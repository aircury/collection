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

After `composer install` (including development dependencies), run the full
PHPUnit suite using the committed `phpunit.xml.dist` configuration:

```sh
bin/phpunit
```

To check one test file or method while working:

```sh
bin/phpunit Tests/StringCollectionTest.php
bin/phpunit --filter testBasicUsage Tests/StringCollectionTest.php
```

Composer installs executables into `bin/`, not `vendor/bin/`. Check PHP syntax
for all tracked PHP files, or just a changed file:

```sh
git ls-files -z '*.php' | xargs -0 -r -n 1 php -l
php -l StringCollection.php
```

The syntax check is not static analysis. No project-level static-analysis
configuration or CI workflow is committed, so these commands do not establish
that a static-analysis or CI gate has passed.

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

<!-- development-standards:begin -->
## Development standards

Commitment: `R1 D1 C1 L1 S1 P1`

Not applicable:

- `I` — this is a PHP library and does not expose an HTTP API.
- `E` — this package does not provide browser-based end-to-end journeys.
- `Y` — this package is not deployed as a standalone service.
- `B` — the package does not retain any data of its own.
- `O` — this package does not run as a production service with operational observability.
- `U` — this package does not publish an availability commitment.
- `T` — this package does not publish a support SLA.
- `A` — this package does not include user-facing browser UI.

Documentation (D) assessed on 2026-10-20; other dimensions assessed on
2026-08-11 — see
[docs/development-standards-assessment.md](docs/development-standards-assessment.md).
<!-- development-standards:end -->
