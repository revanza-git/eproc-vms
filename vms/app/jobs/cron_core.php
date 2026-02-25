<?php
if (!defined('BASEPATH')) {
	define('BASEPATH', __DIR__ . DIRECTORY_SEPARATOR);
}
if (!defined('APPPATH')) {
	define('APPPATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);
}
if (!defined('ENVIRONMENT')) {
	define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : ((getenv('CI_ENV') !== false) ? getenv('CI_ENV') : 'development'));
}

require_once __DIR__ . "/../application/config/database.php";
require_once __DIR__ . "/cron_mail.php";
require_once dirname(__DIR__, 3) . "/shared/legacy/cron_runtime.php";

class cron extends SharedCronRuntime{
	function __construct(){
		global $db;
		parent::__construct(shared_cron_default_db_config($db));
	}
}
