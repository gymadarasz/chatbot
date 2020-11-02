<?php declare(strict_types = 1);

namespace GyMadarasz\ChatBot\Controller;

use GyMadarasz\WebApp\Service\Template;
use GyMadarasz\WebApp\Service\Globals;
use GyMadarasz\ChatBot\Service\Chats;
use GyMadarasz\WebApp\Service\FormToken;

class CreateChatPage
{
    public function view(Template $template): Template
    {
        return $template->create('create-chat.html.php');
    }

    public function save(Template $template, Globals $globals, Chats $chats, FormToken $formToken): Template
    {
        $name = $globals->getPost('name');
        if (!$name) {
            return $template->create('create-chat.html.php', [
                'error' => 'Please set Chat Name',
            ]);
        }
        $id = $chats->create($name);
        if (!$id) {
            return $template->create('create-chat.html.php', [
                'name' => $name,
                'error' => 'Chat is not created',
            ]);
        }
        return $template->create('edit-chat.html.php', [
            'token' => $formToken->get(),
            'id' => $id,
            'name' => $name,
            'message' => 'Chat created',
            'messages' => $chats->loadTree($id),
        ]);
    }
}
