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

use GyMadarasz\WebApp\Service\Template;
use GyMadarasz\WebApp\Service\Globals;
use GyMadarasz\ChatBot\Service\Chats;
use GyMadarasz\WebApp\Service\FormToken;

/**
 * CreateChatPage
 *
 * @category  PHP
 * @package   GyMadarasz\ChatBot\Controller
 * @author    Gyula Madarasz <gyula.madarasz@gmail.com>
 * @copyright 2020 Gyula Madarasz
 * @license   Copyright (c) all right reserved.
 * @link      this
 */
class CreateChatPage
{
    /**
     * Method view
     *
     * @param Template $template template
     *
     * @return Template
     */
    public function view(Template $template): Template
    {
        return $template->create('create-chat.html');
    }

    /**
     * Method save
     *
     * @param Template  $template  template
     * @param Globals   $globals   globals
     * @param Chats     $chats     chats
     * @param FormToken $formToken formToken
     *
     * @return Template
     */
    public function save(
        Template $template,
        Globals $globals,
        Chats $chats,
        FormToken $formToken
    ): Template {
        $name = $globals->getPost('name');
        if (!$name) {
            return $template->create(
                'create-chat.html',
                [
                'error' => 'Please set Chat Name',
                ]
            );
        }
        $cid = $chats->create($name);
        if (!$cid) {
            return $template->create(
                'create-chat.html',
                [
                'name' => $name,
                'error' => 'Chat is not created',
                ]
            );
        }
        return $template->create(
            'edit-chat.html',
            [
            'token' => $formToken->get(),
            'id' => $cid,
            'name' => $name,
            'message' => 'Chat created',
            'messages' => $chats->loadTree($cid),
            ]
        );
    }
}
