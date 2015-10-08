<?php

/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/14/15
 * Time: 1:18 PM
 *
 * A value class representing an investment
 */
namespace BLC\Model;

use JsonSerializable;
use Types\Integer;
use Types\Numeric;

final class Investment implements JsonSerializable
{
    /**
     * The amount that has been invested
     * @var NumericString
     */
    private $amount;

    /**
     * The rate at which this investment is set
     * @var NumericString
     */
    private $rate;

    /**
     * @var Numeric
     */
    private $id;
    /**
     * @var Numeric
     */
    private $loanId;
    /**
     * @var OptionalDate
     */
    private $dateInvested;


    /**
     * @var Numeric
     */
    private $investorId;

    /**
     * Investment constructor.
     * @param NumericString $amount
     * @param NumericString $rate
     * @param Numeric $id
     * @param Integer $loanId
     * @param OptionalDate $dateInvested
     * @param Numeric $investorId
     */
    public function __construct(NumericString $amount, NumericString $rate, Numeric $id, Numeric $loanId, OptionalDate $dateInvested, Numeric $investorId)
    {
        $this->amount = $amount;
        $this->rate = $rate;
        $this->id = $id;
        $this->loanId = $loanId;
        $this->dateInvested = $dateInvested;
        $this->investorId = $investorId;
    }

    /**
     * Get the amount of money that this class
     * @return String
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return String
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @return Numeric
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Numeric
     */
    public function getLoanId()
    {
        return $this->loanId;
    }

    /**
     * @return OptionalDate
     */
    public function getDateInvested()
    {
        return $this->dateInvested;
    }

    /**
     * @return Numeric
     */
    public function getInvestorId()
    {
        return $this->investorId;
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
        $class = new \stdClass();
        $class->amount = $this->amount->jsonSerialize();
        $class->rate = $this->rate->jsonSerialize();
        $class->id = $this->id->jsonSerialize();
        $class->loanId = $this->loanId->jsonSerialize();
        if($this->dateInvested->has()) {
            $class->dateInvested = $this->dateInvested->get()->format(DATE_ISO8601);
        } else {
            $class->dateInvested = null;
        }
        $class->investorId = $this->investorId->jsonSerialize();
        return $class;
    }
}