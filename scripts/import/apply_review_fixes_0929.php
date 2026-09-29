/**
 * Nathan's review of the pages, 29 September 2026: the record fixes.
 *
 *  1. The Hart district is known as "Hart District" and nothing else; the other
 *     districts lose their all-capitals directory forms.
 *  2. Maria Gutzeit is linked to SCV Water (#402), the body she has served on
 *     since 2003 (it holds the Newhall County Water District as an alias).
 *  3. Cameron Smyth: born August 19, 1971, as Nathan gives it. No official
 *     record read carries it; the footnote says so.
 *  4. The Smyths' council terms as officeHolding records, the first in the
 *     archive, each sourced: Clyde 1994-1998 and mayor in 1997; Cameron
 *     2000-2006 and 2016-2024. With them both men are documented public
 *     officeholders, and Cameron's footnote already cites the obituary naming
 *     his son, so the family rule's one exception applies and the father-son
 *     link shows on both pages. Cameron's Assembly terms wait for an
 *     organization record for the Assembly, which the archive does not hold.
 *  5. Clyde Smyth's portrait: the image of photograph #4533, "H. Clyde Smyth,
 *     Ed.D., Superintendent", already tagged to him.
 *  6. Remi Nadeau #18869 goes back to its plain title: a relationship goes in a
 *     relation, not in a name. He is linked to his grandfather #339, and the
 *     editor note says who he is.
 *  7. The war memorial pages: 36 of 54 carried their legacy page key as their
 *     address (/war-memorial/terror-rudyacosta). Each takes its name
 *     (/war-memorial/rudy-alexander-acosta); the old address redirects
 *     (config/redirects.php, same commit).
 *
 * Fills empty fields and adds relations; changes a value only where named above.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/apply_review_fixes_0929.php'))"
 */

use craft\elements\Entry;
use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

$elements = Craft::$app->getElements(); $svc = Craft::$app->getEntries();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$lines = fn($v) => array_values(array_filter(array_map('trim', preg_split('~[\n]~', (string)$v))));
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$ops = [];
$CITY = 394; $COUNCIL = 18327; $MAYOR = 18431; $CAMERON = 16380; $CLYDE = 15985;
$CLERK = 'City of Santa Clarita, City Clerk, Historical Election Results, https://santaclarita.gov/city-clerk/wp-content/uploads/sites/8/2023/06/historical-results-7.pdf';
$OBIT = 'Obituary of H. Clyde Smyth, Dignity Memorial, 2012, https://www.dignitymemorial.com/obituaries/newhall-ca/h-smyth-4971797';
$SCVMW = 'Santa Clarita Valley Man and Woman of the Year, "Clyde Smyth," https://scvmw.org/previous-award-winners/past-winners-biographies/clyde-smyth/';
$CITY_BIO = 'City of Santa Clarita, "Cameron Smyth," https://santaclarita.gov/city-council/cameron-smyth/';

/* 1 */
echo '1. DISTRICT ALIASES' . PHP_EOL;
foreach (['0642510', '0627180', '0635970', '0638220', '0607740'] as $lea) {
    $d = Entry::find()->section('organizations')->status(null)->ncesId($lea)->one();
    if (!$d) { continue; }
    $cur = $lines($d->orgAliases);
    $new = $lea === '0642510' ? ['Hart District'] : array_values(array_filter($cur, fn($a) => $a !== strtoupper($a)));
    if ($new === $cur) { continue; }
    echo "   #{$d->id} {$d->title}: " . implode(' / ', $cur) . ' -> ' . implode(' / ', $new) . PHP_EOL;
    $ops[] = function () use ($d, $new, $get, $elements) { $e = $get($d->id); $e->setFieldValue('orgAliases', implode("\n", $new)); return $elements->saveElement($e) ? '' : "#{$d->id} save"; };
}

/* 2 */
echo PHP_EOL . '2. GUTZEIT AND SCV WATER' . PHP_EOL;
$g = $get(21582); $water = $get(402);
if ($g && $water && !in_array(402, array_map('intval', $g->personOrganizations->status(null)->ids()), true)) {
    echo "   #21582 Maria Gutzeit: personOrganizations + #402 {$water->title}" . PHP_EOL;
    $ops[] = function () use ($get, $elements) { $e = $get(21582); $e->setFieldValue('personOrganizations', array_merge(array_map('intval', $e->personOrganizations->status(null)->ids()), [402])); return $elements->saveElement($e) ? '' : 'gutzeit save'; };
}

/* 3 */
echo PHP_EOL . '3. CAMERON SMYTH\'S BIRTHDAY' . PHP_EOL;
$c = $get($CAMERON);
if (trim((string)$c->birthDateEdtf) === '') {
    echo '   birthDate "August 19, 1971", EDTF 1971-08-19, evidence uncited, with a footnote saying where it came from' . PHP_EOL;
    $ops[] = function () use ($get, $CAMERON, $elements) {
        $e = $get($CAMERON);
        $notes = array_values(array_filter($e->footnotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== ''));
        $notes[] = ['number' => (string)(count($notes) + 1), 'source' => 'editorial-2026', 'note' => 'Birth date: August 19, 1971, given by the editor, Nathan Imhoff, 29 September 2026. No official record read carries it; the City\'s biography does not.'];
        $e->setFieldValues(['birthDate' => 'August 19, 1971', 'birthDateEdtf' => '1971-08-19', 'birthEvidence' => 'uncited', 'footnotes' => $notes]);
        return $elements->saveElement($e) ? '' : 'cameron save';
    };
}

/* 4 */
echo PHP_EOL . '4. OFFICE HOLDINGS' . PHP_EOL;
$HOLDINGS = [
    [$CLYDE, $COUNCIL, '1994', '1994', '1998', '1998', 'elected', 'unknown', 'certified', 'retrospective',
        [$CLERK . ', April 12, 1994: "Clyde Smyth 3,804," elected; not a candidate in April 1998.', $OBIT . ': "elected to the City Council in 1994." ' . $SCVMW . '.']],
    [$CLYDE, $MAYOR, '1997', '1997', '1997', '1997', 'rotated', 'expired', 'retrospective', 'retrospective',
        [$OBIT . ': "a term as Mayor in 1997."', $SCVMW . ': mayor in 1997.']],
    [$CAMERON, $COUNCIL, '2000', '2000', '2006', '2006', 'elected', 'left', 'certified', 'retrospective',
        [$CLERK . ', April 11, 2000: Cameron Smyth first of eleven; re-elected April 13, 2004.', $CITY_BIO . ': "In 2000, Smyth was elected to the Santa Clarita City Council and served six years"; he left for the State Assembly.']],
    [$CAMERON, $COUNCIL, '2016', '2016', 'December 10, 2024', '2024-12-10', 'elected', 'expired', 'certified', 'contemporary',
        ['Los Angeles County, Statement of Votes Cast, 8 November 2016, https://santaclarita.gov/city-clerk/wp-content/uploads/sites/8/2023/06/2016StatementofVotesCast-4.pdf: Smyth 30,109, second, elected. Re-elected 3 November 2020 (Ballotpedia).', 'City of Santa Clarita, City Council meeting of 10 December 2024, https://santaclarita.gov/city-council/blog/2024/12/11/december-10-2024/: "Outgoing Mayor Cameron Smyth was honored."']],
];
$ohSec = $svc->getSectionByHandle('officeHoldings'); $ohType = $svc->getEntryTypeByHandle('officeHolding');
$holdPlan = [];
foreach ($HOLDINGS as $h) {
    [$pid, $office, $sP, $sE, $eP, $eE] = $h;
    $exists = Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $pid, 'field' => 'holdingPerson'], ['targetElement' => $office, 'field' => 'holdingOffice']])->all();
    if (array_filter($exists, fn($x) => trim((string)$x->termStartEdtf) === $sE)) { continue; }
    echo '   ' . $get($pid)->title . ', ' . $get($office)->title . ', The City of Santa Clarita, ' . $sP . ' to ' . $eP . PHP_EOL;
    $holdPlan[] = $h;
}
if ($holdPlan) { $ops[] = function () use ($holdPlan, $ohSec, $ohType, $CITY, $fn, $elements) {
    foreach ($holdPlan as [$pid, $office, $sP, $sE, $eP, $eE, $how, $ended, $sEv, $eEv, $notes]) {
        $e = new Entry(); $e->sectionId = $ohSec->id; $e->setTypeId($ohType->id);
        $e->setFieldValues(['holdingPerson' => [$pid], 'holdingOffice' => [$office], 'holdingBody' => [$CITY], 'termStart' => $sP, 'termStartEdtf' => $sE, 'termEnd' => $eP, 'termEndEdtf' => $eE,
            'selectionMethod' => $how, 'howEnded' => $ended, 'startEvidence' => $sEv, 'endEvidence' => $eEv, 'footnotes' => $fn($notes),
            'recordProvenance' => 'apply_review_fixes_0929.php, 29 September 2026']);
        if (!$elements->saveElement($e)) { return 'holding ' . json_encode($e->getFirstErrors()); }
    }
    return '';
}; }

/* 5 */
echo PHP_EOL . '5. CLYDE SMYTH\'S PORTRAIT' . PHP_EOL;
$clyde = $get($CLYDE); $photo = $get(4533);
$img = Asset::find()->filename(strtolower(trim((string)$photo->photoSourceCode)) . '.jpg')->one();
if (!$clyde->featuredImage->exists() && $img) {
    echo "   featuredImage -> #{$img->id} {$img->filename}, the image of photograph #4533 \"{$photo->title}\"" . PHP_EOL;
    $ops[] = function () use ($get, $CLYDE, $img, $elements) { $e = $get($CLYDE); $e->setFieldValue('featuredImage', [$img->id]); return $elements->saveElement($e) ? '' : 'clyde save'; };
}

/* 6 */
echo PHP_EOL . '6. REMI NADEAU' . PHP_EOL;
$n = $get(18869);
$NOTE = ['heading' => 'Two men named Remi Nadeau', 'position' => 'bottom',
    'note' => 'This Remi Nadeau is the grandson of the Los Angeles freighter Remi Allen Nadeau (1821-1887), who is linked here. The grandson owned the Soledad Canyon ranch and the deer park photographed about 1929. Accounts of the valley, Jerry Reynolds\' among them, often run the two men together; the records that mean the freighter point at him, not here.'];
$notes = array_values(array_filter(array_filter($n->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== ''), fn($r) => ($r['heading'] ?? '') !== $NOTE['heading']));
$needTitle = $n->title !== 'Remi Nadeau';
$needRel = !in_array(339, array_map('intval', $n->relatedPersons->status(null)->ids()), true);
if ($needTitle || $needRel) {
    echo '   #18869 "' . $n->title . '" -> "Remi Nadeau"; relatedPersons + #339 Remi Allen Nadeau (his grandfather); editor note rewritten' . PHP_EOL;
    $ops[] = function () use ($get, $NOTE, $notes, $elements) {
        $e = $get(18869); $e->title = 'Remi Nadeau';
        $e->setFieldValue('relatedPersons', array_values(array_unique(array_merge(array_map('intval', $e->relatedPersons->status(null)->ids()), [339]))));
        $e->setFieldValue('editorNotes', array_merge($notes, [$NOTE]));
        return $elements->saveElement($e) ? '' : 'nadeau save';
    };
}

/* 7 */
echo PHP_EOL . '7. WAR MEMORIAL ADDRESSES' . PHP_EOL;
$renames = [];
foreach (Entry::find()->section('warMemorials')->status(null)->all() as $w) {
    $want = \craft\helpers\ElementHelper::generateSlug($w->title);
    if ($w->slug !== $want) { $renames[$w->id] = [$w->slug, $want]; }
}
echo '   ' . count($renames) . ' records, e.g. ' . implode(', ', array_map(fn($r) => $r[0] . ' -> ' . $r[1], array_slice($renames, 0, 3))) . PHP_EOL;
if ($renames) { $ops[] = function () use ($renames, $get, $elements) {
    foreach ($renames as $id => [$old, $new]) { $e = $get($id); $e->slug = $new; if (!$elements->saveElement($e)) { return "#$id slug"; } }
    return '';
}; }

echo PHP_EOL . 'SUMMARY: ' . count($ops) . ' change groups.' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$short = [];
foreach ($ops as $op) { if ($err = $op()) { $short[] = $err; } }
/* Read back the parts a page shows. */
if (count(Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $CAMERON, 'field' => 'holdingPerson'])->ids()) < 2) { $short[] = 'Cameron holdings'; }
if (!$get($CLYDE)->featuredImage->exists()) { $short[] = 'Clyde portrait'; }
if ($get(18869)->title !== 'Remi Nadeau') { $short[] = 'Nadeau title'; }
if ($get(526)->slug !== 'rudy-alexander-acosta') { $short[] = 'war memorial slugs'; }
if ($lines($get(21588)->orgAliases) !== ['Hart District']) { $short[] = 'Hart aliases'; }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('apply_review_fixes_0929.php', count($ops), $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'aliases, Gutzeit water, Smyth birthday, holdings and portrait, Nadeau, war memorial slugs');
if ($short) { throw new \RuntimeException('apply_review_fixes_0929: ' . implode('; ', $short)); }
