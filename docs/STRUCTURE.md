# Structure

How the archive is organised, why, and what is still undecided. Written
2026-09-25, after a survey of all 17 sections measured against the running site
rather than read off the config.

## The principle

The archive organises its *storage* by record type, which is a cataloguer's
structure and the right one for a catalogue. Readers do not arrive wanting a
record type. They arrive wanting a time, a place, a person or an event.

So the navigation is the reader's axes, not the store's categories. Three
distinctions matter, and only the first two belong in the nav:

- **Sources** — what the archive holds: articles, photographs, documents,
  obituaries. The evidence.
- **Subjects** — what it is about: people, places, bodies, events. The index to
  the evidence.
- **Routes** — collections, eras, communities, on-this-day. These are not a
  third kind of thing. They are ways of traversing the first two, and giving a
  route a nav slot as a peer is what put BY ERA beside PEOPLE as though the two
  were comparable.

A **topic** is a subject area dense enough to deserve a guided entrance: the
civic layer, the military layer. A topic is not a kind of record. When a topic
gets a nav slot for being a kind rather than for being dense, every other topic
has a claim on one too.

The archive has already run that experiment. `warMemorials` (54 records) and
`militaryProfiles` (0 records) are a topic layer with its own record types,
filed under PEOPLE → MEMORIAL, where nobody looking for the valley in Vietnam
would find it, and `/military-profiles` is a live index with nothing in it. A
room built before it was furnished. **Build the lander last.**

## The nav

Five items. COLLECTIONS folds into ARCHIVE as its first column, which frees the
fifth slot for CIVIC without growing the bar.

| Item | Columns |
|---|---|
| **ARCHIVE** — what we hold | Collections · Photographs · Articles · Documents |
| **PEOPLE** | People · Families · Obituaries · War Memorial · Military |
| **PLACES** | Places · Communities · Organizations |
| **CIVIC** — the public record | The City Council · Elections · Bodies · Officeholders |
| **TIME** | By era · On this day · Events · The timeline |

Photographs are 1,544 of 2,644 records, the largest holding and the most
browsable thing here. They belong in the first column of the first item, not the
third row of a submenu called MEDIA.

## URLs

**One record, one URL.** The civic layer gets a lander, not a path prefix.

```
/civic                                      lander
/elections                                  index: every election, every level
/elections/2012-04-10                       an election
/organizations/santa-clarita-city-council    a body — bodies ARE organizations
/photographs            /photographs/<slug>
/documents              /documents/<slug>
/communities/<slug>                          a community
```

Elections are flat at `/elections/`, not under `/civic/`, because school board
and water board elections belong in the same index as municipal ones. A body
stays at `/organizations/` because it *is* an organization, and
`organizations/_entry.twig` is already the body page with officeHolding
rendering into it; a parallel `/civic/bodies/` would give the City Council two
URLs and split its inbound links.

## The guard pattern, and the bug it caused

`_partials/header/site-header.twig` builds the mega menu from live counts:

```twig
{% if cPhotos > 0 %}
  {% set mediaItems = mediaItems|merge([{ t: 'Photo galleries', n: cPhotos, u: url('photographs') }]) %}
{% endif %}
```

The guard checks that records exist. It never checks that the template they link
to exists. `templates/photographs/index.twig` was never built, so the menu
advertised 1,544 records, with an accurate count beside them, behind a 404. The
same for `/documents`. Between them that is 58% of the archive.

**The rule: a nav guard checks the destination, not the payload.** A link is
shown when the template exists *and* the count is non-zero. A count alone proves
only that there is something to fail to show.

The homepage had the same fault in a different form: `templates/index.twig`
lists nine card sections and photographs is not one of them, so "In the archive"
printed **1,008 RECORDS** against a true total of 2,644.

## Build order

1. `templates/photographs/index.twig` — closes a live 404 on 58% of the archive
2. `templates/documents/index.twig` — and the elections import needs it for
   `sourceDocuments`
3. Photographs on the homepage; the record total corrected
4. The nav restructure above, with the guard rule applied to every item
5. `templates/elections/_entry.twig` and `/elections`
6. A connections panel on record pages
7. `/civic` — **last**, once elections and officeHoldings hold records

## Cleanup owed

- `rolesProbe`: a section with 0 records and no URLs. Delete.
- `/tags`: a live index over a category group with 0 terms. Populate or remove.
- `historicalPeriod` (14 terms) and `theme` (15 terms): vocabularies nothing
  links to. Give them a use or retire them.
- `orgType` is empty on 23 of 38 organizations.
- `roles` (81 records) has no URLs and no index. It is about to become
  load-bearing, because an office is a role.

## Connective tissue

The archive's advantage over a city website is that it can link a 2012 election
winner to a 1993 photograph and nine articles. Today that shows only on a record
page.

`/graph` already draws the real relation graph and is admin-locked at
`templates/graph/index.twig:15`. The smaller, better first move is a
**connections panel on record pages** — what links here, grouped by kind —
because it puts the tissue on the 2,644 pages people actually land on. The
election record is the best demonstration of it and should be built to *show*
the chain rather than list relations.

## The front door

The homepage is a list of lists: nine cards, an era bar, a recently-added rail.
Every element invites browsing a catalogue and none of them gives a reader
something to read. The researcher's front door is `/search`, which works and
already covers photographs. The curious reader's front door does not exist yet.

Proposed: three or four **start-here routes**, each a short path through five to
eight records mixing a photograph, an article and a person — "The valley before
the city", "How Santa Clarita got a city council", "The 1928 flood". No schema,
all editorial. Somebody has to write them.

## Still proposed, not decided

The civic layer's data model: a `jurisdictionLevel` field on organizations, and
how a body links to the territory it governs over time. See the proposal of
2026-09-25. Nothing there is built.
