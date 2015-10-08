<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/5/15
 * Time: 2:18 PM
 */

namespace BLC;


use BLC\Config\Data;
use BLC\Model\ExclusiveLock;
use BLC\Model\Investment;
use BLC\Model\NumericString;
use BLC\Model\OptionalDate;
use BLC\Model\WorkItem;
use BLC\Model\WorkQueue;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Monolog\Logger;
use Types\Integer;
use Types\Numeric;

/**
 * @uses BLC\Model\Investments
 * @uses BLC\JSON
 * @uses BLC\Model\OptionalDate
 * @uses BLC\Model\NumericString
 * @uses BLC\Model\Investment
 * @uses BLC\Model\Optional
 * @uses ::BLC\weightedAverageRate
 * @uses ::BLC\convertToInvestment
 * @uses ::BLC\getAverageRate
 * @uses ::BLC\balanceInvestment
 * @uses ::BLC\createInvestment
 * @uses ::BLC\consumeQueue
 * @uses ::BLC\bcmax
 * @uses ::BLC\validateInvestmentsResponse
 * @uses ::BLC\checkPostResponse
 * @uses BLC\Model\LoanFactory
 * @uses BLC\Model\OptionalBoolean
 * @uses BLC\Model\Loan
 * @uses BLC\Model\WorkQueue
 * @uses BLC\Model\OptionalNumericString
 * @uses BLC\Model\WorkItem
 * @uses BLC\Model\ExclusiveLock
 * @uses ::BLC\isValidOptionalDate
 * @uses ::BLC\dateConversionHelper
 * @uses ::BLC\convertOptionalDate
 */
class ConsumeQueueFunctionsTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        date_default_timezone_set("America/Mexico_City");
        bcscale(3);
    }

    /**
     * @covers ::BLC\getAverageRate
     */
    public function testGetAverageRate()
    {
        $loanId = new Numeric(5);

        $payload = new \stdClass();
        $investment1 = new \stdClass();
        $investment1->amount = "1";
        $investment1->rate = "2";
        $investment1->id = 5;
        $investment1->dateInvested = "2015-10-05T23:59:59Z";
        $investment1->investorId = 0;
        $investment1->loanId = 999;

        $investment2 = new \stdClass();
        $investment2->amount = "2";
        $investment2->rate = "3";
        $investment2->id = 5;
        $investment2->dateInvested = "2015-09-05T23:59:59Z";
        $investment2->investorId = 0;
        $investment2->loanId = 888;

        $investment3 = new \stdClass();
        $investment3->amount = "4";
        $investment3->rate = "5";
        $investment3->id = 5;
        $investment3->dateInvested = "2015-08-05T23:59:59Z";
        $investment3->investorId = 0;
        $investment3->loanId = 777;


        $payload->investments = [
            $investment1,
            $investment2,
            $investment3
        ];

        $responses = [
            new Response(200, [], json_encode($payload))
        ];

        $handler = HandlerStack::create(new MockHandler($responses));
        $client = new Client(['handler' => $handler]);

        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();


        $averageRate = getAverageRate($loanId, $client, $logger)->wait(true);

        $this->assertThat($averageRate, $this->equalTo("4.000"));
    }

    /**
     * @covers ::BLC\getAverageRate
     */
    public function testGetAverageRateNoInvestments()
    {
        $loanId = new Numeric(5);

        $payload = new \stdClass();

        $responses = [
            new Response(200, [], json_encode($payload))
        ];

        $handler = HandlerStack::create(new MockHandler($responses));
        $client = new Client(['handler' => $handler]);

        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();


        $this->setExpectedException(InvalidArgumentException::class);
        getAverageRate($loanId, $client, $logger)->wait(true);
    }

    /**
     * @covers ::BLC\getAverageRate
     */
    public function testGetAverageRatBadInvestments()
    {
        $loanId = new Numeric(5);

        $payload = new \stdClass();
        $payload->investments = "AVX";

        $responses = [
            new Response(200, [], json_encode($payload))
        ];

        $handler = HandlerStack::create(new MockHandler($responses));
        $client = new Client(['handler' => $handler]);

        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();


        $this->setExpectedException(InvalidArgumentException::class);
        getAverageRate($loanId, $client, $logger)->wait(true);
    }

    /**
     * @covers ::BLC\getAverageRate
     */
    public function testGetAverageRateError()
    {
        $loanId = new Numeric(5);

        $payload = new \stdClass();
        $payload->investments = "AVX";

        $responses = [
            new Response(400, [], json_encode($payload))
        ];

        $handler = HandlerStack::create(new MockHandler($responses));
        $client = new Client(['handler' => $handler]);

        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();


        $this->setExpectedException(InvalidArgumentException::class);
        getAverageRate($loanId, $client, $logger)->wait(true);
    }

    /**
     * @covers ::BLC\getAverageRate
     */
    public function testGetAverageRateEmptyInvestments()
    {
        $loanId = new Numeric(5);

        $payload = new \stdClass();
        $payload->investments = [];

        $responses = [
            new Response(200, [], json_encode($payload))
        ];

        $handler = HandlerStack::create(new MockHandler($responses));
        $client = new Client(['handler' => $handler]);

        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();


        $this->setExpectedException(InvalidArgumentException::class);
        getAverageRate($loanId, $client, $logger)->wait(true);
    }

    /**
     * @covers ::BLC\getAverageRate
     */
    public function testGetAverageRateOneInvestments()
    {
        $loanId = new Numeric(5);

        $payload = new \stdClass();

        $investment1 = new \stdClass();
        $investment1->amount = "1";
        $investment1->rate = "2";
        $investment1->id = 5;
        $investment1->dateInvested = "2015-10-05T23:59:59Z";
        $investment1->investorId = 0;
        $investment1->loanId = 999;

        $payload->investments = [$investment1];

        $responses = [
            new Response(200, [], json_encode($payload))
        ];

        $handler = HandlerStack::create(new MockHandler($responses));
        $client = new Client(['handler' => $handler]);

        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();


        $rate = getAverageRate($loanId, $client, $logger)->wait(true);
        $this->assertThat($rate, $this->equalTo(2));
    }

    /**
     * @covers ::BLC\balanceInvestment
     */
    public function testBalanceInvestment()
    {
        $payload = new \stdClass();

        $investment1 = new \stdClass();
        $investment1->amount = "1";
        $investment1->rate = "2";
        $investment1->id = 5;
        $investment1->dateInvested = "2015-10-05T23:59:59Z";
        $investment1->investorId = 0;
        $investment1->loanId = 999;

        $investment2 = new \stdClass();
        $investment2->amount = "2";
        $investment2->rate = "3";
        $investment2->id = 5;
        $investment2->dateInvested = "2015-09-05T23:59:59Z";
        $investment2->investorId = 0;
        $investment2->loanId = 888;

        $investment3 = new \stdClass();
        $investment3->amount = "4";
        $investment3->rate = "5";
        $investment3->id = 5;
        $investment3->dateInvested = "2015-08-05T23:59:59Z";
        $investment3->investorId = 0;
        $investment3->loanId = 777;


        $payload->investments = [
            $investment1,
            $investment2,
            $investment3
        ];


        $responses = [
            new Response(200, [], json_encode($payload)),
            new Response(200)
        ];

        $container = [];
        $history = Middleware::history($container);

        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push($history);
        $client = new Client(['handler' => $handler]);

        $now = new \DateTime();
        $investment = new Investment(new NumericString("1"), new NumericString("2"), new Numeric(3), new Numeric(4),
            new OptionalDate($now), new Numeric(5));

        $maxRate = new NumericString("100");


        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        balanceInvestment($investment, $maxRate, $client, $logger)->wait();
        $transaction = $container[1];

        /** @var Request $request */
        $request = $transaction['request'];

        $body = $request->getBody()->getContents();

        parse_str($body, $result);

        $this->assertThat($result["rate"], $this->equalTo("4.000"));
    }

    /**
     * @covers ::BLC\balanceInvestment
     */
    public function testBadAverage()
    {

        $payload = new \stdClass();
        $payload->investments = "";


        $responses = [
            new Response(200, [], json_encode($payload)),
            new Response(200)
        ];

        $container = [];
        $history = Middleware::history($container);

        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push($history);
        $client = new Client(['handler' => $handler]);

        $now = new \DateTime();
        $investment = new Investment(new NumericString("1"), new NumericString("2"), new Numeric(3), new Numeric(4),
            new OptionalDate($now), new Numeric(5));

        $maxRate = new NumericString("3");

        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();


        $this->setExpectedException(InvalidArgumentException::class);
        balanceInvestment($investment, $maxRate, $client, $logger);
    }

    /**
     * @covers ::BLC\balanceInvestment
     */
    public function testBalanceError()
    {
        $payload = new \stdClass();

        $investment1 = new \stdClass();
        $investment1->amount = "1";
        $investment1->rate = "2";
        $investment1->id = 5;
        $investment1->dateInvested = "2015-10-05T23:59:59Z";
        $investment1->investorId = 0;
        $investment1->loanId = 999;

        $investment2 = new \stdClass();
        $investment2->amount = "2";
        $investment2->rate = "3";
        $investment2->id = 5;
        $investment2->dateInvested = "2015-09-05T23:59:59Z";
        $investment2->investorId = 0;
        $investment2->loanId = 888;

        $investment3 = new \stdClass();
        $investment3->amount = "4";
        $investment3->rate = "5";
        $investment3->id = 5;
        $investment3->dateInvested = "2015-08-05T23:59:59Z";
        $investment3->investorId = 0;
        $investment3->loanId = 777;


        $payload->investments = [
            $investment1,
            $investment2,
            $investment3
        ];


        $responses = [
            new Response(200, [], json_encode($payload)),
            new Response(400)
        ];

        $container = [];
        $history = Middleware::history($container);

        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push($history);
        $client = new Client(['handler' => $handler]);

        $now = new \DateTime();
        $investment = new Investment(new NumericString("1"), new NumericString("2"), new Numeric(3), new Numeric(4),
            new OptionalDate($now), new Numeric(5));

        $maxRate = new NumericString("3");


        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $this->setExpectedException(RequestException::class);
        balanceInvestment($investment, $maxRate, $client, $logger)->wait();
    }


    /**
     * @covers ::BLC\createInvestment
     */
    public function testCreateInvestment()
    {
        $payload1 = new \stdClass();
        $payload1->id = 7;

        $payload2 = new \stdClass();
        $investment1 = new \stdClass();
        $investment1->amount = "1";
        $investment1->rate = "2";
        $investment1->id = 5;
        $investment1->dateInvested = "2015-10-05T23:59:59Z";
        $investment1->investorId = 0;
        $investment1->loanId = 999;
        $payload2->investments = [
            $investment1
        ];

        $responses = [
            new Response(200, [], json_encode($payload1)),
            new Response(200, [], json_encode($payload2))
        ];

        $container = [];
        $history = Middleware::history($container);

        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push($history);
        $client = new Client(['handler' => $handler]);

        $loan =new Numeric(1);

        $amount = new NumericString("0.01");
        $rate = new NumericString("5");

        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $data = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $data->expects($this->once())->method("didInvest")->with(
            convertToInvestment($investment1)
        );

        createInvestment($loan, $amount, $client, $data, $logger, $rate)->wait();
    }

    /**
     * @covers ::BLC\createInvestment
     */
    public function testCreateInvestmentCalculateMaxRate()
    {
        $payload1 = new \stdClass();

        $investment1 = new \stdClass();
        $investment1->amount = "1";
        $investment1->rate = "2";
        $investment1->id = 5;
        $investment1->dateInvested = "2015-10-05T23:59:59Z";
        $investment1->investorId = 0;
        $investment1->loanId = 999;

        $investment2 = new \stdClass();
        $investment2->amount = "2";
        $investment2->rate = "3";
        $investment2->id = 5;
        $investment2->dateInvested = "2015-09-05T23:59:59Z";
        $investment2->investorId = 0;
        $investment2->loanId = 888;

        $investment3 = new \stdClass();
        $investment3->amount = "4";
        $investment3->rate = "5";
        $investment3->id = 5;
        $investment3->dateInvested = "2015-08-05T23:59:59Z";
        $investment3->investorId = 0;
        $investment3->loanId = 777;
        $payload1->investments = [
            $investment1,
            $investment2,
            $investment3
        ];

        $payload2 = new \stdClass();
        $payload2->id = 7;


        $payload3 = new \stdClass();
        $investment4 = new \stdClass();
        $investment4->amount = "1";
        $investment4->rate = "4.00";
        $investment4->id = 5;
        $investment4->dateInvested = "2015-10-05T23:59:59Z";
        $investment4->investorId = 0;
        $investment4->loanId = 999;
        $payload3->investments = [
            $investment4
        ];

        $responses = [
            new Response(200, [], json_encode($payload1)),
            new Response(200, [], json_encode($payload2)),
            new Response(200, [], json_encode($payload3))
        ];

        $container = [];
        $history = Middleware::history($container);

        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push($history);
        $client = new Client(['handler' => $handler]);

        $loan = new Numeric(1);

        $amount = new NumericString("0.01");

        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $data = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $data->expects($this->once())->method("didInvest")->with(
            convertToInvestment($investment4)
        );

        createInvestment($loan, $amount, $client, $data, $logger, null)->wait();
    }

    /**
     * @covers ::BLC\createInvestment
     */
    public function testCreateInvestmentBadResponse()
    {
        $payload1 = new \stdClass();

        $investment1 = new \stdClass();
        $investment1->amount = "1";
        $investment1->rate = "2";
        $investment1->id = 5;
        $investment1->dateInvested = "2015-10-05T23:59:59Z";
        $investment1->investorId = 0;
        $investment1->loanId = 999;

        $investment2 = new \stdClass();
        $investment2->amount = "2";
        $investment2->rate = "3";
        $investment2->id = 5;
        $investment2->dateInvested = "2015-09-05T23:59:59Z";
        $investment2->investorId = 0;
        $investment2->loanId = 888;

        $investment3 = new \stdClass();
        $investment3->amount = "4";
        $investment3->rate = "5";
        $investment3->id = 5;
        $investment3->dateInvested = "2015-08-05T23:59:59Z";
        $investment3->investorId = 0;
        $investment3->loanId = 777;
        $payload1->investments = [
            $investment1,
            $investment2,
            $investment3
        ];

        $payload2 = new \stdClass();
        $payload2->id = 7;


        $payload3 = new \stdClass();
        $investment4 = new \stdClass();
        $investment4->amount = "1";
        $investment4->rate = "4.00";
        $investment4->id = 5;
        $investment4->dateInvested = "2015-10-05T23:59:59Z";
        $investment4->investorId = 0;
        $investment4->loanId = 999;
        $payload3->investments = [
            $investment4
        ];

        $responses = [
            new Response(200, [], json_encode($payload1)),
            new Response(400, [], json_encode($payload2)),
            new Response(200, [], json_encode($payload3))
        ];

        $container = [];
        $history = Middleware::history($container);

        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push($history);
        $client = new Client(['handler' => $handler]);

        $loan = new Numeric(1);

        $amount = new NumericString("0.01");

        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $data = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();

        $this->setExpectedException(RequestException::class);
        createInvestment($loan, $amount, $client, $data, $logger, null)->wait();
    }


    /**
     * @covers ::BLC\createInvestment
     */
    public function testCreateInvestmentBadResponseToSave()
    {
        $payload1 = new \stdClass();

        $investment1 = new \stdClass();
        $investment1->amount = "1";
        $investment1->rate = "2";
        $investment1->id = 5;
        $investment1->dateInvested = "2015-10-05T23:59:59Z";
        $investment1->investorId = 0;
        $investment1->loanId = 999;

        $investment2 = new \stdClass();
        $investment2->amount = "2";
        $investment2->rate = "3";
        $investment2->id = 5;
        $investment2->dateInvested = "2015-09-05T23:59:59Z";
        $investment2->investorId = 0;
        $investment2->loanId = 888;

        $investment3 = new \stdClass();
        $investment3->amount = "4";
        $investment3->rate = "5";
        $investment3->id = 5;
        $investment3->dateInvested = "2015-08-05T23:59:59Z";
        $investment3->investorId = 0;
        $investment3->loanId = 777;
        $payload1->investments = [
            $investment1,
            $investment2,
            $investment3
        ];

        $payload2 = new \stdClass();
        $payload2->id = 7;


        $payload3 = new \stdClass();
        $investment4 = new \stdClass();
        $investment4->amount = "1";
        $investment4->rate = "4.00";
        $investment4->id = 5;
        $investment4->dateInvested = "2015-10-05T23:59:59Z";
        $investment4->investorId = 0;
        $investment4->loanId = 999;
        $payload3->investments = [
            $investment4
        ];

        $responses = [
            new Response(200, [], json_encode($payload1)),
            new Response(200, [], json_encode($payload2)),
            new Response(400, [], json_encode($payload3))
        ];

        $container = [];
        $history = Middleware::history($container);

        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push($history);
        $client = new Client(['handler' => $handler]);

        $loanId = new Numeric(1);

        $amount = new NumericString("0.01");

        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $data = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();

        $this->setExpectedException(RequestException::class);
        createInvestment($loanId, $amount, $client, $data, $logger, null)->wait();
    }


    /**
     * @covers ::BLC\consumeQueue
     */
    public function testConsumeQueueCreate()
    {
        $payload1 = new \stdClass();
        $investment1 = new \stdClass();
        $investment1->amount = "1";
        $investment1->rate = "2";
        $investment1->id = 5;
        $investment1->dateInvested = "2015-10-05T23:59:59Z";
        $investment1->investorId = 0;
        $investment1->loanId = 999;
        $payload1->investments = [
            $investment1
        ];

        $payload2 = new \stdClass();
        $payload2->id = 1;

        $responses = [
            new Response(200, [], json_encode($payload1)),
            new Response(200, [], json_encode($payload2)),
            new Response(200, [], json_encode($payload2)),
            new Response(200, [], json_encode($payload1)),
            new Response(200, [], json_encode($payload1)),
            new Response(200, [], json_encode($payload1)),
            new Response(200, [], json_encode($payload1)),
            new Response(200, [], json_encode($payload1)),
            new Response(200, [], json_encode($payload1))
        ];


        $container = [];
        $history = Middleware::history($container);

        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push($history);
        $client = new Client(['handler' => $handler]);

        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $logger->expects($this->exactly(3))->method("addNotice");
        $logger->expects($this->exactly(1))->method("addError");

        $queue = new WorkQueue(new ExclusiveLock("test", $logger));
        $queue->enqueue(new WorkItem(new Numeric(2), new NumericString("1")));
        $queue->enqueue(new WorkItem(new Numeric(5), new NumericString("4"), new NumericString("8")));
        $queue->enqueue(new WorkItem(new Numeric(10), new NumericString("11")));
        $queue->enqueue(new WorkItem(new Numeric(12), new NumericString("13"), null, new NumericString("9")));

        $data = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $data->method("haveInvestedId")->will($this->onConsecutiveCalls(false, false, true, true));
        $data->method("getInvestmentId")->will($this->returnValue(convertToInvestment($investment1)));

        consumeQueue($queue, $data, $client, $logger);
    }

    /**
     * @covers ::BLC\createInvestment
     */
    public function testCreateInvestmentAlreadyInvested() {
        $payload1 = new \stdClass();
        $payload1->errors = new \stdClass();
        $payload1->errors->loan_id  =new \stdClass();
        $payload1->errors->loan_id->{"already invested"} = "BLAH BLAH BLAH";

        $responses = [
            new Response(200, [], json_encode($payload1)),
        ];

        $container = [];
        $history = Middleware::history($container);

        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push($history);
        $client = new Client(['handler' => $handler]);

        $loan =new Numeric(1);

        $amount = new NumericString("0.01");
        $rate = new NumericString("5");

        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $data = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();

        createInvestment($loan, $amount, $client, $data, $logger, $rate)->wait();

        $this->assertThat(count($container), $this->equalTo(1));
    }

    /**
     * @covers ::BLC\createInvestment
     */
    public function testMaxRateHigherCreate() {
        $payload1 = new \stdClass();
        $payload1->errors = new \stdClass();
        $payload1->errors->loan_id  =new \stdClass();
        $payload1->errors->loan_id->{"already invested"} = "BLAH BLAH BLAH";

        $responses = [
            new Response(200, [], json_encode($payload1)),
        ];

        $container = [];
        $history = Middleware::history($container);

        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push($history);
        $client = new Client(['handler' => $handler]);

        $loan = new Numeric(1);

        $amount = new NumericString("0.01");
        $rate = new NumericString("5");
        $maxRate = new NumericString("6");

        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $data = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();

        createInvestment($loan, $amount, $client, $data, $logger, $rate, $maxRate)->wait();

        /** @var Request $request */
        $request = $container[0]["request"];
        $result = [];
        parse_str($request->getBody()->getContents(), $result);
        $this->assertThat($result["rate"], $this->equalTo(5));
    }

    /**
     * @covers ::BLC\createInvestment
     */
    public function testMaxRateLowerCreate() {
        $payload1 = new \stdClass();
        $payload1->errors = new \stdClass();
        $payload1->errors->loan_id  =new \stdClass();
        $payload1->errors->loan_id->{"already invested"} = "BLAH BLAH BLAH";

        $responses = [
            new Response(200, [], json_encode($payload1)),
        ];

        $container = [];
        $history = Middleware::history($container);

        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push($history);
        $client = new Client(['handler' => $handler]);

        $loan =new Numeric(1);

        $amount = new NumericString("0.01");
        $rate = new NumericString("5");
        $maxRate = new NumericString("4");

        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $data = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();

        createInvestment($loan, $amount, $client, $data, $logger, $rate, $maxRate)->wait();

        /** @var Request $request */
        $request = $container[0]["request"];
        $result = [];
        parse_str($request->getBody()->getContents(), $result);
        $this->assertThat($result["rate"], $this->equalTo(4));
    }


    /**
     * @covers ::BLC\createInvestment
     */
    public function testMaxRateHigherForBalance() {
        $payload = new \stdClass();

        $investment1 = new \stdClass();
        $investment1->amount = "1";
        $investment1->rate = "2";
        $investment1->id = 5;
        $investment1->dateInvested = "2015-10-05T23:59:59Z";
        $investment1->investorId = 0;
        $investment1->loanId = 999;

        $investment2 = new \stdClass();
        $investment2->amount = "2";
        $investment2->rate = "3";
        $investment2->id = 5;
        $investment2->dateInvested = "2015-09-05T23:59:59Z";
        $investment2->investorId = 0;
        $investment2->loanId = 888;

        $investment3 = new \stdClass();
        $investment3->amount = "4";
        $investment3->rate = "5";
        $investment3->id = 5;
        $investment3->dateInvested = "2015-08-05T23:59:59Z";
        $investment3->investorId = 0;
        $investment3->loanId = 777;


        $payload->investments = [
            $investment1,
            $investment2,
            $investment3
        ];


        $responses = [
            new Response(200, [], json_encode($payload)),
            new Response(200)
        ];

        $container = [];
        $history = Middleware::history($container);

        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push($history);
        $client = new Client(['handler' => $handler]);

        $now = new \DateTime();
        $investment = new Investment(new NumericString("1"), new NumericString("2"), new Numeric(3), new Numeric(4),
            new OptionalDate($now), new Numeric(5));

        $maxRate = new NumericString("100");


        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        balanceInvestment($investment, $maxRate, $client, $logger)->wait();
        $transaction = $container[1];

        /** @var Request $request */
        $request = $transaction['request'];

        $body = $request->getBody()->getContents();

        parse_str($body, $result);

        $this->assertThat($result["rate"], $this->equalTo("4"));
    }

    /**
     * @covers ::BLC\createInvestment
     */
    public function testMaxRateLowerForBalance() {
        $payload = new \stdClass();

        $investment1 = new \stdClass();
        $investment1->amount = "1";
        $investment1->rate = "2";
        $investment1->id = 5;
        $investment1->dateInvested = "2015-10-05T23:59:59Z";
        $investment1->investorId = 0;
        $investment1->loanId = 999;

        $investment2 = new \stdClass();
        $investment2->amount = "2";
        $investment2->rate = "3";
        $investment2->id = 5;
        $investment2->dateInvested = "2015-09-05T23:59:59Z";
        $investment2->investorId = 0;
        $investment2->loanId = 888;

        $investment3 = new \stdClass();
        $investment3->amount = "4";
        $investment3->rate = "5";
        $investment3->id = 5;
        $investment3->dateInvested = "2015-08-05T23:59:59Z";
        $investment3->investorId = 0;
        $investment3->loanId = 777;


        $payload->investments = [
            $investment1,
            $investment2,
            $investment3
        ];


        $responses = [
            new Response(200, [], json_encode($payload)),
            new Response(200)
        ];

        $container = [];
        $history = Middleware::history($container);

        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push($history);
        $client = new Client(['handler' => $handler]);

        $now = new \DateTime();
        $investment = new Investment(new NumericString("1"), new NumericString("2"), new Numeric(3), new Numeric(4),
            new OptionalDate($now), new Numeric(5));

        $maxRate = new NumericString("3");


        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        balanceInvestment($investment, $maxRate, $client, $logger)->wait();
        $transaction = $container[1];

        /** @var Request $request */
        $request = $transaction['request'];

        $body = $request->getBody()->getContents();

        parse_str($body, $result);

        $this->assertThat($result["rate"], $this->equalTo("3"));
    }


}