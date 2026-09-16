SCVHistory.com — Changelog

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
