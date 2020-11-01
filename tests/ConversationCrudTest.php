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
        $chatId = (int)$this->getInputFieldValue('hidden', 'message[chat_id]', $contents, 0);
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

        $token = (int)$this->getInputFieldValue('hidden', 'token', $contents, 0);

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

        $token = (int)$this->getInputFieldValue('hidden', 'token', $contents, 0);
        $requestMessageId = $this->getInputFieldValue('hidden', 'message_to_message[request_message_id]', $contents, 0);

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

        $token = (int)$this->getInputFieldValue('hidden', 'token', $contents, 0);

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

        $token = (int)$this->getInputFieldValue('hidden', 'token', $contents, 0);

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

        $token = (int)$this->getInputFieldValue('hidden', 'token', $contents, 0);

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
        $token = (int)$this->getInputFieldValue('hidden', 'token', $contents, 0);
        $chatIds = $this->getInputFieldValue('hidden', 'message[chat_id]', $contents);
        $tester->assertEquals(6, count($chatIds), 'Should have 6 form on the screen with message[chat_id] filed in it.');
        foreach ($chatIds as $cid) {
            $tester->assertEquals($chatId, (int)$cid, 'Each form on the screen should works with the same chat ID.');
        }
        $requestMessageIds = $this->getInputFieldValue('hidden', 'message_to_message[request_message_id]', $contents);
        $tester->assertEquals(5, count($requestMessageIds), 'So far, should have 5 form on screen coperating with message_to_message relations.');
        $responseMessageIds = $this->getSelectFieldValue('message_to_message[response_message_id]', $contents);
        $tester->assertEquals(2, count($responseMessageIds), 'Should have 2 form which is modifies the response_message_id in a message_to_message relation.');
    }

    /**
     * @return mixed
     */
    private function getInputFieldValue(string $type, string $name, string $contents, int $at = -1)
    {
        if (!preg_match_all('/<input\s+type\s*=\s*"' . $type . '"\s*name\s*=\s*"' . preg_quote($name) . '"\s*value=\s*"([^"]*)"/', $contents, $matches)) {
            throw new RuntimeException('Input element not found:  <input type="' . $type . '" name="' . $name . '" value=...>');
        }
        if (!isset($matches[1]) || !isset($matches[1][0])) {
            throw new RuntimeException('Input element does not have a value: <input type="' . $type . '" name="' . $name . '" value=...>');
        }
        if ($at > -1) {
            if (!isset($matches[1][$at])) {
                throw new RuntimeException('Input element does not have a value at ' . $at . '-nth: <input type="' . $type . '" name="' . $name . '" value=...>');
            }
            return $matches[1][$at];
        } elseif ($at == -1) {
            return $matches[1];
        }
        throw new RuntimeException('Illegal selection: ' . $at);
    }

    /**
     * @return mixed
     */
    private function getSelectFieldValue(string $name, string $contents, int $at = -1)
    {
        $selects = $this->getSelectFieldContents($name, $contents, $at);
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
        if ($at > -1) {
            if (!isset($values[1][$at])) {
                throw new RuntimeException('Select element does not have a value at ' . $at . '-nth: <select name="' . $name . '"...</select>');
            }
            return $values[$at];
        } elseif ($at == -1) {
            return $values;
        }
        throw new RuntimeException('Illegal selection: ' . $at);
    }

    private function getOptionFieldValue(string $option): string
    {
        if (!preg_match('/<option\s.*\svalue\s*=\s*"([^"]*)"/', $option, $matches) || isset($matches[1])) {
            throw new RuntimeException('Unrecognised option value');
        }
        return $matches[1];
    }

    private function isOptionSelected(string $option): bool
    {
        if (!preg_match('/<option\s[^>]*\s(selected)[\s=]/', $option, $matches)) {
            throw new RuntimeException('Unrecognised option');
        }
        return isset($matches[1]) && $matches[1] === 'selected';
    }

    private function isMultiSelectField(string $select): bool
    {
        if (!preg_match('/<select .*(multiple)/', $select, $matches)) {
            throw new RuntimeException('Unrecognised select');
        }
        return isset($matches[1]) && $matches[1] === 'multiple';
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
     * @return mixed
     */
    private function getSelectFieldContents(string $name, string $contents, int $at = -1)
    {
        if (!preg_match_all('/<select\s+name\s*=\s*"' . preg_quote($name) . '"(.+?)<\/select>/s', $contents, $matches)) {
            throw new RuntimeException('Select element not found: <select name="' . $name . '"...</select>');
        }
        if (!isset($matches[1]) || !isset($matches[1][0])) {
            throw new RuntimeException('Select element does not have a value: <select name="' . $name . '"...</select>');
        }
        if ($at > -1) {
            if (!isset($matches[1][$at])) {
                throw new RuntimeException('Select element does not have a value at ' . $at . '-nth: <select name="' . $name . '"...</select>');
            }
            return $matches[0][$at];
        } elseif ($at == -1) {
            return $matches[0];
        }
        throw new RuntimeException('Illegal selection: ' . $at);
    }
}
