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
