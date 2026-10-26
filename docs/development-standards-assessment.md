# Development standards assessment

Assessed on 2026-10-26 against the standards published at
https://curipedia.aircury.net/development-standards.
That page did not publish a revision identifier.

Project revision: `b8f1e974cc7e83b2cdf7a50eff21be8ff7f524cc`.
Project type: internal tool not in production.
Deviations: none.

## Position

| Dimension | Code | Agreed | Observed | Outcome |
| :--- | :--- | :--- | :--- | :--- |
| Code review | R | R3 | below R2 | Short |
| Documentation and specifications | D | D1 | below D1 | Short |
| API contracts and interoperability | I | N/A | N/A | Not applicable |
| Test coverage | C | C1 | below C1 | Short |
| E2E testing | E | N/A | N/A | Not applicable |
| Static analysis | L | L2 | below L1 | Short |
| Deployment | Y | N/A | N/A | Not applicable |
| Backups and recovery | B | N/A | N/A | Not applicable |
| Security | S | S2 | below S2 | Short |
| Observability | O | N/A | N/A | Not applicable |
| Performance | P | P1 | P1 | Meets |
| Uptime commitment | U | N/A | N/A | Not applicable |
| Support SLA | T | N/A | N/A | Not applicable |
| Accessibility and browser support | A | N/A | N/A | Not applicable |

## Gaps

### R — code review

- Agreed: R3. Observed: below R2.
- Evidence: `origin/HEAD` is `refs/heads/master` at `b8f1e974cc7e83b2cdf7a50eff21be8ff7f524cc`. The tracked tree has no `.github/workflows`, no `CODEOWNERS`, and no pull request template. Host settings for direct push and required approvals were not read.
- To close: reach `main` by pull request with one recorded approval and a green CI result, and add a `CODEOWNERS` catch-all.

### D — documentation and specifications

- Agreed: D1. Observed: below D1.
- Evidence: `README.md` explains how to build an object collection and names the scalar collections. It does not explain how to install, start, check, or contribute. The tracked tree has no `Makefile` and no `.editorconfig`; both are required of every repository.
- To close: describe install, start, check, and contribution in the README, and add the `Makefile` and `.editorconfig`.

### C — test coverage

- Agreed: C1. Observed: below C1.
- Evidence: `phpunit.xml.dist` runs the suite in `Tests/` and includes the repository root as coverage source, excluding `Tests` and `vendor`. It sets no line threshold. No workflow enforces a threshold or names untested files.
- To close: enforce the C1 line floor in CI and name every source file with no covered line.

### L — static analysis

- Agreed: L2. Observed: below L1.
- Evidence: `composer.json` requires PHPUnit, Prophecy, and `aircury/code-manager`. It does not require a static analyser or a coding-standard fixer. `code-options.yaml` sets syntax flags only. No analyser configuration is tracked, and no workflow runs one. `.gitignore` ignores `.php-cs-fixer.cache`; no fixer configuration is tracked. The agreed level is not documented in the tree.
- To close: run a static analyser in CI at a documented level with no errors, and add the format check the PHP section requires.

### S — security

- Agreed: S2. Observed: below S2.
- Evidence: `composer.lock` is committed. The tracked tree has no vulnerability threshold and no workflow that audits dependencies. No Overboards, Dependabot, or Renovate configuration is in the tree. Whether Overboards is enabled on the GitHub repository was not visible.
- To close: update dependencies daily with Overboards and block vulnerabilities above a documented threshold in CI.

## Meets

### P — performance

- Agreed: P1. Observed: P1.
- Evidence: P1 requires no performance evidence. The repository declares no performance budget.

## Not applicable

- `I` — Collection does not publish an HTTP API.
- `E` — there is no application a person uses in a browser.
- `Y` — it is not deployed. Connector consumes it as a path repository on `dev-master`.
- `B` — it retains no data and has no hosted environment.
- `O` — it has no production process.
- `U` — there is no service whose availability can be promised.
- `T` — it has no support channel of its own.
- `A` — it has no user interface.

## Unresolved

None.
