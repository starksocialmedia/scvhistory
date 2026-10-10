# What the archive's images are (9 October 2026)

Claude, for Nathan (brief item 15). Read only: nothing in the database was written, nothing committed.

## What was read, before any number

- **The files (through the scan's output):** storage/runtime/generated-scan.json, written 15:24 today by build_withheld_media.php. `_generated_scan.php` opened every asset's files: the master as it arrived (on Reggie, or held in the repo and matched by checksum), Craft's stored copy, and any manifest the file points to. For each asset it records `class` (generated, text-prompt-step, generative-edit, none-found), `masterRead` and `usedBy`. I did not open the files again; I read what the scan wrote (see "Read from a description").
- **The records:** a read-only query of all 6,978 assets for Craft's `kind`, path, `assetRole`, `provenanceKind`, `source`, `sourceUrl`, `legacySourcePath`, `enhancedFrom`, `enhancementMethod` and `contentCredentials`, with the live entries and categories related to each, queried about 16:10 (storage/runtime/scratch/claude_images_census.php and .json).
- **The text_to_image step:** inventory/review/text-to-image-step-2026-10-09.md, for which edits carry it.
- The scan's `usedBy` (15:24) and the live query (16:10) differ for one asset, Couts's #27387, back on #323 at 15:59. The counts below use the live query.

## The counts

**Images:** 6,886 of the 6,978 assets (the rest are 91 PDFs and 1 Word file).

**On a live record:** 4,769. **On none:** 2,117.

The 4,769 on records, each counted once:

| What it is | On records | Master read | Only Craft's copy read | Marker in the file |
|---|---:|---:|---:|---|
| Photographs and scans, no edit recorded, no marker in the file | 4,722 | 4,625 | 97 | none |
| Enhanced pairs (edited asset, its `enhancedFrom` original on the same record) | 20 | 20 | 0 | 15 text_to_image, 5 generative edit |
| Crops of an original (`enhancedFrom` set, original on the same record) | 5 | 5 | 0 | none |
| Marks: logos and seals (`assetRole` current-mark 19, former-mark 3) | 22 | 22 | 0 | 4 generative edit |
| Generated | **0** | | | |
| An edit recorded with no original linked | 0 | | | |
| A marker in the file with no edit recorded | 0 | | | |

**The 4,722:**
- 4,625 were checked against the master: 4,578 on Reggie, 47 held in the repo and matched by checksum.
- 97 were checked only against Craft's stored copy. Craft re-encodes on import, so "no marker" there says little. By provenance: 61 from the WordPress media library (no master anywhere), 26 from the legacy mirror whose master was not found, 9 with no provenance, 1 outside.
- 25 of the 4,722 are the originals shown beside the 20 enhanced pairs and 5 crops.
- "No edit recorded" means the archive records no edit of its own. It does not mean the file was never edited. 2,945 of the masters name Photoshop: Leon's preparation of his own scans (publisher-edits-census-2026-10-09.md). TODO's 99 portraits "with no edit recorded, to verify" are among these.

**The 20 enhanced pairs:**
- **15 carry the Firefly Image 5 text_to_image step** (the scan's text-prompt-step class): the fourteen in text-to-image-step-2026-10-09.md plus Couts #27387. Perkins #27381, Scofield #27383, ap1334 #31387, lw2054 #31391, lw2529 #31395, rn3002 #31398, Kevin Gary Lynch #31404, Manly #31406, lw2317a #31408, rr1 #31423, sc9010 #31427, Keith Richman #31447, sc9501 #31449, lw2178 #31472, Couts #27387.
- **5 carry a generative edit without that step:** the three plain enlargements, López #31474, Randy Wicks #38450 and Kellar #38452 (sc1310); Frémont's upscale #28814; Leon Worden's edges filled and upscaled #27396.

**The 5 crops:** John Boston #31938, Tom Frew IV #31935, Michele Jenkins #31932, John Amos Ward #31258, JereAnn Bowman #31257. No marker in any.

**The 4 marks with a generative edit (unruled):** Newhall Elementary #29696, Newhall School District #29439, State Senate seal #29400, Canyon High #29298.

**Generated on a record: 0.** The one file the scan calls generated, Bill Cooper's campaign image #38464 (gpt-image, no ingredient), is on no record.

**Off records (2,117):**
- 2,084 have no marker.
- 33 have a marker: 14 text_to_image, 18 generative edit, 1 generated.
- With Wiley #1658 and the Newhall Elementary original #29698, whose records name a generative tool, that makes the 35 that a rebuild of the withheld list should give: the 36 of 15:24, less Couts.

**Provenance unknown:** 11 images have no master read and no `provenanceKind`, `source`, `sourceUrl` or `legacySourcePath` at all. 9 of them are on live records:
- lp_santacruzsentinel082785.jpg #26982, on #26983;
- edwin-bryant.png #1671, on #313;
- story-of-our-valley-collection-1200x675.png #1655, on #873;
- ridge-route.jpg #1197, rancho-camulos.jpg #1195, lang.jpg #1193 (on #31374 and #609), lake-hughes.jpg #1191 (on a category), harry-carey-ranch.jpg #1189, fort-tejon.jpg #1187: the place images.

Taken more widely, 124 images have no master read; 97 of them are on records.

## One sentence Nathan can say

> Of the 6,886 images the archive holds, 4,769 are on a live page, and of those 4,722 are photographs and scans with no edit recorded and no generative marker in the file, 20 are our own enhanced versions shown beside their originals (15 of them through Firefly's Image 5 step, which records generated content combined with the photograph), 5 are crops, 22 are logos and seals (4 edited in Firefly), and not one is a generated image.

## Read from a description

- **The file classes are the scan's output, not a fresh reading of the files.** generated-scan.json is a record of what `_generated_scan.php` found when it opened each file today, so it is one step from the source. I did not reopen the files or the manifests.
- **"Marker" means the scan's marker strings**: C2PA claim, trainedAlgorithmicMedia, Firefly, text_to_image, gpt-image and the like. A file with no marker may still have been edited, composited or generated by a tool that writes none. A composite is invisible to metadata (publisher-edits-census-2026-10-09.md).
- **Which edits are which**, beyond the scan class (Leon Worden's fill, the three plain enlargements), is read from each asset's `enhancementMethod`, a record.
- **The 2,945 Photoshop masters** figure is from publisher-edits-census-2026-10-09.md, not re-counted.
