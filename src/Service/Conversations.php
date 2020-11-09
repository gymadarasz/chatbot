<?php declare(strict_types = 1);

/**
 * PHP version 7.4
 *
 * @category  PHP
 * @package   GyMadarasz\ChatBot\Service
 * @author    Gyula Madarasz <gyula.madarasz@gmail.com>
 * @copyright 2020 Gyula Madarasz
 * @license   Copyright (c) all right reserved.
 * @link      this
 */

namespace GyMadarasz\ChatBot\Service;

use GyMadarasz\WebApp\Service\Mysql;

/**
 * Conversations
 *
 * @category  PHP
 * @package   GyMadarasz\ChatBot\Service
 * @author    Gyula Madarasz <gyula.madarasz@gmail.com>
 * @copyright 2020 Gyula Madarasz
 * @license   Copyright (c) all right reserved.
 * @link      this
 */
class Conversations
{
    const TALKS_CHATBOT = 0;
    const TALKS_HUMAN = 1;
    
    const TALKS_ENUM = [
        self::TALKS_CHATBOT => 'chatbot',
        self::TALKS_HUMAN => 'human',
    ];
    
    protected Mysql $mysql;
    
    /**
     * Method __construct
     *
     * @param Mysql $mysql mysql
     */
    public function __construct(Mysql $mysql)
    {
        $this->mysql = $mysql;
    }
    
    /**
     * Method create
     *
     * @param int $userId userId
     * @param int $chatId chatId
     *
     * @return int
     */
    public function create(int $userId, int $chatId): int
    {
        $query = "INSERT INTO conversation "
                . "(user_id, chat_id) "
                . "VALUES ($userId, $chatId);";
        return $this->mysql->insert($query);
    }
    
    /**
     * Method retrieve
     *
     * @param int  $conversationId conversationId
     * @param bool $onlyLast       onlyLast
     *
     * @return string[][]
     */
    public function retrieve(int $conversationId, bool $onlyLast): array
    {
        $query = "
            SELECT 
                message.talks,
                message.content,
                conversation_message.message_id,    
                conversation_message.created_at
            FROM conversation_message
            JOIN message ON message.id = conversation_message.message_id
            WHERE conversation_id = $conversationId
            ORDER BY conversation_message.created_at
        ";
        if ($onlyLast) {
            $query .= " DESC LIMIT 1";
        }
        
        return $this->mysql->select($query);
    }

    /**
     * Method getFirstMessage
     *
     * @param int $chatId chatId
     *
     * @return int
     */
    public function getFirstMessage(int $chatId): int
    {
        $query = "
            SELECT 
                message.id
            FROM chat 
            JOIN message ON 
                message.chat_id = chat.id
            JOIN message_to_message ON 
                message_to_message.request_message_id = message.id OR 
                message_to_message.response_message_id = message.id
            WHERE 
                chat.id = $chatId AND 
                message.talks = 'chatbot' AND 
                message.deleted = 0 AND 
                message_to_message.request_message_id NOT IN (
                    SELECT 
                        message_to_message.response_message_id 
                    FROM chat 
                    JOIN message ON 
                        message.chat_id = chat.id
                    JOIN message_to_message ON 
                        message_to_message.request_message_id = message.id OR 
                        message_to_message.response_message_id = message.id
                    WHERE 
                        chat.id = $chatId AND 
                        message.deleted = 0
                )
            LIMIT 1;
        ";
        return (int)$this->mysql->selectOne($query)['id'] ?? 0;
    }

    /**
     * Method addMessage
     *
     * @param int $conversationId conversationId
     * @param int $messageId      messageId
     *
     * @return int
     */
    public function addMessage(int $conversationId, int $messageId): int
    {
        $query = "
            INSERT INTO conversation_message (conversation_id, message_id)
            VALUES ($conversationId, $messageId);
        ";
        return $this->mysql->insert($query);
    }

    /**
     * Method getResponseMessages
     *
     * @param int $reqMsgId reqMsgId
     * @param int $talks    talks
     *
     * @return string[][]
     */
    public function getResponseMessages(int $reqMsgId, int $talks): array
    {
        $query = "
            SELECT message.id, message.talks, message.content FROM message_to_message
            JOIN message ON message.id = message_to_message.response_message_id
            WHERE request_message_id = $reqMsgId 
                AND message.talks = '" . self::TALKS_ENUM[$talks] . "'
                AND message.deleted = 0
        ";
        return $this->mysql->select($query);
    }
}
