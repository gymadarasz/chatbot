<h1>My Chats</h1>
<?php echo isset($this) ? $this->create('messages.html.php', $this->data) : ''; ?>

<ul>
    <?php foreach ($chats ?? [] as $chat) { ?>
    <li>
        <a href="?q=editchat&id=<?php echo $chat['id'] ?? '' ?>"><?php echo $chat['name'] ?></a>
        <a href="?q=chat&id=<?php echo $chat['id'] ?? '' ?>">Go Chat</a>
        <a href="?q=deletechat&id=<?php echo $chat['id'] ?? '' ?>">Delete</a>
    </li>
    <?php } ?>
</ul>

<a href="?q=logout">Logout</a>
<a href="?q=createchat">Create new chat</a>