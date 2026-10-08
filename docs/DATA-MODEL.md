# The data model

Generated from the live schema by `scripts/import/generate_data_model.php` on 8 October 2026. Do not edit by hand: regenerate.

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

### Aliases serve two purposes

A name kept for a person does two jobs: it is shown, so a reader knows the other names someone
went by, and it is searched, so a reader who knows only that name finds them. A removal must
consider both (Nathan, 6 October 2026). On 5 October same-name forms (a full middle name, an
initial, a short form) came out of `personAliases` so the header shows only names that differ
from the title, and the site search then found nobody for "A.B. Perkins", "Bill Hart" or
"Joseph Messina". So:

- `personAliases`, shown as "Also known as": a different name a reader would know them by, a
  maiden or married name, a stage or pen name, a name a source prints that differs from the title.
- `personSearchNames`, searched, not shown: forms of their own name the title does not carry.
- A form that is another person's name in the archive goes in neither, or the search lands on the
  wrong man ("Francisco Lopez" is the gold discoverer, not Chico López).

## Transcription and interpretation

A source record keeps what the source says apart from what anyone says about it.

- **`body` is transcription.** The words of the item itself, verbatim, and
  nothing else: the 1899 reporter, the 1889 county history, Scofield's eulogy.
  A `[sic]` is kept where the page printed one and never added.
- **`webmasterNoteTop` is interpretation.** The webmaster's or a contributor's
  framing, commentary and argument about the item, verbatim as they wrote it.
- **`webmasterNoteBottom` is apparatus.** Credits, scan lines and notes on how
  the text was edited ("divided into paragraphs for ease of reading").

Where a legacy page never transcribed its item and wrote about it instead, the
body stays empty. A page about a death certificate is 2014 talking, not 1900,
and putting it in the body would publish the essay as the certificate. Facts the
essay quotes from the item go into `recordDates`, labelled as quoted and
unconfirmed until someone reads them off the scan.

A headline goes into `originallyPublishedTitle` only when the original printed
it. A headline the legacy site wrote for its own page is the page's, not the
item's.

## Evidence rates the claim, not the document

The evidence fields (`birthEvidence`, `deathEvidence`, `burialEvidence`,
`startEvidence`, `endEvidence`) say how good one claim is, not what kind of paper
it was found on.

- **A document is `certified` only for what its certifier attests.** A death
  certificate certifies the death. The birth date on it is an informant's
  statement, rated by who supplied it and how long after the event: usually
  `retrospective`.
- **`certified` requires a certificate the archive holds.** A certificate that a
  page or a book cites is `retrospective` until the document itself is in the
  archive.
- **A contradiction is not an evidence level.** A source that disagrees with
  itself keeps its rating; the doubt goes in the EDTF qualifier and a source
  fault. Charles Alexander Mentry's certificate gives 27 March 1847 and an age
  that counts back to 1848: `1847?-03-27`, `retrospective`, and a note.

`scripts/import/set_evidence_levels.php` audits every `certified` value against
this rule each time it runs.

### A compilation that omits without saying so: CEDA

The California Elections Data Archive (CEDA) compiles the counties' returns, and the
archive rates an outcome it gives as `roster`, never `certified` (council_ceda.php, 29
September 2026: a compilation lifts `derived` to `roster`). It is not complete, and it
does not say when it is not. The Hart district's Trustee Area 2 contest of 8 November
2022 (Bob Jensen Jr 11,639, Andrew Taban 5,736, certified on 5 December 2022) is in
the County's Statement of Votes Cast and in no CEDA file. Checked on 3 October 2026
against the County's statements for 2016, 2020, 2022 and 2024: of 33 valley candidate
contests in CEDA's scope, 32 are in CEDA and that one is not. CEDA's scope is counties,
cities, school and college districts and some community services districts; water
agencies are outside it, so their results come from the County's returns.

- **What CEDA holds keeps its `roster` rating.** The check found no wrong result, only
  a missing contest.
- **Absence from CEDA is not evidence that no contest was held.** A seat with no CEDA
  row is checked against the County's statement where the archive holds one, and is
  otherwise a gap, not a cancelled election. Years without a County statement in
  hand (before 2016, and 2018) are unchecked.

### What checking against the archive cannot catch

The audits that grade a sentence against the archive (`audit_generated_bodies.py`,
`audit_wordpress_records.py`, `trace_wordpress_facts.py`) look for a page that
holds the sentence's dates, numbers and names. They cannot tell whether that page
agrees with it. A sentence can match on every name and date and still say
something its source denies (Nathan, 3 October 2026).

The example: the Rancho El Tejon record said Edward Fitzgerald Beale "acquired the
rancho" in 1855 and consolidated some 270,000 acres. Jerry Reynolds, in the
archive, has Beale, 1855 and the Tejon in the same passage, so the sentence grades
"found"; but Reynolds says Beale bought Rancho La Liebre on 8 August 1855 and
added the Tejon later, 297,000 acres in all. The grade found the parts and missed
the contradiction.

So a match is a lead to a source, never a confirmation. A grade of "none" can
condemn a sentence (nothing anywhere supports it); a grade of "found" cannot clear
one. Only reading the source against the claim does, and that is what an evidence
level records: that someone read it. It is also why a doubtful body is withheld
whole rather than kept for the sentences that matched.

## Identifier schemes

| Field | Scheme | URL pattern | Wikidata property |
| --- | --- | --- | --- |
| `gnisId` | GNIS Feature ID | `https://edits.nationalmap.gov/apps/gaz-domestic/public/summary/{id}` | P590 |
| `wikidataId` | Wikidata item | `https://www.wikidata.org/wiki/{id}` | - |
| `viafId` | VIAF cluster | `https://viaf.org/viaf/{id}` | P214 |
| `bioguideId` | Biographical Directory of the U.S. Congress | `https://bioguide.congress.gov/search/bio/{id}` | P1157 |
| `imdbId` | IMDb | `https://www.imdb.com/name/{id}/` | P345 |
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
| `feedsInto` | school or district to the one its pupils go on to | local; held on the feeder, read from both ends |
| `educationPerson / educationSchool` | an education record joining a person to a school | alumniOf |
| `spouseOf` | person to person | spouse |
| `childOf` | person to person | parent |
| `siblingOf` | person to person | sibling |
| `personOrganizations` | person to organization | affiliation |
| `placePeople` | place to person | no direct term; emitted as about on the place |
| `derivedImageLinks` | record to record | local: images derived from a record, not curated for it |

### Kinship on the page

`childOf`, `siblingOf` and `spouseOf` are stored for anyone, and published only between two
historical people, by the test in `templates/_partials/record/historical.twig`, which treats
undetermined as living. The archive does not publish a family graph of living people.

**One exception, no wider** (Nathan, 29 September 2026): a parent, child or sibling link
publishes when both people hold documented public office, each with at least one
`officeHolding` record, and the relationship is itself sourced, in a footnote on the
holder's record that names the other person and the relation. Both conditions, never one;
never spouses. A father and son on the same council are public record, not private family
structure. The test is `publicKin` in the same file. **It takes effect only when
`officeHoldings` has records; until then it passes nobody.**

### No connection the sources do not make

The archive does not create a connection its sources do not make (Nathan, 5 October 2026).
A relation, a link, a shared tag or a sentence that puts two records side by side asserts that
they belong together, and a search engine reads it that way. Where no source joins them, the
archive does not either, however obvious the connection seems to whoever holds both. The
shooter's father's 2017 obituary, which does not mention the Saugus High School shooting, is
held with the obituaries in their own course and never related to the event. This is the
stronger form of the double-counting rule (docs/PROFILES.md: one account repeated is one
source): that rule stops the archive counting a connection twice; this one stops it making one
at all.

## Entry types

### Affiliations — `affiliations/affiliation`

17 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `affiliationPerson` | Entries | **local** | no external equivalent |
| `affiliationBody` | Entries | **local** | no external equivalent |
| `affiliationKind` | Dropdown | **local** | no external equivalent |
| `affiliationTitle` | PlainText | **local** | no external equivalent |
| `affiliationEnded` | Dropdown | **local** | no external equivalent |
| `termStart` | PlainText | **local** | no external equivalent |
| `termStartEdtf` | PlainText | **local** | no external equivalent |
| `termEnd` | PlainText | **local** | no external equivalent |
| `termEndEdtf` | PlainText | **local** | no external equivalent |
| `startEvidence` | Dropdown | **local** | no external equivalent |
| `endEvidence` | Dropdown | **local** | no external equivalent |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `editorNotes` | Table | **local** | no external equivalent |
| `researchLeads` | PlainText | **local** | no external equivalent |
| `recordProvenance` | PlainText | Dublin Core `provenance` | how the record came to exist |
| `recordDates` | Table | **local** | no external equivalent |

### Articles — `articles/article`

61 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `originallyPublishedTitle` | PlainText | schema.org `alternativeHeadline` | the title the piece first carried |
| `originalPublishDate` | PlainText | schema.org `datePublished` | printed form; EDTF in dateEdtf where it parses |
| `originalPublishDateEdtf` | PlainText | **local** | no external equivalent |
| `subheadline` | PlainText | schema.org `alternativeHeadline` |  |
| `sourceLine` | PlainText | **local** | no external equivalent |
| `heldAs` | Dropdown | **local** | no external equivalent |
| `legacyHeadline` | PlainText | **local** | no external equivalent |
| `legacyKey` | PlainText | **local** | no external equivalent |
| `legacyUrl` | PlainText | Dublin Core `source` | where it stood on the legacy site |
| `archiveUrl` | PlainText | Dublin Core `source` | Internet Archive capture |
| `accessionBatch` | PlainText | **local** | no external equivalent |
| `batchSequence` | PlainText | **local** | no external equivalent |
| `webmasterNoteTop` | PlainText | **local** | no external equivalent |
| `webmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `editorNotes` | Table | **local** | no external equivalent |
| `researchLeads` | PlainText | **local** | no external equivalent |
| `photoSources` | PlainText | **local** | no external equivalent |
| `finePrint` | PlainText | **local** | no external equivalent |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `culturalSensitivityNote` | PlainText | Dublin Core `rights` | approximate; it is a note, not a licence |
| `recordTags` | Categories | Dublin Core `subject` | local vocabulary |
| `writtenBy` | Entries | schema.org `author` | also dcterms:creator |
| `authorshipBasis` | Dropdown | **local** | no external equivalent |
| `authorshipBasisNote` | PlainText | **local** | no external equivalent |
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
| `catalogueCaption` | PlainText | **local** | no external equivalent |
| `photoSourceCode` | PlainText | **local** | no external equivalent |
| `photoSequence` | PlainText | **local** | no external equivalent |
| `creditRaw` | PlainText | **local** | no external equivalent |
| `creditDpi` | PlainText | **local** | no external equivalent |
| `creditProcess` | PlainText | **local** | no external equivalent |
| `creditKind` | PlainText | **local** | no external equivalent |
| `creditName` | PlainText | **local** | no external equivalent |
| `photoDate` | PlainText | **local** | no external equivalent |
| `photoDateEdtf` | PlainText | **local** | no external equivalent |
| `recordProvenance` | PlainText | Dublin Core `provenance` | how the record came to exist |

### Candidacies — `candidacies/Candidacy`

14 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `candidacyElection` | Entries | **local** | no external equivalent |
| `candidacyPerson` | Entries | **local** | no external equivalent |
| `nameAsPrinted` | PlainText | **local** | no external equivalent |
| `votesAsPrinted` | PlainText | **local** | no external equivalent |
| `votes` | Number | **local** | no external equivalent |
| `outcome` | Dropdown | **local** | no external equivalent |
| `outcomeEvidence` | Dropdown | **local** | no external equivalent |
| `candidacyDistrict` | Entries | **local** | no external equivalent |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `editorNotes` | Table | **local** | no external equivalent |
| `researchLeads` | PlainText | **local** | no external equivalent |
| `recordProvenance` | PlainText | Dublin Core `provenance` | how the record came to exist |
| `candidateKey` | PlainText | **local** | no external equivalent |

### Collections — `collections/collection`

36 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `webmasterNoteTop` | PlainText | **local** | no external equivalent |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `webmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `editorNotes` | Table | **local** | no external equivalent |
| `researchLeads` | PlainText | **local** | no external equivalent |
| `legacyUrl` | PlainText | Dublin Core `source` | where it stood on the legacy site |
| `archiveUrl` | PlainText | Dublin Core `source` | Internet Archive capture |
| `culturalSensitivityNote` | PlainText | Dublin Core `rights` | approximate; it is a note, not a licence |
| `recordTags` | Categories | Dublin Core `subject` | local vocabulary |
| `collectionIsMajor` | Lightswitch | **local** | no external equivalent |
| `collectionParts` | Table | **local** | no external equivalent |
| `writtenBy` | Entries | schema.org `author` | also dcterms:creator |
| `authorshipBasis` | Dropdown | **local** | no external equivalent |
| `authorshipBasisNote` | PlainText | **local** | no external equivalent |
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

36 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `webmasterNoteTop` | PlainText | **local** | no external equivalent |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `originallyPublishedTitle` | PlainText | schema.org `alternativeHeadline` | the title the piece first carried |
| `originalPublishDate` | PlainText | schema.org `datePublished` | printed form; EDTF in dateEdtf where it parses |
| `originalPublishDateEdtf` | PlainText | **local** | no external equivalent |
| `sourceLine` | PlainText | **local** | no external equivalent |
| `heldAs` | Dropdown | **local** | no external equivalent |
| `legacyHeadline` | PlainText | **local** | no external equivalent |
| `publishedBy` | Entries | schema.org `publisher` |  |
| `writtenBy` | Entries | schema.org `author` | also dcterms:creator |
| `authorshipBasis` | Dropdown | **local** | no external equivalent |
| `authorshipBasisNote` | PlainText | **local** | no external equivalent |
| `subjectPerson` | Entries | schema.org `about` | dcterms:subject |
| `subjectOrganization` | Entries | schema.org `about` | dcterms:subject |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `webmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `editorNotes` | Table | **local** | no external equivalent |
| `researchLeads` | PlainText | **local** | no external equivalent |
| `documentFiles` | Assets | **local** | no external equivalent |
| `recordTags` | Categories | Dublin Core `subject` | local vocabulary |
| `partOfCollection` | Entries | schema.org `isPartOf` | also dcterms:isPartOf |
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

### Education — `educations/education`

12 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `educationPerson` | Entries | schema.org `alumniOf` | emitted from the person: the person is an alumnus of the school |
| `educationSchool` | Entries | schema.org `alumniOf` | the school side of the same statement |
| `educationYears` | PlainText | **local** | no external equivalent |
| `educationYearsEdtf` | PlainText | **local** | no external equivalent |
| `classOf` | PlainText | **local** | the graduating class as printed; names a year, not a diploma |
| `educationOutcome` | Dropdown | **local** | graduated only where a source says so |
| `educationEvidence` | Dropdown | **local** | the officeHolding scale: certified, contemporary, retrospective, roster, uncited |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `editorNotes` | Table | **local** | no external equivalent |
| `researchLeads` | PlainText | **local** | no external equivalent |
| `recordProvenance` | PlainText | Dublin Core `provenance` | how the record came to exist |

### Elections — `elections/Election`

19 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `electionDate` | PlainText | **local** | no external equivalent |
| `electionDateEdtf` | PlainText | **local** | no external equivalent |
| `electionKind` | Dropdown | **local** | no external equivalent |
| `consolidatedWith` | PlainText | **local** | no external equivalent |
| `registeredVoters` | Number | **local** | no external equivalent |
| `ballotsCast` | Number | **local** | no external equivalent |
| `votesByMail` | Number | **local** | no external equivalent |
| `votesAtPrecinct` | Number | **local** | no external equivalent |
| `seatsUp` | Number | **local** | no external equivalent |
| `seatsUpEvidence` | Dropdown | **local** | no external equivalent |
| `sourceDocuments` | Entries | **local** | no external equivalent |
| `ballotMeasures` | Table | **local** | no external equivalent |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `editorNotes` | Table | **local** | no external equivalent |
| `researchLeads` | PlainText | **local** | no external equivalent |
| `recordProvenance` | PlainText | Dublin Core `provenance` | how the record came to exist |
| `electionBody` | Entries | **local** | no external equivalent |
| `electionDistrict` | Entries | **local** | no external equivalent |

### Events — `events/event`

46 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `webmasterNoteTop` | PlainText | **local** | no external equivalent |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `webmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `editorNotes` | Table | **local** | no external equivalent |
| `researchLeads` | PlainText | **local** | no external equivalent |
| `eventDate` | PlainText | **local** | no external equivalent |
| `eventDateEdtf` | PlainText | **local** | no external equivalent |
| `startEvidence` | Dropdown | **local** | no external equivalent |
| `eventDateStart` | PlainText | **local** | no external equivalent |
| `eventDateEnd` | PlainText | **local** | no external equivalent |
| `eventRecurring` | Lightswitch | **local** | no external equivalent |
| `eventFrequency` | PlainText | **local** | no external equivalent |
| `eventNextOccurrence` | PlainText | **local** | no external equivalent |
| `recordTags` | Categories | Dublin Core `subject` | local vocabulary |
| `wikidataId` | PlainText | Wikidata `QID` | emitted as schema.org sameAs |
| `eventConsequences` | Table | **local** | no external equivalent |
| `eventSignificance` | PlainText | **local** | no external equivalent |
| `eventChlNumber` | PlainText | **local** | no external equivalent |
| `eventWikipediaUrl` | Link | **local** | no external equivalent |
| `eventLegacyUrl` | PlainText | **local** | no external equivalent |
| `culturalSensitivityNote` | PlainText | Dublin Core `rights` | approximate; it is a note, not a licence |
| `eventPlaces` | Entries | **local** | no external equivalent |
| `eventPersons` | Entries | **local** | no external equivalent |
| `eventFallenOfficers` | Entries | **local** | no external equivalent |
| `eventOrganizations` | Entries | **local** | no external equivalent |
| `eventArticles` | Entries | **local** | no external equivalent |
| `sourceDocuments` | Entries | **local** | no external equivalent |
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
| `withheldBody` | PlainText | **local** | no external equivalent |

### Fallen officers — `fallenOfficers/fallenOfficer`

31 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `foAgency` | Entries | **local** | no external equivalent |
| `foRank` | PlainText | **local** | no external equivalent |
| `foAssignment` | PlainText | **local** | no external equivalent |
| `foBadge` | PlainText | **local** | no external equivalent |
| `deathDate` | PlainText | schema.org `deathDate` | printed form |
| `deathDateEdtf` | PlainText | **local** | no external equivalent |
| `foIncidentLocation` | PlainText | **local** | no external equivalent |
| `foCircumstances` | PlainText | **local** | no external equivalent |
| `foValleyTie` | Dropdown | **local** | no external equivalent |
| `birthDate` | PlainText | schema.org `birthDate` | printed form |
| `birthDateEdtf` | PlainText | **local** | no external equivalent |
| `burialPlace` | PlainText | schema.org `deathPlace` | burial rather than death, so the mapping is approximate |
| `foMemorials` | PlainText | **local** | no external equivalent |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `withheldBody` | PlainText | **local** | no external equivalent |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `factSources` | Table | **local** | no external equivalent |
| `editorNotes` | Table | **local** | no external equivalent |
| `researchLeads` | PlainText | **local** | no external equivalent |
| `webmasterNoteTop` | PlainText | **local** | no external equivalent |
| `webmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `recordImages` | Assets | schema.org `image` |  |
| `recordDocuments` | Assets | schema.org `associatedMedia` |  |
| `neighborhood` | Categories | Dublin Core `spatial` | local vocabulary of valley communities |
| `recordTags` | Categories | Dublin Core `subject` | local vocabulary |
| `legacyKey` | PlainText | **local** | no external equivalent |
| `legacyUrl` | PlainText | Dublin Core `source` | where it stood on the legacy site |
| `sourcePath` | PlainText | Dublin Core `source` |  |
| `legacyHtml` | PlainText | **local** | no external equivalent |

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

32 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `webmasterNoteTop` | PlainText | **local** | no external equivalent |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `webmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `editorNotes` | Table | **local** | no external equivalent |
| `researchLeads` | PlainText | **local** | no external equivalent |
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
| `withheldBody` | PlainText | **local** | no external equivalent |

### Obituaries — `obituaries/obituary`

36 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `editorNotes` | Table | **local** | no external equivalent |
| `researchLeads` | PlainText | **local** | no external equivalent |
| `publicationDetails` | PlainText | **local** | no external equivalent |
| `writtenBy` | Entries | schema.org `author` | also dcterms:creator |
| `authorshipBasis` | Dropdown | **local** | no external equivalent |
| `authorshipBasisNote` | PlainText | **local** | no external equivalent |
| `obitCompanions` | Entries | **local** | no external equivalent |
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

### Office Holdings — `officeHoldings/officeHolding`

21 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `holdingPerson` | Entries | **local** | no external equivalent |
| `holderName` | PlainText | **local** | no external equivalent |
| `holdingOffice` | Entries | **local** | no external equivalent |
| `holdingBody` | Entries | **local** | no external equivalent |
| `holdingDistrict` | Entries | **local** | no external equivalent |
| `termStart` | PlainText | **local** | no external equivalent |
| `termStartEdtf` | PlainText | **local** | no external equivalent |
| `termEnd` | PlainText | **local** | no external equivalent |
| `termEndEdtf` | PlainText | **local** | no external equivalent |
| `seatLabel` | PlainText | **local** | no external equivalent |
| `districtPlan` | Dropdown | **local** | no external equivalent |
| `selectionMethod` | Dropdown | **local** | no external equivalent |
| `howEnded` | Dropdown | **local** | no external equivalent |
| `startEvidence` | Dropdown | **local** | no external equivalent |
| `endEvidence` | Dropdown | **local** | no external equivalent |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `editorNotes` | Table | **local** | no external equivalent |
| `researchLeads` | PlainText | **local** | no external equivalent |
| `recordProvenance` | PlainText | Dublin Core `provenance` | how the record came to exist |
| `recordDates` | Table | **local** | no external equivalent |

### Organizations — `organizations/organization`

64 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `currentMark` | Assets | **local** | no external equivalent |
| `webmasterNoteTop` | PlainText | **local** | no external equivalent |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `webmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `editorNotes` | Table | **local** | no external equivalent |
| `researchLeads` | PlainText | **local** | no external equivalent |
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
| `enrolment` | Table | **local** | no external equivalent |
| `schoolIdentity` | Table | **local** | no external equivalent |
| `foundedEvidence` | Dropdown | **local** | no external equivalent |
| `namedFor` | Entries | **local** | no external equivalent |
| `namingNote` | PlainText | **local** | no external equivalent |
| `civicRole` | Dropdown | **local** | no external equivalent |
| `hasParentOrg` | Lightswitch | **local** | no external equivalent |
| `parentOrganization` | Entries | schema.org `parentOrganization` | inverse emitted as subOrganization |
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
| `orgLevel` | Dropdown | **local** | no external equivalent |
| `schoolLevel` | Dropdown | **local** | drives the schema.org School subtype |
| `gradeSpan` | PlainText | **local** | grades taught, as K-6 or 9-12, from the NCES directory unless footnoted |
| `feedsInto` | Entries | **local** | the district or school a body's pupils go on to; the grade of the move is the feeder's top grade plus one |
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
| `withheldBody` | PlainText | **local** | no external equivalent |

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

58 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `editorNotes` | Table | **local** | no external equivalent |
| `researchLeads` | PlainText | **local** | no external equivalent |
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
| `personSearchNames` | PlainText | **local** | no external equivalent |
| `wikidataId` | PlainText | Wikidata `QID` | emitted as schema.org sameAs |
| `viafId` | PlainText | VIAF `cluster ID` | Wikidata P214 |
| `birthEvidence` | Dropdown | **local** | no external equivalent |
| `deathEvidence` | Dropdown | **local** | no external equivalent |
| `burialEvidence` | Dropdown | **local** | no external equivalent |
| `bioguideId` | PlainText | Biographical Directory of the U.S. Congress `member ID` | Wikidata P1157; emitted as sameAs |
| `imdbId` | PlainText | IMDb `name ID` | Wikidata P345; emitted as sameAs |
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
the articles document: lived, worked, owned, built, founded, buried or acted here.
The connection has to be in the text. A name appearing in an article is not a
connection; it is a mention. Filming here is not one either: a location credit is
not a connection to the valley (Nathan, 3 October 2026, removing John Wayne).

**A person belongs in this archive for what they did here, and a profile leads
with that** (Nathan, 3 October 2026). The first paragraph says what the person did
in the valley; a career elsewhere gets a sentence at most, and the record's
Wikipedia link carries the rest. Where the archive holds nothing local, the profile
says so in a line rather than borrowing a career from elsewhere. Kit Carson's says
no source here places him in the valley; Junipero Serra's quotes Leon Worden that
there is no reason to believe he ever set foot in it.

**The connection has to be the person's own** (Nathan, 3 October 2026). A tie
that runs through a father's or husband's land or office is inherited, and does
not by itself earn a profile: Juventino del Valle ran Rancho Camulos, in Ventura
County, on land his father held, and his profile is a line saying so.

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
as a connection gets. So do Tom Mix, who lived in Newhall and made his early films
from there, and Charles Crocker, whose railroad came through. Kit Carson keeps his
record as Fremont's guide, though no source places him in the valley, and his
profile says so. John Wayne, kept on 21 September for filming at Melody Ranch,
was removed on 3 October: the name stays in the text, the record does not.

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

44 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `catalogueCaption` | PlainText | **local** | no external equivalent |
| `featuredImage` | Assets | schema.org `image` |  |
| `webmasterNoteTop` | PlainText | **local** | no external equivalent |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `webmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `editorNotes` | Table | **local** | no external equivalent |
| `researchLeads` | PlainText | **local** | no external equivalent |
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

60 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `webmasterNoteTop` | PlainText | **local** | no external equivalent |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `webmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `editorNotes` | Table | **local** | no external equivalent |
| `researchLeads` | PlainText | **local** | no external equivalent |
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
| `namedFor` | Entries | **local** | no external equivalent |
| `namingNote` | PlainText | **local** | no external equivalent |
| `placePeople` | Entries | **local** | no external equivalent |
| `placeOrganizations` | Entries | **local** | no external equivalent |
| `relatedPlaces` | Entries | **local** | no external equivalent |
| `placeEvents` | Entries | **local** | no external equivalent |
| `placeArticles` | Entries | **local** | no external equivalent |
| `derivedImageLinks` | Entries | **local** | no external equivalent |
| `placeWikipediaUrl` | Link | schema.org `sameAs` |  |
| `placeWebsite` | Link | schema.org `url` | the place's own official site |
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
| `withheldBody` | PlainText | **local** | no external equivalent |

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

### Source Faults — `sourceFaults/SourceFault`

11 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `asPrinted` | PlainText | **local** | no external equivalent |
| `reading` | PlainText | **local** | no external equivalent |
| `basis` | PlainText | **local** | no external equivalent |
| `decidedBy` | PlainText | **local** | no external equivalent |
| `faultRecord` | Entries | **local** | no external equivalent |
| `faultField` | PlainText | **local** | no external equivalent |
| `footnotes` | Table | Dublin Core `bibliographicCitation` | a table, one row per note |
| `footnotesOn` | Entries | schema.org `citation` |  |
| `editorNotes` | Table | **local** | no external equivalent |
| `researchLeads` | PlainText | **local** | no external equivalent |
| `recordProvenance` | PlainText | Dublin Core `provenance` | how the record came to exist |

### Submissions — `submissions/submission`

15 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `submissionStatus` | Dropdown | **local** | no external equivalent |
| `submissionRecord` | Entries | **local** | no external equivalent |
| `submissionKind` | Dropdown | **local** | no external equivalent |
| `submissionWho` | PlainText | **local** | no external equivalent |
| `submissionCorrection` | PlainText | **local** | no external equivalent |
| `submissionPage` | PlainText | **local** | no external equivalent |
| `submissionWhen` | PlainText | **local** | no external equivalent |
| `submissionTakenBy` | PlainText | **local** | no external equivalent |
| `submissionHolder` | PlainText | **local** | no external equivalent |
| `submissionSenderName` | PlainText | **local** | no external equivalent |
| `submissionCredit` | PlainText | **local** | no external equivalent |
| `submissionEmail` | PlainText | **local** | no external equivalent |
| `submissionPermission` | PlainText | **local** | no external equivalent |
| `submissionFiles` | PlainText | **local** | no external equivalent |
| `submissionDecision` | PlainText | **local** | no external equivalent |

### War Memorials — `warMemorials/warMemorial`

57 fields.

| Field | Kind | Maps to | Note |
| --- | --- | --- | --- |
| `featuredImage` | Assets | schema.org `image` |  |
| `webmasterNoteTop` | PlainText | **local** | no external equivalent |
| `body` | PlainText | schema.org `text` | also dcterms:description |
| `webmasterNoteBottom` | PlainText | **local** | no external equivalent |
| `editorNotes` | Table | **local** | no external equivalent |
| `researchLeads` | PlainText | **local** | no external equivalent |
| `deathDate` | PlainText | schema.org `deathDate` | printed form |
| `deathDateEdtf` | PlainText | **local** | no external equivalent |
| `burialPlace` | PlainText | schema.org `deathPlace` | burial rather than death, so the mapping is approximate |
| `recordTags` | Categories | Dublin Core `subject` | local vocabulary |
| `neighborhood` | Categories | Dublin Core `spatial` | local vocabulary of valley communities |
| `wmFamily` | PlainText | **local** | no external equivalent |
| `wmSelectiveServiceDate` | PlainText | **local** | no external equivalent |
| `wikidataId` | PlainText | Wikidata `QID` | emitted as schema.org sameAs |
| `factSources` | Table | **local** | no external equivalent |
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
| `withheldBody` | PlainText | **local** | no external equivalent |

## Controlled values

Every dropdown in the schema, with its values. All of these vocabularies are
local unless the note says otherwise.

- **`affiliationEnded`** — `serving`, `retired`, `resigned`, `left`, `died`, `dismissed`, `unknown`.
- **`affiliationKind`** — `employed`, `member`, `founder`, `owner`, `nonprofit-board`, `volunteer`.
- **`authorshipBasis`** — `printed-byline`, `series-attribution`, `closing-tagline`, `derived`.
- **`birthEvidence`** — `certified`, `contemporary`, `retrospective`, `roster`, `uncited`.
- **`bodyAuthorship`** — `legacy-leon`, `wordpress-import-unsourced`, `editorial-2026`, `mixed`.
- **`burialEvidence`** — `certified`, `contemporary`, `retrospective`, `roster`, `uncited`.
- **`civicRole`** — `governs`, `represents`, `polices`, `advises`, `administers`, `none`.
- **`collectionKind`** — `series`, `book`, `column`, `catalogue`, `topic`. How a collection is read, not what it is about. `series` is a run meant to be read in order, which is what both of the archive's long newspaper serials are; `book` is reserved for an actual published volume and is not yet used by any record.
- **`deathEvidence`** — `certified`, `contemporary`, `retrospective`, `roster`, `uncited`.
- **`districtKind`** — `assembly`, `senate`, `congressional`, `supervisorial`, `trustee-area`, `other`, `council-district`, `water-division`.
- **`districtPlan`** — `1991`, `2001`, `2011`, `2021`, `2025`.
- **`educationEvidence`** — `certified`, `contemporary`, `retrospective`, `roster`, `uncited`.
- **`educationOutcome`** — `graduated`, `attended`, `unknown`.
- **`electionKind`** — `general`, `special`, `recall`, `runoff`.
- **`endEvidence`** — `certified`, `contemporary`, `retrospective`, `roster`, `derived`, `uncited`.
- **`fixStatus`** — `open`, `done`, `wontfix`.
- **`foValleyTie`** — `killed-here`, `served-here`, `resident-elsewhere`, `off-duty`, `en-route`.
- **`foundedEvidence`** — `certified`, `contemporary`, `retrospective`, `roster`, `derived`, `uncited`.
- **`hauntedStatus`** — `reported`, `legend`, `disputed`.
- **`heldAs`** — `clipping`, `magazine-pages`, `transcription-only`, `web`.
- **`howEnded`** — `expired`, `reelected`, `resigned`, `died`, `recalled`, `termed-out`, `left`, `lines-moved`, `serving`, `unknown`, `removed`.
- **`orgLevel`** — `valley`, `county`, `state`, `federal`.
- **`orgType`** — `school`, `government`, `business`, `nonprofit`, `church`, `club`, `media`, `military`, `other`, `rancho`. Drives the schema.org `@type`: school to School, government to GovernmentOrganization, business to Corporation, nonprofit to NGO, church to Church, media to NewsMediaOrganization, club and military and other to Organization.
- **`outcome`** — `unknown`, `elected`, `not-elected`, `withdrew`, `disqualified`.
- **`outcomeEvidence`** — `certified`, `contemporary`, `retrospective`, `roster`, `derived`, `uncited`.
- **`placeType`** — `natural`, `road`, `ranch`, `building`, `park`, `site`, `trail`, `settlement`, `district`, `cemetery`. Mapped from the GNIS feature class where a record carries a GNIS id; see `add_place_type_field.php`.
- **`schoolLevel`** — `elementary`, `middle`, `high`, `college`, `district`. Drives the schema.org School subtype: ElementarySchool, MiddleSchool, HighSchool, CollegeOrUniversity. A district has no schema.org subtype and stays Organization.
- **`seatsUpEvidence`** — `certified`, `contemporary`, `retrospective`, `roster`, `derived`, `uncited`.
- **`selectionMethod`** — `elected`, `appointed`, `rotated`, `exofficio`, `succeeded`, `sole-candidate`, `unopposed`.
- **`startEvidence`** — `certified`, `contemporary`, `retrospective`, `roster`, `derived`, `uncited`.
- **`submissionKind`** — `photograph`, `correction`.
- **`submissionStatus`** — `new`, `accepted`, `declined`, `spam`.

## Category groups

- **`neighborhood`** — 35 terms. Local vocabulary.
- **`historicalEra`** — 15 terms. Local vocabulary.
- **`historicalPeriod`** — 14 terms. Local vocabulary.
- **`tag`** — 0 terms. Local vocabulary.
- **`theme`** — 15 terms. Local vocabulary.

## Who gets a person record

**A person record requires significance to SCV history, not appearance in a result** (Nathan, 1 October 2026). Standing for office, even often, is persistence, not significance; the rule of 25 September that standing more than once earned a record is withdrawn.

- **Keep**: held office in the archive's records (an officeHolding, or an election won), or is the subject of an article, photograph, document, obituary or war memorial record, or has a sourced profile, or is pointed at by a place, organization or another person.
- **No record**: nothing but candidacies, none won. The candidacy keeps `nameAsPrinted` and its votes; the election page prints the name unlinked. Nothing is lost.
- **Nathan decides**: the borderline, such as a candidate notable for something the archive does not yet hold, or a winner in a body with thin data.
- The audit is `scripts/import/audit_person_significance.php`. The election imports no longer create people for repeated candidacy.

## Eras

**A person's era is where they mattered, not where most of their years fell. Where years and significance disagree, significance wins** (Nathan, 2 October 2026).

- **The pass proposes.** `scripts/import/assign_person_eras.php` places a person by the years of their public acts (every year of every office, every race, dated rows, photographs and events within the life) and assigns an era only when one holds two thirds of them. It never overwrites an era already set.
- **Significance decides.** The weighting counts years, and years can mislead: Buck McKeon spent more of them in Congress, but the city's first mayor is what makes him matter here, so he is in the Cityhood Era; Laurene Weste has more years in the Contemporary era, but the development and open-space fights she is known for belong to Mall & Growth. A split is settled by asking what the person is in this archive for.
- **Every era they acted in; the first is the primary** (Nathan, 2 October 2026). `historicalEra` holds several, in order. A person gets each era with an office year, a race, or two other dated acts in their lifetime (`scripts/import/apply_multi_eras.php`); the pass never sets or moves the primary, which is what a card and its label show. Legacy after death (Hart's park, Postwar Boom) is added by hand. Never by the pass: the event eras (St. Francis Dam, Northridge Recovery) and Tataviam & Native Peoples, since a year cannot say where something happened. A low-confidence placement says so in an editor note (Remi Nadeau).
- **On /persons**, an era's chip counts the people whose primary it is; the filtered page shows them as cards, "Known for this era", and everyone else active in it as a line of names with their primary era.

## Sourcing war memorial records

**Two independent sources for each death: one federal, and one that ties him to the valley** (Nathan, 2 October 2026: "That second one is what makes this archive's memorial different from a national database").

- **Federal sources by conflict.** World War II: the War Department's 1946 Honor List by county (NARA NAID 305280 for California, page images in `inventory/legacy/fetched/nara-honor-list-ca/`), the Navy's State Summary of War Casualties, NARA's WWII Army Enlistment Records, and ABMC for burials and the missing abroad. Korea: NARA's Korean War Casualty File (Army, RG 407) and Korean Conflict Casualty File (RG 330). Vietnam: NARA's Combat Area Casualties Current File, with the Coffelt Database for unit and panel. War on Terror: Defense Department casualty releases and NARA's DCAS file. World War I has none online for a death at home; the record says so.
- **`footnotes`** carry the sources, numbered. **`factSources`** is a table, one row per fact: the fact, its value as the record holds it, the note numbers that support it, and, where the sources differ, what each says. A difference is shown, never silently settled.
- **The legacy text is not rewritten.** A fact field is corrected only where the sources settle it (Edward Guy North died April 7, 1919, not April 27), with a "Correction, 2026" editor note; a "Sources, 2026" note says whether the record meets the rule. `scripts/import/source_wm_pilot.php` is the pattern; the readings are in `inventory/sources/wm-pilot-2026-10-02.json`.
- **No likeness known.** Where no portrait exists, an editor note headed Likeness says so and why, as a fact rather than a gap (`scripts/import/note_wm_no_likeness.php`).

## On This Day

`templates/_data/calendar.json`, built by `scripts/import/build_calendar_index.php`, reads two things (Nathan, 2 October 2026). The exact dates records hold in their own date fields, which were set from sources and need no review: events, elections, deaths, foundings and dissolutions, places established, office terms begun, photographs, war memorial deaths, and births of historical people only. And `recordDates` rows a person has ticked Confirmed. Article and document publication dates are listed apart, as "Published on this day", never mixed with what happened. A `recordDates` row ticked "Not for the calendar" (`rejected`: a newspaper dateline, a date in a list) is kept so the proposer never offers it again, and is never read.

## Generated and edited images

**The archivist decides. An image enters the archive on Nathan Imhoff's word, and what he says about it is recorded. The content-credentials scanner reports; it never blocks** (Nathan, 1 October 2026, correcting the rule of the same morning, which had treated every content credential as disqualifying).

- **Edited photographs.** A crop, an upscale, a removed bystander, a cleaned background or a cropped edge filled in is ordinary archival practice and has been since before digital. Provenance covers what was done, not whether software was involved. Adobe writes a content credential (C2PA) for any Firefly-assisted edit, including one that invents nothing, and labels an upscale as algorithmic media; a credential is a record of an edit, kept as a note.
- **What an edited asset records.** The original: `enhancedFrom` when the archive holds it, and its source in `source` as Nathan gives it. Where his word is the only evidence of the source, `source` says so ("per Nathan Imhoff"), which is a different thing from established provenance; where he has given none, it reads "Source per Nathan Imhoff." What was changed, in `enhancementMethod`. Who, `enhancedBy`. When, `enhancedDate`. And `contentCredentials`: the manifest's address and each step it records. Craft re-encodes the file on import and the stored copy loses its manifest, so this field is the only place the credential survives. `scripts/import/import_edited_portraits.php` is the pattern.
- **When the original exists, keep both** (Nathan, 4 October 2026). An edited image imported while its unedited original is in hand enters with the original beside it: the original as its own asset, the edited file where it is used (the mark, the portrait), and `enhancedFrom` on the edited file naming the original, so the archive records which is which. Neither replaces the other. `scripts/import/hold_newhall_elementary_original_2026_10_04.php` is the pattern for a mark.
- **Generated banners.** The engraved banners are illustrations made for the site, not photographs of anything. They are page decoration, labelled on the image, and are not archive records, portraits or relations to the person they depict. The rest of this section is about those.

- **Not in the archive.** A generated image is never an asset in a volume, never a photograph or document record, and never the value of any field: not `featuredImage`, `bandImage`, `recordImages`, `photoPeople` or any relation. Everything in the archive's volumes is a record, and a generated image is not evidence of anything.
- **Where it lives.** A banner is a file in `web/banners/`, listed in `templates/_data/banners.json` under the record it decorates (`persons:16356`). The registry is the only place the connection exists, so nothing in the database can join the image to the person and nothing that reads the database can emit it.
- **What it records.** `kind` (always `ai-generated-illustration`), the file's SHA-256, the record and its title, the style, `tool`, `sourcePhotograph` and `generatedOn` (null until known; the page then says "not recorded", never a guess), who commissioned it, and when and as what the archive received it.
- **Band and frame** (moved from HANDOFF on 3 October 2026, restated for the banners). Artwork fills the band: a banner, or an image in `bandImage`, as its one image layer under the gradient. A photograph is never the band's background: it is framed as a portrait (a 4:5 crop, `_partials/record/portrait`), in the band's portrait slot when there is no banner, and at the top of the sidebar as PORTRAIT when a banner fills the band.
- **On the page.** The banner is the band's background, not the portrait: the person's photograph, where there is one, moves to the sidebar as PORTRAIT with its own credit. The banner carries its credit on the image itself, "AI-generated illustration. Not a photograph.", which opens to what is recorded about how it was made. It is never in the page's JSON-LD and has an empty alternative text.
- **Enforced.** `check_render.php` fails if a banner file is missing or changed, if an asset carries its file name, if its page lacks the credit, or if its page's JSON-LD mentions it. Missing provenance is reported, not failed.

### Enhanced portraits: the standing pattern

Nathan, 6 October 2026: "the enhanced image becomes the portrait, the original stays on the record
as a related image, and the two are linked so a reader seeing the enhanced one can reach the
original." So: the enhanced asset is `featuredImage`, the original is in `recordImages`, and
`enhancedFrom` on the enhanced asset points at the original. The enhanced asset records who
enhanced it (`enhancedBy`), when (`enhancedDate`), and how, in plain words read from its own content
credential (`enhancementMethod`; the manifest and its steps in `contentCredentials`). Its caption says
it is an enhanced version, and the person page says so under the portrait with a link to the
original. The credential scanner reports what a file carries; it does not block an import. Where the
original is not held, `enhancedFrom` stays empty and the caption says so.

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

## Provenance and rights

**Having a file is not holding the rights to it.** Two questions, answered by separate
fields. Provenance says where the file came from: `provenanceKind`, `sourceUrl`,
`acquiredDate`, `source`, `legacySourcePath`. Rights say whether the archive may publish
it: `license`, `rightsHolder`, `creator`, `courtesyOf`. A file can be fully provenanced
and still unpublishable, like a campaign photograph received during campaign work, whose
copyright belongs to someone else. Knowing exactly where it came from settles nothing
about the licence, and an empty licence is not permission.

**What a file is, apart from where it came from** (Nathan, 3 October 2026). `assetRole`
says what the file is: empty for an archive record, which is nearly everything;
`current-mark` for a body's own present logo; `decoration` for an ornament. A logo
fetched from a district's website is `outside` by provenance and `current-mark` by role.
A current mark sits in the organization's `currentMark` field, never its featured
image: a featured image reads as "this is the record", and a logo is not that. It shows
small beside the title, captioned with where it is shown and when it was retrieved; its
/media address goes to the record; it is emitted as schema.org `logo`, never as an item
the archive holds. A historical letterhead or a past logo is an archive record and goes
with the record's media. When a body changes its mark, the old one leaves `currentMark`.
Its licence is `identifying-use`; where the body restricts use of its mark, `rightsNote`
says so and on what footing the archive shows it (the Hart district: identification on
its own record, as a reference work does). The files as received, their checksums and
sizes are in `inventory/marks/marks.json`.

**Provenance is recorded at the point of receipt.** Craft re-encodes every image on
import: the Hart portrait arrived a progressive JPEG and is stored a baseline one, with
different bytes. A checksum taken from a stored asset therefore proves nothing about the
original, and any embedded metadata, a content-credentials (C2PA) manifest included, is
gone from the stored copy. So an import script records the file as received, its name
and SHA-256, in `sourceChecksum` (internal; the `source` sentence says where the file came from,
in a reader's words), and refuses any other file; and the content-credentials check,
`scan_content_credentials.py`, reads the originals (the incoming file, the mirror, the
WordPress upload), never `web/uploads/`. It runs on everything and reports what each
credential records; the report goes into `contentCredentials` as a note. It never blocks
an import: whether an image enters is the archivist's decision.

`acquiredDate` is the calendar date in the site's timezone, America/Los_Angeles, the one
Craft records `dateCreated` in. A file received in the evening in California is already
dated the next day on a machine set to European time; the archive date is the California one.

## Content advisories

A record about killing, violent injury or a suicide carries a one-line advisory as an editor's
note in the **top** position, above the text, on the event and on every source record about it
(Nathan, 5 October 2026: "a warning that appears after the reader has read the thing is not a
warning"). Not `culturalSensitivityNote`, which renders after the body and is kept for the
cultural and Tataviam notes it was made for. The wording names what the record concerns and
what it describes, and nothing more: "This record concerns a school shooting in which
students were killed, and describes injuries and a suicide." First used on the Saugus High
School shooting of 2019; the St. Francis Dam failure, the Newhall Incident and the Kuredjian
standoff need it too.

## Disasters and their consequences: the figures

**A disaster's toll is contested almost by definition, and an archive that picks a number is
choosing for the reader. Every figure is held one row per figure per source** (Nathan, 6 October
2026). Deaths, injuries, acres, structures, cost and displacement are recorded as each source
gives them, and two sources that disagree are two rows, not a choice: the St. Francis Dam's dead
run from "probably almost five hundred" in 1928 to the later counts; Western Air Express Flight 7's
from the Signal's two to five.

**Scope is required on every row, not an optional note.** A figure says whether it counts this
valley, a named community, the county or the region. The 1938 flood's 113 to 115 dead are the
region's, with none in this valley, which is exactly the kind of figure that gets quoted as if it
were local. Cost in today's money states its index and base year on the page; it is never a
silent conversion.

**The section is not natural disasters alone.** The dam was a structural failure, the air crashes
were accidents, the Newhall Pass collapsed in an earthquake and again in a truck fire: what they
share is that something happened here and the valley counted the cost. A kind on each event
carries the distinction, and cause and consequence are links between events (the dam and its
flood; Sylmar and the Newhall Pass). Nothing is built yet: the audit of every figure the archive
holds, what each rests on and its scope (inventory/review/disaster-figures-audit-2026-10-06.md)
comes before any field is designed. The audit is done (inventory/review/disaster-figures-audit-2026-10-07.md,
19 events, 241 figure rows). No figure row is published without its scope.

**Natural and accidental events only; crime stays out** (Nathan, 7 October 2026). The comparison
covers fires, floods, earthquakes, the dam and the air crashes. The Saugus High shooting is not a
disaster figure: set in a table beside fires and floods it would make a claim no source makes.

**Cause and consequence are recorded on the event, as the sources make them** (Nathan, 7 October 2026).
`eventConsequences` is a table: what followed, in words; the record it is, when the archive holds one
(a place, a photograph, an event); and the source that makes the link, quoted. A table and not a
relation, because most consequences are not records: the St. Francis Dam's flood is part of its own
record, the Greenbrier fires have none. The event page shows them under "What followed". First rows:
the Dam to its flood; Sylmar and Northridge each to the Newhall Pass interchange; Northridge to the
Greenbrier fires; the 1938 flood to the Saugus derailment.

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

