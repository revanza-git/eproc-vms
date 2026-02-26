# Phase 6 Coexistence Dev Baseline (Wave A -> Wave B Stage 1/2)

## Metadata
- Created: `February 25, 2026`
- Last Updated: `February 26, 2026` (Wave B Stage 2 stable + sibling bind-mount hook for final Laravel skeleton)
- Status: `Stage 2 Implemented for scoped route toggle (get_barang/get_peserta) + rollback switch in dev; sibling bind-mount hook ready for final Laravel skeleton; CX-05 auth bridge pending`

## Purpose
Menetapkan baseline coexistence CI3 + app baru di environment dev, mulai dari artefak desain Wave A hingga proof Wave B Stage 1/2 (shadow route + scoped toggle) dan hook integrasi skeleton Laravel final via sibling bind mount.

## Current State (Evidence-Based, After Wave B Stage 2)
- Service placeholder `pilot-app` sudah ditambahkan ke `docker-compose.yml` untuk proof coexistence dev.
- Service `pilot-app` kini memakai bind mount path yang configurable via `.env` (`EPROC_PILOT_APP_BIND_PATH`) dengan fallback default `./pilot-app` (workflow existing tetap jalan).
- Route shadow `/_pilot/auction/*` sudah diarahkan ke `pilot-app` pada host `vms.localhost`.
- Helper smoke coexistence (`CX-01`, `CX-02`) sudah tersedia via `tools/dev-env.ps1 -Action coexistence`.
- Route toggle subset endpoint bisnis `auction/admin/json_provider/{get_barang|get_peserta}` sudah diimplementasikan via Nginx include file aktif (`ON/OFF`) + `nginx -s reload` (tanpa full restart stack).
- Helper dev untuk toggle + validasi Stage 2 tersedia via `tools/dev-env.ps1` (`toggle-auction-subset`, `coexistence-stage2`).
- `CX-03` dan `CX-04` sudah tervalidasi untuk aspek routing/rollback marker (pilot `ON`, CI3 `OFF`).
- CI3 response pada sample `id_lelang=1` saat toggle `OFF` terobservasi `HTTP 500` karena tabel dev sample tidak tersedia (`ms_procurement_barang`, `ms_procurement_peserta`); hal ini tidak menghalangi verifikasi rollback route marker.
- Inspeksi sibling path lokal (`C:\Users\Revanza-Home\source\repos`) belum menemukan repo Laravel final; integrasi dev saat ini masih `hook-ready`, belum terhubung ke repo final aktual.
- Auth bridge untuk endpoint protected pilot belum diimplementasikan (pending `CX-05`).

## Wave A Decision (Baseline)
- **Tidak** memaksakan perubahan runtime/compose/Nginx yang berpotensi mematahkan stack existing tanpa adanya app pilot nyata.
- Menyediakan artefak desain + checklist implementasi + acceptance test plan sebagai prasyarat Wave B.

## Wave B Stage 1 Execution (Implemented)
- Menambahkan skeleton dev `pilot-app` (placeholder non-bisnis) untuk endpoint health.
- Menambahkan route shadow dev-only `/_pilot/auction/*` -> `pilot-app`.
- Menambahkan action `coexistence` untuk memverifikasi legacy route tetap sehat (`CX-01`) + shadow route pilot (`CX-02`).
- Menjaga route bisnis CI3 tetap default (belum ada toggle/cutover subset `/auction/*`).

## Wave B Stage 2 Execution (Implemented for Scoped Endpoints)
- Menambahkan toggle route subset dev-only untuk:
  - `/auction/admin/json_provider/get_barang/*`
  - `/auction/admin/json_provider/get_peserta/*`
- Toggle disimpan di file include aktif Nginx dan dapat di-switch `ON/OFF` via helper PowerShell tanpa restart penuh stack (`nginx -s reload`).
- Menambahkan marker header route/source untuk membedakan CI3 vs `pilot-app` saat toggle berubah.
- Menambahkan stub response pilot untuk dua endpoint subset agar validasi route split (`CX-03`) dapat diverifikasi konsisten.
- Menambahkan validasi helper `coexistence-stage2` untuk `CX-03` + `CX-04`.

## Post-Stage 2 Dev Hook (Implemented, Awaiting Actual Sibling Repo)
- `pilot-app` bind mount path dibuat configurable via `.env` (`EPROC_PILOT_APP_BIND_PATH`) dengan fallback `./pilot-app`.
- Tujuan: mengganti placeholder ke skeleton Laravel final repo terpisah tanpa mengubah routing shadow/toggle/Nginx yang sudah proven.
- Implementasi memakai bind long-form di compose agar path absolut Windows (drive letter) aman untuk skenario repo sibling.
- Repo Laravel final sibling belum tersedia/teridentifikasi di mesin ini saat eksekusi, sehingga verifikasi runtime dilakukan dengan fallback placeholder (`./pilot-app`).

## Target Coexistence Topology (Wave B Implementation Target)
```text
browser/client
  -> nginx (routing layer)
      -> CI3 vms-app / intra-app (default path legacy)
      -> pilot-app (placeholder now, Laravel final later) untuk endpoint pilot auction read-only
  -> shared services: db, redis
```

## Routing Strategy Baseline

### Stage 1 (Smoke/Shadow Path in Dev)
- Prefixed route dev-only untuk pilot:
  - `/_pilot/auction/...` -> `pilot-app`
- Tujuan:
  - validasi service wiring, healthcheck, marker header, dan contract/integration harness tanpa mengganggu route legacy.

### Stage 2 (Strangler Toggle for Pilot Paths)
- Route subset endpoint `auction` yang disetujui dipindahkan dari CI3 ke `pilot-app`.
- Routing harus mendukung toggle cepat kembali ke CI3 untuk rollback.

### Route Ownership Rule
- Default fallback tetap ke CI3 sampai endpoint masuk daftar pilot-approved.
- Route split dilakukan per-path, bukan per-host penuh, agar blast radius kecil.

## Compose/Nginx Implementation Checklist (Wave B)
| Item | Status | Target File | Acceptance Condition |
|---|---|---|---|
| Tambah service `pilot-app` (nama final boleh berbeda) | Implemented (placeholder skeleton) | `docker-compose.yml` (+ override bila perlu) | Container start healthy dan bisa diakses Nginx upstream |
| Tambah runtime override jika pilot pakai image/runtime berbeda | Partial (placeholder masih pakai local PHP-FPM image existing) | `docker-compose.php82.yml` | Override final untuk Laravel runtime masih menunggu skeleton final |
| Tambah Nginx upstream/route split untuk pilot path | Implemented (shadow + scoped toggle) | `docker/nginx/default.conf`, `docker/nginx/includes/*`, `docker/nginx/templates/*` | `/_pilot/auction/*` shadow aktif + subset `get_barang/get_peserta` bisa switch `ON/OFF` |
| Tambah helper command readiness/coexistence smoke | Implemented (`coexistence`, `toggle-auction-subset`, `coexistence-stage2`) | `tools/dev-env.ps1` | Command menghasilkan PASS/FAIL jelas untuk CX-01..CX-04 (routing scope) |
| Siapkan hook integrasi skeleton Laravel final via sibling bind mount | Implemented (env-driven bind path, fallback placeholder) | `docker-compose.yml`, `.env`, `.env.example` | `pilot-app` source path bisa diarahkan ke repo sibling tanpa ubah route/toggle/Nginx Stage 2 |
| Dokumentasi langkah start/verify coexistence | Implemented (Wave B Stage 1 + Stage 2) | `docs/DEV_ENV_RUNBOOK.md` | Runbook punya langkah repeatable untuk shadow-route smoke dan toggle rollback |

## Acceptance Test Plan (Wave B)
| Test ID | Goal | Steps (Ringkas) | Expected Result | Evidence | Status |
|---|---|---|---|---|---|
| CX-01 | Legacy route tetap berfungsi setelah penambahan pilot service | Start stack, hit `vms`/`main`/`pengadaan` smoke | HTTP 200 + tanpa error pattern | `tools/dev-env.ps1 -Action coexistence` output | PASS (2026-02-25) |
| CX-02 | Pilot shadow route tersambung ke `pilot-app` | Hit `/_pilot/auction/health` | HTTP 200 dari app baru + header marker app baru | `tools/dev-env.ps1 -Action coexistence` + `curl -D -` header dump | PASS (2026-02-25) |
| CX-03 | Route split subset pilot berfungsi | Enable toggle route pilot, hit endpoint subset (`get_barang`, `get_peserta`) | Response berasal dari `pilot-app` + marker header pilot/toggle | `tools/dev-env.ps1 -Action coexistence-stage2` + curl header dump | PASS (2026-02-26) |
| CX-04 | Rollback route cepat ke CI3 | Disable toggle, hit endpoint subset yang sama | Marker route/source kembali ke CI3 tanpa restart penuh stack (Nginx reload only) | `tools/dev-env.ps1 -Action coexistence-stage2` + curl header dump ON/OFF | PASS (routing marker, 2026-02-26; HTTP CI3 sample=500 due missing dev tables) |
| CX-05 | Auth bridge flow untuk endpoint protected pilot | Login -> obtain bridge token -> call pilot endpoint | 200 untuk token valid, 401/403 untuk token invalid/expired | Integration test log | Pending |

## Wave B Stage 1/2 Evidence (Executed)
| Date | Command | Result | Notes |
|---|---|---|---|
| 2026-02-25 | `docker compose -f docker-compose.yml config` | PASS | Compose config valid setelah tambah `pilot-app` |
| 2026-02-25 | `docker compose -f docker-compose.yml -f docker-compose.php82.yml config` | PASS | Override compose tetap valid |
| 2026-02-25 | `php -l pilot-app/public/index.php` | PASS | Skeleton endpoint health syntax valid |
| 2026-02-25 | `pwsh ./tools/dev-env.ps1 -Action start -PhpRuntime 7.4 -NoBuild` | PASS (setelah retry) | Attempt pertama gagal transient Docker daemon `EOF`; retry sukses |
| 2026-02-25 | `pwsh ./tools/dev-env.ps1 -Action coexistence -PhpRuntime 7.4` | PASS | `CX-01` + `CX-02` pass |
| 2026-02-25 | `curl.exe -sS -D - -o NUL -H "Host: vms.localhost" http://127.0.0.1:8080/_pilot/auction/health` | PASS | Header marker `X-App-Source: pilot-skeleton` + `X-Coexistence-Route: pilot-shadow` terlihat |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action coexistence-stage2 -PhpRuntime 7.4 -AuctionLelangId 1` | PASS | `CX-03` (pilot ON) = HTTP 200 + marker PASS untuk `get_barang/get_peserta`; `CX-04` (rollback OFF) = marker CI3 PASS untuk kedua endpoint, HTTP CI3 sample `500` (DB seed gap) |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action toggle-auction-subset -ToggleMode on/off -PhpRuntime 7.4` | PASS | Toggle file aktif + `nginx -t` + `nginx -s reload` dieksekusi tanpa full restart stack |
| 2026-02-26 | `curl.exe -sS -D - -o NUL -H "Host: vms.localhost" http://127.0.0.1:8080/auction/admin/json_provider/get_barang/1` (toggle `ON`) | PASS | Header: `X-App-Source: pilot-skeleton`, `X-Coexistence-Route: pilot-business-toggle`, `X-Coexistence-Toggle: auction-json-provider=on` |
| 2026-02-26 | `curl.exe -sS -D - -o NUL -H "Host: vms.localhost" http://127.0.0.1:8080/auction/admin/json_provider/get_barang/1` (toggle `OFF`) | PASS (routing marker) | Header: `X-App-Source: ci3-legacy`, `X-Coexistence-Route: ci3-legacy-subset`, `X-Coexistence-Toggle: auction-json-provider=off`; status `500` (Database Error di dev sample data) |
| 2026-02-26 | `docker compose -f docker-compose.yml config` | PASS | Post-hook check: `pilot-app` volume bind resolved ke `.env` default `EPROC_PILOT_APP_BIND_PATH=./pilot-app` |
| 2026-02-26 | `$env:EPROC_PILOT_APP_BIND_PATH='../eproc-vms-laravel-final'; docker compose -f docker-compose.yml config` | PASS | Non-destructive sample override: `pilot-app` bind source ter-resolve ke path sibling contoh (hook interpolation proven, repo aktual belum ada) |
| 2026-02-26 | `docker compose -f docker-compose.yml -f docker-compose.php82.yml config` | PASS | Post-hook check: override PHP 8.2 tetap valid |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action start -PhpRuntime 7.4 -NoBuild` | PASS | Post-hook check: stack healthy tanpa build ulang; Stage 2 toggle tetap tersedia |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action coexistence -PhpRuntime 7.4` | PASS | Post-hook smoke `CX-01` + `CX-02` tetap valid |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action coexistence-stage2 -PhpRuntime 7.4 -AuctionLelangId 1` | PASS | Post-hook smoke `CX-03` + `CX-04` tetap valid; `CX-04` marker PASS dengan HTTP `500` CI3 (known dev table gap) |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action stop -PhpRuntime 7.4` | PASS | Cleanup stack setelah verifikasi post-hook |
| 2026-02-25 | `pwsh ./tools/dev-env.ps1 -Action stop -PhpRuntime 7.4` | PASS | Cleanup stack setelah verifikasi |

## Implementation Notes for Wave B
- Nama service final (`pilot-app`, `laravel-pilot`, dll.) diputuskan saat skeleton Laravel final tersedia.
- Saat ini placeholder skeleton ditempatkan di repo ini (`pilot-app/`) untuk proof Stage 1; keputusan final repo placement dirujuk ke `DR-P6-005`.
- Jika skeleton/Laravel final berada di repo lain, gunakan volume/proxy strategy yang tidak mengubah path CI3 existing.
- Hook dev yang diterapkan sekarang: `docker-compose.yml` memakai bind mount configurable `EPROC_PILOT_APP_BIND_PATH` (default `./pilot-app`), sehingga repo sibling bisa dipasang tanpa mengubah route/toggle coexistence.
- Volume `pilot-app` diubah ke syntax bind long-form agar path absolut Windows (drive letter) tetap aman saat diarahkan ke repo sibling (`C:\...`).
- Tambahkan marker response/header pada app pilot dev untuk memudahkan smoke verification (`X-App-Source`).
- Implementasi Stage 2 memakai file include aktif Nginx (`docker/nginx/includes/pilot-auction-subset-toggle.active.conf`) yang diisi dari template `legacy/pilot`; rollback tercepat = switch file + `nginx -s reload`.

## Closed Questions (Resolved Decisions, February 26, 2026)
1. Placeholder `pilot-app` diganti ke skeleton Laravel final: `Ya, secepatnya`.
2. Jika repo final terpisah: strategi dev yang dipilih = `bind mount sibling`.
3. `CX-03/CX-04` tidak memerlukan `id_lelang` seeded khusus untuk lanjut tahap ini.
4. Tidak perlu command seed minimal untuk `ms_procurement_barang` / `ms_procurement_peserta` pada tahap ini; marker-based rollback validation tetap acceptable.

## Open Questions
1. Repo Laravel final sibling belum tersedia/teridentifikasi di mesin ini saat inspeksi (`C:\Users\Revanza-Home\source\repos`); path aktual yang akan dipakai untuk `EPROC_PILOT_APP_BIND_PATH` masih menunggu repo final dibuat/di-clone.
