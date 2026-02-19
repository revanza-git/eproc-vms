# Credential Rotation Log (Phase 2)

## Metadata
- Date: `2026-02-19`
- Scope: `eproc-vms` repository credential hygiene baseline.
- Reason: historical hardcoded credential exposure ditemukan di file test/utilitas repo.

## Actions Completed
1. Removed historical hardcoded password literal from repository code paths:
   - `Nusantara1234` di file test utilitas diganti ke env-based config (`E2E_DB_*` / `DB_EPROC_*`).
2. Removed hardcoded encryption key value in app config:
   - `intra/pengadaan/application/config/config.php` sekarang menggunakan `ENCRYPTION_KEY` dari environment.
3. Replaced default secret placeholders in templates:
   - `.env.example`, `vms/.env.example`, `intra/.env.example` diubah ke placeholder aman (`change_me_local`, `change_me_local_jwt`), bukan nilai statik lama.

## Safe Placeholders (Current)
- `ENCRYPTION_KEY=change_me_local`
- `JWT_SECRET_KEY=change_me_local_jwt`
- `E2E_DB_PASSWORD` default kosong (`''`) pada script test.

## Manual Rotation Follow-up (Outside Repo)
1. Rotate real DB account password used by shared dev/test DB instance.
2. Rotate app-level encryption/JWT key yang pernah dipakai di environment non-lokal.
3. Update secret manager / deployment variable store sebelum next release.

## Validation Reference
- Secret scan command: `php scripts/scan_secrets.php`
- Baseline evidence dicatat di `docs/REVAMP_CHECKLIST.md` (Phase 2).
