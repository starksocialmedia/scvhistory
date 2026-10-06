/**
 * Five of the 25 blank records, settled (Nathan, 3 October 2026):
 *
 *   California Battalion (group #946)  "fold into Frémont". No source here
 *        names it the California Battalion; Reynolds and Worden write of
 *        Frémont's hundred-man "buckskin battalion", which his profile already
 *        tells. Reynolds chapter 18 (#857), which named the group as its
 *        subject, names Frémont instead; the four people's group links go.
 *        /groups/california-battalion redirects to Frémont.
 *   Catalonian Volunteers (group #940)  "fold into Portolá". Its one member
 *        link (Pedro Fages) is already in the Portolá Expedition; the
 *        expedition's record will tell of Fages's twenty-five Catalonian
 *        soldiers. /groups/catalonian-volunteers redirects to the expedition.
 *   De Anza Expedition (group #942)  "retire". Reynolds chapter 10 (#839) and
 *        Anza's person record lose the link; no source puts the expedition in
 *        the valley.
 *   Mission San Francisco de Asís (#16515), Mission Santa Cruz (#16517)
 *        "retire. Nothing holds them here." Nothing points at either. The two
 *        organization addresses that redirected to them are taken out of
 *        config/redirects.php (same commit), or they would end at a 404.
 * Each goes to Craft's trash (30 days) and into removed-claims.json.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fold_and_retire_records.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $els = Craft::$app->getElements();
$GO = [
    946 => ['California Battalion', 'groups', 'Folded into John C. Frémont (#307): no source here names a California Battalion; the sources tell of Frémont\'s "buckskin battalion", and his record holds it.', 307],
    940 => ['Catalonian Volunteers', 'groups', 'Folded into the Portolá Expedition (#938): the Volunteers are in this archive as the expedition\'s soldiers under Pedro Fages.', 938],
    942 => ['De Anza Expedition', 'groups', 'Retired: no source in the archive puts the expedition in the Santa Clarita Valley.', null],
    16515 => ['Mission San Francisco de Asís', 'places', 'Retired: nothing in the archive holds it to the Santa Clarita Valley.', null],
    16517 => ['Mission Santa Cruz', 'places', 'Retired: nothing in the archive holds it to the Santa Clarita Valley.', null],
];
$REDIRECT_RULES = ["'groups/california-battalion'", "'groups/catalonian-volunteers'"];
$DEAD_RULES = ["'organizations/mission-san-francisco-de-asis'", "'organizations/mission-santa-cruz'"];
$redir = file_get_contents("$root/config/redirects.php");
$bad = [];
foreach ($REDIRECT_RULES as $r) { if (!str_contains($redir, $r)) { $bad[] = "config/redirects.php lacks $r"; } }
foreach ($DEAD_RULES as $r) { if (str_contains($redir, $r)) { $bad[] = "config/redirects.php still sends $r to a retired record"; } }
$REG = "$root/scripts/import/removed-claims.json"; $reg = json_decode(file_get_contents($REG), true);
$plan = [];
foreach ($GO as $id => [$title, $sec, $why, $into]) {
    $e = Entry::find()->id($id)->status(null)->one();
    if ($e && ($e->title !== $title || $e->section->handle !== $sec)) { $bad[] = "#$id is {$e->title}"; continue; }
    $links = [];
    if ($e) {
        foreach (Entry::find()->relatedTo(['targetElement' => $e])->status(null)->limit(null)->all() as $s) {
            foreach ($s->getFieldLayout()->getCustomFields() as $f) {
                /* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
                if ($f instanceof \craft\fields\BaseRelationField && in_array($id, $s->getFieldValue($f->handle)->status(null)->ids(), true)) { $links[] = [$s, $f->handle]; }
            }
        }
    }
    $inReg = (bool)array_filter($reg['removedRecords'], fn($x) => $x['record'] === $id);
    $plan[$id] = [$e, $links, $inReg];
    echo "$title #$id: " . ($e ? count($links) . ' links to take off; to the trash' : 'already removed') . '; registry ' . ($inReg ? 'listed' : 'to add') . PHP_EOL;
    foreach ($links as [$s, $h]) { echo "     {$s->section->handle} #{$s->id} $h  {$s->title}" . PHP_EOL; }
}
$a857 = Entry::find()->id(857)->status(null)->one();
$needFremont = !in_array(307, $a857->subjectPerson->status(null)->ids(), true);
echo 'Reynolds ch. 18 #857: Frémont as subjectPerson ' . ($needFremont ? 'to add' : 'held') . PHP_EOL;
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }

$short = [];
if ($needFremont) { $a857->setFieldValue('subjectPerson', array_merge($a857->subjectPerson->status(null)->ids(), [307])); if (!$els->saveElement($a857)) { $short[] = '#857'; } }
foreach ($plan as $id => [$e, $links, $inReg]) {
    foreach ($links as [$s, $h]) {
        $s = Entry::find()->id($s->id)->status(null)->one();
        $s->setFieldValue($h, array_values(array_diff($s->getFieldValue($h)->status(null)->ids(), [$id])));
        if (!$els->saveElement($s)) { $short[] = "#{$s->id} $h"; }
    }
    if (!$inReg) { [$t, $sec, $why] = $GO[$id]; $reg['removedRecords'][] = ['record' => $id, 'title' => $t, 'section' => $sec, 'why' => $why, 'removed' => '2026-10-03', 'by' => 'scripts/import/fold_and_retire_records.php']; }
    if ($e && !$short) { if (!$els->deleteElement($e)) { $short[] = "delete #$id"; } }
}
file_put_contents($REG, json_encode($reg, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
$n = 0;
foreach ($GO as $id => [$t]) {
    $ok = !Entry::find()->id($id)->status(null)->exists() && !Entry::find()->relatedTo(['targetElement' => $id])->status(null)->exists();
    $ok ? $n++ : $short[] = $t; echo ($ok ? 'OK    ' : 'SHORT ') . $t . PHP_EOL;
}
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('fold_and_retire_records.php', $n, $short ? 'SHORT' : 'verified', 'California Battalion folded into Frémont, Catalonian Volunteers into Portolá; De Anza and two missions retired');
if ($short) { throw new \RuntimeException('fold_and_retire_records: ' . implode(', ', $short)); }
