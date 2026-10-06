/**
 * The 25 era decisions (Nathan, 2 October 2026: "accept all except two").
 * The recommendations in inventory/review/era-decisions-2026-10-02.md, with two
 * changed by Nathan on the rule that significance wins over years:
 *   Buck McKeon -> Cityhood Era: the city's first mayor is what makes him matter
 *     here; Congress is national and documented elsewhere.
 *   Laurene Weste -> Mall & Growth: she came on in 1998, and the development and
 *     open-space fights she is known for belong to that era.
 * Remi Nadeau keeps an editor note that his era is low confidence.
 * Fills empty eras only. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/apply_era_decisions.php'))"
 */

use craft\elements\{Entry, Category};

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$MG = 170; $CON = 171; $CITY = 168; $SILENT = 162; $POST = 166; $FRONT = 160;
$D = [21944 => ['Bob Kellar', $MG], 18791 => ['Buck McKeon', $CITY], 16380 => ['Cameron Smyth', $MG], 15808 => ['Carl Boyer', $CITY], 18726 => ['George Pederson', $CITY],
    25445 => ['Gloria Mercado-Fortine', $MG], 15737 => ['Jan Heidt', $CITY], 15874 => ['Jill Klajic', $CITY], 16140 => ['Jo Anne Darcy', $CITY], 25401 => ['Judy Umeck', $MG],
    25431 => ['Kerry Clegg', $MG], 15929 => ['Laurene Weste', $MG], 23087 => ['Laurie Ender', $CON], 25157 => ['Linda Storli', $CON], 23085 => ['Marsha McLean', $CON],
    25407 => ['Paul de la Cerda', $MG], 26597 => ['Paula Olivares', $MG], 18663 => ['Randy Wicks', $MG], 18869 => ['Remi Nadeau', $SILENT], 25433 => ['Rochelle Weinstein', $MG],
    343 => ['Rodolfo Acosta', $POST], 25405 => ['Rose Diaz', $MG], 25441 => ['Steven Sturgeon', $MG], 16356 => ['William S. Hart', $SILENT], 20226 => ['William Wirt Jenkins', $FRONT]];
$NADEAU_NOTE = ['heading' => 'Era', 'position' => 'bottom', 'note' => 'The Silent Film Era is a low-confidence placement: the archive holds no dated act of his, and his adult life (1892 to 1941) divides almost evenly between the Railroad and Oil and Silent Film eras.'];
$bad = []; $todo = [];
foreach ([$MG, $CON, $CITY, $SILENT, $POST, $FRONT] as $c) { if (!Category::find()->id($c)->group('historicalEra')->exists()) { $bad[] = "era #$c missing"; } }
foreach ($D as $id => [$name, $era]) {
    $p = Entry::find()->id($id)->status(null)->one();
    if (!$p || $p->title !== $name) { $bad[] = "#$id is not $name"; continue; }
    /* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
    $cur = $p->historicalEra->status(null)->ids();
    echo str_pad($name, 26) . ($cur === [$era] ? 'already ' : ($cur ? 'HAS AN ERA, left: ' : '-> ')) . Category::find()->id($era)->one()?->title . PHP_EOL;
    if (!$cur) { $todo[] = [$id, $era]; }
}
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$short = [];
foreach ($todo as [$id, $era]) {
    $p = Entry::find()->id($id)->status(null)->one(); $p->setFieldValue('historicalEra', [$era]);
    if ($id === 18869) {
        $notes = array_values(array_filter($p->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== ''));
        $p->setFieldValue('editorNotes', array_merge(array_map(fn($r) => ['heading' => $r['heading'] ?? '', 'position' => $r['position'] ?? 'bottom', 'note' => $r['note'] ?? ''], $notes), [$NADEAU_NOTE]));
    }
    if (!Craft::$app->getElements()->saveElement($p) || Entry::find()->id($id)->status(null)->one()->historicalEra->ids() !== [$era]) { $short[] = "#$id"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: ' . count($todo) . ' eras set') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('apply_era_decisions.php', count($todo), $short ? 'SHORT' : 'verified', 'the 25 era decisions; McKeon Cityhood, Weste Mall & Growth; Nadeau noted low confidence');
if ($short) { throw new \RuntimeException('apply_era_decisions: ' . implode(', ', $short)); }
