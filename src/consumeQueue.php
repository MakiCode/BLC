<?php

namespace BLC;

use BLC\Model\WorkQueue;
use BLC\Config\Data;
use GuzzleHttp\Client;
use BLC\Model\NumericString;
use GuzzleHttp\Promise\Promise;
use InvalidArgumentException;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use BLC\Model\Investment;
use BLC\Model\Investments;
use Types\Integer;
use Types\Numeric;

/**
 * @param WorkQueue $queue
 * @param Data $data
 * @param \GuzzleHttp\Client $client
 * @param Logger $logger
 * @return \Closure
 * @throws \Exception
 */
function consumeQueue(WorkQueue $queue, Data $data, Client $client, Logger $logger)
{
    $dummy = new Promise();
    $promises = [$dummy];
    while ($queue->hasNext()) {
        $workItem = $queue->dequeue();
        $logger->addInfo("Dequeued workitem.", ["workItem" => $workItem]);
        if (!$data->haveInvestedId($workItem->getLoanID())) {
            $rate = $workItem->getRate()->has() ? $workItem->getRate()->get() : null;
            $maxRate = $workItem->getMaxRate()->has() ? $workItem->getMaxRate()->get() : null;
            $logger->addNotice("Creating investment", ["rate" => (is_null($rate) ? "null" : $rate), "maxRate" => (is_null($maxRate) ? "null" : $maxRate)]);
            $promises[] = createInvestment($workItem->getLoanID(), $workItem->getAmount(), $client, $data, $logger, $rate, $maxRate);
        } else {
            $investment = $data->getInvestmentId($workItem->getLoanID());
            if ($workItem->getMaxRate()->has()) {
                $logger->addNotice("Balancing investment", ["investment" => $investment]);
                $promises[] = balanceInvestment($investment, $workItem->getMaxRate()->get(), $client, $logger);
            } else {
                $logger->addError("Work item did not have a max rate, but it should always have a max rate for this path" .
                    " because config automatically adds it", ['workItem' => $workItem, 'investment' => $investment]);
            }
        }
    }

    $allPromise = \GuzzleHttp\Promise\all($promises);
    $dummy->resolve("Nothing here!");
    $allPromise->wait();
}

/**
 * @param int|Numeric $loanId
 * @param NumericString $amount
 * @param Client $guzzle
 * @param Data $data
 * @param Logger $logger
 * @param NumericString $rate
 * @param NumericString $maxRate
 * @return \GuzzleHttp\Promise\PromiseInterface
 */
function createInvestment(Numeric $loanId, NumericString $amount, Client $guzzle, Data $data, Logger $logger, NumericString $rate = null, NumericString $maxRate = null)
{
    if (is_null($rate)) {
        $logger->addInfo("No rate information given, calculating rate based on other investments from loan", ["loan" => $loanId]);
        $rate = new NumericString(getAverageRate($loanId, $guzzle, $logger)->wait(true));
        $logger->addInfo("finished calculating new rate.", ["rate" => $rate]);
    }
    if (!is_null($maxRate)) {
        if (bccomp($rate->get(), $maxRate->get()) == 1) {
            $logger->addInfo("Rate was found to be higher than max rate, lowering it.", ["rate" => $rate, "maxRate" => $maxRate]);
            $rate = $maxRate;
        }
    }
    return $guzzle->postAsync("/api/investment", [
        "form_params" => ["loan_id" => $loanId->get(), "amount" => $amount->get(), "rate" => $rate->get()]
    ])->then(function (ResponseInterface $response) use ($data, $guzzle, $logger, $loanId) {
        $result = new JSON($response->getBody()->getContents());
        $result = $result->getJSON();

        $postResult = checkPostResponse($result, $logger, $response);

        if ($postResult == RESPONSE_OK) {
            $logger->addInfo("starting to query for investment details", ["investment" => $result]);
            $getResponse = $guzzle->get("api/investment/" . $result->id);
            $logger->addInfo("finished getting the results", ['response' => $response]);
            $json = new JSON($getResponse->getBody()->getContents());
            $json = $json->getJSON();

            validateInvestmentsResponse($json, $logger, $getResponse);

            $investment = convertToInvestment($json->investments[0]);

            $data->didInvest($investment);
        } else if ($postResult == RESPONSE_ALREADY_INVESTED) {
            $logger->addNotice("Already invested in this loan, but it wasn't in data. Ignoring it.", ["loanId" => $loanId]);
        }
    }, function (RequestException $e) use ($logger) {
        $logger->addWarning("Post failed to create investment for loan", ["exception" => $e]);
        throw $e;
    })->then(null, function ($e) use ($logger) {
        $logger->addWarning("Failed to get investment data, newly created investment was not saved", ["exception => $e"]);
        throw $e;
    });
}

/**
 * @param Investment $investment
 * @param NumericString $maxRate
 * @param Client $guzzle
 * @param Data $data
 * @param Logger $logger
 * @return \GuzzleHttp\Promise\PromiseInterface
 */
function balanceInvestment(Investment $investment, NumericString $maxRate, Client $guzzle, Logger $logger)
{
    try {
        $rate = getAverageRate($investment->getLoanId(), $guzzle, $logger)->wait(true);
        if (bccomp($rate, $maxRate->get()) === 1) {
            $logger->addInfo("Rate greater than max rate, lowering", ["maxRate" => $maxRate, "rate" => $rate]);
            $rate = $maxRate->get();
        }
        /** @noinspection PhpUndefinedMethodInspection */
        return $guzzle->putAsync("api/investment/" . $investment->getId()->get(), [
                "form_params" => [
                    "amount" => $investment->getAmount()->get(),
                    "rate" => $rate,
                    "loan_id" => $investment->getLoanId()->get()
                ]
            ]
        )->then(null, function ($e) use ($logger) {
            $logger->addWarning("failed to update investment", ["exception" => $e]);
            throw $e;
        });
    } catch (InvalidArgumentException $e) {
        $logger->addWarning("getting average rate failed for this investment, throwing exception....");
        throw $e;
    }
}

/**
 * @param $loanID
 * @param Client $guzzle
 * @param Logger $logger
 * @return \GuzzleHttp\Promise\PromiseInterface
 */
function getAverageRate(Numeric $loanID, Client $guzzle, Logger $logger)
{
    return $guzzle->getAsync("/api/investments/" . $loanID->get())
        ->then(
            function (ResponseInterface $response) use ($logger) {
                $json = new JSON($response->getBody()->getContents());
                $json = $json->getJSON();
                validateInvestmentsResponse($json, $logger, $response);
                $result = array_map(function ($input) {
                    return convertToInvestment($input);
                }, $json->investments);
                $investments = new Investments(...$result);
                return weightedAverageRate($investments);
            },
            function () use ($logger, $loanID) {
                $logger->addWarning("Failed to get investments for loan ID, not calculating average", ["loanID" => $loanID]);
                throw new InvalidArgumentException("Request failed!");
            }
        );
}