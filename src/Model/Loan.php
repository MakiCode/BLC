<?php
namespace BLC\Model;

use DateTime;
use Types\Boolean;
use Types\Float;
use Types\Integer;
use Types\Numeric;
use Types\String;

/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/17/15
 * Time: 3:44 PM
 */
class Loan
{

    /**
     * @var Integer
     */
    private $id;
    /**
     * @var String
     */
    private $type;
    /**
     * @var String
     */
    private $title;
    /**
     * @var String
     */
    private $description;
    /**
     * Loan amount, number string
     * @var String
     */
    private $amount;

    /**
     * @var Integer
     */
    private $term;

    /**
     * @var Integer
     */
    private $frequency;

    /**
     * ENUM
     * @var String
     */
    private $status;
    /**
     * ENUM
     * @var String
     */
    private $paymentStatus;
    /**
     * @var OptionalDate
     */
    private $createdAt;
    /**
     * @var OptionalDate
     */
    private $expirationDate;
    /**
     * @var OptionalDate
     */
    private $paymentDueDate;
    /**
     * @var OptionalDate
     */
    private $dateRepaid;

    /**
     * ENUM
     * @var String
     */
    private $denomination;

    /**
     * @var Numeric
     */
    private $percentFunded;

    /**
     * @var OptionalInteger
     */
    private $votes;

    /**
     * Borrower ID
     * @var Integer
     */
    private $borrower;

    /**
     * ENUM
     * @var String
     */
    private $countryID;
    /**
     * MONEY NUMBER
     * @var String
     */
    private $salary;

    /**
     * @var Integer
     */
    private $rating;
    /**
     * @var OptionalBoolean
     */
    private $facebook;
    /**
     * @var OptionalBoolean
     */
    private $linkedin;
    /**
     * @var OptionalBoolean
     */
    private $google;
    /**
     * @var OptionalBoolean
     */
    private $twitter;
    /**
     * @var OptionalBoolean
     */
    private $paypal;
    /**
     * @var OptionalBoolean
     */
    private $amazon;
    /**
     * @var OptionalBoolean
     */
    private $localbitcoins;
    /**
     * @var OptionalBoolean
     */
    private $ebay;

    /**
     * @return OptionalBoolean
     */
    public function isCoinbase()
    {
        return $this->coinbase;
    }

    /**
     * @return String
     */
    public function getCreditScore()
    {
        return $this->creditScore;
    }

    /**
     * @return Float
     */
    public function getActiveToRepaid()
    {
        return $this->activeToRepaid;
    }

    /**
     * @var OptionalBoolean
     */
    private $coinbase;

    /**
     * @var String
     */
    private $creditScore;

    /**
     * @var Float
     */
    private $activeToRepaid;

    /**
     * @return Numeric
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return String
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return String
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return String
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return String
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return Integer
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * @return Integer
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @return String
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return String
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * @return OptionalDate
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return OptionalDate
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @return OptionalDate
     */
    public function getPaymentDueDate()
    {
        return $this->paymentDueDate;
    }

    /**
     * @return OptionalDate
     */
    public function getDateRepaid()
    {
        return $this->dateRepaid;
    }

    /**
     * @return String
     */
    public function getDenomination()
    {
        return $this->denomination;
    }

    /**
     * @return Numeric
     */
    public function getPercentFunded()
    {
        return $this->percentFunded;
    }

    /**
     * @return OptionalInteger
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @return Integer
     */
    public function getBorrower()
    {
        return $this->borrower;
    }

    /**
     * @return String
     */
    public function getCountryID()
    {
        return $this->countryID;
    }

    /**
     * @return String
     */
    public function getSalary()
    {
        return $this->salary;
    }

    /**
     * @return Integer
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @return OptionalBoolean
     */
    public function isFacebook()
    {
        return $this->facebook;
    }

    /**
     * @return OptionalBoolean
     */
    public function isLinkedin()
    {
        return $this->linkedin;
    }

    /**
     * @return OptionalBoolean
     */
    public function isGoogle()
    {
        return $this->google;
    }

    /**
     * @return OptionalBoolean
     */
    public function isTwitter()
    {
        return $this->twitter;
    }

    /**
     * @return OptionalBoolean
     */
    public function isPaypal()
    {
        return $this->paypal;
    }

    /**
     * @return OptionalBoolean
     */
    public function isAmazon()
    {
        return $this->amazon;
    }

    /**
     * @return OptionalBoolean
     */
    public function isLocalbitcoins()
    {
        return $this->localbitcoins;
    }

    /**
     * @return OptionalBoolean
     */
    public function isEbay()
    {
        return $this->ebay;
    }

    /**
     * Loan constructor.
     * @param Numeric $id
     * @param String $type
     * @param String $title
     * @param String $description
     * @param String $amount
     * @param Integer $term
     * @param Integer $frequency
     * @param String $status
     * @param String $paymentStatus
     * @param DateTime $createdAt
     * @param DateTime $expirationDate
     * @param DateTime $paymentDueDate
     * @param DateTime $dateRepaid
     * @param String $denomination
     * @param Numeric $percentFunded
     * @param Integer|null $votes
     * @param Integer $borrower
     * @param String $countryID
     * @param String $salary
     * @param Integer $rating
     * @param Boolean $facebook
     * @param Boolean $linkedin
     * @param Boolean $google
     * @param Boolean $twitter
     * @param Boolean $paypal
     * @param Boolean $amazon
     * @param Boolean $localbitcoins
     * @param Boolean $ebay
     * @param Boolean $coinbase
     * @param String $creditScore
     * @param Integer $activeToRepaid
     */
    public function __construct(Numeric $id, String $type, String $title, String $description, String $amount, Integer $term, Integer $frequency, String $status, String $paymentStatus, DateTime $createdAt = null, DateTime $expirationDate = null, DateTime $paymentDueDate = null, DateTime $dateRepaid = null, String $denomination, Numeric $percentFunded, Integer $votes = null, Integer $borrower, String $countryID, String $salary, Integer $rating, Boolean $facebook = null, Boolean $linkedin = null, Boolean $google = null, Boolean $twitter = null, Boolean $paypal = null, Boolean $amazon = null, Boolean $localbitcoins = null, Boolean $ebay = null, Boolean $coinbase = null, String $creditScore, Float $activeToRepaid)

    {
        $this->id = $id;
        $this->type = $type;
        $this->title = $title;
        $this->description = $description;
        $this->amount = $amount;
        $this->term = $term;
        $this->frequency = $frequency;
        $this->status = $status;
        $this->paymentStatus = $paymentStatus;
        $this->createdAt = new OptionalDate($createdAt);
        $this->expirationDate = new OptionalDate($expirationDate);
        $this->paymentDueDate = new OptionalDate($paymentDueDate);
        $this->dateRepaid = new OptionalDate($dateRepaid);
        $this->denomination = $denomination;
        $this->percentFunded = $percentFunded;
        $this->votes = new OptionalInteger($votes);
        $this->borrower = $borrower;
        $this->countryID = $countryID;
        $this->salary = $salary;
        $this->rating = $rating;
        $this->facebook = new OptionalBoolean($facebook);
        $this->linkedin = new OptionalBoolean($linkedin);
        $this->google = new OptionalBoolean($google);
        $this->twitter = new OptionalBoolean($twitter);
        $this->paypal = new OptionalBoolean($paypal);
        $this->amazon = new OptionalBoolean($amazon);
        $this->localbitcoins = new OptionalBoolean($localbitcoins);
        $this->ebay = new OptionalBoolean($ebay);

        $this->coinbase = new OptionalBoolean($coinbase);
        $this->activeToRepaid = $activeToRepaid;
        $this->creditScore = $creditScore;
    }

}