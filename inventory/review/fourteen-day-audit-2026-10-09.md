# Claude, for Nathan: the fourteen-day audit re-run, 9 October 2026

Your item 13: "Re-run the fourteen-day audit after the rule reconciliation and tell me what still fails and whether each failure is real."

Read only. Nothing was saved to the database and nothing was fixed.

## What was read, before any number

- **The audit's output**, as written by the re-run at 16:37 today: storage/runtime/overnight-audit/fourteen-day-audit.json (every failing id), and its log, storage/runtime/scratch/audit-1009.log. I did not run the audit again.
- **The audit script**, scripts/import/audit_fourteen_days_2026_10_08.php, as text, rule by rule, to see what each test reads. Since last night, IMG-1 and IMG-2 open the files through _generated_scan.php. Every other rule still reads Craft fields, not files.
- **Last night's write-up**, inventory/review/overnight-2026-10-08/fourteen-day-audit-2026-10-08.md. Its ids were compared with tonight's JSON, rule by rule. Last night's JSON was overwritten by the re-run, so the comparison is against the ids printed in that write-up.
- **The rules**:
  - inventory/review/overnight-2026-10-08/rules-in-force-2026-10-08.md;
  - docs/DATA-MODEL.md as regenerated today: "Generated and edited images", "Who gets a person record", "Provenance and rights", "Content advisories" and the name policy;
  - docs/PROFILES.md, "An office held alone";
  - the ERRORLOG rows the audit cites.
- **Today's context**: inventory/review/text-to-image-step-2026-10-09.md, CHANGELOG's three 9 October entries, and TODO's "Waiting on Nathan" sections.
- **Two scan caches, as files**:
  - storage/runtime/generated-scan.json, written at 16:22 by build_withheld_media.php through the same scanner the audit uses: class, files read and usedBy for all 6,978 assets.
  - storage/runtime/overnight/cred-scan.json, the overnight credential scan.
- **Craft, read-only**, through four scratch scripts in storage/runtime/scratch/ (audit1009_spot.php to audit1009_spot4.php). They read:
  - rightsNote, contentCredentials, source, enhancedBy, enhancedDate and enhancedFrom on the assets named below;
  - footnotes on #38529 and #38531;
  - legacyUrl and catalogueCaption on the six TTL-2 records;
  - heldAs, aliases and search names on the TYPE-1, TTL-4, PER-4 and PER-5 records;
  - the relations of eleven WordPress-era portraits.
- **Files, read directly**:
  - inventory/source-searches.json (72 keys);
  - templates/persons/_entry.twig and _works.twig (the "author wins" rule as built);
  - the footnotes field's project config, and Craft's Table field (vendor/craftcms/cms/src/fields/Table.php);
  - the metadata of five stored portrait files, read with exiftool.
- **Not read**: no page was fetched, no image was looked at, and Reggie was not opened by me.

## In short

The universe is 4,040 entries (two more than last night: John Boston's two obituaries), 6,968 assets and 2 categories.

Of tonight's failures:
- **Held:** the fifteen text_to_image portraits (IMG-1 and IMG-2), the four Firefly-edited marks, the four lost Cronan and Kansas Street images, #2913 and #5377, and the Hart district and Runner edits.
- **Rule contradictions, unchanged from last night:** PER-2 and TTL-3, plus #28816 under IMG-4.
- **Check faults:**
  - 71 of the 93 IMG-7 rows: the file was read and recorded, in a field the test does not look at;
  - all three AUTH-4 rows, five of the 51 NOTE-1 rows, IMG-9 as a whole, Cooper's two new rows (IMG-4 and IMG-5);
  - and how NOTE-4 lays blame.
- **Real:**
  - Wicks's and Kellar's missing fields (IMG-3 and IMG-4);
  - 24 missing checksums (IMG-6);
  - 22 IMG-7 rows: 17 files with no read on record, and five Firefly downloads that carry a credential their records do not hold;
  - TYPE-1, Garcés (TTL-4), Adams (PER-4), Nadeau (PER-5), the fallen officers' advisories (EV-1), and 46 no-source notes with no search recorded (NOTE-1).

| Rule | Last night | Tonight | What changed | Classification |
|---|---:|---:|---|---|
| IMG-1 no generated image | 0 of 10 | 15 of 4,864 | the test now opens files | HELD (all 15) |
| IMG-2 enhancement rule | 17 of 20 | 15 of 20 | Leon Worden, Frémont now pass | HELD (all 15) |
| IMG-3 enhanced pair | 2 | 2 | none | REAL |
| IMG-4 edited image records the edit | 12 | 13 | + Cooper #38464 | 2 REAL, 9 HELD, 1 RULE CONTRADICTION, 1 CHECK FAULT |
| IMG-5 replaced portrait kept | 0 | 1 | + Cooper | CHECK FAULT |
| IMG-6 checksum at receipt | 24 | 24 | none | REAL |
| IMG-7 credential read and recorded | 94 | 93 | − Cooper | 71 CHECK FAULT, 17 REAL, 5 REAL (on TODO) |
| IMG-9 licence on outside images | 52 | 51 | − Cooper | CHECK FAULT (an unruled question) |
| AUTH-4 author and subject | 3 | 3 | none | CHECK FAULT |
| TTL-2 catalogue caption | 12 | 6 | six applied today | HELD (all 6) |
| TTL-3 legacyHeadline | 53 | 53 | none | RULE CONTRADICTION |
| TTL-4 honorific in title | 1 | 1 | none | REAL |
| TYPE-1 heldAs | 2 | 2 | none | REAL |
| PER-2 office held alone | 90 | 90 | none | RULE CONTRADICTION |
| PER-4 alias repeats title | 1 | 1 | none | REAL |
| PER-5 another person's name | 1 | 1 | none | REAL |
| EV-1 content advisory | 14 | 14 | none | REAL (not public) |
| NOTE-1 search recorded | 51 | 51 | none | 46 REAL, 5 CHECK FAULT |
| NOTE-4 blank footnote rows | 1,194 | 1,196 | + #38529, #38531 | CHECK FAULT as written; the cause is the field's default |

Every other rule passes, as last night:
- IMG-8, the AUTH rules 1 to 3, TTL-1, TTL-5 and TTL-6;
- PER-1, PER-3, OFF-1 and OFF-2, EV-2 and EV-3;
- MISC-1 to MISC-3 and SCR-1.

NOTE-2 and NOTE-3 fail nothing by design; they list records to read (below).

## Rule by rule

### IMG-1. No generated image on any record: 15 fail (last night 0)

**HELD** (TODO, "Current (9 October 2026, evening)", "The fourteen and the seven"; inventory/review/text-to-image-step-2026-10-09.md; DATA-MODEL: "for the fourteen and the seven that is open for Nathan ... Until he rules, check_generated_files.php lists them by name").

The fifteen are the fourteen enhanced portraits on records whose manifest records a Firefly Image 5 edit as text_to_image, and Couts (#27387), back on his record this evening:
- #27381, #27383, #27387;
- #31387, #31391, #31395, #31398, #31404, #31406, #31408, #31423, #31427, #31447, #31449, #31472.

None is class "generated": every chain opens an outside file.

**Why the count went up.** Last night's 0 was the field test that passed with Cooper's portrait on his record. Tonight's 15 is the same files read properly. Nothing on a record got worse.

**One note on the test.** The audit's rule text says "none with a text_to_image step", as if that were settled. DATA-MODEL now leaves it open. check_generated_files.php holds these fifteen by name, but the audit has no held list, so it reports them as failures.

**Passes that rest on less.** No asset on a record went unread (0 "not-read" in the 16:22 cache). But 113 passes rest on Craft's stored copy alone, with no master found. 60 of them are in the persons folder, mostly WordPress-era portraits with no source and no checksum: Portolá #30, Fages #28, Crespí #22, Kit Carson #31, Serra #18 and others.

DATA-MODEL says Craft's re-encode strips a manifest. The five stored copies I read with exiftool still carry their camera and software metadata (Canon EOS 5D Mark II, Canon PowerShot G3, Photoshop CS4 and CS6, a NYPL credit), so for those five the read means something. The other 108 I did not check.

### IMG-2. The enhancement rule: 15 fail (last night 17)

**HELD**, for the same reason as IMG-1. The fifteen are the same assets, now counted by their person records:
- #30219, #29316, #21584, #20224, #18726, #18702, #16432, #16140;
- #15919, #15808, #15477, #2585, #333, #323, #321.

Each fails only on "the file holds a generated element (text-prompt-step)": every one has its original linked, on the record, and its edit named. So IMG-1 and IMG-2 are one open question counted twice. IMG-9 counts twelve of these files a third time, for licence.

**Why two left.** Leon Worden (#279, asset #27396, Generate Fill and the upsampler) and Frémont (#307, #28814) now pass. The reconciled rule judges a file by its steps, and neither file holds a text_to_image step: both are class generative-edit.

They pass on the file's labels, which cannot say how much was painted. That is the same limit text-to-image-step-2026-10-09.md names for the fourteen. Last night's note that Frémont's corners look painted in, while his credential records only the upsampler, is still unsettled.

### IMG-3. The enhanced pair: 2 fail (unchanged)

**REAL.** Kellar (#21944, asset #38452) and Wicks (#18663, asset #38450): enhancedBy and enhancedDate are empty. Read in Craft tonight, each asset's own source says "Enhanced by Nathan Imhoff in Adobe Firefly on October 6, 2026". The fields were never filled when the assets were split on 8 October, and no TODO or ERRORLOG row carries it.

Separately, Kellar's licence is empty where Wicks's is "unknown" (IMG-9).

### IMG-4. An edited image records the edit: 13 fail (last night 12)

- **REAL (2):** #38450 and #38452, as IMG-3.
- **HELD (2):** the State Senate seal #29400 and Canyon High #29298. These are marks with a Firefly edit and no word on whether the unedited mark is held. TODO ("The four Firefly-edited marks on records") and DATA-MODEL ("Generated marks and ornament are a separate question, not yet ruled") hold them. Whatever is ruled, each record should still say whether an unedited original exists. Both sources say only that "where it was taken from is not recorded".
- **HELD (7):** the Hart district and Runner edits, all on no record, with enhancedBy empty: #29330, #29326, #29124, #28978, #28976, #28974, #28972. TODO, "Current (4 October 2026, overnight)", asks who made the edit.
- **RULE CONTRADICTION (1):** Vasquez's upscale #28816, on no record and withheld. It fails "no original linked", but enhancedFrom was cleared on purpose by the 8 October pulls, so that no viewer offers the generated copy. That is rules-in-force contradiction 7: the keep-both rule of 4 October (DATA-MODEL, link the original by enhancedFrom) against the 8 October pulls (pull scripts' headers, clear it). Neither rule names the exception.
- **CHECK FAULT (1, new):** Cooper's campaign image #38464. The test calls an asset "edited" when contentCredentials mentions trainedAlgorithmic. Cooper's is an OpenAI gpt-image file with no ingredient: generated, not edited, so the edit fields do not apply. It is on no record and withheld, and IMG-1 covers it.

### IMG-5. A replaced portrait stays as a related image: 1 fails (last night 0)

**CHECK FAULT.** Bill Cooper (#26946): his former portrait #38464 is on no record. It came off on 9 October under the no-generated-image rule, which overrides the keep-the-old-portrait rule (as the 29 exemptions of 8 October do).

The exemption fails him because it is keyed to wording: the audit exempts an old portrait whose source says "taken off", "off every record" or "off its record", or whose method names a text prompt. Cooper's asset records its removal in contentCredentials ("not shown on any record") and says nothing in source. The real rule behind it is still unwritten (rules-in-force contradiction 7).

His earlier portrait #27389 is in the exempt list, as last night.

### IMG-6. A checksum of the file as received: 24 fail (unchanged)

**REAL** under DATA-MODEL, "Provenance is recorded at the point of receipt": the import records the file's SHA-256 in sourceChecksum.
- Fifteen are election PDFs. Nine of their files sit untracked in inventory/elections, so the checksums could still be taken.
- Seven are Mentry source scans of 25 September: legacy-mirror, with legacySourcePath set.
- One is the Santa Cruz Sentinel scan #26982. Read tonight, it has no provenanceKind, no source, no legacySourcePath and no checksum: no provenance at all.
- One is the Library of Congress Hart photograph #21573.

TODO's unchecked-claims item ("2,569 with no checksum ... Which to check first is Nathan's call") covers these in general but names none of them.

### IMG-7. A credential read and recorded before the file becomes anything: 93 fail (last night 94)

Cooper dropped off because his contentCredentials now records his manifest. Every one of the 93 has now had its file opened by the scanner (all are in the 16:22 cache, none "not-read"). What differs is whether the read is on the asset.

- **CHECK FAULT (67).** rightsNote on each now reads "What the file as received declares (read 9 October 2026): ... no content credential" (outside_file_declares_2026_10_09.php, this afternoon). The test looks only at contentCredentials, and its note ("the scan writes no 'none found' note") describes the scanner as it was before today.
  - One wrinkle: DATA-MODEL, "Provenance and rights", still says the credential report "goes into contentCredentials as a note". The reads went into rightsNote. Either the rule's wording or the field used should move. It is not a failure of the read.
  - The timing half of the rule ("before it becomes anything") cannot be met after the fact for files that entered before 8 October.
- **CHECK FAULT (3).** The crops of Boston, Frew IV and Jenkins (#31938, #31935, #31932). Their source says "Cropped on October 6, 2026 from the original ... held in the archive": these are our own crops of the archive's photographs, though provenanceKind says "outside". The test trusts provenanceKind; a credential read has nothing to find on a crop we made. The kind value itself looks wrong for a crop of an archive original (not checked against any rule).
- **CHECK FAULT (1).** cw9901.pdf #28280 has a legacySourcePath on Reggie and a checksum. The scanner read its master, so the read happened and is simply not recorded on the asset. Counted here with the 67 in the summary: 71 check faults in all.
- **REAL (17).** The fifteen election PDFs, the Santa Cruz Sentinel scan #26982 and the Library of Congress Hart #21573 have no read on record, and their masters are not found: the scanner read only the stored copy. They are 17 of the 24 IMG-6 files. With cw9901.pdf they are the 18 assets carrying no rightsNote at all.
- **REAL, on TODO (5).** The five Firefly downloads, all on no record: Strickland #31465, Atkins #31443, Gladbach #31417, Rasmussen #29122, Walters #29118. Their files carry a credential (Firefly; Rasmussen's and Walters's include text_to_image), and their records say none of it. TODO, "Asset notes still to write", lists exactly these five.

### IMG-9. A licence on outside images shown on records: 51 fail (last night 52)

**CHECK FAULT: the test fails what the rule does not forbid.** DATA-MODEL says "an empty licence is not permission". It does not say a file with licence unknown may not be shown.

The 51 are:
- the 15 outside originals of 8 October and Boston's mug;
- 13 enhanced files and the two enlargements, whose rights are the original's, which the test does not follow by enhancedFrom;
- the three crops;
- lw2724a and lw2724b;
- three Commons portraits and 13 campaign or board portraits.

Last night this was put to you ("Yours to rule"). It is not in TODO's "Waiting on Nathan", so it is held nowhere.

### AUTH-4. Author wins: 3 fail (unchanged)

**CHECK FAULT.** "Author wins" is a display rule, and it is built:
- templates/persons/_entry.twig, lines 84 to 86: "Where a person both wrote a piece and is its subject, author wins: it counts as their work and is left out of what others say about them."
- templates/persons/_works.twig says the same.

So Connie Worden-Roberts as both writtenBy and subjectPerson on #28281, #28285 and #28287 is what the rule expects, and the page already resolves it. See "Read from a description", 1.

### TTL-2. A legacy photograph keeps Leon's catalogue entry: 6 fail (last night 12)

Six were applied today (held_photos_titles_2026_10_09.php): #4861, #4859, #3023, #2987, #5671, #4791.

**HELD (6):**
- The Cronan set #5735, #5737, #5739 and Kansas Street #4475: "no page and no file of that code anywhere on Reggie" (photo-images-census-2026-10-07.md, class E; leon-files-request-2026-10-07.md). The audit's own note expects them.
- #2913 and #5377: TODO, 9 October evening ("#2913 held: no catalogue entry of Leon's found"; "#5377 held with the date call").

#2913's title, "Fort Tejon Camels</", is a separate defect from the caption and is still there.

### TTL-3. legacyHeadline on retitled articles: 53 fail (unchanged)

**RULE CONTRADICTION** (rules-in-force, contradiction 10). The 7 October evening rule, "the title is the headline the page prints", under which these 54 were retitled, is set against the night rule, Leon's page headline kept in legacyHeadline. Neither is in a rule document yet: DATA-MODEL names legacyHeadline only in its field tables.

### TTL-4. No honorific in a person's title: 1 fails (unchanged)

**REAL.** #289 "Father Francisco Garcés". DATA-MODEL's name policy lists Father among the titles stripped. His search names already hold "Father Garces".

### TYPE-1. A moved article records heldAs: 2 fail (unchanged)

**REAL.** #2689 "Story of Sulphur Springs School" and #865 "Surveyor's Map Showing Lyon's Station": heldAs is empty on both, read in Craft tonight.

### PER-2. An office held alone is a row: 90 fail (unchanged)

**RULE CONTRADICTION** (rules-in-force, contradiction 9). Both are still as written:
- DATA-MODEL, "Who gets a person record" (1 October): "Keep: held office in the archive's records".
- PROFILES (6 October): "An office held alone is a row, not a record, unless ...".

Today's regeneration did not touch either. The list is also only candidates: "sources exist to write from" cannot be read by query.

### PER-4 and PER-5. Aliases: 1 each (unchanged)

**REAL.**
- Adrian W. Adams (#28667) shows his own title as an alias.
- Rémi Nadeau (#339) shows "Remi Nadeau", the title of his grandson's record #18869, as an alias.

Both were read in Craft tonight.

### EV-1. The content advisory: 14 fail (unchanged)

**REAL, not public.** The fourteen fallen officers have no top "This record concerns" note.
- DATA-MODEL's advisory rule applies to any record about killing or violent injury, and names the Kuredjian standoff itself. create_fallen_officers_2026_10_05.php writes its editor notes at the bottom only.
- They are disabled until you read them (EV-3; TODO, 5 October night). Nothing defers the advisory to that reading, so it is a fault to fix before they are enabled, not a held question.
- Whether a line-of-duty traffic death counts as "violent injury" is a reading.

### NOTE-1. No-source notes have their search recorded: 51 fail (unchanged)

- **REAL (46).**
  - 37 are the college trustee notes of 5 October ("The return of the 1999 election was not found").
  - Six are the school and water board terms (#28469, #28481, #28511, #28531, #29659, #28217).
  - The others are the Great Flood (two) and the Powerhouse Fire.
  - inventory/source-searches.json was last written on 6 October 08:40. Its 72 keys hold none of them, and the words Fortine, Boyer, Plambeck, Emmons, Flood and Powerhouse do not appear in it.
- **CHECK FAULT (5).**
  - #31370 and #30537 quote the Mint's 1891 letter ("no record of such a deposit ... can be found here"): a quotation, not an archive note.
  - #31893 and #31374 say "the timeline names no source for the line": a statement about a secondary source's citation, not a claim that no source exists.
  - #31359 points to #394, whose search is in the file under organizations:394.

### NOTE-4. Blank footnote rows: 1,196 fail (last night 1,194)

**CHECK FAULT as the rule is written; the real cause is a field setting.**
- **The rule:** "No row builder writes an empty footnote row" (ERRORLOG, 5 October).
- **What the same ERRORLOG row diagnoses:** "Craft stores a blank default footnote row when a record is saved without the field set."
- **Confirmed tonight:**
  - Craft's Table field defaults to one empty row (vendor/craftcms/cms/src/fields/Table.php, line 140: `public ?array $defaults = [[]];`);
  - the footnotes field's project config sets no defaults of its own.
- **The two new failures** are John Boston's obituaries, #38529 and #38531, saved at 15:18 today. Each holds one row of nulls. boston_obituaries_2026_10_09.php never mentions footnotes.

So the test cannot tell a builder's row from Craft's default, and the remedy ERRORLOG gives ("leave a field unset") is what produces the row. Every new entry in a section with footnotes will add to the count. The template hides them, so no reader sees one. Changing the field's default is a schema change, and that is yours.

### NOTE-2 and NOTE-3: nothing failed, lists to read

- **NOTE-2, the Newhall Pass interchange (#934).** It still holds the three unconfirmed On This Day rows whose context text carries "Wikipedia" and "Metroprimaryresources" chips. The record is not public, as last night.
- **NOTE-3.** The same five ellipsis quotations as last night; #394 still needs the ordinance read beside it.

## Read from a description

1. **"Author wins" (AUTH-4).** The audit and rules-in-force-2026-10-08.md both took the rule from CHANGELOG's one-line decision of 1 to 2 October ("Author wins when someone is both author and subject"). They read it as a rule about the data, that a person should not be both. The rule as built (templates/persons/_entry.twig, lines 84 to 86; _works.twig) is a rule about the page: both links stand, and the page counts the piece as the person's work. Read from the code, the three Worden-Roberts records are correct.
   - The template also dates the rule 1 October; the audit says 2 October.
2. **"No row builder writes an empty footnote row" (NOTE-4).** The rule was taken from ERRORLOG's remedy column, not from its diagnosis or from Craft. Craft's Table field writes the empty row itself, by default, whenever the field is left unset. The remedy as written causes the fault it names.
3. **IMG-7's own note**, "the scan writes no 'none found' note". It describes the credential scanner as it was. Since this afternoon, 67 of the assets it lists carry exactly that note, in rightsNote. The audit was re-run with a description of the recording that is a day old.
4. **The IMG-5 exemption.** Whether an old portrait was "taken off under a rule" is decided from the wording of the asset's source sentence, a description of the removal, not from the rule or the removal script. Cooper's removal was recorded in other words, in another field, and he fails.

## What I did not do

- I fixed nothing, re-ran nothing, and looked at no picture.
- The 108 stored-copy-only passes under IMG-1 beyond the five I read are unchecked.
- Whether the WordPress-era portraits of people with no known portrait from life (Portolá, Fages, Crespí) are what their filenames say is a separate question. It is a lead from the filenames, not a finding.
