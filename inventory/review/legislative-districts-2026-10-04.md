> **Superseded figures (4 October 2026, later).** The shares below used the Census Bureau's Newhall division plus Agua Dulce, which left out City of Santa Clarita residents in eastern Canyon Country (12,778 in 2010, 13,364 in 2020). The corrected figures, with the whole City counted, are in inventory/review/valley-district-maps-2026-10-04.md and web/data/valley-districts/index.json, and are what the pages show. Three descriptions also changed: under the 2011 lines the 38th held the whole City and the 36th only unincorporated land; under the 1991 lines the 36th and 17th were the City and Agua Dulce; under the 2025 lines the 30th takes the City's eastern and southeastern edge, about 8,600 City residents, not 4,200.

# The valley's seats in the Assembly, State Senate and U.S. House, 1992 to 2026

Prepared 4 October 2026 by Claude (research subagent) for Nathan. Research only: nothing was written to the database or templates.
Machine-readable version: `inventory/review/legislative-districts-2026-10-04.json`.
Sources are saved in `inventory/sources/legislative-districts-2026-10-04/`; `manifest.json` there lists the URL, date read, sha256 and size of each file. Below, `S/` is short for that folder.

## The short answer

| Chamber | Main seat numbers since 1992 | When each took effect | Split now? |
|---|---|---|---|
| Assembly | **3**: 36, 38, 40 | 36 from Dec 1992; 38 from Dec 2002 (kept through the 2011 plan); 40 from Dec 2022 | Yes, slightly. AD 40 has 98%; AD 34 has Agua Dulce and about 1,700 people north of the City (plus Acton) |
| State Senate | **3**: 17, 21, 23 | 17 from Dec 1992 (kept in the 2001 plan); 21 from Dec 2012; 23 from Dec 2024 | No. The whole valley is in SD 23 |
| U.S. House | **2**: 25, 27 | 25 from Jan 1993 (kept in the 2001 and 2011 plans); 27 from Jan 2023 | Not today. From the November 2026 election (Prop 50 map) it splits three ways: CD 27 (89%), CD 26 (Castaic, Val Verde, Hasley Canyon), CD 30 (Agua Dulce, Sand Canyon area, Acton) |

Three things Nathan's note gets differently from the sources:

1. **The 40th Assembly District is held by Pilar Schiavo (D), not Suzette Martinez Valladares.** Valladares held the 38th (Dec 2020 to Dec 2022), lost the first 40th-District race to Schiavo in November 2022 by 522 votes (79,852 to 79,330), and has been the 23rd District's state senator since December 2024. Source: `S/sos/general-election-nov-8-2022/65-state-assemblymember.pdf`, `S/sos/general-election-nov-5-2024/37-state-senator.pdf`.
2. **The main Assembly seat in the 1990s was the 36th, not the 38th.** Under the 1991 plan the whole City of Santa Clarita was in AD 36 (Pete Knight, then George Runner). AD 38 held only the unincorporated west side (Castaic, Val Verde, Stevenson Ranch). The 38th became the main seat in 2002.
3. **The other district covering part of the valley now is AD 34 (Tom Lackey, termed out in 2026)**, and it is small: Agua Dulce and the unincorporated land north of the City. In the Senate there is no second district now.

The Senate changes lag the Assembly by two years because senators serve staggered four-year terms. The 2011 plan reached the valley's senate seat in 2012, but the 2021 plan only in 2024: from December 2022 to December 2024 the valley was still represented by Scott Wilk (SD 21) and Henry Stern (SD 27), elected in 2020 on the old lines.

## How the valley was defined, and how coverage was measured

- **Valley:** the Census Bureau's county subdivision **Newhall CCD** (census tracts 9200 to 9203: the City of Santa Clarita, Castaic, Stevenson Ranch, Val Verde, Hasley Canyon, Green Valley and the unincorporated land around them) plus the **Agua Dulce CDP**. This is a Census-drawn unit, so it avoids drawing my own line. For the 1991 plan (2000 blocks) the City as it stood in 2000 is added, because about 10,200 City residents then sat in tract 9108 outside the Newhall CCD.
- **Acton** is reported separately and not used to decide main or partial: it is in the South Antelope Valley CCD, over the Sierra Pelona. It never changes the answer.
- **Green Valley** (about 1,000 people) is in the Newhall CCD but at the head of Bouquet Canyon; a district holding only Green Valley is marked *fringe*.
- **Method:** every census block in the valley was assigned to its district, then population was summed.
  - 1991 plan: Statewide Database (UC Berkeley) file of 2000 blocks with their 1991 districts (`S/swdb/block00_district_txt.zip`), weighted by 2000 Census block population (`S/census2000/`).
  - 2001 plan: Statewide Database file of 2010 blocks with their 2001 districts (`S/swdb/districts2001_2010blocks_equivalency.zip`).
  - 2011 plan: Statewide Database 2011 equivalency files (`S/swdb/2011_*_state_equiv.dbf`).
  - 2021 plan and the 2025 congressional map: Census TIGERweb district boundaries (`S/tigerweb/poly_*.json`) with 2020 block points.
  - Cross-check: the TIGERweb boundaries for the 2001 and 2011 plans gave exactly the same counts as the Statewide Database files.
  - Results: `S/analysis/tally.json` and `S/analysis/tally_comm.json`.

Term dates: state legislative terms begin the first Monday in December after the election (California Constitution art. IV sec. 2(a)(3), `S/legislature/ca-constitution-art-iv-sec-2.html`). U.S. House terms begin 3 January. Service years are checked against the Secretary of the Senate's *Record of Members of the Assembly 1849-2026* and *Record of State Senators 1849-2026* (`S/legislature/`). Election results are from the Secretary of State's Statements of Vote (`S/sos/<election>/`).

## Assembly

### 1991 plan (Special Masters; elections 1992 to 2000), population 2000

| District | Role | Valley population | What it held |
|---|---|---|---|
| **36** | main | 177,771 (84%) | The whole City (2000 limits), eastern unincorporated, Agua Dulce, Acton |
| 38 | partial | 33,449 (16%) | Unincorporated west side: Castaic, Val Verde, Stevenson Ranch |

AD 36 members:
- **William J. "Pete" Knight** (R), 7 Dec 1992 to 2 Dec 1996. Elected 1992 and 1994. Left on election to the Senate (SD 17) in 1996.
- **George Runner** (R), 2 Dec 1996 to 2 Dec 2002. Elected 1996, 1998 and 2000. Left after six years and was not on the 2002 ballot. Prop 140 term limit as the reason: NEEDS_VERIFICATION.
- Sources: `S/sos/general-election-november-3-1992/assemblymember.pdf` (p. 6), 1994 and 1996 `assemblymember.pdf` (1996 is a scan, read by OCR and by eye), `S/sos/general-election-november-3-1998/sov1998-general.pdf`, `S/sos/general-election-november-7-2000/assemb.pdf`, Assembly Record.

AD 38 members:
- **Paula Boland** (R), in office from Dec 1990 under the 1981 plan. Elected 1992 and 1994 in this district. Not on the 1996 ballot.
- **Tom McClintock** (R), 2 Dec 1996 to 4 Dec 2000. Elected 1996 and 1998. Left on election to SD 19 in 2000.
- **Keith Richman** (R), from 4 Dec 2000 (elected 2000). Continued in the 2001-plan district below.

### 2001 plan (Legislature; elections 2002 to 2010), population 2010

| District | Role | Valley population | What it held |
|---|---|---|---|
| **38** | main | 222,641 (86%) | The City and Stevenson Ranch, with Simi Valley and the north San Fernando Valley |
| 37 | partial | 35,031 (14%) | Castaic, Val Verde, Hasley Canyon, Agua Dulce, Green Valley (district mostly Ventura County) |
| 36 | Acton only | 0 | Part of Acton (4,384) |

AD 38 members:
- **Keith Richman** (R), main seat from 2 Dec 2002 to 4 Dec 2006. Elected 2002 and 2004. Not on the 2006 ballot.
- **Cameron Smyth** (R), 4 Dec 2006 to 3 Dec 2012. Elected 2006, 2008 and 2010. Not on the 2012 ballot.
- Sources: `S/sos/general-election-november-5-2002/state-assemb.pdf`, `S/sos/presidential-general-election-november-2-2004/formatted_st_AD_all.pdf`, `S/sos/general-election-november-7-2006/assembly.pdf`, `S/sos/presidential-general-election-november-4-2008/40_56_state_assembly.pdf`, `S/sos/general-election-november-2-2010/73-state-assembly.pdf`, Assembly Record.

AD 37 members:
- **Tony Strickland** (R), elected here in 2002. In office from 1998 under the old lines. Not on the 2004 ballot.
- **Audra Strickland** (R), 6 Dec 2004 to 6 Dec 2010. Elected 2004, 2006 and 2008.
- **Jeff Gorell** (R), 6 Dec 2010 to 3 Dec 2012 for this district. Elected 2010. His later district is outside the valley: NEEDS_VERIFICATION.

AD 36 members (Acton only, listed because Nathan named them):
- **Sharon Runner** (R), 2002 to 2008.
- **Steve Knight** (R), 2008 to 2012.

### 2011 plan (Citizens Redistricting Commission; elections 2012 to 2020), population 2010

| District | Role | Valley population | What it held |
|---|---|---|---|
| **38** | main | 238,891 (93%) | The City except its northeast edge, Castaic, Stevenson Ranch, Val Verde, Agua Dulce, with Simi Valley and Porter Ranch |
| 36 | partial | 18,781 (7%) | Northeast edge: unincorporated Canyon Country and Saugus land, Green Valley, plus Acton |

On 2020 blocks, AD 36's share is 22,682 (8%). Most of that land had been annexed to the City by then.

The Commission's 2011 report (`S/crc/crc_20110815_2final_report.pdf`, PDF page 37) says AD 38 "extends from the Simi Valley at the west to Castaic Lake and Agua Dulce to the north."

AD 38 members:
- **Scott Wilk** (R), 3 Dec 2012 to 5 Dec 2016. Elected 2012 and 2014. Left on election to SD 21.
- **Dante Acosta** (R), 5 Dec 2016 to 3 Dec 2018. Elected 2016. Defeated by Christy Smith.
- **Christy Smith** (D), 3 Dec 2018 to 7 Dec 2020. Elected 2018. Ran for Congress in 2020 instead.
- **Suzette Martinez Valladares** (R), 7 Dec 2020 to 5 Dec 2022. Elected 2020 against another Republican, Lucie Lapointe Volotzky. Lost the new AD 40 in 2022.
- Sources: SOV 2012 through 2022 Assembly files in `S/sos/`, Assembly Record.

AD 36 members:
- **Steve Fox** (D), 3 Dec 2012 to 1 Dec 2014. Defeated by Lackey.
- **Tom Lackey** (R), 1 Dec 2014 to 5 Dec 2022. Elected 2014, 2016, 2018 and 2020.

### 2021 plan (Citizens Redistricting Commission; elections 2022 on), population 2020

| District | Role | Valley population | What it held |
|---|---|---|---|
| **40** | main | 271,719 (98%) | "the whole City of Santa Clarita and portions of the City of Los Angeles" (2021 final report, PDF page 59), plus Castaic, Stevenson Ranch, Val Verde, Hasley Canyon |
| 34 | partial | 5,198 (2%) | Agua Dulce and about 1,700 people north of the City (upper Bouquet Canyon / Green Valley area, north of Castaic), plus Acton |

AD 40 members:
- **Pilar Schiavo** (D), 5 Dec 2022 to now. Elected 2022 (50.2%) and 2024 (52.8%).
- June 2026 primary: first with 55.6%. The second-place Republican is printed "Hayes II" (22.3%); the order of his first names is NEEDS_VERIFICATION.
- Sources: `S/sos/general-election-nov-8-2022/65-state-assemblymember.pdf`, `S/sos/general-election-nov-5-2024/42-state-assembly.pdf`, `S/sos/primary-election-june-2-2026/95-state-assembly.pdf`, `S/crc/Final-Maps-Report-with-Appendices-12.26.21-230-PM-1.pdf`.

AD 34 members:
- **Tom Lackey** (R), 5 Dec 2022 to 7 Dec 2026. Elected 2022 and 2024. Not on the 2026 ballot: the Assembly Record shows 2015 to 2026, which is 12 years.
- June 2026 top two: Randall Putz (D) 39.2% and Charles Frederick Hughes (R) 36.7%.

## State Senate

### 1991 plan (Senate elections 1992, 1996, 2000), population 2000

| District | Role | Valley population | What it held |
|---|---|---|---|
| **17** | main | 177,771 (84%) | Same area as AD 36 above, with the Antelope Valley, Inyo, parts of Kern and San Bernardino |
| 19 | partial | 33,449 (16%) | Same west side as AD 38 above, with eastern Ventura County |

SD 17 members:
- **Don Rogers** (R), in the Senate from 1987 under the old lines. Re-elected 1992 for this district (term 7 Dec 1992 to 2 Dec 1996). Not on the 1996 ballot.
- **William J. "Pete" Knight** (R), 2 Dec 1996 until he died in office on 7 May 2004. Elected 1996 and 2000. No special election found; the seat stayed vacant until George Runner took office.
- Sources: `S/sos/general-election-november-3-1992/state-senator.pdf` (p. 35), `S/sos/general-election-november-5-1996/state-senator.pdf` (scan), `S/sos/general-election-november-7-2000/sen.pdf`, Senate Record footnote 113 ("Died in office May 7, 2004. Succeeded by George Runner.").

SD 19 members:
- **Cathie Wright** (R), 7 Dec 1992 to 4 Dec 2000. Elected 1992 and 1996.
- **Tom McClintock** (R), from 4 Dec 2000. Elected 2000.

### 2001 plan (first Senate election on these lines: 2004), population 2010

| District | Role | Valley population | What it held |
|---|---|---|---|
| **17** | main | 187,591 (73%) | The City except its western neighborhoods, Castaic, Val Verde, Hasley Canyon, Agua Dulce, Green Valley, Acton |
| 19 | partial | 70,081 (27%) | Stevenson Ranch and the western neighborhoods of the City (about 50,000 City residents) |

SD 17 members:
- **George C. Runner** (R), 6 Dec 2004 to his resignation on 21 Dec 2010. Elected 2004 and 2008. He resigned on election to the Board of Equalization (Senate Record footnote 188; Governor's proclamation, `S/sos/special-elections/senate-district-17/proclamation.pdf`).
- **Sharon Runner** (R), from 2011 to 3 Dec 2012. She won the special primary of 15 Feb 2011 outright: 65.27% in the official canvass (`S/sos/special-elections/senate-district-17/official-canvass.pdf`). The Senate Record says 65.6%. Her swearing-in date is NEEDS_VERIFICATION.

SD 19 members:
- **Tom McClintock** (R), to Dec 2008. Re-elected 2004.
- **Tony Strickland** (R), 1 Dec 2008 to 3 Dec 2012. Elected 2008 (50.2%).

### 2011 plan (Senate elections 2012, 2016, 2020), population 2010

| District | Role | Valley population | What it held |
|---|---|---|---|
| **21** | main | 202,597 (79%) | Most of the valley with the Antelope and Victor valleys. The 2011 report (PDF page 50) says it "reunites the majority of the Santa Clarita Valley with that of the Lancaster Valley and Victor Valley communities" |
| 27 | partial | 55,075 (21%) | Stevenson Ranch and the western and southwestern neighborhoods of the City (about 35,000 City residents), with eastern Ventura County, Calabasas and Malibu |

SD 21 members:
- **Steve Knight** (R), 3 Dec 2012 to his resignation on 5 Jan 2015. Elected 2012. He resigned on election to Congress (Senate Record footnote 112).
- **Sharon Runner** (R), from 2015 until she died in office on 14 Jul 2016. She won the special primary of 17 Mar 2015 outright with 94.1% (`S/sos/special-elections/2015-sd21/election-results.html`; Senate Record footnote 189). Her swearing-in date is NEEDS_VERIFICATION.
- **Scott Wilk** (R), 5 Dec 2016 to 2 Dec 2024. Elected 2016 and 2020 (50.8%). The Senate Record shows 2017 to 2024.

SD 27 members:
- **Fran Pavley** (D), 3 Dec 2012 to 5 Dec 2016. Elected 2012.
- **Henry Stern** (D), 5 Dec 2016 to 2 Dec 2024 for the valley. Elected 2016 and 2020. He was re-elected in 2024 in a redrawn 27th that no longer includes the valley.

### 2021 plan (first Senate election here: 2024), population 2020

| District | Role | Valley population | What it held |
|---|---|---|---|
| **23** | whole valley | 276,917 (100%), plus Acton | The 2021 report (PDF page 72) lists "the whole Cities of Adelanto, Hesperia, Lancaster, Palmdale, Santa Clarita, and Victorville" |

SD 23 member:
- **Suzette Martinez Valladares** (R), 2 Dec 2024 to now (term to Dec 2028). Elected 2024 with 52.4% over Kipp Mueller.

## U.S. House

### 1991, 2001 and 2011 plans: the 25th throughout

| Plan | District | Role | Valley population |
|---|---|---|---|
| 1991 | **25** | whole valley | 211,220 (100%) |
| 2001 | **25** | main | 256,607 (99.6%) |
| 2001 | 22 | fringe partial | 1,065 (Green Valley CDP only) |
| 2011 | **25** | whole valley | 257,672 (100%) |

CD 25 members:
- **Howard P. "Buck" McKeon** (R), 3 Jan 1993 to 3 Jan 2015. Elected 1992 through 2012. The district was the 25th under all three plans. He did not run in 2014. Sources: SOV `us-representative` / `congress` files 1992 to 2012 in `S/sos/`; Bioguide (Wayback capture), `S/house/bioguide-M000508-wayback.html`.
- **Steve Knight** (R), 3 Jan 2015 to 3 Jan 2019. Elected 2014 and 2016. Defeated by Katie Hill in 2018. Bioguide: `S/house/bioguide-K000387-wayback.html`.
- **Katie Hill** (D), 3 Jan 2019 to her resignation on 3 Nov 2019. Sources: Bioguide `S/house/bioguide-H001087-wayback.html`; Governor's proclamation of 15 Nov 2019 calling the special election "resulting from the resignation of Representative Katie Hill".
- **Mike Garcia** (R), from the special general election of 12 May 2020 (54.86% over Christy Smith) to 3 Jan 2023.
  - In the special primary of 3 Mar 2020, Steve Knight placed third with 17.1%.
  - Garcia won the November 2020 general by 333 votes (169,638 to 169,305).
  - Bioguide dates his service from 12 May 2020. His swearing-in date is NEEDS_VERIFICATION.

CD 22 members (fringe, 2003 to 2013): Bill Thomas (R), elected 2002 and 2004; Kevin McCarthy (R), elected 2006, 2008 and 2010. The election results are confirmed. Their service dates for this district are NEEDS_VERIFICATION.

### 2021 plan: the 27th

| District | Role | Valley population | What it held |
|---|---|---|---|
| **27** | whole valley | 276,917 (100%) | "the whole Cities of Lancaster, Palmdale, and Santa Clarita, and a portion of the City of Los Angeles" (2021 report, PDF page 82) |

CD 27 members:
- **Mike Garcia** (R), 3 Jan 2023 to 3 Jan 2025. Elected 2022 over Christy Smith (53.2%). Defeated in 2024.
- **George Whitesides** (D), 3 Jan 2025 to now. Elected 2024 with 51.3%.

### 2025 congressional map (Proposition 50; used from the 2026 elections)

Proposition 50 was approved on 4 November 2025, 7,453,339 yes to 4,116,998 no (64.4%). Source: `S/sos/statewide-special-nov-4-2025/13-official-dec-vote-results-bm.pdf`. The Census Bureau's boundary layer is labelled "120th Congressional Districts; January 1, 2026 vintage" (`S/tigerweb/poly_cd120.json`). The June 2026 primary was held on these lines. Whether any court has acted on the map is NEEDS_VERIFICATION.

| District | Role | Valley population | What it holds | June 2026 top two |
|---|---|---|---|---|
| **27** | main | 246,532 (89%) | The City except the Sand Canyon area, Stevenson Ranch | Jason Gibbs (R) 41.0%, George Whitesides (D) 40.6% |
| 26 | partial | 22,621 (8%) | Castaic, Val Verde, Hasley Canyon | Jacqui Irwin (D) 41.3%, Sam Gallucci (R) 20.9% |
| 30 | partial | 7,764 (3%) | Agua Dulce and the Sand Canyon area of the City (tract 9200.43, about 4,200 residents), plus Acton | Laura Friedman (D) 52.7%, Alan Meyers (R) 16.6% |

Primary source: `S/sos/primary-election-june-2-2026/76-us-rep.pdf`. If this map stands, the valley's House representation is split from 3 January 2027.

## People Nathan named

| Person | Claim | Finding |
|---|---|---|
| Cameron Smyth | Assembly 38th, 2006 to 2012 | Confirmed: main seat, elected 2006, 2008 and 2010 |
| Scott Wilk | Assembly 38th 2012 to 2016, then Senate | Confirmed: AD 38 Dec 2012 to Dec 2016; SD 21 Dec 2016 to Dec 2024 |
| Dante Acosta | Assembly 38th 2016 to 2018 | Confirmed: lost to Christy Smith in 2018 |
| Christy Smith | Assembly 38th 2018 to 2020? | Confirmed: Dec 2018 to Dec 2020. She then lost CD 25 twice in 2020 and CD 27 in 2022 to Mike Garcia |
| Suzette Martinez Valladares | Assembly 38th, then 40th?, then Senate 23rd? | 38th confirmed (Dec 2020 to Dec 2022). **Never held the 40th**: she lost it to Schiavo in 2022. Senate 23rd since Dec 2024: confirmed |
| Patsy Ayala | "served in the California State Assembly and Senate" (City bio) | **Staff, not a member.** See below |
| Buck McKeon | U.S. House 1993 to 2015; "25 then 25 again?" | Confirmed: 25 in all three plans (1991, 2001, 2011) |
| Tom Lackey | | Partial districts only: AD 36 Dec 2014 to Dec 2022, AD 34 Dec 2022 to Dec 2026 |
| Steve Knight | Assembly 36th, Senate 21st, Congress 25th | All confirmed. His 36th (2008 to 2012) held only part of Acton, not the valley proper. SD 21 and CD 25 were the main seats |
| Sharon Runner | | AD 36 2002 to 2008 (Acton only). SD 17 by special election, 2011 to 2012 (main seat). SD 21 by special election, 2015 until her death on 14 Jul 2016 (main seat) |
| George Runner | | AD 36 1996 to 2002, then the **main** valley Assembly seat. SD 17 Dec 2004 to his resignation on 21 Dec 2010 (main seat) |
| Pete Knight | | AD 36 1992 to 1996 (main seat). SD 17 1996 until his death on 7 May 2004 (main seat) |

**Patsy Ayala: staff, not a member.**
- She is not in either official Record of Members. The only Ayala listed is Ruben S. Ayala, a San Bernardino County senator from 1974 to 1998.
- KHTS, as reprinted by SCVNews on 2 June 2014, reports that Assemblyman Scott Wilk named her field representative in his district office (`S/ayala/scvnews.com_ayala-joins-wilk-staff-hough-now-district-director.html`).
- Her College of the Canyons bio says she worked on Senator Wilk's team and earlier in his Assembly office, and later for Assemblywoman Valladares (`S/ayala/www.canyons.edu_community_womensconference_biopatsyayala.php.html`).
- The City's wording ("served in the California State Assembly and Senate") means staff service.

## Open items (NEEDS_VERIFICATION)

- Swearing-in dates after the special elections: SD 17 (Feb 2011), SD 21 (Mar 2015) and CD 25 (May 2020). The Senate Journal and the House Journal would settle them.
- Service dates for Bill Thomas and Kevin McCarthy in CD 22 (fringe district, Green Valley only).
- Jeff Gorell's district after 2012, and Tom McClintock's move to the U.S. House in 2008. Neither is in the saved sources, and neither district is in the valley.
- The reason Assembly members left after six years in the 1990s and 2000s (presumably Proposition 140). The saved constitution page is the current text.
- Any court action on the Proposition 50 map.
- The order of the first names of the second-place AD 40 candidate in the June 2026 primary.
- District numbers before 1992 were not asked and were not checked.

## Notes on the sources

- **Statements of Vote:** the 1992 and 1994 PDFs carry a rough text layer, and the 1996 PDFs are image-only scans. Those results were read by OCR and checked by eye against the page images and the Records of Members.
- **Bioguide:** the live site refuses scripted requests, so Wayback Machine captures of the retro Bioguide (November 2020) were used. The same holds for the House Clerk member pages.
- **Wikipedia:** the pages Nathan listed were used only as finding aids and are not cited.
- **2021 Commission site:** wedrawthelines.ca.gov refused scripted page requests. Its final report PDF (43.5 MB) downloaded directly and is saved.
- **Size of the source folder:** about 186 MB. The largest files are the three Statewide Database 2011 equivalency files (about 20 MB each) and the 2021 report (43.5 MB). No single file is over 50 MB.
