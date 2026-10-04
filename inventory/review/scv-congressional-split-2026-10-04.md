# Santa Clarita Valley congressional districts: now and from January 2027

Claude (research subagent), 4 October 2026. Question from Nathan: how many U.S. House districts cover the Santa Clarita Valley now, and from January 2027 under the Proposition 50 map? Nathan's belief to test: the valley is now split between the 27th and the 30th, roughly in half.

Read-only research. Nothing was written to the database or templates. `S/` below means `inventory/sources/legislative-districts-2026-10-04/`.

## Answer

| Period | Map | Valley (Newhall CCD + Agua Dulce CDP) | City of Santa Clarita alone |
|---|---|---|---|
| Now, to 3 January 2027 (119th Congress) | 2021 Citizens Redistricting Commission plan | **One district.** CD 27: 276,917 (100%) | CD 27: 228,673 (100%) |
| From 3 January 2027 (120th Congress, elected November 2026) | AB 604, enacted by Proposition 50 | **Three districts.** CD 27: 246,532 (89.0%); CD 26: 22,621 (8.2%); CD 30: 7,764 (2.8%) | CD 27: 220,119 (96.3%); CD 30: 8,554 (3.7%) |

Population is the 2020 Census count (P.L. 94-171 POP100) by census block.

**Nathan's "27th and 30th roughly in half" does not hold for either period.** Today the whole valley, Acton included, is in the 27th; the 30th does not touch it. From January 2027 the 30th takes a small eastern edge (about 3% of the valley as defined, about 4% of the City, about 4% if the City's whole eastern edge is counted), and the larger piece that leaves the 27th goes to the 26th (Castaic, Val Verde, Hasley Canyon), not the 30th. It is not a half split by land area either: under the 2027 map the 30th holds about 7% of the valley's land (36.5 of 514 square miles, Acton excluded) and 17% of the City's land (12.0 of 70.8 square miles); the 26th holds 35% of the land (most of it the Castaic and Angeles Forest back country).

The prior pass (`inventory/review/legislative-districts-2026-10-04.md`, tally keys `tiger_cd119`, `tiger_cd120`) used TIGERweb district polygons. This pass used the official block equivalency files instead and reproduces its numbers exactly for the Newhall CCD (273,466 in CD 27 now; 246,532 / 22,621 / 4,313 in 2027). One correction to that pass is below (the City of Santa Clarita reaches outside the Newhall CCD).

## Plain statements for a public record

**Now (2023 to January 2027):** Under the congressional map drawn by the California Citizens Redistricting Commission in 2021, the whole Santa Clarita Valley lies in California's 27th Congressional District. The City of Santa Clarita, its unincorporated communities, Agua Dulce and Acton are all in the 27th.

**From January 2027:** Under the map enacted by California voters as Proposition 50 in November 2025, and first used in the June 2026 primary, the valley is divided among three districts: the 27th keeps about nine in ten residents, including nearly all of the City of Santa Clarita and Stevenson Ranch. Castaic, Val Verde and Hasley Canyon move to the 26th, and Agua Dulce, Acton and a small eastern edge of the City around Sand Canyon move to the 30th.

(The Sand Canyon label for the City's 30th District portion is NEEDS_VERIFICATION; see "Which communities" below.)

## Valley definition

Kept from the prior pass: the Census Bureau's Newhall census county division (CCD 92110; tracts 9200 to 9203; 2020 population 273,466: City of Santa Clarita, Castaic, Stevenson Ranch, Val Verde, Hasley Canyon, Green Valley and the surrounding unincorporated land) plus Agua Dulce CDP (3,451). Total 276,917. Acton CDP (7,431) reported separately.

**Correction found in this pass.** The City of Santa Clarita, as the Census Bureau drew it for 2020 (place 69088, 228,673 people, from the 2020 Block Assignment File `S/census-baf/BlockAssign_ST06_CA_INCPLACE_CDP.txt`), is not wholly inside the Newhall CCD. 13,364 City residents live outside it: 13,298 in census tracts 9108.07, 9108.08, 9108.09 and 9108.10 (eastern Canyon Country toward Sand Canyon and Agua Dulce, in the South Antelope Valley CCD) and 66 in tracts 9304.00 and 9203.12. The prior pass's "Santa Clarita city" label (219,464) came from the current City polygon and was limited to blocks inside the CCD. The City figures in this note use the full 2020 City (228,673, which matches the 2020 Census count). An extended valley (Newhall CCD + Agua Dulce CDP + those 13,364 City residents + the 963 unincorporated residents of tracts 9108.07 to 9108.10) is also reported below.

## Method

1. **Blocks and population.** Every 2020 census block in the valley: the 2,495 blocks of the Newhall CCD and Agua Dulce CDP from the prior pass (`S/analysis/scv_blocks.json`), plus all City of Santa Clarita blocks from the 2020 place Block Assignment File, plus the remaining blocks of tracts 9108.07 to 9108.10. Block population is POP100 from the Census Bureau's 2020 Census Blocks layer (`S/census-baf/tigerweb_2020_block_pop100_06037_06071.json`; Los Angeles County total 10,014,009, which matches the 2020 count).
2. **Now.** Each block assigned to its district by the Census Bureau's official 118th Congress block equivalency file for California, `06_CA_CD118.txt` inside `S/bef/cd118.zip`. The 2021 CRC plan is the plan for both the 118th and the 119th Congress. Every valley block was found in the file (0 unassigned).
3. **2027.** Each block assigned by the AB 604 block equivalency CSV published by the Senate Elections and Constitutional Amendments Committee, listed on its page as "Congressional Districts - AB 604 - Chaptered 8/21/2025 (Last Updated 8/18/2025)" (`S/bef/AB604_selc_equivalency.csv`). The Statewide Database's `AB604.zip` contains a byte-identical file (sha256 `ac11292bf0e862a0b2716dc687a9eb91fdc4b480ae115c0a21848f3fbd7173b2` for both). It is the enacted plan, not the 15 August draft: the draft equivalency (Senate committee media/600) differs from it in 84 blocks statewide. 0 valley blocks unassigned.
4. Script `S/analysis/bef_tally.py`, output `S/analysis/bef_tally.json`. Standard library only.

### Results by region

| Region | Population | Now (CD118 BEF) | 2027 (AB 604) |
|---|---|---|---|
| A. Newhall CCD | 273,466 | 27: 273,466 | 27: 246,532; 26: 22,621; 30: 4,313 |
| B. Agua Dulce CDP | 3,451 | 27: 3,451 | 30: 3,451 |
| **Valley as defined (A + B)** | **276,917** | **27: 100%** | **27: 89.0%; 26: 8.2%; 30: 2.8%** |
| C. City of Santa Clarita outside the CCD | 13,364 | 27: 13,364 | 27: 9,020; 30: 4,344 |
| E. Unincorporated rest of tracts 9108.07 to 9108.10 | 963 | 27: 963 | 27: 601; 30: 362 |
| Extended valley (A + B + C + E) | 290,244 | 27: 100% | 27: 256,153 (88.3%); 26: 22,621 (7.8%); 30: 11,470 (4.0%) |
| D. Acton CDP (separate) | 7,431 | 27: 7,431 | 30: 7,431 |

### Results by place (2020 Census places; unincorporated land outside any CDP shown as one line)

| Place | Population | Now | 2027 |
|---|---|---|---|
| Santa Clarita city | 228,673 | 27 | 27: 220,119; 30: 8,554 |
| Stevenson Ranch CDP | 20,178 | 27 | 27 |
| Castaic CDP | 18,937 | 27 | 26 |
| Val Verde CDP | 2,399 | 27 | 26 |
| Hasley Canyon CDP | 1,195 | 27 | 26 |
| Green Valley CDP | 1,036 | 27 | 27 |
| Agua Dulce CDP | 3,451 | 27 | 30 |
| Acton CDP | 7,431 | 27 | 30 |
| Unincorporated, no CDP (incl. 40 in place 41208) | 15,375 | 27 | 27: 14,820; 26: 90; 30: 465 |

### Which communities fall in each district from 2027

- **CD 27:** nearly all of the City of Santa Clarita (Valencia, Saugus, Newhall, most of Canyon Country), Stevenson Ranch, Green Valley and most unincorporated land around the City. The 27th also keeps Palmdale and much of the Antelope Valley (June 2026 SVC).
- **CD 26:** Castaic, Val Verde, Hasley Canyon and the northwest back country (tracts 9201.02, 9201.04, 9201.06, 9201.16, 9201.18, 9201.19). The rest of the 26th is most of Ventura County (Oxnard, Thousand Oaks, Camarillo), the Agoura Hills and Calabasas area, part of the City of Los Angeles and a corner of Lancaster (AB 604 City Splits Report).
- **CD 30:** Agua Dulce, Acton, and the City's southeastern and eastern edge: the City blocks of tracts 9200.43 (4,210 people, about half the tract), 9108.09 (1,893), 9108.10 (2,394) and 9304.00 (57). By location these are the Sand Canyon side of Canyon Country; the neighborhood name is NEEDS_VERIFICATION (no street-level check was made). The rest of the 30th is Burbank, Glendale, West Hollywood, part of Pasadena and part of the City of Los Angeles (AB 604 City Splits Report); Laura Friedman ran in it in June 2026.

### Official cross-check of the City split

The Statewide Database's AB 604 City Splits Report (`S/bef/swdb_AB604_City_Splits_Report.pdf`) lists Santa Clarita city as split two ways: CD 27 220,589 and CD 30 8,569, of 229,158. That is the Legislature's adjusted population (2020 count with incarcerated persons reassigned to their last address), so it runs slightly above the raw count; the split (96.3% / 3.7%) is the same as the block analysis here (220,119 / 8,554 of 228,673).

## Independent check 1: June 2, 2026 primary (Proposition 50 lines)

Source: Los Angeles County Registrar-Recorder/County Clerk, Statement of Votes Cast by precinct, Excel format, election 4338 (`S/lavote/4338_svc_excel_v4.zip`), one file per congressional contest, certified 26 June 2026. Each precinct's TOTAL row gives its community label, registration and ballots cast. Script `S/analysis/precinct_cd.py` (with a standard-library .xls reader, `S/analysis/xls.py`); output `S/analysis/precinct_cd.json`.

Registered voters in precincts labelled with valley communities, by the congressional contest on their ballot:

| RR/CC community label | CD 26 | CD 27 | CD 30 |
|---|---|---|---|
| Santa Clarita (the City) | | 149,539 | 6,415 |
| Stevenson Ranch | | 12,217 | |
| Castaic | 16,517 | 319 | |
| Valencia (unincorporated) | | 4,027 | |
| Canyon Country (unincorporated) | | 1,326 | 335 |
| Saugus (unincorporated) | | 984 | |
| Green Valley | | 716 | |
| Newhall (unincorporated) | | 124 | 78 |
| Agua Dulce | | 171 | 2,636 |
| **Valley total (195,404)** | **16,517 (8.5%)** | **169,423 (86.7%)** | **9,464 (4.8%)** |
| Acton (separate) | | 49 | 5,935 |

- City alone: CD 27 149,539 (95.9%), CD 30 6,415 (4.1%). The City's CD 30 voters are in two precincts, 6220005A (1,719) and 6220091A (4,696).
- Valley precincts in CD 26: 1040001A, 1040003A, 1040017B, 1040018A, 1040072A (all Castaic), and Gorman 2610001A.
- Contests on valley ballots: CD 27 (George Whitesides, Roberto Ramos, Caleb Norwood, Jason Gibbs), CD 26 (nine candidates including Jacqui Irwin), CD 30 (seven candidates including Laura Friedman). No valley precinct voted in any other congressional contest.

**Agreement with the block analysis: yes.** Three districts, the 27th dominant, Castaic in the 26th, Agua Dulce, Acton and an eastern edge of the City in the 30th. The shares by registered voters (86.7 / 8.5 / 4.8) are close to the population shares (88.3 / 7.8 / 4.0 on the extended valley). Small differences are expected: registration is not population, and the RR/CC community labels are its own and do not follow Census CDP lines (for example, 319 voters labelled Castaic and 171 labelled Agua Dulce vote in CD 27, outside those CDPs).

## Independent check 2: November 5, 2024 general election (2021 lines)

Source: same series, election 4324 (`S/lavote/4324_final_svc_excel_v2.zip`).

- Every precinct labelled Santa Clarita, Stevenson Ranch, Castaic, Valencia, Saugus, Newhall, Green Valley and Agua Dulce voted in the CD 27 contest (Mike Garcia, George Whitesides): 189,008 registered voters in 240 precincts. City alone: 151,543, all CD 27.
- Exceptions, all effectively empty: six precincts labelled Canyon Country or Agua Dulce were in the CD 29 contest, five with 0 registered and one, 1020192A, with 28 registered. A few Acton-labelled precincts with 0 to 4 registered were in CD 28 or CD 30. These lie outside the blocks counted here (the block equivalency puts every valley block in CD 27), so they are presumably slivers of the Angeles National Forest edge that the RR/CC labels by the nearest community. Their exact location is NEEDS_VERIFICATION; they do not change the answer.
- No valley precinct voted in the CD 30 contest.

**Agreement with the block analysis: yes.** One district, the 27th.

## What remains NEEDS_VERIFICATION

- The neighborhood name for the City's CD 30 portion (tracts 9200.43 part, 9108.09, 9108.10). "Sand Canyon area" is inferred from tract location, not checked street by street.
- Location of 2024 precinct 1020192A (28 voters, CD 29, labelled Canyon Country).
- Any court action on the Proposition 50 map after the June 2026 primary. The primary was held on it; nothing found here suggests it will not be used in November 2026.

## Sources (all read 4 October 2026; hashes in `S/manifest.json`)

- U.S. Census Bureau, 118th Congressional District block equivalency files: https://www2.census.gov/programs-surveys/decennial/rdo/mapping-files/2023/118-congressional-district-bef/cd118.zip (saved `S/bef/cd118.zip`).
- Senate Elections and Constitutional Amendments Committee, Proposed Congressional Map page: https://selc.senate.ca.gov/proposed-congressional-map (saved `S/bef/selc_proposed-congressional-map.html`); AB 604 Districts Equivalency File (CSV): https://selc.senate.ca.gov/media/604 (saved `S/bef/AB604_selc_equivalency.csv`).
- Statewide Database, AB 604 equivalency: https://statewidedatabase.org/pub/data/d25/AB604.zip (saved `S/bef/swdb_AB604.zip`); City Splits Report: https://statewidedatabase.org/pub/data/d25/AB%20604%20City%20Splits%20Report.pdf (saved `S/bef/swdb_AB604_City_Splits_Report.pdf`); Municipalities in district: https://statewidedatabase.org/pub/data/d25/AB%20604%20Municipalities%20in%20district.pdf (saved `S/bef/swdb_AB604_Municipalities_in_district.pdf`).
- AB 604 bill page: https://leginfo.legislature.ca.gov/faces/billNavClient.xhtml?bill_id=202520260AB604 (not saved).
- Los Angeles County RR/CC, past election results page: https://www.lavote.gov/home/voting-elections/current-elections/election-results/past-election-results (saved `S/lavote/past-election-results.html`); June 2, 2026 SVC Excel: https://content.lavote.gov/docs/rrcc/svc/4338_svc_excel_v4.zip (saved `S/lavote/4338_svc_excel_v4.zip`); November 5, 2024 SVC Excel: https://content.lavote.gov/docs/rrcc/svc/4324_final_svc_excel_v2.zip (saved `S/lavote/4324_final_svc_excel_v2.zip`); certified results release, 26 June 2026: https://content.lavote.gov/docs/rrcc/documents/25-20260626_certified-results_v1.pdf (saved `S/lavote/25-20260626_certified-results_v1.pdf`). The precinct PDFs (4338 SVC by precinct, 64 MB) were not saved.
- From the prior pass: 2020 place Block Assignment File (`S/census-baf/BlockAssign_ST06_CA_INCPLACE_CDP.txt`), 2020 block populations (`S/census-baf/tigerweb_2020_block_pop100_06037_06071.json`), valley blocks (`S/analysis/scv_blocks.json`). Land areas: TIGERweb tigerWMS_Census2020 layer 10 AREALAND (`S/analysis/area_2027.json`).
