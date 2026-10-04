# Mirror searches this week, re-run with the system grep (3 October 2026)

Read-only audit by Claude Code. Nothing written to the database; no existing file edited.

## The fault, confirmed

`type grep` in the agent shell: "grep is a shell function", which runs `ugrep -G --ignore-files --hidden -I ...`. With `-I`, ugrep stops seeing a page's text at its first byte that is not valid UTF-8, so most of an ISO-8859-1 legacy page is invisible after the first accented letter, curly quote or copyright sign. The wrapper still returns 26,972 of the 33,690 HTML files for a match on any character, so the loss is partial and silent: the page is "searched", only its first part is.

Reproduced today over `--include="*.htm*"`: "Connie Worden" 49 pages with the wrapper, 148 with `LC_ALL=C /usr/bin/grep -rlia` (207 with the flipbook XML files counted, as ERRORLOG says).

A second test: the by-name survey lists 360 pages for a random sample of 20 of its 134 people. The wrapper can find text in only 105 of those 360 (29 percent). A survey run with the wrapper would have lost about seven pages in ten.

## Which work used what

| Method | Work this week | Affected? |
|---|---|---|
| Python, `open(p,'rb').read().decode('cp1252', errors='replace')` over `os.walk` | survey_by_name.py (by-name-persons, -places, -organizations), survey_legacy_person_pages.py, trace_wordpress_facts.py, audit_wordpress_records.py, audit_generated_bodies.py | No |
| Python count of the mirror text | add_aliases_2026_10_03.php alias counts (the counts in the script match the system grep, not the wrapper: "Connie Worden-Roberts" 98 in the script, 98 system, 26 wrapper; "Ruth Waldo Newhall" 93, 94, 22; "Lake Elizabeth" 196, 200, 27) | No |
| Craft database only | audit_person_profile_sources.php (person-profile-sources-2026-10-02), note-wording, unshown-fields, date-century, on-this-day | No |
| Shell `grep`, the wrapper (method stated) | office-only-recount-2026-10-03.md "Mirror (Reggie)" column; brian-walters-sources-2026-10-03.md "Legacy mirror" | Yes |
| Shell grep, wrapper by the ERRORLOG entry | "Bennett-Arcan: four mirror pages" (build_blank_records_4.php header, CHANGELOG 3 October afternoon); Christy Smith (4 against 6) | Yes |
| Not stated; a mirror search is implied | "no source here names a California Battalion" (fold_and_retire_records.php, removed-claims.json); "no source for the middle name Allen" (build_profiles_batch4.php, #339's public note); "no source here places Couts in the Santa Clarita Valley" (fix_couts_tie.php, public text on his record); De Anza retired; Larkin; Fages's birthplace | Re-run below |
| System grep, already corrected | connie-worden-sources-2026-10-03.md (it found the fault) | No |

## Re-run results

Counts are pages under `--include="*.htm*"` (33,690 files), wrapper against `LC_ALL=C /usr/bin/grep -rlia` (or the same search in Python reading bytes as latin-1, which gives identical counts). Names searched as fixed strings, case-insensitive.

| Finding | Where recorded | Method | Original | Re-run (system) | Decision affected? |
|---|---|---|---|---|---|
| Brian Walters: "no hits" for "Brian Walters", "Brian D. Walters", "B. Walters" | brian-walters-sources-2026-10-03.md | wrapper | 0 | 0 in HTML, and 0 in every file type (system grep over the whole mirror) | No. "Walters" alone: 38 against 53; the extra pages are other Walterses (a 1945 circus agent etc.) |
| Hart board roster, 1945 to date, `/scvhistory/hartschoolboardmembers.htm` (Leon Worden's, from the district's own lists) | not cited anywhere in the recount; the by-name survey lists it only for Buck McKeon | wrapper missed it | not found | names Dennis V. King, Patricia A. Hanrion, Gloria E. Mercado-Fortine, Steven M. Sturgeon, Paul B. Strickland, Joseph V. Messina, Paula E. Olivares, Connie Worden, term by term with officers | **Yes**, see below |
| Dennis King: class (d), "none found"; mirror "2 yearbook files" | office-only-recount | wrapper | 2 | 2 (full name), plus the roster as "Dennis V. King" | **Yes.** Roster: elected 1985, reelected 1989 and 2001, 2005; did not seek reelection 1993 (back 1997) and 2009; president 1987-88, 1991-92, 1999-2000, 2004-05. Not class (d) for office holdings |
| Patricia Hanrion: class (d), "none found"; "1 file" | office-only-recount | wrapper | 1 | 2 (surname 3, the third is the roster) | **Yes.** Roster: elected 1993, reelected 1997, 2001, 2005; did not seek reelection 2009; president 1996-97, 2001-02, 2006-07 |
| Gloria Mercado-Fortine: conflict "CEDA's 1997 win and 2001 loss against sources saying Hart 2000 to 2016" | office-only-recount, conflict 3 | wrapper | 14 | 20 ("Gloria Mercado") | **Yes.** The roster agrees with CEDA: elected 1997, defeated 2001, elected 2003, reelected 2007 and 2011. The web sources' "2000 to 2016" is the error. Also SCV Woman of the Year 2018 (mwoty.htm), a mirror source for what the recount took from the web |
| Joe Messina: "Gap: no row between 2009 and 2018" | office-only-recount | wrapper | 2 | 2, plus the roster as "Joseph V. Messina" | **Yes.** Roster: elected 2009, president 2012-13, reelected unopposed 2013 |
| Paul Strickland: resignation May 2013 and Chris Fall's appointment from scvnews.com | office-only-recount | wrapper | 3 | 4 (+ citycommissioners.htm) and the roster | Sourcing only: the roster gives "resigned eff. 5-1-2013", Fall appointed 6-5-2013 (resigned 8-16-2013), Robert P. Hall appointed 10-16-2013; and "reelected/unopposed 2013 ... did not assume office". City Arts Commission 2008-2014 is on citycommissioners.htm |
| Steven Sturgeon: "1 file" | office-only-recount | wrapper | 1 | 1 as "Steven Sturgeon"; 4 more as "Steve Sturgeon"; the roster as "Steven M. Sturgeon" | Sourcing: SCV Man of the Year 2013 (mwoty.htm); presidencies 2003-04, 2008-09, 2013-14. Class (b) unchanged |
| Paula Olivares: class (c), "Nothing on her Hart years beyond the 1995 win" | office-only-recount | wrapper | 2 | 2, plus the roster as "Paula E. Olivares" | **Yes.** Roster: elected 1991, reelected 1995, did not seek reelection 1999; president 1993-94, 1997-98. The 1991 election is before CEDA |
| Linda Storli: "3 files" | office-only-recount | wrapper | 3 | 8 | Sourcing: City Parks, Recreation and Community Services commissioner 1988-1990 (citycommissioners.htm), an office not recorded; quoted as parade committee member 2012 (tv1210a). Class (b) unchanged |
| Lynne Plambeck: "12 files" | office-only-recount | wrapper | 12 | 15 | No; adds a 2003 Signal page naming her an NCWD board member (sg060803) |
| Ed Colley, Gary Martin: CLWA holdings from the agency's web page | office-only-recount; create_office_gap_holdings.php | wrapper | 3; 0 | 4; 2 | Sourcing: clwadirectors.htm (Leon's "CLWA Directors, 1962 to Date") lists "ED COLLEY 2003—" and "GARY MARTIN 2013—", an archive source for holdings created today from the web |
| Rochelle/Shelley Weinstein: "8 files under Weinstein" | office-only-recount | wrapper | 8 | 11 | No: the three extra are other Weinsteins' obituaries |
| Michael Shapiro: 0 | office-only-recount | wrapper | 0 | 1 | No: a surgeon of the same name (hmnmh082215) |
| The other recount names (Solomon, Todd, Bryce, Umeck, Hogan, Barlavi, Koscielny, Sansone, Armitage, Orzechowski, Caesar, Clegg, Sparks, K. Cooper, Arrowsmith, de la Cerda, Diaz, Robert, Winkler, Christopher, Pearson, Kunak, Emmons, Gingrich) | office-only-recount | wrapper | as in the table | same | No change to class (d) for Hogan, Caesar, Sparks, Winkler, Diaz, Emmons, Gingrich |
| Bennett-Arcan party: "the mirror names it on four pages" | build_blank_records_4.php; CHANGELOG 3 Oct afternoon | wrapper (ERRORLOG) | 4 | 8 (+ jstevens, sg103005-manzer, timeline, Reynolds part 19) | No: the record was kept; more pages only strengthen it |
| Christy Smith | ERRORLOG | wrapper | 4 | 6 (+ citycouncilresults2016, timeline) | No: the 2016 results page is already in the next steps |
| California Battalion: "no source here names a California Battalion" | fold_and_retire_records.php; removed-claims.json; the redirect's note | not stated; wrapper gives 0 | 0 | **1**: pollack1114kitcarson.htm, Dr. Alan Pollack's Kit Carson story: "Fremont's men, now referred to as the California Battalion, met up with ... Stockton in Monterey" | **The stated reason is wrong.** The fold may stand (the page places the battalion in Monterey, not the valley), but the note and removed-claims entry should say a source names it |
| Catalonian Volunteers folded into Portolá | fold_and_retire_records.php | not stated | 1 (wrapper) | 2 (+ Reynolds part 7) | No: the fold was Nathan's call, not a "no source" finding; Reynolds part 7 supports the expedition record |
| De Anza Expedition retired: "no source puts the expedition in the valley" | fold_and_retire_records.php | not stated | "De Anza Expedition" 0, "Anza Expedition" 3 | 1; 5 | No: Reynolds part 10 mentions the 1776 expedition only at the Colorado River; chs052015 is about Afro-Mexican settlers. Nothing places it here |
| Rémi Nadeau: "No source for the middle name Allen has been found" | build_profiles_batch4.php; public note on #339 | not stated | 0 | **1**: lw2449.htm: "the author Remi Allen Nadeau (aka Remi Nadeau III), born Aug. 30, 1920, is the great-great-grandson of the L.A. freighter" | **The note should change, not the decision.** A source gives "Allen" to the 20th-century author, which explains the old title; the retitle stands |
| Couts: "That letter is his only tie ... no source here places Couts in the Santa Clarita Valley" | fix_couts_tie.php; public text on his record | not stated; wrapper `-w Couts` finds 4 | 4 | **23** (word "Couts"): HS3001 to HS3016 (Leon's Rancho Camulos captions), camulos-nrhp3, lw2963 (Odell 1939), lw3758, delcastillo1980, Reynolds parts 20 and 21 | **Yes, for Nathan.** Leon's HS30xx caption says Couts's presence "incidentally was felt in the Santa Clarita Valley at one time or another", and the captions tie him to the Camulos and Ramona rivalry. The caption names "Cave Couts Jr." and then "Couts" in one paragraph, so which man it means needs reading. The public sentence "no source here places Couts in the Santa Clarita Valley" is not safe as written |
| Larkin: "No source in this archive places him in the Santa Clarita Valley" | settle_borrowed_ties.php | not stated | 13 | 21 | No: the extra pages (Prudhomme 1922, Perkins part 5, Reynolds part 17, Ripley, LW columns) have him writing from Monterey |
| Fages: "no source for the town [Guissona] has been found" | build_profiles_batch5.php | not stated | 0 | 0 | No |
| Tom Frew left out of the 26; "Thomas Frew" alias left out as the blacksmith | add_aliases_2026_10_03.php; CHANGELOG 3 Oct 4 a.m. | Python survey; judgment | 4 (wrapper) | 12 | Probably no: the extra pages (tf1000, frew1296, ap0724, ap1603, hs5802, Brunner 1940) are the blacksmith family, Thomas M. Frew II to IV. The roster also has a "Thomas M. Frew Jr." on the first Hart board, 1945-51. Whether the Historical Society president is Thomas Frew IV was not checked here |
| Connie Worden: no "Connie Roberts", "Constance Worden", "Mrs. Leon Worden" | connie-worden-sources-2026-10-03.md | system grep | 0 | 0 | No. The report already cites the Hart roster for her 1974-79 seat |
| Alias counts (35 aliases) | add_aliases_2026_10_03.php | Python | as listed | within a few percent of the system grep | No. "Heritage Auctions" is 3 by both, at the threshold |

## Python-based surveys: not affected

survey_by_name.py, survey_legacy_person_pages.py, trace_wordpress_facts.py, audit_wordpress_records.py and audit_generated_bodies.py walk the mirror with `os.walk` and decode each page as cp1252 with `errors='replace'`. The encoding fault cannot reach them. Spot check: Connie Worden's by-name entry lists her own obituary (obituary_conniewordenroberts.htm), which the wrapper misses. One residual risk, not tested: each script skips any file that raises OSError without counting it, so a run during the 2 and 3 October "Operation not permitted" windows would undercount silently. Their outputs (3 October, 01:47 to 03:59) come between those windows and find pages throughout, so this looks unlikely.

The by-name survey's own rules, not the encoding, kept the Hart roster off every board member's list except McKeon's (a roster names each person often but in few words of prose). Roster pages need a separate pass.

## What needs re-running in full

1. **The office-only recount, mirror column and classes, against the three roster pages**: hartschoolboardmembers.htm, clwadirectors.htm, citycommissioners.htm. At least King and Hanrion leave class (d), Olivares leaves (c) for her terms, and Mercado-Fortine's conflict is resolved in CEDA's favour. The 23 holdings created today (create_office_gap_holdings.php) should be checked against the CLWA list, and the Hart holdings derivation that waits on Nathan should use the Hart roster, which gives appointments, resignations, unopposed reelections and officers back to 1945.
2. **Every public "no source has been found" or "no source here" note written this week** from a mirror search: re-search each with the system grep before it stands. Two are already wrong (California Battalion, Remi Allen Nadeau) and one is doubtful (Couts). The others in build_profiles_batch4.php and _batch5.php (burial places, Carson's and Crespí's dates) were not re-run here.
3. **Christy Smith, Chris Trunkey, Brian Walters, Bennett-Arcan and the hospital, Chamber and SCV Water records** of the 3 October afternoon: the sources were chosen while the wrapper was the search tool. Smith and Bennett-Arcan are known to have missed pages; re-run the others' names.
4. Any similar roster pages for the Newhall, Saugus, Sulphur Springs and Castaic boards (file names were checked only for "boardmembers", "directors", "trustees" and "commissioners").

## Method

Counts were made two ways and agree: a Python pass over all 33,690 `*.htm*` files read as bytes and lowercased (latin-1, so every byte is kept), and `LC_ALL=C /usr/bin/grep -rliaF` for spot checks; the wrapper counts are from the agent shell's `grep -rliF --include="*.htm*"`. 100 names and phrases were re-run in full (all 37 recount names plus short forms, the Walters, Connie Worden, Bennett-Arcan and fold/retire phrases, and the 35 aliases); the by-name surveys were sampled (20 people, 360 pages). Scratch files are in the session scratchpad (`audit/`), not the repo.
