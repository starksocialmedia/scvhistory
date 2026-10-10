# Retype dry run: the 55 sure articles (10 October 2026)

Claude, read only. Nothing was moved, filled or written to the database, the templates or git. For Nathan to approve batch by batch, one word each.

Script: `scripts/import/retype_sure_55_2026_10_10.php` (dry run only; `$APPLY = false`, and setting it true stops the script, because the apply half is to be written after the batches are approved and tested on a fixture first). Full output: `storage/runtime/retype-sure-55/dry-run.txt`. Working probes beside it.

Rule (Nathan, 7 October): "a scanned newspaper page is an article that survives as an image. The scan is how we hold it, not what it is. Type follows the thing."

## What was read

- **The type audit** (`inventory/review/overnight-2026-10-08/type-audit-2026-10-08.md`): the 55 and what each scan or page prints. The script opens it and checks that every record in its plan is named there (all 55 are).
- **How earlier retypes were done:** `inventory/review/retype-dry-run-2026-10-07.md` ("Read this first" and the field mapping), `scripts/import/merge_and_move_2026_10_07.php` (the move of #2689 and #28295: same id, every field carried, old address to `config/redirects.php`), `held_as_and_facsimile_2026_10_07.php` (heldAs and the facsimile role), CHANGELOG 7 October (evening and night), DATA-ORGANIZATION.md, HANDOFF.md, TODO.md (the retype entries), the rules-in-force write-up of 8 October (titles, types, authorship), titles batches 1 to 4 and the title calls of 9 October.
- **Craft, read only:** the 55 records and every field with a value; the field layouts of the photograph, document and article types; every link that points at the 55 and whether its field accepts articles; slugs in the articles section; person and organization records for every byline and publication named; the scan assets each record holds (featuredImage, recordImages, recordDocuments, documentFiles), their assetRole and any other record using them.
- **Templates, read only:** `articles/_entry.twig`, `documents/_entry.twig`, `photographs/_entry.twig`, `_partials/prose.twig`, to see what the move changes on the page.
- **Do-not-touch checks:** none of the 55 is #2913 or #5377; none is in the 490 date decisions (`review/dates.json`, 432 rows, no match); none links to a fallen officer, a Hart or COC trustee draft or Boston's profile; no scan asset in the plan is on any person record, so the 22 enhanced portrait pairs are untouched; the two events the moved documents feed (the Saugus High School Shooting #31338, Santa Clarita Cityhood #31359) and the Placerita Gold Discovery #31370 are not in the disaster comparison (crime and non-disasters are out of it).
- **Not read again:** the scans and the legacy pages themselves. The type audit opened every one of them (by eye and OCR, 8 October); this dry run carries what it found, and the script says so before any number.

## Counts (from the dry run)

| | |
| --- | --- |
| Records in the plan | 55 |
| Already an article (not moved) | 1 (#2689) |
| Would move | 54 (30 photographs, 24 documents) |
| Field values carried as they are | 675 |
| Photograph relations mapped to the article's own field | 11 |
| Values with no home on the article type | 2 (both already in the body) |
| Fills into empty fields | 179: heldAs 54 (magazine pages 18, transcription only 14, clipping 11, web 11), originallyPublishedTitle 30, sourceLine 27, originalPublishDate 26, its EDTF 21, writtenBy 11 (all Leon Worden), publishedBy 10 |
| Non-empty field the plan would change | 1 (#28057, held for a word) |
| Titles that would change | 0 (one, #28293, is a call) |
| Scan assets on the 54 | 358; 113 on 25 records would take the role facsimile; none shared with another record |
| Links pointing at the 54 | 143, all on fields that already accept articles (124 derivedImageLinks, 16 event sources, 2 obituary companions, 1 source-fault record); none blocked |
| Disabled records (stay disabled) | 8 (the Los Angeles Times pieces) |
| Old addresses to redirect | 54; no slug clash in articles |
| Values over a field limit | 0 |

## How a move is done (the same in every batch)

- **Same id.** Every link to the record survives; all 143 sit on fields that accept articles (sourceDocuments and obitCompanions were widened on 7 October).
- **Carried by handle** where the article type has the field (675 values, including the photograph fields on the article's "Photograph details" tab, made for #2689 on 7 October).
- **Mapped** where the photograph or document field has an article counterpart, as the 7 October retype read proposed: photoPeople to subjectPerson, photoPlaces to depictsPlace, photoOrganizations to subjectOrganization, photoEvents to articleEvents, photoArticles to relatedArticles, documentFiles to recordDocuments (the article page prints recordDocuments; it would not print documentFiles). Every mapped target was checked against the target field's sources: all accepted.
- **Filled only where empty:** heldAs; for the photographs, originallyPublishedTitle (the current title, which titles batches 1 to 4 already set to the printed head), sourceLine and originalPublishDate as printed; the EDTF only for a plain year, month or day (a season or a two-month issue is left empty, not invented); writtenBy only where a printed byline names someone with a record (Leon Worden, #279, basis printed-byline); publishedBy only where the publisher is printed on the scan or is the page itself, and has a record (SCVHistory.com #378, Los Angeles Herald #390, The Signal #376).
- **Bylines with no record** stay in sourceLine as printed. No byline among the 55 except Leon Worden's names a person with a record (searched: Frost, Breihan, Simmons, Kelly, Parrish, Warren, Keller, Marquis, Smith, Libby, Taylor, Sperandeo, Suomisto, Holt, Peeples, Alvarenga, Murga, Banks, Gerber; "Kelly", "Warren" and "Taylor" match other people, RJ Kelly, Peter Warren and Sol Taylor). Lang (#18820) and Stearns (#309) have records but write letters, an open question (LETTERS below).
- **Publication from Leon's header only** is not written into sourceLine. Leon's header is already kept whole in catalogueCaption, which moves with the record. Writing his header into sourceLine would state a point read from his description as if read from the source.
- **The scans** stay where they are on the record (featuredImage, recordImages, recordDocuments) and take the asset role facsimile ("a copy of the record that holds it"), only where the role is empty and no other record uses the file. Pictures on born-digital pieces and the vigil photographs on #31326 are illustrations and are left.
- **Old address** to `config/redirects.php`, as on 7 October.

## Two things every moved photograph shares (not in any batch's word)

1. **The legacy category line would become the lead.** The article page promotes the first paragraph to the lead unless it is a [lines] block or carries a note. In 28 of the 30 photograph bodies the first paragraph is Leon's category line ("> NATURE > EARLY CALIFORNIA", "> INDIAN DUNES"). On the photograph page it prints as an ordinary first line; on the article page it would print as the lead, under the title. The stored body is Leon's and is not edited. The template would need to treat a line starting with ">" as it already treats a [lines] block. I made no template change. Recommendation: settle this before the first photograph batch is applied.
2. **The photograph details print on the article page** (CATALOGUE, PHOTO CODE, PHOTO DATE, CREDIT and the rest, as on #2689). Three web pieces carry a photoDate that is a parsing leftover, not a date: #4441 "| January 3, 2015.", #4931 "unknown, were acquired October 19, 2017.", #5589 "(The Signal, September 30, 1938),". They print today on the photograph pages; the move carries them as they are.

## The batches

### WEB: Leon Worden's born-digital pieces, his printed byline (7)

#2691, #4441, #4689, #5071, #5235, #5605, #5767.

What the move does: photograph to article; heldAs web; writtenBy Leon Worden with basis printed-byline, note "Printed byline "By Leon Worden". Read from the original page." (the byline is in the body Craft holds for all seven); sourceLine, originalPublishDate and its EDTF as the page prints them; publishedBy SCVHistory.com where the page names it (#2691, #4689, #5235, #5605, #5767; #4441 and #5071 print a date with no publication, so none); originallyPublishedTitle the title. Nothing lost. #4689 keeps "Principal research and documentation by Tricia Lemon Putnam" in sourceLine, not as an author. #5767 is the site's concise history essay (no image). #5071 is the Hart Museum, not the Hart district.

Not mechanical: nothing beyond the two shared notes.

### WEB-NOTES: the same kind, each with one thing to know (4)

#2721, #4931, #5399, #5589.

- **#2721**: a printed newsletter piece ("Heritage Junction Dispatch | January-February 2020"), held as Leon's web page, so heldAs web. publishedBy left empty: the Dispatch has no record, and that it is the Historical Society's newsletter is not printed. EDTF left empty (a two-month issue). Its body carries "Click image to enlarge | Download archival scan", which the photograph page drops by whole line and the article page would print.
- **#4931**: photoCredit "> TED LAMKIN COLLECTION" has no field on the article type and would be dropped. Nothing is lost: the same text is the first line of the body and the start of legacyCategory, both carried. photoPeople A.B. Perkins (#333) becomes subjectPerson.
- **#5399**: its recordImages include a 1980 Signal photo clipping (sg19801221perkins, "Photo by Tony Mason") and its featured image sg6002 is shared with photograph #27366. Both stay illustrations, not facsimiles. Perkins (#333) becomes subjectPerson.
- **#5589**: the photoDate leftover above; the piece is dated 3 May 2020, about a 1950 discovery.

### MAGAZINE: magazine and newsletter articles, publication and date printed on the pages Craft holds (13)

#4783, #4919, #4967, #5073, #5087, #5137, #5145, #5205, #5233, #5361, #5383, #5385, #5425.

What the move does: photograph to article; heldAs magazine pages; sourceLine with the printed byline, publication and date; originalPublishDate as printed; originallyPublishedTitle the title (already the printed head); every page scan and the PDF become facsimiles (90 files). No writtenBy (no byline names a person with a record) and no publishedBy (none of the magazines has a record). Mapped: #4919 and #5233 photoArticles to relatedArticles; #5073, #5145, #5385 Southern Pacific to subjectOrganization; #5087 its place to depictsPlace.

Not mechanical, each small:
- **#4967**: photoCredit has no home and would be dropped; its text is already in creditRaw and the body. No single byline (each member-elect writes in turn), so sourceLine names the magazine only.
- **EDTF left empty** for #4967 (Winter 1987-88), #5087 (Fall 1969), #5205 (June-July 1967), #5233 (July-August 1966).
- **#5233**: the date written is the printed "July-August 1966", not Leon's "August 1966" (his stays in catalogueCaption). Page 4 of the held set also prints the facing article, "Cattle and Kids" by S.E. (Ed) Bogart; the page is still a facsimile.
- **#5145**: the byline ("Text and photography by Randy Keller") is on the page after the head.
- **#5383**: "By the Staff of Dirt Bike" is kept as printed; no person.

### HEADER: articles whose publication, date or byline is not on the scan Craft holds (6)

#3049, #4269, #4691, #4909, #5245, #2963.

What the move does: as MAGAZINE (heldAs magazine pages; #2963 clipping), facsimile role on 17 files, originallyPublishedTitle the printed head. sourceLine carries only what is printed: #4691 "By Michael Frost."; #5245 "Car Life | November 1964."; #2963 "New-York Observer | Saturday, October 1, 1842." (printed, but on lw2181b to d on Reggie, not in Craft). #3049, #4269 and #4909 get no sourceLine or date at all.

Not mechanical:
- The publication for #3049 (The Land of Sunshine, December 1900), #4269 (Compass, Pacific Telephone, August 8, 1966), #4691 (Pageant, May 1957) and #4909 (TV Guide, 1957) is Leon's, in catalogueCaption only. These articles would show no publication of their own until a scan or a ruling supplies one (the audit's gap "where publication and date come from").
- **#5245**: "By Bill Libby" is only in Leon's header, so no byline is written; its page order is already a TODO item (8 October, afternoon).
- **#2963**: Craft holds only the masthead strip; the item and its dateline are on Reggie. As an article it would show a masthead and Leon's transcription.

### CLIPPING: newspaper items held as clippings in Craft (5)

#20087, #20090, #20093, #20096, #20107.

What the move does: document to article; heldAs clipping; the clipping (featured image) becomes a facsimile. Every other field carries as it is, including sourceLine, dates and publishedBy. Nothing filled beyond heldAs, nothing lost, no links in.

Not mechanical: nothing the move adds. Their sourceLines already carry paper and date from Leon's header ("News reports courtesy of Stan Walker"), not printed on the clippings (audit, "Read from a description", item 8). The move neither fixes nor worsens that. publishedBy is not added to #20093 or #20107 for the same reason.

### LETTERS: newspaper items that print a letter (2)

#26983, #28057.

What the move does: document to article; heldAs clipping; #26983's documentFiles (the clipping) to recordDocuments, as a facsimile; its source-fault record (#30537) and the Placerita Gold Discovery event's source link (#31370) stay. #28057: publishedBy Los Angeles Herald (the masthead is printed on the clipping, which is on Reggie, not in Craft).

Not mechanical:
- **writtenBy is not set on either.** John Lang (#18820) signs the letter in #28057 and Abel Stearns (#309) the one in #26983; both have records. Whether a letter's writer is its author is open (TODO, 6 October).
- **#28057's originallyPublishedTitle** holds "Los Angeles Herald, Wednesday, July 28, 1875, page 3", a citation, not a headline. The clipping prints "Death of a Monster Bear." (the title already says so since titles batch 1). Replacing a non-empty field needs your word; the plan reports it and keeps it. See the seventh-instance candidate below.

### CITYHOOD: clippings on Reggie, none in Craft (3)

#28291, #28293, #28310.

What the move does: document to article; heldAs clipping (the clipping is held on Reggie; Craft has no scan, so there is nothing to mark facsimile); the Cityhood event's source links stay (#28293, #28310). #28310: publishedBy The Signal (#376), from the printed folio "The Newhall Signal ... Sunday, January 4, 1987", as #28293 already has.

Not mechanical:
- **#28293** prints no headline (the page's label is "[Brief.]"). Its title "Cityhood forum announced, The Signal, January 11, 1987" is a description that still carries the suffix the documents rule of 7 October drops. Proposed: "Cityhood forum announced", kept as a description. A call, not part of the batch word.
- **#28310**: the title has a colon where the head prints "Cityhood Backers -- Who Are They?" (originallyPublishedTitle already holds the printed form). Left as it is.
- **#28291**: its editor's note "About the headline" is carried.

### SAUGUS: the Saugus High School shooting coverage, transcriptions (12)

#31308, #31310, #31314, #31316, #31318, #31320, #31322, #31324, #31326, #31328, #31330, #31332.

What the move does: document to article; heldAs transcription only; all twelve links from the event (#31338) stay; the eight Los Angeles Times pieces stay disabled (#31310, #31316, #31318, #31320, #31322, #31324, #31328, #31330); every content advisory in editorNotes is carried and the article page prints editorNotes at the top, as the document page does. #31326's vigil photographs are illustrations and are left.

Not mechanical: no writtenBy (no reporter has a record; whether any should is your open call in bylines-remaining-2026-10-08.md). The shooting is outside the disaster comparison, so the move does not touch it.

### TRANSCRIPTS: two other transcriptions (2)

#28305, #31412.

What the move does: document to article; heldAs transcription only. #28305: the two obituaries' companion links (#28045, #28047) and the Cityhood event's source link stay; no publishedBy (KHTS has no record). #31412: writtenBy Leon Worden with its printed-byline basis is already set and carried; publishedBy The Signal and SCVTV carried; it is the only member of the collection "Newsmaker of the Week" (#671), which would then hold one article.

Not mechanical: nothing.

## Not in any batch

- **#2689 Story of Sulphur Springs School.** Already an article since 7 October. Its heldAs is empty (the fourteen-day audit's TYPE-1); "web" would be a one-field fill, if you want it to ride with WEB.
- **The do-not-touch list:** none of the 55 is on it, and none was added.

None of the other 54 needs to come out: each is an article by what it is. The calls inside LETTERS, CITYHOOD and HEADER are about fields, not about the move.

## Seventh-instance candidate (written up, not fixed)

**#28057, originallyPublishedTitle.** The field holds "a headline the original printed" (DATA-MODEL, "Transcription and interpretation": a headline goes in only when the original printed it). It holds "Los Angeles Herald, Wednesday, July 28, 1875, page 3", which is Leon's citation for the clipping (his sourceLine reads "Los Angeles Herald, Wednesday, July 28, 1875, pg 3."). The clipping prints "Death of a Monster Bear." So the field states, as what the newspaper printed, a line taken from the description of the source. Titles batch 1 corrected the title from the scan on 7 October; the field beside it was not read against the scan. Not fixed.

Not a candidate, but recorded: #5589's photoDate "(The Signal, September 30, 1938)," and #4931's "unknown, were acquired October 19, 2017." are parsing leftovers from Leon's text, not points read from a description.

## What one word each would approve

| Word | Records | Moves | Also |
| --- | --- | --- | --- |
| WEB | 7 | photograph to article | heldAs, writtenBy Leon, sourceLine, date, publisher where printed |
| WEB-NOTES | 4 | photograph to article | as WEB; #4931's duplicate photoCredit dropped |
| MAGAZINE | 13 | photograph to article | heldAs, sourceLine, date, 90 facsimiles; #4967's duplicate photoCredit dropped |
| HEADER | 6 | photograph to article | heldAs, printed parts only, 17 facsimiles |
| CLIPPING | 5 | document to article | heldAs, 5 facsimiles |
| LETTERS | 2 | document to article | heldAs, 1 facsimile, #28057 publisher; no writer, #28057's field held |
| CITYHOOD | 3 | document to article | heldAs, #28310 publisher; #28293's title held |
| SAUGUS | 12 | document to article | heldAs; eight stay disabled |
| TRANSCRIPTS | 2 | document to article | heldAs |

Before any photograph batch (WEB, WEB-NOTES, MAGAZINE, HEADER): the category-line lead on the article page. Every batch adds its old addresses to config/redirects.php.
