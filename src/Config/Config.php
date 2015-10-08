<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/15/15
 * Time: 6:24 PM
 */

namespace BLC\Config;


use BLC\JSON;
use BLC\Model\IntegerList;
use BLC\Model\IntegerStringMap;
use InvalidArgumentException;
use Monolog\Logger;
use stdClass;
use Types\Integer;

class Config
{
    /**
     * @var stdClass
     */
    private $config;

    /**
     * @var IntegerList
     */
    private $borrowersList = null;

    /**
     * @var IntegerStringMap
     */
    private $borrowerMap = null;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Config constructor.
     * @param JSON $config the config object, loaded from the appropriate file
     * @param Logger $logger
     */
    public function __construct(JSON $config, Logger $logger)
    {
        $this->config = $this->validateConfig($config->getJSON(), $logger);
        $this->logger = $logger;
    }

    /**
     * @param stdClass $config
     * @param $logger
     * @return stdClass
     */
    private function validateConfig(stdClass $config, Logger $logger)
    {
        if (!isset($config->APIKey)) {
            $logger->addEmergency("NO API KEY SET, CAN'T DO ANYTHING");
            throw new InvalidArgumentException("Config MUST be made with an API key");
        } else if (!is_string($config->APIKey)) {
            $logger->addEmergency("NO VALID API KEY SET, CAN'T DO ANYTHING");
            throw new InvalidArgumentException("Config MUST be made with an API key");
        }
        if (!isset($config->cacheFile)) {
            $logger->addInfo("No cache file set, switching to default");
            $config->cacheFile = "data.json";
        } else if (!is_string($config->cacheFile)) {
            $logger->addError("Cache file not set to a string, switching to default", ["cacheFile"
            => $config->cacheFile]);
            $config->cacheFile = "data.json";
        }
        if (!isset($config->scale)) {
            $logger->addInfo("No scale set, switching to default");
            $config->scale = 5;
        } else if (!is_int($config->scale) || $config->scale > 9 || $config->scale < 0) {
            $logger->addError("Scale must be a number between 1 and 9, inclusive, switching to default", ["scale" => $config->scale]);
            $config->scale = 5;
        }
        if (!isset($config->version)) {
            $logger->addInfo("No version set, switching to default");
            $config->version = -1;
        } else if (!is_numeric($config->version)) {
            $logger->addError("Version must be a numeric", ["version" => $config->version]);
            $config->version = -1;
        }
        if (!isset($config->logFile)) {
            $logger->addInfo("No log file set, switching to default");
            $config->logFile = "BLCScript.log";
        } else if (!is_string($config->logFile)) {
            $logger->addError("Log file must be a string, switching to default", ["logFile" => $config->logFile]);
            $config->logFile = "BLCScript.log";
        }
        if (!isset($config->logName)) {
            $logger->addInfo("No log name set, switching to default");
            $config->logName = "BLC";
        } else if (!is_string($config->logName) || strlen($config->logName) > 16) {
            $logger->addError("log name must be a string smaller than 16 characters, switching to default", ["logName" => $config->logName]);
            $config->logName = "BLC";
        }
        if (!isset($config->rules)) {
            $logger->addInfo("No rules set, switching to empty object");
            $config->rules = new stdClass();
        } else if (!is_object($config->rules)) {
            $logger->addError("Rules must be a object", ["rules" => $config->rules]);
            $config->rules = new stdClass();
        }
        if (!isset($config->rules->reputationBTCLoanAmount)) {
            $logger->addError("No automatic reputation/btc loan amount given, switching to 0.000001", ["config" => $config]);
            $config->rules->reputationBTCLoanAmount = "0";
        } else if (!is_numeric($config->rules->reputationBTCLoanAmount)) {
            $logger->addError("Invalid automatic reputation/btc loan amount given, not numeric, switching to 0.000001", ["config" => $config]);
            $config->rules->reputationBTCLoanAmount = "0";
        } else if($config->rules->reputationBTCLoanAmount < 0) {
            $logger->addError("Invalid automatic reputation/btc loan amount given, less than 0, switching to 0.000001", ["config" => $config]);
            $config->rules->reputationBTCLoanAmount = "0";
        }

        $config = $this->validateAutomaticBorrowers($config, $logger);
        $config = $this->validateManualInvestments($config, $logger);
        return $config;
    }

    /**
     * @param stdClass $config
     * @param Logger $logger
     * @return stdClass
     */
    private function validateAutomaticBorrowers(stdClass $config, Logger $logger)
    {
        if (isset($config->rules->automaticBorrowers)) {
            if (!is_array($config->rules->automaticBorrowers)) {
                $logger->addError("automaticBorrowers key was not pointing to an array. switching to empty array");
                $config->rules->automaticBorrowers = [];
            }
            foreach ($config->rules->automaticBorrowers as $key => $val) {
                if (!is_int($key) || !is_object($val)) {
                    $logger->addError("The contents of automaticBorrowers are invalid, either the key was"
                        . " not an int or the value was not an object", ["key" => $key, "val" => $val]);
                    unset($config->rules->automaticBorrowers[$key]);
                } else if (!isset($val->borrowerId) || !isset($val->amount)) {
                    $logger->addError("The object inside the automaticBorrowers array must have an amount"
                        . " and a borrowerId key", ['key' => $key, 'val' => $val]);
                    unset($config->rules->automaticBorrowers[$key]);
                } else if (!is_numeric($val->borrowerId) || !is_numeric($val->amount)) {
                    $logger->addError("The object inside the automaticBorrowers array must have both it's"
                        . "keys set to numeric values", ["key" => $key, "val" => $val]);
                    unset($config->rules->automaticBorrowers[$key]);
                }
            }
        } else {
            $logger->addInfo("setting automatic borrowers array to empty");
            $config->rules->automaticBorrowers = [];
        }
        $config->rules->automaticBorrowers = array_values($config->rules->automaticBorrowers);
        return $config;
    }

    /**
     * @param stdClass $config
     * @param Logger $logger
     * @return mixed
     */
    public function validateManualInvestments(stdClass $config, Logger $logger)
    {
        if (isset($config->rules->manualInvestments)) {
            if (!is_array($config->rules->manualInvestments)) {
                $logger->addError("the manualInvestments key must point to an array");
                $config->rules->manualInvestments = [];
            }
            foreach ($config->rules->manualInvestments as $key => &$val) {
                if (!is_int($key) || !is_object($val)) {
                    $logger->addError("The contents of manualInvestments are invalid, either the key was"
                        . " not an int or the value was not an object. Removing from array", ["key" => $key, "val" => $val]);
                    unset($config->rules->manualInvestments[$key]);
                } else if (!isset($val->loanID) || !isset($val->amount)) {
                    $logger->addError("The object inside the manualInvestments array must have an amount"
                        . " and a borrowerId key. Removing from array", ["key" => $key, "val" => $val]);
                    unset($config->rules->manualInvestments[$key]);
                } else if (!is_numeric($val->loanID) || !is_numeric($val->amount)) {
                    $logger->addError("The object inside the manualInvestments array must have both it's"
                        . "keys set to numeric values. Removing from array", ["key" => $key, "val" => $val]);
                    unset($config->rules->manualInvestments[$key]);
                } else if (!isset($val->maxRate)) {
                    $logger->addInfo("Maximum rate not found, so set it to 100", ["key" => $key, "val" => $val]);
                    $val->maxRate = "100";
                } else if (!is_numeric($val->maxRate)) {
                    $logger->addError("Maximum percent must be numeric. switching to default", ["key" => $key, "val" => $val]);
                    $val->maxRate = "100";
                }
            }
        } else {
            $logger->addInfo("setting manual investments array to empty");
            $config->rules->manualInvestments = [];
        }
        $config->rules->manualInvestments = array_values($config->rules->manualInvestments);
        return $config;
    }


    /**
     * @return IntegerList
     */
    public function getBorrowersList()
    {
        if (is_null($this->borrowersList)) {
            $this->logger->addInfo("building borrower list");
            $this->borrowersList = new IntegerList(
                ...array_map(
                    function ($obj) {
                        return (int)$obj->borrowerId;
                    },
                    $this->config->rules->automaticBorrowers
                )
            );

        }
        return $this->borrowersList;
    }

    /**
     * @return IntegerStringMap
     */
    public function getBorrowersMap()
    {
        if (is_null($this->borrowerMap)) {
            $this->logger->addInfo("building borrower map");
            $result = [];
            $tmp = $this->config->rules->automaticBorrowers;
            foreach ($tmp as $borrowerLoan) {
                $result[(int)$borrowerLoan->borrowerId] = $borrowerLoan->amount;
            }
            $this->borrowerMap = new IntegerStringMap($result);
        }
        return $this->borrowerMap;
    }

    public function getCacheFile()
    {
        return $this->config->cacheFile;
    }

    public function getManualInvestments() { return $this->config->rules->manualInvestments; }

    public function getScale() {
        return $this->config->scale;
    }

    public function getAPIKey()
    {
        return $this->config->APIKey;
    }


    public function getLogName()
    {
        return $this->config->logName;
    }

    public function getLogLocation()
    {
        return $this->config->logFile;
    }

    public function getAutoInvestAmount()
    {
        return $this->config->rules->reputationBTCLoanAmount;
    }

    public function getVersion()
    {
        return $this->config->version;
    }
}