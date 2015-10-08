<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/29/15
 * Time: 7:25 PM
 */

namespace BLC\Model;


use Types\Primitive;

class NumericString extends Primitive
{

    /**
     * @return bool
     */
    protected function type($val)
    {
        return is_string($val) && is_numeric($val);
    }
}