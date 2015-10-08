<?php

/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/14/15
 * Time: 1:17 PM
 */
namespace BLC\Model;

use Types\TypedList;

/**
 * Class Investments
 * A class representing a list of investments.
 * @package BLC
 */
final class Investments extends TypedList
{
    /**
     * Create a list of investments. In order to be a list, there must be at least 1 real investment.
     * @param Investment $firstInvestment
     * @param Investment ...$rest convenience for quickly constructing lists, all of these investments will be added
     * to the list
     */
    public function __construct(Investment $firstInvestment, Investment ...$rest)
    {
        parent::__construct($firstInvestment, ...$rest);
    }

    /**
     * @param mixed $val
     * @return bool
     */
    protected function isType($val)
    {
        return $val instanceof Investment;
    }
}