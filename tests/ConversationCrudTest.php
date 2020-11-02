<?php declare(strict_types = 1);

namespace GyMadarasz\ChatBot\Test;

use RuntimeException;
use GyMadarasz\Test\AppTest;
use GyMadarasz\Test\Tester;

class ConversationCrudTest extends AppTest
{
    public function run(Tester $tester): void
    {
        // clean up

        $this->logger->test('I am going to delete all messages from database.');

        $this->mysql->query("TRUNCATE message");
        $this->mysql->query("TRUNCATE message_to_message");


        $this->logger->test('I am going to create a chat.');
        $contents = $tester->post('?q=createchat', [
            'name' => 'conversation test',
        ]);
        $chatId = (int)($this->getInputFieldValue('hidden', 'message[chat_id]', $contents)[0]);
        $tester->assertTrue(0 < $chatId);

        $this->logger->test('I am going to add a chatbot message but using a wrong token so it should fails.');
        $contents = $tester->post('?q=createmsg', [
            // 'token' => $token,
            'message' => [
                'chat_id' => $chatId,
                'talks' => 'chatbot',
                'content' => 'Hi, I am a ChatBot. How are you?',
            ],
        ]);
        $this->checkPageContainsError($tester, $contents, 'Form already processed');

        $token = $this->getInputFieldValue('hidden', 'token', $contents)[0];

        $this->logger->test('I am going to add a chatbot message and this time I am sending the right token.');
        $contents = $tester->post('?q=createmsg', [
            'token' => $token,
            'message' => [
                'chat_id' => $chatId,
                'talks' => 'chatbot',
                'content' => 'Hi, I am a ChatBot. How are you?',
            ],
        ]);
        $tester->assertContains('Hi, I am a ChatBot. How are you?', $contents);

        $token = $this->getInputFieldValue('hidden', 'token', $contents)[0];
        $requestMessageId = (int)($this->getInputFieldValue('hidden', 'message_to_message[request_message_id]', $contents)[0]);

        $this->logger->test('I am going to add a possible human answere');
        $contents = $tester->post('?q=createmsg', [
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
        $tester->assertContains('Hi, I am a ChatBot. How are you?', $contents);
        $tester->assertContains('Hi, I am good thank you! How about you?', $contents);

        $token = $this->getInputFieldValue('hidden', 'token', $contents)[0];

        $this->logger->test('I am going to add one more possible human answere');
        $contents = $tester->post('?q=createmsg', [
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
        $tester->assertContains('Hi, I am a ChatBot. How are you?', $contents);
        $tester->assertContains('Hi, I am good thank you! How about you?', $contents);
        $tester->assertContains('I don\'t want to talk to you. Bye!', $contents);

        $token = $this->getInputFieldValue('hidden', 'token', $contents)[0];

        $this->logger->test('I am going to add a chatbot message as an answer');
        $contents = $tester->post('?q=createmsg', [
            'token' => $token,
            'message' => [
                'chat_id' => $chatId,
                'talks' => 'chatbot',
                'content' => 'Thanks I am good too. Have a nice day! Bye.',
            ],
        ]);
        $tester->assertContains('Hi, I am a ChatBot. How are you?', $contents);
        $tester->assertContains('Hi, I am good thank you! How about you?', $contents);
        $tester->assertContains('I don\'t want to talk to you. Bye!', $contents);
        $tester->assertContains('Thanks I am good too. Have a nice day! Bye.', $contents);

        $token = $this->getInputFieldValue('hidden', 'token', $contents)[0];

        $this->logger->test('I am going to add one more chatbot message as an answer');
        $contents = $tester->post('?q=createmsg', [
            'token' => $token,
            'message' => [
                'chat_id' => $chatId,
                'talks' => 'chatbot',
                'content' => 'Oke, no problem, see you next time.',
            ],
        ]);
        $tester->assertContains('Hi, I am a ChatBot. How are you?', $contents);
        $tester->assertContains('Hi, I am good thank you! How about you?', $contents);
        $tester->assertContains('I don\'t want to talk to you. Bye!', $contents);
        $tester->assertContains('Thanks I am good too. Have a nice day! Bye.', $contents);
        $tester->assertContains('Oke, no problem, see you next time.', $contents);

        $this->logger->test('I am going to assign a chatbot answer to the first human response');
        $token = $this->getInputFieldValue('hidden', 'token', $contents)[0];
        $chatIds = $this->getInputFieldValue('hidden', 'message[chat_id]', $contents);
        $tester->assertEquals(6, count($chatIds), 'Should have 6 form on the screen with message[chat_id] filed in it.');
        foreach ($chatIds as $cid) {
            $tester->assertEquals($chatId, (int)$cid, 'Each form on the screen should works with the same chat ID.');
        }
        $requestMessageIds = $this->getInputFieldValue('hidden', 'message_to_message[request_message_id]', $contents);
        $tester->assertEquals(5, count($requestMessageIds), 'So far, should have 5 form on screen coperating with message_to_message relations.');
        $responseMessageIds = $this->getSelectFieldValue('message_to_message[response_message_id]', $contents);
        $tester->assertEquals(2, count($responseMessageIds), 'Should have 2 form which is modifies the response_message_id in a message_to_message relation.');
        $tester->assertEquals(0, (int)$responseMessageIds[0]);
        $tester->assertEquals(0, (int)$responseMessageIds[1]);
        $responseMessageIdSelectsValues = $this->getSelectsValues('message_to_message[response_message_id]', $contents);

        $contents = $tester->post('?q=modifymsg2msg', [
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
        $token = $this->getInputFieldValue('hidden', 'token', $contents)[0];
        $chatIds = $this->getInputFieldValue('hidden', 'message[chat_id]', $contents);
        $tester->assertEquals(6, count($chatIds), 'Should have 6 form on the screen with message[chat_id] filed in it.');
        foreach ($chatIds as $cid) {
            $tester->assertEquals($chatId, (int)$cid, 'Each form on the screen should works with the same chat ID.');
        }
        $requestMessageIds = $this->getInputFieldValue('hidden', 'message_to_message[request_message_id]', $contents);
        $tester->assertEquals(5, count($requestMessageIds), 'So far, should have 5 form on screen coperating with message_to_message relations.');
        $responseMessageIds = $this->getSelectFieldValue('message_to_message[response_message_id]', $contents);
        $tester->assertEquals(2, count($responseMessageIds), 'Should have 2 form which is modifies the response_message_id in a message_to_message relation.');
        $tester->assertEquals($responseMessageIdSelectsValues[0][2], $responseMessageIds[0]);
        $tester->assertEquals('0', $responseMessageIds[1]);
        $responseMessageIdSelectsValues = $this->getSelectsValues('message_to_message[response_message_id]', $contents);

        $contents = $tester->post('?q=modifymsg2msg', [
            'token' => $token,
            'message' => [
                'chat_id' => $chatId,
            ],
            'message_to_message' => [
                'request_message_id' => $requestMessageIds[1],
                'response_message_id' => $responseMessageIdSelectsValues[1][3],
            ],
        ]);


        $token = $this->getInputFieldValue('hidden', 'token', $contents)[0];
        $chatIds = $this->getInputFieldValue('hidden', 'message[chat_id]', $contents);
        $tester->assertEquals(6, count($chatIds), 'Should have 6 form on the screen with message[chat_id] filed in it.');
        foreach ($chatIds as $cid) {
            $tester->assertEquals($chatId, (int)$cid, 'Each form on the screen should works with the same chat ID.');
        }
        $requestMessageIds = $this->getInputFieldValue('hidden', 'message_to_message[request_message_id]', $contents);
        $tester->assertEquals(5, count($requestMessageIds), 'So far, should have 5 form on screen coperating with message_to_message relations.');
        $responseMessageIds = $this->getSelectFieldValue('message_to_message[response_message_id]', $contents);
        $tester->assertEquals(2, count($responseMessageIds), 'Should have 2 form which is modifies the response_message_id in a message_to_message relation.');
        $tester->assertEquals($responseMessageIdSelectsValues[0][2], $responseMessageIds[0]);
        $tester->assertEquals($responseMessageIdSelectsValues[1][3], $responseMessageIds[1]);

        $deleteMessageLinks = $this->getLinks('?q=delmsg&id=', $contents);
        $tester->assertEquals(5, count($deleteMessageLinks));
        while ($deleteMessageLinks) {
            $contents = $tester->get($deleteMessageLinks[0]);
            $deleteMessageLinks = $this->getLinks('?q=delmsg&id=', $contents);
        }

        $deleteMessageLinks = $this->getLinks('?q=delmsg&id=', $contents);
        $tester->assertEquals(0, count($deleteMessageLinks));
    }

    /** @return array<string> */
    private function getLinks(string $hrefStarts, string $contents): array
    {
        if (!preg_match_all('/<a href="(' . preg_quote($hrefStarts) . '[^"]*)"/', $contents, $matches)) {
            return [];
        }
        return $matches[1];
    }


    /**
     * @return array<array<string>>
     */
    private function getSelectsValues(string $name, string $contents): array
    {
        $selects = $this->getSelectFieldContents($name, $contents);
        $selectsValues = [];
        foreach ($selects as $select) {
            $options = $this->getSelectOptions($select);
            $values = [];
            foreach ($options as $option) {
                $values[] = $this->getOptionValue($option);
            }
            $selectsValues[] = $values;
        }
        return $selectsValues;
    }

    /**
     * @return string
     */
    private function getOptionValue(string $option): string
    {
        if (!preg_match('/<option\b.+?\bvalue\b\s*=\s*"(.+?)"/', $option, $matches)) {
            throw new RuntimeException('Unrecognised value in option: ' . $option); // TODO check inner text??
        }
        return $matches[1];
    }

    /**
     * @return array<string>
     */
    private function getSelectOptions(string $select): array
    {
        if (!preg_match_all('/<option(.+?)<\/option>/s', $select, $matches)) {
            return [];
        }
        return $matches[0];
    }

    /**
     * @return mixed
     */
    private function getInputFieldValue(string $type, string $name, string $contents)
    {
        if (!preg_match_all('/<input\s+type\s*=\s*"' . $type . '"\s*name\s*=\s*"' . preg_quote($name) . '"\s*value=\s*"([^"]*)"/', $contents, $matches)) {
            throw new RuntimeException('Input element not found:  <input type="' . $type . '" name="' . $name . '" value=...>');
        }
        if (!isset($matches[1]) || !isset($matches[1][0])) {
            throw new RuntimeException('Input element does not have a value: <input type="' . $type . '" name="' . $name . '" value=...>');
        }
        return $matches[1];
    }

    /**
     * @return array<string>
     */
    private function getSelectFieldValue(string $name, string $contents): array
    {
        $selects = $this->getSelectFieldContents($name, $contents);
        $values = [];
        foreach ($selects as $select) {
            $multiple = $this->isMultiSelectField($select);
            unset($value);
            if ($options = $this->getOptionFieldContents($select)) {
                if ($multiple) {
                    $value = [];
                    foreach ($options as $option) {
                        if ($this->isOptionSelected($option)) {
                            $value[] = $this->getOptionFieldValue($option);
                        }
                    }
                } else {
                    foreach ($options as $option) {
                        if ($this->isOptionSelected($option) || !isset($value)) {
                            $value = $this->getOptionFieldValue($option);
                        }
                    }
                    $values[] = $value;
                }
            } else {
                throw new RuntimeException('A select element has not any option: ' . explode('\n', $select)[0] . '...');
            }
        }
        return $values;
    }

    private function getOptionFieldValue(string $option): string
    {
        if (!preg_match('/<option\b.+?\bvalue\b\s*=\s*"(.+?)"/', $option, $matches)) {
            throw new RuntimeException('Unrecognised value in option: ' . $option);
        }
        return $matches[1];
    }

    private function isOptionSelected(string $option): bool
    {
        return (bool)preg_match('/<option\s[^>]*\bselected\b/', $option, $matches);
    }

    private function isMultiSelectField(string $select): bool
    {
        return (bool)preg_match('/<select\s[^>]*\bmultiple\b/', $select, $matches);
    }

    /**
     * @return array<string>
     */
    private function getOptionFieldContents(string $select): array
    {
        if (!preg_match_all('/<option(.+?)<\/option>/s', $select, $matches)) {
            throw new RuntimeException('Unrecognised options');
        }
        return $matches[0];
    }
    
    /**
     * @return array<string>
     */
    private function getSelectFieldContents(string $name, string $contents): array
    {
        if (!preg_match_all('/<select\s+name\s*=\s*"' . preg_quote($name) . '"(.+?)<\/select>/s', $contents, $matches)) {
            throw new RuntimeException('Select element not found: <select name="' . $name . '"...</select>');
        }
        if (!isset($matches[0])) {
            throw new RuntimeException('Select element does not have a value: <select name="' . $name . '"...</select>');
        }
        return $matches[0];
    }
}
