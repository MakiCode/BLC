<?php
use BLC\Model\OptionalBoolean;

/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/5/15
 * Time: 12:28 AM
 *  @uses BLC\Model\Optional
 */
class OptionalBooleanTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \BLC\Model\OptionalBoolean::type
     */
    public function testGoodInput() {
        $optional = new OptionalBoolean(new \Types\Boolean(true));
        $this->assertThat($optional->get()->get(), $this->equalTo(true));
    }

    /**
     * @covers \BLC\Model\OptionalBoolean::type
     */
    public function testBadInput() {
        $this->setExpectedException(\InvalidArgumentException::class);
        $optional = new OptionalBoolean("ABC");
    }
}
