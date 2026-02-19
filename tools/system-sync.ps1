param(
	[ValidateSet('vms', 'intra-main', 'intra-pengadaan')]
	[string] $Canonical = 'intra-main',
	[switch] $Apply
)

$ErrorActionPreference = 'Stop'

$root = Split-Path -Parent $PSScriptRoot
$systems = @{
	'vms'            = Join-Path $root 'vms\app\system'
	'intra-main'     = Join-Path $root 'intra\main\system'
	'intra-pengadaan'= Join-Path $root 'intra\pengadaan\system'
}

foreach ($k in $systems.Keys) {
	if (!(Test-Path $systems[$k])) {
		throw "Missing system directory: $k at $($systems[$k])"
	}
}

function Get-FileMap([string] $baseDir) {
	$map = @{}
	Get-ChildItem -Path $baseDir -Recurse -File | ForEach-Object {
		$rel = $_.FullName.Substring($baseDir.Length).TrimStart('\','/')
		$map[$rel] = $_.FullName
	}
	return $map
}

function Get-HashMap([hashtable] $fileMap) {
	$hashes = @{}
	foreach ($rel in $fileMap.Keys) {
		$hashes[$rel] = (Get-FileHash -Algorithm SHA256 -Path $fileMap[$rel]).Hash
	}
	return $hashes
}

$fileMaps = @{}
$hashMaps = @{}
foreach ($name in $systems.Keys) {
	$fileMaps[$name] = Get-FileMap $systems[$name]
	$hashMaps[$name] = Get-HashMap $fileMaps[$name]
}

$allFiles = New-Object System.Collections.Generic.HashSet[string]
foreach ($name in $systems.Keys) {
	foreach ($rel in $fileMaps[$name].Keys) {
		[void] $allFiles.Add($rel)
	}
}

$canonicalRoot = $systems[$Canonical]
$targets = $systems.Keys | Where-Object { $_ -ne $Canonical }

$missing = @{}
$diff = @{}
foreach ($t in $targets) {
	$missing[$t] = @()
	$diff[$t] = @()
}

foreach ($rel in $allFiles) {
	foreach ($t in $targets) {
		$cHas = $hashMaps[$Canonical].ContainsKey($rel)
		$tHas = $hashMaps[$t].ContainsKey($rel)
		if ($cHas -and !$tHas) {
			$missing[$t] += $rel
			continue
		}
		if ($cHas -and $tHas -and ($hashMaps[$Canonical][$rel] -ne $hashMaps[$t][$rel])) {
			$diff[$t] += $rel
		}
	}
}

Write-Output "Canonical=$Canonical"
foreach ($name in $systems.Keys) {
	Write-Output "$name files=$($fileMaps[$name].Count)"
}
foreach ($t in $targets) {
	Write-Output "$t missing_vs_canonical=$($missing[$t].Count) different_vs_canonical=$($diff[$t].Count)"
}

if ($Apply) {
	foreach ($t in $targets) {
		$targetRoot = $systems[$t]
		$toCopy = @($missing[$t] + $diff[$t]) | Sort-Object -Unique
		foreach ($rel in $toCopy) {
			if (!$fileMaps[$Canonical].ContainsKey($rel)) {
				continue
			}
			$src = $fileMaps[$Canonical][$rel]
			$dst = Join-Path $targetRoot $rel
			$dstDir = Split-Path -Parent $dst
			if (!(Test-Path $dstDir)) {
				New-Item -ItemType Directory -Path $dstDir | Out-Null
			}
			Copy-Item -Force -Path $src -Destination $dst
		}
		Write-Output "Applied to $t files=$($toCopy.Count)"
	}
}

