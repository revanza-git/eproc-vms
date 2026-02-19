<?php
ini_set('max_execution_time', 3000);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Jakarta"); 
if (!defined('BASEPATH')) {
	define('BASEPATH', __DIR__ . DIRECTORY_SEPARATOR);
}
if (!defined('APPPATH')) {
	define('APPPATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);
}
if (!function_exists('env')) {
	require_once APPPATH . 'env_loader.php';
}

$base_url = env('EXTERNAL_PENGADAAN_URL', 'http://eproc.nusantararegas.com/eproc/');
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,$base_url."/admin/cron/drop_dpt");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_VERBOSE, true);
$server_output = curl_exec ($ch);

curl_close ($ch);
echo $server_output;
