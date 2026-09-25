# derivedImageLinks, the article layout, and the footnote attribution

2026-09-20. Nothing applied.

## derivedImageLinks

`link_images_to_records.php` was writing to `relatedArticles` and
`relatedPlaces`, which is where the curated entity review writes, and to
`photoPeople`, `photoPlaces` and `photoOrganizations`, which the JSON-LD
publishes. A relation somebody confirmed and a relation inferred from an href
would have been indistinguishable, with no way back.

- **`add_derived_image_links_field.php`** creates one Entries field and adds it
  to thirteen layouts. The field is the provenance: anything in it came from a
  legacy image link and nothing else writes to it. No extra column, no parallel
  table, no convention to remember.
- **No migration.** The linker has never been applied, so there is no mixed data
  to separate.
- **`link_images_to_records.php`** now writes only `derivedImageLinks`. Run
  today it reports `records with relations to write: 0` and names the sections
  that lack the field, rather than falling back to a curated one.
- **`graph/data.twig`** marks those edges `via: 'derived'`, counts them in
  `meta.derivedEdges`, and lets a curated edge win when a pair is joined both
  ways. A confirmed relation that also happens to have been linked from a
  picture is confirmed.
- **`schema.twig`** does not publish them, and now says why in a comment.
  JSON-LD is an assertion to the rest of the web; publishing an inference would
  put the archive's name behind a guess.

The render layer gained `id`, `section`, `kind` and `date` per link, so the grid
can label a tile without a query per tile. 1,416 files, 14,023 links.

## 1. Inline images

One treatment, no branching. **Float right at 280px** in the 840px measure,
which leaves about 520px of text beside it. Caption below in 12.5px. Click opens
the lightbox at full size. Two in a paragraph stack down the right, which is
what floats do once neither clears.

The full-column branch is gone. On chapter 14 it took the column for the 1001px
picture and left the 400px one floated, and the floated one was the one that
read correctly. **The rule is the size and the size is fixed.** The only thing
still read off the asset is its own pixel width, so a 120px thumbnail standing
in for a scan that was never fetched is not blown up to 280 and turned to mush.

## 2. Photos & Documents

Every tile is a record and says so: **a chip, the title, and the date as
printed**. The chip is read from the record through the link layer, not guessed
from the filename.

**No square crop.** A 4:5 crop cut the headline off a clipping and the caption
off a postcard, which is most of what this section holds. Tiles are a fixed
width and the picture keeps its own aspect inside, top aligned, capped at 300px
tall. The tile goes to the record; the magnifier in the corner opens the picture
in place.

A tile whose image links to nothing stays an image with its caption. On chapter
10, 7 of 11 tiles carry a chip; on chapter 14, 5 of 25.

One fix on the way: the duplicate asset `jj2003a_2026-09-18-071338_lure.jpg` was
printing "Jj2003a" as a tile heading, because its Craft-renamed stem no longer
matched its filename-derived title. The stem is compared with the rename suffix
stripped now. **The duplicate asset still wants deleting.**

## 3. Sidebar

Four labelled lists, each omitted when empty. Source is the curated relation
fields **plus** `derivedImageLinks`, merged and de-duplicated by id, **curated
first**, so a record both confirmed and linked appears once in the confirmed
position. A derived entry carries a small gold dot with a title attribute; it is
worth following and it is not a relationship the archive has confirmed.

Routing is by section and needs no tagging: person, place, community and
organization go to the sidebar; photograph and document stay in the grid.

It reads the layer as well as the field, so it works before
`link_images_to_records.php` has ever been applied and after.

## 4. The webmaster note box

No changes, as asked. **Chapter 6 has no webmaster note**, so there is nothing
on it to judge. Records that do carry one:
`/photographs/winifred-westover-hoover-art-co-photograph-1918-1919` is the
clearest, though its note is the five citations
`convert_note_footnotes.php` is waiting to move.

## The footnote attribution

**The Notes block was rendering inside the About the Author box.** My own fault
and a mechanical one: the script that added the include to thirteen templates
matched the *last* prose include on the page, which on the article is the
author's biography. The other twelve were placed correctly; only the article was
wrong. It is now a section of `<article>`, index 5, directly after `.rec-body`
and before the signature.

Rendered on chapter 5: notes parent `ARTICLE`, inside the author box `false`,
author box children `ABOUT THE AUTHOR` and the name block only.

**Two sections, by source.** `record/footnotes.twig` takes a `source` parameter.
`editor` renders its own section headed EDITOR'S NOTES with the line "Notes by
Leon Worden, SCVHistory.com", naming the record's own editor where one is set.
`author` renders inside the author box under the bio, headed AUTHOR'S NOTES,
with no attribution line because the box already says whose it is. A page can
have both.

An empty source reads as `editor` everywhere, so a row written before the column
existed behaves, and the control panel only has to be filled in for the
exception. The `away` and `orphan` states belong to the editor call alone.

### The schema change

The `footnotes` table has **`number` and `note` and nothing else**, so this is a
schema change, as expected. `add_footnote_source_column.php` adds a select
column with four values, `editor` / `author` / `webmaster` / `source`, and
backfills **12 rows on 5 records** to `editor`.

The backfill is safe and the reason is worth stating rather than assuming: every
row in the table today came from the `[mfn]` shortcode conversion or the
webmaster note conversion, and both are Leon's by definition. Nothing in either
was written by the piece's author. Anything later found to be the author's is
one cell to change.

Rows to set: `winifred-westover-hoover-art-co-photograph-1918-1919` 5,
`chapter-16-golden-dreams` 2, `chapter-14-lord-and-master` 1,
`chapter-13-insurrection` 1, `chapter-5-tribal-relics` 3.

## Markdown left as characters

Asked for the count on people records. **1 record, 1 marker**: Jerry Reynolds's
bio, `*Santa Clarita: Valley of the Golden Dream*`.

Across the whole corpus it is **27 records and 37 markers**, almost all in
photographs:

| field | kind | count |
|---|---|---:|
| photographs / body | emphasis | 24 |
| photographs / creditRaw | emphasis | 5 |
| photographs / creditName | emphasis | 3 |
| photographs / creditKind | emphasis | 2 |
| photographs / body | heading | 1 |
| articles / webmasterNoteBottom | emphasis | 1 |
| persons / authorBio | emphasis | 1 |

`prose.twig` renders single-asterisk emphasis now, **conservatively and without
rewriting anything stored**: the paragraph must hold an even number of asterisks
and no more than eight, and each span must have no space against the asterisks
and must contain a letter. An asterisk is also a footnote marker and a
multiplication sign. There is no bold anywhere in the corpus to handle. The
credit fields are not rendered through `prose.twig` and are untouched.

## Render check, computed

| | ch 5 | ch 6 | ch 10 | ch 14 |
|---|---|---|---|---|
| inline figures | 0 | 0 | 1 | 1 |
| inline `full` class | 0 | 0 | **0** | **0** |
| tiles | 0 | 2 | 11 | 25 |
| tiles with a chip | 0 | 0 | 7 | 5 |
| sidebar lists | COMMUNITIES 6 | COMMUNITIES 10 | PEOPLE 1, COMMUNITIES 2 | PEOPLE 1, COMMUNITIES 4, ORGANIZATIONS 1 |
| notes parent | ARTICLE | none | none | ARTICLE |
| notes inside author box | **false** | n/a | n/a | **false** |
| bio emphasis | `<em>Santa Clarita: Valley of the Golden Dream</em>` | none | same | none |

`check_render.php`: 27 pages, no failures.
