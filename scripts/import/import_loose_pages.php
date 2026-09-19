/**
 * Imports inventory/legacy/loose-pages.json: two legacy pages that belong to a
 * place rather than to a series, and so were never picked up by the Perkins,
 * Reynolds, Worden or war memorial passes.
 *
 *   margaretroutledge              The Mysterious Gunshot Death of Margaret
 *                                  Routledge, 1916. Filed on the legacy site
 *                                  under Newhall Ranch House, and the source of
 *                                  the 2014 Ghost Adventures episode that made
 *                                  the house's haunting claim public.
 *   scvhs_ruizcemeterycensus1992   The 1992 Eagle Scout census of Ruiz
 *                                  Cemetery: recorded tombstone information and
 *                                  grave locations, by Kyle Fotheringham for
 *                                  the SCV Historical Society.
 *
 * Both become articles, related to their place through depictsPlace, with the
 * body taken from the extraction and the legacy path and key recorded. Neither
 * body is edited here beyond trimming the navigation chrome that every legacy
 * page carries; clean_legacy_bodies.php is the tool for the rest.
 *
 * It also extends the Newhall Ranch House hauntedSource so the claim cites both
 * sources rather than one. Reynolds records the Blue Lady as a belief; the
 * Routledge page supplies the death the belief attached itself to, and the
 * television episode that carried it. That is a citation change, not a change
 * to the claim: hauntedStatus stays "legend".
 *
 * Idempotent. An article already present by legacyKey is reported and left
 * alone, a relation already set is not set again, and the hauntedSource is
 * written only if it still reads exactly as this script's predecessor left it.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_loose_pages.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

/* Point this at a copy to rehearse the run without the file being on the
   branch. Empty means the real one. */
$SOURCE = '';

$root = \Craft::getAlias('@root');
$path = $SOURCE !== '' ? $root . '/' . ltrim($SOURCE, '/') : $root . '/inventory/legacy/loose-pages.json';

if (!file_exists($path)) {
    echo 'not found: inventory/legacy/loose-pages.json' . PHP_EOL;
    echo 'It is on origin/grok-bot, not on this branch. Bring it across with:' . PHP_EOL;
    echo '  git checkout origin/grok-bot -- inventory/legacy/loose-pages.json' . PHP_EOL;
    return;
}
$data = json_decode(file_get_contents($path), true);
if (!is_array($data)) { echo 'could not parse loose-pages.json' . PHP_EOL; return; }

/* legacyKey => [the place slug it belongs to, the title to give the article] */
$PLACE_OF = [
    'margaretroutledge' => ['newhall-ranch-house',
        'The Mysterious Gunshot Death of Margaret Routledge, 1916'],
    'scvhs_ruizcemeterycensus1992' => ['ruiz-cemetery',
        'Ruiz Cemetery Census, 1992'],
];

$LEGACY_HOST = rtrim((string)(Craft::$app->getConfig()->getCustom()->legacyHost ?? 'https://scvhistory.com'), '/');

$elements = Craft::$app->getElements();
$svc = Craft::$app->getEntries();
$section = $svc->getSectionByHandle('articles');
$type = $svc->getEntryTypeByHandle('article');
if (!$section || !$type) { echo 'articles section or article entry type not found' . PHP_EOL; return; }

$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

/* The navigation crumbs every legacy page opens with: "> HERITAGE JUNCTION". */
$trimChrome = function (string $body): array {
    $lines = preg_split('~\R~u', trim($body));
    $cut = 0;
    foreach ($lines as $i => $l) {
        $t = trim($l);
        if ($t === '' || preg_match('~^>\s*\S~u', $t)) { $cut = $i + 1; continue; }
        break;
    }
    return [implode("\n", array_slice($lines, $cut)), $cut];
};

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo 'source: inventory/legacy/loose-pages.json, ' . count($data['pages'] ?? []) . ' pages' . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

$created = 0; $skipped = 0; $related = 0;

foreach (($data['pages'] ?? []) as $page) {
    $key = (string)($page['legacy_key'] ?? '');
    if (!isset($PLACE_OF[$key])) {
        echo PHP_EOL . 'no place mapped for legacy key "' . $key . '", skipped' . PHP_EOL;
        continue;
    }
    [$placeSlug, $title] = $PLACE_OF[$key];

    $place = \craft\elements\Entry::find()->section('places')->slug($placeSlug)->status(null)->one();
    $existing = \craft\elements\Entry::find()->section('articles')->status(null)
        ->where(['like', 'content', $key])->one();
    if (!$existing) {
        foreach (\craft\elements\Entry::find()->section('articles')->status(null)->limit(null)->all() as $a) {
            if ($hasField($a, 'legacyKey') && trim((string)$a->legacyKey) === $key) { $existing = $a; break; }
        }
    }

    echo PHP_EOL . str_repeat('-', 78) . PHP_EOL;
    echo $title . PHP_EOL;
    echo '  legacyKey   ' . $key . PHP_EOL;
    echo '  legacyUrl   ' . ($page['legacy_path'] ?? '') . PHP_EOL;
    echo '  place       ' . ($place ? $place->title . ' (#' . $place->id . ')' : '*** ' . $placeSlug . ' NOT FOUND ***') . PHP_EOL;

    if ($existing) {
        echo '  already imported as #' . $existing->id . ' "' . $existing->title . '", left alone' . PHP_EOL;
        $skipped++;
        continue;
    }

    [$body, $cut] = $trimChrome((string)($page['body_text'] ?? ''));
    echo '  body        ' . strlen($body) . ' chars, after trimming ' . $cut . ' lines of navigation' . PHP_EOL;
    echo '  opens       ' . mb_substr(trim(preg_replace('~\s+~u', ' ', $body)), 0, 96) . '…' . PHP_EOL;
    echo '  date        ' . trim(preg_replace('~^[A-Za-z0-9.\- ]*\|\s*~u', '', trim((string)($page['date_raw'] ?? ''))))
        . '   (from "' . ($page['date_raw'] ?? '') . '")' . PHP_EOL;
    echo '  images      ' . count($page['images'] ?? []) . ' on the legacy page, not imported here' . PHP_EOL;

    $created++;
    if (!$APPLY) { continue; }

    $e = new \craft\elements\Entry();
    $e->sectionId = $section->id;
    $e->typeId = $type->id;
    $e->title = $title;
    $e->enabled = true;

    $values = [
        'body' => $body,
        'legacyKey' => $key,
        'legacyUrl' => (string)($page['legacy_path'] ?? ''),
        'sourcePath' => (string)($page['source_url'] ?? ''),
    ];
    /* date_raw carries the masthead as well as the date on these pages:
       "SCVHistory.com | March 23, 2014". Keep the date. */
    $date = trim(preg_replace('~^[A-Za-z0-9.\- ]*\|\s*~u', '', trim((string)($page['date_raw'] ?? ''))));
    if ($date !== '') { $values['originalPublishDate'] = $date; }
    if ($place) { $values['depictsPlace'] = [$place->id]; }

    foreach ($values as $h => $v) {
        if (!$hasField($e, $h)) { continue; }
        try { $e->setFieldValue($h, $v); }
        catch (\Throwable $ex) { echo '  set ' . $h . ' failed: ' . $ex->getMessage() . PHP_EOL; }
    }

    if ($elements->saveElement($e)) {
        echo '  created #' . $e->id . ($place ? ', related to ' . $place->title : '') . PHP_EOL;
        if ($place) { $related++; }
    } else {
        echo '  SAVE FAILED: ' . json_encode($e->getErrors()) . PHP_EOL;
    }
}

/* -------------------------------------------- the haunted source citation */

echo PHP_EOL . str_repeat('=', 78) . PHP_EOL;

$WAS = 'Jerry Reynolds, "49. Reflections", History of the Santa Clarita Valley: '
     . '"The Newhall Ranch House, as it is known, is believed to be haunted by a '
     . 'mysterious \'Blue Lady\' and other beings."';
$NOW = 'Jerry Reynolds, "49. Reflections", History of the Santa Clarita Valley: '
     . '"The Newhall Ranch House, as it is known, is believed to be haunted by a '
     . 'mysterious \'Blue Lady\' and other beings." The claim attached itself to the '
     . 'death of Margaret Routledge, wife of the ranch manager Stanley Routledge, who '
     . 'died of a rifle wound in the house on January 24, 1916, the inquest unable to '
     . 'decide between suicide, accident and murder; SCVHistory.com set that record out '
     . 'on March 23, 2014, from the marriage licence, death certificate and newspaper '
     . 'report gathered by Pat Saletore and Lauren Parker. A March 2014 episode of the '
     . 'Travel Channel series "Ghost Adventures", filmed at Heritage Junction, connected '
     . 'the death to noises recorded in the upstairs rooms.';

$nrh = \craft\elements\Entry::find()->section('places')->slug('newhall-ranch-house')->status(null)->one();
if (!$nrh) {
    echo 'newhall-ranch-house not found; hauntedSource not touched' . PHP_EOL;
} elseif (!$hasField($nrh, 'hauntedSource')) {
    echo 'no hauntedSource field on the place type' . PHP_EOL;
} else {
    $current = trim((string)$nrh->hauntedSource);
    echo 'Newhall Ranch House hauntedSource' . PHP_EOL;
    if ($current === $NOW) {
        echo '  already cites both sources' . PHP_EOL;
    } elseif ($current !== $WAS) {
        echo '  SKIP: it does not read as this script expects, so it is left alone.' . PHP_EOL;
        echo '  it holds: "' . mb_substr($current, 0, 96) . '…"' . PHP_EOL;
    } else {
        echo '  would extend it to cite Routledge alongside Reynolds:' . PHP_EOL;
        echo '  ' . mb_substr($NOW, 0, 200) . '…' . PHP_EOL;
        echo '  (hauntedStatus stays "legend"; this changes the citation, not the claim)' . PHP_EOL;
        if ($APPLY) {
            $nrh->setFieldValue('hauntedSource', $NOW);
            echo '  ' . ($elements->saveElement($nrh) ? 'saved' : 'SAVE FAILED: ' . json_encode($nrh->getErrors())) . PHP_EOL;
        }
    }
}

echo PHP_EOL . str_repeat('=', 78) . PHP_EOL;
echo 'articles that would be created: ' . $created . PHP_EOL;
echo 'already imported, left alone:   ' . $skipped . PHP_EOL;
if (!$APPLY) { echo PHP_EOL . 'nothing written.' . PHP_EOL; }
