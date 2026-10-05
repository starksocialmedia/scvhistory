# The officers' memorial: a plan

5 October 2026. Claude Code, for Nathan. Nothing is built. Nathan's brief: "anyone killed on duty
serving this valley, whatever their agency"; on the war memorial's pattern; plan first. The research
is inventory/review/lasd-contract-office-memorial-2026-10-05.md, with the pages in
inventory/news/lasd-2026-10-05/.

## Who is on it

By the brief, killed on duty while serving this valley. Ten fit plainly:

| # | Name | Agency | End of watch | Where | The tie |
|---|---|---|---|---|---|
| 1 | Constable McCoy Pyle | Fillmore constable, Ventura County | 24 April 1897 | Castaic Junction | killed here holding prisoners |
| 2 | Deputy Constable Charles A. De Moranville | LA County Constable, Newhall Township | 4 January 1909 | Newhall | Newhall's deputy constable |
| 3 | Deputy Constable J. Edward "Ed" Brown | LA County Constable, Saugus | 14 September 1924 | Tunnel Canyon, near Newhall | stationed in Saugus |
| 4 | Constable John S. "Jack" Pilcher | LA County Constable, Newhall | 4 June 1925 | Bouquet Canyon | Newhall's constable |
| 5-8 | Officers Walter C. Frago, Roger D. Gore, James E. Pence Jr., George M. Alleyn | California Highway Patrol, Newhall office | 5 April 1970 | Newhall, I-5 at Henry Mayo Drive | the Newhall Incident |
| 9 | Deputy Arthur E. Pelino | LASD | 19 March 1978 | Gorman | the resident deputy, in the SCV station's area |
| 10 | Deputy Hagop "Jake" Kuredjian | LASD, SCV station | 31 August 2001 | Stevenson Ranch | SCV station motor deputy; Deputy Jake Way |

Four do not fit plainly, and each is Nathan's call:

| Name | Why not plainly | If included, the tie stated |
|---|---|---|
| Deputy Shayne D. York (LASD), died 16 August 1997 | shot off duty, in Buena Park | assigned to Pitchess, in Castaic; a park there and the I-5 memorial highway from Newhall Ranch Road to Hasley Canyon Road bear his name |
| Deputy David W. March (LASD), 29 April 2002 | killed on duty in Irwindale, not serving here | a Saugus resident and Canyon High graduate |
| Officer Matthew Pavelka (Burbank Police), 15 November 2003 | killed on duty in Burbank | a Canyon Country resident |
| Officer Clarence Wayne Dean (LAPD), 17 January 1994 | killed in the valley riding to work in the earthquake; LAPD and the memorials count him | the 14/5 interchange is named for him |

Recommendation: the ten, and the four in a second group on the same page, "Of the valley, killed
elsewhere or off duty", each with its tie in one line. Leon's own sidebar already carried March and
Pavelka; leaving them out would drop names he chose to keep.

## How it is built

**A section of its own**, `fallenOfficers`, with URLs at `/fallen-officers/{slug}` and an index at
/fallen-officers. Not a second entry type inside the war memorial: the war memorial is a closed set
(54 records, each checked against federal casualty files) with conflicts and eras of its own, and an
officer killed on duty is not a war casualty. Two sections keep each one's index and checks honest.

**The record, on the war memorial's pattern:**
- Shared fields, used as they are on the war memorial: the portrait (featuredImage), body,
  footnotes, factSources (each fact with its notes and any difference a source shows),
  editorNotes, deathDate and deathDateEdtf (the end of watch), burialPlace, recordImages,
  recordDocuments, the legacy fields (legacyKey, legacyUrl, sourcePath, legacyHtml), neighborhood.
- New fields, in the war memorial's place (its `wm` fields are about military service and are not
  reused):
  - `foAgency`: the agency, related to its organization record (LASD, the CHP, LAPD, Burbank
    Police, the county constables, Fillmore's constable); records made where none exists
  - `foRank`, `foAssignment` (station or office), `foBadge` (where published)
  - `foIncidentLocation`, `foCircumstances` (one line)
  - `foValleyTie`: killed in the valley, served the valley, or of the valley killed elsewhere
  - `foMemorials`: the streets, highways, parks and plaques named for them, each sourced
- The index: names in order of the end of watch, the agency and the place, a portrait where one
  is held; the second group below, under its own heading.

**Sources, as on the war memorial:** each record carries at least one official source (the
agency's own memorial or release, the California Peace Officers' Memorial, a contemporary
newspaper) and its tie to the valley. The Officer Down Memorial Page is secondary and a lead. The
"first officer killed here" claims (LASD has said both Brown and Pelino) are not repeated: Pyle
and De Moranville were earlier.

**What is imported:** Leon's twenty "Fallen Officers" pages from the mirror, their grave-marker
photographs and the news reports, as the war memorial's were. Pyle's legacy page mentions the Bowers
Cave find, which TATAVIAM_AUDIT.md governs: that passage stays out of the public body.

**Navigation:** under the WAR MEMORIAL top-level item if the nav proposal's six items are taken
(as "Fallen officers"), otherwise beside the war memorial.

## Order of work, once approved

1. The schema script (dry run first): the section, the ten fields, the entry type.
2. The records for the ten, from the mirror and the official lists, sourced, with fact rows.
3. The four, once Nathan has said which come in.
4. The index and the record template, from the war memorial's.
5. check_render, the nav, the runbook.

## The Sheriff's runoff, checked

Checked 5 October 2026 against a current source: the County Registrar certified the June 2, 2026
primary on June 26, 2026 (its release, saved as inventory/news/lasd-2026-10-05/lavote-20260626-certified-results.pdf,
gives no candidate figures); the Crescenta Valley Weekly's report of the certification gives Robert
Luna 44.15% and Alex Villanueva 21.70%, and the runoff is on November 3, 2026. The election-night
figures (44.18% and 21.71%) and the LA Almanac's (859,070 and 422,272 votes, 44.2% and 21.7%) are
not certified. The certified vote counts are on lavote.gov's results pages, which this check did not
open. Nothing goes on a page until those counts are read.

## Decisions for Nathan

1. The four: in a second group, as recommended, or out.
2. A section of its own at /fallen-officers, as recommended.
3. Its name: "Fallen officers", or "Officers killed on duty", or another.
