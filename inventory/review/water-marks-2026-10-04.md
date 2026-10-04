# Water body marks: unedited originals (4 October 2026)

Claude, read-only research for Nathan. Question: find the marks of the Newhall County Water District (NCWD), the Castaic Lake Water Agency (CLWA) and the Valencia Water Company (VWC) as the bodies themselves used them, since the files supplied (inventory/incoming/done/NCWD.png and CLWD.png) carry Adobe content credentials recording an Adobe Firefly edit.

Files saved in `inventory/sources/water-marks-2026-10-04/` with `manifest.json` (source URL, read date, sha256, bytes, pixel size, description). Nothing was written to the database or templates.

## The edited files, for comparison

| File | Size | Credentials |
|---|---|---|
| inventory/incoming/done/NCWD.png | 2700x2700 PNG, RGBA | C2PA manifest (Adobe): c2pa.created with digitalSourceType empty, then c2pa.edited by Adobe Firefly with digitalSourceType compositeWithTrainedAlgorithmicMedia |
| inventory/incoming/done/CLWD.png | 2700x2700 PNG, RGBA | the same pattern (Adobe C2PA, Firefly edit) |

The local exiftool is version 10.31, which predates C2PA and does not decode the JUMBF box; the manifest was read with `strings`. Every candidate below was checked the same way (C2PA, JUMBF, Firefly, trainedAlgorithmic) and none carries any of them. All the candidates predate generative tools (2002 to 2018).

## Recommendation

One document holds vector art of all three marks: the **Santa Clarita Valley 2008 Annual Water Quality Report**, the joint report of CLWA, NCWD, the Santa Clarita Water Division and VWC, made in InDesign CS3 on 5 June 2008 and posted by CLWA (Wayback capture 14 December 2009). Its page 1 carries the four suppliers' marks as vector paths. A zoom test at 2400 dpi shows clean curves, not pixels. The renders at 2400 dpi are the recommended originals:

| Body | Recommended file | Pixels | Kind |
|---|---|---|---|
| NCWD | `ncwd-2008-wqr-render-2400dpi.png` | 2000x2230 | render of vector, white ground |
| CLWA | `clwa-2008-wqr-render-2400dpi.png` | 1760x4200 | render of vector, white ground |
| VWC | `vwc-2008-wqr-render-2400dpi.png` | 2000x2040 | render of vector, white ground |

Source PDF kept beside them: `2008-scv-annual-water-quality-report_clwa-org_20091214202408.pdf`
URL: https://web.archive.org/web/20091214202408id_/http://www.clwa.org/h2oquality/pdfs/Santa%20Clarita%20Valley%202008%20Annual%20Water%20Quality%20Report%20.pdf

The renders exclude the report's own typeset captions under each mark (for example "NEWHALL COUNTY-WATER DISTRICT", "VALENCIA WATER COMPANY"), which are report text, not part of the marks. If a true vector file is wanted (SVG or a cropped PDF), the paths can be lifted from this PDF in Illustrator or Inkscape. Poppler's pdftocairo cannot crop PDF output, so I did not make one.

## Newhall County Water District

One design throughout: a navy square with wavy edges and a pale grey-blue offset shadow; a white N made of two drop shapes with a falling drop; the letters NCWD in a serif below. There are two lockups:

1. **NCWD only** (no full-name line): 2008 report (vector), ncwd.org header 2010 to 2014, clwa.org's `ncwd_logo.jpg` of 2002 (77x78), santaclaritawater.com 2014 (100x136).
2. **NCWD with NEWHALL COUNTY WATER DISTRICT** in small sans capitals under the wordmark: 2013 customer notice, Water Lines newsletters 2015 to 2017 (vector), the 2016 joint Q&A.

| Candidate | Source, capture | Pixels | Notes |
|---|---|---|---|
| **2008 report render** (recommended) | clwa.org, 2009-12-14 | 2000x2230 from vector | lockup 1, white ground |
| Water Lines Winter 2016-17 render | ncwd.org, 2017-02-20 (PDF created 2017-01-23) | 2700x3500 from vector | lockup 2, printed on the newsletter's gold ground; the newest use before the 2018 merger |
| 2013 notice, extracted | ncwd.org `CI/In_the_News/Notice to Customers 060413.pdf`, capture 2014-01-01 | 255x374 PNG | lockup 2, raster |
| ncwd.org header `img/logo.jpg` | 2010-05-14 (also at `themes/ncwd2/images/logo.jpg` 2014-01-01) | 978x190 | mark about 110 px tall, beside the name on a water photo |
| Legacy mirror | `scvhistory/ncwd040814.pdf` (NCWD press release, 8 April 2014) | embedded 158x192 | small |

NCWD's own website never offered anything larger than the 978x190 header; the 2015 WordPress uploads hold only small logos of partner programs. No SVG, EPS or AI file is captured on ncwd.org.

**Against the Firefly file:** NCWD.png is the same design and lockup 1 (no full-name line), placed on a square 2700 canvas. Shapes, colours and the serif wordmark match the 2008 vector. Firefly's version has softer, slightly blurred edges and a light halo on the shadow. Nothing in the design differs that I could see, so the edit looks like an upscale and cleanup, not a redraw.

## Castaic Lake Water Agency

One design from at least 2000 (clwa.org `clwa.jpg`, 99x219, captured 2000-08-16) to 2017: CASTAIC / LAKE in wide-spaced condensed capitals above a blue square; a translucent drop with a star highlight crosses the square's lower edge; WATER / AGENCY reversed out of a blue box below. There are two colourways:

- **Dark blue** (web and later print): clwa.org 2000 to 2015, the 2008 report, board memoranda of 2017 (mirror `files/clwa102517/clwa102517.pdf`, 121x265 embedded).
- **Process cyan** (print, 2003): the Water Currents newsletter, Winter 2003.

There is also a horizontal lockup (CASTAIC LAKE | drop | WATER AGENCY), in the November 2016 Q&A with NCWD (mirror `scvhistory/files/scvwa_qa1116/scvwa_qa1116_orig.pdf`, 471x88 raster only).

| Candidate | Source, capture | Pixels | Notes |
|---|---|---|---|
| **2008 report render** (recommended) | clwa.org, 2009-12-14 | 1760x4200 from vector | dark blue, white ground |
| Water Currents Winter 2003 render | clwa.org `Newsletters/Newsletter - Winter2003.pdf`, 2003-04-09 (PDF created 2003-01-14) | 1620x3860 from vector | cyan colourway; the newsletter's sky photo strip starts just below WATER AGENCY |
| clwa.org education logo | `education/images/CLWA_logo.gif`, 2009-12-14 | 100x250 | dark blue web copy |
| clwa.org theme logos 2011 to 2015 | `themes/castaic_lake/images/logo.jpg` and others | 54x140 to 88x212 | small |

**Against the Firefly file:** CLWD.png (the name is a slip; it reads Castaic Lake Water Agency) is the same design in the dark blue colourway, matching the 2008 vector: same lettering, square, drop and star. It sits on a square 2700 canvas with wide margins.

## Valencia Water Company

There are two marks.

1. **The droplet and pinwheel in a circle** (a drop with an eight-vane pinwheel in its bowl, in a ring). Used from at least 2002 to the end: the black `VWC_Logo2002.gif` hosted by CLWA; the 2004 valenciawater.com header; the 2008 report (blue, vector); a white-on-blue square with "Valencia Water Company" in script under the ring (180x181 in VWC's 2015 press release, and the 800x800 copy in the legacy mirror); the 2017 site's `service/images/vwc-logo.jpg` (105x105, white on blue); and the 2018 theme watermark.
2. **The VWC lockup**: a blue rounded bar with VWC in heavy sans capitals and the droplet and pinwheel (without the ring) in a white tile, with "Valencia Water Company" in a bold serif below. It appears in the 15 May 2017 press release. Its first date is not established here; it is later than 2015 on present evidence.

| Candidate | Source, capture | Pixels | Notes |
|---|---|---|---|
| **2008 report render** (recommended for the symbol) | clwa.org, 2009-12-14 | 2000x2040 from vector | blue ring and drop, white ground, no text |
| `VWC_Logo2002.gif` | https://web.archive.org/web/20030407195341id_/http://www.clwa.org:80/VWC_Logo2002.gif | 1667x1667 GIF | the same symbol in solid black, sharp; the largest raster of it |
| **2017 lockup, extracted** (recommended for the lockup) | valenciawater.com `Press_Release_05152017.pdf`, 2017-06-06 | 2063x804 JPEG, extracted without re-encoding | the late mark, clean |
| Legacy mirror `gif/valenciawaterco_logo.jpg` | used on scvhistory/scvwa010918.htm | 800x800 JPEG | white-on-blue square with the script name; **upscaled from a small web image** (soft edges, JPEG blocking); not an original |
| 2018 theme `bg-logo.jpg` | valenciawater.com, 2018-03-04 | 285x490 | pale watermark only |

Nathan supplied no VWC file, so there is nothing to compare with an edit. If one mark is wanted for the VWC record, the choice is between the long-running symbol (2008 vector) and the 2017 lockup. That choice is Nathan's.

## Searched without result

- Wayback CDX for ncwd.org (1,441 image and PDF URLs), clwa.org (2,715) and valenciawater.com (697): no SVG, EPS, AI or letterhead file of any of the three marks. The SVGs on those sites are icon fonts only.
- yourscvwater.com (6,112 URLs): no legacy marks of the predecessors found by name. santaclaritawater.com had small copies (NCWD 100x136, CLWA 99x219; ValenciaWC.jpg not captured).
- CLWA budget covers (2009-10, 2010-11) carry the mark only inside a raster cover photo. NCWD CAFRs (2012, 2014) and the budget awards are raster or scanned.
- Legacy mirror filenames (clwa, ncwd, castaic lake, newhall water, valencia water, vwc): CLWA board packets of 2017 (121x265 embedded), the NCWD press release of 2014 (158x192), and the VWC 800x800 upscale. Nothing better than the Wayback finds.

## For Nathan

One action: if you approve, the three 2008 renders replace the Firefly-edited NCWD and CLWA marks as the former-mark assets, and the VWC symbol render becomes VWC's mark. The edited files stay in recordImages with the edit recorded, as the rule for edited images requires.
