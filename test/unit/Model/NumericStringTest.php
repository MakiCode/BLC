<?php
namespace BLC\Model;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/4/15
 * Time: 5:02 PM
 */
class NumericStringTest extends PHPUnit_Framework_TestCase
{
    public function testGoodString() {
        $numericString = new NumericString("1234");
        $this->assertThat($numericString->get(), $this->equalTo("1234"));
    }
    public function testBadString() {
        $this->setExpectedException(InvalidArgumentException::class);
        new NumericString("abc");
    }
    public function testInt() {
        $this->setExpectedException(InvalidArgumentException::class);
        new NumericString(1234);
    }
}
