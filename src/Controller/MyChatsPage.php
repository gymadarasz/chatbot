<?php declare(strict_types = 1);

namespace GyMadarasz\ChatBot\Controller;

use GyMadarasz\WebApp\Service\Template;
use GyMadarasz\WebApp\Service\Globals;
use GyMadarasz\ChatBot\Service\Chats;
use GyMadarasz\WebApp\Service\Mysql;

class MyChatsPage
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
        $this->mysql->connect();
        return $this->template->create('my-chats.html.php', [
            'chats' => $this->chats->getList(),
        ]);
    }

    public function delete(): Template
    {
        $this->mysql->connect();
        $output = [];
        if (!$this->chats->delete((int)$this->globals->getGet('id'))) {
            $output['error'] = 'Chat is not deleted';
        } else {
            $output['message'] = 'Chat is deleted';
        }
        $output['chats'] = $this->chats->getList();
        return $this->template->create('my-chats.html.php', $output);
    }
}
