# Authorship census, 5 October 2026

Claude, read only. No database writes, no edits. The per-record data is in `authorship-census-2026-10-05.json` next to this file (831 records: id, section, title, class, the byline as printed, the matched person id).

This step is the count. Nothing was linked.

## The answer in three numbers

Of 831 articles, obituaries, documents and collections (830 live, 1 disabled):

- **419 have an author recorded** as a linked person (class A). Six more name a person only in the sourceLine text (class B).
- **356 have a byline on the original page that Craft has not captured** (class C). 325 of them name someone who already has a person record, so they are a link away. 219 of the 356 are Sol Taylor's "Making Cents" columns.
- **6 have no author at all**, in Craft or on the page (class D). Another 44 have only an institutional author: a newspaper, a County or City office, a district, a society (class E).

## How authorship is stored now

| Section | Person link | Text | Notes |
|---|---|---|---|
| articles (764) | `writtenBy`, `editedBy` (to persons) | `sourceLine` (the publication, used as a byline on 2 records) | `publishedBy` links an organization |
| collections (14) | `writtenBy`, `editedBy` | `sourceLine` (publisher, decision of the Reynolds entry in CHANGELOG) | `articlesInCollection` and the articles' `partOfCollection` agree in every collection (checked) |
| documents (46) | **none: the entry type has no `writtenBy`** | `sourceLine` | `publishedBy` (organization), `subjectPerson` |
| obituaries (7) | **none: no `writtenBy`** | none (`publicationDetails` is empty on all 7) | `obitPublishedIn` (organization), `obitSubject` |

There is no byline text field (`bylineRaw`, `authorName` and the like do not exist). `sourceLine` is the publication line by the decision of the Reynolds collection entry in CHANGELOG ("sourceLine is the publisher"), so a byline found there is in the wrong field.

So for obituaries and documents a person author cannot be recorded at all today. Adding `writtenBy` to those two entry types is a schema change for Nathan to approve.

## Counts

B and C are split: **1** = the named person has a record (an easy link), **2** = no record, **3** = several names, some with records.

| Section | A linked | B text, record | B text, no record | C page, record | C page, no record | C page, mixed | D none | E institutional | Total |
|---|---|---|---|---|---|---|---|---|---|
| articles | 409 | 0 | 0 | 321 | 23 | 1 | 3 | 7 | 764 |
| obituaries | 0 | 0 | 0 | 0 | 4 | 0 | 0 | 3 | 7 |
| documents | 0 | 5 | 1 | 2 | 3 | 0 | 3 | 32 | 46 |
| collections | 10 | 0 | 0 | 2 | 0 | 0 | 0 | 2 | 14 |
| **All** | **419** | **5** | **1** | **325** | **30** | **1** | **6** | **44** | **831** |

The one disabled record is an article in class A.

**Obituaries:** none of the seven has an author in Craft, and there is no field to put one in. Four print a byline: Sarah Donner (Gary Murr, The Signal), Patricia Farrell Aidem (Ruth Newhall, L.A. Daily News), Stephen K. Peeples (George Caravalho, SCVNews.com) and Carl Goldman (Connie Worden Roberts). The other three are a Los Angeles Times notice, a funeral-home notice and an HSSC Annual entry.

**Documents:** 32 of 46 are institutional by nature: County Registrar canvasses, City Clerk files, district board pages, 1880s and 1890s newspapers, *Pen Pictures*. The six B records name their author in `sourceLine`:

- Connie Worden-Roberts, three pieces (#16418)
- Demetrius G. Scofield's eulogy (#21584)
- A.B. Perkins's 1957 *Quarterly* study (#333)
- Perry Smith of KHTS (no record)

## The names (B and C), by number of records

These are all 41 names; there are fewer than 40 beyond the top few. Records with no person id need a decision under the rule below.

| # | Name as printed | Records | Class | Person record |
|---|---|---|---|---|
| 1 | Dr. Sol Taylor ("By Dr. Sol Taylor") | 219 | C | #2582 Sol Taylor |
| 2 | Jerry Reynolds ("HISTORY OF THE SANTA CLARITA VALLEY BY JERRY REYNOLDS", page header) | 53 | C | #281 |
| 3 | Leon Worden ("By Leon Worden", "Stage Review by LEON WORDEN") | 35 | C | #279 |
| 4 | A.B. Perkins / Arthur B. Perkins | 6 (5 C, 1 B) | C, B | #333 Arthur Buckingham Perkins |
| 5 | Connie Worden-Roberts | 3 | B | #16418 Connie Worden (alias Worden-Roberts) |
| 6 | Darryl Manzer | 3 | C | #2579 |
| 7 | Jason Smisko | 3 | C | none |
| 8 | Chris Price | 3 | C | none |
| 9 | Richard "Doc" Rioux / Richard H. Rioux | 2 | C | #2585 |
| 10 | Patti Rasmussen ("By PATTI RASMUSSEN", Gazette) | 2 | C | #2591 |
| 11 | Philip Ellis | 2 | C | #28364 Philip Ellis Jr. |
| 12 | Pat Saletore | 2 | C | none |
| 13 | Alex Hernandez | 2 | C | none |
| 14 | Laurel Suomisto | 2 | C | none |
| 15 | Vonnie Wang | 2 (1 + compilation) | C | none |
| 16 | James L. Miller | 2 (1 + compilation) | C | none |
| 17 | Rich Boerner | 2 (1 + compilation) | C | none |
| 18 | Christina Hanson | 2 (1 + compilation) | C | none |
| 19 | Buddy T. (a pen name, "@ The Mining Company") | 2 (1 + compilation) | C | none |
| 20 | Howard P. "Buck" McKeon | 1 (compilation; his own tribute #12850 is already linked) | C | #18791 |
| 21 | Jo Anne Darcy | 1 (compilation) | C | #16140 |
| 22 | Cameron Smyth | 1 | C | #16380 |
| 23 | Laurene Weste | 1 | C | #15929 |
| 24 | Frank Ferry | 1 | C | #23083 |
| 25 | Demetrius G. Scofield | 1 | B | #21584 |
| 26 | John Lang (his 1875 letter; named in the webmaster's note, not a byline) | 1 | C | #18820 |
| 27 | Abel Stearns (his 1867 letter; named in the title) | 1 | C | #309 |
| 28 | Perry Smith, AM-1220 KHTS | 1 | B | none |
| 29 | Paul Brotzman | 1 | C | none |
| 30 | Phil Lantis | 1 | C | none |
| 31 | Andree Walper | 1 | C | none |
| 32 | Shelby Jacobs | 1 | C | none |
| 33 | Michael Fleming | 1 | C | none |
| 34 | Dr. Alan Pollack | 1 | C | none (he is a photo-key donor, AL prefix) |
| 35 | Paul Allen (letter, signed) | 1 | C | none |
| 36 | Sarah Donner, Signal Staff Writer | 1 | C | none |
| 37 | Patricia Farrell Aidem, L.A. Daily News | 1 | C | none |
| 38 | Stephen K. Peeples, SCVNews.com | 1 | C | none |
| 39 | Carl Goldman | 1 | C | none |
| 40 | Lauren Kay | 1 | C | none |
| 41 | Wayne G. Sayles | 1 | C | none |

Where the linking pays: the top four names (Taylor, Reynolds, Worden, Perkins) are 313 of the 362 B and C records, all to existing records. The remaining 30 no-record names are one to three records each.

## The Rasmussen case: what join is missing

How a person page lists what someone wrote (`templates/persons/_entry.twig`, lines 78 to 84, and `templates/persons/_works.twig`):

- The page counts every entry in any section whose `writtenBy` points at the person: "Wrote N pieces in the archive", linked to `/persons/<slug>/works?role=wrote`.
- It never lists the pieces and never names a collection. A collection the person wrote is counted as one more "piece".

Checked on the local site:

- **Patti Rasmussen:** "Wrote 26 pieces in the archive" (25 Open Book columns plus the Open Book collection). The column is named only in her biography prose. Her two Old Town Newhall Gazette pieces ("Newhall Hardware Quits.", "Saletore: Young At Hart.", #12595 and #12593) print "By PATTI RASMUSSEN" but have no `writtenBy`, so they do not reach her page.
- **Sol Taylor:** "Wrote 1 piece in the archive". That one piece is the Making Cents collection. All 219 of his columns are unlinked.

So the missing join has four parts:

1. **A collection's author does not pass to its pieces.** `writtenBy` on a collection is never inherited, and the earlier passes (set_collection_authors.php, _2.php) set pieces only in some series. Members with the collection's author on the page but no link:
   - Making Cents: 219 of 219
   - Reynolds's *History*: 53 of 80. Chapters 1 to 22 are linked; 23 to 71, the Epilogue, Bibliography, Notes and the Kingsburry piece are not.
   - Selections from Leon Worden: 22 of 219
   - Perkins's *Story of Our Valley*: 5 of 20
   - Rioux: 2 of 34

   Open Book, Pauline Harte and Black 'N' Whyte are complete.
2. **Catalogue collections have many authors and no collection author.** All 37 Old Town Newhall Gazette pieces are unlinked, though 36 print a byline: Worden 10, Manzer 3, Smisko 3, Price 3, Rasmussen 2 and others. Nothing inherits here, and nothing should.
3. **Obituaries and documents cannot carry an author.** There is no `writtenBy` on either entry type.
4. **The person page does not show a person's columns as columns.** The count folds the collection into the pieces, and the works page lists it as one row among articles. A reader on Rasmussen's page cannot get to "Open Book" from the archive box, and Boston's and Manzer's pages reach only their collection records, because none of their columns is imported (those collections have 0 members).

## Other findings

- **No conflicts in class A.** Every linked author agrees with the page where the page prints a byline. 51 linked records print no byline line the census could read (series chapters, maps, prefaces), which is expected.
- **Conflict, document #28295** "City Backers Join Prison Furor (Karina Lutz, The Signal, ...)": the record title names Karina Lutz, but the page prints "By Lauren Kay." Check the clipping before linking either.
- **Reynolds's Notes (#2173)** sit under the page header "BY JERRY REYNOLDS", but the notes are editorial ("1. Reynolds wrote that..."). They are probably Leon Worden's, as Perkins's "Editor's Notes" (#1432) are linked to Worden. Nathan to decide.
- **Possible duplicate pairs** (one record from the mirror page, one from body text with no page):
  - #12206 and #12200
  - #12204 and #12188
  - #12280 and #12258
  - #12208 and #12152
- **Tributes compilation #12852** reprints ten tributes, each also its own record. Linking the compilation to all eight writers would double their counts. Recommend linking only the separate records.
- **Courtesy lines are providers, not authors:** Stan Walker (four Mentry documents and the Wiley obituary), Lauren Parker, Tricia Lemon Putnam. They are listed under `contributor_lines` in the JSON and not counted as authors.
- **The 1874 Vasquez sketch (#18991)** names V. Wolfenstein only as the copyright claimant. The text is anonymous, so it is class E.
- **The six with no author (D):**
  - #26999 "The ladder that runs the other way": written in this build. Whose name does it carry?
  - #12854 Rioux's biographical sketch
  - #12702 the Country Fair listing
  - #28303 and #28301, two officer lists compiled by the site
  - #28289 Key to Photos

## Recommended plan for the linking pass

One script, dry run first, in the export, review, apply pattern. It reads `authorship-census-2026-10-05.json` and writes nothing that is not in it.

1. **Schema (ask first).** Add `writtenBy` (persons) to the obituary and document entry types. Without it, 15 of the C and B records (4 obituaries, 11 documents) have nowhere to go. Add one plain-text `bylineText` field to articles, documents, obituaries and collections, for names that stay text. Do not reuse `sourceLine`, which is the publication.
2. **The easy links (329 records).** Set `writtenBy` where the page byline matches an existing record (C1 and B1): Taylor 219, Reynolds 53, Worden 35, Perkins 6, Worden-Roberts 3, Manzer 3, and the rest. Skip #2173 (Notes) and #12852 (compilation) until Nathan decides. Hold #28295 for the Lutz or Kay check.
3. **Inheritance, stated, not automatic.** A piece takes its collection's author only where the piece's own page prints the same name, which is true of every case above. Catalogue collections (Gazette, Abu Ghraib, Newsmaker) never pass an author down.
4. **Names with no record (30 records, 27 names).** Apply the rule below. Names that stay text go to `bylineText` exactly as printed.
5. **Person page (template, Nathan's design call).** Under "Wrote", show the collections a person authored by name ("Open Book, a column, 25 pieces"), then the count of other pieces. Count pieces and collections separately.
6. **Re-run this census after the apply.** Expected: A rises from 419 to about 748, C falls to about 30, and Sol Taylor's page reads 219 pieces and one column.

### Rule: when a byline name gets a person record, and when it stays text

A byline name gets a person record when **any one** of these holds:

- (a) a record already exists. Link it, never duplicate it.
- (b) the person wrote **two or more** separate pieces in the archive, compilations not counted. Today that is Jason Smisko, Chris Price, Pat Saletore, Alex Hernandez and Laurel Suomisto.
- (c) the person has their own role in SCV history apart from the byline: an office holder, a subject of other records, a donor in the photo key (Dr. Alan Pollack), a local journalist of record (Carl Goldman, Stephen K. Peeples, Perry Smith).

Otherwise the name stays text in `bylineText`, as printed. This covers the one-off letter writers and tribute writers: Paul Allen, Christina Hanson, Rich Boerner, James L. Miller, Vonnie Wang, Buddy T. It follows AGENTS.md: full records only for a meaningful, recurring role, and passing mentions stay in text.

A pen name (Buddy T.) never gets a record. A staff or institutional byline ("Signal Staff", "Signal Editorial") goes to `publishedBy` as an organization, never to a person. Nathan confirms the (c) list before any record is created.

## Method and limits

- Craft read through `ddev craft exec` (storage/runtime/ac1.php to ac5.php), every record with `status(null)`.
- Pages read from `/Volumes/Reggie/SCVHistory/scvhistory.com` + `legacyUrl` (or `obitLegacyUrl`) as latin-1, only the pages records name. 167 of the 412 records without a linked author had a mirror page.
- The mirror does not hold the Sol Taylor columns (`signal/coins/soltaylor*.htm*` are absent; the folder has 39 files). For those, and for the other records with no mirror page, the census reads the imported body text, which keeps the page's byline block ("By Dr. Sol Taylor / "Making Cents" / The Signal / date"). The JSON says which source each record used (`page_source`).
- Bylines were found by pattern: "By X" and its variants near the top, "Name · date" headers, all-capital signature lines at the foot, author-bio lines, copyright lines. Every non-coins record without a linked author was then read and classed by hand; the overrides are recorded in each record's `note`.
- Name matching uses person titles and `personAliases`, and the full name or first plus last name.
