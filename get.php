<?php
require_once './app/onpbx_http_api.php';
require_once './app/config.php';
require_once './app/onpbx.php';

$key_results = onpbx_get_secret_key(PBX_DOMAIN, PBX_API_KEY, $new=false);
if ($key_results) {
    $secret_key = $key_results['data']['key'];
    $key_id = $key_results['data']['key_id'];
}

$times = [12, 14, 17, 19];
foreach ($times as $hour) {
    get_calls($secret_key, $key_id, $hour);
}
