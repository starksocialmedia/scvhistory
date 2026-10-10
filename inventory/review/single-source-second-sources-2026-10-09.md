# Claude, for Nathan: a second source for the 50 Perkins-or-Reynolds-only claims, 9 October 2026

Item 11 of your brief. This is a write-up only: no record was changed, nothing was committed, and nothing was written to the database.

## What was read

- **The list.** `inventory/review/overnight-2026-10-08/single-source-claims-2026-10-08.json`: the claims whose only counted source is "Perkins/Reynolds (one source, PROFILES.md)". There are 50. **Antonio del Valle has 11 of them, not 13**: 9 body spans plus the `birthDate` and `birthplace` fields. The .md table agrees (11 rows). The brief's 13 may have counted the two del Valle spans whose second source is Engelhardt; those are not on the Perkins-or-Reynolds-alone list.
- **docs/PROFILES.md**: the Reynolds rule, the Perkins rule, and the rule that a Worden column counts only where it names another source.
- **Craft records (read only, `ddev mysql` SELECTs)**: the bodies and footnotes of the 16 person records involved; Perkins 1957 (#1434) with his own notes 26 to 31, 45 to 49, 84 and 85; Rancho San Francisco (#16446, #382); the del Valle family (#915); the diseño (#849); the Ygnacio family tree (#4461); the cityhood records #4261, #28281, #28287, #28045, #2163; Hart High (#1442, #16052, #12304); and every record mentioning Nadeau with Soledad.
- **The Reggie mirror** (`/mnt/reggie/scvhistory.com/scvhistory/`, read in the web container with `iconv -f latin1`, so the latin-1 pages were not skipped): every page holding "antonio del valle" (80 files listed by `grep -rail`; the del Valle passages were read in `delcastillo1980.htm`, `camulos-nrhp2.htm`, `camulos-nrhp3.htm`, `camulos-nrhp4.htm` (sources), `cullimore_oldadobes.htm`, `cookexpeditions18201840.htm`, `belderrain1930.htm`, `camulos19581009.htm`, `engelhardt_sanfernando56.htm`); `jenkins_kreider1952.htm`, `ap2219.htm`, `tlp1601.htm` and the image `gif/tlp1601_large.jpg` (Jenkins's death certificate), `gif/tlp_lat102016.jpg` (his obituary); `signal/reynolds/bibliography.html`.
- **Archive.org full texts** (read, not uploaded to): Bancroft, *History of California*, vols. I (`historyofcalifor01banc`), III (`historyofcalifor03banc`), IV (`historyofcal04bancroft`) and V (`worksofhuberthow0022unse`, the Pioneer Register); Hoffman, *Reports of Land Cases* (1862), appendix (`reportsoflandcas01hoff`); Thompson and West, *History of Los Angeles County* (1880) (`historyoflosange00wils`); Edwin Bryant, *What I Saw in California* (1849 ed., `whatisawincalifoin00brya`); Horace Bell, *Reminiscences of a Ranger* (1881, `reminiscencesofr00bellrich`); *Biographical Directory of the American Congress, 1774-1949* (1950, `biographicaldire00unit`).
- **Other web**: San Diego History Center's Couts biography (Smythe, 1907).
- **Could not be read by script**: cdnc.ucr.edu and bioguide.congress.gov (both answer a scripted fetch with a Cloudflare challenge, HTTP 403); ECPP record pages (a JavaScript app, so only what the archive's footnotes already quote was available); Chronicling America through loc.gov (works, but returned a 503 after two queries, and its California titles barely cover 1860s Los Angeles). No web search was available in this session: the budget was used up. All fetches used a generic User-Agent.
- **Mirror pages for Beale and Nadeau**: `ripley*.htm` (all 16 parts), `bealescut.htm` and `bonsal_beale1912.txt`, searched for "5,000", "$5000" and "five thousand"; `nadeau-clarification.htm`, `obit-nadeauremi.htm` and `reminadeau-chrisman.htm`, searched for Soledad and Whites.

## Counts

| Result | Claims | of which del Valle |
| --- | ---: | ---: |
| Second source found, agrees | 32 | 7 |
| Second source found, differs | 4 | 2 |
| Reachable without a library, not yet read | 6 | 2 |
| Needs a library or archive visit | 6 | 0 |
| Attribution only ("Perkins wrote...", "Worden notes..."): nothing to corroborate | 2 | 0 |
| **All** | **50** | **11** |

Ten of the 32 "agrees" were already in the record's own footnote. The census counted a footnote as one source when it named several, and it took the first-named source as the footnote's source. Bell 1881 (Banning, 2 claims), Kreider 1952 and Pollack 2012 (Lyon, 2), Warren Times Mirror 1931, the Standard Oiler 1953 and the Los Angeles Times 1954 (Mentry, 2), the ECPP burial (del Valle, 1), and Kreider and the 1910 census (Jenkins, 3) were all there already. So the overnight figure of 50 overstates how much rests on Perkins or Reynolds alone. The census needs a fix that splits a footnote into its named sources.

## Antonio del Valle (#291), 11 claims

**37. Lieutenant, came 1819, took charge of San Fernando at secularization, granted the rancho 1839.** Found, agrees.
- Bancroft V (1886), Pioneer Register, p. 755: "Valle (Antonio del), 1819, Mex. lieut of the S. Blas infantry comp. ... in '34-5 he was comisionado for the secularization of S. Fern., where he served also as majordomo to '37 ... In '39 he was grantee of S. Francisco rancho, iii. 633, where he died in '41."
- Hoffman, *Reports of Land Cases* (1862), appendix: "318, 305, S. D., 71. Jacoba Feliz, claimant for San Francisco, in Santa Barbara and Los Angeles counties, granted January 22d, 1839, by Juan B. Alvarado to Antonio del Valle; claim filed September 2d, 1852, confirmed by the Commission January 2d, 1855, and appeal dismissed June 8th, 1857; containing 48,813.58 acres."
- Bancroft is Perkins's own source (his note 26), so it is earlier and independent of him, but not independent of the tradition Perkins drew on. Hoffman is an official table.

**38. Died on the rancho two years later without a will; heirs held it undivided until the 1870 partition.** Found, differs.
- Agrees on place and year: Bancroft V, 755, "where he died in '41".
- Differs on the partition. Three sources independent of Perkins say the rancho was divided soon after his death. Del Castillo 1980 (`delcastillo1980.htm`), citing Reginaldo del Valle's 1930 manuscript history: "After Antonio's death in 1841, the government divided the rancho among the heirs. Reginaldo's father Ygnacio got an 1800-acre parcel and called it Rancho Camulos." The NRHP nomination, 1996 (`camulos-nrhp3.htm`): "After Antonio's death in 1841, the land was divided among his wife and seven children." Belderrain 1930 (`belderrain1930.htm`): "After Don Antonio's demise, the ranch was divided between his widow and children."
- For Perkins: Hoffman shows a single claimant, Jacoba Feliz, for the whole 48,813.58 acres in 1852 to 1857, which fits an undivided estate. Perkins cites a document for the partition (notes 84 and 85, the California Title Guaranty Co. abstract of title, pp. 57-58), so the trust rule accepts him here.
- "Without a will" rests on Perkins's probate account, which also comes from the abstract (notes 45 to 49). The decree of 30 July 1870 and the probate file need an archive visit (Los Angeles County court records, or the abstract itself if it survives).

**39. Perkins, from Bancroft: from Jalisco, lieutenant of the San Blas company, 1819; Ygnacio joined him at Monterey in 1825, at seventeen.** Found, agrees, with one point to note.
- Bancroft V, 755-6: "V. (Ignacio), 1825, son of the lieut and nat. of Jalisco, who came to Cal. with Echeandía." In Bancroft, "nat. of Jalisco" belongs to Ygnacio, not Antonio. Perkins's note 26 moved it to the father. For Antonio's own birthplace, see claim 47.
- 1825 agrees. Bancroft says Ygnacio came with Governor Echeandía, who landed in the south, and that he became a cadet in the Santa Barbara company in 1828. Bancroft does not say Monterey. The age of seventeen fits the birth date of 1 July 1808 on record #915, whose source is not Perkins.

**40. Reynolds's ages (46 in 1834, 53 at death) put his birth about 1788.** Reachable, not read.
- No second source found. The ECPP burial record 72898 may carry an age; the archive's footnote does not say, and the ECPP page needs a browser. The 1836 Los Angeles padrón (published in the Historical Society of Southern California Quarterly, 1936) may list him at San Fernando: JSTOR, or a library.

**41. Granted by Alvarado on 22 January 1839, over a protest from Fr. Narciso Durán.** Found, agrees on the date; the protest is open.
- Hoffman (1862), above: "granted January 22d, 1839, by Juan B. Alvarado to Antonio del Valle."
- Bancroft III, 633, has opposition but does not name Durán: "granted in 1839 to Antonio del Valle, much against the wishes of the S. Fernando Ind.; Jacoba Felix cl."
- Engelhardt (`engelhardt_sanfernando56.htm`) gives Durán's indignant letter of 7 September 1839 about the colonists at San Fernando, but no protest against the grant. Perkins dates the protest 5 February 1839 and cites the expediente (Case 318, National Archives). That file needs a visit, or a check of whether the Bancroft Library's land case files have been digitized.

**42. Moved his family into the old Estancia, which became the rancho house.** Found, agrees.
- NRHP 1996 (`camulos-nrhp3.htm`): "Antonio del Valle and his family lived at the eastern edge of the ranch near Castaic in the former San Fernando Mission granary adobe building."
- Bancroft III, 648, on Hartnell's visit of June 1839: "Valle had not yet moved his family to the rancho." That fits a move after June 1839.
- The NRHP's source list (`camulos-nrhp4.htm`) includes Bancroft; whether it also used Perkins was not checked.

**43. Reynolds gives 21 June, Perkins 12 June; both after the burial.** Found, agrees: the ECPP burial of 3 June 1841 is footnote 7 of the same record. This one was a census error: the sentence's reasoning rests on the note in the sentence before it.

**44. The heirs were the widow, Ygnacio and the children of both marriages; the sources count them differently.** Found, agrees, and the count is indeed unsettled.
- The ECPP burial (fn 7) names his wife as Jacoba Lopez [Feliz]. Hoffman names Jacoba Feliz as claimant.
- The counts differ: Perkins's text gives two children by the first marriage and four by the second, his note 26 gives five by Jacoba, and the NRHP gives "his wife and seven children". ECPP baptism 976 (fn 8) adds a daughter born in 1831 to María Policarpa López.

**45. Distributed in undivided portions; partitioned in 1870, when Camulos, 1,340 acres, was set apart for Ygnacio.** Found, differs.
- Del Castillo 1980, after Reginaldo del Valle: an 1,800-acre parcel after 1841, with a contest by Pedro Carrillo in 1841. The NRHP 1996 (`camulos-nrhp3.htm`): "Ygnacio del Valle, Antonio's son, inherited 1,800 acres of the grant in 1842."
- Perkins gives 1,340 acres, effective 30 July 1870, signed by Judge Pablo de la Guerra, and cites the abstract of title.
- The two can both be true: possession of about 1,800 acres from 1842, and a legal partition of 1,340 acres in 1870. The decree settles it, and it needs an archive visit.

**46. Field `birthDate` = 1788.** Reachable, not read: same as claim 40.

**47. Field `birthplace` = Jalisco, Mexico.** Found, agrees.
- ECPP baptism 976, already footnote 8 on the record, gives the father as "Antonio Valles, of the city of Guadalajara, alférez". Guadalajara is the capital of Jalisco. The census did not see it because the field was traced to the spans citing Perkins and Reynolds.
- NRHP 1996: "a native of Compostela, Mexico". Compostela was in Jalisco's Tepic canton until the territory of Tepic (now Nayarit) was split off in 1884, so it does not contradict Jalisco. The record's birthplace note already gives Compostela as one genealogy's reading.
- One wrinkle: in 1831 the register calls him alférez (ensign), not lieutenant.

## The other 39

**William Wirt Jenkins (#20226), 5 claims: all found, agree.** Both sources are on Reggie but not in Craft.
- 1 (undersheriff; Castaic Creek from 1878): the Los Angeles obituary of 20 October 1916 (`gif/tlp_lat102016.jpg`, page `tlp_lat102016.htm`): "for six years, 1858-64, served as under sheriff ... at the time of his death owned extensive lands in Castaic Canyon." The 1878 settlement date is still Reynolds's alone. Kreider has him "deputy to Sheriff Alexander". Thompson and West (1880) list "W. W. Jenkins" among the Los Angeles Rangers and call him "a deputy constable" in 1856.
- 2 and 5 (born near Circleville, Ohio, 12 October 1833 or 1835): death certificate (`gif/tlp1601_large.jpg`): "Date of birth October 12 1835", "Birthplace Ohio". Obituary: "born at Circleville, O., in 1835". Two primary-era witnesses for 1835 against Reynolds's 1833.
- 3 and 4 (died 19 October 1916): death certificate, California State Board of Health, Local Registered No. 5087: "Date of death October 19 1916", at 1823 S. Flower St., Los Angeles.

**Sanford Lyon (#20224), 4 claims.**
- 6 (stage station; postmaster of Petroleopolis from 1869): reachable, not read. Pollack 2012 is already in the footnote. The primary is the Record of Appointment of Postmasters (NARA M841, Los Angeles County), online free at FamilySearch.
- 7 (the 1866 paper, "busy dipping the oil"): reachable, not read. The *Los Angeles Semi-Weekly News*, 1 June 1866, is quoted only through Perkins. Look for it in CDNC through a browser; if CDNC does not hold that title, it needs a library visit (Huntington).
- 8 (first well, 1869 or 1870, partners disputed): found, agrees. Kreider's typed copy of the agreement of 8 January 1869 and Walling (1934) are already in the footnote.
- 9 (death in 1881, 1882 or 1885): found, agrees that the sources disagree. Pollack (1882) and Addi Lyon's obituary (about 1881) are already in the footnote.

**Remi Nadeau (#18869), 10 (ranch in Soledad Canyon near Whites Canyon): found, agrees on the ranch.**
- His obituary, Los Angeles Times and The Signal, 28 November 1941 (`obit-nadeauremi.htm`): "33 years ago, he came to the valley of the Little Santa Clara, and purchased the home ranch at Soledad where he afterwards spent his life ... Their home ranches adjoined", his and John W. Mitchell's.
- The spot at Whites Canyon is not in the obituary.
- Leon's 1997 column (#2689) repeats Reynolds, so it is not a second witness.

**Phineas Banning (#18714), 5 claims.**
- 11 (first stage over the pass, December 1854): found, agrees. Bell (1881), pp. 322-4, read on Archive.org: "In December '54 Phineas Banning sat on the box of his Concord stage ... 'a beautiful descent, far less difficult than I anticipated.'"
- 15 (Southern Pacific cleared the thicket): found, agrees. Bell, p. 324: "Twenty-two years thereafter the S. P. R. R. Company cleared away the thicket in which Banning made his first stage stand, in excavating their wonderful San Fernando tunnel."
- 12 (Perkins took it as the first stage at the rancho): found, agrees. Bell calls it "the first stage that ever went out of the Valley of the Angels".
- 13 (Worden's note that Perkins may have doubted the crossing point): attribution only, nothing to corroborate.
- 14 (bought in to the Soledad mines, 1863): reachable, not read: CDNC, Los Angeles papers of 1863, through a browser.

**Charles Alexander Mentry (#18648), 3 claims.**
- 16 ("Then the West"): needs a library. Leon's ch1070 names no source, so it is the same account. Every independent source found says California, not the West.
- 17 ("Then California"): found, agrees, already in the footnote: Warren (Pa.) Times Mirror, 4 February 1931 (#20096); the Standard Oiler, August 1953; Los Angeles Times, 17 March 1954 (#20107).
- 18 (the 1954 Times hedges): found, agrees, already in the footnote: the Times, #20107: "credited with locating and drilling the first oil well in California."

**William S. Hart (#16356), 19 (the high school was named for him in his lifetime): found, agrees.** Leon's column "Keep Canyon, change Hart district's name" (#12304) quotes The Signal of 9 August 1945: the board "initiated a move to have the name changed from Santa Clarita to William S. Hart ... after ascertaining that Mr. Hart is agreeable." It names a source other than Reynolds, so under PROFILES.md it counts. That the school opened in 1945 is not confirmed: the column has the district created in January 1945 and the building bond passed on 2 June 1945.

**Jo Anne Darcy (#16140), 20 (joined the City Formation Committee soon after it was organized): needs a library.** Connie Worden-Roberts's 1999 history (#28281) names Darcy in the Canyon County drive of the 1970s, not the City Formation Committee. Carl Boyer's 2005 cityhood book and The Signal of 1985-87 are the places to look.

**Jill Klajic (#15874), 21: found, agrees on membership.** Leon's caption on the 1985 membership form (#4261): "252-4947 was the home phone number of committee secretary Jill Klajic." Worden-Roberts (#28281): "A City Formation Committee was formed ... Among them were Jill Klajic and Allan Cameron." That she was paid staff is not confirmed.

**Arthur Buckingham Perkins (#333), 22 (Reynolds cites Perkins's history of the rancho): found, agrees.** `signal/reynolds/bibliography.html`: "Perkins, A.B. Rancho San Francisco. Los Angeles, 1957."

**Edward F. Beale (#327), 23 ($5,000 from the supervisors for the cut): needs an archive visit.**
- Perkins 1957 differs, though he is the same leg: "In 1858 ... the County Board of Supervisors put out $5,000 in County warrants for road improvements by and through Rancho San Francisco." That is four years before Beale's franchise of 1862, and Perkins does not say the money was Beale's.
- V.S. Ripley's articles on the San Fernando Pass (`ripley*.htm`, all parts) have no $5,000; the only hit is two 1870s oil companies "with a capital of $5,000,000 each". Bonsal's Beale (1912) has none either.
- The minutes of the Los Angeles County Board of Supervisors for 1858-64 need an archive visit.

**Cave Johnson Couts (#323), 3 claims: all need a library.**
- 24 (his 1852 letter to Stearns): the Stearns Papers at the Huntington. Cleland's *Cattle on a Thousand Hills* (1941) is on Archive.org for borrowing only and may quote the letter; not read.
- 25 (the 1849 drive): Smythe (1907), via the San Diego History Center, has him on army duty at Los Angeles, San Luis Rey and San Diego in 1848-51, and "in 1849 he conducted the Whipple expedition to the Colorado River". That is no support for a spring 1849 drive, and worth a look at his 1849 journal (*Hepah, California!*, 1961).
- 26 (Ysidora and the roof railing): no source found.

**Andrés Pico (#317), 3 claims.**
- 28 (surrender at Cahuenga, 13 January 1847): found, agrees. Bancroft V, 404: "the battalion moved its camp to the rancho of Cahuenga ... next morning, January 13th, it received the signatures of the respective commandants, Frémont and Pico", with the articles quoted "made and entered into at the ranch of Cowenga this 13th day of Jan., A. D. 1847". The Feliz house is not named in Bancroft.
- 27 (among the first to file oil claims in 1865): reachable, not read. Bancroft's register entry (vol. IV, 776-7) says nothing of oil. The next reads are CDNC for 1865 and the Los Angeles County mining district records.
- 29 ("Perkins names Pico among the filers"): attribution only.

**Thomas O. Larkin (#311), 30 (told the New York Sun a laborer could pick up $2 a day): found, agrees.** Bancroft IV, 635: "June 30, 1846, Larkin writes to N. Y. Sun that a common laborer can pick up $2 per day. Larkin's Doc., MS., iv. 183." Bancroft puts the placers "on the San Francisco rancho" and gives the name San Feliciano as of 1846 (p. 297, n. 45). "In a canyon off Piru Creek" is not in Bancroft.

**John C. Frémont (#307), 6 claims.**
- 33 to 36 (born Savannah, 21 January 1813; senator; first Republican candidate, 1856; governor of Arizona; died New York City, 13 July 1890): found, agrees. *Biographical Directory of the American Congress* (1950): "born in Savannah, Ga., January 21, 1813 ... unsuccessful as the first Republican candidate for President of the United States in 1856 ... Governor of Arizona Territory 1878-1881 ... died in New York City July 13, 1890."
- 31 (crossed the valley in January 1847 to Cahuenga): found, agrees: Bryant's diary and Bancroft V, below. The naming of Frémont Pass was not checked.
- 32 (the route, from the Leon and Reynolds shared text): found, differs. Edwin Bryant marched with the battalion and kept a diary (*What I Saw in California*, 1849 ed., pp. 389-91):
  - "January 9 ... We encamped this afternoon at a rancho, situated on the edge of a fertile and finely-watered plain".
  - "January 10. Crossing the plain we encamped ... in the mouth of a cañada, through which we ascend over a difficult pass ... between us and the plain of San Fernando".
  - "January 11 ... the main body, on foot, marching over a ridge of hills to the right of the road".
  - The dates and the sequence agree. The size does not: Bryant gives the battalion on 30 November 1846 as "rank and file, including Indians and servants, 428", not a hundred men. And it came up the Santa Clara from San Buenaventura, from the west, not from the north.

**Pedro Fages (#287), 3 claims.**
- 49 (twenty-five Catalan volunteers; governor until 1791): found, agrees. Bancroft I: "twenty-five Catalan volunteers under Lieutenant Pedro Fages". Bancroft III register: "came back as gov. ... Sept. '82 to April '91".
- 50 (`birthplace` Catalonia): found, agrees. Bancroft I: "Doña Eulalia, a native of Catalonia, like her husband".
- 48 (the pursuit of the deserters through the valley): found, differs on the year. Bancroft I, 197: "It is recorded that some time during 1773 Comandante Fages, while out in search of deserters, crossed the sierra eastward and saw an immense plain covered with tulares". He gives 1773 against the record's 1772, and no route. The route rests on Fages's own diary as Bolton read it (*California Historical Society Quarterly*, 1931): reachable on JSTOR, not read.

## Read from a description

Six places where the archive took a point from something said about a source rather than from the source:

1. **Jenkins's death certificate.** The footnote says "The legacy page cites his death certificate ... The certificate itself is not held." It is held. The scan is on Reggie at `scvhistory.com/gif/tlp1601_large.jpg` (page `tlp1601.htm`, "courtesy of Tricia Lemon Putnam"), and his obituary is at `gif/tlp_lat102016.jpg`. Neither has been imported into Craft. The date came from the caption. Read today, the certificate agrees (19 October 1916) and adds birth on 12 October 1835 in Ohio.
2. **The land case number.** Antonio's footnote 4 and Rancho San Francisco's footnote 5 cite "Land Case 303 SD". Hoffman's table (1862) gives the case as Commission No. 318, District Court No. **305** S.D.; Perkins's "Case 318" is the Commission number. The 303 seems to have come from the source review, not from a case file, and should be checked before it is cited again. Hoffman also gives 48,813.58 acres at confirmation; the patent figure of 48,611.88 is still from the review, as the overnight report said.
3. **Nadeau's ranch.** The footnote cites Hometown Station's "Today in SCV History" for 27 November 1941, "known here only from a summary". The summary is almost certainly of his obituary, which is on Reggie: Los Angeles Times and The Signal, 28 November 1941, `obit-nadeauremi.htm`. Read today, the obituary has the Soledad ranch but not the spot at Whites Canyon road, which so far comes only from the summary.
4. **Lyon's 1866 paper.** It is quoted only through Perkins; the paper has not been seen. The overnight report already noted this.
5. **Perkins's "Jalisco" for Antonio.** It is Perkins's reading of Bancroft, and Bancroft gives Jalisco as Ygnacio's nativity. The point stands, but on a different witness: the 1831 baptism's "of the city of Guadalajara".
6. **Perkins #333, note 2.** It said the Reynolds bibliography was "checked in the archive's review of Reynolds's sources", which is the review, not the page. Read today, the bibliography says what the note says.

## What I would do next, one at a time

Import the Jenkins death certificate and obituary from Reggie as document records and point his notes at them. They settle five claims, and they are already on the drive.
