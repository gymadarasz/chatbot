<h1>Edit Chat</h1>
<?php echo isset($this) ? $this->create('messages.html.php', $this->data) : ''; ?>
<form method="POST" action="?q=editchat">
    <input type="hidden" name="id" value="<?php echo $id ?? '' ?>">
    <input type="text" name="name" value="<?php echo $name ?? '' ?>" placeholder="Chat name">
    <input type="submit" value="Save">
</form>
[TODO chat editor here]
<a href="?q=logout">Logout</a>
<a href="?q=mychats">My Chats</a>
<a href="?q=createchat">Create new chat</a>