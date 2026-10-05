/**
 * Profiles for the Hart district's trustees with no legacy page and no profile (Nathan, 5 October 2026: "Start with the
 * 53 Hart trustees, since they are a coherent group with a common source"). Research: inventory/review/hart-trustees-
 * sources-batch-{a,b,c}-2026-10-05.md; drafts, written to inventory/review/hart-trustees-profile-brief-2026-10-05.md:
 * inventory/review/hart-trustees-profiles-draft-batch-{a,b,c}-2026-10-05.json.
 * Nathan reads every profile in the dry run before it is applied (docs/PROFILES.md, step 5): the dry run prints each one
 * as a reader will see it, with its notes. The checks refuse a draft with an em dash, wording the public-note check fails,
 * a note marker with no note or a note never cited, or a person whose body is no longer empty.
 * Fills an empty body only. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Set $ONLY to a list of person ids to apply some and hold the rest.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_hart_trustee_profiles_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
$ONLY = [];
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$BAD = '~\b(WordPress|the import|on import|imported from|migrated|migration|legacy mirror|in the mirror|inventory/|SHA-?(1|256)|checksums?|manifest|dry run|the script|scripts? (that|which)|next to try|to try next|with Nathan|Nathan\'s|Claude|image tag|commented out|read so far|search summary|page was blocked|ha(s|ve) not been (read|checked)|could not be read|[Ss]earched \d|sources searched|lists searched|release search)\b~i';
$P = [];
foreach (glob("$root/inventory/review/hart-trustees-profiles-draft-batch-*-2026-10-05.json") as $f) { foreach (json_decode(file_get_contents($f), true) ?: [] as $p) { $P[] = $p + ['_file' => basename($f)]; } }
$out = []; $bad = []; $plan = [];
foreach ($P as $p) {
  $id = (int)$p['id']; if ($ONLY && !in_array($id, $ONLY, true)) { continue; }
  $e = Entry::find()->section('persons')->id($id)->status(null)->one(); $why = [];
  if (!$e) { $why[] = 'no such person'; }
  elseif (mb_strlen(trim(strip_tags((string)$e->body))) >= 200) { $why[] = 'body no longer empty'; }
  $body = trim((string)$p['body']); $notes = array_values(array_map('trim', $p['footnotes'] ?? []));
  if ($body === '') { $why[] = 'empty draft'; }
  if (preg_match('~\x{2014}~u', $body . implode(' ', $notes))) { $why[] = 'em dash'; }
  foreach ($notes as $i => $n) { if (preg_match($BAD, preg_replace('~https?://\S+~', '', $n))) { $why[] = 'note ' . ($i + 1) . ' names the process'; } }
  preg_match_all('~\[(\d+)\]~', $body, $m); $used = array_unique(array_map('intval', $m[1]));
  foreach ($used as $u) { if ($u < 1 || $u > count($notes)) { $why[] = "marker [$u] has no note"; } }
  for ($i = 1; $i <= count($notes); $i++) { if (!in_array($i, $used, true)) { $why[] = "note $i is never cited"; } }
  if ($why) { $bad[] = "#$id {$p['name']}: " . implode(', ', $why); continue; }
  $plan[$id] = [$e, $body, $notes];
  $out[] = "## {$e->title} (#$id), {$p['form']}\n\n" . $body . "\n\n" . implode("\n", array_map(fn($i, $n) => '[' . ($i + 1) . "] $n", array_keys($notes), $notes))
    . (($p['notesForNathan'] ?? []) ? "\n\nFor Nathan:\n- " . implode("\n- ", $p['notesForNathan']) : '') . "\n";
}
file_put_contents("$root/inventory/review/hart-trustees-profiles-dry-run-2026-10-05.md", "# Hart trustee profiles: the dry run\n\n" . count($plan) . " profiles ready, " . count($bad) . " refused. Nothing is written until Nathan has read these.\n\n" . ($bad ? "Refused:\n- " . implode("\n- ", $bad) . "\n\n" : '') . implode("\n", $out));
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ': ' . count($plan) . ' ready, ' . count($bad) . ' refused; printed to inventory/review/hart-trustees-profiles-dry-run-2026-10-05.md' . PHP_EOL;
foreach ($bad as $b) { echo "REFUSED $b" . PHP_EOL; }
if (!$APPLY) { return; }
$n = 0;
foreach ($plan as $id => [$e, $body, $notes]) {
  $e->setFieldValues(['body' => $body, 'bodyAuthorship' => 'editorial-2026',
    'footnotes' => array_map(fn($i, $t) => ['number' => (string)($i + 1), 'note' => $t, 'source' => 'editorial-2026'], array_keys($notes), $notes),
    'recordProvenance' => 'build_hart_trustee_profiles_2026_10_05.php, 5 October 2026: profile from the Hart trustee dossiers']);
  if (!$el->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); } $n++;
}
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('build_hart_trustee_profiles_2026_10_05.php', $n, 'verified', "Hart trustee profiles: $n");
echo "done: $n profiles" . PHP_EOL;
