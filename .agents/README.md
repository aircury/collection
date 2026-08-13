# Canonical IA layout

Product-agnostic agent files for this repository. AI product folders (`.cursor/`, `.claude/`, `.github/`, …) are adapters, not source of truth.

Read this file when initializing the layout, adding a skill, or wiring a developer’s AI product. Do not load it for ordinary `aircury/collection` work — use `AGENTS.md` instead.

| Path | Role |
|------|------|
| `AGENTS.md` | Entrypoint: how to work in this repo |
| `.agents/skills/` | Skills (`SKILL.md`, [Agent Skills](https://agentskills.io)) |

Never duplicate skill bodies into vendor folders; symlink or generate pointers. To wire a client (Cursor, Claude Code, Copilot, Codex, Gemini), follow `.agents/skills/adapt-agent-layout/`. To add or revise nested `AGENTS.md` files, follow `.agents/skills/nested-agentsmd-writer/`.

MCP inventories and non-MCP tool catalogs are out of scope until that work lands. Do not invent `.agents/mcp/` or `.agents/tools/` yet.

## Skills

- Canonical location: `.agents/skills/<name>/SKILL.md`
- Format: Agent Skills spec (`name` and `description` in YAML frontmatter)
- Do not add product-only fields (for example Cursor `paths` or `disable-model-invocation`) to canonical `SKILL.md`
