# Collections: what the two templates show, and what the 13 records look like

2026-09-20. **No changes made to collections.** Read-only survey, ahead of review.
Screenshots of all thirteen in `web/review/collections/<slug>.jpg`.

## The two templates

`collections/_entry.twig` is the entry point. It reads `collectionIsMajor` off
the layout and, when set, hands the whole page to `collections/_lander.twig`.
Otherwise it renders the standard record layout itself. So one section has two
quite different pages and a single checkbox decides which.

### `_lander.twig`, the major collection

A landing page for a serialised work. It shows:

- **Band** with artwork, one image layer and the gradient, breadcrumb inside it,
  a kicker row reading COLLECTION, the article count and the era range with gold
  rules between, the title in Playfair, the byline in italic, then chips for
  every era and community its chapters cover.
- **Two controls**: "Start reading" in gold, naming the first chapter, and
  "Listen to the collection" as a ghost button.
- **Stat blocks**: articles, chapters, publication runs, eras covered. Only the
  ones with a value render.
- **Tools row, prose, photographs** as any record.
- **Contents**: the whole reading order, grouped by the collection's own parts
  from `collectionParts`, each row showing sequence number, title, author, date
  and era. Reading order comes from `articlesInCollection` and is never
  re-sorted; a collection with no parts renders one ungrouped list.
- **Sidebar**: cite box, legacy link, author and editor cards, related
  collections by era.

Deliberately not the article treatment: a collection has parts, a reading order,
a start control and stat blocks, and those stay.

### `_entry.twig`, everything else

The shared record chrome: band with kicker, title, a facts row, chips, prose,
photographs, and a sidebar of cite, legacy link and author. It gains band
artwork where `bandImage` is set, and falls back to a 4:5 portrait thumbnail
where it is not.

## The thirteen records

| collection | template | band | chapters | parts | stats | chips | body words | sidebar |
|---|---|---|---:|---:|---:|---:|---:|---:|
| history-of-the-santa-clarita-valley | **_lander** | yes | **80** | 5 | 4 | 11 | 341 | 5 |
| story-of-our-valley | **_lander** | yes | **13** | 0 | 2 | 8 | 280 | 4 |
| worden | _entry | no | **0** | 0 | 0 | 0 | 301 | 3 |
| boston | _entry | no | **0** | 0 | 0 | 0 | 280 | 3 |
| manzer | _entry | no | **0** | 0 | 0 | 0 | 272 | 3 |
| newsmaker | _entry | no | **0** | 0 | 0 | 0 | ~180 | 2 |
| coins | _entry | no | **0** | 0 | 0 | 0 | ~190 | 3 |
| iraq | _entry | no | **0** | 0 | 0 | 0 | ~190 | 2 |
| otn-gazette | _entry | no | **0** | 0 | 0 | 0 | ~120 | 2 |
| otn-patti | _entry | no | **0** | 0 | 0 | 0 | ~170 | 3 |
| otn-pauline | _entry | no | **0** | 0 | 0 | 0 | ~130 | 3 |
| otn-rioux | _entry | no | **0** | 0 | 0 | 0 | ~150 | 3 |
| otn-whyte | _entry | no | **0** | 0 | 0 | 0 | ~150 | 3 |

All thirteen return 200. No template errors.

## What the survey shows

**Eleven of the thirteen collections contain nothing.** `articlesInCollection`
is empty on all eleven and no article carries a `partOfCollection` relation to
any of them. The pages render correctly and there is nothing in them: a title,
a byline, one paragraph of description, a cite box and an author card. The
Articles fact reads **0** on every one.

This is not a template fault. `/collections/worden` says in its own body that
the run "is the largest single-author series in the archive: 197 article pages
in the tree, and 219 recorded in the extraction once the /old/ subdirectory is
included". The description knows about 197 pages; the collection holds none of
them.

**The two that work are the two that were imported as series.** History of the
Santa Clarita Valley has 80 chapters in five parts; Story of Our Valley has 13
in one run. Both were built by an importer that wrote the relation. The other
eleven were created as descriptions and never populated.

**The plain collection is a thin page by construction.** With no chapters there
are no stats, no contents, no start control and no chips, so `_entry.twig`
renders a band with a title and a facts row reading "ARTICLES 0", then a
paragraph. Six of the eleven show an author card and a legacy link; the rest
show only the cite box.

**No collection has photographs.** `recordImages` is empty on all thirteen, so
the Photos and Documents section never renders.

**Two records have no author**: newsmaker, iraq, otn-gazette. Their pages carry
no byline and no author card, which leaves the band with a title and nothing
under it.

## What I did not do

Nothing was changed. The lander and the entry template are as they were at
commit `d8b787f`, when the band, kicker, chips, type and grid were brought onto
the article's design language, plus the image-link and caption work that reaches
every record template.
