# The collections batch

2026-09-20. **Nothing applied.** Every script is `$APPLY = false` with read-back
verification; every template change passed `check_render.php`, 27 pages, no
failures.

## Stop points

Two of the three I was told to stop for came up.

**The coins schema: no new entry type, and no schema change.** The brief asked
whether a coin needs `identifier`, `denomination`, `mint`, `series`, obverse and
reverse images. It does not, because these are not coins. `coins.json` is a
weekly newspaper column *about* coins: 259 pieces by Dr. Sol Taylor, each with a
byline and a date, titled "Sleuthing at Garage and Estate Sales", "ANA Comes to
L.A. In 2009", "Q. David Bowers, America's No. 1 Numismatist". **Only 35 of the
259 titles name a coin**, and not one page carries an identifier, a denomination
or a pair of obverse and reverse images. A coin type would be right for a
cabinet of coins. This is a column and it imports as articles.

**That changes one kind.** `coins` is `column`, not `catalogue`. The one genuine
catalogue is `otn-gazette`: thirty numbered issues of a periodical.

**No fidelity result came in under 97%.** The lowest is 99.15%.

**Nothing touched the database.**

## A. Kinds and templates

`collections/_kind.twig` is a **macro**, not an include: an include has its own
scope and a variable set inside one never reaches the caller, and the
collections index needs the same answer. It falls back to `collectionIsMajor`
where `collectionKind` is missing or empty, so it is correct before the field is
applied and after.

| template | kind | what it does differently |
|---|---|---|
| `_lander` | book | unchanged. Start reading, parts, reading order |
| `_column` | column | author in the band, **no Start reading**, chronological by year, sticky year bar, Undated group |
| `_catalogue` | catalogue | sort by identifier or date, tiles fall back to the identifier when there is no cover |
| `_topic` | topic | grouped by record type with counts, because a subject has no order |

Mocked first in `design/collection-column-source.html`,
`collection-catalogue-source.html` and `collection-topic-source.html`.

Three decisions worth the words:

- **No Start reading on a column.** A column has no first piece. The band
  control is "Read the most recent".
- **An Undated group.** 16 of the 20 Perkins records carry no
  `originalPublishDate`. A run grouped by year has to put them somewhere and
  hiding them would be worse than showing the gap.
- **A catalogue tile with no picture shows its identifier.** The archive holds
  the text of the Gazette issues and not their covers. An empty frame says
  "broken"; a number says "this is issue 1403".

### `set_collection_kinds.php`

| kind | n | |
|---|---:|---|
| book | 2 | history-of-the-scv, story-of-our-valley |
| column | 8 | worden, coins, boston, manzer, otn-patti, otn-pauline, otn-rioux, otn-whyte |
| catalogue | 1 | otn-gazette |
| topic | 2 | iraq, newsmaker |

The brief said column 9, topic 1, **and** newsmaker = topic, which is one out:
moving newsmaker to topic makes it 8 and 2. Idempotent — a value set by hand in
the control panel survives a re-run.

### `create_mentryville_collection.php`

Title, byline and description read off `/mentryville/mstory.htm`, the set's own
front page, rather than composed. Kind `topic`: the 44 pages are a book chapter,
photographs, place records and Darryl Manzer's columns, by different hands and
in no order. Created **without members** — attaching 44 pages is an import with
its own fidelity check, not something to bundle into making a container.

## B. The imports

One engine, `_series_import.php`, behind three entry points. Three copies of a
body rebuilder is three places for the fidelity standard to drift, and the
standard is the point.

Structure transcribed from `body_html`, never inferred. Navigation stripped at
import because it reaches the meta description where no template filter can.
Footnotes to the table with `source = editor`. **Matched on the legacy path**,
because 18 `legacyKey`s are duplicated and matching on the key once reported six
Reynolds chapters as Perkins pages 96% destroyed. Words, not lines: the rebuild
reflows and the line metric cannot see through it.

| import | pages | source words | ours | missing | **fidelity** | images | new pairs |
|---|---:|---:|---:|---:|---:|---:|---:|
| Worden | 219 | 187,015 | 186,457 | 1,297 | **99.31%** | 39 | 2,337 |
| Making Cents | 259 | 272,243 | 272,636 | 1,659 | **99.39%** | 239 | 4,107 |
| OTN Gazette | 37 | 34,649 | 34,835 | 4 | **99.99%** | 16 | 497 |
| OTN Patti | 27 | 20,068 | 19,923 | 165 | **99.18%** | 1 | 382 |
| OTN Pauline | 38 | 27,316 | 27,083 | 233 | **99.15%** | 5 | 169 |
| OTN Rioux | 44 | 32,530 | 32,340 | 242 | **99.26%** | 11 | 418 |
| OTN Whyte | 37 | 36,146 | 35,874 | 276 | **99.24%** | 2 | 242 |
| | **661** | **610,067** | | | | **313** | **8,152** |

The five Old Town runs are measured **separately** rather than averaged: five
collections mean five chances for one set of pages to be shaped differently, and
a bad one must not hide inside a good mean. The engine refuses to write below
97% even with `$APPLY` on.

**8,152 new candidate pairs for the entity review queue.** That is a large
number and it is the mentions the crawl recorded, not confirmed relations: it is
the size of the review job these imports create, not work already done.

Structure kept: 3 tables, 168 list items, 1,043 line blocks. Ten-record before
and after in `web/review/import-worden.md`, `import-coins.md` and
`import-otn-*.md`.

## C. The collections index

Sorted by kind — books, column runs, catalogues, topics — then the uncrawled.
A reader looking for a serialised history should not scroll past four author
columns to find it.

**An empty collection shows its description, not a zero.** Four of them have a
record, a description and a legacy URL and were never crawled. Printing
"0 articles" on boston says the archive has nothing to say about John Boston,
when what is true is that his run has not been migrated. The card says **Not yet
migrated** and the description carries the legacy page's own account of how much
is there.

Rendered now: 13 cards, 2 Books with band art and counts, 11 under Not yet
migrated — correct for the current data, since the kinds are not applied yet.
After the applies it groups into all five sections.

## D. Images

**Pass 1 has landed.** The volume is **2,322 assets**, up from 568, and
`build_enlarge_index.php` now resolves **1,785 of 1,806 pairs** to a held file:
2 still on the drive, 19 on neither.

Magnifiers verified on the pages that were dead before:

| page | inline figures | with magnifier | tiles | with magnifier |
|---|---:|---:|---:|---:|
| rancho-san-francisco (Perkins) | 11 | **7** | 58 | **13** |
| 6-oil-and-newhall | 2 | **2** | 2 | **2** |
| chapter-10-solitary-hiker | 1 | **1** | 11 | **3** |
| the-birth-of-newhall-continued | 0 | — | 2 | **1** |

`chapter-5-tribal-relics` has no images at all, so no controls, which is the
rule working rather than a gap.

**Pass 3 is extended.** A fifth pass, `series`, reads the
`images-wanted-*.json` each dry run writes, so it covers every file Worden,
Making Cents and the five Old Town runs reference without anyone keeping a tally
by hand. 305 distinct files, 41 new, 258 not on the mirror.

Remaining passes: **plates 1,312 new**, **captioned 637 new**, **series 41
new**, **better 258 replaced**. 1,990 to create, 348 to replace, **4,312 assets
afterwards**.

## E. The deploy

Full runbook in **`docs/DEPLOY.md`**. Six things are wrong with the current
workflow:

1. **It pulls code and never applies the schema.** Deploying this batch through
   it would give the server templates reading fields that do not exist there — a
   500 on every record page, not a degraded one.
2. **There is no content.** Every record lives in the local database.
3. **There are no images.** `web/uploads/archive-media` is gitignored, correctly,
   so it has never deployed: 568 assets then, 4,312 after the passes.
4. **It authenticates with a password** in a GitHub secret rather than a key.
5. **Nothing checks whether it worked.** A failed `git pull` still reports
   success.
6. **It has not fired.** `main` is **156 commits behind**, last touched
   17 September.

`main` is an ancestor of `templates-batch-9`: **fast-forward, no conflicts, zero
commits to reconcile.** Do not merge until the applies have been run and the
project config they write has been committed, or main carries templates that
expect fields the config does not declare.
