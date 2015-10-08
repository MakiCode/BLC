<?php
use BLC\Model\OptionalDate;
use Types\Boolean;
use Types\Float;
use Types\Integer;
use Types\Numeric;
use Types\String;

/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/4/15
 * Time: 4:39 PM
 *
 * @uses BLC\Model\OptionalDate
 * @uses Types\Boolean
 * @uses Types\Integer
 * @uses Types\String
 * @uses BLC\Model\OptionalBoolean
 * @uses BLC\Model\Loan
 * @uses BLC\Model\Optional
 * @uses BLC\Model\OptionalString
 * @uses BLC\Model\OptionalInteger
 */
class LoanTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers BLC\Model\Loan::getId
     * @covers BLC\Model\Loan::getType
     * @covers BLC\Model\Loan::getTitle
     * @covers BLC\Model\Loan::getDescription
     * @covers BLC\Model\Loan::getAmount
     * @covers BLC\Model\Loan::getTerm
     * @covers BLC\Model\Loan::getFrequency
     * @covers BLC\Model\Loan::getStatus
     * @covers BLC\Model\Loan::getPaymentStatus
     * @covers BLC\Model\Loan::getCreatedAt
     * @covers BLC\Model\Loan::getExpirationDate
     * @covers BLC\Model\Loan::getPaymentDueDate
     * @covers BLC\Model\Loan::getDateRepaid
     * @covers BLC\Model\Loan::getDenomination
     * @covers BLC\Model\Loan::getPercentFunded
     * @covers BLC\Model\Loan::getVotes
     * @covers BLC\Model\Loan::getBorrower
     * @covers BLC\Model\Loan::getCountryID
     * @covers BLC\Model\Loan::getSalary
     * @covers BLC\Model\Loan::getRating
     * @covers BLC\Model\Loan::isFacebook
     * @covers BLC\Model\Loan::isLinkedin
     * @covers BLC\Model\Loan::isGoogle
     * @covers BLC\Model\Loan::isTwitter
     * @covers BLC\Model\Loan::isPaypal
     * @covers BLC\Model\Loan::isAmazon
     * @covers BLC\Model\Loan::isLocalbitcoins
     * @covers BLC\Model\Loan::isEbay
     * @covers BLC\Model\Loan::isCoinbase
     * @covers BLC\Model\Loan::getCreditScore
     * @covers BLC\Model\Loan::getActiveToRepaid
     * @covers BLC\Model\Loan::__construct
     */
    public function testGetters()
    {
        date_default_timezone_set("America/Mexico_City");
        $baseDate = new DateTime("now");
        $date1 = $baseDate->add(new DateInterval("P1D"));
        $date2 = $baseDate->add(new DateInterval("P2D"));
        $date3 = $baseDate->add(new DateInterval("P3D"));
        $date4 = $baseDate->add(new DateInterval("P4D"));

        $loan = new \BLC\Model\Loan(
            new Numeric(1),
            new String("A"),
            new String("B"),
            new String("C"),
            new String("D"),
            new Integer(2),
            new Integer(3),
            new String("E"),
            new String("F"),
            $date1,
            $date2,
            $date3,
            $date4,
            new String("G"),
            new Numeric(4),
            new Integer(20),
            new Integer(6),
            new String("H"),
            new String("I"),
            new Integer(7),
            new Boolean(true),
            new Boolean(false),
            new Boolean(true),
            new Boolean(false),
            new Boolean(true),
            new Boolean(false),
            new Boolean(true),
            new Boolean(false),
            new Boolean(true),
            new String("J"),
            new Float((float)8)
        );


        $this->assertThat($loan->getId(), $this->equalTo(new Numeric(1)));
        $this->assertThat($loan->getType(), $this->equalTo(new String("A")));
        $this->assertThat($loan->getTitle(), $this->equalTo(new String("B")));
        $this->assertThat($loan->getDescription(), $this->equalTo(new String("C")));
        $this->assertThat($loan->getAmount(), $this->equalTo(new String("D")));
        $this->assertThat($loan->getTerm(), $this->equalTo(new Integer(2)));
        $this->assertThat($loan->getFrequency(), $this->equalTo(new Integer(3)));
        $this->assertThat($loan->getStatus(), $this->equalTo(new String("E")));
        $this->assertThat($loan->getPaymentStatus(), $this->equalTo(new String("F")));
        $this->assertThat($loan->getCreatedAt(), $this->equalTo(new OptionalDate($date1)));
        $this->assertThat($loan->getExpirationDate(), $this->equalTo(new OptionalDate($date2)));
        $this->assertThat($loan->getPaymentDueDate(), $this->equalTo(new OptionalDate($date3)));
        $this->assertThat($loan->getDateRepaid(), $this->equalTo(new OptionalDate($date4)));
        $this->assertThat($loan->getDenomination(), $this->equalTo(new String("G")));
        $this->assertThat($loan->getPercentFunded(), $this->equalTo(new Numeric(4)));
        $this->assertThat($loan->getVotes()->get(), $this->equalTo(new Integer(20)));
        $this->assertThat($loan->getBorrower(), $this->equalTo(new Integer(6)));
        $this->assertThat($loan->getCountryID(), $this->equalTo(new String("H")));
        $this->assertThat($loan->getSalary(), $this->equalTo(new String("I")));
        $this->assertThat($loan->getRating(), $this->equalTo(new Integer(7)));
        $this->assertThat($loan->isFacebook()->get(), $this->equalTo(new Boolean(true)));
        $this->assertThat($loan->isLinkedin()->get(), $this->equalTo(new Boolean(false)));
        $this->assertThat($loan->isGoogle()->get(), $this->equalTo(new Boolean(true)));
        $this->assertThat($loan->isTwitter()->get(), $this->equalTo(new Boolean(false)));
        $this->assertThat($loan->isPaypal()->get(), $this->equalTo(new Boolean(true)));
        $this->assertThat($loan->isAmazon()->get(), $this->equalTo(new Boolean(false)));
        $this->assertThat($loan->isLocalbitcoins()->get(), $this->equalTo(new Boolean(true)));
        $this->assertThat($loan->isEbay()->get(), $this->equalTo(new Boolean(false)));
        $this->assertThat($loan->isCoinbase()->get(), $this->equalTo(new Boolean(true)));
        $this->assertThat($loan->getCreditScore(), $this->equalTo(new String("J")));
        $this->assertThat($loan->getActiveToRepaid(), $this->equalTo(new Float((float)8)));
    }
}
