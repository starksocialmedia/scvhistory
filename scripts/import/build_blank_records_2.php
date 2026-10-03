/**
 * The 25 blank records, batch 2: places and the 1994 earthquake (Nathan,
 * 3 October 2026: "Write the 16 clearly local"). Rancho San Francisco, Lyons
 * Station, Beale's Cut, the Newhall Pass interchange and the Northridge
 * earthquake, each from Leon Worden's and the archive's sources, each opening
 * with what happened here.
 *
 * The Northridge earthquake's legacyKey names newhallpass.htm, which the
 * mirror does not hold; its sources are the archive's own 1994 photographs,
 * with Leon Worden's captions, and two columns. The Newhall Pass interchange's
 * sources speak of the pass and its freeway bridges, not of the interchange's
 * design, and the body says only what they say.
 * Rudy Alexander Acosta (#526) is not written: his page already carries the
 * war memorial's own narrative, and a body would repeat it.
 * Every phrase relied on is checked against a page read from the Reggie mirror
 * and matching its manifest (batch4- to batch8-sha.json) or an archive record.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_blank_records_2.php'))"
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
$R[16446] = ['title' => 'Rancho San Francisco',
    'must' => [27374 => ['The motorist on California State Highway 6 enters the old rancho limit slightly beyond the crest of the Newhall Pass, and leaves the rancho boundaries on Highway 99 near the town of Castaic', 'he will run out of the rancho boundaries when crossing Piru Creek', 'Don Antonio del Valle petitioned Governor Alvarado for the Rancho January 22, 1839', 'José Salazar borrowed $8,500 from William Wolfskill', 'the grant finally went to patent February 12, 1875', 'Rancho San Francisco was deeded to Henry M. Newhall, consideration $90,000, January 15, 1875', 'The first postoffice on the Rancho was opened at Lyons Station'],
        'lapuerta2023' => ['When Mexican Army Lt. Antonio del Valle received the Rancho San Francisco — the ex-mission rancho in the Santa Clarita Valley — as a gift from California\'s governor in 1839, La Puerta demarcated the southeastern tip of his property'],
        293 => ['the 48,829-acre Rancho San Francisco, consisting of the western Santa Clarita Valley and portions of Ventura County easterly to the Piru area', 'a judge awarded awarded 13,599 acres to Ygnacio (the westernmost section), 21,307 acres to Jacoba and 4,684 acres to each of Jacoba\'s six children', 'In 1861 Wolfskill cut a deal with Ygnacio that paid off the Salazár\'s debts and gave Ygnacio the western five-elevenths of the Rancho San Francisco', 'the section known as Camulos']],
    'body' => [
        'Rancho San Francisco was the Mexican land grant that held the western Santa Clarita Valley: 48,829 acres, with part of Ventura County as far as Piru. A traveler entered it just beyond the crest of the Newhall Pass, left it near Castaic going north, and left it at Piru Creek going west along the Santa Clara River.[1][2] Antonio del Valle petitioned Governor Juan B. Alvarado for the former mission rancho on January 22, 1839. Its southeastern corner was La Puerta, "the door," a bar across the old road at the foot of the pass.[1][3]',
        'After Antonio\'s death the estate was fought over. A judge gave his son Ygnacio the westernmost section, 13,599 acres, which included Camulos; 21,307 acres to Antonio\'s widow, Jacoba; and 4,684 acres to each of her six children. José Salazar, who had married Jacoba, borrowed against the grant from William Wolfskill, and in 1861 Wolfskill made a settlement that paid off the Salazars\' debts and gave Ygnacio the western five-elevenths of the rancho.[2][1]',
        'The rancho was deeded to Henry M. Newhall on January 15, 1875, for $90,000, and the grant went to patent that February 12. The first post office on the rancho had opened at Lyon\'s Station.[1]',
    ],
    'notes' => ['A.B. Perkins, "Rancho San Francisco: A Study of a California Land Grant" (1957), document #27374 in this archive.', 'Leon Worden, "Ygnacio del Valle," LW2052, the profile of person #293 in this archive.', $LEGACY('lapuerta2023', 'Leon Worden, "La Puerta: Gateway to the Santa Clarita Valley," March 2023')],
];
$R[926] = ['title' => 'Lyons Station Stagecoach Stop',
    'must' => ['pollack0912lyon' => ['The Lyon brothers were twins born to Henry and Betsy Lyon in Machias, Maine, in 1831', 'An old stage depot at the base of the Fremont Pass on the Rancho San Francisco', 'the site of today\'s Eternal Valley Cemetery', 'Lyons Station — then known as Hart\'s Station for then-owner Josiah Hart — became a regular stop on the famous route', 'with its first run from Tipton, Mo., to San Francisco on September 16, 1858', 'Contemporary reports claimed the Fremont Pass was the most difficult crossing of the entire route', 'eventually grew into a multi-use complex consisting of a depot, tavern, store, telegraph office and post office'],
        27374 => ['The first postoffice on the Rancho was opened at Lyons Station'],
        'newmark_lyons' => ['Newmark says this happened in 1856', 'Newmark wrote his reminiscences about his adventures with Cyrus Lyon roughly 60 years after they took place', 'made our first step at Lyons\'s [sic] Station, where we put up for the night', 'Having to draw some thick blackstrap[2] from a keg', 'overflowed the top of the receptacle and spread itself over the dirt floor'],
        'herald032875' => ['We had the pleasure of a two day\'s pasear[1] at and around Lyon\'s Station last week', 'When a reporter for the Los Angeles Herald came north to check out the rumors of oil']],
    'body' => [
        'Lyon\'s Station was the stage stop at the foot of the Frémont Pass, on the Rancho San Francisco, where Eternal Valley Cemetery is today. It took its name from Sanford and Cyrus Lyon, twins born in Machias, Maine, in 1831, who bought an old stage depot there.[1]',
        'As Hart\'s Station, for its owner Josiah Hart, it was a regular stop on the Butterfield Overland Mail, whose first run was in September 1858, at what contemporary reports called the most difficult crossing of the whole route. It grew from a meal and rest stop into a depot, tavern, store, telegraph office and post office, the first post office on the rancho.[1][2]',
        'Harris Newmark, writing some sixty years later, remembered stopping for the night there in 1856, when one of the brothers sat down to talk while drawing molasses from a keg and let it run over onto the dirt floor.[3] In March 1875 a Los Angeles Herald reporter spent two days at and around the station, looking into rumors of oil.[4]',
    ],
    'notes' => [$LEGACY('pollack0912lyon', 'Alan Pollack, "The Brothers Lyon and Their Stagecoach Station," Heritage Junction Dispatch, September-October 2012'), 'A.B. Perkins, "Rancho San Francisco: A Study of a California Land Grant" (1957), document #27374 in this archive.', $LEGACY('newmark_lyons', 'Harris Newmark, Sixty Years in Southern California, 1853-1913 (1916), pp. 194-195, with Leon Worden\'s notes'), $LEGACY('herald032875', '"Up in the Mountains," Los Angeles Herald, March 1875, with Leon Worden\'s note')],
];
$R[932] = ['title' => 'Beale\'s Cut Stagecoach Pass',
    'must' => [2081 => ['Road improvements over the pass had been started by Generál Pico during the winter of 1862-63 but were washed out by floods', 'Beale went before the Los Angeles Board of Supervisors, took over Pico\'s franchise and got five thousand dollars to do the work', 'He then called out the troops from Fort Tejon to dig a ninety-foot slash through the mountain barrier with picks and shovels', 'On September 19, 1863, Beale loaned two thousand dollars, at two percent interest, to A.A. Hudson and Oliver P. Robbins, who built a toll house below Beale\'s Cut', 'For the next twenty-one years — until it reverted to the county'],
        'lw2725' => ['made through the sandstone hill three miles below Newhall', '[sic; s/b early 1860s]', 'My father used to keep two span of oxen and a driver there to help pull rigs and wagons and the stages over it', 'Teamsters would telegraph my father when they would be at the cut'],
        'lw2225' => ['crossing the San Gabriels through Frémont Pass (later confused with Beale\'s Cut, which was about a quarter-mile to the west)'],
        'lw2159a' => ['Tom Mix "jumps" over Beale\'s Cut in Newhall in the 1923 John Ford 5-reeler, "3 Jumps Ahead,"', 'insists the stunt was performed by Earl Simpson'],
        'carey-bunse-ranch' => ['It is best known from a scene in Ford\'s 1939 classic, Stagecoach, in which Apaches attack as the coach passes through the cut']],
    'body' => [
        'Beale\'s Cut is the deep slot through the sandstone hill at the Newhall Pass, three miles below Newhall, that carried the road between the San Fernando and Santa Clarita valleys from the early 1860s.[1][2]',
        'Andrés Pico began improving the road over the pass in the winter of 1862-63, but floods washed the work out. Edward F. Beale took over Pico\'s franchise from the Los Angeles supervisors, with five thousand dollars to do the work, and called out troops from Fort Tejon to dig a ninety-foot slash through the mountain with picks and shovels. On September 19, 1863, he lent two thousand dollars to A.A. Hudson and Oliver P. Robbins, who built a toll house below the cut, and the pass was a toll road for twenty-one years, until it reverted to the county.[1]',
        'Even after the cut was made the grade was steep. José Jesús López remembered that his father kept two span of oxen and a driver there to help wagons and stages over, and that teamsters telegraphed ahead when they would reach the cut.[2] The pass Frémont\'s battalion had crossed in 1847, later confused with the cut, was about a quarter-mile to the east.[3] In the 20th century the cut was a film location: Tom Mix\'s horse Tony "jumped" it in John Ford\'s 3 Jumps Ahead (1923), though the stunt was done by Earl Simpson, and Apaches attack the coach as it passes through in Ford\'s Stagecoach (1939).[4][5]',
    ],
    'notes' => ['Jerry Reynolds, "28. Monarch of All He Surveys," History of the Santa Clarita Valley, article #2081 in this archive.', $LEGACY('lw2725', '"The Beale Cut," as told by José Jesús López, from Frank F. Latta, Saga of Rancho El Tejon (1976), pp. 195-197, LW2725, with Leon Worden\'s notes'), $LEGACY('lw2225', 'Leon Worden, "John C. Fremont," LW2225'), $LEGACY('lw2159a', 'Leon Worden, "Tom Mix Jumps Over Beale\'s Cut (Or Not)," LW2159'), $LEGACY('carey-bunse-ranch', 'JRP Historical Consulting Services, "Harry Carey Ranch (Clougherty Ranch)," Historic American Buildings Survey No. CA-2712, for the National Park Service, 2001')],
];
$R[934] = ['title' => 'Newhall Pass interchange',
    'must' => ['lapuerta2023' => ['they traversed the mountains by way of an ancient road that their Native hosts had marked out for them', 'This was Camino Viejo, the "old road" through the area we know as the Newhall Pass', 'they laid a sapling or two across a narrow switchback in the road to bar it,[2] in order to stop the cattle of one ranch from wandering onto the other. This was La Puerta, "the door."', 'Today, La Puerta crowns the eastern slope of the 14 Freeway north of the Sierra Highway overpass', 'La Puerta demarcated the southeastern tip of his property'],
        'lw3158' => ['The westbound Interstate 210 overpass has fallen onto Interstate 5 in the Newhall Pass', 'whose actual epicenter was in the Iron Canyon section of Sand Canyon in the SCV'],
        5759 => ['Fallen freeway bridges in the Newhall Pass were reopened in record time', 'if they tried to get home to Santa Clarita from L.A. for several weeks and even months after the Northridge earthquake of Jan. 17, 1994']],
    'body' => [
        'The Newhall Pass is the gap through the mountains between the San Fernando and Santa Clarita valleys, where the freeways now cross. The road through it is far older than they are. The Portolá expedition came over it in August 1769 by an ancient road its Native hosts showed them, the Camino Viejo, and the Spanish later barred a narrow switchback in it to keep one rancho\'s cattle from straying onto another\'s. That bar, La Puerta, "the door," marked the southeastern tip of the Rancho San Francisco; its site crowns the eastern slope of the 14 freeway, north of the Sierra Highway overpass.[1]',
        'Earthquakes have twice brought down freeway bridges in the pass. In the San Fernando earthquake of February 9, 1971, whose epicenter was in Sand Canyon, the westbound Interstate 210 overpass fell onto Interstate 5.[2] After the Northridge earthquake of January 17, 1994, the fallen bridges were reopened in record time, but for weeks and even months the drive home to Santa Clarita from Los Angeles was a slow one.[3]',
    ],
    'notes' => [$LEGACY('lapuerta2023', 'Leon Worden, "La Puerta: Gateway to the Santa Clarita Valley," March 2023'), $LEGACY('lw3158', '"Collapsed Freeway Bridge in Newhall Pass, 1971 Earthquake," LW3158'), '"Freeway Traffic," 1994 Northridge earthquake, photograph #5759 in this archive.'],
];
$R[875] = ['title' => 'Northridge Earthquake',
    'must' => [5761 => ['Time: January 17, 1994 / 4:30:55 am PST', 'The Valencia Library sustained heavy damage in the Northridge earthquake of Jan. 17, 1994'],
        5745 => ['By Jan. 20, three days after the 1994 earthquake devastated the south and west sides of the Santa Clarita Valley, the Federal Emergency Management Agency (FEMA) had set up a claims intake center on the east side of town at Canyon Country Park'],
        5759 => ['Fallen freeway bridges in the Newhall Pass were reopened in record time'],
        5739 => ['At right is the home of city formation leader Connie Worden at 23924 Via Onda — also a total loss', 'Via Onda, a cul-de-sac in the Valencia Hills subdivision, was a mess'],
        5753 => ['Down the street, mobile homes burned down when the earthquake caused gas lines to rupture', 'in the Greenbrier mobile home park north of Soledad Canyon Road in western Canyon Country'],
        12652 => ['Ever since the loss of the Hart auditorium after the Northridge Earthquake of January, 1994'],
        5757 => ['the Newhall Boys & Girls Club gymasium was transformed into a temporary Red Cross shelter for local families displaced by the earthquake'],
        18726 => ['was mayor in 1994, the year of the Northridge earthquake. He was known as the city\'s "earthquake mayor."'],
        12611 => ['The city held the first annual festival months after the devastating Northridge earthquake of 1994. Finding its original festival site — the Hart High School auditorium — in shambles', 'The Veluzat family, owners of historic Melody Ranch, generously offered their property for the event']],
    'body' => [
        'The Northridge earthquake struck at 4:30 in the morning of January 17, 1994, and devastated the south and west sides of the Santa Clarita Valley.[1][2]',
        'Freeway bridges fell in the Newhall Pass.[3] On Via Onda, in the Valencia Hills subdivision, homes were lost, the cityhood leader Connie Worden\'s among them.[4] In the Greenbrier mobile home park in western Canyon Country, mobile homes burned when the earthquake ruptured gas lines.[5] The Valencia Library was heavily damaged, Hart High School lost its auditorium, and the Newhall Boys & Girls Club gymnasium became a Red Cross shelter for families who had lost their homes.[1][6][7] By January 20 the Federal Emergency Management Agency had a claims center at Canyon Country Park.[2]',
        'George Pederson, the mayor that year, became known as the city\'s "earthquake mayor."[8] Months later the city held its first Cowboy Festival at Melody Ranch, after the site it had planned on, the Hart High auditorium, was left in shambles.[9]',
    ],
    'notes' => ['"Valencia Library," 1994 Northridge earthquake, photograph #5761 in this archive.', '"FEMA Claims Intake Center, Canyon Country Park," 1994 Northridge earthquake, photograph #5745 in this archive.', '"Freeway Traffic," 1994 Northridge earthquake, photograph #5759 in this archive.', '"Cronan Home Site on Via Onda, Valencia Hills," 1994 Northridge earthquake, photograph #5739 in this archive.', '"Greenbrier Mobile Home Park Damage," 1994 Northridge earthquake, photograph #5753 in this archive.', '"It\'s showtime at our valley\'s high schools," article #12652 in this archive.', '"Red Cross Shelter at Newhall Park / Boys & Girls Club," 1994 Northridge earthquake, photograph #5757 in this archive.', 'George Pederson\'s profile, person #18726 in this archive, and its sources.', '"Cowboy Festival Gallops Into Newhall," article #12611 in this archive.'],
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
$applyLog('build_blank_records_2.php', $n, $short ? 'SHORT' : 'verified', 'the 25, batch 2: Rancho San Francisco, Lyons Station, Beale\'s Cut, Newhall Pass interchange, Northridge earthquake');
if ($short) { throw new \RuntimeException('build_blank_records_2: ' . implode(', ', $short)); }
