<?php
$root = __DIR__;
require_once $root . '/app/onpbx_http_api.php';
require_once $root . '/app/config.php';
require_once $root . '/app/onpbx.php';

$key_results = onpbx_get_secret_key(PBX_DOMAIN, PBX_API_KEY, $new=false);
if ($key_results) {
    $secret_key = $key_results['data']['key'];
    $key_id = $key_results['data']['key_id'];
}
if (!empty($argv[1]) && ('now' == $argv[1])) {
    // отчет по звонкам не текущий момент
    $calls = get_calls($secret_key, $key_id, true);
} else {
    // отчет по звонкам на начало текущего часа
    $calls = get_calls($secret_key, $key_id);
}

put_calls($calls);
