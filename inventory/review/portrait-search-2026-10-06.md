# Portrait search, 6 October 2026

Report only: nothing was imported or changed. Read-only search of the legacy mirror on Reggie (`/Volumes/Reggie/SCVHistory/scvhistory.com`) and of Craft. Prepared by Claude for Nathan, on his request to run the portrait search again over every person record. Full data, with every name pattern searched, in `portrait-search-2026-10-06.json`.

## How the search was done

- Craft read only: every persons entry (status any) with no featuredImage; fields read through the field layout.
- Mirror: /Volumes/Reggie/SCVHistory/scvhistory.com, read only. 23,736 .htm/.html pages indexed (flipbook basic-html page scans excluded): title, page text, every <img> with alt text and the text before and after it.
- Names: title, fullName, personAliases, personSearchNames; first name with nickname/formal swaps (Bill/William, Tom/Thomas, Ken/Kenneth and so on); optional middle name, initial or quoted nickname between first and last; Jr./Sr./II-IV and Dr./Rev. stripped.
- Searches per record: images whose alt text, caption after, or text just before names the person; photo pages (SCVHistory.com XX0000 titles and galleries) whose caption names the person anywhere, including long "from left" lists; images whose alt text carries the surname; image file names (577,071 image files on the drive) holding first+last; legacy pages with the name in the title.
- Each candidate checked for the file on the drive (sips for dimensions) and for a Craft asset of the same file name (and _large twin) with its relations. Identifications that depended on the picture were viewed.
- Captions are quoted as printed on the legacy pages, cut where marked "...".

## Counts

- Person records: 208. With no featuredImage: **99**.
- PORTRAIT high: 1 (a likeness of this person alone (or plainly the subject), captioned or named, file on the drive)
- PORTRAIT medium: 2 (a likeness identified by file name or inference only)
- PERSON IN PHOTO high: 4 (a captioned photograph that identifies the person by position; a crop candidate)
- NAMED PORTRAIT, FILE NOT ON DRIVE: 3 (a page shows a portrait named for the person, but the image file was never captured)
- PERSON IN PHOTO only: 10 (group photograph naming the person, carried over from the census)
- WEAK: 4 (background only, ambiguous, a title graphic, or a different person)
- none found: 75 (nothing usable; namesake traps noted)
- New since the 5 October census: 10 records (listed below).

## Against the 5 October census

The census of 5 October (`portrait-census-2026-10-05.md`) covered 293 person records, 217 of them without a portrait. Since then the 5 October portrait batch and the 6 October portrait work filled many of them, and 85 records became rows (persons_to_rows_2026_10_06.php). Of the 99 records still without a portrait, 98 were in the census; Scott Newhall (#31431) is new. The census result for each is in the JSON (`census20261005`).

What is new in this pass:

- **Scott Newhall (#31431)**: not in the census. Four solo likenesses on the drive, best TN1968 (1968, 1600 x 1990).
- **Cathie Wright (#29458)**: census none. Three captioned group photographs; LW3032 (1981 or 1982, seated third from left, already a Craft asset, unlinked) crops well.
- **John Boston (#2576)**: census none. A 2006 Signal photo illustration of him alone with the 1968 Honby photo (sg030506b-honby.jpg), the 1968 photo itself (DM6801, as Walt Cieplik), and a named columnist head shot whose file is not on the drive.
- **Gary Murr (#25399)**, **Sol Taylor (#2582)**, **Philip Ellis Jr. (#28364)**: a portrait named for each sits on a legacy page, but the file was never captured to the drive. Leads only.
- **Sheila Dyer (#30203)**: in the background of the 1974 Reagan photograph (AA7401). Weak.
- **Pauline Harte (#2594)**: a 2005 parade photo whose alt text names her among four, without positions. Weak.
- **Tim Whyte (#2588)** and **Ed Colley (#26589)**: hits explained away (a title graphic; a different Colley).
- **Tom Frew IV (#18783)**: unchanged finding (hs9019), but raised from medium to high: the caption names "Tom Frew IV" and the record now has his birth date. LW2043 (1998) added; it is already a Craft asset (#14233) and photograph #2743 already names him.

Unchanged from the census: Carl Goldman and John Lang (PORTRAIT medium, uncaptioned head shots), and the group photographs for the COC trustees, Pulskamp, Wullschleger, Gary Martin and Linda Storli. Michele Jenkins's two-person photograph CO1501c is the cleanest crop among them.

## Portraits

### Scott Newhall (#31431)  NEW

Result: PORTRAIT high. Census 5 October: not in census (record made 6 October). Names searched: title only. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/tn1968.jpg` | `scvhistory/tn1968.htm` | 800 x 980; tn1968_large.jpg 1600 x 1990 | Scott Newhall in his office at the San Francisco Chronicle, 1968. | PORTRAIT | high | none | no |
| `gif/lw2255.jpg` | `scvhistory/lw2255.htm` | 800 x 593; lw2255_large.jpg 1600 x 1421 | August 27, 1971: UPI Telephoto of Scott Newhall, candidate for mayor of San Francisco. | PORTRAIT | high | none | no |
| `gif/al2069.jpg` | `scvhistory/al2069.htm` | 800 x 572; al2069_large.jpg 1600 x 1145 | Scott Newhall in Newcastle Upon Tyne England, AP Wirephoto, September 15, 1969. | PORTRAIT | high | a distant workman on the tug | no |
| `gif/rn1100.jpg` | `scvhistory/rn1100.htm` | 600 x 482 | Scott Newhall hammers one out at The Signal, probably early 1980s. | PORTRAIT | high | none | no |
| `gif/tn4401.jpg` | `scvhistory/tn4401.htm` | 800 x 952; tn4401_large.jpg 2400 x 2856 | Scott Newhall and his three boys at Stinson Beach (Marin County) in 1944, from left: Skip, Tony, Jon. | PERSON IN PHOTO | high | sons Skip, Tony and Jon | no |
| `gif/tn1989.jpg` | `scvhistory/tn1989.htm` | 800 x 1127; tn1989_large.jpg 1600 x 2254 | Ruth and Scott Newhall at the Piru Mansion (aka Newhall Mansion) in Piru, 1989. Shown with their Great Dane, Victoria. | PERSON IN PHOTO | high | Ruth Newhall | no |
| `gif/rn3005.jpg` | `scvhistory/rn3005.htm` | 800 x 1155; rn3005_large.jpg 2400 x 3464 | Ruth and Scott Newhall at the pasteup table in The Signal's composing room | PERSON IN PHOTO | high | Ruth Newhall | no |
| `gif/gt8065.jpg` | `scvhistory/gt8065.htm` | 800 x 1043; gt8065_large.jpg 1600 x 2009 | Tuesday, June 24, 1980: Los Angeles County Supervisor Baxter Ward (center) and Signal newspaper editor Scott Newhall (right) watch the 1887 Saugus Train Station move | PERSON IN PHOTO | high | Baxter Ward | no |
| `gif/sg9101.jpg` | `scvhistory/sg9101.htm` | 800 x 546 | Oct. 12, 1991: (From left) Scott and Ruth Newhall ... honored at College of the Canyons' Silver Spur fund-raising dinner. ... their son, Skip Newhall, is presenting them | PERSON IN PHOTO | high | Ruth and Skip Newhall | no |
| `gif/hs9030.jpg` | `scvhistory/hs9030.htm` | 800 x 578; hs9030_large.jpg 1600 x 1156 | A young Ruth and Scott Newhall (right) at the San Francisco Chronicle, ca. 1944. | PERSON IN PHOTO | high | Ruth Newhall, George Draper, George De Carvalho and others | no |
| `gif/sg19921027kkk.jpg` | `scvhistory/sg19660901kkk.htm` | 800 x 1359; sg19921027kkk_large.jpg 1600 x 2717 | Top: Scott Newhall (center-left) and son Skip Newhall (center-right) exchange words with Klansmen at the rally, September 17, 1966. Bottom: Scott Newhall at left. | PERSON IN PHOTO | high | Skip Newhall, Klansmen | no |
- `tn1968.jpg`: Viewed: one man, waist up, smiling, arms folded on a desk. Credit as printed: "TN1968: 9600 dpi jpeg from original photograph | Online image only". Best single likeness found.
- `lw2255.jpg`: Viewed: one man, chest up, in front of the sailing ship C.A. Thayer. Credit: "LW2255: 9600 dpi jpeg from UPI Telephoto print, purchased by Leon Worden and donated to SCVHS."
- `al2069.jpg`: Viewed: wire print with its cutline printed across the top; he is at right before the Eppleton Hall. Credit: "AL2069: ... Collection of Alan Pollack".
- `rn1100.jpg`: Viewed: one man at a typewriter, in profile. Small (600 px); no credit printed.
- `sg19921027kkk.jpg`: A newspaper clipping (two photographs) reprinted with his obituary.

Not in the 5 October census (his record was made on 6 October). Already in Craft: photograph #4583 (lw2849a_large.jpg, 1936, with Ruth) names him in photoPeople. Namesake trap: rn0114 "Walter Scott Newhall, 4, in 1912" cannot be this man (born 1914); do not use it for him. None of the files above is a Craft asset.

### Carl Goldman (#30546)

Result: PORTRAIT medium. Census 5 October: PORTRAIT medium. Names searched: title only. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/mugs/carlgoldman.jpg` | `scvhistory/khts081314.htm` | 800 x 1000 | (no caption; heads his KHTS editorial, August 13, 2014) | PORTRAIT | medium | none | no |
| `gif/lw9450a.jpg` | `scvhistory/lw9450a.htm` | 800 x 1071; lw9450a_large.jpg 2400 x 3214 | July 4, 1994 — Carl Goldman, grand marshal of the 1994 SCV Fourth of July Parade in downtown Newhall. With wife Jeri Seratti-Goldman and son Ryan Van Wie | PERSON IN PHOTO | high | Jeri Seratti-Goldman, Ryan Van Wie | no |
- `carlgoldman.jpg`: Viewed: one man, head and shoulders, glasses, studio portrait. Identified by file name and byline position only.

Same as the census.

### John Lang (#18820)

Result: PORTRAIT medium. Census 5 October: PORTRAIT medium. Names searched: title only. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/mugs/johnlang1889.jpg` | `scvhistory/penpictures_johnlang.htm` | 800 x 995 | (no caption; alt text "article") | PORTRAIT | medium | none | no |
| `gif/hs7530.jpg` | `scvhistory/hs7530.htm` | 800 x 590; hs7530_large.jpg 2400 x 1923 | Original portrait of John and Mary Lang and their five surviving children and future daughter-in-law, circa 1889. | PERSON IN PHOTO | medium | Mary Lang, five children, Florence Parr | no |
- `johnlang1889.jpg`: Viewed: an elderly bearded man with another person's hand on his shoulder: a crop from the HS7530 family portrait. HS7530's own caption says "we don't know who's who"; he is the only elderly man in it, and Leon Worden filed the crop under his name.

Same as the census. Trap: hs7530_johnbrodericklang.jpg is his son John Broderick Lang.

## Captioned photographs, crop candidates

### Cathie Wright (#29458)  NEW

Result: PERSON IN PHOTO high. Census 5 October: none found. Names searched: title only. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/lw3032.jpg` | `scvhistory/lw3032.htm` | 800 x 629; lw3032_large.jpg 2400 x 1888 | Seated from left: Craig Johansen(?) ... Connie Worden (later Worden-Roberts) ... Assemblywoman Cathie Wright ... | PERSON IN PHOTO | high | Craig Johansen(?), Connie Worden, Barry Goldwater Jr., Samuel X. Garcia, Walter Smith, Ross B. Hopkins | yes: assets #13269 (lw3032.jpg) and #11530 (lw3032_large.jpg), related to no entry |
| `gif/sc8901.jpg` | `scvhistory/sc8901.htm` | 800 x 525; sc8901_large.jpg 2400 x 1568 | From left: Parks Commissioners Linda Storli and Laurene Weste in front, Jeff Wheeler and Todd Longshore in back; City Councilman Howard "Buck" McKeon; Mayor Jo Anne Darcy; City Councilman Dennis Koontz; State Sen. Cathie Wright; ... | PERSON IN PHOTO | medium | a dozen people | no |
| `gif/lw2758.jpg` | `scvhistory/lw2758.htm` | 800 x 570; lw2758_large.jpg 1600 x 1140 | Partially visible at left are then-Assemblyman William J. "Pete" Knight and state Sen. Cathie Wright. | PERSON IN PHOTO | low | Pete Wilson, Gayle Wilson, Buck McKeon, Pete Knight, Roy Huffington | no |
- `lw3032.jpg`: First VIA luncheon, 1981 or 1982. Viewed: she is third from left, a fair-haired woman turned toward the camera; clear enough to crop from the 2400 px file. Credit: "LW3032: 9600 dpi jpeg from original photograph, collection of Connie Worden-Roberts."
- `sc8901.jpg`: December 1989. The caption calls her "State Sen."; she was in the Assembly until 1992 (as lw3032 says). Do not carry the title.
- `lw2758.jpg`: 1994 rally; partially visible only.

NEW: the census found nothing for her. Her name sits deep in long captions, past where the census looked.

### John Boston (#2576)  NEW

Result: PERSON IN PHOTO high. Census 5 October: none found. Names searched: title only. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/sg030506b-honby.jpg` | `scvhistory/sg030506b-honby.htm` | 800 x 472 | March 5, 2006: Thirty-eight years later, John Boston views a 1968 photo of the Honby Men's Club. | PERSON IN PHOTO | high | the 1968 group photo he holds | no |
| `gif/dm6801.jpg` | `scvhistory/dm6801.htm` | 800 x 545; dm6801_large.jpg 2400 x 1634 | Back row, from left: Andy Kress, Walt Cieplik (who later changed his name to John Boston), David Ray, John Calzia, Darryl Manzer, John Dunkin. | PERSON IN PHOTO | high | nine other Honby Men | no |
| `gif/mugs/boston_john.jpg` | `scvhistory/signal/boston/jbindex.htm` | NOT ON DRIVE | (alt text "John Boston"; heads the index of his Signal history columns) | PORTRAIT | high | none | no |
- `sg030506b-honby.jpg`: Viewed: he alone, at left, face clearly shown, holding the 1968 print. Credited as "photo illustration by Bryan Kneiding" (a composite). Nearest thing to a portrait on the drive.
- `dm6801.jpg`: 1968, Hart High, as a teenager under his birth name; second from left in the back row (the 2006 page says "at left").
- `boston_john.jpg`: Columnist head shot. The file is NOT on the drive (not in the manifests either); it was linked as http://www.scvhistory.com/gif/mugs/boston_john.jpg.

NEW: the census found nothing for him.

### Michele R. Jenkins (#30233)

Result: PERSON IN PHOTO high. Census 5 October: PERSON IN PHOTO only. Names searched: Michele Jenkins, Michele R Jenkins. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/galleries/co1501/images/co1501c.jpg` | `gif/galleries/co1501/index.html` | 800 x 560 | CO1501c: Doreetha Daniels and COC Board President Michele Jenkins / Photo by Jesse Munoz/COC | PERSON IN PHOTO | high | Doreetha Daniels | no |
| `gif/galleries/co1501/images/co1501b.jpg` | `gif/galleries/co1501/index.html` | 800 x 518 | CO1501b: From left: Doreetha Daniels, COC Board President Michele Jenkins, COC Chancellor Dianne G. Van Hook / Photo by Jesse Munoz/COC | PERSON IN PHOTO | high | Doreetha Daniels, Dianne G. Van Hook | no |
| `gif/co1310a.jpg` | `scvhistory/co1310a.htm` | 800 x 550; co1310a_large.jpg 1600 x 1030 | August 19, 2013 — From left: COC Board Members Steven Zimmer and Michele Jenkins; COC Chancellor Dr. Dianne G. Van Hook; Student Trustee Ryan Joslin; COC Board Members Joan MacGregor and Bruce Fortine; ... | PERSON IN PHOTO | high | six others | no |
- `co1501c.jpg`: Viewed: two people; she is at right in academic robes and stole. A clean crop of her half would serve as a likeness. 5 June 2015.

Same as the census; co1501c is the best crop.

### Tom Frew IV (#18783)

Result: PERSON IN PHOTO high. Census 5 October: PERSON IN PHOTO only. Names searched: title only. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/hs9019.jpg` | `scvhistory/hs9019.htm` | 800 x 452 | April 8, 2003 — Former SCV Historical Society President Tom Frew (third from right) receives a key to the city ... "our" Tom, Tom Frew IV. | PERSON IN PHOTO | high | six others (city council and Society) | no |
| `gif/lw2043.jpg` | `scvhistory/lw2043.htm` | 800 x 1021 | August 19, 1998 — The Saugus School bell is inspected at SCV Historical Society headquarters by (from left) City of Santa Clarita engineering technician Bonnie Joseph and SCVHS directors Pat Saletore and Tom Frew IV | PERSON IN PHOTO | high | Bonnie Joseph, Pat Saletore | yes: asset #14233 (lw2043.jpg), related to no entry; photograph record #2743 "Saugus (Elementary) School Bell" already names him in photoPeople |
| `gif/jad_scvhs022398a.jpg` | `scvhistory/obituary_joannedarcy.htm` | 6000 x 3686 | SCV Historical Society board installation with Glen Rollins, Patti Rasmussen & Tom Frew, 2-23-1998. | PERSON IN PHOTO | medium | Jo Anne Darcy, Glen Rollins, Patti Rasmussen | no |
- `hs9019.jpg`: Viewed: third from right is an older white-haired man in glasses holding a small award (the key itself is in the hands of the man at center); croppable but small (800 px).
- `jad_scvhs022398a.jpg`: No positions given.

The census rated hs9019 medium because which Tom Frew the record is was unsettled; the record now carries a birth date (Thanksgiving Day, 1928) and the hs9019 caption names Tom Frew IV outright. Trap: tf1000.jpg is Tom Frew II (already his portrait, asset #31356).

## Named portraits whose files are not on the drive

### Gary Murr (#25399)  NEW

Result: NAMED PORTRAIT, FILE NOT ON DRIVE. Census 5 October: none found. Names searched: Gary G. Murr. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/mugs/murr_gary.jpg` | `scvhistory/sg062505.htm` | NOT ON DRIVE | (alt text "Gary Murr"; heads his 2005 death notice: "Funeral services are scheduled Monday for Saugus Union School District board President Gary Murr") | PORTRAIT | high | none | no |
- `murr_gary.jpg`: Identity certain from alt and page; the file is NOT on the drive (linked absolute, http://www.scvhistory.com/gif/mugs/murr_gary.jpg; absent from both manifests).

NEW: the census missed the alt text. Nothing usable on the drive.

### Philip Ellis Jr. (#28364)  NEW

Result: NAMED PORTRAIT, FILE NOT ON DRIVE. Census 5 October: none found. Names searched: Philip C. Ellis, Jr., Philip C. Ellis. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `oldtownnewhall/gif/mugs/ellis_phil.jpg` | `oldtownnewhall/gazette/gazette1101-nrc.htm` | NOT ON DRIVE | (alt text "Phil Ellis"; byline "By PHILIP ELLIS, Chairman, Newhall Redevelopment Committee", Old Town Newhall Gazette, Nov.-Dec. 2005) | PORTRAIT | medium | none | no |
- `ellis_phil.jpg`: The file is NOT on the drive. Identity medium: the record is the Newhall School District board member (1995-2013 candidacies) with six bylined documents; nothing on this page says the committee chairman is the same Philip Ellis, Jr.

NEW.

### Sol Taylor (#2582)  NEW

Result: NAMED PORTRAIT, FILE NOT ON DRIVE. Census 5 October: none found. Names searched: title only. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/worden-coinage0606b.jpg` | `scvhistory/signal/coins/worden-coinage0606.htm` | NOT ON DRIVE | Sol Taylor. (Photo: Leon Worden) | PORTRAIT | high | none | no |
- `worden-coinage0606b.jpg`: In Leon Worden's June 2006 coin column; the record is the coin columnist (writtenBy on 400 coin articles), the same Sol Taylor. The file is NOT on the drive.

NEW. Nothing usable on the drive.

## Group photographs only (as in the census)

### Bruce D. Fortine (#30205)

Result: PERSON IN PHOTO only. Census 5 October: PERSON IN PHOTO only. Names searched: Bruce Fortine. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/co7001.jpg` | `scvhistory/co7001.htm` | 800 x 411; co7001_large.jpg 1600 x 821 | From left: Dr. William Bonelli Jr., Edward Muhl, Bruce Fortine, John Hackney, Peter Huntsinger. | PERSON IN PHOTO | high | four other trustees | no |
| `gif/co1310a.jpg` | `scvhistory/co1310a.htm` | 800 x 550; co1310a_large.jpg 1600 x 1030 | ... COC Board Members Joan MacGregor and Bruce Fortine; ... | PERSON IN PHOTO | high | seven people | no |
| `gif/aa7401.jpg` | `scvhistory/aa7401.htm` | 800 x 559; aa7401_large.jpg 2400 x 1677 | April 22, 1974 — California Gov. Ronald Reagan greets members of the ... Board of Trustees ... Trustee Bruce Fortine ... | PERSON IN PHOTO | high | Reagan, Boyer, Adams, Dyer, Claffey, Rheinschmidt | no |
- `co7001.jpg`: Middle.

Same as the census (aa7401 added). His Silver Spur photograph is on scvnews.com, not the drive.

### Edward Muhl (#30201)

Result: PERSON IN PHOTO only. Census 5 October: PERSON IN PHOTO only. Names searched: Ed Muhl. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/co7001.jpg` | `scvhistory/co7001.htm` | 800 x 411; co7001_large.jpg 1600 x 821 | From left: Dr. William Bonelli Jr., Edward Muhl, ... | PERSON IN PHOTO | high | four other trustees | no |
- `co7001.jpg`: Second from left.

Same as the census.

### Gary Martin (#26587)

Result: PERSON IN PHOTO only. Census 5 October: PERSON IN PHOTO only. Names searched: Gary R Martin. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/tv091613.jpg` | `scvhistory/tv091613.htm` | 800 x 450; tv091613_large.jpg 1600 x 900 | Pictured, from left: City Parks Commissioner Ruthann Levison; Mayor Bob Kellar; City Councilwoman Laurene Weste; CLWA Board Member Gary Martin; City Parks Commissioner Duane Harte. | PERSON IN PHOTO | high | Levison, Kellar, Weste, Harte | no |
- `tv091613.jpg`: September 16, 2013 groundbreaking; fourth from left.

Same as the census.

### Joan W. MacGregor (#30239)

Result: PERSON IN PHOTO only. Census 5 October: PERSON IN PHOTO only. Names searched: Joan Whaling Mac Gregor, Joan Mac Gregor, John Whaling MacGregor (CEDA 2009 misprint), Joan Whaling MacGregor, Joan MacGregor. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/co1310a.jpg` | `scvhistory/co1310a.htm` | 800 x 550; co1310a_large.jpg 1600 x 1030 | August 19, 2013 — From left: ... COC Board Members Joan MacGregor and Bruce Fortine; ... | PERSON IN PHOTO | high | seven people | no |

Same as the census.

### John K. Hackney (#30209)

Result: PERSON IN PHOTO only. Census 5 October: PERSON IN PHOTO only. Names searched: John Hackney. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/co7001.jpg` | `scvhistory/co7001.htm` | 800 x 411; co7001_large.jpg 1600 x 821 | From left: Dr. William Bonelli Jr., Edward Muhl, Bruce Fortine, John Hackney, Peter Huntsinger. | PERSON IN PHOTO | high | four other trustees | no |
- `co7001.jpg`: COC board 1969-1970; fourth from left.

Same as the census.

### Ken Pulskamp (#29599)

Result: PERSON IN PHOTO only. Census 5 October: PERSON IN PHOTO only. Names searched: Kenneth R. Pulskamp. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/obituary_georgeacaravalho02.jpg` | `scvhistory/obituary_georgeacaravalho.htm` | 800 x 790 | All three permanent city managers in 2016: George Caravalho (1988-2002), Ken Pulskamp (2002-2012), Ken Striplin (2012-present). Photo: Gail Morgan. | PERSON IN PHOTO | medium | George Caravalho, Ken Striplin | no |
- `obituary_georgeacaravalho02.jpg`: No positions given; Striplin is identifiable from his own portrait (sc1202), so Pulskamp can be told by elimination only.

Same as the census.

### Kenneth Wullschleger (#28697)

Result: PERSON IN PHOTO only. Census 5 October: PERSON IN PHOTO only. Names searched: Kenneth C. Wullschleger. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/hd7601.jpg` | `scvhistory/hd7601.htm` | 800 x 466 | From left: Dr. H. Clyde Smyth, acting superintendent; Kenneth Wullschleger, school board president; Connie Worden (later -Roberts) ...; Dr. Edgar Fickenscher (standing) | PERSON IN PHOTO | high | Smyth, Connie Worden, Fickenscher | no |
- `hd7601.jpg`: 1976 yearbook photograph; second from left.

Same as the census.

### Linda Storli (#25157)

Result: PERSON IN PHOTO only. Census 5 October: PERSON IN PHOTO only. Names searched: Linda Hovis Storli, Linda H. Storli. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/sc8901.jpg` | `scvhistory/sc8901.htm` | 800 x 525; sc8901_large.jpg 2400 x 1568 | From left: Parks Commissioners Linda Storli and Laurene Weste in front, ... | PERSON IN PHOTO | high | a dozen people | no |
- `sc8901.jpg`: December 1989; front left.

Same as the census.

### Peter F. Huntsinger (#30207)

Result: PERSON IN PHOTO only. Census 5 October: PERSON IN PHOTO only. Names searched: Peter Huntsinger, Pete Huntsinger. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/co7001.jpg` | `scvhistory/co7001.htm` | 800 x 411; co7001_large.jpg 1600 x 821 | From left: Dr. William Bonelli Jr., Edward Muhl, Bruce Fortine, John Hackney, Peter Huntsinger. | PERSON IN PHOTO | high | four other trustees | no |
- `co7001.jpg`: Far right.

Same as the census.

### William G. Bonelli Jr. (#30199)

Result: PERSON IN PHOTO only. Census 5 October: PERSON IN PHOTO only. Names searched: Dr. William G. Bonelli Jr., William Bonelli, Bill Bonelli, Dr. Bill Bonelli. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/co7001.jpg` | `scvhistory/co7001.htm` | 800 x 411; co7001_large.jpg 1600 x 821 | From left: Dr. William Bonelli Jr., Edward Muhl, ... | PERSON IN PHOTO | high | four other trustees | no |
- `co7001.jpg`: Far left.

Same as the census. Trap: mugs/billbonelli.jpg (alt "Bill Bonelli") and the 1939 LA Times clippings are his father, William G. "Bill" Bonelli (b. 1895); the record's own alias "Bill Bonelli" lands on the wrong man. He died in February 1972, so he is not in the 1974 Reagan photograph.

## Weak

### Ed Colley (#26589)  NEW

Result: WEAK. Census 5 October: none found. Names searched: title only. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/mugs/stephencolley.jpg` | `warmemorial/terror_stephencolley.htm` | 800 x 600 | (heads the memorial page of SP4 Stephen Edward Colley, d. May 16, 2007) | NOT THIS PERSON | n/a | none | yes: asset #1892, already the portrait of Stephen Edward Colley (#524) |
- `stephencolley.jpg`: A namesake-by-middle-name hit; nothing on the page ties the soldier to Ed Colley the water board director.

No likeness of Ed Colley.

### Pauline Harte (#2594)  NEW

Result: WEAK. Census 5 October: none found. Names searched: title only. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `scvhistory/files/sg20050704parade02/data1/images/dscn4919.jpg` | `scvhistory/files/sg20050704parade02/sg20050704parade02.htm` | 800 x 600 | (alt text) "Pauline Harte, Rick Winsman, Duane Harte, Alan Wykoff" | PERSON IN PHOTO | low | Rick Winsman, Duane Harte, Alan Wykoff, and others | no |
| `oldtownnewhall/gif/pauline.gif` | `oldtownnewhall/pauline/index.htm` | 325 x 242 | (alt text "Pauline Harte") | NOT A LIKENESS | n/a | none | no |
- `dscn4919.jpg`: 2005 parade gallery. Viewed: two women are in frame (one seated at left, one mostly hidden); the alt text gives no positions, so which is she cannot be said.
- `pauline.gif`: Viewed: the word "Pauline" over a heart, a column title graphic.

NEW: the parade photo was not in the census; her husband's obituary photos are on scvnews.com, not the drive.

### Sheila Dyer (#30203)  NEW

Result: WEAK. Census 5 October: none found. Names searched: title only. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `gif/aa7401.jpg` | `scvhistory/aa7401.htm` | 800 x 559; aa7401_large.jpg 2400 x 1677 | ... Ex-Trustee Sheila Dyer (in background, COC board 1967-1969 only); ... | PERSON IN PHOTO | low | Reagan, Boyer, Adams, Claffey, Fortine, Rheinschmidt | no |
- `aa7401.jpg`: April 22, 1974; she is in the background only.

NEW (the census found nothing). Trap: the "Willis Dyer" images on sg19850906dyer.htm (mugs/jameswillisdyer.jpg and four photos) are of James Willis Dyer, not her.

### Tim Whyte (#2588)  NEW

Result: WEAK. Census 5 October: none found. Names searched: title only. Legacy URL fields: none.

| Image | Page | Size | Caption as printed | Kind | Conf. | Others in frame | In Craft |
|---|---|---|---|---|---|---|---|
| `oldtownnewhall/gif/whyte.gif` | `oldtownnewhall/whyte/index.html` | 529 x 192 | (alt text "Black N Whyte") | NOT A LIKENESS | n/a | none | no |
- `whyte.gif`: Viewed: the column title in lettering. smalltw.gif (alt "Tim Whyte", 100 x 16) is a name button.

No likeness on the drive. Listed only so the alt-text hits are accounted for.

## None found

| Record | Pages naming the person | Note |
|---|---|---|
| Antonio del Valle (#291) | 180 |  |
| Bill Pecsi (#28320) | 1 |  |
| Bill Thomas (#29464) | 3 | No likeness. LW3608 is a program book he signed, not a photograph of him. |
| Bob Brauneisen (#29002) | 0 |  |
| C. R. Huntsinger (#28673) | 1 |  |
| Carol Rock (#15967) | 33 | No likeness. She appears as a byline and as an illustrator; the Rock obituary photographs are of relatives. |
| Carroll Word (#28689) | 3 |  |
| Catherine Kawaguchi (#29000) | 1 | No likeness on the drive. Her Newsmaker of the Week thumbnail is an scvtv.com image, not on the drive. |
| Cephas L. Bard (#2532) | 1 |  |
| Charles Barber (#18689) | 3 |  |
| Charles Brown (#28643) | 9 |  |
| Chris Fall (#28721) | 5 | No likeness on the drive. melladysilverspur04chrisfall.jpg (alt names him as auctioneer) is on scvnews.com, not on the drive. |
| David Barlavi (#25411) | 0 |  |
| Di Thompson (#29208) | 0 |  |
| Don Allen (#30213) | 2 |  |
| Don Rogers (#29322) | 1 |  |
| Douglas Bryce (#25403) | 0 |  |
| Doña Jacoba (#18813) | 12 | No likeness. lw2706a.jpg (alt "Jacoba Feliz") is a page of the 1853 livestock ledger. |
| Edward Duarte (#28681) | 2 |  |
| Fran Pavley (#29460) | 0 |  |
| Francisco Lopez (#18834) | 151 | No likeness. Trap: obituary_franciscolopez.htm (a Francisco Lopez who died in 1900) carries gif/us8502.jpg, which Leon Worden's US8502 page says is Chico López, "often wrongly identified as Francisco Lopez, the gold discoverer"; it is already Chico's portrait in Craft (asset #31220). The 1959 pageant photographs (cc5901, cc5902) show an actor playing him. |
| George Whitesides (#29336) | 0 |  |
| Henry Stern (#29462) | 1 |  |
| James E. Rentz (#30217) | 1 |  |
| James Webb (#28564) | 1 |  |
| Jeff Gorell (#29452) | 0 |  |
| Jim Shuman (#28705) | 1 | No likeness. The Shuman images (lw2061, sg2061, mugs/mikeshuman1973.jpg) are of Mike Shuman. |
| John Michael McGrath (#28342) | 1 |  |
| Julie Olsen (#28346) | 1 |  |
| Kerry Clegg (#25431) | 1 |  |
| Kevin McCarthy (#29466) | 1 |  |
| Laura Arrowsmith (#25415) | 0 |  |
| Lester Freeman (#28354) | 0 |  |
| Leticia Hernandez (#28996) | 0 |  |
| Lisa Eichman (#29109) | 1 |  |
| Lynne Plambeck (#15897) | 13 |  |
| Mary Bonelli (#28641) | 7 | No likeness. mugs/billbonelli.jpg is William G. Bonelli Sr. |
| Michael Freedman (#18848) | 11 |  |
| Michael Kennick (#28362) | 0 |  |
| Michael Millar (#29214) | 3 |  |
| Michael Owen Lambarth (#28866) | 0 |  |
| Michael Shapiro (#25387) | 1 |  |
| Michael White (#18765) | 8 |  |
| Mike Garcia (#29334) | 1 |  |
| Mildred Gilmour (#28649) | 6 |  |
| Nathan Keith (#29203) | 0 |  |
| Patrick Shaughnessy (#28699) | 1 |  |
| Paul Nelson De La Cerda (#25407) | 0 |  |
| Paul Strickland (#25425) | 4 |  |
| Paula Boland (#29444) | 1 |  |
| Paula Olivares (#26597) | 1 |  |
| Peter Warren (#28719) | 1 |  |
| Phineas Banning (#18714) | 84 | No likeness. The only images naming him are of Eugene Daub's 2004 statue (LW052106a-c) and Ridge Route postcards whose text names him. |
| RJ Kelly (#28366) | 1 |  |
| Robert Hall (#28723) | 2 | No likeness. mugs/tedhall.jpg is Fire Capt. Ted Hall. |
| Robert Hernandez (#28998) | 1 |  |
| Ron Winkler (#25383) | 3 |  |
| Rosemarie Koscielny (#25397) | 0 |  |
| S. S. Donaldson (#28645) | 8 |  |
| Stacy Dobbs (#28864) | 0 |  |
| Stephen Cole (#29004) | 2 | No likeness on the drive. His Newsmaker of the Week thumbnail (with Matt Stone) is an scvtv.com image, not on the drive. |
| Stephen Winkler (#28372) | 0 |  |
| Steve Fox (#29454) | 1 |  |
| Suzan Solomon (#25385) | 2 |  |
| Ted Lamkin (#16372) | 32 | No likeness. Pages credit photographs to his collection or his camera; none shows him. |
| Teresa Todd (#25427) | 1 |  |
| Thomas Hanson (#28687) | 3 |  |
| Thomas M. Frew Jr. (#28647) | 84 | No likeness. tf1000.jpg (alt "Tom Frew II") is Tom Frew II (1852-1928), a namesake (by the record's alias Tom Frew III, his father), already Tom Frew II's portrait (asset #31356). The 1945 board appears only in text (ap0815 and others). |
| Tim Burkhart (#29104) | 5 |  |
| Tom Caesar (#26540) | 0 |  |
| Tom Campbell (#18774) | 8 |  |
| Tom McClintock (#29446) | 8 | No likeness. He is named only in a footnote to Marlee Lauffer's Newhall Land biography (nl9903, nl9906); he is not pictured. |
| Tony Strickland (#29448) | 0 |  |
| Val Thomas (#18756) | 8 |  |
| Victor Torres (#28376) | 0 |  |

## For Nathan

1. Scott Newhall: TN1968 as his portrait (the strongest find of this pass).
2. Cathie Wright: a crop of LW3032 (already in Craft, unlinked), or wait for a better likeness.
3. John Boston: sg030506b-honby.jpg is a photo illustration (a composite with the 1968 print); a crop of his face is a likeness, but it is an edited image.
4. Michele Jenkins and Tom Frew IV: crops of CO1501c and hs9019 would serve, under the edited-image rule.
5. Gary Murr, Sol Taylor, Philip Ellis Jr.: their portraits exist only as links to files never captured. Nothing to import from the drive.
