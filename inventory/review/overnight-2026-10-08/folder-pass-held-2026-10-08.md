# The 759 files the folder pass held (overnight, 8 October 2026)

Claude, overnight run, read-only. Nothing written to Craft. For Nathan. Source list: storage/runtime/photo-import/plan2.json, "held" (759 rows), as summarised in inventory/review/folder-pass-dry-run-2026-10-08.md. The pass itself was applied on the evening of 8 October.

## What was read

- The folders on Reggie, file by file (/mnt/reggie inside the container): uc8901, gt8702, scvhs2000minutes, lw3257 (with data1/images), lw3267, ionereed, lw6902, lw3135, lw3136, lw3140, lw3552, sc1903, lw3392b, lw2980. Checksums compared where two files might be one (md5); PDF trailers compared byte by byte.
- The pictures by eye: gt8702a and gt8702g, the four thumbnails against two of their full pictures (lw3552, lw3136s).
- Craft tonight: the asset fields on the 17 records concerned (scratch script, reads declared).
- The Internet Archive's metadata for the item the uc8901 scans came from (read only).

## The groups

759 rows are 745 distinct files: the gt8702 folder (12 files) and the scvhs2000minutes folder (2 files) are each listed under two records.

| # | Group | Rows | Answer |
|---|---|---|---|
| 1 | uc8901: the Internet Archive's raw page scans | 658 | Obvious |
| 2 | -orig and -ebook PDFs | 3 | Obvious |
| 3 | Thumbnails | 4 | Obvious |
| 4 | LW3267's pictures in LW3257's folder | 14 | Obvious |
| 5 | #605 and #613: the places' copies of the photographs' folders | 51 | Obvious, rests on a decision already waiting |
| 6 | gt8702 | 24 (12 files) | Nathan |
| 7 | scvhs2000minutes | 4 (2 files) | Nathan |
| 8 | The mp3 | 1 | Nathan |

658 + 3 + 4 + 14 + 51 + 24 + 4 + 1 = 759.

### 1. uc8901's raw page scans (658 files, #31723)

- What they are: newspapereditor00newhrich_raw_0069.jp2 to _0727.jp2 (0723 missing), 946 MB. The Internet Archive's camera scans of the item newspapereditor00newhrich: Scott Newhall's Bancroft Library oral history, "A newspaper editor's voyage across San Francisco Bay", c1990, University of California Libraries, 726 images. The item is public on archive.org.
- Craft: #31723 holds uc8901.pdf (the 716-page PDF the page linked as "Open original .pdf"), with its rights note.
- **Answer: do not import.** They are the raw material of the PDF the record already holds, and the Internet Archive keeps them publicly. They stay on Reggie: "Web copies on the server, masters on Reggie" (photo-import-plan-2026-10-07.md, "The rule for what goes to the server", the same rule as docs/DEPLOY.md, "The masters").
- One note for Nathan, not a question: the Internet Archive marks the item "possible-copyright-status: IN_COPYRIGHT", which agrees with the rights note already on uc8901.pdf ("Copyright 1990 by The Regents of the University of California ... No permission to republish is established").

### 2. -orig and -ebook PDFs (3 files: #31723 two, #5685 one)

- uc8901-ebook.pdf: the same size as uc8901.pdf (65,419,092 bytes) and the same document. 116 bytes differ, all in the trailer's /ID and the info dates.
- uc8901-orig.pdf (244 MB): the Acrobat Paper Capture original, before the Ghostscript compression that made uc8901.pdf.
- lw6902-orig.pdf (168 MB): the same for lw6902.pdf (16 MB), which #5685 holds with its 108 page pictures.
- **Answer: do not import.** The -ebook is a duplicate; the -orig files are masters and stay on Reggie, by the same rule as group 1.

### 3. Thumbnails (4 files: #4951, #4953, #4959, #5403)

- lw3135at, lw3136st, lw3140at, lw3552t: each 120 x 90 pixels, the site's index thumbnails. Viewed against their pictures: lw3552t is lw3552.jpg cropped (the two figures by the house), lw3136st is lw3136s (the loader by the railcars). Each record holds the full picture.
- **Answer: leave out.** The folder pass plan's own rule says so (scripts/import/folder_pass_plan_2026_10_08.py, header: "Left out: a thumbnail (name ending t) of a picture the record holds or this pass adds"), and the importer prefers the larger file (CHANGELOG, the ledger entry: "the importer prefers the larger file anyway"). They were listed as held, though the rule leaves them out.

### 4. LW3267's pictures in LW3257's folder (14 files, #5077)

- Where they are: files/lw3257/data1/images/lw3267a.jpg to n.jpg. data1/images is the slideshow software's folder of renders, not Leon's own files: lw3257's own pictures sit at the folder's top level (lw3257a to r, each with _large).
- Each is byte-identical to the file of the same name in files/lw3267/data1/images (md5 checked on a, b and n). lw3257's slideshow index does not show them.
- LW3267 is #5089, "Arthur Charles Mentry's Blacksmith Forge", which holds all 14 (a to n) from its own folder, as _large originals.
- **Answer: leave them.** Nothing is missing: they are leftover slideshow renders of pictures #5089 already holds. The folder census says it leaves out "the flipbook and slideshow software and its renders" (inventory/review/folder-census-2026-10-08.md, "What it read").

### 5. #605 Heritage Junction and #613 Six Flags Magic Mountain (51 files)

- lw3789 (39 "Suddenly" screenshots of the Saugus depot) and lw3790 (12 pictures of the Colossus coach). The pass gave them to the photograph records made from those pages: #5645 holds all 39, #5647 all 12.
- The places claim the folders only because their legacyUrl and legacyKey point at those photograph pages. The place legacy links dry run of 5 October already proposes moving #605 to heritage.htm and clearing #613 (inventory/review/place-legacy-links-dry-run-2026-10-05.md), waiting on Nathan's word (TODO, "Silent faults, the dry runs").
- **Answer: no copies on the places.** Once that dry run is applied, the places no longer claim the folders. What links the two is a relation, not a second copy (DATA-ORGANIZATION.md section 5: "Relate, don't tag. If a record exists, link it."). #5645's and #5647's photoPlaces are empty tonight: #5645 to #605 and #5647 to #613 are the obvious links.

### 6. gt8702 (12 files, listed under #28293 and #28310): needs Nathan

- What they are: **Gary Thornhill's photographs, not clippings.** gt8702a to l, each as .jpg, _large.jpg and a 210 MB _orig.tif. Viewed: a is a man holding a "TOTAL REVENUE BY SOURCE" pie chart; g is a speaker at a table under a "CITY OF SANTA CLARITA" map. The page gt8702.htm heads them "Santa Clarita Cityhood? First Public Forum, Canyon Country, January 13, 1987 ... Photos by Gary Thornhill/The Signal", and shows them as a slideshow.
- The same page also transcribes two Signal clippings. Those became the two document records, #28293 (11 January 1987) and #28310 (4 January 1987). No photograph record exists for GT8702; only the thumbnail gt8702t.jpg is in Craft (asset 2410).
- GT8702 is a photo ID in the DATA-ORGANIZATION.md section 8 sense (donor prefix and four digits).
- **Question for Nathan:** should GT8702 become a photograph record, "Santa Clarita Cityhood? First Public Forum" (the page's headline, 7 October title rule), dated 13 January 1987, credit Gary Thornhill/The Signal, holding the 12 pictures, with #28293 and #28310 related to it? Recommended: yes. The alternative is to put the pictures on one of the two clippings, which they do not illustrate: the forum came after both clippings were printed.

### 7. scvhs2000minutes (2 files, listed under #28283 and #28285): needs Nathan

- What they are: the Historical Society's bound minutes volume, scvhs2000minutes.pdf (83 MB), and its master, scvhs2000minutes-orig.pdf (127 MB, Acrobat Paper Capture, from a Canon copier scan).
- The two records are excerpts from it: #28283 the minutes of 19 May 2003 (flipbook pages 414 and 425), #28285 Connie Worden-Roberts's "A Look Back in Time" proposal (pages 427 and 428). Their legacyUrl is the PDF itself. Neither holds a file.
- The -orig is a master and stays on Reggie (group 2's rule).
- **Question for Nathan:** should the 83 MB volume be one asset related to both records (their notes already give the page numbers)? Recommended: yes, one asset on both. The 8 October import already related existing assets to further records ("5 existing assets linked", photo-import-held-2026-10-07.md). The alternative, a document record for the whole volume, adds a record for something nothing else yet cites.

### 8. The mp3 (1 file, #5251): needs Nathan

- What it is: tlp_daredevilsofhollywood19380523ionereed.mp3 (3 MB), the 23 May 1938 episode of "The Daredevils of Hollywood" on Ione Reed, in which she speaks. #5251's body cites it twice, in Leon's text: footnote 27 and "(Click here to hear her.)", which in Craft is now plain text that goes nowhere.
- No audio file is in Craft at all, and audio belongs to the later-phase Oral History work (BUILDPLAN.md, "Oral History", "audio file + transcript").
- **Question for Nathan:** should this one mp3 go onto #5251 as a related file now (recordDocuments), or stay on Reggie until there is audio support? Recommended: stay on Reggie. AGENTS.md says no later-phase features before the content model and the migration are done, and Leon's "(Click here to hear her.)" stays as he wrote it either way.

## Found on the way, outside the 759

**78 of the pictures the pass imported came from slideshow renders** (files/*/data1/images, 800 x 600): 74 on #31326 (sc1903), 2 on #4767 (lw2980, 24324_backhouse01 and 02), 2 on #5235 (lw3392b). For sc1903 and lw3392b the renders are the only pictures in the folder. For lw2980 the two backhouse pictures exist only as renders. So they may well be the best copies on Reggie, but they are renders, and the census says it left renders out. It left out only data1/thumbnails and data1/tooltips (folder_census_2026_10_08.py, SKIP_DIRS). Nathan to decide whether that matters; nothing was changed.

## Read from a description

| Where | What was said | What the source shows |
|---|---|---|
| folder-pass-dry-run-2026-10-08.md | "gt8702 is one folder of clippings behind two documents" | The 12 pictures are Gary Thornhill's photographs of the 13 January 1987 forum; the clippings are transcribed on the page, and none of the 12 is a clipping |
| folder-pass-dry-run-2026-10-08.md and plan2.json | lw3267a to n in lw3257's folder: "named for another record ... a neighbour's picture in this folder" | Slideshow-software renders in data1/images, byte-identical to LW3267's own renders; not files Leon put in LW3257's folder |
| folder-census-2026-10-08.md, "What it read" | "Left out: the flipbook and slideshow software and its renders" | data1/images was not left out: 14 rows held from it and 78 pictures imported from it |
| folder-pass-dry-run-2026-10-08.md | "#605 and #613 are places made from the same legacy pages as photographs #5645 and #5647" | The places' link to those pages is a known wrong link (place-legacy-links-dry-run-2026-10-05.md), not the pages they were made from |
