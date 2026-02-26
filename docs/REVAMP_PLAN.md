# Revamp Plan - eproc-vms

## Metadata
- Project: `eproc-vms`
- Created: `February 19, 2026`
- Last Updated: `February 25, 2026`
- Current Status: `Phase 5 Completed; Phase 6 Wave B Pilot Readiness In Progress (cutover still NO-GO)`
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
- Decision record formal Wave A (migration pattern, pilot boundary, auth/session, data access strategy).
- Baseline desain coexistence dev + checklist implementasi + acceptance test plan.
- Template evidence pilot (UAT + rollback drill) dan rencana quality gate contract/integration.
- Inventory endpoint pilot `auction` (path-by-path, contract ringkas, auth assumption) + draft contract/integration matrix.
- Proof runtime coexistence dev Stage 1 (placeholder pilot-app + shadow route + smoke `CX-01`/`CX-02`).
- Arsitektur coexistence (CI3 + app baru) dengan routing transisi.
- Ekstraksi modul prioritas ke aplikasi baru (strangler pattern).
- Rencana dekomisioning CI3 per domain.

## Mandatory Phase Completion Gate
Sebuah phase hanya boleh ditandai `Completed` jika seluruh syarat berikut terpenuhi:
1. Semua checklist phase tersebut selesai.
2. Confirmation check phase dilakukan (review deliverable terhadap objective/acceptance criteria).
3. Testing phase dijalankan dan hasilnya `PASS`.
4. Bukti eksekusi (command, hasil, tanggal) dicatat di `docs/REVAMP_CHECKLIST.md` pada bagian **Phase Validation Log**.
5. Milestone status di dokumen ini diperbarui.

## Minimum Verification Matrix
| Phase | Confirmation Check (Wajib) | Testing (Wajib) |
|---|---|---|
| Phase 0 | Dokumen plan/checklist/baseline/acceptance criteria lengkap & konsisten | Verifikasi referensi dokumen dan status checklist |
| Phase 1 | Semua acceptance criteria environment terpenuhi | `docker compose` lifecycle check + healthcheck + smoke endpoint minimum |
| Phase 2 | Security baseline policy sudah diterapkan pada target area | Secret scan + CSRF/session regression check + sample query safety check |
| Phase 3 | Mekanisme dual runtime dan compatibility scope tervalidasi | Smoke test runtime 7.4 + smoke test runtime target modern + job/cron check minimum |
| Phase 4 | Quality gate sudah aktif sebagai guardrail merge | CI pipeline run sukses (lint/test/smoke sesuai definisi) |
| Phase 5 | Refactor plan dan scope domain terkonfirmasi | Regression test pada area yang direfactor |
| Phase 6 | Coexistence architecture dan pilot migration readiness terkonfirmasi | Contract/integration test pilot + UAT pilot + rollback drill |

## Milestones
| Milestone | Target | Status | Notes |
|---|---|---|---|
| M0 - Planning docs active | 2026-02-20 | Completed | Plan/checklist + baseline register + acceptance criteria Phase 1 |
| M1 - Dev env stabilized | 2026-02-19 | Completed | Phase 1 completion gate pass (compose lifecycle + smoke + runbook) |
| M2 - Security baseline pass | 2026-02-19 | Completed | Phase 2 completion gate pass (secret scan + CSRF/session + query safety) |
| M3 - Runtime upgrade path validated | 2026-02-19 | Completed | Dual-runtime 7.4/8.2 validated (smoke + DB/Redis + cron check), blocker prioritas tinggi direfactor |
| M4 - CI quality gate live | 2026-02-26 | Completed | Standard `lint/test/smoke` command aktif di `tools/dev-env.ps1`, workflow `.github/workflows/quality-gates.yml` live, dan panduan required status check tersedia di `docs/CI_QUALITY_GATES.md` |
| M5 - Medium-term refactor wave 1 pass | 2026-02-26 | Completed | Phase 5 gate pass: duplication map + shared-component prioritization + wave plan + incremental shared cron runtime refactor + regression pass |
| M6 - Framework target selected | 2026-02-20 | Completed | Laravel |
| M6A - Phase 6 Wave A prerequisite hardening | 2026-03-14 | Completed | Governance sync + decision records + coexistence baseline design + pilot quality evidence templates selesai |
| M6B - Phase 6 Wave B pilot readiness (Stage 1 shadow coexistence) | 2026-03-28 | In Progress | Endpoint inventory v1 + skeleton placement decision + `pilot-app` shadow route + `CX-01/CX-02` smoke PASS; route toggle/auth bridge/CI gates masih pending |
| M7 - First domain migrated off CI3 | TBD | Not Started |  |

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
| 2026-02-19 | Phase 3 dijalankan dengan dual-runtime non-breaking (7.4 tetap default, 8.2 untuk validasi) | Menjaga stabilitas CI3 sambil menurunkan risiko runtime modernisasi | Gate Phase 3 bisa divalidasi tanpa big-bang rewrite |
| 2026-02-26 | Standard quality gate disatukan ke `tools/dev-env.ps1` dan CI minimum dikunci via workflow `quality-gates` | Supaya lint/test/smoke repeatable dan branch protection bisa mengacu ke status check tunggal | Gate Phase 4 dinyatakan pass, siap lanjut Phase 5 |
| 2026-02-26 | Phase 5 dimulai dengan refactor incremental shared cron runtime (`shared/legacy/cron_runtime.php`) | Mengurangi duplikasi berdampak tinggi dengan risiko rendah tanpa ubah kontrak `class cron` | Gate Phase 5 dinyatakan pass, siap lanjut Phase 6 |
| 2026-02-25 | Pattern migrasi Phase 6 dikunci ke `Strangler Fig` melalui decision record Wave A | Menghilangkan mismatch antara strategi (rekomendasi) vs checklist (belum formal) | Menjadi baseline untuk coexistence route split dan rollback per-domain |
| 2026-02-25 | Pilot domain pertama ditetapkan: `auction` read-only endpoint subset | Domain `auction` punya duplikasi tinggi + diff lebih rendah (evidence Phase 5), cocok untuk pilot risiko lebih rendah | Wave B fokus ke endpoint inventory + route toggle + contract comparison |
| 2026-02-25 | Auth/session pilot memakai CI3 authority + signed bridge token | Hindari shared-cookie/session trust langsung saat coexistence awal | Perlu bridge contract + integration test login/logout di Wave B |
| 2026-02-25 | Data access pilot memakai shared DB read-only; CI3 tetap single writer | Mempercepat pilot tanpa dual-write inconsistency | Wave B dibatasi ke endpoint read-only; ACL ditinjau ulang untuk domain berikutnya |
| 2026-02-25 | Inventory endpoint pilot `auction` v1 dipublikasikan path-by-path (scope host `vms`) | Mengurangi ambiguity scope pilot dan unblock draft contract/integration matrix | B3 turun; contract test dapat dimulai dari subset endpoint yang jelas |
| 2026-02-25 | Wave B Stage 1 memakai placeholder `pilot-app` di repo ini + shadow route `/_pilot/auction/*` | Unblock proof coexistence dev tanpa menunggu Laravel final app/repo | B2 turun (Stage 1); route toggle bisnis + auth bridge tetap pending |

## Update Protocol
- Update dokumen ini setiap ada perubahan scope, milestone, atau keputusan arsitektural.
- Update checklist setelah task selesai/blocked.
- Untuk setiap sesi kerja, isi ringkas:
  - What changed
  - What is next
  - Blockers
