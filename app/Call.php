<?php
require_once __DIR__ . "/../vendor/fedor108/onpbx/onpbx_http_api.php";
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/User.php";

class Call
{
    public $secret_key;
    public $key_id;

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

    public function get($date_to = null)
    {
        $url = "api.onlinepbx.ru/" . PBX_DOMAIN . "/history/search.json";

        if (is_null($date_to)) {
            $date_to = date('r');
        }

        $time = date('H:i');

        $post = [
            'billsec_from' => 6,
            'date_from' => date('r', strtotime(date("Y-m-d"))),
            'date_to' => $date_to,
        ];
        $results = onpbx_api_query($this->secret_key, $this->key_id, $url, $post);

        $calls = [];
        if (!empty($results['data'])) {
            $user = new User;
            $users_by_pbx = $user->getByPbx();
            foreach ($results['data'] as $item) {
                $pbx_id = $item['caller'];
                if (! array_key_exists($pbx_id, $users_by_pbx)) {
                    continue;
                }
                if (empty($calls[$pbx_id])) {
                    $calls[$pbx_id] = [
                        'items' => [],
                        'billsec' => 0,
                        'user' => $users_by_pbx[$pbx_id],
                    ];
                }
                $calls[$pbx_id]['items'][] = $item;
                $calls[$pbx_id]['billsec'] += (int) $item['billsec'];
            }
        }

        array_walk($calls, function (&$item) {
            $item['count'] = count($item['items']);
        });

        return compact('calls', 'time');

    }
}
