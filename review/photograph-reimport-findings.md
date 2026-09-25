# The photograph re-import

2026-09-20. Nothing applied. Every figure below is measured, and one of them
corrects a number I reported twice.

## The mechanism, and why this one

None of the three options was right, and the source says why:

```
<p>Ynacio Aceves
</p><p>Beniono Acosta
</p><p>Bernabe Acosta
```

Every name on the Camulos census is its own `<p>`. The source already states the
structure. There is nothing to detect, nothing to infer and nothing to guess at
render time. The old importer lost those lines because it treated short
paragraphs as noise. The fix is to stop doing that, not to build machinery that
re-derives what the HTML already says.

**Transcribe the source's own structure and infer nothing.**

| source | body | why |
|---|---|---|
| `<p>` `<h1..h6>` `<blockquote>` | a paragraph, blank-line separated, text reflowed to one line | the source's own block |
| `<br>` inside a block | `[lines]` ... `[/lines]`, newlines inside are hard breaks | the author's line breaks, fenced so nothing can reflow them back |
| `<li>` | a line prefixed `- ` | 565 items on 31 pages. Rare, and real |
| `<div>` `<table>` `<tr>` `<td>` `<center>` `<font>` | unwrapped | 1,553 of 1,661 pages are built on layout tables. Representing those as tables would invent 1,553 tables that are not tables |

**A separate field was rejected** because it cannot hold position. lw2575c is
prose, then a transcribed letter, then prose again, and a field beside the body
cannot say where the letter went.

**A render-time rule was rejected** because "a run of short lines" is also a
poem, an address block and a stack of captions, and because guessing at render
is the same class of mistake that caused the loss. `prose.twig` already had a
rejoin heuristic, and heuristics are what got us here.

**Detecting the shape at import was rejected as unnecessary.** There is nothing
to detect.

## What the fix recovers

The line-based measure is distorted by the fix itself: the rebuild puts each
source paragraph on one line while the legacy `body_text` keeps its hard
wrapping, so a paragraph wrapped over five lines reads as five lines lost and
one added when not a word has changed. **Words are immune to wrapping and are
the figure that answers the question.**

| | before | after |
|---|---:|---:|
| words in the source pages | 1,591,352 | 1,591,352 |
| words in ours | 1,485,398 | **1,585,629** |
| **source words missing from ours** | **105,958 (6.7%)** | **5,820 (0.4%)** |
| our words not in the source | 4 | 97 |
| **records holding under a third of the source words** | **22** | **0** |
| records under a third, by line | 99 | 7 |
| lost lines | 28,256 | 33,625 (reflow artefact, see above) |

**100,138 words recovered.** The 0.4% remaining is the navigation removed at
import and the headline that duplicates the record title. The 97 words "added"
are entity decoding and the `- ` list prefix, not invented text.

lw2717, the Camulos Cemetery Census: **218 characters and 4 lines, to 2,330
characters and 93 lines.** The eighty names are back. Full before and after in
`photograph-reimport.md`.

## A correction

I reported that **32 of 33** long paragraphs lost their breaks in our import.
That was wrong, and it was my measurement rather than the data.
`build_legacy_html_index.py` counted raw `<p>` tags, and the legacy markup opens
and closes paragraphs around each other, so a page is full of `<p>\n</p>` pairs
with nothing in them. lw2717 has 107 `<p>` tags and 93 paragraphs of text. The
tag count made every faithful body look like it was still losing breaks.

Counting non-empty blocks:

| | before | after |
|---|---:|---:|
| paragraphs of 300+ words | 33 | 36 |
| **breaks lost in our import** | **1** | **0** |
| genuinely one paragraph | 31 | 35 |
| not in the crawl | 1 | 1 |

So the long paragraphs were almost all genuine long paragraphs. The real defect
was the dropped lines, which was and remains large. This is the third figure I
have had to correct in this work; all three came from measuring the proxy rather
than the thing.

## Render check

Ten records rendered through `photographs/_entry` with the rebuilt body set in
memory and nothing saved, served by `/admin-preview?key=<legacyKey>`, which is
admin-guarded and returns 302 to anyone else.

All ten returned 200. None leaked a `[lines]` fence, none carried "click image
to enlarge" or "download archival scan", and none had a bare `|` paragraph.

| key | paragraphs | line blocks | words |
|---|---:|---:|---:|
| lw2717 Camulos Cemetery Census | 90 | 1 | 413 |
| lw2575c A. Rivera to Frank Walker | 48 | 6 | 1,153 |
| lw9410m Red Cross Shelter | 91 | 4 | 3,477 |
| lw2174 'The Disciple' lantern slides | 62 | 4 | 1,171 |
| lw2138 'Branding Broadway' lobby card | 73 | 4 | 1,433 |
| lw3355 Hart in 'Blixtens Broder' | 63 | 5 | 1,120 |

Computed values, article column 840, body 840, paragraphs 17px on 29.75px. On
lw2575c the letter's address block keeps its two `<br>` breaks inside one
`.rec-lines` paragraph at 25.5px leading, tighter than the 29.75px around it,
because those lines belong together.

## Stop: the image-link relations

`link_images_to_records.php` writes to **`relatedArticles`** and
**`relatedPlaces`**, which are exactly the fields `apply_article_links.php` and
`apply_place_links.php` write from the curated entity review. It also writes
`photoPeople`, `photoPlaces` and `photoOrganizations`, which
`_partials/head/schema.twig` publishes as JSON-LD. A derived relation would be
indistinguishable from a curated one in the control panel, in the graph and in
the structured data.

**Not applied.** What it needs:

1. A new Entries field, `derivedImageLinks`, on the thirteen layouts. The field
   itself is the provenance: anything in it came from a legacy image link, and
   nothing else can write to it.
2. **No migration.** The script has never been applied, so there is no mixed
   data to separate. This only has to be settled before the first run.
3. `templates/graph/data.twig` and `_partials/head/schema.twig` read the new
   field separately and mark the edge as derived, so the graph can show or hide
   it and the JSON-LD does not assert a curated relationship the archive never
   made.

The render layer under `_data/image-links` is unaffected and already works: it
is a file, not a relation, and it carries its own provenance by being generated.

## Webmaster notes that are footnote lists

**1 of 77.** I reported this as a fifth source of footnotes and it is a single
record: winifred-westover-hoover-art-co-photograph-1918-1919, five citations in
`webmasterNoteBottom`. `convert_note_footnotes.php` dry-runs it. The converter
does not add markers to the prose, because there are no `[N]` to anchor them to
and inventing positions would be guessing.

---

# The residue, named

Written after the re-import was applied. The database now holds the rebuilt
bodies; lw2717 is 2,332 characters and 93 lines.

## The 7 records still under a third of their source by line

They are not losing content. Every one of them loses between 0 and 7 words, and
three lose none at all.

| record | source lines | ours | source words | our words | words missing |
|---|---:|---:|---:|---:|---:|
| lw3677 Hoot Gibson's Saugus Rodeo programme | 113 | 26 | 1,303 | 1,299 | 4 |
| lw2884 Chatsworth Neighborhood Destroyed | 112 | 30 | 1,544 | 1,537 | 7 |
| lw2414 Hart Promotes 5th Liberty Loan | 109 | 32 | 758 | 754 | 4 |
| lw3365 Borax 20 Mule Team instructions | 98 | 28 | 860 | 860 | **0** |
| lw1917 Hart Promotes Liberty Loan | 99 | 30 | 610 | 610 | **0** |
| lw3689 Clint Walker British lobby card | 63 | 15 | 377 | 377 | **0** |
| lw3107 Arizona Bushwhackers lobby card | 35 | 7 | 173 | 169 | 4 |

The line count fell because the rebuild puts each source paragraph on one line
and the legacy `body_text` keeps its hard wrapping. This is the metric measuring
wrapping, not the archive losing anything. The line figure should be read as a
pointer and never as a verdict.

## What the 5,820 missing words are

Aggregated across all 1,543, every missing word counted:

| word | count |
|---|---:|
| click | 1,326 |
| to | 1,326 |
| enlarge | 1,325 |
| image | 1,071 |
| everything else | 772 |

**5,048 of 5,820, or 87%, is "click to enlarge" and "click image to enlarge"**,
which the importer removes on purpose. It is not loss; it is the navigation
leaving.

The remaining **738 words sit on 132 records**, and almost all of it is one
thing: **the page headline, dropped because it repeats the record's own title.**
`mrs-andersons-3rd-grade-class-1984` is missing "mrs anderson s 3rd grade class
1984"; `switch-tie-plate-from-saugus` is missing "switch tie plate from saugus".
Printing the title twice would be worse than dropping it once.

Two records are not that:

- **lw030597, 52 words.** The one record the importer skips, because its body
  carries `[image:N]` tokens and a rebuild cannot know where they belonged. It
  still holds the old importer's body. It needs doing by hand.
- **lw3562, 42 words.** Real, and recoverable. See below.

## Something the importer could still get, and now does

The 1923 Newhall Telephone Directory was coming out as

```
Abbott, Ed S., general merchandise7-W
American Auto Works, Spruce St.14-W
```

Two faults. **`<td>` was not in my block list**, so table cells were glued
together with no separator. And the brief asked for `<table>` as tables while I
had unwrapped every one of them.

The distinction that makes this safe is a fact about the source rather than a
guess about the content: **a `<tr>` with two or more cells is a table row; a
`<tr>` with one cell is the layout 1,553 of the 1,661 pages sit inside.** A cell
that itself contains paragraphs is layout whatever the row looks like.

Rows now become tab-separated lines inside a `[table]` ... `[/table]` fence, and
`prose.twig` renders them as a table. **16 records change. No words are lost in
any of them**; the four that shrink in characters do so because a `[lines]`
fence became a `[table]` fence.

| record | what it is |
|---|---|
| lw3562 | the 1923 telephone directory, 35 rows, name and number |
| lw3550 | a library catalogue: call number, author, title |
| lw2534 | a superintendent's career: year and post |
| lw3337 | Winifred Westover's vital statistics |
| + 12 more | mostly single-row caption tables |

Rendered check on lw3562: one table, 35 rows, 2 cells in every row, 840px wide,
first row "Abbott, Ed S., general merchandise" and "7-W", no fence or tab
leaking into the text.

Missing words after the fix: **5,778**, of which 5,048 is the navigation. The
true residue is **730 words across 1.59 million, 0.046%**, and it is the
duplicated headline.

## Re-running is safe

Asked, and answered by running it rather than by reading it.

The script rebuilds from `body_html`, which does not change, and compares its
output against the stored body before writing. Three consecutive dry runs after
the apply:

```
records to rebuild: 16   already identical: 1527   md5 80e5495d16dbfa624e2eac9ca3e9324a
records to rebuild: 16   already identical: 1527   md5 80e5495d16dbfa624e2eac9ca3e9324a
records to rebuild: 16   already identical: 1527   md5 80e5495d16dbfa624e2eac9ca3e9324a
```

**1,527 of the 1,543 already-applied records come back "already identical" and
are skipped.** The 16 are the ones the table fix changes. The output is
byte-identical across runs.

Two things that would make a later run not a no-op, and both are wanted:

- **A record that gains `[image:N]` tokens is skipped from then on**, not
  rebuilt. So running this after the images land will not undo the tokens.
- **A body edited by hand in the control panel would be overwritten**, because
  the script's authority is the source HTML. Nothing has been hand-edited yet.
  If that changes, the script needs a "leave modified records alone" rule before
  it runs again.

So captions and `derivedImageLinks` can both follow it safely: neither touches
the body, and a later re-run will skip every record it has already written.
