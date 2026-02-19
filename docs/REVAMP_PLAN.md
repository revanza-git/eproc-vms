# Revamp Plan - eproc-vms

## Metadata
- Project: `eproc-vms`
- Created: `February 19, 2026`
- Last Updated: `February 20, 2026`
- Current Status: `Planning`
- Branch Target: `main` (akan disesuaikan jika nanti pakai branch khusus revamp)

## Target Framework
- Selected: `Laravel`
- Decision Date: `February 20, 2026`
- Rationale (ringkas): ekosistem matang, onboarding dev lebih cepat, tooling testing/CI kuat, dan cocok untuk migrasi bertahap dari monolith legacy.

## Objective
Modernisasi environment development dan fondasi teknis project tanpa memutus alur bisnis yang sudah berjalan, termasuk migrasi bertahap dari CI3 ke framework modern.

## Success Criteria
- Developer baru bisa menjalankan stack lokal dengan langkah yang konsisten dan terdokumentasi.
- Risiko keamanan dasar (secret, CSRF, query raw berisiko tinggi) diturunkan secara terukur.
- Ada quality gate minimum (lint/test/smoke) sebelum perubahan signifikan di-merge.
- Jalur upgrade runtime ke PHP yang lebih modern tersedia dan tervalidasi.
- Jalur migrasi framework keluar dari CI3 tersedia, bertahap, dan dapat dijalankan tanpa big-bang cutover.

## Non-Goals (Saat Ini)
- Refactor total seluruh query legacy dalam satu gelombang.
- Perubahan besar domain bisnis/proses procurement.
- Big-bang migration satu rilis ke framework baru.

## Workstreams
1. Environment & Developer Experience
2. Security & Configuration Hygiene
3. Runtime Compatibility & Upgrade Path
4. Testability & CI Quality Gates
5. Codebase Consolidation (reduksi duplikasi)
6. Framework Migration (CI3 -> modern framework)

## Phase Plan

### Phase 0 - Baseline & Inventory
Tujuan:
- Menetapkan baseline kondisi aktual (runtime, config, testability, risiko utama).

Deliverables:
- Dokumen plan + checklist ini aktif dipakai.
- Daftar blocker kritikal yang harus ditangani dulu.
- Baseline issue register: `docs/BASELINE_ISSUES.md`.

### Phase 1 - Stabilize Development Environment
Tujuan:
- Menjadikan local setup repeatable dan minim error onboarding.

Deliverables:
- Proses bootstrap lokal yang konsisten.
- Konfigurasi `.env` dan config runtime app tervalidasi.
- Dokumentasi runbook dev environment.

Acceptance Criteria (Locked):
1. `docker compose up -d --build` berhasil menjalankan service inti tanpa crash loop.
2. Endpoint minimum terbuka:
   - `http://vms.localhost:8080/`
   - `http://intra.localhost:8080/main/`
   - `http://intra.localhost:8080/pengadaan/`
3. Setup developer terdokumentasi dengan langkah start/stop/reset yang repeatable.
4. Konfigurasi env antar root/`vms`/`intra` konsisten dan tervalidasi.
5. Ada smoke check dasar untuk memastikan web, DB, dan Redis reachable.

### Phase 2 - Security & Hygiene Foundation
Tujuan:
- Menurunkan risiko keamanan paling kritikal dan merapikan repository hygiene.

Deliverables:
- Secret handling policy + cleanup.
- CSRF/session baseline konsisten.
- Quick wins untuk query/input berisiko tinggi.

### Phase 3 - Runtime Modernization Path
Tujuan:
- Menyiapkan transisi aman dari legacy runtime ke runtime modern.

Deliverables:
- Compatibility matrix (PHP 7.4 vs target modern).
- Fix incompatibility utama (contoh: `mysql_*` di cron legacy).
- Jalur validasi smoke test untuk tiap runtime.

### Phase 4 - Quality Gates & Automation
Tujuan:
- Menambahkan guardrails agar kualitas tidak regress.

Deliverables:
- Standard lint/test command.
- CI pipeline minimum (smoke + unit/integration minimum).
- Dokumentasi troubleshooting test.

### Phase 5 - Medium-Term Refactor Track
Tujuan:
- Mengurangi technical debt struktural (duplikasi modul, layering, dependensi).

Deliverables:
- Prioritas refactor per domain.
- Rencana migrasi bertahap dengan risiko terkendali.

### Phase 6 - Framework Migration Execution Track
Tujuan:
- Memindahkan capability bisnis utama dari CI3 ke framework modern secara bertahap.

Deliverables:
- Keputusan framework target (berdasarkan kriteria tim dan operasional).
- Arsitektur coexistence (CI3 + app baru) dengan routing transisi.
- Ekstraksi modul prioritas ke aplikasi baru (strangler pattern).
- Rencana dekomisioning CI3 per domain.

## Milestones
| Milestone | Target | Status | Notes |
|---|---|---|---|
| M0 - Planning docs active | 2026-02-20 | Completed | Plan/checklist + baseline register + acceptance criteria Phase 1 |
| M1 - Dev env stabilized | TBD | Not Started |  |
| M2 - Security baseline pass | TBD | Not Started |  |
| M3 - Runtime upgrade path validated | TBD | Not Started |  |
| M4 - CI quality gate live | TBD | Not Started |  |
| M5 - Framework target selected | 2026-02-20 | Completed | Laravel |
| M6 - First domain migrated off CI3 | TBD | Not Started |  |

## Risks and Mitigation
| Risk | Impact | Mitigation | Status |
|---|---|---|---|
| Config file penting tidak konsisten antar app | High | Standardisasi source-of-truth config + template | Open |
| Legacy code incompatibility pada runtime modern | High | Prioritaskan fix blocker per modul kritikal | Open |
| Scope creep karena codebase besar | High | Eksekusi per phase, timebox, dan acceptance criteria jelas | Open |
| Duplikasi code menyebabkan fix tidak sinkron | Medium | Mapping shared area + checklist sync antar app | Open |
| Migrasi framework mengganggu flow bisnis aktif | High | Strangler migration + contract test per domain | Open |
| Tim belum siap dengan framework baru | Medium | Decision matrix + pilot domain + guideline coding | Open |

## Decision Log
| Date | Decision | Reason | Impact |
|---|---|---|---|
| 2026-02-19 | Gunakan pendekatan bertahap (phase-based), bukan rewrite total | Risiko bisnis dan downtime lebih rendah | Delivery lebih aman |
| 2026-02-19 | Tracking progres via markdown di folder `docs` | Transparan dan mudah dipelihara | Audit progres lebih jelas |
| 2026-02-20 | CI3 tidak dijadikan target jangka panjang production architecture | Framework EOL/legacy risk tinggi | Perlu migration track framework |
| 2026-02-20 | Framework target dipilih: Laravel | Tradeoff terbaik untuk maintainability dan velocity tim | Menjadi baseline migration architecture |
| 2026-02-20 | Baseline issues diregister dan acceptance criteria Phase 1 dikunci | Agar eksekusi phase teknis terarah dan terukur | Phase 0 dinyatakan selesai |

## Update Protocol
- Update dokumen ini setiap ada perubahan scope, milestone, atau keputusan arsitektural.
- Update checklist setelah task selesai/blocked.
- Untuk setiap sesi kerja, isi ringkas:
  - What changed
  - What is next
  - Blockers
