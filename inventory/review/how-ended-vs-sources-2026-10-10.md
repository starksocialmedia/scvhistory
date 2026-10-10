# How a term ended, checked against what the source prints (10 October 2026)

Read-only review by Claude. Nothing in the database, templates or git was changed. Another agent is the single writer tonight and is correcting McGrath (#28455); he is listed here but left to it.

## What was read

- **Field layouts** (config/project): `howEnded` ("How the term ended") is used only by the Office Holding entry type; values checked: `resigned`, `died`, `removed`, and the equivalent `recalled`. The Affiliation type has `affiliationEnded` ("How it ended") with `resigned`, `died` and `dismissed`. No other field in any layout offers a resignation, death or removal value. The fallen officers' assignment fields were not touched (held by brief).
- **Records**: every Office Holding and Affiliation entry, all statuses, read from the database with a read-only `craft exec` (storage/runtime/scratch/howended/dump.php; output dump.json). Affiliations: 30 records, none resigned, died or dismissed. Office holdings: 423 records, of which 43 carry one of the four values (34 resigned, 6 died, 2 recalled, 1 removed). For each, the footnotes, editor's notes, term end and end-evidence fields were read.
- **Review files**: inventory/review/term-endings-2026-10-04.md (and its saved-source folders), for the finding behind each ending it proposed.
- **Sources, read as files** (the words quoted below are from the file, not from a footnote): saved pages under inventory/news/coc-trustees-2026-10-05/, term-endings-2026-10-04/, office-gaps-2026-10-03/, hart-pre1995-2026-10-05/, hart-trustees-2026-10-05/, winkler-2026-10-04/, walters-2026-10-03/, bj-atkins-2026-10-06/, inventory/legacy/fetched/sg062505.htm; PDFs under inventory/sources/legislative-districts-2026-10-04/ (Record of State Senators, read in full text) and inventory/sources/katie-hill-2026-10-06/ (Congressional Record H8727); the Hart roster (Reggie mirror copy, hartschoolboardmembers.htm) and, on Reggie, /mnt/reggie/scvhistory.com/scvhistory/sg091570.htm (the 1970 recall page the roster links).
- **Fetched tonight** (Wayback Machine, generic User-Agent, nothing from scvhistory.com), saved with sha256 in inventory/news/how-ended-2026-10-10/manifest.json: SCVNews, "Patricia Garibay Appointed to Saugus Union School District Board", 27 October 2023; The Signal, "SCV's new water agency elects second vice president", 10 January 2018; The Signal, "New water agency board members paid $228 per meeting", 11 January 2018.
- **Not read**: the scanned governor's proclamations (no text layer; not needed, other sources settle Hill and Runner); the County cancelled-election lists (they bear on how terms began, not how they ended).

## Result

| Class | Count |
|---|---|
| Source prints the ending as done | 39 |
| Source prints only an announcement or intention | 3 (Olsen, Love, McGrath) |
| Source prints something else | 1 (Pecsi) |
| No source cited | 0 |
| Source cannot be read | 0 |
| **Total** | **43** |

All four failures have the ending itself right: for Olsen, Love and Pecsi a source read tonight prints the ending as done, but the record does not cite it. None falls in the held areas (Hart and COC trustee drafts, fallen officers, the disaster comparison, Boston's profile). Wherever a fix would change a date, it is held (listed at the end).

## Failures

### Julie Olsen, Saugus Union (#28481): resigned, 16 August 2020. Announcement only
- Cited source (fn 5): The Signal, 29 July 2020 (saved term-endings-2026-10-04/ss-susd-president-steps-down-2020-07.txt): "Olsen acknowledged her pending resignation"; "I announced that I'll be filing notice to the L.A. County Office of Education that my last day of service on the governing board for SUSD will be on Aug. 16." Written two and a half weeks before the date, about something still to happen.
- A source that prints it done, already saved but not cited: Caleb Lunetta, The Signal, 26 January 2021 (term-endings-2026-10-04/ss-susd-arrowsmith-president-2021-01.txt): Arrowsmith "previously performed the duties of president on an interim basis after former board President Julie Olsen stepped down from the position in August"; "after the resignation of Olsen."
- The record would need: the January 2021 article cited for the resignation having happened (in August 2020), with the 29 July piece kept for her stated last day, worded as what she said she would do ("she said her last day would be 16 August"). Whether to keep 16 August as the end or show August 2020 is a date decision: held.

### Cassandra Nicole Love, Saugus Union (#28473): resigned, 2 October 2023. Announcement only
- Cited source (fn 4): Perry Smith, The Signal, "SUSD board member announces plan to step down," 18 September 2023 (saved term-endings-2026-10-04/ss-love-step-down.txt): "a statement from Love announcing her intention to resign effective Oct. 2"; "Love is stepping down from the district's governing board next month." The footnote's "Patti Garibay was appointed to the seat" has no citation.
- A source that prints it done (fetched tonight, saved how-ended-2026-10-10/scvnews-patricia-garibay-appointed-2023-10-27.html): SCVNews press release, 27 October 2023: "Love, elected to the governing board in 2022, resigned her seat effective Oct. 2 due to an unexpected illness in her family"; the board "selected Patricia Garibay on Wednesday, Oct. 25 to fill the vacant Trustee Area 1 seat previously held by Cassandra Love."
- The record would need: the 27 October 2023 release cited for the resignation and for Garibay's appointment, and the 18 September article described as the announcement. No date change is needed.

### John Michael McGrath, Newhall (#28455): resigned, 8 December 2009. Announcement only (left to tonight's writer)
- Cited source (fn 4): Newhall School District release, 4 November 2009, via SCVTV (saved walters-2026-10-03/scvtv_nsd110409.txt): "He will submit a resignation effective December 8th, creating the open seat." Nothing read tonight prints the resignation as done. The term-endings review's "Brian Walters ... sworn in 8 December 2009" has no quoted source in the review.
- The record would need: either a source printing that he resigned (or that the seat was filled), or howEnded not "resigned" and a note that the district said on 4 November 2009 he would resign effective 8 December. The other agent is correcting this.

### Bill Pecsi, SCV Water (#28421): resigned, January 2018. Source prints something else
- Cited source (fn 4): SCV Water Resolution SCV-20, 20 February 2018 (saved term-endings-2026-10-04/scvwater-resolution-scv-20-pecsi.pdf), read in full: "William Pecsi served on the Castaic Lake Water Agency Board of Directors from December 1998 to January 2018." It gives his years of service only. It does not say how his service ended, and the words "resign" and "SCV Water board" do not appear in it. The record's end evidence is "certified", but the resolution certifies the service dates, not a resignation.
- Sources that print it done (fetched tonight, saved how-ended-2026-10-10/): The Signal, 10 January 2018: "The only other change made to the new board Tuesday was the resignation of longtime board member Bill Pecsi"; "On Tuesday, he resigned as a member of the board"; "In April, Pecsi announced he was moving to Arizona." The Signal, 11 January 2018: "since board member Bill Pecsi resigned this week."
- The record would need: The Signal of 10 January 2018 cited for the resignation, with end evidence "contemporary". The resolution stays as the source for his years of service. Narrowing the end to Tuesday, 9 January 2018 is a date change: held.

## Read from a description

**Pecsi (#28421).** The 4 October term-endings review set howEnded to "resigned" from a search-result snippet of a Signal article (PressReader returned 403 and the review says it found no Wayback capture, so the article was "seen only as a search snippet"). The resolution it did save says nothing about how his service ended. The record then cited the resolution for a resignation that only the snippet supported. The article itself, read tonight from the Wayback Machine, does print the resignation, so the claim stands. But for six days it rested on a description of the source, not on the source. This is not fixed here.

Related, but a different pattern (an announcement read as a completed act, not a description read as the source): Olsen, Love and McGrath. In each, the 4 October review's "Finding" said in the past tense what the cited piece printed only as a plan ("her last day of service was 16 August 2020"; "Cassandra Love resigned, effective 2 October 2023"; "submitted a resignation effective 8 December 2009").

## Confirmed: the source prints the ending as done (39)

Quotes are from the saved file or page named.

| # | Record | Ending | What the source prints |
|---|---|---|---|
| 30407 | Charles L. Lyon, COC | resigned 2024-06-27 | SCVNews, 28 Jun 2024: "Chuck Lyon, representing Trustee Area 1, resigned effective Thursday, June 27" |
| 30367 | Joan W. MacGregor, COC | resigned 2024-08-05 | SCVNews, 16 Sep 2024: "The resignation of Trustee Joan W. MacGregor effective Aug. 5 resulted in a vacant board seat"; COC release, 19 Sep 2024: "The vacancy was created by the recent resignation of Joan MacGregor" |
| 30345 | Michele R. Jenkins, COC | died 2023-02-06 | COC release, 28 Mar 2023: "The vacancy was created by the recent death of Michele Jenkins"; SCVNews, 28 Jun 2024: "Michele Jenkins, who died Feb. 6, 2023" |
| 30311 | James E. Rentz, COC | resigned 1977 | Canyon Call 1976-77: Reiter "replaces Dr. James Rentz who has resigned from the board to move. to Arizona" |
| 30303 | Don Allen, COC | resigned 1974/75 | Canyon Call, Feb 1975: "The recent resignation of trustee Don Allen opened the door for four more candidates to complete his unexpired term" |
| 30295 | Peter F. Huntsinger, COC | resigned 3 June 1982 or 1983 | Boyer ch. 5: "Peter Huntsinger resigned from the district governing board in the middle of the board meeting on June 3" (the year is uncertain: date question, held) |
| 30267 | William G. Bonelli Jr., COC | died 1972-02-22 | The Signal, 23 Jun 1972: "The election was held to fill the vacancy left by the death of Dr. William G. Bonelli"; HM7502 caption: "(Dec. 9, 1922 - Feb. 22, 1972)" |
| 29648 | Michael Shapiro, Newhall | resigned 2016-08-01 | SCVNews, 12 Sep 2016: "Talley replaces Mike Shapiro, who stepped down from the board effective Aug. 1, having moved to another city" |
| 29378 | Katie Hill, U.S. House | resigned 2019-11-03 | Congressional Record H8727, 5 Nov 2019: her letter, "my resignation ... effective November 3, 2019"; the Chair: "in light of the resignation of the gentlewoman from California (Ms. HILL) the whole number of the House is 431" |
| 29365 | Sharon Runner, State Senate | died 2016-07-14 | Record of State Senators, note 189: "Died in office July 14, 2016" |
| 29363 | Steve Knight, State Senate | resigned 2015-01-05 | Record of State Senators, note 112: "Resigned from office January 5, 2015, elected to Congress" |
| 29359 | George Runner, State Senate | resigned 2010-12-21 | Record of State Senators, note 188: "Resigned from office December 21, 2010" |
| 29357 | Pete Knight, State Senate | died 2004-05-07 | Record of State Senators, note 113: "Died in office May 7, 2004" |
| 29190 | Gary Murr, Saugus Union | died June 2005 | The Signal, 25 Jun 2005 (legacy sg062505.htm): "Saugus Union School District board President Gary Murr, who died suddenly this week" |
| 28887 | Michael Owen Lambarth, Castaic Union | resigned 2016-09-01 | SCVNews/KHTS, 9 Sep 2016: "Lambarth gave notice of his resignation on July 21 and was effective September 1. The CUSD Governing Board members appointed Malcomb to the seat on September 8" |
| 28885 | Stacy Dobbs, Castaic Union | resigned 2020 | SCVNews, 2 Oct 2020: "trustee area A, previously filled by Stacy Dobbs before Dobbs resigned" (no date printed) |
| 28881 | Victor Torres, Castaic Union | resigned 2017-02-17 | SCVNews, 1 Mar 2017: "Torres notified ... of his resignation effective Feb. 17. At the Feb. 28 meeting ... appointing Mayreen Burk to fill the vacancy" |
| 28809 | Robert Hall, Hart | resigned 2017-02-01 | SCVNews, 3 Feb 2017: "Hall submitted his resignation, effective February 1, 2017"; the board "voted 3-1 to appoint a new member to fill Robert Hall's remaining two years" |
| 28807 | Chris Fall, Hart | resigned 2013-08-16 | Hart roster: "resigned eff. 8-16-2013"; KHTS, 16 Oct 2013: "Fall resigned ... at the board's Sept. 25 meeting" (the manner agrees; the date conflicts: held) |
| 28805 | Peter Warren, Hart | resigned March 1994 | LA Times, 17 May 1994: "Warren, 36, resigned in March, 105 days after he was elected"; roster: "resigned 4-6-1994" (date disagreement already noted on the record) |
| 28791 | Jim Shuman, Hart | resigned 1978 | Hart roster: "James Putjenter (appointed to complete unexpired term of Shuman, who resigned)" |
| 28785 | Patrick Shaughnessy, Hart | resigned 1978 | Hart roster: "Jim Shuman (appointed to complete the unexpired term of Shaughnessy, who resigned)" |
| 28783 | Kenneth Wullschleger, Hart | resigned 1979-01-23 | Hart roster: "Kenneth C. Wullschleger (resigned eff. 1-23-1979)" |
| 28775 | Carroll Word, Hart | resigned 1974-06-30 | Hart roster: "Carroll E. Word (resigned 6-30-1974)" |
| 28773 | Thomas Hanson, Hart | resigned 1977-05-25 | Hart roster: "Thomas Hanson (reelected 3-1977, resigned 5-25-1977)" |
| 28767 | Edward Duarte, Hart | recalled 1970 | Hart roster: "Edward Duarte (recalled 1970)". Also (not cited) Reggie sg091570.htm, The Signal, 16 Sep 1970: "Curtis Huntsinger and Edward Duarte were recalled from the William S. Hart Board of Trustees in a very dramatic and very close election yesterday" |
| 28763 | Jereann Bowman, Hart | resigned 1969 | Hart roster, 1969 board: "Jereann Bowman (resigned)" |
| 28759 | Earl Schmidt, Hart | resigned 1969 | Hart roster, 1969 board: "Earl Schmidt (resigned)" |
| 28757 | C. R. Huntsinger, Hart | recalled 1970 | Hart roster: "C.R. Huntsinger ( recalled 1970 )"; Reggie sg091570.htm as for Duarte. That page also says the returns "are not official and will not be accepted as final until ... an official canvass in about 10 days" |
| 28731 | Thomas M. Frew Jr., Hart | resigned 1951 | Hart roster, 1951 board: "Thomas M. Frew (resigned)" |
| 28606 | James Webb, Hart | resigned May 2023 | Hart district, 28 Jun 2023: "Webb submitted his resignation to the Los Angeles County Board of Education on May 2, 2023"; the board voted on 17 May "to fill James Webb's remaining two years" |
| 28493 | Stephen Winkler, Saugus Union | removed June 2014 | KHTS via SCVNews, 19 Jul 2014: "Powell replaces Stephen Winkler, who was booted from the board because he didn't live in the district"; "legal action against Winkler in Los Angeles County Superior Court, which was granted in June" |
| 28237 | Laura Arrowsmith, Saugus Union | resigned Sep 2022 | The Signal, 14 Sep 2022: "Superintendent Colleen Hawkins confirmed Arrowsmith resigned on Wednesday" |
| 28427 | Jerry Gladbach, SCV Water | died 2022-07-13 | SCV Water release, 18 Jul 2022: "SCV Water is sad to report the passing of Board Vice President Jerry Gladbach on Wednesday" |
| 28415 | BJ Atkins, SCV Water | resigned 2022-07-20 | The Signal, 13 Jul 2022, is the announcement ("It becomes effective midnight on the 20th"). Printed as done in: the approved minutes of 19 Jul 2022, "the Division 3 seat vacated by Director Atkins"; The Signal, 30 Aug 2022, "Atkins, who resigned because he moved"; the resolution, service "to ... July 19, 2022" (the 19 or 20 July question is held) |
| 28235 | Paul Strickland, Hart | resigned 2013-05-01 | SCVNews, 6 Jun 2013: "The seat was vacated May 1, when former board member Paul Strickland vacated his spot"; 10 Sep 2013: "Paul Strickland, who resigned effective May 1" |
| 28231 | Douglas Bryce, Saugus Union | resigned 2014-09-30 | KHTS via SCVNews, 19 Nov 2014: "Bryce stepped down at the end of September" (the September release, fn 1, is the announcement: "he will be resigning ... effective Sept. 30") |
| 28223 | Ed Colley, SCV Water | resigned 2024-08-07 | SCV Water release: "resigned his seat effective Wednesday, Aug. 7" |
| 26978 | Buck McKeon, Hart | resigned 1987-12-07 | Hart roster: "McKeon (resigned eff. 12-7-1987 upon election to Santa Clarita City Council)"; "William S. Dinsenbacher (appointed 12-7-1987 to complete McKeon's unexpired term)" |

Four confirmed records carry an announcement in one footnote and the completed act in another: Hill (fn 5 holds both), Atkins, Bryce and Strickland. Each is confirmed on the later or official footnote.

## Held (any fix is a date change)

- Olsen #28481: 16 August 2020 rests only on her stated plan; the done source gives "August".
- Pecsi #28421: the day, Tuesday 9 January 2018, from The Signal of 10 January 2018.
- Atkins #28415: 20 July (his words) against 19 July (the resolution and the minutes of 19 July).
- Chris Fall #28807: 16 August 2013 (roster) against the 25 September meeting (KHTS, 16 Oct 2013).
- Peter Warren #28805: March (LA Times) against 6 April 1994 (roster); already footnoted.
- Peter F. Huntsinger #30295: 1982 or 1983; already footnoted.
- Duarte #28767 and C. R. Huntsinger #28757: the recall date, 15 September 1970 (Reggie sg091570.htm), is more precise than the "1970" recorded.
- Footnote publication dates that do not match the saved page: Bryce fn 1 and fn 2 say 5 September 2014, the page reads "Thursday, Sep 4, 2014"; Colley fn 1 and fn 2 say 9 August 2024, the page reads "Thursday, Aug 8, 2024".

## Side note outside the selection

The Signal of 30 August 2022 (term-endings-2026-10-04/wb-signal-water-appoints-division-3.html): "Mortensen resigned from his position after being convicted of misdemeanor domestic violence earlier this year." No office holding exists for Dan Mortensen. Nineteen holdings carry "left" (left for another office). Some of those people also resigned, but the value claims something different and was not checked here.
