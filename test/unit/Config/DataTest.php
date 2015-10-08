<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/4/15
 * Time: 1:02 PM
 */

namespace BLC\Config;


use BLC\JSON;
use BLC\Model\Investment;
use BLC\Model\NumericString;
use BLC\Model\OptionalDate;
use InvalidArgumentException;
use Monolog\Logger;
use BLC\Model\Loan;
use Types\Integer;
use Types\Numeric;
use Types\String;

/**
 * Class DataTest
 * @package BLC\Config
 * @uses BLC\Config\Data
 * @uses BLC\JSON
 * @uses ::\BLC\validateDate()
 * @uses ::\BLC\convertToInvestment()
 * @uses \Types\String
 * @uses \Types\Integer
 * @uses BLC\Model\OptionalDate
 * @uses BLC\Model\NumericString
 * @uses BLC\Model\Investment
 * @uses BLC\Model\Optional
 * @uses ::BLC\isValidOptionalDate
 * @uses ::BLC\dateConversionHelper
 * @uses ::BLC\convertOptionalDate
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Logger
     */
    private $logger;

    public function setUp()
    {
        date_default_timezone_set("America/Mexico_City");
        $this->logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @covers BLC\Config\Data::jsonSerialize()
     * @covers BLC\Config\Data::normalizeData()
     * @covers BLC\Config\Data::__construct()
     */
    public function testBasic()
    {
        $json = <<<TAG
        {
  "lastBorrower": "",
  "cache": {
    "5678": {
      "investment": {
        "amount": "0.6",
        "rate": "12",
        "id": 1234,
        "loanId": 5678,
        "dateInvested": "2016-09-04T13:27:13-0500",
        "investorId": 234
      },
      "dateModified": "05/06/2017"
    },
    "91011": {
      "investment": {
        "amount": "0.7",
        "rate": "13",
        "id": 4567,
        "loanId": 91011,
        "dateInvested": "2017-09-04T13:27:13-0500",
         "investorId": 567
      },
      "dateModified": "05/06/2018"
    }
  }
}
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $this->assertThat(json_encode($data->jsonSerialize()), $this->equalTo(json_encode(json_decode($json))));
    }

    /**
     * @covers BLC\Config\Data::normalizeData()
     */
    public function testNoDataBasic()
    {
        $json = <<<TAG
        true
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "";
        $expected->cache = new \stdClass();
        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }


    /**
     * @covers BLC\Config\Data::normalizeData()
     */
    public function testNoLastBorrower()
    {
        $json = <<<TAG
        {}
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "";
        $expected->cache = new \stdClass();
        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::normalizeData()
     */
    public function testNoCache()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a"
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();
        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::normalizeData()
     */
    public function testBadLastBorrower()
    {
        $json = <<<TAG
        {
         "lastBorrower": {}
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "";
        $expected->cache = new \stdClass();
        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::normalizeData()
     */
    public function testBadCache()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a",
            "cache":[]
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();
        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::validateCache()
     */
    public function testBadKeyInvestmentCache()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a",
            "cache":{
                "a" : {}
            }
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();
        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::validateCache()
     */
    public function testBadValueInvestmentCache()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a",
            "cache":{
                "1" : "a"
            }
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();
        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::validateCache()
     */
    public function testNoInvestmentInCache()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a",
            "cache":{
                "1" : {}
            }
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();
        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::validateInvestment()
     */
    public function testBadInvestmentNoAmountInCache()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a",
            "cache":{
                "1" : {
                    "investment": {

                        "rate": "12",
                        "id": 1234,
                        "loanId": 5678,
                        "dateInvested": "2016-09-04T13:27:13-0500",
                        "investorId": 234
                    }
                }
            }
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();
        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::validateInvestment()
     */
    public function testBadInvestmentNoRateInCache()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a",
            "cache":{
                "1" : {
                    "investment": {
                        "amount": "0.6",

                        "id": 1234,
                        "loanId": 5678,
                        "dateInvested": "2016-09-04T13:27:13-0500",
                        "investorId": 234
                    }
                }
            }
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();
        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::validateInvestment()
     */
    public function testBadInvestmentNoIDInCache()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a",
            "cache":{
                "1" : {
                    "investment": {
                        "amount": "0.6",
                        "rate": "12",
                        "loanId": 5678,
                        "dateInvested": "2016-09-04T13:27:13-0500",
                        "investorId": 234
                    }
                }
            }
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();
        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::validateInvestment()
     */
    public function testBadInvestmentNoLoanIDInCache()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a",
            "cache":{
                "1" : {
                    "investment": {
                        "amount": "0.6",
                        "rate": "12",
                        "id": 1234,
                        "dateInvested": "2016-09-04T13:27:13-0500",
                        "investorId": 234
                    }
                }
            }
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();
        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::validateInvestment()
     */
    public function testBadInvestmentNoDateInvestedInCache()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a",
            "cache":{
                "1" : {
                    "investment": {
                        "amount": "0.6",
                        "rate": "12",
                        "id": 1234,
                        "loanId": 5678,
                        "investorId": 234
                    }
                }
            }
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();
        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::validateInvestment()
     */
    public function testBadInvestmentNoInvestorID()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a",
            "cache":{
                "1" : {
                    "investment": {
                        "amount": "0.6",
                        "rate": "12",
                        "id": 1234,
                        "loanId": 5678,
                        "dateInvested": "2016-09-04T13:27:13-0500"
                    }
                }
            }
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();
        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::validateInvestment()
     */
    public function testBadInvestmentBadAmountInCache()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a",
            "cache":{
                "1" : {
                    "investment": {
                        "amount": "a",
                        "rate": "12",
                        "id": 1234,
                        "loanId": 5678,
                        "dateInvested": "2016-09-04T13:27:13-0500",
                        "investorId": 234
                    }
                }
            }
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();
        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::validateInvestment()
     */
    public function testBadInvestmentBadRateInCache()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a",
            "cache":{
                "1" : {
                    "investment": {
                        "amount": "0.6",
                        "rate": "A",
                        "id": 1234,
                        "loanId": 5678,
                        "dateInvested": "2016-09-04T13:27:13-0500",
                        "investorId": 234
                    }
                }
            }
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();
        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::validateInvestment()
     * @covers BLC\Config\Data::validateCache()
     */
    public function testBadInvestmentBadInCache()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a",
            "cache":{
                "1" : {
                    "investment": {
                        "amount": "0.6",
                        "rate": "12",
                        "id": "1234",
                        "loanId": 5678,
                        "dateInvested": "2016-09-04T13:27:13-0500",
                        "investorId": 234
                    }
                }
            }
        }
TAG;
        $now = new \DateTime();
        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();
        $expected->cache->{1} = new \stdClass();
        $expected->cache->{1}->investment = new \stdClass();
        $expected->cache->{1}->investment->amount = "0.6";
        $expected->cache->{1}->investment->rate = "12";
        $expected->cache->{1}->investment->id = "1234";
        $expected->cache->{1}->investment->loanId = 5678;
        $expected->cache->{1}->investment->dateInvested = "2016-09-04T13:27:13-0500";
        $expected->cache->{1}->investment->investorId = 234;
        $expected->cache->{1}->dateModified = $now->format("d/m/Y");


        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::validateInvestment()
     */
    public function testBadInvestmentBadLoanIDInCache()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a",
            "cache":{
                "1" : {
                    "investment": {
                        "amount": "0.6",
                        "rate": "12",
                        "id": {},
                        "loanId": "5678",
                        "dateInvested": "2016-09-04T13:27:13-0500",
                        "investorId": 234
                    }
                }
            }
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();
        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::validateInvestment()
     */
    public function testBadInvestmentBadDateInvestedInCache()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a",
            "cache":{
                "1" : {
                    "investment": {
                        "amount": "0.6",
                        "rate": "12",
                        "id": 1234,
                        "loanId": 5678,
                        "dateInvested": "a",
                        "investorId": 234
                    }
                }
            }
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();
        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::validateInvestment()
     */
    public function testGoodInvestmentNullDateInvestedInCache()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a",
            "cache":{
                "1" : {
                    "investment": {
                        "amount": "0.6",
                        "rate": "12",
                        "id": 1234,
                        "loanId": 5678,
                        "dateInvested": null,
                        "investorId": 234
                    }
                }
            }
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $today = new \DateTime();
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();
        $expected->cache->{1} = new \stdClass();
        $expected->cache->{1}->investment = new \stdClass();
        $expected->cache->{1}->investment->amount = "0.6";
        $expected->cache->{1}->investment->rate = "12";
        $expected->cache->{1}->investment->id = 1234;
        $expected->cache->{1}->investment->loanId = 5678;
        $expected->cache->{1}->investment->dateInvested = null;
        $expected->cache->{1}->investment->investorId = 234;
        $expected->cache->{1}->dateModified = $today->format("d/m/Y");


        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::validateInvestment()
     */
    public function testBadInvestmentBadInvestorID()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a",
            "cache":{
                "1" : {
                    "investment": {
                        "amount": "0.6",
                        "rate": "12",
                        "id": 1234,
                        "loanId": 5678,
                        "dateInvested": "2016-09-04T13:27:13-0500",
                        "investorId": {}
                    }
                }
            }
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();
        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::validateCache()
     */
    public function testNoDateModified()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a",
            "cache":{
                "1" : {
                    "investment": {
                        "amount": "0.6",
                        "rate": "12",
                        "id": 1234,
                        "loanId": 5678,
                        "dateInvested": null,
                        "investorId": 234
                    }
                }
            }
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $today = new \DateTime();
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();
        $expected->cache->{1} = new \stdClass();
        $expected->cache->{1}->investment = new \stdClass();
        $expected->cache->{1}->investment->amount = "0.6";
        $expected->cache->{1}->investment->rate = "12";
        $expected->cache->{1}->investment->id = 1234;
        $expected->cache->{1}->investment->loanId = 5678;
        $expected->cache->{1}->investment->dateInvested = null;
        $expected->cache->{1}->investment->investorId = 234;
        $expected->cache->{1}->dateModified = $today->format("d/m/Y");


        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::validateCache()
     */
    public function testBadDateModified()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a",
            "cache":{
                "1" : {
                    "investment": {
                        "amount": "0.6",
                        "rate": "12",
                        "id": 1234,
                        "loanId": 5678,
                        "dateInvested": null,
                        "investorId": 234
                    },
                    "dateModified":1234
                }
            }
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $today = new \DateTime();
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();
        $expected->cache->{1} = new \stdClass();
        $expected->cache->{1}->investment = new \stdClass();
        $expected->cache->{1}->investment->amount = "0.6";
        $expected->cache->{1}->investment->rate = "12";
        $expected->cache->{1}->investment->id = 1234;
        $expected->cache->{1}->investment->loanId = 5678;
        $expected->cache->{1}->investment->dateInvested = null;
        $expected->cache->{1}->investment->investorId = 234;
        $expected->cache->{1}->dateModified = $today->format("d/m/Y");


        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }

    /**
     * @covers BLC\Config\Data::validateCache()
     */
    public function testOutOfDate()
    {
        $json = <<<TAG
        {
            "lastBorrower":"a",
            "cache":{
                "1" : {
                    "investment": {
                        "amount": "0.6",
                        "rate": "12",
                        "id": 1234,
                        "loanId": 5678,
                        "dateInvested": null,
                        "investorId": 234
                    },
                    "dateModified":"01/02/2000"
                }
            }
        }
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $expected = new \stdClass();
        $expected->lastBorrower = "a";
        $expected->cache = new \stdClass();

        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }


    /**
     * @covers BLC\Config\Data::getLastBorrowerSHA1()
     */
    public function testGetLastBorrowerSHA1()
    {
        $json = <<<TAG
         {
  "lastBorrower": "asd",
  "cache": {
    "5678": {
      "investment": {
        "amount": "0.6",
        "rate": "12",
        "id": 1234,
        "loanId": 5678,
        "dateInvested": "2016-09-04T13:27:13-0500",
        "investorId": 234
      },
      "dateModified": "05/06/2017"
    },
    "91011": {
      "investment": {
        "amount": "0.7",
        "rate": "13",
        "id": 4567,
        "loanId": 91011,
        "dateInvested": "2017-09-04T13:27:13-0500",
         "investorId": 567
      },
      "dateModified": "05/06/2018"
    }
  }
}
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $this->assertThat($data->getLastBorrowerSHA1(), $this->equalTo("asd"));
    }

    /**
     * @covers BLC\Config\Data::setLastBorrowerSHA1()
     */
    public function testSetLastBorrowerSHA1()
    {
        $json = <<<TAG
         {
  "lastBorrower": "asd",
  "cache": {
    "5678": {
      "investment": {
        "amount": "0.6",
        "rate": "12",
        "id": 1234,
        "loanId": 5678,
        "dateInvested": "2016-09-04T13:27:13-0500",
        "investorId": 234
      },
      "dateModified": "05/06/2017"
    },
    "91011": {
      "investment": {
        "amount": "0.7",
        "rate": "13",
        "id": 4567,
        "loanId": 91011,
        "dateInvested": "2017-09-04T13:27:13-0500",
         "investorId": 567
      },
      "dateModified": "05/06/2018"
    }
  }
}
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $data->setLastBorrowerSHA1(new String("ABC"));
        $actual = $data->jsonSerialize();
        $this->assertThat($actual->lastBorrower, $this->equalTo("ABC"));
    }


    /**
     * @covers BLC\Config\Data::haveInvested()
     */
    public function testHaveInvested()
    {
        $json = <<<TAG
         {
  "lastBorrower": "asd",
  "cache": {
    "5678": {
      "investment": {
        "amount": "0.6",
        "rate": "12",
        "id": 1234,
        "loanId": 5678,
        "dateInvested": "2016-09-04T13:27:13-0500",
        "investorId": 234
      },
      "dateModified": "05/06/2017"
    },
    "91011": {
      "investment": {
        "amount": "0.7",
        "rate": "13",
        "id": 4567,
        "loanId": 91011,
        "dateInvested": "2017-09-04T13:27:13-0500",
         "investorId": 567
      },
      "dateModified": "05/06/2018"
    }
  }
}
TAG;

        $data = new Data(new JSON($json), $this->logger);

        $loan = $this->getMockBuilder(Loan::class)->disableOriginalConstructor()->getMock();
        $loan->method("getId")->will($this->returnValue(new Integer(5678)));

        $this->assertThat($data->haveInvested($loan), $this->equalTo(true));
    }

    /**
     * @covers BLC\Config\Data::haveInvestedId
     */
    public function testHaveInvestedId()
    {
        $json = <<<TAG
         {
  "lastBorrower": "asd",
  "cache": {
    "5678": {
      "investment": {
        "amount": "0.6",
        "rate": "12",
        "id": 1234,
        "loanId": 5678,
        "dateInvested": "2016-09-04T13:27:13-0500",
        "investorId": 234
      },
      "dateModified": "05/06/2017"
    },
    "91011": {
      "investment": {
        "amount": "0.7",
        "rate": "13",
        "id": 4567,
        "loanId": 91011,
        "dateInvested": "2017-09-04T13:27:13-0500",
         "investorId": 567
      },
      "dateModified": "05/06/2018"
    }
  }
}
TAG;

        $data = new Data(new JSON($json), $this->logger);

        $this->assertThat($data->haveInvestedId(new Numeric(5678)), $this->equalTo(true));
    }

    /**
     * @covers BLC\Config\Data::haveInvested()
     */
    public function testHaveInvestedFalse()
    {
        $json = <<<TAG
         {
  "lastBorrower": "asd",
  "cache": {
    "5678": {
      "investment": {
        "amount": "0.6",
        "rate": "12",
        "id": 1234,
        "loanId": 5678,
        "dateInvested": "2016-09-04T13:27:13-0500",
        "investorId": 234
      },
      "dateModified": "05/06/2017"
    },
    "91011": {
      "investment": {
        "amount": "0.7",
        "rate": "13",
        "id": 4567,
        "loanId": 91011,
        "dateInvested": "2017-09-04T13:27:13-0500",
         "investorId": 567
      },
      "dateModified": "05/06/2018"
    }
  }
}
TAG;

        $data = new Data(new JSON($json), $this->logger);

        $loan = $this->getMockBuilder(Loan::class)->disableOriginalConstructor()->getMock();
        $loan->method("getId")->will($this->returnValue(new Integer(1)));

        $this->assertThat($data->haveInvested($loan), $this->equalTo(false));
    }

    /**
     * @covers BLC\Config\Data::haveInvestedId
     */
    public function testHaveInvestedIdFalse()
    {
        $json = <<<TAG
         {
  "lastBorrower": "asd",
  "cache": {
    "5678": {
      "investment": {
        "amount": "0.6",
        "rate": "12",
        "id": 1234,
        "loanId": 5678,
        "dateInvested": "2016-09-04T13:27:13-0500",
        "investorId": 234
      },
      "dateModified": "05/06/2017"
    },
    "91011": {
      "investment": {
        "amount": "0.7",
        "rate": "13",
        "id": 4567,
        "loanId": 91011,
        "dateInvested": "2017-09-04T13:27:13-0500",
         "investorId": 567
      },
      "dateModified": "05/06/2018"
    }
  }
}
TAG;

        $data = new Data(new JSON($json), $this->logger);

        $this->assertThat($data->haveInvestedId(new Numeric(1)), $this->equalTo(false));
    }

    /**
     * @covers BLC\Config\Data::didInvest()
     */
    public function testDidInvest()
    {
        $json = <<<TAG
         {
  "lastBorrower": "asd",
  "cache": {
    "5678": {
      "investment": {
        "amount": "0.6",
        "rate": "12",
        "id": 1234,
        "loanId": 5678,
        "dateInvested": "2016-09-04T13:27:13-0500",
        "investorId": 234
      },
      "dateModified": "05/06/2017"
    },
    "91011": {
      "investment": {
        "amount": "0.7",
        "rate": "13",
        "id": 4567,
        "loanId": 91011,
        "dateInvested": "2017-09-04T13:27:13-0500",
         "investorId": 567
      },
      "dateModified": "05/06/2018"
    }
  }
}
TAG;

        $data = new Data(new JSON($json), $this->logger);

        $now = new \DateTime();
        $investment = new Investment(
            new NumericString("111"),
            new NumericString("222"),
            new Numeric(8888),
            new Numeric(9999),
            new OptionalDate($now),
            new Numeric(10101010)
        );

        $data->didInvest($investment);

        $expected = json_decode($json);
        $expected->cache->{9999} = new \stdClass();
        $expected->cache->{9999}->investment = $investment->jsonSerialize();
        $expected->cache->{9999}->dateModified = $now->format("d/m/Y");

        $this->assertThat($data->jsonSerialize(), $this->equalTo($expected));
    }


    /**
     * @covers BLC\Config\Data::getInvestment()
     */
    public function testGetInvestment()
    {
        $json = <<<TAG
         {
  "lastBorrower": "asd",
  "cache": {
    "5678": {
      "investment": {
        "amount": "0.6",
        "rate": "12",
        "id": 1234,
        "loanId": 5678,
        "dateInvested": "2016-09-04T13:27:13-0500",
        "investorId": 234
      },
      "dateModified": "05/06/2017"
    },
    "91011": {
      "investment": {
        "amount": "0.7",
        "rate": "13",
        "id": 4567,
        "loanId": 91011,
        "dateInvested": "2017-09-04T13:27:13-0500",
         "investorId": 567
      },
      "dateModified": "05/06/2018"
    }
  }
}
TAG;

        $data = new Data(new JSON($json), $this->logger);

        $loan = $this->getMockBuilder(Loan::class)->disableOriginalConstructor()->getMock();
        $loan->method("getId")->will($this->returnValue(new Numeric(5678)));

        $investment = new Investment(new NumericString("0.6"),
            new NumericString("12"),
            new Numeric(1234),
            new Numeric(5678),
            new OptionalDate(\DateTime::createFromFormat(DATE_ISO8601, "2016-09-04T13:27:13-0500")),
            new Numeric(234)
        );

        $this->assertThat($data->getInvestment($loan), $this->equalTo($investment));
    }

    /**
     * @covers BLC\Config\Data::getInvestmentId
     */
    public function testGetInvestmentId()
    {
        $json = <<<TAG
         {
  "lastBorrower": "asd",
  "cache": {
    "5678": {
      "investment": {
        "amount": "0.6",
        "rate": "12",
        "id": 1234,
        "loanId": 5678,
        "dateInvested": "2016-09-04T13:27:13-0500",
        "investorId": 234
      },
      "dateModified": "05/06/2017"
    },
    "91011": {
      "investment": {
        "amount": "0.7",
        "rate": "13",
        "id": 4567,
        "loanId": 91011,
        "dateInvested": "2017-09-04T13:27:13-0500",
         "investorId": 567
      },
      "dateModified": "05/06/2018"
    }
  }
}
TAG;

        $data = new Data(new JSON($json), $this->logger);

//        $loan = $this->getMockBuilder(Loan::class)->disableOriginalConstructor()->getMock();
//        $loan->method("getId")->will($this->returnValue(new Integer(5678)));

        $investment = new Investment(new NumericString("0.6"),
            new NumericString("12"),
            new Numeric(1234),
            new Numeric(5678),
            new OptionalDate(\DateTime::createFromFormat(DATE_ISO8601, "2016-09-04T13:27:13-0500")),
            new Numeric(234)
        );

        $this->assertThat($data->getInvestmentId(new Numeric(5678)), $this->equalTo($investment));
    }

    /**
     * @covers BLC\Config\Data::getInvestment()
     */
    public function testGetInvestmentFail()
    {
        $json = <<<TAG
         {
  "lastBorrower": "asd",
  "cache": {
    "5678": {
      "investment": {
        "amount": "0.6",
        "rate": "12",
        "id": 1234,
        "loanId": 5678,
        "dateInvested": "2016-09-04T13:27:13-0500",
        "investorId": 234
      },
      "dateModified": "05/06/2017"
    },
    "91011": {
      "investment": {
        "amount": "0.7",
        "rate": "13",
        "id": 4567,
        "loanId": 91011,
        "dateInvested": "2017-09-04T13:27:13-0500",
         "investorId": 567
      },
      "dateModified": "05/06/2018"
    }
  }
}
TAG;

        $data = new Data(new JSON($json), $this->logger);

        $loan = $this->getMockBuilder(Loan::class)->disableOriginalConstructor()->getMock();
        $loan->method("getId")->will($this->returnValue(new Numeric(1)));

        $this->setExpectedException(InvalidArgumentException::class);
        $data->getInvestment($loan);
    }

    /**
     * @covers BLC\Config\Data::getInvestmentId
     */
    public function testGetInvestmentIdFail()
    {
        $json = <<<TAG
         {
  "lastBorrower": "asd",
  "cache": {
    "5678": {
      "investment": {
        "amount": "0.6",
        "rate": "12",
        "id": 1234,
        "loanId": 5678,
        "dateInvested": "2016-09-04T13:27:13-0500",
        "investorId": 234
      },
      "dateModified": "05/06/2017"
    },
    "91011": {
      "investment": {
        "amount": "0.7",
        "rate": "13",
        "id": 4567,
        "loanId": 91011,
        "dateInvested": "2017-09-04T13:27:13-0500",
         "investorId": 567
      },
      "dateModified": "05/06/2018"
    }
  }
}
TAG;

        $data = new Data(new JSON($json), $this->logger);

//        $loan = $this->getMockBuilder(Loan::class)->disableOriginalConstructor()->getMock();
//        $loan->method("getId")->will($this->returnValue(new Integer(5678)));


        $this->setExpectedException(InvalidArgumentException::class);
        $data->getInvestmentId(new Numeric(0));
    }

    /**
     * @covers BLC\Config\Data::removeInvestment()
     */
    public function testRemoveInvestment()
    {
        $json = <<<TAG
         {
  "lastBorrower": "asd",
  "cache": {
    "5678": {
      "investment": {
        "amount": "0.6",
        "rate": "12",
        "id": 1234,
        "loanId": 5678,
        "dateInvested": "2016-09-04T13:27:13-0500",
        "investorId": 234
      },
      "dateModified": "05/06/2017"
    },
    "91011": {
      "investment": {
        "amount": "0.7",
        "rate": "13",
        "id": 4567,
        "loanId": 91011,
        "dateInvested": "2017-09-04T13:27:13-0500",
         "investorId": 567
      },
      "dateModified": "05/06/2018"
    }
  }
}
TAG;

        $resultJSON = <<<TAG2
         {
  "lastBorrower": "asd",
  "cache": {
    "91011": {
      "investment": {
        "amount": "0.7",
        "rate": "13",
        "id": 4567,
        "loanId": 91011,
        "dateInvested": "2017-09-04T13:27:13-0500",
         "investorId": 567
      },
      "dateModified": "05/06/2018"
    }
  }
}
TAG2;

        $data = new Data(new JSON($json), $this->logger);

        $investment = new Investment(new NumericString("0.6"),
            new NumericString("12"),
            new Numeric(1234),
            new Numeric(5678),
            new OptionalDate(\DateTime::createFromFormat(DATE_ISO8601, "2016-09-04T13:27:13-0500")),
            new Numeric(234)
        );

        $data->removeInvestment($investment);

        $this->assertThat($data->jsonSerialize(), $this->equalTo(json_decode($resultJSON)));
    }


    /**
     * @covers BLC\Config\Data::validateCache
     */
    public function testOneBadInvestment()
    {
        $json = <<<TAG
        {
  "lastBorrower": "",
  "cache": {
    "5678": {
      "investment": {
        "amount": "0.6",
        "rate": "12",
        "id": 1234,
        "loanId": 5678,
        "dateInvested": "2016-09-04T13:27:13-0500",
        "investorId": 234
      },
      "dateModified": "05/06/2017"
    },
    "91011": {
      "investment": {
      },
      "dateModified": "05/06/2018"
    }
  }
}
TAG;

        $data = new Data(new JSON($json), $this->logger);
        $result = json_decode($json);
        unset($result->cache->{91011});
        $this->assertThat($data->jsonSerialize(), $this->equalTo($result));
    }
}
