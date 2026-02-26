# Baseline Issues Register - eproc-vms

## Metadata
- Created: `February 20, 2026`
- Last Updated: `February 25, 2026`
- Purpose: daftar issue baseline prioritas tinggi sebelum eksekusi phase teknis.

## Prioritization Rule
- `P0`: blocker langsung untuk reliability/security/dev onboarding.
- `P1`: tinggi, harus masuk batch awal phase berikutnya.
- `P2`: penting, tapi bisa sesudah stabilisasi baseline.

## Baseline Issues
| ID | Priority | Area | Issue | Impact | Initial Action | Status |
|---|---|---|---|---|---|---|
| BL-001 | P0 | Secrets | Ada credential sensitif pernah tercatat di repo history/file | Risiko kebocoran akses DB/service | rotate credential + purge/replace file + audit scanner | Mitigated (Phase 2 baseline) |
| BL-002 | P0 | Config | Konfigurasi penting antar app tidak konsisten/kurang lengkap untuk first run | Onboarding gagal, app tidak boot | standardisasi source-of-truth config & env mapping | Open |
| BL-003 | P0 | Runtime | Script legacy (cron) masih menggunakan API DB usang (`mysql_*`) | Gagal di runtime modern, job kritikal tidak jalan | refactor cron ke `mysqli`/CI DB layer | Mitigated (Phase 3 baseline); residual runtime gap lain ditrack di `docs/PHP_UPGRADE.md` |
| BL-004 | P0 | Security | Baseline CSRF/session policy belum seragam antar app | Permukaan serangan meningkat | samakan default policy + whitelist endpoint eksplisit | Mitigated (Phase 2 baseline) |
| BL-005 | P1 | Query Safety | Masih ada SQL raw/concatenation pada bagian tertentu | Risiko SQL injection/logic bug | audit query prioritas tinggi + parameterisasi | In Progress |
| BL-006 | P1 | Repo Hygiene | Artefak backup/coverage/build ikut berada di codebase | Noise tinggi, review sulit, potensi salah deploy | cleanup + update `.gitignore` | Mitigated (Phase 2 baseline) |
| BL-007 | P1 | Testability | Test command/documentation tidak sepenuhnya sinkron dengan file aktual | Sulit validasi perubahan dengan cepat | normalisasi test entrypoint & docs | Mitigated (Phase 4 baseline) |
| BL-008 | P2 | Duplication | Duplikasi besar modul antar app (`vms` vs `intra/pengadaan`) | Biaya maintenance tinggi, fix tidak sinkron | mapping shared candidate + refactor roadmap | In Progress (Phase 5 Wave 1 completed; Waves 2-4 planned) |
| BL-009 | P2 | Architecture | CI3 legacy tidak layak jadi target jangka panjang production architecture | Risiko maintainability & security jangka panjang | eksekusi migration track ke Laravel | In Progress (Phase 6 Wave A governance decisions locked) |

## Exit Criteria (Phase 0)
- Register baseline issue tersedia dan disepakati.
- P0 issues terpetakan dengan owner dan phase target.
- Acceptance criteria Phase 1 terkunci.

## Ownership Baseline (Role Assignment)
| Area | Owner | Notes |
|---|---|---|
| Environment/DevEx | Platform Engineer / DevEx Lead | Menjaga compose, runbook, dan tooling local env |
| Security | Security Engineer | Termasuk auth/session strategy review Phase 6 |
| Runtime Upgrade | Runtime Modernization Lead | Menjaga residual gap runtime dan kompatibilitas target runtime |
| Framework Migration | Solution Architect + Migration Tech Lead | Menjaga decision record, coexistence plan, dan pilot execution governance |
