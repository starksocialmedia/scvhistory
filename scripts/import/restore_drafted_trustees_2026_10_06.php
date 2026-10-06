/**
 * The trustees with a drafted profile, back from rows to records, if Nathan wants them (DRY RUN for his read).
 * persons_to_rows_2026_10_06.php measured "thin" by the text stored on a record. 45 of the 85 it removed have a profile drafted and
 * waiting on Nathan's read (Hart: inventory/review/hart-trustees-profiles-draft-batch-*-2026-10-05.json, 27; College of the Canyons:
 * coc-trustees-profiles-draft-batch-*, 18), so they were thin only because the draft was not yet applied. This puts any of them
 * back: the record is restored from Craft's trash, its terms (holderName equal to its name and the removal line in their
 * provenance) and candidacies (same line) are linked to it again, holderName is cleared and a provenance line added. Then the
 * profile builders (build_hart_trustee_profiles_2026_10_05.php, build_coc_trustee_profiles_2026_10_05.php) apply as before.
 * Charles L. Lyon and Cassandra Nicole Love are left out: Nathan removed them by name (remove_thin_persons_2026_10_06.php), not by
 * the rule. 44 then. $ONLY limits it to chosen ids. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/restore_drafted_trustees_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
$ONLY = [];
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0; $k = 0; $bad = [];
$LINE = 'person record removed under the person-record rule, 6 October 2026';
$D = [];
foreach (['hart' => 'hart-trustees-profiles-draft-batch-*-2026-10-05.json', 'coc' => 'coc-trustees-profiles-draft-batch-*-2026-10-05.json'] as $who => $g) {
  foreach (glob("$root/inventory/review/$g") as $f) { foreach (json_decode(file_get_contents($f), true) ?: [] as $p) { if (trim((string)($p['body'] ?? '')) !== '' || ($p['form'] ?? '') === 'one-line') { $D[(int)$p['id']] = $who; } } } }
foreach ($D as $id => $who) {
  if (($ONLY && !in_array($id, $ONLY, true)) || in_array($id, [30253, 28324], true)) { continue; }
  if (Entry::find()->id($id)->status(null)->exists()) { continue; }
  $p = Entry::find()->id($id)->status(null)->trashed()->one();
  if (!$p) { $bad[] = "#$id: not in the trash"; continue; }
  $name = $p->title; $k++;
  $hs = array_filter(Entry::find()->section('officeHoldings')->status(null)->limit(null)->search('holderName:"' . $name . '"')->all(), fn($h) => (string)$h->holderName === $name && str_contains((string)$h->recordProvenance, $LINE) && !$h->holdingPerson->one());
  $cs = array_filter(Entry::find()->section('candidacies')->status(null)->limit(null)->all(), fn($c) => str_starts_with((string)$c->recordProvenance, "$name's $LINE") && !$c->candidacyPerson->one());
  echo "#$id $name ($who): restore; relink " . count($hs) . ' term(s), ' . count($cs) . " candidacy(ies)\n";
  if (!$APPLY) { continue; }
  if (!$el->restoreElement($p)) { throw new \RuntimeException("#$id not restored"); } $n++;
  $prov = fn($x) => mb_substr("Relinked to $name's restored record, 6 October 2026 (restore_drafted_trustees_2026_10_06.php). " . (string)$x->recordProvenance, 0, 255);
  foreach ($hs as $h) { $h->setFieldValues(['holdingPerson' => [$id], 'holderName' => '', 'recordProvenance' => $prov($h)]); if (!$el->saveElement($h)) { throw new \RuntimeException("#{$h->id}"); } $n++; }
  foreach ($cs as $c) { $c->setFieldValues(['candidacyPerson' => [$id], 'recordProvenance' => $prov($c)]); if (!$el->saveElement($c)) { throw new \RuntimeException("#{$c->id}"); } $n++; }
}
echo "$k to restore. REFUSED: " . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if ($APPLY) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('restore_drafted_trustees_2026_10_06.php', $n, 'verified', "$k drafted trustees restored from rows to records"); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
