# The lightbox rebuilt, and the image passes reordered

2026-09-20. Nothing applied.

## Lightbox sizing

Test page `the-birth-of-newhall-continued`. The image is the Newhall Signal
front page of 2 January 1947: **1200 × 5612**, ratio 4.68 against a frame ratio
of 0.56.

Under the old rule that fitted everything inside the viewport, a 5,612px column
in a 1,025px-tall frame rendered about 215px wide. That is not a picture of a
newspaper.

| | |
|---|---:|
| viewport | 1920 × 1205 |
| dialog width | 1843 |
| natural | **1200 × 5612** |
| rendered at fit | **1200 × 5612** |
| scale at fit | **1.000** |
| at 100% | 1200 |
| at 200% | **2400**, pans both axes |
| drag test | moved 200px |
| close control over the image | **no** |
| caption and record link scroll with the image | **no**, fixed |

**Legibility, measured rather than judged.** Canvas readback on the scan itself,
sampling the left column under "THE BIRTH OF NEWHALL (Continued)", finding rows
with ink and grouping them into text lines:

```
glyph heights: 30, 26, 26, 26, 26, 30, 31 px
median body glyph height at fit: 26px
```

**26px against the 11px floor.** At fit this scan renders 1:1, which is the most
legible it can be.

My first measurement said 6px and failed. That was a fragment clipped at the top
edge of my sample window, not a line of type. Worth saying because the number
looked plausible and was wrong.

### The rules as built

- **Landscape or near-square** fit inside the viewport as before.
- **Portrait taller than the frame's own ratio** fits to *width*, never to
  height, and scrolls vertically. The width floor is 720px or 90vw, whichever is
  smaller, which is what 8pt newsprint needs to stay legible at 1:1 on a 1440px
  screen.
- **Zoom** fit / 100% / 200%. Above fit the picture overflows and drag pans it.
  Double-click toggles fit and 100%.
- **Close** sits in the dialog's own bar above the picture. A scan of a
  newspaper has words in its top right corner and a cross over them is a cross
  over the thing you came to read. Escape closes; the arrows walk the gallery in
  document order.
- **Caption, credit and "View record"** are fixed under the image so a reader
  ten thousand pixels down a column still knows what they are looking at.

One bug worth recording: at 200% the image rendered 1843px instead of 2400. The
view is a flex container so the picture can be centred when it fits, and a flex
item shrinks to its container by default. `max-width: none` does not stop that;
`flex: 0 0 auto` does.

## The passes, reordered

Enlarge targets first, as asked, and the reason holds up: the magnifier renders
only where there is something bigger to open, and today there never is, so no
tile on the Perkins pages or the Reynolds chapters has one at all. This pass
turns a dead control back on. Everything else improves a page that already works.

| order | pass | create | replace in place | provenance only | not on the mirror |
|---|---|---:|---:|---:|---:|
| **1** | **enlarge targets** | **1,754** | 2 | 26 | 22 |
| 2 | plates | 654 | 0 | 0 | 661 claimed by pass 1 |
| 3 | captioned | 1,295 | 30 | 56 | 25 |
| 4 | better copies | 0 | 261 | 0 | 32 claimed earlier |

Pass one alone: **1,754 new files, 2 replaced, 2,322 assets afterwards.**

All four: 3,703 created, 293 replaced, **4,271 assets** against 568 today. The
union is deduplicated, so running the passes one at a time and running them
together give the same result.

`$ONLY` selects a single pass. It is set to `enlarge` in the committed file.

Every asset carries `legacySourcePath` (`/gif/lw2184.jpg`, which is both the
legacy URL path and its path under the mirror root) and `photoSourceCode` (the
stem, which is what the plate resolver matches on).

Replacement keeps the asset: id, relations, caption, alt and every record
pointing at it are untouched and only the bytes change. `avoidFilenameConflicts`
is off deliberately — letting Craft invent a suffix is what produced
`perkins_ab_2026-09-18-071146_nitl.jpg`, and two assets for one picture is worse
than a smaller picture.

## The enhanced-fields crash

`add_enhanced_derivative_fields.php` threw *Field layout tab is missing its field
layout*. A tab validates its elements against the layout it belongs to, so
`setElements()` on a detached tab throws — after the four fields have been
created and before they are attached.

**It left four orphans**, ids 256–259, existing as fields and on no layout:
invisible in the control panel and impossible to fill in. Nothing else was
damaged, and `legacySourcePath` (260) got through and is on the Content tab.

Fixed by calling `setLayout($layout)` before `setElements()`. The re-run now
reports the orphans by name and plans only the attach:

```
enhancedFrom          exists already, id 256
enhancementMethod     exists already, id 257
enhancedBy            exists already, id 258
enhancedDate          exists already, id 259
all four fields exist and none is on the layout: these are the orphans
left by the run that crashed. Attaching them is all that is needed.
```

Idempotent: a second run takes the `already carries all four` branch and writes
nothing.

## The Reynolds portrait in a lightbox

It was not opening because **it was never wired**. The only `data-lightbox` on
`/persons/jerry-reynolds` was inside the lightbox's own script; the portrait was
a bare `<img>` in the band. Not a regression from making the magnifier
conditional — no band portrait on any record type had ever been openable.

Now it is, on the same test as everywhere else: the portrait opens when the file
is wider than the 4:5 crop it is drawn into, and gets no control when it is not.
`jerry-reynolds.jpg` is 720 × 1066 drawn into roughly 340px, so it opens.
