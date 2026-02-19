# Dev Environment Runbook (Phase 1)

## Scope
Runbook ini untuk operasional harian environment development CI3 (`vms`, `intra/main`, `intra/pengadaan`) berbasis Docker Compose.

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
  pwsh ./tools/dev-env.ps1 -Action start
  ```
- Tanpa build image ulang:
  ```powershell
  pwsh ./tools/dev-env.ps1 -Action start -NoBuild
  ```

## Stop Environment
```powershell
pwsh ./tools/dev-env.ps1 -Action stop
```

## Reset Environment (Destructive)
Menghapus container + network + volume DB lalu build ulang.
```powershell
pwsh ./tools/dev-env.ps1 -Action reset
```

## Operational Commands
- Status service:
  ```powershell
  pwsh ./tools/dev-env.ps1 -Action status
  ```
- Tail logs semua service:
  ```powershell
  pwsh ./tools/dev-env.ps1 -Action logs
  ```
- Tail logs service tertentu:
  ```powershell
  pwsh ./tools/dev-env.ps1 -Action logs -Service webserver
  ```
- Smoke check endpoint minimum:
  ```powershell
  pwsh ./tools/dev-env.ps1 -Action smoke
  ```

## Smoke Check Targets
- `http://vms.localhost:8080/`
- `http://intra.localhost:8080/main/`
- `http://intra.localhost:8080/pengadaan/`

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
