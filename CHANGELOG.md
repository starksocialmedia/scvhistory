SCVHistory.com — Changelog

2026-09-15

- Agent: Grok Build
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
- Decisions: none built. Schema changes wait for Nathan/Leon review
- Blockers: Questions for Leon in CONTENT-MODEL.md
- Next: Nathan and Leon review CONTENT-MODEL.md. Task 3 only after approval. No Craft apply yet

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
