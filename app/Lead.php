<?php
use \AmoCRM\Handler;
use \AmoCRM\Request;

require_once __DIR__ . '/../vendor/autoload.php';

class Lead
{
    public $api;
    public $data;
    public $result;
    public $leads;

    public function __construct($domain, $email)
    {
        $this->api = new Handler($domain, $email);
        $this->file = __DIR__ . '/../data/leads';
        $this->leads = [];
    }

    public function getCreatedPerUser($params = [])
    {
        $this->update();

        $created = [];
        if (! empty($this->leads)) {

            $user = new User;
            $users_by_amo = $user->getByAmo();

            if (empty($params['create_from'])) {
                $params['create_from'] = strtotime(date('Y-m-d'));
            }

            if (empty($params['create_to'])) {
                $params['create_to'] = time();
            }

            foreach ($this->leads as $item) {
                $amo_id = $item['created_user_id'];
                if (! array_key_exists($item['created_user_id'], $users_by_amo)) {
                    continue;
                }
                $id = $users_by_amo[$amo_id]['id'];
                if (empty($created[$id])) {
                    $created[$id] = [
                        // 'items' => [],
                        'user' => $users_by_amo[$amo_id],
                        'count' => 0,
                    ];
                } elseif (($item['date_create'] >= $params['create_from'])
                    && ($item['date_create'] <= $params['create_to'])
                ) {
                    // $created[$id]['items'][] = $item;
                    $created[$id]['count']++;
                }
            }
        }

        return $created;
    }

    public function update()
    {
        $limit_offset = 0;
        // $limit_offset = $this->getFile();
        $this->getAmo($limit_offset);
        $this->saveFile();
    }

    private function getFile()
    {
        $limit_offset = 0;
        if (file_exists($this->file)) {
            $this->leads = json_decode(file_get_contents($this->file), true);
            $limit_offset = count($this->leads);
        }
        return $limit_offset;
    }

    private function getAmo($limit_offset = 0, $limit_rows = 500)
    {
        $amo_leads = [];
        do {
            $request = new Request(Request::GET, compact('limit_rows', 'limit_offset'), ['leads', 'list']);
            $data = json_decode(json_encode($this->api->request($request)->result), true);
            if (! empty($data['leads'])) {
                $amo_leads = array_merge($amo_leads, $data['leads']);
            }
            $limit_offset += $limit_rows;
        } while (! empty($data['leads']));

        if (! empty($amo_leads)) {
            $this->leads = array_merge($this->leads, $amo_leads);
        }
        return $amo_leads;
    }

    private function saveFile()
    {
        $path = pathinfo($this->file);
        if (! file_exists($path['dirname'])) {
            mkdir($path['dirname'], 0775, true);
        }
        file_put_contents($this->file, json_encode($this->leads));
    }
}
