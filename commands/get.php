<?php
require_once __DIR__ . '/../app/Call.php';
require_once __DIR__ . '/../app/Lead.php';

$call = new Call;
$calls = $call->getCountsPerUser(['date_from' => '2016-03-04']);

$lead = new Lead('new4', 'fedor@neq4.ru');
$leads = $lead->getCreatedPerUser(['create_from' => strtotime('2016-03-04')]);

print_r(compact('calls', 'leads'));

