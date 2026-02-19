param(
	[ValidateSet('bootstrap', 'start', 'stop', 'down', 'restart', 'reset', 'status', 'logs', 'smoke', 'doctor')]
	[string] $Action = 'status',
	[string] $Service = '',
	[switch] $NoBuild
)

$ErrorActionPreference = 'Stop'

$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

function Invoke-Compose {
	param([string[]] $ComposeArgs)
	& docker compose @ComposeArgs
	if ($LASTEXITCODE -ne 0) {
		throw "docker compose $($ComposeArgs -join ' ') failed"
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
		Invoke-Compose -ComposeArgs @('down')
		$args = @('up', '-d')
		if (-not $NoBuild) {
			$args += '--build'
		}
		Invoke-Compose -ComposeArgs $args
		Invoke-Compose -ComposeArgs @('ps')
	}
	'reset' {
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
		$hostsPath = 'C:\Windows\System32\drivers\etc\hosts'
		if (Test-Path $hostsPath) {
			$hosts = Get-Content $hostsPath -ErrorAction SilentlyContinue
			$hasVms = ($hosts | Where-Object { $_ -match 'vms\.localhost' }).Count -gt 0
			$hasIntra = ($hosts | Where-Object { $_ -match 'intra\.localhost' }).Count -gt 0
			Write-Host "hosts vms.localhost entry: $hasVms"
			Write-Host "hosts intra.localhost entry: $hasIntra"
		}
	}
	default {
		throw "unknown action: $Action"
	}
}
