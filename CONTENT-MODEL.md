# SCVHistory.com content model

Task 2 of HANDOFF.md. For Nathan and Leon to review **before** anything is built in Craft. This file is not a schema change.

Live source of truth: `config/project/` as of 2026-09-15. `DATA_MODEL.md` (2026-04-15) is older and aspirational. Where the two disagree, the live yaml wins and this file says so.

Inventory source: `INVENTORY.md` (Jordy scan, 2026-09-15).

## Settled decisions (do not relitigate)

- Roles Matrix replaces plain-text occupation on Person. The Matrix is **not built yet**. Occupation is still a Plain Text field. This document does not redesign that.
- Military Profile is to be merged into Person as a conditional field group. The Military Profiles section **still exists**. This document does not redesign that merge.
- Import is entity-first: canonical Persons, Places, Organizations, and taxonomies before articles.
- Taxonomy terms need verified linked-data URIs (Wikidata, AAT, LCSH). Never invent a URI; mark `NEEDS_VERIFICATION`.
- Follow `TATAVIAM_AUDIT.md` for Tataviam, Chumash, Tongva, Serrano, Kitanemuk, and Vanyume.

Nathan, 2026-09-15 (no Leon gate for ingest mapping):

- Skip indexes, empty pages, and flipbook HTML
- Object pages to photographs
- `files/` packages to documents
- Remainder HTML to articles
- War memorial profiles to persons
- Mentryville is one Organization plus one Place

## 1. Live schema

Nine sections. 174 fields. One volume (`archiveMedia`). Three category groups (`historicalEra`, `historicalPeriod`, `neighborhood`). No Photograph section. No Document section.

`body` is multiline Plain Text on every entry type, not CKEditor. `featuredImage` is a single image Asset on `archiveMedia` (max 1). It is on Person and Organization layouts only.

Prefixed `*LegacyUrl` fields exist per type. Global `legacyKey` and `legacyUrl` exist as field definitions; they are on Article (and `legacyUrl` on Collection) only. `sourcePath`, `legacyHtml`, and `legacyCategory` do not exist.

JSON files in `taxonomy-import/` (subject-tags, place-type, org-subtype, group-type, confidence-level, and others) are **not** installed as Craft category groups.

None of the custom fields below are required in the live layouts. Craft `title` is the only required element.

### 1.1 Persons (channel)

Handle `persons`. URI `persons/{slug}`. Template `persons/_entry`. Entry type `person`. Title format `{fullName}`. HANDOFF: 33 Person entries already imported.

| Handle | Type | Relates to | Notes |
| --- | --- | --- | --- |
| featuredImage | Assets (max 1) | archiveMedia | |
| body | Plain Text (multiline) | | Public biography |
| fullName | Plain Text | | Feeds the title |
| birthDate | Plain Text | | Flexible dates |
| birthplace | Plain Text | | |
| deathDate | Plain Text | | |
| burialPlace | Plain Text | | |
| occupation | Plain Text | | Settled to become Roles Matrix. Still live as text |
| authorBio | Plain Text | | |
| spouseOf, parentOf, childOf, siblingOf, relatedPersons | Entries | Persons | Family and general links |
| personOrganizations | Entries | Organizations | |
| personGroups | Entries | Groups | |
| personEvents | Entries | Events | |
| articlesAbout | Entries | Articles | |
| personObituaries | Entries | Obituaries | |
| relatedMilitary | Entries | Military Profiles | Bridge to the section that is supposed to merge into Person |
| personWikipediaUrl | URL | | |
| personGraveUrl | URL | | |
| personLegacyUrl | Plain Text | | Prefixed. Global `legacyUrl` not on this layout |
| personWebmasterNoteTop, personWebmasterNoteBottom, personFinePrint | Plain Text | | |
| culturalSensitivityNote | Plain Text | | |
| historicalEra, historicalPeriod, neighborhood | Categories | those groups | |

No military service fields on Person. No `legacyKey`, `sourcePath`, or `legacyHtml`.

### 1.2 Organizations (channel)

Handle `organizations`. URI `organizations/{slug}`. Template `organizations/_entry`. Entry type `organization`. Title format `{title}`.

| Handle | Type | Relates to | Notes |
| --- | --- | --- | --- |
| featuredImage | Assets (max 1) | archiveMedia | |
| body | Plain Text (multiline) | | |
| dateFounded | Plain Text | | |
| orgAliases, orgAddress | Plain Text | | |
| orgLat, orgLng | Number | | |
| orgWebsite, orgWikipediaUrl | URL | | |
| orgLegacyUrl | Plain Text | | Prefixed |
| hasParentOrg | Lightswitch | | |
| parentOrganization | Entries | Organizations | |
| hasSubBoards | Lightswitch | | |
| subBoards | Entries | Organizations | |
| boardMembers | Entries | Persons | |
| termNotes | Plain Text | | |
| orgFoundedBy, orgAssociatedPersons | Entries | Persons | |
| orgEvents | Entries | Events | |
| orgFinePrint, culturalSensitivityNote | Plain Text | | |
| historicalEra, historicalPeriod, neighborhood | Categories | | |

No org-subtype category on the layout (JSON exists in `taxonomy-import/org-subtype.json`, not installed).

### 1.3 Places (channel)

Handle `places`. URI `places/{slug}`. Template `_entries/places`. Entry type `place`.

| Handle | Type | Relates to | Notes |
| --- | --- | --- | --- |
| body | Plain Text (multiline) | | No featuredImage on this layout |
| placeAddress, placeAliases, dateEstablished | Plain Text | | |
| placeLat, placeLng | Number | | |
| placeChlNumber | Plain Text | | California Historical Landmark |
| placeFeatured | Lightswitch | | |
| placeScvhlCheckbox | Lightswitch | | SCV landmark |
| placePeople | Entries | Persons | |
| placeOrganizations | Entries | Organizations | |
| relatedPlaces | Entries | Places | |
| placeEvents | Entries | Events | |
| placeArticles | Entries | Articles | |
| placeWikipediaUrl, placeChlUrl, placeScvhlUrl | URL | | |
| placeLegacyUrl | Plain Text | | Prefixed |
| culturalSensitivityNote | Plain Text | | |
| historicalEra, historicalPeriod, neighborhood | Categories | | |

No place-type category on the layout (JSON exists, not installed).

### 1.4 Events (channel)

Handle `events`. URI `events/{slug}`. Template `_entries/events`. Entry type `event`.

| Handle | Type | Relates to |
| --- | --- | --- |
| body | Plain Text (multiline) | |
| eventDate, eventDateStart, eventDateEnd, eventFrequency, eventNextOccurrence, eventSignificance, eventChlNumber | Plain Text | |
| eventRecurring | Lightswitch | |
| eventWikipediaUrl | URL | |
| eventLegacyUrl | Plain Text | Prefixed |
| eventPlaces | Entries | Places |
| eventPersons | Entries | Persons |
| eventOrganizations | Entries | Organizations |
| eventArticles | Entries | Articles |
| eventGroups | Entries | Groups |
| relatedEvents | Entries | Events |
| culturalSensitivityNote | Plain Text | |
| historicalEra, historicalPeriod, neighborhood | Categories | |

### 1.5 Groups (channel)

Handle `groups`. URI `groups/{slug}`. Template `_entries/groups`. Entry type `group`.

| Handle | Type | Relates to |
| --- | --- | --- |
| body | Plain Text (multiline) | Live. DATA_MODEL.md still said TO ADD |
| groupAliases, groupDateStart, groupDateEnd | Plain Text | |
| groupWebsite | URL | |
| groupLegacyUrl | Plain Text | Prefixed |
| groupPersons | Entries | Persons |
| groupEvents | Entries | Events |
| culturalSensitivityNote | Plain Text | |
| historicalEra, historicalPeriod, neighborhood | Categories | |

No groupType on the layout (JSON exists, not installed). DATA_MODEL.md decided groupType; this round does not install it.

### 1.6 Articles (channel)

Handle `articles`. URI `articles/{slug}`. Template `_entries/articles`. Entry type `article`. This is the only type that already has global `legacyKey` and `legacyUrl` on the layout.

| Handle | Type | Relates to | Notes |
| --- | --- | --- | --- |
| body | Plain Text (multiline) | | |
| originallyPublishedTitle, originalPublishDate, subheadline, sourceLine | Plain Text | | |
| legacyKey, legacyUrl | Plain Text | | Global fields, on this layout |
| archiveUrl | Plain Text | | Named Archive URL; type is Plain Text, not URL |
| accessionBatch, batchSequence | Plain Text | | Leon prefixes |
| webmasterNoteTop, webmasterNoteBottom, photoSources, finePrint, culturalSensitivityNote | Plain Text | | `photoSources` here is Plain Text, not an Org relation |
| writtenBy, editedBy, subjectPerson | Entries | Persons | |
| publishedBy, subjectOrganization | Entries | Organizations | |
| depictsPlace | Entries | Places | |
| articleLat, articleLng | Number | | Purpose unclear. See Questions for Leon |
| subjectGroup | Entries | Groups | |
| partOfCollection | Entries | Collections | |
| relatedArticles | Entries | Articles | |
| articleEvents | Entries | Events | |
| historicalEra, historicalPeriod, neighborhood | Categories | | |

### 1.7 Obituaries (channel)

Handle `obituaries`. URI `obituaries/{slug}`. Template `_entries/obituaries`. Entry type `obituary`. CHANGELOG: remains a separate section with submission workflow. That workflow is not built.

| Handle | Type | Relates to |
| --- | --- | --- |
| body | Plain Text (multiline) | |
| publicationDetails, obitDateOfDeath, obitDatePublished | Plain Text | |
| obitPublishedIn | Entries | Organizations |
| obitLegacyUrl | Plain Text | Prefixed |
| obitGraveUrl | URL | |
| obitSubject | Entries | Persons |
| obitGroup | Entries | Groups |
| obitRelatedPersons | Entries | Persons |
| obitRelatedMilitary | Entries | Military Profiles |
| obitWebmasterNoteTop, obitWebmasterNoteBottom, culturalSensitivityNote | Plain Text | |
| historicalEra, historicalPeriod, neighborhood | Categories | |

### 1.8 Collections (structure)

Handle `collections`. URI `collections/{slug}`. Template `_entries/collections`. Entry type `collection`. For series (Signal columns, Gazette, and similar).

| Handle | Type | Relates to | Notes |
| --- | --- | --- | --- |
| body | Plain Text (multiline) | | |
| legacyUrl | Plain Text | | Global field, on this layout |
| archiveUrl | Plain Text | | |
| writtenBy, editedBy | Entries | Persons | |
| publishedBy | Entries | Organizations | |
| collectionGroups | Entries | Groups | DATA_MODEL.md was unsure of purpose. Live it is Groups |
| articlesInCollection | Entries | Articles | |
| culturalSensitivityNote | Plain Text | | |
| historicalEra, historicalPeriod, neighborhood | Categories | | |

### 1.9 Military Profiles (channel)

Handle `militaryProfiles`. URI `military-profiles/{slug}`. Template `_entries/militaryProfiles`. Entry type `militaryProfile`. Settled to merge into Person. Still live. **Not an ingest target.** War memorial HTML should not be imported here.

Fields (summary): `body`; service (`mpRank`, `mpSpecialty`, `mpUnit`, `mpBase`, `mpServiceStart`, `mpServiceEnd`, `mpCombatOperations`, `mpAwards`, `mpNarrative`); personal (`mpDateOfBirth`, `mpDateOfDeath`, `mpAgeAtLoss`, `mpHomeOfRecord`, `mpHighSchool`, `mpBurialPlace`); family lightswitches plus `mpSpouse`, `mpChildren`, `mpParents`, `mpSiblings`, `mpRelatedPersons`; `mpSubject` to Person; relations to Places, Events, Organizations, Groups, Obituaries; `mpWikipediaUrl`, `mpFindAGraveUrl`, `mpLegacyUrl`; notes; era/period/neighborhood.

### 1.10 Volume: Archive Media

Handle `archiveMedia`. Local FS `localArchiveMedia`. Asset layout currently holds the photograph metadata fields: `photoDate`, `photoCredit`, `photoSourceCode`, `photoSequence`, `photoCaptionExt`, `photoPeople`, `photoPlaces`, `photoArticles`, `photoEvents`, `photoOrganizations`, `photoGroups`.

Those fields exist. They are on **assets**, not on an entry type. Object pages in the inventory are HTML records, not files. That is the Photograph/Object gap.

### 1.11 Taxonomies live in Craft

| Group handle | Name |
| --- | --- |
| historicalEra | Historical Era |
| historicalPeriod | Historical Period |
| neighborhood | Neighborhood |

Not installed (JSON only, in `taxonomy-import/`): subject-tags, place-type, org-subtype, group-type, confidence-level, conflict, disaster-type, water-type. Installing them is not part of this proposal.

## 2. Map every INVENTORY.md page type

| Inventory type | Count | Destination | Gap? |
| --- | --- | --- | --- |
| Object / photo page | 4,871 | New Photograph/Object section. `legacyKey` = item ID (`LW3094`). `gif/` jpeg = `featuredImage`. TIFF/PDF in `files/{id}` = assets on the same entry or on a related Document (see split rule) | **Yes** |
| Article / essay / reprint | 2,430 | Existing Articles. Remainder HTML that is not an object page, obituary, or `files/` package | No |
| Obituary | 635 | Existing Obituaries. `obitSubject` to Person when the person is identified | No |
| Topic or place index | 554 | Skip. Indexes are not entries | Skip |
| Signal newspaper page | 450 | Articles. One Collection per series (Perkins, Reynolds, Worden, Boston, Manzer, Newsmaker, coins, Iraq) | No |
| Old Town Newhall minisite | 275 | Articles. One Collection per Gazette run / columnist (`patti`, `pauline`, `rioux`, `whyte`) | No |
| Yearbook landing | 122 | New Document section (editorial wrapper). The `files/*yearbook*` package is that Document's files | **Yes** (Document) |
| War memorial profile | 36 | Existing Persons (entity-first). Military details stay in `body` / `legacyHtml` until the settled Person military merge | No new section |
| Mentryville minisite | 9 | One Organization (Friends of Mentryville) plus one Place (Mentryville). Story pages that remain are Articles | No |
| Pico minisite | 6 | Object pages with item IDs to photographs. Remainder HTML to articles | No |
| War memorial index | 4 | Not an entry. Derived listing later. Special pages are parked | Skip as a type |
| Home | 2 | Not entries. Craft homepage | Skip |
| Obituary index | 1 | Derived from Obituaries | Skip |
| Orig copy | 1 | Duplicate of Signal Reynolds. Do not import | Skip |
| Unclassified / empty | 33 | Stubs, empty titles, funeral-home link pages | Skip |
| Unreadable | 1 | AppleDouble `._index.htm` | Skip |
| Apache directory listing | 20,702 | Not editorial | Skip |
| Flipbook / yearbook package HTML | 3,559 | Not entries. The package becomes one Document plus assets | Skip as pages |

There is no biography HTML type. Category People object pages (287) become Photograph/Object entries related to a Person. People-category articles (163) become Articles related to a Person. Obituaries stay Obituaries. Create the Person first.

Entity-first still applies: do not import the 4,871 object pages or 2,430 articles until Persons, Places, Organizations, and taxonomies exist for what they depict.

### Split rule (Photograph/Object vs Document)

- If the legacy page is an item-ID object page (`SCVHistory.com LW3094 | ...`), it is Photograph/Object, even when the item is a postcard, clipping, or letter.
- If the work is a `scvhistory/files/{package}` directory (yearbook, inquest, cookbook, DEIR, PDF flipbook), it is one Document. Apache `Index of` pages and `basic-html/pageN.html` are not entries.
- An object page may **link** a TIFF that lives in a Document package. That is a relation, not a reason to skip Photograph/Object.

## 3. Ingest gaps (implemented locally 2026-09-16)

Local DDEV only. Not applied on Cloudways. Not an import.

### 3.1 Photograph/Object section (new)

| | |
| --- | --- |
| Section handle | `photographs` |
| Section type | channel |
| Entry type handle | `photograph` |
| URI | `photographs/{slug}` |
| Maps from | INVENTORY object / photo pages (4,871), plus Pico pages that have item IDs |

The HTML record is the object. The jpeg/tiff are files on `archiveMedia`. Do not treat the Asset as the canonical object.

**Reuse existing fields** (already defined; add them to this entry type layout; they currently live on the asset layout):

| Handle | Type | Required | Notes |
| --- | --- | --- | --- |
| title | Title | yes | Headline from the third pipe field |
| featuredImage | Assets (max 1) | no | `gif/` web jpeg |
| body | Plain Text (multiline) | no | Public text. See 3.5 |
| photoDate | Plain Text | no | Date as written |
| photoCredit | Plain Text | no | Display credit line |
| photoSourceCode | Plain Text | no | Letter prefix of the item ID (`LW`) |
| photoSequence | Plain Text | no | Numeric part (`3094`) |
| photoCaptionExt | Plain Text | no | Extended caption |
| photoPeople | Entries | no | Persons depicted. Fill after Persons exist |
| photoPlaces | Entries | no | Places |
| photoOrganizations | Entries | no | Organizations |
| photoEvents | Entries | no | Events |
| photoArticles | Entries | no | Articles |
| photoGroups | Entries | no | Groups |
| culturalSensitivityNote | Plain Text | no | Required practice for Indigenous content; field already exists |
| historicalEra, historicalPeriod, neighborhood | Categories | no | |

**New on this type**

| Handle | Type | Required | Notes |
| --- | --- | --- | --- |
| relatedPhotographs | Entries (Photographs, max null) | no | Thumbnail strip of other item IDs |
| archivalFiles | Assets (max null) | no | Matching TIFF/JPEG from `files/` when it belongs to this item, not to a separate Document package |

Plus global ingest fields (3.3) and credit parse fields (3.4).

### 3.2 Document section (new)

| | |
| --- | --- |
| Section handle | `documents` |
| Section type | channel |
| Entry type handle | `document` |
| URI | `documents/{slug}` |
| Maps from | Yearbook landings (122) and each real `scvhistory/files/{package}` work (about 1,043 packages, not 24,261 HTML files) |

No separate Yearbook type. No documentType taxonomy this round. Leon's heading goes in `legacyCategory`.

| Handle | Type | Required | Notes |
| --- | --- | --- | --- |
| title | Title | yes | From the landing page title or package name |
| body | Plain Text (multiline) | no | Notes or a later transcription. See 3.5 |
| featuredImage | Assets (max 1) | no | Cover or first page jpeg |
| documentFiles | Assets (max null) | no | PDF, TIFF, page jpegs on `archiveMedia`. Not the flipbook HTML |
| culturalSensitivityNote | Plain Text | no | |
| historicalEra, historicalPeriod, neighborhood | Categories | no | |

Plus global ingest fields (3.3). Credit parse fields are optional on Document when a footer exists.

Related Photograph/Object entries (the landing or object page that points at this package) use `relatedPhotographs` / `photoArticles` as appropriate after both exist. Do not invent a new relation field for that in this round if `photoArticles` does not fit; add `relatedDocuments` only if Task 3 needs it. Default: Photograph `archivalFiles` when the files are the scan of **that item**; Document when the package is a multi-page work.

### 3.3 Global ingest fields

Add to every migrated entry type: Person, Organization, Place, Event, Group, Article, Obituary, Collection, Photograph/Object, Document. Not Military Profile.

| Handle | Type | Required | Status | Use |
| --- | --- | --- | --- | --- |
| legacyKey | Plain Text | no | Exists. On Article only | Item ID (`LW3094`) or filename stem for non-object pages |
| legacyUrl | Plain Text | **yes** on import | Exists. On Article and Collection only | Original scvhistory.com path, for 301s and traceability. Example: `/scvhistory/lw3094.htm` |
| sourcePath | Plain Text | **yes** on import | **New** | Path under `/Volumes/Jordy/SCVHistory`. Example: `scvhistory.com/scvhistory/lw3094.htm` |
| legacyHtml | Plain Text (multiline) | no | **New** | Original page HTML after chrome strip. Not rendered as the public body |
| legacyCategory | Plain Text | no | **New** | Second pipe field (`Newhall Pass`, `Film/Arts`, `Obituaries`). Working heading, not a taxonomy |

Keep the prefixed `personLegacyUrl`, `placeLegacyUrl`, `orgLegacyUrl`, `eventLegacyUrl`, `groupLegacyUrl`, `obitLegacyUrl` fields in place. Do not delete them this round. New imports write the global `legacyUrl`.

### 3.4 Credit parse fields

On Photograph/Object. Optional on Document.

Footer pattern from inventory: `{ID}: {dpi} dpi jpeg from {original|photocopy|copy print} {courtesy of|collection of|purchased by|photo by} {name}`.

Example: `SW1902: 9600 dpi jpeg from original postcard courtesy of Stan Walker.`

| Handle | Type | Required | Use |
| --- | --- | --- | --- |
| creditRaw | Plain Text | no | Full footer line, unparsed |
| creditDpi | Plain Text | no | `9600`, `19200` |
| creditProcess | Plain Text | no | original, photocopy, copy print, smaller jpeg |
| creditKind | Plain Text | no | courtesy of, collection of, purchased by, photo by |
| creditName | Plain Text | no | Name as written. Do not relate to Person/Org on ingest |

Keep `photoCredit` as the display line. Do not replace it.

### 3.5 body vs legacyHtml

| Field | Role |
| --- | --- |
| legacyHtml | Fidelity copy of the source HTML (ads/trackers stripped when present). Not the public page body. Used for later cleanup, captions, related thumbs, and redirects QA |
| body | Public text on the new site. Import may seed it with a conservative text extract. Editors clean it in Craft |

Do not dump full page HTML into `body`. Do not change `body` from Plain Text to CKEditor in this proposal.

## 4. Out of scope

Do not add or redesign in this round:

- Roles Matrix (settled, unimplemented)
- Military fields on Person / retiring Military Profiles (settled, unimplemented)
- Award entry type, Walk of Fame as awards
- Named places map, haunted places, On This Day
- IIIF manifests, tiles, annotation entries (derived later; Archive.org parked)
- Oral History entry type
- Yearbook as its own section
- documentType / subjectTags / placeType / orgSubtype category groups
- Installing `taxonomy-import/` JSON
- Special-page templates (War Memorial indexes, home)
- CKEditor, new plugins
- Task 3 schema implementation until this file is approved

## 5. Mapping closed by Nathan

No Leon gate for ingest mapping. Remaining research (item-ID prefix meanings, `articleLat`/`articleLng` purpose, Tataviam handling under `TATAVIAM_AUDIT.md`) can wait. It does not block Task 3.

## 6. After Task 3

Schema is in local DDEV and `config/project/`. Do not apply on Cloudways. Do not start imports (Task 4) until entities exist. Entity-first still applies.
