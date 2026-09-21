/**
 * Adds "settlement" to placeType, and sets it on the towns outside the valley.
 *
 * The seven kinds assumed every place was in the Santa Clarita Valley, where a
 * named place is a canyon, a road, a ranch, a building, a park, a site or a
 * trail. The corpus also names towns: Tehachapi over the pass, Bakersfield at
 * the end of the Ridge Route, Fillmore and Piru down the river. None of those
 * is a site in any useful sense, and calling Bakersfield a site is the kind of
 * classification that makes a filter useless.
 *
 * GNIS calls them Populated Place, which is the same distinction drawn by
 * somebody with a national dataset, so the mapping follows it.
 *
 * Adds one option to the existing dropdown and leaves the other seven alone.
 * Dry run by default.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_place_type_settlement.php'))"
 */

$APPLY = false;

$HANDLE = 'placeType';
$NEW = ['label' => 'Town or settlement', 'value' => 'settlement', 'default' => false];

/* Named because the brief named them. Each is checked against the record set
   rather than assumed to exist. */
$DEFAULTS = ['Tehachapi', 'Bakersfield', 'Fillmore', 'Piru'];

$fs = Craft::$app->getFields();
$field = $fs->getFieldByHandle($HANDLE);

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 70) . PHP_EOL;

if ($field === null) { echo $HANDLE . ' does not exist. Run add_place_type_field.php first.' . PHP_EOL; return; }

$opts = $field->options ?? [];
$have = array_column($opts, 'value');
echo 'current options: ' . implode(', ', $have) . PHP_EOL;

if (in_array('settlement', $have, true)) {
    echo 'settlement is already an option, left alone' . PHP_EOL;
} else {
    echo 'would add option: settlement, "' . $NEW['label'] . '"' . PHP_EOL;
    if ($APPLY) {
        $opts[] = $NEW;
        $field->options = $opts;
        if (!$fs->saveField($field)) { echo 'FAILED: ' . implode('; ', $field->getFirstErrors()) . PHP_EOL; return; }
        echo 'option added' . PHP_EOL;
    }
}

echo PHP_EOL;
$hasField = function ($el, string $h): bool {
    $l = $el->getFieldLayout();
    if (!$l) { return false; }
    foreach ($l->getCustomFields() as $f) { if ($f->handle === $h) { return true; } }
    return false;
};

$plan = [];
foreach ($DEFAULTS as $title) {
    $e = \craft\elements\Entry::find()->section('places')->title($title)->status(null)->one();
    if (!$e) { printf("  %-16s no such place record; nothing to set\n", $title); continue; }
    if (!$hasField($e, $HANDLE)) { printf("  %-16s #%-6d no placeType field on the layout\n", $title, $e->id); continue; }
    $cur = $e->getFieldValue($HANDLE);
    $cur = ($cur && $cur->value) ? $cur->value : '';
    if ($cur === 'settlement') { printf("  %-16s #%-6d already settlement\n", $title, $e->id); continue; }
    printf("  %-16s #%-6d %s -> settlement\n", $title, $e->id, $cur ?: '(empty)');
    $plan[] = $e;
}

echo PHP_EOL . 'would set: ' . count($plan) . PHP_EOL;

if ($APPLY && $plan) {
    $ok = 0;
    foreach ($plan as $e) { $e->setFieldValue($HANDLE, 'settlement'); if (\Craft::$app->elements->saveElement($e)) { $ok++; } }
    $verified = 0;
    foreach ($plan as $e) {
        $c = \craft\elements\Entry::find()->id($e->id)->status(null)->one();
        $v = $c ? $c->getFieldValue($HANDLE) : null;
        if ($v && $v->value === 'settlement') { $verified++; }
    }
    echo 'set: ' . $ok . '  verified on read-back: ' . $verified . ' of ' . count($plan) . PHP_EOL;
    $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
    $applyLog('add_place_type_settlement.php', $ok, 'verified ' . $verified . ' of ' . count($plan), 'added the settlement option');
    echo PHP_EOL . 'config/project will be dirty. Commit it before deploying.' . PHP_EOL;
}

echo PHP_EOL . str_repeat('=', 70) . PHP_EOL;
echo $APPLY ? 'done' : 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
