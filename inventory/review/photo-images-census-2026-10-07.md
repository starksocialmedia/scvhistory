# Photograph images census, 7 October 2026

Question (Nathan): of the photograph records with no image in Craft, how many have an image on Reggie that was simply not imported, and how many have no image anywhere?

Read-only. Scripts and scratch output: `storage/runtime/photo-images/` (`dump.php`, `index.py`, `scan.py`, `scan2.py`, `final.py`). Per-record results: `photo-images-census-2026-10-07.json`.

## The set

Live photographs (section `photographs`, status live, no drafts or revisions): 1,563. Records with an asset in any Assets field on their layout: 114. **Records with no asset: 1,449, not 1,392.** The type census figure of 171 counted field uses (113 `featuredImage` plus 58 `recordImages`); 57 records have both, so 114 records hold the 171.

Every one of the 1,449 carries `legacyUrl` and `legacyKey`; 1,445 also carry `photoSourceCode`. All legacy pages are under `/scvhistory/`. Reggie holds `scvhistory.com/` only (plus manifests and logs); no `/old/` page paths are used by these records, and no meta-refresh redirects were met.

## Counts

"Text page" means the type census (`type-census-2026-10-07.json`) put the record in a non-picture class (program, article, ad, letter, brochure, document, web article). Everything else is "picture".

| Class | Picture | Text page | Total | Files to import | Size |
|---|---|---|---|---|---|
| A. Image on Reggie (main image found) | 1,257 | 82 | 1,339 | 1,339 | 6.15 GB |
| B. Multi-page document on Reggie (flipbook or PDF) | 41 | 48 | 89 | 1,602 | 57.6 GB (4.1 GB without the TIFFs) |
| C. Page missing, image found by code | 3 | 0 | 3 | 4 | 1.8 MB |
| D. Page on Reggie, image missing | 2 | 4 | 6 | 0 | |
| E. Nothing anywhere | 4 | 0 | 4 | 0 | |
| F. No image by nature (video, essay, index page) | 6 | 2 | 8 | 0 | |
| **Total** | **1,313** | **136** | **1,449** | **2,945** | **63.7 GB** |

So 1,431 of 1,449 (A+B+C) have their image on Reggie and were simply not imported. 10 (D+E) have lost their image. 8 never had one.

## How the image was chosen

- Main image: the first `<img>` on the page that is not a related-record thumbnail (`alt="thumbnail"`), the site banner, an icon or a tracking pixel. Absolute `scvhistory.com` URLs are mapped to the mirror. File names are matched case-insensitively.
- Best version: the largest in pixels among the inline image, the link wrapping it (usually `_large.jpg`, the FULL VIEW), and any other linked image whose name starts with the same stem (`_orig`, `_super`, `b` closeups). For class A the best file is `_large` for 1,117 records, the plain inline file for 210, `_orig` for 11, `_super` for 1. Median width 1,600 px; quartiles 1,600 and 2,400.
- Flipbooks (`<iframe src="files/lwNNNN/...">`): the page images in the folder. TIFF masters when present (501 files, 54.1 GB), else root JPEGs (987), else `data1/images` (102), else the largest PDF (12). A lighter import using the JPEG or PDF instead of the TIFFs is 1,249 files, 4.1 GB.

## Examples

- A: #5763 lw9502, `gif/lw9502_large.jpg`, 2400 x 3415, 3.8 MB.
- A, very large: #5639 lw3786, `gif/lw3786.tif`, 692 MB; #5143 lw3315 (class photo), page shows no inline image but `gif/lw3315_orig.jpg` (428 MB) and `_super.jpg` are on Reggie. Six class A files exceed 100 MB; without them class A is 4.2 GB.
- B: #5691 lw7601, flipbook with page JPEGs and `_orig.tif` masters in `scvhistory/files/lw7601/`. #2773 lw2083 (Popular Mechanics) is counted here: 17 page images `gif/lw2083a-q.jpg` shown on sub-pages.
- C: #3023 lw2248 and #2987 lw2214 (photo galleries; page gone, images in `scvhistory/files/lwNNNN/`), #2913 lw2152a (`gif/lw2152a.jpg`).
- D: #3741 lw2462b and #4435 lw2724 (page `src` file absent, only the thumbnail); #5477 lw3633 (links `lw3633_orig.tif`, absent); #5423 lw3575 (thumbnail only); #4751 lw2972 and #4753 lw2973 (flipbooks with only thumbnails mirrored).
- E: #5735, #5737, #5739 (lw9410b, c, d, Cronan home) and #4475 (lw2763b): no page and no file of that code anywhere on Reggie.
- F: videos #5465, #5303, #5267, #4819; essays #5767 lwhist, #2691 lw100604; index pages #2869 lw2142, #2797 lw2101.

## Caveats

- 151 class A pages (and #5477 in D) link an archival original that is not on Reggie (188 `_orig.tif`, 16 JPEGs). Those records are counted as found because a display image exists, but the master is missing. 13 pages wrap the image in a link to a file that is absent.
- 393 class A pages carry further inline images (secondary photos, maps, clippings). Only the main image is counted.
- D and F cases with a thumbnail (`...t.jpg`, about 120 px) are not counted as found.
- Text-page split relies on the type census labels, which were partly heuristic.
- Pixel sizes are from `sips`; PDFs have no pixel size.
