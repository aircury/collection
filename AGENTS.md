# AGENTS.md — Aircury Collection

PHP package `aircury/collection`: collection structures to use instead of arrays (`Aircury\Collection\`). Keep public APIs stable. Consumers include `aircury/db-model`, `aircury/dbal`, and Connector.

## IA layout

When initializing agent files, adding a skill, or adapting to an AI product, read `.agents/README.md`. Skip it for ordinary library work.

## Navigation rules

Apply always.

1. Start from the current context (open files, task paths, directories you are about to work in). Walk **up** the directory tree from each of those locations to this root `AGENTS.md`. At every directory on that path, if an `AGENTS.md` exists, read and include it. Do not skip levels.
2. If context moves to another path, walk up again from the new location to this root. The nearest nested `AGENTS.md` is local guidance; this file is the top of the chain.
3. Depending on the task, follow **sublinks** inside those nested files (key files, connected areas, tests, `docs/`) for more context. Do not load every linked document by default — only links that match the task.
4. Also interpret from the prompt whether the task involves any data specified in **Dynamic routing** below.
5. Do not read the whole repository by default.

## Dynamic routing

Match the task to a row. Open the first path in **Then read** (usually a nested `AGENTS.md` when that file exists). Follow the rest only if the task needs them.

| Task involves... | Then read |
|------------------|-----------|
| Object collections, `AbstractCollection`, or `CollectionInterface` | `AbstractCollection.php`, `CollectionInterface.php` — then `README.md`, `Tests/` |
| Scalar collections (`StringCollection`, `IntegerCollection`, null variants) | `AbstractScalarTypeCollection.php` and the concrete class — then `AbstractStringCollection.php` / `AbstractIntegerCollection.php` as needed |
| Comparable collections or comparison traits | `AbstractComparableCollection.php`, `ComparableInterface.php`, `ComparableTrait.php` |
| Collection diffs | `CollectionDiff.php` — then matching tests under `Tests/` |
| Graphs, directed graphs, or strongly connected components | `DirectedGraph.php`, `TarjanStronglyConnectedComponents.php` |
| Library exceptions | `Exceptions/` |
| Tests or PHPUnit for this library | `Tests/AGENTS.md` if present, else `Tests/` — then `phpunit.xml.dist` (`./bin/phpunit -c phpunit.xml.dist`) |
| Composer, PHP version, or package metadata | `composer.json` — then `composer.lock`, `code-options.yaml`; `../DBModel` and `../DBAL` when stating consumer impact |
| Planning or extending the nested AGENTS.md architecture | This file — then `.agents/skills/nested-agentsmd-writer/`, then the nearest nested `AGENTS.md` being created or updated |

Until nested `AGENTS.md` files exist, use the file and folder paths in **Then read**. Prefer adding a nested guide for `Tests/` before other folders. Most types live at repo root; do not add an `AGENTS.md` per PHP class.

## Global rules

- Check `AbstractCollection.php` and `CollectionInterface.php` before changing collection behaviour.
- State expected DBModel / DBAL / Connector impact when changing public APIs. Add tests in `Tests/`.
- PHP **8.3**. PSR-12. PSR-4: `Aircury\Collection\` → repository root.
- Documentation, comments, commit messages, and PR text: **English**. Commit only when the user explicitly asks.
- When adding or renaming an important component, update this router and its nearest `AGENTS.md`.
- When adding documentation, create `docs/README.md` and link it from this file (human usage today is `README.md`).
- When changing a documented flow or risk, update the component documentation and its local gotcha where it affects **Dynamic routing**.
- Prefer a few high-value nested `AGENTS.md` files over one file per small folder.

## High-risk areas

- `AbstractCollection.php` / `CollectionInterface.php`: base contract for object collections.
- `AbstractScalarTypeCollection.php`: shared behaviour for scalar collections.
- `CollectionDiff.php`: incorrect diffs affect callers that sync collections.
- Graph helpers (`DirectedGraph.php`, `TarjanStronglyConnectedComponents.php`): specialised algorithms; keep them isolated unless the task needs them.

## Development

- `composer install` then `./bin/phpunit -c phpunit.xml.dist`
- In the Aircury Docker `app` container, run PHPUnit as **www-data** with PHP **8.3**
