# Phase 6 Decision Records (Wave A + Wave B)

## Metadata
- Created: `February 25, 2026`
- Last Updated: `February 25, 2026` (Wave B update)
- Scope: keputusan formal minimum untuk membuka dan menjalankan readiness Wave B (pilot implementation), bukan cutover production.

## Purpose
Dokumen ini mengunci keputusan arsitektural Phase 6 yang sebelumnya masih berupa rekomendasi/TBD agar eksekusi pilot tidak berjalan dengan asumsi berbeda antar tim.

## Decision Index
| ID | Topic | Decision | Status | Review Trigger |
|---|---|---|---|---|
| DR-P6-001 | Migration pattern | `Strangler Fig` untuk coexistence bertahap CI3 + app baru | Locked (Wave A) | Jika pilot memerlukan rewrite big-bang atau gateway constraint tidak mendukung route split |
| DR-P6-002 | Pilot boundary domain pertama | `Auction` read-only endpoint subset sebagai pilot domain pertama | Locked (Wave A) | Jika endpoint inventory menunjukkan dependency write/auth kritikal tidak terisolasi |
| DR-P6-003 | Auth/session lintas aplikasi | CI3 tetap authority session; app baru pakai signed bridge token (short-lived) + trust contract eksplisit | Locked (Wave A) | Jika SSO requirement menuntut shared cookie/session storage penuh |
| DR-P6-004 | Data access strategy pilot | Shared DB read-only untuk pilot; single-writer tetap di CI3; anti-corruption layer ditunda | Locked (Wave A) | Jika pilot butuh write path atau consistency rule lintas app tidak bisa dijaga |
| DR-P6-005 | Skeleton app pilot placement (Wave B dev readiness) | Placeholder skeleton `pilot-app` ditempatkan di repo ini untuk proof coexistence dev; implementasi Laravel final boleh dipisah repo | Provisional (Wave B) | Jika tim memutuskan repo terpisah untuk app pilot Laravel final dan wiring dev berubah |

---

## DR-P6-001 - Migration Pattern

### Context
- `docs/FRAMEWORK_MIGRATION_STRATEGY.md` sudah menyebut `Strangler Fig`, tetapi status di checklist masih belum formal.
- Phase 6 membutuhkan coexistence routing dan rollback per-domain, sehingga pattern harus dikunci sebelum pilot.

### Decision
- Pattern migrasi resmi untuk Phase 6 adalah `Strangler Fig`.
- CI3 tetap menangani route legacy sebagai default.
- Endpoint pilot yang sudah masuk scope migrasi akan diarahkan ke app baru melalui routing layer (Nginx/gateway), dengan toggle/rollback path per-domain.

### Guardrails
- Dilarang big-bang cutover lintas domain.
- Cutover hanya per endpoint/domain boundary yang punya contract test + rollback evidence.
- Route split harus dapat dinonaktifkan cepat tanpa rollback data (untuk pilot read-only).

### Consequences
- Wave A fokus pada desain coexistence + contract boundary, bukan migrasi penuh domain.
- Wave B wajib memvalidasi route split dan observability untuk endpoint pilot.

---

## DR-P6-002 - Pilot Boundary Domain Pertama

### Context
- Mapping duplikasi Phase 5 menunjukkan domain `auction` memiliki duplikasi tinggi dengan diff lebih rendah dibanding `admin`, sehingga lebih cocok sebagai pilot awal.
- Sample query-heavy file identik juga tersedia pada domain `auction`, memberi dasar untuk contract comparison saat parallel-run.

### Decision
- Pilot domain pertama ditetapkan sebagai `auction` dengan boundary awal: **read-only endpoint subset** (GET/list/detail/provider JSON) yang tidak melakukan write DB.
- Boundary pilot dibatasi pada endpoint `auction` yang memenuhi kriteria:
  - response dapat dibandingkan antara CI3 vs app baru,
  - tidak memerlukan dual-write,
  - rollback cukup dengan route switch kembali ke CI3.

### In Scope (Wave B candidate)
- Endpoint read-only `auction` (detail inventory endpoint final akan dikunci di backlog pilot Wave B).
- Contract comparison request/response untuk subset yang dipilih.
- Route toggle untuk subset endpoint pilot.

### Out of Scope (Wave B)
- Endpoint `auction` yang melakukan write/update state.
- Domain `pengadaan`/`kontrak`/`k3` yang punya perbedaan model/query signifikan.
- Decommission modul CI3 `auction`.

### Rollback Boundary
- Rollback dilakukan di routing layer (toggle route ke CI3).
- Tidak ada rollback data untuk pilot karena policy pilot read-only (lihat DR-P6-004).

### Open Questions
1. Daftar endpoint `auction` final (path-by-path) belum diinventarisir di repo saat Wave A.
2. Apakah semua endpoint pilot membutuhkan autentikasi user, atau ada subset publik/internal service?

---

## DR-P6-003 - Auth/Session Strategy Lintas Aplikasi

### Context
- Implementasi legacy lintas aplikasi belum menunjukkan kontrak session/auth yang seragam.
- Pilot butuh strategi yang cukup aman untuk coexistence tanpa memaksa shared session internals sejak awal.

### Decision
- **Authority session tetap di CI3** selama pilot.
- App baru **tidak** dipercaya langsung membaca/mutasi session storage CI3.
- Cross-app auth memakai **signed bridge token** (short-lived) yang diterbitkan oleh CI3 bridge endpoint/gateway contract dan diverifikasi oleh app baru.

### Minimum Contract (Pilot)
- Token berisi minimal: `sub` (user id), `sid_ref` (session reference), `iat`, `exp`, `issuer`, `aud`, `roles/version`.
- Signature: HMAC SHA-256 (minimum baseline) dengan secret terpisah per environment.
- TTL token pendek (contoh target <= 5 menit) dan dapat direfresh via bridge endpoint.
- Logout di CI3 harus menginvalkan sesi authority; app baru wajib treat token as invalid setelah introspection fail/expired.

### Guardrails
- Tidak memakai shared-cookie trust antar aplikasi sebagai baseline pilot.
- Tidak menyalin session payload CI3 penuh ke app baru.
- Validasi signature + expiry wajib sebelum proses request di app baru.

### Consequences
- Wave B perlu endpoint/contract bridge dan integration test login/logout lintas app.
- Key management dan rotation policy harus ditentukan sebelum implementasi production-like pilot.

### Open Questions
1. Kanal penerbitan token bridge terbaik (Nginx auth_request, endpoint CI3 khusus, atau gateway service) belum dipilih.
2. Lokasi secret signing dan rotasinya (Vault/env/CI secret store) belum didokumentasikan di repo ini.

---

## DR-P6-004 - Data Access Strategy (Pilot)

### Context
- Belum ada keputusan formal apakah pilot memakai shared DB langsung atau anti-corruption layer (ACL/service).
- Pilot awal butuh time-to-value cepat tetapi tetap menghindari dual-write inconsistency.

### Decision
- Strategy data access untuk **pilot Wave B**: **shared DB read-only** dengan CI3 tetap sebagai **single writer / system of record**.
- ACL/service layer ditunda untuk domain yang membutuhkan write orchestration atau transformasi kompleks.

### Read/Write Ownership (Pilot)
- App baru: `READ ONLY` pada tabel yang dipakai endpoint pilot `auction`.
- CI3: tetap `READ/WRITE` untuk domain pilot selama coexistence.
- Dual-write lintas app: **dilarang** pada pilot.

### Consistency Policy
- Eventual consistency diterima untuk data read pilot selama sumber data tetap CI3 DB.
- Jika ditemukan kebutuhan write pada pilot scope, pilot harus dipersempit atau keputusan ini direview ulang.

### Guardrails Implementasi
- Query di app baru dibatasi ke repository/service khusus pilot (tidak scatter raw query lintas domain).
- Migration schema DB oleh app baru tidak dilakukan pada Wave B tanpa review terpisah.
- Logging query/response mismatch wajib saat parallel-run comparison.

### Consequences
- Mengurangi risiko rollback karena tidak ada perubahan ownership write.
- Membatasi value pilot ke endpoint read-only, tetapi mempercepat validasi coexistence.

### Open Questions
1. Daftar tabel exact untuk pilot `auction` belum dipetakan (perlu backlog Wave B).
2. Apakah read replica tersedia/diinginkan untuk pilot dev/test, atau tetap shared primary DB dev?

---

## DR-P6-005 - Skeleton App Pilot Placement (Wave B Dev Readiness)

### Context
- Wave A membuka guardrail bahwa lokasi skeleton app pilot (repo ini vs repo terpisah) harus diputuskan agar coexistence runtime bisa dieksekusi.
- Untuk menutup gap B2, repo ini perlu bukti route split + service wiring yang dapat dijalankan tanpa menunggu implementasi bisnis Laravel final.

### Decision (Provisional for Wave B Stage 1)
- Buat **placeholder skeleton dev** `pilot-app` di repo ini (folder `pilot-app/`) untuk membuktikan:
  - service `pilot-app` pada `docker-compose`,
  - route shadow `/_pilot/auction/*` di Nginx,
  - smoke coexistence `CX-01` dan `CX-02`.
- Skeleton ini **bukan** implementasi Laravel final dan **tidak** memuat kode bisnis `auction`.
- Implementasi Laravel pilot final boleh:
  - tetap di repo ini (replace isi `pilot-app`), atau
  - pindah ke repo terpisah dengan tetap mempertahankan kontrak route shadow/dev smoke yang sama.

### Tradeoffs
#### Opsi A - Skeleton di repo ini (dipilih untuk Wave B Stage 1)
- Pro:
  - unblock cepat untuk B2 (compose/Nginx/runbook bisa dibuktikan sekarang),
  - tidak perlu dependency repo eksternal untuk smoke CX-01/CX-02.
- Kontra:
  - bukan representasi final struktur Laravel,
  - perlu migrasi ulang wiring jika app final dipindah repo.

#### Opsi B - Langsung repo terpisah (ditunda)
- Pro:
  - lebih dekat ke arsitektur final pilot.
- Kontra:
  - blocker tambahan (repo bootstrap, akses, path mount, sinkronisasi compose),
  - menunda validasi coexistence dasar.

### Implication to Dev Runtime
- `docker-compose.yml`: service `pilot-app` placeholder dev-only.
- `docker/nginx/default.conf`: shadow route `/_pilot/auction/*` diarahkan ke `pilot-app`.
- `tools/dev-env.ps1`: action `coexistence` untuk smoke legacy + pilot shadow.
- `docs/DEV_ENV_RUNBOOK.md`: langkah start/verify coexistence diperbarui.

### Review Trigger
- Saat skeleton Laravel final tersedia atau diputuskan repo terpisah.
- Saat mulai implementasi route toggle subset `/auction/*` (Stage 2 / CX-03, CX-04).

---

## Wave B Evidence Appendix - Pilot `auction` Read-Only Inventory (v1)

Status: `Drafted (path-by-path) - usable for Wave B contract/integration planning`

Scope baseline appendix ini dibatasi ke implementasi `vms` (`vms/app/application/modules/auction/...`) sebagai target shadow/coexistence dev pertama. Duplikasi modul `auction` di `intra/pengadaan` dicatat sebagai `Open Question` untuk wave berikutnya.

### Path-by-Path Endpoint Inventory (Candidate Pilot)
| Path (CI3) | Method (Observed/Expected) | Pilot Scope | Expected Success Status | Critical Response Fields (Observed) | Auth Requirement (Working Assumption) | Evidence | Open Question |
|---|---|---|---|---|---|---|---|
| `/auction/admin/json_provider/get_barang/{id_lelang}` | GET (expected from JS; not enforced in controller) | `IN` (core) | `200` | Array of `{id,name,hps,hps_in_idr}` | Protected (admin/user UI data feed), but controller auth guard not explicit | `vms/app/application/modules/auction/controllers/admin/Json_provider.php`, `vms/app/application/modules/auction/views/admin/master_js.php`, `vms/app/application/modules/auction/views/user/content_js.php` | Method restriction and role enforcement point (controller/filter/hook) belum terkonfirmasi |
| `/auction/admin/json_provider/get_peserta/{id_lelang}` | GET (expected) | `IN` (core) | `200` | Array of `{id,name}` | Protected (admin UI feed), controller auth guard not explicit | `vms/app/application/modules/auction/controllers/admin/Json_provider.php`, `vms/app/application/modules/auction/views/admin/master_js.php` | Perlu verifikasi apakah endpoint boleh diakses vendor/user |
| `/auction/admin/json_provider/get_initial_data/{id_lelang}/{id_barang}` | GET (expected) | `IN` (core) | `200` | Object `{id,name,subtitle,data,last,time}` | Protected (admin live auction view), controller auth guard not explicit | `vms/app/application/modules/auction/controllers/admin/Json_provider.php`, `vms/app/application/modules/auction/views/admin/master_js.php` | Bentuk nested `data/last` perlu sample response runtime untuk contract schema final |
| `/auction/admin/json_provider/get_chart_update/{id_lelang}` | GET (expected) | `IN` (core) | `200` | Object `{data,time}` | Protected (admin live auction refresh), controller auth guard not explicit | `vms/app/application/modules/auction/controllers/admin/Json_provider.php`, `vms/app/application/modules/auction/views/admin/master_js.php` | Struktur elemen `data` belum terpetakan tanpa sample runtime |
| `/auction/admin/json_provider/get_user_update/{id_lelang}/{id_user}` | GET (expected) | `IN` (auth-bridge candidate) | `200` | Object `status.{is_started,is_finished,is_suspended}`, `time.{now,limit}`, optional `data` | Protected (user-specific; fallback `id_user` dari session/utility jika kosong) | `vms/app/application/modules/auction/controllers/admin/Json_provider.php`, `vms/app/application/modules/auction/views/user/content_js.php` | Mekanisme auth/session enforcement endpoint ini belum eksplisit di controller; butuh bridge contract + test valid/invalid token |
| `/auction/admin/json_provider/get_vendor_rank/{id_lelang}/{type}` | GET (expected) | `BACKLOG` (defer) | `200` | JSON string berisi HTML tabel | Protected (admin UI export/render helper), controller auth guard not explicit | `vms/app/application/modules/auction/controllers/admin/Json_provider.php`, `vms/app/application/modules/auction/views/admin/master_js.php` | Contract kurang stabil (HTML-in-JSON), kurang ideal untuk pilot contract comparison v1 |

### Pilot Subset Decision for Wave B Stage 1 / Stage 2
- **Stage 1 (Shadow/Coexistence proof, no business contract yet):**
  - `/_pilot/auction/health` (skeleton-only health endpoint; bukan endpoint bisnis CI3).
- **Stage 2 (Contract comparison pilot candidate - read-only subset):**
  - `get_barang`
  - `get_peserta`
  - `get_initial_data`
  - `get_chart_update`
- **Stage 2.5 (Auth bridge validation candidate):**
  - `get_user_update`
- **Deferred from v1 pilot contract set:**
  - `get_vendor_rank` (HTML-in-JSON)

### Initial Contract Test Matrix (Draft for `pilot-contract`)
| Endpoint | CI3 Baseline Status | Contract Assertions (Minimum) | Comparison Strategy |
|---|---|---|---|
| `get_barang` | `200` | body = array; each item has `id,name,hps,hps_in_idr` | Compare field presence + scalar type class + item count tolerance |
| `get_peserta` | `200` | body = array; each item has `id,name` | Compare field presence + item count + sort-stability check (if deterministic) |
| `get_initial_data` | `200` | body has `id,name,subtitle,data,last,time` | Compare top-level keys + parseable `time`; nested payload schema snapshot |
| `get_chart_update` | `200` | body has `data,time` | Compare top-level keys + parseable `time`; nested payload schema snapshot |
| `get_user_update` | `200` valid auth | body has `status` + `time`, optional `data` | Separate auth-gated matrix (valid/invalid/expired token) |

### Initial Integration Test Plan (Draft for `pilot-integration`)
| Test ID | Endpoint | Scenario | Expected |
|---|---|---|---|
| IT-AUTH-01 | `get_user_update` (pilot app route equivalent) | Valid bridge token | `200`, body contains `status` and `time` |
| IT-AUTH-02 | `get_user_update` | Missing token | `401`/`403` (final code to be locked in bridge contract) |
| IT-AUTH-03 | `get_user_update` | Invalid signature token | `401`/`403` |
| IT-AUTH-04 | `get_user_update` | Expired token (`exp` passed) | `401`/`403` |
| IT-AUTH-05 | `get_user_update` | Token valid but user/session no longer valid at CI3 authority | `401`/`403` |
| IT-RO-01 | `get_barang` / `get_peserta` | Valid token (or internal trusted route if no token required) | `200`, read-only payload contract match |
| IT-RO-02 | `get_initial_data` / `get_chart_update` | Route shadow enabled | Response source marker menunjukkan `pilot-app` saat toggle ON |

### Open Questions (Wave B)
1. Apakah pilot path contract v1 hanya untuk `vms` host, atau harus mencakup duplikasi `intra/pengadaan` sejak awal?
2. Endpoint `Json_provider` tidak menunjukkan auth guard eksplisit di controller; enforcement sebenarnya di mana (hook/base controller/filter/Nginx)?
3. Kode final auth bridge error untuk token invalid/expired akan `401` atau `403`?
4. Perlu sample payload anonymized untuk membekukan schema nested `data`/`last` sebelum `pilot-contract` diaktifkan.
