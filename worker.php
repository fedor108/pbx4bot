<?php
$root = __DIR__;
require_once $root . '/app/config.php';

$update_id_file = "{$root}/update.id";
$update_id = 0;
if (file_exists($update_id_file)) {
    $update_id = file_get_contents($update_id_file) + 1;
}

$url = "https://api.telegram.org/bot" . BOT_TOKEN . "/getUpdates"
    . "?offset={$update_id}";
$results = json_decode(file_get_contents($url), true);

if (!empty($results['result'])) {

    $update_id = end($results['result'])['update_id'];
    file_put_contents($update_id_file, $update_id);

    $chats = [];
    foreach ($results['result'] as $item) {
        if (!empty($item['message']['text'])
            &&  ('/calls' == $item['message']['text'])) {
            $chats[] = $item['message']['chat']['id'];
        }
    }

    if (!empty($chats)) {
        shell_exec("php -c ~/etc/php.ini {$root}/get.php now > {$root}/calls.txt");
        shell_exec("php -c ~/etc/php.ini {$root}/send.php " . implode(" ", $chats));
    }
}
