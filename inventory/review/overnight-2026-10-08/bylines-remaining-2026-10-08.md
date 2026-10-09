# The bylines still not captured, 8 October 2026 (overnight)

Claude. Read only: nothing was written to the database. Item 7: "The 356 bylines still not captured. Run the same pass as the 746, with basis recorded. Hold the writes."

Dry run script: `scripts/import/bylines_remaining_2026_10_08.php` (`$APPLY = false`). Every row was read by hand from the legacy page on Reggie (`/mnt/reggie/scvhistory.com` inside the container, mounted and readable tonight). The script re-reads each page and refuses any row whose quoted words are not on it. Tonight's run refused **0**. It also sweeps every record that has no author for a byline-shaped line, so nothing outside the table is dropped. `check_census_reads.php` passes for this script. Three other failures are in `boston_portrait_2026_10_08.php`, which is not mine and was left alone.

**Run (MacBook):**

```
ddev craft exec "eval(file_get_contents('scripts/import/bylines_remaining_2026_10_08.php'))"
```

## Where the 356 stand

The 5 October census tracked 362 records: 356 class C, where the page prints a byline Craft had not captured, and 6 class B, where a name appears only in sourceLine.

| State tonight | Records |
|---|---|
| Linked on 5 and 6 October, each with a basis (272 printed byline, 51 series attribution, 1 closing tagline, 1 derived) | 325 |
| In the trash (#27374, merged into #1434, which is linked to Perkins) | 1 |
| Still unlinked: this pass | 36 |
| **Total** | **362** |

The pass also took in records made since the census that print a byline: the sg110185 pieces, #31962, the Saugus High School sources and the Scott Newhall oral history. That is **21 more**, for **57 rows**.

## Counts by outcome

| Outcome | From the census | Made since | All |
|---|---|---|---|
| **Would link** (a person record exists and the page prints the name) | 1 | 0 | **1** |
| No record for the name printed | 27 | 19 | 46 |
| Pen name | 1 | 0 | 1 |
| Held for Nathan | 5 | 2 | 7 |
| No byline after all | 1 | 0 | 1 |
| A credit, not authorship | 1 | 0 | 1 |
| **Rows** | **36** | **21** | **57** |

The sweep found one more unauthored record with a byline-shaped line: #15164, "Signal Coin Columnist a Triple Winner", "By Signal Staff". That is an institutional byline, so it goes to `publishedBy` and never to a person. Its page (`/scvhistory/signal/coins/sg082606-awards.htm`) is not on Reggie.

By basis, across the 47 rows that print a person's name (the 46 with no record and the one would-link): 41 printed bylines and 5 closing taglines. The pen name, Buddy T., is also a printed byline.

## The would-link list

| Record | Person | Basis | Note (as it would be written) |
|---|---|---|---|
| #12135 Mentryville (collection) | Leon Worden #279 | printed-byline | Printed byline "By LEON WORDEN" on the book page this collection stands for ("California's Pioneer Oil Town", first printing March 1996, revised July 1997), followed by "With editorial assistance from Ruth Waldo Newhall and research assistance from Paul R. Higgins". Read from the original page. |

One caveat. The collection's legacy page is the book itself, but its body describes it as a hub ("The archive holds 44 pages on the town"). Worden wrote the book. Whether the collection record takes the book's author is the same question as any collection's author.

Nothing else links. None of the 46 names with no record has gained a record since 5 October. Each was checked by name and surname against all 254 persons: the Hernandez, Allen, Hanson, Smith and Miller surnames match other people, none of them the writer.

## Names with no record (46 records)

Each row's basis and note are already in the script, so a link can carry its basis the day a record exists. Grouped by name:

| Name as printed | Records | Basis | Notes |
|---|---|---|---|
| Jason Smisko ("By JASON SMISKO", Senior Planner, City of Santa Clarita) | 3: #12647, #12625, #12615 | printed byline | #12615 has two authors: "By JASON SMISKO And JASON MIKAELIAN, Planners, City Of Santa Clarita." The census missed Mikaelian |
| Chris Price ("By CHRIS PRICE", Assistant City Engineer) | 3: #12591, #12587, #12583 | printed byline | |
| Pat Saletore ("By PAT SALETORE", Executive Director, SCV Historical Society) | 2: #12649, #12623 | printed byline | |
| Alex Hernandez ("By ALEX HERNANDEZ", Administrative Analyst, City) | 2: #12637, #12633 | printed byline | |
| Laurel Suomisto | 2: #28310 ("By Laurel Suomisto"), #32719 ("By Laurel Suomisto.") | printed byline | was counted twice on 5 October through #28293, which is not hers (below) |
| Stephen K. Peeples | 2: #28049 obituary ("By Stephen K. Peeples, SCVNews.com \| Monday, January 6, 2020."), #31314 ("By Stephen K. Peeples.") | printed byline | #28049 also holds a second piece, "Eulogy. By Kenneth R. Pulskamp.", and a closing prayer |
| Wayne G. Sayles | #15406 | printed byline | guest commentary, closing bio line |
| Paul Brotzman, Phil Lantis, Andree Walper, Michael Fleming (City staff, Gazette) | one each: #12635, #12631, #12627, #12611 | printed byline | |
| Shelby Jacobs ("By Shelby Jacobs, 1953 Class President, Hart High School.") | #12617 | printed byline | |
| Dr. Alan Pollack ("By DR. ALAN POLLACK", President, SCV Historical Society) | #12597 | printed byline | photo-key donor (AL prefix) |
| Vonnie Wang, James L. Miller, Rich Boerner, Christina Hanson | one each: #12868, #12862, #12860, #12858 | **closing tagline** | signed at the end in capitals; no byline at the head (see "Read from a description") |
| Sarah Donner ("By Sarah Donner, Signal Staff Writer") | #28053 obituary | printed byline | |
| Patricia Farrell Aidem ("By PATRICIA / FARRELL AIDEM, Staff Writer. / L.A. Daily News,") | #28051 obituary | printed byline | |
| Perry Smith ("By Perry Smith, AM-1220 KHTS \| Tuesday, August 12, 2014") | #28305 | printed byline | |
| Karina Lutz ("City Backers Join Prison Furor. By Karina Lutz.") | #28295 | printed byline | the Lutz or Kay conflict resolves to Lutz on the page; "By Lauren Kay." heads #32715 |
| Lauren Kay, Joseph Kehoe, Simon-Jacques Ifergan, Thomas Omestad | one each: #32715, #32717, #32724, #32726 | printed byline | sg110185 pieces, made 7 October |
| Richard A. Patterson ("By Richard A. Patterson, President, SCV Facilities Foundation") | #31962 | printed byline | split from #12144 on 7 October |
| Jim Holt, Emily Alvarenga, Tammy Murga (The Signal) | one each: #31308, #31326, #31332 | printed byline | Saugus High sources |
| Los Angeles Times staff: Marisa Gerber, James Queally, Hannah Fry, Sarah Parvini, Colleen Shalby, Alejandra Reyes-Velarde, Leila Miller, Soumya Karlamangla, Richard Winton, Brittny Mejia, Ruben Vives, Sandy Banks, Laura Newberry | 8 records: #31310, #31316, #31318, #31320, #31322, #31324, #31328, #31330 | printed byline | six of the eight have two to four authors each |
| Mike Kuhlman (Deputy Superintendent, Hart District) | #31334 | **closing tagline** | "Sincerely, Mike Kuhlman, Deputy Superintendent"; opens "This is Mike Kuhlman" |

**For Nathan, on the census rule** (5 October, not yet approved): rule (b), "two or more pieces", now covers Smisko, Price, Saletore, Hernandez, Suomisto and Peeples. It would also cover eight Los Angeles Times reporters, each on two or three pieces about a single event: Banks, Gerber, Reyes-Velarde, Shalby, Fry, Leila Miller, Winton and Mejia. That sits poorly with AGENTS.md ("a meaningful, recurring role in SCV history"). Rule (c), "a role of their own", covers Pollack, Saletore, Peeples and Perry Smith. Nothing was created.

## Held, and why

| Record | What the page shows | Why held |
|---|---|---|
| #2173 Notes | Only the series heading "HISTORY OF THE SANTA CLARITA VALLEY BY JERRY REYNOLDS"; the notes speak of Reynolds in the third person ("Reynolds used "The Camulos Story" by Wally Smith (1958) as the basis for chapters 14 and 15.") | An editor's notes under Reynolds's heading. Series attribution to Reynolds, the editor, or nobody: Nathan's call |
| #12852 Tributes | An index: each tribute's title with its byline line, ending "Signal Editorial, April 29, 1997"; no tribute text (body 1,149 characters) | Recommend **no author**: an index, not a compilation, so the double-count worry falls away |
| #12534 The Paul Allen LDS Letter | Two pieces: Leon Worden's note, signed "Sincerely, LEON WORDEN, Opinion and Multimedia Editor", then the letter, signed "Paul Allen / Santa Clarita, Calif." | One record holding two authors' pieces, and the letters question. Allen has no record |
| #28057 John Lang's letter (1875) | Opens "EDITOR HERALD:" and is signed "JOHN LANG. Lang's Station, July 17th." | Letters question (TODO). If a letter's writer counts, the basis is a closing tagline to Lang #18820. Craft's body ends before the signature |
| #26983 Abel Stearns (1885) | The Santa Cruz Sentinel's unsigned 1885 article "Bogus History", which reprints Stearns's letter of "Los Angeles, July 8th, 1867" (signed "Abel Stearns.") and a second letter signed "A Robinson." | Stearns wrote a letter inside the record, not the record. Even a yes on letters would not make him its author |
| #31306 Muehlberger family letter | The page's note: "The following letter was written by Gracie Anne Muehlberger's father, Bryan Muehlberger" | Letters question; named by a note, not a byline; no record, and none proposed |
| #31723 Scott Newhall oral history | "Scott Newhall." over the title, then "Interviewer: Suzanne B. Riess." and "The Bancroft Library" | Does a narrator count as the author? Scott Newhall has a record (#31431); Riess has none |

**A credit, not authorship:** #671 Newsmaker of the Week, "Host: Leon Worden".

**No byline after all:** #28293, "Cityhood forum announced". The page prints the piece as "[Brief.]" under "The Newhall Signal and Saugus Enterprise | Sunday, January 11, 1987." with no name. "By Laurel Suomisto" below it belongs to the next piece (#28310).

## Read from a description

These were stated as fact in the 5 October census, or in the records, on the strength of something other than the page itself. None was fixed.

1. **The four Rioux tributes (#12868, #12862, #12860, #12858).** The census and the 5 October dry run give each a printed byline at the head: "Tribute to Richard 'Doc' Rioux · Vonnie Wang" and so on. Those words are the page's HTML `<title>`, which the webmaster wrote. The visible page has no byline at the head, only the writer's signature in capitals at the end. The basis is a closing tagline, not a printed byline.
2. **#12852 Tributes.** The census calls it a compilation that "reprints ten tributes, each also its own record". The page and the record are an index of titles and bylines with no tribute text. The census read the byline lines and not what sat under them.
3. **#28293 Cityhood forum announced.** The census and the 5 October documents table give it "By Laurel Suomisto". The page prints it unsigned. The byline was taken from the next piece on a two-piece page, so Suomisto's count was one too high.
4. **#28057 John Lang.** The census says the author is "identified in the webmaster note, not a byline line". That was read from the note above the letter. The letter itself is signed "JOHN LANG." The record's body has also lost that signature line.
5. **#26983 Abel Stearns.** The census calls the record "his letter; author identified by title". That was read from the title. The record is an 1885 newspaper article that contains his 1867 letter and another man's.
6. **#12135 Mentryville.** The collection's body says the book was "published by the Santa Clarita Valley Historical Society". The book page prints "Published by The Friends of Mentryville in cooperation with the Santa Monica Mountains Conservancy and the City of Santa Clarita". Where the body's sentence came from was not traced.
7. **TODO.md, line 63** says #28295 "stays a document until the type ruling". Craft holds it in articles, in collection #32711.

## What the model would need (for Nathan; not designed here)

- **Names that stay text.** There is still no field for a byline name that has no record. The 5 October census proposed `bylineText`. Until a field like that exists, 46 records can say only what sourceLine happens to hold.
- **Several authors on one record.** `authorshipBasis` is one value per record, and the 7 October script refuses a record with two authors on different grounds. Seven rows here have two to four authors on the same ground, which the field can hold. A mixed case, a record carrying a second piece by another hand (#28049's eulogy, #12534's note and letter), cannot be expressed.
- **Letters, oral histories and hosts.** Writer, narrator, interviewer and host are not the same as "written by". The letters question in TODO decides four of the seven held rows.

## Files

- `scripts/import/bylines_remaining_2026_10_08.php`: the dry run, every row with its quotes and note.
- Working files, not for commit: `storage/runtime/bylines8_dump.php`, `bylines8-dump.json`, `bylines8_pages.php`, `bylines8-pages.json`, `bylines8-context.txt`, `bylines8_raw.php`, `bylines8_trash.php`.
