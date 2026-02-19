<?php

spl_autoload_register(function ($class) {
	$prefix = 'App\\';
	if (strpos($class, $prefix) !== 0) {
		return;
	}

	$relative = substr($class, strlen($prefix));
	$path = APPPATH . 'src' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
	if (is_file($path)) {
		require $path;
	}
});

