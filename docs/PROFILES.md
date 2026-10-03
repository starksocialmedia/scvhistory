# Editorial profiles

How a person record gets a 2026 editorial body (`bodyAuthorship: editorial-2026`):
the rule before writing, the shape that worked, and what it costs. Written after
the Charles Alexander Mentry pilot (#18648, applied 28 September 2026).

## Who gets a person record

**A person record requires significance to SCV history, not appearance in a
result** (Nathan, 1 October 2026, correcting his own rule of 25 September that
standing more than once earned a record; it produced about 64 people whose only
trace was losing elections). Repeated candidacy is persistence, not significance.

A person gets a record when the archive holds something about them beyond a
result: an office held, an article, photograph, document, obituary or war
memorial record about them, a profile with sources, or a place, organization or
person that points at them. A losing candidate stays a name and a vote count on
the election page, which loses nothing: the page prints the name as the ballot
did, linked only where a record exists. Winning an election is office, and keeps
a record; where the body's data is thin, Nathan decides.

**A person belongs in this archive for what they did here, and a profile leads
with that** (Nathan, 3 October 2026). Filming here is a location credit, not a
connection: John Wayne's record was removed on that ground (the name stays in
the text, and `removed-claims.json` keeps him from coming back). A profile's
first paragraph is what the person did in the valley; a career elsewhere gets a
sentence, and the record's Wikipedia link carries the rest. Where the archive
holds nothing local, the profile says so in a line rather than borrowing a
career: Kit Carson's and Junípero Serra's are the examples. The connection
has to be the person's own: a tie through a father's or husband's land or
office is inherited (Juventino del Valle's profile is a line for that reason).

`scripts/import/audit_person_significance.php` reports every record against
this bar (`inventory/review/person-significance.md`). The import scripts that
created records on the old rule no longer do.

## Before writing: count the source set

Nathan, 28 September 2026. Before any profile is written, count:

1. **Is there a hub page?** A legacy page that gathers the person's sources, the
   way ch1070 gathered Mentry's eight.
2. **How many sources from the person's lifetime?** Contemporary: written while
   the events were current (an obituary, a certificate, a newspaper item, a
   biography published in their life).
3. **How many are already in Craft?** The rest are imported first, as documents
   or photographs, before a word of the profile is written.

**Fewer than three sources from the person's lifetime means a retrospective
profile.** That is fine, as long as the page says so: the body names its
evidence as later accounts, and the evidence fields say `retrospective`.

## Legacy prose becomes a source, not the body

Nathan, 28 September 2026. Biographical prose already on a person record, or
on the legacy page about them, is not the profile. It becomes its own **article
record** with its author's byline (`writtenBy`) and its `legacyUrl`, pointing at
the person through `subjectPerson`. The person's body becomes the editorial
profile, which cites that article like any other source.

- Every word is kept, the author keeps the credit, and the page speaks in one
  voice. Two biographies side by side is two voices saying overlapping things.
- The Mentry pilot did this implicitly: Leon's ch1070 biography was a source
  and never the body. Henry Mayo Newhall #283 makes it explicit: Leon's 2,869
  characters (`legacy-leon`) move to an article by Leon Worden, `legacyUrl`
  /scvhistory/ap1335a.htm.
- The Vasquez sketch set the pattern for a published item
  (`create_vasquez_document.php`): the 1874 text left person #285's body for a
  document with its copyright line and date.

**The 33 hidden WordPress bodies** (`wordpress-import-unsourced`) follow the same
rule wherever their authorship can be established: an article under the real
author's byline, cited by the profile. **Where authorship cannot be established,
the body stays hidden.** It does not become an article by nobody: an unsigned,
uncited biography published as a record would claim a standing it has not
earned.

## Jerry Reynolds as a source: the reliability rule

Nathan, 29 September 2026, from Grok's Reynolds dossier
(`inventory/review/jerry-reynolds-sources.md`, section 2). Grok tested 74 of his
claims against contemporary sources: **18 held, 41 were wrong, partly wrong or
superseded, and 15 are unresolved.** The errors are not random. They fall into
five kinds:

1. **Numbers**: acreage, money, ages, headcounts. The weakest point, and some
   are inconsistent between his own chapters (the del Valle partition figures
   add up to 13,400 acres more than the rancho).
2. **Dates of smaller events**, which slip by a year or several. Dates of major
   documented events are usually right.
3. **Dramatic stories**: the cowboy-suit burial, the Hap-A-Lan morgue, the
   Harrison meal at Saugus. Often wrong, or unprovable.
4. **Titles that did not exist yet, or were never held**: Naval Academy,
   mayor, state senator, "Colonel" Porter.
5. **Two people merged into one**: the two Remi Nadeaus, the Jenks Harris
   robbery built from several crimes.

The usual mechanism is inheritance, not invention: many errors trace to a
secondary source he used (Wally Smith 1958, the 1889 history).

**The rule.** An uncorroborated Reynolds **date for a major event** is accepted
provisionally, and attributed to him in the text. Anything else uncorroborated
(a figure, an age, a sum, an acreage, a rank or title, a "first" or other
superlative, the identity of a minor figure, a vivid story) needs verification
before a profile states it: look for Perkins, a newspaper or a deed first, and
until then give it as his, or leave it out.

**What "Reynolds part NN" is.** The pages `signal/reynolds/partNN.html` are
chapter NN of the **1998 web edition**, *History of the Santa Clarita Valley*,
edited by Leon Worden for the SCV Historical Society, two years after Reynolds'
death. It was drawn from his 1992 book *Santa Clarita: Valley of the Golden
Dream* and his Signal columns of 1976 to 1994, and it is edited: some chapters
were adapted, some errors corrected silently. So it is not always his words.
Chapter 59, Mixville, was rewritten by Robert S. Birchard; chapter 56 includes a
section excerpted from Leon's work of 1996, and chapter 60 is adapted in part
from Leon's of 1997; chapter 70 was reworked by another contributor. **Cite the
chapters as the 1998 edition** (Reynolds, *History of the Santa Clarita Valley*,
ed. Worden, 1998, chapter NN), not as Reynolds' Signal column, and not as his
own words where the edition says otherwise.

**Care.** His column of 14 December 1984 on Bowers Cave tells readers how to find
an archaeological site. Grok flagged it for tribal consultation. It had
already been imported, as article #2177; it is disabled until consultation
(`disable_bowers_cave.php`), and its locational passage is not quoted anywhere
(`inventory/review/live-errors.md`, RL8).

## Leon Worden's Hart board roster: a primary that contradicts itself once

Nathan, 3 October 2026. The roster (hartschoolboardmembers.htm, extracted
verbatim to inventory/legacy/hart-board-roster.json) is Leon's term-by-term
record of the Hart board from 1945, and it is treated as primary: it beat CEDA
and the archive's own inference for Hart's holdings, and it corrected McKeon's
Hart tenure (resigned 7 December 1987, not expired). But it contradicts itself
at least once: Hanrion "did not seek reelection" on one board and was
"reelected 1997" on the next. **If the roster contradicts itself once it may do
so elsewhere.** So, as with Reynolds: where it is the only source for a row and
the row is unclear or disagrees with a neighbouring row, the holding footnotes
both and states neither; where it disagrees with CEDA or the County's returns,
both are shown (Jensen and Solomon marked incumbents in 2009 by CEDA, absent
from the roster's 2008-09 board). A pass reading the whole roster for
self-contradictions, as Grok tested Reynolds's claims, is worth doing before
any profile rests on a roster row alone.

## A note that says no source exists records the search

Nathan, 3 October 2026. **A note asserting that no source exists, or that a
fact was not found, must say what was searched and how, so it can be rechecked
rather than trusted.** Such a note is a claim about the archive, and it can be
wrong in a way a citation cannot: on 3 October a faulty search (the agent
shell's `grep`, which silently skips the mirror's latin-1 pages) had put three
false ones on public records in a week, among them "no source names a
California Battalion" and "no source here places Couts in the valley".

The note keeps its plain reader's sentence, and the record keeps the search:
- **What**: the terms searched, including the variants (Couts, Coutts; Arcan,
  Arcane).
- **Where**: the mirror (its pages, flipbook text and scans' text), the archive's
  own records, and any outside source.
- **How and when**: the tool (`LC_ALL=C /usr/bin/grep -rlia`, or Python reading
  latin-1; never the shell's `grep`) and the date.

The search goes in the record's editorNotes (internal), not the public footnote,
which says only what a reader needs: "No source in the archive, searched
3 October 2026, names him in the valley." A note with no recorded search is
treated as unverified and re-searched before anything rests on it. A removal
or a cut made because "nothing was found" carries the same record, in
removed-claims.json beside the removal.

## The shape that worked

1. **Extract** the sources verbatim from the Reggie mirror into
   `inventory/legacy/<name>-sources.json`, with each page's sha256
   (`extract_mentry_sources.py`).
2. **Import** them as documents and photographs (`import_mentry_sources.php`).
   The body is the source's own text; commentary goes to `webmasterNoteTop`,
   credits and editing notes to `webmasterNoteBottom` (DATA-MODEL: Transcription
   and interpretation). A page that wrote about a source without transcribing it
   gets an empty body.
3. **Make the people and events the profile will cite**, each with its own
   evidence and footnotes (`create_pico_1860s_records.php`).
4. **Write the profile** (`update_mentry_profile.php`): every claim footnoted to a
   record; conflicts kept, not resolved (EDTF sets and qualifiers, a source-fault
   note); evidence per claim (DATA-MODEL: Evidence rates the claim, not the
   document). A disputed claim is written as a disagreement among its sources.
5. **Dry run, read, apply.** Nathan reads the prose in the dry run before the
   apply.

## What the pilot proved

- Evidence per claim holds up. Two birth years, two arrival years, three
  death years for Lyon, and two birth years for Jenkins are all kept visible,
  not resolved.
- The transcription and interpretation split works across ten documents,
  including a certificate with an empty body.
- A disputed claim ("first oil well in ...") is expressible as prose with
  footnotes. It needed no schema.
- Research can overturn an approved framing: Perkins was neither the narrowest
  nor the only hedged source. Check the framing against the sources before
  writing it.

## What it cost

Mentry: eight scripts (six content, two schema), thirteen new records (ten
sources, two people, one event), one research pass by an agent of about six and
a half minutes. The hub page did the gathering: five of its eight links were
sources from the lifetimes of Mentry or his family. Without a hub, finding the
sources is the larger part of the work.

## The queue

| Record | Body now | Hub | Lifetime sources | In Craft | Expect |
|---|---|---|---|---|---|
| Henry Mayo Newhall #283 | Leon's own, 2,869 chars | ap1335, ap1335a | strong: 1882 obituary, 1882 will, 1865-1873 billheads and receipts, railroad pass | 3 of 25 titled pages | full profile |
| Edward Fitzgerald Beale #327 | unsourced, hidden | lw2204, lw2205 | thin locally; Bonsal 1912 is book-length | 4 of 12 | decide scope first: the valley or the life |
| Ygnacio del Valle #293 | unsourced, hidden | lw2052 | weak; Pen Pictures 1889 is posthumous | 2 of 7 | retrospective profile |
