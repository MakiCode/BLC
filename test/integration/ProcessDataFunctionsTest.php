<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/24/15
 * Time: 2:52 PM
 */

namespace BLC;

use BLC\Config\Config;
use BLC\Config\Data;
use BLC\Model\IntegerList;
use BLC\Model\IntegerStringMap;
use BLC\Model\LoanFactory;
use BLC\Model\Loans;
use BLC\Model\Int_Numeric;
use BLC\Model\NumericString;
use BLC\Model\WorkItem;
use BLC\Model\WorkQueue;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Monolog\Logger;
use Types\Float;
use Types\Integer;
use Types\Numeric;
use Types\String;
use Types\Boolean;

/**
 * @uses BLC\Model\IntegerList
 * @uses BLC\Model\LoanFactory
 * @uses BLC\Model\Loans
 * @uses BLC\Model\Int_Numeric
 * @uses Types\Integer
 * @uses Types\String
 * @uses BLC\Model\OptionalDate
 * @uses BLC\Model\Optional
 * @uses BLC\Model\Loan
 * @uses BLC\Model\OptionalBoolean
 * @uses BLC\JSON
 * @uses ::BLC\makeLoan
 * @uses ::BLC\convertOptionalDate
 * @uses ::BLC\isValidOptionalDate
 * @uses BLC\Model\OptionalString
 * @uses BLC\Model\OptionalNumericString
 * @uses BLC\Model\WorkItem
 * @uses BLC\Model\NumericString
 * @uses ::BLC\haveChecked
 * @uses ::BLC\extractRelevant
 * @uses ::BLC\unwrapFirstElement
 * @uses ::BLC\makeLoans
 * @uses BLC\Model\IntegerStringMap
 * @uses ::BLC\getValidLoans
 * @uses ::BLC\findMatchingBorrowers
 * @uses ::\BLC\dateConversionHelper
 * @uses \BLC\Model\OptionalInteger
 */
class ProcessDataFunctionsTest extends \PHPUnit_Framework_TestCase
{
    private $input;
    private $loanFunding1String;
    private $loanFunding2String;
    private $loanFundedString;
    private $loansData;
    private $realData;

    public function setUp()
    {
        date_default_timezone_set("America/Mexico_City");
        $this->input = [new Int_Numeric(new Integer(1), new Numeric(2)), new Int_Numeric(new Integer(3), new Numeric(4))];
        $this->loanFunding1String = '{"creditScore":"C5","activeToRepaid":100,"trusted_localbitcoins": 0,"rating": 0,"description":
        "","title": "Investing","type": "Investing","trusted_ebay": 0,"trusted_coinbase": 0,"salary": "$10001-$30000",
        "countryId": "VE","frequency": 30,"denomination": "BTC","createdAt": "2015-09-04T11:40:32Z","social_linkedin":
         0,"term": 30,"id": 18222,"trusted_paypal": 1,"paymentStatus": "Current","expirationDate":
         "2015-09-09T23:59:59Z","amount": "0.44000000","borrower": 19391,"social_google": 0,"trusted_amazon": 0,
         "social_twitter": 0,"social_facebook": 0,"status": "Funding","percentFunded": 10}';
        $this->loanFunding2String = '{"creditScore":"C5","activeToRepaid":100,"trusted_localbitcoins": 0,"rating": 0,"description":
        "","title": "Investing","type": "Investing","trusted_ebay": 0,"trusted_coinbase": 0,"salary": "$10001-$30000",
        "countryId": "VE","frequency": 30,"denomination": "BTC","createdAt": "2015-09-04T11:40:32Z","social_linkedin":
         0,"term": 30,"id": 18222,"trusted_paypal": 1,"paymentStatus": "Current","expirationDate":
         "2015-09-09T23:59:59Z","amount": "0.44000000","borrower": 19391,"social_google": 0,"trusted_amazon": 0,
         "social_twitter": 0,"social_facebook": 0,"status": "Funding","percentFunded": 10}';
        $this->loanFundedString = '{"creditScore":"C5","activeToRepaid":100,"trusted_localbitcoins": 0,"rating": 0,"description":
        "","title": "Investing","type": "Investing","trusted_ebay": 0,"trusted_coinbase": 0,"salary": "$10001-$30000",
        "countryId": "VE","frequency": 30,"denomination": "BTC","createdAt": "2015-09-04T11:40:32Z","social_linkedin":
         0,"term": 30,"id": 18222,"trusted_paypal": 1,"paymentStatus": "Current","expirationDate":
         "2015-09-09T23:59:59Z","amount": "0.44000000","borrower": 19391,"social_google": 0,"trusted_amazon": 0,
         "social_twitter": 0,"social_facebook": 0,"status": "Funded","percentFunded": 10}';

        $this->loansData = <<<TAG
{
    "loans": [
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
        },
        {
            "id": 18234,
            "title": "Bigger trading volume",
            "description": "Hi! Since my trading business is doing well I'd like to increase my trading volume nad capabilities and use this current situation on BTC market to get some profit on a higher scale. I'd appreciate if you keep your rates up to 12%. The loan has back up in my incomes in case if something with my business goes unplanned. Please vote and rate me after repayment, your feedback is valuable for me. Thank you for investing in my loanes.",
            "borrower": 1818,
            "amount": "4.00000000",
            "frequency": 30,
            "term": 30,
            "expirationDate": "2015-09-19T23:59:59Z",
            "type": "Business",
            "denomination": "BTC",
            "status": "Funding",
            "paymentStatus": "Current",
            "createdAt": "2015-09-04T19:17:24Z",
            "countryId": "HR",
            "rating": 82,
            "social_facebook": 1,
            "social_linkedin": 0,
            "social_google": 0,
            "social_twitter": 0,
            "trusted_paypal": 1,
            "trusted_amazon": 0,
            "trusted_localbitcoins": 0,
            "trusted_ebay": 0,
            "trusted_coinbase": 0,
            "activeToRepaid": 22,
            "salary": "$10001-$30000",
            "creditScore": "D1",
            "percentFunded": 7.8,
            "votes": "2"
        },
        {
            "creditScore": "C3",
            "activeToRepaid": 100,
            "trusted_localbitcoins": 1,
            "rating": 0,
            "description": "Ol&aacute; a todos, conto com a ajuda de todos para realizar meu primeiro emprestimo aqui no BLC, &eacute; um valor bem baixo que serve apenas para uma movimenta&ccedil;&atilde;o em minha conta, sou membro de outro site mas as taxas l&aacute; est&atilde;o muito altas e estou aqui conhecendo o BLC&nbsp;Com inten&ccedil;&atilde;o de conhecer o BLC, agradeceria&nbsp;taxa entre 3% e 5%&nbsp;Hello everyone, I count on everyone's help to make my first loan here in the BLC is a very low value that is only for a drive in my account, I am a member of another site but the rates there are very high and I'm here knowing BLC&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Intended to meet the BLC, I, thank rate between 3% and 5%",
            "title": "First Loan (small)",
            "type": "Business",
            "trusted_ebay": 1,
            "trusted_coinbase": 0,
            "salary": null,
            "countryId": "BR",
            "frequency": 14,
            "denomination": "BTC",
            "createdAt": "2015-09-04T14:48:46Z",
            "social_linkedin": 1,
            "term": 14,
            "id": 18227,
            "trusted_paypal": 1,
            "paymentStatus": "Current",
            "expirationDate": "2015-09-10T23:59:59Z",
            "amount": "0.11370000",
            "borrower": 11747,
            "social_google": 1,
            "trusted_amazon": 0,
            "social_twitter": 1,
            "social_facebook": 1,
            "status": "Funding",
            "percentFunded": 9.1,
            "votes": "1"
        },
        {
            "id": 18215,
            "title": "investing",
            "description": "Olá pessoal para que o reenbolço seja garantido peço que no maximo 5% por favor, obrigado a todos.\n \nPersonal hello to the reenbolço be guaranteed ask that at most 5% please, thank you to everyone.",
            "borrower": 10975,
            "amount": "1.00000000",
            "frequency": 30,
            "term": 270,
            "expirationDate": "2015-09-26T23:59:59Z",
            "type": "Business",
            "denomination": "BTC",
            "status": "Funding",
            "paymentStatus": "Current",
            "createdAt": "2015-09-04T08:38:04Z",
            "countryId": "BR",
            "rating": 2,
            "social_facebook": 1,
            "social_linkedin": 1,
            "social_google": 1,
            "social_twitter": 1,
            "trusted_paypal": 1,
            "trusted_amazon": 0,
            "trusted_localbitcoins": 0,
            "trusted_ebay": 0,
            "trusted_coinbase": 0,
            "activeToRepaid": 100,
            "salary": null,
            "creditScore": "C1",
            "percentFunded": 0.2
        },
        {
            "creditScore": "C1",
            "activeToRepaid": 100,
            "trusted_localbitcoins": 1,
            "rating": 0,
            "description": "This loan will be used for local trading on LBC. I am looking for a good rate where we all can make profit off of. ",
            "title": "LBC Trading",
            "type": "Other",
            "trusted_ebay": 1,
            "trusted_coinbase": 0,
            "salary": "$90001-$150000",
            "countryId": "US",
            "frequency": 30,
            "denomination": "USD",
            "createdAt": "2015-09-04T02:33:02Z",
            "social_linkedin": 0,
            "term": 60,
            "id": 18210,
            "trusted_paypal": 1,
            "paymentStatus": "Current",
            "expirationDate": "2015-09-15T23:59:59Z",
            "amount": "1000.00",
            "borrower": 13989,
            "social_google": 0,
            "trusted_amazon": 1,
            "social_twitter": 1,
            "social_facebook": 1,
            "status": "Funding",
            "percentFunded": 2.8
        },
        {
            "creditScore": "C5",
            "activeToRepaid": 100,
            "trusted_localbitcoins": 0,
            "rating": 0,
            "description": "hello  every one i want loan now . i plan btc for  investing   not hyip site name company is ADBROOK LTD Company.\n .. so far as now im investing here  50$ = 0.22 btc\n \nCalculator\n1 pack is = 10$ 0.04 btc\n5 packs is = 50$  0.22 btc\nmy propit  is daily 2.5$  0.01 BTC\nand than my total return is  75$  with in 40 days  0.33 BTC\n \ni need more adpacks for big daily income pls  trust me ...thanks\n ",
            "title": "10% in 2 month BTC Loan (0.2 only)",
            "type": "Investing",
            "trusted_ebay": 0,
            "trusted_coinbase": 0,
            "salary": null,
            "countryId": "PH",
            "frequency": 30,
            "denomination": "BTC",
            "createdAt": "2015-09-04T00:40:24Z",
            "social_linkedin": 0,
            "term": 60,
            "id": 18208,
            "trusted_paypal": 1,
            "paymentStatus": "Current",
            "expirationDate": "2015-10-04T23:59:59Z",
            "amount": "0.20000000",
            "borrower": 19804,
            "social_google": 1,
            "trusted_amazon": 0,
            "social_twitter": 1,
            "social_facebook": 1,
            "status": "Funding",
            "votes": "1",
            "percentFunded": 0
        },
        {
            "id": 18211,
            "title": "Save Return Make Coins, Rate at 0.008%",
            "description": "Please dont fund if you are not going to follow my Rate of 0.008%\n Read loan please low Rate investment\n loan would only work with low interest, looking for 0.25 Btc  at 0.008% interest Rate. your .25 Btc are save from loss. im only lending for margin, so the investment is save. you wont become rich but at list make some coin back. investors im not making any money from this just testing if this is something i can do every month and make you and me some extra coins.\n Any rate higher the 0.008% would not be profitable and I would have to put money from my pocket to pay it back.\nanyone here has lost more investing in a high interest loan, you dont lose anything by investing in 0.25% Btc at 0.008 so please Fund my loan at 0.008% Rate. \n Any Rate higher then 0.008% would be cancel.",
            "borrower": 15856,
            "amount": "0.25000000",
            "frequency": 30,
            "term": 30,
            "expirationDate": "2015-09-18T23:59:59Z",
            "type": "Investing",
            "denomination": "BTC",
            "status": "Funding",
            "paymentStatus": "Current",
            "createdAt": "2015-09-04T02:59:16Z",
            "countryId": "US",
            "rating": 0,
            "social_facebook": 0,
            "social_linkedin": 0,
            "social_google": 0,
            "social_twitter": 0,
            "trusted_paypal": 0,
            "trusted_amazon": 1,
            "trusted_localbitcoins": 0,
            "trusted_ebay": 1,
            "trusted_coinbase": 0,
            "activeToRepaid": 100,
            "salary": "$10001-$30000",
            "creditScore": "E1",
            "percentFunded": 11.2
        },
        {
            "id": 18216,
            "title": "15 BTC First loan for LBC Trading",
            "description": "First Loan Request for LocalBitcoins - BitMarkets\n \nThis is my first official loan request on BitLendingClub for my LocalBitcoins trading operation. I became a Pro-trader on LBC within 1 month of starting and have been averaging close to $200,000 USD per month in volume over the past 3 months. The $200,000 volume would be more, but is currently limited by the capital I currently have available to me. I maintain trading accounts with 3 major bitcoin exchanges and am generally able to turn my funds an average of once per 4 days at an average net of 5-6%.\n \nI am in the process of trying out BitLending Club and BTCjam to increase my capital base and my trading profits. I am starting relatively small to get a feel for how these lending platforms work and would be open to any advice you may have to increase the efficiency of these loans. I will let the market dictate the rate on this first loan, but I do reserve the right to cancel any offer that is not reasonable based on conditions or could be detrimental to me or my operation's financial health.\n \nTHANK YOU FOR YOUR TIME AND CONSIDERATION\n \nLink to my Trader Profile on LocalBitcoins.com\n\n",
            "borrower": 3222,
            "amount": "25.00000000",
            "frequency": 30,
            "term": 30,
            "expirationDate": "2015-09-07T23:59:59Z",
            "type": "LocalBitcoins Trading",
            "denomination": "BTC",
            "status": "Canceled",
            "paymentStatus": "Current",
            "createdAt": "2015-09-04T08:59:20Z",
            "countryId": "US",
            "rating": 33,
            "social_facebook": 1,
            "social_linkedin": 1,
            "social_google": 1,
            "social_twitter": 1,
            "trusted_paypal": 1,
            "trusted_amazon": 1,
            "trusted_localbitcoins": 1,
            "trusted_ebay": 1,
            "trusted_coinbase": 0,
            "activeToRepaid": 100,
            "salary": "More than $150001",
            "creditScore": "C4",
            "percentFunded": 0
        },
        {
            "id": 18223,
            "title": "Trading Loan",
            "description": "Hi,\nActive Investor on here, (even have my very own TROLL, who tries his best to scupper my listings)\n \nI also trade on LBC &amp; Scrypt.cc\n \nAsking for 1Btc for 30 days\n \nWilling to pay interest @ the recommended 13.11%\nBut would appreciate a lower rate if you could be kind enough ...\n \nOffline I work as a freelance Crowd Safety Consultant, where I advise stadiums, concert venues &amp; festivals, on crowd capacity, crowd flow, etc\n \nHave repaid all my loans on time or early..\n \n ",
            "borrower": 15695,
            "amount": "1.00000000",
            "frequency": 30,
            "term": 30,
            "expirationDate": "2015-09-10T23:59:59Z",
            "type": "Arbitrage Trading",
            "denomination": "BTC",
            "status": "Funding",
            "paymentStatus": "Current",
            "createdAt": "2015-09-04T12:09:57Z",
            "countryId": "GB",
            "rating": 10,
            "social_facebook": 1,
            "social_linkedin": 1,
            "social_google": 1,
            "social_twitter": 1,
            "trusted_paypal": 0,
            "trusted_amazon": 0,
            "trusted_localbitcoins": 1,
            "trusted_ebay": 0,
            "trusted_coinbase": 0,
            "activeToRepaid": 72,
            "salary": null,
            "creditScore": "C4",
            "percentFunded": 14.5,
            "votes": "3"
        },
        {
            "id": 18209,
            "title": "buying mining power",
            "description": "Hello Respected Investors\n \n I am actually looking for loan for investing in cloud mining  \nand also i looking to buy more miners for my home and paying for the power for them\nand have to do  other trading stuff\n \nIf you want to increase your bitcoins just follow me.\n all invest  and please later give me  ++positive++ if i have made early payment\nall payment will be in time ..\nthank you",
            "borrower": 13211,
            "amount": "2.80000000",
            "frequency": 7,
            "term": 14,
            "expirationDate": "2015-09-06T23:59:59Z",
            "type": "Other",
            "denomination": "BTC",
            "status": "Funding",
            "paymentStatus": "Current",
            "createdAt": "2015-09-04T01:05:34Z",
            "countryId": "ZA",
            "rating": 2,
            "social_facebook": 1,
            "social_linkedin": 0,
            "social_google": 1,
            "social_twitter": 1,
            "trusted_paypal": 1,
            "trusted_amazon": 0,
            "trusted_localbitcoins": 0,
            "trusted_ebay": 1,
            "trusted_coinbase": 0,
            "activeToRepaid": 100,
            "salary": "Under $10000",
            "creditScore": "C1",
            "percentFunded": 10,
            "votes": "3"
        }
    ]
}
TAG;

        $this->realData = <<<'TAG'
{
  "loans": [
    {
      "id": 18241,
      "title": "LocalBitcoins Trading Capital  12.46%",
      "description": "Tradding in Localbitcoin capital, guarantee timely payments with my own businessRemember  you vote to fill the loan as soon!\nThank you for your trust and investment\n************************************************************************\nCapital para tradding en Localbitcoin, pagos puntuales con garantia de mi propio negocioRecuerden suto para llenar el prestamo lo mas pronto!\nGracias por su confianza y su inversion\n ",
      "borrower": 9735,
      "amount": "1.00000000",
      "frequency": 14,
      "term": 14,
      "expirationDate": "2015-10-05T23:59:59Z",
      "type": "LocalBitcoins Trading",
      "denomination": "BTC",
      "status": "Funded",
      "paymentStatus": "Current",
      "createdAt": "2015-09-05T03:54:57Z",
      "countryId": "VE",
      "rating": 64,
      "social_facebook": 1,
      "social_linkedin": 1,
      "social_google": 1,
      "social_twitter": 1,
      "trusted_paypal": 1,
      "trusted_amazon": 1,
      "trusted_localbitcoins": 1,
      "trusted_ebay": 1,
      "trusted_coinbase": 0,
      "activeToRepaid": 18,
      "salary": "Under $10000",
      "creditScore": "C3",
      "percentFunded": 100,
      "votes": "2",
      "paymentDueDate": "2015-09-19T23:59:59Z"
    },
    {
      "id": 18246,
      "title": "Debt Console Loan #3 15%",
      "description": "Loan #3\n \nDEBT CONSOLIDATION\n \nThis loan is going towards some of my debt over at BTCjam and also into my mining investments.\nIf you read my previous loan attempt it explains everything as I need to address a few issues before i can really start building my loan rep.\nAll investors can ask for 15% MAX. I hope this loan will help my future repuation amongst the BTC investing community.\nThank for the chance BLC and community.\nCheers.\n \n- Please upvote -",
      "borrower": 10765,
      "amount": "5.00000000",
      "frequency": 7,
      "term": 60,
      "expirationDate": "2015-09-09T23:59:59Z",
      "type": "Debt Consolidation",
      "denomination": "BTC",
      "status": "Funding",
      "paymentStatus": "Current",
      "createdAt": "2015-09-05T09:14:38Z",
      "countryId": "AU",
      "rating": 0,
      "social_facebook": 1,
      "social_linkedin": 1,
      "social_google": 1,
      "social_twitter": 1,
      "trusted_paypal": 1,
      "trusted_amazon": 0,
      "trusted_localbitcoins": 1,
      "trusted_ebay": 1,
      "trusted_coinbase": 0,
      "activeToRepaid": 100,
      "salary": "$10001-$30000",
      "creditScore": "C3",
      "percentFunded": 1.3
    },
    {
      "creditScore": "E1",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "\nThis loan is to help me pay part of my fees. and also build reputation.\n\n ",
      "title": "Payments Of Fees",
      "type": "Debt Consolidation",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": "Under $10000",
      "countryId": "GH",
      "frequency": 7,
      "denomination": "BTC",
      "createdAt": "2015-09-05T02:54:38Z",
      "social_linkedin": 0,
      "term": 7,
      "id": 18239,
      "trusted_paypal": 0,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-12T23:59:59Z",
      "amount": "0.75000000",
      "borrower": 18530,
      "social_google": 0,
      "trusted_amazon": 0,
      "social_twitter": 0,
      "social_facebook": 0,
      "status": "Funding",
      "percentFunded": 7.7
    },
    {
      "id": 18253,
      "title": "Loan to increase trading",
      "description": "Hello again! This is the short term loan to get funds for trading on a larger scale. I would like to use the current situation on the BTC market and pull out some profit. If you can offer me 12% rate I would appreciate it. I pay my loans on time or early and also have back up in my incomes if something unpredicted happens. Please don't forget to vote and rate me after repayment. Thank you for investing.",
      "borrower": 1818,
      "amount": "4.00000000",
      "frequency": 14,
      "term": 14,
      "expirationDate": "2015-09-15T23:59:59Z",
      "type": "Day Trading",
      "denomination": "BTC",
      "status": "Funding",
      "paymentStatus": "Current",
      "createdAt": "2015-09-05T12:53:06Z",
      "countryId": "HR",
      "rating": 82,
      "social_facebook": 1,
      "social_linkedin": 0,
      "social_google": 0,
      "social_twitter": 0,
      "trusted_paypal": 1,
      "trusted_amazon": 0,
      "trusted_localbitcoins": 0,
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "activeToRepaid": 22,
      "salary": "$10001-$30000",
      "creditScore": "D1",
      "percentFunded": 3.8
    },
    {
      "id": 18242,
      "title": "1st Trading Loan",
      "description": "Hi, all my previous loans were for cloud mining, but since I got heavily scammed after my 5th loan I quit cloud mining. This loan would be used for trading, payments will be made every 14 days. Please keep the interest rate within 3%. Thank you.",
      "borrower": 2844,
      "amount": "0.20000000",
      "frequency": 14,
      "term": 30,
      "expirationDate": "2015-09-20T23:59:59Z",
      "type": "Investing",
      "denomination": "BTC",
      "status": "Funding",
      "paymentStatus": "Current",
      "createdAt": "2015-09-05T04:13:22Z",
      "countryId": "IN",
      "rating": 20,
      "social_facebook": 1,
      "social_linkedin": 1,
      "social_google": 1,
      "social_twitter": 1,
      "trusted_paypal": 1,
      "trusted_amazon": 0,
      "trusted_localbitcoins": 0,
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "activeToRepaid": 17,
      "salary": null,
      "creditScore": "C3",
      "votes": "0",
      "percentFunded": 21.4
    },
    {
      "id": 18247,
      "title": "Loan for the holiday",
      "description": "Hello,\n \nAm looking for a loan for the holiday, any and all investors are welcomed.  If you have any quesitons, feel free to ask.",
      "borrower": 11213,
      "amount": "0.50000000",
      "frequency": 7,
      "term": 7,
      "expirationDate": "2015-09-07T23:59:59Z",
      "type": "Other",
      "denomination": "BTC",
      "status": "Funding",
      "paymentStatus": "Current",
      "createdAt": "2015-09-05T09:31:04Z",
      "countryId": "US",
      "rating": -29,
      "social_facebook": 1,
      "social_linkedin": 0,
      "social_google": 1,
      "social_twitter": 1,
      "trusted_paypal": 1,
      "trusted_amazon": 1,
      "trusted_localbitcoins": 1,
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "activeToRepaid": 3,
      "salary": "$30001-$60000",
      "creditScore": "C4",
      "percentFunded": 75.4,
      "votes": "2"
    },
    {
      "creditScore": "D3",
      "activeToRepaid": 100,
      "trusted_localbitcoins": 0,
      "rating": 0,
      "description": "Hello investers anyone help me to get this loan i want this loan for ptc sites investment i will pay on 5th of every month without fail so kindly help me to get this loan thankyou",
      "title": "investment",
      "type": "Investing",
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "salary": null,
      "countryId": "IN",
      "frequency": 30,
      "denomination": "BTC",
      "createdAt": "2015-09-05T00:21:50Z",
      "social_linkedin": 0,
      "term": 90,
      "id": 18236,
      "trusted_paypal": 1,
      "paymentStatus": "Current",
      "expirationDate": "2015-09-08T23:59:59Z",
      "amount": "1.30000000",
      "borrower": 17955,
      "social_google": 1,
      "trusted_amazon": 0,
      "social_twitter": 1,
      "social_facebook": 1,
      "status": "Funding",
      "percentFunded": 0
    },
    {
      "id": 18248,
      "title": "Loan for trading and earn trust with the community",
      "description": "Loan for trading and earn trust with the community",
      "borrower": 1925,
      "amount": "0.50000000",
      "frequency": 30,
      "term": 60,
      "expirationDate": "2015-09-11T23:59:59Z",
      "type": "LocalBitcoins Trading",
      "denomination": "BTC",
      "status": "Funding",
      "paymentStatus": "Current",
      "createdAt": "2015-09-05T09:36:31Z",
      "countryId": "AR",
      "rating": 0,
      "social_facebook": 1,
      "social_linkedin": 0,
      "social_google": 1,
      "social_twitter": 1,
      "trusted_paypal": 1,
      "trusted_amazon": 0,
      "trusted_localbitcoins": 0,
      "trusted_ebay": 1,
      "trusted_coinbase": 0,
      "activeToRepaid": 100,
      "salary": null,
      "creditScore": "D2",
      "percentFunded": 0.1
    },
    {
      "id": 18250,
      "title": "Quickie Loan",
      "description": "I have 6 completed and repaid loans on here.  A few late pays due to outside factors, but all paid in full.  \nI offer particularly high interest rates.  \n \nThanks",
      "borrower": 82,
      "amount": "1.10000000",
      "frequency": 14,
      "term": 14,
      "expirationDate": "2015-09-12T23:59:59Z",
      "type": "Other",
      "denomination": "BTC",
      "status": "Funding",
      "paymentStatus": "Current",
      "createdAt": "2015-09-05T10:57:02Z",
      "countryId": "US",
      "rating": 9,
      "social_facebook": 0,
      "social_linkedin": 0,
      "social_google": 0,
      "social_twitter": 0,
      "trusted_paypal": 0,
      "trusted_amazon": 0,
      "trusted_localbitcoins": 0,
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "activeToRepaid": 16,
      "salary": null,
      "creditScore": "D4",
      "percentFunded": 9.1
    },
    {
      "id": 18243,
      "title": "INVEST ME (EARN REPUTATION AND FAST RETURN)",
      "description": "For building up reputation and investment purposes.",
      "borrower": 13065,
      "amount": "1.50000000",
      "frequency": 30,
      "term": 30,
      "expirationDate": "2015-09-12T23:59:59Z",
      "type": "BLC Investing",
      "denomination": "BTC",
      "status": "Funding",
      "paymentStatus": "Current",
      "createdAt": "2015-09-05T05:07:02Z",
      "countryId": "MY",
      "rating": 0,
      "social_facebook": 1,
      "social_linkedin": 1,
      "social_google": 0,
      "social_twitter": 0,
      "trusted_paypal": 0,
      "trusted_amazon": 0,
      "trusted_localbitcoins": 0,
      "trusted_ebay": 0,
      "trusted_coinbase": 0,
      "activeToRepaid": 100,
      "salary": "Under $10000",
      "creditScore": "D2",
      "percentFunded": 4.4,
      "votes": "2"
    }
  ]
}
TAG;
    }

    public function tearDown()
    {
        $this->input = null;
    }

    /**
     * @covers ::BLC\haveChecked
     */
    function testCheckSha1Basic()
    {
        $data = $this->getMockBuilder("BLC\\Config\\Data")->disableOriginalConstructor()->getMock();
        $data->method("getLastBorrowerSHA1")->willReturn(sha1(json_encode($this->input)));
        $this->assertThat(haveChecked($this->input, $data), $this->equalTo(true));
    }

    /**
     * @covers ::BLC\haveChecked
     */
    function testCheckSha1Set()
    {
        $data = $this->getMockBuilder("BLC\\Config\\Data")->disableOriginalConstructor()->getMock();
        $data->method("getLastBorrowerSHA1")->willReturn("");
        $data->expects($this->once())->method("setLastBorrowerSHA1")->with($this->equalTo(new String(sha1(json_encode($this->input)))));

        $this->assertThat(haveChecked($this->input, $data), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\haveChecked
     */
    function testCheckSha1Nothing()
    {
        $data = $this->getMockBuilder("BLC\\Config\\Data")->disableOriginalConstructor()->getMock();
        $this->assertThat(haveChecked($this->input, $data), $this->equalTo(false));
    }

    /**
     * @covers ::BLC\extractRelevant
     */
    function testExtractRelevantBasic()
    {
        $dataFactory1 = new LoanFactory(new Numeric(5), new Integer(6), new String("A"), new String("B"));
        $dataFactory2 = new LoanFactory(new Numeric(7), new Integer(8), new String("C"), new String("D"));
        $dataFactory3 = new LoanFactory(new Numeric(8), new Integer(9), new String("E"), new String("F"));
        $loans = new Loans($dataFactory1->build(), $dataFactory2->build(), $dataFactory3->build());

        $int_ints = extractRelevant($loans);

        $this->assertThat($int_ints[0]->getFirst(), $this->equalTo(new Integer(6)));
        $this->assertThat($int_ints[0]->getSecond(), $this->equalTo(new Numeric(5)));
        $this->assertThat($int_ints[1]->getFirst(), $this->equalTo(new Integer(8)));
        $this->assertThat($int_ints[1]->getSecond(), $this->equalTo(new Numeric(7)));
        $this->assertThat($int_ints[2]->getFirst(), $this->equalTo(new Integer(9)));
        $this->assertThat($int_ints[2]->getSecond(), $this->equalTo(new Numeric(8)));
    }

    /**
     * @covers ::BLC\extractRelevant
     */
    function testExtractRelevantEmpty()
    {
        $loans = new Loans();

        $result = extractRelevant($loans);

        $this->assertThat($result, $this->isEmpty());
    }

    /**
     * @covers ::BLC\findMatchingBorrowers
     */
    function testFindMatchingBorrowersSimple()
    {
        $ids = new IntegerList(5, 6, 7);
        $loanFac1 = new LoanFactory(new Numeric(1), new Integer(5), new String(""), new String(""));
        $loanFac2 = new LoanFactory(new Numeric(3), new Integer(12), new String(""), new String(""));
        $loanFac3 = new LoanFactory(new Numeric(2), new Integer(6), new String(""), new String(""));
        $loanFac4 = new LoanFactory(new Numeric(4), new Integer(13), new String(""), new String(""));
        $loans = new Loans($loanFac1->build(), $loanFac2->build(), $loanFac3->build(), $loanFac4->build());

        $loansArray = findMatchingBorrowers($loans, $ids);

        $this->assertThat($loansArray[0]->getId(), $this->equalTo(new Numeric(1)));
        $this->assertThat($loansArray[1]->getId(), $this->equalTo(new Numeric(2)));
        $this->assertThat(count($loansArray), $this->equalTo(2));
    }

    /**
     * @covers ::BLC\findMatchingBorrowers
     */
    function testFindMatchingBorrowersEmptyIds()
    {
        $ids = new IntegerList();
        $loanFac1 = new LoanFactory(new Numeric(1), new Integer(5), new String(""), new String(""));
        $loanFac2 = new LoanFactory(new Numeric(3), new Integer(12), new String(""), new String(""));
        $loanFac3 = new LoanFactory(new Numeric(2), new Integer(6), new String(""), new String(""));
        $loanFac4 = new LoanFactory(new Numeric(4), new Integer(13), new String(""), new String(""));
        $loans = new Loans($loanFac1->build(), $loanFac2->build(), $loanFac3->build(), $loanFac4->build());

        $loansArray = findMatchingBorrowers($loans, $ids);

        $this->assertThat($loansArray, $this->isEmpty());
    }

    /**
     * @covers ::BLC\findMatchingBorrowers
     */
    function testFindMatchingBorrowersEmptyLoans()
    {
        $ids = new IntegerList(1, 3, 2);
        $loans = new Loans();

        $loansArray = findMatchingBorrowers($loans, $ids);

        $this->assertThat($loansArray, $this->isEmpty());
    }

    /**
     * @covers ::BLC\findMatchingBorrowers
     */
    function testFindMatchingBorrowersEmptyBoth()
    {
        $ids = new IntegerList();
        $loans = new Loans();

        $loansArray = findMatchingBorrowers($loans, $ids);

        $this->assertThat($loansArray, $this->isEmpty());
    }

    /**
     * @covers ::BLC\makeLoans
     */
    public function testMakeLoansBasic()
    {

        $results = makeLoans(new JSON($this->loansData));
        $this->assertThat($results instanceof Loans, $this->equalTo(true));
        $this->assertThat(count($results), $this->equalTo(10));
    }

    /**
     * @covers ::BLC\makeLoans
     */
    public function testMakeLoansEmpty()
    {
        $json = new JSON('{"loans" : []}');
        $results = makeLoans($json);
        $this->assertThat($results, $this->isEmpty());
    }

    /**
     * @covers ::BLC\makeLoans
     */
    public function testMakeLoansNoKey()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $json = new JSON('{}');
        $results = makeLoans($json);
    }

    /**
     * @covers ::BLC\buildInvestmentCheckPromises
     */
    public function testBuildPromises()
    {
        $payload1 = new \stdClass();
        $payload1->loans = [json_decode($this->loanFunding1String)];

        $payload2 = new \stdClass();
        $payload2->loans = [json_decode($this->loanFunding2String)];

        $responses = [
            new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($payload1))),
            new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($payload2)))
        ];

        $handler = HandlerStack::create(new MockHandler($responses));
        $client = new Client(['handler' => $handler]);


        $investments = json_decode('[{"loanID": 16625,"amount": "0.0001","maxRate": "10"},{"loanID":' .
            '16553,"amount": "0.0001", "maxRate": "9"}]');
        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $config->expects($this->once())
            ->method("getManualInvestments")
            ->will($this->returnValue($investments));


        $queue = $this->getMockBuilder(WorkQueue::class)->disableOriginalConstructor()->getMock();
        $queue->expects($this->exactly(2))->
        method("enqueue")->
        withConsecutive(
            new WorkItem(new Numeric($investments[0]->loanID), new NumericString($investments[0]->amount), null, new NumericString($investments[0]->maxRate)),
            new WorkItem(new Numeric($investments[1]->loanID), new NumericString($investments[1]->amount), null, new NumericString($investments[1]->maxRate))
        );
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $promises = buildInvestmentCheckPromises($config, $client, $queue, $logger);
        \GuzzleHttp\Promise\all($promises)->wait();
    }

    /**
     * @covers ::BLC\buildInvestmentCheckPromises
     */
    public function testBuildPromisesFail()
    {
        $payload1 = new \stdClass();
        $payload1->loans = [json_decode($this->loanFunding1String)];

        $payload2 = new \stdClass();
        $payload2->loans = [json_decode($this->loanFunding2String)];


        $responses = [
            new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($payload1))),
            new Response(400),
            new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($payload2)))
        ];

        $handler = HandlerStack::create(new MockHandler($responses));
        $client = new Client(['handler' => $handler]);


        $investments = json_decode('[{"loanID": 16625,"amount": "0.0001","maxRate": "10"},{"loanID": 1,"amount": "1","maxRate": "1"},{"loanID":16553,"amount": "0.0001", "maxRate": "9"}]');
        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $config->expects($this->once())
            ->method("getManualInvestments")
            ->will($this->returnValue($investments));


        $queue = $this->getMockBuilder(WorkQueue::class)->disableOriginalConstructor()->getMock();
        $queue->expects($this->exactly(2))->
        method("enqueue")->
        withConsecutive(
            new WorkItem(new Numeric($investments[0]->loanID), new NumericString($investments[0]->amount), null, new NumericString($investments[0]->maxRate)),
            new WorkItem(new Numeric($investments[1]->loanID), new NumericString($investments[1]->amount), null, new NumericString($investments[1]->maxRate))
        );
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $promises = buildInvestmentCheckPromises($config, $client, $queue, $logger);
        \GuzzleHttp\Promise\all($promises)->wait();
    }

    /**
     * @covers ::BLC\buildInvestmentCheckPromises
     */
    public function testBuildPromisesBadLoan()
    {
        $payload1 = new \stdClass();
        $payload1->loans = [json_decode($this->loanFunding1String)];

        $payload2 = new \stdClass();
        $payload2->loans = [json_decode($this->loanFundedString)];

        $payload3 = new \stdClass();
        $payload3->loans = [json_decode($this->loanFunding2String)];


        $responses = [
            new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($payload1))),
            new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($payload2))),
            new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($payload3)))
        ];

        $handler = HandlerStack::create(new MockHandler($responses));
        $client = new Client(['handler' => $handler]);


        $investments = json_decode('[{"loanID": 16625,"amount": "0.0001","maxRate": "10"},{"loanID": 1,"amount": "1","maxRate": "1"},{"loanID":16553,"amount": "0.0001", "maxRate": "9"}]');
        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $config->expects($this->once())
            ->method("getManualInvestments")
            ->will($this->returnValue($investments));


        $queue = $this->getMockBuilder(WorkQueue::class)->disableOriginalConstructor()->getMock();
        $queue->expects($this->exactly(2))->
        method("enqueue")->
        withConsecutive(
            new WorkItem(new Numeric($investments[0]->loanID), new NumericString($investments[0]->amount), null, new NumericString($investments[0]->maxRate)),
            new WorkItem(new Numeric($investments[1]->loanID), new NumericString($investments[1]->amount), null, new NumericString($investments[1]->maxRate))
        );
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $promises = buildInvestmentCheckPromises($config, $client, $queue, $logger);
        \GuzzleHttp\Promise\all($promises)->wait();
    }

    /**
     * @covers ::BLC\buildInvestmentCheckPromises
     */
    public function testBuildPromisesAllFail()
    {
        $responses = [
            new Response(400),
            new Response(400),
            new Response(400)
        ];

        $handler = HandlerStack::create(new MockHandler($responses));
        $client = new Client(['handler' => $handler]);


        $investments = json_decode('[{"loanID": 16625,"amount": "0.0001","maxRate": "10"},{"loanID": 1,"amount": "1","maxRate": "1"},{"loanID":16553,"amount": "0.0001", "maxRate": "9"}]');
        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $config->expects($this->once())
            ->method("getManualInvestments")
            ->will($this->returnValue($investments));


        $queue = $this->getMockBuilder(WorkQueue::class)->disableOriginalConstructor()->getMock();
        $queue->expects($this->never())->method("enqueue");
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $promises = buildInvestmentCheckPromises($config, $client, $queue, $logger);
        \GuzzleHttp\Promise\all($promises)->wait();
    }

    /**
     * @covers ::BLC\buildInvestmentCheckPromises
     */
    public function testBuildPromisesEmpty()
    {
        $handler = HandlerStack::create(new MockHandler([]));
        $client = new Client(['handler' => $handler]);

        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $config->expects($this->once())
            ->method("getManualInvestments")
            ->will($this->returnValue([]));


        $queue = $this->getMockBuilder(WorkQueue::class)->disableOriginalConstructor()->getMock();
        $queue->expects($this->never())->method("enqueue");
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $this->assertThat(buildInvestmentCheckPromises($config, $client, $queue, $logger), $this->isEmpty());
    }

    /**
     * @covers ::BLC\buildInvestmentCheckPromises
     */
    public function testBuildPromisesBadPayLoadMissingKey()
    {
        $payload1 = new \stdClass();
        $payload1->loans = [json_decode($this->loanFunding1String)];

        $payload2 = new \stdClass();
        $payload2->loan = [json_decode($this->loanFundedString)];

        $payload3 = new \stdClass();
        $payload3->loans = [json_decode($this->loanFunding2String)];


        $responses = [
            new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($payload1))),
            new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($payload2))),
            new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($payload3)))
        ];

        $handler = HandlerStack::create(new MockHandler($responses));
        $client = new Client(['handler' => $handler]);


        $investments = json_decode('[{"loanID": 16625,"amount": "0.0001","maxRate": "10"},{"loanID": 1,"amount": "1","maxRate": "1"},{"loanID":16553,"amount": "0.0001", "maxRate": "9"}]');
        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $config->expects($this->once())
            ->method("getManualInvestments")
            ->will($this->returnValue($investments));


        $queue = $this->getMockBuilder(WorkQueue::class)->disableOriginalConstructor()->getMock();
        $queue->expects($this->exactly(2))->
        method("enqueue")->
        withConsecutive(
            new WorkItem(new Numeric($investments[0]->loanID), new NumericString($investments[0]->amount), null, new NumericString($investments[0]->maxRate)),
            new WorkItem(new Numeric($investments[1]->loanID), new NumericString($investments[1]->amount), null, new NumericString($investments[1]->maxRate))
        );
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $promises = buildInvestmentCheckPromises($config, $client, $queue, $logger);
        $this->setExpectedException(InvalidArgumentException::class);
        \GuzzleHttp\Promise\all($promises)->wait();
    }

    /**
     * @covers ::BLC\buildInvestmentCheckPromises
     */
    public function testBuildPromisesBadPayLoadBadValue()
    {
        $payload1 = new \stdClass();
        $payload1->loans = [json_decode($this->loanFunding1String)];

        $payload2 = new \stdClass();
        $payload2->loans = json_decode($this->loanFundedString);

        $payload3 = new \stdClass();
        $payload3->loans = [json_decode($this->loanFunding2String)];


        $responses = [
            new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($payload1))),
            new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($payload2))),
            new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($payload3)))
        ];

        $handler = HandlerStack::create(new MockHandler($responses));
        $client = new Client(['handler' => $handler]);


        $investments = json_decode('[{"loanID": 16625,"amount": "0.0001","maxRate": "10"},{"loanID": 1,"amount": "1","maxRate": "1"},{"loanID":16553,"amount": "0.0001", "maxRate": "9"}]');
        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $config->expects($this->once())
            ->method("getManualInvestments")
            ->will($this->returnValue($investments));


        $queue = $this->getMockBuilder(WorkQueue::class)->disableOriginalConstructor()->getMock();
        $queue->expects($this->exactly(2))->
        method("enqueue")->
        withConsecutive(
            new WorkItem(new Numeric($investments[0]->loanID), new NumericString($investments[0]->amount), null, new NumericString($investments[0]->maxRate)),
            new WorkItem(new Numeric($investments[1]->loanID), new NumericString($investments[1]->amount), null, new NumericString($investments[1]->maxRate))
        );
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $promises = buildInvestmentCheckPromises($config, $client, $queue, $logger);
        $this->setExpectedException(InvalidArgumentException::class);
        \GuzzleHttp\Promise\all($promises)->wait();
    }

    /**
     * @covers ::BLC\buildInvestmentCheckPromises
     */
    public function testBuildPromisesBadPayLoadEmptyLoans()
    {
        $payload1 = new \stdClass();
        $payload1->loans = [json_decode($this->loanFunding1String)];

        $payload2 = new \stdClass();
        $payload2->loans = [];

        $payload3 = new \stdClass();
        $payload3->loans = [json_decode($this->loanFunding2String)];


        $responses = [
            new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($payload1))),
            new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($payload2))),
            new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($payload3)))
        ];

        $handler = HandlerStack::create(new MockHandler($responses));
        $client = new Client(['handler' => $handler]);


        $investments = json_decode('[{"loanID": 16625,"amount": "0.0001","maxRate": "10"},{"loanID": 1,"amount": "1","maxRate": "1"},{"loanID":16553,"amount": "0.0001", "maxRate": "9"}]');
        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $config->expects($this->once())
            ->method("getManualInvestments")
            ->will($this->returnValue($investments));


        $queue = $this->getMockBuilder(WorkQueue::class)->disableOriginalConstructor()->getMock();
        $queue->expects($this->exactly(2))->
        method("enqueue")->
        withConsecutive(
            new WorkItem(new Numeric($investments[0]->loanID), new NumericString($investments[0]->amount), null, new NumericString($investments[0]->maxRate)),
            new WorkItem(new Numeric($investments[1]->loanID), new NumericString($investments[1]->amount), null, new NumericString($investments[1]->maxRate))
        );
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $promises = buildInvestmentCheckPromises($config, $client, $queue, $logger);
        $this->setExpectedException(InvalidArgumentException::class);
        \GuzzleHttp\Promise\all($promises)->wait();
    }

    /**
     * @covers ::BLC\buildInvestmentCheckPromises
     */
    public function testBuildPromisesBadPayloadBadLoan()
    {
        $payload1 = new \stdClass();
        $payload1->loans = [json_decode($this->loanFunding1String)];

        $payload2 = new \stdClass();
        $loans2 = json_decode($this->loanFunding1String);
        $loans2->description = [];
        $payload2->loans = [$loans2];

        $payload3 = new \stdClass();
        $payload3->loans = [json_decode($this->loanFunding2String)];


        $responses = [
            new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($payload1))),
            new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($payload2))),
            new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($payload3)))
        ];

        $handler = HandlerStack::create(new MockHandler($responses));
        $client = new Client(['handler' => $handler]);


        $investments = json_decode('[{"loanID": 16625,"amount": "0.0001","maxRate": "10"},{"loanID": 1,"amount": "1","maxRate": "1"},{"loanID":16553,"amount": "0.0001", "maxRate": "9"}]');
        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $config->expects($this->once())
            ->method("getManualInvestments")
            ->will($this->returnValue($investments));


        $queue = $this->getMockBuilder(WorkQueue::class)->disableOriginalConstructor()->getMock();
        $queue->expects($this->exactly(2))->
        method("enqueue")->
        withConsecutive(
            new WorkItem(new Numeric($investments[0]->loanID), new NumericString($investments[0]->amount), null, new NumericString($investments[0]->maxRate)),
            new WorkItem(new Numeric($investments[1]->loanID), new NumericString($investments[1]->amount), null, new NumericString($investments[1]->maxRate))
        );
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $promises = buildInvestmentCheckPromises($config, $client, $queue, $logger);
        $this->setExpectedException(InvalidArgumentException::class);
        \GuzzleHttp\Promise\all($promises)->wait();
    }


    /**
     * @covers ::BLC\reputationRuleFunction
     */
    public function testReputationRuleFunction()
    {
        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $queue = $this->getMockBuilder(WorkQueue::class)->disableOriginalConstructor()->getMock();
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $loan1 = json_decode($this->loanFunding1String);
        $loan2 = json_decode($this->loanFunding2String);

        $data = new \stdClass();
        $data->loans = [
            $loan1,
            $loan2,
        ];

        $investmentAmount = "64";

        $config->expects($this->any())
            ->method("getAutoInvestAmount")
            ->will($this->returnValue($investmentAmount));


        $queue->expects($this->exactly(2))->
        method("enqueue")->
        withConsecutive(
            $this->equalTo(new WorkItem(new Numeric($loan1->id), new NumericString($investmentAmount))),
            $this->equalTo(new WorkItem(new Numeric($loan2->id), new NumericString($investmentAmount)))
        );

        $function = reputationRuleFunction($config, $queue, $logger);
        $response = new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($data)));
        $function($response);
    }

    /**
     * @covers ::BLC\reputationRuleFunction
     */
    public function testReputationRuleFunctionBadLoansKey()
    {
        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $queue = $this->getMockBuilder(WorkQueue::class)->disableOriginalConstructor()->getMock();
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $loan1 = json_decode($this->loanFunding1String);
        $loan2 = json_decode($this->loanFunding2String);

        $data = new \stdClass();
        $data->loan = [
            $loan1,
            $loan2,
        ];

        $investmentAmount = "64";

        $config->expects($this->any())
            ->method("getAutoInvestAmount")
            ->will($this->returnValue($investmentAmount));


        $queue->expects($this->never())->
        method("enqueue");

        $function = reputationRuleFunction($config, $queue, $logger);
        $response = new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($data)));
        $this->setExpectedException(InvalidArgumentException::class);
        $function($response);
    }

    /**
     * @covers ::BLC\reputationRuleFunction
     */
    public function testReputationRuleFunctionBadLoansContent()
    {
        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $queue = $this->getMockBuilder(WorkQueue::class)->disableOriginalConstructor()->getMock();
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $loan1 = json_decode($this->loanFunding1String);
        $loan2 = json_decode($this->loanFunding2String);

        $data = new \stdClass();
        $data->loans = "ASB";

        $investmentAmount = "64";

        $config->expects($this->any())
            ->method("getAutoInvestAmount")
            ->will($this->returnValue($investmentAmount));


        $queue->expects($this->never())->
        method("enqueue");

        $function = reputationRuleFunction($config, $queue, $logger);
        $response = new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($data)));
        $this->setExpectedException(InvalidArgumentException::class);
        $function($response);
    }

    /**
     * @covers ::BLC\reputationRuleFunction
     */
    public function testReputationRuleFunctionBadLoanInArray()
    {
        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $queue = $this->getMockBuilder(WorkQueue::class)->disableOriginalConstructor()->getMock();
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $loan1 = json_decode($this->loanFunding1String);
        $loan2 = json_decode($this->loanFunding2String);


        $loan1->description = false;

        $data = new \stdClass();
        $data->loans = [
            $loan1,
            $loan2,
        ];

        $investmentAmount = "64";

        $config->expects($this->any())
            ->method("getAutoInvestAmount")
            ->will($this->returnValue($investmentAmount));


        $queue->expects($this->once())->
        method("enqueue")->with(
            $this->equalTo(new WorkItem(new Numeric($loan2->id), new NumericString($investmentAmount)))
        );

        $function = reputationRuleFunction($config, $queue, $logger);
        $response = new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($data)));
        $function($response);
    }

    /**
     * @covers ::BLC\reputationRuleFunction
     */
    public function testReputationRuleFunctionEmptyArray()
    {
        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $queue = $this->getMockBuilder(WorkQueue::class)->disableOriginalConstructor()->getMock();
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $loan1 = json_decode($this->loanFunding1String);
        $loan2 = json_decode($this->loanFunding2String);


        $loan1->description = false;

        $data = new \stdClass();
        $data->loans = [
        ];

        $investmentAmount = "64";

        $config->expects($this->any())
            ->method("getAutoInvestAmount")
            ->will($this->returnValue($investmentAmount));


        $queue->expects($this->never())->
        method("enqueue");
        $function = reputationRuleFunction($config, $queue, $logger);
        $response = new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($data)));
        $function($response);
    }

    /**
     * @covers ::BLC\reputationRuleFunction
     */
    public function testReputationRuleFunctionBadResponse()
    {
        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $queue = $this->getMockBuilder(WorkQueue::class)->disableOriginalConstructor()->getMock();
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $investmentAmount = "64";

        $config->expects($this->any())
            ->method("getAutoInvestAmount")
            ->will($this->returnValue($investmentAmount));


        $queue->expects($this->never())->
        method("enqueue");
        $function = reputationRuleFunction($config, $queue, $logger);
        $response = new Response(400);
        $this->setExpectedException(InvalidArgumentException::class);
        $function($response);
    }

    /**
     * @covers ::BLC\getValidLoans
     */
    public function testValidLoad()
    {
        $response = new Response(200, [], \GuzzleHttp\Psr7\stream_for($this->loansData));

        $data = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $data->expects($this->any())->method("getLastBorrowerSHA1")->willReturn("");
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $loans = getValidLoans($response, $data, $logger);

        $this->assertThat(count($loans), $this->equalTo(10));
        $this->assertThat($loans instanceof Loans, $this->equalTo(true));
    }

    /**
     * @covers ::BLC\getValidLoans
     */
    public function testEmptyLoad()
    {
        $response = new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode("{}")));

        $data = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $data->expects($this->any())->method("getLastBorrowerSHA1")->willReturn("");

        $loans = getValidLoans($response, $data, $logger);

        $this->assertThat(count($loans), $this->equalTo(0));
        $this->assertThat($loans instanceof Loans, $this->equalTo(true));
    }

    /**
     * @covers ::BLC\getValidLoans
     */
    public function testNoLoans()
    {
        $response = new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode('{"loans":[]}')));

        $data = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $data->expects($this->any())->method("getLastBorrowerSHA1")->willReturn("");

        $loans = getValidLoans($response, $data, $logger);

        $this->assertThat(count($loans), $this->equalTo(0));
        $this->assertThat($loans instanceof Loans, $this->equalTo(true));
    }

    /**
     * @covers ::BLC\getValidLoans
     */
    public function testCheckSHA1Fail()
    {
        //Construct several dummy loans....
        $sha1Data = [new Int_Numeric(new Integer(1), new Numeric(2)), new Int_Numeric(new Integer(3), new Numeric(4)), new Int_Numeric(new Integer(5), new Numeric(6))];
        $loanFactory1 = new LoanFactory(new Numeric(1), new Integer(2), new String(""), new String(""));
        $loanFactory2 = new LoanFactory(new Numeric(3), new Integer(4), new String(""), new String(""));
        $loanFactory3 = new LoanFactory(new Numeric(5), new Integer(6), new String(""), new String(""));
        $loan1 = $loanFactory1->build();
        $loan2 = $loanFactory2->build();
        $loan3 = $loanFactory3->build();

        $payload = new \stdClass();
        $payload->loans = [$loan1, $loan2, $loan3];

        $response = new Response(200, [], \GuzzleHttp\Psr7\stream_for(json_encode($payload)));

        $data = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $data->expects($this->any())->method("getLastBorrowerSHA1")->willReturn(sha1(json_encode($sha1Data)));

        $loans = getValidLoans($response, $data, $logger);

        $this->assertThat(count($loans), $this->equalTo(0));
        $this->assertThat($loans instanceof Loans, $this->equalTo(true));
    }


    /**
     * @covers ::BLC\processBorrowerRequest
     */
    public function testProcessBorrowersRequest()
    {
        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $data = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $queue = $this->getMockBuilder(WorkQueue::class)->disableOriginalConstructor()->getMock();


        $borrowerMap = new IntegerStringMap([]);
        $borrowerMap[10765] = "1";
        $borrowerMap[2844] = "2";
        $borrowerMap[13065] = "3";

        $config->expects($this->once())->method("getBorrowersMap")->willReturn($borrowerMap);

        $borrowersList = new IntegerList();
        $borrowersList[] = 10765;
        $borrowersList[] = 2844;
        $borrowersList[] = 13065;

        $config->expects($this->once())->method("getBorrowersList")->willReturn($borrowersList);

        $data->expects($this->any())->method("getLastBorrowerSHA1")->willReturn("");

        $queue->expects($this->exactly(3))->method("enqueue")->
        withConsecutive(
            new WorkItem(new Numeric(18246), new NumericString("1")), //BORROWER ID 10765
            new WorkItem(new Numeric(18242), new NumericString("2")), //BORROWER ID 2844
            new WorkItem(new Numeric(18243), new NumericString("3"))  //BORROWER ID 13065
        );

        $response = new Response(200, [], $this->realData);

        $function = processBorrowerRequest($data, $config, $queue, $logger);

        $function($response);
    }

    /**
     * @covers ::BLC\processBorrowerRequest
     */
    public function testProcessNoBorrowers()
    {
        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $data = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $queue = $this->getMockBuilder(WorkQueue::class)->disableOriginalConstructor()->getMock();


        $borrowerMap = new IntegerStringMap([]);

        $config->expects($this->once())->method("getBorrowersMap")->willReturn($borrowerMap);

        $borrowersList = new IntegerList();
        $config->expects($this->once())->method("getBorrowersList")->willReturn($borrowersList);

        $data->expects($this->any())->method("getLastBorrowerSHA1")->willReturn("");

        $queue->expects($this->never())->method("enqueue");

        $response = new Response(200, [], $this->realData);

        $function = processBorrowerRequest($data, $config, $queue, $logger);

        $function($response);
    }

    /**
     * @covers ::BLC\processBorrowerRequest
     */
    public function testPorcessBorrowerRequestNoLoans()
    {
        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $data = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $queue = $this->getMockBuilder(WorkQueue::class)->disableOriginalConstructor()->getMock();


        $borrowerMap = new IntegerStringMap([]);

        $config->expects($this->once())->method("getBorrowersMap")->willReturn($borrowerMap);

        $borrowersList = new IntegerList();

        $config->expects($this->once())->method("getBorrowersList")->willReturn($borrowersList);

        $data->expects($this->any())->method("getLastBorrowerSHA1")->willReturn("");

        $queue->expects($this->never())->method("enqueue");

        $response = new Response(200, [], '{"loans":[]}');

        $function = processBorrowerRequest($data, $config, $queue, $logger);

        $function($response);
    }

    /**
     * @covers ::\BLC\makeLoan
     */
    public function testMakeLoanWithoutPercentFunded()
    {
        $loan = new \stdClass();
        $loan->id = 18400;
        $loan->frequency = 30;
        $loan->term = 270;
        $loan->rating = 5;
        $loan->social_facebook = 1;
        $loan->social_linkedin = 0;
        $loan->social_google = 1;
        $loan->social_twitter = 0;
        $loan->trusted_paypal = 1;
        $loan->trusted_amazon = 0;
        $loan->trusted_localbitcoins = 0;
        $loan->trusted_ebay = 0;
        $loan->activeToRepaid = 0;
        $loan->borrower = 5480;
        $loan->amount = "2.00000000";
        $loan->title = "dept consolidation";
        $loan->description = "loan for dept consolidation";
        $loan->expirationDate = "2015-10-09T23:59:59Z";
        $loan->type = "Debt Consolidation";
        $loan->denomination = "BTC";
        $loan->status = "Canceled";
        $loan->paymentStatus = "Current";
        $loan->createdAt = "2015-09-09T10:14:50Z";
        $loan->countryId = "PL";
        $loan->salary = "Under $10000";
        $loan->creditScore = "D2";

        $loanFactory = new LoanFactory(new Numeric($loan->id), new Integer($loan->borrower), new String($loan->type), new String($loan->denomination));


        date_default_timezone_set("UTC");
        $expirationDate = DateTime::createFromFormat(DATE_ISO8601, "2015-10-09T23:59:59Z");
        $createdAt = DateTime::createFromFormat(DATE_ISO8601, "2015-09-09T10:14:50Z");
        $loanFactory->setFrequency(new Integer(30));
        $loanFactory->setTerm(new Integer(270));
        $loanFactory->setRating(new Integer(5));
        $loanFactory->setActiveToRepaid(new Float((float)0));
        $loanFactory->setTrustedebay(new Boolean(false));
        $loanFactory->setSocialFacebook(new Boolean(true));
        $loanFactory->setSocialLinkedin(new Boolean(false));
        $loanFactory->setSocialGoogle(new Boolean(true));
        $loanFactory->setSocialTwitter(new Boolean(false));
        $loanFactory->setTrustedPaypal(new Boolean(true));
        $loanFactory->setTrustedAmazon(new Boolean(false));
        $loanFactory->setTrustedLocalbitcoins(new Boolean(false));
        $loanFactory->setExpirationDate($expirationDate);
        $loanFactory->setCreatedAt($createdAt);
        $loanFactory->setDescription(new String("loan for dept consolidation"));
        $loanFactory->setAmount(new String("2.00000000"));
        $loanFactory->setTitle(new String("dept consolidation"));
        $loanFactory->setStatus(new String("Canceled"));
        $loanFactory->setPaymentStatus(new String("Current"));
        $loanFactory->setCountryId(new String("PL"));
        $loanFactory->setSalary(new String("Under $10000"));
        $loanFactory->setCreditScore(new String("D2"));


        //Adding this because the makeLoan function has a different default than the factory function
        $loanFactory->setVotes(new Integer(0));

        $this->assertThat(makeLoan($loan), $this->equalTo($loanFactory->build()));
    }

    /**
     * @covers ::BLC\makeLoan
     */
    public function testBadLoan()
    {
        $handle = fopen(__DIR__ . "/realDataLoan.log", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $line = json_decode($line);
                makeLoan($line->context->loan);
            }
        } else {
            $this->fail("Couldn't get file handle");
        }
        fclose($handle);
        $this->assertThat(true, $this->equalTo(true));
    }

    /**
     * @covers ::BLC\makeLoans
     */
    public function testBadLoans()
    {
        $handle = fopen(__DIR__ . "/realDataLoans.log", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $line = json_decode($line);
                makeLoans(new JSON(json_encode($line->context->json)));
            }
        } else {
            $this->fail("Couldn't get file handle");
        }
        fclose($handle);
        $this->assertThat(true, $this->equalTo(true));
    }

    /**
     * @covers ::\BLC\getValidLoans
     */
    public function testNoValidLoansAllInCache()
    {

        $response = new Response(200, [], \GuzzleHttp\Psr7\stream_for($this->loansData));

        $data = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $data->expects($this->any())->method("getLastBorrowerSHA1")->willReturn(sha1(json_encode(extractRelevant(makeLoans(new JSON($this->loansData))))));
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $loans = getValidLoans($response, $data, $logger);

        $this->assertThat(count($loans), $this->equalTo(0));
        $this->assertThat($loans instanceof Loans, $this->equalTo(true));

    }
}
