<?php

namespace BLC\Model;
use Types\Integer;
use Types\Numeric;

/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/15/15
 * Time: 5:42 PM
 */
final class Int_Numeric
{
    /**
     * @var Integer
     */
    private $first;

    /**
     * @var Numeric
     */
    private $second;

    /**
     * BorrowerLoan constructor.
     * @param int $first
     * @param int $second
     */
    public function __construct(Integer $first, Numeric $second)
    {
        $this->first = $first;
        $this->second = $second;
    }

    /**
     * @return int
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * @return Numeric
     */
    public function getSecond()
    {
        return $this->second;
    }

}