<?php declare(strict_types = 1);

namespace GyMadarasz\ChatBot\Test;

use GyMadarasz\Test\AppTest;
use GyMadarasz\WebApp\Service\Mysql;
use GyMadarasz\WebApp\Service\Invoker;
use GyMadarasz\Test\Tester;
use GyMadarasz\ChatBot\Service\Chats;

class ConversationTest
{
    public function test(Tester $tester, Mysql $mysql): void
    {
        $this->setupAChat($mysql);
    }

    private function setupAChat(Mysql $mysql): void
    {
        $mysql->query("TRUNCATE chat");
        $mysql->query("TRUNCATE message");
        $mysql->query("TRUNCATE message_to_message");

        $chats = new Chats($mysql);
        
        $chatId = (string)$chats->create('Test Chat 1');

        $chatbotHiWntToKnoSecret = (string)$chats->createMessage([
            'chat_id' => $chatId,
            'talks' => 'chatbot',
            'content' => 'Hi, how are you? Do you want to know a secret?'
        ]);
        $humanYes = (string)$chats->createMessage([
            'chat_id' => $chatId,
            'talks' => 'human',
            'content' => 'Yes',
        ]);
        $humanNo = (string)$chats->createMessage([
            'chat_id' => $chatId,
            'talks' => 'human',
            'content' => 'No, thanks',
        ]);
        $chats->createMessageToMessage([
            'request_message_id' => $chatbotHiWntToKnoSecret,
            'response_message_id' => $humanYes,
        ]);
        $chats->createMessageToMessage([
            'request_message_id' => $chatbotHiWntToKnoSecret,
            'response_message_id' => $humanNo,
        ]);
        $chatbotTellsSecret = (string)$chats->createMessage([
            'chat_id' => $chatId,
            'talks' => 'chatbot',
            'content' => 'Oke, here is my secret is "apple pie", Bye',
        ]);
        $chats->createMessageToMessage([
            'request_message_id' => $humanYes,
            'response_message_id' => $chatbotTellsSecret,
        ]);
        $chatbotSaysBye = (string)$chats->createMessage([
            'chat_id' => $chatId,
            'talks' => 'chatbot',
            'content' => 'Oke, bye',
        ]);
        $chats->createMessageToMessage([
            'request_message_id' => $humanNo,
            'response_message_id' => $chatbotSaysBye,
        ]);
    }
}
