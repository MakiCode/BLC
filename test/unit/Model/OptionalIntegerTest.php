<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/12/15
 * Time: 3:05 PM
 */

namespace BLC\Model;


use Types\Integer;
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/4/15
 * Time: 5:01 PM
 *
 * @uses BLC\Model\Optional
 * @uses BLC\Model\OptionalInteger
 *
 */
class OptionalIntegerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \BLC\Model\OptionalInteger::type
     */
    public function testGoodInput() {
        $optional = new OptionalInteger(new Integer(134));
        $this->assertThat($optional->get(), $this->equalTo(new Integer(134)));
    }

    /**
     * @covers \BLC\Model\OptionalInteger::type
     */
    public function testBadInput() {
        $this->setExpectedException(\InvalidArgumentException::class);
        $optional = new OptionalInteger("ABC");
    }

}
