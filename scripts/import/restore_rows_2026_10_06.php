/**
 * Two rows back to records on Nathan's word (6 October 2026): Dan Masnada (#28326: "CLWA director 1987 to 1993, general manager,
 * two Newsmaker episodes and regular coverage in the saved water-board material is sources to write from") and Angela Marler
 * (#28312: "restore her record. I meant keep"). The same steps as restore_drafted_trustees_2026_10_06.php: the record comes back
 * from Craft's trash, its terms (holderName equal to its name, the removal line in their provenance) and candidacies are linked to
 * it again, holderName cleared, a provenance line added.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/restore_rows_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
$ONLY = [];
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0; $k = 0; $bad = [];
$LINE = 'person record removed under the person-record rule, 6 October 2026';
$D = [28326 => 'Dan Masnada', 28312 => 'Angela Marler'];
foreach ($D as $id => $who) {
  if (Entry::find()->id($id)->status(null)->exists()) { continue; }
  $p = Entry::find()->id($id)->status(null)->trashed()->one();
  if (!$p) { $bad[] = "#$id: not in the trash"; continue; }
  $name = $p->title; $k++;
  $hs = array_filter(Entry::find()->section('officeHoldings')->status(null)->limit(null)->search('holderName:"' . $name . '"')->all(), fn($h) => (string)$h->holderName === $name && str_contains((string)$h->recordProvenance, $LINE) && !$h->holdingPerson->one());
  $cs = array_filter(Entry::find()->section('candidacies')->status(null)->limit(null)->all(), fn($c) => str_starts_with((string)$c->recordProvenance, "$name's $LINE") && !$c->candidacyPerson->one());
  echo "#$id $name ($who): restore; relink " . count($hs) . ' term(s), ' . count($cs) . " candidacy(ies)\n";
  if (!$APPLY) { continue; }
  if (!$el->restoreElement($p)) { throw new \RuntimeException("#$id not restored"); } $n++;
  $prov = fn($x) => mb_substr("Relinked to $name's restored record, 6 October 2026 (restore_rows_2026_10_06.php). " . (string)$x->recordProvenance, 0, 255);
  foreach ($hs as $h) { $h->setFieldValues(['holdingPerson' => [$id], 'holderName' => '', 'recordProvenance' => $prov($h)]); if (!$el->saveElement($h)) { throw new \RuntimeException("#{$h->id}"); } $n++; }
  foreach ($cs as $c) { $c->setFieldValues(['candidacyPerson' => [$id], 'recordProvenance' => $prov($c)]); if (!$el->saveElement($c)) { throw new \RuntimeException("#{$c->id}"); } $n++; }
}
echo "$k to restore. REFUSED: " . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if ($APPLY) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('restore_rows_2026_10_06.php', $n, 'verified', "Dan Masnada and Angela Marler restored from rows to records"); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
