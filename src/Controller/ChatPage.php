<?php declare(strict_types = 1);

namespace GyMadarasz\ChatBot\Controller;

use GyMadarasz\WebApp\Service\Globals;
use GyMadarasz\ChatBot\Service\Chats;
use GyMadarasz\WebApp\Service\Template;

class ChatPage
{
    public function chat(Template $template, Globals $globals, Chats $chat): Template
    {
        $chatId = $globals->getGet('id');
        $output = $template->create('chat.html.php'); // TODO .. !@#
        return $output;
    }
}
