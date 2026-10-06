# /files/ crawl: slideshow/folder mismatches (lw3257, lw3457, lw3487)

Checked 2026-10-03 PT using read-only live fetches at about 1 request/s. Fetched: the three record pages, their WOWSlider `files/<code>/<code>.htm` configs, lw3267.htm, the `files/lw3267/` and `files/lw3267/data1/images/` listings, and lw3267's 14 slide files, which were downloaded only to hash them. Everything else was checked against the files already downloaded under `/workspace/files-crawl/`.

I could not look at the images: the image viewer returned "file not found" for every contact sheet I made. The verdicts therefore rest on metadata: page text, slider config, Apache listings with mtimes, EXIF (PIL), sha256, and a 16×16 difference hash (dHash) computed locally.

dHash note: two different photos in the same folder are never closer than 93/256 bits; copies of the same photo come out at 0–9.

Evidence files are in `/workspace/files-crawl/_state/verify/`: the fetched pages, `lw3267_data1_images/hashes.json` and `exif_ahash.json`.

| record | verdict | one-line reason |
|---|---|---|
| lw3257 | **misfiled** (confirmed) | 14 lw3267 slides in lw3257's folder are byte-identical to lw3267's own slides and unused by lw3257's slideshow |
| lw3457 | **incomplete slideshow** | the folder is the full camera shoot from the day in the title; the page links it as "Download more images here" |
| lw3487 | **not misfiled; duplicate copies** (closest of the four categories: incomplete slideshow, but it isn't actually incomplete) | the slideshow shows all 56 listing photos; the 49 extra files are near-duplicates of those same photos |

## lw3257: Photo Gallery: Largest Ancient Chumash Basket Ever Found (SBMNH 2015). Verdict: misfiled

- **lw3267 exists.** lw3267.htm is HTTP 200: "Photo Gallery: Arthur Charles Mentry's 1901 Blacksmith Forge Rediscovered in 2018." It is a separate record in the same 226-record live check (class live_image_ok, page image on /gif/). It has its own WOWSlider at `files/lw3267/lw3267.htm`, whose first caption reads: "Blum Ranch, 4-20-2018: Elizabeth Blum Billet discovers the name ''A.C. Mentry'' on the side of her forge. Photo: Paulette Tcherkassky." Its folder holds lw3267a–n.jpg plus lw3267a–n_large.jpg (28 files) and `data1/images/lw3267a–n.jpg` (14 files).
- **Same files, same sha256.** All 14 of `files/lw3257/data1/images/lw3267a–n.jpg` are byte-identical to `files/lw3267/data1/images/lw3267a–n.jpg` (14/14 sha256 matches; sizes 230,690–449,115 bytes).
- **Not used by lw3257.** lw3257's slider config lists only lw3257a–r (18 slides) and never mentions lw3267. Matching `data1/tooltips/lw3267a–n.jpg` thumbnails are also in lw3257's folder, so it looks like a whole WOWSlider export landed in the wrong folder.
- **Timing.** In lw3257's folder the lw3267 files are dated 2018-04-27 22:06; lw3257's own slides are dated 2018-04-22. lw3267's own copies are dated 22:10 and 22:11 the same evening. This fits the lw3267 gallery being exported into lw3257's folder first and then re-exported into lw3267's own folder.
- **Effect.** Nothing is lost: lw3267's folder has these 14 slides plus their originals. The 14 copies (and 14 tooltips) under `/workspace/files-crawl/lw3257/data1/images/` should not be attached to lw3257. lw3267's folder was not part of the 88-record crawl; only its 14 slides were fetched, for hashing.

## lw3457: Kingsburry House: Temporary Roof Repairs, 11-27-2018. Verdict: incomplete slideshow (not misfiled)

- **Page.** "Heritage Junction Historic Park, November 27, 2018 — … SCV Historical Society volunteers Steve Martin and Sarah Brewer … temporary fix to the roof of the Kingsburry House …" followed by "LW3457: Download more images here" (link to `files/lw3457/`) and "Ditigal images by Leon Worden" (sic). The page itself presents the folder as holding more images than the slideshow.
- **Slideshow.** The config lists 6 slides, lw3457a–f.
- **Folder.**
  - 101 camera originals, IMG_5569.JPG through IMG_5669.JPG (with gaps). All are 5184×3888, about 5–6 MB each.
  - lw3457a–f.jpg plus 5 `_large` versions, and lw3457t.jpg, a 120×90 thumbnail.
- **EXIF.**
  - All 101 IMG files: Canon PowerShot SX710 HS, DateTimeOriginal 2018:11:27, 15:45:54 to 15:56:01. That is the date in the title, within one 10-minute window.
  - The lw3457 JPGs carry the same camera model. Each one's DateTimeOriginal matches IMG frames taken in the same second:

    | slide file | time | IMG frames from that second |
    |---|---|---|
    | lw3457a | 15:46:27 | IMG_5596–5599 |
    | lw3457b | 15:45:55 | IMG_5570/5571 |
    | lw3457c and lw3457t | 15:53:18 | IMG_5611/5612 |
    | lw3457d | 15:55:39 | IMG_5650/5651 |
    | lw3457e | 15:55:13 | IMG_5632–5635 |
    | lw3457f | 15:55:16 | IMG_5645–5648 |

  - The dHash match to those exact frames is weak. That fits the slides being cropped or edited from the frames.
- **No other record.** None of the IMG_ names contain an lw code, and none occur in any of the other 87 crawled folders.
- **Conclusion.** The folder is the full shoot that the 6 curated slides were taken from. The images belong to this record.

## lw3487: Charley Mack House, 22931 8th St., Property Listing 2019. Verdict: not misfiled; extras are duplicates (slideshow complete)

- **Page.** A copy of a real-estate listing ("FOR SALE: $1,150,000. Accessed January 29, 2019. Listed by: Alex Meguerditchian / Century 21", built 1929, Charley Mack). It is followed by "LW3487: Download original images here" (link to `files/lw3487/`).
- **Slideshow.** The config lists 56 slides. They are named `2439a69375ef9924e6b411efe568aeb4lm{0–55}xdw1020_h770_q80.jpg`, the hyphen-stripped forms of the root files below.
- **Folder.**
  - 56 matching originals, `2439a69375ef9924e6b411efe568aeb4l-m{0–55}xd-w1020_h770_q80.jpg` (mostly 1020×680), one per slide.
  - 49 `IS…0000000000.jpg` files (mostly 960×641).
  - lw3487.jpg (960×641).
- **The 49 IS files are copies of the same listing photos.** Comparing each IS file against the 56 originals with dHash:
  - 48 are within 0–9 bits of one of the originals. Different photos in this folder are at least 93 bits apart, so these are re-encoded copies, probably from a second listing source.
  - The 49th, ISmy0u1fat1dlo0000000000.jpg (960×578, an odd size), is 45 bits from original m20 and over 100 bits from everything else. It is most likely a cropped version of m20, but that is unconfirmed without seeing it.
  - lw3487.jpg is within 4 bits of original m0. It is probably the lead image.
- **No other record.** No EXIF in any file. No filenames contain an lw code, and none occur in other crawled folders.
- **Conclusion.** The "106 distinct images" flagged in the crawl summary came from counting filenames. There are about 56 unique photos, all of which the slideshow shows, plus duplicate copies. Nothing here looks misfiled or unrelated. For the archive, the 56 originals (or their slides) cover the content; the IS set can be treated as duplicates. ISmy0u1fat1dlo is the one to eyeball if you want to be sure.
