# SCVHistory.com Migration: Handoff Brief

Read this file and CHANGELOG.md at the start of every session. Multiple AI assistants (Grok, Claude) work on this project. These two files, plus CONTENT-MODEL.md, are the shared source of truth.

## The project

SCVHistory.com is a nearly 30-year-old Santa Clarita Valley history archive created by Leon Worden (journalist/historian). Nathan (Stark Social) is migrating its content into a new Craft CMS build. A nonprofit, Santa Clarita History Archives, is being formed to steward the site long-term, targeting a 2027 launch for the site's 30th anniversary.

## Priorities (in order)

1. **Content modeling in Craft CMS.** This is the most important work right now.
2. **Moving content from the local mirror into Craft.**
3. Everything else is parked (see bottom).

## Environment

- Two machines:
  - **iMac** (2020): has the Woodson drive attached. Content inventory and extraction run here. No DDEV installed.
  - **MacBook**: has the local Craft CMS build via DDEV. Content modeling and imports run here.
- Local mirror of the full legacy site: external 1TB ExFAT drive "Woodson", mounted at `/Volumes/Woodson` on the iMac (~729GB, includes TIFFs)
- Code: private GitHub repo under the Stark Social org. Use it to move files between machines
- Craft CMS: local via DDEV on the MacBook; production on Cloudways
- Craft version: [FILL IN]
- DDEV project path: [FILL IN]
- Current state: 33 Person entries imported. Import method: [FILL IN]
- Tools available: DDEV, Python, bash, curl, wget

## Known problem: the drive dismounts unexpectedly

Woodson sometimes dismounts mid-task. ExFAT has no journaling, so an interrupted write can corrupt files. Every script must account for this:

- Check the drive is mounted before doing anything, and stop cleanly if it is not
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

## Task 1: Content inventory (do this before modeling)

Goal: understand what is actually on the drive before designing anything.

Deliverable: `INVENTORY.md` containing
- File counts and total size by extension
- Top-level directory structure with a one-line description of what each directory holds
- Recurring page patterns: identify the distinct page "types" in the HTML (e.g., biography, place, photo page, article, index/list page), with 3 example paths for each
- Recurring metadata visible in pages: dates, photo IDs, credits, captions, sources, bylines
- Estimated count of pages per type

## Task 2: Propose the content model

Deliverable: `CONTENT-MODEL.md` for Nathan and Leon to review **before** anything is built. Leon will help with entity identification, so flag anything ambiguous as a question for him.

Starting hypotheses to test against the inventory (not settled):
- Entities as channel sections: People (exists), Places, Organizations, Events
- Content: Articles/Pages (the legacy narrative pages)
- Media: Photos and documents as Assets with metadata fields (Leon's photo/item ID, date, credit, source, caption)
- Classification: Topics and Eras, likely as a structure section so they can relate to everything
- Relations between entities via Entries fields (e.g., Article relates to People, Places, Events)

Required on every migrated entry:
- `legacyUrl`: original SCVHistory.com path, for 301 redirects and traceability
- `sourcePath`: path on the Woodson drive the content came from

For each section, specify: section type, entry type(s), fields (handle, type, required or not), relations, and which legacy page type maps to it.

## Task 3: Build the model

After Nathan approves CONTENT-MODEL.md, implement it in the local DDEV install so it lands in `config/project/` and can be deployed via project config. Do not change production directly.

## Task 4: Pilot import

Pick one content type (expanding People beyond the current 33 is the natural start). Build a repeatable import: extract from HTML to a clean JSON or CSV on the internal disk, then import into Craft. Verify a sample by hand with Nathan before scaling up.

## Task 5: Batch migration

Run the remaining types in batches, following the drive rules above. Log counts (processed, imported, failed) per batch in CHANGELOG.md.

## Parked (do not work on unless Nathan asks)

- **Archive.org upload** (identifier `scvhistory-com-archive`): paused. May be used later to host large files like TIFFs.
- **Cloudways server clone and Cloudflare DNS cutover**: Nathan handles directly.
- **~587 blocked TIFFs in `/gif/`**: returns 403 to automated tools. Requires Leon's cooperation; not a technical task.

## Lessons already learned

- TIFFs in `/scvhistory/files/` download with curl using browser headers; wget fails. See `download-tiffs.sh`
- The HTML on the drive has already been cleaned of ads, trackers, gtag, and Disqus code by a Python script (verified by diff)
