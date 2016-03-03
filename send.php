<?php
$root = __DIR__;
require_once $root . '/app/config.php';

$message = file_get_contents("{$root}/calls.txt");

if (empty($argv[1])) {
    $chats = [
        // "146574136",
        "@pbx4bot_channel",
        // '-3533819',
    ];
} else {
    $chats = [];
    $i = 1;
    while (!empty($argv[$i])) {
        $chats[] = $argv[$i];
        $i++;
    }
}

foreach ($chats as $chat_id) {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage"
        ."?chat_id={$chat_id}"
        . "&text=" . urlencode($message);
    $results[] = file_get_contents($url);
}
