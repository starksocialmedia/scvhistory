# The 2,445 "no metadata" images: what we know about their provenance (10 October 2026)

Claude, for Nathan. Read-only: nothing in the database, the templates or git was changed. Per-asset results: `inventory/review/no-metadata-images-provenance-2026-10-10.json` (one row per asset: bucket, prefix and its meaning, own page, ID line, credits, what the file carries, the Craft fields). Scratch scripts: `storage/runtime/scratch/nm_*.py` and `nm_*.php`.

## What was read, before any number

- **The file itself, all 2,445 masters on Reggie** (at each asset's legacySourcePath):
  - every tag exiftool 10.31 reads, all groups (`-a -G1`), not a fixed tag list;
  - the JPEG marker segments (APP0 to APP15, COM) and quantization tables, parsed directly;
  - the file dates on Reggie (modify, and for one file its birth date).
- **The file itself, every .htm/.html page on Reggie (33,690):** each page's img src and href values, resolved to paths, to find which pages show each image; then, for each image, the visible text of its own record page (`/scvhistory/<id>.htm`, or the folder's record page), its ID line ("LW2024: 9600 dpi jpeg from ..."), and any photo byline or courtesy line above the footer.
- **The file itself, `scvhistory/key.htm` on Reggie** (Leon's "Key to Photos"): 224 prefixes parsed with their meanings.
- **The file itself, the 42 "outside" masters** the census also classed as no metadata (Reggie or the held copy matched by hash), every tag; read only to test the census label.
- **A record about a file:**
  - the census's own output (`storage/runtime/overnight/pub-census.json`, `pub-targets.json`): which 2,445 assets, and what the census recorded per row;
  - the Craft asset fields, read live (photoCredit, courtesyOf, rightsHolder, rightsNote, license, creator, photoSourceCode, photoCaptionExt, source, sourceChecksum, alt, and which records use the asset);
  - the archive's manifest of Reggie (`inventory/raw/scvhistory-manifest-2026-08-20.sha256`), compared to Craft's sourceChecksum; the hashes were not recomputed from the files;
  - `inventory/raw/page-types.csv` (its `has_credit` flag), compared to the pages as read.
- **Not read:** the live site (never crawled); `storage/runtime/master-hash-index.json` beyond the 42 outside files (it indexes files held in the repo, not Reggie); `storage/runtime/generated-scan.json` (the census output names the same 2,445).

## What "provenance" can and cannot mean here

Two different questions hide in the word:

1. **Who supplied the image to SCVHistory.com** (the donor, lender, seller, institution or website it came through). This is what Leon's prefixes and courtesy lines record. key.htm's own wording is about supply: "courtesy of", "from the collection of", "assembled by", "provided by".
2. **Who made the image** (the photographer, the artist, the publication that printed it), and who made **the file** (the scan, the resave, the crop). The prefix almost never answers this.

The witnesses answer different parts:

- **The prefix** answers question 1 at collection level only. LW means "Photographs from the collection of Leon Worden". It covers a 1924 Ford publicity photograph (LW2024), a 1930 Popular Science photo essay (LW2083), a 2019 property listing (LW3487), and a potter's work photographed by an eBay seller (LW3792).
- **The ID line** often answers how the file was made: "19200 dpi jpeg from copy print", "from original postcard purchased 2014 by Leon Worden". Sometimes it names a maker ("from original photograph by Leon Worden", 91 files) and sometimes an institution ("from California Historical Society collection in USC Digital Archive").
- **The file** answers only what software and device last wrote it, and sometimes the channel it came through (eBay, Facebook, a wire service). It never names the original photographer, with two exceptions (an AP handout and a Signal photo; see below).
- **Leon's own blanket statement**, at the head of key.htm: "Photographs are presented in a high-resolution JPEG format and have been enhanced for maximum clarity and detail." This is a page-level declaration that the site's photographs were edited by their publisher. It is not tied to any one file.

So for most of the 2,445 the honest answer is: **we know who supplied them, at collection level, and how Leon digitised them. We do not know who made them**, unless the page says.

## The answer, measured

Every asset falls in one of four groups:

| Group | Images | On a record |
|---|---:|---:|
| A. A credit tied to the image: its own ID line names a person, body, purchase or source (860), or a credit sentence sits beside a full-size image with no ID (22) | 882 | 658 |
| B. No tied credit, but its own record page carries a photo byline or courtesy line not tied to this file (for example "Photos by Stan Walker and Leon Worden" over 104 gallery files) | 145 (8 of them a false match, "Estate of Margaret Warmuth sold the property": 137 real) | 107 |
| C. No credit line, but a collection prefix with a meaning in key.htm | 1,294 | 955 |
| D. Neither | 124 | 84 |
| **All** | **2,445** | **1,804** |

- **Shown on a Leon page at all:** 2,430 of 2,445. The other 15: 13 pages of `files/harthigh_may1952/` (in a folder whose record page credits "courtesy of Lauren Parker", so B in substance), and lw3575's two TIFFs, added to Reggie on 4 October from the crawl (reggie-comparison.md).
- **A collection prefix with a known meaning:** 2,252. Of these, LW (Leon's own collection) is 1,966, SC (City of Santa Clarita) 93, AP (assembled by A.B. Perkins) 64, then AL, NT, SG, HS, SW, TV and 40 more, few each. Four prefixes are not in key.htm (ANA, LACO, GJ, LAT), one file each; GB, which INVENTORY counts 100 of, has no line in key.htm either.
- **What group A's credits actually name** (860 ID lines):
  - purchase by Leon, nothing else (the seller, not the maker): 454;
  - a holder, lender or institution named, the maker not: 257;
  - the maker named: 91, nearly all "by Leon Worden" (Richard Rioux 4, Bryan Kneiding 1);
  - other wording that names a publication or similar: 58.
- **Group D** is mostly `gif/mugs/` portraits (11), `oldtownnewhall/gif/` (16), thumbnails (46), and one-off images with descriptive file names (reminadeau-chrisman.jpg, danhon.jpg, jugband.jpg). For these the only witness is the page they sit on, and it says nothing about origin.
- **Thumbnails:** 406 of the 2,445 are 120 by 90 or similar thumbnails (filename ending in `t`), 127 of them on records. A thumbnail is a derivative of an image the archive usually also holds at full size. It appears on other items' pages, so a credit read beside it is that other item's credit. They were counted only by their own item page.

## What the files actually carry (the census label was not "no metadata")

The census classed these 2,445 as "master read, no metadata about its making". It read 13 named tags (Software, CreatorTool, the XMP history, Make, Model, IPTC/XMP source type, provenance, Credit, Artist, Copyright, ImageSize). Read in full, **1,979 of the 2,445 carry more than structural tags**:

| What the file carries | Images |
|---|---:|
| Photoshop's own resource block (Photoshop:WriterName "Adobe Photoshop", 1,289; PhotoshopQuality, the Save As quality setting, 1,311; Adobe APP14, 1,350): 1,350 files with at least one, **of which 620 also embed an Epson ICC profile** | 1,350 |
| Epson ICC profile only ("EPSON sRGB", "EPSON Gray - Gamma 2.2", "EPSON Standard RGB": the profiles Epson's scanner software embeds) | 215 |
| Bare uncompressed TIFF, no software tag (ppi 400 on 443, 9600 on 17, 2400 on 8) | 470 |
| A comment or IPTC text, no Photoshop block | 55 |
| JFIF only, encoded with the standard libjpeg (IJG) tables, which Photoshop does not use (180 of them at quality 100, every one a gallery copy under `data1/images/`) | 337 |
| GIF, PNG, other | 18 |

What that tells, and what it does not:

- **Photoshop named the file in 1,350 cases** the census counted as naming no program. The census's legacy-mirror "editing program named" count of 2,945 should read about 4,295. Like the census's own count, this says the program saved the file, not what it changed.
- **An Epson scanner workflow is consistent with 835 files** (Epson ICC profile). That fits Leon's "9600 dpi jpeg from original print" lines. It is not proof of the device: a profile can be assigned in Photoshop.
- **The comments and IPTC text are the only file-level witnesses to a supplier or maker:**
  - "Processed By eBay with ImageMagick": 10 files (lw3792a to j). The page says "Artwork purchased 2021 by Leon Worden". The pictures are the eBay seller's listing photographs of the pot: Leon supplied them, the seller made them.
  - Facebook's markers (IPTC SpecialInstructions "FBMD01..." or a 20-character OriginalTransmissionReference): 31 files, among them tlp_ newspaper clippings, the John Ward family photographs, Gerald Ahnert's images and lp_/jb_/sw_ clippings. These came through Facebook.
  - kuredjian_jake.jpg: IPTC Credit "AP", Source "LA COUNTY SHERIFFS DEPARTMENT", caption "FILE--This a recent handout photo...", dated 2001-08-31. Maker: the Sheriff's Department; distributor: AP. The census row itself recorded Credit "AP" for this file, yet classed it as no metadata. The Craft asset (#31225, on a record) has no credit.
  - sg081302at.jpg (thumbnail, on no record): IPTC caption "MASON POOLE/The Signal". The photographer is named in the file. Craft put the caption into alt, not credit.
  - "Created by Leon Worden" (9 files, lw2083a to i) on scans of a May 1930 Popular Science Monthly photo essay by Edwin W. Teale. The comment names who made the file, not the photographs. This is the plainest example of the supplier-versus-maker gap.
  - "SCV History In Pictures" (19), "LEAD Technologies Inc. V1.01" (6, early oldtownnewhall images), "File written by Adobe Photoshop 4.0/5.0" (3), "gd-jpeg ... quality = 80" (1, a web-generated copy), "Imacon Color Scanner" (davidmarch.jpg: a drum scanner), Picasa as IPTC By-line (3).
- **File dates on Reggie are not dates of making.** Modify dates run 2003 to 2021 for the JPEGs, which fits the server's Last-Modified kept by the download (Leon's upload date, at best). The 470 TIFFs mostly carry 20 or 21 May 2026 (the download, some truncated per reggie-comparison.md), and 94 files carry 4 October 2026, the day they were added to Reggie from the crawl (birth date read on lw3575b_orig.tif; 46 of the 2,445 are not in the 20 August manifest). At best a date says when a file reached the server. It never says when the scan or the photograph was made.
- **Identity, not origin, is what the manifest witnesses:** for 1,391 of the 2,445, Craft's sourceChecksum equals the 20 August manifest's hash; 1,008 have no Craft checksum; 46 are not in the manifest.

## Do the archive's Craft fields claim more than the page says?

Checked field by field against the pages that show each image:

- **creator, rightsHolder:** empty on all 2,445. **license / rightsNote:** 28 assets, all "unknown" / "No permission to republish is established". These claim less, not more.
- **courtesyOf:** 1 asset (johnward_dorothyward.jpg). It matches the page ("Image courtesy of their granddaughter, Tara Vaughn Garza").
- **photoCaptionExt:** 588. Every one is found verbatim on a page that shows the image.
- **source:** 1,115 name the page the image was "published on" or "published with". 1,105 check out (38 show the image directly, 1,060 link its folder, 2 name the file). The five lw2083 sub-images name the index page, not the sub-page that shows them: imprecise, not a false claim.
- **photoCredit:** 30. Nine are the image's own ID line, verbatim. **Twenty-one are not credits for the image at all.** Twenty carry "Hart biography © Friends of Hart Park • Used by permission", the copyright line of the biography text at the foot of every William S. Hart page. One (lw2068) carries "News story courtesy of Tricia Lemon Putnam", about the transcribed news stories.
  - The images' own ID lines say otherwise, for example LW2298A "from original print purchased 2012 by Leon Worden, donated to SCV Historical Society", LW2472 "from original postcard purchased 2013 by Leon Worden".
  - So the field credits Friends of Hart Park with postcards, prints and book pages Leon bought. Three are on records: lw2068 (#14226), lw2138 (#14181), lw2616 (#13495). The templates print photoCredit as the image credit.
  - **This is not new.** `scripts/import/fix_hart_park_captions_2026_10_05.php` names it in its header ("photoCredit ... carries the biography's credit, not the image's, on these 13 and on 22 more assets (35 in all). That is a separate decision for Nathan"). I could not find it in TODO.md. It is still open, so it is listed here, not as a new finding.
- **photoSourceCode** (the "letter prefix of the item ID" by CONTENT-MODEL): 1,348 filled. 1,191 hold an ID-shaped value (the full ID, often with its suffix: `lw2258b`, `ap0930t`), and 157 hold a file stem that is not a donor code (`danhon`, `images`, `smalllw`, `nlg-logo-90`). This misuses the field but claims no donor.
- **alt:** two take text from the file's IPTC caption (sg081302at "MASON POOLE/The Signal...", lw2148 "SCV History In Pictures"). Accurate to the file, but the photographer's name sits in alt text, not in credit.
- **`page-types.csv` has_credit** is not a credit witness. 307 images whose own ID line names a person or purchase sit on pages it marks blank, and 616 with no credit line sit on pages it marks "yes" (it appears to flag the scan line).

**Net:** apart from the known Hart line, **no Craft field claims a supplier, maker or right that the page does not give.** The gap runs the other way. 882 images have a tied credit on their page, and Craft's photoCredit or courtesyOf holds it for 10.

## Candidate seventh instance (written up, not fixed)

**A census label read in place of the files it summarised.** The census report's table heads the column "Master read, no metadata". Its text says the 42 outside masters "carry no metadata at all". This task's brief carried that forward as "carried no metadata at all" for the 2,445. The census's own JSON class is narrower ("no metadata about its making"): it was decided on 13 named tags.

Read in full, 1,979 of the 2,445 carry more than structure. 1,350 name Photoshop in its own resource block, and 31 carry Facebook's markers. One (kuredjian_jake.jpg) carries an AP credit that the census's own row recorded ("credit": "AP") while its class said no metadata. Of the 42 outside masters, 28 carry more than structure, among them boston-john-scvhistory-mug.jpg, whose comment reads "File written by Adobe Photoshop 4.0". The City council portraits (Gibbs, Ayala) do carry nothing, so the census's point about them stands.

The pattern is the one ERRORLOG has six times this week: a summary (here a class label chosen from a fixed tag list) restated as a fact about the files, then read downstream in place of the files. Nothing was built on it yet, as far as I can see, beyond the census's counts and this brief. I have not changed the census or ERRORLOG.

## The sample: 51 read, page and file

Drawn at random within groups (seed 20261010): 8 LW gif, 5 LW folder, 4 SC, 4 AP, 8 other prefixes, 8 neither, 8 with a tied credit, 6 whose file carries text. For each, the page text around the image, its ID line and every exiftool tag were read. Selected rows (all 51 are in the JSON, `bucket` and `file` fields):

| Asset | File | Prefix says | Page says | File says | Supplier / maker, as far as the witnesses go |
|---|---|---|---|---|---|
| #14092 | gif/lw2258b.jpg | LW, Leon's collection | "from printed magazine (The Land of Sunshine, November 1900) / Online image only" | Photoshop q8 | supplier: online copy; maker: the 1900 magazine's photographer, unnamed |
| #12097 | gif/lw9301d_large.jpg | LW | "from original photograph by Leon Worden" | Photoshop q8, Epson sRGB, 2400x3166 | maker and supplier: Leon |
| #10839 | gif/lw2497_large.jpg | LW | "from California Historical Society collection in USC Digital Archive" | Photoshop q8 | supplier: CHS/USC; LW prefix notwithstanding |
| #11036 | gif/lw2627e_large.jpg | LW | Desert magazine Feb. 1957 page "purchased 2014 by Leon Worden" | Photoshop q8, Epson | supplier: Leon (bought); maker: Desert magazine, photographer unnamed |
| #10739 | gif/lw2450a_large.jpg | LW | ACME wirephoto 1937, "original print, purchased 2013 by Leon Worden" | Photoshop q8, Epson | maker: ACME Newspictures (page); supplier: Leon |
| #14214 | gif/lw2095a.jpg | LW | "from digital image by Leon Worden" | Photoshop q4 | maker: Leon |
| #35470 | files/lw3392/data1/images/arch083018ag.jpg | LW | page byline "Photos by Stan Walker and Leon Worden" | JFIF, IJG q100, 800x600 (gallery copy) | maker: Walker or Worden, which one not recorded |
| #36068 | files/lw3029/lw3029_002.jpg | LW | "pdf of original brochure, collection of Connie Worden-Roberts" | Epson sRGB | supplier: Worden-Roberts papers, filed under LW |
| #27869 | gif/sc9612.jpg | SC, City of Santa Clarita | "SC9612: 19200 dpi jpeg." | Photoshop q8, Epson | supplier: the City (prefix only); maker unknown |
| #38369 | files/sc1903/.../49085921291_64f791da5d_o.jpg | SC | gallery page, no credit | JFIF IJG q100, 800x600; Flickr "original" file name | supplier: the City by prefix; the Flickr name suggests the City's Flickr. **Inference only** |
| #31246 | gif/ap2222_large.jpg | AP, Perkins | "from copy print" | Photoshop q8, Epson | supplier: Perkins collection; maker unknown |
| #2355 | gif/gr0301_large.jpg | GR, assembled by Jerry Reynolds | "Jerry Reynolds' depiction of the end of the Crown Valley Feud" | Photoshop q8, Epson Gray | supplier: Reynolds; maker: the illustrator, unnamed |
| #2432 | gif/ms0232.jpg | MS, SCV Historical Society | "MS0232: 9600 dpi jpeg from original print" | Photoshop q8, Epson Gray | supplier: SCVHS; maker unknown |
| #37474 | files/harthigh_may1952/story of hart_52009.jpg | none | folder page: "Online only; courtesy of Lauren Parker" | Epson sRGB | supplier: Lauren Parker; maker: the 1952 school publication |
| #31245 | gif/mugs/gloriamercadofortine.png | none | nominee list, no credit | PNG, ICC "c2" | nothing beyond the page |
| #14599 | gif/mugs/donguglielminot.jpg | none | thumbnail strip, no credit | Photoshop q6, Epson | nothing |
| #13413 | gif/lw2725.jpg | LW | "Photo by Frank F. Latta and in Bear State Library. [Latta 1976:196]" | Photoshop q8, Epson | maker: Latta; supplier: scanned from Latta's book |
| #10420 | gif/lw2139_large.jpg | LW | "from original print / Photograph by Leon Worden" | Photoshop q8, Epson | maker: Leon |
| #14775 | gif/ms0291t.jpg | MS | (thumbnail on hs0200.htm, whose line credits Betty Houghton Pember for HS0200) | Photoshop q6 | the nearby credit belongs to another item; not counted |
| #12055 | gif/lw3805_stanthonys2011_large.jpg | LW | the page's ID line is about the 1898 photograph bought at the Blum estate sale; this file is "St. Anthony's in 2011" | COM "gd-jpeg ... quality = 80" (a web-generated copy) | maker and supplier unknown; **the ID line does not cover it** |
| #37366 | files/lw3792/lw3792f.jpg | LW | "Artwork purchased 2021 by Leon Worden" | COM "Processed By eBay with ImageMagick" | the object was bought; the photograph is the eBay seller's |
| #14301 | gif/lw2083d.jpg | LW | Popular Science Monthly May 1930, photo essay by Edwin W. Teale | COM "Created by Leon Worden" | maker: Popular Science / Teale's photographers; the file's "created by" is the scan |
| #1946 | gif/anzio-nettuno_020944_large.jpg | none | "The front lines on the day/evening of SGT Contreras' death", no credit | COM "File written by Adobe Photoshop 5.0", Photoshop q1, sRGB | a map of unknown origin |

What the 51 show, beyond the counts:

- In every sampled case the Craft fields claim no more than the page.
- Thirteen of the 51 are thumbnails. A credit read near a thumbnail belongs to the item whose page it sits on (#14775, #14517, #14551).
- A page's ID line covers the page's main item. It does not cover every file on the page (#12055, #37366). Group A's 882 is therefore an upper bound on "this file's own credit".
- A collection prefix can be a filing code, not a supplier (LW2497 from CHS/USC, LW3029 from Connie Worden-Roberts's papers, LW3487 a 2019 property listing).

## Limits

- The ID-line and byline classifier is pattern-based. 25 named and 25 scan-only lines were read by eye to check it, and the page-credit pattern was corrected once (it had matched "Collection of Recipes" and "collection of housing tracts"). Group B still holds 8 known false matches.
- "Shown on a page" is by src/href. An image reached only by JavaScript would be missed. 15 were not found on any page; their folders were checked by hand.
- The Epson reading rests on the profile, not on a scanner tag. The Facebook reading rests on Facebook's known IPTC markers.
- Wikipedia was not used.

## Suggested next steps (not done)

1. Nathan: the open Hart photoCredit decision (35 assets, 21 of them in this set), already named in the 5 October fix script and not on TODO.md.
2. Nathan: whether the census's class should be renamed "no making tags among 13 read". Its legacy count of editor-named files moves from 2,945 to about 4,295 once Photoshop's own block is read.
3. If credits are to be carried into Craft: group A's 860 ID lines are verbatim and tied, and could fill photoCredit as written. They are not the maker, except for the 91 that say "by".
