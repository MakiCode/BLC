<?php
use BLC\Model\Investment;
use BLC\Model\Investments;
use BLC\Model\NumericString;
use Types\Integer;
use Types\Numeric;
use Types\String;

/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/14/15
 * Time: 12:27 PM
 *
 * @uses BLC\Model\OptionalDate
 * @uses ::BLC\isValidOptionalDate
 * @uses BLC\Model\Investments
 * @uses BLC\Model\Investment
 * @uses BLC\Model\NumericString
 * @uses BLC\Model\Optional
 * @uses BLC\Model\LoanFactory
 * @uses BLC\Model\Loan
 * @uses BLC\Model\OptionalString
 * @uses BLC\Model\OptionalBoolean
 * @uses ::\BLC\convertOptionalDate
 * @uses ::\BLC\isValidOptionalDate
 * @uses ::\BLC\dateConversionHelper
 * @uses \BLC\Model\OptionalInteger
 */
class FunctionsTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        date_default_timezone_set("UTC");
    }

    /**
     * @covers ::BLC\weightedAverageRate
     */
    public function testBasicCase()
    {
        bcscale(9);

        $investment1 = new Investment(new NumericString('0.0001'), new NumericString("10.00000"), new Numeric(0),
            new Numeric(0), \BLC\Model\OptionalDate::emptyOption(), new Numeric(0));
        $investment2 = new Investment(new NumericString("0.0001"), new NumericString("9.00000"), new Numeric(0),
            new Numeric(0), \BLC\Model\OptionalDate::emptyOption(), new Numeric(0));
        $investment3 = new Investment(new NumericString("0.0100"), new NumericString("15.00000"), new Numeric(0),
            new Numeric(0), \BLC\Model\OptionalDate::emptyOption(), new Numeric(0));
        $investments = new Investments($investment1, $investment2, $investment3);

        $result = BLC\weightedAverageRate($investments);

        $this->assertThat($result, $this->equalTo("14.892156862"));
    }

    /**
     * @covers ::BLC\weightedAverageRate
     */
    public function testOneItem()
    {
        bcscale(1);

        $investments = new Investments(new Investment(new NumericString("1"), new NumericString("2"), new Numeric(0),
            new Numeric(0), \BLC\Model\OptionalDate::emptyOption(), new \Types\Numeric(0)));
        $result = BLC\weightedAverageRate($investments);
        $this->assertThat($result, $this->equalTo("2"));
    }

    /**
     * @covers ::BLC\weightedAverageRate
     */
    function testMax()
    {
        bcscale(1);

        $investment1 = new Investment(new NumericString(PHP_INT_MAX . ""), new NumericString(PHP_INT_MAX . ""),
            new Numeric(0), new Numeric(0), \BLC\Model\OptionalDate::emptyOption(), new \Types\Numeric(0));
        $investment2 = new Investment(new NumericString(PHP_INT_MAX . ""), new NumericString(PHP_INT_MAX . ""),
            new Numeric(0), new Numeric(0), \BLC\Model\OptionalDate::emptyOption(), new \Types\Numeric(0));
        $investment3 = new Investment(new NumericString(PHP_INT_MAX . ""), new NumericString(PHP_INT_MAX . ""),
            new Numeric(0), new Numeric(0), \BLC\Model\OptionalDate::emptyOption(), new \Types\Numeric(0));
        $investments = new Investments($investment1, $investment2, $investment3);

        $result = BLC\weightedAverageRate($investments);
        $this->assertThat($result, $this->equalTo(PHP_INT_MAX . ".0"));
    }

    /**
     * @covers ::BLC\weightedAverageRate
     */
    function testSame()
    {
        bcscale(1);

        $investment1 = new Investment(new NumericString("5"), new NumericString("5"), new Numeric(0), new Numeric(0),
            \BLC\Model\OptionalDate::emptyOption(), new \Types\Numeric(0));
        $investment2 = new Investment(new NumericString("5"), new NumericString("5"), new Numeric(0), new Numeric(0),
            \BLC\Model\OptionalDate::emptyOption(), new \Types\Numeric(0));
        $investment3 = new Investment(new NumericString("5"), new NumericString("5"), new Numeric(0), new Numeric(0),
            \BLC\Model\OptionalDate::emptyOption(), new \Types\Numeric(0));
        $investments = new Investments($investment1, $investment2, $investment3);

        $result = BLC\weightedAverageRate($investments);
        $this->assertThat($result, $this->equalTo("5.0"));
    }

    /**
     * @covers ::BLC\isValidOptionalDate
     */
    public function testIsValidDateNull()
    {
        $this->assertThat(BLC\isValidOptionalDate(null, new String("")), $this->equalTo(true));
    }

    /**
     * @covers ::BLC\isValidOptionalDate
     */
    public function testIsValidDateDateTime()
    {
        $this->assertThat(BLC\isValidOptionalDate(new DateTime("now"), new String("")), $this->equalTo(true));
    }

    /**
     * @covers ::BLC\isValidOptionalDate
     */
    public function testIsValidDateString()
    {
        $this->assertThat(BLC\isValidOptionalDate("06/07/2015", new String("d/m/Y")), $this->equalTo(true));
    }

    /**
     * @covers ::BLC\isValidOptionalDate
     */
    public function testIsValidDateOptionalDateEmpty()
    {
        $this->assertThat(BLC\isValidOptionalDate(\BLC\Model\OptionalDate::emptyOption(), new String("d/m/Y")), $this->equalTo(true));
    }

    /**
     * @covers ::BLC\isValidOptionalDate
     */
    public function testIsValidDateOptionalDate()
    {
        $optionalDate = new \BLC\Model\OptionalDate(new DateTime("now"));
        $this->assertThat(BLC\isValidOptionalDate($optionalDate, new String("d/m/Y")), $this->equalTo(true));
    }

    /**
     * @covers ::BLC\isValidOptionalDate
     */
    public function testInvalidDate()
    {
        $this->assertThat(BLC\isValidOptionalDate("ABC", new String("d/m/Y")), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\isValidOptionalDate
     */
    public function testInvalidInput()
    {
        $this->assertThat(BLC\isValidOptionalDate(1280, new String("d/m/Y")), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\convertOptionalDate
     */
    public function testIsConvertedDateNull()
    {
        $this->assertThat(BLC\convertOptionalDate(null, new String("")), $this->equalTo(null));
    }

    /**
     * @covers ::BLC\convertOptionalDate
     */
    public function testConvertDateTime()
    {
        $now = new DateTime("now");
        $this->assertThat(BLC\convertOptionalDate($now, new String("")), $this->equalTo($now));
    }

    /**
     * @covers ::BLC\convertOptionalDate
     */
    public function testConvertDateString()
    {
        $date = DateTime::createFromFormat("d/m/Y", "06/07/2015");
        $this->assertThat(BLC\convertOptionalDate("06/07/2015", new String("d/m/Y")), $this->equalTo($date));
    }

    /**
     * @covers ::BLC\convertOptionalDate
     */
    public function testConvertOptionalDate()
    {
        $optionalDate = new \BLC\Model\OptionalDate(new DateTime("now"));
        $this->assertThat(BLC\convertOptionalDate($optionalDate, new String("d/m/Y")), $this->equalTo($optionalDate));
    }

    /**
     * @expectedException InvalidArgumentException
     * @covers ::BLC\convertOptionalDate
     */
    public function testConvertInvalidDate()
    {
        BLC\convertOptionalDate("ABC", new String("d/m/Y"));
    }

    /**
     * @expectedException InvalidArgumentException
     * @covers ::BLC\convertOptionalDate
     */
    public function testInvalidDateFormat()
    {
        BLC\convertOptionalDate(1280, new String("d/m/Y"));
    }

    /**
     * @covers ::BLC\convertToInvestment
     */
    public function testConvertToInvestment()
    {
        $now = new DateTime();
        $investment = new stdClass();
        $investment->amount = "5";
        $investment->rate = "6";
        $investment->id = 7;
        $investment->loanId = 8;
        $investment->dateInvested = $now->format(DATE_ISO8601);
        $investment->investorId = 9;

        $investmentObj = BLC\convertToInvestment($investment);

        $this->assertThat($investmentObj->getAmount()->get(), $this->equalTo("5"));
        $this->assertThat($investmentObj->getRate()->get(), $this->equalTo("6"));
        $this->assertThat($investmentObj->getId()->get(), $this->equalTo(7));
        $this->assertThat($investmentObj->getLoanId()->get(), $this->equalTo(8));
        $this->assertThat($investmentObj->getdateInvested()->get(), $this->equalTo($now));
        $this->assertThat($investmentObj->getinvestorId()->get(), $this->equalTo(9));
    }

    /**
     * @covers ::BLC\convertToInvestment
     */
    public function testConvertToInvestmentMissingAmount()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $now = new DateTime();
        $investment = new stdClass();
//        $investment->amount = "5";
        $investment->rate = "6";
        $investment->id = 7;
        $investment->loanId = 8;
        $investment->dateInvested = $now->format(DATE_ISO8601);
        $investment->investorId = 9;

        BLC\convertToInvestment($investment);
    }

    /**
     * @covers ::BLC\convertToInvestment
     */
    public function testConvertToInvestmentMissingRate()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $now = new DateTime();
        $investment = new stdClass();
        $investment->amount = "5";
//        $investment->rate = "6";
        $investment->id = 7;
        $investment->loanId = 8;
        $investment->dateInvested = $now->format(DATE_ISO8601);
        $investment->investorId = 9;

        $investmentObj = BLC\convertToInvestment($investment);

    }

    /**
     * @covers ::BLC\convertToInvestment
     */
    public function testConvertToInvestmentMissingId()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $now = new DateTime();
        $investment = new stdClass();
        $investment->amount = "5";
        $investment->rate = "6";
//        $investment->id = 7;
        $investment->loanId = 8;
        $investment->dateInvested = $now->format(DATE_ISO8601);
        $investment->investorId = 9;

        $investmentObj = BLC\convertToInvestment($investment);

    }

    /**
     * @covers ::BLC\convertToInvestment
     */
    public function testConvertToInvestmentMissingLoanId()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $now = new DateTime();
        $investment = new stdClass();
        $investment->amount = "5";
        $investment->rate = "6";
        $investment->id = 7;
//        $investment->loanId = 8;
        $investment->dateInvested = $now->format(DATE_ISO8601);
        $investment->investorId = 9;

        $investmentObj = BLC\convertToInvestment($investment);

    }

    /**
     * @covers ::BLC\convertToInvestment
     */
    public function testConvertToInvestmentMissingDateInvested()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $now = new DateTime();
        $investment = new stdClass();
        $investment->amount = "5";
        $investment->rate = "6";
        $investment->id = 7;
        $investment->loanId = 8;
//        $investment->dateInvested = $now->format(DATE_ISO8601);
        $investment->investorId = 9;

        $investmentObj = BLC\convertToInvestment($investment);

    }

    /**
     * @covers ::BLC\convertToInvestment
     */
    public function testConvertToInvestmentMissingInvestorId()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $now = new DateTime();
        $investment = new stdClass();
        $investment->amount = "5";
        $investment->rate = "6";
        $investment->id = 7;
        $investment->loanId = 8;
        $investment->dateInvested = $now->format(DATE_ISO8601);
//        $investment->investorId = 9;

        BLC\convertToInvestment($investment);

    }

    /**
     * @covers ::BLC\convertToInvestment
     */
    public function testConvertToInvestmentBadAmount()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $now = new DateTime();
        $investment = new stdClass();
        $investment->amount = 5;
        $investment->rate = "6";
        $investment->id = 7;
        $investment->loanId = 8;
        $investment->dateInvested = $now->format(DATE_ISO8601);
        $investment->investorId = 9;

        BLC\convertToInvestment($investment);
    }

    /**
     * @covers ::BLC\convertToInvestment
     */
    public function testConvertToInvestmentBadRate()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $now = new DateTime();
        $investment = new stdClass();
        $investment->amount = "5";
        $investment->rate = 6;
        $investment->id = 7;
        $investment->loanId = 8;
        $investment->dateInvested = $now->format(DATE_ISO8601);
        $investment->investorId = 9;

        $investmentObj = BLC\convertToInvestment($investment);

    }

    /**
     * @covers ::BLC\convertToInvestment
     */
    public function testConvertToInvestmentBadId()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $now = new DateTime();
        $investment = new stdClass();
        $investment->amount = "5";
        $investment->rate = "6";
        $investment->id = [];
        $investment->loanId = 8;
        $investment->dateInvested = $now->format(DATE_ISO8601);
        $investment->investorId = 9;

        $investmentObj = BLC\convertToInvestment($investment);

    }

    /**
     * @covers ::BLC\convertToInvestment
     */
    public function testConvertToInvestmentBadLoanId()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $now = new DateTime();
        $investment = new stdClass();
        $investment->amount = "5";
        $investment->rate = "6";
        $investment->id = 7;
        $investment->loanId = [];
        $investment->dateInvested = $now->format(DATE_ISO8601);
        $investment->investorId = 9;

        $investmentObj = BLC\convertToInvestment($investment);

    }

    /**
     * @covers ::BLC\convertToInvestment
     */
    public function testConvertToInvestmentBadDateInvested()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $now = new DateTime();
        $investment = new stdClass();
        $investment->amount = "5";
        $investment->rate = "6";
        $investment->id = 7;
        $investment->loanId = 8;
        $investment->dateInvested = '$now->format(DATE_ISO8601)';
        $investment->investorId = 9;

        $investmentObj = BLC\convertToInvestment($investment);

    }

    /**
     * @covers ::BLC\convertToInvestment
     */
    public function testConvertToInvestmentBadInvestorId()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $now = new DateTime();
        $investment = new stdClass();
        $investment->amount = "5";
        $investment->rate = "6";
        $investment->id = 7;
        $investment->loanId = 8;
        $investment->dateInvested = $now->format(DATE_ISO8601);
        $investment->investorId = [];

        $investmentObj = BLC\convertToInvestment($investment);

    }

    /**
     * @covers ::BLC\bcmax
     */
    public function testbcMaxRight()
    {
        $this->assertThat(\BLC\bcmax("1", "3"), $this->equalTo("3"));
    }

    /**
     * @covers ::BLC\bcmax
     */
    public function testbcMaxLeft()
    {
        $this->assertThat(\BLC\bcmax("4", "3"), $this->equalTo("4"));
    }

    /**
     * @covers ::BLC\unwrapFirstElement
     */
    public function testUnWrap()
    {
        $this->assertThat(BLC\unwrapFirstElement([[]]), $this->equalTo([]));
    }

    /**
     * @covers ::BLC\unwrapFirstElement
     */
    public function testUnWrapEmpty()
    {
        $this->assertThat(BLC\unwrapFirstElement([]), $this->equalTo([]));
    }

    /**
     * @covers ::BLC\unwrapFirstElement
     */
    public function testUnWrapNoFirst()
    {
        $this->assertThat(BLC\unwrapFirstElement(["a" => "b"]), $this->equalTo(["a" => "b"]));
    }


    /**
     * @covers ::BLC\validateDate
     */
    public function testValidateDateValidDate()
    {
        $this->assertThat(BLC\validateDate("02/03/2014", "d/m/Y"), $this->equalTo(true));
    }

    /**
     * @covers ::BLC\validateDate
     */
    public function testValidateDateInvalidDate()
    {
        $this->assertThat(BLC\validateDate("2/3/2014", "d/m/Y"), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\validateDate
     */
    public function testValidateDateNonDate()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        BLC\validateDate([], "d/m/Y");
    }

    /**
     * @covers ::BLC\validateDate
     */
    public function testValidateDateNonFormat()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        BLC\validateDate("1/2/2014", []);
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testMakeLoanBasic()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

        $this->assertThat($loanObj->isLocalbitcoins()->get()->get(), $this->equalTo((bool)$json->trusted_localbitcoins));
        $this->assertThat($loanObj->getRating()->get(), $this->equalTo($json->rating));
        $this->assertThat($loanObj->getDescription()->get(), $this->equalTo($json->description));
        $this->assertThat($loanObj->getTitle()->get(), $this->equalTo($json->title));
        $this->assertThat($loanObj->getType()->get(), $this->equalTo($json->type));
        $this->assertThat($loanObj->isEbay()->get()->get(), $this->equalTo((bool)$json->trusted_ebay));
        $this->assertThat($loanObj->getSalary()->get(), $this->equalTo($json->salary));
        $this->assertThat($loanObj->getCountryID()->get(), $this->equalTo($json->countryId));
        $this->assertThat($loanObj->getFrequency()->get(), $this->equalTo($json->frequency));
        $this->assertThat($loanObj->getDenomination()->get(), $this->equalTo($json->denomination));
        $this->assertThat($loanObj->getCreatedAt()->get()->format('Y-m-d\TH:i:s\Z'), $this->equalTo($json->createdAt));//TODO
        $this->assertThat($loanObj->isLinkedin()->get()->get(), $this->equalTo((bool)$json->social_linkedin));
        $this->assertThat($loanObj->getTerm()->get(), $this->equalTo($json->term));
        $this->assertThat($loanObj->getId()->get(), $this->equalTo($json->id));
        $this->assertThat($loanObj->isPaypal()->get()->get(), $this->equalTo((bool)$json->trusted_paypal));
        $this->assertThat($loanObj->getPaymentStatus()->get(), $this->equalTo($json->paymentStatus));
        $this->assertThat($loanObj->getExpirationDate()->get()->format('Y-m-d\TH:i:s\Z'), $this->equalTo($json->expirationDate)); //todo
        $this->assertThat($loanObj->getAmount()->get(), $this->equalTo($json->amount));
        $this->assertThat($loanObj->getBorrower()->get(), $this->equalTo($json->borrower));
        $this->assertThat($loanObj->isGoogle()->get()->get(), $this->equalTo((bool)$json->social_google));
        $this->assertThat($loanObj->isAmazon()->get()->get(), $this->equalTo((bool)$json->trusted_amazon));
        $this->assertThat($loanObj->isTwitter()->get()->get(), $this->equalTo((bool)$json->social_twitter));
        $this->assertThat($loanObj->isFacebook()->get()->get(), $this->equalTo((bool)$json->social_facebook));
        $this->assertThat($loanObj->getStatus()->get(), $this->equalTo($json->status));
        $this->assertThat($loanObj->getPercentFunded()->get(), $this->equalTo($json->percentFunded));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoCreditScore()
    {
        $loan = <<<TAG
{

      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testActiveToRepaid()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",

      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoTrustedBitCoins()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,

      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
//        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

        $this->assertThat($loanObj->isLocalbitcoins()->get()->get(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoRating()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,

      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoDescription()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,

      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoTitle()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",

      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoType()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",

      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoTrustedEbay()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",

      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
//        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

        $this->assertThat($loanObj->isEbay()->get()->get(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoCoinBase()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,

      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
//        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

        $this->assertThat($loanObj->isCoinbase()->get()->get(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoSalary()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,

      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
//        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

        $this->assertThat($loanObj->getSalary()->get(), $this->equalTo(""));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoCountryID()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",

      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoFrequency()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",

      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoDenomination()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,

      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoCreatedAt()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",

      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
//        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

        $this->assertThat($loanObj->getCreatedAt()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoSocialLinkedIn()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",

      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
//        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

        $this->assertThat($loanObj->isLinkedin()->get()->get(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoTerm()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,

      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoID()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,

      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoTrustedPaypal()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,

      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
//        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

        $this->assertThat($loanObj->isPaypal()->get()->get(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoPaymentStatus()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,

      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoExpirationDate()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",

      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
//        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

        $this->assertThat($loanObj->getExpirationDate()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoAmount()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",

      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoBorrower()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",

      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoGoogle()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,

      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
//        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

        $this->assertThat($loanObj->isGoogle()->get()->get(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoAmazon()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,

      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
//        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

        $this->assertThat($loanObj->isAmazon()->get()->get(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoTwitter()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,

      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
//        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

        $this->assertThat($loanObj->isTwitter()->get()->get(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoFacebook()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,

      "status": "Funding",
      "percentFunded": 10
}
TAG;
//        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

        $this->assertThat($loanObj->isLocalbitcoins()->get()->get(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoStatus()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,

      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testNoPercentFunded()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding"

}
TAG;
//        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

        $this->assertThat($loanObj->getPercentFunded()->get(), $this->equalTo((float)0));
    }


    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadCreditScore()
    {
        $loan = <<<TAG
{
      "creditScore": 5,
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadActiveToRepaid()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": {},
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testGoodActiveToRepaidFloat()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 1.1,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
//        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

        $this->assertThat($loanObj->getActiveToRepaid()->get(), $this->equalTo(1.1));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testGoodActiveToRepaidInt()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 1,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
//        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

        $this->assertThat($loanObj->getActiveToRepaid()->get(), $this->equalTo(1));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testGoodActiveToRepaidNumeric()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": "1.3",
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
//        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

        $this->assertThat($loanObj->getActiveToRepaid()->get(), $this->equalTo(1.3));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadBitcoins()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": [],
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testbadRating()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": [],
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadDescription()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": [],
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadTitle()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": {},
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadType()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": {},
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadEbay()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": {},
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadCoinBase()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": {},
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadSalary()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": {},
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadCountryId()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": {},
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadFrequency()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": {},
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadDenom()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": {},
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
        public function testBadCreatedAt()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": {},
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadLinkedIn()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": {},
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadTerm()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": {},
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadID()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": {},
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadPaypal()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": {},
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadPaymentStatus()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": {},
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadExpirationDate()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": {},
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadAmount()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": {},
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadBorrowe()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": {},
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadGoogle()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": {},
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadAmazon()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": {},
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadTwitter()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": [],
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadFacebook()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": {},
      "status": "Funding",
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadStatus()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": {},
      "percentFunded": 10
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadPercentFunded()
    {
        $loan = <<<TAG
{
      "creditScore": "C5",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "",
      "title": "Investing",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "$10001-$30000",
      "countryId": "VE",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-04T11:40:32Z",
      "social_linkedin": 0,
      "term": 30,
      "id": 18222,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-09T23:59:59Z",
      "amount": "0.44000000",
      "borrower": 19391,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": {}
}
TAG;
        $this->setExpectedException(InvalidArgumentException::class);
        $json = json_decode($loan);
        $loanObj = \BLC\makeLoan($json);

//        $this->assertThat($loanObj->isLocalbitcoins()->has(), $this->equalTo(false));
    }


    /**
     * @covers ::BLC\validateInvestmentsResponse
     */
    public function testValidateInvestmentResponseGood()
    {
        $logger = $this->getMockBuilder(\Monolog\Logger::class)->disableOriginalConstructor()->getMock();

        $data = new stdClass();
        $data->investments = ["A"];
        $this->assertThat(\BLC\validateInvestmentsResponse($data, $logger, null), $this->equalTo(true));
    }

    /**
     * @covers ::BLC\validateInvestmentsResponse
     */
    public function testValidateInvestmentResponseBadNoInvestments()
    {
        $logger = $this->getMockBuilder(\Monolog\Logger::class)->disableOriginalConstructor()->getMock();

        $data = new stdClass();
        $this->setExpectedException(InvalidArgumentException::class);
        \BLC\validateInvestmentsResponse($data, $logger, null);
    }

    /**
     * @covers ::BLC\validateInvestmentsResponse
     */
    public function testValidateInvestmentResponseBadNotArray()
    {
        $logger = $this->getMockBuilder(\Monolog\Logger::class)->disableOriginalConstructor()->getMock();

        $data = new stdClass();
        $data->investments = "A";
        $this->setExpectedException(InvalidArgumentException::class);
        \BLC\validateInvestmentsResponse($data, $logger, null);

    }

    /**
     * @covers ::BLC\validateInvestmentsResponse
     */
    public function testValidateInvestmentResponseBadNoItems()
    {
        $logger = $this->getMockBuilder(\Monolog\Logger::class)->disableOriginalConstructor()->getMock();

        $data = new stdClass();
        $data->investments = [];
        $this->setExpectedException(InvalidArgumentException::class);
        \BLC\validateInvestmentsResponse($data, $logger, null);

    }

    /**
     * @covers ::BLC\checkPostResponse
     */
    public function testIdGood() {
        $logger = $this->getMockBuilder(\Monolog\Logger::class)->disableOriginalConstructor()->getMock();

        $data = new stdClass();
        $data->id = 3;
//        $this->setExpectedException(InvalidArgumentException::class);
        $this->assertThat(\BLC\checkPostResponse($data, $logger, null),$this->equalTo(RESPONSE_OK));
    }

    /**
     * @covers ::BLC\checkPostResponse
     */
    public function testIdBadNone() {
        $logger = $this->getMockBuilder(\Monolog\Logger::class)->disableOriginalConstructor()->getMock();

        $data = new stdClass();
//        $data->id = 3;
        $this->setExpectedException(InvalidArgumentException::class);
        BLC\checkPostResponse($data, $logger, null);
    }

    /**
     * @covers ::BLC\checkPostResponse
     */
    public function testIdBadWrongType() {
        $logger = $this->getMockBuilder(\Monolog\Logger::class)->disableOriginalConstructor()->getMock();

        $data = new stdClass();
        $data->id = "4";
        $this->setExpectedException(InvalidArgumentException::class);
        BLC\checkPostResponse($data, $logger, null);
    }

    /**
     * @covers ::BLC\checkPostResponse
     */
    public function testCheckPostAlreadyInvested() {
        $logger = $this->getMockBuilder(\Monolog\Logger::class)->disableOriginalConstructor()->getMock();

        $data = new \stdClass();
        $data->errors = new \stdClass();
        $data->errors->loan_id  =new \stdClass();
        $data->errors->loan_id->{"already invested"} = "BLAH BLAH BLAH";


        $this->assertThat(BLC\checkPostResponse($data, $logger, null), $this->equalTo(RESPONSE_ALREADY_INVESTED));
    }
}
