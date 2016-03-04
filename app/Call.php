<?php

class Call
{
    public $secret_key;
    public $key_id;

    public __construct()
    {
        $key = onpbx_get_secret_key(PBX_DOMAIN, PBX_API_KEY, $new=false);
        if ($key) {
            $secret_key = $key['data']['key'];
            $key_id = $key['data']['key_id'];
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
        $results = onpbx_api_query($secret_key, $key_id, $url, $post);

        $calls = [];
        foreach ($results['data'] as $item) {
            $num = $item['caller'];
            if (! array_key_exists($num, $caller_names)) {
                continue;
            }
            if (empty($calls[$num])) {
                $calls[$num] = [
                    'items' => [],
                    'billsec' => 0,
                    'name' => $caller_names[$num],
                ];
            }
            $calls[$num]['items'][] = $item;
            $calls[$num]['billsec'] += (int) $item['billsec'];
        }

        return compact('calls', 'time');

    }
}
