# The 16 board-term decisions, with a recommendation each (3 October 2026)

An officeHolding for every school board and water board win, as `council_winners_and_terms.php`
did for the council. These are the choices a script cannot make for you. Answer with the
numbers to change; silence means the recommendation stands.

Data read: `inventory/elections/ceda-scv.json` (113 school board wins, 1995 to 2024),
`inventory/elections/county-svc-water.json` (19 water board wins, 2016 to 2024), the County's
cancelled-election lists for 2018 (`inventory/news/walters-2026-10-03/lavote_cancelled_2018.pdf`),
2020, 2022 and 2024, the Hart and Saugus board pages and SCV Water's directors list
(`inventory/elections/*-board-*.json`, `scv-water-directors-2026-09-30.json`), and the 39 holdings
in the database (read-only query, 3 October).

## The decisions

| # | Question | Evidence | Recommend |
|---|---|---|---|
| 1 | School board term dates | Education Code 5017: a four-year term "commencing on the second Friday in December next succeeding" the election (first Friday before AB 2449, effective 1 January 2019). The County's lists write unexpired terms as "ending 12/20", "12/22", "12/26". The 23 tenure holdings already use `YYYY-12`. | December of the election year to December of the next election for the seat, month precision (`1997-12`), as for the council. Footnote cites 5017. No computed Friday dates. |
| 2 | Water board term dates | SCV Water's own list: "Elected: January 2025, Term Expires: January 2029". Bill Cooper's holding is already `2023-01` to `2027-01`. Castaic Lake Water Agency reorganized at a special meeting on 3 January 2017, after the November 2016 election. | January after the November election, for SCV Water and CLWA alike. CLWA terms end 31 December 2017, howEnded "left" (the agency dissolved), as Colley's and Martin's do. |
| 2b | Plambeck's SCV Water end | Her holding #28219 ends `2022-12`; under #2 her successor took the seat in January 2023. | Change to `2023-01` in the same script. |
| 3 | The move from odd to even years | See the table below. Hart held a 2015 election and moved after it. Saugus skipped 2015 and held 2016. Newhall, Sulphur Springs and Castaic have no 2015 or 2016 row, so their 2011 seats ended in either December 2015 or December 2016. | Hart: 2013 terms to December 2018, 2015 terms to December 2020. Saugus: 2011 terms to December 2016, 2013 terms to December 2018. All others: 2013 terms to December 2018. The one 2011 term affected and not already covered (Denis DeFigueiredo, Sulphur Springs) ends `2016-12?` with howEnded "unknown" and a footnote naming both possibilities. |
| 4 | At large or trustee area | CEDA's area field and the County's lists: Hart in areas by 2015, Saugus by 2016, Newhall, Sulphur Springs and Castaic by 2018 (Castaic lettered A to E). The last at-large contests were Hart 2011, Saugus 2013, Newhall 2013, Sulphur Springs 2013 and Castaic 2009. | `holdingDistrict` and `seatLabel` "Trustee Area 3" only when the winning contest names the area. At-large terms carry neither, even when they ran on into the area years (Newhall's 2013 class, to 2018). |
| 5 | Uncontested seats where the same person reappears | 35 school terms have no recorded next election for the seat (8 more belong to the 2022 class still serving). In 10 cases the same person wins later as the incumbent (Umeck 1997 and 2005, Bryce 2005, de la Cerda 2005, Koscielny 1999, Clegg 2001, Weinstein 2003, DeFigueiredo 2003, Jensen 2009, Messina 2009), which implies 15 unrecorded terms. 12 of those 15 already sit inside tenure holdings. 3 do not: de la Cerda 2009 to 2013, DeFigueiredo 2007 to 2011, Jensen (Hart) 2013 to 2018. Two more are bridged by a district roster: Jensen, Hart Area 2, 2022 to 2026, and Watson, Saugus Area 4, 2024 to 2028 (on the County's 2024 list). | Create the 5 bridging terms, with start and end both "derived" and a footnote saying the seat was not contested. The term before each one ends "reelected". |
| 6 | selectionMethod for a seat filled without a vote | Elections Code 10515: where candidates do not exceed seats, the supervisors appoint, and the person serves "exactly as if elected". The County's lists call this "appointment in lieu of election". | "appointed", with a footnote: "appointed in lieu of election; no contest was held". This keeps the County's wording. A vacancy appointment gets a different footnote. |
| 7 | Uncontested seats where nobody reappears | 23 terms. The next election for the seat is missing or was cancelled, and the County does not name the appointee (e.g. Huffaker and Sansone at Castaic 2003, Chase and Macdonald at Sulphur Springs 2013, Smith and Ellis at Newhall 2013). | End the term at the next election for the seat (December, "derived"), because the seat turned over then by law. howEnded "unknown". Do not infer who held it after. |
| 8 | Tenure holdings that already cover a term | 40 of the 113 school wins and 4 of the 19 water wins fall inside existing tenure holdings (Walters, Koscielny, Mercado-Fortine x2, Clegg, Diaz, Shapiro, Christopher, Messina, Weinstein, Pearson, Solomon, Arrowsmith, Strickland x2, Bryce, Umeck, Smith, Trunkey, Ahuja; Martin x2, Colley, Cooper). The tenures rest on quoted sources and often start before CEDA (Clegg 1989, Umeck 1996). | Skip any term a tenure already covers, as the council script did for the Smyths. Keep the tenures and do not split them. A person can then have one tenure on one board and per-term holdings on another. That is accepted. |
| 9 | Earlier appointments the data only hints at | 15 winners were already the incumbent at their first win, so they were appointed before it: Emmons and Burk (Castaic), Tannehill and Walters (Newhall), Umeck, Olsen and Trunkey (Saugus), Clegg, Hogan and Wigdor (Sulphur Springs), Hanrion, Jensen, Moore and Wilson (Hart). Walters, Umeck and Trunkey are covered by tenures. | Create no holding without a date. Add a footnote on the first derived term ("stood as the incumbent"), and list the other 12 for tenure research. |
| 10 | Mid-term departures with no source | James Webb (Hart Area 4, won 2020): Erin Wilson was the incumbent by November 2024. Cassandra Love (Saugus Area 1, won 2022): Patti Garibay now holds Area 1, and the 2024 list has an unexpired Area 1 term ending 12/26. BJ Atkins (SCV Water Division 3, won 2020): Kenneth Petersen was appointed in September 2022, per the agency's list. | Leave termEnd blank, howEnded "unknown", and add a footnote naming the successor as incumbent. Create Petersen's appointed holding (September 2022 to January 2025, "roster") from the agency's list, but do not assert whose seat it was. |
| 11 | Water board scope | Newhall County Water District: no election data held (CEDA excludes special districts, and the County returns held start in 2016). CLWA: only 2016 (at large and Divisions 1 to 3); its appointed purveyor seats have no data. SCV Water: SB 634 seated a 15-member founding board on 1 January 2018 (5 NCWD, 9 CLWA elected, 1 District 36 appointee). Terms that would have ended in 2018 were extended to 2020, and those ending in 2020 to 2022 (Assembly Local Government analysis). Only Martin, Colley and Plambeck have founding holdings. | NCWD stays tenure-only. The four CLWA 2016 winners (Cooper, Kelly, Gladbach, Pecsi) each get a CLWA term (January 2017 to 31 December 2017, "left") and a "succeeded" SCV Water term from 1 January 2018 to January 2023, their CLWA terms extended by SB 634. The other 8 founding members wait for a source that names them. |
| 12 | Evidence levels | CEDA outcomes are already "roster" on all 113 candidacies (your ruling of 29 September: a compilation lifts to roster, never certified). Water outcomes are already "roster" or "derived" against the agency's list. | startEvidence copies the candidacy's outcomeEvidence. endEvidence is "derived" (the date comes from statute and the next election), or "roster" where a board page gives the end (Hart's termTo, SCV Water's "Term Expires"). Bridging terms (#5) are "derived" at both ends. |
| 13 | howEnded | 113 school terms: 31 won again at a recorded election; 32 expired (24 did not stand again, 8 lost); 13 still serving; 12 bridged (#5); 23 unknown (#7); 2 at the 2015/16 switch (#3). | "reelected" when the same person takes the seat at the next election, contested or bridged. "expired" when they lost or did not stand (there is no "defeated" option, so the footnote says "lost"). "serving" when the term runs past today. "unknown" otherwise. Webb and Love are "unknown" (#10). |
| 14 | People for the winners | 45 winning candidacies (38 people) have no person record, e.g. Steven Tannehill, Lori Macdonald, Matthew Watson, Erik Richardson, Dan Masnada. The council precedent: every elected winner is a public figure and gets a record, public facts only. Four name pairs need your call: Robert N. Jensen, Jr. (Newhall 2005) = Bob Jensen (Hart 2009, 2018)? Philip C. Ellis (Hart 1999; "Philip C. Ellis, Jr." as Hart incumbent 2003) = Philip C. Ellis, Jr. (Newhall 1995, 2009, 2013)? J. Micheal McGrath = John Michael McGrath (Newhall 2005, 2009)? Denis F. De Figueiredo = Denis F. DeFigueiredo (Sulphur Springs 2003, 2011)? | Create all 38. Treat each of the four pairs as one person, since every second appearance is marked incumbent or is the same seat. |
| 15 | Hart Area 2 in 2022 | Hart's own page gives Bob Jensen Area 2, 2022 to 2026. Neither CEDA nor the County's 2022 cancelled list has a Hart Area 2 contest. | Create the holding from Hart's page ("roster"), selectionMethod blank, with a footnote recording the missing contest. Ask Grok Bot to check the County's 2022 Hart contests. |
| 16 | College of the Canyons (Santa Clarita Community College District) | It is not in `ceda-scv.json`, but the raw CEDA files hold 24 trustee wins (1997, 2001, 2003, 2005, 2009, 2011, 2016, 2018, 2020, 2024, by area throughout), and the County lists 2018 Area 1 and 2022 Areas 1 and 5 as cancelled. No SCCCD elections are imported. | Out of scope for this script. Import its elections and candidacies first (entity-first), then reuse these rules. |

### The odd-to-even move, by body (#3)

| Body | Last odd-year row | First even-year evidence | Odd classes ran to |
|---|---|---|---|
| Hart | 2015 (Areas 1, 4) | 2018 (Areas 2, 5; Area 3 cancelled) | 2013 class to Dec 2018; 2015 class to Dec 2020 (Storli re-won 2020, Sturgeon lost) |
| Saugus | 2013 | 2016 (Area 3, incumbent Olsen) | 2011 class to Dec 2016 (Koscielny's sourced tenure ends 2016); 2013 class to Dec 2018 |
| Newhall | 2013 | 2018 (Area 2; Areas 1, 3 and an Area 4 term "ending 12/20" cancelled) | 2013 class to Dec 2018; 2011 class to Dec 2015 or 2016, then 2020 (Areas 4, 5 cancelled) |
| Sulphur Springs | 2013 | 2018 (Areas 3, 4, 5 cancelled) | 2013 class to Dec 2018; 2011 class to Dec 2015 or 2016, then 2020 |
| Castaic Union | 2009 (2011, 2013 uncontested) | 2018 (B, D, E and a C term "ending 12/20" cancelled) | 2013 class to Dec 2018; 2011 class to 2015/16, then 2020 (A, C) |
| SCV Water | (even years from founding) | 2020 | founding terms to Jan 2021 or Jan 2023 (SB 634) |

## Scale

| Body | Wins | Already covered | New from wins | Bridging and roster terms | Holdings to create |
|---|---|---|---|---|---|
| Hart | 33 | 11 | 22 | 2 (Jensen 2013, 2022) | 24 |
| Saugus Union | 25 | 12 | 13 | 2 (de la Cerda 2009, Watson 2024) | 15 |
| Newhall | 21 | 9 | 12 | 0 | 12 |
| Sulphur Springs | 14 | 5 | 9 | 1 (DeFigueiredo 2007) | 10 |
| Castaic Union | 20 | 3 | 17 | 0 | 17 |
| CLWA | 4 | 0 | 4 | 4 succeeded SCV Water terms | 8 |
| SCV Water | 15 | 4 | 11 | 1 (Petersen appointment) | 12 |
| **Total** | **132** | **44** | **88** | **10** | **98** |

The script would also create 38 people, correct 1 holding (#2b) and link 45 candidacies. Under the
alternative to #8 (splitting the tenures), it would instead replace those 23 tenures with per-term holdings, several dozen more in all.

## Appendix: mechanics (no decision needed)

- **Seat model.** At-large years: a term's seat is next filled four years on, in the same class, with
  the shifts in #3. Area years: the same area four years on. Hart's 1995 short term (George Aliano)
  ends in December 1997. In 1995 CEDA gives no "vote for", so seats are CEDA's elected count, as in
  the import.
- **Bridging (#5)** requires the later win to carry CEDA's incumbent flag, or a district roster
  naming the person in that area for that term. A County cancelled list alone never names anyone.
- **Footnotes** per holding: the election and place in the count with votes (from the candidacy),
  the statute for the dates (5017, or the agency's list and SB 634), and any bridging, transition or
  incumbent note. Cite CEDA as a compilation, as the import does.
- **Idempotent**: skip by person, body and start year, and skip any term inside an existing holding
  for the same person and body (the council's `$covered` test, keyed on body as well as office:
  School Board Member #26964 and Water Board Director #27420 are shared offices). Dry run by default.
  Refuse on any winner without a person, any area without a place record, and any bridging term
  whose later win lacks the incumbent flag. Write in one transaction, read back, and log to
  APPLIED.log.
- **Order**: people (#14) first, then candidacy links, then holdings, then the #2b correction.
- **Follow-ups outside this script**: the County's 2013, 2015 and 2016 cancelled lists (they could
  settle #3 and some of the 23 unknowns in #7) and Hart 2022 Area 2 (#15) for Grok Bot; a source
  naming SCV Water's 15 founding directors (#11); SCCCD elections (#16).
