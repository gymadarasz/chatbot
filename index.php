<?php declare(strict_types = 1);

use GyMadarasz\WebApp\Router;
use GyMadarasz\WebApp\Service\Invoker;
use GyMadarasz\WebApp\Service\RouteSet;
use GyMadarasz\WebApp\Service\Config;
use GyMadarasz\ChatBot\Controller\LoginPagePost;
use GyMadarasz\ChatBot\Controller\MyChatsPage;
use GyMadarasz\ChatBot\Controller\EditChatPage;
use GyMadarasz\ChatBot\Controller\CreateChatPage;
use GyMadarasz\ChatBot\Controller\ConversationPage;

include __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL | E_STRICT);
set_error_handler(
    static function(int $errno, string $errstr, string $errfile = null, int $errline = null, array $errcontext = null) : bool
    {
        throw new RuntimeException("An error occured: (code: $errno): $errstr\nIn file $errfile:$errline\n");
    }
);

(new Config())->setExtPath(__DIR__ . '/src/config');

new Router(
    (new RouteSet())
        ->apply(RouteSet::APP_ROUTES)
        ->apply([
            'public' => [
                'POST' => [
                    '' => [LoginPagePost::class, 'login'],
                    'login' => [LoginPagePost::class, 'login'],
                ],
            ],
            'protected' => [
                'GET' => [
                    '' => [MyChatsPage::class, 'view'],
                    'mychats' => [MyChatsPage::class, 'view'],
                    'editchat' => [EditChatPage::class, 'view'],
                    'createchat' => [CreateChatPage::class, 'view'],
                    'deletechat' => [MyChatsPage::class, 'delete'],
                    'delmsg' => [EditChatPage::class, 'deleteMessage'],
                ],
                'POST' => [
                    'editchat' => [EditChatPage::class, 'edit'],
                    'createchat' => [CreateChatPage::class, 'save'],
                    'createmsg' => [EditChatPage::class, 'createMessage'],
                    'modifymsg2msg' => [EditChatPage::class, 'modifyMessageToMessage'],
                    'start-conversation' => [ConversationPage::class, 'startConversation'],
                    'conversation-response' => [ConversationPage::class, 'humanRespond'],
                ],
            ],
        ])
        ->getRoutes(),
        new Invoker()
);
