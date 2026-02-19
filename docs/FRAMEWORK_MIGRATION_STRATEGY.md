# Framework Migration Strategy (CI3 Exit Plan)

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

## Recommended Migration Pattern
Pola: `Strangler Fig`
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
- Auth: shared identity vs token bridge.
- Data: shared DB sementara vs anti-corruption layer.
- Files: strategi storage terpadu (uploads/docs).
- Jobs/Cron: penempatan eksekusi saat coexistence.

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
1. Pilih 1 domain pilot untuk migrasi pertama.
2. Definisikan arsitektur coexistence minimal di environment dev.
3. Susun backlog migration per domain berdasarkan nilai bisnis dan kompleksitas.
4. Buat skeleton Laravel app + baseline CI/CD untuk pilot domain.
