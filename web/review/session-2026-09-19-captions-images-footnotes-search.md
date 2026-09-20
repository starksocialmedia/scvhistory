# Captions, inline images, footnotes, the collection pages, and search

Written 2026-09-19. Five pieces of work, in the order Nathan set them.

The five decisions are recorded first because three of them close open items:
box-sizing stays as ours and the article column stays tighter than the design
file's standalone rendering; the three scan credits stay in finePrint and get no
field and no reclassification; mid-body `[ BACK ]` removal is an accepted rule
and the exception is documented in `clean_legacy_bodies.php`.

---

## 1. The captions

**The size of it is smaller than the headline figure, and the headline figure is
still right.** There are 5,220 caption records in the `*-images.json`
inventories and 562 of 568 assets are titled from their own filename. Both true.
But only 113 of the assets we hold are referenced by a page that carried a
caption, and after navigation is stripped that is 89.

The other 1,316 distinct files with a caption are images that have not been
fetched. The script is written to be re-run and should be run again after every
image import; that is where the rest of the 5,220 lands.

| | |
|---|---:|
| caption records in the inventories | 5,220 |
| of those, navigation and nothing else | 794 |
| distinct files with a real caption | 1,405 |
| assets in Craft | 568 |
| assets matched to a real caption | 89 |
| variant conflicts resolved | 7 |

`scripts/import/apply_extracted_captions.php` writes `photoCaptionExt`, the
field that exists for this and was empty on all 568. It also writes the title
where the title is only the filename, and `alt` where `alt` is empty. A title
someone set by hand is never overwritten.

**Navigation is not caption.** 420 records read "Click image to enlarge", 250
read "Click to enlarge.", and the rest are variants of the same. A segment that
opens with click, download or enlarge is removed, as are the bracketed size
controls `[ FULL VIEW ]` and their unbracketed twins `| Full View | Archival
Scan`. Removal is by whole segment and never mid-sentence: "the sign says click
here" is a caption about a sign. Where nothing survives the asset is left with
no caption rather than a false one.

**Two rules of mine were wrong and were corrected by reading the output.**

The first was "longest variant wins". It gave `jj2003a.jpg` a paragraph of body
text about diseño maps in place of the caption "Diseño map of the Rancho San
Francisco (Santa Clarita Valley), ca. 1843". A variant over 200 characters is
now set aside when a shorter one exists, and still used when it is all there is.

The second suppressed any caption matching the filename, which threw away
"Pedro Fages" on `pedrofages.jpg` and "William Lewis Manly" on
`williamlewismanly.jpg`. The archive names a portrait after its sitter, so that
is a correct caption. Only a code is suppressed now, told apart by having no
space and carrying a digit.

All 7 conflicts are listed with every variant in `web/review/captions.md`. If a
pick is wrong, name it.

**alt text is not worth harvesting.** 3,785 files carry an alt attribute; 3,520
of those say "thumbnail" and 177 say "image". Six assets we hold have anything
informative, and they are two-word labels. Not used.

**The templates were printing alt as a credit.** `prose.twig` and
`record/images.twig` both read the caption from the title and the credit from
`alt`. That was wrong before the caption script existed, because alt is the
screen reader's text and not a credit line. Both now read `photoCaptionExt` and
`photoCredit`.

## 2. Inline images and the lightbox

**The float was not working on any article, and I did not notice it last turn.**
The redesign made `.rec-body` a flex column with a 22px gap. A flex item does
not float, so every picture placed inline took the full column whatever
`prose.twig` asked for. The body is ordinary block flow again with the 22px kept
as a margin.

The figure is now 40% of the text column. It takes the whole column instead when
the picture is a portrait taller than it is wide by a fifth, or when the
paragraph beside it is under about 260 characters and has nothing to wrap with.
Headings, the signature, the photographs and the foot all clear.

**Nothing is drawn wider than its own pixels.** A great many of the legacy images
are 120px or 150px thumbnails standing in for a scan that was never fetched.
Blown up to 40% of the column they are mush, and blown up to the whole column
they are worse. Anything under 300px wide stays floated at its natural size.

**The lightbox is now sitewide.** It used to be emitted inside the PHOTOS
section, so a record with no photographs had no lightbox at all and every
picture placed inline in the prose was a dead end. It is now
`_partials/record/lightbox.twig`, included once from the base layout and bound by
delegation on `[data-lightbox]`. Verified on a live article: an inline figure
opens it, the dialog closes, the PHOTOS grid opens the same one.

## 3. Footnotes

**229 records carry a marker. This supersedes the 238 I reported last turn**,
which came from a looser scan. A marker is `[N]` with no word character before it
and no digit after, so "the refinery of the [18]70s" is a date and not a note.
1,161 distinct markers across the 229.

| state | records | what renders |
|---|---:|---|
| the note text is on the record | 2 | the notes print under the piece, the marker links to them |
| the notes are on another record | 6 | the marker links to that record |
| orphan, the note text is not held | **221** | the marker prints unlinked, one line says the notes are not in the source |

That is 96% orphans, and it matches the shape of Grok's survey: the legacy site
kept one `notes.html` for a whole series rather than putting notes on the page
that cited them, and most of those files did not survive.

`scripts/import/add_footnotes_fields.php` creates `footnotes`, a table of number
and note, and `footnotesOn`, a single-entry relation for a series notes page. The
number column is text because the legacy pages number notes 1, 2, 3 and also 1a
and `*`, and because it has to match the marker in the prose exactly.

There is no regex replace in this Twig, and a plain replace of every `[N]` would
wrap that date. The paragraph is cut on the opening bracket and reassembled,
which is the only way to see what sits on either side of a match. Verified: on
`/articles/6-oil-and-newhall` the markers 21, 22, 23, 25 and 26 are superscripted
and `[18]70s` is left as it was.

**Say plainly what is not tested.** The orphan path runs on live records. The
held and elsewhere paths are written and cannot execute until the fields exist,
which is a script for Nathan to run.

## 4. The collection pages

Three pages rendered three ways. The article was built from
`design/article-source.html`; the lander was built from a written brief before
that file existed; the unflagged collection used the shared record chrome, which
was also set from the brief.

**What the lander did differently, and is no longer doing.** Its band had two
image layers, one dead at opacity 0 and one carrying a contrast filter, and no
gradient at all, so the artwork ran the full width behind the words instead of
falling away to the right. It now has one image layer and the article's exact
gradient. The kicker was a list joined with dots; it is now a row with a short
gold rule. There were no chips at all; era and community chips are now drawn as
the article draws them, gold-outlined and navy. The h1 clamp, the italic
subtitle, the 344px sidebar and the 56px gutter all follow the article.

**The shared chrome moved too.** `record/css.twig` held the same things from the
brief, so all eleven record types differed from the article a little: a 1px
vertical tick instead of a gold rule in the kicker, rounded chips, a 340px
sidebar, a 48px gutter, a different h1 clamp. Those now hold the article's
values, which brings eleven templates into line in one move rather than eleven.
It also gains band artwork, which the unflagged collection now uses.

**Deliberate differences, kept.** A collection is not an article. The
start-reading control, the listen control, the stat blocks and the contents list
with its parts and reading order all stay.

**One thing I did not unify, and it is the cite box.** The lander still uses
`record/cite`, not the article's `cite-article` fork. Unifying it would undo the
fork that was made precisely so one redesign did not change eleven templates.
Say the word and it becomes a second fork rather than a shared change.

**A data gap, not a template one.** `/collections/worden` prints "ARTICLES 0"
while its own body says the run reaches 197 article pages. `articlesInCollection`
is empty and no article carries a `partOfCollection` relation to it.

## 5. Advanced search

One box as before. Craft's index finds the records; the facets are computed on
the found set, because what a reader narrows by lives in relations and in the
`recordDates` table rather than in the search index.

Verified on live queries for "newhall":

| query | results |
|---|---:|
| q alone | 84 |
| photographs | 73 |
| articles | 5 |
| photographs OR articles | 78 |
| photographs AND community Newhall | 25 |
| community Newhall alone | 34 |

OR inside a facet, AND across them. 73 and 5 give 78; photographs and Newhall
give 25, which is the intersection and not either count.

**Counts ignore their own facet's selection.** Choosing Newhall drops the record
type counts from 73 to 25 while the community counts stay at 34. Counting a
facet inside its own selection would zero every sibling and the reader could
never widen again.

**A facet with one option does not render**, unless something in it is ticked, or
it could not be unticked. Era is absent from a photographs-only search and author
from a search with one author. People, places and organizations are not facets:
they are records, reached by searching for them.

Every control is a link, so a narrowed search can be sent to someone or kept.
The date range is a form because a year is typed, and it resolves to the same
URL. The result rows now name the record kind.

The found set is capped at 1,200, above the size of the archive, so that a
one-letter query cannot walk every relation on every record. `q=a` returns 528
in 1.7 seconds; ordinary queries are a quarter of a second. Reaching the cap
prints a line saying the counts are for the slice rather than quietly reporting
wrong ones.

**The date facet reaches very little today.** "newhall" between 1900 and 1950
returns 5. Almost nothing carries a parsed `recordDates` row. The facet is right
and the data behind it is thin, and that is a separate job.

---

## 6. The page scale, measured rendered rather than read off the source

Nathan was right that something was wrong and right that I had reported a match
without rendering both. Here are the computed values, both pages open in the
same browser at a 1920 viewport.

| | design/article-source.html | ours, before | ours, now |
|---|---:|---:|---:|
| box-sizing on the wrapper | content-box | border-box | border-box |
| `<main>` outer width | 1320 | 1240 | 1320 |
| `<main>` content width | 1240 | 1160 | 1240 |
| article column | 840 | 760 | 840 |
| sidebar | 344 | 344 | 344 |
| gutter, column to sidebar | 56 | 56 | 56 |
| prose width | 595 | 595 | 595 |
| body font / line height | 17.5 / 31.15 | 17.5 / 31.15 | 17.5 / 31.15 |
| **white, last word to sidebar card** | **301** | **221** | **301** |

**The design is consistently larger and the cause is box-sizing on the wrapper,
exactly as asked.** The design file has no reset, so `max-width: 1240px` plus
40px of padding each side renders 1320 outside and 1240 inside. Our global reset
is border-box, so the same declaration rendered 1240 outside and 1160 inside.
Every wrapper on the site was 80px narrower than the drawing: header, nav, band,
main and footer. They are now 1320 under border-box, which is the design's 1240
of content, and every number above matches.

**It widens the channel rather than closing it, and that has to be said
plainly.** The prose is capped at 34em in both files, which is 595px in both.
The design leaves 245px of empty column plus the 56px gutter: 301px between the
last word and the sidebar card. Ours was 221px. Matching the design takes us
from 221 to 301, because the design has more of that white, not less.

What stops it reading as a hole in the design is that the tools row, the
photographs grid, the prev/next and the author box all run the full 840 and
reach the gutter. Ours reached only 760 and stopped 80px short, so the channel
had nothing bounding it. They now reach the same place.

**If the channel should be tighter than CD drew it**, the lever is the 34em
prose measure or the column split, not the wrapper. 40em would put the prose at
700 and the white at 196. That is a change to the design rather than a
correction to our copy of it, so it is not made here. Say which and it is one
line.

### The lead and the body did not share a right edge, and that is the step

Raised three times, and twice I answered the wrong question. The measurement I
kept giving was the article column against the sidebar, which did match. The
defect was inside the column.

`max-width` in `em` resolves against the element's **own** font size. The design
writes `34em` on the lead, on the body and on the signature row, and those three
elements are 24px, 17.5px and 16px, so they render at **816, 595 and 544**. Three
right edges, the widest 272px past the narrowest, stepping down the page. That is
the ragged channel, and it is in the design file itself rather than in our copy
of it.

One measure, stated once, in the pixels the body text makes of it:

| | before | now |
|---|---:|---:|
| lead paragraph | 816 | 700 |
| body | 595 | 700 |
| signature row | 544 | 700 |
| right edge of all three | 1156 / 935 / 884 | 1040 |
| **prose right edge to sidebar card** | 221 | **196** |

The measure is 40em of body text, on Nathan's instruction, because 34em left a
301px channel that read as a hole. 40em at 17.5px is 700px. The lead is set in
px rather than em, because 40em on 24px Playfair would be 960 and the step would
come straight back. The inline image captions and the footnote blocks take the
same 700.

---

## Not done

- Work order item 4, the shape of the 13 paragraphs of 300-plus words in the
  photographs. Not started.
- Work order item 5, the fidelity audit across the 1,544 photographs. Not
  started.
- `templates/photographs/_entry.twig` and `templates/documents/` are still
  stubs. The photograph template renders `entry.body|nl2br` and nothing else, so
  it has no band, no prose partial, no footnotes and no inline images. The
  shared CSS change does not reach it because it does not use the shared chrome.
- The 5,220 captions are applied to 89 assets. The rest need the image fetch.

## Small findings

- Two assets carry a Craft rename suffix from a re-import and are duplicates of
  a file already in the volume: `jj2003a_2026-09-18-071338_lure.jpg` (1846) and
  `perkins_ab_2026-09-18-071146_nitl.jpg` (1757).
- `juancrespi-cenotaph.jpg` is related to a record and renders 0x0: the asset
  exists in Craft and the file is not on disk.
- Several legacy bodies carry a caption as a body paragraph, ending "Click image
  to enlarge." The caption cleaner handles the asset side; the body side is
  untouched and is a candidate for the fix list.
