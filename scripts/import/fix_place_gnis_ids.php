/**
 * Points gnisId at the feature the record actually describes.
 *
 * Two places carry a GNIS id that is not in the California gazetteer. Both
 * numbers came from Wikidata P590, and both name the park rather than the thing
 * the record is about:
 *
 *   Vasquez Rocks   1665736   the park. The rocks are 1661629, class Summit.
 *   Fort Tejon      271183    the park. The fort is 1656588, "Old Fort Tejon
 *                             (historical)", class Military, Kern County.
 *
 * gnisId holds the id of the feature the record describes, so both are
 * replaced. The retired number is not thrown away: it goes into
 * recordProvenance as "GNIS park id (retired) NNNN", because somebody comparing
 * this archive against Wikidata later will find the old number there and needs
 * to be able to see that it was considered and replaced rather than never
 * known.
 *
 * "Fort Tejon Siphon" (271182) is a canal and is not the fort. The neighbouring
 * number is a coincidence and is recorded here so nobody has to rediscover it.
 *
 * Every replacement is checked against inventory/legacy/gnis-classes.json
 * before it is written. An id that is not in the gazetteer is exactly the fault
 * being fixed, and the script must not be able to introduce another one.
 *
 * Idempotent: a record already carrying the new id is left alone, and the
 * provenance note is appended once.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_place_gnis_ids.php'))"
 */

$APPLY = false;

$PROV_FIELD = 'recordProvenance';

/* title => [ the id to retire, the id it should carry ] */
$FIXES = [
    'Vasquez Rocks' => ['1665736', '1661629'],
    'Fort Tejon'    => ['271183',  '1656588'],
];

$classPath = \Craft::getAlias('@root') . '/inventory/legacy/gnis-classes.json';
$gnis = file_exists($classPath) ? (json_decode(file_get_contents($classPath), true) ?: []) : [];
$classes = $gnis['classes'] ?? [];

if (!$classes) {
    echo 'no gnis-classes.json. Run derive_gnis_classes.py first.' . PHP_EOL;
    return;
}

$hasField = function ($el, string $h): bool {
    $l = $el->getFieldLayout();
    if (!$l) { return false; }
    foreach ($l->getCustomFields() as $f) { if ($f->handle === $h) { return true; } }
    return false;
};

echo ($APPLY ? 'APPLYING, this writes to the database' : 'DRY RUN') . PHP_EOL;
echo 'gazetteer: ' . number_format(count($classes)) . ' California features, downloaded '
   . ($gnis['provenance']['downloaded'] ?? '?') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

$plan = []; $problems = [];

foreach ($FIXES as $title => $pair) {
    [$old, $new] = $pair;

    $e = \craft\elements\Entry::find()->section('places')->title($title)->status(null)->one();
    if (!$e) { $problems[] = $title . ': no such place'; continue; }
    if (!$hasField($e, 'gnisId')) { $problems[] = $title . ': no gnisId field'; continue; }

    /* The replacement has to exist in the gazetteer. This is the whole point. */
    $row = $classes[$new] ?? null;
    if ($row === null) {
        $problems[] = $title . ': the replacement id ' . $new . ' is not in the gazetteer either';
        continue;
    }

    $cur = trim((string)$e->getFieldValue('gnisId'));
    if ($cur === $new) { echo str_pad($title, 18) . 'already carries ' . $new . ', left alone' . PHP_EOL; continue; }
    if ($cur !== $old) {
        $problems[] = $title . ': holds ' . ($cur === '' ? '(empty)' : $cur) . ', expected ' . $old
                    . '. Not touching a value that is not the one this script was written for.';
        continue;
    }

    $note = 'GNIS park id (retired) ' . $old;
    $prov = $hasField($e, $PROV_FIELD) ? trim((string)$e->getFieldValue($PROV_FIELD)) : null;
    if ($prov === null) { $problems[] = $title . ': no ' . $PROV_FIELD . ' field to record the retired id in'; continue; }

    $already = str_contains($prov, $note);
    $newProv = $already ? $prov : trim($prov === '' ? $note : $prov . '; ' . $note);

    $plan[] = ['entry' => $e, 'old' => $old, 'new' => $new, 'row' => $row,
               'prov' => $newProv, 'provWas' => $prov];
}

foreach ($plan as $p) {
    printf("%-16s %s -> %s  \"%s\", class %s, %s County\n",
        $p['entry']->title, $p['old'], $p['new'],
        $p['row']['name'], $p['row']['class'], $p['row']['county']);
    printf("%-16s %s = \"%s\"\n", '', $PROV_FIELD, $p['prov']);
}

if ($problems) {
    echo PHP_EOL . 'PROBLEMS:' . PHP_EOL;
    foreach ($problems as $x) { echo '  ' . $x . PHP_EOL; }
}

if (!$plan) { echo PHP_EOL . 'nothing to do.' . PHP_EOL; return; }

if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
    echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}

$ok = 0;
foreach ($plan as $p) {
    $e = $p['entry'];
    $e->setFieldValues(['gnisId' => $p['new'], $PROV_FIELD => $p['prov']]);
    if (\Craft::$app->elements->saveElement($e)) { $ok++; }
    else { echo 'FAILED ' . $e->title . ': ' . json_encode($e->getErrors()) . PHP_EOL; }
}

/* Read back. A save that reports success and stores nothing has happened on
   this project more than once. */
$verified = 0;
foreach ($plan as $p) {
    $e = \craft\elements\Entry::find()->id($p['entry']->id)->status(null)->one();
    if (!$e) { continue; }
    $g = trim((string)$e->getFieldValue('gnisId'));
    $v = trim((string)$e->getFieldValue($PROV_FIELD));
    if ($g === $p['new'] && str_contains($v, 'GNIS park id (retired) ' . $p['old'])) { $verified++; }
    printf("  %-16s gnisId=%-10s %s=\"%s\"\n", $e->title, $g, $PROV_FIELD, $v);
}

echo PHP_EOL . 'saved: ' . $ok . '  verified on read-back: ' . $verified . ' of ' . count($plan) . PHP_EOL;
