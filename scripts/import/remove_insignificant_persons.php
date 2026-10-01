/**
 * The person records that hold nothing but lost elections, removed, with the
 * candidate key kept (Nathan, 1 October 2026: "Remove the 32"; Ward Connerly
 * "remove unless he has a valley connection": none found, the archive's only
 * mention being Leon Worden's 1996 column on meeting him in Fresno).
 *
 *   1. Every candidacy linked to a person gets candidateKey = "person:<slug>",
 *      the removed and the kept alike, so the archive still knows who stood
 *      more than once, and every count can read the key.
 *   2. Each person in the list is checked again: nothing but candidacies, none
 *      won, no body, no image, no dates, nothing pointing at them but
 *      candidacies. One that no longer qualifies refuses the run.
 *   3. Their candidacies are unlinked (the election pages print the name as the
 *      ballot did, unlinked) and the person goes to the trash, restorable.
 *
 * The list is the audit's REMOVE group of 1 October less Alan Ferdman, whose
 * profile has since made him significant, plus Ward Connerly.
 * Needs add_candidate_key.php first. Idempotent. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/remove_insignificant_persons.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$elements = Craft::$app->getElements();
$REMOVE = [25163 => 'Andy Martin', 26544 => 'Bob Wagenaar', 25199 => 'Brett Haddock', 25447 => 'Chris Werthe', 25169 => 'Dennis Conn', 25183 => 'Diane Trautman',
    25203 => 'Douglas Fraser', 25187 => 'Duane Harte', 25167 => 'Ed Stevens', 25177 => 'Gary Johnson', 25189 => 'Henry Schultz', 25413 => 'Jesus Henao',
    25185 => 'John Steffen', 25165 => 'Kenneth Dean', 25179 => 'Larry Bird', 25173 => 'Linda Calvert', 25159 => 'Louis Brathwaite', 25421 => 'Mark White',
    25197 => 'Matthew Hargett', 25161 => 'Mike Lyons', 25193 => 'Paul Wieczorek', 25181 => 'Rein Schuerger', 26595 => 'Sage Rafferty', 25393 => 'Sandra Bull',
    25195 => 'Sandra Nichols', 25201 => 'Selina Thomas', 25417 => 'Sharlene Duzick', 26585 => 'Stacy Fortner', 25443 => 'Steven Herskovitz', 25171 => 'Vera Johnson',
    25175 => 'Wayne Carter', 16411 => 'Ward Connerly'];
$keyReady = (bool)Craft::$app->getFields()->getFieldByHandle('candidateKey');
if (!$keyReady) { echo 'NEEDS add_candidate_key.php first (the plan below is still checked)' . PHP_EOL; }
$refused = []; $plan = [];
foreach ($REMOVE as $id => $title) {
    $p = Entry::find()->id($id)->status(null)->one();
    if (!$p) { echo "   #$id $title: already gone" . PHP_EOL; continue; }
    if ($p->title !== $title) { $refused[] = "#$id is not $title"; continue; }
    $live = (new \craft\db\Query())->select(['s.handle'])->from(['r' => '{{%relations}}'])->innerJoin(['el' => '{{%elements}}'], 'el.id = r.sourceId')->innerJoin(['e' => '{{%entries}}'], 'e.id = r.sourceId')->innerJoin(['s' => '{{%sections}}'], 's.id = e.sectionId')
        ->where(['r.targetId' => $id, 'el.revisionId' => null, 'el.draftId' => null, 'el.dateDeleted' => null])->column();
    $other = array_diff($live, ['candidacies']);
    $cands = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $p, 'field' => 'candidacyPerson'])->all();
    $h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
    $why = [];
    if ($other) { $why[] = 'pointed at by ' . implode(', ', array_unique($other)); }
    if (array_filter($cands, fn($c) => (string)$c->outcome->value === 'elected')) { $why[] = 'won an election'; }
    if (trim(strip_tags((string)$p->body)) !== '') { $why[] = 'has a body'; }
    if (in_array('featuredImage', $h) && $p->featuredImage->one()) { $why[] = 'has an image'; }
    if (trim((string)$p->birthDate . (string)$p->deathDate) !== '') { $why[] = 'has dates'; }
    if ($why) { $refused[] = "#$id $title no longer qualifies: " . implode('; ', $why); continue; }
    $plan[$id] = [$p, $cands];
    echo '   #' . str_pad($id, 6) . str_pad($title, 20) . count($cands) . ' candidac' . (count($cands) === 1 ? 'y' : 'ies') . ' keep their names and key person:' . $p->slug . '; record to the trash' . PHP_EOL;
}
$allLinked = count(array_filter(Entry::find()->section('candidacies')->status(null)->all(), fn($c) => (bool)$c->candidacyPerson->status(null)->ids()));
echo PHP_EOL . "candidateKey on all $allLinked linked candidacies: person:<slug>" . PHP_EOL;
echo 'SUMMARY: ' . count($plan) . ' to remove of ' . count($REMOVE) . '.' . PHP_EOL . 'REFUSED: ' . ($refused ? implode(' | ', $refused) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($refused || !$keyReady) { echo 'REFUSING: ' . ($refused ? 'resolve the refusals first' : 'run add_candidate_key.php first') . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    foreach (Entry::find()->section('candidacies')->status(null)->all() as $c) {
        $p = $c->candidacyPerson->status(null)->one(); if (!$p) { continue; }
        $key = 'person:' . $p->slug;
        if ((string)$c->candidateKey === $key) { continue; }
        $c->setFieldValue('candidateKey', $key); if (!$elements->saveElement($c)) { throw new \RuntimeException("key #{$c->id}"); }
    }
    foreach ($plan as $id => [$p, $cands]) {
        foreach ($cands as $c) { $c = Entry::find()->id($c->id)->status(null)->one(); $c->setFieldValue('candidacyPerson', []); if (!$elements->saveElement($c)) { throw new \RuntimeException("unlink #{$c->id}"); } }
        if (!$elements->deleteElement(Entry::find()->id($id)->status(null)->one())) { throw new \RuntimeException("trash #$id"); }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$short = [];
foreach ($plan as $id => [$p, $cands]) {
    if (Entry::find()->id($id)->status(null)->exists()) { $short[] = "#$id not removed"; }
    foreach ($cands as $c) { $r = Entry::find()->id($c->id)->status(null)->one(); if ((string)$r->candidateKey !== 'person:' . $p->slug || trim((string)$r->nameAsPrinted) === '') { $short[] = "#{$c->id} lost its key or name"; } }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: ' . count($plan) . ' removed, their candidacies keyed and named') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('remove_insignificant_persons.php', count($plan), $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'person records with nothing but lost elections removed; candidate keys kept');
if ($short) { throw new \RuntimeException('remove_insignificant_persons: ' . implode('; ', $short)); }
