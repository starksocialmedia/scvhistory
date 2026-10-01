/**
 * Aakash Ahuja #25449: a living person, so public-life facts only, two citations
 * for each, no birth date, nothing about family, health or money (Nathan,
 * 1 October 2026). His record came with the school board import; this gives it
 * a body, an office and its sources.
 *
 * THE SOURCES, and what each may carry
 *   Santa Clarita Magazine, "Vote Dr. Aakash Ahuja for Hart School Board,"
 *     29 October 2024: written by Dr. Ahuja himself, campaign material. It is
 *     cited only beside an independent record, never alone, and what only it
 *     says is left out: his family, that he came from India, and the credit he
 *     takes for the junior high phone restriction.
 *   The Hart district's own page, "Governing Board Member Info," read 1 October
 *     2026 (names, areas, offices and terms kept in
 *     inventory/elections/hart-board-2026-10-01.json; its emails, telephone
 *     numbers and biographies are not taken): Trustee Area 1, member, current
 *     term 2024 to 2028, and "Board-Certified Psychiatrist MD by profession."
 *   CEDA's 2020 and 2024 files, and the County's 2020 canvass: the votes and his
 *     ballot designations, "Doctor/Father/Businessman" (2020) and
 *     "Father/Psychiatrist/Educator" (2024), which are what the County printed.
 *
 * THE CLAIMS, two citations each
 *   stood for the city council, November 2020, sixth of nine: the County's
 *     canvass and CEDA 2020.
 *   elected to the Hart board, Trustee Area 1, November 2024, first of three:
 *     CEDA 2024 and the district's page.
 *   a psychiatrist by profession: his 2024 ballot designation and the
 *     district's page (the magazine says so as well, and is cited third).
 *
 * THE OFFICE. An officeHolding: School Board Member, the Hart district, Trustee
 * Area 1, from 2024, serving, elected, roster evidence (the district's own list,
 * as with SCV Water's). The role "School Board Member" is created; the archive
 * had only "College Trustee". The district's page joins the documents.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_ahuja_profile.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$ID = 25449; $HART = Entry::find()->section('organizations')->slug('william-s-hart-union-high-school-district')->one();
$TA1 = Entry::find()->section('places')->title('William S. Hart Union High School District, Trustee Area 1')->one();
$p = $get($ID);
$hart = json_decode(file_get_contents("$root/inventory/elections/hart-board-2026-10-01.json"), true);
$me = array_values(array_filter($hart['members'] ?? [], fn($m) => str_contains($m['name'], 'Ahuja')))[0] ?? null;
$ceda = array_values(array_filter(json_decode(file_get_contents("$root/inventory/elections/ceda-scv.json"), true)['rows'], fn($r) => $r['last'] === 'Ahuja' && $r['first'] === 'Aakash'));
$c20 = array_values(array_filter($ceda, fn($r) => $r['year'] === 2020))[0] ?? null; $c24 = array_values(array_filter($ceda, fn($r) => $r['year'] === 2024))[0] ?? null;
$doc20 = Entry::find()->section('documents')->status(null)->title('Final Election Canvass, General Election, November 3, 2020: Santa Clarita City Council')->one();
$cedaDoc = Entry::find()->section('documents')->status(null)->title('California Elections Data Archive (CEDA): candidate files, 1995 to 2024')->one();
$bad = [];
if (!$p || $p->title !== 'Aakash Ahuja') { $bad[] = '#25449 is not Aakash Ahuja'; }
if (!$HART || !$TA1 || !$doc20 || !$cedaDoc) { $bad[] = 'the Hart district, Trustee Area 1, the 2020 canvass or the CEDA record is missing'; }
if (!$me || $me['area'] !== '1' || $me['termFrom'] !== '2024' || $me['termTo'] !== '2028') { $bad[] = 'the district\'s page does not give him Trustee Area 1, 2024 to 2028'; }
if (!$c20 || $c20['votes'] !== 14300 || $c20['elected'] || !$c24 || $c24['votes'] !== 8888 || !$c24['elected'] || $c24['area'] !== '1') { $bad[] = 'CEDA does not read as the text says'; }
/* The candidacies, as the archive holds them: the places in the text are checked against them. */
$cands = Entry::find()->section('candidacies')->relatedTo(['targetElement' => $ID, 'field' => 'candidacyPerson'])->all();
$placeOf = function ($c) { $e = $c->candidacyElection->one(); $q = Entry::find()->section('candidacies')->relatedTo(['targetElement' => $e, 'field' => 'candidacyElection']); return [substr($e->electionDateEdtf, 0, 4), (int)(clone $q)->votes('> ' . (int)$c->votes)->count() + 1, (int)$q->count()]; };
$places = array_map($placeOf, $cands);
if (!in_array(['2020', 6, 9], $places) || !in_array(['2024', 1, 3], $places)) { $bad[] = 'the archive\'s candidacies are not sixth of nine in 2020 and first of three in 2024: ' . json_encode($places); }

$MAG = 'Aakash Ahuja, "Vote Dr. Aakash Ahuja for Hart School Board," Santa Clarita Magazine, 29 October 2024, https://santaclaritamagazine.com/2024/10/vote-dr-aakash-ahuja-for-hart-school-board-2/ (campaign material, written by the candidate)';
$HARTPAGE = 'William S. Hart Union High School District, "Governing Board Member Info," https://www.hartdistrict.org/governing-board, read 1 October 2026';
$BODY = implode("\n\n", [
    'Aakash Ahuja is a member of the governing board of the William S. Hart Union High School District, elected in November 2024 for Trustee Area 1, for the term of 2024 to 2028.[1][2] He is a psychiatrist by profession.[2][3][4]',
    'He first stood for public office in the Santa Clarita City Council election of November 2020, where he came sixth of nine with 14,300 votes; his ballot designation was "Doctor/Father/Businessman."[5][6] In November 2024 he won Trustee Area 1 of the Hart board, first of three candidates with 8,888 votes, as "Father/Psychiatrist/Educator."[1]',
    'He is one of the candidates the archive records standing for more than one of the valley\'s elected bodies: a council candidate who lost, and later won a school board seat.',
]);
$NOTES = [
    'California Elections Data Archive (CEDA), the 2024 candidates file (CEDA2024Data.xlsx), row ' . ($c24['row'] ?? '') . ': William S. Hart Union High School District, Trustee Area 1, elected, 8,888 votes, ballot designation "Father/Psychiatrist/Educator." A compilation of the County\'s returns.',
    $HARTPAGE . ': "Trustee Area No. 1 representative," "current term 2024 - 2028," "a Board-Certified Psychiatrist MD by profession," and "elected ... representing Trustee Area 1, in November 2024."',
    'His ballot designation in 2024, as the County printed it: "Father/Psychiatrist/Educator" (CEDA 2024, as note 1).',
    $MAG . ': "I am a Board-Certified psychiatrist MD."',
    'County of Los Angeles, Registrar-Recorder/County Clerk, final canvass of the Santa Clarita City Council election of November 3, 2020. Archive record: "Final Election Canvass, General Election, November 3, 2020: Santa Clarita City Council."',
    'California Elections Data Archive (CEDA), the 2020 candidates file (CEDA2020Data.xlsx), row ' . ($c20['row'] ?? '') . ': Santa Clarita City Council, not elected, 14,300 votes, ballot designation "Doctor/Father/Businessman."',
];
$role = Entry::find()->section('roles')->status(null)->title('School Board Member')->one();
$pageDoc = Entry::find()->section('documents')->status(null)->sourcePath('https://www.hartdistrict.org/governing-board')->one();
$holding = $p ? Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $ID, 'field' => 'holdingPerson'])->termStartEdtf('2024')->one() : null;
echo '#25449 Aakash Ahuja: body ' . (trim((string)$p?->body) === trim($BODY) ? 'already written' : (trim((string)$p?->body) ? 'NOT EMPTY, refusing' : 'empty -> ' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes, every claim cited twice')) . PHP_EOL;
if ($p && trim((string)$p->body) && trim((string)$p->body) !== trim($BODY)) { $bad[] = '#25449 has a body already'; }
echo ($role ? "#{$role->id} exists: " : 'create role: ') . 'School Board Member' . PHP_EOL;
echo ($pageDoc ? "#{$pageDoc->id} exists: " : 'create document: ') . 'the Hart district\'s Governing Board Member Info page, read 1 October 2026' . PHP_EOL;
echo ($holding ? "#{$holding->id} exists: " : 'create officeHolding: ') . 'School Board Member, William S. Hart Union High School District, Trustee Area 1, 2024 to now, elected, roster' . PHP_EOL;
echo 'Left out, the campaign\'s own claims: his family, that he came from India, and the phone restriction he credits himself with.' . PHP_EOL;
if (preg_match('~\x{2014}~u', $BODY . implode('', $NOTES))) { $bad[] = 'an em dash in the text'; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    if (!$role) {
        $role = new Entry(); $rs = $svc->getSectionByHandle('roles'); $role->sectionId = $rs->id; $role->setTypeId($rs->getEntryTypes()[0]->id); $role->title = 'School Board Member';
        if (!$elements->saveElement($role)) { throw new \RuntimeException('role: ' . json_encode($role->getFirstErrors())); }
    }
    if (!$pageDoc) {
        $pageDoc = new Entry(); $ds = $svc->getSectionByHandle('documents'); $pageDoc->sectionId = $ds->id; $pageDoc->setTypeId($svc->getEntryTypeByHandle('document')->id);
        $pageDoc->title = 'William S. Hart Union High School District: Governing Board Member Info (web page, read 1 October 2026)';
        $pageDoc->setFieldValues(['sourcePath' => 'https://www.hartdistrict.org/governing-board', 'sourceLine' => 'William S. Hart Union High School District', 'originalPublishDate' => 'read 1 October 2026', 'originalPublishDateEdtf' => '2026-10-01', 'publishedBy' => [$HART->id],
            'body' => 'The district\'s own list of its board: each trustee\'s area, office and current term. Read on 1 October 2026 it named ' . implode('; ', array_map(fn($m) => "{$m['name']}, Trustee Area {$m['area']}, {$m['termFrom']} to {$m['termTo']}", $hart['members'])) . '. The archive keeps the names, areas, offices and terms (inventory/elections/hart-board-2026-10-01.json), not the biographies or contact details.',
            'recordProvenance' => 'build_ahuja_profile.php, 1 October 2026: page SHA-256 ' . $hart['htmlSha256']]);
        if (!$elements->saveElement($pageDoc)) { throw new \RuntimeException('page document: ' . json_encode($pageDoc->getFirstErrors())); }
    }
    $p = $get($ID);
    $h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
    $vals = ['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'occupation' => 'Psychiatrist; school board member', 'roles' => array_values(array_unique(array_merge($p->roles->ids(), [$role->id]))),
        'recordProvenance' => trim((string)$p->recordProvenance . '; build_ahuja_profile.php, 1 October 2026: body, office and sources; public-life facts only')];
    $p->setFieldValues(array_intersect_key($vals, array_flip($h)));
    if (!$elements->saveElement($p)) { throw new \RuntimeException('#25449: ' . json_encode($p->getFirstErrors())); }
    if (!$holding) {
        $holding = new Entry(); $os = $svc->getSectionByHandle('officeHoldings'); $holding->sectionId = $os->id; $holding->setTypeId($svc->getEntryTypeByHandle('officeHolding')->id);
        $holding->setFieldValues(['holdingPerson' => [$ID], 'holdingOffice' => [$role->id], 'holdingBody' => [$HART->id], 'holdingDistrict' => [$TA1->id],
            'termStart' => '2024', 'termStartEdtf' => '2024', 'selectionMethod' => 'elected', 'howEnded' => 'serving', 'startEvidence' => 'roster',
            'footnotes' => $fn(['Elected on November 5, 2024, first of three candidates, with 8,888 votes (CEDA 2024).', $HARTPAGE . ': "current term 2024 - 2028."']),
            'recordProvenance' => 'build_ahuja_profile.php, 1 October 2026']);
        if (!$elements->saveElement($holding)) { throw new \RuntimeException('holding: ' . json_encode($holding->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$r = $get($ID);
$ok = trim((string)$r->body) === trim($BODY) && Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $ID, 'field' => 'holdingPerson'])->exists();
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_ahuja_profile.php', 3, $ok ? 'verified' : 'SHORT', 'Aakash Ahuja: profile, office, sources');
if (!$ok) { throw new \RuntimeException('build_ahuja_profile: read-back failed'); }
