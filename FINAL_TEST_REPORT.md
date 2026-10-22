# GovAssist – Final Alpha & Beta Test Report

Date: 2026-10-22 · Stack: Laravel 13, PHP 8.5, Pest 4 · Result: **135 tests: 134 passing, 1 skipped (needs tesseract; runs in CI) – 762 assertions**

## Scope
- **Alpha (internal, code-level):** review of controllers/services for authorization, validation and failure handling; full automated suite.
- **Beta (user-flow):** simulated resident and facilitator journeys in `tests/Feature/FinalAcceptanceTest.php` (12 new tests).

## Coverage
| Area | Result |
|---|---|
| All 100 routes registered; every parameterless resident/facilitator page rendered in EN / CEB / FIL | Pass (no 5xx) |
| Report exports (applications, residents, assessments, inquiries, master) | Pass |
| Role gates (resident ↔ facilitator, guest, unverified email) | Pass |
| Cross-resident access (results, documents, inquiry read/reply/resolve/delete, reply delete) | Pass – all blocked |
| Upload validation (type, size, locked checklist, no orphan files) | Pass |
| Duplicate assessment submission | Pass – blocked |
| Facilitator self-delete protection; XSS escaping in user list | Pass |

## Bugs found and fixed
| # | Severity | Bug | Fix |
|---|---|---|---|
| 1 | Medium | A resident could file a **reassessment request for a program they never assessed** (`ResidentController@requestReassessment`), cluttering the facilitator queue. | Request is refused unless an assessment exists. |
| 2 | Medium | Creating an eligibility question ran `shell_exec` for translation with **no timeout and no error handling**; a hung or missing Python blocked the request / could 500. | Replaced with `Process::timeout(30)` (argument array, no shell), failures logged, falls back to the original text. |

Both have regression tests.

## Follow-up fixes (second pass)
| Item | Change |
|---|---|
| Free-text questions never scored | Facilitators can set accepted keywords (comma-separated, case-insensitive "mentions" match). Blank keeps the old informational behaviour. |
| Unpaginated lists / extra queries | Residents and Assessments lists now paginate (20/page) with server-side search and database-computed stats. Applications was already paginated. Reassessments and Inquiries stay unpaginated (small, grouped views). |
| CI had no tests | New `.github/workflows/tests.yml` (installs tesseract + Python deps, runs Pest); `deploy.yml` now requires it to pass before deploying. |
| OCR untested | Added `DocumentOcrEndToEndTest` (real script, no mocks): graceful-degradation and exact-match tests run everywhere; the tesseract verified/mismatch test runs in CI. **Not run locally – tesseract not installed.** |

## Known open items (not changed – need owner decision)
1. Plaintext DB password in `.github/workflows/deploy.yml` – **rotate it and move to secrets**.
2. Seeded facilitator account uses a default password – change before production.
3. Business rules rely on service/requirement **name substrings** (employment, PDS, household income).
4. Inquiries and Reassessments pages are still unpaginated.
5. First CI run should be watched: the tesseract end-to-end test has never executed.
6. Working tree has many uncommitted files; nothing committed or pushed.

## Verdict
No critical or high-severity defects remain in tested paths. Ready for release once items 1–2 are handled and OCR is checked on the deployment server.
