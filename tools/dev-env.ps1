param(
	[ValidateSet('bootstrap', 'start', 'stop', 'down', 'restart', 'reset', 'status', 'logs', 'smoke', 'doctor', 'deps', 'cron')]
	[string] $Action = 'status',
	[string] $Service = '',
	[ValidateSet('7.4', '8.2')]
	[string] $PhpRuntime = '7.4',
	[switch] $NoBuild
)

$ErrorActionPreference = 'Stop'

$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

function Invoke-Compose {
	param([string[]] $ComposeArgs)
	$composeFiles = @('-f', 'docker-compose.yml')
	if ($PhpRuntime -eq '8.2') {
		$composeFiles += @('-f', 'docker-compose.php82.yml')
	}

	& docker compose @composeFiles @ComposeArgs
	if ($LASTEXITCODE -ne 0) {
		throw "docker compose $($composeFiles -join ' ') $($ComposeArgs -join ' ') failed"
	}
}

function Copy-IfMissing {
	param(
		[string] $Source,
		[string] $Destination
	)
	if (!(Test-Path $Destination)) {
		Copy-Item $Source $Destination
		Write-Host "created $Destination from template"
	}
}

function Test-Endpoint {
	param([string] $Url)

	$raw = & curl.exe -sS -L -w "`n%{http_code}" $Url
	if ($LASTEXITCODE -ne 0) {
		return [pscustomobject]@{
			url = $Url
			code = 0
			ok = $false
			reason = 'curl failed'
		}
	}

	$parts = $raw -split "`n"
	$code = [int] $parts[-1]
	$body = ($parts[0..($parts.Length - 2)] -join "`n")
	$pattern = 'Fatal error|A PHP Error was encountered|An Error Was Encountered|The configuration file .* does not exist|Unable to connect to your database server'
	$hasAppError = $body -match $pattern
	$ok = ($code -lt 400) -and (-not $hasAppError)

	return [pscustomobject]@{
		url = $Url
		code = $code
		ok = $ok
		reason = if ($ok) { 'ok' } elseif ($hasAppError) { 'application error page detected' } else { 'http error' }
	}
}

switch ($Action) {
	'bootstrap' {
		Copy-IfMissing -Source (Join-Path $root '.env.example') -Destination (Join-Path $root '.env')
		Copy-IfMissing -Source (Join-Path $root 'vms/.env.example') -Destination (Join-Path $root 'vms/.env')
		Copy-IfMissing -Source (Join-Path $root 'intra/.env.example') -Destination (Join-Path $root 'intra/.env')
		Write-Host 'bootstrap completed'
	}
	'start' {
		Write-Host "runtime: php $PhpRuntime"
		$args = @('up', '-d')
		if (-not $NoBuild) {
			$args += '--build'
		}
		Invoke-Compose -ComposeArgs $args
		Invoke-Compose -ComposeArgs @('ps')
	}
	'stop' {
		Invoke-Compose -ComposeArgs @('down')
	}
	'down' {
		Invoke-Compose -ComposeArgs @('down')
	}
	'restart' {
		Write-Host "runtime: php $PhpRuntime"
		Invoke-Compose -ComposeArgs @('down')
		$args = @('up', '-d')
		if (-not $NoBuild) {
			$args += '--build'
		}
		Invoke-Compose -ComposeArgs $args
		Invoke-Compose -ComposeArgs @('ps')
	}
	'reset' {
		Write-Host "runtime: php $PhpRuntime"
		Invoke-Compose -ComposeArgs @('down', '-v', '--remove-orphans')
		Invoke-Compose -ComposeArgs @('up', '-d', '--build')
		Invoke-Compose -ComposeArgs @('ps')
	}
	'status' {
		Invoke-Compose -ComposeArgs @('ps')
	}
	'logs' {
		if ($Service -ne '') {
			Invoke-Compose -ComposeArgs @('logs', '-f', $Service)
		} else {
			Invoke-Compose -ComposeArgs @('logs', '-f')
		}
	}
	'smoke' {
		$targets = @(
			'http://vms.localhost:8080/',
			'http://intra.localhost:8080/main/',
			'http://intra.localhost:8080/pengadaan/'
		)
		$results = foreach ($url in $targets) { Test-Endpoint -Url $url }
		$results | ForEach-Object {
			Write-Host "$($_.code) $($_.url) - $($_.reason)"
		}
		if (($results | Where-Object { -not $_.ok }).Count -gt 0) {
			throw 'smoke check failed'
		}
		Write-Host 'smoke check passed'
	}
	'doctor' {
		& docker version > $null
		if ($LASTEXITCODE -ne 0) {
			throw 'docker engine is not reachable'
		}
		Write-Host 'docker engine reachable'
		Write-Host "runtime target: php $PhpRuntime"
		$hostsPath = 'C:\Windows\System32\drivers\etc\hosts'
		if (Test-Path $hostsPath) {
			$hosts = Get-Content $hostsPath -ErrorAction SilentlyContinue
			$hasVms = ($hosts | Where-Object { $_ -match 'vms\.localhost' }).Count -gt 0
			$hasIntra = ($hosts | Where-Object { $_ -match 'intra\.localhost' }).Count -gt 0
			Write-Host "hosts vms.localhost entry: $hasVms"
			Write-Host "hosts intra.localhost entry: $hasIntra"
		}
	}
	'deps' {
		$phpCode = @'
$host = getenv("DB_HOSTNAME") ?: (getenv("DB_HOST") ?: "db");
$port = (int) (getenv("DB_PORT") ?: 3306);
$user = getenv("DB_USERNAME") ?: "eproc_app";
$pass = getenv("DB_PASSWORD") ?: "root";
$name = getenv("DB_DATABASE") ?: "eproc";

$db = @new mysqli($host, $user, $pass, $name, $port);
if ($db->connect_errno) {
	fwrite(STDERR, "DB_FAIL\n");
	exit(1);
}
$res = $db->query("SELECT 1 AS ok");
$row = $res ? $res->fetch_assoc() : null;
if (!isset($row["ok"]) || (int) $row["ok"] !== 1) {
	fwrite(STDERR, "DB_QUERY_FAIL\n");
	exit(1);
}
$db->close();

if (!class_exists("Redis")) {
	fwrite(STDERR, "REDIS_EXT_FAIL\n");
	exit(1);
}
$redis = new Redis();
if (!$redis->connect("redis", 6379, 2.0)) {
	fwrite(STDERR, "REDIS_CONN_FAIL\n");
	exit(1);
}
$pong = $redis->ping();
if ($pong !== "+PONG" && $pong !== true && strtoupper((string) $pong) !== "PONG") {
	fwrite(STDERR, "REDIS_PING_FAIL\n");
	exit(1);
}
$redis->close();

echo "PASS runtime dependency check\n";
'@
		Invoke-Compose -ComposeArgs @('exec', '-T', 'vms-app', 'php', '-r', $phpCode)
	}
	'cron' {
		$phpCode = @'
require "/var/www/html/vms/app/jobs/cron_core.php";
$cron = new cron();
$result = $cron->query("SELECT 1 AS ok");
$rows = $cron->result($result);

if (!isset($rows[0]["ok"]) || (int) $rows[0]["ok"] !== 1) {
	fwrite(STDERR, "CRON_FAIL\n");
	exit(1);
}

echo "PASS cron runtime check\n";
'@
		Invoke-Compose -ComposeArgs @('exec', '-T', 'vms-app', 'php', '-r', $phpCode)
	}
	default {
		throw "unknown action: $Action"
	}
}
