<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('env')) {
	require_once(APPPATH . 'helpers/env_helper.php');
}

define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0755);

define('BASE_LINK', env('MAIN_BASE_URL', 'http://intra.localhost:8080/main/'));
define('BASE_LINK_EXTERNAL', env('EXTERNAL_EPROC_URL', env('MAIN_BASE_URL', 'http://intra.localhost:8080/main/')));
define('URL_TO_LOGIN', env('URL_TO_LOGIN', env('MAIN_VMS_URL', 'http://vms.localhost:8080/')));
define('URL_TO_VMS', env('URL_TO_VMS', env('MAIN_VMS_URL', 'http://vms.localhost:8080/')));
define('URL_TO_VENDOR', env('URL_TO_VENDOR', env('MAIN_PENGADAAN_URL', 'http://intra.localhost:8080/pengadaan/')));
define('URL_TO_EPROC', env('URL_TO_EPROC', env('MAIN_PENGADAAN_URL', 'http://intra.localhost:8080/pengadaan/')));

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb');
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b');
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

define('SHOW_DEBUG_BACKTRACE', TRUE);

define('EXIT_SUCCESS', 0);
define('EXIT_ERROR', 1);
define('EXIT_CONFIG', 3);
define('EXIT_UNKNOWN_FILE', 4);
define('EXIT_UNKNOWN_CLASS', 5);
define('EXIT_UNKNOWN_METHOD', 6);
define('EXIT_USER_INPUT', 7);
define('EXIT_DATABASE', 8);
define('EXIT__AUTO_MIN', 9);
define('EXIT__AUTO_MAX', 125);
