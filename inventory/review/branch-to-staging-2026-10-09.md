# templates-batch-9 to staging: what is left (9 October 2026, evening)

Claude, for Nathan (brief item 16). This updates inventory/review/overnight-2026-10-08/branch-to-staging-2026-10-08.md. Read only: nothing written to the database, committed, pushed or run against staging.

## What was read, before any number

- **The 8 October list:** branch-to-staging-2026-10-08.md, written 23:42 on 8 October.
- **The repo:**
  - `git log --since=2026-10-08`, `main..HEAD`, `origin/templates-batch-9..HEAD`;
  - `git status` and `git diff` of the tracked files;
  - `git diff main...HEAD` for composer.
- **The documents:** CHANGELOG's two 9 October entries, TODO "Current (9 October 2026, afternoon)", HANDOFF, docs/DEPLOY-RUNBOOK.md step 7 and section 10, docs/DEPLOY.md's rsync step.
- **The fix itself:**
  - config/withheld-media.json and .txt (built 15:24);
  - config/custom.php;
  - templates/media/_entry.twig;
  - templates/_partials/head/schema.twig and the /media template's ORIGINAL and EDITED COPY rows.
- **Local DDEV, read only:**
  - `ddev craft project-config/diff`;
  - GET of five local pages;
  - the relation and text search in withheld-links-2026-10-09.md;
  - templates/_data/calendar.json against the live relations.
- **Not read or run:** staging; check_render and predeploy.sh (another process was rebuilding the withheld list in the container).

## What changed since 8 October, night

- **Two commits:**
  - a0e6855 (8 October, 23:58): Boston's portrait and the overnight write-ups;
  - 71c8853 (9 October, 08:42): the credential scan and Cooper's generated portrait off his record.
- **Neither touches templates/, config/ or web/.** The branch is 147 commits ahead of main (145 on 8 October); main is still its ancestor. Nothing is unpushed.
- **Everything that serves and changed today is uncommitted:**
  - modified: templates/media/_entry.twig, config/custom.php, scripts/import/check_render.php;
  - untracked: config/withheld-media.json, config/withheld-media.txt, scripts/import/_generated_scan.php, build_withheld_media.php, check_generated_files.php.
  - CHANGELOG, HANDOFF, TODO, ERRORLOG and the DEPLOY docs are modified too.
- **No composer change. No new environment variable.** project-config/diff: "No pending project config YAML changes."
- **Database changes since the 8 October list:**
  - Cooper's generated image off;
  - Couts off in the morning, back as an enhanced pair at 15:59;
  - the outside-file declarations in rightsNote;
  - the Boston obituaries #38529 and #38531;
  - more today, per CHANGELOG.

## Blockers from 8 October

### 1. The /media exposure: fixed in the working tree, not yet deployable

What is in place:
- templates/media/_entry.twig answers 404 for any id in `craft.app.config.custom.withheldMedia`;
- config/custom.php reads that list from config/withheld-media.json;
- docs/DEPLOY-RUNBOOK.md step 7 and docs/DEPLOY.md add `--exclude-from=config/withheld-media.txt` to the rsync;
- check_render runs check_generated_files.php, which fails when the list is out of date.

Locally, /media/27387 and /media/29698 answered 404 when fetched.

What still stands between the fix and staging:
- **It must be committed, all of it together.** Staging pulls templates-batch-9 from git.
  - If config/withheld-media.json is not committed, custom.php on the server reads nothing and no /media page 404s.
  - If check_render.php is committed without check_generated_files.php and _generated_scan.php, check_render breaks.
  - The .txt is used on the MacBook, but belongs with the .json.
- **The list was out of date at the moment of reading:**
  - Couts's #27387 is in the 15:24 list but has been his portrait on #323 since 15:59.
  - Until it is rebuilt, /media/27387 answers 404 and /media/12 links "EDITED COPY" to that 404.
  - The rsync would leave his portrait file out, so on staging his page would show a broken image.
  - A rebuild was running at 16:00; check_generated_files.php fails while the list is stale.
- **templates/_data/calendar.json (committed, built 8 October 12:30) still embeds a withheld file.** That is Jerry Gladbach's old portrait, outside/jerry-gladbach-portrait.jpg (#31417), on his "Died, July 13, 2022" row.
  - It also embeds #1757 (perkins_ab_...), which is on no record.
  - On staging that image would be broken, because the rsync leaves the file out.
  - A calendar rebuild (build_calendar_index.php), committed with the rest, clears it.
- **Files already on staging stay** (the rsync never deletes): Wiley's #1658 and Vasquez's upscale #28816, by TODO's account. Their /media pages will 404 after the pull, but a direct file URL will still serve. Removing them by hand on the server is Nathan's.

### 2. check_render and predeploy.sh must pass at the moment of the refresh: still open

- check_render now includes check_generated_files.php. It has not been run in this session.
- predeploy.sh as a whole (check_rendered_bodies.php over every record) has not been run since 8 October, by any account read.

### 3. The /review/ exposure check on staging (runbook section 10, step 3): still open

This is Nathan's to run with the basic-auth credentials.

### 4. The dump must be taken with nothing writing: still open

- A withheld-list rebuild was running in the container while this was read, and today's writers (Couts, outside declarations, Boston) have their CHANGELOG entries in the working tree only.
- The dump comes after the rebuild finishes, the evening CHANGELOG entry is written, and the commit is made.

## Should do, from 8 October

- **Still open:**
  - the send-a-photograph form's four server steps (runbook section 11);
  - the large uploads rsync and the server's free disk.
- **Still unruled: the four Firefly-edited marks** (#29696, #29439, #29400, #29298). They go up as they are unless Nathan rules first.
- **Runbook section 10 is still out of date in the same places:**
  - "last refreshed on 25 September", "165 commits" (lines 576 and 577);
  - step 7's "Only what changed since 25 September" and "The banners are in git (`web/banners/`) and came with the pull" (the pull deletes them);
  - no /send or /media id in step 8's page check. A 404 check of one withheld id, such as /media/38464, would prove the fix on staging.
- **The 20 enhanced pairs on records:** 15 carry the Firefly Image 5 text_to_image step (the fourteen and Couts), and Nathan is deciding them pair by pair (text-to-image-step-2026-10-09.md). Staging is behind basic auth, so this is a reason to keep it closed, not to hold the refresh.

## New since 8 October

- **The /media fix and its check** (above). The list must be rebuilt after any change to which record uses which image, then committed.
- **The 15 text_to_image pairs** are on records and are not withheld, by design (withheld means on no record). Their /media pages and files go to staging.
- **Bill Cooper's generated image #38464** is withheld. 24 queued "Generating image transform" jobs for it from 8 October have never run. If the queue runs locally, the transforms land in `_*` folders, which the rsync leaves out.
- **The calendar index is stale** (above).

## Order, as it stands

1. Let the rebuild finish.
2. Rebuild the calendar index.
3. Run check_render, then predeploy.sh, with nothing else running.
4. Commit the fix and its list together.
5. Take the dump with nothing writing.
6. Refresh staging (runbook).
7. Check /media/38464 for a 404 there.

The text_to_image pairs and the four marks are Nathan's to rule before or after; neither blocks a staging refresh behind basic auth.

## Read from a description

- **What staging holds:** that it was last refreshed on 3 October, that it checks out templates-batch-9, and that Wiley's and Vasquez's files are on its disk all come from HANDOFF, DEPLOY.md, TODO and the 8 October report. Staging was not read.
- **Couts put back on Nathan's word**, and the 15:59 change being that script, come from text-to-image-step-2026-10-09.md; what was checked is the relation row and its timestamp.
- **"The other ids 404"**: two withheld ids were fetched, not all 36. The rest follows from the template reading the list.
