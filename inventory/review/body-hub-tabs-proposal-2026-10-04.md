# The body hub in tabs, worked on the Hart district and the City together: proposal

4 October 2026. Claude Code, for Nathan. Nothing in this proposal is built. It builds on the
approved body-hub proposal (body-hub-proposal-2026-10-03.md) and the decisions of 4 October:
- On a district page the map is the subject, not reference, and it sits on the first tab with
  the board.
- The superintendent box shows only the present holder, and the full list is a section.
- Every connection says what it is.
- Past members are people who have left.

Hart and the City are designed together (Nathan: "between them they cover what any body
needs"). No other body is built until these two are settled.

## The two readers

- **Someone who wants to know what the body is** lands on the first tab and need not leave it.
  It tells them what the body is, where it is (the map), and who runs it now.
- **Someone researching a member's term** chooses a seat and opens History. There they find
  every holder of that seat with years, how each came and went, and how each fact is known.
  Elections gives the contests for the same seat, and the choice of seat stays set between tabs.

## The page, both bodies

```
HART DISTRICT                                    THE CITY
WILLIAM S. HART UNION HIGH SCHOOL DISTRICT       THE CITY OF SANTA CLARITA
Also known as Hart District · ...                Also known as ...
GRADES 7-12 · SEATS 5 trustee areas              INCORPORATED 15 December 1987 · SEATS 5 council
                                                 districts (at large to 2022)
[The district] [History] [Schools] [Elections]   [The city] [History] [Elections]
[In the archive]                                 [In the archive]

Seat: [All] [Area 1] ... [Area 5]                Seat: [All] [At large] [District 1] ... [District 5]
"The boundaries of the trustee areas are         "The boundaries of the council districts are
 not drawn ..."                                   not drawn ..."

TAB 1                                            TAB 1
  lead paragraph                                   lead paragraph
  MAP, full width: Census boundary, every          MAP, full width: the city boundary
  school as a dot (open or closed)                 (communities.geojson)
  THE BOARD NOW: five cards (seat, since,          THE COUNCIL NOW: five cards (district or
  term, earlier terms)                             at large, since, term, earlier terms); the
                                                   member who is mayor this year carries
                                                   "Mayor, 2026" on the card, nothing more
sidebar: logo (one line) · SUPERINTENDENT        sidebar: logo (one line) · CITY MANAGER
(present only, link to the list) · cite ·        (present only, link to the list) · cite ·
source · named for · external                    source · external

TAB 2 HISTORY                                    TAB 2 HISTORY
  the rest of the prose                            the rest of the prose
  TIMELINE of terms, 1945 on                       TIMELINE of terms, 1987 on (the existing
                                                   council timeline, moved here)
  PAST MEMBERS, one row per person                 PAST MEMBERS, one row per person
  SUPERINTENDENTS, with years                      MAYORS, by year (below)
                                                   CITY MANAGERS, with years
                                                   COMMISSIONS: Planning Commission and the
                                                   others, each linked, with members held

TAB 3 SCHOOLS (Hart only)                        (no Schools tab)
  every school, feeds into / takes pupils from

TAB ELECTIONS                                    TAB ELECTIONS
  by year and seat, linked                         by year and seat, linked; council
                                                   elections only

TAB IN THE ARCHIVE                               TAB IN THE ARCHIVE
  other people, each with the connection;          the same
  articles; documents and photographs;
  places; events; what it published
```

## The mayor

A mayor in Santa Clarita is a council member who holds the chair for a year. It is not an office
with its own election. The archive already holds it that way: an officeHolding with
selectionMethod `rotated`, on the City, office Mayor. The page shows it in three places, and in
none of them as a contest:

- **The card.** The council member who holds the chair this year carries "Mayor, 2026" on their
  card. There is no separate mayor card, box or portrait.
- **History, "Mayors".** A table by year: the year, the council member (linked), and "chosen by
  the council". Above it, one sentence: "The council chooses one of its members as mayor each
  year; there is no election for mayor." That sentence needs a source before it is printed: the
  City's municipal code or the City Clerk. Until one is held, the table prints without it.
- **Never on Elections**, never in the seat control, never in "the council now" as a sixth member.
  A rotated holding is not a seat, so the seat filter leaves it out.

What the archive holds today: one mayoralty, H. Clyde Smyth's of 1997. The table will say the
other years are not yet recorded. The City Clerk's list of past mayors is the source to fill it
from.

## Shared, and particular to one body

| Part | Shared | Hart only | City only |
|---|---|---|---|
| Band: name, aliases, facts | yes | GRADES | INCORPORATED (dateFounded, labelled by body kind) |
| Seat control above the tabs, kept across tabs | yes | trustee areas, at large before them | council districts, "At large" for 1987 to 2022 |
| Tab shell, linkable (#history, ?seat=), all content shown when scripts are off or printed | yes | | |
| Map on tab 1, full width | yes, one slot | Census district boundary and schools | city boundary from communities.geojson |
| The board now: cards with seat, since, term, earlier terms | yes | | "Mayor, YEAR" on the card of the member who holds the chair |
| Executive: sidebar present only, History list with years, from affiliations | yes | title Superintendent | title City Manager |
| Timeline of terms | yes, one partial (the council's, generalised) | from 1945 | from 1987 |
| Past members, one row per person | yes | | |
| Mayors by year | | | yes (rotated holdings) |
| Commissions and sub-bodies, with members held | yes, where the body has them | (none held) | Planning Commission and others |
| Schools tab | | yes | |
| Elections tab, filtered by seat | yes | | |
| In the archive tab, every connection stated | yes | | |

Everything in the shared column is one partial. A body that lacks a part has no tab, section or
control for it, and no body's name appears in the code. That is how the other districts, SCV
Water and the House and Assembly follow without new templates.

## What the two pages will show as not yet held

So you can judge the gaps before the page goes up:

- **Hart:**
  - superintendents before 1974 and between 1992 and Vierra (Marc Winger, 1997 to 2015, is named
    on photograph #4015 but has no record);
  - when Vierra took the post;
  - the trustee-area boundaries.
- **City:**
  - mayors other than 1997;
  - every city manager;
  - members of the Planning Commission and any other commission;
  - the seat of Patsy Ayala's 2024 District 1 term (held without one);
  - District 2, 4 and 5 seat records (only Districts 1 and 3 exist);
  - the council-district boundaries.

## Decisions for Nathan

1. **Tab names.** I recommend:
   - Hart: The district, History, Schools, Elections, In the archive.
   - City: The city, History, Elections, In the archive.
2. **The prose.** I recommend the record's first paragraph leads tab 1 and the rest goes to
   History. The alternative is all of it on tab 1, which pushes the map down.
3. **The timeline on History for every body.** I recommend yes. Hart's runs from 1945 with about
   80 people, so it scrolls inside its own frame.
4. **Mayors.** Are rotated holdings, read from the City Clerk's list, the right home for every
   past mayor? I recommend yes: they already exist in the model and need no new field.
5. **City managers and superintendents as affiliations** with the title as printed. Section built
   today, Hart's two recorded.
