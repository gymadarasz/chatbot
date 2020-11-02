<?php declare(strict_types = 1);

namespace GyMadarasz\ChatBot\Test;

use GyMadarasz\Test\AppTest;

class ConversationTest extends AppTest
{
    public function test(): void
    {
        $this->logger->test('I am going to create a test conversation.');
        $this->createTestConversation();
    }

    protected function createTestConversation(): void
    {
        $contents = $this->tester->post('?q=createchat', [
            'name' => 'Test Conversation',
        ]);
    }
}
