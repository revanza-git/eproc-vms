<?php
require('cron_base.php');

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,"http://127.0.0.1/arsip/berkas/cekOutdate");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_VERBOSE, true);
$server_output = curl_exec ($ch);

curl_close ($ch);
echo $server_output;
