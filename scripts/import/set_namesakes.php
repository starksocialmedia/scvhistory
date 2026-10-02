/**
 * Namesakes, generally (Nathan, 1 October 2026: "things named after people
 * should link to them ... do it generally, not just for this pair"). Every place,
 * organization and community whose name contains the surname of a person in the
 * archive was listed (42 pairs, most of them things named for the town of
 * Newhall rather than the man), and the archive searched for a statement of the
 * naming. These are the pairs a source states; each namingNote quotes or cites
 * it. Pairs without a source are not set: the town of Newhall itself is one, its
 * naming for Henry Mayo Newhall not yet stated in any record the archive holds.
 * Pico Canyon and the Hart records were set by their own scripts.
 * Needs add_named_for_fields.php and add_named_for_to_communities.php.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/set_namesakes.php'))"
 */

use craft\elements\{Entry, Category};

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$ws = fn($s) => preg_replace('~\s+~u', ' ', (string)$s);
$body = fn($id) => $ws(strip_tags((string)Entry::find()->id($id)->status(null)->one()?->body));
$PAIRS = [
    ['entry', 15862, 'Wiley Canyon', 331, 'Henry Clay Wiley', 'A.B. Perkins names H.C. Wiley among the first to file oil claims in the hills in 1865 and says "their filings covered adjacent canyons in the Pico hills which still bear their respective names" ("History of Pico Canyon Oil Production," 1958).', [1440, ['H.C. Wiley, husband of Mrs. Gelcich\'s sister', 'Their filings covered adjacent canyons in the Pico hills which still bear their respective names']]],
    ['category', 744, 'Mentryville', 18648, 'Charles Alexander Mentry', 'Named in his honor in his lifetime: Demetrius Scofield\'s eulogy of 1900 speaks of "Mentryville, the little settlement of employees named in his honor."', [20104, ['At Mentryville, the little settlement of employees named in his honor']]],
    ['entry', 659, 'Vasquez Rocks', 285, 'Tiburcio Vasquez', 'The Union Oil Company postcard of 1941 says the rocks "are named for the notorious bandit who used this location as his hideout." It is the postcard\'s statement.', [2741, ['are named for the notorious bandit who used this location as his hideout']]],
    ['entry', 609, 'Lang Station', 18820, 'John Lang', 'John Lang arrived in 1873 and built a hotel at the original Lang Station in Soledad Canyon; three years later the Southern Pacific put its depot there (Leon Worden).', [4299, ['the original Lang Station in Soledad Canyon. That was the year John Lang arrived and built a hotel']]],
    ['entry', 932, 'Beale\'s Cut Stagecoach Pass', 327, 'Edward Fitzgerald Beale', 'The cut was completed by Edward F. Beale\'s hired hands to its full 90-foot depth in 1864 (Leon Worden, "La Puerta," 2023).', ['file', "$root/inventory/legacy/fetched/lapuerta2023.txt", ['Completed by Beale\'s hired hands to its ultimate 90-foot depth in 1864, Beale\'s Cut']]],
    ['entry', 599, 'Harry Carey Ranch', 15919, 'Harry Carey', 'His ranch: he acquired the homestead at the mouth of San Francisquito Canyon in 1916 and established a rancho there (Leon Worden, SCVHistory.com LW2271).', ['file', "$root/inventory/legacy/fetched/hart/lw2271.txt", ['Actor Harry Carey (Sr.) acquired a homestead at the mouth of San Francisquito Canyon in 1916 and established a rancho']]],
    ['entry', 380, 'Henry Mayo Newhall Memorial Hospital', 283, 'Henry Mayo Newhall', 'The hospital carries his name as its memorial.', [0, []]],
    ['entry', 15691, 'Newhall Land and Farming Company', 283, 'Henry Mayo Newhall', 'The company of his heirs: in 1883 they granted their holdings to the Newhall Land and Farming Company (Tom Sitton, survey for the Natural History Museum, 1989).', ['file', "$root/inventory/legacy/fetched/hart/mu8901.txt", ['in 1883, his heirs granted their holdings to the Newhall Land and Farming Company']]],
];
$bad = []; $todo = [];
foreach ($PAIRS as [$kind, $id, $title, $pid, $pname, $note, $ev]) {
    $el = $kind === 'entry' ? Entry::find()->id($id)->status(null)->one() : Category::find()->id($id)->one();
    $p = Entry::find()->id($pid)->status(null)->one();
    if (!$el || $el->title !== $title) { $bad[] = "#$id is not $title"; continue; }
    if (!$p || $p->title !== $pname) { $bad[] = "#$pid is not $pname"; continue; }
    if ($ev[0] === 'file') { $t = $ws(@file_get_contents($ev[1])); $phr = $ev[2]; } elseif ($ev[0]) { $t = $body($ev[0]); $phr = $ev[1]; } else { $t = ''; $phr = []; }
    foreach ($phr as $ph) { if (!str_contains($t, $ws($ph))) { $bad[] = "$title: the source does not read \"" . mb_substr($ph, 0, 50) . '"'; } }
    $done = in_array($pid, $el->namedFor->ids()) && trim((string)$el->namingNote) === $note;
    echo str_pad($title, 40) . ' <- ' . str_pad($pname, 26) . ($done ? 'already set' : 'set') . PHP_EOL;
    if (!$done) { $todo[] = [$el, $pid, $note]; }
}
if (preg_match('~\x{2014}~u', implode('', array_column($PAIRS, 5)))) { $bad[] = 'an em dash in a note'; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$short = [];
foreach ($todo as [$el, $pid, $note]) {
    $el->setFieldValues(['namedFor' => [$pid], 'namingNote' => $note]);
    if (!Craft::$app->getElements()->saveElement($el)) { $short[] = $el->title; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: ' . count($todo) . ' namesakes set') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('set_namesakes.php', count($todo), $short ? 'SHORT' : 'verified', 'namesakes with a stated source: Wiley Canyon, Mentryville, Vasquez Rocks, Lang Station, Beale\'s Cut, Carey Ranch, the hospital, Newhall Land');
if ($short) { throw new \RuntimeException('set_namesakes: ' . implode(', ', $short)); }
