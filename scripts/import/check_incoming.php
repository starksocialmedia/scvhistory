/**
 * READ ONLY. Handed-over files left waiting in inventory/incoming (Nathan, 4 October 2026: "A file sitting
 * in incoming with no done entry and no reason in OUTSTANDING.md should fail check_render ... the failure is
 * a file waiting, not a file missing").
 *
 * Six marks were handed over on 4 October and sat unimported for a day: four never arrived, and one that did
 * arrive was held with them. The done/ rule acts only after an import, so nothing flagged it. Here every file
 * at the top of inventory/incoming must either be named in done/MANIFEST.json (imported, not yet moved) or be
 * named in inventory/incoming/OUTSTANDING.md with its reason (as a whole name: "hart.jpg" is not found inside
 * "Williamshart.jpg"). Anything else fails: import it, or write down
 * why it waits.
 * Returns ['ok' => bool, 'fails' => [...]] for check_render.php.
 * Run alone: ddev craft exec "eval(file_get_contents('scripts/import/check_incoming.php'))"
 */
$IN = \Craft::getAlias('@root') . '/inventory/incoming';
$man = json_decode((string)@file_get_contents("$IN/done/MANIFEST.json"), true) ?: [];
$doneNames = array_column($man['moved'] ?? [], 'file');
$out = (string)@file_get_contents("$IN/OUTSTANDING.md");
$fails = []; $listed = 0; $stale = [];
foreach (scandir($IN) as $f) {
    if ($f[0] === '.' || $f === 'OUTSTANDING.md' || is_dir("$IN/$f")) { continue; }
    if (in_array($f, $doneNames, true)) { $stale[] = $f; continue; }
    if (preg_match('~(?<![\w.=-])' . preg_quote($f, '~') . '(?![\w.-])~u', $out)) { $listed++; continue; }
    $fails[] = "INCOMING  $f: waiting, with no done/ entry and no reason in OUTSTANDING.md";
}
foreach ($fails as $l) { echo $l . PHP_EOL; }
if ($stale) { echo 'incoming: imported but not yet moved to done/: ' . implode(', ', $stale) . PHP_EOL; }
echo 'incoming: ' . $listed . ' files waiting with a reason in OUTSTANDING.md' . ($fails ? ', ' . count($fails) . ' waiting without one' : '') . PHP_EOL;
return ['ok' => !$fails, 'fails' => $fails];
