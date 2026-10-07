/**
 * The CC BY 2.0 licence on the license field (Nathan, 6 October 2026: "Add the CC BY 2.0 licence option and import Pavley's
 * portrait"). Fran Pavley's Commons portrait (Edward Headington, via Flickr) is CC BY 2.0, which the field could not record.
 * Added after cc-by-3.0. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_cc_by_2_licence_2026_10_06.php'))"
 */
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $f = $fs->getFieldByHandle('license'); $n = 0;
$opts = array_map(fn($o) => is_array($o) ? $o : ['label' => (string)$o->label, 'value' => (string)$o->value, 'default' => (bool)$o->default], $f->options);
$vals = array_column($opts, 'value');
if (in_array('cc-by-2.0', $vals, true)) { echo "cc-by-2.0: exists\n"; }
else {
  $i = array_search('cc-by-3.0', $vals, true); $label = str_replace('3.0', '2.0', $opts[$i]['label']);
  echo "cc-by-2.0: add \"$label\" after cc-by-3.0\n";
  if ($APPLY) { array_splice($opts, $i + 1, 0, [['label' => $label, 'value' => 'cc-by-2.0', 'default' => false]]); $f->options = $opts; if (!$fs->saveField($f)) { throw new \RuntimeException(json_encode($f->getFirstErrors())); } $n++; }
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('add_cc_by_2_licence_2026_10_06.php', $n, 'verified', 'cc-by-2.0 on the license field'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
