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

$skipDirs = array(
	$root . DIRECTORY_SEPARATOR . 'vms' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'system',
	$root . DIRECTORY_SEPARATOR . 'intra' . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'system',
	$root . DIRECTORY_SEPARATOR . 'intra' . DIRECTORY_SEPARATOR . 'pengadaan' . DIRECTORY_SEPARATOR . 'system',
	$root . DIRECTORY_SEPARATOR . 'vms' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'vendor',
	$root . DIRECTORY_SEPARATOR . 'intra' . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'vendor',
	$root . DIRECTORY_SEPARATOR . 'intra' . DIRECTORY_SEPARATOR . 'pengadaan' . DIRECTORY_SEPARATOR . 'vendor',
	$root . DIRECTORY_SEPARATOR . '.git',
);

$allowFiles = array(
	$root . DIRECTORY_SEPARATOR . '.env.example',
	$root . DIRECTORY_SEPARATOR . 'vms' . DIRECTORY_SEPARATOR . '.env.example',
	$root . DIRECTORY_SEPARATOR . 'intra' . DIRECTORY_SEPARATOR . '.env.example',
);

$extensions = array('php', 'env', 'sql', 'yml', 'yaml', 'md');

$patterns = array(
	'/smtp_pass\\s*\\=>\\s*[\\\'\\"](.{6,})[\\\'\\"]/i' => 'Hardcoded smtp_pass',
	'/MYSQL_ROOT_PASSWORD\\s*=\\s*[^\\s#]+/i' => 'Hardcoded MySQL root password',
	'/JWT_SECRET_KEY\\s*=\\s*[^\\s#]+/i' => 'Hardcoded JWT secret key',
	'/\\b(SECRET|API_KEY|ACCESS_KEY|PRIVATE_KEY)\\b\\s*[:=]\\s*[\\\'\\"]?[^\\\'\\"\\s#]{12,}/i' => 'Possible secret assignment',
);

$findings = array();

$it = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

foreach ($it as $fileInfo) {
	$path = $fileInfo->getPathname();

	if ($fileInfo->isDir()) {
		continue;
	}

	$skip = false;
	foreach ($skipDirs as $skipDir) {
		if (strpos($path, $skipDir) === 0) {
			$skip = true;
			break;
		}
	}
	if ($skip) {
		continue;
	}

	$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
	if (!in_array($ext, $extensions, true)) {
		continue;
	}
	if ($ext === 'md') {
		continue;
	}

	$real = realpath($path);
	if ($real !== false && in_array($real, $allowFiles, true)) {
		continue;
	}

	$content = @file_get_contents($path);
	if ($content === false) {
		continue;
	}

	foreach ($patterns as $regex => $label) {
		if (preg_match($regex, $content)) {
			$findings[] = $label . ': ' . str_replace($root . DIRECTORY_SEPARATOR, '', $path);
		}
	}
}

$findings = array_values(array_unique($findings));
sort($findings);

if (count($findings) > 0) {
	fwrite(STDERR, "Potential secrets found:\n");
	foreach ($findings as $f) {
		fwrite(STDERR, "- {$f}\n");
	}
	exit(1);
}

echo "OK: no obvious hardcoded secrets detected\n";
exit(0);
