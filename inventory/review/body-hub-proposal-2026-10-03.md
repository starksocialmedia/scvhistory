# A body's page as the hub for its people and its seats: proposal, with Hart as the worked example

3 October 2026. Claude Code, for Nathan. Nothing built. Counts are live, local DDEV.

## What is asked

- /persons gets a **Boards** group whose entries are bodies, not people; each goes to the body's page.
- A body's page carries, in order: the map (boundary, seats, schools), the current board with each member's seat and the superintendent, past members by term, and a control that switches the map between seats and shows who holds each.
- One partial for every body with seats: school districts, the City, SCV Water, and the rest. Not per body.
- McKeon appears on the Hart, City and House pages as one person in three places; his own page is unchanged.

## What the data already supports, and what it does not

| Part of the page | Source in the archive | State |
|---|---|---|
| The body | organization record | Held for every body (the House, Assembly, Senate and Supervisors since today). |
| Who held a seat, when | officeHoldings: holdingPerson, holdingBody, holdingDistrict (the seat), terms, how chosen, how ended, evidence | Held. 152 holdings after today's derivation; Hart's 24 derived terms wait for its roster (in progress). McKeon's three are on Hart, the City and the House. |
| The seats | place records, one per trustee area or division (25 trustee areas; SCV Water's divisions; council districts) | Held as records, **with a point only, no boundary**. |
| The district's boundary | Census school district boundaries, already drawn on /schools (web/data/schools.geojson) | Held for the five school districts. Not held for the City's council districts or SCV Water's divisions. |
| Seat boundaries (trustee areas, divisions, council districts) | none | **Not held.** The Census does not publish trustee areas. Hart publishes a "Trustee Area Voting Map" page; the County Registrar and the bodies hold the GIS files. Item 18 on the site proposals ("boundaries are not drawn until the bodies' files are in hand") and the open LA County GIS licence question both apply. |
| The schools | organizations with parentOrganization, and NCES points in schools.geojson | Held (_partials/district-schools, _partials/school-map). |
| Board officers (president, clerk) | not modeled; the district's own page gives this year's | Shown as the district lists them, dated, not stored per year (see decision 4). |
| The superintendent | **nothing structured** | See below. |
| Elections | elections and candidacies, by body and seat | Held (CEDA, the County's returns). |

## 1. Superintendents

**What the archive holds.** One person carries the role School Superintendent: H. Clyde Smyth (#15985), whose profile says he was Hart's superintendent "until 1992". Two photographs name a superintendent: #4533 (Smyth) and #4015, "Dr. Marc Winger, Superintendent 1997-2015". The mirror has no list of any district's superintendents; the word appears on 492 pages, all in passing (searched 3 October 2026 with the system grep, case-insensitive, all .htm in /scvhistory/). So the archive can name two of Hart's superintendents and none of the other districts'.

**Where they live: affiliations, yes.** A superintendent is employed by the board, not elected to it; an office holding would be wrong (no seat, no election, no term in law). The affiliation record proposed in the relationship model (kind "employed by", title as printed, years, evidence, footnotes) is exactly the shape: "Superintendent, William S. Hart Union High School District, to 1992". The same record carries a city manager, SCV Water's general manager, a newspaper's editor. The body page reads affiliations of kind "employed by" whose title is the body's chief executive, and shows the current one beside the board and the past ones in their own list.

**What would fill it.** The districts' own histories and board minutes (appointments of superintendents are board actions), The Signal's coverage, Leon's pages that name them (Smyth, Winger). A research pass per district, after the affiliations section exists. Until then the page says "The archive has not yet recorded this district's superintendents" and names what it holds.

## 2. Built once: the body hub

One partial, `_partials/civic/body-hub.twig`, included by organizations/_entry.twig for any organization that has office holdings or seats. Nothing in it names a body; everything comes from the data:

- **Seat label** from the holdings' seatLabel or the seat place's title ("Trustee Area", "Division", "District"); at-large where a holding has no seat.
- **Boundaries** from one file per body, `web/data/seats/<body-slug>.geojson`, its features keyed by the seat place's slug; the district boundary from schools.geojson as now. A body with no seat file still gets its map of the whole district and its schools, and the seat control still works on the tables (below).
- **Current board**: holdings on the body with howEnded "serving" or no end, ordered by seat; officers as the body's own page lists them, with the date read.
- **Chief executive**: affiliations, as above.
- **Past members**: holdings ended, by term start, newest first; grouped by seat where the body has seats, by decade where it was at large; each line "name, seat, years, how chosen, how ended", the evidence as the small mark the record pages already use.
- **The seat control**: a row of seat buttons above the map (the /schools picker pattern, built this morning). Choosing a seat highlights its boundary when the file is held, zooms to it, and filters the board, past members and elections to that seat, so "Area 3" on the map and "who holds Area 3" are the same view. With no boundary file it still filters the tables and says the boundary is not yet drawn.
- **Elections**: the body's elections by year and seat, linked.

The City (council at large to 2022, by district from 2024; mayors by year), SCV Water (divisions, its founding board of 2018, its general manager), the Castaic Lake Water Agency, the Newhall County Water District (tenures only), the House, Assembly and Senate (valley members only, no seats map) all use the same partial.

## 3. The worked example: the Hart district, with what we hold today

```
WILLIAM S. HART UNION HIGH SCHOOL DISTRICT                                   [district mark]
Named for William S. Hart. Grades 7 to 12; four elementary districts feed it.

[ All ] [ Area 1 ] [ Area 2 ] [ Area 3 ] [ Area 4 ] [ Area 5 ]
+-------------------------------------------------------------------------------+
| MAP: the district boundary (Census), its 23 schools (16 open) as points.      |
| Trustee areas: boundaries not yet held. Choosing an area filters the lists    |
| below; when the area file is in hand it is outlined and the map zooms to it.  |
+-------------------------------------------------------------------------------+

THE BOARD            as the district lists it, read 1 October 2026
  Area 1   Aakash Ahuja           2024 to 2028   elected 2024
  Area 2   Bob Jensen             2022 to 2026   Assistant Clerk   on the board since 2009
  Area 3   Cherise Moore          2022 to 2026
  Area 4   Erin Wilson            2024 to 2028   Clerk
  Area 5   Joe Messina            2022 to 2026   President         on the board since 2009
SUPERINTENDENT       not yet recorded. The archive names two past superintendents:
                     H. Clyde Smyth (to 1992) and Marc Winger (1997 to 2015).

PAST MEMBERS         (today 6 holdings; after the roster rebuild, every term from 1945)
  2003 to 2015   Gloria Mercado-Fortine     elected; lost in 2015
  2001 to 2013   Paul Strickland            elected; resigned May 2013
  1997 to 2001   Gloria Mercado-Fortine     elected; lost in 2001
  1979 to 1987   Buck McKeon                later on the City Council and in Congress
  ...

SCHOOLS              the existing district-schools list
ELECTIONS            1995 to 2024, by year and area, each linked
```

Today Jensen, Moore and Wilson have no person record (their records come with the Hart rebuild), so their names print without links until then, as the election pages already do.

McKeon's line on the Hart page and on the City page links to his one record; the House page lists him among the valley's members of Congress. A small "also" note on each line names his other bodies, the rule proposed for the person index.

## 4. Decisions for Nathan

1. **Seat boundaries.** Recommend: request the trustee-area and division files from each body (or the County Registrar's GIS) and draw nothing invented meanwhile; the seat control works on the tables without them. The LA County GIS licence question needs settling first if the County's files are used.
2. **Affiliations before superintendents.** Recommend: superintendents, general managers and city managers wait for the affiliations section (step 2 of the model), then a research pass per body.
3. **The Boards group on /persons** lists bodies, each with its count of members held, linking to the hub; it comes with the person index after the model work, as agreed.
4. **Board officers** (president, clerk) are shown as the body's own page gives them, dated, not stored by year. Recommend: no per-year officer records now; the profiles' sourced lines ("president for 2014") carry history.
5. **Build order.** Recommend: the partial on the Hart page first (after the roster rebuild), then the other four districts, SCV Water, the City; the House, Assembly and Senate pages last.
