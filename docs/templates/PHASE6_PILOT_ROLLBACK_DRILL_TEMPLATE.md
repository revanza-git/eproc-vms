# Phase 6 Pilot Rollback Drill Evidence Template

## Metadata
- Pilot Domain:
- Build/Commit:
- Environment:
- Drill Date:
- Incident Commander / Facilitator:
- Participants:

## Rollback Trigger Definition
- Trigger condition yang disimulasikan:
- Detection signal:
- Decision maker:

## Rollback Scope
- Endpoint/route pilot yang di-rollback:
- Data scope:
- Toggle/config file yang diubah:

## Execution Timeline
| Time (UTC+7 or specify TZ) | Step | Expected Outcome | Actual Outcome | Status |
|---|---|---|---|---|
| T0 |  |  |  |  |
| T+5m |  |  |  |  |
| T+10m |  |  |  |  |

## Validation Checklist (Post-Rollback)
- [ ] Route pilot kembali ke CI3
- [ ] Endpoint legacy merespons normal
- [ ] Auth/session flow tetap valid
- [ ] Error rate/log anomali kembali normal
- [ ] Tidak ada perubahan data yang perlu rollback manual (pilot read-only)

## Metrics
- Detection to decision:
- Decision to rollback complete:
- Total MTTR:

## Issues Found During Drill
| ID | Severity | Issue | Impact | Follow-up Owner | Due Date |
|---|---|---|---|---|---|
| RB-01 |  |  |  |  |  |

## Final Decision
- Result: `PASS` / `PARTIAL` / `FAIL`
- Notes:
- Approved By:
- Approval Timestamp:
