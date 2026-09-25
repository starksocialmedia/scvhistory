# The Reggie mount, the lightbox source, and enhanced derivatives

2026-09-20. Mount verified at `/mnt/reggie`, mirror root
`/mnt/reggie/scvhistory.com`, read-only, readable from inside the container.
23,365 distinct image filenames across `gif`, `orig`, `icons`, `pico`,
`mentryville`, `oldtownnewhall` and `warmemorial`. `gif` alone holds 22,011.

## What now resolves

| | |
|---|---:|
| **photograph records resolving to an image** | **1,318 of 1,544** |
| — already an asset in Craft | 3 |
| — **on the mount, not yet fetched** | **1,315** |
| — resolving to neither | 226 |
| **enlarge links resolving to a larger file** | **1,773 of 1,806** |
| — target on the mount | 1,784 |
| — of those, genuinely larger than the source | 1,773 |
| — target on neither | 22 |
| **inline images on the 80 Reynolds chapters** | **37** |
| — with a distinct larger file (a `_large` target) | **0** |
| — with a bigger file of the same name on the mount | **14** |
| — same size on the mount | 20 |
| — not on the mount | 3 |

The 2,473 figure in the brief counts image-to-image *references*. Deduplicated,
and with self-references dropped, there are **1,806 distinct pairs**; 613 of the
references pointed a file at itself, which is not an enlargement.

**A finding I did not expect: 293 of our 568 held assets have a larger file of
the same name on the mount.** The fetched copies came from the live site, which
serves recompressed images; the mirror has what was uploaded. Reynolds's own
portrait is 11,845 bytes here and 17,586 on the mount. This is bytes, not
verified pixel dimensions, so some of it will be compression rather than
resolution — but it means the mount is a better source than the live site for
files we already hold, not only for ones we lack.

**Reynolds has no `_large` targets at all.** Its 37 inline pictures are `ap####`
and `lw####` files that the legacy pages linked to other pages, not to bigger
scans. The 14 that improve do so by being the same file, less compressed.

## Serving: index as assets, not directly from the mount

**Directly from the mount does not survive deployment.** `/mnt/reggie` is a DDEV
bind on this machine. Cloudways has no such mount and will not get one: the
drive is a 2 TB local disk. A template that reads from it works here and 404s on
staging, which is the worst possible failure because it passes every check we
run locally.

Craft transforms are the second reason. A transform needs the file inside a
volume; from outside one, there are no transforms, so every tile serves a
full-size original and the Photos grid becomes tens of megabytes. Titles,
captions, alt text and the control panel all follow the same boundary.

So: **index as assets.** But not 658 GB, and not 23,365 files.

### The order to do it in

| pass | files | why |
|---|---:|---|
| 1. captioned | **1,405** | every file the extraction holds a real caption for. The caption script can then apply all 5,220 records instead of 89, which is the largest single gain on the board |
| 2. photograph plates | **1,315** | one picture per photograph record, so the largest section stops being 1,541 pages that say the image is not held |
| 3. enlarge targets | **1,773** | the `_large` files, which is what makes the magnifier mean something |
| 4. better copies of what we hold | **293** | replace fetched recompressions with the mirror's originals |

The passes overlap; the union is well under 4,000 files, not 23,365. Everything
outside them is document-scan page images under `scvhistory/files/*/files/`,
which are a different problem and belong to the documents section.

I have not built any of this. It is an importer with the same fidelity and
read-back standards as the photograph rebuild, and it wants your decision on
order first.

## The lightbox source

The magnifier now renders only where there is something bigger to open.

Before: every inline picture carried a magnifier that opened **the identical
URL**. The control promised an enlargement and delivered the same pixels.

The rule, and one place where I have read your brief rather than followed it
literally: *"if the resolved file is not larger than the thumbnail, or not held,
render no magnifier"* would remove the lightbox from the whole site today, since
0 enlarge targets are in Craft. That would also remove it from the gallery you
asked me to wire up two messages ago. So the magnifier renders when **either**

- a distinct larger file is held, which is the enlargement proper; **or**
- the asset's own pixels exceed what is being drawn — a 280px column against an
  800px scan, or a 4:5 tile crop against the full file.

Neither is "the same file the tile shows" in any sense a reader would care
about. Say the word and the second clause comes out in one line.

Effect today:

| | inline | grid |
|---|---:|---:|
| images | 76 | 398 |
| larger file held | 0 | 0 |
| own pixels bigger, magnifier kept | 56 | 125 |
| **magnifier removed** | **20** | **273** |

293 controls that did nothing are gone.

`build_enlarge_index.php` writes `templates/_data/enlarge.json`, 1,806 entries,
each carrying the target filename plus an asset id where we hold it and a
`drive` flag where we do not. It writes one file and saves nothing.

## Enhanced derivatives

**The principle first.** An upscaled or colourised version is a new object, not
a better copy. The pixels were invented by a model, however plausible. An
archive that replaces the scan with the enhancement has destroyed the only thing
it was holding.

So the enhancement is its own asset, related back to the original, and the
original stays the record.

`add_enhanced_derivative_fields.php` adds four fields to the `archiveMedia`
asset layout, on their own **Enhancement** tab so nobody fills them in on an
original:

- `enhancedFrom` — the original asset. The relation lives on the derivative and
  points back, so nothing about the original changes.
- `enhancementMethod` — what was done and with what. "Topaz Gigapixel 7, 4x,
  Standard v2" rather than "AI upscale". Nobody can judge the result without the
  process, including us in five years.
- `enhancedBy`, `enhancedDate`.

Rendering, built and ready:

- an **Enhanced** chip on the picture itself, top left
- method, hand and date under the caption
- the lightbox grows a two-way toggle, **Original scan** / **Enhanced**, and
  **opens the original first**, always
- the citation names the original, because the citation is built from the record
- **the JSON-LD names the original**, and now says so in a comment. If a
  derivative is ever passed to it, it publishes that derivative's original
  instead. Structured data is an assertion to the rest of the web about what
  this archive holds, and what it holds is the scan.

Nothing renders differently until a derivative exists: a missing `enhancedFrom`
reads as "this is an original", so all 568 assets behave exactly as before.

`check_render.php`: 27 pages, no failures.
