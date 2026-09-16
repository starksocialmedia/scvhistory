# AGENTS.md: SCVHistory Craft CMS

Before doing anything, read these in full:
1. `PHILOSOPHY.md`: goals, principles, and design philosophy. It overrides your own assumptions
2. `HANDOFF.md`: current tasks, priorities, settled decisions
3. `BUILDPLAN.md` and `DATA_MODEL.md`: roadmap and full data model
4. `CHANGELOG.md` and `ERRORLOG.md`: history, decisions, known errors
5. `CONTENT-MODEL.md` and `INVENTORY.md`: existing work to extend, not replace
6. Any section-specific doc relevant to the task: `COLLECTIONS-HUB.md`, `PLACES-HUB.md`, `PLACE-RELATIONS.md`, `WAR-MEMORIAL.md`, `TAXONOMY_STANDARDS.md`, `craft-cp-field-checklist-2026-04-15.md`

All legacy SCVHistory.com content is moving into this Craft build. Craft becomes the new site.

Several AI agents work on this repo (Grok Build, Grok Bot, Claude). These files are the shared memory. Do not rely on anything not written here.

## Roles

- **Grok Build** (runs locally on Nathan's MacBook in `~/scvhistory`): content inventory, modeling, Craft schema work in DDEV, import scripts. Owns Tasks 1 through 5 in HANDOFF.md. Source content is at `/Volumes/Jordy/SCVHistory` (read-only; the drive can dismount, see HANDOFF.md).
- **Grok Bot** (cloud computer): research only. Taxonomy URI verification (Wikidata, AAT, LCSH), entity research, draft entity lists for Leon Worden to review. Cannot access the Jordy drive or local DDEV. The legacy content lives on Jordy, not the live site: do not crawl scvhistory.com.
- **Grok chat and Claude**: review and advice. Changes come back through Nathan.

## Git rules

- Grok Build works on branch `grok-build`. Grok Bot works on branch `grok-bot`. Never commit directly to `main`
- Pull before starting. Commit small, with clear messages
- Nathan merges to `main`. Ask before any `git push`
- **Pushing to `main` auto-deploys to production** via the workflow in `.github/workflows/` (GitHub Actions to Cloudways). Never push to `main`, and never edit the workflow
- Never rewrite history (no force push, no rebase of shared branches)

## Safety

- Use plan mode for anything beyond reading files. Wait for Nathan's approval of the plan
- Allowed without asking: reading files, `ddev describe`, `ddev craft` read-only commands, writing new files in the repo
- Ask first: anything that changes the database, `ddev craft project-config/apply`, installing packages, deleting files, `git push`
- Never touch production (Cloudways), DNS (Cloudflare), or Archive.org
- Never ask for, store, or print credentials. `.env` stays out of commits
- Docker Desktop must be running before any `ddev` command. If DDEV fails with a Docker socket error, tell Nathan to open Docker Desktop

## Content rules

- Follow PHILOSOPHY.md and the settled decisions in HANDOFF.md, BUILDPLAN.md, and DATA_MODEL.md. Do not redesign what is already decided
- Full records only for entities with a meaningful, recurring role in SCV history. Passing mentions stay in body text
- Never rewrite Leon Worden's prose during migration. Preserve legacy URLs, photo IDs, and bylines
- Dates keep their real precision; claims keep a confidence level
- Do not invent visual design. Follow the design system in PHILOSOPHY.md
- Do not build later-phase features until the content model and migration are done
- Entity-first: canonical entities before any articles
- Follow `TATAVIAM_AUDIT.md` for anything touching Indigenous peoples
- Taxonomy terms need verified linked-data URIs. Never invent a URI; mark it `NEEDS_VERIFICATION` instead
- When unsure whether something is a real historical entity or how to classify it, add it to a "Questions for Leon" list in CONTENT-MODEL.md rather than guessing

## Communicating with Nathan

- One clear action at a time. Recommend one path, not a menu
- Label any command Nathan must run: **iMac**, **MacBook**, or **Server**
- Shell blocks with no `#` comment lines (they break zsh)
- No em dashes in written deliverables

## End of every session

Add a dated entry to `CHANGELOG.md` with: agent name, what was done, decisions made, blockers, next steps. Log any errors and fixes in `ERRORLOG.md`. Propose (do not make) BUILDPLAN.md checkbox updates. Commit.
