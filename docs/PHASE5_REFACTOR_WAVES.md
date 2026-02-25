# Phase 5 Refactor Waves (Incremental, Non-Breaking)

## Guiding Principle
- Refactor dilakukan bertahap dengan pola:
  - `high value + low risk` dulu.
  - Interface publik tetap kompatibel (no big-bang).
  - Setiap wave wajib lolos `lint`, `test`, `smoke`.

## Wave Sequence

### Wave 1 (Completed) - Shared Cron Runtime Extraction
- Domain: Cron / scheduled jobs.
- In Scope:
  - Ekstraksi logic DB runtime + query helpers + email helper dari:
    - `vms/app/jobs/cron_core.php`
    - `intra/pengadaan/cron_core.php`
  - Komponen shared baru:
    - `shared/legacy/cron_runtime.php`
- Out of Scope:
  - Perubahan behavior business logic `cron_blacklist`, `cron_dpt`, `cron_cek_expired`.
  - Perubahan struktur tabel/query domain.
- Risk:
  - Path include shared file tidak ditemukan.
  - Inisialisasi DB config tidak konsisten.
- Mitigasi:
  - Constructor `cron` tetap ada dan signature kompatibel.
  - `shared_cron_default_db_config()` menjaga fallback default host/port/charset.
- Rollback:
  - Revert tiga file wave 1 (`cron_core` x2 + shared runtime).
  - Tidak perlu rollback data DB karena perubahan hanya code-level runtime.

### Wave 2 (Planned) - Shared App Helpers
- Domain: Common helper layer.
- In Scope:
  - Satukan `safe_log_helper.php` lintas app ke shared source.
  - Satukan `utility_helper_old.php` lintas app ke shared source.
- Out of Scope:
  - Rewrite helper date modern (`utility_helper.php`) yang sudah drift.
- Risk:
  - Load order helper CI3/HMVC.
- Rollback:
  - Restore helper file local app masing-masing.

### Wave 3 (Planned) - Admin/Auction Exact-Match Module Consolidation
- Domain: HMVC `admin` + `auction` yang hash-identik.
- In Scope:
  - Introduce shared module package untuk controller/model identik dulu.
  - Alias include/wrapper pada app-specific path.
- Out of Scope:
  - File `diff` dan file backup/experimental.
- Risk:
  - Coupling pada path view/module loader.
  - Potensi side effect autoload precedence.
- Rollback:
  - Matikan alias wrapper, kembali ke file lokal.

### Wave 4 (Planned) - Divergent Query Path Harmonization
- Domain: model/controller `diff` berquery tinggi.
- In Scope:
  - Harmonisasi bertahap pada file berbeda namun domain sama (contoh `Pengadaan_model`, `Kontrak_model`, `K3_model`).
  - Parameter binding/safety pattern standardisasi.
- Out of Scope:
  - Perubahan skema DB.
- Risk:
  - Regression logic bisnis procurement.
- Rollback:
  - Revert per-domain kecil (1 modul per PR/session).

## Sequence Rationale
1. Wave 1 dipilih karena dampak tinggi (core runtime dipakai semua cron) dan risiko rendah (API class `cron` dipertahankan).
2. Wave 2 masih rendah risiko karena helper identik.
3. Wave 3 mulai medium-risk karena menyentuh HMVC loader.
4. Wave 4 highest-risk di Phase 5 karena menyentuh query behavior bisnis.
