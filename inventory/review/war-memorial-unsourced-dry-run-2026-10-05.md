# War memorial: the records still short of the rule, 5 October 2026

Read-only pass by Claude for the main agent. Nothing in Craft was changed. The rule is in `docs/DATA-MODEL.md`, "Sourcing war memorial records": one federal record of the death and one source tying him to the valley. Copies of every page relied on or searched are in `inventory/news/war-memorial-2026-10-05/` (`manifest.json` gives address, read date and sha256). The dry run is `scripts/import/source_wm_unsourced_2026_10_05.php`; it adds footnotes and rewrites "Sources, 2026" editor notes only, by exact text.

## Where the thirteen stand

The list Nathan gave on 5 October is the same list as on 4 October. `source_wm_remaining_2026_10_04.php` was applied that night (APPLIED.log, 21:29, 11 rows), and Craft now reads:

| Record | Status before today | After this dry run, if applied |
|---|---|---|
| #1395 James Robert "Jimmie" Ball | Meets the rule | unchanged |
| #576 Robert Russell Cone | Meets the rule (branch and date differ, shown) | unchanged |
| #518 Augustus A. (August) Rubel | Meets the rule | unchanged |
| #540 Dennis Lee Sellen Jr | Meets the rule (one federal document) | unchanged |
| #538 Ian Timothy D. Gelig | Meets the rule (one federal document) | unchanged |
| #536 Jake William Suter | Meets the rule | unchanged |
| #526 Rudy Alexander Acosta | Meets the rule (one federal document) | unchanged |
| #580 Thomas Milton Ross Jr. | Half: federal, no valley source | Half; note records the Navy list search |
| #514 Lawrence E. Kenaston | Not sourced | **Half: federal (VA burial record)**, valley tie legacy only |
| #542 Dean Glenn Todd Jr | Half: valley (Stars and Stripes), no federal | Half; note records what was searched |
| #534 John Michael Conant | Not met | Not met; note records what was searched |
| #528 Robert Michael Wilson | Not sourced | Not sourced; note records what was searched |
| #524 Stephen Edward Colley | Half: federal, no valley source | **Meets the rule**: Valencia High 2003 yearbook |

The seven met on 4 October are not touched; their differences (Cone's branch and date, the ranks of Sellen, Gelig and Acosta, Acosta's age, Suter's home, Rubel's service years) are shown on the records and still wait on Nathan.

## #514 Lawrence E. Kenaston: half

**Use of the VA locator.** It was approved for Wilson, Todd, Conant and Colley. It was used here because the query is the same kind: a man buried in a VA national cemetery, looked up by name and date of death. The 4 October review asked for this approval; Nathan should confirm it before the script is applied.

**Source found.** U.S. Department of Veterans Affairs, Nationwide Gravesite Locator, https://gravelocator.cem.va.gov/ngl/result, POST last name Kenaston, death year 1945, read 5 October 2026 (copy: `va-gravelocator-kenaston-d1945.html`). Exactly as returned:

> Name: KENASTON, LAWRENCE EDWARD Rank & Branch: CPL US MARINE CORPS Date of Birth: 02/07/1912 Date of Death: 01/11/1945 Buried At: SECTION 174 ROW B SITE 10 Cemetery: LOS ANGELES NATIONAL CEMETERY Cemetery Address: LOS ANGELES NATIONAL CEMETERY, 950 SOUTH SEPULVEDA BOULEVARD LOS ANGELES, CA 90049

The same single result comes back for Kenaston + Lawrence. Kenaston alone gives 19 people; none other died in 1945.

**Rule.** Half. The burial record is a federal record of his death and his service; it does not say how he died. The tie to the valley (his stepsister's house in Newhall) rests on the legacy page alone.

**Differences from the record (shown in the note, not changed).**

- Age at loss: record 34; the VA dates (February 7, 1912 to January 11, 1945) make him 32. The 4 October Find a Grave lead said the same.
- Date of death: record "January 1945" (deathDateEdtf 1945-01); VA gives January 11, 1945. The day could be added; not done here.
- Birth date and burial: not on the record; VA gives both. Fact rows for them are proposed below, not written.

**Searched, not found.** ABMC (4 October, 0 results); Navy State Summary for California (not in it; by its own notice it leaves out deaths in the United States and suicides); the web and the mirror (4 October).

## #524 Stephen Edward Colley: meets the rule

**Source found.** Valencia High School, *Voyager*, the 2003 yearbook, as held on SCVHistory.com (`/scvhistory/vhs2003yearbook.htm`, "Valencia High School's file copy courtesy of Assistant Principal Elizabeth Wilson (2017)"). Read from the Reggie mirror, 5 October 2026, by OCR of all 379 page images and then by eye.

- Printed page 39, footer "2003 Seniors": a senior portrait captioned "Stephen Colley" (page image `files/vhs2003yearbook/page043.jpg`; copy `vhs2003yearbook-page043-printed39.jpg`).
- Index: "Colley, Stephen 28, 39, 248" (copy `vhs2003yearbook-page365-index.jpg`). Page 248 is a group photograph whose caption lists "Stephen Colley" in Row 2 (OCR; the group's name was not read). Page 28 was not found by eye.

**Rule.** Met. Federal: the VA burial record (note 1, already on the record). Valley: the yearbook (proposed note 4). The yearbook does not mention the Army, so the identification rests on name, school and class: the legacy page gives "Valencia High, Class of 2003", and his birth in March 1985 fits that class. The note says the match rests on these, as Ross's note does for ABMC.

**Differences.** None between the yearbook and the record. The rank difference already shown stays (record "Specialist Fourth Class"; VA SPC; NPR and the Army investigation, as quoted, Pfc.).

**Not written, for Nathan.** The record's factSources row "Home | Valencia | 3 | From the legacy page only." would read better with note 4 and without "legacy page only", and a High school row (Valencia High, Class of 2003, notes 3 and 4) could be added. The brief limits the script to footnotes and editor notes, so these are left. The yearbook is not a record in Craft; a document record for it would let the footnote link to it.

## #528 Robert Michael Wilson: not found

**Searched, 4 and 5 October 2026.** VA Gravesite Locator: Wilson + Robert + Michael (4 results, born 1934 to 1950); Wilson + Robert + death 2002 (42; narrowed to August 2002, 4 results, and September 2002, 1 result, none born 1983); Wilson + R + born 1983 (3); Wilson + born April 1983 (2: Bryan Cadena Wilson, and Robert Alan Wilson, born April 24, 1983, died 2013, not him: the record gives April 18, 1983 and death in 2002); Wilson + R + death August 2002 (11). The web (Fort Bragg, 82nd Airborne, crash, August 2002; Santa Clarita). The mirror. Valencia High School's 2001 yearbook (index, no Robert Wilson; a "Robby Wilson" is named once in a classmate's senior message, which cannot be tied to him) and Saugus High School's 2001 yearbook (all 326 pages by OCR; no Robert Wilson).

**Difference inside the record.** The legacy page gives August 13, 2002 in its narrative and August 31, 2002 in its Incident and Casualty Date fields; the record holds deathDate 8-13-2002 and wmIncidentDate August 31, 2002. The new note states the two dates and that no source has settled them.

**Proposed note.** "Not yet sourced" stays, with what was searched added.

## #542 Dean Glenn Todd Jr: still half

**Searched today, VA locator.** Last name begins Todd + death August 2004 (5) and September 2004 (3): none him. Begins Todd + born 1983 (1, not him). Begins Eckard + Dean: none. Begins Eckard + August 2004: none. With the 4 October searches (Todd + Dean; Todd + D + 2004; Todd born 1982 and 1983; Eckard + 2004; Todd-Eckard): he is not in the locator under either name. Web search for "Todd-Eckard" finds only the Stars and Stripes article already cited.

**Proposed note.** The existing text, with what was searched added. No new difference. The differences already shown (name Todd-Eckard, Specialist not Sergeant, specialty, August 30 or 31) stand.

## #534 John Michael Conant: still not met

**Searched today, VA locator.** Conant restricted to the National Memorial Cemetery of the Pacific (Punchbowl, code N899), all years: 4 results (Clarence A., d. 1941; Clarence P., d. 1969; Eileen Maureen, d. 1973; Thomas A., d. 1971): none him. Begins Conan + death April 2008: none. Begins Conan + born 1971: none. With 4 October (Conant + John + 2008; Conant + 2008; Conant + John): he is not in the locator.

**Difference (already shown on the record).** burialPlace "Punchbowl National Cemetery, Honolulu" is not borne out: the cemetery's own listing in the locator does not hold him.

**Also tried, not a source.** The Military Health System's "MHS Honors and Remembers" list (health.mil, which named medical personnel who died) is gone from the live site (404). The Wayback Machine holds 18 list pages and 21 pages for 2008 honorees; none names Conant. The capture is incomplete, so this proves nothing either way.

## #580 Thomas Milton Ross Jr.: still half

**Searched today.** The Navy's 1946 State Summary for California (NAID 305189), Missing section, printed pages 99 to 100 (PDF pages 101 to 102), re-read by fresh OCR of the page images at 200 dpi: the R names run RALSTON, RICE, then SAMPSON. No Ross. The Dead section's Ross block was read on 4 October: no Thomas. So he is not in the California list as dead or missing.

**Proposed note.** The existing text, with that search added. No new difference. The difference already shown stands (record "MIA October 1943"; ABMC date of death September 30, 1944).

## Leads (internal, not for the public notes)

Per the 5 October rule, these are not written to any editor note; they belong in researchLeads if Nathan wants them kept.

- Ross: the Navy lists are filed by next of kin's state; the other 47 states' lists (images on archives.gov) might hold him with a next of kin's address. The errata page (`/scvhistory/warmemorial_errata.htm`) mentions "a newspaper article about Tom Ross, who was missing in action"; it is not on the mirror.
- Kenaston: a Newhall Signal of January 1945 would be the valley source.
- Todd: the Signal of Thursday, September 2, 2004, cited by Stars and Stripes, was not found.
- Wilson: the Fayetteville Observer and the Signal, August and September 2002.
- Conant: a Colorado death record or a Gazette (Colorado Springs) notice, April 2008.
- Colley: Ed Colley (person 26589, water board) and the father in the NPR story share a name; nothing read links them. Not proposed.

## Proposed fact rows (not written by the script)

| Record | Fact | Value on the record | Notes | Difference |
|---|---|---|---|---|
| #514 | Branch | U.S. Marine Corps | 1, 2 | |
| #514 | Rank | Corporal | 1, 2 | |
| #514 | Date of death | January 1945 | 1, 2 | The Department of Veterans Affairs gives January 11, 1945 (note 1). |
| #514 | Age at loss | 34 | 2 | The VA's dates make him 32 (note 1). |
| #514 | Born | Not given on this record | 1 | February 7, 1912 (note 1). |
| #514 | Burial | Not given on this record | 1 | Los Angeles National Cemetery, Section 174, Row B, Site 10 (note 1). |
| #514 | Home | Newhall | 2 | From the legacy page only. |
| #524 | Home | Valencia | 3, 4 | (was 3, legacy only) |
| #524 | High school | Valencia High, Class of 2003 | 3, 4 | |

## Wikipedia and Find a Grave

Nothing here rests on Wikipedia. Find a Grave was a lead for Kenaston on 4 October; the VA record now stands in its place.
