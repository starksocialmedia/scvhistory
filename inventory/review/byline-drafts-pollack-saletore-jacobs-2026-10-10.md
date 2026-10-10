# Draft person records: Alan Pollack, Pat Saletore, Shelby Jacobs, 10 October 2026

**These are drafts for Nathan. No record was created, no relation was made, nothing was written to the database, nothing was committed.** The three are the names that `inventory/review/bylines-rule-and-names-2026-10-09.md` found warrant a record. All three are, as far as the sources read show, living people (nothing read reports a death), so the drafts hold public facts only: what was printed about them, in the archive's own pages or in a newspaper, and nothing private (no address, telephone, email, or family member's name beyond what a profile needs).

## What was read

- **Rules**: AGENTS.md; DATA-ORGANIZATION.md (sections 3 and 5, the Person row and "Written By → Person"); docs/PROFILES.md (who gets a record, "a person belongs in this archive for what they did here", the source count, the shape that worked, "a hit on a name is a lead until the page shows it is the same person"); `inventory/review/bylines-rule-and-names-2026-10-09.md` (the three names and the 9 unclear).
- **Model records in Craft** (read only, `storage/runtime/scratch/c10_b.php` through `ddev craft exec`, every field with a value): person #2591 Patti Rasmussen (a living local writer, `writtenBy` on 28 pieces), #16418 Connie Worden, #333 Arthur B. Perkins, and #2579 Darryl Manzer (title and image only). Which persons are `writtenBy` targets came from a SELECT on `scvh_relations` joined to `scvh_fields`.
- **Craft records about the three** (read only): the byline articles #12597 (Pollack), #12649 and #12623 (Saletore), #12617 (Jacobs); #2721 (photograph, 2019, "President Alan Pollack"); organization #378 (SCVHistory.com); #2743 (photograph LW2043, Saugus School bell); #12593 (Gazette, 2008); #12194 and #12422 (Leon Worden's columns of 1998 and 1997); #28283 (board minutes, 19 May 2003); #31893 (event citing Saletore); #2171 (Reynolds bibliography); #16347 (cites #12593). Category and role ids from `scvh_categories` and `scvh_elements_sites`. A search of every person title for Pollack, Saletore and Jacobs found no existing record.
- **The Reggie mirror** (web container, `iconv -f latin1`, tags stripped; scripts `storage/runtime/scratch/c10txt/x.sh` and `ctx.sh`; the file lists for each name came from the 9 October pass, `storage/runtime/scratch/bylmirror.tsv`, not re-run):
  - Pollack: `scvhistory/sg20190512pollack.htm` (The Signal, 12 May 2019, whole page); `pollack20200407flu.htm`, `pollack1114kitcarson.htm`, `pollack0110hoover.htm`, `pollack120813.htm` (bylines and closing lines only); `scvhistory.htm`, `key.htm`, `signal/newsmaker/index.htm`, `scvhs_awards.htm` (the lines naming him); `files/scvhs20102017minutes/files/basic-html/page6.html` (minutes of 22 February 2010).
  - Saletore: `key.htm`, `factfiction.htm`, `scvhs_awards.htm`, `hs0100.htm`, `hs0100a.htm`, `hs8703.htm`, `ms0232.htm`, `ps1801.htm`, `ps2007.htm`, `gg2008.htm`, `lw032402a.htm`, `sg111201.htm`, `sg121205-hs.htm`, `margaretroutledge.htm`, `newhalldt.htm`, `gazette_artifacts1107.htm`, `scvhs-coc-cchistory.htm`, `signal/reynolds/bibliography.html`, `signal/worden/old/lw042397.htm`, `lw052098.htm`, `signal/newsmaker/index.htm`, `oldtownnewhall/oldtownnews.htm`, the docent manual OCR (`files/scvhs_docenttrainingmanual2006/..._ocr.htm`), minutes pages (`scvhs2000minutes` p. 4 and 5; `scvhs20102017minutes` p. 6 and 15), and the City's 2007 book (`files/sc19872007/.../page11.html`): the lines naming her, with the surrounding sentence.
  - Jacobs: `scvhistory/hb1902.htm` (whole page, including the San Diego Union-Tribune article of 1 January 2019 it carries); `oldtownnewhall/gazette/gazette1202-jacobs.htm` (whole); `lat19940302valverde.htm`, `sg022101b.htm`, `hs_valverde_history_1960.htm`, `general.htm`, `canty-pioneers-list.htm` (each only links to his pages); `hart1953yearbook.htm` (no text names him: the yearbook is images, which were not read).
- **Outside the mirror**: SCVTV's page for "Legacy: Shelby Jacobs & John Reid: Val Verde in the 1940s-50s" (scvtv.com, fetched with a generic User-Agent, no email sent): its title, date and one-line description. The video itself was not watched.
- **Not read**: the 9 October pass's other mirror hits for Pollack (428 pages, about 150 bylined); the Newsmaker episode 259 video and its date; Wikipedia (not consulted for any of the three; **no fact below rests on Wikipedia**). The `writtenBy` and image work these drafts imply was not dry-run.

## The shape these drafts follow

From Patti Rasmussen #2591, the nearest model (a living local writer): `title` and `fullName`; `occupation` as text; `body` as an editorial profile (`bodyAuthorship: editorial-2026`) whose first paragraph is what the person did in the valley (PROFILES.md, 3 October); `footnotes` as numbered rows, each a source actually read, `source: editorial-2026`; `editorNotes` only where a reader needs one; `recordProvenance`. Birth and death fields stay empty unless a source gives them. Then the relation that makes the record worth having: `writtenBy` on the byline articles, which today point at no one (#12597, #12649, #12623 and #12617 have no `writtenBy`).

PROFILES.md asks for the sources to be imported before a profile is written. Of the pages footnoted below, only #12597, #12649, #12623, #12617, #2721, #2743, #378, #12194, #12422, #28283, #12593 and #2171 are entries in Craft. The others (among them `sg20190512pollack.htm`, `hb1902.htm`, `hs8703.htm`, `factfiction.htm`, `scvhs_awards.htm`, `sg121205-hs.htm`, `gazette_artifacts1107.htm`) are on the mirror only; Craft holds thumbnails for `hb1902` and `ps1801` as assets, not as records.

---

## 1. Dr. Alan Pollack

**Fields**

| Field | Value |
|---|---|
| title, fullName | Alan Pollack |
| personSearchNames | Dr. Alan Pollack; Alan Pollack, M.D. |
| occupation | Physician; historian |
| roles | Historian (18301); Physician (18363) |
| birthplace | New York City (source 1). No birth date: none is printed in what was read. |
| personOrganizations | Santa Clarita Valley Historical Society (#15493); SCVHistory.com (#378) |
| neighborhood | Newhall (199) (source 1: home in Newhall from 1991) |
| historicalPeriod | 1990-1999 (182), 2000-2009 (183), 2010-2019 (184), 2020-Present (185) |
| bodyAuthorship | editorial-2026 |
| recordProvenance | draft of 10 October 2026, `inventory/review/byline-drafts-pollack-saletore-jacobs-2026-10-10.md` |
| Relations to make | `writtenBy` on #12597; #378's `orgAssociatedPersons` (now #279 only) |
| featuredImage | none proposed. The Signal's 2019 photographs (Dan Watson) are on the mirror; any portrait goes through the credential read first. |

**Body**

Alan Pollack, a physician who has lived in Newhall since 1991, became president of the Santa Clarita Valley Historical Society in July 2007, and with Leon Worden he is one of the two webmasters of SCVHistory.com.[1][2][3] He was still the society's president when he called its board to order in February 2010, and when he presented its awards in December 2019.[4][5]

He writes on the valley's history under the byline "Alan Pollack, M.D.", with a closing line that gives him as a practicing physician and the society's president. The archive holds one of his pieces as a record, on William Mulholland, from the Old Town Newhall Gazette of February-March 2008, and many more on the original site.[6][7] Photographs he contributed are catalogued under the prefix AL in the site's key.[8] He worked for years, with others he credits, toward a national memorial at the site of the St. Francis Dam; The Signal reported in May 2019 that the bill authorizing the Saint Francis Dam Disaster National Memorial and National Monument had been signed into law that March.[1] The society gave him a special award, equivalent to its Outstanding Service award, in 2018.[9]

By The Signal's 2019 profile, he was born in New York City, grew up in the San Fernando Valley, graduated from UCLA in biochemistry in 1979 and from the University of Texas Southwestern Medical School in 1983, and finished his residency at Cedars-Sinai Medical Center in 1986, when he was board-certified in internal medicine. He is also a collector of historic newspapers and rare books.[1]

**Footnotes**

1. Michele E. Buttelman, "Dr. Alan Pollack and the SCV Historical Society," The Signal, 12 May 2019, as carried at https://scvhistory.com/scvhistory/sg20190512pollack.htm: "Pollack became president of the SCV Historical Society in July 2007"; "Pollack purchased a home in Newhall in 1991"; "was born in New York City"; "graduated in 1979 with a bachelor's degree in biochemistry"; "University of Texas Southwestern Medical School in Dallas. He graduated in 1983"; "completed his internship and residency at Cedars-Sinai Medical Center in 1986. That same year, he became board-certified in internal medicine"; "a bill authorizing the 'Saint Francis Dam Disaster National Memorial and National Monument' was signed into law on March 12 of this year." Not yet in Craft.
2. SCVHistory.com, contents page, https://scvhistory.com/scvhistory/scvhistory.htm: "Webmasters: Leon Worden & Alan Pollack."
3. Organization #378 in this archive (SCVHistory.com): "he and Alan Pollack are its webmasters."
4. Santa Clarita Valley Historical Society, Board of Directors, minutes of 22 February 2010, in the society's minutes 2010-2017 as carried at https://scvhistory.com/scvhistory/files/scvhs20102017minutes/ (p. 6): "President Alan Pollack called the meeting to order at 6:35 PM." Not yet in Craft.
5. Leon Worden, "SCV Historical Society Supporters Thanked for Contributions," Heritage Junction Dispatch, January-February 2020, photograph #2721 in this archive: "President Alan Pollack recognized members [...] with 'Outstanding Service' awards."
6. Alan Pollack, "Mulholland: 'There It Is, Take It,'" The Gazette (Old Town Newhall), February-March 2008, article #12597 in this archive, byline "By DR. ALAN POLLACK President, Santa Clarita Valley Historical Society."
7. Alan Pollack, M.D., "Schools, Shops, Churches Close as L.A. Fights a Pandemic," 7 April 2020, https://scvhistory.com/scvhistory/pollack20200407flu.htm, closing line: "Alan Pollack, M.D., is a practicing physician and president of the Santa Clarita Valley Historical Society." The 9 October pass counted 103 mirror pages printing "By Alan Pollack" and 48 "By Dr. Alan Pollack" (not recounted).
8. "Key to Photos," https://scvhistory.com/scvhistory/key.htm: "AL xxxx = Photographs courtesy of Alan Pollack."
9. "Santa Clarita Valley Historical Society Awards, 1975 to Date," https://scvhistory.com/scvhistory/scvhs_awards.htm, Outstanding Service, 2018: "Alan Pollack *" ("Asterisk = Special award, equivalent to Outstanding Service award"). Not yet in Craft.

**Source count (PROFILES.md).** Hub page: none found (no `pollack.htm` biography; the 2019 Signal profile is the nearest). Sources from his lifetime: all of them; the one biographical source is a single newspaper profile, much of it his own account. In Craft: 3 of 9.

**For Nathan.** The 2019 profile also compares the St. Francis Dam with another disaster; that comparison is left out here, since the disaster comparison is held elsewhere.

---

## 2. Pat Saletore

**Fields**

| Field | Value |
|---|---|
| title, fullName | Pat Saletore |
| personAliases | Patricia Saletore (**not confirmed as the same person**: see note) |
| occupation | Historical society executive director; local historian |
| roles | Historian (18301). No role term for an executive director exists; none is proposed here. |
| personOrganizations | Santa Clarita Valley Historical Society (#15493) |
| neighborhood | none: no source read says where she lives |
| historicalPeriod | 1990-1999 (182), 2000-2009 (183), 2010-2019 (184) |
| bodyAuthorship | editorial-2026 |
| recordProvenance | draft of 10 October 2026, this file |
| Relations to make | `writtenBy` on #12649 and #12623; `photoPeople` on #2743 (she is in the 1998 bell photograph) |

**Body**

Pat Saletore was the Santa Clarita Valley Historical Society's docent leader in 1997 and its executive director from at least 2005 to 2010, and she found documents that corrected the valley's record. In April 1997 Leon Worden called her the society's docent leader, who had stocked the Saugus Train Station's gift shop with local history books, and in August 1998 she was one of the society's directors who inspected the Saugus School bell before it went to the Newhall Metrolink station tower.[1][2] She signed the society's minutes as recording secretary in 2000.[3] By November 2005 she was its executive director, and she still was in February 2010.[4][5][6][7][8] The society gave her its Outstanding Service award in 1995 and again in 2000.[9]

She wrote two history pieces for the Old Town Newhall Gazette as executive director: on Henry Mayo Newhall and the town's name (November-December 2005) and on the Southern Hotel (January-February 2006).[4][10] Her research appears across the archive. In June 2012 she found Charles Kingsburry's World War I draft registration card, signed in his own hand, which the archive says appears to have settled the spelling of his name; it holds her scan of the card.[11][12] She identified, with Jerry Reynolds, a photograph found inside a wall of the Newhall Ranch House, and supplied, with Lauren Parker, the documents on the death of Margaret Routledge in 1916.[13][14] Reynolds's history thanks her among its correspondents, and she shared the research and writing of the 2007 video "Historical Evolution of Canyon Country and Surrounding Areas."[15][16] Photographs from her are catalogued under the prefix PS.[17]

**Footnotes**

1. Leon Worden, "Olde Towne Days are here again," The Signal, 1997 (legacy page `lw042397.htm`), article #12422 in this archive: "docent leader Pat Saletore has assembled many local history books for sale in the gift shop."
2. Caption to photograph LW2043, "Saugus School Bell," photograph #2743 in this archive: "August 19, 1998 [...] SCVHS directors Pat Saletore and Tom Frew IV, prior to its move to the Newhall Metrolink station bell tower." Leon Worden's column of 1998 (#12194, legacy page `lw052098.htm`) calls her "Director Pat Saletore".
3. Santa Clarita Valley Historical Society, minutes, 2000, as carried at https://scvhistory.com/scvhistory/files/scvhs2000minutes/ (p. 4): "Pat Saletore, Recording Secretary." (Craft records #28283 and #28285 come from the same set.)
4. Pat Saletore, "Where Do We Get the Name, Newhall?," The Gazette, November-December 2005, article #12649 in this archive, byline "By PAT SALETORE Executive Director Santa Clarita Valley Historical Society."
5. Kristopher Daams, "SCV Historical Society Celebrates 30th," The Signal, 12 December 2005, https://scvhistory.com/scvhistory/sg121205-hs.htm: "said Pat Saletore, the society's executive director." Not yet in Craft.
6. Santa Clarita Valley Historical Society, Docent Training Manual, 2006, https://scvhistory.com/scvhistory/files/scvhs_docenttrainingmanual2006/: "call Executive Director Pat Saletore." Not yet in Craft.
7. Leon Worden, "Valencia Developer Donates Thousands of Artifacts to Museum in Newhall," Old Town Newhall Gazette, November-December 2007, https://scvhistory.com/scvhistory/gazette_artifacts1107.htm: "Pat Saletore, executive director of the Historical Society." Not yet in Craft.
8. Board minutes of 22 February 2010 (as Pollack note 4): "and Pat Saletore, Executive Director."
9. "Santa Clarita Valley Historical Society Awards, 1975 to Date," https://scvhistory.com/scvhistory/scvhs_awards.htm, Outstanding Service, 1995 and 2000.
10. Pat Saletore, "The Finest Hotel South Of San Francisco," The Gazette, January-February 2006, article #12623 in this archive.
11. Caption to HS8703, "Blueprint: Kingsburry House Relocation Site Plan, 1987," https://scvhistory.com/scvhistory/hs8703.htm: "Now, as of June 2012, local historian Pat Saletore appears to have put the issue to rest when she came across" the card; "Saletore's research shows two different dates for his birth." Also "Corrections to the Santa Clarita Valley's Historical Record," https://scvhistory.com/scvhistory/factfiction.htm: "In 2012, local historian Pat Saletore came upon a World War I registration card signed in the man's own hand. It's KINGSBURRY." Neither in Craft.
12. PS1801, "Charles Kingsburry's WWI Draft Card," https://scvhistory.com/scvhistory/ps1801.htm: "9600 dpi jpeg from scan by Pat Saletore." (Craft holds the thumbnail as asset #2439.)
13. HS0100, "'Spirit of Rory MacGregor,'" https://scvhistory.com/scvhistory/hs0100.htm: "Photograph found inside a wall of the Newhall Ranch House. Identification by Jerry Reynolds and Pat Saletore."
14. "The Mysterious Gunshot Death of Margaret Routledge, 1916," https://scvhistory.com/scvhistory/margaretroutledge.htm: "courtesy of SCV historians Pat Saletore and Lauren Parker."
15. Jerry Reynolds, History of the Santa Clarita Valley, web edition edited by Leon Worden, 1998, bibliography (#2171 in this archive): "Pat Saletore" among those thanked for "correspondence".
16. "Video: Historical Evolution of Canyon Country and Surrounding Areas, 2007," https://scvhistory.com/scvhistory/scvhs-coc-cchistory.htm: "JoEllen Rismanchi, Pat Saletore and Patty Robinson, research and writing."
17. "Key to Photos," https://scvhistory.com/scvhistory/key.htm: "PS xxxx = Photographs courtesy of Pat Saletore."

**Source count.** Hub page: none. Sources from her lifetime: all 17. In Craft: 6 (#12422, #12194, #2743, #12649, #12623, #2171, with #28283 from the same minutes set).

**For Nathan.**
- **"Patricia Saletore"** is among the 189 names on the City's cityhood founders plaque, as printed in the City's 2007 twentieth-anniversary book (`files/sc19872007`, p. 11). It is very probably the same woman, but nothing on that page says so (the namesake rule), so it is an alias with a note, not a sentence in the body.
- **"Director" in 1998** may mean a seat on the board (the 1998 bell caption says "SCVHS directors Pat Saletore and Tom Frew IV"), not the staff post. The body says "directors" for 1998 and "executive director" only from 2005, where the source says so.
- Her daughter's 2008 Gazette profile (#12593) names her as the society's executive director; the daughter is not named in the draft.
- Leon's two columns (#12422, #12194) are dated in Craft only by their legacy file names (`lw042397`, `lw052098`); the dates were not read from the pages.

---

## 3. Shelby Jacobs

**Fields**

| Field | Value |
|---|---|
| title, fullName | Shelby Jacobs |
| occupation | Aerospace engineer |
| roles | none: no role term fits ("Engineer" does not exist; none is proposed) |
| birthDate | not set. The Union-Tribune gives his age as 83 on 1 January 2019, which puts his birth in 1935 or late 1934; if Nathan wants a value, "1935?" with `birthEvidence: retrospective` and the note "from his age in January 2019". |
| birthplace | not set: no source read gives it |
| personOrganizations | Hart High School (#16052) |
| neighborhood | Val Verde (212) |
| historicalPeriod | 1940-1949 (177), 1950-1959 (178) |
| bodyAuthorship | editorial-2026 |
| recordProvenance | draft of 10 October 2026, this file |
| Relations to make | `writtenBy` on #12617 |

**Body**

Shelby Jacobs grew up in Val Verde, where his family moved from Santa Monica in 1944 or 1945 when his father was appointed pastor of the Macedonia Church of God in Christ, and he was senior class president of Hart High School's class of 1953.[1][2] In his own account of those years, written for the Old Town Newhall Gazette in 2006, he rode the bus twelve miles to Hart, lettered four years in football, basketball and track, and sometimes worked the night shift at Tip's restaurant at Castaic Junction after a game.[1] In 2009 he and John Reid recalled Val Verde in the 1940s and 1950s for SCVTV's series "Legacy."[3]

He went on to UCLA in 1953 and to a forty-year career in aerospace, at Rocketdyne and then Rockwell in Downey, from which he retired in 1996.[1][2] There he designed the camera system that filmed the stage separation of the unmanned Apollo 6 in April 1968, and worked for fifteen years on the Space Shuttle, rising to the executive level. The Columbia Memorial Space Center in Downey mounted an exhibit on his life in 2018 and 2019.[2]

**Footnotes**

1. Shelby Jacobs, "Life Lessons From The 'Good Old Days,'" Old Town Newhall Gazette, March-April 2006, article #12617 in this archive (byline "By Shelby Jacobs, 1953 Class President, Hart High School"): "My family moved to Val Verde from Santa Monica in 1944-45, primarily because my father was appointed pastor of the Macedonia Church of God In Christ"; "I was a three-sport, four-year letterman (football, basketball and track) at Hart High School [...] and serve as senior class president"; "a twelve-mile school bus ride to Hart"; "I had the bus driver drop me off at Tip's restaurant at Castaic Junction, where I worked all night (the third shift)." The closing line, on the original page: "Shelby Jacobs retired from Rockwell in 1996 after a 40-year aerospace career."
2. "Columbia Memorial Space Center Features Shelby Jacobs, Hart Class of 1953," https://scvhistory.com/scvhistory/hb1902.htm, carrying the Center's exhibit text and Pam Kragen, "Oceanside man celebrated as hidden figure of space program," San Diego Union-Tribune, 1 January 2019: "Jacobs grew up the son of a preacher in the tiny black community of Val Verde"; "In 1953, he enrolled at UCLA and three years later was hired at Rocketdyne"; "Jacobs transferred to Rockwell in Downey, where he spent the rest of his career"; "In 1965, Jacobs was tasked with designing a camera system that could film the rocket separations for the unmanned Apollo 6"; "the cameras recorded the famous footage just seconds after the launch on April 4, 1968" (see the note below); "For the last 15 years of his career, Jacobs worked on the Space Shuttle program"; the exhibit "opened Dec. 15 and will run through the spring." Not yet in Craft (only its thumbnail, asset #14906).
3. SCVTV, "Legacy: Shelby Jacobs & John Reid: Val Verde in the 1940s-50s," SCVTV 2009, posted 10 January 2016, https://scvtv.com/2016/01/10/legacy-2009-shelby-jacobs-john-reid-val-verde-in-the-1940s-50s/: "Shelby Jacobs and John Reid walk down memory lane to Val Verde in the 1940s and '50s." The video was not watched; the page's description was read.

**Source count.** Hub page: `hb1902.htm` with its sidebar (the 2006 piece, the 1953 yearbook, the 2009 video, the 2018-19 exhibit). Sources from his lifetime: all three. In Craft: 1 of 3.

**For Nathan.**
- **The PROFILES test.** "A person belongs in this archive for what they did here." What he did here is grow up in Val Verde and lead his Hart class; his career was elsewhere, and the draft gives it one paragraph for that reason. The 9 October verdict rests on the archive holding a page about him (hb1902), which it does. Whether a childhood and a class presidency are "a meaningful, recurring role in SCV history" is the judgment the written rules leave to you.
- **The two accounts of his camera differ.** The Center's exhibit text on hb1902 says he was "Project Manager of the Apollo-Soyuz orbiter" and that the camera captured "the separation between the first and second stages of the Apollo 6 spacecraft"; the Union-Tribune, on the same page, describes "a ringlike section of the Saturn V rocket separating from the Apollo 6 spacecraft" and gives the footage as recorded "just seconds after the launch", which does not fit a stage separation 200,000 feet up that the same article describes. The draft says only "the stage separation of the unmanned Apollo 6". The exhibit's "Apollo-Soyuz orbiter" title is left out.
- His 2006 piece names his parents and brother; the draft does not.

---

## Read from a description

Nothing new of the kind in these three. One earlier instance is now closed: the 9 October report (its item 4) said Jacobs's verdict rested on hb1902's page title alone; the page was read whole today, and it does hold an article about him, so the verdict stands on the page, not the title.
