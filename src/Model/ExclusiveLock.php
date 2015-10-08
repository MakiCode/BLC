<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/4/15
 * Time: 5:54 PM
 */

namespace BLC\Model;

use Monolog\Logger;

/**
 *
 * Class ExclusiveLock
 * @package BLC\Model
 * @author harry (http://stackoverflow.com/a/3922765/2167545)
 */
class ExclusiveLock
{
    private $key = null;  //user given value
    private $file = null;  //resource to lock
    private $own = false; //have we locked resource
    /**
     * @var Logger
     */
    private $logger;

    function __construct($key, Logger $logger)
    {
        $this->key = $key;
        $this->logger = $logger;
        //create a new resource or get existing with same key
        $logger->addInfo("Creating file lock");
        $this->file = fopen("$key.lockfile", 'w+');
    }


    function __destruct()
    {
        $this->logger->addInfo("Destroying lock");
        if ($this->own == true) {
            $this->unlock();
        }
        if (is_resource($this->file)) {
            fclose($this->file);
        }
        if(file_exists($this->key . ".lockfile")) {
            unlink($this->key . ".lockfile");
        }
    }


    function lock()
    {
        $this->logger->addInfo("Acquiring lock");
        if (!$this->own) {
            if (flock($this->file, LOCK_EX)) { //failed
                $this->logger->addInfo("Acquired lock");
                $this->own = true;
            } else {
                $this->logger->addWarning("Failed to acquire lock", ["key" => $this->key]);
                return false;
            }
        } else {
            $this->logger->addInfo("Lock already Acquired");
        }
        return $this->own;
    }


    function unlock()
    {
        $this->logger->addInfo("Releasing lock");
        if ($this->own == true) {
            if (flock($this->file, LOCK_UN)) { //failed
                $this->own = false;
                $this->logger->addInfo("Lock released");
                return true;
            } else {
                $this->logger->addWarning("Failed to release lock", ["key" => $this->key]);
                return false;
            }
        } else {
            $this->logger->addInfo("Lock already released");
            return true;
        }
    }
}