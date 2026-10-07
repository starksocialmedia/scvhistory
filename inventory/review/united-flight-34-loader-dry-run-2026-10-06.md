# United Air Lines Flight 34 Crash in Rice Canyon: the loader's dry run, 6 October 2026

Written by `scripts/import/create_united_flight_34_event_2026_10_06.php` (with `scripts/import/_event_from_draft_2026_10_06.php`) in a dry run. A dry run writes nothing to Craft. The event is read from `inventory/review/united-flight-34-draft-2026-10-06.json` (SHA-256 `25af612c0c22cb27...`), the draft of 6 October 2026, written in the v2 shape with every quotation checked word for word that day. Nathan approved it for applying on 6 October 2026.

**Refusals:** none.

## The run

```
DRY RUN create_united_flight_34_event_2026_10_06.php
==============================================================================
EVENT: #31907 "United Air Lines Flight 34 Crash in Rice Canyon" exists, not recreated
    content advisory: yes, the first editor note, top
    historicalEra: #164 Great Depression (1929–1940)
    historicalPeriod: #176 1930-1939
    recordTags: none
    neighborhood: none
    eventPersons: none
    eventPlaces: none
    eventOrganizations: none
    eventFallenOfficers: none
    eventArticles: none
    articles held, no footnote names them: none
    sourceDocuments: none cited
        nothing to set
    cited records that are not documents (not in sourceDocuments): #3691 photographs "Broken Watches Fix Time of 1936 Plane Crash in Rice Canyon."; #3693 photographs "Broken Watches Fix Time of 1936 Plane Crash in Rice Canyon."; #4651 photographs "Stewardess Yvonne Trego, Plane Crash Victim, Rice Canyon 12-27-1936."; #4555 photographs "United Flight 34 (Fatal Crash 12-27-1936): Radio Operator Testifies 1-5-1937."; #4557 photographs "United Flight 34 (Fatal Crash 12-27-1936): Radio Operator Testifies 1-5-1937."
    featuredImage: asset 10737 (lw2448a_large.jpg)
    photograph #3691 LW2448a: photoEvents has it (named in note 4)
    photograph #3693 LW2448b: photoEvents has it (named in note 4)
    photograph #4555 LW2826a: photoEvents has it (named in note 9)
    photograph #4557 LW2826b: photoEvents has it (named in note 9)
    photograph #4651 LW2897: photoEvents has it (named in note 5)
    other records: nothing written
    REFUSED: none
```

## Quotation check

75 quotations in the v2 draft (every quoted passage, in double or single quotation marks, in the fields (body, significance, footnotes, editor notes, recordDates labels, relation ties, photograph rows, image notes, the literature list) and the research leads; forNathan is not loaded and was not checked) were checked word for word: 75 PASS, 0 CORRECTED from v1, 0 FAIL. Ellipses: the draft has none. Checked against: mirror pages on /Volumes/Reggie/SCVHistory/scvhistory.com (HTML read as latin-1; PDFs by pdftotext), and Craft for record text and titles, by read-only queries.

"Terminal punctuation only" means the quotation stops where the source's sentence goes on and closes with a period or comma of its own; no word is changed.

| # | Where | Quotation | Result | Page | Note |
| --- | --- | --- | --- | --- | --- |
| 1 | body | Just a minute. | PASS | /scvhistory/ntsb122736.htm (footnote 3) | body and editor note: checked against the page of the footnote that carries it |
| 2 | body | an error on the part of the pilot for attempting to fly through the Newhall pass at an altitude lower than the surrounding mountains without [cut here for the table] | PASS | /scvhistory/ntsb122736.htm (footnote 3) | body: checked against the page of the footnote that carries it |
| 3 | footnotes[0] | Fatal United Air Lines Plane Crash, 12-27-1936, | PASS | /scvhistory/ntsb122736.htm |  |
| 4 | footnotes[0] | Plane Crash in Rice Canyon Kills All 12, | PASS | /scvhistory/ntsb122736.htm |  |
| 5 | footnotes[0] | United Air Lines Flight 34 out of Oakland | PASS | /scvhistory/ntsb122736.htm |  |
| 6 | footnotes[0] | a Boeing 247-D, registration number NC13355 | PASS | /scvhistory/ntsb122736.htm |  |
| 7 | footnotes[0] | crashed into a mountaintop in Rice Canyon, two miles south of Newhall, at 7:38 p.m. on Dec. 27, 1936, one minute after it was scheduled to a [cut here for the table] | PASS | /scvhistory/ntsb122736.htm |  |
| 8 | footnotes[0] | All 12 persons on board were killed | PASS | /scvhistory/ntsb122736.htm |  |
| 9 | footnotes[0] | Pilot Edwin W. Blom, Co-pilot Robert J. McLean, stewardess Yvonne Trego and nine passengers including H.S. Teague, a 28-year-old cartoonist  [cut here for the table] | PASS | /scvhistory/ntsb122736.htm |  |
| 10 | footnotes[0] | E.T. Ford of San Marino, Calif. | PASS | /scvhistory/ntsb122736.htm (footnote 1) | editor note: checked against the page of the footnote that carries it |
| 11 | footnotes[0] | Mrs. E.T. Ford of San Marino, Calif. | PASS | /scvhistory/ntsb122736.htm |  |
| 12 | footnotes[0] | M.P. Hare of Los Angeles | PASS | /scvhistory/ntsb122736.htm |  |
| 13 | footnotes[0] | John Korn of El Centro, Calif. | PASS | /scvhistory/ntsb122736.htm |  |
| 14 | footnotes[0] | A.L. Markwell of Los Angeles | PASS | /scvhistory/ntsb122736.htm |  |
| 15 | footnotes[0] | Mrs. W.A. Newton of Los Angeles | PASS | /scvhistory/ntsb122736.htm |  |
| 16 | footnotes[0] | Alex Novak of El Centro, Calif. | PASS | /scvhistory/ntsb122736.htm |  |
| 17 | footnotes[0] | H.S. Teague of Los Angeles | PASS | /scvhistory/ntsb122736.htm |  |
| 18 | footnotes[0] | Miss Evelyn Valance, Los Angeles | PASS | /scvhistory/ntsb122736.htm |  |
| 19 | footnotes[0] | It was the first of two fatal crashes of Boeing 247-D's in the mountains south of Newhall within a three-week period. | PASS | /scvhistory/ntsb122736.htm |  |
| 20 | footnotes[1] | SCV Chronology. A Timeline of Historical Events, | PASS | /scvhistory/timeline.htm |  |
| 21 | footnotes[1] | December 27: Passenger plane crash in Rice Canyon kills all 12 aboard (3 crew, 9 passengers). | PASS | /scvhistory/timeline.htm |  |
| 22 | footnotes[2] | Fatal United Air Lines Plane Crash, 12-27-1936, | PASS | /scvhistory/ntsb122736.htm |  |
| 23 | footnotes[2] | According to the accident report, the pilot acknowledged that he picked up the Saugus beacon and was coming in for a landing at Burbank. | PASS | /scvhistory/ntsb122736.htm |  |
| 24 | footnotes[2] | At 7:36 p.m. the copilot requested that the localizer at Burbank be turned on (the airline's low-power radio frequency); this was done and t [cut here for the table] | PASS | /scvhistory/ntsb122736.htm |  |
| 25 | footnotes[2] | Just a minute. | PASS | /scvhistory/ntsb122736.htm (footnote 3) | body and editor note: checked against the page of the footnote that carries it |
| 26 | footnotes[2] | This was the last communication from the aircraft. | PASS | /scvhistory/ntsb122736.htm |  |
| 27 | footnotes[2] | In light rain and scattered clouds, NC13355 hit the ground (elevation 2,620 feet) at a 28-degree angle and spun horizontally 307 degrees, sh [cut here for the table] | PASS | /scvhistory/ntsb122736.htm |  |
| 28 | footnotes[2] | There were no witnesses. The airplane was discovered at 10 a.m. the next day. | PASS | /scvhistory/ntsb122736.htm |  |
| 29 | footnotes[2] | A careful examination of the wreckage failed to indicate any structural failure of the aircraft, | PASS | /scvhistory/ntsb122736.htm |  |
| 30 | footnotes[2] | It is the opinion of the Accident Board that the probable cause of this accident was an error on the part of the pilot for attempting to fly [cut here for the table] | PASS | /scvhistory/ntsb122736.htm |  |
| 31 | footnotes[3] | Broken Watches Fix Time of 1936 Plane Crash, | PASS | /scvhistory/lw2448a.htm; /scvhistory/lw2448b.htm; craft #3691; craft #3693 |  |
| 32 | footnotes[3] | The broken watches of three deceased victims | PASS | /scvhistory/lw2448a.htm; /scvhistory/lw2448b.htm; craft #3691; craft #3693 |  |
| 33 | footnotes[3] | fix the time of the crash of United Flight 34 on a mountaintop south of Newhall at 7:38 (p.m.), two minutes after the last communication fro [cut here for the table] | PASS | /scvhistory/lw2448a.htm; /scvhistory/lw2448b.htm; craft #3691; craft #3693 |  |
| 34 | footnotes[3] | STILLED HANDS FIX FATAL MINUTE, | PASS | /scvhistory/lw2448a.htm; /scvhistory/lw2448b.htm; craft #3691; craft #3693 |  |
| 35 | footnotes[3] | They indicate that Pilot Edwin Blom was not lost when the crash occurred, since he had reported only two minutes before the watches stopped. | PASS | /scvhistory/lw2448a.htm; /scvhistory/lw2448b.htm; craft #3691; craft #3693 |  |
| 36 | footnotes[3] | Actually it was the co-pilot who reported to the tower at 7:36 p.m., saying | PASS | /scvhistory/lw2448a.htm; /scvhistory/lw2448b.htm; craft #3691; craft #3693 |  |
| 37 | footnotes[4] | Stewardess Yvonne Trego, | PASS | /scvhistory/lw2897.htm; craft #4651 |  |
| 38 | footnotes[4] | United Airlines announced today that searchers had sighted the wings of an airplane in the mountains north of here, presumably their missing [cut here for the table] | PASS | /scvhistory/lw2897.htm; craft #4651 |  |
| 39 | footnotes[4] | The wreckage was seen from the air near Saugus, about 15 miles from here. | PASS | /scvhistory/lw2897.htm; craft #4651 |  |
| 40 | footnotes[4] | R.E. Dickinson, airport manager, who flew his own plane, returned to Burbank shortly after 10 a.m. (P.S.T.), and told United Airlines execut [cut here for the table] | PASS | /scvhistory/lw2897.htm; craft #4651 |  |
| 41 | footnotes[4] | Stewardess Yvonne Trego perished in the crash of United Flight 34 on a mountaintop south of Newhall Dec. 27, 1936. All three crew members an [cut here for the table] | PASS | /scvhistory/lw2897.htm; craft #4651 |  |
| 42 | footnotes[4] | Her home was in Hastings, Mich. | PASS | /scvhistory/lw2897.htm; craft #4651 |  |
| 43 | footnotes[5] | Workers search for casualties at the site of the crash of United Flight 34 on a mountaintop south of Newhall. | PASS | /scvhistory/sw3601.htm; /scvhistory/sw3602.htm |  |
| 44 | footnotes[5] | A stretcher is supplied for casualties of the crash of United Flight 34 on a mountaintop south of Newhall. | PASS | /scvhistory/sw3603.htm |  |
| 45 | footnotes[5] | Workers load bodies into the coroner's van following the crash of United Flight 34 on a mountaintop south of Newhall. | PASS | /scvhistory/sw3604.htm |  |
| 46 | footnotes[5] | so this is probably Monday, Dec. 28. | PASS | /scvhistory/sw3603.htm; /scvhistory/sw3604.htm |  |
| 47 | footnotes[6] | Death from the Sky Over Newhall: Times Two, | PASS | /scvhistory/pollack0312planes.htm | a title; the page prints "Death from the Sky Over Newhall – Times Two." with a dash; the colon is citation style |
| 48 | footnotes[6] | At 10 a.m. the following day, wreckage of the aircraft was spotted at the head of Rice Canyon near the top of Oat Mountain. | PASS | /scvhistory/pollack0312planes.htm |  |
| 49 | footnotes[6] | It was reported that a number of local citizens from the SCV braved the soaking rainfall and hiked up the rugged terrain to find the wreckag [cut here for the table] | PASS | /scvhistory/pollack0312planes.htm |  |
| 50 | footnotes[6] | Less than three weeks later, on Jan. 12, 1937, adverse weather conditions brought down Western Air Express Flight 7 four miles southeast of  [cut here for the table] | PASS | /scvhistory/pollack0312planes.htm |  |
| 51 | footnotes[7] | Probe Fails to Reveal Cause of Plane Crash, | PASS | /scvhistory/lp_modestobee010637.htm |  |
| 52 | footnotes[7] | What caused a giant United Airline transport to crash December 27th with a loss of twelve lives remained a mystery today after a hearing by  [cut here for the table] | PASS | /scvhistory/lp_modestobee010637.htm |  |
| 53 | footnotes[7] | Investigators said that if the cause of the crash remains a mystery, it could be blamed to souvenir hunters who looted valuable instruments  [cut here for the table] | PASS | /scvhistory/lp_modestobee010637.htm |  |
| 54 | footnotes[8] | Radio Operator Testifies, | PASS | /scvhistory/lw2826a.htm; /scvhistory/lw2826b.htm; craft #4557; craft #4555 |  |
| 55 | footnotes[8] | C.T. Rycraft, radio operator at the Union Air Terminal in Burbank, testifies at the Bureau of Air Commerce hearing into the crash of United  [cut here for the table] | PASS | /scvhistory/lw2826a.htm; /scvhistory/lw2826b.htm; craft #4557; craft #4555 |  |
| 56 | footnotes[8] | that conversation will stay with me for the rest of my life. | PASS | /scvhistory/lw2826a.htm; /scvhistory/lw2826b.htm; craft #4557; craft #4555 |  |
| 57 | footnotes[8] | He said he called the plane and the co-pilot had said, | PASS | /scvhistory/lw2826a.htm; /scvhistory/lw2826b.htm; craft #4557; craft #4555 |  |
| 58 | footnotes[8] | Wait a minute. | PASS | /scvhistory/lw2826a.htm (footnote 9) | editor note: checked against the page of the footnote that carries it |
| 59 | editorNotes[1].note | Just a minute. | PASS | /scvhistory/ntsb122736.htm (footnote 3) | body and editor note: checked against the page of the footnote that carries it |
| 60 | editorNotes[1].note | Wait a minute. | PASS | /scvhistory/lw2826a.htm (footnote 9) | editor note: checked against the page of the footnote that carries it |
| 61 | editorNotes[2].note | Edward Blom, | PASS | /scvhistory/lw2897.htm (footnote 5) | editor note: the comma is the source's ("Edward Blom, radioed") |
| 62 | editorNotes[2].note | 13 persons aboard | PASS | /scvhistory/lw2897.htm (footnote 5) | editor note: checked against the page of the footnote that carries it |
| 63 | editorNotes[2].note | E.T. Ford | PASS | /scvhistory/ntsb122736.htm (footnote 1) | editor note: checked against the page of the footnote that carries it |
| 64 | editorNotes[2].note | Edward F. Ford | PASS | /scvhistory/pollack0312planes.htm (footnote 7) | editor note: checked against the page of the footnote that carries it |
| 65 | editorNotes[3].note | two miles south of Newhall | PASS | /scvhistory/lw2826a.htm |  |
| 66 | editorNotes[3].note | Rice Canyon southwest of Newhall | PASS | /scvhistory/lw2826a.htm |  |
| 67 | editorNotes[3].note | at the head of Rice Canyon near the top of Oat Mountain | PASS | /scvhistory/pollack0312planes.htm (footnote 7) | editor note: checked against the page of the footnote that carries it |
| 68 | editorNotes[3].note | near Saugus | PASS | /scvhistory/lw2826a.htm |  |
| 69 | theLiterature[1].cite | Death from the Sky Over Newhall: Times Two, | PASS | /scvhistory/pollack0312planes.htm | a title; the page prints "Death from the Sky Over Newhall – Times Two." with a dash; the colon is citation style |
| 70 | researchLeads[4] | Rice Canyon | PASS | /scvhistory/general.htm |  |
| 71 | researchLeads[4] | Flight 34 | PASS | /scvhistory/general.htm |  |
| 72 | researchLeads[4] | United Air | PASS | /scvhistory/general.htm |  |
| 73 | researchLeads[4] | NC13355 | PASS | /scvhistory/ntsb122736.htm | research lead: a search term, the aircraft's registration as the page prints it |
| 74 | researchLeads[4] | Yvonne Trego | PASS | /scvhistory/general.htm |  |
| 75 | researchLeads[4] | Rice Canyon | PASS | /scvhistory/general.htm |  |

## Changes from v1 to v2

None.

## The event as it would read

**Editor's note, Content advisory (top):** This record concerns an airliner crash in which all twelve people aboard were killed, and describes the search for the dead and the recovery of their bodies.

- **eventDate:** December 27, 1936
- **eventDateEdtf:** 1936-12-27
- **eventDateStart:** (empty)
- **eventDateEnd:** (empty)
- **startEvidence:** contemporary
- **eventChlNumber:** (empty)
- **eventSignificance:** United Air Lines Flight 34 crashed into a mountaintop in Rice Canyon, south of Newhall, on the night of December 27, 1936, and all twelve aboard were killed. It was the first of two airliner crashes in the mountains south of Newhall within three weeks.
- **historicalEra:** #164 Great Depression (1929–1940)
- **historicalPeriod:** #176 1930-1939
- **recordTags:** (empty)
- **neighborhood:** (empty)
- **featuredImage:** asset 10737 (lw2448a_large.jpg), the ACME wirephoto of December 31, 1936, of the three victims' broken watches (LW2448a, photograph #3691; in archiveMedia, not yet attached to the photograph)
- **bandImage:** (empty) none; the photographs of the site (SW3601 to SW3604) are not in Craft

On the night of December 27, 1936, United Air Lines Flight 34, a Boeing 247-D flying from Oakland to Burbank by way of San Francisco, crashed into a mountaintop in Rice Canyon, south of Newhall, at 7:38 p.m., a minute after it was due at Burbank.[1] All twelve people aboard were killed: three crew, the pilot Edwin W. Blom, the co-pilot Robert J. McLean and the stewardess Yvonne Trego, and nine passengers.[1][2]

Coming in toward Burbank, the pilot reported picking up the Saugus beacon. At 7:36 the co-pilot asked Burbank to turn on its localizer and said "Just a minute." Nothing more was heard from the plane.[3] The broken watches of three of the dead stopped at 7:38.[4] In light rain and cloud the plane struck the ground at 2,620 feet, which sheared off both wings.[3] Nobody saw it come down. The next morning at about ten o'clock an airport manager, R.E. Dickinson, flying his own plane, saw the wings on top of a ridge near Saugus.[3][5] Workers searched the site, and the dead were carried out on stretchers and loaded into the coroner's van; Alan Pollack writes that people from the valley hiked up through the rain to help.[6][7]

The Bureau of Air Commerce held a hearing in Los Angeles on January 5, 1937, at which the radio operator at the Union Air Terminal in Burbank, C.T. Rycraft, testified to his last exchange with the plane. The hearing ended without a cause; souvenir hunters had taken instruments from the wreck.[8][9] The Accident Board found no structural failure, and gave as the probable cause "an error on the part of the pilot for attempting to fly through the Newhall pass at an altitude lower than the surrounding mountains without first determining by radio the existing weather."[3]

It was the first of two airliner crashes in the mountains south of Newhall within three weeks. Western Air Express Flight 7 came down on January 12, 1937.[1][7]

1. SCVHistory.com, "Fatal United Air Lines Plane Crash, 12-27-1936," unsigned, on the page "Plane Crash in Rice Canyon Kills All 12," as carried on SCVHistory.com, /scvhistory/ntsb122736.htm (the same account is repeated on each photograph page of the crash): "United Air Lines Flight 34 out of Oakland" was "a Boeing 247-D, registration number NC13355"; it "crashed into a mountaintop in Rice Canyon, two miles south of Newhall, at 7:38 p.m. on Dec. 27, 1936, one minute after it was scheduled to arrive at its final destination in Burbank after stopping in San Francisco." "All 12 persons on board were killed"; they were "Pilot Edwin W. Blom, Co-pilot Robert J. McLean, stewardess Yvonne Trego and nine passengers including H.S. Teague, a 28-year-old cartoonist for Walt Disney Studios." The passenger list, as the page prints it: "E.T. Ford of San Marino, Calif."; "Mrs. E.T. Ford of San Marino, Calif."; "M.P. Hare of Los Angeles"; "John Korn of El Centro, Calif."; "A.L. Markwell of Los Angeles"; "Mrs. W.A. Newton of Los Angeles"; "Alex Novak of El Centro, Calif."; "H.S. Teague of Los Angeles"; "Miss Evelyn Valance, Los Angeles". The page adds: "It was the first of two fatal crashes of Boeing 247-D's in the mountains south of Newhall within a three-week period."
2. Leon Worden, "SCV Chronology. A Timeline of Historical Events," as carried on SCVHistory.com, /scvhistory/timeline.htm, 1936: "December 27: Passenger plane crash in Rice Canyon kills all 12 aboard (3 crew, 9 passengers)."
3. SCVHistory.com, "Fatal United Air Lines Plane Crash, 12-27-1936," /scvhistory/ntsb122736.htm, from the Bureau of Air Commerce accident report: "According to the accident report, the pilot acknowledged that he picked up the Saugus beacon and was coming in for a landing at Burbank." "At 7:36 p.m. the copilot requested that the localizer at Burbank be turned on (the airline's low-power radio frequency); this was done and the co-pilot said," in the words the page gives, "Just a minute." "This was the last communication from the aircraft." "In light rain and scattered clouds, NC13355 hit the ground (elevation 2,620 feet) at a 28-degree angle and spun horizontally 307 degrees, shearing off both wings and the right-hand landing gear." "There were no witnesses. The airplane was discovered at 10 a.m. the next day." The report: "A careful examination of the wreckage failed to indicate any structural failure of the aircraft," and "It is the opinion of the Accident Board that the probable cause of this accident was an error on the part of the pilot for attempting to fly through the Newhall pass at an altitude lower than the surrounding mountains without first determining by radio the existing weather." A scan of the report is on the mirror as /scvhistory/ntsb122736.pdf.
4. "Broken Watches Fix Time of 1936 Plane Crash," ACME wirephoto dated 12/31/1936, LW2448a and LW2448b (front and back; photographs #3691 and #3693 in this archive), as carried on SCVHistory.com, /scvhistory/lw2448a.htm. Leon Worden's caption: "The broken watches of three deceased victims"; they "fix the time of the crash of United Flight 34 on a mountaintop south of Newhall at 7:38 (p.m.), two minutes after the last communication from the cockpit on Dec. 27, 1936." The original cutline, "STILLED HANDS FIX FATAL MINUTE," reads: "They indicate that Pilot Edwin Blom was not lost when the crash occurred, since he had reported only two minutes before the watches stopped." Leon Worden: "Actually it was the co-pilot who reported to the tower at 7:36 p.m., saying".
5. United Press dispatches from Burbank in the Battle Creek (Mich.) Enquirer, Monday, December 28, 1936, as carried on SCVHistory.com with "Stewardess Yvonne Trego," LW2897 (photograph #4651 in this archive), /scvhistory/lw2897.htm: "United Airlines announced today that searchers had sighted the wings of an airplane in the mountains north of here, presumably their missing air liner with 12 persons aboard." "The wreckage was seen from the air near Saugus, about 15 miles from here." "R.E. Dickinson, airport manager, who flew his own plane, returned to Burbank shortly after 10 a.m. (P.S.T.), and told United Airlines executives that he saw the wings of the ship on top of a ridge." Leon Worden's caption: "Stewardess Yvonne Trego perished in the crash of United Flight 34 on a mountaintop south of Newhall Dec. 27, 1936. All three crew members and nine passengers were killed." The ACME cutline of 12-28-36: "Her home was in Hastings, Mich."
6. Photographs SW3601 to SW3604, Los Angeles Times Photographic Archive, Department of Special Collections, Charles E. Young Research Library, UCLA, as carried on SCVHistory.com, /scvhistory/sw3601.htm, /scvhistory/sw3602.htm, /scvhistory/sw3603.htm and /scvhistory/sw3604.htm (not yet in this archive). Leon Worden's captions: "Workers search for casualties at the site of the crash of United Flight 34 on a mountaintop south of Newhall." "A stretcher is supplied for casualties of the crash of United Flight 34 on a mountaintop south of Newhall." "Workers load bodies into the coroner's van following the crash of United Flight 34 on a mountaintop south of Newhall." Of their date: "so this is probably Monday, Dec. 28."
7. Alan Pollack, "Death from the Sky Over Newhall: Times Two," Heritage Junction Dispatch, March-April 2012, as carried on SCVHistory.com, /scvhistory/pollack0312planes.htm: "At 10 a.m. the following day, wreckage of the aircraft was spotted at the head of Rice Canyon near the top of Oat Mountain." "It was reported that a number of local citizens from the SCV braved the soaking rainfall and hiked up the rugged terrain to find the wreckage and aid in the removal of the bodies of the unfortunate passengers." Of the next crash: "Less than three weeks later, on Jan. 12, 1937, adverse weather conditions brought down Western Air Express Flight 7 four miles southeast of Newhall".
8. United Press, "Probe Fails to Reveal Cause of Plane Crash," Modesto Bee and News-Herald, Wednesday, January 6, 1937, as carried on SCVHistory.com, /scvhistory/lp_modestobee010637.htm: "What caused a giant United Airline transport to crash December 27th with a loss of twelve lives remained a mystery today after a hearing by the bureau of air commerce at which dozens of experts testified." "Investigators said that if the cause of the crash remains a mystery, it could be blamed to souvenir hunters who looted valuable instruments from the wreckage."
9. "Radio Operator Testifies," ACME wirephoto dated 1/5/37, LW2826a and LW2826b (front and back; photographs #4555 and #4557 in this archive), as carried on SCVHistory.com, /scvhistory/lw2826a.htm. Leon Worden's caption: "C.T. Rycraft, radio operator at the Union Air Terminal in Burbank, testifies at the Bureau of Air Commerce hearing into the crash of United Air Lines Flight 34". The cutline quotes Rycraft: "that conversation will stay with me for the rest of my life." "He said he called the plane and the co-pilot had said," in the cutline's words, "Wait a minute."

**Editor's note, The last words from the plane (bottom):** The accident report, as SCVHistory.com quotes it, gives the co-pilot's last words as "Just a minute." The ACME cutline of January 5, 1937, gives them as C.T. Rycraft told the hearing: "Wait a minute." The ACME cutline of December 31, 1936, says the last report came from the pilot; Leon Worden's caption corrects it to the co-pilot. The record follows the report.

**Editor's note, Names and numbers in the first reports (bottom):** The United Press dispatch printed in the Battle Creek Enquirer on December 28, 1936, while the plane was still missing, names the pilot "Edward Blom," and the Enquirer's own story says the plane had "13 persons aboard". The accident report's figures, as SCVHistory.com gives them, and every later account name him Edwin W. Blom and count twelve aboard. The passenger list on SCVHistory.com prints "E.T. Ford"; Alan Pollack (2012) names him "Edward F. Ford". The record gives the names as the passenger list prints them.

**Editor's note, Where it came down (bottom):** The sources place the crash "two miles south of Newhall" (SCVHistory.com's account), in "Rice Canyon southwest of Newhall" (the caption to LW2826a), "at the head of Rice Canyon near the top of Oat Mountain" (Alan Pollack) and "near Saugus" (the United Press in 1936). The record says Rice Canyon, south of Newhall.

### recordDates

| Printed | ISO | Precision | What happened | Confirmed |
| --- | --- | --- | --- | --- |
| December 27, 1936 | 1936-12-27 | day | Flight 34 crashes in Rice Canyon at 7:38 p.m.; all twelve aboard are killed | no |
| December 28, 1936 | 1936-12-28 | day | the wreck is sighted from the air at about 10 a.m. | no |
| January 5, 1937 | 1937-01-05 | day | Bureau of Air Commerce hearing in Los Angeles; C.T. Rycraft testifies | no |

### Relations

- **cited, not a document, not related:** #3691 photographs "Broken Watches Fix Time of 1936 Plane Crash in Rice Canyon."
- **cited, not a document, not related:** #3693 photographs "Broken Watches Fix Time of 1936 Plane Crash in Rice Canyon."
- **cited, not a document, not related:** #4651 photographs "Stewardess Yvonne Trego, Plane Crash Victim, Rice Canyon 12-27-1936."
- **cited, not a document, not related:** #4555 photographs "United Flight 34 (Fatal Crash 12-27-1936): Radio Operator Testifies 1-5-1937."
- **cited, not a document, not related:** #4557 photographs "United Flight 34 (Fatal Crash 12-27-1936): Radio Operator Testifies 1-5-1937."
- **photograph #3691 LW2448a** takes the event in photoEvents (named in note 4; ACME wirephoto of December 31, 1936: the victims' watches that fix the time of the crash)
- **photograph #3693 LW2448b** takes the event in photoEvents (named in note 4; the back of the same wirephoto, with its cutline)
- **photograph #4555 LW2826a** takes the event in photoEvents (named in note 9; ACME wirephoto of the radio operator C.T. Rycraft at the hearing, January 5, 1937)
- **photograph #4557 LW2826b** takes the event in photoEvents (named in note 9; the back of the same wirephoto, with its cutline)
- **photograph #4651 LW2897** takes the event in photoEvents (named in note 5; ACME wirephoto of the stewardess, December 28, 1936, carried with the United Press dispatches)
- **relatedEvents:** none set; the draft says: Western Air Express Flight 7, January 12, 1937 (drafted the same day as inventory/review/western-air-flight-7-draft-2026-10-06.json; no record yet). Link when it exists: SCVHistory.com's account and Alan Pollack pair the two crashes.
- **footnotesOn:** not set. On this site it means "the notes were published on another record" (create_saugus_2019_event_2026_10_05.php); the documents the draft listed there go to sourceDocuments instead.
- **Not written:** every other record. The draft's forNathan recommendations about other records (new place or organization records, changes to person records, image attachments) are not acted on.

### Research leads (researchLeads, not shown on the page)

- The accident report on the mirror, /scvhistory/ntsb122736.pdf, is a scan with no text layer (pdftotext returns nothing). Every quotation of the report in this draft is taken from SCVHistory.com's page, /scvhistory/ntsb122736.htm, which quotes it. Reading the scan would let the record quote the report directly and check the page's quotations.
- Photographs SW3601 to SW3604 (Los Angeles Times Photographic Archive, UCLA; the search, the stretcher, the coroner's van) are on the mirror and not in Craft. Once imported, each would take this event in photoEvents; footnote 6 already names them.
- An Associated Press story of January 18, 1937, as printed in the San Bernardino County Sun, /scvhistory/lp_sanberdocountysun011937.htm, reports a mining engineer, Charles Stanley, suggesting that uranium deposits in the Newhall hills may have affected the radios of both this plane and Western Air Express Flight 7. It is one man's suggestion as reported, not a finding of the inquiry; it is left out of the body.
- Searched for and not found in Craft: a place record for Rice Canyon or Oat Mountain; organization records for United Air Lines, the Bureau of Air Commerce, Walt Disney Studios or the Union Air Terminal; person records for any of the twelve dead; any article or document naming Rice Canyon, Flight 34, Trego, Blom or NC13355 (search and title queries, 6 October 2026). Only the five photographs above.
- Searched the mirror for "Rice Canyon", "Flight 34", "United Air", "NC13355" and "Yvonne Trego": the pages cited here, the chronology and the General Interest index (/scvhistory/general.htm, which lists them under 12-27-1936). No Newhall Signal report of this crash was found on the mirror. The other "Rice Canyon" hits are oil and mining pages about the canyon, not the crash.
- Wikipedia was not consulted.

## From the draft's forNathan (approved with the draft; listed for the record)

- 1. Featured image. Proposed: asset 10737 (lw2448a_large.jpg), the ACME wirephoto of the victims' watches, December 31, 1936. It is in archiveMedia but not attached to photograph #3691. It shows evidence of the crash, not the crash or the dead. Say if you would rather the record have no featured image; the loader then takes featuredImage null.
- 2. Photograph #4651 (LW2897) has photoDate "BFM#26-A 12-28-36", which is the wire service's file number and date. Recommend photoDate December 28, 1936 (photoDateEdtf 1936-12-28). Photographs #3691 and #3693 have no photoDate; their cutline is dated 12/31/1936. Separate fixes; say if you want them.
- 3. Image assets for #3691 (10737), #3693 (10738), #4555 (11177) and #4557 (11178) are in archiveMedia and not attached. Attaching is a separate fix.
- 4. No neighborhood category fits Rice Canyon (none exists), and no theme fits an air crash ("Fire & Flood" does not). Both are left empty. Whether to add a place record for Rice Canyon, or a theme for aviation or disasters, is a vocabulary decision for you.
- 5. relatedEvents: link this record and Western Air Express Flight 7 to each other once both exist.
