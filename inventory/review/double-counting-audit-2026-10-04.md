# Perkins and Reynolds: double-counting and reliability audit, 4 October 2026

Claude, read-only. No database writes, no edits to existing files. Prompted by the del Valle Family record (#915), which stated Reynolds's partition acreages as fact on 3 October.

## Scope and method

- **Records:** 111 editorial bodies: the 70 persons whose bodyAuthorship is editorial-2026, and the 41 groups, places, organizations and events whose footnotes are editorial-2026 (the build_*, widen_*, rewrite_* and create_* scripts of this week). Only persons have a bodyAuthorship field. 39 of the 111 cite Reynolds or Perkins.
- **Citations:** each footnote was mapped to its source. Reynolds means the History of the Santa Clarita Valley chapters (#817-#871, #2069-#2181). Perkins means Story of Our Valley (#1418-#1432), his articles (#869, #1434-#1450) and Rancho San Francisco (#27374). Each body was split at its footnote markers, and every span carrying a Reynolds or Perkins note was read against the rule in PROFILES.md.
- **Error strings (D):** all 111 bodies and their editorNotes were swept for the six inherited errors (Perkins dossier 2.4a), the 25 wrong or partly wrong Perkins claims (P-ledger), and the Reynolds ledger's contradicted items.
- **Checking:** each flagged claim was checked against the Reynolds web text (inventory/legacy/reynolds-full.json), Perkins (inventory/legacy/perkins.json, perkins-rsf-1957.txt), and the Worden and Pollack pages cited beside them (inventory/legacy/fetched).

## Counts

27 findings on 22 records (some findings cover two or three records). The class marked * is each finding's main one; some findings carry more than one.

| Class | Findings (main) | Findings (any) |
|---|---|---|
| A: only Reynolds or Perkins, stated as fact | 6 | 11 |
| B: a figure, age, sum, acreage, title, "first" or vivid story stated without attribution | 11 | 16 |
| C: one account counted as two | 4 | 8 |
| D: a known Perkins or Reynolds error in a public body or note | 6 | 6 |

**High:** del Valle Family #915, Rancho San Francisco #16446, Beale's Cut #932, Beale #327, Fages #287, Tataviam #913.

## A pattern the rule did not anticipate

**Leon Worden's columns repeat Reynolds.** Some of Leon's columns carry Reynolds's text, so a Worden note can be Reynolds at second hand:
- LW2052 has the partition acreages word for word. It is the only note on them in #915, and one of the two in #16446.
- LW2225 has Frémont's "100-man buckskin battalion ... probably stopped overnight" (#307).
- LW2504 quotes Reynolds on Fages.

The build scripts check that each sentence's words appear in the cited passage (`must`), but not where that passage got them. So a Reynolds figure passes the check once it has been laundered through a Worden page. The same goes for chapter 59 (Birchard's rewrite) and chapters 69 and 70 (reworked), which are cited as "Jerry Reynolds".

## Findings

### 1. #915 del Valle Family (groups): class D/A, high

- **Sentence:** A judge divided the estate: the westernmost section, 13,599 acres, to his son Ygnacio, 21,307 acres to his widow, Jacoba, and 4,684 acres to each of her six children.
- **Notes:** [2] Leon Worden, LW2052. LW2052 repeats Reynolds ch. 15 word for word on this point, so the only source is Reynolds.
- **Ledger:** Reynolds D1 (contradicted): the figures sum to 62,010 acres against a 48,611.88-acre patent. Perkins (P46, confirmed, from the probate and deeds): undivided shares until the 1870 partition, Camulos 1,340 acres.
- **Rule requires:** Drop the figures. Give Perkins's document-based sequence; if the figures are kept at all, give them as Reynolds's and say they cannot be right.
- **Draft:** The heirs held the rancho in undivided shares until it was partitioned in 1870, when Camulos, about 1,340 acres, was set apart for Ygnacio.[3]
- **Notes change:** Cite note 3 (Perkins 1957, his note 85) for the new sentence; keep note 2 for the 1861 Wolfskill settlement and the move to Camulos.

### 2. #16446 Rancho San Francisco (places): class D/C, high

- **Sentence:** After Antonio's death the estate was fought over. A judge gave his son Ygnacio the westernmost section, 13,599 acres, which included Camulos; 21,307 acres to Antonio's widow, Jacoba; and 4,684 acres to each of her six children.
- **Notes:** [2] LW2052 (repeats Reynolds ch. 15), [1] Perkins 1957. Perkins does not give these figures; he says the opposite (no partition before title was confirmed; partition 1870).
- **Ledger:** Reynolds D1; Perkins P46.
- **Rule requires:** Drop the figures. Two notes make it look corroborated, but one is Reynolds at second hand and the other contradicts the sentence.
- **Draft:** After Antonio's death the estate was fought over. His heirs held the rancho in undivided shares until it was partitioned in 1870, when Camulos, about 1,340 acres, was set apart for Ygnacio.[1]
- **Notes change:** Note 2 stays on the Wolfskill sentence that follows.

### 3. #16446 Rancho San Francisco (places): class B, medium

- **Sentence:** Rancho San Francisco was the Mexican land grant that held the western Santa Clarita Valley: 48,829 acres, with part of Ventura County as far as Piru.
- **Notes:** [1] Perkins 1957, [2] LW2052. 48,829 is in LW2052 and Reynolds; Perkins gives eleven leagues, not this figure.
- **Ledger:** Reynolds number pattern; Antonio del Valle #291 already handles it correctly (nominal size vs the patent).
- **Rule requires:** A Reynolds acreage needs a third source. Use the patent figure, as #291 does.
- **Draft:** Rancho San Francisco was the Mexican land grant that held the western Santa Clarita Valley: eleven square leagues, patented in 1875 at 48,611.88 acres, with part of Ventura County as far as Piru.
- **Notes change:** Add the patent (Land Case 303 SD) as #291 note 4 cites it.

### 4. #932 Beale's Cut Stagecoach Pass (places): class D/A/C, high

- **Sentence:** Edward F. Beale took over Pico's franchise from the Los Angeles supervisors, with five thousand dollars to do the work, and called out troops from Fort Tejon to dig a ninety-foot slash through the mountain with picks and shovels.
- **Notes:** [1] Reynolds ch. 28 only.
- **Ledger:** B6 ($5,000; the Los Angeles Star of 4 April 1863, transcribed by Perkins (P51), estimated $16,000 to $18,000); B5/P27 (the ninety-foot slash and the Fort Tejon troops; depth never surveyed; inherited from Perkins); P22 (the franchise was the 1861 act to Brinley, Pico and Vineyard).
- **Rule requires:** Drop the sum, the depth and the troops; the Star figure comes in through Perkins's transcription, which the rule accepts.
- **Draft:** In 1863 the work passed to Edward F. Beale. The Los Angeles Star of 4 April 1863 put its cost at $16,000 to $18,000. The cut's depth was never surveyed: the ninety feet of later accounts, Perkins's and Reynolds's alike, is a single tradition.
- **Notes change:** Cite Perkins, "4. Early Transportation" (#1426), for the Star transcription.

### 5. #932 Beale's Cut Stagecoach Pass (places): class A/B, medium

- **Sentence:** Andrés Pico began improving the road over the pass in the winter of 1862-63, but floods washed the work out. ... On September 19, 1863, he lent two thousand dollars to A.A. Hudson and Oliver P. Robbins, who built a toll house below the cut, and the pass was a toll road for twenty-one years, until it reverted to the county.
- **Notes:** [1] Reynolds ch. 28 only.
- **Ledger:** Minor date, sum and name from Reynolds alone; B13 (Robbins's initials conflict, A.P. or O.P.). Andrés Pico #317 has the Supervisors giving Pico the grade contract in 1861 (Worden, citing the Star and Board minutes).
- **Rule requires:** Give as his.
- **Draft:** Jerry Reynolds writes that Andrés Pico's work on the road was washed out by floods in the winter of 1862-63, and that Beale lent A.A. Hudson and O.P. Robbins the money to build a toll house below the cut. The pass was a toll road until the franchise ran out, about 1884.
- **Notes change:** Keep note 1; the 1884 date is Ripley's (twenty years from March 1864) as the Beale dossier gives it.

### 6. #327 Edward Fitzgerald Beale (persons): class D/A, high

- **Sentence:** He took over Andrés Pico's franchise for the road over the pass and got five thousand dollars from the Los Angeles supervisors to do the work.
- **Notes:** [2] Reynolds ch. 28 only.
- **Ledger:** B6 (contradicted by the Star via Perkins P51); P22.
- **Rule requires:** Drop the sum; replace with the documented estimate.
- **Draft:** In 1863 he took on the cutting of the road through the Newhall Pass; the Los Angeles Star put the cost of the work at $16,000 to $18,000.
- **Notes change:** Cite Perkins, "4. Early Transportation" (#1426), quoting the Star of 4 April 1863.

### 7. #327 Edward Fitzgerald Beale (persons): class D, low

- **Sentence:** He was superintendent of Indian affairs for California and Nevada from 1853, ...
- **Notes:** [1] Beale AFB biography, [2] Reynolds ch. 28.
- **Ledger:** P17 (Perkins, inherited by Reynolds B3): the appointment of 3 March 1853 was for California; Nevada was not a separate jurisdiction until 1861. The AFB biography repeats "and Nevada".
- **Rule requires:** Correct the label; the AFB biography is not a third source on this point.
- **Draft:** He was superintendent of Indian affairs for California from 1853, ...
- **Notes change:** None.

### 8. #327 Edward Fitzgerald Beale (persons): class B, low

- **Sentence:** He bought Rancho La Liebre on August 8, 1855, ...
- **Notes:** [2] Reynolds, [1] AFB.
- **Ledger:** B+5: the year is confirmed, the day (August 8) only Reynolds's.
- **Rule requires:** Give the day as his.
- **Draft:** He bought Rancho La Liebre in 1855, on August 8 by Jerry Reynolds's date, ...
- **Notes change:** None. (The 297,000 acres in the same sentence is already given as Reynolds's beside the AFB's 270,000, which the rule allows; the ledger (B7) calls it contradicted, so it could also be dropped.)

### 9. #287 Pedro Fages (persons): class A/B, high

- **Sentence:** In the spring of 1772 six of his soldiers deserted, and Fages went after them by way of the Mojave River and the Antelope Valley ... After two days' rest the party climbed the canyon ... He never found the deserters.
- **Notes:** [1] Reynolds ch. 9 only, for the whole paragraph (headcount, route, first camp, the chief's misdirection, the two days' rest, Laguna as Lake Elizabeth).
- **Ledger:** Reynolds's vivid-story and minor-detail pattern; the 1772 passage itself is supported by the 1938 landmark (LW2504).
- **Rule requires:** Give as his, or cut to what the landmark supports.
- **Draft:** In 1772 he came back after deserters. Jerry Reynolds tells the pursuit in detail: six soldiers gone, a route by the Mojave River and the Antelope Valley, a first camp probably near Agua Dulce Springs, and a climb up Castaic Canyon on the word of the Tataviam chief. He never found them.
- **Notes change:** Keep note 1; add note 2 (LW2504, the landmark) for the 1772 passage.

### 10. #287 Pedro Fages (persons): class B, medium

- **Sentence:** Born about 1734, a Catalonian, he had led the twenty-five Catalonian soldiers on the 1769 march; he later fought Apaches on the Sonoran frontier, served again as governor until 1791, and died in 1796.
- **Notes:** [1] Reynolds ch. 9 only.
- **Ledger:** Reynolds gives "in his early thirties" in 1769 and no death year; he says accounts differ on how Fages ended. The 1796 is not in the cited source. Standard references give 1794 (NEEDS_VERIFICATION).
- **Rule requires:** The death year has no source here; the headcount is his.
- **Draft:** A Catalonian, he had led the Catalonian Volunteers on the 1769 march, twenty-five of them by Jerry Reynolds's count; he later fought Apaches on the Sonoran frontier and served again as governor until 1791.
- **Notes change:** Drop the death year until a source is cited.

### 11. #913 Tataviam (groups): class A/B/C, high (CARE)

- **Sentence:** Their neighbors the Kitanemuks called them Tataviam, "dwellers on sunny slopes"; peoples they had displaced called them Allikliks, "grunters." ... They lived in some twenty-five villages: Kamulus ..., Tochonanga on Newhall Creek, and Chaguayabit, a town of about five hundred, at Castaic Junction. They wove fine baskets but made no pottery, ...
- **Notes:** [1] Reynolds ch. 4; [1][2] Reynolds ch. 4 and ch. 5 (one author cited twice).
- **Ledger:** Reynolds B14/R3 pattern (glosses unverified, CARE); Perkins P8 (Alliklik superseded). Ch. 4's own editor's note says Tochonanga's site "remains a mystery".
- **Rule requires:** Indigenous ethnography from Reynolds is unverified and TATAVIAM_AUDIT governs. Attribute everything, and Nathan decides whether the glosses stay before consultation.
- **Draft:** Jerry Reynolds wrote that the Kitanemuks called them Tataviam, which he glosses "dwellers on sunny slopes," and that he counted some twenty-five villages, among them Kamulus at what is now Camulos, Piru-U-Bit on Piru Creek and Chaguayabit at Castaic Junction, which he puts at about five hundred people. Tochonanga was in the Newhall area; its exact site is not known. These names and meanings await consultation with the Fernandeño Tataviam Band of Mission Indians.
- **Notes change:** Cite ch. 4 once; drop the Alliklik gloss pending Nathan.

### 12. #913 Tataviam (groups): class D, high (CARE)

- **Sentence:** He gives 1916 for the death of the last full-blooded Tataviam; Reynolds gives 1921, at the Camulos Ranch.
- **Notes:** [4] Worden 1996, [2] Reynolds ch. 5.
- **Ledger:** Reynolds R1 / Perkins P1: Reynolds wrote 1916 and Perkins 1934; the 1921 in the web text is the editor's correction from the Ventura County death certificate (30 June 1921). The sentence credits Reynolds with the correction and leaves open a point a document settles.
- **Rule requires:** Give the document's date; cite the 1998 edition, not Reynolds, for the correction.
- **Draft:** Leon Worden gave 1916 for the death of the last full-blooded Tataviam, as Jerry Reynolds first had; the 1998 edition of Reynolds's history corrects it to 1921, from the Ventura County death certificate.
- **Notes change:** Note 2 should name the 1998 edition's note 1 on ch. 5.

### 13. #938 Portolá Expedition (groups): class C/B, medium

- **Sentence:** Governor Gaspar de Portolá had set out from San Diego with sixty-four men to find the Bay of Monterey, leaving Father Junípero Serra behind. His soldiers included the twenty-five Catalonian Volunteers under Lieutenant Pedro Fages.
- **Notes:** [1] Reynolds ch. 7, [4] Reynolds ch. 9: one author cited twice.
- **Ledger:** Reynolds figure pattern. The 64 has a third source already in the record: note 2, LW2441a ("the 64 members of the Portolà expedition"). The 25 does not.
- **Rule requires:** Cite the third source for 64; give 25 as his.
- **Draft:** Governor Gaspar de Portolá had set out from San Diego with sixty-four men to find the Bay of Monterey, leaving Father Junípero Serra behind. His soldiers included the Catalonian Volunteers under Lieutenant Pedro Fages, twenty-five of them by Jerry Reynolds's count.
- **Notes change:** Notes [1][2] on the first sentence, [4] on the second.

### 14. #16140 Jo Anne Darcy (persons): class A, low

- **Sentence:** She joined the City Formation Committee soon after it was organized.
- **Notes:** [6] Reynolds ch. 70 only.
- **Ledger:** PROFILES: ch. 69-70 were reworked by another contributor, ch. 70 "adapted from material originally developed by Jerry Reynolds and the SCV Chamber of Commerce".
- **Rule requires:** Attribute, to the 1998 edition.
- **Draft:** The 1998 History of the Santa Clarita Valley says she joined the City Formation Committee soon after it was organized.
- **Notes change:** Note 6 to read "History of the Santa Clarita Valley, 1998 edition, chapter 70".

### 15. #15874 Jill Klajic (also #15737 Jan Heidt, #18616 Dan Hon) (persons): class A, low

- **Sentence:** Jerry Reynolds writes that she joined the City Formation Committee soon after it was organized and was paid staff to the campaign. (Heidt: "Jerry Reynolds counts her among the three cityhood leaders ..."; Hon: "Jerry Reynolds writes that he co-chaired ...")
- **Notes:** Reynolds ch. 69 / ch. 70.
- **Ledger:** Same as above: these chapters are not Reynolds's alone.
- **Rule requires:** Attributed, but to the wrong author; name the edition.
- **Draft:** The 1998 History of the Santa Clarita Valley says she joined the City Formation Committee soon after it was organized and was paid staff to the campaign.
- **Notes change:** Same wording change in Heidt and Hon.

### 16. #376 The Santa Clarita Valley Signal (organizations): class A, medium

- **Sentence:** (editor's note, public) The Signal was founded on February 7, 1919, as The Newhall Signal, not in 2019 as this record gave it (note 1).
- **Notes:** [1] Reynolds ch. 61 only.
- **Ledger:** Major-event date from Reynolds alone: accepted provisionally, attributed.
- **Rule requires:** Attribute.
- **Draft:** The Signal was founded as The Newhall Signal in 1919, on February 7 by Jerry Reynolds's date, not in 2019 as this record gave it (note 1).
- **Notes change:** None.

### 17. #20226 William Wirt Jenkins (persons): class B/A, medium

- **Sentence:** William Wirt Jenkins was a California Ranger and later a county undersheriff, and from 1878 ranched on Castaic Creek.
- **Notes:** [1] Reynolds ch. 55 and ch. 41 only.
- **Ledger:** A title (undersheriff) and a minor date from Reynolds alone. "Ranger" has Kreider 1952 ("Ranger Bill Jenkins"), already note 2. The occupation field also reads "undersheriff".
- **Rule requires:** Give the title and the year as his.
- **Draft:** William Wirt Jenkins was a California Ranger. Jerry Reynolds adds that he was later a county undersheriff and ranched on Castaic Creek from 1878.
- **Notes change:** [2][1]; the occupation field should drop "undersheriff" or carry it as Reynolds's.

### 18. #20224 Sanford Lyon (persons): class B, low

- **Sentence:** He and his twin brother Cyrus were born in Machias, Maine, on 20 November 1831.
- **Notes:** [2] Pollack 2012 (1831 only) and Reynolds (the day).
- **Ledger:** Minor date from Reynolds alone.
- **Rule requires:** Give the day as his.
- **Draft:** He and his twin brother Cyrus were born in Machias, Maine, in 1831, on 20 November by Jerry Reynolds's date.
- **Notes change:** None.

### 19. #311 Thomas O. Larkin (persons): class B/A, medium

- **Sentence:** Thomas O. Larkin, the American consul at Monterey, wrote to the New York Sun that a common laborer could pick up $2 a day at the San Feliciano placers, in a canyon off Piru Creek.
- **Notes:** [1] Perkins, Story of Our Valley part 5, only.
- **Ledger:** Perkins paraphrases the letter in his unsourced 1954 series; he does not quote or cite it, so the document rule does not apply.
- **Rule requires:** Give as Perkins's.
- **Draft:** A.B. Perkins wrote that Thomas O. Larkin, the American consul at Monterey, told the New York Sun a common laborer could pick up $2 a day at the San Feliciano placers, in a canyon off Piru Creek.
- **Notes change:** None.

### 20. #948 Bennett-Arcan Party (groups): class B, low

- **Sentence:** Manly, twenty-nine, and Rogers, twenty-two, had set out on November 4 with a canteen of water and some jerky.
- **Notes:** [1] Reynolds ch. 19 only.
- **Ledger:** Ages and vivid detail from Reynolds alone. The rest of the body attributes correctly ("Reynolds writes", "Reynolds tells").
- **Rule requires:** Give as his, or check Manly's Death Valley in '49.
- **Draft:** Manly and Rogers had set out on November 4, by Jerry Reynolds's account with a canteen of water and some jerky; he gives their ages as twenty-nine and twenty-two.
- **Notes change:** None.

### 21. #315 Christopher Houston Carson (also #307 John C. Frémont) (persons): class B, low

- **Sentence:** ... the press called Frémont "the Pathfinder," though Carson found most of the paths.
- **Notes:** #315: [1] Pollack, [2] Reynolds ch. 18; Pollack does not say it. #307: [1] LW2225, [2] Reynolds.
- **Ledger:** Reynolds's own line (ch. 18), a superlative stated as fact.
- **Rule requires:** Drop, or give as his.
- **Draft:** Christopher "Kit" Carson, born in Kentucky in 1809, was a fur trapper and mountain man who guided John C. Frémont, "the Pathfinder," on his expeditions to the Far West.
- **Notes change:** For #307: "...an Army explorer the press called 'the Pathfinder', with Kit Carson as his guide, ..."

### 22. #307 John C. Frémont (persons): class C, medium

- **Sentence:** On January 9, 1847, Frémont and his hundred-man "buckskin battalion" reached Castaic Junction from the north and probably stopped overnight at the del Valle ranch house. The next night they camped at the Newhall Pass.
- **Notes:** [1] Leon Worden LW2225, [2] Reynolds ch. 18.
- **Ledger:** LW2225 tells the same story in the same words ("100-man 'buckskin battalion'", "probably stopped overnight"): one account, counted twice. The date of a major event may stand, attributed; the primary is Edwin Bryant's journal, which Reynolds quotes.
- **Rule requires:** Treat as one source; attribute, or cite Bryant.
- **Draft:** By the account Jerry Reynolds and Leon Worden both give, Frémont and his hundred-man "buckskin battalion" reached Castaic Junction from the north on January 9, 1847, and probably stopped overnight at the del Valle ranch house; the next night they camped at the Newhall Pass.
- **Notes change:** Add Bryant, What I Saw in California, when held.

### 23. #291 Antonio del Valle (persons): class C/B, low

- **Sentence:** (a) "... and his heirs held it in undivided shares until it was partitioned in 1870." [1][2]  (b) "His birth year is given as 1788: Reynolds has him forty-six in 1834 and fifty-three when he died." [2]
- **Notes:** (a) Perkins and Reynolds; Reynolds ch. 15 says the opposite (a judge's division). (b) Reynolds only.
- **Ledger:** (a) del Valle L1/D1; (b) Reynolds ages.
- **Rule requires:** (a) Cite Perkins only. (b) Say it is inferred from his ages.
- **Draft:** (b) Jerry Reynolds's ages for him, forty-six in 1834 and fifty-three at his death, would put his birth about 1788.
- **Notes change:** (a) Drop [2] from the first paragraph's partition clause.

### 24. #297 Juan Crespí (persons): class B/C, low

- **Sentence:** Father Juan Crespí's diary is the first written account of the Santa Clarita Valley and its people.
- **Notes:** [1] Reynolds, [2] Worden LW2441a, [3] Perkins; the next sentence then quotes Reynolds and Perkins as the support.
- **Ledger:** A "first" resting on Reynolds and Perkins, who count once.
- **Rule requires:** Soften or attribute.
- **Draft:** Father Juan Crespí's diary, with Miguel Costansó's, is among the earliest written accounts of the Santa Clarita Valley and its people.
- **Notes change:** None.

### 25. #18702 Tom Mix (also #15919 Harry Carey) (persons): class C, low

- **Sentence:** Tom Mix ... made Newhall his movie town from 1916 into the 1920s. He built one of his "Mixville" Western sets in downtown Newhall ...
- **Notes:** [1] Birchard, King Cowboy (1993), and [3] "Jerry Reynolds, 59. Mixville", which the 1998 edition says Birchard rewrote.
- **Ledger:** PROFILES: do not attribute ch. 59 to Reynolds. Here one author (Birchard) is cited twice, one of them under Reynolds's name. AL3022 is independent, so the claim stands.
- **Rule requires:** Fix the note label.
- **Draft:** (note 3) Robert S. Birchard, "59. Mixville," in Jerry Reynolds, History of the Santa Clarita Valley, 1998 edition, article #2143 in this archive.
- **Notes change:** Same label for Harry Carey note 3.

### 26. #323 Cave Johnson Couts (persons): class B, low

- **Sentence:** Jerry Reynolds quotes the letter in his history of this valley; the drive reached San Jose on July 12, ...
- **Notes:** [3] Reynolds ch. 21 only.
- **Ledger:** Minor date from Reynolds alone; the rest of the profile attributes correctly.
- **Rule requires:** Give as his.
- **Draft:** Jerry Reynolds quotes the letter in his history of this valley and has the drive reaching San Jose on July 12; "the Santa Clara" may as well be the valley around San Jose, where it ended.
- **Notes change:** None.

### 27. #16347 California State University, Northridge (organizations): class B, low (CARE)

- **Sentence:** Archaeologists from CSUN and UCLA recovered 70 items from Elderberry Canyon, in the Castaic Reservoir area, in 1970, ...
- **Notes:** [2] Reynolds, "Ethnography of Castaic" (his curator's list), [3] Worden 1995 for the box only.
- **Ledger:** A figure from Reynolds alone, though from his own curatorial record; the Ethnography page is flagged for tribal consultation (C16), and a named site is a location.
- **Rule requires:** Attribute; Nathan to decide whether the site name stays.
- **Draft:** Jerry Reynolds, who ran the Castaic Lake visitors center, recorded 70 items recovered by archaeologists from CSUN and UCLA in the Castaic Reservoir area in 1970, and much of the material from the valley's prehistoric sites went into the university's collections.
- **Notes change:** None.

## Already compliant (no action)

These follow the rule: Hart #16356 (birth year, the 254 acres, the cowboy-suit legend, Rags in "Left out"), Lang #18820 (the grizzly), Gelcich #16439 (niece, not daughter: P9 avoided), Pico #317 (the 1855 distilling story, hedged), Mentry #18648 (the "firsts" laid out by source), Antonio del Valle #291 (12 vs 21 June; diseño vs patent acreage), Couts #323 (the Ysidora story given as Reynolds's), the two Francisco López records, California Battalion #946, Lyon, Wiley and Jenkins #20228, and Perkins #333 (it states the one-source rule itself).

## Outside this audit's scope, noted

- **Unsourced WordPress bodies:** 7 person bodies are still wordpress-import-unsourced, and at least two retell Reynolds without attribution. William Lewis Manly #321 has the ages, the oranges and Sara Bennett's "Good-bye, Death Valley". Gaspar de Portolá #295 has the "cordial welcome" and the 500. They fall under this rule when they are rewritten.
- **Ygnacio del Valle #293:** the body is legacy-leon (LW2052). It carries the acreages and "mayor". It is Leon's prose and is not to be rewritten; it is live error CE18.
- **Fages death year:** #287 gives 1796, which is not in its cited source. Standard references give 1794 (NEEDS_VERIFICATION).

## For Nathan

1. **Tataviam #913 (CARE).** Should Reynolds's glosses ("dwellers on sunny slopes", "grunters"), village names and headcounts stay on a public page, attributed, before consultation? Or should they come out until consultation? The same question applies to the Elderberry Canyon site name on CSUN #16347.
2. **Worden pages that repeat Reynolds.** Should a Worden page count as independent of Reynolds only where it cites something else? That would extend the Perkins-Reynolds one-source rule to Leon's columns of 1995 to 2000.
3. **Chapters 59, 69 and 70.** Should they be cited as the 1998 edition, or by their reworking authors, instead of "Jerry Reynolds"? PROFILES.md already says so; six records do not follow it.
