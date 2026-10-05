# Sourcing the last 13 war memorial records, 4 October 2026

Read-only pass by Claude for the main agent. Nothing in Craft was changed. Method and standard as in `war-memorial-sourcing-2026-10-02.md`: one official record of the death in service and one source tying him to the valley; every field traced; disagreements shown with both values. Copies of every page relied on are in `inventory/news/war-memorial-2026-10-04/` (manifest.json gives URL, read date and sha256). The same content in structured form, with proposed footnote text, is `war-memorial-sourcing-2026-10-04.json`.

## Summary

| Record | Status | Two-source rule | Disagreements with current values |
|---|---|---|---|
| #1395 James Robert "Jimmie" Ball | sourced | Met | wmSelectiveServiceDate; wmCasualtyReason / incident date; deathDate |
| #580 Thomas Milton Ross Jr. | partly | Half | deathDate / wmIncidentDate |
| #576 Robert Russell Cone | sourced, with a correction | Met for the death; the valley tie is through his family | wmBranch; deathDate / deathDateEdtf; title (middle name); wmRank |
| #514 Lawrence E. Kenaston | not found | Not met | wmAgeAtLoss (lead only); wmNarrative (internal) |
| #518 Augustus A. (August) Rubel | sourced | Met for the death; the home is outside the valley (as the record already says) | wmLengthOfService; wmRank; date of death (to check) |
| #542 Dean Glenn Todd Jr | partly | Half, and the home note must change | wmRank; title (surname); wmSpecialty; date (internal); wmHomeOfRecord and editor note 'Home, 2026' |
| #540 Dennis Lee Sellen Jr | sourced | Met in one federal document, as Slocum's is: the Defense Department's release records his death and gives his home as Newhall (note 1) | wmRank |
| #538 Ian Timothy D. Gelig | sourced | Met in one federal document: the Defense Department's release records his death and gives his home as Stevenson Ranch (note 1) | wmRank; wmNarrative (cause) |
| #536 Jake William Suter | sourced | Met | wmHomeOfRecord; wmStartTour |
| #534 John Michael Conant | partly | Not met | burialPlace |
| #528 Robert Michael Wilson | not found | Not met | deathDate vs wmIncidentDate (internal) |
| #526 Rudy Alexander Acosta | sourced | Met in one federal document: the Defense Department's release records his death and gives his home as Canyon Country (note 1) | wmAgeAtLoss; wmRank |
| #524 Stephen Edward Colley | partly | Half | wmRank |

## How the sources were found

- **Defense Department releases, 2007 to 2011.** Not indexed by name, but numbered in sequence. The old URLs (`defenselink.mil/releases/release.aspx?releaseid=N`, later `defense.gov/...`) are captured in the Wayback Machine; reading the IDs around each date found Sellen (10512), Gelig (13347), Suter (13572) and Acosta (14349).
- **ABMC** (weremember.abmc.gov) is a JavaScript application; records were read in a browser and transcribed. Its search takes one word; `Ross Thomas` returns nothing, `Ross` returns 207 rows.
- **The Navy State Summaries** are filed by next of kin's state. Ball is in the Idaho list (wife at Moreland), which is why the California list lacks him.
- **VA Gravesite Locator**, used only for Wilson, Todd, Conant and Colley as approved, by POST searches; every result page is saved.
- **The mirror** was searched with Python over the raw bytes of all 33,691 HTML pages listed in `inventory/raw/scvhistory-manifest-2026-08-20.sha256` (not the shell's grep). Terms: kenaston; tom ross, thomas ross, thomas milton ross; robert cone, bob cone, bobby cone, robert russel(l) cone; jimmie/jimmy ball, james robert ball, gudgeon; rubel; sellen; gelig; suter; conant; stephen colley, stephen e. colley, steve colley; dean todd, todd-eckard, eckard; robert michael wilson, pfc wilson, robert m. wilson; rudy acosta, rudy a. acosta. Useful hits: the Navy's 1949 Gudgeon loss report attached to Ball's page, Dick Cone's obituary, and the 2012 parade caption naming Acosta.
- **Wikipedia** was not relied on for anything. **Find a Grave** appears once, as a lead (Kenaston).

## #1395 James Robert "Jimmie" Ball: sourced

**Two-source status.** Met. Federal: the Navy's 1946 State Summary for Idaho lists him among the dead (note 1), and the Navy's 1949 submarine-loss report lists him in Gudgeon's crew (note 2). Valley: his own draft card at 1322 Newhall Ave., Newhall (note 4), and the Newhall Signal of September 22, 1944 (note 5).

**Proposed footnotes.**

1. *service, rank, death in service, family.* Navy Department, State Summary of War Casualties from World War II for Navy, Marine Corps, and Coast Guard Personnel from Idaho (1946), Killed in Action, Died of Wounds, or Lost Lives as Result of Operational Movements in War Zones, page 1, National Archives NAID 305197: "BALL, James Robert, Seaman 1c, USNR. Wife, Mrs. Valma Fairchild Ball, Box 74, Moreland."
   - URL: https://www.archives.gov/research/military/ww2/navy-casualties/idaho.html (read 4 October 2026); copy: `navy-state-summary-idaho-p1.gif`
   - Agrees with the record: yes. Agrees with wmBranch (USNR) and wmRank (Seaman First Class). Explains why he is absent from the California list: the lists are filed by next of kin's state, and his wife was in Moreland, Idaho. New fact: married (wmFamily is empty). The wife's name is from a 1946 federal list; the Fose footnote set the precedent of quoting the next-of-kin line.
2. *assignment, loss.* Navy Department, U.S. Submarine Losses, World War II (Washington: Government Printing Office, 1949), Gudgeon (SS-211), pages 94 to 95 and crew list, as attached to his page on SCVHistory.com (/scvhistory/files/gudgeon1949/): "She left Johnston Island on 7 April 1944, after having topped off with fuel, and was never heard from again"; crew list: "BALL, J. R. ... S1".
   - URL: https://scvhistory.com/scvhistory/files/gudgeon1949/gudgeon1949.pdf (read from the Reggie mirror) (read 4 October 2026); copy: `submarine-losses-1949-gudgeon.pdf`
   - Agrees with the record: yes. Supports wmServiceExtra Assignment (USS Gudgeon). The report also says that on 18 April enemy planes claimed a bombed submarine, but that 'All of these conclusions are presumptive': the record's 'presumed April 18, 1944' and 'presumed aerial bombardment' are the report's possibility, not a finding.
3. *loss.* Naval History and Heritage Command, Dictionary of American Naval Fighting Ships, "Gudgeon I (SS-211)": "On 7 June 1944, Gudgeon was officially declared overdue and presumed lost."
   - URL: https://www.history.navy.mil/research/histories/ship-histories/danfs/g/gudgeon-i.html (read 4 October 2026); copy: `danfs-gudgeon-i.txt`
   - Agrees with the record: yes. DANFS adds: 'Captured Japanese records shed no light on the manner of her loss.' Nothing read gives the record's official date of death, January 15, 1946.
4. *home of record, date of birth, birthplace, selective service.* Selective Service System, Registration Card (D.S.S. Form 1, revised 6-1-42), James Robert Ball, Local Board No. 175, Palmdale, held in this archive: residence "1322 Newhall Ave, Newhall, L.A., California"; born "Nov. 18, 1922," Santa Monica, California; employer Aircraft Components, Inc., Van Nuys.
   - URL: web/uploads/archive-media/legacy/ww2_jamesrobertball_draft_large.jpg (asset 1964) (read 4 October 2026)
   - Agrees with the record: yes. Agrees with wmHomeOfRecord, wmDateOfBirth and the body's Santa Monica birth. DISAGREES with wmSelectiveServiceDate '(draft card undated; ~1941)': the card is the form for men born January 1, 1922 to June 30, 1924, revised June 1, 1942, and gives his age as 19, so he registered between mid-1942 and November 17, 1942, not in 1941.
5. *home (valley tie).* The Newhall Signal and Saugus Enterprise, September 22, 1944, "Not Good-Bye": "Jimmie Ball was a member of the Gudgeon crew." The column says the news "came to Newhall from the War Department."
   - URL: web/uploads/archive-media/legacy/sg19440922ball_large.jpg (asset 1966) (read 4 October 2026)
   - Agrees with the record: yes.
6. *loss announced.* Los Angeles Times, September 13, 1944, Part I, "U.S. Submarine Presumed Lost" (Associated Press, Washington, September 12): "The submarine Gudgeon with her crew of approximately 65 officers and men is overdue and presumed lost, the Navy announced today."
   - URL: web/uploads/archive-media/legacy/lat19440913ball.jpg (asset 1965) (read 4 October 2026)
   - Agrees with the record: yes.
7. *memorial.* Photograph of the Courts of the Missing, Honolulu Memorial, held in this archive: "BALL JAMES R / SEAMAN 1C · USNR · CALIFORNIA."
   - URL: web/uploads/archive-media/legacy/ww2_jamesrobertball_memorial.jpg (asset 1961) (read 4 October 2026)
   - Agrees with the record: yes. Supports wmWallReference (name on the Honolulu Memorial); the court number (Court 5) is not visible in the photograph. ABMC's online directory, searched 4 October 2026, does not list him (84 Balls, no James R.); the stone shows he is commemorated, so the gap is the directory's.
8. *narrative.* SCVHistory.com, Santa Clarita Valley War Memorial, page for him (/scvhistory/ww2_jamesrobertball.htm).
   - Agrees with the record: yes.

**Disagreements (show both values).**

- wmSelectiveServiceDate: record has "(draft card undated; ~1941)"; source: registration form revised 6-1-42, age 19: registered mid-1942 to November 17, 1942 (notes 4).
- wmCasualtyReason / incident date: record has "presumed aerial bombardment; presumed April 18, 1944"; source: 1949 Navy report: an 18 April bombing claim is only a possibility, 'All of these conclusions are presumptive'; DANFS: manner of loss unknown (notes 2, 3).
- deathDate: record has "January 15, 1946 (determination)"; source: no source read gives this date.

**Searched, not found** (for `inventory/source-searches.json`).

- ABMC Burial and Memorialization Directory, q=Ball, all 84 rows, 4 October 2026: no James R. Ball (saved: abmc-searched-not-found.txt). q=Gudgeon: 0.
- The official date of death January 15, 1946: not found in DANFS, the 1949 report or the 1946 Idaho list.

**Notes.**

- Lead only, not proposed: On Eternal Patrol (oneternalpatrol.com/ball-j-r.htm, saved) gives service number '554 13 34' (agrees with wmServiceId 5541334), 'From Moreland, Idaho', a Purple Heart, and 'born in Palms, California' (the draft card says Santa Monica). The Purple Heart is unsourced beyond this site.

## #580 Thomas Milton Ross Jr.: partly

**Two-source status.** Half. Federal: ABMC lists a Thomas M. Ross, Gunner's Mate First Class, USN, entered from California, Missing in Action, on the Walls of the Missing at Manila (note 1). The identification is by name, initial, rating and state; ABMC gives no hometown or next of kin. Valley tie: the legacy page only.

**Proposed footnotes.**

1. *service, rank, service number, memorial, date of death, awards.* American Battle Monuments Commission, Burial and Memorialization Directory: Thomas M. Ross, service number 3828119, Gunner's Mate First Class, U.S. Navy, entered the service from California, World War II; Manila American Cemetery, Missing in Action; date of death September 30, 1944; Purple Heart Medal.
   - URL: https://weremember.abmc.gov/s?q=Ross&v=G&type=16&sort=title:ASC (record 109 of 207) (read 4 October 2026); copy: `ross-abmc.txt`
   - Agrees with the record: yes. Agrees with wmBranch and wmRank. New: service number 3828119 (wmServiceId empty), commemoration on the Walls of the Missing at Manila (wmWallReference empty), Purple Heart. The date of death, September 30, 1944, is about a year after the record's 'MIA October 1943': consistent with a finding of death after a year missing, but that reading is an inference.
2. *narrative, home.* SCVHistory.com, Santa Clarita Valley War Memorial, page for him (/warmemorial/ww2_tomross.htm).
   - Agrees with the record: yes.

**Disagreements (show both values).**

- deathDate / wmIncidentDate: record has "MIA October 1943"; source: ABMC: date of death September 30, 1944, Missing in Action (notes 1).

**Searched, not found** (for `inventory/source-searches.json`).

- Navy State Summary for California (NAID 305189), Dead section, ROSS block on PDF page 78, re-read 4 October 2026 from the OCR text and the 2 October reading: no Thomas Ross. So his next of kin was not recorded in California in 1946, or he is in a section not read (Missing).
- Mirror (33,691 HTML pages, Python over raw bytes, 4 October 2026) for 'tom ross|thomas ross|thomas milton ross|ross,? jr|t. m. ross': only the memorial pages and their navigation. The newspaper article about him that the Cone page mentions is not in the mirror as a page.
- ABMC q='Ross Thomas' returns 0 (the directory does not take two-word queries); found under q=Ross.

**Notes.**

- Next step if wanted: the Navy State Summary 'Missing' section for California, and other states' lists, could show his next of kin and so confirm the identification.

## #576 Robert Russell Cone: sourced, with a correction

**Two-source status.** Met for the death; the valley tie is through his family. Federal: ABMC (note 1) and the Navy's 1946 California list (note 2), both U.S. Navy. The Navy list's next of kin, his father Russel Lowell Cone of Glendora, is the father named in his brother Dick Cone's obituary, which also names 'a brother Bobby Cone who was killed in WWII' and the family's Saugus Cafe (note 3).

**Proposed footnotes.**

1. *service, rank, service number, memorial, date of death, awards.* American Battle Monuments Commission, Burial and Memorialization Directory: Robert Russel Cone, service number 3823512, Seaman Second Class, U.S. Navy, entered the service from California, World War II; Manila American Cemetery, Missing in Action; date of death August 10, 1943; Purple Heart Medal.
   - URL: https://weremember.abmc.gov/s?q=Cone&v=G&type=16&sort=title:ASC (record 3 of 9) (read 4 October 2026); copy: `cone-abmc.txt`
   - Agrees with the record: no, see below. DISAGREES with wmBranch 'U.S. Army' and deathDate 'March 13, 1945'. The legacy page's own fields say 'Service: United States Navy' and 'Casualty Date: KIA August 9, 1942'; only its heading says U.S. Army and March 13, 1945. The ABMC date is a year and a day after August 9, 1942, consistent with a presumptive finding of death (an inference).
2. *service, rank, next of kin.* Navy Department, State Summary of War Casualties from World War II for Navy, Marine Corps, and Coast Guard Personnel from California (1946), Dead, page 18, National Archives NAID 305189: "CONE, Robert Russel, Seaman 2c, USN. Father, Mr. Russel Lowell Cone, 649½ Glendora Ave., Glendora."
   - URL: https://catalog.archives.gov/id/305189 (read 4 October 2026); copy: `navy-state-summary-california-pdfp20-020.png`
   - Agrees with the record: no, see below. Read by eye from the page image (PDF page 20, printed 18). Disagrees with wmBranch (Army). Explains why he is not in the Army Honor List the 2 October note searched.
3. *family, valley tie.* Obituary of Dick L. Cone (1921-2007), SCVHistory.com (/scvhistory/obituary_dicklcone.htm): "the son of Russell Lowell Cone and Mary Agnes (Trout) Cone"; "He was preceded in death by his parents, a brother Bobby Cone who was killed in WWII"; "The Saugus Café was in the family for 86 years."
   - URL: https://scvhistory.com/scvhistory/obituary_dicklcone.htm (read from the Reggie mirror) (read 4 October 2026); copy: `legacy-obituary_dicklcone.htm`
   - Agrees with the record: yes. Ties the federal Robert Russel Cone to this family (same father). It places the family, not Robert himself, in the valley; the obituary is from 2007.
4. *narrative, home.* SCVHistory.com, Santa Clarita Valley War Memorial, page for him (/warmemorial/ww2_robertcone.htm).
   - Agrees with the record: yes.

**Disagreements (show both values).**

- wmBranch: record has "U.S. Army"; source: U.S. Navy (ABMC; Navy 1946 list; the legacy page's own Service line) (notes 1, 2).
- deathDate / deathDateEdtf: record has "March 13, 1945 / 1945-03-13"; source: ABMC: August 10, 1943, Missing in Action; legacy page body: KIA August 9, 1942 (notes 1, 4).
- title (middle name): record has "Russell"; source: Russel (ABMC and the Navy list); the father's name is spelled both ways (Navy list Russel, obituary Russell) (notes 1, 2, 3).
- wmRank: record has "(empty)"; source: Seaman Second Class (notes 1, 2).

**Searched, not found** (for `inventory/source-searches.json`).

- Where the record's 'March 13, 1945' and 'U.S. Army' came from: not found in any source read; the legacy page's heading is the only place they appear.

## #514 Lawrence E. Kenaston: not found

**Two-source status.** Not met. No approved source was found. He died at home, outside a war zone, so he is outside the Navy State Summary (which excludes deaths in the United States and suicides) and ABMC.

**Proposed footnotes.**

1. *narrative.* SCVHistory.com, Santa Clarita Valley War Memorial, page for him (/warmemorial/ww2_ekenaston.htm).
   - Agrees with the record: yes.

**Disagreements (show both values).**

- wmAgeAtLoss (lead only): record has "34"; source: Find a Grave lead: born February 7, 1912, died January 11, 1945, 'aged 32'. Not a source; shown so the age can be checked.
- wmNarrative (internal): record has "two sentences"; source: the legacy page's narrative is longer (suicide note, born in Washington State, 1920 and 1930 whereabouts, Navy muster rolls 1940, mother in Ventura County); the record keeps only the first two sentences (notes 1).

**Searched, not found** (for `inventory/source-searches.json`).

- ABMC q=Kenaston: 0 results, 4 October 2026.
- Navy State Summary for California (NAID 305189): not in it (2 October reading; it excludes deaths in the United States and suicides by its own notice).
- Web search 4 October 2026: 'Kenaston Marine corporal Guadalcanal Newhall 1945 suicide': nothing.
- Mirror, 4 October 2026, 'kenaston': only the memorial pages and their navigation.

**Notes.**

- Lead (Find a Grave memorial 3723656, saved, never a sole source): Lawrence Edward Kenaston, born February 7, 1912, died January 11, 1945, buried Los Angeles National Cemetery, Section 174, Row B, Site 10; inscription 'CALIFORNIA CPL US MARINE CORPS'. Los Angeles National Cemetery is a VA cemetery, so the VA Gravesite Locator would confirm it, but it is approved only for Wilson, Todd, Conant and Colley: ask Nathan to approve it for Kenaston.
- A newspaper of January 1945 (the Signal, the Los Angeles Times) is the other likely source for the death; none was searched beyond the web.

## #518 Augustus A. (August) Rubel: sourced

**Two-source status.** Met for the death; the home is outside the valley (as the record already says). Official: ABMC lists his grave at the North Africa American Cemetery (note 1). Service: the American Field Service's own archive (note 2) gives his death and his home at enlistment, Piru.

**Proposed footnotes.**

1. *burial, date of death.* American Battle Monuments Commission, Burial and Memorialization Directory: August A. Rubel, entered the service from California, World War II; North Africa American Cemetery, Tunisia, Plot B, Row 3, Grave 2; date of death April 28, 1943.
   - URL: https://weremember.abmc.gov/s?q=Rubel&v=G&type=16 (record 3 of 3) (read 4 October 2026); copy: `rubel-abmc.txt`
   - Agrees with the record: yes. Agrees with burialPlace and wmIncidentDate; adds the plot. deathDate holds only '1943' and could take the day.
2. *service, unit, dates, home, awards.* The AFS Archive (Archives of the American Field Service and AFS Intercultural Programs), "Rubel, August Alexander": born 1899/07/06, died 1943/04/28; WWI driver, S.S.U. 631; WWII unit FFC, ME 32, "Home at time of enlistment Piru, Calif., USA"; "killed in action North of Enfidaville in Tunisia when his ambulance struck a mine"; "He reenlisted in the AFS in November 1942"; "awarded the Légion d'Honneur (Chevalier degree) posthumously on November 11, 2011."
   - URL: https://the-afs-archive.org/people-in-afs/article/rubel-august-alexander-1-1924 (read 4 October 2026); copy: `rubel-afs-archive.html`
   - Agrees with the record: no, see below. Agrees with wmDateOfBirth, wmIncidentDate, wmIncidentLocation, wmHomeOfRecord (Piru). DISAGREES with wmLengthOfService '1917-1919, 1939-1943' (AFS: reenlisted November 1942) and is silent on wmRank 'Trainer' (AFS: 'WWII driver'). New: unit (Forces Françaises Combattantes, ME 32) and the posthumous Légion d'honneur, 2011.
3. *narrative.* SCVHistory.com, Santa Clarita Valley War Memorial, page for him (/warmemorial/ww2_augustrubel.htm).
   - Agrees with the record: yes.

**Disagreements (show both values).**

- wmLengthOfService: record has "1917-1919, 1939-1943"; source: AFS: WWI 1917 on; reenlisted in the AFS November 1942 (notes 2).
- wmRank: record has "Trainer"; source: AFS: 'WWII driver' (the training role is the legacy page's, unsourced) (notes 2).
- date of death (to check): record has "April 28, 1943"; source: ABMC and AFS agree on April 28, but the AFS page's quotation from George Rock's History of the American Field Service (1956) says the party set out 'On the night of 17/18 April' and 'were never seen again alive'. April 28 may be a recorded date, not the night of the explosion; both should be shown (notes 1, 2).

**Searched, not found** (for `inventory/source-searches.json`).

- U.S. Army or Navy casualty lists: not applicable (civilian volunteer).

**Notes.**

- The AFS page for Richard Sterling Stockton (saved), who rode in the same ambulance, also gives † 1943/04/28.
- Off task, for Nathan: legacy page ra4301 says Rubel's letter was 'published in the Ventura Star-Free Press eight days before his death' but captions the clipping 'April 20, 1948'.

## #542 Dean Glenn Todd Jr: partly

**Two-source status.** Half, and the home note must change. One newspaper (Stars and Stripes, note 1) records his death in service and gives his hometown as Canyon Country and his school as Canyon High: this overturns the 'Home, 2026' note ('No connection to the Santa Clarita Valley has been found'). No federal record found (no DoD release for a death outside a theater of war; not in the VA locator).

**Proposed footnotes.**

1. *rank, unit, date and place of death, home, school, specialty.* Joseph Giordono, "Memorial planned for Camp Carroll soldier," Stars and Stripes, September 5, 2004 (Wayback Machine capture of October 23, 2021): "Spc. Dean Todd-Eckard, of Headquarters and Headquarters Company, 307th Signal Battalion, 1st Signal Brigade"; "found in his room around 9 a.m. Tuesday"; "served as a communications and electronics maintainer at the base, in Waegwan"; "the 21-year-old"; hometown given as "Canyon County [sic], Calif."; "In 2001, the newspaper reported, Todd-Eckard graduated from Canyon High School early and entered a six-year enlistment with the Army."
   - URL: https://web.archive.org/web/20211023074739/https://www.stripes.com/news/memorial-planned-for-camp-carroll-soldier-1.23869 (read 4 October 2026); copy: `todd-stripes-2004.html`
   - Agrees with the record: no, see below. Agrees with wmUnit (adds HHC and 1st Signal Brigade), wmBase, wmAgeAtLoss 21, deathDate 8-31-2004 (Tuesday, August 31). DISAGREES with wmRank (Sergeant vs Spc.), the name (Todd-Eckard), wmSpecialty (satellite communications vs communications and electronics maintainer), and the narrative's 'August 30, 2004'. Fills wmHomeOfRecord (Canyon Country, printed 'Canyon County') and a high school (Canyon High, 2001). It cites a Signal article of Thursday, September 2, 2004, not found.
2. *narrative.* SCVHistory.com, Santa Clarita Valley War Memorial, page for him (/warmemorial/terror_deantodd.htm).
   - Agrees with the record: yes.

**Disagreements (show both values).**

- wmRank: record has "Sergeant"; source: Stars and Stripes: Spc. (notes 1).
- title (surname): record has "Dean Glenn Todd Jr"; source: Stars and Stripes: Dean Todd-Eckard (notes 1).
- wmSpecialty: record has "Satellite communications"; source: communications and electronics maintainer (notes 1).
- date (internal): record has "deathDate and wmIncidentDate August 31, 2004; narrative August 30, 2004"; source: found Tuesday (August 31, 2004) about 9 a.m. (notes 1, 2).
- wmHomeOfRecord and editor note 'Home, 2026': record has "empty; 'No connection to the Santa Clarita Valley has been found'"; source: hometown Canyon Country; Canyon High School 2001 (notes 1).

**Searched, not found** (for `inventory/source-searches.json`).

- VA Gravesite Locator (approved), 4 October 2026: lastName Todd + firstName Dean (exact): one result, Dean G. Todd, LtCol USAF, died 2016, not him. Todd + first begins D + death 2004: 7, none. Todd + birth 1982: 1, Todd + birth 1983: 1, neither him. lastName begins Eckard + death 2004: 6, none. 'Todd-Eckard' and 'Todd Eckard' + 2004: none. Saved.
- DoD releases: none issued for a non-theater death (as the record's note says).
- The Signal article of September 2, 2004 cited by Stars and Stripes: not found on the web or in the Wayback index for the-signal.com or signalscv.com (CDX domain filter on 'todd' not run; 'gelig', 'suter', 'sellen', 'acosta', 'colley', 'conant' filters on the-signal.com returned nothing).

**Notes.**

- His mother is named in the article; she is a private person and is left out of the note.

## #540 Dennis Lee Sellen Jr: sourced

**Two-source status.** Met in one federal document, as Slocum's is: the Defense Department's release records his death and gives his home as Newhall (note 1).

**Proposed footnotes.**

1. *rank, unit, date and place of death, casualty, home.* Department of Defense, news release No. 175-07, "DoD Identifies Army Casualty," February 13, 2007 (Wayback Machine capture of January 9, 2008): "Spc. Dennis L. Sellen Jr., 20, of Newhall, Calif., died Feb. 11 in Umm Qasr, Iraq, of non-combat related injuries. He was assigned to the 1st Battalion, 185th Infantry Regiment, Fresno, Calif. The incident is under investigation." Media were referred to the California Army National Guard.
   - URL: https://web.archive.org/web/20080109122741/http://www.defenselink.mil/Releases/Release.aspx?ReleaseID=10512 (read 4 October 2026); copy: `dod-release-175-07-sellen.html`
   - Agrees with the record: no, see below. Agrees with deathDate, wmIncidentLocation, wmUnit, wmBase (Fresno), wmHomeOfRecord (Newhall), wmAgeAtLoss, wmBranch (Army National Guard), wmCombatOperations (Operation Iraqi Freedom). DISAGREES with wmRank: Sergeant vs Spc. (no source read for a posthumous promotion).
2. *narrative, birth, schooling, burial.* SCVHistory.com, Santa Clarita Valley War Memorial, page for him (/warmemorial/terror_dennissellen.htm).
   - Agrees with the record: yes.

**Disagreements (show both values).**

- wmRank: record has "Sergeant"; source: DoD: Spc. (Military Times 'Honor the Fallen' also Spc.) (notes 1).

**Searched, not found** (for `inventory/source-searches.json`).

- Burial (Forest Lawn, Glendale), date of birth, Kennedy High, College of the Canyons, Alabama Guard 2004: not found in any source read; they rest on the legacy page.
- The release was found by reading DoD release IDs 10501 to 10519 in the Wayback Machine (2007 captures of defenselink.mil/releases/release.aspx?releaseid=N), 4 October 2026.

## #538 Ian Timothy D. Gelig: sourced

**Two-source status.** Met in one federal document: the Defense Department's release records his death and gives his home as Stevenson Ranch (note 1).

**Proposed footnotes.**

1. *rank, unit, date and place of death, casualty, home.* Department of Defense, news release No. 163-10, "DOD Identifies Army Casualty," March 2, 2010 (Wayback Machine capture of November 29, 2010): "Spc. Ian T.D. Gelig, 25, of Stevenson Ranch, Calif., died March 1 in Kandahar, Afghanistan, of wounds suffered when enemy forces attacked his vehicle with an improvised explosive device. He was assigned to the 782nd Brigade Support Battalion, 4th Brigade Combat Team, 82nd Airborne Division, Fort Bragg, N.C."
   - URL: https://web.archive.org/web/20101129102522/http://www.defense.gov/releases/release.aspx?releaseid=13347 (read 4 October 2026); copy: `dod-release-163-10-gelig.html`
   - Agrees with the record: no, see below. Agrees with deathDate, wmUnit, wmBase, wmHomeOfRecord, wmAgeAtLoss, wmCombatOperations. DISAGREES with wmRank (Sergeant vs Spc.) and with the narrative's cause ('a suicide bomber drove into his convoy' vs an IED attack on his vehicle). Place: the release says Kandahar; the record says Kandahar Province.
2. *narrative, awards, burial, school, birthplace.* SCVHistory.com, Santa Clarita Valley War Memorial, page for him (/warmemorial/terror_iangelig.htm).
   - Agrees with the record: yes.

**Disagreements (show both values).**

- wmRank: record has "Sergeant"; source: DoD: Spc. (Military Times lists him as Sgt., which suggests a posthumous promotion; no source for it was found) (notes 1).
- wmNarrative (cause): record has "a suicide bomber drove into his convoy"; source: enemy forces attacked his vehicle with an improvised explosive device (notes 1).

**Searched, not found** (for `inventory/source-searches.json`).

- Awards (Bronze Star and the rest), burial at San Fernando Mission, Hart High 2002, born in the Philippines: not found in a source read. Web searches 4 October 2026 for 'Gelig posthumously promoted', 'Gelig Stevenson Ranch Hart High Signal', and Governor Schwarzenegger's statement (quoted by Military Times) did not reach a primary page. Legacy.com has a guest book, no obituary text (saved, not relied on).
- VA Gravesite Locator not used for him (not approved).

**Notes.**

- Release found by reading DoD release IDs 13336 to 13347 in the Wayback Machine, 4 October 2026.

## #536 Jake William Suter: sourced

**Two-source status.** Met. Federal: the Defense Department's release (note 1). Valley: the Honolulu Advertiser (note 2), which gives Stevenson Ranch and draws on the Signal. The release itself gives Los Angeles, so both are shown.

**Proposed footnotes.**

1. *rank, unit, date and place of death, home.* Department of Defense, news release No. 449-10, "DOD Identifies Marine Casualty," June 2, 2010 (Wayback Machine capture of November 29, 2010): "Pfc. Jake W. Suter, 18, of Los Angeles, Calif., died May 29 while supporting combat operations in Helmand province, Afghanistan. He was assigned to 3rd Battalion, 3rd Marine Regiment, 3rd Marine Division, III Marine Expeditionary Force, Kaneohe Bay, Hawaii."
   - URL: https://web.archive.org/web/20101129102846/http://www.defense.gov/releases/release.aspx?releaseid=13572 (read 4 October 2026); copy: `dod-release-449-10-suter.html`
   - Agrees with the record: no, see below. Agrees with wmRank, wmUnit, wmBase, deathDate, wmIncidentLocation, wmAgeAtLoss. DISAGREES with wmHomeOfRecord: Los Angeles vs Stevenson Ranch.
2. *home, deployment.* William Cole, "Kāne'ohe-based Marine dies in Afghanistan," The Honolulu Advertiser, June 3, 2010: "The Defense Department said Pfc. Jake W. Suter, of Stevenson Ranch, Calif., died Friday." The article quotes the Santa Clarita Valley Signal and says he enlisted in June 2009 and that this was his first deployment.
   - URL: https://archives-new.honoluluadvertiser.com/article/2010/Jun/03/ln/hawaii6030325.html (read 4 October 2026); copy: `suter-honolulu-advertiser-2010-06-03.html`
   - Agrees with the record: yes. Agrees with wmHomeOfRecord. Note it attributes Stevenson Ranch to the Defense Department, while the release on defense.gov says Los Angeles (perhaps an earlier or corrected wording; not resolved).
3. *narrative, burial, school, birth.* SCVHistory.com, Santa Clarita Valley War Memorial, page for him (/warmemorial/terror_jakesuter.htm).
   - Agrees with the record: yes.

**Disagreements (show both values).**

- wmHomeOfRecord: record has "Stevenson Ranch"; source: DoD release: Los Angeles; Honolulu Advertiser: Stevenson Ranch (notes 1, 2).
- wmStartTour: record has "Enlisted at 17"; source: Advertiser: enlisted June 2009 (born July 31, 1991 per the record, so 17: agrees) (notes 2).

**Searched, not found** (for `inventory/source-searches.json`).

- Burial (Utah Veterans Memorial Park, Section A Site 2160), West Ranch High, born in Utah: not found in a source read (VA locator not approved for him).

**Notes.**

- The Advertiser names his mother and stepfather; they are left out of the note.
- Release found by reading DoD release IDs 13562 to 13572 in the Wayback Machine, 4 October 2026.

## #534 John Michael Conant: partly

**Two-source status.** Not met. The archive's own grave marker photograph confirms rank and dates (note 1). No federal record or newspaper was found, and the VA locator does not list him, which puts the record's 'Punchbowl' burial in doubt.

**Proposed footnotes.**

1. *rank, branch, dates of birth and death.* Grave marker photographed for SCVHistory.com, held in this archive: "U.S. ARMY / SGT. JOHN M. CONANT / ALWAYS AND FOREVER / JULY 12, 1971 / APRIL 10, 2008."
   - URL: web/uploads/archive-media/legacy/johnmconant_grave.jpg (asset 1888) (read 4 October 2026)
   - Agrees with the record: yes. Agrees with wmRank, wmBranch, wmDateOfBirth, deathDate. The marker is a round bronze plaque, not a government headstone, and names no cemetery.
2. *narrative, home, unit, burial.* SCVHistory.com, Santa Clarita Valley War Memorial, page for him (/warmemorial/terror_johnconant.htm).
   - Agrees with the record: yes.

**Disagreements (show both values).**

- burialPlace: record has "Punchbowl National Cemetery, Honolulu"; source: VA Gravesite Locator (which covers the National Memorial Cemetery of the Pacific): no John Conant who died in 2008 (searched three ways).

**Searched, not found** (for `inventory/source-searches.json`).

- VA Gravesite Locator (approved), 4 October 2026: Conant + John (exact) + death 2008: no results. Conant + death 2008: 6 results (Chester C., Don Ayers, Eugene Wesley, Norwood Lee, Pearl G., Roger W.), none him. Conant + John: 18 results, page 1 read, none died 2008. Conant + birth 1971: no results. Saved.
- Web, 4 October 2026: 'John Michael Conant 1971 2008 obituary'; 'John Conant sergeant medic 4th Infantry Division Colorado Springs died April 2008 Saugus': nothing but the legacy page.
- Mirror, 'conant': no page about him other than the memorial pages.

## #528 Robert Michael Wilson: not found

**Two-source status.** Not met. Nothing found.

**Proposed footnotes.**

1. *narrative.* SCVHistory.com, Santa Clarita Valley War Memorial, page for him (/warmemorial/terror_robertwilson.htm).
   - Agrees with the record: yes.

**Disagreements (show both values).**

- deathDate vs wmIncidentDate (internal): record has "deathDate 8-13-2002; wmIncidentDate August 31, 2002"; source: the legacy page gives both: its fields say August 31, 2002, its narrative August 13, 2002 (notes 1).

**Searched, not found** (for `inventory/source-searches.json`).

- VA Gravesite Locator (approved), 4 October 2026: Wilson + Robert + Michael + death 2002: none. Wilson + Robert + Michael: 4 results (born 1934, 1949, 1950, 1950), none him. Wilson + first begins R + born 1983: 3 (Raymond Keith Jr., Robert Alan b. 04/24/1983 d. 2013, Roger T.), none him. Wilson + first begins R + died August 2002: 11, no Robert Michael. Saved.
- Web, 4 October 2026: 'Robert Michael Wilson' / 'Robert M. Wilson' 82nd Airborne private killed car accident North Carolina August 2002; Fort Bragg paratrooper killed crash August 2002 Wilson 19 Santa Clarita: nothing but the legacy page.
- Mirror: no page about him other than the memorial pages.

**Notes.**

- Next step if wanted: the Fayetteville Observer of August or September 2002 (Fort Bragg's paper), and the Signal of the same weeks.

## #526 Rudy Alexander Acosta: sourced

**Two-source status.** Met in one federal document: the Defense Department's release records his death and gives his home as Canyon Country (note 1).

**Proposed footnotes.**

1. *rank, age, unit, date and place of death, casualty, home.* Department of Defense, news release No. 223-11, "DOD Identifies Army Casualties," March 20, 2011 (Wayback Machine capture of January 15, 2012): "They died March 19 in Kandahar province, Afghanistan, of wounds suffered when they were allegedly shot with small arms fire by an individual from a military security group ... They were assigned to the 4th Squadron, 2nd Stryker Cavalry Regiment, Vilseck, Germany. Killed were: Cpl. Donald R. Mickler Jr., 29, of Bucyrus, Ohio; and Pfc. Rudy A. Acosta, 19, of Canyon Country, Calif."
   - URL: https://web.archive.org/web/20120115160657/http://www.defense.gov/releases/release.aspx?releaseid=14349 (read 4 October 2026); copy: `dod-release-223-11-acosta.html`
   - Agrees with the record: no, see below. Agrees with deathDate, wmUnit, wmBase, wmIncidentLocation, wmHomeOfRecord, wmCombatOperations. DISAGREES with wmAgeAtLoss (20 vs 19; the record's own birth date, May 2, 1991, makes him 19) and shows his rank at death as Pfc. (the record's SP4 is a posthumous promotion per the legacy page, unsourced).
2. *rank, age, home (state).* Office of Governor Edmund G. Brown Jr., "Governor and First Lady Honor Pfc. Rudy A. Acosta," April 15, 2011 (California State Archives web copy): "Pfc. Rudy A. Acosta, 19, of Canyon Country, CA died March 19 in Kandahar province, Afghanistan ..." The Governor ordered flags at half-staff over the Capitol.
   - URL: https://www.archive.gov.ca.gov/archive/gov39/2011/04/15/news16983/index.html (read 4 October 2026); copy: `acosta-governor-2011-04-15.html`
   - Agrees with the record: no, see below. Repeats the DoD text, so it is not an independent witness; it adds the state's honours.
3. *narrative, burial, awards, school.* SCVHistory.com, Santa Clarita Valley War Memorial, page for him (/warmemorial/terror_rudyacosta.htm).
   - Agrees with the record: yes.

**Disagreements (show both values).**

- wmAgeAtLoss: record has "20 (and the narrative: 'at age 20')"; source: 19 (DoD; Governor; SCVHistory TV1210p caption 2012; the withheld body also says 19) (notes 1, 2).
- wmRank: record has "Specialist 4th Class (SP4)"; source: Pfc. at death (DoD). The posthumous promotion is unsourced; and the Army rank in 2011 was Specialist (SPC), not Specialist 4th Class, which the Army abolished in 1985 (general knowledge, not from a source read) (notes 1).

**Searched, not found** (for `inventory/source-searches.json`).

- Posthumous promotion, Bronze Star, Purple Heart, burial at Eternal Valley: not found in a source read (web searches 4 October 2026; health.mil's MHS Honors page returned 404; VA locator not approved for him).

**Notes.**

- SCVHistory.com TV1210p (2012) calls him 'Army PFC Rudy Acosta, a 19-year-old Canyon Country native' (saved). Legacy page SC1401 (City Council bio of Dante Acosta) calls him 'Specialist Rudy Acosta'.

## #524 Stephen Edward Colley: partly

**Two-source status.** Half. Federal: the VA's burial record (note 1) confirms his service, dates and grave, and its war period is Iraq; it does not say he died in service, and no source read ties him to the valley (Valencia rests on the legacy page). NPR (note 2) confirms the death at Fort Hood's unit after Iraq.

**Proposed footnotes.**

1. *rank, branch, dates, burial.* U.S. Department of Veterans Affairs, Nationwide Gravesite Locator: "COLLEY, STEPHEN EDWARD, SPC US ARMY, War Period: IRAQ, Date of Birth: 03/10/1985, Date of Death: 05/16/2007, Buried At: SECTION 18 ROW E SITE 57, CENTRAL TEXAS STATE VETERANS CEMETERY, 11463 State Highway 195, Killeen, TX."
   - URL: https://gravelocator.cem.va.gov/ngl/result (POST: last name Colley, death year 2007; result 10 of 12) (read 4 October 2026); copy: `va-gravelocator-colley-d2007.html`
   - Agrees with the record: yes. Agrees with wmDateOfBirth, deathDate, burialPlace (and gives the full cemetery address). Rank SPC vs the record's 'Specialist Fourth Class': the same grade in practice, but the Army title in 2007 was Specialist. The archive's headstone photograph (asset 1893) reads the same: 'STEPHEN EDWARD COLLEY / SPC US ARMY / IRAQ / MAR 10 1985 MAY 16 2007', '18 E 57'.
2. *death, circumstances, specialty.* Jamie Tarabay, "Suicide Rivals The Battlefield In Toll On U.S. Military," NPR, Morning Edition, June 17, 2010 (transcript): his father calls him "Stephen Colley, private first class, United States Army"; "Stephen, a helicopter mechanic, had been back in the country for about five months since a tour in Iraq"; "On May 16, 2007 ..."; the Army's investigation is quoted on "Pfc. Colley".
   - URL: https://www.npr.org/transcripts/127860466 (read 4 October 2026); copy: `colley-npr-transcript.html`
   - Agrees with the record: no, see below. Agrees with deathDate, wmSpecialty, and the narrative's 'six months after returning from a tour in Iraq'. DISAGREES on rank: PFC (NPR, and the Army investigation as quoted) vs SPC (VA, headstone).
3. *narrative, home, school, family.* SCVHistory.com, Santa Clarita Valley War Memorial, page for him (/warmemorial/terror_stephencolley.htm).
   - Agrees with the record: yes.

**Disagreements (show both values).**

- wmRank: record has "Specialist Fourth Class"; source: VA and headstone: SPC; NPR and the Army investigation as quoted: Pfc. (notes 1, 2).

**Searched, not found** (for `inventory/source-searches.json`).

- Valley tie (Valencia, Valencia High 2003): not found in a source read. Web, 4 October 2026: 'Stephen Colley Valencia soldier Fort Hood 2007'; signalscv.com Colley Valencia High suicide Iraq; Wayback CDX domain filter 'colley' on the-signal.com: nothing (on signalscv.com the query timed out).
- Whether the Ed Colley in this archive (person 26589, water board) is his father: the NPR father is 'Ed Colley', but no source read links that Ed Colley to Santa Clarita. Not proposed.

**Notes.**

- A family-written TAPS article (2020, saved, not proposed) also calls him 'Army Pfc. Stephen E. Colley'.

## For Nathan

- Approve the VA Gravesite Locator for Kenaston: the Find a Grave lead puts him in Los Angeles National Cemetery, a VA cemetery, which would give the first official source for him.
- Cone's branch and date need a correction note: two federal sources say Navy, missing, 1943; the record says Army, 1945.
- Todd's 'Home, 2026' note (no valley connection) is contradicted by Stars and Stripes (hometown Canyon Country, Canyon High 2001), and his name appears there as Dean Todd-Eckard.
- Ranks: Sellen, Gelig and Todd are Spc. in the sources, Sergeant in the records; Acosta and Colley carry 'Specialist Fourth Class', a title the sources do not use.
