/**
 * Bodies for the state, federal and county offices this valley's people have
 * held (Nathan, 3 October 2026: "McKeon's Congress record having no body is
 * worth fixing early. The House, Assembly, Senate and Board of Supervisors are
 * bodies this valley's people have served in, and the archive cannot say so").
 * Step 1 of the relationship model (inventory/review/relationship-model-2026-10-03.md).
 *
 * Creates four organizations, orgType government, no body text (nothing about
 * them is claimed): the United States House of Representatives, the California
 * State Assembly, the California State Senate, and the Los Angeles County Board
 * of Supervisors. Then:
 *   - Buck McKeon's Congress holding (#26980) gets the House as its body.
 *   - Christy Smith gets an Assembly holding, 38th District, December 2018 to
 *     December 2020, from the Secretary of State's returns already saved for her
 *     profile (inventory/elections/sos/): won in 2018, not a candidate in 2020.
 * Smyth, Acosta and Wilk carry Assembly or Senate offices as roles only; their
 * holdings wait for their returns (the next step), not created on a role alone.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_state_federal_bodies.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$svc = Craft::$app->getEntries(); $el = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$BODIES = ['United States House of Representatives', 'California State Assembly', 'California State Senate', 'Los Angeles County Board of Supervisors'];
$bad = [];
$have = []; foreach ($BODIES as $t) { $have[$t] = Entry::find()->section('organizations')->status(null)->title($t)->one(); echo ($have[$t] ? "#{$have[$t]->id} exists: " : 'create organization: ') . $t . PHP_EOL; }
$mk = $get(26980);
if (!$mk || $mk->section->handle !== 'officeHoldings' || $mk->holdingPerson->one()?->id !== 18791 || $mk->holdingOffice->one()?->title !== 'Congressman') { $bad[] = '#26980 is not McKeon\'s Congress holding'; }
$mkBody = $mk?->holdingBody->one();
if ($mkBody && $mkBody->title !== 'United States House of Representatives') { $bad[] = '#26980 already has another body, ' . $mkBody->title; }
echo '#26980 McKeon, Congressman 1993 to 2015: ' . ($mkBody ? 'body already set' : 'body -> United States House of Representatives') . PHP_EOL;
/* Smith's Assembly term, from the returns saved for her profile. */
$SOS = "$root/inventory/elections/sos"; $SM = json_decode(file_get_contents("$SOS/manifest.json"), true)['files'];
$read = fn($f) => hash_file('sha256', "$SOS/$f") === $SM[$f]['sha256'] ? preg_replace('~\s+~', ' ', (string)file_get_contents("$SOS/" . substr($f, 0, -4) . '.txt')) : '';
$t18 = $read('2018-general-68-state-assemblymember.pdf'); $i = strpos($t18, '38th Assembly District'); $b18 = $i === false ? '' : substr($t18, $i, 400);
$t20 = $read('2020-general-41-state-assembly.pdf'); $j = strpos($t20, '38th Assembly District'); $b20 = $j === false ? '' : substr($t20, $j, 400);
if (!str_contains($b18, 'Christy Dante Smith Acosta*') || !str_contains($b18, 'District Totals 95,751 90,298')) { $bad[] = 'the 2018 return does not read as expected'; }
if (!str_contains($b20, 'Suzette Lucie Martinez Lapointe') || str_contains($b20, 'Christy')) { $bad[] = 'the 2020 return does not read as expected'; }
$smith = $get(25389); $ASM = $get(18313); $D38 = null;
$smithAsm = Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => 25389, 'field' => 'holdingPerson'])->all();
$smithAsm = array_values(array_filter($smithAsm, fn($h) => $h->holdingOffice->one()?->id === 18313));
if (!$smith || $smith->title !== 'Christy Smith' || !$ASM || $ASM->title !== 'State Assemblymember') { $bad[] = 'Smith or the State Assemblymember role is not where expected'; }
echo 'Christy Smith, State Assemblymember, 38th District, 2018-12 to 2020-12: ' . ($smithAsm ? 'exists #' . $smithAsm[0]->id : 'create') . PHP_EOL;
$N18 = 'California Secretary of State, Statement of Vote, General Election, November 6, 2018, State Assemblymember, 38th Assembly District: Christy Smith (DEM) 95,751, 51.5 per cent; Dante Acosta (REP, incumbent) 90,298, 48.5 per cent, ' . $SM['2018-general-68-state-assemblymember.pdf']['url'] . '.';
$N20 = 'California Secretary of State, Statement of Vote, General Election, November 3, 2020, State Assembly, 38th Assembly District: Suzette Martinez Valladares and Lucie Lapointe Volotzky; Smith was not a candidate, ' . $SM['2020-general-41-state-assembly.pdf']['url'] . '.';
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$n = 0;
$tx = Craft::$app->getDb()->beginTransaction();
try {
    $os = $svc->getSectionByHandle('organizations');
    foreach ($BODIES as $t) {
        if ($have[$t]) { continue; }
        $o = new Entry(); $o->sectionId = $os->id; $o->setTypeId($os->getEntryTypes()[0]->id); $o->title = $t;
        $o->setFieldValues(['orgType' => 'government', 'recordProvenance' => 'create_state_federal_bodies.php, 3 October 2026: a body for the offices valley people held']);
        if (!$el->saveElement($o)) { throw new \RuntimeException("$t: " . json_encode($o->getFirstErrors())); }
        $have[$t] = $o; $n++;
    }
    if (!$mkBody) { $mk->setFieldValue('holdingBody', [$have['United States House of Representatives']->id]); if (!$el->saveElement($mk)) { throw new \RuntimeException('#26980: ' . json_encode($mk->getFirstErrors())); } $n++; }
    if (!$smithAsm) {
        $h = new Entry(); $hs = $svc->getSectionByHandle('officeHoldings'); $h->sectionId = $hs->id; $h->setTypeId($svc->getEntryTypeByHandle('officeHolding')->id);
        $h->setFieldValues(['holdingPerson' => [25389], 'holdingOffice' => [18313], 'holdingBody' => [$have['California State Assembly']->id], 'seatLabel' => '38th Assembly District',
            'termStart' => 'December 2018', 'termStartEdtf' => '2018-12', 'termEnd' => 'December 2020', 'termEndEdtf' => '2020-12', 'selectionMethod' => 'elected', 'howEnded' => 'expired', 'startEvidence' => 'certified', 'endEvidence' => 'certified',
            'footnotes' => $fn([$N18, $N20]), 'recordProvenance' => 'create_state_federal_bodies.php, 3 October 2026']);
        if (!$el->saveElement($h)) { throw new \RuntimeException('Smith holding: ' . json_encode($h->getFirstErrors())); }
        $n++;
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$ok = $get(26980)->holdingBody->one()?->title === 'United States House of Representatives' && count(array_filter($BODIES, fn($t) => Entry::find()->section('organizations')->status(null)->title($t)->exists())) === 4;
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . ", $n writes" . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('create_state_federal_bodies.php', $n, $ok ? 'verified' : 'SHORT', 'House, Assembly, Senate, Supervisors; McKeon\'s Congress holding given its body; Smith\'s Assembly term');
if (!$ok) { throw new \RuntimeException('create_state_federal_bodies: read-back failed'); }
