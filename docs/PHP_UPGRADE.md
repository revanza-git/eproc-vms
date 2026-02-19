# PHP Upgrade Readiness (Phase 3)

Last Updated: `2026-02-19`

## Scope
- Framework tetap `CodeIgniter 3` selama transisi runtime.
- Operasional default tetap `PHP 7.4`.
- Validasi compatibility dilakukan di `PHP 8.2` tanpa big-bang rewrite.

## Runtime Images
- Legacy runtime: `docker/php/Dockerfile` (`php:7.4-fpm`, termasuk `mcrypt` + `redis` ext).
- Modern validation runtime: `docker/php/Dockerfile.php82` (`php:8.2-fpm`, `redis` ext aktif, tanpa `mcrypt`).
- Dual-runtime compose:
  - `docker-compose.yml`
  - `docker-compose.php82.yml`

## Compatibility Checklist (PHP 7.4 vs PHP 8.2)
| Area | PHP 7.4 | PHP 8.2 | Status Phase 3 | Notes |
|---|---|---|---|---|
| Legacy `mysql_*` API | Deprecated | Removed/Fatal | Mitigated | Cron legacy dipindah ke `mysqli` |
| `each()` API | Deprecated | Removed/Fatal | Mitigated (core path) | Patched di `Security` dan `MX Modules` |
| Dynamic property deprecation | Notice/low impact | Deprecated warning | Mitigated for smoke | Development error reporting menonaktifkan `E_DEPRECATED` |
| Redis extension | Required by session/runtime checks | Required | Mitigated | Aktif pada image 7.4 dan 8.2 |
| `mcrypt` extension | Tersedia di image | Umumnya tidak tersedia | Open gap (non-blocking smoke) | Alur yang bergantung `mcrypt` perlu eliminasi bertahap |
| DB driver | `mysqli` aktif | `mysqli` aktif | Pass | Config app tetap `dbdriver = mysqli` |

## Runtime Blocker Inventory (Actual)

### Fixed in Phase 3
- `mysql_*` di cron legacy:
  - `vms/app/jobs/cron_core.php`
  - `vms/app/jobs/cron_dpt.php`
  - `vms/app/jobs/cron_blacklist.php`
  - `intra/pengadaan/cron_core.php`
  - `intra/pengadaan/cron_dpt.php`
  - `intra/pengadaan/cron_blacklist.php`
- `each()` pada runtime path utama:
  - `vms/app/system/core/Security.php`
  - `intra/main/system/core/Security.php`
  - `intra/pengadaan/system/core/Security.php`
  - `vms/app/application/third_party/MX/Modules.php`
  - `intra/pengadaan/application/third_party/MX/Modules.php`
- Deprecation noise yang memicu false-negative smoke di PHP 8.2:
  - `vms/app/index.php`
  - `intra/main/index.php`
  - `intra/pengadaan/index.php`

### Remaining Gaps (Tracked, non-blocking for Phase 3 gate)
- Legacy `mysql` driver file CI3 masih ada di vendor core (tidak aktif karena app memakai `mysqli`):
  - `vms/app/system/database/drivers/mysql/*`
  - `intra/main/system/database/drivers/mysql/*`
  - `intra/pengadaan/system/database/drivers/mysql/*`
- `each()` masih ada di library XMLRPC legacy:
  - `vms/app/system/libraries/Xmlrpc*.php`
  - `intra/main/system/libraries/Xmlrpc*.php`
  - `intra/pengadaan/system/libraries/Xmlrpc*.php`
- `create_function()` masih ada di dompdf legacy:
  - `vms/app/system/plugins/dompdf*/*`
  - `vms/app/system/libraries/dompdf/*`
  - `intra/main/application/third_party/dompdf2/*`
  - `intra/pengadaan/system/plugins/dompdf*/*`
  - `intra/pengadaan/system/libraries/dompdf/*`

## Dual-Runtime Commands (Repeatable)

### PHP 7.4
```powershell
pwsh ./tools/dev-env.ps1 -Action start -PhpRuntime 7.4
pwsh ./tools/dev-env.ps1 -Action smoke -PhpRuntime 7.4
pwsh ./tools/dev-env.ps1 -Action deps -PhpRuntime 7.4
pwsh ./tools/dev-env.ps1 -Action cron -PhpRuntime 7.4
```

### PHP 8.2
```powershell
pwsh ./tools/dev-env.ps1 -Action start -PhpRuntime 8.2
pwsh ./tools/dev-env.ps1 -Action smoke -PhpRuntime 8.2
pwsh ./tools/dev-env.ps1 -Action deps -PhpRuntime 8.2
pwsh ./tools/dev-env.ps1 -Action cron -PhpRuntime 8.2
```

Stop environment:
```powershell
pwsh ./tools/dev-env.ps1 -Action stop -PhpRuntime 7.4
```

## Rollback Strategy (Jika issue di PHP 8.2)
1. Kembali ke runtime stabil:
   - `pwsh ./tools/dev-env.ps1 -Action start -PhpRuntime 7.4`
2. Jalankan smoke + dependency + cron check di 7.4 untuk konfirmasi pemulihan:
   - `smoke`, `deps`, `cron`
3. Catat root cause dan scope dampak sebelum re-attempt 8.2.
4. Re-attempt 8.2 hanya setelah fix terverifikasi via command matrix di atas.
