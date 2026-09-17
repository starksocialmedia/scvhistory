SCVHistory.com — Changelog

2026-09-16

- Agent: Grok Build
- Date: 2026-09-16
- Done: reported empty titles (IDs 583-685) caused by import_places_and_series.php plus hasTitleField false. Wrote TODO.md Waiting on Nathan for 22 community Places, 10 real Places, 13 collection titles from Jordy. No database changes.
- Decisions: none executed. 7 Places listed as Open Questions. Five neighborhood terms missing (not added).
- Blockers: waiting on Nathan before any Place delete or title write
- Next: Nathan approves TODO.md then execute

2026-09-16

- Agent: Grok Build
- Date: 2026-09-16
- Done: wrote CANDIDATES.md from candidate_review (10 people, 29 places, 0 orgs). Aliases marked. No Craft import or edits.
- Decisions: none
- Blockers: none
- Next: Nathan reviews CANDIDATES.md

2026-09-16

- Agent: Grok Build
- Date: 2026-09-16
- Done: checked out grok-build. Updated CONTENT-MODEL.md (War Memorial section; Person = figures/authors; ranchos may be Place and Org; local DDEV is sample). Copied PHILOSOPHY.md from main. Extracted inventory/entities.json (4186 mentions) and draft inventory/canonical_entities.json (122 sample records, 39 candidates for review). No new Craft entries. No articles. No Cloudways.
- Decisions: outputs in ~/scvhistory; candidate_review requires 3+ distinct editorial pages; war memorial names are not Persons
- Blockers: none
- Next: Nathan reviews canonical_entities.json candidate_review list

2026-09-16

- Agent: Grok Build
- Date: 2026-09-16
- Done: wired obvious Place relations (10 Places with people, 4 with orgs, Mentryville related to Pico Canyon). Wrote PLACE-RELATIONS.md. Persons still 33. No new stubs. No Cloudways.
- Decisions: skip unsure civic mentions (Acosta, Wilk) and missing Friends of Mentryville org
- Blockers: none
- Next: Nathan reviews /places/newhall and PLACE-RELATIONS.md Unlinked list

2026-09-16

- Agent: Grok Build
- Date: 2026-09-16
- Done: Places hub live at /places (39 stubs, neighborhood filter, no map). Series live at /collections (13 Signal/OTN stubs, no articles). Templates places/_entry and collections/_entry. 301 tables stay in hub files only.
- Decisions: Place body is a one-line ID; neighborhood tagged only when the term already exists; Mentryville Place kept separate from Organization; Tataviam Culture is not a Place
- Blockers: none
- Next: Nathan reviews /places and /collections before article attach or Cloudways 301s

2026-09-16

- Agent: Grok Build
- Date: 2026-09-16
- Done: 11 memorials imported into warMemorials from Jordy; hub specs written (PLACES-HUB.md, COLLECTIONS-HUB.md); Persons unchanged (33)
- Decisions: remaining WWII profiles go to warMemorials, not Persons; no Place or Collection import
- Blockers: none
- Next: Nathan reviews 36 War Memorial entries before more HTML imports

2026-09-16

- Agent: Grok Build
- Date: 2026-09-16
- Done: added warMemorials section; moved 25 casualty records out of Persons
- Decisions: casualties are War Memorial entries; Person stays figures and authors
- Blockers: none
- Next: import remaining warmemorial HTML into warMemorials, not Persons

2026-09-15

- Agent: Grok Build
- Date: 2026-09-15
- Done: specced Places and Collections hubs; wrote PLACES-HUB.md, COLLECTIONS-HUB.md, places-candidates.json. Extracted People-category object-page candidates to people-candidates.json (50, no Craft import). War Memorial is a separate local section (`warMemorials`, URI war-memorial/{slug}); listed 25 Person slugs to move later; did not import the 36 HTML files; did not delete Persons.
- Decisions: indexes 301 to Place stubs; Signal/OTN series are Collections; Person is figures and authors only; casualty honor records belong on War Memorial; no Craft content import this session
- Blockers: none
- Next: Nathan reviews hub specs before Place stubs are imported, and reviews the 25 war-memorial Persons plus people-candidates.json before another Person import

2026-09-15

- Agent: Grok Build
- Date: 2026-09-15
- Done: exported Person Find A Grave fields to grave-audit-export.md for Grok Bot
- Decisions: none
- Blockers: none
- Next: push so Grok Bot can audit

2026-09-15

- Agent: Grok Build
- Date: 2026-09-15
- Done: exported all 58 local Person entries to grave-audit-export.md (slug, title, fullName, birthDate, deathDate, burialPlace, personGraveUrl). Abel Stearns personGraveUrl stays empty. No missing-memorial research. No Cloudways.
- Decisions: audit file lists stored URLs only; do not invent Find A Grave links
- Blockers: none
- Next: Nathan reviews grave-audit-export.md

2026-09-15

- Agent: Grok Build
- Date: 2026-09-15
- Done: Task 4 Person pilot. Extracted 25 war-memorial profiles from Jordy to JSON on the MacBook. Imported 25 new Person entries in local DDEV (created=25 skipped=0 failed=0). Local count 33 to 58. personGraveUrl left empty. No Cloudways.
- Decisions: war memorial first; skip existing slugs; no relations; no replacement grave URLs
- Blockers: none
- Next: Nathan reviews the 25 before scaling. Cloudways Abel Stearns grave URL still needs a clear if that entry exists there

2026-09-15

- Agent: Grok Build
- Date: 2026-09-15
- Done: removed wrong Find A Grave URL for Abel Stearns from import scripts. Local Person entry already cleared in DDEV.
- Decisions: no replacement memorial
- Blockers: none
- Next: Cloudways Person field still needs the same clear if that entry exists there

2026-09-15

- Agent: Grok Build
- Date: 2026-09-15
- What was done: Tasks 1-3 on `grok-build`. Wrote `INVENTORY.md` from Jordy (read-only). Wrote `CONTENT-MODEL.md` from live `config/project/`. Implemented photographs and documents channels plus ingest fields in local DDEV only. No Cloudways apply. No imports.
- Decisions made: Skip indexes, empty pages, and flipbook HTML. Object pages to photographs. `files/` packages to documents. Remainder HTML to articles. War memorial profiles to persons. Mentryville is one Organization plus one Place. No Leon gate. Roles Matrix and military merge not built.
- Blockers: none
- Next step: Task 4 only when Nathan asks. Entity-first still applies. Ask before `git push`.

- Task 1 inventory of `/Volumes/Jordy/SCVHistory/scvhistory.com` (read-only). Wrote `INVENTORY.md`
- Live walk: 734,880 files, 655.59 GiB, 0 errors. Manifest (20 Aug 2026): 734,889 files
- Editorial HTML classified: 9,430. Dominant types: object/photo pages 4,871, articles/essays 2,430, obituaries 635, place/topic indexes 554
- `scvhistory/files/` is 1,043 document packages (TIFFs, PDFs, flipbook HTML, Apache indexes), not the editorial site
- TIFFs on this mirror are all under `scvhistory/files/` (about 351 GiB). `gif/` has none
- Scripts in `scripts/inventory/` are resumable and refuse to write to Jordy
- Task 2: wrote `CONTENT-MODEL.md` from live `config/project/` plus INVENTORY.md mapping
- Live schema: 9 sections, no Photograph, no Document. `legacyKey`/`legacyUrl` exist but are not on most types. `sourcePath`/`legacyHtml`/`legacyCategory` do not exist
- Proposed ingest gaps only: Photograph/Object section, Document section, global ingest fields, credit parse fields, body vs legacyHtml
- Did not redesign roles Matrix, military-on-Person, taxonomies, Award, map, haunted, On This Day, or IIIF
- Nathan approved CONTENT-MODEL.md: skip indexes/empty/flipbook HTML; object pages to photographs; files/ packages to documents; remainder HTML to articles; war memorial profiles to persons; Mentryville is one org plus one place; no Leon gate
- Task 3 (local DDEV only): photographs and documents channels; global `legacyKey`, `legacyUrl`, `sourcePath`, `legacyHtml`, `legacyCategory` on every migrated type except militaryProfiles; credit parse fields on photographs; `archivalFiles` and `documentFiles`
- Did not apply on Cloudways. Did not start imports. Did not build roles Matrix or military merge
- Blockers: none
- Next: Task 4 only after Nathan asks. Entity-first still applies

2026-04-15

- Built full 12-phase BUILDPLAN covering templates, Archive.org, API, Flutter, search, ElevenLabs, membership, legitimacy, data model, community, LOD
- Created DATA_MODEL.md — complete field reference for all 9 entry types plus 5 new entry types
- Created TAXONOMY_STANDARDS.md — LCSH/AAT/TGN alignment, CARE Principles, governance
- Created 11 taxonomy JSON files with verified Wikidata/AAT/LCSH URIs in taxonomy-import/
- Created TATAVIAM_AUDIT.md — complete Indigenous cultural audit for 6 peoples (Tataviam, Chumash, Tongva, Serrano, Kitanemuk, Vanyume)
- Verified all Indigenous Wikidata QIDs — corrected Tongva to Q1479279, Chumash confirmed Q24251468
- Created culturalSensitivityNote global field on Cloudways (field ID 174)
- Created Person JSON-LD partial template at templates/_partials/jsonld/person.twig
- Created craft-cp-field-checklist-2026-04-15.md — complete CP field creation guide
- Created IIIF_SESSION_PROMPT.md — 10-question focused prompt for dedicated IIIF session
- Decided: Groups restructured with groupType taxonomy (Family, Cultural Group, etc.)
- Decided: Roles Matrix field replaces plain text occupation on Person
- Decided: Military Profile merged into Person as conditional field group
- Decided: Obituary remains separate section with submission workflow
- Decided: Haunted places, Named places map, Walk of Fame, War Memorial as special pages
- Decided: Import pipeline is entity-first — never import articles before canonical entities exist
- Decided: IIIF Manifests are derived outputs, not stored entities
- Decided: Archive.org hosts files/tiles; Craft owns all metadata and presentation logic
- Added Phase 12 LOD roadmap — Schema.org + Wikidata light LOD first
- Fixed org section template on Cloudways — now rendering at /organizations/{slug}
- Fixed all 14 org entry titles on Cloudways (were null)
- Next: Add culturalSensitivityNote to all entry type layouts in CP, Military Profile migration, pull Cloudways DB to DDEV, build organizations/index.twig

2026-04-14

- Tested organizations/_entry.twig on Cloudways — template rendering correctly
- Fixed org section template path — was _entries/organizations, corrected to organizations/_entry via CRAFT_ALLOW_ADMIN_CHANGES=true in .env
- Fixed all 14 org entry titles — were null due to circular titleFormat reference
- Identified images not showing on Cloudways — featuredImage field empty on org entries, needs investigation
- Identified need for DB sync from Cloudways to DDEV local
- Next: Pull Cloudways DB to DDEV, fix images, build organizations/index.twig
- Received full project handoff document
- Confirmed `php craft eval` does not exist in Craft 5 — use `php craft exec`
- Confirmed Craft 5 sections API is `Craft::$app->entries` not `Craft::$app->sections`
- Built `organizations/_entry.twig` covering all 23 org fields
- Decided: local = code only, Cloudways = content + testing
- Created session management system: BUILDPLAN, CHANGELOG, ERRORLOG, SESSION_START
- SCVTalk moved to backlog — not an active workstream
- **Next:** Push org entry template to Cloudways, build organizations/index.twig

## 2026-09-17 (Claude)

- Found and fixed a root-cause bug: 8 of 12 entry types had no Title field in their layout, so Craft silently discarded titles on save. Added the native Title element to article, militaryProfile, group, organization, obituary, place, collection, event. Person keeps titleFormat {fullName}.
- War Memorial: added 13 service record fields, parsed all 36 records from stored legacyHtml, populated branch, rank, unit, home of record, dates, incident, awards, burial, and narrative. Body replaced with narrative.
- Communities: renamed the Neighborhood category group to Communities (handle stays neighborhood), added body, aliases, type, and lat/lng fields for the planned community map.
- Places: converted 22 community stubs to Community terms, deleted 3 stubs duplicating Organizations, titled 12 real Places and assigned communities. Places went 39 to 14.
- Collections: titled perkins, reynolds, newsmaker. Ten still need titles read off the legacy index pages.
- Decisions: ranchos are Organizations (the land grant), with a Place only where a site survives (Rancho Camulos). Communities are the term, not Neighborhoods or Townships. Soledad Township is a term inside Communities.
- Open: sleepy-valley and lake-hughes Places undecided. 20 relations pointed at deleted Places and need re-pointing. No placeType field exists yet.
- Next, once the drive is on Reggie: full inventory, collection titles, entity-first article import, Place prose, map coordinates.

## 2026-09-17 (Claude)

- Found and fixed a root-cause bug: 8 of 12 entry types had no Title field in their layout, so Craft silently discarded titles on save. Added the native Title element to article, militaryProfile, group, organization, obituary, place, collection, event. Person keeps titleFormat {fullName}.
- War Memorial: added 13 service record fields, parsed all 36 records from stored legacyHtml, populated branch, rank, unit, home of record, dates, incident, awards, burial, and narrative. Body replaced with narrative.
- Communities: renamed the Neighborhood category group to Communities (handle stays neighborhood), added body, aliases, type, and lat/lng fields for the planned community map.
- Places: converted 22 community stubs to Community terms, deleted 3 stubs duplicating Organizations, titled 12 real Places and assigned communities. Places went 39 to 14.
- Collections: titled perkins, reynolds, newsmaker. Ten still need titles read off the legacy index pages.
- Decisions: ranchos are Organizations (the land grant), with a Place only where a site survives (Rancho Camulos). Communities are the term, not Neighborhoods or Townships. Soledad Township is a term inside Communities.
- Open: sleepy-valley and lake-hughes Places undecided. 20 relations pointed at deleted Places and need re-pointing. No placeType field exists yet.
- Next, once the drive is on Reggie: full inventory, collection titles, entity-first article import, Place prose, map coordinates.

## 2026-09-17 (Claude, branch templates-batch-1)

- Agent: Claude
- Date: 2026-09-17
- Built entry and index templates for five sections on the articles/_entry pattern: base layout, scv-extra-css in the head block, breadcrumbs, h1, era and period chips, community chips, body through _partials/prose, right sidebar of .scv-box relation panels with a cite box. Ten template files: places, organizations, groups, events, war-memorial.
- War Memorial entry follows the WordPress version: a Service record panel (branch, rank, specialty, unit, base, operation, plus start of tour, length of service, service number) and an Incident panel (date, location), then a Life panel and an Awards panel. Index groups by wmConflict in chronological order (World War I, World War II, Korean War, Vietnam War, War on Terror), with any unlisted conflict appended and a "Conflict not recorded" bucket.
- Verified every field handle against config/project/ before use, then re-audited every handle each template reads against that entry type's field layout. All clear.
- Tested: all 78 entry pages across places, organizations, groups, events, and warMemorials return 200, plus all five indexes, the filter variants, and bogus filter values.

### Decisions

- Nathan chose to rebuild all five sections in the scv-extra-css system rather than keep two visual systems side by side. The repo had two: main.css (.scv-article-wrap, .scv-sidebar-card, .scv-tag) used by the old places and organizations templates, and _partials/scv-extra-css (.scv-doc, .scv-grid, .scv-body, .scv-box, .scv-chip) used by articles. The brief specified the second.
- The Leaflet location map from the old organizations template was carried forward, not dropped, and added to places on the same terms. It loads only when the entry has both lat and lng, with the stylesheet in the head block and the script in the foot block.
- Filter query parameter stays `neighborhood` on the places and organizations indexes, matching the category group handle, even though the group now displays as Communities.
- Legacy links use `https://scvhistory.com{{ legacyUrl }}` with no added slash, since stored values already begin with one.

### Fixed along the way

- organizations/_entry.twig was returning 500 on every entry: `Invalid transform handle: cardThumb`. The rebuild does not use an image transform.
- persons/_entry.twig and persons/index.twig were returning 500: both extended `_layout`, which does not exist in this repo. Changed to `_layouts/base` and moved the crumbs block into the content block so the breadcrumb is not silently dropped. Nathan approved this as a minimal fix only, no redesign.
- The old organizations template built legacy links as `https://scvhistory.com/{{ orgLegacyUrl }}`, producing a double slash.

### Skipped, and why

- No hero image on place, group, or event entries. `featuredImage` is not in those three field layouts. Only organization, warMemorial, person, and article have it. The panel is in place and will appear on its own if the field is ever added.
- No era, period, or community chips on War Memorial entries. `historicalEra`, `historicalPeriod`, and `neighborhood` are not in the warMemorial field layout. The conflict chip is the only one. This caused the one render failure during the build and was removed rather than worked around.
- Places index does not group by place type and Groups index does not group by group type. Only three category groups exist (historicalEra, historicalPeriod, neighborhood). Place Type, Group Type, Event Type, and Person Subject are described in DATA-ORGANIZATION.md but not built. Places and organizations filter by community; groups and events filter by era; events group by historical period.
- Persons templates still render unstyled. Their class names (.layout, .box, .body, .chip, .crumbs) belong to neither stylesheet. They no longer error, but they need a real rebuild.
- Events are listed as a later-phase type in DATA-ORGANIZATION.md section 3. Built here because the task named them directly.

### Data problems noticed, not touched

No database changes were made. Flagging for a content session:

- Duplicate entries: places has both `beales-cut` and `beales-cut-stagecoach-pass` with the same title; warMemorials has both `terror-rudyacosta` and `rudy-alexander-acosta` for Rudy Alexander Acosta.
- Place `sleepy-valley` still has a NULL title and falls back to its slug in the index.
- Place `lake-hughes` has the title "Lake Huges", likely a typo for Lake Hughes.
- Four War Memorial narratives are truncated mid sentence by the import: ww2-edwardcontreras, terror-brianprosser, korea-henryacuna, ww2-johnward.
- One War Memorial record has no wmConflict and lands in "Conflict not recorded".
- Most Place bodies are the one line import placeholder ("X is a named place in the Santa Clarita Valley historical archive"), so place pages are thin until real prose lands.

### Blockers

- None.

### Next

- Nathan reviews the five sections at scvhistory.ddev.site before this branch merges. Nothing pushed, nothing merged.
- Decide whether persons gets a full rebuild on the same pattern.
- Add the missing category groups (Place Type, Group Type, Event Type) if the indexes should group by type.
- Resolve the duplicate places and war memorial records and the truncated narratives.

## 2026-09-17 (Claude, branch templates-batch-2)

- Agent: Claude
- Date: 2026-09-17
- One sidebar and page system across every entry page, in the current design language. Reference was the persons entry page and the persons and places indexes.

### Shared sidebar partials

New `templates/_partials/sidebar/`:

- `box.twig`: white box, 3px gold top rule, small uppercase Jost heading. Used with `{% embed %}` so the caller supplies the body.
- `related-list.twig`: related entries with a 44px square featuredImage thumbnail, initials fallback in cream (two initials for people, first letter otherwise), title link, and a one-line subtitle. Subtitle by section: persons gives occupation, events give date, places give community, everything else none.
- `location.twig`: Leaflet 1.9.4 map 200px tall, scrollWheelZoom off, then Established, Address and Coordinates rows. Takes lat, lng, address, established and mapId, so places and organizations share it.
- `meta.twig`: last updated, plus read time and word count on articles.
- `cite.twig`: Chicago style, using author, collection, publish date and url.
- `external.twig`: website, Wikipedia, Find a Grave, CHL, SCV landmark, Archive.org and legacy links, only the ones with values. Handles differ per section, so the caller passes values rather than the partial guessing a handle.

### Design language

`_partials/scv-extra-css.twig` rewritten. Playfair Display headings, Jost labels, Public Sans body. Navy #17254C, gold #C4A031 and #A9842B, cream #FDF7EA, borders #E2E4E8 and #EFE6D0, page #F6F7F9. Cormorant Garamond, Inter and the old #1a2744 and #b8860b palette are gone from the file. The Google Fonts link moved into the partial so entry templates stop repeating it. Verified in the browser: computed styles match the spec exactly.

### Entry pages rebuilt

articles, places, organizations, groups, events, war-memorial, collections, and a new obituaries entry. Each: cream band with breadcrumbs, kicker, title, aliases and key facts; era, period and community chips; featured image hero below the band when the section has the field; body in a white card; right sidebar built only from the partials.

- Places get the location box first in the sidebar.
- War memorial keeps the service record and incident panels as boxes, plus life and awards.
- Articles keep the collection pager, the author bio and the in-this-collection list, and gained events, related articles and the editor.
- Every relation the previous templates showed is preserved, including the reverse lookups on depictsPlace, subjectOrganization, publishedBy, personGroups, subjectGroup and articleEvents.

### Index pages rebuilt

organizations, groups, events, war-memorial, collections, and a new obituaries index. Cream band with stats, sticky filter bar where a filter makes sense, card grid with images and initials fallback. Organizations filter by community, groups and events by era, war memorial by conflict, obituaries by era. Collections has no filter so it has no bar.

### Verified

- All 151 entry pages across nine sections return 200, plus 10 indexes and 6 filter variants including bogus filter values.
- Every field handle re-audited against its entry type layout in `config/project/`. The only out-of-layout reads are `featuredImage`, all guarded with `is defined`.
- No horizontal overflow: `scrollWidth` equals `innerWidth` on ten pages at 1280px and 390px.

### Fixed along the way

- Obituaries had no templates at all. Every obituary URL and `/obituaries` returned 404. Both now exist.
- `{% embed ... only %}` does not inherit outer variables, which broke the first place page that had a community. Variables are now passed in explicitly.

### Skipped, and why

- No hero image on place, group, event, article, collection or obituary entries, and no real card image on those indexes. `featuredImage` is only in the person, organization and warMemorial field layouts. Every reference is guarded, so images appear on their own if the field is added.
- No era, period or community chips on war memorial entries. `historicalEra`, `historicalPeriod` and `neighborhood` are not in the warMemorial layout. The conflict chip is the only one.
- Indexes still cannot group by type. Only three category groups exist (historicalEra, historicalPeriod, neighborhood). Place Type, Group Type, Event Type and Person Subject are described in DATA-ORGANIZATION.md but not built.
- The homepage, articles index, persons index and places index were left alone. They are already in this design language with their own hp, ap, pp and pl prefixes. They still use `.scv-band` and `.scv-band-in`, which the rewrite keeps.
- `militaryProfiles` still has no template. It was not in scope.

### Data problems noticed, not touched

No database changes were made.

- The one obituary body still carries WordPress import artifacts: `[caption]` shortcodes, raw img tags, and absolute links to `wordpress-1656314-6593552.cloudwaysapps.com`. The page renders, but the body needs a cleanup pass before launch.
- Everything flagged in the templates-batch-1 entry is still open: duplicate `beales-cut` places, duplicate Rudy Alexander Acosta war memorial records, the NULL title on `sleepy-valley`, "Lake Huges", four truncated war memorial narratives, and the one-line placeholder Place bodies.

### Blockers

- None.

### Next

- Nathan reviews the eight entry types and six indexes at scvhistory.ddev.site before this branch merges. Nothing pushed, nothing merged.
- Clean the obituary body, then import the rest of the obituaries.
- Decide whether militaryProfiles gets templates or is folded into war memorial.
- Add the missing category groups if indexes should group by type.

## 2026-09-17 (Claude, branch templates-batch-3)

- Agent: Claude
- Date: 2026-09-17
- A Communities section with a boundary map, plus one shared map implementation across the three map indexes.

### Category URLs

`scripts/import/setup_community_urls.php` sets the Communities group (handle `neighborhood`) to hasUrls true, uriFormat `communities/{slug}`, template `communities/_entry`, for every site the group is enabled on. Eval style, no opening tag, safe to run twice, prints settings before and after plus sample URLs. **Nathan runs it.** No `config/` was edited by hand.

Until it runs, `/communities/{slug}` returns 404 and `term.url` is null. Both templates fall back to `url('communities/' ~ slug)` so links and citations are already correct.

### Boundary data

`web/data/communities.geojson`, 57 KB, 15 polygons.

- Source: Los Angeles County Enterprise GIS, eGISBOS Countywide Statistical Areas, ArcGIS item `3abf2449cc054d72ab80e8f1968e5d94`.
- Retrieved in WGS84 with `maxAllowableOffset=0.0002` and 5 decimal places.
- Each feature carries `properties.slug` matching the Craft category slug, plus `source_name`, `city_type` and a `note` where the polygon needs one.
- Source URL, attribution, retrieval date, the exact query and the caveats are in the file's `metadata` member and repeated below.
- The ArcGIS item carries no licence statement, so `metadata.license` is marked `NEEDS_VERIFICATION`. Confirm LA County's open data terms before public launch.

**Got a polygon (15):** acton, agua-dulce, bouquet-canyon, canyon-country, castaic, lake-hughes, newhall, placerita-canyon, san-francisquito-canyon, sand-canyon, santa-clarita, saugus, stevenson-ranch, val-verde, valencia

**No polygon (20):** camulos, castaic-junction, fair-oaks-ranch, fillmore, frazier-park, haskell-canyon, hasley-canyon, lebec, mentryville, mint-canyon, mojave-desert, pico-canyon, piru, potrero-canyon, ravenna, saugus-valencia, soledad-canyon, soledad-township, tejon, towsley-canyon

Those 20 split three ways: canyons and historic townsites with no official boundary; Ventura County (camulos, fillmore, piru) and Kern County (frazier-park, lebec, tejon), which an LA County dataset does not cover; and saugus-valencia, which is our own compound term with no single CSA. No polygon was invented for any of them.

Two caveats worth knowing:

- The Newhall, Saugus, Valencia and Canyon Country CSAs cover only the **unincorporated remnants** of those communities. The bulk of each sits inside the City of Santa Clarita polygon. A reader hovering "Newhall" is seeing a fragment, not the historic community.
- san-francisquito-canyon uses the combined CSA "San Francisquito Canyon/Bouquet Canyon", which overlaps the separate bouquet-canyon polygon.

### Templates

`templates/communities/index.twig`: cream band with the community count and a live count of how many have boundaries, a 520px Leaflet 1.9.4 map from cdnjs, then a card grid of all 35 communities with name, type, alias line and counts. Polygons draw navy `#17254C` at 12 percent fill with a navy outline, turn gold `#C4A031` at 32 percent on hover, carry a tooltip with the name and counts, and navigate to the community on click.

`templates/communities/_entry.twig`: cream band with name, aliases and type; body through `_partials/prose`; a 320px map of that community's polygon or its pin; then every related record grouped by section across people, places, organizations, groups, events, articles, war memorial and obituaries. Sidebar uses the batch 2 partials: meta, cite, neighbouring communities, and a per-section count box.

The GeoJSON is fetched at runtime rather than inlined into every render. Twig has no `file_exists`, so the neighbours list is server rendered from `templates/_data/community-neighbors.json`, generated from the same GeoJSON.

Communities was added to the nav in `_layouts/base.twig` after Places. That is the only base.twig edit.

### Neighbouring communities

Derived from the polygons: two communities are neighbours when any boundary vertex of one lies within 250 m of a boundary segment of the other. This is a proximity test on generalised geometry, not a topological adjacency computation, so it can miss a narrow touch or include a near miss. The method note travels with the data in both files. Shapely was not available and installing packages needs approval, so this was done in pure Python.

### Refactor

`_partials/map-index.twig` now holds the map CSS, the map card markup and the Leaflet wiring that `places/index.twig` and `organizations/index.twig` each carried a copy of. Both are about 80 lines shorter. Behaviour is identical: same pins, same card and pin selection, same hint line, same popups, same fit links. Differences are parameters: `hint`, `fitAllLabel`, `showFitValley`, `initialValley`, and the new `polygonsUrl`, `polygonMeta`, `boundaryCount`, `mapHeight`.

Two fixes the move required:

- The shared script now sits before the card grid rather than after it, so it is wrapped in `DOMContentLoaded` and binds card handlers whatever the order. Without this the card clicks would have silently stopped working.
- Leaflet's stylesheet is registered with `registerCssFile` instead of a hand written link tag, so the partial carries its own dependency.

`cite.twig` gained an optional `url` override.

### Verified

- All 151 entry pages, all 11 indexes including the new `/communities`, and all 35 community pages return 200. Community pages were rendered through a temporary harness template, since category URLs are not on yet; the harness was deleted before committing.
- In the browser: 15 polygons drawn, navy fill at 0.12, gold at 0.32 on hover and back on mouseout, tooltips reading for example "Castaic / 8 people · 2 places · 13 articles", polygon click navigating to the community, the boundary counter filling to 15, map 520px.
- Refactor checked by diffing rendered output before and after, then in the browser: 15 pins and 16 cards on places, 14 and 14 on organizations, card clicks intercepted and selecting rather than navigating.
- Every category field handle re-audited against the `neighborhood` group layout. All six exist and all reads are guarded.

### Blockers

- None, but two things need Nathan.

### Needs Nathan

1. **Run the URL script** on **MacBook**, then commit `config/project/`:

```
ddev craft exec "eval(file_get_contents('scripts/import/setup_community_urls.php'))"
```

2. **`orgLat` and `orgLng` are set to `decimals: 0`.** Every organization pin rounds to a whole degree, so Rancho Camulos plots at 34, -119 instead of 34.407, -118.753, roughly 30 km out. This predates this branch; it came in with the organizations map. Fixing it means changing the two field settings and re-running `set_org_coords.php`. Not done here because the brief allowed no config changes beyond the URL script.

### Data problems noticed, not touched

No database changes were made.

- All 35 community terms have completely empty field content: no body, no aliases, no type, no coordinates. So community pages currently show a title, the related records and nothing else, and the map shows only the 15 polygons. The 20 communities without a polygon will stay off the map until `communityLat` and `communityLng` are filled.
- `warMemorials` has no `neighborhood` field, so its count is structurally always zero. The templates query it and simply omit the line, but no war memorial record can be filed under a community until the field is added.
- Everything flagged in batches 1 and 2 is still open.

### Next

- Nathan runs the URL script, then reviews `/communities` and a few community pages.
- Decide on the `orgLat`/`orgLng` precision fix.
- Populate community bodies, types, aliases and coordinates. Coordinates are what unlock the remaining 20 pins.
- Decide whether `neighborhood` should be added to the warMemorial layout.
