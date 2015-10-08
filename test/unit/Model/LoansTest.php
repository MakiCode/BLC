<?php

namespace BLC\Model;
use PHPUnit_Framework_TestCase;
use Types\Integer;
use Types\Numeric;
use Types\String;


/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/4/15
 * Time: 5:01 PM
 */

/**
 * Class LoansTest
 * @package BLC\Model
 * @uses BLC\Model\LoanFactory
 * @uses BLC\Model\Loan
 * @uses BLC\Model\OptionalDate
 * @uses BLC\Model\Optional
 * @uses BLC\Model\OptionalBoolean
 */
class LoansTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers \BLC\Model\Loans::isType
     */
    public function testBadType()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $loans = new Loans("A", "B", "C");
    }
    /**
     * @covers \BLC\Model\Loans::isType
     */
    public function testGoodType()
    {
        $loanFactory = new LoanFactory(new Numeric(1), new Integer(2), new String("A"), new String("B"));
        $loan = $loanFactory->build();
        $loans = new Loans($loan);
        $this->assertThat($loans[0], $this->equalTo($loan));
    }
}
