# Western Air Express Flight 7 Crash: the loader's dry run, 6 October 2026

Written by `scripts/import/create_western_air_flight_7_event_2026_10_06.php` (with `scripts/import/_event_from_draft_2026_10_06.php`) in a dry run. A dry run writes nothing to Craft. The event is read from `inventory/review/western-air-flight-7-draft-2026-10-06.json` (SHA-256 `0173531586e2bf3b...`), the draft of 6 October 2026, written in the v2 shape with every quotation checked word for word that day. Nathan approved it for applying on 6 October 2026.

**Refusals:** none.

## The run

```
DRY RUN create_western_air_flight_7_event_2026_10_06.php
==============================================================================
EVENT: #31914 "Western Air Express Flight 7 Crash" exists, not recreated
    content advisory: yes, the first editor note, top
    historicalEra: #164 Great Depression (1929–1940)
    historicalPeriod: #176 1930-1939
    recordTags: none
    neighborhood: #202 Placerita Canyon
    eventPersons: none
    eventPlaces: none
    eventOrganizations: none
    eventFallenOfficers: none
    eventArticles: none
    articles held, no footnote names them: none
    sourceDocuments: none cited
        nothing to set
    cited records that are not documents (not in sourceDocuments): #5001 photographs "Rescuers Transport Martin Johnson's Body from Plane Crash Near Newhall, 1937."; #5183 photographs "Plane Crash Survivor Osa Johnson Rescued, 1-13-1937."; #5185 photographs "Plane Crash Victim on Stretcher, 1-13-1937."; #4487 photographs "Victim (Survivor?) Carried from Fatal Plane Crash Site Near Newhall, 1-13-1937"; #4489 photographs "Victim (Survivor?) Carried from Fatal Plane Crash Site Near Newhall, 1-13-1937"; #3643 photographs "Rescuers Recover Body from Plane Crash Near Newhall, 1937"; #3645 photographs "Rescuers Recover Body from Plane Crash Near Newhall, 1937"; #3689 photographs "Boeing 247 Before Crashing South of Newhall"
    featuredImage: asset 11759 (lw3345_large.jpg)
    photograph #5001 LW3182: photoEvents has it (named in note 8)
    photograph #5183 LW3345: photoEvents has it (named in note 8)
    photograph #5185 LW3346: photoEvents has it (named in note 8)
    photograph #4487 LW2784a: photoEvents has it (named in note 8)
    photograph #4489 LW2784b: photoEvents has it (named in note 8)
    photograph #3643 LW2431a: photoEvents has it (named in note 8)
    photograph #3645 LW2431b: photoEvents has it (named in note 8)
    photograph #3689 LW2443: photoEvents has it (named in note 8)
    other records: nothing written
    REFUSED: none
```

## Quotation check

93 quotations in the v2 draft (every quoted passage, in double or single quotation marks, in the fields (body, significance, footnotes, editor notes, recordDates labels, relation ties, photograph rows, image notes, the literature list) and the research leads; forNathan is not loaded and was not checked) were checked word for word: 93 PASS, 0 CORRECTED from v1, 0 FAIL. Ellipses: the draft has none. Checked against: mirror pages on /Volumes/Reggie/SCVHistory/scvhistory.com (HTML read as latin-1; PDFs by pdftotext), and Craft for record text and titles, by read-only queries.

"Terminal punctuation only" means the quotation stops where the source's sentence goes on and closes with a period or comma of its own; no word is changed.

| # | Where | Quotation | Result | Page | Note |
| --- | --- | --- | --- | --- | --- |
| 1 | body | error on the part of the pilot for descending to a dangerously low altitude without positive knowledge of his position. | PASS | /scvhistory/nc13315.htm | the body quotes the Accident Board as note 2 gives it |
| 2 | footnotes[0] | Death from the Sky Over Newhall: Times Two, | PASS | /scvhistory/pollack0312planes.htm | a title; the page prints "Death from the Sky Over Newhall - Times Two." with an en dash. The colon is citation style |
| 3 | footnotes[0] | Less than three weeks later, on Jan. 12, 1937, adverse weather conditions brought down Western Air Express Flight 7 four miles southeast of  [cut here for the table] | PASS | /scvhistory/pollack0312planes.htm |  |
| 4 | footnotes[0] | Flight 7 began its trek in Salt Lake City, en route to San Diego, with intermediate stops to be made at Las Vegas, Burbank and Long Beach. | PASS | /scvhistory/pollack0312planes.htm |  |
| 5 | footnotes[0] | The Western Air Express crash took the lives of one crew member (co-pilot Owens) and four passengers, including noted international adventur [cut here for the table] | PASS | /scvhistory/pollack0312planes.htm |  |
| 6 | footnotes[0] | We begin on the evening of Dec. 27, 1936. | PASS | /scvhistory/pollack0312planes.htm |  |
| 7 | footnotes[0] | All 12 persons on board the airplane died in the crash | PASS | /scvhistory/pollack0312planes.htm |  |
| 8 | footnotes[1] | Date & Time: 12 JAN 1937 at 1107 Local Time | PASS | /scvhistory/nc13315.htm |  |
| 9 | footnotes[1] | Crew on board: 3 | PASS | /scvhistory/nc13315.htm |  |
| 10 | footnotes[1] | Pax on board: 10 | PASS | /scvhistory/nc13315.htm |  |
| 11 | footnotes[1] | Total fatalities: 5 | PASS | /scvhistory/nc13315.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 12 | footnotes[1] | In a descent rate of 525 feet per minute, aircraft hit Pinetos Peak. The copilot and four passengers, among them the explorer Martin Johnson [cut here for the table] | PASS | /scvhistory/nc13315.htm |  |
| 13 | footnotes[1] | It is the opinion of the Accident Board that the probable cause of this accident was error on the part of the pilot for descending to a dang [cut here for the table] | PASS | /scvhistory/nc13315.htm |  |
| 14 | footnotes[1] | According to the U.S. Department of Transportation, the official report (which has no docket number) was adopted May 12, 1937. | PASS | /scvhistory/nc13315.htm |  |
| 15 | footnotes[1] | It may have been translated into French and then to British English. | PASS | /scvhistory/nc13315.htm |  |
| 16 | footnotes[2] | Another Airplane Crash Kills 2. Plane With 13 on Board Hits Iron Mountain. | PASS | /scvhistory/sg011437.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 17 | footnotes[2] | The crash came at 11:15 as Pilot W.W. Lewis attempted to take the plane to the Burbank airport in fog. | PASS | /scvhistory/sg011437.htm |  |
| 18 | footnotes[2] | lost the beam | PASS | /scvhistory/sg011437.htm |  |
| 19 | footnotes[2] | Suddenly the mountain loomed before him, and Lewis with lightning-like thought, shut off the motors | PASS | /scvhistory/sg011437.htm |  |
| 20 | footnotes[2] | on the side of the mountain, only a short distance from the lockout station. | PASS | /scvhistory/sg011437.htm |  |
| 21 | footnotes[2] | The thirteen occupants of the plane were thrown in a heap, and one, James A. Braden, Cleveland, Ohio was instantly killed. Martin Johnson, w [cut here for the table] | PASS | /scvhistory/sg011437.htm |  |
| 22 | footnotes[2] | John Wood, caretaker at the Dulin ranch three miles east of town, heard an airplane motor begin to sputter, and stop suddenly. | PASS | /scvhistory/sg011437.htm |  |
| 23 | footnotes[2] | Arthur S. Robinson, of Rochester, N.Y., the least hurt of all, worked his way down the mountain toward Olive View Sanitarium, where he met a [cut here for the table] | PASS | /scvhistory/sg011437.htm |  |
| 24 | footnotes[2] | After the correct location was learned, systemic arrangements were made to get to the plane, but the rain, which continued all day, hampered [cut here for the table] | PASS | /scvhistory/sg011437.htm |  |
| 25 | footnotes[2] | Curiosity seekers, however, kept coming, all afternoon, and through the night. | PASS | /scvhistory/sg011437.htm |  |
| 26 | footnotes[2] | The wreck is plainly visible from Newhall | PASS | /scvhistory/sg011437.htm |  |
| 27 | footnotes[3] | EXPLORER DIES AFTER AIRPLANE CRASH, | PASS | /scvhistory/lw2247.htm |  |
| 28 | footnotes[3] | Mr. & Mrs. Martin Johnson, 1937 Newhall Plane Crash Victims | PASS | /scvhistory/lw2247.htm |  |
| 29 | footnotes[3] | Martin Johnson, African explorer who followed jungle trails with impunity, died today the victim of an accident of civilization, the crash o [cut here for the table] | PASS | /scvhistory/lw2247.htm |  |
| 30 | footnotes[3] | Johnson's wife, Osa, his companion on many forbidding quests of game and story material, escaped with a fractured knee. | PASS | /scvhistory/lw2247.htm |  |
| 31 | footnotes[3] | Another Killed And 11 Injured In Air Tragedy | PASS | /scvhistory/lw2247.htm |  |
| 32 | footnotes[3] | Martin Johnson died the next day from injuries sustained in the crash; Osa Johnson survived. A total of five people died; only one was kille [cut here for the table] | PASS | /scvhistory/lw2247.htm |  |
| 33 | footnotes[4] | Huge Deposit Might Affect Ships' Radios, | PASS | /scvhistory/lp_sanberdocountysun011937.htm |  |
| 34 | footnotes[4] | Earl E. Spencer, Chicago businessman, died at 6:56 a.m. today, the fourth victim of the Western Air Express crash of last week. | PASS | /scvhistory/lp_sanberdocountysun011937.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 35 | footnotes[4] | Arthur L. Loomis of Omaha, Neb., died yesterday, after being under an oxygen tent since Friday. Pneumonia resulting from exposure while awai [cut here for the table] | PASS | /scvhistory/lp_sanberdocountysun011937.htm |  |
| 36 | footnotes[4] | Vast deposits of radio-active ore on the air line route over Newhall pass, a mining engineer suggested today, may have been responsible for  [cut here for the table] | PASS | /scvhistory/lp_sanberdocountysun011937.htm |  |
| 37 | footnotes[4] | Wireless communication aboard both doomed transports possibly was affected by millions of tons of uranium, a radium-filled ore, which lie ju [cut here for the table] | PASS | /scvhistory/lp_sanberdocountysun011937.htm |  |
| 38 | footnotes[5] | SCV Chronology: A Timeline of Historical Events, | PASS | /scvhistory/timeline.htm | a title; the page prints "SCV Chronology." and "A Timeline of Historical Events." on two lines. The colon is citation style |
| 39 | footnotes[5] | January 12: Boeing 247 crashes at Santa Clara Divide; 2 dead, 11 injured. | PASS | /scvhistory/timeline.htm |  |
| 40 | footnotes[6] | Oil Station Aide Describes Sounds of Newhall Crash, | PASS | /scvhistory/sg011437.htm |  |
| 41 | footnotes[6] | Crash Pilot in Plea for Radio, | PASS | /scvhistory/sg011437.htm |  |
| 42 | footnotes[6] | was the first witness summoned by Major R.W. Schroeder, chief of airline inspection, as the government inquiry opened on the accident, which [cut here for the table] | PASS | /scvhistory/sg011437.htm |  |
| 43 | footnotes[6] | Lewis did not know that five of the 13 persons who were aboard the plane have died. | PASS | /scvhistory/sg011437.htm |  |
| 44 | footnotes[6] | never came on. | PASS | /scvhistory/sg011437.htm |  |
| 45 | footnotes[7] | Photo shows stretcher bearers carrying the body of Martin Johnson down to be placed in wagon for trip down the mountain side. | PASS | /scvhistory/lw3182.htm; craft #5001 |  |
| 46 | footnotes[7] | Photo shows rescue workers as they placed Mrs. Osa Johnson, wife of the noted explorer, in wagon for trip down the mountain grade. | PASS | /scvhistory/lw3345.htm; craft #5183 |  |
| 47 | footnotes[7] | Photo shows one of the victims of yesterday's plane crash on stretcher as rescue workers attempted to get the injured and dead down the prec [cut here for the table] | PASS | /scvhistory/lw3346.htm; craft #5185 |  |
| 48 | footnotes[7] | Victims of the airplane crash near Newhall, California were transported to various hospitals early this morning over trails that wound among [cut here for the table] | PASS | /scvhistory/lw2784a.htm; /scvhistory/lw2784b.htm; craft #4487; craft #4489 |  |
| 49 | footnotes[7] | from the wreckage of the plane that crashed Jan. 12th | PASS | /scvhistory/lw2431a.htm; /scvhistory/lw2431b.htm; craft #3645; craft #3643 |  |
| 50 | footnotes[7] | This same airplane crashed Jan. 12, 1937, in the mountains south of Newhall, killing five of 13 on board. | PASS | /scvhistory/lw2443.htm; craft #3689 |  |
| 51 | footnotes[8] | Boeing 247D Crashes at Santa Clara Divide, 1937 | PASS | /scvhistory/ds3701.htm |  |
| 52 | footnotes[8] | slammed into Pinetos Peak at the Santa Clara Divide south of Newhall on Jan. 12, 1937. It was the second fatal crash of a commercial airline [cut here for the table] | PASS | /scvhistory/ds3701.htm; /scvhistory/lw2431a.htm; /scvhistory/lw2443.htm; /scvhistory/lw2784a.htm; /scvhistory/lw3182.htm; /scvhistory/lw3345.htm; /scvhistory/lw3346.htm |  |
| 53 | footnotes[8] | Osa Johnson filed a $502,539 lawsuit against Western Air Express and United Airports Company of California, Ltd. (builder and owner of Burba [cut here for the table] | PASS | /scvhistory/al2072a.htm | a search term, found on the page |
| 54 | editorNotes[1].note | 2 dead, 11 injured. | PASS | /scvhistory/timeline.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 55 | editorNotes[1].note | Another Airplane Crash Kills 2. | PASS | /scvhistory/sg011437.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 56 | editorNotes[1].note | one other person | PASS | /scvhistory/lw2247.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 57 | editorNotes[1].note | killing one man outright and injuring 12 other persons | PASS | /scvhistory/lw3346.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 58 | editorNotes[1].note | with the loss of two lives and the serious injuries of five others. | PASS | /scvhistory/lw2431a.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 59 | editorNotes[1].note | 5 Killed in Plane Crash Including Adventurer Martin Johnson, | PASS | /scvhistory/sg011437.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 60 | editorNotes[1].note | After this report was published, three more people succumbed to their injuries, for a total of five casualties. | PASS | /scvhistory/sg011437.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 61 | editorNotes[1].note | the fourth victim | PASS | /scvhistory/lp_sanberdocountysun011937.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 62 | editorNotes[1].note | has taken five lives to date | PASS | /scvhistory/sg011437.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 63 | editorNotes[1].note | Total fatalities: 5 | PASS | /scvhistory/nc13315.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 64 | editorNotes[2].note | 1107 Local Time | PASS | /scvhistory/nc13315.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 65 | editorNotes[2].note | The crash came at 11:15, | PASS | /scvhistory/sg011437.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 66 | editorNotes[2].note | The pilot told me the crash took place at 11:10 a.m. | PASS | /scvhistory/lw2247.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 67 | editorNotes[3].note | aircraft hit Pinetos Peak. | PASS | /scvhistory/nc13315.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 68 | editorNotes[3].note | near the summit of Los Pinetos Peak | PASS | /scvhistory/pollack0312planes.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 69 | editorNotes[3].note | Plane With 13 on Board Hits Iron Mountain, | PASS | /scvhistory/sg011437.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 70 | editorNotes[3].note | on top of Stone mountain | PASS | /scvhistory/sg011437.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 71 | editorNotes[3].note | Pinetos Peak at the Santa Clara Divide, | PASS | /scvhistory/ds3701.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 72 | editorNotes[3].note | on the side of Iron Mountain, south of Placerita Canyon on the Mendenhall Ridge along the Santa Clara Divide | PASS | /scvhistory/al2072a.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 73 | editorNotes[3].note | Santa Clara Divide | PASS | /scvhistory/al2072a.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 74 | editorNotes[3].note | The first reports here placed the crash in Placerita Canyon, | PASS | /scvhistory/sg011437.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 75 | editorNotes[3].note | crashed in Placerita canyon | PASS | /scvhistory/sg011437.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 76 | editorNotes[4].note | Boeing 247B, | PASS | /scvhistory/nc13315.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 77 | editorNotes[4].note | Boeing Model 247-B | PASS | /scvhistory/pollack0312planes.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 78 | editorNotes[4].note | There is some question as to whether the aircraft was a Boeing 247 Model B or Model D. | PASS | /scvhistory/ds3701.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 79 | editorNotes[5].note | Co-pilot Clifford P. Owens | PASS | /scvhistory/lw2247.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 80 | editorNotes[5].note | C.T. Owens, co-pilot | PASS | /scvhistory/sg011437.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 81 | editorNotes[5].note | Co-Pilot C.T. Jones | PASS | /scvhistory/lp_sanberdocountysun011937.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 82 | editorNotes[5].note | E.E. Spencer | PASS | /scvhistory/sg011437.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 83 | editorNotes[5].note | D.E. Spencer | PASS | /scvhistory/lw2247.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 84 | editorNotes[5].note | Earl E. Spencer | PASS | /scvhistory/lp_sanberdocountysun011937.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 85 | recordDates[3].label | the fourth victim | PASS | /scvhistory/lp_sanberdocountysun011937.htm | found word for word on the page the sentence names; the note gives the source in its words, not its path |
| 86 | theLiterature[0].cite | Death from the Sky Over Newhall: Times Two, | PASS | /scvhistory/pollack0312planes.htm | a title; the page prints "Death from the Sky Over Newhall - Times Two." with an en dash. The colon is citation style |
| 87 | researchLeads[1] | C.T. Jones | PASS | /scvhistory/lp_sanberdocountysun011937.htm | a search term, found on the page |
| 88 | researchLeads[1] | It is not known how long the three lingered before they succumbed to their injuries. | PASS | /scvhistory/ds3701.htm | a search term, found on the page |
| 89 | researchLeads[5] | Martin Johnson | PASS | /scvhistory/lw3410.htm |  |
| 90 | researchLeads[5] | Osa Johnson | PASS | /scvhistory/al2072a.htm | a search term, found on the page |
| 91 | researchLeads[5] | NC13315 | PASS | /scvhistory/al2072a.htm | a search term, found on the page |
| 92 | researchLeads[5] | Pinetos | PASS | /scvhistory/al2072a.htm | a search term, found on the page |
| 93 | researchLeads[5] | Western Air Express | PASS | /scvhistory/al2072a.htm | a search term, found on the page |

## Changes from v1 to v2

None.

## The event as it would read

**Editor's note, Content advisory (top):** This record concerns an airliner crash in which five people died, and describes their injuries and the carrying down of the injured and the dead.

- **eventDate:** January 12, 1937
- **eventDateEdtf:** 1937-01-12
- **eventDateStart:** (empty)
- **eventDateEnd:** (empty)
- **startEvidence:** contemporary
- **eventChlNumber:** (empty)
- **eventSignificance:** An airliner bound for Burbank crashed in fog on the mountains southeast of Newhall on January 12, 1937, three weeks after United Air Lines Flight 34 came down in Rice Canyon. Five of the thirteen aboard died, among them the explorer and filmmaker Martin Johnson; his wife, Osa Johnson, survived.
- **historicalEra:** #164 Great Depression (1929–1940)
- **historicalPeriod:** #176 1930-1939
- **recordTags:** (empty)
- **neighborhood:** #202 Placerita Canyon
- **featuredImage:** asset 11759 (lw3345_large.jpg), LW3345, Osa Johnson placed in a wagon for the trip down the mountain, January 13, 1937 (ACME); in archiveMedia, attached to no record
- **bandImage:** (empty) none proposed

On January 12, 1937, Western Air Express Flight 7, a Boeing 247 airliner on its way from Salt Lake City to Burbank, Long Beach and San Diego, crashed in fog and rain on the mountains southeast of Newhall, near the summit of Los Pinetos Peak above Placerita Canyon.[1][2] Thirteen people were aboard, a crew of three and ten passengers.[2][3] The pilot, W.W. Lewis, had lost the radio beam in the fog; when the mountain loomed ahead of him he shut off the motors and set the plane down on the mountainside.[3]

One passenger, James A. Braden of Cleveland, was killed at once. The explorer and filmmaker Martin Johnson died in a hospital the next morning; his wife, Osa Johnson, his companion on his expeditions, survived with a fractured knee.[3][4] Arthur L. Loomis of Omaha died on January 17 and Earl E. Spencer of Chicago on January 18, and the co-pilot, Clifford P. Owens, also died of his injuries: five of the thirteen in all.[5][1][2] The first reports, written while the injured still lived, gave two dead, and so does Leon Worden's timeline. SCVHistory.com's heading on its copy of the Signal's report says five, while the Signal itself printed two; the note at the foot of this page sets the three figures side by side.[3][4][6]

A ranch caretaker east of town heard the crash and reported it in Newhall, and patients at the Olive View Sanitarium reported it there. Arthur S. Robinson, the least hurt of the passengers, worked his way down the mountain and met a rescue party of the sanitarium's doctors. Rain fell all day and hampered the rescue, and it was past midnight before the injured were brought down the south side of the mountain by mule teams and buckboard.[3] Photographs taken that morning show them carried down the snowy trails to the waiting wagons.[8] The wreck could be seen plainly from Newhall, and curiosity seekers kept coming through the night.[3]

A federal inquiry opened at Burbank on January 21.[7] The Accident Board of the Bureau of Air Commerce found the probable cause to be "error on the part of the pilot for descending to a dangerously low altitude without positive knowledge of his position."[2] Osa Johnson sued Western Air Express and the owner of the Burbank airport over her husband's death, and lost on appeal in federal court on June 30, 1941.[9]

It was the second fatal crash of an airliner near Newhall in three weeks: United Air Lines Flight 34 had come down in Rice Canyon on December 27, 1936, killing all twelve aboard.[9][1] A mining engineer, Charles Stanley, suggested at the time that uranium in the Newhall hills might have thrown off both planes' radios.[5]

1. Alan Pollack, "Death from the Sky Over Newhall: Times Two," Heritage Junction Dispatch, March-April 2012, as carried on SCVHistory.com, /scvhistory/pollack0312planes.htm: "Less than three weeks later, on Jan. 12, 1937, adverse weather conditions brought down Western Air Express Flight 7 four miles southeast of Newhall, when the Boeing Model 247-B crashed into the San Gabriel Mountains near the summit of Los Pinetos Peak, about 1,600 feet above the Walker Ranch in Placerita Canyon." "Flight 7 began its trek in Salt Lake City, en route to San Diego, with intermediate stops to be made at Las Vegas, Burbank and Long Beach." "The Western Air Express crash took the lives of one crew member (co-pilot Owens) and four passengers, including noted international adventurer and filmmaker Martin Johnson." Of United Air Lines Flight 34: "We begin on the evening of Dec. 27, 1936." Then: "All 12 persons on board the airplane died in the crash".
2. Accident Board of the Bureau of Air Commerce, report on the crash of Western Air Express Flight 7, Boeing 247 NC13315, adopted May 12, 1937, in the summary of the Aircraft Crashes Record Office, a private database in Switzerland, as carried on SCVHistory.com, /scvhistory/nc13315.htm: "Date & Time: 12 JAN 1937 at 1107 Local Time"; "Crew on board: 3"; "Pax on board: 10"; "Total fatalities: 5". "In a descent rate of 525 feet per minute, aircraft hit Pinetos Peak. The copilot and four passengers, among them the explorer Martin Johnson, were killed." "It is the opinion of the Accident Board that the probable cause of this accident was error on the part of the pilot for descending to a dangerously low altitude without positive knowledge of his position." The webmaster's note: "According to the U.S. Department of Transportation, the official report (which has no docket number) was adopted May 12, 1937." It warns that the summary's wording is not the Board's throughout: "It may have been translated into French and then to British English."
3. "Another Airplane Crash Kills 2. Plane With 13 on Board Hits Iron Mountain." The Newhall Signal and Saugus Enterprise, January 14, 1937, as carried on SCVHistory.com, /scvhistory/sg011437.htm: "The crash came at 11:15 as Pilot W.W. Lewis attempted to take the plane to the Burbank airport in fog." Lewis had "lost the beam". "Suddenly the mountain loomed before him, and Lewis with lightning-like thought, shut off the motors" and set the plane down "on the side of the mountain, only a short distance from the lockout station." "The thirteen occupants of the plane were thrown in a heap, and one, James A. Braden, Cleveland, Ohio was instantly killed. Martin Johnson, world famous traveler and explorer, was badly hurt, dying Wednesday morning." "John Wood, caretaker at the Dulin ranch three miles east of town, heard an airplane motor begin to sputter, and stop suddenly." "Arthur S. Robinson, of Rochester, N.Y., the least hurt of all, worked his way down the mountain toward Olive View Sanitarium, where he met a rescue party of all the doctors of the Sanitarium, the crash having been reported there by some patients in an outside cabin, high on the mountainside." "After the correct location was learned, systemic arrangements were made to get to the plane, but the rain, which continued all day, hampered operations, and it was not until past midnight that the victims were finally brought down from the south side by mule teams and buckboard." "Curiosity seekers, however, kept coming, all afternoon, and through the night." "The wreck is plainly visible from Newhall".
4. Associated Press, Los Angeles, January 13, 1937, "EXPLORER DIES AFTER AIRPLANE CRASH," with Leon Worden's caption to LW2247, "Mr. & Mrs. Martin Johnson, 1937 Newhall Plane Crash Victims" (a press photograph not in this archive's records), as carried on SCVHistory.com, /scvhistory/lw2247.htm. The AP: "Martin Johnson, African explorer who followed jungle trails with impunity, died today the victim of an accident of civilization, the crash of an air transport plane which killed one other person and injured 11." "Johnson's wife, Osa, his companion on many forbidding quests of game and story material, escaped with a fractured knee." The headline: "Another Killed And 11 Injured In Air Tragedy". The caption: "Martin Johnson died the next day from injuries sustained in the crash; Osa Johnson survived. A total of five people died; only one was killed on impact."
5. "Huge Deposit Might Affect Ships' Radios," Associated Press and United Press, Los Angeles, January 18, 1937, in the San Bernardino County Sun, January 19, 1937, as carried on SCVHistory.com, /scvhistory/lp_sanberdocountysun011937.htm. The United Press: "Earl E. Spencer, Chicago businessman, died at 6:56 a.m. today, the fourth victim of the Western Air Express crash of last week." "Arthur L. Loomis of Omaha, Neb., died yesterday, after being under an oxygen tent since Friday. Pneumonia resulting from exposure while awaiting rescue was blamed for his death." The Associated Press: "Vast deposits of radio-active ore on the air line route over Newhall pass, a mining engineer suggested today, may have been responsible for two plane crashes and the loss of 15 lives within a month." "Wireless communication aboard both doomed transports possibly was affected by millions of tons of uranium, a radium-filled ore, which lie just below the surface of the earth in the Newhall hills, said Charles Stanley."
6. Leon Worden, "SCV Chronology: A Timeline of Historical Events," as carried on SCVHistory.com, /scvhistory/timeline.htm, 1937: "January 12: Boeing 247 crashes at Santa Clara Divide; 2 dead, 11 injured." The line gives the count of the first reports; see the editor's note on the number of dead.
7. Associated Press, Burbank, January 21, 1937, "Oil Station Aide Describes Sounds of Newhall Crash," in The Fresno Bee, January 21, 1937, and "Crash Pilot in Plea for Radio," in the San Bernardino County Sun, January 22, 1937, as carried on SCVHistory.com, /scvhistory/sg011437.htm (below the Signal's report): Wood "was the first witness summoned by Major R.W. Schroeder, chief of airline inspection, as the government inquiry opened on the accident, which has taken five lives to date." Of the pilot: "Lewis did not know that five of the 13 persons who were aboard the plane have died." Lewis said the localizer radio beam "never came on."
8. Photographs in this archive, ACME wire photographs of January 13, 1937, each with its original cutline: LW3182, photograph #5001 (/scvhistory/lw3182.htm), "Photo shows stretcher bearers carrying the body of Martin Johnson down to be placed in wagon for trip down the mountain side."; LW3345, photograph #5183 (/scvhistory/lw3345.htm), "Photo shows rescue workers as they placed Mrs. Osa Johnson, wife of the noted explorer, in wagon for trip down the mountain grade."; LW3346, photograph #5185 (/scvhistory/lw3346.htm), "Photo shows one of the victims of yesterday's plane crash on stretcher as rescue workers attempted to get the injured and dead down the precipitous trail to a hospital."; LW2784a and LW2784b, the front and back of one print, photographs #4487 and #4489 (/scvhistory/lw2784a.htm), "Victims of the airplane crash near Newhall, California were transported to various hospitals early this morning over trails that wound among the snow-covered hills."; LW2431a and LW2431b, front and back, photographs #3643 and #3645 (/scvhistory/lw2431a.htm), the body of James A. Braden removed "from the wreckage of the plane that crashed Jan. 12th". Also LW2443, photograph #3689 (/scvhistory/lw2443.htm), the same airplane at Denver in May 1933: "This same airplane crashed Jan. 12, 1937, in the mountains south of Newhall, killing five of 13 on board."
9. The caption SCVHistory.com carries with DS3701, "Boeing 247D Crashes at Santa Clara Divide, 1937" (not in this archive's records), and with LW3182, LW3345, LW3346, LW2784a, LW2431a and LW2443, /scvhistory/ds3701.htm: the Boeing "slammed into Pinetos Peak at the Santa Clara Divide south of Newhall on Jan. 12, 1937. It was the second fatal crash of a commercial airliner in the vicinity within three weeks." "Osa Johnson filed a $502,539 lawsuit against Western Air Express and United Airports Company of California, Ltd. (builder and owner of Burbank Airport, in 1937 a United Airlines subsidiary) for allegedly causing the death of her husband. Even though the crash was ruled pilot error, she lost on appeal in federal court on June 30, 1941."

**Editor's note, The number of dead: three figures, and the archive against its own source (bottom):** Leon Worden's timeline gives "2 dead, 11 injured." That was the count of the first reports, made before three of the injured died. The Newhall Signal's headline of January 14, 1937, read "Another Airplane Crash Kills 2." The Associated Press of January 13 had the crash killing "one other person" besides Martin Johnson and injuring 11, and the ACME cutlines of January 13 and 14 have it "killing one man outright and injuring 12 other persons" and "with the loss of two lives and the serious injuries of five others." SCVHistory.com heads its page of the Signal's report "5 Killed in Plane Crash Including Adventurer Martin Johnson," a heading of the site's, not the Signal's, with a webmaster's note: "After this report was published, three more people succumbed to their injuries, for a total of five casualties." The later count is five: the United Press called Earl E. Spencer "the fourth victim" on January 18; the Associated Press wrote on January 21 that the crash "has taken five lives to date"; the Accident Board's report gives "Total fatalities: 5"; and Alan Pollack (2012) and the photograph captions give five. Three figures therefore stand side by side, and none is set aside: the Signal printed two; SCVHistory.com's own heading on its copy of that Signal page says five; and every later source says five. The archive's heading does not match the newspaper it presents, and both are kept as printed.

**Editor's note, The time (bottom):** The Accident Board's report, in the summary on SCVHistory.com, gives "1107 Local Time". The Newhall Signal says "The crash came at 11:15," and Alan Pollack gives 11:15 a.m. A rescuer quoted by the Associated Press said "The pilot told me the crash took place at 11:10 a.m." The record says only that it was late morning.

**Editor's note, The place (bottom):** The sources name the place in several ways. The Accident Board's report: "aircraft hit Pinetos Peak." Alan Pollack: "near the summit of Los Pinetos Peak". The Newhall Signal's subheading: "Plane With 13 on Board Hits Iron Mountain," and in its text, "on top of Stone mountain". The SCVHistory.com captions: "Pinetos Peak at the Santa Clara Divide," and "on the side of Iron Mountain, south of Placerita Canyon on the Mendenhall Ridge along the Santa Clara Divide". Leon Worden's timeline: "Santa Clara Divide". The Signal notes that "The first reports here placed the crash in Placerita Canyon," and an Associated Press report of January 21 still says the airliner "crashed in Placerita canyon".

**Editor's note, The aircraft (bottom):** The Accident Board's report gives the airplane as a "Boeing 247B," and Alan Pollack as a "Boeing Model 247-B". The SCVHistory.com captions call it a 247-D, and say "There is some question as to whether the aircraft was a Boeing 247 Model B or Model D." The record says Boeing 247.

**Editor's note, The names (bottom):** The co-pilot is "Co-pilot Clifford P. Owens" to the Associated Press and Alan Pollack, "C.T. Owens, co-pilot" to The Newhall Signal, and "Co-Pilot C.T. Jones" in a United Press report of January 18. The Chicago passenger is "E.E. Spencer" in the Signal, "D.E. Spencer" in the Associated Press list of the injured, and "Earl E. Spencer" in the United Press report of his death. The record gives Clifford P. Owens and Earl E. Spencer.

### recordDates

| Printed | ISO | Precision | What happened | Confirmed |
| --- | --- | --- | --- | --- |
| January 12, 1937 | 1937-01-12 | day | the crash, late morning; James A. Braden killed | no |
| Wednesday morning | 1937-01-13 | day | Martin Johnson dies in a hospital; the injured brought down the mountain after midnight | no |
| yesterday | 1937-01-17 | day | Arthur L. Loomis dies (the day before a United Press report dated January 18) | no |
| 6:56 a.m. today | 1937-01-18 | day | Earl E. Spencer dies, "the fourth victim" | no |
| Burbank, Jan. 21 | 1937-01-21 | day | the federal inquiry opens at Burbank | no |
| May 12, 1937 | 1937-05-12 | day | the Accident Board's report adopted | no |
| June 30, 1941 | 1941-06-30 | day | Osa Johnson loses her suit on appeal in federal court | no |

### Relations

- **cited, not a document, not related:** #5001 photographs "Rescuers Transport Martin Johnson's Body from Plane Crash Near Newhall, 1937."
- **cited, not a document, not related:** #5183 photographs "Plane Crash Survivor Osa Johnson Rescued, 1-13-1937."
- **cited, not a document, not related:** #5185 photographs "Plane Crash Victim on Stretcher, 1-13-1937."
- **cited, not a document, not related:** #4487 photographs "Victim (Survivor?) Carried from Fatal Plane Crash Site Near Newhall, 1-13-1937"
- **cited, not a document, not related:** #4489 photographs "Victim (Survivor?) Carried from Fatal Plane Crash Site Near Newhall, 1-13-1937"
- **cited, not a document, not related:** #3643 photographs "Rescuers Recover Body from Plane Crash Near Newhall, 1937"
- **cited, not a document, not related:** #3645 photographs "Rescuers Recover Body from Plane Crash Near Newhall, 1937"
- **cited, not a document, not related:** #3689 photographs "Boeing 247 Before Crashing South of Newhall"
- **photograph #5001 LW3182** takes the event in photoEvents (named in note 8; )
- **photograph #5183 LW3345** takes the event in photoEvents (named in note 8; )
- **photograph #5185 LW3346** takes the event in photoEvents (named in note 8; )
- **photograph #4487 LW2784a** takes the event in photoEvents (named in note 8; )
- **photograph #4489 LW2784b** takes the event in photoEvents (named in note 8; )
- **photograph #3643 LW2431a** takes the event in photoEvents (named in note 8; )
- **photograph #3645 LW2431b** takes the event in photoEvents (named in note 8; )
- **photograph #3689 LW2443** takes the event in photoEvents (named in note 8; )
- **relatedEvents:** none set; the draft says: United Air Lines Flight 34, Rice Canyon, December 27, 1936 (no record yet; drafted the same day as inventory/review/united-flight-34-draft-2026-10-06.json). Link when it exists: Alan Pollack (note 1) and the captions (note 9) pair the two crashes.
- **footnotesOn:** not set. On this site it means "the notes were published on another record" (create_saugus_2019_event_2026_10_05.php); the documents the draft listed there go to sourceDocuments instead.
- **Not written:** every other record. The draft's forNathan recommendations about other records (new place or organization records, changes to person records, image attachments) are not acted on.

### Research leads (researchLeads, not shown on the page)

- Photographs on the mirror, none in Craft: DS3701 (two views, with the long caption of note 9); AL2072a and AL2072b (ACME, collection of Alan Pollack; the page says a second copy is LW3344); LW2247 (Wide World press photograph of Martin and Osa Johnson, with the AP report of January 13). Searched Craft photographs by photoSourceCode and for the words plane crash.
- The date the co-pilot, Clifford P. Owens, died is not on the mirror. The United Press of January 18 lists him (as "C.T. Jones") among the injured still in serious condition, and the Associated Press of January 21 counts five dead, so he died between those dates; not stated by any source. The captions say "It is not known how long the three lingered before they succumbed to their injuries."
- Osa Johnson's suit: only the caption (note 9) gives it. The court decision of June 30, 1941, is not on the mirror; the federal reporter citation would confirm the date and the parties.
- The Accident Board's report itself: the mirror has only a database summary (note 2). The full report, adopted May 12, 1937, would settle the time, the place and the aircraft model.
- The captions quote a 2012 recollection of a longtime Placerita resident whose uncles were first at the scene (personal communication to Leon Worden). Not used: one person's memory, and it names private people.
- Searched the mirror for "Martin Johnson", "Osa Johnson", "NC13315", "Pinetos" and "Western Air Express". Besides the series pages, only LW3410 (George Putnam) mentions the crash, in passing; the Lebec pages are another crash (1932).
- Searched Craft (titles and text) for Martin Johnson, Osa Johnson, Western Air Express, the Bureau of Air Commerce, Los Pinetos, the Santa Clara Divide and the Olive View Sanitarium: no records. Wikipedia was not consulted.

## From the draft's forNathan (approved with the draft; listed for the record)

- 1. The conflict you named is narrower than the census put it. "5 Killed in Plane Crash Including Adventurer Martin Johnson" is SCVHistory.com's heading for its page of the Signal's report; the Signal's own 1937 headline is "Another Airplane Crash Kills 2." The timeline's "2 dead, 11 injured" is the first reports' count; three of the injured died later, and every later source gives five. The editor note gives each figure with its source; the record gives five. The timeline line could be corrected when the timeline itself is migrated (a separate decision).
- 2. featuredImage: asset 11759 (lw3345_large.jpg), Osa Johnson carried to a wagon, is proposed. The other photographs show the dead or the injured on stretchers. Say if you would rather the record have no featured image.
- 3. neighborhood Placerita Canyon (#202): the site is on the ridge above the canyon (Pollack: "about 1,600 feet above the Walker Ranch in Placerita Canyon"). Place #18565 (Placerita Canyon) is NOT related: the canyon is where the first reports wrongly put the crash. Say if you want the neighborhood dropped.
- 4. No theme fits: none of the fifteen is aviation or disaster except Fire & Flood. recordTags left empty.
- 5. Martin Johnson has no person record. By the entity threshold this crash is his only tie to the valley; no record recommended. Osa Johnson the same.
- 6. Photograph #3689 (LW2443) is the airplane in 1933, not the crash; it takes the event in photoEvents like the golden spike's map, and its row says so. Its photoDate is empty (the page says May 1933); a separate fix.
- 7. Image assets for all eight photographs are in archiveMedia but not attached to them. Attaching is a separate fix.
