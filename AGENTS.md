# AGENTS.md: SCVHistory Craft CMS

Before doing anything, read these in full:
1. `HANDOFF.md`: project brief, priorities, settled decisions, tasks
2. `CHANGELOG.md`: history and latest decisions
3. `CONTENT-MODEL.md` and `INVENTORY.md` if they exist

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

- Follow the "Settled decisions" in HANDOFF.md. Do not redesign what is already decided
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

Append a dated entry to `CHANGELOG.md` with: agent name, what was done, decisions made, blockers, next steps. Commit it.
