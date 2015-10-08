<?php

/**
 * Created by IntelliJ IDEA.
 * User: trentonmaki
 * Date: 9/12/15
 * Time: 3:09 PM
 */
class bootstrapValidateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::\BLC\validateEmail
     */
    public function testValidateEmailBasic()
    {
        $json = new stdClass();
        $json->email = new stdClass();
        $json->email->username = "1";
        $json->email->password = '2';
        $json->email->smtp = "3";
        $json->email->port = 4;
        $json->email->security = "5";
        $json->email->sender = "trentonmakiA@gmail.com";
        $json->email->to = [];
        $json->email->to[] = "trentonmakiB@gmail.com";
        $json->email->to[] = "trentonmakiC@gmail.com";

        $result = BLC\validateEmail($json);
        $this->assertThat($result[0], $this->equalTo($json->email->username));
        $this->assertThat($result[1], $this->equalTo($json->email->password));
        $this->assertThat($result[2], $this->equalTo($json->email->smtp));
        $this->assertThat($result[3], $this->equalTo($json->email->port));
        $this->assertThat($result[4], $this->equalTo($json->email->security));
        $this->assertThat($result[5][0], $this->equalTo($json->email->to[0]));
        $this->assertThat($result[5][1], $this->equalTo($json->email->to[1]));
        $this->assertThat($result[6], $this->equalTo($json->email->sender));
    }

    /**
     * @covers ::\BLC\validateEmail
     */
    public function testValidateEmailNoEmail()
    {
        $json = new stdClass();

        $result = \BLC\validateEmail($json);

        $this->assertThat($result[0], $this->equalTo('appforgeorg@gmail.com'));
        $this->assertThat($result[1], $this->equalTo('#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0'));
        $this->assertThat($result[2], $this->equalTo("smtp.gmail.com"));
        $this->assertThat($result[3], $this->equalTo(587));
        $this->assertThat($result[4], $this->equalTo("tls"));
        $this->assertThat($result[5][0], $this->equalTo("trentonmaki@gmail.com"));
        $this->assertThat($result[6], $this->equalTo('appforgeorg@gmail.com'));
    }

    /**
     * @covers ::\BLC\validateEmail
     */
    public function testValidateNoUsername()
    {
        $json = new stdClass();
        $json->email = new stdClass();
//        $json->email->username = 'appforgeorg@gmail.com';
        $json->email->password = '#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0';
        $json->email->smtp = "smtp.gmail.com";
        $json->email->port = 587;
        $json->email->security = "tls";
        $json->email->to = ["trentonmaki@gmail.com", "E76631921-WR@workroom.elance.com"];
        $json->email->sender = 'appforgeorg@gmail.com';


        $result = BLC\validateEmail($json);

        $this->assertThat($result[0], $this->equalTo('appforgeorg@gmail.com'));
        $this->assertThat($result[1], $this->equalTo('#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0'));
        $this->assertThat($result[2], $this->equalTo("smtp.gmail.com"));
        $this->assertThat($result[3], $this->equalTo(587));
        $this->assertThat($result[4], $this->equalTo("tls"));
        $this->assertThat($result[5][0], $this->equalTo("trentonmaki@gmail.com"));
        $this->assertThat($result[5][1], $this->equalTo("E76631921-WR@workroom.elance.com"));
        $this->assertThat($result[6], $this->equalTo('appforgeorg@gmail.com'));
    }

    /**
     * @covers ::\BLC\validateEmail
     */
    public function testValidateNoPassword()
    {
        $json = new stdClass();
        $json->email = new stdClass();
        $json->email->username = 'appforgeorg@gmail.com';
//        $json->email->password = '#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0';
        $json->email->smtp = "smtp.gmail.com";
        $json->email->port = 587;
        $json->email->security = "tls";
        $json->email->to = ["trentonmaki@gmail.com", "E76631921-WR@workroom.elance.com"];
        $json->email->sender = 'appforgeorg@gmail.com';


        $result = BLC\validateEmail($json);

        $this->assertThat($result[0], $this->equalTo('appforgeorg@gmail.com'));
        $this->assertThat($result[1], $this->equalTo('#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0'));
        $this->assertThat($result[2], $this->equalTo("smtp.gmail.com"));
        $this->assertThat($result[3], $this->equalTo(587));
        $this->assertThat($result[4], $this->equalTo("tls"));
        $this->assertThat($result[5][0], $this->equalTo("trentonmaki@gmail.com"));
        $this->assertThat($result[5][1], $this->equalTo("E76631921-WR@workroom.elance.com"));
        $this->assertThat($result[6], $this->equalTo('appforgeorg@gmail.com'));
    }

    /**
     * @covers ::\BLC\validateEmail
     */
    public function testValidateNoSMTP()
    {
        $json = new stdClass();
        $json->email = new stdClass();
        $json->email->username = 'appforgeorg@gmail.com';
        $json->email->password = '#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0';
//        $json->email->smtp = "smtp.gmail.com";
        $json->email->port = 587;
        $json->email->security = "tls";
        $json->email->to = ["trentonmaki@gmail.com", "E76631921-WR@workroom.elance.com"];
        $json->email->sender = 'appforgeorg@gmail.com';


        $result = BLC\validateEmail($json);

        $this->assertThat($result[0], $this->equalTo('appforgeorg@gmail.com'));
        $this->assertThat($result[1], $this->equalTo('#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0'));
        $this->assertThat($result[2], $this->equalTo("smtp.gmail.com"));
        $this->assertThat($result[3], $this->equalTo(587));
        $this->assertThat($result[4], $this->equalTo("tls"));
        $this->assertThat($result[5][0], $this->equalTo("trentonmaki@gmail.com"));
        $this->assertThat($result[5][1], $this->equalTo("E76631921-WR@workroom.elance.com"));
        $this->assertThat($result[6], $this->equalTo('appforgeorg@gmail.com'));
    }

    /**
     * @covers ::\BLC\validateEmail
     */
    public function testValidateNoPort()
    {
        $json = new stdClass();
        $json->email = new stdClass();
        $json->email->username = 'appforgeorg@gmail.com';
        $json->email->password = '#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0';
        $json->email->smtp = "smtp.gmail.com";
//        $json->email->port = 587;
        $json->email->security = "tls";
        $json->email->to = ["trentonmaki@gmail.com", "E76631921-WR@workroom.elance.com"];
        $json->email->sender = 'appforgeorg@gmail.com';


        $result = BLC\validateEmail($json);

        $this->assertThat($result[0], $this->equalTo('appforgeorg@gmail.com'));
        $this->assertThat($result[1], $this->equalTo('#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0'));
        $this->assertThat($result[2], $this->equalTo("smtp.gmail.com"));
        $this->assertThat($result[3], $this->equalTo(587));
        $this->assertThat($result[4], $this->equalTo("tls"));
        $this->assertThat($result[5][0], $this->equalTo("trentonmaki@gmail.com"));
        $this->assertThat($result[5][1], $this->equalTo("E76631921-WR@workroom.elance.com"));
        $this->assertThat($result[6], $this->equalTo('appforgeorg@gmail.com'));
    }

    /**
     * @covers ::\BLC\validateEmail
     */
    public function testValidateNoSecurity()
    {
        $json = new stdClass();
        $json->email = new stdClass();
        $json->email->username = 'appforgeorg@gmail.com';
        $json->email->password = '#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0';
        $json->email->smtp = "smtp.gmail.com";
        $json->email->port = 587;
//        $json->email->security = "tls";
        $json->email->to = ["trentonmaki@gmail.com", "E76631921-WR@workroom.elance.com"];
        $json->email->sender = 'appforgeorg@gmail.com';


        $result = BLC\validateEmail($json);

        $this->assertThat($result[0], $this->equalTo('appforgeorg@gmail.com'));
        $this->assertThat($result[1], $this->equalTo('#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0'));
        $this->assertThat($result[2], $this->equalTo("smtp.gmail.com"));
        $this->assertThat($result[3], $this->equalTo(587));
        $this->assertThat($result[4], $this->equalTo("tls"));
        $this->assertThat($result[5][0], $this->equalTo("trentonmaki@gmail.com"));
        $this->assertThat($result[5][1], $this->equalTo("E76631921-WR@workroom.elance.com"));
        $this->assertThat($result[6], $this->equalTo('appforgeorg@gmail.com'));
    }

    /**
     * @covers ::\BLC\validateEmail
     */
    public function testValidateNoTo()
    {
        $json = new stdClass();
        $json->email = new stdClass();
        $json->email->username = 'appforgeorg@gmail.com';
        $json->email->password = '#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0';
        $json->email->smtp = "smtp.gmail.com";
        $json->email->port = 587;
        $json->email->security = "tls";
//        $json->email->to = ["trentonmaki@gmail.com", "E76631921-WR@workroom.elance.com"];
        $json->email->sender = 'appforgeorg@gmail.com';


        $result = BLC\validateEmail($json);

        $this->assertThat($result[0], $this->equalTo('appforgeorg@gmail.com'));
        $this->assertThat($result[1], $this->equalTo('#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0'));
        $this->assertThat($result[2], $this->equalTo("smtp.gmail.com"));
        $this->assertThat($result[3], $this->equalTo(587));
        $this->assertThat($result[4], $this->equalTo("tls"));
        $this->assertThat($result[5][0], $this->equalTo("trentonmaki@gmail.com"));
        $this->assertThat($result[6], $this->equalTo('appforgeorg@gmail.com'));
    }

    /**
     * @covers ::\BLC\validateEmail
     */
    public function testValidateNoToContents()
    {
        $json = new stdClass();
        $json->email = new stdClass();
        $json->email->username = 'appforgeorg@gmail.com';
        $json->email->password = '#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0';
        $json->email->smtp = "smtp.gmail.com";
        $json->email->port = 587;
        $json->email->security = "tls";
        $json->email->to = [];
        $json->email->sender = 'appforgeorg@gmail.com';


        $result = BLC\validateEmail($json);

        $this->assertThat($result[0], $this->equalTo('appforgeorg@gmail.com'));
        $this->assertThat($result[1], $this->equalTo('#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0'));
        $this->assertThat($result[2], $this->equalTo("smtp.gmail.com"));
        $this->assertThat($result[3], $this->equalTo(587));
        $this->assertThat($result[4], $this->equalTo("tls"));
        $this->assertThat($result[5][0], $this->equalTo("trentonmaki@gmail.com"));
        $this->assertThat($result[5][1], $this->equalTo("E76631921-WR@workroom.elance.com"));
        $this->assertThat($result[6], $this->equalTo('appforgeorg@gmail.com'));
    }

    /**
     * @covers ::\BLC\validateEmail
     */
    public function testBadUsername()
    {
        $json = new stdClass();
        $json->email = new stdClass();
        $json->email->username = [];
        $json->email->password = '#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0';
        $json->email->smtp = "smtp.gmail.com";
        $json->email->port = 587;
        $json->email->security = "tls";
        $json->email->to = ["trentonmaki@gmail.com", "E76631921-WR@workroom.elance.com"];
        $json->email->sender = 'appforgeorg@gmail.com';


        $result = BLC\validateEmail($json);

        $this->assertThat($result[0], $this->equalTo('appforgeorg@gmail.com'));
        $this->assertThat($result[1], $this->equalTo('#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0'));
        $this->assertThat($result[2], $this->equalTo("smtp.gmail.com"));
        $this->assertThat($result[3], $this->equalTo(587));
        $this->assertThat($result[4], $this->equalTo("tls"));
        $this->assertThat($result[5][0], $this->equalTo("trentonmaki@gmail.com"));
        $this->assertThat($result[5][1], $this->equalTo("E76631921-WR@workroom.elance.com"));
        $this->assertThat($result[6], $this->equalTo('appforgeorg@gmail.com'));
    }

    /**
     * @covers ::\BLC\validateEmail
     */
    public function testBadPassword()
    {
        $json = new stdClass();
        $json->email = new stdClass();
        $json->email->username = 'appforgeorg@gmail.com';
        $json->email->password = [];
        $json->email->smtp = "smtp.gmail.com";
        $json->email->port = 587;
        $json->email->security = "tls";
        $json->email->to = ["trentonmaki@gmail.com", "E76631921-WR@workroom.elance.com"];
        $json->email->sender = 'appforgeorg@gmail.com';


        $result = BLC\validateEmail($json);

        $this->assertThat($result[0], $this->equalTo('appforgeorg@gmail.com'));
        $this->assertThat($result[1], $this->equalTo('#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0'));
        $this->assertThat($result[2], $this->equalTo("smtp.gmail.com"));
        $this->assertThat($result[3], $this->equalTo(587));
        $this->assertThat($result[4], $this->equalTo("tls"));
        $this->assertThat($result[5][0], $this->equalTo("trentonmaki@gmail.com"));
        $this->assertThat($result[5][1], $this->equalTo("E76631921-WR@workroom.elance.com"));
        $this->assertThat($result[6], $this->equalTo('appforgeorg@gmail.com'));
    }

    /**
     * @covers ::\BLC\validateEmail
     */
    public function testBadSMTP()
    {
        $json = new stdClass();
        $json->email = new stdClass();
        $json->email->username = 'appforgeorg@gmail.com';
        $json->email->password = '#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0';
        $json->email->smtp = [];
        $json->email->port = 587;
        $json->email->security = "tls";
        $json->email->to = ["trentonmaki@gmail.com", "E76631921-WR@workroom.elance.com"];
        $json->email->sender = 'appforgeorg@gmail.com';


        $result = BLC\validateEmail($json);

        $this->assertThat($result[0], $this->equalTo('appforgeorg@gmail.com'));
        $this->assertThat($result[1], $this->equalTo('#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0'));
        $this->assertThat($result[2], $this->equalTo("smtp.gmail.com"));
        $this->assertThat($result[3], $this->equalTo(587));
        $this->assertThat($result[4], $this->equalTo("tls"));
        $this->assertThat($result[5][0], $this->equalTo("trentonmaki@gmail.com"));
        $this->assertThat($result[5][1], $this->equalTo("E76631921-WR@workroom.elance.com"));
        $this->assertThat($result[6], $this->equalTo('appforgeorg@gmail.com'));
    }

    /**
     * @covers ::\BLC\validateEmail
     */
    public function testBadPort()
    {
        $json = new stdClass();
        $json->email = new stdClass();
        $json->email->username = 'appforgeorg@gmail.com';
        $json->email->password = '#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0';
        $json->email->smtp = "smtp.gmail.com";
        $json->email->port = [];
        $json->email->security = "tls";
        $json->email->to = ["trentonmaki@gmail.com", "E76631921-WR@workroom.elance.com"];
        $json->email->sender = 'appforgeorg@gmail.com';


        $result = BLC\validateEmail($json);

        $this->assertThat($result[0], $this->equalTo('appforgeorg@gmail.com'));
        $this->assertThat($result[1], $this->equalTo('#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0'));
        $this->assertThat($result[2], $this->equalTo("smtp.gmail.com"));
        $this->assertThat($result[3], $this->equalTo(587));
        $this->assertThat($result[4], $this->equalTo("tls"));
        $this->assertThat($result[5][0], $this->equalTo("trentonmaki@gmail.com"));
        $this->assertThat($result[5][1], $this->equalTo("E76631921-WR@workroom.elance.com"));
        $this->assertThat($result[6], $this->equalTo('appforgeorg@gmail.com'));
    }

    /**
     * @covers ::\BLC\validateEmail
     */
    public function testBadSecurity()
    {
        $json = new stdClass();
        $json->email = new stdClass();
        $json->email->username = 'appforgeorg@gmail.com';
        $json->email->password = '#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0';
        $json->email->smtp = "smtp.gmail.com";
        $json->email->port = 587;
        $json->email->security = [];
        $json->email->to = ["trentonmaki@gmail.com", "E76631921-WR@workroom.elance.com"];
        $json->email->sender = 'appforgeorg@gmail.com';


        $result = BLC\validateEmail($json);

        $this->assertThat($result[0], $this->equalTo('appforgeorg@gmail.com'));
        $this->assertThat($result[1], $this->equalTo('#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0'));
        $this->assertThat($result[2], $this->equalTo("smtp.gmail.com"));
        $this->assertThat($result[3], $this->equalTo(587));
        $this->assertThat($result[4], $this->equalTo("tls"));
        $this->assertThat($result[5][0], $this->equalTo("trentonmaki@gmail.com"));
        $this->assertThat($result[5][1], $this->equalTo("E76631921-WR@workroom.elance.com"));
        $this->assertThat($result[6], $this->equalTo('appforgeorg@gmail.com'));
    }

    /**
     * @covers ::\BLC\validateEmail
     */
    public function testBadSender()
    {
        $json = new stdClass();
        $json->email = new stdClass();
        $json->email->username = 'appforgeorg@gmail.com';
        $json->email->password = '#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0';
        $json->email->smtp = "smtp.gmail.com";
        $json->email->port = 587;
        $json->email->security = "tls";
        $json->email->to = ["trentonmaki@gmail.com", "E76631921-WR@workroom.elance.com"];
        $json->email->sender = [];


        $result = BLC\validateEmail($json);

        $this->assertThat($result[0], $this->equalTo('appforgeorg@gmail.com'));
        $this->assertThat($result[1], $this->equalTo('#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0'));
        $this->assertThat($result[2], $this->equalTo("smtp.gmail.com"));
        $this->assertThat($result[3], $this->equalTo(587));
        $this->assertThat($result[4], $this->equalTo("tls"));
        $this->assertThat($result[5][0], $this->equalTo("trentonmaki@gmail.com"));
        $this->assertThat($result[5][1], $this->equalTo("E76631921-WR@workroom.elance.com"));
        $this->assertThat($result[6], $this->equalTo('appforgeorg@gmail.com'));
    }

    /**
     * @covers ::\BLC\validateEmail
     */
    public function testBadTo()
    {
        $json = new stdClass();
        $json->email = new stdClass();
        $json->email->username = 'appforgeorg@gmail.com';
        $json->email->password = '#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0';
        $json->email->smtp = "smtp.gmail.com";
        $json->email->port = 587;
        $json->email->security = "tls";
        $json->email->to = new stdClass();
        $json->email->sender = 'appforgeorg@gmail.com';


        $result = BLC\validateEmail($json);

        $this->assertThat($result[0], $this->equalTo('appforgeorg@gmail.com'));
        $this->assertThat($result[1], $this->equalTo('#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0'));
        $this->assertThat($result[2], $this->equalTo("smtp.gmail.com"));
        $this->assertThat($result[3], $this->equalTo(587));
        $this->assertThat($result[4], $this->equalTo("tls"));
        $this->assertThat($result[5][0], $this->equalTo("trentonmaki@gmail.com"));
        $this->assertThat($result[6], $this->equalTo('appforgeorg@gmail.com'));
    }

    /**
     * @covers ::\BLC\validateEmail
     */
    public function testBadContents()
    {
        $json = new stdClass();
        $json->email = new stdClass();
        $json->email->username = 'appforgeorg@gmail.com';
        $json->email->password = '#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0';
        $json->email->smtp = "smtp.gmail.com";
        $json->email->port = 587;
        $json->email->security = "tls";
        $json->email->to = ["a", "trentonmaki@gmail.com", "b"];
        $json->email->sender = 'appforgeorg@gmail.com';


        $result = BLC\validateEmail($json);

        $this->assertThat($result[0], $this->equalTo('appforgeorg@gmail.com'));
        $this->assertThat($result[1], $this->equalTo('#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0'));
        $this->assertThat($result[2], $this->equalTo("smtp.gmail.com"));
        $this->assertThat($result[3], $this->equalTo(587));
        $this->assertThat($result[4], $this->equalTo("tls"));
        $this->assertThat($result[5][0], $this->equalTo("trentonmaki@gmail.com"));
        $this->assertThat(count($result[0]), $this->equalTo(1));
        $this->assertThat($result[6], $this->equalTo('appforgeorg@gmail.com'));
    }

    /**
     * @covers ::\BLC\validateEmail
     */
    public function testBadKeys()
    {
        $json = new stdClass();
        $json->email = new stdClass();
        $json->email->username = 'appforgeorg@gmail.com';
        $json->email->password = '#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0';
        $json->email->smtp = "smtp.gmail.com";
        $json->email->port = 587;
        $json->email->security = "tls";
        $json->email->to = ["a" => "trentonmaki@gmail.com", "b" => "E76631921-WR@workroom.elance.com", "trentonmaki+g@google.com"];
        $json->email->sender = 'appforgeorg@gmail.com';


        $result = BLC\validateEmail($json);

        $this->assertThat($result[0], $this->equalTo('appforgeorg@gmail.com'));
        $this->assertThat($result[1], $this->equalTo('#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0'));
        $this->assertThat($result[2], $this->equalTo("smtp.gmail.com"));
        $this->assertThat($result[3], $this->equalTo(587));
        $this->assertThat($result[4], $this->equalTo("tls"));
        $this->assertThat($result[5][0], $this->equalTo("trentonmaki+g@google.com"));
        $this->assertThat(count($result[0]), $this->equalTo(1));
        $this->assertThat($result[6], $this->equalTo('appforgeorg@gmail.com'));
    }

    /**
     * @covers ::\BLC\validateEmail
     */
    public function testBadContentNotString()
    {
        $json = new stdClass();
        $json->email = new stdClass();
        $json->email->username = 'appforgeorg@gmail.com';
        $json->email->password = '#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0';
        $json->email->smtp = "smtp.gmail.com";
        $json->email->port = 587;
        $json->email->security = "tls";
        $json->email->to = [[], new stdClass(), "trentonmaki+g@google.com", 5];
        $json->email->sender = 'appforgeorg@gmail.com';


        $result = BLC\validateEmail($json);

        $this->assertThat($result[0], $this->equalTo('appforgeorg@gmail.com'));
        $this->assertThat($result[1], $this->equalTo('#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0'));
        $this->assertThat($result[2], $this->equalTo("smtp.gmail.com"));
        $this->assertThat($result[3], $this->equalTo(587));
        $this->assertThat($result[4], $this->equalTo("tls"));
        $this->assertThat($result[5][0], $this->equalTo("trentonmaki+g@google.com"));
        $this->assertThat(count($result[0]), $this->equalTo(1));
        $this->assertThat($result[6], $this->equalTo('appforgeorg@gmail.com'));
    }

    /**
     * @covers ::\BLC\validateEmail
     */
    public function testBadContentNotEmail()
    {
        $json = new stdClass();
        $json->email = new stdClass();
        $json->email->username = 'appforgeorg@gmail.com';
        $json->email->password = '#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0';
        $json->email->smtp = "smtp.gmail.com";
        $json->email->port = 587;
        $json->email->security = "tls";
        $json->email->to = ["b", "a", "trentonmaki+g@google.com", "c"];
        $json->email->sender = 'appforgeorg@gmail.com';


        $result = BLC\validateEmail($json);

        $this->assertThat($result[0], $this->equalTo('appforgeorg@gmail.com'));
        $this->assertThat($result[1], $this->equalTo('#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0'));
        $this->assertThat($result[2], $this->equalTo("smtp.gmail.com"));
        $this->assertThat($result[3], $this->equalTo(587));
        $this->assertThat($result[4], $this->equalTo("tls"));
        $this->assertThat($result[5][0], $this->equalTo("trentonmaki+g@google.com"));
        $this->assertThat(count($result[0]), $this->equalTo(1));
        $this->assertThat($result[6], $this->equalTo('appforgeorg@gmail.com'));
    }

    /**
     * @covers ::\BLC\validateEmail
     */
    public function testEmailBad() {

        $json = new stdClass();
        $json->email = [];

        $result = BLC\validateEmail($json);

        $this->assertThat($result[0], $this->equalTo('appforgeorg@gmail.com'));
        $this->assertThat($result[1], $this->equalTo('#6Mk1pXBxSymt%C5$LtvBNbUU*c4m^eJGIHqPvGAFljf%SuO$1&e$IOIaLdi0fZ0'));
        $this->assertThat($result[2], $this->equalTo("smtp.gmail.com"));
        $this->assertThat($result[3], $this->equalTo(587));
        $this->assertThat($result[4], $this->equalTo("tls"));
        $this->assertThat($result[5][0], $this->equalTo("trentonmaki@gmail.com"));
        $this->assertThat(count($result[0]), $this->equalTo(1));
        $this->assertThat($result[6], $this->equalTo('appforgeorg@gmail.com'));
    }
}