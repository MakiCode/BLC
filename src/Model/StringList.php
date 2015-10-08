<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/24/15
 * Time: 1:22 PM
 */

namespace BLC\Model;


use Types\TypedList;

class StringList extends TypedList
{

    /**
     * @param mixed $val
     * @return bool
     */
    protected function isType($val)
    {
        return is_string($val);
    }
}