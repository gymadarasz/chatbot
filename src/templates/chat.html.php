<h1>My Chats</h1>
<?php echo isset($this) ? $this->create('messages.html.php', $this->data) : ''; ?>

<ul>
    <?php foreach ($chatList ?? [] as $chat) { ?>
        <li><?php echo $chat['sender']; ?> says: <?php echo $chat['message']; ?></li>
    <?php } ?>
</ul>

<form method="POST" action="?q=chat-send">
    <?php echo $token ?? '' ?>
    <input type="hidden" name="id" value="<?php echo $id ?? ''; ?>">
    <input type="text" name="message" value="Say something..">
    <input type="submit">
</form>