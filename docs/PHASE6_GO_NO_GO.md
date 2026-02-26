# Phase 6 GO/NO-GO Assessment - Framework Migration

## Metadata
- Assessment Date: `February 25, 2026`
- Wave A Execution Update: `February 25, 2026`
- Wave B Execution Update: `February 25, 2026` (Stage 1 pilot readiness implementation)
- Scope Evidence:
  - `docs/REVAMP_CHECKLIST.md`
  - `docs/REVAMP_PLAN.md`
  - `docs/FRAMEWORK_MIGRATION_STRATEGY.md`
  - `docs/PHASE6_DECISION_RECORDS.md`
  - `docs/PHASE6_COEXISTENCE_DEV_BASELINE.md`
  - `docs/BASELINE_ISSUES.md`
  - `docs/PHP_UPGRADE.md`
  - `docs/PHASE5_DUPLICATION_MAP.md`
  - `docs/PHASE5_DEPENDENCY_MAP.md`
  - `docs/CI_QUALITY_GATES.md`
  - `docs/templates/PHASE6_PILOT_UAT_EVIDENCE_TEMPLATE.md`
  - `docs/templates/PHASE6_PILOT_ROLLBACK_DRILL_TEMPLATE.md`
  - `tools/dev-env.ps1`
  - `docker/nginx/default.conf`
  - `docker-compose.yml`
  - `docker-compose.php82.yml`
  - `vms/app/application/modules/main/controllers/Main.php`
  - `intra/main/application/controllers/Main.php`

## Executive Decision (Updated After Wave A + Wave B Stage 1)
- Phase 6 pilot execution/cutover: `NO-GO`
- Mulai Wave B (pilot readiness implementation): `CONDITIONAL GO`

Alasan: Wave A berhasil menurunkan blocker governance/documentation. Pada Wave B Stage 1, repo ini sudah membuktikan coexistence runtime dasar via service placeholder `pilot-app`, route shadow `/_pilot/auction/*`, dan smoke `CX-01/CX-02` PASS. Selain itu, endpoint inventory `auction` read-only (path-by-path) dan draft matrix auth bridge/integration test sudah dipublikasikan. Namun mandatory completion gate Phase 6 tetap belum terpenuhi (pilot migration belum dijalankan), route toggle subset endpoint bisnis (`CX-03/CX-04`) belum ada, dan auth bridge + contract/integration/UAT/rollback evidence CI masih pending.

## Audit Gate Resmi Phase 6
| Gate Resmi | Requirement | Status | Evidence |
|---|---|---|---|
| Mandatory Completion Gate #1 | Semua checklist phase selesai | Not Met | `docs/REVAMP_PLAN.md:118`, `docs/REVAMP_CHECKLIST.md:159`, `docs/REVAMP_CHECKLIST.md:168` |
| Mandatory Completion Gate #2 | Confirmation check phase dilakukan | Not Met | `docs/REVAMP_PLAN.md:119`, `docs/REVAMP_CHECKLIST.md:167` |
| Mandatory Completion Gate #3 | Testing phase dijalankan dan PASS | Not Met | `docs/REVAMP_PLAN.md:120`, `docs/REVAMP_CHECKLIST.md:168` |
| Mandatory Completion Gate #4 | Bukti eksekusi tercatat di Phase Validation Log | Not Met | `docs/REVAMP_PLAN.md:121`, `docs/REVAMP_CHECKLIST.md:217` |
| Minimum Verification Matrix (Phase 6) | Coexistence readiness + contract/integration + UAT + rollback drill | Not Met | `docs/REVAMP_PLAN.md:133`, `docs/REVAMP_CHECKLIST.md:161`, `docs/REVAMP_CHECKLIST.md:165`, `docs/REVAMP_CHECKLIST.md:168` |

## Blockers (Ordered by Severity, Baseline Assessment; see Progress Update for latest status)
| ID | Severity | Blocker | Evidence (File:Line) | Exit Criteria to become GO | Suggested Owner (Role) | Target Date |
|---|---|---|---|---|---|---|
| B1 | Critical | Mandatory gate Phase 6 belum terpenuhi (checklist, confirmation check, testing evidence, validation log). | `docs/REVAMP_PLAN.md:118`, `docs/REVAMP_PLAN.md:121`, `docs/REVAMP_CHECKLIST.md:159`, `docs/REVAMP_CHECKLIST.md:167`, `docs/REVAMP_CHECKLIST.md:168`, `docs/REVAMP_CHECKLIST.md:217` | Semua item Phase 6 di checklist selesai, confirmation check ditulis, testing PASS dicatat, dan baris Phase 6 di Validation Log terisi lengkap dengan evidence command+hasil+tanggal. | Engineering Manager, Migration Program Lead | March 21, 2026 |
| B2 | Critical | Coexistence architecture (routing CI3 + app baru) belum siap secara infrastruktur dev. | `docs/REVAMP_CHECKLIST.md:161`, `docs/FRAMEWORK_MIGRATION_STRATEGY.md:46`, `docker-compose.yml:2`, `docker-compose.yml:30`, `docker-compose.yml:65`, `docker-compose.yml:100`, `docker-compose.yml:122`, `docker-compose.php82.yml:2`, `docker-compose.php82.yml:7`, `docker/nginx/default.conf:88`, `docker/nginx/default.conf:93` | Ada service app baru untuk pilot migration, route split CI3 vs app baru terdokumentasi dan diuji via smoke/integration path coexistence. | Platform Engineer, Solution Architect | March 12, 2026 |
| B3 | High | Boundary domain pilot belum ditetapkan; milestone migrasi domain pertama masih Not Started. | `docs/REVAMP_CHECKLIST.md:160`, `docs/REVAMP_CHECKLIST.md:164`, `docs/REVAMP_PLAN.md:146`, `docs/FRAMEWORK_MIGRATION_STRATEGY.md:70` | ADR domain pilot (scope endpoint, data ownership, rollback boundary) disetujui; backlog pilot dipublish. | Product Owner (Procurement Domain), Migration Tech Lead | March 07, 2026 |
| B4 | High | Auth/session lintas aplikasi belum punya strategy yang dikunci; implementasi existing menunjukkan kontrak belum seragam. | `docs/REVAMP_CHECKLIST.md:162`, `docs/FRAMEWORK_MIGRATION_STRATEGY.md:77`, `vms/app/application/modules/main/controllers/Main.php:685`, `vms/app/application/modules/main/controllers/Main.php:703`, `intra/main/application/controllers/Main.php:136`, `intra/main/application/controllers/Main.php:180`, `intra/main/application/controllers/Main.php:191` | Dokumen auth/session bridge final (token/session contract, TTL, signature validation, source trust model), lalu integration test cross-app login/logout PASS. | Security Engineer, Application Architect | March 14, 2026 |
| B5 | High | Data access strategy Phase 6 belum ditentukan (shared DB vs anti-corruption layer). | `docs/REVAMP_CHECKLIST.md:163`, `docs/FRAMEWORK_MIGRATION_STRATEGY.md:82`, `docker-compose.yml:60`, `docker-compose.yml:95` | Keputusan strategy data access ditulis sebagai ADR (read/write ownership, transaction boundary, consistency policy), plus guardrails implementasi pilot. | Data Architect, Backend Lead | March 14, 2026 |
| B6 | High | Quality gate untuk pilot migration belum mencakup contract/integration/UAT/rollback drill. | `docs/REVAMP_PLAN.md:133`, `docs/REVAMP_CHECKLIST.md:165`, `docs/REVAMP_CHECKLIST.md:168`, `docs/CI_QUALITY_GATES.md:35`, `docs/CI_QUALITY_GATES.md:40`, `tools/dev-env.ps1:2` | Tambah gate pilot (`contract`, `integration`, `uat-evidence`, `rollback-drill`) ke workflow CI + runbook; minimal 1 run PASS. | QA Lead, DevOps Engineer | March 18, 2026 |
| B7 | Medium | Konsistensi status antar dokumen belum sinkron (risk governance/reporting). | `docs/REVAMP_CHECKLIST.md:159`, `docs/FRAMEWORK_MIGRATION_STRATEGY.md:33`, `docs/BASELINE_ISSUES.md:18`, `docs/PHP_UPGRADE.md:20`, `docs/BASELINE_ISSUES.md:22`, `docs/REVAMP_CHECKLIST.md:95`, `docs/BASELINE_ISSUES.md:31` | Sinkronisasi status lintas dokumen (checklist/plan/baseline) dan owner role tidak lagi `TBD`; conflict status diberi resolusi eksplisit. | Engineering Manager, PMO/Project Controller | March 05, 2026 |
| B8 | Medium | Runtime residual gap legacy masih tercatat (mcrypt/XMLRPC/dompdf), berisiko saat domain pilot menyentuh area terkait. | `docs/PHP_UPGRADE.md:24`, `docs/PHP_UPGRADE.md:53`, `docs/PHP_UPGRADE.md:57` | Impact analysis pilot terhadap gap legacy dibuat; jika in-scope pilot, mitigasi/isolasi wajib selesai sebelum cutover. | Runtime Modernization Lead, Tech Lead Pilot | March 18, 2026 |

## Wave A Execution Checklist (Post-Execution Update)
| Wave A Task | Status | Evidence | Notes |
|---|---|---|---|
| Sync mismatch status dokumen M1-M4 | Resolved | `docs/BASELINE_ISSUES.md`, `docs/REVAMP_CHECKLIST.md`, `docs/REVAMP_PLAN.md`, `docs/FRAMEWORK_MIGRATION_STRATEGY.md` | Status/owner/decision wording diselaraskan |
| Lock decision record migration pattern | Resolved | `docs/PHASE6_DECISION_RECORDS.md` (`DR-P6-001`) | `Strangler Fig` kini formal, bukan sekadar rekomendasi |
| Lock decision record boundary pilot pertama | Resolved (domain-level) | `docs/PHASE6_DECISION_RECORDS.md` (`DR-P6-002`) | `auction` read-only subset dikunci; endpoint inventory final -> Wave B |
| Lock decision record auth/session strategy | Partial | `docs/PHASE6_DECISION_RECORDS.md` (`DR-P6-003`) | Strategy locked, implement + integration test belum ada |
| Lock decision record data access strategy | Resolved | `docs/PHASE6_DECISION_RECORDS.md` (`DR-P6-004`) | Shared DB read-only + CI3 single writer untuk pilot |
| Siapkan baseline coexistence dev (design/checklist/acceptance plan) | Partial (design done) | `docs/PHASE6_COEXISTENCE_DEV_BASELINE.md`, `docs/DEV_ENV_RUNBOOK.md` | Runtime implementation ditunda ke Wave B (app pilot belum ada) |
| Extend quality gate readiness plan pilot | Partial | `docs/CI_QUALITY_GATES.md`, `docs/templates/PHASE6_PILOT_UAT_EVIDENCE_TEMPLATE.md`, `docs/templates/PHASE6_PILOT_ROLLBACK_DRILL_TEMPLATE.md` | Definisi gate + template ada; job CI/pilot run belum ada |
| Update assessment/gate docs setelah Wave A | Resolved | `docs/PHASE6_GO_NO_GO.md`, `docs/REVAMP_CHECKLIST.md`, `docs/REVAMP_PLAN.md` | Status dan evidence sinkron |

## Wave B Execution Checklist (Stage 1 - Pilot Readiness Implementation)
| Wave B Task | Status | Evidence | Notes |
|---|---|---|---|
| Finalisasi endpoint inventory `auction` read-only (path-by-path + contract ringkas + auth assumption) | Resolved (v1 scope) | `docs/PHASE6_DECISION_RECORDS.md` (appendix Wave B) | Scope v1 dikunci untuk `vms` host; nested payload schema sample masih pending |
| Putuskan strategi skeleton app pilot (repo placement + tradeoff) | Resolved (provisional Wave B Stage 1) | `docs/PHASE6_DECISION_RECORDS.md` (`DR-P6-005`) | Placeholder di repo ini untuk proof coexistence dev; final Laravel repo placement masih review trigger |
| Implement service `pilot-app` + route shadow `/_pilot/auction/*` | Resolved (Stage 1 shadow) | `docker-compose.yml`, `docker-compose.php82.yml`, `docker/nginx/default.conf`, `pilot-app/public/index.php` | Belum mengubah route bisnis `/auction/*` |
| Tambah smoke coexistence untuk `CX-01` + `CX-02` | Resolved | `tools/dev-env.ps1`, `docs/PHASE6_COEXISTENCE_DEV_BASELINE.md` | Action baru `coexistence` memverifikasi legacy route + pilot shadow marker |
| Jalankan verifikasi `CX-01` + `CX-02` di dev | Resolved | `docs/PHASE6_COEXISTENCE_DEV_BASELINE.md` (Wave B Stage 1 evidence) | Attempt start pertama gagal transient Docker daemon `EOF`, retry PASS |
| Siapkan draft auth bridge + integration test matrix endpoint pilot | Partial | `docs/PHASE6_DECISION_RECORDS.md`, `docs/CI_QUALITY_GATES.md` | Belum implementasi bridge/token verifier |

## Blocker Progress Update (After Wave A + Wave B Stage 1)
| ID | Wave A Status | Progress Summary | Updated Evidence | Remaining Gap / Mitigation |
|---|---|---|---|---|
| B1 | Open (Accepted for Wave B entry) | Dokumen dan checklist Wave A sudah disiapkan, tetapi mandatory completion gate Phase 6 memang belum bisa ditutup tanpa eksekusi pilot Wave B. | `docs/REVAMP_CHECKLIST.md`, `docs/REVAMP_PLAN.md` | Tetap open sampai confirmation check + testing + validation log Phase 6 terisi |
| B2 | Partial (Stage 1 shadow coexistence proven) | Service placeholder `pilot-app`, route shadow `/_pilot/auction/*`, dan smoke coexistence `CX-01/CX-02` sudah terimplementasi dan lulus di dev. | `docker-compose.yml`, `docker-compose.php82.yml`, `docker/nginx/default.conf`, `pilot-app/public/index.php`, `tools/dev-env.ps1`, `docs/PHASE6_COEXISTENCE_DEV_BASELINE.md`, `docs/DEV_ENV_RUNBOOK.md` | Route toggle subset endpoint bisnis `/auction/*` + rollback switch (`CX-03`, `CX-04`) belum diimplementasikan |
| B3 | Resolved (Wave B v1 pilot scope locked) | Inventory endpoint `auction` read-only sudah dipublikasikan path-by-path (v1, host `vms`) beserta contract ringkas, pilot subset decision, dan backlog deferred endpoint. | `docs/PHASE6_DECISION_RECORDS.md`, `docs/FRAMEWORK_MIGRATION_STRATEGY.md`, `docs/REVAMP_CHECKLIST.md` | Perlu sample payload runtime untuk bekukan schema nested sebelum `pilot-contract` CI |
| B4 | Partial | Strategy auth/session pilot tetap locked, dan kini sudah ada draft contract/integration test matrix untuk endpoint auth-gated (`get_user_update`) termasuk valid/invalid token scenarios. | `docs/PHASE6_DECISION_RECORDS.md`, `docs/FRAMEWORK_MIGRATION_STRATEGY.md`, `docs/CI_QUALITY_GATES.md` | Belum ada implementasi bridge endpoint/token verification + integration test login/logout PASS |
| B5 | Resolved (Wave A scope) | Keputusan data access pilot formal sudah dikunci (shared DB read-only, CI3 single writer). | `docs/PHASE6_DECISION_RECORDS.md`, `docs/FRAMEWORK_MIGRATION_STRATEGY.md` | Guardrails harus diimplementasikan saat coding pilot |
| B6 | Partial | Rencana gate pilot contract/integration + template evidence UAT/rollback sudah tersedia, ditambah draft contract matrix/integration cases dan helper local `coexistence` smoke untuk baseline route proof. | `docs/CI_QUALITY_GATES.md`, `docs/PHASE6_DECISION_RECORDS.md`, `tools/dev-env.ps1`, `docs/templates/PHASE6_PILOT_UAT_EVIDENCE_TEMPLATE.md`, `docs/templates/PHASE6_PILOT_ROLLBACK_DRILL_TEMPLATE.md` | Workflow/job CI pilot (`pilot-contract`, `pilot-integration`, evidence checks) dan run PASS belum ada |
| B7 | Resolved (Wave A scope) | Mismatch M1-M4 dan ownership baseline telah disinkronkan. | `docs/BASELINE_ISSUES.md`, `docs/REVAMP_CHECKLIST.md`, `docs/REVAMP_PLAN.md`, `docs/FRAMEWORK_MIGRATION_STRATEGY.md` | Monitor drift pada update Wave B |
| B8 | Open | Belum disentuh di Wave A (di luar fokus governance/readiness docs). | `docs/PHP_UPGRADE.md` | Perlu impact analysis pilot `auction` terhadap residual gap runtime sebelum cutover |

## Go/No-Go Checklist (Release Gate Internal)
| Item | Status | Evidence | Risk if skipped |
|---|---|---|---|
| [x] Framework target dipilih (Laravel) | Done | `docs/REVAMP_CHECKLIST.md:158`, `docs/REVAMP_PLAN.md:11`, `docs/FRAMEWORK_MIGRATION_STRATEGY.md:16` | Arah migrasi framework tidak jelas |
| [x] Migration pattern dikunci sebagai keputusan formal (bukan sekadar rekomendasi) | Done | `docs/PHASE6_DECISION_RECORDS.md`, `docs/FRAMEWORK_MIGRATION_STRATEGY.md`, `docs/REVAMP_CHECKLIST.md` | Tim eksekusi dengan asumsi berbeda |
| [x] Boundary domain pilot pertama ditetapkan | Done (v1 scope locked, path-by-path inventory published) | `docs/PHASE6_DECISION_RECORDS.md`, `docs/FRAMEWORK_MIGRATION_STRATEGY.md`, `docs/REVAMP_CHECKLIST.md` | Scope creep dan gagal estimasi |
| [ ] Coexistence architecture siap di dev (routing + service app baru) | Partial (Stage 1 shadow coexistence proven; route toggle bisnis pending) | `docs/PHASE6_COEXISTENCE_DEV_BASELINE.md`, `docs/DEV_ENV_RUNBOOK.md`, `docker-compose.yml`, `docker/nginx/default.conf`, `tools/dev-env.ps1` | Belum ada strangler toggle subset `/auction/*` + rollback switch cepat |
| [ ] Auth/session strategy lintas aplikasi dikunci + diuji | Partial (strategy locked, test pending) | `docs/PHASE6_DECISION_RECORDS.md`, `docs/FRAMEWORK_MIGRATION_STRATEGY.md`, `docs/REVAMP_CHECKLIST.md` | SSO/logout regressions dan security gap |
| [x] Data access strategy dikunci (shared DB vs ACL) | Done | `docs/PHASE6_DECISION_RECORDS.md`, `docs/FRAMEWORK_MIGRATION_STRATEGY.md`, `docs/REVAMP_CHECKLIST.md` | Dual-write inconsistency/corrupt data |
| [ ] Domain pilot pertama selesai dimigrasikan | Not Done | `docs/REVAMP_CHECKLIST.md:164`, `docs/REVAMP_PLAN.md:146` | Tidak ada bukti eksekusi migration track |
| [ ] Contract + integration test pilot tersedia dan PASS | Partial (gate plan + endpoint matrix + integration cases drafted; run pending) | `docs/CI_QUALITY_GATES.md`, `docs/PHASE6_DECISION_RECORDS.md`, `docs/REVAMP_PLAN.md`, `docs/REVAMP_CHECKLIST.md` | Regression API saat cutover |
| [ ] UAT pilot selesai dan sign-off terdokumentasi | Not Done (template ready) | `docs/templates/PHASE6_PILOT_UAT_EVIDENCE_TEMPLATE.md`, `docs/CI_QUALITY_GATES.md` | Risiko reject user setelah cutover |
| [ ] Rollback drill pilot dijalankan dan evidence lengkap | Not Done (template ready) | `docs/templates/PHASE6_PILOT_ROLLBACK_DRILL_TEMPLATE.md`, `docs/CI_QUALITY_GATES.md` | Gagal recovery saat incident migrasi |
| [ ] Rencana dekomisioning modul CI3 terdampak | Not Done | `docs/REVAMP_CHECKLIST.md:166`, `docs/REVAMP_PLAN.md:114` | Biaya maintenance dual-stack berkepanjangan |
| [ ] Phase Validation Log Phase 6 terisi lengkap (bukan TBD) | Not Done | `docs/REVAMP_CHECKLIST.md:217` | Gate tidak audit-ready |

## Konsistensi Antar Dokumen (Mismatch)
| ID | Mismatch | Status After Wave A | Updated Evidence | Impact / Resolution |
|---|---|---|---|---|
| M1 | Checklist menandai migration pattern belum ditetapkan, namun strategy sudah menuliskan pola rekomendasi `Strangler Fig`. | Resolved | `docs/REVAMP_CHECKLIST.md`, `docs/FRAMEWORK_MIGRATION_STRATEGY.md`, `docs/PHASE6_DECISION_RECORDS.md` | Pattern sekarang formal via decision record (`DR-P6-001`) dan wording strategy diubah ke `Selected` |
| M2 | Baseline issue runtime (`BL-003`) masih `Open`, sementara dokumen upgrade runtime menandai mitigasi `mysql_*`. | Resolved | `docs/BASELINE_ISSUES.md`, `docs/PHP_UPGRADE.md` | `BL-003` diubah jadi `Mitigated (Phase 3 baseline)` dengan residual gap runtime tetap dirujuk ke `PHP_UPGRADE` |
| M3 | Baseline issue testability (`BL-007`) masih `Open`, sementara Phase 4 dinyatakan completed dengan gate lint/test/smoke aktif. | Resolved | `docs/BASELINE_ISSUES.md`, `docs/REVAMP_CHECKLIST.md`, `docs/CI_QUALITY_GATES.md` | `BL-007` diubah jadi `Mitigated (Phase 4 baseline)` |
| M4 | Ownership baseline masih `TBD` padahal phase lanjutan membutuhkan owner gate yang jelas. | Resolved | `docs/BASELINE_ISSUES.md` | Ownership baseline diganti role assignment eksplisit |

## Execution Plan (2 Waves)
### Wave A - Prerequisite Hardening (Before Pilot)
Target window: `February 25, 2026 - March 14, 2026`

1. Governance sync dan cleanup mismatch status dokumen (M1-M4). `Completed (Wave A)`
2. Kunci ADR migration pattern + pilot domain boundary. `Completed (Wave A; endpoint inventory final lanjut Wave B)`
3. Kunci ADR auth/session bridge lintas aplikasi. `Completed (strategy only; implement/test lanjut Wave B)`
4. Kunci ADR data access strategy (shared DB vs ACL) dan read/write ownership. `Completed (Wave A)`
5. Implement coexistence baseline dev:
   - service app baru di compose, `Pending Wave B`
   - routing split CI3 vs app baru di Nginx, `Pending Wave B`
   - smoke path coexistence, `Pending Wave B`
   - desain/checklist/acceptance test plan, `Completed (Wave A)`
6. Extend CI gates untuk contract/integration test pilot + template evidence UAT/rollback drill. `Partial (plan + templates done; CI job execution pending Wave B)`

Exit Wave A:
- Blocker `B1-B7` minimal turun ke status `Resolved` atau `Accepted with documented mitigation`.

### Wave B - Pilot Migration Readiness
Target window: `March 15, 2026 - March 28, 2026`

1. Implement domain pilot pada app baru (strangler route aktif via toggle).
2. Parallel-run terbatas untuk endpoint pilot (compare output & log anomaly).
3. Jalankan contract/integration test PASS di CI untuk scope pilot.
4. Jalankan UAT pilot + sign-off formal.
5. Jalankan rollback drill end-to-end dan catat MTTR/step evidence.
6. Isi penuh `Phase Validation Log` Phase 6 dengan hasil testing dan evidence.

Exit Wave B (GO):
- Semua item di Go/No-Go Checklist berubah menjadi `Done`.
- Tidak ada blocker `Critical`/`High` yang tersisa.

## Gate Recommendation (After Wave B Stage 1)
- Recommendation: `CONDITIONAL GO` untuk lanjut **Wave B (pilot readiness implementation)** ke step berikutnya.
- Guardrails sebelum mulai Wave B:
  1. Pastikan owner role Wave B aktif untuk platform, migration tech lead, QA, dan security review.
  2. Kunci keputusan error code auth bridge (`401` vs `403`) dan enforcement point (CI3 bridge/gateway).
  3. Implement route toggle subset `/auction/*` + rollback switch untuk `CX-03/CX-04`.
- Batasan keputusan ini:
  - Bukan `GO` untuk pilot cutover/eksekusi domain migration final.
  - Mandatory completion gate Phase 6 tetap `Not Met` sampai testing/UAT/rollback evidence PASS dan tercatat.

## Next Task Plan (Wave B - Smaller Ordered Steps)
1. Implement route toggle subset `/auction/*` (mulai dari `get_barang` + `get_peserta`) + rollback switch untuk `CX-03`/`CX-04`.
2. Ambil sample payload anonymized CI3 untuk `get_initial_data` dan `get_chart_update`, lalu bekukan schema nested untuk `pilot-contract`.
3. Kunci contract auth bridge (issuer/aud/TTL/error code `401|403`) + pilih enforcement point (CI3 endpoint vs gateway).
4. Implement endpoint bridge/token verifier minimal + integration test valid/invalid/expired token (`CX-05` + `pilot-integration`).
5. Jalankan contract test CI3 vs pilot app untuk subset endpoint pilot (`pilot-contract`) dan simpan report.
6. Jalankan UAT pilot menggunakan template evidence + sign-off.
7. Jalankan rollback drill menggunakan template evidence + catat MTTR.
8. Isi Phase 6 Validation Log + confirmation check/testing evidence di `docs/REVAMP_CHECKLIST.md`.

## Open Questions
1. Apakah pilot v1 tetap dibatasi ke host `vms` saja, atau harus mencakup duplikasi `intra/pengadaan` dalam gelombang yang sama?
2. Apakah required status check `build-lint-test-smoke` sudah benar-benar di-enforce di GitHub branch protection (bukan hanya didokumentasikan)?
3. Apakah evidence UAT dan rollback drill disimpan di sistem lain (ticketing/wiki) yang belum direferensikan di repo ini?
4. Error code final untuk token invalid/expired pada auth bridge pilot akan `401` atau `403`?
