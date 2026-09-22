/**
 * Merges the two remaining person duplicates, each one man under two records.
 *
 *   #15908 Edward F. Beale     -> #327 Edward Fitzgerald Beale
 *   #16312 Henry M. Newhall    -> #283 Henry Mayo Newhall
 *
 * Both were called for in an earlier brief and neither was ever run; the check
 * here is what established that, not an assumption either way.
 *
 * THE DIRECTION IS THE AWKWARD ONE
 *
 * In both pairs the record that keeps the name is the empty one. #327 and #283
 * are the old hand-made records, carrying the full name and nothing else;
 * #15908 and #16312 came out of the review queue with every relation and every
 * alias. So the merge moves the substance onto the record with no substance,
 * which feels backwards and is right: the name policy makes the fullest form
 * the title, and the alternative is renaming a record and deleting the one
 * whose id the older material points at.
 *
 * Everything moves: article relations by field, aliases unioned, and the
 * source's own title added as an alias, because "Edward F. Beale" is the string
 * nine articles contain and a record that cannot be found under it has lost
 * what the rename was for.
 *
 * Nothing is deleted here. The sources are listed for deletion in the control
 * panel after the read-back, for the same reason the ranchos and the externals
 * were: the id is in the decisions file and the relations, and deletion is the
 * one step that cannot be undone.
 *
 * Dry run by default.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/merge_person_duplicates.php'))"
 */

$APPLY = false;

$MERGES = [
    ['from' => 15908, 'into' => 327, 'why' => 'one man; the fuller form is the title'],
    ['from' => 16312, 'into' => 283, 'why' => 'one man; the fuller form is the title'],
];

$ARTICLE_FIELDS = ['subjectPerson', 'writtenBy', 'editedBy', 'relatedPersons'];
$ALIAS = 'personAliases';

$split = fn($v) => array_values(array_filter(array_map('trim', preg_split('~[\r\n]+~', (string)$v))));

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

$plan = [];
foreach ($MERGES as $m) {
    $from = \craft\elements\Entry::find()->id($m['from'])->status(null)->one();
    $into = \craft\elements\Entry::find()->id($m['into'])->status(null)->one();
    if (!$from || !$into) {
        echo 'SKIPPED #' . $m['from'] . ' -> #' . $m['into'] . ': '
           . (!$from ? 'source' : 'target') . ' does not exist' . PHP_EOL;
        continue;
    }
    $moving = []; $n = 0;
    foreach ($ARTICLE_FIELDS as $fh) {
        $as = \craft\elements\Entry::find()->section('articles')
            ->relatedTo(['targetElement' => $from, 'field' => $fh])->status(null)->limit(null)->all();
        if ($as) { $moving[$fh] = $as; $n += count($as); }
    }
    $aliases = array_values(array_unique(array_merge(
        $split($into->getFieldValue($ALIAS)),
        $split($from->getFieldValue($ALIAS)),
        [(string)$from->title]
    )));
    $aliases = array_values(array_filter($aliases, fn($a) => $a !== (string)$into->title));
    $targetHas = 0;
    foreach ($ARTICLE_FIELDS as $fh) {
        $targetHas += (int)\craft\elements\Entry::find()->section('articles')
            ->relatedTo(['targetElement' => $into, 'field' => $fh])->status(null)->count();
    }
    $plan[] = compact('from', 'into', 'moving', 'n', 'aliases', 'targetHas') + ['why' => $m['why']];

    printf("#%-6d %-26s -> #%-6d %s\n", $from->id, $from->title, $into->id, $into->title);
    printf("   %-22s %s\n", 'target holds now', $targetHas . ' relations');
    foreach ($moving as $fh => $as) { printf("   %-22s %d move\n", $fh, count($as)); }
    printf("   %-22s %s\n", 'aliases after', $aliases ? implode(' | ', $aliases) : '(none)');
    printf("   %-22s #%d, after the read-back\n", 'delete in the CP', $from->id);
    echo PHP_EOL;
}

echo str_repeat('-', 78) . PHP_EOL;
echo 'merges: ' . count($plan) . '   relations moving: ' . array_sum(array_column($plan, 'n')) . PHP_EOL;

if (!$APPLY) { echo PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

$moved = 0; $aliasOk = 0;
foreach ($plan as $p) {
    foreach ($p['moving'] as $fh => $arts) {
        foreach ($arts as $a) {
            /* Repoint, not append: an article may already carry the target, and
               writing it in twice is a duplicate relation the control panel
               shows as two identical rows. */
            $ids = array_map(fn($r) => $r->id, $a->{$fh}->all());
            $ids = array_values(array_unique(array_map(
                fn($i) => $i === $p['from']->id ? $p['into']->id : $i, $ids)));
            $a->setFieldValue($fh, $ids);
            if (\Craft::$app->elements->saveElement($a)) { $moved++; }
            else { echo 'FAILED article #' . $a->id . ': ' . json_encode($a->getErrors()) . PHP_EOL; }
        }
    }
    $p['into']->setFieldValue($ALIAS, implode("\n", $p['aliases']));
    if (!\Craft::$app->elements->saveElement($p['into'])) {
        echo 'FAILED target #' . $p['into']->id . ': ' . json_encode($p['into']->getErrors()) . PHP_EOL;
    }
}

/* Read back from fresh queries. */
$stranded = 0; $landed = 0;
foreach ($plan as $p) {
    foreach ($ARTICLE_FIELDS as $fh) {
        $stranded += (int)\craft\elements\Entry::find()->section('articles')
            ->relatedTo(['targetElement' => $p['from'], 'field' => $fh])->status(null)->count();
        $landed += (int)\craft\elements\Entry::find()->section('articles')
            ->relatedTo(['targetElement' => $p['into'], 'field' => $fh])->status(null)->count();
    }
    $back = \craft\elements\Entry::find()->id($p['into']->id)->status(null)->one();
    if (in_array((string)$p['from']->title, $split($back->getFieldValue($ALIAS)), true)) { $aliasOk++; }
}
$want = array_sum(array_column($plan, 'n'));
$wantLanded = $want + array_sum(array_column($plan, 'targetHas'));

echo PHP_EOL . 'READ-BACK' . PHP_EOL;
printf("   %-26s %-18s %s\n", 'relations saved', $moved . ' of ' . $want, $moved === $want ? 'pass' : 'FAIL');
printf("   %-26s %-18s %s\n", 'on the targets now', $landed . ' of ' . $wantLanded, $landed === $wantLanded ? 'pass' : 'FAIL');
printf("   %-26s %-18s %s\n", 'left on the sources', (string)$stranded, $stranded === 0 ? 'pass' : 'FAIL');
printf("   %-26s %-18s %s\n", 'source name kept as alias', $aliasOk . ' of ' . count($plan), $aliasOk === count($plan) ? 'pass' : 'FAIL');

echo PHP_EOL . 'FOR DELETION IN THE CONTROL PANEL:' . PHP_EOL;
foreach ($plan as $p) { printf("   #%-7d %-28s merged into #%d\n", $p['from']->id, $p['from']->title, $p['into']->id); }

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('merge_person_duplicates.php', $moved,
    ($moved === $want && $stranded === 0 && $aliasOk === count($plan) ? 'verified: ' : 'FAILED: ')
        . $moved . ' of ' . $want . ' moved, ' . $stranded . ' stranded, aliases ' . $aliasOk . '/' . count($plan),
    'Beale #15908->#327, Newhall #16312->#283');
