<?php

use const BLC\ISO_8061_MIL_Z;
use const BLC\ISO_8061_Z;

/**
 * Class FunctionDateConversionHelperTest
 *
 * @uses ::BLC\convertOptionalDate
 * @uses ::BLC\isValidOptionalDate
 */
class FunctionDateConversionHelperTest extends PHPUnit_Framework_TestCase
{


    public function setUp()
    {
        date_default_timezone_set("UTC");
    }

    /**
     * @covers ::BLC\dateConversionHelper
     */
    public function testNull()
    {
        $this->assertThat(\BLC\dateConversionHelper(null), $this->isNull());
    }

    /**
     * @covers ::BLC\dateConversionHelper
     */
    public function testBadDate()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        \BLC\dateConversionHelper("a");
    }

    /**
     * @covers ::BLC\dateConversionHelper
     */
    public function testISO8061Date()
    {
        $now = new DateTime();
        $this->assertThat(\BLC\dateConversionHelper($now->format(\BLC\ISO_8061)), $this->equalTo($now));
    }

    public function testISO_8061_NONEDate()
    {
        $now = new DateTime();
        $this->assertThat(\BLC\dateConversionHelper($now->format(\BLC\ISO_8061_NONE)), $this->equalTo($now));
    }

    /**
     * @covers ::BLC\dateConversionHelper
     */
    public function testDATE_AND_TIMEDate()
    {
        $now = new DateTime();
        $this->assertThat(\BLC\dateConversionHelper($now->format(\BLC\DATE_AND_TIME)), $this->equalTo($now));
    }

    /**
     * @covers ::BLC\dateConversionHelper
     */
    public function testISO_8061_MILDate()
    {
        $now = new DateTime();
        $this->assertThat(\BLC\dateConversionHelper($now->format(ISO_8061_MIL)), $this->equalTo($now));
    }

    /**
     * @covers ::BLC\dateConversionHelper
     */
    public function testISO_8061_ZDate()
    {
        $now = new DateTime();
        $result = \BLC\dateConversionHelper($now->format(ISO_8061_Z));
        $this->assertThat($result, $this->equalTo($now));
        $this->assertThat($result->getTimezone(), $this->equalTo(new DateTimeZone("Z")));
    }

    /**
     * @covers ::BLC\dateConversionHelper
     */
    public function testISO_8061_MIL_ZDate()
    {
        $now = new DateTime();
        $result = \BLC\dateConversionHelper($now->format(ISO_8061_MIL_Z));
        $this->assertThat($result, $this->equalTo($now));
        $this->assertThat($result->getTimezone(), $this->equalTo(new DateTimeZone("Z")));
    }
}