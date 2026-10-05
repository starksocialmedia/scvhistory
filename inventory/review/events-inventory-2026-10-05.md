# Events inventory, 5 October 2026

Claude, read-only research for Nathan before the events section is built. Nothing was written to the database and nothing was committed.

Method. The legacy mirror (`/Volumes/Reggie/SCVHistory/scvhistory.com`, 33,690 HTML pages) was read as latin-1 into a local index and searched by page title and by text. Every Craft entry (4,133, all sections, enabled and disabled) was exported and searched by title, body and legacy key; mirror pages were matched to Craft by `legacyUrl`, `sourcePath` and `legacyKey`. Counts "by title" are pages whose `<title>` names the event; Leon's photo pages carry the category in the title (`SCVHistory.com AL3028 | St. Francis Dam Disaster | ...`), so title counts are reliable. Text-only mentions are noted where they matter, not counted, because the site's navigation repeats event names on thousands of pages.

Labels used below: **[general knowledge]** is not from an archive source; **[Wikipedia only]** rests on Wikipedia alone. Nothing below rests on Wikipedia.

---

## The events section as built

Section `events` (channel, URI `events/{slug}`), one entry type `event`. Its fields:

- Text and notes: `body`, `footnotes` (table), `footnotesOn`, `webmasterNoteTop`, `webmasterNoteBottom`, `editorNotes` (table), `researchLeads`, `withheldBody`, `culturalSensitivityNote`
- Dates: `eventDate`, `eventDateEdtf`, `startEvidence` (certified, contemporary, retrospective, roster, derived, uncited), `eventDateStart`, `eventDateEnd`, `recordDates` (table), `eventRecurring`, `eventFrequency`, `eventNextOccurrence`
- Identity: `eventSignificance`, `eventChlNumber`, `wikidataId`, `eventWikipediaUrl`, `eventLegacyUrl`, `legacyKey`, `legacyUrl`, `sourcePath`, `legacyHtml`, `legacyCategory`
- Relations: `eventPlaces` (places), `eventPersons` (persons), `eventOrganizations` (organizations), `eventArticles` (articles), `eventGroups` (groups), `relatedEvents` (events), `derivedImageLinks` (any)
- Taxonomy: `recordTags`, `historicalEra`, `historicalPeriod`, `neighborhood`
- Media: `featuredImage`, `bandImage`, `recordImages`, `recordDocuments`

Reverse relation fields that point at events: `photoEvents` (photographs), `articleEvents` (articles), `personEvents`, `orgEvents`, `placeEvents`. Documents and fallen officers have **no** event field. No photograph and no article in Craft currently uses `photoEvents` or `articleEvents` (0 of each).

Two structural points for Nathan, not decided here:

1. A fallen officer cannot be linked to an event: `eventPersons` accepts only the persons section, and fallen officers have no event field. The Newhall Incident and Kuredjian records would need one or the other.
2. Unused taxonomy that fits this work already exists: themes "St. Francis Dam" (18942), "Northridge Earthquake" (18943), "Fire & Flood" (18940), "Incorporation & Cityhood" (18929), "Railroad" (18932), "Mining & Gold" (18934), all with 0 relations; era "St. Francis Dam Era (1926-1928)" (163), 0 relations.

DATA-ORGANIZATION.md line 210 says the St. Francis Dam itself is a Place and the disaster a tag "until an Event type exists". There is no St. Francis Dam place record in Craft.

## The three existing event records

| ID | Title | Filled | Empty that matter |
|---|---|---|---|
| 27712 | The SCV Water Consolidation | body, footnotes (2), eventDate (January 1, 2018), eventDateEdtf, eventSignificance, eventOrganizations (402, 26563, 27534), historicalEra (171), historicalPeriod (184) | startEvidence, recordDates, eventPlaces, relatedEvents |
| 20228 | Lyon, Wiley and Jenkins Drill at Pico Canyon | body, footnotes (4), eventDate (late 1860s), eventDateEdtf (186X), startEvidence (retrospective), eventPlaces (16163 Pico Canyon), eventPersons (20224, 20226, 331), neighborhood (200), recordDates (3) | historicalEra, historicalPeriod, eventSignificance |
| 875 | Northridge Earthquake | featuredImage (asset 61), body, footnotes (9), eventDate, eventDateEdtf, eventDateStart, eventSignificance, eventPlaces (934), historicalEra (169), historicalPeriod (182), legacyKey, legacyUrl, sourcePath, recordDates (2), withheldBody | see section 10 |

---

## 1. St. Francis Dam failure, 12 March 1928

**(a) Mirror pages.** 353 pages name the dam in their title: 246 photo pages, 4 photo galleries, 42 news pages and clippings, 55 documents and pages, 3 oral histories, 2 timelines, 1 Leon Worden Signal column. About 200 more name the dam in their caption or first lines without it in the title: 166 photo pages and 32 others, of them some 23 more 1928 newspaper pages (`nyt031528a` to `e`, `sfc031328`, `aen031328`, `bdn031428`, `nled031328`, `ombn031428` and others). Some of the 200 are peripheral (aqueduct, Harry Carey Ranch). This is the largest single subject in the mirror.

The hub is `/scvhistory/stfrancis.htm` ("SaintFrancisDam.com"), Leon's index of everything below. Key pages:

| Path | What | Kind |
|---|---|---|
| `/scvhistory/annstansell_damvictims022214.htm` | Roster of victims, Ann Stansell 2011-2014, with Leon's update of 1-17-2018; current list as `annstansell_damvictims011718.pdf` | document |
| `/scvhistory/stansell2014.htm` | Stansell, "Memorialization and Memory...", CSUN master's thesis 2014 | document |
| `/scvhistory/sfdcoronersinquest.htm`, `sfdcoronersverdict.htm` | Coroner's inquest transcript, March-April 1928; jury verdict 4-12-1928 | document |
| `/scvhistory/nlf-stfrancis.htm` (+ `nlf-stfrancis-index.htm`) | Newhall Land & Farming Co. report on flood damage to the ranch, 3-24-1928 | document |
| `/scvhistory/stfrancis-claims071529.htm` | Death and disability claims and claimants, 7-15-1929 | document |
| `/scvhistory/guyljones1928.htm` | Report to Arizona Gov. Hunt, 1928 | document |
| `/scvhistory/stfrancistimeline_pollack2014.htm` | Pollack, extended timeline, 2014 | timeline |
| `/scvhistory/pollack0308victims.htm`, `pollack0310dam.htm`, `pollack0716stfrancis.htm` | Pollack, Heritage Junction Dispatch, 2008, 2010, 2016 | page |
| `/scvhistory/peggykelly_*.htm` (6) | Peggy Kelly series, 2009-2010 | page |
| `/scvhistory/sg19780522dam.htm` | Historical Society marker placed and stolen 1978, state marker 1979 (Leon's note) | news |
| `/scvhistory/signal/worden/lw022603.htm` | Leon Worden, Ivan Dorsett remembers | LW column |
| `/scvhistory/al2034.htm` to `al2058.htm` | 22 front pages of March 1928 from across the country | photo |
| `/scvhistory/lw2642ab.htm` etc. | ex-SFPUC archive views after the break | photo |
| `/scvhistory/fox_c3159.htm`, `fox_c3160.htm`, `fox_c9738.htm`, `fox1928film.htm`, `sw_britishpathe031328.htm` | 1928-1929 newsreel footage, incl. the temporary Newhall morgue | film page |
| `/scvhistory/bizbasolo_fillmore2009.htm`, `thelmamccawleyshaw_fillmore2010.htm`, `juleelicon2014.htm` | survivor oral histories | oral history |
| `/gif/galleries/lw2731/` | Saugus Community Club plaque for 7 member victims | gallery |
| `/gif/galleries/hs7801/` | Program, dedication of California Historical Landmark 919, 5-21-1978 | gallery |
| `/scvhistory/hr2156_2017.htm`, `s47_2019.htm` and 8 more | National Memorial legislation 2014-2019 | document/news |
| `/scvhistory/timeline.htm` | "March 12, 11:57 p.m.: St. Francis Dam breaks, killing more than 450"; also March 15 inspection, March 24 NLF report, 1929 claims report, 2019 S.47 | timeline line |

**(b) Photographs.** Mirror: about 412 photo pages (246 by title, 166 by caption), 4 galleries. Examples: AL3028 "Aerial View"; AL2020 "Castaic Highway Bridge Destroyed by Floodwaters, March 1928"; AL2043 "Boston Evening Transcript: Dam Breaks; 400 Perish?"; LW2642gb "Overview of Dam After Break"; WS2802 "Empty St. Francis Reservoir". Craft: **1**, photograph 28063 (LW2054, "William Mulholland, St. Francis Dam Builder"). None of the dam photographs has been imported.

**(c) Documents and articles in Craft.** No document record is about the dam. Articles that cover it: 2133 (Reynolds, "54. Disaster at 185 Feet"), 12214 (Leon, "'Historic' event with real life, real death"), 12597 (Pollack, "Mulholland: There It Is, Take It"), 2167 (Reynolds, "71. Requiem", the site today). Passing mentions: 12490, 12380, 12484, 12166, 12140, 1428; document 27374 (Perkins 1957) mentions one flood photograph.

**(d) People and places a source ties to it.**
- 16432 William Mulholland (builder; the collapse ended his career; Craft body says so)
- 16356 William S. Hart (Photoplay: his ranch home a relief center; his letter to Wyatt Earp on bodies in the Newhall morgue, `lat021503.htm`)
- 15919 Harry Carey and 599 Harry Carey Ranch (trading post washed away; Leon's caption and Craft bodies)
- 2536 Ruiz Cemetery (six of the Ruiz family killed; buried there)
- 15691 Newhall Land and Farming Company (filed the 1928 damage report; the Craft record does not mention it)
- 15596 Santa Clara River (the flood's course; NLF report and Leon's captions)
- Not to link: 1395 James Robert Ball (his uncle's family died; a family tie, not a role), 944 Newhall Family (date table only), 279 Leon Worden (memorial foundation, later).
- No record exists for the dam, Powerhouse No. 2, Castaic Junction, the Edison camp at Kemp, Thornton Edwards, Louise Gipe, or the dam keeper Tony Harnischfeger.

**(e) Existing event record.** None.

**(f) What the sources say.**
- Time: 11:57:30 p.m., March 12 (Leon's captions on the AL and ES pages; timeline "11:57 p.m."). The Newhall Land report of 3-24-1928 says "on or about 11:30 P.M." Floodwater reached the ocean at 5:25 a.m. March 13, 54 miles.
- Dead. Nathan's 431 is Stansell's 2014 total: 306 recovered bodies (240 identified) plus 125 missing, 79 of them with paid death claims. **Leon's update of 17 January 2018 on the roster page lowers it to 411**: twenty of the missing, never claimed, were found alive in the 1930 census. The current photo-caption boilerplate says "an estimated 411" (on 114 pages); older boilerplate says "431" (81 pages) and "470" (16 ES1928 pages). Others: timeline "more than 450"; Leon's Concise History `lwhist.htm` "more than 450"; Pollack 2008 "between 450 and 600", Pollack 2016 "some 431" and "over 400"; Rippens 1998 "over 450"; The Signal 1978 "nearly 450"; Rogers 2007 "more than 432"; 1928 headlines run from 200 to 1,000. The archive's own latest word is 411.
- The flood as a separate event: no. Every source treats the failure and the flood as one disaster; the NLF report calls it "the flood following the breaking of the St. Francis dam". The valley's part is the first miles of the flood path, which belongs in the one record's body.
- Valley effect (sources): Powerhouse No. 2 and its workers' homes destroyed a mile and a half below the dam (Pollack 2008, Leon's captions); the Frank LeBrun ranch and the Harry Carey ranch and trading post; the Ruiz family; at Castaic Junction the railroad and highway bridges and the Edison transformer station struck (NLF); the Edison construction camp at Kemp, "just west of the Los Angeles-Ventura County Line" (NLF), so on the Ventura side; seven members of the Saugus Community Club; the temporary morgue in Newhall (Fox newsreel; Hart's letter: "78 bodies in the little shack").
- Landmark: California Historical Landmark 919 (5 pages). National Memorial and Monument: S.47 signed 12 March 2019 (timeline).

**The dam's literature the archive cites** (for the record to point at):
- Charles F. Outland, *Man-Made Disaster: The Story of St. Francis Dam*, Arthur H. Clark, 1963; revised 2nd ed. 1977 (`bibliography.htm`; 30 pages)
- Norris Hundley Jr. and Donald C. Jackson, *Heavy Ground: William Mulholland and the St. Francis Dam Disaster*, University of California Press, 2015 (`bibliography.htm`)
- Jon Wilkman, *Floodpath: The Deadliest Man-Made Disaster of 20th-Century America and the Making of Modern Los Angeles*, Bloomsbury, 2016 (`bibliography.htm`)
- John Nichols, *Images of America: St. Francis Dam Disaster*, Arcadia, 2002 (`bibliography.htm`)
- *The St. Francis Dam Disaster Revisited*, Ventura County Museum of History and Art and the Historical Society of Southern California, 1995, with J. David Rogers (`lat091695.htm`, `rippens1998.htm`). That Doyce B. Nunis Jr. edited it is [general knowledge]; no archive page names him.
- J. David Rogers, "The 1928 St. Francis Dam Failure and Its Impact on American Civil Engineering", ASCE 2007 (`ro2007asce.htm`); "Who Designed the Ill-Fated St. Francis Dam?" 2017 (`jdrogers2017a.htm`)
- Ann C. Stansell, roster 2014 (rev. 2018) and master's thesis, CSUN 2014
- Coroner's inquest transcript and verdict, 1928; Newhall Land report, 1928; claims report, 1929
- Charles C. Teague, *Fifty Years a Rancher*, 1944 (excerpt `teague1944.htm`)
- Wesley S. Griswold, "The Day the Dam Burst," Popular Science, March 1964
- Paul H. Rippens, "The Night of the Flood," 1998
- Alan Pollack, Heritage Junction Dispatch pieces 2007-2016 and the 2014 timeline
- J.R. Elizondo, Caltech master's thesis, 1953 (geology); Nickell 1928 (geology)
- The 1928 Governor's commission report is not cited anywhere in the mirror.

**(g) Verdict: full record now**, short, pointing at the literature, the roster and the inquest. Fields: eventDate (March 12, 1928), eventDateEdtf, startEvidence (contemporary), eventDateEnd (March 13, 1928, the flood reaching the sea), eventSignificance, eventChlNumber (919), eventPlaces (599, 2536, 15596; a St. Francis Dam place is missing), eventPersons (16432, 16356, 15919), eventOrganizations (15691), eventArticles (2133, 12214, 12597, 2167), editorNotes (411 against 431 and the others; 11:57 against 11:30), historicalEra (163), recordTags (theme 18942), neighborhood (209 San Francisquito Canyon). The photographs need importing first; until then recordImages can hold only LW2054.

---

## 2. The Newhall Incident, 5 April 1970

**(a) Mirror pages.** 23 by title plus `sg4701.htm` (generic title): 17 photo pages, 5 documents and pages, 2 news.
- `/scvhistory/chp-newhall-incident.htm`, the CHP's account with Leon's note on the date (in Craft as the officers' source)
- `/scvhistory/al1977a.htm` to `al1977h.htm`, the CHP Information Bulletin of 1970, eight pages: a primary minute-by-minute report
- `/scvhistory/al1970.htm` to `al1973.htm`, wire photos; `du1970.htm` to `du1972.htm`, memorial wall dedication 1970; `lw2524.htm`, deputies in tear gas, 4-6-1970; `lw2825.htm`, hostage's son, 4-6-1970
- `/scvhistory/vnvgs040770a.htm`, `b`, The Valley News and Valley Green Sheet, 4-7-1970
- `/scvhistory/sg4701.htm`, Kristin Wilder, "The CHP Slaying: 30 Years Later," The Signal, 4-5-2000
- `/scvhistory/pollack0309newhallincident.htm`, Pollack, 2009
- `/scvhistory/gundigest20130506.htm`, Gun Digest 2013, excerpt from Michael E. Wood's book
- Timeline: April 5, 1970, the slaying "in the J's Coffee Shop parking lot"; June 5, 1970, memorial wall dedicated
- Peripheral: `lw2820.htm` (J's Coffee Shop matchbook), `obituary_ralphspencerlesliejr.htm`

**(b) Photographs.** Mirror 17 pages (8 of them the bulletin). Craft 2: 3977 (LW2524), 4553 (LW2825). Not in Craft: AL1970 to AL1973, AL1977a to h, DU1970 to DU1972.

**(c) Documents and articles in Craft.** None about the incident.

**(d) Ties.** Fallen officers 29768 Frago, 29770 Gore, 29772 Pence, 29774 Alleyn (section disabled); organization 29792 California Highway Patrol (its body names the incident). No person or place record for Jack Twinning, Bobby Davis, Gary Kness, the Hoag house, or the parking lot.

**(e) Existing records.** No event record. The four officer records already carry the narrative, near identical in each: shot "just before midnight on April 5, 1970; the official memorials date it April 6"; place, the restaurant parking lot at I-5 and Henry Mayo Drive (now The Old Road at Magic Mountain Parkway); gunmen Twinning and Davis; the memorial wall (June 5, 1970) and the 2006 highway naming. Each has an editor's note "The date" keeping both dates, and a factSources table.

**(f) What the sources say.**
- Date: the CHP bulletin of 1970 puts it all on "SUNDAY" night, April 5: 2320 hours the first brandishing report, 2354 hours Gore and Frago behind the car, **2356 hours** Pence radios "shots fired". Leon's note on the CHP page: April 6 "was originally used", but all four were dead by 23:59 on April 5. The Signal 2000: "around midnight on the night of April 5". Pollack 2009, the DU and AL captions, Gun Digest and the timeline: April 5. The CHP memorial page, the Peace Officers' Memorial and Caltrans say April 6 (per the officer records' notes; external sources). The April 6 photographs (LW2524, LW2825) are the next morning's siege, where Twinning died: a second day, not a disagreement.
- Dead: four officers; Twinning by his own hand the next morning (Signal 2000).
- Valley effect: the memorial wall at the Newhall CHP office; Interstate 5 named for the four in 2006; Pollack: "It changed police procedures forever after"; The Signal 2000 quotes the Newhall CHP: "We use it in all of our academy training."
- Its literature: Michael E. Wood, *Newhall Shooting: A Tactical Analysis* (book; excerpted in Gun Digest 2013).

**(g) Verdict: full record now**, brief, pointing at the four officer records and Wood's book. Fields: eventDate (April 5, 1970), eventDateEdtf (1970-04-05), startEvidence (contemporary, the bulletin), editorNotes (April 6 in the official memorials), eventOrganizations (29792), recordImages once the AL and DU photographs are imported, historicalPeriod (1970s). Blocked in part by the missing link from event to fallen officer (structural point 1).

---

## 3. Deputy Kuredjian killed, Stevenson Ranch, 31 August 2001

**(a) Mirror pages.** 2 by title: `/scvhistory/kuredjian082811.htm` (Carol Rock, KHTS, 8-28-2011) and `lw2613a.htm` (his name on the Peace Officers' Memorial). Also `lw2613b.htm`; the timeline line ("LASD Deputy Hagop 'Jake' Kuredjian gunned down in Stevenson Ranch while backing up ATF"); Leon's column `signal/worden/old/lw011202b.htm`; passing mentions in `obituary_pelinoarthure.htm`, `sd1901.htm`, `lw2833.htm`.

**(b) Photographs.** Mirror 2; Craft 2: 4257 (LW2613a), 4259 (LW2613b). Neither shows the event.

**(c) Craft documents and articles.** Article 12298 (Leon, "The circuitous trip home and back") mentions him.

**(d) Ties.** Fallen officer 29778 (disabled); organization 29282 Los Angeles County Sheriff's Department (as foAgency). No record for James Allen Beck or Brooks Circle; 29682 Santa Clarita Valley Sheriff's Station does not mention him.

**(e) Existing record.** Officer 29778 holds the whole event: date, Brooks Circle, the shot from a second-story window, Beck's death in the fire, the memorials, and editor's notes on which federal agency (ATF in the station's 2008 release, U.S. marshals in The Signal 2025) and on the rose garden's date.

**(f) Sources.** Every source gives August 31, 2001. Dead: Kuredjian; Beck died when tear gas canisters ignited and the house burned (KHTS 2011). Valley effect: the street, park, monument and highway named for him (officer record).

**(g) Verdict: a line.** There is no event apart from the officer's death, no literature of its own, and the officer record already says all the sources say. A timeline line pointing at 29778 serves; a second record would repeat it.

---

## 4. Cityhood, December 1987

**(a) Mirror pages.** 26 by title: 18 photo pages, 4 documents, 1 news, 3 Leon columns. Plus Carl Boyer's book in full (`boyer2015ch01.htm` to `ch25`, appendices, index: 32 pages), the City's `sc19872007.htm` (20 years), `sc1803.htm` (First 30 Years of Cityhood, 2018), `citycouncilmembers.htm`, `cc121587audio.htm` and `cc121587slideshow.htm` (the first council meeting, 12-15-1987), and timeline lines for November 3 and December 15.

**(b) Photographs.** Mirror 18. Craft 15: 29679 (BW8702, council-elect, November 1987), 29676 (SC8801, first council), 4255 (LW2612 petition), 4261 (LW2614), 4385 (LW2691 logo), 4859 (LW3059), 4861 (LW3060), 4875 (LW3070 poll), 4877 (LW3071 LAFCO), 4879 (LW3072), 4967 (LW3149), 5019 (LW3197 application, 12-17-1985), 5701 (LW8501 press kit), 5703 (LW8602 boundary map), 5763 (LW9502). Not in Craft: BW8703 (inauguration program 12-15-1987), BW8704 (campaign office), SC1803.

**(c) Documents and articles in Craft.** Documents 28281 (A Brief History of the Push for Self-Government), 28287, 28293, 28295, 28303, 28305, 28310; obituary 28045 (Connie Worden-Roberts); articles 2163 and 2165 (Reynolds 69 and 70, "Birth of a City"), Leon's 12368, 12320, 12388, 12426; election 21948 (City Council election, November 3, 1987).

**(d) Ties.** Organization 394 The City of Santa Clarita (body: incorporated 15 December 1987; Proposition U 14,723 to 6,597; Proposition V); 396 SCV Chamber of Commerce ("its leaders prepared the initiative"). Persons: first council 18791 McKeon, 15737 Heidt, 16140 Darcy, 15808 Boyer, 23081 Koontz; City Formation Committee or campaign 16418 Connie Worden, 15929 Laurene Weste, 15874 Jill Klajic, 18726 George Pederson (petition signatures). No record for Art Donnelly, campaign co-chair (LW9301a, LW9502).

**(e) Existing records.** No event record. **Election 21948 has an error:** its ballotMeasures row for Measure U, Incorporation, has `carried: false` beside 14,723 yes to 6,597 no. Its only footnote reads "summary.", a placeholder.

**(f) Sources.** Vote November 3, 1987; incorporated December 15, 1987, the first council meeting (timeline, LW3149, SC9610, `citycouncilmembers.htm`, the City's 20-year book, org 394). No disagreement found. Valley effect: one city of Newhall, Saugus, Valencia and Canyon Country, from a proposed 90-square-mile boundary (LW8602); Leon's columns trace what followed.

Literature: Carl Boyer, *Santa Clarita: The Book* (2005; 2nd ed. 2015, in the mirror); the City's 20-year (2007) and 30-year (2018) books.

**(g) Verdict: full record now.** Most of what it needs is already in Craft. Fields: eventDate (December 15, 1987), eventDateStart (November 3, 1987, the vote), startEvidence (certified), eventOrganizations (394, 396), eventPersons (the nine above), eventArticles (2163, 2165, 12368), recordDocuments, historicalEra (168 Cityhood Era), recordTags (theme 18929). Measure U's flag in 21948 should be fixed first.

---

## 5. The 1842 Placerita gold discovery

**(a) Mirror pages.** 97 titles match, about 30 of them other Lopezes or other gold. The relevant set: about 25 photo pages, 20 documents and pages, 8 news and records, 4 columns. Hub: `/scvhistory/placerita.htm`.
- Leon's notes: `belderrain1930.htm` ("Oak of the Golden Dream: A Legend is Born"), `lp_santacruzsentinel082785.htm` (Abel Stearns's 1867 letter, "No Mention of Dream"), `sw9501.htm` (Guinn 1895, "Historian Questions Date"), `vl0406-lopez.htm`
- Pollack, `pollack1115dream.htm` ("Dissecting the Dream", 2015)
- Older historians: `hssc1906jenkins.htm` (1906), `prudhomme1922hssc.htm`, `engelhardt_lopezgold.htm` (1927), `hssc1928belderrain.htm`, `giffen1948.htm` (an earlier 1838 shipment)
- Family accounts: `latta1976franciscolopez.htm` (grandnephew José Jesús López), `lat042396.htm` (Francisco Garcia 1896)
- Records: baptisms `sgb03346.htm` (Lopez, 3-10-1802), `sgb03175.htm`, `sgb03987.htm`, `sgb07294.htm`, `bpb00632.htm`, `lab00306.htm`, `sfrd01626.htm`; `overlandmonthly0592.htm` (U.S. Treasury voucher for the gold deposit, 6-8-1843)
- 1930 dedication: AP9010 to AP9014 (affidavits and speeches), AP0510, AP0512, AP1308, AP1407, GS2013, SW3001, SW3005, LW2575a to c
- Timeline: March 9, 1842 discovery; March 10 samples to Los Angeles; April 4 mining rights; May 3 first mining district at Rancho San Francisco, Ygnacio del Valle chairman; October 1 New York Observer report; March 9, 1930 Oak dedicated

**(b) Photographs.** Mirror about 25. Craft 8: 2963 (LW2181 New York Observer, 10-1-1842), 2989 (LW2217 Oak ~1963), 3313, 3337, 3339, 4731 (1948 centennial medals), 4169, 4171 (LW2575b, c, 1930 dedication papers).

**(c) Documents and articles in Craft.** Document 26983 (Stearns letter). Articles 853 (Reynolds 16, "Golden Dreams"), 1424 (Perkins, "3. The Placerita Gold Rush"), 12154 (Leon, "The real story of California's first gold discovery"), 12490, 15294 (Leon, "California's REAL First Gold"), 1434 (Perkins 1957).

**(d) Ties.** 18834 Francisco Lopez (body covers the find, the legend, the companions Cota and Bermudez); 309 Abel Stearns (handled and shipped the gold); 293 Ygnacio del Valle (timeline: mining district chairman; his Craft record does not say so); places 18565 Placerita Canyon and 18799 Placerita Canyon Nature Center, both empty stubs; 382 or 16446 Rancho San Francisco (the mining district). **Do not link** 28132 Francisco "Chico" López, a cousin, whom the grandnephew says not to confuse with the discoverer. No record for Manuel Cota, Domingo Bermudez, Adolfo Rivera, or the Oak.

**(e) Existing record.** None.

**(f) Sources.**
- Date: March 9, 1842 is the date California settled on, through Rivera and A.B. Perkins in 1930 (Leon's note on `belderrain1930.htm`); the timeline and Pollack give it. Stearns's 1867 letter gives the find without a day and the shipment as 22 November 1842. Warner and others (1876) and Murray (1892) say 1841; Guinn (1895) argues for 1842. Leon: "probably in 1842 (unless it was in 1841), and ... probably in Placerita Canyon (unless it was somewhere around Hasley Canyon)". Stearns names the place San Francisquito.
- **Disagreement inside Craft:** Stearns's record 309 says the gold was deposited at the mint on 8 July 1843; the mirror's voucher page and Pollack say June 8, 1843.
- The dream under the oak is legend (Pollack; Leon; Stearns "No Mention of Dream"; Lopez record).
- Valley effect: the first documented gold in California; the first mining district (May 3, 1842); some two thousand miners, most from Sonora (Lopez record's source).
- Landmark: the Oak, California Historical Landmark 168 (4 pages).

**(g) Verdict: full record now.** Fields: eventDate ("March 1842" with March 9 as the traditional day), eventDateEdtf (1842-03, with 1841 held in editorNotes), startEvidence (retrospective: every account is later), eventChlNumber (168), eventPersons (18834, 309, 293), eventPlaces (18565), eventArticles (853, 12154, 1424), recordTags (theme 18934), neighborhood (202 Placerita Canyon), culturalSensitivityNote not needed. The legend belongs in editorNotes, not the body.

---

## 6. The San Fernando Tunnel, 1876

**(a) Mirror pages.** 3 by title (LW2137a, LW2137b, `benblow1920.htm` on the later auto tunnel, not this). Dedicated text: `/scvhistory/pollack0710tunnel.html` (Pollack, "1876: Southern Pacific Tunnels Through", 2010) and Marie Harrington's `spike-harrington-i.htm` (1976). Other: OV1001 (entrance 1910), SW1902 (postcard), LW3094 (1952), `ripley14.htm`, `ripley15.htm`, Reynolds 38 to 40, timeline lines.

**(b) Photographs.** Mirror 5 or so, none of the 1876 work. Craft 2: 2859, 2861 (LW2137a, b, Carleton Watkins stereo views).

**(c) Craft articles.** 2101, 2103, 2105 (Reynolds 38 to 40), 1426 (Perkins, "Early Transportation"), mentions only.

**(d) Ties.** 934 Newhall Pass interchange (Pollack: the portal lies beneath the I-5 and Highway 14 interchange); 16101 Southern Pacific Railroad and 16388 Charles Crocker, both empty stubs. No record for Frank Frates, the superintendent.

**(e) Existing record.** None.

**(f) Sources.**
- Date: Harrington (1976), followed by Pollack, gives "two dates": the Chinese crews met face to face on **July 14, 1876**, half an inch out of line; or Frates finished it in **August 1876**, removing the last cart himself. Leon's LW2137a caption and the timeline: completed July 14, 1876; first train through August 12, 1876. Work began March 22, 1875.
- Length: 6,940 feet (Harrington, Leon, timeline); 6,966.5 feet (Pollack).
- Dead: "an unknown number gave up their lives" (Leon); "frequent cave-ins ... and the loss of life" (Harrington). No count anywhere.
- Workforce: about 1,000 Chinese and 500 other workers (Pollack, Harrington).
- Valley effect: the rail line from Los Angeles reached the valley; the first train into Newhall August 12, 1876 (timeline).

**(g) Verdict: short record.** The date is genuinely two-valued and the sources are few but sound. Fields: eventDate ("July 14 or August 1876"), eventDateEdtf (1876-07/1876-08), startEvidence (retrospective), eventDateStart (March 22, 1875), editorNotes (the two dates; the two lengths), eventPlaces (934), eventOrganizations (16101), recordImages (LW2137a, b), relatedEvents (the golden spike). Nathan's 14 July is one of the two versions, not settled.

---

## 7. The golden spike at Lang, 5 September 1876

**(a) Mirror pages.** 59 by title (category "Lang"), about 14 documents and pages and about 45 photo pages, many of the station rather than the event. Key: `golden-spike-centennial-best.htm` (Gerald M. Best, 1976, with Leon's note "About Lang Station and the Wedding of the Rails"), `golden-spike-centennial-louie.htm`, `golden-spike-centennial-index.htm`, `chssc_presskit_lang19760905.htm` (Chinese Historical Society, 1976), `spike-harrington-index.htm` and parts I to IV and bibliography (Harrington, *A Golden Spike*, 1976), `pollack0910lang.html`, `lang-090501-index.htm` and `lang-090501-eu.htm` (2001, 125th), `sg090601c.htm`, hub `lang.htm`, timeline line.

**(b) Photographs.** Leon: at Lang "nobody thought to bring a camera", so no photograph of the 1876 event exists in the archive. Mirror: 1926 50th-anniversary reenactment (HS2601, HS2602, SM2601, US31101a to i, US33025, US36257, US36748 to US36753: about 19); 1976 centennial (TO7601 gallery, JK0010, LW3251); 1996 plate (LW2057a); LW2726 map of 9-9-1876; LW2248a gallery. Craft: 3023 (LW2248, "Photo Gallery: 1876 Golden Spike"), 4439 (LW2726), 5069 (LW3251), 2751 (LW2057a); station views 3893, 5421, 4071 to 4087 (LW2545a to i).

**(c) Craft articles.** 2103 (Reynolds 39, "Ribbons of Steel"), 2105 (40, "A Town is Born"), 2091 (33), 1444 (Perkins, "Tales of Lang and Soledad"), 12286.

**(d) Ties.** 609 Lang Station (body: the spike driven September 5, 1876; Crocker, a silver mallet, a spike from San Gabriel Mountains ore); 18820 John Lang (his homestead); 16388 Charles Crocker (empty stub); 16101 Southern Pacific Railroad (empty stub); 18529 Soledad Canyon.

**(e) Existing record.** None.

**(f) Sources.** All give September 5, 1876. Leon's note: track from north and south met on John Lang's homestead; of about 4,000 workers at least 3,000 were Chinese, ordered aside for the last 1,000 feet so Caucasian men could lay it before the dignitaries; Crocker drove a gold spike with a silver hammer. No deaths at the ceremony. Valley effect: Los Angeles joined to San Francisco through the valley; Newhall station opened September 6, 1876 (timeline); the town of Newhall followed (Reynolds 40). Landmark: Lang Station, California Historical Landmark 590 (13 pages).

**(g) Verdict: full record now.** Fields: eventDate (September 5, 1876), startEvidence (contemporary), eventChlNumber (590), eventPlaces (609, 18529), eventPersons (16388, 18820), eventOrganizations (16101), eventArticles (2103, 2105, 1444), recordImages (the 1926 and 1976 commemorations, captioned as such), eventRecurring is not apt (the anniversaries are events of their own), relatedEvents (tunnel), recordTags (theme 18932), historicalEra (161). Literature to point at: Harrington 1976; Best 1976.

---

## 8. The Sand Fire, 2016

**(a) Mirror pages.** None of its own. Mentions in 6: COC annual report 2017 (`files/cocannualreport2017`, p. 6: "the 38,000-acre Sand Fire in July 2016", COC a staging area); the City's Fall-Winter 2016 newsletter (`sc1602.htm`, pp. 3, 5); `mrca_robinsnest_2018.htm` (the fire burned most of the property); `obituary_armintaguthrie.htm` (the Lang depot site "near the flashpoint"); a navigation thumbnail "Sable Ranch in Sand Fire, July 2016" on `lw2935.htm` and `wayoutwest1937.htm` that links out to scvtv.com, not held. The timeline has no Sand Fire line.

**(b) Photographs.** None in the mirror or in Craft.

**(c) to (e).** Nothing in Craft.

**(f) Sources.** July 2016; 38,000 acres (COC). No archive source gives a start date, deaths, or homes lost. [General knowledge, not checked: it began on 22 July 2016 near Sand Canyon and killed one person.]

**(g) Verdict: a line**, in the timeline, until sources are gathered.

---

## 9. The Tick Fire, 2019

**(a) Mirror pages.** One timeline line: "October 24: 4,615-acre Tick (Canyon) Fire destroys 24 homes, 5 other structures in Canyon Country and Saugus." Two passing mentions in Los Angeles Times coverage of the Saugus High School shooting: `lat20191118shs.htm` ("started on Oct. 24 and burned several thousand acres and forced the evacuation of 40,000 Santa Clarita Valley residents") and `lat20191115shs.htm`.

**(b) Photographs.** None.

**(c) to (e).** Nothing in Craft.

**(f) Sources.** October 24, 2019; 4,615 acres; 24 homes and 5 other structures (timeline, no source given); 40,000 evacuated (Times). No archive source speaks of deaths.

**(g) Verdict: a line.** The timeline line already is one.

---

## 10. The Northridge earthquake, 17 January 1994 (record 875)

**(a) Mirror pages.** About 60 that are about it (136 title matches less the 1971, 1857 and 1893 earthquakes and stray matches):
- City of Santa Clarita papers, none in Craft: SC9402 (freeway overpass), SC9403 (mess inside City Hall), SC9404 (tent City Hall), **SC9405 (valley damage cost estimates, total $431 million, as of December 1994)**, **SC9406 (status report, January 18, 2:30 p.m.)**, **SC9407 (chronology of events, January 17 to February 5, 1994)**
- **The Signal's special section "Images 6.7"** (`sg_earthquake1994.htm`, 20 pages), not in Craft
- Videos: `outabout011714.htm` ("Santa Clarita Comes Together", 1994), `quake1995.htm` ("One Year Later")
- Rancho Camulos: RA9401 gallery (damage 1-21-1994), `getty1994earthquake_camulos.htm` (Getty reports 1994 and 1999), `poiranchocamulos.htm`
- `/gif/galleries/lw2810/` (demolition of 23918 Via Onda)
- Leon's column `signal/worden/old/lw011796.htm` (in Craft as article 12528)

**(b) Photographs.** Craft holds 30 of the event: 3175, 3177 (LW2302, LW2303, Mentryville Big House), 4457 (LW2749), 4847 (LW3049, the ramp where Officer Dean died), 5197 (LW3354), 5713 to 5719 (LW9401a to d, tent City Hall), 5721 to 5731 (LW9402 to LW9407), 5733 to 5761 (LW9410a to o). **None is linked to the event** (photoEvents is empty everywhere). Not in Craft: SC9402 to SC9407, RA9401, LW2810.

**(c) Documents and articles in Craft.** Article 12528 (Leon, "Northridge Earthquake: a day in the life") and articles 12652 and 12611 (cited in the footnotes) are not in eventArticles. Obituaries 28049 (Caravalho) and 28045 (Worden-Roberts) mention it.

**(d) Ties.** Linked and sourced: 934 Newhall Pass interchange (both directions), 2591 Patti Rasmussen, 29113 Jeri Seratti (KBET), 16347 CSUN (over $400 million in damage; the campus is outside the valley). Linked without a source: **380 Henry Mayo Newhall Memorial Hospital** points at 875 by orgEvents, but its record speaks only of the 1971 earthquake, and no mirror page ties the hospital to 1994. Sourced but not linked: 18726 George Pederson (footnote 8), 29780 Officer Clarence Wayne Dean (fallen officer, cannot be linked; see structural point 1), 29794 LAPD, 394 the City (tent City Hall, chronology), 631 Rancho Camulos (RA9401, Getty).

**(e) What record 875 holds, and what is wrong with it.**
- Body: five sentences, nine footnotes, every one to an archive photograph, article or person. Sound.
- **eventSignificance is unsourced and conflicts with the archive:** "57 deaths", "over $20 billion", "Henry Mayo Newhall Memorial Hospital sustained significant damage", "one of the costliest natural disasters". The timeline says 53 killed and $11 billion; no archive page supports the hospital claim. It should be rewritten from the sources or cleared.
- withheldBody (kept off the page) says "Sixty people died", 4:31 a.m., and City Hall repairs over $4.5 million; its source is not recorded.
- Time: the body says 4:30 a.m. (Leon's captions, 29 pages say 4:30); the city's chronology (SC9407) and The Signal's special section say **4:31 a.m.**; the timeline says 4:31. An editor's note is wanted.
- Dead in the valley: The Signal's special section: one person was killed on the local freeways, Officer Dean, "no other fatalities on local Highway 14-Interstate 5 freeways". The record does not say this.
- legacyKey `newhallpass`, legacyUrl `/scvhistory/newhallpass.htm`: that page is not in the mirror and is about the pass, not the earthquake. The same goes for the 1971 earthquake page the withheld body links.
- featuredImage, asset 61: filename "...david_butow_corbis_011794_public_domain". A Corbis photograph described as public domain is a rights question for Nathan.
- eventDateStart repeats eventDate. Empty: startEvidence (should be contemporary), editorNotes, eventPersons, eventOrganizations, eventArticles, relatedEvents (the 1971 earthquake, if it gets a record), recordImages, recordDocuments, recordTags (theme 18943), neighborhood, wikidataId, eventWikipediaUrl.

**(f) Sources.** January 17, 1994, 4:31 a.m. (city, Signal) or 4:30 (Leon). Valley dead: one (Dean). Region: 53 killed and $11 billion (timeline). Valley damage: $431 million (City, December 1994). Valley effect, beyond the body: City Hall into tents and trailers (SC9404, LW9401); the Mentryville Big House off its foundation (LW2302); Rancho Camulos damaged and repaired with Getty help; COC stadium reopened September 15, 1994 (timeline); the chronology of water, debris and FEMA advances (SC9407).

**(g) Verdict: the record stands; complete it.** Import SC9405, SC9406, SC9407 and the Signal section as documents; link the 30 photographs by photoEvents; add 12528, 12611, 12652 to eventArticles; add 394 and 18726; remove or source the Henry Mayo link; replace eventSignificance; add the 4:30 or 4:31 note and the valley's one death.

---

## Summary

| Event | Mirror pages (by title) | Photos: mirror / Craft | Craft docs and articles | Verdict |
|---|---|---|---|---|
| St. Francis Dam, 1928 | 353, plus about 200 by caption | about 412 / 1 | 0 docs; 4 articles | Full record now |
| Newhall Incident, 1970 | 24 | 17 / 2 | 0; 4 officer records | Full record now (brief) |
| Kuredjian, 2001 | 2 | 2 / 2 | 1 article; officer record | A line |
| Cityhood, 1987 | 26, plus Boyer's book (32) | 18 / 15 | 7 docs, 1 obituary, about 6 articles, 1 election | Full record now |
| Placerita gold, 1842 | about 57 relevant | about 25 / 8 | 1 doc, 6 articles | Full record now |
| San Fernando Tunnel, 1876 | 3, plus 2 dedicated texts | about 5 / 2 | mentions only | Short record |
| Golden spike, 1876 | 59 | about 45 (none of 1876) / 4, plus 11 of the station | about 5 articles | Full record now |
| Sand Fire, 2016 | 0 (6 mentions) | 0 / 0 | 0 | A line |
| Tick Fire, 2019 | 0 (timeline line, 2 mentions) | 0 / 0 | 0 | A line |
| Northridge earthquake, 1994 | about 60 | about 40 / 30 (none linked) | 3 articles cited, none linked | Exists; complete it |

Corrections found along the way, for Nathan: election 21948 marks Measure U as not carried; Stearns record 309 dates the mint deposit 8 July 1843 against the voucher's June 8; Northridge record 875's eventSignificance and its Henry Mayo link have no archive source.
