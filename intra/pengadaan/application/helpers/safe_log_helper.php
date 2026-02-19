<?php defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('safe_log_message')) {
	function safe_log_message($level, $message, $context = array())
	{
		$level = (string) $level;
		$message = (string) $message;
		if (!is_array($context) || empty($context)) {
			log_message($level, $message);
			return;
		}

		$sanitized = safe_log_sanitize_context($context);
		$payload = json_encode($sanitized);
		if ($payload === false) {
			$payload = '[unencodable_context]';
		}
		if (strlen($payload) > 1500) {
			$payload = substr($payload, 0, 1500) . '...';
		}
		log_message($level, $message . ' ' . $payload);
	}
}

if (!function_exists('safe_log_sanitize_context')) {
	function safe_log_sanitize_context($value)
	{
		$redactKeys = array('password', 'pass', 'token', 'csrf', 'cookie', 'session', 'authorization', 'bearer', 'secret', 'key');
		if (is_array($value)) {
			$out = array();
			foreach ($value as $k => $v) {
				$kStr = is_string($k) ? strtolower($k) : '';
				$shouldRedact = false;
				foreach ($redactKeys as $rk) {
					if ($kStr !== '' && strpos($kStr, $rk) !== false) {
						$shouldRedact = true;
						break;
					}
				}
				if ($shouldRedact) {
					$out[$k] = '[redacted]';
					continue;
				}
				$out[$k] = safe_log_sanitize_context($v);
			}
			return $out;
		}
		if (is_string($value)) {
			if (strlen($value) > 500) {
				return substr($value, 0, 500) . '...';
			}
			return $value;
		}
		if (is_object($value)) {
			return safe_log_sanitize_context((array) $value);
		}
		return $value;
	}
}

