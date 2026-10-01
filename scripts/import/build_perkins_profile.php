/**
 * A.B. Perkins #333, the valley's first town historian (Nathan, 1 October 2026:
 * "his record is empty. Import and build from" five legacy pages). Perkins is a
 * source as much as a subject, so the profile says where his figures are
 * preferred, where they need checking, and that a point on which Perkins and
 * Reynolds agree is not two independent witnesses.
 *
 * THE SOURCES (passages in inventory/sources/arthur-b-perkins-2026-10-01.json)
 *   [AP0828] and [SG6002]  Leon Worden's captions to two photographs of him.
 *                Both become photograph records here, on the scans already in
 *                the archive (assets #14116 and #14859).
 *   [LAT]      Martha L. Willman, Los Angeles Times, 2 January 1977: the fullest
 *              account of his early life, most of it his own telling, and the
 *              reporter says it was hard to separate fact from fiction with
 *              him. Those parts are given as his. The document record waits for
 *              the mirror: Reggie disconnected on 1 October before the clipping
 *              could be imported.
 *   [LW2017], [LW2019]  Leon Worden's columns lw3113 (#4931) and lw3550 (#5399).
 *   [RSF]      Perkins, "Rancho San Francisco" (1957), for one of his dates.
 *
 * CONFLICTS KEPT VISIBLE: the 1919 arrival (to buy the Needham water company,
 * says the Times; to manage the Newhall Water Company, says the archive); the
 * birth year (1891 here, about 1888 by the Times's guess); the date of "The
 * Story of Our Valley" (1954 in the Times, 1954-55 in the page titles, 1962 in
 * the AP0828 caption).
 *
 * THE NAME: Leon Worden and the Times give Buckingham. "Burnett" came with the
 * WordPress import and no source has been found for it; it stays as an alias.
 * The title follows fullName.
 *
 * NOT DONE: the replacement portrait arthur-b-perkins-outstanding-citizen-
 * newhall-1964-1.jpg carries content credentials saying it was generated in
 * part with Adobe Firefly, so it does not enter the archive and the current
 * portrait stays.
 *
 * Replaces the body only while it is still the unsourced WordPress text, and the
 * recordDates only while every row is still one derived from that text.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_perkins_profile.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$layout = fn($e) => array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
$src = json_decode(file_get_contents("$root/inventory/sources/arthur-b-perkins-2026-10-01.json"), true)['sources'] ?? [];
$ID = 333; $SG_ASSET = 14859; $AP_ASSET = 14116; $RELATE = [3003, 4931, 5399];
$bad = [];
$p = $get($ID);
if (!$p || !in_array($p->title, ['Arthur Burnett Perkins', 'Arthur Buckingham Perkins'], true)) { $bad[] = '#333 is not Perkins'; }
foreach ([$SG_ASSET => 'sg6002.jpg', $AP_ASSET => 'ap0828.jpg'] as $aid => $fname) { $a = \craft\elements\Asset::find()->id($aid)->one(); if (!$a || $a->filename !== $fname) { $bad[] = "asset #$aid is not $fname"; } }
foreach ($RELATE as $rid) { $r = $get($rid); if (!$r || $r->section->handle !== 'photographs' || !str_contains($r->title, 'Perkins')) { $bad[] = "#$rid is not a Perkins photograph"; } }

/* Each claim's words, checked in the passage it is cited to. */
$MUST = [
    'ap0828' => ['Born in 1891', 'came to Newhall in 1919 to manage the fledgling Newhall Water Company, the forerunner of the Newhall County Water District', 'justice of the peace (the local judge) in the 1920s', 'organized Newhall\'s first Fourth of July Parades', 'sold real estate and built many homes', 'Perkins published a manuscript on the history of the Santa Clarita Valley in 1957', 'In 1962 he wrote a series of articles for The Signal newspaper', 'until his death in 1977', 'probably shows Perkins at Well No. 5'],
    'lw3113' => ['Arthur Buckingham Perkins was the Santa Clarita Valley\'s first "town historian."', 'Known as "A.B." or "Art" or "Perk,"', 'to run the then-private Newhall Water Co.', 'until his death in 1977', 'About 1,100 of them in total.', 'as the collection existed in 1963', 'Ted Lamkin had photographed', 'launched the SCVHistory.com archive 21 years ago', 'One day in 1996', 'Reynolds had assumed the mantle of (our second) town historian', 'Perkins gave his original collection to Reynolds', 'used it for several series of articles he penned in The Signal', 'came to photograph Perkins\' collection in the early 1960s'],
    'lw3550' => ['a young man of about 28 when he arrived in 1919', 'He collected local family photographs', 'The Signal and its onetime rival The Sentinel, as well as in academic journals', 'displaced from his home of 45 years on Lyons Avenue', 'approximately 300 volumes at the Valencia Library were first kept in January 1974', 'journals of the Historical Society of Southern California, some of which included articles that Perkins wrote', 'probably about the time the Santa Clarita Valley Historical Society incorporated in December 1975', 'the society\'s first curator, Jerry Reynolds', 'the 206 titles in the A.B. Perkins book collection', 'Special Collections reading room in the COC Library', '"Moving Experience," The Signal, December 14, 1970.'],
    'lat1977' => ['Los Angeles Times | Sunday, January 2, 1977', 'By Martha L. Willman', 'A.B. Perkins Room at the county library in Valencia', 'with an ice pick and brush on an amateur archeological search', 'a weekly serial published by the Newhall Signal from April 1 to Dec. 30, 1954', 'his listing of references is almost as long as the essays themselves', '(our guess is he\'s 88)', 'He was born in Bennington, Vt.', 'raised by maiden aunts after his mother died at a young age', 'childhood in Denver where he was sent to combat a respiratory problem', 'Middlebury College', 'University of Arizona', 'founded a newspaper, the Carara Obelisk', '"not having news, naturally I fabricated it"', 'coming to Saugus in 1919 to purchase the H. Clay Needham Water Co.', 'among the founders of service organizations and community projects including the Masonic Club and the Kiwanis', 'justice of the peace for a period during prohibition', 'frequently difficult to separate the fact from the fiction', 'Perkins: Story of Our Valley 1954-55'],
    'sg6002' => ['Photo by Hal Cooley, probably in 1960', '22508 6th Street, which he built in 1956', 'renting from Perkins until his death in 1977 and afterward from his heirs until 1986', 'Named "Perkins Court,"', '"red Signal buildings."'],
    'rsf1957' => ['June 12, 1841, Don Antonio del Valle died', 'Expediente Rancho San Francisco, Case 318, National Archives', 'Spanish Archives in Sacramento', 'deeds to undivided portions of Rancho San Francisco'],
];
foreach ($MUST as $k => $phrases) { foreach ($phrases as $ph) { if (!str_contains($src[$k]['passage'] ?? '', $ph)) { $bad[] = "$k does not read \"$ph\""; } } }

$BODY = implode("\n\n", [
    'Arthur Buckingham Perkins, known as "A.B.," "Art" or "Perk," came to Newhall in 1919 to run the Newhall Water Company and became the Santa Clarita Valley\'s first town historian: the collector of its photographs, papers and books, and the first to write its history at length.[1][2][5] He died in 1977.[1][2]',
    'Most of what is known of his early life comes from a profile in the Los Angeles Times in January 1977, and most of that from Perkins himself. By his account he was born in Bennington, Vermont, was raised by aunts after his mother died young, spent part of his childhood in Denver for his health, went to Middlebury College and finished his studies at the University of Arizona. He then went to the marble camps at Carrara, Nevada, and founded a newspaper there, the Carara Obelisk; of that paper he said, "not having news, naturally I fabricated it." The reporter warned that it was "frequently difficult to separate the fact from the fiction" in conversation with him.[3]',
    'The archive gives his birth year as 1891, which agrees with Leon Worden\'s "about 28" on his arrival in 1919.[1][5] He would not tell the Times his age; it guessed 88, which would put his birth about 1888.[3]',
    'He came in 1919. The Times says he came to Saugus to buy the H. Clay Needham Water Co.; the archive\'s own accounts say he came to manage, or to run, the then-private Newhall Water Company, the forerunner of the Newhall County Water District.[3][1][2] The two may both be true, and no deed or company record in the archive settles it. He was justice of the peace, the local judge, in the 1920s, during Prohibition; he built homes and sold real estate; and the archive credits him with organizing Newhall\'s first Fourth of July parades.[1][3] His own histories name him among the founders of the Masonic Club and the Kiwanis.[3] In 1956 he built office bungalows at 22508 Sixth Street, named Perkins Court, which The Signal rented from him until his death and from his heirs until 1986; they are better known as the red Signal buildings.[4] In 1970 the widening of Lyons Avenue displaced him from the house he had lived in for 45 years.[5]',
    'By the Times\'s account he began with an ice pick and a brush, digging for the valley\'s Native past, and went on to gather local family photographs, papers and books.[3][5] He wrote about the valley in The Signal, in its rival The Sentinel, and in the journals of the Historical Society of Southern California; the Times said his lists of references could run "almost as long as the essays themselves."[5][3] His history, "The Story of Our Valley," ran in The Signal, and its date is given three ways: a weekly serial from 1 April to 30 December 1954 in the Times, 1954 to 1955 in this archive\'s own page titles, and 1962 in the caption to his water company photograph, which also gives a manuscript history of 1957.[3][1]',
    'In the early 1960s Ted Lamkin photographed the pictures Perkins had collected; his roughly 1,100 negatives record the collection as it stood in 1963. They turned up in 1996 and were the set that launched this archive.[2] About 300 of Perkins\'s books went to the county\'s Valencia library, where their use was first recorded in January 1974; in 1977 his collection was housed in an A.B. Perkins Room there.[5][3] Shortly before his death, probably around the founding of the Santa Clarita Valley Historical Society in December 1975, he passed most of his photographs to the society\'s first curator, Jerry Reynolds.[5][2] The books are now in the Special Collections reading room at College of the Canyons: 206 titles in 2019.[5]',
    'Perkins is a source as much as a subject, and this archive reads him that way. He worked from land-case files, deeds and the Spanish archives, and listed his references, so where a later retelling departs from him on a figure, the archive prefers Perkins until an original is seen. His figures still need checking: his history of Rancho San Francisco (1957) dates Antonio del Valle\'s death 12 June 1841, where Jerry Reynolds gives 21 June.[6][7] And Reynolds, the valley\'s second town historian, worked from Perkins\'s photographs and cites his history of the rancho, so Perkins and Reynolds agreeing on a point are not two independent witnesses: a mistake of Perkins\'s can reach a reader through either.[2][7]',
]);
$NOTES = [
    'Caption to photograph AP0828, "Arthur B. Perkins, Newhall Water Co.," SCVHistory.com, https://scvhistory.com/scvhistory/ap0828.htm. Archive record: "Arthur B. Perkins, Newhall Water Co."',
    'Leon Worden, "Perkins-Lamkin SCV History Images Come Home," SCVHistory.com, 17 September 2017, https://scvhistory.com/scvhistory/lw3113.htm. Archive record #4931.',
    'Martha L. Willman, "History of Santa Clarita Uncovered by Inquisitive Man With an Ice Pick," Los Angeles Times, 2 January 1977, as transcribed with the clipping at https://scvhistory.com/scvhistory/lat19770102perkins.htm, whose page titles date "Story of Our Valley" 1954-55. Much of it is Perkins\'s own account of himself.',
    'Caption to photograph SG6002, "Historian A.B. Perkins at The Signal," photograph by Hal Cooley, probably 1960, Signal Photo Archive; SCVHistory.com, https://scvhistory.com/scvhistory/sg6002.htm. Archive record: "Historian A.B. Perkins at The Signal, about 1960."',
    'Leon Worden, "Who Knew? Perkins\' SCV History Books Still Available to Researchers," SCVHistory.com, 2 June 2019, https://scvhistory.com/scvhistory/lw3550.htm, citing "Moving Experience," The Signal, 14 December 1970, among others. Archive record #5399.',
    'A.B. Perkins, "Rancho San Francisco," 1957, as transcribed at https://scvhistory.com/scvhistory/perkins-rsf-1957.htm, with his notes 26 to 28.',
    'Jerry Reynolds, History of the Santa Clarita Valley, web edition edited by Leon Worden for the SCV Historical Society, 1998, part 15 (Antonio del Valle\'s death, 21 June 1841), and its bibliography, which lists Perkins\'s Rancho San Francisco; checked in the archive\'s review of Reynolds\'s sources, 2026.',
];
$EDITOR = [['heading' => 'Name', 'position' => 'bottom', 'note' => 'Leon Worden and the Los Angeles Times give his middle name as Buckingham. This record came from WordPress as Arthur Burnett Perkins; no source for Burnett has been found, and it is kept as an alias until one is.']];
$row = fn(string $printed, string $iso, string $gran, string $label): array => ['printed' => $printed, 'iso' => $iso . ' 00:00:00', 'granularity' => $gran, 'label' => $label, 'confirmed' => false];
$DATES = [
    $row('1891', '1891-01-01', 'year', 'born, per the AP0828 caption; the Los Angeles Times guessed about 1888'),
    $row('1919', '1919-01-01', 'year', 'came to the valley to run, or buy, its water company'),
    $row('April 1, 1954', '1954-04-01', 'day', '"The Story of Our Valley" began as a weekly serial in The Signal, per the Los Angeles Times'),
    $row('1956', '1956-01-01', 'year', 'built Perkins Court, 22508 Sixth Street'),
    $row('1970', '1970-01-01', 'year', 'displaced from his Lyons Avenue home by the street widening'),
    $row('1977', '1977-01-01', 'year', 'died'),
];
$PHOTOS = [
    'sg6002' => ['title' => 'Historian A.B. Perkins at The Signal, about 1960', 'asset' => $SG_ASSET, 'date' => 'probably 1960', 'edtf' => '1960?', 'credit' => 'Hal Cooley. Signal Photo Archive.', 'code' => 'SG6002'],
    'ap0828' => ['title' => 'Arthur B. Perkins, Newhall Water Co.', 'asset' => $AP_ASSET, 'date' => '', 'edtf' => '', 'credit' => '', 'code' => 'AP0828'],
];
if (preg_match('~\x{2014}~u', $BODY . implode('', $NOTES) . json_encode($EDITOR, JSON_UNESCAPED_UNICODE) . json_encode($DATES, JSON_UNESCAPED_UNICODE))) { $bad[] = 'an em dash in the text'; }

/* ------------------------------------------------ plan */
$OLD_START = 'Arthur B. "Perk" Perkins was born in 1891 and arrived in Newhall in 1919';
$curBody = trim((string)$p?->body);
$isOld = str_starts_with(trim(strip_tags($curBody)), $OLD_START); $isNew = $curBody === trim($BODY);
if (!$isOld && !$isNew) { $bad[] = '#333\'s body has been edited since the WordPress import'; }
$oldPlain = preg_replace('~\s+~', ' ', strip_tags($curBody));
$rows = array_values(array_filter($p?->recordDates ?? [], fn($r) => is_array($r) && trim((string)($r['printed'] ?? '')) !== ''));
$datesDerived = $rows && !array_filter($rows, fn($r) => !str_contains($oldPlain, trim(mb_substr(preg_replace('~\s+~', ' ', (string)$r['label']), 3, 60))));
$datesNew = array_column($rows, 'label') === array_column($DATES, 'label');
foreach ($PHOTOS as $k => $ph) {
    $have = Entry::find()->section('photographs')->status(null)->legacyKey($k)->one();
    echo ($have ? "#{$have->id} exists: " : 'create photograph: ') . $ph['title'] . " ({$ph['code']}), Leon's caption verbatim, scan asset #{$ph['asset']}, pictures #333" . PHP_EOL;
}
foreach ($RELATE as $rid) { $r = $get($rid); echo "#$rid {$r->title}: photoPeople " . (in_array($ID, $r->photoPeople->status(null)->ids()) ? 'already includes #333' : '+ #333') . PHP_EOL; }
echo '#333: title Arthur Burnett Perkins -> Arthur Buckingham Perkins (fullName), alias Arthur Burnett Perkins kept' . PHP_EOL;
echo '#333 body: ' . ($isNew ? 'already the sourced profile' : 'the unsourced WordPress body -> the sourced profile (' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes), bodyAuthorship editorial-2026') . '; every quoted phrase checked in its source (' . array_sum(array_map('count', $MUST)) . ')' . PHP_EOL;
echo '#333 recordDates: ' . ($datesNew ? 'already rebuilt' : ($datesDerived ? count($rows) . ' rows derived from the old body -> ' . count($DATES) . ' rows from the sources' : 'NOT all derived from the old body: kept')) . PHP_EOL;
echo 'Portrait: NOT swapped. arthur-b-perkins-outstanding-citizen-newhall-1964-1.jpg is Firefly-generated in part (C2PA manifest); the current portrait stays.' . PHP_EOL;
echo 'Held for the mirror: the Los Angeles Times clipping as a document (Reggie disconnected).' . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    foreach ($PHOTOS as $k => $ph) {
        if (Entry::find()->section('photographs')->status(null)->legacyKey($k)->exists()) { continue; }
        $e = new Entry(); $e->sectionId = $svc->getSectionByHandle('photographs')->id; $e->setTypeId($svc->getEntryTypeByHandle('photograph')->id); $e->title = $ph['title'];
        $paras = $src[$k]['paragraphs'];
        $caption = array_values(array_filter($paras, fn($t) => !str_starts_with($t, $ph['code'] . ':')));
        $creditRaw = current(array_filter($paras, fn($t) => str_starts_with($t, $ph['code'] . ':')));
        $vals = ['featuredImage' => [$ph['asset']], 'body' => implode("\n\n", $caption), 'photoDate' => $ph['date'], 'photoDateEdtf' => $ph['edtf'], 'photoCredit' => $ph['credit'],
            'photoSourceCode' => $ph['code'], 'creditRaw' => $creditRaw, 'creditDpi' => '9600', 'photoPeople' => [$ID],
            'legacyKey' => $k, 'legacyUrl' => "/scvhistory/$k.htm", 'sourcePath' => $src[$k]['url']];
        $e->setFieldValues(array_intersect_key($vals, array_flip($layout($e))));
        if (!$elements->saveElement($e)) { throw new \RuntimeException("$k: " . json_encode($e->getFirstErrors())); }
    }
    foreach ($RELATE as $rid) {
        $r = $get($rid); $ids = $r->photoPeople->status(null)->ids();
        if (in_array($ID, $ids)) { continue; }
        $r->setFieldValue('photoPeople', array_merge($ids, [$ID]));
        if (!$elements->saveElement($r)) { throw new \RuntimeException("#$rid: " . json_encode($r->getFirstErrors())); }
    }
    $s = $get($ID); $h = $layout($s); $vals = [];
    if (!$isNew) {
        $vals = ['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'fullName' => 'Arthur Buckingham Perkins',
            'personAliases' => 'Arthur Burnett Perkins; A.B. Perkins; Art Perkins; Perk', 'birthplace' => 'Bennington, Vermont',
            'birthEvidence' => 'retrospective', 'deathEvidence' => 'retrospective',
            'occupation' => 'Historian · Water company manager · Justice of the peace · Builder',
            'editorNotes' => array_merge(array_values(array_filter($s->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')), $EDITOR),
            'recordProvenance' => trim((string)$s->recordProvenance . '; build_perkins_profile.php, 1 Oct 2026: sourced profile (inventory/sources/arthur-b-perkins-2026-10-01.json); Burnett to Buckingham; replacement portrait refused, Firefly-generated in part', '; ')];
    }
    if ($datesDerived && !$datesNew) { $vals['recordDates'] = $DATES; }
    if ($vals) {
        $s->setFieldValues(array_intersect_key($vals, array_flip($h)));
        if (!$elements->saveElement($s)) { throw new \RuntimeException('#333: ' . json_encode($s->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }

$s = $get($ID); $short = [];
if (trim((string)$s->body) !== trim($BODY)) { $short[] = 'body'; }
if ($s->title !== 'Arthur Buckingham Perkins') { $short[] = 'title reads ' . $s->title; }
if ($s->slug !== 'arthur-b-perkins') { $short[] = 'slug moved to ' . $s->slug; }
foreach ($PHOTOS as $k => $ph) { $e = Entry::find()->section('photographs')->status(null)->legacyKey($k)->one(); if (!$e || !in_array($ID, $e->photoPeople->status(null)->ids()) || !$e->featuredImage->one()) { $short[] = "photograph $k"; } }
foreach ($RELATE as $rid) { if (!in_array($ID, $get($rid)->photoPeople->status(null)->ids())) { $short[] = "#$rid not related"; } }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: ' . $s->url) . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_perkins_profile.php', 1 + count($PHOTOS) + count($RELATE), $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'A.B. Perkins: sourced profile, two photograph records, three photographs related; portrait not swapped (Firefly)');
if ($short) { throw new \RuntimeException('build_perkins_profile: ' . implode('; ', $short)); }
