# Live errors: one work list

Built by `scripts/import/build_live_errors_list.php` from Grok's four final dossiers (Hart, Beale, del Valle, Reynolds; Perkins did not land). 47 errors. Where each lives was found, not assumed: a Craft record carrying the page, and the wrong text searched for in every record.

| where | count |
| --- | --- |
| IN CRAFT | 18 |
| RECORD, NOT FOUND | 9 |
| LEGACY ONLY | 19 |
| ARCHIVE DATA | 1 |

**IN CRAFT**: the wrong text is in a Craft record now. **RECORD, NOT FOUND**: a Craft record carries the page but the text was not found in it; check by eye. **LEGACY ONLY**: no Craft record carries the page (an index, a title tag, a page not migrated): fix in the migration or on the live site. **ARCHIVE DATA**: an error in our own records.

**CARE: RL8, Bowers Cave (Reynolds column of 14 December 1984).** The page tells readers how to find an archaeological site. Grok flagged it for tribal consultation. It was already imported, as article #2177; `disable_bowers_cave.php` takes it off the site until consultation. Do not quote the locational passage in any record, note or report.

## 1. Hart L1: IN CRAFT

- **Page:** fh2701; NHMLA boilerplate captions (cp1702, mu9067, cp1703, lw3041, lw3169 and similar); lw2068
- **Error:** say LA County Parks and NHMLA operate the park and museum; lw2068: NHMLA "operates the Hart Museum"
- **Correction:** Since July 14, 2025, the City of Santa Clarita owns and operates Hart Park and the Hart Museum. NHM's P-75 and P-98 photo collections were transferred to the City. GC 1012 and GC 1192 are still listed at the Seaver Center.
- **Source:** County approval Aug 6, 2024 (Barger release); Master Agreement Feb 13, 2025; probate court acceptance (City, 5-13-2025); City ownership (City, 7-14-2025); NHM photo guide (C21).
- **Craft:** #2759 photographs (text found: "operates the Hart Museum")

## 2. Hart L2: IN CRAFT

- **Page:** lw2298a and lat19360719hart ("HART IN RETIREMENT" sidebar)
- **Error:** thumbnail hs9909t.jpg "Letter: Gift of Buffalo Coat to Rudy Vallee 1936" links lw3383.htm
- **Correction:** It should link hs9909.htm (lw3383 is the Earhart letter).
- **Source:** Live link parse, 2026-09-29 (C22).
- **Craft:** #3169 photographs, #3171 photographs, #4351 photographs, #4695 photographs, #4833 photographs, #4891 photographs, #4989 photographs, #5041 photographs, #5225 photographs, #5227 photographs, #5251 photographs, #5333 photographs (text found: "Letter: Gift of Buffalo Coat to Rudy Vallee 1936")

## 3. Hart L3: LEGACY ONLY

- **Page:** film.htm (Hart index)
- **Error:** "LW2341 - ... Pinto Ben on Rudy Vallee's Radio Show, 12-13-1934" links lw2341.htm
- **Correction:** It should be LW2342 / lw2342.htm (lw2341 is the 1928 Victor record, which is also listed separately, so LW2341 appears twice).
- **Source:** Live link parse; page titles of lw2341 and lw2342 (C22).

## 4. Hart L4: LEGACY ONLY

- **Page:** lw3629 and its film.htm entry
- **Error:** titled 3-28-1928
- **Correction:** November 3, 1928
- **Source:** The page's own text and cutline: "Chicago, Nov. 3-28" (C22).

## 5. Hart L5: LEGACY ONLY

- **Page:** fh2701
- **Error:** credit line "FH2101"
- **Correction:** FH2701 (FH2101 is a different item, a lantern slide).
- **Source:** Page code vs credit line (C22).

## 6. Hart L6: LEGACY ONLY

- **Page:** mu8491 and its film.htm entry
- **Error:** "1930 or earlier"
- **Correction:** About 1934–37: the page cites a Purdue print stamped 04-11-1934, and Hedda Hopper (1941) placed the photo a week before Earhart's 1937 flight.
- **Source:** The page's own evidence (C22).

## 7. Hart L7: IN CRAFT

- **Page:** lw3486
- **Error:** cutline "Sunday, July 11"
- **Correction:** Published Sunday, July 12, 1936 (the page itself notes the cutline error). Make sure the title/date fields use 7-12-1936.
- **Source:** The page's own note (C22).
- **Craft:** #5333 photographs (text found: "Sunday, July 11")

## 8. Hart L8: LEGACY ONLY

- **Page:** fh2701
- **Error:** "purchased the 254-acre Horseshoe Ranch in Newhall in 1921 from Babcock Smith"
- **Correction:** Leased in 1918. The first purchase in Feb 1921 was a few lots; the ranch was assembled in about 10 deeds through Oct 1933.
- **Source:** The site's own mu8901 (Sitton 1989, citing deed books) (C6).

## 9. Hart L9: LEGACY ONLY

- **Page:** fh2701
- **Error:** "Completed in 1927", on a page whose photo is "under construction, October(?) 1927"
- **Correction:** Completed late 1927 or early 1928. The page contradicts itself.
- **Source:** The page's own caption; lw2271 (Photoplay May 1928, 'new ranch home'); Earp letters to Hollywood until Nov 1927 (C9).

## 10. Hart L10: LEGACY ONLY

- **Page:** cp1703
- **Error:** "Newly Completed Hart Mansion, 2 Views, 1930"
- **Correction:** Completed about 1927–28. Either the photo date or 'newly completed' is wrong.
- **Source:** The site's own hartmansion-const (July 1926 'contemplating building') and lw2271 (May 1928) (C9).

## 11. Hart L11: LEGACY ONLY

- **Page:** cp1702 and mu9067 (NHMLA dating quoted)
- **Error:** construction views dated "1925-1926" and "circa 1926"
- **Correction:** Late 1926–1927. Hart was only 'contemplating building' on July 12, 1926.
- **Source:** The site's own hartmansion-const letter (C9). Leon's captions already say this; the NHMLA dates on the same pages do not.

## 12. Hart L12: IN CRAFT

- **Page:** reynolds part53
- **Error:** "seventeen-year-old" bride (Winifred Westover)
- **Correction:** About 22 (born 1898 or 1899).
- **Source:** The site's own lw3335 ('Winifred Westover, 22') and lw2291 (age 51 in Feb 1950) (C18). Add a [sic] or an editor's note.
- **Craft:** #2131 articles (text found: "seventeen-year-old")

## 13. Hart L13: IN CRAFT

- **Page:** reynolds part53
- **Error:** Tumbleweeds prologue quoted as "I loved... yawning chasm... battled ones that remained"
- **Correction:** "I love the art... yawning canyon... baffled ones that remain"
- **Source:** The site's own tumbleweedsmonologue transcript of the 1939 sound prologue (C20).
- **Craft:** #2131 articles (text found: "battled ones that remained")

## 14. Beale L1: RECORD, NOT FOUND

- **Page:** bealescut.htm (Beale's Cut index)
- **Error:** Entry "Beale's Cut in John Ford's 'The Iron Horse,' 1924" labeled LW2159, linking https://scvhistory.com/scvhistory/lw2228.htm
- **Correction:** The label should be LW2228 (LW2159 is the Tom Mix item, listed separately above it).
- **Source:** Link parse of the live index (bealescut_links.txt): 'LW2159 | .../lw2228.htm'; the lw2228 page title reads 'LW2228 | Beale's Cut | 'The Iron Horse' (1924)'.
- **Craft:** #932 places

## 15. Beale L2: RECORD, NOT FOUND

- **Page:** signal/worden/lw011399.htm
- **Error:** Page title/header: "Henry Newhall Builds an Empire"; body: "E.F. Beale and the Beasts of Tejon By Leon Worden Wednesday, April 24, 2002"
- **Correction:** The title should be 'E.F. Beale and the Beasts of Tejon'. The date needs checking: the filename encodes 01-13-1999, and the text says the SCVHS '1999 calendar' is 'Available now', which fits Jan 1999, not April 2002.
- **Source:** Fetched page text; the inventory title for this URL is 'E.F. Beale and the Beasts of Tejon'. The same page misquotes Reynolds as 'Dec. 16, 1903... 75-year-old' Hi Jolly; reynolds part23 reads 'December 16, 1902... seventy-four-year-old'.
- **Craft:** #12168 articles

## 16. Beale L3: RECORD, NOT FOUND

- **Page:** ridge.htm and tataviam.htm (index labels for heizer1972)
- **Error:** "(Unratified), 1852"
- **Correction:** 1851
- **Source:** heizer1972 page title: 'Treaty Between the United States and the Indians of "Castaic, Tejon, Etc.," 1851. With Heizer (1972).'
- **Craft:** #913 groups

## 17. Beale L4: LEGACY ONLY

- **Page:** lo8801 and lo8802 (Watkins photo captions)
- **Error:** "The Army began to shut down the fort when the U.S. Civil War broke out and closed it, and the reservation, for good in 1854."
- **Correction:** 1864 (the fort was abandoned Sept 11, 1864, and the Indians were removed to Tule River in 1864).
- **Source:** The site's own pages: lw2580, reynolds part23, cullimore_oldadobes, forttejonparkinventory2015, tejonranchtimeline (all Sept 11, 1864); the Interior Solicitor's memo (Tule River 1864). The sentence itself puts the closing after the Civil War broke out (1861).

## 18. Beale L5: RECORD, NOT FOUND

- **Page:** hb1806 (2018 webmaster note); worden lw032905 (2005 column)
- **Error:** "As this is added to the archive in 2018, Beale's Cut is located on private property... Trespassing is prohibited." / "It is private property and you would be trespassing."
- **Correction:** Ownership has changed. On Oct 28, 2025 the Santa Clarita City Council approved buying the approximately 13-acre Forum Engineering Property, which contains Beale's Cut, adding it to the city's Open Space Preservation District. Current access rules: NEEDS_VERIFICATION. Close of escrow: NEEDS_VERIFICATION.
- **Source:** SCVNews 10-27-2025 (city staff report quoted: 'The property includes the California historical landmark, Beale's Cut'); Signal (Perry Smith) 10-30-2025, 'added 670 acres to its open space Tuesday, including the historic Beale's Cut'; CEQAnet 2025110832 (state grant). Both notes are dated, so this is out-of-date custody text in the same way as the Hart Park example.
- **Craft:** #12166 articles

## 19. Beale L6: IN CRAFT

- **Page:** lw2208, lw3451, lw3647, lw2161
- **Error:** "from Fort Defiance, Texas" / "between Ft. Defiance, in Texas, and the Colorado River"
- **Correction:** Fort Defiance, New Mexico Territory (today Arizona).
- **Source:** The site's own lw3491: 'from Fort Defiance, New Mexico Territory, to the Colorado River'; WP further reading: Beale, Wagon Route From Fort Defiance to the Colorado River (H. Ex. Doc. 124, 35th Cong. 1st Sess.).
- **Craft:** #2941 photographs, #2943 photographs, #2945 photographs, #2947 photographs, #2949 photographs, #5285 photographs, #5491 photographs (text found: "between Ft. Defiance, in Texas, and the Colorado River")

## 20. Beale L7: IN CRAFT

- **Page:** signal/worden/lw040997.htm
- **Error:** "Big Bill Hart took a crack at the famous director in his autobiography, saying Ford should have made the chase scene more realistic" (about Stagecoach, 1939)
- **Correction:** Hart's autobiography, My Life East and West, was published in 1929, ten years before Stagecoach. The criticism, if Hart made it, must come from another source (NEEDS_VERIFICATION; the column attributes the anecdote to Buscombe's book).
- **Source:** Stagecoach release 1939 (lw2065, lw2172); Hart autobiography 1929 (Hart dossier; site page hart-wife-son cites 'Hart 1929').
- **Craft:** #12290 articles, #12370 articles (text found: "Big Bill Hart took a crack at the famous director in his autobiography, saying F")

## 21. Beale L8: LEGACY ONLY

- **Page:** bealeafb
- **Error:** "In 1870 he bought the Decatur House"
- **Correction:** 1871
- **Source:** White House Historical Association (operator) and National Trust (owner) both say 1871; WP Beale says 1871 ($60,000). The page reproduces Beale AFB text, so fix it with a [sic] or an editor's note.

## 22. Beale L9: LEGACY ONLY

- **Page:** tejonranchtimeline
- **Error:** "1893 — Beale dies at the age of 72."
- **Correction:** 71 (Feb 4, 1822 to Apr 22, 1893).
- **Source:** The site's own bealeafb and reynolds part28 dates; Wikidata Q1292161. The page is Tejon Ranch Co. 2014 text, so add an editor's note.

## 23. Beale L10: ARCHIVE DATA

- **Page:** CC migration data (not the live site): person records #327 'Edward Fitzgerald Beale' and #15908 'Edward F. Beale'
- **Error:** two person records for one man
- **Correction:** Merge into #327 (the user's canonical record), carrying over #15908's 30 prose-only photo links.
- **Source:** photo-links namedOnlyInProse: 30 for #15908 and 10 for #327. For the migration team; no live-site change needed.

## 24. del Valle L1: RECORD, NOT FOUND

- **Page:** lw2052 (Ygnacio del Valle); same figures in reynolds part15
- **Error:** "a judge awarded 13,599 acres to Ygnacio, 21,307 acres to Jacoba, and 4,684 acres to each of Jacoba's six children"
- **Correction:** The three figures add up to 62,010 acres, about 13,400 more than the whole rancho (48,611.88 acres patented). They cannot all be right. Add an editor's note, or replace them with the documented sequence: undivided shares 1841–1870, then the 1870 partition that gave Ygnacio Camulos (about 1,340 acres). reynolds part29 gives yet another figure, 'original grant of 16,599 acres'.
- **Source:** Arithmetic; patent acreage (Land Case 303 SD, Bancroft/Calisphere); perkins-rsf-1957 ('could not be partitioned before title confirmation'; 1870 partition, 2/21, 1,340 acres); robinson_storyofvalencia_1967 (undivided 5/11).
- **Craft:** #293 persons, #851 articles

## 25. del Valle L2: RECORD, NOT FOUND

- **Page:** lw2052
- **Error:** "Instead, Ygnacio lived at Los Angeles, where he was mayor. Acutally, Ygnacio was a California state legislator, too"
- **Correction:** 'where he was alcalde (1850) and later a city councilman'. Fix the typo 'Acutally'. The page's own office list shows only City Council terms (May 4, 1852 – May 3, 1853; May 7 – Dec 15, 1856).
- **Source:** Same page's office list; penpictures_ydelvalle (1889): 'in 1850 he was alcalde'; carter-ramona1902: A.F. Coronel was mayor in 1853; Wikipedia infobox: alcalde ('de facto mayor') Jan–Jul 1850, succeeded by A.P. Hodges, first mayor under the city charter.
- **Craft:** #293 persons

## 26. del Valle L3: IN CRAFT

- **Page:** lw2052; lw2532; lw3768
- **Error:** lw2052: "When statehood came in 1850, Ygnacio served a short stint in the first Legislature"; lw2532/lw3768: "after serving in the state Legislature at statehood in 1850"
- **Correction:** He was elected on Sept 3, 1851 (2nd Assembly District) and sat in the 1852 session (Jan 5, 1852 – Jan 3, 1853), not the first Legislature of 1849–50.
- **Source:** JoinCalifornia record 'Ignacio Del Valle' (09-03-1851, AD-02, Win); Wikipedia infobox. Confirming entry in the Assembly Journal, 1852: NEEDS_VERIFICATION.
- **Craft:** #293 persons (text found: "When statehood came in 1850, Ygnacio served a short stint in the first Legislatu")

## 27. del Valle L4: IN CRAFT

- **Page:** lw2752 (family tree)
- **Error:** "Upon Antonio's death two years later, the ranch was divided among his widow and children. Son Ygnacio ended up with the western portion"
- **Correction:** The heirs held undivided shares from 1841. Title was confirmed in 1855–57, and the rancho was formally partitioned only in 1870, when Ygnacio's share was set off as Camulos.
- **Source:** perkins-rsf-1957 (estate distributed 'by undivided portions'; 1870 partition, effective July 30); robinson_storyofvalencia_1967 (the explanation of 'undivided').
- **Craft:** #4461 photographs (text found: "Upon Antonio's death two years later, the ranch was divided among his widow and ")

## 28. del Valle L5: IN CRAFT

- **Page:** lw3903, lw2463a, lw2633a, lw2632a, lw2695 (shared Camulos boilerplate)
- **Error:** "Ygnacio, a big-league L.A. politician, worked a deal to keep the 1,500-acre Camulos section"
- **Correction:** About 1,340 acres (1870 partition). Other site pages say 1,300 (Robinson) or 1,350 (lastar18640514wolfskill, hl7101). No site page gives a source for 1,500.
- **Source:** perkins-rsf-1957; reynolds part29; delcastillo1980 ('dwindled to 1,340 acres by 1886'); robinson_storyofvalencia_1967.
- **Craft:** #3743 photographs, #3745 photographs, #3747 photographs, #3749 photographs, #3751 photographs, #3753 photographs, #3755 photographs, #3757 photographs, #3759 photographs, #3761 photographs, #3763 photographs, #4301 photographs, #4303 photographs, #4305 photographs, #4307 photographs, #4309 photographs, #4311 photographs, #4313 photographs, #4315 photographs, #4317 photographs, #4319 photographs, #4321 photographs, #4323 photographs, #4325 photographs, #4327 photographs, #4329 photographs, #4331 photographs, #4333 photographs, #4335 photographs, #4337 photographs, #4389 photographs, #4391 photographs, #5675 photographs (text found: "Ygnacio, a big-league L.A. politician, worked a deal to keep the 1,500-acre Camu")

## 29. del Valle L6: LEGACY ONLY

- **Page:** lastar18640514wolfskill (webmaster's note)
- **Error:** "They retained the westernmost 1,350 acres, which became the separate Rancho Camulos when Henry Newhall bought the 46,460 acres that remained of the old Rancho San Francisco at a sheriff's sale in 1875"
- **Correction:** Newhall's Jan 15, 1875 purchase ($90,000) was not a sheriff's sale. The 1873 sheriff's sale went to Fernald & Richards; a second sheriff's sale was called off when a private buyer (Newhall) was found.
- **Source:** The site's own robinson_storyofvalencia_1967 summary; perkins-rsf-1957 ('deeded to Henry M. Newhall, consideration $90,000, January 15, 1875'). The acreage (46,460) is NEEDS_VERIFICATION.

## 30. del Valle L7: IN CRAFT

- **Page:** signal/reynolds/part29.html (reprint)
- **Error:** "Management of the estate passed to his eldest surviving son, Reginaldo, who was then a state senator"
- **Correction:** In March 1880 Reginaldo was an Assemblyman-elect or Assemblyman (1880–81) and became State Senator only in 1882. Juventino (b. 1841) was older. Add an editor's note.
- **Source:** penpictures_rdelvalle (1889: 'in 1882 he was unanimously nominated as State Senator'); lat18900822delvalle note (Assembly 1880–81); lw2752 (Juventino 1841–1919); lw3664 (Juventino manager 1862–1886).
- **Craft:** #2083 articles (text found: "Management of the estate passed to his eldest surviving son, Reginaldo, who was ")

## 31. del Valle L8: LEGACY ONLY

- **Page:** hl7101
- **Error:** "back in 1858, Ygnacio's stepfather borrowed $8,500"
- **Correction:** José Salazar was the second husband of Ygnacio's stepmother, Jacoba Feliz, not his stepfather. Suggest: 'his stepmother's husband, José Salazar'.
- **Source:** reynolds part15; perkins-rsf-1957; lastar18640514wolfskill ('Jose Salazar, the new husband of Antonio del Valle's widow').

## 32. del Valle L9: LEGACY ONLY

- **Page:** times111101 (reprint); penpictures_rdelvalle (1889 original)
- **Error:** "When Del Valle died in 1880, at age 72"; "He died in 1880, at the age of seventy-two years"
- **Correction:** 71 (born July 1, 1808; died March 30, 1880). Add [sic] notes; the originals should not be altered.
- **Source:** lw2052 / reynolds part14 (born July 1, 1808); reynolds part29 (died March 30, 1880).

## 33. del Valle L10: LEGACY ONLY

- **Page:** sg090199a (1999 Signal reprint)
- **Error:** "Antonio del Valle, a Mexican-born missionary who owned 48,000 acres"
- **Correction:** Antonio was a soldier (lieutenant, San Blas Company) and later the civil administrator (mayordomo) of Mission San Fernando, not a missionary. Add an editor's note. The article also names a living private descendant; this dossier does not repeat the name.
- **Source:** reynolds part14; perkins-rsf-1957 fn 26; engelhardt_sanfernando56; NHM GC 1002 ('came to California in the Spanish army in 1819').

## 34. del Valle L11: IN CRAFT

- **Page:** lw3903, lw2463a, lw2633a, lw2632a, lw2695 (shared boilerplate)
- **Error:** "Camulos has been owned by just two families since the end of war for Mexican independence from Spain"
- **Correction:** Ownership by the del Valles dates from the 1839 grant (with claimed use from about 1824–25). The site itself notes an 1804 grant attempt (Avila) and a 1820s Carrillo attempt. Suggest: 'since 1839' or 'since the Mexican era'.
- **Source:** perkins-rsf-1957 (1804 protest; Carrillo's 1820s attempt; grant Jan 22, 1839; 'the exact current status ... is a little clouded' before 1839); Land Case 303 SD.
- **Craft:** #3743 photographs, #3745 photographs, #3747 photographs, #3749 photographs, #3751 photographs, #3753 photographs, #3755 photographs, #3757 photographs, #3759 photographs, #3761 photographs, #3763 photographs, #4301 photographs, #4303 photographs, #4305 photographs, #4307 photographs, #4309 photographs, #4311 photographs, #4313 photographs, #4315 photographs, #4317 photographs, #4319 photographs, #4321 photographs, #4323 photographs, #4325 photographs, #4327 photographs, #4329 photographs, #4331 photographs, #4333 photographs, #4335 photographs, #4337 photographs, #4389 photographs, #4391 photographs, #5675 photographs (text found: "Camulos has been owned by just two families since the end of war for Mexican ind")

## 35. del Valle L12: LEGACY ONLY

- **Page:** lp_oaklandtrib110418 (webmaster's note 2)
- **Error:** "Prior to the Great Drought of 1864-65, he owned the entire Rancho San Francsico"
- **Correction:** Before the drought of 1862–64, Ygnacio held an undivided share (about 5/11) alongside his stepmother, her husband and his half-siblings. Fix the typo 'Francsico'.
- **Source:** robinson_storyofvalencia_1967 (5/11; drought 1862–64); lw2052 (drought 1862–64); perkins-rsf-1957.

## 36. Reynolds RL1: RECORD, NOT FOUND

- **Page:** signal/reynolds/part41.html
- **Error:** <title> '... | 46: Surrey'
- **Correction:** '41: Baron of Casteca'
- **Source:** The same page's heading and contents.html (C20).
- **Craft:** #2107 articles

## 37. Reynolds RL2: IN CRAFT

- **Page:** signal/reynolds/bibliography.html
- **Error:** 'Burrows, D.H. "Del Valle." Hist. Society of Southern Calif., 1902.'
- **Correction:** 'Barrows, H.D.' (the year needs checking against the HSSC index: NEEDS_VERIFICATION)
- **Source:** H.D. Barrows was the HSSC writer on the del Valles, cited by Perkins 1957 (perkins-rsf-1957).
- **Craft:** #2171 articles (text found: "Hist. Society of Southern Calif., 1902.")

## 38. Reynolds RL3: IN CRAFT

- **Page:** signal/reynolds/bibliography.html
- **Error:** 'Williamson, R.S. Pacific Railroad Survey. Washington, DC 1952.'
- **Correction:** The 1850s (Pacific Railroad Reports, vol. 5, 1856); exact edition NEEDS_VERIFICATION
- **Source:** Williamson's survey was 1853; 1952 duplicates the year on the line above.
- **Craft:** #2171 articles (text found: "Williamson, R.S. Pacific Railroad Survey. Washington, DC 1952.")

## 39. Reynolds RL4: IN CRAFT

- **Page:** signal/reynolds/bibliography.html
- **Error:** 'Van Valkenberg, R.'
- **Correction:** 'Van Valkenburgh, Richard'
- **Source:** part04's editor's note spells it Van Valkenburgh.
- **Craft:** #2171 articles (text found: "Van Valkenberg, R.")

## 40. Reynolds RL5: IN CRAFT

- **Page:** signal/reynolds/part46.html
- **Error:** 'on April 25, a special train ... rolled up to the Saugus Station ... President Benjamin Harrison' ... 'met the president at Saugus'
- **Correction:** Harrison did not stop at Saugus; he passed on Apr 24, 1891 (reported the 25th); the Santa Barbara delegation met him at Ventura. Add an editor's note, as on sauguscafe.
- **Source:** The site's own sauguscafe note 4 (rev. 2019).
- **Craft:** #2117 articles (text found: "rolled up to the Saugus Station")

## 41. Reynolds RL6: IN CRAFT

- **Page:** signal/reynolds/part46.html
- **Error:** 'Joseph H. Tolfree started the Saugus Eating House'
- **Correction:** James Herbert Tolfree (died 1897); Joseph H. may have run it later
- **Source:** sauguscafe notes 2 and 5 (1897 death reports, grave marker).
- **Craft:** #2117 articles (text found: "Joseph H. Tolfree started the Saugus Eating House")

## 42. Reynolds RL7: LEGACY ONLY

- **Page:** signal/reynolds/reynolds-castaicethnography.htm
- **Error:** 'By Jerry Reynolds For The Signal date?'
- **Correction:** Supply the column date, or 'date unknown'
- **Source:** Placeholder is visible on the live page.

## 43. Reynolds RL8: RECORD, NOT FOUND **(CARE)**

- **Page:** signal/reynolds/reynolds121484.htm (CARE)
- **Error:** the column gives a locational description of an archaeological site (not repeated here)
- **Correction:** Not a factual error. Flag for tribal consultation about redacting the locational description before migration.
- **Source:** CARE policy for this project: no site locations.
- **Craft:** #2177 articles
- **CARE:** TRIBAL CONSULTATION FIRST. The Bowers Cave column tells readers how to find an archaeological site. It was already imported, as article #2177, live at /articles/bowers-cave: disable it (disable_bowers_cave.php) until consultation; do not quote the locational passage anywhere (Nathan, 29 September 2026).

## 44. Reynolds RL9: IN CRAFT

- **Page:** signal/worden/lw011399.htm
- **Error:** quotes Reynolds on Hi Jolly as dying 'Dec. 16, 1903' at 75
- **Correction:** Dec 16, 1902, aged 74 (what Reynolds actually wrote in part23)
- **Source:** part23; Quartzsite plaque (lw3451) (C21).
- **Craft:** #12168 articles (text found: "Dec. 16, 1903")

## 45. Reynolds RL10: LEGACY ONLY

- **Page:** hs9032.htm (webmaster's note)
- **Error:** 'Reynolds' conculsion'
- **Correction:** 'conclusion'
- **Source:** Typo.

## 46. Reynolds RL11: LEGACY ONLY

- **Page:** sauguscafe.htm (notes)
- **Error:** 'Narragansut'; 'limted'; 'plausable'
- **Correction:** 'Narragansett'; 'limited'; 'plausible'
- **Source:** Spelling.

## 47. Reynolds RL12: RECORD, NOT FOUND

- **Page:** signal/reynolds/part58.html
- **Error:** '*See note below' plus a note saying 'The section at the top in italics is inaccurate', but the top section now reads $6,000 (the corrected figure)
- **Correction:** Reword the note to say the text has been corrected from Reynolds' $11,000 / Dec 17, 1925, and add the 1922 date to the text
- **Source:** The page itself (C7).
- **Craft:** #2141 articles

## Reprinted errors

Reynolds lists five errors that also sit on his chapter pages (the Hart L12 and L13, del Valle L1 and L7 entries above, and a set still unflagged: part54 Hap-A-Lan morgue and cowboy-suit burial; part28 "Naval Academy" and "$5,000"; part25 "mayor"; part26 the Tejon sale before 1861). They are the same errors, counted once above.
