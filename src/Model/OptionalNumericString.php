<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/2/15
 * Time: 4:02 PM
 */

namespace BLC\Model;



class OptionalNumericString extends Optional
{
    protected function type($val)
    {
        return $val instanceof NumericString;
    }
}