<?php

/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/28/15
 * Time: 3:50 PM
 *
 * @uses Types\Primitive
 * @uses Types\Integer
 */
class IntegerListTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers BLC\Model\IntegerList::isType
     */
    public function testTypeGood() {
        $i = 5;
        $integerList = new \BLC\Model\IntegerList($i);

        $this->assertThat($integerList[0], $this->equalTo($i));
    }

    /**
     * @expectedException InvalidArgumentException
     * @covers BLC\Model\IntegerList::isType
     */
    public function testTypeBad() {
        $i = new \Types\Integer(5);
        $integerList = new \BLC\Model\IntegerList($i);
    }


}
