<?php

namespace BLC\Model;

use DateTime;
use JsonSerializable;
use LogicException;
use Prophecy\Exception\InvalidArgumentException;

/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/17/15
 * Time: 4:14 PM
 */
final class OptionalDate extends Optional
{
    /**
     * @return OptionalDate
     */
    public static function emptyOption()
    {
        return new OptionalDate(null);
    }

    protected function type($val)
    {
        return $val instanceof DateTime;
    }
}