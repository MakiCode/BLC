<?php
/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 8/28/15
 * Time: 1:52 PM
 */

namespace BLC\Model;

use DateInterval;
use DateTime;
use GuzzleHttp\Tests\Psr7\Str;
use PHPUnit_Framework_TestCase;
use Types\Boolean;
use Types\Float;
use Types\Integer;
use Types\Numeric;
use Types\String;

/**
 * @uses BLC\Model\Optional
 * @uses Types\Primitive
 * @uses Types\Integer
 * @uses Types\Boolean
 * @uses BLC\Model\Loan
 * @uses Types\String
 * @uses BLC\Model\OptionalDate
 * @uses BLC\Model\LoanFactory
 * @uses BLC\Model\OptionalBoolean
 * @uses BLC\Model\OptionalString
 * @uses BLC\Model\OptionalInteger
 */
class LoanFactoryTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers \BLC\Model\LoanFactory::setId
     * @covers \BLC\Model\LoanFactory::setType
     * @covers \BLC\Model\LoanFactory::setTitle
     * @covers \BLC\Model\LoanFactory::setDescription
     * @covers \BLC\Model\LoanFactory::setAmount
     * @covers \BLC\Model\LoanFactory::setTerm
     * @covers \BLC\Model\LoanFactory::setFrequency
     * @covers \BLC\Model\LoanFactory::setStatus
     * @covers \BLC\Model\LoanFactory::setPaymentStatus
     * @covers \BLC\Model\LoanFactory::setCreatedAt
     * @covers \BLC\Model\LoanFactory::setExpirationDate
     * @covers \BLC\Model\LoanFactory::setPaymentDueDate
     * @covers \BLC\Model\LoanFactory::setDateRepaid
     * @covers \BLC\Model\LoanFactory::setDenomination
     * @covers \BLC\Model\LoanFactory::setPercentFunded
     * @covers \BLC\Model\LoanFactory::setVotes
     * @covers \BLC\Model\LoanFactory::setBorrower
     * @covers \BLC\Model\LoanFactory::setCountryId
     * @covers \BLC\Model\LoanFactory::setSalary
     * @covers \BLC\Model\LoanFactory::setRating
     * @covers \BLC\Model\LoanFactory::setSocialFacebook
     * @covers \BLC\Model\LoanFactory::setSocialLinkedin
     * @covers \BLC\Model\LoanFactory::setSocialGoogle
     * @covers \BLC\Model\LoanFactory::setSocialTwitter
     * @covers \BLC\Model\LoanFactory::setTrustedPaypal
     * @covers \BLC\Model\LoanFactory::setTrustedAmazon
     * @covers \BLC\Model\LoanFactory::setTrustedLocalbitcoins
     * @covers \BLC\Model\LoanFactory::setTrustedEbay
     * @covers \BLC\Model\LoanFactory::setCoinbase
     * @covers \BLC\Model\LoanFactory::setActiveToRepaid
     * @covers \BLC\Model\LoanFactory::setCreditScore
     * @covers \BLC\Model\LoanFactory::__construct
     * @covers \BLC\Model\LoanFactory::build
     *
     */
    public function testBuildingWithTheFactory()
    {
        date_default_timezone_set("UTC");
        $baseDate = new DateTime("now");
        $date1 = $baseDate->add(new DateInterval("P1D"));
        $date2 = $baseDate->add(new DateInterval("P2D"));
        $date3 = $baseDate->add(new DateInterval("P3D"));
        $date4 = $baseDate->add(new DateInterval("P4D"));

        $factory = new LoanFactory(new Numeric(5), new Integer(12), new String("ABC"), new String("DEF"));
        $factory->setTerm(new Integer(13));
        $factory->setFrequency(new Integer(14));
        $factory->setPercentFunded(new Numeric(15));
        $factory->setVotes(new Integer(1));
        $factory->setRating(new Integer(19));
        $factory->setCreatedAt($date1);
        $factory->setExpirationDate($date2);
        $factory->setPaymentDueDate($date3);
        $factory->setDateRepaid($date4);
        $factory->setTitle(new String("A"));
        $factory->setDescription(new String("B"));
        $factory->setAmount(new String("C"));
        $factory->setStatus(new String("D"));
        $factory->setPaymentStatus(new String("E"));
        $factory->setCountryId(new String("F"));
        $factory->setSalary(new String("G"));
        $factory->setSocialFacebook(new Boolean(true));
        $factory->setSocialLinkedin(new Boolean(false));
        $factory->setSocialGoogle(new Boolean(true));
        $factory->setSocialTwitter(new Boolean(false));
        $factory->setTrustedPaypal(new Boolean(true));
        $factory->setTrustedAmazon(new Boolean (false));
        $factory->setTrustedLocalbitcoins(new Boolean(true));
        $factory->setTrustedEbay(new Boolean(false));
        $factory->setCoinbase(new Boolean(true));
        $factory->setActiveToRepaid(new Float((float)20));
        $factory->setCreditScore(new String("H"));
        $loan = $factory->build();

        $this->assertThat($loan->getId(), $this->equalTo(new Numeric(5)));
        $this->assertThat($loan->getType(), $this->equalTo(new String("ABC")));
        $this->assertThat($loan->getBorrower(), $this->equalTo(new Integer(12)));
        $this->assertThat($loan->getDenomination(), $this->equalTo(new String("DEF")));
        $this->assertThat($loan->getTitle(), $this->equalTo(new String("A")));
        $this->assertThat($loan->getDescription(), $this->equalTo(new String("B")));
        $this->assertThat($loan->getAmount(), $this->equalTo(new String("C")));
        $this->assertThat($loan->getTerm(), $this->equalTo(new Integer(13)));
        $this->assertThat($loan->getFrequency(), $this->equalTo(new Integer(14)));
        $this->assertThat($loan->getStatus(), $this->equalTo(new String("D")));
        $this->assertThat($loan->getPaymentStatus(), $this->equalTo(new String("E")));
        $this->assertThat($loan->getCreatedAt()->get(), $this->equalTo($date1));
        $this->assertThat($loan->getExpirationDate()->get(), $this->equalTo($date2));
        $this->assertThat($loan->getPaymentDueDate()->get(), $this->equalTo($date3));
        $this->assertThat($loan->getDateRepaid()->get(), $this->equalTo($date4));
        $this->assertThat($loan->getPercentFunded(), $this->equalTo(new Numeric(15)));
        $this->assertThat($loan->getVotes()->get(), $this->equalTo(new Integer(1)));
        $this->assertThat($loan->getCountryID(), $this->equalTo(new String("F")));
        $this->assertThat($loan->getSalary(), $this->equalTo(new String("G")));
        $this->assertThat($loan->getRating(), $this->equalTo(new Integer(19)));
        $this->assertThat($loan->isAmazon()->get(), $this->equalTo(new Boolean(false)));
        $this->assertThat($loan->isFacebook()->get(), $this->equalTo(new Boolean(true)));
        $this->assertThat($loan->isLinkedin()->get(), $this->equalTo(new Boolean(false)));
        $this->assertThat($loan->isGoogle()->get(), $this->equalTo(new Boolean(true)));
        $this->assertThat($loan->isTwitter()->get(), $this->equalTo(new Boolean(false)));
        $this->assertThat($loan->isPaypal()->get(), $this->equalTo(new Boolean(true)));
        $this->assertThat($loan->isLocalbitcoins()->get(), $this->equalTo(new Boolean(true)));
        $this->assertThat($loan->isEbay()->get(), $this->equalTo(new Boolean(false)));
        $this->assertThat($loan->isCoinbase()->get(), $this->equalTo(new Boolean(true)));
        $this->assertThat($loan->getCreditScore(), $this->equalTo(new String("H")));
        $this->assertThat($loan->getActiveToRepaid(), $this->equalTo(new Float((float)20)));
    }

    /**
     * @covers \BLC\Model\LoanFactory::setId
     * @covers \BLC\Model\LoanFactory::setType
     * @covers \BLC\Model\LoanFactory::setTitle
     * @covers \BLC\Model\LoanFactory::setDescription
     * @covers \BLC\Model\LoanFactory::setAmount
     * @covers \BLC\Model\LoanFactory::setTerm
     * @covers \BLC\Model\LoanFactory::setFrequency
     * @covers \BLC\Model\LoanFactory::setStatus
     * @covers \BLC\Model\LoanFactory::setPaymentStatus
     * @covers \BLC\Model\LoanFactory::setCreatedAt
     * @covers \BLC\Model\LoanFactory::setExpirationDate
     * @covers \BLC\Model\LoanFactory::setPaymentDueDate
     * @covers \BLC\Model\LoanFactory::setDateRepaid
     * @covers \BLC\Model\LoanFactory::setDenomination
     * @covers \BLC\Model\LoanFactory::setPercentFunded
     * @covers \BLC\Model\LoanFactory::setVotes
     * @covers \BLC\Model\LoanFactory::setBorrower
     * @covers \BLC\Model\LoanFactory::setCountryId
     * @covers \BLC\Model\LoanFactory::setSalary
     * @covers \BLC\Model\LoanFactory::setRating
     * @covers \BLC\Model\LoanFactory::setSocialFacebook
     * @covers \BLC\Model\LoanFactory::setSocialLinkedin
     * @covers \BLC\Model\LoanFactory::setSocialGoogle
     * @covers \BLC\Model\LoanFactory::setSocialTwitter
     * @covers \BLC\Model\LoanFactory::setTrustedPaypal
     * @covers \BLC\Model\LoanFactory::setTrustedAmazon
     * @covers \BLC\Model\LoanFactory::setTrustedLocalbitcoins
     * @covers \BLC\Model\LoanFactory::setTrustedEbay
     * @covers \BLC\Model\LoanFactory::setCoinbase
     * @covers \BLC\Model\LoanFactory::setActiveToRepaid
     * @covers \BLC\Model\LoanFactory::setCreditScore
     * @covers \BLC\Model\LoanFactory::__construct
     * @covers \BLC\Model\LoanFactory::build
     *
     */
    public function testModifyDefaults()
    {
        date_default_timezone_set("UTC");
        $baseDate = new DateTime("now");
        $date1 = $baseDate->add(new DateInterval("P1D"));
        $date2 = $baseDate->add(new DateInterval("P2D"));
        $date3 = $baseDate->add(new DateInterval("P3D"));
        $date4 = $baseDate->add(new DateInterval("P4D"));

        $factory = new LoanFactory(new Numeric(5), new Integer(12), new String("ABC"), new String("DEF"));

        $factory->setId(new Numeric(6));
        $factory->setType(new String("LMNO"));
        $factory->setDenomination(new String("GHI"));
        $factory->setBorrower(new Integer(7));

        $factory->setTerm(new Integer(13));
        $factory->setFrequency(new Integer(14));
        $factory->setPercentFunded(new Numeric(15));
        $factory->setVotes(new Integer(20));
        $factory->setRating(new Integer(19));
        $factory->setCreatedAt($date1);
        $factory->setExpirationDate($date2);
        $factory->setPaymentDueDate($date3);
        $factory->setDateRepaid($date4);
        $factory->setTitle(new String("A"));
        $factory->setDescription(new String("B"));
        $factory->setAmount(new String("C"));
        $factory->setStatus(new String("D"));
        $factory->setPaymentStatus(new String("E"));
        $factory->setCountryId(new String("F"));
        $factory->setSalary(new String("G"));
        $factory->setSocialFacebook(new Boolean(true));
        $factory->setSocialLinkedin(new Boolean(false));
        $factory->setSocialGoogle(new Boolean(true));
        $factory->setSocialTwitter(new Boolean(false));
        $factory->setTrustedPaypal(new Boolean(true));
        $factory->setTrustedAmazon(new Boolean (false));
        $factory->setTrustedLocalbitcoins(new Boolean(true));
        $factory->setTrustedEbay(new Boolean(false));
        $factory->setCoinbase(new Boolean(true));
        $factory->setActiveToRepaid(new Float((float)20));
        $factory->setCreditScore(new String("H"));
        $loan = $factory->build();

        $this->assertThat($loan->getId(), $this->equalTo(new Numeric(6)));
        $this->assertThat($loan->getType(), $this->equalTo(new String("LMNO")));
        $this->assertThat($loan->getBorrower(), $this->equalTo(new Integer(7)));
        $this->assertThat($loan->getDenomination(), $this->equalTo(new String("GHI")));

        $this->assertThat($loan->getTitle(), $this->equalTo(new String("A")));
        $this->assertThat($loan->getDescription(), $this->equalTo(new String("B")));
        $this->assertThat($loan->getAmount(), $this->equalTo(new String("C")));
        $this->assertThat($loan->getTerm(), $this->equalTo(new Integer(13)));
        $this->assertThat($loan->getFrequency(), $this->equalTo(new Integer(14)));
        $this->assertThat($loan->getStatus(), $this->equalTo(new String("D")));
        $this->assertThat($loan->getPaymentStatus(), $this->equalTo(new String("E")));
        $this->assertThat($loan->getCreatedAt(), $this->equalTo(new OptionalDate($date1)));
        $this->assertThat($loan->getExpirationDate(), $this->equalTo(new OptionalDate($date2)));
        $this->assertThat($loan->getPaymentDueDate(), $this->equalTo(new OptionalDate($date3)));
        $this->assertThat($loan->getDateRepaid(), $this->equalTo(new OptionalDate($date4)));
        $this->assertThat($loan->getPercentFunded(), $this->equalTo(new Numeric(15)));
        $this->assertThat($loan->getVotes()->get(), $this->equalTo(new Integer(20)));
        $this->assertThat($loan->getCountryID(), $this->equalTo(new String("F")));
        $this->assertThat($loan->getSalary(), $this->equalTo(new String("G")));
        $this->assertThat($loan->getRating(), $this->equalTo(new Integer(19)));
        $this->assertThat($loan->isAmazon()->get(), $this->equalTo(new Boolean(false)));
        $this->assertThat($loan->isFacebook()->get(), $this->equalTo(new Boolean(true)));
        $this->assertThat($loan->isLinkedin()->get(), $this->equalTo(new Boolean(false)));
        $this->assertThat($loan->isGoogle()->get(), $this->equalTo(new Boolean(true)));
        $this->assertThat($loan->isTwitter()->get(), $this->equalTo(new Boolean(false)));
        $this->assertThat($loan->isPaypal()->get(), $this->equalTo(new Boolean(true)));
        $this->assertThat($loan->isLocalbitcoins()->get(), $this->equalTo(new Boolean(true)));
        $this->assertThat($loan->isEbay()->get(), $this->equalTo(new Boolean(false)));
        $this->assertThat($loan->isCoinbase()->get(), $this->equalTo(new Boolean(true)));
        $this->assertThat($loan->getCreditScore(), $this->equalTo(new String("H")));
        $this->assertThat($loan->getActiveToRepaid(), $this->equalTo(new Float((float)20)));
    }
}
