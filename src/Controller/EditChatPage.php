<?php declare(strict_types = 1);

namespace GyMadarasz\ChatBot\Controller;

use GyMadarasz\WebApp\Service\Template;
use GyMadarasz\WebApp\Service\Globals;
use GyMadarasz\ChatBot\Service\Chats;
use GyMadarasz\WebApp\Service\Mysql;

class EditChatPage
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
        $id = (int)$this->globals->getGet('id');
        $this->mysql->connect();
        if (!$chat = $this->chats->retrieve($id)) {
            return $this->template->create('my-chats.html.php', [
                'error' => 'Chat not found',
            ]);
        }
        return $this->template->create('edit-chat.html.php', $chat);
    }

    public function edit(): Template
    {
        $id = (int)$this->globals->getPost('id');
        $name = $this->globals->getPost('name');
        if (!$id) {
            return $this->template->create('create-chat.html.php', [
                'name' => $name,
                'error' => 'Chat is missing',
            ]);
        }
        if (!$name) {
            return $this->template->create('edit-chat.html.php', [
                'id' => $id,
                'error' => 'Chat Name is missing',
            ]);
        }
        $this->mysql->connect();
        if (!$this->chats->modify($id, $name)) {
            return $this->template->create('edit-chat.html.php', [
                'id' => $id,
                'name' => $name,
                'error' => 'Chat is not modified',
            ]);
        }
        return $this->template->create('edit-chat.html.php', [
            'id' => $id,
            'name' => $name,
            'message' => 'Chat is modified',
        ]);
    }
}
