<h1>My Chats</h1>
<?php echo isset($this) ? $this->create('messages.html.php', $this->data) : ''; ?>

<!-- TODO chats here -->

<a href="?q=logout">Logout</a>
<a href="?q=createchat">Create new chat</a>