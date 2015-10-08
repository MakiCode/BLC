<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/15/15
 * Time: 4:15 PM
 */

namespace BLC\Config;


use function BLC\convertToInvestment;
use BLC\JSON;
use BLC\Model\Investment;
use BLC\Model\Loan;
use DateTime;
use JsonSerializable;
use Monolog\Logger;
use stdClass;
use Types\Numeric;
use Types\String;

class Data implements JsonSerializable
{
    private $data;

    public function __construct(JSON $data, Logger $logger)
    {
        $this->data = $data->getJSON();
        $this->data = $this->normalizeData($this->data, $logger);
    }

    private function normalizeData($data, Logger $logger)
    {
        if (!is_object($data)) {
            $logger->addInfo("No data existed, creating default blank object");
            $data = new \stdClass();
        }
        if (!isset($data->lastBorrower)) {
            $logger->addInfo("No last borrower, setting to default");
            $data->lastBorrower = "";
        } else if (!is_string($data->lastBorrower)) {
            $logger->addInfo("No last borrower poorly formatted, setting to default");
            $data->lastBorrower = "";
        }
        if (!isset($data->cache)) {
            $logger->addInfo("No cache found, switching to empty");
            $data->cache = new stdClass();
        } else if (!is_object($data->cache)) {
            $logger->addInfo("Cache badly formatted, switching to empty");
            $data->cache = new stdClass();
        }
        $data = $this->validateCache($data, $logger);
        return $data;
    }

    public function getLastBorrowerSHA1()
    {
        return $this->data->lastBorrower;
    }

    public function setLastBorrowerSHA1(String $newSha1)
    {
        $this->data->lastBorrower = $newSha1->get();
    }

    /**
     * Returns whether we have already invested in this or not, according to the data file
     * @param $loanID
     * @return bool
     */
    public function haveInvested(Loan $loanID)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return property_exists($this->data->cache, $loanID->getId()->get());

    }

    /**
     * Returns whether we have already invested in this or not, according to the data file
     * @param Integer $loanID
     * @return bool
     */
    public function haveInvestedId(Numeric $loanID)
    {
        return property_exists($this->data->cache, (int)$loanID->get());
    }


    public function didInvest(Investment $investment)
    {
        $class = new \stdClass();
        $now = new \DateTime("now");
        $class->investment = $investment->jsonSerialize();
        $class->dateModified = $now->format("d/m/Y");
        $this->data->cache->{(string)$investment->getLoanId()->get()} = $class;

    }

    /**
     * @param Loan $loan
     * @return Investment
     */
    public function getInvestment(Loan $loan)
    {
        return $this->getInvestmentId($loan->getId());
    }

    /**
     * @param Integer $loanId
     * @return Investment
     */
    public function getInvestmentId(Numeric $loanId)
    {
        $key = (string)$loanId->get();
        if(!property_exists($this->data->cache, $key)) {
            throw new \InvalidArgumentException("InvestmentID must exist in the cache");
        }
        return convertToInvestment($this->data->cache->$key->investment);
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        return $this->data;
    }

    public function removeInvestment(Investment $investment)
    {
        $key = (string)$investment->getLoanId()->get();
        unset($this->data->cache->$key);
    }

    /**
     * @param $data
     * @param Logger $logger
     * @param $cacheItem
     * @param $key
     * @return bool
     */
    private function validateInvestment(&$data, Logger $logger, $cacheItem, $key)
    {
        $issetAmount = property_exists($cacheItem->investment, "amount");
        $issetRate = property_exists($cacheItem->investment, "rate");
        $issetID = property_exists($cacheItem->investment, "id");
        $issetLoanId = property_exists($cacheItem->investment, "loanId");
        $issetDateInvested = property_exists($cacheItem->investment, "dateInvested");
        $issetInvestorID = property_exists($cacheItem->investment, "investorId");
        if ($issetAmount &&
            $issetRate &&
            $issetID &&
            $issetLoanId &&
            $issetDateInvested &&
            $issetInvestorID
        ) {

            $isAmountValid = is_numeric($cacheItem->investment->amount);
            $isRateValid = is_numeric($cacheItem->investment->rate);
            $isIDValid = is_numeric($cacheItem->investment->id);
            $isLoanIDValid = is_numeric($cacheItem->investment->loanId);
            $isDateValid = is_null($cacheItem->investment->dateInvested) || \BLC\validateDate($cacheItem->investment->dateInvested, DATE_ISO8601);
            $isInvestorIDValid = is_numeric($cacheItem->investment->investorId);

            if (!$isAmountValid || !$isRateValid ||
                !$isIDValid || !$isLoanIDValid || !$isDateValid || !$isInvestorIDValid
            ) {
                $logger->addInfo("Cached investment is poorly formed! Obliterating it....", ["key" => $key, "val" => $cacheItem]);
                unset($data->cache->$key);
                return false;
            }
        } else {
            $logger->addInfo("Cached investment is missing keys! Obliterating it....", ["key" => $key, "val" => $cacheItem]);
            unset($data->cache->$key);
            return false;
        }

        return true;
    }

    /**
     * @param $data
     * @param Logger $logger
     */
    private function validateCache($data, Logger $logger)
    {
        $now = new \DateTime();
        $cacheArray = get_object_vars($data->cache);
        foreach ($cacheArray as $key => $cacheItem) {
            if (!is_numeric($key) || !is_object($cacheItem)) {
                $logger->addInfo("Cache item is poorly formatted. Obliterating it....", ["key" => $key, "val" => $cacheItem]);
                unset($data->cache->$key);
                continue;
            }
            if (!isset($cacheItem->investment)) {
                $logger->addInfo("Cache item doesn't have an investment! Obliterating it....", ["key" => $key, "val" => $cacheItem]);
                unset($data->cache->$key);
                continue;
            } else {
                if (!$this->validateInvestment($data, $logger, $cacheItem, $key)) {
                    continue;
                }
            }
            if (!isset($cacheItem->dateModified)) {
                $logger->addInfo("Cache item doesn't have a dateModified, setting default value", ["key" => $key, "val" => $cacheItem]);
                $data->cache->$key->dateModified = $now->format("d/m/Y");
            } else if (!is_string($cacheItem->dateModified) || !\BLC\validateDate($cacheItem->dateModified, "d/m/Y")) {
                $logger->addInfo("Cache item doesn't have a valid dateModified, setting default value", ["key" => $key, "val" => $cacheItem]);
                $data->cache->$key->dateModified = $now->format("d/m/Y");
            }
            $dateModified = DateTime::createFromFormat("d/m/Y", $cacheItem->dateModified);
            $diff = $now->diff($dateModified);
            if ($dateModified < $now && (int)$diff->format("%a") > 7) {
                $logger->addInfo("Cache item is more than 7 days old, deleting", ["key" => $key, "val" => $cacheItem, "now" => $now]);
                unset($data->cache->$key);
                continue;
            }
        }
        return $data;
    }
}