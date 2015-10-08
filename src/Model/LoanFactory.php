<?php
namespace BLC\Model;

use function BLC\convertOptionalDate;
use DateTime;
use GuzzleHttp\Tests\Psr7\Str;
use Types\Boolean;
use Types\Float;
use Types\Integer;
use Types\Numeric;
use Types\String;

/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/17/15
 * Time: 3:45 PM
 */
final class LoanFactory
{
    /**
     * @var Numeric
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
     * @var Integer
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
    private $countryId;
    /**
     * MONEY NUMBER
     * @var String|null
     */
    private $salary;

    /**
     * @var Integer
     */
    private $rating;
    /**
     * @var Boolean|null
     */
    private $social_facebook;
    /**
     * @var Boolean|null
     */
    private $social_linkedin;
    /**
     * @var Boolean|null
     */
    private $social_google;
    /**
     * @var Boolean|null
     */
    private $social_twitter;
    /**
     * @var Boolean|null
     */
    private $trusted_paypal;
    /**
     * @var Boolean|null
     */
    private $trusted_amazon;
    /**
     * @var Boolean|null
     */
    private $trusted_localbitcoins;
    /**
     * @var Boolean|null
     */
    private $trusted_ebay;

    /**
     * @var Boolean|null
     */
    private $coinbase;

    /**
     * @var String
     */
    private $creditScore;

    /**
     * @param Boolean $coinbase
     */
    public function setCoinbase(Boolean $coinbase = null)
    {
        $this->coinbase = $coinbase;
    }

    /**
     * @param String $creditScore
     */
    public function setCreditScore(String $creditScore)
    {
        $this->creditScore = $creditScore;
    }

    /**
     * @param Float $activeToRepaid
     */
    public function setActiveToRepaid(Float $activeToRepaid)
    {
        $this->activeToRepaid = $activeToRepaid;
    }

    /**
     * @var Float
     */
    private $activeToRepaid;

    /**
     * @param Integer $id
     * @return void
     */
    public function setId(Numeric $id)
    {
        $this->id = $id;
    }

    /**
     * @param String $type
     * @return void
     */
    public function setType(String $type)
    {
        $this->type = $type;
    }

    /**
     * @param String $title
     * @return void
     */
    public function setTitle(String $title)
    {
        $this->title = $title;
    }

    /**
     * @param String $description
     * @return void
     */
    public function setDescription(String $description)
    {
        $this->description = $description;
    }

    /**
     * @param String $amount
     * @return void
     */
    public function setAmount(String $amount)
    {
        $this->amount = $amount;
    }

    /**
     * @param Integer $term
     * @return void
     */
    public function setTerm(Integer $term)
    {
        $this->term = $term;
    }

    /**
     * @param Integer $frequency
     * @return void
     */
    public function setFrequency(Integer $frequency)
    {
        $this->frequency = $frequency;
    }

    /**
     * @param String $status
     * @return void
     */
    public function setStatus(String $status)
    {
        $this->status = $status;
    }

    /**
     * @param String $paymentStatus
     * @return void
     */
    public function setPaymentStatus(String $paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;
    }

    /**
     * @param OptionalDate $createdAt
     * @return void
     */
    public function setCreatedAt(DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param OptionalDate $expirationDate
     * @return void
     */
    public function setExpirationDate(DateTime $expirationDate = null)
    {
        $this->expirationDate = $expirationDate;
    }

    /**
     * @param OptionalDate $paymentDueDate
     * @return void
     */
    public function setPaymentDueDate(DateTime $paymentDueDate = null)
    {
        $this->paymentDueDate = $paymentDueDate;
    }

    /**
     * @param OptionalDate $dateRepaid
     * @return void
     */
    public function setDateRepaid(DateTime $dateRepaid = null)
    {
        $this->dateRepaid = $dateRepaid;
    }

    /**
     * @param String $denomination
     * @return void
     */
    public function setDenomination(String $denomination)
    {
        $this->denomination = $denomination;
    }

    /**
     * @param Numeric $percentFunded
     * @return void
     */
    public function setPercentFunded(Numeric $percentFunded)
    {
        $this->percentFunded = $percentFunded;
    }

    /**
     * @param Integer $votes
     * @return void
     */
    public function setVotes(Integer $votes)
    {
        $this->votes = $votes;
    }

    /**
     * @param Integer $borrower
     * @return void
     */
    public function setBorrower(Integer $borrower)
    {
        $this->borrower = $borrower;
    }

    /**
     * @param String $countryId
     * @return void
     */
    public function setCountryId(String $countryId)
    {
        $this->countryId = $countryId;
    }

    /**
     * @param String $salary
     * @return void
     */
    public function setSalary(String $salary)
    {
        $this->salary = $salary;
    }

    /**
     * @param Integer $rating
     * @return void
     */
    public function setRating(Integer $rating)
    {
        $this->rating = $rating;
    }

    /**
     * @param Boolean $social_facebook
     * @return void
     */
    public function setSocialFacebook(Boolean $social_facebook)
    {
        $this->social_facebook = $social_facebook;
    }

    /**
     * @param Boolean $social_linkedin
     * @return void
     */
    public function setSocialLinkedin(Boolean $social_linkedin)
    {
        $this->social_linkedin = $social_linkedin;
    }

    /**
     * @param Boolean $social_google
     * @return void
     */
    public function setSocialGoogle(Boolean $social_google)
    {
        $this->social_google = $social_google;
    }

    /**
     * @param Boolean $social_twitter
     * @return void
     */
    public function setSocialTwitter(Boolean $social_twitter)
    {
        $this->social_twitter = $social_twitter;
    }

    /**
     * @param Boolean $trusted_paypal
     * @return void
     */
    public function setTrustedPaypal(Boolean $trusted_paypal)
    {
        $this->trusted_paypal = $trusted_paypal;
    }

    /**
     * @param Boolean $trusted_amazon
     * @return void
     */
    public function setTrustedAmazon(Boolean $trusted_amazon)
    {
        $this->trusted_amazon = $trusted_amazon;
    }

    /**
     * @param Boolean $trusted_localbitcoins
     * @return void
     */
    public function setTrustedLocalbitcoins(Boolean $trusted_localbitcoins)
    {
        $this->trusted_localbitcoins = $trusted_localbitcoins;
    }

    /**
     * @param Boolean $trusted_ebay
     * @return void
     */
    public function setTrustedEbay(Boolean $trusted_ebay)
    {
        $this->trusted_ebay = $trusted_ebay;
    }


    public function __construct(Numeric $id, Integer $borrower, String $type, String $denomination)
    {
        $this->id = $id;
        $this->borrower = $borrower;
        $this->type = $type;
        $this->denomination = $denomination;
        $this->title = new String("");
        $this->description = new String("");
        $this->amount = new String("");
        $this->term = new Integer(0);
        $this->frequency = new Integer(0);
        $this->status = new String("");
        $this->paymentStatus = new String("");
        $this->createdAt = null;
        $this->expirationDate = null;
        $this->paymentDueDate = null;
        $this->dateRepaid = null;
        $this->percentFunded = new Numeric(0);
        $this->votes = null;
        $this->countryId = new String("");
        $this->salary = new String("$0");
        $this->rating = new Integer(0);
        $this->social_facebook = null;
        $this->social_linkedin = null;
        $this->social_google = null;
        $this->social_twitter = null;
        $this->trusted_paypal = null;
        $this->trusted_amazon = null;
        $this->trusted_localbitcoins = null;
        $this->trusted_ebay = new Boolean(false);

        $this->activeToRepaid = new Float((float)0);
        $this->coinbase = new Boolean(false);
        $this->creditScore = new String("");
    }

    /**
     * @return Loan
     */
    public function build()
    {

        return new Loan($this->id, $this->type, $this->title, $this->description, $this->amount, $this->term, $this->frequency, $this->status, $this->paymentStatus, $this->createdAt, $this->expirationDate, $this->paymentDueDate, $this->dateRepaid, $this->denomination, $this->percentFunded, $this->votes, $this->borrower, $this->countryId, $this->salary, $this->rating, $this->social_facebook, $this->social_linkedin, $this->social_google, $this->social_twitter, $this->trusted_paypal, $this->trusted_amazon, $this->trusted_localbitcoins, $this->trusted_ebay, $this->coinbase, $this->creditScore, $this->activeToRepaid);
    }
}