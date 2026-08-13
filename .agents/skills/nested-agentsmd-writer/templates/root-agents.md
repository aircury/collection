# AGENTS.md — <Project Name>

Short explanation of what this package does. Keep public APIs stable. Primary consumer: …

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

Match the task to a row. Open the first path in **Then read** (usually a nested `AGENTS.md`). Follow the rest only if the task needs them.

| Task involves... | Then read |
|------------------|-----------|
| … | `…/AGENTS.md` — then `docs/…` |
| … | `SomeRootFile.php` — then `…/AGENTS.md`, `docs/…` |

Do not add **Areas**, **Task routing**, or **Main processes**.

## Global rules

- Check connected modules before changing behaviour.
- Do not copy local `.env` credential-like values into issues, docs, commits, or agent responses.
- When adding or renaming an important component, update this router and its nearest `AGENTS.md`.
- When adding or moving documentation, update `docs/README.md` and the related `AGENTS.md` links.
- When changing a documented flow or risk, update the component documentation and its local gotcha where it affects **Dynamic routing**.
- Prefer a few high-value nested `AGENTS.md` files over one file per small folder.

## High-risk areas

- …
- …

## Documentation

| Topic | Location |
|-------|----------|
| Documentation index | `docs/README.md` |
| … | `docs/…` |
