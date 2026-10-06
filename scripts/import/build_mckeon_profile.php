/**
 * Howard P. "Buck" McKeon #18791: the city's first mayor and the valley's
 * congressman for twenty-two years, whose record was empty (Nathan, 1 October
 * 2026). A living person: public life only, each claim on two sources or said to
 * be one source's, and no birth date beyond the year.
 *
 * THE SOURCES
 *   [BIOGUIDE] Biographical Directory of the United States Congress, M000508.
 *              Read in the Wayback Machine's copy of 17 December 2019 of
 *              bioguide.congress.gov/scripts/biodisplay.pl?index=M000508; the
 *              current site refuses automated requests. Linked on the record as
 *              an authority ID (bioguideId, add_bioguide_field.php).
 *   [LEGIS]    unitedstates/congress-legislators, legislators-historical.yaml
 *              (public domain): his eleven terms, 5 January 1993 to 3 January
 *              2015, California's 25th District, and his Wikidata id, Q461981.
 *   [OWN]      his biography on mckeon.house.gov, Wayback Machine copy of
 *              7 July 2006, which its footer marks as paid for by his campaign:
 *              his own account, attributed as such.
 *   [ROSTER]   the archive's council ledger and the City Clerk's 1987 results.
 *   Wikipedia was a finding aid only, and is the record's link.
 *
 * Where they differ, both are given: the Directory has him on the Hart board
 * from 1979, his own account from 1978; it has him born in Los Angeles, his own
 * account in Tujunga, which is in Los Angeles. Left out: his religion, mission,
 * marriage and family, which his own account includes.
 *
 * THE PORTRAIT. Wikimedia Commons, File:Buck McKeon 2011.jpeg, 2630 x 3944,
 * SHA-1 879372020e53e5947937f94bc6c04fc9fc4fa85b, checked against Commons:
 * "United States Congress," 12 April 2011, public domain as a work of the
 * federal government. The file Nathan supplied is the same picture reduced to
 * 500 x 750; the original is used.
 *
 * THE OFFICES, as officeHolding records: School Board Member, Hart district,
 * 1979 to 1987 (retrospective); Congressman, California's 25th District, 1993
 * to 2015 (certified: the House's own Directory). His council term exists.
 *
 * Needs add_bioguide_field.php, and build_ahuja_profile.php (the School Board
 * Member role) or creates the role itself. Fills an empty body only.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_mckeon_profile.php'))"
 */

use craft\elements\Entry;
use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$ID = 18791; $CONGRESSMAN = 18409;
$FILE = "$root/inventory/incoming/Buck_McKeon_2011-commons-original.jpeg"; $SHA1 = '879372020e53e5947937f94bc6c04fc9fc4fa85b'; $FILENAME = 'buck-mckeon-official-portrait-2011.jpg';
$p = $get($ID); $hart = Entry::find()->section('organizations')->slug('william-s-hart-union-high-school-district')->one();
$bad = [];
if (!$p || $p->title !== 'Buck McKeon') { $bad[] = '#18791 is not Buck McKeon'; }
if (!$get($CONGRESSMAN) || $get($CONGRESSMAN)->title !== 'Congressman') { $bad[] = '#18409 is not the Congressman role'; }
if (!is_file($FILE) || sha1_file($FILE) !== $SHA1) { $bad[] = 'the portrait is missing or is not the Commons original'; }
$haveBioguide = (bool)Craft::$app->getFields()->getFieldByHandle('bioguideId');
if (!$haveBioguide) { echo 'NEEDS add_bioguide_field.php first (the plan below is still checked)' . PHP_EOL; }
$council = Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $ID, 'field' => 'holdingPerson'])->all();
if (!array_filter($council, fn($h) => (string)$h->termStartEdtf === '1987-12-15')) { $bad[] = 'his council term is not on the record'; }

$BIOGUIDE = 'Biographical Directory of the United States Congress, "McKEON, Howard P. (Buck)," M000508, read in the Wayback Machine\'s copy of 17 December 2019, http://web.archive.org/web/20191217051358/http://bioguide.congress.gov/scripts/biodisplay.pl?index=M000508';
$BODY = implode("\n\n", [
    'Howard P. "Buck" McKeon sat on the Santa Clarita City Council from its first meeting in December 1987 until 1992, and was the city\'s first mayor; he then represented the valley in the United States House of Representatives for twenty-two years, from 1993 to 2015.[1][2][3][4]',
    'Born in 1938, he graduated from Verdugo Hills High School in Tujunga and, in 1985, took a bachelor\'s degree at Brigham Young University; the Directory describes him as a business owner and a bank executive.[1][3] He was a trustee of the William S. Hart Union High School District, and its board\'s chairman, from 1979 until 1987 in the Directory\'s account, from 1978 in his own.[1][3]',
    'In the cityhood election of 3 November 1987 he came first of twenty-six candidates, with 9,855 votes, and at the council\'s first meeting its members chose him as mayor.[5][4][3] The Directory has him mayor and council member until 1992; he did not stand for the council again.[1][4]',
    'He was elected to Congress in 1992 from California\'s 25th District and re-elected ten times, serving from 3 January 1993 to 3 January 2015, and did not stand in 2014.[1][2] He chaired the Committee on Education and the Workforce from 2006, and the Committee on Armed Services in the 112th and 113th Congresses, 2011 to 2015.[1][3] The Directory also records his membership of the California Republican Central Committee from 1988 to 1992.[1]',
    'His path, from the Hart district\'s board to the first council, is the one the archive\'s election records begin too late to see: the state\'s compilation of school board results starts in 1995.',
]);
$NOTES = [
    $BIOGUIDE . ': "born in Los Angeles ... 1938; graduated from Verdugo Hills High School, Tujunga, Calif.; B.S., Brigham Young University, Provo, Utah, 1985; business owner; chairman and trustee, William S. Hart School District ... 1979-1987; bank executive; mayor and council member of Santa Clarita Valley, Calif., 1987-1992; member of the California Republican Central Committee, 1988-1992; elected as a Republican to the One Hundred Third and to the ten succeeding Congresses (January 3, 1993-January 3, 2015); was not a candidate for reelection ... in 2014; chair, Committee on Education and the Workforce (One Hundred Ninth Congress); chair, Committee on Armed Services (One Hundred Twelfth and One Hundred Thirteenth Congresses)."',
    'unitedstates/congress-legislators, legislators-historical.yaml, https://github.com/unitedstates/congress-legislators (public domain): bioguide M000508, Wikidata Q461981; eleven terms as Representative for California\'s 25th District, 5 January 1993 to 3 January 2015; birth year 1938.',
    'Buck McKeon, "Biography," mckeon.house.gov, Wayback Machine copy of 7 July 2006, http://web.archive.org/web/20060707151608/http://www.mckeon.house.gov:80/Biography/, a page its footer marks "Paid For By Buck McKeon": his own account. "served on the William S. Hart Union High School District Board of Trustees from 1978 to 1987"; "It was during the city council\'s first meeting that McKeon was chosen by his colleagues as Santa Clarita\'s first mayor"; "graduated from Verdugo Hills High School in 1956"; "completed his bachelor\'s degree in 1985"; "Chairman of the House Committee on Education and the Workforce in 2006."',
    'The council ledger in the archive (Leon Worden, "Santa Clarita City Council 1987—," on record #4967): "Buck McKeon 1987-1992"; and the note on the same record that the council took office on 15 December 1987, when "there was no absolute guarantee that Buck McKeon would be named mayor."',
    'City of Santa Clarita, City Clerk, General Municipal Elections: Historical Election Results, 1987 to 2012: 3 November 1987, "Howard P. \'Buck\' McKeon," first of twenty-six, 9,855 votes.',
];
foreach ([$BODY, implode('', $NOTES)] as $t) { if (preg_match('~\x{2014}(?!,)~u', str_replace('1987—,', '', $t))) { $bad[] = 'an em dash in the text'; } }
if (preg_match('~\b(wife|Patricia|children|grandchildren|mission|Latter)~i', $BODY)) { $bad[] = 'family or religion in the body'; }
$cur = trim((string)$p?->body);
$role = Entry::find()->section('roles')->status(null)->title('School Board Member')->one();
$asset = Asset::find()->filename($FILENAME)->one();
echo '#18791 Buck McKeon: body ' . ($cur === trim($BODY) ? 'already written' : ($cur ? 'NOT EMPTY, refusing' : 'empty -> ' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes')) . '; bioguideId M000508; wikidataId Q461981; birth year 1938 only; occupation' . PHP_EOL;
echo 'portrait: ' . ($asset ? "#{$asset->id} exists" : "import $FILENAME (2630 x 3944, public domain, United States Congress, 12 April 2011)") . ' as featuredImage' . PHP_EOL;
echo 'offices: School Board Member, Hart district, 1979 to 1987 (role ' . ($role ? "#{$role->id}" : 'created') . '); Congressman, California\'s 25th District, 1993 to 2015; the council term exists' . PHP_EOL;
if ($cur && $cur !== trim($BODY)) { $bad[] = '#18791 has a body already'; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad || !$haveBioguide) { echo 'REFUSING: ' . ($bad ? 'resolve the refusals first' : 'run add_bioguide_field.php first') . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    if (!$asset) {
        $volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => 'outside/']);
        $tmp = sys_get_temp_dir() . '/' . $FILENAME; copy($FILE, $tmp);
        $asset = new Asset(); $asset->tempFilePath = $tmp; $asset->setFilename($FILENAME); $asset->newFolderId = $folder->id; $asset->setVolumeId($volume->id); $asset->setScenario(Asset::SCENARIO_CREATE); $asset->avoidFilenameConflicts = false;
        if (!$elements->saveElement($asset)) { throw new \RuntimeException('asset: ' . json_encode($asset->getFirstErrors())); }
        $asset = Asset::find()->id($asset->id)->one();
        $asset->title = 'Buck McKeon, official portrait, 2011'; $asset->alt = 'Official portrait of Representative Howard P. "Buck" McKeon';
        $asset->setFieldValues(['license' => 'public-domain', 'provenanceKind' => 'outside', 'creator' => 'United States Congress', 'acquiredDate' => '2026-10-01',
            'dateAsPrinted' => 'April 12, 2011, per Wikimedia Commons', 'dateEdtf' => '2011-04-12', 'sourceUrl' => ['type' => 'url', 'value' => 'https://commons.wikimedia.org/wiki/File:Buck_McKeon_2011.jpeg'],
            'source' => 'Wikimedia Commons, File:Buck McKeon 2011.jpeg, the original, 2630 x 3944, SHA-1 879372020e53e5947937f94bc6c04fc9fc4fa85b: "United States Congress," from his House website\'s high-resolution file. Public domain as a work of the federal government. The 500 x 750 copy supplied on 1 October 2026 was not used; the stored copy is re-encoded.']);
        if (!$elements->saveElement($asset)) { throw new \RuntimeException('asset fields: ' . json_encode($asset->getFirstErrors())); }
    }
    if (!$role) {
        $role = new Entry(); $rs = $svc->getSectionByHandle('roles'); $role->sectionId = $rs->id; $role->setTypeId($rs->getEntryTypes()[0]->id); $role->title = 'School Board Member';
        if (!$elements->saveElement($role)) { throw new \RuntimeException('role'); }
    }
    $e = $get($ID); $h = array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
    /* A field added in the same request is not yet in the layout an element sees, and
       the value would be dropped without a word (found in rehearsal, 1 October 2026):
       refuse instead. Run add_bioguide_field.php in its own request first. */
    if (!in_array('bioguideId', $h, true)) { throw new \RuntimeException('bioguideId is not on the person layout this request sees: run add_bioguide_field.php first, separately'); }
    $vals = ['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'personAliases' => "Howard P. McKeon\nHoward P. \"Buck\" McKeon\nHoward McKeon",
        'occupation' => 'Congressman; first mayor of Santa Clarita', 'birthDate' => '1938', 'birthDateEdtf' => '1938', 'birthEvidence' => 'certified',
        'bioguideId' => 'M000508', 'wikidataId' => 'Q461981', 'personWikipediaUrl' => 'https://en.wikipedia.org/wiki/Buck_McKeon', 'featuredImage' => [$asset->id],
        /* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
        'roles' => array_values(array_unique(array_merge($e->roles->status(null)->ids(), [$CONGRESSMAN, $role->id]))),
        'recordProvenance' => trim((string)$e->recordProvenance . '; build_mckeon_profile.php, 1 October 2026: profile, offices, authority IDs and portrait; public life only')];
    $e->setFieldValues(array_intersect_key($vals, array_flip($h)));
    if (!$elements->saveElement($e)) { throw new \RuntimeException('#18791: ' . json_encode($e->getFirstErrors())); }
    $os = $svc->getSectionByHandle('officeHoldings'); $ot = $svc->getEntryTypeByHandle('officeHolding');
    foreach ([
        ['1979', [$role->id], $hart ? [$hart->id] : [], '1979', '1987', '1987', null, 'expired', 'retrospective', 'retrospective', [$BIOGUIDE . ': "chairman and trustee, William S. Hart School District ... 1979-1987."', 'His own account (mckeon.house.gov, 2006) gives 1978 to 1987.']],
        ['1993-01-03', [$CONGRESSMAN], [], 'January 3, 1993', 'January 3, 2015', '2015-01-03', "California's 25th District", 'expired', 'certified', 'certified', [$BIOGUIDE . ': "elected ... to the One Hundred Third and to the ten succeeding Congresses (January 3, 1993-January 3, 2015); was not a candidate for reelection."', 'unitedstates/congress-legislators: eleven terms, California\'s 25th District.']],
    ] as [$se, $office, $body, $sp, $ep, $ee, $seat, $how, $sev, $eev, $notes]) {
        if (Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $ID, 'field' => 'holdingPerson'])->termStartEdtf($se)->exists()) { continue; }
        $o = new Entry(); $o->sectionId = $os->id; $o->setTypeId($ot->id);
        $o->setFieldValues(array_filter(['holdingPerson' => [$ID], 'holdingOffice' => $office, 'holdingBody' => $body, 'termStart' => $sp, 'termStartEdtf' => $se, 'termEnd' => $ep, 'termEndEdtf' => $ee,
            'seatLabel' => $seat, 'selectionMethod' => $office === [$CONGRESSMAN] ? 'elected' : null, 'howEnded' => $how, 'startEvidence' => $sev, 'endEvidence' => $eev,
            'footnotes' => $fn($notes), 'recordProvenance' => 'build_mckeon_profile.php, 1 October 2026'], fn($v) => $v !== null && $v !== []));
        if (!$elements->saveElement($o)) { throw new \RuntimeException("holding $se: " . json_encode($o->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$r = $get($ID);
$ok = trim((string)$r->body) === trim($BODY) && (string)$r->bioguideId === 'M000508' && $r->featuredImage->one()
    && Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $ID, 'field' => 'holdingPerson'])->count() >= 3;
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_mckeon_profile.php', 4, $ok ? 'verified' : 'SHORT', 'Buck McKeon: profile, offices, Bioguide and Wikidata, the official portrait');
if (!$ok) { throw new \RuntimeException('build_mckeon_profile: read-back failed'); }
