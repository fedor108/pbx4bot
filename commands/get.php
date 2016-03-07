<?php
require_once __DIR__ . '/../app/Call.php';

$call = new Call;
$data = $call->get();
print_r($data);
