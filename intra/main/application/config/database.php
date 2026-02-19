<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('env')) {
	require_once(APPPATH . 'helpers/env_helper.php');
}

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'      => '',
	'hostname' => env('DB_DEFAULT_HOSTNAME', env('DB_HOSTNAME', env('DB_HOST', 'db'))),
	'username' => env('DB_DEFAULT_USERNAME', env('DB_USERNAME', 'eproc_app')),
	'password' => env('DB_DEFAULT_PASSWORD', env('DB_PASSWORD', 'change_me')),
	'database' => env('DB_DEFAULT_DATABASE', env('DB_DATABASE_PLANNING', 'eproc_perencanaan')),
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
	'port' => env('DB_DEFAULT_PORT', env('DB_PORT', 3306)),
);

$db['eproc'] = array(
	'dsn'      => '',
	'hostname' => env('DB_EPROC_HOSTNAME', env('DB_HOSTNAME', env('DB_HOST', 'db'))),
	'username' => env('DB_EPROC_USERNAME', env('DB_USERNAME', 'eproc_app')),
	'password' => env('DB_EPROC_PASSWORD', env('DB_PASSWORD', 'change_me')),
	'database' => env('DB_EPROC_DATABASE', env('DB_DATABASE', 'eproc')),
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
	'port' => env('DB_EPROC_PORT', env('DB_PORT', 3306)),
);

$db['test'] = $db['eproc'];
$db['perencanaan'] = $db['default'];
