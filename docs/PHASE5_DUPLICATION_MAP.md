# Phase 5 Duplication Mapping (`vms` vs `intra/pengadaan`)

## Scope and Method
- Scope folder:
  - `vms/app/application/*` vs `intra/pengadaan/application/*`
  - `vms/app/jobs/*` vs `intra/pengadaan/cron_*`
- Method:
  - Relative-path comparison + SHA256 hash untuk identifikasi `same` vs `diff`.
  - Validasi tambahan untuk file query-heavy (`SELECT/UPDATE/INSERT/DELETE`, `$this->db`, `->query()`).

## High-Level Mapping Summary
| Area | Same | Diff | VMS Only | Pengadaan Only | Notes |
|---|---:|---:|---:|---:|---|
| Modules (`application/modules`) | 233 | 158 | 9 | 7 | Duplikasi besar ada di controller/model/view HMVC |
| App helpers (`application/helpers`) | 3 | 1 | 0 | 1 | `safe_log_helper.php` & `utility_helper_old.php` identik |
| App controllers (`application/controllers`) | 1 | 1 | 4 | 1 | Perbedaan utama: `Migrate_passwords.php` |
| Cron (`jobs` vs root `cron_*`) | 4 | 2 | 0 | 0 | `cron_core.php` refactor Wave 1 |

## Detailed Mapping

### 1. Controller Duplication
- `application/controllers`:
  - `same`: `index.html`
  - `diff`: `Migrate_passwords.php`
  - `vms-only`: `Main.php`, `Reset_password.php`, `Utilities.php`, `Welcome.php`
  - `pengadaan-only`: `Migrate_year_anggaran.php`
- `application/modules/*/controllers`:
  - `same`: 44 file
  - `diff`: 34 file
  - domain dengan duplikasi tinggi:
    - `admin`: `same=66`, `diff=22`
    - `auction`: `same=68`, `diff=8`
    - `pengadaan`: `same=25`, `diff=16`

### 2. Model Duplication
- `application/models`: hanya `index.html`.
- `application/modules/*/models`:
  - `same`: 22 file
  - `diff`: 19 file
- Contoh model identik (query path kandidat share):
  - `admin/models/report/Admin_report_model.php`
  - `admin/models/evaluasi/Evaluasi_model.php`
  - `agen/models/Agen_model.php`
  - `katalog/models/Katalog_model.php`
  - `pengadaan/models/report/Report_pengadaan_model.php`
- Contoh model belum identik (butuh treatment domain-specific):
  - `pengadaan/models/Pengadaan_model.php`
  - `kontrak/models/Kontrak_model.php`
  - `k3/models/K3_model.php`

### 3. Helper Duplication
- `same`:
  - `safe_log_helper.php`
  - `utility_helper_old.php`
  - `index.html`
- `diff`:
  - `utility_helper.php` (versi pengadaan sudah tambah guard invalid date)
- `pengadaan-only`:
  - `env_helper.php`

### 4. Query Path Duplication
- Cron query path:
  - `same`: `cron_blacklist.php`, `cron_dpt.php`, `cron_mail.php`, `cron_cek_pengadaan.php`
  - `diff`: `cron_core.php`, `cron_cek_expired.php`
- HMVC query-heavy file yang identik lintas app (sample):
  - `admin/controllers/Admin_assessment.php`
  - `admin/controllers/Admin_user.php`
  - `auction/controllers/Auction.php`
  - `auction/models/Json_provider_model.php`
  - `admin/models/assessment/Admin_assessment_model.php`

### 5. Utility Duplication
- Utility backend:
  - `safe_log_helper.php` identik (kandidat share langsung).
  - `utility_helper_old.php` identik (kandidat share langsung).
- Utility cron runtime:
  - Sebelum Wave 1: logic DB/query/mail identik diduplikasi di dua `cron_core.php`.
  - Setelah Wave 1: dipusatkan ke `shared/legacy/cron_runtime.php`.

## Candidate Shared Components (Prioritas)
| Priority | Candidate | Value | Risk | Status |
|---|---|---|---|---|
| P0 | Shared cron runtime (`query/execute/result/send_email`) | Tinggi, mengurangi duplikasi core cron lintas app | Rendah (interface `cron` dipertahankan) | Implemented (Wave 1) |
| P0 | `safe_log_helper.php` jadi source tunggal shared helper | Tinggi, sinkronisasi logging lintas app | Rendah | Planned |
| P1 | `utility_helper_old.php` jadi source tunggal shared helper | Sedang, kurangi drift helper legacy | Rendah | Planned |
| P1 | Shared package untuk `admin` controller/model yang hash-identik | Tinggi, domain paling banyak duplikasi | Sedang (routing/load order HMVC) | Planned |
| P2 | Harmonisasi `utility_helper.php` (new guard behavior) | Sedang, konsistensi utility date | Sedang (potensi behavior change) | Planned |
| P2 | Konvergensi `Migrate_passwords.php` | Sedang, konsistensi security migration tool | Sedang | Planned |
