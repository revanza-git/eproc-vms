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
		'file' => 'intra/main/application/controllers/Input.php',
		'required' => array(
			'/\\$id_pengadaan\\s*=\\s*\\(int\\)\\s*\\$id_pengadaan\\s*;/',
			'/\\$status\\s*=\\s*\\(int\\)\\s*\\$status\\s*;/',
			'/a\\.id_pengadaan\\s*=\\s*\\?\\s+AND\\s+a\\.is_status\\s*=\\s*\\?/',
			'/\\$this->db->query\\(\\$q,\\s*array\\(\\$id_pengadaan,\\s*\\$status\\)\\)\\s*->result\\(\\)/',
		),
		'forbidden' => array(
			'/a\\.id_pengadaan\\s*=\\s*\\$id_pengadaan/',
			'/a\\.is_status\\s*=\\s*\\$status/',
		),
	),
	array(
		'file' => 'intra/main/application/models/Main_model.php',
		'required' => array(
			'/\\$search\\s*=\\s*trim\\(\\(string\\)\\s*\\$value\\)\\s*;/',
			'/\\$this->db->query\\(\\$query,\\s*array\\(\\\'%\\\'\\.\\$search\\.\\\'%\\\',\\\'%\\\'\\.\\$search\\.\\\'%\\\'\\)\\)\\s*->result_array\\(\\)/',
		),
		'forbidden' => array(
			'/\\$_POST\\[\\s*\\\'search\\\'\\s*\\]\\s*\\.\\s*\\\'%\\\'/',
			'/array\\(\\s*\\\'%\\\'\\.\\$_POST\\[\\s*\\\'search\\\'\\s*\\]/',
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

	foreach ($check['required'] as $rule) {
		if (!preg_match($rule, $content)) {
			$errors[] = $check['file'] . ': required safe-query pattern missing (' . $rule . ')';
		}
	}

	foreach ($check['forbidden'] as $rule) {
		if (preg_match($rule, $content)) {
			$errors[] = $check['file'] . ': forbidden raw-query pattern still exists (' . $rule . ')';
		}
	}
}

if (count($errors) > 0) {
	fwrite(STDERR, "Query safety check failed:\n");
	foreach ($errors as $error) {
		fwrite(STDERR, "- {$error}\n");
	}
	exit(1);
}

echo "PASS: Sample high-risk query paths use parameter binding\n";
exit(0);
