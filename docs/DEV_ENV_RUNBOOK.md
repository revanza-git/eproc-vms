# Dev Environment Runbook (Phase 1)

## Scope
Runbook ini untuk operasional harian environment development CI3 (`vms`, `intra/main`, `intra/pengadaan`) berbasis Docker Compose.

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
- Check koneksi DB + Redis di runtime aktif:
  ```powershell
  pwsh ./tools/dev-env.ps1 -Action deps -PhpRuntime 7.4
  ```
- Check minimal cron runtime (`vms` cron core):
  ```powershell
  pwsh ./tools/dev-env.ps1 -Action cron -PhpRuntime 7.4
  ```

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
