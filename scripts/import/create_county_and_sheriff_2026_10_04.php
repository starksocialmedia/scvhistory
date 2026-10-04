/**
 * The County of Los Angeles and its Sheriff's Department, and the valley's two most recent supervisors
 * (Nathan, 4 October 2026: "County of Los Angeles, with the Board of Supervisors under it. The 5th District
 * covers this valley: Michael Antonovich held it, then Kathryn Barger. Both need person records and office
 * holdings"; "Los Angeles County Sheriff's Department, which we will need for the sheriff's memorials. Note
 * that as the reason it exists").
 *
 *  1. County of Los Angeles: a government organization; the Board of Supervisors (#28275, created in the
 *     state-and-federal pass) is nested under it.
 *  2. Los Angeles County Sheriff's Department: a government organization under the County, created for the
 *     sheriff's memorials; that reason is in its record's provenance and an editor's note.
 *  3. Michael D. Antonovich and Kathryn Barger: person records, role Supervisor, and a holding each on the
 *     Board for the 5th District: 1980 to 2016 and 2016 to now, at year precision, from the list of the
 *     valley's county supervisors on Leon Worden's Castaic Lake page (photograph record #3795, "COUNTY
 *     SUPERVISORS ... Michael D. Antonovich 1980-2016 ... Kathyrn Barger 2016-"), evidence roster. How far
 *     their records go beyond this is Nathan's to decide.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_county_and_sheriff_2026_10_04.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$svc = Craft::$app->getEntries(); $el = Craft::$app->getElements(); $bad = [];
$org = fn($t) => Entry::find()->section('organizations')->status(null)->title($t)->one();
$person = fn($t) => Entry::find()->section('persons')->status(null)->title($t)->one();
$SUP = Entry::find()->id(28275)->status(null)->one(); $ROLE = Entry::find()->section('roles')->status(null)->title('Supervisor')->one();
$SRC = Entry::find()->id(3795)->status(null)->one(); $body = (string)$SRC?->body;
foreach (['COUNTY SUPERVISORS', 'Michael D. Antonovich 1980-2016', 'Kathyrn Barger 2016'] as $q) { if (!str_contains($body, $q)) { $bad[] = "#3795 does not read: $q"; } }
if ($SUP?->title !== 'Los Angeles County Board of Supervisors' || !$ROLE) { $bad[] = 'the Board or the Supervisor role is not where expected'; }
$CITE = 'The list of the valley\'s county supervisors on Leon Worden\'s page of Castaic Lake photographs, as held in this archive (photograph record #3795, "Warren Dorn & Amean G. Haddad at Castaic Lake Dedication," ' . $SRC?->legacyUrl . '): "COUNTY SUPERVISORS ... Michael D. Antonovich 1980-2016 ... Kathyrn Barger 2016-." Years only; the list gives no months.';
$C = $org('County of Los Angeles'); $S = $org('Los Angeles County Sheriff\'s Department');
echo 'County of Los Angeles: ' . ($C ? "#{$C->id} exists" : 'create') . '; the Board of Supervisors nested under it' . PHP_EOL;
echo 'Los Angeles County Sheriff\'s Department: ' . ($S ? "#{$S->id} exists" : 'create, under the County, for the sheriff\'s memorials') . PHP_EOL;
$P = [['Michael D. Antonovich', 'Michael Antonovich', '1980', '1980', '2016', '2016', 'expired'], ['Kathryn Barger', '', '2016', '2016', '', '', 'serving']];
foreach ($P as [$n, $alias, $sp, $se, $ep, $ee, $how]) { $p = $person($n); echo "$n: " . ($p ? "#{$p->id} exists" : 'create') . "; Supervisor, 5th District, $se to " . ($ee ?: 'now') . PHP_EOL; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$PROV = 'create_county_and_sheriff_2026_10_04.php, 4 October 2026';
$os = $svc->getSectionByHandle('organizations'); $n = 0;
$tx = Craft::$app->getDb()->beginTransaction();
try {
    if (!$C) { $C = new Entry(); $C->sectionId = $os->id; $C->setTypeId($os->getEntryTypes()[0]->id); $C->title = 'County of Los Angeles'; $C->setFieldValues(['orgType' => 'government', 'recordProvenance' => $PROV]); if (!$el->saveElement($C)) { throw new \RuntimeException('County: ' . json_encode($C->getFirstErrors())); } $n++; }
    if ($SUP->parentOrganization->one()?->id !== $C->id) { $SUP->setFieldValue('parentOrganization', [$C->id]); if (!$el->saveElement($SUP)) { throw new \RuntimeException('#28275'); } $n++; }
    if (!$S) { $S = new Entry(); $S->sectionId = $os->id; $S->setTypeId($os->getEntryTypes()[0]->id); $S->title = 'Los Angeles County Sheriff\'s Department';
        $S->setFieldValues(['orgType' => 'government', 'parentOrganization' => [$C->id], 'recordProvenance' => $PROV . ': created for the war memorial\'s and the archive\'s sheriff\'s memorials (Nathan, 4 October 2026)',
            'editorNotes' => [['heading' => 'Why this record exists', 'position' => 'bottom', 'note' => 'This record was made so the archive\'s memorials to sheriff\'s deputies can name the department they served in.']]]);
        if (!$el->saveElement($S)) { throw new \RuntimeException('Sheriff: ' . json_encode($S->getFirstErrors())); } $n++; }
    $pSec = $svc->getSectionByHandle('persons'); $pType = $svc->getEntryTypeByHandle('person'); $hs = $svc->getSectionByHandle('officeHoldings'); $ht = $svc->getEntryTypeByHandle('officeHolding');
    foreach ($P as [$name, $alias, $sp, $se, $ep, $ee, $how]) {
        $p = $person($name);
        if (!$p) { $p = new Entry(); $p->sectionId = $pSec->id; $p->setTypeId($pType->id); $p->setFieldValues(array_filter(['fullName' => $name, 'personAliases' => $alias, 'roles' => [$ROLE->id], 'recordProvenance' => $PROV])); if (!$el->saveElement($p)) { throw new \RuntimeException("$name: " . json_encode($p->getFirstErrors())); } $n++; }
        if (Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $p, 'field' => 'holdingPerson'], ['targetElement' => $SUP, 'field' => 'holdingBody']])->exists()) { continue; }
        $h = new Entry(); $h->sectionId = $hs->id; $h->setTypeId($ht->id);
        $v = ['holdingPerson' => [$p->id], 'holdingOffice' => [$ROLE->id], 'holdingBody' => [$SUP->id], 'seatLabel' => '5th District', 'termStart' => $sp, 'termStartEdtf' => $se, 'howEnded' => $how, 'startEvidence' => 'roster', 'footnotes' => [['number' => '1', 'note' => $CITE, 'source' => 'editorial-2026']], 'recordProvenance' => $PROV];
        if ($ee) { $v['termEnd'] = $ep; $v['termEndEdtf'] = $ee; $v['endEvidence'] = 'roster'; }
        $h->setFieldValues($v); if (!$el->saveElement($h)) { throw new \RuntimeException("$name holding: " . json_encode($h->getFirstErrors())); } $n++;
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
echo "READ-BACK OK: $n writes" . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('create_county_and_sheriff_2026_10_04.php', $n, 'verified', 'County of Los Angeles (Board of Supervisors under it); Sheriff\'s Department for the memorials; Antonovich and Barger, 5th District');
