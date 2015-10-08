<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/4/15
 * Time: 5:02 PM
 */

namespace BLC\Model;


use Monolog\Logger;
use Types\Integer;
use Types\Numeric;

/**
 * Class WorkQueueTest
 * @package BLC\Model
 * @uses BLC\Model\WorkQueue
 * @uses BLC\Model\OptionalNumericString
 * @uses BLC\Model\NumericString
 * @uses BLC\Model\Optional
 * @uses BLC\Model\WorkItem
 *
 */
class WorkQueueTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Logger
     */
    private $logger;
    private $dummyLock;

    public function setUp()
    {
        $this->logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $this->dummyLock = $this->getMockBuilder(ExclusiveLock::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @covers BLC\Model\WorkQueue::__construct
     * @covers BLC\Model\WorkQueue::enqueue
     */
    public function testEnqueue()
    {
        $workQueue = new WorkQueue($this->dummyLock);
        $workItem = new WorkItem(new Numeric(1), new NumericString("2"), new NumericString("3"), new NumericString("4"));
        $workQueue->enqueue($workItem);
        $this->assertThat($workQueue->hasNext(), $this->equalTo(true));
        $this->assertThat($workQueue->dequeue(), $this->equalTo($workItem));
    }

    /**
     * @covers BLC\Model\WorkQueue::dequeue
     */
    public function testDequeue()
    {
        $workQueue = new WorkQueue($this->dummyLock);
        $workItem1 = new WorkItem(new Numeric(1), new NumericString("2"), new NumericString("3"), new NumericString("4"));
        $workItem2 = new WorkItem(new Numeric(2), new NumericString("3"), new NumericString("4"), new NumericString("5"));
        $workItem3 = new WorkItem(new Numeric(3), new NumericString("4"), new NumericString("5"), new NumericString("6"));
        $workQueue->enqueue($workItem1);
        $workQueue->enqueue($workItem2);
        $deque1 = $workQueue->dequeue();
        $workQueue->enqueue($workItem3);
        $deque2 = $workQueue->dequeue();
        $this->assertThat($deque1, $this->equalTo($workItem1));
        $this->assertThat($deque2, $this->equalTo($workItem2));
        $this->assertThat($workQueue->dequeue(), $this->equalTo($workItem3));
    }

    /**
     * @covers BLC\Model\WorkQueue::dequeue
     */
    public function testDequeueEmpty()
    {
        $this->setExpectedException(\RuntimeException::class);
        $workQueue = new WorkQueue($this->dummyLock);
        $workQueue->dequeue();
    }


    /**
     * @covers BLC\Model\WorkQueue::hasNext
     */
    public function testHasNextFull()
    {
        $queue = new WorkQueue($this->dummyLock);
        $queue->enqueue(new WorkItem(new Numeric(1), new NumericString("2"), new NumericString("3"), new NumericString("4")));
        $this->assertThat($queue->hasNext(), $this->equalTo(true));
    }

    /**
     * @covers BLC\Model\WorkQueue::hasNext
     */
    public function testHasNextEmpty()
    {
        $queue = new WorkQueue($this->dummyLock);
        $this->assertThat($queue->hasNext(), $this->equalTo(false));
    }

    /**
     * @covers BLC\Model\WorkQueue::__destruct
     */
    public function testDestruct()
    {
        $this->dummyLock->expects($this->once())->method("__destruct");
        $queue = new WorkQueue($this->dummyLock);
        $this->assertThat($queue->hasNext(), $this->equalTo(false));
    }
}
