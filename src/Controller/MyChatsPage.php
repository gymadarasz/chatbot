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
use GyMadarasz\WebApp\Service\Mysql;

/**
 * MyChatsPage
 *
 * @category  PHP
 * @package   GyMadarasz\ChatBot\Controller
 * @author    Gyula Madarasz <gyula.madarasz@gmail.com>
 * @copyright 2020 Gyula Madarasz
 * @license   Copyright (c) all right reserved.
 * @link      this
 */
class MyChatsPage
{
    protected Template $template;
    protected Globals $globals;
    protected Chats $chats;

    /**
     * Method __construct
     *
     * @param Template $template template
     * @param Globals  $globals  globals
     * @param Chats    $chats    chats
     */
    public function __construct(Template $template, Globals $globals, Chats $chats)
    {
        $this->template = $template;
        $this->globals = $globals;
        $this->chats = $chats;
    }

    /**
     * Method view
     *
     * @return Template
     */
    public function view(): Template
    {
        return $this->template->create(
            'my-chats.html',
            [
            'chats' => $this->chats->getList(),
            ]
        );
    }

    /**
     * Method delete
     *
     * @return Template
     */
    public function delete(): Template
    {
        $output = [];
        if (!$this->chats->delete((int)$this->globals->getGet('id'))) {
            $output['error'] = 'Chat is not deleted';
            $output['chats'] = $this->chats->getList();
            return $this->template->create('my-chats.html', $output);
        }
        $output['message'] = 'Chat is deleted';
        $output['chats'] = $this->chats->getList();
        return $this->template->create('my-chats.html', $output);
    }
}
