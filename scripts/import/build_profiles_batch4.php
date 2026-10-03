/**
 * The 25: batch 2 (Nathan, 3 October 2026: "import and source the 26 in
 * batches of ten"; "Nadeau: settle it from 'Which is Which?' before writing").
 *
 * Seven profiles: Rémi Nadeau (#339), Francisco López (#18834), Edward
 * Fitzgerald Beale (#327), John C. Frémont (#307), Kit Carson (#315), Junípero
 * Serra (#299), Juan Crespí (#297). All but López replace a withheld WordPress
 * body, as Pico's did.
 *
 * NADEAU. "Remi Nadeau: Which is Which?" (SCVHistory.com, August 11, 2003, a
 * descendant's letter) says people confuse "my great-uncle Remi Nadeau and his
 * deer ranch" with "my great-great-grandfather Remi Nadeau, the teamster", the
 * deer-ranch owner's grandfather. Record #339 is the teamster (1821-1887) and
 * #18869 the grandson (1867-1941). The page settles which is which. No page
 * gives the middle name "Allen" that #339's title carried; it is retitled
 * Rémi Nadeau, as Henriette Nadeau writes him, and both records say why.
 *
 * LÓPEZ. Two men. The gold discoverer of March 9, 1842, born March 9, 1802
 * (his baptism at Mission San Gabriel), whom his grandnephew José Jesús López
 * calls Cusa and says "is not to be confused with Chico López"; and Chico,
 * about 1820-1900, a grandson of Claudio López and the discoverer's cousin
 * (US8502; the Los Angeles Times, January 21, 1900). Jerry Reynolds names the
 * discoverer "Juan José Francisco de Gracia ('Chico') Lopez", which joins
 * them; Alan Pollack names him José Francisco de Gracia Lopez. #18834,
 * "Francisco Lopez", is written as the discoverer, with the disagreement
 * shown. #305, titled with Reynolds's name, keeps its withheld text and gets a
 * note: which man it stands for waits on Nathan.
 *
 * Imported: the elder Nadeau's obituary (Los Angeles, January 1887). Other
 * legacy pages are cited where they are. Every page was read from the Reggie
 * mirror and matches its manifest (batch4-sha.json).
 *
 * Fields: dates and places the WordPress import supplied are kept where a
 * source confirms them (with evidence), narrowed to what a source says where
 * it says less (Crespí born 1721, Carson born 1809 in Kentucky, with a note),
 * and marked unsourced with a note where nothing confirms them. Crespí's
 * "named the Santa Clara River" is not repeated: it appears only in the
 * withheld WordPress place record.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_profiles_batch4.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $elements = Craft::$app->getElements(); $svc = Craft::$app->getEntries();
$F = "$root/inventory/legacy/fetched";
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{00A0}"], ["'", "'", '"', '"', ' '], html_entity_decode(strip_tags((string)$s), ENT_QUOTES))));
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$RIGHTS = '~SCVTV|SOLELY RESPONSIBLE|additional restrictions|Personal or Research use|ownership of any original|enable JavaScript|Click to enlarge|Click image|Download original|^SCVHistory\.com~';
$prose = function ($k, $from = null, $to = null) use ($F, $RIGHTS) {
    $t = explode('[ RETURN TO TOP ]', (string)@file_get_contents("$F/$k.txt"))[0];
    $L = array_values(array_filter(array_map('trim', explode("\n", $t)), fn($l) => str_word_count($l) >= 8 && !preg_match($RIGHTS, $l)));
    if ($from !== null) { $i = null; foreach ($L as $j => $l) { if (str_starts_with($l, $from)) { $i = $j; break; } } $L = $i === null ? [] : array_slice($L, $i); }
    if ($to !== null) { foreach ($L as $j => $l) { if (str_starts_with($l, $to)) { $L = array_slice($L, 0, $j); break; } } }
    return $L;
};
$txt = fn($k) => $ws(@file_get_contents("$F/$k.txt"));
$VERIFIED = json_decode((string)@file_get_contents("$F/batch4-sha.json"), true)['files'] ?? [];
$okFile = fn($name) => is_file("$F/$name") && isset($VERIFIED[$name]) && hash_file('sha256', "$F/$name") === $VERIFIED[$name];
$LEGACY = fn($k, $title) => "$title, as carried on SCVHistory.com, /scvhistory/$k.htm.";

$IMP = [
    'obituary_reminadeau1887' => ['section' => 'obituaries', 'type' => 'obituary', 'title' => 'Remi Nadeau, L.A. Freighter and Hotelier, 1821-1887', 'subject' => 339,
        'fields' => ['obitDateOfDeath' => 'January 15, 1887', 'publicationDetails' => 'Los Angeles newspaper, January 1887, with Leon Worden\'s note'], 'from' => '[Los Angeles] — Remi Nadeau', 'to' => null],
];

$P = [];
$P[339] = ['title' => ['Remi Allen Nadeau', 'Rémi Nadeau'], 'replaceBody' => true, 'fullName' => 'Rémi Nadeau', 'alias' => 'Remi Nadeau',
    'fields' => ['birthEvidence' => 'retrospective', 'deathEvidence' => 'contemporary', 'burialEvidence' => 'uncited'],
    'notesAdd' => [
        ['Which Remi Nadeau', 'This is Rémi Nadeau the freighter (1821-1887). His grandson of the same name (1867-1941), who kept a deer ranch in what is now Canyon Country, is person #18869. A descendant wrote to SCVHistory.com in 2003 ("Remi Nadeau: Which is Which?") that the two are often confused.'],
        ['His name', 'This record formerly called him Remi Allen Nadeau. No source for the middle name Allen has been found; the sources call him Remi or Rémi Nadeau.'],
        ['His burial place', 'No source has been found for his burial place.'],
    ],
    'must' => ['obituary_reminadeau1887' => ['died at his residence, corner of Fifth and Olive streets', 'was born near Quebec, Canada, 67 years ago', 'He settled in Minnesota, and followed his trade, that of a miller. Twenty-six years ago he came across the plains, pausing awhile in Salt Lake City, where he put up one or two mills', 'secured a loan of a few hundred dollars from Prudent Beaudry and with this embarked as a freighter between this city and Utah and Nevada', 'became owner of a farm of nearly 3600 acres near Florence', 'At the time of his death his vineyard covered 2400 acres, and was probably the largest in the world', 'in 1882 he began thereon the construction of the huge block now known as the Nadeau House', 'southwest corner of Spring and First streets', 'Nadeau was born Sept. 21, 1821, in Kamouraska, Quebec, Canada. His grandson of the same name owned property in present-day Canyon Country'],
        'henriettenadeau20171119_en' => ['emigrated to New Hampshire, where he married Martha Flanders-Frye in 1844', 'In December 1868, mining engineer Mortimer Belshaw and Victor Beaudry (Prudent\'s brother) hired Remi Nadeau to transport the ore to Los Angeles', 'He became full partner of the new Cerro Gordo Freighting Company at John Lang\'s spread in Soledad Canyon', 'The Cerro Gordo Freighting Company was dissolved in 1882', 'He died January 15, 1887'],
        'nadeau-clarification' => ['have confused my great-uncle Remi Nadeau and his deer ranch with my great-great-grandfather Remi Nadeau, the teamster']],
    'body' => [
        'Rémi Nadeau was the Los Angeles freighter whose mule teams hauled silver from the Cerro Gordo mines in the 1870s, and whose freighting company was formed at John Lang\'s place in Soledad Canyon.[1][2]',
        'Born in Kamouraska, Quebec, on September 21, 1821, he was a miller in New Hampshire, where he married Martha Flanders Frye in 1844, and in Minnesota. In 1861 he crossed the plains, building mills at Salt Lake City, and went on to Los Angeles.[1][2]',
        'With a few hundred dollars borrowed from Prudent Beaudry he went into freighting, first to Utah and Nevada. From December 1868 he hauled the silver and lead of Mortimer Belshaw and Victor Beaudry\'s Cerro Gordo mines down to Los Angeles, and in 1873 he became a full partner in the new Cerro Gordo Freighting Company, which ran until 1882.[1][2]',
        'With his freighting earnings he farmed nearly 3,600 acres near Florence, where his vineyard of 2,400 acres was said at his death to be the largest in the world, and built the Nadeau House at Spring and First streets, begun in 1882.[1]',
        'He died at his home at Fifth and Olive streets on January 15, 1887.[2][1] His grandson of the same name, who kept a deer ranch in what is now Canyon Country, was a different man; a descendant wrote in 2003 that the two were often confused.[3][1]',
    ],
    'notes' => ['"Remi Nadeau, A Prominent And Old-Time Citizen Passes Away," Los Angeles, January 1887, with Leon Worden\'s note, obituary #{OBIT:obituary_reminadeau1887} in this archive.', 'Henriette Nadeau, "Remi Nadeau (1821-1887): From Miller to Hotelier," Association of Nadeau d\'Amérique, November 19, 2017 (English translation), as carried on SCVHistory.com, /scvhistory/henriettenadeau20171119_en.htm.', $LEGACY('nadeau-clarification', '"Remi Nadeau: Which is Which?" a descendant\'s letter, August 11, 2003')],
];
$P[18869] = ['title' => ['Remi Nadeau'], 'notesOnly' => true,
    'notesAdd' => [['Which Remi Nadeau', 'This is the grandson. His grandfather, Rémi Nadeau the freighter (1821-1887), is person #339. A descendant wrote to SCVHistory.com in 2003 ("Remi Nadeau: Which is Which?") that the two are often confused.']],
    'must' => ['nadeau-clarification' => ['have confused my great-uncle Remi Nadeau and his deer ranch with my great-great-grandfather Remi Nadeau, the teamster', 'Grandpa Nadeau was the grandfather of the owner']]];
$P[305] = ['title' => ['Juan José Francisco de Gracia ("Chico") Lopez'], 'notesOnly' => true,
    'notesAdd' => [['Two men named Francisco López', 'Two men named Francisco López appear in this archive\'s sources. The gold discoverer of 1842 was born in 1802; his grandnephew calls him Cusa and says he "is not to be confused with Chico López." Chico López, about 1820-1900, was his cousin. Jerry Reynolds gives the discoverer\'s name as Juan José Francisco de Gracia ("Chico") Lopez, which joins the two, and this record was titled from him. The discoverer is person #18834. Which man this record stands for is not yet settled, and its text is withheld until it is.']],
    'must' => ['latta1976franciscolopez' => ['He is not to be confused with Chico López, whose first name also was Francisco. This Francisco\'s nickname was Cusa'], 'us8502' => ['the man in the photo was a cousin of the gold discoverer'], 853 => ['Juan José Francisco de Gracia ("Chico") Lopez']]];
$P[18834] = ['title' => ['Francisco Lopez'],
    'fields' => ['birthDate' => 'March 9, 1802', 'birthDateEdtf' => '1802-03-09', 'birthEvidence' => 'contemporary', 'occupation' => 'Soldier; ranch foreman; gold discoverer'],
    'must' => ['sgb03346' => ['Placerita gold discoverer Francisco Lopez was born March 9 , 1802', 'He discovered gold March 9 , 1842', 'Mission records show Francisco was baptized as a newborn at Mission San Gabriel', 'when he married Maria Antonia Felis', 'born at the Santa Barbara Presidio, where he was stationed as a soldier', 'Mission San Fernando, where Francisco\'s military commission had been transfered'],
        15294 => ['Lopez paused to rest with his two companions, Manuél Cota and Domingo Bermudez', '"I with my sheath knife," Lopez later recalled, "dug up some wild onions, and in the earth discovered a piece of gold, and, searching further, found some more."', 'Lopez and his brother, Pedro, the majordomo (foreman) at the Mission San Fernando, rode to Los Angeles to bring samples of their findings to the prominent merchant Abel Stear', 'petitioned the Mexican governor of California, Juan Batista Alvarado', 'Lopez\'s original petition resides in the National Archives in Washington, D.C.', 'It is the document that makes Lopez\'s discovery the first "documented" discovery of gold in California', 'An estimated 2,000 miners, primarily from Lopez\'s home state of Sonora, worked Placerita Canyon in the ensuing years', 'As the story goes, Lopez fell asleep in the shade of an oak tree and dreamed he was floating on a pool of gold'],
        26983 => ['The placer mines from which this gold was taken was first discovered by Francisco Lopez, a native of California, in the month of March, 1842, at a place called San Francisquito', 'Lopez with a companion, was out in search of some stray horses', 'November 22d, 1842, I sent by Alfred Robinson', 'twenty ounces'],
        'latta1976franciscolopez' => ['The man who first discovered the placer gold in Placerita Canyon was Francisco López', 'He is not to be confused with Chico López, whose first name also was Francisco. This Francisco\'s nickname was Cusa', 'the gold was not discovered until the onions were being washed'],
        853 => ['Juan José Francisco de Gracia ("Chico") Lopez', 'in search of half-wild range cattle'],
        'us8502' => ['the man in the photo was a cousin of the gold discoverer']],
    'body' => [
        'Francisco López made the first documented discovery of gold in California, in Placerita Canyon in March 1842.[1][2][3]',
        'He was born on March 9, 1802, and baptized as a newborn at Mission San Gabriel. He was a soldier at the Santa Barbara Presidio and then at Mission San Fernando, and married María Antonia Felis.[4]',
        'On March 9, 1842, his fortieth birthday, he and two companions, Manuel Cota and Domingo Bermudez, were out after strays; Jerry Reynolds says cattle, and Abel Stearns, who handled the gold, says horses. Pulling wild onions, López found gold in the earth around them.[1][2][5] With his brother Pedro, majordomo of Mission San Fernando, he took samples to Stearns in Los Angeles, and he and his companions petitioned Governor Juan Bautista Alvarado for the right to mine; the petition is in the National Archives. On November 22, 1842, Stearns sent twenty ounces of the gold to the mint at Philadelphia.[1][2]',
        'The story that he first dreamed of gold under an oak is legend. His grandnephew, José Jesús López, remembered him telling that the gold was found when the women washed the onions he brought in.[1][3] Some two thousand miners, most from Sonora, worked the canyon in the years after.[1]',
        'Reynolds calls the discoverer "Chico" López. His grandnephew says the discoverer\'s nickname was Cusa, and that he "is not to be confused with Chico López," a cousin who lived from about 1820 to 1900.[5][3][6]',
    ],
    'notes' => ['"California\'s REAL First Gold," article #15294 in this archive.', 'Abel Stearns, account of the discovery and of the gold he sent to the mint, in "Abel Stearns Tells of Lopez 1842 Gold Discovery; No Mention of Dream," August 27, 1885, document #26983 in this archive.', $LEGACY('latta1976franciscolopez', 'José Jesús López, his grandnephew, in Frank F. Latta, "Saga of Rancho El Tejon" (1976), p. 171, "Francisco Lopez Discovers Gold"'), $LEGACY('sgb03346', '"Baptismal Data: Francisco Lopez (Gold Discoverer)," from the Huntington Library\'s Early California Population Project database'), 'Jerry Reynolds, "Chapter 16. Golden Dreams," History of the Santa Clarita Valley, article #853 in this archive.', $LEGACY('us8502', '"Francisco \'\'Chico\'\' Lopez, ~1820-1900," US8502')],
];
$P[327] = ['title' => ['Edward Fitzgerald Beale'], 'replaceBody' => true,
    'fields' => ['birthEvidence' => 'retrospective', 'deathEvidence' => 'retrospective', 'burialEvidence' => 'uncited'],
    'notesAdd' => [['His burial place', 'No source has been found for his burial place.']],
    'must' => ['bealeafb' => ['Edward Fitzgerald Beale was born Feb. 4, 1822 in the District of Columbia', 'Beale graduated in 1842', 'Beale and two other men (his Delaware Indian servant and Kit', 'crept through the Mexican lines and made their way to San Diego for reinforcements. Their actions saved Kearny\'s soldiers', 'he crossed Mexico in disguise to bring', 'the federal government proof of California\'s gold', 'Beale resigned from the Navy in May 1851', 'appointed Beale Superintendent of Indian Affairs for California and Nevada', 'Beale took 25 camels, imported from Tunis', 'Beale Surveyor General of California and Nevada', 'Rancho Tejon, part of 270,000 acres he had acquired', 'died at Decatur House on April 22, 1893'],
        2081 => ['Beale\'s Cut', 'with the birth of Edward Fitzgerald "Ned" Beale', 'Beale resigned from the Navy in November 1852', 'General Beale bought Rancho La Liebre on August 8, 1855', 'Ranchos Castac (Castaic), Los Alamos Y Agua Caliente and Tejon', 'a total of 297,000 acres', 'Beale went before the Los Angeles Board of Supervisors, took over Pico\'s franchise and got five thousand dollars to do the work', 'Beale, Carson and two Delaware Indians made "The Long Crawl,"'],
        'pollack1114kitcarson' => ['Kearny sent a young Navy lieutenant, Edward F. Beale, along with Kit Carson and an Indian guide to sneak through the Mexican lines']],
    'body' => [
        'Edward Fitzgerald "Ned" Beale was a Navy officer, explorer and surveyor general whose name is on Beale\'s Cut, the gap through the Newhall Pass, and who gathered the ranchos that became the Tejon Ranch.[1][2]',
        'Born in Washington, D.C., on February 4, 1822, he graduated from the Naval School in 1842 and came to California in the war with Mexico. After the battle of San Pasqual in December 1846, he and Kit Carson, with one or two Indian companions, crept through the Mexican lines to San Diego for reinforcements, which saved General Stephen Kearny\'s force.[1][2][3]',
        'He carried dispatches across the continent several times, once crossing Mexico in disguise to bring Washington proof of California\'s gold, and resigned from the Navy in May 1851 by one account and November 1852 by Reynolds\'s. He was superintendent of Indian affairs for California and Nevada from 1853, took 25 camels on his survey of a wagon road to the Colorado River in 1857, and under Lincoln was surveyor general of California and Nevada.[1][2]',
        'For the valley he is the man of the cut. He took over Andrés Pico\'s franchise for the road over the pass and got five thousand dollars from the Los Angeles supervisors to do the work.[2] He bought Rancho La Liebre on August 8, 1855, and added Castaic, Los Alamos y Agua Caliente and the Tejon: 297,000 acres by Reynolds\'s count, 270,000 by the Air Force\'s biography.[2][1]',
        'He died at Decatur House in Washington on April 22, 1893.[1]',
    ],
    'notes' => [$LEGACY('bealeafb', '"Beale Air Force Base: Biography of Edward F. Beale"'), 'Jerry Reynolds, "28. Monarch of All He Surveys," History of the Santa Clarita Valley, article #2081 in this archive.', $LEGACY('pollack1114kitcarson', 'Alan Pollack, "Kit Carson, Ned Beale Cross Paths and Alter California\'s Future"')],
];
$P[307] = ['title' => ['John C. Frémont'], 'replaceBody' => true,
    'fields' => ['birthEvidence' => 'retrospective', 'deathEvidence' => 'retrospective', 'burialEvidence' => 'uncited'],
    'notesAdd' => [['His burial place', 'No source has been found for his burial place.']],
    'must' => ['lw2225' => ['Born Jan. 21, 1813 in Savannah, Ga.', 'joining the U.S. Topographical Corps', 'led three major expeditions to the Far West — in 1842, 1843-44 and 1845-47', 'On Jan. 9, 1847, Frémont and his 100-man "buckskin battalion" arrived at Castaic Junction from the north', 'probably stopped overnight at the Del Valle ranch home', 'The following night, the troops camped at the Newhall Pass', 'crossing the San Gabriels through Frémont Pass (later confused with Beale\'s Cut, which was about a quarter-mile to the west)', 'Pico handed over his sword to Frémont in what is known as the Capitulation of Cahuenga', 'Voted into the U.S. Senate in California\'s first election following statehood in 1850', 'the Republican Party\'s first presidential candidate in 1856', 'He got a job as Territorial Governor of Arizona, a post he held from 1878-83', 'He died July 13, 1890 in New York City'],
        857 => ['The press dubbed him "The Pathfinder," even though Kit Carson was his guide and found most of the paths', 'Frémont would leave his name on mountains, towns, and even the pass between the Santa Clarita and San Fernando valleys', 'arriving at Castaic Junction on the evening of January 9, 1847']],
    'body' => [
        'John Charles Frémont, the explorer the press called "the Pathfinder," led the American battalion that crossed the Santa Clarita Valley in January 1847 on its way to the end of the war in California, and the pass between the Santa Clarita and San Fernando valleys bears his name.[1][2]',
        'Born in Savannah, Georgia, on January 21, 1813, he joined the Army\'s Topographical Corps and led expeditions to the Far West in 1842, in 1843 and 1844, and from 1845 to 1847. Kit Carson was his guide, and found most of the paths.[1][2]',
        'On January 9, 1847, Frémont and his hundred-man "buckskin battalion" reached Castaic Junction from the north and probably stopped overnight at the del Valle ranch house. The next night they camped at the Newhall Pass, and the morning after they crossed the San Gabriels by the pass later named for him, a quarter-mile east of Beale\'s Cut, to meet Andrés Pico, who surrendered to him in the Capitulation of Cahuenga.[1][2]',
        'After the war he was elected to the United States Senate in California\'s first election after statehood, was the Republican Party\'s first presidential candidate in 1856, and was territorial governor of Arizona from 1878 to 1883. He died in New York City on July 13, 1890.[1]',
    ],
    'notes' => [$LEGACY('lw2225', 'Leon Worden, "John C. Fremont," LW2225'), 'Jerry Reynolds, "Chapter 18. The Pathfinder," History of the Santa Clarita Valley, article #857 in this archive.'],
];
$P[315] = ['title' => ['Christopher Houston Carson'], 'replaceBody' => true,
    'correct' => ['birthDate' => ['December 24, 1809', '1809'], 'birthDateEdtf' => [null, '1809'], 'birthplace' => ['Madison County, Kentucky', 'Kentucky']],
    'fields' => ['birthEvidence' => 'retrospective', 'deathEvidence' => 'uncited', 'burialEvidence' => 'uncited'],
    'notesAdd' => [['His dates', 'This record formerly gave his birth as December 24, 1809, in Madison County, Kentucky; the sources here give only 1809 and Kentucky. No source has been found for the day of his death, May 23, 1868, or for his burial place.']],
    'must' => ['pollack1114kitcarson' => ['Born in Kentucky in 1809, Kit Carson spent his early years near Franklin, Mo.', 'Using Taos as a base camp between 1829 and 1840, Carson roamed the West as a fur trapper and mountain man', 'Returning to Missouri in 1842, Carson had a chance meeting with the future Pathfinder, John C. Fremont, on a steamboat on the Missouri River. Fremont eventually hired him as a guide on his first Western expedition', 'Carson accompanied Fremont as a guide on his famous second and third expeditions', 'Kearny sent a young Navy lieutenant, Edward F. Beale, along with Kit Carson and an Indian guide to sneak through the Mexican lines', 'Carson and Beale abandoned their canteens and boots and proceeding barefoot'],
        2081 => ['Beale, Carson and two Delaware Indians made "The Long Crawl,"', 'Beale and Carson made seven hazardous transcontinental journeys carrying dispatches'],
        857 => ['even though Kit Carson was his guide and found most of the paths']],
    'body' => [
        'Christopher "Kit" Carson, the mountain man and guide, was John C. Frémont\'s scout on his expeditions to the Far West and, with Edward F. Beale, made the crawl through the Mexican lines at San Pasqual in 1846 that saved General Kearny\'s army.[1][2]',
        'Born in Kentucky in 1809, he grew up near Franklin, Missouri, and from 1829 to 1840 trapped furs across the West from Taos.[1] In 1842 he met Frémont on a Missouri River steamboat and was hired as guide for the first expedition; he went on the second and third. The press made Frémont "the Pathfinder," though Carson found most of the paths.[1][3]',
        'In December 1846, guiding General Stephen Kearny\'s dragoons into California, he and Beale, with one or two Indian companions, crept barefoot past the Mexican sentries at San Pasqual and went on to San Diego for reinforcements.[1][2] By Jerry Reynolds\'s count he and Beale made seven journeys across the continent with dispatches.[2]',
        'He is in this archive for his part with Beale and Frémont; no record here places him in the Santa Clarita Valley itself.',
    ],
    'notes' => [$LEGACY('pollack1114kitcarson', 'Alan Pollack, "Kit Carson, Ned Beale Cross Paths and Alter California\'s Future"'), 'Jerry Reynolds, "28. Monarch of All He Surveys," History of the Santa Clarita Valley, article #2081 in this archive.', 'Jerry Reynolds, "Chapter 18. The Pathfinder," History of the Santa Clarita Valley, article #857 in this archive.'],
];
$P[299] = ['title' => ['Junípero Serra'], 'replaceBody' => true,
    'fields' => ['birthEvidence' => 'retrospective', 'deathEvidence' => 'retrospective', 'burialEvidence' => 'uncited'],
    'notesAdd' => [['His burial place', 'No source in this archive gives his burial place.']],
    'must' => [3673 => ['Fr. Junípero Serra, born Miquel Joseph Serra on Nov. 24, 1713, in Petra, Majorca, Spain, was responsible for establishing the mission system in Alta California', 'He came to Mexico City in 1749', 'In 1768', 'he was appointed the leader of a band of Franciscan friars for the Indian missions in Baja California', 'We have no reason to believe Serra ever set foot in the Santa Clarita Valley. He stayed behind in San Diego with a leg injury when in 1769', 'They passed through the Santa Clarita Valley from Aug. 8-10 of that year', 'Serra set up Alta California\'s first mission at San Diego', 'Serra established the first nine of Alta California\'s 21 missions', 'His nearest to the SCV was also his last, San Buenaventura (Ventura) in 1782. (His successor, Fr. Fermín Lasuén, established the Mission San Fernando in 1797.)', 'Serra died Aug. 28, 1784, at Carmel. On Sept. 25, 1988, he was beatified'],
        'pollack0713crespi' => ['he attended philosophy and theology classes taught by Father Junipero Serra']],
    'body' => [
        'Father Junípero Serra founded the mission system of Alta California, though there is no reason to believe he ever set foot in the Santa Clarita Valley.[1]',
        'Born Miquel Joseph Serra in Petra, Majorca, on November 24, 1713, he taught philosophy and theology in Palma, where Juan Crespí was among his students, came to Mexico City in 1749, and in 1768 was put in charge of the Franciscans sent to the missions of Baja California.[1][2]',
        'When the Portolá expedition set out overland from San Diego for Monterey in 1769, Serra stayed behind with an injured leg and founded the first mission in Alta California at San Diego. The expedition passed through the Santa Clarita Valley from August 8 to 10 without him.[1]',
        'He founded the first nine of the twenty-one Alta California missions; the nearest to the valley, and his last, was San Buenaventura, in 1782. Mission San Fernando, which later governed the valley, was founded by his successor, Fermín Lasuén, in 1797.[1] He died at Carmel on August 28, 1784, and was beatified by Pope John Paul II on September 25, 1988.[1]',
    ],
    'notes' => ['Leon Worden, "Fr. Junipero Serra," LW2441a, photograph #3673 in this archive.', $LEGACY('pollack0713crespi', 'Alan Pollack, "Crespi\'s SCV Legacy"')],
];
$P[297] = ['title' => ['Juan Crespí'], 'replaceBody' => true,
    'correct' => ['birthDate' => ['March 1, 1721', '1721'], 'birthDateEdtf' => [null, '1721']],
    'fields' => ['birthEvidence' => 'retrospective', 'deathEvidence' => 'uncited', 'burialEvidence' => 'uncited'],
    'notesAdd' => [['His dates', 'This record formerly gave his birth as March 1, 1721; the source here gives the year only. No source has been found for the day of his death, January 1, 1782, or for his burial place.']],
    'must' => ['pollack0713crespi' => ['Born in 1721 in Palma de Mallorca, Spain, Crespí became a Franciscan priest at age 17', 'he attended philosophy and theology classes taught by Father Junipero Serra', 'Crespí followed them that September', 'Father Crespí was sent to, and placed in charge of, the Misión La Purísima Concepción de Cadegomó', 'the expedition led by Rivera y Moncada, with Crespí acting as one of two diarists, had arrived in San Diego', 'the land expedition left San Diego on July 14, 1769, with Portolá in command'],
        835 => ['The chief diarist of the Portolá expedition of 1769 was Father Juan Crespí', 'we must have encountered at least five hundred souls here', 'is known as the Paraje del Corrál (Pen Camp)', 'with a great many very large, round houses well-roofed with grass', 'Father Crespí traded tobacco for beads'],
        3673 => ['They passed through the Santa Clarita Valley from Aug. 8-10 of that year'],
        12532 => ['By Father Juan Crespí\'s own account, the peaceful Tataviam Indians of the Upper Santa Clara River Valley offered seeds and sweet preserves'],
        1436 => ['there exist the day-by-day diaries of Father Crespi, the Franciscan, and Constanso, lieutenant of Gaspar de Portola, to tell of the valley and its inhabitants as they saw them']],
    'body' => [
        'Father Juan Crespí was the chief diarist of the Portolá expedition of 1769, the Spanish party that crossed the Santa Clarita Valley in August 1769. His diary, with that of Miguel Costansó, is the first written account of the valley and its people.[2][5]',
        'Born in Palma, Majorca, in 1721, he became a Franciscan at seventeen and studied under Junípero Serra. He sailed for New Spain in 1749, served in the missions of the Sierra Gorda, and in 1768 was put in charge of the mission of La Purísima Concepción de Cadegomó in Baja California.[1]',
        'In 1769 he kept a diary of the march north from Baja California to San Diego, and then of Portolá\'s expedition from San Diego toward Monterey, which left on July 14 and crossed the valley from August 8 to 10.[1][3]',
        'At a village where two creeks met, which the soldiers called the Paraje del Corral, he reckoned they met at least five hundred people. He described "very large, round houses well-roofed with grass," traded tobacco for beads, and wrote that the Tataviam offered the party seeds and sweet preserves.[2][4]',
    ],
    'notes' => [$LEGACY('pollack0713crespi', 'Alan Pollack, "Crespi\'s SCV Legacy"'), 'Jerry Reynolds, "Chapter 8. The Feast," History of the Santa Clarita Valley, article #835 in this archive, quoting Crespí\'s diary.', 'Leon Worden, "Fr. Junipero Serra," LW2441a, photograph #3673 in this archive.', 'Leon Worden, "Latins Invade, Conquer Western SCV," August 28, 1996, article #12532 in this archive.', 'A.B. Perkins, "Manuscript: Colonization," article #1436 in this archive.'],
];

/* ================================================================ checks */
$bad = []; $held = [];
foreach (array_keys($VERIFIED) as $name) { if (!$okFile($name)) { $bad[] = "$name: missing or not as verified against the manifest"; } }
foreach ($IMP as $k => $c) {
    $rec = Entry::find()->section($c['section'])->status(null)->legacyKey($k)->one(); $held[$k] = $rec?->id;
    if (count($prose($k, $c['from'], $c['to'])) < 2) { $bad[] = "$k: body not found"; }
    echo str_pad($k, 30) . ($rec ? "held as #{$rec->id}" : "import as {$c['section']}: " . count($prose($k, $c['from'], $c['to'])) . ' paragraphs') . PHP_EOL;
}
$read = function ($src) use ($ws, $txt) {
    if (is_string($src)) { return $txt($src); }
    $e = Entry::find()->id($src)->status(null)->one(); if (!$e) { return ''; }
    $h = array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
    return $ws($e->body . ' ' . (in_array('photoCaptionExt', $h, true) ? $e->photoCaptionExt : ''));
};
$plan = [];
foreach ($P as $id => $c) {
    $p = Entry::find()->id($id)->status(null)->one();
    if (!$p || !in_array($p->title, $c['title'], true)) { $bad[] = "#$id is not " . $c['title'][0]; continue; }
    foreach ($c['must'] as $src => $phrases) { $t = $read($src); foreach ($phrases as $ph) { if (!str_contains($t, $ws($ph))) { $bad[] = "{$c['title'][0]}: " . (is_string($src) ? $src : "#$src") . ' does not read "' . mb_substr($ph, 0, 60) . '"'; } } }
    if (!empty($c['notesOnly'])) { $plan[$id] = ['done' => true]; echo str_pad($c['title'][0], 26) . "notes only\n"; continue; }
    $body = implode("\n\n", $c['body']); $cur = trim((string)$p->body); $done = $cur === trim($body);
    if ($cur !== '' && !$done && !(!empty($c['replaceBody']) && ($p->bodyAuthorship->value ?? '') === 'wordpress-import-unsourced')) { $bad[] = "{$c['title'][0]}: has a body this does not replace"; }
    foreach ($c['fields'] ?? [] as $k => $v) { $have = trim((string)($p->getFieldValue($k)->value ?? $p->getFieldValue($k))); if ($have !== '' && $have !== $v && $k !== 'occupation' && !str_ends_with($k, 'Evidence')) { $bad[] = "{$c['title'][0]}: $k is \"$have\", not \"$v\""; } }
    foreach ($c['correct'] ?? [] as $k => [$from, $to]) { $have = trim((string)$p->getFieldValue($k)); if ($have !== $to && $from !== null && $have !== $from) { $bad[] = "{$c['title'][0]}: $k is \"$have\", not \"$from\""; } }
    preg_match_all('~\[(\d+)\]~', $body, $m); $used = array_unique(array_map('intval', $m[1]));
    if (count($used) !== count($c['notes']) || max($used) > count($c['notes'])) { $bad[] = "{$c['title'][0]}: notes used " . json_encode(array_values($used)) . ' of ' . count($c['notes']); }
    if (preg_match('~\x{2014}~u', $body . implode('', $c['notes']) . json_encode($c['notesAdd'] ?? []))) { $bad[] = "{$c['title'][0]}: an em dash"; }
    $plan[$id] = ['body' => $body, 'done' => $done];
    echo str_pad($c['title'][0], 26) . ($done ? 'already written' : str_word_count($body) . ' words, ' . count($c['notes']) . ' notes') . PHP_EOL;
}
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

/* ================================================================ writes */
$short = []; $ids = [];
foreach ($IMP as $k => $c) {
    if ($held[$k]) { $ids[$k] = $held[$k]; continue; }
    $e = new Entry(); $e->sectionId = $svc->getSectionByHandle($c['section'])->id; $e->setTypeId($svc->getEntryTypeByHandle($c['type'])->id); $e->title = $c['title'];
    $h = array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
    $vals = ['body' => implode("\n\n", $prose($k, $c['from'], $c['to'])), 'legacyKey' => $k, 'legacyUrl' => "/scvhistory/$k.htm", 'sourcePath' => "https://scvhistory.com/scvhistory/$k.htm", 'obitSubject' => [$c['subject']], 'obitLegacyUrl' => "/scvhistory/$k.htm"] + $c['fields'];
    $e->setFieldValues(array_intersect_key($vals, array_flip($h)));
    if (!$elements->saveElement($e)) { throw new \RuntimeException("$k: " . json_encode($e->getFirstErrors())); }
    $ids[$k] = $e->id; echo "imported $k as {$c['section']} #{$e->id}" . PHP_EOL;
}
$n = 0;
$noteRows = fn($e) => array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
foreach ($P as $id => $c) {
    $p = Entry::find()->id($id)->status(null)->one();
    $h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
    $vals = [];
    if (!empty($c['notesAdd'])) { $rows = $noteRows($p); $add = false; foreach ($c['notesAdd'] as [$hd, $nt]) { if (!in_array($nt, array_column($rows, 'note'), true)) { $rows[] = ['heading' => $hd, 'position' => 'bottom', 'note' => $nt]; $add = true; } } if ($add) { $vals['editorNotes'] = $rows; } }
    if (empty($c['notesOnly'])) {
        foreach ($c['fields'] ?? [] as $k => $v) { $have = trim((string)($p->getFieldValue($k)->value ?? $p->getFieldValue($k))); if ($have === '' || ($k === 'occupation' && $have !== $v) || (str_ends_with($k, 'Evidence') && $have !== $v)) { $vals[$k] = $v; } }
        foreach ($c['correct'] ?? [] as $k => [$from, $to]) { if (trim((string)$p->getFieldValue($k)) !== $to) { $vals[$k] = $to; } }
        if (!empty($c['fullName']) && (string)$p->fullName !== $c['fullName']) { $vals['fullName'] = $c['fullName']; }
        if (!empty($c['alias'])) { $al = array_filter(array_map('trim', preg_split('~[;\n]~', (string)$p->personAliases))); if (!in_array($c['alias'], $al, true)) { $vals['personAliases'] = implode("\n", array_merge($al, [$c['alias']])); } }
        if (!$plan[$id]['done']) {
            $notes = array_map(fn($x) => preg_replace_callback('~\{OBIT:([\w-]+)\}~', fn($m) => (string)$ids[$m[1]], $x), $c['notes']);
            $vals += ['body' => $plan[$id]['body'], 'footnotes' => $fn($notes), 'bodyAuthorship' => 'editorial-2026'];
            $prov = trim((string)$p->recordProvenance . '; build_profiles_batch4.php, 4 Oct 2026', '; '); if (mb_strlen($prov) <= 255) { $vals['recordProvenance'] = $prov; }
        }
    }
    $vals = array_intersect_key($vals, array_flip($h));
    if ($vals) { $p->setFieldValues($vals); if (!$elements->saveElement($p)) { $short[] = "{$c['title'][0]} " . json_encode($p->getFirstErrors()); continue; } }
    $r = Entry::find()->id($id)->status(null)->one();
    $ok = !empty($c['notesOnly']) ? count(array_filter($c['notesAdd'], fn($x) => in_array($x[1], array_column($r->editorNotes ?? [], 'note'), true))) === count($c['notesAdd'])
        : (trim((string)$r->body) === trim($plan[$id]['body']) && ($r->bodyAuthorship->value ?? '') === 'editorial-2026');
    $ok ? $n++ : $short[] = $c['title'][0];
    echo ($ok ? 'OK    ' : 'SHORT ') . $r->title . ' ' . $r->url . PHP_EOL;
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : "OK: $n records") . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_profiles_batch4.php', $n, $short ? 'SHORT' : 'verified', 'the 25, batch 2: Nadeau (retitled from Which is Which?), López the discoverer, Beale, Frémont, Carson, Serra, Crespí; notes on #305 and #18869');
if ($short) { throw new \RuntimeException('build_profiles_batch4: ' . implode(', ', $short)); }
