# What changed in the last 48 hours, against the rules as they now stand (10 October 2026)

Claude, for Nathan. Read only. Nothing in the database, the templates, the rule documents or git was changed. Nothing was fixed. Another agent was the single database writer while this ran; what it saved is in section 1.4.

## 1. What was read

### 1.1 The rules

- **docs/DATA-MODEL.md** as generated on 9 October (generate_data_model.php), and its diff against the 7 October version (git diff 1dd6fef..1095412): "Generated and edited images", "Enhanced portraits: the standing pattern", "Images as objects", "Provenance and rights", "Who gets a person record", "Transcription and interpretation", the name policy, "Disasters and their consequences".
- **DATA-ORGANIZATION.md, PHILOSOPHY.md, docs/PROFILES.md**: read for the recurring-role test and the person-record rules. git log shows no change to any of them, or to PROFILES, since 6 October.
- **CHANGELOG.md**: every entry from 7 October to 9 October (evening).
- **TODO.md**: "Waiting on Nathan", 5 to 9 October.
- **ERRORLOG.md**: the open rows of 8 and 9 October, including the six counted instances of "read from a description".
- **inventory/review/overnight-2026-10-08/rules-in-force-2026-10-08.md**, whole.
- Review files, for wording and earlier classifications:
  - bylines-rule-and-names-2026-10-09.md (items 9 and 10);
  - fourteen-day-audit-2026-10-09.md (its classification of each failure);
  - titles-calls-2026-10-09.md;
  - titles-batch-2-2026-10-08.md (#5199);
  - portrait-changes-2026-10-08.md;
  - document-subject-organization-2026-10-08.md.
- Script headers, where a rule's wording or a value's origin lives only there:
  - pull_firefly_edits_2026_10_08.php ("Originals matched by eye on the side-by-side sheet");
  - restore_enhanced_pairs_2026_10_08.php;
  - check_generated_files.php (its HELD and RULED lists);
  - _generated_scan.php.

### 1.2 Craft, read only

- **The universe.** Every live element (not a draft, revision or trashed) whose `dateUpdated` is on or after **2026-10-08 05:29 UTC**, that is 7 October 22:29 Pacific, 48 hours before the run. It was read at 22:30 Pacific on 9 October.
  - The result: **1,555 entries, 2,582 assets, 0 categories: 4,137 elements**.
  - 2,877 of them (1,353 photograph entries and 1,524 new assets) are the photograph import of 7 October, 22:31 to 22:46 Pacific. That is inside 48 hours but before 8 October 00:00. They are kept in, and named where it matters.
  - Entries by section: 1,465 photographs, 51 persons, 20 documents, 13 articles, 2 obituaries, 2 war memorials, 1 office holding, 1 candidacy. Assets: all in archiveMedia (2,463 legacy-mirror, 104 outside, 13 commissioned, 1 legacy-wordpress, 1 with no kind).
  - Also in the window: one record trashed, Angela Marler #28312 (8 October, made a row on Nathan's word).
- **Scripts run.** All are read only; scratch copies are in storage/runtime/scratch/:
  - `changed48h_audit.php`: **audit_fourteen_days_2026_10_08.php**, copied unchanged except for its window and its output path (storage/runtime/scratch/changed48h-audit.json). Its IMG-1 and IMG-2 **open the files** (master on Reggie or held in the repo, stored copy, manifest) through _generated_scan.php. Every other rule in it reads Craft fields, not files.
  - `c48_extra.php`. It read:
    - the relations written in the window from live entries to assets;
    - the files of the 39 outside, commissioned or kind-less assets among them, through _generated_scan.php;
    - each entry's title in its last revision before the window, against its title now;
    - the persons and entries created in the window;
    - asset `source` sentences.
  - `c48_probe.php` to `c48_probe5.php`, `c48_late.php`: fields on named assets and entries (each says so in its reads header).
  - The standing checks: check_note_wording, check_checksums, check_pasted_labels, check_removed_claims, check_data_model, check_census_reads.
  - **Not run:** check_render as a whole, check_generated_files.php (it opens all 6,978 assets' files, over an hour; IMG-1 does the same for the window), and check_rendered_fields and check_rendered_bodies. **No page was fetched**, so "the edit is named on the page" is not tested here.
- **Files read directly:**
  - storage/runtime/generated-scan.json, the scanner's own cache, written at 16:22 on 9 October by build_withheld_media.php. Used for one count, the files the scanner could not open, which IMG-1 passes silently. The time is named where it is used.
  - config/withheld-media.json.
  - The manifest of #27396 (storage/runtime/manifests/urn-c2pa-8c2c59b2...).
  - The three County election-return PDFs, #26572, #26575 and #26578 (web/uploads/archive-media/elections/OER-*.pdf). They have no text layer. Pages 1 and 11 of the 2016 file and page 1 of the 2020 and 2022 files were rendered and looked at.

### 1.3 Not read

- No scan was compared with a title, so the 37 retitles rest on the batch files' readings.
- No portrait was looked at by eye.
- Reggie was opened only through the scanner.

### 1.4 The other writer, during the run

- **Seen saving from 22:56 to 22:58 Pacific, after the universe was read:**
  - Jenkins #20226;
  - two new legacy-mirror assets, #38564 and #38565;
  - two new entries, #38566 (document) and #38568 (obituary);
  - McGrath's holding #28455 (howEnded now "unknown", termEnd kept);
  - footnotes on persons #18869, #16356, #18714, #311, #317, #333, #15874, #291, #307, #287.
- They are **outside the counts below**, except the six persons that were already in the window.
- Read at 23:23 (c48_late.php):
  - #38566 and #38568 each carry the default blank footnote row, the NOTE-4 pattern below.
  - Their two new assets have no `source` sentence and no licence.
  - The new script `jenkins_certificate_obituary_2026_10_10.php` **fails check_census_reads** on four fields it reads about files (sourceChecksum, legacySourcePath, provenanceKind, filename) without listing them.
- All of that is work in progress, noted here and not judged.

---

## 2. The rules that changed between 7 and 9 October

The rules changed in four rounds:

1. 8 October, evening: no generated image anywhere.
2. 8 October, late: the Firefly-edit rule.
3. 8 October, later that evening: the 17 restored and the originals put back.
4. 9 October: the enhancement rule, the reconciliation, publisher edits and opening the file.

Two title rules of 7 October were applied in the window. The table gives each rule, oldest wording first.

| # | Rule | Old wording (date, where) | New wording (date, where) |
|---|---|---|---|
| R1 | **Generated images** | Banners "are illustrations made for the site, not photographs of anything. They are page decoration, labelled on the image" (1 October, DATA-MODEL) | "**No generated image of a real person, place or event, anywhere in the archive, decoration included** ... A label does not cure it, and the banner rule does not license it" (Nathan, 8 October, evening; DATA-MODEL) |
| R2 | **The Firefly-edit rule** | "A crop, an upscale, a removed bystander, a cleaned background or a cropped edge filled in is ordinary archival practice" (1 October, DATA-MODEL, **still there**) | "Any portrait where Firefly filled, removed, cleaned, or made an edit the credential does not describe comes off the record"; "The 14 with no original held: those come off too ... A record with no portrait is honest" (8 October, late; CHANGELOG, pull_firefly_edits_2026_10_08.php). Then, the same evening: "Restore the 17 to the enhanced pair" (restore_enhanced_pairs_2026_10_08.php). Then **reconciled** (9 October, DATA-MODEL): "read through the enhancement rule ... judged by whether it is published as a pair, with its original held and the edit named on the page, and by what its own file records, not by which tool made it ... the 8 October removals of edits with no original held stand" |
| R3 | **The enhancement rule** | The enhanced pair: "the enhanced image becomes the portrait, the original stays on the record as a related image ... **Where the original is not held, `enhancedFrom` stays empty and the caption says so**" (6 October, DATA-MODEL, **still there**) | "An enhancement is a visible change to a photograph the archive holds, made by us, published alongside the original with the edit named on the page ... An edit that is not published as a pair, or **whose original we do not hold**, or that contains a generated element rather than an alteration of what the photograph shows, is not an enhancement and does not go on a record ... `enhancedBy` is the archive's own editor" (Nathan, 9 October, DATA-MODEL) |
| R4 | **The text_to_image step** | Couts: "If his credential records text_to_image he is in that class whoever made him" (Nathan, 9 October, morning; ERRORLOG) | Couts back on Nathan's word once his chain was read. The fourteen and the seven are open, and check_generated_files.php lists them by name until he rules (9 October, evening; DATA-MODEL, TODO) |
| R5 | **Publisher-edited images** | None before 9 October | "Edited by its publisher before it reached us ... It is published as it was published. Where the edit can be seen, in the file's metadata, its credential or the picture itself, the asset says so in `rightsNote` or `source`" (Nathan, 9 October, morning). Narrowed the same afternoon: "**Outside publishers only**: Leon Worden's preparation of his own scans for the original site is not a publisher edit" (DATA-MODEL) |
| R6 | **Reading the content credential** | "The content-credentials scanner reports; it never blocks" (1 October, DATA-MODEL, **still there** in "Provenance and rights" and "Enhanced portraits": "does not block an import") | "A supplied file is read for its download record and its content credential before it replaces or becomes anything" (8 October, ERRORLOG). Then: "The scanner half of that is superseded (8 and 9 October 2026): a file's own bytes are read before it goes on a record" and "Whether a step is a generated element is read from the file's credential step by step, never from the record's summary of it" (9 October, DATA-MODEL); "no outside file goes on a record before its own bytes are read for a credential"; "A check of a file's contents now has to open the file" (9 October, ERRORLOG) |
| R7 | **Originals back on** | (8 October, evening) "originals go straight back on, unedited" (CHANGELOG) | Overtaken by R6 the next morning, after Cooper's original turned out to be generated |
| R8 | **Census reads** | A lesson in ERRORLOG (8 October, afternoon) | "Turn that into a check": every script made since 8 October calls _reads first; check_census_reads.php in check_render (8 October, continued; AGENTS.md) |
| R9 | **Titles** (set 7 October, applied in the window) | Evening: "the title is the headline the page prints. Leon's catalogue caption goes in its own field and is kept, every word" (`catalogueCaption`; retitle_2026_10_07.php) | Night: "newspapers and magazines take what the scan prints, with Leon's page headline kept in its own field" (`legacyHeadline`); ephemera take the printed cover or masthead title; "The agency name belongs in fields, not after a colon in the title" (titles_batch_1_2026_10_07.php, titles-batch-1). Batch 2 applied 8 October; batches 3 and 4 applied 9 October; four calls and the program dates held. **In no rule document** |
| R10 | **Bylines / writtenBy** | The authorship basis on every link (7 October; DATA-MODEL holds the dropdown only). Census rule (b), "two or more pieces", proposed 5 October | Unchanged in the window. On 9 October the write-up found rule (b) **never adopted**, and the recurring-role test governs (bylines-rule-and-names-2026-10-09.md). Nathan has not ruled |
| R11 | **The recurring-role test** | PHILOSOPHY 2.3 (16 September): "a meaningful, recurring role in SCV history"; DATA-MODEL (1 October): "Keep: held office" | Unchanged. PROFILES (6 October): "An office held alone is a row" still contradicts DATA-MODEL (rules-in-force, contradiction 9) |
| R12 | **subjectOrganization on documents** | None | "A document that does not say whose it is names its body" (8 October, afternoon; CHANGELOG) |
| R13 | **The enhanced-pair rule's date** | "5 October" in public notes (8 October) | Corrected to 6 October on all 17 (9 October) |
| R14 | **Withheld media** | Any asset had a /media page | A generated or generatively edited image on no live record answers 404 (config/withheld-media.json, 9 October; DEPLOY-RUNBOOK step 7) |

**Three of the new rules sit in DATA-MODEL beside older text that says the opposite:**

- R2: "ordinary archival practice" against the Firefly rule;
- R3: "Where the original is not held, `enhancedFrom` stays empty" against "whose original we do not hold ... does not go on a record";
- R6: "it never blocks" against "read before it goes on a record".

The new text is the later, and DATA-MODEL is generated, so the old sentences need a change to generate_data_model.php. **For Nathan.**

---

## 3. Counts per rule

"Complies" means the current state meets the rule as now written. "Held" are the records named in the brief as off limits (the fourteen, Couts, the seven, #2913, #5377 and the others listed); they are listed where they fail and are not proposed for change. Each "Cannot tell" row says why it cannot.

### 3.1 Images

| Rule | Applies | Complies | Does not | Held | Cannot tell | Check used |
|---|---:|---:|---:|---:|---:|---|
| R1 No generated image on a record (files opened) | 2,547 assets on live records | 2,532 | 0 | 15 | 0 | IMG-1 (live run). The scanner cache of 16:22 shows 0 of the 2,547 unreadable and 2,546 read at the master |
| R1 Generated files on no record are withheld | 31 window assets on no record (1 generated, 14 text-prompt, 16 generative edit) | 31 | 0 | 0 | 0 | config/withheld-media.json against the cache. The /media 404s were not re-fetched; the 9 October evening run had 35 of 35 |
| R3 The enhancement rule (pair, original on the record, edit named, no generated element, editor recorded) | 19 person records with an edited portrait | 0 | 2 | 15 | 2 | IMG-2 and IMG-3 |
| R2 The Firefly-edit rule, as reconciled | the same 19 | (as R3) | (as R3) | | | Judged through R3, as DATA-MODEL says |
| What an edited asset records (1 and 4 October) | 45 edited assets | 34 | 2 | 7 | 2 rule or check faults | IMG-4 |
| A replaced portrait stays as a related image (3 October), unless the 8 October rules took it off | 48 records | 47 (29 by the exemption) | 0 | 0 | 1 check fault | IMG-5 |
| R5 Publisher-edited, outside publishers only | 21 outside-published files on records | 0 | 2 | 0 | 19 | Fields read. The by-eye pass on official portraits is not done (TODO) |
| R5 Leon's own files are not publisher edits | 2,463 legacy-mirror assets | 2,463 | 0 | 0 | 0 | None says "publisher" in rightsNote or source (c48_probe) |
| R6 The file is read, and the read recorded, before it goes on a record | 39 outside, commissioned or kind-less assets on records | 38 in state | 0 in state | 15 (on the files' content, R1) | 1 | Scanner opened all 39 tonight. 19 record the read in contentCredentials, 19 in rightsNote (9 October), 1 nowhere. IMG-7, a field test, fails 16 of them: a check fault |
| R6, in order | the 18 originals of 8 October | 0 | 18 changed under R7, before any read | 0 | 0 | Placed 8 October evening, read 9 October overnight (CHANGELOG). Cooper's was generated |
| Checksum at receipt (3 October) | 948 assets created in the window | 948 | 0 | 0 | 0 | IMG-6; check_checksums passes (4,280 match their master) |
| Licence on outside images ("an empty licence is not permission") | 78 | 31 | 0 | 0 | 47 | IMG-9: the rule does not forbid showing a file whose licence is "unknown" (the 9 October audit's reading) |
| R13 The enhanced-pair date | every window element | all | 0 | 0 | 0 | No text says "enhanced-pair rule of 5 October" |

### 3.2 Titles, authorship, people, notes

| Rule | Applies | Complies | Does not | Held | Cannot tell | Check used |
|---|---:|---:|---:|---:|---:|---|
| R9 The catalogue entry kept in `catalogueCaption` | 1,465 photographs | 1,463 | 0 | 2 (#5377, #2913) | 0 | TTL-2 |
| R9 Leon's page headline kept in `legacyHeadline` when retitled | 5 articles and documents | 5 | 0 | 0 | 0 | TTL-3 |
| R9 The title prints what the scan prints | 1,498 photographs, articles and documents | 37 retitled in the window, each read against its scan in the batch files (not re-read here) | 0 | 1 (#21936, a title call) | 1,460 | Not tested against scans. The titles census compared only 124 legible scans of printed matter |
| R9 The agency in fields, not after a colon | 20 documents | 12 | 8 | 0 | 0 | TTL-6 passes all 20, a check fault: it matches only the exact title of an organization record, and these say "Santa Clarita Valley Water Agency" or "Santa Clarita City Council" where the records are "Santa Clarita Valley Water" and "The City of Santa Clarita" |
| R9 No "(Author, Publication, Date)" suffix | 20 documents | 20 | 0 | 0 | 0 | TTL-1 |
| R10 Every author link has a basis; a basis other than printed says from what | 15, and 3 | 15, and 3 | 0 | 0 | 0 | AUTH-1, AUTH-2, AUTH-3 |
| Author wins (2 October) | 15 | 14 | 0 | 0 | 1 (#28281) | AUTH-4: a check fault; the person template already resolves it |
| R11 The recurring-role test | 0 persons created; 1 person name met as a co-byline (Kristopher Daams, #38531) | 1 | 0 | 0 | 0 | No record made for him |
| R11 Person-record rules (no loser-only record; office alone a row) | 51 persons | 51 | 0 | 0 | 0 | PER-1, PER-2 |
| Name policy, aliases, living birth year | 51 | 51 | 0 | 0 | 0 | TTL-4, PER-3, PER-4, PER-5 |
| R12 subjectOrganization on election documents | 14 | 14 by field | 0 | 0 | 0 | MISC-3. But 3 say the wrong thing: section 6 |
| Notes for a reader (3 October) | all window entries and asset sources | all, by the check | 0 | 15 | 12 | check_note_wording passes. 27 asset sources on records narrate the archive's own removal and restore ("Taken off its record on 8 October 2026 ... put back the same day on Nathan's word"; "Restored on 8 October 2026 to this file: on 6 October it had been replaced ..."). That is the tension rules-in-force flagged; not ruled |
| No-source notes record their search | 6 notes | 6 | 0 | 0 | 0 | NOTE-1 |
| Wikipedia a lead, not a citation | 2 to read (#323 Couts, #21584 Scofield) | 0 | 0 | 0 | 2 | NOTE-2, to read |
| No blank footnote row | 1,555 | 1,520 | 0 | 0 | 35 | NOTE-4: the Table field's default row (the 9 October audit's finding), not a row builder |
| R8 Census reads | 42 scripts made since the rule | 38 | 4 rows, 1 script | 0 | 0 | check_census_reads. The failing script is the other writer's, saved during this run |
| Other standing checks | all | all | 0 | 0 | 0 | check_pasted_labels, check_removed_claims and check_data_model pass; recordTags and the User-Agent rule pass (MISC-1, SCR-1) |

The rules with nothing in the window to apply to are content advisories, fallen officers, consequences, heldAs, current marks, the WordPress body and the ellipsis rule (EV-1 to EV-3, TYPE-1, IMG-8, MISC-2, NOTE-3).

---

## 4. Every non-complying record

### 4.1 Real

| Id | Title | Rule | What fails |
|---|---|---|---|
| #18663, asset #38450 | Randy Wicks; randywicks1995_karzinphoto_large-enhanced.jpg | R3 (9 October: "`enhancedBy` is the archive's own editor"); IMG-3, IMG-4 | `enhancedBy` and `enhancedDate` empty. The asset's own source says "Enhanced by Nathan Imhoff in Adobe Firefly on October 6, 2026". The fields were never filled when the asset was split on 8 October |
| #21944, asset #38452 | Bob Kellar; sc1310-enhanced.jpg | the same | The same. Also licence and rightsNote empty, where Wicks's say "unknown" and "No permission ... established" |
| #23091, asset #38460 | Jason Gibbs; jason-gibbs-city-2023.png | R5, publisher-edited (9 October) | The City's composite (sitter on a shared council-chamber backdrop, seen in the picture: ERRORLOG, 9 October) is not disclosed. rightsNote says only "no software named; no camera named; no content credential". The rule requires the asset to say what the picture shows "where the edit can be seen ... in the picture itself". TODO lists the note as still to write |
| #23093, asset #38462 | Patsy Ayala; patsy-ayala-city-2024.png | the same | The same |
| #26576, #26579, #26581, #21918, #21921, #21924, #21927, #21930 | The election documents whose titles end ": Santa Clarita Valley Water Agency", ": Santa Clarita City Council" or ": Santa Clarita City Council, District 1" | R9 (7 October: "The agency name belongs in fields, not after a colon in the title") | The body follows a colon in the title. Since 8 October it is also in subjectOrganization, so taking it out loses nothing (the 8 October proposal said so and sent them to the titles batches, which have not reached them). For three of these the body named is not what the document is about: section 6 |

### 4.2 Held: listed only, not proposed for change

- **The fourteen and Couts**: R1 and R3 (a `text_to_image` step in the file).
  - Lynch #30219 (#31404), Richman #29316 (#31447), Scofield #21584 (#27383), Lyon #20224 (#31387), Pederson #18726 (#31395), Tom Mix #18702 (#31408), Mulholland #16432 (#31391), Darcy #16140 (#31449), Carey #15919 (#31472), Boyer #15808 (#31427), Ruth Newhall #15477 (#31398), Rioux #2585 (#31423), Perkins #333 (#27381), Manly #321 (#31406); Couts #323 (#27387).
  - Each has its original linked and on the record, and the edit named. Each fails only on the step, which is Nathan's open question.
- **The seven** (Connie Worden #16418, Klajic #15874, Brathwaite #28703, Nadeau #18869, Frew II #31354, Gelcich #16439, Jenkins #20226): their text-prompt portraits are off every record, and the originals are the portraits. They comply in state; their question is open.
- **#5377 and #2913** (TTL-2: no catalogue entry). **#21936** (title call 2).
- **The Hart district and Runner edits**, all on no record, with `enhancedBy` empty: #29330 Steve Knight, #29326 Sharon Runner, #29124 George Runner, #28978 Messina, #28976 Jensen, #28974 Moore, #28972 Wilson. TODO asks who made them (IMG-4).

### 4.3 Rule contradictions and check faults

| Id | Title | Rule | What it is |
|---|---|---|---|
| asset #28816 | tiburcio-vasquez-1874-upscaled.jpg (on no record, withheld) | IMG-4, "links its original or says none is held" | `enhancedFrom` was cleared on purpose by the 8 October pull. That is keep-both (4 October) against the pulls (rules-in-force, contradiction 7) |
| asset #38464 | bill-cooper-campaign-2026.png (on no record, withheld) | IMG-4, IMG-5 | Generated, not edited, so the edit fields do not apply. Off his record by R1. Check faults. **But** its `source` still reads "the photograph beside his biography", a generated image called a photograph, where the file says otherwise (see section 6) |
| #28281 | A Brief History of the Push for Self-Government in Santa Clarita | AUTH-4 | Connie Worden as author and subject. The display rule already resolves it |
| 16 assets: #38525 (Boston's mug) and #38460 to #38490 (even ids, Cooper's #38464 excepted) | the outside originals and the mug | IMG-7 | The file was read and recorded, in rightsNote, which the test does not look at |
| 47 assets | outside images on records | IMG-9 | Licence "unknown" or empty, which the rule does not forbid |
| 35 entries | the Boston pieces, the election documents and others | NOTE-4 | A blank default footnote row from the field's own default |

### 4.4 Cannot tell

- **Leon Worden #279 (asset #27396) and Frémont #307 (#28814)** under R3. Each passes as a pair and its file has no `text_to_image` step. But Leon's records "Generate Fill" on the edges, and Frémont's chain begins "created (Adobe Firefly, creative upsampler; trainedAlgorithmicMedia)". Whether either is "a generated element rather than an alteration" is the same by-eye question as the fourteen. Frémont's painted-in corners are already logged.
- **The enlargements, Wicks #38450 and Kellar #38452**, under R3's "generated element" clause. They were kept on 8 October as "plain enlargements", a decision and not a rule. Their own method says "Generative steps can add detail the photograph did not record". López (#31474) is the same case but is outside the window.
- **19 outside originals and files on records**, for R5, until the by-eye pass on official portrait sets is done.
- **1,460 titles** not read against a scan.
- **#31938** (Boston crop) for R6. It is a crop made from a legacy file and records no read. Its master was opened tonight: nothing found. It was placed on 6 October, before R6; the relation was rewritten in the window.

---

## 5. Changed under an older rule, where the newer one says otherwise

1. **The 18 originals put back on 8 October, evening (R7), were placed before any file was read.**
   - R6 now says a file's bytes are read before it goes on a record.
   - They were read the next night, and Cooper's (#26946, asset #38464) was an OpenAI image: placed under R7, removed under R1.
   - The other 17 now comply in state: no credential, and the read is recorded in rightsNote. They did not comply in order.
2. **The 8 October removals "for no original held" stand by R2's reconciled wording, but their ground has changed for about ten.**
   - The edit's own source photograph was found and put on that same evening, for Gibbs, Ayala, Cooper, Ferdman, Steve Knight, Moore, Wiley, Rasmussen and Miranda among them (portrait-changes-2026-10-08.md, last section).
   - So "whose original we do not hold" is no longer true of the edit, though the edit stays off.
   - Nothing needs doing unless Nathan wants any of those edits back as pairs. Recorded so it is not read as settled on its stated ground.
3. **The 17 put back on 8 October were put back on Nathan's word**, against R2 as then written.
   - R3 (9 October) now makes their status turn on the file: two pass (Leon Worden, Frémont, both cannot tell by eye), and the fourteen and Couts are held.
   - The restore also set Couts on a label sort, which was the sixth instance.
4. **Wicks and Kellar** were restructured on 8 October to "the López pattern", which then meant original linked and in related images. R3 (9 October) adds "`enhancedBy` is the archive's own editor", and their `enhancedBy` is empty: section 4.1.
5. **Gibbs and Ayala** went on as unedited originals on 8 October. R5 (9 October) now asks that the City's composite be said on the asset; it is not.
6. **DATA-MODEL itself** still carries the older wording beside R2, R3 and R6 (section 2). A reader of the document finds both.
7. **The election documents' colon titles** were written before 7 October and are now out of line with R9. They were deferred to the titles batches on 8 October and have not been reached.
8. **Marler** (#28312, trashed 8 October) was made a row under PROFILES (6 October), which DATA-MODEL's "Keep: held office" (1 October) would not do. This is the standing contradiction 9, not new.

---

## 6. Read from a description: candidate seventh instance

**The County's whole-election returns recorded as one water agency's document.**

**What the records say:**
- **#26573**, "Final Official Election Returns, November 08, 2016 General Election": subjectOrganization **Castaic Lake Water Agency** (#26563). The 8 October proposal (document-subject-organization-2026-10-08.md) calls it "the County's certified count for the Castaic Lake Water Agency board contests".
- **#26576**, "Statement of Votes Cast and Official Election Returns, General Election, November 3, 2020: Santa Clarita Valley Water Agency", and **#26579**, the same for 8 November 2022: subjectOrganization **Santa Clarita Valley Water** (#402).
- The page shows this as CONCERNS and emits it in JSON-LD as schema.org `about` (CHANGELOG, 8 October, afternoon).

**What the documents show**, opened tonight (OER-3496-11082016.pdf, OER-4193-11032020.pdf, OER-4300-11082022.pdf; no text layer, pages rendered and read):
- Each is the County Registrar-Recorder's return for the **whole general election in Los Angeles County**.
- The 2016 file's page 1 opens with President and Vice President and the state propositions. Its page 11 ends with the Water Replenishment District of Southern California, the West Basin Municipal Water District, West Covina City Measure H and the West Covina Unified bond.
- The 2020 file is "Page 1 of 21" and opens with President, the Board of Supervisors' names and the state measures.
- The 2022 file is "Page 1 of 32" and opens with Governor, Lieutenant Governor, Secretary of State and the other statewide offices.
- The water agency is one contest among hundreds in each.

**Where the value came from.** The proposal says so itself: "Each value is computed from the organizations on the election records that link to the document, not typed by hand". The body was taken from what cites the document (a description of it: a link from the election records) and not from the document. The title suffixes ": Santa Clarita Valley Water Agency" on #26576 and #26579 say the same thing in the title. The proposal also called #26573 the count "for the Castaic Lake Water Agency board contests". It is the count for every contest.

**Not checked:** #26581 (the 2024 Statement of Votes Cast) has no file attached, so what it is cannot be read. #25146 (CEDA) carries six bodies, which is nearer the truth for a statewide compilation, but it was set the same way.

**Not fixed.** Two ways it could be read, both Nathan's:
- these documents concern the general election, not a body, so subjectOrganization should be empty, or name the County as issuer;
- the field means "the body this archive cites it for", and its label should say that.

---

## 7. Files

- This report: /Users/nathanimhoff/scvhistory/inventory/review/changed-48h-vs-rules-2026-10-10.md
- Audit output, every failing id: /Users/nathanimhoff/scvhistory/storage/runtime/scratch/changed48h-audit.json, and its log, changed48h-audit.log
- The extra reads: /Users/nathanimhoff/scvhistory/storage/runtime/scratch/c48-extra.json
- Scratch scripts: /Users/nathanimhoff/scvhistory/storage/runtime/scratch/changed48h_audit.php, c48_extra.php, c48_probe.php to c48_probe5.php, c48_late.php
