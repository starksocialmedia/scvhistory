/**
 * READ ONLY. Dumps every claim-bearing field of every person record, and of the term, affiliation, education and
 * candidacy records that point at a person, to JSON for the single-source census (Nathan, 8 October 2026, item 11:
 * "Every claim on a person record that rests on a single source, listed. After the Perkins trust rule and the del
 * Valle dates I want to know how much of the archive stands on one leg").
 *
 * Writes inventory/review/overnight-2026-10-08/_person-claims-dump.json. The census itself is
 * single_source_claims_2026_10_08.py, which reads that dump.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/dump_person_claims_2026_10_08.php'))"
 */

use craft\elements\Entry;

ini_set('memory_limit', '2048M');
$root = \Craft::getAlias('@root');
$reads = require "$root/scripts/import/_reads.php";
$reads([
    ['record', 'Craft person records (body, footnotes, footnotesOn, editorNotes, recordDates, researchLeads, birth, death, birthplace and burial fields with their evidence, kinship relations)', 'the sources their footnotes cite', 'not read: this census counts the citations each record makes, not what the cited sources say'],
    ['record', 'Craft officeHolding, affiliation, education, candidacy and election records pointing at a person (terms, evidence, footnotes, footnotesOn, editorNotes)', 'the sources their footnotes cite', 'not read: as above, citations counted, not the sources'],
]);

$fieldsOf = function ($e) { $h = []; foreach ($e->getFieldLayout()->getCustomFields() as $f) { $h[$f->handle] = true; } return $h; };
$val = function ($e, $h, $k) {
    if (!isset($h[$k])) { return null; }
    try { $v = $e->getFieldValue($k); } catch (\Throwable $t) { return null; }
    if ($v instanceof \craft\elements\db\ElementQuery) { return array_map(fn($x) => ['id' => $x->id, 'title' => $x->title, 'section' => $x instanceof Entry ? $x->section->handle : null], $v->status(null)->all()); }
    if (is_object($v) && property_exists($v, 'value')) { return (string)$v->value; }
    if (is_array($v)) {
        return array_map(function ($row) { $o = []; foreach ($row as $k => $c) { if (str_starts_with((string)$k, 'col')) { continue; } $o[$k] = is_array($c) && isset($c['date']) ? substr($c['date'], 0, 10) : $c; } return $o; }, $v);
    }
    return is_object($v) ? (string)$v : $v;
};
$common = ['body', 'footnotes', 'footnotesOn', 'editorNotes', 'researchLeads', 'recordDates', 'recordProvenance'];
$dumpEntry = function ($e, array $extra) use ($fieldsOf, $val, $common) {
    $h = $fieldsOf($e);
    $o = ['id' => $e->id, 'title' => $e->title, 'slug' => $e->slug, 'section' => $e->section->handle, 'enabled' => (bool)$e->enabled];
    foreach (array_merge($common, $extra) as $k) { $v = $val($e, $h, $k); if ($v !== null && $v !== '' && $v !== []) { $o[$k] = $v; } }
    return $o;
};

$out = ['persons' => [], 'officeHoldings' => [], 'affiliations' => [], 'educations' => [], 'candidacies' => [], 'elections' => []];
$personFields = ['bodyAuthorship', 'authorBio', 'fullName', 'birthDate', 'birthDateEdtf', 'birthplace', 'birthEvidence', 'deathDate', 'deathDateEdtf', 'deathEvidence', 'burialPlace', 'burialEvidence', 'occupation', 'spouseOf', 'childOf', 'siblingOf', 'relatedPersons', 'personWebmasterNoteTop', 'personWebmasterNoteBottom', 'personFinePrint', 'personAliases', 'wikidataId', 'personWikipediaUrl'];
foreach (Entry::find()->section('persons')->each(100) as $p) { $out['persons'][] = $dumpEntry($p, $personFields); }
$sets = [
    'officeHoldings' => ['holdingPerson', ['holdingPerson', 'holderName', 'holdingOffice', 'holdingBody', 'holdingDistrict', 'termStart', 'termStartEdtf', 'termEnd', 'termEndEdtf', 'seatLabel', 'selectionMethod', 'howEnded', 'startEvidence', 'endEvidence']],
    'affiliations' => ['affiliationPerson', ['affiliationPerson', 'affiliationBody', 'affiliationKind', 'affiliationTitle', 'affiliationEnded', 'termStart', 'termStartEdtf', 'termEnd', 'termEndEdtf', 'startEvidence', 'endEvidence']],
    'educations' => ['educationPerson', ['educationPerson', 'educationSchool', 'educationYears', 'classOf', 'educationOutcome', 'educationEvidence']],
    'candidacies' => ['candidacyPerson', ['candidacyPerson', 'candidacyElection', 'nameAsPrinted', 'votes', 'outcome', 'outcomeEvidence', 'candidacyDistrict']],
    'elections' => [null, []],
];
foreach ($sets as $sec => [$link, $fields]) {
    foreach (Entry::find()->section($sec)->each(200) as $e) { $out[$sec][] = $dumpEntry($e, $fields); }
}
/* Titles of the archive records the footnotes cite by number, so the report can name them. */
$refs = [];
foreach ($out as $sec => $list) { foreach ($list as $e) { foreach (($e['footnotes'] ?? []) as $f) { if (preg_match_all('~#(\d{3,6})~', (string)($f['note'] ?? ''), $m)) { foreach ($m[1] as $n) { $refs[(int)$n] = true; } } } } }
$out['refTitles'] = [];
foreach (array_chunk(array_keys($refs), 200) as $ids) { foreach (Entry::find()->id($ids)->status(null)->all() as $x) { $out['refTitles'][$x->id] = $x->title . ' (' . $x->section->handle . ')'; } }
$path = "$root/inventory/review/overnight-2026-10-08/_person-claims-dump.json";
file_put_contents($path, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
foreach ($out as $k => $v) { echo str_pad($k, 16) . count($v) . ($k === 'refTitles' ? " records cited by number\n" : " live records\n"); }
echo "wrote $path\n";
