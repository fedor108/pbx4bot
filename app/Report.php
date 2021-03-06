<?php
require_once __DIR__ . "/User.php";

class Report
{
    public $file;
    public $report;

    public function __construct()
    {
        $this->file = __DIR__ . '/../tmp/report';
        $this->report = [];
    }

    public function createCallsAndLeads($calls, $leads)
    {
        $user = new User;
        $users = $user->get();
        $report = [];
        foreach ($users as $item) {
            $user_calls = $this->findCall($calls, $item['id']);
            $report[] = [
                'user' => $item,
                'calls_count' => $user_calls['count'],
                'billsec' => $user_calls['billsec'],
                'billmin' => round($user_calls['billsec'] / 60),
                'leads_count' => $this->findLeadsCount($leads, $item['id']),
            ];
        }
        $this->report = $report;
        return $report;
    }

    private function findCall($calls, $user_id)
    {
        $res = [];
        foreach ($calls as $item) {
            if ($item['user']['id'] == $user_id) {
                $res = $item;
                break;
            }
        }
        return $res;
    }

    private function findLeadsCount($leads, $user_id)
    {
        $res = 0;
        foreach ($leads as $item) {
            if ($item['user']['id'] == $user_id) {
                $res = $item['count'];
                break;
            }
        }
        return $res;
    }

    public function outCallsAndLeads($data)
    {
        $path = pathinfo($this->file);
        if (! file_exists($path['dirname'])) {
            mkdir($path['dirname'], 0775, true);
        }
        $time = date("H:i");
        $res = "Звонки и сделки на {$time}\n";
        foreach ($data as $item) {
            $res .= "{$item['user']['name']}: {$item['calls_count']} зв / {$item['billmin']} мин / {$item['leads_count']} сд\n";
        }
        file_put_contents($this->file, $res);
        return $res;
    }

    public function outTasks($data)
    {
        $path = pathinfo($this->file);
        if (! file_exists($path['dirname'])) {
            mkdir($path['dirname'], 0775, true);
        }
        $time = date("H:i");
        $res = "Просроченные задачи на {$time}\n";
        foreach ($data as $item) {
            $res .= "{$item['user']['name']}: {$item['count']}\n";
        }
        file_put_contents($this->file, $res);
        return $res;
    }


}