<?php
require_once './app/config.php';

$message = file_get_contents('calls.txt');

$chats = [
    // "146574136",
    "@pbx4bot_channel",
    // '-3533819',
];
foreach ($chats as $chat_id) {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage"
        ."?chat_id={$chat_id}"
        . "&text=" . urlencode($message);
    $results[] = file_get_contents($url);
}
