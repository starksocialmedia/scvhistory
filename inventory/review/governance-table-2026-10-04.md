# Governance table for /civic

Claude (research subagent), 4 October 2026, for Nathan: "Rows as places, columns as the bodies governing that spot, each marked elected or appointed." Generated data and research only: nothing was written to the database, no template was edited other than the new data file, nothing committed.

- Data: `templates/_data/governance-table.json` (written by `scripts/import/build_governance_table.py`; reads saved files only).
- New sources, with manifest entries in `inventory/sources/legislative-districts-2026-10-04/manifest.json`: `lacounty/supervisorial-districts-2021.json` (LA County Supervisorial District (2021), Political_Boundaries MapServer layer 26), `lacounty/rrcc-division-boundaries-acton-agua-dulce-usd.geojson`, `gnis/DomesticNames_CA_Text.zip`, `tigerweb/zcta_2020_internal_points_scv.json`.
- Archive slugs were read with `ddev craft exec` (organizations by title, seat places by slug), read only.

## The table

Key: E-d elected by district; E-ta elected by trustee area; E-div elected by division (three directors per division). House "now" is the 2021 map; "from 3 Jan 2027" is the 2025 map (AB 604, enacted by Proposition 50), first elected in November 2026.

| Place | In the City | Supervisor | City Council | Elementary district | Hart district | SCV Water | Assembly | Senate | House now | House from 3 Jan 2027 |
|---|---|---|---|---|---|---|---|---|---|---|
| Valencia | yes | District 5 (E-d) | District 2 (at large to Dec 2026) | Newhall, Trustee Area 2 (E-ta) | Trustee Area 1 (E-ta) | Division 1 (E-div) | 40th (E-d) | 23rd (E-d) | 27th (E-d) | 27th (E-d) |
| Newhall | yes | District 5 (E-d) | District 1 (E-d) | Newhall, Trustee Area 3 (E-ta) | Trustee Area 3 (E-ta) | Division 1 (E-div) | 40th (E-d) | 23rd (E-d) | 27th (E-d) | 27th (E-d) |
| Canyon Country | yes | District 5 (E-d) | District 1 (E-d) | Sulphur Springs, Trustee Area 2 (E-ta) | Trustee Area 3 (E-ta) | Division 1 (E-div) | 40th (E-d) | 23rd (E-d) | 27th (E-d) | 27th (E-d) |
| Sand Canyon | yes | District 5 (E-d) | District 5 (at large to Dec 2026) | Sulphur Springs, Trustee Area 5 (E-ta) | Trustee Area 4 (E-ta) | Division 1 (E-div) | 40th (E-d) | 23rd (E-d) | 27th (E-d) | 27th (E-d) |
| Stevenson Ranch | no | District 5 (E-d) | not in the City | Newhall, Trustee Area 1 (E-ta) | Trustee Area 2 (E-ta) | Division 3 (E-div) | 40th (E-d) | 23rd (E-d) | 27th (E-d) | 27th (E-d) |
| Castaic | no | District 5 (E-d) | not in the City | Castaic, Trustee Area C (E-ta) | Trustee Area 1 (E-ta) | Division 3 (E-div) | 40th (E-d) | 23rd (E-d) | 27th (E-d) | 26th (E-d) |
| Val Verde | no | District 5 (E-d) | not in the City | Castaic, Trustee Area E (E-ta) | Trustee Area 1 (E-ta) | Division 3 (E-div) | 40th (E-d) | 23rd (E-d) | 27th (E-d) | 26th (E-d) |
| Agua Dulce | no | District 5 (E-d) | not in the City | Acton-Agua Dulce Unified, Trustee Area 2 (E-ta) | not in the district | not in the agency | 34th (E-d) | 23rd (E-d) | 27th (E-d) | 30th (E-d) |

**What the table shows.** Every row is under six to eight bodies at once. Inside the City: County, City, an elementary district, Hart, SCV Water, Assembly, Senate, House (eight). Outside it, the City drops out (seven), and in Agua Dulce three of them change: Acton-Agua Dulce Unified replaces both school districts, SCV Water does not reach it, and it sits in the 34th Assembly District, not the 40th (six). From January 2027 the House column splits three ways: the 26th for Castaic and Val Verde, the 30th for Agua Dulce, the 27th for the rest.

**City Council.** Districts 1 and 3 were filled in 2024 (District 1 by election, Patsy Ayala; District 3 by Jason Gibbs, sole nominee, appointed in lieu of election under Elections Code 10229 and serving as if elected). Districts 2, 4 and 5 have no member of their own until December 2026: the three members elected at large in 2022 (Laurene Weste, Marsha McLean, Bill Miranda) sit for the whole City until their terms end, and residents of Districts 1 and 3 are also represented by them until then. Source: `inventory/review/council-transition-2026-10-04.md` (Ordinance 23-4).

## Columns

| Column | Body (archive slug) | How filled |
|---|---|---|
| County Board of Supervisors | `los-angeles-county-board-of-supervisors` | elected by district (five supervisorial districts) |
| City Council | `city-of-santa-clarita` | elected by district (five districts, Ordinance 23-4); Districts 1 and 3 since December 2024; the three members elected at large in 2022 serve until December 2026, when Districts 2, 4 and 5 are first filled |
| Elementary school district | varies by row | elected by trustee area (the body varies by row; see each cell's bodySlug) |
| Hart Union High School District | `william-s-hart-union-high-school-district` | elected by trustee area |
| SCV Water | `santa-clarita-valley-water` | elected by division (three divisions, three directors each) |
| State Assembly | `california-state-assembly` | elected by district |
| State Senate | `california-state-senate` | elected by district |
| U.S. House, now (2021 map) | `united-states-house-of-representatives` | elected by district |
| U.S. House, from 3 January 2027 (Proposition 50 map) | `united-states-house-of-representatives` | elected by district (2025 map, AB 604, enacted by Proposition 50; first elected November 2026) |

Archive records: every body has an organization record except **Acton-Agua Dulce Unified School District** (no record; `bodySlug` is null). Seat places exist for City council districts 1 to 5, every trustee area of Hart, Newhall, Saugus, Sulphur Springs and Castaic (the cell's `seatSlug`), and SCV Water divisions 1 to 3. No seat places exist for supervisorial, Assembly, Senate or congressional districts, or Acton-Agua Dulce trustee areas, so those cells carry no `seatSlug`.

## The points

| Place | Lat | Lng | Point source | Census 2020 CDP |
|---|---|---|---|---|
| Valencia | 34.4018497 | -118.5700136 | Census Bureau, 2020 ZIP Code Tabulation Area 91355 internal point (TIGERweb) | none |
| Newhall | 34.3847198 | -118.530919 | USGS GNIS feature 272642, "Newhall" (Populated Place), primary point | none |
| Canyon Country | 34.4233293 | -118.4720281 | USGS GNIS feature 1946238, "Canyon Country" (Populated Place), primary point | none |
| Sand Canyon | 34.4213125 | -118.4253812 | USGS GNIS feature 273524, "Sand Canyon" (Valley), primary point | none |
| Stevenson Ranch | 34.3893864 | -118.5883407 | USGS GNIS feature 2583151, "Stevenson Ranch Census Designated Place" (Census), primary point | Stevenson Ranch CDP |
| Castaic | 34.4888822 | -118.6228656 | USGS GNIS feature 270335, "Castaic" (Populated Place), primary point | Castaic CDP |
| Val Verde | 34.4449952 | -118.657589 | USGS GNIS feature 1661607, "Val Verde" (Populated Place), primary point | Val Verde CDP |
| Agua Dulce | 34.4963817 | -118.3256348 | USGS GNIS feature 1660235, "Agua Dulce" (Populated Place), primary point | Agua Dulce CDP |

- **Valencia**: the GNIS "Valencia" populated place (feature 1661608, 34.4436063, -118.6095321) lies outside the City, west of Interstate 5 (unincorporated; Castaic Union Trustee Area E, House 26th from 2027). The row uses the Census Bureau 2020 internal point of ZCTA 91355, which is how the archive already draws Valencia's outline. That point is in southern Valencia, in the Newhall School District; much of Valencia is in Saugus Union instead (ZCTA 91354's internal point tests in Saugus Union Trustee Area 2).
- **Sand Canyon: the point is in the City** (Council District 5). GNIS has Sand Canyon only as a valley (feature 273524), not a populated place. Much of the Sand Canyon community is unincorporated (the County's "Sand Canyon" statistical area); the point is not in that part. A point in unincorporated Sand Canyon would read "not in the City" in the City column and could change the Sulphur Springs trustee area.
- **inCity** comes from the County's council-district layer and agrees with the Census 2020 Santa Clarita city polygon for every row. Each CDP row's point lies inside its 2020 CDP.
- Supervisorial District: all eight points are in **District 5**. LA County's layer 27, "Supervisorial District (Current)", has the same five areas as the 2021 layer.

## Open points (NEEDS_VERIFICATION)

- Acton-Agua Dulce Unified: the County's Division_Boundaries layer has five trustee areas (Agua Dulce's point is in Trustee Area 2). NEEDS_VERIFICATION that every seat is now elected by trustee area. The district's own resolution was not fetched.
- A point stands for one spot. Communities that straddle a line (Valencia across two elementary districts, Canyon Country across City districts, Sand Canyon across the City limit) have neighbours with different answers. The page should say so.
