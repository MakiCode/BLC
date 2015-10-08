<?php
namespace BLC\Model;
use PHPUnit_Framework_TestCase;

/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/4/15
 * Time: 5:01 PM
 *
 * @uses BLC\Model\Optional
 * @uses BLC\Model\NumericString
 * @uses BLC\Model\OptionalNumericString
 *
 */
class OptionalNumericStringTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \BLC\Model\OptionalNumericString::type
     */
    public function testGoodInput() {
        $optional = new OptionalNumericString(new NumericString("134"));
        $this->assertThat($optional->get(), $this->equalTo(new NumericString("134")));
    }

    /**
     * @covers \BLC\Model\OptionalNumericString::type
     */
    public function testBadInput() {
        $this->setExpectedException(\InvalidArgumentException::class);
        $optional = new OptionalNumericString("ABC");
    }

}
