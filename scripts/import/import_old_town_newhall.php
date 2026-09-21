/**
 * The Old Town Newhall columns: 183 pages into five collections.
 *
 * Unlike Worden and Making Cents this is not one run. oldtownnewhall.json holds
 * 231 pages across five author directories plus 48 outside them, and each
 * author is already a collection record. The directory decides which:
 *
 *   /oldtownnewhall/gazette/   otn-gazette   37
 *   /oldtownnewhall/patti/     otn-patti     27
 *   /oldtownnewhall/pauline/   otn-pauline   38
 *   /oldtownnewhall/rioux/     otn-rioux     44
 *   /oldtownnewhall/whyte/     otn-whyte     37
 *
 * The other 48 are /news 26, /newhall 21 and a subscribe page. They belong to no
 * author and are left alone: an importer that guesses where a page goes is worse
 * than one that reports it does not know.
 *
 * Each directory is run through the shared engine separately, so the fidelity
 * figure is per collection and a bad one does not hide inside a good average.
 * That matters here more than on a single run: five collections mean five
 * chances for one set of pages to be shaped differently from the rest.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_old_town_newhall.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will create entries' . PHP_EOL; }

$DIRS = [
    ['dir' => 'gazette', 'collection' => 'otn-gazette', 'label' => 'Old Town Newhall Gazette'],
    ['dir' => 'patti',   'collection' => 'otn-patti',   'label' => 'Open Book, by Patti Rasmussen'],
    ['dir' => 'pauline', 'collection' => 'otn-pauline', 'label' => 'Pauline Harte'],
    ['dir' => 'rioux',   'collection' => 'otn-rioux',   'label' => "Richard 'Doc' Rioux At Large"],
    ['dir' => 'whyte',   'collection' => 'otn-whyte',   'label' => "Black 'N' Whyte, by Tim Whyte"],
];

$engine = \Craft::getAlias('@root') . '/scripts/import/_series_import.php';
$grand = ['pages' => 0, 'plan' => 0, 'src' => 0, 'lost' => 0, 'imgs' => 0, 'pairs' => 0];
$worst = [];

foreach ($DIRS as $d) {
    echo PHP_EOL . str_repeat('#', 74) . PHP_EOL;
    echo '# ' . $d['label'] . PHP_EOL;
    echo str_repeat('#', 74) . PHP_EOL;

    $SERIES = [
        'key'        => 'otn-' . $d['dir'],
        'label'      => $d['label'],
        'inventory'  => 'oldtownnewhall',
        'collection' => $d['collection'],
        'script'     => 'import_old_town_newhall.php',
        'pathFilter' => '#^/oldtownnewhall/' . $d['dir'] . '/#',
    ];
    require $engine;
}

echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
echo 'Five collections, each measured on its own. A fidelity figure below 97 on' . PHP_EOL;
echo 'any one of them stops that one without touching the others.' . PHP_EOL;
