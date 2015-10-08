<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/4/15
 * Time: 9:42 PM
 */

namespace BLC\Model;


use Types\Integer;
use Types\Numeric;

class Int_NumericTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers BLC\Model\Int_Numeric::__construct
     * @covers BLC\Model\Int_Numeric::getSecond
     * @covers BLC\Model\Int_Numeric::getFirst
     */
    public function testGetters() {
        $int_int = new Int_Numeric(new Integer(1), new Numeric(2));
        $this->assertThat($int_int->getFirst(), $this->equalTo(new Integer(1)));
        $this->assertThat($int_int->getSecond(), $this->equalTo(new Numeric(2)));
    }
}
