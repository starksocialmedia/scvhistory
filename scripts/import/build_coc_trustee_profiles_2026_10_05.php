/**
 * Profiles for the trustees of the Santa Clarita Community College District (Nathan, 5 October 2026: "Fill them the way the
 * Hart trustees are being filled: sources counted first, then as much profile as each can support. Many will be a line").
 * Drafts: inventory/review/coc-trustees-profiles-draft-batch-{a,b}-2026-10-05.json, to the Hart brief. Nathan reads every
 * profile in the dry run (inventory/review/coc-trustees-profiles-dry-run-2026-10-05.md) before it is applied. The checks refuse
 * an em dash, wording the public-note check fails, a note marker with no note or a note never cited, or a person whose body is
 * no longer empty. Carl Boyer and Scott Wilk already have profiles and are not drafted.
 * Fills an empty body only. Idempotent. Dry run by default. Set $APPLY = true to write. $ONLY to apply some.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_coc_trustee_profiles_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
$ONLY = [];
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$BAD = '~\b(WordPress|the import|on import|imported from|migrated|migration|legacy mirror|in the mirror|inventory/|SHA-?(1|256)|checksums?|manifest|dry run|the script|scripts? (that|which)|next to try|to try next|with Nathan|Nathan\'s|Claude|image tag|commented out|read so far|search summary|page was blocked|ha(s|ve) not been (read|checked)|could not be read|[Ss]earched \d|sources searched|lists searched|release search)\b~i';
$P = [];
foreach (glob("$root/inventory/review/coc-trustees-profiles-draft-batch-*-2026-10-05.json") as $f) { foreach (json_decode(file_get_contents($f), true) ?: [] as $p) { $P[] = $p + ['_file' => basename($f)]; } }
$out = []; $bad = []; $plan = [];
foreach ($P as $p) {
  $id = (int)$p['id']; if ($ONLY && !in_array($id, $ONLY, true)) { continue; }
  if (trim((string)($p['body'] ?? '')) === '' && ($p['form'] ?? '') !== 'one-line') { continue; } /* Boyer and Wilk: notes only, no draft */
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
  $plan[$id] = [$e, $body, $notes, array_map(fn($x) => ['heading' => (string)($x['heading'] ?? ''), 'note' => (string)$x['note'], 'position' => 'bottom'], $p['editorNotes'] ?? [])];
  if (preg_match('~[\x{2013}\x{2014}]~u', json_encode($p['editorNotes'] ?? [], JSON_UNESCAPED_UNICODE))) { $bad[] = "#$id {$p['name']}: em dash in an editor note"; unset($plan[$id]); continue; }
  $out[] = "## {$e->title} (#$id), {$p['form']}\n\n" . $body . "\n\n" . implode("\n", array_map(fn($i, $n) => '[' . ($i + 1) . "] $n", array_keys($notes), $notes))
    . (($p['editorNotes'] ?? []) ? "\n\nEditor's notes (shown on the page):\n- " . implode("\n- ", array_map(fn($x) => $x['heading'] . ': ' . $x['note'], $p['editorNotes'])) : '')
    . (($p['notesForNathan'] ?? []) ? "\n\nFor Nathan:\n- " . implode("\n- ", $p['notesForNathan']) : '') . "\n";
}
file_put_contents("$root/inventory/review/coc-trustees-profiles-dry-run-2026-10-05.md", "# College district trustee profiles: the dry run\n\n" . count($plan) . " profiles ready, " . count($bad) . " refused. Nothing is written until Nathan has read these.\n\n" . ($bad ? "Refused:\n- " . implode("\n- ", $bad) . "\n\n" : '') . implode("\n", $out));
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ': ' . count($plan) . ' ready, ' . count($bad) . ' refused; printed to inventory/review/coc-trustees-profiles-dry-run-2026-10-05.md' . PHP_EOL;
foreach ($bad as $b) { echo "REFUSED $b" . PHP_EOL; }
if (!$APPLY) { return; }
$n = 0;
foreach ($plan as $id => [$e, $body, $notes, $ed]) {
  $e->setFieldValues(['body' => $body, 'bodyAuthorship' => 'editorial-2026',
    'footnotes' => array_map(fn($i, $t) => ['number' => (string)($i + 1), 'note' => $t, 'source' => 'editorial-2026'], array_keys($notes), $notes),
    'recordProvenance' => 'build_coc_trustee_profiles_2026_10_05.php, 5 October 2026: profile from the college district trustee drafts']);
  if ($ed) { $rows = array_values(array_filter(array_map(fn($r) => ['heading' => (string)$r['heading'], 'note' => (string)$r['note'], 'position' => (string)$r['position'] ?: 'bottom'], iterator_to_array($e->editorNotes ?? [])), fn($r) => $r['note'] !== '')); foreach ($ed as $x) { if (!in_array($x['note'], array_column($rows, 'note'), true)) { $rows[] = $x; } } $e->setFieldValue('editorNotes', $rows); }
  if (!$el->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); } $n++;
}
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('build_coc_trustee_profiles_2026_10_05.php', $n, 'verified', "college district trustee profiles: $n");
echo "done: $n profiles" . PHP_EOL;
