# Byline census rule (b), and the names with no record, 9 October 2026

Claude, for Nathan. Overnight brief, items 9 and 10. Write-up only: no database writes, no records created, nothing committed.

## What was read

- Rules: AGENTS.md, PHILOSOPHY.md section 2.3, DATA-ORGANIZATION.md, CONTENT-MODEL.md, docs/PROFILES.md, docs/DATA-MODEL.md ("Who gets a record", "Who gets a person record"), TODO.md, CHANGELOG.md, ERRORLOG.md, HANDOFF.md, and the review files that name the census rule (grep for "census rule", "two or more pieces", "role of their own", "own role" across inventory/, docs/, scripts/ and the root documents). Dates come from the rule's own text or, where it carries none, from `git log -S` on the sentence.
- Craft, read only, through `storage/runtime/scratch/bylnames.php`, `bylctx.php`, `bylctx2.php`, `bylctx3.php` (each calls `_reads.php` first): every entry's title and every field value (`elements_sites.content`), all statuses, not trashed, no drafts or revisions, searched for each name and, where the surname is distinctive, the surname. Each hit outside the byline records was read in context. Raw results: `storage/runtime/scratch/bylnames.json`.
- The Reggie mirror inside the container (`/mnt/reggie/scvhistory.com`), with `LC_ALL=C grep -rioaF` (never the shell's grep), limited to `.htm`, `.html`, `.shtml` and `.txt`: one pass for every name (`storage/runtime/scratch/bylmirror.tsv`, name and file), one pass counting pages that print "By <name>" (flipbook `basic-html` pages left out), and a context read of the hits that mattered (`bylmctx.sh`). Case-insensitive; a page counts once however often it names the person. A hit on a name is a lead until the page shows it is the same person (PROFILES, the namesake trap).

## Item 9: rule (b) against the recurring-role test

### The two rules, exactly

**Rule (b)**, in `inventory/review/authorship-census-2026-10-05.md`, section "Rule: when a byline name gets a person record, and when it stays text" (Claude's census, 5 October 2026; `git log -S` puts it in 55ae5f6, 5 October):

> A byline name gets a person record when **any one** of these holds:
>
> - (a) a record already exists. Link it, never duplicate it.
> - (b) the person wrote **two or more** separate pieces in the archive, compilations not counted. Today that is Jason Smisko, Chris Price, Pat Saletore, Alex Hernandez and Laurel Suomisto.
> - (c) the person has their own role in SCV history apart from the byline: an office holder, a subject of other records, a donor in the photo key (Dr. Alan Pollack), a local journalist of record (Carl Goldman, Stephen K. Peeples, Perry Smith).
>
> Otherwise the name stays text in `bylineText`, as printed. [...] It follows AGENTS.md: full records only for a meaningful, recurring role, and passing mentions stay in text.
>
> A pen name (Buddy T.) never gets a record. [...] Nathan confirms the (c) list before any record is created.

**The recurring-role rule**, Nathan's, in three places with the same words:

- PHILOSOPHY.md section 2.3, "Nathan's rule" (in the file since 16 September 2026, 7adcd0c): "**A full record (Person, Place, Organization, etc.) is created only when the entity has a meaningful, recurring role in SCV history.** A passing mention stays in the body text (or a tag). It does not get a record." It goes on: "Being named in an article is not enough. Over-creating stub records is a known failure mode; it has happened before and Nathan rejected it. When in doubt, list the candidate under "Open Questions" in CONTENT-MODEL.md for Nathan instead of creating it."
- AGENTS.md, Content rules (16 September 2026, 5927d7b): "Full records only for entities with a meaningful, recurring role in SCV history. Passing mentions stay in body text".
- DATA-ORGANIZATION.md, the Person row: "An individual with a meaningful, recurring role in SCV history"; not a Person: "Someone mentioned once (tag them)".

**The profile selectivity rule**, docs/PROFILES.md "Who gets a person record" (Nathan, 1 October 2026; repeated in docs/DATA-MODEL.md): "**A person record requires significance to SCV history, not appearance in a result** (Nathan, 1 October 2026, correcting his own rule of 25 September that standing more than once earned a record; it produced about 64 people whose only trace was losing elections). Repeated candidacy is persistence, not significance." And: "A person gets a record when the archive holds something about them beyond a result: an office held, an article, photograph, document, obituary or war memorial record about them, a profile with sources, or a place, organization or person that points at them."

docs/DATA-MODEL.md "Who gets a record" adds (Nathan, 3 October 2026, by its context): "A person record requires a Santa Clarita Valley connection the articles document: lived, worked, owned, built, founded, buried or acted here. The connection has to be in the text. A name appearing in an article is not a connection; it is a mention." And: "National figures and subject-matter figures a local columnist wrote about get no record. [...] The test is the subject, not the collection."

### Do the written rules resolve it?

**Yes, for the eight reporters. Rule (b) has no standing. The rules in force give the eight no record.**

1. **Rule (b) was never approved.** It is a proposal in Claude's census, and the census says so: "Nathan approves any record before it is made" (author-links-dry-run-2026-10-05.md), "Nathan confirms the (c) list". No CHANGELOG entry, TODO line or ruling adopts it. TODO.md line 128 still lists the census as waiting on Nathan, and the 8 October bylines report calls it "5 October, not yet approved". A proposal does not override Nathan's rule, whatever its date.
2. **The rule in force is the recurring-role rule.** It is Nathan's (PHILOSOPHY 2.3, AGENTS.md, DATA-ORGANIZATION). The test is a meaningful, recurring role *in SCV history*, not recurring appearance *in the archive*. PROFILES and DATA-MODEL (1 and 3 October) say the same thing in their own terms: significance, not appearance; a connection the text documents; "a name appearing in an article is not a connection". The eight reporters (Sandy Banks, Marisa Gerber, Alejandra Reyes-Velarde, Colleen Shalby, Hannah Fry, Leila Miller, Richard Winton, Brittny Mejia) are on two or three Los Angeles Times pieces each, all about the Saugus High School shooting, 15 to 18 November 2019 (#31310, #31316, #31318, #31320, #31322, #31324, #31328, #31330). Nothing in Craft or on the mirror is about any of them, and nothing places any of them in the valley except to report that one event (the Craft and mirror counts are below). Under the rules in force they stay text.
3. **Rule (b) contradicts itself on this case.** It claims "It follows AGENTS.md: full records only for a meaningful, recurring role". Applied to the eight, it would not. The clause it rests on governs.
4. **The nearest written precedent points the same way.** On 1 October Nathan withdrew his own count rule of 25 September ("standing more than once earned a record") because counting appearances produced records with no significance behind them. That rule was about election results, not bylines, so it is a precedent, not the rule that decides this. It is the same mistake: a count standing in for significance.

### What the written rules leave open (for Nathan)

The rules settle the eight. They do not settle three things, and a rule should not be invented for them:

1. **Whether rule (b) is withdrawn, or kept as a lead.** Once the recurring-role test governs, rule (b) can at most flag a name for review. Nathan's word would close TODO line 128's census rule either way.
2. **Whether writing about the valley, often, is itself a "recurring role in SCV history".** This is the open question behind rule (c)'s "local journalist of record" (Carl Goldman, Stephen K. Peeples, Perry Smith). No written rule says a local reporter's or a City staffer's body of work counts, or that it does not. CONTENT-MODEL.md's settled list (Nathan, 15 and 16 September) says "Person is notable figures and authors only". That line could be read as "authors get records". It is a limit, not a grant ("only"), and the later rules (PROFILES, DATA-MODEL) speak of significance, not authorship. Nine of the 44 names below turn on this one answer.
3. **What a name that stays text is held in.** There is still no `bylineText` field (proposed 5 October, TODO line 128). This is a schema decision, not a rule question, and it is listed here only because "stays text" has nowhere to go yet.

## Item 10: the names with no record

The bylines report's "46" is records. They carry **44 names**: 22 from the census set (Jason Mikaelian among them, the co-author on #12615 the census missed) and 22 from records made since. Suomisto and Peeples appear in both sets and are counted once. One more name sits on these records with a record of its own. Kenneth R. Pulskamp (#29599) signs the eulogy inside #28049 ("Eulogy. By Kenneth R. Pulskamp."). He needs no decision under this rule. His piece is the mixed-authorship case the bylines report already lists.

Columns: **Craft** is the number of entries that carry the name anywhere, the byline records included, with what the others are. **Mirror** is the pages naming the person; "bylined" is the pages printing "By <name>", where that was counted. **Verdict** applies the recurring-role test and PROFILES ("something about them beyond" the byline).

**Count: 3 warrant a record, 9 unclear (all on the open question in item 9, point 2), 32 do not. 44 names.**

### Warrants a record (3)

| Name | Byline records | Craft | Mirror | Reason |
|---|---|---|---|---|
| Dr. Alan Pollack | #12597 | 55 entries with the full name: 31 photographs, persons #297, #315 and #327 citing his articles, organization #378 (SCVHistory.com: "he and Alan Pollack are its webmasters"), place #926, group #946, article #2081, photograph #2721 ("President Alan Pollack recognized members...") | 428 pages; 103 print "By Alan Pollack" and 48 "By Dr. Alan Pollack"; 45 files named pollack* | Historical Society president, co-webmaster of the archive, a photo-key collection, and a body of local history the archive's own profiles cite: a recurring role many times over. |
| Pat Saletore | #12649, #12623 | 10: the two bylines; photograph #2743 (SCVHS director, the Saugus School bell); board minutes #28283; event #31893 citing her; Leon's columns #12194 ("Director Pat Saletore has done a terrific job") and #12422 (docent leader); #12593 (her daughter's profile names her); #2171 bibliography; organization #16347 cites #12593 | 44 pages, 6 bylined; Historical Society minutes 2000 and 2010 to 2017; factfiction.htm ("local historian Pat Saletore came upon a World War I registration card") | Executive director of the Historical Society, docent leader and researcher, in four kinds of record: a role of her own, not only bylines. |
| Shelby Jacobs | #12617 | 1 (the byline) | 9 pages: hb1902.htm, whose title is "Columbia Memorial Space Center Features Shelby Jacobs, Hart Class of 1953"; the 2009 "Legacy: Shelby Jacobs, John Reid" video on Val Verde, listed on people.htm, valverde.htm, canty-pioneers-list.htm and sg022101b.htm | The mirror holds a page about him (hb1902) and lists him as a Val Verde subject, which meets PROFILES ("an article [...] about them"). Craft does not yet hold hb1902: import it first (entity-first). The page title was read, not the whole page. |

### Unclear: turns on whether writing or City work about the valley is a role (9)

| Name | Byline records | Craft | Mirror | Reason |
|---|---|---|---|---|
| Stephen K. Peeples | #28049, #31314 | 5: the two bylines; contributor line on #31308; cited as a source by person #16396 (Caravalho) and organization #29870 | 21 pages, 18 bylined (obituaries, Signal 2005, SCVNews), SCVTV photo credits | SCVTV/SCVNews editor-reporter with a large body of local work. Census rule (c) named him a "journalist of record". No written rule says that is a role. |
| Perry Smith | #28305 | 18: the byline; cited as the source on 13 office-holding records, 3 person records and event #31359 | 17 pages, 14 bylined (KHTS, The Signal, obituaries) | The archive's office-holding records lean on his reporting. Being cited is not being the subject. Same open question. |
| Laurel Suomisto | #28310, #32719 | 3: the two bylines; person #29458 (Cathie Wright) cites her | 6 pages, all bylined: 4 more Signal pieces from November and December 1984 (McKibben, Warren Wilson, Sloan, Jauregui) not in Craft | A Signal reporter on the cityhood and prison story. Six pieces, nothing about her. Same question. |
| Patricia Farrell Aidem | #28051 | 4: the byline; persons #15477 (Ruth Newhall) and #31431 (Scott Newhall) cite her; #31308 quotes a "Patricia Aidem, public relations director for Providence Holy Cross" | 7 pages, 5 bylined (Daily News 1996, 2003, gt8703) | A Daily News reporter. The Providence Holy Cross Aidem may or may not be the same person (namesake trap, not resolved). Nothing about her. Same question. |
| Tammy Murga | #31332 | 5: the byline; contributor on #31308; cited by event #31338, person #28316 and office holding #31719 (BJ Atkins) | 3 pages, 2 bylined (one more, scvhs20200228, not in Craft) | A Signal reporter, cited as a source. Same question; thinner than the four above. |
| Jason Smisko | #12647, #12625, #12615 | 4: the three bylines; #12462 quotes "spokesman Jason Smisko" | 11 pages: 3 bylined; Signal news 2005 to 2006 quoting "Senior Planner Jason Smisko"; the City's Old Town Newhall specific plan (2005); Leon's 2002 column | City senior planner and spokesman on Newhall redevelopment, quoted in the news of it. Is a City staffer's recurring work a role in SCV history? |
| Chris Price | #12591, #12587, #12583 | 3 (the bylines) | 4 pages: 3 bylined; Newsmaker of the Week episode 194, "Chris Price, Newhall Redevelopment" | Assistant City Engineer on the same project. Thinner than Smisko. Same question. |
| Paul Brotzman | #12635 | 4: the byline; #12633 on his entertainment-industry plans as director of community development; photographs #3765 and #3767 (as West Hollywood's City Manager, guiding the Hart Park work, then joining Santa Clarita in 2005) | 31 pages, 1 bylined: the Hart Park photo set lw2276a to lw2276u and al2276 (one caption repeated), three Newsmaker episodes (220, 251, 293), Signal news 2005 | The nearest to a warrant among City staff: he is named in records about Hart Park and Newhall redevelopment, not only on them. He holds no office in the archive's sense. |
| Mike Kuhlman | #31334 (closing tagline) | 2: the message; event #31338 cites it | 6 pages: timeline.htm records his "official first day as Superintendent, William S. Hart Union High School District" (the year was not read); a Canyon High principal quote; two program PDFs | The Hart District superintendent, a hired post, not an elected one. PROFILES lists office held, and a superintendent is not a trustee. Nathan's call. |

### Does not warrant a record (32)

| Name | Byline records | Craft | Mirror | Reason |
|---|---|---|---|---|
| Wayne G. Sayles | #15406 | 2 more: #15402 quotes "Wayne Sayles, executive director of the Ancient Coin Collectors Guild"; #4435's "Sayles" is a "Chic' Sayles" outhouse | 3 pages (the piece, Leon's column index) | A national coin-trade figure from Gainesville, Missouri: the DATA-MODEL numismatists rule. |
| Vonnie Wang | #12868 | 2 (the tribute and the #12852 index) | 2 | One tribute. |
| James L. Miller | #12862 | 5: the other three are the "James L. Miller Memorial Award" (Leon's #279, NLG results #15446 and #15448), which is a namesake, not him | 5 (the same) | One tribute. |
| Rich Boerner | #12860 | 2 (the tribute, the index) | 2 | One tribute. |
| Christina Hanson | #12858 | 2 (the tribute, the index) | 2 | One tribute. |
| Jason Mikaelian | #12615 (co-author) | 1 | 1 | One co-byline. |
| Alex Hernandez | #12637, #12633 | 2 (the bylines) | 2 (the same) | City analyst, two pieces in one Gazette issue, nothing else. Rule (b)'s only reach. |
| Phil Lantis | #12631 | 1 | 8 pages: City arts staff, "TAC founding member", the Arts Master Plan | City arts supervisor, named in passing. Nothing about him. |
| Andree Walper | #12627 | 1 | 8 pages: Newsmaker episode 307, a donor list ("Andree & Carl Walper"), Signal 2001 | City staff, passing mentions. |
| Michael Fleming | #12611 | 1 (the six other "Fleming" hits are other people: Rhonda, Don and Cheri, John L.) | 2 for "Michael"; 5 for "Mike Fleming" (Newsmaker episode 268, donor list, OTN 2001), not confirmed as the same man | Cowboy Festival manager, one piece. |
| Sarah Donner | #28053 | 4: the obituary, cited by person #25399 and office holdings #28477 and #29190 (all Gary Murr) | 1 | One obituary, cited for the obituary. |
| Karina Lutz | #28295 | 4: the piece; cited by persons #16418 and #29458 and event #31359 | 3 | One 1985 piece. |
| Lauren Kay | #32715 | 1 | 2 | One 1985 piece. |
| Joseph Kehoe | #32717 | 2 (cited by #29458) | 3 (one is the piece; the other two are a 1976 report and a federal preservation brief, not confirmed as him) | One 1985 piece. |
| Simon-Jacques Ifergan | #32724 | 1 | 2 | One 1985 piece. |
| Thomas Omestad | #32726 | 2 (cited by #29458) | 1 | One 1985 LA Times piece. |
| Richard A. Patterson | #31962 | 2: also listed among the SCV Facilities Foundation directors who signed #12156 | 7 pages: the Foundation's 2014 report (bylined), Leon's 2005 columns; "Rick Patterson", an attorney in a 2003 judicial race (sg101303), is not confirmed as him | President of an advocacy foundation, one piece of his own. Nothing about him. |
| Jim Holt | #31308 | 3: cited by person #28336 and event #31338 | 1 | One piece, cited for it. |
| Emily Alvarenga | #31326 | 2 (cited by #31338) | 1 | One piece. |
| Marisa Gerber (LAT) | #31310, #31330 | 2 (the "Gerber" photographs are other people) | 2 | The Saugus shooting only. One of the eight. |
| James Queally (LAT) | #31310 | 2 (contributor line on #31320) | 2 | The Saugus shooting only. |
| Hannah Fry (LAT) | #31310, #31320 | 5 (plus #31316 and #31318 contributor lines, and #31338 cites her) | 2 | The Saugus shooting only. One of the eight. |
| Sarah Parvini (LAT) | #31310 | 2 (contributor on #31320) | 2 | The Saugus shooting only. |
| Colleen Shalby (LAT) | #31316, #31318 | 4 (#31310 contributor, #31338 cites) | 2 | The Saugus shooting only. One of the eight. |
| Alejandra Reyes-Velarde (LAT) | #31316, #31318, #31322 | 6 | 2 | The Saugus shooting only. One of the eight. |
| Leila Miller (LAT) | #31316, #31320 | 3 | 2 | The Saugus shooting only. One of the eight. |
| Soumya Karlamangla (LAT) | #31316 | 2 | 1 | The Saugus shooting only. |
| Richard Winton (LAT) | #31320, #31322 | 3 (the 14 other "Winton" hits are other people) | 2 | The Saugus shooting only. One of the eight. |
| Brittny Mejia (LAT) | #31320, #31322 | 2 (the other "Mejia" hits are war memorial records and others) | 1 | The Saugus shooting only. One of the eight. |
| Ruben Vives (LAT) | #31322 | 1 (the other "Vives" hits are other people) | 1 | The Saugus shooting only. |
| Sandy Banks (LAT) | #31324, #31328 | 3 (#31338 cites) | 2 | The Saugus shooting only. One of the eight. |
| Laura Newberry (LAT) | #31328 | 2 (#31338 cites) | 1 | The Saugus shooting only. |

A citation as a source is counted above but not treated as "a person that points at them" (PROFILES). A footnote names the writer of the source, not a person in the valley's history. If Nathan reads PROFILES the other way, Perry Smith, Peeples and Donner move first.

## Read from a description

None of these was fixed.

1. **Rule (b) and rule (c) are quoted in words they do not contain.** The overnight brief and MORNING-2026-10-09.md quote rule (b) as "two or more pieces gets a record". The 8 October bylines report quotes it as "two or more pieces" and rule (c) as "a role of their own". The census itself reads "the person wrote **two or more** separate pieces in the archive, compilations not counted" and "the person has their own role in SCV history apart from the byline". The quotation marks carry a summary, and the summary drops "compilations not counted" (which is what kept #12852 from counting for the tribute writers).
2. **Rule (b) is said to follow AGENTS.md.** The census says "It follows AGENTS.md: full records only for a meaningful, recurring role". That was written about the rule, not tested against the cases it would decide. The eight Los Angeles Times reporters are where the two part. The author-links dry run of 5 October then says the five names "qualify for a record" under the census rule, which states a proposal's outcome as if the rule stood (it does add that Nathan approves any record).
3. **"46 byline names".** The brief's count is the bylines report's record count. The records carry 44 names (above).
4. **Shelby Jacobs's verdict rests on a page title.** hb1902.htm was read for its `<title>` and og:title only, not its body. It is a description of the page in the webmaster's words: read the page before acting on it.
5. **Mike Kuhlman's superintendency** was read from one timeline line, and the year on that line was not read.

## Files

- This file.
- Scratch, not for commit: `storage/runtime/scratch/bylnames.php`, `bylnames.json`, `bylctx.php`, `bylctx2.php`, `bylctx3.php`, `bylpats.txt`, `bylmirror.sh`, `bylmirror.tsv`, `bylmctx.sh`, `bylm2.sh`. No script was left in `scripts/import`.
