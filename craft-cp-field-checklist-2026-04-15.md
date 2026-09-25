# SCVHistory.com — Exact Craft CP Field Creation Checklist
**Date:** 2026-04-15
**Purpose:** Complete these steps **before** any further content entry or bulk imports.
**Time estimate:** 45–60 minutes
**Environment:** Cloudways Craft CP (or local DDEV)
**After completion:** Run `ddev craft clear-caches/all` (local) and clear caches on Cloudways.

---

## Craft 5 Notes Before Starting

**Conditional fields** do not use nested groups in Craft 5. Use field conditions in the layout editor:
- Add the lightswitch field (e.g. `hasMilitaryService`)
- Add each dependent field individually to the entry type layout
- In the layout editor, click each dependent field → set condition: "Show when [lightswitch] is enabled"
This applies to: hasMilitaryService, allegedlyHaunted, waterInfrastructure conditionals.

**Matrix field (Roles)** — create a Matrix field named `roles`, one block type named "Role",
sub-fields inside the block type: roleTitle, roleOrganization, roleStart, roleEnd, primaryRole.
Draggable sort order is native to Matrix.

---

## Step 0 – Prerequisites
- [ ] Log into the Craft Control Panel (Cloudways or local DDEV)
- [ ] Go to Settings → Fields
- [ ] Go to Settings → Categories

---

## Step 1 – Global Asset Fields (REQUIRED for WCAG)
Create these Plain Text fields and attach to the Asset field layout (apply to all asset volumes):

- [ ] `altText` — Plain Text → **Required** → Label: "Alt Text (WCAG)"
- [ ] `licenseType` — Plain Text → Label: "License / Copyright Status"
- [ ] `creditLine` — Plain Text → Label: "Credit Line"
- [ ] `permissionsNotes` — Plain Text → Label: "Usage Restrictions"

---

## Step 2 – New Taxonomies (Settings → Categories)

| Taxonomy Handle | Name in CP | Terms to Add |
|---|---|---|
| `orgSubtype` | Organization Subtype | educational-institution, rancho, mission, government, business, nonprofit, media, military-unit, cultural, religious-institution |
| `placeType` | Place Type | road, park, building, canyon, school, neighborhood, landmark, body-of-water, ranch, cemetery, mine, oil-field, railway-station |
| `groupType` | Group Type | family, cultural-group, informal-association, expedition-party, military-unit-informal |
| `subjectTags` | Subject Tags | gold-rush, water-history, indigenous-history, railroads, oil-industry, film-industry, agriculture, architecture, military-history, politics-government, law-enforcement, education, religion, commerce-trade, land-grants, natural-disasters, conservation, arts-culture |
| `confidenceLevel` | Confidence Level | confirmed, probable, uncertain, disputed |
| `disasterType` | Disaster Type | fire, flood, earthquake, dam-failure, landslide, drought, industrial-accident |
| `conflict` | Conflict | world-war-i, world-war-ii, korean-war, vietnam-war, gulf-war, iraq-war-oif, afghanistan-war-oef, civil-war, mexican-american-war, spanish-american-war |
| `waterType` | Water Infrastructure Type | dam, reservoir, aqueduct, canal, spring, well, treatment-plant |

---

## Step 3 – Add Fields to Entry Types

### PERSON
- [ ] Lightswitch → `hasMilitaryService` (Label: "Has Military Service")
- [ ] Copy all mp* fields from Military Profile section into Person layout
- [ ] Set field condition on each mp* field: show when hasMilitaryService is enabled
- [ ] Lightswitch → `hasFirstResponderService`
- [ ] Plain Text → `firstResponderRole` (conditional on hasFirstResponderService)
- [ ] Relation → Organization → `firstResponderAgency` (conditional)
- [ ] Plain Text → `firstResponderStart` (conditional)
- [ ] Plain Text → `firstResponderEnd` (conditional)
- [ ] Rich Text → `firstResponderNotes` (conditional)
- [ ] Matrix → `roles` (replace plain text occupation — block type: Role, sub-fields: roleTitle, roleOrganization, roleStart, roleEnd, primaryRole lightswitch)
- [ ] Lightswitch → `isLivingPerson`
- [ ] Plain Text → `specificYear`
- [ ] Plain Text → `onThisDayDate` (help text: "MM-DD format e.g. 03-12")
- [ ] Plain Text → `onThisDayNote` (help text: "Hook line e.g. On this date in 1876...")
- [ ] Relation → Organization → `elementarySchool`
- [ ] Relation → Organization → `middleSchool`
- [ ] Relation → Organization → `highSchool`
- [ ] Relation → Organization (multi) → `college`
- [ ] Plain Text → `classOf`
- [ ] Category (multi) → `subjectTags`
- [ ] Category → `confidenceLevel`

### ORGANIZATION
- [ ] Category → `orgSubtype`
- [ ] Plain Text → `dateDissolved`
- [ ] Plain Text → `gradeRange` (conditional on orgSubtype = educational-institution)
- [ ] Plain Text → `schoolMascot` (conditional)
- [ ] Relation → Yearbook (multi) → `yearbooks` (conditional — add after Yearbook section created)
- [ ] Category (multi) → `subjectTags`
- [ ] Plain Text → `onThisDayDate` (MM-DD — for founding date On This Day)
- [ ] Plain Text → `onThisDayNote`

### PLACE
- [ ] Category (multi) → `placeType`
- [ ] Rich Text → `body` (if missing)
- [ ] Asset → `featuredImage` (if missing)
- [ ] Relation → Person (multi) → `namedAfter`
- [ ] Plain Text → `namedAfterNotes`
- [ ] Plain Text → `namedAfterDate`
- [ ] Lightswitch → `allegedlyHaunted`
- [ ] Rich Text → `hauntedNotes` (conditional on allegedlyHaunted)
- [ ] Plain Text → `hauntedSources` (conditional on allegedlyHaunted)
- [ ] Lightswitch → `waterInfrastructure`
- [ ] Category → `waterType` (conditional on waterInfrastructure)
- [ ] Plain Text → `capacityNotes` (conditional)
- [ ] Plain Text → `constructionDate` (conditional)
- [ ] Relation → Organization → `operatorOrg` (conditional)
- [ ] Plain Text → `specificYear`
- [ ] Plain Text → `onThisDayDate`
- [ ] Plain Text → `onThisDayNote`
- [ ] Category (multi) → `subjectTags`
- [ ] Category → `confidenceLevel`
- [ ] Plain Text → `placeBounds` (bounding box for areas/regions)
- [ ] URL → `iiifManifestUrl`
- [ ] Lightswitch → `iiifHostedOnArchiveOrg`
- [ ] Plain Text → `iiifArchiveOrgIdentifier`

### EVENT
- [ ] Rich Text → `body`
- [ ] Asset → `featuredImage`
- [ ] Category (multi) → `historicalEra`
- [ ] Category (multi) → `historicalPeriod`
- [ ] Category (multi) → `neighborhood`
- [ ] Category (multi) → `subjectTags`
- [ ] Category → `disasterType`
- [ ] Number → `casualtyCount` (conditional on disasterType)
- [ ] Plain Text → `propertyDamageNotes` (conditional)
- [ ] Plain Text → `specificYear`
- [ ] Plain Text → `onThisDayDate`
- [ ] Plain Text → `onThisDayNote`
- [ ] Category → `confidenceLevel`
- [ ] Relation → Organization (multi) → `responseOrganizations`

### ARTICLE
- [ ] Relation → Person (multi) → `subjectPersons` (explicit bidirectional)
- [ ] Relation → Organization (multi) → `subjectOrganizations` (explicit bidirectional)
- [ ] Relation → Place (multi) → `subjectPlaces` (explicit bidirectional)
- [ ] Relation → Event (multi) → `subjectEvents` (explicit bidirectional)
- [ ] Category (multi) → `subjectTags`
- [ ] Category → `confidenceLevel`
- [ ] Plain Text → `specificYear`
- [ ] Plain Text → `onThisDayDate`
- [ ] Plain Text → `onThisDayNote`

### GROUP
- [ ] Category (single) → `groupType`
- [ ] Rich Text → `body`
- [ ] Asset → `featuredImage`
- [ ] Category (multi) → `historicalEra`
- [ ] Category (multi) → `historicalPeriod`
- [ ] Category (multi) → `neighborhood`
- [ ] Category (multi) → `subjectTags`

### OBITUARY
- [ ] Relation → Group (multi) → `obitGroups`
- [ ] Rename / clarify existing `obitRelatedPersons` label to "Family Members Named"

---

## Step 4 – New Sections to Create

### YEARBOOK (New Section)
- [ ] Create channel section: handle `yearbooks`, template `yearbooks/_entry`
- [ ] Fields:
  - Title (required)
  - Relation → Organization (required) → `yearbookSchool`
  - Number (required) → `yearbookYear`
  - Asset → `featuredImage`
  - Rich Text → `body`
  - URL → `archiveUrl`
  - Plain Text → `iiifArchiveOrgIdentifier`
  - Relation → Person (multi) → `yearbookStudents`
  - Category (multi) → `historicalEra`, `historicalPeriod`, `subjectTags`

### PHOTOGRAPH (New Section — High Priority)
- [ ] Create channel section: handle `photographs`, template `photographs/_entry`
- [ ] Fields per DATA_MODEL.md: photoSourceCode, photoSequence, photoDate, photographer (Person), photoCaptionExt, photoCredit, photoSources (Org multi), archiveUrl, iiifArchiveOrgIdentifier, body, historicalEra, historicalPeriod, neighborhood, subjectTags, photoPeople (Person multi), photoPlaces (Place multi), photoOrganizations (Org multi), photoEvents (Event multi), photoArticles (Article multi), photoGroups (Group multi)
- [ ] Help text on photoSourceCode: "Two-letter donor prefix e.g. JR, HM"
- [ ] Help text on photoSequence: "Four-digit sequence number e.g. 0042"

### WAR MEMORIAL (New Section)
- [ ] Create channel section: handle `warMemorial`, template `war-memorial/_entry`
- [ ] Fields:
  - Title (required) — person's name
  - Relation → Person (optional) → `memorialSubject` (link if Person record exists)
  - Category (required) → `conflict`
  - Plain Text → `killedInActionDate`
  - Plain Text → `killedInActionLocation`
  - Rich Text → `memorialNotes`
  - Asset → `featuredImage`
  - Plain Text → `onThisDayDate` (KIA date in MM-DD for On This Day)

### AWARD (New Section)
- [ ] Create channel section: handle `awards`, template `awards/_entry`
- [ ] Fields:
  - Title (required) — award name
  - Relation → Organization → `awardingOrganization`
  - Plain Text → `yearEstablished`
  - Rich Text → `body`
  - Asset → `featuredImage`
  - Relation → Person (multi) → `awardRecipients`
  - (Recipients need year — consider Matrix for recipients: person + year)

### ANNOTATION (New Section — For IIIF)
- [ ] Create channel section: handle `annotations`, template none (internal use only)
- [ ] Fields:
  - URL → `annotationTarget` (Canvas/image URL)
  - Plain Text → `annotationRegion` (xywh bounding box)
  - Relation → Person → `annotatedPerson`
  - Relation → Place → `annotatedPlace`
  - Relation → Organization → `annotatedOrg`
  - Plain Text → `annotationNote`
  - Number → `recognitionConfidence`
  - Plain Text → `recognitionMethod` (manual, face-recognition, ocr-entity-extraction)
  - Lightswitch → `annotationConfirmed`

---

## Step 5 – Quick Validation
- [ ] Person entry: confirm hasMilitaryService toggle shows/hides military fields
- [ ] Person entry: confirm roles Matrix field accepts blocks with sub-fields
- [ ] Place entry: confirm allegedlyHaunted toggle shows hauntedNotes
- [ ] Create one test Yearbook entry — confirm all fields present
- [ ] Create one test Photograph entry — confirm donor code help text visible
- [ ] Verify all new taxonomies appear in correct field layouts
- [ ] Run `ddev craft clear-caches/all` locally
- [ ] Clear caches on Cloudways
- [ ] Test that altText is enforced as required on new asset upload

---

## Next Actions After Checklist Complete
1. Military Profile migration script — copy mp* fields to Person, set hasMilitaryService = true, delete Military Profile records
2. Pull Cloudways DB to local DDEV
3. Build organizations/index.twig template
4. Start import pipeline: scan_inventory.py against Leon's HTML archive

---
*Derived from DATA_MODEL.md and BUILDPLAN.md (2026-04-15). No credentials; the repo (starksocialmedia/scvhistory) is private.*

---

## Amendment — 2026-04-15

### culturalSensitivityNote — Add to All Entry Types and Category Layouts

Field created on Cloudways (field ID 174, UID f0a5c7de-ccc3-4005-84ee-53feaafc26e9).
Must be added to field layouts manually in CP.

**Add to entry type layouts:**
- [ ] Person
- [ ] Place
- [ ] Organization
- [ ] Group
- [ ] Event
- [ ] Article
- [ ] Photograph
- [ ] Yearbook
- [ ] Oral History
- [ ] Document
- [ ] War Memorial

**Add to Category field layout (so individual taxonomy terms carry their own note):**
- [ ] Settings → Categories → each group → Field Layout → drag in culturalSensitivityNote

**Instructions text for the field:**
"Note any CARE Principles or PNAAM considerations. Example: 'Developed in consultation with Fernandeño Tataviam Band of Mission Indians' or 'Chumash content — respect Santa Ynez Band of Chumash Indians.'"

### JSON-LD Person Template
Created at: templates/_partials/jsonld/person.twig
Include in persons/_entry.twig head block:
{% include '_partials/jsonld/person' with { entry: entry } only %}

### URI Audit Corrections (Do Not Override These)
- water-history: correct URI is https://id.loc.gov/authorities/subjects/sh85145505 (LCSH water supply)
- land-grants: correct URIs are https://www.wikidata.org/wiki/Q1000743 and https://id.loc.gov/authorities/subjects/sh85074242
- Q6683 is the Mexican-American War — do NOT use for land-grants
- Q134166 is reservoir — do NOT use for water-history
- These are already correct in the committed subject-tags.json — do not replace

