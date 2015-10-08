<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/5/15
 * Time: 1:00 AM
 */

namespace BLC\Model;


use Types\String;

class OptionalString extends Optional
{

    /**
     * @param mixed $val
     * @return boolean
     */
    protected function type($val)
    {
        return $val instanceof String;
    }
}