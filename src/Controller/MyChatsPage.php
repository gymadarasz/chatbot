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

    public function __construct(Template $template, Globals $globals, Chats $chats)
    {
        $this->template = $template;
        $this->globals = $globals;
        $this->chats = $chats;
    }

    public function view(): Template
    {
        return $this->template->create('my-chats.html.php', [
            'chats' => $this->chats->getList(),
        ]);
    }

    public function delete(): Template
    {
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
