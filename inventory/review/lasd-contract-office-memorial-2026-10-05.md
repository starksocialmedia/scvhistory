# The Sheriff's contract, the office of Sheriff, and officers killed in the valley

Claude, research for Nathan, 5 October 2026. Read-only: nothing was written to the database, no templates were touched, nothing was committed, nothing was pushed.

**Where the sources are.** Every page relied on is saved in `inventory/news/lasd-2026-10-05/`. Copies of mirror pages are in its `mirror/` folder. `manifest.json` gives the URL or mirror path, the read date (2026-10-05), sha256, size and a one-line summary for each file (117 entries).

**Archive queries.** Three read-only queries are in `inventory/review/lasd-contract-office-memorial-2026-10-05-queries/` (q1 to q3, each with its `.out`), run with `ddev craft exec "eval(file_get_contents(...))"`. The mirror search script is there too (`mirror_search.py`: Python, bytes decoded cp1252, tags stripped, case-insensitive regex over all 33,705 .htm/.html/.txt files on /Volumes/Reggie/SCVHistory/scvhistory.com).

**Citation forms.** `mirror:<path>` is a page on the Reggie mirror. "Boyer ch. N" is Carl Boyer, *Santa Clarita: The Formative Years* (2015), the chapter PDFs on the mirror (`scvhistory/boyer2015chNN.pdf`); Boyer was a member of the first council, so he is a participant's memoir, written years later. "BOS 2024" is the LASD board letter adopted by the Board of Supervisors on 25 June 2024 with the boilerplate Municipal Law Enforcement Services Agreement (file.lacounty.gov/SDSInter/bos/supdocs/192471.pdf).

**Wikipedia.** No fact below rests on Wikipedia. Two search summaries drew on it (the 2026 primary vote counts, the list of sheriffs); each was replaced by a non-Wikipedia source.

---

## 1. The contract

### When it began

| Fact | Value | Sources | Confidence |
|---|---|---|---|
| The plan before cityhood | The City Feasibility Committee's press kit of 14 October 1985: the city "would be a 'general law' city and would contract for both law enforcement and fire protection with the L.A. County Sheriff and Fire Departments. More than 35 other cities in the County now have that arrangement." Boyer: the committee chose to stay with the County for fire and sheriff, and the Sheriff would replace the CHP for traffic patrol | mirror:scvhistory/lw8501.htm; Boyer ch. 5 | certain (the plan) |
| Incorporation | 15 December 1987 | City newsletter, Fall/Winter 2022 (santaclarita.gov doc 21462); archive #394 | certain |
| The contract at incorporation | LASD, 2012: "At the time of incorporation, the Los Angeles County Sheriff's Department entered into an agreement with the city of Santa Clarita to provide law enforcement services". The City's FY1987-88 CAFR letter (24 Jan 1989): "The City is the largest contract City in California" | LASD release via Nixle, 28 Jun 2012; City CAFR FY1988 letter (saved 4 Oct in `inventory/news/city-managers-2026-10-04/`) | certain that service ran from incorporation |
| The first weeks | The council did **not** sign a Sheriff contract at its first meetings. Boyer: "because we did not sign a contract with the Sheriff's Department the deputies could only enforce ordinances that had been in effect under county government", which is why the council's second act was an ordinance making all county laws city laws. The Signal (Sharon Hormell, quoted by Boyer) reported that the council "side-stepped ... whether it should sign county service contracts, but voted that county services be continued". A Signal editorial quoted by Boyer: "A stack of contracts for county services remains unsigned while the council decides if it will pay the estimated $2.7 million cost of the services". The dispute was over a clause the City wanted to reserve its rights on payment | Boyer ch. 8 | the account is Boyer's, quoting the Signal of December 1987; the Signal originals are not in the mirror |
| **Date the first agreement was signed** | **Not found.** Most likely early 1988 (services continued from 15 Dec 1987 under the "continue county services" vote), but no source gives the date | see "What was searched" below | NEEDS_VERIFICATION |
| What changed on the street | The CHP withdrew from city streets at incorporation ("they refused to submit a bid for patrolling the streets"), and the Sheriff's estimate included deputies for traffic patrol. A Signal obituary of 2007: "Before cityhood brought contracts with the Los Angeles County Sheriff's Department, the CHP's jurisdiction stretched throughout the entire Santa Clarita Valley." The Citizen, 18 Sep 1988: drug arrests up 30 percent "since Santa Clarita became a city last December", because "There are more police on the streets since the city became incorporated" (Sgt. Bob Wachsmuth) | Boyer ch. 1 and 5; mirror:scvhistory/sg012307-obit.htm; mirror:scvhistory/files/citizen19880918/citizen19880918_ocr.htm | Boyer's "refused to submit a bid" is his characterization; the CHP's own reason is not documented here |
| The captain as police chief | Capt. Bob Spierer, station captain in 1988, "doubled as our police chief" | Boyer ch. 10; Citizen 18 Sep 1988 | certain for Spierer in 1988 |

### What it covers and how it works (the current agreement)

From BOS 2024, the agreement every one of the 42 contract cities signs, 1 July 2024 to 30 June 2029:

* **Authority.** County Charter sections 56½ and 56¾ and Government Code 51301 ("A board of supervisors may contract with a city ... for the performance by its appropriate officers and employees, of city functions", added 1949). Government Code 51302: the term "shall not exceed five years but may continue for periods of five years each, unless the legislative body of either local agency votes not to continue the term at a meeting more than one year before the expiration". That statute is the source of the five-year cycle.
* **Scope.** "General law enforcement services within the corporate limits of the City", of the kind the Sheriff renders under the Charter, state law and the City's municipal code, plus anything else in public safety the City asks for (section 3.7).
* **Two levels.** The five-year agreement, and an annual **Service Level Authorization (form SH-AD 575)** signed each 1 July: the units, hours and minutes the City buys. The City meets the station captain and "provide[s] direction ... regarding the method of deployment"; together they set a minimum daily staffing standard (sections 3.1 to 3.6). Knock LA's explainer (secondary) describes the same two levels and says the master agreement is negotiated by the County with the California Contract Cities Association.
* **Price.** Billing rates are set by the County Auditor-Controller and readjusted every 1 July under Board policy (section 8). The City pays monthly invoices within 60 days. The 2024 rate sheet adds a **12.5 percent liability** charge; in 2014 the bureau described the pooled liability fund as "roughly 4 percent of the contract" (KHTS via SCVNews, 22 Apr 2014). Both figures are as stated; the change between them was not traced.
* **Compliance.** Below 98 percent of contracted service in a month, the captain meets the City; below 98 percent for a quarter, a division chief does (section 3.3). The City reported 99.8 percent compliance under the 2009-2014 contract (KHTS 2014).
* **Termination.** Either side on 180 days' notice; the City within 60 days of a rate increase (section 7).
* **Units are minutes.** 2012-13: 8,253,960 deputy minutes for about $19.35 million; 2013-14: 8,361,300 minutes (KHTS 2014, quoting Capt. Rick Mouwen of the Contract Law Enforcement Bureau).
* **Beyond the base contract.** The City pays separately for a probation officer, the Juvenile Intervention Team, special events, and splits the cost of school resource deputies with the County (KHTS 2014). Boyer: the City added COBRA and SANE units when County coverage was cut, and by about 2004 "the city provides fifteen traffic cars in the core area while the county provides two for a much larger area" (Boyer ch. 24).

**The shared station.** One station serves both the City and the unincorporated valley. LASD, 2012: 593 square miles unincorporated and 55 square miles of city; population about 98,000 unincorporated and 177,000 city. The City buys its own service units; the County funds patrol of the unincorporated area. The County Sheriff also keeps county-wide functions in the city (helicopters, search and rescue, SWAT) that every city gets (City EIR 2008 section 5.12; KHTS 2014).

### The five-year cycles and the money

| Period | Evidence | Source |
|---|---|---|
| 1987/88 to ? | First agreement; date and term not found | above |
| to June 2009 | "renewed annually and currently extends until June 2009" | City, Henry Mayo Newhall Hospital Master Plan EIR, draft Sept 2008, sec. 5.12 |
| 2009 to June 2014 | "previous five-year contract ... in June" | KHTS via SCVNews, 22 Apr 2014 |
| 2014 to 2019 | Council set to approve a new five-year contract, about $21.2 million, 22 Apr 2014 | same |
| 2019 to 2024 | Inferred from the cycle; no document read | NEEDS_VERIFICATION |
| 2024 to 2029 | Council approved on consent, 25 Jun 2024, without discussion; Board of Supervisors adopted the boilerplate the same day | The Signal, 25 Jun 2024 (Wayback); BOS 2024 |

| Fiscal year | City cost | Source |
|---|---|---|
| 2004/05 | General Law line $11,291,480; Police Services $12.4 million, 99.7 percent of public safety | City budget pages (santaclarita.gov doc 1949) |
| about 2004 | "over $12,000,000 annually" | Boyer ch. 24 |
| 2011/12 | $20,542,294 | LASD release, 28 Jun 2012 |
| 2013/14 | $18.8 million "general law contract" and about $21.2 million total expected (the article gives both) | KHTS 2014 |
| 2022/23 | $29,986,358 | The Signal, 11 Apr 2025, "per city figures" |
| 2023/24 | $33,286,658 (estimate) | same |
| 2024/25 | $34,562,597 (budget) | same |
| 2025/26, 2026/27 | Not found. The City's 2026-27 budget post gives no Sheriff figure | santaclarita.gov blog, 16 May 2026 (saved 4 Oct) |

**Rank among contract cities.** The sources disagree with time: the City's FY1988 CAFR, "largest contract City in California"; LASD 2012, "second largest in Los Angeles County"; KHTS 2014, Lancaster and Palmdale "paying slightly more"; The Signal 2025, the City's contract is "the LASD's largest". All can be true at their dates.

### Major changes since

* **New station.** Ground broken 25 July 2018 on Golden Valley Road; ribbon cutting 18 October 2021; operations moved in November 2021; about $68.9 million; built by the City and County together on City-owned land. Already sourced in `inventory/review/schools-church-sheriff-2026-10-04.md` section 4 and in record #29682. **One refinement:** LASD's 2012 release dates the opening of the Valencia station (the County Civic Center) to **8 May 1972**, "with Sheriff's Captain Edwin Coffeen officiating"; the earlier report had "May 1972" from one caption.
* **Staffing.** June 2024: Capt. Justin Diez said the station was at about 65 to 70 percent of its traditional staffing and that the contract was being met with mandatory overtime. Lancaster's class action of April 2024, on behalf of all contract cities, alleges LASD bills staffed positions it fills with overtime. Santa Clarita, as a class member, did not opt out as of June 2024 (The Signal, 25 Jun 2024). April 2025: the council sent a letter to the Board of Supervisors asking for recruitment and retention investment (The Signal, 11 Apr 2025).
* **The program itself** dates from 1954, when Lakewood incorporated as the first contract city (KHTS 2014 quoting the bureau; lakewoodca.gov, read but not saved because the site blocked a direct download).

### What was searched for the first contract date (for source-searches.json)

* Mirror (5 Oct 2026, Python over cp1252 text of 33,705 pages): `contract(s|ed|ing)? (with|for) (the )?(L.A. County |Los Angeles County )?(Sheriff|law enforcement|police)`, `(Sheriff|law enforcement|police)[^.]{0,80}contract`, `contract cit(y|ies)`, `Lakewood Plan`, `own police (department|force)`; and every page whose name holds 1987 or 1988 for "sheriff" near contract/city/council. Hits: lw8501, sg012307-obit, sg011506-manzer, the Citizen 1988, three "Contract Cities Association" mentions. None gives the date.
* Boyer's book: all 31 chapter and appendix PDFs, text extracted with pdftotext, searched for sheriff, contract, traffic, CHP.
* City Council minutes of 14 January 1988 (mirror: scvhistory/cc011488minutes.pdf): no Sheriff item.
* The City's FY1988 CAFR letter (saved 4 Oct): no contract date.
* Web: santaclarita.gov documents, LA County BOS documents, LASD pages, LA Times and Signal search queries.
* Where it would be: the City Clerk's minutes for December 1987 to spring 1988, the Board of Supervisors' Statement of Proceedings for the same months, or the Signal of those months (the Signal photo archive went to SCVHS in 2019).

---

## 2. The office of Sheriff

**Plainly.** Los Angeles County has one Sheriff, elected by the voters of the whole county. Everyone else in the Department, from the station captain who acts as Santa Clarita's chief of police to the patrol deputy, serves under the Sheriff's authority as an employee of the County. The City of Santa Clarita elects no police official and employs no police; it buys the Sheriff's services.

| Point | Text | Source |
|---|---|---|
| Elected by the Constitution | "The Legislature shall provide for county powers, an elected county sheriff, an elected district attorney, an elected assessor, and an elected governing body in each county." | Cal. Const. art. XI, sec. 1(b) |
| A county officer | Gov. Code 24000: "The officers of a county are: ... (b) A sheriff." | leginfo |
| Elected, and cannot be made appointive | Gov. Code 24009(a) lists the sheriff among officers "to be elected by the people"; 24009(b) lets counties make offices appointive "except for those officers named in subdivision (b) of Section 1 of Article XI", which includes the sheriff | leginfo |
| Duties | Gov. Code 26600: "The sheriff shall preserve peace"; 26601: arrest and take before a magistrate those who commit public offenses; 26602: "prevent and suppress any affrays, breaches of the peace, riots, and insurrections ... and investigate public offenses" | leginfo |
| Term | Four years (Daily Bruin, 2 Jun 2026). LA Almanac (secondary): one-year terms to 1882, two-year to 1894, four-year since | Daily Bruin; laalmanac.com |
| When elected | Most counties now elect the sheriff in presidential years (Elections Code 1300, AB 759, effective 2023; a sheriff elected in 2022 serves six years). **Los Angeles County is exempt**: its Charter requires the sheriff (and assessor) to be elected in gubernatorial years, and AB 759 spares charter counties that set the timing before 1 January 2021 | Elec. Code 1300(c), (d); Assembly Elections Committee analysis of AB 759, 29 Apr 2021 |
| Taking office | Gov. Code 24200 says first Monday after 1 January, but LASD: Luna "sworn into office on December 3, 2022" and, "in line with the County Charter", took command on 5 December 2022. So the Charter governs LA's date | leginfo; LASD news, 8 Dec 2022 |

**The current Sheriff.** **Robert G. Luna**, 34th Sheriff: elected 8 November 2022 (defeating the incumbent, Alex Villanueva, with about 61 percent), sworn in 3 December 2022, in command from 5 December 2022 (LASD, 8 Dec 2022; NBC LA and LA Magazine, June 2026). Verified. **Surprise: the office is on the ballot now.** In the 2 June 2026 primary Luna had 44.18 percent and Villanueva 21.71 percent (Registrar figures reported by NBC LA and LA Magazine; the final certified count was not read), so the two meet again in the general election on **3 November 2026**, four weeks from today. Living people: public role only.

**Holders of the office, 1898 to now.** From Leon Worden's list (mirror:scvhistory/lacountysheriffs.htm; his note: "Up to Baca, most dates are election dates"), checked against the LA Almanac (secondary) and LASD's own releases for the recent four. The Almanac's years run one later at the start of most terms (taking office, not election).

| Sheriff | Years (Worden) | Notes | Archive |
|---|---|---|---|
| William A. Hammel | Nov 1898 to Nov 1902; Nov 1906 to Nov 1914 | led the 1909 capture of De Moranville's killer (see 3) | no record |
| Will A. White | Nov 1902 to Nov 1906 | | no record |
| John C. Cline | Nov 1914 to Mar 1921 | removed by the Board of Supervisors in 1921 (Almanac) | no record |
| William I. Traeger | Mar 1921 to Dec 1932 | appointed to fill the term | no record |
| Eugene W. Biscailuz | Dec 1932 to Nov 1958 | appointed, then elected; "27th" sheriff (LW3349); the Biscailuz family house of about 1908 is at 24427 Chestnut, Newhall (City historic resources 1991, mirror:scvhistory/city-historic-resources-91.htm) | photograph #5191 |
| Peter J. Pitchess | Nov 1958 to Jun 1982 | | photographs #2959 and #3789 (same title: a duplicate check is worth doing) |
| Sherman Block | Jun 1982 to Oct 1998 | appointed; died in office days before the 1998 election (Almanac) | photograph #4013; article #12246 |
| Leroy D. "Lee" Baca | Nov 1998 to 30 Jan 2014 | | no record (SD1401 not imported) |
| John L. Scott | 30 Jan to 1 Dec 2014 | interim, appointed | photograph #4413 |
| Jim McDonnell | 1 Dec 2014 to Dec 2018 | 32nd (LASD 2014, mirror:sd1403) | no record (SD1403 not imported) |
| Alex Villanueva | 3 Dec 2018 to Dec 2022 | 33rd (LASD 2019, mirror:sd1902) | no record (SD1902 not imported) |
| Robert G. Luna | 3 Dec 2022 to now | 34th (LASD 2022) | no record |

Earlier: William R. "Billy" Rowland, 1872 to 1876 and 1879 to 1882, captor of Vasquez (photograph #4449).

**What the archive holds.** No person record for any sheriff (q2: the 259 person records were checked by name). Role record **#18441 "Sheriff"** (Wikidata Q578478) exists with nothing related to it (q3). The LASD record **#29282 has an empty body.** The station #29682 is complete.

---

## 3. Officers killed: a possible memorial

### What the legacy site already has

Leon built this memorial already. About twenty law-enforcement pages on the mirror, chp-newhall-incident.htm among them, carry a **"FALLEN OFFICERS"** sidebar listing, in order: the Newhall Incident; Harnischfeger 1889; Pyle 1897; De Moranville 1909 (two pages); Brown 1924 (two); Pilcher 1925 (two); Pelino 1978; Kuredjian 2001; March 2002; the Sacramento memorial; the March interchange; Pavelka 2003; and, under "See also", Emma Benson 1919. The general index (mirror:scvhistory/general.htm, LAW ENFORCEMENT and NEWHALL C.H.P.) lists the same pages with dates. So a memorial section would carry over an existing legacy structure, as the war memorial did, not invent one.

**The archive holds almost none of it** (q1). In Craft: photographs #4257 (Kuredjian and March on the Sacramento memorial, LW2613a), #4259 (the memorial, LW2613b), #4563 (March interchange, LW2833), and two Newhall Incident photographs, #3977 (LW2524) and #4553 (LW2825). Most other name hits in q1 come from the sidebar text stored inside those records. Not imported: the LASD history articles on De Moranville, Brown and Pilcher (by Lt. John Stanley, 2013 and 2014), the 1897 and 1909 news reports, the Pelino, Kuredjian, Pavelka and Arce pages, the grave-marker photographs AS2401 and AS2501, SD1901, and the Newhall Incident set (chp-newhall-incident, sg4701, du1970, vnvgs040770a, AL1970 to AL1977, pollack0309).

### Candidates tied to the valley

| # | Name | Agency | Date (end of watch) | Place | Circumstances | Valley tie | Sources |
|---|---|---|---|---|---|---|---|
| 1 | Constable **McCoy Pyle** | Fillmore constable, Ventura County | 24 Apr 1897 | "the Castac switch, four miles from Saugus" (Castaic Junction) | shot in the head by one of two robbery suspects he had in custody, before dawn | killed in the valley; Leon calls him "of the Pyle Ranch at Castaic Junction" | LA Herald 25 Apr 1897 and SF Chronicle, mirror:lp_sfchronicle052597.htm. Age conflict: 29 (Leon's note) against "about 35" (Herald). The Chronicle item is headed "May 25, 1897" under an April 24 dateline: a date slip on the page. **Care:** the same note ties him to the Bowers Cave find; TATAVIAM_AUDIT.md and RL8 govern, and the location must not be carried |
| 2 | Deputy Constable **Charles A. De Moranville** | Los Angeles County Constable (Newhall Township) | 4 Jan 1909 | edge of Newhall, along the railroad | shot answering a saloon disturbance by John "Arizona Jack" Allen, who was acquitted | Newhall's deputy constable; killed in Newhall | Stanley (LASD), 31 May 2013, mirror:lasd053113demoranville.htm; LA Herald 1909, mirror:laherald010609.htm. Name added to the Memorial Wall at the LASD Training Academy, Whittier, 29 May 2013. ODMP page located (odmp.org/officer/21689), not readable by script |
| 3 | Deputy Constable **J. Edward "Ed" Brown** | LA County Constable (Saugus) | 14 Sep 1924 | Bonita Darling's ranch, Tunnel Canyon, near Newhall | gun battle with Gus Le Brun, who was killed by Constable Pilcher | stationed in Saugus | Stanley (LASD), 2013, mirror:lasd053113brown.htm; grave marker AS2401; LASD release 28 Jun 2012 (added to the SCV station memorial wall that day) |
| 4 | Constable **John S. "Jack" Pilcher** | LA County Constable (Newhall) | 4 Jun 1925 | Gage Ranch, Bouquet Canyon | a three-day rookie's pistol fell from an old holster and fired | Newhall's constable | Stanley (LASD), 4 Jun 2014, mirror:lasd060414pilcher.htm; grave marker AS2501. ODMP page located (odmp.org/officer/21999), not read |
| 5-8 | CHP Officers **Walter C. Frago**, **Roger D. Gore**, **James E. Pence Jr.**, **George M. Alleyn** ("the Newhall Incident") | California Highway Patrol, Newhall office | 5 Apr 1970, just before midnight (CHP: "The date of April 6 was originally used") | parking lot by J's Coffee Shop, I-5 at Henry Mayo Drive (now The Old Road at Magic Mountain Parkway) | shot in a 4½-minute gun battle by Jack Twining and Bobby Davis after a stop | Newhall area officers; Alleyn lived on Enderly St., Saugus; memorial wall at the Newhall CHP office, dedicated 5 Jun 1970 | CHP text on mirror:chp-newhall-incident.htm; Signal 5 Apr 2000 (sg4701); du1970. Spelling: "Twining" (du1970) against "Twinning" (CHP text, Signal). Memorial highway: "April, 2008" (Leon, du1970) against 11 Aug 2006 (search snippet from Find a Grave, not read): NEEDS_VERIFICATION |
| 9 | Deputy **Arthur E. Pelino** | LASD | 19 Mar 1978 | his resident-deputy office, Gorman | a mentally ill prisoner took his gun during booking | Gorman resident deputy, in the SCV station's area; LASD 2008 calls him "the first Santa Clarita Valley Deputy to be killed in the line of duty" | mirror:obituary_pelinoarthure.htm; mirror:sd1901.htm (SCV station, 19 Mar 2019); LASD museum, "March Officers Killed" (lasd.org PDF, saved). Age 50, 21 years, badge 1145 |
| 10 | Deputy **Hagop "Jake" Kuredjian** | LASD, SCV station (motor deputy) | 31 Aug 2001 | Brooks Circle, Stevenson Ranch | shot by James Allen Beck while backing up a federal search warrant | SCV station deputy from 1995; killed in the valley | KHTS 28 Aug 2011 (kuredjian082811); SCV station release Aug 2008 (scvtv.com); The Signal 8 Jan 2025; ACR 92 analysis 2023; mirror:lw2613a. Age 40, 17 years. Which federal agency: ATF (station 2008), U.S. Marshals (Signal 2025, citing ODMP), "federal agents" (Leon) |
| 11 | Deputy **Shayne Daniel York** | LASD, Pitchess Detention Center | shot 14 Aug 1997, died 16 Aug 1997 | a salon in Buena Park, off duty | shot by a robber who found his badge | assigned to Pitchess; a park at PDC East is named for him; I-5 from Newhall Ranch Road to Hasley Canyon Road is his memorial highway (ACR 16, Wilk, 2015) | mirror:pdc_overview_2002.htm; Senate floor analysis of ACR 16 (saved) |
| 12 | Deputy **David W. March** | LASD | 29 Apr 2002 | traffic stop, Irwindale | shot by Armando Garcia | Saugus resident, Canyon High alumnus; on Leon's sidebar | mirror:lw2613a, lw2833; Leon Worden's interview of his parents, Signal 2 May 2004 (signal/newsmaker/sg050204) |
| 13 | Officer **Matthew Pavelka** | Burbank Police | 15 Nov 2003 | near Burbank airport | shot at a traffic contact | Canyon Country resident | SCVNews 24 Jul 2012 (scvnews072412). Leon's caption has "E.O.W. 11-15-2013", a typo for 2003 |
| 14 | Officer **Clarence Wayne Dean** | LAPD (motor) | 17 Jan 1994 | the collapsed 14/5 ramp, Newhall Pass | rode off the collapsed ramp in the dark on his way to work, Northridge earthquake | killed in the valley; the 5/14 interchange is named for him | mirror:lw3049.htm. Whether he counts as line of duty (en route to work) was not checked against ODMP |

Not recommended, for Nathan's word:
* **Constable Anton Harnischfeger** (LA County Constable, 20 Mar 1889): killed in Garvanza. His tie is his son, the St. Francis Dam keeper: an inherited tie, which PROFILES.md says does not count.
* **Deputy Emma Benson** (LASD, 20 Mar 1919, Pico Rivera): Leon lists her under "See also"; no valley tie is stated.
* **Deputy Brandon Arce** (LASD, NCCF Castaic, 14 Oct 2015): killed off duty in a head-on collision on San Francisquito Canyon Road. LASD's release does not call it line of duty (mirror:obituary_brandonarce.htm).

**Conflicting "firsts".** LASD 2012 calls Ed Brown (1924) "the first law enforcement officer killed in the line of duty in Santa Clarita"; LASD 2008 calls Pelino (1978) the first SCV deputy so killed. De Moranville (1909) and Pyle (1897) were killed here earlier. Brown was "newly discovered" in 2012, and De Moranville was added to the county wall in 2013, after both statements. A memorial page should not repeat either "first".

### Deputy Jake Way

* **The street is "Deputy Jake Way", not Drive.** Dr. J. Michael McGrath Elementary is at **21501 Deputy Jake Way, Newhall** (Ed-Data). Capt. Paul Becker in 2011 listed "Deputy Jake Way, near McGrath Elementary School in Newhall" among the tributes to Kuredjian (SCVNews, 31 Aug 2011); the station's 2008 release: "A Newhall Street was also named after Jake."
* **When:** it existed by December 2001: the Old Town Newhall Gazette of that month speaks of "the existing Deputy Jake Way" in The Master's College plans (mirror:oldtownnewhall/gazette/gazette1201-masters.htm). So it was named between September and December 2001. Who named it (City Council resolution or tract map) and on what day: **not found** (searched: mirror for `Deputy Jake (Way|Drive|Dr\.)` and `Kuredjian`, all pages; web for "Deputy Jake Way" with Kuredjian, City and NSD). McGrath Elementary opened in 2003 (NSD history, mirror:scvhistory/lw2534.htm).
* Other Kuredjian memorials, with dates: station memorial rose garden (the station's 2008 release says "Friday, August 30, 2001", which cannot be right: he died on 31 August 2001, and 30 August was a Friday in **2002**, so the garden was likely dedicated on the first anniversary eve: NEEDS_VERIFICATION); monument at Stevenson Ranch Parkway and Poe Parkway, 1 Nov 2001; Jake Kuredjian Park, 26265 Pico Canyon Road, dedicated 6 Oct 2004; I-5 between the Pico-Lyons and McBean overcrossings, ACR 92 (2023), sign dedicated 8 Jan 2025; California Peace Officers Memorial, Sacramento.

### What the memorial lists publish

* **LASD.** Its memorial is the Memorial Wall at the Training Academy, Whittier (480 names from county agencies in 2013, per Stanley); LASD's museum publishes month-by-month PDFs of members killed (the March one was saved; Pelino is on it). A single LASD "Fallen Heroes" page was not found at lasd.org/fallen-heroes (404); lasd.org/memorial is a 2019 news item, not a roll.
* **SCV station.** Its own memorial garden and wall: Pelino and Kuredjian plaques (2001 or 2002); Ed Brown added 28 Jun 2012. Whether De Moranville, Pilcher, York or March are on it is not documented in what was read.
* **ODMP** (secondary). Officer pages exist for Kuredjian (15756), Pelino (10498), De Moranville (21689), Pilcher (21999) and York (14952), found through search results; the site renders by script and its text could not be read or saved, so nothing here rests on it. Leon's Pelino and Harnischfeger pages reproduce ODMP-style entries.
* **California Peace Officers' Memorial, Sacramento.** Kuredjian and March are on it (photographed by Leon, 25 May 2014). The Foundation's roll (camemorial.org) was not searched name by name.
* **Los Angeles County Peace Officers Memorial.** Not located as a separate list in this pass.

---

## Open questions for Nathan

1. **The memorial: build it, and in what form?** Leon's "FALLEN OFFICERS" sidebar is a ready legacy structure. Recommended: a section like War Memorial (its own section, not Persons), seeded with Leon's ten names plus York and Dean, each with agency, rank, end of watch, place, circumstances, home or tie, and sources. Your call on scope.
2. **Who belongs.** Officers killed in the valley, officers of valley stations killed elsewhere (York, and March, who was a resident but not a valley deputy), and valley residents killed elsewhere (Pavelka). Which of these count?
3. **Arce, Harnischfeger, Benson.** Recommend leaving out Arce (off duty, not line of duty) and Harnischfeger (inherited tie); Benson out unless a valley tie turns up.
4. **Sheriffs as person records.** None exist. Under the significance bar most sheriffs did nothing here beyond holding a county-wide office. Biscailuz has a family house in Newhall and an uncle in Pico Canyon; Rowland captured Vasquez. Recommend: Sheriff role holders listed on the LASD record, person records only for Biscailuz and Rowland if you agree.
5. **The 2026 election.** The sheriff's race is on the 3 November ballot. Record the result when certified, or leave current officeholders alone until then?
6. **Duplicate photographs** #2959 and #3789 (both "Sheriff Peter J. Pitchess, 1912-1999 (1953-1982)").

## What each piece would need to be built

* **The contract.** (a) The first agreement's date: the City Clerk's minutes for December 1987 to spring 1988, or the Board of Supervisors' Statement of Proceedings; until then, write "from incorporation, under a contract the council signed in [date unknown]". (b) Body text for #29282 (it is empty) and a paragraph on #394 and #29682 about the contract, its five-year cycle under Gov. Code 51302, the SH-AD 575 and the cost series above. (c) Optionally a document record for the 2024-2029 agreement (BOS 2024). (d) The 2019-2024 renewal and the FY2025-26 and 2026-27 amounts from the City's adopted budgets.
* **The office.** Body text on #29282 explaining the elected office (Constitution, Gov. Code 24000, 24009, 26600 to 26602; the Charter's gubernatorial-year timing); the holders list as relations on role #18441 or as a table on #29282; imports of SD1401 (Baca), SD1403 (McDonnell), SD1902 (Villanueva) and LW2705 (Scott, if not #4413) as photographs.
* **The memorial.** A schema decision (new section and fields, on the War Memorial pattern: that is a database change, so a plan for your approval first); then imports of the legacy pages listed above with their legacy URLs, bylines (Lt. John Stanley, LASD; Carol Rock, KHTS; Kristin Wilder, The Signal; Leon Worden) and photo IDs (AS2401, AS2501, SD1901, LW2613a/b, LW2833, AL1970 to AL1977, DU1970, LW2524, LW2825); a 301 table; and the open verifications: the Newhall Incident highway date, the rose-garden year, Deputy Jake Way's naming date, which federal agency Kuredjian was backing up, and Pyle's age.
