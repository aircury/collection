---
name: nested-agentsmd-writer
description: >-
  Design, create, review, and maintain a nested AGENTS.md architecture. Use when
  adding or improving the root AGENTS.md, nested module guides, Navigation
  rules, Dynamic routing, or links from AGENTS files to docs/. Prefer this skill
  for progressive context loading and compact local guides instead of long
  documentation.
---

# Nested AGENTS.md Writer

Create and maintain an `AGENTS.md` architecture that matches this repository’s root `AGENTS.md`.

Three layers:

1. Root `AGENTS.md` — always-on navigation + **Dynamic routing**
2. Nested `AGENTS.md` — local module guidance
3. `docs/` — deeper documentation (add when needed; this library may not have `docs/` yet)

Progressive loading:

- Start at the root `AGENTS.md`
- Walk **up** from current context to the root, reading every `AGENTS.md` on that path
- Match the task to **Dynamic routing** when the prompt or path needs it
- Follow **Then read** / nested sublinks only when the task needs them
- Do not read the whole repository by default

Canonical IA files live under `.agents/` (see `.agents/README.md`). Do not treat `.cursor/` or `.claude/` as source of truth.

## When to use this skill

Use when the user asks to:

- create or revise a root `AGENTS.md`
- create a nested `AGENTS.md` for a folder or module
- review or normalize the AGENTS hierarchy
- improve Navigation rules or Dynamic routing
- define how AGENTS and `docs/` work together

Do not use for long-form architecture docs, generic READMEs, runtime code changes, or an `AGENTS.md` in every folder.

## Core design principles

- Root `AGENTS.md` is a router, not full documentation.
- Nested files explain local context and impact.
- Deeper explanation belongs in `docs/` when that tree exists.
- Prefer fewer high-value nested files over many thin ones.
- Link instead of copying large explanations.
- Use compact Markdown tables.
- When adding or renaming an important component, update the root **Dynamic routing** row and the nearest nested `AGENTS.md`.

## When to create a nested `AGENTS.md`

Create one when at least one is true:

- the folder is a common entry point for changes
- the area has important dependencies or risks
- the area would otherwise require reading many files
- the area owns meaningful behaviour, contracts, or orchestration

Do not create one automatically for every folder. In this repo, most types live at the repository root; prefer `Tests/` (and `Exceptions/` only if it grows) over one `AGENTS.md` per PHP class.

## Root `AGENTS.md` requirements

The root must match this structure (see `templates/root-agents.md` and the live `AGENTS.md`):

1. Short overview (what the package is; keep APIs stable; primary consumer)
2. **IA layout** — point at `.agents/README.md`; skip for ordinary library work
3. **Navigation rules** — apply always (walk-up, prompt vs Dynamic routing, do not load the whole repo)
4. **Dynamic routing** — two columns only: **Task involves...** | **Then read**
5. **Global rules** — behaviour, docs freshness, prefer few nested files
6. **High-risk areas**
7. **Documentation** — index of `docs/` when that tree exists; omit the section until then
8. **Development** — install and test commands when they are not obvious from Composer/PHPUnit defaults

Do **not** add separate **Areas**, **Task routing**, **Main processes**, or **Layout** inventory sections. Folder maps belong in **Dynamic routing** or nested files.

### Navigation rules

Must tell the agent to:

- Start from current context (open files, task paths, directories about to be worked in)
- Walk **up** the directory tree to this root `AGENTS.md`
- Read every `AGENTS.md` on that path; do not skip levels
- Walk up again if context moves
- Follow nested **sublinks** only when the task needs them
- Also interpret the prompt against **Dynamic routing**
- Not read the whole repository by default

Copy the numbered Navigation rules from the live root `AGENTS.md` (or `templates/root-agents.md`). Do not invent a different walk-up procedure.

### Dynamic routing

This is the only task map on the root.

- **Task involves...** — real user tasks or symptoms (not a folder inventory)
- **Then read** — first path is usually a nested `AGENTS.md` (or a folder/source file if the nested file does not exist yet); further paths only if needed
- Merge overlapping area / process / task rows into one row
- Avoid vague rows like “code changes” or “docs changes”
- Reflect the real architecture
- Until a nested file exists, point at the folder and say `AGENTS.md` if present

Do not use **Read first** / **Extra context** or **Area** / **Start here** on the root.

## Nested `AGENTS.md` requirements

Keep them short. They should answer:

- what this area is responsible for
- which files matter most
- where to go for a given local task
- which nearby areas are connected
- local rules or gotchas
- which `docs/` files go deeper (if any)

Use **Dynamic routing** with **Task involves...** | **Then read** (same column names as the root). Do not add Areas / Main processes.

Existing nested files may still say **Task routing**; when revising a nested file, convert that table to **Dynamic routing** unless the user asks to leave it.

Nested files do not repeat the full Navigation rules; the root applies always. They are local guidance on the walk-up chain.

## Standard structures

Use these templates unless the user asks for a different format:

- Root: `templates/root-agents.md`
- Nested module: `templates/nested-agents.md`
- Small nested scope: `templates/nested-agents-minimal.md`
- Docs index: `templates/docs-readme.md`
- Deeper process: `templates/process-doc.md`

Prefer:

- root template for the repository entrypoint
- standard nested template for important modules
- minimal nested template only when full local Dynamic routing is not justified
- docs README for indexing `docs/`
- process doc when a nested or root **Then read** cell needs a deeper workflow

## Suggested layout (this repo)

```text
Collection/
├── AGENTS.md
├── .agents/
│   ├── README.md
│   └── skills/
│       ├── adapt-agent-layout/
│       └── nested-agentsmd-writer/
├── AbstractCollection.php
├── CollectionInterface.php
├── Exceptions/
├── Tests/
│   └── AGENTS.md
└── README.md
```

Most collection types live at repo root and are routed from **Dynamic routing**. Do not add an `AGENTS.md` per PHP class.

## Workflow

1. Inspect the repository shape and the live root `AGENTS.md`.
2. Identify common change entry points.
3. Decide which folders deserve nested AGENTS files.
4. Draft or update the root first (Navigation rules + Dynamic routing).
5. Draft nested AGENTS only for high-value scopes.
6. Push deeper explanation into `docs/` when that tree exists.
7. Keep links real, useful, and minimal.
8. Summarize missing docs, weak routing, or gaps.

## Quality bar

A good result should let an agent:

- start at the root
- walk up from context and load the nested files on that path
- pick a **Dynamic routing** row from the prompt when needed
- follow only necessary **Then read** links
- understand likely impact before changing code

If AGENTS files become long, repetitive, or too abstract, simplify them.

## Optional metadata

Do not require YAML frontmatter in `AGENTS.md` by default.

Only add minimal frontmatter if the user explicitly wants machine-readable metadata.

## Output contract

When generating or revising AGENTS files, return:

1. the Markdown content for each file requested
2. a short note listing assumptions, missing docs worth adding, and routing or scope gaps
