# templates-batch-9 to staging: the list, with the critical path (10 October 2026)

Claude, for Nathan (overnight brief, item 17). This updates branch-to-staging-2026-10-09.md. Nothing was run against staging.

## What was read

- **Last night's list:** branch-to-staging-2026-10-09.md.
- **Git:** `git log` and `git status` this morning. 1095412 was pushed by you ("Pushing 1095412"); tonight's work is one local commit on top of it.
- **Tonight's applies:** scripts/import/APPLIED.log.
- **Not read:** staging itself; predeploy.sh, which was not run.

## What changed since last night

- **No template, config or composer change tonight.**
- **Database only:**
  - Jenkins: a death certificate document #38566, an obituary #38568, two legacy scans #38564 and #38565, his footnotes and deathEvidence;
  - footnotes on ten person records;
  - McGrath's howEnded.
- **The withheld-media list** is rebuilt only if check_render's generated-files check says it is stale. The two new scans are the original site's own files and are on records.
- **Every /media fix item from last night's list is now committed (1095412) and pushed:**
  - the template;
  - config/custom.php;
  - config/withheld-media.json and .txt;
  - the check scripts;
  - the rebuilt calendar index;
  - the DEPLOY docs.

## The critical path

Each step waits on the one before. Nothing else stands between the branch and a staging refresh.

1. **check_render passes on tonight's commit.** It was run at the end of tonight's work, and the commit says whether it passed (CHANGELOG, 10 October).
2. **You push tonight's commit.**
3. **predeploy.sh passes, with nothing writing.** It has not run since 8 October. It includes check_rendered_bodies.php over every record, so allow the time.
4. **The dump is taken with nothing writing:** after the push, before any new apply.
5. **The refresh, by the runbook:** pull, import the dump, then the rsync with `--exclude-from=config/withheld-media.txt`.
6. **On staging:**
   - /media/38464 answers 404 (the withheld check);
   - a record page and a /media page that should serve, such as Couts's #27387, answer 200.
7. **Your /review/ exposure check** with the basic-auth credentials (runbook section 10, step 3).

## Not on the critical path (staging is behind basic auth)

- **The rule for the 22 pairs and the four marks (DECISIONS 1 and 2).** Without a ruling, they go up as they are now: the 15 text_to_image pairs on their records, and the marks as they are.
- **Wiley's #1658 and Vasquez's #28816 files already on staging's disk.** The rsync never deletes, so they stay until removed by hand. Their /media pages will 404 after the refresh.
- **The send-a-photograph form's four server steps (runbook section 11); the large uploads rsync and the server's free disk.**
- **Runbook section 10 is still out of date:**
  - "last refreshed on 25 September" and "165 commits";
  - step 7's "Only what changed since 25 September" and the banners line;
  - no /media 404 check in step 8.

  Updating it is a docs edit and will wait for your word on whether section 10 or DEPLOY.md is the one to keep.
- **The archive's false sentences (DECISIONS 4)** go up as they are unless fixed first. They are wrong on the live site's data too, so this is not a reason to hold staging.

## Read from a description

- **What staging holds** (last refreshed 3 October; Wiley's and Vasquez's files on disk) comes from HANDOFF, DEPLOY.md and the earlier lists. Staging was not read.
- **"1095412 pushed"** is your message's word. The local status showed the branch level with origin before tonight's commit.
