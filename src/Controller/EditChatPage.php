<?php declare(strict_types = 1);

namespace GyMadarasz\ChatBot\Controller;

use RuntimeException;
use GyMadarasz\WebApp\Service\Template;
use GyMadarasz\WebApp\Service\Globals;
use GyMadarasz\ChatBot\Service\Chats;
use GyMadarasz\WebApp\Service\FormToken;

class EditChatPage
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
        $id = (int)$this->globals->getGet('id');
        return $this->viewChat($id);
    }

    public function edit(): Template
    {
        $id = (int)$this->globals->getPost('id');
        $name = $this->globals->getPost('name');
        if (!$id) {
            return $this->template->create('create-chat.html.php', [
                'name' => $name,
                'error' => 'Chat is missing',
            ]);
        }
        if (!$name) {
            return $this->template->create('edit-chat.html.php', [
                'token' => $this->formToken->get(),
                'id' => $id,
                'error' => 'Chat Name is missing',
                'messages' => $this->chats->loadTree($id),
            ]);
        }
        if (!$this->chats->modify($id, $name)) {
            return $this->template->create('edit-chat.html.php', [
                'token' => $this->formToken->get(),
                'id' => $id,
                'name' => $name,
                'error' => 'Chat is not modified',
                'messages' => $this->chats->loadTree($id),
            ]);
        }
        return $this->template->create('edit-chat.html.php', [
            'token' => $this->formToken->get(),
            'id' => $id,
            'name' => $name,
            'message' => 'Chat is modified',
            'messages' => $this->chats->loadTree($id),
        ]);
    }

    public function createMessage(): Template
    {
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
        if ($messageToMessage) {
            $messageToMessage['response_message_id'] = $messageId;
            $messageToMessageId = $this->chats->createMessageToMessage($messageToMessage);
        }
        return $this->viewChat(
            (int)$message['chat_id'],
            $messageId ? null : 'Message is not created'
        );
    }

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
                $messageToMessageAll = $this->chats->getMessageToMessageAllByRequestMessageId($requestMessageId);
                $count = count($messageToMessageAll);
                if ($count === 0) {
                    $success = (bool)$this->chats->createMessageToMessage($messageToMessage);
                } elseif ($count === 1) {
                    $responseMessageId = (int)$messageToMessage['response_message_id'];
                    $success = (bool)$this->chats->setMessageToMessageResponseMessageIdByRequestMessageId($requestMessageId, $responseMessageId);
                } else {
                    throw new RuntimeException(sprintf('Multiple (possible) responses for a human request message. (req:%s)', $requestMessageId));
                }
            break;

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

    private function viewChat(int $id, string $error = null): Template
    {
        if (!$chat = $this->chats->retrieve($id)) {
            return $this->template->create('my-chats.html.php', [
                'error' => 'Chat not found',
            ]);
        }
        $output = array_merge($chat, [
            'token' => $this->formToken->get(),
            'messages' => $this->chats->loadTree((int)$chat['id']),
        ]);
        if ($error) {
            $output['error'] = $error;
        }
        return $this->template->create('edit-chat.html.php', $output);
    }
}
