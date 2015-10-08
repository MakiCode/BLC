<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/2/15
 * Time: 4:02 PM
 */

namespace BLC\Model;


use InvalidArgumentException;
use JsonSerializable;
use LogicException;

abstract class Optional implements JsonSerializable
{
    /**
     * @var mixed|null
     */
    private $val;

    /**
     * Construct this optional
     * @param mixed $val
     */
    public function __construct($val)
    {
        if (!is_null($val) && !$this->type($val)) {
            throw new InvalidArgumentException("You must construct this with either null or a valid type");
        }
        $this->val = $val;
    }

    /**
     * @return bool
     */
    public function has()
    {
        return !is_null($this->val);
    }

    /**
     * @return mixed
     */
    public function get()
    {
        if (!$this->has()) {
            throw new LogicException("this option is empty, remember to call hasDate() first!");
        }
        return $this->val;
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        return $this->val;
    }

    /**
     * @param mixed $val
     * @return boolean
     */
    protected abstract function type($val);
}