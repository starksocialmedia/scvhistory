/**
 * The duplicate person records, found by a scan of every person and casualty
 * record for the same name under a shorter form, an initial, an accent or an
 * alias (Nathan, 29 September 2026, after Grok's Beale dossier flagged #15908).
 *
 * Seven candidate pairs. Two are different people and are left alone: Cameron
 * and Clyde Smyth (son and father), Rudy and Rodolfo Acosta. The five strong
 * pairs, each read record by record:
 *
 *   #15908 Edward F. Beale   -> #327 Edward Fitzgerald Beale    empty, nothing points at it
 *   #15976 Kit Carson        -> #315 Christopher Houston Carson empty, nothing points at it
 *   #16312 Henry M. Newhall  -> #283 Henry Mayo Newhall         empty, nothing points at it
 *   #18737 James Marshall    -> #319 James Wilson Marshall      empty; its links move
 *   #18869 Remi Nadeau       NOT A DUPLICATE: the grandson
 *
 * James Marshall. Every record pointing at #18737 means the gold discoverer,
 * except Perkins' chapter 6, "Oil and Newhall" (#1430), whose James Marshall was
 * "working on the rig in 1868" near Lyon Station: another man, a name in a
 * directory, a passing mention that stays in the text. So the gold links move
 * to #319, #1430's link is dropped, and #18737 goes.
 *
 * Remi Nadeau. #18869 is the grandson of the freighter #339, the owner of the
 * Soledad Canyon ranch and deer park of the 1920s, the two men Reynolds is found
 * to conflate. The links are sorted by what each record says: the freighter's
 * Cerro Gordo teams and Lang station (#12558, #12460, #2091, #869) move to
 * #339; the deer park and the ranch (#3695, #2155) stay on #18869; #2085, which
 * names both, points at both. #18869 is kept, and its title waits on Nathan.
 *
 * Each retired record is soft-deleted (to the trash, restorable) only after
 * every relation it had has been moved or dropped and read back, and its URL
 * redirects to the surviving record (config/redirects.php, same commit).
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/merge_duplicate_persons.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

/* retire => [survivor, expected titles, per-source overrides: sourceId => 'drop' | 'both' | survivorId] */
$MERGES = [
    15908 => [327, 'Edward F. Beale', 'Edward Fitzgerald Beale', []],
    15976 => [315, 'Kit Carson', 'Christopher Houston Carson', []],
    16312 => [283, 'Henry M. Newhall', 'Henry Mayo Newhall', []],
    18737 => [319, 'James Marshall', 'James Wilson Marshall', [1430 => 'drop']],
];
$NADEAU_GRANDSON = 18869; $NADEAU = 339;
$NADEAU_MOVE = [12558, 12460, 2091, 869];
$NADEAU_BOTH = [2085];
$NADEAU_STAY = [3695, 2155];

$elements = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$inbound = fn($id) => (new \craft\db\Query())->select(['r.sourceId', 'f.handle'])->from(['r' => '{{%relations}}'])->innerJoin(['f' => '{{%fields}}'], 'f.id = r.fieldId')
    ->innerJoin(['el' => '{{%elements}}'], 'el.id = r.sourceId')->where(['r.targetId' => $id, 'el.revisionId' => null, 'el.draftId' => null, 'el.dateDeleted' => null])->all();

/* A relation move: in the source's field, replace $from with $to, or drop it, or add $to beside it. */
$repoint = function (int $src, string $field, int $from, ?int $to, bool $keepFrom) use ($get, $elements) {
    $s = $get($src);
    $ids = array_map('intval', $s->getFieldValue($field)->status(null)->ids());
    $new = [];
    foreach ($ids as $i) { if ($i === $from) { if ($keepFrom) { $new[] = $i; } if ($to) { $new[] = $to; } } else { $new[] = $i; } }
    $s->setFieldValue($field, array_values(array_unique($new)));
    return $elements->saveElement($s) ? '' : "#$src save failed";
};

$ops = []; $refused = []; $done = [];
foreach ($MERGES as $drop => [$keep, $dropTitle, $keepTitle, $over]) {
    $d = $get($drop); $k = $get($keep);
    if (!$d) { $done[] = "#$drop already gone"; continue; }
    if ($d->title !== $dropTitle || !$k || $k->title !== $keepTitle) { $refused[] = "#$drop or #$keep is not as seen"; continue; }
    $in = $inbound($drop);
    echo "#$drop $dropTitle -> #$keep $keepTitle: " . count($in) . ' inbound' . PHP_EOL;
    $moves = [];
    foreach ($in as $r) {
        $src = (int)$r['sourceId']; $s = $get($src);
        $act = $over[$src] ?? $keep;
        echo '   ' . ($act === 'drop' ? 'drop ' : 'move ') . str_pad('#' . $src, 7) . str_pad($r['handle'], 18) . mb_substr($s->title, 0, 60) . ($act === 'drop' ? '  (another James Marshall: a passing mention)' : '') . PHP_EOL;
        $moves[] = [$src, $r['handle'], $act === 'drop' ? null : $keep];
    }
    echo '   then #' . $drop . ' to the trash; /' . $d->uri . ' redirects to /' . $k->uri . PHP_EOL;
    $ops[] = ['drop' => $drop, 'keep' => $keep, 'moves' => $moves, 'label' => "$dropTitle into $keepTitle"];
}

echo PHP_EOL . "#$NADEAU_GRANDSON Remi Nadeau (the grandson) is kept; links sorted by which man the record means:" . PHP_EOL;
$g = $get($NADEAU_GRANDSON); $n = $get($NADEAU);
$nadeauMoves = [];
if (!$g || !$n || $g->title !== 'Remi Nadeau' || $n->title !== 'Remi Allen Nadeau') { $refused[] = 'the Nadeau records are not as seen'; }
else {
    foreach ($inbound($NADEAU_GRANDSON) as $r) {
        $src = (int)$r['sourceId']; $s = $get($src);
        if (in_array($src, $NADEAU_MOVE, true)) { $nadeauMoves[] = [$src, $r['handle'], false]; echo '   to #339 the freighter    #' . $src . ' ' . mb_substr($s->title, 0, 60) . PHP_EOL; }
        elseif (in_array($src, $NADEAU_BOTH, true)) { $nadeauMoves[] = [$src, $r['handle'], true]; echo '   to both                  #' . $src . ' ' . mb_substr($s->title, 0, 60) . PHP_EOL; }
        elseif (in_array($src, $NADEAU_STAY, true)) { echo '   stays, the grandson      #' . $src . ' ' . mb_substr($s->title, 0, 60) . PHP_EOL; }
        else { $refused[] = "Nadeau: #$src points at the grandson and was not read; sort it by hand"; }
    }
}
echo PHP_EOL . 'ALREADY DONE: ' . ($done ? implode('; ', $done) : 'none') . PHP_EOL . 'REFUSED: ' . ($refused ? implode('; ', $refused) : 'none') . PHP_EOL;
echo 'SUMMARY: ' . count($ops) . ' records retired, ' . count($nadeauMoves) . ' Nadeau links sorted. The Smyths and the Acostas are different people.' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($refused) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$short = [];
foreach ($nadeauMoves as [$src, $field, $both]) {
    if ($e = $repoint($src, $field, $NADEAU_GRANDSON, $NADEAU, $both)) { $short[] = $e; continue; }
    $ids = array_map('intval', $get($src)->getFieldValue($field)->status(null)->ids());
    if (!in_array($NADEAU, $ids, true) || ($both !== in_array($NADEAU_GRANDSON, $ids, true))) { $short[] = "Nadeau #$src did not sort"; }
}
foreach ($ops as $op) {
    $bad = [];
    foreach ($op['moves'] as [$src, $field, $to]) {
        if ($e = $repoint($src, $field, $op['drop'], $to, false)) { $bad[] = $e; }
    }
    /* Read back before anything is deleted: nothing may still point at the record, and each move must have landed. */
    if ((int)count($inbound($op['drop']))) { $bad[] = count($inbound($op['drop'])) . ' relation(s) still point at #' . $op['drop']; }
    foreach ($op['moves'] as [$src, $field, $to]) { if ($to && !in_array($to, array_map('intval', $get($src)->getFieldValue($field)->status(null)->ids()), true)) { $bad[] = "#$src does not point at #$to"; } }
    if ($bad) { $short[] = $op['label'] . ': ' . implode('; ', $bad) . ' (NOT deleted)'; continue; }
    if (!$elements->deleteElement($get($op['drop']))) { $short[] = $op['label'] . ': delete failed'; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(' | ', $short) : 'OK: ' . count($ops) . ' retired to the trash, links moved; Nadeau sorted') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('merge_duplicate_persons.php', count($ops) + count($nadeauMoves), $short ? 'SHORT: ' . implode(' | ', $short) : 'verified', 'Beale, Carson, Newhall, Marshall duplicates retired; Nadeau links sorted');
if ($short) { throw new \RuntimeException('merge_duplicate_persons: ' . implode(' | ', $short)); }
