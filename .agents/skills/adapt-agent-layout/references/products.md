# Product adapters (skills and instructions)

MCP and tool catalogs are out of scope. Paths are relative to the repository root unless noted.

| Client | Skills adapter | Instructions adapter |
|--------|----------------|----------------------|
| Cursor | `.cursor/skills/<name>` → `../../.agents/skills/<name>` | Optional `.cursor/rules/ia-layout.mdc` (`alwaysApply: true`) telling the agent to follow `AGENTS.md` |
| Claude Code | `.claude/skills/<name>` → `../../.agents/skills/<name>` | `CLAUDE.md` containing only `@AGENTS.md` |
| Copilot / VS Code | `.github/skills/<name>` → `../../.agents/skills/<name>` | `.github/copilot-instructions.md` pointing at `AGENTS.md` |
| Codex | Often reads `.agents/skills/` natively; if not, `.codex/skills/<name>` symlink | `AGENTS.md` only |
| Gemini | Often reads `.agents/skills/` natively; else `.gemini/skills/<name>` symlink | `GEMINI.md` pointing at `AGENTS.md` |

User-level paths (`~/.cursor/skills/`, `~/.claude/skills/`, `~/.agents/skills/`) are personal. Default to **project** adapters. Touch home-directory paths only if the user asks.

## Symlink example (Cursor)

From repo root:

```bash
mkdir -p .cursor/skills
ln -sfn ../../.agents/skills/adapt-agent-layout .cursor/skills/adapt-agent-layout
```

Repeat for every directory under `.agents/skills/` that contains `SKILL.md`.

## Thin instruction files

**CLAUDE.md**

```markdown
@AGENTS.md
```

**`.github/copilot-instructions.md`**

```markdown
Follow AGENTS.md at the repository root. Skills live in `.agents/skills/`.
```

**Cursor rule** (only if the user wants always-on Cursor rules): YAML frontmatter with `alwaysApply: true`; body: follow `AGENTS.md`; skills are under `.agents/skills/`.
