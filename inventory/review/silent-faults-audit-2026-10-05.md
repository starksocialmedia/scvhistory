# Silent faults audit, 5 October 2026

Claude, read-only. This audit followed two faults in `import_elections.php`:

- `'carried' => false` was written for every ballot measure.
- `$docByKey` returned null, and the footnote then printed the bare key "summary.".

Nathan asked for a wider check:

- **A. Hard-coded values** written where the value should come from the source data.
- **B. Silent fallbacks**: a lookup fails and the script writes something that looks plausible instead of failing loudly.

## Method and scope

- **Scope.** There are 549 scripts in `scripts/import/`. 386 of them write to Craft. Another 21 feed writers or write through a shared importer: the JSON builders (`parse_*`, `extract_*`, `build_valley_legislators_data.py`) and the three series imports that call `_series_import.php`. Those 407 were audited. The other 142 only read: `export_*`, `audit_*`, `check_*`, `survey_*`, `_probe_*` and `report_*`.
- **How the work was split.** The 407 were split into four groups. Three groups were audited by parallel forked agents, one by me. Every suspect was checked against the live DDEV data with read-only queries; the probe files are in `storage/runtime/sf_*.php` and `b1_*` to `b3_*`.
- **Generic data sweep.** It ran across every entry (about 4,000), on all PlainText fields, all Table cells and all titles. It looked for:
  - footnote or note rows under 25 characters, or matching `^[a-z0-9_-]+\.?$`;
  - "Array", "null", "#" with no number, empty parentheses, a lone period, `, ,` and doubled spaces;
  - titles that look like keys or filenames;
  - legacyUrl and sourcePath values that do not match legacyKey, or that are shared between records;
  - footnote rows with text but no number or source.
- **Closure scan.** A scan over all 549 PHP scripts looked for the root-cause shape of the `$docByKey` fault: an arrow function that captures an array by value before the apply phase fills it.
- **What the checks found:**
  - `import_elections.php:85` is the only real case of the closure fault.
  - The sweep found no footnote with text but no source or number.
  - It found no "Array" or "null" in any note.
  - It found no title equal to a key, apart from the media filenames in finding 6.
- **Coverage limit.** The single-record profile and record scripts (`build_*_profile.php` and similar) set hand-written values for one named record, from sources named in their doc blocks. They were checked by grep for constants and fallbacks, not read line by line.

## Summary

| # | Pattern | File:line | Field | Records affected | Severity |
|---|---|---|---|---|---|
| 1 | B | import_elections.php:85, 190-192 | elections footnotes, sourceDocuments | 5 elections | Visible on the site |
| 2 | B (template, blank data rows) | templates/_partials/record/footnotes.twig:62-64 | footnotes | 757 pages (1,254 blank rows) | Visible on the site |
| 3 | B | parse_war_memorials.php:76-101, via populate_war_memorials.php:84-105 | wmNarrative, body | 8 war memorials | Visible on the site |
| 4 | B | apply_extracted_captions.php:137-150, 216-262 | photoCaptionExt, asset title, alt | 13 assets on 13 photograph pages | Visible on the site |
| 5 | A | attach_place_images.php:51 (same shape at find_place_images.php:82) | asset alt | 6 assets | Visible on the site (alt text) |
| 6 | B (template) | templates/media/_entry.twig:65, 133 | /media heading, meta description | 2,979 assets | Visible on the site |
| 7 | B | inventory/canonical_entities.json (same shape at backfill_provenance.php:204-215) | legacyUrl, legacyKey, sourcePath | 7 places | Visible on the site |
| 8 | A | import_mentry_sources.php:153 | webmasterNoteBottom | 2 documents | Visible on the site |
| 9 | B (template) | templates/collections/_column.twig:141 | publication line | 6 column pages, 4 of them unverified | Visible on the site |
| 10 | B | build_valley_legislators_data.py:24-32, via record_valley_legislators_2026_10_04.php:119-120 | officeHoldings footnotes | 1 holding | Data only |
| 11 | A | import_kellar_portrait.php:66, import_weste_portrait.php:53 and 4 more | legacySourcePath, photoSourceCode | 17 assets | Data only |
| 12 | B | import_wp_content.php:216-222 | relations | 4 relation keys, 3 targets | Data only |
| 13 | A (gap) | set_collection_authors.php | writtenBy | about 57 articles with no author | Data only |
| 14 | A | add_footnote_source_column.php | footnote source | 5 rows on 1 photograph | Data only |
| 15 | crawl artifact | import_perkins.php:256 | recordDates label | 1 row | Data only |
| 16 | A | import_elections.php:196, parse_election_results.py:77, 92, 114, 150 | electionKind | 0 (data agrees) | Latent |
| 17 | A | import_ceda_school_boards.php:254, import_water_boards.php:276 | electionKind | 0 (data agrees) | Latent |
| 18 | B | create_coc_elections_2026_10_05.php:39, 46-48 | seatsUp, outcome, electionDateEdtf | 0 | Latent |
| 19 | B | create_fallen_officers_2026_10_05.php:53 | foValleyTie | 0 | Latent |
| 20 | B | create_saugus_2019_sources_2026_10_05.php:50 | editorNotes | 0 | Latent |
| 21 | B | derive_board_holdings.php:134, 403 | footnotes | 0 | Latent |
| 22 | A/B | import_remaining_war_memorials.py:81, .php:43, 50 | wmConflict, title, body | 0 | Latent |
| 23 | B | import_legacy_images.php:731 | [image:N] tokens | 0 | Latent |
| 24 | B | _series_import.php:336 | title | 0 | Latent |
| 25 | B | fix_no_source_notes_2026_10_03.php:52, fix_own_text_figures_2026_10_04.php:58, fix_del_valle_burial_and_magdalena_2026_10_04.php:56, apply_audit_decisions_2026_10_04.php:65 | footnote source | 0 | Latent |
| 26 | B (template) | templates/_partials/record/footnotes.twig:62 | note attribution | 0 | Latent |
| 27 | B | apply_confirmed_dates.php:31-46 | recordDates confirmed | 0 | Latent |
| 28 | B | 13 scripts (list below) | entry relations | 0 known | Latent |
| 29 | B | create_records_from_review.php:600, 620 | article links, aliases | 0 | Latent |

Totals:

- **Pattern A:** 8 findings. 3 are visible on the site (5, 8 and part of 2's root cause), 3 are data only and 2 are latent.
- **Pattern B:** 21 findings. 6 are visible on the site, 3 are data only and 12 are latent.

The earlier Measure U fault is not counted: it was fixed today by `fix_council_election_sources_2026_10_05.php`.

## Visible on the site

### 1. Five council elections still cite a bare document key

**Where:** `import_elections.php:85`, `:190-192`.

**The fault.** `$docByKey = fn($key) => $docs[...] ?? null` is an arrow function, so it copied `$docs` when it was defined, at line 85. The nine documents were created at line 173, in the apply phase, after that copy was taken. Every lookup therefore returned null, and line 192 printed the bare key.

**What the earlier fix covered.** `fix_council_election_sources_2026_10_05.php` repaired the fourteen elections that cited "summary." and "r2014.". It matches only those two keys. These five were missed:

| Record | Footnote 1 reads |
|---|---|
| #22304 (November 8, 2016) | "sov2016: grand total, text layer; names, page 1 image." |
| #22328 (November 6, 2018) | "sov2018: grand totals, page images 9 and 18." |
| #22360 (November 3, 2020) | "sov2020: grand total, Exhibit A, page 7 image." |
| #22380 (November 8, 2022) | "sov2022: grand total, page 10 image." |
| #22400 (November 5, 2024, District 1) | "sov2024: grand total, text layer; names, page 1." |

Each of the five links only the CEDA compilation (#25146) as its source document. None links the County statement of votes it actually rests on.

**Fix in the data.** Extend the fix script to map each sov key to its document:

| Key | File |
|---|---|
| sov2016 | 2016StatementofVotesCast-4.pdf |
| sov2018 | LACountyFinalVoteCount-3.pdf |
| sov2020 | Final-Election-Canvass-Res-2.pdf |
| sov2022 | Final-Certficate-of-Canvas-1.pdf |
| sov2024 | 10880.pdf |

Find each document by sourcePath. Rewrite footnote 1 as "County of Los Angeles, Registrar-Recorder/County Clerk, "<title>" (archive document #N): <reading>." and add the document to sourceDocuments.

**Fix in the script.** Replace the arrow function with a lookup that reads `$docs` at call time, either `function ($k) use (&$docs)` or a direct index. Throw when the key does not resolve, and never print `$e['doc']`.

**Not affected.** The Kellar and Boydston holdings (#22418, #22420) read "The resolution is document #21936." correctly, because that line indexes `$docs` directly.

### 2. Empty "Editor's Notes" credited to Leon Worden on 757 pages

**Where:** `templates/_partials/record/footnotes.twig:62-64`. The cause is in the data: scripts create entries without setting `footnotes`.

**The data.** When a script creates an entry and does not set a Table field, Craft stores the field's default row, `{"col1":null,"col2":null,"col3":null}`. The archive holds 1,254 such blank footnote rows. Every footnote row that does have text has both a number and a source.

**The template.** The partial does not drop rows whose note is empty. A source other than "author" counts as the editor's. So a blank row renders:

- the "EDITOR'S NOTES" heading;
- the byline "Notes by Leon Worden, SCVHistory.com";
- an empty item "1." with a back-link.

A fork fetched every enabled record that holds only blank rows (1,008). 757 show the empty section; I confirmed the markup on `/articles/mr-brenners-lincoln-a-profile`.

| Section | Pages showing it |
|---|---|
| articles | 660 |
| documents | 36 |
| places | 36 |
| photographs | 18 |
| obituaries | 6 |
| collections | 1 (#12135 Mentryville) |

Persons and organizations render through another path and are not affected. Examples: #15454, #28310, #29406, #29676, #28051.

**Fix in the template.** Skip rows whose trimmed note is empty, before counting `notes`. This is one filter and fixes all 757 pages.

**Fix in the data.** Optionally, clear the blank rows.

**Fix in the scripts.** New-entry builders should set Table fields they do not fill to `[]`.

The other Table fields (editorNotes, ballotMeasures, recordDates) carry the same blank default rows. Their templates already filter on content: elections, for example, filters measures on `letter`.

### 3. Eight war memorial narratives cut to their first line

**Where:** `parse_war_memorials.php:76-101`, feeding `populate_war_memorials.php:84-105`.

**The fault.** The parser keeps only the first line after "Narrative:". Its multi-line fallback never runs, because the label was already found. The cut text is written as though it were the whole narrative.

**Four end mid-sentence:**

| Record | Length or ending |
|---|---|
| #570 John Amos Ward | 59 characters, ends "fought with the 110th Infantry Regiment," |
| #552 Acuna | ends "a year after his death," |
| #546 Prosser | ends "about 100 yards from the" |
| #516 Contreras | ends "engaging the enemy on" |

**Four lose their later paragraphs:** #566 Bartlett, #564 Redmond, #532 Flores-Mejia and #514 Kenaston.

**Fix in the data.** Re-extract the eight narratives from legacyHtml, reading up to the next label or the index block.

**Fix in the script.** Read up to the next "Label:" line. Refuse a narrative that ends without closing punctuation.

### 4. A byline written as a caption, title and alt on 13 images

**Where:** `apply_extracted_captions.php:137-150`, `:216-262`.

**The fault.** `lw-features-images.json` carries "Biography by Friends of Hart Park" as the caption of 13 images. The cleaner strips navigation text but not bylines, so the byline became each image's caption, title and alt text.

**Affected assets:** #13046, #13083, #13086, #13090, #13104, #13120, #13271, #13302, #13306, #13405, #13535, #13733 and #14847. They are the plates of 13 photograph pages. For example, #3787 "Mural (Detail) at Santa Clarita City Hall" prints the caption "Biography by Friends of Hart Park".

**Fix in the data.** Clear the caption and alt on the 13, and restore their titles.

**Fix in the script.** Reject byline and credit phrases ("Biography by", "Photo by", "Courtesy"). Flag any caption that five or more files share. Repeated gallery headings such as "AMERICAN HOTEL" (21 files) and "Front" (43) are real legacy text and were not judged here.

### 5. Licence text used as alt text on six place images

**Where:** `attach_place_images.php:51`.

**The fault.** Every image in the loop gets the alt text "Public domain. Wikimedia Commons." The doc block justifies the licence, but not its use as alt text.

**Affected assets:** #1187, #1189, #1191, #1193, #1195 and #1197. They are the featured images of Fort Tejon, Harry Carey Ranch, Lake Hughes, Lang, Rancho Camulos and Ridge Route. `find_place_images.php:82` has the same shape, but nothing it wrote is in the data now.

**Fix in the data.** Set the alt text from the caption (already in the asset title).

**Fix in the script.** Keep licence text in the licence and source fields only.

### 6. /media pages headed by a filename

**Where:** `templates/media/_entry.twig:65`, `:133`.

**The fault.** When an asset has no caption, the page heading and meta description fall back to the asset title. For 2,979 of 4,417 assets the title is just the filename, such as "Sc9020", "Lw2054" or "Bw8702 orig" (e.g. #28065, #28062, #29678). The meta description then reads "Sc9020, an image in the SCVHistory.com archive".

**Fix in the template.** When the title equals the filename, use the title of the record that uses the image. Otherwise, show the legacy code labelled as a code.

### 7. Seven places whose original-page link goes to an unrelated photograph page

**The fault.** Seven places carry the legacyUrl, legacyKey and sourcePath of a photograph record, and their "original page" link goes there. The general sweep also found these as legacyUrl groups shared between a place and a photograph:

| Place | Key | Photograph |
|---|---|---|
| #631 Rancho Camulos | lw3903 | #5675, a liquor tax certificate |
| #635 Ridge Route | lw3795 | #5653 |
| #613 Six Flags Magic Mountain | lw3790 | #5647 |
| #605 Heritage Junction | lw3789 | #5645 |
| #659 Vasquez Rocks | lw3730 | #5563 |
| #597 Fort Tejon | lw3698 | #5543 |
| #643 Saugus Speedway | lw3271 | #5095 |

**Where it comes from.** `inventory/canonical_entities.json` gives these places a page that only mentions them as their own legacy page. `backfill_provenance.php:204-215` has the same shape: it takes the first scvhistory.com link in a WordPress post as the record's own page, or matches on title alone. Which import copied the values into Craft was not identified.

**Fix in the data.** Clear the three fields on the seven.

**Fix in the scripts.** Never treat a page that mentions a record, or the first link found, as that record's own page. Leave the field empty instead.

**Other shared legacyUrls, probably deliberate:**

- several documents from one legacy page: #20087, #20090, #20093 and #20096 share sw_petermentre.htm;
- four fallen officers from one incident page;
- photograph #3179 and article #865;
- organization #396 and group #950 share lw2102.

Nathan may want to confirm the #396 and #950 pair.

### 8. An invented credit line on two documents

**Where:** `import_mentry_sources.php:153`.

**The fault.** The script writes "News reports courtesy of Stan Walker" into webmasterNoteBottom for every `sw_` page whose source has no framing text. The doc block does not mention it. The extract (`inventory/legacy/mentry-sources.json`) prints "Walker" only once, on sw_herald111186 (#20087), where the credit is genuine.

**Affected:** documents #20090 (sw_herald031799) and #20093 (sw_lat031799). Both show a credit their extracted sources do not print. The Jordy drive was not mounted, so the originals were not checked.

**Fix in the data.** Clear the note on #20090 and #20093, unless the originals print it.

**Fix in the script.** Delete the line, or derive the credit from the source.

### 9. Column collections print "The Signal" from no data

**Where:** `templates/collections/_column.twig:141`, `{{ F.sourceLine ?: 'The Signal' }}`.

**The fault.** No column collection has a sourceLine, so every column with dated pieces prints "The Signal" as its publication. Six pages show it:

- `/collections/worden` (#665);
- `/collections/coins` (#673);
- the four Old Town Newhall runs: `otn-patti` (#679), `otn-pauline` (#681), `otn-rioux` (#683) and `otn-whyte` (#685).

The Old Town Newhall pieces come from the Old Town Newhall minisite, and none of their articles records a publication. Whether they ran in The Signal is not established in the data. Worden and Making Cents probably did run in The Signal, but that is not recorded either.

**Fix in the template.** Print nothing when sourceLine is empty.

**Fix in the data.** Nathan sets sourceLine on each column from the evidence.

## Data only

### 10. A legislator citation with a raw folder slug and an empty date

**Where:** `build_valley_legislators_data.py:24-32` (`cite()`), written by `record_valley_legislators_2026_10_04.php:119-120`.

**The fault.** The folder-name regex `(.*?)-(?:nov|november)-(\d+)-(\d{4})` matches only November elections. On a miss, the code writes the raw folder name and an empty date.

**Affected:** officeHolding #29353 (Pilar Schiavo, State Assembly), footnote 3, reads "California Secretary of State, Statement of Vote, primary-election-june-2-2026, , State Assembly, https://...". It is the only case in the generated JSON and in Craft. Holdings have no page of their own, and the person page carries its own correct citation, so it does not show on the site.

**Also worth a look.** The term filter lets a serving term cite a later election: the June 2026 primary is cited on a term that began in 2022.

**Fix in the data.** Rewrite the citation as "Statement of Vote, Primary Election, June 2, 2026, State Assembly, URL".

**Fix in the script.** Parse primary and special folder names. Exit when the kind or date cannot be read.

### 11. Portrait scripts write the source path without its code

**Where:** `import_kellar_portrait.php:66`, `import_weste_portrait.php:53`, and the same in `build_acosta_profile`, `build_council_profiles_batch1`, `build_profiles_batch3` and `import_first_council_photos_2026_10_04`.

**The fault.** These scripts write legacySourcePath as `gif/x.jpg`, with no leading slash, and set no photoSourceCode. The mirror importers write `/gif/x.jpg` together with the code.

**Affected:** 17 assets (e.g. #28065, #28062, #27886, #29678, #27405). Their legacy-code line is blank, and code-based plate matching cannot find them.

**Fix.** Fill the code from the filename, add the slash, and have the scripts set both fields.

### 12. Relations dropped without a report

**Where:** `import_wp_content.php:216-222`.

**The fault.** Relation keys with no mapping, and targets that do not resolve, are skipped silently.

**What was dropped:**

- Four keys, one target each: collection.related_groups, military_profile.mp_parents, organization.articles_published and person.articles_written.
- Three targets that did not resolve:
  - chapter-10-solitary-hiker to de-anza-expedition;
  - pedro-fages to catalonian-volunteers;
  - juan-bautista-de-anza to de-anza-expedition.

**Fix.** List each one and refuse until it is accounted for. Nathan decides whether the seven links belong in Craft.

### 13. Articles imported after the author pass have no author

**Where:** `set_collection_authors.php`.

**The fault.** The script gives every article in the Reynolds (#871) and Perkins (#873) collections the collection's author. That was correct where it ran: the Leon pieces #817, #1418 and #1432 carry Leon. But articles imported after it ran have no writtenBy at all:

- Reynolds chapters 23 to 71, the Epilogue, Notes, Bibliography and #2181 (about 52 records, #2071 to #2181);
- Perkins #1442, #1446, #1448, #1450 and #1454.

The collection's author still shows on the page. No wrong author was found.

**Fix.** Rerun the author pass, or make the series importer set writtenBy.

### 14. Photograph footnotes labelled editor

**Where:** `add_footnote_source_column.php`.

**The fault.** The script set every existing footnote row to "editor", though its own doc block names "webmaster" for notes that came from webmaster fields.

**Affected:** photograph #5551 has 5 such rows (York Dispatch; Shields 2013:53-54). Leon wrote them, so "editor" is defensible.

**Fix.** Nathan to confirm.

### 15. A date label with an empty quotation

**Where:** `import_perkins.php:256`.

**The fault.** Article #1428, recordDates row 29, has the label "...writing in the Los Angeles Star, describes it as: .". The crawl dropped the block quote, so this is not a script fallback. It is the only row of this shape. It is unconfirmed, so it does not show on the site.

**Fix.** Correct the label by hand.

## Latent: the fault exists but has not fired

### 16 and 17. electionKind hard-coded "general"

**Council elections.** `import_elections.php:196` ignores the `kind` that elections.json carries. `parse_election_results.py` hard-codes it per document type, including 1987, which was the incorporation election consolidated with the County election.

**School and water boards.** `import_ceda_school_boards.php:254` and `import_water_boards.php:276` give every contest "general". CEDA has no election-type column.

**Data check.** All 19 council, 309 CEDA and the water contests fall on regular dates, so nothing is contradicted today.

**Fix.** Write `$e['kind']` and refuse an unknown kind. For CEDA, either derive the kind from the date or state in the doc block why every contest from this source is general.

### 18. College district elections

**Where:** `create_coc_elections_2026_10_05.php:39`, `:46-48`.

**Silent defaults:**

- a missing seat count becomes 1;
- a candidate with no `elected` key becomes "not elected";
- a date that does not parse leaves the date field empty.

**Data check.** All 30 contests are complete. One minor point: `votesAsPrinted` is produced by `number_format()`, not taken from the print.

**Fix.** Refuse when any of the three is missing.

### 19. Fallen officers' valley tie

**Where:** `create_fallen_officers_2026_10_05.php:53`.

**The fault.** A missing valley tie would silently assert "killed here".

**Data check.** All 14 draft records state their tie, and the stored values match the draft. All 14 are disabled.

**Fix.** Refuse a missing tie.

### 20. Saugus 2019 sources

**Where:** `create_saugus_2019_sources_2026_10_05.php:50`.

**The fault.** If the family's letter is not found, the note would print "archive document #" followed by an empty value.

**Data check.** No record contains it.

**Fix.** Throw if the letter is not found.

### 21. School board holdings

**Where:** `derive_board_holdings.php:134` and `:403`.

**The faults:**

- Line 134: an empty URL leaves empty parentheses in the Hart Area 2 2022 citation. That text has since been replaced; #28584 and #28837 are correct.
- Line 403: a missing year prints "stood as the incumbent in ;". No such text exists.

**Fix.** Throw on a missing URL or year.

### 22. War memorial extractor

**Where:** `import_remaining_war_memorials.py:81`; the PHP importer at `:43` and `:50`.

**The faults:**

- The Python extractor hard-codes the conflict as "World War II" and cuts every body to 500 characters.
- The PHP importer falls back to the slug for a missing title, and to "World War II" for a missing conflict.

**Data check.** All 11 source files are ww2_, and every body has since been replaced. Records #560 to #580 were checked.

**Fix.** Derive the conflict from the filename prefix, as `import_warmemorial.php` does. Refuse a missing title, and never truncate.

### 23. Guessed image token numbers

**Where:** `import_legacy_images.php:731`.

**The fault.** An image with no asset id gets a guessed token number, which could point at a different picture.

**Data check.** All 31 bodies with tokens are currently in range, with no duplicates.

**Fix.** Skip the token when there is no asset id.

### 24. Filename as title in the series importer

**Where:** `_series_import.php:336`.

**The fault.** The script uses the filename when a page has no title, and publishes the record enabled.

**Data check.** No Coins, Worden or Old Town Newhall article has a filename as its title.

**Fix.** Refuse a page with no title.

### 25. Footnote rows re-saved with an editorial default

**Where:** `fix_no_source_notes_2026_10_03.php:52`, `fix_own_text_figures_2026_10_04.php:58`, `fix_del_valle_burial_and_magdalena_2026_10_04.php:56` and `apply_audit_decisions_2026_10_04.php:65`.

**The fault.** Each re-saves footnotes with `'source' => (string)($r['source'] ?? 'editorial-2026')`. A row with no source would be re-credited to the archive without anyone noticing.

**Data check.** No row with text has an empty source today.

**Fix.** Keep the value as read, and refuse on empty.

### 26. Other source options rendered as Leon's notes

**Where:** `templates/_partials/record/footnotes.twig:62`.

**The fault.** Rows marked "webmaster" or "In the source document" would render under "Notes by Leon Worden".

**Data check.** No row uses either value yet.

**Fix.** Give each source its own label, or refuse the unused options.

### 27. Date confirmations matched by position

**Where:** `apply_confirmed_dates.php:31-46`.

**The fault.** The script matches rows by position and skips unmatched rows without a report. After a deletion the positions shift, so a second run with the same review file would confirm or delete the wrong rows.

**Fix.** Match on the printed text plus the date.

### 28. Relation rewrites drop disabled targets

**The fault.** Each of these scripts reads a relation with `->field->ids()` and writes the result back with an addition or removal. `ids()` returns only enabled targets, so the write silently unlinks every disabled record.

**Scripts:**

| Script | Line(s) | Relation field |
|---|---|---|
| fold_and_retire_records.php | 64, 71 | subjectPerson |
| fix_country_fair_and_folder_attribution_2026_10_04.php | 65, 66, 70, 73 | partOfCollection, writtenBy, articlesInCollection |
| apply_article_links.php | 66 | relatedArticles |
| apply_place_links.php | 63 | relatedPlaces |
| _series_import.php | 330 | articlesInCollection |
| attach_perkins_collection.php | 145 | articlesInCollection |
| restore_california_battalion.php | 95, 139 | groupPersons |
| import_ruiz_census.php | 803, 816 | spouseOf, childOf |
| create_scv_water_event.php | 66 | eventOrganizations |
| record_city_commissions_2026_10_04.php | 127 | personEvents |
| create_redevelopment_agency_2026_10_04.php | 54 | subjectOrganization |
| fix_gibbs_current_term.php | 44 | holdingBody, holdingDistrict, holdingOffice |
| water_chain_and_edited_marks_2026_10_04.php, fix_sb634_two_bodies_2026_10_04.php | 91-92, 63 | precededBy |

Several profile builders do the same with `personOrganizations` and `roles`.

**Data check.** The archive holds only 15 disabled entries today: 1 article and the 14 draft fallen officers. A dropped link leaves no trace, so this cannot be checked after the fact. With so few disabled records, the risk has been small.

**Fix.** Use `->status(null)->ids()` in every read-modify-write. Most scripts already do.

### 29. Review records: links and aliases skipped without a report

**Where:** `create_records_from_review.php:600`, `:620`.

**The faults:**

- Line 600: when an article lacks the link field, the article link is skipped without a failure entry.
- Line 620: when a merge target lacks the alias field, the alias is skipped the same way.

**Data check.** All 74 surviving "created from review" records have incoming relations.

**Fix.** Add both cases to `$failed`.

## Checked and clean

Scripts with a finding above are not repeated here.

### Elections, holdings and public bodies

- **Schema:** add_civic_role_2026_10_05, add_district_plan_field_2026_10_04, add_election_body, add_elections_schema, add_event_fallen_officers_2026_10_05, add_fallen_officers_section_2026_10_05, add_office_holding, add_roles_schema.
- **apply_*:** apply_cancelled_2013_2016, apply_double_counting_audit_2026_10_04, apply_hart_1993_1994_2026_10_05, apply_hart_2022_area2_and_smyth, apply_term_endings_2026_10_04.
- **build_*:** build_coc_trustee_profiles_2026_10_05, build_council_profiles, build_council_profiles_batch1, build_hart_trustee_profiles_2026_10_05, build_nadeau_and_gutzeit_runs, build_smyth_careers.
- **council_*:** council_ceda, council_winners_and_terms.
- **create_*:** create_aadusd_2026_10_04, create_coc_trustee_terms_2026_10_05, create_coc_trustees_2026_10_05, create_county_and_sheriff_2026_10_04, create_office_gap_holdings, create_officer_agencies_2026_10_05, create_public_bodies_2026_10_05, create_redevelopment_agency_2026_10_04, create_state_federal_bodies.
- **enable_*:** enable_public_bodies_2026_10_05.
- **fix_*:** fix_city_seats_and_body_headers_2026_10_04, fix_cooper_term_start, fix_council_election_sources_2026_10_05, fix_council_transition_2026_10_04, fix_gibbs_cooper_terms, fix_gibbs_current_term, fix_gibbs_district3_appointed_2026_10_04, fix_gibbs_note_2026_10_04, fix_sole_candidate_terms_2026_10_04, fix_winkler_removal_2026_10_04.
- **import_*:** import_commission_portraits_2026_10_04, import_first_council_photos_2026_10_04.
- **Parsers:** parse_ceda.py, parse_county_svc.py.
- **record_*:** record_city_commissions_2026_10_04, record_city_managers_2026_10_04, record_city_mayors_2026_10_04, record_district_boards_2026_10_04, record_hart_superintendents_and_board_2026_10_04, record_three_commissioners_2026_10_04.
- **Roles and fields:** rebuild_roles_vocabulary, repair_role_titles, retire_board_fields, retitle_nadeau_grandson.
- **Other:** newhall_redevelopment_committee_2026_10_04, public_bodies_contrasts_2026_10_05, publish_board_ladder, saugus_2019_entities_2026_10_05, schools_church_sheriff_2026_10_04, smyth_family_and_roles.

### Images, assets and war memorials

- **War memorials:** import_warmemorial; import_wm_high_schools, whose "retrospective" evidence is documented.
- **Memorial sourcing:** source_wm_pilot, source_wm_korea, source_wm_terror, source_wm_vietnam, source_wm_ww2_batch1, source_wm_ww2_batch2, source_wm_remaining_2026_10_04. All 591 fact-source rows cite footnotes that exist.
- **Image import and linking:** rebuild_photo_relations, link_images_to_records, import_mirror_images, import_wp_media, recover_wp_landmarks, reimport_photograph_bodies.
- **Provenance:** backfill_asset_provenance, fix_asset_provenance, simplify_media_provenance, fix_provenance_dates, set_photo_legacy_category, set_asset_provenance.
- **Attaching images:** attach_held_photo_images, attach_perkins_collection, attach_wiley_obituary_clippings, set_wm_lead_images, repoint_to_larger, set_person_focal_points.
- **Record moves and marking:** clear_memorial_non_portraits_2026_10_04, move_war_memorial_persons, mark_external_people.
- **War memorial notes:** note_wm_home_outside_valley, note_wm_no_likeness, note_wm_searched_not_found.
- **Marks:** import_current_marks, import_marks_2026_10_04, import_school_marks_2026_10_04, replace_newhall_mark_2026_10_04, water_marks_from_originals_2026_10_04.
- **Portraits:** record_missed_credentials_2026_10_04, import_edited_portraits, import_hart_portrait, import_hart_portraits_2026_10_04, import_smith_portrait, import_smyth_portrait, import_trunkey_portrait, import_ahuja_portrait, import_portraits_2026_10_04b, import_city_hall_csun_photos, swap_fremont_vasquez_portraits, swap_smyth_portrait.
- **Corrections:** sharpen_csow_corrections, fix_schools_and_csow_image, fix_portrait_provenance_2026_10_03, create_castaic_high_and_mark_2026_10_04, add_caption_corrections.
- **Schema only:** the add_* field scripts in this group.

### Content, notes, dates and legacy imports

- **Schema and quality:** _quality_pass and the add_* schema scripts.
- **apply_* and assign_*:** apply_era_decisions, apply_multi_eras, apply_relations, apply_review_fixes_0929, assign_person_eras.
- **Cleaning and conversion:** build_perkins_profile, classify_person_bodies, clean_bodies, clean_legacy_bodies, clean_notes, clean_text_mechanical, clear_submission_emails, convert_mfn_footnotes, convert_note_footnotes.
- **Record creation:** create_mentryville_collection, create_newhall_court_2026_10_05, create_vasquez_document.
- **Dates, extracts and stubs:** decide_record_dates, detach_ai_collection_bands, extract_mentry_sources.py, extract_newhall_sources.py, fill_collection_stub_bodies, fill_place_stub_bodies.
- **Fix scripts:** every fix_* not named above.
- **Importers:**
  - hold_newhall_elementary_original_2026_10_04, import_connie_worden_sources, import_fixes, import_loose_pages, import_lw_features;
  - import_nces_enrolment, import_newhall_sources, import_persons_pilot, import_places_and_series, import_reynolds;
  - import_ruiz_census, apart from item 28;
  - import_coins, import_old_town_newhall, import_worden_columns, apart from item 24.
- **Linking, notes and collections:**
  - link_acosta_family, link_mentions_in_prose, merge_duplicate_collections, move_leads_off_notes_2026_10_05, move_notes_to_ourselves_2026_10_05;
  - normalise_legacy_urls, note_duzick_johnson, propose_record_dates, record_newhall_elementary_edit, relabel_person_bodies;
  - rename_collection_kind_book_to_series, reword_public_notes, rewrite_connie_worden_profile;
  - set_collection_authors_2, set_collection_frozen, set_collection_kinds, set_mentryville_members;
  - settle_family_relations, setup_pages_section, setup_record_dates, setup_tags, source_fault_mentry_birth_2026_10_05;
  - strip_split_base_aliases, title_collections, trim_living_birth_dates;
  - update_gutzeit_profile, update_mentry_profile, wire_place_relations, withhold_wordpress_bodies.

### Persons, organizations, places and settlement

These were read in full:

- **apply_wikidata_matches:** it never overwrites a value and reports every conflict.
- **set_evidence_levels:** it audits every certified value against a held certificate.
- **create_hart_early_members:** every value is derived from the roster and documented.
- **create_records_from_review:** clean apart from item 29.

These were checked by grep for constants and fallbacks:

- **Schema and fields:** add_affiliations, add_aliases_2026_10_03, add_authority_fields, add_bioguide_field, add_derived_evidence, add_edtf_fields, add_education, add_event_evidence_field, add_fact_sources_field, add_family_to_wm, add_gnis_field, add_haunted_fields, add_imdb_field, add_jurisdiction_fields, add_license_cc_by_sa_2, add_missing_places, add_named_for_fields, add_north_differences, add_org_identifier_fields, add_org_level_and_kinds_2026_10_04, add_org_taxonomy_fields, add_person_aliases, add_person_evidence_fields, add_place_type_field, add_place_type_settlement, add_place_website_field, add_school_fields, add_school_structure, add_source_checksum_field.
- **apply_*:** apply_entity_merges, apply_org_backfill.
- **Profiles and single records:** build_acosta_profile, build_ahuja_profile, build_antonio_del_valle_profile, build_ayala_profile, build_blank_records_1 to 4, build_california_star_oil_works, build_city_of_santa_clarita, build_cooper_profile, build_couts_profile, build_csun_record, build_de_la_cerda_profile, build_ferdman_profile, build_hart_profile, build_kellar_profile, build_lackey_profile_2026_10_04, build_mckeon_profile, build_pico_profile, build_profiles_batch2 to 5, build_schiavo_profile_2026_10_04, build_scofield_profile, build_smith_profile, build_smyth_profiles, build_stearns_profile, build_trunkey_profile, build_valladares_profile_2026_10_04, build_walters_profile, build_weste_profile, build_worden_profile.
- **Conversion and creation:** convert_orgs_to_places, convert_places_to_communities, create_gutzeit_record, create_ncwd_record, create_pico_1860s_records, create_scv_water_event (apart from item 28).
- **Removal, disabling and enabling:** delete_impossible_claims, disable_bowers_cave, enable_ranchos_missions_retire_shells_2026_10_04.
- **Extractors:** extract_hart_roster.py, extract_saugus_high_2019_sources.py.
- **Folding and merging:** fold_and_retire_records (apart from item 28), match_authorities, merge_cooper, merge_duplicate_persons, merge_held_names, merge_person_duplicates, merge_rudy, move_orgs_to_places, organize_orgs_places, paragraph_bios.
- **Records, relabels and retirement:** record_external_qids, record_trunkey_measure_v_2026_10_04, relabel_ygnacio_del_valle, remove_insignificant_persons, remove_john_wayne, restore_california_battalion (apart from item 28), retire_305_add_chico_lopez, retitle_hart_high.
- **Revisions:** retype_decisions_by_canon, revise_inherited_ties, revise_profiles_batch4.
- **set_*:** set_acosta_family, set_city_seal_rights, set_community_coords, set_koscielny_death, set_namesakes, set_org_communities, set_org_coords, set_place_coords.
- **Settlement:** settle_borrowed_ties, settle_decisions, settle_duzick_johnson_2026_10_05, settle_stale_decisions.
- **Setup:** setup_communities, setup_person_aliases.
- **Sources:** source_corrections, source_fault_stearns_mint_2026_10_05.
- **Trims and widening:** trim_wilk_to_public_life, widen_couts_profile.

### Templates

These defaults were checked against the data and are honest or unreached:

- `offices.twig` "At large": 1 holding has a district but no seatLabel (#26969), and that case does not reach the default.
- `offices.twig` "Office": no holding lacks an office.
- `affiliations.twig` "Connected": no affiliation lacks both a title and a kind.
- `valley-districts.twig` "No member recorded".
- `elections/_entry.twig` "uncited".
