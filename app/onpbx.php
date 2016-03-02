<?php
function get_calls($secret_key, $key_id, $hour = 12)
{
    if (date("H") < $hour) {
        $date_to = date('r');
        $hour = date('H:i', strtotime($date_to));
    } else {
        $date_to = date('r', strtotime(date("Y-m-d") . " {$hour}:00"));
    }

    $caller_names = [
        '141' => 'Илья',
        '201' => 'Яна',
        '203' => 'Ильфат',
        '206' => 'Наиль',
    ];

    $url = "api.onlinepbx.ru/" . PBX_DOMAIN . "/history/search.json";
    $date_from = date('r', strtotime(date("Y-m-d")));

    $post = [
        'date_from' => $date_from,
        'date_to' => $date_to,
    ];
    $calls_results = onpbx_api_query($secret_key, $key_id, $url, $post);

    $calls = [];
    foreach ($calls_results['data'] as $item) {
        $num = $item['caller'];
        if (! array_key_exists($num, $caller_names)) {
            continue;
        }

        $calls[$num]['items'][] = $item;

        if (!isset($calls[$num]['billsec'])) {
            $calls[$num]['billsec'] = 0;
            $calls[$num]['name'] = $caller_names[$num];
            $calls[$num]['count_success'] = 0;
        }
        if ($item['billsec'] > 6) {
            $calls[$num]['billsec'] += (int) $item['billsec'];
            $calls[$num]['count_success']++;
        }
    }

    echo "Звонки на {$hour} часов (кол-во / продолжительность)" . PHP_EOL;
    foreach ($calls as $num => $caller) {
        $billsec = round($caller['billsec'] / 60);
        $count = count($caller['items']);
        echo "{$caller['name']}: {$caller['count_success']} / {$billsec} мин." . PHP_EOL;
    }
    echo PHP_EOL;
    return true;
}
