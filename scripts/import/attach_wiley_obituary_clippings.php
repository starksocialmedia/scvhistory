/**
 * The Henry Clay Wiley obituary (#888) did not match its original, which carries
 * the newspaper clippings (Nathan, 1 October 2026). The files were in the
 * archive, imported from WordPress but attached to nothing: the Los Angeles
 * Herald obituary of 1898 (#59), the Herald's list of Mexican War veterans given
 * medals in 1876 (#67), and the original page's PDF (#71). This attaches the two
 * clippings as record images and the PDF as a record document. The portrait
 * (#15) stays the lead.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/attach_wiley_obituary_clippings.php'))"
 */

use craft\elements\{Entry, Asset};

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$e = Entry::find()->id(888)->status(null)->one();
$WANT = ['recordImages' => [59, 67], 'recordDocuments' => [71]];
$NAMES = [59 => 'henry-clay-wiley-obituary-los-angeles-herald-1898.jpg', 67 => 'henry-clay-wiley-mexican-war-veterans-badges-los-angeles-herald-1876.jpg', 71 => 'sw_hssc0402wiley.pdf'];
$bad = [];
if (!$e || $e->slug !== 'in-memoriam-henry-clay-wiley-1829-1898') { $bad[] = '#888 is not the Wiley obituary'; }
foreach ($NAMES as $id => $fn) { if (Asset::find()->id($id)->one()?->filename !== $fn) { $bad[] = "asset #$id is not $fn"; } }
$vals = [];
foreach ($WANT as $h => $ids) { $have = $e->getFieldValue($h)->ids(); $add = array_diff($ids, $have); if ($add) { $vals[$h] = array_values(array_merge($have, $add)); echo "   $h + " . implode(', ', array_map(fn($i) => "#$i {$NAMES[$i]}", $add)) . PHP_EOL; } }
echo ($vals ? '' : 'nothing to do' . PHP_EOL) . 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || !$vals) { if (!$APPLY) { echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; } return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$e->setFieldValues($vals);
if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException(json_encode($e->getFirstErrors())); }
$r = Entry::find()->id(888)->status(null)->one();
$ok = !array_diff([59, 67], $r->recordImages->ids()) && in_array(71, $r->recordDocuments->ids());
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('attach_wiley_obituary_clippings.php', 1, $ok ? 'verified' : 'SHORT', 'Wiley obituary: the 1898 and 1876 clippings and the PDF attached');
if (!$ok) { throw new \RuntimeException('read-back failed'); }
