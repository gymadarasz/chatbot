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

    public function run(Tester $tester): void
    {
        $this->cleanup();

        $this->logger->test('I am going to register a tester user.');
        $this->registryTesterUser($tester);

        $this->logger->test('I am going to check if I can login.');
        $contents = $this->checkIfICanLogin($tester);
        $this->checkIfICanSeeMyChatsPage($tester, $contents);

        $this->logger->test('I am going to check if Chat CRUD works.');

        $this->checkChatsCrud($tester);
    }

    protected function registryTesterUser(Tester $tester): void
    {
        $this->logger->test('I am going to create a tester user for testing.');

        $contents = $tester->post('?q=registry', [
            'email' => AppTest::USER_EMAIL,
            'email_retype' => AppTest::USER_EMAIL,
            'password' => AppTest::USER_PASSWORD,
        ]);
        $this->checkLoginPage($tester, $contents);
        $this->checkPageContainsMessage($tester, $contents, 'Registration success, please check your email inbox and validate your account, or try to resend by <a href="?q=resend">click here</a>');


        $this->logger->test('I am going to check my activation email.');

        $email = $this->checkMail($tester, 'Activate your account');
        $token = $this->checkRegistrationEmail($tester, $email);
        $this->cleanupMails();

        $this->logger->test('I am going to activate my account with the correct activation token.');

        $contents = $tester->get($this->config->get('baseUrl') . '?q=activate&token=' . $token);
        $this->checkLoginPage($tester, $contents);
        $this->checkPageContainsMessage($tester, $contents, 'Your account is now activated.');
    }

    protected function checkChatsCrud(Tester $tester): void
    {
        $this->logger->test('I am going to create a chatbot.');

        $contents = $tester->get('?q=createchat');
        $this->checkIfICanSeeCreateChatPage($tester, $contents);


        $this->logger->test('I am going to post a new chat, but without name. It should fails.');

        $contents = $tester->post('?q=createchat', [
            'name' => '',
        ]);
        $this->checkPageContainsError($tester, $contents, 'Please set Chat Name');
        $this->checkIfICanSeeCreateChatPage($tester, $contents);


        $this->logger->test('I am going to post a new chat.');

        $contents = $tester->post('?q=createchat', [
            'name' => 'Test Chat',
        ]);
        $this->checkPageContainsMessage($tester, $contents, 'Chat created');
        $form = $this->checkIfICanSeeEditChatPage($tester, $contents);
        $id = (int)$form['id'];
        $tester->assertNotEquals(0, $id);
        $tester->assertEquals('Test Chat', $form['name']);
        

        $this->logger->test('I am going to post a new chat name.');
        $contents = $tester->post('?q=editchat', [
            'id' => $form['id'],
            'name' => 'Test Chat Renamed',
        ]);
        $this->checkPageContainsMessage($tester, $contents, 'Chat is modified');
        $form = $this->checkIfICanSeeEditChatPage($tester, $contents);
        $tester->assertEquals($id, (int)$form['id']);
        $tester->assertEquals('Test Chat Renamed', $form['name']);


        $this->logger->test('I am going to try chat modification link.');

        $contents = $tester->get('?q=editchat&id=' . $id);
        $form = $this->checkIfICanSeeEditChatPage($tester, $contents);
        $tester->assertEquals($id, (int)$form['id']);
        $tester->assertEquals('Test Chat Renamed', $form['name']);


        $this->logger->test('I am going to check if I see my chat in My Chats list.');

        $contents = $tester->get('?q=mychats');
        $this->checkIfICanSeeMyChatsPage($tester, $contents, [
            ['id' => (string)$id, 'name' => 'Test Chat Renamed'],
        ]);

        $this->logger->test('I am going to logout and then log in to see I still can see my chats');
        $contents = $tester->get('?q=logout');
        $this->checkLoginPage($tester, $contents);
        $this->checkPageContainsMessage($tester, $contents, 'Logout success');
        $this->logger->test('I am going login.');
        $contents = $this->checkIfICanLogin($tester);
        $this->checkIfICanSeeMyChatsPage($tester, $contents, [
            ['id' => (string)$id, 'name' => 'Test Chat Renamed'],
        ]);


        $this->logger->test('I am going to try to delete my a non-exists chat');
        $contents = $tester->get('?q=deletechat&id=12345678987654321');
        $this->checkIfICanSeeMyChatsPage($tester, $contents);
        $this->checkPageContainsError($tester, $contents, 'Chat is not deleted');

        $this->logger->test('I am going to try to delete my chat');
        $contents = $tester->get('?q=deletechat&id=' . $id);
        $this->checkIfICanSeeMyChatsPage($tester, $contents);
        $this->checkPageContainsMessage($tester, $contents, 'Chat is deleted');
        $tester->assertNotContains('Test Chat Renamed', $contents);

        $this->logger->test('I am going to try to delete my chat again');
        $contents = $tester->get('?q=deletechat&id=' . $id);
        $this->checkIfICanSeeMyChatsPage($tester, $contents);
        $this->checkPageContainsError($tester, $contents, 'Chat is not deleted');
        $tester->assertNotContains('Test Chat Renamed', $contents);
    }

    /** @param array<array<string>> $chats */
    protected function checkIfICanSeeMyChatsPage(Tester $tester, string $contents, array $chats = []): void
    {
        $this->logger->test('I am going to check that I can see the My Chats page properly.');

        $tester->assertContains('<h1>My Chats</h1>', $contents);

        foreach ($chats as $chat) {
            $this->logger->test('I am going to check if I see the chat (ID:' . $chat['id']. ', name:"' . $chat['name'] . '")');
            $tester->assertContains('<a href="?q=editchat&id=' . $chat['id'] . '">' . $chat['name'] . '</a>', $contents);
            $tester->assertContains('<a href="?q=deletechat&id=' . $chat['id'] . '">Delete</a>', $contents);
        }

        $tester->assertContains('<a href="?q=logout">Logout</a>', $contents);
        $tester->assertContains('<a href="?q=createchat">Create new chat</a>', $contents);
    }

    protected function checkIfICanSeeCreateChatPage(Tester $tester, string $contents): void
    {
        $this->logger->test('I am going to check that I can see the Create Chat page properly.');

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
        $this->logger->test('I am going to check that I can see the Edit Chat page properly.');

        $tester->assertContains('<h1>Edit Chat</h1>', $contents);
        $tester->assertContains('<form method="POST" action="?q=editchat"', $contents);
        $tester->assertContains('<input type="hidden" name="id"', $contents);
        $tester->assertContains('<input type="text" name="name"', $contents);
        $tester->assertContains('<input type="submit" value="Save"', $contents);

        $tester->assertContains('<a href="?q=logout">Logout</a>', $contents);
        $tester->assertContains('<a href="?q=mychats">My Chats</a>', $contents);
        $tester->assertContains('<a href="?q=createchat">Create new chat</a>', $contents);

        $tester->assertTrue((bool)preg_match('/<input type="hidden" name="id" value="([0-9]+)">/', $contents, $matches));
        $tester->assertTrue(!empty($matches));
        $tester->assertTrue(!empty($matches[1]));
        $id = $matches[1] ?? null;
        
        $tester->assertTrue((bool)preg_match('/<input type="text" name="name" value="(.+)" placeholder="Chat name">/', $contents, $matches));
        $tester->assertTrue(!empty($matches));
        $tester->assertTrue(!empty($matches[1]));
        $name = $matches[1] ?? null;
        
        return [
            'id' => $id,
            'name' => $name,
        ];
    }
}
