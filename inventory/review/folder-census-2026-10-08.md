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

**14 records have content in their own folder that Craft does not hold: 759 items, 882 files.** None of the missing files is in Craft on another record.

2 folders are claimed by two records each (gt8702: #28293 and #28310; scvhs2000minutes: #28283 and #28285), so the table below counts their files under both. Counted once: **745 items, 832 files.**

| Group | Records | Items | Files |
|---|---|---|---|
| documents | 5 | 688 | 760 |
| photographs imported 7 October | 3 | 16 | 16 |
| photographs skipped, already had a picture | 4 | 4 | 4 |
| places | 2 | 51 | 102 |

By kind: 751 picture, 7 pdf, 1 audio.

## By record

| Record | Section | Title | Folder | Held | Not held | Files |
|---|---|---|---|---|---|---|
| #605 | places | Heritage Junction Historic Park | lw3789 | 0 | 39 (39 picture) | 78 |
| #613 | places | Six Flags Magic Mountain | lw3790 | 0 | 12 (12 picture) | 24 |
| #4951 | photographs | Future Veterans Historical Plaza Site | lw3135 | 6 | 1 (1 picture) | 1 |
| #4953 | photographs | Railroad Avenue Improvements | lw3136 | 19 | 1 (1 picture) | 1 |
| #4959 | photographs | Creekview Park Groundbreaking | lw3140 | 12 | 1 (1 picture) | 1 |
| #5077 | photographs | Chumash Storage Basket | brynecache2016, lw3257 | 19 | 14 (14 picture) | 14 |
| #5251 | photographs | William S. Hart, Ione Reed, George Putnam | ionereed | 23 | 1 (1 audio) | 1 |
| #5403 | photographs | Visitors at Curry's Lebec Lodge | lw3552 | 6 | 1 (1 picture) | 1 |
| #5685 | photographs | Newhall-Saugus-Valencia-Canyon Country | lw6902 | 109 | 1 (1 pdf) | 1 |
| #28283 | documents | Santa Clarita Valley Historical Society board minutes, May 19, 2003 | scvhs2000minutes | 0 | 2 (2 pdf) | 2 |
| #28285 | documents | "A Look Back in Time": proposal for the Historical Society's first ann | scvhs2000minutes | 0 | 2 (2 pdf) | 2 |
| #28293 | documents | Cityhood forum announced, The Signal, January 11, 1987 | gt8702 | 0 | 12 (12 picture) | 48 |
| #28310 | documents | Cityhood Backers: Who Are They? | gt8702 | 0 | 12 (12 picture) | 48 |
| #31723 | documents | A Newspaper Editor's Voyage Across San Francisco Bay: San Francisco Ch | uc8901 | 1 | 660 (2 pdf, 658 picture) | 660 |
