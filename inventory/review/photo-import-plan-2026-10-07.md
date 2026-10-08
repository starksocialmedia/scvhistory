# The never-imported photograph images: plan and time (7 October 2026)

From inventory/review/photo-images-census-2026-10-07.md: 1,431 photograph records have their image on Reggie and were never
imported. A plan, not the import (Nathan: "Give me the plan and the time, not the import").

## The rule for what goes to the server

Web copies on the server, masters on Reggie. The same rule docs/DEPLOY.md ("The masters") already sets for the enlarge
targets. Each asset records its master's path on Reggie (legacySourcePath on the archiveMedia layout), so nothing is cut
loose from its original.

## Size

| Set | Records | Files | As on Reggie | On the server as web copies |
|---|---|---|---|---|
| Single images | 1,339 | 1,339 | 6.15 GB | about 1.8 GB (2,400 px long edge, JPEG quality 82; measured on a sample of 40: mean 1.35 MB) |
| Flipbooks and PDFs | 89 | 1,602 | 4.1 GB as JPEG and PDF; 57.6 GB with 501 TIFF masters | about 1.5 to 4.1 GB (pages resized as above; PDFs as they are). To be measured in the dry run. The TIFF masters never go to the server. |
| Found by code | 3 | 4 | 1.8 MB | 1.8 MB |

Server today: web/uploads is 7.6 GB locally (7.5 GB on staging, 22 September), 6.4 GB of it the `_large` and `_orig`
masters. The import adds about 3.3 to 6 GB. If the existing masters move off the web root as DEPLOY.md proposes, the
total after the import is about 5 to 7 GB instead of 11 to 13.6 GB.

Server, measured by Nathan on 7 October 2026: 158 GB, 94 GB used, 58 GB free; uploads 7.3 GB. Disk is not a constraint.

**The magnifier originals move off the web root as part of this import** (Nathan, 7 October 2026), not for space: full-
resolution masters should not be publicly fetchable by anyone walking the uploads directory, and the web root is not where
masters belong. The magnifier then opens a web copy, as DEPLOY.md "The masters" describes.

## Steps

1. Script and dry run: list every file, the derivative size and the target record; no writes. About two hours of work.
2. Apply locally: make each web copy with sips, create the asset, relate it as featuredImage, record the master path.
   About one to one and a half hours of machine time for roughly 3,000 files.
3. Predeploy (about 25 minutes), commit.
4. rsync to the server: 3.3 to 6 GB. At 20 Mbps up about 25 to 40 minutes; at 10 Mbps about 45 to 80 minutes.
   `--partial` resumes a dropped run.

## Not in this import

- The 6 records whose page is on Reggie but whose image file is not, and the 4 once counted as "nothing anywhere"
  (#5735, #5737, #5739, #4475): the Internet Archive holds their pages and their thumbnails, captured as late as
  December 2025, so the live server was still serving them then. The full images are most likely on Leon's server; ask
  him for the files. Not lost.
- The 8 with no image by nature (videos, essays, indexes).

**Status (7 October 2026):** approved in principle by Nathan; waits on the mirror-gap count (how many of the 1,449 have an image on the Internet Archive that Reggie lacks).

**Done (7 October 2026, night):** 1,416 records imported (import_photograph_images_2026_10_07.php): 1,526 new web-copy assets, 1.5 GB; 1,305 existing assets linked. The magnifier masters left the web root first (storage/masters). 15 records held for Nathan (photo-import-held-2026-10-07.md). Only #4435 came from the Internet Archive; the other four thought to need it are on Reggie (ERRORLOG).
