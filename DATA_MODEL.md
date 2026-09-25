# SCVHistory.com — Full Data Model

This document maps every entry type, all fields, field types, and relationships.
Use this to analyze the data model before building or migrating content.
Last updated: 2026-04-15

---

## Relationship Key

```
→  one-way relation (source points to target)
↔  bidirectional relation (both entries link to each other)
*  multi-select (one-to-many)
?  optional field
!  required field
TO ADD  field does not yet exist — needs to be created
TO MERGE  field exists on Military Profile, needs migration to Person
```

---

## Entry Types

---

### PERSON
*The central entity. Almost everything connects through people.*

**Identity**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Title (display name) | title | Title | Short name — "Leon Worden" not "Leon John Worden II" |
| Full legal name | fullName | Plain Text | For formal citations |
| Occupation | occupation | Plain Text | Brief descriptor |
| Author bio | authorBio | Rich Text | Short third-person bio for article bylines |

**Vital Records**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Birth date | birthDate | Plain Text | Flexible — "c. 1734" or "May 13, 1825" |
| Birthplace | birthplace | Plain Text | City, state/country |
| Death date | deathDate | Plain Text | Flexible |
| Burial place | burialPlace | Plain Text | Cemetery, location |

**Education** *(TO ADD)*
| Field | Handle | Type | Notes |
|---|---|---|---|
| Elementary school | elementarySchool | Relation → Organization | ? |
| Middle school | middleSchool | Relation → Organization | ? |
| High school | highSchool | Relation → Organization | ? |
| College | college | Relation → Organization * | ? |
| Class of | classOf | Plain Text | Graduation year e.g. "1980" |

**Military Service** *(TO MERGE from Military Profile)*
| Field | Handle | Type | Notes |
|---|---|---|---|
| Has military service | hasMilitaryService | Lightswitch | Conditional toggle — reveals fields below |
| Branch / Unit | mpUnit | Plain Text | TO MERGE |
| Rank | mpRank | Plain Text | TO MERGE |
| Specialty | mpSpecialty | Plain Text | TO MERGE |
| Service start | mpServiceStart | Plain Text | TO MERGE |
| Service end | mpServiceEnd | Plain Text | TO MERGE |
| Combat operations | mpCombatOperations | Rich Text | TO MERGE |
| Awards | mpAwards | Rich Text | TO MERGE |
| High school | mpHighSchool | Plain Text | TO MERGE |
| Home of record | mpHomeOfRecord | Plain Text | TO MERGE |

**Content**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Body | body | Rich Text | Main biographical content |
| Webmaster note top | personWebmasterNoteTop | Plain Text | Editorial note |
| Webmaster note bottom | personWebmasterNoteBottom | Plain Text | Editorial note |
| Fine print | personFinePrint | Plain Text | Copyright, sourcing notes |

**External Links**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Wikipedia URL | personWikipediaUrl | URL | ? |
| Find A Grave URL | personGraveUrl | URL | ? |
| Legacy site URL | personLegacyUrl | Plain Text | Relative path to old site |

**Media**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Featured image | featuredImage | Asset | Single portrait |

**Taxonomy**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Historical era | historicalEra | Category * | |
| Historical period | historicalPeriod | Category * | Decade buckets |
| Neighborhood | neighborhood | Category * | |
| Subject tags | subjectTags | Category * | TO ADD |
| Confidence level | confidenceLevel | Category | TO ADD |
| Specific year | specificYear | Plain Text | TO ADD — alongside decade buckets |

**Family Relationships** *(Person ↔ Person)*
| Field | Handle | Type | Notes |
|---|---|---|---|
| Spouse of | spouseOf | Relation ↔ Person * | |
| Parent of | parentOf | Relation ↔ Person * | |
| Child of | childOf | Relation ↔ Person * | |
| Sibling of | siblingOf | Relation ↔ Person * | |
| Related persons | relatedPersons | Relation ↔ Person * | General association |

**Organizational Relationships**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Organizations | personOrganizations | Relation ↔ Org * | |
| Groups | personGroups | Relation ↔ Group * | |

**Other Relationships**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Events | personEvents | Relation ↔ Event * | |
| Articles about | articlesAbout | Relation ↔ Article * | Reverse of subjectPersons |
| Obituaries | personObituaries | Relation → Obituary * | |

---

### ORGANIZATION
*Formal bodies — governments, businesses, missions, ranchos, schools, nonprofits.*

**Identity**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Title | title | Title | ! |
| Aliases | orgAliases | Plain Text | Also known as |
| Organization subtype | orgSubtype | Category | TO ADD — Educational Institution, Rancho, Mission, Government, Business, Nonprofit, Media, Military Unit, Cultural, Religious |

**Educational Institution Fields** *(conditional on subtype — TO ADD)*
| Field | Handle | Type | Notes |
|---|---|---|---|
| Grade range | gradeRange | Plain Text | "K-6", "9-12" |
| School mascot | schoolMascot | Plain Text | |
| Yearbooks | yearbooks | Relation → Yearbook * | |

**Dates**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Date founded | dateFounded | Plain Text | |
| Date dissolved | dateDissolved | Plain Text | TO ADD |

**Location**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Address | orgAddress | Plain Text | ? |
| Latitude | orgLat | Number | ? |
| Longitude | orgLng | Number | ? |

**Content**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Body | body | Rich Text | |
| Term notes | termNotes | Plain Text | Leadership term notes |
| Fine print | orgFinePrint | Plain Text | |

**External Links**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Website | orgWebsite | URL | ? |
| Wikipedia | orgWikipediaUrl | URL | ? |
| Legacy URL | orgLegacyUrl | Plain Text | ? |

**Media**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Featured image | featuredImage | Asset | Logo or photo |

**Taxonomy**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Historical era | historicalEra | Category * | |
| Historical period | historicalPeriod | Category * | |
| Neighborhood | neighborhood | Category * | |
| Subject tags | subjectTags | Category * | TO ADD |

**Relationships**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Founded by | orgFoundedBy | Relation → Person * | |
| Board members | boardMembers | Relation → Person * | |
| Associated persons | orgAssociatedPersons | Relation → Person * | |
| Parent organization | parentOrganization | Relation → Org | |
| Has parent org | hasParentOrg | Lightswitch | |
| Sub-boards | subBoards | Relation → Org * | |
| Has sub-boards | hasSubBoards | Lightswitch | |
| Events | orgEvents | Relation ↔ Event * | |

---

### PLACE
*Physical locations — geographic, architectural, natural features.*

**Identity**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Title | title | Title | ! |
| Aliases | placeAliases | Plain Text | Former names |
| Place type | placeType | Category | TO ADD — Road, Park, Building, Canyon, School, Neighborhood, Landmark, Body of Water, Ranch, Cemetery, Mine, Oil Field, Railway Station |

**Named After** *(TO ADD)*
| Field | Handle | Type | Notes |
|---|---|---|---|
| Named after | namedAfter | Relation → Person * | Multi — some places honor multiple people |
| Named after notes | namedAfterNotes | Rich Text | Context for the naming |
| Named after date | namedAfterDate | Plain Text | When officially named |

**Haunted / Folk History** *(TO ADD)*
| Field | Handle | Type | Notes |
|---|---|---|---|
| Allegedly haunted | allegedlyHaunted | Lightswitch | Enables Haunted Places page |
| Haunted notes | hauntedNotes | Rich Text | The legend/story |
| Haunted sources | hauntedSources | Plain Text | Where the claim originates |

**Location**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Address | placeAddress | Plain Text | ? |
| Latitude | placeLat | Number | ? |
| Longitude | placeLng | Number | ? |
| Bounding box | placeBounds | Plain Text | TO ADD — for areas not just points |

**Landmark Fields**
| Field | Handle | Type | Notes |
|---|---|---|---|
| CHL number | placeChlNumber | Plain Text | California Historical Landmark # |
| CHL URL | placeChlUrl | URL | |
| SCVHL checkbox | placeScvhlCheckbox | Lightswitch | SCV Historical Landmark |
| SCVHL URL | placeScvhlUrl | URL | |

**Content**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Body | body | Rich Text | TO ADD — places need descriptions |
| Featured | placeFeatured | Lightswitch | Highlight on map |
| Legacy URL | placeLegacyUrl | Plain Text | |
| Wikipedia URL | placeWikipediaUrl | URL | |

**Media**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Featured image | featuredImage | Asset | TO ADD |

**Taxonomy**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Historical era | historicalEra | Category * | |
| Historical period | historicalPeriod | Category * | |
| Neighborhood | neighborhood | Category * | |
| Subject tags | subjectTags | Category * | TO ADD |
| Confidence level | confidenceLevel | Category | TO ADD |
| Specific year | specificYear | Plain Text | TO ADD |

**Relationships**
| Field | Handle | Type | Notes |
|---|---|---|---|
| People | placePeople | Relation ↔ Person * | |
| Organizations | placeOrganizations | Relation ↔ Org * | |
| Events | placeEvents | Relation ↔ Event * | |
| Articles | placeArticles | Relation ↔ Article * | |

---

### EVENT
*Dated occurrences — battles, discoveries, elections, disasters, ceremonies.*

**Identity**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Title | title | Title | ! |
| Event date | eventDate | Plain Text | |
| Event date start | eventDateStart | Plain Text | For multi-day events |
| Event date end | eventDateEnd | Plain Text | |
| Recurring | eventRecurring | Lightswitch | |
| Frequency | eventFrequency | Plain Text | "Annual" |
| Next occurrence | eventNextOccurrence | Plain Text | |
| Significance | eventSignificance | Plain Text | Brief descriptor |

**Content** *(TO ADD)*
| Field | Handle | Type | Notes |
|---|---|---|---|
| Body | body | Rich Text | TO ADD — events need descriptions |
| Featured image | featuredImage | Asset | TO ADD |

**External**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Wikipedia URL | eventWikipediaUrl | URL | ? |
| Legacy URL | eventLegacyUrl | Plain Text | ? |

**Taxonomy** *(TO ADD)*
| Field | Handle | Type | Notes |
|---|---|---|---|
| Historical era | historicalEra | Category * | TO ADD |
| Historical period | historicalPeriod | Category * | TO ADD |
| Neighborhood | neighborhood | Category * | TO ADD |
| Subject tags | subjectTags | Category * | TO ADD |
| Specific year | specificYear | Plain Text | TO ADD |

**Relationships**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Persons | eventPersons | Relation ↔ Person * | |
| Organizations | eventOrganizations | Relation ↔ Org * | |
| Places | eventPlaces | Relation ↔ Place * | |
| Articles | eventArticles | Relation ↔ Article * | |
| Groups | eventGroups | Relation ↔ Group * | |

---

### ARTICLE
*Original written content — Leon's articles, historical pieces, primary source reprints.*

**Identity**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Title | title | Title | ! |
| Originally published title | originallyPublishedTitle | Plain Text | If different |
| Subheadline | subheadline | Plain Text | |

**Publication**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Original publish date | originalPublishDate | Plain Text | |
| Published by | publishedBy | Relation → Org | Newspaper/publication |
| Written by | writtenBy | Relation → Person * | Author(s) |
| Edited by | editedBy | Relation → Person * | |
| Publication details | publicationDetails | Plain Text | Volume, issue, page |
| Source line | sourceLine | Plain Text | Full attribution |
| Accession batch | accessionBatch | Plain Text | Leon's lw/sg/wr/ws/wy prefix |
| Batch sequence | batchSequence | Plain Text | |

**Content**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Body | body | Rich Text | ! |
| Fine print | finePrint | Plain Text | Copyright |
| Webmaster note top | webmasterNoteTop | Plain Text | |
| Webmaster note bottom | webmasterNoteBottom | Plain Text | |

**Archive**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Archive URL | archiveUrl | URL | Archive.org link |
| Legacy URL | legacyUrl | Plain Text | |

**Media**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Featured image | featuredImage | Asset | |

**Taxonomy**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Historical era | historicalEra | Category * | |
| Historical period | historicalPeriod | Category * | |
| Neighborhood | neighborhood | Category * | |
| Subject tags | subjectTags | Category * | TO ADD |
| Confidence level | confidenceLevel | Category | TO ADD |
| Specific year | specificYear | Plain Text | TO ADD |

**Relationships** *(bidirectional — TO ADD explicit fields)*
| Field | Handle | Type | Notes |
|---|---|---|---|
| Subject persons | subjectPersons | Relation ↔ Person * | TO ADD as explicit field |
| Subject organizations | subjectOrganizations | Relation ↔ Org * | TO ADD as explicit field |
| Subject places | subjectPlaces | Relation ↔ Place * | TO ADD as explicit field |
| Subject events | subjectEvents | Relation ↔ Event * | TO ADD as explicit field |
| Related articles | relatedArticles | Relation → Article * | |
| Part of collection | partOfCollection | Relation → Collection | |

---

### OBITUARY
*Published obituaries — community service, separate from Person biographical entries.*

**Decision: Keep as separate section.**
Many obituaries have no associated Person record and never will. Obituary is its own content type with its own submission workflow.

**Identity**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Title | title | Title | Usually deceased's name |
| Subject | obitSubject | Relation → Person | ? — link to Person if record exists |

**Publication**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Date of death | obitDateOfDeath | Plain Text | |
| Date published | obitDatePublished | Plain Text | |
| Published in | obitPublishedIn | Relation → Org | |
| Legacy URL | obitLegacyUrl | Plain Text | |
| Find A Grave URL | obitGraveUrl | URL | |

**Content**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Body | body | Rich Text | The obituary text |
| Webmaster note top | obitWebmasterNoteTop | Plain Text | |
| Webmaster note bottom | obitWebmasterNoteBottom | Plain Text | |

**Relationships**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Related persons | obitRelatedPersons | Relation → Person * | Family named in obit |
| Group | obitGroup | Relation → Group | |

**Submission System** *(TO BUILD)*
- Public form at /submit/obituary
- Fields: name, dates, survived by, life summary, photo upload, submitter contact
- Submissions land in Craft review queue
- Approved obits auto-create Person stub if none exists
- Submitter receives email confirmation
- Free community service — restore Leon's original offering

---

### GROUP
*Informal or cultural groups — posses, clubs, bands, crews, families.*

**Identity**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Title | title | Title | ! |
| Aliases | groupAliases | Plain Text | |

**Dates**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Date start | groupDateStart | Plain Text | |
| Date end | groupDateEnd | Plain Text | |

**Content** *(gaps to fill)*
| Field | Handle | Type | Notes |
|---|---|---|---|
| Body | body | Rich Text | TO ADD |
| Featured image | featuredImage | Asset | TO ADD |
| Legacy URL | groupLegacyUrl | Plain Text | |
| Website | groupWebsite | URL | |

**Taxonomy** *(TO ADD)*
| Field | Handle | Type | Notes |
|---|---|---|---|
| Historical era | historicalEra | Category * | TO ADD |
| Historical period | historicalPeriod | Category * | TO ADD |
| Neighborhood | neighborhood | Category * | TO ADD |
| Subject tags | subjectTags | Category * | TO ADD |

**Relationships**
| Field | Handle | Type | Notes |
|---|---|---|---|
| Persons | groupPersons | Relation ↔ Person * | |
| Events | groupEvents | Relation ↔ Event * | |

---

### MILITARY PROFILE
*TO BE MERGED INTO PERSON before Articles import.*

See Person entry type above for merged field list.

**Migration plan:**
1. Build migration script
2. For each Military Profile: find linked Person via mpSubject
3. Copy all military fields to Person, set hasMilitaryService = true
4. If no linked Person exists: create Person stub first
5. Delete Military Profile record after confirmed migration
6. Deprecate Military Profile section after all records migrated

---

### COLLECTION
*Series — groups of related articles.*

| Field | Handle | Type | Notes |
|---|---|---|---|
| Title | title | Title | ! |
| Body | body | Rich Text | Series description |
| Written by | writtenBy | Relation → Person * | |
| Published by | publishedBy | Relation → Org | |
| Original publish date | originalPublishDate | Plain Text | |
| Featured image | featuredImage | Asset | |
| Historical era | historicalEra | Category * | TO ADD |
| Neighborhood | neighborhood | Category * | TO ADD |
| Subject tags | subjectTags | Category * | TO ADD |
| Collection groups | collectionGroups | Plain Text | PURPOSE UNCLEAR — needs Leon input |

---

## New Entry Types (TO BUILD)

---

### YEARBOOK *(new)*

| Field | Handle | Type | Notes |
|---|---|---|---|
| Title | title | Title | "Hart High School 1965" |
| School | yearbookSchool | Relation → Organization | ! |
| Year | yearbookYear | Number | ! |
| Featured image | featuredImage | Asset | Cover photo |
| Body | body | Rich Text | Edition notes |
| Archive.org URL | archiveUrl | URL | Full scan link |
| Students | yearbookStudents | Relation → Person * | People in this yearbook |
| Historical era | historicalEra | Category * | |
| Historical period | historicalPeriod | Category * | |

---

### PHOTOGRAPH *(new — HIGH PRIORITY)*

| Field | Handle | Type | Notes |
|---|---|---|---|
| Title | title | Title | Descriptive caption |
| Photo | featuredImage | Asset | ! |
| Donor code | photoSourceCode | Plain Text | Two-letter prefix |
| Sequence number | photoSequence | Plain Text | Four-digit sequence |
| Date taken | photoDate | Plain Text | Flexible |
| Photographer | photographer | Relation → Person | ? |
| Extended caption | photoCaptionExt | Rich Text | |
| Photo credit | photoCredit | Plain Text | Attribution |
| Sources | photoSources | Relation → Org * | Donating institution |
| Archive.org URL | archiveUrl | URL | High-res version |
| Condition notes | photoCondition | Plain Text | |
| Copyright status | photoCopyright | Plain Text | |
| Body | body | Rich Text | Extended description |
| Historical era | historicalEra | Category * | |
| Historical period | historicalPeriod | Category * | |
| Neighborhood | neighborhood | Category * | |
| Subject tags | subjectTags | Category * | |
| Depicted persons | photoPeople | Relation → Person * | |
| Depicted places | photoPlaces | Relation → Place * | |
| Depicted organizations | photoOrganizations | Relation → Org * | |
| Depicted events | photoEvents | Relation → Event * | |
| Related articles | photoArticles | Relation → Article * | |
| Related groups | photoGroups | Relation → Group * | |

---

### DOCUMENT *(new)*

| Field | Handle | Type | Notes |
|---|---|---|---|
| Title | title | Title | |
| Document type | documentType | Category | Deed, Letter, Map, Newspaper clipping, Court record, Census record, Government doc, Telegram, Diary |
| Date | documentDate | Plain Text | |
| Source institution | sourceInstitution | Relation → Org | |
| Transcription | body | Rich Text | Full text |
| Archive.org URL | archiveUrl | URL | |
| Copyright status | documentCopyright | Plain Text | |
| Access notes | documentAccess | Plain Text | |
| Featured image | featuredImage | Asset | Scan thumbnail |
| Historical era | historicalEra | Category * | |
| Historical period | historicalPeriod | Category * | |
| Subject tags | subjectTags | Category * | |
| Confidence level | confidenceLevel | Category | |
| Related persons | documentPersons | Relation → Person * | |
| Related places | documentPlaces | Relation → Place * | |
| Related organizations | documentOrganizations | Relation → Org * | |
| Related events | documentEvents | Relation → Event * | |

---

### ORAL HISTORY *(new — URGENT)*

| Field | Handle | Type | Notes |
|---|---|---|---|
| Title | title | Title | "Interview with [Name], [Year]" |
| Interviewee | ohInterviewee | Relation → Person | ! |
| Interviewer | ohInterviewer | Relation → Person | |
| Date recorded | ohDateRecorded | Plain Text | |
| Duration | ohDuration | Plain Text | "45 minutes" |
| Transcript | body | Rich Text | Full text |
| Audio URL | ohAudioUrl | URL | Archive.org or SCVTV |
| Video URL | ohVideoUrl | URL | |
| Topics | ohTopics | Category * | Subject taxonomy |
| Featured image | featuredImage | Asset | |
| Archive.org URL | archiveUrl | URL | |
| Recording notes | ohNotes | Plain Text | |
| Historical era | historicalEra | Category * | |
| Historical period | historicalPeriod | Category * | |
| Neighborhood | neighborhood | Category * | |
| Subject tags | subjectTags | Category * | |
| Related persons | ohPersons | Relation → Person * | People mentioned |
| Related places | ohPlaces | Relation → Place * | |
| Related organizations | ohOrganizations | Relation → Org * | |
| Related events | ohEvents | Relation → Event * | |
| Related articles | ohArticles | Relation → Article * | |

---

## Taxonomy

---

### Historical Era
| Slug | Label |
|---|---|
| tataviam-pre-contact | Tataviam / Pre-Contact |
| spanish-colonial-1769-1821 | Spanish Colonial (1769–1821) |
| mexican-rancho-era-1821-1848 | Mexican Rancho Era (1821–1848) |
| american-frontier-1848-1876 | American Frontier (1848–1876) |
| railroad-oil-era-1876-1910 | Railroad & Oil Era (1876–1910) |
| early-twentieth-century-1910-1945 | Early 20th Century (1910–1945) |
| postwar-boom-1945-1965 | Postwar Boom (1945–1965) |
| incorporation-era-1965-1990 | Incorporation Era (1965–1990) |
| contemporary-2010-present | Contemporary (2010–Present) |

---

### Historical Period
pre-1850, 1850-1899, 1900-1919, 1920-1929, 1930-1939, 1940-1949, 1950-1959, 1960-1969, 1970-1979, 1980-1989, 1990-1999, 2000-2009, 2010-2019, 2020-present

Add specificYear plain text field on all entries alongside these buckets.

---

### Neighborhood
*Needs full audit — current list may be incomplete*

Current confirmed: camulos, canyon-country, castaic, lebec, newhall, piru, placerita-canyon, san-francisquito-canyon, saugus, soledad-canyon, tejon, valencia

Likely missing: agua-dulce, acton, bouquet-canyon, green-valley, hasley-canyon, live-oak, mint-canyon, sand-canyon, stevenson-ranch, tourney-road-corridor

---

### Subject Tags *(TO ADD)*
gold-rush, water-history, indigenous-history, railroads, oil-industry, film-industry, agriculture, architecture, military-history, politics-government, law-enforcement, education, religion, commerce-trade, land-grants, natural-disasters, conservation, arts-culture

---

### Confidence Level *(TO ADD)*
| Slug | Label | Meaning |
|---|---|---|
| confirmed | Confirmed | Primary source document exists |
| probable | Probable | Strong secondary evidence |
| uncertain | Uncertain | Limited or circumstantial evidence |
| disputed | Disputed | Conflicting sources exist |

---

### Organization Subtype *(TO ADD)*
educational-institution, rancho, mission, government, business, nonprofit, media, military-unit, cultural-organization, religious-institution

---

### Place Type *(TO ADD)*
road, park, building, canyon, school, neighborhood, landmark, body-of-water, ranch, cemetery, mine, oil-field, railway-station

---

### Document Type *(TO ADD)*
deed, letter, map, newspaper-clipping, court-record, census-record, government-document, telegram, diary, advertisement, contract

---

## Open Questions

1. **Neighborhood audit** — current taxonomy list is likely incomplete. Leon should review against all known SCV neighborhoods before Places import begins.

2. **Collection groups field** — `collectionGroups` on Collection has unclear purpose. Needs clarification from Leon before templating Collections.

3. **Yearbook entry point** — tag yearbooks on Person (person-first) or tag persons on Yearbook (yearbook-first)? Both work — pick one as primary entry point for data entry workflow.

4. **Obituary auto-create Person stub** — when an obituary is approved, should it auto-create a minimal Person record? Or leave that as a manual editorial decision? Recommend: manual, to avoid low-quality stubs.

5. **specificYear field** — add as plain text to all entry types alongside decade buckets? Recommend: yes.

6. **Photograph entry type timing** — when do we create the Photograph section? Recommend: after core templates are live, before bulk photo import.

7. **Article lat/lng fields** — `articleLat` and `articleLng` exist on Article. What is the intended use? Possibly the location described in the article? Needs clarification.

8. **depictsPlace field on Article** — `depictsPlace` exists. Relation to Place? Needs confirmation of handle and type.

9. **Haunted places content strategy** — who writes the hauntedNotes content? Leon? Community submissions? Suggest: Leon writes core entries, community can submit via a form similar to obituary submission.

10. **Military Profile migration timing** — must happen before Articles import to avoid orphaned relatedMilitary references.


---

## Decisions — 2026-04-15 Session 4 Continued

### Group Section — Revised Purpose and Types

Group section handles all informal human associations. Add groupType taxonomy to differentiate:

| groupType slug | Label | Examples |
|---|---|---|
| family | Family | del Valle family, Newhall family, Acosta family |
| cultural-group | Cultural Group | Tataviam people, Californio community, Chinese railroad workers |
| informal-association | Informal Association | California Rangers posse, Bennett-Arcan Party, gold mining crew |
| expedition-party | Expedition Party | Portolá Expedition, De Anza Expedition |
| military-unit-informal | Military Unit (Informal) | California Battalion, irregular militia |

Ranchos are Organizations (subtype: Rancho) not Groups — they had legal standing, land grants, and formal structure.

Walk of Fame inductees are Award recipients (Award entry type) not a Group.

For Tataviam and Indigenous Cultural Groups:
- Use preferred self-designation always
- Include note linking to living Tataviam Nation community
- For sensitive site locations — describe without publishing exact coordinates
- Develop Indigenous taxonomy in consultation with community representatives

### Cemetery Records — Deferred but Modeled

Low priority to build. When built:
- Cemetery Record links to Person (pre-filled relation)
- Fields: cemetery (relation to Place), plot/section, inscription text, date interred, marker photo, Find A Grave URL
- Find A Grave URL stays on Person as external — Cemetery Record is richer internal version
- Appears in Person sidebar alongside Obituary as a third death-records card

### Obituary — Family and Group Connections Confirmed

Add to Obituary:
- obitGroups: Relation → Group * (family groups, cultural groups)
- obitFamilyConnections: Relation → Person * (rename/clarify existing obitRelatedPersons)
Obituary sidebar on Person: already works via personObituaries relation — confirm template surfaces it correctly.

### On This Day — Data-Driven Implementation

Add monthDay field (Plain Text, MM-DD format) to all entry types with significant dates.
Populated during data entry or via migration script parsing existing date fields.

Craft query on On This Day page:
- Born today: Persons where birthDate monthDay matches
- Died today: Persons where deathDate monthDay matches
- Founded today: Organizations where dateFounded monthDay matches
- Happened today: Events where eventDate monthDay matches
- Disaster anniversary: Events/Places with disaster connection

This is genuinely dynamic and data-driven — superior to KHTS manual approach.
St. Francis Dam (March 12, 1928) is a prime example — every March 12 surfaces dam, victims, places, articles.

### St. Francis Dam — Long-Term Merge

Merge Leon's standalone St. Francis Dam site into SCVHistory as a special section.
Low priority to build but capture in model now.
Significant enough for its own landing page aggregating: event, victims (War Memorial style), places, articles, primary sources, photographs.

### Transportation Routes — Use Place Instead

Streets, canyons, roads = Place entries with placeType: Road or Canyon.
namedAfter field handles the naming connection.
Smyth Drive → Place → namedAfter: Clyde Smyth → auto-appears on Person page.
No separate Route entry type needed at SCV scale.

### Water Infrastructure — Add to Place

Add to Place entry type:
- waterInfrastructure: Lightswitch
- waterType: Category (Dam, Reservoir, Aqueduct, Canal, Spring, Well, Treatment Plant)
- capacityNotes: Plain Text
- constructionDate: Plain Text
- operatorOrg: Relation → Organization

### Awards and Honors — New Entry Type (Medium Priority)

Award entry type:
- Title (award name)
- Awarding organization (relation to Org)
- Year established
- Body (history of award)
- Featured image
- Recipients: Relation → Person * with year field

Walk of Western Stars = one Award entry, all inductees as recipients.
SCV Man/Woman of the Year = one Award entry per gender or combined.
Person sidebar shows awards received via reverse relationship.

### Film and Media Productions — Deferred (Fun, Not Urgent)

Add when core archive is complete. Will require significant research.
Place entries handle filming locations via placeType and subject tags (film-industry).
Productions connect places → persons → organizations → eras.

### Natural Disasters — Use Event Section with Disaster Type

Add to Event taxonomy: eventType with value "Natural Disaster / Emergency"
Add disaster-specific fields to Event:
- disasterType: Category (Fire, Flood, Earthquake, Dam Failure, Landslide, Drought, Industrial Accident)
- casualtyCount: Number
- propertyDamageNotes: Plain Text
- responseOrganizations: Relation → Org *

St. Francis Dam Disaster = Event entry with disasterType: Dam Failure.
Northridge Earthquake = Event entry.
Significant enough disasters get their own landing pages via template logic.

### Industry and Religion — Subject Tags

Both handled via Subject tags taxonomy — not separate entry types.

Industry tags: oil-industry, agriculture, ranching, film-industry, aerospace, real-estate, retail, mining, water-infrastructure, construction

Religion tags: catholicism, protestant, jewish-community, indigenous-spirituality, mission-catholicism

Applied to Person, Organization, Place, Event, Article entries as relevant.

### Genealogical Data Points — Add to Person (Low Priority)

Add to Person external links section:
- ancestryUrl: URL
- familySearchUrl: URL (LDS Family Search)
- findMyPastUrl: URL

Population strategy: community members with genealogical research can submit via contributor portal.

### Archaeological Sites — Very Low Priority

Capture in model only. Population strategy unclear — defer until academic partnership established.
Sensitive location data (burial grounds, sacred sites) requires special handling.

### Legal Records / Land Grants — Low Priority

Only if significant volume of records found. Land grant records likely best handled as Document entry type with documentType: Land Grant.


---

## Decisions — 2026-04-15 Session 4 Final

### Zodiac Sign — Calculated, Not Stored
Never store zodiac as a field. Calculate from birthDate using Twig macro or JavaScript.
Only display when birthDate is specific enough to determine sign with certainty.
"c. 1734" or "December 1962" = too ambiguous, do not display.
"May 13, 1825" = display Taurus.
Add to Person template as a subtle display element only.

### Shared Birthday Feature (Phase 10 — User Accounts)
When user has birthday in profile, query: Persons where birthDate monthDay matches user's monthDay.
Display as personal touch on user dashboard: "You share a birthday with Henry Mayo Newhall."
Simple, delightful, makes archive feel personal.

### On This Day — Hybrid Approach
Add to all entry types:
- onThisDayDate: Plain Text (MM-DD) — manually set by editor
- onThisDayNote: Plain Text — optional hook line "On this date in 1876..."

Query logic:
1. Use onThisDayDate if set (editorial control)
2. Fall back to monthDay parsed from birthDate, eventDate, dateFounded
3. Leon manually flags entries he knows have anniversary significance

Not all articles have clean date hooks — this hybrid respects that reality while enabling dynamic content where data exists.

### Occupation / Career — Replace Plain Text with Structured Roles Field

Remove: occupation (Plain Text) from Person
Replace with: roles (Matrix or Table field)

Roles field structure:
- roleTitle: Plain Text — "State Assemblymember"
- roleOrganization: Relation → Organization (optional)
- roleStart: Plain Text — year
- roleEnd: Plain Text — year or "present"
- primaryRole: Lightswitch — marks most SCV-significant role (one only)
- Sort order: draggable

Display rules:
- Cards and index listings show primaryRole only
- Full Person entry shows all roles in draggable order
- People index filterable by roleTitle (structured query)
- Organization pages show people who held roles there via reverse relation

Migration: parse existing occupation plain text strings into structured roles during content entry. Not a script — done editorially as each Person record is reviewed.

Example — Dante Acosta roles in order:
1. State Assemblymember (primaryRole: true) ← shows on cards
2. Santa Clarita City Council Member
3. College of the Canyons Board of Trustees
4. Political Staffer (McClintock, Boland, McKeon)
5. Financial Advisor

### Auto-Tagging / Entity Linking — Named Entity Recognition

Two-layer system:

Layer 1 — AI-assisted on save (editorial):
When article saved, body text sent to Claude API.
Claude returns detected entities with suggested archive matches.
Editor reviews and approves — adds explicit relation fields to article.
Builds structured data: subjectPersons, subjectPlaces, subjectOrganizations.

Layer 2 — Real-time tooltip in frontend (reader):
JavaScript scans article body text for known entity names.
Names found in archive get wrapped in tooltip trigger.
On hover/tap: mini-card appears inline — photo, name, DOB-DOD, one-line description, link to full entry.
Click navigates to full Person/Place/Org entry.
No editor involvement — purely frontend, powered by GraphQL API.

Both layers together:
- Layer 1 builds the knowledge graph (structured relations)
- Layer 2 surfaces it for readers (inline discovery)
- Every article becomes richer with every entity link
- Feeds On This Day, knowledge graph visualization, related content recommendations

Implementation notes:
- Layer 2 requires a JavaScript entity dictionary built from GraphQL query at page load
- Entity names must be exact match or fuzzy match (Levenshtein distance)
- Disambiguation needed — "Hart" could be William S. Hart or Hart High School
- Show tooltip only for entities with featuredImage + sufficient data for a useful card
- Mobile: tap to show, tap again to dismiss or navigate

### Ethnic and Cultural Heritage — Group Entry Type

Confirmed: use Group section with groupType: Cultural Group

Examples:
- Tataviam People (groupType: Cultural Group)
- Californio Community (groupType: Cultural Group)
- Chinese Railroad Workers (groupType: Cultural Group)
- Japanese American Farming Community (groupType: Cultural Group)
- Basque Settlers (groupType: Cultural Group)

Each gets full body content, associated persons, associated places, subject tags, and Indigenous taxonomy where applicable.
For Tataviam specifically: develop taxonomy terms in consultation with community representatives.


---

## Decisions — 2026-04-15 Technical Deep Dive

### Image and Asset Fields — Required for Compliance

Every Asset (image) needs these fields added to the Asset field layout in Craft:
- altText: Plain Text — REQUIRED for published images (accessibility)
- licenseType: Plain Text — copyright status
- creditLine: Plain Text — attribution line
- permissionsNotes: Plain Text — usage restrictions

Every Oral History entry: transcript field is REQUIRED, not optional.

### Living Persons — Privacy Protection

Add to Person entry type:
- isLivingPerson: Lightswitch
- When true: gates certain sensitive fields from public display (birth date exact, address, etc.)
- Template logic checks this flag before outputting sensitive data

### User-Submitted Content Fields

Add to any entry created via community submission:
- submittedBy: Relation → User
- submissionDate: DateTime
- approvedBy: Relation → User (editor)
- approvedDate: DateTime
- submissionNotes: Plain Text (editor notes)

### Legal Pages Required

| Page | Data Model Intersection |
|---|---|
| Privacy Policy | User data fields, cookie consent |
| Terms of Use | Submission workflow fields |
| Copyright and Attribution | licenseType and creditLine on every asset |
| Accessibility Statement | altText required fields, transcript fields |
| Data Sources | sourceLine and sourceInstitution fields on Article and Document |
| Corrections Policy | Correction request entry type |
| Takedown / Right to Be Forgotten | isLivingPerson flag, deletion workflow |
| Community Guidelines | User submission fields |
| Cookie Policy | Minimal — even if no tracking cookies |

### Accessibility — Data Model Requirements (WCAG 2.2 AA)

- altText: Plain Text — REQUIRED on all image assets (never optional)
- transcript: Rich Text — REQUIRED on all Oral History entries
- Entity tooltip system must use: role="tooltip", aria-describedby, keyboard navigation (Tab, Enter, Escape)
- Semantic HTML landmarks in all templates: main, article, section, nav, aside
- Color contrast: verify Gold #b8860b on white backgrounds — may fail 4.5:1 AA ratio for link text

### Import Pipeline — Entity-First Approach

Correct sequence for content migration:
1. Extract entities from all HTML files using Claude API (people, places, orgs, events)
2. Deduplicate, create canonical stubs in Craft
3. Build entity dictionary (name → Craft entry ID)
4. Import articles with auto-populated relation fields using dictionary
5. Human review pass — editor confirms/rejects suggested links
6. Enrich stub entities with full biographical data

Claude API prompt pattern for entity extraction:
Send HTML body, request JSON array of: name, type (person/place/org/event), brief description, date if mentioned.
Script checks against existing Craft records, creates stubs for new entities, flags matches for linking.

### Photo Tagging with Vision AI

Workflow for archive photo processing:
1. Claude vision API: era, setting type, people count, visible text, recognizable landmarks
2. Face recognition (AWS Rekognition or similar): compare against known Person portraits
3. NEVER auto-link faces — always human confirmation required
4. Store: recognitionConfidence (Number), recognitionSuggestion (Relation → Person), recognitionConfirmed (Lightswitch)
5. Add to Photograph entry type: recognitionConfidence, recognitionSuggestion, recognitionConfirmed

### Yearbook Reader — Recommended Approach

Primary: Archive.org BookReader (free, open source, handles page-turning, zoom, search if OCR exists)
Craft entry = metadata layer, Archive.org = file host
Embed via Archive.org JavaScript embed

Advanced (Phase 3): IIIF + Universal Viewer
- Enables bounding box annotations linking page regions to Person entries
- Tag a face in the 1965 Hart yearbook → links to Person entry
- Academic standard — required for institutional partnerships
- Higher complexity, defer to Phase 3

### Open-Source Starter Kit — Architecture

SCV-specific content stays private. Generic infrastructure goes public.

Public repo: OpenArchive Starter Kit
- Craft CMS project config (entry types, fields, relations) — taxonomy terms as importable JSON
- Base templates with generic variable names
- Import pipeline scripts with external config files for field mappings
- Entity linking JavaScript layer
- DATA_MODEL.md as a documentation standard
- Setup guide for new regional archives
- MIT license for code, CC-BY for documentation

Private repo: SCVHistory (starksocialmedia/scvhistory)
- Leon's content
- SCV-specific taxonomy terms
- SCV design system

Immediate action: Add CONTRIBUTING.md and README.md note that project will be open-sourced as starter kit after Phase 1 launch.

Grant opportunities: NEH, Mellon Foundation, California Humanities — all fund this type of civic infrastructure. CSUN partnership + Code for America credential + 30-year archive proof of concept = strong application.


---

## IIIF Integration Architecture — 2026-04-15

### Core Principle: Manifests Are Derived, Not Stored

IIIF Manifests, Canvases, Sequences, and Ranges are generated on-the-fly from Craft entities.
Never store Manifests as database records — synchronization problems and duplication.
Archive.org generates Manifests automatically for hosted items — reference theirs directly.
Craft entries ARE the authoritative data. Manifests are a view of that data.

### IIIF Fields on Media Entry Types (Yearbook, Photograph, Document)

Add to all media entry types:
- iiifManifestUrl: URL — Archive.org manifest or own generated endpoint
- iiifHostedOnArchiveOrg: Lightswitch
- iiifArchiveOrgIdentifier: Plain Text — Archive.org item ID

Archive.org URL patterns from identifier:
- Base image: https://archive.org/services/img/{identifier}/full/max/0/default.jpg
- IIIF tile: https://iiif.archive.org/iiif/3/{identifier}:{page}/full/max/0/default.jpg
- Manifest: https://iiif.archive.org/iiif/3/{identifier}/manifest.json

Own generated Manifest endpoint:
https://scvhistory.com/iiif/3/{entry-slug}/manifest.json
Craft template at templates/iiif/manifest.twig outputs JSON-LD IIIF Presentation API 3.0

### Annotation Entry Type (New — For IIIF Entity Linking)

Stores bounding box annotations linking image regions to Craft entities.
Fed into generated IIIF Manifests as Web Annotations.
Confirmed by editor — never auto-published.

Fields:
- annotationTarget: URL — Canvas/image being annotated
- annotationRegion: Plain Text — "xywh=120,340,80,100" bounding box coordinates
- annotatedPerson: Relation → Person (optional)
- annotatedPlace: Relation → Place (optional)
- annotatedOrg: Relation → Organization (optional)
- annotationNote: Plain Text
- recognitionConfidence: Number — face recognition confidence score
- recognitionMethod: Plain Text — "manual", "face-recognition", "ocr-entity-extraction"
- confirmedBy: Relation → User

### What Lives Where

| Asset | Location |
|---|---|
| High-res image tiles | Archive.org (free, CDN, permanent) |
| Binary files (PDFs, audio) | Archive.org |
| IIIF tile server | Archive.org (native support) |
| OCR text layer | Archive.org (auto-generated) |
| Auto-generated Manifests | Archive.org |
| All metadata | Craft CMS (scvhistory.com) |
| Entity relations | Craft CMS |
| Annotation records | Craft CMS |
| Rights information | Craft CMS |
| Custom Manifests | Generated by Craft template |
| Presentation logic | Craft templates |

### IIIF Viewer Recommendation

Mirador 3 over Universal Viewer for WCAG 2.2 AA compliance.
Mirador 3 has better keyboard navigation, screen reader announcements, focus management.
Embeds as a JavaScript component in Craft templates.

### IIIF Accessibility Fields in Manifests

IIIF Manifest fields that map to WCAG requirements:
- label → screen reader title for page/image
- summary → descriptive text alternative for whole object
- metadata → structured facts (date, creator, rights)
- requiredStatement → attribution and rights

These fields populated from Craft entry data when generating Manifest.

### Yearbook IIIF Pipeline

1. Upload scan to Archive.org — auto-generates IIIF Manifest and OCR layer
2. Download OCR text: https://archive.org/download/{id}/{id}_djvu.xml
3. Run entity extraction on OCR text (same Claude API pipeline as articles)
4. Entity mentions → Annotation records with page/region coordinates
5. Face recognition pass → additional Annotation suggestions (editor-confirmed)
6. Annotations feed into generated Manifest as Web Annotations
7. Mirador 3 renders clickable portrait overlays linked to Person entries

Result: every page of every yearbook is searchable, entity-linked, and accessible.

