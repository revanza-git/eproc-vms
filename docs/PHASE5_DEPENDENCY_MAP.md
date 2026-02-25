# Phase 5 Dependency Map (Post Wave 1)

## Architecture Change Summary
- Tujuan wave 1: menghilangkan duplikasi runtime cron di dua codebase tanpa mengubah kontrak pemanggilan `class cron`.
- Hasil:
  - `vms/app/jobs/cron_core.php` dan `intra/pengadaan/cron_core.php` menjadi thin adapter.
  - Runtime logic dipusatkan ke `shared/legacy/cron_runtime.php`.

## Dependency Graph
```text
vms cron files (cron_blacklist, cron_dpt, ...)
  -> vms/app/jobs/cron_core.php (adapter)
     -> shared/legacy/cron_runtime.php
     -> vms/app/application/config/database.php
     -> vms/app/jobs/cron_mail.php

intra/pengadaan cron files (cron_blacklist, cron_dpt, ...)
  -> intra/pengadaan/cron_core.php (adapter)
     -> shared/legacy/cron_runtime.php
     -> intra/pengadaan/application/config/database.php
     -> intra/pengadaan/cron_mail.php
```

## Shared Runtime Surface
`shared/legacy/cron_runtime.php` menyediakan:
- `shared_cron_default_db_config()` untuk normalisasi config DB default.
- `SharedCronRuntime::query()`
- `SharedCronRuntime::execute()`
- `SharedCronRuntime::num_rows()`
- `SharedCronRuntime::row_array()`
- `SharedCronRuntime::result()`
- `SharedCronRuntime::escape_identifier()`
- `SharedCronRuntime::send_email()`

## Backward Compatibility Notes
- `class cron` tetap tersedia di masing-masing app.
- Signature method yang dipakai job existing tidak berubah.
- SQL/business rule di file cron lain tidak diubah.

## Risk and Rollback Map
- Risiko utama:
  - include shared file path.
  - inisialisasi DB via `$db['default']`.
- Rollback cepat:
  - revert `shared/legacy/cron_runtime.php`
  - revert dua file adapter `cron_core.php`.
