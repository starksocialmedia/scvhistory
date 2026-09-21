/**
 * Sets collectionKind on the thirteen existing collections.
 *
 * The values are a judgement and the judgement is recorded here rather than in
 * a spreadsheet, because the next person to ask "why is Making Cents a column
 * and not a catalogue" deserves the reason and not the answer.
 *
 *   book       written to be read in order. Chapters, a first page. The only
 *              kind that offers Start reading.
 *   column     a run by one author over time. Chronological by year, no
 *              beginning, the author is the subject.
 *   catalogue  discrete numbered items: issues of a periodical.
 *   topic      pieces by different hands sharing a subject.
 *
 * MAKING CENTS IS A COLUMN, NOT A CATALOGUE, AND THAT MATTERS
 *
 * The brief had it as a catalogue with a coin entry type behind it: identifier,
 * denomination, mint, obverse and reverse. The data does not support that.
 * coins.json is 259 weekly newspaper pieces by Dr. Sol Taylor, each with a
 * byline and a date, titled things like "Sleuthing at Garage and Estate Sales",
 * "ANA Comes to L.A. In 2009" and "Q. David Bowers, America's No. 1
 * Numismatist". Only 35 of the 259 titles name a coin at all. There are no
 * identifiers, no denominations and no obverse images, because these are
 * columns about coins and not records of coins.
 *
 * So no coin entry type and no schema change. It is a column and it imports as
 * articles, like Worden.
 *
 * NEWSMAKER IS A TOPIC, on instruction. It is a recurring Signal slot, which
 * argues for column, but the authors differ piece to piece and a column is one
 * hand over time. Topic is the better fit and is what is set here.
 *
 * Idempotent: a collection that already carries a value is left alone, so a
 * hand correction in the control panel survives a re-run.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/set_collection_kinds.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$HANDLE = 'collectionKind';

$KINDS = [
    'history-of-the-santa-clarita-valley' => ['book',      '80 chapters in five parts, read in order'],
    'story-of-our-valley'                 => ['book',      'a 13-part series with an introduction'],
    'worden'                              => ['column',    '219 weekly pieces, one author, 1995 to 2009'],
    'coins'                               => ['column',    '259 weekly pieces by Sol Taylor. Columns about coins, not records of coins'],
    'boston'                              => ['column',    "John Boston's Signal run"],
    'manzer'                              => ['column',    "Darryl Manzer's Mentryville columns"],
    'otn-patti'                           => ['column',    'Patti Rasmussen, one author'],
    'otn-pauline'                         => ['column',    'Pauline Harte, one author'],
    'otn-rioux'                           => ['column',    "Richard 'Doc' Rioux, one author"],
    'otn-whyte'                           => ['column',    'Tim Whyte, one author'],
    'otn-gazette'                         => ['catalogue', 'thirty numbered issues of a periodical'],
    'iraq'                                => ['topic',     'sixty-four pieces by different hands on one subject'],
    'newsmaker'                           => ['topic',     'a recurring slot, but the authors differ piece to piece'],
];

$fs = Craft::$app->getFields();
$elements = Craft::$app->getElements();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;

$field = $fs->getFieldByHandle($HANDLE);
if (!$field) {
    echo PHP_EOL . $HANDLE . ' does not exist yet.' . PHP_EOL;
    echo 'Run scripts/import/add_collection_kind_field.php first.' . PHP_EOL;
    if ($APPLY) { return; }
}
echo str_repeat('=', 74) . PHP_EOL;

$plan = []; $already = 0; $missing = []; $noField = 0;
foreach ($KINDS as $slug => [$kind, $why]) {
    $e = \craft\elements\Entry::find()->section('collections')->slug($slug)->status(null)->one();
    if (!$e) { $missing[] = $slug; continue; }

    $has = false;
    foreach ($e->getFieldLayout()->getCustomFields() as $f) { if ($f->handle === $HANDLE) { $has = true; } }
    if (!$has) { $noField++; printf("%-38s %-10s (no %s on the layout yet)\n", $slug, $kind, $HANDLE); continue; }

    $current = trim((string)$e->getFieldValue($HANDLE));
    if ($current !== '') {
        printf("%-38s %-10s already set to %s, left alone\n", $slug, $kind, $current);
        $already++;
        continue;
    }
    printf("%-38s %-10s %s\n", $slug, $kind, $why);
    $plan[] = ['e' => $e, 'kind' => $kind];
}

echo str_repeat('-', 74) . PHP_EOL;
$tally = [];
foreach ($KINDS as [$k, $w]) { $tally[$k] = ($tally[$k] ?? 0) + 1; }
ksort($tally);
foreach ($tally as $k => $n) { echo '  ' . str_pad($k, 12) . $n . PHP_EOL; }
echo 'to set: ' . count($plan) . ', already set: ' . $already . PHP_EOL;
if ($noField) { echo 'without the field: ' . $noField . PHP_EOL; }
if ($missing) { echo 'not found: ' . implode(', ', $missing) . PHP_EOL; }

$saved = 0; $failed = [];
if ($APPLY && $plan) {
    foreach ($plan as $p) {
        $p['e']->setFieldValue($HANDLE, $p['kind']);
        if ($elements->saveElement($p['e'])) { $saved++; }
        else { $failed[] = $p['e']->slug . ': ' . json_encode($p['e']->getErrors()); }
    }
    echo PHP_EOL . 'saved: ' . $saved . PHP_EOL;
    foreach ($failed as $f) { echo '  FAILED ' . $f . PHP_EOL; }

    /* Read the writes back. A save that reports success and stores nothing is
       worse than one that fails, because the counter says the work is done. */
    $back = 0; $short = [];
    foreach ($plan as $p) {
        $fresh = \craft\elements\Entry::find()->id($p['e']->id)->status(null)->one();
        $got = $fresh ? trim((string)$fresh->getFieldValue($HANDLE)) : '';
        if ($got === $p['kind']) { $back++; }
        else { $short[] = $p['e']->slug . ': reads back as "' . $got . '", wanted "' . $p['kind'] . '"'; }
    }
    echo 'read back: ' . $back . ' of ' . count($plan) . PHP_EOL;
    if ($short) {
        echo PHP_EOL . 'THE WRITE DID NOT PERSIST' . PHP_EOL;
        foreach ($short as $m) { echo '  ' . $m . PHP_EOL; }
        echo 'Do not re-run until this is understood.' . PHP_EOL;
        return;
    }
    echo 'verified.' . PHP_EOL;
}

echo PHP_EOL . ($APPLY ? 'done' : 'nothing was written. Set $APPLY = true to apply.') . PHP_EOL;
