# Type census: photographs and documents that are articles (7 October 2026)

Read-only. Claude. Scripts and working files are in `storage/runtime/type-census/`. Every record is listed in `type-census-2026-10-07.json` beside this file.

Rule tested (Nathan): type follows the thing, not the file it arrived as.

## 1. Counts

### Photographs (1,563 live entries, all from `/scvhistory/*.htm` on the Reggie mirror)

| Kind | Count | Band |
| --- | --- | --- |
| Magazine or newspaper article (headline, byline or masthead, pages scanned) | 19 | sure |
| Same, less clear (company newsletter item 4269; whole magazine issue 5363) | 2 | likely |
| Borderline text pieces (fiction serials 4735 and 5347, film-news program 5199, sales sheet with byline 5325, book excerpts 4437, 3185 and 4451, a press photo page with news reports 4429) | 8 | unsure |
| SCVHistory.com or SCVNews web article that sits on a photo page (By Leon Worden, and others). Not a scan, but an article | 18 | sure (the three Placerita Oil Field records 3957, 3959 and 3961 are one article) |
| Programs, movie heralds (27 are Baker Ranch 1926 program pages) | 42 | not articles |
| Brochures, menus, catalogs, directories | 24 | not articles |
| Official or business papers (petition, application, reports, invoices, press releases, screenplay) | 20 | not articles |
| Letters, memos, envelopes | 15 | not articles |
| Advertisements | 11 | not articles |
| Pictures or objects (correctly typed as photographs) | 1,404 | |

So 21 photographs are article scans (sure plus likely), 8 more are unsure, and 18 are born-digital articles. About 112 more are text pages that are not articles. Press-photo prints with a typed caption sheet (ACME, NEA, AP, for example 4557, 3693, 4377 and 4387) are photographs. Their versos carry printed text, but they are not articles.

### Documents (64)

| Kind | Count | Band |
| --- | --- | --- |
| Newspaper or online news article (The Signal, LA Times, LA Herald, KHTS, SCVNews and others) | 23 | sure |
| Interview or news item (31412, 31312, 28291); essay or journal article (27374, 28281, 28287) | 6 | likely |
| Press releases (31336, 28307), eulogy (20104), 1874 pamphlet (18991), open letters (31306, 31334), Pen Pictures book entries (28055, 20099) | 8 | unsure |
| Election returns, web pages, lists, minutes, bylaws, a death certificate | 27 | not articles |

## 2. Signals: precision measured against hand labels

**Method.** I looked at 44 photographs by eye: 16 OCR-positive, 16 header-flagged and 12 random. I then labelled all 220 records that any signal flagged by reading their title and header block. The 12 random records and a keyword sweep of the 1,343 records no signal flagged turned up no missed article scans. A strict hit is an article labelled sure or likely.

| Signal (photographs) | Flagged | Article precision | Text-page precision | Recall |
| --- | --- | --- | --- | --- |
| Title keyword (article, story, magazine, Signal…) | 19 | 0.37 | 0.68 | 0.33 |
| `body` `[lines]` header has "Publication \| date" | 42 | 0.33 | 0.93 | 0.67 |
| `[lines]` header has a "By …" line | 38 | 0.37 | 0.66 | 0.67 |
| Legacy page links "Open original .pdf" or Book View | 60 | 0.25 | 0.90 | 0.71 |
| Legacy page is a flipbook iframe (`files/<key>/`) | 102 | 0.15 | 0.52 | 0.71 |
| `creditKind` or `creditRaw` says magazine, newspaper or newsprint | 38 | 0.37 | 0.55 | 0.67 |
| Tesseract on the mirror image, 10 or more common English words | 75 | 0.08 | 0.61 | 0.29 |
| Image taller than wide (h/w > 1.25) | 327 | 0.00 | 0.14 | 0.00 |
| `legacyCategory`, collection membership | none useful (`partOfCollection` is empty on every photograph) | | | |

OCR and aspect ratio mostly find programs, menus and press-photo snipes, so they find text but not articles. The main images are 800 px wide, and a flipbook's first page is usually the magazine's cover.

**Recommended combination.** The `[lines]` header has a byline or a "Publication | date" line, and the title does not name ephemera (program, menu, brochure, ad, letter, catalog, comic, map, painting, report, petition, book or intro), and the header is not SCVHistory.com's own byline, and the legacy page carries the pages as a PDF or flipbook.

- Strict: 21 flagged, precision 0.71 (0.86 counting unsure articles), recall 0.71. The false positives are 5427, 4877 and 4785.
- Dropping the PDF or flipbook condition gives 39 flagged and recall 0.86, but the nine Land of Sunshine illustrations (3051 to 3067) come in. They share one article's header, but each is a picture.
- Use the 39-record list as the review queue for Nathan, not an automatic move.

**Documents.** `sourceLine` matching "Publication | date", or a newspaper named in the title, flags 25 documents with precision 0.96 (24 of 25; the false positive is the open letter 31306) and recall 0.83.

## 3. Model

- **One section per entry.** `photographs`, `documents` and `articles` are separate sections, each with one entry type, and a Craft entry belongs to exactly one section. A record cannot be both an article and a photograph.
- **Nothing records the form an item survives in.** No field across the install has a name meaning format, medium or carrier. Photographs have free-text `creditKind` and `creditProcess` (for example "original newspaper purchased…", "jpeg"). Asset fields `provenanceKind` (legacy-mirror, legacy-wordpress, outside, commissioned, donated) and `assetRole` (current-mark, decoration, former-mark) say where a file came from, not what it is a copy of.
- **Articles can hold a scan.** The article layout has `featuredImage`, `recordImages` and `recordDocuments`, and documents have `documentFiles`. In use: 67 articles have `recordImages` and 25 have `featuredImage`. The archiveMedia asset layout already carries photograph metadata: `photoSourceCode`, `photoCredit`, `photoCaptionExt`, `photoPeople`, `photoPlaces`, `photoArticles`, `legacySourcePath`, `creator`, `dateAsPrinted` and `source`. Note that only 171 of the 1,563 photographs have any asset in Craft: 113 `featuredImage` and 58 `recordImages`. The rest exist only on the mirror.
- **Cross-section duplicates.**
  - Two legacy pages are held in two sections: `perkins-rsf-1957.htm` is article 1434 and document 27374 (the same Perkins 1957 study, twice), and `lw2304a.htm` is article 865 and photograph 3179.
  - Two assets (1812 and 1813) sit on both article 1444 and photograph 2689.
  - `photoArticles` links 54 photographs to articles, but it is an illustrates link, not identity.
- **Verdict.** "One identity, with the scan as how we hold it" is half supported: an article entry can carry its page scans as assets with their own credit and source metadata. Three things are missing:
  1. A field on the article (or the asset) naming the carrier: clipping, magazine pages, transcription only, web.
  2. An asset role meaning "facsimile of this record", as distinct from an illustration.
  3. A home on articles for the photograph-only fields (`creditRaw`, `creditDpi`, `creditProcess`, `photoSequence`, `archivalFiles`), unless these are moved to the asset.
