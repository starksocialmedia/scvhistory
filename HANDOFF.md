# Handoff, 2026-10-08

HANDOFF says where things stand and where the rules live; it states no rules of its
own, so it has no copy to go stale. It is rewritten at the end of every session
(AGENTS.md), and `check_handoff.php`, run by check_render, fails it when it is more
than two days older than the newest CHANGELOG entry or when it states a rule.

## Standing caution: every generated image came from our own work

Nathan, 8 October 2026. Every AI-generated image found in this archive came in through our own work, not from Leon. It came by three paths:

1. **Grok banners.** Fifteen engraved banners, each a Grok portrait of a real person, on seventeen pages. All taken off on 8 October.
2. **Firefly portraits.** Real photographs put through Adobe Firefly (text prompts, Generate Fill, removals, undescribed Image 5 edits, generative upscales) and entered as enhanced portraits. On 8 October, 50 came off records, five of them Firefly files whose records never said so; the same evening real photographs replaced them on 19 records that had been left with none; three plain enlargements remain (Chico López, Randy Wicks, Bob Kellar).
3. **Files swapped in with a note that did not describe what happened.** On 6 October ten legacy portraits had their file replaced in place by a Firefly output recorded as "a better copy of the same image", with the Firefly file's checksum filed as the master's. All ten hold the original site's files again; Wicks's and Kellar's enlargements are now their own linked assets.

None came from Leon's site. The work that let them in was ours, and it read as careful work. The rule and its reasons are in docs/DATA-MODEL.md ("Generated and edited images"); what happened and how it was found is in ERRORLOG.md (8 October) and inventory/review/generated-images-2026-10-08.md.

## Start here

**Push state** (8 October 2026): Nathan pushes after his own predeploy; check `git log origin/templates-batch-9..HEAD` for local commits not yet pushed.

1. `git pull` on templates-batch-9, the working branch.
2. Read the documents in AGENTS.md's order. The newest CHANGELOG.md entries say what
   the last sessions did and decided.
3. TODO.md, "Waiting on Nathan", is the one list of what waits on him.

## Where things stand

- **Staging** was refreshed by Nathan on 3 October. Everything applied since is local only until
  the next refresh.
- **War memorial.** 52 of 54 records sourced; Kenaston and Wilson not found (4 October).
- **People.** 255 person records after the person-record rule of 6 October (docs/PROFILES.md, CHANGELOG): 39 thin office-only records are rows, their terms kept and named by holderName; the 44 with drafted profiles came back.
- **The civic layer.** Office holdings on every body with holdings, in tabbed pages. The Assembly,
  State Senate and House show every district that held part of the valley under each plan, with its
  share and members (inventory/review/legislative-districts-2026-10-04.md; templates/_data/valley-districts.json). The City has its five council districts. The other districts and SCV
  Water are behind their own pages (inventory/review/district-boards-check-2026-10-04.md).
- **Marks.** Every body with a mark shows it in the header; eleven imported on 4 October, the last two Santa Clarita Christian School's and Newhall Elementary's. Building
  photographs are related images.
- **Photographs.** Every photograph record with an image on Reggie has it in Craft (7 October, night; CHANGELOG), except 15 held for a read by hand; what each record's own folder holds is in too (the folder pass, 8 October), but 759 files held for Nathan. The web root holds web copies only; the masters are on Reggie, and storage/masters holds the files the web root used to serve.
- **Checks.** check_render also checks recorded checksums against the masters (check_checksums.php) and whether each new script says what it read before any number (check_census_reads.php; scripts/import/_reads.php).
- **inventory/incoming** is checked by check_render (check_incoming.php); OUTSTANDING.md gives a
  reason for every file still there.

## In progress or next

1. TODO.md, "Waiting on Nathan", 8 October continued first (the list of every portrait changed on 8 October; Angela Marler; what is still unchecked), then 8 October late (the people who lost a portrait), then 8 October evening (the 759 held files), then 8 October afternoon (#5245's page order), then 8 October (the 153 sole copies he is checking), then 7 October night (the Leon request to send; the 13 TIFF masters), then 7 October evening (the retype read, four held titles), then 6 October night (the disaster-figures audit to rerun whole; Stern and the terms that ended when the lines moved), then 6 October (the duplicate pairs, the redirect map), then 5 October: the appointed-terms audit (not to be fixed before Nathan reads it), the same-name aliases, authorship, the silent-faults dry runs, and the portrait census to rerun whole. Then: Measure U, the Saugus High source records and the St. Francis Dam (all dry runs), the events queue after them (inventory/review/events-inventory-2026-10-05.md), the Harris interview when it arrives, the two trustee profile dry runs, two portraits whose credentials record text_to_image, the war memorial differences shown but not changed, the 490 date decisions, and the photo form's server steps.
2. The send-a-photograph form is built and tested on DDEV (/send, /admin-submissions); it reaches staging with the next refresh, after docs/DEPLOY-RUNBOOK.md section 11.
3. The place record Porta Bella (#20152), live and empty: whether to build it. The congressional split recheck.
4. The City Hall photograph, when Nathan sends it.
5. Affiliations exist for few people yet; the older person-organization links are still to be read into them.
6. The Worden duplicate pairs and the remaining board corrections (inventory/review/duplicate-slugs-2026-10-04.md, district-boards-check-2026-10-04.md); the 17 unsourced term endings (term-endings-2026-10-04.md).
7. When the queue runs out: the 51 people with no legacy page and no profile (no list made yet), and the Hart district's terms before 1995 from the board minutes.

## Where the rules live

Each rule lives in one document; this list only points. A rule written as a principle
("an edited photograph enters with its edit recorded") lasts; a rule that describes a
state ("war memorial images are related-only") expires when the state changes, and was
retired on 3 October for that reason (38 of 54 casualties now have portraits). Write
rules as principles, and date any that describe a state.

- Process, git, safety, the database and other agents, Twig traps: AGENTS.md.
- What the archive is for and its design: PHILOSOPHY.md.
- Where each kind of thing goes: DATA-ORGANIZATION.md.
- Fields, evidence, CEDA's limits, media, edited photographs, banners, band and frame:
  docs/DATA-MODEL.md (generated by scripts/import/generate_data_model.php).
- Writing profiles, sources, the Reynolds and roster reliability rules, notes that say
  no source exists and where their searches are recorded: docs/PROFILES.md.
- Import scripts, record templates, review screens: .claude/skills/.
- Deploying, staging, large files, the Reggie drive, searching the mirror:
  docs/DEPLOY-RUNBOOK.md.
- Errors and the patterns to avoid: ERRORLOG.md.
