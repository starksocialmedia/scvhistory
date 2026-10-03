/**
 * Connie Worden #16418: the profile rewritten from her sources (Nathan, 3 October
 * 2026: "Lead with what she did. The relationship to Leon is a fact about her
 * record, not the reason for it"; "She is a source of this archive, not only a
 * subject in it, and the profile should show that from the photo key and the
 * collection labels rather than asserting a role she did not hold").
 *
 * The body written this morning (build_profiles_batch3.php) rested on her two
 * obituaries and Reynolds. This one rests on what import_connie_worden_sources.php
 * brought in today: her own 1999 history of the campaigns, her 2003 proposal to
 * the Historical Society, her 2007 recollection, the 1975, 1985 and 1987
 * coverage, the Friends of Hart Park papers, the Hart board and Planning
 * Commission lists, the photo key, the bridge, and three photographs credited to
 * her files. The obituary keeps her birth and the facts only it gives.
 *
 * Disagreements shown, not resolved: she wrote that she and Louis Garasi
 * co-chaired the 1985 effort; the Formation Committee's 1987 release calls her
 * vice chairman. The Hart district's list gives overlapping dates for her
 * presidency. What the archive does not say: that she held any office in the
 * Historical Society (the 2003 minutes record her as a visitor), or that she had
 * any part in founding SCVHistory.com.
 *
 * The script refuses unless the stored body is this morning's, word for word.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/rewrite_connie_worden_profile.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{00A0}"], ["'", "'", '"', '"', ' '], strip_tags((string)$s))));
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$ID = 16418; $p = $get($ID);
$bad = [];
if (!$p || $p->title !== 'Connie Worden') { $bad[] = '#16418 is not Connie Worden'; }
$OLD_START = 'Connie Worden-Roberts was one of the founders of the City of Santa Clarita. She co-chaired the first of the two campaigns of the 1970s';
/* Every source and the phrases the text leans on, read from the records themselves. */
$MUST = [
    28295 => ['said Connie Worden, spokesman for the Cityhood Feasibility Subcommittee of the Canyon Country and Santa Clarita Valley chambers of commerce'],
    28293 => ['"The bottom line is the pocketbook," said cityhood spokesman Connie Worden', '"If we couldn\'t pay for the city I\'d champion a cause for no city at all," she said'],
    28287 => ['I personally collected 2,000 of them by standing in front of grocery stores', 'my original involvement was primarily stimulated by Ruth and Scott Newhall, who did not favor a city, but instead favored new county formation'],
    28305 => ['Worden-Roberts personally gathered 2,000 of the 24,000 signatures needed for the petition for cityhood', 'In 1990, Connie started her own business as the Transportation Management Association', 'Worden-Roberts was given a key to the city of Santa Clarita and a Lifetime Achievement Award on May 16'],
    28303 => ['Planning Commission', 'Connie Worden'],
    28281 => ['In 1976-77, as co-chairman of the Canyon County effort with local attorney Daniel Hon', 'collecting 25 percent of the registered voters in a 250-square-mile area', 'It was not until 1985 when Louis Garasi and I co-chaired the effort that the successful "big push" occurred', 'My files were given to the Historical Society for safekeeping.'],
    28307 => ['often referred to as the "Road Warrior" for her efforts behind the development of the Cross-Valley Connector', 'host a dedication ceremony of the Connie Worden-Roberts Memorial Bridge'],
    4861 => ['according to Connie Worden, Vice Chairman of the City Formation Committee', 'collection of Connie Worden Roberts'],
    28291 => ['Spotlighted as Newhall-Saugus-Valencia Chamber of Commerce\'s Man, Woman of the Year, Connie Worden and Rene Veluzat receive their awards', 'School board member Mrs. Worden is active also on North County Planning Commission, serves as speaker for the Henry Mayo Newhall Memorial Hospital, and is past president of the Santa Clarita Valley League of Women Voters'],
    28301 => ['Connie Worden (12-1-1974)', 'Connie Worden, Clerk', 'Connie Worden, President'],
    28297 => ['Founding Board of Directors:', 'Connie Worden'],
    28299 => ['Connie Worden and Jessie Wyatt were the initial Directors of the Friends of Hart Park', 'known as "FOUNDERS"'],
    28289 => ['CWxxxx = Materials from the collection of Connie Worden-Roberts'],
    5701 => ['CONNIE WORDEN, Public Affairs Spokesperson'],
    4255 => ['4. Connie Worden'],
    28283 => ['Visitor: Connie Worden suggested that we create a foundation for the organization', 'She also presented an idea for a fundraiser. Her report is on file.'],
    28285 => ['The SCV Historical Society\'s First ANNUAL Fund Raiser', 'Connie Worden-Roberts Member'],
    28045 => ['born Constance Alice Batterman on Nov. 19, 1930, in Fairmont, Minn.'],
];
/* Credits that sit outside the body on the three photographs. */
$CREDIT = [5701 => 'Documents from the files of Connie Worden-Roberts.', 4255 => 'Collection of Connie Worden-Roberts', 4861 => 'collection of Connie Worden Roberts'];
$T = [];
foreach ($MUST as $id => $ps) {
    $e = $get($id); if (!$e) { $bad[] = "#$id missing"; continue; }
    $t = $ws($e->body) . ' ' . $ws($e->getFieldLayout()->getFieldByHandle('creditRaw') ? $e->creditRaw : '') . ' ' . $ws($e->getFieldLayout()->getFieldByHandle('webmasterNoteBottom') ? $e->webmasterNoteBottom : '') . ' ' . $ws($e->getFieldLayout()->getFieldByHandle('legacyHtml') ? $e->legacyHtml : '');
    $T[$id] = [$e, $t];
    foreach ($ps as $ph) { if (!str_contains($t, $ws($ph))) { $bad[] = "#$id does not read \"" . mb_substr($ph, 0, 60) . '"'; } }
}
foreach ($CREDIT as $id => $ph) { if (isset($T[$id]) && !str_contains($T[$id][1], $ws($ph))) { $bad[] = "#$id carries no credit \"$ph\" in its record (creditRaw, notes or page)"; } }
$t = fn($id) => $T[$id][0]->title ?? '';
$doc = fn($id, $what = 'document') => '"' . $t($id) . '," ' . $what . " #$id in this archive";

$BODY = implode("\n\n", [
    'Connie Worden-Roberts was one of the people who made the City of Santa Clarita. She spoke for the cityhood campaign from its feasibility study in 1985 to the vote of November 1987, gathered 2,000 of the petition\'s signatures herself, and sat on the new city\'s first Planning Commission. A decade earlier she had co-chaired the campaign to make the valley a county of its own, and afterward she was the valley\'s "Road Warrior" for the Cross-Valley Connector, where a bridge now bears her name.[1][2][3][4][5][6][7]',
    'In her own history of the campaigns, written in 1999, she co-chaired the Canyon County effort of 1976-77 with the attorney Daniel Hon, whose petitions gathered 25 percent of the registered voters in a 250-square-mile area. The city effort that succeeded began in 1985, when, she wrote, "Louis Garasi and I co-chaired the effort"; the City Formation Committee\'s own release of December 1987 calls her its vice chairman.[6][8] In 2007 she recalled that she had first favored a county, as Ruth and Scott Newhall did, and that she collected her signatures "by standing in front of grocery stores."[3] As spokesman she argued the city\'s case on cost. "The bottom line is the pocketbook," she told The Signal in January 1987. "If we couldn\'t pay for the city I\'d champion a cause for no city at all."[2]',
    'By 1975, when the Newhall-Saugus-Valencia Chamber of Commerce named her its Woman of the Year, she sat on the board of the William S. Hart Union High School District, served on the North County Planning Commission, spoke for Henry Mayo Newhall Memorial Hospital, and was a past president of the valley\'s League of Women Voters.[9] The district\'s list of its board members dates her seat from December 1, 1974, and names her its clerk and its president, though its dates for her presidency overlap.[10] In 1981 she was a founding director of the Friends of Hart Park, whose board in 1986 named her one of its nine "Founders."[11][12] In 1990 she started the Transportation Management Association.[4]',
    'Much of what this archive holds on the city\'s formation came from her files. The site\'s key to its photographs reserves the prefix CW for "materials from the collection of Connie Worden-Roberts," and the formation records themselves carry credits to her files: the Feasibility Committee\'s 1985 press kit, the first cityhood petition, and the Formation Committee\'s release of December 1987.[13][14][15][8] Her files from the Canyon County campaign, she wrote in 1999, "were given to the Historical Society for safekeeping." In May 2003 she came to the Historical Society\'s board meeting as a visitor, suggested that it create a foundation, and brought a written plan for its first annual fundraiser.[6][16][17]',
    'She was born Constance Alice Batterman in Fairmont, Minnesota, in 1930, and died on August 12, 2014, at 83; that May the city had given her a key to the city and a lifetime achievement award.[18][4] Her son is Leon Worden, the founder of SCVHistory.com.[18][19]',
]);
$NOTES = [
    'Karina Lutz, ' . $doc(28295) . ': "Connie Worden, spokesman for the Cityhood Feasibility Subcommittee of the Canyon Country and Santa Clarita Valley chambers of commerce."',
    $doc(28293) . '.',
    'Connie Worden-Roberts, ' . $doc(28287) . ', in the City of Santa Clarita\'s twentieth-anniversary book.',
    'Perry Smith, ' . $doc(28305) . ': "Worden-Roberts personally gathered 2,000 of the 24,000 signatures needed for the petition for cityhood."',
    $doc(28303) . '.',
    'Connie Worden-Roberts, ' . $doc(28281) . ', January 18, 1999, with her typescript.',
    $doc(28307) . '.',
    $doc(4861, 'photograph') . ': "according to Connie Worden, Vice Chairman of the City Formation Committee"; credited to the "collection of Connie Worden Roberts."',
    $doc(28291) . ', May 27, 1975.',
    $doc(28301) . '. The list gives her as president for 1977-78 and again for 1978-79, overlapping another member\'s term; it is kept as printed.',
    $doc(28297) . '.',
    $doc(28299) . ', November 25, 1986.',
    $doc(28289) . ': "CWxxxx = Materials from the collection of Connie Worden-Roberts." Most items from her files are numbered with the LW prefix and carry her name in the credit line instead.',
    $doc(5701, 'photograph') . ': "Documents from the files of Connie Worden-Roberts."',
    $doc(4255, 'photograph') . ': "Collection of Connie Worden-Roberts."',
    $doc(28283) . ': "Visitor: Connie Worden suggested that we create a foundation for the organization."',
    'Connie Worden-Roberts, ' . $doc(28285) . ', signed "Connie Worden-Roberts Member."',
    '"' . $t(28045) . '," obituary #28045 in this archive.',
    'Leon Worden\'s profile, person #279 in this archive, and its source: St. Francis Dam National Memorial Foundation, "Leon Worden Biography," 22 February 2020, https://stfrancisdammemorial.org/leon-worden-biography/.',
];
$cur = (string)$p?->body;
$done = $ws($cur) === $ws($BODY);
if (!$done && !str_starts_with($ws($cur), $ws($OLD_START))) { $bad[] = '#16418\'s body is not the one written this morning; refusing to replace it'; }
if (preg_match('~\x{2014}|inventory/~u', $BODY . implode('', $NOTES))) { $bad[] = 'an em dash or a repository path'; }
preg_match_all('~\[(\d+)\]~', $BODY, $m); if (count(array_unique($m[1])) !== count($NOTES)) { $bad[] = 'notes used ' . json_encode(array_values(array_unique($m[1]))) . ' of ' . count($NOTES); }
echo '#16418 Connie Worden: ' . ($done ? 'already rewritten' : str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes, replacing ' . str_word_count($cur) . ' words and ' . count($p?->footnotes ?? []) . ' notes') . PHP_EOL . PHP_EOL . $BODY . PHP_EOL . PHP_EOL;
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $done) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written.' . ($APPLY ? '' : ' Set $APPLY = true to apply.') . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$PROV = '; rewrite_connie_worden_profile.php, 3 Oct 2026: from her sources';
$prov = trim((string)$p->recordProvenance . $PROV); if (mb_strlen($prov) > 255) { $prov = mb_substr($prov, 0, 255); }
$p->setFieldValues(['body' => $BODY, 'footnotes' => $fn($NOTES), 'recordProvenance' => $prov]);
if (!Craft::$app->getElements()->saveElement($p)) { throw new \RuntimeException(json_encode($p->getFirstErrors())); }
$ok = $ws($get($ID)->body) === $ws($BODY);
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('rewrite_connie_worden_profile.php', 1, $ok ? 'verified' : 'SHORT', 'Connie Worden: profile rewritten from her sources; a source of the archive, shown from the photo key and credits');
if (!$ok) { throw new \RuntimeException('rewrite_connie_worden_profile: read-back failed'); }
