# Where the sole copies came from (8 October 2026)

Read-only, Claude, for Nathan ("List those 116 separately with their origin so I can check my own drives"; the 37 "Tell me what they are and I will look"). Nothing was moved, committed or changed. Evidence per file: the macOS download record on the file itself (kMDItemWhereFroms, the URL Chrome saved it from, and the quarantine date and app), embedded EXIF/XMP and content credentials (exiftool and a byte scan for C2PA), pixel size, inventory/incoming/done/MANIFEST.json and OUTSTANDING.md, and a 16x16 picture match against every image in web/uploads and inventory/incoming. Working files: the session scratchpad (evidence.json); not kept in the repo.

## Short answer

Most of the 116 are not original material at all. They fall into these groups:

- Wikimedia Commons downloads (URL recorded): **7**
- Other website downloads (URL recorded): **13**
- Adobe Firefly outputs (downloaded from firefly.adobe.com): **37**
- Marks edited in Adobe Firefly, saved with no download record: **5**
- SVG marks exported from Adobe Illustrator: **3**
- Grok-generated images: **9**
- No download record, or received another way: **6**
- Your downloads of the archive's own files from the local site: **15**
- Crops made by the archive: **5**
- Screenshots of the archive's own pages: **16**

- **Another copy exists, or it can be made again (56):** 20 downloads whose URL is on the file (7 Commons, 13 other sites including one Google Drive share), 15 of your downloads of the archive's own files from the local site, 5 crops of Reggie originals, and 16 screenshots of the site's pages.
- **Probably still in one of your accounts (46):** 37 Adobe Firefly outputs, which your Firefly history may hold, and 9 Grok images.
- **Only here as far as the evidence shows (14):** 5 marks edited in Firefly and 3 Illustrator SVGs with no download record; the Castaic High and LASD PNG marks, also with no record; the SchlickArt portrait of Anna Griese; the Flickr City Hall original; the AirDropped marathon photo; and the Tank Man photograph. Look for these first.

Three pairs in the list are byte-identical (city-hall.jpg = BOM-pg14-shutterstock-185944559.jpg; edited-image.jpg = grok-image-fb994b75-....jpg; reynolds=map-3.jpg = reynolds=map-4.jpg). Not SCV history at all: the Shutterstock image (both copies), IMG_2226 2.jpg (a personal marathon photograph), tank-guy.jpg (Tiananmen Square, 1989), and the generated images (Grok). Flagged only; keeping them is your call.

**The census missed five files** (section 3): it treated a file as Reggie's when Reggie had any file of the same name. Your own photograph of Cameron Smyth (done/Smyth.jpg, the original of asset #21579) and four Firefly edits are sole copies too.

**The 37 "supplied by Nathan" are not 37 more originals** (section 2). Each is Craft's re-save of a file you handed over, and that file is still in inventory/incoming/done: 32 of them are in the 116 list, 4 are already in git, and 1 (done/Smyth.jpg) is one of the five the census missed. Committing the 116 and the five covers every original behind the 37; the 37 web files are copies.

## 1. The 116 files in inventory/incoming

Path relative to inventory/incoming. "Became" is what MANIFEST.json says the file was imported as.

### Wikimedia Commons downloads (URL recorded): 7

Where to look: Commons, at the URL.

| File | Size | Pixels | Origin | Became / note |
|---|---|---|---|---|
| `Buck_McKeon_2011.jpeg` | 73 KB | 500x750 | Wikimedia Commons, downloaded 2026-09-30: https://thumb.wikimedia.org/wikipedia/commons/thumb/0/0e/Buck_McKeon_2011.jpeg/500px-Buck_McKeon_2011.jpeg?utm_source=en.wikipedia.org&utm_campaign=parser&utm_content=thumbnail | 500-pixel Wikipedia thumbnail of the Commons file; the full Commons original is also held (Buck_McKeon_2011-commons-original.jpeg). |
| `done/Christy_Smith_CA_Assembly_official_photo.jpg` | 1.3 MB | 1920x2688 | Wikimedia Commons, downloaded 2026-10-03: https://thumb.wikimedia.org/wikipedia/commons/thumb/1/19/Christy_Smith_CA_Assembly_official_photo.jpg/1920px-Christy_Smith_CA_Assembly_official_photo.jpg?utm_source=en.wikipedia.org&utm_campaign=imageinfo&utm_content=thumbnail | asset #28210 christy-smith-assembly-portrait-2018.jpg |
| `done/CSUN_Seal.png` | 161 KB | 450x450 | Wikimedia Commons, downloaded 2026-10-04: https://upload.wikimedia.org/wikipedia/commons/c/cc/CSUN_Seal.png?download | asset #29300 csun-seal.png (currentMark, California State University, Northridge) |
| `done/Seal_of_the_United_States_Congress.svg` | 232 KB | 1055.000x1052.400 | Wikimedia Commons, downloaded 2026-10-04: https://upload.wikimedia.org/wikipedia/commons/4/4b/Seal_of_the_United_States_Congress.svg?utm_source=commons.wikimedia.org&utm_campaign=index&utm_content=original | asset united-states-congress-seal.svg (currentMark, United States Congress) |
| `done/suzette-martinez-valladares.jpg` | 2.5 MB | 3732x4665 | Wikimedia Commons, downloaded 2026-10-04: https://upload.wikimedia.org/wikipedia/commons/2/2b/Suzette_Martinez_Valladares%2C_2024_%282%29.jpg?utm_source=en.wikipedia.org&utm_campaign=index&utm_content=original | asset suzette-martinez-valladares.jpg (featuredImage, Suzette Martinez Valladares) |
| `done/tom-lackey.jpg` | 342 KB | 960x1281 | Wikimedia Commons, downloaded 2026-10-04: https://thumb.wikimedia.org/wikipedia/commons/thumb/d/d3/Tom_Lackey%2C_2022.jpg/960px-Tom_Lackey%2C_2022.jpg?utm_source=en.wikipedia.org&utm_campaign=index&utm_content=thumbnail | asset tom-lackey.jpg (featuredImage, Tom Lackey) |
| `Williamshart.jpg` | 1.7 MB | 2868x3715 | Wikimedia Commons, downloaded 2026-09-28: https://upload.wikimedia.org/wikipedia/commons/a/a1/Williamshart.jpg?utm_source=de.wikipedia.org&utm_campaign=imageinfo&utm_content=original?download | Commons original of the Library of Congress portrait of William S. Hart. Not imported; the record uses the LOC file. |

### Other website downloads (URL recorded): 13

Where to look: The URL; ~/Downloads.

| File | Size | Pixels | Origin | Became / note |
|---|---|---|---|---|
| `1572388199414-710-817.jpg` | 216 KB | 1600x640 | Downloaded 2026-10-03 from https://transparentscv.com/wp-content/uploads/2023/03/1572388199414-710-817.jpg (page: https://transparentscv.com/wp-admin/upload.php?item=236) | The Hart District sign on its office building. Not imported. |
| `8-pioneer-oil-refinery-california-star-oil-works700x450.jpg` | 78 KB | 700x450 | Downloaded 2026-09-28 from https://www.asme.org/wwwasmeorg/media/asmemedia/about%20asme/whoweare/history/landmarks/8-pioneer-oil-refinery-california-star-oil-works700x450.jpg | ASME landmark photograph of the Pioneer Oil Refinery. Not imported. |
| `BOM-pg14-shutterstock-185944559.jpg` | 71 KB | 900x500 | Downloaded 2026-09-28 from https://www.facilitiesnet.com/resources/editorial/2022/BOM-pg14-shutterstock-185944559.jpg (page: https://www.facilitiesnet.com/hvac/article/Using-UV-to-Improve-Indoor-Air-Quality160--19728) | Shutterstock stock image (notice: Lynne Albright/Shutterstock, 2014). Not imported. Same bytes as city-hall.jpg. |
| `city-hall.jpg` | 71 KB | 900x500 | Downloaded 2026-09-28 from https://www.facilitiesnet.com/resources/editorial/2022/BOM-pg14-shutterstock-185944559.jpg | Same bytes as BOM-pg14-shutterstock-185944559.jpg, renamed. Shutterstock stock image. |
| `CSUN.jpg` | 190 KB | 1024x900 | Downloaded 2026-09-28 from https://newsroom.csun.edu/wp-content/uploads/2026/08/20240110rc-campus005-1024x900.jpg | CSUN campus photograph (notice: Copyright 2024 CSUN). Not imported. |
| `done/california-assembly.webp` | 245 KB | 2331x2330 | Downloaded 2026-10-04 from https://www.assembly.ca.gov/sites/assembly.ca.gov/files/styles/large/public/2025-07/assembly-logo-2x.png.webp?itok=Ii_ySsQG (page: https://www.assembly.ca.gov/media/2743) | asset california-state-assembly-seal.webp (currentMark, California State Assembly) |
| `done/Cameron-Smyth-2017-820x1024-1.jpg` | 76 KB | 820x1024 | Downloaded 2026-09-29 from https://santaclaritamagazine.com/wp-content/uploads/2019/12/Cameron-Smyth-2017-820x1024-1.jpg (page: https://santaclaritamagazine.com/2019/12/cameron-smyth-city-councilmember-selected-as-mayor-for-2020/) | asset #28261 cameron-smyth-2017.jpg |
| `done/Candidate-Dr.AakashAhuja.jpg` | 86 KB | 840x1001 | Downloaded 2026-09-30 from https://santaclaritamagazine.com/wp-content/uploads/2024/10/Candidate-Dr.AakashAhuja.jpg (page: https://santaclaritamagazine.com/2024/10/vote-dr-aakash-ahuja-for-hart-school-board-2/) | asset #27350 aakash-ahuja-campaign-image.jpg |
| `done/maria-gutzeit-avatar.png` | 253 KB | 600x600 | Downloaded 2026-09-28 from https://drive.usercontent.google.com/download?id=1ErafnrkmeYCR3NDhUo85KnB-f5HW9o3I&export=download&authuser=0&confirm=t&uuid=9a3c6471-9772-4762-ac4b-d75dff042cbf&at=AMrWOn1SnQsxMRr5_2-KSRuMG35G:1790649622364 | asset #21581 maria-gutzeit-campaign-avatar.png |
| `done/michael_vierra_2025.jpg` | 177 KB | 1280x1920 | Downloaded 2026-10-04 from https://3.files.edl.io/6b56/26/07/27/213213-081cfa2c-da77-482a-9bcd-e3befc477f8d.jpg | asset #28980 michael-vierra-hart-district.jpg |
| `done/Schiavo-030-11-29-22.jpg` | 4.5 MB | 5504x7706 | Downloaded 2026-10-04 from https://schiavo.asmdc.org/sites/schiavo.asmdc.org/files/inline-images/Schiavo-030-11-29-22.jpg | asset pilar-schiavo.jpg (featuredImage, Pilar Schiavo) |
| `done/Trunkey-Chris-scaled.jpg` | 515 KB | 2560x2406 | Downloaded 2026-10-03 from https://santaclaritamagazine.com/wp-content/uploads/2022/08/Trunkey-Chris-scaled.jpg (page: https://santaclaritamagazine.com/2022/09/meet-the-candidate-chris-trunkey-running-for-saugus-union-school-district-2/) | asset #28197 chris-trunkey-campaign-image.jpg |
| `IMG_4024-2048x1983.jpg` | 259 KB | 2048x1983 | Downloaded 2026-09-30 from https://stfrancisdammemorial.org/wp-content/uploads/2019/12/IMG_4024-2048x1983.jpg | A portrait from the St. Francis Dam memorial site. Not imported. |

### Adobe Firefly outputs (downloaded from firefly.adobe.com): 37

Where to look: Your Firefly account (Files / history); ~/Downloads.

| File | Size | Pixels | Origin | Became / note |
|---|---|---|---|---|
| `Cameron-Smyth.jpg` | 1.1 MB | 3072x4096 | downloaded 2026-10-03 | Firefly enlargement of the Santa Clarita Magazine 2017 portrait. Not imported. |
| `done/adrian-w-adams-hm7301_large.jpg` | 4.5 MB | 3043x4057 | downloaded 2026-10-06 | asset #31454 adrian-w-adams-hm7301_large.jpg (file replaced in place) |
| `done/Alan-Ferdman.jpg` | 165 KB | 891x1190 | downloaded 2026-09-30 | asset #27391 alan-ferdman-edited.jpg |
| `done/arthur-b-perkins-outstanding-citizen-newhall-1964-1.jpg` | 1.3 MB | 3035x4051 | downloaded 2026-09-30 | asset #27381 arthur-b-perkins-outstanding-citizen-1964-edited.jpg |
| `done/Audra_Strickland.jpg` | 956 KB | 2251x3000 | downloaded 2026-10-06 | asset #31465 audra-strickland.jpg (6 October 2026) |
| `done/bill-cooper.jpg` | 1.4 MB | 4088x5452 | downloaded 2026-10-01 | asset #27389 bill-cooper-edited.jpg |
| `done/BillMiranda.jpg` | 413 KB | 1500x2000 | downloaded 2026-10-01 | asset #27402 bill-miranda-edited.jpg |
| `done/BJ-Atkins.jpg` | 4.2 MB | 4140x5520 | downloaded 2026-10-06 | asset #31443 bj-atkins.jpg (6 October 2026) |
| `done/Bob-Jenson.jpg` | 1.1 MB | 2647x3528 | downloaded 2026-10-04 | asset #28976 bob-jensen-hart-district.jpg |
| `done/brianwalters.png` | 3.6 MB | 1856x2272 | downloaded 2026-10-03 | asset #29118 brian-walters.png |
| `done/cave-johnson-couts-portrait-us-army.png` | 4.4 MB | 1696x2480 | downloaded 2026-10-01 | asset #27387 cave-johnson-couts-us-army-edited.png |
| `done/Cherise-Moore.jpg` | 792 KB | 2251x3004 | downloaded 2026-10-04 | asset #28974 cherise-moore-hart-district.jpg |
| `done/darrylmanzer2020-copy-2026-10-06.jpg` | 1.6 MB | 3760x5014 | downloaded 2026-10-06 |  |
| `done/DemetriusGScofield.jpg` | 978 KB | 2167x2892 | downloaded 2026-09-28 | asset #27383 demetrius-g-scofield-1911-edited.jpg |
| `done/Erin-Wilson.jpg` | 1.2 MB | 3360x4480 | downloaded 2026-10-04 | asset #28972 erin-wilson-hart-district.jpg |
| `done/General_Andres_Pico.jpg` | 3.1 MB | 3680x4544 | downloaded 2026-09-30 | asset #27385 andres-pico-commons-edited.jpg |
| `done/George-Runner.jpg` | 610 KB | 1800x2400 | downloaded 2026-10-04 | asset #29124 george-runner.jpg |
| `done/JasonGibbs.jpg` | 355 KB | 1500x2000 | downloaded 2026-10-01 | asset #27400 jason-gibbs-edited.jpg |
| `done/jerry-gladbach-portrait.jpg` | 1.3 MB | 2545x3394 | downloaded 2026-10-06 | asset #31417 jerry-gladbach-portrait.jpg (6 October 2026) |
| `done/Joe-Messina.jpg` | 2.6 MB | 3200x4276 | downloaded 2026-10-04 | asset #28978 joe-messina-hart-district.jpg |
| `done/john-c-fremont-portrait-california-state-library-scaled.jpg` | 1.9 MB | 2587x3450 | downloaded 2026-10-03 | asset #28814 john-c-fremont-california-state-library-upscaled.jpg |
| `done/leon-worden-cowboy-hat.jpg` | 1.3 MB | 3128x4168 | downloaded 2026-09-30 | asset #27396 leon-worden-edited.jpg |
| `done/lw2317a_large-writing-removed.jpg` | 2.7 MB | 3552x4736 | downloaded 2026-10-06 | asset #31410 lw2317a_large-writing-removed.jpg (6 October 2026) |
| `done/lw2427_large-copy-2026-10-06.jpg` | 2.1 MB | 3424x4566 | downloaded 2026-10-06 |  |
| `done/MarshaMclean.jpg` | 381 KB | 1500x2000 | downloaded 2026-10-01 | asset #27398 marsha-mclean-edited.jpg |
| `done/PatsyAyala.jpg` | 365 KB | 1500x2000 | downloaded 2026-10-01 | asset #27394 patsy-ayala-edited.jpg |
| `done/Patti-Rasmussen.jpg` | 1.9 MB | 3456x4608 | downloaded 2026-10-04 | asset #29122 patti-rasmussen.jpg |
| `done/pete-knight.jpg` | 2.8 MB | 3552x4736 | downloaded 2026-10-06 | asset #31414 pete-knight.jpg, Pete Knight's portrait (6 October 2026) |
| `done/randywicks1995_karzinphoto_large-copy-2026-10-06.jpg` | 2.6 MB | 4800x3500 | downloaded 2026-10-06 |  |
| `done/sg19720614claffey01_zoom.jpg` | 2.4 MB | 3135x4084 | downloaded 2026-10-06 | asset #31236 sg19720614claffey01_large.jpg (6 October 2026) |
| `done/Sharon-Runner.jpg` | 625 KB | 1800x2400 | downloaded 2026-10-04 | asset sharon-runner.jpg (featuredImage, Sharon Runner) |
| `done/sk5003_large-copy-2026-10-06.jpg` | 3.2 MB | 3552x4704 | downloaded 2026-10-06 |  |
| `done/Steve-Knight.jpg` | 682 KB | 1800x2400 | downloaded 2026-10-04 | asset steve-knight.jpg (featuredImage, Steve Knight) |
| `done/stroup_clara-copy-2026-10-06.jpg` | 746 KB | 1872x2496 | downloaded 2026-10-06 |  |
| `done/tiburcio-vasquez.jpg` | 2.7 MB | 3360x4992 | downloaded 2026-10-03 | asset #28816 tiburcio-vasquez-1874-upscaled.jpg |
| `henry-clay-wiley-portrait.jpg` | 1.1 MB | 2304x3072 | downloaded 2026-09-17 | Firefly output; the original of the asset henry-clay-wiley-portrait.jpg (#1658). |
| `Sharlene-Duzick.jpg` | 1.3 MB | 2815x3755 | downloaded 2026-10-04 | Firefly upscale of sharlene-headshot.jpg (in git). Not used, by decision. |

### Marks edited in Adobe Firefly, saved with no download record: 5

Where to look: Your Firefly account; ~/Downloads; wherever the pre-edit file came from (the body's website).

| File | Size | Pixels | Origin | Became / note |
|---|---|---|---|---|
| `done/california-state-senate.png` | 11.4 MB | 3584x3584 | Edited in Adobe Firefly (its content credentials say so); no download record on the file | asset california-state-senate-seal.png (currentMark, California State Senate) |
| `done/canyon-high-logo.png` | 6.2 MB | 3072x3072 | Edited in Adobe Firefly (its content credentials say so); no download record on the file | asset #29298 canyon-high-school-logo.png (currentMark, Canyon High School) |
| `done/CLWD.png` | 1.8 MB | 2700x2700 | Edited in Adobe Firefly (its content credentials say so); no download record on the file | asset castaic-lake-water-agency-logo.png (former mark, Castaic Lake Water Agency; edited with Adobe Firefly, recorded) |
| `done/NCWD.png` | 1.9 MB | 2700x2700 | Edited in Adobe Firefly (its content credentials say so); no download record on the file | asset newhall-county-water-district-logo.png (former mark, Newhall County Water District; edited with Adobe Firefly, recorded) |
| `done/Newhall-School-District.png` | 4.5 MB | 2700x2700 | Edited in Adobe Firefly (its content credentials say so); no download record on the file | asset newhall-school-district-logo-2700.png (currentMark, Newhall School District; the 102-pixel asset kept as superseded) |

### SVG marks exported from Adobe Illustrator: 3

Where to look: Your Illustrator / Creative Cloud files; the body's website.

| File | Size | Pixels | Origin | Became / note |
|---|---|---|---|---|
| `done/city-of-santa-clarita.svg` | 43 KB | 405.560x405.570 | Adobe Illustrator SVG export (no download record); made or saved on a Mac on 4 October | asset #29304 city-of-santa-clarita-seal.svg (currentMark, The City of Santa Clarita) |
| `done/hart-high.svg` | 28 KB | 405.490x405.640 | Adobe Illustrator SVG export (no download record); made or saved on a Mac on 4 October | asset #29296 hart-high-school-logo.svg (currentMark, Hart High School) |
| `done/la-county-seal.svg` | 163 KB | 405.560x405.570 | Adobe Illustrator SVG export (no download record); made or saved on a Mac on 4 October | asset #29302 los-angeles-county-seal.svg (currentMark, County of Los Angeles) |

### Grok-generated images: 9

Where to look: Your grok.com image history; ~/Downloads.

| File | Size | Pixels | Origin | Became / note |
|---|---|---|---|---|
| `edited-image (1).jpg` | 1018 KB | 2176x912 | downloaded 2026-09-29 | Generated banner (2176x912). Not imported. |
| `edited-image.jpg` | 1.0 MB | 2176x912 | downloaded 2026-09-29 | Same bytes as grok-image-fb994b75-....jpg. Generated banner. |
| `eMU3V.jpg` | 622 KB | 2176x912 | downloaded 2026-09-29 | Generated drawing: a man in a hat beside a map (a banner-style image). Not imported. |
| `grok-image-fb994b75-1760-4be5-a4da-3623d2bc4605.jpg` | 1.0 MB | 2176x912 | downloaded 2026-09-17 | Generated banner. Same bytes as edited-image.jpg. |
| `reynolds=map-2.jpg` | 396 KB | 832x1232 | downloaded 2026-09-17 | Generated image for the Reynolds chapters. Not imported. |
| `reynolds=map-3.jpg` | 389 KB | 832x1232 | downloaded 2026-09-17 | Generated image. Same bytes as reynolds=map-4.jpg. |
| `reynolds=map-4.jpg` | 389 KB | 832x1232 | downloaded 2026-09-17 | Generated image. Same bytes as reynolds=map-3.jpg. |
| `reynolds=map.jpg` | 632 KB | 832x1232 | downloaded 2026-09-17 | Generated image for the Reynolds chapters. Not imported. |
| `WilliamS.jpg` | 290 KB | 2000x857 | downloaded 2026-09-29 | Generated banner-shaped image (2000x857). Not imported. |

### No download record, or received another way: 6

Where to look: ~/Downloads, Mail or Messages attachments, the iPhone Photos library (IMG_2226 2.jpg), Flickr (photo 2600036728).

| File | Size | Pixels | Origin | Became / note |
|---|---|---|---|---|
| `done/anna-griese.jpg` | 1.1 MB | 3061x4592 | No download record; opened in Preview on 2026-10-04 | asset #29120 anna-griese-schlickart-2022.jpg; SchlickArt portrait (Lindsay Schlick, Canon 5D Mark IV, 25 April 2022, Lightroom). Opened in Preview 4 October 2026; no download record. |
| `done/Castaic-High.png` | 968 KB | 3072x3072 | No download record | asset castaic-high-school-logo.png (currentMark, Castaic High School) |
| `done/lasd.png` | 1.1 MB | 1690x1690 | No download record | asset lasd-star.png (currentMark, Los Angeles County Sheriff's Department) |
| `IMG_2226 2.jpg` | 630 KB | 1492x2652 | Received by AirDrop | A personal photograph (a runner in the Tokyo Marathon 2025), taken 2 March 2025, received by AirDrop on 9 September 2026. Not SCV history. Not imported. |
| `santa-clarita-city-hall-flickr-2600036728.jpg` | 2.5 MB | 3648x2736 | No download record | Flickr photo 2600036728 (Kodak EasyShare camera, 19 June 2008). The original of asset santa-clarita-city-hall-2008-flickr.jpg. |
| `tank-guy.jpg` | 1.3 MB | 2280x2280 | No download record; opened in Preview on 2026-09-25 | The 1989 Tiananmen Square "Tank Man" photograph. Not SCV history. Not imported. |

### Your downloads of the archive's own files from the local site: 15

Where to look: Already in the archive; for legacy names the master is on Reggie.

| File | Size | Pixels | Origin | Became / note |
|---|---|---|---|---|
| `done/abel-stearns-portrait-california-state-library-1840-1860-scaled.jpg` | 924 KB | 2048x2560 | Your download of the archive's own file from scvhistory.ddev.site on 2026-09-30 (persons/abel-stearns-portrait-california-state-library-1840-1860-scaled.jpg) | web/uploads/archive-media/persons/abel-stearns-portrait-california-state-library-1840-1860-scaled.jpg |
| `done/ap2222_large-copy-2026-10-06.jpg` | 9.3 MB | 2400x3610 | Your download of the archive's own file from scvhistory.ddev.site on 2026-10-06 (legacy/ap2222_large.jpg) |  |
| `done/cylde smyth.jpg` | 413 KB | 800x1158 | Your download of the archive's own file from scvhistory.ddev.site on 2026-09-30 (legacy/lw2809.jpg) | web/uploads/archive-media/legacy/lw2809.jpg |
| `done/edwin-bryant.jpg` | 27 KB | 479x600 | Your download of the archive's own file from scvhistory.ddev.site on 2026-09-17 (persons/edwin-bryant.jpg) | web/uploads/archive-media/persons/edwin-bryant.jpg |
| `done/history-santa-clarita-jerry-reynolds.jpg` | 558 KB | 1200x675 | Your download of the archive's own file from scvhistory.ddev.site on 2026-09-17 (persons/history-santa-clarita-jerry-reynolds.jpg) | web/uploads/archive-media/persons/history-santa-clarita-jerry-reynolds.jpg |
| `done/history-santa-clarita-preface-jerry-reynolds.jpg` | 503 KB | 1200x674 | Your download of the archive's own file from scvhistory.ddev.site on 2026-09-17 (persons/history-santa-clarita-preface-jerry-reynolds.jpg) | web/uploads/archive-media/persons/history-santa-clarita-preface-jerry-reynolds.jpg |
| `done/lw2317a_large-copy-2026-10-06.jpg` | 1.6 MB | 1600x2133 | Your download of the archive's own file from scvhistory.ddev.site on 2026-10-06 (legacy/lw2317a_large.jpg) |  |
| `done/lw9501_large-copy-2026-10-06.jpg` | 4.3 MB | 2400x3058 | Your download of the archive's own file from scvhistory.ddev.site on 2026-10-06 (legacy/lw9501_large.jpg) |  |
| `done/obituary_kevingarylynch-copy-2026-10-06.jpg` | 295 KB | 800x1035 | Your download of the archive's own file from scvhistory.ddev.site on 2026-10-06 (legacy/obituary_kevingarylynch.jpg) |  |
| `done/reminadeau-chrisman-copy-2026-10-06.jpg` | 72 KB | 334x504 | Your download of the archive's own file from scvhistory.ddev.site on 2026-10-06 (legacy/reminadeau-chrisman.jpg) |  |
| `done/rn3004-copy-2026-10-06.jpg` | 38 KB | 400x437 | Your download of the archive's own file from scvhistory.ddev.site on 2026-10-06 (legacy/rn3004.jpg) |  |
| `done/rr1-copy-2026-10-06.jpg` | 21 KB | 225x311 | Your download of the archive's own file from scvhistory.ddev.site on 2026-10-06 (legacy/rr1.jpg) |  |
| `done/sg19720614claffey01_large-copy-2026-10-06.jpg` | 4.0 MB | 2400x2385 | Your download of the archive's own file from scvhistory.ddev.site on 2026-10-06 (legacy/sg19720614claffey01_large.jpg) |  |
| `done/tf1000-copy-2026-10-06.jpg` | 129 KB | 800x1246 | Your download of the archive's own file from scvhistory.ddev.site on 2026-10-06 (legacy/tf1000.jpg) |  |
| `done/william-lewis-manly-portrait-1890s.jpg` | 93 KB | 700x963 | Your download of the archive's own file from scvhistory.ddev.site on 2026-10-06 (persons/william-lewis-manly-portrait-1890s.jpg) | not imported: Nathan's download of the original already in the archive as asset #21 (6 October 2026) |

### Crops made by the archive: 5

Where to look: Not needed: it can be cut again from the Reggie original.

| File | Size | Pixels | Origin | Became / note |
|---|---|---|---|---|
| `done/john-boston-cropped-from-sg030506b-honby.jpg` | 66 KB | 300x375 | Crop made by the archive (Claude) on 5 or 6 October from gif/sg030506b-honby (Reggie) | asset #31938 (John Boston's portrait, a crop), 6 October 2026 |
| `done/johnward_dorothyward_crop-john-ward.jpg` | 232 KB | 370x823 | Crop made by the archive (Claude) on 5 or 6 October from gif/johnward_dorothyward.jpg (Reggie) | asset #31258 john-amos-ward-cropped-from-johnward_dorothyward.jpg (6 October 2026) |
| `done/michele-jenkins-cropped-from-co1501c.jpg` | 51 KB | 280x350 | Crop made by the archive (Claude) on 5 or 6 October from gif/co1501c (Reggie) | asset #31932 (Michele R. Jenkins's portrait, a crop), 6 October 2026 |
| `done/sg19950324bowman_large_crop-jereann-bowman.jpg` | 1.9 MB | 860x1200 | Crop made by the archive (Claude) on 5 or 6 October from gif/sg19950324bowman_large.jpg (Reggie) | asset #31257 jereann-bowman-cropped-from-sg19950324bowman.jpg (6 October 2026) |
| `done/tom-frew-iv-cropped-from-hs9019.jpg` | 20 KB | 170x212 | Crop made by the archive (Claude) on 5 or 6 October from gif/hs9019 (Reggie) | asset #31935 (Tom Frew IV's portrait, a crop), 6 October 2026 |

### Screenshots of the archive's own pages: 16

Where to look: Not needed: it can be taken again from the site.

| File | Size | Pixels | Origin | Became / note |
|---|---|---|---|---|
| `beales-cut-sta.jpg` | 2.5 MB | 3066x4682 | 066 px wide) |  |
| `chapter-21-b.jpg` | 5.5 MB | 3066x6614 | 066 px wide) |  |
| `dante-acosta.jpg` | 2.2 MB | 3066x4782 | 066 px wide) |  |
| `getting-closer.jpg` | 5.6 MB | 3066x6902 | 066 px wide) |  |
| `henry-clay-w.jpg` | 2.7 MB | 3066x4030 | 066 px wide) |  |
| `history-of-the-santa-clarita-va.jpg` | 1.6 MB | 3066x7370 | 066 px wide) |  |
| `in-memoriam-henry-clay-wiley-182.jpg` | 3.4 MB | 3066x8742 | 066 px wide) |  |
| `in-memoriam.jpg` | 3.6 MB | 3066x5544 | 066 px wide) |  |
| `newhall-pass-i.jpg` | 4.3 MB | 3066x4278 | 066 px wide) |  |
| `northridge-ear.jpg` | 5.7 MB | 3066x5674 | 066 px wide) |  |
| `not-even-close.jpg` | 4.6 MB | 3066x7408 | 066 px wide) |  |
| `prologue-his.jpg` | 5.6 MB | 3066x6010 | 066 px wide) |  |
| `rancho.jpg` | 3.4 MB | 3066x4288 | 066 px wide) |  |
| `rodolfo-acost.jpg` | 1.3 MB | 3066x3450 | 066 px wide) |  |
| `rudy.jpg` | 3.6 MB | 3066x4140 | 066 px wide) |  |
| `the-birth-of.jpg` | 5.5 MB | 3066x6998 | 066 px wide) |  |

## 2. The 37 "supplied by Nathan"

Each is the file Craft wrote when the handed-over file was imported, so none of them is an original. The handed-over file is the original, named in the third column; "in the 116" means it is listed above, "git" means it is already committed.

| Web file (web/uploads/archive-media/) | Asset | Made from (inventory/incoming/done/) | Original held | What it is |
|---|---|---|---|---|
| `legacy/jereann-bowman-cropped-from-sg19950324bowman.jpg` | #31257 | `sg19950324bowman_large_crop-jereann-bowman.jpg` | in the 116 | Cropped by the archive on 5 October 2026, for Nathan Imhoff, from the obituary clipping held as the original: SCVHistory.com, gif/sg19950324bowman_large.jpg, the archival scan linked from /scvhistory/. |
| `legacy/john-amos-ward-cropped-from-johnward_dorothyward.jpg` | #31258 | `johnward_dorothyward_crop-john-ward.jpg` | in the 116 | Cropped by the archive on 5 October 2026, for Nathan Imhoff, from the double portrait "John and Dorothy Ward" held as asset #1973: SCVHistory.com, gif/johnward_dorothyward.jpg, on /warmemorial/ww2_joh. |
| `marks/california-state-assembly-seal.webp` | #29398 | `california-assembly.webp` | in the 116 | The seal of the California State Assembly, supplied by Nathan Imhoff on 4 October 2026; where it was taken from is not recorded with the file. |
| `marks/california-state-senate-seal.png` | #29400 | `california-state-senate.png` | in the 116 | The seal of the California State Senate, supplied by Nathan Imhoff on 4 October 2026; where it was taken from is not recorded with the file. |
| `marks/canyon-high-school-logo.png` | #29298 | `canyon-high-logo.png` | in the 116 | Canyon High School's logo, from its website, retrieved 4 October 2026 (Nathan Imhoff); the page it was taken from is not recorded with the file. |
| `marks/castaic-high-school-logo.png` | #29294 | `Castaic-High.png` | in the 116 | Castaic High School's logo, from the school's website, retrieved 4 October 2026 (Nathan Imhoff); the page it was taken from is not recorded with the file. |
| `marks/castaic-lake-water-agency-logo.png` | #29563 | `CLWD.png` | in the 116 | Logo of the Castaic Lake Water Agency, supplied by Nathan Imhoff on 4 October 2026 as the mark the body used; where it was taken from is not recorded with the file, and no unedited original is held. |
| `marks/city-of-santa-clarita-seal.svg` | #29304 | `city-of-santa-clarita.svg` | in the 116 | The seal of the City of Santa Clarita, supplied by Nathan Imhoff on 4 October 2026; where it was taken from is not recorded with the file. |
| `marks/csun-seal.png` | #29300 | `CSUN_Seal.png` | in the 116 | The seal of California State University, Northridge, supplied by Nathan Imhoff on 4 October 2026; where it was taken from is not recorded with the file. |
| `marks/hart-high-school-logo.svg` | #29296 | `hart-high.svg` | in the 116 | Hart High School's logo, from its website, retrieved 4 October 2026 (Nathan Imhoff); the page it was taken from is not recorded with the file. |
| `marks/lasd-star.png` | #29306 | `lasd.png` | in the 116 | The Sheriff's Department's star, supplied by Nathan Imhoff on 4 October 2026; where it was taken from is not recorded with the file. |
| `marks/los-angeles-county-seal.svg` | #29302 | `la-county-seal.svg` | in the 116 | The seal of the County of Los Angeles, supplied by Nathan Imhoff on 4 October 2026; where it was taken from is not recorded with the file. |
| `marks/newhall-county-water-district-logo.png` | #29561 | `NCWD.png` | in the 116 | Logo of the Newhall County Water District, supplied by Nathan Imhoff on 4 October 2026 as the mark the body used; where it was taken from is not recorded with the file, and no unedited original is hel. |
| `marks/newhall-elementary-school-logo-original.jpg` | #29698 | `newhall-elementary.jpeg` | git | Newhall Elementary School's logo, the N with "Eagles", supplied by Nathan Imhoff on 4 October 2026 as the unedited original of the mark shown on the school's record (asset #29696, edited with Adobe Fi. |
| `marks/newhall-elementary-school-logo.png` | #29696 | `newhall-elementary.png` | git | Newhall Elementary School's logo, the N with "Eagles", supplied by Nathan Imhoff on 4 October 2026 after editing; where it was taken from is not recorded with the file. |
| `marks/newhall-school-district-logo-2700.png` | #29439 | `Newhall-School-District.png` | in the 116 | The Newhall School District's logo, a larger version supplied by Nathan Imhoff on 4 October 2026; where it was taken from is not recorded with the file. |
| `marks/santa-clarita-christian-school-logo.png` | #29694 | `SCCS.png` | git | Santa Clarita Christian School's logo, the C with the Cardinal's head, supplied by Nathan Imhoff on 4 October 2026; where it was taken from is not recorded with the file. |
| `marks/united-states-congress-seal.svg` | #29396 | `Seal_of_the_United_States_Congress.svg` | in the 116 | The seal of the United States Congress, supplied by Nathan Imhoff on 4 October 2026; where it was taken from is not recorded with the file. |
| `outside/alan-ferdman-edited.jpg` | #27391 | `Alan-Ferdman.jpg` | in the 116 | Supplied and edited by Nathan Imhoff; the original photograph is not recorded. |
| `outside/anna-griese-schlickart-2022.jpg` | #29120 | `anna-griese.jpg` | in the 116 | Supplied by Nathan Imhoff on 4 October 2026. |
| `outside/arthur-b-perkins-outstanding-citizen-1964-edited.jpg` | #27381 | `arthur-b-perkins-outstanding-citizen-newhall-1964-1.jpg` | in the 116 | Edited by Nathan Imhoff from the archive's own 1964 photograph of Perkins as Outstanding Citizen. |
| `outside/audra-strickland.jpg` | #31465 | `Audra_Strickland.jpg` | in the 116 | Supplied by Nathan Imhoff on October 6, 2026. |
| `outside/bill-cooper-edited.jpg` | #27389 | `bill-cooper.jpg` | in the 116 | Edited by Nathan Imhoff from a photograph on Bill Cooper's campaign site or the SCV Water board page (campaign or agency material); which page is not recorded. |
| `outside/bill-miranda-edited.jpg` | #27402 | `BillMiranda.jpg` | in the 116 | The City of Santa Clarita's council portrait, upscaled by Nathan Imhoff. |
| `outside/brian-walters.png` | #29118 | `brianwalters.png` | in the 116 | Supplied by Nathan Imhoff on 3 October 2026. |
| `outside/cameron-smyth-2017.jpg` | #28261 | `Cameron-Smyth-2017-820x1024-1.jpg` | in the 116 | Supplied by Nathan Imhoff, a resized copy from a web page; which page, the photographer and the rights holder are not recorded. |
| `outside/cameron-smyth-nathan-imhoff.jpg` | #21579 | `Smyth.jpg` | missed by the census (section 3) | Photograph by Nathan Imhoff, who holds the copyright and licenses it to SCVHistory. |
| `outside/cave-johnson-couts-us-army-edited.png` | #27387 | `cave-johnson-couts-portrait-us-army.png` | in the 116 | Edited by Nathan Imhoff from a U.S. |
| `outside/chris-trunkey-campaign-image.jpg` | #28197 | `Trunkey-Chris-scaled.jpg` | in the 116 | Campaign material, supplied by Nathan Imhoff: a photograph by Jennifer Emery for his campaign, July 2016, as the file's own notice records. |
| `outside/jason-gibbs-edited.jpg` | #27400 | `JasonGibbs.jpg` | in the 116 | The City of Santa Clarita's council portrait, upscaled by Nathan Imhoff. |
| `outside/john-c-fremont-california-state-library-upscaled.jpg` | #28814 | `john-c-fremont-portrait-california-state-library-scaled.jpg` | in the 116 | The California State Library's photograph of Frémont, upscaled by Nathan Imhoff. |
| `outside/leon-worden-edited.jpg` | #27396 | `leon-worden-cowboy-hat.jpg` | in the 116 | Supplied and edited by Nathan Imhoff; the original photograph is not recorded. |
| `outside/marsha-mclean-edited.jpg` | #27398 | `MarshaMclean.jpg` | in the 116 | The City of Santa Clarita's council portrait, upscaled by Nathan Imhoff. |
| `outside/patsy-ayala-edited.jpg` | #27394 | `PatsyAyala.jpg` | in the 116 | Supplied and upscaled by Nathan Imhoff; the original photograph is not recorded. |
| `outside/pilar-schiavo.jpg` | #29442 | `Schiavo-030-11-29-22.jpg` | in the 116 | Supplied by Nathan Imhoff on 4 October 2026. |
| `outside/sharlene-rose-johnson.jpg` | #30544 | `sharlene-headshot.jpg` | git | Supplied by Nathan Imhoff on 4 October 2026, with a copy enlarged by Adobe Firefly that the archive does not use. |
| `outside/tiburcio-vasquez-1874-upscaled.jpg` | #28816 | `tiburcio-vasquez.jpg` | in the 116 | The 1874 photograph of Vasquez, upscaled by Nathan Imhoff. |

## 3. Missed by the census: 5 files

The census counted a file as Reggie's whenever Reggie held any file with the same name (a name match, because Craft re-saves every upload). For files in inventory/incoming that rule is wrong: these five have names that happen to exist on Reggie but are not Reggie's pictures. Their checksums match nothing in git or on Reggie.

| File (inventory/incoming/) | Size | Pixels | Origin | Became | Reggie file of the same name |
|---|---|---|---|---|---|
| `done/Smyth.jpg` | 4.8 MB | 3128x2086 | Downloaded 28 September from a Google Drive share (your photograph of Cameron Smyth, which you license to the archive) | asset #21579 cameron-smyth-nathan-imhoff.jpg | oldtownnewhall/gif/smyth.jpg, a different picture |
| `done/danhon.jpg` | 656 KB | 2275x3036 | Adobe Firefly output, 6 October | asset #14933 danhon.jpg (6 October 2026) | gif/danhon.jpg, the unedited original |
| `done/sc1310.jpg` | 1.6 MB | 3000x4000 | Adobe Firefly output, 6 October | asset #27852 sc1310.jpg (6 October 2026) | gif/sc1310.jpg, the unedited original |
| `done/sc9611.jpg` | 2.1 MB | 3648x4864 | Adobe Firefly output, 6 October | asset #27865 sc9611.jpg (6 October 2026) | gif/sc9611.jpg, the unedited original |
| `lw2184.jpg` | 538 KB | 1439x2132 | Adobe Firefly output, 20 September (not imported; listed as unidentified in OUTSTANDING.md) |  | gif/lw2184.jpg, the unedited original |

The other five incoming files the census matched by name (ap1334.jpg, lw2054.jpg, lw2452.jpg, lw2529.jpg and sc9010.jpg, all in done/) carry a download record from scvhistory.ddev.site/uploads/archive-media/legacy/, so they are copies of the archive's own files, as the census said. A Firefly edit of a legacy picture that replaced the asset in place (danhon, sc1310, sc9611) also means the web file is a copy of the edit, not of Reggie's file. The fix to the census is a checksum on the handed-over file, which MANIFEST.json already records.
