<?php

namespace BLC\Model;
use InvalidArgumentException;
use Types\Integer;
use Types\Numeric;
use Types\TypedList;


/**
 * TODO refactor this test.
 * This whole test suite has been replaced by TypedList. But as typed list doesn't have a test suite, I'm not going to
 * get rid of this, instead I'm going to have a false sense of positivity about the correctness of my code.
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/14/15
 * Time: 2:50 PM
 *
 * @uses BLC\Model\Investment
 * @uses Types\TypedList
 * @uses BLC\Model\OptionalDate
 * @uses BLC\Model\NumericString
 * @uses BLC\Model\Optional
 */
class InvestmentsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers BLC\Model\Investments::__construct
     * @covers BLC\Model\Investments::isType
     */
    public function testGoodInvestment()
    {
        $investment = new Investment(
            new NumericString("123"),
            new NumericString("123"),
            new Numeric(123),
            new Numeric(123),
            new OptionalDate(new \DateTime()),
            new Numeric(123)
        );
        $investments = new Investments($investment);
        $this->assertThat($investments[0], $this->equalTo($investment));
    }
}
