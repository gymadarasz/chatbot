<?php declare(strict_types = 1);

namespace GyMadarasz\ChatBot\Service;

use GyMadarasz\WebApp\Service\Mysql;

class Chats
{
    private Mysql $mysql;

    public function __construct(Mysql $mysql)
    {
        $this->mysql = $mysql;
    }

    public function create(string $name): int
    {
        $_name = $this->mysql->escape($name);
        return $this->mysql->insert("INSERT INTO chat (name) VALUES ('$_name');");
    }

    public function modify(int $id, string $name): bool
    {
        $_name = $this->mysql->escape($name);
        return (bool)$this->mysql->update("UPDATE chat SET name = '$_name' WHERE id = $id AND deleted = 0 LIMIT 1;");
    }

    /** @return array<string> */
    public function retrieve(int $id): array
    {
        return (array)$this->mysql->selectOne("SELECT id, name FROM chat WHERE id = $id AND deleted = 0 LIMIT 1;");
    }

    /** @return array<array<string>> */
    public function getList(): array
    {
        return $this->mysql->select("SELECT id, name FROM chat WHERE deleted = 0;");
    }

    public function delete(int $id): bool
    {
        return (bool)$this->mysql->update("UPDATE chat SET deleted = 1 WHERE id = $id AND deleted = 0 LIMIT 1;");
    }

    /**
     * @return array<mixed>
     */
    public function loadTree(int $chatId): array
    {
        $messages = $this->mysql->select("
            SELECT id, talks, content FROM message 
            WHERE chat_id = $chatId AND talks = 'chatbot' AND deleted = 0;
        ");
        foreach ($messages as &$message) {
            $message['human_response_messages'] = $this->mysql->select("
                SELECT response_message_id as id, talks, content FROM message_to_message
                JOIN message ON message.id = response_message_id
                WHERE request_message_id = {$message['id']} AND message.deleted = 0;
            ");
            foreach ($message['human_response_messages'] as &$hmessage) {
                $messageToMessage = $this->mysql->selectOne("
                    SELECT response_message_id FROM message_to_message 
                    WHERE request_message_id = {$hmessage['id']}
                    LIMIT 1;
                ");
                $hmessage['response_message_id'] = $messageToMessage['response_message_id'] ?? 0;
            }
        }
        return $messages;
    }

    /**
     * @param array<string> $message
     */
    public function createMessage(array $message): int
    {
        $_chatId = (int)$message['chat_id'];
        $_talks = $this->mysql->escape($message['talks']);
        $_content = $this->mysql->escape($message['content']);
        return $this->mysql->insert("INSERT INTO message (chat_id, talks, content) VALUES ($_chatId, '$_talks', '$_content');");
    }

    /**
     * @param array<string> $messageToMessage
     */
    public function createMessageToMessage(array $messageToMessage): int
    {
        $_requestMessageId = (int)$messageToMessage['request_message_id'];
        $_responseMessageId = (int)$messageToMessage['response_message_id'];
        return $this->mysql->insert("INSERT INTO message_to_message (request_message_id, response_message_id) VALUES ($_requestMessageId, $_responseMessageId);");
    }

    /**
     * @return string
     */
    public function getMessageTalks(int $id): string
    {
        $message = $this->mysql->selectOne("SELECT talks FROM message WHERE id = $id AND deleted = 0 LIMIT 1;");
        return $message['talks'] ?? '';
    }

    /**
     * @return array<array<string>>
     */
    public function getMessageToMessageAllByRequestMessageId(int $requestMessageId): array
    {
        return $this->mysql->select("SELECT * FROM message_to_message WHERE request_message_id = $requestMessageId;");
    }

    public function setMessageToMessageResponseMessageIdByRequestMessageId(int $requestMessageId, int $responseMessageId): int
    {
        return $this->mysql->update("UPDATE message_to_message SET response_message_id = $responseMessageId WHERE request_message_id = $requestMessageId LIMIT 1;");
    }
}
