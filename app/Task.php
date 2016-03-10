<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/../vendor/autoload.php';

class Task
{
    public $api;
    public $data;
    public $file;

    public function __construct()
    {
        $this->api = new AmoRestApi(AMO_DOMAIN, AMO_USER, AMO_KEY);
        $this->data = [];
        $this->file = __DIR__ . '/../data/tasks';
    }

    public function getOverduePerUser()
    {
        $res = [];
        $time = time();
        $user = new User;
        $users_by_amo = $user->getByAmo();

        $this->update(array_keys($users_by_amo));

        foreach ($users_by_amo as $amo_id => $item) {
            $count = 0;
            if (! empty($this->data)) {
                $overdue = array_filter($this->data, function ($task) use ($time, $amo_id){
                    return (($task['responsible_user_id'] == $amo_id)
                        && (0 == $task['status'])
                        && ($task['complete_till'] < $time));
                });
                $count = count($overdue);
            }
            $res[$item['id']] = [
                'user' => $item,
                'count' => $count,
            ];
        }
        return $res;
    }

    public function update($responsible = null)
    {
        $dateModified = null;
        $this->data = [];
        if (file_exists($this->file)) {
            $dateModified = new DateTime(filemtime($this->file));
            $this->data = json_decode(file_get_contents($this->file), true);
        }

        $limit_offset = 0;
        do {
            $res = $this->api->getTasksList(
                $limit_rows = 500,
                $limit_offset,
                $ids = null,
                $query = null,
                $responsible,
                $status = null,
                $dateModified
            );
            if (! empty($res['tasks'])) {
                $tasks = [];
                foreach ($res['tasks'] as $task) {
                    $tasks[$task['id']] = $task;
                }
                $this->data = array_replace($this->data, $tasks);
            }
            $limit_offset += $limit_rows;

        } while (! empty($res['tasks']));

        $this->saveFile();
        return $this->data;
    }

    public function saveFile()
    {
        $path = pathinfo($this->file);
        if (! file_exists($path['dirname'])) {
            mkdir($path['dirname'], 0775, true);
        }
        return file_put_contents($this->file, json_encode($this->data));
    }
}
