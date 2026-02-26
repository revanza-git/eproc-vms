# Phase 6 Coexistence Dev Baseline (Wave A -> Wave B Stage 1)

## Metadata
- Created: `February 25, 2026`
- Last Updated: `February 25, 2026` (Wave B Stage 1 update)
- Status: `Stage 1 Implemented (shadow route + pilot skeleton + coexistence smoke CX-01/CX-02); Stage 2 route toggle pending`

## Purpose
Menetapkan baseline coexistence CI3 + app baru di environment dev, mulai dari artefak desain Wave A hingga implementasi proof Wave B Stage 1 (service placeholder + shadow route + smoke).

## Current State (Evidence-Based, After Wave B Stage 1)
- Service placeholder `pilot-app` sudah ditambahkan ke `docker-compose.yml` untuk proof coexistence dev.
- Route shadow `/_pilot/auction/*` sudah diarahkan ke `pilot-app` pada host `vms.localhost`.
- Helper smoke coexistence (`CX-01`, `CX-02`) sudah tersedia via `tools/dev-env.ps1 -Action coexistence`.
- Route toggle subset endpoint bisnis `/auction/*` belum diimplementasikan (masih pending Stage 2).
- Auth bridge untuk endpoint protected pilot belum diimplementasikan (pending `CX-05`).

## Wave A Decision (Baseline)
- **Tidak** memaksakan perubahan runtime/compose/Nginx yang berpotensi mematahkan stack existing tanpa adanya app pilot nyata.
- Menyediakan artefak desain + checklist implementasi + acceptance test plan sebagai prasyarat Wave B.

## Wave B Stage 1 Execution (Implemented)
- Menambahkan skeleton dev `pilot-app` (placeholder non-bisnis) untuk endpoint health.
- Menambahkan route shadow dev-only `/_pilot/auction/*` -> `pilot-app`.
- Menambahkan action `coexistence` untuk memverifikasi legacy route tetap sehat (`CX-01`) + shadow route pilot (`CX-02`).
- Menjaga route bisnis CI3 tetap default (belum ada toggle/cutover subset `/auction/*`).

## Target Coexistence Topology (Wave B Implementation Target)
```text
browser/client
  -> nginx (routing layer)
      -> CI3 vms-app / intra-app (default path legacy)
      -> pilot-app (placeholder now, Laravel final later) untuk endpoint pilot auction read-only
  -> shared services: db, redis
```

## Routing Strategy Baseline

### Stage 1 (Smoke/Shadow Path in Dev)
- Prefixed route dev-only untuk pilot:
  - `/_pilot/auction/...` -> `pilot-app`
- Tujuan:
  - validasi service wiring, healthcheck, marker header, dan contract/integration harness tanpa mengganggu route legacy.

### Stage 2 (Strangler Toggle for Pilot Paths)
- Route subset endpoint `auction` yang disetujui dipindahkan dari CI3 ke `pilot-app`.
- Routing harus mendukung toggle cepat kembali ke CI3 untuk rollback.

### Route Ownership Rule
- Default fallback tetap ke CI3 sampai endpoint masuk daftar pilot-approved.
- Route split dilakukan per-path, bukan per-host penuh, agar blast radius kecil.

## Compose/Nginx Implementation Checklist (Wave B)
| Item | Status | Target File | Acceptance Condition |
|---|---|---|---|
| Tambah service `pilot-app` (nama final boleh berbeda) | Implemented (placeholder skeleton) | `docker-compose.yml` (+ override bila perlu) | Container start healthy dan bisa diakses Nginx upstream |
| Tambah runtime override jika pilot pakai image/runtime berbeda | Partial (placeholder masih pakai local PHP-FPM image existing) | `docker-compose.php82.yml` | Override final untuk Laravel runtime masih menunggu skeleton final |
| Tambah Nginx upstream/route split untuk pilot path | Implemented (shadow path only) | `docker/nginx/default.conf` | `/_pilot/auction/*` mengarah ke `pilot-app`; subset `/auction/*` toggle masih pending |
| Tambah helper command readiness/coexistence smoke | Implemented (`coexistence`) | `tools/dev-env.ps1` | Command menghasilkan PASS/FAIL jelas untuk route CI3 + pilot |
| Dokumentasi langkah start/verify coexistence | Implemented (Wave B Stage 1) | `docs/DEV_ENV_RUNBOOK.md` | Runbook punya langkah repeatable untuk shadow-route smoke |

## Acceptance Test Plan (Wave B)
| Test ID | Goal | Steps (Ringkas) | Expected Result | Evidence | Status |
|---|---|---|---|---|---|
| CX-01 | Legacy route tetap berfungsi setelah penambahan pilot service | Start stack, hit `vms`/`main`/`pengadaan` smoke | HTTP 200 + tanpa error pattern | `tools/dev-env.ps1 -Action coexistence` output | PASS (2026-02-25) |
| CX-02 | Pilot shadow route tersambung ke `pilot-app` | Hit `/_pilot/auction/health` | HTTP 200 dari app baru + header marker app baru | `tools/dev-env.ps1 -Action coexistence` + `curl -D -` header dump | PASS (2026-02-25) |
| CX-03 | Route split subset pilot berfungsi | Enable toggle route pilot, hit endpoint pilot yang sama | Response berasal dari `pilot-app` | Contract test/integration log | Pending |
| CX-04 | Rollback route cepat ke CI3 | Disable toggle, hit endpoint pilot | Response kembali dari CI3 tanpa restart penuh | Curl output before/after + config diff | Pending |
| CX-05 | Auth bridge flow untuk endpoint protected pilot | Login -> obtain bridge token -> call pilot endpoint | 200 untuk token valid, 401/403 untuk token invalid/expired | Integration test log | Pending |

## Wave B Stage 1 Evidence (Executed)
| Date | Command | Result | Notes |
|---|---|---|---|
| 2026-02-25 | `docker compose -f docker-compose.yml config` | PASS | Compose config valid setelah tambah `pilot-app` |
| 2026-02-25 | `docker compose -f docker-compose.yml -f docker-compose.php82.yml config` | PASS | Override compose tetap valid |
| 2026-02-25 | `php -l pilot-app/public/index.php` | PASS | Skeleton endpoint health syntax valid |
| 2026-02-25 | `pwsh ./tools/dev-env.ps1 -Action start -PhpRuntime 7.4 -NoBuild` | PASS (setelah retry) | Attempt pertama gagal transient Docker daemon `EOF`; retry sukses |
| 2026-02-25 | `pwsh ./tools/dev-env.ps1 -Action coexistence -PhpRuntime 7.4` | PASS | `CX-01` + `CX-02` pass |
| 2026-02-25 | `curl.exe -sS -D - -o NUL -H "Host: vms.localhost" http://127.0.0.1:8080/_pilot/auction/health` | PASS | Header marker `X-App-Source: pilot-skeleton` + `X-Coexistence-Route: pilot-shadow` terlihat |
| 2026-02-25 | `pwsh ./tools/dev-env.ps1 -Action stop -PhpRuntime 7.4` | PASS | Cleanup stack setelah verifikasi |

## Implementation Notes for Wave B
- Nama service final (`pilot-app`, `laravel-pilot`, dll.) diputuskan saat skeleton Laravel final tersedia.
- Saat ini placeholder skeleton ditempatkan di repo ini (`pilot-app/`) untuk proof Stage 1; keputusan final repo placement dirujuk ke `DR-P6-005`.
- Jika skeleton/Laravel final berada di repo lain, gunakan volume/proxy strategy yang tidak mengubah path CI3 existing.
- Tambahkan marker response/header pada app pilot dev untuk memudahkan smoke verification (`X-App-Source`).

## Open Questions
1. Kapan placeholder `pilot-app` diganti ke skeleton Laravel final (repo ini vs repo terpisah)?
2. Jika repo terpisah dipilih untuk app final, strategi dev compose yang dipakai: submodule, bind mount sibling, atau reverse proxy ke container eksternal?
3. Mekanisme route toggle subset `/auction/*` untuk `CX-03/CX-04` akan memakai include file Nginx, env flag, atau compose profile terpisah?
