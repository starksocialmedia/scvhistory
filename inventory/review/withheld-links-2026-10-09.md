# The withheld /media list: does anything still use or link these files? (9 October 2026)

Claude, for Nathan (brief item 14). Read only: nothing in the database was written, nothing committed.

## What was read, before any number

- **The list:** config/withheld-media.json, **built 2026-10-09 15:24** (copied at the start; 36 assets). A rebuild by another process began at 16:00 and had not finished at 16:17; this report is against the 15:24 build.
- **Relations (records, not files):** every row of the relations table with the asset as source or target, joined to the element on the other end (live, disabled, draft, revision or trashed), and Craft's own queries for live entries and categories related to the asset either way. Queried about 16:05.
- **Text (records):** title, slug, URI and the content JSON (every custom field: body, footnotes, editor notes, tables, plain text) of all 34,290 element-site rows, live or not, drafts and revisions included, searched for `/media/<id>`, `{asset:<id>`, the filename, the filename without extension, the volume path and the URL path `/uploads/archive-media/<path>`.
- **Other tables:** a dump of every other table, searched for the filename, path and `media/<id>`.
- **[image:N] tokens:** a token names the Nth of the record's own recordImages (templates/_partials/prose.twig), so it can only embed an asset the record relates to; it is covered by the relation check.
- **templates/_data:** all 1,433 files, recursively, for the same strings and the bare id.
- **Pages:** local GET of /media/12, /media/27387, /media/29696, /media/29698 and /persons/cave-johnson-couts.
- **Not read:** the files themselves (item 15 uses the scan of them), staging.

## Answer

- **35 of the 36 are on no live entry or category, and nothing in any live field links, embeds or names them.**
- **One is live on a record now: #27387, Cave Johnson Couts's enhanced portrait.** It became #323's featuredImage at 15:59:48 (relation row 158968), 35 minutes after the list was built, when he was put back as an enhanced pair (couts_pair_back_2026_10_09.php). Until the list is rebuilt:
  - /media/27387 answers 404 while it is his portrait;
  - /media/12, his original, shows "EDITED COPY" linking to that 404;
  - config/withheld-media.txt would keep his portrait file out of the staging rsync, so on staging his page would show a broken image.
  - The rebuild running now should drop him; check_generated_files.php fails while the list is out of date.
- **One data file still embeds a withheld file: templates/_data/calendar.json** (built 8 October 12:30, committed) gives Jerry Gladbach's "Died, July 13, 2022" row the image `/uploads/archive-media/outside/jerry-gladbach-portrait.jpg` (#31417). His record's portrait has been #38486 since the evening of 8 October; the calendar was not rebuilt after. Locally the file still serves at that URL (only the /media page 404s); on staging it would be a broken image, since the rsync leaves it out. The same file also points at `legacy/perkins_ab_2026-09-18-071146_nitl.jpg` (#1757), which is on no live record but not withheld. A calendar rebuild (build_calendar_index.php) clears both.
- **#29698**, the Newhall Elementary logo original, is on no record, but #29696 (the Firefly-edited logo, current mark of #15958) names it in enhancedFrom. Its /media page would link "ORIGINAL" to /media/29698 (a 404), but a current mark's /media address redirects to the record (302 here), so the link is never shown.

## Per asset

"Other relation rows" are rows whose source is a revision (Craft's history, never shown) or an asset-to-asset enhancedFrom. Where the filename without its extension matched, it was always the slug, URI or title of the person record of that name (persons/george-runner and the terms titled with it), never a file reference; these are named so the search is visible.

| Asset | File | (a) Live relation | Other relation rows | (b) Text, link or embed | templates/_data |
|---|---|---|---|---|---|
| #1658 | henry-clay-wiley-portrait.jpg | none | 8 in revisions only | none live; two old revision elements (#889, #989; revision ids 465 and 553) embed a WordPress-hosted henry-clay-wiley-portrait-546x800.png, not this asset | none |
| #27385 | outside/andres-pico-commons-edited.jpg | none | 5 in revisions only | none | none |
| #27387 | outside/cave-johnson-couts-us-army-edited.png | **#323 featuredImage (live)** | 13 in revisions only; enhancedFrom of #27387 to #12 | none | none |
| #27389 | outside/bill-cooper-edited.jpg | none | 5 in revisions only | none | none |
| #27391 | outside/alan-ferdman-edited.jpg | none | 2 in revisions only | none | none |
| #27394 | outside/patsy-ayala-edited.jpg | none | 3 in revisions only | none | none |
| #27398 | outside/marsha-mclean-edited.jpg | none | 3 in revisions only | none | none |
| #27400 | outside/jason-gibbs-edited.jpg | none | 3 in revisions only | none | none |
| #27402 | outside/bill-miranda-edited.jpg | none | 2 in revisions only | none | none |
| #28816 | outside/tiburcio-vasquez-1874-upscaled.jpg | none | 2 in revisions only | none | none |
| #28972 | outside/erin-wilson-hart-district.jpg | none | 1 in revisions only | none | none |
| #28974 | outside/cherise-moore-hart-district.jpg | none | 2 in revisions only | none | none |
| #28976 | outside/bob-jensen-hart-district.jpg | none | 4 in revisions only | none | none |
| #28978 | outside/joe-messina-hart-district.jpg | none | 3 in revisions only | none | none |
| #29118 | outside/brian-walters.png | none | 3 in revisions only | none; the name "brian-walters" only as the slug or title of a record of the same name | none |
| #29122 | outside/patti-rasmussen.jpg | none | 2 in revisions only | none; the name "patti-rasmussen" only as the slug or title of a record of the same name | none |
| #29124 | outside/george-runner.jpg | none | 5 in revisions only | none; the name "george-runner" only as the slug or title of a record of the same name | none |
| #29326 | outside/sharon-runner.jpg | none | 1 in revisions only | none; the name "sharon-runner" only as the slug or title of a record of the same name | none |
| #29330 | outside/steve-knight.jpg | none | 4 in revisions only | none; the name "steve-knight" only as the slug or title of a record of the same name | none |
| #29561 | marks/newhall-county-water-district-logo.png | none | 2 in revisions only | none | none |
| #29563 | marks/castaic-lake-water-agency-logo.png | none | 2 in revisions only | none | none |
| #29698 | marks/newhall-elementary-school-logo-original.jpg | none | enhancedFrom from #29696 to #29698 | none | none |
| #31389 | legacy/ap2222_large_enhanced.jpg | none | 1 in revisions only | none | none |
| #31393 | legacy/lw2452-enhanced.jpg | none | 1 in revisions only | none | none |
| #31400 | legacy/rn3004-enhanced.jpg | none | 2 in revisions only | none | none |
| #31402 | legacy/tf1000-enhanced.jpg | none | 1 in revisions only | none | none |
| #31410 | legacy/lw2317a_large-writing-removed.jpg | none | 1 in revisions only | none | none |
| #31414 | outside/pete-knight.jpg | none | 3 in revisions only | none; the name "pete-knight" only as the slug or title of a record of the same name | none |
| #31417 | outside/jerry-gladbach-portrait.jpg | none | 2 in revisions only | none | **calendar.json embeds the file URL** |
| #31425 | legacy/reminadeau-chrisman-enhance.jpg | none | 1 in revisions only | none | none |
| #31429 | legacy/lw9501_large_enhanced.jpg | none | 1 in revisions only | none | none |
| #31443 | outside/bj-atkins.jpg | none | 2 in revisions only | none; the name "bj-atkins" only as the slug or title of a record of the same name | none |
| #31445 | legacy/brathwaite-louis-enhance.png | none | 1 in revisions only | none | none |
| #31451 | legacy/sc9612-enhanced.jpg | none | 1 in revisions only | none | none |
| #31465 | outside/audra-strickland.jpg | none | 2 in revisions only | none; the name "audra-strickland" only as the slug or title of a record of the same name | none |
| #38464 | outside/bill-cooper-campaign-2026.png | none | 1 in revisions only | none | none |

Other tables: the filename appears only in the asset's own row, in the image transform index (transform files, which the rsync's `--exclude='_*/'` leaves out), and, for #38464 (Bill Cooper's generated campaign image), in 24 queued, never-run "Generating image transform" jobs from 8 October. `/media/1658`, `/media/27387`, `/media/31451` and `/media/38464` are in the Retour 404 log from today's curl checks.

## Read from a description

- That Couts was put back on Nathan's word comes from inventory/review/text-to-image-step-2026-10-09.md; what this report checked is the relation row and its timestamp.
- That Wiley's and Vasquez's files are already on staging is from TODO.md and the 8 October report; staging was not read.
