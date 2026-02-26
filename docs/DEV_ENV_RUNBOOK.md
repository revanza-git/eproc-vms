# Dev Environment Runbook (Phase 1)

## Scope
Runbook ini untuk operasional harian environment development CI3 (`vms`, `intra/main`, `intra/pengadaan`) berbasis Docker Compose, termasuk proof coexistence Phase 6 Wave B (`pilot-app` Laravel skeleton dev).

Dual-runtime tersedia:
- Default legacy: `PHP 7.4`
- Validation target modern: `PHP 8.2`

## Prerequisites
- Docker Desktop aktif.
- Port `8080` (web) dan `3308` (DB) tidak dipakai proses lain.
- Hostname lokal tersedia:
  - `vms.localhost`
  - `intra.localhost`

## One-Time Bootstrap
1. Buat file env lokal dari template:
   ```powershell
   pwsh ./tools/dev-env.ps1 -Action bootstrap
   ```
2. (Opsional) review nilai sensitif di:
   - `.env`
   - `vms/.env`
   - `intra/.env`

## Start Environment
- Standard:
  ```powershell
  pwsh ./tools/dev-env.ps1 -Action start -PhpRuntime 7.4
  ```
- Tanpa build image ulang:
  ```powershell
  pwsh ./tools/dev-env.ps1 -Action start -PhpRuntime 7.4 -NoBuild
  ```
- Start runtime PHP 8.2:
  ```powershell
  pwsh ./tools/dev-env.ps1 -Action start -PhpRuntime 8.2
  ```

## Stop Environment
```powershell
pwsh ./tools/dev-env.ps1 -Action stop -PhpRuntime 7.4
```

## Reset Environment (Destructive)
Menghapus container + network + volume DB lalu build ulang.
```powershell
pwsh ./tools/dev-env.ps1 -Action reset -PhpRuntime 7.4
```

## Operational Commands
- Status service:
  ```powershell
  pwsh ./tools/dev-env.ps1 -Action status -PhpRuntime 7.4
  ```
- Tail logs semua service:
  ```powershell
  pwsh ./tools/dev-env.ps1 -Action logs -PhpRuntime 7.4
  ```
- Tail logs service tertentu:
  ```powershell
  pwsh ./tools/dev-env.ps1 -Action logs -PhpRuntime 7.4 -Service webserver
  ```
- Smoke check endpoint minimum:
  ```powershell
  pwsh ./tools/dev-env.ps1 -Action smoke -PhpRuntime 7.4
  ```
- Coexistence smoke (legacy + pilot shadow route `CX-01`/`CX-02`):
  ```powershell
  pwsh ./tools/dev-env.ps1 -Action coexistence -PhpRuntime 7.4
  ```
- Check koneksi DB + Redis di runtime aktif:
  ```powershell
  pwsh ./tools/dev-env.ps1 -Action deps -PhpRuntime 7.4
  ```
- Check minimal cron runtime (`vms` cron core):
  ```powershell
  pwsh ./tools/dev-env.ps1 -Action cron -PhpRuntime 7.4
  ```

## Quality Gate Commands (Phase 4)
Jalankan command ini setelah environment aktif (`start`) untuk validasi minimum sebelum merge:

- Lint:
  ```powershell
  pwsh ./tools/dev-env.ps1 -Action lint -PhpRuntime 7.4
  ```
- Test bootstrap:
  ```powershell
  pwsh ./tools/dev-env.ps1 -Action test -PhpRuntime 7.4
  ```
- Smoke endpoint minimum:
  ```powershell
  pwsh ./tools/dev-env.ps1 -Action smoke -PhpRuntime 7.4
  ```

Referensi lengkap quality gate + branch protection:
- `docs/CI_QUALITY_GATES.md`

## Phase 6 Coexistence Baseline (Wave A -> Wave B Stage 1/2 + Sibling Bind-Mount Hook)

Status saat ini: `Stage 2 implemented untuk scoped route toggle (get_barang/get_peserta) + rollback switch via nginx reload; ./pilot-app sudah berisi skeleton Laravel dengan endpoint subset read-only query-based + nested endpoint read-only berikutnya (get_initial_data/get_chart_update) yang diverifikasi via shadow path, dengan graceful degraded fallback saat tabel dev tidak tersedia; EPROC_PILOT_APP_BIND_PATH hook tetap ready/opsional; CX-05 auth bridge pending`

Wave A menyiapkan baseline desain/checklist/test plan. Wave B Stage 1/2 sudah menambahkan proof runtime coexistence dev di repo ini:
- service `pilot-app` pada compose (dev pilot; saat ini skeleton Laravel in-project untuk health/shadow + subset read-only `get_barang/get_peserta`),
- bind mount path `pilot-app` configurable via `.env` (`EPROC_PILOT_APP_BIND_PATH`, default `./pilot-app`) untuk integrasi repo sibling Laravel final,
- route shadow `/_pilot/auction/*` di Nginx (`vms.localhost`),
- toggle subset endpoint bisnis `auction/admin/json_provider/{get_barang|get_peserta}` via include file Nginx aktif,
- action `coexistence` (`CX-01`,`CX-02`) dan `coexistence-stage2` (`CX-03`,`CX-04`) di `tools/dev-env.ps1`.
- `./pilot-app` sudah diganti dari placeholder ke skeleton Laravel 8 (kompatibel PHP 7.4), marker header `X-App-Source: pilot-skeleton` tetap dipertahankan, endpoint subset `get_barang/get_peserta` sudah memakai query builder read-only dengan fallback `[]` + header degradasi jika DB/schema dev mismatch, dan endpoint nested `get_initial_data/get_chart_update` sudah memiliki implementasi query-based + fallback object-shape (diverifikasi via shadow path, belum ditambahkan ke toggle Stage 2).

Referensi detail status, checklist, dan evidence:
- `docs/PHASE6_COEXISTENCE_DEV_BASELINE.md`
- `docs/PHASE6_DECISION_RECORDS.md`

### Cara Verifikasi Stage 1 (Sudah Tersedia)
1. Start environment:
   ```powershell
   pwsh ./tools/dev-env.ps1 -Action start -PhpRuntime 7.4
   ```
2. Jalankan coexistence smoke:
   ```powershell
   pwsh ./tools/dev-env.ps1 -Action coexistence -PhpRuntime 7.4
   ```
3. (Opsional) verifikasi header marker route shadow:
   ```powershell
   curl.exe -sS -D - -o NUL -H "Host: vms.localhost" http://127.0.0.1:8080/_pilot/auction/health
   ```
4. Stop environment:
   ```powershell
   pwsh ./tools/dev-env.ps1 -Action stop -PhpRuntime 7.4
   ```

### Cara Verifikasi Stage 2 (Route Toggle + Rollback Switch)
1. Pastikan environment aktif:
   ```powershell
   pwsh ./tools/dev-env.ps1 -Action start -PhpRuntime 7.4
   ```
2. Cek status toggle subset:
   ```powershell
   pwsh ./tools/dev-env.ps1 -Action toggle-auction-subset -ToggleMode status -PhpRuntime 7.4
   ```
3. Jalankan validasi otomatis `CX-03` + `CX-04`:
   ```powershell
   pwsh ./tools/dev-env.ps1 -Action coexistence-stage2 -PhpRuntime 7.4 -AuctionLelangId 1
   ```
4. (Opsional) Toggle `ON` manual + cek marker header pilot:
   ```powershell
   pwsh ./tools/dev-env.ps1 -Action toggle-auction-subset -ToggleMode on -PhpRuntime 7.4
   curl.exe -sS -D - -o NUL -H "Host: vms.localhost" http://127.0.0.1:8080/auction/admin/json_provider/get_barang/1
   ```
   - Endpoint pilot read-only tetap harus mengembalikan marker `X-App-Source: pilot-skeleton`.
   - Jika tabel dev `auction` belum tersedia, expected tambahan header: `X-Pilot-Data-Source: degraded-empty` dan `X-Pilot-Data-Status: db-unavailable-or-schema-mismatch` (body tetap `[]` untuk kompatibilitas smoke/consumer minimum).
5. (Opsional, aman tanpa memperluas toggle Stage 2) Verifikasi endpoint nested pilot via shadow path:
   ```powershell
   curl.exe -sS -D - -H "Host: vms.localhost" http://127.0.0.1:8080/_pilot/auction/admin/json_provider/get_initial_data/1/1
   curl.exe -sS -D - -H "Host: vms.localhost" http://127.0.0.1:8080/_pilot/auction/admin/json_provider/get_chart_update/1
   ```
   - Expected marker pilot tetap ada: `X-App-Source: pilot-skeleton`, `X-Pilot-Endpoint`, `X-Pilot-Route-Mode: shadow-route`.
   - Jika DB/schema dev mismatch, expected `HTTP 200` dengan header degradasi `X-Pilot-Data-*` dan body fallback object-shape minimum:
     - `get_initial_data`: `{id,name,subtitle,data,last,time}`
     - `get_chart_update`: `{data,time}`
   - Jalur shadow ini dipakai untuk verifikasi implementasi nested tanpa mengubah scoped toggle Stage 2 existing (`get_barang/get_peserta` tetap satu-satunya route bisnis yang ditoggle).
6. (Opsional) Toggle `OFF` manual + cek marker rollback CI3:
   ```powershell
   pwsh ./tools/dev-env.ps1 -Action toggle-auction-subset -ToggleMode off -PhpRuntime 7.4
   curl.exe -sS -D - -o NUL -H "Host: vms.localhost" http://127.0.0.1:8080/auction/admin/json_provider/get_barang/1
   ```
7. Stop environment (opsional setelah verifikasi):
   ```powershell
   pwsh ./tools/dev-env.ps1 -Action stop -PhpRuntime 7.4
   ```

### Integrasi Skeleton Laravel Final (Default In-Project, Sibling Bind Mount Tetap Opsional)
Gunakan langkah ini untuk maintain/replace source pilot Laravel. Default dev path saat ini adalah in-project `./pilot-app`; opsi sibling repo tetap tersedia via hook bind mount.

Keputusan terbaru Wave B: path dev utama untuk pilot adalah in-project `./pilot-app`. Hook `EPROC_PILOT_APP_BIND_PATH` tetap dipertahankan sebagai opsi jika nanti dipindah ke repo terpisah.

Status terbaru (2026-02-26, next-step Wave B nested endpoint step selesai parsial): `./pilot-app` berhasil diganti in-place menjadi skeleton Laravel 8 (kompatibel PHP 7.4) dengan route kompatibel (`/_pilot/auction/health`, `get_barang`, `get_peserta`) dan kini endpoint nested `get_initial_data`/`get_chart_update` juga tersedia pada route direct + shadow pilot dengan marker header `X-App-Source: pilot-skeleton` tetap muncul.
Hook `EPROC_PILOT_APP_BIND_PATH` tetap dipertahankan (opsional jika nanti pindah repo sibling); workflow route shadow, scoped toggle subset, dan rollback cepat `nginx reload` tidak berubah.
Ringkasan evidence pasca-step nested (2026-02-26): `docker compose -f docker-compose.yml config` PASS, `pwsh ./tools/dev-env.ps1 -Action coexistence -PhpRuntime 7.4` PASS (`CX-01`,`CX-02`), `pwsh ./tools/dev-env.ps1 -Action coexistence-stage2 -PhpRuntime 7.4 -AuctionLelangId 1` PASS (`CX-03`,`CX-04`; `CX-04` marker rollback CI3 PASS dengan HTTP `500` tetap accepted), header dump manual saat toggle `ON` menunjukkan marker pilot tetap muncul pada `/_pilot/auction/health` dan `/auction/admin/json_provider/get_barang/1`, serta verifikasi shadow path `/_pilot/auction/admin/json_provider/get_initial_data/1/1` dan `.../get_chart_update/1` mengembalikan `HTTP 200` dengan header degradasi `X-Pilot-Data-*` + object-shape minimum saat DB/schema dev mismatch (`42S02/1146`).

1. Pilih source pilot Laravel yang akan dipakai.
   - Default saat ini: gunakan in-project `./pilot-app` (sudah terisi skeleton Laravel).
   - Opsi alternatif: repo sibling Laravel final via `EPROC_PILOT_APP_BIND_PATH`.
   - Jika memilih repo sibling, pastikan repo tersebut sudah ada di mesin lokal.
   - Quick-check (opsional, PowerShell):
     ```powershell
     Get-ChildItem -Path C:\Users\Revanza-Home\source\repos -Recurse -File -Filter artisan -ErrorAction SilentlyContinue
     ```
   - Quick-check path in-project target (`./pilot-app`) sebelum klaim integrasi final:
     ```powershell
     Get-ChildItem -Force .\pilot-app
     rg --files .\pilot-app
     ```
   - Jika hasil tidak menunjukkan struktur Laravel (`artisan`, `composer.json`, `bootstrap/`, `routes/`, `public/`) atau kembali placeholder-only, catat blocker eksplisit lalu jalankan smoke baseline (`coexistence`, `coexistence-stage2`) tanpa klaim integrasi final.
2. Ubah path bind `pilot-app` di `.env`:
   ```dotenv
   EPROC_PILOT_APP_BIND_PATH=../nama-repo-laravel-final
   ```
   Atau gunakan path absolut Windows (didukung karena compose memakai bind long-form):
   ```dotenv
   EPROC_PILOT_APP_BIND_PATH=C:\Users\Revanza-Home\source\repos\nama-repo-laravel-final
   ```
3. Validasi compose resolve path dengan config check:
   ```powershell
   docker compose -f docker-compose.yml config
   ```
   - Untuk verifikasi non-destruktif sementara (tanpa edit `.env`), boleh gunakan override session:
     ```powershell
     $env:EPROC_PILOT_APP_BIND_PATH='./pilot-app'
     docker compose -f docker-compose.yml config
     ```
4. Start/restart environment (container `pilot-app` perlu recreate agar mount path baru terpakai):
   ```powershell
   pwsh ./tools/dev-env.ps1 -Action restart -PhpRuntime 7.4 -NoBuild
   ```
5. Verifikasi route coexistence tetap stabil:
   ```powershell
   pwsh ./tools/dev-env.ps1 -Action coexistence -PhpRuntime 7.4
   pwsh ./tools/dev-env.ps1 -Action coexistence-stage2 -PhpRuntime 7.4 -AuctionLelangId 1
   ```
   - Ini juga command evidence minimal untuk sesi verifikasi saat skeleton final belum tersedia di `./pilot-app`.
   - `CX-04` boleh tetap dianggap PASS untuk tahap ini jika marker rollback CI3 valid tetapi HTTP sample CI3 `500` (gap tabel dev `ms_procurement_barang` / `ms_procurement_peserta`).
6. Rollback paling ringan untuk kembali ke placeholder lokal:
   - Kembalikan `.env`:
     ```dotenv
     EPROC_PILOT_APP_BIND_PATH=./pilot-app
     ```
   - Lalu recreate service:
     ```powershell
     pwsh ./tools/dev-env.ps1 -Action restart -PhpRuntime 7.4 -NoBuild
     ```
   - Route rollback subset `get_barang/get_peserta` tetap gunakan toggle existing (`nginx reload only`) dan tidak berubah oleh langkah bind mount ini.

### Target Wave B Stage 3+ (Remaining)
- Implement auth bridge/token verifier untuk endpoint protected (`CX-05`).
- Sambungkan repo Laravel final aktual ke `pilot-app` via `EPROC_PILOT_APP_BIND_PATH`, lalu ganti placeholder traffic secara bertahap.
- Tambah contract/integration test automation + CI jobs untuk endpoint pilot bisnis.

### Acceptance Test Plan Reference
Gunakan test plan `CX-01` s.d. `CX-05` pada `docs/PHASE6_COEXISTENCE_DEV_BASELINE.md`.
- `CX-01` dan `CX-02` sudah punya evidence eksekusi Wave B Stage 1.
- `CX-03` dan `CX-04` sudah punya evidence eksekusi Wave B Stage 2 (routing toggle + rollback marker).
- `CX-05` masih pending (auth bridge belum implement).

## Smoke Check Targets
- `http://vms.localhost:8080/`
- `http://intra.localhost:8080/main/`
- `http://intra.localhost:8080/pengadaan/`

Untuk validasi PHP 8.2, jalankan command yang sama dengan `-PhpRuntime 8.2`.

Smoke command akan gagal jika:
- HTTP status `>= 400`
- halaman mengandung error pattern CI/PHP (fatal/config/database error)

## Healthcheck Coverage
Healthcheck tersedia untuk:
- `webserver` (Nginx route check `vms`)
- `vms-app` (php-fpm syntax check)
- `intra-app` (php-fpm syntax check)
- `pilot-app` (Laravel skeleton php-fpm syntax check)
- `db` (MariaDB `mysqladmin ping`)
- `redis` (`redis-cli ping`)

## Troubleshooting Ringkas
- `error during connect ... EOF` saat `docker compose up`
  - Jalankan ulang per-service:
    ```powershell
    docker compose up -d db redis
    docker compose up -d vms-app intra-app
    docker compose up -d webserver
    ```
- DB container exit saat first boot
  - Lihat log:
    ```powershell
    docker compose logs db
    ```
  - Reset volume:
    ```powershell
    pwsh ./tools/dev-env.ps1 -Action reset
    ```
- Endpoint membuka halaman error CI/PHP
  - Jalankan smoke check:
    ```powershell
    pwsh ./tools/dev-env.ps1 -Action smoke
    ```
  - Cek log:
    ```powershell
    docker compose logs webserver vms-app intra-app
    ```
- `CX-04` rollback marker PASS tapi endpoint `get_barang/get_peserta` mengembalikan `500`
  - Ini bisa terjadi jika tabel sample dev (`ms_procurement_barang` / `ms_procurement_peserta`) belum ada.
  - Verifikasi tetap dapat dilakukan pada marker header:
    - `X-App-Source: ci3-legacy`
    - `X-Coexistence-Route: ci3-legacy-subset`
  - Fallback operasional paling ringan jika reload gagal:
    ```powershell
    docker compose restart webserver
    ```
- Toggle `ON` (pilot) mengembalikan `[]` dengan header `X-Pilot-Data-Source: degraded-empty`
  - Ini menandakan endpoint pilot Laravel sudah mencoba query read-only, tetapi DB/schema dev tidak cocok atau tabel belum tersedia (contoh sesi baseline: `ms_procurement_barang` / `ms_procurement_peserta` tidak ada).
  - Marker coexistence yang tetap harus terlihat:
    - `X-App-Source: pilot-skeleton`
    - `X-Coexistence-Route: pilot-business-toggle`
    - `X-Coexistence-Toggle: auction-json-provider=on`
  - Cek detail warning (opsional, evidence blocker):
    ```powershell
    Get-Content .\pilot-app\storage\logs\laravel.log -Tail 80
    ```
  - Jangan menambah requirement seed data untuk `CX-03/CX-04`; cukup catat blocker eksplisit bila perlu.
- `pilot-app` gagal start setelah mengubah `EPROC_PILOT_APP_BIND_PATH`
  - Penyebab umum: path sibling repo belum ada / typo path absolut.
  - Validasi path di host:
    ```powershell
    Test-Path <nilai-EPROC_PILOT_APP_BIND_PATH>
    ```
  - Validasi compose resolve:
    ```powershell
    docker compose -f docker-compose.yml config
    ```
  - Rollback cepat ke placeholder lokal:
    ```dotenv
    EPROC_PILOT_APP_BIND_PATH=./pilot-app
    ```
    lalu:
    ```powershell
    pwsh ./tools/dev-env.ps1 -Action restart -PhpRuntime 7.4 -NoBuild
    ```
- `php` tidak dikenali saat `Action lint`
  - Pastikan PHP CLI tersedia di `PATH`.
  - Validasi:
    ```powershell
    php -v
    ```
- `Action test` gagal karena service `vms-app` tidak bisa di-exec
  - Cek status service:
    ```powershell
    pwsh ./tools/dev-env.ps1 -Action status -PhpRuntime 7.4
    ```
  - Start ulang environment:
    ```powershell
    pwsh ./tools/dev-env.ps1 -Action restart -PhpRuntime 7.4
    ```
