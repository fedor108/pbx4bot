<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/Call.php";
require_once __DIR__ . "/Lead.php";
require_once __DIR__ . "/Report.php";

class Bot
{
    public $updatesFile;
    public $updateIdFile;
    public $updates;
    public $commands;

    public function __construct()
    {
        $this->updateIdFile = __DIR__ . "/../data/update-id";
        $this->chatsFile = __DIR__ . "/../data/chats";
        $this->chats = [];
        $this->updates = [];
        $this->commands = ['calls', 'add'];
    }

    public function getUpdates()
    {
        $update_id = $this->getUpdateId();
        $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/getUpdates"
            . "?offset={$update_id}";
        $res = json_decode(file_get_contents($url), true);
        $this->updates = $res['result'];

        $this->saveUpdateId();

        return $res['result'];
    }

    public function getUpdateId()
    {
        $id = 0;
        if (file_exists($this->updateIdFile)) {
            $id = file_get_contents($this->updateIdFile);
        }
        return $id;
    }

    public function saveUpdateId()
    {
        $path = pathinfo($this->updateIdFile);
        if (! file_exists($path['dirname'])) {
            mkdir($path['dirname'], 0775, true);
        }

        if (! empty($this->updates)) {
            $id = end($this->updates)['update_id'] + 1;
            return file_put_contents($this->updateIdFile, $id);
        }
        return true;
    }

    public function commandsExec()
    {
        $commands = ['/calls'];
        foreach ($this->updates as $item) {
            if (! empty($item['message']['text'])) {
                $text = $item['message']['text'];
                $chat_id = $item['message']['chat']['id'];
                if (0 === strpos($text, '/')) {
                    $command = explode(' ', substr($text, 1), 1)[0];

                    if ('calls' == $command) {
                        $this->callsCommand($chat_id);
                    } elseif('add' == $command) {
                        $this->addCommand($chat_id);
                    } else {
                        $this->unknownCommand($chat_id);
                    }
                } else {
                    $this->noCommand($chat_id);
                }
            }
        }
    }

    private function noCommand($chat_id)
    {
        $send_message = 'Здравствуй, %человек%!';
        $this->send($chat_id, $send_message);
    }

    private function unknownCommand($chat_id)
    {
        $send_message = '/' . implode(' /', $this->commands);
        $this->send($chat_id, $send_message);
    }

    private function callsCommand($chat_id)
    {
        $call = new Call;
        $calls = $call->getCountsPerUser();

        $lead = new Lead('new4', 'fedor@neq4.ru');
        $leads = $lead->getCreatedPerUser();

        $report = new Report;
        $report->createCallsAndLeads($calls, $leads);

        $send_message = $report->saveFile();
        $this->send($chat_id, $send_message);
    }

    private function addCommand($chat_id)
    {
        $this->getChatsFromFile();
        if (! in_array($chat_id, $chat_id)) {
            $this->chats[] = $chat_id;
        }
        $this->saveChatsFile();
        $this->send($chat_id, 'Вы подписаны на регулярные отчеты по звонкам и сделкам.');
    }

    private function saveChatsFile()
    {
        $path = pathinfo($this->chatsFile);
        if (! file_exists($path['dirname'])) {
            mkdir($path['dirname'], 0775, true);
        }

        return file_put_contents($this->chatsFile, json_encode($this->chats));
    }

    private function getChatsFromFile()
    {
        $chats = [];
        if (file_exists($this->chatsFile)) {
            $chats = json_decode(file_get_contents($this->chatsFile), true);
        }
        $this->chats = $chats;
        return $chats;
    }

    public function callsExec()
    {
        $call = new Call;
        $calls = $call->getCountsPerUser();

        $lead = new Lead;
        $leads = $lead->getCreatedPerUser();

        $report = new Report;
        $report->createCallsAndLeads($calls, $leads);

        $send_message = $report->saveFile();

        $this->getChatsFromFile();
        foreach ($this->chats as $chat_id) {
            $this->send($chat_id, $send_message);
        }
    }

    public function send($chat_id, $text)
    {
        $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage"
            ."?chat_id={$chat_id}"
            . "&text=" . urlencode($text);
        return file_get_contents($url);
    }
}
