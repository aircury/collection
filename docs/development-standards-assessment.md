# Development standards assessment

Assessed on 2026-10-26 against the standards published at
[https://curipedia.aircury.net/development-standards](https://curipedia.aircury.net/development-standards).
That page did not publish a revision identifier.

Project revision: `b8f1e974cc7e83b2cdf7a50eff21be8ff7f524cc`.
Project type: internal tool not in production.
Deviations: none.

## Position


| Dimension                          | Code | Agreed | Observed | Outcome        |
| ---------------------------------- | ---- | ------ | -------- | -------------- |
| Code review                        | R    | R4     | below R2 | Short          |
| Documentation and specifications   | D    | D1     | below D1 | Short          |
| API contracts and interoperability | I    | N/A    | N/A      | Not applicable |
| Test coverage                      | C    | C4     | below C1 | Short          |
| E2E testing                        | E    | N/A    | N/A      | Not applicable |
| Static analysis                    | L    | L3     | below L1 | Short          |
| Deployment                         | Y    | N/A    | N/A      | Not applicable |
| Backups and recovery               | B    | N/A    | N/A      | Not applicable |
| Security                           | S    | S4     | below S2 | Short          |
| Observability                      | O    | N/A    | N/A      | Not applicable |
| Performance                        | P    | P1     | P1       | Meets          |
| Uptime commitment                  | U    | N/A    | N/A      | Not applicable |
| Support SLA                        | T    | N/A    | N/A      | Not applicable |
| Accessibility and browser support  | A    | N/A    | N/A      | Not applicable |




## Gaps



### R — code review

- Agreed: R4. Observed: below R2.
- Evidence: `origin/HEAD` is `refs/heads/master` at `b8f1e974cc7e83b2cdf7a50eff21be8ff7f524cc`. The tracked tree has no `.github/workflows`, no `CODEOWNERS`, and no pull request template. Host settings for direct push and required approvals were not read. R2 is a pull request into `main` whose CI passed. R4 adds two approvals from different people, recorded before merge, and a `CODEOWNERS` catch-all. The type minimum is R1. None of that gate is present, so the observed position stays below R2.
- To close: reach `main` by pull request with two approvals from different people and a green CI result, and add a `CODEOWNERS` catch-all.



### D — documentation and specifications

- Agreed: D1. Observed: below D1.
- Evidence: `README.md` explains how to build an object collection and names the scalar collections. It does not explain how to install, start, check, or contribute. The tracked tree has no `Makefile` and no `.editorconfig`; both are required of every repository.
- To close: describe install, start, check, and contribution in the README, and add the `Makefile` and `.editorconfig`.



### C — test coverage

- Agreed: C4. Observed: below C1.
- Evidence: `phpunit.xml.dist` runs the suite in `Tests/` and includes the repository root as coverage source, excluding `Tests` and `vendor`. It sets no line threshold. No workflow enforces a threshold or names untested files. On 2026-10-26, `php bin/phpunit` on PHP 8.3.6 reported OK (84 tests, 270 assertions). Asking the same run for coverage reported that no code coverage driver is available, so a line percentage was not measured. A passing suite is not a coverage gate. C1 is a CI gate at 25% of lines that names every source file with executable code and no covered line. C4 is that gate at 95% of lines. From C3 upwards, an exemption may name only generated code or a process entry point. Neither gate is present.
- To close: enforce the C4 line floor in CI and name every source file with no covered line.



### L — static analysis

- Agreed: L3. Observed: below L1.
- Evidence: `composer.json` requires PHPUnit, Prophecy, and `aircury/code-manager`. It does not require a static analyser or a coding-standard fixer. `code-options.yaml` sets syntax flags only. No analyser configuration is tracked, and no workflow runs one. `.gitignore` ignores `.php-cs-fixer.cache`; no fixer configuration is tracked. L1 is an analyser running in CI on its recommended configuration. L2 is a documented level that permits no errors, plus the format check the PHP section requires, the unused-dependency check, and the file-size, duplication, and complexity gates. L3 is the strictest level, permitting no errors or warnings and no baseline, plus ShellCheck and a workflow linter. The PHP section requires at least L2 and prefers L3. The type minimum is L1. The agreed floor is L3, not L2, because this library reaches Connector through `dev-master` and Connector's agreed floor is L3. None of that gate is present, so the observed position stays below L1.
- To close: run the strictest analyser level in CI with no errors or warnings and no baseline, document that level, and add the format check, the unused-dependency check, the file-size, duplication, and complexity gates, ShellCheck, and a workflow linter.



### S — security

- Agreed: S4. Observed: below S2.
- Evidence: `composer.lock` is committed. The tracked tree has no vulnerability threshold and no workflow that audits dependencies. No Overboards, Dependabot, or Renovate configuration is in the tree. Whether Overboards is enabled on the GitHub repository was not visible. Collection publishes no HTTP response, so the header, cookie, and rate-limit rules have no response to attach to in this repository. They do not lower the agreed floor. S1 is Overboards updating dependencies daily. Dependabot and Renovate do not meet that. S2 adds a documented vulnerability threshold that CI blocks on. Where security applies, the published minimum is S2. The type row lists security as N/A. S3 adds a documented review at least annually and before a material risk change. S4 adds a threat model, audit logging, and secrets rotation, all three documented, active, and reviewed at least annually. The agreed floor is S4, not S2 or S3, because this library's code and Composer dependencies reach Connector through `dev-master` and Connector's agreed floor is S4. None of the S2 gate is present, so the observed position stays below S2.
- To close: update dependencies daily with Overboards, block vulnerabilities above a documented threshold in CI, review security at least annually and before a material risk change, and keep a threat model, audit logging, and secrets rotation documented, active, and reviewed at least annually.



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