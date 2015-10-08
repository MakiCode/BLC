<?php

namespace BLC\Model;
use DateTime;
use PHPUnit_Framework_TestCase;
use stdClass;
use Types\Numeric;

/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/4/15
 * Time: 4:26 PM
 *
 * @uses BLC\Model\OptionalDate
 * @uses BLC\Model\NumericString
 * @uses BLC\Model\Optional
 */
class InvestmentTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        date_default_timezone_set("America/Mexico_City");
    }

    /**
     * @covers \BLC\Model\Investment::__construct
     * @covers \BLC\Model\Investment::getAmount
     * @covers \BLC\Model\Investment::getRate
     * @covers \BLC\Model\Investment::getId
     * @covers \BLC\Model\Investment::getLoanId
     * @covers \BLC\Model\Investment::getDateInvested
     * @covers \BLC\Model\Investment::getInvestorId
     */
    public function testGetters()
    {
        $now = new DateTime();
        $investment = new Investment(
            new NumericString("1"),
            new NumericString("2"),
            new Numeric(3),
            new Numeric(4),
            new OptionalDate($now),
            new Numeric(5)
        );

        $this->assertThat($investment->getAmount(), $this->equalTo(new NumericString("1")));
        $this->assertThat($investment->getRate(), $this->equalTo(new NumericString("2")));
        $this->assertThat($investment->getId(), $this->equalTo(new Numeric(3)));
        $this->assertThat($investment->getLoanId(), $this->equalTo(new Numeric(4)));
        $this->assertThat($investment->getDateInvested()->get(), $this->equalTo($now));
        $this->assertThat($investment->getInvestorId(), $this->equalTo(new Numeric(5)));
    }

    public function testJSONSerializeHas()
    {
        $now = new DateTime();
        $investment = new Investment(
            new NumericString("1"),
            new NumericString("2"),
            new Numeric(3),
            new Numeric(4),
            new OptionalDate($now),
            new Numeric(5)
        );

        $expected = new stdClass();
        $expected->amount = "1";
        $expected->rate = "2";
        $expected->id = 3;
        $expected->loanId = 4;
        $expected->dateInvested = $now->format(DATE_ISO8601);
        $expected->investorId = 5;

        $this->assertThat($investment->jsonSerialize(), $this->equalTo($expected));
    }

    public function testJSONSerializeHasNot()
    {
        $investment = new Investment(
            new NumericString("1"),
            new NumericString("2"),
            new Numeric(3),
            new Numeric(4),
            new OptionalDate(null),
            new Numeric(5)
        );

        $expected = new stdClass();
        $expected->amount = "1";
        $expected->rate = "2";
        $expected->id = 3;
        $expected->loanId = 4;
        $expected->dateInvested = null;
        $expected->investorId = 5;

        $this->assertThat($investment->jsonSerialize(), $this->equalTo($expected));
    }
}
