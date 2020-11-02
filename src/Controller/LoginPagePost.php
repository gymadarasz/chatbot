<?php declare(strict_types = 1);

namespace GyMadarasz\ChatBot\Controller;

use GyMadarasz\WebApp\Service\Template;
use GyMadarasz\WebApp\Service\User;
use GyMadarasz\WebApp\Service\Globals;
use GyMadarasz\ChatBot\Service\Chats;

class LoginPagePost
{
    public function login(Template $template, User $user, Globals $globals, Chats $chats): Template
    {
        if ($user->doAuth(
            $globals->getPost('email', ''),
            $globals->getPost('password', '')
        )) {
            $output = $template->create('my-chats.html.php', [
                'chats' => $chats->getList(),
            ]);
            $output->set('message', 'Login success');
        } else {
            $output = $template->create('login.html.php');
            $output->set('error', 'Login failed');
        }

        return $output;
    }
}
