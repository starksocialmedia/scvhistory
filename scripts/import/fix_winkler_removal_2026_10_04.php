/**
 * Stephen Winkler's Saugus Union term (#28493) read "elected 2011; end not recorded" and ran to December
 * 2016. He was removed from office (Nathan, 4 October 2026: "Correct it ... how it ended 'removed from
 * office', sourced ... The residency ground is the legal basis and should be stated plainly. Whatever else
 * was alleged, say only what a source states and attribute it").
 *
 * What the sources, saved in inventory/news/office-gaps-2026-10-03/ and inventory/news/winkler-2026-10-04/,
 * establish:
 *   18 June 2013  the board voted 4 to 1, at a special hearing, to vacate his seat on the ground that he did
 *                 not reside in the district, as state law requires (Perry Smith, Hometown Station, 19 June
 *                 2013; 25 July 2013).
 *   then          the County Office of Education required a quo warranto proceeding (25 July 2013).
 *   11 Feb 2014   the Attorney General granted the district leave to sue in quo warranto "to determine
 *                 whether proposed defendant STEPHEN WINKLER meets the legal residency requirements"
 *                 (Opinion No. 13-902, as published in full by SCVNews.com, 12 February 2014).
 *   late Feb 2014 the district filed the suit (20 March 2014).
 *   June 2014     the Superior Court granted it (Perry Smith, 19 July 2014: "The board then sought legal
 *                 action against Winkler in Los Angeles County Superior Court, which was granted in June").
 * So the term ends June 2014, removed from office, and the 2013 vote is stated as the board's act. The
 * conduct reported in June 2013 is not recorded; the ground is residency, as every source states it.
 * Also adds "Removed from office" (removed) to the holdings' howEnded options.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_winkler_removal_2026_10_04.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database and project config' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $fs = Craft::$app->getFields(); $bad = [];
$N = "$root/inventory/news";
$T = fn($f) => preg_replace('~\s+~u', ' ', (string)@file_get_contents("$N/$f.txt"));
$need = [
    ['office-gaps-2026-10-03/winkler-wont-fight', 'The Saugus Union School District governing board members voted 4-1 to vacate the seat held by Stephen S. Winkler on Tuesday night.'],
    ['office-gaps-2026-10-03/winkler-wont-fight', 'after the board voted to vacate his seat on grounds he did not maintain a residence in the school district, as state law requires.'],
    ['winkler-2026-10-04/saugus-board-needs-attorney-general-ok-to-remove-winkler', 'voted 4-1 to remove Stephen Winkler on June 18 at a special hearing. In accordance with the state’s Education Code, the board was required to notify the Los Angeles County Office of Education, which asked the district to initiate the quo warranto proceeding.'],
    ['winkler-2026-10-04/attorney-generals-findings-in-the-winkler-matter-full-text', 'Leave to sue is GRANTED to determine whether proposed defendant STEPHEN WINKLER meets the legal residency requirements for holding the public office of School District Trustee.'],
    ['winkler-2026-10-04/winkler-vows-to-fight-ouster-from-school-board', 'We filed the quo warranto lawsuit in late February'],
    ['winkler-2026-10-04/cal-lutheran-administrator-picked-to-fill-winkler-seat', 'The board then sought legal action against Winkler in Los Angeles County Superior Court, which was granted in June.'],
];
foreach ($need as [$f, $q]) { if (!str_contains($T($f), $q)) { $bad[] = "$f does not read: " . mb_substr($q, 0, 60); } }
$NOTES = [
    'The Saugus Union board voted 4 to 1 on 18 June 2013, at a special hearing, to vacate his seat "on grounds he did not maintain a residence in the school district, as state law requires" (Perry Smith, "Winkler Says He Won\'t Fight Ouster from School Board," Hometown Station, as carried on SCVNews.com, 19 June 2013, https://scvnews.com/winkler-says-he-wont-fight-boards-decision-to-oust-him; and "Saugus Board Needs Attorney General OK to Remove Winkler," 25 July 2013, https://scvnews.com/saugus-board-needs-attorney-general-ok-to-remove-winkler/). The Los Angeles County Office of Education then required the district to proceed in quo warranto (the same, 25 July 2013).',
    'The Attorney General granted the district leave to sue "to determine whether proposed defendant STEPHEN WINKLER meets the legal residency requirements for holding the public office of School District Trustee" (Office of the Attorney General, Opinion No. 13-902, 11 February 2014, as published in full by SCVNews.com, 12 February 2014, https://scvnews.com/attorney-generals-findings-in-the-winkler-matter-full-text/). The district filed the suit in late February 2014 (Hometown Station, 20 March 2014, https://scvnews.com/winkler-vows-to-fight-ouster-from-school-board/).',
    'The Los Angeles County Superior Court granted the action in June 2014; the board filled the seat by appointing David Powell in July 2014 (Perry Smith, "Cal Lutheran Instructor Picked to Fill Winkler Seat," Hometown Station, as carried on SCVNews.com, 19 July 2014, https://scvnews.com/cal-lutheran-administrator-picked-to-fill-winkler-seat/).',
];
$h = Entry::find()->id(28493)->status(null)->one();
if ($h?->holdingPerson->one()?->title !== 'Stephen Winkler') { $bad[] = '#28493 is not Winkler\'s term'; }
$done = $h && $h->howEnded->value === 'removed';
$field = $fs->getFieldByHandle('howEnded'); $hasOpt = $field && array_filter($field->options, fn($o) => ($o['value'] ?? '') === 'removed');
echo 'howEnded option "removed": ' . ($hasOpt ? 'exists' : 'add "Removed from office"') . PHP_EOL;
echo '#28493 Stephen Winkler: ' . ($done ? 'already removed' : 'December 2011 to June 2014, removed from office; footnotes 2 and 3 replaced by the three on the removal') . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
if (!$hasOpt) { $opts = $field->options; $opts[] = ['label' => 'Removed from office', 'value' => 'removed', 'default' => false]; $field->options = $opts; if (!$fs->saveField($field)) { throw new \RuntimeException('howEnded: ' . json_encode($field->getErrors())); } }
if (!$done) {
    $h = Entry::find()->id(28493)->status(null)->one(); $old = $h->footnotes;
    $rows = [['number' => '1', 'note' => (string)$old[0]['note'], 'source' => 'editorial-2026']];
    foreach ($NOTES as $i => $nt) { $rows[] = ['number' => (string)($i + 2), 'note' => $nt, 'source' => 'editorial-2026']; }
    $rows[] = ['number' => (string)(count($NOTES) + 2), 'note' => (string)$old[3]['note'], 'source' => 'editorial-2026'];
    $h->setFieldValues(['termEnd' => 'June 2014', 'termEndEdtf' => '2014-06', 'howEnded' => 'removed', 'endEvidence' => 'contemporary', 'footnotes' => $rows]);
    if (!$el->saveElement($h)) { throw new \RuntimeException('#28493: ' . json_encode($h->getFirstErrors())); }
}
$ok = Entry::find()->id(28493)->status(null)->one()->howEnded->value === 'removed';
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('fix_winkler_removal_2026_10_04.php', 1, $ok ? 'verified' : 'SHORT', 'Stephen Winkler removed from office (residency), board vote 2013, quo warranto granted June 2014; howEnded option "removed"');
if (!$ok) { throw new \RuntimeException('fix_winkler_removal: read-back failed'); }
