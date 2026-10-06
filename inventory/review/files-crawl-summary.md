# /scvhistory/files/ crawl: summary

Generated 2026-10-03T15:53:52-07:00 from a live read-only crawl of scvhistory.com (~1 request/s, User-Agent `SCVHistoryArchiveMigration/1.0 (read-only archive-migration crawl for the site owner; ~1 req/s)`). Manifest: `/workspace/review/files-crawl-manifest.json`. Files: `/workspace/files-crawl/<record code>/` (paths inside each folder match the paths on the site).

## Summary

All 88 records were crawled. That is exactly the 88 the live check placed under "images only under /scvhistory/files/": 42 WOWSlider galleries, 43 Flip PDF flipbooks and 3 Zoomify viewers. They span 89 folders, because lw3392 also uses files/lw3392b/. Every folder had an Apache directory listing, so the listings were used throughout. Recursion stayed inside each record's folder.

The crawl downloaded 3,381 files: 19,795,059,359 bytes (19.80 GB, 18.4 GiB on disk). That is 2,929 JPGs and 452 TIFs, and all of them have width/height. After the crawl, every file was re-checked against its recorded size and SHA-256 with no mismatches, and there are no stray partial or untracked files. Smaller files (10 MiB or less) went first across all records, then the larger ones. The total ended just under the 20 GB stop, so nothing in scope was left unfetched. By role: 1,620 files from folder roots (the full-size downloads, 18.34 GB), 267 from subfolders (mainly lw3392 large/), 714 WOWSlider display copies (data1/images) and 780 flipbook page renders (files/mobile).

85 files over 50 MiB were not downloaded. They are listed with their HEAD Content-Length, 52.81 GB in total, and all are _orig.tif or page TIFs. They come from 13 records: lw7601 21, lw3558 12, lw3664 8, lw3505 8, lw3368 7, lw3690 6, lw3332 6, lw3517 5, lw3453 4, lw3604 3, lw3607 2, lw3567 2 and lw3299 1. The largest is lw3558g_orig.tif at 1.82 GB.

Some files were listed but deliberately not downloaded:
- 1,537 viewer thumbnails (WOWSlider tooltips, flipbook thumb/ and shot.png).
- 45 PDFs, which are non-image files.
- 4 Zoomify tile sets: lw3633 (1,881 tiles), lw3315 (23,847 tiles) and lw3575a/b (344 each). These are summarised once per set in the manifest.

About 15,000 viewer-engine, code and icon files were counted but not listed.

No folder was 404 or empty. 86 records have status ok. 2 are partial: lw3633 and lw3315. Their folders hold only Zoomify tiles. Each record's full-size original sits under /gif/ instead (lw3633_orig.tif at 275 MB, lw3315_orig.jpg at 428 MB), which is outside the folder and over 50 MiB anyway. The two known broken links were checked once each on 2026-10-03 and both still return 404:
- https://scvhistory.com/gif/lw3733_orig.tif (lw3733 is not one of the 88)
- https://scvhistory.com/scvhistory/files/lw3575/lw3575_files/ (the "Download individual files" link on lw3575). The parent folder files/lw3575/ is fine; both 42 MiB TIFs and the tiles are there.

Folders that may contain images unrelated to the record, or many more than expected (judged from filenames and listings; the images were not viewed):
- lw3257 (Chumash basket): data1/images holds 14 extra slides named lw3267a–n.jpg, dated 2018-04-27 rather than the folder's 2018-04-22. They appear to belong to record lw3267, which has its own files/lw3267/ folder.
- lw3457 (Kingsburry House roof): 108 distinct images (camera originals IMG_5569… and others) against 6 slides.
- lw3487 (Charley Mack House listing): 106 distinct images against 56 slides. Most have listing-site-style hash names (…-w1020_h770_q80.jpg, IS2….jpg), consistent with the "Property Listing 2019" title.
- One-off odd names to check: lw3794 HCF9z9n.JPG, lw3534 dirtbike_001.jpg, lw3801 slide paulette20180209trunk_800.jpg (the rest of that set is dated 2020-02-29), lw3365 1_Instruction_Front.jpg and 6_SWING.jpg.
- Flipbooks where the downloadable scans don't match the page count: lw3197 has 47 page renders but only 2 JPGs plus the PDF, so the renders are the only page images; lw3365 has 5 renders against 2 JPGs; lw3743 has 212 renders against 219 scans.
- Key/code: lw2248a (code) uses folder files/lw2248/ and is saved under /workspace/files-crawl/lw2248a/.

Files are stored under /workspace/files-crawl/<record code>/ with each file's path inside the folder kept as on the site. This matters because a root file and its data1/images copy often have the same name but different content. The lw3392b folder is stored under lw3392/lw3392b/. Crawl state and logs are in /workspace/files-crawl/_state/ (checkpoint.json, discovery.json, crawl3.log). Only read-only requests went to scvhistory.com, at about 1 request per second.

## Per-record table

| code | title | viewer | folder | status | downloaded | MB downloaded | >50 MiB listed only (GB) | thumbs / non-image listed | failed | notes |
|---|---|---|---|---|---|---|---|---|---|---|
| [lw7601](https://scvhistory.com/scvhistory/lw7601.htm) | Photo Gallery: Magic Mountain Amusement Park Visit, 1976. | WOWSlider gallery | files/lw7601/ | ok | 42 | 22.9 | 21 (15.40) | 21 / 0 | 0 | 21 file(s) over 50 MiB listed only |
| [lw6902](https://scvhistory.com/scvhistory/lw6902.htm) | 1969 Newhall-Saugus-Valencia-Canyon Country Telephone Direct | Flip PDF flipbook | files/lw6902/ | ok | 216 | 4569.9 | 0 (0.00) | 109 / 2 | 0 | filenames not matching record code (check relevance): Page_040a.tif, Page_045b.tif, Page_049b.tif |
| [lw3801](https://scvhistory.com/scvhistory/lw3801.htm) | Blum Ranch: George Blum Sr.'s Swiss Travel Trunk. | WOWSlider gallery | files/lw3801/ | ok | 24 | 62.5 | 0 (0.00) | 8 / 0 | 0 |  |
| [lw3800](https://scvhistory.com/scvhistory/lw3800.htm) | Blum Ranch: Quarter-Circle G Branding Iron. | WOWSlider gallery | files/lw3800/ | ok | 12 | 25.4 | 0 (0.00) | 4 / 0 | 0 | filenames not matching record code (check relevance): lw20200229brandingiron01.jpg, lw20200229brandingiron01_large.jpg, lw20200229brandingiron02.jpg, lw20200229brandingiron02_large.jpg, lw20200229brandingiron03.jpg, lw20200229brandingiron03_large.jpg, lw20200229brandingiron04.jpg, lw20200229brandingiron04_large.jpg |
| [lw3794](https://scvhistory.com/scvhistory/lw3794.htm) | Original Living Room Furniture, Harry Carey Adobe. | WOWSlider gallery | files/lw3794/ | ok | 19 | 2.9 | 0 (0.00) | 9 / 0 | 0 | 1 download-folder image(s) not in the slideshow: hcf9z9n; filenames not matching record code (check relevance): HCF9z9n.JPG |
| [lw3788](https://scvhistory.com/scvhistory/lw3788.htm) | 5-Gal Service Station Fuel/Oil Can, 1920s-(1950s?). | WOWSlider gallery | files/lw3788/ | ok | 18 | 40.3 | 0 (0.00) | 9 / 0 | 0 |  |
| [lw3781](https://scvhistory.com/scvhistory/lw3781.htm) | Railroad Hand Cart. | WOWSlider gallery | files/lw3781/ | ok | 6 | 30.6 | 0 (0.00) | 3 / 0 | 0 |  |
| [lw3779](https://scvhistory.com/scvhistory/lw3779.htm) | Switch Tie Plate from Saugus. | WOWSlider gallery | files/lw3779/ | ok | 10 | 42.9 | 0 (0.00) | 5 / 0 | 0 |  |
| [lw3762](https://scvhistory.com/scvhistory/lw3762.htm) | New Cab Cover for Mogul Locomotive, 2020. | WOWSlider gallery | files/lw3762/ | ok | 10 | 25.9 | 0 (0.00) | 5 / 0 | 0 |  |
| [lw3755](https://scvhistory.com/scvhistory/lw3755.htm) | Fort Tejon State Historical Monument Brochure, n.d. (pre-196 | Flip PDF flipbook | files/lw3755/ | ok | 7 | 104.6 | 0 (0.00) | 5 / 1 | 0 |  |
| [lw3743](https://scvhistory.com/scvhistory/lw3743.htm) | Seasoned with Grace: A Collection of Recipes by Crown Valley | Flip PDF flipbook | files/lw3743/ | ok | 432 | 3439.5 | 0 (0.00) | 213 / 1 | 0 | flipbook has 212 page renders vs 219 page scans in the folder |
| [lw3690](https://scvhistory.com/scvhistory/lw3690.htm) | Photo Gallery: Vasquez Rocks Vacation Photos, 1969. | WOWSlider gallery | files/lw3690/ | ok | 19 | 33.4 | 6 (1.95) | 6 / 0 | 0 | 6 file(s) over 50 MiB listed only; 1 download-folder image(s) not in the slideshow: lw3690proofsheet |
| [lw3686](https://scvhistory.com/scvhistory/lw3686.htm) | Curry's Lebec Lodge: Room Key and Fob with 3c Prepaid Postag | WOWSlider gallery | files/lw3686/ | ok | 30 | 58.1 | 0 (0.00) | 10 / 0 | 0 |  |
| [lw3664](https://scvhistory.com/scvhistory/lw3664.htm) | Juventino del Valle, Black Walnut Tree, (5) Important Camulo | WOWSlider gallery | files/lw3664/ | ok | 24 | 32.2 | 8 (4.19) | 8 / 0 | 0 | 8 file(s) over 50 MiB listed only |
| [lw3663](https://scvhistory.com/scvhistory/lw3663.htm) | Dell Comic No. 791: ''The 77th Bengal Lancers'' (No. 1/Only) | Flip PDF flipbook | files/lw3663/ | ok | 72 | 1269.5 | 0 (0.00) | 37 / 1 | 0 |  |
| [lw3647](https://scvhistory.com/scvhistory/lw3647.htm) | Guide to Hi Jolly Pioneer Cemetery, Quartzsite, Ariz., n.d.  | Flip PDF flipbook | files/lw3647/ | ok | 24 | 102.6 | 0 (0.00) | 13 / 1 | 0 |  |
| [lw3633](https://scvhistory.com/scvhistory/lw3633.htm) | First Wall Map: Magic Mountain Amusement Park, 1971. | Zoomify deep-zoom | files/lw3633/ | partial | 0 | 0.0 | 0 (0.00) | 0 / 0 | 0 | folder holds only Zoomify tiles (not downloaded); full-size original is linked from the page under /gif/ and is >50 MiB |
| [lw3609](https://scvhistory.com/scvhistory/lw3609.htm) | Signal (Marker) Tree Loses Large Limb, 8/2019. | WOWSlider gallery | files/lw3609/ | ok | 40 | 120.2 | 0 (0.00) | 13 / 0 | 0 | 1 download-folder image(s) not in the slideshow: lw3609z |
| [lw3607](https://scvhistory.com/scvhistory/lw3607.htm) | Fold-out Brochure: Indian Dunes Family Motor Recreation Park | Flip PDF flipbook | files/lw3607/ | ok | 2 | 1.3 | 2 (0.13) | 3 / 1 | 0 | 2 file(s) over 50 MiB listed only |
| [lw3604](https://scvhistory.com/scvhistory/lw3604.htm) | Call Sheet: ''Wild Wild West'' (Warner Bros. 1999), 4-21-199 | Flip PDF flipbook | files/lw3604/ | ok | 3 | 1.8 | 3 (0.17) | 4 / 1 | 0 | 3 file(s) over 50 MiB listed only |
| [lw3578](https://scvhistory.com/scvhistory/lw3578.htm) | Movie Herald: ''Die Flamme von Arabien'' (''Flame of Araby'' | Flip PDF flipbook | files/lw3578/ | ok | 8 | 146.9 | 0 (0.00) | 5 / 1 | 0 |  |
| [lw3576](https://scvhistory.com/scvhistory/lw3576.htm) | Indian Dunes' First Competitive Event (Story November 1970). | Flip PDF flipbook | files/lw3576/ | ok | 10 | 218.6 | 0 (0.00) | 6 / 1 | 0 |  |
| [lw3575](https://scvhistory.com/scvhistory/lw3575.htm) | Brochure: Dragoon Walk (Self-Guided Trail), Fort Tejon State | Zoomify deep-zoom | files/lw3575/ | ok | 2 | 88.7 | 0 (0.00) | 0 / 0 | 0 | Zoomify tile set(s) not downloaded |
| [lw3567](https://scvhistory.com/scvhistory/lw3567.htm) | Fold-out Brochure: Indian Dunes Family Motor Recreation Park | Flip PDF flipbook | files/lw3567/ | ok | 2 | 1.8 | 2 (0.13) | 3 / 1 | 0 | 2 file(s) over 50 MiB listed only |
| [lw3558](https://scvhistory.com/scvhistory/lw3558.htm) | Photo Gallery: William S. Hart Jr. as Young Adult in Cowboy  | WOWSlider gallery | files/lw3558/ | ok | 36 | 52.2 | 12 (17.75) | 12 / 0 | 0 | 12 file(s) over 50 MiB listed only |
| [lw3536](https://scvhistory.com/scvhistory/lw3536.htm) | Southern Pacific and T&NO Class M-4 2-6-0s (Moguls). | Flip PDF flipbook | files/lw3536/ | ok | 14 | 291.6 | 0 (0.00) | 8 / 1 | 0 |  |
| [lw3534](https://scvhistory.com/scvhistory/lw3534.htm) | Course of the Month: Indian Dunes (Dirt Bike, June 1973). | Flip PDF flipbook | files/lw3534/ | ok | 10 | 184.7 | 0 (0.00) | 6 / 1 | 0 | filenames not matching record code (check relevance): dirtbike_001.jpg |
| [lw3522](https://scvhistory.com/scvhistory/lw3522.htm) | Photo Gallery: Perkins Building (''Red Signal Buildings'') U | WOWSlider gallery | files/lw3522/ | ok | 18 | 22.5 | 0 (0.00) | 6 / 0 | 0 |  |
| [lw3521](https://scvhistory.com/scvhistory/lw3521.htm) | James Garner as Maverick: ''Relic of Fort Tejon,'' Dell Comi | Flip PDF flipbook | files/lw3521/ | ok | 72 | 1271.2 | 0 (0.00) | 37 / 1 | 0 | filenames not matching record code (check relevance): page025b.tif |
| [lw3518](https://scvhistory.com/scvhistory/lw3518.htm) | The Pacific Mineralogist, December 1941 (Complete): Includes | Flip PDF flipbook | files/lw3518/ | ok | 64 | 824.1 | 0 (0.00) | 33 / 1 | 0 |  |
| [lw3517](https://scvhistory.com/scvhistory/lw3517.htm) | Bermite's Patrick Lizza: He Sets the Sky On Fire / Saturday  | Flip PDF flipbook | files/lw3517/ | ok | 5 | 7.1 | 5 (0.34) | 6 / 1 | 0 | 5 file(s) over 50 MiB listed only |
| [lw3516](https://scvhistory.com/scvhistory/lw3516.htm) | 47th Annual SCV Boys & Girls Club Auction Catalog, 2018. | Flip PDF flipbook | files/lw3516/ | ok | 128 | 2759.5 | 0 (0.00) | 65 / 1 | 0 |  |
| [lw3505](https://scvhistory.com/scvhistory/lw3505.htm) | Story of Edwin Carewe's Version of ''Ramona'' in Italian / C | Flip PDF flipbook | files/lw3505/ | ok | 8 | 9.4 | 8 (0.44) | 9 / 1 | 0 | 8 file(s) over 50 MiB listed only |
| [lw3487](https://scvhistory.com/scvhistory/lw3487.htm) | Charley Mack House, 22931 8th St., Property Listing 2019. | WOWSlider gallery | files/lw3487/ | ok | 162 | 40.2 | 0 (0.00) | 56 / 0 | 0 | MANY MORE: download folder has 106 distinct images vs 56 slides in the gallery |
| [lw3481](https://scvhistory.com/scvhistory/lw3481.htm) | Squadron of Giant Tillers Speed California's Castaic Dam, To | Flip PDF flipbook | files/lw3481/ | ok | 4 | 87.1 | 0 (0.00) | 3 / 1 | 0 |  |
| [lw3457](https://scvhistory.com/scvhistory/lw3457.htm) | Kingsburry House: Temporary Roof Repairs, 11-27-2018. | WOWSlider gallery | files/lw3457/ | ok | 119 | 656.4 | 0 (0.00) | 6 / 0 | 0 | MANY MORE: download folder has 108 distinct images vs 6 slides in the gallery |
| [lw3453](https://scvhistory.com/scvhistory/lw3453.htm) | Lebec (Hotel?) Restaurant and Coffee Shop Menu, n.d. (~1960s | Flip PDF flipbook | files/lw3453/ | ok | 12 | 106.8 | 4 (7.07) | 5 / 1 | 0 | 4 file(s) over 50 MiB listed only; filenames not matching record code (check relevance): page001_large.jpg, page002_large.jpg, page003_large.jpg, page004_large.jpg |
| [lw3451](https://scvhistory.com/scvhistory/lw3451.htm) | Beale's U.S. Camel Corps: Hi Jolly's Tomb & Cemetery, Quartz | WOWSlider gallery | files/lw3451/ | ok | 41 | 78.1 | 0 (0.00) | 13 / 0 | 0 | 1 download-folder image(s) not in the slideshow: lw3451l |
| [lw3404](https://scvhistory.com/scvhistory/lw3404.htm) | J.C. Agajanian: Saugus Hog Rancher, Race Promoter / Biograph | Flip PDF flipbook | files/lw3404/ | ok | 14 | 49.1 | 0 (0.00) | 8 / 1 | 0 |  |
| [lw3402](https://scvhistory.com/scvhistory/lw3402.htm) | Charlie Chaplin at First Presbyterian Church in 'The Pilgrim | WOWSlider gallery | files/lw3402/ | ok | 26 | 4.4 | 0 (0.00) | 13 / 0 | 0 |  |
| [lw3392](https://scvhistory.com/scvhistory/lw3392.htm) | Rock Arch at Needham Ranch Moved, Again / 8-30-2018. | WOWSlider gallery | files/lw3392/, files/lw3392b/ | ok | 314 | 542.8 | 0 (0.00) | 105 / 0 | 0 |  |
| [lw3390](https://scvhistory.com/scvhistory/lw3390.htm) | Backgrounder: Express Companies & Staging in California / Tr | Flip PDF flipbook | files/lw3390/ | ok | 12 | 43.2 | 0 (0.00) | 7 / 1 | 0 |  |
| [lw3386](https://scvhistory.com/scvhistory/lw3386.htm) | Weireter, Christopher Honored by Camulos Museum, 8-26-2018. | WOWSlider gallery | files/lw3386/ | ok | 39 | 52.2 | 0 (0.00) | 13 / 0 | 0 |  |
| [lw3368](https://scvhistory.com/scvhistory/lw3368.htm) | Photo Gallery: Piru Oil Lease with Likely Owner and Visitors | WOWSlider gallery | files/lw3368/ | ok | 14 | 5.7 | 7 (4.05) | 7 / 0 | 0 | 7 file(s) over 50 MiB listed only |
| [lw3365](https://scvhistory.com/scvhistory/lw3365.htm) | Borax 20 Mule Team Scale Model Assembly Instructions. | Flip PDF flipbook | files/lw3365/ | ok | 7 | 10.3 | 0 (0.00) | 6 / 2 | 0 | flipbook has 5 page renders vs 2 page scans in the folder; filenames not matching record code (check relevance): 1_Instruction_Front.jpg, 6_SWING.jpg |
| [lw3359](https://scvhistory.com/scvhistory/lw3359.htm) | Battle of San Pasqual, 1846: Pico Routs Kearny; Beale's Stea | WOWSlider gallery | files/lw3359/ | ok | 174 | 307.0 | 0 (0.00) | 58 / 0 | 0 |  |
| [lw3332](https://scvhistory.com/scvhistory/lw3332.htm) | Edwin Carewe's 'Ramona' (1928): 6 Spanish Pressbook Illustra | WOWSlider gallery | files/lw3332/ | ok | 18 | 49.1 | 6 (1.13) | 6 / 0 | 0 | 6 file(s) over 50 MiB listed only |
| [lw3316](https://scvhistory.com/scvhistory/lw3316.htm) | Railfanning on Southern Pacific's Saugus Line, November 1991 | Flip PDF flipbook | files/lw3316/ | ok | 20 | 64.4 | 0 (0.00) | 11 / 1 | 0 |  |
| [lw3315](https://scvhistory.com/scvhistory/lw3315.htm) | Class Photo: Class of 1969. | Zoomify deep-zoom | files/lw3315/ | partial | 0 | 0.0 | 0 (0.00) | 0 / 0 | 0 | folder holds only Zoomify tiles (not downloaded); full-size original is linked from the page under /gif/ and is >50 MiB |
| [lw3312](https://scvhistory.com/scvhistory/lw3312.htm) | Stock Car Racing Magazine, July 1981. | Flip PDF flipbook | files/lw3312/ | ok | 8 | 47.9 | 0 (0.00) | 5 / 1 | 0 |  |
| [lw3308](https://scvhistory.com/scvhistory/lw3308.htm) | OSI Report: Air Force Pilots Sight UFO Near Lebec, 9-5-1949. | Flip PDF flipbook | files/lw3308/ | ok | 38 | 48.2 | 0 (0.00) | 20 / 1 | 0 |  |
| [lw3299](https://scvhistory.com/scvhistory/lw3299.htm) | Photo Gallery: Tennessee Ernie Ford Attends BBQ at Cliffie S | WOWSlider gallery | files/lw3299/ | ok | 14 | 162.1 | 1 (0.06) | 5 / 0 | 0 | 1 file(s) over 50 MiB listed only |
| [lw3265](https://scvhistory.com/scvhistory/lw3265.htm) | The Winged Monster of Elizabeth Lake (Old West, Fall 1969). | Flip PDF flipbook | files/lw3265/ | ok | 10 | 72.5 | 0 (0.00) | 6 / 1 | 0 |  |
| [lw3257](https://scvhistory.com/scvhistory/lw3257.htm) | Photo Gallery: Largest Ancient Chumash Basket Ever Found (SB | WOWSlider gallery | files/lw3257/ | ok | 68 | 79.5 | 0 (0.00) | 32 / 0 | 0 | STRAY: 14 image(s) named for other record code(s) lw3267a, lw3267b, lw3267c, lw3267d, lw3267e, lw3267f, lw3267g, lw3267h, lw3267i, lw3267j, lw3267k, lw3267l, lw3267m, lw3267n (e.g. data1/images/lw3267a.jpg) |
| [lw3254](https://scvhistory.com/scvhistory/lw3254.htm) | Story: Southern Pacific's Historic Soledad Canyon Link, 1984 | Flip PDF flipbook | files/lw3254/ | ok | 16 | 97.5 | 0 (0.00) | 9 / 1 | 0 |  |
| [lw3222](https://scvhistory.com/scvhistory/lw3222.htm) | 'Fireball 500' Lobby Cards (Full Set of 8). | WOWSlider gallery | files/lw3222/ | ok | 24 | 53.1 | 0 (0.00) | 8 / 0 | 0 |  |
| [lw3219](https://scvhistory.com/scvhistory/lw3219.htm) | Thousand Trails Soledad Canyon RV & Camping Resort, Slide Sh | WOWSlider gallery | files/lw3219/ | ok | 50 | 18.3 | 0 (0.00) | 25 / 0 | 0 |  |
| [lw3197](https://scvhistory.com/scvhistory/lw3197.htm) | Application for the Incorporation of the City of 'Santa Clar | Flip PDF flipbook | files/lw3197/ | ok | 49 | 35.9 | 0 (0.00) | 48 / 1 | 0 | flipbook has 47 page renders vs 2 page scans in the folder |
| [lw3187](https://scvhistory.com/scvhistory/lw3187.htm) | Park and Museum Brochure, 1960s. | Flip PDF flipbook | files/lw3187/ | ok | 33 | 81.2 | 0 (0.00) | 17 / 1 | 0 |  |
| [lw3149](https://scvhistory.com/scvhistory/lw3149.htm) | 1st City Council Members' New Year's Resolutions, SCV Magazi | Flip PDF flipbook | files/lw3149/ | ok | 7 | 21.1 | 0 (0.00) | 4 / 1 | 0 |  |
| [lw3134](https://scvhistory.com/scvhistory/lw3134.htm) | Official Program: Bonelli Stadium Automobile Races, 10-7-194 | Flip PDF flipbook | files/lw3134/ | ok | 8 | 28.7 | 0 (0.00) | 5 / 1 | 0 |  |
| [lw3122](https://scvhistory.com/scvhistory/lw3122.htm) | Bonelli Stadium Racing Program 5-26-1946; Driver Profile: Bi | Flip PDF flipbook | files/lw3122/ | ok | 12 | 48.0 | 0 (0.00) | 7 / 1 | 0 |  |
| [lw3118](https://scvhistory.com/scvhistory/lw3118.htm) | Newhall Elementary School Auditorium as Warehouse, Interiors | WOWSlider gallery | files/lw3118/ | ok | 12 | 23.9 | 0 (0.00) | 6 / 0 | 0 |  |
| [lw3117](https://scvhistory.com/scvhistory/lw3117.htm) | City's 10th Birthday Ice Cream Party, 1997. | WOWSlider gallery | files/lw3117/ | ok | 18 | 36.5 | 0 (0.00) | 9 / 0 | 0 |  |
| [lw3104](https://scvhistory.com/scvhistory/lw3104.htm) | The Move to Secede from Los Angeles County, by Bob Simmons,  | Flip PDF flipbook | files/lw3104/ | ok | 8 | 13.4 | 0 (0.00) | 5 / 1 | 0 |  |
| [lw3103](https://scvhistory.com/scvhistory/lw3103.htm) | Program Book: 34th Annual Newhall-Saugus Rodeo, 1960. | Flip PDF flipbook | files/lw3103/ | ok | 20 | 52.4 | 0 (0.00) | 11 / 1 | 0 |  |
| [lw3097](https://scvhistory.com/scvhistory/lw3097.htm) | Fort Oghora Built for NBC's '77th Bengal Lancers' / TV Guide | Flip PDF flipbook | files/lw3097/ | ok | 6 | 8.8 | 0 (0.00) | 4 / 1 | 0 |  |
| [lw3076](https://scvhistory.com/scvhistory/lw3076.htm) | Corporate Seal Embosser, Sterling Borax Co. (Unique), 1908. | WOWSlider gallery | files/lw3076/ | ok | 26 | 59.3 | 0 (0.00) | 13 / 0 | 0 |  |
| [lw3061](https://scvhistory.com/scvhistory/lw3061.htm) | Program Book: Dedication of Permanent COC Campus, 10-26-1970 | Flip PDF flipbook | files/lw3061/ | ok | 8 | 5.9 | 0 (0.00) | 5 / 1 | 0 |  |
| [lw3060](https://scvhistory.com/scvhistory/lw3060.htm) | City Formation Committee Kicks Off Voter Registration Progra | Flip PDF flipbook | files/lw3060/ | ok | 4 | 4.0 | 0 (0.00) | 3 / 1 | 0 |  |
| [lw3059](https://scvhistory.com/scvhistory/lw3059.htm) | Arthur Young CPAs Predict 22% Budget Windfall for Proposed C | Flip PDF flipbook | files/lw3059/ | ok | 4 | 4.1 | 0 (0.00) | 3 / 1 | 0 |  |
| [lw3048](https://scvhistory.com/scvhistory/lw3048.htm) | Exterior Views & Barnyard, 1968. | WOWSlider gallery | files/lw3048/ | ok | 10 | 23.2 | 0 (0.00) | 5 / 0 | 0 |  |
| [lw3047](https://scvhistory.com/scvhistory/lw3047.htm) | Hart Mansion Interiors, 1968. | WOWSlider gallery | files/lw3047/ | ok | 10 | 38.9 | 0 (0.00) | 5 / 0 | 0 |  |
| [lw3046](https://scvhistory.com/scvhistory/lw3046.htm) | Vasquez Rocks County Park, 1968. | WOWSlider gallery | files/lw3046/ | ok | 12 | 17.9 | 0 (0.00) | 6 / 0 | 0 |  |
| [lw3037](https://scvhistory.com/scvhistory/lw3037.htm) | Montie Montana Rodeo Ranch Photo Collection, 1970s-1980s. | WOWSlider gallery | files/lw3037/ | ok | 22 | 41.9 | 0 (0.00) | 11 / 0 | 0 |  |
| [lw3029](https://scvhistory.com/scvhistory/lw3029.htm) | Brochure: (History of) The Piru Mansion, 1988. | Flip PDF flipbook | files/lw3029/ | ok | 8 | 7.6 | 0 (0.00) | 5 / 1 | 0 |  |
| [lw3028](https://scvhistory.com/scvhistory/lw3028.htm) | Newhall Ranch Marketing Brochure, 1990s. | Flip PDF flipbook | files/lw3028/ | ok | 24 | 69.4 | 0 (0.00) | 13 / 1 | 0 |  |
| [lw3017](https://scvhistory.com/scvhistory/lw3017.htm) | Wooden Plow, Spanish Mission Period, Early 1800s. | WOWSlider gallery | files/lw3017/ | ok | 15 | 31.4 | 0 (0.00) | 7 / 0 | 0 | 1 download-folder image(s) not in the slideshow: lw3017 |
| [lw3007](https://scvhistory.com/scvhistory/lw3007.htm) | SCV School District Reorganization Plan (Failed), 1970-71. | Flip PDF flipbook | files/lw3007/ | ok | 10 | 13.8 | 0 (0.00) | 6 / 1 | 0 |  |
| [lw2993](https://scvhistory.com/scvhistory/lw2993.htm) | Tepee Rock Shop (Soledad Cyn.): Rockhounding Guide for L.A.  | Flip PDF flipbook | files/lw2993/ | ok | 72 | 93.6 | 0 (0.00) | 37 / 1 | 0 |  |
| [lw2992](https://scvhistory.com/scvhistory/lw2992.htm) | The Bogus Story of Tom Vernon and the Sweetwater Incident, G | Flip PDF flipbook | files/lw2992/ | ok | 14 | 39.0 | 0 (0.00) | 8 / 1 | 0 |  |
| [lw2980](https://scvhistory.com/scvhistory/lw2980.htm) | Slated for Demolition: 24326 Walnut Street Residence, April  | WOWSlider gallery | files/lw2980/ | ok | 92 | 148.8 | 0 (0.00) | 47 / 0 | 0 |  |
| [lw2979](https://scvhistory.com/scvhistory/lw2979.htm) | 2015: John Bergstrom, Gency Brown and Buck Corbett Entertain | WOWSlider gallery | files/lw2979/ | ok | 10 | 7.8 | 0 (0.00) | 5 / 0 | 0 | filenames not matching record code (check relevance): outwest01.jpg, outwest02.jpg, outwest03.jpg, outwest04.jpg, outwest05.jpg |
| [lw2978](https://scvhistory.com/scvhistory/lw2978.htm) | 2015 Rotary Club Pancake Breakfast. | WOWSlider gallery | files/lw2978/ | ok | 28 | 18.9 | 0 (0.00) | 14 / 0 | 0 |  |
| [lw2977](https://scvhistory.com/scvhistory/lw2977.htm) | 2015 Parade, Inside-Out (Spectators & Organizers). | WOWSlider gallery | files/lw2977/ | ok | 178 | 178.3 | 0 (0.00) | 89 / 0 | 0 |  |
| [lw2248a](https://scvhistory.com/scvhistory/lw2248.htm) | Photo Gallery: 1876 Golden Spike. | WOWSlider gallery | files/lw2248/ | ok | 30 | 10.7 | 0 (0.00) | 15 / 0 | 0 |  |
| [lw2214](https://scvhistory.com/scvhistory/lw2214.htm) | Photo Gallery: Sandberg's Summit Hotel Site, 2006. | WOWSlider gallery | files/lw2214/ | ok | 12 | 5.7 | 0 (0.00) | 6 / 0 | 0 |  |
| [lw1501](https://scvhistory.com/scvhistory/lw1501.htm) | Photo Gallery: Grounds, Rose Garden, Carreta, 2015. | WOWSlider gallery | files/lw1501/ | ok | 28 | 84.2 | 0 (0.00) | 10 / 0 | 0 |  |
