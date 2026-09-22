/**
 * Quality pass 5 of 5: missing images.
 *
 * A piece is missing images when its legacy page carried more content pictures
 * than the record holds (the quality report's definition; logos, buttons and
 * pictures on five or more pages don't count). This attaches, to recordImages,
 * every one of those pictures that is already an asset in the volume, matched
 * by filename, in the order the legacy page had them, after whatever the
 * record already holds. Nothing already attached moves.
 *
 * It does not fetch anything. Bringing files in is import_mirror_images.php,
 * run first with $MIRROR_ONLY = 'series'. On 22 September that pass found 305
 * files named in the images-wanted lists: 29 already held, 18 held in a smaller
 * copy that it replaces in place (same asset, so nothing here changes), none
 * new, and 258 not on the mirror at all. So this pass is the one that moves
 * the count, and what it can't reach is listed for a person with the missing
 * filenames: those pictures are not in the archive and not on Reggie.
 *
 * Filenames are unique across the volume (checked: 4,312 assets, no two
 * sharing a name), so a filename match is one asset. The script checks that
 * again on every run and stops if it stops being true.
 *
 * Dry run by default, with before and after counts from the quality report.
 * Run:   ddev craft exec "eval(file_get_contents('scripts/import/quality_images.php'))"
 * Apply: ddev craft exec '$QUALITY_APPLY = true; eval(file_get_contents("scripts/import/quality_images.php"));'
 */

$APPLY = false;
if (!empty($QUALITY_APPLY)) { $APPLY = true; echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$run = require \Craft::getAlias('@root') . '/scripts/import/_quality_pass.php';

$byName = [];
foreach ((new \craft\db\Query())->select(['id', 'filename'])->from('{{%assets}}')->all() as $a) {
    $k = strtolower($a['filename']);
    if (isset($byName[$k])) { echo 'STOP: two assets named ' . $k . ' (#' . $byName[$k] . ', #' . $a['id'] . '); a filename match is no longer exact' . PHP_EOL; return; }
    $byName[$k] = (int)$a['id'];
}
echo 'assets by filename: ' . count($byName) . PHP_EOL;

$legacy = new \modules\quality\QualityReport();

$run([
    'script' => 'quality_images.php',
    'label' => 'Missing images',
    'apply' => $APPLY,
    'classes' => ['imgMissing'],
    'propose' => function (\craft\elements\Entry $e, int $cid, array &$listed) use ($byName, $legacy): ?array {
        $want = $legacy->contentImagesFor($e);
        if (!$want || !$e->getFieldLayout()?->getFieldByHandle('recordImages')) { return null; }
        $have = $e->recordImages->status(null)->ids();
        $featured = $e->getFieldLayout()->getFieldByHandle('featuredImage') ? $e->featuredImage->status(null)->ids() : [];
        $held = array_unique(array_merge($have, $featured));
        if (count($held) >= count($want)) { return null; }

        $add = []; $absent = []; $show = [];
        foreach ($want as $fn) {
            $id = $byName[$fn] ?? null;
            if ($id === null) { $absent[] = $fn; continue; }
            if (in_array($id, $held, true) || in_array($id, $add, true)) { continue; }
            $add[] = $id; $show[] = '+ recordImages #' . $id . ' ' . $fn;
        }
        if ($absent) {
            $still = count($want) - count($held) - count($add);
            if ($still > 0) {
                $listed[] = count($absent) . ' of ' . count($want) . ' legacy pictures not in the archive: ' . implode(', ', array_slice($absent, 0, 6)) . (count($absent) > 6 ? ', ...' : '');
            }
        }
        if (!$add) { return null; }
        return ['set' => ['recordImages' => array_merge($have, $add)], 'show' => $show];
    },
]);
