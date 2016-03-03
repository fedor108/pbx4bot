<?php
function get_calls($secret_key, $key_id)
{
    $caller_names = [
        '141' => 'Илья',
        '201' => 'Яна',
        '203' => 'Ильфат',
        '206' => 'Наиль',
    ];

    $url = "api.onlinepbx.ru/" . PBX_DOMAIN . "/history/search.json";
    $post = [
        'billsec_from' => 6,
        'date_from' => date('r', strtotime(date("Y-m-d"))),
        'date_to' => date('r', strtotime(date("Y-m-d H"))),
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

    return $calls;
}

function put_calls($calls)
{
    echo "Звонки на " . date('H') . " часов (кол-во / продолжительность)" . PHP_EOL;
    foreach ($calls as $num => $caller) {
        $billsec = round($caller['billsec'] / 60);
        $count = count($caller['items']);
        echo "{$caller['name']}: {$count} / {$billsec} мин." . PHP_EOL;
    }
    echo PHP_EOL;
    return true;
}
