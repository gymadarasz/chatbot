<?php declare(strict_types = 1);

namespace GyMadarasz\ChatBot\Controller;

use GyMadarasz\WebApp\Service\Template;

class MyChatsPage
{
    protected Template $template;

    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    public function view(): Template
    {
        return $this->template->create('my-chats.html.php');
    }
}
