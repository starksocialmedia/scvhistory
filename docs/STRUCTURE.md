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
| **PEOPLE** — who acted | People · Organizations · Schools · Families · Obituaries · War Memorial · Military (when it has records) |
| **PLACES** — what is located | Places · Communities |
| **CIVIC** — the public record | The City Council · Elections · Bodies · Officeholders |
| **TIME** | By era · On this day · Events · The timeline (when it exists) |

**Built 29 September 2026**, four items, CIVIC waiting: `templates/_partials/header/site-header.twig`.

**Organizations are under PEOPLE, not PLACES** (Nathan, 28 September 2026). An
organization acts: it decides, employs, publishes. Filing it under PLACES said an
organization is a kind of place, the confusion that moved Acton Hotel and the
missions out of the organizations section. Nor do they all go under CIVIC:
Newhall Hardware, Southern Pacific and Stack's act, but they are not the public
record. So PEOPLE is the door for who acted, people and organizations alike,
with a Government chip on `/organizations`. CIVIC's Bodies column is that
government view of the same URLs, not a second home for them.

**CIVIC waits for elections.** Today it would hold four government bodies, no
office holdings and no elections: another `/military-profiles`. It appears when
the elections import lands, by the rule below, with no one having to remember.

### The rule for every nav link

**A nav link appears only when its page exists and has records.** Both, checked
at render: the template is there, and the query behind it returns something. A
count alone proves only that there is something to fail to show (the
`/photographs` 404); a template alone gives a room with nothing in it (the
`/military-profiles` index). This applies to every item, column and row of the
menu, and to landers: a door opens when there is something behind it.

### The rule that makes links appear

The nav-link rule only takes links away. Nothing put one in when a page filled:
`/schools` had records for an hour before anyone could reach it. So
`scripts/import/check_render.php` lists every destination from the site itself
(each section and category group with URLs and live records, each top-level
index template, each static page) and fails unless each is linked from the
header menu or the footer, or is named in `templates/_data/nav-exempt.json`
with a reason. The render check runs before every commit that touches a
template, so new material cannot land unreachable and be reported as done.

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

How a governing body links to the territory it governs over time: the city's
annexations, a district's boundary as it changed. The school districts now carry
their current boundaries (from the Census Bureau, on their pages); history of
territory is not modelled.

**Dropped, 29 September 2026: `jurisdictionLevel`.** It was proposed twice
(2026-09-25) to mark government bodies and their level, and never built.
Government is an `orgType` value, which is enough: /organizations groups by it,
the menu links to it, and the archive's bodies are few enough that each record
says whether it is the City, the County or a district. It is not in the schema
and not in DATA-MODEL.

## How the indexes group

/organizations and /places are **grouped into sections** by `orgType` and
`placeType` (Nathan, 29 September 2026), each record in exactly one section. On
/organizations school districts and schools are separate sections, because a
district governs and a school teaches; Government carries a line pointing to
the districts rather than listing them twice. **An index becomes filter chips on
the same groups once it passes about 60 records**, the point where sections stop
fitting on two screens.
