<?php declare(strict_types = 1);

/**
 * PHP version 7.4
 *
 * @category  PHP
 * @package   GyMadarasz\ChatBot\Controller
 * @author    Gyula Madarasz <gyula.madarasz@gmail.com>
 * @copyright 2020 Gyula Madarasz
 * @license   Copyright (c) all right reserved.
 * @link      this
 */

namespace GyMadarasz\ChatBot\Controller;

use GyMadarasz\WebApp\Service\Globals;
use GyMadarasz\ChatBot\Service\Chats;
use GyMadarasz\WebApp\Service\Template;

/**
 * ChatPage
 *
 * @category  PHP
 * @package   GyMadarasz\ChatBot\Controller
 * @author    Gyula Madarasz <gyula.madarasz@gmail.com>
 * @copyright 2020 Gyula Madarasz
 * @license   Copyright (c) all right reserved.
 * @link      this
 */
class ChatPage
{
    /**
     * Method chat
     *
     * @param Template $template template
     * @param Globals  $globals  globals
     * @param Chats    $chats    chats
     *
     * @return Template
     */
    public function chat(
        Template $template,
        Globals $globals,
        Chats $chats
    ): Template {
        $cid = $globals->getGet('id');
        $chat = $chats->retrieve($cid);
        $template->create(
            'chat1.html',
            [
            'chat' => $chat,
            ]
        );
        return $template;
    }
}
