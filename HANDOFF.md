# Handoff — morning of 2026-09-18

Supersedes the 2026-09-17 handoff. Read this first.

## Start here

1. `git pull` on templates-batch-9. It is the only working branch. Do not push main
   until deploy.yml is fixed; see TODO.md.
2. `ddev craft exec "eval(file_get_contents('scripts/import/audit_records.php'))"`
   shows what is missing across every section, measured against RECORD-CHECKLIST.md.
3. Skills live in .claude/skills/ for import scripts, record templates and review
   screens. Point agents at them rather than restating conventions.

## Done overnight

- 325 legacy images imported, 113.9 MB, no failures. 370 relations across 60 records,
  49 placement tokens in 17 article bodies. War memorial images are related-only.
- Legacy chrome stripped from 38 bodies: breadcrumbs, repeated headlines, bracket
  navigation, drop caps rejoined, trailing gallery captions removed. Footnote markers
  no longer break into stray paragraphs; that affected 30 records, one with 122.
- Provenance backfilled: 60 records gained sourcePath, legacyKey and legacyUrl.
- Perkins articles attributed. Introduction and Editor's Notes corrected to Leon Worden
  with Perkins as subject.
- Article bands inherit their collection's band image. Text column capped at 52% so
  titles never run under the artwork.
- Reading player: Google US English, no picker, smooth progress bar.
- Every $APPLY flag in scripts/import/ reset to false, and every script now prints a
  warning on its first line when the flag is on.

## Today, in order

### 1. Reynolds import — DO NOT APPLY YET
`scripts/import/import_reynolds.php` dry-runs 24 updates against 23 existing records,
and its title fallback matched a Perkins article to a Reynolds page. Scope the title
match to the target collection, re-run, confirm 23 updates and 57 creations, then
apply. Afterwards re-run import_legacy_images.php to pick up the 28 Reynolds pages
whose images were skipped. Both are idempotent.

### 2. Collection parts for the full series
The four parts on the Reynolds collection describe only the 23 chapters we hold. Once
80 land, Part Four swallows everything from Chapter 22 onward. Add a fifth row
starting at position 24.

### 3. Relation adjudication
The extraction files hold 1,111 people, 78 places and 110 organizations with mention
counts, page lists and needs_review flags. Nothing is linked: 45 articles have no
place, 26 have no person. Build the export, review screen and apply script on the
pattern of the dates and entities screens in web/review/. This is what makes the
archive a graph rather than a pile of pages.

## Grok's output, ready to use

- inventory/legacy/perkins.json — 13 series pages, 7 related
- inventory/legacy/reynolds-full.json — 80 pages, the whole 71-chapter series
- inventory/legacy/warmemorial.json — 54 casualties, imported
- inventory/legacy/sitemap.json — 5,000 pages, capped, mostly /scvhistory/
- inventory/legacy/sitemap-2.json — 845 pages, the other trees
- has_legacy_page re-derived against both sitemaps. 127 entities flagged, namesake
  facilities demoted, split given names and honorific variants tagged for review.

The real site is at least 9,500 URLs. Roughly 4,500 were still queued at the cap.

## Known gaps

Research, not import: 20 articles with no publish date (Reynolds chapters carry no
printed dateline, confirmed from the pages), 19 war memorial narratives, 12 places
with no establishment date, 15 casualties with no portrait.

Correct as they are: 52 records with no provenance are born-digital — expeditions,
families, missions and historical figures created to hold relationships. Amend
RECORD-CHECKLIST.md to say legacyKey is required only for migrated records.

Rudy Acosta's age at loss reads 20 on Leon's page; born May 2 1991, died March 19
2011, so he was 19. Worth telling Leon rather than changing silently.

## Waiting on Nathan

- 6 community coordinates: fair-oaks-ranch, haskell-canyon, mint-canyon,
  potrero-canyon, ravenna, towsley-canyon
- 2 bios still one paragraph: jerry-reynolds, dante-acosta
- 8 site page bodies; About and Permissions matter most
- 35 community write-ups
- 9 Find A Grave links
- Confirm the LA County GIS licence and add attribution before launch
- Story of Our Valley band has pseudo-text on the map; replacement requested from CD
- Perkins collection order: the Introduction should precede The Birth of Newhall

## Rules learned the hard way

- One writer to the database at a time. Readers parallelise freely.
- While Claude Code runs: no template edits, no branch switching, never `git add -A`.
- `entry.someHandle is defined` is not safe on an Element. Four site outages. Check
  the field layout instead. See AGENTS.md.
- Never move relations with raw SQL. Craft stores them twice and reads the JSON.
- Clear storage/runtime/compiled_templates before trusting any before-and-after.
- An inline `ddev craft exec` containing `!` is mangled by zsh. Write it to a file.
- No generative restoration on archival images. Upscaling and tonal correction yes;
  anything that synthesises detail is falsification.
- Artwork gets the layered band. Photographs get the portrait frame.

## The aim

If the database can be rebuilt from inventory/ plus scripts/import/, then the database
is a cache and the repository is the archive. Keep it that way.
