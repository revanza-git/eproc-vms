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

## Phase 6 Coexistence Baseline (Wave A -> Wave B Stage 1)

Status saat ini: `Stage 1 implemented (pilot placeholder + shadow route + coexistence smoke); Stage 2 route toggle pending`

Wave A menyiapkan baseline desain/checklist/test plan. Wave B Stage 1 sudah menambahkan proof runtime coexistence dev di repo ini:
- service placeholder `pilot-app` pada compose (non-bisnis, untuk health/shadow wiring),
- route shadow `/_pilot/auction/*` di Nginx (`vms.localhost`),
- action `coexistence` di `tools/dev-env.ps1` untuk `CX-01` + `CX-02`.

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

### Target Wave B Stage 2 (Pending)
- Implement route toggle subset endpoint `auction` (strangler path) dengan rollback cepat ke CI3.
- Ganti placeholder `pilot-app` ke skeleton Laravel final (repo ini atau repo terpisah sesuai `DR-P6-005` review).
- Tambah contract/integration test automation untuk endpoint pilot bisnis.

### Acceptance Test Plan Reference
Gunakan test plan `CX-01` s.d. `CX-05` pada `docs/PHASE6_COEXISTENCE_DEV_BASELINE.md`.
- `CX-01` dan `CX-02` sudah punya evidence eksekusi Wave B Stage 1.
- `CX-03` s.d. `CX-05` masih pending.

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
