<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/17/15
 * Time: 3:34 PM
 */

namespace BLC\Model;

use Types\TypedList;

final class Loans extends TypedList
{
    /**
     * @param mixed $val
     * @return bool
     */
    protected function isType($val)
    {
        return $val instanceof Loan;
    }
}