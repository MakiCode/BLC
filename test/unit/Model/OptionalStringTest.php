<?php
use BLC\Model\OptionalString;

/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/5/15
 * Time: 1:08 AM
 *
 * @uses BLC\Model\Optional
 * @uses Types\String
 * @uses Types\Primitive
 *
 */
class OptionalStringTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \BLC\Model\OptionalString::type
     */
    public function testGoodInput() {
        $optional = new OptionalString(new \Types\String("abc"));
        $this->assertThat($optional->get()->get(), $this->equalTo("abc"));
    }

    /**
     * @covers \BLC\Model\OptionalString::type
     */
    public function testBadInput() {
        $this->setExpectedException(\InvalidArgumentException::class);
        $optional = new OptionalString("ABC");
    }
}
