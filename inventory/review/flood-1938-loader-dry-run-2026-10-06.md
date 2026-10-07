# Great Flood of 1938: the loader's dry run, 6 October 2026

Written by `scripts/import/create_flood_1938_event_2026_10_06.php` (with `scripts/import/_event_from_draft_2026_10_06.php`) in a dry run. A dry run writes nothing to Craft. The event is read from `inventory/review/flood-1938-draft-2026-10-06.json` (SHA-256 `ccb319416f54b92f...`), the draft of 6 October 2026, written in the v2 shape with every quotation checked word for word that day. Nathan approved it for applying on 6 October 2026.

**Refusals:** none.

## The run

```
DRY RUN create_flood_1938_event_2026_10_06.php
==============================================================================
EVENT: #31904 "Great Flood of 1938" exists, not recreated
    content advisory: yes, the first editor note, top
    historicalEra: #164 Great Depression (1929–1940)
    historicalPeriod: #176 1930-1939
    recordTags: #18940 Fire & Flood
    neighborhood: #205 Saugus; #191 Castaic; #207 Soledad Canyon; #202 Placerita Canyon; #186 Acton
    eventPersons: none
    eventPlaces: #15596 Santa Clara River (notes 2, 4, 6); #643 Saugus Speedway (notes 6, 3); #15886 Castaic Creek (notes 4, 5); #18529 Soledad Canyon (notes 4); #18565 Placerita Canyon (notes 7)
    eventOrganizations: #16101 Southern Pacific Railroad (notes 4, 11)
    eventFallenOfficers: none
    eventArticles: #1444 Tales of Lang and Soledad (note 9)
    articles held, no footnote names them: none
    sourceDocuments: none cited
        nothing to set
    cited records that are not documents (not in sourceDocuments): #1444 articles "Tales of Lang and Soledad"; #4871 photographs "Southern Pacific Locomotive Derailed, Overturned 3-25-1938."
    featuredImage: none
    photograph #4871 LW3067: photoEvents has it (named in note 11)
    other records: nothing written
    REFUSED: none
```

## Quotation check

61 quotations in the v2 draft (every quoted passage, in double or single quotation marks, in the fields (body, significance, footnotes, editor notes, recordDates labels, relation ties, photograph rows, image notes, the literature list) and the research leads; forNathan is not loaded and was not checked) were checked word for word: 61 PASS, 0 CORRECTED from v1, 0 FAIL. Ellipses: the draft has none. Checked against: mirror pages on /Volumes/Reggie/SCVHistory/scvhistory.com (HTML read as latin-1; PDFs by pdftotext), and Craft for record text and titles, by read-only queries.

"Terminal punctuation only" means the quotation stops where the source's sentence goes on and closes with a period or comma of its own; no word is changed.

| # | Where | Quotation | Result | Page | Note |
| --- | --- | --- | --- | --- | --- |
| 1 | body | when a creek became a river, | PASS | /scvhistory/files/lw3356/lw3356.pdf | body; the source is cited in note 5; the caption ends with a period, here a comma; terminal punctuation only |
| 2 | body | having collapsed during the March floods, | PASS | /scvhistory/tlp_lat042338.htm | body; the source is cited in note 6; the sentence ends with a period, here a comma; terminal punctuation only |
| 3 | body | expressed surprise at the speed with which the work of rehabilitation had been accomplished following the flood. | PASS | /scvhistory/arnettefield_cccbearcanyon1933.htm | body; the source is cited in note 8 |
| 4 | body | mudded up in the flood of 1938 and were never cleaned out, | PASS | craft #1444; /scvhistory/perkins032361.htm | body; the source is cited in note 9; the sentence ends with a period, here a comma; terminal punctuation only |
| 5 | footnotes[0] | March 2: Great Flood of 1938 causes massive destruction across the greater Los Angeles region. | PASS | /scvhistory/timeline.htm |  |
| 6 | footnotes[1] | About the Great Flood of 1938, | PASS | /scvhistory/do3801.htm; /scvhistory/jn3801.htm |  |
| 7 | footnotes[1] | Hill's Ranch Rodeo (Saugus Speedway), | PASS | /scvhistory/jn3801.htm |  |
| 8 | footnotes[1] | the Great Flood (aka Los Angeles Flood) of 1938 hit the greater Los Angeles area hardest overnight on March 1-2. By the time the water reced [cut here for the table] | PASS | /scvhistory/ap3101.htm; /scvhistory/ap3314.htm; /scvhistory/do3801.htm; /scvhistory/jn3801.htm |  |
| 9 | footnotes[1] | Considered a 50-year flood, it started Feb. 27, 1938, when a storm system moved in from the Pacific Ocean and hit the San Gabriel Mountains. | PASS | /scvhistory/ap3101.htm; /scvhistory/ap3314.htm; /scvhistory/do3801.htm; /scvhistory/jn3801.htm |  |
| 10 | footnotes[1] | Driven by gale-force winds, it hit March 1 at about 8:45 p.m., dumping 10 inches of rain in the lowlands and at least 32 inches in the mount [cut here for the table] | PASS | /scvhistory/ap3101.htm; /scvhistory/ap3314.htm; /scvhistory/do3801.htm; /scvhistory/jn3801.htm |  |
| 11 | footnotes[1] | The Santa Clarita Valley wasn't spared. The Santa Clara River overflowed. Roads and bridges were wiped out and ranch buildings situated alon [cut here for the table] | PASS | /scvhistory/ap3101.htm; /scvhistory/ap3314.htm; /scvhistory/do3801.htm; /scvhistory/jn3801.htm |  |
| 12 | footnotes[1] | In response to the 1938 flood, Congress passed the Flood Control Act of 1941, which authorized the U.S. Army Corps of Engineers to channeliz [cut here for the table] | PASS | /scvhistory/ap3101.htm; /scvhistory/ap3314.htm; /scvhistory/do3801.htm; /scvhistory/jn3801.htm |  |
| 13 | footnotes[2] | Great Flood of 1938: Skies Open Up with Deadly Force, | PASS | /scvhistory/pollack0719greatflood.htm | a title; the page prints it with a closing period, here a comma before the closing quotation mark |
| 14 | footnotes[2] | It all started February 27, 1938, when 1.42 inches of rain fell on the Los Angeles area. | PASS | /scvhistory/pollack0719greatflood.htm |  |
| 15 | footnotes[2] | Approximately 115 people perished in the floods of 1938 in Los Angeles. Damage was estimated at $78 million. | PASS | /scvhistory/pollack0719greatflood.htm |  |
| 16 | footnotes[2] | Shortly thereafter, Paul Hill, owner of the rodeo at that time, lost the property to bank repossession. Ownership passed to William Bonelli  [cut here for the table] | PASS | /scvhistory/pollack0719greatflood.htm |  |
| 17 | footnotes[2] | Los Angeles Times, February 28 - March 5, 1938; Newhall Signal, March 3, 1938. | PASS | /scvhistory/pollack0719greatflood.htm |  |
| 18 | footnotes[3] | Great Flood of 1938: Skies Open Up with Deadly Force, | PASS | /scvhistory/pollack0719greatflood.htm | a title; the page prints it with a closing period, here a comma before the closing quotation mark |
| 19 | footnotes[3] | Damage in the Santa Clarita Valley | PASS | /scvhistory/pollack0719greatflood.htm |  |
| 20 | footnotes[3] | Roads were damaged all across the valley as water roared down from the surrounding canyons. | PASS | /scvhistory/pollack0719greatflood.htm |  |
| 21 | footnotes[3] | The tracks of the Southern Pacific Railroad suffered extensive damage. Several bridges were out in the Saugus area, as well as the Pico Brid [cut here for the table] | PASS | /scvhistory/pollack0719greatflood.htm |  |
| 22 | footnotes[3] | Much of the town of Castaic was reported to be flooded. A major traffic artery through the valley was blocked when the bridge over Castaic C [cut here for the table] | PASS | /scvhistory/pollack0719greatflood.htm |  |
| 23 | footnotes[3] | The home of the James Fryer family was washed away by the flood waters of the Santa Clara River in Soledad Canyon. The school at Honby lost  [cut here for the table] | PASS | /scvhistory/pollack0719greatflood.htm |  |
| 24 | footnotes[4] | Great Flood of 1938: March Storms Took Big Toll, | PASS | /scvhistory/lw3356.htm |  |
| 25 | footnotes[4] | Three separate storms during the period December 11 to March 4, damaged State highways and structures to the extent of $8,000,000. | PASS | /scvhistory/lw3356.htm |  |
| 26 | footnotes[4] | Embankment was washed out together with half the Newhall Saugus highway when a creek became a river. | PASS | /scvhistory/files/lw3356/lw3356.pdf |  |
| 27 | footnotes[4] | carried away 200 feet of the bridge west of Castaic, | PASS | /scvhistory/files/lw3356/lw3356.pdf |  |
| 28 | footnotes[5] | Hill's Ranch Rodeo (Saugus Speedway), | PASS | /scvhistory/jn3801.htm |  |
| 29 | footnotes[5] | Paul Hill's Saugus Rodeo grounds, showing the effects of the Great Flood of March 2, 1938, when the Santa Clara River overflowed and filled  [cut here for the table] | PASS | /scvhistory/jn3801.htm |  |
| 30 | footnotes[5] | Runaway Horse Plunges Through Rodeo Grandstand, | PASS | /scvhistory/tlp_lat042338.htm |  |
| 31 | footnotes[5] | Special Southern Pacific trains carried visitors directly to the new arena | PASS | /scvhistory/tlp_lat042338.htm |  |
| 32 | footnotes[5] | the old buildings having collapsed during the March floods. | PASS | /scvhistory/tlp_lat042338.htm |  |
| 33 | footnotes[6] | Aerial Views: Progression of Ernie Hickson's Placeritos (Monogram) Ranch, 1936-1952, | PASS | /scvhistory/lw3577.htm |  |
| 34 | footnotes[6] | buildings west down Placerita Canyon Road and set up the Placeritos Ranch | PASS | /scvhistory/lw3577.htm |  |
| 35 | footnotes[6] | erected his buildings on the south bank of the creek | PASS | /scvhistory/lw3577.htm |  |
| 36 | footnotes[6] | The violent downpour shifted the course of the creek south. Now Hickson's movie town is on the north bank of the creek. | PASS | /scvhistory/lw3577.htm |  |
| 37 | footnotes[7] | Ballfield Named for CCC Worker Killed While Battling SCV Brush Fire, 1933, | PASS | /scvhistory/arnettefield_cccbearcanyon1933.htm |  |
| 38 | footnotes[7] | The composite photograph was made between May and July of 1938, shortly after the Great Flood of March 2. The camp required repairs | PASS | /scvhistory/arnettefield_cccbearcanyon1933.htm |  |
| 39 | footnotes[7] | Captain Floyd B. Rutherford of the district staff made the monthly inspection at Camp Bear Canyon on [March 31]. He gave the outfit a fine r [cut here for the table] | PASS | /scvhistory/arnettefield_cccbearcanyon1933.htm |  |
| 40 | footnotes[8] | Tales of Lang and Soledad, | PASS | /scvhistory/perkins032361.htm; craft #1444 |  |
| 41 | footnotes[8] | Rivers End, | PASS | /scvhistory/perkins032361.htm; craft #1444 |  |
| 42 | footnotes[8] | which attractive resort is on the old Lang ranch | PASS | /scvhistory/perkins032361.htm; craft #1444 |  |
| 43 | footnotes[8] | They mudded up in the flood of 1938 and were never cleaned out. | PASS | /scvhistory/perkins032361.htm; craft #1444 |  |
| 44 | footnotes[9] | The Santa Clara River floods. Unidentified photograph; probably in Acton and probably the Great Flood of March 2, 1938 | PASS | /scvhistory/ap3314.htm |  |
| 45 | footnotes[9] | Structures in the Acton area are lost as the banks of the Santa Clara River crumble, probably in the flood of March 2, 1938. | PASS | /scvhistory/ap3101.htm |  |
| 46 | footnotes[9] | Flooding at the intersection of Crown Valley Road and Cory Avenue. Most likely the Great Flood of March 2, 1938. | PASS | /scvhistory/do3801.htm |  |
| 47 | footnotes[10] | Southern Pacific Locomotive Derailed, Overturned 3-25-1938, | PASS | /scvhistory/lw3067.htm; craft #4871 |  |
| 48 | footnotes[10] | Collateral damage from the Great Flood of March 2, 1938: A Southern Pacific locomotive rests on its side after it hit an open switch outside [cut here for the table] | PASS | /scvhistory/lw3067.htm; craft #4871 |  |
| 49 | footnotes[10] | Switch Derails Espee Engine, | PASS | /scvhistory/lw3067.htm; craft #4871 |  |
| 50 | footnotes[10] | Striking an open switch three miles north of Saugus last night, the engine of a work train returning from the north, where a crew of workmen [cut here for the table] | PASS | /scvhistory/lw3067.htm; craft #4871 |  |
| 51 | footnotes[11] | Army Corps to Turn Santa Clara River Into L.A.-Style Concrete Channel, | PASS | /scvhistory/sg19680812armycorps.htm |  |
| 52 | footnotes[11] | Then he launched into a discussion of the floods of the last six major floods dating back to 1938. | PASS | /scvhistory/sg19680812armycorps.htm |  |
| 53 | footnotes[11] | Col. Irvine then discussed the Army's plan to build reinforced concrete channels, like those of the Los Angeles River, along the 28 miles of [cut here for the table] | PASS | /scvhistory/sg19680812armycorps.htm |  |
| 54 | footnotes[11] | USACE's channelization plan did not come to fruition. | PASS | /scvhistory/sg19680812armycorps.htm |  |
| 55 | footnotes[12] | Highway Patrol Active in Rescues During Flood, | PASS | /scvhistory/hb3802.htm | a title; the page prints it with a closing period, here a comma before the closing quotation mark |
| 56 | footnotes[13] | Role of Highways in Recent California Floods, | PASS | /scvhistory/panhorst0838.htm | a title; here a comma before the closing quotation mark |
| 57 | footnotes[13] | In southern California, the floods came in March. From February 26 through February 28, approximately 5 in. of rain fell throughout the area [cut here for the table] | PASS | /scvhistory/panhorst0838.htm |  |
| 58 | theLiterature[0].cite | Great Flood of 1938: Skies Open Up with Deadly Force, | PASS | /scvhistory/pollack0719greatflood.htm | a title; the page prints it with a closing period, here a comma before the closing quotation mark |
| 59 | theLiterature[2].cite | Highway Patrol Active in Rescues During Flood, | PASS | /scvhistory/hb3802.htm | a title; the page prints it with a closing period, here a comma before the closing quotation mark |
| 60 | theLiterature[3].cite | Role of Highways in Recent California Floods, | PASS | /scvhistory/panhorst0838.htm | a title; here a comma before the closing quotation mark |
| 61 | researchLeads[0] | could be | PASS | /scvhistory/cy3801.htm |  |

## Changes from v1 to v2

None.

## The event as it would read

**Editor's note, Content advisory (top):** This record concerns a flood in which more than a hundred people died in the Los Angeles region, and a derailment in which two railroad men were hurt.

- **eventDate:** March 2, 1938
- **eventDateEdtf:** 1938-03-02
- **eventDateStart:** February 27, 1938
- **eventDateEnd:** (empty)
- **startEvidence:** contemporary
- **eventChlNumber:** (empty)
- **eventSignificance:** The worst of a storm that began on February 27, 1938, the Great Flood struck the Los Angeles region overnight on March 1 to 2 and killed more than a hundred people. In this valley the Santa Clara River overflowed, washing out roads, bridges and railroad track and wrecking the Saugus rodeo grounds. Congress answered with the Flood Control Act of 1941.
- **historicalEra:** #164 Great Depression (1929–1940)
- **historicalPeriod:** #176 1930-1939
- **recordTags:** #18940 Fire & Flood
- **neighborhood:** #205 Saugus; #191 Castaic; #207 Soledad Canyon; #202 Placerita Canyon; #186 Acton
- **featuredImage:** (empty) none. The one flood-related photograph in Craft is the derailment of March 25 (LW3067), not the flood. JN3801 (the rodeo grounds after the flood) would suit, but it is not in Craft.
- **bandImage:** (empty) none

The Great Flood of 1938 struck the greater Los Angeles region hardest overnight on March 1 to 2, 1938, at the height of a storm that had begun on February 27.[1][2][3] A second storm, driven by gale-force winds, arrived at about 8:45 p.m. on March 1 and dropped 10 inches of rain in the lowlands and at least 32 inches in the mountains.[2] By Leon Worden's count 5,601 buildings were destroyed and 113 to 115 people in Southern California were killed; Alan Pollack gives about 115 dead in the Los Angeles floods and $78 million in damage.[2][3]

The Santa Clarita Valley was not spared. The Santa Clara River overflowed, roads and bridges were wiped out, and ranch buildings along the riverbank floated away.[2] Alan Pollack, writing in 2019 from the Los Angeles Times and the Newhall Signal of 1938, lists the valley's losses: Southern Pacific track damaged, with several bridges out around Saugus and the Pico Bridge west of Newhall; cave-ins that closed the Newhall auto tunnel several times; a fire at the Edison substation in Saugus when a gas main broke; much of Castaic flooded, and the bridge over Castaic Creek west of Castaic Junction washed out; the James Fryer family's home in Soledad Canyon washed away; the Honby school's outbuildings lost; two large bridges on Soledad Canyon Road washed out; and most of the buildings at the Nadeau Deer Farm, in today's Canyon Country, destroyed.[3][4] The State's highway journal reported half of the Newhall-Saugus highway washed out with its embankment "when a creek became a river," and 200 feet of the bridge west of Castaic carried away.[5]

At Paul Hill's rodeo grounds in Saugus, the future Saugus Speedway, the river filled the ranch home and arena with mud and debris.[6] The rodeo went on that April in a new arena, the old buildings "having collapsed during the March floods," the Los Angeles Times reported. Hill lost the property to the bank, and in 1939 it passed to William Bonelli, who made the arena into the Saugus Speedway.[6][3] In Placerita Canyon the flood moved the creek south of Ernie Hickson's movie town, which had stood on its south bank.[7] The Civilian Conservation Corps camp in Bear Canyon needed repairs; an inspector on March 31 "expressed surprise at the speed with which the work of rehabilitation had been accomplished following the flood."[8] The sulphur springs on the old Lang ranch "mudded up in the flood of 1938 and were never cleaned out," A.B. Perkins wrote in 1961.[9] Three photographs from Acton show the Santa Clara River out of its banks; Leon Worden dates them to the flood as probable, not certain.[10]

On March 25 the engine of a Southern Pacific work train, returning from repairing flood damage, struck an open switch near Saugus and overturned, and two trainmen were slightly hurt. Leon Worden calls it collateral damage from the flood.[11]

In response to the flood, Congress passed the Flood Control Act of 1941, which authorized the Army Corps of Engineers to channelize the Los Angeles River and parts of the Santa Ana.[2] In 1968 the Corps proposed concrete channels for the Santa Clara River as well, reviewing the valley's major floods back to 1938; that plan was never carried out.[12]

Accounts written in 1938 are the State's highway journal of April, the Highway Patrol's of April, and F.W. Panhorst's paper for civil engineers of August; Alan Pollack's article of 2019 retells the Los Angeles Times and the Newhall Signal of the time.[5][13][14][3]

1. Leon Worden, SCV Chronology, as carried on SCVHistory.com, /scvhistory/timeline.htm, 1938: "March 2: Great Flood of 1938 causes massive destruction across the greater Los Angeles region."
2. Leon Worden, "About the Great Flood of 1938," 2013, the note under JN3801, "Hill's Ranch Rodeo (Saugus Speedway)," as carried on SCVHistory.com, /scvhistory/jn3801.htm (the same note is under AP3314, AP3101 and DO3801): "the Great Flood (aka Los Angeles Flood) of 1938 hit the greater Los Angeles area hardest overnight on March 1-2. By the time the water receded, 5,601 buildings had been destroyed and 113 to 115 Southland residents were killed." "Considered a 50-year flood, it started Feb. 27, 1938, when a storm system moved in from the Pacific Ocean and hit the San Gabriel Mountains." "Driven by gale-force winds, it hit March 1 at about 8:45 p.m., dumping 10 inches of rain in the lowlands and at least 32 inches in the mountains." "The Santa Clarita Valley wasn't spared. The Santa Clara River overflowed. Roads and bridges were wiped out and ranch buildings situated along the riverbank floated away." "In response to the 1938 flood, Congress passed the Flood Control Act of 1941, which authorized the U.S. Army Corps of Engineers to channelize the Los Angeles River and parts of the Santa Ana."
3. Alan Pollack, "Great Flood of 1938: Skies Open Up with Deadly Force," Heritage Junction Dispatch, July-August 2019, as carried on SCVHistory.com, /scvhistory/pollack0719greatflood.htm: "It all started February 27, 1938, when 1.42 inches of rain fell on the Los Angeles area." "Approximately 115 people perished in the floods of 1938 in Los Angeles. Damage was estimated at $78 million." Of the Saugus rodeo grounds: "Shortly thereafter, Paul Hill, owner of the rodeo at that time, lost the property to bank repossession. Ownership passed to William Bonelli in 1939. He eventually transformed the rodeo arena into the Saugus Speedway." Pollack names his sources: "Los Angeles Times, February 28 - March 5, 1938; Newhall Signal, March 3, 1938."
4. Alan Pollack, "Great Flood of 1938: Skies Open Up with Deadly Force," Heritage Junction Dispatch, July-August 2019, as carried on SCVHistory.com, /scvhistory/pollack0719greatflood.htm, "Damage in the Santa Clarita Valley": "Roads were damaged all across the valley as water roared down from the surrounding canyons." "The tracks of the Southern Pacific Railroad suffered extensive damage. Several bridges were out in the Saugus area, as well as the Pico Bridge west of Newhall. Multiple cave ins occurred in the Newhall Auto Tunnel causing several closures to auto traffic. The entire valley was illuminated by a fire occurring at the Edison Saugus substation when a gas main broke." "Much of the town of Castaic was reported to be flooded. A major traffic artery through the valley was blocked when the bridge over Castaic Creek was washed out just west of Castaic Junction." "The home of the James Fryer family was washed away by the flood waters of the Santa Clara River in Soledad Canyon. The school at Honby lost all of its outbuildings. Two big bridges on Soledad Canyon road were washed out. Most of the buildings at the Nadeau Deer Farm in today's Canyon Country were destroyed."
5. "Great Flood of 1938: March Storms Took Big Toll," California Highways and Public Works, the journal of the State Division of Highways, Vol. 16 No. 4, April 1938, LW3356, as carried on SCVHistory.com, /scvhistory/lw3356.htm and /scvhistory/files/lw3356/lw3356.pdf. The abstract: "Three separate storms during the period December 11 to March 4, damaged State highways and structures to the extent of $8,000,000." A caption: "Embankment was washed out together with half the Newhall Saugus highway when a creek became a river." The text: Castaic Creek "carried away 200 feet of the bridge west of Castaic,".
6. Leon Worden, caption to JN3801, "Hill's Ranch Rodeo (Saugus Speedway)," from the collection of Jennifer Jones, as carried on SCVHistory.com, /scvhistory/jn3801.htm: "Paul Hill's Saugus Rodeo grounds, showing the effects of the Great Flood of March 2, 1938, when the Santa Clara River overflowed and filled Hill's ranch home and arena with mud and debris." The Los Angeles Times, April 24, 1938, "Runaway Horse Plunges Through Rodeo Grandstand," as carried on SCVHistory.com, /scvhistory/tlp_lat042338.htm: "Special Southern Pacific trains carried visitors directly to the new arena", "the old buildings having collapsed during the March floods."
7. Leon Worden, LW3577, "Aerial Views: Progression of Ernie Hickson's Placeritos (Monogram) Ranch, 1936-1952," as carried on SCVHistory.com, /scvhistory/lw3577.htm. Hickson moved his "buildings west down Placerita Canyon Road and set up the Placeritos Ranch" and "erected his buildings on the south bank of the creek". Of the 1938 aerial photograph: "The violent downpour shifted the course of the creek south. Now Hickson's movie town is on the north bank of the creek."
8. Leon Worden, "Ballfield Named for CCC Worker Killed While Battling SCV Brush Fire, 1933," as carried on SCVHistory.com, /scvhistory/arnettefield_cccbearcanyon1933.htm: "The composite photograph was made between May and July of 1938, shortly after the Great Flood of March 2. The camp required repairs". His note 5 quotes The Signal, April 14, 1938: "Captain Floyd B. Rutherford of the district staff made the monthly inspection at Camp Bear Canyon on [March 31]. He gave the outfit a fine rating and expressed surprise at the speed with which the work of rehabilitation had been accomplished following the flood." The bracketed date is Leon Worden's.
9. A.B. Perkins, "Tales of Lang and Soledad," March 23, 1961, article #1444 in this archive (/scvhistory/perkins032361.htm), of the sulphur springs at "Rivers End," "which attractive resort is on the old Lang ranch": "They mudded up in the flood of 1938 and were never cleaned out."
10. Leon Worden's captions to three Acton photographs, as carried on SCVHistory.com. AP3314, /scvhistory/ap3314.htm: "The Santa Clara River floods. Unidentified photograph; probably in Acton and probably the Great Flood of March 2, 1938". AP3101, /scvhistory/ap3101.htm: "Structures in the Acton area are lost as the banks of the Santa Clara River crumble, probably in the flood of March 2, 1938." DO3801, /scvhistory/do3801.htm: "Flooding at the intersection of Crown Valley Road and Cory Avenue. Most likely the Great Flood of March 2, 1938." None of the three is in Craft.
11. Leon Worden, caption to LW3067, "Southern Pacific Locomotive Derailed, Overturned 3-25-1938," photograph #4871 in this archive (/scvhistory/lw3067.htm): "Collateral damage from the Great Flood of March 2, 1938: A Southern Pacific locomotive rests on its side after it hit an open switch outside of Saugus." The Los Angeles Times, March 26, 1938, "Switch Derails Espee Engine," on the same page: "Striking an open switch three miles north of Saugus last night, the engine of a work train returning from the north, where a crew of workmen had been repairing flood damages, was derailed and two men were injured slightly."
12. "Army Corps to Turn Santa Clara River Into L.A.-Style Concrete Channel," a public hearing at Friendly Valley Country Club, August 12, 1968, with The Signal's report of August 14, 1968, as carried on SCVHistory.com, /scvhistory/sg19680812armycorps.htm. The Signal: "Then he launched into a discussion of the floods of the last six major floods dating back to 1938." "Col. Irvine then discussed the Army's plan to build reinforced concrete channels, like those of the Los Angeles River, along the 28 miles of the Santa Clara River and its branches." Leon Worden's webmaster's note: "USACE's channelization plan did not come to fruition."
13. Lyle Sanard, District Inspector, California Highway Patrol, "Highway Patrol Active in Rescues During Flood," The California Highway Patrolman, April 1938, HB3802, as carried on SCVHistory.com, /scvhistory/hb3802.htm. It covers San Bernardino and Riverside counties, not this valley.
14. F.W. Panhorst, Bridge Engineer, State Division of Highways, "Role of Highways in Recent California Floods," Civil Engineering, Vol. 8 No. 8, August 1938, as carried on SCVHistory.com, /scvhistory/panhorst0838.htm: "In southern California, the floods came in March. From February 26 through February 28, approximately 5 in. of rain fell throughout the area and thoroughly saturated the ground. A more severe storm swept the entire area on March 2 and 3."

**Editor's note, The dates (bottom):** Leon Worden (2013) and Alan Pollack (2019) date the start of the storm to February 27, 1938. F.W. Panhorst, writing in August 1938, gives rain from February 26 through February 28 and the more severe storm on March 2 and 3. Leon Worden has the flood at its worst overnight on March 1 to 2; his chronology and his photograph captions give March 2. The State's highway journal of April 1938 counts the storm period to March 4. The record gives March 2, Leon Worden's date, and February 27 as the start. No source on the mirror gives the day the flood ended in this valley, so the record gives no end date.

**Editor's note, The number of dead (bottom):** Leon Worden gives 113 to 115 dead across Southern California (2013, on the flood photographs, and in his rainfall notes), and at least 113 in the greater Los Angeles area on another page (LW3677). Alan Pollack gives about 115 in the Los Angeles floods, after the Los Angeles Times counted thirty dead on March 3 and 62 the next day. None of these sources reports a death in the Santa Clarita Valley, and none was found on the mirror. Buildings lost: 5,601 destroyed (Leon Worden), or 5,600 homes and businesses (Alan Pollack).

### recordDates

| Printed | ISO | Precision | What happened | Confirmed |
| --- | --- | --- | --- | --- |
| February 27, 1938 | 1938-02-27 | day | the storm begins (Leon Worden 2013; Alan Pollack 2019) | no |
| March 1, 1938 | 1938-03-01 | day | the second storm arrives at about 8:45 p.m. | no |
| March 2, 1938 | 1938-03-02 | day | the Great Flood, the date in Leon Worden's chronology | no |
| March 25, 1938 | 1938-03-25 | day | a work train returning from flood repairs derails near Saugus | no |
| March 31, 1938 | 1938-03-31 | day | Camp Bear Canyon inspected after its repairs (The Signal, April 14, 1938) | no |
| April 23, 1938 | 1938-04-23 | day | the Saugus rodeo opens in its new arena | no |
| 1941 | 1941-01-01 | year | Congress passes the Flood Control Act of 1941 | no |

### Relations

- **eventPlaces:** #15596 Santa Clara River (notes 2, 4, 6; it overflowed through the valley)
- **eventPlaces:** #643 Saugus Speedway (notes 6, 3; the rodeo grounds the flood wrecked, later the speedway)
- **eventPlaces:** #15886 Castaic Creek (notes 4, 5; its bridge west of Castaic Junction washed out)
- **eventPlaces:** #18529 Soledad Canyon (notes 4; the Fryer home and two bridges on Soledad Canyon Road lost)
- **eventPlaces:** #18565 Placerita Canyon (notes 7; the flood moved the creek at Hickson's movie ranch)
- **eventOrganizations:** #16101 Southern Pacific Railroad (notes 4, 11; its track and bridges damaged; its work train derailed during the repairs)
- **eventArticles:** #1444 Tales of Lang and Soledad (named in note 9)
- **cited, not a document, not related:** #1444 articles "Tales of Lang and Soledad"
- **cited, not a document, not related:** #4871 photographs "Southern Pacific Locomotive Derailed, Overturned 3-25-1938."
- **photograph #4871 LW3067** takes the event in photoEvents (named in note 11; )
- **relatedEvents:** none set; the draft says: None. The St. Francis Dam disaster (#31342) is compared with this flood in Leon Worden's note and in the State highway journal, but only as a comparison; not linked.
- **footnotesOn:** not set. On this site it means "the notes were published on another record" (create_saugus_2019_event_2026_10_05.php); the documents the draft listed there go to sourceDocuments instead.
- **Not written:** every other record. The draft's forNathan recommendations about other records (new place or organization records, changes to person records, image attachments) are not acted on.

### Research leads (researchLeads, not shown on the page)

- Photographs on the mirror, none in Craft: JN3801 (the Saugus rodeo grounds after the flood, the best image of the event in the valley), AP3314, AP3101 and DO3801 (Acton, each dated to the flood as probable), CY3801 (a Cheney child on the Woolridge footbridge at Mentryville; Leon Worden says it "could be" the flood, and the date is a guess) and LW3577 (Hickson's ranch from the air, 1938). Each would need to be brought into Craft before it can take the event.
- Pollack names The Newhall Signal of March 3, 1938, as a source. No page of that issue was found on the mirror (searched file names and text for 1938 and March 3). A transcription would be the contemporary local account.
- Meryl Adams, Heritage Happenings (1988), page 301, has a photograph of the Acton Hotel in the 1938 flood (DO3801's caption). The Acton Hotel has a record (#20109). Not read; the book is not on the mirror.
- The Sheriff's air squadron dropped food, water and medicine to marooned residents in the foothills during the flood (LW3349, photograph #5191, Eugene Biscailuz). The page does not say any of this was in the valley; left out.
- Lopez 1974 rainfall notes (/scvhistory/lopezrobert1974rainfall.htm) have a table of monthly rain for 1932-1977 from an unknown source, and Sandberg station data; neither read for the 1938 figures.
- Searched the mirror for the phrases flood of 1938, 1938 flood, Great Flood and March 2, 1938, and the flood index in general.htm; searched Craft photographs, documents and articles for flood titles and texts. No Craft document records this flood.

## From the draft's forNathan (approved with the draft; listed for the record)

- 1. Title. "Great Flood of 1938" is Leon Worden's name for it (his index heading reads "Great Flood of March 2, 1938"). Say if you want another.
- 2. Photographs. Only LW3067 (#4871, the derailment of March 25) is in Craft, and Leon Worden ties it to the flood. JN3801, AP3314, AP3101 and DO3801 are not in Craft; importing them is a separate step, after which a later run could add them.
- 3. William Bonelli. Person #30199 is "William G. Bonelli Jr."; the sources here say only "William Bonelli" bought the rodeo grounds in 1939, so no person is related. Say which Bonelli, if any.
- 4. Paul Hill has no person record. He appears on many Saugus Speedway pages; not recommended for a record on this evidence alone.
- 5. The relation to Placerita Canyon (#18565) rests on LW3577, which places Hickson's ranch on Placerita Canyon Road. Leon Worden files LW3577 under Melody Ranch (#615); not related, since the footnote does not name it.
