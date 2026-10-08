# What sits in each record's own folder and not in Craft (8 October 2026)

Read-only census, Claude, for Nathan ("Walk every record's own folder on Reggie ... Not a sample"). Script: scripts/import/folder_census_2026_10_08.py; data storage/runtime/photo-import/folder-census.json.

## What it read

- **The folders themselves**, file by file on the drive (/mnt/reggie/scvhistory.com/scvhistory/files/, 1044 folders), not a page's links and not a manifest.
- **Which folder is a record's own:** its name is the record's page name or legacyKey, or the record's page on Reggie links into it. 152 folders are claimed by a Craft record. 3 linked from more than three pages are shared material, not one record's, and are left out of the count (centurypacific19320215 (4 pages), citizen19880918 (290 pages), guestbook_1999-2006 (7 pages)).
- **Content:** pictures (jpg, png, gif, tif, jp2), PDFs, audio, video, office files. Left out: the flipbook and slideshow software and its renders (files/, mobile/, engine1/, data1/thumbnails, data1/tooltips, zoom tiles), and html, js, css, xml and fonts.
- **Held** means an asset on that record has the file's path, its checksum, or its name (less _large, _orig, a -p1 cover suffix or the import's folder prefix, compared without punctuation). One picture saved twice in a folder is one item.
- **Craft** as dumped on 8 October (scripts/import/folder_census_dump_2026_10_08.php), while the 15 held records were being written.
- **Not covered:** 249 records whose page is not on Reggie (the mirror's known limit), except where a folder carries their name; and the 892 folders no Craft record claims, nearly all behind the 5,606 legacy pages that have no record yet.

## The number

**100 records have content in their own folder that Craft does not hold: 1615 items, 2430 files.** None of the missing files is in Craft on another record.

4 folders are claimed by two records each (gt8702: #28293 and #28310; lw3789: #605 and #5645; lw3790: #613 and #5647; scvhs2000minutes: #28283 and #28285), so the table below counts their files under both. Counted once: **1549 items, 2278 files.**

| Group | Records | Items | Files |
|---|---|---|---|
| articles | 4 | 46 | 50 |
| documents | 8 | 767 | 839 |
| photographs imported 7 October | 52 | 269 | 373 |
| photographs skipped, already had a picture | 32 | 480 | 1064 |
| places | 2 | 51 | 102 |
| warMemorials | 2 | 2 | 2 |

By kind: 1551 picture, 62 pdf, 1 office, 1 audio.

## By record

| Record | Section | Title | Folder | Held | Not held | Files |
|---|---|---|---|---|---|---|
| #605 | places | Heritage Junction Historic Park | lw3789 | 0 | 39 (39 picture) | 78 |
| #613 | places | Six Flags Magic Mountain | lw3790 | 0 | 12 (12 picture) | 24 |
| #1384 | warMemorials | Edward Guy North | woodcock2013 | 0 | 1 (1 pdf) | 1 |
| #1395 | warMemorials | James Robert "Jimmie" Ball | gudgeon1949 | 0 | 1 (1 pdf) | 1 |
| #1436 | articles | Story of Little Santa Clara Valley | perkins_manuscript_ch2 | 0 | 16 (1 pdf, 15 picture) | 16 |
| #1442 | articles | Picture Story of Hart High School (and District) | harthigh_may1952 | 0 | 25 (1 pdf, 24 picture) | 25 |
| #1454 | articles | 42nd Annual Newhall Old West July 4th Celebration | hs_parade19680704book | 0 | 1 (1 pdf) | 1 |
| #2987 | photographs | Photo Gallery: Sandberg's Summit Hotel Site, 2006. | lw2214 | 1 | 5 (5 picture) | 10 |
| #3023 | photographs | Photo Gallery: 1876 Golden Spike. | lw2248 | 1 | 14 (14 picture) | 28 |
| #4675 | photographs | CCC Camp | hb2001 | 0 | 4 (4 picture) | 8 |
| #4755 | photographs | Blum Ranch Property For Sale | lw2974 | 0 | 33 (1 office, 32 picture) | 63 |
| #4761 | photographs | 2015 Parade, Inside-Out | lw2977 | 86 | 3 (3 picture) | 6 |
| #4767 | photographs | Demolition: 24326 Walnut Street | lw2980 | 44 | 3 (3 picture) | 4 |
| #4769 | photographs | Blue Cloud Chinchilla Dust Mine | lw2981 | 1 | 8 (8 picture) | 16 |
| #4783 | photographs | Tragedy on the Sweetwater | lw2992 | 7 | 1 (1 pdf) | 1 |
| #4785 | photographs | Favorite Field Trips for Los Angeles Gem Hunters | lw2993 | 36 | 1 (1 pdf) | 1 |
| #4801 | photographs | Proposed Reorganization of the Hart Union High School District Area | lw3007 | 5 | 1 (1 pdf) | 1 |
| #4803 | photographs | Verbiesen Ranch & Car Collection Damaged by Fire | lw3014 | 0 | 42 (42 picture) | 84 |
| #4821 | photographs | Newhall Ranch: A Community By Nature | lw3028 | 12 | 1 (1 pdf) | 1 |
| #4823 | photographs | The Piru Mansion | lw3029 | 4 | 1 (1 pdf) | 1 |
| #4859 | photographs | Arthur Young CPAs Predict 22% Budget Windfall for Proposed City of San | lw3059 | 2 | 1 (1 pdf) | 1 |
| #4861 | photographs | City Formation Committee Kicks Off Voter Registration Program to Get M | lw3060 | 2 | 1 (1 pdf) | 1 |
| #4863 | photographs | Dedication Ceremony of the Permanent Campus | lw3061 | 4 | 1 (1 pdf) | 1 |
| #4909 | photographs | India Never Had It So Good | lw3097 | 3 | 1 (1 pdf) | 1 |
| #4917 | photographs | 34th Annual Newhall-Saugus Rodeo | lw3103 | 10 | 1 (1 pdf) | 1 |
| #4919 | photographs | The Move to Secede from Los Angeles County | lw3104 | 4 | 1 (1 pdf) | 1 |
| #4943 | photographs | Bonelli Stadium Racing Program 5-26-1946 | lw3122 | 6 | 1 (1 pdf) | 1 |
| #4949 | photographs | Official Program: Bonelli Stadium Automobile Races | lw3134 | 4 | 1 (1 pdf) | 1 |
| #4951 | photographs | Future Veterans Historical Plaza Site | lw3135 | 6 | 1 (1 picture) | 1 |
| #4953 | photographs | Railroad Avenue Improvements | lw3136 | 0 | 20 (20 picture) | 58 |
| #4957 | photographs | RandyWicks.com | lw3139 | 0 | 13 (13 picture) | 37 |
| #4959 | photographs | Creekview Park Groundbreaking | lw3140 | 1 | 12 (12 picture) | 34 |
| #4981 | photographs | Souvenir Album: "Ben-Hur." | lw3164 | 0 | 41 (1 pdf, 40 picture) | 41 |
| #5011 | photographs | 5-Stamp Mill, Puritan Mine | lw3191 | 0 | 40 (40 picture) | 120 |
| #5019 | photographs | Application for the Incorporation of the City of "Santa Clarita." | lw3197 | 2 | 1 (1 pdf) | 1 |
| #5029 | photographs | Thousand Trails - Soledad Canyon | lw3219 | 24 | 1 (1 picture) | 2 |
| #5067 | photographs | Emery Whilton's Florafaunium | lw3247 | 0 | 8 (8 picture) | 22 |
| #5073 | photographs | The Old Road to Los Angeles | lw3254 | 8 | 1 (1 pdf) | 1 |
| #5077 | photographs | Chumash Storage Basket | brynecache2016, lw3257 | 19 | 14 (14 picture) | 14 |
| #5087 | photographs | Our Country's Mysterious Monsters | lw3265 | 5 | 1 (1 pdf) | 1 |
| #5089 | photographs | Arthur Charles Mentry's Blacksmith Forge | lw3267 | 1 | 13 (13 picture) | 39 |
| #5113 | photographs | Buzz Barton Home Torched | lw3284 | 0 | 8 (8 picture) | 22 |
| #5131 | photographs | Green Pastures Dairy | lw3306 | 0 | 7 (7 picture) | 21 |
| #5135 | photographs | Project Grudge: Incident near Camp Oak Flat, California | lw3308 | 19 | 1 (1 pdf) | 1 |
| #5137 | photographs | The Story of Dan Press | lw3312 | 4 | 1 (1 pdf) | 1 |
| #5145 | photographs | Commuting: Southern Pacific's Saugus Line | lw3316 | 10 | 1 (1 pdf) | 1 |
| #5199 | photographs | Filmnyheter, 21 November 1921 | lw3355 | 0 | 25 (1 pdf, 24 picture) | 25 |
| #5205 | photographs | Will and Charlie | lw3361 | 0 | 8 (1 pdf, 7 picture) | 8 |
| #5207 | photographs | Borax 20 Mule Team Scale Model Kit | lw3365 | 1 | 3 (2 pdf, 1 picture) | 3 |
| #5225 | photographs | Letter from William S. Hart to Amelia Earhart | lw3383 | 0 | 5 (1 pdf, 4 picture) | 5 |
| #5233 | photographs | The Boom Days of Staging | lw3390 | 6 | 1 (1 pdf) | 1 |
| #5235 | photographs | Rock Arch at Needham Ranch Moved, Again | lw3392, lw3392b | 155 | 5 (5 picture) | 9 |
| #5245 | photographs | Aggie | lw3404 | 7 | 1 (1 pdf) | 1 |
| #5251 | photographs | William S. Hart, Ione Reed, George Putnam | ionereed | 0 | 24 (1 audio, 1 pdf, 22 picture) | 25 |
| #5271 | photographs | Green Pastures Dairy | lw3441 | 0 | 6 (6 picture) | 18 |
| #5275 | photographs | California Fire Siege 2007 | lw3443 | 0 | 1 (1 pdf) | 1 |
| #5287 | photographs | Lebec (Hotel?) Restaurant and Coffee Shop Menu | lw3453 | 4 | 1 (1 pdf) | 1 |
| #5295 | photographs | Kingsburry House: Temporary Roof Repairs | lw3457 | 107 | 1 (1 picture) | 1 |
| #5297 | photographs | New Colonial Theatre (Beach Haven, N.J.) Program, Week of 7-28-1924 | lw3458 | 0 | 5 (1 pdf, 4 picture) | 5 |
| #5325 | photographs | Squadron of Giant Tillers Speed California's Castaic Dam | lw3481 | 2 | 1 (1 pdf) | 1 |
| #5357 | photographs | Original Tip's | lw3513 | 0 | 1 (1 pdf) | 1 |
| #5359 | photographs | 47th Annual Benefit Auction | lw3516 | 64 | 1 (1 pdf) | 1 |
| #5361 | photographs | He Sets the Sky On Fire! | lw3517 | 5 | 1 (1 pdf) | 1 |
| #5363 | photographs | The Pacific Mineralogist, December 1941 | lw3518 | 32 | 1 (1 pdf) | 1 |
| #5365 | photographs | James Garner as Maverick: "Relic of Fort Tejon." | lw3521 | 36 | 1 (1 pdf) | 1 |
| #5383 | photographs | Indian Dunes/Valencia, California | lw3534 | 4 | 2 (1 pdf, 1 picture) | 2 |
| #5385 | photographs | Southern Pacific and Texas & New Orleans Class M-4 2-6-0s | lw3536 | 7 | 1 (1 pdf) | 1 |
| #5399 | photographs | Who Knew? Perkins' SCV History Books Still Available to Researchers | lw3550 | 0 | 29 (29 picture) | 49 |
| #5403 | photographs | Visitors at Curry's Lebec Lodge | lw3552 | 1 | 6 (6 picture) | 18 |
| #5419 | photographs | Lebec Hotel and Rancho Coffee Shop Menu | lw3569 | 0 | 1 (1 pdf) | 1 |
| #5423 | photographs | Dragoon Walk: A Self-Guided Trail | lw3575 | 0 | 2 (2 picture) | 2 |
| #5425 | photographs | Indian Dunes' First Competitive Event | lw3576 | 5 | 1 (1 pdf) | 1 |
| #5427 | photographs | Filmprogrammheft: "Die Flamme von Arabien." | lw3578 | 4 | 1 (1 pdf) | 1 |
| #5435 | photographs | Geological Investigation of Future Piru Lake Site | lw3585 | 0 | 11 (11 picture) | 33 |
| #5469 | photographs | Pin (Brooch) by Joseff of Hollywood | lw3624 | 0 | 5 (5 picture) | 15 |
| #5473 | photographs | 3-Gal. Milk Can | lw3631 | 0 | 9 (9 picture) | 27 |
| #5491 | photographs | Hi Jolly Pioneer Cemetery | lw3647 | 12 | 1 (1 pdf) | 1 |
| #5507 | photographs | "The 77th Bengal Lancers." | lw3663 | 36 | 1 (1 pdf) | 1 |
| #5509 | photographs | Juventino del Valle, Black Walnut Tree; 5 Ranch Views | lw3664 | 1 | 7 (7 picture) | 28 |
| #5521 | photographs | George Blum's Stonecutting Tools Preserved | lw3676 | 0 | 47 (47 picture) | 141 |
| #5523 | photographs | Program Book: 6th Annual Hoot Gibson-Golden State Ranch Rodeo | lw3677 | 0 | 25 (1 pdf, 24 picture) | 25 |
| #5529 | photographs | LADWP House | lw3682 | 0 | 16 (16 picture) | 48 |
| #5577 | photographs | Seasoned with Grace | lw3743 | 148 | 72 (1 pdf, 71 picture) | 72 |
| #5597 | photographs | Fort Tejon State Historical Monument | lw3755 | 3 | 1 (1 pdf) | 1 |
| #5601 | photographs | Seco, Bouquet, Haskell, Plum Canyon Area | lw3759 | 0 | 1 (1 picture) | 1 |
| #5605 | photographs | Winkler Homestead Road: Past and Future Meet at Castaic High School | lw3761 | 0 | 14 (14 picture) | 28 |
| #5627 | photographs | Oronite Cleaning Fluid | lw3778 | 0 | 6 (6 picture) | 11 |
| #5645 | photographs | SPRR Saugus Depot Screenshots | lw3789 | 0 | 39 (39 picture) | 78 |
| #5647 | photographs | Repurposed Coach from Original Colossus | lw3790 | 0 | 12 (12 picture) | 24 |
| #5685 | photographs | Newhall-Saugus-Valencia-Canyon Country | lw6902 | 108 | 2 (2 pdf) | 2 |
| #5765 | photographs | Sulphur Springs Celebrates Quasquicentennial | lw9801, lw9801program | 0 | 36 (1 pdf, 35 picture) | 68 |
| #12577 | articles | Newhall Community Center Under Construction | sc0503 | 0 | 4 (4 picture) | 8 |
| #28281 | documents | A Brief History of the Push for Self-Government in Santa Clarita | cw9901 | 1 | 4 (4 picture) | 4 |
| #28283 | documents | Santa Clarita Valley Historical Society board minutes, May 19, 2003 | scvhs2000minutes | 0 | 2 (2 pdf) | 2 |
| #28285 | documents | "A Look Back in Time": proposal for the Historical Society's first ann | scvhs2000minutes | 0 | 2 (2 pdf) | 2 |
| #28293 | documents | Cityhood forum announced, The Signal, January 11, 1987 | gt8702 | 0 | 12 (12 picture) | 48 |
| #28297 | documents | Friends of Hart Park: original bylaws and initial directors, 1981 | fh8101 | 0 | 1 (1 pdf) | 1 |
| #28310 | documents | Cityhood Backers: Who Are They? | gt8702 | 0 | 12 (12 picture) | 48 |
| #31326 | documents | Community Comes Together for Vigil | sc1903 | 0 | 74 (74 picture) | 74 |
| #31723 | documents | A Newspaper Editor's Voyage Across San Francisco Bay: San Francisco Ch | uc8901 | 1 | 660 (2 pdf, 658 picture) | 660 |
