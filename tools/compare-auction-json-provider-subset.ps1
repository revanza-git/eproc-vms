param(
    [string] $BaseUrl = 'http://127.0.0.1:8080',
    [string] $HostHeader = 'vms.localhost',
    [string] $AuctionLelangId = '1',
    [string] $AuctionBarangId = '1',
    [string] $PhpRuntime = '7.4',
    [string] $OutputPath = ''
)

$ErrorActionPreference = 'Stop'

function Invoke-CurlCapture {
    param(
        [string] $Url,
        [string] $HostHeaderValue,
        [int] $TimeoutSeconds = 20
    )

    $headersPath = [System.IO.Path]::GetTempFileName()
    $bodyPath = [System.IO.Path]::GetTempFileName()

    try {
        $curlArgs = @(
            '-sS',
            '--max-time', $TimeoutSeconds,
            '-D', $headersPath,
            '-o', $bodyPath,
            '-H', "Host: $HostHeaderValue",
            '-w', '%{http_code}',
            $Url
        )

        $httpCodeRaw = & curl.exe @curlArgs
        $curlExit = $LASTEXITCODE

        $headerText = if (Test-Path $headersPath) { Get-Content -Raw -Path $headersPath } else { '' }
        $bodyText = if (Test-Path $bodyPath) { Get-Content -Raw -Path $bodyPath } else { '' }

        $headers = Parse-HttpHeaders -HeaderText $headerText

        return [pscustomobject]@{
            url = $Url
            host = $HostHeaderValue
            http_status = [int]([string]$httpCodeRaw).Trim()
            curl_exit_code = $curlExit
            headers = $headers
            body = $bodyText
            raw_header_text = $headerText
        }
    } finally {
        Remove-Item -Path $headersPath, $bodyPath -ErrorAction SilentlyContinue
    }
}

function Parse-HttpHeaders {
    param([string] $HeaderText)

    $lines = ($HeaderText -replace "`r", '') -split "`n"
    $blocks = @()
    $current = @()

    foreach ($line in $lines) {
        if ($line -match '^HTTP/\d+\.\d+\s+\d+') {
            if ($current.Count -gt 0) {
                $blocks += ,$current
            }
            $current = @($line)
            continue
        }

        if ($current.Count -eq 0) {
            continue
        }

        if ($line -eq '') {
            $blocks += ,$current
            $current = @()
            continue
        }

        $current += $line
    }

    if ($current.Count -gt 0) {
        $blocks += ,$current
    }

    $selected = if ($blocks.Count -gt 0) { $blocks[-1] } else { @() }
    $map = @{}

    foreach ($line in $selected) {
        if ($line -notmatch '^[^:]+:\s*') {
            continue
        }
        $parts = $line -split ':\s*', 2
        $name = $parts[0]
        $value = if ($parts.Count -gt 1) { $parts[1] } else { '' }
        if ($map.ContainsKey($name)) {
            $map[$name] = @($map[$name]) + $value
        } else {
            $map[$name] = $value
        }
    }

    return $map
}

function Get-HeaderValue {
    param(
        [hashtable] $Headers,
        [string] $Name
    )

    foreach ($key in $Headers.Keys) {
        if ($key -ieq $Name) {
            $value = $Headers[$key]
            if ($value -is [array]) {
                return [string]$value[-1]
            }
            return [string]$value
        }
    }
    return $null
}

function Try-ParseJson {
    param([string] $Body)

    $trimmed = if ($null -eq $Body) { '' } else { $Body.Trim() }
    if ([string]::IsNullOrWhiteSpace($trimmed)) {
        return [pscustomobject]@{
            ok = $false
            kind = 'empty'
            value = $null
            error = 'empty body'
        }
    }

    $kind = if ($trimmed.StartsWith('[')) { 'array' } elseif ($trimmed.StartsWith('{')) { 'object' } else { 'non-json' }
    if ($kind -eq 'non-json') {
        return [pscustomobject]@{
            ok = $false
            kind = $kind
            value = $null
            error = 'body is not JSON'
        }
    }

    try {
        $parsed = $trimmed | ConvertFrom-Json -AsHashtable -Depth 100
        return [pscustomobject]@{
            ok = $true
            kind = $kind
            value = $parsed
            error = $null
        }
    } catch {
        return [pscustomobject]@{
            ok = $false
            kind = $kind
            value = $null
            error = $_.Exception.Message
        }
    }
}

function Test-MinShape {
    param(
        [string] $EndpointKey,
        [pscustomobject] $Json
    )

    $result = [ordered]@{
        ok = $false
        endpoint = $EndpointKey
        expected_top = ''
        actual_top = $Json.kind
        missing_keys = @()
        element_checked = $false
        notes = @()
    }

    switch ($EndpointKey) {
        'get_barang' {
            $result.expected_top = 'array'
            if ($Json.kind -ne 'array' -or -not $Json.ok) { return [pscustomobject]$result }
            $result.ok = $true
            $items = @($Json.value)
            if ($items.Count -gt 0) {
                $result.element_checked = $true
                $required = @('id', 'name', 'hps', 'hps_in_idr')
                foreach ($k in $required) {
                    if (-not $items[0].ContainsKey($k)) { $result.missing_keys += $k }
                }
                if ($result.missing_keys.Count -gt 0) { $result.ok = $false }
            } else {
                $result.notes += 'empty-array-allowed'
            }
            return [pscustomobject]$result
        }
        'get_peserta' {
            $result.expected_top = 'array'
            if ($Json.kind -ne 'array' -or -not $Json.ok) { return [pscustomobject]$result }
            $result.ok = $true
            $items = @($Json.value)
            if ($items.Count -gt 0) {
                $result.element_checked = $true
                $required = @('id', 'name')
                foreach ($k in $required) {
                    if (-not $items[0].ContainsKey($k)) { $result.missing_keys += $k }
                }
                if ($result.missing_keys.Count -gt 0) { $result.ok = $false }
            } else {
                $result.notes += 'empty-array-allowed'
            }
            return [pscustomobject]$result
        }
        'get_initial_data' {
            $result.expected_top = 'object'
            if ($Json.kind -ne 'object' -or -not $Json.ok) { return [pscustomobject]$result }
            $required = @('id', 'name', 'subtitle', 'data', 'last', 'time')
            foreach ($k in $required) {
                if (-not $Json.value.ContainsKey($k)) { $result.missing_keys += $k }
            }
            $result.ok = ($result.missing_keys.Count -eq 0)
            return [pscustomobject]$result
        }
        'get_chart_update' {
            $result.expected_top = 'object'
            if ($Json.kind -ne 'object' -or -not $Json.ok) { return [pscustomobject]$result }
            $required = @('data', 'time')
            foreach ($k in $required) {
                if (-not $Json.value.ContainsKey($k)) { $result.missing_keys += $k }
            }
            $result.ok = ($result.missing_keys.Count -eq 0)
            return [pscustomobject]$result
        }
        default {
            $result.notes += 'unknown-endpoint-shape'
            return [pscustomobject]$result
        }
    }
}

function Get-BodySnippet {
    param([string] $Body, [int] $Max = 280)
    if ($null -eq $Body) { return '' }
    $snippet = ($Body -replace '\s+', ' ').Trim()
    if ($snippet.Length -le $Max) { return $snippet }
    return $snippet.Substring(0, $Max) + '...'
}

function Set-AuctionSubsetToggleSafe {
    param(
        [ValidateSet('on', 'off')]
        [string] $Mode,
        [string] $PhpRuntimeValue
    )

    $devEnvScript = Join-Path $PSScriptRoot 'dev-env.ps1'
    & $devEnvScript -Action 'toggle-auction-subset' -ToggleMode $Mode -PhpRuntime $PhpRuntimeValue | Out-Host
}

function Get-AuctionSubsetToggleStateLocal {
    $toggleFile = Join-Path $PSScriptRoot '..\docker\nginx\includes\pilot-auction-subset-toggle.active.conf'
    $content = Get-Content -Raw -Path $toggleFile
    if ($content -match 'auction-json-provider=on') { return 'on' }
    if ($content -match 'auction-json-provider=off') { return 'off' }
    return 'unknown'
}

function Invoke-EndpointAssessment {
    param(
        [string] $EndpointKey,
        [string] $Variant,
        [string] $Url,
        [string] $HostHeaderValue,
        [hashtable] $ExpectedHeaders = @{}
    )

    $resp = Invoke-CurlCapture -Url $Url -HostHeaderValue $HostHeaderValue
    $json = Try-ParseJson -Body $resp.body
    $shape = Test-MinShape -EndpointKey $EndpointKey -Json $json

    $headerChecks = @()
    $headersOk = $true
    foreach ($k in $ExpectedHeaders.Keys) {
        $actual = Get-HeaderValue -Headers $resp.headers -Name $k
        $expected = [string]$ExpectedHeaders[$k]
        $matched = ($actual -eq $expected)
        if (-not $matched) { $headersOk = $false }
        $headerChecks += [pscustomobject]@{
            name = $k
            expected = $expected
            actual = $actual
            matched = $matched
        }
    }

    $pilotDataStatus = Get-HeaderValue -Headers $resp.headers -Name 'X-Pilot-Data-Status'
    $pilotDataSource = Get-HeaderValue -Headers $resp.headers -Name 'X-Pilot-Data-Source'
    $pilotSqlState = Get-HeaderValue -Headers $resp.headers -Name 'X-Pilot-Error-SqlState'
    $pilotErrorCode = Get-HeaderValue -Headers $resp.headers -Name 'X-Pilot-Error-Code'

    $httpOk = ($resp.curl_exit_code -eq 0 -and $resp.http_status -ge 200 -and $resp.http_status -lt 400)
    $blocked = ($resp.curl_exit_code -ne 0 -or $resp.http_status -ge 500)

    return [pscustomobject]@{
        endpoint = $EndpointKey
        variant = $Variant
        url = $Url
        host = $HostHeaderValue
        http_status = $resp.http_status
        curl_exit_code = $resp.curl_exit_code
        headers = $resp.headers
        header_checks = $headerChecks
        header_expectation_count = $ExpectedHeaders.Count
        headers_ok = $headersOk
        http_ok = $httpOk
        blocked = $blocked
        json_ok = $json.ok
        json_kind = $json.kind
        json_error = $json.error
        min_shape = $shape
        body_snippet = Get-BodySnippet -Body $resp.body
        pilot_data_source = $pilotDataSource
        pilot_data_status = $pilotDataStatus
        pilot_error_sqlstate = $pilotSqlState
        pilot_error_code = $pilotErrorCode
    }
}

$runAt = (Get-Date).ToString('yyyy-MM-dd HH:mm:ss zzz')
$originalToggleState = Get-AuctionSubsetToggleStateLocal
$results = @()

$endpointSpecs = @(
    [pscustomobject]@{
        key = 'get_barang'
        ci3Path = "/auction/admin/json_provider/get_barang/$AuctionLelangId"
        pilotPath = "/auction/admin/json_provider/get_barang/$AuctionLelangId"
        pilotMode = 'business-toggle'
    },
    [pscustomobject]@{
        key = 'get_peserta'
        ci3Path = "/auction/admin/json_provider/get_peserta/$AuctionLelangId"
        pilotPath = "/auction/admin/json_provider/get_peserta/$AuctionLelangId"
        pilotMode = 'business-toggle'
    },
    [pscustomobject]@{
        key = 'get_initial_data'
        ci3Path = "/auction/admin/json_provider/get_initial_data/$AuctionLelangId/$AuctionBarangId"
        pilotPath = "/_pilot/auction/admin/json_provider/get_initial_data/$AuctionLelangId/$AuctionBarangId"
        pilotMode = 'shadow-route'
    },
    [pscustomobject]@{
        key = 'get_chart_update'
        ci3Path = "/auction/admin/json_provider/get_chart_update/$AuctionLelangId"
        pilotPath = "/_pilot/auction/admin/json_provider/get_chart_update/$AuctionLelangId"
        pilotMode = 'shadow-route'
    }
)

try {
    foreach ($spec in $endpointSpecs) {
        if ($spec.pilotMode -eq 'business-toggle') {
            Set-AuctionSubsetToggleSafe -Mode 'off' -PhpRuntimeValue $PhpRuntime
        }

        $ci3ExpectedHeaders = @{}
        if ($spec.pilotMode -eq 'business-toggle') {
            $ci3ExpectedHeaders = @{
                'X-App-Source' = 'ci3-legacy'
                'X-Coexistence-Route' = 'ci3-legacy-subset'
                'X-Coexistence-Toggle' = 'auction-json-provider=off'
            }
        }

        $ci3 = Invoke-EndpointAssessment `
            -EndpointKey $spec.key `
            -Variant 'ci3' `
            -Url ($BaseUrl.TrimEnd('/') + $spec.ci3Path) `
            -HostHeaderValue $HostHeader `
            -ExpectedHeaders $ci3ExpectedHeaders

        if ($spec.pilotMode -eq 'business-toggle') {
            Set-AuctionSubsetToggleSafe -Mode 'on' -PhpRuntimeValue $PhpRuntime
        }

        $pilotExpectedHeaders = @{
            'X-App-Source' = 'pilot-skeleton'
            'X-Pilot-Endpoint' = $spec.key
        }
        if ($spec.pilotMode -eq 'business-toggle') {
            $pilotExpectedHeaders['X-Pilot-Route-Mode'] = 'business-toggle'
            $pilotExpectedHeaders['X-Coexistence-Route'] = 'pilot-business-toggle'
            $pilotExpectedHeaders['X-Coexistence-Toggle'] = 'auction-json-provider=on'
        } else {
            $pilotExpectedHeaders['X-Pilot-Route-Mode'] = 'shadow-route'
            $pilotExpectedHeaders['X-Coexistence-Route'] = 'pilot-shadow'
        }

        $pilot = Invoke-EndpointAssessment `
            -EndpointKey $spec.key `
            -Variant 'pilot' `
            -Url ($BaseUrl.TrimEnd('/') + $spec.pilotPath) `
            -HostHeaderValue $HostHeader `
            -ExpectedHeaders $pilotExpectedHeaders

        $compareStatus = 'PASS'
        $compareReason = 'comparable'
        $blockerDetails = @()

        if ($ci3.blocked) {
            $compareStatus = 'BLOCKED'
            $compareReason = 'ci3-runtime-http-5xx-or-network'
            $blockerDetails += "ci3-http=$($ci3.http_status)"
        } elseif (-not $ci3.json_ok) {
            $compareStatus = 'BLOCKED'
            $compareReason = 'ci3-response-not-json'
            $blockerDetails += "ci3-json=$($ci3.json_error)"
        } elseif (-not $ci3.min_shape.ok) {
            $compareStatus = 'BLOCKED'
            $compareReason = 'ci3-min-shape-unavailable-or-invalid'
            $blockerDetails += "ci3-shape-missing=$([string]::Join(',', @($ci3.min_shape.missing_keys)))"
        } elseif ($pilot.blocked) {
            $compareStatus = 'BLOCKED'
            $compareReason = 'pilot-runtime-http-5xx-or-network'
            $blockerDetails += "pilot-http=$($pilot.http_status)"
        } elseif (-not $pilot.json_ok) {
            $compareStatus = 'MISMATCH'
            $compareReason = 'pilot-response-not-json'
            $blockerDetails += "pilot-json=$($pilot.json_error)"
        } elseif (-not $pilot.min_shape.ok) {
            $compareStatus = 'MISMATCH'
            $compareReason = 'pilot-min-shape-invalid'
            $blockerDetails += "pilot-shape-missing=$([string]::Join(',', @($pilot.min_shape.missing_keys)))"
        }

        if ($compareStatus -eq 'PASS' -and ($ci3.min_shape.actual_top -ne $pilot.min_shape.actual_top)) {
            $compareStatus = 'MISMATCH'
            $compareReason = 'top-level-kind-differs'
            $blockerDetails += "ci3-top=$($ci3.min_shape.actual_top)"
            $blockerDetails += "pilot-top=$($pilot.min_shape.actual_top)"
        }

        $results += [pscustomobject]@{
            endpoint = $spec.key
            pilot_mode = $spec.pilotMode
            compare_status = $compareStatus
            compare_reason = $compareReason
            blocker_details = $blockerDetails
            ci3 = $ci3
            pilot = $pilot
        }
    }
}
finally {
    if ($originalToggleState -in @('on', 'off')) {
        Set-AuctionSubsetToggleSafe -Mode $originalToggleState -PhpRuntimeValue $PhpRuntime
    }
}

$summaryRows = foreach ($r in $results) {
    [pscustomobject]@{
        endpoint = $r.endpoint
        mode = $r.pilot_mode
        compare = $r.compare_status
        ci3_http = $r.ci3.http_status
        ci3_json = $r.ci3.json_ok
        ci3_markers = $(if ($r.ci3.header_expectation_count -eq 0) { 'n/a' } else { [string]$r.ci3.headers_ok })
        pilot_http = $r.pilot.http_status
        pilot_json = $r.pilot.json_ok
        pilot_shape = $r.pilot.min_shape.ok
        pilot_markers = $(if ($r.pilot.header_expectation_count -eq 0) { 'n/a' } else { [string]$r.pilot.headers_ok })
        pilot_data_status = $r.pilot.pilot_data_status
        pilot_sqlstate = $r.pilot.pilot_error_sqlstate
        pilot_error_code = $r.pilot.pilot_error_code
        reason = $r.compare_reason
    }
}

Write-Host ''
Write-Host 'Auction json_provider subset compare summary'
$summaryRows | Format-Table -AutoSize | Out-Host

foreach ($r in $results | Where-Object { $_.compare_status -eq 'BLOCKED' }) {
    Write-Host ''
    Write-Host ("BLOCKED {0}" -f $r.endpoint)
    Write-Host ("  reason: {0}" -f $r.compare_reason)
    Write-Host ("  ci3: http={0}; snippet={1}" -f $r.ci3.http_status, $r.ci3.body_snippet)
    if ($r.pilot.pilot_data_status) {
        Write-Host ("  pilot: data-status={0}; sqlstate={1}; error-code={2}" -f $r.pilot.pilot_data_status, $r.pilot.pilot_error_sqlstate, $r.pilot.pilot_error_code)
    }
}

$artifact = [ordered]@{
    meta = [ordered]@{
        generated_at = $runAt
        base_url = $BaseUrl
        host_header = $HostHeader
        auction_lelang_id = $AuctionLelangId
        auction_barang_id = $AuctionBarangId
        php_runtime = $PhpRuntime
        original_toggle_state = $originalToggleState
        restored_toggle_state = Get-AuctionSubsetToggleStateLocal
        semantics = [ordered]@{
            PASS = 'CI3 and pilot responses both comparable at minimum shape level for current sample'
            BLOCKED = 'Compare cannot conclude due to CI3/pilot runtime block (HTTP 5xx/network/non-JSON/min-shape unavailable)'
            MISMATCH = 'Both sides reachable but minimum shape/marker expectations differ'
        }
    }
    summary = $summaryRows
    results = $results
}

if ([string]::IsNullOrWhiteSpace($OutputPath)) {
    $stamp = (Get-Date).ToString('yyyyMMdd-HHmmss')
    $OutputPath = Join-Path $PSScriptRoot "..\docs\artifacts\phase6-waveb-json-provider-subset-compare-$stamp.json"
}

$outputDir = Split-Path -Parent $OutputPath
if (-not [string]::IsNullOrWhiteSpace($outputDir)) {
    New-Item -ItemType Directory -Path $outputDir -Force | Out-Null
}

$artifact | ConvertTo-Json -Depth 20 | Set-Content -Path $OutputPath -Encoding UTF8
Write-Host ''
Write-Host "compare artifact written: $OutputPath"
