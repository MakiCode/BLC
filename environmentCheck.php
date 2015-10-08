<?php

if(PHP_SAPI != "cli") {
    die();
}

require(__DIR__ . "/vendor/autoload.php");

if (!function_exists("errorHandler")) {
    function errorHandler($severity, $errstr, $errfile, $errline)
    {
        if (error_reporting() == 0) {
            return;
        }
        if (error_reporting() & $severity) {
            throw new ErrorException($errstr, 0, $severity, $errfile, $errline);
        }
    }

    set_error_handler('errorHandler');
}

if (!function_exists("redCLI")) {
    function redCLI($string)
    {
        return "\033[0;31m" . $string . "\033[0m";
    }
}

date_default_timezone_set("America/Mexico_City");
$shouldReturn = true;
$permissionsValid = require(__DIR__ . "/checkFilePermissions.php");
if (is_string($permissionsValid)) {
    echo redCLI("Permissions invalid! Output: ") . PHP_EOL;
    echo redCLI($permissionsValid);
} else {
    echo "Permissions on all files is ok!" . PHP_EOL;
}


require("checkConfig.php");

try {
    $configJSON = file_get_contents(__DIR__ . "/src/data/ruleConfig.json");
//    echo "Successfully loaded ruleConfig.json file!" . PHP_EOL;

} catch (Exception $e) {
}

if (!isset($configJSON) || $configJSON === false) {
    echo redCLI("Failed to open ./src/data/ruleConfig.json, check that it exists and that the permissions are correct!" . PHP_EOL .
        "Skipping SMTP check") . PHP_EOL;
} else {
    try {
        $encodedConfig = json_decode($configJSON);
        //Already taken care of in configCheck.php
        //echo "ruleConfig.json is properly formatted JSON" . PHP_EOL;
    } catch (Exception $e) {
//        echo "Skipping SMTP check...";
        //Already taken care of in configCheck.php
//        echo "ruleConfig.json is poorly formed! Use a JSON format checker (like http://www.jsoneditoronline.org) to fix this. Skipping SMTP check..." . PHP_EOL;s
    }

    if (isset($encodedConfig)) {
        try {
            $smtp = $encodedConfig->email->smtp;
            $port = $encodedConfig->email->port;
            $security = $encodedConfig->email->security;
            $username = $encodedConfig->email->username;
            $password = $encodedConfig->email->password;
            $sender = $encodedConfig->email->sender;
            $to = $encodedConfig->email->to;

            echo "Email keys in jsonConfig exist!" . PHP_EOL;
        } catch (ErrorException $e) {
            echo redClI("Some of the email keys in jsonConfig don't exist! Skipping SMTP...") . PHP_EOL;
        }
        if (isset($smtp) &&
            isset($port) &&
            isset($security) &&
            isset($username) &&
            isset($password) &&
            isset($sender) &&
            isset($to)
        ) {
            try {
                $transporter = Swift_SmtpTransport::newInstance($smtp, (int)$port, $security)
                    ->setUsername($username)
                    ->setPassword($password);
                try {
                    $transporter->start();
                    echo "Created email transporter sucessfully" . PHP_EOL;
                } catch (Swift_TransportException $e) {
                    echo redClI("Problems with creating emails! You probably didn't set the username, password, port," . PHP_EOL .
                        "security, or SMTP properly, or the network is down") . PHP_EOL;
                    $failed = true;
                }
                if (!isset($failed)) {
                    $swiftMailer = Swift_Mailer::newInstance($transporter);
                    $message = Swift_Message::newInstance("Sending test email", "If you're seeing this, emails are working properly!", "text/html", "utf-8");
                    $message->setTo($to);
                    $message->setSender($sender);

                    $swiftMailer->send($message);
                    echo "Sent an email sucessfully! (as far as I can tell)" . PHP_EOL;
                }
            } catch (Exception $e) {
                echo redClI("Poorly formed data for emails, did you mix up arrays and objects or strings and numbers?") . PHP_EOL;
            }
        }
    }
}

try {
    $client = new \GuzzleHttp\Client([
        'base_uri' => 'https://api.bitlendingclub.com',
        'timeout' => 5.0,
        'headers' => [
            "Accept" => "application/vnd.blc.v1+json"
        ]
    ]);

    try {
        $response = $client->get("/api/loans?limit=0");
    } catch (\GuzzleHttp\Exception\ClientException $e) {
        echo redClI("Client Exception! The connection to bit lending club returned a " . $e->getResponse()->getStatusCode() .
            "status! This shouldn't happen." . PHP_EOL .
            " Try again, and if it still fails call your developer. Have them look at lines 102-111 to start") . PHP_EOL;
    }
    if (isset($response)) {
        if ($response->getStatusCode() != 200) {
            echo redClI("Connection to bit lending club isn't working! " . $response->getStatusCode() . ": " . $response->getReasonPhrase()) . PHP_EOL;
        } else {
            echo "Connection to bit lending club succeeded!" . PHP_EOL;
        }
    }
} catch (\GuzzleHttp\Exception\ConnectException $e) {
    echo redClI("Failed to connect to bitlendingclub.com! Is the network on?") . PHP_EOL;
}