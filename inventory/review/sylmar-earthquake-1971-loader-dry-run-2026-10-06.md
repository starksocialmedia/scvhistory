# Sylmar Earthquake: the loader's dry run, 6 October 2026

Written by `scripts/import/create_sylmar_earthquake_1971_event_2026_10_06.php` (with `scripts/import/_event_from_draft_2026_10_06.php`) in a dry run. A dry run writes nothing to Craft. The event is read from `inventory/review/sylmar-earthquake-1971-draft-2026-10-06.json` (SHA-256 `3e201a163fd69516...`), the draft of 6 October 2026, written in the v2 shape with every quotation checked word for word that day. Nathan approved it for applying on 6 October 2026.

**Refusals:** none.

## The run

```
DRY RUN create_sylmar_earthquake_1971_event_2026_10_06.php
==============================================================================
EVENT: #31893 "Sylmar Earthquake" exists, not recreated
    content advisory: yes, the first editor note, top
    historicalEra: #167 Incorporation Struggle (1965–1986)
    historicalPeriod: #180 1970-1979
    recordTags: none
    neighborhood: #203 Sand Canyon; #199 Newhall
    eventPersons: none
    eventPlaces: #934 Newhall Pass interchange (notes 5, 8, 9)
    eventOrganizations: #380 Henry Mayo Newhall Memorial Hospital (notes 7); #29850 Santa Clarita Community College District (notes 10)
    eventFallenOfficers: none
    eventArticles: #2129 52. Servicing the Traveler (note 13); #12623 History: The Finest Hotel South Of San Francisco. (note 13); #1438 History of Downtown Newhall (note 14)
    articles held, no footnote names them: none
    sourceDocuments: none cited
        nothing to set
    cited records that are not documents (not in sourceDocuments): #5689 photographs "2-9-1971 Earthquake"; #3245 photographs "M=6.0+ Southern California Earthquakes, 1912-1971"; #3247 photographs "2-9-1971 Sylmar Earthquake: Main Shock (in SCV) & Aftershocks"; #4973 photographs "Collapsed 210 Freeway Bridge in Newhall Pass, 2-9-1971."; #4493 photographs "5/14 Freeway Overpass, 2-9-1971"; #4977 photographs "Collapsed Freeway Bridge in Newhall Pass, 1971."; #4975 photographs "Workers Attempt to Save Van Norman Reservoir, 2-9-1971."; #2129 articles "52. Servicing the Traveler"; #12623 articles "History: The Finest Hotel South Of San Francisco."; #5217 photographs "Wall Calendar from Albert Swall's Newhall Cash Store, 1915."; #1438 articles "History of Downtown Newhall"; #4809 photographs "Damage in Liquor Store, 2-9-1971."; #4095 photographs "Freeway Damage & Repairs, I-5 & 210"
    featuredImage: asset 11610 (lw3158_large.jpg)
    photograph #5689 LW7102: photoEvents has it (named in note 1)
    photograph #3247 LW2316c: photoEvents has it (named in note 5, 6, 16)
    photograph #3245 LW2316: photoEvents has it (named in note 4)
    photograph #4973 LW3158: photoEvents has it (named in note 8)
    photograph #4493 LW2794: photoEvents has it (named in note 9)
    photograph #4977 LW3160: photoEvents has it (named in note 11)
    photograph #4095 LW2548a: photoEvents has it (named in note 17)
    photograph #4975 LW3159: photoEvents has it (named in note 12)
    photograph #4809 LW3019: photoEvents has it (named in note 15)
    photograph #4089 LW2547a: HELD, no footnote cites it (the draft says so)
    other records: nothing written
    REFUSED: none
```

## Quotation check

73 quotations in the v2 draft (every quoted passage, in double or single quotation marks, in the fields (body, significance, footnotes, editor notes, recordDates labels, relation ties, photograph rows, image notes, the literature list) and the research leads; forNathan is not loaded and was not checked) were checked word for word: 73 PASS, 0 CORRECTED from v1, 0 FAIL. Ellipses: the draft has none. Checked against: mirror pages on /Volumes/Reggie/SCVHistory/scvhistory.com (HTML read as latin-1; PDFs by pdftotext), and Craft for record text and titles, by read-only queries.

"Terminal punctuation only" means the quotation stops where the source's sentence goes on and closes with a period or comma of its own; no word is changed.

| # | Where | Quotation | Result | Page | Note |
| --- | --- | --- | --- | --- | --- |
| 1 | footnotes[0] | 2-9-1971 Earthquake, | PASS | /scvhistory/lw7102.htm; craft #5689 |  |
| 2 | footnotes[0] | of Tuesday, Feb. 9, 1971, was actually centered in Iron Canyon, in the Sand Canyon area of Canyon Country, as seen on this official instrume [cut here for the table] | PASS | /scvhistory/lw7102.htm; craft #5689 |  |
| 3 | footnotes[0] | It struck at 6:00:41 a.m. and measured 6.7 on the Richter scale (revised to 6.6). | PASS | /scvhistory/lw7102.htm; craft #5689 |  |
| 4 | footnotes[1] | USDA Film: 1971 Sylmar Earthquake, | PASS | /scvhistory/sylmarquake1971_usda.htm |  |
| 5 | footnotes[1] | EARTHQUAKE! Story of the Sylmar Earthquake of February 9, 1971 | PASS | /scvhistory/sylmarquake1971_usda.htm |  |
| 6 | footnotes[1] | The Sylmar-San Fernando Earthquake struck the San Fernando and Santa Clarita Valleys on February 9, 1971, when a powerful pre-dawn earthquak [cut here for the table] | PASS | /scvhistory/sylmarquake1971_usda.htm |  |
| 7 | footnotes[1] | claimed 65 lives, injured thousands, and caused widespread destruction throughout the San Fernando Valley, with additional impacts to commun [cut here for the table] | PASS | /scvhistory/sylmarquake1971_usda.htm |  |
| 8 | footnotes[1] | At least one confirmed fatality occurred in the Santa Clarita Valley. | PASS | /scvhistory/sylmarquake1971_usda.htm |  |
| 9 | footnotes[1] | documents the crisis at Van Norman Dam, which prompted the evacuation of tens of thousands of residents living downstream. | PASS | /scvhistory/sylmarquake1971_usda.htm |  |
| 10 | footnotes[2] | February 9, 5:59 a.m.: 6.5 magnitude Sylmar earthquake (actually centered in Iron Canyon section of Sand Canyon). | PASS | /scvhistory/timeline.htm |  |
| 11 | footnotes[3] | M=6.0+ Southern California Earthquakes, 1912-1971, | PASS | /scvhistory/lw2316.htm; craft #3245 |  |
| 12 | footnotes[3] | The San Fernando earthquake is commonly known as the Sylmar earthquake. Its epicenter was in the Santa Clarita Valley. | PASS | /scvhistory/lw2316.htm; craft #3245 |  |
| 13 | footnotes[3] | The Legislature's report gives a magnitude of 6.4, but it was subsequently assigned a magnitude of 6.6. | PASS | /scvhistory/lw2316.htm; craft #3245 |  |
| 14 | footnotes[4] | 2-9-1971 Sylmar Earthquake: Main Shock (in SCV) & Aftershocks, | PASS | /scvhistory/lw2316c.htm; craft #3247 |  |
| 15 | footnotes[4] | Because the fault was five miles below the epicenter and it surfaces at Sylmar/San Fernando, so the damage was greater there. | PASS | /scvhistory/lw2316c.htm; craft #3247 |  |
| 16 | footnotes[4] | when the Olive View Medical Center went down. | PASS | /scvhistory/lw2316c.htm; craft #3247 |  |
| 17 | footnotes[4] | lasted about 60 seconds, killed 65 people (primarily in the San Fernando Valley) and caused more than half a billion dollars in damage to bo [cut here for the table] | PASS | /scvhistory/lw2316c.htm; craft #3247 |  |
| 18 | footnotes[5] | although the fault reaches the surface near Sylmar, it lies at a depth of about 4 miles under Newhall, several miles to the north | PASS | /scvhistory/lw2316c.htm |  |
| 19 | footnotes[5] | the fracturing then propagated southward and upward along the fault plane until it actually broke the ground surface in Sylmar and San Ferna [cut here for the table] | PASS | /scvhistory/lw2316c.htm |  |
| 20 | footnotes[5] | The shaking was heavier in Sylmar than at the epicenter probably for two reasons | PASS | /scvhistory/lw2316c.htm |  |
| 21 | footnotes[6] | Volunteers Pave the Way to Henry Mayo Hospital, | PASS | /scvhistory/hmnmhprehistory.htm |  |
| 22 | footnotes[6] | of Feb. 9, 1971, killed 65 people and caused $500 million in property damage. Most deaths and injuries occurred in the San Fernando Valley,  [cut here for the table] | PASS | /scvhistory/hmnmhprehistory.htm |  |
| 23 | footnotes[6] | Plans for Henry Mayo Newhall Memorial Hospital were nearly complete when the earthquake struck; now they would have to be reworked to meet e [cut here for the table] | PASS | /scvhistory/hmnmhprehistory.htm |  |
| 24 | footnotes[6] | The epicenter was in the Iron Canyon area of Sand Canyon in the eastern Santa Clarita Valley. | PASS | /scvhistory/hmnmhprehistory.htm |  |
| 25 | footnotes[7] | Collapsed 210 Freeway Bridge in Newhall Pass, 2-9-1971, | PASS | /scvhistory/lw3158.htm; craft #4973 |  |
| 26 | footnotes[7] | The westbound Interstate 210 overpass has fallen onto Interstate 5 in the Newhall Pass. | PASS | /scvhistory/lw3158.htm; craft #4973 |  |
| 27 | footnotes[8] | 5/14 Freeway Overpass, 2-9-1971, | PASS | /scvhistory/lw2794.htm; craft #4493 |  |
| 28 | footnotes[8] | Freeway overpass at the 5-14 split (Newhall Pass) as it appeared on the day of the Sylmar-San Fernando Earthquake. | PASS | /scvhistory/lw2794.htm; craft #4493 |  |
| 29 | footnotes[8] | The earthquake that hit Southern California caused part of massive elevated freeway to collapse, burying the 80-ton crane (bottom) which had [cut here for the table] | PASS | /scvhistory/lw2794.htm; craft #4493 |  |
| 30 | footnotes[9] | Early History of College of the Canyons, | PASS | /scvhistory/aa7401.htm |  |
| 31 | footnotes[9] | No permanent campus structures yet existed, but the architectural plans for the buildings on the drawing board were beefed up significantly  [cut here for the table] | PASS | /scvhistory/aa7401.htm |  |
| 32 | footnotes[9] | The Student Center was supposed to be two stories, but everything changed the day of the Sylmar earthquake, | PASS | /scvhistory/aa7401.htm |  |
| 33 | footnotes[9] | was strong enough to topple the lofty Interstate 5-Highway 14 connectors that were then under construction | PASS | /scvhistory/aa7401.htm |  |
| 34 | footnotes[9] | librarian Jan Keller estimated that some 10,000 volumes lay buried under displaced steel shelves. It took two days to sort through the mess  [cut here for the table] | PASS | /scvhistory/aa7401.htm |  |
| 35 | footnotes[9] | The scaled-back Student Center, now relegated to a single story in the interest of earthquake safety, opened in February of 1975. | PASS | /scvhistory/aa7401.htm |  |
| 36 | footnotes[10] | Collapsed Freeway Bridge in Newhall Pass, 1971, | PASS | /scvhistory/lw3160.htm; craft #4977 |  |
| 37 | footnotes[10] | A collapsed freeway bridge in the Newhall Pass. | PASS | /scvhistory/lw3160.htm; craft #4977 |  |
| 38 | footnotes[10] | In all, 67 bridges on five major freeways were damaged. | PASS | /scvhistory/lw3160.htm; craft #4977 |  |
| 39 | footnotes[11] | Workers Attempt to Save Van Norman Reservoir, 2-9-1971, | PASS | /scvhistory/lw3159.htm; craft #4975 |  |
| 40 | footnotes[11] | A helicopter hovers while workers attempt to avert a flood as waves erode the earthen walls of LADWP's Van Norman Reservoir. | PASS | /scvhistory/lw3159.htm; craft #4975 |  |
| 41 | footnotes[11] | Water lapped at the top of the damaged Van Norman Lakes Reservoir in the San Fernando Valley while workmen sandbagged a leaking portion in a [cut here for the table] | PASS | /scvhistory/lw3159.htm; craft #4975 |  |
| 42 | footnotes[12] | March 13: Brick front of Newhall Pharmacy (ex-Swall Hotel at Market & Spruce/Main), damaged in Feb. 9 quake, collapses. | PASS | /scvhistory/timeline.htm |  |
| 43 | footnotes[12] | Servicing the Traveler, | PASS | /scvhistory/signal/reynolds/part52.html; craft #2129 |  |
| 44 | footnotes[12] | The Swall Hotel, later known as Newhall Pharmacy, was heavily damaged in the February 9, 1971 Sylmar Earthquake. When it was rebuilt, its br [cut here for the table] | PASS | /scvhistory/signal/reynolds/part52.html; craft #2129 |  |
| 45 | footnotes[12] | The Finest Hotel South Of San Francisco, | PASS | craft #12623 |  |
| 46 | footnotes[12] | only to be destroyed again in the Sylmar Earthquake of February 9, 1971. | PASS | craft #12623 |  |
| 47 | footnotes[12] | In the 1971 earthquake the original brick building was essentially destroyed. | PASS | /scvhistory/lw3373.htm; craft #5217 |  |
| 48 | footnotes[13] | Timeline: First Presbyterian Church, | PASS | /scvhistory/hs8601.htm |  |
| 49 | footnotes[13] | February 9. Earthquake damages Church structure. Cheaper to build a new one than restore the old. | PASS | /scvhistory/hs8601.htm |  |
| 50 | footnotes[13] | March 7, groundbreaking ceremonies. | PASS | /scvhistory/hs8601.htm |  |
| 51 | footnotes[13] | February 6. Third church dedicated. | PASS | /scvhistory/hs8601.htm |  |
| 52 | footnotes[13] | In 1923, the wooden chapel was moved a couple of hundred feet toward Eighth Street and transformed into a two-story brick structure that was [cut here for the table] | PASS | /scvhistory/firstpresbyterian1976cookbook.htm |  |
| 53 | footnotes[13] | March 7: Groundbreaking for new First Presbyterian Church in Newhall (former structure heavily damaged in 1971 earthquake). | PASS | /scvhistory/timeline.htm |  |
| 54 | footnotes[13] | History of Downtown Newhall | PASS | craft #1438 |  |
| 55 | footnotes[13] | It should be noted that the church building that existed in 1958 was razed after the 1971 earthquake, and a third church building was erecte [cut here for the table] | PASS | craft #1438 |  |
| 56 | footnotes[14] | Damage in Liquor Store, 2-9-1971, | PASS | /scvhistory/lw3019.htm; craft #4809 |  |
| 57 | footnotes[14] | A jumble of liquor bottles littered the floor of a store in Newhall Tuesday after an earthquake jolted the southern California area. | PASS | /scvhistory/lw3019.htm; craft #4809 |  |
| 58 | footnotes[14] | According to members of the Dillenbeck family (pers. comm. 2017), who ran grocery stores in Newhall and Canyon Country at the time of the qu [cut here for the table] | PASS | /scvhistory/lw3019.htm; craft #4809 |  |
| 59 | footnotes[14] | Milan's Liquor and Deli at 8534 Foothill Blvd. in Sunland | PASS | /scvhistory/lw3019.htm; craft #4809 |  |
| 60 | footnotes[15] | In response, the Joint Committee established a | PASS | /scvhistory/lw2316.htm; /scvhistory/lw2316c.htm; /scvhistory/lw3158.htm |  |
| 61 | footnotes[15] | with the goal of evaluating its effects and learning from it. | PASS | /scvhistory/lw2316.htm; /scvhistory/lw2316c.htm; /scvhistory/lw3158.htm |  |
| 62 | footnotes[15] | Chaired by Assemblyman James A. Hayes of Long Beach, the three-person subcommittee included Democratic Sen. Joseph M. Kennick, also of Long  [cut here for the table] | PASS | /scvhistory/lw2316.htm; /scvhistory/lw2316c.htm; /scvhistory/lw3158.htm |  |
| 63 | footnotes[15] | Just three months earlier, Keysor, chairman of the board of Keysor-Century Records (aka Keysor-Century Corp.) in Saugus, had been elected to [cut here for the table] | PASS | /scvhistory/lw2316.htm; /scvhistory/lw2316c.htm; /scvhistory/lw3158.htm |  |
| 64 | footnotes[15] | In July 1972, the Special Subcommittee published a 132-page report | PASS | /scvhistory/lw2316.htm; /scvhistory/lw2316c.htm; /scvhistory/lw3158.htm |  |
| 65 | footnotes[16] | Freeway Damage & Repairs, I-5 & 210, | PASS | /scvhistory/lw2548a.htm; craft #4095 |  |
| 66 | footnotes[16] | Freeway damage and repairs in Sylmar after the earthquake of Feb. 9, 1971. | PASS | /scvhistory/lw2548a.htm; craft #4095 |  |
| 67 | editorNotes[2].note | now called Bonelli Hall, | PASS | /scvhistory/aa7401.htm |  |
| 68 | editorNotes[2].note | opened in early 1974. | PASS | /scvhistory/aa7401.htm |  |
| 69 | researchLeads[0] | At least one confirmed fatality occurred in the Santa Clarita Valley. | PASS | /scvhistory/sylmarquake1971_usda.htm |  |
| 70 | researchLeads[0] | quake | PASS | /scvhistory/sg19191999.htm; /scvhistory/sylmarquake1971_usda.htm |  |
| 71 | researchLeads[1] | destroyed in the 1971 Sylmar-San Fernando earthquake when the second floor collapsed | PASS | /scvhistory/ap3113.htm |  |
| 72 | researchLeads[1] | heavily damaged during the 1971 earthquake | PASS | /scvhistory/hs0100.htm |  |
| 73 | researchLeads[2] | felled the hospital's four stairwell wings and its parking structure. | PASS | /scvhistory/hb6201.htm |  |

## Changes from v1 to v2

None.

## The event as it would read

**Editor's note, Content advisory (top):** This record concerns an earthquake in which 65 people died, most of them in the San Fernando Valley, and describes the damage it did.

- **eventDate:** February 9, 1971
- **eventDateEdtf:** 1971-02-09
- **eventDateStart:** (empty)
- **eventDateEnd:** (empty)
- **startEvidence:** contemporary
- **eventChlNumber:** (empty)
- **eventSignificance:** The earthquake of February 9, 1971, known as the Sylmar or San Fernando earthquake, was centered in the Iron Canyon section of Sand Canyon in the Santa Clarita Valley. It killed 65 people, most of them in the San Fernando Valley, brought down the new freeway bridges in the Newhall Pass, and heavily damaged brick buildings in downtown Newhall.
- **historicalEra:** #167 Incorporation Struggle (1965–1986)
- **historicalPeriod:** #180 1970-1979
- **recordTags:** (empty)
- **neighborhood:** #203 Sand Canyon; #199 Newhall
- **featuredImage:** asset 11610 (lw3158_large.jpg), LW3158, UPI Telephoto: the westbound Interstate 210 overpass fallen onto Interstate 5 in the Newhall Pass, February 9, 1971 (in archiveMedia; not attached to photograph #4973)
- **bandImage:** (empty) none proposed

Before dawn on February 9, 1971, an earthquake struck the San Fernando and Santa Clarita valleys.[1][2] It is called the Sylmar or the San Fernando earthquake, but its epicenter was in the Santa Clarita Valley, in the Iron Canyon section of Sand Canyon.[1][3][4] Leon Worden explains the name: the fault lies about five miles below the epicenter and comes to the surface at Sylmar and San Fernando, where the damage was greater, and the press took up the name Sylmar when the Olive View Medical Center collapsed.[5][6] The shaking lasted about 60 seconds. It killed 65 people, most of them in the San Fernando Valley, and caused more than half a billion dollars in damage in the two valleys.[5][7] SCVHistory.com's page for a government film of the disaster says that at least one death was confirmed in the Santa Clarita Valley.[2]

In the Newhall Pass the new freeway bridges fell.[5] The westbound Interstate 210 overpass came down onto Interstate 5, and at the junction of Interstate 5 and State Route 14, connectors still under construction collapsed onto an 80-ton crane; the wire service's caption said no one was killed there.[8][9][10] In all, 67 bridges on five major freeways were damaged.[11] To the south, workers sandbagged the damaged Van Norman Reservoir in the San Fernando Valley, and tens of thousands of people living downstream were evacuated.[12][2] Repairs to the freeways at Sylmar were still under way that May.[17]

In Newhall, the brick Swall Hotel building at Spruce and Market streets, by then the Newhall Pharmacy, was heavily damaged. Its front collapsed on March 13, and the building was rebuilt with a stucco front.[13] The First Presbyterian Church's two-story brick building of 1923 was severely damaged; the church found it cheaper to build again than to restore it, broke ground for a new church on March 7, 1976, and dedicated it on February 6, 1977.[14] A wire photograph of bottles strewn across a liquor store's floor was published as damage in Newhall, though a family who ran grocery stores in the valley later said another paper placed the store in Sunland.[15]

College of the Canyons had no permanent buildings yet. The quake buried some 10,000 of its library's books under fallen shelves, and its building plans were strengthened: the Student Center, meant to have two stories, opened with one in February 1975.[10] Plans for Henry Mayo Newhall Memorial Hospital, nearly complete when the earthquake struck, had to be reworked to meet new seismic safety requirements.[7]

The Legislature's Joint Committee on Seismic Safety formed a special subcommittee to study the earthquake. Its three members included the Santa Clarita Valley's assemblyman, Jim Keysor, elected three months before, and its 132-page report was published in July 1972.[16] The U.S. Department of Agriculture's Motion Picture Service filmed the aftermath for the federal government.[2]

1. Leon Worden, caption to LW7102, "2-9-1971 Earthquake," instrumental intensity map, photograph #5689 in this archive, as carried on SCVHistory.com, /scvhistory/lw7102.htm: the earthquake "of Tuesday, Feb. 9, 1971, was actually centered in Iron Canyon, in the Sand Canyon area of Canyon Country, as seen on this official instrumental intensity map." "It struck at 6:00:41 a.m. and measured 6.7 on the Richter scale (revised to 6.6)."
2. SCVHistory.com, "USDA Film: 1971 Sylmar Earthquake," the page for the film "EARTHQUAKE! Story of the Sylmar Earthquake of February 9, 1971" (so titled on the page), produced by the Motion Picture Service, U.S. Department of Agriculture, for the President's Office of Emergency Preparedness and the Defense Civil Preparedness Agency, as carried on SCVHistory.com, /scvhistory/sylmarquake1971_usda.htm (the page is unsigned): "The Sylmar-San Fernando Earthquake struck the San Fernando and Santa Clarita Valleys on February 9, 1971, when a powerful pre-dawn earthquake tore through Southern California, collapsing freeways, hospitals, and critical infrastructure across the region." The earthquake "claimed 65 lives, injured thousands, and caused widespread destruction throughout the San Fernando Valley, with additional impacts to communities to the north, including the Santa Clarita Valley." "At least one confirmed fatality occurred in the Santa Clarita Valley." The film "documents the crisis at Van Norman Dam, which prompted the evacuation of tens of thousands of residents living downstream."
3. SCVHistory.com timeline, as carried on SCVHistory.com, /scvhistory/timeline.htm, 1971: "February 9, 5:59 a.m.: 6.5 magnitude Sylmar earthquake (actually centered in Iron Canyon section of Sand Canyon)." The timeline names no source for the line.
4. Leon Worden, note to LW2316, "M=6.0+ Southern California Earthquakes, 1912-1971," photograph #3245 in this archive, as carried on SCVHistory.com, /scvhistory/lw2316.htm: "The San Fernando earthquake is commonly known as the Sylmar earthquake. Its epicenter was in the Santa Clarita Valley." "The Legislature's report gives a magnitude of 6.4, but it was subsequently assigned a magnitude of 6.6."
5. Leon Worden, note to LW2316c, "2-9-1971 Sylmar Earthquake: Main Shock (in SCV) & Aftershocks," photograph #3247 in this archive, as carried on SCVHistory.com, /scvhistory/lw2316c.htm: "Because the fault was five miles below the epicenter and it surfaces at Sylmar/San Fernando, so the damage was greater there." The media took up the name "when the Olive View Medical Center went down." The earthquake "lasted about 60 seconds, killed 65 people (primarily in the San Fernando Valley) and caused more than half a billion dollars in damage to both valleys, including the collapse of the fairly new freeway bridges in the Newhall Pass."
6. Clarence R. Allen, California Institute of Technology, in Special Subcommittee of the Joint Committee on Seismic Safety, California Legislature, The San Fernando Earthquake of February 9, 1971, and Public Policy, July 1972, pp. 4-6, as carried on SCVHistory.com, /scvhistory/lw2316c.htm: "although the fault reaches the surface near Sylmar, it lies at a depth of about 4 miles under Newhall, several miles to the north"; "the fracturing then propagated southward and upward along the fault plane until it actually broke the ground surface in Sylmar and San Fernando"; "The shaking was heavier in Sylmar than at the epicenter probably for two reasons".
7. Leon Worden, "Volunteers Pave the Way to Henry Mayo Hospital," 2012, as carried on SCVHistory.com, /scvhistory/hmnmhprehistory.htm: the earthquake "of Feb. 9, 1971, killed 65 people and caused $500 million in property damage. Most deaths and injuries occurred in the San Fernando Valley, alleviating the strain that would otherwise have befallen the local hospitals". "Plans for Henry Mayo Newhall Memorial Hospital were nearly complete when the earthquake struck; now they would have to be reworked to meet evolving seismic safety requirements." His note 7: "The epicenter was in the Iron Canyon area of Sand Canyon in the eastern Santa Clarita Valley."
8. Caption to LW3158, "Collapsed 210 Freeway Bridge in Newhall Pass, 2-9-1971," UPI Telephoto, photograph #4973 in this archive, as carried on SCVHistory.com, /scvhistory/lw3158.htm: "The westbound Interstate 210 overpass has fallen onto Interstate 5 in the Newhall Pass."
9. Caption to LW2794, "5/14 Freeway Overpass, 2-9-1971," UPI Telephoto, photograph #4493 in this archive, as carried on SCVHistory.com, /scvhistory/lw2794.htm: "Freeway overpass at the 5-14 split (Newhall Pass) as it appeared on the day of the Sylmar-San Fernando Earthquake." The original cutline: "The earthquake that hit Southern California caused part of massive elevated freeway to collapse, burying the 80-ton crane (bottom) which had been used in work on the freeway interchange, which intersects US Highway Interstate 5. Miraculously, no one was killed."
10. John Green, "Early History of College of the Canyons," College of the Canyons, as carried on SCVHistory.com, /scvhistory/aa7401.htm: "No permanent campus structures yet existed, but the architectural plans for the buildings on the drawing board were beefed up significantly to make the college's first structures among the safest in California." Al Adelini, dean of student activities: "The Student Center was supposed to be two stories, but everything changed the day of the Sylmar earthquake," The quake "was strong enough to topple the lofty Interstate 5-Highway 14 connectors that were then under construction"; at the library "librarian Jan Keller estimated that some 10,000 volumes lay buried under displaced steel shelves. It took two days to sort through the mess and re-shelve the books." "The scaled-back Student Center, now relegated to a single story in the interest of earthquake safety, opened in February of 1975."
11. Caption to LW3160, "Collapsed Freeway Bridge in Newhall Pass, 1971," a Caltech photograph published in the Houston Chronicle on February 3, 1976, photograph #4977 in this archive, as carried on SCVHistory.com, /scvhistory/lw3160.htm: "A collapsed freeway bridge in the Newhall Pass." The 1976 cutline: "In all, 67 bridges on five major freeways were damaged."
12. Caption to LW3159, "Workers Attempt to Save Van Norman Reservoir, 2-9-1971," wire photograph published in the Los Angeles Times, February 10, 1971, photograph #4975 in this archive, as carried on SCVHistory.com, /scvhistory/lw3159.htm: "A helicopter hovers while workers attempt to avert a flood as waves erode the earthen walls of LADWP's Van Norman Reservoir." The Times cutline: "Water lapped at the top of the damaged Van Norman Lakes Reservoir in the San Fernando Valley while workmen sandbagged a leaking portion in an attempt to save it."
13. SCVHistory.com timeline, /scvhistory/timeline.htm, 1971: "March 13: Brick front of Newhall Pharmacy (ex-Swall Hotel at Market & Spruce/Main), damaged in Feb. 9 quake, collapses." Jerry Reynolds, History of the Santa Clarita Valley, chapter 52, "Servicing the Traveler," article #2129 in this archive (/scvhistory/signal/reynolds/part52.html), in a parenthesis in the text: "The Swall Hotel, later known as Newhall Pharmacy, was heavily damaged in the February 9, 1971 Sylmar Earthquake. When it was rebuilt, its bricks were replaced with Spanish stucco." Pat Saletore, "The Finest Hotel South Of San Francisco," Old Town Newhall Gazette, January-February 2006, article #12623 in this archive: the Swall Hotel was rebuilt after a fire in 1916, "only to be destroyed again in the Sylmar Earthquake of February 9, 1971." Caption to LW3373, photograph #5217 in this archive (/scvhistory/lw3373.htm): "In the 1971 earthquake the original brick building was essentially destroyed."
14. Santa Clarita Valley Historical Society, "Timeline: First Presbyterian Church," 1986, HS8601, as carried on SCVHistory.com, /scvhistory/hs8601.htm, 1971: "February 9. Earthquake damages Church structure. Cheaper to build a new one than restore the old." 1976: "March 7, groundbreaking ceremonies." 1977: "February 6. Third church dedicated." Introduction to the church's 1976 cookbook, /scvhistory/firstpresbyterian1976cookbook.htm: "In 1923, the wooden chapel was moved a couple of hundred feet toward Eighth Street and transformed into a two-story brick structure that was severely damaged in the Sylmar Earthquake of Feb. 9, 1971." SCVHistory.com timeline, /scvhistory/timeline.htm, 1976: "March 7: Groundbreaking for new First Presbyterian Church in Newhall (former structure heavily damaged in 1971 earthquake)." Editor's note j to A.B. Perkins, "History of Downtown Newhall" (1958), article #1438 in this archive: "It should be noted that the church building that existed in 1958 was razed after the 1971 earthquake, and a third church building was erected on the property."
15. Caption to LW3019, "Damage in Liquor Store, 2-9-1971," Associated Press wire photograph, photograph #4809 in this archive, as carried on SCVHistory.com, /scvhistory/lw3019.htm. The published caption: "A jumble of liquor bottles littered the floor of a store in Newhall Tuesday after an earthquake jolted the southern California area." Leon Worden adds: "According to members of the Dillenbeck family (pers. comm. 2017), who ran grocery stores in Newhall and Canyon Country at the time of the quake," another paper that ran the photograph placed it at "Milan's Liquor and Deli at 8534 Foothill Blvd. in Sunland".
16. Leon Worden, note to LW2316c, /scvhistory/lw2316c.htm (the same text heads LW2316, LW3158 and the other photographs of the series): "In response, the Joint Committee established a" special subcommittee "with the goal of evaluating its effects and learning from it." "Chaired by Assemblyman James A. Hayes of Long Beach, the three-person subcommittee included Democratic Sen. Joseph M. Kennick, also of Long Beach, and the Santa Clarita Valley's Democratic Assemblyman, Jim Keysor." "Just three months earlier, Keysor, chairman of the board of Keysor-Century Records (aka Keysor-Century Corp.) in Saugus, had been elected to the Assembly for the first time." "In July 1972, the Special Subcommittee published a 132-page report".
17. Caption to LW2548a, "Freeway Damage & Repairs, I-5 & 210," May 22, 1971, photograph #4095 in this archive, as carried on SCVHistory.com, /scvhistory/lw2548a.htm: "Freeway damage and repairs in Sylmar after the earthquake of Feb. 9, 1971." The series runs to LW2548h, eight views.

**Editor's note, The time and the magnitude (bottom):** The sources differ. The SCVHistory.com timeline gives 5:59 a.m. and magnitude 6.5. Leon Worden's captions to LW7102 and LW2316 give 6:00:41 a.m.; LW7102 gives 6.7 on the Richter scale, revised to 6.6, and LW2316 says the Legislature's report of 1972 gave 6.4 and the quake was later assigned 6.6. John Green's history of College of the Canyons gives 6.4, and a Daily News profile of 1996 (/scvhistory/ladn19960506.htm) gives 6.5. The record gives the day only and no magnitude in its text.

**Editor's note, The college library (bottom):** John Green (/scvhistory/aa7401.htm) places the fallen books in the Instructional Resource Center, "now called Bonelli Hall," but writes in the same history that no permanent campus structures yet existed in February 1971, and the page's own caption says the Instructional Resource Center "opened in early 1974." The record says only that the college's library lost its books from the shelves.

**Editor's note, The photographs (bottom):** Most of the photographs with this record were taken on the day. Two were not: LW2548a shows the freeway repairs at Sylmar on May 22, 1971, and LW3160 is a Caltech photograph published in 1976. The liquor store in LW3019 was captioned as Newhall in 1971 but may have been in Sunland (note 15).

### recordDates

| Printed | ISO | Precision | What happened | Confirmed |
| --- | --- | --- | --- | --- |
| March 13, 1971 | 1971-03-13 | day | the brick front of the Newhall Pharmacy, the old Swall Hotel, damaged in the earthquake, collapses | no |
| July 1972 | 1972-07-01 | month | the Legislature's special subcommittee publishes its report on the earthquake | no |
| February 1975 | 1975-02-01 | month | College of the Canyons opens its Student Center, cut to one story for earthquake safety | no |
| March 7, 1976 | 1976-03-07 | day | ground broken in Newhall for a new First Presbyterian Church, to replace the building the earthquake damaged | no |
| February 6, 1977 | 1977-02-06 | day | the new First Presbyterian Church is dedicated | no |

### Relations

- **eventPlaces:** #934 Newhall Pass interchange (notes 5, 8, 9; the freeway bridges in the Newhall Pass collapsed in the earthquake)
- **eventOrganizations:** #380 Henry Mayo Newhall Memorial Hospital (notes 7; the hospital's nearly finished plans were reworked for seismic safety after the earthquake)
- **eventOrganizations:** #29850 Santa Clarita Community College District (notes 10; the district's College of the Canyons lost its library shelves and strengthened its building plans after the earthquake)
- **eventArticles:** #2129 52. Servicing the Traveler (named in note 13)
- **eventArticles:** #12623 History: The Finest Hotel South Of San Francisco. (named in note 13)
- **eventArticles:** #1438 History of Downtown Newhall (named in note 14)
- **cited, not a document, not related:** #5689 photographs "2-9-1971 Earthquake"
- **cited, not a document, not related:** #3245 photographs "M=6.0+ Southern California Earthquakes, 1912-1971"
- **cited, not a document, not related:** #3247 photographs "2-9-1971 Sylmar Earthquake: Main Shock (in SCV) & Aftershocks"
- **cited, not a document, not related:** #4973 photographs "Collapsed 210 Freeway Bridge in Newhall Pass, 2-9-1971."
- **cited, not a document, not related:** #4493 photographs "5/14 Freeway Overpass, 2-9-1971"
- **cited, not a document, not related:** #4977 photographs "Collapsed Freeway Bridge in Newhall Pass, 1971."
- **cited, not a document, not related:** #4975 photographs "Workers Attempt to Save Van Norman Reservoir, 2-9-1971."
- **cited, not a document, not related:** #2129 articles "52. Servicing the Traveler"
- **cited, not a document, not related:** #12623 articles "History: The Finest Hotel South Of San Francisco."
- **cited, not a document, not related:** #5217 photographs "Wall Calendar from Albert Swall's Newhall Cash Store, 1915."
- **cited, not a document, not related:** #1438 articles "History of Downtown Newhall"
- **cited, not a document, not related:** #4809 photographs "Damage in Liquor Store, 2-9-1971."
- **cited, not a document, not related:** #4095 photographs "Freeway Damage & Repairs, I-5 & 210"
- **photograph #5689 LW7102** takes the event in photoEvents (named in note 1; )
- **photograph #3247 LW2316c** takes the event in photoEvents (named in note 5, 6, 16; )
- **photograph #3245 LW2316** takes the event in photoEvents (named in note 4; )
- **photograph #4973 LW3158** takes the event in photoEvents (named in note 8; )
- **photograph #4493 LW2794** takes the event in photoEvents (named in note 9; )
- **photograph #4977 LW3160** takes the event in photoEvents (named in note 11; )
- **photograph #4095 LW2548a** takes the event in photoEvents (named in note 17; )
- **photograph #4975 LW3159** takes the event in photoEvents (named in note 12; )
- **photograph #4809 LW3019** takes the event in photoEvents (named in note 15; )
- **Held, photograph #4089 LW2547a** "Doobie Brothers Cover Art: Collapsed 5/14 Freeway Bridges": no footnote cites it (the draft says so). Set `$CFG['linkHeldPhotos'] = true` to link the held photographs too.
- **relatedEvents:** none set; the draft says: The Northridge Earthquake (#875), January 17, 1994: the Newhall Pass interchange record (#934) ties the two, as the two earthquakes that brought down its bridges. Not set by the loader; link by hand if wanted.
- **footnotesOn:** not set. On this site it means "the notes were published on another record" (create_saugus_2019_event_2026_10_05.php); the documents the draft listed there go to sourceDocuments instead.
- **Not written:** every other record. The draft's forNathan recommendations about other records (new place or organization records, changes to person records, image attachments) are not acted on.

### Research leads (researchLeads, not shown on the page)

- The death in the Santa Clarita Valley. The unsigned film page (/scvhistory/sylmarquake1971_usda.htm) says "At least one confirmed fatality occurred in the Santa Clarita Valley." No mirror page names the person or the place. Searched: every mirror page containing "quake" with 1971, every page dated February 9 or 10, 1971, the SCVHistory.com timeline, the Signal's 80-year timeline (/scvhistory/sg19191999.htm, which does not mention the 1971 quake) and the obituaries index (/obits.htm). Not found.
- Other damage the mirror records, not in the body: the brick building at the Acton Hotel site on Crown Valley Road, "destroyed in the 1971 Sylmar-San Fernando earthquake when the second floor collapsed" (AP3113, /scvhistory/ap3113.htm); the Rocky Springs swimming pool in Sand Canyon (LW3753, photograph #5595); the Mint Canyon school library (/scvhistory/ladn19960506.htm); the Newhall Ranch house now at Heritage Junction, "heavily damaged during the 1971 earthquake" (HS0100 and others); the Asher house at the Triple A Ranch, Vasquez Rocks (HS2787); breakables lost at the Hart Museum (CN7103); and the Old Road, which the new Colton route relieved (LW3254). Each could become a recordDates row or a sentence once checked.
- Olive View: the 1962 fire gallery (HB6201, /scvhistory/hb6201.htm) says the earthquake "felled the hospital's four stairwell wings and its parking structure." Olive View is in Sylmar, outside the valley; left out of the body.
- Photographs on the mirror of the series, none of them in Craft: LW2778 (Ken Maynard on the quake, AP, March 1971). LW2548b to LW2548h are in Craft (#4097 to #4109), the rest of the May 1971 freeway series; not proposed for photoEvents, as no note names them. LW2547a (#4089), the Doobie Brothers cover of 1973, is listed and held.
- Wikipedia was not consulted.

## From the draft's forNathan (approved with the draft; listed for the record)

- 1. Title. "Sylmar Earthquake," matching the existing "Northridge Earthquake" (#875). Leon's own index heading in general.htm is "1971 Sylmar Earthquake," and his photograph captions use "Sylmar-San Fernando Earthquake." The body gives both names and his explanation of why the Santa Clarita Valley epicenter carries a San Fernando Valley name. Say if you prefer "1971 Sylmar Earthquake."
- 2. Featured image: asset 11610 (lw3158_large.jpg), the Newhall Pass collapse on the day, already in archiveMedia but not attached to photograph #4973. Say if you would rather have none.
- 3. No theme fits: the theme vocabulary has "Northridge Earthquake" and "Fire & Flood" but no general earthquake or disaster theme. recordTags is left empty. A theme "Earthquakes" (or "Disasters") would hold both earthquakes; a vocabulary change, yours to make.
- 4. Relations not made, for want of a record: the First Presbyterian Church of Newhall, the Swall Hotel (Newhall Pharmacy), College of the Canyons as a college (the district #29850 is used), Jim Keysor, Olive View, Van Norman Reservoir. None is created.
- 5. The ten photographs of the quake have their image files in archiveMedia but none attached. Attaching is a separate fix.
