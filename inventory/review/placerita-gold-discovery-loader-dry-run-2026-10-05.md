# The Placerita Gold Discovery: the loader's dry run, 6 October 2026

Written by `scripts/import/create_placerita_gold_discovery_event_2026_10_05.php` (with `scripts/import/_event_from_draft_2026_10_06.php`) in a dry run. A dry run writes nothing to Craft. The event is read from `inventory/review/placerita-gold-discovery-draft-v2-2026-10-05.json` (SHA-256 `86bb0287e96619b8...`), the v2 draft whose quotations were rechecked on 6 October 2026. Nathan approved the draft for applying on 6 October 2026.

**Refusals:** none.

## The run

```
DRY RUN create_placerita_gold_discovery_event_2026_10_05.php
==============================================================================
EVENT: #31370 "The Placerita Gold Discovery" exists, not recreated
    content advisory: none (the draft has none)
    historicalEra: #159 Mexican Rancho Era (1821–1847)
    historicalPeriod: #172 Pre-1850
    recordTags: #18934 Mining & Gold
    neighborhood: #202 Placerita Canyon
    eventPersons: #18834 Francisco Lopez (notes 1, 2); #309 Abel Stearns (notes 2, 7); #293 Ygnacio del Valle (notes 6)
    eventPlaces: #16446 Rancho San Francisco (notes 1, 6); #18565 Placerita Canyon (notes 3, 12, 17, 18)
    eventOrganizations: none
    eventFallenOfficers: none
    eventArticles: #1424 3. The Placerita Gold Rush (note 1, 6, 17); #15294 California's REAL First Gold (note 1)
    articles held, no footnote names them: #853 Chapter 16. Golden Dreams (Reynolds, 1998 edition); #12154 The real story of California's first gold discovery (Leon Worden, 1996); #12490 New Study Will Nag SCV Historians (Leon Worden, 1996)
    sourceDocuments: #26983 Abel Stearns Tells of Lopez 1842 Gold Discovery; No Mention of Dream (note 2, note 3, note 7, editor note 2, editor note 4)
        the event already has [26983]; not changed
    cited records that are not documents (not in sourceDocuments): #1424 articles "3. The Placerita Gold Rush"; #15294 articles "California's REAL First Gold"; #309 persons "Abel Stearns"; #2963 photographs "New York Observer Report on Placerita Gold Discovery, 10-1-1842"; #30537 sourceFaults "deposited the 8th day of July, 1843 (the memorandum as Robinson copied it) → 8 July or 8 June 1843: not decided"; #4169 photographs "Tentative Program for 1930 Dedication"; #18834 persons "Francisco Lopez"; #28132 persons "Francisco "Chico" López"
    featuredImage: none
    photograph #2963 LW2181: photoEvents has it (named in note 4)
    photograph #4169 LW2575b: photoEvents has it (named in note 14)
    photograph #4171 LW2575c: HELD, footnote 18 does not name it
    photograph #2989 LW2217: HELD, no footnote cites it (the draft says so)
    photograph #4731 LW2952: HELD, no footnote cites it (the draft says so)
    photograph #3337 LW2352: HELD, no footnote cites it (the draft says so)
    photograph #3339 LW2353: HELD, no footnote cites it (the draft says so)
    photograph #3313 LW2338: HELD, no footnote cites it (the draft says so)
    other records: nothing written
    REFUSED: none
```

## Quotation check

127 quotations in the v2 draft (every quoted passage, in double or single quotation marks, in the fields (body, significance, footnotes, editor notes, recordDates labels, relation ties, photograph rows, image notes, the literature list) and the research leads; forNathan is not loaded and was not rechecked) were checked word for word: 118 PASS, 9 CORRECTED from v1, 0 FAIL. Ellipses: v2 has none. Checked against: mirror pages on /Volumes/Reggie/SCVHistory/scvhistory.com (HTML read as latin-1; PDFs by pdftotext), and Craft for record text, titles and the asset title, by read-only queries (storage/runtime/ev4/craft.json).

"Terminal punctuation only" means the quotation stops where the source's sentence goes on and closes with a period or comma of its own; no word is changed.

| # | Where | Quotation | Result | Page | Note |
| --- | --- | --- | --- | --- | --- |
| 1 | body | documented | PASS | /scvhistory/belderrain1930.htm |  |
| 2 | body | His Divine Majesty | PASS | /scvhistory/signal/coins/worden-coinage1005.htm |  |
| 3 | body | a placer of gold on the 9th day of March last, at the place of San Francisco, appertaining to the late Don Antonio del Valle, distant from h [cut here for the table] | PASS | /scvhistory/signal/perkins/part03.html |  |
| 4 | body | in the month of March, 1842, at a place called San Francisquito, | PASS | /scvhistory/lp_santacruzsentinel082785.htm |  |
| 5 | body | with his sheath knife dug up some wild onions, and in the dirt discovered a piece of gold. | PASS | /scvhistory/lp_santacruzsentinel082785.htm; document #26983 | the letter continues ", and searching further"; terminal punctuation only |
| 6 | body | They have at last discovered gold, not far from San Fernando, | PASS | /scvhistory/lw2181.htm |  |
| 7 | body | Gold to the amount of some thousands of dollars has already been collected. | PASS | /scvhistory/lw2181.htm |  |
| 8 | body | upwards of Fifty men to work washing the earth, | PASS | /scvhistory/pollack1115dream.htm |  |
| 9 | body | principally by Sonorensee (Sonorians), until the latter part of 1846. | PASS | /scvhistory/lp_santacruzsentinel082785.htm; document #26983 | the letter continues ", when most of the Sonorensee left"; terminal punctuation only |
| 10 | body | The strongest evidence seems to incline toward March 1842, | PASS | /scvhistory/pollack1115dream.htm |  |
| 11 | body | shows conclusively | PASS | /scvhistory/sw9501.htm |  |
| 12 | body | probably in 1842 (unless it was in 1841), and it was probably in Placerita Canyon (unless it was somewhere around Hasley Canyon). | PASS | /scvhistory/belderrain1930.htm |  |
| 13 | body | taking their siesta under the shade of the oak tree, | PASS | /scvhistory/pollack1115dream.htm |  |
| 14 | body | Encino del Ensueno Dorado, | PASS | /scvhistory/belderrain1930.htm |  |
| 15 | body | on the word of relatives of a Lopez descendant who pointed it out to them 70 years after the fact | PASS | /scvhistory/lw2575b.htm |  |
| 16 | body | from under that Tree of the Golden Dreams. | PASS | /scvhistory/pollack1115dream.htm | the speech continues ", and made"; terminal punctuation only |
| 17 | body | the gold was not discovered until the onions were being washed. | PASS | /scvhistory/latta1976franciscolopez.htm |  |
| 18 | body | Francisco Lopez / Here discovered the first gold in California, / March 9, 1842, | PASS | /scvhistory/signal/perkins/part03.html | the plaque's lines (<br> on the page) are marked with slashes |
| 19 | body | Francisco Lopez made California's first authenticated gold discovery on March 9, 1842. | PASS | /scvhistory/signal/coins/worden-coinage1005.htm |  |
| 20 | body | Dissecting the Dream: Fact, Fiction, and Placerita's Golden Oak | PASS | /scvhistory/pollack1115dream.htm |  |
| 21 | footnotes[0] | 3. The Placerita Gold Rush, | PASS | /scvhistory/signal/perkins/part03.html |  |
| 22 | footnotes[0] | The citizens Francisco Lopez, Manuel Cota and Domingo Bermudez, residents of the Port of Santa Barbara, before Your Excellency with the utmo [cut here for the table] | PASS | /scvhistory/signal/perkins/part03.html |  |
| 23 | footnotes[0] | In the archives at Sacramento may be found the following document, which is quoted in full because it constitutes the first mining location  [cut here for the table] | PASS | /scvhistory/signal/perkins/part03.html |  |
| 24 | footnotes[0] | on the ninth day of March last | PASS | /scvhistory/signal/coins/worden-coinage1005.htm |  |
| 25 | footnotes[0] | California's REAL First Gold, | PASS | /scvhistory/signal/coins/worden-coinage1005.htm |  |
| 26 | footnotes[0] | in the National Archives in Washington, D.C. | PASS | /scvhistory/signal/coins/worden-coinage1005.htm |  |
| 27 | footnotes[1] | The placer mines from which this gold was taken was first discovered by Francisco Lopez, a native of California, in the month of March, 1842 [cut here for the table] | PASS | /scvhistory/lp_santacruzsentinel082785.htm |  |
| 28 | footnotes[1] | Lopez with a companion, was out in search of some stray horses, and about midday they stopped under some trees and tied their horses out to  [cut here for the table] | PASS | /scvhistory/lp_santacruzsentinel082785.htm |  |
| 29 | footnotes[1] | the placers were worked with more or less success, and principally by Sonorensee (Sonorians), until the latter part of 1846, when most of th [cut here for the table] | PASS | /scvhistory/lp_santacruzsentinel082785.htm |  |
| 30 | footnotes[1] | November 22d, 1842, I sent by Alfred Robinson, Esq., (who returned from California to the States by the way of Mexico), twenty ounces Califo [cut here for the table] | CORRECTED | /scvhistory/lp_santacruzsentinel082785.htm | v2 change: The ellipsis stood for Stearns's parenthesis. No ellipsis survives v2; the parenthesis is restored. |
| 31 | footnotes[2] | The True History of the First Discovery of Gold in California in 1842 | PASS | /scvhistory/belderrain1930.htm |  |
| 32 | footnotes[2] | Francisco Lopez made California's first 'documented' discovery of gold in the Santa Clarita Valley. It was probably in 1842 (unless it was i [cut here for the table] | PASS | /scvhistory/belderrain1930.htm | inner double quotation marks become single |
| 33 | footnotes[2] | San Francisquito | PASS | /scvhistory/belderrain1930.htm |  |
| 34 | footnotes[2] | where gold occurs in placer form even today, | PASS | craft #26983 |  |
| 35 | footnotes[2] | makes the Placerita discovery California's first 'documented' gold find. | PASS | /scvhistory/sw9501.htm | inner double quotation marks become single |
| 36 | footnotes[2] | documented | PASS | /scvhistory/belderrain1930.htm |  |
| 37 | footnotes[2] | documented | PASS | /scvhistory/belderrain1930.htm |  |
| 38 | footnotes[3] | California Gold, | PASS | /scvhistory/lw2181.htm |  |
| 39 | footnotes[3] | A letter from California, dated May 1, speaking of the discovery of gold in that country, says | PASS | /scvhistory/lw2181.htm |  |
| 40 | footnotes[3] | They have at last discovered gold, not far from San Fernando, and gather pieces of the size of an eighth of a dollar. Those who are acquaint [cut here for the table] | PASS | /scvhistory/lw2181.htm | inner double quotation marks ("placeres,") become single |
| 41 | footnotes[3] | placeres, | PASS | /scvhistory/lw2181.htm |  |
| 42 | footnotes[4] | Dissecting the Dream: Fact, Fiction, and Placerita's Golden Oak, | PASS | /scvhistory/pollack1115dream.htm |  |
| 43 | footnotes[4] | There has been a gold mine discovered about forty miles from the Pueblo, the gold is of a fine quality & some grains have been found worth n [cut here for the table] | PASS | /scvhistory/pollack1115dream.htm |  |
| 44 | footnotes[5] | Senor Ygnacio del Valle. / In charge of Justice of Law Enforcement / Rancho del Mission San Fernando, | CORRECTED | /scvhistory/signal/perkins/part03.html | v2 change: Perkins prints the address in three lines (<br> on the page) with no comma after "Enforcement"; v1 added one. |
| 45 | footnotes[5] | 3. The Placerita Gold Rush, | PASS | /scvhistory/signal/perkins/part03.html |  |
| 46 | footnotes[5] | a number of people are gathering at this place, and in order that this work may proceed in an orderly fashion, I have appointed a magistrate [cut here for the table] | PASS | /scvhistory/signal/perkins/part03.html |  |
| 47 | footnotes[5] | As for the eight dollars which you collect for entering, and for the time they remain there, in consideration of this, they will be in posse [cut here for the table] | PASS | /scvhistory/signal/perkins/part03.html |  |
| 48 | footnotes[5] | The original document, in Spanish, is in Bancroft Library, | PASS | /scvhistory/signal/perkins/part03.html |  |
| 49 | footnotes[5] | Ygnacio del Valle, oldest son of Antonio. | PASS | /scvhistory/signal/perkins/part03.html | the sentence continues ", immediately petitioned"; terminal punctuation only |
| 50 | footnotes[6] | Memorandum of gold bullion deposited the 8th day of July, 1843, at the mint of the United States at Philadelphia, by Grant & Stone, of weigh [cut here for the table] | CORRECTED | /scvhistory/lp_santacruzsentinel082785.htm | v2 change: The ellipsis joined the memorandum's heading to a later line of its table, across the weights and fineness: two clauses. Quoted in two parts. |
| 51 | footnotes[6] | value, $344.75. | CORRECTED | /scvhistory/lp_santacruzsentinel082785.htm | v2 change: The ellipsis joined the memorandum's heading to a later line of its table, across the weights and fineness: two clauses. Quoted in two parts. |
| 52 | footnotes[6] | California's Discovery of Gold in 1841, | PASS | /scvhistory/overlandmonthly0592.htm |  |
| 53 | footnotes[6] | there was such a deposit of gold by Grant & Stone June 8th, 1843, as evidenced by voucher No. 150 [sic] belonging to the bullion accounts, N [cut here for the table] | PASS | /scvhistory/overlandmonthly0592.htm |  |
| 54 | footnotes[6] | Voucher No. 350. | PASS | /scvhistory/overlandmonthly0592.htm | continues ", filed with"; terminal punctuation only |
| 55 | footnotes[6] | no record of such a deposit from 1841 to 1844 can be found here. | PASS | /scvhistory/overlandmonthly0592.htm |  |
| 56 | footnotes[6] | not decided. | PASS | craft #30537 | the source fault's title ends "not decided" |
| 57 | footnotes[7] | California's Discovery of Gold in 1841, | PASS | /scvhistory/overlandmonthly0592.htm |  |
| 58 | footnotes[7] | An Historical Sketch of Los Angeles County | PASS | /scvhistory/overlandmonthly0592.htm |  |
| 59 | footnotes[7] | the first known grain of native gold dust was found upon, or near, the San Francisco Ranch, about forty-five miles westerly from Los Angeles [cut here for the table] | PASS | /scvhistory/overlandmonthly0592.htm |  |
| 60 | footnotes[7] | Not long after my return home, I accompanied three or four men to the gold fields on the ranch of San Francisco. | PASS | /scvhistory/overlandmonthly0592.htm | continues ", then owned by Antonio Del Valle."; terminal punctuation only |
| 61 | footnotes[7] | Thus the date of the discovery is established as being 1841. | PASS | /scvhistory/overlandmonthly0592.htm |  |
| 62 | footnotes[8] | The First Discovery of Gold in California, | PASS | /scvhistory/sw9501.htm |  |
| 63 | footnotes[8] | From this mass of contradictory data it is impossible to evolve the correct one. Nor is it probably that the exact date will ever be known.  [cut here for the table] | PASS | /scvhistory/sw9501.htm |  |
| 64 | footnotes[8] | shows conclusively that Don Abel Stearns was mistaken, and that the year 1841 is the correct date of the discovery of gold in the San Felici [cut here for the table] | PASS | /scvhistory/sw9501.htm | continues ", Los Angeles county."; terminal punctuation only |
| 65 | footnotes[8] | a quart bottle of gold dust containing about 80 ounces. | PASS | /scvhistory/sw9501.htm | continues "obtained about where"; terminal punctuation only |
| 66 | footnotes[9] | Baptismal Data: Francisco Lopez (Gold Discoverer), | PASS | /scvhistory/sgb03346.htm |  |
| 67 | footnotes[9] | Age: 1 dia (day) | PASS | /scvhistory/sgb03346.htm |  |
| 68 | footnotes[9] | Birth date: el dia antecedente (the previous day). | PASS | /scvhistory/sgb03346.htm | the database page has no closing period |
| 69 | footnotes[10] | San Fernando Rey, The Mission of the Valley, | PASS | /scvhistory/engelhardt_lopezgold.htm |  |
| 70 | footnotes[10] | according to the Rev. Eugene Sugranes, C.M.F., who had the facts from the niece of the discoverer, Catalina Lopez. On March 9, 1842, the fea [cut here for the table] | PASS | /scvhistory/engelhardt_lopezgold.htm | the page sets its footnote marker [1] after "Rancho"; the marker is left out |
| 71 | footnotes[10] | There is no 'golden dream' (and thus no oak tree) in Engelhardt. | PASS | /scvhistory/engelhardt_lopezgold.htm | inner double quotation marks become single |
| 72 | footnotes[10] | golden dream | PASS | /scvhistory/engelhardt_lopezgold.htm |  |
| 73 | footnotes[11] | Gold Discovery in California: Who Was the First Real Discoverer of Gold in This State? | PASS | /scvhistory/prudhomme1922hssc.htm | a title; the page prints "Gold Discovery in California. Who Was..." The colon is citation style |
| 74 | footnotes[11] | These men, being weary, tethered their tired horses, and proceeded to make themselves comfortable, taking their siesta under the shade of th [cut here for the table] | PASS | /scvhistory/prudhomme1922hssc.htm |  |
| 75 | footnotes[12] | The True History of the First Discovery of Gold in California in 1842 And A Short Biography of Francisco Lopez, the Discoverer, | PASS | /scvhistory/belderrain1930.htm |  |
| 76 | footnotes[12] | With a genuine romantic touch, Rivera suggested the words: 'Encino del Ensueno Dorado,' meaning 'Oak of the Golden Dream' for a tablet place [cut here for the table] | PASS | /scvhistory/belderrain1930.htm | inner double quotation marks become single |
| 77 | footnotes[12] | a shady tree under which to rest and have lunch, | PASS | /scvhistory/belderrain1930.htm |  |
| 78 | footnotes[12] | a lengthy siesta | PASS | /scvhistory/belderrain1930.htm |  |
| 79 | footnotes[12] | Encino del Ensueno Dorado, | PASS | /scvhistory/belderrain1930.htm |  |
| 80 | footnotes[12] | Oak of the Golden Dream | PASS | /scvhistory/belderrain1930.htm |  |
| 81 | footnotes[13] | Tentative Program for 1930 Dedication, | PASS | /scvhistory/lw2575b.htm |  |
| 82 | footnotes[13] | It was Adolfo who determined which tree would be associated with the legend of Lopez's golden dream, on the word of relatives of a Lopez des [cut here for the table] | PASS | /scvhistory/lw2575b.htm |  |
| 83 | footnotes[13] | the 500 year old oak tree, under whose shade Francisco Lopez enjoyed his mid-day siesta on the day of his discovery. | PASS | /scvhistory/lw2575b.htm |  |
| 84 | footnotes[14] | Dissecting the Dream: Fact, Fiction, and Placerita's Golden Oak, | PASS | /scvhistory/pollack1115dream.htm |  |
| 85 | footnotes[14] | (W)e wish to recall March 9, 1842, when Francisco Lopez arose from under that Tree of the Golden Dreams, and made that epochal Discovery of  [cut here for the table] | PASS | /scvhistory/pollack1115dream.htm |  |
| 86 | footnotes[14] | The very first time we would see mention of a golden dream and a golden oak was in 1930 by historians Arthur B. Perkins and Adolfo G. Rivera | PASS | /scvhistory/pollack1115dream.htm |  |
| 87 | footnotes[14] | Francisco Lopez made the first documented gold discovery in California history in the Santa Clarita Valley, most likely in Placerita Canyon. | PASS | /scvhistory/pollack1115dream.htm |  |
| 88 | footnotes[15] | Saga of Rancho El Tejon, | PASS | /scvhistory/latta1976franciscolopez.htm |  |
| 89 | footnotes[15] | He took them to the women folks at the ranch kitchen and, as I remember it, the gold was not discovered until the onions were being washed. | PASS | /scvhistory/latta1976franciscolopez.htm |  |
| 90 | footnotes[16] | 3. The Placerita Gold Rush, | PASS | /scvhistory/signal/perkins/part03.html |  |
| 91 | footnotes[16] | Francisco Lopez / Here discovered the first gold in California, / March 9, 1842. / This plate placed March 9, 1930, / by the Ramona Parlor N [cut here for the table] | PASS | /scvhistory/signal/perkins/part03.html | the plaque's lines (<br> on the page) are marked with slashes |
| 92 | footnotes[16] | Encino de Los Ensuenos Dorados / de Francisco Lopez / Oak of the Golden Dream / placed by La Mesa Club / March 9, 1930. | PASS | /scvhistory/signal/perkins/part03.html | lines marked with slashes; the plaque text has no closing period |
| 93 | footnotes[17] | Placerita Gold: Sutter and Marshall knew they weren't the first to discover gold in California, | PASS | /scvhistory/vl0406-lopez.htm | a title; the page prints the subtitle on its own line. The colon is citation style |
| 94 | footnotes[17] | Francisco Lopez made California's first authenticated gold discovery on March 9, 1842 | PASS | /scvhistory/vl0406-lopez.htm |  |
| 95 | footnotes[17] | There's a chance it isn't the right tree, | CORRECTED | /scvhistory/vl0406-lopez.htm | v2 change: The ellipsis here is the page's own, not an omission, but no ellipsis survives v2: the sentence is given in its two parts. |
| 96 | footnotes[17] | but that's another story. | CORRECTED | /scvhistory/vl0406-lopez.htm | v2 change: The ellipsis here is the page's own, not an omission, but no ellipsis survives v2: the sentence is given in its two parts. |
| 97 | editorNotes[0].note | Chico | PASS | craft #28132 |  |
| 98 | editorNotes[0].note | also known by the name of Cuso | PASS | /scvhistory/overlandmonthly0592.htm |  |
| 99 | editorNotes[1].note | In 1834 the placers of San Francisco, Placerita and Castiac and the San Feliciana, forty-five miles northwest from Los Angeles, were discove [cut here for the table] | CORRECTED | /scvhistory/hssc1906jenkins.htm | v2 change: The ellipsis stood for Jenkins's words "forty-five miles northwest from Los Angeles" (and the webmaster's bracketed notes, which are not Jenkins's and are left out, as v1 also left out "[Rancho?]"). J |
| 100 | editorNotes[1].note | evidently following Stearns, | PASS | /scvhistory/pollack1115dream.htm |  |
| 101 | editorNotes[2].note | at the place of San Francisco, appertaining to the late Don Antonio del Valle, distant from his house about one league toward the south. | PASS | /scvhistory/signal/perkins/part03.html | the petition continues ", we apply"; terminal punctuation only |
| 102 | editorNotes[2].note | a place called San Francisquito, about thirty-five miles north-west of this city. | PASS | /scvhistory/lp_santacruzsentinel082785.htm | the page adds "[Los Angeles]" after "this city"; terminal punctuation only |
| 103 | editorNotes[2].note | upon, or near, the San Francisco Ranch, about forty-five miles westerly from Los Angeles city. | PASS | /scvhistory/overlandmonthly0592.htm | continues ", in the month of June, 1841."; terminal punctuation only |
| 104 | editorNotes[2].note | the San Feliciano Canyon, in the county of Los Angeles | CORRECTED | /scvhistory/sw9501.htm | v2 change: The ellipsis joined two of Guinn's sentences. Quoted in two parts. |
| 105 | editorNotes[2].note | This canyon is about eight miles northwest of Newhall. | CORRECTED | /scvhistory/sw9501.htm | v2 change: The ellipsis joined two of Guinn's sentences. Quoted in two parts. |
| 106 | editorNotes[3].note | Before melting, 18 34-100 oz.; after melting, 19 1-100 oz.; fineness, 926-1000; value, $344.75 | PASS | craft #26983 |  |
| 107 | editorNotes[3].note | after melting, 18 1-100 oz. | PASS | /scvhistory/overlandmonthly0592.htm |  |
| 108 | editorNotes[3].note | $244.75. | PASS | /scvhistory/prudhomme1922hssc.htm | Prudhomme prints "value $244.75;" |
| 109 | eventPlaces[0].tie | place of San Francisco, appertaining to the late Don Antonio del Valle | PASS | /scvhistory/signal/coins/worden-coinage1005.htm |  |
| 110 | photographs[3].tie | after sleeping beneath these boughs | PASS | /scvhistory/lw2217.htm |  |
| 111 | photographs[4].tie | FIRST GOLD DISCOVERY / PLACERITA CANYON / 1842 | PASS | /scvhistory/lw2352.htm |  |
| 112 | theLiterature[0].cite | California's Discovery of Gold in 1841, | PASS | /scvhistory/overlandmonthly0592.htm |  |
| 113 | theLiterature[1].cite | The First Discovery of Gold in California, | PASS | /scvhistory/sw9501.htm |  |
| 114 | theLiterature[2].cite | Gold Discovery in California, | PASS | /scvhistory/prudhomme1922hssc.htm |  |
| 115 | theLiterature[3].cite | San Fernando Rey, The Mission of the Valley, | PASS | /scvhistory/engelhardt_lopezgold.htm |  |
| 116 | theLiterature[4].cite | The True History of the First Discovery of Gold in California in 1842, | PASS | /scvhistory/belderrain1930.htm |  |
| 117 | theLiterature[5].cite | The Story of Our Valley, | PASS | craft #28132 |  |
| 118 | theLiterature[6].cite | Dissecting the Dream: Fact, Fiction, and Placerita's Golden Oak, | PASS | /scvhistory/pollack1115dream.htm |  |
| 119 | theLiterature[7].cite | History of the Development of Placer Mining in California, | PASS | /scvhistory/hssc1906jenkins.htm |  |
| 120 | theLiterature[9].cite | Saga of Rancho El Tejon, | PASS | /scvhistory/latta1976franciscolopez.htm |  |
| 121 | researchLeads[0] | in the archives at Sacramento | PASS | /scvhistory/signal/perkins/part03.html | Perkins: "In the archives at Sacramento"; capital I at the head of his sentence |
| 122 | researchLeads[0] | in the National Archives in Washington, D.C. | PASS | /scvhistory/signal/coins/worden-coinage1005.htm |  |
| 123 | researchLeads[0] | the 9th day of March last | PASS | /scvhistory/signal/perkins/part03.html |  |
| 124 | researchLeads[3] | 125 pounds | PASS | craft #15294 |  |
| 125 | researchLeads[3] | Governor | PASS | craft #12154 |  |
| 126 | researchLeads[7] | first documented | PASS | /scvhistory/lw2352.htm |  |
| 127 | researchLeads[10] | Rancho San Francisco: A Study of a California Land Grant | PASS | craft #27374 |  |

## Changes from v1 to v2

1. **footnotes[1].** Was: "November 22d, 1842, I sent by Alfred Robinson, Esq., ... twenty ounces Now: "November 22d, 1842, I sent by Alfred Robinson, Esq., (who returned from California to the States by the way of Mexico), twenty ounces Why: The ellipsis stood for Stearns's parenthesis. No ellipsis survives v2; the parenthesis is restored.
2. **footnotes[5].** Was: "Senor Ygnacio del Valle. In charge of Justice of Law Enforcement, Rancho del Mission San Fernando," Now: "Senor Ygnacio del Valle. / In charge of Justice of Law Enforcement / Rancho del Mission San Fernando," Why: Perkins prints the address in three lines (<br> on the page) with no comma after "Enforcement"; v1 added one.
3. **footnotes[6].** Was: "Memorandum of gold bullion deposited the 8th day of July, 1843, at the mint of the United States at Philadelphia, by Grant & Stone ... value, $344.75." Now: "Memorandum of gold bullion deposited the 8th day of July, 1843, at the mint of the United States at Philadelphia, by Grant & Stone, of weight and value as follows"; then "value, $344.75." Why: The ellipsis joined the memorandum's heading to a later line of its table, across the weights and fineness: two clauses. Quoted in two parts.
4. **footnotes[17].** Was: "There's a chance it isn't the right tree ... but that's another story." Now: "There's a chance it isn't the right tree," and "but that's another story." Why: The ellipsis here is the page's own, not an omission, but no ellipsis survives v2: the sentence is given in its two parts.
5. **editorNotes[1].note.** Was: "In 1834 the placers of San Francisco, Placerita and Castiac and the San Feliciana ... were discovered" Now: "In 1834 the placers of San Francisco, Placerita and Castiac and the San Feliciana, forty-five miles northwest from Los Angeles, were discovered" Why: The ellipsis stood for Jenkins's words "forty-five miles northwest from Los Angeles" (and the webmaster's bracketed notes, which are not Jenkins's and are left out, as v1 also left out "[Rancho?]"). Jenkins's words are restored.
6. **editorNotes[2].note.** Was: "the San Feliciano Canyon ... about eight miles northwest of Newhall." Now: "the San Feliciano Canyon, in the county of Los Angeles"; "This canyon is about eight miles northwest of Newhall." Why: The ellipsis joined two of Guinn's sentences. Quoted in two parts.
7. **researchLeads[3].** Was: '200 ounces by November' Now: two hundred ounces by November Why: Not the sources' words: Leon Worden (2005, #15294) has "by November 1842, two hundred ounces" and Reynolds chapter 16 (#853) "By November, two hundred ounces". The figure is given without quotation marks.
8. **recordDates.** Was: no ISO dates Now: iso on every row: 1842-03-09, 1842-04-04, 1842-05-01, 1842-05-03, 1842-05-11, 1842-11-22, 1843-06-08, 1843-07-08, 1930-03-09, 1935-03-06 Why: The calendar reads the ISO column. Each is the row's own printed date written as ISO; the bracketed years are the draft's own (the body and footnotes give them). confirmed is left unset: nothing in the draft confirms a row.

## The event as it would read

- **eventDate:** March 9, 1842
- **eventDateEdtf:** 1842-03-09
- **eventDateStart:** (empty)
- **eventDateEnd:** (empty)
- **startEvidence:** contemporary
- **eventChlNumber:** 168
- **eventSignificance:** Francisco Lopez's find of placer gold on the Rancho San Francisco in March 1842, probably in Placerita Canyon, is the first documented discovery of gold in California, dated by a petition of the time. It brought the valley its first miners, most of them from Sonora, and its first magistrate for a mining camp.
- **historicalEra:** #159 Mexican Rancho Era (1821–1847)
- **historicalPeriod:** #172 Pre-1850
- **recordTags:** #18934 Mining & Gold
- **neighborhood:** #202 Placerita Canyon
- **featuredImage:** (empty) none attached anywhere in Craft; first choice asset 14123 (LW2217, the oak, about 1963) once it is attached to photograph #2989
- **bandImage:** (empty) none: no wide image of the canyon or the oak is in Craft

Francisco Lopez found placer gold on the Rancho San Francisco in March 1842, probably in what is now Placerita Canyon. Leon Worden and Alan Pollack call it the first "documented" discovery of gold in California.[1][3][15] On April 4, 1842, at Santa Barbara, Lopez and his companions Manuel Cota and Domingo Bermudez asked the governor for leave to work it. Their petition, as A.B. Perkins printed it in translation, says that "His Divine Majesty" had granted them "a placer of gold on the 9th day of March last, at the place of San Francisco, appertaining to the late Don Antonio del Valle, distant from his house about one league toward the south," and sends specimens with it.[1] Abel Stearns, the Los Angeles merchant who handled the gold, wrote in 1867 that Lopez found it "in the month of March, 1842, at a place called San Francisquito," while out with a companion after stray horses: resting at midday under some trees, Lopez "with his sheath knife dug up some wild onions, and in the dirt discovered a piece of gold."[2]

The find brought the valley its first miners. A letter from California dated May 1, printed in the New York Observer that October, reported "They have at last discovered gold, not far from San Fernando," and "Gold to the amount of some thousands of dollars has already been collected."[4] On May 11 Pliny F. Temple wrote of "upwards of Fifty men to work washing the earth," with Sonorans expected in the fall.[5] On May 3, 1842, the prefect at Los Angeles, Santiago Argüello, named Ygnacio del Valle, the late grantee's son, magistrate for the placer, to keep order among the people gathering there, and approved the eight dollars he was charging each for entry, pasture, water and firewood.[6] Stearns wrote that the placers were worked "principally by Sonorensee (Sonorians), until the latter part of 1846."[2]

On November 22, 1842, Stearns sent twenty ounces of the gold by Alfred Robinson to the United States mint at Philadelphia. Robinson's copy of the mint's memorandum dates the deposit July 8, 1843, and values it at $344.75; a Treasury voucher found in 1891 records a deposit of the same value, by the same depositors, Grant & Stone, on June 8, 1843. Both dates are kept.[2][7]

The year has been disputed since 1876. J.J. Warner, who visited the diggings, gave June 1841, and John Murray, publishing Warner's letters in 1892, declared 1841 established.[8] J.M. Guinn found in 1895 that "The strongest evidence seems to incline toward March 1842," then printed, the same year, a pioneer's letter that he said "shows conclusively" that 1841 was right.[9] The ninth of March is the petition's own date. By his baptismal record it was also Lopez's fortieth birthday, as the mission historian Zephyrin Engelhardt, writing from the family's account, noted in 1927.[1][10][11] In Leon Worden's words, the discovery was "probably in 1842 (unless it was in 1841), and it was probably in Placerita Canyon (unless it was somewhere around Hasley Canyon)."[3]

The golden dream is legend. Stearns's account has no dream and no particular tree, and Engelhardt's has neither.[2][3][11] Charles J. Prudhomme, guided over the ground in 1920 by Lopez's kinswomen, has the men "taking their siesta under the shade of the oak tree," with no dream.[12] The dream enters in 1930, when Adolfo G. Rivera and A.B. Perkins prepared the 88th anniversary. Rivera named a tree "Encino del Ensueno Dorado," Oak of the Golden Dream, and chose it, in Leon Worden's words, "on the word of relatives of a Lopez descendant who pointed it out to them 70 years after the fact"; Perkins spoke that day of Lopez rising "from under that Tree of the Golden Dreams."[13][14][15] Francisca Lopez de Belderrain's own account of that year has Lopez nap beneath the oak, but not dream. His grandnephew, José Jesús López, who heard him tell it, remembered that "the gold was not discovered until the onions were being washed."[13][16]

On March 9, 1930, Ramona Parlor No. 109 of the Native Sons of the Golden West, La Mesa Club and the Kiwanis Club of Newhall-Saugus placed a plate reading "Francisco Lopez / Here discovered the first gold in California, / March 9, 1842," and La Mesa Club another on the oak.[17] The state later registered the oak as California Historical Landmark No. 168; its description says that "Francisco Lopez made California's first authenticated gold discovery on March 9, 1842."[18]

The discovery has its own literature, most of it about the date and the legend: Murray (1892), Guinn (1895), Prudhomme (1922), Engelhardt (1927), Belderrain (1930), Perkins's Story of Our Valley, and Alan Pollack's "Dissecting the Dream: Fact, Fiction, and Placerita's Golden Oak" (2015).[8][9][12][11][13][1][15]

1. Francisco Lopez, Manuel Cota and Domingo Bermudez to the Governor, Santa Barbara, April 4, 1842, in translation, as quoted in full by A.B. Perkins, "3. The Placerita Gold Rush," The Story of Our Valley, part 3, article #1424 in this archive, as carried on SCVHistory.com, /scvhistory/signal/perkins/part03.html: "The citizens Francisco Lopez, Manuel Cota and Domingo Bermudez, residents of the Port of Santa Barbara, before Your Excellency with the utmost submission, appear saying that His Divine Majesty having granted us a placer of gold on the 9th day of March last, at the place of San Francisco, appertaining to the late Don Antonio del Valle, distant from his house about one league toward the south, we apply to Your Excellency to be pleased to decree in our favor whatsoever you may deem proper and just, forwarding herewith the specimens of said gold." Perkins: "In the archives at Sacramento may be found the following document, which is quoted in full because it constitutes the first mining location notice of California." Leon Worden quotes the same translation ("on the ninth day of March last") in "California's REAL First Gold," COINage, October 2005, article #15294 in this archive (/scvhistory/signal/coins/worden-coinage1005.htm), and places the original "in the National Archives in Washington, D.C."
2. Abel Stearns to Louis R. Lull, Secretary of the Society of Pioneers, Los Angeles, July 8, 1867, with Alfred Robinson's letter to Stearns of August 6, 1843, printed in the Santa Cruz Sentinel, August 27, 1885, document #26983 in this archive (/scvhistory/lp_santacruzsentinel082785.htm). Written twenty-five years after the find, from Stearns's account books for the shipment. "The placer mines from which this gold was taken was first discovered by Francisco Lopez, a native of California, in the month of March, 1842, at a place called San Francisquito, about thirty-five miles north-west of this city"; "Lopez with a companion, was out in search of some stray horses, and about midday they stopped under some trees and tied their horses out to feed, they resting under the shade; when Lopez with his sheath knife dug up some wild onions, and in the dirt discovered a piece of gold, and searching further found some more"; "the placers were worked with more or less success, and principally by Sonorensee (Sonorians), until the latter part of 1846, when most of the Sonorensee left with Captain Flores for Sonora"; "November 22d, 1842, I sent by Alfred Robinson, Esq., (who returned from California to the States by the way of Mexico), twenty ounces California weight (18¾ ounces mint weight) of placer gold." See also the person record for Abel Stearns, #309.
3. Leon Worden, webmaster's note to Francisca Lopez de Belderrain, "The True History of the First Discovery of Gold in California in 1842" (1930), as carried on SCVHistory.com, /scvhistory/belderrain1930.htm: "Francisco Lopez made California's first 'documented' discovery of gold in the Santa Clarita Valley. It was probably in 1842 (unless it was in 1841), and it was probably in Placerita Canyon (unless it was somewhere around Hasley Canyon)." His note on document #26983 sets out the two readings of Stearns's "San Francisquito": San Francisquito Canyon, "where gold occurs in placer form even today," or the colloquial name of the wider valley. His note to Guinn (/scvhistory/sw9501.htm) calls the petition of April 4, 1842 the document that "makes the Placerita discovery California's first 'documented' gold find."
4. "California Gold," New York Observer, October 1, 1842, page 3, photograph #2963 in this archive (LW2181, /scvhistory/lw2181.htm): "A letter from California, dated May 1, speaking of the discovery of gold in that country, says"; then, "They have at last discovered gold, not far from San Fernando, and gather pieces of the size of an eighth of a dollar. Those who are acquainted with these 'placeres,' as they call them, (for it is not a mine,) say it will grow richer, and may lead to a mine. Gold to the amount of some thousands of dollars has already been collected." The printed item sets a dash before the letter's words. The writer of the letter is not named.
5. Pliny F. Temple to Abraham Temple, May 11, 1842, as quoted by Alan Pollack, "Dissecting the Dream: Fact, Fiction, and Placerita's Golden Oak," Heritage Junction Dispatch, November-December 2015, as carried on SCVHistory.com, /scvhistory/pollack1115dream.htm: "There has been a gold mine discovered about forty miles from the Pueblo, the gold is of a fine quality & some grains have been found worth nearly three dollars. There are upwards of Fifty men to work washing the earth & there are expected the coming fall a large number of Sonorians to work in the mines." Pollack does not say where the letter is held.
6. S. Argüello to "Senor Ygnacio del Valle. / In charge of Justice of Law Enforcement / Rancho del Mission San Fernando," May 3, 1842, in translation (by Lois Phillips of Hart High School), as quoted in full by A.B. Perkins, "3. The Placerita Gold Rush," article #1424 in this archive, as carried on SCVHistory.com, /scvhistory/signal/perkins/part03.html: "a number of people are gathering at this place, and in order that this work may proceed in an orderly fashion, I have appointed a magistrate for that place in order to keep law and order"; "As for the eight dollars which you collect for entering, and for the time they remain there, in consideration of this, they will be in possession and owe it for pasturing their livestock, for water, firewood and even lumber for temporary shelters. This charge seems just, collected only once." Perkins: "The original document, in Spanish, is in Bancroft Library," and, citing Bancroft, Argüello was appointed Prefect in 1840; "Ygnacio del Valle, oldest son of Antonio."
7. Alfred Robinson to Abel Stearns, New York, August 6, 1843, in document #26983: "Memorandum of gold bullion deposited the 8th day of July, 1843, at the mint of the United States at Philadelphia, by Grant & Stone, of weight and value as follows"; then "value, $344.75." J.R. Garrison, Acting Comptroller of the Treasury, to John Murray, October 21, 1891, in John Murray, "California's Discovery of Gold in 1841," Overland Monthly, vol. 19, no. 113, May 1892, pp. 524-529, as carried on SCVHistory.com, /scvhistory/overlandmonthly0592.htm: "there was such a deposit of gold by Grant & Stone June 8th, 1843, as evidenced by voucher No. 150 [sic] belonging to the bullion accounts, No. 86,829, of Isaac Roach, Treasurer of the Mint at Philadelphia"; the certified copy is of "Voucher No. 350." The mint itself had answered on October 5, 1891, that "no record of such a deposit from 1841 to 1844 can be found here." The archive records the disagreement as source fault #30537 on Stearns's record, "not decided."
8. John Murray, "California's Discovery of Gold in 1841," Overland Monthly, May 1892, as carried on SCVHistory.com, /scvhistory/overlandmonthly0592.htm, quoting J.J. Warner's part of "An Historical Sketch of Los Angeles County" (Warner, Hayes and Widney, 1876): "the first known grain of native gold dust was found upon, or near, the San Francisco Ranch, about forty-five miles westerly from Los Angeles city, in the month of June, 1841"; and Warner's letter to Murray of November 13, 1891: "Not long after my return home, I accompanied three or four men to the gold fields on the ranch of San Francisco." Murray: "Thus the date of the discovery is established as being 1841."
9. J.M. Guinn, "The First Discovery of Gold in California," San Francisco Call, September 8, 1895, and his introduction to I.L. Given's letter of the same date, Annual Publication of the Historical Society of Southern California, 1895, as carried on SCVHistory.com, /scvhistory/sw9501.htm. In the Call: "From this mass of contradictory data it is impossible to evolve the correct one. Nor is it probably that the exact date will ever be known. The strongest evidence seems to incline toward March 1842." In the Annual: Given's letter "shows conclusively that Don Abel Stearns was mistaken, and that the year 1841 is the correct date of the discovery of gold in the San Feliciano placers, near Newhall." Given wrote that in the fall of 1841 Stearns showed him "a quart bottle of gold dust containing about 80 ounces."
10. "Baptismal Data: Francisco Lopez (Gold Discoverer)," San Gabriel baptism no. 03346, from the Huntington Library's Early California Population Project database, as carried on SCVHistory.com, /scvhistory/sgb03346.htm: baptized March 10, 1802, "Age: 1 dia (day)"; "Birth date: el dia antecedente (the previous day)."
11. Fr. Zephyrin Engelhardt, O.F.M., "San Fernando Rey, The Mission of the Valley," Chicago: Franciscan Herald Press 1927, pp. 143-144, as carried on SCVHistory.com, /scvhistory/engelhardt_lopezgold.htm: "according to the Rev. Eugene Sugranes, C.M.F., who had the facts from the niece of the discoverer, Catalina Lopez. On March 9, 1842, the feast of St. Frances (Francesca) of Rome, Francisco Lopez, then in charge of San Francisquito Rancho determined to celebrate his birthday by adding to the dinner some fresh vegetables he had cultivated." Leon Worden's note on the page: "There is no 'golden dream' (and thus no oak tree) in Engelhardt."
12. Charles J. Prudhomme, "Gold Discovery in California: Who Was the First Real Discoverer of Gold in This State?" Annual Publication, Historical Society of Southern California, 1922, as carried on SCVHistory.com, /scvhistory/prudhomme1922hssc.htm. Of a visit of November 21, 1920, guided by Francisca Lopez de Bilderrain [Belderrain] and Romona Lopez Shung: "These men, being weary, tethered their tired horses, and proceeded to make themselves comfortable, taking their siesta under the shade of the oak tree."
13. Francisca Lopez de Belderrain, "The True History of the First Discovery of Gold in California in 1842 And A Short Biography of Francisco Lopez, the Discoverer," 1930, as carried on SCVHistory.com, /scvhistory/belderrain1930.htm: "With a genuine romantic touch, Rivera suggested the words: 'Encino del Ensueno Dorado,' meaning 'Oak of the Golden Dream' for a tablet placed by La Mesa Club on the oak tree beneath which Francisco Lopez slept just before making his great discovery." Her account has him select "a shady tree under which to rest and have lunch," and after "a lengthy siesta" dig the onions; it has no dream.
14. Leon Worden's note to "Tentative Program for 1930 Dedication," LW2575b, photograph #4169 in this archive (/scvhistory/lw2575b.htm): "It was Adolfo who determined which tree would be associated with the legend of Lopez's golden dream, on the word of relatives of a Lopez descendant who pointed it out to them 70 years after the fact." The program, signed by A.G. Rivera, promises "the 500 year old oak tree, under whose shade Francisco Lopez enjoyed his mid-day siesta on the day of his discovery."
15. Alan Pollack, "Dissecting the Dream: Fact, Fiction, and Placerita's Golden Oak," Heritage Junction Dispatch, November-December 2015, as carried on SCVHistory.com, /scvhistory/pollack1115dream.htm: Perkins in his speech of March 9, 1930, "(W)e wish to recall March 9, 1842, when Francisco Lopez arose from under that Tree of the Golden Dreams, and made that epochal Discovery of Gold"; "The very first time we would see mention of a golden dream and a golden oak was in 1930 by historians Arthur B. Perkins and Adolfo G. Rivera"; and in conclusion, "Francisco Lopez made the first documented gold discovery in California history in the Santa Clarita Valley, most likely in Placerita Canyon."
16. José Jesús López (1853-1939), grandnephew of the discoverer, in Frank F. Latta, "Saga of Rancho El Tejon," Santa Cruz: Bear State Books 1976, p. 171, as carried on SCVHistory.com, /scvhistory/latta1976franciscolopez.htm: "He took them to the women folks at the ranch kitchen and, as I remember it, the gold was not discovered until the onions were being washed." Told to Latta in interviews from 1916 on.
17. A.B. Perkins, "3. The Placerita Gold Rush," article #1424 in this archive, as carried on SCVHistory.com, /scvhistory/signal/perkins/part03.html, giving the plaque: "Francisco Lopez / Here discovered the first gold in California, / March 9, 1842. / This plate placed March 9, 1930, / by the Ramona Parlor No. 109, N.S.G.W., / La Mesa Club, Kiwanis Club of Newhall-Saugus," and on the oak, "Encino de Los Ensuenos Dorados / de Francisco Lopez / Oak of the Golden Dream / placed by La Mesa Club / March 9, 1930."
18. Leon Worden, "Placerita Gold: Sutter and Marshall knew they weren't the first to discover gold in California," Santa Clarita Valley Living, February 2006, as carried on SCVHistory.com, /scvhistory/vl0406-lopez.htm, quoting the state's description of California Historical Landmark No. 168: "Francisco Lopez made California's first authenticated gold discovery on March 9, 1842"; and of the oak, "There's a chance it isn't the right tree," and "but that's another story." The SCVHistory.com timeline (/scvhistory/timeline.htm) gives the listing as March 6, 1935. The state's registration record is not in the archive.

**Editor's note, Not to be confused with (bottom):** The discoverer is Francisco Lopez (born 1802; person #18834), called Cusa or Cuso. He is not his kinsman Francisco "Chico" López (about 1820-1900; person #28132), whose portrait is often taken for the discoverer's and whose nickname Jerry Reynolds gives him (José Jesús López in Latta 1976; Warner 1876, "also known by the name of Cuso").

**Editor's note, The date in the sources (bottom):** Each as its source gives it. 1834: W.W. Jenkins, 1906, "In 1834 the placers of San Francisco, Placerita and Castiac and the San Feliciana, forty-five miles northwest from Los Angeles, were discovered" (/scvhistory/hssc1906jenkins.htm). 1838 or earlier: Francisco Garcia, Los Angeles Times, April 23, 1896, as Alan Pollack summarizes it (/scvhistory/pollack1115dream.htm). 1840: William Heath Davis, as Guinn reports him (/scvhistory/sw9501.htm). June 1841: J.J. Warner, 1876, and John Murray, 1892 (/scvhistory/overlandmonthly0592.htm). Fall 1841, gold already in Stearns's hands: I.L. Given, 1895 (/scvhistory/sw9501.htm). March 1842: Abel Stearns, 1867 (document #26983); Bancroft in the text of his History, "evidently following Stearns," and 1841 in his Pioneer Register, as Guinn reports. April 1842: Bandini, as Guinn reports. 1842: Antonio F. Coronel, as Guinn reports. The ninth of March [1842]: the petition of April 4, 1842 (Perkins, /scvhistory/signal/perkins/part03.html). March 9, 1842: Engelhardt, 1927, from Catalina Lopez; the plaque of 1930; Landmark No. 168. Guinn himself gave March 1842 in the San Francisco Call of September 8, 1895, and 1841 in the Historical Society's Annual the same year.

**Editor's note, The place in the sources (bottom):** The petition: "at the place of San Francisco, appertaining to the late Don Antonio del Valle, distant from his house about one league toward the south." Stearns: "a place called San Francisquito, about thirty-five miles north-west of this city." Warner: "upon, or near, the San Francisco Ranch, about forty-five miles westerly from Los Angeles city." Guinn, 1895: "the San Feliciano Canyon, in the county of Los Angeles"; "This canyon is about eight miles northwest of Newhall." Prudhomme (1922), from a note of Cyrus Lyon's, and Engelhardt (1927) make San Feliciano a second, later find; Leon Worden's note to Guinn suggests the 1841 accounts may describe a find at San Feliciano, not at Placerita. Belderrain, Perkins and the state place the 1842 find in Placerita Canyon.

**Editor's note, The mint deposit (bottom):** Robinson's memorandum of the mint's statement, as printed in 1885: deposited July 8, 1843; "Before melting, 18 34-100 oz.; after melting, 19 1-100 oz.; fineness, 926-1000; value, $344.75" (document #26983). Murray's printing of the same memorandum in 1892 gives "after melting, 18 1-100 oz." and Prudhomme's in 1922 gives "$244.75." The Treasury voucher found in 1891 gives June 8, 1843. The gold was deposited and assayed; that it was coined is not in these sources (Leon Worden's note to Belderrain 1930).

### recordDates

| Printed | ISO | Precision | What happened | Confirmed |
| --- | --- | --- | --- | --- |
| the 9th day of March last [1842] | 1842-03-09 | day | the find, as dated in the petition of April 4, 1842 | no |
| April 4, 1842 | 1842-04-04 | day | petition of Lopez, Cota and Bermudez to the governor, Santa Barbara | no |
| May 1 [1842] | 1842-05-01 | day | letter from California reporting the find, printed in the New York Observer, October 1, 1842 | no |
| May 3, 1842 | 1842-05-03 | day | Argüello appoints Ygnacio del Valle magistrate for the placer | no |
| May 11, 1842 | 1842-05-11 | day | Pliny F. Temple's letter: upwards of fifty men at work | no |
| November 22d, 1842 | 1842-11-22 | day | Stearns sends twenty ounces by Alfred Robinson to the Philadelphia mint | no |
| June 8th, 1843 | 1843-06-08 | day | deposit by Grant & Stone, per the Treasury voucher (1891) | no |
| the 8th day of July, 1843 | 1843-07-08 | day | deposit by Grant & Stone, per Robinson's copy of the mint memorandum | no |
| March 9, 1930 | 1930-03-09 | day | plaques placed; the oak named Oak of the Golden Dream | no |
| March 6 [1935] | 1935-03-06 | day | Landmark No. 168 listed, per the SCVHistory.com timeline (uncited) | no |

### Relations

- **eventPersons:** #18834 Francisco Lopez (notes 1, 2; the discoverer; first of the three petitioners)
- **eventPersons:** #309 Abel Stearns (notes 2, 7; handled the gold and shipped twenty ounces to the Philadelphia mint)
- **eventPersons:** #293 Ygnacio del Valle (notes 6; appointed magistrate for the placer, May 3, 1842)
- **eventPlaces:** #16446 Rancho San Francisco (notes 1, 6; the petition's "place of San Francisco, appertaining to the late Don Antonio del Valle")
- **eventPlaces:** #18565 Placerita Canyon (notes 3, 12, 17, 18; the probable site, and the site the 1930 plaques and Landmark No. 168 name; Stearns says San Francisquito. The place record is an empty stub.)
- **eventArticles:** #1424 3. The Placerita Gold Rush (named in note 1, 6, 17)
- **eventArticles:** #15294 California's REAL First Gold (named in note 1)
- **sourceDocuments:** #26983 Abel Stearns Tells of Lopez 1842 Gold Discovery; No Mention of Dream (note 2, note 3, note 7, editor note 2, editor note 4)
- **cited, not a document, not related:** #1424 articles "3. The Placerita Gold Rush"
- **cited, not a document, not related:** #15294 articles "California's REAL First Gold"
- **cited, not a document, not related:** #309 persons "Abel Stearns"
- **cited, not a document, not related:** #2963 photographs "New York Observer Report on Placerita Gold Discovery, 10-1-1842"
- **cited, not a document, not related:** #30537 sourceFaults "deposited the 8th day of July, 1843 (the memorandum as Robinson copied it) → 8 July or 8 June 1843: not decided"
- **cited, not a document, not related:** #4169 photographs "Tentative Program for 1930 Dedication"
- **cited, not a document, not related:** #18834 persons "Francisco Lopez"
- **cited, not a document, not related:** #28132 persons "Francisco "Chico" López"
- **photograph #2963 LW2181** takes the event in photoEvents (named in note 4; the first printed report of the find, from a letter of May 1, 1842)
- **photograph #4169 LW2575b** takes the event in photoEvents (named in note 14; Rivera's program for March 9, 1930)
- **Held, photograph #4171 LW2575c** "A. Rivera to Frank Walker Re: State Recognition, 1934": footnote 18 does not name it. Set `$CFG['linkHeldPhotos'] = true` to link the held photographs too.
- **Held, photograph #2989 LW2217** "Oak of the Golden Dream ~1963": no footnote cites it (the draft says so). Set `$CFG['linkHeldPhotos'] = true` to link the held photographs too.
- **Held, photograph #4731 LW2952** "1842/1948 Placerita Gold Discovery Medal (Bronze), 1948.": no footnote cites it (the draft says so). Set `$CFG['linkHeldPhotos'] = true` to link the held photographs too.
- **Held, photograph #3337 LW2352** "1842/1948 Placerita Gold Discovery Medal (Gilt)": no footnote cites it (the draft says so). Set `$CFG['linkHeldPhotos'] = true` to link the held photographs too.
- **Held, photograph #3339 LW2353** "1842/1948 Placerita Gold Discovery Medal (Cu/Br)": no footnote cites it (the draft says so). Set `$CFG['linkHeldPhotos'] = true` to link the held photographs too.
- **Held, photograph #3313 LW2338** "1968 Anillo Restrike of 1948 Placerita Gold Discovery Medal": no footnote cites it (the draft says so). Set `$CFG['linkHeldPhotos'] = true` to link the held photographs too.
- **Held, article #853** Chapter 16. Golden Dreams (Reynolds, 1998 edition) (about the event; not cited (Reynolds rule)): no footnote names it
- **Held, article #12154** The real story of California's first gold discovery (Leon Worden, 1996) (about the event; quotes Reynolds for the story; not cited): no footnote names it
- **Held, article #12490** New Study Will Nag SCV Historians (Leon Worden, 1996) (on claims of gold mined in the valley before 1842; not cited): no footnote names it
- **relatedEvents:** none
- **footnotesOn:** not set. On this site it means "the notes were published on another record" (create_saugus_2019_event_2026_10_05.php); the documents the draft listed there go to sourceDocuments instead.
- **Not written:** every other record. The draft's forNathan recommendations about other records (new place or organization records, changes to person records, image attachments) are not acted on.

### Research leads (researchLeads, not shown on the page)

- The petition of April 4, 1842: the archive holds only the English translation, as Perkins printed it and Leon Worden quoted it in 2005. Perkins puts the original 'in the archives at Sacramento'; Leon (2005, 2006) and the Lopez record #18834 put it 'in the National Archives in Washington, D.C.' Neither cites a call number. A scan of the Spanish original would confirm 'the 9th day of March last' and settle where it is.
- Ygnacio del Valle's report of June 1842 to Argüello (100 miners, then 50 for want of water) is given by Perkins in quotation marks but reads as a paraphrase; Perkins says the Argüello letter is in the Bancroft Library. Not used in the body.
- Pliny F. Temple's letter of May 11, 1842 is known here only through Pollack 2015, who gives no repository.
- Leon Worden's 2005 COINage column (#15294) and his 1996 Signal column (#12154) repeat Reynolds chapter 16 for the 2,000 Sonoran miners, Pedro Lopez's ride to Los Angeles, two hundred ounces by November and '125 pounds'; under the rule in docs/PROFILES.md they are one account, not corroboration. Reynolds also calls Argüello 'Governor'; Perkins, citing Bancroft, has him Prefect.
- The 1843 anniversary Mass at the site (Catalina Lopez's memory, through Belderrain, Prudhomme and Engelhardt, who names Fr. Blas Ordaz) is family tradition with no record of the time in the archive. Not in the body.
- The 1930 affidavits (AP9010 to AP9014: Belderrain's of February 23, 1930, Frank Walker's of March 1, 1930, and the speeches) were read only as Pollack quotes them. Not yet in Craft.
- The date Landmark No. 168 was registered (March 6, 1935) rests on the SCVHistory.com timeline, which cites nothing; the state's own record is not in the archive. Rivera's letter of April 9, 1934 (LW2575c) shows the application under way.
- Guy J. Giffen (HSSC Quarterly, March 1948), as Pollack 2015 quotes him, describes a Philadelphia mint record of January 30, 1838, of California placer gold deposited by Hussey and Mackay, origin undetermined. It bears on any claim that the 1842 find was the first gold from California, and is why the body attributes 'first documented' to Worden and Pollack rather than stating it.
- Francisco Garcia's 1896 Los Angeles Times interview (/scvhistory/lat042396.htm) was read only in Pollack's summary for this draft.
- Guinn's 1895 page carries Leon Worden's note that the essay's date is misgiven (October 8 for September 8). The Annual's printed text was not checked against a scan.
- A.B. Perkins, 'Rancho San Francisco: A Study of a California Land Grant' (1957; article #1434, document #27374) was not read for its gold passages.

## From the draft's forNathan (approved with the draft; listed for the record)

- 1. The day. The ninth of March is in the petition itself (April 4, 1842: 'the 9th day of March last'), as Perkins printed it and Leon quoted it in 2005, and in Engelhardt's 1927 account from the family. So the day did not originate in 1930; what 1930 added was the dream, the named tree, and the state's adoption of the date (Leon's note on belderrain1930). Stearns gives March 1842 with no day. Recommend eventDate 'March 9, 1842', eventDateEdtf 1842-03-09, and startEvidence 'contemporary', since the claim rests on a document written within four weeks (the events inventory proposed 'retrospective' and 'March 1842').
- 2. Guinn. He did argue for March 1842, in the San Francisco Call of September 8, 1895, but the same year, after I.L. Given's letter, he wrote in the Historical Society's Annual that 1841 was 'the correct date.' The body gives both.
- 3. The mint deposit. Stearns's record #309 is not wrong: it follows Robinson's copy of the mint memorandum (July 8, 1843). The Treasury voucher found in 1891 (in Murray 1892, and Pollack 2015) says June 8, 1843. Source fault #30537 already holds it as 'not decided'; the body states both dates. No change to #309 recommended.
- 4. The oak. Stearns #309 says the dream and 'any particular oak' both 'enter the story in 1930', following Leon's note on the Sentinel page. The dream and the named tree are 1930, but an oak is already in Prudhomme's 1922 account from the same informants ('their siesta under the shade of the oak tree'), and Belderrain's 1930 text has a siesta, not a dream: the word 'dream' is in Rivera's name for the tree and Perkins's speech. Recommend changing #309's sentence to: 'The dream enters the story in 1930, with the naming of a particular tree; an oak is in the family's telling by 1922.'
- 5. Francisco Lopez #18834 says 'Some two thousand miners, most from Sonora, worked the canyon,' and that he rode with his brother Pedro to Stearns, citing Leon's 2005 column, which repeats Reynolds chapter 16 on both. Under the Reynolds rule and the Leon-independence rule these are uncorroborated: recommend attributing them to Reynolds or removing them. It also says the petition 'is in the National Archives'; Perkins says Sacramento. Recommend 'the original is variously placed at Sacramento and in the National Archives.'
- 6. Create a place record for the Oak of the Golden Dream (California Historical Landmark No. 168). DATA-ORGANIZATION.md makes a landmark a Place, none exists, and it would carry the 1930 dedication, the 1935 listing and the legend. Then link it in eventPlaces. Placerita Canyon #18565 is an empty stub and wants a body.
- 7. Images. None of the eight photographs has an image attached. LW2217 (asset 14123), LW2575b and c, and the medals have files in archiveMedia; LW2181 (the New York Observer) has none. Recommend attaching 14123 to #2989 and using it as featuredImage. No band image.
- 8. Not linked, deliberately: Francisco 'Chico' López #28132 (a different man; see the editor note); A.B. Perkins #333 (historian and 1930 speaker, linked as the author of #1424); Antonio del Valle #291 (named in the petition only as the late owner of the land); Rancho San Francisco organization #382 (duplicates place #16446 and carries an unsourced body); Placerita Canyon Nature Center #18799. No records exist for Manuel Cota, Domingo Bermudez, Alfred Robinson, Adolfo G. Rivera, Frank E. Walker, Francisca Lopez de Belderrain, Ramona Parlor No. 109 or La Mesa Club.
- 9. eventChlNumber 168 is the Oak's landmark number; its description is of the discovery, so the draft sets it on the event. Say if it should live only on the Oak's place record.
- 10. Spelling: the body writes Argüello and Ensueno as the sources print them (Perkins 'S. Arguello'; Belderrain 'Ensueno'). Say if the diacritics should be normalised.
