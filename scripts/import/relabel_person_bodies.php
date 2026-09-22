/**
 * Corrects two of the 33 body labels, with the measurement that justifies each.
 *
 * classify_person_bodies.php labelled all 33 wordpress-import-unsourced, which
 * was right about where the text arrived from and wrong about who wrote two of
 * them. Measured on 22 September by six-word-run overlap:
 *
 *   #283 Henry Mayo Newhall   93% of the body appears verbatim on the legacy
 *                             page /scvhistory/ap1335a.htm. It is the legacy
 *                             site's own text, carried through WordPress.
 *                             -> legacy-leon
 *   #281 Jerry Reynolds       22% from /scvhistory/lw2184.htm and 30% from
 *                             article #817, "Preface" (1998) by Leon Worden,
 *                             which the archive holds. Part legacy, part not.
 *                             -> mixed
 *
 * The other two of the four local subjects stay as they are: Perkins #333 is
 * 9% from its legacy page and matches nothing else in the archive or the
 * crawl, and Worden #279 has no legacy page and matches nothing at all.
 *
 * WHERE THE NOTE GOES. On #281 the overlap is worth keeping, because a profile
 * should cite #817 rather than restate it. It goes in recordProvenance, which
 * nothing on the site renders. Not editorNotes: those render as boxes on the
 * page, visibly the archive speaking, which is not what a note about text
 * provenance is for.
 *
 * Idempotent: each record is written only if it does not already read the way
 * this script wants it.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/relabel_person_bodies.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$PLAN = [
    283 => ['label' => 'legacy-leon', 'note' => '',
            'why'  => '93% of the body appears verbatim on the legacy page /scvhistory/ap1335a.htm'],
    281 => ['label' => 'mixed',
            'note' => 'body overlap measured 2026-09-22: 30% of its six-word runs are article #817, "Preface" (1998) by Leon Worden, which the archive holds; 22% are the legacy page /scvhistory/lw2184.htm. A profile cites #817 rather than restating it.',
            'why'  => '30% from held article #817 and 22% from the legacy page: part legacy, part not'],
];

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

$plan = [];
foreach ($PLAN as $id => $want) {
    $e = \craft\elements\Entry::find()->section('persons')->id($id)->status(null)->one();
    if (!$e) { echo '#' . $id . ' NOT FOUND' . PHP_EOL; continue; }
    $was = (string)$e->getFieldValue('bodyAuthorship');
    $prov = trim((string)$e->getFieldValue('recordProvenance'));
    $needLabel = $was !== $want['label'];
    $needNote = $want['note'] !== '' && !str_contains($prov, 'article #817');

    echo PHP_EOL . '#' . $id . '  ' . $e->title . PHP_EOL;
    echo '   label        ' . ($was === '' ? '(empty)' : $was) . ' -> ' . $want['label'] . ($needLabel ? '' : '   (already)') . PHP_EOL;
    echo '   because      ' . $want['why'] . PHP_EOL;
    if ($want['note'] !== '') {
        echo '   provenance   ' . ($prov === '' ? '(empty)' : $prov) . PHP_EOL;
        echo '   would add    ' . $want['note'] . ($needNote ? '' : '   (already)') . PHP_EOL;
    }
    if ($needLabel || $needNote) { $plan[] = ['e' => $e, 'want' => $want, 'label' => $needLabel, 'note' => $needNote, 'prov' => $prov]; }
}

echo PHP_EOL . 'records to write: ' . count($plan) . PHP_EOL;
if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}

$saved = 0; $failed = [];
foreach ($plan as $p) {
    $e = $p['e'];
    if ($p['label']) { $e->setFieldValue('bodyAuthorship', $p['want']['label']); }
    if ($p['note']) {
        $e->setFieldValue('recordProvenance', trim(($p['prov'] === '' ? '' : $p['prov'] . ' | ') . $p['want']['note']));
    }
    if (Craft::$app->getElements()->saveElement($e)) { $saved++; }
    else { $failed[] = '#' . $e->id . ': ' . json_encode($e->getFirstErrors()); }
}
echo 'saved ' . $saved . ' of ' . count($plan) . PHP_EOL;
foreach ($failed as $f) { echo 'FAILED ' . $f . PHP_EOL; }

$ok = 0; $short = [];
foreach ($plan as $p) {
    $f = \craft\elements\Entry::find()->id($p['e']->id)->status(null)->one();
    $label = (string)$f->getFieldValue('bodyAuthorship');
    $prov = (string)$f->getFieldValue('recordProvenance');
    $bodyKept = trim((string)$f->getFieldValue('body')) !== '';
    if ($label !== $p['want']['label']) { $short[] = '#' . $f->id . ' label reads "' . $label . '"'; continue; }
    if ($p['want']['note'] !== '' && !str_contains($prov, '#817')) { $short[] = '#' . $f->id . ' provenance note did not land'; continue; }
    if (!$bodyKept) { $short[] = '#' . $f->id . ' the body is gone; this script only relabels'; continue; }
    $ok++;
}
$readback = ($short ? 'SHORT ' : 'verified ') . $ok . ' of ' . count($plan);
echo 'READ-BACK ' . $readback . PHP_EOL;
foreach ($short as $m) { echo '  ' . $m . PHP_EOL; }

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('relabel_person_bodies.php', $saved, $readback, '#283 -> legacy-leon, #281 -> mixed with the #817 overlap recorded');
if ($short || $failed) { throw new \RuntimeException('relabel_person_bodies: the write did not land as planned.'); }
