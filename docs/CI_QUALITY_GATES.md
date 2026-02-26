# CI Quality Gates (Phase 4)

Dokumen ini mendefinisikan guardrail minimum yang wajib dijalankan sebelum merge ke `main`.

## Standard Commands

Jalankan dari root repo:

```powershell
pwsh ./tools/dev-env.ps1 -Action lint -PhpRuntime 7.4
pwsh ./tools/dev-env.ps1 -Action test -PhpRuntime 7.4
pwsh ./tools/dev-env.ps1 -Action smoke -PhpRuntime 7.4
```

Keterangan:
- `lint`: secret scan + baseline security checks + lint syntax file test kritikal.
- `test`: validasi bootstrap test CI3 (`vms/app/tests/test_bootstrap.php`) di container `vms-app`.
- `smoke`: validasi endpoint minimum (`vms`, `intra/main`, `intra/pengadaan`) via Nginx route.

## CI Pipeline

Workflow aktif: `.github/workflows/quality-gates.yml`

Job utama:
- `build-lint-test-smoke`

Urutan eksekusi:
1. bootstrap env template
2. build + start Docker stack
3. lint
4. test
5. smoke
6. stop stack (always)

## Phase 6 Pilot Readiness Gate Extension (Wave A Plan -> Wave B Draft)

Status saat ini: `Partial (gate definitions + endpoint matrix + integration cases drafted; belum di-enforce/di-run di CI)`  
Tujuan: menyiapkan dan mematangkan definisi gate pilot sebelum cutover, sambil memanfaatkan proof coexistence Wave B Stage 1 (`CX-01`, `CX-02`) sebagai baseline runtime.

### Planned Pilot Gates
| Gate | Scope | Status | Evidence Artifact | Notes |
|---|---|---|---|---|
| `pilot-contract` | Contract test endpoint pilot `auction` (CI3 vs app baru) | Planned | Test report/log CI | Membandingkan schema/status/body fields kritikal |
| `pilot-integration` | Auth bridge + data read path + route split pilot | Planned | Integration test report/log CI | Harus mencakup valid/invalid token case |
| `pilot-uat-evidence` | Verifikasi keberadaan sign-off UAT pilot | Planned | `docs/templates/PHASE6_PILOT_UAT_EVIDENCE_TEMPLATE.md` (instansiasi hasil) | Bisa berupa artifact upload/file repo/ticket link |
| `pilot-rollback-drill-evidence` | Verifikasi rollback drill terdokumentasi | Planned | `docs/templates/PHASE6_PILOT_ROLLBACK_DRILL_TEMPLATE.md` (instansiasi hasil) | Wajib ada timeline + MTTR + issue list |

### Draft Endpoint Matrix for `pilot-contract` (Wave B v1)
Referensi inventory source-of-truth: appendix Wave B pada `docs/PHASE6_DECISION_RECORDS.md`.

| Endpoint | Scope v1 | Minimum Assertions | Status |
|---|---|---|---|
| `auction/admin/json_provider/get_barang/{id_lelang}` | Core read-only | HTTP `200`, body array, item fields `id,name,hps,hps_in_idr` | Drafted |
| `auction/admin/json_provider/get_peserta/{id_lelang}` | Core read-only | HTTP `200`, body array, item fields `id,name` | Drafted |
| `auction/admin/json_provider/get_initial_data/{id_lelang}/{id_barang}` | Core read-only | HTTP `200`, body keys `id,name,subtitle,data,last,time` | Drafted (nested schema sample pending) |
| `auction/admin/json_provider/get_chart_update/{id_lelang}` | Core read-only | HTTP `200`, body keys `data,time` | Drafted (nested schema sample pending) |
| `auction/admin/json_provider/get_user_update/{id_lelang}/{id_user}` | Auth-gated candidate | Dipindah ke `pilot-integration` auth matrix | Drafted (auth contract pending) |

### Draft Integration Cases for `pilot-integration` (Auth Bridge)
| Case | Scenario | Expected |
|---|---|---|
| `IT-AUTH-01` | Valid bridge token | `200` + payload contract (`status`, `time`) |
| `IT-AUTH-02` | Missing token | `401`/`403` (final code pending decision) |
| `IT-AUTH-03` | Invalid signature | `401`/`403` |
| `IT-AUTH-04` | Expired token | `401`/`403` |
| `IT-AUTH-05` | Token valid but CI3 authority session invalid | `401`/`403` |

### Local Baseline Dependency Before CI Pilot Gates
- `pwsh ./tools/dev-env.ps1 -Action coexistence -PhpRuntime 7.4`
  - mencakup `CX-01` legacy smoke + `CX-02` pilot shadow route marker.
  - status: `Implemented` (local dev proof), **bukan** pengganti `pilot-contract`/`pilot-integration` CI.

### Minimum Definition of Done (Before Pilot Cutover)
1. `pilot-contract` PASS untuk endpoint pilot yang disetujui.
2. `pilot-integration` PASS (auth/session bridge + data read path + route toggle).
3. Dokumen UAT pilot terisi dan ada sign-off.
4. Dokumen rollback drill terisi dan hasil minimal `PARTIAL` dengan action item tertutup sebelum cutover.

### Implementation Notes (Wave B)
- Tambahkan workflow/job baru atau extend workflow existing dengan job pilot terpisah agar status check branch protection bisa granular.
- Nama job final harus disepakati sebelum branch protection diubah (hindari rename setelah rule aktif).
- Jika evidence UAT/rollback disimpan di sistem eksternal, CI tetap harus menyimpan pointer/link immutable pada artifact summary.
- Jangan tandai `pilot-contract` aktif sebelum schema nested (`data`/`last`) untuk endpoint `get_initial_data` dan `get_chart_update` dibekukan dari sample payload CI3.

### Templates (Wave A Prepared)
- `docs/templates/PHASE6_PILOT_UAT_EVIDENCE_TEMPLATE.md`
- `docs/templates/PHASE6_PILOT_ROLLBACK_DRILL_TEMPLATE.md`

## Required Status Check Before Merge

Atur branch protection/ruleset untuk branch `main`:
1. Buka `Settings -> Branches` (atau `Rulesets`).
2. Aktifkan `Require status checks to pass before merging`.
3. Tambahkan check wajib:
   - `build-lint-test-smoke`
4. Simpan rule.

Catatan:
- Konfigurasi enforcement branch protection tidak bisa dipaksa hanya dari file repo.
- Nama check harus sama dengan nama job workflow di atas.
- Untuk Phase 6 pilot, tambah rule/check baru hanya setelah nama job final (`pilot-contract`, `pilot-integration`, dst.) benar-benar stabil.

## Troubleshooting Test/CI

- `Action lint` gagal karena `php` tidak ditemukan:
  - Lokal: install PHP CLI 7.4+ atau pastikan `php` ada di `PATH`.
  - CI: gunakan step setup PHP (sudah ada di workflow).

- `Action test` gagal dengan `docker compose ... exec ... failed`:
  - Pastikan stack sudah jalan:
    ```powershell
    pwsh ./tools/dev-env.ps1 -Action start -PhpRuntime 7.4
    pwsh ./tools/dev-env.ps1 -Action status -PhpRuntime 7.4
    ```
  - Lihat log service:
    ```powershell
    pwsh ./tools/dev-env.ps1 -Action logs -PhpRuntime 7.4 -Service vms-app
    ```

- `Action smoke` gagal pada endpoint:
  - Pastikan service healthy:
    ```powershell
    pwsh ./tools/dev-env.ps1 -Action status -PhpRuntime 7.4
    ```
  - Cek log routing web:
    ```powershell
    pwsh ./tools/dev-env.ps1 -Action logs -PhpRuntime 7.4 -Service webserver
    ```
  - Jika diperlukan, restart stack:
    ```powershell
    pwsh ./tools/dev-env.ps1 -Action restart -PhpRuntime 7.4
    ```
