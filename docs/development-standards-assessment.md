# Development standards assessment

Assessed on 2026-11-27 against the standards published at
https://curipedia.aircury.net/development-standards (fetched 2026-11-27; the
page carries no revision identifier, so the revision is recorded as the
SHA-256 of the fetched document, prefix `165bfc0e107b`).

Project revision observed: `d1d8e28` on branch `DS4`. `make ci` passes on it.

Project type: internal tool in production.

## Position

| Dimension | Code | Agreed | Observed | Outcome |
| :--- | :--- | :--- | :--- | :--- |
| Code review | R | R3 | R1 | Short |
| Documentation and specifications | D | D1 | D1 | Meets |
| API contracts and interoperability | I | - | - | Not applicable |
| Test coverage | C | C2 | C4 | Meets |
| E2E testing | E | - | - | Not applicable |
| Static analysis | L | L2 | L1 | Short |
| Deployment | Y | - | - | Not applicable |
| Backups and recovery | B | - | - | Not applicable |
| Security | S | S2 | None | Short |
| Observability | O | - | - | Not applicable |
| Performance | P | P1 | P1 | Meets |
| Uptime commitment | U | - | - | Not applicable |
| Support SLA | T | - | - | Not applicable |
| Accessibility and browser support | A | - | - | Not applicable |

## Gaps

### R — code review

- Agreed: R3. Observed: R1.
- Evidence: `master` has no branch protection and the repository has no
  rulesets (GitHub API, 2026-11-27), so direct pushes are permitted. There is
  no `CODEOWNERS` file. Recent pull requests carry approving reviews, but
  nothing requires them before merge.
- To close: enable branch protection on the main branch requiring the `CI`
  check and one approving review (available, as the repository is public),
  and add a `CODEOWNERS` file with a single catch-all rule whose handles are
  verified to resolve.

### L — static analysis

- Agreed: L2. Observed: L1.
- Evidence: `phpstan.neon` sets level `max` with no baseline, and
  `make phpstan` runs in `make ci`'s second tier with zero errors.
  What L2 adds beyond that is absent:
  - no check that every Composer package is referenced or excused;
    `phpspec/prophecy-phpunit` is declared in `require-dev` and no test
    references Prophecy;
  - the shape rules from `aircury/code-manager` are fixed ceilings (800 lines
    per file, cyclomatic complexity 10, duplicate methods of 5+ statements):
    there is no warning band or per-run size report for file size, test code
    is not exempt from the size ceiling, and duplication is not budgeted as a
    proportion per declared area.
- To close: add a check in `make ci` that every Composer package, `require-dev`
  included, is referenced or excused with a reason (for example composer-unused,
  which judges only `require`), and bring the three shape gates to the
  form L2 describes.

### S — security

- Agreed: S2. Observed: neither S1 nor the S2 gate is in place.
- Evidence: Overboards is not configured to update this repository's
  dependencies (stated by the person who invoked this run; not visible in the
  repository). No dependency vulnerability audit runs in `make ci` or CI, and
  no vulnerability threshold policy is documented. `composer audit` reports 6
  advisories affecting 2 locked packages.
- To close: configure Overboards' daily dependency updates for the
  repository, document the vulnerability threshold policy, and add a
  `composer audit` gate to `make ci` that blocks above it.

## Not applicable

- `I` — this is a PHP library and does not expose an HTTP API.
- `E` — this package does not provide browser-based end-to-end journeys.
- `Y` — this package is not deployed as a standalone service.
- `B` — the package does not retain any data of its own.
- `O` — this package does not run as a production service with operational observability.
- `U` — this package does not publish an availability commitment.
- `T` — this package does not publish a support SLA.
- `A` — this package does not include user-facing browser UI.

## Repository-level requirements

43 of 107 repository-checkable requirements are met (25 not met, 37 not
applicable, 2 not assessed).

| Section | Requirement | Outcome | Evidence |
| :--- | :--- | :--- | :--- |
| Developing with Overboards | A plan to converge on Overboards lives in `docs/overboards-convergence-plan.md` | Not met | No such file |
| Developing with Overboards | A non-standard Overboards contract is approved by the CAB and recorded | Not assessed | The board's contracts are not visible from the repository |
| Commitment levels | Where what is automated is narrower than what is declared, the difference is recorded where it is declared | Not met | `composer.json` allows PHP `^8.2 \|\| ^8.3` and the README says 8.3 or a later 8.x; CI tests only PHP 8.3, and the difference is recorded nowhere |
| Every repository → README | A project started from the company template records its adoption | Not applicable | The repository dates from 2017 and was not started from the template |
| Every repository → Makefile | A command reprints a multi-service stack's addresses | Not applicable | No services; `make urls` says so |
| Every repository → Makefile | `make seed` loads a known example dataset | Not applicable | The library needs no data to be seen working |
| Every repository → Makefile | Docker Official Images are pulled from `public.ecr.aws/docker/library` | Not met | `Dockerfile` pulls `php` and `composer` from Docker Hub |
| Every repository → Makefile | CI logs in to the registry through OIDC before pulling, falling back to anonymous with a warning | Not met | No registry login in the workflow |
| Git and code review | The main branch is called `main` | Not met | Default branch is `master` |
| Git and code review | Commits follow conventional commits in English, with the card reference at the front | Not met | Recent commits read `Collection \| Dev Standards \| ...`: no card reference and no conventional type |
| Git and code review | A pull request template asks what, why, how it was verified, and notes for the reviewer | Not met | No pull request template |
| Documentation | A project configured for AI agents has `AGENTS.md`, skills under `.agents/`, symlinked entry files and a traps register | Not applicable | The project is not configured for AI agents |
| Documentation | Specifications and ADRs live under `/specs` in the required shape | Not applicable | The project has no specifications or ADRs (D1) |
| Architecture | A DDD project has an architectural boundaries check | Not applicable | The library does not use DDD |
| Architecture | A second complete copy of the stack can be started beside the first | Not applicable | The library runs no service |
| Infrastructure and databases | AWS, Symfony, PostgreSQL and AppSync are the stack, or a deviation is CAB-approved | Not applicable | A library with no backend service, database or realtime channel |
| Infrastructure and databases | Schema changes live in versioned migrations | Not applicable | No database |
| Infrastructure and databases | The schema is documented in DBML with a CI drift check | Not applicable | No database |
| Infrastructure and databases | Production stores have deletion protection; reset and seed commands refuse unless allowed; environment differences are capabilities | Not applicable | No environments |
| Infrastructure and databases | Growing tables have a retention rule, a scheduled job and an index | Not applicable | No database |
| Infrastructure and databases | Buckets are private, versioned, with lifecycle rules and a guarded orphan cleanup | Not applicable | No object storage |
| Infrastructure and databases | Data-deleting housekeeping runs on its own schedule | Not applicable | No housekeeping jobs |
| Infrastructure and databases | Unattended scheduled jobs have a dead-letter queue, bounded retries, idempotent dry-runnable commands and sweep counts | Not applicable | No scheduled jobs |
| Internal URLs | Internal URLs use `<host>.localhost.aircury.net` or a client equivalent | Not applicable | No hosted URLs |
| Web crawlers | Every environment declares a no-crawl, no-index policy and publishes no site map | Not applicable | No websites |
| Testing | A test run fails on skipped, incomplete, assertion-free or focused tests, an empty run, and the runner's deprecations, notices and warnings | Not met | `phpunit.xml.dist` sets none of `failOnRisky`, `failOnWarning`, `failOnDeprecation`, `failOnNotice`, `failOnSkipped`, `failOnIncomplete`, `failOnEmptyTestSuite` |
| Testing | Each suite holds a time budget in versioned data, enforced by a nightly run | Not met | No budget, no nightly workflow |
| Testing | Tests set up their own data and never assert on the example dataset | Not applicable | No example dataset |
| Testing | Tests that commit run in a throwaway database | Not applicable | No database |
| Testing | A fake adapter is held to the real one by a shared contract suite | Not applicable | No adapters |
| Static analysis | A baseline comes with a plan to zero and a target date, and CI fails when it grows | Not applicable | No baselines |
| Static analysis | Shell scripts pass ShellCheck at `style`, pinned, found from the tracked files | Not applicable | No shell scripts are tracked |
| Static analysis | Workflows pass a pinned linter such as actionlint | Not met | No workflow linter runs |
| Static analysis | Every ratchet gate offers check, report and update, with distinct exit codes | Not applicable | No ratchet gates |
| PHP projects | A static PHP analyser at L2 or above | Not met | PHPStan runs at level `max` with zero errors, but L2 is incomplete (see L) |
| PHP projects | A coding standard with a format check | Not met | No PHP-CS-Fixer or PHP_CodeSniffer check in `make ci` or CI |
| Continuous integration | The production build of every deployable part succeeds in CI, failing on unaccepted warnings | Not applicable | Nothing is deployed |
| Continuous integration | The gate names its stages: one command lists them, another re-runs one | Not met | No stage list or re-run command |
| Continuous integration | The local gate applies deterministic formatters, naming changed files, and CI checks format strictly | Not met | No formatter in the gate |
| Continuous integration | Repository conventions are enforced by tests that discover their subjects from the filesystem, and every project-written check is tested red, green and "could not judge" | Not met | No convention tests; the rules the standards say a check verifies (workflow concurrency, `closed` and draft handling, runner selection and naming, fixed runner images) hold in `ci.yml` but nothing checks them. `ci/check-coverage.php` and `ci/check-thresholds.php` have no tests proving their red, green and "could not judge" (exit 2) outcomes |
| Continuous integration | Dependencies and images are cached across jobs, keyed from the lock files | Not met | `vendor/` and `bin/` are cached by `composer.lock`; the image is rebuilt every run |
| Continuous integration | GitHub-hosted jobs name a fixed image matching the internal runners' release, verified by a check | Not assessed | `ubuntu-24.04` is fixed; the internal runners' Ubuntu release is not visible from the repository |
| Continuous integration | On persistent runners every job cleans the workspace before checkout | Not met | No clean step |
| Continuous integration | CI services publish ephemeral ports | Not applicable | CI starts no services |
| Deployment → Real production and staging | A new project is in real production within one sprint of starting | Not applicable | A library, not deployed |
| Deployment → At every level | Workflows are gated by a check that the deployment variables are present and non-empty | Not applicable | No deployment |
| Deployment → At every level | Non-production environments require authentication | Not applicable | No environments |
| Deployment → At every level | Non-production environments never deliver messages to real recipients | Not applicable | No environments |
| Deployment → At every level | A generated secret is created only on a definite absence | Not applicable | No deployment |
| Deployment → At every level | Development environment files are not packaged; production configuration comes from IaC and the secret store | Not applicable | No deployment |
| Deployment → At every level | No automatic deploy step depends on mutable user data | Not applicable | No deployment |
| Deployment → At every level | Each release works beside the previous one in both directions | Not applicable | No deployment |
| Backups → At every level | A hosted environment documents its parts, the cost of losing each, and how each is rebuilt | Not applicable | No hosted environment |
| Security → At every level | CI is gated by a vulnerability audit of every ecosystem | Not met | No `composer audit` in `make ci` or CI |
| Security → At every level | An audit that could not run never passes; exceptions are versioned, time-boxed and expire | Not met | No audit runs |
| Security → At every level | Automated remediation by an AI agent runs inside the deterministic constraints | Not applicable | No autofix |
| Security → CI supply chain | External actions are pinned by full commit SHA with the release in a comment, and a check rejects any other reference | Not met | Both actions are pinned by SHA; no check enforces it |
| Security → CI supply chain | A release build holding cloud credentials installs nothing outside the lockfiles | Not applicable | No release build |
| Security → CI supply chain | CI checks every manifest against its lockfile, and that each locked entry states its source, an integrity hash and the expected registry | Not met | `composer.lock` is committed, but nothing checks it; `aircury/code-manager` comes from a VCS repository, locked to branch `dev-dev-standards-phpstan` with no dist checksum |
| Security → CI supply chain | Install scripts and package-manager plugins run only for packages on a reviewed allow list | Not met | `composer.json` has no `allow-plugins` list and no check |
| Security → CI supply chain | Third-party tools and images are pinned exactly, with one version source covered by automated updates | Not met | Images are pinned by digest, PCOV by version (`pcov-1.0.12`) and the editorconfig checker by version and checksum, but no automated update covers them |
| Security → CI supply chain | One record lists the versions of the building blocks, where each is pinned, its source and when it was verified | Not met | No such record |
| Security → CI supply chain | CI fails a change that moves a package across a major version it did not ask for | Not met | No such check |
| Security → CI supply chain | Shared or third-party agent skills are pinned and never edited in the project | Not applicable | The project carries no agent skills |

Code behaviour, for a code review (26, not assessed in this run):

- Architecture (14): stateless services; operational vs programmer errors;
  optimistic concurrency with 409 on conflict; no silent catch blocks; safe
  user-facing messages; bounded retries with backoff; idle and total limits on
  outbound calls; one budget across chained calls; timeouts reported as
  unavailability; limits inside the enclosing ceiling; durable records in the
  mutation's transaction; transient effects after commit; after-commit hooks
  on the outermost transaction with deduplicated fan-out; narrowest realtime
  event and delta ordering.
- Authentication and sessions (8): server-side revocation; token lifetime and
  invalidation; refresh reuse detection and an explicit expiry decision;
  sliding expiry under an absolute ceiling; high-entropy secrets stored as
  hashes; credential cookie attributes; development shortcuts absent from
  published builds; complete Link-hub delegation.
- Testing (1): every new test proven by breaking the code it covers.
- Infrastructure and databases (3): bounded index keys; database-allocated
  sequence numbers; one query per sibling collection.
