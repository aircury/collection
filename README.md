# Collection

PHP Arrays with additional functionality. Arrays on steroids.

## Installation

Requires PHP 8.3 or a compatible later PHP 8.x release, and Composer.

To use the library in an application, run from that application's root:

```sh
composer require aircury/collection
```

For local development, install Git and clone the repository:

```sh
git clone git@github.com:aircury/collection.git
cd collection
composer install
```

Run the remaining development commands from the repository root. Use
`composer install` again to restore the versions in `composer.lock`; do not
use `composer update` unless intentionally changing dependencies. Development
dependencies include `aircury/code-manager`, fetched from GitHub; if Composer
reports an authentication error, ensure your GitHub credentials have access to
that repository. Do not commit credentials.

## Start / quick start

This is a library, not a standalone application: there is no server to start,
database to initialise, or environment file to configure. After installation,
load Composer's autoloader in your application's PHP entry point:

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Aircury\Collection\StringCollection;

$strings = new StringCollection();
$strings[] = 'hello';
$strings[] = 'world';

echo $strings->implode(' '); // hello world
```

To try this locally, save it as `example.php` in the repository root and run
`php example.php`. The expected output is `hello world`. Remove the temporary
file afterwards.

## Usage

There are two ways of using this library. To create Collections of objects or Collections of PHP scalar types.

### Object Collections

To create you own Collection, the easiest way is to copy and adjust the sample `CarCollection`, replacing `Car` with
the class that you need.

More generically, these are the steps to create your own Collection:
  - Extend the `AbstractCollection` class
  - Implement on the child these methods, 
    - `getClass()` to provide information about the expected class or interface that all the objects on the collection
      should be.
    - `offsetGet($offset)` to specify the return class.
    - `toArray()` to provide PHPDoc information about the elements of the array.
    - `first()` to specify the return class.

### Scalar type Collections

This library comes out of the box with the following Collections, ready to use:
  - `StringCollection`
  - `StringOrNullCollection`
  - `IntegerCollection`
  - `IntegerOrNullCollection`

These classes include additional expected functionality that you would expect from the data type, e.g. `implode()`, 
`sum()`, ...

If they do not suit your needs, you can extend any of them or the `AbstractScalarTypeCollection` to implement your own. 

## Checks

The checks run inside the library's container, so they need only Docker and
Make on the host. `make` with no arguments lists every command.

Run the whole gate, exactly as CI runs it, before submitting:

```sh
make ci
```

It first runs, in parallel, the editorconfig check and `make thresholds`, which
fails if any minimum in `coverage-threshold.json` is lower than on
`origin/master` where your branch left it (fetch it first; set
`TARGET_BRANCH=...` for another base). It then installs the dependencies and
runs the tests with coverage and PHPStan in parallel. Every failure in a stage
is reported together. Nothing in it is CI-only.

While working, run each check over just the files you changed:

```sh
make test PATHS=Tests/StringCollectionTest.php
make phpstan PATHS="StringCollection.php Tests/StringCollectionTest.php"
make editorconfig PATHS="README.md Makefile"
```

Coverage and `make thresholds` take no `PATHS`: they judge the project as a
whole.

## Contributing

1. Create a branch for a focused change from the repository's default branch.
2. Follow the surrounding PHP style and namespace conventions. Add or update 
  tests in `Tests/` for changed behaviour; put test fixtures in `Tests/Fixtures/`.
3. Run the relevant tests and syntax checks while developing, then the full
  PHPUnit suite before submitting. Update this README when usage or setup changes.
4. Use English conventional commit messages prefixed by the work item's
  reference, for example `[ABC-123] fix(collection): handle empty collections`.
5. Open a pull request describing the problem, the change, and the checks you
  ran (including any failures or checks you could not run).

Report bugs and propose changes through
[GitHub issues](https://github.com/aircury/collection/issues), including a minimal
reproduction, your PHP version, and the expected and actual behaviour.

## AI delivery board

Work on Collection is queued and delivered by Overboards on a Boards board of
its own.

| | Production |
| --- | --- |
| Board | `https://boards.aircury.net/b/collection` |
| API base | `https://api.boards.aircury.net` |
| Token | The secret `COLLECTION_BOARD_TOKEN` in the Overboards service's secrets store |
| Integration branch | `staging` |

The active **Backlog** column on this board is the canonical backlog for
Collection. Repository backlog files must not be created or used as a parallel
queue. Before filing a finding, read the board digest to determine whether an
active card already covers it, then use the `overboards-add-card` skill to
enrich the covering card or create a missing card at the bottom of Backlog.

The token is board-scoped, issued from the board's **Manage tokens** with the
**Unattended contributor** shortcut, and entered once by one of the board's
administrators on the Overboards Agents page, under Secrets: never in this
repository, a shell environment or an environment file.

Completed cards land on the `staging` integration branch; the first pipeline
run creates it from `master`. The release pull request from `staging` into
`master` is where a person reviews.

## What a stage reads from this project

| What a stage asks for | This project's answer |
| --- | --- |
| The governing standards | [Development standards](#development-standards) in this README, assessed in [docs/development-standards-assessment.md](docs/development-standards-assessment.md) |
| The full verification gate | `make ci` from the repository root: builds the library image, installs dependencies, then runs PHPStan, the editor-configuration check and PHPUnit with the coverage threshold in `coverage-threshold.json` |
| Targeted tests | `make test PATHS="Tests/<File>Test.php"` for named test files, `make phpstan`, `make editorconfig` and `make coverage`; run `make init` once first |
| The browser harness for a screen item | None: this is a library with no UI |
| Where a systemic lesson lands | This README's [Contributing](#contributing) section |
| The canonical behaviour record | None beyond this README's [Usage](#usage) section and the PHPUnit suite in `Tests/` |
| Application stack | None: `make up` starts no service |

<!-- development-standards:begin -->
## Development standards

Project type: internal tool in production (a library other Aircury products
depend on).

Commitment: `R3 D1 C2 L2 S2 P1`

Not applicable:

- `I` — this is a PHP library and does not expose an HTTP API.
- `E` — this package does not provide browser-based end-to-end journeys.
- `Y` — this package is not deployed as a standalone service.
- `B` — the package does not retain any data of its own.
- `O` — this package does not run as a production service with operational observability.
- `U` — this package does not publish an availability commitment.
- `T` — this package does not publish a support SLA.
- `A` — this package does not include user-facing browser UI.

Assessed on 2026-11-27 — see
[docs/development-standards-assessment.md](docs/development-standards-assessment.md).
<!-- development-standards:end -->
