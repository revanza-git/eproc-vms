# Security Notes

- Never commit real secrets (DB passwords, SMTP passwords, API keys, JWT secrets).
- Store secrets in environment variables and keep `.env` files untracked.
- Ensure nginx denies direct access to sensitive directories (system, application, vendor, tests).

## Quick Checks

- Run the secret scanner:
  - `php scripts/scan_secrets.php`
- Run CSRF/session baseline regression check:
  - `php scripts/check_csrf_session_baseline.php`
- Run sample query safety check:
  - `php scripts/check_query_safety.php`

## Email Configuration

- VMS: [email.php](file:///c:/inetpub/eproc/vms/app/application/config/email.php)
- Intra Pengadaan: [email.php](file:///c:/inetpub/eproc/intra/pengadaan/application/config/email.php)
- Configure SMTP via environment variables; defaults are intentionally empty.

## Sessions

- Default is file-based sessions.
- To enable database-backed sessions:
  - `SESSION_DRIVER=database`
  - `SESSION_SAVE_PATH=ci_sessions`
