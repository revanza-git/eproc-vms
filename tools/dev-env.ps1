param(
	[ValidateSet('bootstrap', 'start', 'stop', 'down', 'restart', 'reset', 'status', 'logs', 'smoke', 'coexistence', 'coexistence-stage2', 'toggle-auction-subset', 'doctor', 'deps', 'cron', 'lint', 'test')]
	[string] $Action = 'status',
	[string] $Service = '',
	[ValidateSet('7.4', '8.2')]
	[string] $PhpRuntime = '7.4',
	[switch] $NoBuild,
	[ValidateSet('status', 'on', 'off')]
	[string] $ToggleMode = 'status',
	[string] $AuctionLelangId = '1'
)

$ErrorActionPreference = 'Stop'

$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

function Get-ComposeFiles {
	$composeFiles = @('-f', 'docker-compose.yml')
	if ($PhpRuntime -eq '8.2') {
		$composeFiles += @('-f', 'docker-compose.php82.yml')
	}

	return $composeFiles
}

function Invoke-Compose {
	param(
		[string[]] $ComposeArgs,
		[switch] $CaptureOutput
	)
	$composeFiles = Get-ComposeFiles

	if ($CaptureOutput) {
		$output = & docker compose @composeFiles @ComposeArgs 2>&1
		if ($LASTEXITCODE -ne 0) {
			throw "docker compose $($composeFiles -join ' ') $($ComposeArgs -join ' ') failed`n$($output -join "`n")"
		}
		return $output
	}

	& docker compose @composeFiles @ComposeArgs
	if ($LASTEXITCODE -ne 0) {
		throw "docker compose $($composeFiles -join ' ') $($ComposeArgs -join ' ') failed"
	}
}

function Get-CurlExecutable {
	$curlExe = Get-Command 'curl.exe' -ErrorAction SilentlyContinue
	if ($curlExe) {
		return $curlExe.Source
	}

	$curl = Get-Command 'curl' -CommandType Application -ErrorAction SilentlyContinue
	if ($curl) {
		return $curl.Source
	}

	throw 'curl executable is not available in PATH'
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

function Invoke-PhpScript {
	param([string] $ScriptPath)

	$fullPath = Join-Path $root $ScriptPath
	if (!(Test-Path $fullPath)) {
		throw "PHP script not found: $ScriptPath"
	}

	& php $fullPath
	if ($LASTEXITCODE -ne 0) {
		throw "php $ScriptPath failed"
	}
}

function Invoke-PhpLint {
	param([string] $FilePath)

	$fullPath = Join-Path $root $FilePath
	if (!(Test-Path $fullPath)) {
		throw "Lint target not found: $FilePath"
	}

	& php -l $fullPath
	if ($LASTEXITCODE -ne 0) {
		throw "php -l $FilePath failed"
	}
}

function Test-Endpoint {
	param(
		[string] $Url,
		[string] $HostHeader = ''
	)

	if (-not $script:CurlExecutable) {
		$script:CurlExecutable = Get-CurlExecutable
	}

	$curlArgs = @('-sS', '-L', '-w', "`n%{http_code}")
	if ($HostHeader -ne '') {
		$curlArgs += @('-H', "Host: $HostHeader")
	}
	$curlArgs += $Url

	$raw = & $script:CurlExecutable @curlArgs
	if ($LASTEXITCODE -ne 0) {
		return [pscustomobject]@{
			url = $Url
			host = $HostHeader
			code = 0
			ok = $false
			reason = 'curl failed'
		}
	}

	$parts = $raw -split "`n"
	$code = 0
	[void] [int]::TryParse($parts[-1], [ref] $code)
	$body = if ($parts.Length -gt 1) {
		($parts[0..($parts.Length - 2)] -join "`n")
	} else {
		''
	}
	$pattern = 'Fatal error|A PHP Error was encountered|An Error Was Encountered|The configuration file .* does not exist|Unable to connect to your database server'
	$hasAppError = $body -match $pattern
	$ok = ($code -lt 400) -and (-not $hasAppError)

	return [pscustomobject]@{
		url = $Url
		host = $HostHeader
		code = $code
		ok = $ok
		reason = if ($ok) { 'ok' } elseif ($hasAppError) { 'application error page detected' } else { 'http error' }
	}
}

function Test-EndpointHeader {
	param(
		[string] $Url,
		[string] $HostHeader = '',
		[string] $HeaderPattern
	)

	if (-not $script:CurlExecutable) {
		$script:CurlExecutable = Get-CurlExecutable
	}

	$curlArgs = @('-sS', '-D', '-', '-o', '-', '-w', "`n%{http_code}")
	if ($HostHeader -ne '') {
		$curlArgs += @('-H', "Host: $HostHeader")
	}
	$curlArgs += $Url

	$raw = & $script:CurlExecutable @curlArgs
	if ($LASTEXITCODE -ne 0) {
		return [pscustomobject]@{
			url = $Url
			host = $HostHeader
			code = 0
			ok = $false
			reason = 'curl failed'
			headerMatched = $false
		}
	}

	$parts = $raw -split "`n"
	$code = 0
	[void] [int]::TryParse($parts[-1], [ref] $code)
	$payload = if ($parts.Length -gt 1) {
		($parts[0..($parts.Length - 2)] -join "`n")
	} else {
		''
	}
	$headerMatched = $payload -match $HeaderPattern
	$ok = ($code -lt 400) -and $headerMatched

	return [pscustomobject]@{
		url = $Url
		host = $HostHeader
		code = $code
		ok = $ok
		reason = if ($ok) { 'ok' } elseif (-not $headerMatched) { 'missing header marker' } else { 'http error' }
		headerMatched = $headerMatched
	}
}

function Get-AuctionSubsetTogglePaths {
	$base = Join-Path $root 'docker/nginx'
	$templates = Join-Path $base 'templates'
	$includes = Join-Path $base 'includes'

	return [pscustomobject]@{
		active = Join-Path $includes 'pilot-auction-subset-toggle.active.conf'
		legacyTemplate = Join-Path $templates 'pilot-auction-subset-toggle.legacy.conf'
		pilotTemplate = Join-Path $templates 'pilot-auction-subset-toggle.pilot.conf'
	}
}

function Get-AuctionSubsetToggleState {
	$paths = Get-AuctionSubsetTogglePaths
	if (!(Test-Path $paths.active)) {
		return 'missing'
	}

	$content = Get-Content -Path $paths.active -Raw
	if ($content -match 'auction-json-provider=on') {
		return 'on'
	}
	if ($content -match 'auction-json-provider=off') {
		return 'off'
	}

	return 'unknown'
}

function Invoke-NginxReload {
	Invoke-Compose -ComposeArgs @('exec', '-T', 'webserver', 'nginx', '-t')
	Invoke-Compose -ComposeArgs @('exec', '-T', 'webserver', 'nginx', '-s', 'reload')
	Start-Sleep -Milliseconds 750
}

function Set-AuctionSubsetToggle {
	param(
		[ValidateSet('on', 'off')]
		[string] $Mode,
		[switch] $ReloadNginx
	)

	$paths = Get-AuctionSubsetTogglePaths
	if (!(Test-Path $paths.active)) {
		throw "active toggle include not found: $($paths.active)"
	}

	$template = if ($Mode -eq 'on') { $paths.pilotTemplate } else { $paths.legacyTemplate }
	if (!(Test-Path $template)) {
		throw "toggle template not found: $template"
	}

	Copy-Item -Path $template -Destination $paths.active -Force
	Write-Host "auction subset toggle file updated: $Mode -> $($paths.active)"

	if ($ReloadNginx) {
		Invoke-NginxReload
		Write-Host 'nginx reload completed'
	}
}

function Test-EndpointMarkers {
	param(
		[string] $Url,
		[string] $HostHeader,
		[string] $AppHeaderPattern,
		[string] $RouteHeaderPattern,
		[switch] $RequireHttpOk
	)

	$appCheck = Test-EndpointHeader -Url $Url -HostHeader $HostHeader -HeaderPattern $AppHeaderPattern
	$routeCheck = Test-EndpointHeader -Url $Url -HostHeader $HostHeader -HeaderPattern $RouteHeaderPattern
	$markerOk = ($appCheck.headerMatched -and $routeCheck.headerMatched)
	$httpOk = (($appCheck.code -lt 400) -and ($routeCheck.code -lt 400))
	$finalOk = if ($RequireHttpOk) { $markerOk -and $httpOk } else { $markerOk }

	return [pscustomobject]@{
		url = $Url
		host = $HostHeader
		code = $appCheck.code
		routeCode = $routeCheck.code
		ok = $finalOk
		appHeaderMatched = $appCheck.headerMatched
		routeHeaderMatched = $routeCheck.headerMatched
		httpOk = $httpOk
		reason = if ($finalOk -and (-not $RequireHttpOk) -and (-not $httpOk)) {
			'markers ok (HTTP non-2xx/3xx observed)'
		} elseif ($finalOk) {
			'ok'
		} elseif (-not $appCheck.headerMatched) {
			'missing app marker'
		} elseif (-not $routeCheck.headerMatched) {
			'missing route marker'
		} else {
			'http error'
		}
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
			@{ url = 'http://127.0.0.1:8080/'; host = 'vms.localhost' },
			@{ url = 'http://127.0.0.1:8080/main/'; host = 'intra.localhost' },
			@{ url = 'http://127.0.0.1:8080/pengadaan/'; host = 'intra.localhost' }
		)
		$results = foreach ($target in $targets) {
			Test-Endpoint -Url $target.url -HostHeader $target.host
		}
		$results | ForEach-Object {
			Write-Host "$($_.code) $($_.url) [Host: $($_.host)] - $($_.reason)"
		}
		if (($results | Where-Object { -not $_.ok }).Count -gt 0) {
			throw 'smoke check failed'
		}
		Write-Host 'smoke check passed'
	}
	'coexistence' {
		$legacyTargets = @(
			@{ url = 'http://127.0.0.1:8080/'; host = 'vms.localhost' },
			@{ url = 'http://127.0.0.1:8080/main/'; host = 'intra.localhost' },
			@{ url = 'http://127.0.0.1:8080/pengadaan/'; host = 'intra.localhost' }
		)
		$legacyResults = foreach ($target in $legacyTargets) {
			Test-Endpoint -Url $target.url -HostHeader $target.host
		}
		$pilotResult = Test-EndpointHeader `
			-Url 'http://127.0.0.1:8080/_pilot/auction/health' `
			-HostHeader 'vms.localhost' `
			-HeaderPattern 'X-App-Source:\s*pilot-skeleton'

		$legacyResults | ForEach-Object {
			Write-Host "CX-01 $($_.code) $($_.url) [Host: $($_.host)] - $($_.reason)"
		}
		Write-Host "CX-02 $($pilotResult.code) $($pilotResult.url) [Host: $($pilotResult.host)] - $($pilotResult.reason)"

		if (($legacyResults | Where-Object { -not $_.ok }).Count -gt 0) {
			throw 'coexistence check failed: CX-01 legacy smoke failed'
		}
		if (-not $pilotResult.ok) {
			throw 'coexistence check failed: CX-02 pilot shadow route failed'
		}

		Write-Host 'coexistence check passed (CX-01, CX-02)'
	}
	'toggle-auction-subset' {
		$paths = Get-AuctionSubsetTogglePaths
		$state = Get-AuctionSubsetToggleState

		switch ($ToggleMode) {
			'status' {
				Write-Host "auction subset toggle state: $state"
				Write-Host "active include: $($paths.active)"
			}
			'on' {
				Set-AuctionSubsetToggle -Mode 'on' -ReloadNginx
				Write-Host "auction subset toggle state: $(Get-AuctionSubsetToggleState)"
			}
			'off' {
				Set-AuctionSubsetToggle -Mode 'off' -ReloadNginx
				Write-Host "auction subset toggle state: $(Get-AuctionSubsetToggleState)"
			}
			default {
				throw "unsupported ToggleMode for toggle-auction-subset: $ToggleMode"
			}
		}
	}
	'coexistence-stage2' {
		$vmsHost = 'vms.localhost'
		$targets = @(
			@{
				key = 'get_barang'
				url = "http://127.0.0.1:8080/auction/admin/json_provider/get_barang/$AuctionLelangId"
			},
			@{
				key = 'get_peserta'
				url = "http://127.0.0.1:8080/auction/admin/json_provider/get_peserta/$AuctionLelangId"
			}
		)

		Set-AuctionSubsetToggle -Mode 'on' -ReloadNginx
		$cx03Results = foreach ($target in $targets) {
			Test-EndpointMarkers `
				-Url $target.url `
				-HostHeader $vmsHost `
				-AppHeaderPattern 'X-App-Source:\s*pilot-skeleton' `
				-RouteHeaderPattern 'X-Coexistence-Route:\s*pilot-business-toggle' `
				-RequireHttpOk
		}

		$cx03Results | ForEach-Object {
			Write-Host "CX-03 $($_.code) $($_.url) [Host: $($_.host)] - $($_.reason) (app=$($_.appHeaderMatched); route=$($_.routeHeaderMatched))"
		}

		if (($cx03Results | Where-Object { -not $_.ok }).Count -gt 0) {
			throw 'coexistence stage2 failed: CX-03 route toggle to pilot did not pass'
		}

		Set-AuctionSubsetToggle -Mode 'off' -ReloadNginx
		$cx04Results = foreach ($target in $targets) {
			Test-EndpointMarkers `
				-Url $target.url `
				-HostHeader $vmsHost `
				-AppHeaderPattern 'X-App-Source:\s*ci3-legacy' `
				-RouteHeaderPattern 'X-Coexistence-Route:\s*ci3-legacy-subset'
		}

		$cx04Results | ForEach-Object {
			Write-Host "CX-04 $($_.code) $($_.url) [Host: $($_.host)] - $($_.reason) (app=$($_.appHeaderMatched); route=$($_.routeHeaderMatched); httpOk=$($_.httpOk))"
		}

		if (($cx04Results | Where-Object { -not $_.ok }).Count -gt 0) {
			throw 'coexistence stage2 failed: CX-04 rollback to CI3 did not pass'
		}

		Write-Host 'coexistence stage2 check passed (CX-03, CX-04)'
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
	'lint' {
		Invoke-PhpScript -ScriptPath 'scripts/scan_secrets.php'
		Invoke-PhpScript -ScriptPath 'scripts/check_csrf_session_baseline.php'
		Invoke-PhpScript -ScriptPath 'scripts/check_query_safety.php'
		Invoke-PhpScript -ScriptPath 'scripts/check_php82_blockers.php'

		Invoke-PhpLint -FilePath 'vms/app/tests/test_bootstrap.php'
		Invoke-PhpLint -FilePath 'vms/app/application/tests/Smoke_test.php'

		Write-Host 'lint checks passed'
	}
	'test' {
		$output = Invoke-Compose -ComposeArgs @('exec', '-T', 'vms-app', 'sh', '-lc', 'cd /var/www/html/vms/app && php tests/test_bootstrap.php') -CaptureOutput
		$joined = $output -join "`n"
		$successPattern = 'CodeIgniter bootstrap completed successfully'
		$failurePattern = 'Fatal error|Parse Error|Exception:|Error:|❌'

		if ($joined -notmatch $successPattern) {
			throw "test bootstrap check failed: success marker was not found.`n$joined"
		}
		if ($joined -match $failurePattern) {
			throw "test bootstrap check failed: error marker detected.`n$joined"
		}

		Write-Host 'test bootstrap check passed'
	}
	default {
		throw "unknown action: $Action"
	}
}
