/**
 * Angela Marler (#28312) back to a row (Nathan, 8 October 2026: "Your read is right and the restore misread 'keep'. She has
 * one term, no sources to write from, and none of the other conditions. Make her a row the way the other 85 were done, with
 * her name on the term."). The 6 October restore (restore_rows_2026_10_06.php) took "I meant keep" as keep the record.
 * The steps of persons_to_rows_2026_10_06.php: every term takes holderName before its holder is unlinked, so its generated
 * title keeps the name; every candidacy is unlinked (it prints nameAsPrinted); a provenance line says the record was removed;
 * the person record is deleted (Craft's soft delete, restorable). Refused if anything else points at the record.
 * Idempotent. Dry run by default. Set $APPLY = true.
 */
use craft\elements\Entry;
$APPLY = false;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$reads = require "$root/scripts/import/_reads.php";
$reads([['record', "Craft's record #28312 and the terms and candidacies that point at it", 'no file', 'not read: no file is involved; the record is the thing']]);
$PID = 28312; $NAME = 'Angela Marler';
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$p = Entry::find()->id($PID)->status(null)->one();
if (!$p) { echo "#$PID removed already" . PHP_EOL; return; }
if ($p->title !== $NAME) { throw new \RuntimeException("#$PID is {$p->title}, not $NAME"); }
$other = Entry::find()->status(null)->relatedTo(['targetElement' => $p])->section(['not', 'officeHoldings', 'candidacies'])->ids();
if ($other) { throw new \RuntimeException("#$PID also pointed at by " . implode(', ', $other) . ': refused'); }
$hs = Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $p, 'field' => 'holdingPerson'])->all();
$cs = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $p, 'field' => 'candidacyPerson'])->all();
foreach ($hs as $h) { echo "  term #{$h->id} {$h->title}: holderName \"$NAME\", unlinked" . PHP_EOL; }
foreach ($cs as $c) { echo "  candidacy #{$c->id} {$c->title}: unlinked" . PHP_EOL; }
echo "#$PID $NAME: " . count($hs) . ' term(s), ' . count($cs) . ' candidacy(ies); the record to the trash' . PHP_EOL;
if (!$APPLY) { echo 'nothing written' . PHP_EOL; return; }
$prov = fn($x) => mb_substr("$NAME's person record removed under the person-record rule, 8 October 2026 (marler_to_row_2026_10_08.php; the 6 October restore misread Nathan's \"keep\"). " . (string)$x->recordProvenance, 0, 255);
foreach ($hs as $h) {
  $h->setFieldValues(['holderName' => $NAME, 'holdingPerson' => [], 'recordProvenance' => $prov($h)]);
  if (!$el->saveElement($h)) { throw new \RuntimeException("#{$h->id} " . json_encode($h->getFirstErrors())); } $n++;
  if (!str_starts_with(Entry::find()->id($h->id)->status(null)->one()->title, $NAME)) { throw new \RuntimeException("#{$h->id} title lost the name"); }
}
foreach ($cs as $c) { $c->setFieldValues(['candidacyPerson' => [], 'recordProvenance' => $prov($c)]); if (!$el->saveElement($c)) { throw new \RuntimeException("#{$c->id} " . json_encode($c->getFirstErrors())); } $n++; }
if (!$el->deleteElement($p)) { throw new \RuntimeException("#$PID not deleted"); } $n++;
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('marler_to_row_2026_10_08.php', $n, 'verified', 'Angela Marler to a row: her term named, her candidacy kept');
echo "done: $n; person records now: " . Entry::find()->section('persons')->status(null)->count() . PHP_EOL;
