<?php
namespace BLC\Model;
use DateTime;
use PHPUnit_Framework_TestCase;

/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/4/15
 * Time: 5:00 PM
 */

/**
 * Class OptionalDateTest
 * @package BLC\Model
 * @uses BLC\Model\Optional
 */
class OptionalDateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \BLC\Model\OptionalDate::type
     */
    public function testGoodInput() {
        date_default_timezone_set("America/Mexico_City");
        $now = new DateTime();
        $optional = new OptionalDate($now);
        $this->assertThat($optional->get(), $this->equalTo($now));
    }

    /**
     * @covers \BLC\Model\OptionalDate::type
     */
    public function testBadInput() {
        $this->setExpectedException(\InvalidArgumentException::class);
        $optional = new OptionalDate("ABC");
    }

    /**
     * @covers \BLC\Model\OptionalDate::emptyOption
     */
    public function testEmpty() {
        $optional = OptionalDate::emptyOption();
        $expected = new OptionalDate(null);

        $this->assertThat($optional, $this->equalTo($expected));
    }
}
