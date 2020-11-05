<h1>Edit Chat</h1>
<?php echo isset($this) ? $this->create('messages.html.php', $this->data) : ''; ?>
<form method="POST" action="?q=editchat">
    <input type="hidden" name="id" value="<?php echo $id ?? '' ?>">
    <input type="text" name="name" value="<?php echo $name ?? '' ?>" placeholder="Chat name">
    <input type="submit" value="Save">
</form>

<h2>Conversation script</h2>
<ul>
    <?php foreach ($messages ?? [] as $mkey => $message) { ?>
    <li>
        <?php echo $message['talks'] ?> says #<?php echo $message['id'] ?>: <?php echo $message['content'] ?>
        <a href="?q=delmsg&id=<?php echo $message['id']; ?>">delete</a>
        <br>
        Possible human responses: (<?php echo count($message['human_response_messages'])?>)
        <ul>
            <?php foreach ($message['human_response_messages'] as $hkey => $hmessage) { ?>
            <li>
                <?php echo $hmessage['talks'] ?> says #<?php echo $hmessage['id'] ?>: <?php echo $hmessage['content'] ?>
                <a href="?q=delmsg&id=<?php echo $hmessage['id']; ?>">delete</a>
                
                <form id="modifymsg2msgform-<?php echo "$mkey-$hkey" ?>" method="POST" action="?q=modifymsg2msg">
                    <?php echo $token ?? '' ?>
                    <input type="hidden" name="message[chat_id]" value="<?php echo $id ?? '' ?>">
                    <input type="hidden" name="message_to_message[request_message_id]" value="<?php echo $hmessage['id'] ?>">
                    <span>=> Chatbot response:</span>
                    <select name="message_to_message[response_message_id]" onchange="document.getElementById('modifymsg2msgform-<?php echo "$mkey-$hkey" ?>').submit();">
                        <option value="0">-- End conversation --</option>
                        <?php foreach ($messages ?? [] as $bmessage) { ?>
                        <option value="<?php echo $bmessage['id'] ?>"<?php if ($bmessage['id'] === $hmessage['response_message_id']) { ?> <?php echo "selected" ?> <?php } ?>>#<?php echo $bmessage['id'] ?>: <?php echo $bmessage['content'] ?></option>
                        <?php } ?>
                    </select>
                </form>

            </li>
            <?php } ?>
        </ul>
        <form method="POST" action="?q=createmsg">
            <?php echo $token ?? '' ?>
            <input type="hidden" name="message[chat_id]" value="<?php echo $id ?? '' ?>">
            <input type="hidden" name="message[talks]" value="human">
            <input type="text" name="message[content]" placeholder="Human says...">

            <input type="hidden" name="message_to_message[request_message_id]" value="<?php echo $message['id'] ?>">

            <input type="submit">
        </form>
    </li>
    <?php } ?>
</ul>

<form method="POST" action="?q=createmsg">
    <?php echo $token ?? '' ?>
    <input type="hidden" name="message[chat_id]" value="<?php echo $id ?? '' ?>">
    <input type="hidden" name="message[talks]" value="chatbot">
    <input type="text" name="message[content]" placeholder="Chatbot says...">
    <input type="submit">
</form>

<a href="?q=logout">Logout</a>
<a href="?q=mychats">My Chats</a>
<a href="?q=createchat">Create new chat</a>
<a href="?q=chat&id=<?php echo $id ?? '' ?>">Go to this Chat</a>