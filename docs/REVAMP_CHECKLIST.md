# Revamp Progress Checklist - eproc-vms

## Usage
- Checklist ini dipakai sebagai tracker operasional.
- Ubah status dengan:
  - `[ ]` belum mulai
  - `[x]` selesai
- Jika ada blocker, tulis di bagian **Active Blockers**.

## Snapshot
- Last Updated: `February 19, 2026`
- Overall Status: `Phase 2 Completed`

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
- [ ] Buat compatibility checklist PHP 7.4 -> target runtime modern
- [ ] Identifikasi blocker runtime (contoh: `mysql_*`, API deprecated)
- [ ] Refactor script cron legacy ke driver modern (`mysqli`/CI DB)
- [ ] Jalankan smoke test pada runtime saat ini
- [ ] Jalankan smoke test pada runtime target
- [ ] Catat gap dan rollback strategy
- [ ] Confirmation check Phase 3 (compatibility scope tervalidasi)
- [ ] Testing evidence Phase 3 dicatat (smoke 7.4 + smoke target + cron check)

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
- (kosong)

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
| Phase 3 | TBD | TBD | TBD | TBD | TBD |
| Phase 4 | TBD | TBD | TBD | TBD | TBD |
| Phase 5 | TBD | TBD | TBD | TBD | TBD |
| Phase 6 | TBD | TBD | TBD | TBD | TBD |

## Session Log
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
