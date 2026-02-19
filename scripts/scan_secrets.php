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

$scanExtensions = array('php', 'env', 'ini', 'yml', 'yaml', 'sql', 'txt');

$skipDirs = array(
	$root . DIRECTORY_SEPARATOR . '.git',
	$root . DIRECTORY_SEPARATOR . 'vms' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'system',
	$root . DIRECTORY_SEPARATOR . 'intra' . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'system',
	$root . DIRECTORY_SEPARATOR . 'intra' . DIRECTORY_SEPARATOR . 'pengadaan' . DIRECTORY_SEPARATOR . 'system',
	$root . DIRECTORY_SEPARATOR . 'vms' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'vendor',
	$root . DIRECTORY_SEPARATOR . 'intra' . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'vendor',
	$root . DIRECTORY_SEPARATOR . 'intra' . DIRECTORY_SEPARATOR . 'pengadaan' . DIRECTORY_SEPARATOR . 'vendor',
	$root . DIRECTORY_SEPARATOR . 'intra' . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'third_party',
	$root . DIRECTORY_SEPARATOR . 'intra' . DIRECTORY_SEPARATOR . 'pengadaan' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'js',
);

$allowFiles = array(
	$root . DIRECTORY_SEPARATOR . '.env.example',
	$root . DIRECTORY_SEPARATOR . 'vms' . DIRECTORY_SEPARATOR . '.env.example',
	$root . DIRECTORY_SEPARATOR . 'intra' . DIRECTORY_SEPARATOR . '.env.example',
	__FILE__,
);

$patterns = array(
	array(
		'label' => 'Embedded private key material',
		'regex' => '/-----BEGIN (?:RSA |EC |DSA |OPENSSH |)?PRIVATE KEY-----/i',
		'capture' => null,
	),
	array(
		'label' => 'Hardcoded smtp_pass',
		'regex' => '/smtp_pass\\s*\\=>\\s*[\\\'\\"]([^\\\'\\"]+)[\\\'\\"]/i',
		'capture' => 1,
	),
	array(
		'label' => 'Hardcoded PDO password argument',
		'regex' => '/new\\s+PDO\\s*\\(\\s*[\\\'\\"][^\\\'\\"]+[\\\'\\"]\\s*,\\s*[\\\'\\"][^\\\'\\"]+[\\\'\\"]\\s*,\\s*[\\\'\\"]([^\\\'\\"]+)[\\\'\\"]/i',
		'capture' => 1,
	),
	array(
		'label' => 'Hardcoded mysqli password argument',
		'regex' => '/new\\s+mysqli\\s*\\(\\s*[^,\\n]+,\\s*[\\\'\\"][^\\\'\\"]+[\\\'\\"]\\s*,\\s*[\\\'\\"]([^\\\'\\"]+)[\\\'\\"]\\s*,/i',
		'capture' => 1,
	),
	array(
		'label' => 'Hardcoded credential assignment',
		'regex' => '/\\b(?:DB_PASSWORD|MYSQL_ROOT_PASSWORD|MYSQL_PASSWORD|EMAIL_SMTP_PASS(?:WORD)?|JWT_SECRET_KEY|ENCRYPTION_KEY)\\b\\s*[:=]\\s*[\\\'\\"]?([^\\\'\\"\\s#]+)[\\\'\\"]?/i',
		'capture' => 1,
	),
);

function is_placeholder_secret($value)
{
	$value = trim((string) $value);
	if ($value === '') {
		return true;
	}

	if (preg_match('/^\\$[A-Za-z_][A-Za-z0-9_]*$/', $value)) {
		return true;
	}

	if (strpos($value, '${') !== false) {
		return true;
	}

	$lower = strtolower($value);
	$safeExact = array(
		'root',
		'password',
		'admin123',
		'changeme',
		'change_me',
		'change_me_local',
		'dev_only_change_me',
		'dev_only_change_me_jwt_secret',
		'change_me_local_jwt',
		'your_password',
		'your_email_password',
		'example',
		'null',
		'localhost',
		'127.0.0.1',
	);
	if (in_array($lower, $safeExact, true)) {
		return true;
	}

	$safeFragments = array(
		'change_me',
		'dev_only',
		'example',
		'placeholder',
	);
	foreach ($safeFragments as $fragment) {
		if (strpos($lower, $fragment) !== false) {
			return true;
		}
	}

	return false;
}

function path_is_skipped($path, $skipDirs)
{
	foreach ($skipDirs as $skipDir) {
		if (strpos($path, $skipDir) === 0) {
			return true;
		}
	}
	return false;
}

function detect_line_number($content, $offset)
{
	if ($offset <= 0) {
		return 1;
	}
	return substr_count(substr($content, 0, $offset), "\n") + 1;
}

function collect_tracked_files($root)
{
	$files = array();
	$cmd = 'git -C ' . escapeshellarg($root) . ' ls-files';
	$raw = @shell_exec($cmd);
	if (!is_string($raw) || trim($raw) === '') {
		return $files;
	}

	$lines = preg_split('/\\r\\n|\\n|\\r/', trim($raw));
	foreach ($lines as $line) {
		$line = trim($line);
		if ($line === '') {
			continue;
		}
		$full = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $line);
		if (is_file($full)) {
			$files[] = $full;
		}
	}

	return $files;
}

$files = collect_tracked_files($root);
if (count($files) === 0) {
	$it = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
	);
	foreach ($it as $fileInfo) {
		if ($fileInfo->isFile()) {
			$files[] = $fileInfo->getPathname();
		}
	}
}

$findings = array();

foreach ($files as $path) {
	if (path_is_skipped($path, $skipDirs)) {
		continue;
	}

	$real = realpath($path);
	if ($real !== false && in_array($real, $allowFiles, true)) {
		continue;
	}

	$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
	if (!in_array($ext, $scanExtensions, true)) {
		continue;
	}

	$content = @file_get_contents($path);
	if ($content === false || $content === '') {
		continue;
	}

	$relativePath = str_replace($root . DIRECTORY_SEPARATOR, '', $path);

	foreach ($patterns as $pattern) {
		$matches = array();
		if (!preg_match_all($pattern['regex'], $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
			continue;
		}

		foreach ($matches as $match) {
			$offset = isset($match[0][1]) ? (int) $match[0][1] : 0;
			$line = detect_line_number($content, $offset);

			if ($pattern['capture'] !== null) {
				$captureIndex = (int) $pattern['capture'];
				$secretValue = isset($match[$captureIndex][0]) ? (string) $match[$captureIndex][0] : '';
				if (is_placeholder_secret($secretValue)) {
					continue;
				}
			}

			$findings[] = $relativePath . ':' . $line . ': ' . $pattern['label'];
		}
	}
}

$findings = array_values(array_unique($findings));
sort($findings);

if (count($findings) > 0) {
	fwrite(STDERR, "Potential secrets found:\n");
	foreach ($findings as $finding) {
		fwrite(STDERR, "- {$finding}\n");
	}
	exit(1);
}

echo "OK: no obvious hardcoded secrets detected\n";
exit(0);
