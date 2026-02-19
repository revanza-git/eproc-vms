# Revamp Progress Checklist - eproc-vms

## Usage
- Checklist ini dipakai sebagai tracker operasional.
- Ubah status dengan:
  - `[ ]` belum mulai
  - `[x]` selesai
- Jika ada blocker, tulis di bagian **Active Blockers**.

## Snapshot
- Last Updated: `February 19, 2026`
- Overall Status: `Phase 3 Completed`

---

## Phase 0 - Baseline & Inventory
- [x] Buat dokumen plan (`docs/REVAMP_PLAN.md`)
- [x] Buat checklist tracking (`docs/REVAMP_CHECKLIST.md`)
- [x] Finalisasi daftar baseline issue prioritas tinggi (`docs/BASELINE_ISSUES.md`)
- [x] Finalisasi scope Phase 1 dengan acceptance criteria (`docs/REVAMP_PLAN.md`)
- [x] Confirmation check Phase 0 (kelengkapan dokumen dan alignment objective)
- [x] Testing evidence Phase 0 dicatat (validasi referensi dokumen/checklist)

## Phase 1 - Stabilize Development Environment
- [x] Validasi docker compose up/down end-to-end
- [x] Standarisasi setup `.env` root, `vms`, dan `intra`
- [x] Pastikan konfigurasi app minimum tersedia untuk onboarding dev
- [x] Dokumentasi runbook start/stop/reset environment
- [x] Tambahkan helper command (opsional: `Makefile`/`justfile`/script ps1)
- [x] Tambahkan healthcheck service penting (web, php-fpm, db, redis)
- [x] Confirmation check Phase 1 terhadap acceptance criteria
- [x] Testing evidence Phase 1 dicatat (compose lifecycle + healthcheck + smoke)

## Phase 2 - Security & Hygiene Foundation
- [x] Audit dan cleanup secret yang tidak boleh ada di repo
- [x] Rotasi credential yang pernah ter-expose
- [x] Aktifkan dan samakan baseline CSRF/session policy antar app
- [x] Prioritaskan perbaikan query berisiko tinggi (input -> raw SQL)
- [x] Rapikan `.gitignore` untuk artefak coverage/build/backup
- [x] Upgrade/ketatkan scanner secret internal
- [x] Confirmation check Phase 2 (security baseline terpenuhi)
- [x] Testing evidence Phase 2 dicatat (secret scan + CSRF/session regression)

### Confirmation check Phase 2
- Tindakan cleanup secret sudah diterapkan pada file terdampak (`vms/app/tests`, `intra/tests`, config `intra/pengadaan`) dan literal kredensial terekspos sudah dihapus dari code path aktif.
- Dokumentasi rotasi credential + placeholder aman tersedia di `docs/SECURITY_CREDENTIAL_ROTATION.md`.
- Baseline CSRF/session policy sudah disamakan lintas app via config env-driven (`CSRF_PROTECTION`, `SESSION_MATCH_IP`, `COOKIE_HTTPONLY`) di:
  - `vms/app/application/config/config.php`
  - `intra/main/application/config/config.php`
  - `intra/pengadaan/application/config/config.php`
- Quick-win query safety pada jalur berisiko tinggi sudah diperbaiki (input route -> parameterized query) di `intra/main/application/controllers/Input.php`.
- Internal scanner secret ditingkatkan di `scripts/scan_secrets.php` dan repo hygiene untuk artefak/noise diperbarui di `.gitignore`.

### Testing evidence Phase 2
| Date | Command | Result | File/Area |
|---|---|---|---|
| 2026-02-19 | `php scripts/scan_secrets.php` | PASS (`OK: no obvious hardcoded secrets detected`) | `scripts/scan_secrets.php`, `vms/app/tests`, `intra/tests`, repo tracked files |
| 2026-02-19 | `php scripts/check_csrf_session_baseline.php` | PASS (`CSRF/session baseline is consistent across apps`) | `vms/app/application/config/config.php`, `intra/main/application/config/config.php`, `intra/pengadaan/application/config/config.php` |
| 2026-02-19 | `php scripts/check_query_safety.php` | PASS (`Sample high-risk query paths use parameter binding`) | `intra/main/application/controllers/Input.php`, `intra/main/application/models/Main_model.php` |
| 2026-02-19 | `php -l` (lint) untuk file PHP yang diubah | PASS (no syntax errors) | config/security scripts + controller/model + test utilities |

## Phase 3 - Runtime Modernization Path
- [x] Buat compatibility checklist PHP 7.4 -> target runtime modern
- [x] Identifikasi blocker runtime (contoh: `mysql_*`, API deprecated)
- [x] Refactor script cron legacy ke driver modern (`mysqli`/CI DB)
- [x] Jalankan smoke test pada runtime saat ini
- [x] Jalankan smoke test pada runtime target
- [x] Catat gap dan rollback strategy
- [x] Confirmation check Phase 3 (compatibility scope tervalidasi)
- [x] Testing evidence Phase 3 dicatat (smoke 7.4 + smoke target + cron check)

### Confirmation check Phase 3
- Mekanisme dual-runtime sudah tersedia dan repeatable lewat `tools/dev-env.ps1` dengan parameter `-PhpRuntime 7.4|8.2`.
- Compatibility checklist dan runtime matrix 7.4 vs 8.2 terdokumentasi di `docs/PHP_UPGRADE.md`.
- Blocker prioritas tinggi runtime sudah direfactor:
  - Migrasi `mysql_*` cron legacy ke `mysqli` + query execution yang aman di `vms/app/jobs/*` dan `intra/pengadaan/cron_*`.
  - Hot path fatal `each()` sudah dihilangkan dari `Security` core dan `MX Modules`.
- Dependency runtime penting (`mysqli`, `redis`) tervalidasi pada kedua runtime melalui action `deps`.
- Rollback strategy eksplisit untuk fallback ke 7.4 sudah dicatat di `docs/PHP_UPGRADE.md`.

### Testing evidence Phase 3
| Date | Command | Result | File/Area |
|---|---|---|---|
| 2026-02-19 | `php scripts/check_php82_blockers.php` | PASS (`no high-severity PHP 8.2 blockers found in scoped runtime paths`) | `scripts/check_php82_blockers.php`, cron legacy + Security + MX runtime path |
| 2026-02-19 | `pwsh ./tools/dev-env.ps1 -Action start -PhpRuntime 7.4` | PASS | Docker runtime 7.4 (`docker/php/Dockerfile`) |
| 2026-02-19 | `pwsh ./tools/dev-env.ps1 -Action smoke -PhpRuntime 7.4` | PASS (`vms/main/pengadaan endpoint 200`) | Nginx routing + 3 app endpoint minimum |
| 2026-02-19 | `pwsh ./tools/dev-env.ps1 -Action deps -PhpRuntime 7.4` | PASS (`DB query + Redis ping`) | DB/Redis connectivity from `vms-app` |
| 2026-02-19 | `pwsh ./tools/dev-env.ps1 -Action cron -PhpRuntime 7.4` | PASS (`cron runtime DB check`) | `vms/app/jobs/cron_core.php` |
| 2026-02-19 | `pwsh ./tools/dev-env.ps1 -Action start -PhpRuntime 8.2` | PASS | Docker runtime 8.2 (`docker/php/Dockerfile.php82`, `docker-compose.php82.yml`) |
| 2026-02-19 | `pwsh ./tools/dev-env.ps1 -Action smoke -PhpRuntime 8.2` | PASS (`vms/main/pengadaan endpoint 200`) | Nginx routing + 3 app endpoint minimum |
| 2026-02-19 | `pwsh ./tools/dev-env.ps1 -Action deps -PhpRuntime 8.2` | PASS (`DB query + Redis ping`) | DB/Redis connectivity from `vms-app` |
| 2026-02-19 | `pwsh ./tools/dev-env.ps1 -Action cron -PhpRuntime 8.2` | PASS (`cron runtime DB check`) | `vms/app/jobs/cron_core.php` |
| 2026-02-19 | `php -l` untuk file PHP yang diubah | PASS (no syntax errors) | Cron refactor + Security/MX patch + runtime checker scripts |

## Phase 4 - Quality Gates & Automation
- [ ] Definisikan command standar `lint`, `test`, `smoke`
- [ ] Pastikan test bootstrap bisa dijalankan konsisten di dev
- [ ] Buat pipeline CI minimal (build + smoke)
- [ ] Tambahkan status check wajib sebelum merge (jika branch protection aktif)
- [ ] Dokumentasi troubleshooting test/CI
- [ ] Confirmation check Phase 4 (quality gate aktif)
- [ ] Testing evidence Phase 4 dicatat (pipeline run PASS)

## Phase 5 - Medium-Term Refactor Track
- [ ] Mapping duplikasi modul `vms` vs `intra/pengadaan`
- [ ] Tentukan candidate shared components
- [ ] Susun urutan refactor per domain (high value first)
- [ ] Jalankan refactor bertahap dengan regression check
- [ ] Dokumentasi perubahan arsitektur dan dependency map
- [ ] Confirmation check Phase 5 (refactor scope & output tervalidasi)
- [ ] Testing evidence Phase 5 dicatat (regression test area terdampak)

## Phase 6 - Framework Migration (Out of CI3)
- [x] Tetapkan framework target (decision record) - Laravel (2026-02-20)
- [ ] Tetapkan migration pattern (strangler/modular replacement)
- [ ] Definisikan boundary domain prioritas migrasi pertama
- [ ] Siapkan arsitektur coexistence (routing CI3 + app baru)
- [ ] Siapkan auth/session strategy lintas aplikasi
- [ ] Siapkan data access strategy (shared DB vs anti-corruption layer)
- [ ] Migrasikan domain pilot pertama ke framework baru
- [ ] Jalankan UAT domain pilot dan rollback drill
- [ ] Rencanakan dekomisioning modul CI3 yang tergantikan
- [ ] Confirmation check Phase 6 (pilot migration readiness/cutover plan)
- [ ] Testing evidence Phase 6 dicatat (contract + UAT + rollback drill)

---

## Active Blockers
- Tidak ada blocker aktif yang menghentikan gate Phase 3.
- Gap residual (non-blocking, ditrack untuk phase lanjut):
  - `each()` di XMLRPC legacy library.
  - `create_function()` di dompdf legacy.
  - Driver `mysql` bawaan CI3 masih ada (tidak aktif, app memakai `mysqli`).

## Session Log Template
Gunakan format ini setiap selesai sesi kerja:

```
Date:
Scope:
Completed:
- 
Next:
- 
Blockers:
- 
```

## Phase Validation Log (Mandatory)
| Phase | Date | Confirmation Check | Testing Executed | Result | Evidence |
|---|---|---|---|---|---|
| Phase 0 | 2026-02-20 | Dokumen baseline + acceptance criteria lengkap | Validasi referensi antar dokumen/checklist | PASS | `docs/REVAMP_PLAN.md`, `docs/BASELINE_ISSUES.md` |
| Phase 1 | 2026-02-19 | Acceptance criteria environment terpenuhi | Compose lifecycle + healthcheck + smoke endpoint minimum | PASS | Session Log entry Phase 1 + `docs/DEV_ENV_RUNBOOK.md` |
| Phase 2 | 2026-02-19 | Security baseline policy diterapkan (secret cleanup, credential rotation log, CSRF/session baseline, query quick-win, scanner hardening) | Secret scan + CSRF/session baseline regression + sample query safety check + lint file terdampak | PASS | `docs/SECURITY_CREDENTIAL_ROTATION.md`, `scripts/scan_secrets.php`, `scripts/check_csrf_session_baseline.php`, `scripts/check_query_safety.php` |
| Phase 3 | 2026-02-19 | Dual-runtime mechanism + compatibility scope tervalidasi, blocker high priority direfactor, gap+rollback terdokumentasi | Runtime smoke 7.4 + runtime smoke 8.2 + DB/Redis dependency check + cron runtime check + blocker scan + lint | PASS | `docs/PHP_UPGRADE.md`, `tools/dev-env.ps1`, `docker-compose.php82.yml`, `vms/app/jobs/*`, `intra/pengadaan/cron_*`, `scripts/check_php82_blockers.php` |
| Phase 4 | TBD | TBD | TBD | TBD | TBD |
| Phase 5 | TBD | TBD | TBD | TBD | TBD |
| Phase 6 | TBD | TBD | TBD | TBD | TBD |

## Session Log
Date: February 19, 2026
Scope: Phase 3 - Runtime Modernization Path
Completed:
- Compatibility matrix PHP 7.4 vs 8.2 dan runtime guidance dilengkapi di `docs/PHP_UPGRADE.md`.
- Refactor blocker prioritas tinggi: cron legacy `mysql_*` dipindah ke `mysqli` dengan helper execution aman di `vms` dan `intra/pengadaan`.
- Fatal risk `each()` pada runtime path utama (`Security`, `MX Modules`) dipatch agar kompatibel PHP 8.2.
- Mekanisme dual-runtime dibuat repeatable (`docker-compose.php82.yml`, `tools/dev-env.ps1 -PhpRuntime 7.4|8.2`).
- Tambahan action validasi runtime (`smoke`, `deps`, `cron`) dan checker blocker (`scripts/check_php82_blockers.php`) dijalankan.
- Smoke + dependency + cron check PASS di kedua runtime (7.4 dan 8.2).
Next:
- Lanjut ke Phase 4 (quality gates & automation) dengan baseline command yang sudah stabil.
Blockers:
- Tidak ada blocker aktif untuk completion gate Phase 3; residual gap dicatat sebagai non-blocking di `docs/PHP_UPGRADE.md`.

Date: February 19, 2026
Scope: Phase 2 - Security & Hygiene Foundation
Completed:
- Secret audit/cleanup: hardcoded credential literal dihapus dari script test dan diganti env-based config.
- Credential rotation baseline terdokumentasi dengan placeholder aman (`docs/SECURITY_CREDENTIAL_ROTATION.md`).
- Baseline CSRF/session disamakan lintas `vms`, `intra/main`, `intra/pengadaan` (env-driven policy).
- Query berisiko tinggi (input route -> raw SQL) diparameterisasi pada `show_riwayat_pengadaan` dan sample search flow diperketat.
- `.gitignore` dirapikan untuk artefak coverage/build/backup + file sensitif.
- Secret scanner internal ditingkatkan dan ditambah regression checker (`scripts/check_csrf_session_baseline.php`, `scripts/check_query_safety.php`).
Next:
- Lanjut ke Phase 3 (runtime modernization path) sesuai checklist.
Blockers:
- Tidak ada blocker aktif untuk completion gate Phase 2.

Date: February 19, 2026
Scope: Phase 1 - Stabilize Development Environment
Completed:
- Docker Compose lifecycle validated (`up -d --build` and `down`) with service healthchecks.
- Environment templates standardized across root/`vms`/`intra`.
- Missing minimum app config added for `vms` and `intra/main` onboarding.
- Dev runbook and helper command (`tools/dev-env.ps1`) added.
- Smoke check endpoint minimum passed (`vms`, `intra/main`, `intra/pengadaan`).
Next:
- Lanjut ke Phase 2 tasks (security & hygiene baseline).
Blockers:
- Tidak ada blocker aktif untuk acceptance criteria Phase 1.
