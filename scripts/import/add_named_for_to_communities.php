/**
 * namedFor and namingNote on communities (the neighborhood category group), so a
 * town can be linked to its namesake as places and organizations can: Mentryville
 * for Charles Alexander Mentry, Newhall for Henry Mayo Newhall once a source is
 * held (Nathan, 1 October 2026). Schema only; its own request.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_named_for_to_communities.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$fs = Craft::$app->getFields(); $cats = Craft::$app->getCategories();
$g = $cats->getGroupByHandle('neighborhood');
if (!$g || !$fs->getFieldByHandle('namedFor') || !$fs->getFieldByHandle('namingNote')) { echo 'REFUSING: the group or the fields are missing (run add_named_for_fields.php first)' . PHP_EOL; return; }
$have = array_map(fn($f) => $f->handle, $g->getFieldLayout()->getCustomFields());
$add = array_values(array_diff(['namedFor', 'namingNote'], $have));
echo $add ? '   add ' . implode(', ', $add) . ' to the neighborhood layout' . PHP_EOL : 'nothing to do' . PHP_EOL;
if (!$add) { return; }
if (!$APPLY) { echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$layout = $g->getFieldLayout(); $tabs = $layout->getTabs(); $els = array_values($tabs[0]->getElements());
foreach ($add as $h) { $els[] = new \craft\fieldlayoutelements\CustomField($fs->getFieldByHandle($h)); }
$tabs[0]->setElements($els); $layout->setTabs($tabs); $g->setFieldLayout($layout);
if (!$cats->saveGroup($g)) { throw new \RuntimeException(json_encode($g->getFirstErrors())); }
$now = array_map(fn($f) => $f->handle, $cats->getGroupByHandle('neighborhood')->getFieldLayout()->getCustomFields());
$ok = !array_diff(['namedFor', 'namingNote'], $now);
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_named_for_to_communities.php', count($add), $ok ? 'verified' : 'SHORT', 'namedFor and namingNote on communities');
if (!$ok) { throw new \RuntimeException('read-back failed'); }
