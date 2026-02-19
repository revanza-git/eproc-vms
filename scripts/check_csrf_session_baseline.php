<?php

if (php_sapi_name() !== 'cli') {
	fwrite(STDERR, "CLI only\n");
	exit(2);
}

$root = realpath(__DIR__ . '/..');
if ($root === false) {
	fwrite(STDERR, "Repo root not found\n");
	exit(2);
}

$checks = array(
	array(
		'file' => 'vms/app/application/config/config.php',
		'rules' => array(
			'/\\$config\\[\\\'csrf_protection\\\'\\]\\s*=\\s*env\\(\\s*\\\'CSRF_PROTECTION\\\'\\s*,\\s*TRUE\\s*\\)\\s*;/',
			'/\\$config\\[\\\'sess_match_ip\\\'\\]\\s*=\\s*env\\(\\s*\\\'SESSION_MATCH_IP\\\'\\s*,\\s*FALSE\\s*\\)\\s*;/',
			'/\\$config\\[\\\'cookie_httponly\\\'\\]\\s*=\\s*env\\(\\s*\\\'COOKIE_HTTPONLY\\\'\\s*,\\s*TRUE\\s*\\)\\s*;/',
		),
	),
	array(
		'file' => 'intra/main/application/config/config.php',
		'rules' => array(
			'/\\$config\\[\\\'csrf_protection\\\'\\]\\s*=\\s*env\\(\\s*\\\'CSRF_PROTECTION\\\'\\s*,\\s*TRUE\\s*\\)\\s*;/',
			'/\\$config\\[\\\'sess_match_ip\\\'\\]\\s*=\\s*env\\(\\s*\\\'SESSION_MATCH_IP\\\'\\s*,\\s*FALSE\\s*\\)\\s*;/',
			'/\\$config\\[\\\'cookie_httponly\\\'\\]\\s*=\\s*env\\(\\s*\\\'COOKIE_HTTPONLY\\\'\\s*,\\s*TRUE\\s*\\)\\s*;/',
		),
	),
	array(
		'file' => 'intra/pengadaan/application/config/config.php',
		'rules' => array(
			'/require_once\\(APPPATH\\s*\\.\\s*\\\'helpers\\/env_helper\\.php\\\'\\);/',
			'/\\$config\\[\\\'csrf_protection\\\'\\]\\s*=\\s*env\\(\\s*\\\'CSRF_PROTECTION\\\'\\s*,\\s*TRUE\\s*\\)\\s*;/',
			'/\\$config\\[\\\'sess_match_ip\\\'\\]\\s*=\\s*env\\(\\s*\\\'SESSION_MATCH_IP\\\'\\s*,\\s*FALSE\\s*\\)\\s*;/',
			'/\\$config\\[\\\'cookie_httponly\\\'\\]\\s*=\\s*env\\(\\s*\\\'COOKIE_HTTPONLY\\\'\\s*,\\s*TRUE\\s*\\)\\s*;/',
		),
	),
);

$errors = array();

foreach ($checks as $check) {
	$filePath = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $check['file']);
	if (!is_file($filePath)) {
		$errors[] = $check['file'] . ': file not found';
		continue;
	}

	$content = file_get_contents($filePath);
	if ($content === false) {
		$errors[] = $check['file'] . ': failed to read file';
		continue;
	}

	foreach ($check['rules'] as $rule) {
		if (!preg_match($rule, $content)) {
			$errors[] = $check['file'] . ': missing expected policy rule ' . $rule;
		}
	}
}

if (count($errors) > 0) {
	fwrite(STDERR, "CSRF/session baseline check failed:\n");
	foreach ($errors as $error) {
		fwrite(STDERR, "- {$error}\n");
	}
	exit(1);
}

echo "PASS: CSRF/session baseline is consistent across apps\n";
exit(0);
