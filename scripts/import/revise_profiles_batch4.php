/**
 * Recasts four of batch 2's profiles to lead with the valley (Nathan,
 * 3 October 2026: "a person belongs in this archive for what they did here,
 * and a profile leads with that. Where the archive holds nothing local, the
 * profile says so in a line rather than borrowing a career from elsewhere").
 *
 *   Kit Carson (#315)  cut to three sentences: who he was, that he guided
 *                      Frémont, and that nothing places him in this valley.
 *   Serra (#299)       cut to Leon Worden's "no reason to believe Serra ever
 *                      set foot" here, and the Lasuén link through Mission San
 *                      Fernando.
 *   Frémont (#307)     leads with the 1847 march, the del Valle ranch, the
 *                      Newhall Pass camp and the pass named for him; the
 *                      national career is one sentence, and the page's own
 *                      Wikipedia link (personWikipediaUrl) is the link out.
 *   Crespí (#297)      leads with his diary as the first written account of
 *                      the valley and its people.
 * Every phrase relied on is checked against the fetched page or archive
 * record, as in build_profiles_batch4.php. A body is replaced only if it is
 * batch 2's text (bodyAuthorship editorial-2026), so nothing else is lost.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/revise_profiles_batch4.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $F = "$root/inventory/legacy/fetched";
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{00A0}"], ["'", "'", '"', '"', ' '], html_entity_decode(strip_tags((string)$s), ENT_QUOTES))));
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$VERIFIED = json_decode((string)@file_get_contents("$F/batch4-sha.json"), true)['files'] ?? [];
$read = function ($src) use ($ws, $F, $VERIFIED) {
    if (is_string($src)) { return isset($VERIFIED["$src.htm"]) && hash_file('sha256', "$F/$src.htm") === $VERIFIED["$src.htm"] ? $ws(@file_get_contents("$F/$src.txt")) : ''; }
    $e = Entry::find()->id($src)->status(null)->one(); if (!$e) { return ''; }
    return $ws($e->body . ' ' . ($e->getFieldLayout()->getFieldByHandle('photoCaptionExt') ? $e->photoCaptionExt : ''));
};
$LEGACY = fn($k, $title) => "$title, as carried on SCVHistory.com, /scvhistory/$k.htm.";

$P = [];
$P[315] = ['title' => 'Christopher Houston Carson',
    'must' => ['pollack1114kitcarson' => ['Born in Kentucky in 1809', 'roamed the West as a fur trapper and mountain man', 'Fremont eventually hired him as a guide on his first Western expedition', 'Kearny sent a young Navy lieutenant, Edward F. Beale, along with Kit Carson and an Indian guide to sneak through the Mexican lines'], 857 => ['The press dubbed him "The Pathfinder," even though Kit Carson was his guide and found most of the paths']],
    'body' => [
        'Christopher "Kit" Carson, born in Kentucky in 1809, was a fur trapper and mountain man who guided John C. Frémont on his expeditions to the Far West; the press called Frémont "the Pathfinder," though Carson found most of the paths.[1][2] In December 1846 he and Edward F. Beale crept through the Mexican lines at San Pasqual to bring reinforcements.[1] No source in this archive places him in the Santa Clarita Valley.',
    ],
    'notes' => [$LEGACY('pollack1114kitcarson', 'Alan Pollack, "Kit Carson, Ned Beale Cross Paths and Alter California\'s Future"'), 'Jerry Reynolds, "Chapter 18. The Pathfinder," History of the Santa Clarita Valley, article #857 in this archive.'],
];
$P[299] = ['title' => 'Junípero Serra',
    'must' => [3673 => ['We have no reason to believe Serra ever set foot in the Santa Clarita Valley. He stayed behind in San Diego with a leg injury when in 1769', 'They passed through the Santa Clarita Valley from Aug. 8-10 of that year', '(His successor, Fr. Fermín Lasuén, established the Mission San Fernando in 1797.)'],
        12532 => ['The Estancia de San Francisco Xavier was a ranching out-station, and probably a religious outpost, of Mission San Fernando']],
    'body' => [
        'Father Junípero Serra, who founded the missions of Alta California, has no known connection to the Santa Clarita Valley. As Leon Worden writes, "We have no reason to believe Serra ever set foot" here: he stayed behind in San Diego with an injured leg when the Portolá expedition crossed the valley in August 1769.[1] His successor, Fermín Lasuén, founded Mission San Fernando in 1797, and its outpost in the valley, the Estancia de San Francisco Xavier, followed.[1][2]',
    ],
    'notes' => ['Leon Worden, "Fr. Junipero Serra," LW2441a, photograph #3673 in this archive.', 'Leon Worden, "Latins Invade, Conquer Western SCV," August 28, 1996, article #12532 in this archive.'],
];
$P[307] = ['title' => 'John C. Frémont',
    'must' => ['lw2225' => ['On Jan. 9, 1847, Frémont and his 100-man "buckskin battalion" arrived at Castaic Junction from the north', 'probably stopped overnight at the Del Valle ranch home', 'The following night, the troops camped at the Newhall Pass', 'crossing the San Gabriels through Frémont Pass (later confused with Beale\'s Cut, which was about a quarter-mile to the west)', 'Pico handed over his sword to Frémont in what is known as the Capitulation of Cahuenga', 'Born Jan. 21, 1813 in Savannah, Ga.', 'Voted into the U.S. Senate in California\'s first election following statehood in 1850', 'the Republican Party\'s first presidential candidate in 1856', 'Territorial Governor of Arizona, a post he held from 1878-83', 'He died July 13, 1890 in New York City'],
        857 => ['Frémont would leave his name on mountains, towns, and even the pass between the Santa Clarita and San Fernando valleys', 'arriving at Castaic Junction on the evening of January 9, 1847', 'The press dubbed him "The Pathfinder," even though Kit Carson was his guide and found most of the paths']],
    'body' => [
        'John C. Frémont led the American battalion that crossed the Santa Clarita Valley in January 1847 on its way to Cahuenga, where Andrés Pico surrendered to him, and the pass between the Santa Clarita and San Fernando valleys bears his name.[1][2]',
        'On January 9, 1847, Frémont and his hundred-man "buckskin battalion" reached Castaic Junction from the north and probably stopped overnight at the del Valle ranch house. The next night they camped at the Newhall Pass. They crossed the San Gabriels by the pass later named for him, a quarter-mile east of the gap later cut by Beale, and went on to meet Pico in the Capitulation of Cahuenga.[1][2]',
        'Born in Savannah, Georgia, in 1813, he was an Army explorer the press called "the Pathfinder," though Kit Carson, his guide, found most of the paths, and he was later one of California\'s first United States senators, the Republican Party\'s first presidential candidate in 1856, and territorial governor of Arizona; he died in New York City in 1890.[1][2]',
    ],
    'notes' => [$LEGACY('lw2225', 'Leon Worden, "John C. Fremont," LW2225'), 'Jerry Reynolds, "Chapter 18. The Pathfinder," History of the Santa Clarita Valley, article #857 in this archive.'],
];
$P[297] = ['title' => 'Juan Crespí',
    'must' => [835 => ['The chief diarist of the Portolá expedition of 1769 was Father Juan Crespí', 'read the unexpurgated narrative of the first European to visit the Santa Clara River Valley', 'we must have encountered at least five hundred souls here', 'is known as the Paraje del Corrál (Pen Camp)', 'with a great many very large, round houses well-roofed with grass', 'Father Crespí traded tobacco for beads', 'They are made of white shells'],
        3673 => ['They passed through the Santa Clarita Valley from Aug. 8-10 of that year'],
        1436 => ['there exist the day-by-day diaries of Father Crespi, the Franciscan, and Constanso, lieutenant of Gaspar de Portola, to tell of the valley and its inhabitants as they saw them'],
        12532 => ['the peaceful Tataviam Indians of the Upper Santa Clara River Valley offered seeds and sweet preserves to Don Gaspar de Portolá\'s soldiers', 'Built with Tataviam labor in 1804 on the site proposed by Portolá and Crespí'],
        'pollack0713crespi' => ['Born in 1721 in Palma de Mallorca, Spain, Crespí became a Franciscan priest at age 17', 'he attended philosophy and theology classes taught by Father Junipero Serra', 'Father Crespí was sent to, and placed in charge of, the Misión La Purísima Concepción de Cadegomó', 'the land expedition left San Diego on July 14, 1769, with Portolá in command']],
    'body' => [
        'Father Juan Crespí\'s diary is the first written account of the Santa Clarita Valley and its people. He was the chief diarist of the Portolá expedition, which crossed the valley from August 8 to 10, 1769. Jerry Reynolds calls his journal the narrative of "the first European to visit the Santa Clara River Valley," and A.B. Perkins wrote that his diary and Miguel Costansó\'s "tell of the valley and its inhabitants as they saw them."[1][2][3]',
        'At a village where two creeks met, which the soldiers called the Paraje del Corral, he reckoned they met at least five hundred people. He described "very large, round houses well-roofed with grass," traded tobacco for beads of white shell, and recorded that the Tataviam offered the soldiers seeds and sweet preserves.[1][4] The Estancia de San Francisco Xavier, the outpost of Mission San Fernando built with Tataviam labor in 1804, stood on a site he and Portolá had proposed.[4]',
        'Born in Palma, Majorca, in 1721, he became a Franciscan at seventeen, studied under Junípero Serra, and in 1768 was put in charge of the mission of La Purísima Concepción de Cadegomó in Baja California, from where he marched north the next year and kept the diary of the expedition that left San Diego for Monterey on July 14, 1769.[5]',
    ],
    'notes' => ['Jerry Reynolds, "Chapter 8. The Feast," History of the Santa Clarita Valley, article #835 in this archive, quoting Crespí\'s diary.', 'Leon Worden, "Fr. Junipero Serra," LW2441a, photograph #3673 in this archive.', 'A.B. Perkins, "Manuscript: Colonization," article #1436 in this archive.', 'Leon Worden, "Latins Invade, Conquer Western SCV," August 28, 1996, article #12532 in this archive.', $LEGACY('pollack0713crespi', 'Alan Pollack, "Crespi\'s SCV Legacy"')],
];

$bad = []; $plan = [];
foreach ($P as $id => $c) {
    $p = Entry::find()->id($id)->status(null)->one();
    if (!$p || $p->title !== $c['title']) { $bad[] = "#$id is not {$c['title']}"; continue; }
    if (($p->bodyAuthorship->value ?? '') !== 'editorial-2026') { $bad[] = "{$c['title']}: body is not batch 2's editorial text"; }
    foreach ($c['must'] as $src => $phrases) { $t = $read($src); foreach ($phrases as $ph) { if (!str_contains($t, $ws($ph))) { $bad[] = "{$c['title']}: " . (is_string($src) ? $src : "#$src") . ' does not read "' . mb_substr($ph, 0, 60) . '"'; } } }
    $body = implode("\n\n", $c['body']);
    preg_match_all('~\[(\d+)\]~', $body, $m); $used = array_values(array_unique(array_map('intval', $m[1])));
    if (count($used) !== count($c['notes']) || max($used) > count($c['notes'])) { $bad[] = "{$c['title']}: notes used " . json_encode($used) . ' of ' . count($c['notes']); }
    if (preg_match('~\x{2014}~u', $body . implode('', $c['notes']))) { $bad[] = "{$c['title']}: an em dash"; }
    $done = trim((string)$p->body) === trim($body);
    $plan[$id] = ['body' => $body, 'done' => $done, 'was' => str_word_count(strip_tags((string)$p->body))];
    echo str_pad($c['title'], 28) . ($done ? 'already revised' : "{$plan[$id]['was']} words -> " . str_word_count($body) . ' words, ' . count($c['notes']) . ' notes') . PHP_EOL;
}
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$els = Craft::$app->getElements(); $n = 0; $short = [];
foreach ($P as $id => $c) {
    $p = Entry::find()->id($id)->status(null)->one();
    if (!$plan[$id]['done']) {
        $p->setFieldValues(['body' => $plan[$id]['body'], 'footnotes' => $fn($c['notes'])]);
        if (!$els->saveElement($p)) { $short[] = "{$c['title']} " . json_encode($p->getFirstErrors()); continue; }
    }
    $r = Entry::find()->id($id)->status(null)->one();
    $ok = trim((string)$r->body) === trim($plan[$id]['body']) && count($r->footnotes ?? []) === count($c['notes']);
    $ok ? $n++ : $short[] = $c['title'];
    echo ($ok ? 'OK    ' : 'SHORT ') . $r->title . ' ' . $r->url . PHP_EOL;
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : "OK: $n records") . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('revise_profiles_batch4.php', $n, $short ? 'SHORT' : 'verified', 'valley first: Carson and Serra cut to what places them (nothing / never here), Frémont and Crespí lead with the valley');
if ($short) { throw new \RuntimeException('revise_profiles_batch4: ' . implode(', ', $short)); }
