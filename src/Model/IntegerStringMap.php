<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/21/15
 * Time: 11:55 AM
 */

namespace BLC\Model;

use Types\Integer;
use Types\String;
use Types\TypedMap;

class IntegerStringMap extends TypedMap
{

    /**
     * @param mixed $key
     * @return boolean
     */
    protected function keyType($key)
    {
        return is_int($key);
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    protected function valueType($value)
    {
        return is_string($value);
    }
}