# Phase 6 Coexistence Dev Baseline (Wave A -> Wave B Stage 1/2)

## Metadata
- Created: `February 25, 2026`
- Last Updated: `February 26, 2026` (Wave B next-step implementation: pilot Laravel read-only nested endpoints `get_initial_data/get_chart_update` added with graceful fallback; contract+compare harness baseline untuk subset `json_provider` dibekukan; Stage 1/2 smoke revalidated)
- Status: `Stage 2 Implemented for scoped route toggle (get_barang/get_peserta) + rollback switch in dev; in-project pilot path ./pilot-app now contains Laravel skeleton with query-based read-only pilot subset + nested shadow-verifiable endpoints (graceful degraded fallback on dev table gap); json_provider subset contract+compare harness baseline/artifact tersedia; EPROC_PILOT_APP_BIND_PATH hook retained as optional; CX-05 auth bridge pending`

## Purpose
Menetapkan baseline coexistence CI3 + app baru di environment dev, mulai dari artefak desain Wave A hingga proof Wave B Stage 1/2 (shadow route + scoped toggle) dan hook integrasi skeleton Laravel final via sibling bind mount.
Coexistence pada Phase 6 Wave B adalah safety rail transisi menuju migrasi penuh CI3 -> Laravel, bukan state akhir arsitektur target.

## Current State (Evidence-Based, After Wave B Stage 2)
- Service `pilot-app` sudah ditambahkan ke `docker-compose.yml` untuk proof coexistence dev dan kini bind ke skeleton Laravel in-project (`./pilot-app`).
- Service `pilot-app` kini memakai bind mount path yang configurable via `.env` (`EPROC_PILOT_APP_BIND_PATH`) dengan fallback default `./pilot-app` (workflow existing tetap jalan).
- Route shadow `/_pilot/auction/*` sudah diarahkan ke `pilot-app` pada host `vms.localhost`.
- Helper smoke coexistence (`CX-01`, `CX-02`) sudah tersedia via `tools/dev-env.ps1 -Action coexistence`.
- Route toggle subset endpoint bisnis `auction/admin/json_provider/{get_barang|get_peserta}` sudah diimplementasikan via Nginx include file aktif (`ON/OFF`) + `nginx -s reload` (tanpa full restart stack).
- Helper dev untuk toggle + validasi Stage 2 tersedia via `tools/dev-env.ps1` (`toggle-auction-subset`, `coexistence-stage2`).
- `CX-03` dan `CX-04` sudah tervalidasi untuk aspek routing/rollback marker (pilot `ON`, CI3 `OFF`).
- CI3 response pada sample `id_lelang=1` saat toggle `OFF` terobservasi `HTTP 500` karena tabel dev sample tidak tersedia (`ms_procurement_barang`, `ms_procurement_peserta`); hal ini tidak menghalangi verifikasi rollback route marker.
- Keputusan path dev untuk pilot sudah ditutup: gunakan path in-project `./pilot-app`; hook `EPROC_PILOT_APP_BIND_PATH` tetap dipertahankan untuk opsi repo terpisah.
- Integrasi next-step sesi ini berhasil mengganti placeholder `./pilot-app` menjadi skeleton Laravel (Laravel 8, kompatibel PHP 7.4) dan kini endpoint subset `get_barang/get_peserta` sudah memakai jalur query read-only (bukan stub hardcoded) dengan fallback degradasi kompatibel saat DB/schema dev mismatch.
- Sesi lanjutan Wave B menambahkan endpoint nested pilot read-only `get_initial_data` dan `get_chart_update` di Laravel (service/query builder + controller + shadow route aman) tanpa memperluas scoped toggle Stage 2 yang existing.
- Marker header app pilot tetap dipertahankan (`X-App-Source: pilot-skeleton`) sehingga smoke `CX-02`/`CX-03` tetap kompatibel dengan checker existing.
- Pada DB dev sample saat sesi ini, tabel `eproc.ms_procurement_barang` dan `eproc.ms_procurement_peserta` tidak tersedia; endpoint pilot mengembalikan `[]` + header degradasi (`X-Pilot-Data-Status`) dan warning log Laravel, sehingga shape response minimum tetap kompatibel smoke tanpa menambah requirement seed data.
- Untuk endpoint nested (`get_initial_data`, `get_chart_update`), DB dev mismatch yang sama memicu fallback object-shape minimum (`200 OK` + header degradasi `X-Pilot-Data-*`) pada shadow path `/_pilot/auction/...`; compare payload CI3 vs pilot secara runtime penuh tetap blocked karena CI3 nested sample juga `HTTP 500` di dev.
- Baseline contract + compare harness untuk subset `auction/admin/json_provider` (`get_barang`, `get_peserta`, `get_initial_data`, `get_chart_update`) kini dibekukan sebagai fondasi migrasi penuh: minimum shape, marker expectation, semantics compare `PASS/BLOCKED/MISMATCH`, dan artifact JSON compare dev tersedia.
- Hook `EPROC_PILOT_APP_BIND_PATH` tetap dipertahankan sebagai opsi repo terpisah; ketiadaan repo sibling final tidak lagi memblokir proof integrasi in-project.
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
- Meng-upgrade dua endpoint subset pilot (`get_barang`, `get_peserta`) ke implementasi Laravel read-only berbasis query builder dengan graceful degraded-empty fallback saat query gagal (mis. tabel/schema dev tidak tersedia).
- Menambahkan endpoint nested pilot read-only `get_initial_data` + `get_chart_update` pada `pilot-app` (query builder + service layer + fallback object-shape) dan mengekspos shadow path aman `/_pilot/auction/admin/json_provider/...` untuk verifikasi tanpa mengubah scoped toggle Stage 2.
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
      -> pilot-app (Laravel skeleton in-project, final artifact baseline) untuk endpoint pilot auction read-only
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
| Tambah service `pilot-app` (nama final boleh berbeda) | Implemented (Laravel skeleton in-project + compatibility stubs) | `docker-compose.yml` (+ override bila perlu) | Container start healthy dan bisa diakses Nginx upstream |
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
| 2026-02-26 | `Get-ChildItem -Path C:\Users\Revanza-Home\source\repos -Recurse -File -Filter artisan -ErrorAction SilentlyContinue` | BLOCKED (no result) | Re-check pasca-merge `33139dc`: repo Laravel sibling final belum terdeteksi di mesin lokal |
| 2026-02-26 | `$env:EPROC_PILOT_APP_BIND_PATH='./pilot-app'; docker compose -f docker-compose.yml config` | PASS | Revalidation pasca-merge `33139dc`: compose tetap resolve ke fallback placeholder tanpa ubah route/toggle |
| 2026-02-26 | `$env:EPROC_PILOT_APP_BIND_PATH='./pilot-app'; pwsh ./tools/dev-env.ps1 -Action start -PhpRuntime 7.4 -NoBuild` | PASS | Menyalakan stack untuk re-run smoke pasca-merge `33139dc` |
| 2026-02-26 | `$env:EPROC_PILOT_APP_BIND_PATH='./pilot-app'; pwsh ./tools/dev-env.ps1 -Action coexistence -PhpRuntime 7.4` | PASS | Revalidation `CX-01` + `CX-02` pasca-merge `33139dc` |
| 2026-02-26 | `$env:EPROC_PILOT_APP_BIND_PATH='./pilot-app'; pwsh ./tools/dev-env.ps1 -Action coexistence-stage2 -PhpRuntime 7.4 -AuctionLelangId 1` | PASS | Revalidation `CX-03` + `CX-04` pasca-merge `33139dc`; rollback marker PASS, HTTP CI3 `500` tetap acceptable (dev table gap) |
| 2026-02-26 | `Get-ChildItem -Force .\pilot-app` | BLOCKED (final skeleton not present) | Hasil inspeksi path in-project: hanya folder `public/` (placeholder), belum ada struktur Laravel final |
| 2026-02-26 | `rg --files .\pilot-app` | BLOCKED (placeholder-only) | Hanya `pilot-app/public/index.php`; tidak ada `artisan` / `composer.json` untuk skeleton Laravel final |
| 2026-02-26 | `docker compose -f docker-compose.yml config` | PASS | Re-check sesi ini dengan keputusan path in-project `./pilot-app`; compose tetap valid dan bind `pilot-app` ter-resolve |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action coexistence -PhpRuntime 7.4` | PASS | Re-check sesi ini: `CX-01` + `CX-02` tetap PASS setelah inspeksi `./pilot-app` |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action coexistence-stage2 -PhpRuntime 7.4 -AuctionLelangId 1` | PASS | Re-check sesi ini: `CX-03` + `CX-04` tetap PASS; `CX-04` marker rollback CI3 PASS dengan HTTP `500` accepted (gap tabel dev) |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action coexistence-stage2 -PhpRuntime 7.4 -AuctionLelangId 1` (output summary) | PASS | Toggle `on/off` + `nginx reload completed`; `CX-03` kedua endpoint subset = HTTP `200`; `CX-04` kedua endpoint subset = HTTP `500` dengan marker rollback CI3 tetap ok |
| 2026-02-26 | `composer create-project laravel/laravel:^8.0 pilot-app --prefer-dist --no-interaction` | PASS | Placeholder `./pilot-app` diganti in-place menjadi skeleton Laravel 8 (kompatibel PHP `7.4`); composer post-install menjalankan `artisan key:generate` sukses |
| 2026-02-26 | `php artisan route:list` (di `pilot-app`) | PASS | Route stub kompatibel terdaftar: `/_pilot/auction/health`, `/_pilot/auction/{path?}`, `auction/admin/json_provider/get_barang/{idLelang}`, `.../get_peserta/{idLelang}` |
| 2026-02-26 | `Get-ChildItem -Force .\pilot-app` + `rg --files .\pilot-app` | PASS | Struktur Laravel hadir (`artisan`, `composer.json`, `bootstrap/`, `routes/`, `public/`); tidak lagi placeholder-only |
| 2026-02-26 | `docker compose -f docker-compose.yml config` | PASS | Post-integrasi Laravel in-project: compose tetap valid dan bind `pilot-app` tetap resolve ke `./pilot-app` (hook `EPROC_PILOT_APP_BIND_PATH` tidak berubah) |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action coexistence -PhpRuntime 7.4` | PASS | Post-integrasi Laravel in-project: `CX-01` + `CX-02` tetap PASS |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action coexistence-stage2 -PhpRuntime 7.4 -AuctionLelangId 1` | PASS | Post-integrasi Laravel in-project: `CX-03` PASS (pilot marker + HTTP `200`), `CX-04` rollback marker CI3 PASS dengan HTTP `500` tetap accepted (gap tabel dev) |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action toggle-auction-subset -ToggleMode on/off -PhpRuntime 7.4` + `curl -D -` health/get_barang | PASS | Header marker pilot terlihat pasca-integrasi Laravel: `X-App-Source: pilot-skeleton`, `X-Coexistence-Route: pilot-shadow/pilot-business-toggle`, `X-Coexistence-Toggle: auction-json-provider=on`; rollback cepat tetap via `nginx reload completed` |
| 2026-02-26 | `docker compose -f docker-compose.yml config` | PASS | Post-read-only upgrade: compose valid; `pilot-app` env DB (`DB_HOST=db`, `DB_DATABASE=eproc`, dst.) ter-resolve dan hook `EPROC_PILOT_APP_BIND_PATH` tetap dipertahankan |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action start -PhpRuntime 7.4 -NoBuild` | PASS | `pilot-app` container di-recreate untuk memuat env DB baru; service kembali healthy |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action coexistence -PhpRuntime 7.4` | PASS | Revalidation pasca-upgrade endpoint read-only: `CX-01` + `CX-02` tetap PASS |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action coexistence-stage2 -PhpRuntime 7.4 -AuctionLelangId 1` | PASS | Revalidation pasca-upgrade endpoint read-only: `CX-03` pilot ON tetap HTTP `200` + marker; `CX-04` rollback CI3 marker PASS dengan HTTP `500` accepted |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action toggle-auction-subset -ToggleMode on -PhpRuntime 7.4` | PASS | Toggle subset ON + `nginx reload completed` untuk header dump manual pasca-upgrade |
| 2026-02-26 | `curl.exe -sS -D - -o NUL -H "Host: vms.localhost" http://127.0.0.1:8080/_pilot/auction/health` | PASS | Shadow route tetap `200`; marker `X-App-Source: pilot-skeleton` + `X-Coexistence-Route: pilot-shadow` terlihat |
| 2026-02-26 | `curl.exe -sS -D - -o NUL -H "Host: vms.localhost" http://127.0.0.1:8080/auction/admin/json_provider/get_barang/1` (toggle `ON`) | PASS (degraded data path) | Marker pilot/toggle tetap muncul + header degradasi `X-Pilot-Data-Source: degraded-empty`, `X-Pilot-Data-Status: db-unavailable-or-schema-mismatch`, `SQLSTATE 42S02/1146` |
| 2026-02-26 | `curl.exe -sS -H "Host: vms.localhost" http://127.0.0.1:8080/auction/admin/json_provider/{get_barang|get_peserta}/1` (toggle `ON`) | PASS (shape compatibility) | Body sample pilot untuk kedua endpoint = `[]` (graceful fallback; bukan stub hardcoded) |
| 2026-02-26 | `Get-Content .\\pilot-app\\storage\\logs\\laravel.log -Tail 80` | BLOCKED (dev data table gap confirmed) | Warning log mencatat query builder path gagal karena tabel `eproc.ms_procurement_barang` / `eproc.ms_procurement_peserta` tidak ada (SQLSTATE `42S02`, error `1146`) |
| 2026-02-26 | `docker compose exec -T db mysql -uroot -proot -e "USE eproc; SHOW TABLES LIKE 'ms_procurement_barang'; SHOW TABLES LIKE 'ms_procurement_peserta';"` | BLOCKED (no rows) | Konfirmasi eksplisit tabel dev `auction` untuk sample endpoint belum tersedia; tidak menambah requirement seed data untuk `CX-03/CX-04` |
| 2026-02-26 | `docker compose -f docker-compose.yml config` | PASS | Re-check Wave B next step nested endpoint: compose valid; baseline coexistence bind/hook (`EPROC_PILOT_APP_BIND_PATH`) tidak berubah |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action coexistence -PhpRuntime 7.4` | PASS | Revalidation pasca-implementasi nested endpoint: `CX-01` + `CX-02` tetap PASS; `/_pilot/auction/health` tetap `200` |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action coexistence-stage2 -PhpRuntime 7.4 -AuctionLelangId 1` | PASS | Stage 2 scoped toggle existing tetap aman: `CX-03` PASS, `CX-04` rollback marker CI3 PASS dengan HTTP `500` accepted |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action toggle-auction-subset -ToggleMode on/off -PhpRuntime 7.4` | PASS | Header dump manual Wave B nested step dilakukan dengan toggle `ON`, lalu dikembalikan `OFF`; rollback tetap via `nginx reload completed` |
| 2026-02-26 | `curl.exe -sS -D - -o NUL -H "Host: vms.localhost" http://127.0.0.1:8080/_pilot/auction/health` (toggle `ON`) | PASS | Marker pilot tetap kompatibel: `X-App-Source: pilot-skeleton`, `X-Coexistence-Route: pilot-shadow` |
| 2026-02-26 | `curl.exe -sS -D - -o NUL -H "Host: vms.localhost" http://127.0.0.1:8080/auction/admin/json_provider/get_barang/1` (toggle `ON`, recheck nested step) | PASS (degraded data path) | Marker pilot/toggle tetap muncul + header degradasi `X-Pilot-Data-Source: degraded-empty`, `X-Pilot-Data-Status: db-unavailable-or-schema-mismatch`, `SQLSTATE 42S02/1146` |
| 2026-02-26 | `curl.exe -sS -D - -H "Host: vms.localhost" http://127.0.0.1:8080/_pilot/auction/admin/json_provider/get_initial_data/1/1` | PASS (graceful fallback, shadow verification) | `HTTP 200`; header `X-App-Source: pilot-skeleton`, `X-Pilot-Endpoint: get_initial_data`, `X-Pilot-Route-Mode: shadow-route`, `X-Pilot-Data-Status: db-unavailable-or-schema-mismatch`; body shape minimum object `{id,name,subtitle,data,last,time}` |
| 2026-02-26 | `curl.exe -sS -D - -H "Host: vms.localhost" http://127.0.0.1:8080/_pilot/auction/admin/json_provider/get_chart_update/1` | PASS (graceful fallback, shadow verification) | `HTTP 200`; header `X-App-Source: pilot-skeleton`, `X-Pilot-Endpoint: get_chart_update`, `X-Pilot-Route-Mode: shadow-route`, `X-Pilot-Data-Status: db-unavailable-or-schema-mismatch`; body shape minimum object `{data,time}` |
| 2026-02-26 | `curl.exe -sS -D - -o NUL -H "Host: vms.localhost" http://127.0.0.1:8080/auction/admin/json_provider/get_initial_data/1/1` + `.../get_chart_update/1` (toggle `OFF`) | BLOCKED (runtime compare sample) | CI3 nested sample di dev tetap `HTTP 500`; compare body CI3 vs pilot untuk nested payload belum bisa dibekukan dari runtime sample (tidak menambah requirement seed data) |
| 2026-02-26 | `pwsh ./tools/compare-auction-json-provider-subset.ps1 -AuctionLelangId 1 -AuctionBarangId 1 -PhpRuntime 7.4` | BLOCKED (expected dev runtime sample gap; harness valid) | Semua endpoint subset tercatat `BLOCKED` eksplisit (bukan false PASS) karena CI3 sample `HTTP 500`; pilot side tetap `HTTP 200` + minimum shape/marker valid + degradasi `SQLSTATE 42S02 / 1146`; artifact JSON tersimpan di `docs/artifacts/phase6-waveb-json-provider-subset-compare-20260226-124453.json` |
| 2026-02-26 | `rg -n "get_initial_data|get_chart_update|readonly endpoint degraded" pilot-app/storage/logs/laravel.log` | BLOCKED (dev schema mismatch confirmed) | Log Laravel menunjukkan query builder path nested memang dieksekusi lalu gagal `42S02/1146` pada `eproc.ms_procurement_barang` (`get_chart_update` select `id`; `get_initial_data` join `tb_kurs`) |
| 2026-02-26 | `docker compose -f docker-compose.yml config` | PASS | Re-check awal follow-up Wave B sample hunt: baseline coexistence infra tetap sehat; bind `pilot-app` + hook `EPROC_PILOT_APP_BIND_PATH` tidak berubah |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action coexistence -PhpRuntime 7.4` | PASS | Follow-up sample hunt: `CX-01` + `CX-02` tetap PASS; `/_pilot/auction/health` tetap `200` |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action coexistence-stage2 -PhpRuntime 7.4 -AuctionLelangId 1` | PASS | Follow-up sample hunt: `CX-03` + `CX-04` tetap PASS; `CX-04` marker rollback CI3 dengan HTTP `500` tetap accepted |
| 2026-02-26 | `docker exec eproc-db mysql -uroot -proot -e "SELECT TABLE_SCHEMA, TABLE_NAME FROM information_schema.tables WHERE TABLE_NAME IN ('ms_procurement','ms_procurement_barang','ms_procurement_peserta','ms_penawaran','tb_kurs','ms_vendor','ms_procurement_kurs') ORDER BY TABLE_SCHEMA, TABLE_NAME;"` | BLOCKED (local schema source unavailable) | Tidak ada row hasil query pada DB lokal (`eproc`, `eproc_perencanaan`) untuk tabel runtime subset `auction`; akibatnya tidak ada `id_lelang/id_barang` lokal yang dapat dipilih untuk sample CI3 `HTTP 200` tanpa seed/import data |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action toggle-auction-subset -ToggleMode on -PhpRuntime 7.4` + `curl.exe -sS -D - -o NUL -H "Host: vms.localhost" http://127.0.0.1:8080/_pilot/auction/health` + `.../auction/admin/json_provider/get_barang/1` | PASS (marker/header revalidated) | Toggle `ON` header dump tetap kompatibel: `X-App-Source: pilot-skeleton`; `/_pilot/auction/health` `200`, `get_barang/1` routed ke pilot dengan `X-Coexistence-Route: pilot-business-toggle`, `X-Coexistence-Toggle: auction-json-provider=on`, dan header degradasi `42S02/1146` |
| 2026-02-26 | `pwsh ./tools/compare-auction-json-provider-subset.ps1 -AuctionLelangId 1 -AuctionBarangId 1 -PhpRuntime 7.4` (follow-up sample hunt) | BLOCKED (granular per-endpoint; no false PASS) | Artifact terbaru `docs/artifacts/phase6-waveb-json-provider-subset-compare-20260226-130218.json`: `get_barang`, `get_peserta`, `get_initial_data`, `get_chart_update` semuanya `BLOCKED` karena CI3 `HTTP 500` (Database Error HTML), sementara pilot tetap `HTTP 200` + marker valid + `X-Pilot-Data-Status=db-unavailable-or-schema-mismatch` (`42S02/1146`) |
| 2026-02-25 | `pwsh ./tools/dev-env.ps1 -Action stop -PhpRuntime 7.4` | PASS | Cleanup stack setelah verifikasi |

## Implementation Notes for Wave B
- Nama service final (`pilot-app`, `laravel-pilot`, dll.) diputuskan saat skeleton Laravel final tersedia.
- `pilot-app/` kini berisi skeleton Laravel in-project (menggantikan placeholder Stage 1) dengan route stub kompatibel untuk proof Stage 1/2; keputusan repo placement jangka lanjut tetap dirujuk ke `DR-P6-005`.
- Jika skeleton/Laravel final berada di repo lain, gunakan volume/proxy strategy yang tidak mengubah path CI3 existing.
- Hook dev yang diterapkan sekarang: `docker-compose.yml` memakai bind mount configurable `EPROC_PILOT_APP_BIND_PATH` (default `./pilot-app`), sehingga repo sibling bisa dipasang tanpa mengubah route/toggle coexistence.
- Volume `pilot-app` diubah ke syntax bind long-form agar path absolut Windows (drive letter) tetap aman saat diarahkan ke repo sibling (`C:\...`).
- Tambahkan marker response/header pada app pilot dev untuk memudahkan smoke verification (`X-App-Source`).
- Implementasi Stage 2 memakai file include aktif Nginx (`docker/nginx/includes/pilot-auction-subset-toggle.active.conf`) yang diisi dari template `legacy/pilot`; rollback tercepat = switch file + `nginx -s reload`.
- Integrasi skeleton Laravel final-compatible ke path in-project `./pilot-app` berhasil dilakukan pada sesi ini dengan `composer create-project` (Laravel 8, PHP 7.4-compatible), tanpa mengubah route/toggle/Nginx coexistence existing.
- Sesi ini menambahkan implementasi endpoint pilot read-only di Laravel (`get_barang`, `get_peserta`) memakai query builder + service layer; jika query DB gagal karena table/schema mismatch dev, endpoint tetap mengembalikan array kosong dengan marker degradasi (`X-Pilot-Data-*`) dan log warning agar coexistence smoke tidak rusak.
- Sesi lanjutan menambahkan implementasi nested endpoint `get_initial_data` dan `get_chart_update` pada service/controller Laravel dengan jalur query read-only nyata (termasuk komposisi payload nested berdasarkan query CI3) dan fallback object-shape minimum untuk mismatch schema dev; verifikasi dilakukan via shadow path `/_pilot/auction/...` agar scoped toggle Stage 2 (`get_barang/get_peserta`) tidak berubah.

## Nested Payload Minimum Shape Baseline (Wave B Next Step, February 26, 2026)
- Sumber shape dasar:
  - CI3 controller/model `Json_provider` / `Json_provider_model` (query path dan field assembly).
  - Verifikasi runtime pilot shadow path (hasil aktual sesi ini).
- `get_initial_data/{idLelang}/{idBarang}` (CI3 query path):
  - Object root: `id`, `name`, `subtitle`, `data`, `last`, `time`
  - `data`: array series peserta -> `{ name, data }`
  - `data[].data`: array titik penawaran -> `{ x, y }`
  - `last`: array barang -> `{ id, data }` dengan `data` berisi series peserta terbaru (latest-only)
- `get_chart_update/{idLelang}` (CI3 query path):
  - Object root: `data`, `time`
  - `data`: array barang -> `{ id, data }`
  - `data[].data`: array series peserta -> `{ name, data }`
  - `data[].data[].data`: array titik latest (maksimum 1 record per peserta pada query path CI3) -> `{ x, y }`
- Hasil runtime pilot (shadow path, dev DB mismatch):
  - `get_initial_data`: `{"id":"1","name":"","subtitle":"","data":[],"last":[],"time":"YYYY-MM-DD HH:MM:SS"}`
  - `get_chart_update`: `{"data":[],"time":"YYYY-MM-DD HH:MM:SS"}`
- Blocker runtime compare CI3 vs pilot:
  - CI3 nested endpoints sample (`/auction/admin/json_provider/get_initial_data/1/1`, `/auction/admin/json_provider/get_chart_update/1`) terobservasi `HTTP 500` di dev.
  - Pilot log mengonfirmasi query path nested dieksekusi tetapi fallback karena `SQLSTATE 42S02 / 1146` (`eproc.ms_procurement_barang` missing).
- Tidak ada requirement seed data tambahan yang ditambahkan pada tahap ini; blocker dicatat eksplisit.

## json_provider Subset Contract + Compare Baseline (Wave B Contract Freeze, February 26, 2026)
- Scope contract baseline (subset migrasi aktif):
  - `get_barang`
  - `get_peserta`
  - `get_initial_data`
  - `get_chart_update`
- Tujuan: menjadi fondasi compare/cutover readiness menuju migrasi penuh CI3 -> Laravel, sambil mempertahankan coexistence Stage 1/2 sebagai safety rail.

### Contract Matrix (Minimum Shape + Marker/Fallback Expectation)
| Endpoint | CI3 compare path (dev) | Pilot verification path (dev) | Minimum shape (top-level) | Acceptable pilot fallback di dev (schema mismatch) | Marker/header expectation verifikasi |
|---|---|---|---|---|---|
| `get_barang` | `/auction/admin/json_provider/get_barang/{idLelang}` (toggle `OFF`) | `/auction/admin/json_provider/get_barang/{idLelang}` (toggle `ON`) | Array item minimum `{id,name,hps,hps_in_idr}`; empty array tetap acceptable | `HTTP 200`, body `[]`, `X-Pilot-Data-Source: degraded-empty`, `X-Pilot-Data-Status: db-unavailable-or-schema-mismatch`, `X-Pilot-Error-SqlState`, `X-Pilot-Error-Code` | CI3 (OFF): `X-App-Source: ci3-legacy`, `X-Coexistence-Route: ci3-legacy-subset`, `X-Coexistence-Toggle: auction-json-provider=off`; Pilot (ON): `X-App-Source: pilot-skeleton`, `X-Pilot-Endpoint: get_barang`, `X-Pilot-Route-Mode: business-toggle`, `X-Coexistence-Route: pilot-business-toggle`, `X-Coexistence-Toggle: auction-json-provider=on` |
| `get_peserta` | `/auction/admin/json_provider/get_peserta/{idLelang}` (toggle `OFF`) | `/auction/admin/json_provider/get_peserta/{idLelang}` (toggle `ON`) | Array item minimum `{id,name}`; empty array tetap acceptable | Sama seperti `get_barang` (`HTTP 200` + degraded headers + empty array acceptable) | CI3 (OFF): marker subset legacy; Pilot (ON): `X-App-Source: pilot-skeleton`, `X-Pilot-Endpoint: get_peserta`, `X-Pilot-Route-Mode: business-toggle`, `X-Coexistence-Route: pilot-business-toggle` |
| `get_initial_data` | `/auction/admin/json_provider/get_initial_data/{idLelang}/{idBarang}` (CI3 direct path, toggle tidak relevan) | `/_pilot/auction/admin/json_provider/get_initial_data/{idLelang}/{idBarang}` (shadow) | Object minimum `{id,name,subtitle,data,last,time}` | `HTTP 200` dengan object fallback shape yang sama + `X-Pilot-Data-*` degraded headers (`42S02/1146` observed) | Pilot shadow: `X-App-Source: pilot-skeleton`, `X-Pilot-Endpoint: get_initial_data`, `X-Pilot-Route-Mode: shadow-route`, `X-Coexistence-Route: pilot-shadow`; CI3 nested path pada scope ini tidak punya marker coexistence subset yang wajib |
| `get_chart_update` | `/auction/admin/json_provider/get_chart_update/{idLelang}` (CI3 direct path, toggle tidak relevan) | `/_pilot/auction/admin/json_provider/get_chart_update/{idLelang}` (shadow) | Object minimum `{data,time}` | `HTTP 200` dengan object fallback shape yang sama + `X-Pilot-Data-*` degraded headers (`42S02/1146` observed) | Pilot shadow: `X-App-Source: pilot-skeleton`, `X-Pilot-Endpoint: get_chart_update`, `X-Pilot-Route-Mode: shadow-route`, `X-Coexistence-Route: pilot-shadow`; CI3 nested path marker subset tidak diwajibkan |

### Compare Harness Baseline (Reusable)
- Harness: `tools/compare-auction-json-provider-subset.ps1`
- Output artifact (latest follow-up sample hunt sesi ini): `docs/artifacts/phase6-waveb-json-provider-subset-compare-20260226-130218.json`
- Artifact baseline sebelumnya tetap tersimpan: `docs/artifacts/phase6-waveb-json-provider-subset-compare-20260226-124453.json`
- Semantics:
  - `PASS`: CI3 dan pilot sama-sama comparable (minimum shape + marker expectation sesuai sample).
  - `BLOCKED`: compare tidak bisa disimpulkan (mis. CI3 `HTTP 5xx`, non-JSON, atau sample runtime tidak tersedia). Hasil harus tetap merekam endpoint + blocker detail.
  - `MISMATCH`: kedua sisi reachable tetapi minimum shape/marker expectation berbeda.
- Hasil sesi ini (dev sample `id_lelang=1`, `id_barang=1`):
  - `get_barang`: `BLOCKED` (CI3 `HTTP 500`), pilot `HTTP 200` degraded fallback, `SQLSTATE 42S02`, error `1146`
  - `get_peserta`: `BLOCKED` (CI3 `HTTP 500`), pilot `HTTP 200` degraded fallback, `SQLSTATE 42S02`, error `1146`
  - `get_initial_data`: `BLOCKED` (CI3 `HTTP 500`), pilot `HTTP 200` fallback object-shape, `SQLSTATE 42S02`, error `1146`
  - `get_chart_update`: `BLOCKED` (CI3 `HTTP 500`), pilot `HTTP 200` fallback object-shape, `SQLSTATE 42S02`, error `1146`
- Follow-up sample hunt status (payload runtime nyata):
  - Local DB inspection (`information_schema`) menunjukkan tabel runtime subset `auction` tidak tersedia pada DB lokal (`eproc`, `eproc_perencanaan`), sehingga tidak ada candidate `id_lelang/id_barang` valid untuk CI3 `HTTP 200` tanpa seed/import.
  - Tidak ada akses staging/non-prod yang terkonfigurasi/tersedia pada sesi ini, sehingga compare belum bisa dinaikkan ke `PASS/MISMATCH` berbasis payload runtime nyata.
- Catatan: `BLOCKED` pada sesi ini adalah hasil yang benar/jujur (dev schema gap), bukan kegagalan harness. Tidak menambah requirement seed data.

## json_provider Subset Cutover-Readiness Path (Technical vs Business)
- Sudah siap untuk cutover teknis terbatas (subset ini):
  - Routing rail coexistence proven: shadow route `/_pilot/auction/*`, scoped toggle Stage 2 untuk `get_barang/get_peserta`, rollback cepat via Nginx include + `nginx -s reload`.
  - Marker/header verifikasi proven dan tetap kompatibel (`X-App-Source: pilot-skeleton` + marker route/toggle).
  - Pilot Laravel endpoint subset sudah query-based read-only (bukan stub) dengan graceful fallback yang menjaga minimum shape saat schema dev mismatch.
  - Compare harness reusable tersedia untuk mencatat PASS/BLOCKED/MISMATCH dan artifact JSON.
- Belum siap untuk cutover bisnis (subset ini):
  - CI3 runtime sample comparable belum tersedia (semua sample compare sesi ini `HTTP 500` di dev).
  - Follow-up sample hunt membuktikan akar masalah lokal adalah ketiadaan tabel runtime subset `auction` (bukan sekadar salah pilih `id_lelang/id_barang`), sehingga kebutuhan evidence runtime nyata bergeser ke environment lain yang memang punya data (dev yang lengkap atau staging/non-prod).
  - Contract runtime sample CI3 teranonymisasi untuk baseline `pilot-contract` belum lengkap (khususnya nested payload actual values).
  - Auth bridge (`CX-05`) belum diimplementasikan untuk endpoint protected follow-up.
  - Test automation (`pilot-contract`, `pilot-integration`) dan CI evidence belum ada.
- Open Question:
  - Kapan environment dev/staging menyediakan sample CI3 runtime valid (`HTTP 200`) untuk subset ini agar compare dapat naik dari `BLOCKED` ke `PASS/MISMATCH` berbasis payload nyata?
  - Siapa owner yang dapat menyediakan akses aman ke staging/non-prod (atau dump read-only anonymized) untuk subset `auction/admin/json_provider` tanpa mengubah kode bisnis CI3?

## Closed Questions (Resolved Decisions, February 26, 2026)
1. Placeholder `pilot-app` diganti ke skeleton Laravel final: `Ya, secepatnya`.
2. Jika repo final terpisah: strategi dev yang dipilih = `bind mount sibling`.
3. `CX-03/CX-04` tidak memerlukan `id_lelang` seeded khusus untuk lanjut tahap ini.
4. Tidak perlu command seed minimal untuk `ms_procurement_barang` / `ms_procurement_peserta` pada tahap ini; marker-based rollback validation tetap acceptable.

## Open Questions
1. Resolved (2026-02-26): path dev yang dipakai berada di dalam project ini (`./pilot-app`). Hook `EPROC_PILOT_APP_BIND_PATH` tetap dipertahankan untuk opsi repo terpisah di masa lanjut.
