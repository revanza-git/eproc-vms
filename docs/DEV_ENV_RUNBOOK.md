# Dev Environment Runbook (Phase 1)

## Scope
Runbook ini untuk operasional harian environment development CI3 (`vms`, `intra/main`, `intra/pengadaan`) berbasis Docker Compose, termasuk proof coexistence Phase 6 Wave B (`pilot-app` placeholder dev).

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

Status saat ini: `Stage 2 implemented untuk scoped route toggle (get_barang/get_peserta) + rollback switch via nginx reload; sibling bind-mount hook untuk skeleton Laravel final ready; CX-05 auth bridge pending`

Wave A menyiapkan baseline desain/checklist/test plan. Wave B Stage 1/2 sudah menambahkan proof runtime coexistence dev di repo ini:
- service placeholder `pilot-app` pada compose (non-bisnis, untuk health/shadow wiring),
- bind mount path `pilot-app` configurable via `.env` (`EPROC_PILOT_APP_BIND_PATH`, default `./pilot-app`) untuk integrasi repo sibling Laravel final,
- route shadow `/_pilot/auction/*` di Nginx (`vms.localhost`),
- toggle subset endpoint bisnis `auction/admin/json_provider/{get_barang|get_peserta}` via include file Nginx aktif,
- action `coexistence` (`CX-01`,`CX-02`) dan `coexistence-stage2` (`CX-03`,`CX-04`) di `tools/dev-env.ps1`.

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
5. (Opsional) Toggle `OFF` manual + cek marker rollback CI3:
   ```powershell
   pwsh ./tools/dev-env.ps1 -Action toggle-auction-subset -ToggleMode off -PhpRuntime 7.4
   curl.exe -sS -D - -o NUL -H "Host: vms.localhost" http://127.0.0.1:8080/auction/admin/json_provider/get_barang/1
   ```
6. Stop environment (opsional setelah verifikasi):
   ```powershell
   pwsh ./tools/dev-env.ps1 -Action stop -PhpRuntime 7.4
   ```

### Integrasi Skeleton Laravel Final via Sibling Bind Mount (Dev, Minimal Workflow Change)
Gunakan langkah ini saat repo Laravel final sudah tersedia sebagai sibling repo (terpisah dari repo ini).

1. Pastikan repo sibling Laravel final sudah ada di mesin lokal.
   - Jika belum ada, status tetap `hook-ready` (belum bisa claim integrasi final tersambung).
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
4. Start/restart environment (container `pilot-app` perlu recreate agar mount path baru terpakai):
   ```powershell
   pwsh ./tools/dev-env.ps1 -Action restart -PhpRuntime 7.4 -NoBuild
   ```
5. Verifikasi route coexistence tetap stabil:
   ```powershell
   pwsh ./tools/dev-env.ps1 -Action coexistence -PhpRuntime 7.4
   pwsh ./tools/dev-env.ps1 -Action coexistence-stage2 -PhpRuntime 7.4 -AuctionLelangId 1
   ```
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
- `pilot-app` (placeholder php-fpm syntax check)
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
