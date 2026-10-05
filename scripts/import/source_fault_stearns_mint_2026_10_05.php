/**
 * Stearns's gold at the mint: a source disagreement, not a record error (Nathan, 5 October 2026: "a source disagreement
 * rather than a record error is the right reading. Make it a source fault once Reggie is back and you can read the voucher").
 * Abel Stearns's record (#309) says the gold was deposited at the Philadelphia mint on 8 July 1843, following the mint's
 * memorandum as Alfred Robinson copied it in his letter of 6 August 1843 (printed with Stearns's letter of 1867, archive
 * document #26983). John Murray, "California's Discovery of Gold in 1841," Overland Monthly, May 1892, as carried on
 * SCVHistory.com (/scvhistory/overlandmonthly0592.htm, read 5 October 2026), prints that memorandum and the Treasury's
 * answer of 1891: the mint found no record of the deposit; the First Comptroller found "a deposit of gold by Grant & Stone
 * June 8th, 1843, as evidenced by voucher No. 150 [sic]", the certified copy calling it Voucher No. 350. The record keeps
 * 8 July; the fault says both, under the text of his page (templates/persons/_entry.twig). Not decided.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/source_fault_stearns_mint_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$s = Entry::find()->id(309)->status(null)->one(); $d = Entry::find()->id(26983)->status(null)->one();
if ($s?->title !== 'Abel Stearns' || !$d || !str_contains((string)$s->body, 'deposited on 8 July 1843')) { throw new \RuntimeException('Stearns or his letter not as expected'); }
$have = array_filter(Entry::find()->section('sourceFaults')->status(null)->relatedTo(['targetElement' => $s, 'field' => 'faultRecord'])->all(), fn($f) => (string)$f->faultField === 'body');
$BASIS = 'The sources disagree on the day the gold was deposited at the mint. Alfred Robinson\'s copy of the mint\'s memorandum, in his letter of August 6, 1843, gives "the 8th day of July, 1843." In 1891 the mint found no record of the deposit, and the Treasury\'s First Comptroller found "a deposit of gold by Grant & Stone June 8th, 1843," by a voucher whose certified copy is numbered 350 (John Murray, "California\'s Discovery of Gold in 1841," Overland Monthly, May 1892).';
echo 'Stearns source fault: ' . ($have ? 'exists' : 'create') . "\n  $BASIS\n";
if ($APPLY && !$have) {
  $sec = Craft::$app->getEntries()->getSectionByHandle('sourceFaults'); $f = new Entry(); $f->sectionId = $sec->id; $f->setTypeId($sec->getEntryTypes()[0]->id);
  $f->title = '8 July 1843 → 8 July or 8 June 1843';
  $f->setFieldValues(['asPrinted' => 'deposited the 8th day of July, 1843 (the memorandum as Robinson copied it)', 'reading' => '8 July or 8 June 1843: not decided', 'basis' => $BASIS,
    'decidedBy' => 'source_fault_stearns_mint_2026_10_05.php, 5 October 2026', 'faultRecord' => [$s->id, $d->id], 'faultField' => 'body',
    'footnotes' => [['number' => '1', 'note' => 'John Murray, "California\'s Discovery of Gold in 1841," Overland Monthly, vol. XIX, no. 113, May 1892, pp. 524-529, as carried on SCVHistory.com, /scvhistory/overlandmonthly0592.htm: the memorandum "deposited the 8th day of July, 1843"; J.R. Garrison, Acting Comptroller, October 21, 1891: "such a deposit of gold by Grant & Stone June 8th, 1843, as evidenced by voucher No. 150 [sic]"; L.W. Reid\'s certificate: "Voucher No. 350"; the mint, October 5, 1891: "no record of such a deposit from 1841 to 1844 can be found here."', 'source' => 'editorial-2026']]]);
  if (!Craft::$app->getElements()->saveElement($f)) { throw new \RuntimeException(json_encode($f->getFirstErrors())); }
  $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('source_fault_stearns_mint_2026_10_05.php', 1, 'verified', 'Stearns: the mint deposit, 8 July or 8 June 1843');
}
echo ($APPLY ? 'done' : 'nothing written') . PHP_EOL;
