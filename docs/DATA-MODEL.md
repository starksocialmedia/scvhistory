# The data model

Generated from the live schema by `scripts/import/generate_data_model.php` on 24 September 2026. Do not edit by hand: regenerate.

Every entry type, every field, and the external standard each maps to. A field
marked **local** has no external equivalent, and that is a statement rather than
an omission: `placeScvhlCheckbox` records whether the historical society has
listed a place, and no vocabulary has a term for it.

## The name policy

Three rules, applied by the review screen and the name canon.

1. **The authority form is the title.** Where an authority holds the body, its
   official name becomes the record title: NCES for a school, the IRS Business
   Master File for a nonprofit, Wikidata otherwise. Where none does, the fullest
   form the corpus uses is the title.
2. **Usage becomes an alias.** The spelling the articles actually carry goes into
   the aliases, always. A record that cannot be found under the name the text
   uses has lost what the rename was for.
3. **No titles in names.** Honorifics and civic titles are stripped from the
   title and kept as aliases: Doctor, Captain, Colonel, Congressman,
   Councilwoman, Mayor, Supervisor, Sheriff, Judge, Senator, Reverend, Father,
   Chief. A man is a councilman for four years and a name for the rest of his
   life.

   **Mrs, Miss, Ms and Sister are never stripped.** "Mrs. George LeBrun" is a
   woman named by her husband, and folding her into his record erases her.

## Identifier schemes

| Field | Scheme | URL pattern | Wikidata property |
| --- | --- | --- | --- |
| `gnisId` | GNIS Feature ID | `https://edits.nationalmap.gov/apps/gaz-domestic/public/summary/{id}` | P590 |
| `wikidataId` | Wikidata item | `https://www.wikidata.org/wiki/{id}` | - |
| `viafId` | VIAF cluster | `https://viaf.org/viaf/{id}` | P214 |
| `ein` | IRS Employer Identification Number | `https://apps.irs.gov/app/eos/ (no direct row URL)` | P1297 |
| `cdsCode` | CDE county-district-school code | `https://www.cde.ca.gov/SchoolDirectory/details?cdscode={id}` | P2183 |
| `ncesId` | NCES school or district id | `https://nces.ed.gov/ccd/schoolsearch/school_detail.asp?ID={id}` | P2696 |
| `nrhpReference` | National Register reference | `https://npgallery.nps.gov/NRHP/AssetDetail?assetID={id}` | P649 |

## The relation vocabulary

Relations are held on the side that reads naturally in the control panel, and
emitted from whichever side schema.org expects.

| Ours | Between | schema.org |
| --- | --- | --- |
| `subjectPerson` | article to person | about |
| `subjectOrganization` | article to organization | about |
| `subjectGroup` | article to group | about |
| `depictsPlace` | article to place | contentLocation |
| `articleEvents` | article to event | about |
| `writtenBy` | article to person | author |
| `editedBy` | article to person | editor |
| `publishedBy` | article to organization | publisher |
| `partOfCollection` | article to collection | isPartOf |
| `relatedArticles` | article to article | relatedLink |
| `parentOrganization` | organization to organization | parentOrganization, inverse subOrganization |
| `spouseOf` | person to person | spouse |
| `childOf` | person to person | parent |
| `siblingOf` | person to person | sibling |
| `personOrganizations` | person to organization | affiliation |
| `placePeople` | place to person | no direct term; emitted as about on the place |
| `derivedImageLinks` | record to record | local: images derived from a record, not curated for it |

## Entry types

### Articles — `articles/article`

45 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `originallyPublishedTitle` | PlainText | schema.org `alternativeHeadline` | the title the piece first carried |
| `originalPublishDate` | PlainText | schema.org `datePublished` | printed form; EDTF in dateEdtf where it parses |
| `originalPublishDateEdtf` | PlainText | **local** | no external equivalent |
| `subheadline` | PlainText | schema.org `alternativeHeadline` |  |
| `sourceLine` | PlainText | **local** | no external equivalent |
| `legacyKey` | PlainText | **local** | no external equivalent |
| `legacyUrl` | PlainText | Dublin Core `source` | where it stood on the legacy site |
| `archiveUrl` | PlainText | Dublin Core `source` | Internet Archive capture |
| `accessionBatch` | PlainText | **local** | no external equivalent |
| `batchSequence` | PlainText | **local** | no external equivalent |
| `webmasterNoteTop` | PlainText | **local** | no external equivalent |
| `webmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `editorNotes` | Table | **local** | no external equivalent |
| `photoSources` | PlainText | **local** | no external equivalent |
| `finePrint` | PlainText | **local** | no external equivalent |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `culturalSensitivityNote` | PlainText | Dublin Core `rights` | approximate; it is a note, not a licence |
| `recordTags` | Categories | Dublin Core `subject` | local vocabulary |
| `writtenBy` | Entries | schema.org `author` | also dcterms:creator |
| `editedBy` | Entries | schema.org `editor` |  |
| `subjectPerson` | Entries | schema.org `about` | dcterms:subject |
| `publishedBy` | Entries | schema.org `publisher` |  |
| `subjectOrganization` | Entries | schema.org `about` | dcterms:subject |
| `depictsPlace` | Entries | schema.org `contentLocation` | dcterms:spatial |
| `articleLat` | Number | WGS84 `lat` | where the piece is about a spot |
| `articleLng` | Number | WGS84 `long` |  |
| `subjectGroup` | Entries | schema.org `about` | dcterms:subject |
| `partOfCollection` | Entries | schema.org `isPartOf` | also dcterms:isPartOf |
| `relatedArticles` | Entries | schema.org `relatedLink` |  |
| `derivedImageLinks` | Entries | **local** | no external equivalent |
| `articleEvents` | Entries | schema.org `about` |  |
| `historicalEra` | Categories | Dublin Core `temporal` | local vocabulary, no external period thesaurus |
| `articleThemes` | Categories | Dublin Core `subject` | local vocabulary; any number per article, where an era is one |
| `historicalPeriod` | Categories | Dublin Core `temporal` | local vocabulary |
| `neighborhood` | Categories | Dublin Core `spatial` | local vocabulary of valley communities |
| `sourcePath` | PlainText | Dublin Core `source` |  |
| `legacyHtml` | PlainText | **local** | no external equivalent |
| `legacyCategory` | PlainText | **local** | no external equivalent |
| `recordImages` | Assets | schema.org `image` |  |
| `recordDocuments` | Assets | schema.org `associatedMedia` |  |
| `recordDates` | Table | **local** | no external equivalent |
| `bandImage` | Assets | schema.org `image` | presentation only |

### Collections — `collections/collection`

33 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `webmasterNoteTop` | PlainText | **local** | no external equivalent |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `webmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `editorNotes` | Table | **local** | no external equivalent |
| `legacyUrl` | PlainText | Dublin Core `source` | where it stood on the legacy site |
| `archiveUrl` | PlainText | Dublin Core `source` | Internet Archive capture |
| `culturalSensitivityNote` | PlainText | Dublin Core `rights` | approximate; it is a note, not a licence |
| `recordTags` | Categories | Dublin Core `subject` | local vocabulary |
| `collectionIsMajor` | Lightswitch | **local** | no external equivalent |
| `collectionParts` | Table | **local** | no external equivalent |
| `writtenBy` | Entries | schema.org `author` | also dcterms:creator |
| `editedBy` | Entries | schema.org `editor` |  |
| `publishedBy` | Entries | schema.org `publisher` |  |
| `collectionKind` | Dropdown | **local** | how a collection is read, not what it is about |
| `collectionFrozen` | Lightswitch | **local** | no external equivalent |
| `collectionGroups` | Entries | **local** | no external equivalent |
| `articlesInCollection` | Entries | **local** | no external equivalent |
| `historicalEra` | Categories | Dublin Core `temporal` | local vocabulary, no external period thesaurus |
| `historicalPeriod` | Categories | Dublin Core `temporal` | local vocabulary |
| `neighborhood` | Categories | Dublin Core `spatial` | local vocabulary of valley communities |
| `legacyKey` | PlainText | **local** | no external equivalent |
| `sourceLine` | PlainText | **local** | no external equivalent |
| `sourcePath` | PlainText | Dublin Core `source` |  |
| `legacyHtml` | PlainText | **local** | no external equivalent |
| `legacyCategory` | PlainText | **local** | no external equivalent |
| `recordImages` | Assets | schema.org `image` |  |
| `recordDocuments` | Assets | schema.org `associatedMedia` |  |
| `recordDates` | Table | **local** | no external equivalent |
| `bandImage` | Assets | schema.org `image` | presentation only |
| `derivedImageLinks` | Entries | **local** | no external equivalent |

### Documents — `documents/document`

28 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `webmasterNoteTop` | PlainText | **local** | no external equivalent |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `originallyPublishedTitle` | PlainText | schema.org `alternativeHeadline` | the title the piece first carried |
| `originalPublishDate` | PlainText | schema.org `datePublished` | printed form; EDTF in dateEdtf where it parses |
| `originalPublishDateEdtf` | PlainText | **local** | no external equivalent |
| `sourceLine` | PlainText | **local** | no external equivalent |
| `publishedBy` | Entries | schema.org `publisher` |  |
| `subjectPerson` | Entries | schema.org `about` | dcterms:subject |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `webmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `editorNotes` | Table | **local** | no external equivalent |
| `documentFiles` | Assets | **local** | no external equivalent |
| `recordTags` | Categories | Dublin Core `subject` | local vocabulary |
| `legacyKey` | PlainText | **local** | no external equivalent |
| `legacyUrl` | PlainText | Dublin Core `source` | where it stood on the legacy site |
| `sourcePath` | PlainText | Dublin Core `source` |  |
| `legacyHtml` | PlainText | **local** | no external equivalent |
| `legacyCategory` | PlainText | **local** | no external equivalent |
| `historicalEra` | Categories | Dublin Core `temporal` | local vocabulary, no external period thesaurus |
| `historicalPeriod` | Categories | Dublin Core `temporal` | local vocabulary |
| `neighborhood` | Categories | Dublin Core `spatial` | local vocabulary of valley communities |
| `culturalSensitivityNote` | PlainText | Dublin Core `rights` | approximate; it is a note, not a licence |
| `recordImages` | Assets | schema.org `image` |  |
| `recordDocuments` | Assets | schema.org `associatedMedia` |  |
| `recordDates` | Table | **local** | no external equivalent |
| `derivedImageLinks` | Entries | **local** | no external equivalent |

### Events — `events/event`

40 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `webmasterNoteTop` | PlainText | **local** | no external equivalent |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `webmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `editorNotes` | Table | **local** | no external equivalent |
| `eventDate` | PlainText | **local** | no external equivalent |
| `eventDateEdtf` | PlainText | **local** | no external equivalent |
| `eventDateStart` | PlainText | **local** | no external equivalent |
| `eventDateEnd` | PlainText | **local** | no external equivalent |
| `eventRecurring` | Lightswitch | **local** | no external equivalent |
| `eventFrequency` | PlainText | **local** | no external equivalent |
| `eventNextOccurrence` | PlainText | **local** | no external equivalent |
| `recordTags` | Categories | Dublin Core `subject` | local vocabulary |
| `wikidataId` | PlainText | Wikidata `QID` | emitted as schema.org sameAs |
| `eventSignificance` | PlainText | **local** | no external equivalent |
| `eventChlNumber` | PlainText | **local** | no external equivalent |
| `eventWikipediaUrl` | Link | **local** | no external equivalent |
| `eventLegacyUrl` | PlainText | **local** | no external equivalent |
| `culturalSensitivityNote` | PlainText | Dublin Core `rights` | approximate; it is a note, not a licence |
| `eventPlaces` | Entries | **local** | no external equivalent |
| `eventPersons` | Entries | **local** | no external equivalent |
| `eventOrganizations` | Entries | **local** | no external equivalent |
| `eventArticles` | Entries | **local** | no external equivalent |
| `eventGroups` | Entries | **local** | no external equivalent |
| `relatedEvents` | Entries | **local** | no external equivalent |
| `historicalEra` | Categories | Dublin Core `temporal` | local vocabulary, no external period thesaurus |
| `historicalPeriod` | Categories | Dublin Core `temporal` | local vocabulary |
| `neighborhood` | Categories | Dublin Core `spatial` | local vocabulary of valley communities |
| `legacyKey` | PlainText | **local** | no external equivalent |
| `legacyUrl` | PlainText | Dublin Core `source` | where it stood on the legacy site |
| `sourcePath` | PlainText | Dublin Core `source` |  |
| `legacyHtml` | PlainText | **local** | no external equivalent |
| `legacyCategory` | PlainText | **local** | no external equivalent |
| `recordImages` | Assets | schema.org `image` |  |
| `recordDocuments` | Assets | schema.org `associatedMedia` |  |
| `recordDates` | Table | **local** | no external equivalent |
| `bandImage` | Assets | schema.org `image` | presentation only |
| `derivedImageLinks` | Entries | **local** | no external equivalent |

### Fixes — `fixes/fix`

5 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `fixNote` | PlainText | **local** | no external equivalent |
| `fixStatus` | Dropdown | **local** | no external equivalent |
| `fixRecord` | Entries | **local** | no external equivalent |
| `fixLegacyUrl` | PlainText | **local** | no external equivalent |
| `fixSeenOn` | PlainText | **local** | no external equivalent |

### Groups — `groups/group`

30 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `webmasterNoteTop` | PlainText | **local** | no external equivalent |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `webmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `editorNotes` | Table | **local** | no external equivalent |
| `groupAliases` | PlainText | **local** | no external equivalent |
| `groupDateStart` | PlainText | **local** | no external equivalent |
| `groupDateEnd` | PlainText | **local** | no external equivalent |
| `groupWebsite` | Link | **local** | no external equivalent |
| `groupLegacyUrl` | PlainText | **local** | no external equivalent |
| `culturalSensitivityNote` | PlainText | Dublin Core `rights` | approximate; it is a note, not a licence |
| `recordTags` | Categories | Dublin Core `subject` | local vocabulary |
| `wikidataId` | PlainText | Wikidata `QID` | emitted as schema.org sameAs |
| `groupPersons` | Entries | **local** | no external equivalent |
| `groupEvents` | Entries | **local** | no external equivalent |
| `historicalEra` | Categories | Dublin Core `temporal` | local vocabulary, no external period thesaurus |
| `historicalPeriod` | Categories | Dublin Core `temporal` | local vocabulary |
| `neighborhood` | Categories | Dublin Core `spatial` | local vocabulary of valley communities |
| `legacyKey` | PlainText | **local** | no external equivalent |
| `legacyUrl` | PlainText | Dublin Core `source` | where it stood on the legacy site |
| `sourcePath` | PlainText | Dublin Core `source` |  |
| `legacyHtml` | PlainText | **local** | no external equivalent |
| `legacyCategory` | PlainText | **local** | no external equivalent |
| `recordImages` | Assets | schema.org `image` |  |
| `recordDocuments` | Assets | schema.org `associatedMedia` |  |
| `recordDates` | Table | **local** | no external equivalent |
| `bandImage` | Assets | schema.org `image` | presentation only |
| `derivedImageLinks` | Entries | **local** | no external equivalent |

### Military Profiles — `militaryProfiles/militaryProfile`

47 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `mpRank` | PlainText | **local** | no external equivalent |
| `mpSpecialty` | PlainText | **local** | no external equivalent |
| `mpUnit` | PlainText | **local** | no external equivalent |
| `mpBase` | PlainText | **local** | no external equivalent |
| `mpServiceStart` | PlainText | **local** | no external equivalent |
| `mpServiceEnd` | PlainText | **local** | no external equivalent |
| `mpCombatOperations` | PlainText | **local** | no external equivalent |
| `recordTags` | Categories | Dublin Core `subject` | local vocabulary |
| `mpDateOfBirth` | PlainText | **local** | no external equivalent |
| `mpDateOfDeath` | PlainText | **local** | no external equivalent |
| `mpAgeAtLoss` | Number | **local** | no external equivalent |
| `mpHomeOfRecord` | PlainText | **local** | no external equivalent |
| `mpHighSchool` | PlainText | **local** | no external equivalent |
| `mpBurialPlace` | PlainText | **local** | no external equivalent |
| `mpAwards` | PlainText | **local** | no external equivalent |
| `mpNarrative` | PlainText | **local** | no external equivalent |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `mpHasSpouse` | Lightswitch | **local** | no external equivalent |
| `mpHasChildren` | Lightswitch | **local** | no external equivalent |
| `mpHasParents` | Lightswitch | **local** | no external equivalent |
| `mpHasSiblings` | Lightswitch | **local** | no external equivalent |
| `mpRelatedPersons` | Entries | **local** | no external equivalent |
| `childOf` | Entries | schema.org `parent` | inverse of schema.org children |
| `siblingOf` | Entries | schema.org `sibling` |  |
| `spouseOf` | Entries | schema.org `spouse` |  |
| `mpSubject` | Entries | **local** | no external equivalent |
| `mpPlaces` | Entries | **local** | no external equivalent |
| `mpEvents` | Entries | **local** | no external equivalent |
| `mpOrganizations` | Entries | **local** | no external equivalent |
| `mpGroups` | Entries | **local** | no external equivalent |
| `mpRelatedObituaries` | Entries | **local** | no external equivalent |
| `mpWikipediaUrl` | Link | **local** | no external equivalent |
| `mpFindAGraveUrl` | Link | **local** | no external equivalent |
| `mpLegacyUrl` | PlainText | **local** | no external equivalent |
| `mpWebmasterNoteTop` | PlainText | **local** | no external equivalent |
| `mpWebmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `mpFinePrint` | PlainText | **local** | no external equivalent |
| `historicalEra` | Categories | Dublin Core `temporal` | local vocabulary, no external period thesaurus |
| `historicalPeriod` | Categories | Dublin Core `temporal` | local vocabulary |
| `neighborhood` | Categories | Dublin Core `spatial` | local vocabulary of valley communities |
| `recordImages` | Assets | schema.org `image` |  |
| `recordDocuments` | Assets | schema.org `associatedMedia` |  |
| `recordDates` | Table | **local** | no external equivalent |
| `derivedImageLinks` | Entries | **local** | no external equivalent |

### Obituaries — `obituaries/obituary`

31 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `editorNotes` | Table | **local** | no external equivalent |
| `publicationDetails` | PlainText | **local** | no external equivalent |
| `obitDateOfDeath` | PlainText | **local** | no external equivalent |
| `obitDatePublished` | PlainText | **local** | no external equivalent |
| `obitPublishedIn` | Entries | **local** | no external equivalent |
| `obitLegacyUrl` | PlainText | **local** | no external equivalent |
| `obitGraveUrl` | Link | **local** | no external equivalent |
| `recordTags` | Categories | Dublin Core `subject` | local vocabulary |
| `obitSubject` | Entries | **local** | no external equivalent |
| `obitGroup` | Entries | **local** | no external equivalent |
| `obitRelatedPersons` | Entries | **local** | no external equivalent |
| `obitRelatedMilitary` | Entries | **local** | no external equivalent |
| `obitWebmasterNoteTop` | PlainText | **local** | no external equivalent |
| `obitWebmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `culturalSensitivityNote` | PlainText | Dublin Core `rights` | approximate; it is a note, not a licence |
| `historicalEra` | Categories | Dublin Core `temporal` | local vocabulary, no external period thesaurus |
| `historicalPeriod` | Categories | Dublin Core `temporal` | local vocabulary |
| `neighborhood` | Categories | Dublin Core `spatial` | local vocabulary of valley communities |
| `legacyKey` | PlainText | **local** | no external equivalent |
| `legacyUrl` | PlainText | Dublin Core `source` | where it stood on the legacy site |
| `sourcePath` | PlainText | Dublin Core `source` |  |
| `legacyHtml` | PlainText | **local** | no external equivalent |
| `legacyCategory` | PlainText | **local** | no external equivalent |
| `recordImages` | Assets | schema.org `image` |  |
| `recordDocuments` | Assets | schema.org `associatedMedia` |  |
| `recordDates` | Table | **local** | no external equivalent |
| `derivedImageLinks` | Entries | **local** | no external equivalent |

### Organizations — `organizations/organization`

56 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `webmasterNoteTop` | PlainText | **local** | no external equivalent |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `webmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `editorNotes` | Table | **local** | no external equivalent |
| `dateFounded` | PlainText | schema.org `foundingDate` | printed form |
| `dateFoundedEdtf` | PlainText | **local** | no external equivalent |
| `orgAliases` | PlainText | SKOS `altLabel` | schema.org alternateName |
| `orgAddress` | PlainText | schema.org `address` |  |
| `orgLat` | Number | WGS84 `lat` | schema.org latitude |
| `orgLng` | Number | WGS84 `long` | schema.org longitude |
| `orgWebsite` | Link | **local** | no external equivalent |
| `orgWikipediaUrl` | Link | schema.org `sameAs` |  |
| `orgLegacyUrl` | PlainText | **local** | no external equivalent |
| `recordTags` | Categories | Dublin Core `subject` | local vocabulary |
| `wikidataId` | PlainText | Wikidata `QID` | emitted as schema.org sameAs |
| `cdsCode` | PlainText | CDE `CDS code` | Wikidata P2183 |
| `ncesId` | PlainText | NCES `school or district ID` | Wikidata P2696 |
| `ein` | PlainText | IRS `EIN` | Wikidata P1297 |
| `hauntedStatus` | Dropdown | **local** | no external equivalent |
| `hauntedAccount` | PlainText | **local** | no external equivalent |
| `hauntedSource` | PlainText | **local** | no external equivalent |
| `dateDissolved` | PlainText | **local** | no external equivalent |
| `dateDissolvedEdtf` | PlainText | **local** | no external equivalent |
| `precededBy` | Entries | **local** | no external equivalent |
| `succeededBy` | Entries | **local** | no external equivalent |
| `seatCount` | Number | **local** | no external equivalent |
| `hasParentOrg` | Lightswitch | **local** | no external equivalent |
| `parentOrganization` | Entries | schema.org `parentOrganization` | inverse emitted as subOrganization |
| `hasSubBoards` | Lightswitch | **local** | no external equivalent |
| `subBoards` | Entries | **local** | no external equivalent |
| `boardMembers` | Entries | **local** | no external equivalent |
| `termNotes` | PlainText | **local** | no external equivalent |
| `orgFoundedBy` | Entries | **local** | no external equivalent |
| `orgAssociatedPersons` | Entries | **local** | no external equivalent |
| `orgEvents` | Entries | **local** | no external equivalent |
| `derivedImageLinks` | Entries | **local** | no external equivalent |
| `orgFinePrint` | PlainText | **local** | no external equivalent |
| `culturalSensitivityNote` | PlainText | Dublin Core `rights` | approximate; it is a note, not a licence |
| `historicalEra` | Categories | Dublin Core `temporal` | local vocabulary, no external period thesaurus |
| `historicalPeriod` | Categories | Dublin Core `temporal` | local vocabulary |
| `neighborhood` | Categories | Dublin Core `spatial` | local vocabulary of valley communities |
| `orgType` | Dropdown | **local** | drives the schema.org @type |
| `schoolLevel` | Dropdown | **local** | drives the schema.org School subtype |
| `legacyKey` | PlainText | **local** | no external equivalent |
| `legacyUrl` | PlainText | Dublin Core `source` | where it stood on the legacy site |
| `sourcePath` | PlainText | Dublin Core `source` |  |
| `legacyHtml` | PlainText | **local** | no external equivalent |
| `legacyCategory` | PlainText | **local** | no external equivalent |
| `recordProvenance` | PlainText | Dublin Core `provenance` | how the record came to exist |
| `recordImages` | Assets | schema.org `image` |  |
| `recordDocuments` | Assets | schema.org `associatedMedia` |  |
| `recordDates` | Table | **local** | no external equivalent |
| `bandImage` | Assets | schema.org `image` | presentation only |

### Pages — `pages/page`

11 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `webmasterNoteTop` | PlainText | **local** | no external equivalent |
| `webmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `featuredImage` | Assets | schema.org `image` |  |
| `recordImages` | Assets | schema.org `image` |  |
| `recordDocuments` | Assets | schema.org `associatedMedia` |  |
| `recordTags` | Categories | Dublin Core `subject` | local vocabulary |
| `recordDates` | Table | **local** | no external equivalent |
| `derivedImageLinks` | Entries | **local** | no external equivalent |

### Persons — `persons/person`

51 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `editorNotes` | Table | **local** | no external equivalent |
| `fullName` | PlainText | **local** | no external equivalent |
| `birthDate` | PlainText | schema.org `birthDate` | printed form |
| `birthplace` | PlainText | schema.org `birthPlace` |  |
| `deathDate` | PlainText | schema.org `deathDate` | printed form |
| `birthDateEdtf` | PlainText | **local** | no external equivalent |
| `deathDateEdtf` | PlainText | **local** | no external equivalent |
| `burialPlace` | PlainText | schema.org `deathPlace` | burial rather than death, so the mapping is approximate |
| `occupation` | PlainText | schema.org `hasOccupation` | free text today; see the roles work |
| `roles` | Entries | **local** | no external equivalent |
| `authorBio` | PlainText | **local** | no external equivalent |
| `recordTags` | Categories | Dublin Core `subject` | local vocabulary |
| `personAliases` | PlainText | SKOS `altLabel` | schema.org alternateName |
| `wikidataId` | PlainText | Wikidata `QID` | emitted as schema.org sameAs |
| `viafId` | PlainText | VIAF `cluster ID` | Wikidata P214 |
| `spouseOf` | Entries | schema.org `spouse` |  |
| `childOf` | Entries | schema.org `parent` | inverse of schema.org children |
| `siblingOf` | Entries | schema.org `sibling` |  |
| `relatedPersons` | Entries | **local** | no external equivalent |
| `derivedImageLinks` | Entries | **local** | no external equivalent |
| `personOrganizations` | Entries | **local** | no external equivalent |
| `personGroups` | Entries | **local** | no external equivalent |
| `personEvents` | Entries | **local** | no external equivalent |
| `articlesAbout` | Entries | **local** | no external equivalent |
| `personObituaries` | Entries | **local** | no external equivalent |
| `relatedMilitary` | Entries | **local** | no external equivalent |
| `personWikipediaUrl` | Link | schema.org `sameAs` |  |
| `personGraveUrl` | Link | schema.org `sameAs` | Find a Grave |
| `personLegacyUrl` | PlainText | **local** | no external equivalent |
| `personWebmasterNoteTop` | PlainText | **local** | no external equivalent |
| `personWebmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `personFinePrint` | PlainText | **local** | no external equivalent |
| `culturalSensitivityNote` | PlainText | Dublin Core `rights` | approximate; it is a note, not a licence |
| `historicalEra` | Categories | Dublin Core `temporal` | local vocabulary, no external period thesaurus |
| `historicalPeriod` | Categories | Dublin Core `temporal` | local vocabulary |
| `neighborhood` | Categories | Dublin Core `spatial` | local vocabulary of valley communities |
| `legacyKey` | PlainText | **local** | no external equivalent |
| `legacyUrl` | PlainText | Dublin Core `source` | where it stood on the legacy site |
| `sourcePath` | PlainText | Dublin Core `source` |  |
| `legacyHtml` | PlainText | **local** | no external equivalent |
| `legacyCategory` | PlainText | **local** | no external equivalent |
| `recordProvenance` | PlainText | Dublin Core `provenance` | how the record came to exist |
| `bodyAuthorship` | Dropdown | **local** | no external equivalent |
| `recordImages` | Assets | schema.org `image` |  |
| `recordDocuments` | Assets | schema.org `associatedMedia` |  |
| `recordDates` | Table | **local** | no external equivalent |
| `bandImage` | Assets | schema.org `image` | presentation only |

**Who gets a record.** A person record requires a Santa Clarita Valley connection
the articles document: lived, worked, owned, built, founded, filmed, buried or
acted here. The connection has to be in the text. A name appearing in an article
is not a connection; it is a mention.

National figures and subject-matter figures a local columnist wrote about get no
record. Leon Worden covering Proposition 209 does not make Ward Connerly part of
this valley, and the coin column naming forty numismatists does not make them
residents. Those names keep their articles and point at Wikidata instead.

The test is the subject, not the collection. Most of the numismatists are caught
by being written about only inside one national column, but that is a symptom and
not the rule: Connerly appears in a local column throughout and still fails,
because nothing in the text places him here.

William Mulholland has a record. He built the aqueduct and the St Francis Dam,
and the dam broke in this valley and killed people in it; that is as documented
as a connection gets. So do John Wayne and Tom Mix, who filmed at Melody Ranch,
Charles Crocker, whose railroad came through, and Kit Carson, who came through
with Fremont.

**Holding an office that represents the valley is a connection.** Somebody who
sat on a body whose constituency includes the SCV, or held a single-member seat
drawn to include it, has a record, and the officeHolding record is the evidence:
they legislated, voted or governed for this valley whether or not they ever lived
here. A state senator whose district reached the SCV for one cycle passes on that
ground alone. Without this the two rules disagree, because the inclusion rule for
offices admits the office while the rule above turns away the person who held it.

The exception is deliberate and bounded: statewide and national at-large offices
are context, not valley seats, because nobody is the valley's governor. The two
California US Senate seats are carried anyway, marked as statewide rather than
valley offices, because an archive that cannot say who represented the state in
the Senate has a hole a reader will notice.

The ruling is recorded, not just acted on. A name ruled out is marked **External**
on the review screen, which creates nothing and writes the Wikidata id into the
name canon so the prose linker can point the name somewhere. External is not Skip:
a skip means the queue has not been settled, and an external means it has.

### Photographs — `photographs/photograph`

42 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `webmasterNoteTop` | PlainText | **local** | no external equivalent |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `webmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `editorNotes` | Table | **local** | no external equivalent |
| `photoDate` | PlainText | **local** | no external equivalent |
| `photoDateEdtf` | PlainText | **local** | no external equivalent |
| `photoCredit` | PlainText | **local** | no external equivalent |
| `photoCaptionExt` | PlainText | **local** | no external equivalent |
| `photoSourceCode` | PlainText | **local** | no external equivalent |
| `photoSequence` | PlainText | **local** | no external equivalent |
| `recordTags` | Categories | Dublin Core `subject` | local vocabulary |
| `bandImage` | Assets | schema.org `image` | presentation only |
| `partOfCollection` | Entries | schema.org `isPartOf` | also dcterms:isPartOf |
| `creditRaw` | PlainText | **local** | no external equivalent |
| `creditDpi` | PlainText | **local** | no external equivalent |
| `creditProcess` | PlainText | **local** | no external equivalent |
| `creditKind` | PlainText | **local** | no external equivalent |
| `creditName` | PlainText | **local** | no external equivalent |
| `archivalFiles` | Assets | **local** | no external equivalent |
| `photoPeople` | Entries | **local** | no external equivalent |
| `photoPlaces` | Entries | **local** | no external equivalent |
| `photoOrganizations` | Entries | **local** | no external equivalent |
| `photoEvents` | Entries | **local** | no external equivalent |
| `photoArticles` | Entries | **local** | no external equivalent |
| `photoGroups` | Entries | **local** | no external equivalent |
| `relatedPhotographs` | Entries | **local** | no external equivalent |
| `derivedImageLinks` | Entries | **local** | no external equivalent |
| `legacyKey` | PlainText | **local** | no external equivalent |
| `legacyUrl` | PlainText | Dublin Core `source` | where it stood on the legacy site |
| `sourcePath` | PlainText | Dublin Core `source` |  |
| `legacyHtml` | PlainText | **local** | no external equivalent |
| `legacyCategory` | PlainText | **local** | no external equivalent |
| `historicalEra` | Categories | Dublin Core `temporal` | local vocabulary, no external period thesaurus |
| `historicalPeriod` | Categories | Dublin Core `temporal` | local vocabulary |
| `neighborhood` | Categories | Dublin Core `spatial` | local vocabulary of valley communities |
| `culturalSensitivityNote` | PlainText | Dublin Core `rights` | approximate; it is a note, not a licence |
| `recordImages` | Assets | schema.org `image` |  |
| `recordDocuments` | Assets | schema.org `associatedMedia` |  |
| `recordDates` | Table | **local** | no external equivalent |

### Places — `places/place`

55 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `webmasterNoteTop` | PlainText | **local** | no external equivalent |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `webmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `editorNotes` | Table | **local** | no external equivalent |
| `placeAddress` | PlainText | schema.org `address` |  |
| `placeLat` | Number | WGS84 `lat` | schema.org latitude |
| `placeLng` | Number | WGS84 `long` | schema.org longitude |
| `dateEstablished` | PlainText | schema.org `foundingDate` | printed form |
| `dateEstablishedEdtf` | PlainText | **local** | no external equivalent |
| `placeAliases` | PlainText | SKOS `altLabel` | schema.org alternateName |
| `placeChlNumber` | PlainText | OHP `California Historical Landmark number` | state register |
| `placeFeatured` | Lightswitch | **local** | no external equivalent |
| `placeType` | Dropdown | **local** | mapped from GNIS feature class where a record has one |
| `placeScvhlCheckbox` | Lightswitch | **local** | no external equivalent |
| `culturalSensitivityNote` | PlainText | Dublin Core `rights` | approximate; it is a note, not a licence |
| `recordTags` | Categories | Dublin Core `subject` | local vocabulary |
| `wikidataId` | PlainText | Wikidata `QID` | emitted as schema.org sameAs |
| `gnisId` | PlainText | GNIS `Feature ID` | Wikidata P590 |
| `scvhlNumber` | PlainText | SCVHS `landmark number` | local register, no external scheme |
| `nrhpReference` | PlainText | NPS `NRHP reference number` | Wikidata P649 |
| `nrhpListedDate` | PlainText | NPS `listing date` |  |
| `hauntedStatus` | Dropdown | **local** | no external equivalent |
| `hauntedAccount` | PlainText | **local** | no external equivalent |
| `hauntedSource` | PlainText | **local** | no external equivalent |
| `districtNumber` | PlainText | **local** | no external equivalent |
| `districtKind` | Dropdown | **local** | no external equivalent |
| `effectiveFrom` | PlainText | **local** | no external equivalent |
| `effectiveTo` | PlainText | **local** | no external equivalent |
| `placePeople` | Entries | **local** | no external equivalent |
| `placeOrganizations` | Entries | **local** | no external equivalent |
| `relatedPlaces` | Entries | **local** | no external equivalent |
| `placeEvents` | Entries | **local** | no external equivalent |
| `placeArticles` | Entries | **local** | no external equivalent |
| `derivedImageLinks` | Entries | **local** | no external equivalent |
| `placeWikipediaUrl` | Link | schema.org `sameAs` |  |
| `placeChlUrl` | Link | **local** | no external equivalent |
| `placeScvhlUrl` | Link | **local** | no external equivalent |
| `placeLegacyUrl` | PlainText | **local** | no external equivalent |
| `historicalEra` | Categories | Dublin Core `temporal` | local vocabulary, no external period thesaurus |
| `historicalPeriod` | Categories | Dublin Core `temporal` | local vocabulary |
| `neighborhood` | Categories | Dublin Core `spatial` | local vocabulary of valley communities |
| `legacyKey` | PlainText | **local** | no external equivalent |
| `legacyUrl` | PlainText | Dublin Core `source` | where it stood on the legacy site |
| `sourcePath` | PlainText | Dublin Core `source` |  |
| `legacyHtml` | PlainText | **local** | no external equivalent |
| `legacyCategory` | PlainText | **local** | no external equivalent |
| `recordProvenance` | PlainText | Dublin Core `provenance` | how the record came to exist |
| `recordImages` | Assets | schema.org `image` |  |
| `recordDocuments` | Assets | schema.org `associatedMedia` |  |
| `recordDates` | Table | **local** | no external equivalent |
| `graveCensus` | Table | **local** | no external equivalent |
| `bandImage` | Assets | schema.org `image` | presentation only |

### Roles — `roles/role`

2 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `roleWikidataId` | PlainText | **local** | no external equivalent |
| `roleMatch` | PlainText | **local** | no external equivalent |

### Roles Probe — `rolesProbe/roleProbe`

1 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `roleWikidataId` | PlainText | **local** | no external equivalent |

### War Memorials — `warMemorials/warMemorial`

54 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `webmasterNoteTop` | PlainText | **local** | no external equivalent |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `webmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `editorNotes` | Table | **local** | no external equivalent |
| `deathDate` | PlainText | schema.org `deathDate` | printed form |
| `deathDateEdtf` | PlainText | **local** | no external equivalent |
| `burialPlace` | PlainText | schema.org `deathPlace` | burial rather than death, so the mapping is approximate |
| `recordTags` | Categories | Dublin Core `subject` | local vocabulary |
| `neighborhood` | Categories | Dublin Core `spatial` | local vocabulary of valley communities |
| `wmFamily` | PlainText | **local** | no external equivalent |
| `wmSelectiveServiceDate` | PlainText | **local** | no external equivalent |
| `wikidataId` | PlainText | Wikidata `QID` | emitted as schema.org sameAs |
| `wmBranch` | PlainText | **local** | no external equivalent |
| `wmRank` | PlainText | **local** | no external equivalent |
| `wmUnit` | PlainText | **local** | no external equivalent |
| `wmConflict` | PlainText | **local** | no external equivalent |
| `wmHomeOfRecord` | PlainText | **local** | no external equivalent |
| `wmRelatedPerson` | Entries | **local** | no external equivalent |
| `legacyKey` | PlainText | **local** | no external equivalent |
| `legacyUrl` | PlainText | Dublin Core `source` | where it stood on the legacy site |
| `sourcePath` | PlainText | Dublin Core `source` |  |
| `legacyHtml` | PlainText | **local** | no external equivalent |
| `wmDateOfBirth` | PlainText | **local** | no external equivalent |
| `wmHighSchool` | PlainText | **local** | no external equivalent |
| `wmServiceId` | PlainText | **local** | no external equivalent |
| `wmSpecialty` | PlainText | **local** | no external equivalent |
| `wmLengthOfService` | PlainText | **local** | no external equivalent |
| `wmStartTour` | PlainText | **local** | no external equivalent |
| `wmBase` | PlainText | **local** | no external equivalent |
| `wmCombatOperations` | PlainText | **local** | no external equivalent |
| `wmIncidentDate` | PlainText | **local** | no external equivalent |
| `wmIncidentLocation` | PlainText | **local** | no external equivalent |
| `wmAgeAtLoss` | PlainText | **local** | no external equivalent |
| `wmAwards` | PlainText | **local** | no external equivalent |
| `wmNarrative` | PlainText | **local** | no external equivalent |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `wmCasualtyReason` | PlainText | **local** | no external equivalent |
| `wmCasualtyType` | PlainText | **local** | no external equivalent |
| `wmWallReference` | PlainText | **local** | no external equivalent |
| `wmBirthplace` | PlainText | **local** | no external equivalent |
| `wmCasualtyDetail` | PlainText | **local** | no external equivalent |
| `wmGradeAtLoss` | PlainText | **local** | no external equivalent |
| `wmNotes` | PlainText | **local** | no external equivalent |
| `wmServiceExtra` | Table | **local** | no external equivalent |
| `recordImages` | Assets | schema.org `image` |  |
| `recordDocuments` | Assets | schema.org `associatedMedia` |  |
| `recordDates` | Table | **local** | no external equivalent |
| `bandImage` | Assets | schema.org `image` | presentation only |
| `childOf` | Entries | schema.org `parent` | inverse of schema.org children |
| `siblingOf` | Entries | schema.org `sibling` |  |
| `spouseOf` | Entries | schema.org `spouse` |  |
| `derivedImageLinks` | Entries | **local** | no external equivalent |

## Controlled values

Every dropdown in the schema, with its values. All of these vocabularies are
local unless the note says otherwise.

- **`bodyAuthorship`** — `legacy-leon`, `wordpress-import-unsourced`, `editorial-2026`, `mixed`.
- **`collectionKind`** — `series`, `book`, `column`, `catalogue`, `topic`. How a collection is read, not what it is about. `series` is a run meant to be read in order, which is what both of the archive's long newspaper serials are; `book` is reserved for an actual published volume and is not yet used by any record.
- **`districtKind`** — `assembly`, `senate`, `congressional`, `supervisorial`, `trustee-area`, `other`.
- **`fixStatus`** — `open`, `done`, `wontfix`.
- **`hauntedStatus`** — `reported`, `legend`, `disputed`.
- **`orgType`** — `school`, `government`, `business`, `nonprofit`, `church`, `club`, `media`, `military`, `other`. Drives the schema.org `@type`: school to School, government to GovernmentOrganization, business to Corporation, nonprofit to NGO, church to Church, media to NewsMediaOrganization, club and military and other to Organization.
- **`placeType`** — `natural`, `road`, `ranch`, `building`, `park`, `site`, `trail`, `settlement`, `district`. Mapped from the GNIS feature class where a record carries a GNIS id; see `add_place_type_field.php`.
- **`schoolLevel`** — `elementary`, `middle`, `high`, `college`, `district`. Drives the schema.org School subtype: ElementarySchool, MiddleSchool, HighSchool, CollegeOrUniversity. A district has no schema.org subtype and stays Organization.

## Category groups

- **`neighborhood`** — 35 terms. Local vocabulary.
- **`historicalEra`** — 15 terms. Local vocabulary.
- **`historicalPeriod`** — 14 terms. Local vocabulary.
- **`tag`** — 0 terms. Local vocabulary.
- **`theme`** — 15 terms. Local vocabulary.

## Images as objects

Every image a page shows is emitted as a schema.org `ImageObject` in the page graph,
not as a URL hanging off the record. A picture used on six articles is one thing with
a creator, a date and a licence, and saying so is the difference between publishing a
file and publishing a photograph.

| Asset field | ImageObject property |
| --- | --- |
| (the file) | `contentUrl` |
| `photoCaptionExt` | `caption` |
| `creator` | `creator`, as a Person |
| `dateEdtf`, else `dateAsPrinted` | `dateCreated` |
| `dateAsPrinted` | `temporalCoverage`, always the printed form |
| `photoCredit` + `courtesyOf` | `creditText` |
| `rightsHolder` | `copyrightHolder` |
| `license` | `license`, as a URL |
| `legacySourcePath` | `isBasedOn` |
| width, height | `width`, `height` as QuantitativeValue in pixels (unitCode E37) |
| (the page) | `isPartOf` |

The node is identified by the photograph record's URL where one exists, so the record
and the image are one node; otherwise by its own `/media/<id>` page. A photograph
record names its image as `primaryImageOfPage`.

**An enhanced derivative is never emitted.** JSON-LD is an assertion to the rest of the
web about what this archive holds, and what it holds is the scan. An upscaled version
has pixels a model invented, and publishing it under the archive's name would be a
false claim however good it looks. Where one is attached, its original is emitted.

## Dates

Dates are held as the source printed them. "about 1887", "spring of 1912" and
"12 March 1884" are all things the corpus says, and normalising them on the way
in would lose what the source actually claimed.

Where a printed form parses cleanly, an EDTF form is derived alongside it. Where
it does not, the EDTF field stays empty rather than being guessed. EDTF is
Extended Date/Time Format, ISO 8601-2.

The EDTF fields sit beside the printed ones and are derived by
`add_edtf_fields.php`: `originalPublishDateEdtf`, `eventDateEdtf`, `photoDateEdtf`,
`birthDateEdtf`, `deathDateEdtf`, `dateFoundedEdtf` and `dateEstablishedEdtf`.

