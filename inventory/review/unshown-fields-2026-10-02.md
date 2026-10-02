# Fields that hold data and do not show, 2 October 2026

Nathan asked which fields exist on records and are never shown. Every field is now classed in `scripts/import/field-display.json`, and `check_rendered_fields.php` checks the page fields on rendered pages (DEPLOY-RUNBOOK section 9). Counts are records holding a value. Records with no page of their own (candidacies, office holdings, educations) show through their person and election pages; the checker reads only sections with pages, so their fields are covered by the classes here, not by a page check.

## Fixed today (applied, never rendered)

- **editorNotes** on persons, organizations, places, war memorials and the other record pages: only articles, documents and photographs rendered them. 18 records' notes were invisible, among them the Correction notes on Ygnacio del Valle and others.
- **Top-position editor notes on documents**: only bottom notes rendered (Perkins's 1957 history, "Reading this document").
- **personWebmasterNoteBottom** when a body is withheld: the note was inside the body's condition (Vasquez).
- **The memorial header's DIED** printed the incident date, so Edward Guy North died in September 1918. It now prints the death date, with INCIDENT beside it where they differ.

## Should show, not built yet (gap)

| Field | Records | Where | Why it matters |
|---|---|---|---|
| `provenanceKind` | 4329 | assets 4329 | how an image came to the archive; the /media page does not say |
| `electionKind` | 95 | elections 95 | general, special or primary: a reader needs to know which |
| `seatsUpEvidence` | 95 | elections 95 | how the seats up are known (a confidence level) |
| `candidacyDistrict` | 94 | candidacies 94 | the district a candidate ran in, now that council and board seats are by district |
| `startEvidence` | 56 | officeHoldings 55, events 1 | how a start date is known, for an office or an event (a confidence level) |
| `personAliases` | 53 | persons 53 | other names a person is known by; only in JSON-LD |
| `endEvidence` | 48 | officeHoldings 48 | how an office end is known (a confidence level, which the content rules say a claim keeps) |
| `acquiredDate` | 21 | assets 21 | when an image was acquired; not on /media |
| `birthEvidence` | 17 | persons 17 | how a birth date is known (confidence) |
| `votesAtPrecinct` | 13 | elections 13 | mail and precinct counts; the election page uses them only to flag source faults |
| `votesByMail` | 13 | elections 13 | mail and precinct counts; the election page uses them only to flag source faults |
| `deathEvidence` | 12 | persons 12 | how a death date is known (confidence) |
| `contentCredentials` | 11 | assets 11 | an edited image's content credential; DATA-MODEL says this field is the only place it survives, and no page shows it |
| `educationEvidence` | 10 | educations 10 | how a school attendance is known (confidence) |
| `sourceUrl` | 5 | assets 5 | where an image came from; not on /media |
| `burialEvidence` | 4 | persons 4 | how a burial is known (confidence) |
| `hauntedAccount` | 2 | places 2 | as hauntedStatus |
| `hauntedSource` | 2 | places 2 | as hauntedStatus |
| `hauntedStatus` | 2 | places 2 | the haunted-places layer: shown, or kept back as a later phase? Nathan to decide |
| `succeededBy` | 2 | organizations 2 | what a dissolved body became (Newhall County Water District to SCV Water) |
| `precededBy` | 1 | organizations 1 | what a body grew out of |
| `relatedArticles` | 1 | articles 1 | articles an article points to |

## Shown, but not every value (a recorded reason)

| Field | Reason |
|---|---|
| `collectionParts` | part headings show on book collections only; a series (Reynolds) lists its chapters without them |
| `derivedImageLinks` | listed on articles only; photographs and places do not show derived images yet |
| `electionDistrict` | printed as its short label, "Division 3" or "Trustee Area 2" |
| `finePrint` | the site-wide rights line is in the footer, not under each record (_partials/record/fine-print.twig); other fine print shows |
| `placeLegacyUrl` | the source link shows legacyUrl when a place has both |

## Never shown, on purpose (internal)

| Field | Records | Why |
|---|---|---|
| `photoSequence` | 1544 | steers the template (grouping, labels or what is published), not printed as its value |
| `recordProvenance` | 825 | which script or import wrote the record; the audit trail, not content |
| `candidateKey` | 271 | the key that matches a candidate across elections |
| `recordDates` | 234 | feeds On This Day (calendar.json), not the record page |
| `roleMatch` | 80 | the text a role was matched on |
| `legacyHtml` | 54 | the legacy page as captured, kept for the record; the body is what shows |
| `bodyAuthorship` | 50 | steers the template (grouping, labels or what is published), not printed as its value |
| `placeType` | 48 | steers the template (grouping, labels or what is published), not printed as its value |
| `orgType` | 39 | steers the template (grouping, labels or what is published), not printed as its value |
| `authorBio` | 33 | shown with an author's byline on their writing, not on their record page |
| `districtKind` | 33 | steers the template (grouping, labels or what is published), not printed as its value |
| `collectionKind` | 14 | steers the template (grouping, labels or what is published), not printed as its value |
| `ncesId` | 11 | steers the template (grouping, labels or what is published), not printed as its value |
| `hasParentOrg` | 7 | a structural flag; the parent shows through its relation |
| `decidedBy` | 5 | sourceFaults are the archive's own review queue |

## Machine form only (schema)

`articlesAbout`, `birthDateEdtf`, `cdsCode`, `dateDissolvedEdtf`, `dateEdtf`, `dateEstablishedEdtf`, `dateFoundedEdtf`, `deathDateEdtf`, `ein`, `electionDateEdtf`, `eventDateEdtf`, `fullName`, `gnisId`, `originalPublishDateEdtf`, `photoDateEdtf`, `roleWikidataId`, `termEndEdtf`, `termStartEdtf`, `viafId`, `wikidataId`. Each is the JSON-LD or sorting form of something the page prints, or an identifier emitted as `sameAs`.

## Empty everywhere

`accessionBatch`, `articleThemes`, `batchSequence`, `collectionFrozen`, `educationYears`, `effectiveFrom`, `effectiveTo`, `graveCensus`, `parentOf`, `placeFeatured`, `relatedMilitary`, `seatCount`.

