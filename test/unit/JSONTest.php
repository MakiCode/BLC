<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/28/15
 * Time: 4:26 PM
 */

namespace BLC;


use InvalidArgumentException;

/**
 * Class JSONTest
 * @package BLC
 * @uses BLC\JSON
 */
class JSONTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers BLC\JSON::__construct
     * @covers BLC\JSON::getJSON
     */
    public function testJSONGood()
    {
        $JSON = new JSON('{"A":"B"}');
        $jsonArray = $JSON->getJSON();
        $this->assertThat($jsonArray->A, $this->equalTo("B"));
    }

    /**
     * @expectedException InvalidArgumentException
     * @covers BLC\JSON::__construct
     */
    public function testBadJSON()
    {
        new JSON("{A:B}");
    }

    /**
     * @covers \BLC\JSON::cleanString
     */
    public function testCleanStringBasic()
    {
        $this->assertThat(JSON::cleanString("ABC"), $this->equalTo("ABC"));
    }

    /**
     * @covers \BLC\JSON::cleanString
     */
    public function testCleanStringFirst31()
    {
        $base = "ABC";
        for ($i = 0; $i <= 31; ++$i) {
            $this->assertThat(JSON::cleanString($this->concat($i, $base)), $this->equalTo($base . $base));
        }
    }

    /**
     * @covers \BLC\JSON::cleanString
     */
    public function testCleanString127()
    {
        $base = "ABC";
        $this->assertThat(JSON::cleanString($this->concat(127, $base)), $this->equalTo($base . $base));
    }

    /**
     * @covers \BLC\JSON::cleanString
     */
    public function testEFBBF()
    {
        $BOM = chr(239) . chr(187) . chr(191) . "ABC";
        $this->assertThat(JSON::cleanString($BOM), $this->equalTo("ABC"));
    }

    /**
     * @covers \BLC\JSON::__construct
     */
    public function testNonString()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        new JSON([]);
    }

    /**
     * @covers \BLC\JSON::cleanString
     */
    public function testNonStringCleanString()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        JSON::cleanString([]);
    }

    /**
     * @param $i
     * @param $base
     * @return string
     */
    private function concat($i, $base)
    {
        return chr($i) . $base . chr($i) . chr($i) . $base . chr($i);
    }
}
