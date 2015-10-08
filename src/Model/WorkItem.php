<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/15/15
 * Time: 5:33 PM
 */

namespace BLC\Model;

use Types\Integer;
use Types\Numeric;

final class WorkItem
{
    /**
     * @var Numeric
     */
    private $loanID;

    /**
     * @var NumericString
     */
    private $amount;

    /**
     * @var NumericString
     */
    private $rate;

    /**
     * @var NumericString
     */
    private $maxRate;


    /**
     * WorkItem constructor.
     * @param Numeric $loanID
     * @param NumericString $amount
     * @param NumericString $rate
     * @param NumericString $maxRate
     */
    public function __construct(Numeric $loanID, NumericString $amount, NumericString $rate = null, NumericString $maxRate = null)
    {
        $this->loanID = $loanID;
        $this->amount = $amount;
        $this->rate = new OptionalNumericString($rate);
        $this->maxRate = new OptionalNumericString($maxRate);
    }

    /**
     * @return Numeric
     */
    public function getLoanID()
    {
        return $this->loanID;
    }

    /**
     * @return NumericString
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return OptionalNumericString
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @return OptionalNumericString
     */
    public function getMaxRate()
    {
        return $this->maxRate;
    }
}