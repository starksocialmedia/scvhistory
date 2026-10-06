/**
 * Aliases that are the title's own name (Nathan, 5 October 2026: "Remove 'Also known as Joseph Vincent Messina, Joseph Messina'
 * from his page. His full name and a shortened form are not aliases, they are the same name. Aliases are for names a reader
 * might search that differ from the title ... Those should come out everywhere"). The list is
 * inventory/review/aliases-same-name-2026-10-05.json: an alias line with the title's surname whose given names are the title's
 * own, longer or shorter (a middle name or initial added or dropped, an initial for a name, a standard short form, an honorific
 * or office prefixed, a suffix, a repeat of the title). Kept although the test matched them: a different given name in a
 * legal name ("Howard P. 'Buck' McKeon", "William J. 'Pete' Knight", "Rochelle 'Shelley' Weinstein"), and Tichenor's "Jr.",
 * added today on Nathan's word. $ONLY limits the run to listed records (Messina first). Idempotent. Dry run by default.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/remove_same_name_aliases_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false; $ONLY = [];
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ($ONLY ? ' only ' . implode(',', $ONLY) : '') . PHP_EOL;
$L = json_decode(file_get_contents(\Craft::getAlias('@root') . '/inventory/review/aliases-same-name-2026-10-05.json'), true);
$by = []; foreach ($L as [$pid, $t, $a]) { $by[$pid][] = $a; }
$n = 0; $lines = 0;
foreach ($by as $pid => $drop) {
  if ($ONLY && !in_array($pid, $ONLY, true)) { continue; }
  $e = Entry::find()->id($pid)->status(null)->one(); if (!$e) { echo "no #$pid\n"; continue; }
  $cur = (string)$e->personAliases; $parts = preg_split('~\R~', $cur); $out = [];
  foreach ($parts as $line) { $segs = array_map('trim', explode(';', $line)); $segs = array_values(array_filter($segs, fn($s) => $s !== '' && !in_array($s, $drop, true))); if ($segs) { $out[] = implode('; ', $segs); } }
  $new = implode("\n", $out); if ($new === trim($cur)) { continue; }
  $lines += count($drop); echo "#$pid {$e->title}: drop " . implode(' | ', $drop) . ($new !== '' ? "; keep " . str_replace("\n", ' | ', $new) : '; none left') . "\n";
  if (!$APPLY) { continue; }
  $e->setFieldValue('personAliases', $new); if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException("#$pid"); } $n++;
}
echo "$lines lines\n";
if ($APPLY && $n) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('remove_same_name_aliases_2026_10_05.php', $n, 'verified', 'aliases that are the title\'s own name' . ($ONLY ? ' (' . implode(',', $ONLY) . ')' : '')); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
