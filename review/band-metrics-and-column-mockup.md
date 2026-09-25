# Lightbox verified, the two bands measured, and the column mockup

2026-09-20. No database work. Nothing applied.

## 1. Lightbox on `the-birth-of-newhall-continued`

Re-measured on the current code, so every number below comes from one pass.

| | |
|---|---|
| viewport | 1920 × 1205 |
| dialog | **1843 × 1133** |
| natural | **1200 × 5612** |
| rendered at fit | **1200 × 5612** |
| scale at fit | **1.000** |
| fits to width, not height | **yes** |
| scrolls vertically | **yes** |
| first body line glyph height | **30px** |
| median body glyph height | **26px** |
| meets the 11px floor | **yes** |
| close control outside the image | **yes**, and in the bar |
| caption fixed, not scrolling | **yes** |

Zoom and pan: fit 1200, 100% 1200, **200% 2400**, pans on both axes, drag moved
260px, Escape closes.

Legibility is canvas readback on the scan itself — rows with ink, grouped into
lines, under "THE BIRTH OF NEWHALL (Continued)". Not an estimate.

Double-click from fit gives 1200, which is the same as fit on this image because
its natural width is 1200 and the frame is wider. The toggle works; this
particular scan has nowhere to go.

## 2. The two bands, side by side

| | Perkins `the-birth-of-newhall-continued` | Reynolds `chapter-5-tribal-relics` |
|---|---:|---:|
| band height | **373** | **473** |
| padding top | 26px | 26px |
| padding bottom | 46px | 46px |
| breadcrumb → kicker | 34 | 34 |
| kicker → title | 18 | 18 |
| title → subtitle | 14 | 14 |
| byline → chips | 66 | 66 |
| chips → band bottom | 47 | 47 |
| content column | 560 | 560 |

**Every spacing value is identical. There are no per-collection overrides to
remove.** Both bands render from the same block in `articles/_entry.twig` and
neither has a variant anywhere.

The 100px is content:

| | Perkins | Reynolds | difference |
|---|---:|---:|---:|
| title | "The Birth of Newhall", **1 line**, 57px | "Chapter 5: Tribal Relics", **2 lines**, 113px | **+56** |
| chips | 3 chips, **1 row**, 37px | 7 chips, **2 rows**, 81px | **+44** |
| | | | **100** |

56 + 44 = 100, which is the whole of the gap. Both titles are 54px on a 56.7px
line in a 560px column; Reynolds's is simply longer and wraps, and it carries
four more communities.

So Perkins does not read tighter because it is spaced tighter. It reads tighter
because it is shorter.

### Done, and two corrections to the measurement above

Reporting instead of acting was the wrong call, and two of the numbers in that
table were wrong as well. They came from matching inline style fragments, and
`div[style*="flex-wrap: wrap"]` matched the kicker rather than the chips, so
"byline to chips 66" was measuring something else. With real classes it is 26 on
both, which is what the CSS always said.

**There were three band implementations**: inline styles on the article,
`.cl-band` on the collection lander, `.rec-band` for the other eleven types.
Their values had been brought into line by hand, which held, but three copies of
a thing drift and the only question is when. There is one now.

- `articles/_entry.twig` band: inline styles gone, uses the shared classes.
- `collections/_lander.twig`: `.cl-band`, `.cl-kick`, `.cl-col`, `.cl-by`,
  `.cl-chips` and `.cl-chip` deleted, uses the shared classes.
- `_partials/record/css.twig` holds every value, once.

**The band is self-contained now**, which mattered more than it sounds. The
article's band is a section in the page and not inside `.rec`, so
`.rec h1{margin:0}` never reached it and the browser's own 21.44px h1 margin
came back, collapsing against the kicker and the subtitle. That is why the first
folded version measured 21 and 21 where the CSS said 18 and 14. Every element is
addressed from `.rec-band` now.

**`--rec-band-floor: 473px`** makes the heights match. Measured after:

| | Perkins | Reynolds | Felton School | Story of Our Valley |
|---|---:|---:|---:|---:|
| band height | **474** | **474** | **474** | 563 |
| padding top / bottom | 26 / 46 | 26 / 46 | 26 / 46 | 26 / 46 |
| breadcrumb → kicker | 34 | 34 | 34 | 34 |
| kicker → title | 18 | 18 | 18 | 18 |
| title → subtitle | 14 | 14 | 14 | 14 |
| byline → chips | 26 | 26 | 26 | 26 |

The lander is 563 because it carries two controls and four stat blocks under the
chips. That is content below the floor, not a different set of values.

The floor is dropped below 700px wide, where a fixed height would be a tall
empty box on a phone.

## 3. `design/collection-column-source.html`

HTML only, in `design/`, not wired. Built to the spec:

- **Author band.** The portrait sits in the band beside the title, because for a
  column the author is the subject rather than a credit. The run of years sits
  under the name.
- **No "Start reading".** A column has no first piece. The only band control is
  "Read the most recent".
- **Chronological list grouped by year**, newest first, each group headed by the
  year with a count.
- **Year jump bar**, sticky, marking the group you are in. The point of it is to
  be reachable from anywhere in a list that may be 259 items long.
- **Stat blocks**: pieces, years, dated, words.

### What the real data forced into the design

Content is the actual Perkins records, unedited. **Only 4 of the 20 carry an
`originalPublishDate`**, so sixteen sit under an **Undated** group.

That is not a flaw in the mockup, it is the case a book template never has to
face and a column template always will. A run grouped by year needs somewhere to
put the undated, and hiding them would be worse than showing the gap. They keep
the order the source gave them.

A real column will not look like this: Worden is 219 pieces across 1995–2009 and
Making Cents is 259 weekly pieces, so the year bar there carries fifteen years
and the groups run twenty deep. **Perkins is a book being shown in a column
layout to test the layout.** The mockup says so on the page.

Two faults found by looking at it rendered and fixed: the title and the source
ran together on one line, because the spans were inline; and the year bar listed
1976 and 1958, which have no group in this data.
