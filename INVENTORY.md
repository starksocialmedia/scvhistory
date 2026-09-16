# SCVHistory.com content inventory

Task 1 of HANDOFF.md. Read-only survey of the legacy mirror on Jordy. This file is not a content model. Mapping these page types onto Craft is Task 2.

## Scan

- Date: 2026-09-15
- Machine: MacBook
- Source: `/Volumes/Jordy/SCVHistory/scvhistory.com`
- Drive treated as read-only. Nothing was written under `/Volumes/Jordy`.
- File list: copy of `scvhistory-manifest-2026-08-20.sha256` (already on the drive) into gitignored `inventory/raw/`
- Byte totals: batched `lstat` walk of the live tree, 1,056 batches, 0 errors
- Page types: parsed 9,430 editorial `.htm`/`.html` files. Did not parse the 24,261 HTML files under `scvhistory/files/` as editorial pages (Apache indexes and generated document viewers)
- Scripts: `scripts/inventory/`
- Manifest file count: 734,889. Live walk file count: 734,880. Difference of 9. Likely AppleDouble `._*` files and similar crawl artifacts, not missing editorial pages

## Totals

| Measure | Value |
| --- | --- |
| Files (live walk) | 734,880 |
| Files (20 Aug 2026 manifest) | 734,889 |
| Total size | 655.59 GiB (703,934,395,378 bytes) |
| Editorial HTML classified | 9,430 |
| HTML under `scvhistory/files/` (not editorial) | 24,261 |
| HTML total | 33,691 |

Almost all of the bytes are under `scvhistory/files/` (701,592 files in 1,043 package directories, plus 8 loose files). Public web images live mainly in `gif/` (23,445 files). Editorial pages live mainly as loose `.htm` files in `scvhistory/`.

## File counts and size by extension

Counts from the manifest. Bytes from the live walk. Extensions with a `?` are crawl artifacts (URL query strings kept in filenames). A handful of `._*` AppleDouble files appear as bogus extensions and are not listed below.

| Extension | Files | Size | Notes |
| --- | --- | --- | --- |
| tif | 3,625 | 349.74 GiB | All under `scvhistory/files/`. Archival scans |
| jpg | 472,147 | 191.55 GiB | Web images, thumbs, flipbook page images |
| pdf | 1,525 | 64.46 GiB | Document packages under `files/` |
| png | 103,535 | 42.03 GiB | Mostly flipbook UI and page renders |
| js | 61,901 | 1.87 GiB | Flipbook viewers, not hand-written site chrome |
| tiff | 331 | 1.67 GiB | Same role as `tif`, also only under `files/` |
| xml | 51,145 | 1.33 GiB | Flipbook/package metadata |
| jp2 | 658 | 0.92 GiB | JPEG 2000, under `files/` |
| mov | 1 | 0.62 GiB | Single QuickTime file |
| html | 24,108 | 0.32 GiB | Mostly `files/` viewers and Apache indexes |
| htm | 9,583 | 0.20 GiB | Editorial pages, almost all in `scvhistory/` |
| css | 4,040 | 0.16 GiB | Site chrome plus flipbook CSS |
| pptx | 1 | 0.12 GiB | |
| mp4 | 1 | 0.05 GiB | |
| gif | 1,310 | 0.03 GiB | Icons, old thumbs, site chrome |
| docx | 5 | 5.0 MiB | |
| mp3 | 260 | 4.8 MiB | |
| txt | 15 | 3.8 MiB | |
| ogg | 259 | 2.7 MiB | |
| Other (fonts, doc, svg, ico, midi, xls, rtf) | few hundred | under 2 MiB combined | |

TIFF plus TIF is 3,956 files and about 351 GiB. That is more than half the drive by bytes, and none of it sits in `gif/`. HANDOFF.md mentions blocked TIFFs in `/gif/` on the live site. On this mirror, `gif/` has no `.tif`/`.tiff` files.

## Top-level directory structure

Paths below are relative to `/Volumes/Jordy/SCVHistory/scvhistory.com`.

| Path | What it holds |
| --- | --- |
| `index.htm`, `index.html` | Duplicate copies of the site home page |
| `obits.htm` | Obituaries and testimonials index |
| `gif/` | Public web images: 22,038 files at the top level, plus `galleries/`, `mugs/` (338 portraits), `ring/`, `thumbnails/`, `events/`. JPEGs named after item IDs (`lw3752t.jpg`) and a few PDFs |
| `icons/` | Nine Apache directory-listing GIFs (`folder.gif`, `image2.gif`, and so on) |
| `include/` | Shared CSS, header JS, fonts, FlexSlider, WOWSlider, OpenSeadragon |
| `mentryville/` | Friends of Mentryville minisite (story, directions, donors, one PDF trail map, one dated subdirectory) |
| `oldtownnewhall/` | Old Town Newhall Gazette and columnist minisite (`gazette/`, `patti/`, `pauline/`, `rioux/`, `whyte/`, `newhall/`, `news/`, `signal/`) |
| `orig/` | One leftover copy of a Jerry Reynolds Signal chapter under `orig/scvhistory/signal/reynolds/` |
| `pico/` | Six HTML documents on Placerita gold and Pico Canyon |
| `scvhistory/` | Main archive: about 8,589 editorial HTML files at this level, plus `files/`, `signal/`, and one leftover GIF |
| `scvhistory/files/` | 1,043 document packages. Each package is typically a PDF and/or TIFF set, JPEG page images, a generated HTML flipbook (`basic-html/`, `mobile/`), and an Apache `Index of` page |
| `scvhistory/signal/` | Signal newspaper series: Perkins, Reynolds, Worden, Boston, Manzer, Newsmaker, coins, Iraq |
| `warmemorial/` | Santa Clarita Valley War Memorial: war-index pages and one HTML profile per casualty |
| `pass1.log`, `pass2.log` | Crawl logs on the parent `/Volumes/Jordy/SCVHistory/` folder, not site content. Not inventoried as pages |

Parent folder `/Volumes/Jordy/SCVHistory/` also holds the sha256 manifest used for counts. Inventory of editorial content is the `scvhistory.com` tree only.

## Recurring page types

HTML was classified from titles, paths, and simple size/link/image counts. The 24,261 HTML files under `scvhistory/files/` were counted from the manifest only (not parsed). Counts for those two `files/` rows are therefore path-based.

| Type | Count | What it is | Example paths |
| --- | --- | --- | --- |
| Apache directory listing | 20,702 | `Index of /scvhistory/files/...` pages captured from Apache. Not editorial | `scvhistory/files/sfdcoronersinquest/index.html`, `scvhistory/files/fillmore1930yearbook/index.html`, `scvhistory/files/stansell2014/index.html` |
| Object / photo page | 4,871 | The main archive unit. Title `SCVHistory.com {ITEMID} \| {Category} \| {Headline}`. One item, image, caption, credit footer, related thumbs | `scvhistory/sw1902.htm`, `scvhistory/ov1001.htm`, `scvhistory/lw3094.htm` |
| Flipbook / yearbook package HTML | 3,559 | Generated page-turners inside `files/*yearbook*` (basic-html, mobile, named `.html` wrappers). Same pattern exists in non-yearbook packages and is counted in the Apache row when the path has no "yearbook" | `scvhistory/files/fillmore1930yearbook/fillmore1930yearbook.html`, `scvhistory/files/fillmore1930yearbook/files/basic-html/page1.html`, `scvhistory/files/actonschool1968yearbook/index.html` |
| Article / essay / reprint | 2,430 | Editorial HTML whose title has a category but no item ID: `SCVHistory.com \| {Category} \| {Headline}`. Mix of long essays, transcribed documents, biographies, news reprints, and some topic landings that do not use the "History In Pictures" title | `scvhistory/pollack0710tunnel.html`, `scvhistory/annstansell_damvictims022214.htm`, `scvhistory/costanso-diary.htm` |
| Obituary | 635 | Individual obituary or testimonial pages, mostly linked from `obits.htm`. Title contains "Obituaries" or the path/title has "obit" | `scvhistory/tlp_sg041928.htm`, `scvhistory/sg19490630swall.htm`, `scvhistory/dn112503.htm` |
| Topic or place index | 554 | List pages titled `Santa Clarita Valley History In Pictures - {Place or topic}`. Thumbnail grids linking to object pages | `scvhistory/acton.htm`, `scvhistory/bealescut.htm`, `scvhistory/people.htm` |
| Signal newspaper page | 450 | Columns and book chapters under `scvhistory/signal/` | `scvhistory/signal/perkins/part01.html`, `scvhistory/signal/reynolds/index.html`, `scvhistory/signal/worden/lw012496.htm` |
| Old Town Newhall minisite | 275 | Gazette issues and named columnists | `oldtownnewhall/index.htm`, `oldtownnewhall/gazette/gazette1201-history.htm`, `oldtownnewhall/pauline/ph030497.htm` |
| Yearbook landing | 122 | Editorial wrappers in `scvhistory/` that introduce a yearbook package in `files/` | `scvhistory/schoolyearbooks.htm`, `scvhistory/fillmore1930yearbook.htm`, `scvhistory/actonschool1968yearbook.htm` |
| War memorial profile | 36 | One page per SCV casualty | `warmemorial/ww2_johnward.htm`, `warmemorial/korea_albertthomas.htm`, `warmemorial/terror_brianprosser.htm` |
| Unclassified / empty | 33 | Almost all have empty `<title>` and zero text. Stubs, broken saves, or funeral-home "link" pages | `scvhistory/lw2152b.htm`, `scvhistory/chapelofthevalleylink.htm`, `scvhistory/newhall.htm` |
| Mentryville minisite | 9 | Friends of Mentryville pages | `mentryville/index.html`, `mentryville/mstory.htm`, `mentryville/mdirections.htm` |
| Pico minisite | 6 | Short document set, some with item IDs in the footer | `pico/ap9011.htm`, `pico/fema030398.htm`, `pico/prudhomme1922hssc.htm` |
| War memorial index | 4 | Home plus WWII, Korea, and "terror" casualty lists | `warmemorial/home.htm`, `warmemorial/ww2casualties-index.htm`, `warmemorial/koreacasualties-index.htm` |
| Home | 2 | Identical home pages | `index.htm`, `index.html` |
| Obituary index | 1 | Master list of obituaries | `obits.htm` |
| Orig copy | 1 | Duplicate Reynolds chapter | `orig/scvhistory/signal/reynolds/part17.html` |
| Unreadable | 1 | AppleDouble sidecar, not a page | `._index.htm` |

There is no separate HTML template named "biography". People show up as object pages (category People: 287), as articles (category People: 163), and as obituaries (635). Place content is the 554 History-in-Pictures indexes plus thousands of object pages whose category is a place name (Newhall 416, Saugus 139, Acton 121, and so on).

### Object-page categories (top of 4,871)

These are the second field of the page title, Leon's working categories, not a controlled vocabulary.

| Category | Object pages |
| --- | --- |
| Film-Arts | 444 |
| Newhall | 416 |
| People | 287 |
| St. Francis Dam Disaster | 146 |
| Rancho Camulos | 141 |
| Saugus | 139 |
| Mojave Desert | 139 |
| Melody Ranch | 137 |
| Acton | 121 |
| Pico Canyon | 120 |
| William S. Hart | 117 |
| Powerhouse Fire | 105 |
| Tataviam Culture | 100 |
| Valencia | 97 |
| Canyon Country | 90 |
| Ridge Route | 87 |

Article pages use the same category slot in the title (People 163, Tataviam Culture 121, Rancho Camulos 91, and so on) but omit the item ID.

## Recurring metadata

Seen on object pages and many articles. Percentages are of the 9,430 editorial pages unless noted.

**Item ID.** Title pattern `SCVHistory.com {ID} \|`. 4,871 object pages have a parsed ID. Prefixes are collector or series codes, not file types. Top prefixes: LW 2,459, AP 237, AL 211, HS 163, HB 108, GB 100, JK 91, SC 81. The same ID appears in the footer (`SW1902: 9600 dpi jpeg from original postcard courtesy of Stan Walker.`), in `gif/` filenames (`sw1902.jpg`, `sw1902t.jpg`), and often in a `files/{id}/` package when an archival scan exists.

**Category.** Second pipe field in the title (`Newhall Pass`, `Film/Arts`, `Obituaries`). Working heading, not LCSH.

**Headline / date.** Third pipe field. 3,049 of 4,871 object-page titles contain a four-digit year. Dates also appear as `n.d.`, spans (`1900-1920`), and exact days (`11-23-1952`).

**Credit / source footer.** 5,328 editorial pages match "collection of", "courtesy of", "photo by", "photograph by", "dpi jpeg", or "from photocopy". Standard object-page line is `{ID}: {dpi} dpi jpeg from {original|photocopy|copy print} {courtesy of|collection of|purchased by} {name}`. 4,253 of 4,871 object pages have this. Example: `OV1001: 19200 dpi jpeg from smaller jpeg of 6x8-inch print | San Fernando Valley Historical Society Collection at California State University, Northridge. Online image only.`

**Captions.** 7,079 pages use `thumbcaption`, `inversecaption`, or a class containing "caption". Object pages caption the main image and a column of related-item thumbs (`Prairie Trails 1920`).

**Bylines.** 885 pages. Concentrated on Signal columns (410 of 450) and Old Town Newhall (160 of 275), not on object pages (86 of 4,871). Named authors also appear in article titles (`By Dianne Erskine-Hellrigel`, `Compiled by Ann Stansell`).

**Archival download.** Object pages often link `Download archival scan` to a `.tif` under `gif/` or `files/` (`https://scvhistory.com/gif/sw1902_orig.tif` on `sw1902.htm`). The TIFFs that actually sit on this mirror are under `scvhistory/files/`, not `gif/`.

**Related items.** Object pages end with a thumbnail strip of other item IDs. Useful later for relations, not a structured field today.

**HTML head.** `og:title`, `og:image`, `og:url`, `link rel="canonical"`, windows-1252 or utf-8, shared `include/header*.css`. Body still contains leftover ad, Quantcast, Privy, UserWay, and gtag script tags. HANDOFF.md says a Python pass already stripped some of that from the mirror; remnants remain on the pages sampled.

**War memorial extras.** Profile titles carry service branch and death date (`John Amos Ward, U.S. Army, d. 2-1-1945`). Credits on those pages are often family (`Image courtesy of their granddaughter, Tara Vaughn Garza`).

## Limits and stop rules

- Jordy can dismount. Scripts check `/Volumes/Jordy/SCVHistory/scvhistory.com` before work and during classify (every 200 pages) and size-walk (every batch). They exit without writing to the drive.
- Raw scan files stay in gitignored `inventory/raw/`. Do not commit the 96 MB manifest.
- Classifier "article" is a remainder class: editorial HTML that is not an object page, index, obituary, Signal page, or minisite. Some of those 2,430 pages are really topic landings (`tataviam.htm`, `timeline.htm`). Do not treat the number as a clean essay count.
- Flipbook HTML inside non-yearbook `files/` packages is lumped with Apache listings (20,702) because those paths were not parsed. The 3,559 yearbook HTML count is the subset whose path contains "yearbook".
- No bulk copy. No Craft imports. No production contact.

## Next

Task 2: `CONTENT-MODEL.md` mapping these page types onto `config/project/`, with gaps and questions for Leon. Entity-first import still applies: people, places, organizations, and taxonomies before articles.
