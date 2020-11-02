<?php declare(strict_types = 1);

namespace GyMadarasz\ChatBot\Test;

use GyMadarasz\Test\AppTest;
use GyMadarasz\Test\Tester;

class ChatBotTest extends AppTest
{
    protected function cleanupChats(): void
    {
        $this->logger->test('I am going to delete all chats from database.');

        $this->mysql->query("TRUNCATE chat");
    }

    protected function cleanup(): void
    {
        parent::cleanup();
        $this->cleanupChats();
    }

    public function test(): void
    {
        $this->cleanup();

        $this->logger->test('I am going to register a tester user.');
        $this->registryTesterUser();

        $this->logger->test('I am going to check if I can login.');
        $contents = $this->checkIfICanLogin();
        $this->checkIfICanSeeMyChatsPage($contents);

        $this->logger->test('I am going to check if Chat CRUD works.');

        $this->checkChatsCrud();
    }

    protected function registryTesterUser(): void
    {
        $this->logger->test('I am going to create a tester user for testing.');

        $contents = $this->tester->post('?q=registry', [
            'email' => AppTest::USER_EMAIL,
            'email_retype' => AppTest::USER_EMAIL,
            'password' => AppTest::USER_PASSWORD,
        ]);
        $this->checkLoginPage($contents);
        $this->checkPageContainsMessage($contents, 'Registration success, please check your email inbox and validate your account, or try to resend by <a href="?q=resend">click here</a>');


        $this->logger->test('I am going to check my activation email.');

        $email = $this->checkMail('Activate your account');
        $token = $this->checkRegistrationEmail($email);
        $this->cleanupMails();

        $this->logger->test('I am going to activate my account with the correct activation token.');

        $contents = $this->tester->get($this->config->get('baseUrl') . '?q=activate&token=' . $token);
        $this->checkLoginPage($contents);
        $this->checkPageContainsMessage($contents, 'Your account is now activated.');
    }

    protected function checkChatsCrud(): void
    {
        $this->logger->test('I am going to create a chatbot.');

        $contents = $this->tester->get('?q=createchat');
        $this->checkIfICanSeeCreateChatPage($contents);


        $this->logger->test('I am going to post a new chat, but without name. It should fails.');

        $contents = $this->tester->post('?q=createchat', [
            'name' => '',
        ]);
        $this->checkPageContainsError($contents, 'Please set Chat Name');
        $this->checkIfICanSeeCreateChatPage($contents);


        $this->logger->test('I am going to post a new chat.');

        $contents = $this->tester->post('?q=createchat', [
            'name' => 'Test Chat',
        ]);
        $this->checkPageContainsMessage($contents, 'Chat created');
        $form = $this->checkIfICanSeeEditChatPage($contents);
        $id = (int)$form['id'];
        $this->tester->assertNotEquals(0, $id);
        $this->tester->assertEquals('Test Chat', $form['name']);
        

        $this->logger->test('I am going to post a new chat name.');
        $contents = $this->tester->post('?q=editchat', [
            'id' => $form['id'],
            'name' => 'Test Chat Renamed',
        ]);
        $this->checkPageContainsMessage($contents, 'Chat is modified');
        $form = $this->checkIfICanSeeEditChatPage($contents);
        $this->tester->assertEquals($id, (int)$form['id']);
        $this->tester->assertEquals('Test Chat Renamed', $form['name']);


        $this->logger->test('I am going to try chat modification link.');

        $contents = $this->tester->get('?q=editchat&id=' . $id);
        $form = $this->checkIfICanSeeEditChatPage($contents);
        $this->tester->assertEquals($id, (int)$form['id']);
        $this->tester->assertEquals('Test Chat Renamed', $form['name']);


        $this->logger->test('I am going to check if I see my chat in My Chats list.');

        $contents = $this->tester->get('?q=mychats');
        $this->checkIfICanSeeMyChatsPage($contents, [
            ['id' => (string)$id, 'name' => 'Test Chat Renamed'],
        ]);

        $this->logger->test('I am going to logout and then log in to see I still can see my chats');
        $contents = $this->tester->get('?q=logout');
        $this->checkLoginPage($contents);
        $this->checkPageContainsMessage($contents, 'Logout success');
        $this->logger->test('I am going login.');
        $contents = $this->checkIfICanLogin();
        $this->checkIfICanSeeMyChatsPage($contents, [
            ['id' => (string)$id, 'name' => 'Test Chat Renamed'],
        ]);


        $this->logger->test('I am going to try to delete my a non-exists chat');
        $contents = $this->tester->get('?q=deletechat&id=12345678987654321');
        $this->checkIfICanSeeMyChatsPage($contents);
        $this->checkPageContainsError($contents, 'Chat is not deleted');

        $this->logger->test('I am going to try to delete my chat');
        $contents = $this->tester->get('?q=deletechat&id=' . $id);
        $this->checkIfICanSeeMyChatsPage($contents);
        $this->checkPageContainsMessage($contents, 'Chat is deleted');
        $this->tester->assertNotContains('Test Chat Renamed', $contents);

        $this->logger->test('I am going to try to delete my chat again');
        $contents = $this->tester->get('?q=deletechat&id=' . $id);
        $this->checkIfICanSeeMyChatsPage($contents);
        $this->checkPageContainsError($contents, 'Chat is not deleted');
        $this->tester->assertNotContains('Test Chat Renamed', $contents);
    }

    /** @param array<array<string>> $chats */
    protected function checkIfICanSeeMyChatsPage(string $contents, array $chats = []): void
    {
        $this->logger->test('I am going to check that I can see the My Chats page properly.');

        $this->tester->assertContains('<h1>My Chats</h1>', $contents);

        foreach ($chats as $chat) {
            $this->logger->test('I am going to check if I see the chat (ID:' . $chat['id']. ', name:"' . $chat['name'] . '")');
            $this->tester->assertContains('<a href="?q=editchat&id=' . $chat['id'] . '">' . $chat['name'] . '</a>', $contents);
            $this->tester->assertContains('<a href="?q=deletechat&id=' . $chat['id'] . '">Delete</a>', $contents);
        }

        $this->tester->assertContains('<a href="?q=logout">Logout</a>', $contents);
        $this->tester->assertContains('<a href="?q=createchat">Create new chat</a>', $contents);
    }

    protected function checkIfICanSeeCreateChatPage(string $contents): void
    {
        $this->logger->test('I am going to check that I can see the Create Chat page properly.');

        $this->tester->assertContains('<h1>Create Chat</h1>', $contents);
        $this->tester->assertContains('<form method="POST" action="?q=createchat"', $contents);
        $this->tester->assertContains('<input type="text" name="name"', $contents);
        $this->tester->assertContains('<input type="submit" value="Create"', $contents);

        $this->tester->assertContains('<a href="?q=logout">Logout</a>', $contents);
        $this->tester->assertContains('<a href="?q=mychats">My Chats</a>', $contents);
    }

    /**
     * @return array<mixed>
     */
    protected function checkIfICanSeeEditChatPage(string $contents): array
    {
        $this->logger->test('I am going to check that I can see the Edit Chat page properly.');

        $this->tester->assertContains('<h1>Edit Chat</h1>', $contents);
        $this->tester->assertContains('<form method="POST" action="?q=editchat"', $contents);
        $this->tester->assertContains('<input type="hidden" name="id"', $contents);
        $this->tester->assertContains('<input type="text" name="name"', $contents);
        $this->tester->assertContains('<input type="submit" value="Save"', $contents);

        $this->tester->assertContains('<a href="?q=logout">Logout</a>', $contents);
        $this->tester->assertContains('<a href="?q=mychats">My Chats</a>', $contents);
        $this->tester->assertContains('<a href="?q=createchat">Create new chat</a>', $contents);

        $this->tester->assertTrue((bool)preg_match('/<input type="hidden" name="id" value="([0-9]+)">/', $contents, $matches));
        $this->tester->assertTrue(!empty($matches));
        $this->tester->assertTrue(!empty($matches[1]));
        $id = $matches[1] ?? null;
        
        $this->tester->assertTrue((bool)preg_match('/<input type="text" name="name" value="(.+)" placeholder="Chat name">/', $contents, $matches));
        $this->tester->assertTrue(!empty($matches));
        $this->tester->assertTrue(!empty($matches[1]));
        $name = $matches[1] ?? null;
        
        return [
            'id' => $id,
            'name' => $name,
        ];
    }
}
