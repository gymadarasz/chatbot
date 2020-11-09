<?php declare(strict_types=1);

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
use GyMadarasz\ChatBot\Service\Conversations;
use GyMadarasz\WebApp\Service\Config;
use GyMadarasz\WebApp\Service\FormToken;
use GyMadarasz\WebApp\Service\Globals;
use GyMadarasz\WebApp\Service\Template;
use GyMadarasz\WebApp\Service\User;
use RuntimeException;

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
class ConversationPage
{
    const VIEW = 'only-last';
    
    protected string $token;
    
    protected FormToken $formToken;

    /**
     * Method __construct
     *
     * @param FormToken $formToken formToken
     */
    public function __construct(FormToken $formToken)
    {
        $this->formToken = $formToken;
    }
    
    /**
     * Method formTokenCheck
     *
     * @return bool
     */
    protected function formTokenCheck(): bool
    {
        $formTokenOk = $this->formToken->check();
        $this->token = $this->formToken->get();
        return $formTokenOk;
    }
    
    /**
     * Method getConversationData
     *
     * @param Conversations $conversations  conversations
     * @param int           $conversationId conversationId
     *
     * @return mixed[]
     */
    protected function getConversationData(
        Conversations $conversations,
        int $conversationId
    ): array {
        $conversation = $conversations->retrieve(
            $conversationId,
            self::VIEW === 'only-last'
        );
        if (!$conversation) {
            throw new RuntimeException('Conversaion not found: ' . $conversationId);
        }
        $responses = $conversations->getResponseMessages(
            (int)end($conversation)['message_id'],
            Conversations::TALKS_HUMAN
        );
        return [
            'conversation_id' => $conversationId,
            'conversation' => $conversation,
            'responses' => $responses,
        ];
    }
    
    /**
     * Method getErrorPageResponse
     *
     * @param Template $template template
     * @param Config   $config   config
     * @param string   $error    error
     *
     * @return Template
     */
    protected function getErrorPageResponse(
        Template $template,
        Config $config,
        string $error
    ): Template {
        return $template->create(
            'index.html',
            [
                'body' => 'error-page.html',
                'error' => $error,
                'base' => $config->get('baseUrl'),
            ]
        );
    }
    
    /**
     * Method getErrorMessageResponse
     *
     * @param Template      $template       template
     * @param Conversations $conversations  conversations
     * @param int           $conversationId conversationId
     * @param string[]      $chat           chat
     * @param string        $error          error
     *
     * @return Template
     */
    protected function getErrorMessageResponse(
        Template $template,
        Conversations $conversations,
        int $conversationId,
        array $chat,
        string $error
    ): Template {
        return $template->create(
            'index.html',
            array_merge(
                [
                    'body' => 'conversation.html',
                    'error' => $error,
                    'token' => $this->token,
                    'chat' => $chat,
                ],
                $this->getConversationData($conversations, $conversationId)
            ),
        );
    }
    
    /**
     * Method getConversationResponse
     *
     * @param Template      $template       template
     * @param Conversations $conversations  conversations
     * @param int           $conversationId conversationId
     * @param string[]      $chat           chat
     * @param string        $message        message
     *
     * @return Template
     */
    protected function getConversationResponse(
        Template $template,
        Conversations $conversations,
        int $conversationId,
        array $chat,
        string $message = null
    ): Template {
        return $template->create(
            'index.html',
            array_merge(
                [
                    'body' => 'conversation.html',
                    'message' => $message,
                    'token' => $this->token,
                    'chat' => $chat,
                ],
                $this->getConversationData($conversations, $conversationId)
            ),
        );
    }

    /**
     * Method startConversation
     *
     * @param Template      $template      template
     * @param Globals       $globals       globals
     * @param User          $user          user
     * @param Conversations $conversations conversations
     * @param Config        $config        config
     * @param Chats         $chats         chats
     *
     * @return Template
     */
    public function startConversation(
        Template $template,
        Globals $globals,
        User $user,
        Conversations $conversations,
        Config $config,
        Chats $chats
    ): Template {
        if (!$this->formTokenCheck()) {
            return $this->getErrorPageResponse(
                $template,
                $config,
                'Form token error'
            );
        }
        
        $chatId = (int)$globals->getPost('chat_id');
        
        $firstMessageId = $conversations->getFirstMessage($chatId);
        if (!$firstMessageId) {
            return $this->getErrorPageResponse(
                $template,
                $config,
                'Conversation has not starter message'
            );
        }
        
        $conversationId = $conversations->create(
            $user->getUid(),
            $chatId
        );
        if (!$conversationId) {
            return $this->getErrorPageResponse(
                $template,
                $config,
                'Conversation is not created'
            );
        }
        
        $conversationMsgId = $conversations->addMessage(
            $conversationId,
            $firstMessageId
        );
        
        $chat = $chats->retrieve($chatId);
        
        if (!$conversationMsgId) {
            return $this->getErrorMessageResponse(
                $template,
                $conversations,
                $conversationId,
                $chat,
                'Conversation is not started'
            );
        }
        
        return $this->getConversationResponse(
            $template,
            $conversations,
            $conversationId,
            $chat
        );
    }
    
    /**
     * Method humanRespond
     *
     * @param Template      $template      template
     * @param Globals       $globals       globals
     * @param Config        $config        config
     * @param Conversations $conversations conversations
     * @param Chats         $chats         chats
     *
     * @return Template
     */
    public function humanRespond(
        Template $template,
        Globals $globals,
        Config $config,
        Conversations $conversations,
        Chats $chats
    ): Template {
        if (!$this->formTokenCheck()) {
            return $this->getErrorPageResponse(
                $template,
                $config,
                'Form token error'
            );
        }
        
        $conversationId = (int)$globals->getPost('conversation_id');
        if (!$conversationId) {
            return $this->getErrorPageResponse(
                $template,
                $config,
                'Missing conversation'
            );
        }
        
        $humanRespMsgId = (int)$globals->getPost('human_response_message_id');
        
        $chat = $chats->retrieveFromConversationId($conversationId);
        
        if (!$humanRespMsgId) {
            return $this->getErrorMessageResponse(
                $template,
                $conversations,
                $conversationId,
                $chat,
                'Missing or invalid answer'
            );
        }
        
        $conversationMsgId = $conversations->addMessage(
            $conversationId,
            $humanRespMsgId
        );
        if (!$conversationMsgId) {
            return $this->getErrorMessageResponse(
                $template,
                $conversations,
                $conversationId,
                $chat,
                'Human message is not saved'
            );
        }
        
        $chatbotRespMsgs = $conversations->getResponseMessages(
            $humanRespMsgId,
            Conversations::TALKS_CHATBOT
        );
        if (!$chatbotRespMsgs) {
            return $this->getErrorMessageResponse(
                $template,
                $conversations,
                $conversationId,
                $chat,
                'Chatbot left the conversation'
            );
        }
        
        // TODO select the right chatbot reponse (conditions)
        // TODO also parse placeholders etc..
        $chatbotRespMsg = $chatbotRespMsgs[0];
        $chatbotRespMsgId = (int)$chatbotRespMsg['id'];
        
        // TODO now we just wait two second to make sure the ordering is right,
        // but have to figure out something about this issue here
        sleep(2);
        $conversationMsgId = $conversations->addMessage(
            $conversationId,
            $chatbotRespMsgId
        );
        if (!$conversationMsgId) {
            return $this->getErrorMessageResponse(
                $template,
                $conversations,
                $conversationId,
                $chat,
                'Chatbot message is not saved'
            );
        }
        
        return $this->getConversationResponse(
            $template,
            $conversations,
            $conversationId,
            $chat
        );
    }
}
