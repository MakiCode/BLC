<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/15/15
 * Time: 5:49 PM
 */

namespace BLC;


use \InvalidArgumentException;
use stdClass;

final class JSON
{
    /**
     * @var stdClass
     */
    private $json;

    /**
     * JSON constructor.
     * @param string $json A valid JSON string
     */
    public function __construct($json)
    {
        if(!is_string($json)) {
            throw new InvalidArgumentException('$json must be a string');
        }
        $this->json = json_decode(self::cleanString($json));
        $error = json_last_error();
        if($error != JSON_ERROR_NONE) {
            throw new InvalidArgumentException("Must pass a valid JSON string, JSON error: " . json_last_error_msg());
        }
    }

    /**
     * @param string $json
     * @return string
     */
    public static function cleanString($json) {
        if(!is_string($json)) {
            throw new InvalidArgumentException("Can only clean strings");
        }

        for ($i = 0; $i <= 31; ++$i) {
           $json  = str_replace(chr($i), "", $json);
        }
        $json = str_replace(chr(127), "", $json);

        // This is the most common part
        // Some file begins with 'efbbbf' to mark the beginning of the file. (binary level)
        // here we detect it and we remove it, basically it's the first 3 characters
        if (0 === strpos(bin2hex($json), 'efbbbf')) {
            $json = substr($json, 3);
        }
        return $json;
    }

    /**
     * @return stdClass
     */
    public function getJSON()
    {
        return $this->json;
    }
}