/**
 * Writes the six backfill proposals Nathan accepted.
 *
 * From propose_org_backfill.php, the ones that matched an authority outright at
 * 100 per cent. The two that did not are deliberately absent:
 *
 *   Rancho Camulos       matched "Rancho Camulos Museum" at 70 per cent. The
 *                        museum is a body that looks after the rancho; the
 *                        rancho is the place. Different things, so no EIN.
 *   Planning Commission  matched a government agency in Islamabad. It is the
 *                        City of Santa Clarita's commission, so it takes the
 *                        City as its parent and no identifier at all, because
 *                        no authority we hold carries it.
 *
 * Both of those are written here too, as the type and parent they should have
 * rather than the identifiers they should not.
 *
 * Idempotent: a field already holding a value is left alone and reported, so a
 * second run cannot overwrite something a person typed.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/apply_org_backfill.php'))"
 */

$APPLY = false;

$SET = [
    'Henry Mayo Newhall Memorial Hospital'     => ['ein' => '952821104', 'orgType' => 'nonprofit'],
    'Santa Clarita Valley Chamber of Commerce' => ['ein' => '951987319', 'orgType' => 'nonprofit'],
    'Santa Clarita Valley Historical Society'  => ['ein' => '953003205', 'orgType' => 'nonprofit'],
    'Newhall Land and Farming Company'         => ['wikidataId' => 'Q7018089', 'orgType' => 'business'],
    'Valencia High School'                     => ['wikidataId' => 'Q7910661', 'orgType' => 'school', 'schoolLevel' => 'high'],
    'Newhall Elementary School'                => ['ncesId' => '062718009956', 'orgType' => 'school', 'schoolLevel' => 'elementary'],
    /* Rejected for identifiers, kept for what they are. */
    'Planning Commission'                      => ['orgType' => 'government', 'parentOrganization' => 'The City of Santa Clarita'],
    'Rancho Camulos'                           => ['orgType' => 'other'],
];

$hasField = function ($el, string $h): bool {
    $l = $el->getFieldLayout();
    if (!$l) { return false; }
    foreach ($l->getCustomFields() as $f) { if ($f->handle === $h) { return true; } }
    return false;
};

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

/* The identifier fields have to exist. setFieldValue on a handle the layout
   does not carry writes nothing and reports success, which is how a backfill
   reports eight successes and changes nothing. */
$probe = \craft\elements\Entry::find()->section('organizations')->status(null)->one();
$missing = [];
foreach (['orgType', 'schoolLevel', 'cdsCode', 'ncesId', 'ein', 'wikidataId', 'parentOrganization'] as $h) {
    if (!$probe || !$hasField($probe, $h)) { $missing[] = $h; }
}
if ($missing) {
    echo 'MISSING FROM THE ORGANIZATION LAYOUT: ' . implode(', ', $missing) . PHP_EOL;
    echo 'Run add_org_taxonomy_fields.php and add_org_identifier_fields.php first.' . PHP_EOL;
    if ($APPLY) { echo 'refusing to apply.' . PHP_EOL; return; }
    echo PHP_EOL . 'The plan below is what would be written once they exist.' . PHP_EOL;
}
echo PHP_EOL;

$plan = []; $skipped = [];

foreach ($SET as $title => $vals) {
    $e = \craft\elements\Entry::find()->section('organizations')->title($title)->status(null)->one();
    if (!$e) { $skipped[] = $title . ': no such record'; continue; }

    $write = []; $held = [];
    foreach ($vals as $h => $v) {
        if (!$hasField($e, $h)) { $write[$h] = $v; continue; }   /* reported, blocked above */
        if ($h === 'parentOrganization') {
            $cur = $e->parentOrganization->one();
            if ($cur) { $held[] = $h . ' already ' . $cur->title; continue; }
            $t = \craft\elements\Entry::find()->section('organizations')->title($v)->status(null)->one();
            if (!$t) { $skipped[] = $title . ': parent "' . $v . '" is not a record'; continue; }
            $write[$h] = $t->id;
            continue;
        }
        $cur = trim((string)$e->getFieldValue($h));
        if ($cur !== '' && $cur !== $v) { $held[] = $h . ' already "' . $cur . '"'; continue; }
        if ($cur === $v) { $held[] = $h . ' already correct'; continue; }
        $write[$h] = $v;
    }

    printf("%-42s #%d\n", mb_substr($title, 0, 41), $e->id);
    foreach ($write as $h => $v) {
        printf("    %-20s %s\n", $h, $h === 'parentOrganization' ? ('#' . $v . ' ' . $SET[$title][$h]) : $v);
    }
    foreach ($held as $h) { printf("    %-20s %s\n", '(left alone)', $h); }
    echo PHP_EOL;

    if ($write) { $plan[] = ['entry' => $e, 'write' => $write]; }
}

echo str_repeat('-', 74) . PHP_EOL;
echo 'records to write: ' . count($plan) . PHP_EOL;
foreach ($skipped as $s) { echo '  skipped: ' . $s . PHP_EOL; }

if (!$APPLY) { echo PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

$ok = 0; $fields = 0;
foreach ($plan as $p) {
    $p['entry']->setFieldValues($p['write']);
    if (\Craft::$app->elements->saveElement($p['entry'])) { $ok++; $fields += count($p['write']); }
    else { echo 'FAILED ' . $p['entry']->title . ': ' . json_encode($p['entry']->getErrors()) . PHP_EOL; }
}

$verified = 0;
foreach ($plan as $p) {
    $e = \craft\elements\Entry::find()->id($p['entry']->id)->status(null)->one();
    if (!$e) { continue; }
    $good = true;
    foreach ($p['write'] as $h => $v) {
        if ($h === 'parentOrganization') { $good = $good && ($e->parentOrganization->one()?->id == $v); continue; }
        if (trim((string)$e->getFieldValue($h)) !== (string)$v) { $good = false; }
    }
    if ($good) { $verified++; }
}

echo PHP_EOL . 'saved: ' . $ok . '  fields written: ' . $fields
   . '  verified on read-back: ' . $verified . ' of ' . count($plan) . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('apply_org_backfill.php', $ok, 'verified ' . $verified . ' of ' . count($plan), $fields . ' fields');
