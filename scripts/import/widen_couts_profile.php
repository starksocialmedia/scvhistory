/**
 * Cave Johnson Couts (#323): widen the record from the one line it was cut to
 * (Nathan, 3 October 2026, approving the re-search: "widening Couts"; earlier the
 * same day: "his record may deserve more than the line we cut it to").
 *
 * WHY. fix_couts_tie.php (3 October, 09:51) added "That letter is his only tie to
 * this valley in the archive's sources ... and no source here places Couts in the
 * Santa Clarita Valley." The search behind it ran through the agent shell's grep,
 * which skips the mirror's latin-1 pages, and found 4 pages; a search that reads
 * every page finds 23 (inventory/review/no-source-notes-2026-10-03.md). What they
 * support, and all this profile now says:
 *   1. Leon Worden's caption on the Lummis "Home of Ramona" pages (HS3001, HS3003
 *      to HS3016): Couts's presence "incidentally was felt in the Santa Clarita
 *      Valley at one time or another." No date, no act. The caption names "Cave
 *      Couts Jr." while describing the father; said so plainly.
 *   2. Guajome against Camulos as the "Home of Ramona": Worden (HS3001, LW3758),
 *      Odell 1939 (#4743), the Camulos National Register nomination, Del Castillo
 *      1980. Jackson came in 1882-83, after the father's death in 1874, so the
 *      claims are his family's; said so.
 *   3. Indian slave labor at Guajome: Worden citing Akins and Bauer (2021). This
 *      answers the profile's own "does not yet cover" note, which now keeps only
 *      the trials (no source in the archive).
 *   4. The Historical Society's holdings: his portrait (hs0141) and his published
 *      journal, Hepah, California!.
 *   5. The 1852 letter and the cattle drives, as they stand.
 * The lead says what ties him here, and that it is thin. Every quotation is
 * checked against the page it comes from (_source_texts.php: byte copies of the
 * mirror pages with their hashes) or against the archive record it cites. The
 * search behind every remaining "no source" note on the record is written into
 * an editorNotes row (docs/PROFILES.md, "A note that says no source exists
 * records the search").
 *
 * Refuses unless the body and footnotes are exactly as read on 3 October (hashes
 * below). Nothing removed comes back: the new text is checked against
 * removed-claims.json. One transaction; read-back; apply log.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/widen_couts_profile.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$SRC = require "$root/scripts/import/_source_texts.php"; $ws = $SRC['ws'];
$bad = $SRC['bad'];
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$rows = fn($e) => array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));

$ID = 323; $p = $get($ID);
if (!$p || $p->title !== 'Cave Johnson Couts') { echo 'REFUSING: #323 is not Cave Johnson Couts' . PHP_EOL; return; }
$OLD_BODY_SHA = '01b306bb0545126e9c8f484c335da47c568f62f73c6005279b022664809b28d9';
$OLD_FN_SHA = '6f66d73db4016c4130affb8c6061789ba789a791390f10db693359fda7b04914';

$SMYTHE = 'William Ellsworth Smythe, History of San Diego, 1542-1908 (San Diego: History Co., 1907), pages 268-269, as published by the San Diego History Center, "Cave Johnson Couts (1821-1874)," https://sandiegohistory.org/archives/biographysubject/cjcouts/, read 1 October 2026. The Center corrects Smythe, who had Cave Johnson as Secretary of the Treasury.';
$BODY = implode("\n\n", [
    'Cave Johnson Couts was a San Diego County rancher and former army officer whose tie to the Santa Clarita Valley is real but thin. Leon Worden writes that his presence "incidentally was felt in the Santa Clarita Valley at one time or another," and says no more; no source in the archive gives a dated act of his in the valley.[1] Worden\'s caption names him "Cave Couts Jr." while describing the father, the Tennessee-born officer of this record. The son of the same name owned Guajome in the 1930s and was the one who told the story of Helen Hunt Jackson\'s visit there, so which of the two Worden means is not clear.[1][2]',
    'Couts drove cattle north to the Gold Rush markets. In the spring of 1852 he wrote to Abel Stearns: "The nest of thieves in the Santa Clara did all they knew how to make me loose [sic] a lot, but not all." Others fared worse; he heard that the thieves had taken about a hundred head of Forester\'s, fifty of José Antonio Arguello\'s, seventy of Machado\'s and all of Castro\'s. Jerry Reynolds quotes the letter in his history of this valley; the drive reached San Jose on July 12, and "the Santa Clara" may as well be the valley around San Jose, where it ended.[3]',
    'According to Reynolds, in the spring of 1849 Couts and his brother, William Blunt Couts, drove a herd of some seven hundred head, on consignment from Juan Bandini and John Temple, up the coast to San Jose, where Cave held out for twenty dollars a head, about ten times what cattle had fetched in the days of hides and tallow. The next year, Reynolds writes, 2,500 head were driven north over the present-day Newhall Pass to the Santa Clara River, some by a route Ygnacio del Valle suggested, and del Valle charged grazing fees on his rancho. Reynolds does not say whose herds those were. The figures are his, and the drive of 1849 itself went by the coast, not through this valley.[4]',
    'His Rancho Guajome, in San Diego County, was built about the same time the del Valles were building at Camulos, "the \'Home of Ramona\' at the west end of the historic Rancho San Francisco," and the two ranchos later contended for that name.[5] Helen Hunt Jackson visited Camulos in 1882 and San Diego in 1882 and 1883, and is thought to have been a houseguest of the Couts family in 1882.[1][6] Worden writes that the Couts claim for Guajome was in the end given up, at least partly because the del Valles and their friends, Charles Lummis among them, promoted Camulos; Richard Griswold del Castillo records that "Cave Couts, a local San Diego rancher, held that Ramona was really a Temecula Indian girl he had known, named Matutini."[1][7] Jackson\'s visits came eight years after Couts\'s death, so these were his family\'s claims, not his own.[1][8]',
    'A Tennessee-born army officer, he settled in San Diego County after the Mexican War, married into the Bandini family and became one of the wealthiest ranchers in Southern California.[8] William Smythe\'s History of San Diego (1907) says he was born near Springfield, Tennessee, on 11 November 1821, and that his uncle Cave Johnson, Postmaster General under President Polk, had him appointed to West Point, where he graduated in 1843. He served on the frontier until after the Mexican War and then at Los Angeles, San Luis Rey and San Diego from 1848 to 1851, and in 1849 he conducted the Whipple expedition to the Colorado River.[8]',
    'He married Ysidora Bandini, Juan Bandini\'s daughter, on 5 April 1851, and resigned from the army that October.[8] Reynolds tells of their meeting, Ysidora tumbling from a roof railing into his arms as his column passed; it is Reynolds\'s story, and no earlier source for it is in the archive.[4] Smythe records that Couts sat on San Diego County\'s first grand jury in September 1850 and was county judge in 1854, and that in 1853 he moved to the Guajome grant, a wedding gift to his wife from her brother-in-law, Abel Stearns.[8]',
    'Appointed sub-agent for the San Luis Rey Indians, he was, in Smythe\'s words, "able to secure all the cheap labor needed for the improvement of his property." Worden, citing Akins and Bauer (2021), is plainer: Couts was "a slaveholder from Tennessee" who "used Indian slave labor" to build the adobe at Guajome.[8][5] He bought the San Marcos, Buena Vista and La Jolla ranchos and government land, about 20,000 acres in all. Smythe says he entertained Helen Hunt Jackson while she was gathering material for Ramona, which cannot be so of Couts himself: he died at the Horton House in San Diego on 10 June 1874.[8][1]',
    'The Santa Clarita Valley Historical Society lists among its photographs item hs0141, a "Portrait of young soldier holding whip. Back of picture states "Cave J. Couts"," held as an electronic image only.[9] Its library holds his published journal, Hepah, California! (1961).[10]',
]);
$NOTES = [
    'Leon Worden, caption to the pages of Charles F. Lummis\'s The Home of Ramona (1888), HS3001 and HS3003 to HS3016, as carried on SCVHistory.com, /scvhistory/hs3001.htm: Couts, "whose presence incidentally was felt in the Santa Clarita Valley at one time or another," "ultimately gave up on his claim"; Jackson visited "Camulos during a trek through California in 1882, and San Diego in 1882 and 1883." The caption calls him "Cave Couts Jr., a slaveholder from Tennessee who arrived in 1851 and used Indian slave labor at his Rancho Guajome in San Diego County (Akins & Bauer 2021:141-142)."',
    'Odell, "About Helen Hunt Jackson\'s Visit to Rancho Camulos" (1939), record #4743 in this archive: the Guajome story "was first given by George Wharton James on the authority of no less a person than Mr. Cave Couts, son of Major Couts, and the present eighty-year-old owner of Guajome."',
    'Jerry Reynolds, "Chapter 21. Buttons and Bows," History of the Santa Clarita Valley, article #863 in this archive, quoting Couts\'s letter to Abel Stearns.',
    'Jerry Reynolds, History of the Santa Clarita Valley, web edition edited by Leon Worden for the SCV Historical Society, 1998, part 20, "An Eager Market," https://scvhistory.com/scvhistory/signal/reynolds/part20.html. Archive record: "Chapter 20. An Eager Market."',
    'Leon Worden, caption to "\'Ramona\'s Bedroom at Camulos,\' Souvenir Postcard, ~1915," LW3758, as carried on SCVHistory.com, /scvhistory/lw3758.htm: Camulos, "the \'Home of Ramona\' at the west end of the historic Rancho San Francisco (SCV)"; "Cave Couts, a slaveholder from Tennessee and brother-in-law of L.A. merchant Abel Stearns ... used Indian slave labor (Akins & Bauer 2021:141-142) to build the adobe at Guajome about the same time the Del Valles were building theirs at Camulos."',
    'Judith P. Triem and Mitch Stone, Rancho Camulos, National Register of Historic Places nomination, as carried on SCVHistory.com, /scvhistory/camulos-nrhp3.htm: "Jackson is thought to have been a houseguest of the Couts family in 1882, the same year she visited Camulos."',
    'Richard Griswold del Castillo, "The Del Valle Family and the Fantasy Heritage" (1980), as carried on SCVHistory.com, /scvhistory/delcastillo1980.htm.',
    $SMYTHE,
    'Santa Clarita Valley Historical Society, collection list of 14 November 2013, item hs0141, "Print, Photographic," "Electronic Image only, No original print," as carried on SCVHistory.com, /scvhistory/files/scvhs_collectionlist_20131114/.',
    'Santa Clarita Valley Historical Society, library holdings, 2012, page 9: "Couts, Cave Johnson. Hepah, California!: The Journal of Cave Johnson Couts from Monterey, Nuevo Leon, Mexico to Los [Angeles]," 1961, Arizona Pioneers\' Historical Society; as carried on SCVHistory.com, /scvhistory/files/scvhs_library_holdings_2012/.',
];
$BIO = 'Cave Johnson Couts (1821-1874) was a Tennessee-born army officer who became a San Diego County rancher. Leon Worden writes that his presence was felt in the Santa Clarita Valley at one time or another, and his Rancho Guajome was the rival of the del Valles\' Camulos as the "Home of Ramona."';

/* Every quotation, against the page it comes from. */
$QUOTE = [
    ['hs3001', 'whose presence incidentally was felt in the Santa Clarita Valley at one time or another'],
    ['hs3001', 'ultimately gave up on his claim'], ['hs3001', 'Cave Couts Jr., a slaveholder from Tennessee who arrived in 1851 and used Indian slave labor at his Rancho Guajome in San Diego County ( Akins & Bauer 2021 :141-142)'],
    ['hs3001', 'Camulos during a trek through California in 1882, and San Diego in 1882 and 1883'], ['hs3001', 'at least partly because of the efforts of the Del Valles and their associates, like Lummis, to boost Camulos'],
    ['lw2963', 'was first given by George Wharton James on the authority of no less a person than Mr. Cave Couts, son of Major Couts, and the present eighty-year-old owner of Guajome'],
    ['lw3758', "the \"Home of Ramona\" at the west end of the historic Rancho San Francisco (SCV)"], ['lw3758', 'Cave Couts, a slaveholder from Tennessee and brother-in-law of L.A. merchant Abel Stearns'],
    ['lw3758', 'used Indian slave labor ( Akins & Bauer 2021 :141-142) to build the adobe at Guajome about the same time the Del Valles were building theirs at Camulos'],
    ['camulos-nrhp3', 'Jackson is thought to have been a houseguest of the Couts family in 1882, the same year she visited Camulos'],
    ['delcastillo1980', 'Cave Couts, a local San Diego rancher, held that Ramona was really a Temecula Indian girl he had known, named Matutini'],
    ['scvhs-collectionlist-2013-p174', 'hs0141-Print, Photographic Fair Electronic Image only, No original print P Portrait of young soldier holding whip. Back of picture states "Cave J. Couts"'],
    ['scvhs-library-2012-p9', 'Couts, Cave Johnson Hepah, California!: The Journal of Cave Johnson Couts from Monterey, Nuevo Leon, Mexico to Los 1 1961 Arizona Pioneer\'s Historical Society'],
    ['reynolds-part21', 'The nest of thieves in the Santa Clara did all they knew how to make me loose [sic] a lot, but not all'],
    ['reynolds-part20', 'tumbling her into the arms of Tennessee-born Lieutenant Cave Johnson Couts'],
];
foreach (range(3, 16) as $n) { $QUOTE[] = [sprintf('hs30%02d', $n), 'whose presence incidentally was felt in the Santa Clarita Valley at one time or another']; }
foreach ($QUOTE as [$k, $q]) { $ok = $SRC['has']($k, $q); echo ($ok ? 'quote ok   ' : 'QUOTE MISSING ') . "$k: \"" . mb_substr($q, 0, 70) . '"' . PHP_EOL; if (!$ok) { $bad[] = "$k does not read \"" . mb_substr($q, 0, 60) . '"'; } }
foreach ([[4743, 'Mr. Cave Couts, son of Major Couts'], [863, 'The nest of thieves in the Santa Clara']] as [$rid, $q]) {
    $r = $get($rid); $ok = $r && str_contains($ws(strip_tags((string)$r->body . ' ' . (string)($r->getFieldLayout()->getFieldByHandle('photoCaptionExt') ? $r->photoCaptionExt : ''))), $ws($q));
    echo ($ok ? 'record ok  ' : 'RECORD MISSING ') . "#$rid: \"$q\"" . PHP_EOL; if (!$ok) { $bad[] = "#$rid does not read \"$q\""; }
}

/* The search behind each "no source" note on the record (docs/PROFILES.md). */
$SEARCHED = 'Searched 3 October 2026 by a Python script reading every one of the 84,849 text pages of the legacy SCVHistory.com site (its pages, the flipbook books\' text and their search files, each read as cp1252 so no page is skipped), and the archive\'s own records. "Couts", "Coutts", "Cave J." and "Cave Johnson" find 23 pages; "Guajome" 28 files. None gives a dated act of his in the valley (the 1852 letter\'s "Santa Clara" is undetermined). Nothing found for: a birth on 6 November 1821 or in Smith County (searched "Smith County", "November 6, 1821", "6 November 1821" on pages naming Couts); his burial place ("Campo Santo", "buried", "cemetery", "interred" within 250 characters of his name); his trials ("murder", "acquit", "trial", "indict" within 300 characters); an earlier source for the roof-railing story than Reynolds ("Ysidora" or "Isidora" within 300 characters of "railing" or "roof").';
$NOT_COVERED_OLD = 'Smythe\'s 1907 account is the only biography of Couts in the archive. It is an admiring one, and it says nothing of how Couts treated the Native workers whose labor he secured as sub-agent. Wikipedia, used here only as a finding aid, reports that he was tried on several charges, including murder, and acquitted. The archive holds no source for this yet, and the profile will say more when it does.';
$NOT_COVERED_NEW = 'Smythe\'s 1907 account is the only biography of Couts in the archive, and an admiring one; it says nothing of how he treated the Native workers whose labor he secured as sub-agent. Leon Worden, citing Akins and Bauer (2021), writes that Couts used Indian slave labor to build the adobe at Rancho Guajome, and the profile says so. Wikipedia, used here only as a finding aid, reports that he was tried on several charges, including murder, and acquitted. No source in the archive, searched 3 October 2026, speaks of the trials.';

$curBody = (string)$p->body; $curFn = $p->footnotes;
$done = trim($curBody) === trim($BODY);
if (!$done && hash('sha256', $curBody) !== $OLD_BODY_SHA) { $bad[] = 'the body is not the one read on 3 October (fix_couts_tie.php\'s text); refusing to replace it'; }
if (!$done && hash('sha256', json_encode($curFn)) !== $OLD_FN_SHA) { $bad[] = 'the footnotes are not the ones read on 3 October'; }
$R = $rows($p); $newRows = []; $nc = 0; $hasSearch = false;
foreach ($R as $r) {
    if ($r['note'] === $NOT_COVERED_OLD) { $r['note'] = $NOT_COVERED_NEW; $r['heading'] = 'Not yet covered'; $nc++; }
    elseif ($r['note'] === $NOT_COVERED_NEW) { $nc++; }
    if ($r['heading'] === 'How the archive was searched') { $hasSearch = true; if ($r['note'] !== $SEARCHED) { $bad[] = 'a different search note is already on the record'; } }
    $newRows[] = $r;
}
if ($nc !== 1) { $bad[] = 'the "does not yet cover" note is not exactly as read'; }
if (!$hasSearch) { $newRows[] = ['heading' => 'How the archive was searched', 'position' => 'bottom', 'note' => $SEARCHED]; }

/* Nothing removed comes back. */
$REG = json_decode((string)file_get_contents("$root/scripts/import/removed-claims.json"), true);
foreach ($REG['claims'] ?? [] as $c) { if (str_contains($ws($BODY . $BIO), $ws($c['claim']))) { $bad[] = 'a removed claim is in the new text: ' . mb_substr($c['claim'], 0, 60); } }
$all = $BODY . implode('', $NOTES) . $BIO . $SEARCHED . $NOT_COVERED_NEW;
if (preg_match('~\x{2014}|inventory/|\.json~u', $all)) { $bad[] = 'an em dash or a repository path in the text'; }
preg_match_all('~\[(\d+)\]~', $BODY, $m); $used = array_values(array_unique(array_map('intval', $m[1])));
if (count($used) !== count($NOTES) || max($used) !== count($NOTES)) { $bad[] = 'notes used ' . json_encode($used) . ' of ' . count($NOTES); }
$first = []; foreach ($m[1] as $x) { if (!in_array((int)$x, $first, true)) { $first[] = (int)$x; } }
if ($first !== range(1, count($NOTES))) { $bad[] = 'notes are not numbered in order of first use: ' . json_encode($first); }
$PROV = '; widen_couts_profile.php, 3 Oct 2026: widened (Worden HS3001, LW3758; Ramona rivalry; slave labor; SCVHS holdings)';
$prov = trim((string)$p->recordProvenance . (str_contains((string)$p->recordProvenance, 'widen_couts_profile.php') ? '' : $PROV));
if (mb_strlen($prov) > 255) { $bad[] = 'recordProvenance would be ' . mb_strlen($prov) . ' characters'; }

echo str_repeat('-', 78) . PHP_EOL . '#323 Cave Johnson Couts' . PHP_EOL;
echo 'BODY ' . ($done ? 'already written' : 'OLD:') . PHP_EOL;
if (!$done) { echo '  ' . str_replace("\n\n", PHP_EOL . '  ' . PHP_EOL . '  ', $curBody) . PHP_EOL . 'BODY NEW (' . str_word_count($BODY) . ' words):' . PHP_EOL; }
echo '  ' . str_replace("\n\n", PHP_EOL . '  ' . PHP_EOL . '  ', $BODY) . PHP_EOL;
echo 'FOOTNOTES OLD: ' . count($curFn) . ' -> NEW: ' . count($NOTES) . PHP_EOL; foreach ($NOTES as $i => $n) { echo '  [' . ($i + 1) . "] $n" . PHP_EOL; }
echo 'AUTHOR BIO OLD: ' . $p->authorBio . PHP_EOL . 'AUTHOR BIO NEW: ' . $BIO . PHP_EOL;
echo 'EDITOR NOTE "What this profile does not yet cover" OLD: ' . $NOT_COVERED_OLD . PHP_EOL . '  NEW ("Not yet covered"): ' . $NOT_COVERED_NEW . PHP_EOL;
echo 'EDITOR NOTE ' . ($hasSearch ? 'present' : 'ADD') . ' "How the archive was searched": ' . $SEARCHED . PHP_EOL;
echo 'PROVENANCE: ' . $prov . ' (' . mb_strlen($prov) . ' chars)' . PHP_EOL;
echo 'Mirror mounted for a live hash check: ' . ($SRC['mirror'] ? 'yes' : 'no (checked against the byte copies only)') . PHP_EOL;
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    $p = $get($ID);
    $p->setFieldValues(['body' => $BODY, 'footnotes' => $fn($NOTES), 'authorBio' => $BIO, 'editorNotes' => $newRows, 'recordProvenance' => $prov]);
    if (!Craft::$app->getElements()->saveElement($p)) { throw new \RuntimeException('#323: ' . json_encode($p->getFirstErrors())); }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$r = $get($ID);
$ok = trim((string)$r->body) === trim($BODY) && count($r->footnotes) === count($NOTES) && (bool)array_filter($r->editorNotes, fn($x) => ($x['note'] ?? '') === $SEARCHED);
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('widen_couts_profile.php', 1, $ok ? 'verified' : 'SHORT', 'Couts widened: Worden\'s "felt in the valley", Guajome and Camulos, slave labor, SCVHS holdings; search recorded');
if (!$ok) { throw new \RuntimeException('widen_couts_profile: read-back failed'); }
