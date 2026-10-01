/**
 * Three sitting members of the Santa Clarita City Council (Nathan, 1 October
 * 2026): Marsha McLean #23085, Jason Gibbs #23091, Bill Miranda #23089. Same
 * treatment as Patsy Ayala: public life only, no birth date, no family.
 *
 * THE SOURCES
 *   [CITY]    The City's own biography of each member, read 1 October 2026
 *             (inventory/sources/santa-clarita-council-2026-10-01.json, family
 *             sentences omitted). Their careers outside the council rest on it
 *             alone and are given as the City's account.
 *   [ARCHIVE] Their candidacies and office holdings, checked here against the
 *             figures the text gives.
 *
 * Kept visible, not settled: Miranda's City page gives his mayoral years as
 * 2021 and 2024 in its heading and 2021 and 2025 in its text. Gibbs's office
 * holding in the archive ends with his first term in December 2024 and no 2024
 * race is recorded for him; the City lists him as a sitting member, and the
 * profile says the archive lacks the 2024 record rather than guess how he
 * continued.
 *
 * Portraits: upscales Nathan made of the City's WebP photographs, imported on
 * his word with the credential noted.
 *
 * Fills empty bodies only. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_council_profiles.php'))"
 */

use craft\elements\{Entry, Asset};

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $elements = Craft::$app->getElements();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$city = json_decode((string)@file_get_contents("$root/inventory/sources/santa-clarita-council-2026-10-01.json"), true)['sources'] ?? [];
$ARCH = 'Archive records: the City Council elections of %s, with their returns, and the office holdings for each term.';
$P = [
  'mclean' => ['id' => 23085, 'name' => 'Marsha McLean', 'file' => 'MarshaMclean.jpg', 'sha' => '43c1c8e69bbaee6ce3bde745fdb3e78945e570ed7b9d55bed644b9222ff1d527', 'as' => 'marsha-mclean-edited.jpg',
    'urn' => 'de17d4de-fcac-449b-9503-d7ae3cab5819', 'when' => '2026-10-01 15:04',
    'votes' => ['1998-04-14' => [4531, 'not-elected'], '2000-04-11' => [4201, 'not-elected'], '2002-04-09' => [6117, 'elected'], '2006-04-11' => [5564, 'elected'], '2010-04-13' => [6831, 'elected'], '2014-04-08' => [5677, 'elected'], '2018-11-06' => [25273, 'elected'], '2022-11-08' => [28352, 'elected']],
    'must' => ['Mayoral Terms: 4 (2007, 2011, 2015, 2019)', 'Program Analyst for special projects for the City of Santa Clarita', 'worked for the Los Angeles Police Department and for a Los Angeles City Councilman', 'American Embassies in Tel Aviv, Israel and Paris, France', 'U.S. Information Agency', 'founder of the S.C.V. Canyons Preservation Committee, which successfully co-sponsored legislation to acquire funds for the preservation of Whitney and Elsmere Canyons', 'save Santa Clarita from being home to the world’s largest garbage dump', '2007-2008 President of the Los Angeles County Division of the League of California Cities', 'Transportation Policy Committee of the Southern California Association of Governments', 'founded the SCV Transportation Coalition', 'founder of the North Los Angeles County Cities Protection Coalition', 'High-Speed Rail', 'small business owner'],
    'body' => [
      'Marsha McLean has served on the Santa Clarita City Council since 2002, elected six times, and has been the city\'s mayor four times, in 2007, 2011, 2015 and 2019.[1][2]',
      'She stood for the council twice before she won: in April 1998, fifth of fifteen candidates with 4,531 votes, and in April 2000, third of eleven with 4,201. She was elected in April 2002, second of twelve with 6,117 votes; re-elected in 2006 and 2010, first both times, with 5,564 and 6,831; in 2014, second of thirteen with 5,677; and, after the city moved its elections to November, in 2018 with 25,273 votes and in 2022 with 28,352.[2] The term won in April 2014 ran to December 2018 because of the move.[2]',
      'The City\'s biography says that before the council she was a program analyst for special projects for the City of Santa Clarita, and before that worked for the Los Angeles Police Department and for a Los Angeles city councilman, and for the U.S. government at the American embassies in Tel Aviv and Paris, where she was a liaison to French citizens on the U.S. Information Agency\'s cultural exchange programs. It describes her as a small business owner.[1]',
      'Her public causes, as the City gives them, have been the canyons and transportation. She founded the S.C.V. Canyons Preservation Committee, which co-sponsored legislation to fund the preservation of Whitney and Elsmere Canyons, and organized opposition to a landfill proposed for Elsmere Canyon, which the City\'s biography calls the world\'s largest. She was president of the Los Angeles County Division of the League of California Cities in 2007 and 2008 and has sat on the state league\'s board; she represents the city and the North County on the Southern California Association of Governments\' Transportation Policy Committee; and she founded the SCV Transportation Coalition and the North Los Angeles County Cities Protection Coalition, formed to oppose the effects of the proposed High-Speed Rail routes on the communities along them.[1]',
    ], 'elections' => '1998, 2000, 2002, 2006, 2010, 2014, 2018 and 2022'],
  'gibbs' => ['id' => 23091, 'name' => 'Jason Gibbs', 'file' => 'JasonGibbs.jpg', 'sha' => 'f7a0cb1e7a6e7af527739c4c5626bac942dcb3b73200cc5d6c8dcb3adde04603', 'as' => 'jason-gibbs-edited.jpg',
    'urn' => '33085b59-f8d8-412d-a6b0-ee1ac55be7a5', 'when' => '2026-10-01 15:09',
    'votes' => ['2018-11-06' => [10008, 'not-elected'], '2020-11-03' => [29474, 'elected']],
    'must' => ['Year Elected: 2020', 'Mayoral Terms: 1 (2023)', 'joined the City of Santa Clarita as a first time Councilmember in December 2020', 'Advisory Board for the Boys and Girls Club and Executive Boards for the Valley Industry Association and the WiSH Foundation', 'Cal Poly with his BS and MS in Mechanical Engineering', 'GP Strategies Corporation as the Senior Principal Engineer of West Coast Operations', 'Delta II, Delta IV and Atlas V', 'Ground Support Equipment'],
    'body' => [
      'Jason Gibbs was elected to the Santa Clarita City Council in November 2020 and took his seat that December; he was the city\'s mayor in 2023.[1][2]',
      'He first stood in 2018 and came ninth of fifteen candidates with 10,008 votes. In 2020 he was elected second of nine, with 29,474.[2] The archive\'s record of his office ends with that first term in December 2024, and it does not yet hold a record of his seat in the 2024 elections; the City lists him as a sitting member in 2026.[1][2]',
      'The City\'s biography says he holds bachelor\'s and master\'s degrees in mechanical engineering from Cal Poly and works in the aerospace industry as senior principal engineer of West Coast operations for GP Strategies Corporation, on the ground support equipment for rocket launch programs including the Delta II, Delta IV and Atlas V. Before the council he served on the advisory board of the Boys and Girls Club and on the executive boards of the Valley Industry Association and the WiSH Foundation.[1]',
    ], 'elections' => '2018 and 2020'],
  'miranda' => ['id' => 23089, 'name' => 'Bill Miranda', 'file' => 'BillMiranda.jpg', 'sha' => 'bcb17dbcf8e95576a0a6740825537f96eb17371d2ee5f4181c49281365279cf7', 'as' => 'bill-miranda-edited.jpg',
    'urn' => 'f1f09fd8-bbec-4d5e-8787-220ffd3e3105', 'when' => '2026-10-01 15:10', 'related' => [341],
    'votes' => ['2018-11-06' => [18885, 'elected'], '2022-11-08' => [32306, 'elected']],
    'must' => ['Appointed January 8, 2017 to Fill Vacancy', 'Mayoral Terms: 2 (2021, 2024)', 'He served as Mayor in 2021 and 2025', 'appointed to the council seat vacated by Dante Acosta in January 2017', 'Air Force Veteran', 'former CEO of the Santa Clarita Valley Latino Chamber of Commerce', 'IBM, Xerox and Data General', 'president of Real SCV and CEO of the Santa Clarita Arts and Culture Center', 'hosting over 200 episodes of SCV 101 on SCVTV', 'Profiles in Latino Courage, A New Kind of Mayor and Faith That Burns Bright', 'lived in Santa Clarita for the last 43 years'],
    'body' => [
      'Bill Miranda was appointed to the Santa Clarita City Council on 8 January 2017, to the seat Dante Acosta had vacated, and has since been elected to it twice, in 2018 and 2022.[1][2] He has been the city\'s mayor twice: in 2021, and in 2024 or 2025, since the City\'s page gives both.[1]',
      'He was elected in November 2018, third of fifteen candidates with 18,885 votes, and re-elected in November 2022, second of nine with 32,306.[2]',
      'The City\'s biography describes him as an Air Force veteran, a business owner, a former chief executive of the Santa Clarita Valley Latino Chamber of Commerce, and the host of more than two hundred episodes of SCV 101 on SCVTV. It says he worked for IBM, Xerox and Data General, and that he is president of Real SCV and chief executive of the Santa Clarita Arts and Culture Center. He is the author of three books, Profiles in Latino Courage, A New Kind of Mayor and Faith That Burns Bright, and, the City says, has lived in Santa Clarita for forty-three years.[1]',
    ], 'elections' => '2018 and 2022'],
];
$bad = []; $plan = [];
foreach ($P as $k => $d) {
    $p = Entry::find()->id($d['id'])->status(null)->one();
    if (!$p || $p->title !== $d['name']) { $bad[] = "#{$d['id']} is not {$d['name']}"; continue; }
    $text = $city[$k]['passage'] ?? '';
    foreach ($d['must'] as $ph) { if (!str_contains($text, $ph)) { $bad[] = "$k: the City's page does not read \"$ph\""; } }
    foreach (Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $p, 'field' => 'candidacyPerson'])->all() as $c) {
        $date = (string)$c->candidacyElection->one()?->electionDateEdtf;
        if (!isset($d['votes'][$date]) || $d['votes'][$date] !== [(int)$c->votes, (string)$c->outcome->value]) { $bad[] = "$k: the $date candidacy reads " . (int)$c->votes . ' ' . $c->outcome->value . ', not as written'; }
        unset($d['votes'][$date]);
    }
    if ($d['votes']) { $bad[] = "$k: no candidacy for " . implode(', ', array_keys($d['votes'])); }
    $path = "$root/inventory/incoming/{$d['file']}";
    if (!is_file($path) || hash_file('sha256', $path) !== $d['sha']) { $bad[] = "{$d['file']} is missing or not the file received"; }
    $body = implode("\n\n", $d['body']);
    $notes = ['City of Santa Clarita, "' . $city[$k]['title'] . '," ' . $city[$k]['url'] . ', read 1 October 2026: the City\'s own biography of a sitting member.', sprintf($ARCH, $d['elections'])];
    if (preg_match('~\x{2014}~u', $body . implode('', $notes))) { $bad[] = "$k: an em dash in the text"; }
    if (preg_match('~\b(husband|wife|children|grandchildren|daughters|Chandra|Virginia)\b~i', $body)) { $bad[] = "$k: family detail in the body"; }
    $cur = trim((string)$p->body);
    if ($cur && $cur !== trim($body)) { $bad[] = "#{$d['id']} has a body already"; }
    $plan[$k] = [$p, $body, $notes];
    echo str_pad($d['name'], 16) . ($cur ? 'body already written' : 'body empty -> ' . str_word_count($body) . ' words, 2 notes') . '; portrait ' . (Asset::find()->filename($d['as'])->exists() ? 'already imported' : "import {$d['file']}") . PHP_EOL;
}
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'outside/']);
$tx = Craft::$app->getDb()->beginTransaction();
try {
    foreach ($P as $k => $d) {
        [$p, $body, $notes] = $plan[$k];
        $a = Asset::find()->filename($d['as'])->one();
        if (!$a) {
            $tmp = sys_get_temp_dir() . '/' . $d['as']; copy("$root/inventory/incoming/{$d['file']}", $tmp);
            $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($d['as']); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
            $a->setFieldValues(['license' => 'unknown', 'provenanceKind' => 'commissioned', 'acquiredDate' => '2026-10-01', 'enhancedBy' => 'Nathan Imhoff', 'enhancedDate' => new \DateTime('2026-10-01'),
                'enhancementMethod' => 'Upscaled by Nathan Imhoff (a Firefly upscale of a WebP, per the content credential).',
                'contentCredentials' => "Adobe content credential (C2PA), https://cai-manifests.adobe.com/manifests/urn-c2pa-{$d['urn']}-adobe\nSteps recorded (UTC): {$d['when']} upscaled (Firefly creative upsampler), from a WebP.\nA note, not a refusal.",
                'source' => "Source per Nathan Imhoff (the City of Santa Clarita's council portrait). Upscaled by Nathan Imhoff. Received as {$d['file']}, SHA-256 {$d['sha']}; the stored copy is re-encoded on import."]);
            if (!$elements->saveElement($a)) { throw new \RuntimeException("{$d['file']}: " . json_encode($a->getFirstErrors())); }
        }
        $p = Entry::find()->id($d['id'])->status(null)->one(); $h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
        $vals = ['featuredImage' => [$a->id]];
        if (trim((string)$p->body) === '') {
            $vals += ['body' => $body, 'footnotes' => $fn($notes), 'bodyAuthorship' => 'editorial-2026', 'occupation' => 'City council member',
                'recordProvenance' => trim((string)$p->recordProvenance . '; build_council_profiles.php, 1 Oct 2026: profile from the City\'s biography and the archive\'s returns; portrait per Nathan Imhoff', '; ')];
            if (!empty($d['related'])) { $vals['relatedPersons'] = array_values(array_unique(array_merge($p->relatedPersons->status(null)->ids(), $d['related']))); }
        }
        $p->setFieldValues(array_intersect_key($vals, array_flip($h)));
        if (!$elements->saveElement($p)) { throw new \RuntimeException("#{$d['id']}: " . json_encode($p->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$short = []; $urls = [];
foreach ($P as $k => $d) { $s = Entry::find()->id($d['id'])->status(null)->one(); if (trim((string)$s->body) !== trim($plan[$k][1]) || $s->featuredImage->one()?->filename !== $d['as']) { $short[] = $d['name']; } $urls[] = $s->url; }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: ' . implode(' ', $urls)) . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_council_profiles.php', count($P) * 2, $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'McLean, Gibbs, Miranda: public-life profiles and portraits');
if ($short) { throw new \RuntimeException('build_council_profiles: ' . implode('; ', $short)); }
