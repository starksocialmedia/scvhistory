/**
 * Maria Gutzeit #21582: the full public career, from her official SCV Water
 * board biography and her campaign site (Nathan, 29 September 2026).
 *
 * The first body (create_gutzeit_record.php) held only the board service. This
 * adds what the board biography states: the part she took in forming SCV Water
 * under SB 634, her committee and board roles, and her profession, an
 * environmental engineer with a chemical engineering degree from the
 * University of Illinois, at Amoco Oil, Waste Management of California and
 * Anheuser-Busch before founding Compliance Plus in 1995.
 *
 * Living person, public life only. The board biography's lines about her
 * husband, child and dogs are left out, as are the campaign site's claims of
 * grants won and money saved, which are claims, not record. Where the two
 * sources word a thing differently (she "participated" in forming SCV Water,
 * says the board; she "led" it, says the campaign), the body takes the
 * official wording and the footnote gives both.
 *
 * Replaces the body and footnotes written on 29 September, and only those: a
 * body edited since is refused. The occupation is updated; the editor notes
 * are kept. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/update_gutzeit_profile.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

$ID = 21582;
$OLD_START = 'Maria Gutzeit was elected to the board of the Newhall County Water District in 2003.';
$BIO = 'SCV Water, Board of Directors, "Maria Gutzeit - President," https://www.yourscvwater.com/governance/board-directors, read 29 September 2026';
$CAMPAIGN = 'Maria Gutzeit for SCV Water, https://www.electmaria.com/, her campaign site, read 29 September 2026';
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);

$BODY = implode("\n\n", [
    'Maria Gutzeit is president of the board of the Santa Clarita Valley Water Agency, SCV Water, for Division 3.[1][2] She was elected to the board of the Newhall County Water District in 2003, and served on it and, after the district merged into SCV Water in 2018, on the SCV Water board until 2020.[1][3] She took part in forming SCV Water, which state legislation, Senate Bill 634, created with effect from 1 January 2018.[1][2]',
    'In April 2022 the board appointed her to a vacancy in Division 3,[4][5] and in November 2022 she was elected to that seat, for the term ending in January 2027; the board\'s own record dates her return from January 2023.[1][6] The board chose her as its president for 2025, and she holds the office now.[1][7] She chairs its Public Outreach and Legislation committee and sits on its water resources committee, and she serves on the SCV Water Financing Corporation, as president of the Upper Santa Clara Valley Joint Powers Authority, and as a director of the Santa Clarita Valley Groundwater Sustainability Agency.[1][2]',
    'She is an environmental engineer, with a degree in chemical engineering from the University of Illinois at Urbana-Champaign. She began her career with Amoco Oil, Waste Management of California and Anheuser-Busch, as site environmental engineer for three Southern California facilities, and in 1995 founded Compliance Plus, which helps industrial clients in Southern California and the western United States meet environmental regulations. She is its owner and principal engineer.[1]',
]);
$FOOTNOTES = $fn([
    $BIO . ': "board president of the Santa Clarita Valley Water Agency"; "a board member of predecessor agency Newhall County Water District and then SCV Water from 2003-2020 and 2023-present"; "participating in the formation of SCV Water via state legislation (SB634 Wilk effective 1/1/2018.)"; "the owner and principal engineer at Compliance Plus"; "Amoco Oil, Waste Management of California, and Anheuser Busch"; "founded Compliance Plus in 1995"; "a degree in Chemical Engineering from the University of Illinois Champaign-Urbana"; Division 3, elected January 2023, term expires January 2027; Chair, Public Outreach and Legislation; Member, Water Resources and Outreach; Member, SCV Water Financing Corporation; President, Upper Santa Clara Valley Joint Powers Authority; Director, Santa Clarita Valley Groundwater Sustainability Agency. Her professional career rests on this, her official biography, alone.',
    $CAMPAIGN . ': "serves as President of the Board"; committee roles "Public Outreach and Legislation, and Water Resources and Watershed"; a member of the Groundwater Sustainability Agency, the Financing Corporation and the Joint Powers Authority; "Maria led the merger that created SCV Water," the campaign\'s wording. Its figures for grants won and money saved are campaign claims and are not repeated.',
    'SCV Water, press release, 17 January 2018, https://www.yourscvwater.com/sites/default/files/SCVWA/newscenter/Press%20Release/2018/2018-01.17-Press-Release_SCVWA-Leadership_FINAL.pdf: "was initially elected to the NCWD board in 2003"; the district merged into SCV Water on 1 January 2018.',
    'SCVNews, "SCV Water appoints new member to represent District 3," 27 April 2022, https://scvnews.com/scv-water-appoints-new-member-to-represent-district-3/',
    'Santa Clarita Magazine, 28 May 2022, https://santaclaritamagazine.com/2022/05/scv-water-board-of-directors-appoints-new-member-to-represent-division-3/: selected on 25 April from four applicants, for the term ending 1 January 2023.',
    'Ballotpedia, https://ballotpedia.org/Maria_Gutzeit: Division 3, 8 November 2022, Gutzeit 11,190 (51.19%), Lynne Plambeck 10,668. Secondary; the LA County Registrar\'s final count has not been read.',
    'SCVNews, "SCV Water elects Gutzeit board president," 8 January 2025, https://scvnews.com/scv-water-elects-gutzeit-board-president/',
]);

$e = Entry::find()->id($ID)->status(null)->one();
if (!$e || $e->title !== 'Maria Gutzeit') { echo "REFUSING: #$ID is not Maria Gutzeit" . PHP_EOL; return; }
$body = trim((string)$e->body);
if ($body === trim($BODY)) { echo 'already written; nothing to do' . PHP_EOL; return; }
if (!str_starts_with($body, $OLD_START)) { echo 'REFUSING: the body is not the one written on 29 September; someone has edited it' . PHP_EOL; return; }
foreach (explode("\n\n", $BODY) as $i => $p) { echo 'P' . ($i + 1) . ': ' . $p . PHP_EOL . PHP_EOL; }
echo 'footnotes: ' . count($FOOTNOTES) . ' (replacing 6)' . PHP_EOL . 'occupation: "' . $e->occupation . '" -> "Environmental engineer; water board president"' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$e->setFieldValues(['body' => $BODY, 'footnotes' => $FOOTNOTES, 'occupation' => 'Environmental engineer; water board president']);
if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException('update_gutzeit_profile: ' . json_encode($e->getFirstErrors())); }
$ok = trim((string)Entry::find()->id($ID)->status(null)->one()->body) === trim($BODY);
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT: body differs') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('update_gutzeit_profile.php', 1, $ok ? 'verified' : 'SHORT', 'Gutzeit full public career, 7 footnotes');
if (!$ok) { throw new \RuntimeException('update_gutzeit_profile: body differs'); }
