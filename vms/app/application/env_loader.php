<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Define ENVIRONMENT constant early if not already defined
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'production');
}

$dir = __DIR__;
$shared = null;
for ($i = 0; $i < 8; $i++) {
	$try = $dir . '/shared/env.php';
	if (is_file($try)) {
		$shared = $try;
		break;
	}
	$parent = dirname($dir);
	if ($parent === $dir) {
		break;
	}
	$dir = $parent;
}
if ($shared) {
	require_once $shared;
}
