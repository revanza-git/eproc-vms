<?php

error_reporting(E_ALL);

$targets = array(
	__DIR__ . '/../vms/app/jobs/cron_core.php',
	__DIR__ . '/../vms/app/jobs/cron_dpt.php',
	__DIR__ . '/../vms/app/jobs/cron_blacklist.php',
	__DIR__ . '/../intra/pengadaan/cron_core.php',
	__DIR__ . '/../intra/pengadaan/cron_dpt.php',
	__DIR__ . '/../intra/pengadaan/cron_blacklist.php',
	__DIR__ . '/../vms/app/system/core/Security.php',
	__DIR__ . '/../intra/main/system/core/Security.php',
	__DIR__ . '/../intra/pengadaan/system/core/Security.php',
	__DIR__ . '/../vms/app/application/third_party/MX/Modules.php',
	__DIR__ . '/../intra/pengadaan/application/third_party/MX/Modules.php',
);

$checks = array(
	array(
		'label' => 'legacy mysql_* API',
		'pattern' => '/\bmysql_(?:connect|pconnect|select_db|query|fetch_array|fetch_assoc|num_rows|close)\s*\(/i',
		'severity' => 'error',
	),
	array(
		'label' => 'removed each() API',
		'pattern' => '/(?<![A-Za-z0-9_$.])each\s*\(/i',
		'severity' => 'error',
	),
	array(
		'label' => 'removed create_function() API',
		'pattern' => '/\bcreate_function\s*\(/i',
		'severity' => 'warn',
	),
);

$results = array();
foreach ($checks as $check) {
	$results[$check['label']] = array(
		'severity' => $check['severity'],
		'matches' => array(),
	);
}

foreach ($targets as $path) {
	if (!is_file($path)) {
		continue;
	}

	$lines = @file($path, FILE_IGNORE_NEW_LINES);
	if (!is_array($lines)) {
		continue;
	}

	foreach ($lines as $lineNo => $lineText) {
		foreach ($checks as $check) {
			if (preg_match($check['pattern'], $lineText)) {
				$results[$check['label']]['matches'][] = array(
					'file' => str_replace('\\', '/', $path),
					'line' => $lineNo + 1,
					'text' => trim($lineText),
				);
			}
		}
	}
}

$hasError = false;
foreach ($results as $label => $result) {
	$count = count($result['matches']);
	echo strtoupper($result['severity']) . ": {$label} => {$count} hit(s)\n";

	$maxPreview = 5;
	for ($i = 0; $i < $count && $i < $maxPreview; $i++) {
		$match = $result['matches'][$i];
		echo "  - {$match['file']}:{$match['line']} {$match['text']}\n";
	}

	if ($result['severity'] === 'error' && $count > 0) {
		$hasError = true;
	}
}

if ($hasError) {
	exit(1);
}

echo "PASS: no high-severity PHP 8.2 blockers found in scoped runtime paths\n";
