/**
 * The 25 blank records, batch 3: the Tataviam, the Portolá Expedition, the del
 * Valle family and the Newhall family (Nathan, 3 October 2026: "Write the 16
 * clearly local"; "Catalonian Volunteers: fold into Portolá", so the
 * expedition's record tells of Fages's twenty-five Catalonian soldiers).
 *
 * Written from archive records and from pages already read from the Reggie
 * mirror and verified against its manifest. Where sources disagree the body
 * says so: the last full-blooded Tataviam died in 1916 by Leon Worden's 1996
 * account and in 1921, at Camulos, by Jerry Reynolds's. Leon Worden's framing
 * of the Tataviam's end is attributed to him, not stated as the archive's.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_blank_records_3.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $F = "$root/inventory/legacy/fetched"; $els = Craft::$app->getElements();
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{00A0}"], ["'", "'", '"', '"', ' '], html_entity_decode(strip_tags((string)$s), ENT_QUOTES))));
$VERIFIED = [];
foreach (['batch4', 'batch5', 'batch6', 'batch7', 'batch8'] as $b) { $VERIFIED += json_decode((string)@file_get_contents("$F/$b-sha.json"), true)['files'] ?? []; }
$read = function ($src) use ($ws, $F, $VERIFIED) {
    if (is_string($src)) { return isset($VERIFIED["$src.htm"]) && hash_file('sha256', "$F/$src.htm") === $VERIFIED["$src.htm"] ? $ws(@file_get_contents("$F/$src.txt")) : ''; }
    return $ws(Entry::find()->id($src)->status(null)->one()?->body);
};
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$LEGACY = fn($k, $title) => "$title, as carried on SCVHistory.com, /scvhistory/$k.htm.";

$R = [];
$R[913] = ['title' => 'Tataviam',
    'must' => [827 => ['These newcomers, arriving about AD 450, spoke Takic, the Uto-Aztecan language of the Shoshone Indians', 'The invasion was complete by AD 500 with the takeover of the Upper Santa Clara River Valley', 'They referred to them as Atapili-ish or Allikliks, meaning "grunters."', 'their cousins, the Kitanemuks, always called them Tataviam, or "Dwellers on Sunny Slopes"', 'The Tataviam settled down into some twenty-five semi-permanent villages', 'Kamulus stood at the present Camulos Ranch, Piru-U-Bit sat on the banks of Piru Creek, Tochonanga clustered around Newhall Creek, while Chaguayabit was a metropolis of about five hundred souls at Castaic Junction', 'The Tataviam wove excellent baskets but made no pottery'],
        829 => ['The last full-blooded Tataviam died on the Camulos Ranch in 1921', 'The Tataviam did leave a wealth of information about themselves chiseled into and painted on rocky overhangs and secreted deep in caves'],
        833 => ['Yet there was no panic among the Tataviam, merely cordial welcome and quiet acceptance. It was August 8, 1769'],
        12532 => ['offered seeds and sweet preserves to Don Gaspar de Portolá\'s soldiers when they rode their peculiar four-legged beasts into Castaic Junction on Tuesday, August 8, 1769', 'Spanish troops removed the Tataviam from their land and relocated them', 'to San Fernando, where they were put to work in mission vineyards and fields. All Tataviam were baptized by 1810', 'Their introduction to Old World diseases and their intermarriage with other relocated tribes effectively ended their 1400-year history. The last full-blooded Tataviam died in 1916', 'Within a few years after the founding of Mission San Fernando'],
        'engelhardt_villagenames' => ['he includes, from examinations of mission records, the names of the ancestral villages of the Indians brought to each mission']],
    'body' => [
        'The Tataviam were the people of the upper Santa Clara River valley when the Spanish came. Their neighbors the Kitanemuks called them Tataviam, "dwellers on sunny slopes"; peoples they had displaced called them Allikliks, "grunters." They spoke Takic, a Uto-Aztecan language, and by Jerry Reynolds\'s account had taken the upper valley by about AD 500.[1]',
        'They lived in some twenty-five villages: Kamulus at what is now Camulos, Piru-U-Bit on Piru Creek, Tochonanga on Newhall Creek, and Chaguayabit, a town of about five hundred, at Castaic Junction.[1] They wove fine baskets but made no pottery, and left paintings and carvings on rock overhangs and in caves.[1][2]',
        'When the Portolá expedition rode into Castaic Junction on August 8, 1769, the Tataviam met it with a cordial welcome, and Father Juan Crespí recorded that they offered the soldiers seeds and sweet preserves.[3][4] Within a few years of Mission San Fernando\'s founding, Spanish troops removed them from their land to the mission, where they were put to work in its vineyards and fields; all had been baptized by 1810.[4] The mission registers name their villages as the birthplaces of people brought to San Fernando and San Gabriel.[5] Disease and intermarriage with other relocated peoples, Leon Worden wrote in 1996, effectively ended their 1,400-year history. He gives 1916 for the death of the last full-blooded Tataviam; Reynolds gives 1921, at the Camulos Ranch.[4][2]',
    ],
    'notes' => ['Jerry Reynolds, "Chapter 4: Children of Nature," History of the Santa Clarita Valley, article #827 in this archive.', 'Jerry Reynolds, "Chapter 5: Tribal Relics," History of the Santa Clarita Valley, article #829 in this archive.', 'Jerry Reynolds, "Chapter 7. Spain Reconnoiters," History of the Santa Clarita Valley, article #833 in this archive.', 'Leon Worden, "Latins Invade, Conquer Western SCV," August 28, 1996, article #12532 in this archive.', $LEGACY('engelhardt_villagenames', '"Village Names in Baptismal Records at San Gabriel, San Fernando Missions," from Fr. Zephyrin Engelhardt (1927)')],
];
$R[938] = ['title' => 'Portolá Expedition',
    'must' => [833 => ['It was August 8, 1769, when a strange cavalcade appeared atop what is now called Frémont Pass, a quarter-mile east of Beale\'s Cut in the Newhall Pass', 'Serra remained there to nurse the struggling colony along while the governor headed north with sixty-four men to locate the Bay of Monterey'],
        3673 => ['They passed through the Santa Clarita Valley from Aug. 8-10 of that year'],
        837 => ['Fages was a lieutenant in charge of the twenty-five Catalonian soldiers during the Portolá march to Monterey'],
        835 => ['The chief diarist of the Portolá expedition of 1769 was Father Juan Crespí', 'read the unexpurgated narrative of the first European to visit the Santa Clara River Valley', 'we must have encountered at least five hundred souls here', 'is known as the Paraje del Corrál (Pen Camp)', 'Going three leagues, we came to the meeting of these creeks and set up camp close to a very sizable village', 'with a great many very large, round houses well-roofed with grass'],
        'lapuerta2023' => ['encountered the inhabitants of Tochonanga in the vicinity of today\'s Eternal Valley Cemetery, they traversed the mountains by way of an ancient road that their Native hosts had marked out for them', 'This was Camino Viejo'],
        12532 => ['offered seeds and sweet preserves to Don Gaspar de Portolá\'s soldiers', 'Portolá had considered the confluence of Castaic Creek and the Santa Clara River a "very suitable site" for Father Junípero Serra to establish a mission, but it was not to be', 'Built with Tataviam labor in 1804 on the site proposed by Portolá and Crespí', 'The Estancia de San Francisco Xavier was a ranching out-station, and probably a religious outpost, of Mission San Fernando']],
    'body' => [
        'The Portolá expedition came over what is now Frémont Pass, a quarter-mile east of Beale\'s Cut, on August 8, 1769, and crossed the Santa Clarita Valley until August 10. Jerry Reynolds calls its chief diarist, Father Juan Crespí, the first European to visit the Santa Clara River Valley.[1][2][3]',
        'Governor Gaspar de Portolá had set out from San Diego with sixty-four men to find the Bay of Monterey, leaving Father Junípero Serra behind. His soldiers included the twenty-five Catalonian Volunteers under Lieutenant Pedro Fages.[1][4]',
        'They came over the mountains by the Camino Viejo, an ancient road their Native hosts showed them, and met the people of Tochonanga near today\'s Eternal Valley Cemetery.[5] At a large village where two creeks met, which the soldiers called the Paraje del Corral, Crespí counted at least five hundred people and described very large, round houses roofed with grass, and the Tataviam offered the soldiers seeds and sweet preserves.[3][6] Portolá thought the meeting of Castaic Creek and the Santa Clara River a very suitable site for a mission. None was built there, but in 1804 the Estancia de San Francisco Xavier, an outpost of Mission San Fernando, went up on the site he and Crespí had proposed.[6]',
    ],
    'notes' => ['Jerry Reynolds, "Chapter 7. Spain Reconnoiters," History of the Santa Clarita Valley, article #833 in this archive.', 'Leon Worden, "Fr. Junipero Serra," LW2441a, photograph #3673 in this archive.', 'Jerry Reynolds, "Chapter 8. The Feast," History of the Santa Clarita Valley, article #835 in this archive, quoting Crespí\'s diary.', 'Jerry Reynolds, "Chapter 9. The Trail Blazer," History of the Santa Clarita Valley, article #837 in this archive.', $LEGACY('lapuerta2023', 'Leon Worden, "La Puerta: Gateway to the Santa Clarita Valley," March 2023'), 'Leon Worden, "Latins Invade, Conquer Western SCV," August 28, 1996, article #12532 in this archive.'],
];
$R[915] = ['title' => 'del Valle Family',
    'must' => [291 => ['Antonio del Valle was a Mexican army lieutenant who came to California in 1819, took charge of Mission San Fernando when it was secularized, and in 1839 received Rancho San Francisco', 'He died on the rancho two years later without a will'],
        293 => ['a judge awarded awarded 13,599 acres to Ygnacio (the westernmost section), 21,307 acres to Jacoba and 4,684 acres to each of Jacoba\'s six children', 'In 1861 Wolfskill cut a deal with Ygnacio that paid off the Salazár\'s debts and gave Ygnacio the western five-elevenths of the Rancho San Francisco', 'it was 1861 before Ygnacio would move permanently into the adobe home on his property (the section known as Camulos, near Piru'],
        27374 => ['Rancho San Francisco was deeded to Henry M. Newhall, consideration $90,000, January 15, 1875'],
        'lw3664' => ['Juventino (1841-1919) was the eldest child of Ygnacio del Valle', 'he served as ranch manager from 1862-1886', 'when Juventino\'s half-brother Reginaldo hosted the big, annual fiestas at the ranch']],
    'body' => [
        'The del Valle family held the Rancho San Francisco, the western Santa Clarita Valley, for a generation. Antonio del Valle, a Mexican army lieutenant who had taken charge of Mission San Fernando when it was secularized, received the rancho in 1839 and died on it two years later without a will.[1]',
        'A judge divided the estate: the westernmost section, 13,599 acres, to his son Ygnacio, 21,307 acres to his widow, Jacoba, and 4,684 acres to each of her six children. In 1861 a settlement with the creditor William Wolfskill gave Ygnacio the western five-elevenths of the rancho, and that year he moved for good into the adobe on the section known as Camulos, near Piru.[2] The rancho was deeded to Henry M. Newhall in 1875.[3] Camulos stayed with the family: Ygnacio\'s eldest son, Juventino, managed it from 1862 to 1886, and in the 1880s Juventino\'s half-brother Reginaldo hosted its big annual fiestas.[4]',
    ],
    'notes' => ['Antonio del Valle\'s profile, person #291 in this archive, and its sources.', 'Leon Worden, "Ygnacio del Valle," LW2052, the profile of person #293 in this archive.', 'A.B. Perkins, "Rancho San Francisco: A Study of a California Land Grant" (1957), document #27374 in this archive.', $LEGACY('lw3664', 'Leon Worden, "Juventino del Valle, Black Walnut Tree, (5) Important Camulos Views, 1910s," LW3664')],
];
$R[944] = ['title' => 'Newhall Family',
    'must' => [283 => ['Between 1872 and 1875, Newhall purchased five of the old Mexican land-grant ranchos throughout California, including 46,460 acres of the 48,000-acre Rancho San Francisco', 'He sold a railroad right-of-way to Southern Pacific for a dollar', 'a town sprang up bearing his nam', 'from which he died on March 13 at the age of 56', 'On June 1, 1883, H.M. Newhall\'s five sons incorporated the family-owned Newhall Land and Farming Company', 'Agriculture, cattle ranching, and vineyards in Central California remained the company\'s focus well into the 20th Century, even after oil was discovered on the Rancho San Francisco (by then called the Newhall Ranch)', 'In March of 1882, he was thrown from his horse while riding on the Rancho']],
    'body' => [
        'The Newhall family came to the Santa Clarita Valley with Henry Mayo Newhall, who between 1872 and 1875 bought five of California\'s old Mexican ranchos, among them 46,460 acres of the Rancho San Francisco. He sold the Southern Pacific a right-of-way for a dollar, and the town that grew up there bears his name.[1]',
        'He was thrown from his horse while riding on the rancho in March 1882 and died that month. On June 1, 1883, his five sons incorporated the family-owned Newhall Land and Farming Company, which kept to agriculture, cattle and vineyards well into the 20th century, even after oil was found on the rancho, by then called the Newhall Ranch.[1]',
    ],
    'notes' => ['Leon Worden\'s profile of Henry Mayo Newhall, person #283 in this archive.'],
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
$applyLog('build_blank_records_3.php', $n, $short ? 'SHORT' : 'verified', 'the 25, batch 3: Tataviam, Portolá Expedition, del Valle Family, Newhall Family');
if ($short) { throw new \RuntimeException('build_blank_records_3: ' . implode(', ', $short)); }
