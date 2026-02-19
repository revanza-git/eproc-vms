<?php

if (!function_exists('parse_env_value')) {
	function parse_env_value($value)
	{
		if (!is_string($value)) {
			return $value;
		}
		$lower = strtolower(trim($value));
		if ($lower === 'true') {
			return true;
		}
		if ($lower === 'false') {
			return false;
		}
		if ($lower === 'null') {
			return null;
		}
		if (is_numeric($value)) {
			return $value + 0;
		}
		return $value;
	}
}

if (!function_exists('load_env_file')) {
	function load_env_file($file_path)
	{
		if (!is_string($file_path) || $file_path === '' || !file_exists($file_path)) {
			return false;
		}
		$lines = @file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if (!is_array($lines)) {
			return false;
		}

		foreach ($lines as $line) {
			$line = trim((string) $line);
			if ($line === '' || strpos($line, '#') === 0) {
				continue;
			}
			if (strpos($line, '=') === false) {
				continue;
			}
			list($key, $value) = explode('=', $line, 2);
			$key = trim($key);
			$value = trim($value);
			if ($key === '' || array_key_exists($key, $_ENV)) {
				continue;
			}
			if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
				(substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
				$value = substr($value, 1, -1);
			}
			$_ENV[$key] = $value;
			putenv($key . '=' . $value);
		}

		return true;
	}
}

if (!function_exists('load_env')) {
	function load_env($file_path = null)
	{
		$root = realpath(dirname(__DIR__));
		if ($root === false) {
			return false;
		}

		$paths = array();
		if (is_string($file_path) && $file_path !== '') {
			$paths[] = $file_path;
		}

		$paths[] = $root . DIRECTORY_SEPARATOR . '.env';
		$paths[] = $root . DIRECTORY_SEPARATOR . 'vms' . DIRECTORY_SEPARATOR . '.env';
		$paths[] = $root . DIRECTORY_SEPARATOR . 'vms' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . '.env';
		$paths[] = $root . DIRECTORY_SEPARATOR . 'intra' . DIRECTORY_SEPARATOR . '.env';
		$paths[] = $root . DIRECTORY_SEPARATOR . 'intra' . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . '.env';
		$paths[] = $root . DIRECTORY_SEPARATOR . 'intra' . DIRECTORY_SEPARATOR . 'pengadaan' . DIRECTORY_SEPARATOR . '.env';

		$loaded = false;
		foreach ($paths as $p) {
			if (load_env_file($p)) {
				$loaded = true;
			}
		}
		return $loaded;
	}
}

if (!function_exists('env')) {
	function env($key, $default = null)
	{
		if (array_key_exists($key, $_ENV)) {
			return parse_env_value($_ENV[$key]);
		}
		$value = getenv($key);
		if ($value !== false) {
			return parse_env_value($value);
		}
		if (array_key_exists($key, $_SERVER)) {
			return parse_env_value($_SERVER[$key]);
		}
		return $default;
	}
}

load_env();

