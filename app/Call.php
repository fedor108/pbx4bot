<?php
require_once __DIR__ . "/../vendor/fedor108/onpbx/onpbx_http_api.php";
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/User.php";

class Call
{
    public $secret_key;
    public $key_id;
    public $calls;
    public $time;
    public $result;

    public function __construct()
    {
        $key = onpbx_get_secret_key(PBX_DOMAIN, PBX_API_KEY, false);
        if (empty($key)) {
            $key = onpbx_get_secret_key(PBX_DOMAIN, PBX_API_KEY, true);
        }

        if (! empty($key)) {
            $this->secret_key = $key['data']['key'];
            $this->key_id = $key['data']['key_id'];
        } else {
            throw new Excerption("Не удалось получить ключ onpbx");
        }
    }

    public function getCountsPerUser($post = [])
    {
        $url = "api.onlinepbx.ru/" . PBX_DOMAIN . "/history/search.json";

        if (empty($post['date_from'])) {
            $post['date_from'] = date('r', strtotime(date("Y-m-d")));
        } else {
            $post['date_from'] = date('r', strtotime($post['date_from']));
        }

        if (empty($post['date_to'])) {
            $post['date_to'] = date('r');
        } else {
            $post['date_to'] = date('r', strtotime($post['date_to']));
        }

        if (empty($post['billsec_from'])) {
            $post['billsec_from'] = 6;
        }

        $result = onpbx_api_query($this->secret_key, $this->key_id, $url, $post);

        $calls = [];
        if (!empty($result['data'])) {
            $user = new User;
            $users_by_pbx = $user->getByPbx();
            foreach ($users_by_pbx as $item) {
                $calls[$item['id']] = [
                    // 'items' => [],
                    'billsec' => 0,
                    'user' => $users_by_pbx[$pbx_id],
                    'count' => 0,
                ];
            }

            foreach ($result['data'] as $item) {
                $pbx_id = $item['caller'];
                if (! array_key_exists($pbx_id, $users_by_pbx)) {
                    continue;
                }
                $id = $users_by_pbx[$pbx_id]['id'];
                // $calls[$id]['items'][] = $item;
                $calls[$id]['billsec'] += (int) $item['billsec'];
                $calls[$id]['count']++;
            }
        }

        // array_walk($calls, function (&$item) {
        //     $item['count'] = count($item['items']);
        //     unset($item['items']);
        // });

        $time = date('H:i', strtotime($post['date_to']));

        $this->result = $result;
        $this->calls = $calls;
        $this->time = $time;

        return $calls;
    }
}
