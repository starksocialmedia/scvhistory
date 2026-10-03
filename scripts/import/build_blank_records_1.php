/**
 * The 25 blank records, batch 1: the short ones (Nathan, 3 October 2026:
 * "Mission San Gabriel: short record. Baptismal records naming valley villages
 * is real evidence of this place"; "Rancho El Tejon: a line. Ygnacio's
 * co-grant is a real connection even though the land is elsewhere"; "Los
 * Angeles Herald and Historical Society of Southern California: a line each as
 * publishers of sources cited here"; and of the 16 clearly local, the Walk of
 * Western Stars and SCVHistory.com).
 *
 * Each body replaces nothing: these records' WordPress text was withheld into
 * withheldBody on 3 October and stays there. Every phrase relied on is checked
 * against a page read from the Reggie mirror and matching its manifest
 * (batch5-, batch6-, batch7-sha.json) or against an archive record.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_blank_records_1.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $F = "$root/inventory/legacy/fetched"; $els = Craft::$app->getElements();
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{00A0}"], ["'", "'", '"', '"', ' '], html_entity_decode(strip_tags((string)$s), ENT_QUOTES))));
$VERIFIED = [];
foreach (['batch4', 'batch5', 'batch6', 'batch7'] as $b) { $VERIFIED += json_decode((string)@file_get_contents("$F/$b-sha.json"), true)['files'] ?? []; }
$read = function ($src) use ($ws, $F, $VERIFIED) {
    if (is_string($src)) { return isset($VERIFIED["$src.htm"]) && hash_file('sha256', "$F/$src.htm") === $VERIFIED["$src.htm"] ? $ws(@file_get_contents("$F/$src.txt")) : ''; }
    return $ws(Entry::find()->id($src)->status(null)->one()?->body);
};
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$LEGACY = fn($k, $title) => "$title, as carried on SCVHistory.com, /scvhistory/$k.htm.";

$R = [];
$R[16511] = ['title' => 'Mission San Gabriel Arcángel',
    'must' => ['engelhardt_villagenames' => ['Some were taken into the San Gabriel Mission, which predated the San Fernando Mission by 26 years', 'Some of the villages listed by Engelhardt from San Gabriel Mission records were in the Santa Clarita Valley', 'No dates are associated with his list, so we don\'t know if they relate to people who arrived at San Gabriel before or after the San Fernando Mission was built in 1797', 'Chaguayabit (Choquayabit, Chaguayanga, etc.), at Castaic Junction', 'Jujunga (Juiunga), Saugus area', 'Chaguayabit Choquayabit', 'Juiunga'],
        'us8504' => ['Claudio served as mayordomo at the Mission San Gabriel for 40 years'],
        'us8502' => ['grandson of Claudio Lopez who came to mission San Gabriel', 'Claudio Lopez\'s brother was was the father of 1842 gold discoverer Francisco Lopez'],
        'obituary_franciscolopez' => ['after his death his body was buried inside the mission and a tablet in his memory is in the mission today'],
        'sgb03346' => ['Mission records show Francisco was baptized as a newborn at Mission San Gabriel']],
    'body' => [
        'Mission San Gabriel Arcángel, east of Los Angeles, holds some of the earliest records of the Santa Clarita Valley\'s people. Its baptismal registers name villages in the valley as the birthplaces of people brought to the mission, among them Chaguayabit, at Castaic Junction, and Juiunga, in the Saugus area, as the mission historian Zephyrin Engelhardt listed them in 1927. The lists are undated, so they may record people who came before or after Mission San Fernando, which San Gabriel predates by 26 years, was built in 1797.[1]',
        'Claudio López was its mayordomo for forty years and was buried inside the mission. His grandson Francisco "Chico" López ranched at Elizabeth Lake, and his nephew Francisco López, who discovered gold in Placerita Canyon in 1842, was baptized at San Gabriel as a newborn.[2][3][4][5]',
    ],
    'notes' => [$LEGACY('engelhardt_villagenames', '"Village Names in Baptismal Records at San Gabriel, San Fernando Missions," from Fr. Zephyrin Engelhardt, San Gabriel Mission and the Beginnings of Los Angeles (1927)'),
        $LEGACY('us8504', '"Francisca Lopez de Belderrain, Source of Golden Dream Site & Story; Lopez Genealogy," US8504'),
        $LEGACY('us8502', '"Francisco \'\'Chico\'\' Lopez, ~1820-1900," US8502'),
        $LEGACY('obituary_franciscolopez', '"Death of a Pioneer," Los Angeles Times, January 21, 1900'),
        $LEGACY('sgb03346', '"Baptismal Data: Francisco Lopez (Gold Discoverer)," from the Huntington Library\'s Early California Population Project database')],
];
$R[16506] = ['title' => 'Rancho El Tejon',
    'must' => [27374 => ['With Jose Antonio Aguirre, received grant of the 97,000 acre Tejon rancho in 1843'], 2081 => ['General Beale bought Rancho La Liebre on August 8, 1855', 'Ranchos Castac (Castaic), Los Alamos Y Agua Caliente and Tejon']],
    'body' => ['Rancho El Tejon, a grant of some 97,000 acres, was made in 1843 to Ygnacio del Valle, of Rancho San Francisco, and José Antonio Aguirre.[1] Edward F. Beale later added it to the ranchos he gathered after buying Rancho La Liebre in 1855.[2]'],
    'notes' => ['A.B. Perkins, "Rancho San Francisco: A Study of a California Land Grant" (1957), document #27374 in this archive.', 'Jerry Reynolds, "28. Monarch of All He Surveys," History of the Santa Clarita Valley, article #2081 in this archive.'],
];
$R[390] = ['title' => 'Los Angeles Herald',
    'must' => ['herald032875' => ['When a reporter for the Los Angeles Herald came north to check out the rumors of oil', 'We had the pleasure of a two day\'s pasear[1] at and around Lyon\'s Station last week']],
    'body' => ['The Los Angeles Herald, a Los Angeles daily newspaper, is in this archive as the publisher of reports from the valley that the archive cites, among them a reporter\'s two days at Lyon\'s Station in March 1875, sent north to look into rumors of oil.[1]'],
    'notes' => [$LEGACY('herald032875', '"Up in the Mountains," Los Angeles Herald, March 1875, with Leon Worden\'s note')],
];
$R[392] = ['title' => 'Historical Society of Southern California',
    'must' => ['hssc1929parks_chicolopez' => ['Historical Society of Southern California Annual | Vol. 14 No 2, 1929', 'La Laguna de Chico Lopez. aka Elizabeth Lake'], 'hssc1929parks_buque' => ['Cañon del Buque. Origin of the Name, Martin Ruiz Adobe'], 'hssc1928belderrain' => ['By Francisca Lopez de Belderrain. Historical Society of Southern California Annual Publication, 1928']],
    'body' => ['The Historical Society of Southern California is in this archive as the publisher of its Annual, the source of Marion Parks\'s 1929 visits to the old adobes of Bouquet Canyon and Elizabeth Lake and of Francisca López de Belderrain\'s 1928 history of the López family.[1][2][3]'],
    'notes' => [$LEGACY('hssc1929parks_buque', 'Marion Parks, "In Pursuit of Vanished Days," Historical Society of Southern California Annual, vol. 14, no. 2, 1929, the excerpt on Cañon del Buque'), $LEGACY('hssc1929parks_chicolopez', 'The same, the excerpt "La Laguna de Chico Lopez, aka Elizabeth Lake"'), $LEGACY('hssc1928belderrain', 'Francisca Lopez de Belderrain, "The Awakening of Paredon Blanco Under a California Sun," Historical Society of Southern California Annual, 1928')],
];
$R[950] = ['title' => 'Walk of Western Stars Inductees',
    'must' => ['lw2102' => ['The Downtown Newhall Walk of Western Stars began in 1981 as the "Western Walk of Fame" as a means of honoring Western film, stage, television and radio personalities who performed in the Santa Clarita Valley', 'The Walk grew out of a Western Celebrities Luncheon (1975-1979) / Dinner (1980) put together by Jo Anne Darcy after she became manager of the Newhall-Saugus-Valencia (SCV) Chamber of Commerce in 1974', 'which name was changed to "Walk of Western Stars" after the Hollywood Chamber of Commerce sued over the use of the trademarked "Walk of Fame" name', 'Stars were immortalized along San Fernando Road and Newhall Avenue, and later Market Street, with bronze plaques and terrazzo tile set into the sidewalks', '1981 Inductees: Gene Autry William S. Hart* Tom Mix*']],
    'body' => [
        'The Downtown Newhall Walk of Western Stars honors Western film, stage, television and radio performers who worked in the Santa Clarita Valley, with bronze plaques and terrazzo tiles set into the sidewalks of San Fernando Road and Newhall Avenue, and later Market Street.[1]',
        'It began in 1981 as the Western Walk of Fame. It grew out of the Western Celebrities Luncheon that Jo Anne Darcy put together from 1975, after she became manager of the Newhall-Saugus-Valencia Chamber of Commerce in 1974, and took its present name after the Hollywood Chamber of Commerce sued over "Walk of Fame." The first inductees, in 1981, were Gene Autry, William S. Hart and Tom Mix.[1]',
    ],
    'notes' => [$LEGACY('lw2102', '"Downtown Newhall Walk of Western Stars," LW2102')],
];
$R[378] = ['title' => 'SCVHistory.com — Santa Clarita Valley History',
    'must' => ['scvhistory' => ['Online Archives & Repository of the SCV Historical Society, City of Santa Clarita, Friends of Mentryville, Old Town Newhall, More', 'Webmasters: Leon Worden & Alan Pollack'], 'key' => ['AAxxxx = Photographs courtesy of Judge Adrian W. Adams', 'ALxxxx = Photographs courtesy of Alan Pollack', 'APxxxx = Photographs assembled by Arthur B. Perkins, catalogued by Ted Lamkin'], 279 => ['the founder and editor of SCVHistory.com']],
    'body' => [
        'SCVHistory.com is an online archive and research library of Santa Clarita Valley history. It describes itself as the online archives and repository of the SCV Historical Society, the City of Santa Clarita, the Friends of Mentryville, Old Town Newhall and others.[1] Leon Worden founded it and edits it, and he and Alan Pollack are its webmasters.[1][2]',
        'Its photographs come from dozens of collections, each catalogued under its own two-letter prefix: AA for Judge Adrian W. Adams, AL for Alan Pollack, AP for the pictures Arthur B. Perkins assembled and Ted Lamkin catalogued, and so on.[3]',
    ],
    'notes' => [$LEGACY('scvhistory', 'SCVHistory.com, its contents page'), 'Leon Worden\'s profile, person #279 in this archive, and its sources.', $LEGACY('key', '"Photo Sources," SCVHistory.com\'s key to its photographs')],
];

$bad = []; $plan = [];
foreach ($R as $id => $c) {
    $e = Entry::find()->id($id)->status(null)->one();
    if (!$e || $e->title !== $c['title']) { $bad[] = "#$id is not {$c['title']}"; continue; }
    foreach ($c['must'] as $src => $ps) { $t = $read($src); foreach ($ps as $ph) { if (!str_contains($t, $ws($ph))) { $bad[] = "{$c['title']}: " . (is_string($src) ? $src : "#$src") . ' does not read "' . mb_substr($ph, 0, 60) . '"'; } } }
    $body = implode("\n\n", $c['body']); $cur = trim((string)$e->body); $done = $cur === $body;
    if ($cur !== '' && !$done) { $bad[] = "{$c['title']}: has a body this does not replace"; }
    preg_match_all('~\[(\d+)\]~', $body, $m); $used = array_unique(array_map('intval', $m[1]));
    if (count($used) !== count($c['notes']) || max($used) > count($c['notes'])) { $bad[] = "{$c['title']}: notes used " . json_encode(array_values($used)) . ' of ' . count($c['notes']); }
    if (preg_match('~\x{2014}~u', $body . implode('', $c['notes']))) { $bad[] = "{$c['title']}: an em dash"; }
    $plan[$id] = [$e, $body, $done];
    echo str_pad($c['title'], 46) . ($done ? 'already written' : str_word_count($body) . ' words, ' . count($c['notes']) . ' notes') . PHP_EOL;
}
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$short = []; $n = 0;
foreach ($plan as $id => [$e, $body, $done]) {
    if (!$done) { $e->setFieldValues(['body' => $body, 'footnotes' => $fn($R[$id]['notes'])]); if (!$els->saveElement($e)) { $short[] = "{$e->title} " . json_encode($e->getFirstErrors()); continue; } }
    $r = Entry::find()->id($id)->status(null)->one(); $ok = trim((string)$r->body) === $body && count($r->footnotes ?? []) === count($R[$id]['notes']);
    $ok ? $n++ : $short[] = $r->title; echo ($ok ? 'OK    ' : 'SHORT ') . $r->title . ' ' . $r->url . PHP_EOL;
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : "OK: $n records") . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('build_blank_records_1.php', $n, $short ? 'SHORT' : 'verified', 'the 25, batch 1: Mission San Gabriel, Rancho El Tejon, LA Herald, HSSC, Walk of Western Stars, SCVHistory.com');
if ($short) { throw new \RuntimeException('build_blank_records_1: ' . implode(', ', $short)); }
