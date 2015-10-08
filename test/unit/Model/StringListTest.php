<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/4/15
 * Time: 5:02 PM
 */

namespace BLC\Model;

/**
 * Class StringListTest
 * @package BLC\Model
 * @uses BLC\Model\StringList
 */
class StringListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \BLC\Model\StringList::isType
     */
    public function testGoodType()
    {
        $strings = new StringList("A", "B", "C");
        $this->assertThat($strings[0], $this->equalTo("A"));
        $this->assertThat($strings[1], $this->equalTo("B"));
        $this->assertThat($strings[2], $this->equalTo("C"));
    }
    /**
     * @covers \BLC\Model\StringList::isType
     */
    public function testBadType()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new StringList(1,2,3);
    }
}
