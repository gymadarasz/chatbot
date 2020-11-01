<?php declare(strict_types = 1);

namespace GyMadarasz\ChatBot\Controller;

use GyMadarasz\WebApp\Service\Template;
use GyMadarasz\WebApp\Service\Mysql;
use GyMadarasz\WebApp\Service\User;
use GyMadarasz\WebApp\Service\Globals;
use GyMadarasz\ChatBot\Service\Chats;

class LoginPagePost
{
    private Template $template;
    private Mysql $mysql;
    private User $user;
    private Globals $globals;
    private Chats $chats;

    public function __construct(Template $template, Mysql $mysql, User $user, Globals $globals, Chats $chats)
    {
        $this->template = $template;
        $this->mysql = $mysql;
        $this->user = $user;
        $this->globals = $globals;
        $this->chats = $chats;
    }

    /**
     * @return mixed
     */
    public function login()
    {
        $this->mysql->connect();
        if ($this->user->doAuth(
            $this->globals->getPost('email', ''),
            $this->globals->getPost('password', '')
        )) {
            $output = $this->template->create('my-chats.html.php', [
                'chats' => $this->chats->getList(),
            ]);
            $output->set('message', 'Login success');
        } else {
            $output = $this->template->create('login.html.php');
            $output->set('error', 'Login failed');
        }

        return $output;
    }
}
