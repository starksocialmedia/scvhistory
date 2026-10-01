/**
 * Leon Worden #279: journalist, historian, president of SCVTV, and the editor
 * and much of the author of this archive (Nathan, 1 October 2026: "his record
 * matters more than most"). A living person: public life only, year of birth at
 * most, no family.
 *
 * The record came from WordPress with his birth month, his parents' names, his
 * baby book and his mother's civic career. All of it goes: the body is replaced
 * whole, the birth date is cut to the year (still uncited), and the birthplace,
 * which has no source, is cleared.
 *
 * THE SOURCES
 *   [MWOTY] The SCV Man & Woman of the Year Committee's page for him, 2015.
 *   [INDEX] "Selections From Leon Worden," his own index page, about 2008.
 *   [STF]   St. Francis Dam National Memorial Foundation, "Leon Worden
 *           Biography," 22 February 2020.
 *   These three are versions of one biography, very likely his own words. They
 *   are cited for what they say and the profile attributes them; they are not
 *   treated as independent of one another, and an editor note says so.
 *   [LW3113] His column of 17 September 2017, for how the archive began.
 *   [IMDB]  Forgotten Tragedy (2018), full credits.
 *   [ARCHIVE] The count of records naming him as author, checked at run time.
 *
 * The portrait is leon-worden-cowboy-hat.jpg, imported on Nathan's word (its
 * credential records Generate Fill and an upscale); his old portrait, asset #23,
 * moves to the record's images. The banner is registered separately.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_worden_profile.php'))"
 */

use craft\elements\{Entry, Asset};

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $elements = Craft::$app->getElements();
$ws = fn($s) => preg_replace('~\s+~u', ' ', (string)$s);
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$ID = 279; $FILE = 'leon-worden-cowboy-hat.jpg'; $SHA = 'a6a93601a3925549ba7e33771309a0096b008e8720b3f10107bbc8804cb043b2'; $AS = 'leon-worden-edited.jpg';
$p = Entry::find()->id($ID)->status(null)->one();
$j = json_decode((string)@file_get_contents("$root/inventory/sources/leon-worden-2026-10-01.json"), true)['sources'] ?? [];
$src = ['mwoty' => $ws(@file_get_contents("$root/inventory/legacy/fetched/worden-mwoty050115man.htm.txt")), 'index' => $ws(@file_get_contents("$root/inventory/legacy/fetched/worden-index.html.txt")),
    'stf' => $ws($j['stf']['passage'] ?? ''), 'imdb' => $ws($j['imdb']['passage'] ?? ''),
    'lw3113' => $ws(json_decode((string)@file_get_contents("$root/inventory/sources/arthur-b-perkins-2026-10-01.json"), true)['sources']['lw3113']['passage'] ?? '')];
$bad = [];
if (!$p || $p->title !== 'Leon Worden') { $bad[] = '#279 is not Leon Worden'; }
$MUST = [
    'mwoty' => ['Leon Worden, 2015 SCV Man of the Year', 'SCV Man & Woman of the Year Committee', 'president and CEO of SCVTV', 'An SCV resident since 1970, UCLA graduate and award-winning journalist', 'has produced local television programming since 2002', 'has served as president of the SCV Historical Society', 'Leon established the Old Town Newhall Gazette in 1994 and was editor of The Signal newspaper until 2007, when he left to prepare the reorganization of SCVTV', 'chief administrative officer of the Santa Clarita Public Television Authority', 'the Rancho Camulos Museum board', 'Coinage, the largest U.S. numismatic monthly'],
    'index' => ['Leon hosts half-hour interview programs, "Legacy" and "SCV Newsmaker of the Week."', '2008 WINNER James L. Miller Memorial Award: Best Numismatic Article in Any Medium, Worldwide', "Mr. Brenner's Lincoln: Forgotten Figures (Part 2, December 2007)", 'Leon was Senior Editor of The Signal newspaper until 2007', 'founding director of the Friends of Mentryville'],
    'stf' => ['Associated Press, California Newspaper Publishers Association, National Newspaper Association, Numismatic Literary Guild', 'chairman of the City of Santa Clarita’s Newhall Redevelopment Committee', 'founding member of the Los Angeles County Small Business Commission and the county’s Ad-Hoc Committee on Homeless Services (SCV)', 'founding member of SCV Habitat for Heroes', 'co-creator (1989) of the SCV Sheriff Station’s Haunted Jailhouse', 'created the SCVHistory.com archive in 1996', 'put The Signal newspaper online in 1998', 'Measure SA Bond Oversight Committee', 'Pukúu Cultural Community Services board (Fernandeño-Tataviam Band of Mission Indians)', 'vice president of the SCV Historical Society board, chair of the Rancho Camulos Museum board', 'president of the Santa Clarita Valley Fourth of July Parade committee', 'Leon was named the Santa Clarita Valley Man of the Year in 2015', 'provides multimedia services to all education and government agencies and to the 120-plus nonprofit organizations'],
    'imdb' => ['Forgotten Tragedy: The Story of the St. Francis Dam (2018)', 'Director: Jesse Cash', 'Leon Worden, Self (archive footage)'],
    'lw3113' => ['One day in 1996, a box showed up on the doorstep of Ruth Newhall', 'About 1,100 of them in total.', 'launched the SCVHistory.com archive 21 years ago'],
];
foreach ($MUST as $k => $phrases) { foreach ($phrases as $ph) { if (!str_contains($src[$k], $ws($ph))) { $bad[] = "$k does not read \"$ph\""; } } }
$wrote = (int)(new \craft\db\Query())->from(['r' => '{{%relations}}'])->innerJoin(['el' => '{{%elements}}'], 'el.id = r.sourceId')->innerJoin(['f' => '{{%fields}}'], 'f.id = r.fieldId')
    ->where(['r.targetId' => $ID, 'f.handle' => 'writtenBy', 'el.revisionId' => null, 'el.draftId' => null, 'el.dateDeleted' => null])->count('DISTINCT r.sourceId');
if ($wrote < 200) { $bad[] = "only $wrote records name him as author; the text says more than two hundred"; }

$BODY = implode("\n\n", [
    'Leon Worden is a Santa Clarita Valley journalist, historian and broadcaster: president and chief executive of SCVTV, the valley\'s nonprofit public television service; editor of The Signal until 2007; and the founder and editor of SCVHistory.com, this archive.[1][3] He was named Santa Clarita Valley Man of the Year in 2015, and has lived in the valley since 1970.[1][3]',
    'His published biography, which appears in much the same words on his own index page, on the Man of the Year page of 2015 and on the St. Francis Dam National Memorial Foundation\'s site in 2020, describes him as a UCLA graduate and an award-winning journalist, with awards from the Associated Press, the California Newspaper Publishers Association, the National Newspaper Association and the Numismatic Literary Guild.[1][2][3] He founded the Old Town Newhall Gazette in 1994, put The Signal online in 1998 with the paper\'s IT manager, and edited The Signal until 2007, when he left to prepare the reorganization of SCVTV.[1][3] Writing for COINage, the largest numismatic monthly in the country, he won the James L. Miller Memorial Award for the best numismatic article in any medium, worldwide, in 2008, for "Mr. Brenner\'s Lincoln: Forgotten Figures," the second part of a series in COINage, December 2007.[2]',
    'He has produced local television since 2002. SCVTV runs the valley\'s public television channels and provides media services to its schools, government agencies and more than 120 nonprofit organizations; he is its president and chief executive, and chief administrative officer of the Santa Clarita Public Television Authority.[1][3] On SCVTV he has hosted the interview programs "Legacy" and "SCV Newsmaker of the Week."[2] He appears, in archive footage, in Jesse Cash\'s documentary Forgotten Tragedy: The Story of the St. Francis Dam (2018).[6]',
    'He created this archive in 1996. That year a box of about 1,100 negatives, Ted Lamkin\'s copies of the A.B. Perkins photograph collection, reached him through Ruth Newhall, and they became its first images.[4][3] He is its editor and the author of much of it: more than two hundred of its records name him as their author.[5] He has been president of the Santa Clarita Valley Historical Society and, by 2020, its vice president; he chairs the board of the Rancho Camulos Museum; and he was a founding director of the Friends of Mentryville.[1][2][3]',
    'His biography lists, among his civic service, the chairmanship of the City\'s Newhall Redevelopment Committee; founding membership of the Los Angeles County Small Business Commission, the county\'s Ad-Hoc Committee on Homeless Services for the valley, and SCV Habitat for Heroes; the co-creation, in 1989, of the Sheriff\'s Station\'s Haunted Jailhouse; and leadership of the Santa Clarita Valley Fourth of July Parade. By 2020 it added the Hart district\'s Measure SA Bond Oversight Committee and the board of Pukúu Cultural Community Services of the Fernandeño Tataviam Band of Mission Indians.[1][3]',
]);
$NOTES = [
    'SCV Man & Woman of the Year Committee, "Leon Worden, 2015 SCV Man of the Year," SCVHistory.com, https://scvhistory.com/scvhistory/mwoty050115man.htm, with video by Desiree Ramos for SCVTV, 1 May 2015.',
    '"Selections From Leon Worden," his index page on SCVHistory.com, https://scvhistory.com/scvhistory/signal/worden/, about 2008.',
    'St. Francis Dam National Memorial Foundation, "Leon Worden Biography," 22 February 2020, https://stfrancisdammemorial.org/leon-worden-biography/, read 1 October 2026.',
    'Leon Worden, "Perkins-Lamkin SCV History Images Come Home," SCVHistory.com, 17 September 2017, https://scvhistory.com/scvhistory/lw3113.htm. Archive record #4931.',
    'The archive\'s own records, counted 1 October 2026.',
    'Forgotten Tragedy: The Story of the St. Francis Dam (2018), directed by Jesse Cash, full credits at IMDb, https://www.imdb.com/title/tt6210054/fullcredits/, read 1 October 2026.',
];
$EDITOR = [['heading' => 'The sources', 'position' => 'bottom', 'note' => 'Notes 1, 2 and 3 are versions of one biography, very likely in Leon Worden\'s own words, published in 2008, 2015 and 2020. They are cited for what each says, and the profile gives their claims as his account; they do not confirm one another. The Man of the Year honor is the committee\'s.']];
$row = fn(string $printed, string $iso, string $gran, string $label): array => ['printed' => $printed, 'iso' => $iso . ' 00:00:00', 'granularity' => $gran, 'label' => $label, 'confirmed' => false];
$DATES = [$row('1994', '1994-01-01', 'year', 'founds the Old Town Newhall Gazette'), $row('1996', '1996-01-01', 'year', 'creates the SCVHistory.com archive'),
    $row('2007', '2007-01-01', 'year', 'leaves The Signal to prepare the reorganization of SCVTV'), $row('2015', '2015-01-01', 'year', 'named Santa Clarita Valley Man of the Year')];
if (preg_match('~\x{2014}~u', $BODY . implode('', $NOTES) . json_encode([$EDITOR, $DATES], JSON_UNESCAPED_UNICODE))) { $bad[] = 'an em dash in the text'; }
if (preg_match('~\b(Constance|Harvey|baby book|Telstar|his mother|his father|Connie)\b~i', $BODY)) { $bad[] = 'family detail in the body'; }
$cur = trim((string)$p?->body); $isOld = str_starts_with(trim(strip_tags($cur)), 'Early Life & Beginnings'); $isNew = $cur === trim($BODY);
if (!$isOld && !$isNew) { $bad[] = '#279\'s body has been edited since the WordPress import'; }
$path = "$root/inventory/incoming/$FILE";
if (!is_file($path) || hash_file('sha256', $path) !== $SHA) { $bad[] = "$FILE is missing or not the file received"; }
$have = Asset::find()->filename($AS)->one(); $port = $p?->featuredImage->one();
echo '#279 body: ' . ($isNew ? 'already the profile' : 'the WordPress body (birth month, parents, baby book) -> the public-life profile (' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes)') . "; $wrote records name him as author; " . array_sum(array_map('count', $MUST)) . ' phrases checked' . PHP_EOL;
echo '#279 birthDate "' . $p?->birthDate . '" -> "1962" (uncited); birthplace "' . $p?->birthplace . '" -> cleared; recordDates -> ' . count($DATES) . ' public-life rows' . PHP_EOL;
echo 'portrait: ' . ($have ? "#{$have->id} exists" : "import $FILE as outside/$AS") . '; ' . ($port && $have && $port->id === $have->id ? 'already the portrait' : 'replaces #' . ($port?->id ?? '-') . ', which moves to recordImages') . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    if (!$have) {
        $vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'outside/']);
        $tmp = sys_get_temp_dir() . '/' . $AS; copy($path, $tmp);
        $have = new Asset(); $have->tempFilePath = $tmp; $have->setFilename($AS); $have->newFolderId = $folder->id; $have->setVolumeId($vol->id); $have->setScenario(Asset::SCENARIO_CREATE); $have->avoidFilenameConflicts = false;
        $have->setFieldValues(['license' => 'unknown', 'provenanceKind' => 'commissioned', 'acquiredDate' => '2026-10-01', 'enhancedBy' => 'Nathan Imhoff', 'enhancedDate' => new \DateTime('2026-10-01'),
            'enhancementMethod' => 'Edges filled and upscaled by Nathan Imhoff (Generate Fill and a Firefly upscale, per the content credential).',
            'contentCredentials' => "Adobe content credential (C2PA), https://cai-manifests.adobe.com/manifests/urn-c2pa-8c2c59b2-8f7e-48e4-86d0-e34c2da00498-adobe\nSteps recorded (UTC): 2026-10-01 04:59 edited (Generate Fill); 04:59 upscaled (Firefly creative upsampler).\nA note, not a refusal.",
            'source' => "Source per Nathan Imhoff. Edited by Nathan Imhoff. Received as $FILE, SHA-256 $SHA; the stored copy is re-encoded on import."]);
        if (!$elements->saveElement($have)) { throw new \RuntimeException('asset: ' . json_encode($have->getFirstErrors())); }
    }
    $s = Entry::find()->id($ID)->status(null)->one(); $h = array_map(fn($f) => $f->handle, $s->getFieldLayout()->getCustomFields());
    $vals = [];
    $old = $s->featuredImage->one();
    if (!$old || $old->id !== $have->id) {
        $vals['featuredImage'] = [$have->id];
        if ($old && in_array('recordImages', $h)) { $vals['recordImages'] = array_values(array_unique(array_merge($s->recordImages->ids(), [$old->id]))); }
    }
    if (!$isNew) {
        $vals += ['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'birthDate' => '1962', 'birthDateEdtf' => '1962', 'birthplace' => '', 'birthEvidence' => 'uncited',
            'editorNotes' => $EDITOR, 'recordDates' => $DATES,
            'recordProvenance' => trim((string)$s->recordProvenance . '; build_worden_profile.php, 1 Oct 2026: public-life profile; WordPress family detail, birth month and birthplace removed', '; ')];
    }
    if ($vals) { $s->setFieldValues(array_intersect_key($vals, array_flip($h))); if (!$elements->saveElement($s)) { throw new \RuntimeException('#279: ' . json_encode($s->getFirstErrors())); } }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$s = Entry::find()->id($ID)->status(null)->one();
$ok = trim((string)$s->body) === trim($BODY) && $s->featuredImage->one()?->filename === $AS && (string)$s->birthDate === '1962' && trim((string)$s->birthplace) === '';
echo 'READ-BACK ' . ($ok ? 'OK: ' . $s->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_worden_profile.php', 2, $ok ? 'verified' : 'SHORT', 'Leon Worden: public-life profile and portrait; family detail and birth month removed');
if (!$ok) { throw new \RuntimeException('build_worden_profile: read-back failed'); }
