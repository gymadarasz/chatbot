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

use GyMadarasz\Test\AppChecker;
use GyMadarasz\Test\Inspector;
use GyMadarasz\Test\Tester;
use GyMadarasz\WebApp\Service\Config;
use GyMadarasz\WebApp\Service\Logger;
use GyMadarasz\WebApp\Service\Mysql;
use function count;

/**
 * ConversationCrudTest
 *
 * @category  PHP
 * @package   GyMadarasz\ChatBot\Test
 * @author    Gyula Madarasz <gyula.madarasz@gmail.com>
 * @copyright 2020 Gyula Madarasz
 * @license   Copyright (c) all right reserved.
 * @link      this
 */
class ConversationCrudTest
{
    protected Tester $tester;
    protected Config $config;
    protected Logger $logger;
    protected Mysql $mysql;
    protected Inspector $inspector;
    protected AppChecker $appChecker;

    /**
     * Method __construct
     *
     * @param Tester     $tester     tester
     * @param Config     $config     config
     * @param Logger     $logger     logger
     * @param Mysql      $mysql      mysql
     * @param Inspector  $inspector  inspector
     * @param AppChecker $appChecker appChecker
     */
    public function __construct(
        Tester $tester,
        Config $config,
        Logger $logger,
        Mysql $mysql,
        Inspector $inspector,
        AppChecker $appChecker
    ) {
        $this->tester = $tester;
        $this->config = $config;
        $this->logger = $logger;
        $this->mysql = $mysql;
        $this->inspector = $inspector;
        $this->appChecker = $appChecker;
    }
    
    /**
     * Method test
     *
     * @return void
     */
    public function test(): void
    {
        $this->testThenCleanup();
    }
    
    /**
     * Method testThenCleanup
     *
     * @return void
     */
    protected function testThenCleanup(): void
    {
        
        // clean up

        $this->logger->test(
            'I am going to delete all messages from database.'
        );

        $this->mysql->query("TRUNCATE chat");
        $this->mysql->query("TRUNCATE message");
        $this->mysql->query("TRUNCATE message_to_message");

        $this->testThenCreate();
    }
    
    /**
     * Method testThenCreate
     *
     * @return void
     */
    protected function testThenCreate(): void
    {
        $this->logger->test(
            'I am going to create a chat.'
        );
        $contents = $this->tester->post(
            '?q=createchat',
            [
            'name' => 'conversation test',
            ]
        );
        $chatId = (int)($this->inspector->getInputFieldValue(
            'hidden',
            'message[chat_id]',
            $contents
        )[0]);
        $this->tester->getAssertor()->assertTrue(0 < $chatId);

        $this->logger->test(
            'I am going to add a chatbot message but using a wrong token '
                . 'so it should fails.'
        );
        $contents = $this->tester->post(
            '?q=createmsg',
            [
            // 'token' => $token,
            'message' => [
                'chat_id' => $chatId,
                'talks' => 'chatbot',
                'content' => 'Hi, I am a ChatBot. How are you?',
            ],
            ]
        );
        $this->appChecker->checkPageContainsError(
            $contents,
            'Form already processed'
        );

        $token = $this->inspector->getInputFieldValue(
            'hidden',
            'token',
            $contents
        )[0];

        $this->logger->test(
            'I am going to add a chatbot message '
                . 'and this time I am sending the right token.'
        );
        $contents = $this->tester->post(
            '?q=createmsg',
            [
            'token' => $token,
            'message' => [
                'chat_id' => $chatId,
                'talks' => 'chatbot',
                'content' => 'Hi, I am a ChatBot. How are you?',
            ],
            ]
        );
        $this->tester->getAssertor()->assertContains(
            'Hi, I am a ChatBot. How are you?',
            $contents
        );

        $token = $this->inspector->getInputFieldValue(
            'hidden',
            'token',
            $contents
        )[0];
        $requestMessageId = (int)($this->inspector->getInputFieldValue(
            'hidden',
            'message_to_message[request_message_id]',
            $contents
        )[0]);
        $this->testCreateHuman(
            $token,
            $chatId,
            $requestMessageId
        );
    }
    
    /**
     * Method testCreateHuman
     *
     * @param string $token            token
     * @param int    $chatId           chatId
     * @param int    $requestMessageId requestMessageId
     *
     * @return void
     */
    protected function testCreateHuman(
        string $token,
        int $chatId,
        int $requestMessageId
    ): void {
        $this->logger->test(
            'I am going to add a possible human answere'
        );
        $contents = $this->tester->post(
            '?q=createmsg',
            [
            'token' => $token,
            'message' => [
                'chat_id' => $chatId,
                'talks' => 'human',
                'content' => 'Hi, I am good thank you! How about you?',
            ],
            'message_to_message' => [
                'request_message_id' => $requestMessageId,
            ]
            ]
        );
        $this->tester->getAssertor()->assertContains(
            'Hi, I am a ChatBot. How are you?',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            'Hi, I am good thank you! How about you?',
            $contents
        );

        $token = $this->inspector->getInputFieldValue(
            'hidden',
            'token',
            $contents
        )[0];

        $this->logger->test(
            'I am going to add one more possible human answere'
        );
        $contents = $this->tester->post(
            '?q=createmsg',
            [
            'token' => $token,
            'message' => [
                'chat_id' => $chatId,
                'talks' => 'human',
                'content' => 'I don\'t want to talk to you. Bye!',
            ],
            'message_to_message' => [
                'request_message_id' => $requestMessageId,
            ]
            ]
        );
        $this->tester->getAssertor()->assertContains(
            'Hi, I am a ChatBot. How are you?',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            'Hi, I am good thank you! How about you?',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            'I don\'t want to talk to you. Bye!',
            $contents
        );

        $token = $this->inspector->getInputFieldValue(
            'hidden',
            'token',
            $contents
        )[0];
        $this->testThenAnswer($token, $chatId);
    }
    
    /**
     * Method testThenAnswer
     *
     * @param string $token  token
     * @param int    $chatId chatId
     *
     * @return void
     */
    protected function testThenAnswer(string $token, int $chatId): void
    {
        $this->logger->test(
            'I am going to add a chatbot message as an answer'
        );
        $contents = $this->tester->post(
            '?q=createmsg',
            [
            'token' => $token,
            'message' => [
                'chat_id' => $chatId,
                'talks' => 'chatbot',
                'content' => 'Thanks I am good too. Have a nice day! Bye.',
            ],
            ]
        );
        $this->tester->getAssertor()->assertContains(
            'Hi, I am a ChatBot. How are you?',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            'Hi, I am good thank you! How about you?',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            'I don\'t want to talk to you. Bye!',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            'Thanks I am good too. Have a nice day! Bye.',
            $contents
        );

        $token = $this->inspector->getInputFieldValue(
            'hidden',
            'token',
            $contents
        )[0];

        $this->logger->test(
            'I am going to add one more chatbot message as an answer'
        );
        $contents = $this->tester->post(
            '?q=createmsg',
            [
            'token' => $token,
            'message' => [
                'chat_id' => $chatId,
                'talks' => 'chatbot',
                'content' => 'Oke, no problem, see you next time.',
            ],
            ]
        );
        $this->tester->getAssertor()->assertContains(
            'Hi, I am a ChatBot. How are you?',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            'Hi, I am good thank you! How about you?',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            'I don\'t want to talk to you. Bye!',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            'Thanks I am good too. Have a nice day! Bye.',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            'Oke, no problem, see you next time.',
            $contents
        );

        $this->testThenChatbotAnswer($contents, $chatId);
    }
    
    /**
     * Method testThenChatbotAnswer
     *
     * @param string $contents contents
     * @param int    $chatId   chatId
     *
     * @return void
     */
    protected function testThenChatbotAnswer(string $contents, int $chatId): void
    {
        $this->logger->test(
            'I am going to assign a chatbot answer to the first human response'
        );
        $token = $this->inspector->getInputFieldValue(
            'hidden',
            'token',
            $contents
        )[0];
        $chatIds = $this->inspector->getInputFieldValue(
            'hidden',
            'message[chat_id]',
            $contents
        );
        $this->tester->getAssertor()->assertEquals(
            6,
            count($chatIds),
            'Should have 6 form on the screen with message[chat_id] filed in it.'
        );
        foreach ($chatIds as $cid) {
            $this->tester->getAssertor()->assertEquals(
                $chatId,
                (int)$cid,
                'Each form on the screen should works with the same chat ID.'
            );
        }
        $requestMessageIds = $this->inspector->getInputFieldValue(
            'hidden',
            'message_to_message[request_message_id]',
            $contents
        );
        $this->tester->getAssertor()->assertEquals(
            5,
            count($requestMessageIds),
            'So far, should have 5 form on screen coperating '
                . 'with message_to_message relations.'
        );
        $responseMessageIds = $this->inspector->getSelectFieldValue(
            'message_to_message[response_message_id]',
            $contents
        );
        $this->tester->getAssertor()->assertEquals(
            2,
            count($responseMessageIds),
            'Should have 2 form which is modifies the response_message_id '
                . 'in a message_to_message relation.'
        );
        $this->tester->getAssertor()->assertEquals(
            0,
            (int)$responseMessageIds[0]
        );
        $this->tester->getAssertor()->assertEquals(
            0,
            (int)$responseMessageIds[1]
        );
        $respMsgIdSelsVals = $this->inspector->getSelectsValues(
            'message_to_message[response_message_id]',
            $contents
        );

        $contents = $this->tester->post(
            '?q=modifymsg2msg',
            [
            'token' => $token,
            'message' => [
                'chat_id' => $chatId,
            ],
            'message_to_message' => [
                'request_message_id' => $requestMessageIds[0],
                'response_message_id' => $respMsgIdSelsVals[0][2],
            ],
            ]
        );
        $this->testThenChatbotAnswerToSecond(
            $contents,
            $chatId,
            $respMsgIdSelsVals
        );
    }
    
    /**
     * Method testThenChatbotAnswerToSecond
     *
     * @param string     $contents          contents
     * @param int        $chatId            chatId
     * @param string[][] $respMsgIdSelsVals respMsgIdSelsVals
     *
     * @return void
     */
    protected function testThenChatbotAnswerToSecond(
        string $contents,
        int $chatId,
        array $respMsgIdSelsVals
    ): void {
        $this->logger->test(
            'I am going to assign a chatbot answer to the second human response'
        );
        $token = $this->inspector->getInputFieldValue(
            'hidden',
            'token',
            $contents
        )[0];
        $chatIds = $this->inspector->getInputFieldValue(
            'hidden',
            'message[chat_id]',
            $contents
        );
        $this->tester->getAssertor()->assertEquals(
            6,
            count($chatIds),
            'Should have 6 form on the screen with message[chat_id] filed in it.'
        );
        foreach ($chatIds as $cid) {
            $this->tester->getAssertor()->assertEquals(
                $chatId,
                (int)$cid,
                'Each form on the screen should works with the same chat ID.'
            );
        }
        $requestMessageIds = $this->inspector->getInputFieldValue(
            'hidden',
            'message_to_message[request_message_id]',
            $contents
        );
        $this->tester->getAssertor()->assertEquals(
            5,
            count($requestMessageIds),
            'So far, should have 5 form on screen coperating '
                . 'with message_to_message relations.'
        );
        $responseMessageIds = $this->inspector->getSelectFieldValue(
            'message_to_message[response_message_id]',
            $contents
        );
        $this->tester->getAssertor()->assertEquals(
            2,
            count($responseMessageIds),
            'Should have 2 form which is modifies the response_message_id '
                . 'in a message_to_message relation.'
        );
        $this->tester->getAssertor()->assertEquals(
            $respMsgIdSelsVals[0][2],
            $responseMessageIds[0]
        );
        $this->tester->getAssertor()->assertEquals(
            '0',
            $responseMessageIds[1]
        );
        $respMsgIdSelsVals = $this->inspector->getSelectsValues(
            'message_to_message[response_message_id]',
            $contents
        );
        
        $this->testThenModifyMsg2Msg(
            $token,
            $chatId,
            $requestMessageIds,
            $respMsgIdSelsVals
        );
    }
    
    /**
     * Method testThenModifyMsg2Msg
     *
     * @param string     $token             token
     * @param int        $chatId            chatId
     * @param mixed[]    $requestMessageIds requestMessageIds
     * @param string[][] $respMsgIdSelsVals respMsgIdSelsVals
     *
     * @return void
     */
    protected function testThenModifyMsg2Msg(
        string $token,
        int $chatId,
        array $requestMessageIds,
        array $respMsgIdSelsVals
    ): void {
        $contents = $this->tester->post(
            '?q=modifymsg2msg',
            [
            'token' => $token,
            'message' => [
                'chat_id' => $chatId,
            ],
            'message_to_message' => [
                'request_message_id' => $requestMessageIds[1],
                'response_message_id' => $respMsgIdSelsVals[1][3],
            ],
            ]
        );


        $token = $this->inspector->getInputFieldValue(
            'hidden',
            'token',
            $contents
        )[0];
        $chatIds = $this->inspector->getInputFieldValue(
            'hidden',
            'message[chat_id]',
            $contents
        );
        $this->tester->getAssertor()->assertEquals(
            6,
            count($chatIds),
            'Should have 6 form on the screen with message[chat_id] filed in it.'
        );
        foreach ($chatIds as $cid) {
            $this->tester->getAssertor()->assertEquals(
                $chatId,
                (int)$cid,
                'Each form on the screen should works with the same chat ID.'
            );
        }
        $requestMessageIds = $this->inspector->getInputFieldValue(
            'hidden',
            'message_to_message[request_message_id]',
            $contents
        );
        $this->tester->getAssertor()->assertEquals(
            5,
            count($requestMessageIds),
            'So far, should have 5 form on screen coperating '
                . 'with message_to_message relations.'
        );
        $responseMessageIds = $this->inspector->getSelectFieldValue(
            'message_to_message[response_message_id]',
            $contents
        );
        $this->tester->getAssertor()->assertEquals(
            2,
            count($responseMessageIds),
            'Should have 2 form which is modifies the response_message_id '
                . 'in a message_to_message relation.'
        );
        $this->tester->getAssertor()->assertEquals(
            $respMsgIdSelsVals[0][2],
            $responseMessageIds[0]
        );
        $this->tester->getAssertor()->assertEquals(
            $respMsgIdSelsVals[1][3],
            $responseMessageIds[1]
        );

        $deleteMessageLinks = $this->inspector->getLinks(
            '?q=delmsg&id=',
            $contents
        );
        $this->tester->getAssertor()->assertEquals(
            5,
            count($deleteMessageLinks)
        );
        while ($deleteMessageLinks) {
            $contents = $this->tester->get($deleteMessageLinks[0]);
            $deleteMessageLinks = $this->inspector->getLinks(
                '?q=delmsg&id=',
                $contents
            );
        }

        $deleteMessageLinks = $this->inspector->getLinks(
            '?q=delmsg&id=',
            $contents
        );
        $this->tester->getAssertor()->assertEquals(
            0,
            count($deleteMessageLinks)
        );
    }
}
