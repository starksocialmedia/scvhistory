# LA County RR/CC cancelled elections (appointment in lieu of election), 2013, 2015, 2016

Researched 2026-10-03 PT (Grok Bot, read-only). Every source document came from current lavote.gov (fetched 2026-10-03); the Wayback Machine was not needed for any document. No repo was modified. Downloaded PDFs are in `/workspace/review/cancelled-elections/<year>/` under their original filenames. Structured data: `/workspace/review/cancelled-elections-2013-2015-2016.json`.

## Summary

**Our archive's existing 2020/2022/2024 lists:** I could not find them on the box. A search of /workspace/scvhistory, /workspace/scvhistory-data and /workspace/cc-review for "in lieu", "cancel" and "appointed" turned up only obituary text ("in lieu of flowers"); there are no election files. I could not match their format, so this capture follows RR/CC's own structure. For reference, the current RR/CC equivalents are linked on the same lavote.gov page as `11032020_cancelled-elections.pdf`, `11082022_final-list-of-cancelled-elections.pdf` and `cancelled-elections-november-2024.pdf`.

**How the lists work.** For each election, RR/CC published a one-to-three-page "Final List of Cancelled Elections (Appointment in Lieu of Election Due to Insufficiency of Candidates)". It names jurisdictions and trustee areas/divisions but **not the appointees**. The appointee names come from the companion RR/CC "Final List of Qualified Candidates Whose Name Will Not Appear on the Ballot" for the same election, matched contest by contest. Every not-on-ballot contest matched a cancelled-list line, and the parsed candidate counts match the totals printed in each PDF. Both lists are full countywide lists for the jurisdictions consolidated with the RR/CC election; jurisdictions that ran their own elections are not covered. The JSON leaves out candidate addresses, phone numbers and emails.

### 2013, March 5 (Consolidated Municipal & Special)

**No list exists.** No cancelled-elections list was published. The lavote.gov past-election page lists no such document for this election, and Wayback CDX for lavote.net/documents/_03052013* shows none. Every contest on the RR/CC Final List of Qualified Candidates had more candidates than seats (Bell 6 for 2, Cudahy 6 for 2, Cudahy U/T 3 for 1, LACCD 2/2/4 for 1 each), and that list is identical to the on-ballot list. No SCV jurisdictions.

### 2013, November 5 (Local and Municipal Consolidated Elections)

**Found.** Source: https://www.lavote.gov/Documents/Election_Info/11052013_canc_elec.pdf (appointee names: https://www.lavote.gov/Documents/Election_Info/11052013_final_list_of_qualified_candidates_whose_name_will_not_appear_on_the_ballot.pdf). 57 entries (57 appointment-in-lieu, 0 no-candidate), 86 appointees named.

SCV entries:

- **Castaic Union**, Governing Board Member (3 seat(s)): Susan M. Christopher (inc.), Laura L. Pearson (inc.), Victor M. Torres (inc.).
- **Santa Clarita Community College**, Governing Board Member, Seat No. 1 (1 seat(s)): Michael D. Berger (inc.).
- **Santa Clarita Community College**, Governing Board Member, Seat No. 3 (1 seat(s)): Joan Whaling Mac Gregor (inc.).
- **Santa Clarita Community College**, Governing Board Member, Seat No. 5 UNEXPIRED TERM ENDING 12/03/2015 (candidate list heading: SPECIAL ELECTION) (1 seat(s)): Steven D. Zimmer (inc.).
- **William S. Hart Union High**, Governing Board Member (3 seat(s)): Chris A. Fall (inc.), Bob Jensen (inc.), Joe Messina (inc.). At-large election (3 seats) before trustee areas. Chris A. Fall (listed as Appointed Incumbent) resigned from the board after filing; per KHTS (Oct 16, 2013), the board chose Robert Hall on a 3-1 vote to fill Fall's seat for the four-year term, to be sworn in Dec 11, 2013 with Messina and Jensen. Source: https://www.hometownstation.com/santa-clarita-news/hart-district-selects-robert-hall-to-fill-chris-falls-seat-38430 (fetched 2026-10-03). The exact resignation date and the board action date are NEEDS_VERIFICATION (board minutes).

SCV-adjacent (outside the core list; your call): Acton-Agua Dulce Unified (Mark W. Distaso, Ed Porter, Matthew J. Ridenour); Gorman Joint (Julianne C. Ralphs); Green Valley (Jeane M. Sargent).

Notes: lavote.gov links this PDF as "Tentative Cancelled Election Report", but the document is titled "FINAL LIST". San Marino (city) is marked "Received written confirmation of cancellation on 8/13/13. Resolution still pending." Gorman Joint is "Shared with Kern County". 2013 was an odd-year school election: Hart was still at-large (3 seats).

### 2015, March 3 (Consolidated Elections)

**No list exists.** No cancelled-elections list was published. The lavote.gov past-election page lists none, and Wayback CDX for lavote.net/Documents/Election_Info/03032015* shows none. All 8 contests on the RR/CC Final List of Qualified Candidates had more candidates than seats (42 candidates). No SCV jurisdictions.

### 2015, November 3 (Local and Municipal Consolidated Elections)

**Found.** Source: https://www.lavote.gov/Documents/Election_Info/11032015-CancelledElections.pdf (appointee names: https://www.lavote.gov/Documents/Election_Info/11032015_CandidatesNotOnBallot.pdf). 73 entries (73 appointment-in-lieu, 0 no-candidate), 102 appointees named.

SCV entries:

- **Castaic Union School District**, GOVERNING BOARD MEMBER (2 seat(s)): Stacy Dobbs, Michael Owen Lambarth.
- **Newhall School District**, GOV BD MEMBER TR AREA 4 (1 seat(s)): Michael R. Shapiro (inc.).
- **Newhall School District**, GOV BD MEMBER TR AREA 5 (1 seat(s)): Sue Solomon (inc.).
- **Sulphur Springs Union School**, GOV BD MEMBER TR AREA 1 (1 seat(s)): Rochelle "Shelley" Weinstein (inc.).
- **Sulphur Springs Union School**, GOV BD MEMBER TR AREA 2 (1 seat(s)): Denis F. Defigueiredo (inc.).

SCV-adjacent (outside the core list; your call): Acton-Agua Dulce Unified School District (Larry H. Layton, Michael Fox); Gorman Joint School District (Patricia A. Edwards, Steven C. Sonder); Green Valley County Water District (Robert E. Garth, Jeffrey A. McCracken).

Notes: Hart district Trustee Areas 1 and 4 and Newhall County Water District are on the RR/CC Final List of Qualified Candidates but not on the not-on-ballot list, so they were on the 2015 ballot and are not cancellations. Cities Artesia, Bell Gardens and Duarte cancelled under EC 10229; RR/CC does not name their appointees.

### 2016, June 7 (Presidential Primary)

**Found.** Source: https://www.lavote.gov/Documents/Election_Info/06072016_Final-List-Cancelled-Elections.pdf (appointee names: https://www.lavote.gov/Documents/Election_Info/06072016_Final-List-of-Qualified-Candidates-Not-Appear-on-Ballot.pdf). 37 entries (33 appointment-in-lieu, 4 no-candidate), 134 appointees named.

SCV entries:

- **Democratic Party County Central Committee**, Assembly District 38 (38TH DISTRICT - DEM) (7 seat(s)): Stacy L. Fortner, Michelle H. Elmer, Michael R. Kulka (inc.), Patti Sulpizio, Lynne Plambeck (inc.), A. Lysa Simon. Six candidates for 7 seats. This is a party committee, not a public office, and AD 38 being an SCV district (2011 lines) is NEEDS_VERIFICATION.
- **Central Committee (Peace and Freedom Party)**, 36th, 38th, 41st, 44th, 45th, 48th, 49th, 52nd, 55th, 57th, 58th, 63rd and 66th Districts: cancelled for **no candidates** (includes the 38th AD).

Notes: Only party offices were cancelled (county central committees, Peace and Freedom central committees, Green Party county councils); no public offices. The PDF has no usable text layer, so the list was transcribed from the rendered page image. Each district was then matched to the not-on-ballot list (33 of 33 contests matched). That list also carries unopposed Superior Court judge offices, which are not on the cancelled list and are not recorded as entries.

### 2016, November 8 (General Election)

**Found.** Source: https://www.lavote.gov/Documents/Election_Info/11082016-Cancelled-Elections.pdf (appointee names: https://www.lavote.gov/Documents/Election_Info/11082016-final-list-qualified-candidates-who-will-not-appear-on-the-ballot.pdf). 19 entries (18 appointment-in-lieu, 1 no-candidate), 18 appointees named.

SCV entries:

- **Santa Clarita Community College District**, GOV BD MEMBER TR AREA 003 (1 seat(s)): Steven D. Zimmer (inc.).
- **Saugus Union School District**, GOV BD MEMBER TR AREA 004 (1 seat(s)): David C. Powell (inc.).
- **Saugus Union School District**, TRUSTEE 005 TERM ENDS 12/18 (1 seat(s)): Chris Trunkey (inc.).

Notes: Castaic Lake Water Agency, the Santa Clarita City Council, other SCCCD trustee areas and other Saugus Union areas are on the RR/CC Final List of Qualified Candidates but not on the not-on-ballot list, so they were on the ballot. Alhambra USD (First and Second Districts) and Santa Monica-Malibu USD are on the cancelled list but appear in neither RR/CC candidate list, so their appointees are not named. Foothill MWD Division 3 was cancelled for no candidates.

## What would complete the record

- NEEDS_VERIFICATION: BOS Statements of Proceedings formally making the 2013/2015/2016 appointments in lieu were not located. Granicus (lacounty.granicus.com view_id=1) has SOP links only for a few special meetings in those years, and searches of file.lacounty.gov/SDSInter/bos/sop found none. To get them: search bos.lacounty.gov Statements of Proceedings for Aug-Oct 2013/2015/2016 meetings (item "In Lieu of Election"), or ask the BOS Executive Office.
- NEEDS_VERIFICATION: Entries with no appointees (Point Dume CSD, Miraleste and Ridgecrest Ranchos R&P, cities San Marino 2013 / Artesia, Bell Gardens, Duarte 2015, West Valley CWD 2015, Kinneloa Div 5 2015, Palm Ranch Div 4 2013, Alhambra USD and Santa Monica-Malibu USD 2016, plus the no-candidate contests): the RR/CC documents fetched do not name the appointees. Cities appoint under EC 10229, so their names come from city clerk records.
- NEEDS_VERIFICATION: Seats only partly covered by candidates (Wilsona 2013 2 of 3; Green Valley CWD 2013 1 of 3; several 2016 party committees): who filled the remaining seats is not named.
- NEEDS_VERIFICATION: Hart 2013: the exact date of Chris Fall's resignation and the board vote for Robert Hall (Hart board minutes).
- NEEDS_VERIFICATION: SCV relevance of the June 2016 38th AD Democratic County Central Committee (2011 AD lines).
- NEEDS_VERIFICATION: Nov 2013: lavote.gov links the PDF as "Tentative Cancelled Election Report", but the document itself is titled "FINAL LIST", and San Marino is marked "Resolution still pending".

## Documents downloaded

| Election | File | Pages | Role | Source URL |
|---|---|---|---|---|
| 2013-03-05 | `cancelled-elections/2013/_03052013_candidate_information_cons_final_list_qual_cand.pdf` | 5 | evidence_no_cancellations | https://www.lavote.gov/documents/_03052013_candidate_information_cons_final_list_qual_cand.pdf |
| 2013-03-05 | `cancelled-elections/2013/_03052013_candidate_information_cons_final_list_qual_cand_on_blt.pdf` | 5 | evidence_no_cancellations | https://www.lavote.gov/documents/_03052013_candidate_information_cons_final_list_qual_cand_on_blt.pdf |
| 2013-11-05 | `cancelled-elections/2013/11052013_canc_elec.pdf` | 2 | cancelled_list | https://www.lavote.gov/Documents/Election_Info/11052013_canc_elec.pdf |
| 2013-11-05 | `cancelled-elections/2013/11052013_final_list_of_qualified_candidates_whose_name_will_not_appear_on_the_ballot.pdf` | 21 | appointee_names | https://www.lavote.gov/Documents/Election_Info/11052013_final_list_of_qualified_candidates_whose_name_will_not_appear_on_the_ballot.pdf |
| 2013-11-05 | `cancelled-elections/2013/11052013_final_list_of_qualified_candidates_to_appear_on_the_ballot.pdf` | 88 | context | https://www.lavote.gov/Documents/Election_Info/11052013_final_list_of_qualified_candidates_to_appear_on_the_ballot.pdf |
| 2013-11-05 | `cancelled-elections/2013/11052013_extension_of_nomination_period.pdf` | 2 | context | https://www.lavote.gov/Documents/Election_Info/11052013_extension_of_nomination_period.pdf |
| 2015-03-03 | `cancelled-elections/2015/03032015-FinalCandidateList.pdf` | 8 | evidence_no_cancellations | https://www.lavote.gov/Documents/Election_Info/03032015-FinalCandidateList.pdf |
| 2015-03-03 | `cancelled-elections/2015/03032015-FinalCandidateListBallot.pdf` | 8 | evidence_no_cancellations | https://www.lavote.gov/Documents/Election_Info/03032015-FinalCandidateListBallot.pdf |
| 2015-11-03 | `cancelled-elections/2015/11032015-CancelledElections.pdf` | 3 | cancelled_list | https://www.lavote.gov/Documents/Election_Info/11032015-CancelledElections.pdf |
| 2015-11-03 | `cancelled-elections/2015/11032015_CandidatesNotOnBallot.pdf` | 26 | appointee_names | https://www.lavote.gov/Documents/Election_Info/11032015_CandidatesNotOnBallot.pdf |
| 2015-11-03 | `cancelled-elections/2015/11032015_Candidates-Final.pdf` | 94 | context | https://www.lavote.gov/Documents/Election_Info/11032015_Candidates-Final.pdf |
| 2015-11-03 | `cancelled-elections/2015/11032015_ExtCandidateFiling.pdf` | 2 | context | https://www.lavote.gov/Documents/Election_Info/11032015_ExtCandidateFiling.pdf |
| 2016-06-07 | `cancelled-elections/2016/06072016_Final-List-Cancelled-Elections.pdf` | 1 | cancelled_list | https://www.lavote.gov/Documents/Election_Info/06072016_Final-List-Cancelled-Elections.pdf |
| 2016-06-07 | `cancelled-elections/2016/06072016_Final-List-of-Qualified-Candidates-Not-Appear-on-Ballot.pdf` | 80 | appointee_names | https://www.lavote.gov/Documents/Election_Info/06072016_Final-List-of-Qualified-Candidates-Not-Appear-on-Ballot.pdf |
| 2016-11-08 | `cancelled-elections/2016/11082016-Cancelled-Elections.pdf` | 1 | cancelled_list | https://www.lavote.gov/Documents/Election_Info/11082016-Cancelled-Elections.pdf |
| 2016-11-08 | `cancelled-elections/2016/11082016-final-list-qualified-candidates-who-will-not-appear-on-the-ballot.pdf` | 6 | appointee_names | https://www.lavote.gov/Documents/Election_Info/11082016-final-list-qualified-candidates-who-will-not-appear-on-the-ballot.pdf |
| 2016-11-08 | `cancelled-elections/2016/11082016-final-list-qualified-candidates.pdf` | 66 | context | https://www.lavote.gov/Documents/Election_Info/11082016-final-list-qualified-candidates.pdf |

All were fetched 2026-10-03 PT; none came from Wayback. Wayback CDX was queried only to confirm that no March 2013 or March 2015 cancelled-list document ever existed on lavote.net. Index page: https://www.lavote.gov/home/voting-elections/current-elections/election-results/past-election-info

## All entries

In the tables below, names are title-cased from the all-caps RR/CC text; the JSON keeps the exact source capitalization.

### 2013-11-05

| Jurisdiction | Office / seat | Seats | Appointees (* = incumbent per RR/CC) | SCV | Note |
|---|---|---|---|---|---|
| Acton-Agua Dulce Unified | Governing Board Member | 3 | *Mark W. Distaso; *Ed Porter; *Matthew J. Ridenour | adjacent |  |
| Bonita Unified | Governing Board Member | 2 | *Charles (Chuck) Coyne; *Patti A. Latourelle |  |  |
| Castaic Union | Governing Board Member | 3 | *Susan M. Christopher; *Laura L. Pearson; *Victor M. Torres | yes |  |
| Centinela Valley Union High | Governing Board Member, Trustee Area No. 1 | 1 | *Maritza R. Molina |  |  |
| Centinela Valley Union High | Governing Board Member, Trustee Area No. 2 | 1 | *Hugo M. Rojas II |  |  |
| Centinela Valley Union High | Governing Board Member, Trustee Area No. 5 | 1 | *Rocio Carmen Pizano |  |  |
| Citrus Community College | Governing Board Member, Trustee Area No. 3 | 1 | *Edward C. Ortell |  |  |
| Compton Community College | Governing Board Member, Trustee Area No. 1 | 1 | Andres Ramos |  |  |
| Compton Community College | Governing Board Member, Trustee Area No. 4 | 1 | *Deborah Sims Le Blanc |  |  |
| Covina-Valley Unified | Governing Board Member | 2 | *Charles M. Kemp; *Richard M. White |  |  |
| Gorman Joint | Governing Board Member | 1 | *Julianne C. Ralphs | adjacent |  |
| Hughes-Elizabeth Lakes Union | Governing Board Member | 3 | *Melanie Dohn; *Lola J. Skelton; *Jim Walker |  |  |
| Lawndale | Governing Board Member | 3 | *Bonnie J. Coronado; *Ann M. Phillips; *Shirley Rudolph |  |  |
| Los Nietos | Governing Board Member | 3 | *Art Escobedo; *Marisa Bribiescas Hernandez; *Silvia R. Monge |  |  |
| Mt. San Antonio Community College | Governing Board Member, Trustee Area No. 2 | 1 | *David K. Hall |  |  |
| Mt. San Antonio Community College | Governing Board Member, Trustee Area No. 6 | 1 | *Judy Chen Haggerty |  |  |
| Palos Verdes Peninsula Unified | Governing Board Member | 3 | *Anthony Collatos; *Barbara Lucky; *Malcolm S. Sharp |  |  |
| Paramount Unified | Governing Board Member | 3 | *Sonya S. Cuellar; *Alicia M. Linden Anderson; *Tony Pena |  |  |
| Pasadena Area Community College | Governing Board Member, Trustee Area No. 3 | 1 | *Berlinda J. Brown |  |  |
| Pasadena Area Community College | Governing Board Member, Trustee Area No. 5 | 1 | *Linda S. Wah |  |  |
| Pasadena Area Community College | Governing Board Member, Trustee Area No. 7 | 1 | *Tony Fellow |  |  |
| Rio Hondo Community College | Governing Board Member, Trustee Area No. 1 | 1 | *Norma Edith Garcia |  |  |
| Rio Hondo Community College | Governing Board Member, Trustee Area No. 5 | 1 | *Madeline R. Shapiro |  |  |
| Rosemead | Governing Board Member | 3 | *Rhonda Harmon; *Dennis McDonald; John Quintanilla |  |  |
| Santa Clarita Community College | Governing Board Member, Seat No. 1 | 1 | *Michael D. Berger | yes |  |
| Santa Clarita Community College | Governing Board Member, Seat No. 3 | 1 | *Joan Whaling Mac Gregor | yes |  |
| Santa Clarita Community College | Governing Board Member, Seat No. 5 UNEXPIRED TERM ENDING 12/03/2015 (candidate list heading: SPECIAL ELECTION) | 1 | *Steven D. Zimmer | yes |  |
| Temple City Unified | Governing Board Member | 2 | Vinson G. Bell; John Pomeroy |  |  |
| Whittier City | Governing Board Member | 3 | *Efrain Aceves; *Ken Henderson; *Linda L.A. Small |  |  |
| Whittier Union High | Governing Board Member | 3 | *Leighton M. Anderson; *Jeffrey S. Baird; *Russell A. Castaneda-Calleros |  |  |
| William S. Hart Union High | Governing Board Member | 3 | *Chris A. Fall; *Bob Jensen; *Joe Messina | yes | At-large election (3 seats) before trustee areas. Chris A. Fall (listed as Appointed Incumbent) resigned from the board after filing; per KHTS (Oct 16, 2013)... |
| Wilsona | Governing Board Member | 3 | *Diana Lee Callari; *Starsea Mendelson |  | Fewer candidates (2) than seats (3); remaining seat(s) to be filled by appointment (not named in RR/CC documents). NEEDS_VERIFICATION |
| Wiseburn | Governing Board Member | 3 | *Roger Banuelos; *Nelson Edward Martinez; *Israel A. Mora |  |  |
| Point Dume |  |  | (not named) |  | Named on the RR/CC cancelled list, but no matching contest/candidates on the RR/CC list of candidates whose names will not appear on the ballot; appointee(s)... |
| Walnut Valley | Member, Board of Directors, Division 2 | 1 | *Edwin Hilden |  |  |
| Walnut Valley | Member, Board of Directors, Division 3 | 1 | *Barbara A. Carrera |  |  |
| Green Valley | Member, Board of Directors | 3 | *Jeane M. Sargent | adjacent | Fewer candidates (1) than seats (3); remaining seat(s) to be filled by appointment (not named in RR/CC documents). NEEDS_VERIFICATION |
| La Puente Valley | Member, Board of Directors | 3 | *Charlie Aguirre; *John P. Escalera; *Henry Hernandez |  |  |
| Palmdale Water | Member, Board of Directors, Division 2 | 1 | Joe Estes |  |  |
| Palmdale Water | Member, Board of Directors, Division 5 | 1 | Vincent J. Dino |  |  |
| Rowland Water | Member, Board of Directors, Division 5 | 1 | *Szu Pei Lu-Yang |  |  |
| Rowland Water | Member, Board of Directors, Division 3 | 1 | *John Edward Bellah |  |  |
| Rowland Water | Member, Board of Directors, Division 4 | 1 | *Robert Lewis |  |  |
| Sativa-Los Angeles | Member, Board of Directors UNEXPIRED TERM | 1 | *Jose Torres |  |  |
| West Valley | Member, Board of Directors | 2 | Nancy J. Dillon; *Jim Hoerricks |  |  |
| Kinneloa | Member, Board of Directors, Division 2 | 1 | *Frank J. Griffith |  |  |
| La Canada | Member, Board of Directors, Division 1 | 1 | *Richard H. Myers, Jr. |  |  |
| La Canada | Member, Board of Directors, Division 4 | 1 | *Sheree R. Butts |  |  |
| Littlerock Creek | Member, Board of Directors | 3 | *Lynn W. Burns; *Barbara L. Hogan; *John Peter Tenerelli |  |  |
| Palm Ranch | Member, Board of Directors, Division 2 | 1 | *Brett M. Valasek |  |  |
| Palm Ranch | Division 2 and 4 (area/division 4) |  | (not named) |  | Listed as cancelled, but no candidate for this area/division on the RR/CC not-on-ballot list (likely no candidate filed; appointee not named). NEEDS_VERIFICA... |
| South Montebello | Member, Board of Directors, Division 1 | 1 | *Robert E. Brown |  |  |
| South Montebello | Member, Board of Directors, Division 2 | 1 | *Annette Sanchez |  |  |
| Palos Verdes Library | Member, Board of Trustees | 2 | Kay Jue; Kingston Wong |  |  |
| Miraleste Recreation and Park |  |  | (not named) |  | Named on the RR/CC cancelled list, but no matching contest/candidates on the RR/CC list of candidates whose names will not appear on the ballot; appointee(s)... |
| Ridgecrest Ranchos Recreation and Park |  |  | (not named) |  | Named on the RR/CC cancelled list, but no matching contest/candidates on the RR/CC list of candidates whose names will not appear on the ballot; appointee(s)... |
| San Marino | ** Received written confirmation of cancellation on 8/13/13. Resolution still pending. |  | (not named) |  | Named on the RR/CC cancelled list, but no matching contest/candidates on the RR/CC list of candidates whose names will not appear on the ballot; appointee(s)... |

### 2015-11-03

| Jurisdiction | Office / seat | Seats | Appointees (* = incumbent per RR/CC) | SCV | Note |
|---|---|---|---|---|---|
| Acton-Agua Dulce Unified School District | GOVERNING BOARD MEMBER | 2 | *Larry H. Layton; *Michael Fox | adjacent |  |
| Bonita Unified School District | GOVERNING BOARD MEMBER | 3 | *Glenn A. Creiman; *Diane M. Koach; *James K. Elliot |  |  |
| Castaic Union School District | GOVERNING BOARD MEMBER | 2 | Stacy Dobbs; Michael Owen Lambarth | yes |  |
| Charter Oak Unified School District | GOVERNING BOARD MEMBER | 3 | *Joseph Probst; *Brian R. Akers; Jeanette V. Flores |  |  |
| Citrus Community College District | GOV BD MEMBER TR AREA 2 | 1 | *Sue Keith |  |  |
| Citrus Community College District | GOV BD MEMBER TR AREA 4 | 1 | *Patricia A. Rasmussen |  |  |
| Claremont Unified School District | GOVERNING BOARD MEMBER | 2 | *Hilary Laconte; Elizabeth E. Bingham |  |  |
| Compton Community College District | GOV BD MEMBER TR AREA 2 | 1 | *Leslie A. Irving |  |  |
| Compton Community College District | GOV BD MEMBER TR AREA 3 | 1 | *Sonia Lopez |  |  |
| Downey Unified School District | GOV BD MEMBER TR AREA 2 | 1 | *Tod M. Corrin |  |  |
| East Whittier City School District | GOVERNING BOARD MEMBER | 2 | *Carlos Aparicio; *Dimitri Elbling |  |  |
| Eastside Union School District | GOV BD MEMBER TR AREA 2 | 1 | *Peggy W. Foster |  |  |
| El Camino Community College District | GOV BD MEMBER TR AREA 1 | 1 | *Kenneth A. Brown |  |  |
| El Camino Community College District | GOV BD MEMBER TR AREA 3 | 1 | *William James Beverly |  |  |
| El Camino Community College District | GOV BD MEMBER TR AREA 4 | 1 | *Mary E. Combs |  |  |
| Gorman Joint School District | GOVERNING BOARD MEMBER | 2 | *Patricia A. Edwards; *Steven C. Sonder | adjacent |  |
| Hawthorne School District | GOVERNING BOARD MEMBER | 2 | *Cristina Chiappe; *Alexandre Teixeira Monteiro |  |  |
| Hughes-Elizabeth Lakes Union School District | GOVERNING BOARD MEMBER | 2 | *Mary Wall; *Jason Robert Nuesca |  |  |
| La Canada Unified School District | GOVERNING BOARD MEMBER | 2 | Brent A. Kuszyk; *Ellen Shewfelt Multari |  |  |
| Lawndale School District | GOVERNING BOARD MEMBER | 2 | *Cathy Burris; *Shirley A. Bennett |  |  |
| Manhattan Beach Unified School District | GOVERNING BOARD MEMBER | 2 | *Karen A. Komatinsky; *Bill Fournell |  |  |
| Monrovia Unified School District | GOVERNING BOARD MEMBER | 3 | *Ed Gililland; *Rob Hammond; *Bryan Wong |  |  |
| Mt. San Antonio Community College | GOV BD MEMBER TR AREA 7 | 1 | *Manuel Baca |  |  |
| Newhall School District | GOV BD MEMBER TR AREA 4 | 1 | *Michael R. Shapiro | yes |  |
| Newhall School District | GOV BD MEMBER TR AREA 5 | 1 | *Sue Solomon | yes |  |
| Paramount Unified School District | GOVERNING BOARD MEMBER | 2 | *Vivian A. Hansen; *Linda Garcia |  |  |
| Pasadena Area Community College | GOV BD MEMBER TR AREA 6 | 1 | *John H. Martin |  |  |
| Pomona Unified School District | GOV BD MEMBER TR AREA 2 | 1 | *Jason A. Rothman |  |  |
| Pomona Unified School District | GOV BD MEMBER TR AREA 3 | 1 | *Frank Carlos Guzman |  |  |
| Rio Hondo Community College District | GOV BD MEMBER TR AREA 4 | 1 | *Gary Mendez |  |  |
| Rosemead School District | GOVERNING BOARD MEMBER | 2 | *Ron Esquivel; *Randy C. Cantrell |  |  |
| San Gabriel Unified School District | GBM TERM ENDS 12/17 | 1 | *Cristina Alvarado |  |  |
| San Gabriel Unified School District | GOVERNING BOARD MEMBER | 2 | Cheryl Wasser Shellhart; *Andrew L. Ammon |  |  |
| San Marino Unified School District | GOVERNING BOARD MEMBER | 2 | *Lisa Hinchliffe Link; *Chris Norgaard |  |  |
| South Whittier School District | GBM TERM ENDS 12/17 | 1 | *Francisco "Javi" Santana |  |  |
| Sulphur Springs Union School | GOV BD MEMBER TR AREA 1 | 1 | *Rochelle "Shelley" Weinstein | yes |  |
| Sulphur Springs Union School | GOV BD MEMBER TR AREA 2 | 1 | *Denis F. Defigueiredo | yes |  |
| Westside Union School | GOVERNING BOARD MEMBER | 2 | Patricia K. Shaw; *Steven P. Demarzio |  |  |
| Whittier City School | GOVERNING BOARD MEMBER | 2 | *Cecilia Ruiz Perez; *Irella S. Perez |  |  |
| Whittier Union High School District | GOVERNING BOARD MEMBER | 2 | *Ralph S. Pacheco; *Tim Schneider |  |  |
| Wilsona School District | GBM TERM ENDS 12/17 | 2 | Robert Harris; Anne E. Misicka |  |  |
| Wilsona School District | GOVERNING BOARD MEMBER | 2 | Vladimir E. Gomez; *Victoria L. Green |  |  |
| Wiseburn Unified School District | GOVERNING BOARD MEMBER | 2 | *Neil E. Goldman; *Joanne L. Kaneda |  |  |
| Crescenta Valley Water District | BOARD OF DIRECTORS | 2 | *Michael L. Claessens; *Judy Tejeda |  |  |
| Green Valley County Water District | BOARD OF DIRECTORS | 2 | *Robert E. Garth; *Jeffrey A. McCracken | adjacent |  |
| La Habra Heights County Water District | BOARD OF DIRECTORS | 3 | *Pamela McVicar; *Robert C. Wilson; *Mark Perumean |  |  |
| La Puente Valley County Water District | BOARD OF DIRECTORS | 2 | *David Hastings; *William R. Rojas |  |  |
| Rowland Water District | BOARD OF DIRECTORS DIV 1 | 1 | *Teresa Pauline Rios |  |  |
| Rowland Water District | BOARD OF DIRECTORS DIV 2 | 1 | *Anthony John Lima |  |  |
| Valley County Water District | BOARD OF DIRECTORS | 2 | *Paul C. Hernandez; *Margarita R. Vargas |  |  |
| West Valley County Water District |  |  | (not named) |  | Named on the RR/CC cancelled list, but no matching contest/candidates on the RR/CC list of candidates whose names will not appear on the ballot; appointee(s)... |
| Point Dume |  |  | (not named) |  | Named on the RR/CC cancelled list, but no matching contest/candidates on the RR/CC list of candidates whose names will not appear on the ballot; appointee(s)... |
| Walnut Valley District | BOARD OF DIRECTORS DIV 1 | 1 | *Allen L. Wu |  |  |
| Walnut Valley District | BOARD OF DIRECTORS DIV 4 | 1 | *Ted Ebenkamp |  |  |
| Walnut Valley District | BOARD OF DIRECTORS DIV 5 | 1 | *Scarlett Kwong |  |  |
| Kinneloa Irrigation District | BOARD OF DIRECTORS DIV 1 | 1 | *Gerrie G. Kilburn |  |  |
| Kinneloa Irrigation District | BOARD OF DIRECTORS DIV 4 | 1 | *Timothy J. Eldridge |  |  |
| Kinneloa Irrigation District | Divisions 1, 4 and 5 (area/division 5) |  | (not named) |  | Listed as cancelled, but no candidate for this area/division on the RR/CC not-on-ballot list (likely no candidate filed; appointee not named). NEEDS_VERIFICA... |
| La Canada Irrigation District | BOARD OF DIRECTORS DIV 2 | 1 | *Spencer L. Soohoo |  |  |
| La Canada Irrigation District | BOARD OF DIRECTORS DIV 3 | 1 | *Robert Wallace |  |  |
| La Canada Irrigation District | BOARD OF DIRECTORS DIV 5 | 1 | *Anthony D. Angelica |  |  |
| Littlerock Creek Irrigation District | BOARD OF DIRECTORS | 2 | *Leo C. Thibault; *Tim C. Clark |  |  |
| Palm Ranch Irrigation District | BOARD OF DIRECTORS DIV 1 | 1 | *Jess W. Baker |  |  |
| Palm Ranch Irrigation District | BOARD OF DIRECTORS DIV 3 | 1 | *Wayne D. Nygaard |  |  |
| Palm Ranch Irrigation District | BOARD OF DIRECTORS DIV 5 | 1 | *Donald R. Berry |  |  |
| South Montebello Irrigation District | BOARD OF DIRECTORS DIV 3 | 1 | *Harris S. Mataalii |  |  |
| Altadena Library District | BOARD OF TRUSTEES | 3 | *John D. McDonald; *Gwendolyn W. McMullins; *Adalila Zelada Garcia |  |  |
| Palos Verdes Library District | BOARD OF TRUSTEES | 3 | *Debby Stegura; *James D. Moore; Sanford S. Davidson |  |  |
| Miraleste Recreation and Park District | 3 Full terms |  | (not named) |  | Named on the RR/CC cancelled list, but no matching contest/candidates on the RR/CC list of candidates whose names will not appear on the ballot; appointee(s)... |
| Ridgecrest Ranchos Recreation and Park District | 2 Full terms |  | (not named) |  | Named on the RR/CC cancelled list, but no matching contest/candidates on the RR/CC list of candidates whose names will not appear on the ballot; appointee(s)... |
| Artesia |  |  | (not named) |  | Named on the RR/CC cancelled list, but no matching contest/candidates on the RR/CC list of candidates whose names will not appear on the ballot; appointee(s)... |
| Bell Gardens |  |  | (not named) |  | Named on the RR/CC cancelled list, but no matching contest/candidates on the RR/CC list of candidates whose names will not appear on the ballot; appointee(s)... |
| Duarte |  |  | (not named) |  | Named on the RR/CC cancelled list, but no matching contest/candidates on the RR/CC list of candidates whose names will not appear on the ballot; appointee(s)... |

### 2016-06-07

| Jurisdiction | Office / seat | Seats | Appointees (* = incumbent per RR/CC) | SCV | Note |
|---|---|---|---|---|---|
| Republican Party County Central Committee | Assembly District 36 (36TH DISTRICT - REP) | 7 | *Lisa G. Moulton; Raj Malhi; *Drew Mercy; *Norm Hickling; *Josh Mann; *Marvin Crist; Matthew Loren Himlin |  | Party committee seat, not a public office. 36th AD (Antelope Valley) may include part of the SCV: NEEDS_VERIFICATION. |
| Democratic Party County Central Committee | Assembly District 38 (38TH DISTRICT - DEM) | 7 | Stacy L. Fortner; Michelle H. Elmer; *Michael R. Kulka; Patti Sulpizio; *Lynne Plambeck; A. Lysa Simon | yes | Party committee seat, not a public office. Assembly District 38 (2011 lines) covered the Santa Clarita Valley: NEEDS_VERIFICATION. Fewer candidates (6) than ... |
| Republican Party County Central Committee | Assembly District 39 (39TH DISTRICT - REP) | 7 | Douglas Chapin; Michael Menjivar; Sonny Sardo; Camille Wilson; *Eric M. Pierson |  | Party committee seat, not a public office. Fewer candidates (5) than seats (7). |
| Republican Party County Central Committee | Assembly District 41 (41ST DISTRICT - REP) | 7 | Don Meredith; Robert W. Bates; *Hugh Hemington; *Sandra J. Siraganian; *Michael Prescott Grant; *Elaine H. Klock |  | Party committee seat, not a public office. Fewer candidates (6) than seats (7). |
| Republican Party County Central Committee | Assembly District 44 (44TH DISTRICT - REP) | 7 | *Robin De Sapio; Jay Esban |  | Party committee seat, not a public office. Fewer candidates (2) than seats (7). |
| Republican Party County Central Committee | Assembly District 49 (49TH DISTRICT - REP) | 7 | *John Quintanilla; *Ronald Esquivel; *Peter Amundson; *Julie Nguyen; *Dale Case; *Eric Chan; Scott Kwong |  | Party committee seat, not a public office. |
| Republican Party County Central Committee | Assembly District 50 (50TH DISTRICT - REP) | 7 | *Marcella Sutton; *Adam Abrahms; *Howard Hyde; *James Carcich; *Scott Snapp; *Joseph Fein; *Ariana Assenmacher |  | Party committee seat, not a public office. |
| Republican Party County Central Committee | Assembly District 51 (51ST DISTRICT - REP) | 7 | Keith Edward Survillas; Erik Rosas; Rodolfo Torres |  | Party committee seat, not a public office. Fewer candidates (3) than seats (7). |
| Republican Party County Central Committee | Assembly District 52 (52ND DISTRICT - REP) | 7 | Roxanne Vaniman; *Danielle D. Rascon; Collin Davis; *James Nelson Popovich; James E. Eagon |  | Party committee seat, not a public office. Fewer candidates (5) than seats (7). |
| Republican Party County Central Committee | Assembly District 53 (53RD DISTRICT - REP) | 7 | Cary Sandoval; William "Rodriguez" Morrison |  | Party committee seat, not a public office. Fewer candidates (2) than seats (7). |
| Democratic Party County Central Committee | Assembly District 55 (55TH DISTRICT - DEM) | 7 | Peggye A. Jackson; Grace K. Hu |  | Party committee seat, not a public office. Fewer candidates (2) than seats (7). |
| Republican Party County Central Committee | Assembly District 55 (55TH DISTRICT - REP) | 7 | *Patricia L. (Trisha) Bowler; *Jody Roberto; *Nancy Lyons; Gene Doss; *Steven Tye; Mike Spence |  | Party committee seat, not a public office. Fewer candidates (6) than seats (7). |
| Democratic Party County Central Committee | Assembly District 57 (57TH DISTRICT - DEM) | 7 | *Christopher T. Kakimi; *Ronald Lozano; *Beverly F. Brown; *Jaime V. López; *Efrain Escobedo |  | Party committee seat, not a public office. Fewer candidates (5) than seats (7). |
| Democratic Party County Central Committee | Assembly District 58 (58TH DISTRICT - DEM) | 7 | *John P. Perez; *Delta Elaine "Rivas" Duvali; *Monica M. Sanchez; Vivian Romero; Ali Sajjad Taj; Lorraine Morales De La O; Sandra Salazar |  | Party committee seat, not a public office. |
| Republican Party County Central Committee | Assembly District 58 (58TH DISTRICT - REP) | 7 | Johnathan Alexis Quevedo; Joan Pylman; *Matt Kauble |  | Party committee seat, not a public office. Fewer candidates (3) than seats (7). |
| Republican Party County Central Committee | Assembly District 62 (62ND DISTRICT - REP) | 7 | *Julius Wilson; *Barbara Ford; *Carl Davis; Ted Grose; *Maureen Johnson; *Gary Aminoff; *Marc Rener |  | Party committee seat, not a public office. |
| Democratic Party County Central Committee | Assembly District 63 (63RD DISTRICT - DEM) | 7 | Anna M. Soto; Leticia Vasquez; Mary Louise Zavala; Maria Unzueta; Jorge Morales; Sandra R. Orozco; Jose Luis Solache |  | Party committee seat, not a public office. |
| Republican Party County Central Committee | Assembly District 63 (63RD DISTRICT - REP) | 7 | Julian Del Real-Calleros; *Jerry Taylor Sink; *Eugene E. Champagne; *Wayne W. Miller; *Gladys O. Miller; *Mark William Dameron |  | Party committee seat, not a public office. Fewer candidates (6) than seats (7). |
| Republican Party County Central Committee | Assembly District 64 (64TH DISTRICT - REP) | 7 | *William B. Sanford; *Theresa Sanford |  | Party committee seat, not a public office. Fewer candidates (2) than seats (7). |
| Republican Party County Central Committee | Assembly District 66 (66TH DISTRICT - REP) | 7 | *Patricia Lagrelius; Peter C. Michel; *Gary J. Aven; Janice Webb; *John M. Fleming; *Kenneth A. Hartley; *William L. Schmidt |  | Party committee seat, not a public office. |
| Peace and Freedom Party Central Committee | Assembly District 39 (39TH DISTRICT - PF) | 7 | Benjamin Huff; Yohana De Leon |  | Party committee seat, not a public office. Fewer candidates (2) than seats (7). |
| Peace and Freedom Party Central Committee | Assembly District 43 (43RD DISTRICT - PF) | 6 | Jeff Bigelow |  | Party committee seat, not a public office. Fewer candidates (1) than seats (6). |
| Peace and Freedom Party Central Committee | Assembly District 46 (46TH DISTRICT - PF) | 5 | David Feldman |  | Party committee seat, not a public office. Fewer candidates (1) than seats (5). |
| Peace and Freedom Party Central Committee | Assembly District 50 (50TH DISTRICT - PF) | 5 | *Nancy Lawrence; Scott Scheffer |  | Party committee seat, not a public office. Fewer candidates (2) than seats (5). |
| Peace and Freedom Party Central Committee | Assembly District 51 (51ST DISTRICT - PF) | 11 | Sheila Xiao; Preston Wood; Wilbert Gutierrez; Alise Y. Sochaczewski |  | Party committee seat, not a public office. Fewer candidates (4) than seats (11). |
| Peace and Freedom Party Central Committee | Assembly District 53 (53RD DISTRICT - PF) | 8 | Esmeralda Loreto; Victor Quintero |  | Party committee seat, not a public office. Fewer candidates (2) than seats (8). |
| Peace and Freedom Party Central Committee | Assembly District 54 (54TH DISTRICT - PF) | 6 | *Gary Gordon; *Cindy Gordon; Lawrence Phillip Reyes |  | Party committee seat, not a public office. Fewer candidates (3) than seats (6). |
| Peace and Freedom Party Central Committee | Assembly District 59 (59TH DISTRICT - PF) | 9 | John Thompson Parker; Margaret Vascassenno |  | Party committee seat, not a public office. Fewer candidates (2) than seats (9). |
| Peace and Freedom Party Central Committee | Assembly District 62 (62ND DISTRICT - PF) | 7 | Abraham Marquez; *Alice Stek; Edward E. Ferrer |  | Party committee seat, not a public office. Fewer candidates (3) than seats (7). |
| Peace and Freedom Party Central Committee | Assembly District 64 (64TH DISTRICT - PF) | 13 | Andrew Nance |  | Party committee seat, not a public office. Fewer candidates (1) than seats (13). |
| Peace and Freedom Party Central Committee | Assembly District 70 (70TH DISTRICT - PF) | 13 | Douglas Kauffman; Raymond White; Mika White; Eman Khaleq |  | Party committee seat, not a public office. Fewer candidates (4) than seats (13). |
| Green Party County Council | Assembly District 26 (26TH DISTRICT - GRN) | 7 | *Michael Feinstein; *Cordula Ohman; Tom Bibiyan; *Linda Piera-Avila; Cris Gutierrez; Anne Goeke |  | Party committee seat, not a public office. Fewer candidates (6) than seats (7). |
| Green Party County Council | Assembly District 29 & 32 (29TH & 32ND DIST - GRN) | 3 | Daniel Alvarado |  | Party committee seat, not a public office. Fewer candidates (1) than seats (3). |
| County Central Committee (Republican Party) | 59th District |  | (not named) |  | NO CANDIDATES. Listed under "CANCELLED ELECTIONS DUE TO NO CANDIDATES". |
| County Central Committee (Democratic Party) | 44th District |  | (not named) |  | NO CANDIDATES. Listed under "CANCELLED ELECTIONS DUE TO NO CANDIDATES". |
| Central Committee (Peace and Freedom Party) | 36th, 38th, 41st, 44th, 45th, 48th, 49th, 52nd, 55th, 57th, 58th, 63rd and 66th |  | (not named) | yes | NO CANDIDATES. Listed under "CANCELLED ELECTIONS DUE TO NO CANDIDATES". Includes the 38th AD (SCV under 2011 lines: NEEDS_VERIFICATION). |
| County Council (Green Party) | 18th, (20th, 23rd & 25th), 21st, 22nd, 24th, 27th, 30th, 33rd and (34th & 35th) |  | (not named) |  | NO CANDIDATES. Listed under "CANCELLED ELECTIONS DUE TO NO CANDIDATES". |

### 2016-11-08

| Jurisdiction | Office / seat | Seats | Appointees (* = incumbent per RR/CC) | SCV | Note |
|---|---|---|---|---|---|
| Alhambra Unified School District | First and Second Districts |  | (not named) |  | Named on the RR/CC cancelled list, but no matching contest/candidates on the RR/CC list of candidates whose names will not appear on the ballot; appointee(s)... |
| Lowell Joint School District | GOVERNING BOARD MEMBER | 2 | *Fred W. Schambeck; *Brandon R. Jones |  |  |
| North Orange County Community College District | GOV BD MEMBER TR AREA 004 | 1 | Molly McClanahan |  |  |
| Santa Clarita Community College District | GOV BD MEMBER TR AREA 003 | 1 | *Steven D. Zimmer | yes |  |
| Saugus Union School District | GOV BD MEMBER TR AREA 004 | 1 | *David C. Powell | yes |  |
| Saugus Union School District | TRUSTEE 005 TERM ENDS 12/18 | 1 | *Chris Trunkey | yes |  |
| Santa Monica-Malibu Unified School District |  |  | (not named) |  | Named on the RR/CC cancelled list, but no matching contest/candidates on the RR/CC list of candidates whose names will not appear on the ballot; appointee(s)... |
| Westfield Recreation and Park District | BOARD OF DIRECTORS | 2 | Brenda L. Swanney-Caropino; *Alan R. Hoffman |  |  |
| Foothill Municipal Water District | BOARD OF DIRECTORS DIV 001 | 1 | *Garry E. Bryant |  |  |
| Las Virgenes Municipal Water District | BOARD OF DIRECTORS DIV 001 | 1 | *Charles P. Caspary |  |  |
| Las Virgenes Municipal Water District | BOARD OF DIRECTORS DIV 004 | 1 | *Leonard E. Polan |  |  |
| San Gabriel Valley Municipal Water District | BOARD OF DIRECTORS DIV 003 | 1 | *Thomas Wong |  |  |
| Three Valleys Municipal Water District | BOARD OF DIRECTORS DIV 002 | 1 | *David D. De Jesus |  |  |
| Three Valleys Municipal Water District | BOARD OF DIRECTORS DIV 004 | 1 | *Robert G. Kuhn |  |  |
| Three Valleys Municipal Water District | BOARD OF DIRECTORS DIV 007 | 1 | *Danny M. Horan |  |  |
| Upper San Gabriel Valley Water District | BOARD OF DIRECTORS DIV 002 | 1 | *Charles M. Trevino |  |  |
| West Basin Municipal Water District | BOARD OF DIRECTORS DIV 003 | 1 | *Carol W. Kwan |  |  |
| Antelope Valley East Kern Water Agency | BOARD OF DIRECTORS DIV 006 | 1 | *Marlon Barnes |  |  |
| Foothill Municipal Water District | Division 3 |  | (not named) |  | NO CANDIDATES. Listed under "CANCELLED ELECTIONS DUE TO NO CANDIDATES"; appointee not named in RR/CC documents. |
