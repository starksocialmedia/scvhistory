# Claims on person records that rest on a single source, 8 October 2026

Item 11 of the overnight run. Nathan: "Every claim on a person record that rests on a single source, listed. After the Perkins trust rule and the del Valle dates I want to know how much of the archive stands on one leg."

Read only. Nothing in the database was changed. Built by `scripts/import/dump_person_claims_2026_10_08.php` (the Craft records, to `_person-claims-dump.json`), `scripts/import/single_source_claims_2026_10_08.py` (the census, to the JSON beside this file) and `scripts/import/single_source_report_2026_10_08.py` (this report). Every claim, with its footnotes and what each footnote was counted as, is in `single-source-claims-2026-10-08.json`.

**What was read.** The Craft records: 254 live person records and the 423 office holdings, 30 affiliations, 10 education records, 539 candidacies and 127 elections that point at them. The census counts the citations each record makes, by the archive's own rules of independence. It did not open any cited source, so it says how many legs a claim has, not whether any leg holds.

## Headline

**1772 claims** were found on person records and the records that hang from them. **926 (52 per cent) rest on one source**, on 213 people. A further **107 carry no source the census could count**: a date field with no footnote anywhere on the record, a candidacy with no footnote on it or its election, a sentence whose only note is reasoning. 739 rest on two or more independent sources.

| Kind of claim | Claims | One source | of which stated plainly | No source counted | Two or more |
| --- | ---: | ---: | ---: | ---: | ---: |
| Profile text (citation spans) | 896 | 519 | 368 | 2 | 375 |
| Birth, death, birthplace, burial fields | 210 | 68 | 68 | 86 | 56 |
| Kinship links | 10 | 1 | 1 | 2 | 7 |
| Confirmed On This Day dates | 24 | 6 | 6 | 14 | 4 |
| Office terms | 350 | 155 | 155 | 0 | 195 |
| Affiliations | 30 | 24 | 24 | 0 | 6 |
| Education records | 10 | 10 | 10 | 0 | 0 |
| Candidacies (election results) | 242 | 143 | 143 | 3 | 96 |
| **All** | **1772** | **926** | **775** | **107** | **739** |

"Stated plainly" is a test on the profile sentence only: a sentence that attributes its point ("Reynolds gives", "by the City's account", "the sources differ") is counted as hedged. Fields, terms and candidacies have no sentence to hedge, so every one-source field, term and candidacy counts as plain.

### What the one-source claims rest on

| The one source | Claims |
| --- | ---: |
| County returns (CEDA or the County's statement) | 176 |
| Perkins/Reynolds (one source, PROFILES.md) | 50 |
| Hart board roster (Leon Worden) | 44 |
| City of Santa Clarita biographies (the City's own account of a member) | 43 |
| California Secretary of State, Statement of Vote | 35 |
| Leon Worden's City Council ledger | 32 |
| santaclarita.gov/commission-information/arts-commission | 17 |
| Biographical Directory of the U.S. Congress | 10 |
| "john & sarah gifford home" | 10 |
| Find a Grave | 9 |
| archive record #28060, "Dr. Vincent Gelcich (photographs)" | 9 |
| homesteadmuseum.blog/2018/09/27/on-this-day-the-probate-of-abel-stearns-27-september-1871 | 9 |
| archive record #28049, "George A. Caravalho, Santa Clarita's First Permanent City Manager, 1938-2020 (obituaries)" | 8 |
| santaclarita.gov/commission-information/planning-commission | 7 |
| hartdistrict.org/apps/pages/governing-board-members | 7 |
| "city founder jo anne darcy dies at 86" | 7 |
| sandiegohistory.org/archives/biographysubject/cjcouts | 7 |
| Secretary of the Senate, Record of Members | 6 |
| canyons.edu/administration/board/history.php | 6 |
| Pen Pictures from the Garden of the World (1888) | 6 |
| archive record #817, "Preface (articles)" | 6 |
| archive record #21939, "General Municipal Elections: Historical Election Results, 1987 to 2012 (documents)" | 6 |
| "1991 lines, drawn by the court's special masters" | 5 |
| vcstar.com/news/2010/oct/30/gorell-expects-deployment-to-afghanistan | 5 |
| archive record #20102, "Charles Alexander Mentry's Death Certificate (documents)" | 5 |
| "a history of vasquez rocks and vicinity" | 5 |
| "colma cemeteries: henry mayo newhall & heirs" | 4 |
| "thomas m. frew ii, newhall blacksmith" | 4 |
| "2011 lines, drawn by the citizens redistricting commission" | 4 |
| sd27.senate.ca.gov/biography | 4 |
| 265 other sources, each under 4 claims | 380 |

Six sources carry more than two fifths of the weight: the County's election returns (in CEDA's compilation or the County's own statement), Perkins and Reynolds counted together as the PROFILES.md rule requires, Leon Worden's roster of the Hart board, the City's own biographies of its council members, the Secretary of State's Statements of Vote, and Leon Worden's City Council ledger. The returns and the Statements are official counts, and one leg is what an election result normally has. The Hart roster and Perkins/Reynolds are the sources the archive's own rules mark as needing a second witness. The City's biographies are a member's office describing the member.

## How a claim was counted

- **Profile text.** A claim is a citation span: the text from the previous marker group, or the start of the paragraph, to a marker group such as `[1][3]`. It is the writer's own unit of citation, and may hold more than one sentence. Only bodies that publish (`editorial-2026`, `legacy-leon`) are counted.
- **Fields.** `birthDate` and `deathDate` are found in the spans that hold the year and a word of birth or death; `birthplace` and `burialPlace` in the spans that hold the place's first word. If no span holds it, a footnote naming the date or place is used; failing both, the field is "no citation located".
- **Kinship.** Each `spouseOf`, `childOf` and `siblingOf` link, sourced from the spans that name the relative.
- **On This Day.** Only `recordDates` rows ticked Confirmed, the ones that publish.
- **Terms, affiliations, education, candidacies.** All the footnotes on the record; for a candidacy, also those on its election.

**Independence**, from docs/PROFILES.md and DATA-MODEL.md:

- Perkins and Reynolds are one source (Nathan, 3 October 2026). A Leon Worden column cited beside either is the same account unless its footnote names another source (4 October 2026).
- CEDA compiles the County's returns, so CEDA and the County's statement are one source.
- The City's biographies of a council member (1998, 2008, 2013 and the current council page) are one source: the City's own account.
- A page and its Wayback copy are one source.
- A note that names no document is reasoning, not a witness: a term length from the Education Code, "the seat was next filled at the election of...", a district-lines remark. It is listed on the claim in the JSON and not counted.

## Limits

- The census reads the citations, not the sources. Two citations it counts as independent may not be: a City biography and a magazine profile drawn from the same press kit, two papers carrying one wire story, a Worden caption that repeats Reynolds without naming him. It can only undercount single-source claims, never overcount them.
- Field location is by year and place word. A field whose year appears in a span about something else would be credited with that span's sources. Spot-checked on 20 fields: three had been credited with a neighbouring span's sources by year alone (Rioux, Reynolds, Knight), so the census now prefers a span holding the whole date as printed; two of the three then matched their own sentence. It is still a heuristic.
- A span is the writer's unit of citation, and a long span with many markers credits all of them to every point in it. Where one marker in such a span is the only witness to one of its points, the census cannot tell, so it undercounts.

- A term whose footnotes cite CEDA and the Hart roster counts as two sources. Where the roster is the only source for its dates and CEDA only for the election, that is generous.
- Candidacies that are not linked to a person record (297 of 539) are not person claims and are not counted.

## The legs the archive's rules already doubt

### Perkins or Reynolds alone

Under the reliability rule, Perkins is trusted only where he transcribes a document; his summaries, hearsay, "firsts", family labels and lesser-event dates are unverified until a primary is found, and Reynolds repeating him is not a second witness. These claims have nothing else under them.

| Person | Kind | Claim | Hedged in text |
| --- | --- | --- | --- |
| Andrés Pico #317 | body | Andrés Pico was a Californio military commander and landholder. The Santa Clarita Valley carries his name in Pico Canyon, where he was among the first to file oil claims in 1865. | no |
| Andrés Pico #317 | body | On 13 January 1847, at the home of María Jesus Lopez de Felíz near Cahuenga Pass, he surrendered to John C. Frémont and signed the articles of capitulation that ended the war in California. | no |
| Andrés Pico #317 | body | When the first oil claims were filed in the hills north of the pass in 1865, Perkins names Pico among the filers. | no |
| Antonio del Valle #291 | body | Antonio del Valle was a Mexican army lieutenant who came to California in 1819, took charge of Mission San Fernando when it was secularized, and in 1839 received Rancho San Francisco, the grant that took in most of the Santa... | no |
| Antonio del Valle #291 | body | He died on the rancho two years later without a will, and his heirs held it in undivided shares until it was partitioned in 1870. | no |
| Antonio del Valle #291 | body | Perkins, drawing on Bancroft, says he came from Jalisco as a lieutenant in the Company of San Blas in 1819. His son Ygnacio, from his first marriage, joined him at Monterey in 1825, at seventeen. | yes |
| Antonio del Valle #291 | body | Jerry Reynolds's ages for him, forty-six in 1834 and fifty-three at his death, would put his birth about 1788. | no |
| Antonio del Valle #291 | body | He then sought the rancho for himself. Governor Juan B. Alvarado granted it on 22 January 1839, over a protest from Fr. Narciso Durán, prefect of the southern missions. | no |
| Antonio del Valle #291 | body | He moved them into the old Estancia de San Francisco Xavier, the mission outpost of 1804 above the junction of Castaic Creek and the Santa Clara River, which became the rancho house. | no |
| Antonio del Valle #291 | body | Jerry Reynolds gives his death as 21 June and Perkins as 12 June; both are after the burial, so neither can be right. | yes |
| Antonio del Valle #291 | body | The heirs were his widow, Ygnacio and the children of both marriages, though the sources count them differently. | no |
| Antonio del Valle #291 | body | The probate court distributed the rancho to them in undivided portions, and it was not partitioned until 1870, when Camulos, 1,340 acres, was set apart for Ygnacio. | no |
| Antonio del Valle #291 | field | birthDate: 1788 | no |
| Antonio del Valle #291 | field | birthplace: Jalisco, Mexico | no |
| Arthur Buckingham Perkins #333 | body | And Reynolds, the valley's second town historian, worked from Perkins's photographs and cites his history of the rancho, so Perkins and Reynolds agreeing on a point are not two independent witnesses: a mistake of Perkins's can... | no |
| Cave Johnson Couts #323 | body | Couts drove cattle north to the Gold Rush markets. In the spring of 1852 he wrote to Abel Stearns: "The nest of thieves in the Santa Clara did all they knew how to make me loose [sic] a lot, but not all." Others fared worse; he... | yes |
| Cave Johnson Couts #323 | body | According to Reynolds, in the spring of 1849 Couts and his brother, William Blunt Couts, drove a herd of some seven hundred head, on consignment from Juan Bandini and John Temple, up the coast to San Jose, where Cave held out... | yes |
| Cave Johnson Couts #323 | body | Reynolds tells of their meeting, Ysidora tumbling from a roof railing into his arms as his column passed; it is Reynolds's story, and no earlier source for it is in the archive. | no |
| Charles Alexander Mentry #18648 | body | Then the West. | no |
| Charles Alexander Mentry #18648 | body | Then California. | no |
| Charles Alexander Mentry #18648 | body | Two sources hedge: the Los Angeles Times in 1954, which has his father "credited with" the first well in California, and Perkins, once, in 1962: "probably the first commercially successful oil well in California." | yes |
| Edward Fitzgerald Beale #327 | body | For the valley he is the man of the cut. He took over Andrés Pico's franchise for the road over the pass and, by Jerry Reynolds's account, got five thousand dollars from the Los Angeles supervisors to do the work. | yes |
| Jill Klajic #15874 | body | She came to the council from the cityhood campaign. The 1998 edition of Jerry Reynolds's history says that she joined the City Formation Committee soon after it was organized and was paid staff to the campaign. | yes |
| Jo Anne Darcy #16140 | body | She joined the City Formation Committee soon after it was organized. | no |
| John C. Frémont #307 | body | John C. Frémont led the American battalion that crossed the Santa Clarita Valley in January 1847 on its way to Cahuenga, where Andrés Pico surrendered to him, and the pass between the Santa Clarita and San Fernando valleys bears... | no |
| John C. Frémont #307 | body | By the account Leon Worden and Jerry Reynolds both give, in the same words, Frémont and his hundred-man "buckskin battalion" reached Castaic Junction from the north on January 9, 1847, and probably stopped overnight at the del... | yes |
| John C. Frémont #307 | body | Born in Savannah, Georgia, in 1813, he was an Army explorer the press called "the Pathfinder," with Kit Carson as his guide, and he was later one of California's first United States senators, the Republican Party's first... | no |
| John C. Frémont #307 | field | birthDate: January 21, 1813 | no |
| John C. Frémont #307 | field | deathDate: July 13, 1890 | no |
| John C. Frémont #307 | field | birthplace: Savannah, Georgia | no |
| Pedro Fages #287 | body | Jerry Reynolds tells the pursuit in detail: six soldiers gone, a route by the Mojave River and the Antelope Valley and down through the Sierra Pelona, a first camp probably near Agua Dulce Springs, which Fages named for its... | yes |
| Pedro Fages #287 | body | A Catalonian, he had led the Catalonian Volunteers on the 1769 march, twenty-five of them by Jerry Reynolds's count; he later fought Apaches on the Sonoran frontier and served again as governor until 1791. | no |
| Pedro Fages #287 | field | birthplace: Catalonia | no |
| Phineas Banning #18714 | body | Phineas Banning drove the first stagecoach over the San Fernando Pass into the Santa Clarita Valley, in December 1854. Horace Bell, who knew him, wrote that when Fort Tejon was established, Banning's firm, Alexander & Banning,... | yes |
| Phineas Banning #18714 | body | A.B. Perkins took the ride as the arrival of the first stage at Rancho San Francisco. | no |
| Phineas Banning #18714 | body | Leon Worden notes that some, Perkins perhaps among them, believe Banning's crossing was not where Beale's Cut was later made. | yes |
| Phineas Banning #18714 | body | In the Soledad copper boom of 1863, Perkins wrote, he "bought in" to the district's mines. | yes |
| Phineas Banning #18714 | body | Bell, writing in 1881, said the Southern Pacific had cleared away the thicket where Banning's stage came to rest in 1854 when it dug its San Fernando tunnel. | yes |
| Remi Nadeau #18869 | body | Remi Nadeau owned a ranch in Soledad Canyon, on the north side of the canyon road near Whites Canyon, | no |
| Sanford Lyon #20224 | body | Sanford Lyon kept the stage station on the road north of the San Fernando Pass that carried the family name, and from 1869 was postmaster of Petroleopolis, the post office there. | no |
| Sanford Lyon #20224 | body | He was at the Pico Canyon oil seeps by 1866, when a Los Angeles paper reported that "Mr. S. Lyon is busy dipping the oil from the holes." | yes |
| Sanford Lyon #20224 | body | Later accounts credit him with drilling the first well at Pico, with Henry Clay Wiley and William Wirt Jenkins, but they disagree on the year, 1869 or 1870, and on who his partners were. | yes |
| Sanford Lyon #20224 | body | The sources give three years for his death: 1881, 1882 and 1885. | yes |
| Thomas O. Larkin #311 | body | A.B. Perkins wrote that Thomas O. Larkin, the American consul at Monterey, told the New York Sun that a common laborer could pick up $2 a day at the San Feliciano placers, in a canyon off Piru Creek. | yes |
| William S. Hart #16356 | body | The valley's first high school, which opened in 1945, was named for him in his lifetime. | no |
| William Wirt Jenkins #20226 | body | Jerry Reynolds adds that he was later a county undersheriff and ranched on Castaic Creek from 1878. | no |
| William Wirt Jenkins #20226 | body | He was born near Circleville, Ohio, on 12 October, in 1833 or 1835; the sources differ. | yes |
| William Wirt Jenkins #20226 | body | He died on 19 October 1916. | no |
| William Wirt Jenkins #20226 | field | deathDate: October 19, 1916 | no |
| William Wirt Jenkins #20226 | field | birthplace: Circleville, Ohio | no |

### The Hart board roster alone

PROFILES.md: the roster contradicts itself at least once (Hanrion) and was wrong twice about who stood (Loberg, King); "where it is the only source for a row and the row is unclear or disagrees with a neighbouring row, the holding footnotes both and states neither", and a whole-roster pass for self-contradictions "is worth doing before any profile rests on a roster row alone." These rest on a roster row alone.

| Person | Record | Term or claim | Evidence |
| --- | --- | --- | --- |
| Adrian W. Adams #28667 | profile text | Leon Worden's roster of the William S. Hart Union High School District board lists an Adrian Adams on the board from... | / |
| C. L. Dillenbeck #28651 | officeHoldings #28735 | 1948 to 1952 | roster/roster |
| Carroll Word #28689 | officeHoldings #28775 | 1970 to June 30, 1974 | roster/roster |
| Charles Brown #28643 | officeHoldings #28727 | March 9, 1945 to 1948 | roster/roster |
| Charleton Hadley #28659 | officeHoldings #28743 | 1953 to 1959 | roster/roster |
| Chester Allen #28655 | officeHoldings #28739 | 1952 to 1956 | roster/roster |
| Clara Stroup #28713 | officeHoldings #28799 | December 1979 to December 1991 | roster/roster |
| Connie Worden #16418 | officeHoldings #28588 | December 1, 1974 to December 1979 | roster/roster |
| David Holden #28685 | officeHoldings #28771 | 1969 to 1973 | roster/roster |
| Dennis King #25439 | officeHoldings #28590 | December 1985 to December 1989 | roster/roster |
| Earl Schmidt #28675 | officeHoldings #28759 | 1962 to 1969 | roster/roster |
| Edith Palmer #28669 | officeHoldings #28753 | 1957 to 1968 | roster/roster |
| Edward Duarte #28681 | officeHoldings #28767 | 1968 to 1970 | roster/roster |
| Elisha Agajanian #28679 | officeHoldings #28765 | 1968 to 1972 | roster/roster |
| Emmett Carraher #28683 | officeHoldings #28769 | 1969 to 1970 | roster/roster |
| Ernest Malam #28665 | officeHoldings #28749 | 1956 to 1957 | roster/roster |
| Gerald Heidt #28709 | officeHoldings #28795 | December 1979 to December 1991 | roster/roster |
| Howard Blackwell #28657 | officeHoldings #28741 | 1953 to 1954 | roster/roster |
| Howard Gulley #28663 | officeHoldings #28747 | 1956 to 1962 | roster/roster |
| James Putjenter #28707 | officeHoldings #28793 | 1978 to December 1979 | roster/roster |
| Jereann Bowman #28677 | officeHoldings #28761 | 1964 to 1968 | roster/roster |
| Jereann Bowman #28677 | officeHoldings #28763 | 1969 to 1969 | roster/roster |
| Jim Shuman #28705 | officeHoldings #28791 | 1978 to July 1978 | roster/roster |
| John Hassel #28566 | officeHoldings #28608 | December 1991 to December 1995 | roster/roster |
| Julio Lombardi #28661 | officeHoldings #28745 | 1955 to 1962 | roster/roster |
| Kenneth Wullschleger #28697 | officeHoldings #28783 | 1973 to January 23, 1979 | roster/roster |
| Louis Brathwaite #28703 | officeHoldings #28789 | April 12, 1977 to December 1981 | roster/roster |
| Mary Bonelli #28641 | officeHoldings #28725 | March 9, 1945 to 1957 | roster/roster |
| Mildred Gilmour #28649 | officeHoldings #28733 | March 9, 1945 to 1956 | roster/roster |
| Patricia Hanrion #25437 | officeHoldings #28616 | December 1993 to December 1997 | roster/roster |
| Patrick Shaughnessy #28699 | officeHoldings #28785 | April 1, 1975 to January 1978 | roster/roster |
| Paula Olivares #26597 | officeHoldings #28624 | December 1991 to December 1995 | roster/roster |
| Robert Crozier #28695 | officeHoldings #28781 | 1973 to April 1977 | roster/roster |
| Robert Keysor #28711 | officeHoldings #28797 | December 1979 to December 1985 | roster/roster |
| Ruth Kelley #28693 | officeHoldings #28779 | 1971 to April 1975 | roster/roster |
| S. A. Wright #28691 | officeHoldings #28777 | 1970 to 1973 | roster/roster |
| S. S. Donaldson #28645 | officeHoldings #28729 | March 9, 1945 to 1953 | roster/roster |
| Sheldon Allen #28701 | officeHoldings #28787 | 1977 to December 1979 | roster/roster |
| Thomas Hanson #28687 | officeHoldings #28773 | 1970 to May 25, 1977 | roster/roster |
| Thomas M. Frew Jr. #28647 | officeHoldings #28731 | March 9, 1945 to 1951 | roster/roster |
| W. D. Ross #28671 | officeHoldings #28755 | 1959 to 1969 | roster/roster |
| Walter Cook #28653 | officeHoldings #28737 | 1951 to 1955 | roster/roster |
| William Dinsenbacher #28717 | officeHoldings #30604 | December 1989 to December 1993 | roster/roster |
| William Dinsenbacher #28717 | officeHoldings #30606 | December 1993 to December 1997 | roster/roster |

### Find a Grave, the City's own account, a commission page

| Person | Kind | Claim | Source |
| --- | --- | --- | --- |
| Bill Miranda #23089 | body | He has been the city's mayor twice: in 2021, and in 2024 or 2025, since the City's page gives both. | City of Santa Clarita biographies (the City's own account of a member) |
| Bill Miranda #23089 | body | The City's biography describes him as an Air Force veteran, a business owner, a former chief executive of the Santa Clarita Valley Latino Chamber of Commerce,... | City of Santa Clarita biographies (the City's own account of a member) |
| Bill Miranda #23089 | term #29078 | Bill Miranda: Mayor, The City of Santa Clarita: 2021 | City of Santa Clarita biographies (the City's own account of a member) |
| Bob Kellar #21944 | body | Before the council he served in the United States Army from 1965 to 1967 and then spent 25 years with the Los Angeles Police Department, retiring in 1993 as... | City of Santa Clarita biographies (the City's own account of a member) |
| Bob Kellar #21944 | body | By the City's account he was president of the Canyon Country Chamber of Commerce from 1993 until it joined the Santa Clarita Valley Chamber of Commerce in... | City of Santa Clarita biographies (the City's own account of a member) |
| Bob Kellar #21944 | body | The City's biography lists him as president of the Santa Clarita Division of the Southland Regional Association of Realtors in 2000; a member of the Santa... | City of Santa Clarita biographies (the City's own account of a member) |
| Bob Kellar #21944 | body | On the council, the City says, he worked against the proposed CEMEX mining operation and to bring the parties together over the cleanup of the... | City of Santa Clarita biographies (the City's own account of a member) |
| Cameron Smyth #16380 | term #29076 | Cameron Smyth: Mayor, The City of Santa Clarita: 2024 | City of Santa Clarita biographies (the City's own account of a member) |
| Cameron Smyth #16380 | term #29074 | Cameron Smyth: Mayor, The City of Santa Clarita: 2020 | City of Santa Clarita biographies (the City's own account of a member) |
| Cameron Smyth #16380 | term #29072 | Cameron Smyth: Mayor, The City of Santa Clarita: 2017 | City of Santa Clarita biographies (the City's own account of a member) |
| Cameron Smyth #16380 | term #29070 | Cameron Smyth: Mayor, The City of Santa Clarita: 2005 | City of Santa Clarita biographies (the City's own account of a member) |
| Cameron Smyth #16380 | term #29068 | Cameron Smyth: Mayor, The City of Santa Clarita: 2003 | City of Santa Clarita biographies (the City's own account of a member) |
| Carl Boyer #15808 | body | He had studied at Edinburgh University in 1956 and 1957, and took a B.A. in history at Trinity University in 1959 and a master's degree in education at the... | City of Santa Clarita biographies (the City's own account of a member) |
| Carl Boyer #15808 | body | The City's biography of 1998 names him the charity's treasurer in California. | City of Santa Clarita biographies (the City's own account of a member) |
| Frank Ferry #23083 | body | By the City's account he holds a bachelor's degree in governmental communications from California State University, Northridge, a bachelor's degree in law, a... | City of Santa Clarita biographies (the City's own account of a member) |
| Frank Ferry #23083 | body | The City says he first ran to get roads built and relieve traffic, and that on the council he wanted after-school programs at every elementary school and more... | City of Santa Clarita biographies (the City's own account of a member) |
| Jan Heidt #15737 | body | The City's biography of 1998 records her as a co-founder of the "Dump the Dump" task force of 1978, which kept two toxic waste dumps out of the valley, and of... | City of Santa Clarita biographies (the City's own account of a member) |
| Jan Heidt #15737 | body | By the same account she graduated from Michigan State University in 1961 and was a Navy officer from 1964 to 1969, two years of it at the Navy Communication... | City of Santa Clarita biographies (the City's own account of a member) |
| Jan Heidt #15737 | body | The Zonta Club named her its Woman of the Year in 1984 and 1988. | City of Santa Clarita biographies (the City's own account of a member) |
| Jason Gibbs #23091 | body | The City's biography says he holds bachelor's and master's degrees in mechanical engineering from Cal Poly and works in the aerospace industry as senior... | City of Santa Clarita biographies (the City's own account of a member) |
| Jason Gibbs #23091 | term #29082 | Jason Gibbs: Mayor, The City of Santa Clarita: 2023 | City of Santa Clarita biographies (the City's own account of a member) |
| Jill Klajic #15874 | body | The City's biography of 1998 lists her as general manager and recycling coordinator of Cal Coast Recycling; a consultant to TCB International, organizing... | City of Santa Clarita biographies (the City's own account of a member) |
| Jo Anne Darcy #16140 | body | The City's biography of 1998 gives her seven years as the chamber's executive vice president and manager and fifteen as a senior deputy to the Board of... | City of Santa Clarita biographies (the City's own account of a member) |
| Jo Anne Darcy #16140 | body | Governor George Deukmejian appointed her to the California State Film Commission in 1989, and Governor Pete Wilson reappointed her in 1993. | City of Santa Clarita biographies (the City's own account of a member) |
| Laurene Weste #15929 | body | As a commissioner, the City says, she oversaw the establishment of parks, the preservation of open space and the building of the cross-town trail system. | City of Santa Clarita biographies (the City's own account of a member) |
| Laurene Weste #15929 | term #29056 | Laurene Weste: Mayor, The City of Santa Clarita: 2022 | City of Santa Clarita biographies (the City's own account of a member) |
| Laurene Weste #15929 | term #29054 | Laurene Weste: Mayor, The City of Santa Clarita: 2018 | City of Santa Clarita biographies (the City's own account of a member) |
| Laurene Weste #15929 | term #29052 | Laurene Weste: Mayor, The City of Santa Clarita: 2014 | City of Santa Clarita biographies (the City's own account of a member) |
| Laurene Weste #15929 | term #29050 | Laurene Weste: Mayor, The City of Santa Clarita: 2010 | City of Santa Clarita biographies (the City's own account of a member) |
| Laurene Weste #15929 | term #29048 | Laurene Weste: Mayor, The City of Santa Clarita: 2006 | City of Santa Clarita biographies (the City's own account of a member) |
| Laurene Weste #15929 | term #29046 | Laurene Weste: Mayor, The City of Santa Clarita: 2001 | City of Santa Clarita biographies (the City's own account of a member) |
| Laurie Ender #23087 | body | Before the council, by the City's account, she had been a Parks, Recreation and Community Services commissioner since 2003 and chaired the commission in 2006... | City of Santa Clarita biographies (the City's own account of a member) |
| Marsha McLean #23085 | body | The City's biography says that before the council she was a program analyst for special projects for the City of Santa Clarita, and before that worked for the... | City of Santa Clarita biographies (the City's own account of a member) |
| Marsha McLean #23085 | body | Her public causes, as the City gives them, have been the canyons and transportation. She founded the S.C.V. Canyons Preservation Committee, which co-sponsored... | City of Santa Clarita biographies (the City's own account of a member) |
| Marsha McLean #23085 | term #29066 | Marsha McLean: Mayor, The City of Santa Clarita: 2019 | City of Santa Clarita biographies (the City's own account of a member) |
| Marsha McLean #23085 | term #29064 | Marsha McLean: Mayor, The City of Santa Clarita: 2015 | City of Santa Clarita biographies (the City's own account of a member) |
| Marsha McLean #23085 | term #29062 | Marsha McLean: Mayor, The City of Santa Clarita: 2011 | City of Santa Clarita biographies (the City's own account of a member) |
| Marsha McLean #23085 | term #29060 | Marsha McLean: Mayor, The City of Santa Clarita: 2007 | City of Santa Clarita biographies (the City's own account of a member) |
| Patsy Ayala #23093 | body | When the City's biography of her was read on 1 October 2026, she was serving as Mayor Pro Tem. | City of Santa Clarita biographies (the City's own account of a member) |
| Patsy Ayala #23093 | body | The City's biography describes more than a decade in public service and transportation policy before the council. It says she "served in the California State... | City of Santa Clarita biographies (the City's own account of a member) |
| Patsy Ayala #23093 | term #29084 | Patsy Ayala: Mayor Pro Tem, The City of Santa Clarita: | City of Santa Clarita biographies (the City's own account of a member) |
| TimBen Boydston #21946 | body | By the City's account he came to the valley as a boy in 1960, went to Sulphur Springs Elementary, Placerita Junior High and Canyon High, served four years in... | City of Santa Clarita biographies (the City's own account of a member) |
| TimBen Boydston #21946 | body | After his appointment he kept a promise not to run in 2008, and formed the Santa Clarita Neighborhood Coalition. | City of Santa Clarita biographies (the City's own account of a member) |
| Demetrius G. Scofield #21584 | body | Demetrius G. Scofield was a San Francisco oil man who rose through the California Star Oil Works and the Pacific Coast Oil Company to be president of Standard... | Find a Grave |
| Demetrius G. Scofield #21584 | field | birthDate: about 1843 | Find a Grave |
| Demetrius G. Scofield #21584 | field | birthplace: New York City | Find a Grave |
| Remi Nadeau #18869 | body | He was a grandson of the Los Angeles freighter Remi Nadeau, and the son of the freighter's eldest son, Joseph Frye Nadeau. | Find a Grave |
| Remi Nadeau #18869 | body | He was born in Minnesota on 3 May 1867, and died on 25 November 1941 in Glendale; he is buried at Angelus Rosedale Cemetery in Los Angeles, in the lot where... | Find a Grave |
| Remi Nadeau #18869 | field | birthDate: May 3, 1867 | Find a Grave |
| Remi Nadeau #18869 | field | deathDate: November 25, 1941 | Find a Grave |
| Remi Nadeau #18869 | field | birthplace: Minnesota | Find a Grave |
| Remi Nadeau #18869 | field | burialPlace: Angelus Rosedale Cemetery, Los Angeles | Find a Grave |
| Jeri Seratti #29113 | body | Jeri Seratti, also known as Jeri Seratti Goldman, has co-owned and run the valley's radio station since 2003, when AM 1220 was bought back from Clear Channel... | santaclarita.gov/commission-information/arts-commission |
| Jeri Seratti #29113 | body | After the Northridge earthquake of 1994 the station was the valley's line of information through the months of recovery, and she directed its full-time... | santaclarita.gov/commission-information/arts-commission |
| Jeri Seratti #29113 | body | She sits on the City's Arts Commission, her term running to 2028. | santaclarita.gov/commission-information/arts-commission |
| Jeri Seratti #29113 | affiliation #29116 | Jeri Seratti, Commissioner, Arts Commission: 2028 | santaclarita.gov/commission-information/arts-commission |
| Michael Millar #29214 | body | Michael Millar was the founding chair of the City's Arts Commission, from 2009 to 2011, and sits on it again, his term running to December 2026. An arts... | santaclarita.gov/commission-information/arts-commission |
| Michael Millar #29214 | body | He has taught music at Cal Poly Pomona since 2004 and directed its Center for Community Engagement. A bass trombonist, he played on the Grammy-winning 2004... | santaclarita.gov/commission-information/arts-commission |
| Michael Millar #29214 | affiliation #29218 | Michael Millar, Founding Chair, Arts Commission: 2009 to 2011 | santaclarita.gov/commission-information/arts-commission |
| Michael Millar #29214 | affiliation #29216 | Michael Millar, Commissioner, Arts Commission: December 2026 | santaclarita.gov/commission-information/arts-commission |
| Patti Rasmussen #2591 | body | Patti Rasmussen, a freelance journalist who has lived in Newhall since the mid-1970s, was the education reporter for The Signal. | santaclarita.gov/commission-information/arts-commission |
| Patti Rasmussen #2591 | body | After the Northridge earthquake of 1994, the City says, her backyard served as a theatre while Hart High School repaired its auditorium. | santaclarita.gov/commission-information/arts-commission |
| Patti Rasmussen #2591 | body | She served on the board of the Santa Clarita Valley Historical Society as its educational outreach chair, was an original member of the Arts Alliance, and has... | santaclarita.gov/commission-information/arts-commission |
| Patti Rasmussen #2591 | affiliation #29098 | Patti Rasmussen, Board member, Educational Outreach Chair, Santa Clarita Valley Historical Society: | santaclarita.gov/commission-information/arts-commission |
| Patti Rasmussen #2591 | affiliation #29096 | Patti Rasmussen, Education Reporter, The Santa Clarita Valley Signal: | santaclarita.gov/commission-information/arts-commission |
| Patti Rasmussen #2591 | affiliation #29094 | Patti Rasmussen, Commissioner, Arts Commission: December 2026 | santaclarita.gov/commission-information/arts-commission |
| Susan Shapiro #29100 | body | Susan Shapiro came to Santa Clarita in 1993 to manage the local public television channel, SCVTV, and was the cable company's community television manager... | santaclarita.gov/commission-information/arts-commission |
| Susan Shapiro #29100 | body | She sat on the City's Newhall Redevelopment Committee from 2002 to 2009 and its Citizens Public Library Advisory Committee in 2010 and 2011, and on the Hart... | santaclarita.gov/commission-information/arts-commission |
| Susan Shapiro #29100 | affiliation #29102 | Susan Shapiro, Chair, Arts Commission: December 2026 | santaclarita.gov/commission-information/arts-commission |
| Di Thompson #29208 | body | Di Thompson chairs the City's Parks, Recreation and Community Services Commission; her term runs to December 2026. A resident of Santa Clarita for 24 years,... | santaclarita.gov/commission-information/parks-recreation-and-community-services |
| Di Thompson #29208 | affiliation #29212 | Di Thompson, Board of Directors; 2025 Chair Elect, Santa Clarita Valley Chamber of Commerce: | santaclarita.gov/commission-information/parks-recreation-and-community-services |
| Di Thompson #29208 | affiliation #29210 | Di Thompson, Chair, Parks, Recreation and Community Services Commission: December 2026 | santaclarita.gov/commission-information/parks-recreation-and-community-services |
| Lisa Eichman #29109 | body | Lisa Eichman has been a member of the City's Planning Commission since October 2010; her present term runs to December 2026. She has lived in Valencia since... | santaclarita.gov/commission-information/planning-commission |
| Lisa Eichman #29109 | affiliation #29111 | Lisa Eichman, Commissioner, Planning Commission: December 2026 | santaclarita.gov/commission-information/planning-commission |
| Nathan Keith #29203 | body | Nathan Keith chairs the City's Planning Commission; his term runs to December 2028. He came to Santa Clarita in 2001 to attend The Master's University,... | santaclarita.gov/commission-information/planning-commission |
| Nathan Keith #29203 | affiliation #29206 | Nathan Keith, Chairperson, Planning Commission: December 2028 | santaclarita.gov/commission-information/planning-commission |
| Tim Burkhart #29104 | body | Tim Burkhart, a graduate of William S. Hart High School who attended College of the Canyons and took bachelor's and master's degrees at California State... | santaclarita.gov/commission-information/planning-commission |
| Tim Burkhart #29104 | body | He retired as corporate vice president of maintenance and construction for Six Flags. | santaclarita.gov/commission-information/planning-commission |
| Tim Burkhart #29104 | affiliation #29106 | Tim Burkhart, Commissioner, Planning Commission: December 2028 | santaclarita.gov/commission-information/planning-commission |

No claim on a person record rests on Wikipedia alone; no footnote on a person record cites Wikipedia at all.

## Fields with no source located on the record

These print on the person page (birth, death, birthplace, burial) and no footnote on the record names them. Several have an evidence level set, which says someone rated a source the record does not cite. 25 of them sit on the seven people whose WordPress profile is withheld (Wilk, Manly, Marshall, Bryant, Anza, Portolá, Garcés): their fields came in with the withheld text.

| Person | Field | Value | Evidence set |
| --- | --- | --- | --- |
| Abel Stearns #309 | birthDate | February 9, 1798 | uncited |
| Abel Stearns #309 | burialPlace | Calvary Cemetery, Los Angeles, California | (none) |
| Almer Mayo Newhall #31730 | birthDate | May 14, 1881 | retrospective |
| Almer Mayo Newhall #31730 | burialPlace | Cypress Lawn Memorial Park, Colma, California | retrospective |
| Andrés Pico #317 | birthDate | November 18, 1810 | uncited |
| Andrés Pico #317 | birthplace | San Diego, Alta California | (none) |
| Andrés Pico #317 | burialPlace | Mission San Fernando Rey de España, Mission Hills, California | uncited |
| Andrés Pico #317 | deathDate | February 14, 1876 | retrospective |
| Arthur Buckingham Perkins #333 | burialPlace | (unknown) | (none) |
| Bob Kellar #21944 | birthDate | 1944 | retrospective |
| Cameron Smyth #16380 | birthDate | 1971 | uncited |
| Cave Johnson Couts #323 | burialPlace | El Campo Santo Cemetery, San Diego, California | (none) |
| Christopher Houston Carson #315 | burialPlace | Kit Carson Cemetery, Taos, New Mexico | uncited |
| Christopher Houston Carson #315 | deathDate | May 23, 1868 | uncited |
| Dan Hon #18616 | deathDate | December 1996 | contemporary |
| Dante Acosta #341 | birthDate | 1963 | contemporary |
| Dennis Koontz #23081 | birthDate | 1939 | retrospective |
| Edward Fitzgerald Beale #327 | burialPlace | Rock Creek Cemetery, Washington, D.C. | uncited |
| Edwin Bryant #313 | birthDate | 1805 | (none) |
| Edwin Bryant #313 | birthplace | Pelham, Massachusetts | (none) |
| Edwin Bryant #313 | burialPlace | Cave Hill Cemetery, Louisville, Kentucky | (none) |
| Edwin Bryant #313 | deathDate | December 16, 1869 | (none) |
| Edwin White Newhall #31728 | birthDate | May 7, 1856 | retrospective |
| Edwin White Newhall #31728 | burialPlace | Cypress Lawn Memorial Park, Colma, California | retrospective |
| Father Francisco Garcés #289 | birthDate | April 12, 1738 | (none) |
| Father Francisco Garcés #289 | birthplace | Morata de Jalón, Aragon, Spain | (none) |
| Father Francisco Garcés #289 | burialPlace | Yuma, Arizona | (none) |
| Father Francisco Garcés #289 | deathDate | July 18, 1781 | (none) |
| Francisco "Chico" López #28132 | burialPlace | Catholic Cemetery, Los Angeles | contemporary |
| Frank Ferry #23083 | birthDate | 1965 | retrospective |
| Gaspar de Portolá #295 | birthDate | c. 1727 | (none) |
| Gaspar de Portolá #295 | birthplace | Balaguer, Catalonia, Spain | (none) |
| Gaspar de Portolá #295 | deathDate | c. 1786 | (none) |
| Henry Clay Wiley #331 | burialPlace | Rosedale Cemetery, Los Angeles, California | contemporary |
| Henry Mayo Newhall #283 | birthDate | May 13, 1825 | (none) |
| Henry Mayo Newhall #283 | birthplace | Saugus, Massachusetts | (none) |
| Henry Mayo Newhall #283 | burialPlace | Cypress Lawn Memorial Park Colma, San Mateo County, California | (none) |
| Henry Mayo Newhall #283 | deathDate | March 13, 1882 | (none) |
| James Wilson Marshall #319 | birthDate | October 8, 1810 | (none) |
| James Wilson Marshall #319 | birthplace | Hope Township, New Jersey | (none) |
| James Wilson Marshall #319 | burialPlace | Marshall Gold Discovery State Historic Park, Coloma, California | (none) |
| James Wilson Marshall #319 | deathDate | August 10, 1885 | (none) |
| Jan Heidt #15737 | birthDate | 1939 | retrospective |
| Jerry Reynolds #281 | burialPlace | Eternal Valley Memorial Park, Newhall, California | uncited |
| Jill Klajic #15874 | birthDate | 1946 | retrospective |
| John C. Frémont #307 | burialPlace | Trinity Church Cemetery, New York City, New York | uncited |
| Juan Bautista de Anza #301 | birthDate | July 7, 1736 Death Date: December 19, 1 | (none) |
| Juan Bautista de Anza #301 | birthplace | ronteras, Sonora, New Spain | (none) |
| Juan Bautista de Anza #301 | burialPlace | Cathedral of Nuestra Señora de la Asunción, Arizpe, Sonora, Mexico | (none) |
| Juan Bautista de Anza #301 | deathDate | December 19, 1788 | (none) |
| Juan Crespí #297 | burialPlace | Mission San Carlos Borromeo, Carmel, California | uncited |
| Juan Crespí #297 | deathDate | January 1, 1782 | uncited |
| Junípero Serra #299 | birthDate | November 24, 1713 | retrospective |
| Junípero Serra #299 | birthplace | Petra, Mallorca, Spain | (none) |
| Junípero Serra #299 | burialPlace | Mission San Carlos Borromeo, Carmel, California | uncited |
| Junípero Serra #299 | deathDate | August 28, 1784 | retrospective |
| Juventino del Valle #303 | birthDate | 1841 | retrospective |
| Juventino del Valle #303 | deathDate | 1919 | contemporary |
| Laurene Weste #15929 | birthDate | 1948 | retrospective |
| Laurie Ender #23087 | birthDate | 1964 | retrospective |
| Leon Worden #279 | birthDate | 1962 | uncited |
| Pedro Fages #287 | birthDate | c. 1734 | retrospective |
| Pedro Fages #287 | deathDate | 1796 | retrospective |
| Rodolfo Acosta #343 | birthplace | El Paso, Texas | (none) |
| Rodolfo Acosta #343 | burialPlace | Forest Lawn Memorial Park, Hollywood Hills, California | uncited |
| Rosemarie Koscielny #25397 | birthDate | July 8, 1952 | retrospective |
| Rosemarie Koscielny #25397 | deathDate | August 13, 2026 | contemporary |
| Scott Newhall #31431 | burialPlace | Cypress Lawn Memorial Park, Colma, California | retrospective |
| Scott Thomas Wilk Sr. #335 | birthDate | 1959 | (none) |
| Scott Thomas Wilk Sr. #335 | birthplace | Lancaster, California | (none) |
| Thomas O. Larkin #311 | birthDate | September 16, 1802 | uncited |
| Thomas O. Larkin #311 | birthplace | Charlestown, Massachusetts | (none) |
| Thomas O. Larkin #311 | burialPlace | Mountain View Cemetery, Oakland, California | (none) |
| Thomas O. Larkin #311 | deathDate | October 27, 1858 | uncited |
| Tiburcio Vasquez #285 | birthplace | Monterey County, California | (none) |
| TimBen Boydston #21946 | birthDate | 1955 | retrospective |
| Tom Frew II #31354 | birthDate | April 2, 1852 | retrospective |
| Tom Frew IV #18783 | birthDate | Thanksgiving Day, 1928 | retrospective |
| William Lewis Manly #321 | birthDate | April 6, 1820 | (none) |
| William Lewis Manly #321 | birthplace | St. Albans, Vermont | (none) |
| William Lewis Manly #321 | burialPlace | Oak Hill Cemetery, San Jose, California | (none) |
| William Lewis Manly #321 | deathDate | February 5, 1903 | (none) |
| Ygnacio del Valle #293 | birthDate | July 1, 1808 | (none) |
| Ygnacio del Valle #293 | birthplace | Jalisco, Mexico | (none) |
| Ygnacio del Valle #293 | burialPlace | Camulos Ranch, Ventura County, California | (none) |
| Ygnacio del Valle #293 | deathDate | 1880 | (none) |

### Other claims with no source counted

| Person | Kind | Claim | What its notes are |
| --- | --- | --- | --- |
| Leon Worden #279 | body | He is its editor and the author of much of it: more than two hundred of its records name him as their author. | note |
| Tom Lackey #29456 | body | Most of the valley was then in the 38th, so through those years it had two Assembly members at once: Lackey, and Scott Wilk until 2016, then Dante Acosta, Christy Smith and Suzette Martinez... | note |
| Father Francisco Garcés #289 | calendar | Francisco Tomás Hermenegildo Garcés was born April 12, 1738 in Zaragoza, Aragon, Spain. He entered the Franciscan order at age twenty-five and was assigned to Mission San Xavier de Bac in present-day | no footnote |
| Father Francisco Garcés #289 | calendar | pent the next five years nurturing his mission colony at the Yuma crossing. On July 18, 1781, the Yuma rose in revolt against Spanish soldiers stationed there and killed every Spaniard in sight. Garcé | no footnote |
| Henry Mayo Newhall #283 | calendar | Born May 13, 1825, in the industrial town of Saugus, Massachusetts, Henry Mayo Newhall sought his fortune in the California Gold Rush of 1849. He failed as a miner but succeeded as an auctioneer in br | no footnote |
| Juan Bautista de Anza #301 | calendar | Juan Bautista de Anza (July 7, 1736 – December 19, 1788) was a Spanish colonial military officer born in Fronteras, Sonora, New Spain, who led the two overland expeditions that established the land ro | no footnote |
| Juan Crespí #297 | calendar | Father Juan Crespí (March 1, 1721 – January 1, 1782) was a Franciscan friar from Palma, Mallorca, Spain, best known as the chief diarist of the Portolá Expedition of 1769: the first European overland | no footnote |
| Juan Crespí #297 | calendar | Father Juan Crespí (March 1, 1721 – January 1, 1782) was a Franciscan friar from Palma, Mallorca, Spain, best known as the chief diarist of the Portolá Expedition of 1769: the first European overland | no footnote |
| Junípero Serra #299 | calendar | Father Junípero Serra (November 24, 1713 – August 28, 1784) was a Spanish Franciscan friar who founded nine of California's twenty-one missions and served as the first Father-Presidente of the Califor | no footnote |
| Junípero Serra #299 | calendar | Father Junípero Serra (November 24, 1713 – August 28, 1784) was a Spanish Franciscan friar who founded nine of California's twenty-one missions and served as the first Father-Presidente of the Califor | no footnote |
| Rodolfo Acosta #343 | calendar | Rodolfo Pérez Acosta (July 29, 1920 – November 7, 1974) was an American actor who became known for his roles as Mexican outlaws and American Indians in Hollywood western films. Born in the El Segundo | no footnote |
| Rodolfo Acosta #343 | calendar | Rodolfo Pérez Acosta (July 29, 1920 – November 7, 1974) was an American actor who became known for his roles as Mexican outlaws and American Indians in Hollywood western films. Born in the El Segundo | no footnote |
| Sanford Lyon #20224 | calendar | born, per Reynolds; retrospective | no footnote |
| William S. Hart #16356 | calendar | born at Newburgh, New York (the year disputed) | no footnote |
| William Wirt Jenkins #20226 | calendar | died, per the death certificate as the legacy page cites it | no footnote |
| Ygnacio del Valle #293 | calendar | Ygnacio Ramón de Jesus del Valle (sometimes spelled Ignacio) was born July 1, 1808 in Jalisco, Mexico, to Lt. Antonio Seferino del Valle and María Josepha (Carillo) del Valle. In March 1828, Ygnacio w | no footnote |
| Dan Masnada #28326 | candidacy #26685 | DAN MASNADA: Santa Clarita Valley Water board election, Division 1, November 5, 2024: elected | note |
| Gary Martin #26587 | candidacy #26683 | GARY MARTIN: Santa Clarita Valley Water board election, Division 1, November 5, 2024: elected | note |
| Paula Olivares #26597 | candidacy #26687 | PAULA OLIVARES: Santa Clarita Valley Water board election, Division 1, November 5, 2024: not-elected | note |
| Dante Acosta #341 | kin | childOf: Rodolfo Acosta | no footnote |
| Ygnacio del Valle #293 | kin | childOf: Antonio del Valle | no footnote |

## Read from a description

Claims stated as fact where the record's own footnote says the point was read from something about the source, not the source. Listed, not fixed.

1. **Antonio del Valle #291, body, the patent acreage.** "The United States patent of 1875, issued after the heirs' claim was confirmed, was for 48,611.88 acres." Footnote 4: "As read for the archive's del Valle source review of 29 September 2026; the patent itself is not in the archive." The figure comes from the source review's account of the patent (a dossier), not the patent. Its span also cites Reynolds, but Reynolds gives the diseño's 48,829, not the patent's figure, so for 48,611.88 the review is the only leg.
2. **Sanford Lyon #20224, body.** "He was at the Pico Canyon oil seeps by 1866, when a Los Angeles paper reported that 'Mr. S. Lyon is busy dipping the oil from the holes.'" Footnote: "Los Angeles Semi-Weekly News, 1 June 1866, quoted in A.B. Perkins, 'History of Pico Canyon Oil Production'... record #1440, note 16." The 1866 paper has not been read; the quotation is Perkins's. A Perkins transcription is what the trust rule accepts, so this is within the rule, but the sentence presents the paper, not Perkins, as the witness.
3. **Antonio del Valle #291, deathDate "on or before 3 June 1841" and the body sentence "He died without a will on or before 3 June 1841, the day he was buried at Mission San Fernando."** One source: the ECPP index, "a typed transcription of the registers, not the register page, which has not been seen", and the identification of "Antonio Valle" with Antonio del Valle "is an inference". The caveats are on the record (footnote 7), but the date field states the date with `deathEvidence` contemporary, and it is the date that replaced two published ones on 4 October. It stands on an index of the register, and on an inference, alone.
4. **Katie Hill #29332, body.** The civil statute under which she sued is quoted "as quoted in the ruling cited in the next note"; the Civil Code section itself was not read. Minor: the ruling is a court's own quotation.

## Found on the way

- **Juan Bautista de Anza #301: two fields are broken.** `birthDate` reads "July 7, 1736 Death Date: December 19, 1" (the death label and part of the death date ran into the field) and `birthplace` reads "ronteras, Sonora, New Spain" (first letter lost; Fronteras). His body is withheld WordPress text, but the fields print.
- **On This Day publishes 14 dates from person records that cite nothing for them.** They are `recordDates` rows ticked Confirmed, so they reach the calendar, and no footnote on the record names them. Eleven of them carry as their label a sentence of a withheld WordPress profile, on seven people (Garcés twice, Henry Mayo Newhall, Anza, Crespí twice, Serra twice, Rodolfo Acosta twice, Ygnacio del Valle), several in the form of an encyclopedia lead, "Juan Bautista de Anza (July 7, 1736 - December 19, 1788) was a Spanish colonial military officer..."; where they came from is not recorded, so they are unsourced leads, not facts. Henry Mayo Newhall's is "Born May 13, 1825", the day his dossier holds open against 23 May.
- **Henry Mayo Newhall #283, `birthDate` "May 13, 1825", no footnote on the record.** The HMN dossier (inventory/review/henry-mayo-newhall-sources.md, C1) records 13 May against 23 May as open. Passed to the disputed-as-fact report (item 12).
- **Abel Stearns #309, `birthDate` "February 9, 1798", `birthEvidence` uncited.** His own editor note says no source has been found for the day; the field still prints it as a full date.
- **Bodies with no footnote markers: 9.** Seven are withheld WordPress text; two publish as Leon's own (`legacy-leon`): Ygnacio del Valle #293 and Henry Mayo Newhall #283. Each is one source, Leon, for every sentence in it, and neither record carries a footnote.
- **Uncited closing sentences in footnoted profiles: 6**, all the archive's own remarks ("No source in this archive places him in the Santa Clarita Valley."), listed in the JSON.

## Every one-source claim, by person

Profile spans, fields, kinship and calendar dates by person; then terms, affiliations, education and candidacies by the source they rest on. "H" marks a profile sentence that attributes or hedges its point.

### Aakash Ahuja (#25449): 2

- "He first stood for public office in the Santa Clarita City Council election of November 2020, where he came sixth of nine with 14,300 votes; his ballot designation was "Doctor/Father/Businessman."" (County returns (CEDA or the County's statement))
- "In November 2024 he won Trustee Area 1 of the Hart board, first of three candidates with 8,888 votes, as "Father/Psychiatrist/Educator."" (County returns (CEDA or the County's statement))

### Abel Stearns (#309): 11

- "He came from Lunenburg, Massachusetts." (homesteadmuseum.blog/2018/09/27/on-this-day-the-probate-of-abel-stearns-27-september-1871)
- "He opened a store in the pueblo, kept a warehouse at San Pedro for imported goods and built an early flour mill north of the town." (homesteadmuseum.blog/2018/09/27/on-this-day-the-probate-of-abel-stearns-27-september-1871)
- "He married Arcadia Bandini, who was fourteen to his forty-odd; they had no children, and lived in a large adobe on Main Street called El Palacio." (homesteadmuseum.blog/2018/09/27/on-this-day-the-probate-of-abel-stearns-27-september-1871)
- "In 1842 he bought Rancho Los Alamitos, and by the mid-1860s he held Las Bolsas, La Bolsa Chica, Los Coyotes, La Habra and San Juan Cajón de Santa Ana, most of them in what is now Orange County, and Jurupa and La Sierra near Riverside." (homesteadmuseum.blog/2018/09/27/on-this-day-the-probate-of-abel-stearns-27-september-1871)
- H "His tie to the Santa Clarita Valley is gold. In March 1842 Francisco Lopez found placer gold at a place Stearns called San Francisquito, about thirty-five miles north-west of Los Angeles. On 22 November 1842 Stearns sent twenty ounces of it by Alfred..." ("abel stearns tells of lopez 1842 gold discovery; no mention of dream.")
- H "That letter is the earliest account the archive holds, and two things are not in it: a dream, and any particular oak. Both enter the story in 1930. Nor is it settled what Stearns meant by "San Francisquito": San Francisquito Canyon, where placer gold is..." ("san francisquito.")
- H "He was alcalde of Los Angeles after the American seizure of the pueblo in 1847, a member of the ayuntamiento and then of the Common Council, a county supervisor and a member of the State Assembly, and the region elected him to the convention that wrote..." (homesteadmuseum.blog/2018/09/27/on-this-day-the-probate-of-abel-stearns-27-september-1871)
- "The droughts and floods that ended the cattle economy left him rich in land and short of money. His friend Alfred Robinson formed the Robinson Trust, which took over what were called the Stearns Ranchos and sold their nearly 180,000 acres, largely in..." (homesteadmuseum.blog/2018/09/27/on-this-day-the-probate-of-abel-stearns-27-september-1871)
- "He died in San Francisco, on a business trip, on 23 August 1871. His will of 12 March 1870, witnessed among others by Pío Pico, was admitted to probate in Los Angeles on 27 September 1871, and Arcadia was his sole heir." (homesteadmuseum.blog/2018/09/27/on-this-day-the-probate-of-abel-stearns-27-september-1871)
- field `deathDate` = August 23, 1871 (homesteadmuseum.blog/2018/09/27/on-this-day-the-probate-of-abel-stearns-27-september-1871)
- field `birthplace` = Lunenburg, Massachusetts (homesteadmuseum.blog/2018/09/27/on-this-day-the-probate-of-abel-stearns-27-september-1871)

### Adrian W. Adams (#28667): 12

- H "The telephone directories of 1957 and 1958 list him as an attorney on West Market Street, and the city directory of 1969 at the same address." (archive record #5685, "Newhall-Saugus-Valencia-Canyon Country (photographs)")
- H "Leon Worden's roster of the William S. Hart Union High School District board lists an Adrian Adams on the board from 1957 to 1962." (Hart board roster (Leon Worden))
- H "The district's commencement programs of 1958, 1960 and 1961 name "Mr. Adrian W. Adams" first among its trustees, and the yearbook of 1962 names him president of the board, over a message to the students." ("mr. adrian w. adams")
- H "No source found says in words that the trustee was the Newhall lawyer; the directories of those years list one Adrian W. Adams in the valley, and he is the attorney." (archive record #5685, "Newhall-Saugus-Valencia-Canyon Country (photographs)")
- H "On January 27, 1970, Governor Ronald Reagan appointed him to the Newhall Municipal Court. "Newhall became a two-judge town yesterday," The Signal reported: C.M. MacDougall, already on the bench, had been the district's only judge. The Signal described Adams..." ("judge adrian w. adams")
- "In 1983, at the installation of Judge H. Keith Byram, he remembered his own swearing-in at the old courthouse on Market Street, where on a rainy day "we'd have to have buckets on the floor."" ("judge byram formally installed")
- "SCVHistory.com's note on him counts among the highlights of his years as a judge the arraignment of the suspects in the Newhall Incident, the killing of four Highway Patrol officers in April 1970, "just months after his appointment to the bench."" ("judge adrian w. adams")
- H "The note speaks of two suspects. Only one survived to be charged: the gunman who held a householder hostage killed himself on the morning of April 6, and the Highway Patrol reported that "the surviving suspect" had been held to answer in the Superior Court." (archive record #31376, "The Newhall Incident (events)")
- "John Fuller, the board's first treasurer, remembered that the Lutheran Hospital Society, Newhall Land "and (Municipal Court Judge) Adrian Adams brought together a group of about 25 people to be the board."" ("john stevens fuller")
- H "In April 1974 he spoke at the dedication of College of the Canyons' first permanent building, a tribute to Dr. William G. Bonelli. One caption calls him a trustee of the college; another, of the same ceremony, calls him a Municipal Court judge, and the..." (canyons.edu/administration/board/history.php)
- "He was the valley's Man of the Year for 1980." ("scv man & woman of the year")
- "In April 2003, near the fiftieth anniversary of his practice, The Signal published his history of the Newhall court, drawn from its dockets." ("tales of the newhall court")

### Alan Ferdman (#25191): 5

- "His 2016 campaign put his career at forty years with Litton and six with JPL." (santaclaritamagazine.com/2016/10/vote-alan-ferdman-city-council)
- H "The nomination also lists the City's 2006 advisory committees on choosing a police chief and filling a council seat, the Whittaker-Bermite Citizens Advisory Group, and a weekly column in the SCV Gazette." (hometownstation.com/santa-clarita-news/community-news/2020-scv-man-and-woman-of-the-year-nominees-316046)
- "He stood for the council in April 2014 as "Community Committee Chairperson" and came fourth of thirteen, with 4,833 votes; and in November 2016 as "CEO Non-Profit," fourth of eleven, with 12,106." (County returns (CEDA or the County's statement))
- "Announcing the second run in January 2016, he campaigned on traffic, on what he called back-room dealing, citing the council's 2014 vote on electronic billboards, and on public safety." (hometownstation.com/santa-clarita-news/politics/alan-ferdman-to-run-for-santa-clarita-city-council-seat-in-2016-election-166287)
- H "In 2020 the Samuel Dixon Family Health Center nominated him for Santa Clarita Valley Man of the Year; its nomination also says he had been named among the valley's "Top 51 Most Influential People."" (hometownstation.com/santa-clarita-news/community-news/2020-scv-man-and-woman-of-the-year-nominees-316046)

### Almer Mayo Newhall (#31730): 3

- H "At the time of the St. Francis Dam disaster in 1928 he was assistant to the president of The Newhall Land and Farming Company, his cousin George A. Newhall Jr., and in 1930, as president of the San Francisco Chamber of Commerce, he placed the first..." ("hmn grandson almer m. newhall makes first long-distance call from s.f. to buenos aires, 1930")
- "He died on January 14, 1933." ("colma cemeteries: henry mayo newhall & heirs")
- field `deathDate` = January 14, 1933 ("colma cemeteries: henry mayo newhall & heirs")

### Andrés Pico (#317): 4

- "Andrés Pico was a Californio military commander and landholder. The Santa Clarita Valley carries his name in Pico Canyon, where he was among the first to file oil claims in 1865." (Perkins/Reynolds (one source, PROFILES.md))
- H "In the Mexican-American War he led the Californio lancers who met General Stephen W. Kearny's dragoons at San Pasqual, near present-day Escondido, on 6 December 1846, in what the state park's account calls the bloodiest of the California battles. The sources..." (scvhistory.com/scvhistory/lw3359.htm)
- "On 13 January 1847, at the home of María Jesus Lopez de Felíz near Cahuenga Pass, he surrendered to John C. Frémont and signed the articles of capitulation that ended the war in California." (Perkins/Reynolds (one source, PROFILES.md))
- "When the first oil claims were filed in the hills north of the pass in 1865, Perkins names Pico among the filers." (Perkins/Reynolds (one source, PROFILES.md))

### Antonio del Valle (#291): 18

- "Antonio del Valle was a Mexican army lieutenant who came to California in 1819, took charge of Mission San Fernando when it was secularized, and in 1839 received Rancho San Francisco, the grant that took in most of the Santa Clarita Valley." (Perkins/Reynolds (one source, PROFILES.md))
- "He died on the rancho two years later without a will, and his heirs held it in undivided shares until it was partitioned in 1870." (Perkins/Reynolds (one source, PROFILES.md))
- H "Perkins, drawing on Bancroft, says he came from Jalisco as a lieutenant in the Company of San Blas in 1819. His son Ygnacio, from his first marriage, joined him at Monterey in 1825, at seventeen." (Perkins/Reynolds (one source, PROFILES.md))
- "Jerry Reynolds's ages for him, forty-six in 1834 and fifty-three at his death, would put his birth about 1788." (Perkins/Reynolds (one source, PROFILES.md))
- H "His inventory of 26 July 1835 valued the mission buildings at $15,511. He reported that horses were being stolen and blamed Indians who had taken refuge at the mission, asked for a corporal to be posted at the Rancho San Francisco, and accused Fr. Ibarra of..." (scvhistory.com/scvhistory/engelhardt_sanfernando56.htm)
- "He then sought the rancho for himself. Governor Juan B. Alvarado granted it on 22 January 1839, over a protest from Fr. Narciso Durán, prefect of the southern missions." (Perkins/Reynolds (one source, PROFILES.md))
- H "According to Engelhardt, when the inspector of missions, William Hartnell, visited San Fernando in June 1839 he found the Indians angry that the rancho "had been taken from them and given to Antonio del Valle," so angry that del Valle feared to trust himself..." (scvhistory.com/scvhistory/engelhardt_sanfernando56.htm)
- "He moved them into the old Estancia de San Francisco Xavier, the mission outpost of 1804 above the junction of Castaic Creek and the Santa Clara River, which became the rancho house." (Perkins/Reynolds (one source, PROFILES.md))
- H "His second wife was Jacoba Feliz. A daughter, María Magdalena Antonia, was baptized at the Santa Barbara Presidio on 4 July 1831, born the day before; the register names her mother as María Policarpa López and her father, "Antonio Valles," as a widower." (ECPP record 976)
- "He died without a will on or before 3 June 1841, the day he was buried at Mission San Fernando." (ECPP record 72898)
- H "Jerry Reynolds gives his death as 21 June and Perkins as 12 June; both are after the burial, so neither can be right." (Perkins/Reynolds (one source, PROFILES.md))
- "The heirs were his widow, Ygnacio and the children of both marriages, though the sources count them differently." (Perkins/Reynolds (one source, PROFILES.md))
- "The probate court distributed the rancho to them in undivided portions, and it was not partitioned until 1870, when Camulos, 1,340 acres, was set apart for Ygnacio." (Perkins/Reynolds (one source, PROFILES.md))
- "Writers have judged him as differently as they have dated him. Engelhardt, writing in 1927 from the Franciscan side, presents the del Valles as taking the rancho "against the will of the neophytes," and Leon Worden's introduction to the passage warns of his..." (scvhistory.com/scvhistory/engelhardt_sanfernando56.htm)
- "A 1999 newspaper feature called Antonio "a Mexican-born missionary," which he was not." (scvhistory.com/scvhistory/sg090199a.htm)
- field `birthDate` = 1788 (Perkins/Reynolds (one source, PROFILES.md))
- field `deathDate` = on or before 3 June 1841 (ECPP record 72898)
- field `birthplace` = Jalisco, Mexico (Perkins/Reynolds (one source, PROFILES.md))

### Arthur Buckingham Perkins (#333): 10

- H "Most of what is known of his early life comes from a profile in the Los Angeles Times in January 1977, and most of that from Perkins himself. By his account he was born in Bennington, Vermont, was raised by aunts after his mother died young, spent part of..." ("history of santa clarita uncovered by inquisitive man with an ice pick")
- "He would not tell the Times his age; it guessed 88, which would put his birth about 1888." ("history of santa clarita uncovered by inquisitive man with an ice pick")
- "SCV Water's own history of the company settles it: he came as its general manager in 1919 and bought it a year later." (yourscvwater.com/who-we-are/history)
- H "His own histories name him among the founders of the Masonic Club and the Kiwanis." ("history of santa clarita uncovered by inquisitive man with an ice pick")
- "In 1956 he built office bungalows at 22508 Sixth Street, named Perkins Court, which The Signal rented from him until his death and from his heirs until 1986; they are better known as the red Signal buildings." (scvhistory.com/scvhistory/sg6002.htm)
- "In 1970 the widening of Lyons Avenue displaced him from the house he had lived in for 45 years." (scvhistory.com/scvhistory/lw3550.htm)
- "In the early 1960s Ted Lamkin photographed the pictures Perkins had collected; his roughly 1,100 negatives record the collection as it stood in 1963. They turned up in 1996 and were the set that launched this archive." (scvhistory.com/scvhistory/lw3113.htm)
- "The books are now in the Special Collections reading room at College of the Canyons: 206 titles in 2019." (scvhistory.com/scvhistory/lw3550.htm)
- "And Reynolds, the valley's second town historian, worked from Perkins's photographs and cites his history of the rancho, so Perkins and Reynolds agreeing on a point are not two independent witnesses: a mistake of Perkins's can reach a reader through either." (Perkins/Reynolds (one source, PROFILES.md))
- field `birthplace` = Bennington, Vermont ("history of santa clarita uncovered by inquisitive man with an ice pick")

### Audra Strickland (#29450): 11

- "Audra Strickland was one of the Santa Clarita Valley's members of the State Assembly from December 2004 to December 2010, for the 37th District." (Secretary of the Senate, Record of Members)
- "Under the lines drawn in 2001 the 37th held Castaic, Val Verde, Hasley Canyon, Agua Dulce and Green Valley: 12.9 per cent of the valley's people by the archive's count from the 2010 census, in a district that was mostly Ventura County. The rest of the valley..." (archive record #29342, "Keith Richman: State Assemblymember, California State Assembly (officeHoldings)")
- "The Acorn of Thousand Oaks described her district in 2009 as covering "nearly half of Ventura County, including the cities of Thousand Oaks, Moorpark, Simi Valley and Camarillo."" (toacorn.com/articles/member-of-assembly-to-seek-seat-in-ventura-county)
- H "In August 2003 Roll Call reported that he was helping "his wife, Audra Strickland, a one-time chief of staff to former California Assembly Speaker Curt Pringle (R), in a tough primary to win his Ventura County-based legislative seat in 2004."" (rollcall.com/2003/08/29/lonely-runners)
- "Before her election she had been, in the Acorn's words, "a political aide and a private-school teacher."" (toacorn.com/articles/member-of-assembly-to-seek-seat-in-ventura-county)
- "In the Republican primary of 2 March 2004 she won with 17,845 votes, 35.6 per cent, ahead of Jeff Gorell with 16,086 and Mike Robinson with 15,262," (California Secretary of State, Statement of Vote)
- "and in November she beat the Democrat, Ferial Masry, by 100,309 votes to 74,774." (California Secretary of State, Statement of Vote)
- "She beat Masry again in 2006 and in 2008." (California Secretary of State, Statement of Vote)
- "The Acorn put it in 2009: "She succeeded her husband, Tony Strickland, who's now a state senator."" (toacorn.com/articles/member-of-assembly-to-seek-seat-in-ventura-county)
- "Term limits ended her service in 2010." (toacorn.com/articles/member-of-assembly-to-seek-seat-in-ventura-county)
- "Jeff Gorell, whom she had beaten in the 2004 primary, won the seat that November and succeeded her." (California Secretary of State, Statement of Vote)

### BJ Atkins (#28316): 15

- "B. J. Atkins served on the Santa Clarita Valley's water boards for more than sixteen years, from 2005 to 2022: on the board of the Newhall County Water District, on the board of the Castaic Lake Water Agency as the Newhall district's appointee, and on the..." (yourscvwater.com/sites/default/files/scvwa/board-meetings/2022/scv-water-board-handout-082622-item-5.1-revised-resoluiton-b.-j.-atkins-2.pdf)
- "He was first elected to the Newhall County Water District board on 8 November 2005, third of six candidates for three seats, with 4,061 votes; his ballot designation was "Environmental Consultant."" (smartvoter.org/2005/11/08/ca/la/race/139)
- "He was re-elected on 3 November 2009, first of four candidates for three seats with 1,743 votes," (smartvoter.org/2009/11/03/ca/la/race/02086000)
- "and on 5 November 2013, second of four by the election-night count." (scvnews.com/election-results)
- "In January 2013 he and the board's president, Maria Gutzeit, appeared on SCVTV's "Newsmaker of the Week" to discuss the Castaic Lake Water Agency's acquisition of the Valencia Water Company." (scvtv.com/html/notw353.html)
- "He supported the merger of the two agencies into the Santa Clarita Valley Water Agency," (yourscvwater.com/sites/default/files/scvwa/board-meetings/2022/scv-water-board-handout-082622-item-5.1-revised-resoluiton-b.-j.-atkins-2.pdf)
- "and when it began on 1 January 2018 he was one of its fifteen founding directors, seated by the act that created it, which made the five sitting directors of the Newhall district members of its first board." (SB 634 (2017))
- "He was re-elected for Division 3 on 3 November 2020, first of four candidates for two seats, with 16,883 votes." (County returns (CEDA or the County's statement))
- "He also chaired the Santa Clarita Valley Groundwater Sustainability Agency." (scvnews.com/scv-water-board-director-bj-atkins-plans-to-resign)
- H "In December 2020 he said he would resign, as he would be moving out of Division 3;" (scvnews.com/scv-water-board-director-bj-atkins-plans-to-resign)
- "construction delays put the move back." (scvnews.com/atkins-delays-resigning-from-scv-water-board)
- "He resigned in July 2022. "My letter of resignation went in two days ago," he told The Signal on 13 July. "It becomes effective midnight on the 20th."" (signalscv.com/2022/07/atkins-to-resign-from-scv-water-board)
- H "The agency's resolution honoring him gives his service on its board as ending on 19 July 2022." (yourscvwater.com/sites/default/files/scvwa/board-meetings/2022/scv-water-board-handout-082622-item-5.1-revised-resoluiton-b.-j.-atkins-2.pdf)
- "At the end of August the board chose Kenneth Petersen to serve the rest of the term." ("new scv water board member appointed to represent division 3")
- H "Outside the water boards he is an environmental consultant. By his own company's account he graduated from Humboldt State University in 1978 with a degree in Oceanography and Economics, and founded Environmental HELP, Inc., on 5 May 1989." (environmentalhelp.net/history)

### Bill Cooper (#26946): 4

- "He was elected to the Castaic Lake board at large in November 2016, printed on the ballot as William Cooper, with 43,841 votes to Lynne Plambeck's 35,623; and to the SCV Water board from Division 1 in November 2022, with 15,251 votes against 4,743 for Nicole..." ("castaic lake water agency board election, november 8, 2016")
- "In August 2026 he announced that he would stand again in Division 1 that November." (hometownstation.com/santa-clarita-news/politics/santa-clarita-elections/bill-cooper-announces-campaign-for-re-election-to-scv-water-agency-board-604476)
- H "He is, he says, a Navy veteran of three tours in the Vietnam War, and has lived in the Santa Clarita Valley since 1972." (hometownstation.com/santa-clarita-news/politics/santa-clarita-elections/bill-cooper-announces-campaign-for-re-election-to-scv-water-agency-board-604476)
- H "His campaign site lists his service on the governing board of the Child and Family Center, and on the City's Elected Officials Committee on teenage alcohol and drug abuse." (billcooperforwater.com/about)

### Bill Miranda (#23089): 3

- H "He has been the city's mayor twice: in 2021, and in 2024 or 2025, since the City's page gives both." (City of Santa Clarita biographies (the City's own account of a member))
- "He was elected in November 2018, third of fifteen candidates with 18,885 votes, and re-elected in November 2022, second of nine with 32,306." ("archive records: the city council elections of 2018 and 2022, with their returns")
- H "The City's biography describes him as an Air Force veteran, a business owner, a former chief executive of the Santa Clarita Valley Latino Chamber of Commerce, and the host of more than two hundred episodes of SCV 101 on SCVTV. It says he worked for IBM,..." (City of Santa Clarita biographies (the City's own account of a member))

### Bill Thomas (#29464): 2

- "He won the 22nd in November 2002 with 73.4 per cent of the vote and was re-elected unopposed in 2004." (California Secretary of State, Statement of Vote)
- "By then he had been in the House since January 1979. William Marshall Thomas taught at Bakersfield Community College from 1965 to 1974 and sat in the State Assembly from 1974 to 1978 before he was elected to Congress as a Republican. In the House he chaired..." (Biographical Directory of the U.S. Congress)

### Bob Jensen (#28322): 2

- H "He holds the Trustee Area 2 seat for the term 2022 to 2026 and is the board's assistant clerk; the district says he has served as its president several times, without giving the years." (hartdistrict.org/apps/pages/governing-board-members)
- "He is a member of the Saugus-Hart School Facilities Financing Authority and of the William S. Hart Joint School Financing Authority, and has been a board member of the Santa Clarita Valley Chamber of Commerce." (hartdistrict.org/apps/pages/governing-board-members)

### Bob Kellar (#21944): 5

- H "Before the council he served in the United States Army from 1965 to 1967 and then spent 25 years with the Los Angeles Police Department, retiring in 1993 as supervisor in charge of reserve officer training at the Police Academy, as the City's 2013 biography..." (City of Santa Clarita biographies (the City's own account of a member))
- H "A column of August 1997 calls him a retired Los Angeles police officer." (archive record #12408, "Valley Fair attracts families, not gangs (articles)")
- H "By the City's account he was president of the Canyon Country Chamber of Commerce from 1993 until it joined the Santa Clarita Valley Chamber of Commerce in 1995, and helped bring Canyon Country into the valley chamber." (City of Santa Clarita biographies (the City's own account of a member))
- H "The City's biography lists him as president of the Santa Clarita Division of the Southland Regional Association of Realtors in 2000; a member of the Santa Clarita Valley Veterans Memorial Committee for more than twenty years by 2013, and its president in..." (City of Santa Clarita biographies (the City's own account of a member))
- H "On the council, the City says, he worked against the proposed CEMEX mining operation and to bring the parties together over the cleanup of the Whittaker-Bermite site." (City of Santa Clarita biographies (the City's own account of a member))

### Brian Walters (#25391): 1

- "The district's biography of him, which he most likely supplied, adds what no other source in hand confirms: that he co-founded the William S. Hart District (WiSH) Education Foundation and twice chaired its board, that he holds a joint BA and MPA from The..." (newhallschooldistrict.com/domain/11)

### Buck McKeon (#18791): 2

- "He was elected to Congress in 1992 from California's 25th District and re-elected ten times, serving from 3 January 1993 to 3 January 2015, and did not stand in 2014." (Biographical Directory of the U.S. Congress)
- "The Directory also records his membership of the California Republican Central Committee from 1988 to 1992." (Biographical Directory of the U.S. Congress)

### Cameron Smyth (#16380): 2

- "He won the Assembly seat in November 2006 and was re-elected in 2008 and 2010." (California Secretary of State, Statement of Vote)
- H "His Assembly biography also records that he chaired the Republican Caucus after the 2008 election, and credits him with the Surrogate Stalker Act of 2008, a part in passing the film production tax credit of 2009, and Legislator of the Year awards from the..." (arc.asm.ca.gov/member/38/?p=bio)

### Carl Boyer (#15808): 6

- "He had studied at Edinburgh University in 1956 and 1957, and took a B.A. in history at Trinity University in 1959 and a master's degree in education at the University of Cincinnati in 1962." (City of Santa Clarita biographies (the City's own account of a member))
- "In the first council election, on November 3, 1987, he was fourth of 26 candidates for five seats, with 6,585 votes. He was re-elected in April 1990, second of ten candidates for three seats, with 4,042, and in April 1994, second of thirteen with 4,216." ("archive records: the city council elections of november 3, 1987, april 10, 1990")
- "From 1994 he and Chris worked with Healing the Children, a Santa Clarita charity that brought children from abroad for medical treatment. Over fifteen years they fostered eleven children from other countries, and he organized and led its medical missions to..." ("carl boyer 3rd, santa clarita city founder, former mayor, 1937-2019")
- H "The City's biography of 1998 names him the charity's treasurer in California." (City of Santa Clarita biographies (the City's own account of a member))
- "He died of cancer on May 29, 2019." ("carl boyer 3rd, santa clarita city founder, former mayor, 1937-2019")
- field `deathDate` = May 29, 2019 ("carl boyer 3rd, santa clarita city founder, former mayor, 1937-2019")

### Carl Goldman (#30546): 4

- "Carl Goldman co-owned AM-1220, the Santa Clarita Valley's own radio station. He bought it, as KBET, out of bankruptcy in 1990 with investor partners and sold it to Clear Channel in 1998; on October 24, 2003, he and his wife, Jeri Seratti-Goldman, bought it..." (scvhistory.com/scvhistory/lw9450a.htm)
- "After the Northridge earthquake of January 17, 1994, KBET stayed on the air around the clock and the City Council declared it Santa Clarita's official emergency radio station; that July he was grand marshal of the Fourth of July Parade in Newhall, whose..." (scvhistory.com/scvhistory/lw9450a.htm)
- "He was the 2008 SCV Man of the Year." (scvhistory.com/scvhistory/mwoty.htm)
- H "He wrote a tribute to Connie Worden-Roberts for KHTS on August 13, 2014." (scvhistory.com/scvhistory/khts081314.htm)

### Cathie Wright (#29458): 9

- "Under the district lines drawn in 1991, the 19th took in Castaic, Val Verde and Stevenson Ranch, joined to eastern Ventura County: 16.3 per cent of the valley's people by the archive's count from the 2000 census." ("1991 lines, drawn by the court's special masters")
- "As Leon Worden put it in 1996, "West of Interstate 5, Cathie Wright is running for Senate."" ("a politician by any other name...")
- "Tom McClintock succeeded her." (archive record #29513, "Tom McClintock: State Senator, California State Senate (officeHoldings)")
- "In the 1980s the newspapers counted her as one of the valley's two state legislators. In 1984 she joined the residents of Agua Dulce in fighting a proposed state prison there." (archive record #28295, "City Backers Join Prison Furor (articles)")
- H "When Los Angeles Mayor Tom Bradley offered city land above Bouquet Canyon Road in Saugus for a prison in 1985, she called the plan "plain stupidity" and said, "He couldn't get a vote out of that area if he wanted to." At the rally at Hart High School in..." (archive record #28295, "City Backers Join Prison Furor (articles)")
- "When the City of Santa Clarita was inaugurated on 15 December 1987, she was one of the three officials who made presentations." (archive record #31359, "Santa Clarita Cityhood (events)")
- "In 1985 she sent an Assembly resolution commending Iron Eyes Cody on his induction into the Western Walk of Fame in Newhall." (City Council resolutions declaring results)
- H "At her death SCVNews.com said she had represented the Santa Clarita Valley in the Legislature for twenty years. In the Assembly she carried a bill that added the valley's four water purveyors to the board of the Castaic Lake Water Agency. Michael P. Murphy,..." (scvnews.com/cathie-wright-former-state-senator-dies-at-82)
- "The Los Angeles Times remembered her as a conservative who often broke with her own party: she refused to join the Republicans who tried to unseat Assembly Speaker Willie Brown in 1988, and in 1997 she voted for the Democratic welfare reform bill that..." ("cathie wright dies at 82; former assemblywoman and state senator")

### Cave Johnson Couts (#323): 14

- H "Cave Johnson Couts was a San Diego County rancher and former army officer whose tie to the Santa Clarita Valley is real but thin. Leon Worden writes that his presence "incidentally was felt in the Santa Clarita Valley at one time or another," and says no..." ("whose presence incidentally was felt in the santa clarita valley at one time or another")
- H "Couts drove cattle north to the Gold Rush markets. In the spring of 1852 he wrote to Abel Stearns: "The nest of thieves in the Santa Clara did all they knew how to make me loose [sic] a lot, but not all." Others fared worse; he heard that the thieves had..." (Perkins/Reynolds (one source, PROFILES.md))
- H "According to Reynolds, in the spring of 1849 Couts and his brother, William Blunt Couts, drove a herd of some seven hundred head, on consignment from Juan Bandini and John Temple, up the coast to San Jose, where Cave held out for twenty dollars a head, about..." (Perkins/Reynolds (one source, PROFILES.md))
- "His Rancho Guajome, in San Diego County, was built about the same time the del Valles were building at Camulos, "the 'Home of Ramona' at the west end of the historic Rancho San Francisco," and the two ranchos later contended for that name." ("'ramona's bedroom at camulos,' souvenir postcard, ~1915")
- "A Tennessee-born army officer, he settled in San Diego County after the Mexican War, married into the Bandini family and became one of the wealthiest ranchers in Southern California." (sandiegohistory.org/archives/biographysubject/cjcouts)
- H "William Smythe's History of San Diego (1907) says he was born near Springfield, Tennessee, on 11 November 1821, and that his uncle Cave Johnson, Postmaster General under President Polk, had him appointed to West Point, where he graduated in 1843. He served..." (sandiegohistory.org/archives/biographysubject/cjcouts)
- "He married Ysidora Bandini, Juan Bandini's daughter, on 5 April 1851, and resigned from the army that October." (sandiegohistory.org/archives/biographysubject/cjcouts)
- "Reynolds tells of their meeting, Ysidora tumbling from a roof railing into his arms as his column passed; it is Reynolds's story, and no earlier source for it is in the archive." (Perkins/Reynolds (one source, PROFILES.md))
- H "Smythe records that Couts sat on San Diego County's first grand jury in September 1850 and was county judge in 1854, and that in 1853 he moved to the Guajome grant, a wedding gift to his wife from her brother-in-law, Abel Stearns." (sandiegohistory.org/archives/biographysubject/cjcouts)
- H "The Santa Clarita Valley Historical Society lists among its photographs item hs0141, a "Portrait of young soldier holding whip. Back of picture states "Cave J. Couts"," held as an electronic image only." ("print, photographic")
- "Its library holds his published journal, Hepah, California! (1961)." ("santa clarita valley historical society, library holdings")
- field `birthDate` = November 11, 1821 (sandiegohistory.org/archives/biographysubject/cjcouts)
- field `birthplace` = near Springfield, Tennessee (sandiegohistory.org/archives/biographysubject/cjcouts)
- calendar `born near Springfield, Tennessee, per Smythe` = November 11, 1821 (sandiegohistory.org/archives/biographysubject/cjcouts)

### Charles Alexander Mentry (#18648): 24

- H "Charles Alexander Mentry drilled Pico No. 4, the 1876 well in Pico Canyon that his employer said began the oil business in California, and ran the Pico field for the Pacific Coast Oil Company for more than twenty years." (archive record #20104, "Demetrius Scofield's Eulogy to Charles Alexander Mentry (documents)")
- "He was born in France and came to the United States as a boy of seven with his father, Peter." (Pen Pictures from the Garden of the World (1888))
- H "His death certificate gives his birth date as 27 March 1847, then gives an age at death that counts back to 1848." (archive record #20102, "Charles Alexander Mentry's Death Certificate (documents)")
- H "Leon Worden gives the family name as Menetrier, and says "maybe."" (archive record #4187, "Some Notes About Alec Mentry's Birth Name (photographs)")
- H "He worked the Pennsylvania oil regions from 1864, in Venango County, Greene County and at Pithole, and came to California in November 1873, though the same 1889 biography also gives August 1875." (Pen Pictures from the Garden of the World (1888))
- "In 1875 he and two partners leased the Pico claim, and he began with a spring pole." (Pen Pictures from the Garden of the World (1888))
- "Steam drilling began in 1876, and on 26 September 1876 his No. 4 well came in at 617 feet." (Pen Pictures from the Garden of the World (1888))
- "Demetrius Scofield bought the claim and kept Mentry on, and the camp that grew up around the wells took his name." (archive record #20104, "Demetrius Scofield's Eulogy to Charles Alexander Mentry (documents)")
- H "What the well was first at depends on who is asked, and the claims differ in scope. The widest is the world: by 2001 it was "the oldest producing well in the world."" (archive record #2089, "32. Gushers and Glory (articles)")
- "Then the West." (Perkins/Reynolds (one source, PROFILES.md))
- "Then everything west of Pennsylvania." (archive record #12490, "New Study Will Nag SCV Historians (articles)")
- "Then California." (Perkins/Reynolds (one source, PROFILES.md))
- H "Scofield, in 1900, claimed less than any of them: that Mentry was the first to show oil in California "in paying quantities," after others had drilled since 1865." (archive record #20104, "Demetrius Scofield's Eulogy to Charles Alexander Mentry (documents)")
- H "Two sources hedge: the Los Angeles Times in 1954, which has his father "credited with" the first well in California, and Perkins, once, in 1962: "probably the first commercially successful oil well in California."" (Perkins/Reynolds (one source, PROFILES.md))
- H "Others had drilled in Pico Canyon before him: the Hughes well in 1865 and, by later accounts, Sanford Lyon." ("lyon, wiley and jenkins drill at pico canyon")
- H "He married May Lake in 1878. His 1889 biography names two children, Irene and Arthur; Leon Worden gives his wife as Flora May Lake of New York, and four children." (Pen Pictures from the Garden of the World (1888))
- "His father walked out of Pico Canyon in 1886 and was not found until 1899." (archive record #20087, "A Missing Man (documents)")
- "He died at California Hospital in Los Angeles on 4 October 1900, of typhoid fever, with chronic nephritis contributing." (archive record #20102, "Charles Alexander Mentry's Death Certificate (documents)")
- "He is buried in Evergreen Cemetery, Boyle Heights." ("he was laid to rest in the evergreen cemetery in boyle heights")
- field `birthDate` = March 27, 1847 (archive record #20102, "Charles Alexander Mentry's Death Certificate (documents)")
- field `deathDate` = October 4, 1900 (archive record #20102, "Charles Alexander Mentry's Death Certificate (documents)")
- field `birthplace` = France (Pen Pictures from the Garden of the World (1888))
- field `burialPlace` = Evergreen Cemetery, Boyle Heights, Los Angeles ("he was laid to rest in the evergreen cemetery in boyle heights")
- calendar `died, per the death certificate` = Oct. 4, 1900 (archive record #20102, "Charles Alexander Mentry's Death Certificate (documents)")

### Cherise Moore (#28558): 3

- "Cherise Moore has been a member of the board of the William S. Hart Union High School District since 2017 and holds its Trustee Area 3 seat for the term 2022 to 2026." (hartdistrict.org/apps/pages/governing-board-members)
- "She is an elected member of the Los Angeles County Committee on School District Organization, of which she is the immediate past chair, and a representative on the California School Boards Association's Delegate Assembly." (hartdistrict.org/apps/pages/governing-board-members)
- "An educator for more than thirty years, she has worked in the Hart district and for the California Department of Education, and is a principal researcher with the American Institutes for Research." (hartdistrict.org/apps/pages/governing-board-members)

### Christopher Houston Carson (#315): 2

- "In December 1846 he and Edward F. Beale crept through the Mexican lines at San Pasqual to bring reinforcements." ("kit carson, ned beale cross paths and alter california's future")
- H "Leon Worden wrote in 1998 that Frémont and Carson "rode through the area in the mid-1840s," meaning the pass between the San Fernando and Santa Clarita valleys; he gives no date, and no other source in the archive places Carson in the valley." (archive record #12334, "Beale's Cut, parade, the 'Marsha question' (articles)")

### Christopher Trunkey (#25409): 3

- "He represented the Santa Clarita and Antelope valleys as a delegate to the California School Boards Association from 2021 to 2025, completed its Masters in Governance program in November 2017, and sits on its President's Legislative Committee in 2026." (saugususd.org/governing-board)
- "He is a graduate of Drake University and a longtime resident of Saugus." (saugususd.org/governing-board)
- H "In his 2022 candidate statement in Santa Clarita Magazine he wrote that it had been "an honor to represent you on the Saugus Union School District Governing Board since 2014."" (santaclaritamagazine.com/2022/09/meet-the-candidate-chris-trunkey-running-for-saugus-union-school-district-2)

### Christy Smith (#25389): 3

- "Christy Smith served on the governing board of the Newhall School District. She first stood in November 2007, as a community volunteer, and came third of four for two seats. In November 2009 she was elected, second of five for three seats, and in 2013 she..." (County returns (CEDA or the County's statement))
- "In October 2017, when the Newhall School auditorium reopened as the Newhall Family Theater for the Performing Arts, she was the board's president." ("newhall school auditorium reborn")
- "She then stood three times for Congress and lost each time to Mike Garcia: in the special election of May 2020 and the general election of that November for the 25th District, the second by 333 votes of more than 338,000, and in November 2022 for the 27th." (California Secretary of State, Statement of Vote)

### Connie Worden (#16418): 5

- "In 2007 she recalled that she had first favored a county, as Ruth and Scott Newhall did, and that she collected her signatures "by standing in front of grocery stores."" (archive record #28287, "City Formation Committee Member Connie Worden-Roberts Remembers, 2007 (documents)")
- "As spokesman she argued the city's case on cost. "The bottom line is the pocketbook," she told The Signal in January 1987. "If we couldn't pay for the city I'd champion a cause for no city at all."" (archive record #28293, "Cityhood forum announced, The Signal, January 11, 1987 (documents)")
- "By 1975, when the Newhall-Saugus-Valencia Chamber of Commerce named her its Woman of the Year, she sat on the board of the William S. Hart Union High School District, served on the North County Planning Commission, spoke for Henry Mayo Newhall Memorial..." (archive record #28291, "Peter Pitchess to Speak at Newhall CC Luncheon (documents)")
- H "The district's list of its board members dates her seat from December 1, 1974, and names her its clerk and its president, though its dates for her presidency overlap." (archive record #28301, "Roster of Board Members, 1948 to Present (documents)")
- "In 1990 she started the Transportation Management Association." (archive record #28305, "Connie Worden Roberts, City Co-Founder (documents)")

### Dan Hon (#18616): 2

- "A frequent writer of letters to the editor, he began a weekly column for The Signal about 1988, by Leon Worden's memory, and kept it for the rest of his life; he cared deeply about downtown Newhall, and his last published piece appeared in the Old Town..." (archive record #12480, "Dan Hon, Signal columnist (articles)")
- "He died that December, four months after his friend the cartoonist Randy Wicks." (archive record #12480, "Dan Hon, Signal columnist (articles)")

### Dante Acosta (#341): 4

- "He was elected to the council on 8 April 2014, third of thirteen candidates with 4,937 votes." ("city council election, april 8, 2014")
- H "Leon Worden's note records that he was appointed to the board of the SCV Water Agency in January 2019, resigned that August, and moved to Texas as district director of the U.S. Small Business Administration for the El Paso region." (scvhistory.com/scvhistory/sc1401.htm)
- H "The City's biography of 2013 says he worked his way up at a San Fernando Valley Chevrolet dealership to general sales manager while attending California State University, Northridge, then spent some twenty years in financial services: as a financial advisor..." (scvhistory.com/scvhistory/sc1401.htm)
- H "IMDb credits him with acting roles, among them A Deadly Dance (2019) and The Rally (2010)." (imdb.com/name/nm3408102)

### Demetrius G. Scofield (#21584): 9

- H "Demetrius G. Scofield was a San Francisco oil man who rose through the California Star Oil Works and the Pacific Coast Oil Company to be president of Standard Oil Company (California). He was born in New York City; his age at death, 74, puts his birth..." (Find a Grave)
- H "He was in San Francisco by 1873, when a D. G. Scofield, very probably the same man, was secretary of a political committee there." (cdnc.ucr.edu/?a=d&d=dac18730726.2.15)
- "By January 1874 he was with F. B. Taylor & Co., importers of oil, and sailed for Yokohama on its business." (cdnc.ucr.edu/?a=d&d=hbsf18740130.2.16)
- "Standard Oil bought the Pacific Coast Oil Company in December 1900." (cdnc.ucr.edu/?a=d&d=sfc19001211.2.27)
- "Scofield was Standard's general manager on the coast by 1906, when John D. Rockefeller wired him about relief for the earthquake;" (cdnc.ucr.edu/?a=d&d=mpe19060425.2.17)
- "he was vice president of Standard Oil Company (California) from its incorporation in 1906, was elected its president on 5 December 1911, and became its chairman in February 1917." (cdnc.ucr.edu/?a=d&d=sfc19170730.2.2)
- "After Charles Alexander Mentry died in October 1900, Scofield delivered the eulogy the archive holds as record #20104." (archive record #20104, "Demetrius Scofield's Eulogy to Charles Alexander Mentry (documents)")
- field `birthDate` = about 1843 (Find a Grave)
- field `birthplace` = New York City (Find a Grave)

### Dennis Koontz (#23081): 5

- "He worked on the City Formation Committee." (archive record #4009, "Dennis Koontz (photographs)")
- "In the first council election, on November 3, 1987, he was fifth of 26 candidates for the five seats, with 6,164 votes. He stood again in April 1990 and came seventh of ten, with 2,155." ("archive records: the city council elections of november 3, 1987 and april 10, 19")
- H "The biography on his council page says he retired from the Los Angeles City Fire Department as a fire captain, captain of a paramedic engine company, and was a founding member of the United Firefighters of Los Angeles City, Local 112." (archive record #4009, "Dennis Koontz (photographs)")
- H "The account of the 1987 election in this archive calls him a retired county firefighter." (archive record #4261, "Santa Clarita Valley City Formation Committee Membership-Donation Form (photographs)")
- "By the same biography he was later health assistant at Valencia High School for more than fifteen years, retiring in 2010; president of Chapter 349 of the California School Employees Association; and a member of the William S. Hart Union High School..." (archive record #4009, "Dennis Koontz (photographs)")

### Di Thompson (#29208): 1

- "Di Thompson chairs the City's Parks, Recreation and Community Services Commission; her term runs to December 2026. A resident of Santa Clarita for 24 years, she works in residential and investment real estate. She sits on the board of directors of the Santa..." (santaclarita.gov/commission-information/parks-recreation-and-community-services)

### Edward Fitzgerald Beale (#327): 3

- H "For the valley he is the man of the cut. He took over Andrés Pico's franchise for the road over the pass and, by Jerry Reynolds's account, got five thousand dollars from the Los Angeles supervisors to do the work." (Perkins/Reynolds (one source, PROFILES.md))
- "He died at Decatur House in Washington on April 22, 1893." ("beale air force base: biography of edward f. beale")
- field `deathDate` = April 22, 1893 ("beale air force base: biography of edward f. beale")

### Edwin White Newhall (#31728): 3

- "With his brothers he incorporated The Newhall Land and Farming Company on June 1, 1883, the year after their father's death." (archive record #283, "Henry Mayo Newhall (persons)")
- "He died on October 28, 1915." ("colma cemeteries: henry mayo newhall & heirs")
- field `deathDate` = October 28, 1915 ("colma cemeteries: henry mayo newhall & heirs")

### Fran Pavley (#29460): 7

- "Under the lines drawn in 2011 by the Citizens Redistricting Commission, the 27th took in Stevenson Ranch and the western and southwestern neighborhoods of the City of Santa Clarita, with eastern Ventura County, Calabasas and Malibu: 20.3 per cent of the..." ("2011 lines, drawn by the citizens redistricting commission")
- "Her Senate office counted Santa Clarita among the district's communities, and its photographs of 2014 show her touring Henry Mayo Newhall Memorial Hospital and College of the Canyons, meeting the valley's school superintendents, visiting City Hall to meet..." (sd27.senate.ca.gov/category/image-galleries/santa-clarita)
- "When the Castaic Lake Water Agency and the Newhall County Water District set out to form a single water agency for the valley, her office was among those they briefed." ("scv water plan for services")
- "She chaired the Senate Natural Resources and Water Committee and a select committee on climate change and putting AB 32 into effect." (sd27.senate.ca.gov/biography)
- "A middle school teacher for 28 years, she became the first mayor of Agoura Hills in 1982 and served four terms on its city council." (sd27.senate.ca.gov/biography)
- H "Carl Boyer wrote in his history of the City of Santa Clarita's formation that "Fran Pavley of Agoura Hills" appointed him to the Regional Issues Task Force of the League of California Cities' Los Angeles Division." (Carl Boyer, Santa Clarita: The Formation)
- "Henry Stern succeeded her in the 27th District in December 2016." (California Secretary of State, Statement of Vote)

### Francisco "Chico" López (#28132): 7

- "He lived at Paredon Blanco in Los Angeles and ran his cattle at Rancho Rosa de Castilla until, about 1850, his kinsman Francisco López, the gold discoverer, showed him the lake and advised him to take his stock there. He did, built an adobe ranch house by a..." ("in pursuit of vanished days")
- "It has also been suggested that Francisco Chari, who settled in Bouquet Canyon about 1843, had herded cattle there for him." ("mining and ranching in soledad canyon and antelope valley")
- "The ranch slipped away from him. Kept in Los Angeles most of the time, he saw his herds dwindle under his foreman's care and realized on only 800 of his 4,000 cattle; his ranch house and barns were burned in his absence; and the ranch went on a mortgage to..." ("in pursuit of vanished days")
- "Born in what is now San Diego County about 1820, he died in Los Angeles in January 1900, in his eightieth year." ("death of a pioneer")
- field `birthDate` = 1820 ("death of a pioneer")
- field `deathDate` = January 18, 1900 ("death of a pioneer")
- field `birthplace` = San Diego County, California ("death of a pioneer")

### Francisco Lopez (#18834): 3

- "He was born on March 9, 1802, and baptized as a newborn at Mission San Gabriel. He was a soldier at the Santa Barbara Presidio and then at Mission San Fernando, and married María Antonia Felis." ("baptismal data: francisco lopez (gold discoverer)")
- "Some two thousand miners, most from Sonora, worked the canyon in the years after." (archive record #15294, "California's REAL First Gold (articles)")
- field `birthDate` = March 9, 1802 ("baptismal data: francisco lopez (gold discoverer)")

### Frank Ferry (#23083): 3

- "He first stood in April 1996 and came third of thirteen candidates for two seats, with 3,208 votes. Elected in April 1998, second of fifteen candidates for three seats, with 6,583, he was re-elected in 2002, first of twelve with 6,684; in 2006, second of..." ("archive records: the city council elections of april 9, 1996, april 14, 1998, ap")
- H "By the City's account he holds a bachelor's degree in governmental communications from California State University, Northridge, a bachelor's degree in law, a juris doctorate and a California teaching credential." (City of Santa Clarita biographies (the City's own account of a member))
- H "The City says he first ran to get roads built and relieve traffic, and that on the council he wanted after-school programs at every elementary school and more active park space. It lists his service on the California Contract Cities committee, the Regional..." (City of Santa Clarita biographies (the City's own account of a member))

### Gary Murr (#25399): 4

- "Gary Murr was president of the Saugus Union School District board when he died suddenly in June 2005, at 58." (archive record #28053, "Gary Murr, Saugus School Board President (obituaries)")
- "He came to the board through his children's schooling. He was active in the PTA from the time his eldest started kindergarten, and was moved to work for new schools because Plum Canyon School had not yet been built when his children began at Emblem..." (archive record #28053, "Gary Murr, Saugus School Board President (obituaries)")
- "A 28-year veteran of the garment industry, he left it for real estate when he began his service to the district. As a board member he went to site council meetings at every school in the district." (archive record #28053, "Gary Murr, Saugus School Board President (obituaries)")
- field `deathDate` = June 2005 (archive record #28053, "Gary Murr, Saugus School Board President (obituaries)")

### George Caravalho (#16396): 8

- "He was born on August 1, 1938, in Kohala, Hawaii, and grew up on the Big Island; he joined the Marines in 1956. He took a bachelor's degree in sociology and a master's in political science at San Jose State University." (archive record #28049, "George A. Caravalho, Santa Clarita's First Permanent City Manager, 1938-2020 (obituaries)")
- H "Before Santa Clarita he was city manager of San Clemente, from 1980 to 1985, and of Bakersfield, and director of its redevelopment agency, from 1984 to 1988; the obituary gives both spans as they stand, overlapping." (archive record #28049, "George A. Caravalho, Santa Clarita's First Permanent City Manager, 1938-2020 (obituaries)")
- "After Santa Clarita he was city manager of Riverside and, from 2005 to 2007, director of Dana Point Harbor, and he retired from public life in 2008." (archive record #28049, "George A. Caravalho, Santa Clarita's First Permanent City Manager, 1938-2020 (obituaries)")
- "The city dedicated the George A. Caravalho Santa Clarita Sports Complex to him on December 5, 1998." (archive record #28049, "George A. Caravalho, Santa Clarita's First Permanent City Manager, 1938-2020 (obituaries)")
- "He died on January 5, 2020, at 81; his burial was announced for Santa Cruz Memorial Park." (archive record #28049, "George A. Caravalho, Santa Clarita's First Permanent City Manager, 1938-2020 (obituaries)")
- field `birthDate` = August 1, 1938 (archive record #28049, "George A. Caravalho, Santa Clarita's First Permanent City Manager, 1938-2020 (obituaries)")
- field `deathDate` = January 5, 2020 (archive record #28049, "George A. Caravalho, Santa Clarita's First Permanent City Manager, 1938-2020 (obituaries)")
- field `birthplace` = Kohala, Hawaii (archive record #28049, "George A. Caravalho, Santa Clarita's First Permanent City Manager, 1938-2020 (obituaries)")

### George Pederson (#18726): 3

- "He earned a master's degree and a doctorate in the administration of justice, and taught at College of the Canyons." ("biography of mayor george pederson, lasd")
- "In 1986 he helped gather signatures for the cityhood petition." (archive record #4007, "George Pederson (photographs)")
- "In 1996 he ran for the State Assembly and lost to George Runner, though he had the most votes in Santa Clarita. In 1997 he was named Santa Clarita Valley Man of the Year." (archive record #4007, "George Pederson (photographs)")

### George Runner (#18747): 12

- "Under the 1991 lines the 36th held the City of Santa Clarita as it then stood, Agua Dulce and the unincorporated land between them, 83.7 per cent of the valley's people by the archive's count from the 2000 census. Under the 2001 lines the 17th held all of..." ("1991 lines, drawn by the court's special masters")
- "In 2005 the Signal introduced him as the senator "who represents most of the Santa Clarita Valley."" ("newsmaker of the week: george runner, state senator, 17th district")
- H "A Lancaster man, he had served on the Lancaster City Council and as the city's mayor, and had helped found Desert Christian Schools, before he ran for the Assembly in 1996, when Pete Knight left the seat to run for the State Senate; by his own account the..." ("the signal, sunday, october 24, 2004 (")
- "After winning the Republican nomination that spring he told the Signal his goal was "to spend a lot of time meeting with people in the Santa Clarita Valley and learning the local issues," and he went to the hearing on the proposed Elsmere Canyon Landfill,..." (archive record #12570, "'Front Runner' is off and running (articles)")
- "He was vice chairman of the Assembly Budget Committee for four of his six years there." ("the signal, sunday, october 24, 2004 (")
- "With State Senator Pete Knight he supported the City's effort in 2000 to secure $250,000 in state money, through the Department of Veterans Affairs, to buy half an acre for the Veterans Historical Plaza in Newhall." ("future veterans historical plaza site, 1998")
- "His bill of 2001 turned the part of San Fernando Road that was State Route 126 within the City over to the City, so that it could rework the street through downtown Newhall "without being delayed by bureaucratic red tape," as he put it." ("san fernando changing hands? runner bill would transfer ownership of a portion of san fernando road to the city")
- H "The Signal credited him with the Assembly bill that made the Santa Clarita Water Company's seat on the board of the Castaic Lake Water Agency an elected one after the agency bought the company; he said in 2005 that the agency had yet to act on it, and that..." ("newsmaker of the week: george runner, state senator, 17th district")
- "Term limits ended his Assembly service in 2002." ("the signal, sunday, october 24, 2004 (")
- "His wife, Sharon Runner, was then in the Assembly, and the Signal asked him in 2005 what it was like "being half of California's first legislative couple." The Signal described him then as chairman of the Senate Republican caucus; he kept his Santa Clarita..." ("newsmaker of the week: george runner, state senator, 17th district")
- H "In the same interview he said he was working to even out state school funding and expected "the schools in the Santa Clarita Valley better funded."" ("newsmaker of the week: george runner, state senator, 17th district")
- "By 2016 he sat for the board's First District, with offices in Sacramento and Lancaster." (Secretary of the Senate, Record of Members)

### George Whitesides (#29336): 6

- H "The Clerk of the House gives his hometown as Agua Dulce." (clerk.house.gov/members/w000830)
- "A Democrat, he won the seat on 5 November 2024 from the Republican incumbent, Mike Garcia, by 154,040 votes to 146,050." (California Secretary of State, Statement of Vote)
- "In the House he sits on the Committee on Armed Services and the Committee on Science, Space, and Technology." (clerk.house.gov/members/w000830)
- "He was then chief executive of Virgin Galactic until July 2020, when its board named Michael Colglazier to succeed him and made Whitesides the company's Chief Space Officer." (sec.gov/archives/edgar/data/1706946/000119312520193434/d46161d8k.htm)
- "Proposition 50, passed in November 2025, replaced the 2021 congressional lines. From 3 January 2027 the 27th District keeps 88 per cent of the valley's people; Castaic, Val Verde and Hasley Canyon go to the 26th District, and Agua Dulce with the eastern and..." ("2021 lines, drawn by the citizens redistricting commission")
- "In the primary of 2 June 2026 for the redrawn 27th, Whitesides finished second to the Republican Jason Gibbs, 62,214 votes to 62,758, and the two go on to the general election of 3 November 2026." (California Secretary of State, Statement of Vote)

### H. Clyde Smyth (#15985): 8

- H "They say little of what he did in the post. The one measure the archive holds is the district's enrolment, which the federal count shows growing in his last six years, from 9,371 pupils in 1986-87 to 10,615 in 1991-92; the count begins in 1986." ("nces common core of data, enrolment of the william s. hart union high school dis")
- "He was elected to the City Council on 12 April 1994, third of the candidates, with 3,804 votes, sixteen more than Jill Klajic." (santaclarita.gov/city-clerk/wp-content/uploads/sites/8/2023/06/historical-results-7.pdf)
- "Taking the mayor's chair in December 1996 he named his priorities as the redevelopment of Newhall, the central park on Bouquet Canyon Road, growth outside the city's limits, and keeping a landfill out of Elsmere Canyon, and described his way of working as..." (archive record #12474, "Mayor Clyde Smyth outlines priorities (articles)")
- H "According to his obituary he had presided over the council's first meeting in December 1987, when he was not a member of it." (dignitymemorial.com/obituaries/newhall-ca/h-smyth-4971797)
- "His son Cameron Smyth was elected to the same council in 2000." (santaclarita.gov/city-clerk/wp-content/uploads/sites/8/2023/06/historical-results-7.pdf)
- "Clyde Smyth died on 22 January 2012, at 80." (dignitymemorial.com/obituaries/newhall-ca/h-smyth-4971797)
- field `birthDate` = September 27, 1931 (archive record #4533, "Clyde Smyth (photographs)")
- field `deathDate` = January 22, 2012 (dignitymemorial.com/obituaries/newhall-ca/h-smyth-4971797)

### Harry Carey (#15919): 2

- "The St. Francis Dam flood of March 1928 washed the trading post away, and it was not rebuilt. The ranch house, on higher ground, survived the flood and burned in 1932; the adobe that replaced it, sold with the ranch in 1945, is now the centerpiece of the..." ("harry carey sr. with infant dobe in saugus, 1921")
- "Their son, the actor Harry Carey Jr., was born on the ranch on May 16, 1921." ("harry carey ranch (clougherty ranch)")

### Henry Clay Wiley (#331): 2

- H "He is said to have drilled a well in Pico Canyon with Sanford Lyon and William Wirt Jenkins in the late 1860s; the date is uncertain." (archive record #20228, "Lyon, Wiley and Jenkins Drill at Pico Canyon (events)")
- "He was a charter member of the Southern California Pioneer Society and owned considerable real estate in Los Angeles." (archive record #867, "Another Pioneer Passes Away (articles)")

### Henry Stern (#29462): 8

- "Under the lines drawn in 2011 by the Citizens Redistricting Commission, the 27th took in Stevenson Ranch and the western and southwestern neighborhoods of the City of Santa Clarita, with eastern Ventura County, Calabasas and Malibu: 20.3 per cent of the..." ("2011 lines, drawn by the citizens redistricting commission")
- "The Signal described his district in 2019 as including "some western portions of the Santa Clarita Valley."" (scvnews.com/newsom-signs-sb-630-sterns-human-trafficking-bill)
- H "College of the Canyons welcomed him to its Valencia campus, where he toured the welding, nursing and media entertainment arts departments and met students of its civic engagement program; the college reported the visit in its annual report for 2017-18." ("community connections")
- "When the Castaic Lake Water Agency and the Newhall County Water District set out to form a single water agency for the valley, his office was among those they briefed." ("scv water plan for services")
- H "In Sacramento he wrote SB 225 of 2017, which had the state Department of Justice add a texting option to its model notice on human trafficking, and SB 630, signed by Governor Gavin Newsom in July 2019, which made clear that local governments may act to..." (scvnews.com/newsom-signs-sb-630-sterns-human-trafficking-bill)
- H "After the Woolsey fire of 2018 destroyed his home, he wrote the Wildfire Resilience through Community and Ecology Act of 2021. He chaired the Senate Natural Resources and Water Committee from 2018 to 2022, pressed for the closing of the Aliso Canyon natural..." (sd27.senate.ca.gov/biography)
- "A former educator and environmental attorney, Stern was raised in Malibu, graduated from Harvard University and took his law degree at UC Berkeley." (sd27.senate.ca.gov/biography)
- "The Signal gave his home as Canoga Park in 2019." (scvnews.com/newsom-signs-sb-630-sterns-human-trafficking-bill)

### Jan Heidt (#15737): 4

- "She was re-elected in April 1992, first of sixteen candidates for two seats, with 6,748, and in 1996, second of thirteen with 3,422. She stood again in 2002 and came fourth of twelve for three seats, with 5,111." ("archive records: the city council elections of november 3, 1987, april 14, 1992")
- H "The City's biography of 1998 records her as a co-founder of the "Dump the Dump" task force of 1978, which kept two toxic waste dumps out of the valley, and of the Santa Clarita Valley Hazardous Waste Committee of 1984, and as spokesperson in 1986 for the..." (City of Santa Clarita biographies (the City's own account of a member))
- H "By the same account she graduated from Michigan State University in 1961 and was a Navy officer from 1964 to 1969, two years of it at the Navy Communication Center in the Pentagon; it also puts her on duty there during the Cuban Missile Crisis, which was in..." (City of Santa Clarita biographies (the City's own account of a member))
- "The Zonta Club named her its Woman of the Year in 1984 and 1988." (City of Santa Clarita biographies (the City's own account of a member))

### Jason Gibbs (#23091): 2

- "He first stood in 2018 and came ninth of fifteen candidates with 10,008 votes. In 2020 he was elected second of nine, with 29,474." ("archive records: the city council elections of 2018 and 2020, with their returns")
- H "The City's biography says he holds bachelor's and master's degrees in mechanical engineering from Cal Poly and works in the aerospace industry as senior principal engineer of West Coast operations for GP Strategies Corporation, on the ground support..." (City of Santa Clarita biographies (the City's own account of a member))

### Jeff Gorell (#29452): 9

- "Under the 2001 district lines the 37th took in Castaic, Val Verde, Hasley Canyon, Agua Dulce and Green Valley, 12.9 per cent of the valley's people by the archive's count from the 2010 census, in a district that was mostly Ventura County;" ("2001 lines, drawn by the legislature")
- "the Ventura County Star listed its main places as Thousand Oaks, Moorpark, Camarillo, Fillmore, Santa Paula, Ojai and much of Simi Valley." (vcstar.com/news/2010/oct/30/gorell-expects-deployment-to-afghanistan)
- "He succeeded Audra Strickland, who could not run again under term limits." (vcstar.com/news/2010/oct/30/gorell-expects-deployment-to-afghanistan)
- "Three days before the election he announced that the Navy Reserve would send him to Afghanistan in March, for twelve months, half of the two-year term he was seeking." (vcstar.com/news/2010/oct/30/gorell-expects-deployment-to-afghanistan)
- "The Los Angeles Times called him the first California legislator deployed for active duty since the Second World War. Finding that the state's constitution would not let him name a stand-in, he took a leave of absence and left his office to his staff and..." (latimes.com/archives/la-xpm-2010-nov-14-la-me-assembly-deploy-20101114-story.html)
- H "His orders had him report on 18 March 2011, and he said he would take no legislator's pay while away." (vcstar.com/news/2010/oct/30/gorell-expects-deployment-to-afghanistan)
- "Before his election he had worked on the staff of Governor Pete Wilson in the early 1990s and had been a deputy district attorney in Ventura County from 1999 to 2006, prosecuting narcotics and violent felony cases. A Navy reservist for nearly twelve years by..." (vcstar.com/news/2010/oct/30/gorell-expects-deployment-to-afghanistan)
- "In 2014 he ran for Congress in the 26th District, almost wholly in Ventura County, and lost to Julia Brownley, 51.3 per cent to 48.7." (California Secretary of State, Statement of Vote)
- "In 2015 Mayor Eric Garcetti made him deputy mayor of Los Angeles for homeland security and public safety." (mpacorn.com/news/2015-06-19/front_page/gorell_new_deputy_mayor_of_los_angeles.html)

### Jeri Seratti (#29113): 3

- "Jeri Seratti, also known as Jeri Seratti Goldman, has co-owned and run the valley's radio station since 2003, when AM 1220 was bought back from Clear Channel and put on the air as KHTS, "Santa Clarita's Hometown Station"; she had managed the same station,..." (santaclarita.gov/commission-information/arts-commission)
- "After the Northridge earthquake of 1994 the station was the valley's line of information through the months of recovery, and she directed its full-time emergency broadcast." (santaclarita.gov/commission-information/arts-commission)
- "She sits on the City's Arts Commission, her term running to 2028." (santaclarita.gov/commission-information/arts-commission)

### Jerry Gladbach (#28336): 10

- "In 2005 he chaired its Water Resources Committee." (govinfo.gov/content/pkg/crec-2005-11-17/pdf/crec-2005-11-17-pt1-pge2388.pdf)
- H "In November 1996 he stood for the board for Newhall and Valencia, against an opponent, by Leon Worden's account at the time." ("a politician by any other name...")
- "Under SB 634 he and the other sitting directors became the first board of SCV Water, his term extended to 2022." (SB 634 (2017))
- "He was its vice president from February 2020 and chaired its Public Outreach and Legislation Committee." (yourscvwater.com/sites/default/files/scvwa/approved-resolutions/scv/scv-water-approved-resolution-011723-resolution-scv-329.pdf)
- H "Interviewed by Leon Worden for the Signal's "Newsmaker of the Week" in April 2005, he spoke for the agency on the 41,000 acre-feet of water it had bought from Wheeler Ridge, then held up in court, on the treatment of the perchlorate in part of the Saugus..." (scvhistory.com:80/scvhistory/signal/newsmaker/sg050105.htm)
- H "The agency's release at his death credits him with championing the Rio Vista Water Treatment Plant, whose construction began in 1991, and with a part in buying the Devil's Den Water District in Kern County the same year." (yourscvwater.com/sites/default/files/scvwa/newscenter/press%20release/2022/press-release_-jerry-gladbach_announcement.pdf)
- "In 2024 SCV Water renamed the plant the E. G. "Jerry" Gladbach Water Treatment Plant." (scvnews.com/scv-water-dedicates-water-treatment-plant-after-jerry-gladbach)
- H "His campaign biography gives the master's as civil engineering, and says that he came to California on an internship and was then hired by the Department." (jerrygladbach.com)
- "He sat on ACWA's board from 1998 until his death, by SCV Water's resolution." (yourscvwater.com/sites/default/files/scvwa/approved-resolutions/scv/scv-water-approved-resolution-011723-resolution-scv-329.pdf)
- H "His campaign biography says he was one of three members ACWA appointed, and that he served on the U.S. Environmental Protection Agency's Groundwater Task Force; no other source found names him on that task force." (jerrygladbach.com)

### Jerry Reynolds (#281): 6

- "He was born Gerald G. Reynolds in Torrance, California, on July 16, 1937. He studied art history at Long Beach State College, worked as a private investigator, and then as a tour guide at Hearst Castle at San Simeon. He made his career with the California..." (archive record #817, "Preface (articles)")
- "He came to Newhall in 1971 with his wife, Myrna, and their three sons, and took up the work of the aging Perkins, collecting historical photographs and writing down the recollections of the valley's old-timers. By the time Perkins died, in 1977, he had taken..." (archive record #817, "Preface (articles)")
- H "He wrote for The Signal off and on for two decades, with complete series of historical columns in the mid-1970s and the mid-1980s. Those columns were the backbone of "Santa Clarita: Valley of the Golden Dream," published by the Santa Clarita Valley Chamber..." (archive record #817, "Preface (articles)")
- field `birthDate` = July 16, 1937 (archive record #817, "Preface (articles)")
- field `birthplace` = Torrance, California (archive record #817, "Preface (articles)")
- calendar `Born Gerald G. Reynolds in Torrance, California, on July 16,` = July 16, 1937 (archive record #817, "Preface (articles)")

### Jill Klajic (#15874): 3

- H "She came to the council from the cityhood campaign. The 1998 edition of Jerry Reynolds's history says that she joined the City Formation Committee soon after it was organized and was paid staff to the campaign." (Perkins/Reynolds (one source, PROFILES.md))
- "Elected in April 1990, first of ten candidates for three seats, with 4,081 votes, she came fourth of thirteen in 1994, with 3,788, and lost her seat. In 1996 she was elected again, first of thirteen candidates for two seats, with 3,584." ("archive records: the city council elections of april 10, 1990, april 12, 1994 an")
- H "The City's biography of 1998 lists her as general manager and recycling coordinator of Cal Coast Recycling; a consultant to TCB International, organizing training for small businesses; co-owner of a calibration business, Precision Technical Services, in Van..." (City of Santa Clarita biographies (the City's own account of a member))

### Jo Anne Darcy (#16140): 11

- "She was born Jo Anne Hall in San Angelo, Texas, on May 2, 1931, and came to California as a child. From 1967 she and her husband, Curtis Darcy, ran the Acton '49er saloon, and in the early 1970s the family moved to Saugus." ("city founder jo anne darcy dies at 86")
- H "The City's biography of 1998 gives her seven years as the chamber's executive vice president and manager and fifteen as a senior deputy to the Board of Supervisors." (City of Santa Clarita biographies (the City's own account of a member))
- "She joined the City Formation Committee soon after it was organized." (Perkins/Reynolds (one source, PROFILES.md))
- "In the first council election, on November 3, 1987, she was third of 26 candidates for five seats, with 7,601 votes. She was re-elected in April 1990, third of ten candidates for three seats, with 3,548; in 1994, first of thirteen with 5,460; and in 1998,..." ("archive records: the city council elections of november 3, 1987, april 10, 1990")
- "Her last council meeting was on April 23, 2002." ("city founder jo anne darcy dies at 86")
- "Governor George Deukmejian appointed her to the California State Film Commission in 1989, and Governor Pete Wilson reappointed her in 1993." (City of Santa Clarita biographies (the City's own account of a member))
- "In 2001 Los Angeles County named the Jo Anne Darcy Canyon Country Library for her." ("city founder jo anne darcy dies at 86")
- "She died on October 29, 2017, at 86." ("city founder jo anne darcy dies at 86")
- field `birthDate` = May 2, 1931 ("city founder jo anne darcy dies at 86")
- field `deathDate` = October 29, 2017 ("city founder jo anne darcy dies at 86")
- field `birthplace` = San Angelo, Texas ("city founder jo anne darcy dies at 86")

### John C. Frémont (#307): 6

- "John C. Frémont led the American battalion that crossed the Santa Clarita Valley in January 1847 on its way to Cahuenga, where Andrés Pico surrendered to him, and the pass between the Santa Clarita and San Fernando valleys bears his name." (Perkins/Reynolds (one source, PROFILES.md))
- H "By the account Leon Worden and Jerry Reynolds both give, in the same words, Frémont and his hundred-man "buckskin battalion" reached Castaic Junction from the north on January 9, 1847, and probably stopped overnight at the del Valle ranch house. The next..." (Perkins/Reynolds (one source, PROFILES.md))
- "Born in Savannah, Georgia, in 1813, he was an Army explorer the press called "the Pathfinder," with Kit Carson as his guide, and he was later one of California's first United States senators, the Republican Party's first presidential candidate in 1856, and..." (Perkins/Reynolds (one source, PROFILES.md))
- field `birthDate` = January 21, 1813 (Perkins/Reynolds (one source, PROFILES.md))
- field `deathDate` = July 13, 1890 (Perkins/Reynolds (one source, PROFILES.md))
- field `birthplace` = Savannah, Georgia (Perkins/Reynolds (one source, PROFILES.md))

### John Lang (#18820): 3

- "John Lang was the Soledad Canyon pioneer whose homestead gave its name to Lang Station, where the Southern Pacific's tracks from north and south were joined in 1876, linking Los Angeles to the rest of the country by rail." ("original portrait, john lang family, ~1889")
- H "He married Mary E. Fletcher in Sacramento on May 3, 1862 (the 1889 biography gives her name as Floretta); they had six children. He died in Los Angeles County on January 20, 1909." ("original portrait, john lang family, ~1889")
- field `deathDate` = January 20, 1909 ("original portrait, john lang family, ~1889")

### John Timothy Gifford (#337): 10

- "John Timothy Gifford was Newhall's first railroad agent and telegraph operator, and the man who sent the news in 1876 that the Southern Pacific's San Fernando Tunnel had broken through." ("john & sarah gifford home")
- "He is first known in 1871 as a Western Union line rider between Los Angeles and Lake Elizabeth. In 1875 he took charge of the north-side crew digging the 6,940-foot tunnel through Railroad Canyon, and when the two crews met on July 15, 1876, he telegraphed..." ("john & sarah gifford home")
- "The Newhall depot opened on September 6, 1876, near today's Bouquet Canyon Road and Magic Mountain Parkway, and the Giffords lived in a boxcar on a siding; their daughter Mabel, born November 3, 1875, was the first child to live in Newhall. When the town..." ("john & sarah gifford home")
- "He married Sarah Beckwith, born in England, in San Diego in 1875. He was also the local telegrapher for Wells, Fargo & Co., and he retired from the railroad in 1912. Born in Cincinnati, Ohio, on February 14, 1847, he died on October 15, 1922, and is buried..." ("john & sarah gifford home")
- field `birthDate` = February 14, 1847 ("john & sarah gifford home")
- field `deathDate` = October 15, 1922 ("john & sarah gifford home")
- field `birthplace` = Cincinnati, Ohio ("john & sarah gifford home")
- field `burialPlace` = Garden of Pioneers, Eternal Valley Cemetery, Newhall ("john & sarah gifford home")
- calendar `John Timothy Gifford was born February 14, 1847, in Ohio, to` = February 14, 1847 ("john & sarah gifford home")
- calendar `sion thereafter: $27.30 in September 1919, for example. Joh` = October 15, 1922 ("john & sarah gifford home")

### Juan Crespí (#297): 4

- "The Estancia de San Francisco Xavier, the outpost of Mission San Fernando built with Tataviam labor in 1804, stood on a site he and Portolá had proposed." (archive record #12532, "Latins Invade, Conquer Western SCV (articles)")
- "Born in Palma, Majorca, in 1721, he became a Franciscan at seventeen, studied under Junípero Serra, and in 1768 was put in charge of the mission of La Purísima Concepción de Cadegomó in Baja California, from where he marched north the next year and kept the..." ("crespi's scv legacy")
- field `birthDate` = 1721 ("crespi's scv legacy")
- field `birthplace` = Palma, Mallorca, Spain ("crespi's scv legacy")

### Junípero Serra (#299): 1

- H "Father Junípero Serra, who founded the missions of Alta California, has no known connection to the Santa Clarita Valley. As Leon Worden writes, "We have no reason to believe Serra ever set foot" here: he stayed behind in San Diego with an injured leg when..." (archive record #3673, "Fr. Junípero Serra (photographs)")

### Juventino del Valle (#303): 3

- "Juventino del Valle, the eldest child of Ygnacio del Valle, ran Rancho Camulos as its manager from 1862 to 1886." ("juventino del valle, black walnut tree, (5) important camulos views, 1910s")
- "Camulos, near Piru in Ventura County, about ten miles west of today's Interstate 5, was part of the Rancho San Francisco, the grant made to his grandfather Antonio del Valle, and lay in the part his father held." (archive record #293, "Ygnacio del Valle (persons)")
- kin `childOf` = Ygnacio del Valle ("juventino del valle, black walnut tree, (5) important camulos views, 1910s")

### Kathryn Barger (#29288): 6

- "She led a field of eight in the primary of 7 June 2016 with 105,520 votes, and on 8 November 2016 she beat Darrell Park across the district, 350,998 votes to 255,165; in the City of Santa Clarita the count was 42,080 to 27,052." (County returns (CEDA or the County's statement))
- "Her election, with Janice Hahn's in the 4th District, gave the Board a female majority for the first time. She is limited to three four-year terms." (archive record #31241)
- H "Her office has announced several of the Board's decisions on the valley. In July 2017 the Board certified, 4 to 0, the environmental impact reports for Mission Village and Landmark Village, part of the Newhall Ranch Specific Plan; she said the projects..." (kathrynbarger.lacounty.gov/county-certifies-environmental-impact-reports-for-landmark-village-and-mission-village)
- H "In March 2024 her office announced the County's plan to rebuild and widen The Old Road beside Stevenson Ranch to six lanes and to replace its bridge over the Santa Clara River, which the Federal Highway Administration classed as structurally deficient; she..." (kathrynbarger.lacounty.gov/l-a-county-invests-250m-to-improve-old-road-in-santa-clarita-region)
- H "In August 2024 the Board unanimously approved her motion to transfer William S. Hart Park and the Hart Museum to the City of Santa Clarita, on terms that keep the park open to every County resident on the same fees; "Los Angeles County is not losing a park,"..." (kathrynbarger.lacounty.gov/l-a-county-supervisors-green-light-transfer-of-historic-william-s-hart-park-and-museum-to-city-of-santa-clarita)
- H "When the Chiquita Canyon Landfill stopped taking waste on 1 January 2025, she said she would ask Public Works to study what the closing meant, and that her first concern remained relief for the neighbors still troubled by the landfill's odors, for which the..." (kathrynbarger.lacounty.gov/l-a-county-addresses-chiquita-canyon-landfill-closure-planning-for-impact-and-protection)

### Katie Hill (#29332): 13

- "Katie Hill represented the Santa Clarita Valley in the United States House of Representatives, for the 25th District, from 3 January 2019 until she resigned on 3 November 2019." (Biographical Directory of the U.S. Congress)
- "On 6 November 2018 she won the seat from the incumbent, Steve Knight, by 133,209 votes to 111,813, 54.4 per cent of the vote." (California Secretary of State, Statement of Vote)
- "She was the first Democrat to hold the 25th District since the 1991 lines put the valley in it: Republicans held it from January 1993, Buck McKeon until 2015 and Steve Knight after him." (clerk.house.gov/member_info/electioninfo/1990election.pdf)
- "Before Congress she was the executive director of PATH (People Assisting The Homeless), by her official biography." ("u.s. rep. katie hill: first 100 days (video highlights), 2019")
- "On 6 February 2019 she introduced H.R. 1015, with Julia Brownley, to establish a national memorial and national monument to those killed by the collapse of the St. Francis Dam on 12 March 1928." (govinfo.gov/content/pkg/bills-116hr1015ih/html/bills-116hr1015ih.htm)
- H "On 23 October 2019 the House Committee on Ethics announced that it was investigating public allegations that she "may have engaged in a sexual relationship with an individual on her congressional staff," in violation of House Rule XXIII, clause 18(a), and..." (ethics.house.gov/press-release/statement-chairman-and-ranking-member-committee-ethics-regarding-representative-katie)
- "She denied the allegation." (signalscv.com/2019/10/ethics-committee-investigates-allegations-of-hill-affair-with-congressional-staffer)
- "The same day, in an email to her supporters, she acknowledged "a relationship with someone on my campaign" and apologized for it." (signalscv.com/2019/10/ethics-committee-investigates-allegations-of-hill-affair-with-congressional-staffer)
- "The rule the Committee cited applies to a Member's relationship with an employee of the House who works under the Member's supervision or for one of the Member's committees." (govinfo.gov/content/pkg/hman-116/pdf/hman-116-houserules.pdf)
- "On 27 October she announced that she would resign." ("hill announced in a tweet sunday afternoon at 4:03 p.m.")
- "The Governor called a special election for 12 May 2020 to fill the vacancy, and Mike Garcia won it." (California Secretary of State, Statement of Vote)
- "In December 2020 she sued over the publication of the photographs in the Los Angeles County Superior Court, under California's statute giving a civil claim against distributing sexually explicit material without consent, Civil Code section 1708.85, which..." (". subdivision (c)")
- "The court struck her claims against the media defendants; in a ruling of April 2021 it found that the images were a matter of public concern under that exception. Her claim against the remaining defendant was settled in December 2023, on terms that were not..." ("distribution of sexually explicit materials; private cause of action; use of pseudonym")

### Keith Richman (#29316): 9

- "He had first won the 38th in November 2000, when, under the 1991 district lines, it held only the valley's unincorporated west side, Castaic, Val Verde and Stevenson Ranch: 16.3 per cent of the valley's people by the archive's count from the 2000 census. The..." ("1991 lines, drawn by the court's special masters")
- H "Asked by the Signal in 2004 whether the Santa Clarita Valley was now the biggest part of his district, he said, "It is the biggest single component."" ("newsmaker of the week: assemblyman keith s. richman, republican incumbent, 38th district")
- H "In the 2004 interview he said he had been "involved and tried to help" Henry Mayo Newhall Memorial Hospital through the money troubles that ended with the hospital out of Chapter 11 bankruptcy, and that he had met the state Department of Toxic Substances..." ("newsmaker of the week: assemblyman keith s. richman, republican incumbent, 38th district")
- "The City of Santa Clarita credited him, with State Senator George Runner and Governor Arnold Schwarzenegger, for its designation in 2006 as a state Enterprise Zone, which took in 98 per cent of the City's commercial, business and industrial land. The City's..." ("in 2006, the city was named as a new enterprise zone")
- "The Signal photographed him at the valley's Fourth of July parade in Newhall in 2005." ("2005 scv fourth of july parade")
- "With the radio station KHTS he sponsored the first annual tour of Sacramento for the valley's community leaders, an effort his successor, Cameron Smyth, carried on; at his death Mayor Laurene Weste spoke of working with him "on so many issues impacting Santa..." (archive record #31239)
- "In Sacramento he was known for working with Democrats on the state's budget deficits, workers' compensation and public works, and the California Journal named him the Legislature's Rookie of the Year." (dailynews.com/2010/07/31/former-assemblyman-keith-richman-dies)
- "In 2005 he campaigned for Proposition 77, to take the drawing of district lines from the Legislature, and for Proposition 76, which he called "particularly important for areas like Santa Clarita" because it would keep transportation money from being moved..." ("newsmaker of the week: assemblyman keith richman")
- "Cameron Smyth succeeded him in the 38th District that December." (California Secretary of State, Statement of Vote)

### Kevin McCarthy (#29466): 2

- "He had worked on Thomas's staff from 1987 to 2002, and served in the State Assembly from 2002 to 2007, as its Republican minority leader from 2004 to 2006." (Biographical Directory of the U.S. Congress)
- "The lines drawn by the Citizens Redistricting Commission in 2011, first used in the election of 2012, put the whole valley in the 25th District, and McCarthy's district held none of it after January 2013." ("2001 lines, drawn by the legislature")

### Laurene Weste (#15929): 5

- "She first stood in April 1996 and came fourth of thirteen candidates, with 3,104 votes. Elected in April 1998, third of fifteen with 5,770, she was re-elected in 2002, third of twelve with 5,516; in 2006, third of eleven with 5,241; in 2010, second of eleven..." ("archive records: the city council elections of 1996, 1998, 2002, 2006, 2010, 201")
- H "Before the council, by her own account in a biography she submitted to Congress in 2019, she was on the City Formation Committee that helped bring the city into being, and a member and chair of the City's Parks and Recreation Commission from 1987 to 1998." (congress.gov/116/meeting/house/109217/witnesses/hhrg-116-ii10-bio-westel-20190402.pdf)
- H "As a commissioner, the City says, she oversaw the establishment of parks, the preservation of open space and the building of the cross-town trail system." (City of Santa Clarita biographies (the City's own account of a member))
- H "Open space has been her cause. She told Congress that she led the effort to create the Santa Clarita Open Space Preservation District, the first such district, which has helped preserve more than 11,000 acres in and around the valley; that she was the..." (congress.gov/116/meeting/house/109217/witnesses/hhrg-116-ii10-bio-westel-20190402.pdf)
- H "Her campaign credits her with leading the transfer of the 160-acre William S. Hart Park and Museum from Los Angeles County to the city, with the 720-acre Haskell Canyon Open Space, and with opposition to a county prison proposed for Saugus and a state..." (laureneweste.com)

### Laurie Ender (#23087): 2

- "Elected in April 2008, first of five candidates for two seats, with 6,180 votes, she came third of five in 2012, with 5,408, and was not re-elected." ("archive records: the city council elections of april 8, 2008 and april 10, 2012")
- H "Before the council, by the City's account, she had been a Parks, Recreation and Community Services commissioner since 2003 and chaired the commission in 2006 and 2007, working on Todd Longshore Park, Veterans Memorial Plaza and Valencia Heritage Park. She..." (City of Santa Clarita biographies (the City's own account of a member))

### Leon Worden (#279): 3

- "Writing for COINage, the largest numismatic monthly in the country, he won the James L. Miller Memorial Award for the best numismatic article in any medium, worldwide, in 2008, for "Mr. Brenner's Lincoln: Forgotten Figures," the second part of a series in..." (scvhistory.com/scvhistory/signal/worden)
- "On SCVTV he has hosted the interview programs "Legacy" and "SCV Newsmaker of the Week."" (scvhistory.com/scvhistory/signal/worden)
- "He appears, in archive footage, in Jesse Cash's documentary Forgotten Tragedy: The Story of the St. Francis Dam (2018)." (imdb.com/title/tt6210054/fullcredits)

### Lisa Eichman (#29109): 1

- "Lisa Eichman has been a member of the City's Planning Commission since October 2010; her present term runs to December 2026. She has lived in Valencia since 1986, and is a founding partner of Eichman &amp; Eichman, Tax and Accounting, and an owner of..." (santaclarita.gov/commission-information/planning-commission)

### Maria Gutzeit (#21582): 1

- "She is an environmental engineer, with a degree in chemical engineering from the University of Illinois at Urbana-Champaign. She began her career with Amoco Oil, Waste Management of California and Anheuser-Busch, as site environmental engineer for three..." (yourscvwater.com/governance/board-directors)

### Marsha McLean (#23085): 4

- "She stood for the council twice before she won: in April 1998, fifth of fifteen candidates with 4,531 votes, and in April 2000, third of eleven with 4,201. She was elected in April 2002, second of twelve with 6,117 votes; re-elected in 2006 and 2010, first..." ("archive records: the city council elections of 1998, 2000, 2002, 2006, 2010, 201")
- "The term won in April 2014 ran to December 2018 because of the move." ("archive records: the city council elections of 1998, 2000, 2002, 2006, 2010, 201")
- H "The City's biography says that before the council she was a program analyst for special projects for the City of Santa Clarita, and before that worked for the Los Angeles Police Department and for a Los Angeles city councilman, and for the U.S. government at..." (City of Santa Clarita biographies (the City's own account of a member))
- H "Her public causes, as the City gives them, have been the canyons and transportation. She founded the S.C.V. Canyons Preservation Committee, which co-sponsored legislation to fund the preservation of Whitney and Elsmere Canyons, and organized opposition to a..." (City of Santa Clarita biographies (the City's own account of a member))

### Michael D. Antonovich (#29284): 12

- "He took the seat from Baxter Ward in 1980." ("supervisor candidates challenge each others' records")
- "Until the City of Santa Clarita was formed in 1987, the supervisor's office was the valley's contact with local government, as Leon Worden put it." (archive record #12300, "Women have always run the SCV (articles)")
- "He chaired the California Republican Party in 1985 and 1986." (aqmd.gov/bios/bm_antonovich_michael.html)
- "His Santa Clarita Valley field deputy from 1980 was Jo Anne Darcy, who stayed in the post for twenty years, through her ten years on the Santa Clarita City Council and her time as the City's mayor." (archive record #12142, "Preservation Group Hammers City But Misses the Nail's Head (articles)")
- H ""Mike (Antonovich) believed in self-government, and he didn't stand in our way," she said later." (archive record #12142, "Preservation Group Hammers City But Misses the Nail's Head (articles)")
- "In November 1985 he went to Bouquet Canyon to oppose Los Angeles Mayor Tom Bradley's proposal for a state prison there, calling it "Tom Bradley's trick-or-treat," and his office led the fight against it; the plan was dropped." (archive record #28295, "City Backers Join Prison Furor (articles)")
- "In 1986 and 1987 his motions before the board brought the William S. Hart Museum under the management of the Natural History Museum of Los Angeles County, which took it over on 1 September 1987." ("operational management of william s. hart museum (news reports, 1958-1988)")
- "From 1980 he let the Santa Clarita Valley Historical Society run Heritage Junction rent-free on a section of Hart Park, and in 2003 he helped it protect the old Harry Carey Ranch buildings at Tesoro del Valle." (archive record #12142, "Preservation Group Hammers City But Misses the Nail's Head (articles)")
- "He spoke at the dedication of the North County Correctional Facility at Castaic by President George H.W. Bush in 1990." ("president george h.w. bush dedicates new north county correctional facility, 3-1-1990")
- H "In 2003, when the Castaic Lake recreation area was to close, he put money from his office and the county's general fund toward keeping it open; he opposed the Cemex sand and gravel mine in Soledad Canyon "for environmental reasons," and said that whether..." ("newsmaker of the week: supervisor michael d. antonovich")
- "He was at the opening of the Vasquez Rocks Interpretive Center at Agua Dulce in May 2013." ("open house / grand opening of vasquez rocks interpretive center")
- "Term limits approved by the county's voters in 2002 ended his service in 2016. Kathryn Barger, who had begun in his office as a college intern and was his chief deputy and chief of staff, was elected that November to succeed him." ("kathryn barger, los angeles county 5th district supervisor, 2016-")

### Michael Millar (#29214): 2

- "Michael Millar was the founding chair of the City's Arts Commission, from 2009 to 2011, and sits on it again, his term running to December 2026. An arts advocate in the valley since moving to Santa Clarita in 1993, he served on the City's Arts Advisory..." (santaclarita.gov/commission-information/arts-commission)
- "He has taught music at Cal Poly Pomona since 2004 and directed its Center for Community Engagement. A bass trombonist, he played on the Grammy-winning 2004 recording of Carlos Chávez's chamber works with Southwest Chamber Music." (santaclarita.gov/commission-information/arts-commission)

### Michael Vierra (#28928): 1

- "Michael Vierra is the superintendent of the William S. Hart Union High School District. Before that he was the district's Assistant Superintendent for Human Resources and its Deputy Superintendent for Educational Services." (hartdistrict.org/apps/pages/superintendent)

### Mike Garcia (#29334): 10

- "Mike Garcia represented the whole Santa Clarita Valley in the United States House of Representatives from May 2020 to January 2025." (Biographical Directory of the U.S. Congress)
- "Until January 2023 he sat for the 25th District, which under the 2011 lines joined the valley to Palmdale, eastern Lancaster and part of Simi Valley; from then he sat for the 27th, which under the 2021 lines joined it to Lancaster, Palmdale and part of the..." ("2011 lines, drawn by the citizens redistricting commission")
- "In Congress he kept a Santa Clarita Valley office on Tourney Road, and he gave Santa Clarita as his home." (mikegarcia.house.gov/about)
- "He took a bachelor's degree at the Naval Academy and a master's at Georgetown University, both in 1998, and served in the Navy from 1999 to 2009 and in the Navy Reserve until 2012." (Biographical Directory of the U.S. Congress)
- "By his official biography he was one of the Navy's first F/A-18 Super Hornet pilots and flew more than 30 combat missions in Operation Iraqi Freedom, and after leaving the Navy he spent eleven years as an executive of the Raytheon Company." (mikegarcia.house.gov/about)
- "He came to the House through the special election held after Katie Hill resigned the seat in November 2019. In the special primary of 3 March 2020 he finished second of twelve candidates, with 41,365 votes, 25.4 per cent, behind Christy Smith, a Democrat,..." (California Secretary of State, Statement of Vote)
- "He beat her again in the general election that November, by 169,638 votes to 169,305, a margin of 333," (California Secretary of State, Statement of Vote)
- "and a third time in 2022, in the new 27th District, by 104,624 to 91,892." (California Secretary of State, Statement of Vote)
- "In November 2024 George Whitesides, a Democrat, defeated him, by 154,040 votes to 146,050." (California Secretary of State, Statement of Vote)
- "In his last term he sat on the House Appropriations Committee, the Permanent Select Committee on Intelligence and the Committee on Science, Space, and Technology." (mikegarcia.house.gov/about)

### Nathan Keith (#29203): 1

- "Nathan Keith chairs the City's Planning Commission; his term runs to December 2028. He came to Santa Clarita in 2001 to attend The Master's University, graduating in 2004, and joined the Tejon Ranch Company in 2007, where he became senior vice president of..." (santaclarita.gov/commission-information/planning-commission)

### Patsy Ayala (#23093): 6

- "When the City's biography of her was read on 1 October 2026, she was serving as Mayor Pro Tem." (City of Santa Clarita biographies (the City's own account of a member))
- "She won the District 1 race with 4,563 votes, ahead of Bryce Jepsen with 4,142 and Tim Burkhart with 4,108." ("city council election, district 1, november 5, 2024")
- H "The City's biography describes more than a decade in public service and transportation policy before the council. It says she "served in the California State Assembly and Senate," working on transportation legislation with Caltrans, Metro and Metrolink, and..." (City of Santa Clarita biographies (the City's own account of a member))
- H "She served the Legislature as staff, not as a member: neither house's record of its members lists her." (Secretary of the Senate, Record of Members)
- "In June 2014 Assemblyman Scott Wilk named her a field representative in his district office, for local media outreach." (scvnews.com/ayala-joins-wilk-staff-hough-now-district-director)
- "A College of the Canyons biography later described her as senior field representative to Assemblywoman Suzette Martinez Valladares in the 38th District, having worked on Senator Wilk's staff and, before that, in his Assembly office." (canyons.edu/community/womensconference/biopatsyayala.php)

### Patti Rasmussen (#2591): 3

- "Patti Rasmussen, a freelance journalist who has lived in Newhall since the mid-1970s, was the education reporter for The Signal." (santaclarita.gov/commission-information/arts-commission)
- H "After the Northridge earthquake of 1994, the City says, her backyard served as a theatre while Hart High School repaired its auditorium." (santaclarita.gov/commission-information/arts-commission)
- "She served on the board of the Santa Clarita Valley Historical Society as its educational outreach chair, was an original member of the Arts Alliance, and has been president of the boards of the Santa Clarita Shakespeare Festival and the SCV Senior..." (santaclarita.gov/commission-information/arts-commission)

### Paul Nelson De La Cerda (#25407): 1

- H "He had worked in education since 2004, as a high school STEM instructor, a college professor and a college administrator. In August 2018 he said he would not stand again, having been admitted to a doctoral program in organizational change and leadership at..." (signalscv.com/2018/08/saugus-union-member-wont-seek-reelection)

### Pedro Fages (#287): 4

- "In 1772 he came back after deserters." (archive record #3929, "Don Pedro Fages (photographs)")
- H "Jerry Reynolds tells the pursuit in detail: six soldiers gone, a route by the Mojave River and the Antelope Valley and down through the Sierra Pelona, a first camp probably near Agua Dulce Springs, which Fages named for its sweet water, and, on the word of..." (Perkins/Reynolds (one source, PROFILES.md))
- "A Catalonian, he had led the Catalonian Volunteers on the 1769 march, twenty-five of them by Jerry Reynolds's count; he later fought Apaches on the Sonoran frontier and served again as governor until 1791." (Perkins/Reynolds (one source, PROFILES.md))
- field `birthplace` = Catalonia (Perkins/Reynolds (one source, PROFILES.md))

### Pete Knight (#29314): 12

- H "Asked how it felt that most Californians would remember him for opposing gay marriage, he said that he did not want "to change the definition of marriage"; he went on to Proposition 22, which he had written, the lawsuits his foundation was bringing in its..." (archive record #31412, "Newsmaker of the Week: State Sen. William J. "Pete" Knight (documents)")
- "Under the 1991 district lines both districts held the City of Santa Clarita as it then stood, Agua Dulce and the land between them, 83.7 per cent of the valley's people by the archive's count from the 2000 census; the Senate district joined them to the..." ("1991 lines, drawn by the court's special masters")
- "He lived in Palmdale." ("sen. pete knight succumbs to leukemia")
- "In 2000 he and Assemblyman George Runner supported the City Council's effort to secure $250,000 in state money toward the land for the Veterans Historical Plaza in Newhall." (archive record #4951, "Future Veterans Historical Plaza Site (photographs)")
- "He was one of the twelve men who flew the X-15 rocket plane, and flew it sixteen times." ("william j. knight, usaf, 16 flights")
- "On 29 June 1967, climbing through 107,000 feet, the aircraft lost all electrical power, and he glided it down to an emergency landing on Mud Lake, Nevada, for which he earned the Distinguished Flying Cross." (nasa.gov/history/x15/knight.html)
- "From 1968 he flew 253 combat sorties in Southeast Asia in the F-100." (nasa.gov/history/x15/knight.html)
- "He then took the question to the voters as an initiative, Proposition 22, which added to the Family Code the words that only marriage between a man and a woman was valid or recognized in California. It passed on 7 March 2000 with 61.4 per cent of the vote." (California Secretary of State, Statement of Vote)
- "After it passed he set up the Proposition 22 Legal Defense and Education Fund to defend it in court." (archive record #31412, "Newsmaker of the Week: State Sen. William J. "Pete" Knight (documents)")
- "In 2008 the California Supreme Court held the law unconstitutional, and in 2014 the Legislature repealed it." (leginfo.ca.gov/pub/13-14/bill/sen/sb_1301-1350/sb_1306_bill_20140711_status.html)
- H "In September 1996, after that bill had died, Knight told the Los Angeles Times that his middle son, David, was gay, and that a younger brother had died of complications of AIDS the summer before; of his brother he said, "We never talked about it."" (latimes.com/archives/la-xpm-1996-09-11-me-42738-story.html)
- "In March 2004, while his father was in court against San Francisco's marriage licences, David Knight married his partner at San Francisco City Hall, and told the Times, "I love my father dearly and I miss him."" ("former test pilot was foe of gay marriage")

### Phineas Banning (#18714): 10

- H "Phineas Banning drove the first stagecoach over the San Fernando Pass into the Santa Clarita Valley, in December 1854. Horace Bell, who knew him, wrote that when Fort Tejon was established, Banning's firm, Alexander & Banning, wanted to run a six-horse stage..." (Perkins/Reynolds (one source, PROFILES.md))
- "A.B. Perkins took the ride as the arrival of the first stage at Rancho San Francisco." (Perkins/Reynolds (one source, PROFILES.md))
- H "Leon Worden notes that some, Perkins perhaps among them, believe Banning's crossing was not where Beale's Cut was later made." (Perkins/Reynolds (one source, PROFILES.md))
- H "The pass stayed hard going. In May 1858 a reporter of the Daily Alta California who crossed it with Banning wrote that he took it "somewhat more carefully than Banning's usual custom," and in June 1858, when the Butterfield Overland Mail wanted the road..." ("la puerta: gateway to the santa clarita valley")
- H "In the Soledad copper boom of 1863, Perkins wrote, he "bought in" to the district's mines." (Perkins/Reynolds (one source, PROFILES.md))
- H "He was at Lang Station in Soledad Canyon on 5 September 1876, when the Southern Pacific joined its line from San Francisco to Los Angeles. After Charles Crocker drove the golden spike and the other speakers, among them Leland Stanford and the mayors of both..." ("golden spike joins rails in soledad canyon: contemporary accounts")
- H "Bell, writing in 1881, said the Southern Pacific had cleared away the thicket where Banning's stage came to rest in 1854 when it dug its San Fernando tunnel." (Perkins/Reynolds (one source, PROFILES.md))
- "He died at the Occidental Hotel in San Francisco on 8 March 1885." (loc.gov/resource/sn82014381/1885-03-10/ed-1/?sp=4)
- "At the centennial of the golden spike at Lang in 1976, his grandson Robert Banning drove the replica spike." ("lang station golden spike centennial, 1876-1976: historical program")
- field `deathDate` = March 8, 1885 (loc.gov/resource/sn82014381/1885-03-10/ed-1/?sp=4)

### Pilar Schiavo (#29320): 5

- "She won the seat at its first election, in November 2022, beating Suzette Martinez Valladares, who had held the valley's seat as the 38th District, by 522 votes: 79,852 to 79,330." (California Secretary of State, Statement of Vote)
- "She was re-elected in 2024 over Patrick Lee Gipson, 119,654 votes to 106,960," (California Secretary of State, Statement of Vote)
- "and placed first in the primary of 2 June 2026, with 55.6 per cent, going on to the November election." (California Secretary of State, Statement of Vote)
- "Her district office is in Santa Clarita." (schiavo.asmdc.org/biography)
- H "Her office's biography says that on her election the Speaker appointed her Assistant Majority Whip, and describes her before office as a nurse advocate and small business owner who worked in the labor movement for more than twenty years." (schiavo.asmdc.org/biography)

### Randy Wicks (#18663): 7

- "Randy Wicks was The Signal's editorial cartoonist for sixteen years. His cartoons were syndicated nationally and won nineteen national and regional awards." ("dedication of randy wicks memorial wall at valencia library")
- "He came to Santa Clarita from his hometown of Belmond, Iowa, in 1976, graduated from the California Institute of the Arts in 1980, and was hired at The Signal by Scott and Ruth Newhall." ("dedication of randy wicks memorial wall at valencia library")
- "His colleague Tim Whyte remembered that his licence plate read "PSN PEN": his pen could sting a politician, especially a Republican, but his cartoons almost always carried something to take the sting away." (archive record #12897, "Randy Wicks: More than just a funny face (articles)")
- H "Leon Worden, who often stood on the other side of the political fence, wrote that he always cared what Wicks thought of him." (archive record #12444, "Politics aside, Randy Wicks' opinion counted (articles)")
- "He died of a heart attack on August 3, 1996, at 41." (archive record #12897, "Randy Wicks: More than just a funny face (articles)")
- "In 1997 a Randy Wicks Memorial Wall was dedicated at the Valencia Library, with a Wicks book collection and the collection of his cartoons, given in his memory to the Friends of the Libraries of the Santa Clarita Valley." ("dedication of randy wicks memorial wall at valencia library")
- field `deathDate` = August 3, 1996 (archive record #12897, "Randy Wicks: More than just a funny face (articles)")

### Remi Nadeau (#18869): 8

- "Remi Nadeau owned a ranch in Soledad Canyon, on the north side of the canyon road near Whites Canyon," (Perkins/Reynolds (one source, PROFILES.md))
- H "and there kept a deer park, photographed about 1929 (record #3695). A later summary of the Los Angeles Times reports that he opened it in 1927 as a deer farm, at a cost of $40,000, stocked with mule deer from the Kaibab Forest, elk from Yellowstone and..." ("today in scv history")
- "He was a grandson of the Los Angeles freighter Remi Nadeau, and the son of the freighter's eldest son, Joseph Frye Nadeau." (Find a Grave)
- "He was born in Minnesota on 3 May 1867, and died on 25 November 1941 in Glendale; he is buried at Angelus Rosedale Cemetery in Los Angeles, in the lot where his father lies." (Find a Grave)
- field `birthDate` = May 3, 1867 (Find a Grave)
- field `deathDate` = November 25, 1941 (Find a Grave)
- field `birthplace` = Minnesota (Find a Grave)
- field `burialPlace` = Angelus Rosedale Cemetery, Los Angeles (Find a Grave)

### Rodolfo Acosta (#343): 2

- "Leon Worden notes that a few of the films and television episodes he appeared in were made in the Santa Clarita Valley, among them Apache Warrior (1957), which used Vasquez Rocks; no source in the archive places him here otherwise." ("rodolfo acosta co-stars in 'apache warrior' (1957)")
- field `birthDate` = July 29, 1920 ("rodolfo acosta co-stars in 'apache warrior' (1957)")

### Ruth Newhall (#15477): 1

- "Her books include "The Newhall Ranch" (1958), a history of the Newhall Land and Farming Company, which she rewrote in 1992 as "A California Legend: A History of the Newhall Land and Farming Company," and histories of the Folger coffee and Spreckels sugar..." ("a tribute to ruth waldo newhall")

### Rémi Nadeau (#339): 1

- H "With his freighting earnings he farmed nearly 3,600 acres near Florence, where his vineyard of 2,400 acres was said at his death to be the largest in the world, and built the Nadeau House at Spring and First streets, begun in 1882." (archive record #28079, "Remi Nadeau, L.A. Freighter and Hotelier, 1821-1887 (obituaries)")

### Sanford Lyon (#20224): 8

- "Sanford Lyon kept the stage station on the road north of the San Fernando Pass that carried the family name, and from 1869 was postmaster of Petroleopolis, the post office there." (Perkins/Reynolds (one source, PROFILES.md))
- "He and his twin brother Cyrus were born in Machias, Maine, in 1831, on 20 November by Jerry Reynolds's date." (archive record #2075, "25. Rest Stop (articles)")
- H "He was at the Pico Canyon oil seeps by 1866, when a Los Angeles paper reported that "Mr. S. Lyon is busy dipping the oil from the holes."" (Perkins/Reynolds (one source, PROFILES.md))
- H "Later accounts credit him with drilling the first well at Pico, with Henry Clay Wiley and William Wirt Jenkins, but they disagree on the year, 1869 or 1870, and on who his partners were." (Perkins/Reynolds (one source, PROFILES.md))
- "A well of his was pumping by 1876." ("one more well on this tract which was put down by mr. lyon, and which has been pumping fine oil for over a year.")
- H "The sources give three years for his death: 1881, 1882 and 1885." (Perkins/Reynolds (one source, PROFILES.md))
- field `birthDate` = November 20, 1831 (archive record #2075, "25. Rest Stop (articles)")
- field `birthplace` = Machias, Maine (archive record #2075, "25. Rest Stop (articles)")

### Scott Newhall (#31431): 5

- "In 1968 he introduced himself to a reporter as "Executive Editor of the San Francisco Chronicle, Publisher of the Newhall Signal."" (archive record #31723, "A Newspaper Editor's Voyage Across San Francisco Bay: San Francisco Chronicle, 1935-1971, and Other Adventures (documents)")
- H "The Signal's own history lists him as its publisher from 1963 to 1977 and its owner until 1978; other accounts differ, as the note at the foot of this page shows." ("vigilance forever: our 75th, the signal 1919-1994")
- "He was born in San Francisco on January 21, 1914, and died on October 26, 1992, at Henry Mayo Newhall Memorial Hospital in Valencia." ("scott newhall with sons skip, tony, jon and stinson beach, 1944")
- field `birthDate` = January 21, 1914 ("scott newhall with sons skip, tony, jon and stinson beach, 1944")
- field `deathDate` = October 26, 1992 ("scott newhall with sons skip, tony, jon and stinson beach, 1944")

### Sharon Runner (#29324): 1

- field `deathDate` = July 14, 2016 (Secretary of the Senate, Record of Members)

### Steve Knight (#29328): 12

- "Under the 2011 lines the 21st held 79.7 per cent of the valley's people, with the Antelope and Victor valleys, and the 25th held all of it, with Palmdale, eastern Lancaster and part of Simi Valley." ("2011 lines, drawn by the citizens redistricting commission")
- "He served in the Army from 1985 to 1987 and in the Army Reserve until 1993, and was a police officer in Los Angeles. He sat on the Palmdale City Council from 2005 to 2008 and in the Assembly from 2008 to 2012, where he was assistant minority leader from 2010." (Biographical Directory of the U.S. Congress)
- "His Assembly district, the 36th, held Acton and none of the valley." (Secretary of the Senate, Record of Members)
- "He won the 21st Senate District in November 2012, beating Star Moffatt, a Democrat, by 153,412 votes to 112,780." (California Secretary of State, Statement of Vote)
- "He was re-elected in 2016, over Bryan Caforio, by 138,755 votes to 122,406," (California Secretary of State, Statement of Vote)
- "and lost in 2018 to Katie Hill, by 111,813 to 133,209." (California Secretary of State, Statement of Vote)
- "When Hill resigned in 2019 he stood in the special primary of 3 March 2020 and finished third, with 27,911 votes, 17.1 per cent, behind Christy Smith and Mike Garcia." (California Secretary of State, Statement of Vote)
- "In Congress he took up the national memorial at the site of the St. Francis Dam, in San Francisquito Canyon, that McKeon had proposed before he retired." (archive record #26980, "Buck McKeon: Congressman, United States House of Representatives (officeHoldings)")
- "Knight introduced his first bill in July 2015, with a Castaic wilderness of about 69,000 acres, and presented it that August at Tesoro Adobe Historic Park to about 50 of the valley's leaders, residents, Native Americans and historians of the dam. He brought..." ("u.s. rep. steve knight, r-palmdale")
- H "Speaking for it in the House on 11 July 2017 he said, "It happened less than 20 miles from my house, almost 100 years ago," and the House passed it that day." ("mr. knight")
- "After he left office its terms became section 1111 of S. 47, approved on 12 March 2019, which authorized a memorial at the dam site to the victims of the disaster of 12 March 1928 and established a national monument of about 353 acres." (govinfo.gov/content/pkg/plaw-116publ9/html/plaw-116publ9.htm)
- "His office was among those that presented certificates at the groundbreaking of the new Santa Clarita Valley Sheriff's Station on Golden Valley Road on 25 July 2018." ("groundbreaking: scv sheriff's station, golden valley road")

### Susan Shapiro (#29100): 2

- "Susan Shapiro came to Santa Clarita in 1993 to manage the local public television channel, SCVTV, and was the cable company's community television manager here until 2007. She designed and equipped the SCVTV Media Center in Newhall, and worked with the video..." (santaclarita.gov/commission-information/arts-commission)
- "She sat on the City's Newhall Redevelopment Committee from 2002 to 2009 and its Citizens Public Library Advisory Committee in 2010 and 2011, and on the Hart district's ROP Advisory Committee from 2000. She chairs the City's Arts Commission, her term running..." (santaclarita.gov/commission-information/arts-commission)

### Suzette Martinez Valladares (#29318): 4

- "The 23rd is, with small changes, the old 21st District under a new number: 92 per cent of the 23rd's people in 2020 had lived in the 21st, which Scott Wilk held from 2016 to 2024, so she succeeded him in the same territory." ("census bureau, 2020 census block assignment file for california (state senate di")
- "She won the seat in November 2024 over Kipp Mueller, 190,957 votes to 173,695." (California Secretary of State, Statement of Vote)
- "When the 2021 lines replaced the 38th with the 40th, she stood for the new district in November 2022 and lost to Pilar Schiavo by 522 votes, 79,330 to 79,852." (California Secretary of State, Statement of Vote)
- H "Her Senate biography says she began her career at Six Flags Magic Mountain, studied at College of the Canyons and California State University, Northridge, and was executive director of Southern California Autism Speaks; that she manages an early childcare..." (sr23.senate.ca.gov/about-suzette)

### Thomas O. Larkin (#311): 1

- H "A.B. Perkins wrote that Thomas O. Larkin, the American consul at Monterey, told the New York Sun that a common laborer could pick up $2 a day at the San Feliciano placers, in a canyon off Piru Creek." (Perkins/Reynolds (one source, PROFILES.md))

### Tiburcio Vasquez (#285): 7

- "Tiburcio Vasquez, the California bandit, gave his name to Vasquez Rocks. From the raid on Tres Pinos in August 1873 until his capture in May 1874 he ranged widely around Elizabeth Lake and Soledad Canyon, where a brother apparently lived, moving about under..." ("a history of vasquez rocks and vicinity")
- "His first recorded crime was here too: a horse-stealing raid on a ranch on the Santa Clara River in Los Angeles County on July 15, 1857, which sent him to San Quentin." ("a history of vasquez rocks and vicinity")
- H "After the bloody raid on Tres Pinos the gang moved south, and Jim Heffner's ranch near Elizabeth Lake became a favorite hiding place. When his lieutenant Abdon Leiva found Vasquez with his wife, Rosaria, Leiva gave himself up to the Los Angeles undersheriff..." ("a history of vasquez rocks and vicinity")
- H "John M. Glenn, who wrote the county's history of the rocks in 1974, thought it a safe assumption that Vasquez was among them at one time or another, but if he hid there it was not for long: he had too many ranches to shelter him." ("a history of vasquez rocks and vicinity")
- "He was born in Monterey on April 10, 1835." ("tiburcio vasquez birthplace")
- field `birthDate` = April 10, 1835 ("tiburcio vasquez birthplace")
- field `burialPlace` = Santa Clara, California ("a history of vasquez rocks and vicinity")

### Tim Burkhart (#29104): 3

- "Tim Burkhart, a graduate of William S. Hart High School who attended College of the Canyons and took bachelor's and master's degrees at California State University, Northridge, has served on the City's Planning Commission for more than twenty years; his..." (santaclarita.gov/commission-information/planning-commission)
- "He retired as corporate vice president of maintenance and construction for Six Flags." (santaclarita.gov/commission-information/planning-commission)
- "In November 2024 he stood for the City Council in District 1 and came third of three, with 4,108 votes." ("city council election, district 1, november 5, 2024")

### Tim Whyte (#2588): 2

- H "He wrote from inside the paper. His columns speak of The Signal's general manager and columnists as colleagues, and recall an earlier time when he was "something of a rookie city editor."" (archive record #12935, "A weird week in the news (articles)")
- "They range over local life: traffic court, the water district, a Star Wars re-release, the death of a friend." (archive record #685, "Black 'N' Whyte (collections)")

### TimBen Boydston (#21946): 4

- H "By the City's account he came to the valley as a boy in 1960, went to Sulphur Springs Elementary, Placerita Junior High and Canyon High, served four years in the United States Air Force, and earned a bachelor's degree in theatre at CSUN. He ran a candy..." (City of Santa Clarita biographies (the City's own account of a member))
- H "A column of May 1996 already calls him the Guild's president." (archive record #12520, "Homeless thespians overtaking Newhall? (articles)")
- "After his appointment he kept a promise not to run in 2008, and formed the Santa Clarita Neighborhood Coalition." (City of Santa Clarita biographies (the City's own account of a member))
- "He came third of eleven in 2016 and was not re-elected, and stood again in 2018 and 2020 without winning a seat." ("archive records: the city council elections of april 9, 1996, april 13, 2010, ap")

### Tom Frew II (#31354): 4

- "The shop stayed in the family for three generations, his son Thomas M. Frew Jr. and his grandson Tom Frew IV after him, and closed in 1970." ("thomas m. frew ii, newhall blacksmith")
- "In 1913 he bought 23 acres beside William S. Hart's land in downtown Newhall; part of the Frew land is now Heritage Junction, the home of the Santa Clarita Valley Historical Society." ("thomas m. frew ii, newhall blacksmith")
- "He died on August 11, 1928." ("thomas m. frew ii, newhall blacksmith")
- field `deathDate` = August 11, 1928 ("thomas m. frew ii, newhall blacksmith")

### Tom Lackey (#29456): 4

- "Since 5 December 2022 he has sat for the 34th District, which under the 2021 lines holds Agua Dulce and about 1,700 people in the unincorporated land north of the City: about 2 per cent of the valley, the rest being in Pilar Schiavo's 40th." (California Secretary of State, Statement of Vote)
- "He won the 36th in 2014 from the sitting member, Steve Fox, 42,107 votes to 27,866," (California Secretary of State, Statement of Vote)
- "and the 34th in 2022 over Thurston "Smitty" Smith, 63,840 to 49,183, and in 2024 over Ricardo Ortega, 117,751 to 72,152." (California Secretary of State, Statement of Vote)
- H "His office's biography says that before the Assembly he served on the Palmdale Elementary School District board and the Palmdale City Council, taught special education, and worked for the California Highway Patrol for 28 years." (ad34.asmrc.org/biography)

### Tom McClintock (#29446): 9

- "Tom McClintock represented the Santa Clarita Valley's west side in the State Assembly, for the 38th District, from December 1996 to December 2000, and in the State Senate, for the 19th District, from December 2000 to December 2008." (Secretary of the Senate, Record of Members)
- "Under the 1991 district lines both seats held Castaic, Val Verde and Stevenson Ranch, 16.3 per cent of the valley's people by the archive's count from the 2000 census. The 2001 lines, which reached the 19th at the election of 2004, gave it Stevenson Ranch..." ("1991 lines, drawn by the court's special masters")
- "In 1998 the Signal's Leon Worden described him as the member who "represents Stevenson Ranch, Castaic and other areas west of Interstate 5," with "his reputation as the consummate tax fighter."" ("local republicans split over school bonds")
- H "That fall the two parted over Proposition 1A, a $9.2 billion school construction bond: McClintock co-wrote the official argument against it, and Runner supported it as "a fair compromise for getting schools built in California."" ("local republicans split over school bonds")
- "In 2006, by then a state senator from Thousand Oaks, he was a sponsor of two of the three initiatives then circulating to limit the use of eminent domain." ("north newhall, home inspections and eminent domain")
- H "For the City's book of its first twenty years, published in 2007, he wrote that "Santa Clarita has grown to be a premier community."" ("gail ortiz and diana sevanian, editors")
- "Keith Richman followed him in the 38th Assembly District in 2000, and Tony Strickland in the 19th Senate District in 2008." (archive record #29493, "Paula Boland: State Assemblymember, California State Assembly (officeHoldings)")
- "He took his seat in Congress in January 2009, and the Biographical Directory still listed him as serving in November 2020." (Biographical Directory of the U.S. Congress)
- "He won that first House election, in November 2008, in the 4th District, by 185,790 votes to 183,990 over Charlie Brown; the district's returns came from Butte, El Dorado, Lassen, Modoc, Nevada, Placer, Plumas, Sacramento and Sierra counties." (California Secretary of State, Statement of Vote)

### Tom Mix (#18702): 2

- "Two bungalows built about 1920 survive at 24247 Main Street." ("tom mix and selig polyscope crew come to newhall, 1916: news reports")
- "In 1920 Mix pleaded guilty to driving recklessly through the streets of Newhall and paid a $50 fine on the spot." ("actor tom mix fined $50 for reckless driving in newhall, 1920")

### Vincent Gelcich (#16439): 9

- "Dr. Vincent Gelcich was a physician who brought the oil seeps of Pico Canyon to the attention of the Los Angeles and San Francisco investors who went on to finance California's first successful oil operations." (archive record #28060, "Dr. Vincent Gelcich (photographs)")
- "Born in 1828 in Splitsko-Dalmatinska, in Croatia, he studied medicine in Venice and Trieste, fought with Garibaldi at the siege of Rome in 1849, and then went to San Francisco, where he opened a medical practice in 1856 and became a citizen in 1860." (archive record #28060, "Dr. Vincent Gelcich (photographs)")
- "In 1863 he married Petra Pico, a niece of Andrés Pico. In 1865, with Pico, Edward F. Beale and others, he formed the Los Angeles Asphaltum and Petroleum Mining District, which granted Andrés Pico the naphtha springs claim in Pico Canyon. In the Civil War he..." (archive record #28060, "Dr. Vincent Gelcich (photographs)")
- "By 1872 he was writing in the Los Angeles papers about the oil district's promise, and with investors' money he organized the Los Angeles Petroleum Refining Company, whose small refinery at Lyon's Station, finished in April 1874, failed." (archive record #28060, "Dr. Vincent Gelcich (photographs)")
- "He died at his home in Los Angeles on June 5, 1885, at 56, and is buried at Calvary Cemetery in East Los Angeles." (archive record #28060, "Dr. Vincent Gelcich (photographs)")
- field `birthDate` = 1828 (archive record #28060, "Dr. Vincent Gelcich (photographs)")
- field `deathDate` = June 5, 1885 (archive record #28060, "Dr. Vincent Gelcich (photographs)")
- field `birthplace` = Splitsko-Dalmatinska, Croatia (archive record #28060, "Dr. Vincent Gelcich (photographs)")
- field `burialPlace` = Calvary Cemetery, East Los Angeles (archive record #28060, "Dr. Vincent Gelcich (photographs)")

### William Mulholland (#16432): 5

- "William Mulholland was chief engineer of the Los Angeles water department. He built the Los Angeles Aqueduct, which crosses the Santa Clarita Valley, and the St. Francis Dam in San Francisquito Canyon, whose collapse on March 12, 1928, ended his career and..." (archive record #28063, "William Mulholland (photographs)")
- "The dam, a curved concrete wall 185 feet high and 600 feet long above Saugus, was built from August 1924 to the spring of 1926 to hold about a year's water for Los Angeles. At three minutes before midnight on March 12, 1928, half of it collapsed. The flood..." (archive record #28063, "William Mulholland (photographs)")
- "A government study released five days later blamed the dam's west abutment; later study found the east abutment stood on an ancient landslide, which Mulholland did not know." (archive record #28063, "William Mulholland (photographs)")
- "He took full responsibility, and died in 1935." ("the rise and fall of william mulholland")
- field `deathDate` = 1935 ("the rise and fall of william mulholland")

### William S. Hart (#16356): 11

- H "He came to Newhall in 1918, leasing the property of George Babcock Smith, which became the core of his Horseshoe Ranch. In February 1921 he bought the first of it, two lots that held the ranch house and a log cabin, and that July he bought two more parcels...." (scvhistory.com/scvhistory/mu8901.htm)
- "In 1940 he gave three lots at Spruce and Eleventh streets, with money to build and furnish a theater, to American Legion Post 507; he signed over the deed on 7 November 1940, and the American, designed by S. Charles Lee, opened on 23 May 1941 and served as..." (scvhistory.com/scvhistory/ap1516.htm)
- "The valley's first high school, which opened in 1945, was named for him in his lifetime." (Perkins/Reynolds (one source, PROFILES.md))
- H "He died at California Lutheran Hospital in Los Angeles at 11:20 p.m. on 23 June 1946, having been in the hospital since 5 June; the reference works that give Newhall as the place of his death are mistaken." (scvhistory.com/scvhistory/lae62446a.htm)
- H "His funeral was held first in the den of the ranch house and then at the Little Church of the Recessional at Forest Lawn, where Rudy Vallee sang "The Last Round-Up." He was cremated, and his ashes were to be laid in Green-Wood Cemetery in Brooklyn beside..." (scvhistory.com/scvhistory/lahe06261946b.htm)
- "His will, made at Newhall on 9 September 1944, left the ranch, "approximately 200 acres," with his home and its contents, to the County of Los Angeles, to be used "exclusively as a public park and pleasure grounds," never with a charge for admission, under..." (scvhistory.com/scvhistory/cp19440909.htm)
- H "The Friends of Hart Park give his reason in his words: "When I am gone, I want them to have my home."" (scvhistory.com/scvhistory/lw2616.htm)
- "In July 2025 the park passed to the City of Santa Clarita, which opened it as its fortieth park." (santaclarita.gov/blog/2025/07/14/william-s-hart-park-officially-opens-as-citys-40th-park)
- field `deathDate` = June 23, 1946 (scvhistory.com/scvhistory/lae62446a.htm)
- field `burialPlace` = Green-Wood Cemetery, Brooklyn, New York (his ashes) (scvhistory.com/scvhistory/lahe06261946b.htm)
- calendar `dies at California Lutheran Hospital, Los Angeles` = June 23, 1946 (scvhistory.com/scvhistory/lae62446a.htm)

### William Wirt Jenkins (#20226): 9

- "William Wirt Jenkins was a California Ranger." ("the story of ranger bill jenkins of castaic, 1835-1916")
- "Jerry Reynolds adds that he was later a county undersheriff and ranched on Castaic Creek from 1878." (Perkins/Reynolds (one source, PROFILES.md))
- H "His family Bible records him as William Willoby Jenkins; he went by Wirt." ("the story of ranger bill jenkins of castaic, 1835-1916")
- H "He wrote in 1906 that he and Sanford Lyon had visited the Pico oil springs with Francisco Lopez in 1854." ("california gold discoveries before (and after) lopez 1842")
- "In 1869, by an agreement that survives only as a typed copy, he joined Lyon and Henry Clay Wiley to drill a well at "Camp Pico."" ("the story of ranger bill jenkins of castaic, 1835-1916")
- H "He was born near Circleville, Ohio, on 12 October, in 1833 or 1835; the sources differ." (Perkins/Reynolds (one source, PROFILES.md))
- "He died on 19 October 1916." (Perkins/Reynolds (one source, PROFILES.md))
- field `deathDate` = October 19, 1916 (Perkins/Reynolds (one source, PROFILES.md))
- field `birthplace` = Circleville, Ohio (Perkins/Reynolds (one source, PROFILES.md))

### Terms, affiliations, education and candidacies resting on one source

#### County returns (CEDA or the County's statement): 170

- candidacy #25991: Aakash Ahuja: William S. Hart Union High School District board election, Trustee Area 1, November 5, 2024; elected; evidence roster
- candidacy #25747: Anna Griese: Saugus Union School District board election, Trustee Area 2, November 8, 2022; elected; evidence roster
- candidacy #28839: BOB JENSEN JR: William S. Hart Union High School District board election, Trustee Area 2, November 8, 2022; elected; evidence certified
- candidacy #25949: Bob Jensen: William S. Hart Union High School District board election, Trustee Area 2, November 6, 2018; elected; evidence roster
- candidacy #25917: Bob Jensen: William S. Hart Union High School District board election, November 3, 2009; elected; evidence roster
- candidacy #25579: Robert N. Jensen, Jr.: Newhall School District board election, November 8, 2005; elected; evidence roster
- candidacy #25629: Brian D. Walters: Newhall School District board election, Trustee Area 1, November 8, 2022; not-elected; evidence roster
- candidacy #25611: Brian Walters: Newhall School District board election, November 5, 2013; elected; evidence roster
- candidacy #30012: Bruce D. Fortine: Santa Clarita Community College District board election, November 8, 2011; elected; evidence contemporary
- candidacy #29964: Bruce D. Fortine: Santa Clarita Community College District board election, November 4, 2003; elected; evidence contemporary
- candidacy #29922: Bruce Fortine: Santa Clarita Community College District board election, November 7, 1995; elected; evidence contemporary
- candidacy #25979: Cherise Moore: William S. Hart Union High School District board election, Trustee Area 3, November 8, 2022; elected; evidence roster
- candidacy #25753: Christopher Trunkey: Saugus Union School District board election, Trustee Area 5, November 8, 2022; elected; evidence roster
- candidacy #25723: Chris Trunkey: Saugus Union School District board election, Trustee Area 5, November 6, 2018; elected; evidence roster
- candidacy #25697: Chris Trunkey: Saugus Union School District board election, November 5, 2013; not-elected; evidence roster
- candidacy #25609: Christy Smith: Newhall School District board election, November 5, 2013; elected; evidence roster
- candidacy #25599: Christy L. Smith: Newhall School District board election, November 3, 2009; elected; evidence roster
- candidacy #25591: Christy Smith: Newhall School District board election, November 6, 2007; not-elected; evidence roster
- candidacy #30064: Darlene Trevino: Santa Clarita Community College District board election, November 5, 2024; elected; evidence contemporary
- candidacy #25709: David "CD" Barlavi: Saugus Union School District board election, Trustee Area 1, November 6, 2018; elected; evidence roster
- candidacy #25895: Dennis V. King: William S. Hart Union High School District board election, November 8, 2005; elected; evidence roster
- candidacy #25865: Dennis V. King: William S. Hart Union High School District board election, November 6, 2001; elected; evidence roster
- candidacy #25843: Dennis V. King: William S. Hart Union High School District board election, November 4, 1997; elected; evidence roster
- candidacy #25695: Douglas Bryce: Saugus Union School District board election, November 5, 2013; elected; evidence roster
- candidacy #25677: Douglas A. Bryce: Saugus Union School District board election, November 8, 2005; elected; evidence roster
- candidacy #25669: Douglas A. Bryce: Saugus Union School District board election, November 2, 1999; not-elected; evidence roster
- candidacy #25661: Douglas A. Bryce: Saugus Union School District board election, November 4, 1997; not-elected; evidence roster
- candidacy #30068: Edel Alonso: Santa Clarita Community College District board election, November 5, 2024; elected; evidence contemporary
- candidacy #30046: Edel Alonso: Santa Clarita Community College District board election, November 3, 2020; elected; evidence contemporary
- candidacy #25999: Erin Wilson: William S. Hart Union High School District board election, Trustee Area 4, November 5, 2024; elected; evidence roster
- candidacy #29980: Ernie Tichenor: Santa Clarita Community College District board election, November 8, 2005; elected; evidence contemporary
- candidacy #29944: Ernie Tichenor: Santa Clarita Community College District board election, November 6, 2001; elected; evidence contemporary
- candidacy #29936: Ernie Tichenor: Santa Clarita Community College District board election, November 4, 1997; elected; evidence contemporary
- candidacy #30074: Fred Arnold: Santa Clarita Community College District board election, November 5, 2024; elected; evidence contemporary
- candidacy #30050: Fred Arnold: Santa Clarita Community College District board election, November 3, 2020; not-elected; evidence contemporary
- candidacy #25657: Gary G. Murr: Saugus Union School District board election, November 4, 1997; elected; evidence roster
- candidacy #25649: Gary G. Murr: Saugus Union School District board election, November 7, 1995; not-elected; evidence roster
- candidacy #25833: George Aliano: William S. Hart Union High School District board election, November 7, 1995; elected; evidence roster
- candidacy #25993: Gloria Mercado-Fortine: William S. Hart Union High School District board election, Trustee Area 1, November 5, 2024; not-elected; evidence roster
- candidacy #25929: Gloria Mercado-Fortine: William S. Hart Union High School District board election, November 8, 2011; elected; evidence roster
- candidacy #25909: Gloria Mercado-Fortine: William S. Hart Union High School District board election, November 6, 2007; elected; evidence roster
- candidacy #25881: Gloria Mercado: William S. Hart Union High School District board election, November 4, 2003; elected; evidence roster
- candidacy #25871: Gloria Mercado: William S. Hart Union High School District board election, November 6, 2001; not-elected; evidence roster
- candidacy #25839: Gloria E. Mercado: William S. Hart Union High School District board election, November 4, 1997; elected; evidence roster
- candidacy #25827: Gloria E. Mercado: William S. Hart Union High School District board election, November 7, 1995; not-elected; evidence roster
- candidacy #25973: James Webb: William S. Hart Union High School District board election, Trustee Area 4, November 3, 2020; elected; evidence roster
- candidacy #30082: Jerry Danielsen: Santa Clarita Community College District board election, November 5, 2024; not-elected; evidence contemporary
- candidacy #30056: Jerry Danielsen: Santa Clarita Community College District board election, November 3, 2020; not-elected; evidence contemporary
- candidacy #30040: Joan Mac Gregor: Santa Clarita Community College District board election, November 6, 2018; elected; evidence contemporary
- candidacy #30002: John Whaling MacGregor: Santa Clarita Community College District board election, November 3, 2009; elected; evidence contemporary
- candidacy #29984: Joan Whaling MacGregor: Santa Clarita Community College District board election, November 8, 2005; elected; evidence contemporary
- candidacy #29950: Joan Whaling MacGregor: Santa Clarita Community College District board election, November 6, 2001; elected; evidence contemporary
- candidacy #29940: Joan Whaling MacGregor: Santa Clarita Community College District board election, November 4, 1997; elected; evidence contemporary
- candidacy #25985: Joe Messina: William S. Hart Union High School District board election, Trustee Area 5, November 8, 2022; elected; evidence roster
- candidacy #25957: Joe Messina: William S. Hart Union High School District board election, Trustee Area 5, November 6, 2018; elected; evidence roster
- candidacy #25921: Joe Messina: William S. Hart Union High School District board election, November 3, 2009; elected; evidence roster
- candidacy #25913: Joe Messina: William S. Hart Union High School District board election, November 6, 2007; not-elected; evidence roster
- candidacy #25897: Joe Messina: William S. Hart Union High School District board election, November 8, 2005; not-elected; evidence roster
- candidacy #25887: Joseph Vincent Messina: William S. Hart Union High School District board election, November 4, 2003; not-elected; evidence roster
- candidacy #29932: John D. Hoskinson: Santa Clarita Community College District board election, November 4, 1997; not-elected; evidence contemporary
- candidacy #25823: John R. Hassel: William S. Hart Union High School District board election, November 7, 1995; elected; evidence roster
- candidacy #25597: John Michael McGrath: Newhall School District board election, November 3, 2009; elected; evidence roster
- candidacy #25577: J. Micheal McGrath: Newhall School District board election, November 8, 2005; elected; evidence roster
- candidacy #25703: Julie Olsen: Saugus Union School District board election, Trustee Area 3, November 8, 2016; elected; evidence roster
- candidacy #25817: Kerry Clegg: Sulphur Springs Union School District board election, November 5, 2013; elected; evidence roster
- candidacy #25783: Kerry B. Clegg: Sulphur Springs Union School District board election, November 6, 2001; elected; evidence roster
- candidacy #25749: Laura Arrowsmith: Saugus Union School District board election, Trustee Area 2, November 8, 2022; not-elected; evidence roster
- candidacy #25717: Laura Arrowsmith: Saugus Union School District board election, Trustee Area 2, November 6, 2018; elected; evidence roster
- candidacy #25455: Lester M. Freeman: Castaic Union School District board election, November 7, 1995; elected; evidence roster
- candidacy #25995: Linda Hovis Storli: William S. Hart Union High School District board election, Trustee Area 1, November 5, 2024; not-elected; evidence roster
- candidacy #25965: Linda Hovis Storli: William S. Hart Union High School District board election, Trustee Area 1, November 3, 2020; elected; evidence roster
- candidacy #25937: Linda Storli: William S. Hart Union High School District board election, Trustee Area 1, November 3, 2015; elected; evidence roster
- candidacy #29992: Michael David Berger: Santa Clarita Community College District board election, November 3, 2009; elected; evidence contemporary
- candidacy #25647: Michael P. Kennick: Saugus Union School District board election, November 7, 1995; elected; evidence roster
- candidacy #25587: Michael R. Shapiro: Newhall School District board election, November 6, 2007; elected; evidence roster
- candidacy #25569: Michael R. Shapiro: Newhall School District board election, November 4, 2003; elected; evidence roster
- candidacy #30058: Michele R. Jenkins: Santa Clarita Community College District board election, November 3, 2020; elected; evidence contemporary
- candidacy #30008: Michele R. Jenkins: Santa Clarita Community College District board election, November 8, 2011; elected; evidence contemporary
- candidacy #29956: Michele R. Jenkins: Santa Clarita Community College District board election, November 4, 2003; elected; evidence contemporary
- candidacy #29918: Michele Jenkins: Santa Clarita Community College District board election, November 7, 1995; elected; evidence contemporary
- candidacy #25891: Patricia Hanrion: William S. Hart Union High School District board election, November 8, 2005; elected; evidence roster
- candidacy #25869: Patricia A. Hanrion: William S. Hart Union High School District board election, November 6, 2001; elected; evidence roster
- candidacy #25841: Patricia A. Hanrion: William S. Hart Union High School District board election, November 4, 1997; elected; evidence roster
- candidacy #25693: Paul Nelson De La Cerda: Saugus Union School District board election, November 5, 2013; elected; evidence roster
- candidacy #25675: Paul de la Cerda: Saugus Union School District board election, November 8, 2005; elected; evidence roster
- candidacy #25919: Paul B. Strickland: William S. Hart Union High School District board election, November 3, 2009; elected; evidence roster
- candidacy #25893: Paul B. Strickland: William S. Hart Union High School District board election, November 8, 2005; elected; evidence roster
- candidacy #25867: Paul B. Strickland: William S. Hart Union High School District board election, November 6, 2001; elected; evidence roster
- candidacy #25853: Paul Strickland: William S. Hart Union High School District board election, November 2, 1999; not-elected; evidence roster
- candidacy #25767: Paul Strickland: Sulphur Springs Union School District board election, November 7, 1995; elected; evidence roster
- candidacy #25825: Paula Olivares: William S. Hart Union High School District board election, November 7, 1995; elected; evidence roster
- candidacy #25901: Philip C. Ellis, Jr.: William S. Hart Union High School District board election, November 8, 2005; not-elected; evidence roster
- candidacy #25885: Philip C. Ellis, Jr.: William S. Hart Union High School District board election, November 4, 2003; not-elected; evidence roster
- candidacy #25849: Philip C. Ellis: William S. Hart Union High School District board election, November 2, 1999; elected; evidence roster
- candidacy #25613: Philip Ellis, Jr.: Newhall School District board election, November 5, 2013; elected; evidence roster
- candidacy #25601: Philip C. Ellis, Jr.: Newhall School District board election, November 3, 2009; elected; evidence roster
- candidacy #25549: Philip C. Ellis, Jr.: Newhall School District board election, November 7, 1995; elected; evidence roster
- candidacy #29934: Richard G. Peoples: Santa Clarita Community College District board election, November 4, 1997; not-elected; evidence contemporary
- candidacy #29928: Richard G. Peoples: Santa Clarita Community College District board election, November 7, 1995; not-elected; evidence contemporary
- candidacy #25557: Ron Winkler: Newhall School District board election, November 2, 1999; elected; evidence roster
- candidacy #25547: Ron Winkler: Newhall School District board election, November 7, 1995; elected; evidence roster
- candidacy #29970: Ron Gillis: Santa Clarita Community College District board election, November 4, 2003; elected; evidence contemporary
- candidacy #29926: Ron Gillis: Santa Clarita Community College District board election, November 7, 1995; elected; evidence contemporary
- candidacy #25683: Rose Koscielny: Saugus Union School District board election, November 8, 2011; elected; evidence roster
- candidacy #25665: Rose Koscielny: Saugus Union School District board election, November 2, 1999; elected; evidence roster
- candidacy #25645: Rose Koscielny: Saugus Union School District board election, November 7, 1995; elected; evidence roster
- candidacy #30018: Scott T. Wilk: Santa Clarita Community College District board election, November 8, 2011; elected; evidence contemporary
- candidacy #30052: Sebastian Cazares: Santa Clarita Community College District board election, November 3, 2020; elected; evidence contemporary
- candidacy #30084: Sharlene Rose Johnson: Santa Clarita Community College District board election, November 5, 2024; elected; evidence contemporary
- candidacy #25755: Sharlene Rose Duzick: Saugus Union School District board election, Trustee Area 5, November 8, 2022; not-elected; evidence roster
- candidacy #25725: Sharlene Duzick: Saugus Union School District board election, Trustee Area 5, November 6, 2018; not-elected; evidence roster
- candidacy #25685: Stephen S. Winkler: Saugus Union School District board election, November 8, 2011; elected; evidence roster
- candidacy #25975: Steven M. Sturgeon: William S. Hart Union High School District board election, Trustee Area 4, November 3, 2020; not-elected; evidence roster
- candidacy #25943: Steven M. Sturgeon: William S. Hart Union High School District board election, Trustee Area 4, November 3, 2015; elected; evidence roster
- candidacy #25931: Steven M. Sturgeon: William S. Hart Union High School District board election, November 8, 2011; elected; evidence roster
- candidacy #25911: Steven M. Sturgeon: William S. Hart Union High School District board election, November 6, 2007; elected; evidence roster
- candidacy #25879: Steven M. Sturgeon: William S. Hart Union High School District board election, November 4, 2003; elected; evidence roster
- candidacy #25851: Steven M. Sturgeon: William S. Hart Union High School District board election, November 2, 1999; elected; evidence roster
- candidacy #25923: Suzan Solomon: William S. Hart Union High School District board election, November 3, 2009; not-elected; evidence roster
- candidacy #25639: Suzan T. Solomon: Newhall School District board election, Trustee Area 5, November 5, 2024; elected; evidence roster
- candidacy #25589: Suzan Teri Solomon: Newhall School District board election, November 6, 2007; elected; evidence roster
- candidacy #25567: Suzan Teri Solomon: Newhall School District board election, November 4, 2003; elected; evidence roster
- candidacy #25559: Suzan T. Solomon: Newhall School District board election, November 2, 1999; elected; evidence roster
- candidacy #25981: Teresa Todd: William S. Hart Union High School District board election, Trustee Area 3, November 8, 2022; not-elected; evidence roster
- candidacy #25883: M. Teresa Todd: William S. Hart Union High School District board election, November 4, 2003; not-elected; evidence roster
- candidacy #25775: M. Teresa Todd: Sulphur Springs Union School District board election, November 2, 1999; elected; evidence roster
- candidacy #25475: Thomas L. Caesar: Castaic Union School District board election, November 2, 1999; elected; evidence roster
- candidacy #25453: Tom L. Caesar: Castaic Union School District board election, November 7, 1995; elected; evidence roster
- candidacy #25523: Victor M. Torres: Castaic Union School District board election, November 3, 2009; elected; evidence roster
- candidacy #25845: William "Bill" Dinsenbacher: William S. Hart Union High School District board election, November 4, 1997; not-elected; evidence roster
- candidacy #29948: William J. Broyles: Santa Clarita Community College District board election, November 6, 2001; not-elected; evidence contemporary
- term #28471: Anna Griese: School Board Member, Saugus Union School District; December 2022 to December 2026; evidence roster
- term #28584: Bob Jensen: School Board Member, William S. Hart Union High School District; December 2022 to December 2026; evidence certified
- term #28582: Bob Jensen: School Board Member, William S. Hart Union High School District; December 2018 to December 2022; evidence roster
- term #28447: Bob Jensen: School Board Member, Newhall School District; December 2005 to December 2009; evidence roster
- term #30601: Brian Walters: School Board Member, Newhall School District; December 2013 to December 2018; evidence roster
- term #30285: Bruce D. Fortine: College Trustee, Santa Clarita Community College District; December 2003 to December 2007; evidence contemporary
- term #30281: Bruce D. Fortine: College Trustee, Santa Clarita Community College District; December 1995 to December 1999; evidence contemporary
- term #28586: Cherise Moore: School Board Member, William S. Hart Union High School District; December 2022 to December 2026; evidence roster
- term #29656: Christopher Trunkey: School Board Member, Saugus Union School District; December 2018; evidence roster
- term #29654: Christopher Trunkey: School Board Member, Saugus Union School District; December 2016 to December 2018; evidence certified
- term #28475: David Barlavi: School Board Member, Saugus Union School District; December 2018 to December 2022; evidence roster
- term #30377: Ernest L. Tichenor: College Trustee, Santa Clarita Community College District; December 2001 to December 2005; evidence contemporary
- term #30375: Ernest L. Tichenor: College Trustee, Santa Clarita Community College District; December 1997 to December 2001; evidence contemporary
- term #23359: Jason Gibbs: City Council Member, The City of Santa Clarita; December 2020 to December 2024; evidence roster
- term #30365: Joan W. MacGregor: College Trustee, Santa Clarita Community College District; December 2018 to December 2022; evidence contemporary
- term #30361: Joan W. MacGregor: College Trustee, Santa Clarita Community College District; December 2009 to December 2013; evidence contemporary
- term #30359: Joan W. MacGregor: College Trustee, Santa Clarita Community College District; December 2005 to December 2009; evidence contemporary
- term #30357: Joan W. MacGregor: College Trustee, Santa Clarita Community College District; December 2001 to December 2005; evidence contemporary
- term #30355: Joan W. MacGregor: College Trustee, Santa Clarita Community College District; December 1997 to December 2001; evidence contemporary
- term #28936: Joe Messina: School Board Member, William S. Hart Union High School District; December 2022 to December 2026; evidence certified
- term #28453: John Michael McGrath: School Board Member, Newhall School District; December 2005 to December 2009; evidence roster
- term #28527: Lester Freeman: School Board Member, Castaic Union School District; December 1995 to December 1999; evidence roster
- term #28614: Linda Storli: School Board Member, William S. Hart Union High School District; December 2020 to December 2024; evidence roster
- term #28612: Linda Storli: School Board Member, William S. Hart Union High School District; December 2015 to December 2020; evidence roster
- term #28491: Michael Kennick: School Board Member, Saugus Union School District; December 1995 to December 1999; evidence roster
- term #30337: Michele R. Jenkins: College Trustee, Santa Clarita Community College District; December 2003 to December 2007; evidence contemporary
- term #30333: Michele R. Jenkins: College Trustee, Santa Clarita Community College District; December 1995 to December 1999; evidence contemporary
- term #23401: Patsy Ayala: City Council Member, The City of Santa Clarita; December 2024; evidence certified
- term #28459: Philip Ellis Jr.: School Board Member, Newhall School District; December 2009 to December 2013; evidence roster
- term #28457: Philip Ellis Jr.: School Board Member, Newhall School District; December 1995 to December 1999; evidence roster
- term #28467: Ron Winkler: School Board Member, Newhall School District; December 1999 to December 2003; evidence roster
- term #28465: Ron Winkler: School Board Member, Newhall School District; December 1995 to December 1999; evidence roster
- term #30369: Ronald E. Gillis: College Trustee, Santa Clarita Community College District; December 1995 to December 1999; evidence contemporary
- term #28638: Steven Sturgeon: School Board Member, William S. Hart Union High School District; December 2015 to December 2020; evidence roster
- term #29640: Suzan Solomon: School Board Member, Newhall School District; December 2015 to December 2024; evidence certified
- term #28513: Teresa Todd: School Board Member, Sulphur Springs Union School District; December 1999 to December 2003; evidence roster
- term #28541: Tom Caesar: School Board Member, Castaic Union School District; December 1999 to December 2003; evidence roster
- term #28539: Tom Caesar: School Board Member, Castaic Union School District; December 1995 to December 1999; evidence roster
- term #28543: Victor Torres: School Board Member, Castaic Union School District; December 2009 to December 2013; evidence roster

#### Hart board roster (Leon Worden): 43

- term #28735: C. L. Dillenbeck: School Board Member, William S. Hart Union High School District; 1948 to 1952; evidence roster
- term #28775: Carroll Word: School Board Member, William S. Hart Union High School District; 1970 to June 30, 1974; evidence roster
- term #28727: Charles Brown: School Board Member, William S. Hart Union High School District; March 9, 1945 to 1948; evidence roster
- term #28743: Charleton Hadley: School Board Member, William S. Hart Union High School District; 1953 to 1959; evidence roster
- term #28739: Chester Allen: School Board Member, William S. Hart Union High School District; 1952 to 1956; evidence roster
- term #28799: Clara Stroup: School Board Member, William S. Hart Union High School District; December 1979 to December 1991; evidence roster
- term #28588: Connie Worden: School Board Member, William S. Hart Union High School District; December 1, 1974 to December 1979; evidence roster
- term #28771: David Holden: School Board Member, William S. Hart Union High School District; 1969 to 1973; evidence roster
- term #28590: Dennis King: School Board Member, William S. Hart Union High School District; December 1985 to December 1989; evidence roster
- term #28759: Earl Schmidt: School Board Member, William S. Hart Union High School District; 1962 to 1969; evidence roster
- term #28753: Edith Palmer: School Board Member, William S. Hart Union High School District; 1957 to 1968; evidence roster
- term #28767: Edward Duarte: School Board Member, William S. Hart Union High School District; 1968 to 1970; evidence roster
- term #28765: Elisha Agajanian: School Board Member, William S. Hart Union High School District; 1968 to 1972; evidence roster
- term #28769: Emmett Carraher: School Board Member, William S. Hart Union High School District; 1969 to 1970; evidence roster
- term #28749: Ernest Malam: School Board Member, William S. Hart Union High School District; 1956 to 1957; evidence roster
- term #28795: Gerald Heidt: School Board Member, William S. Hart Union High School District; December 1979 to December 1991; evidence roster
- term #28741: Howard Blackwell: School Board Member, William S. Hart Union High School District; 1953 to 1954; evidence roster
- term #28747: Howard Gulley: School Board Member, William S. Hart Union High School District; 1956 to 1962; evidence roster
- term #28793: James Putjenter: School Board Member, William S. Hart Union High School District; 1978 to December 1979; evidence roster
- term #28763: Jereann Bowman: School Board Member, William S. Hart Union High School District; 1969 to 1969; evidence roster
- term #28761: Jereann Bowman: School Board Member, William S. Hart Union High School District; 1964 to 1968; evidence roster
- term #28791: Jim Shuman: School Board Member, William S. Hart Union High School District; 1978 to July 1978; evidence roster
- term #28608: John Hassel: School Board Member, William S. Hart Union High School District; December 1991 to December 1995; evidence roster
- term #28745: Julio Lombardi: School Board Member, William S. Hart Union High School District; 1955 to 1962; evidence roster
- term #28783: Kenneth Wullschleger: School Board Member, William S. Hart Union High School District; 1973 to January 23, 1979; evidence roster
- term #28789: Louis Brathwaite: School Board Member, William S. Hart Union High School District; April 12, 1977 to December 1981; evidence roster
- term #28725: Mary Bonelli: School Board Member, William S. Hart Union High School District; March 9, 1945 to 1957; evidence roster
- term #28733: Mildred Gilmour: School Board Member, William S. Hart Union High School District; March 9, 1945 to 1956; evidence roster
- term #28616: Patricia Hanrion: School Board Member, William S. Hart Union High School District; December 1993 to December 1997; evidence roster
- term #28785: Patrick Shaughnessy: School Board Member, William S. Hart Union High School District; April 1, 1975 to January 1978; evidence roster
- term #28624: Paula Olivares: School Board Member, William S. Hart Union High School District; December 1991 to December 1995; evidence roster
- term #28781: Robert Crozier: School Board Member, William S. Hart Union High School District; 1973 to April 1977; evidence roster
- term #28797: Robert Keysor: School Board Member, William S. Hart Union High School District; December 1979 to December 1985; evidence roster
- term #28779: Ruth Kelley: School Board Member, William S. Hart Union High School District; 1971 to April 1975; evidence roster
- term #28777: S. A. Wright: School Board Member, William S. Hart Union High School District; 1970 to 1973; evidence roster
- term #28729: S. S. Donaldson: School Board Member, William S. Hart Union High School District; March 9, 1945 to 1953; evidence roster
- term #28787: Sheldon Allen: School Board Member, William S. Hart Union High School District; 1977 to December 1979; evidence roster
- term #28773: Thomas Hanson: School Board Member, William S. Hart Union High School District; 1970 to May 25, 1977; evidence roster
- term #28731: Thomas M. Frew Jr.: School Board Member, William S. Hart Union High School District; March 9, 1945 to 1951; evidence roster
- term #28755: W. D. Ross: School Board Member, William S. Hart Union High School District; 1959 to 1969; evidence roster
- term #28737: Walter Cook: School Board Member, William S. Hart Union High School District; 1951 to 1955; evidence roster
- term #30606: William Dinsenbacher: School Board Member, William S. Hart Union High School District; December 1993 to December 1997; evidence roster
- term #30604: William Dinsenbacher: School Board Member, William S. Hart Union High School District; December 1989 to December 1993; evidence roster

#### Leon Worden's City Council ledger: 32

- term #23319: Bill Miranda: City Council Member, The City of Santa Clarita; December 2018 to December 2022; evidence roster
- term #23317: Bill Miranda: City Council Member, The City of Santa Clarita; 2017 to December 2018; evidence roster
- term #23329: Bob Kellar: City Council Member, The City of Santa Clarita; December 2016 to December 2020; evidence roster
- term #23327: Bob Kellar: City Council Member, The City of Santa Clarita; April 2008 to April 2012; evidence roster
- term #23325: Bob Kellar: City Council Member, The City of Santa Clarita; April 2004 to April 2008; evidence roster
- term #23323: Bob Kellar: City Council Member, The City of Santa Clarita; April 2000 to April 2004; evidence roster
- term #23337: Carl Boyer: City Council Member, The City of Santa Clarita; April 1994 to April 1998; evidence roster
- term #23335: Carl Boyer: City Council Member, The City of Santa Clarita; April 1990 to April 1994; evidence roster
- term #23349: Frank Ferry: City Council Member, The City of Santa Clarita; April 2010 to April 2014; evidence roster
- term #23347: Frank Ferry: City Council Member, The City of Santa Clarita; April 2006 to April 2010; evidence roster
- term #23345: Frank Ferry: City Council Member, The City of Santa Clarita; April 2002 to April 2006; evidence roster
- term #23343: Frank Ferry: City Council Member, The City of Santa Clarita; April 1998 to April 2002; evidence roster
- term #23357: Jan Heidt: City Council Member, The City of Santa Clarita; April 1996 to April 2000; evidence roster
- term #23355: Jan Heidt: City Council Member, The City of Santa Clarita; April 1992 to April 1996; evidence roster
- term #23363: Jill Klajic: City Council Member, The City of Santa Clarita; April 1996 to April 2000; evidence roster
- term #23361: Jill Klajic: City Council Member, The City of Santa Clarita; April 1990 to April 1994; evidence roster
- term #23371: Jo Anne Darcy: City Council Member, The City of Santa Clarita; April 1998 to April 2002; evidence roster
- term #23369: Jo Anne Darcy: City Council Member, The City of Santa Clarita; April 1994 to April 1998; evidence roster
- term #23367: Jo Anne Darcy: City Council Member, The City of Santa Clarita; April 1990 to April 1994; evidence roster
- term #23383: Laurene Weste: City Council Member, The City of Santa Clarita; December 2018 to December 2022; evidence roster
- term #23381: Laurene Weste: City Council Member, The City of Santa Clarita; April 2014 to December 2018; evidence roster
- term #23379: Laurene Weste: City Council Member, The City of Santa Clarita; April 2010 to April 2014; evidence roster
- term #23377: Laurene Weste: City Council Member, The City of Santa Clarita; April 2006 to April 2010; evidence roster
- term #23375: Laurene Weste: City Council Member, The City of Santa Clarita; April 2002 to April 2006; evidence roster
- term #23373: Laurene Weste: City Council Member, The City of Santa Clarita; April 1998 to April 2002; evidence roster
- term #23387: Laurie Ender: City Council Member, The City of Santa Clarita; April 2008 to April 2012; evidence roster
- term #23397: Marsha McLean: City Council Member, The City of Santa Clarita; December 2018 to December 2022; evidence roster
- term #23395: Marsha McLean: City Council Member, The City of Santa Clarita; April 2014 to December 2018; evidence roster
- term #23393: Marsha McLean: City Council Member, The City of Santa Clarita; April 2010 to April 2014; evidence roster
- term #23391: Marsha McLean: City Council Member, The City of Santa Clarita; April 2006 to April 2010; evidence roster
- term #23389: Marsha McLean: City Council Member, The City of Santa Clarita; April 2002 to April 2006; evidence roster
- term #23403: TimBen Boydston: City Council Member, The City of Santa Clarita; 2006 to April 2008; evidence roster

#### City of Santa Clarita biographies (the City's own account of a member): 18

- term #29078: Bill Miranda: Mayor, The City of Santa Clarita; 2021; evidence certified
- term #29076: Cameron Smyth: Mayor, The City of Santa Clarita; 2024; evidence certified
- term #29074: Cameron Smyth: Mayor, The City of Santa Clarita; 2020; evidence certified
- term #29072: Cameron Smyth: Mayor, The City of Santa Clarita; 2017; evidence certified
- term #29070: Cameron Smyth: Mayor, The City of Santa Clarita; 2005; evidence certified
- term #29068: Cameron Smyth: Mayor, The City of Santa Clarita; 2003; evidence certified
- term #29082: Jason Gibbs: Mayor, The City of Santa Clarita; 2023; evidence certified
- term #29056: Laurene Weste: Mayor, The City of Santa Clarita; 2022; evidence certified
- term #29054: Laurene Weste: Mayor, The City of Santa Clarita; 2018; evidence certified
- term #29052: Laurene Weste: Mayor, The City of Santa Clarita; 2014; evidence certified
- term #29050: Laurene Weste: Mayor, The City of Santa Clarita; 2010; evidence certified
- term #29048: Laurene Weste: Mayor, The City of Santa Clarita; 2006; evidence certified
- term #29046: Laurene Weste: Mayor, The City of Santa Clarita; 2001; evidence certified
- term #29066: Marsha McLean: Mayor, The City of Santa Clarita; 2019; evidence certified
- term #29064: Marsha McLean: Mayor, The City of Santa Clarita; 2015; evidence certified
- term #29062: Marsha McLean: Mayor, The City of Santa Clarita; 2011; evidence certified
- term #29060: Marsha McLean: Mayor, The City of Santa Clarita; 2007; evidence certified
- term #29084: Patsy Ayala: Mayor Pro Tem, The City of Santa Clarita; ; evidence certified

#### santaclarita.gov/commission-information/arts-commission: 7

- affiliation #29116: Jeri Seratti, Commissioner, Arts Commission; 2028; evidence (none)
- affiliation #29218: Michael Millar, Founding Chair, Arts Commission; 2009 to 2011; evidence certified
- affiliation #29216: Michael Millar, Commissioner, Arts Commission; December 2026; evidence (none)
- affiliation #29098: Patti Rasmussen, Board member, Educational Outreach Chair, Santa Clarita Valley Historical Society; ; evidence (none)
- affiliation #29096: Patti Rasmussen, Education Reporter, The Santa Clarita Valley Signal; ; evidence (none)
- affiliation #29094: Patti Rasmussen, Commissioner, Arts Commission; December 2026; evidence (none)
- affiliation #29102: Susan Shapiro, Chair, Arts Commission; December 2026; evidence (none)

#### archive record #21939, "General Municipal Elections: Historical Election Results, 1987 to 2012 (documents)": 6

- candidacy #21984: Howard P. “Buck” McKeon: City Council election, November 3, 1987; elected; evidence retrospective
- candidacy #21998: Carl Boyer, III: City Council election, November 3, 1987; elected; evidence retrospective
- candidacy #21962: Dennis M. Koontz: City Council election, November 3, 1987; elected; evidence retrospective
- candidacy #21958: Janice Heidt: City Council election, November 3, 1987; elected; evidence retrospective
- candidacy #21968: Jo Anne Darcy: City Council election, November 3, 1987; elected; evidence retrospective
- candidacy #21974: Linda Hovis Storli: City Council election, November 3, 1987; not-elected; evidence retrospective

#### canyons.edu/administration/board/history.php: 5

- term #30351: Ernest Moreno: College Trustee, Santa Clarita Community College District; by 1990 to 1995 or 1996; evidence roster
- term #30347: John D. Hoskinson: College Trustee, Santa Clarita Community College District; by 1989 to by 1990; evidence roster
- term #30329: Mark A. Posner: College Trustee, Santa Clarita Community College District; ; evidence roster
- term #30323: Patricia R. Steele: College Trustee, Santa Clarita Community College District; ; evidence roster
- term #30381: Scott Thomas Wilk Sr.: College Trustee, Santa Clarita Community College District; August 23, 2006 to December 2007; evidence uncited

#### "city council fills 3 seats with 5 people": 4

- affiliation #29573: Leon Worden, Chairman, Newhall Redevelopment Committee; June 2002; evidence contemporary
- affiliation #29575: Susan Shapiro, Alternate member, Newhall Redevelopment Committee; June 2002; evidence contemporary
- term #29579: Cameron Smyth: Mayor Pro Tem, The City of Santa Clarita; 2002; evidence contemporary
- term #29577: Frank Ferry: Mayor, The City of Santa Clarita; 2002; evidence contemporary

#### California Secretary of State, Statement of Vote: 3

- term #28278: Christy Smith: State Assemblymember, California State Assembly; December 2018 to December 2020; evidence certified
- term #29384: George Whitesides: Congressman, United States House of Representatives; January 3, 2025; evidence certified
- term #29382: Mike Garcia: Congressman, United States House of Representatives; January 3, 2023 to January 3, 2025; evidence certified

#### santaclarita.gov/commission-information/planning-commission: 3

- affiliation #29111: Lisa Eichman, Commissioner, Planning Commission; December 2026; evidence (none)
- affiliation #29206: Nathan Keith, Chairperson, Planning Commission; December 2028; evidence (none)
- affiliation #29106: Tim Burkhart, Commissioner, Planning Commission; December 2028; evidence (none)

#### canyons.edu/about/history.php: 3

- candidacy #29908: Edward Muhl: Santa Clarita Community College District board election, April 1971; elected; evidence contemporary
- candidacy #29904: Peter Huntsinger: Santa Clarita Community College District board election, April 1971; elected; evidence contemporary
- candidacy #29906: William Bonelli: Santa Clarita Community College District board election, April 1971; elected; evidence contemporary

#### "vote for": 3

- candidacy #26655: BILL COOPER: Santa Clarita Valley Water board election, Division 1, November 8, 2022; elected; evidence roster
- candidacy #26613: E G GLADBACH: Castaic Lake Water Agency board election, Division 2, November 8, 2016; elected; evidence derived
- candidacy #26607: RJ KELLY: Castaic Lake Water Agency board election, Division 1, November 8, 2016; elected; evidence derived

#### hartdistrict.org/apps/pages/governing-board-members: 2

- affiliation #29038: Bob Jensen, Board member, Santa Clarita Valley Chamber of Commerce; ; evidence (none)
- term #28938: Cherise Moore: School Board Member, William S. Hart Union High School District; 2017 to December 2022; evidence certified

#### SB 634 (2017): 2

- term #28419: Bill Cooper: Water Board Director, Santa Clarita Valley Water; January 1, 2018 to January 2023; evidence derived
- term #28445: RJ Kelly: Water Board Director, Santa Clarita Valley Water; January 1, 2018 to January 2023; evidence derived

#### City Council resolutions declaring results: 2

- term #22418: Bob Kellar: City Council Member, The City of Santa Clarita; April 2012 to December 2016; evidence certified
- term #22420: TimBen Boydston: City Council Member, The City of Santa Clarita; April 2012 to December 2016; evidence certified

#### canyons.edu/community/womensconference/biopatsyayala.php: 2

- affiliation #29390: Patsy Ayala, Senior Field Representative to Assemblywoman Suzette Martinez Valladares, California State Assembly; ; evidence retrospective
- affiliation #29388: Patsy Ayala, Staff of Senator Scott Wilk, California State Senate; ; evidence retrospective

#### santaclarita.gov/commission-information/parks-recreation-and-community-services: 2

- affiliation #29212: Di Thompson, Board of Directors; 2025 Chair Elect, Santa Clarita Valley Chamber of Commerce; ; evidence (none)
- affiliation #29210: Di Thompson, Chair, Parks, Recreation and Community Services Commission; December 2026; evidence (none)

#### scvnews.com/candidates-file-for-clwa-governing-board-election: 1

- term #30609: Gary Martin: Water Board Director, Castaic Lake Water Agency; December 2014 to December 2017; evidence derived

#### yourscvwater.com/sites/default/files/scvwa/approved-resolutions/scv-water-approved-resolution-122022-resolution-scv-321.pdf: 1

- term #29661: Lynne Plambeck: Water Board Director, Newhall County Water District; 2015 to December 2017; evidence derived

#### scvnews.com/4-more-years-for-plambeck-mortensen-on-water-board: 1

- term #29659: Lynne Plambeck: Water Board Director, Newhall County Water District; 2011 to 2015; evidence contemporary

#### scvhistory.com/scvhistory/sg062505.htm: 1

- term #29190: Gary Murr: School Board Member, Saugus Union School District; December 2001 to June 2005; evidence retrospective

#### scvhistory.com/scvhistory/files/boyer2015ch14/files/basic-html/page5.html: 1

- term #28255: Gloria Mercado-Fortine: School Board Member, William S. Hart Union High School District; December 1997 to December 2001; evidence retrospective

#### scvnews.com/newhall-school-board-selects-officers-for-2014: 1

- term #28249: Michael Shapiro: School Board Member, Newhall School District; December 2003 to December 2015; evidence retrospective

#### yourscvwater.com/governance/board-directors: 1

- term #27422: Bill Cooper: Water Board Director, Santa Clarita Valley Water; January 2023 to January 2027; evidence derived

#### Biographical Directory of the U.S. Congress: 1

- term #26980: Buck McKeon: Congressman, United States House of Representatives; January 3, 1993 to January 3, 2003; evidence certified

#### scvnews.com/ayala-joins-wilk-staff-hough-now-district-director: 1

- affiliation #29386: Patsy Ayala, Field Representative to Assemblyman Scott Wilk, California State Assembly; 2014; evidence contemporary

#### yourscvwater.com/who-we-are/executive-management: 1

- affiliation #29036: Stephen Cole, General Manager, Santa Clarita Valley Water; ; evidence (none)

#### castaicusd.com/apps/pages/index.jsp?urec_id=799493&type=d: 1

- affiliation #29034: Bob Brauneisen, Superintendent, Castaic Union School District; ; evidence (none)

#### sssd.k12.ca.us/superintendent-message: 1

- affiliation #29032: Catherine Kawaguchi, Superintendent, Sulphur Springs Union School District; ; evidence (none)

#### saugususd.org/our-administration: 1

- affiliation #29030: Robert Hernandez, Superintendent, Saugus Union School District; ; evidence (none)

#### newhallschooldistrict.com/office-of-the-superintendent: 1

- affiliation #29028: Leticia Hernandez, Superintendent, Newhall School District; ; evidence (none)

#### hartdistrict.org/apps/pages/superintendent: 1

- affiliation #28930: Michael Vierra, Superintendent, William S. Hart Union High School District; ; evidence (none)

#### "a legacy of leadership": 1

- education #26973: Cameron Smyth at Hart High School; ; evidence retrospective

#### scvhistory.ddev.site/war-memorial/terror-stephencolley: 1

- education #21801: Stephen Edward Colley at Valencia High School; ; evidence retrospective

#### scvhistory.ddev.site/war-memorial/terror-rudyacosta: 1

- education #21799: Rudy Alexander Acosta at Santa Clarita Christian School; ; evidence retrospective

#### scvhistory.ddev.site/war-memorial/terror-richardslocum: 1

- education #21797: Richard Patrick Slocum at Saugus High School; ; evidence retrospective

#### scvhistory.ddev.site/war-memorial/terror-jakesuter: 1

- education #21795: Jake William Suter at West Ranch High School; ; evidence retrospective

#### scvhistory.ddev.site/war-memorial/terror-iangelig: 1

- education #21793: Ian Timothy D. Gelig at William S. Hart High School; ; evidence retrospective

#### scvhistory.ddev.site/war-memorial/terror-colelarsen: 1

- education #21791: Cole William Larsen at Canyon High School; ; evidence retrospective

#### scvhistory.ddev.site/war-memorial/korea-robertwhisler: 1

- education #21789: Robert L. Whisler at William S. Hart High School; ; evidence retrospective

#### scvhistory.ddev.site/war-memorial/korea-donaldmorissett: 1

- education #21787: Donald E. Morissett at William S. Hart High School; ; evidence retrospective

#### scvhistory.ddev.site/war-memorial/korea-albertthomas: 1

- education #21785: Albert Edward Thomas at William S. Hart High School; ; evidence retrospective

