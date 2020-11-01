<?php declare(strict_types = 1);

namespace GyMadarasz\ChatBot\Controller;

use GyMadarasz\WebApp\Service\Template;
use GyMadarasz\WebApp\Service\Globals;
use GyMadarasz\ChatBot\Service\Chats;
use GyMadarasz\WebApp\Service\Mysql;
use GyMadarasz\WebApp\Service\FormToken;

class CreateChatPage
{
    protected Template $template;
    protected Globals $globals;
    protected Chats $chats;
    protected FormToken $formToken;

    public function __construct(Template $template, Globals $globals, Chats $chats, FormToken $formToken)
    {
        $this->template = $template;
        $this->globals = $globals;
        $this->chats = $chats;
        $this->formToken = $formToken;
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
        $id = $this->chats->create($name);
        if (!$id) {
            return $this->template->create('create-chat.html.php', [
                'name' => $name,
                'error' => 'Chat is not created',
            ]);
        }
        return $this->template->create('edit-chat.html.php', [
            'token' => $this->formToken->get(),
            'id' => $id,
            'name' => $name,
            'message' => 'Chat created',
            'messages' => $this->chats->loadTree($id),
        ]);
    }
}
