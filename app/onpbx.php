<?php
function get_calls($secret_key, $key_id, $now = false)
{
    $caller_names = [
        '141' => 'Илья',
        '201' => 'Яна',
        '203' => 'Ильфат',
        '206' => 'Наиль',
    ];

    $url = "api.onlinepbx.ru/" . PBX_DOMAIN . "/history/search.json";

    if ($now) {
        $date_to = date('r');
        $time = date('H:i');
    } else {
        $date_to = date('r', strtotime(date("Y-m-d H")));
        $time = date('H');
    }

    $post = [
        'billsec_from' => 6,
        'date_from' => date('r', strtotime(date("Y-m-d"))),
        'date_to' => $date_to,
    ];
    $calls_results = onpbx_api_query($secret_key, $key_id, $url, $post);

    $calls = [];
    foreach ($calls_results['data'] as $item) {
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

function put_calls($params)
{
    extract($params);
    if (strpos($time, ':')) {
        echo "Звонки на " . $time . " (кол-во / продолжительность)" . PHP_EOL;
    } else {
        echo "Звонки на " . $time . " часов (кол-во / продолжительность)" . PHP_EOL;
    }

    foreach ($calls as $num => $caller) {
        $billsec = round($caller['billsec'] / 60);
        $count = count($caller['items']);
        echo "{$caller['name']}: {$count} / {$billsec} мин." . PHP_EOL;
    }
    echo PHP_EOL;
    return true;
}
