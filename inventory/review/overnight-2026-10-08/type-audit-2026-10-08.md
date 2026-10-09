# Type audit: photographs, documents and articles by what they are (8 October 2026, overnight)

Claude, read only. Nothing was moved, retyped or written to the database. For Nathan to read before the retype.

Starting points: `inventory/review/type-census-2026-10-07.md` and `.json`, `inventory/review/retype-dry-run-2026-10-07.md`, TODO.md ("Type by content", "The retype read"), CHANGELOG 7 October (the rulings: heldAs, assetRole "facsimile", the three merges). Rule under test (Nathan): "a scanned newspaper page is an article that survives as an image. The scan is how we hold it, not what it is. Type follows the thing."

## What was read

- **Craft, read only:** every photograph, document, article, obituary and collection (2,424 entries: 1,561 photographs, 779 articles, 62 documents, 15 collections, 7 obituaries), with their legacyUrl, sourceLine, legacyHeadline, heldAs and the files on featuredImage, recordImages and documentFiles. Dump script `storage/runtime/type-audit-2026-10-08/lite.php`.
- **The scans themselves.** The photograph import has run since the census, so most candidates now hold their page scans in Craft (`web/uploads/archive-media/legacy/`). For every candidate the first pages were opened and looked at, and up to eight pages per record (562 files, 258 records) were put through Tesseract so the bylines, folios and mastheads could be searched (`storage/runtime/type-audit-2026-10-08/ocr/`, summary `ocrsum.txt`). Where Craft holds less than the page, the file on Reggie was opened instead (`/mnt/reggie/scvhistory.com/gif/` and the flipbook folders under `scvhistory/files/`).
- **The legacy pages on Reggie** for every photograph and document (2,125 of 2,372 records with a legacyUrl have a page there; `pages.py`, `heads.json`). These are Leon's pages: their header lines are his description of the scan, not the scan. Where this report quotes a header line, it says so.
- Tesseract misses text on some pages (it returned nothing on four Pacific News pages that plainly carry a headline and byline), so an OCR blank was never read as "no byline": every verdict below rests on a page looked at, or says it does not.

## Counts

| Set | Sure article | Likely | Unsure | Not an article | Total |
| --- | --- | --- | --- | --- | --- |
| Photographs the census called article sure, likely or unsure | 19 | 1 | 6 | 3 | 29 |
| Photographs the census called web articles | 12 (one, #2689, already moved) | 2 | 0 | 4 | 18 |
| Photographs the census missed | 0 | 1 | 2 | (see section 4) | 3 |
| Documents the census called article sure, likely or unsure (35 still documents) | 24 | 0 | 8 | 3 | 35 |
| Document added since the census (#31723) | | | | 1 | 1 |

The census's 19 "sure" photographs and its 19 here are not the same 19: #4435 drops to likely (no page scan is held anywhere) and #4269 rises to sure (the clipping is a full news story). Two of the census's documents are gone: #27374 was merged into article #1434 and #28295 moved to articles on 7 October.

`heldAs` exists on articles and documents but is set on no record (0 of 841).

## 1. Photographs the census flagged as article scans (29)

### Sure: an article, with the page held (19)

| Record | What the scan shows | Publication and date: printed on the held page, or only in Leon's header |
| --- | --- | --- |
| #2963 California Gold | Masthead "NEW-YORK OBSERVER", dateline "NEW YORK, SATURDAY, OCTOBER 1, 1842.", news item "CALIFORNIA GOLD.--A letter from California, dated May 1, speaking of the discovery of gold..." No byline. | Printed. **Craft holds only the masthead strip** (lw2181a.jpg); the dateline and the item are lw2181b, c and d on Reggie, not in Craft. |
| #3049 Petroleum Versus Petroleum | Headline "PETROLEUM VERSUS PETROLEUM.", unsigned item quoting the J.M. Curtis & Son assay of the Placerita white oil. | Not printed on the held page. The Land of Sunshine, December 1900 rests on Leon's text. |
| #4269 Castaic Dam Site Explodes Into Action | Headline "Castaic Dam Site Explodes Into Action", a full news story ("Drill. Blast. Dig, then drill again.") with captions "STEEL WORK" and "INSPECTION TOUR". No byline. | Not printed on the clipping. "Compass, Pacific Telephone, Vol. 3 No. 11, August 8, 1966" is Leon's header; Reggie holds a separate masthead image (gif/compasspubbox.jpg), not opened. The census had this as likely, the retype read as unsure; the scan is a news story, not a captioned photo spread. |
| #4691 The Terrifying Tale of the Runaway Drone | Two-page spread, pp. 20-21: "THE TERRIFYING TALE OF THE RUNAWAY DRONE", deck "High in the air, two rocket-firing jets vs. an old, pilotless World War II Hellcat...", "BY MICHAEL FROST". | Not printed on the spread. Pageant, May 1957 is Leon's. The kicker "THE BATTLE OF PALMDALE" is not on the held spread. |
| #4783 Tragedy on the Sweetwater | p. 10: "TRAGEDY ON THE SWEETWATER", "by Carl W. Breihan", folio "GOLDEN WEST". Seven pages held. | Golden West printed; "1967" printed in the held pages. |
| #4909 India Never Had It So Good | p. 24: "India NEVER HAD IT SO GOOD", deck "The Fort (The Outside Only, Sahib) And Incidentals Add Up To $182,000". No byline. | Not printed on the three held pages. TV Guide, 1957 is Leon's. |
| #4919 The Move to Secede from Los Angeles County | Cover "California Journal, April 1976"; p. 117: "REVOLT OF THE BALKANS / THE MOVE TO SECEDE FROM LOS ANGELES COUNTY", "By BOB SIMMONS", folio "APRIL 1976". | Printed. |
| #4967 "We Hereby Resolve..." | Cover "Santa Clarita Valley Magazine, Winter 1987-88"; inside "City Council: ... Resolve...", each member-elect in turn. No single byline. | Printed. |
| #5073 The Old Road to Los Angeles | Cover "Pacific News No. 249 April 1984"; p. 8: "SOUTHERN PACIFIC'S SOLEDAD CANYON ROUTE / THE OLD ROAD TO LOS ANGELES", "By Bruce Kelly", folio "8 . APRIL 1984". | Printed. |
| #5087 Our Country's Mysterious Monsters | Cover "OLD WEST ... Fall, 1969"; p. 25: "OUR COUNTRY'S MYSTERIOUS MONSTERS", deck "Tall tales, big yarns and weird happenings were part of the Old West, too!", "By J. K. PARRISH / Illustrated by Al Martin Napoletano", folio "Fall, 1969". | Printed. A magazine article, not a book excerpt (see "Read from a description"). |
| #5137 The Story of Dan Press | "DAN PRESS / By Larry Warren", "STOCK CAR RACING", "JULY 1981". | Printed. |
| #5145 Commuting: Southern Pacific's Saugus Line | Cover "PACIFIC RAIL NEWS, NOVEMBER 1991, ISSUE 336", cover line "Railfanning in the '90s: COMMUTING SOLEDAD"; p. 28: "COMMUTING / SOUTHERN PACIFIC'S SAUGUS LINE"; next page "TEXT AND PHOTOGRAPHY BY RANDY KELLER". | Printed. |
| #5205 Will and Charlie | "WILL ... CHARLIE / By ARNOLD MARQUIS"; folios "Frontier Times", "June-July, 1967"; a cover "TRUE WEST / Frontier Times, July, 1967". | Printed. |
| #5233 The Boom Days of Staging | p. 22: "The BOOM DAYS", "By WADDELL F. SMITH / Photos Courtesy Author", folio "True West"; later "The Boom Days of Staging (Continued from page 24)". | True West printed; the held folios read "July-August, 1966", not "August 1966" as Leon's header has it. Page 4 of the held set also carries a second article, "CATTLE AND KIDS, By S. E. (Ed) Bogart" (the facing page). |
| #5245 Aggie | "J.C. Agajanian, the Haberdasher's Best Friend" printed on a held page; folios "NOVEMBER 1964", "CAR LIFE". | Car Life, November 1964 printed. "By Bill Libby" was not found on the seven held pages; it is in Leon's header only. Page order is already a TODO item (8 October afternoon). |
| #5361 He Sets the Sky On Fire! | Cover "The Saturday Evening Post, October 13, 1951"; p. 3 "He Sets the Sky on Fire!", "By FRANK J. TAYLOR"; colour photo pages of the Bermite plant. | Printed. |
| #5383 Course of the Month: Indian Dunes | "COURSE OF THE MONTH / INDIAN DUNES", "By the Staff of DIRT BIKE", "JUNE 1973"; cover "DIRT BIKE ... JUNE 1973". | Printed. |
| #5385 Southern Pacific and Texas & New Orleans Class M-4 2-6-0s | "Southern Pacific and Texas & New Orleans class M-4 2-6-0s", deck "Some of these turn-of-the-century Moguls served into the 1950s", "BY ANDY SPERANDEO", folio "72 AUGUST 1994", "Drawn for MODEL RAILROADER". | Printed. The printed head spells out Texas & New Orleans (the current title already does). |
| #5425 Indian Dunes' First Competitive Event | Cover "DUNE BUGGY, NOVEMBER 1970"; inside "INDIAN DUNES ..." news story, no byline, folio "November 1970". | Printed (cover reads "Dune Buggy"; "Road Test Dune Buggy, Vol. 2 No. 11" is Leon's). |

### Likely (1)

- **#4435 New Indian-Frontier Village.** Craft holds two images, and both are the article's illustrations: a map ("INDIAN VILLAGE", Mint Canyon, Route 14) and a photograph of carved figures. No page of the article is held in Craft, and Reggie has no PDF or flipbook for lw2724. The article ("By Margaret Romer, Desert Magazine, April 1965") exists here only as Leon's transcription. An article by content, but nothing held lets the text, byline or date be checked.

### Unsure (6)

- **#4735 Ramona (Cine-Miroir).** p. 461 of "Cine-Miroir": "RAMONA / Grand roman d'amour, tire du celebre film des Artistes Associes, par HENRI ST-GERMAIN", "Chapitre IV (suite)", with a summary of earlier chapters. A serial installment of a film novelization, with a printed byline. Article only if serial fiction installments count.
- **#5325 Squadron of Giant Tillers.** Towner Manufacturing Co. sheet: "TOWNER MANUFACTURING COMPANY ... SANTA ANA, CALIFORNIA", "SQUADRON [OF] GIANT TILLERS SPEED CALIFORNIA'S CASTAIC [DAM]", "PHOTOS AND REPORT BY RAY DAY, WESTERN EDITOR FOR CONSTRUCTION EQUIPMENT MAGAZINE". A manufacturer's job report with a journalist's byline: trade literature or an article reprint.
- **#5199 Filmnyheter, 21 November 1921.** The whole issue: "21 NOVEMBER 1921 | N:o 40", "Stockholm 1921, Zetterlund & Thelanders Boktryckeri", with other pieces inside ("EN NY FILM MED DOUGLAS FAIRBANKS", "KNUT HAMSUNS MARKENS GRODE PA FILM"). An issue, not an article.
- **#5363 The Pacific Mineralogist, December 1941.** The whole issue, 32 pages: "The Pacific Mineralogist / Published Semi-Annually by the LOS ANGELES MINERALOGICAL SOCIETY / Vol. IX DECEMBER, 1941 No. 2", with advertisements. The Mint Canyon piece Leon names is one article inside it (not located in the held pages read).
- **#5347 Ramona (Cine-Romanzo).** First page "ROMANZO COMPLETO", 1929. A complete novelization filling an issue: a book in magazine form.
- **#4429 Boy, 6, at Center of MacCaulley Paternity Swindle.** Not one thing. Its featured image is a newspaper clipping, "CLEARS 'BILL' HART IN AMAZING CONFESSION" (tlp_hornelltribune052623); its record images are four more clippings, among them "TWO-GUN HERO SORRY FOR GIRL IN PATERNITY CASE ... By WILLIAM G. CAYCE, International News Service Staff Correspondent (Copyright, 1923...)", "Woman Says Paternity Charge Against Film Star Is Frameup" and "HART CASE 'CAT'S PAW' BACK WITH MOTHER", plus the press photograph (asset 14701). Five articles held as scans on one photograph record, none with a record of its own.

### Not an article (3)

- **#3185 Introduction to "Injun and Whitey."** The image is a book title page: "THE GOLDEN WEST BOYS / INJUN AND WHITEY / A STORY OF ADVENTURE / BY WILLIAM S. HART ... GROSSET & DUNLAP PUBLISHERS NEW YORK". The text is Leon's transcription of the book's introduction. A book, not an article.
- **#4437 "The Beale Cut."** The image is a photograph of the cut with pipelines; no text. The text is Leon's transcription of a passage from Latta's "Saga of Rancho El Tejon" (1976). Photograph plus book excerpt.
- **#4451 Purpose of Fort Tejon.** The image is a map (Tulare Lake to Fort Tejon); no running text. The text is Leon's transcription from Wilke and Lawton (1976). Map plus book excerpt.

## 2. Photographs the census called web articles (18)

These have no scan: the legacy page is the thing. Quoted from the page on Reggie.

**Sure (12):** a byline and date at the head of a born-digital or newsletter piece.
- #2689 Story of Sulphur Springs School: "By Leon Worden / Wednesday, March 5, 1997" (already an article since 7 October).
- #2691 Have We Sucked the Life Out of Our River?: "By Leon Worden / SCVHistory.com | Wednesday, October 6, 2004".
- #2721 SCV Historical Society Supporters Thanked: "By Leon Worden. / Heritage Junction Dispatch | January-February 2020." A printed newsletter article, not only a web piece.
- #4441 Placerita White Oil, Piru Oil Displayed at 1901 Pan-American Exposition: "By Leon Worden / | January 3, 2015."
- #4689 Will the Real Buffalo Vernon Please Stand Up?: "By Leon Worden | Principal research and documentation by Tricia Lemon Putnam | SCVHistory.com, March 2017."
- #4931 Perkins-Lamkin SCV History Images Come Home: "By Leon Worden. / SCVHistory.com | Sunday, September 17, 2017."
- #5071 County Museum System Honors Hart Volunteers: "By Leon Worden. / Wednesday, April 18, 2018."
- #5235 Rock Arch at Needham Ranch Moved, Again: "By Leon Worden / Photos by Stan Walker and Leon Worden, Video by Jessica Boyer. / SCVHistory.com | Thursday, August 30, 2018."
- #5399 Who Knew? Perkins' SCV History Books: "By Leon Worden. / SCVHistory.com | June 2, 2019."
- #5589 Remains of 2 Ancient Bison Found in Castaic Oil Fields: "By Leon Worden. / Discovery 1950 | SCVHistory.com, May 3, 2020."
- #5605 Winkler Homestead Road: "By Leon Worden. / SCVHistory.com | October 13, 2020."
- #5767 Santa Clarita: Where It All Started: "By Leon Worden / | February 1997 / (c)1997 SCVHistory.com". An essay (the site's concise history), not a news piece.

**Likely (2):** dated, no byline.
- #4187 Some Notes About Alec Mentry's Birth Name: "SCVHistory.com | March 1, 2014", headed by an 1884 voter register image.
- #5229 Weireter, Christopher Honored by Camulos Museum: "> NEWS ... SCVHistory.com | August 26, 2018."

**Not an article as a record (4):** the record is a photograph that carries someone else's article text.
- #3957, #3959, #3961 Placerita Oil Field: Leon's own photographs of 17 November 2013; the page below each carries two SCVNews.com articles ("Placerita Oil Field Has New Owners, By Leon Worden, Tuesday, December 17, 2013" and "A New Owner for Placerita Oil Field on Sierra Highway, By Leon Worden, Thursday, February 21, 2013") and a Berry Petroleum 8-K extract. The articles are two things the three photographs share; neither has a record.
- #5765 Sulphur Springs Celebrates Quasquicentennial: a photo gallery with a webmaster's note. It also holds a scanned Signal clipping (sg19980426ssusd): "Sulphur Springs marks 125th anniversary, By NICOLE M. CAMPBELL, Signal Staff Writer", folio "THE SIGNAL, Sunday, April 26" (the year reads 1996 in OCR, 1998 in the file name; not checked by eye). The article is the clipping, not the record.

## 3. Documents (35 still documents, plus #31723)

### Sure: an article (24)

Ten have a clipping held; the clipping was opened.

| Record | The clipping shows | Publication and date |
| --- | --- | --- |
| #20087 A Missing Man | "A Missing Man." and the item ("On the 28th of last August Mr. Mentry..."). | Not on the clipping. "Los Angeles Herald, Thursday, November 11, 1886" is the header of Leon's page ("News reports courtesy of Stan Walker"). |
| #20090 Skeleton in the Mountains | "SKELETON IN THE MOUNTAINS / Remains of a Man Dead Twelve Years Are Found". | Not on the clipping (same header). |
| #20093 Mentre's Bones Found | "MENTRE'S BONES FOUND. / Mysterious Disappearance of Twelve Years Ago Explained." | Not on the clipping (same header). |
| #20096 First California Well | "FIRST CALIFORNIA WELL", quoting the Meadville Tribune-Republican. | Not on the clipping (same header). |
| #20107 Son of Pioneer Oilman Dies | "Son of Pioneer Oilman Dies / SANTA BARBARA, March 16--Funeral services for Arthur Charles Mentry, 73..." | Not on the clipping. "Los Angeles Times, March 17, 1954" is Leon's. |
| #26983 Bogus History | "BOGUS HISTORY. / Angelenos the First Argonauts." Prints Stearns's letter of July 8, 1867, signed "ABEL STEARNS", and a letter signed "A. ROBINSON". | Not on the clipping. "Santa Cruz Sentinel, August 27, 1885" is Leon's. |
| #28057 Death of a Monster Bear | Masthead "Los Angeles Herald. WEDNESDAY, ......JULY 28, 1875."; "Death of a Monster Bear." An editor's lead-in, then the letter "EDITOR HERALD: ..." signed "JOHN LANG. Lang's Station, July 17th." | Printed. A newspaper item that carries a signed letter to the editor. |
| #28291 Peter Pitchess to Speak at Newhall CC Luncheon | Headline "Peter Pitchess to Speak at Newhall CC Luncheon", and a separate photo clipping, "SPOTLIGHTED as Newhall-Saugus-Valencia Chamber of Commerce's Man, Woman of the Year...", credit "The News photo". | Not on the clippings. "Van Nuys Valley News, May 27, 1975" is Leon's. |
| #28293 Cityhood forum announced | Folio "Sunday, January 11, 1987", the "CITYHOOD" logo box, and a short item ("The cityhood movement is going public..."). **No headline printed**: the record's title is a description. | Date printed; paper not named on the clipping. |
| #28310 Cityhood Backers: Who Are They? | Folio "The Newhall Signal ... Newhall, California, Sunday, January 4, 1987"; "Cityhood Backers -- Who Are They?", "By Laurel Suomisto, Signal Staff Writer"; jump page "City Backers' Backgrounds". | Printed. |

Fourteen are transcriptions on Leon's pages, with no scan held; the byline and publication are as the page prints them: #31308 ("By Jim Holt. / The Signal | Thursday, November 14, 2019."), #31310 ("By Marisa Gerber, James Queally, Hannah Fry and Sarah Parvini / Los Angeles Times | Friday, November 15, 2019"), #31314 ("By Stephen K. Peeples. / SCVTV/SCVNews.com | Friday, November 15, 2019."), #31316, #31318, #31320, #31322, #31324 (sourceLine "Commentary by Sandy Banks. Los Angeles Times | Saturday, November 16, 2019"), #31326 ("By Emily Alvarenga. / The Signal | Sunday Evening, November 17, 2019."), #31328 ("By Sandy Banks and Laura Newberry / Los Angeles Times | Monday, November 18, 2019"), #31330 ("By Marisa Gerber / Los Angeles Times | Monday, November 18, 2019"), #31332 ("By Tammy Murga. / The Signal | Tuesday, November 19, 2019."), #28305 ("By Perry Smith, AM-1220 KHTS | Tuesday, August 12, 2014"), #31412 ("Interview by Leon Worden / Signal City Editor / Sunday, April 25, 2004 / (Television interview conducted April 1, 2004)"). Eight of the Los Angeles Times records are disabled and would stay so.

### Unsure (8)

- **#31312 Press Conference at SCV Sheriff Station.** "SCVTV | Friday, November 15, 2019." A summary of a televised press conference, no byline. A news report or a source document about a video.
- **#28287 Connie Worden-Roberts Remembers.** The page on Reggie (flipbook sc19872007, page 71, printed folio 67): "City Formation Committee Member / Connie Worden-Roberts Remembers . . .", first person, no byline, in the City's 20th-anniversary book "Celebrating 20 Years of Success". A signed-in-voice section of a commemorative book.
- **#31336 Principal Vince Ferry Honored.** "William S. Hart Union High School District. / April 6, 2020." A district announcement.
- **#28307 Connie Worden-Roberts Memorial Bridge.** "[City of Santa Clarita] - On Tuesday, October 4, 2016, the City of Santa Clarita hosted..." A City release.
- **#20104 Scofield's eulogy.** Leon's page: "Ode to the Man Behind Mentryville / By DEMETRIUS G. SCOFIELD / SCVHistory.com | August 15, 2013 / The Following Eulogy Was Delivered By ... D.G. Scofield, Upon The Death Of ... Mentry In 1900." A speech; no scan, and no first publication named.
- **#18991 A Brief Sketch of the Notorious Bandit.** "Entered according to act of Congress, in the year 1874, by V. WOLFENSTEIN ... TIBURCIO VASQUEZ! A Brief Sketch of the Notorious Bandit." A pamphlet (Reggie has gif/je4001.jpg, not opened).
- **#28055 John Lang** and **#20099 C.A. Mentry**, in Pen Pictures From the Garden of the World (1889). Book entries; Leon calls the Lang one a "pay-to-play 'article'". Both sourceLines say "pp. 539-540"; two different sketches on the same two pages is possible but not checked against the book.

### Not an article (3, plus #31723)

- **#28281 A Brief History of the Push for Self-Government.** The scan (cw9901_001) is a **letter**: "January 18, 1999 / Stacy Miller / Economic Development / City of Santa Clarita ... Dear Stacy, Following our conversation and a brief review of the FAX you sent, I am submitting some comments..." Unpublished correspondence; the title and the "By Connie Worden-Roberts" line are Leon's.
- **#31306** A personal letter from the Muehlberger family (read at the vigil).
- **#31334** Mike Kuhlman's email to Hart District families ("Distributed by email to Hart District families, January 12, 2020").
- **#31723** (new since the census) Scott Newhall's Bancroft oral history, "Interviewer: Suzanne B. Riess, 1988-1989": a book-length transcript.

The other documents (election returns, rosters, minutes, bylaws, the death certificate, the photo key, the Friends of Hart Park resolution, the 2003 proposal) are not articles and were not in question.

## 4. Candidates the census signal missed

The census looked for a byline or "Publication | date" in the header. These were found by reading the scans and the legacy pages instead.

- **#2773 Charles Lindbergh Goes Gliding (likely).** The scan is a magazine page: running head "MAY, 1930 / POPULAR SCIENCE MONTHLY / 41", headline "Lindbergh Goes Gliding", deck "Three Pages of Pictures Tell the Story of the Lone Eagle's Life at Lebec, Calif., Where He Mastered the Art of Gliding". Eight images held. A pictorial feature. Leon's caption names Edwin W. Teale; no byline was found on the held pages.
- **#4803 Verbiesen Ranch & Car Collection Damaged by Fire** and **#5113 Buzz Barton Home Torched (unsure).** Leon's news posts on photo pages ("> NEWS", dated 29 May 2017 and 22 April 2018), no byline. Same kind as #5229.
- **Twelve photograph records hold newspaper clippings as images, none with an article record:** #4429 (five clippings, above); #5611 (Los Angeles Times "Arms Plant Blast Kills 1; 17 Injured" and The Newhall Signal "Thursday, Feb. 4, 1954 ... Cause undetermined for worst Bermite 'blow', two killed, 16 injured"); #5765 (the Signal clipping above); #5565 (Oakland Tribune, Sunday, June 30, 1957, "California Camels Only a Memory"); #5523 (Los Angeles Times, "Whooping it up at the RODEO ... By GRACE KINGSLEY"); #5473 (two Los Angeles Times clippings, "PRINCE AAGGIE BUY CONFIRMED" and the Sunday Times Farm and Orchard Magazine of April 26, 1925); #5435 (Los Angeles Times Farm and Garden Magazine, May 22, 1932); #5399 (a captioned Signal photo of 1980, sg19801221perkins, "Photo by Tony Mason"); #5255 (Newhall Signal & Saugus Enterprise, Wednesday, December 13, 1978, Supertrain at the Saugus depot); #5251 (four 1938 to 1945 clippings on Ione Reed); #2795 (Los Angeles Times, April 29, 1935, "HOOT GIBSON RODEO LURES SPORT FANS"); #2701 (Signal item on C.C. Cristadoro's Hart statue). Found by file name and then OCR, so this list may be short (sg6002, on #27366 and #5399, read as no text and may be a photograph).
- **"News Reports" pages are photographs, not articles.** #4641 (a Wide World press photo with its typed caption sheet, "URGES IRANIAN WOMEN TO ASSERT THEMSELVES ... 8/23/50"), #5047 (the image is a photograph of the 1949 plaque), #5611 (a press photo of a woman being helped to a car): opened, the image is a photograph and the reports are Leon's transcriptions below it. #4979 and #5117 to #5121 and #5211 are the same kind by their pages' captions (wire photos); their images were not opened. The transcribed reports are articles with no record.
- **Articles inside ephemera** (the item stays what it is): the Placerita Camp brochure #2747 carries "ORO ORO! By A. G. RIVERA"; Hoot Gibson's 1931 program book #5523 carries Grace Kingsley's piece; the Tepee Rock Shop guide #4785 prints "FIRST EDITION / COPYRIGHT 1966 / BY GARY L. GUNTHER / Published by TEPEE ROCK SHOP".
- **Articles that are not articles:** #865 Surveyor's Map Showing Lyon's Station (a map, kept as an article by the 7 October merge) and #849 Diseño ~1843 (a map; its image is the diseño). The OCR sweep of the 112 ephemera records (programs, brochures, papers, letters, ads) found no other article hiding as ephemera.

## 5. Duplicates across sections

- **Resolved on 7 October:** Perkins 1957 (#27374 into #1434), lw2304a (#3179 into #865), and lw030597 (#12558 into #2689, which moved to articles). The census's pairing of #1444 with #2689 over assets 1812 and 1813 was wrong, as ERRORLOG says; they are different pieces sharing two images.
- **No legacy page is now held in two sections** (checked across all 2,424 entries).
- **One object, three records, two sections:** article #865 is the full view of the 1875 County Surveyor's map; photographs #3181 (lw2304b, closeup) and #3183 (lw2304c, ultra closeup) are the same map, same title. #865 links no image, though lw2304a.jpg and lw2304a_large.jpg are in the volume (whether an asset record exists for them was not checked).
- **Shared asset, not identity:** asset 1765 (lw2439b.jpg, an Overland Stage wagon) is on photograph #3661 and article #1434; it illustrates the article.
- **Shared text, not identity:** the two SCVNews articles on #3957, #3959 and #3961; the Land of Sunshine article on the nine Piru pictures (#3051 to #3067).
- **Same section, not in TODO's pair list:** articles #12574 and #12542 are one Worden column (lw092596, old/ and current copies, both 5,011 characters). Already called identical in inventory/review/duplicate-slugs-2026-10-04.md.
- **One legacy page, several documents** (by design, one record per piece): sw_petermentre.htm (four), lat20191116shs.htm (five), lat20191118shs.htm (two), scvtv20191115shs.htm (two), gt8702.htm (two), scvhs2000minutes.pdf (two).

## Read from a description

Things stated as fact in earlier reports, or held in Craft, that rest on Leon's header or a title rather than the scan, and that the scan contradicts or does not show. Reported, not fixed.

1. **#4735: "Byline: none printed"** (retype dry run). The scan prints "par HENRI ST-GERMAIN" under the headline.
2. **#5087: "a book excerpt reprinted in a magazine"** (retype dry run), read from Leon's "Except from 'Our Country's Mysterious Monsters.'" The scan is a magazine article under that headline, by J.K. Parrish; nothing on the page names a book.
3. **#4785: "'By Tepee Rock Shop' names the publisher"** (retype dry run). The scan names an author: "COPYRIGHT 1966 BY GARY L. GUNTHER / Published by TEPEE ROCK SHOP".
4. **#28281: "essay or journal article"** (type census, likely). The scan is a dated letter to Stacy Miller of the City's Economic Development office. Its title and byline are Leon's.
5. **#4435 counted among "photographs holding article scans"** (type census, TODO). It holds no page of the article: a map and a photograph from it. The article text is a transcription with nothing held to check it against.
6. **#2963**: Craft holds only the masthead strip; the item and dateline it is about are on Reggie only.
7. **#4429: "the record is an original International Newsreel press photograph"** (retype dry run). Its featured image is a newspaper clipping, and it holds five clippings in all, with the photograph as one record image.
8. **Publication and date in sourceLine on #20087, #20090, #20093, #20096, #20107, #26983 and #28291** come from Leon's page headers. None of these clippings prints a masthead or date. They may well be right; they are not read from the scan.
9. **#5233: "True West | August 1966"** (Leon's header, carried into the retype read). The held folios read "July-August, 1966".
10. **#28293: the title "Cityhood forum announced"** reads like a headline, but the clipping prints none.
11. **#2773: "photo essay by Edwin W. Teale"** (Leon's caption). No byline was found on the eight held pages.
12. **#5245: "By Bill Libby"**, **#4691: Pageant, May 1957**, **#4909: TV Guide, 1957**, **#3049: The Land of Sunshine, December 1900**: the byline or publication is not on the pages held, only in Leon's header.

## What the model would need (gaps, not a design)

The 7 October rulings added `heldAs` (clipping, magazine pages, transcription only, web) on articles and documents, and an asset role "facsimile". Against what the scans show, these gaps remain:

- **Where publication and date come from.** sourceLine does not say whether the paper and date are printed on the held scan or supplied by Leon's header (item 8 above). Authorship has `authorshipBasis` for exactly this; publication and date have nothing like it.
- **How much of the thing is held.** heldAs names the form but not completeness: #2963 holds the masthead without the item, #4435 the illustrations without the page, #5245 seven pages out of order.
- **Records that hold several pieces.** One photograph record can hold five articles as images (#4429), or carry two articles' text shared with two other photographs (#3957 to #3961). One record per identity has no place for "photograph plus the clippings filed with it".
- **Things larger than an article.** A whole issue (#5363, #5199), a complete novel printed as an issue (#5347), a serial installment (#4735), a book title page with its introduction (#3185), a book section (#28287, the Pen Pictures entries, the Latta and Wilke excerpts), an oral history (#31723). Article, document and photograph each fit some of these badly.
- **Articles printed inside ephemera** (#2747, #5523, #4785): the program or brochure is the item, and the bylined piece inside it has no home.
- **Where letters, releases and emails go.** A letter published in a newspaper (#28057), an unpublished letter (#28281, #31306), an email (#31334), and press releases (#31336, #28307, #4861) are each in some section today, with no stated line between document and article.
- **Publishers.** None of the magazines (Pageant, Golden West, California Journal, Pacific News, Pacific Rail News, Old West, True West and Frontier Times, Car Life, Saturday Evening Post, Dirt Bike, Model Railroader, Dune Buggy, Popular Science Monthly, Cine-Miroir, Filmnyheter) has an organization record for publishedBy.
- **heldAs is unset everywhere** (0 of 841 articles and documents).

## Files

- Working files and scripts (read only): `storage/runtime/type-audit-2026-10-08/` (`lite.php` the Craft dump, `pages.py` the Reggie page reader, `ocr.sh` and `ocr/` the OCR, `ocrsum.txt`, `heads.json`, `sweep.txt`). Nothing was added to scripts/import.
