<?php
require_once __DIR__ . '/../app/Bot.php';

$bot = new Bot;
$bot->getUpdates();
$bot->commandsExec();
