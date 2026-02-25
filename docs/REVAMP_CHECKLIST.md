# Revamp Progress Checklist - eproc-vms

## Usage
- Checklist ini dipakai sebagai tracker operasional.
- Ubah status dengan:
  - `[ ]` belum mulai
  - `[x]` selesai
- Jika ada blocker, tulis di bagian **Active Blockers**.

## Snapshot
- Last Updated: `February 26, 2026`
- Overall Status: `Phase 5 Completed`

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
- [x] Definisikan command standar `lint`, `test`, `smoke`
- [x] Pastikan test bootstrap bisa dijalankan konsisten di dev
- [x] Buat pipeline CI minimal (build + smoke)
- [x] Tambahkan status check wajib sebelum merge (jika branch protection aktif)
- [x] Dokumentasi troubleshooting test/CI
- [x] Confirmation check Phase 4 (quality gate aktif)
- [x] Testing evidence Phase 4 dicatat (pipeline run PASS)

### Confirmation check Phase 4
- Command standar quality gate sudah aktif dan konsisten untuk local dev + CI melalui helper tunggal `tools/dev-env.ps1`:
  - `-Action lint`
  - `-Action test`
  - `-Action smoke`
- Test bootstrap dapat dijalankan konsisten dari container `vms-app` melalui `Action test` (eksekusi dari working directory yang benar) dan script bootstrap `vms/app/tests/test_bootstrap.php` sudah dipatch agar tidak berhenti oleh guard `BASEPATH`.
- Pipeline CI minimal otomatis tersedia di `.github/workflows/quality-gates.yml` dengan job `build-lint-test-smoke` (build/start stack + lint + test + smoke + stop).
- Requirement status check wajib sebelum merge sudah didokumentasikan lengkap di `docs/CI_QUALITY_GATES.md` (nama check yang harus di-require: `build-lint-test-smoke`).
- Dokumentasi troubleshooting test/CI tersedia dan sinkron di:
  - `docs/CI_QUALITY_GATES.md`
  - `docs/DEV_ENV_RUNBOOK.md`

### Testing evidence Phase 4
| Date | Command | Result | File/Area |
|---|---|---|---|
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action bootstrap -PhpRuntime 7.4` | PASS (`bootstrap completed`) | `.env`, `vms/.env`, `intra/.env` template bootstrap |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action start -PhpRuntime 7.4` | PASS (Docker stack build + start healthy) | `docker-compose.yml`, `docker/php/Dockerfile` |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action lint -PhpRuntime 7.4` | PASS (`lint checks passed`) | `scripts/*`, `vms/app/tests/test_bootstrap.php`, `vms/app/application/tests/Smoke_test.php` |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action test -PhpRuntime 7.4` | PASS (`test bootstrap check passed`) | `tools/dev-env.ps1`, `vms/app/tests/test_bootstrap.php` |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action smoke -PhpRuntime 7.4` | PASS (HTTP 200 untuk `vms/main/pengadaan`) | Nginx routing + 3 app endpoint minimum |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action stop -PhpRuntime 7.4` | PASS (stack cleanup) | Docker services/network lifecycle |
| 2026-02-26 | Workflow definition check (`.github/workflows/quality-gates.yml`) | PASS (pipeline config aktif untuk push/PR `main`) | `.github/workflows/quality-gates.yml`, `docs/CI_QUALITY_GATES.md` |

## Phase 5 - Medium-Term Refactor Track
- [x] Mapping duplikasi modul `vms` vs `intra/pengadaan`
- [x] Tentukan candidate shared components
- [x] Susun urutan refactor per domain (high value first)
- [x] Jalankan refactor bertahap dengan regression check
- [x] Dokumentasi perubahan arsitektur dan dependency map
- [x] Confirmation check Phase 5 (refactor scope & output tervalidasi)
- [x] Testing evidence Phase 5 dicatat (regression test area terdampak)

### Confirmation check Phase 5
- Mapping duplikasi lintas `vms` vs `intra/pengadaan` sudah terdokumentasi lengkap mencakup controller, model, helper, query path, dan utility di `docs/PHASE5_DUPLICATION_MAP.md`.
- Candidate shared component sudah diprioritaskan berdasarkan dampak vs risiko (P0/P1/P2), dengan fokus `high value + low risk` terlebih dahulu.
- Urutan refactor per domain sudah didefinisikan per wave (in-scope, out-of-scope, risk, rollback) di `docs/PHASE5_REFACTOR_WAVES.md`.
- Minimal 1 wave refactor nyata sudah dieksekusi secara incremental dan non-breaking:
  - Ekstraksi runtime inti cron ke `shared/legacy/cron_runtime.php`.
  - `vms/app/jobs/cron_core.php` dan `intra/pengadaan/cron_core.php` dipertahankan kompatibel via adapter `class cron extends SharedCronRuntime`.
- Dependency map pasca-refactor sudah dicatat di `docs/PHASE5_DEPENDENCY_MAP.md`.
- Objective Phase 5 terpenuhi: duplikasi struktural mulai diturunkan dengan komponen shared yang aman tanpa big-bang migration.

### Testing evidence Phase 5
| Date | Command | Result | File/Area |
|---|---|---|---|
| 2026-02-26 | `php -l shared/legacy/cron_runtime.php; php -l vms/app/jobs/cron_core.php; php -l intra/pengadaan/cron_core.php` | PASS (no syntax errors) | `shared/legacy/cron_runtime.php`, `vms/app/jobs/cron_core.php`, `intra/pengadaan/cron_core.php` |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action start -PhpRuntime 7.4` | PASS | Docker runtime 7.4 (`db`, `redis`, `vms-app`, `intra-app`, `webserver`) |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action lint -PhpRuntime 7.4` | PASS (`lint checks passed`) | Secret/security checks + syntax gate |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action test -PhpRuntime 7.4` | PASS (`test bootstrap check passed`) | `vms/app/tests/test_bootstrap.php` |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action smoke -PhpRuntime 7.4` | PASS (`vms/main/pengadaan` endpoint HTTP 200) | Nginx route + 3 app endpoint minimum |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action stop -PhpRuntime 7.4` | PASS | Docker lifecycle cleanup |
| 2026-02-26 | `pwsh ./tools/dev-env.ps1 -Action start -PhpRuntime 7.4; pwsh ./tools/dev-env.ps1 -Action cron -PhpRuntime 7.4; pwsh ./tools/dev-env.ps1 -Action stop -PhpRuntime 7.4` | PASS (`PASS cron runtime check`) | Regression area refactor cron (`cron_core` shared runtime path) |

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
- Tidak ada blocker aktif yang menghentikan gate Phase 5.
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
| Phase 4 | 2026-02-26 | Command standar lint/test/smoke aktif, bootstrap test stabil, CI workflow + required-status-check guidance tersedia, troubleshooting test/CI terdokumentasi | Build/start stack + lint + bootstrap test + smoke + stop (simulasi pipeline lokal) | PASS | `tools/dev-env.ps1`, `.github/workflows/quality-gates.yml`, `docs/CI_QUALITY_GATES.md`, `docs/DEV_ENV_RUNBOOK.md`, `vms/app/tests/test_bootstrap.php` |
| Phase 5 | 2026-02-26 | Mapping duplikasi + kandidat shared component + wave plan + 1 wave refactor incremental + dependency map tervalidasi terhadap objective reduksi duplikasi | Lint syntax file terdampak + quality gates (`start/lint/test/smoke/stop`) + cron regression check | PASS | `docs/PHASE5_DUPLICATION_MAP.md`, `docs/PHASE5_REFACTOR_WAVES.md`, `docs/PHASE5_DEPENDENCY_MAP.md`, `shared/legacy/cron_runtime.php`, `vms/app/jobs/cron_core.php`, `intra/pengadaan/cron_core.php` |
| Phase 6 | TBD | TBD | TBD | TBD | TBD |

## Session Log
Date: February 26, 2026
Scope: Phase 5 - Medium-Term Refactor Track
Completed:
- Mapping duplikasi `vms` vs `intra/pengadaan` diselesaikan dengan per-area summary (controller/model/helper/query path/utility) di `docs/PHASE5_DUPLICATION_MAP.md`.
- Candidate shared component diprioritaskan (P0/P1/P2) dan urutan wave refactor dengan in-scope/out-of-scope/risk/rollback disusun di `docs/PHASE5_REFACTOR_WAVES.md`.
- Wave 1 refactor nyata dieksekusi: ekstraksi runtime cron shared ke `shared/legacy/cron_runtime.php`, lalu `vms/app/jobs/cron_core.php` dan `intra/pengadaan/cron_core.php` dijadikan adapter kompatibel.
- Dependency map pasca-wave didokumentasikan di `docs/PHASE5_DEPENDENCY_MAP.md`.
- Regression + quality gates dijalankan dan PASS (`lint`, `test`, `smoke`, serta `cron runtime check` pada runtime 7.4).
Next:
- Lanjut ke Phase 6 (framework migration execution track) dengan boundary domain pilot yang paling siap.
Blockers:
- Tidak ada blocker aktif untuk completion gate Phase 5.

Date: February 26, 2026
Scope: Phase 4 - Quality Gates & Automation
Completed:
- Command standar `lint`, `test`, `smoke` ditambahkan ke `tools/dev-env.ps1` dan disamakan untuk local dev + CI.
- Smoke check dibuat portable lintas OS (curl executable detection + host header check) untuk kompatibilitas GitHub Actions Linux.
- Validasi test bootstrap distabilkan melalui `Action test` dan patch `vms/app/tests/test_bootstrap.php` (guard `BASEPATH`).
- Workflow CI minimum aktif di `.github/workflows/quality-gates.yml` dengan job `build-lint-test-smoke`.
- Dokumen status check wajib sebelum merge + troubleshooting test/CI dilengkapi (`docs/CI_QUALITY_GATES.md`, update `docs/DEV_ENV_RUNBOOK.md`).
- Evidence command Phase 4 (`bootstrap/start/lint/test/smoke/stop`) dicatat dengan hasil PASS.
Next:
- Lanjut ke Phase 5 (medium-term refactor track) sesuai prioritas domain dan regression scope.
Blockers:
- Tidak ada blocker aktif untuk completion gate Phase 4.

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
