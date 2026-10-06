/**
 * The person-record rule (Nathan, 6 October 2026, settled): "Keep a record where any of these holds: resigned, died in office or
 * was removed; held a higher office; sat on a body's first board; has a portrait; or has something named for them. Everything
 * else becomes a row with holderName."
 * The list, inventory/review/person-rows-2026-10-06.json, is every person whose record holds office and nothing else (no text of
 * 300 characters, no article, document, photograph, event, obituary, collection, organization or place pointing at it) and who
 * meets none of the tests: 86, less John K. Hackney (#30209), for whom College of the Canyons named its "John K. Hackney
 * Outstanding Musician Award" (Canyon Call, 1971 to 1978, on the mirror): 85.
 * For each: every term takes holderName (the person's name) before its holder is unlinked, so its generated title keeps the name;
 * every candidacy is unlinked (it prints nameAsPrinted); a provenance line says the record was removed; the person record is
 * deleted (Craft's soft delete, restorable). Refused, person by person, if anything else points at the record.
 * A row becomes a record again by creating the person and linking the term (holderName is then ignored).
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/persons_to_rows_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0; $done = 0; $gone = 0; $refused = []; $terms = 0; $cands = 0;
$L = json_decode(file_get_contents("$root/inventory/review/person-rows-2026-10-06.json"), true);
foreach ($L as $row) {
  $pid = $row['id']; $name = $row['name'];
  $p = Entry::find()->id($pid)->status(null)->one();
  if (!$p) { $gone++; continue; }
  if ($p->title !== $name) { $refused[] = "#$pid is {$p->title}, not $name"; continue; }
  $other = Entry::find()->status(null)->relatedTo(['targetElement' => $p])->section(['not', 'officeHoldings', 'candidacies'])->ids();
  if ($other) { $refused[] = "#$pid $name: also pointed at by " . implode(', ', $other); continue; }
  $hs = Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $p, 'field' => 'holdingPerson'])->all();
  $cs = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $p, 'field' => 'candidacyPerson'])->all();
  $terms += count($hs); $cands += count($cs); $done++;
  if ($done <= 3 || !$APPLY) { echo "#$pid $name: " . count($hs) . ' term(s), ' . count($cs) . " candidacy(ies)\n"; }
  if (!$APPLY) { continue; }
  $prov = fn($x) => mb_substr("$name's person record removed under the person-record rule, 6 October 2026 (persons_to_rows_2026_10_06.php). " . (string)$x->recordProvenance, 0, 255);
  foreach ($hs as $h) { $h->setFieldValues(['holderName' => $name, 'holdingPerson' => [], 'recordProvenance' => $prov($h)]); if (!$el->saveElement($h)) { throw new \RuntimeException("#{$h->id} " . json_encode($h->getFirstErrors())); } $n++;
    if (!str_starts_with(Entry::find()->id($h->id)->status(null)->one()->title, $name)) { throw new \RuntimeException("#{$h->id} title lost the name"); } }
  foreach ($cs as $c) { $c->setFieldValues(['candidacyPerson' => [], 'recordProvenance' => $prov($c)]); if (!$el->saveElement($c)) { throw new \RuntimeException("#{$c->id} " . json_encode($c->getFirstErrors())); } $n++; }
  if (!$el->deleteElement($p)) { throw new \RuntimeException("#$pid not deleted"); } $n++;
}
echo "persons: $done to rows" . ($gone ? ", $gone removed already" : '') . "; terms kept, named and unlinked: $terms; candidacies kept, unlinked: $cands\n";
echo 'REFUSED: ' . ($refused ? "\n  " . implode("\n  ", $refused) : 'none') . PHP_EOL;
echo 'person records now: ' . Entry::find()->section('persons')->status(null)->count() . PHP_EOL;
if ($APPLY) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('persons_to_rows_2026_10_06.php', $n, 'verified', "the person-record rule: $done records to rows; $terms terms named, $cands candidacies kept"); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
