# The five faults, and what else came out

## 1. Band image — renders here, cannot reach the server

On `chapter-9-the-trail-blazer` the chain resolves:

- article `bandImage`: **none**
- collection `History of the Santa Clarita Valley` #871 `bandImage`:
  **`collection-band-2400x1000.png` #1409**, 2.9 MB, present on disk

It renders. `background-image` is in the HTML and the band measures 430px with
Jerry visible at the right.

**Why you see cream:** `web/uploads/archive-media/.gitignore` is `*` with only
`site/` excepted, so that file and `story-of-our-valley-band-2400x1000.png` are
**not in git and a `git pull` can never put them on the server.** They are the
two band PNGs flagged as undeployed earlier. Only rsync moves them.

## 2. Column gap — not a fault

Byte-identical to the design:

```
main   max-width: 1240px; padding: 40px 40px 90px;
       grid-template-columns: minmax(0, 1fr) minmax(300px, 344px); gap: 56px
```

Measured in the browser: **main 1240, columns 760px and 344px, gap 56px.**

Rendering the design file standalone gives **main 1320, columns 840 and 344,
gap 56** — because the file has no box-sizing reset and uses `content-box`,
while our page inherits `border-box` from the site CSS. The design's own
whitespace between the end of the prose and the sidebar is **301px**; ours is
**221px**, because the 34em body sits in a narrower column. Ours is tighter,
not looser.

Nothing changed. If you want the design file's standalone rendering, the change
is `box-sizing: content-box` on that one `<main>`, and it widens the article
column to 840. Say which.

## 3. Header search — rebuilt

Search is out of the top row and at the right of the nav row: a button that
expands a 300px field, label **SEARCH** closed and **CLOSE** open, gold
underline and gold icon when open, focus landing in the input. Verified:
`SEARCH → CLOSE`, form `none → block`, `document.activeElement` is the search
input.

**The header is `{% cache %}`d.** The key is bumped to `scv-header-v4-`; without
that the old markup keeps serving.

## 4. Account placeholder — drawn

Avatar circle, `Leon Worden`, `EDITOR`, caret, in the top row. Rendered as a
`div` with `aria-hidden`, not a `button`: it has no menu and no behaviour, and a
button that does nothing is worse than a shape that admits it is one.

Worth one line: this puts a named signed-in user on every page of a public
archive. It is what the design draws and I have drawn it.

## 5. Listen and the player — fixed

`hidden` is a user-agent rule and the inline `display: flex` beat it, so the
player rendered while every script reading `.hidden` was told it was closed.
That is why my own check reported it hidden and you saw it open.

It is `display: none` inline now, and the toggle sets `flex`. Verified:
`none → flex → none`.

## The cite box — forked

`_partials/record/cite-article.twig` carries the design's markup and inline
styles with the same script. `record/cite.twig` is untouched and its eleven
other templates are unaffected. When they are redesigned the two should merge.

## The attach step — fixed, 548 relations added

**Cause.** A file already in the volume was skipped with `continue` before its
id was recorded against its URL. The relation is built from that map, and the
map was filled only by the download loop. So a file fetched on one run and not
attached on that run could never be attached by any later run: the next run saw
the filename, skipped the row, and the picture stayed orphaned.

Downloading and relating are two jobs, and only one of them is done when the
file is already here.

**Fixed**, and the drive guard refined: it now fires only when there is
something to fetch, because relating needs no drive. `$RELATE_ONLY = true`
relates and fetches nothing. Run: **548 relations added, 3 bodies changed, 4
tokens inserted.**

3,003 images remain to fetch and still need the drive bound into DDEV.

## Captions — the filename fallback

Craft titles an untitled asset after its filename, so `garcesbakersfield.jpg`
became the caption **"Garcesbakersfield"**. A title that matches the filename
once case and punctuation are removed is now treated as absent and no caption
renders.

**562 of the 568 assets are in that state. Six have a real title.**

The extraction holds **5,220 non-empty captions** across 29,607 image rows, none
of which has been applied. That is a script to write, not a template change.

## Footnotes — the three states, measured

Correcting my own first count: **240 was wrong.** The marker regex matched
`In December [18]54`, a date the extraction broke. With the marker required not
to be preceded by a word character or followed by a digit:

| | records |
|---|---|
| carry footnote markers | **238** |
| we hold the notes on the record | **9** |
| the notes live on another record | **13** |
| orphan, no note anywhere | **216** |

Two notes records exist: `notes` #2173 in collection 871 and `editors-notes`
#1432 in collection 873. The 13 are chapters in those two collections.

This matches Grok's survey in shape: the orphan is the common case. Ours is 216
of 238, or **91%**.

## The fix list — two notes, both read

**#7313 `chapter-8-the-feast`** — "Can we do something with all the large quotes
on this page?" Five quote-opening lines in a 3,444-character body, no
recordImages. This is a pull-quote treatment question and needs a design
decision, not a fix.

**#7314 `chapter-9-the-trail-blazer`** — "This page is missing the picture in
the text." **Partly resolved by the attach fix**: the record now has four
related images including `pedrofagest.jpg`, so it appears in Photos &
Documents. It still does **not** appear in the prose, because the body has **0
image tokens**, and the file attached is the thumbnail `pedrofagest.jpg` rather
than the full-size `pedrofages.jpg` you linked. Both want the token pass and the
full-size fetch, which needs the drive.
