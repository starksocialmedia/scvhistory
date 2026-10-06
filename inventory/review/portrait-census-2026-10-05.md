# Portrait census, 5 October 2026

Report only: nothing was imported or changed. Read-only search of the legacy mirror on Reggie (`/Volumes/Reggie/SCVHistory/scvhistory.com`) and of Craft. Prepared by Claude for Nathan.

## How the search was done

- Craft: every persons, warMemorials and fallenOfficers record in any status whose featuredImage is empty. War memorial pages show `bandImage` first and fall back to `featuredImage`; no memorial record has a bandImage, so featuredImage is the likeness field for all three sections.
- The mirror was read once into a text index: 33,690 .htm/.html pages (title, every `<img>` with its alt text, its enclosing link and the text printed just before and after it, and the page text). Flipbook page scans (`scvhistory/files/*/basic-html`) were searched as OCR text only: their images are page scans, so a name there is a lead, not a captioned image.
- For each record: its own legacy page (legacyUrl, sourcePath or personLegacyUrl, and the copy in the other tree where both `warmemorial/` and `scvhistory/` exist); every page whose title or text names the person (full name and every alias, with optional middle names or initials); every image whose alt text or printed caption names the person; every image file under `gif/` (22,043 files, names only) whose name holds the surname. Candidates were then read by caption and, where the identification depended on it, viewed.
- Craft check: each candidate file name (and its `_large` twin) was looked up among Craft assets, with the entries each asset is related to.
- Kinds: PORTRAIT (a likeness of this person as the subject); PERSON IN PHOTO (a group, event or background image that shows the person); UNCERTAIN (who is who is unclear, or a namesake or relative). Confidence high only when the caption names this person as the sole or clearly identified subject and the page is about the same person.

## Counts

| | Persons | War memorials | Fallen officers |
|---|---|---|---|
| Records in the section | 293 | 54 | 14 |
| No portrait now (featuredImage empty) | 217 | 34 | 14 |
| PORTRAIT high | 25 | 1 | 11 |
| PORTRAIT medium | 6 | 0 | 0 |
| UNCERTAIN | 0 | 1 | 0 |
| PERSON IN PHOTO only | 15 | 2 | 0 |
| none found | 171 | 30 | 3 |
| Candidate image already in Craft but not the portrait | 4 | 3 | 2 |

Each record is counted once, under its best candidate (PORTRAIT high, then PORTRAIT medium, then UNCERTAIN, then PERSON IN PHOTO).

## Fallen officers

### Fallen officers: PORTRAIT high (11)

| Record | Image | Page | Caption as printed | Credit as printed | Kind | Conf. | Why | In Craft |
|---|---|---|---|---|---|---|---|---|
| Deputy Constable J. Edward "Ed" Brown (#29764) | `gif/sd2401_large.jpg` | `scvhistory/lasd053113brown.htm` | Deputy Constable Ed Brown, April 8, 1924. | none printed | PORTRAIT | high | Alt text "Ed Brown"; caption names him alone; page is about his 1924 death. Viewed: a single man standing, full length, outdoors. | no |
| Deputy Constable J. Edward "Ed" Brown (#29764) | `gif/sd2402_large.jpg` | `scvhistory/lasd053113brown.htm` | Members of Newhall's Dry Squad -- Constable Jack Pilcher (left), Deputy Constable Ed Brown (center) and a third officer bust up a still in one of the local canyons during Prohibition in 1924. | none printed | PERSON IN PHOTO | high | Group of three; Brown identified as center. | no |
| Officer Clarence Wayne Dean (#29780) | `gif/mugs/clarencewaynedean.jpg` | `scvhistory/lw3049.htm` | Officer Dean, E.O.W. 1-17-1994. | none printed for the portrait (LW3049, the AP wire photo on the same page, is "9600 dpi jpeg from original wire photo purchased 2017 by Leon Worden") | PORTRAIT | high | Caption names him; viewed: LAPD uniform portrait with an LAPD ID placard. Already a Craft asset (#14804, clarencewaynedean.jpg) related to no entry. | asset #14804, unlinked |
| Officer Clarence Wayne Dean (#29780) | `gif/lw3049_large.jpg` | `scvhistory/lw3049.htm` | Dean's covered body and his wrecked Kawasaki Police 1000 motorcycle are visible in the center of this 7x10-inch Associated Press wire photo | LW3049: 9600 dpi jpeg from original wire photo purchased 2017 by Leon Worden | PERSON IN PHOTO | high | The scene of his death; never a likeness. | assets #13263, #11540 |
| Deputy Hagop "Jake" Kuredjian (#29778) | `gif/mugs/kuredjian_jake.jpg` | `scvhistory/kuredjian082811.htm` | (no caption line; the image heads the memorial article) | none printed | PORTRAIT | high | Only photograph on his memorial page, filed under mugs/ with his name; viewed: one deputy in uniform, head and shoulders. | no |
| Officer Matthew Pavelka (#29786) | `gif/mugs/matthewpavelka.jpg` | `scvhistory/scvnews072412.htm` | Officer Matthew Pavelka, E.O.W. 11-15-2013. | none printed | PORTRAIT | high | Caption names him alone; viewed: Burbank police officer, uniform portrait. Note the caption prints E.O.W. 11-15-2013; he died in November 2003 (the page text says so). Do not carry the caption date. | no |
| Deputy David W. March (#29784) | `gif/mugs/davidmarch.jpg` | `scvhistory/obituary_davidmarch.htm` | (no caption line; heads his obituary) | none printed | PORTRAIT | high | Only photograph on his obituary page, filed under mugs/; viewed: one deputy in uniform, studio portrait. | no |
| Deputy Arthur E. Pelino (#29776) | `gif/obituary_pelinoarthure.jpg` | `scvhistory/obituary_pelinoarthure.htm` | (no caption line; heads his obituary) | none printed | PORTRAIT | high | Only photograph on his obituary page; viewed: one man, head and shoulders, cropped from a larger photograph (other people at the edges). | no |
| Officer George M. Alleyn (#29774) | `gif/sg4701b.jpg` | `scvhistory/chp-newhall-incident.htm` | George Alleyn | none printed | PORTRAIT | high | Captioned by name under each of four separate head-and-shoulders photographs in a row (same row on sg4701.htm, The Signal 30th-anniversary story, and pollack0309newhallincident.htm). Order read from the HTML: sg4701b Alleyn, sg4701a Frago, sg4701d Gore, sg4701c Pence. Viewed: single CHP officer in uniform. | no |
| Officer James E. Pence Jr. (#29772) | `gif/sg4701c.jpg` | `scvhistory/chp-newhall-incident.htm` | James Pence | none printed | PORTRAIT | high | Captioned by name under each of four separate head-and-shoulders photographs in a row (same row on sg4701.htm, The Signal 30th-anniversary story, and pollack0309newhallincident.htm). Order read from the HTML: sg4701b Alleyn, sg4701a Frago, sg4701d Gore, sg4701c Pence. Viewed: single CHP officer in uniform. | no |
| Officer Roger D. Gore (#29770) | `gif/sg4701d.jpg` | `scvhistory/chp-newhall-incident.htm` | Roger Gore | none printed | PORTRAIT | high | Captioned by name under each of four separate head-and-shoulders photographs in a row (same row on sg4701.htm, The Signal 30th-anniversary story, and pollack0309newhallincident.htm). Order read from the HTML: sg4701b Alleyn, sg4701a Frago, sg4701d Gore, sg4701c Pence. Viewed: single CHP officer in uniform. | no |
| Officer Walter C. Frago (#29768) | `gif/sg4701a.jpg` | `scvhistory/chp-newhall-incident.htm` | Walter Frago | none printed | PORTRAIT | high | Captioned by name under each of four separate head-and-shoulders photographs in a row (same row on sg4701.htm, The Signal 30th-anniversary story, and pollack0309newhallincident.htm). Order read from the HTML: sg4701b Alleyn, sg4701a Frago, sg4701d Gore, sg4701c Pence. Viewed: single CHP officer in uniform. | no |
| Constable John S. "Jack" Pilcher (#29766) | `gif/mugs/pilcher_jack3.jpg` | `scvhistory/lasd060414pilcher.htm` | (no caption line; image heads the article on Constable John S. "Jack" Pilcher) | none printed | PORTRAIT | high | Lead image of his own memorial article, filed as mugs/pilcher_jack3; viewed: one man in a hat, head and shoulders. The same face as pilcher_jack1, which is captioned "Constable Jack Pilcher." on the Ed Brown page. | no |
| Constable John S. "Jack" Pilcher (#29766) | `gif/mugs/pilcher_jack1.jpg` | `scvhistory/lasd053113brown.htm` | Constable Jack Pilcher. | none printed | PORTRAIT | high | Alt "Jack Pilcher"; caption names him alone. Viewed: one man with badge and hat. | no |
| Constable John S. "Jack" Pilcher (#29766) | `gif/sd2402_large.jpg` | `scvhistory/lasd053113brown.htm` | Members of Newhall's Dry Squad -- Constable Jack Pilcher (left), ... | none printed | PERSON IN PHOTO | high | Group of three. | no |

### Fallen officers: none found (3)

| Record | Searched | Note |
|---|---|---|
| Constable McCoy Pyle (#29760) | 43 pages or checks (listed in the JSON) | Two images on his page: newspaper reports (LA Herald 25 April 1897; SF Chronicle). His name appears at Bowers Cave only as carved initials (AP0813, valkenburgh1952d). No likeness on the mirror. |
| Deputy Shayne D. York (#29782) | 2 pages or checks (listed in the JSON) | No legacy page on the record. No page on the mirror names Shayne York. |
| Deputy Constable Charles A. De Moranville (#29762) | 21 pages or checks (listed in the JSON) | His page shows a memorial plaque (charlesdemoranville_plaque.jpg, his name on the LASD wall). No likeness on the mirror. |

## War memorials

### War memorials: PORTRAIT high (1)

| Record | Image | Page | Caption as printed | Credit as printed | Kind | Conf. | Why | In Craft |
|---|---|---|---|---|---|---|---|---|
| John Amos Ward (#570) | `gif/johnward_dorothyward.jpg` | `warmemorial/ww2_johnward.htm` | John and Dorothy Ward. Image courtesy of their granddaughter, Tara Vaughn Garza. | Image courtesy of their granddaughter, Tara Vaughn Garza. | PORTRAIT | high | A double portrait of the couple on his own memorial page; viewed: a woman (left) and a soldier in an overseas cap (right), so who is who is not in doubt. A likeness for the record only as a crop of the right half: that is an edited image and needs Nathan's word. Already in Craft among his recordImages (asset #1973). | asset #1973, in recordImages |
| John Amos Ward (#570) | `gif/wd4501.jpg` | `scvhistory/wd4501.htm` | From left: John Ward; John's wife Dorothy (photo contributor); Dorothy's sister Lois; Eleanor Ward; Kenneth Ward. Photo shot in 1939 on 7th Street in Escondido, Calif. | WD4501: 19200 dpi jpeg from smaller jpeg courtesy of Dorothy Ward / Online image only. | PERSON IN PHOTO | high | Group of five, 1939; John Ward (d. 1945) is first from left. Same man by the page text (married to Dorothy, children of Margaret Rivera Ward). | no |

### War memorials: UNCERTAIN (1)

| Record | Image | Page | Caption as printed | Credit as printed | Kind | Conf. | Why | In Craft |
|---|---|---|---|---|---|---|---|---|
| Perry Leon Cherry (#572) | `gif/tlp_leoncherrymug_large.jpg` | `warmemorial/ww2_leoncherry.htm` | (no caption under the image) Page text: "The 1946 San Fernando High School yearbook shows Perry (Leon) in a compilation of war veterans and casualties (photo shown here)" | none printed | UNCERTAIN | medium | A yearbook clipping: one photograph of a sailor beside a paragraph naming three men (Leon Cherry, Walter D. Rushton, Harral V. Grant). The clipping does not say whose photograph it is; Leon Worden's page says it shows Perry Leon, and the site uses it as his mug (tlp_leoncherrymugt.jpg in every memorial index). Nathan cleared it as featured image on 4 October as "a yearbook clipping that is mostly text". A crop of the photograph would make a likeness if the full yearbook page confirms the sitter. Already in Craft (asset #1988, in recordImages). | asset #1988, in recordImages |

### War memorials: PERSON IN PHOTO only (2)

| Record | Image | Page | Caption as printed | Credit as printed | Kind | Conf. | Why | In Craft |
|---|---|---|---|---|---|---|---|---|
| Johnny Cordova (#568) | `gif/sg032449_large.jpg` | `warmemorial/ww2_johnnycordova.htm` | Soldier is borne to last resting place among own native hills (The Newhall Signal, March 24, 1949; SIGNAL PHOTO) | SIGNAL PHOTO | PERSON IN PHOTO | high | His reburial at the Ruiz cemetery, 1949: pallbearers carrying his casket. Not a likeness of him. Already in Craft (sg032449_large.jpg in recordImages). | in recordImages |
| Robert L. Whisler (#548) | `gif/ga4503_large.jpg` | `scvhistory/ga4503.htm` | Hart High School 9th grade class, Group C (3 of 3), 1945-46. ... Front row: Neil Aitken, Phil Hoskins, Bob Whisler, Tom Christie, Paul Hoskins, Frank ?, Harry Kidder, John Barnhill, Bob Waltrip. | GA4503: 9600 dpi jpeg from original 3x5-inch print, collection of Dean and Gwen Booth Gallion. | PERSON IN PHOTO | medium | Class photograph of about 29 students, chalkboard "Newhall 9c Grade 1946". Bob Whisler is third from left in the front row (nine boys kneeling, nine names). His record: born January 1, 1931, "a former Hart High School student"; Hart opened in 1945 with ninth graders only, so a 14-year-old Bob Whisler in its first ninth grade fits. The caption does not give his middle initial or dates, so the identification rests on age, school and name, not on the page saying so. A face could be cropped from the large scan; that would be an edited image and is Nathan's call. | no |

### War memorials: none found (30)

| Record | Searched | Note |
|---|---|---|
| William Ernest Pineau (#1407) | 4 pages or checks (listed in the JSON) |  |
| Terry Dale Gemas (#1382) | 2 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (vietnam_terrygemas.jpg); the file is not on the drive. |
| Stephen Russell Peterson (#1380) | 2 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (vietnam_stephenpeterson.jpg); the file is not on the drive. |
| Michael Andrew Fay (#1378) | 2 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (vietnam_michaelfay.jpg); the file is not on the drive. |
| Joseph Samuel Godwin (#1376) | 2 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (vietnam_josephgodwin.jpg); the file is not on the drive. |
| John William Borders Jr. (#1374) | 2 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (vietnam_johnborders.jpg); the file is not on the drive. |
| Gary Allen Turnbull (#1370) | 2 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (vietnam_garyturnbull.jpg); the file is not on the drive. |
| Gary Robert Monteleone (#1368) | 2 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (vietnam_garymonteleone.jpg); the file is not on the drive. |
| Frank Dennis Ortega (#1366) | 2 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (vietnam_frankortega.jpg); the file is not on the drive. |
| David Lee Reeder (#1362) | 2 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (vietnam_davidreeder.jpg); the file is not on the drive. |
| Bruce Wayne St. Louis (#1356) | 3 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (vietnam_brucestlouis.jpg); the file is not on the drive. |
| Thomas Milton Ross Jr. (#580) | 3 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (ww2_tomross.jpg); the file is not on the drive. |
| Robert Remy Fose (#578) | 3 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (ww2_robertfose.jpg); the file is not on the drive. |
| Robert Russell Cone (#576) | 3 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (ww2_robertcone.jpg); the file is not on the drive. |
| Ozal R. Smart (#574) | 3 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (ww2_ozalsmart.jpg); the file is not on the drive. |
| James A. Bartlett (#566) | 2 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (ww2_jimBartlett.jpg); the file is not on the drive. |
| James M. Redmond (#564) | 3 pages or checks (listed in the JSON) |  |
| Jack Lewis Harland (#562) | 4 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (ww2_jackharland.jpg); the file is not on the drive. |
| Garry Wingfield (#560) | 3 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (ww2_garrywingfield.jpg); the file is not on the drive. |
| Albert Edward Thomas (#558) | 3 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (korea_albertthomas.jpg); the file is not on the drive. |
| Gilbert D. Montenegro (#554) | 2 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (korea_gilbertmontenegro.jpg); the file is not on the drive. |
| Henry Acuna (#552) | 2 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (korea_henryacuna.jpg); the file is not on the drive. |
| Raymond Gene Kelly (#550) | 2 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (korea_raymondkelly.jpg); the file is not on the drive. |
| John Michael Conant (#534) | 2 pages or checks (listed in the JSON) |  |
| Robert Michael Wilson (#528) | 5 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (robertmichaelwilson.jpg); the file is not on the drive. |
| Albert Lee Moore (#522) | 3 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (ww2_albertmoore.jpg); the file is not on the drive. |
| Edward D. Contreras (#516) | 3 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (ww2_edwardcontreras.jpg); the file is not on the drive. |
| Lawrence E. Kenaston (#514) | 3 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (ww2_ekenaston.jpg); the file is not on the drive. |
| Eugene E. Darr (#512) | 4 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (ww2_eugenedarr.jpg); the file is not on the drive. |
| Frank Pike Whitmore (#510) | 4 pages or checks (listed in the JSON) | Portrait tag on the legacy page commented out (ww2_frankwhitmore.jpg); the file is not on the drive. |

## War memorial: the sixteen "no likeness is known" records

Each of these pages once carried a portrait tag that Leon Worden commented out (`<!-- <img src="../gif/ww2_NAME.jpg"> -->`). In every case the commented-out file is not on the drive: the mirror holds no file by that name. Searches run for each, beyond that: the own page; every page on the mirror that names him in full (title or text, with the casualty-index sidebars and the index pages excluded); a surname search across all pages and flipbook OCR; image file names on the drive holding the surname; Craft assets. Result per record:

| Record | Own page | Commented-out portrait file | Other pages naming him | Result |
|---|---|---|---|---|
| Thomas Milton Ross Jr. (#580) | `warmemorial/ww2_tomross.htm` | ww2_tomross.jpg (not on drive) | `scvhistory/warmemorial_errata.htm` | claim stands: no image found. Portrait tag on the legacy page commented out (ww2_tomross.jpg); the file is not on the drive. |
| Robert Remy Fose (#578) | `warmemorial/ww2_robertfose.htm` | ww2_robertfose.jpg (not on drive) | `scvhistory/warmemorial_errata.htm` | claim stands: no image found. Portrait tag on the legacy page commented out (ww2_robertfose.jpg); the file is not on the drive. |
| Robert Russell Cone (#576) | `warmemorial/ww2_robertcone.htm` | ww2_robertcone.jpg (not on drive) | `scvhistory/warmemorial_errata.htm` | claim stands: no image found. Portrait tag on the legacy page commented out (ww2_robertcone.jpg); the file is not on the drive. |
| Ozal R. Smart (#574) | `warmemorial/ww2_ozalsmart.htm` | ww2_ozalsmart.jpg (not on drive) | `scvhistory/warmemorial_errata.htm` | claim stands: no image found. Portrait tag on the legacy page commented out (ww2_ozalsmart.jpg); the file is not on the drive. |
| James A. Bartlett (#566) | `warmemorial/ww2_jimbartlett.htm` | ww2_jimBartlett.jpg (not on drive) | none | claim stands: no image found. Portrait tag on the legacy page commented out (ww2_jimBartlett.jpg); the file is not on the drive. |
| Jack Lewis Harland (#562) | `warmemorial/ww2_jackharland.htm` | ww2_jackharland.jpg (not on drive) | `scvhistory/sg19290425school.htm`, `scvhistory/warmemorial_errata.htm` | claim stands: no image found. Portrait tag on the legacy page commented out (ww2_jackharland.jpg); the file is not on the drive. |
| Garry Wingfield (#560) | `warmemorial/ww2_garrywingfield.htm` | ww2_garrywingfield.jpg (not on drive) | `scvhistory/warmemorial_errata.htm` | claim stands: no image found. Portrait tag on the legacy page commented out (ww2_garrywingfield.jpg); the file is not on the drive. |
| Albert Edward Thomas (#558) | `warmemorial/korea_albertthomas.htm` | korea_albertthomas.jpg (not on drive) | `scvhistory/warmemorial_errata.htm` | claim stands: no image found. Portrait tag on the legacy page commented out (korea_albertthomas.jpg); the file is not on the drive. |
| Gilbert D. Montenegro (#554) | `warmemorial/korea_gilbertmontenegro.htm` | korea_gilbertmontenegro.jpg (not on drive) | none | claim stands: no image found. Portrait tag on the legacy page commented out (korea_gilbertmontenegro.jpg); the file is not on the drive. |
| Raymond Gene Kelly (#550) | `warmemorial/korea_raymondkelly.htm` | korea_raymondkelly.jpg (not on drive) | none | claim stands: no image found. Portrait tag on the legacy page commented out (korea_raymondkelly.jpg); the file is not on the drive. |
| Robert L. Whisler (#548) | `warmemorial/korea_robertwhisler.htm` | korea_robertwhisler.jpg (not on drive) | none | overturned in part: an image of him (group photograph) exists. Portrait tag on the legacy page commented out (korea_robertwhisler.jpg); the file is not on the drive. Group photograph found (GA4503, Hart 9th grade 1945-46). |
| Robert Michael Wilson (#528) | `warmemorial/terror_robertwilson.htm` | robertmichaelwilson.jpg (not on drive) | `scvhistory/lw3792.htm`, `scvhistory/files/hart1988commencement/hart1988commencement_ocr.htm`, `scvhistory/files/placerita2006yearbook/placerita2006yearbook_ocr.htm` | claim stands: no image found. Portrait tag on the legacy page commented out (robertmichaelwilson.jpg); the file is not on the drive. |
| Albert Lee Moore (#522) | `warmemorial/ww2_albertmoore.htm` | ww2_albertmoore.jpg (not on drive) | `scvhistory/warmemorial_errata.htm` | claim stands: no image found. Portrait tag on the legacy page commented out (ww2_albertmoore.jpg); the file is not on the drive. |
| Lawrence E. Kenaston (#514) | `warmemorial/ww2_ekenaston.htm` | ww2_ekenaston.jpg (not on drive) | `scvhistory/warmemorial_errata.htm` | claim stands: no image found. Portrait tag on the legacy page commented out (ww2_ekenaston.jpg); the file is not on the drive. |
| Eugene E. Darr (#512) | `warmemorial/ww2_eugenedarr.htm` | ww2_eugenedarr.jpg (not on drive) | `scvhistory/warmemorial_errata.htm`, `scvhistory/ww2_josephbbalsz.htm` | claim stands: no image found. Portrait tag on the legacy page commented out (ww2_eugenedarr.jpg); the file is not on the drive. |
| Frank Pike Whitmore (#510) | `warmemorial/ww2_frankwhitmore.htm` | ww2_frankwhitmore.jpg (not on drive) | `scvhistory/lw2077.htm`, `scvhistory/warmemorial_errata.htm` | claim stands: no image found. Portrait tag on the legacy page commented out (ww2_frankwhitmore.jpg); the file is not on the drive. |

Suggested wording for the restated note, if Nathan agrees: "No likeness of NAME is known. Leon Worden's memorial page carried a portrait tag (FILE) that was commented out, and the file is not in the archive. Searched 5 October 2026: his memorial page, every page of the legacy site that names him, the site's image files and the flipbook text."

Leads not followed to the end (page scans with no text layer): `scvhistory/files/tomahawk1948` (the 1948 Hart yearbook, no OCR; Whisler and Albert Edward Thomas, born 1931 and 1930 and from Hart's first years, could be pictured) and the 1950 to 1952 Hart yearbooks (OCR present, no hit for any of the sixteen).

## Already in Craft, not set as the portrait

| Record | Asset | Kind | Note |
|---|---|---|---|
| Sanford Lyon (#20224) | asset #2316 (ap1334.jpg), used in two Reynolds chapters, not on his record (`ap1334_large.jpg`) | PORTRAIT high | His own AP1334 page; caption names Sanford Lyon with his dates. Caution: he had a twin, Cyrus, and the caption is the only identification; no second source for which twin sits here was found. |
| Dan Hon (#18616) | asset #14933, on the article "Dan Hon, Signal columnist", not on his record (`danhon.jpg`) | PORTRAIT high | Byline portrait on his own column; alt text "Dan Hon"; one man. |
| Connie Worden (#16418) | asset #30549, on her two obituary records, not on her person record (`lw9501_large.jpg`) | PORTRAIT high | Heads her obituary ("Connie Worden-Roberts, Cityhood Pioneer"); one woman, studio portrait with light trails. |
| Ruth Newhall (#15477) | asset #11344, photograph record #4583 (already names her in photoPeople) (`lw2849a_large.jpg`) | PERSON IN PHOTO high | Scott and Ruth Newhall aboard ship, 1936. Already a photograph record in Craft naming her. |
| Perry Leon Cherry (#572) | asset #1988, in recordImages (`tlp_leoncherrymug_large.jpg`) | UNCERTAIN medium | A yearbook clipping: one photograph of a sailor beside a paragraph naming three men (Leon Cherry, Walter D. Rushton, Harral V. Grant). The clipping does not say whose photograph it is; Leon Worden's p |
| John Amos Ward (#570) | asset #1973, in recordImages (`johnward_dorothyward.jpg`) | PORTRAIT high | A double portrait of the couple on his own memorial page; viewed: a woman (left) and a soldier in an overseas cap (right), so who is who is not in doubt. A likeness for the record only as a crop of th |
| Johnny Cordova (#568) | in recordImages (`sg032449_large.jpg`) | PERSON IN PHOTO high | His reburial at the Ruiz cemetery, 1949: pallbearers carrying his casket. Not a likeness of him. Already in Craft (sg032449_large.jpg in recordImages). |
| Officer Clarence Wayne Dean (#29780) | asset #14804, unlinked (`clarencewaynedean.jpg`) | PORTRAIT high | Caption names him; viewed: LAPD uniform portrait with an LAPD ID placard. Already a Craft asset (#14804, clarencewaynedean.jpg) related to no entry. |
| Officer Clarence Wayne Dean (#29780) | assets #13263, #11540 (`lw3049_large.jpg`) | PERSON IN PHOTO high | The scene of his death; never a likeness. |

## Notes on persons (namesakes, duplicates, files not on the drive)

Listed per record in the JSON (`note`). The ones that matter most:

- **Remi Nadeau (#18869)**: the portrait found is of this record's man (born 1867), per Leon Worden's note on the page. The "Remi Nadeau (1821-1887)" page images are his grandfather's and must not be used.
- **William G. Bonelli Jr. (#30199)**: `mugs/billbonelli.jpg` and the 1939 Los Angeles Times clippings are his father, William G. "Bill" Bonelli (born 1895). This record carries the alias "Bill Bonelli", so a search by alias lands on the wrong man. Only the 1969-70 board group photograph shows Jr.
- **John Lang (#18820)**: `hs7530_johnbrodericklang.jpg` is his son John Broderick Lang; "John L. Lang" in the Callahan's photographs is a modern man.
- **Sanford Lyon (#20224)**: identical twin of Cyrus Lyon. The AP1334 caption names Sanford; nothing else confirms which twin is pictured.
- **Chico Lopez (#28132) and Francisco Lopez (#18834)**: no likeness of either. The 1959 pageant photographs show an actor playing Francisco Lopez.
- **Tom Frew (#18783) and Thomas Frew Jr. (#28647)**: possibly one man in two records. `tf1000.jpg` is Tom Frew II (1852-1928), a namesake of both.
- **Chester Allen (#28655), Ernest Moreno (#30237), Elisha Agajanian (#28679)**: the obituary photographs that the name search finds are of namesakes or relatives. None was used.
- **Adrian Adams (#28667)**: the portraits are of Judge Adrian W. Adams. The record is the Hart board member of 1957-1963. They are very likely one man, but no page says so.
- **Not on the drive**: images that legacy pages link from scvnews.com, scvtv.com or scvleon.com (Fortine, Kawaguchi, Chris Fall, Pauline Harte), the wedding photographs of Walter R. Cook (nw2001), and every commented-out war memorial portrait.

## Cases for Nathan's eye

1. **John Amos Ward (#570)**: a double portrait of John and Dorothy Ward from his granddaughter, already among his record images. It becomes a likeness only as a crop of his half.
2. **Perry Leon Cherry (#572)**: the yearbook clipping that was cleared on 4 October. Its photograph sits beside text about three men and has no caption. Leon Worden's page says it shows Perry Leon. Check the 1946 San Fernando High yearbook page before cropping it.
3. **Robert L. Whisler (#548)**: "Bob Whisler", third from left in the front row of the Hart 9th grade class photo of 1945-46. His age and school fit, but nothing on the page says this is the same man. If accepted, his "no likeness" note changes.
4. **Jereann Bowman (#28677)** and **Earl Schmidt (#28675)**: likenesses from a clipping (Bowman, which needs a crop) and an uncaptioned article portrait (Schmidt).
5. **Carl Goldman (#30546)** and **John Lang (#18820)**: uncaptioned head shots, identified by file name and placement only.
6. **Michael D. Berger (#30245)**: a candid shot at Dodger Stadium with others in the frame.
7. **Tom Mix (#18702)**: two publicity photographs (high) and a painted portrait (medium). Pick one.
8. **Matthew Pavelka (#29786)**: the caption prints E.O.W. 11-15-2013, but he died in November 2003. The image is sound; do not carry the date.
