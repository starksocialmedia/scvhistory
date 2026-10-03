/**
 * The two Francisco Lópezes, settled (Nathan, 4 October 2026: "Retire #305,
 * list it in the removed-records registry, and redirect /persons/francisco-
 * lopez to the discoverer"; "Yes to a new record for Chico López. He passes on
 * his own"; "His portrait page noting it is often mistaken for the discoverer
 * is exactly the confusion #305 was built on, so say that on both records").
 * Also Rodolfo Acosta (#343) cut to a line ("A Walk of Western Stars
 * induction is an honour, not a connection, and being someone's father is not
 * either").
 *
 *   #305  "Juan José Francisco de Gracia ("Chico") Lopez", Jerry Reynolds's
 *         name, which joins the discoverer's uncle (Juan José Francisco), the
 *         discoverer (José Francisco de Gracia) and his cousin (Chico). Its
 *         withheld text is the discoverer's story under Chico's name. Nothing
 *         points at it. Deleted the ordinary way (Craft's trash, 30 days),
 *         listed in removed-claims.json, and its address redirected to the
 *         discoverer, #18834 (config/redirects.php, same commit).
 *   new   Francisco "Chico" López (about 1820-1900), at /persons/chico-lopez:
 *         the Elizabeth Lake stock ranch (la Laguna de Chico López), las
 *         montañas de Chico López, the Chicalopes, Chico Lopez Mountain.
 *   #18834 an editor note: not to be confused with Chico, whose portrait is
 *         often taken for the discoverer's.
 *   #343  Rodolfo Acosta: a line; dates narrowed to the years LW3309 gives,
 *         with a note giving the old values.
 * Every page was read from the Reggie mirror and matches its manifest
 * (batch4-, batch5-, batch6-sha.json).
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/retire_305_add_chico_lopez.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $F = "$root/inventory/legacy/fetched"; $els = Craft::$app->getElements(); $svc = Craft::$app->getEntries();
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{00A0}"], ["'", "'", '"', '"', ' '], html_entity_decode(strip_tags((string)$s), ENT_QUOTES))));
$VERIFIED = [];
foreach (['batch4', 'batch5', 'batch6'] as $b) { $VERIFIED += json_decode((string)@file_get_contents("$F/$b-sha.json"), true)['files'] ?? []; }
$read = function ($src) use ($ws, $F, $VERIFIED) {
    if (is_string($src)) { return isset($VERIFIED["$src.htm"]) && hash_file('sha256', "$F/$src.htm") === $VERIFIED["$src.htm"] ? $ws(@file_get_contents("$F/$src.txt")) : ''; }
    return $ws(Entry::find()->id($src)->status(null)->one()?->body);
};
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$LEGACY = fn($k, $title, $path = null) => "$title, as carried on SCVHistory.com, " . ($path ?? "/scvhistory/$k.htm") . '.';
$bad = [];
$check = function (string $who, array $must) use ($read, $ws, &$bad) {
    foreach ($must as $src => $ps) { $t = $read($src); foreach ($ps as $ph) { if (!str_contains($t, $ws($ph))) { $bad[] = "$who: " . (is_string($src) ? $src : "#$src") . ' does not read "' . mb_substr($ph, 0, 60) . '"'; } } }
};

/* ------------------------------------------------------------ Chico */
$CHICO = [
    'slug' => 'chico-lopez', 'fullName' => 'Francisco "Chico" López',
    'fields' => ['birthDate' => '1820', 'birthDateEdtf' => '1820', 'birthplace' => 'San Diego County, California', 'deathDate' => 'January 18, 1900', 'deathDateEdtf' => '1900-01-18', 'burialPlace' => 'Catholic Cemetery, Los Angeles', 'occupation' => 'Rancher; landowner', 'birthEvidence' => 'retrospective', 'deathEvidence' => 'contemporary', 'burialEvidence' => 'contemporary', 'personAliases' => "Chico López\nChico Lopez\nDon Chico López\nFrancisco Lopez"],
    'must' => [
        'hssc1929parks_chicolopez' => ['It was Mayordomo Pedro López who first showed these trails and cañons to his nephew Francisco, who while exploring in them afterwards, discovered gold in Placeritas cañon', 'in the rich little valley where Elizabeth Lake lies established his sítio de ganado mayor, or stock range', 'This was the handsome Francisco, known to everyone in the Southland as Don Chico López', 'His cattle ranged Antelope Valley where Lancaster and Palmdale are now', 'What we call Elizabeth Lake was to them la Laguna de Chico López, and the hills surrounding it were las montañas de Chico López', 'Don Francisco López, the discoverer of gold, was Don Chico\'s uncle', 'Chico was living at Paredon Blanco in Los Angeles and had his cattle at Rancho Rosa de Castilla', 'About 1850 his uncle took him into the mountains and showed him the laguna', 'He found a little spring, and near it built his adobe ranch house. Obtaining title to the land', 'during his absence, under the careless guardianship of his mayordomo, his herds dwindled mysteriously', 'His mayordomo was Tiburcio Vasquez\'s brother', '4,000 head of cattle in what is known today as Leona Valley', 'In the end he realized on the sale of only 800 out of his 4,000 cattle', 'At last the rancho itself went from him on a mortgage into the hands of Miguel Leonis', 'his ranch house, his barns, and corrals and implements were set fire to and deliberately burned', 'From the name of Leonis, who then acquired the property, comes the modern "Leona" Valley'],
        'al2001' => ['they were previously known as the Chicalopes, an anglicization of Francisco "Chico" Lopez', 'Lopez owned a large ranch in the latter half of the 1800s at Elizabeth Lake — which was called La Laguna de Chico Lopez before it was called Elizabeth Lake'],
        'hssc1928belderrain' => ['The author\'s father was Francisco "Chico" Lopez (1820-1900), a prominent Southern California landowner from whom is derived "Chicalopes," an early name for the Vasquez Rocks area', 'Chico Vasquez, the law-abiding brother of Tiburcio — was Lopez\'s ranch foreman', 'Chico Lopez was related to but should not be confused with the less well-to-do Francisco Lopez (b. 1802, d.?) who discovered gold in Placerita Canyon in 1842'],
        'perkins-part01' => ['high on the slopes of Chico Lopez Mountain at the head of Mint Canyon'],
        'lw3265' => ['Sometime after 1855 another settler took a chance. Chico Lopez moved in, bringing another Chico — last name Vasquez, said to be a brother of the notorious outlaw Tiburcio Vasquez — as foreman. [He was. —Ed.]'],
        'earle-mining-0103' => ['it has been suggested that Francisco Chari had settled in Bouquet (originally Buque) Canyon as early as 1843, having previously herded cattle in that area for Chico Lopez'],
        'us8502' => ['grandson of Claudio Lopez who came to mission San Gabriel as assistant to Father Salordio', 'Claudio Lopez\'s brother was was the father of 1842 gold discoverer Francisco Lopez ... so the man in the photo was a cousin of the gold discoverer', 'This photograph has often been wrongly identified as Francsico Lopez, the gold discoverer'],
        'obituary_franciscolopez' => ['Francisco Lopez, one of the oldest residents of Los Angeles, died late Thursday evening at the home of his daughter', 'in his eightieth year', 'The deceased was born in what is now San Diego county in 1820', 'his grandfather, Claudio Lopez, having come to this country during the last century with the priests who founded the San Gabriel Mission', 'Los Angeles Times | Sunday, January 21, 1900', 'the interment will be in the Catholic Cemetery', 'Francisco Lopez 1820 — January 18, 1900'],
        'sg030803' => ['a daughter of landowner Chico Lopez (1818-1900)'],
        853 => ['Juan José Francisco de Gracia ("Chico") Lopez'],
    ],
    'body' => [
        'Francisco "Chico" López left his name on the country north of the Santa Clarita Valley. Elizabeth Lake, where he kept a stock ranch, was la Laguna de Chico López, and the hills around it las montañas de Chico López; the rocks now named for Tiburcio Vasquez were once the Chicalopes, from his name; and A.B. Perkins knew a Chico Lopez Mountain at the head of Mint Canyon.[1][2][3][4]',
        'He lived at Paredon Blanco in Los Angeles and ran his cattle at Rancho Rosa de Castilla until, about 1850, his kinsman Francisco López, the gold discoverer, showed him the lake and advised him to take his stock there. He did, built an adobe ranch house by a spring and obtained title to the land, and his cattle and horses ranged the Antelope Valley and what is now Leona Valley.[1] His foreman was Chico Vasquez, a brother of the bandit Tiburcio.[1][3][5] It has also been suggested that Francisco Chari, who settled in Bouquet Canyon about 1843, had herded cattle there for him.[6]',
        'The ranch slipped away from him. Kept in Los Angeles most of the time, he saw his herds dwindle under his foreman\'s care and realized on only 800 of his 4,000 cattle; his ranch house and barns were burned in his absence; and the ranch went on a mortgage to Miguel Leonis, from whom Leona Valley takes its name.[1]',
        'He was a grandson of Claudio López, who came to Mission San Gabriel with its founding priests, and a cousin of the gold discoverer, although Marion Parks, writing in 1929, calls the discoverer his uncle.[7][8][1] The two Franciscos are often confused. A portrait of Chico in the California Historical Society\'s collection has often been identified as the discoverer, and Jerry Reynolds gives the discoverer Chico\'s nickname.[7][9] Born in what is now San Diego County about 1820, he died in Los Angeles in January 1900, in his eightieth year.[8]',
    ],
    'notes' => [
        $LEGACY('hssc1929parks_chicolopez', 'Marion Parks, "In Pursuit of Vanished Days," Historical Society of Southern California Annual, vol. 14, no. 2, 1929, the excerpt "La Laguna de Chico Lopez, aka Elizabeth Lake"'),
        $LEGACY('al2001', 'Leon Worden\'s note to "Vasquez Rocks Marketing Brochure, n.d. (~1930s)," AL2001'),
        $LEGACY('hssc1928belderrain', 'Leon Worden\'s note to Francisca Lopez de Belderrain, "The Awakening of Paredon Blanco Under a California Sun," Historical Society of Southern California Annual, 1928'),
        $LEGACY('perkins-part01', 'A.B. Perkins, "The Story of Our Valley," part 1, "Early Inhabitants"', '/scvhistory/signal/perkins/part01.html'),
        $LEGACY('lw3265', '"The Winged Monster of Elizabeth Lake," Old West, Fall 1969, LW3265, with Leon Worden\'s note'),
        $LEGACY('earle-mining-0103', 'David D. Earle, "Mining and Ranching in Soledad Canyon and Antelope Valley," January 8, 2003'),
        $LEGACY('us8502', '"Francisco \'\'Chico\'\' Lopez, ~1820-1900," US8502, a portrait in the California Historical Society collection'),
        $LEGACY('obituary_franciscolopez', '"Death of a Pioneer," Los Angeles Times, January 21, 1900'),
        'Jerry Reynolds, "Chapter 16. Golden Dreams," History of the Santa Clarita Valley, article #853 in this archive.',
    ],
    'notesAdd' => [['His birth year', 'His obituary gives 1820, and he died in his eightieth year; a later account gives 1818.'], ['Not to be confused with', 'Not to be confused with his kinsman Francisco López (born 1802), who discovered gold in Placerita Canyon in 1842, person #18834. A portrait of Chico is often taken for the discoverer\'s, and Jerry Reynolds gives the discoverer Chico\'s nickname.']],
];
$check('Chico', $CHICO['must']);
$chico = Entry::find()->section('persons')->slug($CHICO['slug'])->status(null)->one();
$cBody = implode("\n\n", $CHICO['body']);
preg_match_all('~\[(\d+)\]~', $cBody, $m); $used = array_unique(array_map('intval', $m[1]));
if (count($used) !== count($CHICO['notes']) || max($used) > count($CHICO['notes'])) { $bad[] = 'Chico: notes used ' . json_encode(array_values($used)) . ' of ' . count($CHICO['notes']); }
if (preg_match('~\x{2014}~u', $cBody . implode('', $CHICO['notes']) . json_encode($CHICO['notesAdd'], JSON_UNESCAPED_UNICODE))) { $bad[] = 'Chico: an em dash'; }
echo 'Chico López: ' . ($chico ? "held as #{$chico->id}" . (trim((string)$chico->body) === $cBody ? ', written' : ', body differs') : 'create at /persons/' . $CHICO['slug'] . ', ' . str_word_count($cBody) . ' words, ' . count($CHICO['notes']) . ' notes') . PHP_EOL;

/* ------------------------------------------------------------ the discoverer's note */
$disc = Entry::find()->id(18834)->status(null)->one();
$discNote = fn($cid) => ['Not to be confused with', 'Not to be confused with his kinsman Francisco "Chico" López (about 1820-1900), who kept a stock ranch at Elizabeth Lake, person #' . $cid . '. A portrait of Chico in the California Historical Society\'s collection is often taken for the discoverer\'s, and Jerry Reynolds gives the discoverer Chico\'s nickname.'];
if (!$disc || $disc->title !== 'Francisco Lopez') { $bad[] = '#18834 is not Francisco Lopez'; }
$discHas = $disc && (bool)array_filter($disc->editorNotes ?? [], fn($r) => is_array($r) && str_starts_with((string)($r['note'] ?? ''), 'Not to be confused with his kinsman Francisco "Chico"'));
echo 'Francisco Lopez #18834: ' . ($discHas ? 'note held' : 'add the note') . PHP_EOL;

/* ------------------------------------------------------------ #305 */
$r305 = Entry::find()->id(305)->status(null)->one();
$T305 = 'Juan José Francisco de Gracia ("Chico") Lopez';
if ($r305 && $r305->title !== $T305) { $bad[] = "#305 is {$r305->title}"; }
$in305 = $r305 ? Entry::find()->relatedTo(['targetElement' => $r305])->status(null)->count() : 0;
if ($in305) { $bad[] = "#305 has $in305 records pointing at it"; }
$REG = "$root/scripts/import/removed-claims.json"; $reg = json_decode(file_get_contents($REG), true);
$inReg = (bool)array_filter($reg['removedRecords'] ?? [], fn($x) => $x['record'] === 305);
$redir = str_contains(file_get_contents("$root/config/redirects.php"), "'persons/francisco-lopez'");
if (!$redir) { $bad[] = 'config/redirects.php has no rule for persons/francisco-lopez'; }
echo '#305: ' . ($r305 ? "live, $in305 inbound; to the trash" : 'already removed') . '; registry ' . ($inReg ? 'listed' : 'to add') . '; redirect ' . ($redir ? 'present' : 'MISSING') . PHP_EOL;

/* ------------------------------------------------------------ Rodolfo Acosta */
$RA = Entry::find()->id(343)->status(null)->one();
$check('Rodolfo Acosta', ['lw3309' => ['Mexican-American character actor Rodolfo Acosta (1920-1974), a posthumous honoree on the Newhall Walk of Western Stars and the father of Santa Clarita Councilman (later Assemblyman) Dante Acosta'], 'lw2102' => ['2013 Inductees: Rodolfo Acosta']]);
$raBody = 'Rodolfo Acosta (1920-1974), a Mexican-American character actor in Hollywood Westerns, was inducted into the Newhall Walk of Western Stars after his death, in 2013, and was the father of Dante Acosta, a Santa Clarita councilman and later assemblyman.[1][2] No source in this archive places him in the Santa Clarita Valley.';
$raNotes = [$LEGACY('lw3309', 'Leon Worden, "Harry Carey Jr. in \'The Raiders\' with Robert Culp, Brian Keith (Universal 1964)," LW3309'), $LEGACY('lw2102', '"Downtown Newhall Walk of Western Stars," LW2102')];
$raCorrect = ['birthDate' => ['July 29, 1920', '1920'], 'birthDateEdtf' => ['1920-07-29', '1920'], 'birthplace' => ['El Paso, Texas', ''], 'deathDate' => ['November 7, 1974', '1974'], 'deathDateEdtf' => ['1974-11-07', '1974']];
$raFields = ['birthEvidence' => 'retrospective', 'deathEvidence' => 'retrospective', 'burialEvidence' => 'uncited'];
$raNote = ['His dates', 'This record formerly gave his birth as July 29, 1920, in El Paso, Texas, and his death as November 7, 1974. The source here gives the years only. No source has been found for his burial place.'];
if (!$RA || $RA->title !== 'Rodolfo Acosta') { $bad[] = '#343 is not Rodolfo Acosta'; }
foreach ($raCorrect as $k => [$from, $to]) { $have = trim((string)$RA->getFieldValue($k)); if ($have !== $from && $have !== $to) { $bad[] = "Rodolfo Acosta: $k is \"$have\""; } }
$raDone = trim((string)$RA->body) === $raBody;
echo 'Rodolfo Acosta #343: ' . ($raDone ? 'already a line' : str_word_count(strip_tags((string)$RA->body)) . ' words -> ' . str_word_count($raBody)) . PHP_EOL;

echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }

$noteRows = fn($e) => array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
$short = [];

/* Chico */
if (!$chico) {
    $chico = new Entry(); $chico->sectionId = $svc->getSectionByHandle('persons')->id; $chico->setTypeId($svc->getEntryTypeByHandle('person')->id);
    $chico->slug = $CHICO['slug'];
}
if (trim((string)$chico->body) !== $cBody) {
    $chico->title = $CHICO['fullName'];
    $chico->setFieldValues(['fullName' => $CHICO['fullName']] + $CHICO['fields'] + ['body' => $cBody, 'footnotes' => $fn($CHICO['notes']), 'bodyAuthorship' => 'editorial-2026',
        'editorNotes' => array_map(fn($x) => ['heading' => $x[0], 'position' => 'bottom', 'note' => $x[1]], $CHICO['notesAdd']),
        'recordProvenance' => 'retire_305_add_chico_lopez.php, 4 Oct 2026: Nathan, "Yes to a new record for Chico López. He passes on his own"', 'legacyUrl' => '/scvhistory/us8502.htm']);
    if (!$els->saveElement($chico)) { $short[] = 'Chico ' . json_encode($chico->getFirstErrors()); }
}
/* the discoverer's note */
if ($chico->id && !$discHas) {
    $rows = $noteRows($disc); [$h, $n] = $discNote($chico->id); $rows[] = ['heading' => $h, 'position' => 'bottom', 'note' => $n];
    $disc->setFieldValue('editorNotes', $rows); if (!$els->saveElement($disc)) { $short[] = '#18834 ' . json_encode($disc->getFirstErrors()); }
}
/* #305 */
if (!$inReg) {
    $reg['removedRecords'][] = ['record' => 305, 'title' => $T305, 'section' => 'persons', 'why' => 'Jerry Reynolds\'s name for the gold discoverer, which joins three men: the discoverer\'s uncle (Juan José Francisco), the discoverer (José Francisco de Gracia) and his cousin Francisco "Chico" López. A record standing for two people is worse than none. The discoverer is #18834; Chico has his own record.', 'removed' => '2026-10-04', 'by' => 'scripts/import/retire_305_add_chico_lopez.php; /persons/francisco-lopez redirects to the discoverer (config/redirects.php)'];
    file_put_contents($REG, json_encode($reg, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
}
if ($r305 && !$short) { if (!$els->deleteElement($r305)) { $short[] = 'delete #305'; } }
/* Rodolfo Acosta */
if (!$raDone) {
    $vals = ['body' => $raBody, 'footnotes' => $fn($raNotes), 'bodyAuthorship' => 'editorial-2026'];
    foreach ($raCorrect as $k => [$from, $to]) { if (trim((string)$RA->getFieldValue($k)) !== $to) { $vals[$k] = $to; } }
    foreach ($raFields as $k => $v) { if (($RA->getFieldValue($k)->value ?? '') !== $v) { $vals[$k] = $v; } }
    $rows = $noteRows($RA); if (!in_array($raNote[1], array_column($rows, 'note'), true)) { $rows[] = ['heading' => $raNote[0], 'position' => 'bottom', 'note' => $raNote[1]]; $vals['editorNotes'] = $rows; }
    $RA->setFieldValues($vals); if (!$els->saveElement($RA)) { $short[] = '#343 ' . json_encode($RA->getFirstErrors()); }
}

/* read-back */
$c = Entry::find()->section('persons')->slug($CHICO['slug'])->status(null)->one();
$ok = [
    'Chico written' => $c && trim((string)$c->body) === $cBody && $c->title === $CHICO['fullName'],
    'discoverer note' => (bool)array_filter(Entry::find()->id(18834)->status(null)->one()->editorNotes ?? [], fn($r) => is_array($r) && str_contains((string)($r['note'] ?? ''), 'person #' . ($c->id ?? 0))),
    '#305 in trash' => !Entry::find()->id(305)->status(null)->exists() && Entry::find()->id(305)->status(null)->trashed()->exists(),
    '#305 in registry' => (bool)array_filter(json_decode(file_get_contents($REG), true)['removedRecords'], fn($x) => $x['record'] === 305),
    'Rodolfo a line' => trim((string)Entry::find()->id(343)->status(null)->one()->body) === $raBody,
];
foreach ($ok as $k => $v) { echo ($v ? 'OK    ' : 'SHORT ') . $k . PHP_EOL; if (!$v) { $short[] = $k; } }
if ($c) { echo '      ' . $c->title . ' #' . $c->id . ' ' . $c->url . PHP_EOL; }
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('retire_305_add_chico_lopez.php', count(array_filter($ok)), $short ? 'SHORT' : 'verified', '#305 to the trash and the registry; Chico López created; note on #18834; Rodolfo Acosta cut to a line');
if ($short) { throw new \RuntimeException('retire_305_add_chico_lopez: ' . implode(', ', $short)); }
