<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/4/15
 * Time: 6:36 PM
 */

namespace BLC\Model;
$flockFail = false;

use Monolog\Logger;

function flock($handle, $operation, &$wouldblock = null) {
    global $flockFail;

    if($flockFail) {
        return false;
    } else {
        return \flock($handle, $operation, $wouldblock);
    }
}
/**
 * @uses BLC\Model\ExclusiveLock
 */
class ExclusiveLockTest extends \PHPUnit_Framework_TestCase
{
    private $key = "test";
    private $logger;

    public function setUp()
    {
        $this->logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
    }

    public function tearDown()
    {
        global $flockFail;
        $this->logger = null;
        $flockFail = false;
    }

    /**
     * @covers BLC\Model\ExclusiveLock::__construct
     */
    public function testCreation()
    {
        $exclusiveLock = new ExclusiveLock($this->key, $this->logger);
        $this->assertThat($this->key . ".lockfile", $this->fileExists());
    }

    /**
     * @covers BLC\Model\ExclusiveLock::lock
     */
    public function testLocking()
    {
        $exclusiveLock = new ExclusiveLock($this->key, $this->logger);
        $exclusiveLock->lock();
        $handle = fopen($this->key . ".lockfile", "r");
        $this->assertThat(flock($handle, LOCK_EX | LOCK_NB), $this->equalTo(false));
        fclose($handle);
    }

    /**
     * @covers BLC\Model\ExclusiveLock::lock
     */
    public function testLockingMultipleTimes()
    {
        $exclusiveLock = new ExclusiveLock($this->key, $this->logger);
        $exclusiveLock->lock();
        $exclusiveLock->lock();
        $exclusiveLock->lock();
        $exclusiveLock->lock();
        $handle = fopen($this->key . ".lockfile", "r");
        $this->assertThat(flock($handle, LOCK_EX | LOCK_NB), $this->equalTo(false));
        fclose($handle);
    }


    /**
     * @covers BLC\Model\ExclusiveLock::unlock
     */
    public function testUnlocking()
    {
        $exclusiveLock = new ExclusiveLock($this->key, $this->logger);
        $exclusiveLock->lock();
        $handle = fopen($this->key . ".lockfile", "r");
        $this->assertThat(flock($handle, LOCK_EX | LOCK_NB), $this->equalTo(false));
        $exclusiveLock->unlock();
        $this->assertThat(flock($handle, LOCK_EX), $this->equalTo(true));
        fclose($handle);
    }

    /**
     * @covers BLC\Model\ExclusiveLock::unlock
     */
    public function testUnlockingMulti()
    {
        $exclusiveLock = new ExclusiveLock($this->key, $this->logger);
        $exclusiveLock->lock();
        $handle = fopen($this->key . ".lockfile", "r");
        $this->assertThat(flock($handle, LOCK_EX | LOCK_NB), $this->equalTo(false));
        $exclusiveLock->unlock();
        $this->assertThat(flock($handle, LOCK_EX), $this->equalTo(true));
        fclose($handle);
        $this->assertThat($exclusiveLock->unlock(), $this->equalTo(true));
        $this->assertThat($exclusiveLock->unlock(), $this->equalTo(true));
        $this->assertThat($exclusiveLock->unlock(), $this->equalTo(true));
    }

    /**
     * @covers BLC\Model\ExclusiveLock::__destruct
     */
    public function testDestruct()
    {
        $exclusiveLock = new ExclusiveLock($this->key, $this->logger);
        $this->assertThat($this->key . ".lockfile", $this->fileExists());
        $exclusiveLock->__destruct();
        $this->assertThat($this->key . ".lockfile", $this->logicalNot($this->fileExists()));
    }

    /**
     * @covers BLC\Model\ExclusiveLock::__destruct
     */
    public function testDestructLocked()
    {
        $exclusiveLock = new ExclusiveLock($this->key, $this->logger);
        $this->assertThat($this->key . ".lockfile", $this->fileExists());
        $exclusiveLock->lock();
        $exclusiveLock->__destruct();
        $this->assertThat($this->key . ".lockfile", $this->logicalNot($this->fileExists()));
    }

    public function testFLockFailLock() {
        global $flockFail;
        $exclusiveLock = new ExclusiveLock($this->key, $this->logger);
        $flockFail = true;
        $this->assertThat($exclusiveLock->lock(), $this->equalTo(false));
    }


    public function testFLockFailUnlockWithoutLock() {
        global $flockFail;
        $exclusiveLock = new ExclusiveLock($this->key, $this->logger);
        $flockFail = true;
        $this->assertThat($exclusiveLock->unlock(), $this->equalTo(true));
    }

    public function testFLockFailUnlock() {
        global $flockFail;
        $exclusiveLock = new ExclusiveLock($this->key, $this->logger);
        $exclusiveLock->lock();
        $flockFail = true;
        $this->assertThat($exclusiveLock->unlock(), $this->equalTo(false));
    }
}
