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
 * Chats
 *
 * @category  PHP
 * @package   GyMadarasz\ChatBot\Service
 * @author    Gyula Madarasz <gyula.madarasz@gmail.com>
 * @copyright 2020 Gyula Madarasz
 * @license   Copyright (c) all right reserved.
 * @link      this
 */
class Chats
{
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
     * @param string $name name
     *
     * @return int
     */
    public function create(string $name): int
    {
        $nameEscaped = $this->mysql->escape($name);
        return $this->mysql->insert(
            "INSERT INTO chat (name) VALUES ('$nameEscaped');"
        );
    }

    /**
     * Method modify
     *
     * @param int    $cid  id
     * @param string $name name
     *
     * @return bool
     */
    public function modify(int $cid, string $name): bool
    {
        $nameEscaped = $this->mysql->escape($name);
        return (bool)$this->mysql->update(
            "UPDATE chat SET name = '$nameEscaped' "
                . "WHERE id = $cid AND deleted = 0 LIMIT 1;"
        );
    }

    /**
     * Method retrieve
     *
     * @param int $cid id
     *
     * @return string[]
     */
    public function retrieve(int $cid): array
    {
        return (array)$this->mysql->selectOne(
            "SELECT id, name FROM chat "
                . "WHERE id = $cid AND deleted = 0 LIMIT 1;"
        );
    }

    /**
     * Method getList
     *
     * @return string[][]
     */
    public function getList(): array
    {
        return $this->mysql->select("SELECT id, name FROM chat WHERE deleted = 0;");
    }

    /**
     * Method delete
     *
     * @param int $cid id
     *
     * @return bool
     */
    public function delete(int $cid): bool
    {
        return (bool)$this->mysql->update(
            "UPDATE chat SET deleted = 1 "
                . "WHERE id = $cid AND deleted = 0 LIMIT 1;"
        );
    }

    /**
     * Method loadTree
     *
     * @param int $chatId chatId
     *
     * @return array<array<string|string[][]>>
     */
    public function loadTree(int $chatId): array
    {
        $messages = $this->mysql->select(
            "
            SELECT id, talks, content FROM message 
            WHERE chat_id = $chatId AND talks = 'chatbot' AND deleted = 0;
        "
        );
        foreach ($messages as &$message) {
            $humanMessages = $this->mysql->select(
                "
                SELECT response_message_id as id, talks, content 
                FROM message_to_message
                JOIN message ON message.id = response_message_id
                WHERE request_message_id = {$message['id']} AND message.deleted = 0;
            "
            );
            foreach ($humanMessages as &$hmessage) {
                $messageToMessage = $this->mysql->selectOne(
                    "
                    SELECT response_message_id FROM message_to_message 
                    WHERE request_message_id = {$hmessage['id']}
                    LIMIT 1;
                "
                );
                $hmessage['response_message_id'] = $messageToMessage[
                    'response_message_id'] ?? '';
            }
            $message['human_response_messages'] = $humanMessages;
        }
        return $messages;
    }

    /**
     * Method createMessage
     *
     * @param string[] $message message
     *
     * @return int
     */
    public function createMessage(array $message): int
    {
        $chatIdEscaped = (int)$message['chat_id'];
        $talksEscaped = $this->mysql->escape($message['talks']);
        $contentEscaped = $this->mysql->escape($message['content']);
        return $this->mysql->insert(
            "INSERT INTO message (chat_id, talks, content) "
                . "VALUES ($chatIdEscaped, '$talksEscaped', '$contentEscaped');"
        );
    }

    /**
     * Method createMessageToMessage
     *
     * @param string[] $messageToMessage messageToMessage
     *
     * @return int
     */
    public function createMessageToMessage(array $messageToMessage): int
    {
        $reqMsgIdEscaped = (int)$messageToMessage['request_message_id'];
        $respMsgIdEscaped = (int)$messageToMessage['response_message_id'];
        return $this->mysql->insert(
            "INSERT INTO message_to_message "
                . "(request_message_id, response_message_id) "
                . "VALUES ($reqMsgIdEscaped, $respMsgIdEscaped);"
        );
    }

    /**
     * Method getMessageTalks
     *
     * @param int $cid id
     *
     * @return string
     */
    public function getMessageTalks(int $cid): string
    {
        $message = $this->mysql->selectOne(
            "SELECT talks FROM message WHERE id = $cid AND deleted = 0 LIMIT 1;"
        );
        return $message['talks'] ?? '';
    }

    /**
     * Method getMessageToMessageAllByRequestMessageId
     *
     * @param int $requestMessageId requestMessageId
     *
     * @return string[][]
     */
    public function getMessageToMessageAllByRequestMessageId(
        int $requestMessageId
    ): array {
        return $this->mysql->select(
            "SELECT * FROM message_to_message "
                . "WHERE request_message_id = $requestMessageId;"
        );
    }

    /**
     * Method setMessageToMessageResponseMessageIdByRequestMessageId
     *
     * @param int $requestMessageId  requestMessageId
     * @param int $responseMessageId responseMessageId
     *
     * @return int
     */
    public function setMessageToMessageResponseMessageIdByRequestMessageId(
        int $requestMessageId,
        int $responseMessageId
    ): int {
        return $this->mysql->update(
            "UPDATE message_to_message "
                . "SET response_message_id = $responseMessageId "
                . "WHERE request_message_id = $requestMessageId LIMIT 1;"
        );
    }

    /**
     * Method getChatIdFromMessageId
     *
     * @param int $messageId messageId
     *
     * @return int
     */
    public function getChatIdFromMessageId(int $messageId): int
    {
        $message = $this->mysql->selectOne(
            "SELECT chat_id FROM message "
                . "WHERE id = $messageId AND deleted = 0 LIMIT 1;"
        );
        return (int)($message['chat_id'] ?? 0);
    }

    /**
     * Method deleteMessage
     *
     * @param int $messageId messageId
     *
     * @return int
     */
    public function deleteMessage(int $messageId): int
    {
        return $this->mysql->update(
            "UPDATE message SET deleted = 1 "
                . "WHERE id = $messageId AND deleted = 0 LIMIT 1;"
        );
    }
}
