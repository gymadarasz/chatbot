<?php declare(strict_types = 1);

use GyMadarasz\WebApp\Service\Invoker;
use GyMadarasz\WebApp\Service\Logger;
use GyMadarasz\WebApp\Service\Config;
use GyMadarasz\WebApp\Service\Mysql;
use GyMadarasz\Test\Test;
use GyMadarasz\Test\Tester;
use GyMadarasz\Test\AppTest;
use GuzzleHttp\Client;
use GyMadarasz\ChatBot\Test\ChatBotTest;
use GyMadarasz\ChatBot\Test\ConversationCrudTest;
use GyMadarasz\ChatBot\Test\ConversationTest;

include __DIR__ . '/vendor/autoload.php';

// TODO: needs coverage info and more tests (cURL requests merged coverage!!)

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL | E_STRICT);
// set_error_handler(
//     static function(int $errno, string $errstr, string $errfile = null, int $errline = null, array $errcontext = null) : bool
//     {
//         throw new RuntimeException("An error occured: (code: $errno): $errstr\nIn file $errfile:$errline\n");
//     }
// );


($config = new Config())->setExtPath(__DIR__ . '/src/config');

// $mysql = new Mysql($config);
return (new Tester())->test(
    new Invoker(),
    $config,
    $logger = new Logger($config),
    new Client([
        'base_uri' => $config->get('baseUrl'), 
        'cookies' => true,
    ]), [
        // new AppTest($config, $logger, $mysql),
        ChatBotTest::class, //new ChatBotTest($config, $logger, $mysql),
        // ConversationCrudTest::class, //new ConversationCrudTest($config, $logger, $mysql),
        ConversationTest::class, //new ConversationTest($config, $logger, $mysql),
    ]
)->stat();