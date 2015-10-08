<?php

if(PHP_SAPI != "cli") {
    die();
}

require(__DIR__ . "/vendor/autoload.php");

date_default_timezone_set("UTC");

if(!function_exists('errorHandler')) {
    function errorHandler($severity, $errstr, $errfile, $errline)
    {
        if (error_reporting() == 0) {
            return;
        }
        if (error_reporting() & $severity) {
            throw new ErrorException($errstr, 0, $severity, $errfile, $errline);
        }
    }

    set_error_handler('errorHandler');
}

if (!function_exists("redCLI")) {
    function redCLI($string)
    {
        return "\033[0;31m" . $string . "\033[0m";
    }
}


$file = "/src/data/ruleConfig.json";

if(count($argv) == 2) {
    if(is_string($argv[1])) {
        if(is_file(__DIR__ . "/" . $argv[1])) {
            $file = $argv[1];
        } else {
            echo redCLI("Argument was not a file");
            return;
        }
    } else {
      echo redCLI("How did you pass a non string to this?");
      return;
    }
}

//THIS IS A COPY OF THE CONFIG CLASS, WITH ALL LOGGED ERRORS CHANGED TO EXCEPTIONS
function checkConfig($config)
{
    if (!isset($config->APIKey)) {
        throw new InvalidArgumentException("Config MUST be made with an API key");
    } else if (!is_string($config->APIKey)) {
        throw new InvalidArgumentException("Config MUST be made with a valid API key");
    }
    if (!isset($config->cacheFile)) {
        $config->cacheFile = "data.json";
    } else if (!is_string($config->cacheFile)) {
        throw new InvalidArgumentException("Cache file not set to a string");
    }
    if (!isset($config->scale)) {
        $config->scale = 9;
    } else if (!is_int($config->scale) || $config->scale > 9 || $config->scale < 0) {
        throw new InvalidArgumentException("Scale must be a number between 1 and 5, inclusive");
    }
    if (!isset($config->version)) {
        $config->version = -1;
    } else if (!is_numeric($config->version)) {
        throw new InvalidArgumentException("Version must be a numeric");
    }
    if (!isset($config->logFile)) {
        $config->logFile = "BLCScript.log";
    } else if (!is_string($config->logFile)) {
        throw new InvalidArgumentException("Log file must be a string");
    }
    if (!isset($config->logName)) {
        $config->logName = "BLC";
    } else if (!is_string($config->logName) || strlen($config->logName) > 16) {
        throw new InvalidArgumentException("log name must be a string smaller than 16 characters");
    }
    if (!isset($config->rules)) {
        $config->rules = new stdClass();
    } else if (!is_object($config->rules)) {
        throw new InvalidArgumentException("Rules must be a object");
    }
    if (!isset($config->rules->reputationBTCLoanAmount)) {
        $config->rules->reputationBTCLoanAmount = "0";
    } else if (!is_numeric($config->rules->reputationBTCLoanAmount)) {
        throw new InvalidArgumentException("Invalid automatic reputation/btc loan amount given, non numeric");
    } else if($config->rules->reputationBTCLoanAmount < 0) {
        throw new InvalidArgumentException("Invalid automatic reputation/btc loan amount given, less than 0");
    }

    if (isset($config->rules->automaticBorrowers)) {
        if (!is_array($config->rules->automaticBorrowers)) {
            throw new InvalidArgumentException("automaticBorrowers key was not pointing to an array.");
        }
        foreach ($config->rules->automaticBorrowers as $key => $val) {
            if (!is_int($key) || !is_object($val)) {
                throw new InvalidArgumentException("The contents of automaticBorrowers are invalid, either the key was"
                    . " not an int or the value was not an object (you probably tried to put a non-object in the list)");
            } else if (!isset($val->borrowerId) || !isset($val->amount)) {
                throw new InvalidArgumentException("The objects inside the automaticBorrowers array must have an amount"
                    . " and a borrowerId key");
            } else if (!is_numeric($val->borrowerId) || !is_numeric($val->amount)) {
                throw new InvalidArgumentException("The objects inside the automaticBorrowers array must have both it's"
                    . "keys set to numeric values");
            }
        }
    } else {
        $config->rules->automaticBorrowers = [];
    }
    $config->rules->automaticBorrowers = array_values($config->rules->automaticBorrowers);

    if (isset($config->rules->manualInvestments)) {
        if (!is_array($config->rules->manualInvestments)) {
            throw new InvalidArgumentException("the manualInvestments key must point to an array");
        }
        foreach ($config->rules->manualInvestments as $key => &$val) {
            if (!is_int($key) || !is_object($val)) {
                throw new InvalidArgumentException("The contents of manualInvestments are invalid, either the key was"
                    . " not an int or the value was not an object (you probably tried to put a non-object in the list)");
            } else if (!isset($val->loanID) || !isset($val->amount)) {
                throw new InvalidArgumentException("The objects inside the manualInvestments array must have an amount"
                    . " and a borrowerId key. ");
            } else if (!is_numeric($val->loanID) || !is_numeric($val->amount)) {
                throw new InvalidArgumentException("The objects inside the manualInvestments array must have both it's"
                    . "keys set to numeric values.");
            } else if (!isset($val->maxRate)) {
                $val->maxRate = "100";
            } else if (!is_numeric($val->maxRate)) {
                throw new InvalidArgumentException("Maximum percent must be numeric.");
            }
        }
    } else {
        $config->rules->manualInvestments = [];
    }
    $config->rules->manualInvestments = array_values($config->rules->manualInvestments);
}


function checkEmail($JSON)
{
    if (property_exists($JSON, 'email')) {
        if(!is_object($JSON->email)) {
            throw new InvalidArgumentException("Email was not pointing to an object!");
        }
        if (property_exists($JSON->email, 'password')) {
            if (!is_string($JSON->email->password)) {
                throw new InvalidArgumentException("Password key in email must be a string!");
            }
        }
        if (property_exists($JSON->email, 'username')) {
            if (!is_string($JSON->email->username)) {
                throw new InvalidArgumentException("Username key in email must be a string!");
            }
        }
        if (property_exists($JSON->email, "to")) {
            if (is_array($JSON->email->to)) {
                foreach ($JSON->email->to as $key => $emailAddress) {
                    if (!is_int($key)  || !is_string($emailAddress) || !filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                        throw new InvalidArgumentException("Email address in 'to' was not a string, was not a list, or was not an email address");
                    }
                }
            } else {
                throw new InvalidArgumentException("To field in Email must be an array");
            }
        }
        if (property_exists($JSON->email, "smtp")) {
            if (!is_string($JSON->email->smtp)) {
                throw new InvalidArgumentException("SMTP in Email must be a string.");
            }
        }
        if (property_exists($JSON->email, "port")) {
            if (!is_numeric($JSON->email->port)) {
                throw new InvalidArgumentException("Port in Email must be numeric.");
            }
        }
        if (property_exists($JSON->email, "security")) {
            if (!is_string($JSON->email->security)) {
                throw new InvalidArgumentException("Security in Email must be a string.");
            }
        }
        if (property_exists($JSON->email, "sender")) {
            if (!is_string($JSON->email->sender) || !filter_var($JSON->email->sender, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("Sender in Email must be a string and a valid email address");
            }
        }
    }
}


try {
    $jsonString = file_get_contents(__DIR__ . $file);

    try {
        $jsonObj = new \BLC\JSON($jsonString);
        try {
            $nullHandler = new \Monolog\Handler\NullHandler();

            checkConfig($jsonObj->getJSON());
            checkEmail($jsonObj->getJSON());
            echo "Config file is OK!" . PHP_EOL;
        } catch (Exception $e) {
            echo redCLI("Config file didn't follow the specification: " . $e->getMessage()) . PHP_EOL;
        }
    } catch (Exception $e) {
        echo redCLI("Config file wasn't valid JSON! Use a JSON format checker (like http://www.jsoneditoronline.org) to fix this.") . PHP_EOL;
    }
} catch (Exception $e) {
    echo redCLI("Can't get the contents of config file") . PHP_EOL;
}

