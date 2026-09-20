# Collections: the inventory map, a kind field, and the Perkins pilot

2026-09-20. **Nothing applied. No template changes.**

## 1. Every extracted set against a collection record

| inventory set | pages | maps to | what the pages are | in Craft |
|---|---:|---|---|---|
| `perkins` | 20 | **story-of-our-valley** | articles: a 13-part series plus 7 related pieces | 20 articles |
| `reynolds-full` | 80 | **history-of-the-santa-clarita-valley** | articles: 80 chapters | 80 articles |
| `reynolds` | 23 | same collection, an earlier partial crawl | articles | subset of the above |
| `worden` | 219 | **worden** | articles: a weekly Signal column, 1995–2009 | **0** |
| `coins` | 259 | **coins** | articles: a weekly coin column by Sol Taylor | **0** |
| `oldtownnewhall` | 231 | **the five otn-\* collections** (183 of them) | articles: five author columns | **0** |
| `oldtownnewhall` | 48 of the above | no collection | `/news` 26, `/newhall` 21, a subscribe page | 0 |
| `lw-features` | 1,661 | no collection, and correctly so | the photographs section: illustrated feature pages | 1,544 photographs |
| `warmemorial` | 54 | no collection, and correctly so | the war memorial section: person records | imported |
| `mentryville` | 44 | **no collection record** | a place feature set | 0 |
| `media` | 63 | **no collection record** | mixed media pages | 0 |
| `loose-pages` | 2 | **no collection record** | two orphans | 0 |
| `lw-map` | 2,725 | not a content set | a site map crawl | n/a |
| `scvhistory-map` | 12,000 | not a content set | a site map crawl | n/a |
| `sitemap`, `sitemap-2` | 5,845 | not a content set | crawl indexes | n/a |

Split per author for Old Town Newhall: otn-rioux 44, otn-pauline 38, otn-gazette
37, otn-whyte 37, otn-patti 27.

### Four collections have no crawl at all

**boston**, **manzer**, **newsmaker** and **iraq** have a record, a description
and a legacy URL, and **no inventory set covers their directories**:
`/scvhistory/signal/boston/`, `/signal/manzer/`, `/signal/newsmaker/`,
`/signal/iraq/`. They cannot be populated until somebody crawls them. Each
page's own body says roughly how much is there: Boston "a three-part account of
law and order in the early valley", Manzer "twenty-two pieces in the tree",
Newsmaker "two pages", Iraq "sixty-four pieces".

### Three sets have no collection to go to

`mentryville` (44), `media` (63) and `loose-pages` (2). Mentryville is the
clearest candidate for a new collection record; the other two are probably not
collections at all.

## 2. collectionKind

`collectionIsMajor` is a checkbox that answers "does this deserve a landing
page", which is a question about effort rather than about the material. A column
run of 219 pieces deserves a landing page and must not get a "Start reading"
button. `add_collection_kind_field.php` adds a dropdown:

- **book** — written to be read in order. Chapters, parts, a first page. The
  only kind that offers Start reading.
- **column** — a run by one author over time. Chronological by year, the author
  is the subject, no beginning.
- **catalogue** — discrete numbered items: issues of a periodical.
- **topic** — pieces by different hands gathered because they share a subject.

| collection | kind | why |
|---|---|---|
| history-of-the-santa-clarita-valley | **book** | 80 chapters in five parts |
| story-of-our-valley | **book** | a 13-part series with an introduction |
| worden | **column** | 219 weekly pieces, one author, fourteen years |
| coins | **column** | 259 weekly pieces by Sol Taylor |
| boston | **column** | John Boston's Signal run |
| manzer | **column** | Darryl Manzer's Mentryville columns |
| otn-patti / otn-pauline / otn-rioux / otn-whyte | **column** | one author each |
| newsmaker | **column** | a recurring Signal slot. Arguable: the authors differ, so **topic** is defensible |
| otn-gazette | **catalogue** | thirty numbered issues of a periodical |
| iraq | **topic** | sixty-four pieces by different hands on one subject |

The values are a judgement, so the script creates the field and sets nothing.

## 3. The Perkins pilot

### It is not an import, and the dry run caught something worse

All 20 Perkins pages are already articles in Craft. An importer would have made
20 duplicates and lost every relation the originals carry.

**And the first dry run, matched on `legacyKey`, was dangerously wrong.** Perkins
numbers his chapters `part01` to `part06`. So does Reynolds. The lookup matched
six *Reynolds* chapters, reported them as Perkins pages 96% destroyed, and would
have moved six chapters of History of the Santa Clarita Valley into Story of Our
Valley and broken the book.

**There are 18 duplicated legacyKeys across the corpus.** Matching is on the
legacy path now, which is unique because it is where the file actually was:
`/scvhistory/signal/reynolds/part01.html` against
`/scvhistory/signal/perkins/part01.html`.

A related correction: I said earlier that the series' Editor's Notes was filed
under the wrong book. It is not. `notes` is also a duplicated key, and the
key-based lookup was reading Reynolds's notes record. Perkins's `editors-notes`
has been in `story-of-our-valley` all along.

### What the pilot does

| | |
|---|---:|
| pages in the inventory | 20 |
| matched to an existing article | **20** |
| not in Craft at all | 0 |
| already in the collection | 13 |
| **to attach** | **7** |
| to move from another collection | 0 |

Only the seven related pages need attaching. Reading order: the series keeps the
order the legacy contents page gave it; the related pieces are appended after
it, because they are work by the same author about the same valley rather than
chapters. If they should be interleaved by date instead, that is one line and a
decision for you.

### Fidelity, on the photograph rebuild's terms

Words, not lines. **74,252 source words, 73,038 ours, 1,300 missing, 1.8%.**

| page | role | source | ours | missing | |
|---|---|---:|---:|---:|---:|
| part01 1. Early Inhabitants | series | 5,341 | 5,329 | 12 | 0.2% |
| part05 5. Mining | series | 6,689 | 6,680 | 11 | 0.2% |
| part06 6. Oil and Newhall | series | 6,745 | 6,736 | 13 | 0.2% |
| part02 2. Rancho San Francisco | series | 3,992 | 3,983 | 12 | 0.3% |
| hs_parade19680704book | related | 1,889 | 1,883 | 8 | 0.4% |
| perkins-pico-1958 | series | 5,294 | 5,263 | 39 | 0.7% |
| part03 3. The Placerita Gold Rush | series | 2,942 | 2,921 | 21 | 0.7% |
| perkins-newhall-1958 | series | 4,627 | 4,599 | 48 | 1.0% |
| part04 4. Early Transportation | series | 6,718 | 6,649 | 76 | 1.1% |
| intro Introduction | series | 768 | 758 | 10 | 1.3% |
| sg19630117depot | related | 1,088 | 1,067 | 25 | 2.3% |
| perkins-rsf-1957 | series | 12,407 | 12,134 | 295 | 2.4% |
| perkins_manuscript_ch2 | series | 5,251 | 5,115 | 136 | 2.6% |
| notes Editor's Notes | series | 1,299 | 1,253 | 46 | 3.5% |
| perkins032361 Tales of Lang | related | 2,234 | 2,141 | 101 | 4.5% |
| perkins-picocamp Pico Ghost Camp | related | 4,036 | 3,840 | 196 | 4.9% |
| sg19470102perkins Birth of Newhall | series | 915 | 860 | 55 | 6.0% |
| sentinel042865 Pardee House | related | 863 | 801 | 64 | 7.4% |
| perkins-fremont Fremont's Trek | related | 556 | 509 | 51 | 9.2% |
| **harthigh_may1952 Hart High** | related | **598** | **517** | **81** | **13.5%** |

Sixteen of twenty are under 3%. The worst, `harthigh_may1952`, is a picture
story where most of the page is captions, so the proportion is high on a small
number. None is in the state the photographs were.

Full before and after for ten records in `web/review/perkins-pilot.md`. Nothing
in any body changes: before and after is the relation.

## 4. What the Perkins page would show, and what it lacks

After the attach, `story-of-our-valley` is still `collectionIsMajor`, so it
renders `_lander.twig`:

- band with artwork, breadcrumb, kicker reading COLLECTION · **20 ARTICLES** ·
  the era range, title, "by Arthur Burnett Perkins" in italic, era and community
  chips from the chapters
- **Start reading → The Birth of Newhall**, and Listen
- stat blocks: 20 articles, publication runs, eras covered
- prose, then Contents: **one ungrouped list of 20** in reading order, since
  `collectionParts` is empty
- sidebar: cite, legacy link, author card, related collections

**For Perkins that is very nearly right**, because Perkins is a book. The one
thing it gets wrong is the flat list: the 13 series pieces and the 7 related
ones would run together with nothing saying which is the series. Two parts in
`collectionParts`, "The Series" and "Related Work", fixes that with no code.

**What the lander lacks for a column run** — worden with 219, coins with 259,
the five Old Town columns — is not a detail. It is the wrong page:

- **Start reading is wrong and must not render.** There is no first piece of a
  column. The gold primary control should be "Read the latest" or nothing.
- **There is no chronological list.** Contents renders in stored reading order,
  which for a column is meaningless. A column wants grouping by year with a
  count against each, and probably a year jump.
- **The author is a sidebar card.** For a column the author is the subject, and
  belongs in the band beside the title with the run of years under it.
- **The stat blocks are wrong.** "Chapters" means nothing; a column wants first
  and last date, and pieces per year.
- **80 rows is already too many** for the flat list on the Reynolds book, and
  259 would be unusable.

That is a `_column.twig` beside `_lander.twig`, chosen by `collectionKind`
rather than by `collectionIsMajor`. Not started, and not to be started until you
say.
