# Four claims where the second source differs, 10 October 2026

Claude, for Nathan. Write-up only: no record was changed, nothing was committed, nothing was written to the database. Each disagreement is laid out the way Grok's dossiers lay out a conflict (each witness quoted, then what would settle it), with no resolution and no recommendation. The "Evidence favors" line the dossiers carry is left out on purpose.

## What was read

- **The brief's starting point**: `inventory/review/single-source-second-sources-2026-10-09.md`, claims 32, 38, 45 and 48. Its quotes were not copied: every quote below was read again from the source named.
- **The layout**: `inventory/review/edward-f-beale-sources.md` and `inventory/review/a-b-perkins-sources.md` (Grok's dossiers), section 3, "Conflicts", and section 4, "What would settle each conflict". No file in `inventory/` has "dossier" or "grok" in its name; these two call themselves dossiers in their first line.
- **The rules**: AGENTS.md; docs/PROFILES.md (the Reynolds rule, the Perkins rule, "Perkins and Reynolds telling the same story are one source", the Worden-column rule).
- **Craft, read only** (`storage/runtime/scratch/c10_dump.php`, `ddev craft exec`, no save): person #291 Antonio del Valle, #307 John C. Frémont, #287 Pedro Fages, every field with a value (body, footnotes, editorNotes, recordDates, authorBio and the date fields).
- **The Reggie mirror**, inside the web container, each page converted with `iconv -f latin1 -t utf-8` and its tags stripped (`storage/runtime/scratch/c10txt/x.sh`; the text copies are in that folder):
  - `scvhistory/perkins-rsf-1957.htm` (Perkins, "Rancho San Francisco," 1957), the whole text and his notes 26 to 31, 45 to 50, 84 and 85.
  - `scvhistory/delcastillo1980.htm` (Griswold del Castillo, *California History* 59, 1980), the text and notes 6 to 8.
  - `scvhistory/camulos-nrhp3.htm` (NRHP nomination, 1996, section III) and `camulos-nrhp4.htm` (its source list).
  - `scvhistory/belderrain1930.htm` (Francisca Lopez de Belderrain, 1930, with Leon Worden's introduction).
  - `scvhistory/signal/reynolds/part14.html`, `part15.html`, `part16.html` (Reynolds, 1998 edition); `part17.html` is not on the mirror.
  - `scvhistory/signal/reynolds/part18.html` and `scvhistory/lw2225.htm` (Frémont).
  - `scvhistory/signal/reynolds/part09.html` and `scvhistory/lw2504.htm` (Fages).
- **Archive.org full texts** (djvu text, read, nothing uploaded; generic User-Agent, no email address sent):
  - Edwin Bryant, *What I Saw in California* (1849), `whatisawincalifoin00brya`: 30 November 1846 (pp. 365-366), 5 to 14 January 1847 (pp. 386-393), and Kearny's report of 14 January 1847 printed in the appendix (p. 400).
  - Bancroft, *History of California*, vol. I (`historyofcalifor01banc`), chapter VIII, pp. 183-197.
  - Bancroft, *History of California*, vol. V (`worksofhuberthow0022unse`), pp. 360 (the battalion's strength) and 399-401 (the march).
- **Tried, not read**: Hoffman, *Reports of Land Cases* (1862), `reportsoflandcas01hoff` and the copy `reportslandcase00distgoog`: Archive.org answered HTTP 500 for both text files on 10 October. Hoffman's single claimant for the rancho, mentioned once below, is the 9 October report's reading and is marked so.
- **Could be read, not read**: the page images of the Bryant and Bancroft volumes (the OCR text was read; the quotes below were checked against the OCR only, so a misread letter is possible); the Rancho Camulos Museum marker (record #291, footnote 5). **Cannot be read from here**: Reginaldo del Valle's 1930 manuscript and John T. Stow's 1886 field notes (Del Valle Collection, Huntington); the California Title Guaranty Co. abstract of title that Perkins cites; the Los Angeles County probate file and the 1870 partition decree; Land Case 318 SD (Bancroft Library); Wallace Smith, *This Land Was Ours* (1977); Bolton's 1931 edition of Fages's diary (JSTOR); Palou's *Noticias* (not looked for on Archive.org in this pass).

---

## D1. Antonio del Valle's estate: held undivided until 1870, or divided soon after 1841 (claim 38)

**The claim as the record states it.** Person #291, field `body`, first paragraph: "He died on the rancho two years later without a will, and his heirs held it in undivided shares until it was partitioned in 1870.[1]" Footnote 1 is Perkins 1957. The same field, fifth paragraph: "The probate court distributed the rancho to them in undivided portions, and it was not partitioned until 1870 [...].[1]"

**Reading 1: undivided until a partition in 1870.**
- Perkins 1957 (`perkins-rsf-1957.htm`), on Camulos after Antonio's death: "Possibly, as the oldest son, already mature, it was understood that Camulos was to be his portion, even though the estate of Don Antonio was not as yet partitioned."
- Perkins, on the 1850s probate: "The probate petition was granted and the estate distributed by undivided portions of the rancho to the heirs. Ygnacio del Valle purchased his sister Magdalena's share for some $6,000.00.[49] The land could not be partitioned before title confirmation." His note 49: "Ibid.", that is, note 47, "Abstract of Title, California Title Guaranty Company."
- Perkins, on 1865: "Commencing in 1865, deeds to undivided portions of Rancho San Francisco appear in the records from the various heirs of Antonio del Valle in favor of Ygnacio del Valle [...]" (note 74, the abstract).
- Perkins, on 1870: "The following year, Rancho San Francisco was finally partitioned by one L.F. Cooper [...].[84]" and "The partition went into effect July 30, over the signature of Pablo de la Guerra, then district judge.[85]" Note 84: "Abstract of Title, California Title Guaranty Co., p. 57-58." Note 85: "Ibid."

**Reading 2: divided among the heirs soon after his death.**
- Griswold del Castillo 1980 (`delcastillo1980.htm`): "After Antonio's death in 1841, the government divided the rancho among the heirs. Reginaldo's father Ygnacio got an 1800-acre parcel and called it Rancho Camulos. In his history Reginaldo neglected to mention that Pedro Carrillo [cq] contested Rancho San Francisco's partition in 1841. Carrillo filed an application for a portion of the grant with governor Alvarado. A year later, governor Micheltorena ruled in Carrillo's favor. [...] A final settlement favoring the Del Valles came in 1855 by the action of the California Board of Land Commissioners.[7]"
- NRHP nomination, 1996 (`camulos-nrhp3.htm`): "After Antonio's death in 1841, the land was divided among his wife and seven children. Ygnacio received the western portion of the ranch known as Camulos and built a corral and stocked it with cattle in 1842 [...]"
- Belderrain 1930 (`belderrain1930.htm`): "After Don Antonio's demise, the ranch was divided between his widow and children."
- Reynolds, 1998 edition, chapter 15, "Family Squabbles" (`part15.html`), not cited on the record: Ygnacio and his bride "presented the old don's missive to his son as evidence of his title to the western portion of Rancho San Francisco", and "The legal battle was joined and, at last, the juéz (judge) awarded 13,599 acres to Ygnacio del Valle, while Doña Jacoba got 21,307 acres and each of her six children received 4,684 acres." No year is given for the award. The "missive" is the deathbed offer in chapter 14 (`part14.html`): "the place extending from the portsuelo of [Rancho] San Francisco towards the west".

**On "without a will".** The record's footnote for this sentence is Perkins alone. The words "will" and "intestate" do not occur in Perkins's text as transcribed on the mirror in connection with Antonio (searched 10 October); he gives the probate cases (note 45) but does not say there was no will. Reynolds does, in chapter 14: "seventy-five square miles of land, and no will" and "Don Antonio had died intestate." The earlier report's "'Without a will' rests on Perkins's probate account" is an inference from the probate, not a quotation.

**What each source is, and how independent.**
- *Perkins 1957*: a local historian's study, Historical Society of Southern California Quarterly. On these points he cites a title abstract, a document. Under PROFILES.md the archive accepts Perkins where he cites a document. The abstract itself has not been seen.
- *Griswold del Castillo 1980*: an academic article. On the 1841 division his source is Reginaldo del Valle's 1930 manuscript ("In 1930, the eldest son of the family, Reginaldo del Valle, wrote a history of his family's homestead.[6]"; note 6, "MS., The Del Valle Collection, H.E. Huntington Library"). On Carrillo his note 7 is "Smith, p. 96" (Wallace Smith 1977). He does not cite Perkins. Reginaldo was born after these events, so the 1841 division is family memory written 89 years later.
- *NRHP 1996*: a consultants' nomination. Its source list (`camulos-nrhp4.htm`) includes "Del Castillo, Richard Griswold. 'The del Valle Family and the Fantasy Heritage.' California History 59 (1980)", Smith 1977, Bancroft, and "Kimbro, Edna. Early History of Camulos: Rancho San Francisco. Draft Ms. July 1995." Perkins is not on it. **So the NRHP is not independent of del Castillo**, and through him of Reginaldo's manuscript and Smith. The 9 October report counted the three as independent of one another; on this point they are at most two lines (the del Valle family's memory, and Belderrain).
- *Belderrain 1930*: a paper by a Lopez family descendant read at the 1930 gold-discovery celebration (Worden's introduction calls her "an indirect Lopez descendant who wasn't alive to witness"). No source is given for the division. Written the same year as Reginaldo's manuscript, by a relative of the widow's family.
- *Reynolds, chapter 15*: no source given. PROFILES.md: Reynolds's acreages are his weakest point, and Leon's LW2052 is Reynolds's chapter 15 again, not a second witness. Reynolds's three figures add to 63,010 acres (13,599 + 21,307 + 6 x 4,684), against the record's grant of 48,829 acres nominal and 48,611.88 at patent. (PROFILES.md says the partition figures "add up to 13,400 acres more than the rancho"; the arithmetic here gives 14,181 or 14,398, depending on which total is used. Noted, not changed.)

**Two readings can both be partly true.** A division of possession in the 1840s (Reading 2) and a legal partition decreed in 1870 (Reading 1) are different acts. The sources do not say which kind of "division" they mean, and none of the Reading 2 sources cites a decree or a deed.

**What would settle it, and where it is held.**
| Question | Source | Where |
|---|---|---|
| Was there a decree or award dividing the rancho in the 1840s? | Pedro Carrillo's application and Micheltorena's ruling; any juez's award (Reynolds's 13,599 / 21,307 / 4,684) | Spanish Archives (the Sacramento series Perkins used); Land Case 318 SD file, Bancroft Library |
| What did the heirs hold in 1852-57? | The confirmation claim (the 9 October report read Hoffman 1862 as listing one claimant, Jacoba Feliz, for 48,813.58 acres; not re-read today) and the case file | Land Case 318 SD, Bancroft Library |
| The probate and "no will" | Probate file of Antonio del Valle's estate, Los Angeles County, from 1850 (Perkins's note 45: "the first three cases filed in the new Probate Court") | Los Angeles County court records; where they are now held was not looked up |
| The 1870 partition | Decree of 30 July 1870, District Court, Judge Pablo de la Guerra; the abstract, pp. 57-58 | Los Angeles County records; the abstract, if it survives |
| What Reginaldo actually wrote | "History of Rancho Camulos," 1930, MS. | Del Valle Collection, Huntington |

---

## D2. Camulos: 1,340 acres set apart in 1870, or 1,800 acres inherited in 1842 (claim 45)

**The claim as the record states it.** Person #291, field `body`, fifth paragraph: "The probate court distributed the rancho to them in undivided portions, and it was not partitioned until 1870, when Camulos, 1,340 acres, was set apart for Ygnacio.[1]" Footnote 1 is Perkins 1957, "with his notes 26 (Antonio del Valle) and 85 (the partition)." The next sentence: "The Rancho Camulos Museum's marker at the house says that Ygnacio 'inherited the land' on his father's death; he inherited a share of it.[5][1]"

**Reading 1: 1,340 acres, set apart by the 1870 partition.**
- Perkins 1957: "Scott interests claimed an undivided 19/21 of the Rancho interests by purchase, having acquired all but those of Don Ygnacio del Valle [...]" and "The undivided 2/21, carried with it Camulos, legally, formally, and unencumbered. Its 1,340 acres were permanently separated and divorced from the troubled background of the grant." Then: "The partition went into effect July 30, over the signature of Pablo de la Guerra, then district judge.[85]" (note 85: the abstract of title, as note 84, pp. 57-58).

**Reading 2: 1,800 acres, Ygnacio's from 1842.**
- NRHP 1996 (`camulos-nrhp3.htm`), statement of significance: "Ygnacio del Valle, Antonio's son, inherited 1,800 acres of the grant in 1842 and stocked it with cattle."
- Griswold del Castillo 1980: "Reginaldo's father Ygnacio got an 1800-acre parcel and called it Rancho Camulos."

**The same sources say more than the 9 October report quoted.** Read in full, two of the Reading 2 sources also carry the 1,340 figure, at different dates:
- Griswold del Castillo, a page later: "In 1930, Reginaldo remembered that the original partition of Camulos had been for 1,800 acres. Actually, this had dwindled to 1,340 acres by 1886.[8]" Note 8: "John T. Stow, 'Field Notes of Rancho Camulos, May 1886,' MS., The Del Valle Collection, H.E. Huntington Library." So del Castillo himself gives 1,800 as Reginaldo's memory and 1,340 as the 1886 survey.
- The NRHP, in its history section: "The present 1,800 acre Camulos Ranch, established by Ygnacio del Valle in 1853, was carved out of the 48,612 acre Rancho San Francisco [...]" and "Bard purchased 42,216 acres of the Rancho San Francisco from the del Valle heirs and split off the 1,500 acre Rancho Camulos selling it back to Ygnacio del Valle. In 1868 the acreage was reduced to 1,340 acres and then to 1,290 acres when Ygnacio gave his first born son Juventino fifty acres." The nomination gives 1842, 1853 and 1865 for Camulos's start, and 1,800, 1,500, 1,340 and 1,290 for its size, without reconciling them. "1,800 acre" is also the size of the site nominated in 1996.

**What each source is, and how independent.** As in D1. Perkins cites the abstract of title for 1,340 and 1870. Del Castillo's 1,800 is Reginaldo's 1930 memory, and his 1,340 is Stow's 1886 field notes, a survey document. The NRHP draws on del Castillo and others (its source list), not on Perkins; its 1868 date for 1,340 has no note. The Rancho Camulos Museum marker (record footnote 5) was not re-read in this pass.

**What would settle it, and where it is held.**
| Question | Source | Where |
|---|---|---|
| Acreage and date of the 1870 partition | Partition decree of 30 July 1870; the abstract of title, pp. 57-58 | Los Angeles County records; the abstract, if it survives |
| Acreage in 1886 | Stow, "Field Notes of Rancho Camulos, May 1886" | Del Valle Collection, Huntington |
| Bard's 1865 purchase and any reconveyance of Camulos (the NRHP's 1,500 acres) | Deed, Ygnacio del Valle to Thomas R. Bard (Perkins's note 74) and any deed back | Los Angeles and Santa Barbara (later Ventura) County recorder |
| The 1868 figure | Whatever the NRHP drew it from (Kimbro's 1995 draft is the likeliest) | San Buenaventura Research Associates; the Rancho Camulos Museum |
| Reginaldo's 1,800 | His 1930 manuscript | Del Valle Collection, Huntington |

---

## D3. Frémont's battalion: a hundred men from the north, or some four hundred up the Santa Clara from the west (claim 32)

**The claim as the record states it.** Person #307, field `body`, second paragraph: "By the account Leon Worden and Jerry Reynolds both give, in the same words, Frémont and his hundred-man 'buckskin battalion' reached Castaic Junction from the north on January 9, 1847, and probably stopped overnight at the del Valle ranch house. The next night they camped at the Newhall Pass. They crossed the San Gabriels by the pass later named for him [...].[1][2]" Footnote 1 is Worden, LW2225; footnote 2 is Reynolds, chapter 18 (#857).

**Reading 1: a hundred men, arriving from the north.**
- Worden, LW2225 (`lw2225.htm`): "On Jan. 9, 1847, Frémont and his 100-man 'buckskin battalion' arrived at Castaic Junction from the north, on their way to meeting Pico, and probably stopped overnight at the Del Valle ranch home. The following night, the troops camped at the Newhall Pass [...]"
- Reynolds, 1998 edition, chapter 18 (`part18.html`): "Meanwhile, Frémont and his hundred-man 'buckskin battalion' marched southward along the coast, arriving at Castaic Junction on the evening of January 9, 1847." Reynolds then quotes Bryant for 9 and 10 January (below), and identifies the 9 January rancho: "This must have been the forty-five-year-old Don José Salazár."

**Reading 2: about four hundred men, coming up the Santa Clara valley from San Buenaventura.**
- Bryant, who marched with the battalion, on its size, 30 November 1846 (pp. 365-366): "The battalion of mounted riflemen under the command of Lieutenant-colonel Fremont, numbers, rank and file, including Indians and servants, 428."
- Kearny's report from Los Angeles, 14 January 1847, printed in Bryant's appendix (p. 400): "This morning, Lieutenant-colonel Fremont, of the regiment of mounted riflemen, reached here with 400 volunteers from the Sacramento; the enemy capitulated with him yesterday, near San Fernando [...]"
- Bancroft V, p. 360: "The whole force at that time, according to Bryant, who was an officer present at the time, was 428 men. [...] According to the official report, when the force was mustered out in April 1847 the total number of men enlisted had been 475 mounted riflemen and 41 artillerymen, in ten companies." His note adds Larkin's estimate of 9 November 1846: "He will have 400 to 450 men."
- Bryant on the route (pp. 387-390): 5 January, "We reached the mission of San Buenaventura"; 6 January, the mission is "on the edge of a plain or valley watered by the Rio Santa Clara", and "Proceeding up the valley about seven miles from the mission"; 7 January, "Continuing our march up the valley, we encamped near the rancho of Carrillo"; 8 January, "We encamped this afternoon in a grove of willows near a rancho"; 9 January, "We encamped this afternoon at a rancho, situated on the edge of a fertile and finely-watered plain of considerable extent [...] The rancho was owned and occupied by an aged Californian, of commanding and respectable appearance [...] whose sons were now all absent and engaged in the war"; 10 January, "Crossing the plain we encamped, about two o'clock, p. M., in the mouth of a canada, through which we ascend over a difficult pass in a range of elevated hills between us and the plain of San Fernando, or Couenga"; 11 January, "the main body, on foot, marching over a ridge of hills to the right of the road or trail; and the artillery, horses, and baggage [...] marching by the direct route."
- Bryant also writes, on 9 January: "As we march south there appears to be a larger supply of wheat, maize, beans, and barley". The battalion's march as a whole was southward from Monterey; its last leg before the valley ran east, up the Santa Clara from the coast.

**Points the two readings share.** The dates (9 January at a rancho on a plain, 10 January at the mouth of the pass, 11 January over it, 13 January the capitulation) agree in all of them. Reynolds's two dated quotations from Bryant match the 1849 text (Reynolds has "ascended" where the 1849 OCR reads "ascend").

**What each source is, and how independent.**
- *Bryant*: an eyewitness journal; the edition read is dated 1849 in Archive.org's catalogue. On 11 January he writes that he was with the advance party. His 428 is for 30 November 1846 at San Juan, five weeks before the valley; no figure of his own for January was found.
- *Kearny*: an official letter written the day Frémont arrived in Los Angeles, independent of Bryant, though Kearny did not march with the battalion and gives a round number.
- *Bancroft V*: on the route, not independent: "I follow Bryant's journal, additional details from other sources being either hopelessly contradictory or obviously erroneous" (p. 400). On the size he adds Larkin and the muster-out report, which are independent of Bryant.
- *Worden LW2225 and Reynolds chapter 18*: the record itself says they give the account "in the same words". Neither names a source for "hundred-man" or "from the north". Under PROFILES.md, a Worden column that repeats Reynolds and names no other source is the same account, so Reading 1 is one witness. Reynolds's chapter quotes Bryant for the dates but not for the size.
- "Probably stopped overnight at the del Valle ranch house" is an inference in both Worden and Reynolds; Bryant names no owner, and Reynolds himself names Salazar.

**What would settle it, and where it is held.**
| Question | Source | Where |
|---|---|---|
| Strength of the battalion in January 1847 | The official report Bancroft cites (31st Cong. 1st Sess., H. Ex. Doc. 24, p. 22h); Frémont's court-martial record; any company lists | Congressional Serial Set; NARA (whether either is online was not checked) |
| Where "hundred-man" came from | Reynolds's own source (his Signal column, or his 1992 book) | Reynolds's columns on the mirror; *Santa Clarita: Valley of the Golden Dream* (1992) |
| The route into the valley | Bryant (already read); Frémont's *Geographical Memoir* (1848), p. 42, which Bancroft cites | Archive.org or HathiTrust |

---

## D4. Fages's pursuit of the deserters: 1772 or 1773 (claim 48)

**The claim as the record states it.** Person #287, field `body`: "Pedro Fages first saw the Santa Clarita Valley as a lieutenant with the Portolá expedition in August 1769, and came back in 1772 as governor of Alta California at the head of his own party.[1][2]" Then: "In 1772 he came back after deserters.[2] Jerry Reynolds tells the pursuit in detail [...]" and "a state historical landmark on Lebec Road, at the top of the Grapevine, marks where he passed in 1772.[1][2]" Field `authorBio`: "in 1772 led an expedition through the Santa Clarita Valley in pursuit of deserters". Field `recordDates`, the 1770 entry's label: "In the spring of 1772, six of his soldados de cuero deserted with six Tataviam women".

**Reading 1: 1772.**
- Reynolds, 1998 edition, chapter 9 (`part09.html`): "He must have had mixed feelings that spring morning in 1772 when it was discovered that six of his soldados de cuero had deserted with an equal number of comely young Indian maidens. Fages was upset by the loss of his lancers, but he welcomed the excuse to go off exploring."
- Worden, LW2504 (`lw2504.htm`): "When he returned in 1772 in search of some deserters, he did so as governor of Alta California." The column then quotes Reynolds, and gives the landmark's text: "In 1772, Don Pedro Fages, leaving the first written record of explorations in the south San Joaquin Valley, passed this site traveling from San Diego to San Luis Obispo via Cajon Pass, Mojave Desert, Hughes Lake, Antelope Valley, Tejon Pass, Canada de Las Uvas (Grapevine Canyon), and Buena Vista Lake."

**Reading 2: 1773.**
- Bancroft I, p. 197 (running head "Visit to the Tulares"): "It is recorded that some time during 1773 Comandante Fages, while out in search of deserters, crossed the sierra eastward and saw an immense plain covered with tulares and a great lake, whence came as he supposed the great river that had prevented him from going to Point Reyes. This may be regarded as the discovery of the Tulare Valley. Thus close the somewhat meagre annals of an uneventful year [...]" The chapter's only note on its sources: "On the events of this chapter see Palou. Not., i. 180-245, 481-513; Id. Vida, 134-51."
- The context is odd and worth stating as found: the passage closes chapter VIII, whose heading is "Events of 1772" and which ends with "Palou's Journey to San Diego and Monterey in 1773". Bancroft gives no route through the Santa Clarita Valley, no number of deserters and no Indian women, and calls Fages "Comandante", not governor.

**What each source is, and how independent.**
- *Reynolds, chapter 9*: names no source. The route detail (Mojave River, Antelope Valley, Sierra Pelona, Agua Dulce, Castaic, Lake Elizabeth, Tejon Pass) is his. Under PROFILES.md a Reynolds date for a lesser event is to be spot-checked.
- *Worden LW2504*: for the narrative it quotes Reynolds, so it is the same account. **The landmark plaque it quotes is a separate witness**: the State's registered text for the landmark at the top of the Grapevine, which gives 1772 and a route (San Diego, Cajon Pass, the Mojave, Antelope Valley, Tejon Pass, Buena Vista Lake) close to Reynolds's but not through the Santa Clarita Valley. Its own source is not known. (LW2504's title says "Landmark No. 263"; its first line says "No. 283". Not checked further.)
- *Bancroft I*: an 1884 synthesis from the archives, here citing Palou's *Noticias*. Independent of Reynolds. "It is recorded" names no document.

**What would settle it, and where it is held.**
| Question | Source | Where |
|---|---|---|
| The year and route of the pursuit | Fages's own diary or report of the expedition, as edited by Herbert E. Bolton, *California Historical Society Quarterly* (1931), the edition the 9 October report names | JSTOR; where the original is held was not looked up |
| Bancroft's "1773" | Palou, *Noticias de la Nueva California*, i. 180-245, 481-513, the pages Bancroft cites | An edition or translation on Archive.org was not looked for |
| The landmark's 1772 | The registration file for the landmark | California Office of Historic Preservation |

---

## Read from a description

**Candidate seventh instance: the 1,800 acres of 1842 rest on a description of a manuscript, and the "three independent sources" are two.** The 9 October report (claims 38 and 45) and the record's reading of the del Valle estate both meet del Castillo's 1,800-acre parcel as if it were his finding. It is not. Read whole, del Castillo is describing what Reginaldo del Valle wrote in 1930 ("In 1930, Reginaldo remembered that the original partition of Camulos had been for 1,800 acres"), and he corrects it on the same page from a survey ("Actually, this had dwindled to 1,340 acres by 1886", citing Stow's field notes). The 9 October report quoted the first sentence and not the second. The NRHP's "inherited 1,800 acres of the grant in 1842" lists del Castillo among its sources, so it is not a separate witness either. The point "1,800 acres, 1842" was taken from a summary of a family manuscript that has not been seen, through a second document that repeats the summary. Not fixed: both readings stand as written above, and the manuscript is at the Huntington.

Two smaller findings of the same kind, also not fixed:
- **"Without a will" on #291** is footnoted to Perkins, whose transcribed text does not say it; it is Reynolds's phrase (chapter 14). The 9 October report described it as resting on Perkins's probate account, which is a reading of what Perkins implies, not what he says.
- **Reynolds's chapter 15** holds a third version of the division (a judge's award of 13,599, 21,307 and 4,684 acres) that neither the record nor the 9 October report mentions. PROFILES.md describes these figures as adding to "13,400 acres more than the rancho"; read today, they add to 63,010 acres, 14,181 more than the nominal grant.
