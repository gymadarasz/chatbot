<?php declare(strict_types = 1);

namespace GyMadarasz\ChatBot\Controller;

use GyMadarasz\WebApp\Service\Template;
use GyMadarasz\WebApp\Service\Globals;
use GyMadarasz\ChatBot\Service\Chats;
use GyMadarasz\WebApp\Service\Mysql;

class CreateChatPage
{
    protected Template $template;
    protected Globals $globals;
    protected Chats $chats;
    protected Mysql $mysql;

    public function __construct(Template $template, Globals $globals, Chats $chats, Mysql $mysql)
    {
        $this->template = $template;
        $this->globals = $globals;
        $this->chats = $chats;
        $this->mysql = $mysql;
    }

    public function view(): Template
    {
        return $this->template->create('create-chat.html.php');
    }

    public function save(): Template
    {
        $name = $this->globals->getPost('name');
        if (!$name) {
            return $this->template->create('create-chat.html.php', [
                'error' => 'Please set Chat Name',
            ]);
        }
        $this->mysql->connect();
        $id = $this->chats->create($name);
        if (!$id) {
            return $this->template->create('create-chat.html.php', [
                'name' => $name,
                'error' => 'Chat is not created',
            ]);
        }
        return $this->template->create('edit-chat.html.php', [
            'id' => $id,
            'name' => $name,
            'message' => 'Chat created',
        ]);
    }
}
