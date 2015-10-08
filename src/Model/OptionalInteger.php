<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/9/15
 * Time: 1:58 PM
 */

namespace BLC\Model;


use Types\Integer;

class OptionalInteger extends Optional
{

    /**
     * @param mixed $val
     * @return boolean
     */
    protected function type($val)
    {
        return $val instanceof Integer;
    }
}