<?php

include 'SimplyRets.php';

$api = new SimplyRets();
//$json = $api -> test();
$json = $api -> keywordSearch('texas');
//$json = $api->adHocQuery('https://api.simplyrets.com/properties?q=texas');
var_dump($json);
echo "Test index";