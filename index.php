<?php declare(strict_types = 1);

use GyMadarasz\WebApp\Router;
use GyMadarasz\WebApp\Service\Config;
use GyMadarasz\WebApp\Controller\LoginPage;
use GyMadarasz\WebApp\Controller\RegistryPage;
use GyMadarasz\WebApp\Controller\ActivatePage;
use GyMadarasz\WebApp\Controller\PasswordResetPage;
use GyMadarasz\WebApp\Controller\NewPasswordPage;
use GyMadarasz\WebApp\Controller\ResendPage;
use GyMadarasz\WebApp\Controller\ErrorPage;
use GyMadarasz\ChatBot\Controller\LoginPagePost;
use GyMadarasz\WebApp\Controller\RegistryPagePost;
use GyMadarasz\WebApp\Controller\PasswordResetPagePost;
use GyMadarasz\WebApp\Controller\NewPasswordPagePost;
use GyMadarasz\WebApp\Controller\LogoutPage;
use GyMadarasz\ChatBot\Controller\MyChatsPage;
use GyMadarasz\ChatBot\Controller\CreateChatPage;
use GyMadarasz\ChatBot\Controller\EditChatPage;

include __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
set_error_handler(
    static function(int $errno, string $errstr, string $errfile = null, int $errline = null, array $errcontext = null) : bool
    {
        throw new RuntimeException("An error occured: (code: $errno): $errstr\nIn file $errfile:$errline\n");
    }
);

(new Config())->setExtPath(__DIR__ . '/src/config');

new Router([
    'public' => [
        'GET' => [
            '' => [LoginPage::class, 'run'],
            'login' => [LoginPage::class, 'run'],
            'registry' => [RegistryPage::class, 'run'],
            'activate' => [ActivatePage::class, 'run'],
            'pwdreset' => [PasswordResetPage::class, 'run'],
            'newpassword' => [NewPasswordPage::class, 'run'],
            'resend' => [ResendPage::class, 'run'],
            '*' => [ErrorPage::class, 'run'],
        ],
        'POST' => [
            '' => [LoginPagePost::class, 'run'],
            'login' => [LoginPagePost::class, 'run'],
            'registry' => [RegistryPagePost::class, 'run'],
            'pwdreset' => [PasswordResetPagePost::class, 'run'],
            'newpassword' => [NewPasswordPagePost::class, 'run'],
            '*' => [ErrorPage::class, 'run'],
        ],
        '*' => [
            '*' => [ErrorPage::class, 'run'],
        ]
    ],
    'protected' => [
        'GET' => [
            '' => [MyChatsPage::class, 'view'],
            'mychats' => [MyChatsPage::class, 'view'],
            'logout' => [LogoutPage::class, 'run'],
            'editchat' => [EditChatPage::class, 'view'],
            'createchat' => [CreateChatPage::class, 'view'],
            'deletechat' => [MyChatsPage::class, 'delete'],
            '*' => [ErrorPage::class, 'run'],
        ],
        'POST' => [
            'editchat' => [EditChatPage::class, 'edit'],
            'createchat' => [CreateChatPage::class, 'save'],
            '*' => [ErrorPage::class, 'run'],
        ],
        '*' => [
            '*' => [ErrorPage::class, 'run'],
        ]
    ],
    '*' => [
        '*' => [
            '*' => [ErrorPage::class, 'run'],
        ]
    ]
]);

