/**
 * Three person records removed (Nathan, 6 October 2026: "Remove Bowman, Love and Charles L. Lyon now. If any has a term or
 * candidacy, keep the office record on the body's page and unlink it cleanly rather than orphaning it. Report what each held as
 * you remove it"). Each held office and nothing else: no text, no source beyond the term's own.
 * - Each term stays, its holder unlinked; the body's page shows it under the name in the term's own title, without a link
 *   (templates/_partials/civic/body-hub.twig past members, terms-timeline.twig). A provenance line says the record was removed.
 * - A candidacy stays, its candidate unlinked; election pages print nameAsPrinted already.
 * - The person record is deleted (Craft's soft delete: it sits in the trash and can be restored).
 * Jereann Bowman (#28677) is held behind $WITH_BOWMAN: Nathan wrote earlier the same day that she "clearly belongs" and a Hart
 * school bears her name. Her portrait crop (an asset) is not deleted.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/remove_thin_persons_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
$WITH_BOWMAN = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $n = 0;
$P = [28324 => 'Cassandra Nicole Love', 30253 => 'Charles L. Lyon'] + ($WITH_BOWMAN ? [28677 => 'Jereann Bowman'] : []);
$FIELD = ['officeHoldings' => 'holdingPerson', 'candidacies' => 'candidacyPerson'];
foreach ($P as $pid => $name) {
  $p = Entry::find()->id($pid)->status(null)->one();
  $refs = []; foreach ($FIELD as $sec => $f) { foreach (Entry::find()->section($sec)->status(null)->relatedTo(['targetElement' => $pid, 'field' => $f])->all() as $x) { $refs[] = [$x, $f]; } }
  if (!$p) { echo "#$pid $name: removed already" . ($refs ? ', but ' . count($refs) . ' still point at it' : '') . "\n"; continue; }
  if ($p->title !== $name) { throw new \RuntimeException("#$pid is {$p->title}, not $name"); }
  $other = Entry::find()->status(null)->relatedTo(['targetElement' => $p])->section(['not', 'officeHoldings', 'candidacies'])->ids();
  if ($other) { throw new \RuntimeException("#$pid: other records point at it: " . implode(', ', $other)); }
  echo "#$pid $name\n";
  foreach ($refs as [$x, $f]) {
    $what = $f === 'holdingPerson' ? "{$x->termStartEdtf} to {$x->termEndEdtf}, {$x->selectionMethod->value}, ended {$x->howEnded->value}" : "{$x->nameAsPrinted}, {$x->outcome->value}";
    echo "  keeps #{$x->id} {$x->title} ($what), unlinked\n";
    if (!$APPLY) { continue; }
    $prov = "$name's person record removed, 6 October 2026 (remove_thin_persons_2026_10_06.php). " . (string)$x->recordProvenance;
    $x->setFieldValues([$f => [], 'recordProvenance' => mb_substr($prov, 0, 255)]);
    if (!$el->saveElement($x)) { throw new \RuntimeException("#{$x->id} " . json_encode($x->getFirstErrors())); } $n++;
  }
  if ($APPLY) { if (!$el->deleteElement($p)) { throw new \RuntimeException("#$pid not deleted"); } $n++; echo "  deleted\n"; }
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('remove_thin_persons_2026_10_06.php', $n, 'verified', 'Love and Lyon removed; their terms and candidacy kept, unlinked'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
