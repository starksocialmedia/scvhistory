/**
 * birthEvidence, deathEvidence, burialEvidence on person records.
 *
 * WHAT WENT WRONG THAT MADE THIS NECESSARY. The Alex Mentry pilot. Leon's page
 * says "Born Charles Alexander Menetrier (maybe) in France on March 27, 1847
 * (probably)". Three sources give three birth years — Reynolds says 1846, the
 * death certificate says March 27 1847 and then says he was 52 years, 6 months
 * and 8 days old when he died on 4 October 1900, which computes to March 27
 * 1848. The month and the day are well attested; only the year is contested.
 *
 * birthDateEdtf can carry that: '1847?-03-27' qualifies the year alone. What no
 * field could carry is how good the claim is. A person record has six fields
 * asserting births, deaths and burials and not one of them says whether the
 * value came from a certificate or from somebody's recollection in 2014.
 * officeHolding got startEvidence and endEvidence in September; persons never
 * did, which is how a hedged date and a certified one render identically.
 *
 * THE RULE THIS FOLLOWS, and the reason it is not "every disputable field".
 * "Anything someone could dispute" is every field in the archive, and a rule
 * that doubles the schema gets abandoned in a month. The line that actually
 * holds:
 *
 *   An evidence sibling belongs on any field where the archive states a
 *   conclusion the source did not state in that form.
 *
 * So there are two kinds of field and only one of them needs evidence:
 *
 *   TRANSCRIPTION — birthDate 'March 27, 1847 (probably)', nameAsPrinted,
 *     votesAsPrinted. These ARE the evidence. Their failure mode is a
 *     mistranscription, which is what sourceFault records. No sibling.
 *   INTERPRETATION — birthDateEdtf '1847?-03-27', votes, outcome, burialPlace.
 *     Here the archive has decided something. It owes the reader a basis.
 *
 * ONE VALUE PER CLAIM, NOT PER FIELD. A birth is established by one source act,
 * so birthDate, birthDateEdtf and birthplace share birthEvidence. Six fields,
 * three evidence values. Per-field evidence would invite three different
 * answers to a question that has one.
 *
 * burialPlace gets its own, and deliberately: it is nearly always established
 * by a different source than the death. For Mentry the death is a certificate
 * and the burial is "Cemetery information from Jack and Joan Beitzel" — a
 * credit line with no document behind it. One evidence value across both would
 * average a certified fact and a personal communication into a single claim
 * that is wrong at one end, which is the same argument that split officeHolding
 * into startEvidence and endEvidence.
 *
 * The levels are officeHolding's, unchanged, so certified means the same thing
 * on a term and on a birth.
 *
 * NOT DONE HERE, on purpose: organization, place, photograph, article and
 * document carry the same bare claims and get the same treatment when each is
 * next opened. A single migration across five types, none of which has a
 * pending editorial pass, is a large write with no reader waiting for it.
 *
 * Idempotent: each field is created only when missing and appended to the
 * layout only when absent. A second run writes nothing.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_person_evidence_fields.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database and to project config' . PHP_EOL; }

$TYPE = 'person';

/* Identical to officeHolding's startEvidence/endEvidence. Verified against the
   live field rather than retyped from the docs; the check is below. */
$LEVELS = [
    'certified' => 'Certified: the body\'s own record',
    'contemporary' => 'Contemporary report',
    'retrospective' => 'Retrospective account',
    'roster' => 'Undated roster',
    'uncited' => 'Uncited',
];

$NEW = [
    'birthEvidence'  => ['Evidence for the birth',  'Covers birthDate, birthDateEdtf and birthplace. How good is the claim, not what it says.'],
    'deathEvidence'  => ['Evidence for the death',  'Covers deathDate and deathDateEdtf.'],
    'burialEvidence' => ['Evidence for the burial', 'Covers burialPlace. Separate from the death on purpose: the two usually come from different sources.'],
];

$fs = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 76) . PHP_EOL;

$type = $svc->getEntryTypeByHandle($TYPE);
if (!$type) { throw new \RuntimeException('add_person_evidence_fields: no person entry type'); }
$have = [];
foreach ($type->getFieldLayout()->getCustomFields() as $f) { $have[$f->handle] = true; }

/* The levels must match officeHolding exactly. If they have drifted, stop:
   two vocabularies that look the same and are not is worse than one missing
   field. */
$ref = $fs->getFieldByHandle('startEvidence');
if (!$ref) { throw new \RuntimeException('add_person_evidence_fields: startEvidence is missing; run add_office_holding.php first'); }
$refValues = array_column($ref->options, 'value');
$want = array_keys($LEVELS);
if ($refValues !== $want) {
    throw new \RuntimeException('add_person_evidence_fields: the levels have drifted from startEvidence. '
        . 'startEvidence: ' . implode(',', $refValues) . ' / here: ' . implode(',', $want));
}
echo 'levels match startEvidence: ' . implode(', ', $want) . PHP_EOL . PHP_EOL;

echo 'fields:' . PHP_EOL;
foreach ($NEW as $handle => [$label, $note]) {
    $exists = (bool)$fs->getFieldByHandle($handle);
    $onLayout = isset($have[$handle]);
    printf("   %-16s %-14s %s\n", $handle,
        $exists ? 'field exists' : 'would create',
        $onLayout ? 'already on the person layout' : 'would add to the person layout');
    echo '        ' . $note . PHP_EOL;
}

/* What the archive would be able to say that it cannot say now. */
echo PHP_EOL . 'the claims these would qualify, as they stand today:' . PHP_EOL;
$counts = ['birthDate' => 0, 'birthDateEdtf' => 0, 'birthplace' => 0, 'deathDate' => 0, 'deathDateEdtf' => 0, 'burialPlace' => 0];
$people = 0;
foreach (\craft\elements\Entry::find()->section('persons')->status(null)->limit(null)->each() as $p) {
    $people++;
    foreach (array_keys($counts) as $h) {
        if (!isset($have[$h])) { continue; }
        try { $v = trim((string)$p->getFieldValue($h)); } catch (\Throwable $e) { continue; }
        if ($v !== '') { $counts[$h]++; }
    }
}
foreach ($counts as $h => $n) { printf("   %-18s %3d of %d person records\n", $h, $n, $people); }
echo '   every one of them currently renders as fact, with no way to say otherwise.' . PHP_EOL;

if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 76) . PHP_EOL;
    echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    echo 'then: the person template renders the level beside the date, and the Mentry' . PHP_EOL;
    echo 'profile is the first record to use it.' . PHP_EOL;
    return;
}

$created = 0;
$layoutAdds = [];
foreach ($NEW as $handle => [$label, $note]) {
    $f = $fs->getFieldByHandle($handle);
    if (!$f) {
        $f = new \craft\fields\Dropdown();
        $f->name = $label;
        $f->handle = $handle;
        $f->instructions = $note;
        $f->options = array_map(
            fn($v, $l) => ['label' => $l, 'value' => $v, 'default' => false],
            array_keys($LEVELS), array_values($LEVELS)
        );
        if (!$fs->saveField($f)) {
            throw new \RuntimeException('add_person_evidence_fields: field ' . $handle . ' refused: ' . json_encode($f->getErrors()));
        }
        $created++;
        $f = $fs->getFieldByHandle($handle);
    }
    if (!isset($have[$handle])) { $layoutAdds[] = $f; }
}

if ($layoutAdds) {
    $layout = $type->getFieldLayout();
    $tabs = $layout->getTabs();
    $first = $tabs[0];
    $els = $first->getElements();
    foreach ($layoutAdds as $f) { $els[] = new \craft\fieldlayoutelements\CustomField($f); }
    $first->setElements($els);
    $layout->setTabs($tabs);
    $type->setFieldLayout($layout);
    if (!$svc->saveEntryType($type)) {
        throw new \RuntimeException('add_person_evidence_fields: entry type refused: ' . json_encode($type->getErrors()));
    }
    $created += count($layoutAdds);
    echo 'added to the person layout: ' . count($layoutAdds) . PHP_EOL;
}

/* -------------------------------------------------------------- read back */
Craft::$app->getFields()->refreshFields();
$t2 = $svc->getEntryTypeByHandle($TYPE);
$present = array_map(fn($c) => $c->handle, $t2->getFieldLayout()->getCustomFields());
$missing = array_values(array_diff(array_keys($NEW), $present));
$optionsOk = true;
foreach (array_keys($NEW) as $h) {
    $f = $fs->getFieldByHandle($h);
    if (!$f || array_column($f->options, 'value') !== $want) { $optionsOk = false; }
}
echo PHP_EOL . 'READ-BACK ' . (!$missing && $optionsOk ? 'OK' : 'FAIL') . PHP_EOL;
echo '   on the layout: ' . count(array_intersect(array_keys($NEW), $present)) . ' of ' . count($NEW) . PHP_EOL;
echo '   option lists match startEvidence: ' . ($optionsOk ? 'yes' : 'NO') . PHP_EOL;
if ($missing) { echo '   missing: ' . implode(', ', $missing) . PHP_EOL; }
if ($missing || !$optionsOk) { throw new \RuntimeException('add_person_evidence_fields read-back failed'); }

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_person_evidence_fields.php', $created,
    'verified: 3 dropdowns on the person layout, levels identical to startEvidence',
    'evidence per claim, not per field; a hedge now has somewhere to live');
