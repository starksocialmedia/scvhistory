# The Saugus High School shooting, 14 November 2019: what the mirror holds and what an import would involve

Survey for Nathan, 5 October 2026 (Claude). Read-only: no record was written, no file
changed except this one. Nothing here is a ruling; section (d) lists the decisions that
come before any script.

**How it was searched.** The Reggie mirror (`/Volumes/Reggie/SCVHistory/scvhistory.com`),
every `*.htm` and `*.html` page, with `LC_ALL=C /usr/bin/grep -lia` through
`find -print0 | xargs -0`, on 5 October 2026. Terms, in two passes: `saugus ?strong`,
the shooter's surname, `Muehlberger`, `Koegle`, `sg20191114shs`, `saugus high school
shooting`, `saugushighshooting`, `saugus high shooting`; then `Dominic Blackwell`,
`Tretta`, `November 14, 2019`, `11/14/2019`, `11-14-2019`, `Nov. 14, 2019`, `Saugus
High` within 120 characters of shooting, shooter, tragedy, gunman or gunfire (both
orders), `Grace Baptist` near 2019, `Vince Ferry`, and `Saugus High` near memorial,
anniversary or remembrance. 85 and 27 pages matched; each was read in Python as
latin-1 and sorted by hand. Most of the first pass were the 1954 Muehlberger geology
thesis and an unrelated Koegle on College of the Canyons pages. Images were checked
on disk, each referenced path resolved against the page. The pages and images are in
the Reggie manifest (`scvhistory-manifest-2026-08-20.sha256`): 67 matching paths.

---

## (a) What the mirror holds

### The series: eleven pages under one navigation box

Every page carries the same "SAUGUS HIGH SCHOOL SHOOTING" box listing the other ten,
with a thumbnail each. `sg20191114shs.htm` is both the first news report and the hub:
`saugus.htm` ("Saugus High School Shooting, November 14, 2019") and the 2019 line of
`timeline.htm` link to it. All under `scvhistory/`. No page carries a webmaster note,
and none carries Leon Worden's byline. The site's own hand shows in the page titles,
the headers, two unattributed introductions (the Koegle video page, the letter page)
and the timeline line.

| # | Page | Page title (the site's) | Date | Kind | Byline and source | What it holds | Images (all exist in the mirror) |
|---|---|---|---|---|---|---|---|
| 1 | `sg20191114shs.htm` | Breaking News: 2 Students Killed, 4 Wounded (11/14/2019) | Thu 14 Nov 2019 | news text, 2 videos, aerial frames; series hub | Jim Holt, The Signal; Caleb Lunetta and Tammy Murga (Signal) and Stephen K. Peeples (SCVTV/SCVNews.com) contributed | 1,168 words. The day's running report: LASD briefings (Capt. Kent Wegener, Sheriff Alex Villanueva), hospitals, Central Park evacuation and reunification, Supervisor Barger, Joe Messina. Names the shooter. A caption gives the street and block of his family's home. | 8 inline, `sg20191114shs.jpg`, `sg20191114shs02.jpg` to `08.jpg` (1,255 to 1,464 px wide; `08` is 430x547), plus 2 video posters `sg20191114shs_video01.jpg`, `_video02.jpg`. Seen: `shs`, `02`, `03` are KTLA-5 helicopter frames of the injured being treated, each with a blue box over the person (who added it is not recorded); `04`, `05` KTLA frames of the search of the shooter's home; `06` KTLA frame of the evacuation lines at Central Park; `07` a #SAUGUSSTRONG banner on a fence (Two-8-Nine Media watermark); `08` the shooter's school portrait. The captions on the page sit out of step with the images they describe, and "Photo: Two-8-Nine Media" sits under two frames that carry the KTLA logo. Videos `scvtv.com/vid/Saugusshooting_20191114.mp4`, `saugusshooting_secondupdate_11142019.mp4`: not in the mirror. |
| 2 | `lat20191115shs.htm` | Campus Shooting Kills Two (L.A. Times 11/15/2019) | Fri 15 Nov 2019 | news text | Marisa Gerber, James Queally, Hannah Fry, Sarah Parvini, Los Angeles Times; eleven more staff contributed | 1,462 words. The narrative account; the shooter's background; his late father's arrests and a battery arrest with no charges filed; neighbors. | 2: `lat20191115shs.jpg`, `02.jpg` (1600 px; Al Seib/LAT). The first names a 16-year-old student hugging his father. |
| 3 | `austindave20191115shs.htm` | We Stand Together, #SaugusStrong (Video 11/15/2019) | 14 to 15 Nov 2019 | video | "Video & story by Austin Dave" | 87 words of text; video `scvtv.com/vid/shsshootingaustin_20191115.mp4`, not in the mirror. | Poster `austindave20191115shs.jpg`. |
| 4 | `scvtv20191115shs.htm` | Press Conference; Saugus Grads Set Up Fund (SCVTV 11/15/2019) | Fri 15 Nov 2019 | video and news text (two pieces) | Press conference summary, SCVTV; fund story by Stephen K. Peeples, SCVTV/SCVNews.com | 734 words. LASD briefing points; a GoFundMe by two 2010 alumnae, quoting the fund page, **with the organizers' work email addresses and LinkedIn addresses, and three personal email addresses for vigil donations, one of them the ASB president's (17)**. | `scvtv20191115shsvigil.jpg` (the City's vigil announcement graphic, 1200x800), poster `scvtv20191115shs_video.jpg`. Video `saugusshooting_pressconference20191115.mp4`, not in the mirror. |
| 5 | `lat20191116shs.htm` | Shooting Victims Mourned (L.A. Times 11/16/2019) | 15 to 16 Nov 2019 | news text, **five pieces** | Los Angeles Times: (a) "This world lost a shining light", Colleen Shalby, Alejandra Reyes-Velarde, Leila Miller, Soumya Karlamangla, 16 Nov; (b) "Shooting Victims Identified", Reyes-Velarde and Shalby, 15 Nov; (c) "Unregistered firearms seized from teenage shooter's home", Hannah Fry, Miller, Richard Winton, Brittny Mejia, 16 Nov; (d) "School shooting stirs a search for answers", Mejia, Ruben Vives, Winton, Reyes-Velarde, 16 Nov; (e) "Peace of mind on list of casualties at school", commentary by Sandy Banks, 16 Nov | 5,115 words. The two who died, remembered; the shooter's death on 15 Nov; the weapon in detail; his family's separation and the street they lived on; many students named. | 9 references, 8 files of its own: `lat20191116shs.jpg`, `02` to `06.jpg` (Kent Nishimura, Irfan Khan, Luis Sinco/LAT; captions name students at the memorial, two of them sisters aged 16 and 12), `shs2019-graciemuehlberger_full.jpg` ("Courtesy of Muehlberger family"), `shs2019-dominicblackwell.jpg` (no credit), and `sg20191114shs08.jpg` again. |
| 6 | `sg20191117shs.htm` | #SaugusStrong Vigil (Video 11/17/2019) | Sun 17 Nov 2019 | video and news text | Video "produced and live-streamed by SCVTV for the City of Santa Clarita. Photos: City of Santa Clarita"; "Community Comes Together for Vigil", Emily Alvarenga, The Signal | 1,172 words. The Central Park vigil: Principal Vince Ferry, the ASB president, both families, Mayor Pro Tem Cameron Smyth, Superintendent Vicki Engbrecht. | 4 full-resolution: `sg20191117shs.jpg` (5760x3840), `02`, `03` (Cory Rubin/The Signal: the Blackwell family on stage; Gracie's brothers speaking), `04` (3800x5700, the two memorial crosses; no credit of its own); poster `saugusstrongvigil20191117.jpg`. Video `saugusvigil_20191118.mp4`, not in the mirror. |
| 7 | `addisonkoegle20191117.htm` | Video Message from Addison Koegle, 14 (11/17/2019) | 17 Nov 2019 | video | Introduction unattributed (the site's) | 96 words: an injured 14-year-old's message recorded for the vigil; names the shooter. | None local. Poster and both videos (`addisonkoegle20191117.mp4`, `_full.mp4`) on scvtv.com, not in the mirror. |
| 8 | `bryanmuehlberger20191117.htm` | A Personal Letter from the Parents of Gracie Muehlberger (11/17/2019) | letter 17 Nov; "released for publication by the family on November 19, 2019" | document (letter) with family photographs | Bryan Muehlberger, with Cindy Muehlberger; introduction unattributed (the site's) | 1,609 words. Her life; her brothers; friends by first name; the families of the others hurt; the detective at her side. | 10: `shs-gracieannemuehlberger.jpg`, `02` to `10.jpg` (311 to 1875 px; family photographs released with the letter). |
| 9 | `lat20191118shs.htm` | Thousands Mourn Pair of Victims (L.A. Times 11/18/2019) | Mon 18 Nov 2019 | news text, **two pieces** | Los Angeles Times: (a) Sandy Banks and Laura Newberry; (b) "Facing a New Wave of Grief", Marisa Gerber | 1,792 words. The vigil; two students' account of the day. | 4: `lat20191118shs.jpg` to `04.jpg` (1600 px; Carolyn Cole, Kent Nishimura, Irfan Khan/LAT; captions name students). |
| 10 | `sg20191119shs.htm` | Last Victim Home from Hospital (11/19/2019) | Tue 19 Nov 2019 | news text | Tammy Murga, The Signal | 407 words. Mia Tretta, 15, home; her wound described; the Koegle family's statement, which **asks the media to respect the family's privacy**. | 1: `shs-miatretta.jpg` (800x1199, no credit). |
| 11 | `hd20200112.htm` | Hart District Actions in Light of Saugus High School Shooting, 1-12-2020 | 12 Jan 2020 | document, with PDF | Mike Kuhlman, Deputy Superintendent, William S. Hart Union High School District; emailed to district families | 2,765 words: Remember, Recover, Reinforce; mental health and threat assessment; consultants' biographies. | `hd20200112.pdf` (511,806 bytes); district logo thumbnail. |

Thumbnails, all present: `sg20191114shst.jpg`, `lat20191115shst.jpg`, `austindave20191115shst.jpg`,
`scvtv20191115shst.jpg`, `lat20191116shst.jpg`, `sg20191117shst.jpg`,
`addisonkoegle20191117t.jpg`, `bryanmuehlberger20191117t.jpg`, `lat20191118shst.jpg`,
`shs-miatrettat.jpg`, and `mugs/hartschooldistrictlogot.jpg`. Derivatives; not for import.

### Around the series

| Page | What | Bearing |
|---|---|---|
| `hd20200406.htm` | "Principal Vince Ferry, Saugus High, Honored by Council on School Culture", Hart District, 6 April 2020; `vinceferry2020.jpg`, `_large.jpg` | One paragraph on "the tragic events of November 14, 2019". The latest page found that mentions the shooting. |
| `timeline.htm`, 2019 | Leon's chronology: "November 14: Gunman, age 16, slays 2 fellow Saugus High School students, wounds 4 others before turning gun on himself", linked to page 1 | The count disagrees with the later reports (below). Leon's line is not rewritten. |
| `saugus.htm`, `people.htm` | Index lines linking page 1 and the Ferry page | Navigation only. |
| `obituary_markberhow.htm` (listed on `obits.htm`) | Eternal Valley's 2017 obituary of the shooter's father, one photograph, a mortuary video on scvtv.com | Not about the shooting and does not link to it. The connection exists only in the L.A. Times text of 15 November. |

### Counts

- **11 series pages**, plus 1 later district page that mentions it, 3 index and chronology lines, and 1 adjacent obituary.
- **16 text pieces**: The Signal 3 (pages 1, 6, 10); SCVTV and SCVNews.com 2 (page 4); Los Angeles Times 8 (pages 2, 5, 9); the family's letter 1 (page 8); the Hart District 2 (page 11, and the Ferry page). Plus 3 short video introductions (pages 3, 6, 7).
- **43 content images, none missing**: 10 on page 1 (8 inline, 2 posters), 2 on page 2, 1 on page 3, 2 on page 4, 8 on page 5, 5 on page 6, 10 on page 8, 4 on page 9, 1 on page 10. By source: KTLA frames 6, Two-8-Nine Media 1, the shooter's portrait 1, L.A. Times 12, The Signal 3, uncredited vigil photograph 1, SCVTV video posters 5, the City's vigil graphic 1, family photographs 11 (10 with the letter and 1 on page 5), Dominic Blackwell's portrait 1, Mia Tretta's portrait 1, and the 1 PDF. Plus 11 thumbnails.
- **7 video files on the series pages and 1 on the obituary, all on scvtv.com, none in the mirror.**
- **Nothing dated after 6 April 2020.** No anniversary, no permanent memorial, no investigation's closing report: the mirror's coverage ends there.

### Disagreements among the sources (for the event body and source faults)

- **The count.** Page 1's title and Leon's timeline say 4 wounded: the shooter counted among the wounded, written before he died on 15 November. The L.A. Times of 15 November: two killed, three others wounded. The Signal of 19 November: "three teenagers dead and three others wounded" (the shooter counted among the dead).
- **Gracie Muehlberger's age.** Page 1 (morning of the 14th) reports a 16-year-old girl; the L.A. Times of the 15th and her parents' letter (born 10 October 2004) make her 15.

---

## (b) What Craft holds

Queried on 5 October 2026, read only (`storage/runtime/saugus_survey_probe*.php`, gitignored,
through `ddev craft exec`): `Entry::find()->status(null)->search()` for each name and phrase,
`scvh_elements_sites.content LIKE` for each page filename and for the names and phrases, and
`scvh_assets.filename LIKE` for each image pattern.

**Nothing about the shooting.** No entry, no asset, no legacy key or URL from any of the
eleven pages or the obituary; no text containing the shooter's surname, Tretta, "Dominic
Blackwell", "Saugus Strong", "November 14, 2019" or "Vince Ferry". The one "Muehlberger"
is the 1954 thesis on photograph #3343.

**Records an import would point at, all live:**

| Record | Id | Note |
|---|---|---|
| Saugus High School | #21777 | organization, school (NCES, CDS), parent #21588. **No community set.** No place record for the campus. |
| William S. Hart Union High School District | #21588 | |
| Santa Clarita Valley Sheriff's Station | #29682 | |
| Los Angeles County Sheriff's Department | #29282 | |
| Henry Mayo Newhall Memorial Hospital | #380 | |
| The City of Santa Clarita | #394 | |
| The Santa Clarita Valley Signal | #376 | 26 records already credit it as publisher, two of them news stories imported verbatim as documents (#28295, 1985; #28293, 1987). |
| Kathryn Barger, Marsha McLean, Cameron Smyth, Joe Messina, Leon Worden | #29288, #23085, #16380, #26549, #279 | Spoke or appear in the coverage; none needs relating (D9). |
| Era Contemporary, period 2010-2019 | categories #171, #184 | as used by the SCV Water event |

**Missing:** Central Park (photograph #4961, "Future Santa Clarita Central Park Site, 1998",
relates no place); the Los Angeles Times (no record, and no `lat*` page has ever been
imported); SCVTV as an organization (only role #18381); KTLA; Vince Ferry; Alex Villanueva;
Grace Baptist Church; Providence Holy Cross.

**One record to leave alone:** candidacy #29998, Brian E. Koegle (college board, 2009). The
letter names an injured girl's parents as "Brian and Lindsey"; College of the Canyons pages
name a Brian and Lindsay Koegle. Joining them would be an inference about a living family,
and nothing should.

**Three events exist** (#875 Northridge Earthquake, #27712 SCV Water, #20228 Lyon, Wiley and
Jenkins). #875 is the closest pattern: editorial body with footnotes, legacy URL, places,
era and period, and the legacy text held in `withheldBody`.

---

## (c) What an import would make

Mapped to the sections and fields as they stand (docs/DATA-MODEL.md, generated 5 October).

**1. One event: "Saugus High School Shooting"** (`events/event`, the title the legacy index uses).
- `eventDate` "November 14, 2019", `eventDateEdtf` 2019-11-14, `startEvidence` contemporary.
- `body`: a short editorial account (`editorial-2026`), every sentence footnoted to the source records through `footnotes` and `footnotesOn` (which takes any section). The counts and ages stated as the later sources settle them, the disagreements shown in notes.
- `eventSignificance`: one line.
- `eventOrganizations`: #21777, #21588, #29682, #29282, #380, #394.
- `eventPlaces`: Central Park, if made (D10).
- `eventPersons`: none (D2, D3, D4, D9).
- `historicalEra` #171, `historicalPeriod` #184, `neighborhood` Saugus.
- `editorNotes`, position top: the content advisory (D7).
- No `featuredImage` until a photograph clears its rights (D6).

Documents cannot point at an event: documents have no event field, and `eventArticles`
takes Articles only. Citing them through `footnotesOn` needs no schema change and is how the
Mentry profile cites its sources.

**2. Source records, one per text piece, as documents** (`documents/document`, the pattern
of the 1985 and 1987 Signal stories and `import_connie_worden_sources.php`):
- `body` the piece verbatim (DATA-MODEL, Transcription and interpretation). `webmasterNoteTop` the site's introduction where there is one, verbatim (the letter page, the Koegle page). `webmasterNoteBottom` the credits, "contributed" lines, and any editing note (D8).
- `originallyPublishedTitle` only where the original printed the headline; the page titles are the site's.
- `originalPublishDate` and EDTF; `publishedBy` The Signal #376, SCVTV, the Los Angeles Times or the Hart district; `legacyKey` and `legacyUrl` from the page; `sourcePath`; `finePrint` the page's copyright block verbatim ("Site contents ©SCVTV ..." and the comment line "ALL UNATTRIBUTED CONTENT (c)1996-2019 SCVTV/SCVHistory.com - OTHER COPYRIGHTS APPLY").
- `subjectPerson`: none (see D2 to D4). `historicalPeriod` #184, `neighborhood` Saugus.
- The district's PDF in `documentFiles`.
- Where one page holds several pieces (page 4 two, page 5 five, page 9 two), one document per piece; the page's legacy URL goes on the first and the rest say where they stood.
- Count: 16 documents (3 Signal, 2 SCVTV, 8 L.A. Times, 1 letter, 2 district), of which the 8 L.A. Times wait on D5.

**3. Photographs and assets**, each with provenance recorded at receipt (`sourceChecksum`,
`legacySourcePath`), `creator` and `rightsHolder` as printed, and `license` empty where none
is known, which is not permission (DATA-MODEL, Provenance and rights). How many become
public `photographs` records turns on D6; the plan proposes 6 never, and the rest held,
attached to their documents, or published, as ruled.

**4. Places.** Saugus High School stays an Organization (#21777): a school acts, and nothing
in the coverage gives the campus a story apart from the school. Central Park recurs (the
1998 photograph, articles on its building, the evacuation, the memorial, the vigil):
a Place, `placeType` park, community Saugus, made before the event (D10).

**5. People.** None (D2 to D4, D9).

**6. Redirects.** All eleven legacy URLs resolve: each to its document, the three video
pages to the event until their videos and transcripts exist (D11).

---

## (d) Decisions before anything is written

Each with a recommendation and the rule it rests on. One path each.

**D1. Scope: a dedicated, small import, read in a dry run, not a batch.** Recommend: yes, in
the Mentry shape (extract with checksums, sources first, then the event, then a dry run Nathan
reads). Rule: docs/PROFILES.md, "The shape that worked"; the import-script skill (dry run by
default); PHILOSOPHY 2.10 (humans decide).

**D2. The shooter: whether and how to name him.** He was 16, died on 15 November 2019, and
was named by the Sheriff's Department; every source names him. Recommend: the sources keep
his name exactly as printed, and the archive's own words do not use it. The event's title,
body, significance line, captions the archive writes, SEO and JSON-LD say "a 16-year-old
student". No person record, no relation, no portrait. Reasons: transcription is verbatim and
Leon's pages are not rewritten (PHILOSOPHY 2.7; DATA-MODEL, Transcription and interpretation);
a record needs significance to SCV history, and his only role is the act (PHILOSOPHY 2.3,
docs/PROFILES.md, Who gets a person record); his mother and family are living and the archive
does not publish a living family's structure (DATA-MODEL, Kinship on the page); and the
precedent of 3 October for the Suomisto story: "the archive holds it as published ... do not
extract those details into structured fields or person records"
(`import_connie_worden_sources.php`). His school portrait (`sg20191114shs08.jpg`) is not
imported as a record.

**D3. The two students who died, Gracie Anne Muehlberger (15) and Dominic Blackwell (14).**
Recommend: named in the event body, as the coroner, their families, the vigil and the memorial
crosses named them; no person records; no family relations; their parents and brothers named
only where a source names them. Reasons: the families made the names public, and an account
that left them out would erase them; but a person record is for a recurring role (PHILOSOPHY
2.3), and the kinship rule keeps living parents and siblings out of the archive's structure
(DATA-MODEL, Kinship on the page). If Nathan wants a memorial record for each, as the war
memorial does for its dead, that is a new kind of record and his decision; this survey does
not propose one.

**D4. The three students wounded.** Mia Tretta (15) and Addison Koegle (14) are named in the
Signal and the L.A. Times; the third, a boy of 14, is not named except by a first name in the
letter. All were minors then and are adults now. The Koegle family asked the media for privacy
(page 10). Recommend: their names stay in the verbatim sources only; the archive's own text
says "three other students were wounded"; no records, relations or `photoPeople`; Mia
Tretta's portrait and Addison Koegle's video page held. Reasons: living people, minors at the
time, with no public role in the archive's sense (PHILOSOPHY 2.3; docs/PROFILES.md); a stated
privacy request; and #29998 must not be joined to them (section b). A later public life, if
one is documented, would be sourced and decided on its own.

**D5. The Los Angeles Times text: 8 pieces on 3 pages.** The archive has never imported an
L.A. Times page, and nothing on the pages says the site held a licence. Recommend: create the
8 documents with full citation, byline, date and legacy URL, and their verbatim bodies, **saved
disabled** until Nathan settles the rights; the legacy URLs redirect to the event meanwhile.
Reasons: "Having a file is not holding the rights to it ... an empty licence is not permission"
(DATA-MODEL, Provenance and rights); and saved disabled, nothing is lost and nothing is
published. The pieces also carry the most sensitive detail: the weapon, the family's street,
the late father's arrests.

**D6. Photographs, by who holds them.** Recommend:
- **Never as public records (6):** the three KTLA frames of the injured under treatment (graphic; altered with blue boxes by an unknown hand, an edit no one recorded; KTLA's broadcast); the two KTLA frames of the search of the shooter's home (a living family's house, with the street in the caption); the shooter's portrait (D2). They stay as mirror files with their checksums in the extract, not as assets.
- **Held for rights, not published (16):** the 12 L.A. Times photographs (theirs; their captions name minors), the KTLA evacuation frame, the Two-8-Nine Media banner, Mia Tretta's and Dominic Blackwell's uncredited portraits.
- **The family's 11 photographs:** with the letter only, `rightsHolder` the Muehlberger family, `courtesyOf` the family, and no person relation (D3). The family released the letter and photographs for publication in November 2019; whether that reaches a new archive in 2027 is Nathan's to judge, and the recommendation is to treat it as reaching it, since the letter was written to be read publicly.
- **The Signal's 3 vigil photographs and the uncredited fourth:** on the same footing Nathan sets for Signal text (D8). The fourth is credited nowhere on its own; it is held until its maker is known.
- **SCVTV's 5 video posters and the City's vigil graphic:** as SCVTV and City material, with the video records when those exist (D11).
Reasons: DATA-MODEL, Provenance and rights; Generated and edited images (an edited image enters on Nathan's word with its edit recorded, and here the edit is unknown); PHILOSOPHY 3 (credit, source and caption always shown).

**D7. A content advisory.** The texts describe gunshot wounds and a suicide, and some give
the weapon's details. Recommend: a one-line advisory as an editor note in the top position
on the event and on every source record ("This record concerns a school shooting in which
students were killed, and describes injuries and a suicide."). Not `culturalSensitivityNote`:
the event and document templates render it after the body, where an advisory is read too
late, and its one use so far is the Tataviam consultation note. The top editor note already
renders above the body, so no template changes. Rule: none says this yet; it would be a new
rule, Nathan's to make and to write into DATA-MODEL.

**D8. The Signal and SCVTV text, and one redaction.** Recommend: import the 3 Signal and 2
SCVTV pieces verbatim as published documents, on the footing of the 1985 and 1987 Signal
stories already in the archive (#28295, #28293), with the copyright block as `finePrint`.
**One exception to verbatim:** page 4 prints the fund organizers' work email addresses and
LinkedIn addresses, and three personal email addresses for donations, one of them a
17-year-old's. Recommend: those five contact details replaced with "[contact details
withheld]", the change recorded in `webmasterNoteBottom` as an editing note. Reasons:
DATA-MODEL places editing notes in `webmasterNoteBottom`; the archive does not publish a
private minor's contact details because a 2019 page did. Nathan decides whether this exception
stands; if it does, it should be written into DATA-MODEL as a rule.

**D9. Public officials in the coverage.** The Sheriff, Supervisor Barger, the Mayor, the
Mayor Pro Tem, the principal, the superintendent and the deputy superintendent spoke in their
public roles. Recommend: no `eventPersons` and no new person records from this import; their
names stay in the text. Reasons: a statement at a briefing is a passing mention (PHILOSOPHY
2.3; DATA-ORGANIZATION, Tags). Vince Ferry is the one candidate who may later earn a record,
for what he did at Saugus beyond that week (the Ferry page of April 2020); that is a separate,
later decision.

**D10. Central Park.** Recommend: create the Place "Santa Clarita Central Park" before the
event, park, community Saugus, with photograph #4961 related, and the event pointing at it.
Reasons: it recurs and is a specific site (DATA-ORGANIZATION, section 2: the test); entities
first (PHILOSOPHY 2.4). Also: give Saugus High School #21777 its community, Saugus, which it
lacks today.

**D11. The videos.** Seven files on scvtv.com, none in the mirror; PHILOSOPHY 3 requires a
transcript for every video. Recommend: no video records yet; the three video pages' legacy
URLs redirect to the event; Nathan asks SCVTV for the files, which go to Archive.org (by
Nathan, never an agent) with transcripts made then. Addison Koegle's message stays out even
then, on D4.

**D12. Where page 1's URL goes.** `sg20191114shs.htm` is the Signal's report and the series'
hub, and the index and timeline link to it as "the shooting". Recommend: the URL redirects to
the event, and the Signal document records that it stood there. Reason: readers and citations
of that URL wanted the event; the document keeps its provenance (PHILOSOPHY 2.8).

**D13. The father's obituary.** Recommend: imported, if at all, with the obituaries in their
own course, never related to the event, and the connection never stated by the archive.
Reason: a private person's death notice from 2017; the kinship rule (DATA-MODEL, Kinship on
the page).

**D14. What happened after April 2020.** The mirror holds nothing later. Leads from general
knowledge, **checked against no source** and to be treated as unsourced until they are: a
permanent memorial; anniversaries; the investigation's conclusion; the later public advocacy
of a parent and a survivor. Recommend: none of it in this import; a research brief for Grok
Bot after the import, from outside sources (not scvhistory.com), each item then its own
decision.

---

## (e) Order of work

1. **Nathan rules on D1 to D14.** Nothing is written before.
2. **Extract** (read-only Python): `inventory/legacy/saugus-high-2019-sources.json`, the 16
   pieces verbatim, split per piece, each page's and each image's SHA-256 matched against the
   Reggie manifest, captions and credits as printed, video URLs listed, the D8 redaction
   applied and recorded, and the six D6 "never" files listed with checksums and no import.
3. **Entities first:** Central Park; the Los Angeles Times and SCVTV as organizations if
   Nathan wants them as publishers; Saugus High School's community. Dry run, read, apply.
4. **Source records:** the 16 documents (the 8 L.A. Times saved disabled), with the D7 advisory.
   Dry run, read, apply.
5. **Assets and photographs** per D6, with provenance and rights fields. Dry run, read, apply.
6. **The event**, last, its body citing the documents; Nathan reads the prose in the dry run.
7. **Redirects** for the eleven legacy URLs (D11, D12).
8. `check_render.php`; CHANGELOG, ERRORLOG and HANDOFF; the source faults for the count and
   the age (section a), on the pattern of the existing `sourceFaults` records.
9. **Later, separately:** the videos and transcripts (D11); the research brief (D14).

One writer to the database at a time; each step is its own script under `scripts/import/`.

---

## Rulings, 5 October 2026 (Nathan)

- **D2, the shooter:** as recommended. The sources keep his name as printed; the archive's own words, titles and structured data never use it; no record, no portrait. The reasoning goes on the event record, so it reads as a decision rather than an omission: it is the settled convention in reporting on these events and what a thoughtful editor would do; the sources are kept as published because altering them would falsify the record, and the archive does not amplify beyond that.
- **D3:** Gracie Muehlberger and Dominic Blackwell are named in the event's text; no person records. "A record of this event that does not name them would be a strange silence."
- **D4:** the wounded are named only where the sources name them; no records; the Koegle family's request honoured.
- **D6:** the six are never imported ("should not be in this archive at all"); the sixteen are held for rights.
- **D8:** the contact details are replaced with "[contact details withheld]" and the change is recorded on the record.
- **Added:** Gracie Muehlberger's age (16 in the first report; she was 15) is a correction on the record. The Muehlberger family's letter is the most important document in the set: the family's own words, and the only piece that is not journalism.
- **Waiting:** D1, D5, D7, D9 to D14, sent to Nathan together.
