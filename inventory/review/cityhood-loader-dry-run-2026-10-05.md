# Santa Clarita Cityhood: the loader's dry run, 6 October 2026

Written by `scripts/import/create_cityhood_event_2026_10_05.php` (with `scripts/import/_event_from_draft_2026_10_06.php`) in a dry run. A dry run writes nothing to Craft. The event is read from `inventory/review/cityhood-draft-v2-2026-10-05.json` (SHA-256 `e2bcc5e6d729af9a...`), the v2 draft whose quotations were rechecked on 6 October 2026. Nathan approved the draft for applying on 6 October 2026.

**Refusals:** none.

## The run

```
DRY RUN create_cityhood_event_2026_10_05.php
==============================================================================
EVENT: #31359 "Santa Clarita Cityhood" exists, not recreated
    content advisory: none (the draft has none)
    historicalEra: #168 Cityhood Era (1987–1993)
    historicalPeriod: #181 1980-1989
    recordTags: #18929 Incorporation & Cityhood
    neighborhood: #199 Newhall; #205 Saugus; #211 Valencia; #190 Canyon Country
    eventPersons: #16418 Connie Worden (notes 8, 9, 10); #15808 Carl Boyer (notes 2, 3, 17); #18791 Buck McKeon (notes 2, 5); #15737 Jan Heidt (notes 2); #16140 Jo Anne Darcy (notes 2); #23081 Dennis Koontz (notes 2)
    eventPlaces: none
    eventOrganizations: #394 The City of Santa Clarita (notes 1, 2, 16); #396 Santa Clarita Valley Chamber of Commerce (notes 6); #28275 Los Angeles County Board of Supervisors (notes 14, 15)
    eventFallenOfficers: none
    eventArticles: #12368 Brightly burns the flame of Cityhood (note 18); #12320 Roads: The broken promise of Cityhood (note 18); #12388 Out, brief candle of Cityhood (note 18)
    articles held, no footnote names them: #2165 70. Birth of a City (Reynolds, 1998 edition)
    sourceDocuments: #21939 General Municipal Elections: Historical Election Results, 1987 to 2012 (note 2, editor note 1); #28281 A Brief History of the Push for Self-Government in Santa Clarita (note 3, note 8); #28295 City Backers Join Prison Furor (Karina Lutz, The Signal, November 1, 1985) (note 9); #28293 Cityhood forum announced, The Signal, January 11, 1987 (note 9); #28287 City Formation Committee Member Connie Worden-Roberts Remembers, 2007 (note 10); #28305 Connie Worden Roberts, City Co-Founder (Perry Smith, KHTS, August 12, 2014) (note 10); #28310 Cityhood Backers: Who Are They? (Laurel Suomisto, The Signal, January 4, 1987) (note 15)
        the event already has [21939,28281,28295,28293,28287,28305,28310]; not changed
    cited records that are not documents (not in sourceDocuments): #394 organizations "The City of Santa Clarita"; #4385 photographs "City of Santa Clarita Formation Committee Logo 1987"; #21948 elections "City Council election, November 3, 1987"; #18791 persons "Buck McKeon"; #5701 photographs "Press Kit: City of Santa Clarita Feasibility Committee, 1985."; #5019 photographs "Application for the Incorporation of the City of 'Santa Clarita,' 12-17-1985."; #4861 photographs "City Formation Committee Kicks Off Voter Registration Program to Get More Funds From State, 12-1-1987."; #4879 photographs "Storyboard for Cityhood Campaign TV Commercial, 1987."; #4859 photographs "Arthur Young CPAs Predict 22% Budget Windfall for Proposed City of Santa Clarita, 9-18-1987."; #4255 photographs "Cityhood Petition No. 1"; #5703 photographs "Boundary Map of Proposed 90-Square-Mile City, 1-2-1986"; #4877 photographs "The Case for Cityhood: Formation Committee Addresses LAFCO, 2-25-1987."; #5401 photographs "Santa Clarita: The Book by Carl Boyer (Story, 2005)."; #12368 articles "Brightly burns the flame of Cityhood"; #12320 articles "Roads: The broken promise of Cityhood"; #12388 articles "Out, brief candle of Cityhood"
    featuredImage: asset 29678 (bw8702_orig.jpg)
    photograph #5701 LW8501: photoEvents has it (named in note 6)
    photograph #5019 LW3197: photoEvents has it (named in note 7)
    photograph #5703 LW8602: photoEvents has it (named in note 12)
    photograph #4255 LW2612: photoEvents has it (named in note 12)
    photograph #4385 LW2691: photoEvents has it (named in note 1, 15)
    photograph #4877 LW3071: photoEvents has it (named in note 13)
    photograph #4859 LW3059: photoEvents has it (named in note 11)
    photograph #4879 LW3072: photoEvents has it (named in note 11)
    photograph #4861 LW3060: photoEvents has it (named in note 9)
    photograph #29679 BW8702: HELD, footnote 2 does not name it
    photograph #29676 SC8801: HELD, no footnote cites it (the draft says so)
    photograph #4261 LW2614: HELD, no footnote cites it (the draft says so)
    photograph #4875 LW3070: HELD, no footnote cites it (the draft says so)
    photograph #4967 LW3149: HELD, no footnote cites it (the draft says so)
    photograph #5763 LW9502: HELD, no footnote cites it (the draft says so)
    other records: nothing written
    REFUSED: none
```

## Quotation check

87 quotations in the v2 draft (every quoted passage, in double or single quotation marks, in the fields (body, significance, footnotes, editor notes, recordDates labels, relation ties, photograph rows, image notes, the literature list) and the research leads; forNathan is not loaded and was not rechecked) were checked word for word: 82 PASS, 5 CORRECTED from v1, 0 FAIL. Ellipses: v2 has none. Checked against: mirror pages on /Volumes/Reggie/SCVHistory/scvhistory.com (HTML read as latin-1; PDFs by pdftotext), and Craft for record text, titles and the asset title, by read-only queries (storage/runtime/ev4/craft.json).

"Terminal punctuation only" means the quotation stops where the source's sentence goes on and closes with a period or comma of its own; no word is changed.

| # | Where | Quotation | Result | Page | Note |
| --- | --- | --- | --- | --- | --- |
| 1 | body | Buck | PASS | /scvhistory/boyer2015ch06.pdf |  |
| 2 | body | The inadequacy of infrastructure was an important component in selling 'city formation,' | PASS | /scvhistory/cw9901.htm; document #28281 | the source's inner double quotation marks become single; in the body the closing period becomes a comma before "she wrote" |
| 3 | body | Every year millions of dollars are leaving this valley, money that is needed to build roads and redevelop communities. | PASS | /scvhistory/lw3072.htm |  |
| 4 | body | city formation, | PASS | /scvhistory/boyer2015ch08.pdf |  |
| 5 | footnotes[0] | On December 15, 1987, the City of Santa Clarita was incorporated. | PASS | craft #394 (note 1) | the source quotation ends without the period; terminal punctuation only |
| 6 | footnotes[0] | A Premiere Evening: The Historic Inauguration of the City Council, | PASS | /scvhistory/bw8703.htm | a title; the page prints "A Premiere Evening. The Historic Inauguration of the City Council." The colon is citation style |
| 7 | footnotes[0] | Ever since Newhall started in 1876 and Saugus in 1887 (and actually since 1850), local municipal services were provided by the County of Los [cut here for the table] | PASS | /scvhistory/lw2691.htm |  |
| 8 | footnotes[1] | General Municipal Elections: Historical Election Results, 1987 to 2012, | PASS | craft #21948 |  |
| 9 | footnotes[1] | Proposition U, Incorporation of City of Santa Clarita, Yes 14,723, No 6,597; Proposition V, Council Elected by District or at Large, By Dist [cut here for the table] | CORRECTED | document #21939 (historical-results-7.pdf, p. of 1987) | v2 change: The City Clerk's table (inventory/elections/historical-results-7.pdf, document #21939; record #394's note 3 says the same) prints the at-large total "11.166". A quotation keeps the source's figure; th |
| 10 | footnotes[2] | Santa Clarita: The Formation and Organization of the Largest Newly Incorporated City in the History of Humankind, | PASS | craft #5401 |  |
| 11 | footnotes[2] | Let Pete Keep His Horses, | PASS | /scvhistory/boyer2015ch08.pdf |  |
| 12 | footnotes[2] | Clyde Smyth announced, 'At 4:30 this afternoon we became a city.' That was when Ruth Benell filed the necessary papers. | PASS | /scvhistory/boyer2015ch08.pdf | Boyer's inner double quotation marks become single inside the quotation |
| 13 | footnotes[2] | officially became a city at 4:30 p.m. the same day. | PASS | /scvhistory/bw8703.htm | the page continues "-- December 15, 1987."; terminal punctuation only |
| 14 | footnotes[2] | At 4:30 this afternoon we became a city. | PASS | /scvhistory/boyer2015ch08.pdf |  |
| 15 | footnotes[3] | Santa Clarita: The Formation and Organization of the Largest Newly Incorporated City in the History of Humankind, | PASS | craft #5401 |  |
| 16 | footnotes[3] | The gymnasium was full with about 2,000 people, according to the Los Angeles Times | PASS | /scvhistory/boyer2015ch08.pdf |  |
| 17 | footnotes[3] | Appellate Court Judge Roger W. Boren, who had been a municipal judge from our valley and prosecutor in the Hillside Strangler case, administ [cut here for the table] | CORRECTED | /scvhistory/boyer2015ch08.pdf | v2 change: The ellipsis cut a relative clause out of Boyer's sentence. No ellipsis survives v2; the sentence is quoted whole. |
| 18 | footnotes[3] | Allan Cameron had advised me that he had heard a developer calling his partner during the recess, telling him to get a crew out in the morni [cut here for the table] | PASS | /scvhistory/boyer2015ch08.pdf |  |
| 19 | footnotes[3] | We brought a proposed forty-five day moratorium on cutting down oak trees up as the first item of business. I moved it, and it passed unanim [cut here for the table] | CORRECTED | /scvhistory/boyer2015ch08.pdf (pp. 132-133; the page header "LET PETE KEEP HIS HORSES 133" falls inside the sentence in the PDF) | v2 change: Boyer's sentence ends "passed unanimously as Ordinance No. 1." The v1 quotation stopped short and closed with a period, dropping the words research lead 2 turns on. |
| 20 | footnotes[3] | in the College of the Canyons gymnasium, December 15, 1987 | PASS | /scvhistory/cc121587audio.htm |  |
| 21 | footnotes[4] | It was during the city council's first meeting that McKeon was chosen by his colleagues as Santa Clarita's first mayor. | PASS | craft #18791 (note 3) |  |
| 22 | footnotes[4] | confirmed as our first mayor | PASS | /scvhistory/boyer2015ch08.pdf |  |
| 23 | footnotes[5] | Press Kit: City of Santa Clarita Feasibility Committee, | PASS | /scvhistory/lw8501.htm |  |
| 24 | footnotes[5] | The City Feasibility Committee / Canyon Country Chamber of Commerce / Santa Clarita Valley Chamber of Commerce | PASS | /scvhistory/lw8501.htm | the letterhead's lines are marked with slashes |
| 25 | footnotes[5] | at their request, the Joint Chambers of Commerce have formed a committee to study incorporation. | PASS | /scvhistory/lw8501.htm |  |
| 26 | footnotes[5] | Connie Worden / Public Affairs, Spokesperson. | CORRECTED | /scvhistory/lw8501.htm | v2 change: The press kit prints the contact on two lines, "Connie Worden" and "Public Affairs, Spokesperson" (a <br> on the page); v1 joined them with a comma that is not in the source. |
| 27 | footnotes[6] | Application for the Incorporation of the City of 'Santa Clarita,' | PASS | /scvhistory/lw3197.htm |  |
| 28 | footnotes[6] | Santa Clarita, | PASS | /scvhistory/lw3197.htm |  |
| 29 | footnotes[7] | A Brief History of the Push for Self-Government in Santa Clarita, | PASS | /scvhistory/cw9901.htm |  |
| 30 | footnotes[7] | Once again, petitions were circulated and more than 25 percent of the signatures of registered voters were obtained. This time, house-to-hou [cut here for the table] | PASS | /scvhistory/cw9901.htm |  |
| 31 | footnotes[7] | The inadequacy of infrastructure was an important component in selling 'city formation.' | PASS | /scvhistory/cw9901.htm; document #28281 | the source's inner double quotation marks become single; in the body the closing period becomes a comma before "she wrote" |
| 32 | footnotes[7] | city formation. | PASS | /scvhistory/cw9901.htm |  |
| 33 | footnotes[8] | City Backers Join Prison Furor, | PASS | craft #28295 |  |
| 34 | footnotes[8] | Connie Worden, spokesman for the Cityhood Feasibility Subcommittee of the Canyon Country and Santa Clarita Valley chambers of commerce. | PASS | craft #28295 |  |
| 35 | footnotes[8] | Cityhood forum announced, | PASS | craft #28293 |  |
| 36 | footnotes[8] | cityhood spokesman Connie Worden. | PASS | document #28293 | the source continues ", referring"; terminal punctuation only |
| 37 | footnotes[8] | according to Connie Worden, Vice Chairman of the City Formation Committee. | PASS | /scvhistory/lw3060.htm | the source continues ", the response"; terminal punctuation only |
| 38 | footnotes[9] | City Formation Committee Member Connie Worden-Roberts Remembers, | PASS | craft #28287 |  |
| 39 | footnotes[9] | City of Santa Clarita 1987-2007 | PASS | craft #28287 |  |
| 40 | footnotes[9] | We actually collected much more than that, well in excess of 24,000 signatures. I personally collected 2,000 of them by standing in front of [cut here for the table] | PASS | craft #28287 |  |
| 41 | footnotes[9] | Worden-Roberts personally gathered 2,000 of the 24,000 signatures needed for the petition for cityhood. | PASS | craft #28305 |  |
| 42 | footnotes[10] | Storyboard for Cityhood Campaign TV Commercial, | PASS | /scvhistory/lw3072.htm |  |
| 43 | footnotes[10] | Get Control | PASS | /scvhistory/lw3072.htm |  |
| 44 | footnotes[10] | Every year millions of dollars are leaving this valley, money that is needed to build roads and redevelop communities. You can keep this tax [cut here for the table] | PASS | /scvhistory/lw3072.htm |  |
| 45 | footnotes[11] | Cityhood Petition No. 1, | PASS | /scvhistory/lw2612.htm |  |
| 46 | footnotes[11] | Petition No. 000001 for the incorporation of 90 square miles of the Santa Clarita Valley (including Castaic) as the City of Santa Clarita. | PASS | /scvhistory/lw2612.htm | the caption continues ", circulated by Lou Garasi, 1986."; terminal punctuation only |
| 47 | footnotes[11] | Boundary Map, Proposed City of Santa Clarita, | PASS | /scvhistory/lw8602.htm |  |
| 48 | footnotes[11] | scaled back the proposal to 39.5 square miles, removing areas west of Interstate 5, north of today's Copper Hill Drive, and east of State Ro [cut here for the table] | PASS | /scvhistory/lw8602.htm |  |
| 49 | footnotes[11] | The developers of planned subdivisions that did not yet exist -- Stevenson Ranch, Valencia-Westridge, Tesoro Del Valle, Fair Oaks Ranch and  [cut here for the table] | CORRECTED | /scvhistory/lw8602.htm | v2 change: The ellipsis stood for the list between Leon Worden's dashes. No ellipsis survives v2; the sentence is quoted whole, with the page's double hyphens. |
| 50 | footnotes[11] | LAFCO deleted Castaic from the map. | PASS | /scvhistory/lw8602.htm | the note continues "-- even though"; terminal punctuation only |
| 51 | footnotes[12] | The Case for Santa Clarita Cityhood, | PASS | /scvhistory/lw3071.htm |  |
| 52 | footnotes[12] | trimmed the proposal to 39.5 square miles, excizing [sic] the large swaths of raw land. The city of Santa Clarita has subsequently grown its [cut here for the table] | PASS | /scvhistory/lw3071.htm | "[sic]" is the draft's; otherwise as printed |
| 53 | footnotes[13] | Santa Clarita: The Formation and Organization of the Largest Newly Incorporated City in the History of Humankind, | PASS | craft #5401 |  |
| 54 | footnotes[13] | A Dixie Cup Instead of the Holy Grail, | PASS | /scvhistory/boyer2015book.htm |  |
| 55 | footnotes[13] | August 6 was the last meeting of the Board of Supervisors during which a vote could be taken to put us on the November ballot | PASS | /scvhistory/boyer2015ch06.pdf |  |
| 56 | footnotes[13] | The board voted three to two to put us on the ballot in November | PASS | /scvhistory/boyer2015ch06.pdf |  |
| 57 | footnotes[13] | We were not about to allow the county another five months of control over planning and zoning. | PASS | /scvhistory/boyer2015ch06.pdf | the sentence continues ", and did not want"; terminal punctuation only |
| 58 | footnotes[14] | the county is divided into five supervisorial districts, so SCV voters could participate in electing only one of the five people who made th [cut here for the table] | PASS | /scvhistory/lw2691.htm |  |
| 59 | footnotes[14] | Cityhood Backers: Who Are They? | PASS | craft #28310 |  |
| 60 | footnotes[14] | The committee's members cite the desire for local control, hearings held here at night, better planning and more consistent enforcement of l [cut here for the table] | PASS | craft #28310 |  |
| 61 | footnotes[15] | has never been part of the city | PASS | craft #394 |  |
| 62 | footnotes[16] | Santa Clarita: The Book by Carl Boyer, | PASS | craft #5401 |  |
| 63 | footnotes[16] | City of Santa Clarita 1987-2007: Celebrating 20 Years of Success | PASS | /scvhistory/sc19872007.htm |  |
| 64 | footnotes[16] | Santa Clarita: Fulfilling the Dream: The First 30 Years of Cityhood | PASS | /scvhistory/sc1803.htm | a title; the page prints "Santa Clarita: Fulfilling the Dream \| The First 30 Years of Cityhood" |
| 65 | footnotes[17] | Brightly burns the flame of Cityhood, | PASS | craft #12368 |  |
| 66 | footnotes[17] | Roads: The broken promise of Cityhood, | PASS | craft #12320 |  |
| 67 | footnotes[17] | Out, brief candle of Cityhood, | PASS | craft #12388 |  |
| 68 | editorNotes[1].note | Public Affairs, Spokesperson | PASS | /scvhistory/lw8501.htm |  |
| 69 | editorNotes[1].note | cityhood spokesman, | PASS | craft #28293 |  |
| 70 | editorNotes[1].note | Vice Chairman of the City Formation Committee, | PASS | /scvhistory/lw3060.htm |  |
| 71 | editorNotes[1].note | It was not until 1985 when Louis Garasi and I co-chaired the effort | PASS | /scvhistory/cw9901.htm |  |
| 72 | editorNotes[1].note | vice chairman of the City Formation Committee | PASS | /scvhistory/boyer2015ch07.pdf | Boyer, ch. 7: "On Oct. 23 Louis Garasi, who was then vice chairman of the City Formation Committee" |
| 73 | editorNotes[1].note | Cityhood Formation Committee Chairman | PASS | craft #28293 |  |
| 74 | editorNotes[1].note | co-chair of the cityhood committee | PASS | craft #28310 |  |
| 75 | editorNotes[2].note | a ninety square-mile area | PASS | craft #2165 |  |
| 76 | editorNotes[2].note | about 70 square miles | PASS | craft #28287 |  |
| 77 | editorNotes[2].note | just over thirty-nine square miles | PASS | craft #2165 |  |
| 78 | editorNotes[2].note | 39 square miles | PASS | craft #394 |  |
| 79 | photographs[3].title | Santa Clarita, | PASS | /scvhistory/boyer2015book.htm |  |
| 80 | photographs[11].tie | Get Control | PASS | /scvhistory/lw3072.htm |  |
| 81 | theLiterature[0].cite | Santa Clarita: The Formation and Organization of the Largest Newly Incorporated City in the History of Humankind, | PASS | craft #5401 |  |
| 82 | theLiterature[1].cite | City of Santa Clarita 1987-2007: Celebrating 20 Years of Success, | PASS | /scvhistory/sc19872007.htm |  |
| 83 | theLiterature[2].cite | Santa Clarita: Fulfilling the Dream: The First 30 Years of Cityhood, | PASS | /scvhistory/sc1803.htm | a title; the page prints "Santa Clarita: Fulfilling the Dream \| The First 30 Years of Cityhood" |
| 84 | theLiterature[3].cite | A Brief History of the Push for Self-Government in Santa Clarita, | PASS | /scvhistory/cw9901.htm |  |
| 85 | theLiterature[5].cite | Birth of a City | PASS | craft #2165 |  |
| 86 | researchLeads[0] | retrospective | PASS | craft #21948 |  |
| 87 | researchLeads[1] | Ordinance No. 1 | PASS | /scvhistory/boyer2015ch08.pdf |  |

## Changes from v1 to v2

1. **footnotes[1].** Was: By District 7,905, At Large 11,166" Now: By District 7,905, At Large 11.166" (so printed, for 11,166) Why: The City Clerk's table (inventory/elections/historical-results-7.pdf, document #21939; record #394's note 3 says the same) prints the at-large total "11.166". A quotation keeps the source's figure; the correction follows it, outside the quotation marks.
2. **footnotes[3].** Was: "Appellate Court Judge Roger W. Boren ... administered the oaths of office" Now: "Appellate Court Judge Roger W. Boren, who had been a municipal judge from our valley and prosecutor in the Hillside Strangler case, administered the oaths of office" Why: The ellipsis cut a relative clause out of Boyer's sentence. No ellipsis survives v2; the sentence is quoted whole.
3. **footnotes[3].** Was: I moved it, and it passed unanimously." Now: I moved it, and it passed unanimously as Ordinance No. 1." Why: Boyer's sentence ends "passed unanimously as Ordinance No. 1." The v1 quotation stopped short and closed with a period, dropping the words research lead 2 turns on.
4. **footnotes[5].** Was: "Connie Worden, Public Affairs, Spokesperson." Now: "Connie Worden / Public Affairs, Spokesperson." Why: The press kit prints the contact on two lines, "Connie Worden" and "Public Affairs, Spokesperson" (a <br> on the page); v1 joined them with a comma that is not in the source.
5. **footnotes[11].** Was: "The developers of planned subdivisions that did not yet exist ... opted out" Now: "The developers of planned subdivisions that did not yet exist -- Stevenson Ranch, Valencia-Westridge, Tesoro Del Valle, Fair Oaks Ranch and others in between -- opted out" Why: The ellipsis stood for the list between Leon Worden's dashes. No ellipsis survives v2; the sentence is quoted whole, with the page's double hyphens.
6. **recordDates.** Was: no ISO dates Now: iso on every row: 1985-10-14, 1985-12-17, 1986-01-02, 1987-02-25, 1987-08-06, 1987-11-03, 1987-12-15 Why: The calendar reads the ISO column. Each is the row's own printed date written as ISO; the bracketed years are the draft's own (the body and footnotes give them). confirmed is left unset: nothing in the draft confirms a row.

## The event as it would read

- **eventDate:** December 15, 1987
- **eventDateEdtf:** 1987-12-15
- **eventDateStart:** November 3, 1987
- **eventDateEnd:** December 15, 1987
- **startEvidence:** contemporary
- **eventChlNumber:** (empty)
- **eventSignificance:** The voters of Newhall, Saugus, Valencia and Canyon Country approved cityhood on November 3, 1987, and the City of Santa Clarita was incorporated on December 15, 1987, moving the government of the four communities from the Los Angeles County Board of Supervisors to a city council of their own.
- **historicalEra:** #168 Cityhood Era (1987–1993)
- **historicalPeriod:** #181 1980-1989
- **recordTags:** #18929 Incorporation & Cityhood
- **neighborhood:** #199 Newhall; #205 Saugus; #211 Valencia; #190 Canyon Country
- **featuredImage:** asset 29678 (bw8702_orig.jpg), BW8702, the first City Council-elect, November 1987 (already the image of photograph #29679)
- **bandImage:** (empty) asset 29678 if the band crop suits it (7628x5292); no wider image of the founding is in Craft

The City of Santa Clarita was incorporated on December 15, 1987. With it Newhall, Saugus, Valencia and Canyon Country, until then unincorporated territory served by the County of Los Angeles, became one city.[1] The voters of the proposed city had approved incorporation on November 3, 1987. Proposition U carried by 14,723 votes to 6,597; Proposition V, by 11,166 to 7,905, chose to elect the council at large rather than by district; and from twenty-six candidates the same ballot chose the first City Council: Howard P. "Buck" McKeon, Jan Heidt, Jo Anne Darcy, Carl Boyer III and Dennis Koontz.[2] The city came into being at 4:30 that afternoon, when Ruth Benell of the county's Local Agency Formation Commission filed the papers. That evening the council took its oaths before a crowd in the College of the Canyons gymnasium, and McKeon became the first mayor.[3][4][5]

The campaign began in 1985, when the Santa Clarita Valley and Canyon Country chambers of commerce formed a committee to study cityhood; it applied to the Local Agency Formation Commission for incorporation on December 17, 1985.[6][7] Its volunteers, the City Formation Committee, collected signatures house to house and then outside shopping centers and supermarkets. Connie Worden, its spokesman from 1985 and its vice chairman by the end, gathered 2,000 of them herself.[8][9][10] Their case was local control of growth, roads and money. "The inadequacy of infrastructure was an important component in selling 'city formation,'" she wrote in 1999, and the campaign's television commercial told voters, "Every year millions of dollars are leaving this valley, money that is needed to build roads and redevelop communities."[8][11]

The petitions asked for a city of 90 square miles, Castaic included. The commission cut it to under 40, leaving out Castaic, the land west of Interstate 5 and the large undeveloped tracts whose owners chose to stay in the county.[12][13] On August 6, 1987, the Board of Supervisors voted three to two to put the question on the November ballot.[14]

What the vote changed was who decided. Planning, zoning and roads in the four communities passed from the Board of Supervisors, on which the valley's voters chose one member of five, to a council of five elected by the city's own voters.[15][14] By Boyer's account the new council's first business, that first night, was a 45-day moratorium on cutting oak trees, moved when word came that a developer meant to clear his property the next morning.[4] Castaic and the other areas left out stayed with the county. The city has since annexed some of the excluded land as it was built, and its council was elected at large until 2024.[16][13]

The founding has its own literature. Carl Boyer, a chairman of the Formation Committee and one of the first council, wrote its history, Santa Clarita: The Formation and Organization of the Largest Newly Incorporated City in the History of Humankind (2005; second edition 2015). The City marked its twentieth year with City of Santa Clarita 1987-2007, and SCVTV its thirtieth with the documentary Santa Clarita: Fulfilling the Dream (2018). Connie Worden-Roberts set down her own account in 1999, and Leon Worden's columns of 1996 to 2002 weigh what the city made of its promises.[17][8][18]

1. The City of Santa Clarita, organization record #394 in this archive, and its source: City of Santa Clarita, Community Profile, "On December 15, 1987, the City of Santa Clarita was incorporated." "A Premiere Evening: The Historic Inauguration of the City Council," program book, City of Santa Clarita, College of the Canyons, December 15, 1987, BW8703, Bob Weber Collection, as carried on SCVHistory.com, /scvhistory/bw8703.htm. On the county: Leon Worden's note to LW2691, photograph #4385 in this archive (/scvhistory/lw2691.htm): "Ever since Newhall started in 1876 and Saugus in 1887 (and actually since 1850), local municipal services were provided by the County of Los Angeles."
2. City of Santa Clarita, City Clerk, "General Municipal Elections: Historical Election Results, 1987 to 2012," document #21939 in this archive, and the election record #21948, City Council election, November 3, 1987: "Proposition U, Incorporation of City of Santa Clarita, Yes 14,723, No 6,597; Proposition V, Council Elected by District or at Large, By District 7,905, At Large 11.166" (so printed, for 11,166); 21,918 ballots cast; 26 candidates; McKeon 9,855, Heidt 8,402, Darcy 7,601, Boyer 6,585, Koontz 6,164.
3. Carl Boyer 3rd, "Santa Clarita: The Formation and Organization of the Largest Newly Incorporated City in the History of Humankind," second edition, Santa Clarita 2015, chapter 8, "Let Pete Keep His Horses," p. 132, as carried on SCVHistory.com, /scvhistory/boyer2015ch08.pdf: "Clyde Smyth announced, 'At 4:30 this afternoon we became a city.' That was when Ruth Benell filed the necessary papers." Written by a participant, eighteen years later. Leon Worden's note to BW8703 (/scvhistory/bw8703.htm): the city "officially became a city at 4:30 p.m. the same day." Connie Worden-Roberts (document #28281) names Ruth Benell among the commission's staff the committee visited.
4. Carl Boyer 3rd, "Santa Clarita: The Formation and Organization of the Largest Newly Incorporated City in the History of Humankind," second edition, Santa Clarita 2015, chapter 8, pp. 132-133, as carried on SCVHistory.com, /scvhistory/boyer2015ch08.pdf: "The gymnasium was full with about 2,000 people, according to the Los Angeles Times"; "Appellate Court Judge Roger W. Boren, who had been a municipal judge from our valley and prosecutor in the Hillside Strangler case, administered the oaths of office"; "Allan Cameron had advised me that he had heard a developer calling his partner during the recess, telling him to get a crew out in the morning to cut down the oak trees on their property"; "We brought a proposed forty-five day moratorium on cutting down oak trees up as the first item of business. I moved it, and it passed unanimously as Ordinance No. 1." The audio recording of the meeting, by Bob Weber, places it "in the College of the Canyons gymnasium, December 15, 1987" (/scvhistory/cc121587audio.htm).
5. Buck McKeon, person record #18791 in this archive, footnote 3, quoting his own biography: "It was during the city council's first meeting that McKeon was chosen by his colleagues as Santa Clarita's first mayor." Boyer (chapter 8, p. 129) has McKeon "confirmed as our first mayor" at the council-elect's private meetings between the election and December 15.
6. "Press Kit: City of Santa Clarita Feasibility Committee," October 14, 1985, LW8501, photograph #5701 in this archive (/scvhistory/lw8501.htm): "The City Feasibility Committee / Canyon Country Chamber of Commerce / Santa Clarita Valley Chamber of Commerce"; "at their request, the Joint Chambers of Commerce have formed a committee to study incorporation." Contact: "Connie Worden / Public Affairs, Spokesperson."
7. "Application for the Incorporation of the City of 'Santa Clarita,'" City of Santa Clarita City Feasibility Committee to the Local Agency Formation Commission, County of Los Angeles, December 17, 1985, LW3197, photograph #5019 in this archive (/scvhistory/lw3197.htm). Collection of Connie Worden-Roberts.
8. Connie Worden-Roberts, "A Brief History of the Push for Self-Government in Santa Clarita," January 18, 1999, document #28281 in this archive (/scvhistory/cw9901.htm): "Once again, petitions were circulated and more than 25 percent of the signatures of registered voters were obtained. This time, house-to-house drives were augmented by signature gathering in shopping centers and supermarkets"; "The inadequacy of infrastructure was an important component in selling 'city formation.'"
9. Karina Lutz, "City Backers Join Prison Furor," The Signal, November 1, 1985, document #28295 in this archive: "Connie Worden, spokesman for the Cityhood Feasibility Subcommittee of the Canyon Country and Santa Clarita Valley chambers of commerce." "Cityhood forum announced," The Signal, January 11, 1987, document #28293: "cityhood spokesman Connie Worden." Draft press release, City Formation Committee, December 1, 1987, LW3060, photograph #4861 in this archive: "according to Connie Worden, Vice Chairman of the City Formation Committee." Her other titles in the sources are set out in the note below.
10. Connie Worden-Roberts, "City Formation Committee Member Connie Worden-Roberts Remembers," in City of Santa Clarita, "City of Santa Clarita 1987-2007" (2007), p. 67, document #28287 in this archive: "We actually collected much more than that, well in excess of 24,000 signatures. I personally collected 2,000 of them by standing in front of grocery stores and asking people (for their signature)." Perry Smith, KHTS, August 12, 2014, document #28305: "Worden-Roberts personally gathered 2,000 of the 24,000 signatures needed for the petition for cityhood."
11. "Storyboard for Cityhood Campaign TV Commercial," City of Santa Clarita Formation Committee, 1987, LW3072, photograph #4879 in this archive (/scvhistory/lw3072.htm), the commercial "Get Control": "Every year millions of dollars are leaving this valley, money that is needed to build roads and redevelop communities. You can keep this tax surplus here in the Santa Clarita Valley by voting yes on cityhood." The committee's accountants, Arthur Young, put the surplus at $3.5 million a year (LW3059, photograph #4859, letter of September 18, 1987, as Leon Worden summarizes it).
12. "Cityhood Petition No. 1," 1986, LW2612, photograph #4255 in this archive (/scvhistory/lw2612.htm): "Petition No. 000001 for the incorporation of 90 square miles of the Santa Clarita Valley (including Castaic) as the City of Santa Clarita." "Boundary Map, Proposed City of Santa Clarita," January 2, 1986, LW8602, photograph #5703 (/scvhistory/lw8602.htm), Leon Worden's note: the commission "scaled back the proposal to 39.5 square miles, removing areas west of Interstate 5, north of today's Copper Hill Drive, and east of State Route 14 (but leaving in the developed parts of Sand Canyon)"; "The developers of planned subdivisions that did not yet exist -- Stevenson Ranch, Valencia-Westridge, Tesoro Del Valle, Fair Oaks Ranch and others in between -- opted out"; "LAFCO deleted Castaic from the map."
13. "The Case for Santa Clarita Cityhood," the City of Santa Clarita Formation Committee's presentation to the Local Agency Formation Commission, February 25, 1987, LW3071, photograph #4877 in this archive (/scvhistory/lw3071.htm), Leon Worden's note: the commission "trimmed the proposal to 39.5 square miles, excizing [sic] the large swaths of raw land. The city of Santa Clarita has subsequently grown its boundaries through LAFCO-approved annexations of some of those areas after they were developed." The figures for the area are set out in the note below.
14. Carl Boyer 3rd, "Santa Clarita: The Formation and Organization of the Largest Newly Incorporated City in the History of Humankind," second edition, Santa Clarita 2015, chapter 6, "A Dixie Cup Instead of the Holy Grail," pp. 100-103, as carried on SCVHistory.com, /scvhistory/boyer2015ch06.pdf: "August 6 was the last meeting of the Board of Supervisors during which a vote could be taken to put us on the November ballot"; "The board voted three to two to put us on the ballot in November"; and, on the urgency, "We were not about to allow the county another five months of control over planning and zoning."
15. Leon Worden's note to LW2691, photograph #4385 in this archive (/scvhistory/lw2691.htm): "the county is divided into five supervisorial districts, so SCV voters could participate in electing only one of the five people who made the decisions affecting our valley." "Cityhood Backers: Who Are They?" The Signal, January 4, 1987, document #28310: "The committee's members cite the desire for local control, hearings held here at night, better planning and more consistent enforcement of land plans as the major reasons for forming a city."
16. The City of Santa Clarita, organization record #394 in this archive, and its sources: Castaic "has never been part of the city"; forty-one annexations since 1987; the council elected at large until district elections began in 2024 (Ordinance No. 23-4, June 13, 2023).
17. Carl Boyer's book: photograph #5401 in this archive, "Santa Clarita: The Book by Carl Boyer," December 2005, and its caption; the second edition (2015) as carried on SCVHistory.com, /scvhistory/boyer2015book.htm, in chapters as PDFs. City of Santa Clarita, "City of Santa Clarita 1987-2007: Celebrating 20 Years of Success" (2007), /scvhistory/sc19872007.htm. SCVTV, "Santa Clarita: Fulfilling the Dream: The First 30 Years of Cityhood" (2018), SC1803, /scvhistory/sc1803.htm.
18. Leon Worden, "Brightly burns the flame of Cityhood," May 1, 1996 (article #12368); "Roads: The broken promise of Cityhood," December 2, 1998 (article #12320); "Out, brief candle of Cityhood," April 10, 2002 (article #12388); all The Signal, in this archive.

**Editor's note, The count (bottom):** The City Clerk's returns give Proposition U 14,723 to 6,597, Proposition V 11,166 at large to 7,905 by district, and 21,918 ballots (document #21939). Earlier counts, as Carl Boyer quotes them: The Signal on election night, with thirty of thirty-eight precincts counted, had cityhood at 67.15 percent and at-large elections ahead 8,110 to 5,874; the Daily News two days later, with all thirty-eight precincts, had 14,416 to 6,474 (69 percent) and 10,919 to 7,732, and McKeon 9,657, Heidt 8,198, Darcy 7,441, Boyer 6,430 and Koontz 6,052, against the final 9,855, 8,402, 7,601, 6,585 and 6,164 (Boyer 2015, chapter 7, pp. 122-123). The 1998 edition of Jerry Reynolds's History of the Santa Clarita Valley, chapter 70, gives the election-night 67.15 percent. The sources call the measure Proposition U.

**Editor's note, Who led the committee (bottom):** The sources differ, each as stated. Connie Worden: spokesman for the chambers' feasibility subcommittee, November 1985 (The Signal); 'Public Affairs, Spokesperson' on the October 1985 press kit; 'cityhood spokesman,' January 1987 (The Signal); 'Vice Chairman of the City Formation Committee,' December 1, 1987 (the committee's release); in her own account of 1999, 'It was not until 1985 when Louis Garasi and I co-chaired the effort'; vice chair throughout, in the 1998 Reynolds edition and her 2014 obituary. Louis Garasi: chairman (Reynolds 1998), co-chair (Worden-Roberts 1999), 'vice chairman of the City Formation Committee' on October 23, 1987 (Boyer). Carl Boyer III: 'Cityhood Formation Committee Chairman' (The Signal, January 11, 1987), succeeded as chairman by Art Donnelly when he ran for the council (LW3071, LW9502, Reynolds 1998, Boyer). Jim Schutte: 'co-chair of the cityhood committee' (The Signal, January 4, 1987).

**Editor's note, The size of the city (bottom):** Proposed: 90 square miles, including Castaic (the petition, LW2612; the boundary map of January 2, 1986, LW8602; LW3071; Reynolds 1998, 'a ninety square-mile area'); 'about 70 square miles' in Connie Worden-Roberts's recollection of 2007. Approved: 39.5 square miles (Leon Worden's notes to LW8602, LW3071 and LW2691); 'just over thirty-nine square miles' (Reynolds 1998); '39 square miles' (Worden-Roberts 2007); 39.79 square miles in the City's table of annexations, as cited on record #394, which notes that 39.5 has not been found in an official source.

### recordDates

| Printed | ISO | Precision | What happened | Confirmed |
| --- | --- | --- | --- | --- |
| October 14, 1985 | 1985-10-14 | day | the chambers' City Feasibility Committee announces its study | no |
| December 17, 1985 | 1985-12-17 | day | application for incorporation filed with the Local Agency Formation Commission | no |
| Jan. 2, 1986 | 1986-01-02 | day | boundary map of the proposed 90-square-mile city | no |
| February 25, 1987 | 1987-02-25 | day | the Formation Committee's presentation to LAFCO | no |
| August 6 [1987] | 1987-08-06 | day | Board of Supervisors votes three to two to put cityhood on the November ballot | no |
| November 3, 1987 | 1987-11-03 | day | Proposition U approved; first City Council elected | no |
| December 15, 1987 | 1987-12-15 | day | incorporation at 4:30 p.m.; first council meeting that evening | no |

### Relations

- **eventPersons:** #16418 Connie Worden (notes 8, 9, 10; spokesman and vice chairman of the campaign; gathered 2,000 signatures)
- **eventPersons:** #15808 Carl Boyer (notes 2, 3, 17; chairman of the Formation Committee until he ran; elected to the first council; the campaign's historian)
- **eventPersons:** #18791 Buck McKeon (notes 2, 5; elected first of twenty-six; first mayor)
- **eventPersons:** #15737 Jan Heidt (notes 2; elected to the first council)
- **eventPersons:** #16140 Jo Anne Darcy (notes 2; elected to the first council)
- **eventPersons:** #23081 Dennis Koontz (notes 2; elected to the first council)
- **eventOrganizations:** #394 The City of Santa Clarita (notes 1, 2, 16; the city incorporated)
- **eventOrganizations:** #396 Santa Clarita Valley Chamber of Commerce (notes 6; with the Canyon Country chamber, formed the feasibility committee of 1985; its record says its leaders prepared the initiative)
- **eventOrganizations:** #28275 Los Angeles County Board of Supervisors (notes 14, 15; voted three to two on August 6, 1987 to put cityhood on the ballot; governed the area until incorporation)
- **eventArticles:** #12368 Brightly burns the flame of Cityhood (named in note 18)
- **eventArticles:** #12320 Roads: The broken promise of Cityhood (named in note 18)
- **eventArticles:** #12388 Out, brief candle of Cityhood (named in note 18)
- **sourceDocuments:** #21939 General Municipal Elections: Historical Election Results, 1987 to 2012 (note 2, editor note 1)
- **sourceDocuments:** #28281 A Brief History of the Push for Self-Government in Santa Clarita (note 3, note 8)
- **sourceDocuments:** #28295 City Backers Join Prison Furor (Karina Lutz, The Signal, November 1, 1985) (note 9)
- **sourceDocuments:** #28293 Cityhood forum announced, The Signal, January 11, 1987 (note 9)
- **sourceDocuments:** #28287 City Formation Committee Member Connie Worden-Roberts Remembers, 2007 (note 10)
- **sourceDocuments:** #28305 Connie Worden Roberts, City Co-Founder (Perry Smith, KHTS, August 12, 2014) (note 10)
- **sourceDocuments:** #28310 Cityhood Backers: Who Are They? (Laurel Suomisto, The Signal, January 4, 1987) (note 15)
- **cited, not a document, not related:** #394 organizations "The City of Santa Clarita"
- **cited, not a document, not related:** #4385 photographs "City of Santa Clarita Formation Committee Logo 1987"
- **cited, not a document, not related:** #21948 elections "City Council election, November 3, 1987"
- **cited, not a document, not related:** #18791 persons "Buck McKeon"
- **cited, not a document, not related:** #5701 photographs "Press Kit: City of Santa Clarita Feasibility Committee, 1985."
- **cited, not a document, not related:** #5019 photographs "Application for the Incorporation of the City of 'Santa Clarita,' 12-17-1985."
- **cited, not a document, not related:** #4861 photographs "City Formation Committee Kicks Off Voter Registration Program to Get More Funds From State, 12-1-1987."
- **cited, not a document, not related:** #4879 photographs "Storyboard for Cityhood Campaign TV Commercial, 1987."
- **cited, not a document, not related:** #4859 photographs "Arthur Young CPAs Predict 22% Budget Windfall for Proposed City of Santa Clarita, 9-18-1987."
- **cited, not a document, not related:** #4255 photographs "Cityhood Petition No. 1"
- **cited, not a document, not related:** #5703 photographs "Boundary Map of Proposed 90-Square-Mile City, 1-2-1986"
- **cited, not a document, not related:** #4877 photographs "The Case for Cityhood: Formation Committee Addresses LAFCO, 2-25-1987."
- **cited, not a document, not related:** #5401 photographs "Santa Clarita: The Book by Carl Boyer (Story, 2005)."
- **cited, not a document, not related:** #12368 articles "Brightly burns the flame of Cityhood"
- **cited, not a document, not related:** #12320 articles "Roads: The broken promise of Cityhood"
- **cited, not a document, not related:** #12388 articles "Out, brief candle of Cityhood"
- **photograph #5701 LW8501** takes the event in photoEvents (named in note 6; the campaign's first announcement)
- **photograph #5019 LW3197** takes the event in photoEvents (named in note 7; the application to LAFCO)
- **photograph #5703 LW8602** takes the event in photoEvents (named in note 12; the boundary first proposed)
- **photograph #4255 LW2612** takes the event in photoEvents (named in note 12; the first petition, 1986)
- **photograph #4385 LW2691** takes the event in photoEvents (named in note 1, 15; the committee's letterhead; Leon Worden's note on why and by whom)
- **photograph #4877 LW3071** takes the event in photoEvents (named in note 13; the committee's presentation to LAFCO)
- **photograph #4859 LW3059** takes the event in photoEvents (named in note 11; the accountants' letter on the tax surplus)
- **photograph #4879 LW3072** takes the event in photoEvents (named in note 11; the campaign commercial 'Get Control')
- **photograph #4861 LW3060** takes the event in photoEvents (named in note 9; the committee's release between the vote and incorporation)
- **Held, photograph #29679 BW8702** "First City Council-Elect: Darcy, Boyer, Heidt, Koontz, McKeon; November 1987": footnote 2 does not name it. Set `$CFG['linkHeldPhotos'] = true` to link the held photographs too.
- **Held, photograph #29676 SC8801** "First Santa Clarita City Council, 1987-1990": no footnote cites it (the draft says so). Set `$CFG['linkHeldPhotos'] = true` to link the held photographs too.
- **Held, photograph #4261 LW2614** "Santa Clarita Valley City Formation Committee Membership-Donor Form, ~1985": no footnote cites it (the draft says so). Set `$CFG['linkHeldPhotos'] = true` to link the held photographs too.
- **Held, photograph #4875 LW3070** "Public Opinion Poll Results: Question of Santa Clarita Cityhood, February 1987.": no footnote cites it (the draft says so). Set `$CFG['linkHeldPhotos'] = true` to link the held photographs too.
- **Held, photograph #4967 LW3149** "1st City Council Members' New Year's Resolutions, SCV Magazine, Winter 1987-88.": no footnote cites it (the draft says so). Set `$CFG['linkHeldPhotos'] = true` to link the held photographs too.
- **Held, photograph #5763 LW9502** "Art & Glo Donnelly, Laurene Weste, 1995-96": no footnote cites it (the draft says so). Set `$CFG['linkHeldPhotos'] = true` to link the held photographs too.
- **Held, article #2165** 70. Birth of a City (Reynolds, 1998 edition) (about the event; reworked for the edition by another contributor; cited only in the editor notes): no footnote names it
- **relatedEvents:** none
- **footnotesOn:** not set. On this site it means "the notes were published on another record" (create_saugus_2019_event_2026_10_05.php); the documents the draft listed there go to sourceDocuments instead.
- **Not written:** every other record. The draft's forNathan recommendations about other records (new place or organization records, changes to person records, image attachments) are not acted on.

### Research leads (researchLeads, not shown on the page)

- The certificate of incorporation, the commission's resolution and the Board of Supervisors' resolutions (the ballot order of August 6, 1987 and the canvass of the November 3 result) are not in the archive. The City Clerk's results table (document #21939) is a compilation of 2012 or later; the election record #21948 rates seats 'retrospective'. The County's statement of votes for 1987 would let the count be rated from the canvass itself.
- Boyer's book is read here from the chapter PDFs of the 2015 edition (chapters 6 to 8). He is a participant; the 4:30 filing is corroborated by Leon Worden's note to BW8703, but the August 6 vote, the council-elect meetings and the oak moratorium rest on him (he cites the Los Angeles Times and The Signal in his notes). He also calls the moratorium the first item of business and then calls the ordinance adopting county law 'Ordinance No. 1'; which ordinance carried the number is a lead for the City Clerk's records.
- BW8703 (the inauguration program book), BW8704 (the campaign office) and SC1803 (the 2018 documentary) are on the mirror but not in Craft as records; only their thumbnails are attached to article #2165. The audio of the first meeting (cc121587audio) and the slide show (cc121587slideshow) are on the mirror too.
- The City's 2007 book (sc19872007) was read only for Connie Worden-Roberts's page; its account of the founding was not read for this draft.
- LAFCO's final hearing (June 24, 1987, by Boyer) and the earlier hearings of 1986 and early 1987 (Leon's note to LW8602) are not documented in the archive.
- Arthur Young's letter of September 18, 1987 (LW3059) was read only in Leon Worden's summary and opening lines; its figure for the surplus should be read from the letter before any number goes in the body.
- Laurene Weste's part in the Formation Committee rests only on her own biography of 2019 (record #15929); LW9502 calls her a parks commissioner. George Pederson's signature-gathering (1986) rests on one note of Leon's (LW2529).

## From the draft's forNathan (approved with the draft; listed for the record)

- 1. Election #21948 is already corrected: Measure U shows carried: true and its footnote cites document #21939. The events inventory's correction needs no action.
- 2. The sources call it Proposition U (the City Clerk's returns, Boyer, LW9502, Reynolds 1998), not Measure U. The draft says Proposition U.
- 3. Dates and evidence. Recommend eventDate December 15, 1987 (EDTF 1987-12-15), eventDateStart November 3, 1987 (the vote) and eventDateEnd December 15, 1987, with startEvidence 'contemporary', resting on the program book of December 15, 1987 (BW8703) and the committee's release of December 1. The inventory proposed 'certified'; the archive holds no certificate of incorporation, so 'certified' does not apply.
- 4. Connie Worden's title. The body calls her the campaign's 'spokesman from 1985 and its vice chairman by the end', which is what the documents of 1985-1987 say. Her own 1999 account says she co-chaired with Louis Garasi; the editor note shows every version. Approve, or say which to lead with.
- 5. Create an organization record for the Local Agency Formation Commission for the County of Los Angeles. It set the city's boundary in 1987 and has approved every annexation since (record #394 counts forty-one), so its role recurs. The body names it in plain words; it could then go in eventOrganizations. Art Donnelly (the committee's last chairman; photographs 5705, 5709, 5763) and Louis Garasi (chairman or co-chair, by source) have no person records; they are the next candidates.
- 6. Not linked, deliberately: Jill Klajic #15874 (committee secretary by LW2614, and a backer quoted in The Signal in January 1987, but her role in this event is minor beside her later council career; say if she should be linked); Louis Brathwaite #28703 (a steering-committee member in The Signal of January 4, 1987; first Planning Commission); Laurene Weste #15929 and George Pederson #18726 (see researchLeads); Michael D. Antonovich #29284 (Boyer's ally on the board; the board itself is linked); Dan Hon #18616 and Ruth Newhall #15477 (Canyon County, 1976-78, a separate effort); Reynolds chapter 69 #2163 and Leon's 1997 column #12426 (both on Canyon County). Canyon County is the obvious related event when one is made.
- 7. 'Under 40' square miles. The body avoids choosing among 39, 39.5, 'just over thirty-nine' and 39.79; the note lists them. Say if you want a figure in the body; the City's 39.79 (record #394) is the only one from an official table.
- 8. Images. featuredImage asset 29678 (BW8702) is ready. Seven of the fifteen photograph records have full-size files in archiveMedia that are not attached (LW8501, LW8602, LW2612, LW2614, LW2691, LW9502), and five have no file at all (LW3060, LW3070, LW3071, LW3072, LW3149). Attaching is a separate fix; say if you want it.
- 9. Boyer's superlative ('the largest newly incorporated city in the history of humankind', also Reynolds 1998's 'largest city ever to incorporate') appears only as his book's title, not as a claim.
