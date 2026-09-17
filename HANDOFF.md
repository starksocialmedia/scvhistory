# Handoff — 2026-09-17 (end of session)

## State of the build
Craft 5 site on DDEV at https://scvhistory.ddev.site. Design system settled:
Playfair Display, Jost, Public Sans; navy #17254C, gold #C4A031/#A9842B, cream #FDF7EA.
One layout (_layouts/base.twig), one CSS partial (_partials/scv-extra-css.twig),
shared sidebar partials (_partials/sidebar/), shared map partial (_partials/map-index.twig),
record partials (_partials/record/) from batch 4.

## Branches
- main: merged through templates-batch-3. NOT pushed since. Do not push main until deploy.yml is fixed (see TODO.md).
- templates-batch-4: record tools, person record, community coords script. Pushed. Unmerged.
- templates-batch-5: QA sweep (CC). Pushed. In progress or done; check CHANGELOG.
- templates-batch-6: attachments, inline images, editor notes (CC). Pushed. Holds the attachment and editor-note field schema commits.
Merge order when CC is quiet: 4, 5, 6 into main, locally. Never checkout a branch while CC is running.

## Schema added today (all in config/project)
- featuredImage on every entry type
- recordImages, recordDocuments on every entry type (Media tab)
- webmasterNoteTop, webmasterNoteBottom on every type lacking a pair (labelled Editor's note)
- placeLat/placeLng, orgLat/orgLng, articleLat/Lng, communityLat/Lng all at 6 decimals
- neighborhood category group has URLs: communities/{slug}

## Data done today
- WP import: 44 new, 47 updated entries; 68 featured images; Reynolds order restored
- War Memorial: 36 records populated; Rudy Acosta linked to Dante Acosta via wmRelatedPerson
- Places: 16 with coordinates; Sleepy Valley removed; Lake Hughes fixed; beales-cut merged
- Organizations: 14 with coordinates
- Communities: 35 terms; 26 with coordinates; 15 with polygons (11 county CSA, 4 ZCTA)
- Person bios: 16 paragraphed by paragraph_bios.php
- Bodies cleaned of WP shortcodes by clean_bodies.php
- Service seals in web/uploads/archive-media/site/seals (public domain, Wikimedia)

## Waiting on Nathan
- 6 community coordinates: fair-oaks-ranch, haskell-canyon, mint-canyon, potrero-canyon, ravenna, towsley-canyon (script in chat; skip = leave [0,0])
- Bios still single-paragraph: jerry-reynolds, dante-acosta (hand-mark)
- Community write-ups: all 35 bodies empty
- Confirm LA County GIS licence; add attribution before launch
- 12 places with no photo until legacy image pull

## Waiting on the drive (Reggie)
- Full legacy site extract, images for places, all remaining articles

## Lessons
- Twig: do not `{% set v = (h) => ... %}` and call v(); Twig 3.21 cannot call a stored arrow function. Use a plain dictionary.
- Number fields default to 0 decimals; set 6 on every new lat/lng field.
- Two writers on templates/ clobber each other. While CC runs: no template edits, no branch switching.
- Long single-line commands wrap and break on paste; keep them short or split.
