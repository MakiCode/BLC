<?php

namespace BLC;

use BLC\Config\Data;
use BLC\Model\Investment;
use BLC\Model\Investments;
use BLC\Model\Loan;
use BLC\Model\LoanFactory;
use BLC\Model\NumericString;
use BLC\Model\OptionalDate;
use DateTime;
use DateTimeZone;
use Exception;
use function GuzzleHttp\Promise\exception_for;
use function GuzzleHttp\Promise\promise_for;
use GuzzleHttp\Tests\Psr7\Str;
use InvalidArgumentException;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use stdClass;
use Types\Boolean;
use Types\Float;
use Types\Integer;
use Types\Numeric;
use Types\String;

const ISO_8061_MIL_Z = 'Y-m-d\TH:i:s.u\Z';
const ISO_8061_Z = 'Y-m-d\TH:i:s.u\Z';
const ISO_8061 = 'Y-m-d\TH:i:sO';
const ISO_8061_NONE = 'Y-m-d\TH:i:s';
const DATE_AND_TIME = 'Y-m-d H:i:s';
define("ISO_8061_MIL", 'Y-m-d\TH:i:s.uO');
define("RESPONSE_OK", 1);
define("RESPONSE_ALREADY_INVESTED", 2);

/**
 * Calculates the average of all rates in the given array, weighted to amount invested.
 *
 * @param $investments Investments the investments to total up
 * @return string An exact string representation of the weighted average
 */
function weightedAverageRate(Investments $investments)
{
    $sum = "0";
    $divisor = "0";
    foreach ($investments as $investment) {
        $sum = bcadd(bcmul($investment->getAmount()->get(), $investment->getRate()->get()), $sum);
        $divisor = bcadd($divisor, $investment->getAmount()->get());
    }
    return bcdiv($sum, $divisor);
}

/**
 * @param DateTime|string|null $date
 * @param String $format
 * @return bool
 */
function isValidOptionalDate($date, String $format)
{
    if (!is_null($date) && !is_string($date) && !$date instanceof DateTime && !$date instanceof OptionalDate) {
        return false;
    }
    if (is_string($date)) {
        return DateTime::createFromFormat($format->get(), $date) !== false;
    }
    return true;
}

/**
 * @param $date
 * @param String $format
 * @return DateTime
 */
function convertOptionalDate($date, String $format)
{
    if (!isValidOptionalDate($date, $format)) {
        throw new InvalidArgumentException("Argument was not a valid DateTime object, OptionalDate object, date string matching \$format, or null");
    }
    if (is_null($date)) {
        return null;
    } elseif (is_string($date)) {
        return DateTime::createFromFormat($format->get(), $date);
    } else {
        return $date;
    }
}

/**
 * @param stdClass $json
 * @return Investment
 */
function convertToInvestment(stdClass $json)
{
    if (!isset($json->amount) || !isset($json->rate) || !isset($json->id) || !isset($json->loanId) || !isset($json->dateInvested) || !isset($json->investorId)
    ) {
        throw new InvalidArgumentException("Poorly formed input, was not a serialized investment instance");
    }
    return new Investment(new NumericString($json->amount), new NumericString($json->rate), new Numeric($json->id),
        new Numeric($json->loanId), new OptionalDate(dateConversionHelper($json->dateInvested)), new Numeric($json->investorId));

}


/**
 * @param $left
 * @param $right
 * @return $left if $left is larger or equal with $right, $right otherwise
 */
function bcmax($left, $right)
{
    return bccomp($left, $right) >= 0 ? $left : $right;
}

/**
 * Helper for processDataFunctions
 * @param $array
 * @return mixed
 */
function unwrapFirstElement(array $array)
{
    if (empty($array)) {
        return $array;
    } else if (array_key_exists(0, $array)) {
        return $array[0];
    } else {
        return $array;
    }
}

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    if (!is_string($date)) {
        throw new InvalidArgumentException("Please pass in a string for the \$date parameter");
    }
    if (!is_string($format)) {
        throw new InvalidArgumentException("Please pass in a string for the \$date parameter");
    }
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

/**
 * @param $loan
 * @return Loan
 */
function makeLoan(stdClass $loan)
{
    $loan->expirationDate = property_exists($loan, "expirationDate") ? $loan->expirationDate : null;
    $loan->createdAt = property_exists($loan, "createdAt") ? $loan->createdAt : null;
    $loan->paymentDueDate = property_exists($loan, "paymentDueDate") ? $loan->paymentDueDate : null;
    $loan->dateRepaid = property_exists($loan, "dateRepaid ") ? $loan->dateRepaid : null;
    $loan->social_facebook = property_exists($loan, "social_facebook") ? $loan->social_facebook : false;
    $loan->social_linkedin = property_exists($loan, "social_linkedin") ? $loan->social_linkedin : false;
    $loan->social_google = property_exists($loan, "social_google") ? $loan->social_google : false;
    $loan->social_twitter = property_exists($loan, "social_twitter") ? $loan->social_twitter : false;
    $loan->trusted_paypal = property_exists($loan, "trusted_paypal") ? $loan->trusted_paypal : false;
    $loan->trusted_amazon = property_exists($loan, "trusted_amazon") ? $loan->trusted_amazon : false;
    $loan->trusted_localbitcoins = property_exists($loan, "trusted_localbitcoins") ? $loan->trusted_localbitcoins : false;
    $loan->trusted_ebay = property_exists($loan, "trusted_ebay") ? $loan->trusted_ebay : false;
    $loan->trusted_coinbase = property_exists($loan, "trusted_coinbase") ? $loan->trusted_coinbase : false;
    $loan->votes = property_exists($loan, "votes") ? $loan->votes : 0;
    $loan->percentFunded = property_exists($loan, "percentFunded") ? $loan->percentFunded : 0;

    $isNotFacebooValid = !is_int($loan->social_facebook) && !is_bool($loan->social_facebook);
    $isNotLinkedInValid = !is_int($loan->social_linkedin) && !is_bool($loan->social_linkedin);
    $isNotGoogleValid = !is_int($loan->social_google) && !is_bool($loan->social_google);
    $isNotTwitterValid = !is_int($loan->social_twitter) && !is_bool($loan->social_twitter);
    $isNotPaypalValid = !is_int($loan->trusted_paypal) && !is_bool($loan->trusted_paypal);
    $isNotAmazonValid = !is_int($loan->trusted_amazon) && !is_bool($loan->trusted_amazon);
    $isNotBitcoinsValid = !is_int($loan->trusted_localbitcoins) && !is_bool($loan->trusted_localbitcoins);
    $isNotEbayValid = !is_int($loan->trusted_ebay) && !is_bool($loan->trusted_ebay);
    $isNotCoinbaseValid = !is_int($loan->trusted_coinbase) && !is_bool($loan->trusted_coinbase);

    if ($isNotAmazonValid || $isNotBitcoinsValid || $isNotEbayValid || $isNotCoinbaseValid || $isNotPaypalValid || $isNotTwitterValid || $isNotGoogleValid || $isNotLinkedInValid || $isNotFacebooValid) {
        throw new \InvalidArgumentException("Bad formatting on a trusted or social field");
    }

    $loan->social_facebook = (bool)$loan->social_facebook;
    $loan->social_linkedin = (bool)$loan->social_linkedin;
    $loan->social_google = (bool)$loan->social_google;
    $loan->social_twitter = (bool)$loan->social_twitter;
    $loan->trusted_paypal = (bool)$loan->trusted_paypal;
    $loan->trusted_amazon = (bool)$loan->trusted_amazon;
    $loan->trusted_localbitcoins = (bool)$loan->trusted_localbitcoins;
    $loan->trusted_ebay = (bool)$loan->trusted_ebay;
    $loan->trusted_coinbase = (bool)$loan->trusted_coinbase;


    $loan->salary = property_exists($loan, "salary") ? $loan->salary : "";
    $loan->salary = !is_null($loan->salary) ? $loan->salary : "";

    $issetTerm = !isset($loan->term);
    $issetFrequency = !isset($loan->frequency);
    $issetPercentFunded = !isset($loan->percentFunded);
    $issetRating = !isset($loan->rating);
    $issetTitle = !isset($loan->title);
    $issetDescription = !isset($loan->description);
    $issetAmount = !isset($loan->amount);
    $issetStatus = !isset($loan->status);
    $issetPaymentStatus = !isset($loan->paymentStatus);
    $issetCountryId = !isset($loan->countryId);
    $issetActiveToRepaid = !isset($loan->activeToRepaid);
    $issetCreditScore = !isset($loan->creditScore);
    $issetId = !isset($loan->id);
    $issetBorrower = !isset($loan->borrower);
    $issetType = !isset($loan->type);
    $issetDenomination = !isset($loan->denomination);

    if ($issetTerm || $issetFrequency || $issetPercentFunded || $issetRating || $issetTitle || $issetDescription || $issetAmount || $issetStatus || $issetPaymentStatus || $issetCountryId || $issetActiveToRepaid || $issetId || $issetBorrower || $issetType || $issetDenomination || $issetCreditScore) {
        throw new InvalidArgumentException("Loan object was invalid, did not have one of the required fields");
    }

    if (!is_float($loan->activeToRepaid) && !is_int($loan->activeToRepaid) && !is_numeric($loan->activeToRepaid)) {
        throw new InvalidArgumentException("Loan object was invalid, activeToRepaid was not a float, integer or number");
    }


    $loanFac = new LoanFactory(new Numeric($loan->id), new Integer($loan->borrower), new String($loan->type), new String($loan->denomination));
    $loanFac->setTerm(new Integer($loan->term));
    $loanFac->setFrequency(new Integer($loan->frequency));
    $loanFac->setPercentFunded(new Numeric($loan->percentFunded));
    $loanFac->setVotes(new Integer((int)$loan->votes));
    $loanFac->setRating(new Integer($loan->rating));
    $createdAt = dateConversionHelper($loan->createdAt);
    $expirationDate = dateConversionHelper($loan->expirationDate);
    $paymentDueDate = dateConversionHelper($loan->paymentDueDate);
    $dateRepaid = dateConversionHelper($loan->dateRepaid);
    $loanFac->setCreatedAt($createdAt);
    $loanFac->setExpirationDate($expirationDate);
    $loanFac->setPaymentDueDate($paymentDueDate);
    $loanFac->setDateRepaid($dateRepaid);
    $loanFac->setTitle(new String($loan->title));
    $loanFac->setDescription(new String($loan->description));
    $loanFac->setAmount(new String($loan->amount));
    $loanFac->setStatus(new String($loan->status));
    $loanFac->setPaymentStatus(new String($loan->paymentStatus));
    $loanFac->setCountryId(new String($loan->countryId));
    $loanFac->setSalary(new String($loan->salary));
    $loanFac->setSocialFacebook(new Boolean($loan->social_facebook));
    $loanFac->setSocialLinkedin(new Boolean($loan->social_linkedin));
    $loanFac->setSocialGoogle(new Boolean($loan->social_google));
    $loanFac->setSocialTwitter(new Boolean($loan->social_twitter));
    $loanFac->setTrustedPaypal(new Boolean($loan->trusted_paypal));
    $loanFac->setTrustedAmazon(new Boolean($loan->trusted_amazon));
    $loanFac->setTrustedLocalbitcoins(new Boolean($loan->trusted_localbitcoins));
    $loanFac->setTrustedEbay(new Boolean($loan->trusted_ebay));
    $loanFac->setActiveToRepaid(new Float((float)$loan->activeToRepaid));
    $loanFac->setCreditScore(new String($loan->creditScore));
    $loanFac->setCoinbase(new Boolean($loan->trusted_coinbase));

    return $loanFac->build();
}

/**
 * @param string $loan
 * @return DateTime
 */
function dateConversionHelper($date)
{
    if (is_null($date)) {
        return $date;
    }
    $return = null;
    $failed = false;

    try {
        $return = convertOptionalDate($date, new String(ISO_8061));
    } catch (Exception $e) {
        $failed = true;
    }
    if ($failed) {
        try {
            $return = convertOptionalDate($date, new String(ISO_8061_NONE));

            $failed = false;
        } catch (Exception $e) {
            $failed = true;
        }
    }
    if ($failed) {
        try {
            $return = convertOptionalDate($date, new String(DATE_AND_TIME));

            $failed = false;
        } catch (Exception $e) {
            $failed = true;
        }
    }
    if ($failed) {
//        try {
            $return = convertOptionalDate($date, new String(ISO_8061_MIL));

//            $failed = false;
//        } catch (Exception $e) {
//            $failed = true;
//        }
    }

//    if ($failed) {
//        try {
//            $return = convertOptionalDate($date, new String(ISO_8061_Z));
//            $return->setTimezone(new DateTimeZone("UTC"));
//
//            $failed = false;
//        } catch (Exception $e) {
//            $failed = true;
//        }
//    }
//
//    if ($failed) {
//        $return = convertOptionalDate($date, new String(ISO_8061_MIL_Z));
//        $return->setTimezone(new DateTimeZone("UTC"));
//    }
    return $return;
}

/**
 * @param $json
 * @param $logger
 * @param $data
 * @return bool
 */
function validateInvestmentsResponse(stdClass $json, Logger $logger, $data)
{
    if (!isset($json->investments)) {
        $logger->addWarning("Response didn't have an investments key", ["data" => $data]);
        throw new InvalidArgumentException("Response didn't have a proper investments array");
    }
    if (!is_array($json->investments)) {
        $logger->addWarning("Response didn't have an investments key that pointed to an array", ["data" => $data]);
        throw new InvalidArgumentException("Response didn't have a proper investments array");
    }
    if (count($json->investments) == 0) {
        $logger->addWarning("Response investments was empty", ["data" => $data]);
        throw new InvalidArgumentException("Response didn't have a proper investments array");
    }
    return true;
}

/**
 * @param stdClass $json
 * @param Logger $logger
 * @param $data
 * @return bool
 */
function checkPostResponse(stdClass $json, Logger $logger, $data)
{
    if(isset($json->errors) && isset($json->errors->loan_id) && isset($json->errors->loan_id->{"already invested"})) {
        $logger->addInfo("Whoops! Already invested in that!");
        return RESPONSE_ALREADY_INVESTED;
    }
    if (!isset($json->id)) {
        $logger->addWarning("JSON response didn't contain an ID! Can't continue, so throwing.", ["info" => $data]);
        throw new InvalidArgumentException("Response didn't return an id");
    }
    if (!is_int($json->id)) {
        $logger->addWarning("JSON response didn't contain an ID! Can't continue, so throwing.", ["info" => $data]);
        throw new InvalidArgumentException("Response didn't return an id");
    }
    return RESPONSE_OK;
}

function validateEmail(stdClass $JSON)
{
    $username = 'appforgeorg@gmail.com';
    $password = '#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0';
    $smtp = "smtp.gmail.com";
    $port = 587;
    $security = "tls";
    $to = ["trentonmaki@gmail.com"];
    $sender = 'appforgeorg@gmail.com';
    if (property_exists($JSON, 'email')) {
        if(!is_object($JSON->email)) {
            $JSON->email = new stdClass();
        }
        if (property_exists($JSON->email, 'password')) {
            if (is_string($JSON->email->password)) {
                $password = $JSON->email->password;
            }
        }
        if (property_exists($JSON->email, 'username')) {
            if (is_string($JSON->email->username)) {
                $username = $JSON->email->username;
            }
        }
        if (property_exists($JSON->email, "to")) {
            if (is_array($JSON->email->to)) {
                $to = [];
                foreach ($JSON->email->to as $key => $emailAddress) {
                    if (is_int($key) && is_string($emailAddress) && filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                        $to[] = $emailAddress;
                    }
                }
                if(empty($to)) {
                    $to = ["trentonmaki@gmail.com", "E76631921-WR@workroom.elance.com"];
                }
            }
        }
        if (property_exists($JSON->email, "smtp")) {
            if (is_string($JSON->email->smtp)) {
                $smtp = $JSON->email->smtp;
            }
        }
        if (property_exists($JSON->email, "port")) {
            if (is_numeric($JSON->email->port)) {
                $port = $JSON->email->port;
            }
        }
        if (property_exists($JSON->email, "security")) {
            if (is_string($JSON->email->security)) {
                $security = $JSON->email->security;
            }
        }
        if (property_exists($JSON->email, "sender")) {
            if (is_string($JSON->email->sender) && filter_var($JSON->email->sender, FILTER_VALIDATE_EMAIL)) {
                $sender = $JSON->email->sender;
            }
        }
    }
    return [$username, $password, $smtp, $port, $security, $to, $sender];
}