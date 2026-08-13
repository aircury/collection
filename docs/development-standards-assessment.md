# Development standards assessment

Assessed on 2026-08-11 against the standards published at
https://curipedia.aircury.net/development-standards

## Position

| Dimension | Code | Agreed | Observed | Outcome |
| :--- | :--- | :--- | :--- | :--- |
| Code review | R | R1 | ? | Unresolved |
| Documentation and specifications | D | D1 | D0 | Short |
| API contracts and interoperability | I | - | - | Not applicable |
| Test coverage | C | C1 | ? | Unresolved |
| E2E testing | E | - | - | Not applicable |
| Static analysis | L | L1 | ? | Unresolved |
| Deployment | Y | - | - | Not applicable |
| Backups and recovery | B | - | - | Not applicable |
| Security | S | S1 | ? | Unresolved |
| Observability | O | - | - | Not applicable |
| Performance | P | P1 | P1 | Meets |
| Uptime commitment | U | - | - | Not applicable |
| Support SLA | T | - | - | Not applicable |
| Accessibility and browser support | A | - | - | Not applicable |

## Gaps

### D — documentation and specifications

- Agreed: D1. Observed: D0.
- Evidence: `README.md` describes library usage, but does not explain how to install, start, check, and contribute (operational README requirements).
- To close: add an operational README section covering install, start, verification steps (tests/linters), and how to contribute, matching D1.

## Not applicable

- `I` — this is a PHP library and does not expose an HTTP API.
- `E` — this package does not provide browser-based end-to-end journeys.
- `Y` — this package is not deployed as a standalone service.
- `B` — the package does not retain any data of its own.
- `O` — this package does not run as a production service with operational observability.
- `U` — this package does not publish an availability commitment.
- `T` — this package does not publish a support SLA.
- `A` — this package does not include user-facing browser UI.

## Unresolved

- `R` — code review expectations (direct-push allowed vs required PR approvals) cannot be established from repository files; branch protection and PR approval routing live in hosting/repo settings.
  - Evidence: no `CODEOWNERS` file and no CI/PR template evidence in this repository for routing and required approvals.
- `C` — whether test coverage is measured and enforced in CI cannot be established from repository files.
  - Evidence: `phpunit.xml.dist` defines the test suite but there is no coverage gate configuration or CI workflow in-repo.
- `L` — whether static analysis runs in CI with the recommended configuration cannot be established from repository files.
  - Evidence: the repository has no CI workflow definitions or pinned analyser configuration in-repo.
- `S` — whether dependency vulnerability updates are automated and gated in CI cannot be established from repository files.
  - Evidence: no in-repo dependency update automation configuration (e.g. no `.github/dependabot.yml`) to verify.

