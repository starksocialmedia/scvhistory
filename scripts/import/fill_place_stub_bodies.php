/**
 * Replaces a generated placeholder body on a place record with prose the corpus
 * supports.
 *
 * Twelve of the eighteen places carry a body of the form "X is a named place in
 * the Santa Clarita Valley historical archive." That is a placeholder written by
 * an importer, not a sentence anybody meant, and it is what a reader gets today
 * on Fort Tejon, Rancho Camulos and Vasquez Rocks.
 *
 * This fills them, one at a time, from sentences that exist in inventory/legacy.
 * Nothing is composed from outside the corpus and every entry names the articles
 * it draws on.
 *
 * It will only ever overwrite a body that still matches the placeholder exactly.
 * A body somebody has written, or already filled by an earlier run, is never
 * touched: the guard is the point of the script, not a precaution around it.
 *
 * Heritage Junction Historic Park is filled here. It is the fourth of the four
 * places named in the brief; the other three did not exist and were created by
 * add_missing_places.php, while this one existed with a placeholder. The
 * remaining eleven are listed at the end as the same job, not yet done.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fill_place_stub_bodies.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

/* The exact shape an importer left behind. Anything else is somebody's writing. */
$STUB = '~^.{0,60}\s+is a named place in the Santa Clarita Valley historical archive\.?$~u';

$FILL = [
    'heritage-junction' => [
        'body' => "A section of William S. Hart Park in Newhall where the Santa Clarita Valley "
            . "Historical Society has gathered buildings saved from demolition and moved to the "
            . "site. The Saugus Train Station stands there as a museum, joined over the years by "
            . "the Kingsburry House, moved in July 1990; a two-story Victorian dating from 1893 "
            . "that stood surrounded by the Magic Mountain parking lot until it was moved in the "
            . "same year; the house of constable Ed Pardee, who ran the livery stable; and a "
            . "structure bought by the Pacific Telephone Company in 1946, later used by the Santa "
            . "Clarita Valley Boys Club and the Newhall-Saugus-Valencia Chamber of Commerce, "
            . "moved again in 1992. The adobe bricks of Martha Mitchell's home in Soledad Canyon, "
            . "the valley's first school house, were moved here in 1986 and the school "
            . "reassembled. The Historical Society operates the park rent-free on Hart Park land. "
            . "It is open to visitors at weekends.",
        'from' => [
            'Jerry Reynolds, "71. Requiem": the Saugus Station, Martha Mitchell\'s adobe, '
                . '"Heritage Junction is open for visitors on weekends"',
            'Jerry Reynolds, "49. Reflections": the 1893 Victorian moved from the Magic Mountain '
                . 'parking lot in 1990',
            'Jerry Reynolds, "47. Dry Colony": the Pacific Telephone structure, 1946 and 1992',
            'Jerry Reynolds, "55. Fights and Feuds": Ed Pardee, constable and livery stable keeper',
            'Jerry Reynolds, "About the Namesakes of the Kingsburry House": the Kingsburry House, '
                . 'moved July 1990',
            'A.B. Perkins, "Tales of Lang and Soledad", Editor\'s Note: the adobe bricks moved in 1986',
            'Leon Worden, "A Third-Grade History Cheat Sheet": the Saugus Train Station Museum and '
                . '"buildings that were saved from the bulldozer"',
            'Leon Worden, "Preservation Group Hammers City": the Society operating the park rent-free '
                . 'on a section of Hart Park',
        ],
    ],
];

$elements = Craft::$app->getElements();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

$filled = 0; $blocked = 0;

foreach ($FILL as $slug => $spec) {
    $e = \craft\elements\Entry::find()->section('places')->slug($slug)->status(null)->one();
    if (!$e) { echo $slug . ': no place record' . PHP_EOL; continue; }

    $current = trim((string)$e->body);
    echo PHP_EOL . $e->title . '  (#' . $e->id . ')' . PHP_EOL;

    if (!preg_match($STUB, $current)) {
        echo '  BLOCKED: the body is not the placeholder any more, so it is left alone.' . PHP_EOL;
        echo '  it reads: "' . mb_substr($current, 0, 90) . '…"' . PHP_EOL;
        $blocked++;
        continue;
    }

    echo '  was:  "' . $current . '"' . PHP_EOL;
    echo '  now:  ' . mb_substr($spec['body'], 0, 104) . '…' . PHP_EOL;
    echo '  ' . strlen($spec['body']) . ' characters, drawn from:' . PHP_EOL;
    foreach ($spec['from'] as $f) { echo '      ' . $f . PHP_EOL; }
    $filled++;

    if ($APPLY) {
        $e->setFieldValue('body', $spec['body']);
        echo '  ' . ($elements->saveElement($e) ? 'saved' : 'SAVE FAILED: ' . json_encode($e->getErrors())) . PHP_EOL;
    }
}

/* ------------------------------------------------- the rest of the same job */

$remaining = [];
foreach (\craft\elements\Entry::find()->section('places')->status(null)->orderBy('title asc')->limit(null)->all() as $e) {
    if (isset($FILL[$e->slug])) { continue; }
    if (preg_match($STUB, trim((string)$e->body))) { $remaining[] = $e->title; }
}

echo PHP_EOL . str_repeat('=', 78) . PHP_EOL;
echo 'bodies that would be filled: ' . $filled . PHP_EOL;
echo 'blocked because somebody had written one: ' . $blocked . PHP_EOL;

if ($remaining) {
    echo PHP_EOL . 'STILL PLACEHOLDERS, ' . count($remaining) . '. Same job, not yet done.' . PHP_EOL;
    echo 'Each needs somebody to read the corpus passages and write the paragraph;' . PHP_EOL;
    echo 'a script cannot compose these, it can only refuse to invent them.' . PHP_EOL;
    foreach ($remaining as $r) { echo '  ' . $r . PHP_EOL; }
}

if (!$APPLY) { echo PHP_EOL . 'nothing written.' . PHP_EOL; }
