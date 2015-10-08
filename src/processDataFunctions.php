<?php

namespace BLC;

use BLC\Config\Config;
use BLC\Config\Data;
use BLC\Model\IntegerList;
use BLC\Model\Loan;
use BLC\Model\Loans;
use BLC\Model\NumericString;
use BLC\Model\WorkItem;
use BLC\Model\WorkQueue;
use BLC\Model\Int_Numeric;
use Exception;
use GuzzleHttp\Client;
use InvalidArgumentException;
use BLC\Model\LoanFactory;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Closure;
use BLC\JSON;
use Types\Boolean;
use Types\Integer;
use Types\Numeric;
use Types\String;
use function \BLC\convertOptionalDate;

/**
 * @param Data $data
 * @param Config $config
 * @param WorkQueue $queue
 * @param Logger $logger
 * @return Closure
 */
function processBorrowerRequest(Data $data, Config $config, WorkQueue $queue, Logger $logger)
{
    return function (ResponseInterface $response) use ($data, $config, $queue, $logger) {
        $borrowersMap = $config->getBorrowersMap();
        $result = getValidLoans($response, $data, $logger);
        $result = findMatchingBorrowers($result, $config->getBorrowersList());
        $result = array_filter($result, function (Loan $loan) {
            return $loan->getStatus()->get() == "Funding";
        });
        $result = array_map(function (Loan $loan) use ($queue, $config, $borrowersMap, $logger) {
            $queue->enqueue(new WorkItem($loan->getId(), new NumericString($borrowersMap[$loan->getBorrower()->get()])));
        }, $result);
    };
}


/**
 * @param ResponseInterface $response
 * @param Data $data
 * @param Logger $logger
 * @return Loans
 */
function getValidLoans(ResponseInterface $response, Data $data, Logger $logger)
{
    try {
        $result = makeLoans(new JSON($response->getBody()->getContents()));
        $relevantData = extractRelevant($result);
        if(haveChecked($relevantData, $data)) {
            return new Loans();
        }
        return $result;
    } catch (Exception $e) {
        $logger->addWarning("Failed to make valid loans, abandoning this enterprise and returning an empty list");
        return new Loans();
    }

}


/**
 * Compares the extracted loan list to the previously cached value. Also updates cache
 * @param Int_Numeric[] $loansList
 * @param $data
 * @return bool
 */
function haveChecked(array $loansList, Data $data)
{
    $newSha1 = sha1(json_encode($loansList));
    $return = $newSha1 == $data->getLastBorrowerSHA1();
    $data->setLastBorrowerSHA1(new String($newSha1));
    return $return;
}

/**
 * Extracts the relevant data from the loans array and converts it to a an array of int tuples
 * @param Loans $loans
 * @return Int_Numeric[] With the borrower first and the loan ID second
 */
function extractRelevant(Loans $loans)
{
    return array_map(
        function (Loan $loan) {
            return new Int_Numeric($loan->getBorrower(), $loan->getId());
        }, $loans->getArray()
    );

}

/**
 * @param Loans $loans
 * @param IntegerList $borrowers
 * @return Loan[]
 */
function findMatchingBorrowers(Loans $loans, IntegerList $borrowers)
{
    $result = $loans->getArray();
    $result = array_filter($result,
        function (Loan $loan) use ($borrowers) {
            return in_array($loan->getBorrower()->get(), $borrowers->getArray());
        }
    );
    $result = array_values($result);
    return $result;
}

/**
 * @param JSON $response
 * @return Loans
 */
function makeLoans(JSON $response)
{
    $json = $response->getJSON();
    if (!isset($json->loans)) {
        throw new InvalidArgumentException("The JSON response doesn't contain the key `loans`");
    }
    $result = array_map(function ($loan) {
        return makeLoan($loan);
    }, $json->loans);
    return new Loans(...$result);
}

/**
 * @param Config $config
 * @param WorkQueue $queue
 * @param Logger $logger
 * @return Closure
 */
function reputationRuleFunction(Config $config, WorkQueue $queue, Logger $logger)
{
    return function (ResponseInterface $response) use ($config, $queue, $logger) {
        $loans = new JSON($response->getBody()->getContents());
        $loans = $loans->getJSON();

        if (!isset($loans->loans)) {
            $logger->addWarning("Response didn't have the key!", ["response" => $response]);
            throw new InvalidArgumentException("Response was invalid");
        } else if (!is_array($loans->loans)) {
            $logger->addWarning("Response didn't have the key loans pointing at a array!", ["response" => $response]);
            throw new InvalidArgumentException("Response was invalid");
        }

        foreach ($loans->loans as $loan) {
            try {
                $loanObj = makeLoan($loan);
                if ($loanObj->getStatus()->get() == "Funding") {
                    $queue->enqueue(new WorkItem($loanObj->getId(), new NumericString($config->getAutoInvestAmount())));
                }
            } catch (\Exception $e) {

                $logger->addWarning("Poorly formed Loan, abandoning", ["loan" => $loan]);
                continue;
            }
        }
    };
}

function buildInvestmentCheckPromises(Config $config, Client $guzzle, WorkQueue $queue, Logger $logger)
{
    return array_map(
        function ($investment) use ($guzzle, $queue, $logger) {
            return $guzzle->getAsync("/api/loan/" . $investment->loanID)
                ->then(
                    function (ResponseInterface $responseObject) use ($queue, $investment, $logger) {
                        $json = new JSON($responseObject->getBody()->getContents());
                        $json = $json->getJSON();
                        if (!isset($json->loans)) {
                            $logger->addWarning("Response didn't have the key!", ["response" => $json]);
                            throw new InvalidArgumentException("Response was invalid");
                        } else if (!is_array($json->loans)) {
                            $logger->addWarning("Response didn't have the key loans pointint at a array!", ["response" => $json]);
                            throw new InvalidArgumentException("Response was invalid");
                        } else if (!isset($json->loans[0])) {
                            $logger->addWarning("Response loans was empty!", ["response" => $json]);
                            throw new InvalidArgumentException("Response was invalid");
                        }
                        try {
                            $loan = makeLoan($json->loans[0]);
                            if ($loan->getStatus()->get() == "Funding") {
                                $queue->enqueue(
                                    new WorkItem(
                                        new Numeric($investment->loanID),
                                        new NumericString($investment->amount),
                                        null,
                                        new NumericString($investment->maxRate)
                                    )
                                );
                            }
                        } catch (Exception $e) {
                            $logger->addWarning("Poorly formed loan, abandoning check.", ["json" => $json]);
                            throw new InvalidArgumentException("Response was invalid");
                        }
                    },
                    function ($e) use ($logger, $investment) {
                        $logger->addWarning("Failed to find whether the loan is closed, aborting investment.", ["exception" => $e, "investment" => $investment]);
                    });
        },
        $config->getManualInvestments()
    );
}