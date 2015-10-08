<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/24/15
 * Time: 1:14 PM
 */

namespace BLC\Config;


use BLC\JSON;
use BLC\Model\IntegerList;
use BLC\Model\IntegerStringMap;
use InvalidArgumentException;
use Monolog\Logger;
use PHPUnit_Framework_TestCase;
use Types\Integer;

/**
 * Class ConfigTest
 * @package BLC\Config
 * @uses Types\Primitive
 * @uses Types\Integer
 * @uses BLC\Model\IntegerList
 * @uses Types\TypedList
 * @uses BLC\Config\Config
 * @uses Types\TypedMap
 * @uses BLC\Model\IntegerStringMap
 * @uses BLC\JSON
 * @uses BLC\Config\Config
 */
class ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var JSON
     */
    private $data;

    /**
     * @var Logger
     */
    private $logger;

    public function setUp()
    {
        $this->logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $string = <<<'JSON'
{
  "APIKey": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvYXBpLmJpdGxlbmRpbmdjbHViLmNvbVwvYXBpXC90b2tlbiIsInN1YiI6IjE5NTQ5IiwiaWF0IjoxNDM5MzEyNTM3LCJleHAiOjE0NDE5MDQ1Mzd9.EgRAsFcuZa1EqBEzaDzld5GpkAjh5EVxy7iIJiVGaDA",
  "scale": 9,
  "version": "1",
  "logFile":"data/log.log",
  "logName":"BLC",
  "cacheFile":"data/data.json",
  "rules": {
    "automaticBorrowers": [
      {
        "borrowerId": "18516",
        "amount": "0.0001"
      },
      {
        "borrowerId": "17629",
        "amount": "0.0002"
      },
      {
        "borrowerId": "18945",
        "amount": "0.0003"
      }
    ],
    "reputationBTCLoanAmount": "0.0001",
    "manualInvestments": [
      {
        "loanID": 16625,
        "amount": "0.0001",
        "maxRate": "10"
      },
      {
        "loanID": 16553,
        "amount": "0.0001"
      }
    ]
  }
}
JSON;
        $this->data = new JSON($string);
    }

    public function tearDown()
    {
        $this->data = null;
        $this->logger = null;
    }

    /**
     *
     * @covers \BLC\Config\Config::getBorrowersList
     */
    public function testBasicList()
    {
        $config = new Config($this->data, $this->logger);
        $result = $config->getBorrowersList();
        $expected = new IntegerList(18516, 17629, 18945);
        $this->assertThat($result, $this->equalTo($expected));
    }

    /**
     * @covers \BLC\Config\Config::getBorrowersMap
     */
    public function testBasicMap()
    {
        $config = new Config($this->data, $this->logger);
        $result = $config->getBorrowersMap();
        $expected = new IntegerStringMap([
            18516 => "0.0001",
            17629 => "0.0002",
            18945 => "0.0003"
        ]);
        $this->assertThat($result, $this->equalTo($expected));
    }

    /**
     * @covers \BLC\Config\Config::validateConfig
     */
    public function testNoAPIKey()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        new Config(new JSON("{}"), $this->logger);
    }

    /**
     * @covers \BLC\Config\Config::validateConfig
     */
    public function testNoScale()
    {
        $config = new Config(new JSON('{"APIKey": "w"}'), $this->logger);
        $this->assertThat($config->getScale(), $this->equalTo(5));
    }

    /**
     * @covers \BLC\Config\Config::validateConfig
     */
    public function testNoVersion()
    {
        $config = new Config(new JSON('{"APIKey": "w", "scale": 9}'), $this->logger);
        $this->assertThat($config->getVersion(), $this->equalTo(-1));
    }

    /**
     * @covers \BLC\Config\Config::validateConfig
     */
    public function testNoCacheFile()
    {
        $config = new Config(new JSON('{"APIKey": "w", "scale": 9, "version":4}'), $this->logger);
        $this->assertThat($config->getCacheFile(), $this->equalTo("data.json"));
    }

    /**
     * @covers \BLC\Config\Config::validateConfig
     */
    public function testNoLogName()
    {
        $config = new Config(new JSON('{"APIKey": "w", "scale": 8, "version":4, "cacheFile":"cache.json"}'), $this->logger);
        $this->assertThat($config->getLogName(), $this->equalTo("BLC"));
    }

    /**
     * @covers \BLC\Config\Config::validateConfig
     */
    public function testNoLogFile()
    {
        $config = new Config(new JSON('{"APIKey": "w", "scale": 8, "version":4, "cacheFile":"cache.json", "logName":"DLC"}'), $this->logger);
        $this->assertThat($config->getLogLocation(), $this->equalTo("BLCScript.log"));
    }

    public function testModifiedValues()
    {
        $config = new Config(new JSON('{"APIKey": "w", "scale": 8, "version":4, "cacheFile":"cache.json", "logName":"DLC", "logFile":"AXM.log"}'), $this->logger);
        $this->assertThat($config->getAPIKey(), $this->equalTo("w"));
        $this->assertThat($config->getScale(), $this->equalTo(8));
        $this->assertThat($config->getVersion(), $this->equalTo(4));
        $this->assertThat($config->getCacheFile(), $this->equalTo("cache.json"));
        $this->assertThat($config->getLogName(), $this->equalTo("DLC"));
        $this->assertThat($config->getLogLocation(), $this->equalTo("AXM.log"));
    }

    /**
     * @covers \BLC\Config\Config::validateConfig
     */
    public function testBadAPIKey()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $config = new Config(new JSON('{"APIKey": []}'), $this->logger);
    }

    /**
     * @covers \BLC\Config\Config::validateConfig
     */
    public function testBadScale()
    {
        $config = new Config(new JSON('{"APIKey": "w", "scale": "a"}'), $this->logger);
        $this->assertThat($config->getScale(), $this->equalTo(5));
    }

    /**
     * @covers \BLC\Config\Config::validateConfig
     */
    public function testBadVersion()
    {
        $config = new Config(new JSON('{"APIKey": "w", "scale": 9, "version":"a"}'), $this->logger);
        $this->assertThat($config->getVersion(), $this->equalTo(-1));
    }

    /**
     * @covers \BLC\Config\Config::validateConfig
     */
    public function testBadCacheFile()
    {
        $config = new Config(new JSON('{"APIKey": "w", "scale": 8, "version":4, "cacheFile":[]}'), $this->logger);
        $this->assertThat($config->getCacheFile(), $this->equalTo("data.json"));
    }

    /**
     * @covers \BLC\Config\Config::validateConfig
     */
    public function testBadLogName()
    {
        $config = new Config(new JSON('{"APIKey": "w", "scale": 8, "version":4, "cacheFile":"cache.json", "logName":[]}'), $this->logger);
        $this->assertThat($config->getLogName(), $this->equalTo("BLC"));
    }

    /**
     * @covers \BLC\Config\Config::validateConfig
     */
    public function testBadLogFile()
    {
        $config = new Config(new JSON('{"APIKey": "w", "scale": 8, "version":4, "cacheFile":"cache.json", "logName":"DLC", "logFile":[]}'), $this->logger);
        $this->assertThat($config->getLogLocation(), $this->equalTo("BLCScript.log"));
    }

    /**
     * @covers \BLC\Config\Config::validateConfig
     */
    public function testNoRules()
    {
        $config = new Config(new JSON('{"APIKey": "w", "scale": 3}'), $this->logger);
        $this->assertThat($config->getBorrowersList(), $this->isEmpty());
    }

    /**
     * @covers \BLC\Config\Config::validateConfig
     */
    public function testBadRules()
    {
        $config = new Config(new JSON('{"APIKey": "w", "scale": 12, "rules" : "AB"}'), $this->logger);
        $this->assertThat($config->getBorrowersList(), $this->isEmpty());
    }

    /**
     * @covers \BLC\Config\Config::validateAutomaticBorrowers
     */
    public function testBadAutomaticBorrowers()
    {
        $config = new Config(new JSON(<<<'TAG'
{
    "APIKey": "w",
    "scale": 8,
    "version":4,
    "cacheFile":"cache.json",
    "logName":"DLC",
    "logFile":"AXM.log",
    "rules": {
      "automaticBorrowers": "a"
    }
}
TAG
        ), $this->logger);
        $this->assertThat($config->getBorrowersList(), $this->isEmpty());
    }

    /**
     * @covers \BLC\Config\Config::validateAutomaticBorrowers
     */
    public function testBadAutomaticBorrowersContents()
    {
        $config = new Config(new JSON(<<<'TAG'
{
    "APIKey": "w",
    "scale": 8,
    "version":4,
    "cacheFile":"cache.json",
    "logName":"DLC",
    "logFile":"AXM.log",
    "rules": {
      "automaticBorrowers": [
        "a",
        {"a": "b"},
        {
          "borrowerId": "18516",
          "amount": "0.0001"
        },
        {
          "borrowerId": "a",
          "amount": "0.0001"
        },
        {
          "borrowerId": "1234",
          "amount": "s"
        }
      ]
    }
}
TAG
        ), $this->logger);
        $item = $config->getBorrowersList()[0];
        $this->assertThat($item, $this->equalTo("18516"));
    }

    /**
     * @covers \BLC\Config\Config::validateManualInvestments
     */
    public function testBadManualInvestments()
    {
        $config = new Config(new JSON(<<<'TAG'
{
    "APIKey": "w",
    "scale": 8,
    "version":4,
    "cacheFile":"cache.json",
    "logName":"DLC",
    "logFile":"AXM.log",
    "rules": {
      "manualInvestments": "a"
    }
}
TAG
        ), $this->logger);
        $this->assertThat($config->getManualInvestments(), $this->isEmpty());
    }


    /**
     * @covers \BLC\Config\Config::validateManualInvestments
     * @covers \BLC\Config\Config::getManualInvestments
     */
    public function testManualInvestmentsBasic()
    {
        $config = new Config(new JSON(<<<'TAG'
{
    "APIKey": "w",
    "scale": 8,
    "version":4,
    "cacheFile":"cache.json",
    "logName":"DLC",
    "logFile":"AXM.log",
    "rules": {
      "manualInvestments": [
        {
         "loanID": 1,
          "amount": "0.001",
          "maxRate": "10"
        },
        {
         "loanID": 2,
         "amount": "0.0002",
         "maxRate":"40"
        }
      ]
    }
}
TAG
        //1, 2, 4, and 7 are the good ones
        ), $this->logger);
        $manualInvestments = $config->getManualInvestments();
        $this->assertThat(count($manualInvestments), $this->equalTo(2));
        $this->assertThat($manualInvestments[0]->loanID, $this->equalTo(1));
        $this->assertThat($manualInvestments[0]->amount, $this->equalTo("0.001"));
        $this->assertThat($manualInvestments[0]->maxRate, $this->equalTo("10"));

        $this->assertThat($manualInvestments[1]->loanID, $this->equalTo(2));
        $this->assertThat($manualInvestments[1]->amount, $this->equalTo("0.0002"));
        $this->assertThat($manualInvestments[1]->maxRate, $this->equalTo("40"));
    }

    /**
     * @covers \BLC\Config\Config::validateManualInvestments
     */
    public function testManualInvestmentNoMaxRate()
    {
        $config = new Config(new JSON(<<<'TAG'
{
    "APIKey": "w",
    "scale": 8,
    "version":4,
    "cacheFile":"cache.json",
    "logName":"DLC",
    "logFile":"AXM.log",
    "rules": {
      "manualInvestments": [
        {
         "loanID": 1,
          "amount": "0.001",
          "maxRate": "10"
        },
        {
         "loanID": 2,
         "amount": "0.0002"
        }
      ]
    }
}
TAG
        ), $this->logger);

        $manualInvestments = $config->getManualInvestments();
        $this->assertThat(count($manualInvestments), $this->equalTo(2));
        $this->assertThat($manualInvestments[0]->loanID, $this->equalTo(1));
        $this->assertThat($manualInvestments[0]->amount, $this->equalTo("0.001"));
        $this->assertThat($manualInvestments[0]->maxRate, $this->equalTo("10"));

        $this->assertThat($manualInvestments[1]->loanID, $this->equalTo(2));
        $this->assertThat($manualInvestments[1]->amount, $this->equalTo("0.0002"));
        $this->assertThat($manualInvestments[1]->maxRate, $this->equalTo("100"));

    }

    /**
     * @covers \BLC\Config\Config::validateManualInvestments
     */
    public function testManualInvestmentPoorlyFormedValues()
    {
        $config = new Config(new JSON(<<<'TAG'
{
    "APIKey": "w",
    "scale": 8,
    "version":4,
    "cacheFile":"cache.json",
    "logName":"DLC",
    "logFile":"AXM.log",
    "rules": {
      "manualInvestments": [
        {"a":"b"},
        {
         "loanID": 1,
          "amount": "0.001",
          "maxRate": "10"
        },
        [],
        {
         "loanID": 2,
         "amount": "0.0002"
        },
        "a"
       ]
    }
}
TAG
        ), $this->logger);

        $manualInvestments = $config->getManualInvestments();
        $this->assertThat(count($manualInvestments), $this->equalTo(2));
        $this->assertThat($manualInvestments[0]->loanID, $this->equalTo(1));
        $this->assertThat($manualInvestments[0]->amount, $this->equalTo("0.001"));
        $this->assertThat($manualInvestments[0]->maxRate, $this->equalTo("10"));

        $this->assertThat($manualInvestments[1]->loanID, $this->equalTo(2));
        $this->assertThat($manualInvestments[1]->amount, $this->equalTo("0.0002"));
        $this->assertThat($manualInvestments[1]->maxRate, $this->equalTo("100"));
    }

    /**
     * @covers \BLC\Config\Config::validateManualInvestments
     */
    public function testManualInvestmentBadLoanID()
    {
        $config = new Config(new JSON(<<<'TAG'
{
    "APIKey": "w",
    "scale": 8,
    "version":4,
    "cacheFile":"cache.json",
    "logName":"DLC",
    "logFile":"AXM.log",
    "rules": {
      "manualInvestments": [
        {
         "loanID": 1,
          "amount": "0.001",
          "maxRate": "10"
        },
        {
         "loanID": [],
          "amount": "0.003",
          "maxRate": "20"
        },
        {
         "loanID": 2,
         "amount": "0.0002"
        }
       ]
    }
}
TAG
        ), $this->logger);

        $manualInvestments = $config->getManualInvestments();
        $this->assertThat(count($manualInvestments), $this->equalTo(2));
        $this->assertThat($manualInvestments[0]->loanID, $this->equalTo(1));
        $this->assertThat($manualInvestments[0]->amount, $this->equalTo("0.001"));
        $this->assertThat($manualInvestments[0]->maxRate, $this->equalTo("10"));

        $this->assertThat($manualInvestments[1]->loanID, $this->equalTo(2));
        $this->assertThat($manualInvestments[1]->amount, $this->equalTo("0.0002"));
        $this->assertThat($manualInvestments[1]->maxRate, $this->equalTo("100"));
    }

    /**
     * @covers \BLC\Config\Config::validateManualInvestments
     */
    public function testManualInvestmentBadAmount()
    {
        $config = new Config(new JSON(<<<'TAG'
{
    "APIKey": "w",
    "scale": 8,
    "version":4,
    "cacheFile":"cache.json",
    "logName":"DLC",
    "logFile":"AXM.log",
    "rules": {
      "manualInvestments": [
        {
         "loanID": 1,
          "amount": "0.001",
          "maxRate": "10"
        },
        {
         "loanID": 3,
          "amount": [],
          "maxRate": "30"
        },
        {
         "loanID": 2,
         "amount": "0.0002"
        }
       ]
    }
}
TAG
        ), $this->logger);

        $manualInvestments = $config->getManualInvestments();
        $this->assertThat(count($manualInvestments), $this->equalTo(2));
        $this->assertThat($manualInvestments[0]->loanID, $this->equalTo(1));
        $this->assertThat($manualInvestments[0]->amount, $this->equalTo("0.001"));
        $this->assertThat($manualInvestments[0]->maxRate, $this->equalTo("10"));

        $this->assertThat($manualInvestments[1]->loanID, $this->equalTo(2));
        $this->assertThat($manualInvestments[1]->amount, $this->equalTo("0.0002"));
        $this->assertThat($manualInvestments[1]->maxRate, $this->equalTo("100"));
    }

    /**
     * @covers \BLC\Config\Config::validateManualInvestments
     */
    public function testManualInvestmentNoLoanIDKey()
    {
        $config = new Config(new JSON(<<<'TAG'
{
    "APIKey": "w",
    "scale": 8,
    "version":4,
    "cacheFile":"cache.json",
    "logName":"DLC",
    "logFile":"AXM.log",
    "rules": {
      "manualInvestments": [
        {
         "loanID": 1,
          "amount": "0.001",
          "maxRate": "10"
        },
        {
         "loanIDA": 5,
          "amount": "0.005",
          "maxRate": "40"
        },
        {
         "loanID": 2,
         "amount": "0.0002"
        }
       ]
    }
}
TAG
        ), $this->logger);

        $manualInvestments = $config->getManualInvestments();
        $this->assertThat(count($manualInvestments), $this->equalTo(2));
        $this->assertThat($manualInvestments[0]->loanID, $this->equalTo(1));
        $this->assertThat($manualInvestments[0]->amount, $this->equalTo("0.001"));
        $this->assertThat($manualInvestments[0]->maxRate, $this->equalTo("10"));

        $this->assertThat($manualInvestments[1]->loanID, $this->equalTo(2));
        $this->assertThat($manualInvestments[1]->amount, $this->equalTo("0.0002"));
        $this->assertThat($manualInvestments[1]->maxRate, $this->equalTo("100"));
    }

    /**
     * @covers \BLC\Config\Config::validateManualInvestments
     */
    public function testManualInvestmentNoAmountKey()
    {
        $config = new Config(new JSON(<<<'TAG'
{
    "APIKey": "w",
    "scale": 8,
    "version":4,
    "cacheFile":"cache.json",
    "logName":"DLC",
    "logFile":"AXM.log",
    "rules": {
      "manualInvestments": [
        {
         "loanID": 1,
          "amount": "0.001",
          "maxRate": "10"
        },
        {
         "loanID": 6,
          "amountA": "0.006",
          "maxRate": "50"
        },
        {
         "loanID": 2,
         "amount": "0.0002"
        }
       ]
    }
}
TAG
        ), $this->logger);

        $manualInvestments = $config->getManualInvestments();
        $this->assertThat(count($manualInvestments), $this->equalTo(2));
        $this->assertThat($manualInvestments[0]->loanID, $this->equalTo(1));
        $this->assertThat($manualInvestments[0]->amount, $this->equalTo("0.001"));
        $this->assertThat($manualInvestments[0]->maxRate, $this->equalTo("10"));

        $this->assertThat($manualInvestments[1]->loanID, $this->equalTo(2));
        $this->assertThat($manualInvestments[1]->amount, $this->equalTo("0.0002"));
        $this->assertThat($manualInvestments[1]->maxRate, $this->equalTo("100"));
    }

    /**
     * @covers \BLC\Config\Config::validateManualInvestments
     */
    public function testManualInvestmentBadMaxRate()
    {
        $config = new Config(new JSON(<<<'TAG'
{
    "APIKey": "w",
    "scale": 8,
    "version":4,
    "cacheFile":"cache.json",
    "logName":"DLC",
    "logFile":"AXM.log",
    "rules": {
      "manualInvestments": [
        {
         "loanID": 1,
          "amount": "0.001",
          "maxRate": "10"
        },
        {
         "loanID": 6,
          "amount": "0.006",
          "maxRate": "asd"
        },
        {
         "loanID": 2,
         "amount": "0.0002"
        }
       ]
    }
}
TAG
        ), $this->logger);

        $manualInvestments = $config->getManualInvestments();
        $this->assertThat(count($manualInvestments), $this->equalTo(3));
        $this->assertThat($manualInvestments[0]->loanID, $this->equalTo(1));
        $this->assertThat($manualInvestments[0]->amount, $this->equalTo("0.001"));
        $this->assertThat($manualInvestments[0]->maxRate, $this->equalTo("10"));

        $this->assertThat($manualInvestments[1]->loanID, $this->equalTo(6));
        $this->assertThat($manualInvestments[1]->amount, $this->equalTo("0.006"));
        $this->assertThat($manualInvestments[1]->maxRate, $this->equalTo("100"));

        $this->assertThat($manualInvestments[2]->loanID, $this->equalTo(2));
        $this->assertThat($manualInvestments[2]->amount, $this->equalTo("0.0002"));
        $this->assertThat($manualInvestments[2]->maxRate, $this->equalTo("100"));
    }

    /**
     * @covers \BLC\Config\Config::validateConfig
     * @covers \BLC\Config\Config::getAutoInvestAmount
     */
    public function testGetAutoInvestment()
    {
        $config = new Config(new JSON(<<<'TAG'
{
    "APIKey": "w",
    "scale": 8,
    "version":4,
    "cacheFile":"cache.json",
    "logName":"DLC",
    "logFile":"AXM.log",
    "rules": {
      "reputationBTCLoanAmount":"1"
    }
}
TAG
        ), $this->logger);

        $autoInvest = $config->getAutoInvestAmount();
        $this->assertThat($autoInvest, $this->equalTo(1));
    }

    /**
     * @covers \BLC\Config\Config::validateConfig
     */
    public function testGetAutoDefaultInvestment()
    {
        $config = new Config(new JSON(<<<'TAG'
{
    "APIKey": "w",
    "scale": 8,
    "version":4,
    "cacheFile":"cache.json",
    "logName":"DLC",
    "logFile":"AXM.log",
    "rules": {
    }
}
TAG
        ), $this->logger);

        $autoInvest = $config->getAutoInvestAmount();
        $this->assertThat($autoInvest, $this->equalTo("0"));
    }

    /**
     * @covers \BLC\Config\Config::validateConfig
     */
    public function testGetAutoBadValue()
    {
        $config = new Config(new JSON(<<<'TAG'
{
    "APIKey": "w",
    "scale": 8,
    "version":4,
    "cacheFile":"cache.json",
    "logName":"DLC",
    "logFile":"AXM.log",
    "rules": {
          "reputationBTCLoanAmount":"a"
    }
}
TAG
        ), $this->logger);

        $autoInvest = $config->getAutoInvestAmount();
        $this->assertThat($autoInvest, $this->equalTo("0"));
    }

    /**
     * @covers \BLC\Config\Config::validateConfig
     */
    public function testGetUnder0()
    {
        $config = new Config(new JSON(<<<'TAG'
{
    "APIKey": "w",
    "scale": 8,
    "version":4,
    "cacheFile":"cache.json",
    "logName":"DLC",
    "logFile":"AXM.log",
    "rules": {
          "reputationBTCLoanAmount":"-1"
    }
}
TAG
        ), $this->logger);

        $autoInvest = $config->getAutoInvestAmount();
        $this->assertThat($autoInvest, $this->equalTo("0"));
    }


}