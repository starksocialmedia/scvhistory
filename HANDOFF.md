# SCVHistory.com Migration: Handoff Brief

Read `PHILOSOPHY.md` first, then this file, `CHANGELOG.md`, `BUILDPLAN.md`, `DATA_MODEL.md`, and `ERRORLOG.md` at the start of every session. Multiple AI assistants (Grok, Claude) work on this project. These files are the shared source of truth. PHILOSOPHY.md explains why; BUILDPLAN.md and DATA_MODEL.md hold the roadmap and the full data model. HANDOFF.md only lists the current agent tasks.

## The project

SCVHistory.com is a nearly 30-year-old Santa Clarita Valley history archive created by Leon Worden (journalist/historian). Nathan (Stark Social) is moving **all** of its content into a new Craft CMS build. Craft becomes the new SCVHistory.com. The local copy of the legacy site is source material only. A nonprofit, Santa Clarita History Archives, is being formed to steward the site long-term, targeting a 2027 launch for the site's 30th anniversary.

## Priorities (in order)

1. **Content modeling in Craft CMS.** This is the most important work right now.
2. **Moving content from the local mirror into Craft.**
3. Everything else is parked (see bottom).

## Environment

- **MacBook** is the working machine: local Craft build via DDEV, and the source drive is attached here. Inventory, modeling, extraction, and imports all run on the MacBook.
- **Source content:** external drive "Jordy", legacy site at `/Volumes/Jordy/SCVHistory` on the MacBook
- **iMac** (2020): also holds a full mirror on the "Woodson" drive (~729GB, includes TIFFs). Not used for this work; no DDEV installed
- Code: private GitHub repo under the Stark Social org. Use it to move files between machines
- Craft CMS: local via DDEV on the MacBook; production on Cloudways (recently moved to a new server; deploys are being reworked, so do not touch deployment)
- **Workflow rule:** code and schema go local → git → Cloudways. Content lives on Cloudways, not local. Agents build and test import scripts locally; Nathan runs real imports on Cloudways
- Craft version: 5 (composer constraint `^5.9.20`)
- DDEV project path: `~/scvhistory` on the MacBook (Docker must be running for DDEV)
- Repo: `git@github.com:starksocialmedia/scvhistory.git`
- Current state: modeling is well underway, not starting from scratch. 33 Person entries imported. See CHANGELOG.md for full history
- Existing work in the repo (read before proposing anything):
  - `taxonomy-import/`: 11 taxonomy JSON files with verified Wikidata, AAT, and LCSH URIs
  - `TATAVIAM_AUDIT.md`: Indigenous cultural audit covering Tataviam, Chumash, Tongva, Serrano, Kitanemuk, and Vanyume. Follow it for any content touching these peoples
  - `templates/_partials/jsonld/person.twig`: Person JSON-LD partial
  - `config/project/`: the current Craft schema
  - `DATA_MODEL.md`: the documented data model, including open questions
  - `TAXONOMY_STANDARDS.md` and `craft-cp-field-checklist-*.md` if present
  - Content status: 33 People (local and Cloudways), 14 Organizations (Cloudways only)
- Tools available: DDEV, Python, bash, curl, wget

## Known problem: the drive dismounts unexpectedly

The source drive sometimes dismounts mid-task. An interrupted write can corrupt files. Every script must account for this:

- Check `/Volumes/Jordy/SCVHistory` exists before doing anything, and stop cleanly if it is not
- Treat the drive as **read-only source**. Never write output back to it
- Write outputs to the iMac's internal disk
- Make scripts resumable: keep a manifest or checkpoint file of what has been processed, and skip completed items on rerun
- Process in batches, not one giant run
- For heavy work, copy a working subset to the internal disk with `rsync` first and process that copy

## How Nathan works (follow these)

- One clear action at a time
- Label every command **iMac**, **MacBook**, or **Server** (and note when it runs inside DDEV)
- Shell commands as single clean copy-paste blocks
- **No `#` comment lines inside shell blocks** (they trigger zsh `quote>` prompts). Put explanations outside the block
- Direct answers, no conflicting alternatives. Recommend one path
- Never ask for or handle credentials (Cloudways, Cloudflare, Archive.org). Write the command; Nathan runs it
- **End every session with a dated CHANGELOG.md entry**: what was done, decisions made, blockers, next steps

## Settled decisions (do not relitigate)

- Roles Matrix field replaces plain-text occupation on Person
- Military Profile is merged into Person as a conditional field group
- Import pipeline is entity-first: never import articles before canonical entities exist
- Taxonomies use verified linked-data URIs (Wikidata, AAT, LCSH)

Check CHANGELOG.md for any later decisions before acting.

## Task 1: Content inventory (do this before modeling)

`INVENTORY.md` already exists. Read it first and extend or correct it; do not start over.

Goal: understand what is actually in `/Volumes/Jordy/SCVHistory` before extending the model. Read-only; no bulk copying.

Follow Stage 1 of the import pipeline in PHILOSOPHY.md and BUILDPLAN.md. Deliverables: `inventory.json` (every file catalogued, produced by a resumable script such as `scan_inventory.py`) and a human-readable `INVENTORY.md` containing
- File counts and total size by extension
- Top-level directory structure with a one-line description of what each directory holds
- Recurring page patterns: identify the distinct page "types" in the HTML (e.g., biography, place, photo page, article, index/list page), with 3 example paths for each
- Recurring metadata visible in pages: dates, photo IDs, credits, captions, sources, bylines
- Estimated count of pages per type

## Task 2: Audit and extend the content model

`CONTENT-MODEL.md` already exists. Read it first and build on it; do not start over. Also check `COLLECTIONS-HUB.md`, `PLACES-HUB.md`, `PLACE-RELATIONS.md`, and `WAR-MEMORIAL.md` for decisions already made.

`DATA_MODEL.md` already documents the intended model. Deliverable: `CONTENT-MODEL.md` comparing DATA_MODEL.md against what actually exists in `config/project/` (what is built, what is missing, what differs), then proposing what is missing, for Nathan and Leon to review **before** anything is built. Leon will help with entity identification, so flag anything ambiguous as a question for him.

Steps:
1. Compare `config/project/` against DATA_MODEL.md and the settled decisions above
2. Map every page type from INVENTORY.md to an existing section, or mark it as a gap
3. Propose additions only for the gaps, and only for entities that meet the recurring-role threshold in PHILOSOPHY.md. Likely candidates to check: Places, Organizations, Events, Articles/Pages, Photos and documents as Assets (Leon's item ID, date, credit, source, caption), Topics and Eras

Required on every migrated entry (confirm whether these already exist):
- `legacyUrl`: original SCVHistory.com path, for 301 redirects and traceability
- `sourcePath`: path under `/Volumes/Jordy/SCVHistory` the content came from

For each new or changed section, specify: section type, entry type(s), fields (handle, type, required or not), relations, and which legacy page type maps to it.

## Task 3: Build the model

After Nathan approves CONTENT-MODEL.md, implement it in the local DDEV install so it lands in `config/project/`. Nathan applies it to Cloudways with `project-config/apply`. Do not change production directly.

## Task 4: Pilot import

Follow Stages 2 to 4 of the import pipeline in PHILOSOPHY.md. Entities first: canonical entities (People, Places, Organizations, taxonomies) before any articles. Expanding People beyond the current 33 is the natural start.

Build a repeatable process: extract from HTML to the JSON artifacts on the internal disk, flag duplicates for human review, then import. Test the import script against local DDEV with a small sample. Nathan reviews the sample by hand, then runs the real import on Cloudways.

## Task 5: Batch migration

Nathan runs the remaining types on Cloudways in batches, using the scripts and artifacts you prepare. Follow the drive rules above. Log counts (processed, imported, failed) per batch in CHANGELOG.md.

## Parked (do not work on unless Nathan asks)

- **Archive.org upload** (identifier `scvhistory-com-archive`): paused. May be used later to host large files like TIFFs.
- **Cloudways server clone and Cloudflare DNS cutover**: Nathan handles directly.
- **~587 blocked TIFFs in `/gif/`**: returns 403 to automated tools. Requires Leon's cooperation; not a technical task.

## Lessons already learned

- TIFFs in `/scvhistory/files/` download with curl using browser headers; wget fails. See `download-tiffs.sh`
- The HTML in the mirror has already been cleaned of ads, trackers, gtag, and Disqus code by a Python script (verified by diff)
