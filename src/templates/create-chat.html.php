<h1>Create Chat</h1>
<?php echo isset($this) ? $this->create('messages.html.php', $this->data) : ''; ?>
<form method="POST" action="?q=createchat">
    <input type="text" name="name" placeholder="Chat name" value="<?php echo $name ?? ''?>">
    <input type="submit" value="Create">
</form>
<a href="?q=logout">Logout</a>
<a href="?q=mychats">My Chats</a>