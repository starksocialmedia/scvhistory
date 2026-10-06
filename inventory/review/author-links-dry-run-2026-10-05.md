# Author links, dry run, 5 October 2026

Claude. Dry run only: nothing was written. Script `scripts/import/link_authors_2026_10_05.php` (`$APPLY = false`), data `inventory/review/author-links-2026-10-05.json`, source `authorship-census-2026-10-05.json`, checked against the database and re-read from the mirror today.

**The rule.** A piece takes a person as `writtenBy` only when its own page prints that person's name as author (or, where the page is not on the mirror, the byline block kept in its imported body does), and the person already has a record. A collection's author is never passed down; catalogue collections pass nothing. No person record is created. Documents have no `writtenBy`, so they are listed, not linked.

## The numbers

- **320 articles would take `writtenBy`**, all to existing person records. Every byline was found again on re-read, as printed (97 on the mirror page, 223 in the body's byline block because the page is not on the mirror; 219 of those are Sol Taylor's columns).
- **0 obituaries.** Carl Goldman's piece on Connie Worden Roberts (#28047) is already linked to Goldman (#30546), set earlier today. The other three bylined obituaries name people with no record (below).
- **28 refused** (articles and obituaries): 25 because the name has no record, 1 a pen name (Buddy T.), and 2 held for Nathan (#2173 Notes, #12852 compilation).
- **11 documents** with a byline or a named author, not linked: the document type has no `writtenBy`. 7 would link to an existing record once Nathan approves the field.
- **2 collections** with a printed byline, outside this step (articles and obituaries only).
- At run time the script found **0 conflicts** (no target already has a different author) and **0 already done**.

After an apply, class A in the census would rise from 419 to 740 (the 320, and Goldman's obituary linked since the census ran).

## Counts by author

| Person | Record | Pieces | Where the byline is |
|---|---|---|---|
| Sol Taylor | #2582 | 219 | body 219; Making Cents 219 |
| Jerry Reynolds | #281 | 52 | page 52; History of the Santa Clarita Valley 52 |
| Leon Worden | #279 | 32 | page 28, body 4; Old Town Newhall Gazette 10, Selections from Leon Worden 22 |
| Arthur Buckingham Perkins | #333 | 5 | page 5; Story of Our Valley 5 |
| Darryl Manzer | #2579 | 3 | page 3; Old Town Newhall Gazette 3 |
| Richard Rioux | #2585 | 2 | page 2; Richard 'Doc' Rioux At Large 2 |
| Philip Ellis Jr. | #28364 | 2 | page 2; Old Town Newhall Gazette 2 |
| Patti Rasmussen | #2591 | 2 | page 2; Old Town Newhall Gazette 2 |
| Cameron Smyth | #16380 | 1 | page 1; Old Town Newhall Gazette 1 |
| Laurene Weste | #15929 | 1 | page 1; Old Town Newhall Gazette 1 |
| Frank Ferry | #23083 | 1 | page 1; Old Town Newhall Gazette 1 |
| **Total** | | **320** | |

Bylines as printed: Taylor "By Dr. Sol Taylor" (all 219); Reynolds the page header "HISTORY OF THE SANTA CLARITA VALLEY BY JERRY REYNOLDS" (51) and "By Jerry Reynolds." (#2181); Worden "By Leon Worden" / "By LEON WORDEN", "Stage Review by LEON WORDEN" (#12204, #12188), "Leon Worden · August 20, 1997" (#12206) and the tagline "Leon Worden is a Santa Clarita resident. His commentary appears on Wednesdays." (#12200); Perkins "By A.B. Perkins" / "By Arthur B. Perkins."; the Gazette bylines in capitals ("By DARRYL MANZER" and so on). Every row, with its page path, is in the JSON and in the script's dry-run output.

### Notes on particular links

- #12645 Redevelopment Committee At Work for Newhall.: Byline prints "PHILIP ELLIS, Chairman, Newhall Redevelopment Committee"; the sources join that office to #28364 (SCVNews.com, 1 March 2012 and 5 February 2016: Phil Ellis of the Newhall School Board chaired the committee). The byline omits "Jr."; no other Ellis has a record.
- #12613 An Update On Redevelopment.: Byline prints "PHILIP ELLIS, Chairman, Newhall Redevelopment Committee"; the sources join that office to #28364 (SCVNews.com, 1 March 2012 and 5 February 2016: Phil Ellis of the Newhall School Board chaired the committee). The byline omits "Jr."; no other Ellis has a record.
- #2181 About the Namesakes of the Kingsburry House: The page opens with a webmaster's note (Leon Worden's) before Reynolds's bylined article; the link is for the article.
- #2171 BIBLIOGRAPHY: Reynolds's bibliography, headed AUTHOR'S RESOURCES.
- #2169 EPILOGUE: Epilogue in the author's voice ("this writer").
- #1442 Picture Story of Hart High School (and District): The booklet names Superintendent Irvin A. Shimmin as General Editor; the piece itself is bylined to Perkins.

## The four likely duplicate pairs

Each record is linked on its own byline. Both halves of every pair print Leon Worden; the pairs are listed for Nathan to decide whether to merge.

| Record | Byline as printed | Read from | Pair |
|---|---|---|---|
| #12206 Don't let sign ordinance catch you off-guard | Leon Worden · August 20, 1997 | body byline block (legacy page /scvhistory/signal/worden/lw082097.htm not on the mirror) | #12200 |
| #12200 Don't let sign ordinance catch you off-guard | Leon Worden is a Santa Clarita resident. His commentary appears on Wednesdays. | page /scvhistory/signal/worden/old/lw082097.htm | #12206 |
| #12204 You'll die laughing at CTG's 'Drop Dead' | Stage Review by LEON WORDEN | body byline block (legacy page /scvhistory/signal/worden/lw080798eb.htm not on the mirror) | #12188 |
| #12188 You'll die laughing at CTG's 'Drop Dead' | Stage Review by LEON WORDEN | page /scvhistory/signal/worden/old/lw080798eb.htm | #12204 |
| #12280 Car guys needn't fear Newhall revitalization | By Leon Worden | body byline block (legacy page /scvhistory/signal/worden/lw110597.htm not on the mirror) | #12258 |
| #12258 Car guys needn't fear Newhall revitalization | By Leon Worden | page /scvhistory/signal/worden/old/lw110597.htm | #12280 |
| #12208 The Garden of Eden that might have been | By Leon Worden | body byline block (legacy page /scvhistory/signal/worden/old/lw071598.htm not on the mirror) | #12152 |
| #12152 Piru: The Garden of Eden that might have been | By Leon Worden | page /scvhistory/signal/worden/lw071598.htm | #12208 |

## Refused, and why

| Record | Section | Byline as printed | Reason |
|---|---|---|---|
| #15406 'Cultural Property': At Odds with Globalism | articles | By Wayne G. Sayles | No person record for Wayne G. Sayles (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12868 May Peace Be With You | articles | Tribute to Richard 'Doc' Rioux · Vonnie Wang | No person record for Vonnie Wang (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12864 Giant Loss to the Recovery Community | articles | By Buddy T. @ The Mining Company | Pen name ("Buddy T. @ The Mining Company"). A pen name never becomes a person record; stays text. |
| #12862 Richard provided friendship and comfort | articles | Tribute to Richard 'Doc' Rioux · James L. Miller | No person record for James L. Miller (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12860 Richard is still working on Earth | articles | Tribute to Richard 'Doc' Rioux \| Rich Boerner | No person record for Rich Boerner (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12858 Touched by the Web site | articles | Tribute to Richard 'Doc' Rioux · Christina Hanson | No person record for Christina Hanson (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12852 Tributes | articles | By Congressman Howard P. "Buck" McKeon (and nine more bylines on the page) | Compilation of ten tributes, each also its own record. Linking its writers here would double their counts. Of its eight named writers three have records (McKeon #18791, Worden #279, Darcy #16140), four have none and one is a pen name (Buddy T.). Link the separate tribute records, not this page. Not linked. |
| #12649 Where Do We Get the Name, Newhall? | articles | By PAT SALETORE | No person record for Pat Saletore (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12647 Shaping Newhall Inside and Out. | articles | By JASON SMISKO | No person record for Jason Smisko (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12637 Business Spotlight: Planet Soccer. | articles | By ALEX HERNANDEZ | No person record for Alex Hernandez (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12635 Newhall: A Gem In The Making. | articles | By PAUL BROTZMAN | No person record for Paul Brotzman (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12633 Gate-King: Downtown Neighbor A Part Of Revitalization Cast. | articles | By ALEX HERNANDEZ | No person record for Alex Hernandez (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12631 Arts Play A Vital Role In Old Town. | articles | By PHIL LANTIS | No person record for Phil Lantis (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12627 Events: Everything Fun Happens In Newhall. | articles | By ANDREE WALPER | No person record for Andree Walper (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12625 Master Plan For Master's College. | articles | By JASON SMISKO | No person record for Jason Smisko (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12623 History: The Finest Hotel South Of San Francisco. | articles | By PAT SALETORE | No person record for Pat Saletore (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12617 Life Lessons From The 'Good Old Days.' | articles | By Shelby Jacobs, 1953 Class President, Hart High School. | No person record for Shelby Jacobs (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12615 A 'North Newhall' Plan. | articles | By JASON SMISKO | No person record for Jason Smisko (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12611 Cowboy Festival Gallops Into Newhall. | articles | By MICHAEL FLEMING | No person record for Michael Fleming (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12597 Mulholland: "There It Is, Take It." | articles | By DR. ALAN POLLACK | No person record for Alan Pollack (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12591 Newhall Moving Forward. | articles | By CHRIS PRICE | No person record for Chris Price (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12587 Coming Soon: A New Library For Newhall. | articles | By CHRIS PRICE | No person record for Chris Price (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12583 Downtown Projects Moving Forward. | articles | By CHRIS PRICE | No person record for Chris Price (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #12534 The Paul Allen LDS Letter | articles | Paul Allen / Santa Clarita, Calif. (signature) | No person record for Paul Allen (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #2173 Notes | articles | HISTORY OF THE SANTA CLARITA VALLEY BY JERRY REYNOLDS | Reynolds's Notes: the page header names Reynolds, but the notes are an editor's ("Reynolds wrote that..."), probably Leon Worden's, as Perkins's Editor's Notes #1432 are linked to Worden. Nathan to decide; not linked to either. |
| #28053 Gary Murr, Saugus School Board President | obituaries | By Sarah Donner / Signal Staff Writer | No person record for Sarah Donner (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #28051 Ruth Newhall dies at 93 | obituaries | By PATRICIA / FARRELL AIDEM, Staff Writer. / L.A. Daily News, | No person record for Patricia Farrell Aidem (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |
| #28049 George A. Caravalho, Santa Clarita's First Permanent City Manager, 1938-2020 | obituaries | By Stephen K. Peeples, SCVNews.com \| Monday, January 6, 2020. | No person record for Stephen K. Peeples (checked by name and surname against all persons today). Stays text until Nathan approves a record (census rule). |

Names with no record were checked against every person record by full name and surname today. Hanson, Allen, Hernandez and Smith surnames match other people (Thomas Hanson; Don, Sheldon and Chester Allen; Robert and Leticia Hernandez; Ernesto and Christy Smith), none of them the writer, so none is linked. Under the census rule, Smisko, Price, Saletore and Hernandez (two or more pieces each) and Pollack, Peeples and Perry Smith (their own role) qualify for a record; Nathan approves any record before it is made.

## Documents that would link once the field exists

The document entry type has no `writtenBy`. Adding it is a schema change for Nathan. Nothing here is linked.

| Record | Byline or naming | Person | Note |
|---|---|---|---|
| #28310 Cityhood Backers: Who Are They? (Laurel Suomisto, The Signal, January 4, 1987) | By Laurel Suomisto | no record | sourceLine holds the newspaper |
| #28305 Connie Worden Roberts, City Co-Founder (Perry Smith, KHTS, August 12, 2014) | By Perry Smith, AM-1220 KHTS \| Tuesday, August 12, 2014 | no record | byline is in sourceLine |
| #28295 City Backers Join Prison Furor (Karina Lutz, The Signal, November 1, 1985) | By Lauren Kay. | none: hold | CONFLICT: the record title names Karina Lutz, the page prints "By Lauren Kay." Neither has a record. Check the clipping; do not link. |
| #28293 Cityhood forum announced, The Signal, January 11, 1987 | By Laurel Suomisto | no record | sourceLine holds the newspaper |
| #28287 City Formation Committee Member Connie Worden-Roberts Remembers, 2007 | Connie Worden-Roberts Remembers ... | Connie Worden #16418 | sourceLine; her memoir piece |
| #28285 "A Look Back in Time": proposal for the Historical Society's first annual fund raiser, by Connie Worden-Roberts, 2003 | Respectfully submitted, Connie Worden-Roberts Member | Connie Worden #16418 | sourceLine |
| #28281 A Brief History of the Push for Self-Government in Santa Clarita | By Connie Worden-Roberts. | Connie Worden #16418 | sourceLine and page |
| #28057 John Lang's Letter on the Grizzly Bear, Los Angeles Herald, July 28, 1875 | Now comes a more sober version of the story as written by John Lang himself, in the form o | John Lang #18820 | letter to the editor; author identified in the webmaster note, not a byline line; "News story courtesy of Tricia Lemon Putnam." is the provider. Weaker than a byline: the author is named by the webmaster's note or the title. Nathan to say whether a letter's writer counts as its author here. |
| #27374 Rancho San Francisco: A Study of a California Land Grant, by A.B. Perkins (1957) | A.B. Perkins, The Historical Society of Southern California Quarterly, June 1957 | Arthur Buckingham Perkins #333 | sourceLine; page also prints "By Arthur B. Perkins" |
| #26983 Abel Stearns Tells of Lopez 1842 Gold Discovery; No Mention of Dream | Abel Stearns Tells of Lopez 1842 Gold Discovery; No Mention of Dream. / Correspondence of  | Abel Stearns #309 | his letter; author identified by title, not a byline line. Weaker than a byline: the author is named by the webmaster's note or the title. Nathan to say whether a letter's writer counts as its author here. |
| #20104 Demetrius Scofield's Eulogy to Charles Alexander Mentry | By DEMETRIUS G. SCOFIELD | Demetrius G. Scofield #21584 | sourceLine and page |

Of the 11: 5 print a byline naming an existing record (Connie Worden #16418 three times, Perkins #333, Scofield #21584); 2 name the writer of a letter by note or title (John Lang #18820, Abel Stearns #309); 3 name people with no record (Laurel Suomisto twice, Perry Smith); 1 is the Lutz or Kay conflict.

## Collections, outside this step

- #671 Newsmaker of the Week: "Host: Leon Worden" (person #279). SCVTV program; Leon is the host, not strictly the author
- #12135 Mentryville: "By LEON WORDEN" (person #279). A byline on the collection page; collections carry writtenBy, so this needs no schema change, but it is outside the articles and obituaries scope.

#671 credits a host, not an author: not a writtenBy candidate as it stands.

## Running it

**MacBook**

```
ddev craft exec "eval(file_get_contents('scripts/import/link_authors_2026_10_05.php'))"
```

Dry run as committed. The apply flips `$APPLY` locally; a second run sets nothing. Afterwards: re-run the authorship census (expected A 740, Sol Taylor's page "Wrote 220 pieces", Reynolds's History 79 of 80 chapters linked, the Notes held) and check a person page renders.
