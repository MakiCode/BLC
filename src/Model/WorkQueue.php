<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/15/15
 * Time: 3:29 PM
 */

namespace BLC\Model;


use Monolog\Logger;
use Mutex;
use SplQueue;

/**
 * Class InvestmentQueue
 * A asynchronous queue class
 * @package BLC
 */
class WorkQueue
{
    private $queue;
    private $exclusiveLock;

    public function __construct(ExclusiveLock $lock)
    {
        $this->exclusiveLock = $lock;
        $this->queue = new SplQueue();
    }

    /**
     * (PHP 5 &gt;= 5.3.0)<br/>
     * Adds an element to the queue.
     * @link http://php.net/manual/en/splqueue.enqueue.php
     * @param WorkItem $value <p>
     * The value to enqueue.
     * </p>
     * @return void
     */
    public function enqueue(WorkItem $value)
    {
        $this->exclusiveLock->lock();
        $this->queue->enqueue($value);
        $this->exclusiveLock->unlock();
    }

    /**
     * (PHP 5 &gt;= 5.3.0)<br/>
     * Dequeues a node from the queue
     * @link http://php.net/manual/en/splqueue.dequeue.php
     * @return WorkItem The value of the dequeue-d node.
     */
    public function dequeue()
    {
        $this->exclusiveLock->lock();
        $result = $this->queue->dequeue();
        $this->exclusiveLock->unlock();
        return $result;
    }

    public function __destruct()
    {
        $this->exclusiveLock->__destruct();
    }

    public function hasNext()
    {
        $this->exclusiveLock->lock();
        $result = $this->queue->count();
        $this->exclusiveLock->unlock();
        return $result != 0;
    }
}