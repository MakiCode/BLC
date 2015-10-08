<?php

/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/28/15
 * Time: 3:53 PM
 *
 * @uses Types\Primitive
 * @uses Types\String
 */
class IntegerStringMapTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers BLC\Model\IntegerStringMap::keyType
     * @covers BLC\Model\IntegerStringMap::valueType
     */
    public function testKeyAndValueGood() {
        $i = 5;
        $val =  "abc";
        $map = new \BLC\Model\IntegerStringMap([$i => $val]);
        $this->assertThat($map[$i], $this->equalTo($val));
    }
    /**
     * @expectedException InvalidArgumentException
     * @covers BLC\Model\IntegerStringMap::keyType
     */
    public function testKeyBad() {
        new \BLC\Model\IntegerStringMap(["ABC" => "1243"]);
    }

    /**
     * @expectedException InvalidArgumentException
     * @covers BLC\Model\IntegerStringMap::keyType
     * @covers BLC\Model\IntegerStringMap::valueType
     */
    public function testValueBad() {
        new \BLC\Model\IntegerStringMap([123 => 1243]);
    }

}
