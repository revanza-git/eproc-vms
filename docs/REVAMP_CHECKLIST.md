# Revamp Progress Checklist - eproc-vms

## Usage
- Checklist ini dipakai sebagai tracker operasional.
- Ubah status dengan:
  - `[ ]` belum mulai
  - `[x]` selesai
- Jika ada blocker, tulis di bagian **Active Blockers**.

## Snapshot
- Last Updated: `February 20, 2026`
- Overall Status: `Phase 0 Completed, Ready for Phase 1`

---

## Phase 0 - Baseline & Inventory
- [x] Buat dokumen plan (`docs/REVAMP_PLAN.md`)
- [x] Buat checklist tracking (`docs/REVAMP_CHECKLIST.md`)
- [x] Finalisasi daftar baseline issue prioritas tinggi (`docs/BASELINE_ISSUES.md`)
- [x] Finalisasi scope Phase 1 dengan acceptance criteria (`docs/REVAMP_PLAN.md`)

## Phase 1 - Stabilize Development Environment
- [ ] Validasi docker compose up/down end-to-end
- [ ] Standarisasi setup `.env` root, `vms`, dan `intra`
- [ ] Pastikan konfigurasi app minimum tersedia untuk onboarding dev
- [ ] Dokumentasi runbook start/stop/reset environment
- [ ] Tambahkan helper command (opsional: `Makefile`/`justfile`/script ps1)
- [ ] Tambahkan healthcheck service penting (web, php-fpm, db, redis)

## Phase 2 - Security & Hygiene Foundation
- [ ] Audit dan cleanup secret yang tidak boleh ada di repo
- [ ] Rotasi credential yang pernah ter-expose
- [ ] Aktifkan dan samakan baseline CSRF/session policy antar app
- [ ] Prioritaskan perbaikan query berisiko tinggi (input -> raw SQL)
- [ ] Rapikan `.gitignore` untuk artefak coverage/build/backup
- [ ] Upgrade/ketatkan scanner secret internal

## Phase 3 - Runtime Modernization Path
- [ ] Buat compatibility checklist PHP 7.4 -> target runtime modern
- [ ] Identifikasi blocker runtime (contoh: `mysql_*`, API deprecated)
- [ ] Refactor script cron legacy ke driver modern (`mysqli`/CI DB)
- [ ] Jalankan smoke test pada runtime saat ini
- [ ] Jalankan smoke test pada runtime target
- [ ] Catat gap dan rollback strategy

## Phase 4 - Quality Gates & Automation
- [ ] Definisikan command standar `lint`, `test`, `smoke`
- [ ] Pastikan test bootstrap bisa dijalankan konsisten di dev
- [ ] Buat pipeline CI minimal (build + smoke)
- [ ] Tambahkan status check wajib sebelum merge (jika branch protection aktif)
- [ ] Dokumentasi troubleshooting test/CI

## Phase 5 - Medium-Term Refactor Track
- [ ] Mapping duplikasi modul `vms` vs `intra/pengadaan`
- [ ] Tentukan candidate shared components
- [ ] Susun urutan refactor per domain (high value first)
- [ ] Jalankan refactor bertahap dengan regression check
- [ ] Dokumentasi perubahan arsitektur dan dependency map

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
