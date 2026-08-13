---
name: adapt-agent-layout
description: >-
  Maps this repo’s canonical agent layout (AGENTS.md, .agents/skills/) onto a
  developer’s AI client. Use when setting up Cursor, Claude Code, Copilot,
  Codex, or Gemini, wiring skills into a product folder, or adapting agent
  files from the canonical layout.
---

# Adapt agent layout

Apply the canonical layout in `.agents/README.md` to one AI client. Do not copy skill bodies. Do not create MCP or tools catalogs.

Canonical:

- `AGENTS.md` — entrypoint
- `.agents/README.md` — layout contract
- `.agents/skills/<name>/` — skills (Agent Skills spec)

## Workflow

1. Read `AGENTS.md` and `.agents/README.md`.
2. List directories under `.agents/skills/` that contain `SKILL.md`.
3. Detect the client (`cursor`, `claude-code`, `copilot`, `codex`, `gemini`) from the user or from existing product folders. If unclear, ask.
4. Read [references/products.md](references/products.md) for that client.
5. **Instructions:** write a **thin** product file that points at `AGENTS.md`. Do not duplicate AGENTS.md or `.agents/README.md`.
6. **Skills:** for each canonical skill, expose it with a **symlink** of the skill directory into the product skills path. Copy files only if the environment cannot use symlinks, and say so.
7. Do not overwrite extra product-only files the user already has unless they asked.
8. Skip MCP config and tool catalogs. If asked, say they are deferred.
9. Report created or updated paths and any manual enablement.

## Rules

- Canonical `SKILL.md` stays portable: `name` and `description` in frontmatter, plus the body. No Cursor-only or Claude-only fields in `.agents/skills/`.
- Product overlays (rules, hooks) belong only in the product tree, if needed.
- Prefer relative symlinks from the product skills dir to `.agents/skills/<name>` (adjust `../` count to the real product path).
- Do not create `.agents/mcp/` or `.agents/tools/`.
