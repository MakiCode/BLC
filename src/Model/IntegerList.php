<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/21/15
 * Time: 11:58 AM
 */

namespace BLC\Model;


use Types\Integer;
use Types\TypedList;

class IntegerList extends TypedList
{

    /**
     * @param mixed $val
     * @return bool
     */
    protected function isType($val)
    {
        return is_int($val);
    }
}