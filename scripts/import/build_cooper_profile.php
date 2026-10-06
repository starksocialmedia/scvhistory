/**
 * Bill Cooper #26946, SCV Water Division 1 (Nathan, 1 October 2026). A living
 * person and a sitting director: public life only, two citations where a second
 * exists, no birth date, no family.
 *
 * THE SOURCES (read 2 October 2026 in a browser)
 *   [SCVW]     SCV Water, "Board of Directors": lists "William Cooper - Vice
 *              President"; four-year terms, elected by division.
 *   [ARCHIVE]  His two races: Castaic Lake Water Agency, at large, November 2016;
 *              SCV Water, Division 1, November 2022.
 *   [KHTS]     "Bill Cooper Announces Campaign For Re-Election," KHTS, 4 August
 *              2026, with the station's note that the information was provided
 *              by Cooper: his own account, given as his.
 *   [CAMPAIGN] billcooperforwater.com/about: campaign material, given as his.
 *   His career, his years on the Castaic Lake board from 1993 and his part in
 *   forming SCV Water rest on his own two statements and are attributed.
 *
 * THE OFFICE. No water-board office existed, so a Water Board Director office
 * is created, and a holding for SCV Water, Division 1, from December 2022, open.
 * The board's page gives no end date; Nathan gives January 2027, and the
 * holding's footnote says so. The family sentence in the KHTS piece is left out.
 *
 * Fills an empty body only. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_cooper_profile.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $elements = Craft::$app->getElements(); $svc = Craft::$app->getEntries();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$ID = 26946; $SCVW = 402; $DIV1 = 25365; $C22 = 26655; $C16 = 26601; $ROLE = 'Water Board Director';
$p = Entry::find()->id($ID)->status(null)->one();
$src = json_decode((string)@file_get_contents("$root/inventory/sources/bill-cooper-2026-10-02.json"), true)['sources'] ?? [];
$bad = [];
if (!$p || $p->title !== 'Bill Cooper') { $bad[] = '#26946 is not Bill Cooper'; }
$c22 = Entry::find()->id($C22)->status(null)->one(); $c16 = Entry::find()->id($C16)->status(null)->one();
if ((int)$c22?->votes !== 15251 || (int)$c16?->votes !== 43841 || $c22?->candidacyElection->one()?->electionDistrict->one()?->id !== $DIV1) { $bad[] = 'the candidacies are not as written'; }
$MUST = [
    'scvw' => ['William Cooper - Vice President', 'the term of office for all elected Directors is four years', 'three electoral divisions'],
    'khts' => ['Posted by: Jade Aubuchon', 'August 4, 2026', 'representing Division 1', 'The election will be held Tuesday, Nov. 3, 2026', 'worked for the Metropolitan Water District of Southern California for about 40 years', 'Water Treatment Section Manager', 'taught water treatment courses at College of the Canyons', 'His service on local water boards began in 1993 with the former Castaic Lake Water Agency', 'He served as president of that board for five years and later helped bring Santa Clarita’s three former water organizations together to form the SCV Water Agency', 'Cooper became the new agency’s first board president', 'chairs its Engineering and Operations Committee', 'Water Resources and Watershed Committee', 'United States Navy veteran who completed three tours during the Vietnam War', 'moved to Santa Clarita in 1972', 'Ed. Note: The above information was provided to KHTS Radio by Bill Cooper.'],
    'campaign' => ['Metropolitan Water District of Southern California for almost 40 years', 'overseeing the operations and maintenance of all five Metropolitan Water Treatment Plants', 'adjunct Professor at College of the Canyons', 'served on the CLWA Board since 1993 and was President of the Board for five year from 1996 to 2001', 'chair of the Planning and Engineering Committee', 'Child and Family Center Governing Board', 'Elected Officials Committee on teenage alcohol and drug abuse'],
];
foreach ($MUST as $k => $phrases) { foreach ($phrases as $ph) { if (!str_contains($src[$k]['passage'] ?? '', $ph)) { $bad[] = "$k does not read \"" . mb_substr($ph, 0, 60) . '"'; } } }

$BODY = implode("\n\n", [
    'Bill Cooper is a director of the Santa Clarita Valley Water Agency, elected from Division 1 in 2022, and in 2026 one of its two vice presidents. Before the agency was formed he sat on the board of the Castaic Lake Water Agency.[1][2][3]',
    'He was elected to the Castaic Lake board at large in November 2016, printed on the ballot as William Cooper, with 43,841 votes to Lynne Plambeck\'s 35,623; and to the SCV Water board from Division 1 in November 2022, with 15,251 votes against 4,743 for Nicole Wilson and 3,877 for Melissa K. Cantu.[2] In August 2026 he announced that he would stand again in Division 1 that November.[3]',
    'By his own account, given to KHTS and on his campaign site, he worked for the Metropolitan Water District of Southern California for about forty years, latterly as water treatment section manager over the district\'s five treatment plants, and taught water treatment at College of the Canyons. He says he has served on local water boards since 1993, beginning with the Castaic Lake Water Agency, which he led as president for five years, from 1996 to 2001; that he helped bring the valley\'s three water organizations together as SCV Water; and that he was the new agency\'s first board president. In 2026 he chaired its Engineering and Operations Committee.[3][4] He is, he says, a Navy veteran of three tours in the Vietnam War, and has lived in the Santa Clarita Valley since 1972.[3]',
    'His campaign site lists his service on the governing board of the Child and Family Center, and on the City\'s Elected Officials Committee on teenage alcohol and drug abuse.[4]',
]);
$NOTES = [
    'Santa Clarita Valley Water Agency, "Board of Directors," https://yourscvwater.com/governance/board-directors, read 2 October 2026.',
    'Archive records: "Castaic Lake Water Agency board election, November 8, 2016" and "Santa Clarita Valley Water board election, Division 1, November 8, 2022," with their returns.',
    'Jade Aubuchon, "Bill Cooper Announces Campaign For Re-Election To SCV Water Agency Board," KHTS / Hometown Station, 4 August 2026, https://www.hometownstation.com/santa-clarita-news/politics/santa-clarita-elections/bill-cooper-announces-campaign-for-re-election-to-scv-water-agency-board-604476. The station notes that the information was provided by Cooper.',
    'Bill Cooper for Water, "About Bill," http://billcooperforwater.com/about/, read 2 October 2026 (campaign material, written while he was on the Castaic Lake board).',
];
if (preg_match('~\x{2014}~u', $BODY . implode('', $NOTES))) { $bad[] = 'an em dash in the text'; }
if (preg_match('~\b(children|wife)\b|his family~i', $BODY)) { $bad[] = 'family detail in the body'; }
$cur = trim((string)$p?->body); if ($cur && $cur !== trim($BODY)) { $bad[] = '#26946 has a body already'; }
$role = Entry::find()->section('roles')->status(null)->title($ROLE)->one();
$hold = Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $ID, 'field' => 'holdingPerson'], ['targetElement' => $SCVW, 'field' => 'holdingBody']])->one();
echo '#26946 body: ' . ($cur ? 'already written' : 'empty -> ' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes') . '; office "' . $ROLE . '" ' . ($role ? "#{$role->id} exists" : 'create') . '; holding ' . ($hold ? "#{$hold->id} exists" : 'create: SCV Water, Division 1, December 2022 to (open)') . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }
$tx = Craft::$app->getDb()->beginTransaction();
try {
    if (!$role) {
        $tmpl = Entry::find()->id(18327)->status(null)->one();
        $role = new Entry(); $role->sectionId = $tmpl->sectionId; $role->setTypeId($tmpl->typeId); $role->title = $ROLE;
        if (!$elements->saveElement($role)) { throw new \RuntimeException('role: ' . json_encode($role->getFirstErrors())); }
    }
    if (!$hold) {
        $tmpl = Entry::find()->id(23401)->status(null)->one();
        $hold = new Entry(); $hold->sectionId = $tmpl->sectionId; $hold->setTypeId($tmpl->typeId);
        $hold->setFieldValues(['holdingPerson' => [$ID], 'holdingOffice' => [$role->id], 'holdingBody' => [$SCVW], 'holdingDistrict' => [$DIV1], 'seatLabel' => 'Division 1',
            'termStart' => 'December 2022', 'termStartEdtf' => '2022-12', 'selectionMethod' => 'elected', 'howEnded' => 'serving', 'startEvidence' => 'derived',
            'footnotes' => [['number' => '1', 'note' => 'Elected 8 November 2022 (archive record: "Santa Clarita Valley Water board election, Division 1, November 8, 2022"); the start is the month after, read from the count. The board\'s page, read 2 October 2026, lists him as a vice president without dates; per Nathan Imhoff, the term runs to January 2027.', 'source' => 'editorial-2026']],
            'recordProvenance' => 'build_cooper_profile.php, 2 October 2026']);
        if (!$elements->saveElement($hold)) { throw new \RuntimeException('holding: ' . json_encode($hold->getFirstErrors())); }
    }
    if (!$cur) {
        $s = Entry::find()->id($ID)->status(null)->one(); $h = array_map(fn($f) => $f->handle, $s->getFieldLayout()->getCustomFields());
        $s->setFieldValues(array_intersect_key(['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'occupation' => 'Water board director',
            /* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
            'personOrganizations' => array_values(array_unique(array_merge($s->personOrganizations->status(null)->ids(), [$SCVW]))),
            'recordProvenance' => trim((string)$s->recordProvenance . '; build_cooper_profile.php, 2 Oct 2026: public-life profile; Division 1 holding', '; ')], array_flip($h)));
        if (!$elements->saveElement($s)) { throw new \RuntimeException('#26946: ' . json_encode($s->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$s = Entry::find()->id($ID)->status(null)->one();
$ok = trim((string)$s->body) === trim($BODY) && Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $ID, 'field' => 'holdingPerson'])->exists();
echo 'READ-BACK ' . ($ok ? 'OK: ' . $s->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_cooper_profile.php', 3, $ok ? 'verified' : 'SHORT', 'Bill Cooper: public-life profile; Water Board Director office; SCV Water Division 1 holding');
if (!$ok) { throw new \RuntimeException('build_cooper_profile: read-back failed'); }
