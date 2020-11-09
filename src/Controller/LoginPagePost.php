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
use GyMadarasz\WebApp\Service\User;

/**
 * LoginPagePost
 *
 * @category  PHP
 * @package   GyMadarasz\ChatBot\Controller
 * @author    Gyula Madarasz <gyula.madarasz@gmail.com>
 * @copyright 2020 Gyula Madarasz
 * @license   Copyright (c) all right reserved.
 * @link      this
 */
class LoginPagePost
{
    /**
     * Method login
     *
     * @param Template  $template  template
     * @param User      $user      user
     * @param Globals   $globals   globals
     * @param Chats     $chats     chats
     * @param FormToken $formToken formToken
     *
     * @return Template
     */
    public function login(
        Template $template,
        User $user,
        Globals $globals,
        Chats $chats,
        FormToken $formToken
    ): Template {
        if ($user->doAuth(
            $globals->getPost('email', ''),
            $globals->getPost('password', '')
        )
        ) {
            $output = $template->create(
                'index.html',
                [
                    'body' => 'my-chats.html',
                    'chats' => $chats->getList(),
                    'token' => $formToken->get(),
                ]
            );
            $output->set('message', 'Login success');
            return $output;
        }
        $output = $template->create('index.html', ['body' => 'login.html']);
        $output->set('error', 'Login failed');
        return $output;
    }
}
