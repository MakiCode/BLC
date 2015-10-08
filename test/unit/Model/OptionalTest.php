<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/28/15
 * Time: 2:35 PM
 */

namespace BLC\Config;


use BLC\Model\Optional;
use InvalidArgumentException;
use LogicException;

class OptionalTestImpl extends Optional {

    public $valid;

    public function __construct($valid, $item) {
        $this->valid = $valid;
        parent::__construct($item);
    }
    /**
     * @param mixed $val
     * @return boolean
     */
    protected function type($val)
    {
        return $this->valid;
    }
}

/**
 * Class OptionalDateTest
 * @uses \BLC\Model\Optional
 * * @uses \BLC\Model\OptionalDate
 * @package BLC\Config
 */
class OptionalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers BLC\Model\Optional::has
     */
    public function testNull()
    {
        $optional = new OptionalTestImpl(true, null);
        $this->assertThat($optional->has(), $this->equalTo(false));
    }

    /**
     * @covers BLC\Model\Optional::get
     */
    public function testNonEmpty()
    {
        $optionalTrue = new OptionalTestImpl(true, "A");
        $this->assertThat($optionalTrue->get(), $this->equalTo("A"));
    }

    /**
     * @covers BLC\Model\Optional::get
     */
    public function testGetEmpty()
    {
        $this->setExpectedException(LogicException::class);
        $optional = new OptionalTestImpl(true, null);
        $optional->get();
    }


    /**
     * @covers BLC\Model\Optional::has
     */
    public function testDoesHaveDate()
    {
        $optionalDate = new OptionalTestImpl(true, "B");
        $this->assertThat($optionalDate->has(), $this->equalTo(true));
    }

    /**
     * @covers BLC\Model\Optional::__construct
     */
    public function testMakeBadDate()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        new OptionalTestImpl(false, "A");
    }

    /**
     * @covers BLC\Model\Optional::jsonSerialize
     */
    public function testJSONSerialize() {
        $optional = new OptionalTestImpl(true, "A");
        $this->assertThat($optional->jsonSerialize(), $this->equalTo("A"));
    }

    /**
     * @covers BLC\Model\Optional::jsonSerialize
     */
    public function testJSONSerializeNull() {
        $optional = new OptionalTestImpl(true, null);
        $this->assertThat($optional->jsonSerialize(), $this->equalTo(null));
    }
    /**
     * @covers BLC\Model\Optional::__construct
     */
    public function testFalseNull() {
        $optional = new OptionalTestImpl(false, null);
        $this->assertThat($optional->has(), $this->equalTo(false));
    }

}
