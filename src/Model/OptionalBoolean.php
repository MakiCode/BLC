<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/5/15
 * Time: 12:02 AM
 */

namespace BLC\Model;



use Types\Boolean;

class OptionalBoolean extends Optional
{

    /**
     * @param mixed $val
     * @return boolean
     */
    protected function type($val)
    {
        return $val instanceof Boolean;
    }
}