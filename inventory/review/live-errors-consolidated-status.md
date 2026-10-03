# The 69 live errors: where each stands in the archive

Built 3 October 2026 by `scripts/import/build_live_errors_consolidated.php` from Grok's `live-errors-consolidated.json`. Read only. The class, the records and the 2 October notes are found in the database; the action is a decision, with its reason. The live site is Leon Worden's and is not edited; authors' text in Craft is not rewritten, so an error in it gets a dated, sourced Correction note (`fix_live_errors_consolidated.php`).

| class | count |
| --- | --- |
| IN CRAFT | 45 |
| RECORD, NOT FOUND | 5 |
| LEGACY ONLY | 18 |
| ARCHIVE DATA | 1 |

| action | count |
| --- | --- |
| CORRECTION NOTE | 15 |
| FIELD FIX | 2 |
| ALREADY DONE | 17 |
| HOLD | 8 |
| NONE | 27 |

17 entries were already corrected, in whole or in part, by `fix_live_errors_in_craft.php` on 2 October: CE20, CE21, CE22, CE26, CE27, CE29, CE30, CE46, CE47, CE48, CE53, CE55, CE56, CE57, CE58, CE64, CE65.

## HOLD

- **CE12** (medium (escrow and access NEEDS_VERIFICATION)): Leon's dated 2005 column (#12166) was right when written; the change is the City's purchase approved 28 October 2025, and close of escrow and today's access rules are NEEDS_VERIFICATION. An update note waits on those. Not an error of fact; hb1806 is legacy only.
- **CE38** (high): Not in Craft (#913 Tataviam does not carry the label); legacy ridge.htm and tataviam.htm. The fix (1851) is harmless to pass to Leon, but anything on the Tataviam pages migrates under TATAVIAM_AUDIT.md.
- **CE41** (medium): Perkins 1947 (#869). The correction (July 1876) rests on Perkins's own 1954 sequence and Reynolds part 39, which under the Perkins rule are one source, and the day is NEEDS_VERIFICATION: it needs a source independent of both (a railroad report or a newspaper of 1876).
- **CE44** (medium-high (statute text NEEDS_VERIFICATION)): Perkins part 4 (#1426), three claims, MEDIUM-HIGH: the right of way (two miles, statute text NEEDS_VERIFICATION), the franchise's end (1883-84, from Ripley), and the superintendency (Superintendent for California, appointed 3 March 1853). The archive disagrees with itself on the last: its own Beale profile (#327) says "California and Nevada from 1853" and the Tejon Ranch timeline says 1852. Settle #327 first.
- **CE52** (high): Reynolds part 28 (#2081). The LA Star of 4 April 1863, as Perkins transcribes it (#1426), estimates "the additional work" at $16,000 to $18,000; Reynolds's $5,000 is what Beale "got" from the supervisors. The two figures may measure different things, so this is not yet a contradiction: it needs the Board's minutes. The archive's own prose repeats the $5,000 on #327 (Beale) and #932 (Beale's Cut).
- **CE59** (high): Reynolds part 54 (#2133), in part. The burial is noted: Leon's "Requiem to a Little Soldier" (2003, from the Newhall Signal of 29 March and 5 April 1928) has the boy buried at Oakwood Cemetery, Chatsworth, and calls the cowboy outfit "an oft-repeated local legend that hasn't been refuted" (quoted, not called false). HOLD the morgue: Leon puts it in "the Masonic lodge in Newhall, normally a happy and popular dance hall", which may be the same building as the Hap-A-Lan; not noted until that is settled.
- **CE61** (high): Reynolds's "Ethnography of Castaic" (#2175): ethnography, under TATAVIAM_AUDIT.md. The "date?" placeholder did not come into Craft; originalPublishDate is empty.
- **CE62** (CARE flag (not a factual error)): Bowers Cave (#2177): tribal consultation before anything else; the record is disabled (disable_bowers_cave.php). Not a factual error.
- **CE63** (high (title); medium (date)): Craft's title is already right ("E.F. Beale and the Beasts of Tejon"). The byline date, April 24, 2002, is in Leon's body; the file name lw011399 and the "1999 calendar ... available now" fit 13 January 1999, MEDIUM. Also: #12168's originalPublishDate holds a paragraph of the column's text, not a date: a field fix waiting on the right date (Nathan).

## For Leon: errors on the live site with nothing to do in Craft

Legacy only, a title tag or index label, or a slip kept as printed in Craft. Nathan passes these to Leon; the archive does not edit his site.

- **CE02** /scvhistory/ap0828.htm: link text 'Oustanding Citizen 1964' -> 'Outstanding Citizen 1964'
- **CE03** /scvhistory/bealeafb.htm: "In 1870 he bought the Decatur House" -> 1871
- **CE04** /scvhistory/bealescut.htm: Entry "Beale's Cut in John Ford's 'The Iron Horse,' 1924" labeled LW2159, linking https://scvhistory.com/scvhistory/lw2228.htm -> The label should be LW2228 (LW2159 is the Tom Mix item, listed separately above it).
- **CE05** /scvhistory/cp1702.htm: construction views dated "1925-1926" and "circa 1926" -> Late 1926–1927. Hart was only 'contemplating building' on July 12, 1926.
- **CE06** /scvhistory/cp1703.htm: "Newly Completed Hart Mansion, 2 Views, 1930" -> Completed about 1927–28. Either the photo date or 'newly completed' is wrong.
- **CE07** /scvhistory/di5001.htm: <title> 'SCVHistory.com DO5001 | Newhall | Arthur B. Perkins at Work ...' -> Probably 'DI5001' (NEEDS_VERIFICATION: check the catalog prefix)
- **CE08** /scvhistory/fh2701.htm: credit line "FH2101" -> FH2701 (FH2101 is a different item, a lantern slide).
- **CE10** /scvhistory/fh2701.htm: "Completed in 1927", on a page whose photo is "under construction, October(?) 1927" -> Completed late 1927 or early 1928. The page contradicts itself.
- **CE11** /scvhistory/film.htm: "LW2341 - ... Pinto Ben on Rudy Vallee's Radio Show, 12-13-1934" links lw2341.htm -> It should be LW2342 / lw2342.htm (lw2341 is the 1928 Victor record, which is also listed separately, so LW2341 appears twice).
- **CE13** /scvhistory/hl7101.htm: "back in 1858, Ygnacio's stepfather borrowed $8,500" -> José Salazar was the second husband of Ygnacio's stepmother, Jacoba Feliz, not his stepfather. Suggest: 'his stepmother's husband, José Salazar'.
- **CE14** /scvhistory/hs9032.htm: 'Reynolds' conculsion' -> 'conclusion'
- **CE15** /scvhistory/lastar18640514wolfskill.htm: "They retained the westernmost 1,350 acres, which became the separate Rancho Camulos when Henry Newhall bought the 46,460 acres that remained of the old Rancho  -> Newhall's Jan 15, 1875 purchase ($90,000) was not a sheriff's sale. The 1873 sheriff's sale went to Fernald & Richards; a second sheriff's sale was called off when a private buyer (Newhall) was found.
- **CE16** /scvhistory/lo8801.htm: "The Army began to shut down the fort when the U.S. Civil War broke out and closed it, and the reservation, for good in 1854." -> 1864 (the fort was abandoned Sept 11, 1864, and the Indians were removed to Tule River in 1864).
- **CE17** /scvhistory/lp_oaklandtrib110418.htm: "Prior to the Great Drought of 1864-65, he owned the entire Rancho San Francsico" -> Before the drought of 1862–64, Ygnacio held an undivided share (about 5/11) alongside his stepmother, her husband and his half-siblings. Fix the typo 'Francsico'.
- **CE28** /scvhistory/lw3629.htm: titled 3-28-1928 -> November 3, 1928
- **CE31** /scvhistory/mu8491.htm: "1930 or earlier" -> About 1934–37: the page cites a Purdue print stamped 04-11-1934, and Hedda Hopper (1941) placed the photo a week before Earhart's 1937 flight.
- **CE32** /scvhistory/perkins-desmond.htm: 'American Carrera Marble Co.' / 'A town site, Carrera' / 'Hotel Carrera'; 'they arrived in Newhall in 1918' -> Carrara (town; company name probably 'American Carrara Marble Co.', NEEDS_VERIFICATION). Add a note that other site sources give 1919 for the arrival.
- **CE33** /scvhistory/perkins-fremont.htm: By A.B. Perkins | Date unkonwn. -> 'Date unknown.'
- **CE36** /scvhistory/perkins-rsf-1957.htm: [editor's note at 80] 'Sanford Lyon was bured in the family plot' -> 'buried'
- **CE39** /scvhistory/sauguscafe.htm: 'Narragansut'; 'limted'; 'plausable' -> 'Narragansett'; 'limited'; 'plausible'
- **CE40** /scvhistory/sg090199a.htm: "Antonio del Valle, a Mexican-born missionary who owned 48,000 acres" -> Antonio was a soldier (lieutenant, San Blas Company) and later the civil administrator (mayordomo) of Mission San Fernando, not a missionary. Add an editor's note. The article also names a living priv
- **CE42** /scvhistory/signal/perkins/notes.html: note 8 'Lopez was uncle to Jacopa Feliz'; note 28 'ownership was transfered' -> 'Jacoba Feliz'; 'transferred'
- **CE43** /scvhistory/signal/perkins/part01.html: 'senior ethnologist, Bureau of American Ethnology, Smithsonlan Institution' -> 'Smithsonian'
- **CE54** /scvhistory/signal/reynolds/part41.html: <title> '... | 46: Surrey' -> '41: Baron of Casteca'
- **CE60** /scvhistory/signal/reynolds/part58.html: '*See note below' plus a note saying 'The section at the top in italics is inaccurate', but the top section now reads $6,000 (the corrected figure) -> Reword the note to say the text has been corrected from Reynolds' $11,000 / Dec 17, 1925, and add the 1922 date to the text
- **CE67** /scvhistory/tejonranchtimeline.htm: "1893 — Beale dies at the age of 72." -> 71 (Feb 4, 1822 to Apr 22, 1893).
- **CE68** /scvhistory/times111101.htm: "When Del Valle died in 1880, at age 72"; "He died in 1880, at the age of seventy-two years" -> 71 (born July 1, 1808; died March 30, 1880). Add [sic] notes; the originals should not be altered.

## Every entry

### CE01: IN CRAFT / CORRECTION NOTE

- **Page:** https://scvhistory.com/scvhistory/ap0828.htm
- **Error:** Perkins published a manuscript on the history of the Santa Clarita Valley in 1957. In 1962 he wrote a series of articles for The Signal newspaper, appropriately titled "Story of our Valley."
- **Correction:** In 1957 the HSSC Quarterly published his 'Rancho San Francisco: A Study of a California Land Grant' (June 1957). 'The Story of Our Valley' ran in The Signal in 1954–55 (an earlier version ran in 1946–47). (high)
- **Page records:** #27368 photographs "Arthur B. Perkins, Newhall Water Co."
- **Text in:** #3003 photographs "Arthur B. Perkins, Oustanding Citizen 1964." [body]
- **Text in:** #27368 photographs "Arthur B. Perkins, Newhall Water Co." [body]
- **Action:** CORRECTION NOTE. Leon's caption, carried on #27368 (AP0828) and #3003 (LW2232): a note that "The Story of Our Valley" ran in The Signal from April 1954 to January 1955, from the series' own introduction (#1418) and the Los Angeles Times profile of 1977 (cited on #333). The series date rests on the series itself, not on Perkins's word.

### CE02: IN CRAFT / NONE

- **Page:** https://scvhistory.com/scvhistory/ap0828.htm (also /scvhistory/lw3113.htm, /scvhistory/lw3550.htm)
- **Error:** link text 'Oustanding Citizen 1964'
- **Correction:** 'Outstanding Citizen 1964' (high)
- **Page records:** #27368 photographs "Arthur B. Perkins, Newhall Water Co."; #4931 photographs "Perkins-Lamkin SCV History Images Come Home, 9-17-"; #5399 photographs "Who Knew? Perkins' SCV History Books Still Availab"
- **Text in:** #3003 photographs "Arthur B. Perkins, Oustanding Citizen 1964." [title, body]
- **Text in:** #4931 photographs "Perkins-Lamkin SCV History Images Come Home, 9-17-" [body]
- **Text in:** #5399 photographs "Who Knew? Perkins' SCV History Books Still Availab" [body]
- **Action:** NONE. "Oustanding" in #3003, #4931 and #5399 is Leon's sidebar label text carried in the body, not a link label the archive rebuilt: kept as printed. #3003's title is corrected under CE23. for Leon: the live pages ap0828, lw3113, lw3550.

### CE03: LEGACY ONLY / NONE

- **Page:** https://scvhistory.com/scvhistory/bealeafb.htm
- **Error:** "In 1870 he bought the Decatur House"
- **Correction:** 1871 (high)
- **Action:** NONE. Legacy only (bealeafb, Beale AFB text). for Leon: the live page: an editor's note, 1871.

### CE04: RECORD, NOT FOUND / NONE

- **Page:** https://scvhistory.com/scvhistory/bealescut.htm
- **Error:** Entry "Beale's Cut in John Ford's 'The Iron Horse,' 1924" labeled LW2159, linking https://scvhistory.com/scvhistory/lw2228.htm
- **Correction:** The label should be LW2228 (LW2159 is the Tom Mix item, listed separately above it). (high)
- **Page records:** #932 places "Beale's Cut Stagecoach Pass"
- **Action:** NONE. The mislabelled link is on the legacy Beale's Cut index only; #932 cites LW2159 for the Tom Mix item, correctly. for Leon: the live page.

### CE05: LEGACY ONLY / NONE

- **Page:** https://scvhistory.com/scvhistory/cp1702.htm (also /scvhistory/mu9067.htm)
- **Error:** construction views dated "1925-1926" and "circa 1926"
- **Correction:** Late 1926–1927. Hart was only 'contemplating building' on July 12, 1926. (medium-high)
- **Action:** NONE. Legacy only (cp1702, mu9067: NHMLA dates). for Leon: the live page.

### CE06: LEGACY ONLY / NONE

- **Page:** https://scvhistory.com/scvhistory/cp1703.htm
- **Error:** "Newly Completed Hart Mansion, 2 Views, 1930"
- **Correction:** Completed about 1927–28. Either the photo date or 'newly completed' is wrong. (medium)
- **Action:** NONE. Legacy only (cp1703). Medium. for Leon: the live page.

### CE07: LEGACY ONLY / NONE

- **Page:** https://scvhistory.com/scvhistory/di5001.htm
- **Error:** <title> 'SCVHistory.com DO5001 | Newhall | Arthur B. Perkins at Work ...'
- **Correction:** Probably 'DI5001' (NEEDS_VERIFICATION: check the catalog prefix) (low)
- **Action:** NONE. Legacy title tag only (di5001); low, the code NEEDS_VERIFICATION. for Leon: the live page.

### CE08: LEGACY ONLY / NONE

- **Page:** https://scvhistory.com/scvhistory/fh2701.htm
- **Error:** credit line "FH2101"
- **Correction:** FH2701 (FH2101 is a different item, a lantern slide). (high)
- **Action:** NONE. Legacy only (fh2701 credit line). for Leon: the live page.

### CE09: IN CRAFT / CORRECTION NOTE

- **Page:** https://scvhistory.com/scvhistory/fh2701.htm
- **Error:** "purchased the 254-acre Horseshoe Ranch in Newhall in 1921 from Babcock Smith"
- **Correction:** Leased in 1918. The first purchase in Feb 1921 was a few lots; the ranch was assembled in about 10 deeds through Oct 1933. (high)
- **Text in:** #2131 articles "53. Two-Gun Bill" [body, recordDates]
- **Action:** CORRECTION NOTE. The error page (fh2701) is legacy only, but the same claim is in Reynolds's chapter 53 (#2131): a note from Tom Sitton's 1989 survey of the deeds (MU8901), which the archive's Hart profile (#16356) already follows. fh2701 itself for Leon: the live page.

### CE10: LEGACY ONLY / NONE

- **Page:** https://scvhistory.com/scvhistory/fh2701.htm
- **Error:** "Completed in 1927", on a page whose photo is "under construction, October(?) 1927"
- **Correction:** Completed late 1927 or early 1928. The page contradicts itself. (medium)
- **Action:** NONE. Legacy only (fh2701). Medium. for Leon: the live page.

### CE11: LEGACY ONLY / NONE

- **Page:** https://scvhistory.com/scvhistory/film.htm
- **Error:** "LW2341 - ... Pinto Ben on Rudy Vallee's Radio Show, 12-13-1934" links lw2341.htm
- **Correction:** It should be LW2342 / lw2342.htm (lw2341 is the 1928 Victor record, which is also listed separately, so LW2341 appears twice). (high)
- **Action:** NONE. Legacy only (film.htm index link). for Leon: the live page.

### CE12: IN CRAFT / HOLD

- **Page:** https://scvhistory.com/scvhistory/hb1806.htm (also /scvhistory/signal/worden/lw032905.htm)
- **Error:** "As this is added to the archive in 2018, Beale's Cut is located on private property... Trespassing is prohibited." / "It is private property and you would be trespassing."
- **Correction:** Ownership has changed. On Oct 28, 2025 the Santa Clarita City Council approved buying the approximately 13-acre Forum Engineering Property, which contains Beale's Cut, adding it to the city's Open Space Preservation District. Current access rules: NEEDS_VERIFICATION. Close of escrow: NEEDS_VERIFICATION. (medium (escrow and access NEEDS_VERIFICATION))
- **Page records:** #12166 articles "A Third-Grade History Cheat Sheet ** 2005 UPDATE *"
- **Text in:** #12166 articles "A Third-Grade History Cheat Sheet ** 2005 UPDATE *" [body]
- **Action:** HOLD. Leon's dated 2005 column (#12166) was right when written; the change is the City's purchase approved 28 October 2025, and close of escrow and today's access rules are NEEDS_VERIFICATION. An update note waits on those. Not an error of fact; hb1806 is legacy only.

### CE13: LEGACY ONLY / NONE

- **Page:** https://scvhistory.com/scvhistory/hl7101.htm
- **Error:** "back in 1858, Ygnacio's stepfather borrowed $8,500"
- **Correction:** José Salazar was the second husband of Ygnacio's stepmother, Jacoba Feliz, not his stepfather. Suggest: 'his stepmother's husband, José Salazar'. (high)
- **Action:** NONE. Legacy only (hl7101). for Leon: the live page.

### CE14: LEGACY ONLY / NONE

- **Page:** https://scvhistory.com/scvhistory/hs9032.htm
- **Error:** 'Reynolds' conculsion'
- **Correction:** 'conclusion' (high)
- **Action:** NONE. Legacy only (hs9032 webmaster's note typo). for Leon: the live page.

### CE15: LEGACY ONLY / NONE

- **Page:** https://scvhistory.com/scvhistory/lastar18640514wolfskill.htm
- **Error:** "They retained the westernmost 1,350 acres, which became the separate Rancho Camulos when Henry Newhall bought the 46,460 acres that remained of the old Rancho San Francisco at a sheriff's sale in 1875"
- **Correction:** Newhall's Jan 15, 1875 purchase ($90,000) was not a sheriff's sale. The 1873 sheriff's sale went to Fernald & Richards; a second sheriff's sale was called off when a private buyer (Newhall) was found. (high (acreage NEEDS_VERIFICATION))
- **Action:** NONE. Legacy only (lastar18640514wolfskill webmaster's note); the 46,460 acres NEEDS_VERIFICATION. for Leon: the live page.

### CE16: LEGACY ONLY / NONE

- **Page:** https://scvhistory.com/scvhistory/lo8801.htm (also /scvhistory/lo8802.htm)
- **Error:** "The Army began to shut down the fort when the U.S. Civil War broke out and closed it, and the reservation, for good in 1854."
- **Correction:** 1864 (the fort was abandoned Sept 11, 1864, and the Indians were removed to Tule River in 1864). (high)
- **Action:** NONE. Legacy only (lo8801, lo8802 captions). for Leon: the live page.

### CE17: LEGACY ONLY / NONE

- **Page:** https://scvhistory.com/scvhistory/lp_oaklandtrib110418.htm
- **Error:** "Prior to the Great Drought of 1864-65, he owned the entire Rancho San Francsico"
- **Correction:** Before the drought of 1862–64, Ygnacio held an undivided share (about 5/11) alongside his stepmother, her husband and his half-siblings. Fix the typo 'Francsico'. (high (typo); medium (drought dates))
- **Action:** NONE. Legacy only (lp_oaklandtrib110418 webmaster's note). The "Francsico" in the Lang photo captions in Craft is a different sentence. for Leon: the live page.

### CE18: IN CRAFT / CORRECTION NOTE

- **Page:** https://scvhistory.com/scvhistory/lw2052.htm (also /scvhistory/signal/reynolds/part15.html)
- **Error:** "a judge awarded 13,599 acres to Ygnacio, 21,307 acres to Jacoba, and 4,684 acres to each of Jacoba's six children"
- **Correction:** The three figures add up to 62,010 acres, about 13,400 more than the whole rancho (48,611.88 acres patented). They cannot all be right. Add an editor's note, or replace them with the documented sequence: undivided shares 1841–1870, then the 1870 partition that gave Ygnacio Camulos (about 1,340 acres). reynolds part29 gives yet another figure, 'original grant of 16,599 acres'. (high)
- **Page records:** #293 persons "Ygnacio del Valle"; #851 articles "Chapter 15. Family Squabbles"
- **Text in:** #293 persons "Ygnacio del Valle" [body] authorship legacy-leon
- **Text in:** #851 articles "Chapter 15. Family Squabbles" [body]
- **Text in:** #915 groups "del Valle Family" [body]
- **Text in:** #16446 places "Rancho San Francisco" [body]
- **Action:** CORRECTION NOTE. Leon's text on #293 and Reynolds's chapter 15 (#851): a note that the three figures exceed the patented rancho (the 1875 patent, 48,611.88 acres, cited on #291) and that the heirs held undivided shares until 1870 (Perkins citing the partition decree). The archive's OWN prose repeats the figures as fact on #915 (del Valle Family) and #16446 (Rancho San Francisco): that is archive text, to be rewritten rather than noted, and is not done here (Nathan).

### CE19: IN CRAFT / CORRECTION NOTE

- **Page:** https://scvhistory.com/scvhistory/lw2052.htm
- **Error:** "Instead, Ygnacio lived at Los Angeles, where he was mayor. Acutally, Ygnacio was a California state legislator, too"
- **Correction:** 'where he was alcalde (1850) and later a city councilman'. Fix the typo 'Acutally'. The page's own office list shows only City Council terms (May 4, 1852 – May 3, 1853; May 7 – Dec 15, 1856). (high)
- **Page records:** #293 persons "Ygnacio del Valle"
- **Text in:** #293 persons "Ygnacio del Valle" [body] authorship legacy-leon
- **Action:** CORRECTION NOTE. Leon's text on #293: alcalde in 1850 (Pen Pictures 1889) and city council 1852 and 1856 (the City of Los Angeles record of his offices on LW2052). "Acutally" is a spelling slip, kept as printed.

### CE20: IN CRAFT / CORRECTION NOTE

- **Page:** https://scvhistory.com/scvhistory/lw2052.htm (also /scvhistory/lw2532.htm, /scvhistory/lw3768.htm)
- **Error:** lw2052: "When statehood came in 1850, Ygnacio served a short stint in the first Legislature"; lw2532/lw3768: "after serving in the state Legislature at statehood in 1850"
- **Correction:** He was elected on Sept 3, 1851 (2nd Assembly District) and sat in the 1852 session (Jan 5, 1852 – Jan 3, 1853), not the first Legislature of 1849–50. (high (Assembly Journal NEEDS_VERIFICATION))
- **Page records:** #293 persons "Ygnacio del Valle"; #5617 photographs "Envelope Mailed from R.F. del Valle to Ysabel del "
- **Text in:** #293 persons "Ygnacio del Valle" [body, recordDates] authorship legacy-leon **corrected 2 Oct (D3)**
- **Text in:** #5617 photographs "Envelope Mailed from R.F. del Valle to Ysabel del " [body]
- **Action:** CORRECTION NOTE. #293 was corrected on 2 October (D3). The same claim is in Leon's caption on #5617 (LW3768), which 2 October missed: the D3 note goes there too. lw2532 is legacy only, for Leon: the live page.

### CE21: IN CRAFT / ALREADY DONE

- **Page:** https://scvhistory.com/scvhistory/lw2068.htm (also /scvhistory/fh2701.htm)
- **Error:** say LA County Parks and NHMLA operate the park and museum; lw2068: NHMLA "operates the Hart Museum"
- **Correction:** Since July 14, 2025, the City of Santa Clarita owns and operates Hart Park and the Hart Museum. NHM's P-75 and P-98 photo collections were transferred to the City. GC 1012 and GC 1192 are still listed at the Seaver Center. (high)
- **Page records:** #2759 photographs "Hart & Robert Taylor with Billy The Kid Gun"
- **Text in:** #2759 photographs "Hart & Robert Taylor with Billy The Kid Gun" [body] **corrected 2 Oct (L1)**
- **Action:** ALREADY DONE. #2759 corrected on 2 October (L1). fh2701 and the NHMLA boilerplate captions are legacy only, for Leon: the live page.

### CE22: IN CRAFT / CORRECTION NOTE

- **Page:** https://scvhistory.com/scvhistory/lw2208.htm (also /scvhistory/lw3451.htm, /scvhistory/lw3647.htm, /scvhistory/lw2161.htm)
- **Error:** "from Fort Defiance, Texas" / "between Ft. Defiance, in Texas, and the Colorado River"
- **Correction:** Fort Defiance, New Mexico Territory (today Arizona). (high)
- **Page records:** #2983 photographs "Map of Beale's Camel Expedition</"; #5285 photographs "Beale's U.S. Camel Corps: Hi Jolly's Tomb & Cemete"; #5491 photographs "Guide to Hi Jolly Pioneer Cemetery, Quartzsite, Ar"
- **Text in:** #2941 photographs "Hi Jolly's Tomb" [body] **corrected 2 Oct (B6)**
- **Text in:** #2943 photographs "Hi Jolly's Tomb" [body] **corrected 2 Oct (B6)**
- **Text in:** #2945 photographs "Hi Jolly's Tomb" [body] **corrected 2 Oct (B6)**
- **Text in:** #2947 photographs "Hi Jolly's Tomb" [body] **corrected 2 Oct (B6)**
- **Text in:** #2949 photographs "Hi Jolly's Tomb." [body] **corrected 2 Oct (B6)**
- **Text in:** #5285 photographs "Beale's U.S. Camel Corps: Hi Jolly's Tomb & Cemete" [body] **corrected 2 Oct (B6)**
- **Text in:** #5491 photographs "Guide to Hi Jolly Pioneer Cemetery, Quartzsite, Ar" [body] **corrected 2 Oct (B6)**
- **Text in:** #2983 photographs "Map of Beale's Camel Expedition</" [body]
- **Action:** CORRECTION NOTE. Seven records corrected on 2 October (B6). Leon's map caption on #2983 (LW2208) reads "Fort Defiance, Texas", a wording 2 October did not match: the B6 note goes there too. lw2161 is legacy only.

### CE23: IN CRAFT / FIELD FIX

- **Page:** https://scvhistory.com/scvhistory/lw2232.htm
- **Error:** <title> 'SCVHistory.com AP0828 | People | Arthur B. Perkins, Oustanding Citizen 1964.'; heading 'Oustanding Citizen 1964'; body repeats the ap0828 '1957 manuscript' / '1962' sentences
- **Correction:** Title 'SCVHistory.com LW2232 | ...'; 'Outstanding'; fix the two sentences as in PL1 (high)
- **Page records:** #333 persons "Arthur Buckingham Perkins"; #3003 photographs "Arthur B. Perkins, Oustanding Citizen 1964."
- **Text in:** #3003 photographs "Arthur B. Perkins, Oustanding Citizen 1964." [title, body, photoSourceCode]
- **Text in:** #4931 photographs "Perkins-Lamkin SCV History Images Come Home, 9-17-" [body]
- **Text in:** #5399 photographs "Who Knew? Perkins' SCV History Books Still Availab" [body]
- **Text in:** #27368 photographs "Arthur B. Perkins, Newhall Water Co." [body]
- **Data:** #3003 photoSourceCode = "AP0828" (wrong)
- **Action:** FIELD FIX. #3003 (lw2232): photoSourceCode AP0828 -> LW2232. The code was read from the legacy title tag, which carries the wrong code; the page's own image is lw2232.jpg and its credit line reads LW2232, and the code is what the record's image is found by (AP0828 finds the other photograph). Its title "Oustanding" is Leon's title tag: corrected by note, with the 1962 sentence (CE01).

### CE24: IN CRAFT / ALREADY DONE

- **Page:** https://scvhistory.com/scvhistory/lw2298a.htm (also /scvhistory/lat19360719hart.htm)
- **Error:** thumbnail hs9909t.jpg "Letter: Gift of Buffalo Coat to Rudy Vallee 1936" links lw3383.htm
- **Correction:** It should link hs9909.htm (lw3383 is the Earhart letter). (high)
- **Page records:** #3169 photographs "Hart & Nurse at Brown Derby, 1938"
- **Text in:** #3169 photographs "Hart & Nurse at Brown Derby, 1938" [body]
- **Text in:** #3171 photographs "Hart & Nurse at Brown Derby, 1938" [body]
- **Text in:** #4351 photographs "Hart with Actress Lina Basquette, 1932." [body]
- **Text in:** #4695 photographs "Hart with Freddie Bartholomew, 1935." [body]
- **Text in:** #4833 photographs "Two-Gun Bill Hart at Home in Newhall, 1-7-1946." [body]
- **Text in:** #4891 photographs "William S. Hart at the Races? Watson Photo 1920s-1" [body]
- **Text in:** #4989 photographs "Hart and his Equine Friends, 1940s." [body]
- **Text in:** #5041 photographs "Hart Describes Gun Work for Latin American Radio A" [body]
- **Text in:** #5225 photographs "Letter from William S. Hart to Amelia Earhart re: " [body]
- **Text in:** #5227 photographs "Letter from William S. Hart to Amelia Earhart re: " [body]
- **Text in:** #5251 photographs "William S. Hart, Ione Reed, George Putnam (Amelia " [body]
- **Text in:** #5333 photographs "Yesterday's Headliner: ''Smiling'' Bill Hart, 1936" [body]
- **Action:** ALREADY DONE. Settled on 2 October (L2): the label survives in Craft only as text from the legacy sidebar, with no link. The wrong link for Leon: the live pages lw2298a, lat19360719hart.

### CE25: IN CRAFT / FIELD FIX

- **Page:** https://scvhistory.com/scvhistory/lw2311m.htm
- **Error:** <title> 'SCVHistory.com LW2311l | Saugus Speedway | Baker Ranch Rodeo Program 4-11-1926 (Page 13 of 26)'
- **Correction:** 'LW2311m' (high)
- **Page records:** #3215 photographs "Baker Ranch Rodeo Program 4-11-1926 (Page 13 of 26"
- **Text in:** #3215 photographs "Baker Ranch Rodeo Program 4-11-1926 (Page 13 of 26" [photoSourceCode]
- **Data:** #3215 photoSourceCode = "LW2311l" (wrong)
- **Action:** FIELD FIX. #3215 (lw2311m, page 13): photoSourceCode LW2311l -> LW2311m. The code came from the legacy title tag, which repeats page 12's code; LW2311l finds page 12's image (#3213 is page 12). lw2311m.jpg is not yet in the volume, so the plate will say so, which is true. The title tag for Leon: the live page.

### CE26: IN CRAFT / ALREADY DONE

- **Page:** https://scvhistory.com/scvhistory/lw2752.htm
- **Error:** "Upon Antonio's death two years later, the ranch was divided among his widow and children. Son Ygnacio ended up with the western portion"
- **Correction:** The heirs held undivided shares from 1841. Title was confirmed in 1855–57, and the rancho was formally partitioned only in 1870, when Ygnacio's share was set off as Camulos. (high)
- **Page records:** #4461 photographs "Ygnacio del Valle Family Tree."
- **Text in:** #4461 photographs "Ygnacio del Valle Family Tree." [body] **corrected 2 Oct (D4)**
- **Action:** ALREADY DONE. #4461 corrected on 2 October (D4).

### CE27: IN CRAFT / ALREADY DONE

- **Page:** https://scvhistory.com/scvhistory/lw3486.htm
- **Error:** cutline "Sunday, July 11"
- **Correction:** Published Sunday, July 12, 1936 (the page itself notes the cutline error). Make sure the title/date fields use 7-12-1936. (high (page notes it; check metadata))
- **Page records:** #5333 photographs "Yesterday's Headliner: ''Smiling'' Bill Hart, 1936"
- **Text in:** #5333 photographs "Yesterday's Headliner: ''Smiling'' Bill Hart, 1936" [body] **corrected 2 Oct (L7)**
- **Action:** ALREADY DONE. #5333 corrected on 2 October (L7): photoDate 8 July 1936, the AP's date, with a note that the paper ran it on Sunday 12 July.

### CE28: LEGACY ONLY / NONE

- **Page:** https://scvhistory.com/scvhistory/lw3629.htm (also /scvhistory/film.htm)
- **Error:** titled 3-28-1928
- **Correction:** November 3, 1928 (high)
- **Action:** NONE. Legacy only (lw3629 title, film.htm). for Leon: the live page.

### CE29: IN CRAFT / ALREADY DONE

- **Page:** https://scvhistory.com/scvhistory/lw3903.htm (also /scvhistory/lw2463a.htm, /scvhistory/lw2633a.htm, /scvhistory/lw2632a.htm, /scvhistory/lw2695.htm)
- **Error:** "Camulos has been owned by just two families since the end of war for Mexican independence from Spain"
- **Correction:** Ownership by the del Valles dates from the 1839 grant (with claimed use from about 1824–25). The site itself notes an 1804 grant attempt (Avila) and a 1820s Carrillo attempt. Suggest: 'since 1839' or 'since the Mexican era'. (medium)
- **Page records:** #631 places "Rancho Camulos"; #5675 photographs "Liquor Tax Certificate and Coupons, 1876, Issued t"; #3743 photographs "1867 Del Valle Winery"; #4317 photographs "Del Valle Brandy Still (In Use Circa 1867-1900)"; #4313 photographs "Native American Grinding Stones, ex-Rubel Museum i"; #4391 photographs "Del Valle Citrus Label (Pre-1924), Discovered 2014"
- **Text in:** #3743 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #3745 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #3747 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #3749 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #3751 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #3753 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #3755 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #3757 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #3759 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #3761 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #3763 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #4301 photographs "Handmade Clay Tiles Stored in 1867 Del Valle Winer" [body] **corrected 2 Oct (D5)**
- **Text in:** #4303 photographs "Handmade Clay Tiles Stored in 1867 Del Valle Winer" [body] **corrected 2 Oct (D5)**
- **Text in:** #4305 photographs "Handmade Clay Tiles Stored in 1867 Del Valle Winer" [body] **corrected 2 Oct (D5)**
- **Text in:** #4307 photographs "Handmade Clay Tiles Stored in 1867 Del Valle Winer" [body] **corrected 2 Oct (D5)**
- **Text in:** #4309 photographs "Statuary Associated with Chapel, ex-Rubel Museum i" [body] **corrected 2 Oct (D5)**
- **Text in:** #4311 photographs "Wall Decor Associated with Chapel, ex-Rubel Museum" [body] **corrected 2 Oct (D5)**
- **Text in:** #4313 photographs "Native American Grinding Stones, ex-Rubel Museum i" [body] **corrected 2 Oct (D5)**
- **Text in:** #4315 photographs "Native American Grinding Stones, ex-Rubel Museum i" [body] **corrected 2 Oct (D5)**
- **Text in:** #4317 photographs "Del Valle Brandy Still (In Use Circa 1867-1900)" [body] **corrected 2 Oct (D5)**
- **Text in:** #4319 photographs "Del Valle Brandy Still (In Use Circa 1867-1900)" [body] **corrected 2 Oct (D5)**
- **Text in:** #4321 photographs "Del Valle Brandy Still (In Use Circa 1867-1900)" [body] **corrected 2 Oct (D5)**
- **Text in:** #4323 photographs "Del Valle Brandy Still (In Use Circa 1867-1900)" [body] **corrected 2 Oct (D5)**
- **Text in:** #4325 photographs "Del Valle Brandy Still (In Use Circa 1867-1900)" [body] **corrected 2 Oct (D5)**
- **Text in:** #4327 photographs "Del Valle Brandy Still (In Use Circa 1867-1900)" [body] **corrected 2 Oct (D5)**
- **Text in:** #4329 photographs "Del Valle Brandy Still (In Use Circa 1867-1900)" [body] **corrected 2 Oct (D5)**
- **Text in:** #4331 photographs "Del Valle Brandy Still (In Use Circa 1867-1900)" [body] **corrected 2 Oct (D5)**
- **Text in:** #4333 photographs "Del Valle Brandy Still (In Use Circa 1867-1900)" [body] **corrected 2 Oct (D5)**
- **Text in:** #4335 photographs "Original Confessional Window from Chapel" [body] **corrected 2 Oct (D5)**
- **Text in:** #4337 photographs "Original Confessional Window from Chapel" [body] **corrected 2 Oct (D5)**
- **Text in:** #4389 photographs "Del Valle Citrus Label (Pre-1924), Discovered 2014" [body] **corrected 2 Oct (D5)**
- **Text in:** #4391 photographs "Del Valle Citrus Label (Pre-1924), Discovered 2014" [body] **corrected 2 Oct (D5)**
- **Text in:** #5675 photographs "Liquor Tax Certificate and Coupons, 1876, Issued t" [body] **corrected 2 Oct (D5)**
- **Action:** ALREADY DONE. The 33 Camulos records corrected on 2 October (D5). #631 Rancho Camulos does not carry the sentence. Medium on the live site.

### CE30: IN CRAFT / ALREADY DONE

- **Page:** https://scvhistory.com/scvhistory/lw3903.htm (also /scvhistory/lw2463a.htm, /scvhistory/lw2633a.htm, /scvhistory/lw2632a.htm, /scvhistory/lw2695.htm)
- **Error:** "Ygnacio, a big-league L.A. politician, worked a deal to keep the 1,500-acre Camulos section"
- **Correction:** About 1,340 acres (1870 partition). Other site pages say 1,300 (Robinson) or 1,350 (lastar18640514wolfskill, hl7101). No site page gives a source for 1,500. (high)
- **Page records:** #631 places "Rancho Camulos"; #5675 photographs "Liquor Tax Certificate and Coupons, 1876, Issued t"; #3743 photographs "1867 Del Valle Winery"; #4317 photographs "Del Valle Brandy Still (In Use Circa 1867-1900)"; #4313 photographs "Native American Grinding Stones, ex-Rubel Museum i"; #4391 photographs "Del Valle Citrus Label (Pre-1924), Discovered 2014"
- **Text in:** #3743 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #3745 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #3747 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #3749 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #3751 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #3753 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #3755 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #3757 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #3759 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #3761 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #3763 photographs "1867 Del Valle Winery" [body] **corrected 2 Oct (D5)**
- **Text in:** #4301 photographs "Handmade Clay Tiles Stored in 1867 Del Valle Winer" [body] **corrected 2 Oct (D5)**
- **Text in:** #4303 photographs "Handmade Clay Tiles Stored in 1867 Del Valle Winer" [body] **corrected 2 Oct (D5)**
- **Text in:** #4305 photographs "Handmade Clay Tiles Stored in 1867 Del Valle Winer" [body] **corrected 2 Oct (D5)**
- **Text in:** #4307 photographs "Handmade Clay Tiles Stored in 1867 Del Valle Winer" [body] **corrected 2 Oct (D5)**
- **Text in:** #4309 photographs "Statuary Associated with Chapel, ex-Rubel Museum i" [body] **corrected 2 Oct (D5)**
- **Text in:** #4311 photographs "Wall Decor Associated with Chapel, ex-Rubel Museum" [body] **corrected 2 Oct (D5)**
- **Text in:** #4313 photographs "Native American Grinding Stones, ex-Rubel Museum i" [body] **corrected 2 Oct (D5)**
- **Text in:** #4315 photographs "Native American Grinding Stones, ex-Rubel Museum i" [body] **corrected 2 Oct (D5)**
- **Text in:** #4317 photographs "Del Valle Brandy Still (In Use Circa 1867-1900)" [body] **corrected 2 Oct (D5)**
- **Text in:** #4319 photographs "Del Valle Brandy Still (In Use Circa 1867-1900)" [body] **corrected 2 Oct (D5)**
- **Text in:** #4321 photographs "Del Valle Brandy Still (In Use Circa 1867-1900)" [body] **corrected 2 Oct (D5)**
- **Text in:** #4323 photographs "Del Valle Brandy Still (In Use Circa 1867-1900)" [body] **corrected 2 Oct (D5)**
- **Text in:** #4325 photographs "Del Valle Brandy Still (In Use Circa 1867-1900)" [body] **corrected 2 Oct (D5)**
- **Text in:** #4327 photographs "Del Valle Brandy Still (In Use Circa 1867-1900)" [body] **corrected 2 Oct (D5)**
- **Text in:** #4329 photographs "Del Valle Brandy Still (In Use Circa 1867-1900)" [body] **corrected 2 Oct (D5)**
- **Text in:** #4331 photographs "Del Valle Brandy Still (In Use Circa 1867-1900)" [body] **corrected 2 Oct (D5)**
- **Text in:** #4333 photographs "Del Valle Brandy Still (In Use Circa 1867-1900)" [body] **corrected 2 Oct (D5)**
- **Text in:** #4335 photographs "Original Confessional Window from Chapel" [body] **corrected 2 Oct (D5)**
- **Text in:** #4337 photographs "Original Confessional Window from Chapel" [body] **corrected 2 Oct (D5)**
- **Text in:** #4389 photographs "Del Valle Citrus Label (Pre-1924), Discovered 2014" [body] **corrected 2 Oct (D5)**
- **Text in:** #4391 photographs "Del Valle Citrus Label (Pre-1924), Discovered 2014" [body] **corrected 2 Oct (D5)**
- **Text in:** #5675 photographs "Liquor Tax Certificate and Coupons, 1876, Issued t" [body] **corrected 2 Oct (D5)**
- **Action:** ALREADY DONE. The 33 Camulos records corrected on 2 October (D5).

### CE31: LEGACY ONLY / NONE

- **Page:** https://scvhistory.com/scvhistory/mu8491.htm (also /scvhistory/film.htm)
- **Error:** "1930 or earlier"
- **Correction:** About 1934–37: the page cites a Purdue print stamped 04-11-1934, and Hedda Hopper (1941) placed the photo a week before Earhart's 1937 flight. (medium-high)
- **Action:** NONE. Legacy only (mu8491 partly fixed by Leon; film.htm entry still reads 1930 or earlier). for Leon: the live page.

### CE32: LEGACY ONLY / NONE

- **Page:** https://scvhistory.com/scvhistory/perkins-desmond.htm
- **Error:** 'American Carrera Marble Co.' / 'A town site, Carrera' / 'Hotel Carrera'; 'they arrived in Newhall in 1918'
- **Correction:** Carrara (town; company name probably 'American Carrara Marble Co.', NEEDS_VERIFICATION). Add a note that other site sources give 1919 for the arrival. (high (spelling); medium (1918 note))
- **Action:** NONE. Legacy only (perkins-desmond is not in Craft). Carrara NEEDS_VERIFICATION. for Leon: the live page.

### CE33: IN CRAFT / NONE

- **Page:** https://scvhistory.com/scvhistory/perkins-fremont.htm
- **Error:** By A.B. Perkins | Date unkonwn.
- **Correction:** 'Date unknown.' (high)
- **Page records:** #1450 articles "Enemy Confirms Fremont's Trek Through SCV"
- **Text in:** #1450 articles "Enemy Confirms Fremont's Trek Through SCV" [body]
- **Action:** NONE. "Date unkonwn" on #1450 is a spelling slip in the web edition's byline line: kept as printed, as the archive keeps verbatim typos. for Leon: the live page.

### CE34: IN CRAFT / CORRECTION NOTE

- **Page:** https://scvhistory.com/scvhistory/perkins-newhall-1958.htm
- **Error:** 'Mary Pickford and William S. Hart filmed "Rags" at the old Newhall Land and Farming Company's deserted warehouse' (note [p] covers only the warehouse); '"The Virginians," and "The Light of the Eastern Star"'
- **Correction:** Add webmaster note: Hart was not in Rags (1915); a Pickford Newhall location is unverified; the two titles are probably The Virginian (1914) and The Light of Western Stars (1918) (NEEDS_VERIFICATION). (high (Hart not in cast); low (titles))
- **Page records:** #1438 articles "History of Downtown Newhall"
- **Text in:** #1438 articles "History of Downtown Newhall" [body]
- **Action:** CORRECTION NOTE. Perkins's 1958 text on #1438: a film story, unverified under the Perkins rule; the AFI Catalog's cast lists for Rags (1915) do not include Hart (as #16356 already says). Only the Hart part is noted; "The Virginians" and "The Light of the Eastern Star" are low confidence and left alone.

### CE35: IN CRAFT / CORRECTION NOTE

- **Page:** https://scvhistory.com/scvhistory/perkins-picocamp.htm
- **Error:** 'When the hotel burned in 1887, that ended that.'
- **Correction:** Add note: the Southern Hotel burned Oct 23, 1888 (medium-high)
- **Page records:** #1446 articles "The Pico Ghost Camp"
- **Text in:** #1446 articles "The Pico Ghost Camp" [body, recordDates]
- **Action:** CORRECTION NOTE. Perkins on #1446: a year slip on a lesser event. Leon's note on "The Birth of Newhall" (#869) and LW2273 (#3151) give 23 October 1888.

### CE36: IN CRAFT / NONE

- **Page:** https://scvhistory.com/scvhistory/perkins-rsf-1957.htm
- **Error:** [editor's note at 80] 'Sanford Lyon was bured in the family plot'
- **Correction:** 'buried' (high)
- **Page records:** #1434 articles "Rancho San Francisco: A Study of a California Land"; #27374 documents "Rancho San Francisco: A Study of a California Land"
- **Text in:** #1434 articles "Rancho San Francisco: A Study of a California Land" [body]
- **Text in:** #27374 documents "Rancho San Francisco: A Study of a California Land" [body]
- **Action:** NONE. "bured" is in Leon's editor's note to Perkins (#1434, #27374): a spelling slip, kept as printed. for Leon: the live page.

### CE37: IN CRAFT / CORRECTION NOTE

- **Page:** https://scvhistory.com/scvhistory/perkins-rsf-1957.htm
- **Error:** 'Served as assemblyman in 1852 and 1856.' (no Ed. note)
- **Correction:** Add Ed. note: Assembly 1852 session only (elected Sept 3, 1851); in 1856 he sat on the Los Angeles Common Council. (medium-high)
- **Page records:** #1434 articles "Rancho San Francisco: A Study of a California Land"; #27374 documents "Rancho San Francisco: A Study of a California Land"
- **Text in:** #1434 articles "Rancho San Francisco: A Study of a California Land" [footnotes]
- **Text in:** #27374 documents "Rancho San Francisco: A Study of a California Land" [body]
- **Action:** CORRECTION NOTE. Perkins's footnote on #1434 and #27374: an institutional label (Assembly against Common Council), unverified under the Perkins rule. MEDIUM-HIGH, flagged: the note states only what the sources hold (JoinCalifornia's one election, 1851; the City's record of his 1856 council term) and does not assert he was not in the Assembly in 1856 (Assembly Journal NEEDS_VERIFICATION).

### CE38: RECORD, NOT FOUND / HOLD

- **Page:** https://scvhistory.com/scvhistory/ridge.htm (also /scvhistory/tataviam.htm)
- **Error:** "(Unratified), 1852"
- **Correction:** 1851 (high)
- **Page records:** #913 groups "Tataviam"
- **Action:** HOLD. Not in Craft (#913 Tataviam does not carry the label); legacy ridge.htm and tataviam.htm. The fix (1851) is harmless to pass to Leon, but anything on the Tataviam pages migrates under TATAVIAM_AUDIT.md.

### CE39: IN CRAFT / NONE

- **Page:** https://scvhistory.com/scvhistory/sauguscafe.htm
- **Error:** 'Narragansut'; 'limted'; 'plausable'
- **Correction:** 'Narragansett'; 'limited'; 'plausible' (high)
- **Text in:** #2117 articles "46. Surrey" [body]
- **Action:** NONE. The typos are in Leon's notes on sauguscafe, legacy only. "Narragansut" in Reynolds's chapter 46 (#2117) is his spelling, kept as printed. for Leon: the live page.

### CE40: LEGACY ONLY / NONE

- **Page:** https://scvhistory.com/scvhistory/sg090199a.htm
- **Error:** "Antonio del Valle, a Mexican-born missionary who owned 48,000 acres"
- **Correction:** Antonio was a soldier (lieutenant, San Blas Company) and later the civil administrator (mayordomo) of Mission San Fernando, not a missionary. Add an editor's note. The article also names a living private descendant; this dossier does not repeat the name. (high)
- **Action:** NONE. Legacy only (sg090199a, a 1999 Signal feature). The archive's own profile of Antonio del Valle (#291) already says he was not a missionary. for Leon: the live page.

### CE41: IN CRAFT / HOLD

- **Page:** https://scvhistory.com/scvhistory/sg19470102perkins.htm
- **Error:** 'completion of the 6,964 ft. railroad tunnel in July 1875' (no note)
- **Correction:** Add note: the San Fernando tunnel was finished in July 1876, just before the Sept 5, 1876 spike (exact day NEEDS_VERIFICATION) (medium)
- **Page records:** #869 articles "The Birth of Newhall"
- **Text in:** #869 articles "The Birth of Newhall" [recordDates, body]
- **Action:** HOLD. Perkins 1947 (#869). The correction (July 1876) rests on Perkins's own 1954 sequence and Reynolds part 39, which under the Perkins rule are one source, and the day is NEEDS_VERIFICATION: it needs a source independent of both (a railroad report or a newspaper of 1876).

### CE42: IN CRAFT / NONE

- **Page:** https://scvhistory.com/scvhistory/signal/perkins/notes.html
- **Error:** note 8 'Lopez was uncle to Jacopa Feliz'; note 28 'ownership was transfered'
- **Correction:** 'Jacoba Feliz'; 'transferred' (high)
- **Page records:** #1432 articles "Editor's Notes"
- **Text in:** #1432 articles "Editor's Notes" [body, recordDates]
- **Action:** NONE. "Jacopa" and "transfered" on #1432 are in Leon's editor's notes; "Jacopa" is also Perkins's own spelling throughout (#1422, #1434, #27374). Kept as printed. for Leon: the live page.

### CE43: IN CRAFT / NONE

- **Page:** https://scvhistory.com/scvhistory/signal/perkins/part01.html
- **Error:** 'senior ethnologist, Bureau of American Ethnology, Smithsonlan Institution'
- **Correction:** 'Smithsonian' (high)
- **Page records:** #1420 articles "1. Early Inhabitants"
- **Text in:** #1420 articles "1. Early Inhabitants" [body, recordDates]
- **Action:** NONE. "Smithsonlan" on #1420 is a typing or OCR slip in Perkins's chapter on the first inhabitants: kept as printed, and the chapter is under TATAVIAM_AUDIT.md in any case. for Leon: the live page.

### CE44: IN CRAFT / HOLD

- **Page:** https://scvhistory.com/scvhistory/signal/perkins/part04.html
- **Error:** 'The right of way covered one mile each side of the Pass.' / 'Superintendent of Indian Affairs in California and Nevada in 1852' / 'about '86, that the franchise expired'
- **Correction:** Add editor's notes: two miles either side (1862 statute per Ripley); appointed Mar 3, 1853, Superintendent for California; franchise expired 1883–84. The page's ERRATA block (Ahnert 2019) covers only the Butterfield material. (medium-high (statute text NEEDS_VERIFICATION))
- **Page records:** #1426 articles "4. Early Transportation"
- **Text in:** #1426 articles "4. Early Transportation" [body, recordDates]
- **Action:** HOLD. Perkins part 4 (#1426), three claims, MEDIUM-HIGH: the right of way (two miles, statute text NEEDS_VERIFICATION), the franchise's end (1883-84, from Ripley), and the superintendency (Superintendent for California, appointed 3 March 1853). The archive disagrees with itself on the last: its own Beale profile (#327) says "California and Nevada from 1853" and the Tejon Ranch timeline says 1852. Settle #327 first.

### CE45: IN CRAFT / CORRECTION NOTE

- **Page:** https://scvhistory.com/scvhistory/signal/perkins/part06.html
- **Error:** 'Dr. Vincent Gelcich ... had married one of the daughters of Andres Pico'
- **Correction:** Add note: he married María Petra Pico y Bernal, Andrés Pico's niece (1863) (high)
- **Page records:** #1430 articles "6. Oil and Newhall"
- **Text in:** #1430 articles "6. Oil and Newhall" [body]
- **Action:** CORRECTION NOTE. Perkins part 6 (#1430): a family-relationship label, unverified under the Perkins rule. Leon's note c to Perkins's "History of Pico Canyon Oil Production" (#1440) gives the marriage: a niece, 1863.

### CE46: IN CRAFT / ALREADY DONE

- **Page:** https://scvhistory.com/scvhistory/signal/reynolds/bibliography.html
- **Error:** 'Burrows, D.H. "Del Valle." Hist. Society of Southern Calif., 1902.'
- **Correction:** 'Barrows, H.D.' (the year needs checking against the HSSC index: NEEDS_VERIFICATION) (high (year NEEDS_VERIFICATION))
- **Page records:** #2171 articles "BIBLIOGRAPHY"
- **Text in:** #2171 articles "BIBLIOGRAPHY" [body] **corrected 2 Oct (R2)**
- **Action:** ALREADY DONE. #2171 corrected on 2 October (R2).

### CE47: IN CRAFT / ALREADY DONE

- **Page:** https://scvhistory.com/scvhistory/signal/reynolds/bibliography.html
- **Error:** 'Williamson, R.S. Pacific Railroad Survey. Washington, DC 1952.'
- **Correction:** The 1850s (Pacific Railroad Reports, vol. 5, 1856); exact edition NEEDS_VERIFICATION (high (edition NEEDS_VERIFICATION))
- **Page records:** #2171 articles "BIBLIOGRAPHY"
- **Text in:** #2171 articles "BIBLIOGRAPHY" [body] **corrected 2 Oct (R2)**
- **Action:** ALREADY DONE. #2171 corrected on 2 October (R2).

### CE48: IN CRAFT / ALREADY DONE

- **Page:** https://scvhistory.com/scvhistory/signal/reynolds/bibliography.html
- **Error:** 'Van Valkenberg, R.'
- **Correction:** 'Van Valkenburgh, Richard' (high)
- **Page records:** #2171 articles "BIBLIOGRAPHY"
- **Text in:** #2171 articles "BIBLIOGRAPHY" [body] **corrected 2 Oct (R2)**
- **Action:** ALREADY DONE. #2171 corrected on 2 October (R2).

### CE49: IN CRAFT / CORRECTION NOTE

- **Page:** https://scvhistory.com/scvhistory/signal/reynolds/part25.html
- **Error:** 'The mayor of the town, Don Ygnacio del Valle'
- **Correction:** alcalde (1850), later city councilman; never mayor. Add an editor's note. (high)
- **Page records:** #2075 articles "25. Rest Stop"
- **Text in:** #2075 articles "25. Rest Stop" [body]
- **Action:** CORRECTION NOTE. Reynolds part 25 (#2075): same sources as CE19 (Pen Pictures 1889; the City's record of his offices on LW2052), both independent of Perkins and Reynolds.

### CE50: IN CRAFT / CORRECTION NOTE

- **Page:** https://scvhistory.com/scvhistory/signal/reynolds/part26.html
- **Error:** 'He had already sold Rancho Tejon to General Beale' (set before 1861)
- **Correction:** Beale acquired El Tejon in 1865–66. Add an editor's note. (high)
- **Page records:** #2077 articles "26. Twilight of the Dons"
- **Text in:** #2077 articles "26. Twilight of the Dons" [body]
- **Action:** CORRECTION NOTE. Reynolds part 26 (#2077): Beale bought Rancho El Tejon in 1865 and Rancho de Castac in 1866 (Tejon Ranch Company timeline, at SCVHistory.com). Note: #327's calendar dates still carry "In 1855 he acquired Rancho El Tejon", from its earlier text (Nathan).

### CE51: IN CRAFT / CORRECTION NOTE

- **Page:** https://scvhistory.com/scvhistory/signal/reynolds/part28.html
- **Error:** 'appointed to the U.S. Naval Academy by President Andrew Jackson'
- **Correction:** the Naval School; the Academy opened in 1845. Add an editor's note. (high)
- **Page records:** #2081 articles "28. Monarch of All He Surveys"
- **Text in:** #2081 articles "28. Monarch of All He Surveys" [body]
- **Action:** CORRECTION NOTE. Reynolds part 28 (#2081): the Naval Academy opened in 1845; Beale graduated from the Naval School in Philadelphia in 1842 (Pollack, 2014, at SCVHistory.com; #327 says the same).

### CE52: IN CRAFT / HOLD

- **Page:** https://scvhistory.com/scvhistory/signal/reynolds/part28.html
- **Error:** Beale 'got five thousand dollars to do the work'
- **Correction:** LA Star 4-4-1863 estimated $16,000–18,000. Add an editor's note. (high)
- **Page records:** #2081 articles "28. Monarch of All He Surveys"
- **Text in:** #327 persons "Edward Fitzgerald Beale" [body] authorship editorial-2026
- **Text in:** #2081 articles "28. Monarch of All He Surveys" [body]
- **Text in:** #932 places "Beale's Cut Stagecoach Pass" [body]
- **Action:** HOLD. Reynolds part 28 (#2081). The LA Star of 4 April 1863, as Perkins transcribes it (#1426), estimates "the additional work" at $16,000 to $18,000; Reynolds's $5,000 is what Beale "got" from the supervisors. The two figures may measure different things, so this is not yet a contradiction: it needs the Board's minutes. The archive's own prose repeats the $5,000 on #327 (Beale) and #932 (Beale's Cut).

### CE53: IN CRAFT / ALREADY DONE

- **Page:** https://scvhistory.com/scvhistory/signal/reynolds/part29.html
- **Error:** "Management of the estate passed to his eldest surviving son, Reginaldo, who was then a state senator"
- **Correction:** In March 1880 Reginaldo was an Assemblyman-elect or Assemblyman (1880–81) and became State Senator only in 1882. Juventino (b. 1841) was older. Add an editor's note. (high)
- **Page records:** #2083 articles "29. Barbaric Elegance"
- **Text in:** #2083 articles "29. Barbaric Elegance" [body] **corrected 2 Oct (D7)**
- **Action:** ALREADY DONE. #2083 corrected on 2 October (D7).

### CE54: RECORD, NOT FOUND / NONE

- **Page:** https://scvhistory.com/scvhistory/signal/reynolds/part41.html
- **Error:** <title> '... | 46: Surrey'
- **Correction:** '41: Baron of Casteca' (high)
- **Page records:** #2107 articles "41. Baron of Casteca"
- **Action:** NONE. Legacy title tag only; #2107's title is right ("41. Baron of Casteca"). for Leon: the live page.

### CE55: IN CRAFT / ALREADY DONE

- **Page:** https://scvhistory.com/scvhistory/signal/reynolds/part46.html
- **Error:** 'on April 25, a special train ... rolled up to the Saugus Station ... President Benjamin Harrison' ... 'met the president at Saugus'
- **Correction:** Harrison did not stop at Saugus; he passed on Apr 24, 1891 (reported the 25th); the Santa Barbara delegation met him at Ventura. Add an editor's note, as on sauguscafe. (high)
- **Page records:** #2117 articles "46. Surrey"
- **Text in:** #2117 articles "46. Surrey" [body] **corrected 2 Oct (R5)**
- **Action:** ALREADY DONE. #2117 corrected on 2 October (R5).

### CE56: IN CRAFT / ALREADY DONE

- **Page:** https://scvhistory.com/scvhistory/signal/reynolds/part46.html
- **Error:** 'Joseph H. Tolfree started the Saugus Eating House'
- **Correction:** James Herbert Tolfree (died 1897); Joseph H. may have run it later (medium)
- **Page records:** #2117 articles "46. Surrey"
- **Text in:** #2117 articles "46. Surrey" [body] **corrected 2 Oct (R5)**
- **Action:** ALREADY DONE. #2117 corrected on 2 October (R5). Medium on the live site.

### CE57: IN CRAFT / ALREADY DONE

- **Page:** https://scvhistory.com/scvhistory/signal/reynolds/part53.html
- **Error:** "seventeen-year-old" bride (Winifred Westover)
- **Correction:** About 22 (born 1898 or 1899). (high)
- **Page records:** #2131 articles "53. Two-Gun Bill"
- **Text in:** #2131 articles "53. Two-Gun Bill" [body, recordDates] **corrected 2 Oct (L12)**
- **Action:** ALREADY DONE. #2131 corrected on 2 October (L12).

### CE58: IN CRAFT / ALREADY DONE

- **Page:** https://scvhistory.com/scvhistory/signal/reynolds/part53.html
- **Error:** Tumbleweeds prologue quoted as "I loved... yawning chasm... battled ones that remained"
- **Correction:** "I love the art... yawning canyon... baffled ones that remain" (high)
- **Page records:** #2131 articles "53. Two-Gun Bill"
- **Text in:** #2131 articles "53. Two-Gun Bill" [body] **corrected 2 Oct (L13)**
- **Action:** ALREADY DONE. #2131 corrected on 2 October (L13).

### CE59: IN CRAFT / CORRECTION NOTE

- **Page:** https://scvhistory.com/scvhistory/signal/reynolds/part54.html
- **Error:** St. Francis Dam morgue 'inside ... Hap-A-Lan dance hall'; Hart dressed a dead boy 'in a little cowboy suit' and led a procession to the Ruiz family cemetery
- **Correction:** Morgue was in the Masonic lodge; the boy was buried at Oakwood, Chatsworth (cowboy-suit story is legend). Add an editor's note. (high)
- **Page records:** #2133 articles "54. Disaster at 185 Feet"
- **Text in:** #2133 articles "54. Disaster at 185 Feet" [body]
- **Action:** CORRECTION NOTE. Reynolds part 54 (#2133), in part. The burial is noted: Leon's "Requiem to a Little Soldier" (2003, from the Newhall Signal of 29 March and 5 April 1928) has the boy buried at Oakwood Cemetery, Chatsworth, and calls the cowboy outfit "an oft-repeated local legend that hasn't been refuted" (quoted, not called false). HOLD the morgue: Leon puts it in "the Masonic lodge in Newhall, normally a happy and popular dance hall", which may be the same building as the Hap-A-Lan; not noted until that is settled.

### CE60: IN CRAFT / NONE

- **Page:** https://scvhistory.com/scvhistory/signal/reynolds/part58.html
- **Error:** '*See note below' plus a note saying 'The section at the top in italics is inaccurate', but the top section now reads $6,000 (the corrected figure)
- **Correction:** Reword the note to say the text has been corrected from Reynolds' $11,000 / Dec 17, 1925, and add the 1922 date to the text (high)
- **Page records:** #2141 articles "58. Pistoleros"
- **Text in:** #2141 articles "58. Pistoleros" [body]
- **Action:** NONE. Leon's webmaster's note on #2141 still calls the top "inaccurate" after the top was corrected: his wording, kept. Note: #2141's calendar dates carry "December 17, 1925", taken from "(Reynolds said December 17, 1925)" (Nathan). for Leon: the live page.

### CE61: RECORD, NOT FOUND / HOLD

- **Page:** https://scvhistory.com/scvhistory/signal/reynolds/reynolds-castaicethnography.htm
- **Error:** 'By Jerry Reynolds For The Signal date?'
- **Correction:** Supply the column date, or 'date unknown' (high)
- **Page records:** #2175 articles "Ethnography of Castaic"
- **Action:** HOLD. Reynolds's "Ethnography of Castaic" (#2175): ethnography, under TATAVIAM_AUDIT.md. The "date?" placeholder did not come into Craft; originalPublishDate is empty.

### CE62: RECORD, NOT FOUND / HOLD

- **Page:** https://scvhistory.com/scvhistory/signal/reynolds/reynolds121484.htm
- **Error:** a locational description of an archaeological site (not repeated here)
- **Correction:** Not a factual error. Flag for tribal consultation about redacting the locational description before migration. (CARE flag (not a factual error))
- **Page records:** #2177 articles (disabled) "Bowers Cave"
- **Action:** HOLD. Bowers Cave (#2177): tribal consultation before anything else; the record is disabled (disable_bowers_cave.php). Not a factual error.

### CE63: IN CRAFT / HOLD

- **Page:** https://scvhistory.com/scvhistory/signal/worden/lw011399.htm
- **Error:** Page title/header: 'Henry Newhall Builds an Empire'; body byline date 'Wednesday, April 24, 2002'
- **Correction:** The title should be 'E.F. Beale and the Beasts of Tejon'. The date needs checking: the filename encodes 01-13-1999, and the text says the SCVHS '1999 calendar' is 'Available now', which fits Jan 1999, not April 2002. (high (title); medium (date))
- **Page records:** #12168 articles "E.F. Beale and the Beasts of Tejon"
- **Text in:** #12168 articles "E.F. Beale and the Beasts of Tejon" [body]
- **Action:** HOLD. Craft's title is already right ("E.F. Beale and the Beasts of Tejon"). The byline date, April 24, 2002, is in Leon's body; the file name lw011399 and the "1999 calendar ... available now" fit 13 January 1999, MEDIUM. Also: #12168's originalPublishDate holds a paragraph of the column's text, not a date: a field fix waiting on the right date (Nathan).

### CE64: IN CRAFT / ALREADY DONE

- **Page:** https://scvhistory.com/scvhistory/signal/worden/lw011399.htm
- **Error:** quotes Reynolds on Hi Jolly as dying 'Dec. 16, 1903' at 75
- **Correction:** Dec 16, 1902, aged 74 (what Reynolds actually wrote in part23) (high)
- **Page records:** #12168 articles "E.F. Beale and the Beasts of Tejon"
- **Text in:** #12168 articles "E.F. Beale and the Beasts of Tejon" [body] **corrected 2 Oct (R9)**
- **Action:** ALREADY DONE. #12168 corrected on 2 October (R9).

### CE65: IN CRAFT / ALREADY DONE

- **Page:** https://scvhistory.com/scvhistory/signal/worden/lw040997.htm
- **Error:** "Big Bill Hart took a crack at the famous director in his autobiography, saying Ford should have made the chase scene more realistic" (about Stagecoach, 1939)
- **Correction:** Hart's autobiography, My Life East and West, was published in 1929, ten years before Stagecoach. The criticism, if Hart made it, must come from another source (NEEDS_VERIFICATION; the column attributes the anecdote to Buscombe's book). (high (date logic); source NEEDS_VERIFICATION)
- **Page records:** #12290 articles "Movie trivia from Beale's Cut"
- **Text in:** #12290 articles "Movie trivia from Beale's Cut" [body] **corrected 2 Oct (B7)**
- **Text in:** #12370 articles "Movie trivia from Beale's Cut" [body] **corrected 2 Oct (B7)**
- **Action:** ALREADY DONE. #12290 and #12370 corrected on 2 October (B7).

### CE66: IN CRAFT / CORRECTION NOTE

- **Page:** https://scvhistory.com/scvhistory/signal/worden/lw092596.htm (also /scvhistory/signal/worden/old/lw092596.htm)
- **Error:** He used several in 1962 when he wrote the "Story of Our Valley" in successive installments in The Signal.
- **Correction:** 1954–55. This 1996 column is probably where the ap0828 '1962' came from. Add an author's/editor's note rather than changing the column text. (high)
- **Page records:** #12542 articles "Historic A.B. Perkins photos resurface"; #12574 articles "Historic A.B. Perkins Photos Reappear"
- **Text in:** #12542 articles "Historic A.B. Perkins photos resurface" [body]
- **Text in:** #12574 articles "Historic A.B. Perkins Photos Reappear" [body]
- **Action:** CORRECTION NOTE. Leon's 1996 column, both copies (#12542 and #12574, the old version): a note that the series ran April 1954 to January 1955.

### CE67: LEGACY ONLY / NONE

- **Page:** https://scvhistory.com/scvhistory/tejonranchtimeline.htm
- **Error:** "1893 — Beale dies at the age of 72."
- **Correction:** 71 (Feb 4, 1822 to Apr 22, 1893). (high)
- **Action:** NONE. Legacy only (tejonranchtimeline, Tejon Ranch Co. text). for Leon: the live page: an editor's note, 71.

### CE68: LEGACY ONLY / NONE

- **Page:** https://scvhistory.com/scvhistory/times111101.htm (also /scvhistory/penpictures_rdelvalle.htm)
- **Error:** "When Del Valle died in 1880, at age 72"; "He died in 1880, at the age of seventy-two years"
- **Correction:** 71 (born July 1, 1808; died March 30, 1880). Add [sic] notes; the originals should not be altered. (high)
- **Action:** NONE. Legacy only (times111101, penpictures_rdelvalle). for Leon: the live page: [sic] notes, 71.

### CE69: ARCHIVE DATA / ALREADY DONE

- **Page:** CC migration data (not the live site): person records #327 'Edward Fitzgerald Beale' and #15908 'Edward F. Beale'
- **Error:** two person records for one man
- **Correction:** Merge into #327 (the user's canonical record), carrying over #15908's 30 prose-only photo links. (n/a (migration data))
- **Action:** ALREADY DONE. ARCHIVE DATA: #15908 "Edward F. Beale" is in the trash and #327 is the record (settled before this list).

