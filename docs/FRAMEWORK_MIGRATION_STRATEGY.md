# Framework Migration Strategy (CI3 Exit Plan)

## Metadata
- Last Updated: `February 25, 2026` (Wave B Stage 1 update)
- Decision Records:
  - `docs/PHASE6_DECISION_RECORDS.md`
  - `docs/PHASE6_COEXISTENCE_DEV_BASELINE.md`

## Context
Project saat ini berjalan di CodeIgniter 3 (CI3) yang bersifat legacy. Upgrade runtime PHP saja tidak cukup untuk menurunkan risiko jangka panjang production.

## Goal
Memigrasikan aplikasi secara bertahap dari CI3 ke framework modern tanpa menghentikan operasional bisnis.

## Selected Target
- Framework: `Laravel`
- Selected On: `February 20, 2026`

## Principles
1. No big-bang rewrite.
2. Migration per domain/capability.
3. Coexistence dulu, decommission belakangan.
4. Setiap step harus punya rollback path.

## Framework Decision Gate
Gunakan kriteria ini sebelum memilih framework target:
- Kesesuaian skill tim saat ini.
- Ekosistem package, security, dan maintenance cadence.
- Kemudahan testing, observability, dan deployment.
- Kemampuan integrasi dengan sistem existing.
- Total cost of migration (bukan hanya cost coding awal).

## Selected Migration Pattern (Locked - Wave A)
Pola resmi: `Strangler Fig` (lihat `DR-P6-001` di `docs/PHASE6_DECISION_RECORDS.md`)
- Jalur request lama tetap ke CI3.
- Domain baru/yang dimigrasi diarahkan ke app baru.
- Secara bertahap, traffic dipindah dari CI3 ke app baru hingga CI3 bisa dipensiunkan.

## Execution Phases

### A. Foundation
- Putuskan framework target.
- Siapkan coding standards, project skeleton, dan CI/CD baseline app baru.
- Siapkan observability minimum (logs, metrics, error tracking).

### B. Coexistence Architecture
- Tambah routing layer (Nginx/API Gateway) untuk memisahkan endpoint lama vs baru.
- Tetapkan kontrak API antar aplikasi (request/response, auth, error format).
- Tetapkan session/auth strategy lintas aplikasi.

### C. Domain-by-Domain Migration
- Pilih domain pilot risiko rendah, value tinggi.
- Rebuild domain di app baru, lalu parallel-run.
- Validasi: unit test + integration test + UAT domain.
- Aktifkan traffic switching bertahap.

### D. CI3 Decommission Track
- Bekukan perubahan fitur di modul CI3 yang sudah masuk antrean migrasi.
- Hapus modul CI3 yang sudah tergantikan.
- Perbarui dokumentasi operasional dan incident runbook.

## Technical Streams to Decide Early
- Auth: **Locked for pilot** -> CI3 authority + signed bridge token (lihat `DR-P6-003`).
- Data: **Locked for pilot** -> shared DB read-only, CI3 single writer (lihat `DR-P6-004`).
- Files: strategi storage terpadu (uploads/docs).
- Jobs/Cron: penempatan eksekusi saat coexistence.

## Wave A Locked Decisions (Pilot Readiness)

### Pilot Domain Boundary
- Pilot domain pertama: `auction` (read-only endpoint subset) untuk validasi coexistence/cutover risk rendah.
- Dasar pemilihan:
  - duplikasi tinggi dan diff lebih rendah dibanding domain `admin`,
  - tersedia sample query-heavy file identik untuk contract comparison.
- Inventory endpoint path-by-path v1 + contract ringkas + backlog deferred endpoint sudah dipublikasikan di appendix Wave B `docs/PHASE6_DECISION_RECORDS.md`.
- Scope v1 untuk readiness pilot dikunci ke implementasi `vms` terlebih dahulu (host `vms.localhost`); duplikasi `intra/pengadaan` ditunda sebagai open question wave berikutnya.

### Auth/Session Strategy (Pilot)
- CI3 tetap authority session.
- App baru menggunakan signed bridge token short-lived; tidak mengakses session storage CI3 secara langsung.
- Draft matrix integration test valid/invalid/expired token untuk endpoint auth-gated pilot sudah disiapkan (appendix Wave B di `docs/PHASE6_DECISION_RECORDS.md`).
- Integration test login/logout lintas app tetap menjadi gate Wave B (implementasi + PASS masih pending).

### Data Access Strategy (Pilot)
- Shared DB read-only untuk app baru pada scope pilot.
- CI3 tetap single writer/system of record selama coexistence pilot.
- Dual-write lintas app dilarang pada pilot.

### Coexistence Baseline in Dev
- Wave A menyediakan desain + checklist + acceptance test plan di `docs/PHASE6_COEXISTENCE_DEV_BASELINE.md`.
- Wave B Stage 1 sudah membuktikan runtime shadow coexistence:
  - service placeholder `pilot-app`,
  - route shadow `/_pilot/auction/*`,
  - smoke `CX-01` + `CX-02` PASS via `tools/dev-env.ps1 -Action coexistence`.
- Route toggle subset endpoint bisnis `/auction/*` (Stage 2 / `CX-03`, `CX-04`) masih pending.

### Skeleton App Pilot Strategy (Wave B Stage 1)
- Untuk unblock runtime proof, skeleton placeholder `pilot-app` ditempatkan **di repo ini** (bukan Laravel final).
- Keputusan ini bersifat provisional dan didokumentasikan sebagai `DR-P6-005` untuk mempercepat validasi compose/Nginx/runbook tanpa menunggu repo/app final.

## Risks
- Regression business flow saat dual-stack.
- Inkonsistensi data saat dua aplikasi menulis sumber data yang sama.
- Scope creep jika domain boundary tidak tegas.

## Controls
- Contract testing untuk endpoint yang di-migrate.
- Feature flag / route toggle untuk cutover terkontrol.
- Rollback plan per domain.
- Freeze window saat cutover domain kritikal.

## Immediate Next Actions
1. Implement route toggle subset `/auction/*` (mulai `get_barang`/`get_peserta`) + rollback switch untuk `CX-03`/`CX-04`.
2. Kunci contract auth bridge (enforcement point + `401/403` semantics) lalu implement verifier minimal untuk `get_user_update`.
3. Jalankan `pilot-contract` untuk subset endpoint read-only v1 dengan schema assertions berbasis inventory appendix.
4. Jalankan `pilot-integration`, UAT, dan rollback drill dengan evidence template yang sudah disiapkan.
