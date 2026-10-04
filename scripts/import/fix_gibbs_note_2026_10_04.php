/**
 * Gibbs's District 3 term (#27410), footnote 1: it said his seat was "one of the two elected that November" and that "the
 * District 3 canvass is not yet in the archive"; no election was held for District 3 (fix_sole_candidate_terms_2026_10_04.php,
 * footnote 2). The note is rewritten to say how the term's end is derived and nothing more.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 */
$APPLY = false;
$h = craft\elements\Entry::find()->id(27410)->one(); $rows = $h->footnotes;
$NEW = 'The term\'s end is derived: four years from December 2024, when he took the District 3 seat. The City lists him as "Councilmember Jason Gibbs, District 3" (City of Santa Clarita, City Council, https://santaclarita.gov/city-council/, read 3 October 2026), and under Ordinance 23-4 District 3 is next filled in 2028.';
$i = array_search(true, array_map(fn($r) => str_contains($r['note'], 'District 3 canvass') || str_contains($r['note'], 'one of the two elected'), $rows), true);
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ': ' . ($i === false ? 'nothing to fix' : "replace note " . $rows[$i]['number']) . PHP_EOL;
if (!$APPLY || $i === false) { return; }
$rows[$i]['note'] = $NEW; $rows[$i]['col2'] = $NEW; $h->setFieldValue('footnotes', $rows); if (!Craft::$app->getElements()->saveElement($h)) { throw new \RuntimeException('save'); }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('fix_gibbs_note_2026_10_04.php', 1, 'verified', 'Gibbs District 3: the derived-end note no longer says the seat was elected');
echo 'done' . PHP_EOL;
