# Baseline Issues Register - eproc-vms

## Metadata
- Created: `February 20, 2026`
- Last Updated: `February 20, 2026`
- Purpose: daftar issue baseline prioritas tinggi sebelum eksekusi phase teknis.

## Prioritization Rule
- `P0`: blocker langsung untuk reliability/security/dev onboarding.
- `P1`: tinggi, harus masuk batch awal phase berikutnya.
- `P2`: penting, tapi bisa sesudah stabilisasi baseline.

## Baseline Issues
| ID | Priority | Area | Issue | Impact | Initial Action | Status |
|---|---|---|---|---|---|---|
| BL-001 | P0 | Secrets | Ada credential sensitif pernah tercatat di repo history/file | Risiko kebocoran akses DB/service | rotate credential + purge/replace file + audit scanner | Open |
| BL-002 | P0 | Config | Konfigurasi penting antar app tidak konsisten/kurang lengkap untuk first run | Onboarding gagal, app tidak boot | standardisasi source-of-truth config & env mapping | Open |
| BL-003 | P0 | Runtime | Script legacy (cron) masih menggunakan API DB usang (`mysql_*`) | Gagal di runtime modern, job kritikal tidak jalan | refactor cron ke `mysqli`/CI DB layer | Open |
| BL-004 | P0 | Security | Baseline CSRF/session policy belum seragam antar app | Permukaan serangan meningkat | samakan default policy + whitelist endpoint eksplisit | Open |
| BL-005 | P1 | Query Safety | Masih ada SQL raw/concatenation pada bagian tertentu | Risiko SQL injection/logic bug | audit query prioritas tinggi + parameterisasi | Open |
| BL-006 | P1 | Repo Hygiene | Artefak backup/coverage/build ikut berada di codebase | Noise tinggi, review sulit, potensi salah deploy | cleanup + update `.gitignore` | Open |
| BL-007 | P1 | Testability | Test command/documentation tidak sepenuhnya sinkron dengan file aktual | Sulit validasi perubahan dengan cepat | normalisasi test entrypoint & docs | Open |
| BL-008 | P2 | Duplication | Duplikasi besar modul antar app (`vms` vs `intra/pengadaan`) | Biaya maintenance tinggi, fix tidak sinkron | mapping shared candidate + refactor roadmap | Open |
| BL-009 | P2 | Architecture | CI3 legacy tidak layak jadi target jangka panjang production architecture | Risiko maintainability & security jangka panjang | eksekusi migration track ke Laravel | In Progress |

## Exit Criteria (Phase 0)
- Register baseline issue tersedia dan disepakati.
- P0 issues terpetakan dengan owner dan phase target.
- Acceptance criteria Phase 1 terkunci.

## Ownership Placeholder
| Area | Owner | Notes |
|---|---|---|
| Environment/DevEx | TBD |  |
| Security | TBD |  |
| Runtime Upgrade | TBD |  |
| Framework Migration | TBD |  |

