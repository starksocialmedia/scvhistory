SCVHistory.com — Changelog

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: scripts/import/add_wm_community.php adds neighborhood, wmFamily and wmSelectiveServiceDate to the warMemorial entry type. import_warmemorial.php maps the two new labels and skips five letter fragments as noise. war-memorial/_entry.twig renders all eight new fields. war-memorial/index.twig rebuilt: cream band, grouped by conflict chronologically, no filter and no sort, and a nameplate card face where there is no portrait.
- Decisions: the #877 duplicate is reported by the pre-flight and otherwise left alone. The external box label changed from RELATED to EXTERNAL, matching every other entry template, because that is where wmWallReference belongs. On a nameplate card the face carries the name and rank, so the strip beneath carries only branch and year; both card types still convey the same four facts.
- Blockers: the F loop in war-memorial/_entry.twig used `is defined`, which reads true for a field the entry type does not have and then throws on read. It 500ed every casualty page once wmFamily was referenced. Rewritten to read the entry's own field layout.
- Next: Nathan runs add_wm_community.php then import_warmemorial.php

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: band artwork now comes from bandImage first with featuredImage only as a fallback, in collections/_lander.twig and in the six other entry templates whose band carries an image. Each carries a one-line note that the fallback may show lettering. Lander stat blocks rebuilt: articles, chapters, publication runs and eras covered, each derived from the collection's own articles.
- Decisions: articles/_entry.twig is untouched because its band has no image; pages/_entry.twig is untouched because the page entry type has no bandImage field. A run or era count of one is omitted rather than printed, since one is not a statistic.
- Blockers: the stats compute to 23 articles, 21 chapters, 3 publication runs and 3 eras covered, not the 2 runs and 4 eras expected. The six portrait bands use a 4:5 frame with object-position top, so a wide right-composed band image will crop to its top strip there.
- Next: Nathan uploads the CD artwork to bandImage on the Reynolds collection, and decides on the run gap threshold and the era count

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: wrote scripts/import/import_warmemorial.php, eval style, dry run by default. 54 casualty pages: 18 created, 36 gap-filled on legacyKey. service_record labels mapped to the wm fields; the 45 unmapped labels go to wmServiceExtra as label and value verbatim, 69 rows, nothing dropped. wmConflict set from the legacy_key prefix. Also wrote inventory/legacy/warmemorial-images.json, 133 images, none downloaded. Verified every field write with a temporary fixture record, then hard-deleted it.
- Decisions: a trailing period is trimmed from a title only when the last word is not an abbreviation or an initial, so the four transcription artifacts are fixed and the six Jr. names are left intact. Where two labels hit one field the first in the map wins and the other overflows, the rule the brief sets for College. No value corrected: the three "Amry of the United States" pages import verbatim.
- Blockers: neighborhood is not on the warMemorial entry type, so communities_mentioned is dropped on 51 pages. Craft holds a duplicate Rudy Alexander Acosta, #877 and #526, sharing one legacy URL. The war-memorial template renders none of the eight new wm fields.
- Next: Nathan runs the script, resolves the Acosta duplicate, and decides on a neighborhood field and template rows for the new fields

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: collection lander rebuilt to the cream band spec with two masked image layers; Contents now groups by the collection's own parts in articlesInCollection order and the era chips and "No era recorded" group are gone. Added scripts/import/add_collection_parts.php (collectionParts table field, four parts for the Reynolds work). Added scripts/import/import_reynolds.php for the 80 page reynolds-full.json. Added LEGACY_HOST to .env and a new .env.example, config/custom.php exposing legacyHost, and scripts/import/normalise_legacy_urls.php. templates/_partials/legacy-url.twig now reads the host from config.
- Decisions: custom config lives in config/custom.php because Craft 5 GeneralConfig has no slot for it; general.php points at it. The normaliser leaves a legacy URL pointing at another host alone rather than stripping it to a path that would resolve against the wrong site. Reynolds matching falls back to the legacyUrl tail and then to the normalised title, because five WordPress rows have a wrong or missing legacyUrl.
- Blockers: 14 of the 23 existing Reynolds bodies differ from the legacy text and were left alone pending a call on which is authoritative. The four collectionParts rows only cover positions 1 to 23, so once all 80 land, PART FOUR swallows everything from Chapter 22 on.
- Next: Nathan runs add_collection_parts.php, normalise_legacy_urls.php and import_reynolds.php, and decides on the Reynolds body differences

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: wrote scripts/import/import_perkins.php, eval style, dry run by default. Imports the 20 wave 0 Perkins pages as Articles matched on legacyKey, wires the 13 series pages to the Story of Our Valley collection in series_position order, and writes inventory/legacy/perkins-images.json (199 images, not downloaded). Dry run reported; nothing written to the database. Verified every field write with a temporary fixture article, then hard-deleted it.
- Decisions: match falls back to legacyUrl when legacyKey is empty, so the already-imported Birth of Newhall (#869) is adopted rather than duplicated. An existing article is only gap-filled, never clobbered, unless $OVERWRITE is set. An empty extracted value never overwrites anything.
- Blockers: date_raw and subtitle are empty on all 20 pages, so originalPublishDate and subheadline cannot be set. Saugus-Valencia has no matching neighborhood term; Craft spells it Saugus/Valencia. body_text carries legacy site chrome.
- Next: Nathan decides on Saugus-Valencia, on the body_text chrome, and whether to run with $APPLY

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: rebuilt the site header and mega menu in templates/_layouts/base.twig and templates/_partials/header/site-header.twig, from design/menu-source.html for markup and design/MENU-MAPPING.md for data, targets and omissions. Five menus, live counts, feature cards, active-state mapping, hover and keyboard behaviour, 980px collapse. Whole fragment cached with Craft's cache tag keyed on the active menu.
- Decisions: items marked OMIT in MENU-MAPPING.md are absent, and a column left empty by them is dropped. Photo galleries and Documents omitted because both sections are empty. DONATE omitted because no donate page exists. Panels are all rendered and toggled rather than conditionally rendered, so the header can be cached as one fragment.
- Blockers: none
- Next: Nathan reviews the header, decides on DONATE and on showing a zero count for Newsmaker of the Week

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

## 2026-09-17 (Claude, branch templates-batch-4)

- Agent: Claude
- Date: 2026-09-17
- One record design across every entry page, shared record tools, sub-city community boundaries, and a community coordinates script.

### Merge and push

Not done. AGENTS.md says never push to main, and deploy.yml redeploys Cloudways on any push to main. There is also a gap: the workflow only runs `git pull origin main`, so production would get the community templates without the applied project config and `/communities/{slug}` would 404 there until `project-config/apply` runs on the server. Nathan chose to merge and push himself. templates-batch-4 was branched from templates-batch-3, which is identical to what main becomes, so it still merges cleanly.

### Record tools as shared partials

New `templates/_partials/record/`:

- `css.twig`: the record design system lifted from the approved war memorial page. Cream band, kicker, h1, italic subtitle, labelled facts, chips, optional portrait, two column main, cream sidebar boxes, the `.rec-rows` label/value grid.
- `tools.twig`: read time, word count, updated date, Save / Print, Listen. Listen uses SpeechSynthesis and stays hidden when the API is missing. Read time is omitted under 20 words.
- `cite.twig`: Cite this record, with Chicago, MLA 9 and APA 7. All three are built in Twig so the citation survives with JavaScript off; the toggles only swap which is visible. Chicago is default. Copy reads Copied for two seconds.
- `print.twig`: hides header, nav, footer, sidebar, tools and buttons, drops to one column, prints the Chicago citation at the end.

All nine entry templates use them. War memorial was migrated onto them too and no longer carries its own tools row, cite box, copy script or print rules; it keeps only its unit seal box and its footer CTA.

### Fixed a live 500 on the reference page

`templates/war-memorial/_entry.twig` used `{% set v = (h) => ... %}` and then called `v('handle')`. Twig 3.21 cannot call a variable holding an arrow function, so every war memorial page was erroring with `Unknown "v" function`. It had been serving from a stale compiled template cache and broke the moment the cache cleared. Both that template and the person record now build a plain `F` dictionary of guarded field values.

### Person record

Portrait at left with an initials fallback, kicker PEOPLE with the era, occupation subtitle, BORN, DIED and RESIDENCE facts, chips for period, community and group. Main column is tools, prose, cite. Sidebar: Family and relationships (child of, parent of, sibling of, spouse of, each a 44px round thumb with occupation or life dates, plus a navy War Memorial badge when a memorial's `wmRelatedPerson` points at them), Organizations, Groups, Places, Written by, Articles about, Obituary, External. Every box conditional.

### The other seven

articles, places, organizations, groups, events, obituaries and collections rebuilt to the same pattern, keeping all batch 2 content. Places keep the location map box first. Collections keep the ordered chapter list. Articles keep the previous and next pager, the in this collection list and the author bio. Organizations are the only one of the seven whose layout has featuredImage, so they are the only one with a band portrait.

### Sub-city community boundaries

The City of Santa Clarita publishes no community or planning-area layer. Searched ArcGIS Hub and ArcGIS Online for city owned content, checked the one City of Santa Clarita Layers service (Oak Trees, General Plan, Zoning) and probed four likely city portal hostnames, none of which resolve. So the ZCTA fallback applies.

Newhall, Saugus, Valencia and Canyon Country now come from 2020 Census ZIP Code Tabulation Areas via TIGERweb: 91321, 91350, 91354 + 91355, 91351 + 91387. They replace CSA fragments that covered only the unincorporated remnant and were 8 to 31 times smaller. The city wide santa-clarita polygon is kept.

Every feature carries `properties.method`, `csa` or `zcta`, shown as a one line source note in the map tooltip and under the map on the community page. That note supersedes the fragment caveat that was planned for those four, which is no longer true of them.

Check against the county shapes: Stevenson Ranch ZCTA 91381 is within 10 percent of its CSA, which supports the method. Castaic ZCTA 91384 is about a third of its CSA, because the county area sweeps in undeveloped backcountry. Both keep their CSA polygon.

Adjacency recomputed on the new geometry and is markedly better. Newhall now neighbours Canyon Country, Placerita Canyon, Saugus, Stevenson Ranch and Valencia rather than only Santa Clarita and Stevenson Ranch.

No boundary was hand drawn. The file is 84 KB, well under the 300 KB budget.

### Community coordinates

`scripts/import/set_community_coords.php`, eval style, dry run by default behind `$APPLY`. Nathan runs it.

- 15 from the area weighted centroid of that community's polygon.
- 11 from Wikipedia, with the article URL kept beside each value: camulos, castaic-junction, fillmore, frazier-park, hasley-canyon, lebec, mentryville, pico-canyon, piru, soledad-canyon, tejon.
- 3 skipped on purpose as areas rather than points: mojave-desert, saugus-valencia, soledad-township.
- 6 left alone with no coordinates: fair-oaks-ranch, haskell-canyon, mint-canyon, potrero-canyon, ravenna, towsley-canyon. None has a polygon, and none has a Wikipedia article carrying coordinates. The USGS GNIS site is a single page app with no public API, so it could not be queried. Nothing was estimated by hand.

### Verified

- All 151 entry pages, 11 indexes and 35 community pages return 200.
- Every field handle re-audited against its entry type layout. The one out of layout read is `historicalEra` on war memorial, guarded with `is defined`.
- In the browser: cite toggles switch between the three styles with Chicago default, tools row reads "5 min read · 834 words · Updated", Listen unhides, conditional boxes render only when they have content, 15 polygons draw with 11 csa and 4 zcta, tooltips carry the source note.

### Fixed along the way

- The Listen button never appeared. The tools partial renders above the prose, so its script ran before the element it reads existed. Now bound on DOMContentLoaded.
- A rejected clipboard write no longer leaves a dangling rejection.

### Blockers

- None.

### Needs Nathan

1. Merge templates-batch-3 and templates-batch-4 to main and push, then run `project-config/apply` on Cloudways, or `/communities/{slug}` will 404 in production.
2. Run the coordinates script on **MacBook**, dry run first:

```
ddev craft exec "eval(file_get_contents('scripts/import/set_community_coords.php'))"
```

### Data problems noticed, not touched

No database changes were made.

- `wmRelatedPerson` has zero relations, so no war memorial is linked to a person. The War Memorial badge on the person record is wired and tested but cannot appear until those links exist.
- All 35 community terms still have empty body, aliases and type. The coordinates script fills coordinates only.
- The one obituary body still carries WordPress import artifacts.

### Next

- Populate community bodies, aliases and types.
- Link war memorial records to their Person records so the badge appears.
- Find coordinates for the six unresolved communities from a source with real data, ideally the GNIS domestic names file rather than the web app.

## 2026-09-17 (Claude, branches templates-batch-5 and templates-batch-6)

- Agent: Claude
- Date: 2026-09-17

### templates-batch-5, partial

Two items of the quality pass landed. The rest is outstanding, listed below.

**Legacy stylesheets stripped.** main.css went from 2131 lines to 34 and layout.css from 60 to 80. Of the 350 classes those two defined, only 19 were still referenced anywhere, all header, nav, footer, lightbox, band or map; the rest styled templates that no longer exist. Real deviations fixed in the chrome: the header carried a 2px #b8860b bottom border, the footer heading, links and tagline were still set in Inter, footer text used the old rgba(245,230,192,...) cream, and the footer column was 1280px against a 1240px header so the gutters did not line up. base.twig no longer loads the Cormorant Garamond and Inter webfonts. No occurrence of Cormorant, Inter, #1a2744 or #b8860b remains under templates/.

**Cite this record moved into the sidebar.** The brief sets the sidebar order as portrait or unit box, then Cite this record, then label/value boxes, then relations, then External. Cite had been in the main column, which is where the approved war memorial reference put it; Nathan confirmed the move. cite.twig gained a sidebar variant that renders inside a .rec-box and stacks the toggles, citation and Copy for a 340px column.

**Still outstanding from batch 5**, none of it started:

- military-profiles/_entry.twig and index.twig. The section has no template at all and is the last one missing.
- templates/404.twig, a search results template at templates/search/index.twig, and scripts/import/setup_search_route.php.
- The internal link crawl for 404s, 500s, links to the old WordPress host and stray scvhistory.com links.
- Head and metadata: title tags and meta descriptions. Nathan settled the meta description as roughly the first 160 characters of the body, cut at a word boundary.
- Community boundaries drawn on the places and organizations maps.

Note that the batch 5 brief was truncated: item 4 ended mid sentence at "drawn from the first 7" and the list jumped straight to an item numbered 7, so items 5 and 6 never arrived.

### templates-batch-6

**Fields verified.** recordImages and recordDocuments are Assets fields present on all twelve entry types: article, collection, document, event, group, militaryProfile, obituary, organization, person, photograph, place, warMemorial. Neither is on the neighborhood category group, so community pages cannot carry them. Every type also has a top and bottom editor note, and there is a third handle pair the brief did not mention: militaryProfile uses mpWebmasterNoteTop and mpWebmasterNoteBottom.

**New partials in _partials/record/:**

- `images.twig`: PHOTOS section under the prose, gold rule and label above a grid of square thumbnails, each opening the full image in a `<dialog>` lightbox with caption and credit beneath. No library. Caption from the asset title, credit from its alt.
- `documents.twig`: DOCUMENTS sidebar box, each recordDocument a link with a PDF icon, its title and its file size, opening in a new tab. Sits after the relation boxes and before External.
- `note.twig`: cream box with a 3px gold left rule, 15px text, gold links. Empty notes render nothing.

`_partials/prose.twig` now replaces a line consisting only of `[image:N]` with a figure floated right at 300px, caption and credit beneath in italic 13.5px grey, unfloated below 640px. A token pointing at an image that is not there is dropped rather than printed. images.twig reads the same tokens, so an image placed inline is left out of the Photos grid and nothing appears twice.

All nine record templates wired: top note between the tools row and the prose, prose with the images passed in, Photos section, bottom note, and the documents box in the sidebar. Each passes the note handle its own type carries.

Where featuredImage is empty or absent the first record image becomes the hero. Six of the nine templates had no portrait markup at all, since their types have no featuredImage field, so the band now takes a portrait and drops the single column modifier when there is an image.

**Verified.** All 151 entry pages, 11 indexes and 35 community pages return 200. The partials were exercised against real assets through a temporary template, since the fields hold no data: `[image:2]` produced one floated figure, `[image:9]` was dropped, the Photos grid showed the remaining three, the dialog and both notes rendered, and the documents box listed extension and size. That template was deleted before committing.

### Blockers

- None.

### Data problems noticed, not touched

No database changes were made.

- recordImages and recordDocuments have zero relations across the whole site, so no Photos section, no documents box, no inline image and no hero fallback can appear until Nathan populates them. All of it is wired and tested, just unfed.
- wmRelatedPerson still has zero relations, so the War Memorial badge on person records cannot appear.
- All 35 community terms still have empty body, aliases and type.

### Next

- Finish the batch 5 items listed above.
- Populate recordImages and recordDocuments, then re-check a record page with real photos.
- Confirm whether community terms should get recordImages and recordDocuments too; they are the only content type without them.

## 2026-09-17 (Claude, batch 5 continued, on templates-batch-6)

- Agent: Claude
- Date: 2026-09-17
- The rest of the quality pass. Stylesheet strip and Cite move had already landed.

### Missing pages

`military-profiles/_entry.twig` and `index.twig` on the record pattern. The section holds no entries, so the entry page renders against the 42 field layout: service record, life, awards, the four family groups behind their toggles, relation boxes, documents and External. Its note handles are `mpWebmasterNoteTop` and `mpWebmasterNoteBottom`, a third pair beyond the two the brief named. It has no featuredImage, so the hero comes from the first record image.

`search/index.twig` groups results by section with a thumbnail, title and subtitle per row, and searches community terms too. **No route script was needed and none was written**: Craft resolves `templates/search/index.twig` at `/search` on its own and `config/routes.php` is empty. The homepage form now targets `/search` and no longer renders results inline.

`404.twig` with the search box and a card per section with live counts. It only takes effect with devMode off; the dev environment has devMode on so Craft still shows its own debug page for a missing URL, and the template renders at `/404`.

### Head and metadata

`_partials/head/meta.twig`, included once from base.twig, gives every page a title of "record title | section | site name", a canonical, a meta description of the first 160 characters of the body cut at a word boundary, Open Graph and Twitter card. The OG image is featuredImage, then the first record image, then the site seal. Index pages set `metaDescription` at template top level, reusing the lede already on the page.

Rewrote `_partials/jsonld/person.twig`. It was stale from April and had never been included anywhere: it referenced `person.sameAs` and a roles matrix that were never built, and `person.sameAs is defined` reported true and then threw `Calling unknown method: Entry::sameAs()`. It is now built with `json_encode` rather than hand written JSON and touches only handles that exist. Wired into persons and parses on all 33 person pages.

### Link and render check

Crawled all 200 pages and every internal link. Two template level defects found and fixed.

**Eight footer links pointed at pages that do not exist**, so every page carried eight 404s: `/about`, `/contact`, `/permissions`, `/photo-credits`, `/newsletter`, `/submit`, `/nonprofit`, `/privacy`. Removed and the footer rebalanced. **These are pages still to build.**

**Legacy URLs were concatenated blindly.** The data holds three shapes: `/scvhistory/lw3730.htm`, `scvhistory.com/scvhistory/lw3730.htm`, and, where a website landed in the legacy field by mistake, `sangabrielmission.org`. The last produced `https://scvhistory.comsangabrielmission.org` on the San Gabriel mission page. `_partials/legacy-url.twig` now resolves all three, applied in all ten places.

**Left for Nathan, content level, no database changes made:**

- `organizations/mission-san-gabriel-arcangel` has a website URL (`sangabrielmission.org`) sitting in its legacy URL field. The template now renders it correctly, but the value is in the wrong field.
- One broken internal link: `/person/pedro-fages/` in the body of `articles/chapter-9-the-trail-blazer`, a WordPress era link in Leon's prose.
- Three links to the old WordPress host `wordpress-1656314-6593552.cloudwaysapps.com`: two in `obituaries/in-memoriam-henry-clay-wiley-1829-1898` and one in `persons/remi-nadeau-i`.
- Of 98 links to scvhistory.com, 96 are deliberate "View on legacy site" links. Two are inside body prose on `events/northridge-earthquake`.
- `HenryMayo.com` appears as a link host with inconsistent casing.

### Accessibility

Measured the palette rather than assuming. **Gold `#A9842B` on cream is 3.27:1**, which fails AA for the 11 to 13px labels it was used for, so gold text on light backgrounds is now `#8F6E22` as specified. A second failure the brief did not mention: **`#8A919E`, the label column in every sidebar box, is 2.97:1 on cream**; it is now `#5B6472`, already in the palette at 5.60:1.

**Worth knowing: `#8F6E22` on `#FDF7EA` measures 4.45:1, still just under the 4.5 AA threshold for text below 18.66px.** `#7A5C1B` gives 5.83:1 and clears it. Left at `#8F6E22` as instructed; say the word and it is a one line change.

Every image already carried alt, with empty alt on decorative thumbnails. Every map now has `role="img"`, an accessible name and a screen-reader sentence pointing at the text equivalent below. Added a global `:focus-visible` ring, a skip link, and made `#content` focusable.

### Performance

The webfonts were requested **up to three times per page**, from base.twig plus two partials plus six index templates. One superset request now lives in base.twig; the eight duplicates are gone. Leaflet was already conditional and loads only on pages with a map, confirmed against the actual link and script tags: three stylesheets and one script on a record page, four and two on a map page. Removed three dead `.leaflet-popup` rules that shipped to every page without a map.

### Listen player

Replaced the button in `_partials/record/tools.twig`. The record is split into sentences and spoken one at a time, which is what makes progress real: the bar fills as sentences complete, the sentence being read gets a soft cream highlight and is scrolled into view, and clicking or dragging the bar restarts from that sentence. Elapsed and estimated total start from 165 words per minute, corrected against real elapsed time as sentences finish. Speed at 0.8x, 1x, 1.25x, 1.5x. Prefers an English voice, hides itself when speechSynthesis is missing, cancels on `beforeunload` and `pagehide`. The bar is a real slider: focusable, arrows seek by a sentence, space and enter toggle. Verified on a person record: 71 sentences, estimate 5:17 for 834 words, seek to half fills the bar to 49 percent and moves the highlight.

### Community boundaries on the maps

`map-index.twig` gained an outline mode. With `outlineUrl` set, every polygon draws as a bare navy 1px outline under the pins with no fill; hover fills at 8 percent and names the community; a click goes to `?community={slug}`, the same filter the chips use. With `activeSlug` set, that community draws gold at 2px and the map fits it rather than the pins. Communities with no polygon are absent from the file so nothing is drawn. The Communities page passes no `outlineUrl` and keeps its filled treatment.

### Verified

All 151 entry pages, 15 indexes and route variants, and 35 community pages return 200.

### Left for Nathan

- Build the eight footer pages, or confirm they should stay off the site.
- The content level link problems listed above.
- Five partials are now unused and can go, but AGENTS.md says ask before deleting: `_partials/cite-article.twig` (0 bytes), `_partials/search-form.twig`, `_partials/sidebar/external.twig`, `_partials/sidebar/location.twig`, `_partials/sidebar/related-list.twig`. `_partials/sidebar/box.twig`, `cite.twig` and `meta.twig` are still used by the communities page.
- Decide on `#8F6E22` versus `#7A5C1B` for small gold text on cream.
- `recordImages`, `recordDocuments` and `wmRelatedPerson` still have zero relations sitewide.

## 2026-09-17 (Claude, branch templates-batch-7)

- Agent: Claude
- Date: 2026-09-17

### Small-text gold now clears AA

`#8F6E22` measured 4.45:1 on cream, short of the 4.5 AA threshold for text under 18.66px. Small gold text is now `#7A5C1B`: 5.83:1 on cream, 6.22:1 on white, 5.81:1 on the page grey.

The swap was decided per declaration block, reading the font-size in the same rule, plus the six inline link colours in the empty-state messages, which sit in 16px text. Four display uses keep `#8F6E22`, all Playfair numerals at 22 to 30px where AA only asks 3.0: the era and feature numerals on the homepage, the article list numeral, and the collection chapter numeral.

### Pages section

`scripts/import/setup_pages_section.php`, eval style, dry run behind `$APPLY`, safe to run twice. **Nathan runs it.** It creates:

- section `pages`, channel, uriFormat `{slug}`, template `pages/_entry`
- entry type `page` with body, webmasterNoteTop, webmasterNoteBottom, featuredImage, recordImages, recordDocuments, all six verified present
- the eight entries with empty bodies: About, Contact, Permissions, Photo Credits, Newsletter, Submit a Photo or Article, Nonprofit, Privacy Policy

No prose is written by the script. The copy is Nathan's to write in the control panel.

`templates/pages/_entry.twig` on the record pattern with no relation boxes, since a page is prose rather than a record with connections: cream band, tools, top note, prose, images, bottom note; sidebar of cite, a search box and the other pages. It shows "This page has not been written yet" while a body is empty.

### Footer restored

Back to the original three columns and the bottom bar: Browse (By Era, By Collection, People, Obituaries), About (About, Contact, Permissions, Photo Credits, SCV Historical Society), Connect (Facebook Group, Newsletter, Submit a photo/article), and Nonprofit, Permissions, Privacy Policy along the bottom.

By Era and By Collection needed somewhere real to point, so the articles index gained a `view` parameter. `view=era` groups the same articles by historical era, `view=collection` keeps the existing per series grouping, and a GROUP BY chip row switches between them. Verified: `?view=era` renders Spanish Colonial, Mexican Rancho Era, American Frontier and Other articles as group headings.

**The eight page links 404 until Nathan runs the section script.** That is expected and resolves on the first run.

### Unused partials deleted

Confirmed zero references for each, then removed: `_partials/cite-article.twig` (an empty file), `_partials/search-form.twig`, `_partials/sidebar/external.twig`, `_partials/sidebar/location.twig`, `_partials/sidebar/related-list.twig`. `sidebar/box.twig`, `cite.twig` and `meta.twig` stay, since the communities page still uses them.

### Content link fixes

`scripts/import/fix_bad_links.php`, eval style, dry run behind `$APPLY`. **Nathan runs it.** Every rewrite is anchored on the exact stored URL, so a second run is a no-op.

All four link targets were checked against Craft before the script was written, and all four exist:

- `/person/pedro-fages/` in `articles/chapter-9-the-trail-blazer` to `/persons/pedro-fages`
- `wordpress-1656314.../article/henry-clay-wiley/` to `/articles/henry-clay-wiley`
- `wordpress-1656314.../article/surveyors-map-showing-lyons-station/` to `/articles/surveyors-map-showing-lyons-station`
- `wordpress-1656314.../person/tiburcio-vasquez/` to `/persons/tiburcio-vasquez`

The strip-the-anchor-keep-the-text path is implemented for targets that do not exist, but none of these four needs it.

The script also moves `sangabrielmission.org` out of the legacy URL field on `organizations/mission-san-gabriel-arcangel` and into `orgWebsite`, clearing the legacy field. It leaves `orgWebsite` alone if something is already there.

The two scvhistory.com links in `events/northridge-earthquake` are reported and left untouched, since a reference to the legacy site may well be deliberate. **Nathan decides.**

### Verified

All 151 entry pages, 16 index and route variants, and 35 community pages return 200.

### Needs Nathan

Two scripts to run on **MacBook**, both dry run first, then with `$APPLY = true`:

```
ddev craft exec "eval(file_get_contents('scripts/import/setup_pages_section.php'))"
ddev craft exec "eval(file_get_contents('scripts/import/fix_bad_links.php'))"
```

Then `project-config/write` and commit `config/project/` for the first one. Write the copy for the eight pages, and decide on the two Northridge legacy links.

### Still open

- `recordImages`, `recordDocuments` and `wmRelatedPerson` have zero relations sitewide, so photos, documents and the war memorial badge stay wired but unfed.
- Community terms still have empty body, aliases and type.

## 2026-09-17 (Claude, templates-batch-7 follow-up)

- Agent: Claude
- Date: 2026-09-17

### fix_bad_links.php crashed; guarded and re-verified

It threw from `Element->normalizeFieldValue()` and died before applying anything. It walked a fixed list of ten text handles and called `getFieldValue()` for each on every entry, so the first entry whose layout did not carry one of them killed the run.

Field access is now guarded twice, the way `import_wp_media.php` and `clean_bodies.php` do it: the handle is checked against that entry's own field layout first, and every get and set is still wrapped in try/catch. `setFieldValues` and `saveElement` are wrapped too, so a failure on one entry reports and moves on.

The dry run prints all five planned changes, the two Northridge links it leaves alone, and a per entry list of handles skipped for not being on that layout. Those skipped handles are exactly what crashed the first version: `wmNarrative`, `mpNarrative`, `authorBio` and the three note variants belonging to other entry types.

**Also fixed a hazard that was not reported:** replacements used `$target->getUrl()`, which bakes the current environment's hostname into stored content. Run locally, it would have written `scvhistory.ddev.site` links into bodies that then sync to production. Replacements are now root relative.

Idempotent on a second run, which matters because `$APPLY` is already true in Nathan's copy: each rewrite is anchored on the exact stored URL, so once fixed the pattern no longer matches, and once the legacy field is cleared there is no bare domain to move. A second run plans zero and says so.

Verified by running the dry run to completion: plans 5, writes 0.

### Pages verified

All eight pages return 200: `/about`, `/contact`, `/permissions`, `/photo-credits`, `/newsletter`, `/submit`, `/nonprofit`, `/privacy`. All 13 internal footer links return 200, including the two grouping links `/articles?view=era` and `/articles?view=collection`. Nothing 404s. A page with an empty body renders the record shell and says "This page has not been written yet".

## 2026-09-17 (Claude, branch templates-batch-8)

- Agent: Claude
- Date: 2026-09-17

### Article page rebuilt to the approved layout

`templates/articles/_entry.twig`, modelled on the Reynolds Prologue page.

Band: breadcrumbs, title in Playfair gold, collection title beneath it in gold and linked, byline of "By X · Edited by Y · date" with both names linked, chip row of the historical period and every community term.

Body column: featured image as a full-width hero with its caption beneath in italic 13.5px grey from the asset title; a previous/next bar with Jost gold labels above the chapter titles; the prose; a right-aligned author and year in small caps; the same bar again; then ABOUT THE AUTHOR with a round portrait, name, occupation, bio and a View Full Profile link.

Sidebar: tools with the listen player, cite, search, PART OF COLLECTION with the collection's featured image, IN THIS COLLECTION with every chapter in order and the current one on cream behind a gold left rule, PEOPLE IN THIS ARTICLE with thumbnails and occupations, PUBLISHED BY, the remaining relation boxes, documents, and the legacy site link.

Previous, next and the chapter list all read the same `articlesInCollection` order, so they cannot disagree. Every block is conditional: no hero without an image, no bars outside a collection, no author block without a bio, no sign-off without a four-digit year. Built on the existing record partials for tools, cite, note, images, documents and prose.

**One block could not be built: TAGS.** There is no tags field on the article layout, and no tag or keyword field anywhere in this Craft install. The band's chips carry the historical period and the communities instead. If tags are wanted, the field has to be created first.

`featuredImage` is now on the article layout, so the hero is a real featured image; it falls back to the first record image when empty. 50 of the 84 collection-linked articles carry one.

Verified: all 27 article pages return 200, and on the Prologue page the hero, both prev/next bars, the sign-off, the author block, the collection card and the chapter list with the current chapter highlighted all render.

## 2026-09-17 (Claude, branch templates-batch-9)

- Agent: Claude
- Date: 2026-09-17
- Two briefs landed under this batch number: tags, and community editing support. Both are here.

### Tags

`scripts/import/setup_tags.php`, eval style, dry run behind `$APPLY`, safe to run twice. **Nathan runs it.** It creates the `tag` category group (name Tags, uriFormat `tags/{slug}`, template `tags/_entry`, on every site), a Categories field `recordTags` pointing at it, and adds that field to the Content tab of all thirteen entry types. It creates no terms.

`templates/tags/index.twig` is a cloud sized by record count in five steps, so one very common tag cannot flatten the rest. `templates/tags/_entry.twig` lists every record carrying the tag, grouped by section, each row a thumbnail with title and subtitle.

`_partials/record/tags.twig` is the sidebar box: plain chips linking to the tag page, wired into all eleven entry templates that have a sidebar plus the community page, sitting after the relation and document boxes and before External. `documents/_entry.twig` and `photographs/_entry.twig` are still bare fifteen-line stubs with no sidebar, so they were left alone.

Nothing here needs the taxonomy to exist. `/tags` renders and says the taxonomy has not been created yet.

### I broke the site and committed it

The first version of the tags box used `entry.recordTags is defined`. That is not a safe test on an Entry: it reports true even when the field does not exist, Twig then calls `recordTags()`, and Craft throws `Calling unknown method`. Since `recordTags` does not exist until the script runs, **that took out all 151 entry pages and the eight site pages, and I committed it before the sweep finished.**

Fixed in the next commit. The only reliable test is the element's own field layout, which is what the import scripts already do, so the partial now takes the element and does that check itself. This is the third time this exact trap has bitten: `person.sameAs` in the JSON-LD partial, `recordImages` on Category elements, and now this. **`is defined` is not a safe guard for a Craft custom field. Check the field layout.**

### Community editing support

`scripts/import/add_community_media.php`, eval style, dry run behind `$APPLY`. **Nathan runs it.** It puts `recordImages` and `recordDocuments` on the Communities category group, which is the only content type in the archive without them. Both fields already exist, so it only touches the group's field layout.

`templates/communities/_entry.twig` now renders like an entry page: it pulls in `_partials/record/css` alongside `scv-extra-css`, passes `recordImages` to the prose so `[image:N]` resolves, and adds the Photos section, both editor note partials, the documents box and the tags box. `communityType` moves out of the facts row and becomes a chip in the band.

### Community field coverage, as asked

Of the 35 terms, **26 carry coordinates and nothing else**. Not one has a body, an alias, a type or a cultural sensitivity note. So the new type chip and the note partials have nothing to show yet, and the map is still the only thing on those pages besides the related records.

The nine with no field content at all: `fair-oaks-ranch`, `haskell-canyon`, `mint-canyon`, `mojave-desert`, `potrero-canyon`, `ravenna`, `saugus-valencia`, `soledad-township`, `towsley-canyon`. Those are the six that had no coordinate source in batch 4 plus the three skipped as areas rather than points.

### Verified

All 151 entry pages, 22 index and route variants including `/tags`, the eight site pages, and all 35 community pages return 200.

### Needs Nathan

Two scripts on **MacBook**, dry run first, then `$APPLY = true`:

```
ddev craft exec "eval(file_get_contents('scripts/import/setup_tags.php'))"
ddev craft exec "eval(file_get_contents('scripts/import/add_community_media.php'))"
```

Then `project-config/write` and commit `config/project/`. After that, add tag terms and start filling community bodies, aliases and types.

## 2026-09-17 (Claude, templates-batch-9, On This Day)

- Agent: Claude
- Date: 2026-09-17

### Calendar index

`scripts/import/build_calendar_index.php` walks every record's `recordDates`, keeps only rows ticked Confirmed whose precision is `day` or `month`, and writes `templates/_data/calendar.json` keyed by MM-DD. Each entry holds title, a root-relative url, section, ISO and printed dates, label, and the featuredImage url where one exists. Same generated-file pattern as `community-neighbors.json`, read with `source()` rather than queried per request.

Year and circa rows are left out deliberately: a year has no day to file it under, and an approximate date would place a record on a calendar day the source does not claim.

It reads only. It never writes to the database and never modifies a `recordDates` row. Rerun after each round of confirmations; it rewrites the file whole.

**A trap worth recording:** Craft returns the table's date column in the site timezone, and for a date-only value that rolls the day backwards. June 19, 1874 came back as `1874-06-18 16:07:02 America/Los_Angeles`. The builder converts to UTC before taking MM-DD, so the day matches what is printed in the source.

### The page

`templates/on-this-day/index.twig` shows today by default and any day via `?d=MM-DD`, with a month strip and a day strip marking which days hold entries. An empty day says so plainly and offers the nearest day that has entries, wrapping around the year end. Cards carry the image, the date as printed, the label, the section and a link; with no image the year stands in.

The homepage gains an "On this day" block of up to three of today's entries, hidden entirely when there are none. On This Day joins the footer Browse column.

### Current state, as asked

**Zero confirmed rows.** 111 records carry 432 proposal rows: 167 day, 36 month, 228 year, 1 circa. None is ticked Confirmed, so `calendar.json` is valid and empty, `/on-this-day` says no dates have been confirmed yet, and the homepage block does not render. Once rows are confirmed, rerun the builder and both appear.

### Verified

Rendering was checked against a temporary fixture: a populated day rendered four cards oldest-first with image and year fallbacks, a month-precision row rendered, an empty day offered the nearest, and the homepage block appeared with its "All 4 records for today" link. The fixture was deleted and the real generated file restored before committing.

All 158 record pages, 18 index and route variants including `/on-this-day` and bogus `?d` values, and all 35 community pages return 200.

`/places/sleepy-valley` now 404s because the entry was deleted from the database, which resolves one of the data problems flagged in batch 1. My URL list was stale, not the site.

### Note

`git add -A` briefly swept four of Nathan's untracked files into my commit: `apply_confirmed_dates.php`, `export_unconfirmed_dates.php` and `web/review/dates.{html,json}`. The commit was undone and remade with only my five files; his remain untracked and untouched.

## 2026-09-17 (Claude, templates-batch-9, entity reconciliation)

- Agent: Claude
- Date: 2026-09-17
- Built to the shape of the date workflow already in the repo: a read-only export to `web/review`, a standalone screen with localStorage and a download, and an apply script that is dry run by default.

### export_entity_candidates.php

Read only. Emits every Person, Place and Organization with id, section, title, slug, alias, mention count (from the relations table) and url, then pairs them **within a section** four ways:

- **normalised** — titles match once punctuation, accents, honorifics and a leading "the" are stripped
- **initials** — same surname, one side's initials expand to the other's given names, so "H.M. Newhall" meets "Henry Mayo Newhall"
- **substring** — one title inside the other at a word boundary, so "Newhall" meets "Henry Mayo Newhall"
- **surname** — same surname, different given names

Each pair carries both ids, both titles, the reason and a confidence of high, medium or low. It merges nothing and writes nothing to the database. The better-attested record is offered as the survivor first.

### Counts against the current data

33 Persons, 15 Places, 14 Organizations. **4 candidate pairs, all medium, all "same surname, different given names":**

| | |
|---|---|
| Juventino del Valle | Antonio del Valle |
| Ygnacio del Valle | Juventino del Valle |
| Ygnacio del Valle | Antonio del Valle |
| Rodolfo Acosta | Dante Acosta |

**All four are genuinely different people** — del Valle relatives and an Acosta father and son. That is the workflow behaving correctly: it proposes, a person rejects. There are no high-confidence pairs in the current data, which is expected at 62 hand-curated records; the matching earns its keep against thousands of extracted candidates.

### entities.html

Each pair side by side with facts and links, asks which title survives, offers Merge, Not the same and Skip. Filters by section and confidence. Keyboard: `M` merge, `N` not the same, `S` skip, `1`/`2` choose the survivor, `J`/`K` move. Decisions in localStorage, downloads `merged.json`.

### apply_entity_merges.php

Moves every relation pointing at the loser onto the survivor, appends the losing title to the survivor's alias field where the type has one, and deletes the loser, inside a transaction per pair. Dry run by default behind `$APPLY`.

Guards, since this is the destructive half: it refuses to merge a record with itself; it skips a pair where either record is gone, which is what makes a second run a no-op; it drops rather than duplicates a relation the survivor already holds; and it will not append an alias twice. The dry run prints which fields the relations come through, with counts, before anything moves.

### Persons have no alias field

The type carries only `fullName`, which is the canonical name rather than a list of other names. Export, screen and apply script all use the same section-to-alias map, so the screen never offers to record a title the apply step cannot store, and the card says so. **Merging two people therefore loses the losing title.** If that matters, a `personAliases` field would need creating first.

### Verified

A temporary fixture covered a place merge, an organization merge, a person merge with no alias field, a self-merge, a missing record and a `notsame` row. The dry run reported 3 merges and 2 skipped, named the relation fields and counts (for example Rancho Camulos would take 61 relations through `placeOrganizations`, `personOrganizations` and `subjectOrganization`, dropping 6 duplicates), moved nothing, and the record counts were identical afterwards. Fixture deleted.

`web/review/entities.json` and `merged.json` are added to `.gitignore`, matching `dates.json` and `confirmed.json`.

### Needs Nathan

```
ddev craft exec "eval(file_get_contents('scripts/import/export_entity_candidates.php'))"
```

then open `https://scvhistory.ddev.site/review/entities.html`, decide, download `merged.json` into `web/review/`, and run `apply_entity_merges.php` dry first.
