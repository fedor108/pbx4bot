<?php
require_once './onpbx_http_api.php';
require_once './config.php';
require_once './onpbx.php';

$key_results = onpbx_get_secret_key(PBX_DOMAIN, PBX_API_KEY, $new=false);
if ($key_results) {
    $secret_key = $key_results['data']['key'];
    $key_id = $key_results['data']['key_id'];
}

$times = [12, 14, 17, 19];
foreach ($times as $hour) {
    getCalls($secret_key, $key_id, $hour);
}
