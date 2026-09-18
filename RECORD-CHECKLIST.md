# Record checklist

What a complete record carries. Used by scripts/import/audit_records.php, which
reports every record against it. Required means the record is not finished
without it; expected means it should be there unless the source genuinely
lacks it; optional is a bonus.

## Every record

| Field | Level | Note |
|---|---|---|
| title | required | |
| body | required | the text itself |
| sourcePath | required | where it came from, with host |
| legacyKey | required | matches the legacy page, enables re-import |
| legacyUrl | required | root-relative path on the legacy site |
| neighborhood | expected | the communities it concerns |
| historicalEra | expected | drives Browse by era |
| featuredImage | optional | social card and thumbnails |
| recordDates | optional | feeds On This Day |
| recordImages | optional | |
| recordTags | optional | |

## Article

| Field | Level | Note |
|---|---|---|
| writtenBy | required | the author. An article with no author is not citable |
| originalPublishDate | required | as printed in the source |
| partOfCollection | expected | when it belongs to a series |
| subjectPerson | expected | people the article is about |
| depictsPlace | expected | places it concerns |
| subjectOrganization | optional | |
| publishedBy | expected | the newspaper or publisher |
| editedBy | optional | |
| subheadline | optional | |

## Person

| Field | Level | Note |
|---|---|---|
| occupation | expected | |
| birthDate | expected | |
| deathDate | expected | unless living |
| burialPlace | optional | |
| personGraveUrl | optional | |
| personWikipediaUrl | optional | |
| personAliases | optional | other names they appear under |

## Place

| Field | Level | Note |
|---|---|---|
| placeLat, placeLng | required | without coordinates it cannot be mapped |
| dateEstablished | expected | |
| placeAddress | optional | |
| placeChlNumber | optional | California Historical Landmark |

## Organization

| Field | Level | Note |
|---|---|---|
| orgLat, orgLng | expected | |
| dateFounded | expected | |
| orgWebsite | optional | |

## War memorial

| Field | Level | Note |
|---|---|---|
| wmBranch | required | |
| wmConflict | required | |
| deathDate | required | |
| wmHomeOfRecord | expected | |
| wmRank | expected | |
| wmUnit | expected | |
| wmNarrative | expected | |
| wmIncidentDate, wmIncidentLocation | expected | |
| wmRelatedPerson | optional | family with a record of their own |

## Collection

| Field | Level | Note |
|---|---|---|
| writtenBy | required | |
| articlesInCollection | required | in reading order |
| collectionParts | expected | for a series long enough to have divisions |
| bandImage | expected | clean artwork, no lettering |

## Rules

An importer never invents a value to satisfy this list. A missing required
field is reported, not filled. The checklist measures the archive; it does not
license guessing.
