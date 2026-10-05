# Acton-Agua Dulce Unified; the Santa Clarita Redevelopment Agency and the Newhall Redevelopment Committee

Claude, 4 October 2026. Read-only research for Nathan. Nothing was written to the database.

Sources are saved in `inventory/news/aadusd-redevelopment-2026-10-04/` with `manifest.json` (file, URL or mirror path, read date 2026-10-04, sha256, bytes, what). Mirror pages are copied under `mirror/` there, keeping their mirror paths. No finding rests on Wikipedia. Unconfirmed points are marked NEEDS_VERIFICATION.

Access notes: CDE's School Directory answered once (the district record) and then served a captcha, so the school-level CDE pages could not be read; NCES (Urban Institute API) carries the same state codes. CourtListener's opinion text needs an API key and Justia returned 403, so only the 1995 case's citation and docket are confirmed. The City's Granicus archive and the Signal and SCVNews sites refused automated reads. Web search was unavailable (session limit).

---

## 1. Acton-Agua Dulce Unified School District

### Record facts

| Field | Value | Sources |
|---|---|---|
| Legal name | Acton-Agua Dulce Unified School District | district site (every page); CDE directory "Acton-Agua Dulce Unified"; NCES "Acton-Agua Dulce Unified" |
| orgType / schoolLevel | school / district (unified, K-12) | CDE: "District Type Unified School District, Low Grade K, High Grade 12"; NCES agency_type 1, grades 0 to 12 |
| Unified | 1993 | district home page, Wayback 4 Jan 2000: "The District unified in 1993"; NCES CCD first lists leaid 0600001 in the 1993 file (grades K-9, three schools), and Vasquez High first appears in 1994 |
| Formed from | the Soledad-Agua Dulce Union (elementary) School District | NCES lists "SOLEDAD-AGUA DULCE UNION ELEME" (leaid 0637080, state id 1965003, K-8, three schools: Acton, Agua Dulce, High Desert) 1987 to 1992, and those same three schools under the new unified leaid from 1993, keeping their state school codes. SCVHistory's yearbook pages call Soledad-Agua Dulce "forerunner of the Acton-Agua Dulce Unified School District" |
| High school district it left | NEEDS_VERIFICATION | Not found. In 1949 the district's high-school pupils rode the bus to Hart in Newhall (pt9001). Whether by 1993 Acton and Agua Dulce were in the Antelope Valley Union High School District or the Hart district is unconfirmed. Do not enter a value yet |
| Office | 32248 N. Crown Valley Road, Acton, CA 93510-2620 | CDE directory; NCES 2022; the 2000 home page gives 32248 Crown Valley Road, P.O. Box 68 |
| NCES LEA ID | 0600001 | NCES CCD; CDE "NCES/Federal District ID 0600001" |
| CDS code | 19 75309 0000000 | CDE directory (district record, last updated 23 March 2022) |
| Predecessor CDS | 19 65003 (Soledad-Agua Dulce Union Elementary) | NCES state id 1965003 / 19650030000000 |
| Area | about 200 square miles, between the Santa Clarita Valley and the Antelope Valley | district homepage; 2000 home page |
| Enrollment | 1,060 (2024-25); 1,002 (NCES 2022) | County Committee presentation, 30 June 2025; NCES |

Founding-date caution: the district says "established in 1881." Leon Worden's pt9001 (2020) shows that is wrong for the forerunner: the Soledad School District existed by January 1878 (Los Angeles Herald), Perkins (1954) and Adams (1988) put its petition in June 1869, and the 1881 date comes from a mislabeled photo of the Little White School (1888). The Agua Dulce district began with a 1914 school; the two merged as the Soledad-Agua Dulce Union School District by a 1947 vote (212 to 67). Worden left open when the name became Acton-Agua Dulce ("after 1969"). NCES still names the elementary district Soledad-Agua Dulce through 1992, so the name likely came with unification in 1993 (NEEDS_VERIFICATION: no document seen states it). For the record's dateFounded, use 1993 as the unified district's founding, and give the 1869/1878, 1914 and 1947 history in the body, citing pt9001.

### Schools (NCES)

Open (NCES 2022 and 2023; district's 2025 presentation lists the same three):

| School | Grades | NCES ID | State school code (CDS = 19 75309 + code) | NCES years |
|---|---|---|---|---|
| Meadowlark Elementary (the district now calls it Meadowlark School, TK-4), 3015 W. Sacramento St., Acton | K-4 | 060000107534 | 6115679 | 1998 to now |
| High Desert (School), 3620 Antelope Woods Rd., Acton | 5-8 | 060000109444 | 6107494 | 1988 under Soledad-Agua Dulce (063708009444), 1993 to now |
| Vasquez High, 33630 Red Rover Mine Rd., Acton | 9-12 | 060000103278 | 1995786 | 1994 to now |

Closed district schools:

| School | NCES ID | State code | NCES years |
|---|---|---|---|
| Acton Elementary, 32248 N. Crown Valley Rd. (the district office site; its first wing built 1938 per pt9001) | 060000106293 (earlier 063708006293) | 6022735 | 1987 to 2004 |
| Agua Dulce Elementary, 11311 W. Frascati St., Agua Dulce | 060000106294 (earlier 063708006294) | 6022743 | 1987 to 2015 (marked closed). Closure year NEEDS_VERIFICATION from a district source |

Charters: from 2012 to 2018 NCES carries some 20 charter schools under the district's LEA ID, many far outside it (Pacoima, Van Nuys, Fontana, San Marcos, Valencia). Among them were Albert Einstein Academy's Valencia campus and an "Agua Dulce Partnership Academy" at the Agua Dulce Elementary address (2014 to 2018), iLEAD, Inspire, SCALE, Valiant, Method and others. From 2018 NCES lists them as separate LEAs. SCVHistory's tv20190514 records that Einstein won its charter from Acton-Agua Dulce after Newhall and Saugus turned it down. This is a story for the body; it does not make those charters the district's schools.

`templates/_data/school-directory.json` has no entry for this district (it holds Hart, Newhall, Saugus, Sulphur Springs and Castaic only), so the NCES IDs above come from the Urban Institute API and `inventory/legacy/authorities/schools-ca.json`.

### Board

Five trustees. The district's board page (read 4 Oct 2026; officers as the page gives them):

| Trustee | Office | Term |
|---|---|---|
| Brianna Taksony | President | 2024-2028 |
| William Mayes | Vice-President | 2024-2026 |
| Dr. Jorge De Jesus | Clerk | 2024-2028 |
| Ken Pfalzgraf | Member | 2022-2026 |
| Lester Mascon | Member | 2022-2026 |

Election method:
- At large through 2024. The district's CVRA forum flyer for 15 October 2024 says "the system we have in place right now is called 'at-large'."
- Trustee areas: the board adopted Map 105 by resolution on 13 February 2025, after hearings on 10 and 24 October, 14 November and 19 December 2024. On 30 June 2025 the district asked the Los Angeles County Committee on School District Organization to approve by-trustee-area elections and waive an election under Elections Code 5020(a)(2), so the change could apply from 2026. Sequence: **2026, Areas 1, 2 and 4; 2028, Areas 3 and 5** (County Committee presentation, slide 33).
- The County Registrar's Division_Boundaries layer has five trustee areas, TA1 to TA5 (`inventory/sources/legislative-districts-2026-10-04/lacounty/rrcc-division-boundaries-acton-agua-dulce-usd.geojson`; the features were created in September 2026). That strongly suggests the County Committee approved. The approving resolution itself was not seen: NEEDS_VERIFICATION.
- The three seats up in 2026 (Mayes, Pfalzgraf, Mascon) match the three areas elected in 2026.
- The County cancelled the district's board elections for lack of contest in 2022 (a full term and an unexpired term to 12/24) and 2024 (a full term and an unexpired term to 12/26). `council-transition-2026-10-04.md` already lists these; who was appointed is NEEDS_VERIFICATION.

Earlier trustees named in the mirror (not held): Larry H. Layton, more than ten years on the board, vice-president at his death in 2018 (obituary_lawrencehoyttlayton.htm, with a statement from Supt. Lawrence M. King); Elizabeth and Ray Billet (lw2974, held as #4755; obituary_rayfbillet.htm says Ray sat on the Soledad-Agua Dulce board); Ruby E. Blum, board president of the union district in 1949 (pt9001).

### What the archive holds

- No organization record. The governance table already has a null `bodySlug` for it.
- Communities: Acton (category #186) is related to 16 articles, 48 photographs and 1 war memorial; Agua Dulce (#187) to 14 articles, 28 photographs, 1 place, 1 group and 1 war memorial.
- Text mentions: #12348 "What's year-round schooling all about?" (Worden, 18 June 1997): "Castaic and Acton-Agua Dulce have their own districts"; #4755 Blum Ranch (the Billets on "the Acton-Agua Dulce school board"); Soledad School in #1428, #1434, #2115, #12542, #12574 and #27374 (Perkins, Reynolds). The Callahan's Old West photographs (#2709 and others) mention the Acton-Agua Dulce *Town Council*, not the district.
- Not yet in the archive (mirror only): pt9001 (the district's early history), the Acton School yearbooks 1967-68 and 1968-69, hs9910, ap3125, ap3237, the Layton and Billet obituaries, tv20190514. The district's own history page links to six of these SCVHistory pages.

---

## 2. The Santa Clarita Redevelopment Agency and the Newhall Redevelopment Committee

### (a) The Agency

| Fact | Value | Sources |
|---|---|---|
| Legal name | Redevelopment Agency of the City of Santa Clarita (the City's 2012 resolutions). The City's ROPS calls it "The City of Santa Clarita Redevelopment Agency"; press and the Gazette call it the "Santa Clarita Redevelopment Agency" | sc-rda-board-agenda-item-2012-01-24.pdf; sc-successor-agency-agenda-item-2012-04-24.pdf |
| Board | the five City Council members | Signal 7 Nov 2000; Worden 11 Feb 2000 ("the redevelopment agency board and the City Council are the same five people"); Gazette May-June 2006 ("governed by the members of the Santa Clarita City Council and run by city staff") |
| Created | 1989 | City of Santa Clarita, "The Redevelopment Story of the City of Santa Clarita" (2012): "the Santa Clarita City Council formed the Redevelopment Agency (Agency) in 1989." Jo Anne Darcy's City biography supports an agency existing by 1991 ("Elected Director Santa Clarita Redevelopment Agency, 1991, reelected 1992-1995"). Exact date and ordinance: NEEDS_VERIFICATION |
| First project (failed) | A citywide plan adopted after the 17 January 1994 Northridge earthquake, about $1.1 billion over 30 years. The Castaic Lake Water Agency sued: *Castaic Lake Water Agency v. City of Santa Clarita* (1995) 41 Cal.App.4th 1257, filed 21 December 1995, B088277, on appeal from LASC BC101876. Worden: the plan was "killed in the courts because it violated state law" and cost about $1.3 million in attorney fees | Worden 1 Mar 1995 (the agency "quietly celebrated its first birthday last week", meaning the plan was adopted about late February 1994: NEEDS_VERIFICATION); Worden 3 Apr 1996; Worden 11 Feb 2000; CourtListener metadata. The holding is NEEDS_VERIFICATION (opinion text not read) |
| Project area | Newhall Redevelopment Project Area, the only one at dissolution | City ROPS 2012 ("Project Area(s): Newhall Redevelopment Project Area"); Jan. 2012 report |
| Project area adopted | 8 July 1997, when the Council (Smyth mayor; Heidt, Darcy, Boyer, Klajic) adopted the initial plan | archive #4953 (LW3136); the City booklet gives 1997; Worden 25 June 1997 (the plan cleared its last hurdle when CLWA came to terms); Signal 8 Jan 2003 (the zone was "established in 1997"). Ordinance number NEEDS_VERIFICATION |
| Extent | downtown Newhall plus corridors: west to I-5, north to Magic Mountain Parkway, south to Highway 14 | Gazette Nov.-Dec. 2005 editorial |
| Money | borrowed from the City's general fund until June 2008, when it sold tax allocation bonds ($27.85 million general, $8.15 million housing). It spent about $2.4 million from 1993 to 1999, much of it on the failed 1994 plan | Gazette Summer 2008; Worden 11 Feb 2000; ROPS (2008 bonds; City loans) |
| What it did | facade and parking-lot improvement programs; grants to the Canyon Theatre Guild (one of its first) and the Repertory East Playhouse; Railroad Avenue; Main Street restriping and the five-block streetscape; the Newhall Community Center; Veterans Historical Plaza; Newhall Metrolink Station area; land for the Old Town Newhall Library; purchase of the 1.7-acre Avery block, Main and Lyons, in November 2009 for $6,216,000; affordable-housing site on Newhall Avenue; Newhall Roundabout design. It held eminent domain powers but never used them | City booklet 2012; sg092200; Gazette 2006 to 2008; sc1201; sc1505 |
| Dissolution | ABX1 26 (signed 29 June 2011). After *California Redevelopment Assn. v. Matosantos* (December 2011) upheld it, all agencies were dissolved as of **1 February 2012** | City agenda reports 23 Aug 2011 and 24 Jan 2012; Housing Successor report FY 2014-15 |
| Successor | The City became the Successor Agency automatically on 1 February 2012 (the Council did not decline it). On 24 January 2012 the Council was asked to adopt a resolution making the City the Successor Housing Agency; the City's site confirms it is the Housing Successor. On 28 February 2012 the Council, "acting as Successor Agency," adopted the first ROPS. A seven-member Oversight Board, required by the Act, was still being seated in April 2012 | the four City agenda reports; housing-successor page |

### (b) The Newhall Redevelopment Committee

- **Established:** 1996. Mayor Clyde Smyth wrote in the Gazette of February-March 1997: "In December, a 17-member citizen redevelopment committee was formed." That is contemporary, so December 1996. Later sources say only 1996: Signal 8 June 2002 ("established in 1996"), hs9019 ("since its inception in 1996"), and Gazette Nov.-Dec. 2005. Worden's Summer 2007 Gazette says "early 1996", which conflicts with Smyth. Keep dateFounded 1996, and record December 1996 with Smyth as the source.
- **Role:** advisory to the Redevelopment Agency. Ellis, Gazette Nov.-Dec. 2005: "established as an advisory body to the Santa Clarita Redevelopment Agency"; it advised on land-use applications and development standards in the redevelopment area.
- **Size:** 17 members (1996 to 2002: Smyth 1997, Signal 2001 and 2002). Thirteen members by 2005 (Ellis).
- **Terms:** The Signal of 8 June 2002 says members "are appointed without term limits," which is where record #16290 gets its wording. Ellis (2005) says four-year terms, appointed by the Agency board. The rule may have changed between 2002 and 2005 (NEEDS_VERIFICATION). The record should give both, each dated.
- **Chairs named:** Larry Bird (2001), Leon Worden (2002), Duane Harte (2003), Philip Ellis (2005-2006).
- **Last contemporary mention:** Summer 2008 Gazette (a split vote at the June 2008 meeting). After that, the City's Historic Preservation Ordinance amendment (approved 27 November 2012, adopted 8 January 2013) lists the committee among the groups consulted in public meetings held before adoption, so it was still meeting into 2010-2012 (exact dates not given).
- **End:** SCVHistory's chronology (timeline.htm): "2012 March 1: Newhall Redevelopment Committee formally dissolved, after state outlaws redevelopment agencies." That is the only dated source found. The City action that ended it (a resolution or minute order of about February or March 2012) was not found: NEEDS_VERIFICATION. No source shows it being replaced by another body. The Arts Commission and the Old Town Newhall Association are separate bodies with no stated succession.
- **Suggested record values:** dateDissolved 2012-03-01 (EDTF 2012-03-01), evidence "later account" (the chronology), with NEEDS_VERIFICATION until a City document is found. No successor.

What the archive holds on (b): record #16290 (body from Signal 8 June 2002), affiliations #29573 (Worden, chairman) and #29575 (Shapiro, alternate), plus articles #12645 and #12613 (Ellis), #12585, #12589, #12603, #12605, #12609, #12621, #12641 (Gazette editorials), #12258/#12280, #12334, #12386, #12538/#12550, #12595 and #12196. Agency mentions include #12302 (Worden 11 Feb 2000, the most detailed on its finances), #12382, #12412/#12540, #12524, #12230, #12326, #12585, #12587, #12591, #12605, #12621, #27862 (Darcy) and #4953. The City's 2012 booklet is carried on the mirror page `sc_newhallredevelopment19972012.htm`, but only its thumbnail is in the archive (asset #14913). The page and PDF are not held.

Also not in the archive: timeline.htm (the only dated end of the committee), sc1201/tv1201 (Avery block), cityhistoricordinance010813, smyth297, the Signal pages sg011001b, sg061201a, sg110700b and sg010803, and Gazette 1302, 1304, 1401 and 1402-library.

### (c) Should the Agency have its own record? Yes.

Recommendation: create an organization record for the Redevelopment Agency of the City of Santa Clarita (orgType government, orgLevel valley), parent The City of Santa Clarita (#394).
- Founded 1989 (City booklet; exact date NEEDS_VERIFICATION). Dissolved 1 February 2012 (ABX1 26; City reports).
- Body: its board was the City Council; the Newhall Redevelopment Project Area (1997); the failed 1994 plan and the 1995 appellate case; the 2008 bonds; and the City as Successor Agency and Housing Successor from 1 February 2012.

Why:
1. It was a separate public body in law (Community Redevelopment Law, Health and Safety Code). It had its own board actions, budget, bonds (the 2008 tax allocation bonds were its debt, not the City's), property (the Avery block) and eminent domain power. The City's own documents treat it as a separate party: "Redevelopment Agency Board adopt...", "the City, acting as Successor Agency."
2. The archive already refers to it as a body. The committee record says it advised the Agency. Darcy's biography lists it as an office she held (1991 to 1995). The Canyon Theatre Guild got one of its first grants. Without a record these have nothing to link to.
3. It has a clear start and end, and a successor the record can name.

Do not make a separate Successor Agency record. The successor was the City acting in that capacity (Health and Safety Code 34173), which a line in the Agency's body covers. The Oversight Board is too minor for a record. Moving the committee's parent from the City to the Agency is Nathan's call. The sources split: the Signal (2002) says the Council appointed members, while Ellis (2005) says the Agency board did. Since they were the same five people, keeping it under the City and adding a body sentence about advising the Agency loses nothing.

---

## Open questions for Nathan

1. AADUSD: which high school district Acton and Agua Dulce left in 1993 (Antelope Valley Union High, or Hart). No source found.
2. AADUSD: the County Committee resolution approving trustee areas (2025). The County layer implies approval.
3. Agency: the 1989 creating ordinance and date, the 1997 Newhall plan ordinance number, and the outcome of *Castaic Lake Water Agency v. City of Santa Clarita* (1995) from the opinion text.
4. Committee: the City action dissolving it (about 1 March 2012), and how record #16290 should give the terms: "without term limits" (Signal, 2002) or four-year terms (Ellis, 2005).
