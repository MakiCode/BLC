<?php

use BLC\Config\Config;
use BLC\Config\Data;
use BLC\Model\WorkQueue;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

date_default_timezone_set("America/Mexico_City");

try {
    $configJSON = new \BLC\JSON(file_get_contents(__DIR__ . "/data/ruleConfig.json"));
    $JSON = $configJSON->getJSON();
} catch(Exception $e) {
    syslog(LOG_EMERG, "Config file not valid! Can't do anything!");
    echo "Config file not valid! Can't do anything!";
    throw $e;
}

if(isset($configJSON) && isset($JSON)) {
$logFile = __DIR__ . "/data/data.log";
if (isset($JSON->logFile)) {
    $logFile = __DIR__ . "/" . $JSON->logFile;
}

list($username, $password, $smtp, $port, $security, $to, $sender) = BLC\validateEmail($JSON);
$transporter = Swift_SmtpTransport::newInstance($smtp, (int)$port, $security)
    ->setUsername($username)
    ->setPassword($password);
try {
    $transporter->start();
} catch (Swift_TransportException $e) {
    $SMTPError = $e;
    $transporter = Swift_MailTransport::newInstance();
}
$swiftMailer = Swift_Mailer::newInstance($transporter);
$swiftLogger = new Swift_Plugins_Loggers_ArrayLogger(100);
$swiftMailer->registerPlugin(new Swift_Plugins_LoggerPlugin($swiftLogger));
$message = Swift_Message::newInstance("Critical error in app!", null, "text/html", "utf-8");
$message->setTo($to);
$message->setSender($sender);


$logger = new \Monolog\Logger("BLC");
$fileHandler = new \Monolog\Handler\RotatingFileHandler($logFile, 1);
$fileHandler->setFormatter(new \Monolog\Formatter\LineFormatter());
$logger->pushHandler($fileHandler);

$mailHandler = new \Monolog\Handler\SwiftMailerHandler($swiftMailer, $message, \Monolog\Logger::CRITICAL);
$mailHandler->setFormatter(new \Monolog\Formatter\HtmlFormatter("d/m/Y"));

$buffer = new \Monolog\Handler\BufferHandler($mailHandler, 100, \Monolog\Logger::CRITICAL);
$logger->pushHandler($buffer);


$logger->pushProcessor(new \Monolog\Processor\IntrospectionProcessor());
$logger->pushProcessor(new \Monolog\Processor\MemoryUsageProcessor());

if (isset($SMTPError)) {
    $logger->addCritical("Failed to create SMTP transporter, switching to native mail()", ["exception" => $SMTPError]);
}

$config = new Config($configJSON, $logger);
try {
    $dataContents = file_get_contents(__DIR__ . "/data/data.json");
    $dataJSON = new \BLC\JSON($dataContents);
} catch(Exception $e) {
    $logger->addNotice("Data file was bad, switching to empty", ["exception" => $e]);
    $dataJSON = new \BLC\JSON("{}");
}

$data = new Data($dataJSON, $logger);

bcscale($config->getScale());

$handler = GuzzleHttp\HandlerStack::create();
$handler->push(Middleware::mapRequest(function (RequestInterface $request) use ($logger) {
    $logger->addInfo("Making request.", ["URI" => (string)$request->getUri(), "method" => $request->getMethod(), "body" => $request->getBody()->getContents(), "request" => $request]);
    return $request;
}));
try {
    $client = new \GuzzleHttp\Client([
        'base_uri' => 'https://api.bitlendingclub.com',
        'timeout' => 5.0,
        'handler' => $handler,
        'headers' => [
            'Authorization' => 'Bearer ' . $config->getAPIKey(),
            "Accept" => "application/vnd.blc.v1+json"
        ]

    ]);
} catch(ConnectException $e) {
    $logger->addEmergency("Connection Error couldn't connect to BitLendingClub!", ["exception" => $e, "trace" => $e->getTraceAsString()]);
    exit(1);
}


$queue = new WorkQueue(new \BLC\Model\ExclusiveLock("BLC", $logger));
}

return ['config' => $config, "guzzle" => $client, "data" => $data, 'queue' => $queue, 'swift' => $swiftMailer, 'logger' => $logger, 'swiftLogger' => $swiftLogger];