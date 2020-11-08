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

use RuntimeException;
use GyMadarasz\WebApp\Service\Template;
use GyMadarasz\WebApp\Service\Globals;
use GyMadarasz\ChatBot\Service\Chats;
use GyMadarasz\WebApp\Service\FormToken;

/**
 * EditChatPage
 *
 * @category  PHP
 * @package   GyMadarasz\ChatBot\Controller
 * @author    Gyula Madarasz <gyula.madarasz@gmail.com>
 * @copyright 2020 Gyula Madarasz
 * @license   Copyright (c) all right reserved.
 * @link      this
 */
class EditChatPage
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
        $cid = (int)$this->globals->getGet('id');
        return $this->viewChat($cid);
    }

    /**
     * Method edit
     *
     * @return Template
     */
    public function edit(): Template
    {
        $cid = (int)$this->globals->getPost('id');
        $name = $this->globals->getPost('name');
        if (!$cid) {
            return $this->template->create(
                'create-chat.html',
                [
                'name' => $name,
                'error' => 'Chat is missing',
                ]
            );
        }
        if (!$name) {
            return $this->template->create(
                'edit-chat.html',
                [
                'token' => $this->formToken->get(),
                'id' => $cid,
                'error' => 'Chat Name is missing',
                'messages' => $this->chats->loadTree($cid),
                ]
            );
        }
        if (!$this->chats->modify($cid, $name)) {
            return $this->template->create(
                'edit-chat.html',
                [
                'token' => $this->formToken->get(),
                'id' => $cid,
                'name' => $name,
                'error' => 'Chat is not modified',
                'messages' => $this->chats->loadTree($cid),
                ]
            );
        }
        return $this->template->create(
            'edit-chat.html',
            [
            'token' => $this->formToken->get(),
            'id' => $cid,
            'name' => $name,
            'message' => 'Chat is modified',
            'messages' => $this->chats->loadTree($cid),
            ]
        );
    }

    /**
     * Method createMessage
     *
     * @return Template
     */
    public function createMessage(): Template
    {
        $error = null;
        $post = $this->globals->getPost();
        $message = $post['message'];
        if (!$this->formToken->check()) {
            return $this->viewChat(
                (int)$message['chat_id'],
                'Form already processed'
            );
        }
        $messageToMessage = $post['message_to_message'] ?? null;
        $messageId = $this->chats->createMessage($message);
        if (!$messageId) {
            $error = 'Message is not created';
        }
        if ($messageId && $messageToMessage) {
            $messageToMessage['response_message_id'] = $messageId;
            $messageToMessageId = $this->chats->createMessageToMessage(
                $messageToMessage
            );
            if (!$messageToMessageId) {
                $error = 'Message relaction is not created';
            }
        }
        return $this->viewChat((int)$message['chat_id'], $error);
    }

    /**
     * Method modifyMessageToMessage
     *
     * @return Template
     * @throws RuntimeException
     */
    public function modifyMessageToMessage(): Template
    {
        $success = false;
        $post = $this->globals->getPost();
        $message = $post['message'];
        $messageToMessage = $post['message_to_message'];
        $requestMessageId = (int)$messageToMessage['request_message_id'];
        $requestMessageTalks = $this->chats->getMessageTalks($requestMessageId);
        switch ($requestMessageTalks) {
        case 'human':
            $messageToMessageAll = $this->chats
                ->getMessageToMessageAllByRequestMessageId($requestMessageId);
            $count = count($messageToMessageAll);
            if ($count === 0) {
                $success = (bool)$this->chats->createMessageToMessage(
                    $messageToMessage
                );
                break;
            }
            if ($count === 1) {
                $responseMessageId = (int)$messageToMessage['response_message_id'];
                $success = (bool)$this->chats
                    ->setMessageToMessageResponseMessageIdByRequestMessageId(
                        $requestMessageId,
                        $responseMessageId
                    );
                break;
            }
            throw new RuntimeException(
                sprintf(
                    'Multiple (possible) responses for a human request message.'
                            . ' (req:%s)',
                    $requestMessageId
                )
            );

        case 'chatbot':
            // TODO ...
            break;

        default:
            throw new RuntimeException('Illegal talks: ' . $requestMessageTalks);
        }
        return $this->viewChat(
            (int)$message['chat_id'],
            $success ? null : 'Failed to join conversation pieces',
        );
    }

    /**
     * Method deleteMessage
     *
     * @return Template
     */
    public function deleteMessage(): Template
    {
        $messageId = (int)$this->globals->getGet('id');
        $chatId = $this->chats->getChatIdFromMessageId($messageId);
        $this->chats->deleteMessage($messageId);
        return $this->viewChat($chatId);
    }

    /**
     * Method viewChat
     *
     * @param int    $cid   id
     * @param string $error error
     *
     * @return Template
     */
    protected function viewChat(int $cid, string $error = null): Template
    {
        $chat = $this->chats->retrieve($cid);
        if (!$chat) {
            return $this->template->create(
                'my-chats.html',
                [
                'error' => 'Chat not found',
                ]
            );
        }
        $output = array_merge(
            $chat,
            [
            'token' => $this->formToken->get(),
            'messages' => $this->chats->loadTree((int)$chat['id']),
            ]
        );
        if ($error) {
            $output['error'] = $error;
        }
        return $this->template->create('edit-chat.html', $output);
    }
}
