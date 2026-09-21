/**
 * Selections from Leon Worden: 219 pieces, 1995 to 2009, into the worden
 * collection.
 *
 * The heavy lifting is in _series_import.php, which the three series imports
 * share so the fidelity standard cannot drift between them. Read its header for
 * how structure is transcribed and why matching is on the legacy path.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_worden_columns.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will create entries' . PHP_EOL; }

$SERIES = [
    'key'        => 'worden',
    'label'      => 'Selections from Leon Worden',
    'inventory'  => 'worden',
    'collection' => 'worden',
    'script'     => 'import_worden_columns.php',
    'pathFilter' => '#^/scvhistory/signal/worden/#',
];

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . '  ' . $SERIES['label'] . PHP_EOL;
require \Craft::getAlias('@root') . '/scripts/import/_series_import.php';
