<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('env')) {
	require_once(dirname(dirname(__FILE__)) . '/env_loader.php');
}

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'      => '',
	'hostname' => env('DB_HOSTNAME', env('DB_HOST', 'db')),
	'username' => env('DB_USERNAME', 'eproc_app'),
	'password' => env('DB_PASSWORD', 'change_me'),
	'database' => env('DB_DATABASE', 'eproc'),
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE,
	'port' => env('DB_PORT', 3306),
);

$db['perencanaan'] = array(
	'dsn'      => '',
	'hostname' => env('DB_PLANNING_HOSTNAME', env('DB_HOSTNAME', env('DB_HOST', 'db'))),
	'username' => env('DB_PLANNING_USERNAME', env('DB_USERNAME', 'eproc_app')),
	'password' => env('DB_PLANNING_PASSWORD', env('DB_PASSWORD', 'change_me')),
	'database' => env('DB_DATABASE_PLANNING', 'eproc_perencanaan'),
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE,
	'port' => env('DB_PLANNING_PORT', env('DB_PORT', 3306)),
);
