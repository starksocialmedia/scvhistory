/**
 * The held names, settled (Nathan, 30 September 2026: "the first six pairs are
 * each one person ... merge each with the variant as an alias").
 *
 *   Ken Dean            into Kenneth Dean (#25165): the two 2006 and 2018
 *                       council candidacies join the six he already has.
 *   Tom Caesar          a record, with "Thomas L. Caesar" (Castaic 1995, 1999).
 *   Bob Wagenaar        a record, with "Robert N. Wagenaar" (Hart 1995, 1999, 2001).
 *   Joe Messina         a record, with "Joseph Vincent Messina" (Hart 2003-2022).
 *   Gloria Mercado      into Gloria Mercado-Fortine (#25445): the four Hart
 *                       candidacies of #25435 move, its printings become
 *                       aliases, and #25435 goes to the trash (restorable);
 *                       /persons/gloria-mercado redirects.
 *
 * HELD, not merged, and said so in the report:
 *   Bob Jensen and Robert N. Jensen, Jr. Nathan listed the pair to merge, but
 *   the list he was shown dropped the suffix, and his rule for a suffix is
 *   separate until sourced. Both give "CPA" as their ballot designation.
 *   Philip Ellis and Philip Ellis, Jr.; Lester Freeman and Lester Freeman, III:
 *   separate until sourced (Nathan).
 *   "G Mercado-Fortine" (Hart, 2015, the incumbent): an initial, not a name.
 *   Not linked; CEDA marks the candidate as the incumbent, which only Gloria
 *   Mercado-Fortine was. Nathan's call.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/merge_held_names.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$refused = [];
$cands = Entry::find()->section('candidacies')->status(null)->all();
$printed = fn(array $names) => array_values(array_filter($cands, fn($c) => in_array((string)$c->nameAsPrinted, $names, true)));
$yearOf = fn($c) => substr($c->candidacyElection->status(null)->one()->electionDateEdtf, 0, 4);

/* target: an existing id, or a title to create. printings: the names as printed that are this person. */
$PLAN = [
    ['target' => 25165, 'title' => 'Kenneth Dean', 'printings' => ['Ken Dean', 'Kenneth Dean'], 'aliases' => ['Ken Dean']],
    ['target' => null, 'title' => 'Tom Caesar', 'printings' => ['Tom L. Caesar', 'Thomas L. Caesar'], 'aliases' => ['Tom L. Caesar', 'Thomas L. Caesar', 'Thomas Caesar']],
    ['target' => null, 'title' => 'Bob Wagenaar', 'printings' => ['Bob Wagenaar', 'Robert N. Wagenaar'], 'aliases' => ['Robert N. Wagenaar', 'Robert Wagenaar']],
    ['target' => null, 'title' => 'Joe Messina', 'printings' => ['Joe Messina', 'Joseph Vincent Messina'], 'aliases' => ['Joseph Vincent Messina', 'Joseph Messina']],
    ['target' => 25445, 'title' => 'Gloria Mercado-Fortine', 'printings' => ['Gloria Mercado-Fortine', 'Gloria Mercado', 'Gloria E. Mercado'], 'aliases' => ['Gloria Mercado', 'Gloria E. Mercado'], 'retire' => 25435],
];
$todo = [];
foreach ($PLAN as $i => $m) {
    $t = $m['target'] ? $get($m['target']) : Entry::find()->section('persons')->status(null)->title($m['title'])->one();
    if ($m['target'] && (!$t || $t->title !== $m['title'])) { $refused[] = "#{$m['target']} is not {$m['title']}"; continue; }
    if (!$m['target'] && Entry::find()->section('persons')->status(null)->title($m['title'])->count() > 1) { $refused[] = "more than one {$m['title']}"; continue; }
    $cs = $printed($m['printings']);
    $foreign = array_filter($cs, fn($c) => ($p = $c->candidacyPerson->status(null)->ids()) && $p[0] !== $t?->id && $p[0] !== ($m['retire'] ?? null));
    if ($foreign) { $refused[] = "{$m['title']}: a candidacy is linked to someone else: " . implode(', ', array_map(fn($c) => "#{$c->id}", $foreign)); }
    $move = array_filter($cs, fn($c) => !$t || ($c->candidacyPerson->status(null)->ids()[0] ?? null) !== $t->id);
    $haveAl = $t ? array_map('trim', preg_split('~\n~', (string)$t->personAliases)) : [];
    $addAl = array_values(array_diff($m['aliases'], $haveAl));
    echo ($t ? "#{$t->id} {$t->title}" : "create {$m['title']}") . ': ' . count($cs) . ' candidacies (' . implode(', ', array_map(fn($c) => $yearOf($c) . ' ' . $c->nameAsPrinted, $cs)) . ')'
        . ($move ? ', ' . count($move) . ' to link' : '') . ($addAl ? '; aliases + ' . implode('; ', $addAl) : '') . (isset($m['retire']) ? "; retire #{$m['retire']} " . ($get($m['retire'])?->title ?? '(gone)') : '') . PHP_EOL;
    $todo[$i] = [$t, $move, $addAl];
}
echo PHP_EOL . 'HELD: Bob Jensen / Robert N. Jensen, Jr. (a suffix); Philip C. Ellis / Philip C. Ellis, Jr.; Lester M. Freeman / Lester M. Freeman, III; "G Mercado-Fortine" 2015 (an initial).' . PHP_EOL;
echo 'REFUSED: ' . ($refused ? implode(' | ', $refused) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($refused) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    $pSec = $svc->getSectionByHandle('persons'); $pType = $svc->getEntryTypeByHandle('person');
    foreach ($PLAN as $i => $m) {
        [$t, $move, $addAl] = $todo[$i];
        if (!$t) {
            $t = new Entry(); $t->sectionId = $pSec->id; $t->setTypeId($pType->id); $t->title = $m['title'];
            $t->setFieldValues(['fullName' => $m['title'], 'recordProvenance' => 'merge_held_names.php, 30 September 2026: stood more than once for a school board; the two printings are one person (Nathan); public facts only']);
        }
        if ($addAl) { $t->setFieldValue('personAliases', trim(implode("\n", array_filter(array_merge(preg_split('~\n~', (string)$t->personAliases), $addAl))))); }
        if (!$t->id || $addAl) { if (!$elements->saveElement($t)) { throw new \RuntimeException("{$m['title']}: " . json_encode($t->getFirstErrors())); } }
        /* Worked out now the person exists: a new person has no id at plan time, and
           "no person" equal to "no person yet" moved nothing (caught by the
           read-back in test, 30 September). */
        foreach ($printed($m['printings']) as $c) { $c = $get($c->id); if (($c->candidacyPerson->status(null)->ids()[0] ?? null) === $t->id) { continue; } $c->setFieldValue('candidacyPerson', [$t->id]); if (!$elements->saveElement($c)) { throw new \RuntimeException("candidacy #{$c->id}"); } }
        if (isset($m['retire']) && ($old = $get($m['retire']))) {
            $left = (int)(new \craft\db\Query())->from(['r' => '{{%relations}}'])->innerJoin(['el' => '{{%elements}}'], 'el.id = r.sourceId')->where(['r.targetId' => $old->id, 'el.revisionId' => null, 'el.draftId' => null, 'el.dateDeleted' => null])->count();
            if ($left) { throw new \RuntimeException("#{$old->id} still has $left inbound relation(s)"); }
            if (!$elements->deleteElement($old)) { throw new \RuntimeException("retire #{$old->id}"); }
        }
    }
    $tx->commit();
} catch (\Throwable $e) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $e->getMessage() . PHP_EOL; throw $e; }

$short = [];
foreach ($PLAN as $m) {
    $t = Entry::find()->section('persons')->status(null)->title($m['title'])->one();
    foreach ($printed($m['printings']) as $c) { if (($get($c->id)->candidacyPerson->status(null)->ids()[0] ?? null) !== $t?->id) { $short[] = "#{$c->id} not on {$m['title']}"; } }
    if (isset($m['retire']) && $get($m['retire'])) { $short[] = "#{$m['retire']} not retired"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('merge_held_names.php', count($PLAN), $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'Dean, Caesar, Wagenaar, Messina, Mercado-Fortine: one person each');
if ($short) { throw new \RuntimeException('merge_held_names: ' . implode('; ', $short)); }
