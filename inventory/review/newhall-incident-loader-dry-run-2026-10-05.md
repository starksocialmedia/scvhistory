# The Newhall Incident: the loader's dry run, 6 October 2026

Written by `scripts/import/create_newhall_incident_event_2026_10_05.php` (with `scripts/import/_event_from_draft_2026_10_06.php`) in a dry run. A dry run writes nothing to Craft. The event is read from `inventory/review/newhall-incident-draft-v2-2026-10-05.json` (SHA-256 `18546276d37145a7...`), the v2 draft whose quotations were rechecked on 6 October 2026. Nathan approved the draft for applying on 6 October 2026.

**Refusals:** none.

## The run

```
DRY RUN create_newhall_incident_event_2026_10_05.php
==============================================================================
EVENT: #31376 "The Newhall Incident" exists, not recreated
    content advisory: yes, the first editor note, top
    historicalEra: #167 Incorporation Struggle (1965–1986)
    historicalPeriod: #180 1970-1979
    recordTags: none
    neighborhood: #211 Valencia
    eventPersons: none
    eventPlaces: #16248 Pico Canyon Road (notes 3, 9)
    eventOrganizations: #29792 California Highway Patrol (notes 1, 2, 7); #29282 Los Angeles County Sheriff's Department (notes 1, 8)
    eventFallenOfficers: #29768 Officer Walter C. Frago (notes 1, 2, 6); #29770 Officer Roger D. Gore (notes 1, 2, 6); #29772 Officer James E. Pence Jr. (notes 1, 2, 6); #29774 Officer George M. Alleyn (notes 1, 2, 6)
    eventArticles: none
    articles held, no footnote names them: none
    sourceDocuments: none cited
        nothing to set
    cited records that are not documents (not in sourceDocuments): #4547 photographs "Matchbook Cover: J's Coffee Shop & Castaic Junction Restaurant, 1976."; #29768 fallenOfficers "Officer Walter C. Frago"; #29770 fallenOfficers "Officer Roger D. Gore"; #29772 fallenOfficers "Officer James E. Pence Jr."; #29774 fallenOfficers "Officer George M. Alleyn"; #3977 photographs "Deputies in Tear Gas Cloud, 4-6-1970"; #4553 photographs "Hostage's Son Talks to Reporters, 4-6-1970."
    featuredImage: none
    photograph #3977 LW2524: photoEvents has it (named in note 8)
    photograph #4553 LW2825: photoEvents has it (named in note 9)
    photograph #4547 LW2820: photoEvents has it (named in note 4)
    other records: nothing written
    REFUSED: none
```

## Quotation check

88 quotations in the v2 draft (every quoted passage, in double or single quotation marks, in the fields (body, significance, footnotes, editor notes, recordDates labels, relation ties, photograph rows, image notes, the literature list) and the research leads; forNathan is not loaded and was not rechecked) were checked word for word: 85 PASS, 3 CORRECTED from v1, 0 FAIL. Ellipses: v2 has none. Checked against: mirror pages on /Volumes/Reggie/SCVHistory/scvhistory.com (HTML read as latin-1; PDFs by pdftotext), and Craft for record text, titles and the asset title, by read-only queries (storage/runtime/ev4/craft.json).

"Terminal punctuation only" means the quotation stops where the source's sentence goes on and closes with a period or comma of its own; no word is changed.

| # | Where | Quotation | Result | Page | Note |
| --- | --- | --- | --- | --- | --- |
| 1 | body | shots fired | PASS | /scvhistory/al1977b.htm | the bulletin: "11-99, shots fired, at J's Standard." |
| 2 | body | We use it in all of our academy training. | PASS | /scvhistory/sg4701.htm | the source closes the sentence with a comma before "said Sweeney"; terminal punctuation only |
| 3 | body | the preeminent study | PASS | /scvhistory/gundigest20130506.htm |  |
| 4 | footnotes[0] | Shooting Incident - Newhall Area, | PASS | /scvhistory/al1977a.htm | a title; the bulletin prints it in capitals. Title case is citation style |
| 5 | footnotes[0] | OVER 30 WITNESSES AND CONSIDERABLE PHYSICAL EVIDENCE. | CORRECTED | /scvhistory/al1977c.htm | v2 change: The bulletin prints the sentence in capitals; v1 lowered them, though it quotes the bulletin's other capitals as printed. |
| 6 | footnotes[0] | SUNDAY 2320 hours, | PASS | /scvhistory/al1977a.htm |  |
| 7 | footnotes[0] | pointed a revolver at him | PASS | /scvhistory/al1977a.htm |  |
| 8 | footnotes[0] | SUNDAY 2354 hours, | PASS | /scvhistory/al1977b.htm |  |
| 9 | footnotes[0] | SUNDAY 2356 hours, | PASS | /scvhistory/al1977b.htm |  |
| 10 | footnotes[0] | An excited voice, identified as Officer Pence's (Unit 78-12) radioed for an '11-99, shots fired, at J's Standard.' | CORRECTED | /scvhistory/al1977b.htm | v2 change: The bulletin puts the call in quotation marks, "11-99, shots fired, at J's Standard."; v1 dropped the marks and cut the call short. |
| 11 | footnotes[0] | ALL OF THE ABOVE ACTION TOOK PLACE WITHIN THE SPAN OF APPROXIMATELY FOUR AND ONE-HALF MINUTES, FROM THE INITIAL STOP AT 2354 HOURS UNTIL THE [cut here for the table] | PASS | /scvhistory/al1977f.htm | continues ", 15 BY THE OFFICERS"; terminal punctuation only |
| 12 | footnotes[0] | into the Standard Service Station adjacent to J'S Coffee Shop | PASS | /scvhistory/al1977b.htm |  |
| 13 | footnotes[0] | Old | PASS | /scvhistory/al1977a.htm |  |
| 14 | footnotes[0] | The suspect exited the vehicle with his hands up | PASS | /scvhistory/al1977f.htm |  |
| 15 | footnotes[0] | An assault team composed of three Los Angeles County Sheriff's Deputies entered the house under cover of a tear gas barrage | PASS | /scvhistory/al1977g.htm |  |
| 16 | footnotes[1] | The Newhall Incident, | PASS | /scvhistory/chp-newhall-incident.htm |  |
| 17 | footnotes[1] | lost their lives in a 4-1/2 minute gun battle | PASS | /scvhistory/chp-newhall-incident.htm |  |
| 18 | footnotes[1] | the scene of the slayings, which occurred in a restaurant parking lot just before midnight. | PASS | /scvhistory/chp-newhall-incident.htm |  |
| 19 | footnotes[1] | The gunman, later identified as Jack Twinning | PASS | /scvhistory/chp-newhall-incident.htm |  |
| 20 | footnotes[1] | the driver, Bobby Davis. | PASS | /scvhistory/chp-newhall-incident.htm | continues ", turned and shot Gore"; terminal punctuation only |
| 21 | footnotes[1] | Suspects Jack Twinning and Bobby Davis escaped, later abandoned their vehicle and then split up. | PASS | /scvhistory/chp-newhall-incident.htm |  |
| 22 | footnotes[1] | The date of April 6 was originally used, but the incident began shortly before midnight on April 5 and all four officers were dead by 23:59: [cut here for the table] | PASS | /scvhistory/chp-newhall-incident.htm |  |
| 23 | footnotes[1] | a completely revamped set of procedures to be followed during high-risk and felony stops. | PASS | /scvhistory/chp-newhall-incident.htm | continues ", with emphasis"; terminal punctuation only |
| 24 | footnotes[1] | once stood at the former Newhall office, but was rebuilt at the new site, about one mile from the scene of the slayings. | PASS | /scvhistory/chp-newhall-incident.htm | continues ", which occurred"; terminal punctuation only |
| 25 | footnotes[1] | The Newhall Incident: A Law Enforcement Tragedy (SCVHS/SCVTV 2010). | PASS | /scvhistory/chp-newhall-incident.htm | the link text |
| 26 | footnotes[2] | The Newhall Incident: Tragedy and Heroism, | PASS | /scvhistory/pollack0309newhallincident.htm |  |
| 27 | footnotes[2] | in the parking lot of a Standard service station next to J's Coffee Shop at the present-day intersection of The Old Road and Magic Mountain  [cut here for the table] | PASS | /scvhistory/pollack0309newhallincident.htm |  |
| 28 | footnotes[2] | raced out of his car to the officer's side and tried to pull him out of the line of fire, | PASS | /scvhistory/pollack0309newhallincident.htm |  |
| 29 | footnotes[2] | picked up a revolver and managed to fire off a round. | PASS | /scvhistory/pollack0309newhallincident.htm |  |
| 30 | footnotes[2] | Davis committed suicide Aug. 16, 2009, in his maximum security prison cell at Kern Valley State Prison | PASS | /scvhistory/pollack0309newhallincident.htm |  |
| 31 | footnotes[2] | It changed police procedures forever after. | PASS | /scvhistory/pollack0309newhallincident.htm | continues ", leading to"; terminal punctuation only |
| 32 | footnotes[3] | Matchbook Cover: J's Coffee Shop & Castaic Junction Restaurant, 1976, | PASS | /scvhistory/lw2820.htm |  |
| 33 | footnotes[3] | The coffee shop was J's when it was the location of the 1970 Newhall Incident. | PASS | /scvhistory/lw2820.htm |  |
| 34 | footnotes[3] | took over the former Tip's Coffee Shop at the Saugus off-ramp from Highway 99 (which became the Magic Mountain Parkway exit from Interstate  [cut here for the table] | PASS | /scvhistory/lw2820.htm; photograph #4547 | continues ", while its sister"; terminal punctuation only |
| 35 | footnotes[4] | SAUGUS, Cal., Apr. 6 | PASS | /scvhistory/al1970.htm |  |
| 36 | footnotes[4] | This early morning view shows the scene near here where four California Highway Patrolmen were shot and killed. | PASS | /scvhistory/al1970.htm | the cutline continues "in a gun battle with two suspects"; terminal punctuation only |
| 37 | footnotes[4] | Apr. 6 | PASS | /scvhistory/al1970.htm |  |
| 38 | footnotes[4] | engaged in a gun fight near here early this morning with four California Highway Patrolmen. | PASS | /scvhistory/al1971.htm |  |
| 39 | footnotes[4] | late 4/5. | PASS | /scvhistory/lw2825.htm; photograph #4553 | the cutline continues ", tells newsmen"; terminal punctuation only |
| 40 | footnotes[5] | Newhall Officers, April 6, 1970 | PASS | craft #29772 |  |
| 41 | footnotes[6] | Newhall Incident Memorial Wall Dedication | PASS | /scvhistory/du1970.htm |  |
| 42 | footnotes[6] | Photograph taken June 5, 1970, at the 'Newhall Incident' Memorial Wall dedication at the Highway Patrol Office (located on the frontage road [cut here for the table] | PASS | /scvhistory/du1970.htm | inner double quotation marks become single |
| 43 | footnotes[6] | Gary D. Kness receiving a Community Service Award from the California Highway Patrol for coming to the assistance of the four Newhall office [cut here for the table] | PASS | /scvhistory/du1972.htm | inner double quotation marks become single |
| 44 | footnotes[6] | Newhall Incident | PASS | /scvhistory/du1970.htm |  |
| 45 | footnotes[6] | Newhall Incident | PASS | /scvhistory/du1970.htm |  |
| 46 | footnotes[7] | Deputies in Tear Gas Cloud, 4-6-1970, | PASS | /scvhistory/lw2524.htm |  |
| 47 | footnotes[7] | A cloud of tear gas hovered near three deputy sheriffs who had fired it at a home where a suspect in the slaying of four highway-patrol offi [cut here for the table] | PASS | /scvhistory/lw2524.htm |  |
| 48 | footnotes[8] | Hostage's Son Talks to Reporters, 4-6-1970, | PASS | /scvhistory/lw2825.htm |  |
| 49 | footnotes[8] | The Hoag house was located on the hill above the gas station at the southwest corner of The Old Road and Pico Canyon Road | PASS | /scvhistory/lw2825.htm |  |
| 50 | footnotes[8] | It was actually in Newhall. Today it would be considered Stevenson Ranch. | PASS | /scvhistory/lw2825.htm; photograph #4553 | continues ", where the first tract housing opened in 1988."; terminal punctuation only |
| 51 | footnotes[9] | THE SURVIVING SUSPECT HAS BEEN HELD TO ANSWER IN THE SUPERIOR COURT FOR THE COUNTY OF LOS ANGELES ON FOUR COUNTS OF MURDER AND ONE COUNT OF  [cut here for the table] | PASS | /scvhistory/al1977g.htm |  |
| 52 | footnotes[9] | Davis was captured, stood trial and convicted on four counts of murder. | PASS | /scvhistory/chp-newhall-incident.htm |  |
| 53 | footnotes[9] | Bobby Davis was sentenced to die in the gas chamber, but in 1972 the California Supreme Court declared the death penalty to be cruel and unu [cut here for the table] | PASS | /scvhistory/chp-newhall-incident.htm |  |
| 54 | footnotes[9] | Davis was later convicted on four counts of murder and sentenced to life in prison. | PASS | /scvhistory/sg4701.htm |  |
| 55 | footnotes[10] | The CHP Slaying: 30 Years Later, | PASS | /scvhistory/sg4701.htm |  |
| 56 | footnotes[10] | Four cypress trees were planted in front of the Newhall station in memory of the slain officers. A memorial plaque near the trees bears the  [cut here for the table] | PASS | /scvhistory/sg4701.htm | continues "who lost their lives"; terminal punctuation only |
| 57 | footnotes[10] | We use it in all of our academy training, | PASS | /scvhistory/sg4701.htm |  |
| 58 | footnotes[10] | It was one of the turning points in training for officer safety. | PASS | /scvhistory/sg4701.htm |  |
| 59 | footnotes[11] | California Highway Patrol Officers James E. Pence, Jr., Roger D. Gore, Walter C. Frago, and George M. Alleyn Memorial Highway, | PASS | craft #29772 |  |
| 60 | footnotes[11] | when a portion of Interstate 5 was named for the four downed officers. | PASS | /scvhistory/pollack0309newhallincident.htm |  |
| 61 | footnotes[12] | The Newhall Incident: Anatomy of a Gunfight, | PASS | /scvhistory/gundigest20130506.htm |  |
| 62 | footnotes[12] | a consolidated and edited excerpt from the book, 'Newhall Shooting: A Tactical Analysis,' by Michael E. Wood, | PASS | /scvhistory/gundigest20130506.htm | inner double quotation marks become single |
| 63 | footnotes[12] | the preeminent study of the 1970 Newhall shooting. | PASS | /scvhistory/gundigest20130506.htm | continues "in which four"; terminal punctuation only |
| 64 | footnotes[12] | Newhall Shooting: A Tactical Analysis, | PASS | /scvhistory/gundigest20130506.htm |  |
| 65 | editorNotes[1].note | shots fired | PASS | /scvhistory/al1977b.htm | the bulletin: "11-99, shots fired, at J's Standard." |
| 66 | editorNotes[1].note | The date of April 6 was originally used, but the incident began shortly before midnight on April 5 and all four officers were dead by 23:59: [cut here for the table] | PASS | /scvhistory/chp-newhall-incident.htm |  |
| 67 | editorNotes[1].note | early this morning | PASS | /scvhistory/al1971.htm |  |
| 68 | editorNotes[1].note | late 4/5. | PASS | /scvhistory/lw2825.htm; photograph #4553 | the cutline continues ", tells newsmen"; terminal punctuation only |
| 69 | editorNotes[2].note | instantly | PASS | /scvhistory/al1977c.htm |  |
| 70 | editorNotes[2].note | almost instantly | PASS | /scvhistory/al1977c.htm |  |
| 71 | editorNotes[2].note | mortally wounded. | PASS | craft #29772 |  |
| 72 | editorNotes[2].note | Two of the patrolmen died at the scene and the other two died later in the hospital. | PASS | /scvhistory/al1971.htm |  |
| 73 | editorNotes[3].note | pronounced TWINE-ing | PASS | /scvhistory/lw2825.htm |  |
| 74 | recordDates[0].label | SUNDAY 2356 hours | PASS | /scvhistory/al1977b.htm |  |
| 75 | recordDates[0].label | shots fired | PASS | /scvhistory/al1977b.htm | the bulletin: "11-99, shots fired, at J's Standard." |
| 76 | theLiterature[0].where | The Newhall Incident: Anatomy of a Gunfight, | PASS | /scvhistory/gundigest20130506.htm |  |
| 77 | theLiterature[1].cite | Shooting Incident - Newhall Area, | PASS | /scvhistory/al1977a.htm | a title; the bulletin prints it in capitals. Title case is citation style |
| 78 | theLiterature[3].cite | The Newhall Incident: Tragedy and Heroism, | PASS | /scvhistory/pollack0309newhallincident.htm |  |
| 79 | researchLeads[0] | 4 CHP Men Slain, Suspect Dead | PASS | /scvhistory/vnvgs040770a.htm |  |
| 80 | researchLeads[0] | 4 CHP Officers Slain; 1 Suspect Killed, 1 Wounded | PASS | /scvhistory/vnvgs040770b.htm |  |
| 81 | researchLeads[1] | a residence behind Denny's Coffee Shop at Lyons Avenue | PASS | /scvhistory/al1977g.htm |  |
| 82 | researchLeads[1] | top of Lyons | PASS | /scvhistory/lw2825.htm |  |
| 83 | researchLeads[2] | six hours | PASS | /scvhistory/al1973.htm |  |
| 84 | researchLeads[2] | For nine hours, officers blanketed the area | PASS | /scvhistory/chp-newhall-incident.htm |  |
| 85 | researchLeads[4] | THE INITIAL STOP. | CORRECTED | /scvhistory/al1977f.htm | v2 change: The bulletin prints it in capitals. |
| 86 | researchLeads[5] | Robert Gore | PASS | /scvhistory/gundigest20130506.htm |  |
| 87 | researchLeads[5] | were wanted for murder in Oregon | PASS | /scvhistory/sg4701.htm |  |
| 88 | researchLeads[5] | shot several times by the owner | PASS | /scvhistory/sg4701.htm |  |

## Changes from v1 to v2

1. **footnotes[0].** Was: "over 30 witnesses and considerable physical evidence." Now: "OVER 30 WITNESSES AND CONSIDERABLE PHYSICAL EVIDENCE." Why: The bulletin prints the sentence in capitals; v1 lowered them, though it quotes the bulletin's other capitals as printed.
2. **footnotes[0].** Was: radioed for an 11-99, shots fired" Now: radioed for an '11-99, shots fired, at J's Standard.'" Why: The bulletin puts the call in quotation marks, "11-99, shots fired, at J's Standard."; v1 dropped the marks and cut the call short.
3. **researchLeads[4].** Was: "the initial stop." Now: "THE INITIAL STOP." Why: The bulletin prints it in capitals.
4. **eventFallenOfficers[0].footnotes.** Was: [1, 2] Now: [1, 2, 6] Why: Footnotes 1 and 2 do not name every officer (Alleyn is named in neither); footnote 6 cites the four fallen-officer records by number. Each tie now includes 6.
5. **eventFallenOfficers[1].footnotes.** Was: [1, 2] Now: [1, 2, 6] Why: Footnotes 1 and 2 do not name every officer (Alleyn is named in neither); footnote 6 cites the four fallen-officer records by number. Each tie now includes 6.
6. **eventFallenOfficers[2].footnotes.** Was: [1, 2] Now: [1, 2, 6] Why: Footnotes 1 and 2 do not name every officer (Alleyn is named in neither); footnote 6 cites the four fallen-officer records by number. Each tie now includes 6.
7. **eventFallenOfficers[3].footnotes.** Was: [1, 2] Now: [1, 2, 6] Why: Footnotes 1 and 2 do not name every officer (Alleyn is named in neither); footnote 6 cites the four fallen-officer records by number. Each tie now includes 6.

## The event as it would read

**Editor's note, Content advisory (top):** This record concerns the killing of four police officers in a gun battle, and describes the shooting, a hostage-taking and a suicide.

- **eventDate:** April 5 to 6, 1970
- **eventDateEdtf:** 1970-04-05/1970-04-06
- **eventDateStart:** April 5, 1970
- **eventDateEnd:** April 6, 1970
- **startEvidence:** contemporary
- **eventChlNumber:** (empty)
- **eventSignificance:** Four California Highway Patrol officers of the Newhall office were shot and killed at a traffic stop at what is now The Old Road and Magic Mountain Parkway, just before midnight on April 5, 1970; the official memorials date it April 6. The CHP rewrote its procedures for high-risk stops, and a memorial wall and a stretch of Interstate 5 in the valley carry the officers' names.
- **historicalEra:** #167 Incorporation Struggle (1965–1986)
- **historicalPeriod:** #180 1970-1979
- **recordTags:** (empty)
- **neighborhood:** #211 Valencia
- **featuredImage:** (empty) none from Craft as it stands. Recommend DU1970 (the memorial wall dedication, June 5, 1970) once imported, rather than a scene of the shooting or the siege.
- **bandImage:** (empty) none

Late on Sunday night, April 5, 1970, four California Highway Patrol officers of the Newhall office, Walter Frago, Roger Gore, James Pence and George Alleyn, were shot and killed at a traffic stop in the lot of a Standard service station beside J's Coffee Shop, at what is now The Old Road and Magic Mountain Parkway.[1][2][3][4] Frago and Gore had stopped a car whose driver had pointed a revolver at another motorist on Highway 99. The two men in it opened fire, and Pence and Alleyn, arriving to back them up, were killed in the gun battle that followed.[1]

By the CHP's bulletin of July 1, 1970, the stop began at 2354 hours, Pence radioed "shots fired" at 2356, and the next patrol car arrived at 2359: more than 40 shots in about four and a half minutes.[1] The CHP's own account says all four officers were dead before midnight on April 5. The wire services' first reports were datelined April 6, and the official memorials give April 6 as the date of their deaths.[2][5][6] This record gives both.

A passing driver, Gary Kness, stopped, tried to pull Alleyn out of the line of fire, and fired at one of the gunmen with a fallen officer's revolver. The CHP gave him a Community Service Award.[1][3][7]

The gunmen, Jack Twinning and Bobby Davis, fled on foot.[2] Sheriff's deputies stopped Davis on San Francisquito Canyon Road in a camper he had taken from its owner.[1] Twinning broke into a house on the hill at Pico Canyon Road and The Old Road and held its owner hostage. On the morning of April 6, deputies fired tear gas into the house and went in, and found that he had killed himself.[1][8][9] Davis was convicted of the four killings and sentenced to death; the sentence became life in prison, and he died in prison in 2009.[10][3]

On June 5, 1970, the CHP dedicated a memorial wall to the four at its Newhall office, on the frontage road west of the freeway and south of Lyons Avenue. The wall was later rebuilt at the present Newhall Area office, about a mile from the scene.[7][2] Four cypress trees were planted in front of the station in their memory.[11] Interstate 5 from Magic Mountain Parkway to Rye Canyon Road carries their names.[12] The CHP rewrote its procedures for high-risk stops after the shooting, and thirty years on its Newhall station told The Signal, "We use it in all of our academy training."[2][11]

The shooting has its own literature, above all Michael E. Wood's book Newhall Shooting: A Tactical Analysis, which Gun Digest excerpted in 2013 and calls "the preeminent study" of it.[13] The CHP's bulletin of 1970 reconstructs the four and a half minutes from more than 30 witnesses, and the Santa Clarita Valley Historical Society and SCVTV made a documentary, The Newhall Incident: A Law Enforcement Tragedy, in 2010.[1][2]

1. California Highway Patrol, Information Bulletin, July 1, 1970, "Shooting Incident - Newhall Area," eight pages, AL1977a to AL1977h (collection of Alan Pollack), as carried on SCVHistory.com, /scvhistory/al1977a.htm to al1977h.htm. A reconstruction from "OVER 30 WITNESSES AND CONSIDERABLE PHYSICAL EVIDENCE." Times are in the bulletin's margin: "SUNDAY 2320 hours," the serviceman's encounter with a driver who "pointed a revolver at him" on US 99; "SUNDAY 2354 hours," Unit 78-8 (Officers Gore and Frago) behind the red Pontiac; "SUNDAY 2356 hours," "An excited voice, identified as Officer Pence's (Unit 78-12) radioed for an '11-99, shots fired, at J's Standard.'"; and "ALL OF THE ABOVE ACTION TOOK PLACE WITHIN THE SPAN OF APPROXIMATELY FOUR AND ONE-HALF MINUTES, FROM THE INITIAL STOP AT 2354 HOURS UNTIL THE ARRIVAL OF UNIT 78-16R AT 2359 HOURS. DURING THIS PERIOD MORE THAN 40 SHOTS WERE FIRED." The suspects pulled "into the Standard Service Station adjacent to J'S Coffee Shop" at Henry Mayo Drive and "Old" 99. Afterward a Los Angeles County Sheriff's unit stopped the driver suspect in a stolen camper on San Francisquito Road, and "The suspect exited the vehicle with his hands up"; the passenger suspect held a householder hostage until "An assault team composed of three Los Angeles County Sheriff's Deputies entered the house under cover of a tear gas barrage" and found that he had killed himself with the CHP shotgun.
2. California Highway Patrol, "The Newhall Incident," n.d., as carried on SCVHistory.com with Leon Worden's notes, /scvhistory/chp-newhall-incident.htm: four officers "lost their lives in a 4-1/2 minute gun battle"; "the scene of the slayings, which occurred in a restaurant parking lot just before midnight." "The gunman, later identified as Jack Twinning"; "the driver, Bobby Davis." "Suspects Jack Twinning and Bobby Davis escaped, later abandoned their vehicle and then split up." Leon Worden's note: "The date of April 6 was originally used, but the incident began shortly before midnight on April 5 and all four officers were dead by 23:59:00 that night. The incident carried well into the next day." Of the aftermath: "a completely revamped set of procedures to be followed during high-risk and felony stops." The memorial "once stood at the former Newhall office, but was rebuilt at the new site, about one mile from the scene of the slayings." The page links the documentary "The Newhall Incident: A Law Enforcement Tragedy (SCVHS/SCVTV 2010)."
3. Alan Pollack, "The Newhall Incident: Tragedy and Heroism," Heritage Junction Dispatch, March-April 2009, as carried on SCVHistory.com, /scvhistory/pollack0309newhallincident.htm: the officers were killed "in the parking lot of a Standard service station next to J's Coffee Shop at the present-day intersection of The Old Road and Magic Mountain Parkway in Valencia." Of Gary Dean Kness, a passing driver: he "raced out of his car to the officer's side and tried to pull him out of the line of fire," then "picked up a revolver and managed to fire off a round." "Davis committed suicide Aug. 16, 2009, in his maximum security prison cell at Kern Valley State Prison" (a sentence added after the article's date). "It changed police procedures forever after."
4. Caption to LW2820, "Matchbook Cover: J's Coffee Shop & Castaic Junction Restaurant, 1976," photograph #4547 in this archive (/scvhistory/lw2820.htm): "The coffee shop was J's when it was the location of the 1970 Newhall Incident." J's "took over the former Tip's Coffee Shop at the Saugus off-ramp from Highway 99 (which became the Magic Mountain Parkway exit from Interstate 5)."
5. Associated Press wirephotos, April 6, 1970 (collection of Alan Pollack), as carried on SCVHistory.com. AL1970 (/scvhistory/al1970.htm), dateline "SAUGUS, Cal., Apr. 6": "This early morning view shows the scene near here where four California Highway Patrolmen were shot and killed." AL1971 (/scvhistory/al1971.htm), "Apr. 6": the suspects "engaged in a gun fight near here early this morning with four California Highway Patrolmen." LW2825's UPI cutline of the same day says "late 4/5."
6. The California Highway Patrol's memorial page ("Newhall Officers, April 6, 1970"), the California Peace Officers' Memorial Foundation's honor roll (end of watch April 6, 1970) and Caltrans's list of named highways, as cited in the fallen-officer records #29768, #29770, #29772 and #29774 in this archive. These are external pages; they were read for those records, not again for this draft.
7. Captions to DU1970 and DU1972, "Newhall Incident Memorial Wall Dedication" (photographs provided by Don Uelmen, printed in the CHP's Zenith 12000, May-June 1970), as carried on SCVHistory.com, /scvhistory/du1970.htm and du1972.htm: "Photograph taken June 5, 1970, at the 'Newhall Incident' Memorial Wall dedication at the Highway Patrol Office (located on the frontage road west of I5 and south of Lyons Ave)." DU1972: "Gary D. Kness receiving a Community Service Award from the California Highway Patrol for coming to the assistance of the four Newhall officers slain during the 'Newhall Incident' gun battle on April 5, 1970. Kness exchanged gunfire with the killers and is credited with wounding one."
8. Caption to LW2524, "Deputies in Tear Gas Cloud, 4-6-1970," photograph #3977 in this archive (/scvhistory/lw2524.htm), Associated Press wirephoto with a cutline published April 7, 1970: "A cloud of tear gas hovered near three deputy sheriffs who had fired it at a home where a suspect in the slaying of four highway-patrol officers had taken refuge near Saugus, Calif., yesterday. A short time later, officers rushed the house and found the suspect, Jack Wright Twinning, had killed himself."
9. Caption to LW2825, "Hostage's Son Talks to Reporters, 4-6-1970," photograph #4553 in this archive (/scvhistory/lw2825.htm). Leon Worden's note: "The Hoag house was located on the hill above the gas station at the southwest corner of The Old Road and Pico Canyon Road"; "It was actually in Newhall. Today it would be considered Stevenson Ranch."
10. The legal form. The CHP's bulletin of July 1, 1970 (AL1977g): "THE SURVIVING SUSPECT HAS BEEN HELD TO ANSWER IN THE SUPERIOR COURT FOR THE COUNTY OF LOS ANGELES ON FOUR COUNTS OF MURDER AND ONE COUNT OF ROBBERY." The CHP's account (/scvhistory/chp-newhall-incident.htm): "Davis was captured, stood trial and convicted on four counts of murder." "Bobby Davis was sentenced to die in the gas chamber, but in 1972 the California Supreme Court declared the death penalty to be cruel and unusual punishment and in 1973, the court modified Davis's sentence to life in prison." The Signal (2000): "Davis was later convicted on four counts of murder and sentenced to life in prison."
11. Kristin Wilder, "The CHP Slaying: 30 Years Later," The Signal, April 5, 2000, as carried on SCVHistory.com, /scvhistory/sg4701.htm: "Four cypress trees were planted in front of the Newhall station in memory of the slain officers. A memorial plaque near the trees bears the names of the four young officers." Doug Sweeney of the CHP's Newhall station: "We use it in all of our academy training," and "It was one of the turning points in training for officer safety."
12. California Department of Transportation, 2021 Named Freeways, Highways, Structures and Other Appurtenances in California, "California Highway Patrol Officers James E. Pence, Jr., Roger D. Gore, Walter C. Frago, and George M. Alleyn Memorial Highway," Route 5, Magic Mountain Parkway to Rye Canyon Road undercrossing, SCR 93, Ch. 92, 2006, as cited in the fallen-officer records (external; not read again for this draft). Alan Pollack (2009) dates the ceremony to April 2008, "when a portion of Interstate 5 was named for the four downed officers."
13. Michael E. Wood, "The Newhall Incident: Anatomy of a Gunfight," Gun Digest, May 6, 2013, "a consolidated and edited excerpt from the book, 'Newhall Shooting: A Tactical Analysis,' by Michael E. Wood," as carried on SCVHistory.com, /scvhistory/gundigest20130506.htm (PDF purchased 2019 by Leon Worden). Gun Digest calls the book "the preeminent study of the 1970 Newhall shooting."

**Editor's note, The date (bottom):** The CHP's Information Bulletin of July 1, 1970 times the encounter on US 99 at 2320 hours and Officer Pence's call of "shots fired" at 2356 hours on Sunday, April 5, 1970, and puts the end of the shooting at 2359. Leon Worden's note to the CHP's account: "The date of April 6 was originally used, but the incident began shortly before midnight on April 5 and all four officers were dead by 23:59:00 that night." The Associated Press wirephotos of the next day are datelined April 6 and speak of the gun fight "early this morning"; the UPI cutline on LW2825 says "late 4/5." The CHP's memorial page, the California Peace Officers' Memorial Foundation and Caltrans give April 6. The search, the hostage-taking and Twinning's death were on April 6. The record gives April 5 for the shooting and April 6 as the date the memorials give, and dates the event April 5 to 6.

**Editor's note, Where the officers died (bottom):** Leon Worden's note says all four were dead by 23:59 on April 5, and the CHP's bulletin has Frago, Gore and Pence killed "instantly" or "almost instantly" and Alleyn "mortally wounded." The AP cutline on AL1971, of April 6, says "Two of the patrolmen died at the scene and the other two died later in the hospital." Not reconciled; the body says only that they were killed.

**Editor's note, The names (bottom):** The gunmen are named here as the CHP's account, The Signal, Alan Pollack, Michael E. Wood and Leon Worden's captions name them. Twinning is also spelled Twining (Wood; Leon: "pronounced TWINE-ing"), and Davis's middle name is given as Augustus (Wood) and Augusta (The Signal, Pollack). The householder held hostage is not named in this record.

### recordDates

| Printed | ISO | Precision | What happened | Confirmed |
| --- | --- | --- | --- | --- |
| April 5, 1970 | 1970-04-05 | day | "SUNDAY 2356 hours": Officer Pence radios "shots fired" (CHP Information Bulletin, July 1, 1970) | yes |
| April 6, 1970 | 1970-04-06 | day | The date the official memorials give for the officers' deaths (CHP memorial page; Peace Officers' Memorial Foundation; Caltrans) | yes |
| April 6, 1970 | 1970-04-06 | day | The siege of the house on Pico Canyon Road ends with Twinning's death (LW2524, LW2825) | yes |
| June 5, 1970 | 1970-06-05 | day | Memorial wall dedicated at the Newhall CHP office (DU1970) | yes |

### Relations

- **eventPlaces:** #16248 Pico Canyon Road (notes 3, 9; the house where Twinning held its owner hostage and killed himself)
- **eventOrganizations:** #29792 California Highway Patrol (notes 1, 2, 7; its four Newhall officers killed; its bulletin of 1970; the memorial wall)
- **eventOrganizations:** #29282 Los Angeles County Sheriff's Department (notes 1, 8; its deputies stopped Davis and went into the house where Twinning had killed himself)
- **eventFallenOfficers:** #29768 Officer Walter C. Frago (notes 1, 2, 6; killed)
- **eventFallenOfficers:** #29770 Officer Roger D. Gore (notes 1, 2, 6; killed)
- **eventFallenOfficers:** #29772 Officer James E. Pence Jr. (notes 1, 2, 6; killed)
- **eventFallenOfficers:** #29774 Officer George M. Alleyn (notes 1, 2, 6; killed)
- **cited, not a document, not related:** #4547 photographs "Matchbook Cover: J's Coffee Shop & Castaic Junction Restaurant, 1976."
- **cited, not a document, not related:** #29768 fallenOfficers "Officer Walter C. Frago"
- **cited, not a document, not related:** #29770 fallenOfficers "Officer Roger D. Gore"
- **cited, not a document, not related:** #29772 fallenOfficers "Officer James E. Pence Jr."
- **cited, not a document, not related:** #29774 fallenOfficers "Officer George M. Alleyn"
- **cited, not a document, not related:** #3977 photographs "Deputies in Tear Gas Cloud, 4-6-1970"
- **cited, not a document, not related:** #4553 photographs "Hostage's Son Talks to Reporters, 4-6-1970."
- **photograph #3977 LW2524** takes the event in photoEvents (named in note 8; )
- **photograph #4553 LW2825** takes the event in photoEvents (named in note 9; )
- **photograph #4547 LW2820** takes the event in photoEvents (named in note 4; )
- **relatedEvents:** none
- **footnotesOn:** not set. On this site it means "the notes were published on another record" (create_saugus_2019_event_2026_10_05.php); the documents the draft listed there go to sourceDocuments instead.
- **Not written:** every other record. The draft's forNathan recommendations about other records (new place or organization records, changes to person records, image attachments) are not acted on.

### Research leads (researchLeads, not shown on the page)

- Not in Craft, on the mirror: AL1970 to AL1973 (AP wirephotos of April 6, 1970: the scene, the abandoned car, the weapons, the house under siege), AL1977a to h (the CHP bulletin, better as a document record), DU1970 to DU1972 (the memorial wall dedication, June 5, 1970), and the Valley News and Valley Green Sheet of April 7, 1970 (vnvgs040770a, b; images only, the HTML holds just the headlines "4 CHP Men Slain, Suspect Dead" and "4 CHP Officers Slain; 1 Suspect Killed, 1 Wounded").
- The householder: Steven Hoag in Leon Worden's captions (LW2524, LW2825), Steven and Betty Jean Hoag in Pollack 2009, Glenn Hoag in The Signal 2000. The house: "a residence behind Denny's Coffee Shop at Lyons Avenue" (bulletin), on Pico Canyon Road (Signal, Pollack), at the southwest corner of The Old Road and Pico Canyon Road, then the "top of Lyons" (LW2825). Consistent on the place, not on the name.
- How long the hostage was held: "six hours" (AP, AL1973); released at 9 a.m. Monday (The Signal 2000); "For nine hours, officers blanketed the area" (CHP). Not in the body.
- The officers' children: seven (CHP account; The Signal 2000) against nine (Pollack 2009). Not in the body.
- The bulletin itself puts 2354 hours both on Unit 78-8's report that it was behind the Pontiac at the Castaic inspection facility and on "THE INITIAL STOP." The body gives 2354 as the stop, as the bulletin's summary does.
- Gun Digest's excerpt calls Officer Gore "Robert Gore" once; every other source gives Roger. The Signal 2000 says the gunmen "were wanted for murder in Oregon" and that Davis was "shot several times by the owner" of the camper; the bulletin says the owner returned fire and was beaten, and that Davis surrendered. Neither claim is in the body.
- Where Davis was taken: San Francisquito Canyon Road (bulletin; Pollack). Not linked: San Francisquito #16184 is an empty settlement record.
- Wood's book is not in the archive. The 2010 SCVHS/SCVTV documentary is linked from the CHP page; its URL was not followed.

## From the draft's forNathan (approved with the draft; listed for the record)

- 1. The gunmen are named, once, as Jack Twinning and Bobby Davis, because the archive's sources name them (the CHP's account, The Signal 2000, Pollack 2009, Wood 2013, and Leon's captions on LW2524 and LW2825). Nothing else about them is given: no prison histories, no plans, no motive. Both are dead (Twinning April 6, 1970; Davis 2009, by Pollack). Approve.
- 2. Plain words in the body ("convicted of the four killings", "the sentence became life in prison", "killed himself"); the legal form (held to answer on four counts of murder and one of robbery; convicted on four counts of murder; the death sentence modified in 1973) is in its own footnote.
- 3. The householder held hostage is not named. LW2825 (#4553), proposed as a photograph here, names him and his 17-year-old son in its caption. Say if that photograph should stay off the event record.
- 4. Gary Kness is named for what the CHP honored him for, a public act. No person record is proposed for him.
- 5. The April 6 memorial dates and the highway naming rest on external pages (CHP memorial, Peace Officers' Memorial Foundation, Caltrans) as the officer records cite them; they were not re-read. The archive's own April 6 sources are the AP wirephotos. The highway: Caltrans gives SCR 93 of 2006; Pollack dates the ceremony to April 2008. The body gives no year.
- 6. eventFallenOfficers links all four records (the section is still disabled). eventPersons is empty: there are no person records for Twinning, Davis, Kness or the householder, and none is recommended.
- 7. eventOrganizations adds the Los Angeles County Sheriff's Department (#29282). Not linked: the Santa Clarita Valley Sheriff's Station (#29682); the bulletin names "the Los Angeles County Sheriff's Station at Newhall" and no source read says #29682 is that station.
- 8. Neighborhood: Valencia (211), as Pollack and LW2820 place the scene. The siege was at today's Stevenson Ranch (LW2825; category 208). Add 208 if the record should carry both.
- 9. No theme fits (there is no law-enforcement theme); recordTags is left empty.
- 10. The legacy page /scvhistory/chp-newhall-incident.htm (legacyUrl on all four officer records) could 301 to this event once it is live, as the officer drafts left open. Recommend this record as the target.
- 11. Image assets for #3977 (10875/13638) and #4553 (11176/13361) are in archiveMedia but not attached. A separate fix.
