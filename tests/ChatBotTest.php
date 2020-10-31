<?php declare(strict_types = 1);

namespace GyMadarasz\ChatBot\Test;

use GyMadarasz\Test\AppTest;
use GyMadarasz\Test\Tester;

class ChatBotTest extends AppTest
{
    protected function cleanupChats(): void
    {
        $this->logger->debug('I am going to delete all chats from database.');

        // TODO ...
        // $this->mysql->connect();
        // $this->mysql->query("TRUNCATE chatbot");
    }

    protected function cleanup(): void
    {
        $this->cleanupChats();
    }

    public function run(Tester $tester): void
    {
        $this->cleanup();

        $this->logger->debug('I am goint to check if I can login.');

        $contents = $this->checkIfICanLogin($tester);
        $this->checkIfICanSeeMyChatsPage($tester, $contents);

        // $this->logger->debug('I am going to check if Chat CRUD works.');

        // $this->checkChatsCrud($tester);
    }

    protected function checkChatsCrud(Tester $tester): void
    {
        $this->logger->debug('I am going to create a chatbot.');

        $contents = $tester->get('?q=createchat');
        $this->checkIfICanSeeCreateChatPage($tester, $contents);


        $this->logger->debug('I am going to post a new chat, but without name. It should fails.');

        $contents = $tester->post('?q=createchat', [
            'name' => '',
        ]);
        $this->checkPageContainsError($tester, $contents, 'Please set Chat name');
        $this->checkIfICanSeeCreateChatPage($tester, $contents);


        $this->logger->debug('I am going to post a new chat.');

        $contents = $tester->post('?q=createchat', [
            'name' => 'Test Chat',
        ]);
        $this->checkPageContainsMessage($tester, $contents, 'Chat created');
        $form = $this->checkIfICanSeeEditChatPage($tester, $contents);
        $id = (int)$form['id'];
        $tester->assertNotEquals(0, $id);
        $tester->assertEquals('Test Chat', $form['name']);
        

        $this->logger->debug('I am going to post a new chat name.');

        $contents = $tester->post('?q=editchat', [
            'id' => $form['id'],
            'name' => 'Test Chat Renamed',
        ]);
        $this->checkPageContainsMessage($tester, $contents, 'Chat modified');
        $form = $this->checkIfICanSeeEditChatPage($tester, $contents);
        $tester->assertEquals($id, (int)$form['id']);
        $tester->assertEquals('Test Chat Renamed', $form['name']);
    }

    protected function checkIfICanSeeMyChatsPage(Tester $tester, string $contents): void
    {
        $this->logger->debug('I am check that I can see the My Chats page properly.');

        $tester->assertContains('<h1>My Chats</h1>', $contents);

        $tester->assertContains('<a href="?q=logout">Logout</a>', $contents);
        $tester->assertContains('<a href="?q=createchat">Create new chat</a>', $contents);
    }

    protected function checkIfICanSeeCreateChatPage(Tester $tester, string $contents): void
    {
        $this->logger->debug('I am check that I can see the Chreate Chat page properly.');

        $tester->assertContains('<h1>Create Chat</h1>', $contents);
        $tester->assertContains('<form method="POST" action="?q=createchat"', $contents);
        $tester->assertContains('<input type="text" name="name"', $contents);
        $tester->assertContains('<input type="submit" value="Create"', $contents);

        $tester->assertContains('<a href="?q=logout">Logout</a>', $contents);
        $tester->assertContains('<a href="?q=mychats">My Chats</a>', $contents);
    }

    /**
     * @return array<mixed>
     */
    protected function checkIfICanSeeEditChatPage(Tester $tester, string $contents): array
    {
        $this->logger->debug('I am check that I can see the Edit Chat page properly.');

        $tester->assertContains('<h1>Edit Chat</h1>', $contents);
        $tester->assertContains('<form method="POST" action="?q=editchat"', $contents);
        $tester->assertContains('<input type="hidden" name="id"', $contents);
        $tester->assertContains('<input type="text" name="name"', $contents);
        $tester->assertContains('<input type="submit" value="Save"', $contents);

        $tester->assertContains('<a href="?q=logout">Logout</a>', $contents);
        $tester->assertContains('<a href="?q=createchat">Create new chat</a>', $contents);

        $tester->assertTrue((bool)preg_match('/<input type="hidden" name="id" value="([0-9]+)"/', $contents, $matches));
        $tester->assertTrue(!empty($matches));
        $tester->assertTrue(!empty($matches[1]));
        $id = $matches[1] ?? null;
        
        $tester->assertTrue((bool)preg_match('/<input type="text" name="name" value="(.+)"/', $contents, $matches));
        $tester->assertTrue(!empty($matches));
        $tester->assertTrue(!empty($matches[1]));
        $name = $matches[1] ?? null;
        
        return [
            'id' => $id,
            'name' => $name,
        ];
    }
}
