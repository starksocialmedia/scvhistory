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
    'personGraveUrl'       => ['schema.org', 'sameAs', 'Find a Grave'],
    'personAliases'        => ['SKOS', 'altLabel', 'schema.org alternateName'],
    'placeAliases'         => ['SKOS', 'altLabel', 'schema.org alternateName'],
    'orgAliases'           => ['SKOS', 'altLabel', 'schema.org alternateName'],
    'spouseOf'             => ['schema.org', 'spouse', ''],
    'childOf'              => ['schema.org', 'parent', 'inverse of schema.org children'],
    'siblingOf'            => ['schema.org', 'sibling', ''],
    'parentOrganization'   => ['schema.org', 'parentOrganization', 'inverse emitted as subOrganization'],
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
    'placeType'            => ['local', 'vocabulary', 'mapped from GNIS feature class where a record has one'],
    'orgType'              => ['local', 'vocabulary', 'drives the schema.org @type'],
    'schoolLevel'          => ['local', 'vocabulary', 'drives the schema.org School subtype'],
    'collectionKind'       => ['local', 'vocabulary', 'how a collection is read, not what it is about'],
];

$IDENTIFIERS = [
    ['gnisId', 'GNIS Feature ID', 'https://edits.nationalmap.gov/apps/gaz-domestic/public/summary/{id}', 'P590'],
    ['wikidataId', 'Wikidata item', 'https://www.wikidata.org/wiki/{id}', '-'],
    ['viafId', 'VIAF cluster', 'https://viaf.org/viaf/{id}', 'P214'],
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
    ['spouseOf', 'person to person', 'spouse'],
    ['childOf', 'person to person', 'parent'],
    ['siblingOf', 'person to person', 'sibling'],
    ['personOrganizations', 'person to organization', 'affiliation'],
    ['placePeople', 'place to person', 'no direct term; emitted as about on the place'],
    ['derivedImageLinks', 'record to record', 'local: images derived from a record, not curated for it'],
];
foreach ($RELATIONS as $r) { $lines[] = '| `' . $r[0] . '` | ' . $r[1] . ' | ' . $r[2] . ' |'; }
$lines[] = '';

/* ------------------------------------------------------------ the types */

$lines[] = '## Entry types';
$lines[] = '';

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
$lines[] = '_The `dateEdtf` fields are not yet in the schema; this section describes the';
$lines[] = 'intent that the schema script implements._';
$lines[] = '';

file_put_contents($OUT, implode("\n", $lines) . "\n");

echo 'entry types: ' . count(array_merge(...array_map(fn($s) => $s->getEntryTypes(), $svc->getAllSections()))) . PHP_EOL;
echo 'distinct field handles: ' . count($allHandles) . PHP_EOL;
echo 'mapped to a standard: ' . count(array_intersect(array_keys($allHandles), array_keys($MAP))) . PHP_EOL;
echo 'local by definition: ' . (count($allHandles) - count(array_intersect(array_keys($allHandles), array_keys($MAP)))) . PHP_EOL;
echo 'dropdowns documented: ' . count($dropdowns) . PHP_EOL;
echo 'wrote ' . $OUT . ' (' . number_format(strlen(implode("\n", $lines))) . ' bytes)' . PHP_EOL;
