# TODO

## Waiting on Nathan

Do not change the database until Nathan names the Place slugs to remove and whether to add missing community terms.

### What created the 52 empty titles

IDs 583 to 685 were created 2026-09-16 07:02:58 to 07:03:00 by `scripts/import/import_places_and_series.php` in local DDEV (commit `220809b`). That script set `$entry->title` from `places-candidates.json` and the hardcoded series list.

Craft still saved empty titles because both entry types have `hasTitleField: false` and `titleFormat: null` (`config/project/entryTypes/place--*.yaml`, `collection--*.yaml`). Craft 5 ignores `Entry->title` on save in that configuration. Same class of bug as the 2026-04-14 org titles (`titleFormat` / title field mismatch). Slugs, body, and ingest fields were stored; titles were not.

Fix when executing: enable the title field on Place and Collection (or set a real `titleFormat`), then write titles. Do not rely on `$entry->title` while `hasTitleField` is false.

Undo of the import itself: the 52 entries are only in local DDEV. A JSON snapshot of id, section, slug, and field values should be written before any delete so they can be recreated.

### 1. Convert 22 community Places to neighborhood categories

These Place slugs are communities, not sites:

acton, agua-dulce, bouquet-canyon, canyon-country, castaic, hasley-canyon, haskell-canyon, lebec, mentryville, mojave-desert, newhall, pico-canyon, piru, placerita-canyon, potrero-canyon, san-francisquito-canyon, saugus, soledad-canyon, tejon, towsley-canyon, val-verde, valencia

Neighborhood group `neighborhood` today:

| Slug | Term exists? |
| --- | --- |
| acton, agua-dulce, bouquet-canyon, canyon-country, castaic, hasley-canyon, lebec, newhall, pico-canyon, piru, placerita-canyon, san-francisquito-canyon, saugus, soledad-canyon, tejon, val-verde, valencia | yes |
| haskell-canyon | **missing** |
| mentryville | **missing** |
| mojave-desert | **missing** |
| potrero-canyon | **missing** |
| towsley-canyon | **missing** |

Do not add the five missing terms unless Nathan approves each one.

Relations on those Places now (move to Person/Org `neighborhood`, then clear Place relations):

| Place slug | Move |
| --- | --- |
| newhall | people: henry-mayo-newhall, arthur-b-perkins, jerry-reynolds, leon-worden, john-gifford → category `newhall` |
| saugus | people: henry-mayo-newhall → category `saugus` |
| placerita-canyon | people: francisco-lopez, abel-stearns → category `placerita-canyon` |
| pico-canyon | people: henry-clay-wiley, jerry-reynolds → category `pico-canyon`; drop relatedPlaces `mentryville` |
| mentryville | people: leon-worden. Cannot assign category until a `mentryville` term exists |

Keep existing Person-Org links (del Valle ranchos, Leon Worden → SCVHistory.com). Those are not Place records.

Then remove the 22 Place entries (hard delete only after snapshot). Community index 301s (`/scvhistory/acton.htm`) must not point at `/places/acton`. Target URL is still open.

**Undo:** restore from the pre-delete JSON snapshot (id, slug, title once fixed, body, legacyUrl, sourcePath, legacyCategory, neighborhood, placePeople, placeOrganizations, relatedPlaces). Re-save as Places. Re-apply placePeople from the snapshot. Category assignments on Persons can stay; they are correct even if the Place is restored.

### 2. Keep these 10 as Places: titles, SEO titles, community

Place entry type has no separate SEO title field. SEOmatic will use the entry title. Proposed title is the display name. Proposed SEO title is the same string until SEOmatic is configured.

| Slug | Proposed title | Proposed SEO title | Community (`neighborhood`) |
| --- | --- | --- | --- |
| vasquez-rocks | Vasquez Rocks | Vasquez Rocks | agua-dulce |
| beales-cut | Beale's Cut | Beale's Cut | newhall |
| ridge-route | Ridge Route | Ridge Route | castaic |
| saugus-speedway | Saugus Speedway | Saugus Speedway | saugus |
| melody-ranch | Melody Ranch | Melody Ranch | newhall |
| magic-mountain | Magic Mountain | Magic Mountain | valencia |
| harry-carey-ranch | Harry Carey Ranch | Harry Carey Ranch | saugus |
| heritage-junction | Heritage Junction | Heritage Junction | newhall |
| fort-tejon | Fort Tejon | Fort Tejon | tejon |
| estancia | Estancia | Estancia | valencia |

Ridge Route, Harry Carey Ranch, Estancia, and Melody Ranch sit near more than one community. Confirm before save.

Execution also requires turning `hasTitleField` on (or a non-empty titleFormat) so titles persist.

### 4. Collection titles from Jordy index pages

Copied from `<title>` or the visible series heading on the collection index. Not invented.

| Slug | Proposed title | Source |
| --- | --- | --- |
| perkins | SCVHistory.com \| The Story Of Our Valley by A.B. Perkins | `scvhistory/signal/perkins/index.html` `<title>` |
| reynolds | History of the Santa Clarita Valley by Jerry Reynolds | `scvhistory/signal/reynolds/index.html` `<title>` |
| worden | Selections From Leon Worden | `scvhistory/signal/worden/index.htm` `<title>` |
| boston | SCVHistory.com \| John Boston \| Santa Clarita History | `scvhistory/signal/boston/jbindex.htm` `<title>` |
| manzer | Darryl Manzer: 'Way Back When' in the Santa Clarita Valley | `scvhistory/signal/manzer/index.htm` `<title>` |
| newsmaker | SCV Newsmaker of the Week | Visible `<h2>` on `scvhistory/signal/newsmaker/index.htm`. The `<title>` is `SCVTV.com \| Local Television for Santa Clarita` (site chrome, not the series) |
| iraq | Abu Ghraib Prison Abuse Scandal Hits Home | `scvhistory/signal/iraq/index.htm` `<title>` |
| coins | NEEDS_TITLE | No index page under `scvhistory/signal/coins/` |
| otn-gazette | NEEDS_TITLE | `oldtownnewhall/index.htm` is a redirect with empty title. No `gazette/index.htm` |
| otn-patti | 'Open Book' - Santa Clarita Valley School Issues with Patti Rasmussen | `oldtownnewhall/patti/index.html` `<title>` |
| otn-pauline | Pauline Harte | `oldtownnewhall/pauline/index.htm` `<title>` |
| otn-rioux | Richard 'Doc' Rioux At Large | `oldtownnewhall/rioux/index.htm` `<title>` |
| otn-whyte | Black 'N' Whyte | `oldtownnewhall/whyte/index.html` `<title>` |

Same title-field bug as Places: titles will not stick until Collection `hasTitleField` is true.

## Open Questions

Leave these 7 Place entries untouched until Nathan decides:

- sleepy-valley
- lake-hughes
- santa-clarita
- lang
- rancho-san-francisco
- rancho-camulos
- tejon-ranch

Also still open: 301 target for community indexes; whether to add the five missing neighborhood terms; Ridge Route / Harry Carey Ranch / Estancia / Melody Ranch community assignment; coins and OTN Gazette collection titles.

## Deploy pipeline (blocking production)
- deploy.yml only runs `git pull origin main`. Production never receives schema changes.
- Fix: after the pull, run `composer install --no-dev --optimize-autoloader`, `php craft project-config/apply`, `php craft up`, then `php craft clear-caches/all`.
- Also fix the GitHub Actions to Cloudways SSH connection that broke on the new server.
- Until fixed: do not push main. Merge locally only. Production stays on the old build.
- After fixed: first deploy must also copy web/uploads/archive-media/site (logos, seals, now tracked) and the Craft assets volume.

## Data gaps noted this session
- 20 communities have no polygon; coordinates come from set_community_coords.php (batch 4).
- Newhall, Saugus, Valencia, Canyon Country polygons are unincorporated fragments; sub-city boundaries task pending (city GIS, then ZCTA fallback).
- Community terms have no body, aliases, or type content yet.
- 12 places have no featured image until the legacy site images are pulled.
- GeoJSON licence marked NEEDS_VERIFICATION; confirm LA County GIS terms and add attribution before launch.
- militaryProfiles section has no template.
- Obituary body still carries WordPress artifacts; run clean_bodies.php again after adding obituaries to the field list.
