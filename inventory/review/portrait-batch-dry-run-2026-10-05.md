# Portrait batch, 5 October 2026: dry run

Dry run of `scripts/import/import_portrait_batch_2026_10_05.php` (`$APPLY = false`), reading `inventory/review/portrait-batch-2026-10-05.json`. Nothing was written to the database. Prepared by Claude for Nathan, following his rulings of 5 October 2026 on `inventory/review/portrait-census-2026-10-05.md`.

## Counts

- Imports: 33 portraits from the mirror, plus Jereann Bowman's obituary clipping kept as the original of her crop (34 files).
- Crops (edited images, enhancedFrom the original): 2, John Amos Ward (from asset #1973) and Jereann Bowman (from the clipping above).
- Links to images already in Craft: 6. Dean #14804, Lyon #2316, Hon #14933 and Connie Worden-Roberts #30549 become the portrait; Ruth Newhall's #11344 (photograph record #4583) joins her record images; Ward's #1973 is the crop's original, already among his record images.
- Empty provenance fields filled on linked assets: 4 (Dean, Lyon, Hon, Ward's original). Nothing non-empty is overwritten.
- Source fault: 1 (Pavelka's caption).
- Editor note: 0. Chico López's "Likeness" note is HELD (see below).
- Refusals: none. No record already had a portrait; no stored filename or mirror twin is already in Craft.

Every import records: the mirror path (`legacySourcePath`), the source page in `source`, the caption as printed (`photoCaptionExt`) where one is printed, the credit as printed (`photoCredit`), the legacy code (`photoSourceCode`) where there is one, `sourceChecksum` sha256 of the file as taken, `acquiredDate` 2026-10-05, `license` unknown, `rightsNote` "No permission to republish is established.", alt "Portrait of NAME". Stored in archiveMedia/legacy/ under the mirror file name.

## Held: the Chico López note

Not written. His own legacy page, `/scvhistory/us8502.htm` (his record's legacyUrl), is a photograph of him: "Half-tone print in the California Historical Society collection ... the man in the photo was a cousin of the gold discoverer. This photograph has often been wrongly identified as Francsico Lopez, the gold discoverer." Credit: "US8502: 9600 dpi jpeg from digital image in USC Digital Library; California Historical Society catalog No. CHS-8502." Files on the mirror: gif/us8502.jpg (800 by 962), us8502_large.jpg, us8502_orig.jpg; not in Craft. His record's body and "Not to be confused with" note already say a portrait of Chico exists. The census missed it. A "no likeness is known" note would be false; US8502 waits on Nathan's word as his portrait.

## Pavelka

The image is imported with no caption on the asset: the printed caption carries the misprinted year. A source fault on his record (faultField featuredImage) records it: as printed, the caption under his portrait; reading, end of watch 15 November 2003; basis, the page it heads (a press release carried 24 July 2012) says he was killed 15 November 2003. `faultRecord` is an Entries field, so the asset cannot be named there; and its configured sources list only candidacies, documents and elections, though the Stearns and Mentry faults already name person records, so the API accepts it.

## The Cherry check (report only, nothing imported)

The full 1946 San Fernando High School yearbook page is not on the mirror. What is there: `gif/tlp_leoncherrymug_large.jpg` (960 by 323), a strip from the yearbook's armed-forces section: one photograph of a smiling sailor at the right, beside one paragraph that names three men: "★Leon Cherry entered the Navy September 16, 1942. He served in the South and Central Pacific on the U.S.S. Thatcher. While serving in the Central Pacific he was killed on May 27, 1944. Walter D. Rushton, '40, entered the Navy in 1942. As a Seaman first class he has served in the Pacific area and has the Aleutian Islands, Tarawa, and Tokyo campaigns to his credit. Harral V. Grant, W'45, left school to enter the Navy in January, 1943. He served in the European, Asiatic and American areas as Gunner's Mate and is at present stationed in South America." No caption under the photograph. `gif/tlp_leoncherrymug.jpg` (800 by 1183) is the photograph alone, already cropped. The researcher's notes linked from the page (`warmemorial/docs/tlp_ww2_leoncherry_research.docx`, Tricia Lemon Putnam) say of it: "Photo of Leon Cherry, San Fernando H.S. 1946 Yearbook 'Armed Forces' compilation. note: I have not confirmed that this is him but it seems to match. It does not give his class year. Compilation includes past students from several different graduating classes." The only other strip from the same yearbook on the mirror, `gif/ww2_wallacewillett_sfhs1946yearbook.jpg`, pairs one photograph with one paragraph about one man ("★ Wallace Willett ..."), so the layout pairs photographs with paragraphs, but Cherry's paragraph names three, and the star (the casualty mark) and first place go to Cherry. Finding: not identifiably Perry Leon Cherry as printed. The page does not caption it, and the person who found it recorded that she had not confirmed it. Keep the hold.

## Plan, as the script printed it

```
DRY RUN
==============================================================================
#29764 Deputy Constable J. Edward "Ed" Brown   import as legacy/sd2401_large.jpg (1600 x 1994, 3,874,235 bytes); set as the portrait (featuredImage)
#29766 Constable John S. "Jack" Pilcher        import as legacy/pilcher_jack3.jpg (800 x 1093, 388,891 bytes); set as the portrait (featuredImage)
#29778 Deputy Hagop "Jake" Kuredjian           import as legacy/kuredjian_jake.jpg (720 x 933, 216,252 bytes); set as the portrait (featuredImage)
#29784 Deputy David W. March                   import as legacy/davidmarch.jpg (800 x 1067, 292,994 bytes); set as the portrait (featuredImage)
#29776 Deputy Arthur E. Pelino                 import as legacy/obituary_pelinoarthure.jpg (800 x 1000, 257,542 bytes); set as the portrait (featuredImage)
#29786 Officer Matthew Pavelka                 import as legacy/matthewpavelka.jpg (798 x 1130, 413,204 bytes); set as the portrait (featuredImage)
#29774 Officer George M. Alleyn                import as legacy/sg4701b.jpg (150 x 200, 8,824 bytes); set as the portrait (featuredImage)
#29768 Officer Walter C. Frago                 import as legacy/sg4701a.jpg (150 x 200, 9,449 bytes); set as the portrait (featuredImage)
#29770 Officer Roger D. Gore                   import as legacy/sg4701d.jpg (150 x 200, 8,977 bytes); set as the portrait (featuredImage)
#29772 Officer James E. Pence Jr.              import as legacy/sg4701c.jpg (150 x 200, 9,059 bytes); set as the portrait (featuredImage)
#30247 Steven D. Zimmer                        import as legacy/lw2428.jpg (800 x 1120, 318,495 bytes); set as the portrait (featuredImage)
#30223 Donald M. Benton                        import as legacy/donbenton.jpg (638 x 900, 460,355 bytes); set as the portrait (featuredImage)
#30219 Kevin Lynch                             import as legacy/obituary_kevingarylynch.jpg (800 x 1035, 259,833 bytes); set as the portrait (featuredImage)
#30211 Francis T. Claffey                      import as legacy/sg19720614claffey01_large.jpg (2400 x 2385, 3,812,748 bytes); set as the portrait (featuredImage)
#29601 Ken Striplin                            import as legacy/sc1202.jpg (800 x 1048, 554,347 bytes); set as the portrait (featuredImage)
#29332 Katie Hill                              import as legacy/katiehill_officialportrait2019_large.jpg (1638 x 2048, 875,352 bytes); set as the portrait (featuredImage)
#29316 Keith Richman                           import as legacy/obituary_keithrichman.jpg (800 x 781, 276,577 bytes); set as the portrait (featuredImage)
#29314 Pete Knight                             import as legacy/sg042504.jpg (250 x 304, 26,427 bytes); set as the portrait (featuredImage)
#29288 Kathryn Barger                          import as legacy/lw3109_large.jpg (2400 x 3000, 4,408,743 bytes); set as the portrait (featuredImage)
#29284 Michael D. Antonovich                   import as legacy/lw2427_large.jpg (1600 x 2306, 1,105,185 bytes); set as the portrait (featuredImage)
#28713 Clara Stroup                            import as legacy/stroup_clara.jpg (150 x 200, 14,150 bytes); set as the portrait (featuredImage)
#28703 Louis Brathwaite                        import as legacy/brathwaite-louis.jpg (150 x 200, 11,624 bytes); set as the portrait (featuredImage)
#25445 Gloria Mercado-Fortine                  import as legacy/gloriamercadofortine.png (800 x 1075, 426,911 bytes); set as the portrait (featuredImage)
#20226 William Wirt Jenkins                    import as legacy/ap2222_large.jpg (2400 x 3610, 9,383,665 bytes); set as the portrait (featuredImage)
#18869 Remi Nadeau                             import as legacy/reminadeau-chrisman.jpg (334 x 504, 89,472 bytes); set as the portrait (featuredImage)
#16388 Charles Crocker                         import as legacy/hs3021.jpg (800 x 1028, 183,300 bytes); set as the portrait (featuredImage)
#15919 Harry Carey                             import as legacy/lw2178.jpg (800 x 1024, 125,494 bytes); set as the portrait (featuredImage)
#18702 Tom Mix                                 import as legacy/lw2317a_large.jpg (1600 x 2133, 1,702,356 bytes); set as the portrait (featuredImage)
#2585 Richard Rioux                            import as legacy/rr1.jpg (225 x 311, 22,590 bytes); set as the portrait (featuredImage)
#18663 Randy Wicks                             import as legacy/randywicks1995_karzinphoto_large.jpg (1200 x 875, 683,569 bytes); set as the portrait (featuredImage)
#15477 Ruth Newhall                            import as legacy/rn3004.jpg (400 x 437, 33,368 bytes); set as the portrait (featuredImage)
#2579 Darryl Manzer                            import as legacy/darrylmanzer2020.jpg (800 x 945, 231,397 bytes); set as the portrait (featuredImage)
#28675 Earl Schmidt                            import as legacy/sk5003_large.jpg (2400 x 3179, 4,040,068 bytes); set as the portrait (featuredImage)
#29780 Officer Clarence Wayne Dean             link #14804 clarencewaynedean.jpg (800 x 983); fill empty: source, license, rightsNote; set as the portrait (featuredImage)
#20224 Sanford Lyon                            link #2316 ap1334.jpg (800 x 1047); fill empty: photoCredit, source, license, rightsNote; set as the portrait (featuredImage)
#18616 Dan Hon                                 link #14933 danhon.jpg (200 x 231); fill empty: source, license, rightsNote, alt; set as the portrait (featuredImage)
#16418 Connie Worden                           link #30549 lw9501_large.jpg (2400 x 3058); nothing to fill; set as the portrait (featuredImage)
#15477 Ruth Newhall                            link #11344 lw2849a_large.jpg (2400 x 3156); nothing to fill; added to recordImages
#28677 Jereann Bowman                          import as legacy/sg19950324bowman_large.jpg (4443 x 1950, 11,394,963 bytes); added to recordImages
#28677 Jereann Bowman                          import as legacy/jereann-bowman-cropped-from-sg19950324bowman.jpg (860 x 1200, 2,000,309 bytes); set as the portrait (featuredImage)
#570 John Amos Ward                            link #1973 johnward_dorothyward.jpg (800 x 823); fill empty: photoCredit, courtesyOf, source, license, rightsNote; already among record images
#570 John Amos Ward                            import as legacy/john-amos-ward-cropped-from-johnward_dorothyward.jpg (370 x 823, 237,316 bytes); set as the portrait (featuredImage)
#29786 Officer Matthew Pavelka                 source fault create: E.O.W. 11-15-2013 → 15 November 2003
    reading: End of watch 15 November 2003
    basis: The caption under his portrait on SCVHistory.com prints an end of watch in 2013. The page it heads, a Burbank Police Department press release carried on July 24, 2012, says he was gunned down on November 15, 2003, and that his killer was sentenced nearly nine years later; a 2013 date cannot stand in a page of 2012. His record gives 15 November 2003.
#28132 Francisco "Chico" López                editor note "Likeness": HELD, not written
    No likeness of Francisco "Chico" López is known. Searched 5 October 2026: his page on SCVHistory.com, every page of the legacy site that names him or the gold discoverer, the site's image files and the flipbook text. The photographs of the 1959 Placerita Park pageant show an actor playing López in a reenactment of the gold discovery, and are not his likeness.
    HELD: Contradicted by the archive: his own legacy page, /scvhistory/us8502.htm (the record's legacyUrl), is a photograph of him: "Half-tone print in the California Historical Society collection ... the man in the photo was a cousin of the gold discoverer. This photograph has often been wrongly identified as Francsico Lopez, the gold discoverer." (US8502: 9600 dpi jpeg from digital image in USC Digital Library; California Historical Society catalog No. CHS-8502; gif/us8502.jpg 800 by 962, us8502_large.jpg, us8502_orig.jpg on the mirror; not in Craft). His record's body and its "Not to be confused with" note already say a portrait of Chico exists. The census missed it. The note is not written; Nathan to rule on US8502 as his portrait.
------------------------------------------------------------------------------
imports 34, crops 2, links 6 (with empty fields to fill: 4), already imported 0; source fault 1; note held
REFUSED: none
------------------------------------------------------------------------------
```

## Contact sheet

| Record | File | Size | Caption as printed | Credit as printed | Link or import | Crop |
|---|---|---|---|---|---|---|
| #29764 Deputy Constable J. Edward "Ed" Brown | `sd2401_large.jpg` (mirror `gif/sd2401_large.jpg`) | 1600 x 1994, 3,874,235 bytes | "Deputy Constable Ed Brown, April 8, 1924." | none printed | import into featuredImage |  |
| #29766 Constable John S. "Jack" Pilcher | `pilcher_jack3.jpg` (mirror `gif/mugs/pilcher_jack3.jpg`) | 800 x 1093, 388,891 bytes | (none printed) No caption line; the image heads the article on him. The same face as gif/mugs/pilcher_jack1.jpg (150 by 200), captioned "Constable Jack Pilcher." on /scvhistory/lasd053113brown.htm; the larger file is used. | none printed | import into featuredImage |  |
| #29778 Deputy Hagop "Jake" Kuredjian | `kuredjian_jake.jpg` (mirror `gif/mugs/kuredjian_jake.jpg`) | 720 x 933, 216,252 bytes | (none printed) No caption line; the only photograph on his memorial article. | none printed | import into featuredImage |  |
| #29784 Deputy David W. March | `davidmarch.jpg` (mirror `gif/mugs/davidmarch.jpg`) | 800 x 1067, 292,994 bytes | (none printed) No caption line; heads his obituary. | none printed | import into featuredImage |  |
| #29776 Deputy Arthur E. Pelino | `obituary_pelinoarthure.jpg` (mirror `gif/obituary_pelinoarthure.jpg`) | 800 x 1000, 257,542 bytes | (none printed) No caption line; heads his obituary. | none printed | import into featuredImage |  |
| #29786 Officer Matthew Pavelka | `matthewpavelka.jpg` (mirror `gif/mugs/matthewpavelka.jpg`) | 798 x 1130, 413,204 bytes | (none printed) The printed caption names him and gives an end-of-watch date that is misprinted; it is recorded only in the source fault made by this script, not on the image. | none printed | import into featuredImage |  |
| #29774 Officer George M. Alleyn | `sg4701b.jpg` (mirror `gif/sg4701b.jpg`) | 150 x 200, 8,824 bytes | "George Alleyn" Name printed under the photograph, first of four in a row. | none printed | import into featuredImage |  |
| #29768 Officer Walter C. Frago | `sg4701a.jpg` (mirror `gif/sg4701a.jpg`) | 150 x 200, 9,449 bytes | "Walter Frago" Name printed under the photograph, second of four in a row. | none printed | import into featuredImage |  |
| #29770 Officer Roger D. Gore | `sg4701d.jpg` (mirror `gif/sg4701d.jpg`) | 150 x 200, 8,977 bytes | "Roger Gore" Name printed under the photograph, third of four in a row. | none printed | import into featuredImage |  |
| #29772 Officer James E. Pence Jr. | `sg4701c.jpg` (mirror `gif/sg4701c.jpg`) | 150 x 200, 9,059 bytes | "James Pence" Name printed under the photograph, fourth of four in a row. | none printed | import into featuredImage |  |
| #30247 Steven D. Zimmer | `lw2428.jpg` (mirror `gif/lw2428.jpg`) | 800 x 1120, 318,495 bytes | "Steven D. Zimmer (b. April 7, 1947), a real estate attorney, was appointed on or about July 23, 1999, as vice president (head) of The Newhall Land and Farming Co.'s Newhall Ranch Division, succeeding SVP James M. Harter, who retired at an early age." The first sentence of the text under the photograph. | LW2428: 9600 dpi jpeg from smaller jpeg. | import into featuredImage |  |
| #30223 Donald M. Benton | `donbenton.jpg` (mirror `gif/mugs/donbenton.jpg`) | 638 x 900, 460,355 bytes | (none printed) No caption beyond "Click to enlarge."; heads the White House announcement of his nomination. | none printed | import into featuredImage |  |
| #30219 Kevin Lynch | `obituary_kevingarylynch.jpg` (mirror `gif/obituary_kevingarylynch.jpg`) | 800 x 1035, 259,833 bytes | (none printed) No caption line; heads his obituary. | none printed | import into featuredImage |  |
| #30211 Francis T. Claffey | `sg19720614claffey01_large.jpg` (mirror `gif/sg19720614claffey01_large.jpg`) | 2400 x 2385, 3,812,748 bytes | "June 14, 1972 — This photograph of Francis Thomas "Fran" Claffey ( 1925-2015 ) was used in his campaign advertisement for Santa Clarita Community College District (COC) Board of Trustees." | 9600 dpi jpeg from original 2x2-inch negative, Signal Photo Archive, Santa Clarita Valley Historical Society collection. | import into featuredImage |  |
| #29601 Ken Striplin | `sc1202.jpg` (mirror `gif/sc1202.jpg`) | 800 x 1048, 554,347 bytes | "Ken Striplin, 2012. Striplin succeeded Ken Pulskamp as city manager of the City of Santa Clarita in 2012." | SC1202: 9600 dpi jpeg from digital image. | import into featuredImage |  |
| #29332 Katie Hill | `katiehill_officialportrait2019_large.jpg` (mirror `gif/katiehill_officialportrait2019_large.jpg`) | 1638 x 2048, 875,352 bytes | (none printed) No caption beyond "Click to enlarge."; her official portrait (by the file name), on the article on her resignation. | none printed | import into featuredImage |  |
| #29316 Keith Richman | `obituary_keithrichman.jpg` (mirror `gif/obituary_keithrichman.jpg`) | 800 x 781, 276,577 bytes | (none printed) No caption line; heads his obituary (KHTS AM-1220, August 3, 2010). | none printed | import into featuredImage |  |
| #29314 Pete Knight | `sg042504.jpg` (mirror `gif/sg042504.jpg`) | 250 x 304, 26,427 bytes | "Pete Knight on April 1, 2004" | none printed | import into featuredImage |  |
| #29288 Kathryn Barger | `lw3109_large.jpg` (mirror `gif/lw3109_large.jpg`) | 2400 x 3000, 4,408,743 bytes | "Kathyrn Barger was elected in November 2016 to succeed Michael D. Antonovich as supervisor for Los Angeles County's 5th District, which includes the Santa Clarita Valley." The first sentence of the text under the photograph; "Kathyrn" as printed. | LW3109: 9600 dpi jpeg from Los Angeles County Board of Supervisors. | import into featuredImage |  |
| #29284 Michael D. Antonovich | `lw2427_large.jpg` (mirror `gif/lw2427_large.jpg`) | 1600 x 2306, 1,105,185 bytes | "Michael Dennis Antonovich (b. August 12, 1939, in L.A.), Los Angeles County 5th District Supervisor (1980-2016). Mid-1990s photograph." | LW2427: 9600 dpi jpeg from original print. | import into featuredImage |  |
| #28713 Clara Stroup | `stroup_clara.jpg` (mirror `gif/mugs/stroup_clara.jpg`) | 150 x 200, 14,150 bytes | (none printed) No caption; alt text "Clara Stroup"; heads her obituary (The Signal, February 8, 2004). | none printed | import into featuredImage |  |
| #28703 Louis Brathwaite | `brathwaite-louis.jpg` (mirror `gif/mugs/brathwaite-louis.jpg`) | 150 x 200, 11,624 bytes | (none printed) No caption; alt text "Louis Brathwaite"; heads his obituary (The Signal, November 15, 2001). | none printed | import into featuredImage |  |
| #25445 Gloria Mercado-Fortine | `gloriamercadofortine.png` (mirror `gif/mugs/gloriamercadofortine.png`) | 800 x 1075, 426,911 bytes | (none printed) No caption; alt text "Gloria Mercado-Fortine", beside her nomination as a 2015 Man and Woman of the Year nominee. | none printed | import into featuredImage |  |
| #20226 William Wirt Jenkins | `ap2222_large.jpg` (mirror `gif/ap2222_large.jpg`) | 2400 x 3610, 9,383,665 bytes | "California Ranger-turned-Castaic land baron and oil explorer William Wirt (aka Willoby) Jenkins, b. 10-12-1835 d. 10-19-1916." | AP2222: 19200 dpi jpeg from copy print. | import into featuredImage |  |
| #18869 Remi Nadeau | `reminadeau-chrisman.jpg` (mirror `gif/reminadeau-chrisman.jpg`) | 334 x 504, 89,472 bytes | (none printed) No caption; alt text "Remi Nadeau", in "Uncle Remi Nadeau" by Marylin Nadeau Chrisman (2007), whose webmaster's note says its subject is the grandson (born 1867) of the freighter of the same name. | none printed | import into featuredImage |  |
| #16388 Charles Crocker | `hs3021.jpg` (mirror `gif/hs3021.jpg`) | 800 x 1028, 183,300 bytes | "With six steady blows of a silver hammer, on Sept. 5, 1876, Southern Pacific President Charles Crocker drove the golden spike that linked northern and southern California at Lang Station in today's Canyon Country, completing the Southern Pacific's San Joaquin Valley Line and joining California with the rest of the nation via the transcontinental railroad — the western portion of which Crocker supervised." | HS3021: 2400 dpi jpeg from printed copy. | import into featuredImage |  |
| #15919 Harry Carey | `lw2178.jpg` (mirror `gif/lw2178.jpg`) | 800 x 1024, 125,494 bytes | "Harry Carey, signed publicity photograph, c. 1930s." | LW2178: 9600 dpi jpeg | import into featuredImage |  |
| #18702 Tom Mix | `lw2317a_large.jpg` (mirror `gif/lw2317a_large.jpg`) | 1600 x 2133, 1,702,356 bytes | "Publicity photo (6"x8"), circa 1930, of actor Tom Mix, who starred in films shot in the Newhall area and every other Western filming location of the silent era and the early sound period." The first sentence of the text under the photograph. Chosen over LW2030 (Mix with Tony, online image only) and HS9031 (a painted portrait): a solo portrait from an original print. | LW2317a: 9600 dpi jpeg from original print purchased 2013 by Leon Worden | import into featuredImage |  |
| #2585 Richard Rioux | `rr1.jpg` (mirror `oldtownnewhall/gif/rr1.jpg`) | 225 x 311, 22,590 bytes | (none printed) No caption; alt text "Richard 'Doc' Rioux", on the index page of his columns. | none printed | import into featuredImage |  |
| #18663 Randy Wicks | `randywicks1995_karzinphoto_large.jpg` (mirror `gif/randywicks1995_karzinphoto_large.jpg`) | 1200 x 875, 683,569 bytes | "Randy Wicks, 1995. Photo: Kevin Karzin/The Signal." As printed, less the instruction "Click to enlarge." | Photo: Kevin Karzin/The Signal. | import into featuredImage |  |
| #15477 Ruth Newhall | `rn3004.jpg` (mirror `gif/rn3004.jpg`) | 400 x 437, 33,368 bytes | "Ruth at home at the Piru Mansion in the 1970s." | none printed | import into featuredImage |  |
| #2579 Darryl Manzer | `darrylmanzer2020.jpg` (mirror `gif/mugs/darrylmanzer2020.jpg`) | 800 x 945, 231,397 bytes | (none printed) No caption; alt text "Darryl Manzer", heading his column of April 9, 2020. Chosen over KT0110, his youth portrait in Mentryville. | none printed | import into featuredImage |  |
| #28675 Earl Schmidt | `sk5003_large.jpg` (mirror `gif/sk5003_large.jpg`) | 2400 x 3179, 4,040,068 bytes | "Earl Schmidt is the writer's grandfather." In "All About Earl Schmidt" by Cassandra Skaggs (2017). | none printed | import into featuredImage |  |
| #29780 Officer Clarence Wayne Dean | asset #14804 `clarencewaynedean.jpg` | 800 x 983 | "Officer Dean, E.O.W. 1-17-1994." | none printed for the portrait | link into featuredImage |  |
| #20224 Sanford Lyon | asset #2316 `ap1334.jpg` | 800 x 1047 | "Sanford Lyon (b. Nov. 20, 1831, in Machias, Maine; d. Nov. 30, 1882, in Newhall) was an early SCV entrepreneur." | AP1334: 9600 dpi jpeg from copy print. | link into featuredImage. He had an identical twin, Cyrus; the AP1334 caption is the only identification of which twin this is. |  |
| #18616 Dan Hon | asset #14933 `danhon.jpg` | 200 x 231 | (none printed) | none printed | link into featuredImage. 200 by 231 pixels: small. |  |
| #16418 Connie Worden | asset #30549 `lw9501_large.jpg` | 2400 x 3058 | (none printed) | "Photo by Gary Choppe" printed on the image | link into featuredImage. The asset already carries full provenance from worden_roberts_obituaries_2026_10_05.php; nothing is added to it. |  |
| #15477 Ruth Newhall | asset #11344 `lw2849a_large.jpg` | 2400 x 3156 | "Twenty-somethings Scott and Ruth Newhall strike a Kennedyesque pose in 1936 (before there was such a thing) as they prepare for a "three-year adventure" circumnavigating the globe in a 42-foot sailboat." | none printed | link into recordImages. The image of photograph record #4583 (Scott and Ruth Newhall, 1936), a double portrait: it joins her record images, and RN3004 is her portrait. |  |
| #28677 Jereann Bowman | `sg19950324bowman_large.jpg` (mirror `gif/sg19950324bowman_large.jpg`) | 4443 x 1950, 11,394,963 bytes | (none printed) The clipping heads her obituary page; printed under the photograph within the clipping: "Jereann Bowman". The page prints "Click image for archival scan." and the headline "Bowman, Force Behind Continuation School, Dies." | none printed (byline in the clipping: By Jill Dolan, Signal Staff Writer) | import into recordImages |  |
| #28677 Jereann Bowman | `sg19950324bowman_large_crop-jereann-bowman.jpg` | 860 x 1200, 2,000,309 bytes | "Jereann Bowman" The name printed under the photograph in the clipping. | none printed | import-edited into featuredImage | Cropped to her photograph from the 4443 by 1950 pixel clipping: 860 by 1200 pixels from 60 pixels left and 255 pixels down; the printed name, headline and text left out; nothing else changed. Made with macOS sips; no content credential. |
| #570 John Amos Ward | asset #1973 `johnward_dorothyward.jpg` | 800 x 823 | "John and Dorothy Ward. Image courtesy of their granddaughter, Tara Vaughn Garza." | Image courtesy of their granddaughter, Tara Vaughn Garza. | link into recordImages. The original double portrait is already in Craft (asset #1973, 800 by 823, among his record images), so it is not imported again; it becomes the crop's enhancedFrom. The mirror file, not copied to incoming, has the sha256 recorded here. |  |
| #570 John Amos Ward | `johnward_dorothyward_crop-john-ward.jpg` | 370 x 823, 237,316 bytes | "John and Dorothy Ward. Image courtesy of their granddaughter, Tara Vaughn Garza." The caption of the double portrait; the crop shows him only. | Image courtesy of their granddaughter, Tara Vaughn Garza. | import-edited into featuredImage | Cropped to his half (the right) of the 800 by 823 double portrait: 370 by 823 pixels from 430 pixels left; an edge of his wife's hair remains at the left; nothing else changed. Made with macOS sips; no content credential. |

## Files placed in inventory/incoming

All copied unchanged from the mirror (byte-identical, checked) except the two crops, made with macOS sips from mirror files (never altered). Each is listed in `inventory/incoming/OUTSTANDING.md`, section 4, "portrait batch of 5 October 2026, a dry run waiting on Nathan"; `check_incoming.php` passes (84 files waiting with a reason).

- `sd2401_large.jpg`
- `pilcher_jack3.jpg`
- `kuredjian_jake.jpg`
- `davidmarch.jpg`
- `obituary_pelinoarthure.jpg`
- `matthewpavelka.jpg`
- `sg4701b.jpg`
- `sg4701a.jpg`
- `sg4701d.jpg`
- `sg4701c.jpg`
- `lw2428.jpg`
- `donbenton.jpg`
- `obituary_kevingarylynch.jpg`
- `sg19720614claffey01_large.jpg`
- `sc1202.jpg`
- `katiehill_officialportrait2019_large.jpg`
- `obituary_keithrichman.jpg`
- `sg042504.jpg`
- `lw3109_large.jpg`
- `lw2427_large.jpg`
- `stroup_clara.jpg`
- `brathwaite-louis.jpg`
- `gloriamercadofortine.png`
- `ap2222_large.jpg`
- `reminadeau-chrisman.jpg`
- `hs3021.jpg`
- `lw2178.jpg`
- `lw2317a_large.jpg`
- `rr1.jpg`
- `randywicks1995_karzinphoto_large.jpg`
- `rn3004.jpg`
- `darrylmanzer2020.jpg`
- `sk5003_large.jpg`
- `sg19950324bowman_large.jpg`
- `sg19950324bowman_large_crop-jereann-bowman.jpg`
- `johnward_dorothyward_crop-john-ward.jpg`

## For Nathan

1. Chico López: US8502 is a portrait of him on his own legacy page. Import it as his portrait instead of the note?
2. Sanford Lyon: the AP1334 caption is the only identification of which twin (Sanford or Cyrus) is pictured.
3. Ruth Newhall: RN3004 (400 by 437, at home in the 1970s) becomes the portrait and the 1936 double portrait (#11344) joins her record images. Say if you meant #11344 as the portrait.
4. Small files: the four CHP officers, Stroup and Brathwaite are 150 by 200; Hon 200 by 231; Rioux 225 by 311; Pete Knight 250 by 304. No larger copy is on the mirror.
5. Choices made where the census offered more than one: Pilcher pilcher_jack3 (800 by 1093, heads his own article) over pilcher_jack1 (150 by 200, captioned); Tom Mix LW2317a (original print, circa 1930); Claffey the 1972 campaign photograph; Manzer the 2020 column portrait over his youth portrait KT0110.
6. To apply: set `$APPLY = true` locally and run, **MacBook**: `ddev craft exec "eval(file_get_contents('scripts/import/import_portrait_batch_2026_10_05.php'))"`. Then move the imported files to `inventory/incoming/done/` with manifest entries.
