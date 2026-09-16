SCVHistory.com — Changelog

2026-09-15

- Agent: Grok Bot
- Done: applied verified non-Indigenous Wikidata/LCSH URI corrections in taxonomy-import/ (wars, disasters, subjects, place/org/group types, water types); left spanish-colonial-expedition Wikidata as Portola expedition Q3966440 after live check
- Decisions: left AAT-only failures as NEEDS_VERIFICATION; did not invent AAT replacements; Indigenous terms from 7fcb8d3 left untouched
- Blockers: none
- Next: wait for Nathan before further taxonomy edits

2026-09-15

- Grok Bot (research): applied Indigenous-first verified Wikidata URI fixes from TAXONOMY-AUDIT.md (Nathan approved)
- subject-tags.json: Tongva placeholder -> Q1479279; Serrano Q745474 -> Q617532; Kitanemuk Q6422453 -> Q6417841; Vanyume Q7915068 -> Q11216568; Tataviam language Q743736 -> people Q1562200 (tataviam-history + indigenous-history)
- historical-era.json: tataviam-pre-contact Wikidata -> Q1562200; tribal site URLs kept
- Chumash Q24251468 unchanged (already correct)
- Non-Indigenous URI corrections still pending Nathan approval
- Next: Nathan review remaining TAXONOMY-AUDIT corrections; decide era gap 1990-2010 with Leon; push when ready

2026-09-15

- Grok Bot (research): audited all taxonomy-import/*.json against live Wikidata, Getty AAT, and LCSH
- Wrote TAXONOMY-AUDIT.md (tables per file + Questions for Leon); JSON not edited pending Nathan approval
- Failures: Tongva placeholder still present (verified fix Q1479279); Serrano Q745474 is an asteroid (fix Q617532); Kitanemuk Q6422453 and Vanyume Q7915068 wrong (fix Q6417841 / Q11216568); Tataviam Q743736 is language not people (prefer Q1562200); historical-era gap 1990-2010; many war/disaster/subject Wikidata QIDs and AAT/LCSH IDs point to unrelated concepts
- Next: Nathan review TAXONOMY-AUDIT.md; approve JSON URI corrections before any edits; then push grok-bot if wanted

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
