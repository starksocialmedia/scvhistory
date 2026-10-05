# Santa Clarita Christian School, Santa Clarita Baptist Church, Newhall Elementary School, SCV Sheriff's Station: sourced facts

Claude, research for Nathan, 4 October 2026. Read-only: nothing was written to the database, no templates were touched, nothing was committed.

**Where the sources are.** Every page relied on is saved in `inventory/news/schools-church-sheriff-2026-10-04/`. Mirror pages are copied into its `mirror/` folder. `manifest.json` gives the URL or mirror path, the read date (2026-10-04), sha256, size and a one-line summary for each file.

**Archive queries.** The read-only queries are in `inventory/review/schools-church-sheriff-2026-10-04-queries/` (q1 to q5, each with its `.out`). They were run with `ddev craft exec "eval(file_get_contents(...))"`.

**Citation forms used below.**

* **Mirror pages** are cited as `mirror:<file>`, a page under `/Volumes/Reggie/SCVHistory/scvhistory.com/scvhistory/`.
* **Archive entries** are cited as `#id`.

**Not available this session:**

* The Signal's site (signalscv.com) refused the search requests.
* The CDE school directory sits behind a captcha.
* The web search budget was used up, so sources were found through site searches, the Wayback CDX index and the mirror.

---

## 1. Santa Clarita Christian School

### Facts ready for a record

| Fact | Value | Sources | Confidence |
|---|---|---|---|
| Name | Santa Clarita Christian School (SCCS) | sccs.org; NCES PSS | certain |
| Type | Private K-12 Christian school, affiliation Baptist | NCES PSS 2023-24 (KG-12, "Affiliation: Baptist", ACSI member); sccs.org | certain |
| Parent body | A ministry of Santa Clarita Baptist Church | sccs.org ("founded in 1982 as a ministry of Santa Clarita Baptist Church"); scbc.org/sccs ("founded as a ministry of SCBC") | high, but both are the same organization's own statements |
| Founded | 1982; the church gives **September 1982** | sccs.org (1982); scbc.org/sccs ("Founded in September 1982") | attribute. Both sources are the school and its church. No independent second source was found: NCES PSS gives no founding year, and the Signal could not be searched. Record the year 1982 with the month from the church, confidence "likely" |
| Address | 27249 Luther Drive, Santa Clarita, CA 91351 (Canyon Country) | NCES PSS 2023-24 ("27249 Luther Dr, Santa Clarita, CA 91351-3733"); sccs.org footer; scbc.org | certain (independent federal source) |
| Enrollment | 552 students, 33.5 FTE teachers (2023-24) | NCES PSS | certain for that year |
| First year | 110 students, 12 faculty, K-12 | sccs.org only | attribute to the school |
| Graduates | "over 1,000" | sccs.org only | attribute |
| Students now | "more than 500 students annually" | sccs.org; consistent with NCES's 552 | attribute, or use the NCES figure |
| Lost Canyon campus | "development of our Lost Canyon campus, with the opportunity to serve more than 300 additional students" | sccs.org only | attribute. NEEDS_VERIFICATION: no City planning record found |
| Harland Working | "a deacon of Santa Clarita Baptist Church and architect of the original library and office building"; wife Bernice; both deceased | sccs.org only | attribute. Passing mention: body text, not a person record |

### Open points

1. **Possible predecessor school (NEEDS_VERIFICATION).** The church's 2016 history page (Wayback, scbc.cc/our-history) says: "In 1972, First Baptist Church founded Canyon Country Christian School." It also says Bruce H. Anderson continued for a year after the May 1981 merger "as his associate and the school administrator", which implies a school already existed in 1981-82. Neither the school nor the church says whether SCCS (1982) continued Canyon Country Christian School or replaced it. The record should not claim continuity until a Signal report from 1981 or 1982 settles it.
2. **Graduates in the archive.** The archive already holds an SCCS graduate. Rudy Alexander Acosta (#526, war memorial; education link #21799) lists Santa Clarita Christian School as his high school, per mirror:warmemorial/terror_rudyacosta.htm.

### Archive holdings

* **#21783 Santa Clarita Christian School**, organizations, orgType `school`, live. It is an empty shell: no footnotes, no address, no dates. Provenance: `import_wm_high_schools.php`, 29 September 2026, created because a war memorial record named the school.
* **#526** Rudy Alexander Acosta (warMemorials) and **#21799** (educations) mention the school.
* No entry mentions "SCCS", "Santa Clarita Baptist", "Temple Baptist" or "First Baptist Church of Saugus".

---

## 2. Santa Clarita Baptist Church

### Facts ready for a record

| Fact | Value | Sources | Confidence |
|---|---|---|---|
| Name | Santa Clarita Baptist Church (SCBC) | scbc.org | certain |
| Address | 27249 Luther Drive, Santa Clarita, CA 91351; "the heart of Canyon Country". Same campus as SCCS | scbc.org home; the church's 2016 history ("the church at the end of Luther Drive"); NCES PSS gives the same address for SCCS | certain |
| Formed | By merger of First Baptist Church of Saugus and Temple Baptist Church of Canyon Country. The merged congregations first met **31 May 1981** under the new name | church's own history (Wayback, 6 Oct 2016, scbc.cc/our-history); sccs.org gives the year 1981 | the day rests on the church alone: attribute. Year 1981 from two pages of the same institution |
| First Baptist Church of Saugus | Began **19 November 1962** under Amos Clemmons, meeting in his garage. Therman Fuller pastor from 1966. Construction began in **1968** on "the current church's 2 1/2 acre site", which became the merged church's site | church's 2016 history | attribute. NEEDS_VERIFICATION for a second source |
| Temple Baptist Church | First met **11 January 1976** in the "Glass Bottle Blower Association Building", under Pastor Richard Willoughby. "Only one mile away" from First Baptist | church's 2016 history | attribute. NEEDS_VERIFICATION |
| Pastors after the merger | Don Sewell, senior pastor (1981); Pete Mothershead, senior pastor from 19 May 1985 to May 2007; Scott Basolo, July 2008 to August 2014; Vaughn Park from May 2015 | church's 2016 history | attribute. Living people: public role only. Current senior pastor not confirmed (NEEDS_VERIFICATION) |

### Notes

1. **"First Baptist Church of Saugus" and Canyon Country.** The name says Saugus, but the 1968 site is the Luther Drive campus in Canyon Country. "Saugus" was the area's name before "Canyon Country" came into use. This is not a conflict, but the record should say so in plain words.
2. **Look-alike sites.** www.scbchurch.com is a different church (Southcenter Community Baptist Church, Tukwila, Washington). The pages fetched from it were deleted and are not cited.
3. **Mirror.** The mirror has no page on the church, its predecessors or the merger. Its only mention is an obituary (mirror:obituary_donaldverrilli.htm, 2013) naming SCBC as the deceased's church, which is not a useful source. Leon Worden's pages do not cover it.
4. **Signal.** The church's 2016 page is the only source for the dates. A Signal search for May 1981 (merger) and November 1962 (founding) would give the second source.

### Archive holdings

None. No entry's title or body mentions the church or either predecessor.

---

## 3. Newhall Elementary School (Newhall School District)

### 3a. Wikipedia's history, claim by claim

Wikipedia's "History" section has no citations. Almost all of it is a compressed retelling of Jerry Reynolds and Leon Worden. Contemporary records contradict it in four places.

| # | Wikipedia's claim | Verdict | Sources |
|---|---|---|---|
| 1 | The school opened in **1876** near the current Saugus Cafe | **No source; conflicts.** The town of Newhall was founded in 1876 at Bouquet Junction, near Magic Mountain Parkway and Bouquet Canyon / Railroad Avenue, which is the Saugus Cafe area. But the **district was formed 10 May 1877** on the petition of J.F. Powell and 47 others. Worden: "We don't know where classes were held." The 1877 teacher was Kate A. Caystile. No source puts a school there in 1876, and the infobox's "Established 1876" is wrong | Brunner 1940 (district records); Worden, History of Newhall School (citing the County's bond notices, e.g. The Signal, 14 Feb 1941); Worden timeline |
| 2 | Named for Henry Mayo Newhall, as is the town | **Holds for the town.** The school and district take the town's name | Worden |
| 3 | In 1878 the school and the town moved two miles south | **Partly holds.** The town moved two miles south in January to February 1878 (completed 16 Feb 1878). The 1878-79 classes did not go with it: they were held in a corner of Addi Lyon's bunkhouse on the Sanford Lyon ranch | Worden essay and timeline; Brunner ("a building at the Sanford Lyon home ... now 18763 U.S. Highway No. 99"); Reynolds ch. 49 (bunk house on Lyon land "below Pico Canyon, west of present-day Interstate 5") |
| 4 | An oil boom brought the families of **53 students**, and the school was placed where the Valencia Marketplace (Target) stands | **Garbled.** The 53 is the County's count of children aged 5 to 17 in the district for 1879-80 (13 attended, average daily attendance 7), not 53 pupils. Worden does say oil drew families. The Valencia Marketplace location is Worden's estimate for the 1878-79 bunkhouse classroom ("roughly where the Valencia Marketplace stands today"), not a schoolhouse and not the town. Reynolds's "west of I-5" is consistent with it. Not independently verified; attribute to Worden | Brunner 1940; Worden essay; Reynolds ch. 49 |
| 5 | **1879**: a two-storey wooden schoolhouse at 9th and Walnut; second storey used for Sunday school; burned **1890** | **Holds.** Location is the northeast corner of 9th and Walnut. Brunner says "believed to have been in 1879". Judge Powell financed it at $3,500 (Worden, Reynolds ch. 36). Upstairs was a hall; Sunday school was held there in the 1880s (Worden; HS0731). The fire was in May 1890, started by weed burning (Brunner). The Los Angeles Herald of 4 June 1890 reports the school "burned down at that place a few days ago" | Brunner 1940; Reynolds ch. 36, 47; Worden; mirror:lp_laherald060490school.htm (contemporary) |
| 6 | Rebuilt on the same site in the 1890s | **Holds.** Rebuilt 1890 near the first, again two storeys, largely funded by H.C. Needham. Classes met at 719 Spruce Street meanwhile. Second room added 1892 | Brunner; Worden; Reynolds ch. 47 |
| 7 | Older pupils rode horseback to San Fernando High after it was built in **1896** | **Holds on Worden alone.** Brunner says only that the ninth grade ended about 1898-99. Worden: after 1896 pupils could continue at San Fernando High; after 1899 ninth graders went there. "Horseback" is Worden's "horseback or bicycle" | Worden |
| 8 | **1914** fire; new building at the **northwest corner of Lyons and Newhall Avenues** | **Conflicts.** Reynolds (ch. 71) gives the 1914 fire, and Worden's photo captions repeat it (HS0731). Brunner 1940 says there was no fire: the site was too small, and a new one was taken at the NW corner of Newhall Avenue and Pico Road (Pico Road = 10th Street = Lyons Avenue) in **1911**. Worden's own essay doubts the fire: "we've never encountered a contemporary news report about any fire." The location holds (Brunner; Reynolds "near the northwest corner"; Worden "10th Street ... just east of Kansas Street"; the 1926 sale ads sell "Newhall Ave. lots cut from old school grounds"). Record the date as 1911 or 1914, unresolved, and the fire as unconfirmed | Brunner; Reynolds ch. 71; Worden essay; mirror:tlp_signal082025.htm; archive #4473 |
| 9 | Older students rode in a converted automobile to San Fernando High; a bus was bought in **1932** | **Holds, with a nuance.** Worden: Newhall merchant S.D. Dill's converted automobile carried high schoolers until 1932, "when he purchased a new bus". Brunner (elementary bus system): a 31-seat Chevrolet bus was acquired in 1932 "with the more rigid State law requirements". Two accounts of the same year, for different riders | Worden; Brunner bus history; AP0729 (Dill's bus, 1918) |
| 10 | A new school was built in **1928** at 11th and Walnut | **Contradicted by contemporary reports. The fourth school opened in September 1925.** (a) The Newhall Signal of 20 Aug 1925 calls patrons to inspect "the new school house" with its new auditorium seats. (b) The Signal of 3 Sept 1925: school opens 14 Sept 1925, and "the pleasing part of school opening will be the new building". (c) The old school property was sold to A.B. Perkins for $4,910 (Signal, 12 Nov 1925) and split into three houses (Dec 1925 to Feb 1926). (d) The Signal of 17 Feb 1939: "erected in 1925", wings added 1930, architect Arthur W. Angel. The 1928 date comes from Reynolds ch. 71 and appears in Worden's essay and the GR0221 and HS0731 captions. Brunner's "erected in 1926 and 1928" probably counts later building work. Worden's own later caption (#4473, 2015) gives "about 1911 to 1925". Record **1925** | mirror:tlp_signal082025b.htm, tlp_signal082025.htm, tlp_signal021739pg3.htm (all contemporary); archive #4473 |
| 11 | Fire again in **1939**; rebuilt on the same site; reopened **1940**; the current location | **Holds, with exact dates.** Fire early on **14 February 1939**; all but the north (left) wing destroyed; loss estimated at $60,000. New building first occupied **10 May 1940** at 1101 Walnut Street (old numbering). Today's address is 24607 (North) Walnut Street. A WPA auditorium was finished September 1941 (Worden) | LA Times 15 Feb 1939 and AP (contemporary); Brunner 1940; Worden; NSD site; NCES |
| 12 | Older pupils travelled until **1945** when Hart High opened | **Wrong year** (outside this brief, noted for completeness). The district was approved 13 Jan 1945. Hart High's first students were Newhall's ninth-grade class of 1945-46, who became Hart students in 1946-47 and graduated in 1949. Seventh and eighth grades moved over in 1948 | Worden |

**Gaps.** Three Wikipedia claims have no source beyond Wikipedia: the 1876 opening, the Saugus Cafe site of a school, and the "oil boom brought 53 students" sentence. Everything else traces to Reynolds, Worden or Brunner. Where a contemporary report exists, it settles the 1890 fire (holds), the 1925 move (not 1928) and the 1939 fire (holds).

**The district's own history.**

* The current Newhall School District and Newhall Elementary sites (newhallschooldistrict.com, nh.newhallschooldistrict.com) carry no history page.
* The old newhall.k12.ca.us site's `newhall/search/history.htm` (Wayback 2000 to 2007) is a list of links, not a history.

**A.B. Perkins.**

* *The Story of Our Valley* has nothing on the Newhall School beyond the Felton note.
* Perkins himself bought the third school site in 1925 (Signal, 12 Nov 1925). That is a fact about the school, not a Perkins history claim.

**Internal conflict on the mirror.** Several Saugus captions say "Newhall School District (est. 1879)": hb5601, lp_laherald100309, lw2043 and ox1001. They conflict with Worden's own 1877 (essay, timeline, Brunner). 1879 is the first schoolhouse, not the district. These captions should be listed as live errors.

### 3b. Is it "the oldest school in the valley"?

**No.** What does hold:

* It is the oldest school of the Newhall School District.
* It is the district's first school; the district is dated 10 May 1877.
* It is Newhall's oldest school.

School district founding dates in the region:

| District | Formed | Source |
|---|---|---|
| Elizabeth Lake | 12 Aug 1871 | Worden timeline (outside the valley proper) |
| **Sulphur Springs** | **16 Sept 1872** | Worden timeline; Reynolds ch. 33 ("September, 1872 ... second-oldest school district in Los Angeles County"); LW9801 ("the Santa Clarita Valley's oldest school district, Sulphur Springs, was founded in 1872"); The Signal, 26 Apr 1998; City of Santa Clarita General Plan 1991 and 2011 staff report |
| Newhall | 10 May 1877 | Brunner; Worden |
| Mint Canyon | 12 Feb 1879 | Worden timeline |
| Felton (Mentryville) | Oct 1885; disbanded 1932, absorbed by Newhall 1933 | Reynolds via AP0123; Worden timeline; Westcott page; Perkins notes |
| Castaic | 25 Mar 1889 | Reynolds 1992:80 via laherald18891130csd |
| Saugus | 12 Nov 1908 | Worden timeline |

Sulphur Springs Elementary also still operates, on the site Col. T.F. Mitchell gave by 1886, so "oldest still-operating school" fails as well. The safe wording for the record is "the Newhall School District's first and oldest school".

### 3c. The district's ten schools

Wikipedia's founding years, checked against the NCES directory and the district's own pages.

* **NCES dates.** NCES CCD only begins in 1987-88, so it cannot date the older schools. For later schools its `school_status` code is decisive: 3 means new that year, 7 means future. Years below are school years.
* **Addresses.** Each school's own site (`<code>.newhallschooldistrict.com/about-us`, saved) and the 2023-24 NCES directory.

| School | Address (school's own site) | Wikipedia | Sourced opening | Verdict |
|---|---|---|---|---|
| Newhall Elementary | 24607 North Walnut Street, Newhall 91321 | 1879 | First district school; district formed 1877; first schoolhouse 1879; present site since Sept 1925; present building occupied 10 May 1940 | the table's 1879 holds for the first schoolhouse. Contradicts Wikipedia's own text (1876) |
| Peachland Avenue Elementary | 24800 Peachland Avenue, Newhall 91321 | 1959 | 1959 (Worden: "the elementary district's second campus in 1959") | holds on Worden only. NCES floor 1987 |
| Wiley Canyon Elementary | 24240 West La Glorita Circle, Newhall 91321 | 1966 | 1966 (Worden) | holds on Worden only |
| Old Orchard Elementary | 25141 North Avenida Rondel, Valencia 91355 | 1969 | 1969-70 school year (Worden) | holds |
| Meadows Elementary | 25577 North Fedala Road, Valencia 91355 | 1975 | **1976** (Worden) | **conflict** (1975 vs 1976). NEEDS_VERIFICATION: no district source |
| Valencia Valley Elementary | 23601 Carrizo Drive, Valencia 91355 | blank | **1988-89**: NCES 1988 lists "Valencia Valley School" as status 3 (new). Worden: 1988 | settled by two sources |
| Stevenson Ranch Elementary | 25820 North Carroll Lane, Stevenson Ranch 91381 | 1995 | **1995-96**: NCES 1995 status 3. The school's own 2005 page: "originally built in 1995". Worden 1997 essay: 1995 | holds |
| Dr. J. Michael McGrath Elementary | 21501 Deputy Jake **Drive**, Newhall 91321 (its own site; Wikipedia agrees). NCES writes "Deputy Jake Way" | 2003 | **2003-04**: NCES 2003 status 3 | holds. The street suffix differs between NCES and the school; use the school's |
| Pico Canyon Elementary | 25255 Pico Canyon Road, Stevenson Ranch 91381 | blank | **2003-04**: NCES 2003 status 3 | settled from NCES. Second source NEEDS_VERIFICATION |
| Oak Hills Elementary | 26730 Old Rock Road, Valencia 91381 (school site; Wikipedia says Stevenson Ranch) | 2005 | **2005-06**: NCES 2004 lists it as status 7 (future, no enrolment). NCES 2005 lists it as status 3 (new, 413 pupils) | holds. The repo's `school-directory.json` "opened 2004" is the first listing, not the opening. Its `opened` years for Valencia Valley (1988), Stevenson Ranch (1995), McGrath and Pico Canyon (2003) are right; for the six older schools 1987 is only the NCES floor |

**Archive holdings for the ten.** Only **one** of the ten has a record:

* **#15958 Newhall Elementary School**, organizations, orgType `school`, schoolLevel `elementary`, K-6, parent #21590. It carries NCES ID 062718004095, CDS 19648326020796 and NCES enrolment 1986-87 to 2023-24. It has no address, no founding date and no history.
* The other nine (McGrath, Meadows, Oak Hills, Old Orchard, Peachland Avenue, Pico Canyon, Stevenson Ranch, Valencia Valley, Wiley Canyon) have no organization record. They exist only as rows in `templates/_data/school-directory.json`.

The district is **#21590 Newhall School District**: orgType school, schoolLevel district, alias "Newhall Elementary School District". It has no founding date; record 10 May 1877.

**What the archive holds on Newhall School.** Queries q1 and q4 found 29 entries mentioning "Newhall Elementary" and 174 mentioning "Newhall School". Most are board elections, candidacies and office holdings. The items about the school itself:

* **Photographs:**
  * **#3153** Newhall town view showing the first school, ca. 1879-1888.
  * **#5485** Second Newhall School postcard, about 1909 (LW3640).
  * **#4473 and #4475** 24514 Kansas Street, the ex-site of the third school's centre section (LW2763a). The caption says the school stood on Lyons "from about 1911 to 1925".
  * **#4939** auditorium as warehouse, 1996 (LW3118).
  * **#4741** auditorium refurbishment, 2015-16 (LW2961).
  * **#5517 and #5519** class photos from 1981 and 1984.
  * **#4015** Marc Winger, superintendent 1997-2015.
* **Articles:**
  * **#2097, #2119, #2123, #2149**: Reynolds chapters 36, 47, 49 and 62.
  * **#12658** "Simple dreams of good times at Newhall auditorium" (Rasmussen 1997).
  * **#12686** "Hart, Newhall districts pick new leaders".
* **Place:** **#2540 Felton School**.

**Not yet in the archive.** About 60 of the mirror's Newhall School pages, matched by legacy key (q4.out):

* Worden's "History of Newhall School" (newhallschool.htm).
* All three Brunner 1940 pages.
* The 1890, 1925 and 1939 news pages.
* AP0113, AP0124, HS0731, AP1705, AP1710, AP1011, AP1708, RL0100, GR0220, GR0221, GR0222, KU2501, KU2502, NS2801, NS3001, the PC-series class photos, the LW2638 razing gallery and NS1501.

These are the strongest sources for the record and should come in with it.

---

## 4. Santa Clarita Valley Sheriff's Station (Los Angeles County Sheriff's Department)

### Facts ready for a record

| Fact | Value | Sources | Confidence |
|---|---|---|---|
| Name | Santa Clarita Valley Sheriff's Station (LASD "Santa Clarita Valley Station") | lasd.org | certain |
| Current address | **26201 Golden Valley Road, Santa Clarita, CA 91350** | lasd.org station page (read 2026-10-04); SCVNews / LASD release, 26 Aug 2026 (centennial held there) | certain |
| New station: ground broken | **25 July 2018**, on Golden Valley Road between Centre Pointe Parkway and Robert C. Lee Parkway. Built by the City and the County together; 46,000 sq ft, with a 4,000 sq ft vehicle maintenance building, heliport, 9-1-1 dispatch and jail | City of Santa Clarita release (mirror:sc1806.htm) | certain |
| New station: opened | Ribbon cutting **18 October 2021**. The station was not yet open to the public that day; operations were expected to move "in the next month or so". LASD's own station page switched its address from 23740 Magic Mountain Parkway to 26201 Golden Valley Road between Wayback captures of **8 Nov 2021** and **19 Nov 2021**. The City: "opened in 2021" | SCVNews / LASD release 18 Oct 2021; Wayback captures of lasd.org (saved); City of Santa Clarita, 24 Aug 2022 | ribbon cutting certain. Operational move: **November 2021**, between 8 and 19 Nov, by inference from LASD's page. The exact day is NEEDS_VERIFICATION (a Signal report would settle it) |
| Cost | About $68.9 million ($68,886,449) excluding land | LASD release, 18 Oct 2021 | attribute |
| Previous station | **23740 Magic Mountain Parkway, Valencia**, in the County Civic Center at the northwest corner of Valencia Boulevard and Magic Mountain Parkway; 25,100 sq ft; **opened 1972** (May 1972 per LW3347). Groundbreaking for the Civic Center was 11 Feb 1970. Served until November 2021 | mirror:sc1806.htm (City, 2018); mirror:lw3347.htm; mirror:lw7001.htm; Worden, The Signal, 8 Nov 1996; SCVNews 2016 and 2021 | certain for 1972 and the address. Month (May) from one caption |
| Original station | **Substation No. 6**, opened **26 August 1926** by Deputy J.E.B. Stewart. The Board of Supervisors created it in July 1926. It was in the former Albert Swall residence at the northeast corner of Spruce (later San Fernando Road, now Main Street) and 6th Street, with a house at 6th and Railroad Avenue used while that was readied. Cells were added in 1928. It was used until the 1972 move; afterwards it housed The Signal (to 1986) and later the Canyon Theatre Guild office | Worden, The Signal, 8 Nov 1996 (quoting the Newhall Signal); SD0100 caption (text from Estelle Foley); LW3347 | high. The 26 Aug 1926 date is matched by the station's own centennial on 26 Aug 2026 (LASD release) |
| Relationship to the City | The City of Santa Clarita is a contract city for public safety: it contracts with the County (LASD) for police services and has no police department of its own. The station captain serves as the City's chief of police. The City and County jointly financed the new station on City-owned land | City Candidate Handbook 2024, p. 4 ("Contract City - Fire and Public Safety"; saved in inventory/news/council-transition-2026-10-04/); City FY1988 CAFR letter ("the largest contract City in California", same folder); City release 14 Aug 2025 via SCVNews (captain announced by the City as "New Chief of Police"); City release 2018; SCVNews 2016 (City-owned land) | certain. The contract's own text and current dollar amount were not read: NEEDS_VERIFICATION if the record is to cite the agreement itself |
| Area served | LASD lists: Angeles National Forest, Bouquet Canyon, Canyon Country, Castaic, City of Santa Clarita, Gorman, Hasley Canyon, Newhall, Neenach, Sand Canyon, Santa Clarita, Saugus, Six Flags Magic Mountain, Sleepy Valley, Southern Oaks, Stevenson Ranch, Sunset Point, Tesoro del Valle, Valencia, Val Verde, West Hills, Westridge | lasd.org | certain |
| Captain now | **Captain Brandon Barclay**. The City announced him as the new captain on 14 Aug 2025. He succeeded Justin Diez, captain from March 2020, who was promoted to Commander | lasd.org station page (lists "Captain Brandon Barclay"); City release via SCVNews, 14 Aug 2025; City of Santa Clarita, 22 May 2026; LASD centennial release, 26 Aug 2026 | certain. Living person: public role only. The lasd.org page still carries Diez's old biography lower down; ignore it |

### Earlier captains named in the sources (for a list, if wanted)

* J.E.B. Stewart opened the substation in 1926 and later commanded it as captain (SD0100).
* Capt. Ambrose Stewart, until early 1950 (LW3347). Whether he is the same man as J.E.B. Stewart is NEEDS_VERIFICATION.
* Eugene A. "Gene" Haisch, early 1950 to 1 May 1960 (LW3347).
* Michael I. Quinn, 1997 (Worden, The Signal, 17 Sept 1997).
* Jeremy "Jerry" Conklin, 1992-1994 (mirror obituary title).
* Robert Lewis, 2018 (sc1806).
* Justin Diez, 2020-2025.

### Archive holdings

* No organization record for the station.
* **#29282 Los Angeles County Sheriff's Department** (organizations, orgType government) exists and would be the parent.
* **Articles:** #12292 and #12456 "Sheriff celebrating 70 years in Newhall" (Worden 1996, duplicated); #12198 "Despite complaints, we've got the policing we want" (1997).
* **Photographs:** #5187 Personnel of Substation No. 6, 1959 (LW3347); #5687 County Civic Center (LW7001); #4013 Sherman Block.
* **Not in the archive:**
  * SC1806, the 2018 groundbreaking.
  * SD0100 to SD0120a, Substation No. 6 photos.
  * SD1901, the Pelino memorial at the station, 2019.
  * The old Newhall jail photos (AL3025, AL3026, SW5201, SW5202, SD0200).
  * The Conklin obituary.

---

## For Nathan

1. **Newhall Elementary: the move to Walnut Street.** Record the move as September 1925, not 1928: three contemporary Signal reports and the Signal's 1939 history agree. Record the third school's date as "1911 or 1914, unresolved", and the 1914 fire as unconfirmed.
2. **Wikipedia's 1876.** Do not carry it. The district dates from 10 May 1877.
3. **"Oldest school in the valley" fails.** Sulphur Springs dates from 1872. Use "the Newhall School District's first school".
4. **Duplicate articles.** #12292 and #12456 appear to be the same Worden column; worth a duplicate check.
5. **Saugus captions.** Four captions give Newhall SD "est. 1879"; they are candidates for the live-errors list.
