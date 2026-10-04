/**
 * Christopher Trunkey #25409 is tied to the Hart district by personOrganizations, a link that says
 * nothing about why, so the district's page listed him under "Other people connected with it" with no
 * reason (Nathan, 4 October 2026: "He is there for the Measure V oversight committee. Every entry in
 * that section should say what the connection is"). One affiliation: member of the district's
 * Measure V Citizens' Oversight Committee, years not given, from the two sources his profile cites for
 * it (its footnotes 2 and 8, copied). Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/record_trunkey_measure_v_2026_10_04.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$svc = Craft::$app->getEntries(); $el = Craft::$app->getElements();
$t = Entry::find()->id(25409)->status(null)->one(); $bad = [];
$notes = array_values(array_filter($t?->footnotes ?? [], fn($f) => in_array((string)$f['number'], ['2', '8'], true)));
if ($t?->title !== 'Christopher Trunkey' || count($notes) !== 2 || !str_contains($notes[1]['note'], 'Measure V') || !str_contains(strip_tags((string)$t->body), "Measure V Citizens' Oversight Committee")) { $bad[] = 'Trunkey\'s record or its two sources are not as read'; }
$have = Entry::find()->section('affiliations')->status(null)->relatedTo(['and', ['targetElement' => 25409, 'field' => 'affiliationPerson'], ['targetElement' => 21588, 'field' => 'affiliationBody']])->one();
echo 'Trunkey: ' . ($have ? "affiliation exists #{$have->id}" : 'create: member, "Member, Measure V Citizens\' Oversight Committee", years not given') . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
if (!$have) {
    $a = new Entry(); $a->sectionId = $svc->getSectionByHandle('affiliations')->id; $a->setTypeId($svc->getEntryTypeByHandle('affiliation')->id);
    $a->setFieldValues(['affiliationPerson' => [25409], 'affiliationBody' => [21588], 'affiliationKind' => 'member', 'affiliationTitle' => "Member, Measure V Citizens' Oversight Committee", 'affiliationEnded' => 'unknown',
        'footnotes' => array_map(fn($i, $f) => ['number' => (string)($i + 1), 'note' => (string)$f['note'], 'source' => 'editorial-2026'], array_keys($notes), $notes),
        'recordProvenance' => 'record_trunkey_measure_v_2026_10_04.php, 4 October 2026: from the two sources on his record']);
    if (!$el->saveElement($a)) { throw new \RuntimeException('Trunkey: ' . json_encode($a->getFirstErrors())); }
}
$ok = Entry::find()->section('affiliations')->status(null)->relatedTo(['targetElement' => 25409, 'field' => 'affiliationPerson'])->exists();
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('record_trunkey_measure_v_2026_10_04.php', 1, $ok ? 'verified' : 'SHORT', 'Trunkey: member of the Hart district\'s Measure V oversight committee, as an affiliation');
