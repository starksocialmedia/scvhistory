/**
 * Andrés Pico #317, and Pico Canyon #16163 and Pico Canyon Road #16248 as his
 * namesakes (Nathan, 1 October 2026). Replaces the unsourced WordPress body.
 *
 * THE SOURCES (the mirror's copies, each matching the manifest of 20 August 2026)
 *   [LW3359]   Leon Worden's San Pasqual page, with the state park's handout and
 *              the CSSDAR marker. It gives Pico three ranks; all three are named.
 *   [PERKINS]  A.B. Perkins, "History of Pico Canyon Oil Production," 1958 (#1440).
 *   [REYNOLDS] Part 18, "The Pathfinder" (#857): Cahuenga.
 *   [PUERTA]   Leon Worden, "La Puerta," March 2023: the Newhall Pass road.
 *   [LW3623]   Leon Worden's note on the San Emedio certificate: Pico's seepage oil.
 *   [WPA]      Lois Ann Woodward for the State Division of Parks, 1936: Pico's
 *              claim, "Canada Pico."
 *   [CN7701]   The program for Mentryville's dedication as a state landmark, 1977.
 *
 * THE NAME is explained three ways (the first claim, called Canada Pico; the
 * filers' canyons bearing their names; his trips for asphalt seepage), and all
 * three are given, in the profile and in Pico Canyon's namingNote. The 1855 or
 * 1856 oil-lamp story is given with Perkins's "may well be true" and Leon's "Or
 * maybe not." His birth, death, burial and legislative service have no source
 * in the archive yet: the fields stay, marked uncited, and the text leaves them
 * out.
 *
 * Needs add_named_for_fields.php. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_pico_profile.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $elements = Craft::$app->getElements(); $F = "$root/inventory/legacy/fetched";
$ws = fn($s) => preg_replace('~\s+~u', ' ', (string)$s);
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$ID = 317; $CANYON = 16163; $ROAD = 16248; $PERKINS = 1440; $REYNOLDS = 857;
$p = $get($ID);
$src = ['lw3359' => $ws(@file_get_contents("$F/lw3359.txt")), 'perkins' => $ws(strip_tags((string)$get($PERKINS)?->body)), 'reynolds' => $ws(strip_tags((string)$get($REYNOLDS)?->body)),
    'puerta' => $ws(@file_get_contents("$F/lapuerta2023.txt")), 'lw3623' => $ws(@file_get_contents("$F/lw3623.txt")), 'wpa' => $ws(@file_get_contents("$F/wpa_pioneerrefinery_1936.txt")), 'cn7701' => $ws(@file_get_contents("$F/cn7701.txt"))];
$bad = [];
if (!$p || $p->title !== 'Andrés Pico') { $bad[] = '#317 is not Andrés Pico'; }
if ($get($CANYON)?->title !== 'Pico Canyon' || $get($ROAD)?->title !== 'Pico Canyon Road') { $bad[] = 'the places are not who they should be'; }
if (!Craft::$app->getFields()->getFieldByHandle('namedFor')) { $bad[] = 'run add_named_for_fields.php first'; }
$MUST = [
    'lw3359' => ['Andrés Pico (as in Pico Canyon )', 'The Battle of San Pasqual took place on December 6, 1846 between the United States forces led by Brigadier General Stephen W. Kearny and Californios led by Major Andrés Pico', 'Captain Andrés Pico followed', 'under the command of Gen. Andrés Pico', 'On January 13, 1847, Andrés Pico signed the Articles of Capitulation at Cahuenga Pass', 'proved to be the bloodiest', 'near the present city of Escondido'],
    'perkins' => ['Andres Pico distilling illuminating oil at San Fernando Mission (then his home) in 1855. The story may well be true. [Or maybe not', 'Who filed? Gelcich\'s father-in-law [sic], Andres Pico, for one', 'Their filings covered adjacent canyons in the Pico hills which still bear their respective names'],
    'reynolds' => ['Andrés Pico met with Frémont\'s forces at the home of María Jesus Lopez de Felíz', 'Pico handed over his sword and ended the war in California'],
    'puerta' => ['By Leon Worden. March 2023', 'O.W. Childs, Andrés Pico, and Del Valle', 'In 1861 it awarded the job to the ex-Mexican General Andrés Pico, and in 1863 to his onetime battlefield adversary, Edward F. Beale', 'June 1858'],
    'lw3623' => ['in 1856, the ex-Mexican Army General Andres Pico, the canyon\'s namesake, was gathering up oil from natural seepages, distilling it and using it to illuminate his home — the ex-Mission San Fernando'],
    'wpa' => ['By Lois Ann Woodward', 'The first claim, which was General Pico\'s holding, was called Canada Pico'],
    'cn7701' => ['Newhall Woman\'s Club & Santa Clarita Valley Historical Society', 'October 8, 1977', 'Pico Canyon got its name from the fact that Andres Pico, owner of the Rancho San Fernando that occupied most of the San Fernando Valley, came into the canyon to get asphalt seepage for fuel'],
];
foreach ($MUST as $k => $phrases) { foreach ($phrases as $ph) { if (!str_contains($src[$k], $ws($ph))) { $bad[] = "$k does not read \"" . mb_substr($ph, 0, 70) . '"'; } } }

$BODY = implode("\n\n", [
    'Andrés Pico was a Californio military commander and landholder. The Santa Clarita Valley carries his name in Pico Canyon, where he was among the first to file oil claims in 1865.[1][2]',
    'In the Mexican-American War he led the Californio lancers who met General Stephen W. Kearny\'s dragoons at San Pasqual, near present-day Escondido, on 6 December 1846, in what the state park\'s account calls the bloodiest of the California battles. The sources gathered on that page make him a captain, a major and a general.[1] On 13 January 1847, at the home of María Jesus Lopez de Felíz near Cahuenga Pass, he surrendered to John C. Frémont and signed the articles of capitulation that ended the war in California.[1][3]',
    'He lived at the former Mission San Fernando, and the road north from it over the Newhall Pass concerned him. In 1858, when the Overland Mail Company was planning its stage route, he was among the Los Angeles men who lent the county money for repairs to the road; in 1861 the Board of Supervisors gave him the contract to lower its grade, and in 1863 the next contract went to Edward F. Beale, his opponent at San Pasqual.[4][2]',
    'His interest in oil came early. Leon Worden writes that in 1856 he was gathering oil from natural seepages and distilling it to light his home at the ex-mission; A.B. Perkins, writing in 1958, gives 1855 and says the story "may well be true," and Leon\'s note on that sentence adds, "Or maybe not."[5][2] When the first oil claims were filed in the hills north of the pass in 1865, Perkins names Pico among the filers.[2]',
    'How the canyon came by his name is told three ways. The State\'s landmark history of 1936 says the first claim staked there was General Pico\'s, "called Canada Pico." Perkins says the first filers\' claims covered canyons "which still bear their respective names." And the program for Mentryville\'s dedication as a state landmark in 1977 says the canyon got its name because Pico went there for asphalt seepage to use as fuel.[6][2][7]',
]);
$NOTES = [
    'Leon Worden, "Battle of San Pasqual, 1846: Pico Routs Kearny; Beale\'s Stealth Turns Tide," SCVHistory.com LW3359, https://scvhistory.com/scvhistory/lw3359.htm, with the San Pasqual Battlefield State Historic Park\'s handout and the state historical marker. Archive record #5203.',
    'A.B. Perkins, "History of Pico Canyon Oil Production," Historical Society of Southern California Quarterly, 1958, with Leon Worden\'s notes. Archive record: "History of Pico Canyon Oil Production."',
    'Jerry Reynolds, History of the Santa Clarita Valley, web edition 1998, part 18, "The Pathfinder." Archive record: "Chapter 18. The Pathfinder."',
    'Leon Worden, "La Puerta: Gateway to the Santa Clarita Valley," SCVHistory.com, March 2023, https://scvhistory.com/scvhistory/lapuerta2023.htm, citing the Los Angeles Star and the Board of Supervisors.',
    'Leon Worden, note on the San Emedio Petroleum Company stock certificate of 1865, SCVHistory.com LW3623, https://scvhistory.com/scvhistory/lw3623.htm.',
    'Lois Ann Woodward, "Pioneer Oil Refinery: Registered Landmark No. 172," California Historical Landmarks Series, State of California, Division of Parks, 1936, https://scvhistory.com/scvhistory/wpa_pioneerrefinery_1936.htm.',
    'Newhall Woman\'s Club and Santa Clarita Valley Historical Society, Mentryville: Pioneer Oil Town, program for the dedication of California Registered Historical Landmark No. 516-2, 8 October 1977, https://scvhistory.com/scvhistory/cn7701.htm.',
];
$BIO = 'Andrés Pico led the Californio lancers at the Battle of San Pasqual in 1846 and signed the capitulation at Cahuenga in 1847. Pico Canyon, where he was among the first to file oil claims in 1865, carries his name.';
$NAMING = "Three explanations, all given:\n- The first claim staked in the canyon was General Pico's, \"called Canada Pico\" (Lois Ann Woodward for the State Division of Parks, 1936).\n- The first filers' claims, Pico's among them in 1865, covered canyons \"which still bear their respective names\" (A.B. Perkins, 1958).\n- Pico \"came into the canyon to get asphalt seepage for fuel\" (program for Mentryville's dedication as a state landmark, 1977).";
$ROAD_NAMING = 'The road to Pico Canyon, which is named for Andrés Pico; see Pico Canyon for how the canyon got its name. A.B. Perkins called it "the Pico Road" in 1958.';
$EDITOR = [['heading' => 'Dates and burial', 'position' => 'bottom', 'note' => 'His dates of birth and death, his burial place and his later public offices came with the WordPress import and have no source in the archive yet; they are not repeated in the profile.']];
if (preg_match('~\x{2014}~u', $BODY . $BIO . $NAMING . $ROAD_NAMING . implode('', $NOTES) . json_encode($EDITOR, JSON_UNESCAPED_UNICODE))) { $bad[] = 'an em dash in the text'; }
if (!str_contains($src['perkins'], 'the Pico Road')) { $bad[] = 'Perkins does not say "the Pico Road"'; }
$cur = trim((string)$p?->body); $isOld = str_starts_with(trim(strip_tags($cur)), 'Andrés Pico was born in San Diego in 1810'); $isNew = $cur === trim($BODY);
if (!$isOld && !$isNew) { $bad[] = '#317\'s body has been edited since the WordPress import'; }
echo '#317 body: ' . ($isNew ? 'already the profile' : 'WordPress body -> sourced profile (' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes)') . '; ' . array_sum(array_map('count', $MUST)) . ' phrases checked' . PHP_EOL;
foreach ([$CANYON, $ROAD] as $pl) { $e = $get($pl); echo "#$pl {$e?->title}: namedFor " . (in_array($ID, $e?->namedFor->ids() ?? []) ? 'already Pico' : '-> Andrés Pico, with a naming note') . PHP_EOL; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }
$tx = Craft::$app->getDb()->beginTransaction();
try {
    if (!$isNew) {
        $s = $get($ID); $h = array_map(fn($f) => $f->handle, $s->getFieldLayout()->getCustomFields());
        $s->setFieldValues(array_intersect_key(['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'authorBio' => $BIO, 'birthEvidence' => 'uncited', 'deathEvidence' => 'uncited', 'burialEvidence' => 'uncited',
            'editorNotes' => array_merge(array_values(array_filter($s->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')), $EDITOR),
            'recordProvenance' => trim((string)$s->recordProvenance . '; build_pico_profile.php, 2 Oct 2026: sourced profile; namesake of Pico Canyon and the road', '; ')], array_flip($h)));
        if (!$elements->saveElement($s)) { throw new \RuntimeException('#317: ' . json_encode($s->getFirstErrors())); }
    }
    foreach ([$CANYON => $NAMING, $ROAD => $ROAD_NAMING] as $pl => $note) {
        $e = $get($pl);
        if (in_array($ID, $e->namedFor->ids()) && trim((string)$e->namingNote) === $note) { continue; }
        $e->setFieldValues(['namedFor' => [$ID], 'namingNote' => $note]);
        if (!$elements->saveElement($e)) { throw new \RuntimeException("#$pl: " . json_encode($e->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$ok = trim((string)$get($ID)->body) === trim($BODY) && in_array($ID, $get($CANYON)->namedFor->ids()) && in_array($ID, $get($ROAD)->namedFor->ids());
echo 'READ-BACK ' . ($ok ? 'OK: ' . $get($ID)->url . ' ' . $get($CANYON)->url . ' ' . $get($ROAD)->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_pico_profile.php', 3, $ok ? 'verified' : 'SHORT', 'Andrés Pico profile; Pico Canyon and Pico Canyon Road named for him, three explanations recorded');
if (!$ok) { throw new \RuntimeException('build_pico_profile: read-back failed'); }
