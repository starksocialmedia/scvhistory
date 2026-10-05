# The site menu: a proposal

5 October 2026. Claude Code, for Nathan. Nothing here is built. The menu is
`templates/_partials/header/site-header.twig`; the footer is in
`templates/_layouts/base.twig`.

## What the menu is now

Five top-level items, each a dropdown of columns:

| Top level | Columns and items today |
|---|---|
| ARCHIVE | Collections (with the four largest collections), Photographs, Articles, Documents |
| PEOPLE | People, Groups; Organizations, "Government bodies" (`/organizations#government`); Schools and districts; Obituaries, War Memorial |
| PLACES | Places; Communities |
| CIVIC | The City Council (the City's record, `#council`); Elections, Districts; "Civic: the public bodies", Schools |
| TIME | By era, On this day, Events |

What has gone wrong with it:
- **Schools are in two places** (PEOPLE and CIVIC), and the government bodies in two (PEOPLE's
  "Government bodies" anchor and CIVIC's /civic). Each was added where it fitted at the time.
- **The War Memorial**, the most complete and best-sourced part of the archive, is the last item
  of a column under PEOPLE.
- **"Districts"** sits under ELECTIONS and means council districts, trustee areas and water
  divisions. Next to "Schools and districts" it reads as school districts.
- **CIVIC's first item** is the City Council, not the bodies; "Civic: the public bodies" is a
  label written to fit the column.
- The other organizations (businesses, churches, newspapers, clubs: 56 organization records, the
  public bodies among them) have no entry of their own once the bodies move to CIVIC.

## Every destination the site has

Index pages (a template and records):

| Path | What | Records | In the menu now |
|---|---|---|---|
| /collections | collections | 14 | ARCHIVE |
| /articles | articles; `?view=era`, `?view=collection` | 763 | ARCHIVE, TIME |
| /photographs | photographs; topics, communities | 1,563 | ARCHIVE |
| /documents | documents | 46 | ARCHIVE |
| /persons | people; `?era=` | 259 | PEOPLE |
| /groups | groups (families, parties, expeditions) | 7 | PEOPLE |
| /organizations | every organization; `?kind=`, `?level=`, `?community=` | 56 | PEOPLE |
| /civic | the public bodies, by level | (the government and school bodies) | CIVIC |
| /schools | schools and districts | | PEOPLE, CIVIC |
| /elections | elections | 96 | CIVIC |
| /districts | council districts, trustee areas, water divisions | | CIVIC |
| /obituaries | obituaries | 7 | PEOPLE |
| /war-memorial | the war memorial; `?view=roll` | 54 | PEOPLE |
| /places | places | 82 | PLACES |
| /communities | communities, on a map | 35 | PLACES |
| /events | events; `?era=` | 3 | TIME |
| /on-this-day | today in the archive | | TIME |
| /evidence | how the archive knows what it says | | footer only |
| /search | search | | the header's search box |
| /send | send a photograph for a record | | from the record (no likeness) |
| /tags | tags | 0 | hidden by the nav rule |
| /military-profiles | military profiles | 0 | hidden by the nav rule |

Pages (the `pages` section, all in the footer): About, Contact, Newsletter, Nonprofit,
Permissions, Photo Credits, Privacy Policy, Submit a Photo or Article.

Records reached only through their own pages: the City of Santa Clarita (CIVIC's "The City
Council" points at it), every body's tabs (board, elections, commissions), the eras and themes
(category groups with no pages of their own; eras are reached as `?view=era` and `?era=`).

Not reachable from the menu: /evidence and the eight pages (footer only, which is right for all
but /evidence); /send (from records, which is right); /tags and /military-profiles (empty).

## The proposal: six top-level items

**ARCHIVE · PEOPLE · PLACES · CIVIC · TIME · WAR MEMORIAL**

Top level is for what a reader comes looking for, not for how the database is built. Each of the
six is a question a reader brings: what do you hold, who, where, who governs, when, and who
served and died.

| Top level | Why it is top level |
|---|---|
| ARCHIVE | What the archive holds: the material itself. Most search traffic lands on an article or photograph. |
| PEOPLE | Who acted. The largest body of new work (profiles, holdings). |
| PLACES | What is located. The communities map is the way most readers place themselves. |
| CIVIC | Who governs. Since 3 October the largest new layer: the bodies, their boards, the elections, the seats. |
| TIME | When. Eras, the day, events. |
| WAR MEMORIAL | **New at the top.** 54 records, every one checked against federal sources; the part of the site a family arrives for, and the one a Memorial Day story links to. It is the archive's most complete record type and is now the last item in a PEOPLE column. |

### The dropdowns, in order

**ARCHIVE**, kicker WHAT WE HOLD
1. Collections, with the four largest collections beneath (as now)
2. Articles
3. Photographs
4. Documents
5. How we know (/evidence): what a record's notes, dates and confidence mean. **Moves up from
   the footer**: the archive's method is its claim to be trusted, and a reader who wonders where a
   fact came from should find it here. It stays in the footer too.

**PEOPLE**, kicker WHO ACTED
1. People
2. Groups (families, parties, expeditions)
3. Organizations: businesses, churches, newspapers, clubs (`/organizations`, the public bodies
   filtered out, since they have CIVIC)
4. Obituaries

**PLACES**, kicker WHAT IS LOCATED
1. Communities (the map)
2. Places

**CIVIC**, kicker THE PUBLIC RECORD
1. **Public bodies** (the page renamed from "Civic: the public bodies"; see the URL question below)
2. The City of Santa Clarita (the City's record, its council first)
3. Schools and districts (one home now; out of PEOPLE)
4. Elections
5. Election districts (`/districts`, renamed from "Districts": the council districts, trustee
   areas and water divisions seats are elected from)

**TIME**, kicker ERAS, DAYS AND EVENTS
1. By era
2. On this day
3. Events (3 records; shown while it has any, by the nav rule)

**WAR MEMORIAL**, kicker THOSE WHO SERVED
1. The War Memorial (/war-memorial)
2. The roll of honor (`/war-memorial?view=roll`)
3. Send a photograph: a line, not a link to /send itself (which needs a record), pointing readers
   to the records with no likeness. Optional; it can wait.

### What leaves the menu, and why
- "Government bodies" under PEOPLE: a duplicate of Public bodies.
- Schools under PEOPLE: one home, under CIVIC.
- War Memorial under PEOPLE: moves to the top.
- Nothing else is removed. /tags and /military-profiles stay hidden by the nav rule while empty;
  /military-profiles has never had a record and may be retired (a separate decision).

## Decisions for Nathan

1. **Six top-level items, with the War Memorial at the top?** The alternative is five, with a
   MEMORIAL column under PEOPLE holding the War Memorial and Obituaries, as now but first.
2. **The URL of Public bodies:** keep `/civic` with the page titled "Public bodies" (no link
   changes; recommended for now), or move to `/public-bodies` with a 301 from `/civic`.
3. **"Election districts"** for /districts, or another name ("Seats").
4. **/evidence in ARCHIVE** as "How we know", or footer only.
5. **The footer's "Submit a photo/article" page** now overlaps /send. Keep both (the page covers
   articles and anything without a record), or point it at the records with no likeness.
6. **/military-profiles:** keep the empty section or retire it.

The check: check_render's navigation coverage keeps passing with every change above (every
destination with records stays linked from the menu or the footer), and the nav-link rule still
drops an item whose page has no records.
