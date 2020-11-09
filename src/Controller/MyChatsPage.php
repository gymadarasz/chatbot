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

use GyMadarasz\ChatBot\Service\Chats;
use GyMadarasz\WebApp\Service\FormToken;
use GyMadarasz\WebApp\Service\Globals;
use GyMadarasz\WebApp\Service\Template;

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
    protected FormToken $formToken;

    /**
     * Method __construct
     *
     * @param Template  $template  template
     * @param Globals   $globals   globals
     * @param Chats     $chats     chats
     * @param FormToken $formToken formToken
     */
    public function __construct(
        Template $template,
        Globals $globals,
        Chats $chats,
        FormToken $formToken
    ) {
        $this->template = $template;
        $this->globals = $globals;
        $this->chats = $chats;
        $this->formToken = $formToken;
    }

    /**
     * Method view
     *
     * @return Template
     */
    public function view(): Template
    {
        return $this->template->create(
            'index.html',
            [
                'body' => 'my-chats.html',
                'chats' => $this->chats->getList(),
                'token' => $this->formToken->get(),
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
        $output = [
            'body' => 'my-chats.html',
            'token' => $this->formToken->get(),
        ];
        if (!$this->chats->delete((int)$this->globals->getGet('id'))) {
            $output['error'] = 'Chat is not deleted';
            $output['chats'] = $this->chats->getList();
            return $this->template->create('my-chats.html', $output);
        }
        $output['message'] = 'Chat is deleted';
        $output['chats'] = $this->chats->getList();
        return $this->template->create('index.html', $output);
    }
}
