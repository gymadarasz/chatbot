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
use GyMadarasz\Test\AppTest;
use GyMadarasz\Test\Tester;
use GyMadarasz\WebApp\Service\Config;
use GyMadarasz\WebApp\Service\Logger;
use GyMadarasz\WebApp\Service\Mysql;

/**
 * ChatBotTest
 *
 * @category  PHP
 * @package   GyMadarasz\ChatBot\Test
 * @author    Gyula Madarasz <gyula.madarasz@gmail.com>
 * @copyright 2020 Gyula Madarasz
 * @license   Copyright (c) all right reserved.
 * @link      this
 */
class ChatBotTest
{
    protected Tester $tester;
    protected Config $config;
    protected Logger $logger;
    protected Mysql $mysql;
    protected AppTest $appTest;
    protected AppChecker $appChecker;
    
    /**
     * Method __construct
     *
     * @param Tester     $tester     tester
     * @param Config     $config     config
     * @param Logger     $logger     logger
     * @param Mysql      $mysql      mysql
     * @param AppTest    $appTest    appTest
     * @param AppChecker $appChecker appChecker
     *
     * @return void
     */
    public function __construct(
        Tester $tester,
        Config $config,
        Logger $logger,
        Mysql $mysql,
        AppTest $appTest,
        AppChecker $appChecker
    ) {
        $this->tester = $tester;
        $this->config = $config;
        $this->logger = $logger;
        $this->mysql = $mysql;
        $this->appTest = $appTest;
        $this->appChecker = $appChecker;
        
        $this->tester->setAssertor($this->tester->getAssertor());
        $this->appTest->setTester($this->tester);
        $this->appTest->setAppCheckker($this->appChecker);
        $this->appChecker->setAppTest($this->appTest);
    }

    /**
     * Method cleanupChats
     *
     * @return void
     */
    protected function cleanupChats(): void
    {
        $this->logger->test('I am going to delete all chats from database.');

        $this->mysql->query("TRUNCATE chat");
    }
    

    /**
     * Method cleanupUsers
     *
     * @return void
     */
    protected function cleanupUsers(): void
    {
        $this->logger->test('I am going to delete all users from database.');

        $this->mysql->query(
            "DELETE FROM user WHERE email = '" . AppTest::USER_EMAIL . "';"
        );
    }

    /**
     * Method cleanupMails
     *
     * @return void
     */
    public function cleanupMails(): void
    {
        $this->logger->test('I am going to delete all saved emails.');

        $files = (array)glob($this->config->get('mailerSaveMailsPath') . '/*.*');
        foreach ($files as $file) {
            unlink((string)$file);
        }
    }

    /**
     * Method cleanup
     *
     * @return void
     */
    protected function cleanup(): void
    {
        $this->cleanupUsers();
        $this->cleanupMails();
        $this->cleanupChats();
    }
    
    /**
     * Method test
     *
     * @return void
     */
    public function test():void
    {
        $this->cleanup();

        $this->logger->test('I am going to register a tester user.');
        $this->registryTesterUser();

        $this->logger->test('I am going to check if I can login.');
        $contents = $this->appTest->checkIfICanLogin();
        $this->checkIfICanSeeMyChatsPage($contents);

        $this->logger->test('I am going to check if Chat CRUD works.');

        $this->checkChatsCrud();
    }

    /**
     * Method registryTesterUser
     *
     * @return void
     */
    protected function registryTesterUser(): void
    {
        $this->logger->test('I am going to create a tester user for testing.');

        $contents = $this->tester->post(
            '?q=registry',
            [
            'email' => AppTest::USER_EMAIL,
            'email_retype' => AppTest::USER_EMAIL,
            'password' => AppTest::USER_PASSWORD,
            ]
        );
        $this->appChecker->checkLoginPage($contents);
        $this->appChecker->checkPageContainsMessage(
            $contents,
            'Registration success, '
                . 'please check your email inbox and validate your account, '
                . 'or try to resend by <a href="?q=resend">click here</a>'
        );


        $this->logger->test('I am going to check my activation email.');

        $email = $this->appTest->checkMail('Activate your account');
        $token = $this->appTest->checkRegistrationEmail($email);
        $this->cleanupMails();

        $this->logger->test(
            'I am going to activate my account with the correct activation token.'
        );

        $contents = $this->tester->get(
            $this->config->get('baseUrl') . '?q=activate&token=' . $token
        );
        $this->appChecker->checkLoginPage($contents);
        $this->appChecker->checkPageContainsMessage(
            $contents,
            'Your account is now activated.'
        );
    }

    /**
     * Method checkChatsCrud
     *
     * @return void
     */
    protected function checkChatsCrud(): void
    {
        $this->logger->test('I am going to create a chatbot.');

        $contents = $this->tester->get('?q=createchat');
        $this->checkIfICanSeeCreateChatPage($contents);


        $this->logger->test(
            'I am going to post a new chat, but without name. It should fails.'
        );

        $contents = $this->tester->post(
            '?q=createchat',
            [
            'name' => '',
            ]
        );
        $this->appChecker->checkPageContainsError($contents, 'Please set Chat Name');
        $this->checkIfICanSeeCreateChatPage($contents);


        $this->logger->test('I am going to post a new chat.');

        $contents = $this->tester->post(
            '?q=createchat',
            [
            'name' => 'Test Chat',
            ]
        );
        $this->appChecker->checkPageContainsMessage($contents, 'Chat created');
        $form = $this->checkIfICanSeeEditChatPage($contents);
        $cid = (int)$form['id'];
        $this->tester->getAssertor()->assertNotEquals(0, $cid);
        $this->tester->getAssertor()->assertEquals('Test Chat', $form['name']);
        

        $this->logger->test('I am going to post a new chat name.');
        $contents = $this->tester->post(
            '?q=editchat',
            [
            'id' => $form['id'],
            'name' => 'Test Chat Renamed',
            ]
        );
        $this->appChecker->checkPageContainsMessage($contents, 'Chat is modified');
        $form = $this->checkIfICanSeeEditChatPage($contents);
        $this->tester->getAssertor()->assertEquals($cid, (int)$form['id']);
        $this->tester->getAssertor()->assertEquals(
            'Test Chat Renamed',
            $form['name']
        );


        $this->logger->test('I am going to try chat modification link.');

        $contents = $this->tester->get('?q=editchat&id=' . $cid);
        $form = $this->checkIfICanSeeEditChatPage($contents);
        $this->tester->getAssertor()->assertEquals($cid, (int)$form['id']);
        $this->tester->getAssertor()->assertEquals(
            'Test Chat Renamed',
            $form['name']
        );


        $this->logger->test(
            'I am going to check if I see my chat in My Chats list.'
        );

        $contents = $this->tester->get('?q=mychats');
        $this->checkIfICanSeeMyChatsPage(
            $contents,
            [
            ['id' => (string)$cid, 'name' => 'Test Chat Renamed'],
            ]
        );

        $this->logger->test(
            'I am going to logout and then log in to see I still can see my chats'
        );
        $contents = $this->tester->get('?q=logout');
        $this->appChecker->checkLoginPage($contents);
        $this->appChecker->checkPageContainsMessage($contents, 'Logout success');
        $this->logger->test('I am going login.');
        $contents = $this->appTest->checkIfICanLogin();
        $this->checkIfICanSeeMyChatsPage(
            $contents,
            [
            ['id' => (string)$cid, 'name' => 'Test Chat Renamed'],
            ]
        );

        $this->checkChatsCrudDelete($cid);
    }
    
    /**
     * Method checkChatsCrudDelete
     *
     * @param int $cid id
     *
     * @return void
     */
    protected function checkChatsCrudDelete(int $cid): void
    {
        $this->logger->test('I am going to try to delete my a non-exists chat');
        $contents = $this->tester->get('?q=deletechat&id=12345678987654321');
        $this->checkIfICanSeeMyChatsPage($contents);
        $this->appChecker->checkPageContainsError($contents, 'Chat is not deleted');

        $this->logger->test('I am going to try to delete my chat');
        $contents = $this->tester->get('?q=deletechat&id=' . $cid);
        $this->checkIfICanSeeMyChatsPage($contents);
        $this->appChecker->checkPageContainsMessage($contents, 'Chat is deleted');
        $this->tester->getAssertor()->assertNotContains(
            'Test Chat Renamed',
            $contents
        );

        $this->logger->test('I am going to try to delete my chat again');
        $contents = $this->tester->get('?q=deletechat&id=' . $cid);
        $this->checkIfICanSeeMyChatsPage($contents);
        $this->appChecker->checkPageContainsError($contents, 'Chat is not deleted');
        $this->tester->getAssertor()->assertNotContains(
            'Test Chat Renamed',
            $contents
        );
    }

    /**
     * Method checkIfICanSeeMyChatsPage
     *
     * @param string     $contents contents
     * @param string[][] $chats    chats
     *
     * @return void
     */
    protected function checkIfICanSeeMyChatsPage(
        string $contents,
        array $chats = []
    ): void {
        $this->logger->test(
            'I am going to check that I can see the My Chats page properly.'
        );

        $this->tester->getAssertor()->assertContains('<h1>My Chats</h1>', $contents);

        foreach ($chats as $chat) {
            $this->logger->test(
                'I am going to check if I see the chat (ID:' .
                    $chat['id']. ', name:"' . $chat['name'] . '")'
            );
            $this->tester->getAssertor()->assertContains(
                '<a href="?q=editchat&id='
                    . $chat['id'] . '">' . $chat['name'] . '</a>',
                $contents
            );
            $this->tester->getAssertor()->assertContains(
                '<a href="?q=deletechat&id=' . $chat['id'] . '">Delete</a>',
                $contents
            );
        }

        $this->tester->getAssertor()->assertContains(
            '<a href="?q=logout">Logout</a>',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            '<a href="?q=createchat">Create new chat</a>',
            $contents
        );
    }

    /**
     * Method checkIfICanSeeCreateChatPage
     *
     * @param string $contents contents
     *
     * @return void
     */
    protected function checkIfICanSeeCreateChatPage(string $contents): void
    {
        $this->logger->test(
            'I am going to check that I can see the Create Chat page properly.'
        );

        $this->tester->getAssertor()->assertContains(
            '<h1>Create Chat</h1>',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            '<form method="POST" action="?q=createchat"',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            '<input type="text" name="name"',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            '<input type="submit" value="Create"',
            $contents
        );

        $this->tester->getAssertor()->assertContains(
            '<a href="?q=logout">Logout</a>',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            '<a href="?q=mychats">My Chats</a>',
            $contents
        );
    }

    /**
     * Method checkIfICanSeeEditChatPage
     *
     * @param string $contents contents
     *
     * @return mixed[]
     */
    protected function checkIfICanSeeEditChatPage(string $contents): array
    {
        $this->logger->test(
            'I am going to check that I can see the Edit Chat page properly.'
        );

        $this->tester->getAssertor()->assertContains(
            '<h1>Edit Chat</h1>',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            '<form method="POST" action="?q=editchat"',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            '<input type="hidden" name="id"',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            '<input type="text" name="name"',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            '<input type="submit" value="Save"',
            $contents
        );

        $this->tester->getAssertor()->assertContains(
            '<a href="?q=logout">Logout</a>',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            '<a href="?q=mychats">My Chats</a>',
            $contents
        );
        $this->tester->getAssertor()->assertContains(
            '<a href="?q=createchat">Create new chat</a>',
            $contents
        );

        $matches = [];
        $this->tester->getAssertor()->assertTrue(
            (bool)preg_match(
                '/<input type="hidden" name="id" value="([0-9]+)">/',
                $contents,
                $matches
            )
        );
        $this->tester->getAssertor()->assertTrue(!empty($matches));
        $this->tester->getAssertor()->assertTrue(!empty($matches[1]));
        $cid = $matches[1] ?? null;
        
        $this->tester->getAssertor()->assertTrue(
            (bool)preg_match(
                '/<input type="text" name="name" value="(.+)" '
                    . 'placeholder="Chat name">/',
                $contents,
                $matches
            )
        );
        $this->tester->getAssertor()->assertTrue(!empty($matches));
        $this->tester->getAssertor()->assertTrue(!empty($matches[1]));
        $name = $matches[1] ?? null;
        
        return [
            'id' => $cid,
            'name' => $name,
        ];
    }
}
