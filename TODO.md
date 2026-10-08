# TODO

## Waiting on Nathan

### Current (8 October 2026, continued)
- **The portraits that changed on 8 October**: all 56 person records in one list, with the old image, the new one or none, and the reason and rule for each (inventory/review/portrait-changes-2026-10-08.md). 19 have no portrait now. Nathan to read; nothing acted on.
- **Angela Marler (#28312)**: a live record with no text and no portrait. It has one term (Castaic Union, #28515) and a 2005 candidacy (#25513). It was restored on 6 October with the quote "restore her record. I meant keep" (restore_rows_2026_10_06.php). Under the person-record rule (docs/PROFILES.md) it would be a row: none of the exceptions holds, and the mirror pass found nothing to write from. The 8 October work did not touch it. Nathan's word to take it back to a row.
- **What is still unchecked** (inventory/review/unchecked-claims-2026-10-08.md): 6,909 of 6,958 assets have a claim about their file that rests on a record. The largest classes are 4,201 web copies never compared with their masters, 2,697 with credentials never read, 2,569 with no checksum and 2,450 given to a record by name. Which to check first is Nathan's call. The cheapest is the credential scan over the 2,496 masters on Reggie.

### Done (8 October 2026, after midnight; applied later on 8 October on Nathan's word: both portraits set, Wicks and Kellar in the López pattern, the checksum audit in check_render)
- **Two people without a portrait hold an unedited photograph of themselves as a related image**, the only two of 153: Pete Knight (#29314, sg042504.jpg, "Pete Knight on April 1, 2004", from the original site) and Tiburcio Vasquez (#285, tiburcio-vasquez.jpg, the 1874 oval portrait, from the old WordPress site, no source further recorded). Nathan's word before setting them.
- **Randy Wicks and Bob Kellar** (ERRORLOG, open): their checksums name a master but hold the Firefly enlargement's hash. Proposed: the López pattern.
- **The checksum audit as a standing check**: checksum_audit_2026_10_08.py into check_render, failing on "matches neither the master named nor the file". It is shown to fail; Nathan's word to add it.

### Current (8 October 2026, late)
- (21 by the end of the night; Pico's set 8 October after midnight) **17 person records lost their portrait today** (CHANGELOG, 8 October late): a real photograph for any of them, with its source, enters under the edited-image rule as an unedited original. Pico's record already holds an unedited photograph of him (andres_pico_circa_1850.jpg) as a related image: Nathan's word whether it becomes the portrait.

### Current (8 October 2026, night)
- (applied 8 October, late: 38 off, 3 enlargements stay) **41 Firefly edits held in place** (inventory/review/generated-images-2026-10-08.md, section 4, and the side-by-side sheet): Nathan to decide each. 13 with fill, removal or cleaning; 18 Firefly Image 5 edits whose credential does not say what was edited; 10 enlargements only. Sheet: https://claude.ai/artifact/G3ksC59M8TWuNhyik6mcRA.
- (Schmidt and Hon restored 8 October, late) **The five records left with no portrait** after the pull (Pete Knight, Tiburcio Vasquez, Henry Clay Wiley, Earl Schmidt, Dan Hon): whether to restore the legacy originals of Earl Schmidt (sk5003) and Dan Hon (danhon) from Reggie into their assets; Wiley's real photograph to be found.

### Current (8 October 2026, evening)
- (pulled 8 October, night, with the text-prompt portraits) **Generated images** (inventory/review/generated-images-2026-10-08.md): Henry Clay Wiley's portrait (#1658, from Firefly, no source recorded) first; then whether the new rule reaches the 9 portraits regenerated in part from a text prompt and the 34 Firefly upscales and fills. Marks and ornament not ruled.
- **The 759 files the folder pass held** (inventory/review/folder-pass-dry-run-2026-10-08.md): uc8901's 658 raw page scans, the shared folders (#605, #613, gt8702, scvhs2000minutes), LW3267's pictures in LW3257's folder, thumbnails, -orig PDFs, one mp3.

### Current (8 October 2026, afternoon)
- (applied 8 October, evening) **The folder pass** (inventory/review/folder-pass-dry-run-2026-10-08.md): dry run done, 91 records, 803 pictures and 56 documents to add, 759 files held with reasons (658 of them uc8901's raw page scans). Nathan's word to apply.
- **#5245** may have its pages out of order (ERRORLOG): the original's order from the item or Leon.
- (done 8 October, evening: all fifteen banners off) **The Hart banner** web/banners/william-s-hart.jpg is made from WilliamS.jpg, a Grok image in inventory/incoming: Nathan's call whether that changes anything.
- **The 14 sole copies with no other copy known** (sole-copies-origins-2026-10-08.md): Nathan checking his drives.

### Current (8 October 2026)
- (applied 8 October, afternoon) **Titles against the scans, batch 2 of 4** (inventory/review/titles-batch-2-2026-10-08.md): twenty, each printed headline quoted; 15 retitled, 5 kept. Three for Nathan's call: #28291, #5359, #5245. 37 left for batches 3 and 4, all ephemera.
- (done 8 October, afternoon) **The 15 held photographs**, with the pictures (a private artifact; links in the report of 8 October): for 9 the right picture is in the record's own folder; for 6 the census's pick is right (#2939 and #2741 carry another page's code, LW2158 and LW2042).
- **The 116 in inventory/incoming and the 37 supplied** (inventory/review/sole-copies-origins-2026-10-08.md): Nathan to check his drives, then into inventory/sole-copies. The 37 are re-saves of files in inventory/incoming/done.
- (done 8 October, afternoon) **#26573 and the election documents** (inventory/review/document-subject-organization-2026-10-08.md): subjectOrganization on the document type, filled on 14 from the election links, shown on the page. Schema change: Nathan's word.
- (measured and dry-run 8 October, afternoon) **The import's blind spot** (ERRORLOG, 8 October; storage/runtime/photo-import/folder-gap.json): 39 source PDFs, six records' slideshow pictures, and records skipped because they had one picture. A second pass, dry run first, on Nathan's word.

### Current (7 October 2026, night)
- (batch 2 sent 8 October) **Titles against the scans, batches 2 to 4** (Nathan's rule of 7 October, night): about 57 left of the 79, by hand, twenty at a time, each printed headline quoted. Batch 1 is applied (inventory/review/titles-batch-1-2026-10-07.md).
- **15 photographs held from the import** (inventory/review/photo-import-held-2026-10-07.md): the census's picture does not carry the record's code. To read one at a time.
- (8 October: all into git on Nathan's word; 152 there, 153 wait on his drive check) **Files whose only copy is outside git and Reggie** (inventory/review/sole-copies-2026-10-07.md): 313 files, 429 MB.
- **The Leon request** (inventory/review/leon-files-request-2026-10-07.md): one list of 201 files, for Nathan to send. The 175 masters are on neither Reggie nor the Internet Archive.
- **The 13 TIFF masters the Internet Archive holds and Reggie lacks** (inventory/review/mirror-gap-2026-10-07.json): fetching them into storage/masters is open.
- **Staging**: the next uploads rsync replaces each magnifier master on staging with its web copy (same names), and adds the photograph import, about 1.5 GB.

### Current (7 October 2026, evening)
- (7 October, night: the title rule is set and applied in batches; the photograph import run; the Leon request made one list, for Nathan to send. The retype read below still waits.)
- **Titles against the scans** (inventory/review/titles-vs-scans-2026-10-07.md): of 124 legible scans of printed matter that print a headline, 45 titles match it, 42 are a shortened or lengthened form, 35 are Leon's words, 2 were better before today's retitle (#4607, #4751). Newspapers and magazines 25 of 47; ephemera 20 of 77. Unchanged titles fail the same way. A rule for printed matter is Nathan's: about 79 titles to fix one by one from the scans, each printed headline quoted. #32724 (sg110185) prints "School Chiefs".
- **The retype** waits on that rule (Nathan: "Stop before the retype").
- **The mirror is not complete:** Reggie lacks files Leon's server served in December 2025. Ten known (inventory/review/leon-files-request-2026-10-07.md, a draft for Nathan to send); the count across the 1,449 against the Internet Archive's index is running (storage/runtime/mirror-gap/). The photograph import (inventory/review/photo-import-plan-2026-10-07.md, approved in principle; the magnifier originals leave the web root with it) waits on that count. No record is marked lost.
- **The retype read** (inventory/review/retype-dry-run-2026-10-07.md): 39 photographs (17 article, 4 unsure, 18 not, nine of them the Land of Sunshine pictures) and 25 documents (24 article, #31306 not), the printed header quoted for each. Nothing moves until Nathan has read it. The dry run found that the headline at the top of a legacy page is often Leon's, not the publication's (#4783, #4909, #20107 checked against the scans). Moving the 24 documents repoints 18 event source links and two obituary links; eight LA Times documents are disabled and stay so. #865 (lw2304a, a map, kept as an article by the merge) belongs in the same read.
- **Photographs with no image in Craft: 1,449** (not 1,392; the census counted field uses). 1,431 have their image on Reggie and were never imported (1,339 single images, 6.15 GB; 89 flipbooks or PDFs, 1,602 files, 4.1 GB as JPEG and PDF, 57.6 GB with the TIFF masters; 3 found by code); 10 have lost it (#5735, #5737, #5739, #4475 nothing anywhere; 6 with the page but not the file); 8 never had one (inventory/review/photo-images-census-2026-10-07.md). The import is Nathan's call.
- **Four article titles held** (title-plan-2026-10-07.json, "held"): #12534, #12854, #12852, #12796; and 20 photographs left as they are (7 not on Reggie, 4 on multi-piece pages, 9 with no headline found).

### Current (7 October 2026, afternoon)
- (done 7 October, evening: Nathan's rulings applied) **Titles against Leon's pages** (inventory/review/title-census-2026-10-07.md): of 2,200 records with a page, 687 identical, 77 case or punctuation only, 987 words added or dropped, 420 a different title, 21 multi-piece, 8 no printed title. Photographs took the page's <title> (Leon's catalogue caption) not the printed headline; 57 of the 58 differing articles took an extraction file's title (44 the series index link text). Nathan to rule: photographs (headline or caption as title), the 58 articles, the documents' "(Author, Publication, Date)" suffix.
- (rulings applied 7 October, evening; the moves wait on the retype read) **Type by content** (inventory/review/type-census-2026-10-07.md): photographs holding article scans 19 sure, 2 likely, 8 unsure, plus 18 web articles on photo pages; documents that are articles 23 sure, 6 likely, 8 unsure. Signal: a byline or "Publication | date" header and no ephemera word in the title (a review queue of 39, not an automatic move). The model holds one identity per record but has no field for the form a thing survives in, no facsimile role for a scan, and no home on articles for the photograph-only fields. Duplicates across sections: Perkins 1957 (#1434 and #27374), lw2304a (#865 and #3179), assets 1812/1813 (#1444 and #2689). Nathan to read before anything is written.
- (done 7 October) sg110185 as a collection of seven articles under their printed headlines; #28295 stays a document until the type ruling.
- (done 7 October) The authorship basis on all 746 author links. Six are derived (#28287, #855, #1432, #1418, #865, #849), each saying from what; four rest on the text held because the page is not on Reggie and Archive.org was rate-limiting.
- (done 7 October) faultRecord takes every section.
- (done 7 October) #12144 split (#31962, no parent). The 137 Archive.org captures read: every one prints its author's byline and the record's title; Sol Taylor's columns carry a byline on each column, not a series heading. 86 have no capture and stay unconfirmed, said in each note.
- **Disasters:** scope settled 7 October (natural and accidental only; crime, the Saugus High shooting among it, stays out; docs/DATA-MODEL.md). The comparison section is next to design; no figure row is published without its scope.
- **Northridge #875:** its 57 dead and $13 to $50 billion sit in eventSignificance and the withheld body with no footnote.

### Current (6 October 2026, night)
- (done 7 October) Grant the terminal access to the Reggie drive (MacBook: System Settings, Privacy & Security, Files and Folders, the terminal app, Removable Volumes). Since it remounted on 6 October, this session got "Operation not permitted". The disaster audit needs it.
- **Stern and the terms that ended when the lines moved:** Nathan to say go on (1) a howEnded option "Lines moved: the district no longer held the valley", set on the twelve terms whose footnotes say so, after a check of each (5 are "reelected", 7 "expired" now); (2) printing such a term's ending note in the offices box (term footnotes show nowhere today).
- War memorial records and the 490 date decisions: Nathan's.
- **Design question, not a build: disasters and their consequences** (Nathan, 6 October 2026; the principle is in docs/DATA-MODEL.md: one row per figure per source, scope required on every row, not natural disasters alone, a kind field carries the distinction). The audit comes first and is NOT done: an agent started it on 6 October and was stopped at the session's close with nothing written (macOS had refused access to the Reggie drive). Rerun it whole once Removable Volumes access is granted to the terminal; its brief is in the CHANGELOG entry "after midnight", 6 October. One section, not wildfires alone: fires, floods, earthquakes and the dam, compared by deaths, acres, structures, cost (in the money of the day and today's) and displacement, and what each did to the valley. "Consequences" because some are both: the St. Francis Dam was a structural failure that caused a flood; Sylmar collapsed the Newhall Pass interchange. First job: find what can be compared at all and what each figure rests on. What the archive would need:
  - Today the event type has no figure fields: deaths, acres, structures, cost and displacement live only in prose and notes, and they disagree (Northridge; the Dam's dead from "probably almost five hundred" to later counts; Flight 7's two and five; the 1938 flood's 113 to 115 for the region, none in the valley).
  - A figures table on events, one row per figure per source: measure, value, unit, scope (the valley or the region; the 1938 flood's dead are regional), as of when, source, evidence. Several rows for one measure hold a disagreement instead of picking.
  - Cost in today's money needs a stated index and base year on the page, never a silent conversion.
  - A kind of event (fire, flood, earthquake, structural failure) and cause-and-consequence links between events (the Dam and its flood; Sylmar and the Newhall Pass), on relatedEvents or a new relation with a role.
  - The boundary, settled: not natural disasters alone (the dam, the air crashes, the Newhall Pass truck fire are in).
  - The audit itself: every disaster event held (the Dam, the 1938 flood, Sylmar, Northridge, the Powerhouse Fire) and the A and B entries in inventory/review/events-missed-2026-10-05.md, with each figure, its source and its scope, before any field is designed.
- Navigation in the body copy (Nathan confirmed Western Air Express Flight 7): done 6 October; the people aboard are tables on Flight 7 (Ron Kraus's chart, thirteen) and United Flight 34 (the twelve, all nine passengers named by SCVHistory.com's account).

### Current (6 October 2026, evening)
- **Stern's holding #29521** is marked reelected in 2024 though his district then held none of the valley.

### Current (6 October 2026)
- **Duplicate article pairs** (Leon Worden's Signal columns): #12206/#12200, #12204/#12188, #12280/#12258 are word-for-word the same, one copy from /signal/worden/old/; #12152 (869 words) and #12208 (761) are two versions of the Piru column. Which to keep.
- **Letters as documents with a writer:** John Lang's (#28057), Abel Stearns's (#26983): does a letter's writer count as its author.
- **The redirect map at cutover:** sg20191114shs.htm to the Saugus High event (D12), the video pages (D11), chp-newhall-incident.htm to the Newhall Incident.
- **The 356 bylines:** 320 linked on 5 October, 5 documents on 6 October; what remains is names with no record (25), a pen name, and the held cases.

### Current (5 October 2026, late night)
- Search names: applied 6 October. Was: the alias removal cut "A.B. Perkins", "Bill Hart", "Joseph Messina" and others out of search; a hidden search field puts 177 back and 4 maiden names return as shown aliases.
- **Portrait batch** (inventory/review/portrait-batch-dry-run-2026-10-05.md): read before apply. Chico López has a likeness (US8502), so his "no likeness" note is held: import it instead?
- **Events, dry runs to read:** the Saugus High sources then event (the event needs the sources applied first; whether events get a sourceDocuments field); the St. Francis Dam loader (its decision list); Cityhood, Placerita, the golden spike, the Newhall Incident (naming the gunmen once, as the sources do).
- **War memorial sources** (inventory/review/war-memorial-unsourced-dry-run-2026-10-05.md): Kenaston via the VA locator (approve its use for him), Colley via the 2003 yearbook; Wilson, Todd, Conant, Ross searched and still short.
- **Authors:** documents need a writtenBy field (11 waiting); the Ellis Gazette bylines linked on two SCVNews pieces; the four duplicate article pairs; #2173 and #12852 held.
- **Tom Frew II:** a record of his own?

### Current (5 October 2026, night)
- **Appointed terms:** done 5 October (unopposed, the splits, Messina, the four notes, Gibbs and Plambeck). Still open: the 2 partly right runs (Talley 2016, Moore 2017); DeFigueiredo 2007's "no election held", which rests only on a contest missing from CEDA; the 18 RISKY quotations and the #394 attribution in inventory/review/spliced-quotations-audit-2026-10-05.md.
- **The portrait census** is running again (5 October night); its report writes as it goes.
- **Same-name aliases** (inventory/review/aliases-same-name-dry-run-2026-10-05.txt): 186 lines on 135 people, dry run; Messina's applied. Four kept although the test matched (McKeon's Howard, Knight's William, Weinstein's Rochelle, Tichenor's Jr.). Removing "Bill Hart" and the like also removes them from search.
- **Authorship** (inventory/review/authorship-census-2026-10-05.md): approve writtenBy on documents and a bylineText field; then about 329 easy links; the person page to name a person's columns.
- **Silent faults, the dry runs:** war memorial narratives (8, restore_war_memorial_narratives_2026_10_05.php, with extract_war_memorial_narratives_2026_10_05.py run first on the MacBook); Hart Park captions (13), place legacy links (7), place image alt (6) (inventory/review/*-dry-run-2026-10-05.md); the Mentry credit is right and stays. Decisions in each.
- **Connie Worden-Roberts:** restore Goldman's two dropped paragraphs (#28047)? correct #28045's publication line to the mortuary's dateline? Choppé's portrait on her person record? Perry Smith's piece (#28305) as an obituary?
- **Collections:** the three topic pages (Newsmaker, Iraq, Mentryville) and the Gazette catalogue still take their own layouts; whether they too take the standard one.

### Current (4 October 2026, overnight)
- Measure U and the fourteen council elections: applied 5 October. **The five County-sourced elections, 2016 to 2024** (the same script, extended): dry run ready.
- **The silent-faults audit** (inventory/review/silent-faults-audit-2026-10-05.md): 29 findings; the empty-note template fault is fixed; the other visible ones wait on Nathan.
- **The Saugus High source records** (inventory/review/saugus-high-2019-sources-dry-run-2026-10-05.md): read, then apply create_saugus_2019_sources_2026_10_05.php. The 74 City vigil photographs wait for Nathan's look.
- **The St. Francis Dam** (inventory/review/st-francis-dam-dry-run-2026-10-05.md): read the record; the decisions at its foot (a dam place record first, Mulholland's 431, the Ruiz count, the Newhall Land report as a document).
- **Northridge:** rebuild plan in the report of 5 October; Stearns's mint date, a disagreement between his letter and the voucher, waits for the Reggie drive.
- **Reggie** dismounted on 5 October: reconnect it and restart DDEV (docs/DEPLOY-RUNBOOK.md).
- **The college district trustees' profiles** (inventory/review/coc-trustees-profiles-dry-run-2026-10-05.md, 33): read, then apply with build_coc_trustee_profiles_2026_10_05.php. Tichenor's closing paragraph and Johnson's Saugus runs are in the drafts; the Hoskinson and Lynch leads go to researchLeads, not the page.
- Notes to ourselves, the legacy-page wording, Mentry's source fault: applied 5 October.
- Sub-body marks: built 5 October (the parent's mark in the Parent organization box).
- **The Darren Harris interview:** the recording, its date, who asked, and his title then. It becomes a document record with a verbatim transcript, and Harris a person record with the Public Information Officer role (#30506).
- Johnson is Duzick: applied 5 October.
- Leads off the page, the sweep: applied 5 October.
- **Send the note to the college district** about Don Allen (inventory/review/coc-don-allen-note-2026-10-05.md).
- **Fallen officers:** read the fourteen (disabled; inventory/review/fallen-officers-draft-2026-10-05.md, or in the control panel), then enable them (re-run create_fallen_officers_2026_10_05.php with $ENABLE = true).
- **The Hart trustees dry run** (inventory/review/hart-trustees-profiles-dry-run-2026-10-05.md): read the 55 and the "For Nathan" items under each; then apply.
- **The nav** (inventory/review/nav-proposal-2026-10-05.md): six decisions; then the build, and the body pages' breadcrumb with it.
- **The district map key** on the Senate and Assembly: look, then the House.
- **The Sheriff** (inventory/review/lasd-contract-office-memorial-2026-10-05.md): whether to build an officers' memorial section (a schema plan first) and who counts; whether any sheriff gets a person record; the first contract's date needs the City Clerk's or the Board of Supervisors' records of December 1987 to 1988.
- **Hart before 1995** (inventory/review/hart-pre1995-terms-2026-10-05.md): Aliano's appointment as May 1994; Loberg and King ran and lost in 1993 (a "defeated" ending needs a new option); Warren's resignation, March or 6 April 1994.
- **The 51 people:** which 51 (inventory/review/people-without-profile-2026-10-05.md lists 167).
- **Two portraits may be generated, not edited:** Patti Rasmussen (#29122) and Brian Walters (#29118). Their files' content credentials record Firefly text_to_image steps. Keep them as edited photographs (with the edit recorded), or take them down. Nothing was changed.
- **Seven portraits with a Firefly edit now recorded** (Knight, Sharon Runner, George Runner, Messina, Jensen, Moore, Erin Wilson): who made the edit (enhancedBy is empty), and whether the unedited originals exist, to be held beside them under the new keep-both rule.
- **War memorial differences, shown on the records and not changed** (inventory/review/war-memorial-sourcing-2026-10-04.md): Cone is U.S. Navy, Seaman Second Class, missing August 10, 1943 by ABMC and the Navy's 1946 list (the record says Army, March 13, 1945; middle name Russel in both); ranks at death per the Defense Department (Sellen, Gelig, Acosta: Specialist or Private First Class against Sergeant or SP4); Acosta was 19, not 20; Suter's release gives Los Angeles; Todd appears as Spc. Dean Todd-Eckard of Canyon Country; Ross's ABMC date is September 30, 1944; Rubel reenlisted November 1942, a driver; Ball's draft registration was 1942, and his January 15, 1946 date has no source; Conant is not in the VA locator, so "Punchbowl" is unsupported. Kenaston: may the VA locator be used for him (a lead puts him at Los Angeles National Cemetery)?
- **The 490 date decisions:** review/dates.html, in Nathan's browser; no confirmed.json has been exported.
- **The photo form on the server:** the four Cloudways steps in docs/DEPLOY-RUNBOOK.md section 11, before it goes to staging.
- The Newhall Redevelopment Committee (#16290): how its terms read ("without term limits", 2002, or four-year terms, 2005). Its end is now March 1, 2012, from the chronology.
- Acton-Agua Dulce Unified (#29691): which high school district Acton and Agua Dulce left in 1993, and the County Committee's 2025 trustee-area resolution (inventory/review/aadusd-redevelopment-2026-10-04.md).

### Current (3 October 2026, evening)
- Seat boundary files (trustee areas, council districts, SCV Water divisions): Nathan is asking the Hart district and the City. Nothing is drawn until they are in hand.
- The research list of 10 first-win incumbents (inventory/review/board-holdings-dry-run-2026-10-03.txt, section 4), with the districts' online minutes archives as the first place to look.
- The next staging refresh carries what was applied after the 3 October refresh: the 19 no-source corrections, Couts, the California Battalion, Smyth's former portrait, the Hart 2022 Trustee Area 2 records.
- Done 3 October, evening: the three held dry runs, Smyth's former portrait, Jensen's certified 11,639 and the missing Hart 2022 Area 2 election and candidacies.

### Carried from the 18 September handoff (not rechecked since, unless marked)
- 6 community coordinates: fair-oaks-ranch, haskell-canyon, mint-canyon, potrero-canyon, ravenna, towsley-canyon
- 8 site page bodies; About and Permissions matter most
- 35 community write-ups
- 9 Find A Grave links
- Confirm the LA County GIS licence and add attribution before launch (now also needed for any seat boundaries taken from the County, 3 October)
- Story of Our Valley band has pseudo-text on the map; replacement requested from CD
- Perkins collection order: the Introduction should precede The Birth of Newhall
- Rudy Acosta's age at loss reads 20 on Leon's page; born May 2 1991, died March 19 2011, so he was 19. Worth telling Leon rather than changing silently.
- Research, not import: 20 articles with no publish date (Reynolds chapters carry no printed dateline), 19 war memorial narratives, 12 places with no establishment date, 15 casualties with no portrait.
- Done since, checked 3 October: the jerry-reynolds and dante-acosta bios (both full profiles now); RECORD-CHECKLIST.md already says legacyKey is required only if migrated.

### Older
Do not change the database until Nathan names the Place slugs to remove and whether to add missing community terms.

### What created the 52 empty titles

IDs 583 to 685 were created 2026-09-16 07:02:58 to 07:03:00 by `scripts/import/import_places_and_series.php` in local DDEV (commit `220809b`). That script set `$entry->title` from `places-candidates.json` and the hardcoded series list.

Craft still saved empty titles because both entry types have `hasTitleField: false` and `titleFormat: null` (`config/project/entryTypes/place--*.yaml`, `collection--*.yaml`). Craft 5 ignores `Entry->title` on save in that configuration. Same class of bug as the 2026-04-14 org titles (`titleFormat` / title field mismatch). Slugs, body, and ingest fields were stored; titles were not.

Fix when executing: enable the title field on Place and Collection (or set a real `titleFormat`), then write titles. Do not rely on `$entry->title` while `hasTitleField` is false.

Undo of the import itself: the 52 entries are only in local DDEV. A JSON snapshot of id, section, slug, and field values should be written before any delete so they can be recreated.

### 1. Convert 22 community Places to neighborhood categories

These Place slugs are communities, not sites:

acton, agua-dulce, bouquet-canyon, canyon-country, castaic, hasley-canyon, haskell-canyon, lebec, mentryville, mojave-desert, newhall, pico-canyon, piru, placerita-canyon, potrero-canyon, san-francisquito-canyon, saugus, soledad-canyon, tejon, towsley-canyon, val-verde, valencia

Neighborhood group `neighborhood` today:

| Slug | Term exists? |
| --- | --- |
| acton, agua-dulce, bouquet-canyon, canyon-country, castaic, hasley-canyon, lebec, newhall, pico-canyon, piru, placerita-canyon, san-francisquito-canyon, saugus, soledad-canyon, tejon, val-verde, valencia | yes |
| haskell-canyon | **missing** |
| mentryville | **missing** |
| mojave-desert | **missing** |
| potrero-canyon | **missing** |
| towsley-canyon | **missing** |

Do not add the five missing terms unless Nathan approves each one.

Relations on those Places now (move to Person/Org `neighborhood`, then clear Place relations):

| Place slug | Move |
| --- | --- |
| newhall | people: henry-mayo-newhall, arthur-b-perkins, jerry-reynolds, leon-worden, john-gifford → category `newhall` |
| saugus | people: henry-mayo-newhall → category `saugus` |
| placerita-canyon | people: francisco-lopez, abel-stearns → category `placerita-canyon` |
| pico-canyon | people: henry-clay-wiley, jerry-reynolds → category `pico-canyon`; drop relatedPlaces `mentryville` |
| mentryville | people: leon-worden. Cannot assign category until a `mentryville` term exists |

Keep existing Person-Org links (del Valle ranchos, Leon Worden → SCVHistory.com). Those are not Place records.

Then remove the 22 Place entries (hard delete only after snapshot). Community index 301s (`/scvhistory/acton.htm`) must not point at `/places/acton`. Target URL is still open.

**Undo:** restore from the pre-delete JSON snapshot (id, slug, title once fixed, body, legacyUrl, sourcePath, legacyCategory, neighborhood, placePeople, placeOrganizations, relatedPlaces). Re-save as Places. Re-apply placePeople from the snapshot. Category assignments on Persons can stay; they are correct even if the Place is restored.

### 2. Keep these 10 as Places: titles, SEO titles, community

Place entry type has no separate SEO title field. SEOmatic will use the entry title. Proposed title is the display name. Proposed SEO title is the same string until SEOmatic is configured.

| Slug | Proposed title | Proposed SEO title | Community (`neighborhood`) |
| --- | --- | --- | --- |
| vasquez-rocks | Vasquez Rocks | Vasquez Rocks | agua-dulce |
| beales-cut | Beale's Cut | Beale's Cut | newhall |
| ridge-route | Ridge Route | Ridge Route | castaic |
| saugus-speedway | Saugus Speedway | Saugus Speedway | saugus |
| melody-ranch | Melody Ranch | Melody Ranch | newhall |
| magic-mountain | Magic Mountain | Magic Mountain | valencia |
| harry-carey-ranch | Harry Carey Ranch | Harry Carey Ranch | saugus |
| heritage-junction | Heritage Junction | Heritage Junction | newhall |
| fort-tejon | Fort Tejon | Fort Tejon | tejon |
| estancia | Estancia | Estancia | valencia |

Ridge Route, Harry Carey Ranch, Estancia, and Melody Ranch sit near more than one community. Confirm before save.

Execution also requires turning `hasTitleField` on (or a non-empty titleFormat) so titles persist.

### 4. Collection titles from Jordy index pages

Copied from `<title>` or the visible series heading on the collection index. Not invented.

| Slug | Proposed title | Source |
| --- | --- | --- |
| perkins | SCVHistory.com \| The Story Of Our Valley by A.B. Perkins | `scvhistory/signal/perkins/index.html` `<title>` |
| reynolds | History of the Santa Clarita Valley by Jerry Reynolds | `scvhistory/signal/reynolds/index.html` `<title>` |
| worden | Selections From Leon Worden | `scvhistory/signal/worden/index.htm` `<title>` |
| boston | SCVHistory.com \| John Boston \| Santa Clarita History | `scvhistory/signal/boston/jbindex.htm` `<title>` |
| manzer | Darryl Manzer: 'Way Back When' in the Santa Clarita Valley | `scvhistory/signal/manzer/index.htm` `<title>` |
| newsmaker | SCV Newsmaker of the Week | Visible `<h2>` on `scvhistory/signal/newsmaker/index.htm`. The `<title>` is `SCVTV.com \| Local Television for Santa Clarita` (site chrome, not the series) |
| iraq | Abu Ghraib Prison Abuse Scandal Hits Home | `scvhistory/signal/iraq/index.htm` `<title>` |
| coins | NEEDS_TITLE | No index page under `scvhistory/signal/coins/` |
| otn-gazette | NEEDS_TITLE | `oldtownnewhall/index.htm` is a redirect with empty title. No `gazette/index.htm` |
| otn-patti | 'Open Book' - Santa Clarita Valley School Issues with Patti Rasmussen | `oldtownnewhall/patti/index.html` `<title>` |
| otn-pauline | Pauline Harte | `oldtownnewhall/pauline/index.htm` `<title>` |
| otn-rioux | Richard 'Doc' Rioux At Large | `oldtownnewhall/rioux/index.htm` `<title>` |
| otn-whyte | Black 'N' Whyte | `oldtownnewhall/whyte/index.html` `<title>` |

Same title-field bug as Places: titles will not stick until Collection `hasTitleField` is true.

## Deferred by decision

Parked on purpose, with the reason, so the next pass that opens the area finds them.

- **Rancho Camulos: merge the organization into the place by hand.** Organization #384 and place #631 hold two different bodies about the same rancho (the organization's 1,450 characters begin "Rancho Camulos is a historic rancho located along"; the place's begin "The del Valle family seat, and the westernmost") and two different images (#34 on the organization, #1195 on the place). convert_orgs_to_places.php carried everything else on 25 September and held the organization live. Nathan, 25 September: two bodies about one subject is a merge that needs a human read, not a script choice. When it is done, disable #384; the redirect goes in config/redirects.php.
- **Derived image links from the Walk of Western Stars.** Eleven photographs reach the SCV Chamber of Commerce #396 through derivedImageLinks, among them a Clint Walker lobby card (lw3689), Bob Hope in 'Alias Jesse James' and Montie Montana photographs, most likely because their captions name the Walk of Western Stars, which the Chamber ran. The records are not wrong; the derivation rule is. Revisit when the derived-links pass is next opened: an event the Chamber ran is not a link from every inductee's photograph to the Chamber.

## Open Questions

Leave these 7 Place entries untouched until Nathan decides:

- sleepy-valley
- lake-hughes
- santa-clarita
- lang
- rancho-san-francisco
- rancho-camulos
- tejon-ranch

Also still open: 301 target for community indexes; whether to add the five missing neighborhood terms; Ridge Route / Harry Carey Ranch / Estancia / Melody Ranch community assignment; coins and OTN Gazette collection titles.

## Deploy pipeline (blocking production)
- Updated 3 October 2026: deploy.yml was rewritten (commit 584a868) and is disabled. Its only trigger is workflow_dispatch and the job carries `if: false`, so pushing main deploys nothing. It now targets staging on templates-batch-9 and, when enabled, runs a backup, `composer install`, `craft up` and a cache clear (see its header).
- Staging is refreshed by hand: DEPLOY-RUNBOOK.md section 10. Check the server's branch (`git branch --show-current`) before choosing to push main or only the branch.
- To enable automatic deploys (Nathan only): add CLOUDWAYS_SSH_KEY, remove `if: false`, restore the push trigger, as the workflow header says.
- Production is still on the old build. Its first deploy must also copy web/uploads/archive-media/site (logos, seals, now tracked) and the Craft assets volume.

## Data gaps noted this session
- 20 communities have no polygon; coordinates come from set_community_coords.php (batch 4).
- Newhall, Saugus, Valencia, Canyon Country polygons are unincorporated fragments; sub-city boundaries task pending (city GIS, then ZCTA fallback).
- Community terms have no body, aliases, or type content yet.
- 12 places have no featured image until the legacy site images are pulled.
- GeoJSON licence marked NEEDS_VERIFICATION; confirm LA County GIS terms and add attribution before launch.
- militaryProfiles section has no template.
- Obituary body still carries WordPress artifacts; run clean_bodies.php again after adding obituaries to the field list.
