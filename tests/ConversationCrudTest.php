<?php declare(strict_types = 1);

namespace GyMadarasz\ChatBot\Test;

use RuntimeException;
use GyMadarasz\Test\AppTest;

class ConversationCrudTest extends AppTest
{
    public function test(): void
    {
        // clean up

        $this->logger->test('I am going to delete all messages from database.');

        $this->mysql->query("TRUNCATE message");
        $this->mysql->query("TRUNCATE message_to_message");


        $this->logger->test('I am going to create a chat.');
        $contents = $this->tester->post('?q=createchat', [
            'name' => 'conversation test',
        ]);
        $chatId = (int)($this->tester->getInputFieldValue('hidden', 'message[chat_id]', $contents)[0]);
        $this->tester->assertTrue(0 < $chatId);

        $this->logger->test('I am going to add a chatbot message but using a wrong token so it should fails.');
        $contents = $this->tester->post('?q=createmsg', [
            // 'token' => $token,
            'message' => [
                'chat_id' => $chatId,
                'talks' => 'chatbot',
                'content' => 'Hi, I am a ChatBot. How are you?',
            ],
        ]);
        $this->checkPageContainsError($contents, 'Form already processed');

        $token = $this->tester->getInputFieldValue('hidden', 'token', $contents)[0];

        $this->logger->test('I am going to add a chatbot message and this time I am sending the right token.');
        $contents = $this->tester->post('?q=createmsg', [
            'token' => $token,
            'message' => [
                'chat_id' => $chatId,
                'talks' => 'chatbot',
                'content' => 'Hi, I am a ChatBot. How are you?',
            ],
        ]);
        $this->tester->assertContains('Hi, I am a ChatBot. How are you?', $contents);

        $token = $this->tester->getInputFieldValue('hidden', 'token', $contents)[0];
        $requestMessageId = (int)($this->tester->getInputFieldValue('hidden', 'message_to_message[request_message_id]', $contents)[0]);

        $this->logger->test('I am going to add a possible human answere');
        $contents = $this->tester->post('?q=createmsg', [
            'token' => $token,
            'message' => [
                'chat_id' => $chatId,
                'talks' => 'human',
                'content' => 'Hi, I am good thank you! How about you?',
            ],
            'message_to_message' => [
                'request_message_id' => $requestMessageId,
            ]
        ]);
        $this->tester->assertContains('Hi, I am a ChatBot. How are you?', $contents);
        $this->tester->assertContains('Hi, I am good thank you! How about you?', $contents);

        $token = $this->tester->getInputFieldValue('hidden', 'token', $contents)[0];

        $this->logger->test('I am going to add one more possible human answere');
        $contents = $this->tester->post('?q=createmsg', [
            'token' => $token,
            'message' => [
                'chat_id' => $chatId,
                'talks' => 'human',
                'content' => 'I don\'t want to talk to you. Bye!',
            ],
            'message_to_message' => [
                'request_message_id' => $requestMessageId,
            ]
        ]);
        $this->tester->assertContains('Hi, I am a ChatBot. How are you?', $contents);
        $this->tester->assertContains('Hi, I am good thank you! How about you?', $contents);
        $this->tester->assertContains('I don\'t want to talk to you. Bye!', $contents);

        $token = $this->tester->getInputFieldValue('hidden', 'token', $contents)[0];

        $this->logger->test('I am going to add a chatbot message as an answer');
        $contents = $this->tester->post('?q=createmsg', [
            'token' => $token,
            'message' => [
                'chat_id' => $chatId,
                'talks' => 'chatbot',
                'content' => 'Thanks I am good too. Have a nice day! Bye.',
            ],
        ]);
        $this->tester->assertContains('Hi, I am a ChatBot. How are you?', $contents);
        $this->tester->assertContains('Hi, I am good thank you! How about you?', $contents);
        $this->tester->assertContains('I don\'t want to talk to you. Bye!', $contents);
        $this->tester->assertContains('Thanks I am good too. Have a nice day! Bye.', $contents);

        $token = $this->tester->getInputFieldValue('hidden', 'token', $contents)[0];

        $this->logger->test('I am going to add one more chatbot message as an answer');
        $contents = $this->tester->post('?q=createmsg', [
            'token' => $token,
            'message' => [
                'chat_id' => $chatId,
                'talks' => 'chatbot',
                'content' => 'Oke, no problem, see you next time.',
            ],
        ]);
        $this->tester->assertContains('Hi, I am a ChatBot. How are you?', $contents);
        $this->tester->assertContains('Hi, I am good thank you! How about you?', $contents);
        $this->tester->assertContains('I don\'t want to talk to you. Bye!', $contents);
        $this->tester->assertContains('Thanks I am good too. Have a nice day! Bye.', $contents);
        $this->tester->assertContains('Oke, no problem, see you next time.', $contents);

        $this->logger->test('I am going to assign a chatbot answer to the first human response');
        $token = $this->tester->getInputFieldValue('hidden', 'token', $contents)[0];
        $chatIds = $this->tester->getInputFieldValue('hidden', 'message[chat_id]', $contents);
        $this->tester->assertEquals(6, count($chatIds), 'Should have 6 form on the screen with message[chat_id] filed in it.');
        foreach ($chatIds as $cid) {
            $this->tester->assertEquals($chatId, (int)$cid, 'Each form on the screen should works with the same chat ID.');
        }
        $requestMessageIds = $this->tester->getInputFieldValue('hidden', 'message_to_message[request_message_id]', $contents);
        $this->tester->assertEquals(5, count($requestMessageIds), 'So far, should have 5 form on screen coperating with message_to_message relations.');
        $responseMessageIds = $this->tester->getSelectFieldValue('message_to_message[response_message_id]', $contents);
        $this->tester->assertEquals(2, count($responseMessageIds), 'Should have 2 form which is modifies the response_message_id in a message_to_message relation.');
        $this->tester->assertEquals(0, (int)$responseMessageIds[0]);
        $this->tester->assertEquals(0, (int)$responseMessageIds[1]);
        $responseMessageIdSelectsValues = $this->tester->getSelectsValues('message_to_message[response_message_id]', $contents);

        $contents = $this->tester->post('?q=modifymsg2msg', [
            'token' => $token,
            'message' => [
                'chat_id' => $chatId,
            ],
            'message_to_message' => [
                'request_message_id' => $requestMessageIds[0],
                'response_message_id' => $responseMessageIdSelectsValues[0][2],
            ],
        ]);
        
        $this->logger->test('I am going to assign a chatbot answer to the second human response');
        $token = $this->tester->getInputFieldValue('hidden', 'token', $contents)[0];
        $chatIds = $this->tester->getInputFieldValue('hidden', 'message[chat_id]', $contents);
        $this->tester->assertEquals(6, count($chatIds), 'Should have 6 form on the screen with message[chat_id] filed in it.');
        foreach ($chatIds as $cid) {
            $this->tester->assertEquals($chatId, (int)$cid, 'Each form on the screen should works with the same chat ID.');
        }
        $requestMessageIds = $this->tester->getInputFieldValue('hidden', 'message_to_message[request_message_id]', $contents);
        $this->tester->assertEquals(5, count($requestMessageIds), 'So far, should have 5 form on screen coperating with message_to_message relations.');
        $responseMessageIds = $this->tester->getSelectFieldValue('message_to_message[response_message_id]', $contents);
        $this->tester->assertEquals(2, count($responseMessageIds), 'Should have 2 form which is modifies the response_message_id in a message_to_message relation.');
        $this->tester->assertEquals($responseMessageIdSelectsValues[0][2], $responseMessageIds[0]);
        $this->tester->assertEquals('0', $responseMessageIds[1]);
        $responseMessageIdSelectsValues = $this->tester->getSelectsValues('message_to_message[response_message_id]', $contents);

        $contents = $this->tester->post('?q=modifymsg2msg', [
            'token' => $token,
            'message' => [
                'chat_id' => $chatId,
            ],
            'message_to_message' => [
                'request_message_id' => $requestMessageIds[1],
                'response_message_id' => $responseMessageIdSelectsValues[1][3],
            ],
        ]);


        $token = $this->tester->getInputFieldValue('hidden', 'token', $contents)[0];
        $chatIds = $this->tester->getInputFieldValue('hidden', 'message[chat_id]', $contents);
        $this->tester->assertEquals(6, count($chatIds), 'Should have 6 form on the screen with message[chat_id] filed in it.');
        foreach ($chatIds as $cid) {
            $this->tester->assertEquals($chatId, (int)$cid, 'Each form on the screen should works with the same chat ID.');
        }
        $requestMessageIds = $this->tester->getInputFieldValue('hidden', 'message_to_message[request_message_id]', $contents);
        $this->tester->assertEquals(5, count($requestMessageIds), 'So far, should have 5 form on screen coperating with message_to_message relations.');
        $responseMessageIds = $this->tester->getSelectFieldValue('message_to_message[response_message_id]', $contents);
        $this->tester->assertEquals(2, count($responseMessageIds), 'Should have 2 form which is modifies the response_message_id in a message_to_message relation.');
        $this->tester->assertEquals($responseMessageIdSelectsValues[0][2], $responseMessageIds[0]);
        $this->tester->assertEquals($responseMessageIdSelectsValues[1][3], $responseMessageIds[1]);

        $deleteMessageLinks = $this->tester->getLinks('?q=delmsg&id=', $contents);
        $this->tester->assertEquals(5, count($deleteMessageLinks));
        while ($deleteMessageLinks) {
            $contents = $this->tester->get($deleteMessageLinks[0]);
            $deleteMessageLinks = $this->tester->getLinks('?q=delmsg&id=', $contents);
        }

        $deleteMessageLinks = $this->tester->getLinks('?q=delmsg&id=', $contents);
        $this->tester->assertEquals(0, count($deleteMessageLinks));
    }
}
