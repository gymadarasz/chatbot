<?php declare(strict_types = 1);

/**
 * PHP version 7.4
 *
 * @category  PHP
 * @package   GyMadarasz\ChatBot\Test
 * @author    Gyula Madarasz <gyula.madarasz@gmail.com>
 * @copyright 2020 Gyula Madarasz
 * @license   Copyright (c) all right reserved.
 * @link      this
 */

namespace GyMadarasz\ChatBot\Test;

use GyMadarasz\Test\AppTest;
use GyMadarasz\WebApp\Service\Mysql;
use GyMadarasz\WebApp\Service\Invoker;
use GyMadarasz\Test\Tester;
use GyMadarasz\ChatBot\Service\Chats;

/**
 * ConversationTest
 *
 * @category  PHP
 * @package   GyMadarasz\ChatBot\Test
 * @author    Gyula Madarasz <gyula.madarasz@gmail.com>
 * @copyright 2020 Gyula Madarasz
 * @license   Copyright (c) all right reserved.
 * @link      this
 */
class ConversationTest
{
    protected Tester $tester;
    
    /**
     * Method test
     *
     * @param Tester $tester tester
     * @param Mysql  $mysql  mysql
     *
     * @return void
     */
    public function test(Tester $tester, Mysql $mysql): void
    {
        $this->tester = $tester;
        $this->setupAChat($mysql);
    }

    /**
     * Method setupAChat
     *
     * @param Mysql $mysql mysql
     *
     * @return void
     */
    protected function setupAChat(Mysql $mysql): void
    {
        $mysql->query("TRUNCATE chat");
        $mysql->query("TRUNCATE message");
        $mysql->query("TRUNCATE message_to_message");

        $chats = new Chats($mysql);
        
        $chatId = (string)$chats->create('Test Chat 1');

        $cbotHiWntToKnoScrt = (string)$chats->createMessage(
            [
            'chat_id' => $chatId,
            'talks' => 'chatbot',
            'content' => 'Hi, how are you? Do you want to know a secret?'
            ]
        );
        $humanYes = (string)$chats->createMessage(
            [
            'chat_id' => $chatId,
            'talks' => 'human',
            'content' => 'Yes',
            ]
        );
        $humanNo = (string)$chats->createMessage(
            [
            'chat_id' => $chatId,
            'talks' => 'human',
            'content' => 'No, thanks',
            ]
        );
        $chats->createMessageToMessage(
            [
            'request_message_id' => $cbotHiWntToKnoScrt,
            'response_message_id' => $humanYes,
            ]
        );
        $chats->createMessageToMessage(
            [
            'request_message_id' => $cbotHiWntToKnoScrt,
            'response_message_id' => $humanNo,
            ]
        );
        $chatbotTellsSecret = (string)$chats->createMessage(
            [
            'chat_id' => $chatId,
            'talks' => 'chatbot',
            'content' => 'Oke, here is my secret is "apple pie", Bye',
            ]
        );
        $chats->createMessageToMessage(
            [
            'request_message_id' => $humanYes,
            'response_message_id' => $chatbotTellsSecret,
            ]
        );
        $chatbotSaysBye = (string)$chats->createMessage(
            [
            'chat_id' => $chatId,
            'talks' => 'chatbot',
            'content' => 'Oke, bye',
            ]
        );
        $chats->createMessageToMessage(
            [
            'request_message_id' => $humanNo,
            'response_message_id' => $chatbotSaysBye,
            ]
        );
    }
}
