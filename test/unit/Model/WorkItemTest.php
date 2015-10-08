<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/4/15
 * Time: 5:02 PM
 */

namespace BLC\Model;


use Types\Integer;
use Types\Numeric;

/**
 * Class WorkItemTest
 * @package BLC\Model
 * @uses BLC\Model\OptionalNumericString
 * @uses BLC\Model\WorkItem
 * @uses BLC\Model\NumericString
 * @uses BLC\Model\Optional
 */
class WorkItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers BLC\Model\WorkItem::__construct
     * @covers BLC\Model\WorkItem::getAmount
     * @covers BLC\Model\WorkItem::getLoanID
     * @covers BLC\Model\WorkItem::getRate
     * @covers BLC\Model\WorkItem::getMaxRate
     */
    public function testGetters()
    {
        $workItem = new WorkItem(new Numeric(1), new NumericString("2"), new NumericString("3"), new NumericString("4"));
        $this->assertThat($workItem->getLoanID(), $this->equalTo(new Numeric(1)));
        $this->assertThat($workItem->getAmount(), $this->equalTo(new NumericString("2")));
        $this->assertThat($workItem->getRate()->get(), $this->equalTo(new NumericString("3")));
        $this->assertThat($workItem->getMaxRate()->get(), $this->equalTo(new NumericString("4")));

    }
}
