# Golden Spike at Lang Station: the loader's dry run, 6 October 2026

Written by `scripts/import/create_golden_spike_event_2026_10_05.php` (with `scripts/import/_event_from_draft_2026_10_06.php`) in an apply. A dry run writes nothing to Craft. The event is read from `inventory/review/golden-spike-draft-v2-2026-10-05.json` (SHA-256 `a52a0c89a8ecbd55...`), the v2 draft whose quotations were rechecked on 6 October 2026. Nathan approved the draft for applying on 6 October 2026.

**Refusals:** none.

## The run

```
APPLYING create_golden_spike_event_2026_10_05.php
==============================================================================
EVENT: #31374 "Golden Spike at Lang Station" exists, not recreated
    content advisory: none (the draft has none)
    historicalEra: #161 Railroad & Oil Era (1876–1909)
    historicalPeriod: #173 1850-1899
    recordTags: #18932 Railroad
    neighborhood: #207 Soledad Canyon
    eventPersons: #16388 Charles Crocker (notes 3, 4); #18820 John Lang (notes 1)
    eventPlaces: #609 Lang Station (notes 1, 4); #18529 Soledad Canyon (notes 1, 7)
    eventOrganizations: #16101 Southern Pacific Railroad (notes 2, 4); #15493 Santa Clarita Valley Historical Society (notes 10, 11); #392 Historical Society of Southern California (notes 4)
    eventFallenOfficers: none
    eventArticles: #2105 40. A Town is Born (note 8)
    articles held, no footnote names them: #2103 39. Ribbons of Steel (Reynolds, 1998 edition); #12286 Future Destination: Historic Santa Clarita Valley (Leon Worden, 2009)
    sourceDocuments: none cited
        nothing to set
    cited records that are not documents (not in sourceDocuments): #4439 photographs "Southern Pacific Connects with Central Pacific, Map 9-9-1876."; #2751 photographs "Commemorative Plate: Lang Station Golden Spike 120th Anniversary 1996."; #2105 articles "40. A Town is Born"; #3023 photographs "Photo Gallery: 1876 Golden Spike."; #1426 articles "4. Early Transportation"
    featuredImage: asset 1193 (lang.jpg)
    photograph #3023 LW2248a: photoEvents append the event to [] (named in note 13)
    photograph #4439 LW2726: photoEvents append the event to [] (named in note 2)
    photograph #2751 LW2057: photoEvents append the event to [] (named in note 4)
    photograph #5069 LW3251: HELD, footnote 10, 11 does not name it
    other records: nothing written
    REFUSED: none
```

## Quotation check

70 quotations in the v2 draft (every quoted passage, in double or single quotation marks, in the fields (body, significance, footnotes, editor notes, recordDates labels, relation ties, photograph rows, image notes, the literature list) and the research leads; forNathan is not loaded and was not rechecked) were checked word for word: 70 PASS, 0 CORRECTED from v1, 0 FAIL. Ellipses: v2 has none. Checked against: mirror pages on /Volumes/Reggie/SCVHistory/scvhistory.com (HTML read as latin-1; PDFs by pdftotext), and Craft for record text, titles and the asset title, by read-only queries (storage/runtime/ev4/craft.json).

"Terminal punctuation only" means the quotation stops where the source's sentence goes on and closes with a period or comma of its own; no word is changed.

| # | Where | Quotation | Result | Page | Note |
| --- | --- | --- | --- | --- | --- |
| 1 | body | San Francisco and Los Angeles came together in Santa Clarita, | PASS | /scvhistory/lwhist.htm |  |
| 2 | body | over three thousand Chinese who helped build the Southern Pacific Railroad and the San Fernando Tunnel | PASS | /scvhistory/chssc_presskit_lang19760905.htm |  |
| 3 | body | Story of the Golden Spike at Lang Station | PASS | /scvhistory/golden-spike-centennial-best.htm |  |
| 4 | footnotes[0] | About Lang Station and the 'Wedding of the Rails,' | PASS | /scvhistory/golden-spike-centennial-best.htm | a heading; inner double quotation marks become single |
| 5 | footnotes[0] | Story of the Golden Spike at Lang Station, | PASS | /scvhistory/golden-spike-centennial-best.htm |  |
| 6 | footnotes[0] | Train tracks laid north out of Los Angeles and south out of San Francisco met on John Lang's homestead in Soledad Canyon and culminated in a [cut here for the table] | PASS | /scvhistory/golden-spike-centennial-best.htm | inner quotation marks become single; the sentence continues ", similar to"; terminal punctuation only |
| 7 | footnotes[0] | Out of a workforce of approximately 4,000 men, at least 3,000 were Chinese immigrants. | PASS | /scvhistory/golden-spike-centennial-best.htm |  |
| 8 | footnotes[0] | When the last 1,000 feet of track remained to be laid, the Chinese workers were ordered to stand aside so Caucasian men could complete the t [cut here for the table] | PASS | /scvhistory/golden-spike-centennial-best.htm | continues "where Charles Crocker hammered"; terminal punctuation only |
| 9 | footnotes[0] | (Ironically, nobody thought to bring a camera.) | PASS | /scvhistory/golden-spike-centennial-best.htm |  |
| 10 | footnotes[0] | Wedding of the Rails, | PASS | /scvhistory/golden-spike-centennial-best.htm |  |
| 11 | footnotes[0] | golden spike | PASS | /scvhistory/golden-spike-centennial-best.htm |  |
| 12 | footnotes[1] | Story of the Golden Spike at Lang Station, | PASS | /scvhistory/golden-spike-centennial-best.htm |  |
| 13 | footnotes[1] | The Southern Pacific announced that the rails would be joined on September 5, 1876 at a point called Lang, 43 miles from Los Angeles and 440 [cut here for the table] | PASS | /scvhistory/golden-spike-centennial-best.htm |  |
| 14 | footnotes[1] | It is not where the rails physically came together on Sept. 5, 1876. | PASS | /scvhistory/lw2726.htm |  |
| 15 | footnotes[2] | Story of the Golden Spike at Lang Station, | PASS | /scvhistory/golden-spike-centennial-best.htm |  |
| 16 | footnotes[2] | Gov. Downey introduced L.W. Thatcher to Col. Crocker as the public spirited jeweler who had manufactured the gold spike and silver hammer to [cut here for the table] | PASS | /scvhistory/golden-spike-centennial-best.htm |  |
| 17 | footnotes[2] | The spike is of solid San Gabriel gold, the same in size as ordinary railroad spikes; the hammer is of solid silver with a handle of orange  [cut here for the table] | PASS | /scvhistory/golden-spike-centennial-best.htm |  |
| 18 | footnotes[2] | with six blows of the silver hammer drove it to its resting place. | PASS | /scvhistory/golden-spike-centennial-best.htm | continues "and the railroad connection"; terminal punctuation only |
| 19 | footnotes[2] | Golden Spike Joins Rails in Soledad Canyon: Contemporary Accounts, | PASS | /scvhistory/pollack0910lang.html |  |
| 20 | footnotes[2] | L.W. Thatcher, a jeweler of Los Angeles, and formerly a conductor on the Central Pacific Railroad, has made and presented a golden spike and [cut here for the table] | PASS | /scvhistory/pollack0910lang.html |  |
| 21 | footnotes[3] | Lang Station Landmark Plaque | PASS | /scvhistory/jk0009.htm |  |
| 22 | footnotes[3] | LANG SOUTHERN PACIFIC STATION / On September 5, 1876, Charles Crocker, President of the Southern Pacific Railroad drove a gold spike to comp [cut here for the table] | PASS | /scvhistory/jk0009.htm |  |
| 23 | footnotes[3] | Plaque placed by California Park Commission in cooperation with Historical Society of Southern California, June 15, 1957. | PASS | /scvhistory/jk0009.htm |  |
| 24 | footnotes[4] | Story of the Golden Spike at Lang Station, | PASS | /scvhistory/golden-spike-centennial-best.htm |  |
| 25 | footnotes[4] | There were nearly 4,000 people on the ground, nearly 3,000 being Chinese employees of the railroad. | PASS | /scvhistory/golden-spike-centennial-best.htm | the reporter's sentence continues with a relative clause ("who with their picks, shovels and bamboo hats..."); the quotation ends at "railroad" and closes with a period, terminal punctuation only |
| 26 | footnotes[4] | The laying of the remaining 1,050 feet of track and the connecting of the through line was done as soon as the railroad officials and invite [cut here for the table] | PASS | /scvhistory/golden-spike-centennial-best.htm |  |
| 27 | footnotes[4] | All the tracklayers were Caucasians and the Chinese simply looked on and cheered their favorite crew. | PASS | /scvhistory/golden-spike-centennial-best.htm |  |
| 28 | footnotes[4] | The time occupied was between 5½ and 6 minutes. | PASS | /scvhistory/golden-spike-centennial-best.htm |  |
| 29 | footnotes[4] | formed the railroad workers only; another 1,000 or more were the spectators. | PASS | /scvhistory/golden-spike-centennial-best.htm | continues "who gathered at various vantage points"; terminal punctuation only |
| 30 | footnotes[5] | Story of the Golden Spike at Lang Station, | PASS | /scvhistory/golden-spike-centennial-best.htm |  |
| 31 | footnotes[5] | The scene was one worthy of the painter's pencil, but by some strange oversight, no photographer was present and the picture presented will  [cut here for the table] | PASS | /scvhistory/golden-spike-centennial-best.htm |  |
| 32 | footnotes[6] | Concise History of the Santa Clarita Valley, | PASS | /scvhistory/lwhist.htm |  |
| 33 | footnotes[6] | August 12, 1876 saw the first iron horse lumber through the San Fernando Tunnel into the little town of Newhall. On September 5, Southern Pa [cut here for the table] | PASS | /scvhistory/lwhist.htm |  |
| 34 | footnotes[7] | A Town is Born, | PASS | /scvhistory/signal/reynolds/part40.html |  |
| 35 | footnotes[7] | The day after the golden spike was driven at Lang Station in Soledad Canyon, Newhall Depot opened its doors for business. The depot was not  [cut here for the table] | PASS | /scvhistory/signal/reynolds/part40.html |  |
| 36 | footnotes[7] | The town of Newhall was laid out on October 13, 1876 by Western Development, a real estate subsidiary of Southern Pacific. | PASS | /scvhistory/signal/reynolds/part40.html |  |
| 37 | footnotes[8] | September 6: Newhall Train Station opens at Bouquet Junction (moves in 1878). | PASS | /scvhistory/timeline.htm |  |
| 38 | footnotes[8] | October 13: Town of Newhall founded at Bouquet Junction. | PASS | /scvhistory/timeline.htm |  |
| 39 | footnotes[8] | October 18: Southern Pacific begins subdividing town of Newhall. | PASS | /scvhistory/timeline.htm |  |
| 40 | footnotes[9] | Lang Station Landmark Plaque, | PASS | /scvhistory/jk0009.htm |  |
| 41 | footnotes[9] | On Sept. 5, 1926, railroad enthusiasts and history buffs descended on Lang for a 50th anniversary celebration of the driving of the golden s [cut here for the table] | PASS | /scvhistory/jk0009.htm |  |
| 42 | footnotes[9] | Two bronze plaques were placed at the site, | PASS | /scvhistory/jk0009.htm |  |
| 43 | footnotes[9] | Representatives of the SCV Historical Society and the Chinese Historical Society returned Sept. 5, 2001, for a 125th anniversary ceremony or [cut here for the table] | PASS | /scvhistory/jk0009.htm | continues ", which operated the rail line."; terminal punctuation only |
| 44 | footnotes[10] | Golden Spike Centennial: Illustrated Historical Program, | PASS | /scvhistory/golden-spike-centennial-index.htm |  |
| 45 | footnotes[10] | Don Torgeson of E Clampus Vitus leads Clampers parade and places plaque | PASS | /scvhistory/golden-spike-centennial-index.htm |  |
| 46 | footnotes[10] | placing of plaque by Chinese Historical Society | PASS | /scvhistory/golden-spike-centennial-index.htm |  |
| 47 | footnotes[10] | Driving of Golden Spike | PASS | /scvhistory/golden-spike-centennial-index.htm |  |
| 48 | footnotes[11] | Press Kit: Lang Station Golden Spike Centennial, | PASS | /scvhistory/chssc_presskit_lang19760905.htm |  |
| 49 | footnotes[11] | On this Centennial we honor over three thousand Chinese who helped build the Southern Pacific Railroad and the San Fernando Tunnel. Their la [cut here for the table] | PASS | /scvhistory/chssc_presskit_lang19760905.htm |  |
| 50 | footnotes[12] | 1876 Lang Station Golden Spike, | PASS | /scvhistory/lw2248a.htm |  |
| 51 | footnotes[12] | One day in November 1956, an heir of C. Tempelton Crocker walked into the office of the California Historical Society | PASS | /scvhistory/lw2248a.htm |  |
| 52 | footnotes[12] | Last Spike / Connecting Los Angeles / and San Francisco / by Rail, | PASS | /scvhistory/lw2248a.htm |  |
| 53 | footnotes[12] | Sept. 5th / 1876. | PASS | /scvhistory/lw2248a.htm |  |
| 54 | footnotes[13] | A Golden Spike: Completion of the Rail Line at Lang Station: The Story of the Completion of the Southern Pacific-San Joaquin Valley Line Bet [cut here for the table] | PASS | /scvhistory/spike-harrington-index.htm | a title; the page sets the subtitles off with periods. The colons are citation style |
| 55 | footnotes[14] | Golden Spike Joins Rails in Soledad Canyon: Contemporary Accounts, | PASS | /scvhistory/pollack0910lang.html |  |
| 56 | editorNotes[0].note | no photographer was present, | PASS | /scvhistory/golden-spike-centennial-best.htm |  |
| 57 | editorNotes[0].note | nobody thought to bring a camera. | PASS | /scvhistory/golden-spike-centennial-best.htm |  |
| 58 | editorNotes[2].note | The mayors of San Francisco and Los Angeles were present to drive the Golden, last spike. | PASS | /scvhistory/chssc_presskit_lang19760905.htm |  |
| 59 | editorNotes[3].note | July 6. | PASS | /scvhistory/signal/perkins/notes.html | note 19: Perkins wrote "July 6" here; a double typo. |
| 60 | editorNotes[3].note | a double typo | PASS | /scvhistory/signal/perkins/notes.html | note 19 |
| 61 | featuredImage | William Crocker drives a rail spike at the re-enactment of the Southern Pacific completion at Lang Station, September 1926. | PASS | Craft asset 1193 (lang.jpg), its title |  |
| 62 | theLiterature[1].cite | Story of the Golden Spike at Lang Station, | PASS | /scvhistory/golden-spike-centennial-best.htm |  |
| 63 | theLiterature[2].cite | The Golden Spike: The Chinese Contribution, | PASS | /scvhistory/golden-spike-centennial-louie.htm |  |
| 64 | theLiterature[4].cite | Golden Spike Joins Rails in Soledad Canyon: Contemporary Accounts, | PASS | /scvhistory/pollack0910lang.html |  |
| 65 | researchLeads[1] | Chinese Track Layers, | PASS | /scvhistory/hs3024.htm |  |
| 66 | researchLeads[2] | William H. Crocker Drives Ceremonial Golden Spike at 50th Anniversary Reenactment, 1926 | PASS | /scvhistory/us36753.htm (and us31101a.htm) | the page title |
| 67 | researchLeads[3] | within a year after its May 1968 closing, | PASS | /scvhistory/jk0009.htm |  |
| 68 | researchLeads[4] | the real golden spike stood nearby in a Plexiglas case | PASS | /scvhistory/to7601.htm |  |
| 69 | researchLeads[5] | with State Landmark dedication at Lang Station | PASS | /scvhistory/timeline.htm |  |
| 70 | researchLeads[6] | The Central Pacific rails, coming down from the north, and the Southern Pacific rails, coming up from the south | PASS | /scvhistory/to7601.htm |  |

## Changes from v1 to v2

1. **footnotes[12].** Was: "1876 Lang Station Golden Spike," LW2248, May 4, 2012 Now: "1876 Lang Station Golden Spike," LW2248a, May 4, 2012 Why: The photograph's code in Craft (#3023, photoSourceCode) and on the mirror (/scvhistory/lw2248a.htm) is LW2248a.
2. **photographs[0].photoId.** Was: LW2248 Now: LW2248a Why: As above: #3023 is LW2248a; the loader matches the code exactly.

## The event as it would read

- **eventDate:** September 5, 1876
- **eventDateEdtf:** 1876-09-05
- **eventDateStart:** (empty)
- **eventDateEnd:** (empty)
- **startEvidence:** contemporary
- **eventChlNumber:** 590
- **eventSignificance:** The Southern Pacific's line between San Francisco and Los Angeles was completed at Lang, in Soledad Canyon, on September 5, 1876: the first rail connection of Los Angeles with San Francisco. The line ran through this valley, and Newhall began as a town beside its new depot that fall.
- **historicalEra:** #161 Railroad & Oil Era (1876–1909)
- **historicalPeriod:** #173 1850-1899
- **recordTags:** #18932 Railroad
- **neighborhood:** #207 Soledad Canyon
- **featuredImage:** asset 1193 (lang.jpg), titled in Craft "William Crocker drives a rail spike at the re-enactment of the Southern Pacific completion at Lang Station, September 1926." (the 1926 reenactment, not the event; already Lang Station's image)
- **bandImage:** (empty) none; nothing of the event exists to show, and the 1926 reenactment photographs (US31101, US36748 to US36753, HS2601, HS2602, SM2601) are not in Craft

On September 5, 1876, the Southern Pacific Railroad completed its line between San Francisco and Los Angeles at Lang, on John Lang's homestead in Soledad Canyon, where the track laid south from San Francisco met the track laid north from Los Angeles.[1][2] Charles Crocker, the company's president, drove the last spike, made of gold, with a silver hammer; L.W. Thatcher, a Los Angeles jeweler, made both.[3] It was the first rail connection of Los Angeles with San Francisco and with the transcontinental lines.[4]

Most of the line was built by Chinese laborers: at least 3,000 of a workforce of about 4,000, by Leon Worden's count, and nearly 3,000 of the nearly 4,000 people on the ground that day, by the San Francisco Chronicle's.[1][5] At the ceremony two gangs of white tracklayers laid the last 1,050 feet of track from either end while the Chinese workers stood aside and watched.[5][1] No photographer was there.[6]

The line came through this valley, by the San Fernando Tunnel into Newhall and up Soledad Canyon, and its two halves were joined inside it. "San Francisco and Los Angeles came together in Santa Clarita," Leon Worden wrote.[7] By Jerry Reynolds's history, the Newhall depot opened the next day, at today's Bouquet Canyon Road and Magic Mountain Parkway, and a Southern Pacific real estate company laid out the town of Newhall on October 13; the SCVHistory.com timeline gives the same dates.[8][9]

The site is California Historical Landmark No. 590, and the State's plaque was placed there on June 15, 1957.[4] The day has been marked at Lang since: by a reenactment for its fiftieth anniversary in 1926; at the centennial on September 5, 1976, when the Chinese Historical Society of Southern California placed a plaque honoring "over three thousand Chinese who helped build the Southern Pacific Railroad and the San Fernando Tunnel"; and on its 125th anniversary in 2001.[10][11][12] The spike itself is held by the California Historical Society in San Francisco.[13]

The fuller accounts are Marie Harrington's A Golden Spike, published by the San Fernando Valley Historical Society for the centennial, and Gerald M. Best's "Story of the Golden Spike at Lang Station" in the centennial program, which quotes the Los Angeles Star and San Francisco Chronicle reporters who were there. Alan Pollack read the newspapers of 1876 again in 2010.[14][2][15]

1. Leon Worden, webmaster's note "About Lang Station and the 'Wedding of the Rails,'" 2020, at the head of Gerald M. Best, "Story of the Golden Spike at Lang Station," as carried on SCVHistory.com, /scvhistory/golden-spike-centennial-best.htm: "Train tracks laid north out of Los Angeles and south out of San Francisco met on John Lang's homestead in Soledad Canyon and culminated in a 'golden spike' ceremony on September 5, 1876." "Out of a workforce of approximately 4,000 men, at least 3,000 were Chinese immigrants." "When the last 1,000 feet of track remained to be laid, the Chinese workers were ordered to stand aside so Caucasian men could complete the task in view of the political dignitaries and railroad executives who gathered at the Lang site." "(Ironically, nobody thought to bring a camera.)"
2. Gerald M. Best, "Story of the Golden Spike at Lang Station," Golden Spike Centennial Souvenir Program, Santa Clarita Valley Historical Society, September 5, 1976, as carried on SCVHistory.com, /scvhistory/golden-spike-centennial-best.htm: "The Southern Pacific announced that the rails would be joined on September 5, 1876 at a point called Lang, 43 miles from Los Angeles and 440 miles from San Francisco." A map published four days later, LW2726 (photograph #4439 in this archive, /scvhistory/lw2726.htm), marks the two companies' connection at Goshen, in Tulare County; Leon Worden's caption: "It is not where the rails physically came together on Sept. 5, 1876."
3. Gerald M. Best, "Story of the Golden Spike at Lang Station," 1976, /scvhistory/golden-spike-centennial-best.htm, quoting the Los Angeles Star's reporter at the ceremony: "Gov. Downey introduced L.W. Thatcher to Col. Crocker as the public spirited jeweler who had manufactured the gold spike and silver hammer to be used in the ceremonies." "The spike is of solid San Gabriel gold, the same in size as ordinary railroad spikes; the hammer is of solid silver with a handle of orange wood." Best: Crocker "with six blows of the silver hammer drove it to its resting place." Alan Pollack, "Golden Spike Joins Rails in Soledad Canyon: Contemporary Accounts," Heritage Junction Dispatch, September-October 2010, /scvhistory/pollack0910lang.html, quotes the announcement that "L.W. Thatcher, a jeweler of Los Angeles, and formerly a conductor on the Central Pacific Railroad, has made and presented a golden spike and a silver hammer."
4. Caption to JK0009, "Lang Station Landmark Plaque" (photograph by James Krause), as carried on SCVHistory.com, /scvhistory/jk0009.htm, giving the text of the State's plaque of 1957: "LANG SOUTHERN PACIFIC STATION / On September 5, 1876, Charles Crocker, President of the Southern Pacific Railroad drove a gold spike to complete his company's San Joaquin Valley line. First rail connection of Los Angeles with San Francisco and transcontinental lines." The plaque names the landmark as No. 590 and reads: "Plaque placed by California Park Commission in cooperation with Historical Society of Southern California, June 15, 1957." The same text is on the back of the 1996 collector's plate, LW2057 (photograph #2751 in this archive).
5. Gerald M. Best, "Story of the Golden Spike at Lang Station," 1976, /scvhistory/golden-spike-centennial-best.htm, quoting the San Francisco Chronicle's reporter at the ceremony: "There were nearly 4,000 people on the ground, nearly 3,000 being Chinese employees of the railroad." "The laying of the remaining 1,050 feet of track and the connecting of the through line was done as soon as the railroad officials and invited guests could alight from the San Francisco train and take their places." "All the tracklayers were Caucasians and the Chinese simply looked on and cheered their favorite crew." "The time occupied was between 5½ and 6 minutes." Best adds that the 4,000 "formed the railroad workers only; another 1,000 or more were the spectators."
6. Gerald M. Best, "Story of the Golden Spike at Lang Station," 1976, /scvhistory/golden-spike-centennial-best.htm, quoting the Los Angeles Star's reporter: "The scene was one worthy of the painter's pencil, but by some strange oversight, no photographer was present and the picture presented will live only in the memories of those whose good fortune it was to be present."
7. Leon Worden, "Concise History of the Santa Clarita Valley," February 1997, as carried on SCVHistory.com, /scvhistory/lwhist.htm: "August 12, 1876 saw the first iron horse lumber through the San Fernando Tunnel into the little town of Newhall. On September 5, Southern Pacific president Charles Crocker hammered a golden spike through the rails at John Lang's homestead in Soledad Canyon. San Francisco and Los Angeles came together in Santa Clarita."
8. Jerry Reynolds, History of the Santa Clarita Valley, ed. Leon Worden, Santa Clarita Valley Historical Society, 1998, chapter 40, "A Town is Born," article #2105 in this archive (/scvhistory/signal/reynolds/part40.html): "The day after the golden spike was driven at Lang Station in Soledad Canyon, Newhall Depot opened its doors for business. The depot was not located at the present townsite, but rather at the modern-day junction of Bouquet Canyon Road and Magic Mountain Parkway." "The town of Newhall was laid out on October 13, 1876 by Western Development, a real estate subsidiary of Southern Pacific." Given as Reynolds's under the reliability rule (docs/PROFILES.md).
9. SCVHistory.com timeline, /scvhistory/timeline.htm, 1876: "September 6: Newhall Train Station opens at Bouquet Junction (moves in 1878)." "October 13: Town of Newhall founded at Bouquet Junction." "October 18: Southern Pacific begins subdividing town of Newhall." The timeline names no source for these lines and may rest on Reynolds.
10. Caption to JK0009, "Lang Station Landmark Plaque," /scvhistory/jk0009.htm: "On Sept. 5, 1926, railroad enthusiasts and history buffs descended on Lang for a 50th anniversary celebration of the driving of the golden spike." Of September 5, 1976: "Two bronze plaques were placed at the site," one by the Chinese Historical Society of Southern California and one by E Clampus Vitus. "Representatives of the SCV Historical Society and the Chinese Historical Society returned Sept. 5, 2001, for a 125th anniversary ceremony organized by Metrolink."
11. Santa Clarita Valley Historical Society, "Golden Spike Centennial: Illustrated Historical Program," Lang Station, Sunday, September 5, 1976, edited and produced by Ruth Newhall, as carried on SCVHistory.com, /scvhistory/golden-spike-centennial-index.htm. The order of events lists, at 4:50, "Don Torgeson of E Clampus Vitus leads Clampers parade and places plaque"; at 5:00, the "placing of plaque by Chinese Historical Society"; at 5:20, "Driving of Golden Spike" by Robert Banning, grandson of Phineas Banning.
12. Chinese Historical Society of Southern California, "Press Kit: Lang Station Golden Spike Centennial," September 5, 1976, as carried on SCVHistory.com, /scvhistory/chssc_presskit_lang19760905.htm, giving the English text of the Society's plaque: "On this Centennial we honor over three thousand Chinese who helped build the Southern Pacific Railroad and the San Fernando Tunnel. Their labor gave California the first North-South Railway, changing the State's history."
13. Leon Worden, "1876 Lang Station Golden Spike," LW2248a, May 4, 2012, photograph #3023 in this archive (/scvhistory/lw2248a.htm): the spike is held by the California Historical Society in San Francisco; "One day in November 1956, an heir of C. Tempelton Crocker walked into the office of the California Historical Society" and donated it. Engraved "Last Spike / Connecting Los Angeles / and San Francisco / by Rail," and on its head "Sept. 5th / 1876."
14. Marie Harrington, "A Golden Spike: Completion of the Rail Line at Lang Station: The Story of the Completion of the Southern Pacific-San Joaquin Valley Line Between Los Angeles and San Francisco, September 5, 1876," San Fernando Valley Historical Society, Mission Hills, Calif., September 5, 1976, as carried on SCVHistory.com, /scvhistory/spike-harrington-index.htm (parts I to IV and bibliography).
15. Alan Pollack, "Golden Spike Joins Rails in Soledad Canyon: Contemporary Accounts," Heritage Junction Dispatch, September-October 2010, as carried on SCVHistory.com, /scvhistory/pollack0910lang.html, from the Sacramento Daily Record-Union and San Francisco's Daily Alta California of September 1876.

**Editor's note, The photographs (bottom):** Nobody photographed the ceremony of 1876. The Los Angeles Star's reporter wrote that "no photographer was present," and Leon Worden notes that "nobody thought to bring a camera." The images with this record are of other things and other years: the spike itself, photographed in 2012; a map published four days after the event; and the commemorations of 1926, 1976 and 1996. Each says which.

**Editor's note, The last stretch of track (bottom):** The San Francisco Chronicle's reporter, as Gerald M. Best quotes him, gives 1,050 feet, and so do Alan Pollack (2010) and Jerry Reynolds. Leon Worden (2020), the Chinese Historical Society's press kit (1976) and March Fong Eu's remarks of 2001 give 1,000 feet. The record gives 1,050, the reporter's figure.

**Editor's note, Who drove the spike (bottom):** Every account in the archive names Charles Crocker, except the abstract in the Chinese Historical Society's press kit of 1976, which says "The mayors of San Francisco and Los Angeles were present to drive the Golden, last spike." The mayors were present and spoke (Pollack 2010, quoting the Daily Alta California); the record follows the reporters who were there.

**Editor's note, The date in Perkins (bottom):** A.B. Perkins's chapter on early transportation (article #1426) prints the date as "July 6." Leon Worden's editor's note 19 to that chapter calls it "a double typo" for September 5, 1876. Every other source gives September 5, 1876.

### Relations

- **eventPersons:** #16388 Charles Crocker (notes 3, 4; drove the last spike as president of the Southern Pacific (the record is an empty stub))
- **eventPersons:** #18820 John Lang (notes 1; the rails met on his homestead, which gave Lang its name)
- **eventPlaces:** #609 Lang Station (notes 1, 4; the site of the ceremony; Landmark No. 590)
- **eventPlaces:** #18529 Soledad Canyon (notes 1, 7; the rails met on John Lang's homestead in Soledad Canyon)
- **eventOrganizations:** #16101 Southern Pacific Railroad (notes 2, 4; built the line and held the ceremony (the record is an empty stub))
- **eventOrganizations:** #15493 Santa Clarita Valley Historical Society (notes 10, 11; organized the centennial of 1976 and published its program)
- **eventOrganizations:** #392 Historical Society of Southern California (notes 4; the State's plaque of 1957 was placed in cooperation with it)
- **eventArticles:** #2105 40. A Town is Born (named in note 8)
- **cited, not a document, not related:** #4439 photographs "Southern Pacific Connects with Central Pacific, Map 9-9-1876."
- **cited, not a document, not related:** #2751 photographs "Commemorative Plate: Lang Station Golden Spike 120th Anniversary 1996."
- **cited, not a document, not related:** #2105 articles "40. A Town is Born"
- **cited, not a document, not related:** #3023 photographs "Photo Gallery: 1876 Golden Spike."
- **cited, not a document, not related:** #1426 articles "4. Early Transportation"
- **photograph #3023 LW2248a** takes the event in photoEvents (named in note 13; )
- **photograph #4439 LW2726** takes the event in photoEvents (named in note 2; )
- **photograph #2751 LW2057** takes the event in photoEvents (named in note 4; )
- **Held, photograph #5069 LW3251** "Commemorative Cover: SPRR Lang Station Golden Spike Centennial, 9-5-1976.": footnote 10, 11 does not name it. Set `$CFG['linkHeldPhotos'] = true` to link the held photographs too.
- **Held, article #2103** 39. Ribbons of Steel (Reynolds, 1998 edition) (his account of the ceremony; not cited in the body (its figures, 335 Angelenos and Huntington present, differ from Best's 191 passengers and Huntington absent)): no footnote names it
- **Held, article #12286** Future Destination: Historic Santa Clarita Valley (Leon Worden, 2009) (its Lang Station paragraph on the spike and the site; not cited): no footnote names it
- **relatedEvents:** none set; the draft says: The San Fernando Tunnel, 1876 (no record yet; events inventory section 6). Link when it exists: Leon Worden's Concise History and Perkins tie the tunnel's completion to the spike.
- **footnotesOn:** not set. On this site it means "the notes were published on another record" (create_saugus_2019_event_2026_10_05.php); the documents the draft listed there go to sourceDocuments instead.
- **Not written:** every other record. The draft's forNathan recommendations about other records (new place or organization records, changes to person records, image attachments) are not acted on.

### Research leads (researchLeads, not shown on the page)

- Photographs on the mirror, none in Craft: the 1926 reenactment (US31101a to i, US33025, US36257, US36259, US36748 to US36753, HS2601, HS2602, SM2601), the 1976 centennial (TO7601 gallery by Tom Mason, JK0010 plaques), the 2001 quasquicentennial (lang-090501-index, ra090501f), JK0009 (the 1957 plaque) and HS3023 (the spike, from a printed copy). Each would carry its year in the caption; none shows 1876.
- HS3024, 'Chinese Track Layers,' is captioned as a handcar crew in the Tehachapis, not at Lang, and gives no date for the photograph. Do not attach it to this record as an image of the event.
- Asset 1193 (lang.jpg), the 1926 William Crocker photograph that is Lang Station's featured image, has no photo ID. It is probably one of US31101a or US36753 ('William H. Crocker Drives Ceremonial Golden Spike at 50th Anniversary Reenactment, 1926'); not checked image against image.
- When Lang Station was torn down: 1971 by Leon Worden (2020 note; LW2057; TO7601), Reynolds chapter 33, the timeline and the Lang Station record; 'within a year after its May 1968 closing,' gone by late spring 1969, by John Sweetser as quoted in the JK0009 caption. For the place record, not this one.
- Whether the spike was shown in public: Leon Worden 2012 (LW2248), from the California Historical Society, says it is believed to have been displayed just once, at the Whittier Mansion; TO7601b says 'the real golden spike stood nearby in a Plexiglas case' at the 1976 centennial at Lang. Not reconciled.
- The timeline says the centennial of 1976 was marked 'with State Landmark dedication at Lang Station'; the State's plaque is dated June 15, 1957 (JK0009, LW2057, and the timeline's own 1957 line). Probably the plaques of 1976; not settled.
- Which companies met: Best (1976) has the Southern Pacific building from both ends; TO7601 says 'The Central Pacific rails, coming down from the north, and the Southern Pacific rails, coming up from the south'; LW2726 shows the connection at Goshen. The body says only the Southern Pacific's line, which the 1957 plaque supports.
- Harrington parts I to IV and Louie were not read in full for this draft; the Los Angeles Daily Star of September 6, 1876, which Harrington's bibliography cites, is not in the mirror as a scan as far as was checked.
- Who came from San Francisco: Best has Stanford aboard and Huntington and Hopkins absent; Reynolds chapter 39 has Stanford and Huntington; the passenger counts differ (Best 191 from Los Angeles; Reynolds 335). Not in the body.

## From the draft's forNathan (approved with the draft; listed for the record)

- 1. Images. Every image proposed is labelled with what it is and its year (editor note 'The photographs' and each caption). The featured image proposed is asset 1193, the 1926 reenactment, with its existing title. Say if you would rather the record have no featured image.
- 2. Photograph #2751 (LW2057, the 1996 plate) has photoDate 'September 5, 1876' and photoDateEdtf 1876-09-05, which dates the plate to the event. Recommend photoDate 1996 (the plate reads "1876-1996" and "120 Years"). A separate fix; say if you want it.
- 3. Image assets for #4439 (11109/13412) and #5069 (11674/13186) are in archiveMedia but not attached; #3023 and #2751 have none. Attaching is a separate fix.
- 4. The Newhall depot and town dates rest on Reynolds chapter 40 and the timeline, which names no source and may repeat him. The body attributes them to Reynolds, as the reliability rule asks. Approve, or cut the sentence.
- 5. eventOrganizations adds the Historical Society of Southern California (#392, the 1957 plaque) and the SCV Historical Society (#15493, the 1976 centennial). Both are commemoration ties the sources make. Approve or drop.
- 6. Not linked, deliberately: Phineas Banning #18714 (spoke at the ceremony, Pollack 2010; his grandson drove the replica spike in 1976), Henry Mayo Newhall #283 (Leon 2012 ties him to the depot and town site, not to the ceremony), John Timothy Gifford #337 (the Newhall agent; his tie is to the depot), Perkins's Tales of Lang and Soledad #1444 (mentions the railroad's completion in passing), the LW2545 photographs of the overgrown markers (2013).
- 7. Charles Crocker #16388 and Southern Pacific #16101 are empty stubs. This record links them; they are the obvious next profiles.
- 8. The Chinese Historical Society of Southern California has no record. It placed the 1976 plaque and returned in 2001; a candidate if it recurs elsewhere.
- 9. Leon's 2020 note on the Best page says the two centennial plaques were 'placed at the centennial event in 1876', a slip for 1976. Not quoted here and not changed; flagged only.
- 10. relatedEvents waits for the San Fernando Tunnel record.
