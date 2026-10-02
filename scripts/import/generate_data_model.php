/**
 * Writes docs/DATA-MODEL.md from the live schema.
 *
 * Fourteen entry types carry 496 fields between them. A data model written by
 * hand is out of date the afternoon it is written, and an out-of-date data
 * model is worse than none: it is consulted and believed. So this reads the
 * schema and emits the document, and the only hand-written part is the table of
 * external standards, which is judgement and cannot be derived.
 *
 * WHERE A FIELD MAPS TO NOTHING
 *
 * Most of them do. "placeScvhlCheckbox" records whether the Santa Clarita
 * Valley Historical Society has listed a place, and no vocabulary on earth has
 * a term for that. Those are marked local rather than left blank, because a
 * blank reads as an oversight and the distinction between "we have not mapped
 * this" and "this does not map" is the whole point of the exercise.
 *
 * KEEPING IT CURRENT
 *
 * check_data_model.php compares the document against the schema and fails if a
 * field exists that the document does not mention. Run it from check_render so
 * a schema script that adds a field without regenerating this cannot pass.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/generate_data_model.php'))"
 */

$OUT = \Craft::getAlias('@root') . '/docs/DATA-MODEL.md';

/* ------------------------------------------------- the hand-written part */

/* handle => [standard, term, note]. Anything absent is local by definition and
   is printed as such. */
$MAP = [
    'title'                => ['schema.org', 'name', 'also dcterms:title'],
    'body'                 => ['schema.org', 'text', 'also dcterms:description'],
    'subheadline'          => ['schema.org', 'alternativeHeadline', ''],
    'originallyPublishedTitle' => ['schema.org', 'alternativeHeadline', 'the title the piece first carried'],
    'originalPublishDate'  => ['schema.org', 'datePublished', 'printed form; EDTF in dateEdtf where it parses'],
    'dateEstablished'      => ['schema.org', 'foundingDate', 'printed form'],
    'dateFounded'          => ['schema.org', 'foundingDate', 'printed form'],
    'birthDate'            => ['schema.org', 'birthDate', 'printed form'],
    'deathDate'            => ['schema.org', 'deathDate', 'printed form'],
    'birthplace'           => ['schema.org', 'birthPlace', ''],
    'burialPlace'          => ['schema.org', 'deathPlace', 'burial rather than death, so the mapping is approximate'],
    'occupation'           => ['schema.org', 'hasOccupation', 'free text today; see the roles work'],
    'placeLat'             => ['WGS84', 'lat', 'schema.org latitude'],
    'placeLng'             => ['WGS84', 'long', 'schema.org longitude'],
    'orgLat'               => ['WGS84', 'lat', 'schema.org latitude'],
    'orgLng'               => ['WGS84', 'long', 'schema.org longitude'],
    'articleLat'           => ['WGS84', 'lat', 'where the piece is about a spot'],
    'articleLng'           => ['WGS84', 'long', ''],
    'placeAddress'         => ['schema.org', 'address', ''],
    'orgAddress'           => ['schema.org', 'address', ''],
    'gnisId'               => ['GNIS', 'Feature ID', 'Wikidata P590'],
    'wikidataId'           => ['Wikidata', 'QID', 'emitted as schema.org sameAs'],
    'viafId'               => ['VIAF', 'cluster ID', 'Wikidata P214'],
    'bioguideId'           => ['Biographical Directory of the U.S. Congress', 'member ID', 'Wikidata P1157; emitted as sameAs'],
    'imdbId'               => ['IMDb', 'name ID', 'Wikidata P345; emitted as sameAs'],
    'ein'                  => ['IRS', 'EIN', 'Wikidata P1297'],
    'cdsCode'              => ['CDE', 'CDS code', 'Wikidata P2183'],
    'ncesId'               => ['NCES', 'school or district ID', 'Wikidata P2696'],
    'gnisId_asset'         => ['GNIS', 'Feature ID', ''],
    'scvhlNumber'          => ['SCVHS', 'landmark number', 'local register, no external scheme'],
    'placeChlNumber'       => ['OHP', 'California Historical Landmark number', 'state register'],
    'nrhpReference'        => ['NPS', 'NRHP reference number', 'Wikidata P649'],
    'nrhpListedDate'       => ['NPS', 'listing date', ''],
    'personWikipediaUrl'   => ['schema.org', 'sameAs', ''],
    'placeWikipediaUrl'    => ['schema.org', 'sameAs', ''],
    'orgWikipediaUrl'      => ['schema.org', 'sameAs', ''],
    'placeWebsite'         => ['schema.org', 'url', 'the place\'s own official site'],
    'personGraveUrl'       => ['schema.org', 'sameAs', 'Find a Grave'],
    'personAliases'        => ['SKOS', 'altLabel', 'schema.org alternateName'],
    'placeAliases'         => ['SKOS', 'altLabel', 'schema.org alternateName'],
    'orgAliases'           => ['SKOS', 'altLabel', 'schema.org alternateName'],
    'spouseOf'             => ['schema.org', 'spouse', ''],
    'childOf'              => ['schema.org', 'parent', 'inverse of schema.org children'],
    'siblingOf'            => ['schema.org', 'sibling', ''],
    'parentOrganization'   => ['schema.org', 'parentOrganization', 'inverse emitted as subOrganization'],
    'feedsInto'            => ['local', '', 'the district or school a body\'s pupils go on to; the grade of the move is the feeder\'s top grade plus one'],
    'educationPerson'      => ['schema.org', 'alumniOf', 'emitted from the person: the person is an alumnus of the school'],
    'educationSchool'      => ['schema.org', 'alumniOf', 'the school side of the same statement'],
    'classOf'              => ['local', '', 'the graduating class as printed; names a year, not a diploma'],
    'educationOutcome'     => ['local', '', 'graduated only where a source says so'],
    'educationEvidence'    => ['local', '', 'the officeHolding scale: certified, contemporary, retrospective, roster, uncited'],
    'gradeSpan'            => ['local', '', 'grades taught, as K-6 or 9-12, from the NCES directory unless footnoted'],
    'writtenBy'            => ['schema.org', 'author', 'also dcterms:creator'],
    'editedBy'             => ['schema.org', 'editor', ''],
    'publishedBy'          => ['schema.org', 'publisher', ''],
    'partOfCollection'     => ['schema.org', 'isPartOf', 'also dcterms:isPartOf'],
    'subjectPerson'        => ['schema.org', 'about', 'dcterms:subject'],
    'subjectOrganization'  => ['schema.org', 'about', 'dcterms:subject'],
    'subjectGroup'         => ['schema.org', 'about', 'dcterms:subject'],
    'depictsPlace'         => ['schema.org', 'contentLocation', 'dcterms:spatial'],
    'articleEvents'        => ['schema.org', 'about', ''],
    'relatedArticles'      => ['schema.org', 'relatedLink', ''],
    'footnotes'            => ['Dublin Core', 'bibliographicCitation', 'a table, one row per note'],
    'footnotesOn'          => ['schema.org', 'citation', ''],
    'recordTags'           => ['Dublin Core', 'subject', 'local vocabulary'],
    'historicalEra'        => ['Dublin Core', 'temporal', 'local vocabulary, no external period thesaurus'],
    'articleThemes'        => ['Dublin Core', 'subject', 'local vocabulary; any number per article, where an era is one'],
    'historicalPeriod'     => ['Dublin Core', 'temporal', 'local vocabulary'],
    'neighborhood'         => ['Dublin Core', 'spatial', 'local vocabulary of valley communities'],
    'featuredImage'        => ['schema.org', 'image', ''],
    'recordImages'         => ['schema.org', 'image', ''],
    'recordDocuments'      => ['schema.org', 'associatedMedia', ''],
    'bandImage'            => ['schema.org', 'image', 'presentation only'],
    'legacyUrl'            => ['Dublin Core', 'source', 'where it stood on the legacy site'],
    'archiveUrl'           => ['Dublin Core', 'source', 'Internet Archive capture'],
    'sourcePath'           => ['Dublin Core', 'source', ''],
    'legacySourcePath'     => ['Dublin Core', 'source', 'assets: the path the file had on the legacy site'],
    'recordProvenance'     => ['Dublin Core', 'provenance', 'how the record came to exist'],
    'culturalSensitivityNote' => ['Dublin Core', 'rights', 'approximate; it is a note, not a licence'],
    'creator'              => ['schema.org', 'creator', 'ImageObject: who made the picture'],
    'dateAsPrinted'        => ['schema.org', 'temporalCoverage', 'ImageObject: the date as the source prints it'],
    'dateEdtf'             => ['EDTF', 'ISO 8601-2', 'emitted as schema.org dateCreated'],
    'source'               => ['Dublin Core', 'source', 'the repository or publication'],
    'rightsHolder'         => ['schema.org', 'copyrightHolder', 'also dcterms:rightsHolder'],
    'license'              => ['schema.org', 'license', 'local vocabulary, emitted as a licence URL'],
    'courtesyOf'           => ['schema.org', 'creditText', 'the depositor\'s wording, verbatim'],
    'placeType'            => ['local', 'vocabulary', 'mapped from GNIS feature class where a record has one'],
    'orgType'              => ['local', 'vocabulary', 'drives the schema.org @type'],
    'schoolLevel'          => ['local', 'vocabulary', 'drives the schema.org School subtype'],
    'collectionKind'       => ['local', 'vocabulary', 'how a collection is read, not what it is about'],
];

$IDENTIFIERS = [
    ['gnisId', 'GNIS Feature ID', 'https://edits.nationalmap.gov/apps/gaz-domestic/public/summary/{id}', 'P590'],
    ['wikidataId', 'Wikidata item', 'https://www.wikidata.org/wiki/{id}', '-'],
    ['viafId', 'VIAF cluster', 'https://viaf.org/viaf/{id}', 'P214'],
    ['bioguideId', 'Biographical Directory of the U.S. Congress', 'https://bioguide.congress.gov/search/bio/{id}', 'P1157'],
    ['imdbId', 'IMDb', 'https://www.imdb.com/name/{id}/', 'P345'],
    ['ein', 'IRS Employer Identification Number', 'https://apps.irs.gov/app/eos/ (no direct row URL)', 'P1297'],
    ['cdsCode', 'CDE county-district-school code', 'https://www.cde.ca.gov/SchoolDirectory/details?cdscode={id}', 'P2183'],
    ['ncesId', 'NCES school or district id', 'https://nces.ed.gov/ccd/schoolsearch/school_detail.asp?ID={id}', 'P2696'],
    ['nrhpReference', 'National Register reference', 'https://npgallery.nps.gov/NRHP/AssetDetail?assetID={id}', 'P649'],
];

$fs  = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

$lines = [];
$lines[] = '# The data model';
$lines[] = '';
$lines[] = 'Generated from the live schema by `scripts/import/generate_data_model.php` on '
         . (new DateTime())->format('j F Y') . '. Do not edit by hand: regenerate.';
$lines[] = '';
$lines[] = 'Every entry type, every field, and the external standard each maps to. A field';
$lines[] = 'marked **local** has no external equivalent, and that is a statement rather than';
$lines[] = 'an omission: `placeScvhlCheckbox` records whether the historical society has';
$lines[] = 'listed a place, and no vocabulary has a term for it.';
$lines[] = '';

/* ------------------------------------------------------- the name policy */

$lines[] = '## The name policy';
$lines[] = '';
$lines[] = 'Three rules, applied by the review screen and the name canon.';
$lines[] = '';
$lines[] = '1. **The authority form is the title.** Where an authority holds the body, its';
$lines[] = '   official name becomes the record title: NCES for a school, the IRS Business';
$lines[] = '   Master File for a nonprofit, Wikidata otherwise. Where none does, the fullest';
$lines[] = '   form the corpus uses is the title.';
$lines[] = '2. **Usage becomes an alias.** The spelling the articles actually carry goes into';
$lines[] = '   the aliases, always. A record that cannot be found under the name the text';
$lines[] = '   uses has lost what the rename was for.';
$lines[] = '3. **No titles in names.** Honorifics and civic titles are stripped from the';
$lines[] = '   title and kept as aliases: Doctor, Captain, Colonel, Congressman,';
$lines[] = '   Councilwoman, Mayor, Supervisor, Sheriff, Judge, Senator, Reverend, Father,';
$lines[] = '   Chief. A man is a councilman for four years and a name for the rest of his';
$lines[] = '   life.';
$lines[] = '';
$lines[] = '   **Mrs, Miss, Ms and Sister are never stripped.** "Mrs. George LeBrun" is a';
$lines[] = '   woman named by her husband, and folding her into his record erases her.';
$lines[] = '';

/* ------------------------------------ transcription and interpretation */

$lines[] = '## Transcription and interpretation';
$lines[] = '';
$lines[] = 'A source record keeps what the source says apart from what anyone says about it.';
$lines[] = '';
$lines[] = '- **`body` is transcription.** The words of the item itself, verbatim, and';
$lines[] = '  nothing else: the 1899 reporter, the 1889 county history, Scofield\'s eulogy.';
$lines[] = '  A `[sic]` is kept where the page printed one and never added.';
$lines[] = '- **`webmasterNoteTop` is interpretation.** The webmaster\'s or a contributor\'s';
$lines[] = '  framing, commentary and argument about the item, verbatim as they wrote it.';
$lines[] = '- **`webmasterNoteBottom` is apparatus.** Credits, scan lines and notes on how';
$lines[] = '  the text was edited ("divided into paragraphs for ease of reading").';
$lines[] = '';
$lines[] = 'Where a legacy page never transcribed its item and wrote about it instead, the';
$lines[] = 'body stays empty. A page about a death certificate is 2014 talking, not 1900,';
$lines[] = 'and putting it in the body would publish the essay as the certificate. Facts the';
$lines[] = 'essay quotes from the item go into `recordDates`, labelled as quoted and';
$lines[] = 'unconfirmed until someone reads them off the scan.';
$lines[] = '';
$lines[] = 'A headline goes into `originallyPublishedTitle` only when the original printed';
$lines[] = 'it. A headline the legacy site wrote for its own page is the page\'s, not the';
$lines[] = 'item\'s.';
$lines[] = '';

/* ------------------------------------------------------------- evidence */

$lines[] = '## Evidence rates the claim, not the document';
$lines[] = '';
$lines[] = 'The evidence fields (`birthEvidence`, `deathEvidence`, `burialEvidence`,';
$lines[] = '`startEvidence`, `endEvidence`) say how good one claim is, not what kind of paper';
$lines[] = 'it was found on.';
$lines[] = '';
$lines[] = '- **A document is `certified` only for what its certifier attests.** A death';
$lines[] = '  certificate certifies the death. The birth date on it is an informant\'s';
$lines[] = '  statement, rated by who supplied it and how long after the event: usually';
$lines[] = '  `retrospective`.';
$lines[] = '- **`certified` requires a certificate the archive holds.** A certificate that a';
$lines[] = '  page or a book cites is `retrospective` until the document itself is in the';
$lines[] = '  archive.';
$lines[] = '- **A contradiction is not an evidence level.** A source that disagrees with';
$lines[] = '  itself keeps its rating; the doubt goes in the EDTF qualifier and a source';
$lines[] = '  fault. Charles Alexander Mentry\'s certificate gives 27 March 1847 and an age';
$lines[] = '  that counts back to 1848: `1847?-03-27`, `retrospective`, and a note.';
$lines[] = '';
$lines[] = '`scripts/import/set_evidence_levels.php` audits every `certified` value against';
$lines[] = 'this rule each time it runs.';
$lines[] = '';

/* ------------------------------------------------------- the identifiers */

$lines[] = '## Identifier schemes';
$lines[] = '';
$lines[] = '| Field | Scheme | URL pattern | Wikidata property |';
$lines[] = '| --- | --- | --- | --- |';
foreach ($IDENTIFIERS as $i) {
    $lines[] = '| `' . $i[0] . '` | ' . $i[1] . ' | `' . $i[2] . '` | ' . $i[3] . ' |';
}
$lines[] = '';

/* ------------------------------------------------- the relation vocabulary */

$lines[] = '## The relation vocabulary';
$lines[] = '';
$lines[] = 'Relations are held on the side that reads naturally in the control panel, and';
$lines[] = 'emitted from whichever side schema.org expects.';
$lines[] = '';
$lines[] = '| Ours | Between | schema.org |';
$lines[] = '| --- | --- | --- |';
$RELATIONS = [
    ['subjectPerson', 'article to person', 'about'],
    ['subjectOrganization', 'article to organization', 'about'],
    ['subjectGroup', 'article to group', 'about'],
    ['depictsPlace', 'article to place', 'contentLocation'],
    ['articleEvents', 'article to event', 'about'],
    ['writtenBy', 'article to person', 'author'],
    ['editedBy', 'article to person', 'editor'],
    ['publishedBy', 'article to organization', 'publisher'],
    ['partOfCollection', 'article to collection', 'isPartOf'],
    ['relatedArticles', 'article to article', 'relatedLink'],
    ['parentOrganization', 'organization to organization', 'parentOrganization, inverse subOrganization'],
    ['feedsInto', 'school or district to the one its pupils go on to', 'local; held on the feeder, read from both ends'],
    ['educationPerson / educationSchool', 'an education record joining a person to a school', 'alumniOf'],
    ['spouseOf', 'person to person', 'spouse'],
    ['childOf', 'person to person', 'parent'],
    ['siblingOf', 'person to person', 'sibling'],
    ['personOrganizations', 'person to organization', 'affiliation'],
    ['placePeople', 'place to person', 'no direct term; emitted as about on the place'],
    ['derivedImageLinks', 'record to record', 'local: images derived from a record, not curated for it'],
];
foreach ($RELATIONS as $r) { $lines[] = '| `' . $r[0] . '` | ' . $r[1] . ' | ' . $r[2] . ' |'; }
$lines[] = '';
$lines[] = '### Kinship on the page';
$lines[] = '';
$lines[] = '`childOf`, `siblingOf` and `spouseOf` are stored for anyone, and published only between two';
$lines[] = 'historical people, by the test in `templates/_partials/record/historical.twig`, which treats';
$lines[] = 'undetermined as living. The archive does not publish a family graph of living people.';
$lines[] = '';
$lines[] = '**One exception, no wider** (Nathan, 29 September 2026): a parent, child or sibling link';
$lines[] = 'publishes when both people hold documented public office, each with at least one';
$lines[] = '`officeHolding` record, and the relationship is itself sourced, in a footnote on the';
$lines[] = 'holder\'s record that names the other person and the relation. Both conditions, never one;';
$lines[] = 'never spouses. A father and son on the same council are public record, not private family';
$lines[] = 'structure. The test is `publicKin` in the same file. **It takes effect only when';
$lines[] = '`officeHoldings` has records; until then it passes nobody.**';
$lines[] = '';

/* ------------------------------------------------------------ the types */

$lines[] = '## Entry types';
$lines[] = '';

/* Prose that belongs to one entry type rather than to the model as a whole.
   Emitted under that type's field table, because a rule about who gets a record
   is read by whoever is looking at the fields, not by whoever scrolls to the
   end. Keyed by entry type handle. */
$TYPE_NOTES = [
    'person' => [
        '**Who gets a record.** A person record requires a Santa Clarita Valley connection',
        'the articles document: lived, worked, owned, built, founded, filmed, buried or',
        'acted here. The connection has to be in the text. A name appearing in an article',
        'is not a connection; it is a mention.',
        '',
        'National figures and subject-matter figures a local columnist wrote about get no',
        'record. Leon Worden covering Proposition 209 does not make Ward Connerly part of',
        'this valley, and the coin column naming forty numismatists does not make them',
        'residents. Those names keep their articles and point at Wikidata instead.',
        '',
        'The test is the subject, not the collection. Most of the numismatists are caught',
        'by being written about only inside one national column, but that is a symptom and',
        'not the rule: Connerly appears in a local column throughout and still fails,',
        'because nothing in the text places him here.',
        '',
        'William Mulholland has a record. He built the aqueduct and the St Francis Dam,',
        'and the dam broke in this valley and killed people in it; that is as documented',
        'as a connection gets. So do John Wayne and Tom Mix, who filmed at Melody Ranch,',
        'Charles Crocker, whose railroad came through, and Kit Carson, who came through',
        'with Fremont.',
        '',
        '**Holding an office that represents the valley is a connection.** Somebody who',
        'sat on a body whose constituency includes the SCV, or held a single-member seat',
        'drawn to include it, has a record, and the officeHolding record is the evidence:',
        'they legislated, voted or governed for this valley whether or not they ever lived',
        'here. A state senator whose district reached the SCV for one cycle passes on that',
        'ground alone. Without this the two rules disagree, because the inclusion rule for',
        'offices admits the office while the rule above turns away the person who held it.',
        '',
        'The exception is deliberate and bounded: statewide and national at-large offices',
        'are context, not valley seats, because nobody is the valley\'s governor. The two',
        'California US Senate seats are carried anyway, marked as statewide rather than',
        'valley offices, because an archive that cannot say who represented the state in',
        'the Senate has a hole a reader will notice.',
        '',
        'The ruling is recorded, not just acted on. A name ruled out is marked **External**',
        'on the review screen, which creates nothing and writes the Wikidata id into the',
        'name canon so the prose linker can point the name somewhere. External is not Skip:',
        'a skip means the queue has not been settled, and an external means it has.',
    ],
];

$dropdowns = [];
$allHandles = [];

foreach ($svc->getAllSections() as $sec) {
    foreach ($sec->getEntryTypes() as $et) {
        $cf = $et->getFieldLayout()->getCustomFields();
        $lines[] = '### ' . $sec->name . ' — `' . $sec->handle . '/' . $et->handle . '`';
        $lines[] = '';
        $lines[] = count($cf) . ' fields.';
        $lines[] = '';
        $lines[] = '| Field | Kind | Maps to | Note |';
        $lines[] = '| --- | --- | --- | --- |';
        foreach ($cf as $f) {
            $h = $f->handle;
            $allHandles[$h] = true;
            $kind = (new ReflectionClass($f))->getShortName();
            if (isset($MAP[$h])) {
                [$std, $term, $note] = $MAP[$h];
                $maps = $std === 'local' ? '**local**' : $std . ' `' . $term . '`';
            } else {
                $maps = '**local**';
                $note = 'no external equivalent';
            }
            $lines[] = '| `' . $h . '` | ' . $kind . ' | ' . $maps . ' | ' . $note . ' |';

            if ($f instanceof \craft\fields\Dropdown || $f instanceof \craft\fields\MultiSelect) {
                $vals = [];
                foreach (($f->options ?? []) as $o) { if (($o['value'] ?? '') !== '') { $vals[] = $o['value']; } }
                if ($vals) { $dropdowns[$h] = $vals; }
            }
        }
        $lines[] = '';
        foreach (($TYPE_NOTES[$et->handle] ?? []) as $ln) { $lines[] = $ln; }
        if (isset($TYPE_NOTES[$et->handle])) { $lines[] = ''; }
    }
}

/* ------------------------------------------------------- dropdown values */

$lines[] = '## Controlled values';
$lines[] = '';
$lines[] = 'Every dropdown in the schema, with its values. All of these vocabularies are';
$lines[] = 'local unless the note says otherwise.';
$lines[] = '';
ksort($dropdowns);
foreach ($dropdowns as $h => $vals) {
    $note = '';
    if ($h === 'placeType') { $note = ' Mapped from the GNIS feature class where a record carries a GNIS id; see `add_place_type_field.php`.'; }
    if ($h === 'collectionKind') { $note = ' How a collection is read, not what it is about. `series` is a run'
        . ' meant to be read in order, which is what both of the archive\'s long newspaper serials are;'
        . ' `book` is reserved for an actual published volume and is not yet used by any record.'; }
    if ($h === 'orgType') { $note = ' Drives the schema.org `@type`: school to School, government to GovernmentOrganization, business to Corporation, nonprofit to NGO, church to Church, media to NewsMediaOrganization, club and military and other to Organization.'; }
    if ($h === 'schoolLevel') { $note = ' Drives the schema.org School subtype: ElementarySchool, MiddleSchool, HighSchool, CollegeOrUniversity. A district has no schema.org subtype and stays Organization.'; }
    $lines[] = '- **`' . $h . '`** — ' . implode(', ', array_map(fn($v) => '`' . $v . '`', $vals)) . '.' . $note;
}
$lines[] = '';

/* -------------------------------------------------------- the categories */

$lines[] = '## Category groups';
$lines[] = '';
foreach (Craft::$app->categories->getAllGroups() as $g) {
    $n = \craft\elements\Category::find()->group($g->handle)->status(null)->count();
    $lines[] = '- **`' . $g->handle . '`** — ' . $n . ' terms. Local vocabulary.';
}
$lines[] = '';

$lines[] = '## Who gets a person record';
$lines[] = '';
$lines[] = '**A person record requires significance to SCV history, not appearance in a result** (Nathan, 1 October 2026). Standing for office, even often, is persistence, not significance; the rule of 25 September that standing more than once earned a record is withdrawn.';
$lines[] = '';
$lines[] = '- **Keep**: held office in the archive\'s records (an officeHolding, or an election won), or is the subject of an article, photograph, document, obituary or war memorial record, or has a sourced profile, or is pointed at by a place, organization or another person.';
$lines[] = '- **No record**: nothing but candidacies, none won. The candidacy keeps `nameAsPrinted` and its votes; the election page prints the name unlinked. Nothing is lost.';
$lines[] = '- **Nathan decides**: the borderline, such as a candidate notable for something the archive does not yet hold, or a winner in a body with thin data.';
$lines[] = '- The audit is `scripts/import/audit_person_significance.php`. The election imports no longer create people for repeated candidacy.';
$lines[] = '';
$lines[] = '## Eras';
$lines[] = '';
$lines[] = '**A person\'s era is where they mattered, not where most of their years fell. Where years and significance disagree, significance wins** (Nathan, 2 October 2026).';
$lines[] = '';
$lines[] = '- **The pass proposes.** `scripts/import/assign_person_eras.php` places a person by the years of their public acts (every year of every office, every race, dated rows, photographs and events within the life) and assigns an era only when one holds two thirds of them. It never overwrites an era already set.';
$lines[] = '- **Significance decides.** The weighting counts years, and years can mislead: Buck McKeon spent more of them in Congress, but the city\'s first mayor is what makes him matter here, so he is in the Cityhood Era; Laurene Weste has more years in the Contemporary era, but the development and open-space fights she is known for belong to Mall & Growth. A split is settled by asking what the person is in this archive for.';
$lines[] = '- **Every era they acted in; the first is the primary** (Nathan, 2 October 2026). `historicalEra` holds several, in order. A person gets each era with an office year, a race, or two other dated acts in their lifetime (`scripts/import/apply_multi_eras.php`); the pass never sets or moves the primary, which is what a card and its label show. Legacy after death (Hart\'s park, Postwar Boom) is added by hand. Never by the pass: the event eras (St. Francis Dam, Northridge Recovery) and Tataviam & Native Peoples, since a year cannot say where something happened. A low-confidence placement says so in an editor note (Remi Nadeau).';
$lines[] = '- **On /persons**, an era\'s chip counts the people whose primary it is; the filtered page shows them as cards, "Known for this era", and everyone else active in it as a line of names with their primary era.';
$lines[] = '';
$lines[] = '## Generated and edited images';
$lines[] = '';
$lines[] = '**The archivist decides. An image enters the archive on Nathan Imhoff\'s word, and what he says about it is recorded. The content-credentials scanner reports; it never blocks** (Nathan, 1 October 2026, correcting the rule of the same morning, which had treated every content credential as disqualifying).';
$lines[] = '';
$lines[] = '- **Edited photographs.** A crop, an upscale, a removed bystander, a cleaned background or a cropped edge filled in is ordinary archival practice and has been since before digital. Provenance covers what was done, not whether software was involved. Adobe writes a content credential (C2PA) for any Firefly-assisted edit, including one that invents nothing, and labels an upscale as algorithmic media; a credential is a record of an edit, kept as a note.';
$lines[] = '- **What an edited asset records.** The original: `enhancedFrom` when the archive holds it, and its source in `source` as Nathan gives it. Where his word is the only evidence of the source, `source` says so ("per Nathan Imhoff"), which is a different thing from established provenance; where he has given none, it reads "Source per Nathan Imhoff." What was changed, in `enhancementMethod`. Who, `enhancedBy`. When, `enhancedDate`. And `contentCredentials`: the manifest\'s address and each step it records. Craft re-encodes the file on import and the stored copy loses its manifest, so this field is the only place the credential survives. `scripts/import/import_edited_portraits.php` is the pattern.';
$lines[] = '- **Generated banners.** The engraved banners are illustrations made for the site, not photographs of anything. They are page decoration, labelled on the image, and are not archive records, portraits or relations to the person they depict. The rest of this section is about those.';
$lines[] = '';
$lines[] = '- **Not in the archive.** A generated image is never an asset in a volume, never a photograph or document record, and never the value of any field: not `featuredImage`, `bandImage`, `recordImages`, `photoPeople` or any relation. Everything in the archive\'s volumes is a record, and a generated image is not evidence of anything.';
$lines[] = '- **Where it lives.** A banner is a file in `web/banners/`, listed in `templates/_data/banners.json` under the record it decorates (`persons:16356`). The registry is the only place the connection exists, so nothing in the database can join the image to the person and nothing that reads the database can emit it.';
$lines[] = '- **What it records.** `kind` (always `ai-generated-illustration`), the file\'s SHA-256, the record and its title, the style, `tool`, `sourcePhotograph` and `generatedOn` (null until known; the page then says "not recorded", never a guess), who commissioned it, and when and as what the archive received it.';
$lines[] = '- **On the page.** The banner is the band\'s background, not the portrait: the person\'s photograph, where there is one, moves to the sidebar as PORTRAIT with its own credit. The banner carries its credit on the image itself, "AI-generated illustration. Not a photograph.", which opens to what is recorded about how it was made. It is never in the page\'s JSON-LD and has an empty alternative text.';
$lines[] = '- **Enforced.** `check_render.php` fails if a banner file is missing or changed, if an asset carries its file name, if its page lacks the credit, or if its page\'s JSON-LD mentions it. Missing provenance is reported, not failed.';
$lines[] = '';
$lines[] = '## Images as objects';
$lines[] = '';
$lines[] = 'Every image a page shows is emitted as a schema.org `ImageObject` in the page graph,';
$lines[] = 'not as a URL hanging off the record. A picture used on six articles is one thing with';
$lines[] = 'a creator, a date and a licence, and saying so is the difference between publishing a';
$lines[] = 'file and publishing a photograph.';
$lines[] = '';
$lines[] = '| Asset field | ImageObject property |';
$lines[] = '| --- | --- |';
foreach ([
    ['(the file)', '`contentUrl`'],
    ['`photoCaptionExt`', '`caption`'],
    ['`creator`', '`creator`, as a Person'],
    ['`dateEdtf`, else `dateAsPrinted`', '`dateCreated`'],
    ['`dateAsPrinted`', '`temporalCoverage`, always the printed form'],
    ['`photoCredit` + `courtesyOf`', '`creditText`'],
    ['`rightsHolder`', '`copyrightHolder`'],
    ['`license`', '`license`, as a URL'],
    ['`legacySourcePath`', '`isBasedOn`'],
    ['width, height', '`width`, `height` as QuantitativeValue in pixels (unitCode E37)'],
    ['(the page)', '`isPartOf`'],
] as $r) { $lines[] = '| ' . $r[0] . ' | ' . $r[1] . ' |'; }
$lines[] = '';
$lines[] = 'The node is identified by the photograph record\'s URL where one exists, so the record';
$lines[] = 'and the image are one node; otherwise by its own `/media/<id>` page. A photograph';
$lines[] = 'record names its image as `primaryImageOfPage`.';
$lines[] = '';
$lines[] = '**An enhanced derivative is never emitted.** JSON-LD is an assertion to the rest of the';
$lines[] = 'web about what this archive holds, and what it holds is the scan. An upscaled version';
$lines[] = 'has pixels a model invented, and publishing it under the archive\'s name would be a';
$lines[] = 'false claim however good it looks. Where one is attached, its original is emitted.';
$lines[] = '';
$lines[] = '## Provenance and rights';
$lines[] = '';
$lines[] = '**Having a file is not holding the rights to it.** Two questions, answered by separate';
$lines[] = 'fields. Provenance says where the file came from: `provenanceKind`, `sourceUrl`,';
$lines[] = '`acquiredDate`, `source`, `legacySourcePath`. Rights say whether the archive may publish';
$lines[] = 'it: `license`, `rightsHolder`, `creator`, `courtesyOf`. A file can be fully provenanced';
$lines[] = 'and still unpublishable, like a campaign photograph received during campaign work, whose';
$lines[] = 'copyright belongs to someone else. Knowing exactly where it came from settles nothing';
$lines[] = 'about the licence, and an empty licence is not permission.';
$lines[] = '';
$lines[] = '**Provenance is recorded at the point of receipt.** Craft re-encodes every image on';
$lines[] = 'import: the Hart portrait arrived a progressive JPEG and is stored a baseline one, with';
$lines[] = 'different bytes. A checksum taken from a stored asset therefore proves nothing about the';
$lines[] = 'original, and any embedded metadata, a content-credentials (C2PA) manifest included, is';
$lines[] = 'gone from the stored copy. So an import script records the file as received, its name';
$lines[] = 'and SHA-256, in `source`, and refuses any other file; and the content-credentials check,';
$lines[] = '`scan_content_credentials.py`, reads the originals (the incoming file, the mirror, the';
$lines[] = 'WordPress upload), never `web/uploads/`. It runs on everything and reports what each';
$lines[] = 'credential records; the report goes into `contentCredentials` as a note. It never blocks';
$lines[] = 'an import: whether an image enters is the archivist\'s decision.';
$lines[] = '';
$lines[] = '`acquiredDate` is the calendar date in the site\'s timezone, America/Los_Angeles, the one';
$lines[] = 'Craft records `dateCreated` in. A file received in the evening in California is already';
$lines[] = 'dated the next day on a machine set to European time; the archive date is the California one.';
$lines[] = '';
$lines[] = '## Dates';
$lines[] = '';
$lines[] = 'Dates are held as the source printed them. "about 1887", "spring of 1912" and';
$lines[] = '"12 March 1884" are all things the corpus says, and normalising them on the way';
$lines[] = 'in would lose what the source actually claimed.';
$lines[] = '';
$lines[] = 'Where a printed form parses cleanly, an EDTF form is derived alongside it. Where';
$lines[] = 'it does not, the EDTF field stays empty rather than being guessed. EDTF is';
$lines[] = 'Extended Date/Time Format, ISO 8601-2.';
$lines[] = '';
$lines[] = 'The EDTF fields sit beside the printed ones and are derived by';
$lines[] = '`add_edtf_fields.php`: `originalPublishDateEdtf`, `eventDateEdtf`, `photoDateEdtf`,';
$lines[] = '`birthDateEdtf`, `deathDateEdtf`, `dateFoundedEdtf` and `dateEstablishedEdtf`.';
$lines[] = '';

file_put_contents($OUT, implode("\n", $lines) . "\n");

echo 'entry types: ' . count(array_merge(...array_map(fn($s) => $s->getEntryTypes(), $svc->getAllSections()))) . PHP_EOL;
echo 'distinct field handles: ' . count($allHandles) . PHP_EOL;
echo 'mapped to a standard: ' . count(array_intersect(array_keys($allHandles), array_keys($MAP))) . PHP_EOL;
echo 'local by definition: ' . (count($allHandles) - count(array_intersect(array_keys($allHandles), array_keys($MAP)))) . PHP_EOL;
echo 'dropdowns documented: ' . count($dropdowns) . PHP_EOL;
echo 'wrote ' . $OUT . ' (' . number_format(strlen(implode("\n", $lines))) . ' bytes)' . PHP_EOL;
