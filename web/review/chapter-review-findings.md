# Findings from the chapter 6 and chapter 9 review

Generated 19 September 2026. Everything below is measured, not inferred.

## 1. The two vocabularies, reconciled

The uncertainty report said "a short trailing run of non-sentence lines" and
"a possible caption run". Both true; neither said the run contains `[image:2]`.
Three decisions were made on those words and all three would have orphaned
pictures. It now names what it found:

```
short trailing run of 1 line(s), left alone CONTAINING 1 image token: [image:1]
run of 6 lines at the end CONTAINING 2 image tokens, a footnote marker, and the
  line above is none of prose, a copyright line, an exhibit heading or another
  caption: Del Valle married Jacopa Feliz...
short trailing run of 1 line(s), left alone (nothing the archive marks as its own)
```

It reports image tokens, editor brackets, footnote markers, all-capitals lines
and inline markup by name. 283 records carry something the walk stopped at.

## 2. Bracketed BACK, applied

**40 records, and the two article ones are the bulk:** `editors-notes` 68 lines
(34 BACK plus their brackets), `notes` 16, and 38 photographs with one or two
each. The rule takes the bracket above and below when each is alone on its line,
so no orphan `[` is left behind.

This is the one place the cleaner reaches past the head and the tail, and the
header says so. A second run changes nothing; 1,702 records read clean.

## 3. The fused caption: not in our data

**Chapter 6 renders 9 paragraphs, longest 100 words, about 548 in total.**
"The Legend of Califa" is not in the stored body at all. On the legacy page the
caption and the prose are already on separate lines, and the extraction kept
them apart.

What you are looking at is almost certainly **staging**, whose database predates
this session's cleaning runs and whose code is several commits back.

The shape does exist elsewhere, in a different form: **13 lines of 300 or more
words**, a paragraph nobody typed. All but one are in the newly imported
photographs, the longest 505 words, on records like
`postcard-ramonas-home-1907-1914` and `grapevine-grade-1934`. That is a
paragraph never broken rather than a caption fused, and it wants its own pass.

## 4. 1650-calif-map.jpg: downloaded, then orphaned

Not an extraction miss. The extraction caught it as the thumbnail
`gif/thumbnails/t1650-calif-map.jpg` with `links_to` pointing at the full-size
file, and the importer followed the link and fetched it:

**`1650-calif-map.jpg` is asset #42, in the volume.**

But `chapter-6-winds-of-change` has **0 recordImages and 0 image tokens**. The
picture was downloaded and never attached. The failure is in the attach step of
`import_legacy_images.php`, not in the extraction or the download.

**25 of 568 assets are attached to nothing.** Seven are the site logos and seals,
which templates use directly and which are correctly unattached. The rest are
this same failure: `rancho-san-francisco-diseno-map-key-1843.jpg`,
`jose-jesus-lopez-portrait.jpg`, `county-road-map-lyons-station-newhall-seebold-1875-scaled.jpg`
and others.

## 5. The listen player

Moved below the cite box on articles. Articles are the only template that puts
the tools block in the sidebar, where at 340px the player is the heaviest thing
on the page and was the first thing the eye met. The citation is what a reader
wants first. The other eleven templates put it in the main column and are
unchanged.

## 6. Leon's cross-references: 100 of them, from 41 records

`export_article_links.php` now records a link from a record we hold to a legacy
page we do not, with its anchor text and the sentence around it, instead of
dropping it. Bare footnote numbers are excluded: "7" pointing at the notes page
says nothing a reader can use.

Chapter 9 is in the list exactly as you described:

```
chapter-9-the-trail-blazer  (2)
   "COSTANSÓ DIARY"  x2                            -> /scvhistory/costanso-diary.htm
   "Story: Rio Santa Clara in Costansó's Diary" x2 -> /scvhistory/lw100604.htm
```

**Chapter 6 is not, and cannot be.** Its `links_out` holds two entries, both
pointing at the map image, and neither is a cross-reference. SPANISH OFFICIALS,
EXPLORERS, MISSIONARIES and COSTANSÓ DIARY are on the legacy page and **not in
the extraction**. That is a gap for Grok, not something the exporter can reach.

## 7. editorNotes

`add_editor_notes_field.php` creates the table and adds it to eleven entry
types. Columns: heading, note (HTML allowed), position (top, inline, bottom).
Rendered by `_partials/record/editor-notes.twig` as boxes with a gold top rule
and a cream ground, the heading in the small gold Jost, the note in the body
face. Distinct from `record/note.twig`, which keeps its left rule for the two
single notes, and distinct from a pull quote.

### Should the two existing fields migrate?

**`webmasterNoteTop` is used by 0 records. `webmasterNoteBottom` by 65.** None
uses both.

They should coexist, and the migration is not worth doing as a script, for one
reason: **the extraction records a position and never a heading.** Its
`editor_notes` entries carry `position` and `text` and nothing else, so every
migrated row would arrive untitled and the boxes would render without the
heading that is the reason for having them.

Chapter 6 makes the case. Its `webmasterNoteBottom` is **3,143 characters** of
several separate annotations run together, and its `editor_notes` in the
extraction is **empty** — the extraction did not separate them either. Splitting
that blob into headed rows is reading and judgement, not a transformation.

So: leave the 65 where they are, use `editorNotes` for new annotation work, and
migrate a record by hand when somebody is already reading it.

### What the extraction would give

Pages carrying more than one annotation, across the corpus:

| inventory | pages | with notes | more than one | most on one page |
|---|---|---|---|---|
| reynolds-full | 80 | 4 | 1 | 6 |
| perkins | 20 | 6 | 1 | 2 |
| worden | 219 | 5 | 1 | 2 |
| lw-features | 1661 | 42 | 6 | 4 |
| warmemorial | 54 | 1 | 0 | 1 |

**Nine pages in the corpus carry more than one.** Positions are recorded
(`inline` mostly, some `bottom`), headings never.

## 8. The three scan credits: blocked

`LW2304a: 9600 dpi jpeg from digital image | Online image only` and two others
sit in `finePrint` on `surveyors-map-showing-lyons-station`,
`diseno-map-of-rancho-san-francisco-c-1843` and, as `personFinePrint`, on
`tiburcio-vasquez`.

They cannot move to `creditRaw`, because **none of those entry types has that
field**. `creditRaw` and its four parsed parts exist only on `photograph`.

Three ways, and it is a decision rather than a fix:

- add the five credit fields to `article` and `person`, for three records;
- make these three records photographs, which they arguably are, since two are
  maps and one is a portrait;
- leave them, and accept that three records print a scan line as fine print.

The parse, if the field existed, is straightforward: `LW2304a` is the code,
`9600` the dpi, `jpeg` the process, `digital image` the kind, `Online image
only` the name. `JJ2003b: 2400 dpi jpeg from 96 dpi jpeg` and `JE4001: 1200 dpi
jpeg from copy print` parse the same way with no name.
