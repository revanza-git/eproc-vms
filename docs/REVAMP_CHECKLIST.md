# Revamp Progress Checklist - eproc-vms

## Usage
- Checklist ini dipakai sebagai tracker operasional.
- Ubah status dengan:
  - `[ ]` belum mulai
  - `[x]` selesai
- Jika ada blocker, tulis di bagian **Active Blockers**.

## Snapshot
- Last Updated: `February 20, 2026`
- Overall Status: `Phase 1 Completed`

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
- [ ] Audit dan cleanup secret yang tidak boleh ada di repo
- [ ] Rotasi credential yang pernah ter-expose
- [ ] Aktifkan dan samakan baseline CSRF/session policy antar app
- [ ] Prioritaskan perbaikan query berisiko tinggi (input -> raw SQL)
- [ ] Rapikan `.gitignore` untuk artefak coverage/build/backup
- [ ] Upgrade/ketatkan scanner secret internal
- [ ] Confirmation check Phase 2 (security baseline terpenuhi)
- [ ] Testing evidence Phase 2 dicatat (secret scan + CSRF/session regression)

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
| Phase 2 | TBD | TBD | TBD | TBD | TBD |
| Phase 3 | TBD | TBD | TBD | TBD | TBD |
| Phase 4 | TBD | TBD | TBD | TBD | TBD |
| Phase 5 | TBD | TBD | TBD | TBD | TBD |
| Phase 6 | TBD | TBD | TBD | TBD | TBD |

## Session Log
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
