# Reggie mirror vs. /files/ crawl

Generated 2026-10-04T00:21:27-07:00 on the box only, with no network access. Compared against Nathan's Reggie mirror: the 2026-10-04 file listing (sizes) and the 2026-08-20 sha256 manifest. Reggie paths are relative to `/Volumes/Reggie/SCVHistory/`. Detail is in `/workspace/review/reggie-comparison.json`.

## Summary

**Large files (87 = 85 over 50 MiB + 2 Zoomify /gif/ originals).**
- 41 are on Reggie at the same path with the same size as live.
- 7 are on Reggie with a different size. All are lw3558 a–g _orig.tif, and the Reggie copies look truncated: each is 65–78% of the live size, the sizes are exact multiples of 4 KiB, and the 7 files were written about 5 minutes apart on 2026-05-20, which fits a download timeout. So lw3558h–l are missing and a–g are incomplete; Reggie has no good copy of any of lw3558's 12 originals.
- 38 are missing: lw3332 6, lw3368 7, lw3453 4, lw3505 8, lw3558 5, lw3567 2, lw3604 3, lw3607 2, lw3633 1.
- lw3315_orig.jpg is in the Aug-20 manifest at `scvhistory.com/gif/lw3315_orig.jpg`, but its size can't be checked because the Oct-4 listing doesn't cover the site-root /gif/.
- None of the missing large files exists anywhere else on Reggie under the same name (/gif/, /orig/ or another /files/ folder). Generic page names like page001.tif do turn up in other publications' folders; those aren't alternates.

**The 29 files where the large file is the only high-res copy:**
- lw7601's 21 _orig.tif files are all on Reggie with matching size.
- lw3299d_orig.jpg is on Reggie with matching size.
- lw3368's 7 _orig.tif files (4.05 GB) are all missing from Reggie, so they are still not held anywhere.
- Of the two no-derivative Zoomify originals, lw3315_orig.jpg is on Reggie (manifest) and lw3633_orig.tif is missing. Reggie's /gif/ has only lw3633t.jpg, and Reggie has no files/lw3633/ or files/lw3315/ folder.

**Known broken links.**
- gif/lw3733_orig.tif is not on Reggie, and no lw3733_orig file exists anywhere; Reggie /gif/ has lw3733a/b, crops, _large and t.
- files/lw3575/lw3575_files/ is not on Reggie. In fact Reggie has no files/lw3575/ folder at all, so lw3575a_orig.tif and lw3575b_orig.tif (42 MiB each, downloaded by the crawl) are also missing.

**Whole crawl (3,381 downloaded files).**
- 3,250 are already on Reggie and identical, all confirmed by sha256 against the Aug-20 manifest. No size-only matches were needed.
- 0 are present but different.
- 131 are missing from Reggie, 2.32 GB (2,320,706,154 bytes), across 10 records.
  - Whole folders' images missing: lw3222 (24/24), lw3332 (18/18), lw3505 (8/8), lw3575 (2/2).
  - Partly missing: lw3516 38/128 (page TIFs), lw3686 20/30, lw3103 10/20, lw3576 5/10, lw3578 4/8, lw3481 2/4.
  - By type: 51 TIFs (2.14 GB: page scans plus lw3575's two _orig.tif), 48 download-folder JPGs (0.15 GB), 18 flipbook page renders and 14 slider display copies. This fits the 403 gaps you mentioned in the wget logs, but I haven't checked those logs.
- The other 78 records are fully on Reggie byte-for-byte.

**Copy plan.**
- `/workspace/reggie/copy-list.txt` lists the 131 missing files with their targets under `scvhistory.com/scvhistory/files/<site folder>/`.
- `/workspace/reggie/conflicts.txt` has 0 entries, because no file is present-but-different.
- None of the 131 targets clashes with an existing Reggie path, including case-insensitive matches and file-vs-directory clashes.
- The files are packed into 1 tar: `/workspace/reggie/tars/reggie-add-01.tar`, 2.32 GB. Its SHA256SUMS and member list are next to it, and every member was re-hashed against the crawl sha256.
- To add without overwriting, extract from `/Volumes/Reggie/SCVHistory` with `tar -xkf reggie-add-01.tar` (-k = keep existing files).

## Large files and broken links

| code | file | live bytes | Reggie | Reggie bytes | only high-res copy? | note |
|---|---|---|---|---|---|---|
| lw3299 | lw3299d_orig.jpg | 63,620,153 | present, size matches | 63,620,153 | YES |  |
| lw3315 | lw3315_orig.jpg | 427,879,753 | present in Aug-20 manifest (path not covered by Oct-4 listing; size not checkable) |  | YES |  |
| lw3332 | lw3332a_orig.jpg | 184,118,813 | missing |  |  |  |
| lw3332 | lw3332b_orig.jpg | 193,446,546 | missing |  |  |  |
| lw3332 | lw3332c_orig.jpg | 196,402,938 | missing |  |  |  |
| lw3332 | lw3332d_orig.jpg | 189,158,917 | missing |  |  |  |
| lw3332 | lw3332e_orig.jpg | 180,863,856 | missing |  |  |  |
| lw3332 | lw3332f_orig.jpg | 183,407,198 | missing |  |  |  |
| lw3368 | lw3368a_orig.tif | 465,885,712 | missing |  | YES |  |
| lw3368 | lw3368b_orig.tif | 496,740,632 | missing |  | YES |  |
| lw3368 | lw3368c_orig.tif | 475,896,128 | missing |  | YES |  |
| lw3368 | lw3368d_orig.tif | 460,697,388 | missing |  | YES |  |
| lw3368 | lw3368e_orig.tif | 481,114,996 | missing |  | YES |  |
| lw3368 | lw3368f_orig.tif | 585,460,448 | missing |  | YES |  |
| lw3368 | lw3368g_orig.tif | 1,082,963,404 | missing |  | YES |  |
| lw3453 | page001.tif | 1,759,934,096 | missing |  |  | 12 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3453 | page002.tif | 1,776,891,612 | missing |  |  | 12 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3453 | page003.tif | 1,774,653,644 | missing |  |  | 12 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3453 | page004.tif | 1,753,568,764 | missing |  |  | 12 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3505 | page001.tif | 55,320,472 | missing |  |  | 12 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3505 | page002.tif | 55,320,812 | missing |  |  | 12 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3505 | page003.tif | 55,320,988 | missing |  |  | 12 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3505 | page004.tif | 55,320,504 | missing |  |  | 12 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3505 | page005.tif | 55,319,624 | missing |  |  | 11 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3505 | page006.tif | 55,320,984 | missing |  |  | 10 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3505 | page007.tif | 55,321,460 | missing |  |  | 10 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3505 | page008.tif | 55,320,672 | missing |  |  | 10 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3517 | page001.tif | 69,655,260 | present, size matches | 69,655,260 |  | 11 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3517 | page002.tif | 69,118,660 | present, size matches | 69,118,660 |  | 11 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3517 | page003.tif | 67,890,396 | present, size matches | 67,890,396 |  | 11 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3517 | page004.tif | 68,806,948 | present, size matches | 68,806,948 |  | 11 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3517 | page005.tif | 68,235,856 | present, size matches | 68,235,856 |  | 10 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3558 | lw3558a_orig.tif | 1,627,158,428 | present, size differs | 1,270,775,808 |  | Reggie copy looks truncated: size is a multiple of 4 KiB, 65–78% of the live Content-Length; the 7 files were written ~5 min apart on 2026-05-20 (consistent with a download timeout) |
| lw3558 | lw3558b_orig.tif | 1,627,158,428 | present, size differs | 1,261,649,920 |  | Reggie copy looks truncated: size is a multiple of 4 KiB, 65–78% of the live Content-Length; the 7 files were written ~5 min apart on 2026-05-20 (consistent with a download timeout) |
| lw3558 | lw3558c_orig.tif | 1,722,621,404 | present, size differs | 1,321,910,272 |  | Reggie copy looks truncated: size is a multiple of 4 KiB, 65–78% of the live Content-Length; the 7 files were written ~5 min apart on 2026-05-20 (consistent with a download timeout) |
| lw3558 | lw3558d_orig.tif | 1,722,621,404 | present, size differs | 1,323,499,520 |  | Reggie copy looks truncated: size is a multiple of 4 KiB, 65–78% of the live Content-Length; the 7 files were written ~5 min apart on 2026-05-20 (consistent with a download timeout) |
| lw3558 | lw3558e_orig.tif | 1,627,158,428 | present, size differs | 1,221,328,896 |  | Reggie copy looks truncated: size is a multiple of 4 KiB, 65–78% of the live Content-Length; the 7 files were written ~5 min apart on 2026-05-20 (consistent with a download timeout) |
| lw3558 | lw3558f_orig.tif | 1,722,621,404 | present, size differs | 1,332,543,488 |  | Reggie copy looks truncated: size is a multiple of 4 KiB, 65–78% of the live Content-Length; the 7 files were written ~5 min apart on 2026-05-20 (consistent with a download timeout) |
| lw3558 | lw3558g_orig.tif | 1,823,810,724 | present, size differs | 1,192,148,992 |  | Reggie copy looks truncated: size is a multiple of 4 KiB, 65–78% of the live Content-Length; the 7 files were written ~5 min apart on 2026-05-20 (consistent with a download timeout) |
| lw3558 | lw3558h_orig.tif | 1,823,810,724 | missing |  |  |  |
| lw3558 | lw3558i_orig.tif | 1,805,972,428 | missing |  |  |  |
| lw3558 | lw3558j_orig.tif | 1,722,621,404 | missing |  |  |  |
| lw3558 | lw3558k_orig.tif | 230,250,732 | missing |  |  |  |
| lw3558 | lw3558l_orig.tif | 296,550,432 | missing |  |  |  |
| lw3567 | page_001.tif | 65,645,208 | missing |  |  | 25 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3567 | page_002.tif | 65,416,768 | missing |  |  | 26 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3604 | page_001.tif | 55,078,764 | missing |  |  | 25 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3604 | page_002.tif | 55,077,976 | missing |  |  | 26 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3604 | page_003.tif | 55,079,244 | missing |  |  | 24 other Reggie files share this generic page filename (other publications); not treated as alternates |
| lw3607 | lw3607_01.tif | 66,160,284 | missing |  |  |  |
| lw3607 | lw3607_02.tif | 66,177,700 | missing |  |  |  |
| lw3633 | lw3633_orig.tif | 275,522,652 | missing |  | YES |  |
| lw3664 | lw3664a_orig.tif | 666,014,836 | present, size matches | 666,014,836 |  |  |
| lw3664 | lw3664ab_orig.tif | 93,975,468 | present, size matches | 93,975,468 |  |  |
| lw3664 | lw3664b_orig.tif | 683,312,508 | present, size matches | 683,312,508 |  |  |
| lw3664 | lw3664bb_orig.tif | 146,296,748 | present, size matches | 146,296,748 |  |  |
| lw3664 | lw3664c_orig.tif | 672,870,392 | present, size matches | 672,870,392 |  |  |
| lw3664 | lw3664d_orig.tif | 665,953,308 | present, size matches | 665,953,308 |  |  |
| lw3664 | lw3664e_orig.tif | 739,696,904 | present, size matches | 739,696,904 |  |  |
| lw3664 | lw3664eb_orig.tif | 520,612,612 | present, size matches | 520,612,612 |  |  |
| lw3690 | lw3690a_orig.tif | 319,772,256 | present, size matches | 319,772,256 |  |  |
| lw3690 | lw3690b_orig.tif | 320,708,448 | present, size matches | 320,708,448 |  |  |
| lw3690 | lw3690c_orig.tif | 321,488,640 | present, size matches | 321,488,640 |  |  |
| lw3690 | lw3690d_orig.tif | 319,755,228 | present, size matches | 319,755,228 |  |  |
| lw3690 | lw3690e_orig.tif | 329,838,208 | present, size matches | 329,838,208 |  |  |
| lw3690 | lw3690f_orig.tif | 338,698,944 | present, size matches | 338,698,944 |  |  |
| lw7601 | lw7601a_orig.tif | 215,509,552 | present, size matches | 215,509,552 | YES |  |
| lw7601 | lw7601b_orig.tif | 852,819,504 | present, size matches | 852,819,504 | YES |  |
| lw7601 | lw7601c_orig.tif | 215,448,732 | present, size matches | 215,448,732 | YES |  |
| lw7601 | lw7601d_orig.tif | 855,772,432 | present, size matches | 855,772,432 | YES |  |
| lw7601 | lw7601e_orig.tif | 863,951,616 | present, size matches | 863,951,616 | YES |  |
| lw7601 | lw7601f_orig.tif | 853,791,764 | present, size matches | 853,791,764 | YES |  |
| lw7601 | lw7601g_orig.tif | 858,879,984 | present, size matches | 858,879,984 | YES |  |
| lw7601 | lw7601h_orig.tif | 841,668,332 | present, size matches | 841,668,332 | YES |  |
| lw7601 | lw7601i_orig.tif | 866,032,984 | present, size matches | 866,032,984 | YES |  |
| lw7601 | lw7601j_orig.tif | 853,822,608 | present, size matches | 853,822,608 | YES |  |
| lw7601 | lw7601k_orig.tif | 852,724,276 | present, size matches | 852,724,276 | YES |  |
| lw7601 | lw7601l_orig.tif | 216,427,212 | present, size matches | 216,427,212 | YES |  |
| lw7601 | lw7601m_orig.tif | 857,917,432 | present, size matches | 857,917,432 | YES |  |
| lw7601 | lw7601n_orig.tif | 854,721,732 | present, size matches | 854,721,732 | YES |  |
| lw7601 | lw7601o_orig.tif | 852,748,600 | present, size matches | 852,748,600 | YES |  |
| lw7601 | lw7601p_orig.tif | 854,772,920 | present, size matches | 854,772,920 | YES |  |
| lw7601 | lw7601q_orig.tif | 855,798,820 | present, size matches | 855,798,820 | YES |  |
| lw7601 | lw7601r_orig.tif | 216,414,472 | present, size matches | 216,414,472 | YES |  |
| lw7601 | lw7601s_orig.tif | 859,805,776 | present, size matches | 859,805,776 | YES |  |
| lw7601 | lw7601t_orig.tif | 855,718,304 | present, size matches | 855,718,304 | YES |  |
| lw7601 | lw7601u_orig.tif | 844,577,416 | present, size matches | 844,577,416 | YES |  |
| broken link | gif/lw3733_orig.tif | live 404 (2026-10-03) | missing | | | |
| broken link | scvhistory/files/lw3575/lw3575_files/ | live 404 (2026-10-03) | missing (no paths under it) | | | |

## Per-record (crawl downloads)

| code | title | downloaded | identical on Reggie | different | missing | missing MB | large files: on Reggie (size ok) / differs / missing |
|---|---|---|---|---|---|---|---|
| lw7601 | Photo Gallery: Magic Mountain Amusement Park Visit, 1976. | 42 | 42 | 0 | 0 | 0.0 | 21 / 0 / 0 |
| lw6902 | 1969 Newhall-Saugus-Valencia-Canyon Country Telephone Direct | 216 | 216 | 0 | 0 | 0.0 |  |
| lw3801 | Blum Ranch: George Blum Sr.'s Swiss Travel Trunk. | 24 | 24 | 0 | 0 | 0.0 |  |
| lw3800 | Blum Ranch: Quarter-Circle G Branding Iron. | 12 | 12 | 0 | 0 | 0.0 |  |
| lw3794 | Original Living Room Furniture, Harry Carey Adobe. | 19 | 19 | 0 | 0 | 0.0 |  |
| lw3788 | 5-Gal Service Station Fuel/Oil Can, 1920s-(1950s?). | 18 | 18 | 0 | 0 | 0.0 |  |
| lw3781 | Railroad Hand Cart. | 6 | 6 | 0 | 0 | 0.0 |  |
| lw3779 | Switch Tie Plate from Saugus. | 10 | 10 | 0 | 0 | 0.0 |  |
| lw3762 | New Cab Cover for Mogul Locomotive, 2020. | 10 | 10 | 0 | 0 | 0.0 |  |
| lw3755 | Fort Tejon State Historical Monument Brochure, n.d. (pre-196 | 7 | 7 | 0 | 0 | 0.0 |  |
| lw3743 | Seasoned with Grace: A Collection of Recipes by Crown Valley | 432 | 432 | 0 | 0 | 0.0 |  |
| lw3690 | Photo Gallery: Vasquez Rocks Vacation Photos, 1969. | 19 | 19 | 0 | 0 | 0.0 | 6 / 0 / 0 |
| lw3686 | Curry's Lebec Lodge: Room Key and Fob with 3c Prepaid Postag | 30 | 10 | 0 | 20 | 54.5 |  |
| lw3664 | Juventino del Valle, Black Walnut Tree, (5) Important Camulo | 24 | 24 | 0 | 0 | 0.0 | 8 / 0 / 0 |
| lw3663 | Dell Comic No. 791: ''The 77th Bengal Lancers'' (No. 1/Only) | 72 | 72 | 0 | 0 | 0.0 |  |
| lw3647 | Guide to Hi Jolly Pioneer Cemetery, Quartzsite, Ariz., n.d.  | 24 | 24 | 0 | 0 | 0.0 |  |
| lw3609 | Signal (Marker) Tree Loses Large Limb, 8/2019. | 40 | 40 | 0 | 0 | 0.0 |  |
| lw3607 | Fold-out Brochure: Indian Dunes Family Motor Recreation Park | 2 | 2 | 0 | 0 | 0.0 | 0 / 0 / 2 |
| lw3604 | Call Sheet: ''Wild Wild West'' (Warner Bros. 1999), 4-21-199 | 3 | 3 | 0 | 0 | 0.0 | 0 / 0 / 3 |
| lw3578 | Movie Herald: ''Die Flamme von Arabien'' (''Flame of Araby'' | 8 | 4 | 0 | 4 | 141.9 |  |
| lw3576 | Indian Dunes' First Competitive Event (Story November 1970). | 10 | 5 | 0 | 5 | 211.8 |  |
| lw3575 | Brochure: Dragoon Walk (Self-Guided Trail), Fort Tejon State | 2 | 0 | 0 | 2 | 88.7 |  |
| lw3567 | Fold-out Brochure: Indian Dunes Family Motor Recreation Park | 2 | 2 | 0 | 0 | 0.0 | 0 / 0 / 2 |
| lw3558 | Photo Gallery: William S. Hart Jr. as Young Adult in Cowboy  | 36 | 36 | 0 | 0 | 0.0 | 0 / 7 / 5 |
| lw3536 | Southern Pacific and T&NO Class M-4 2-6-0s (Moguls). | 14 | 14 | 0 | 0 | 0.0 |  |
| lw3534 | Course of the Month: Indian Dunes (Dirt Bike, June 1973). | 10 | 10 | 0 | 0 | 0.0 |  |
| lw3522 | Photo Gallery: Perkins Building (''Red Signal Buildings'') U | 18 | 18 | 0 | 0 | 0.0 |  |
| lw3521 | James Garner as Maverick: ''Relic of Fort Tejon,'' Dell Comi | 72 | 72 | 0 | 0 | 0.0 |  |
| lw3518 | The Pacific Mineralogist, December 1941 (Complete): Includes | 64 | 64 | 0 | 0 | 0.0 |  |
| lw3517 | Bermite's Patrick Lizza: He Sets the Sky On Fire / Saturday  | 5 | 5 | 0 | 0 | 0.0 | 5 / 0 / 0 |
| lw3516 | 47th Annual SCV Boys & Girls Club Auction Catalog, 2018. | 128 | 90 | 0 | 38 | 1617.0 |  |
| lw3505 | Story of Edwin Carewe's Version of ''Ramona'' in Italian / C | 8 | 0 | 0 | 8 | 9.4 | 0 / 0 / 8 |
| lw3487 | Charley Mack House, 22931 8th St., Property Listing 2019. | 162 | 162 | 0 | 0 | 0.0 |  |
| lw3481 | Squadron of Giant Tillers Speed California's Castaic Dam, To | 4 | 2 | 0 | 2 | 84.5 |  |
| lw3457 | Kingsburry House: Temporary Roof Repairs, 11-27-2018. | 119 | 119 | 0 | 0 | 0.0 |  |
| lw3453 | Lebec (Hotel?) Restaurant and Coffee Shop Menu, n.d. (~1960s | 12 | 12 | 0 | 0 | 0.0 | 0 / 0 / 4 |
| lw3451 | Beale's U.S. Camel Corps: Hi Jolly's Tomb & Cemetery, Quartz | 41 | 41 | 0 | 0 | 0.0 |  |
| lw3404 | J.C. Agajanian: Saugus Hog Rancher, Race Promoter / Biograph | 14 | 14 | 0 | 0 | 0.0 |  |
| lw3402 | Charlie Chaplin at First Presbyterian Church in 'The Pilgrim | 26 | 26 | 0 | 0 | 0.0 |  |
| lw3392 | Rock Arch at Needham Ranch Moved, Again / 8-30-2018. | 314 | 314 | 0 | 0 | 0.0 |  |
| lw3390 | Backgrounder: Express Companies & Staging in California / Tr | 12 | 12 | 0 | 0 | 0.0 |  |
| lw3386 | Weireter, Christopher Honored by Camulos Museum, 8-26-2018. | 39 | 39 | 0 | 0 | 0.0 |  |
| lw3368 | Photo Gallery: Piru Oil Lease with Likely Owner and Visitors | 14 | 14 | 0 | 0 | 0.0 | 0 / 0 / 7 |
| lw3365 | Borax 20 Mule Team Scale Model Assembly Instructions. | 7 | 7 | 0 | 0 | 0.0 |  |
| lw3359 | Battle of San Pasqual, 1846: Pico Routs Kearny; Beale's Stea | 174 | 174 | 0 | 0 | 0.0 |  |
| lw3332 | Edwin Carewe's 'Ramona' (1928): 6 Spanish Pressbook Illustra | 18 | 0 | 0 | 18 | 49.1 | 0 / 0 / 6 |
| lw3316 | Railfanning on Southern Pacific's Saugus Line, November 1991 | 20 | 20 | 0 | 0 | 0.0 |  |
| lw3312 | Stock Car Racing Magazine, July 1981. | 8 | 8 | 0 | 0 | 0.0 |  |
| lw3308 | OSI Report: Air Force Pilots Sight UFO Near Lebec, 9-5-1949. | 38 | 38 | 0 | 0 | 0.0 |  |
| lw3299 | Photo Gallery: Tennessee Ernie Ford Attends BBQ at Cliffie S | 14 | 14 | 0 | 0 | 0.0 | 1 / 0 / 0 |
| lw3265 | The Winged Monster of Elizabeth Lake (Old West, Fall 1969). | 10 | 10 | 0 | 0 | 0.0 |  |
| lw3257 | Photo Gallery: Largest Ancient Chumash Basket Ever Found (SB | 68 | 68 | 0 | 0 | 0.0 |  |
| lw3254 | Story: Southern Pacific's Historic Soledad Canyon Link, 1984 | 16 | 16 | 0 | 0 | 0.0 |  |
| lw3222 | 'Fireball 500' Lobby Cards (Full Set of 8). | 24 | 0 | 0 | 24 | 53.1 |  |
| lw3219 | Thousand Trails Soledad Canyon RV & Camping Resort, Slide Sh | 50 | 50 | 0 | 0 | 0.0 |  |
| lw3197 | Application for the Incorporation of the City of 'Santa Clar | 49 | 49 | 0 | 0 | 0.0 |  |
| lw3187 | Park and Museum Brochure, 1960s. | 33 | 33 | 0 | 0 | 0.0 |  |
| lw3149 | 1st City Council Members' New Year's Resolutions, SCV Magazi | 7 | 7 | 0 | 0 | 0.0 |  |
| lw3134 | Official Program: Bonelli Stadium Automobile Races, 10-7-194 | 8 | 8 | 0 | 0 | 0.0 |  |
| lw3122 | Bonelli Stadium Racing Program 5-26-1946; Driver Profile: Bi | 12 | 12 | 0 | 0 | 0.0 |  |
| lw3118 | Newhall Elementary School Auditorium as Warehouse, Interiors | 12 | 12 | 0 | 0 | 0.0 |  |
| lw3117 | City's 10th Birthday Ice Cream Party, 1997. | 18 | 18 | 0 | 0 | 0.0 |  |
| lw3104 | The Move to Secede from Los Angeles County, by Bob Simmons,  | 8 | 8 | 0 | 0 | 0.0 |  |
| lw3103 | Program Book: 34th Annual Newhall-Saugus Rodeo, 1960. | 20 | 10 | 0 | 10 | 10.6 |  |
| lw3097 | Fort Oghora Built for NBC's '77th Bengal Lancers' / TV Guide | 6 | 6 | 0 | 0 | 0.0 |  |
| lw3076 | Corporate Seal Embosser, Sterling Borax Co. (Unique), 1908. | 26 | 26 | 0 | 0 | 0.0 |  |
| lw3061 | Program Book: Dedication of Permanent COC Campus, 10-26-1970 | 8 | 8 | 0 | 0 | 0.0 |  |
| lw3060 | City Formation Committee Kicks Off Voter Registration Progra | 4 | 4 | 0 | 0 | 0.0 |  |
| lw3059 | Arthur Young CPAs Predict 22% Budget Windfall for Proposed C | 4 | 4 | 0 | 0 | 0.0 |  |
| lw3048 | Exterior Views & Barnyard, 1968. | 10 | 10 | 0 | 0 | 0.0 |  |
| lw3047 | Hart Mansion Interiors, 1968. | 10 | 10 | 0 | 0 | 0.0 |  |
| lw3046 | Vasquez Rocks County Park, 1968. | 12 | 12 | 0 | 0 | 0.0 |  |
| lw3037 | Montie Montana Rodeo Ranch Photo Collection, 1970s-1980s. | 22 | 22 | 0 | 0 | 0.0 |  |
| lw3029 | Brochure: (History of) The Piru Mansion, 1988. | 8 | 8 | 0 | 0 | 0.0 |  |
| lw3028 | Newhall Ranch Marketing Brochure, 1990s. | 24 | 24 | 0 | 0 | 0.0 |  |
| lw3017 | Wooden Plow, Spanish Mission Period, Early 1800s. | 15 | 15 | 0 | 0 | 0.0 |  |
| lw3007 | SCV School District Reorganization Plan (Failed), 1970-71. | 10 | 10 | 0 | 0 | 0.0 |  |
| lw2993 | Tepee Rock Shop (Soledad Cyn.): Rockhounding Guide for L.A.  | 72 | 72 | 0 | 0 | 0.0 |  |
| lw2992 | The Bogus Story of Tom Vernon and the Sweetwater Incident, G | 14 | 14 | 0 | 0 | 0.0 |  |
| lw2980 | Slated for Demolition: 24326 Walnut Street Residence, April  | 92 | 92 | 0 | 0 | 0.0 |  |
| lw2979 | 2015: John Bergstrom, Gency Brown and Buck Corbett Entertain | 10 | 10 | 0 | 0 | 0.0 |  |
| lw2978 | 2015 Rotary Club Pancake Breakfast. | 28 | 28 | 0 | 0 | 0.0 |  |
| lw2977 | 2015 Parade, Inside-Out (Spectators & Organizers). | 178 | 178 | 0 | 0 | 0.0 |  |
| lw2248a | Photo Gallery: 1876 Golden Spike. | 30 | 30 | 0 | 0 | 0.0 |  |
| lw2214 | Photo Gallery: Sandberg's Summit Hotel Site, 2006. | 12 | 12 | 0 | 0 | 0.0 |  |
| lw1501 | Photo Gallery: Grounds, Rose Garden, Carreta, 2015. | 28 | 28 | 0 | 0 | 0.0 |  |
