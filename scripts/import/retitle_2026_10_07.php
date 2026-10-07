/**
 * Titles are what the page printed (Nathan, 7 October 2026):
 *  - Photographs: "the title is the headline the page prints. Leon's catalogue caption goes in its own field and is kept, every
 *    word, never discarded." New field catalogueCaption ("Catalogue entry") on the photograph type holds the whole entry the
 *    page's title tag carries (code, subject, caption); the title becomes the printed headline. 1,550 photographs; the 7 not on
 *    the mirror, 4 on multi-piece pages and 9 with no headline found are left as they are.
 *  - Articles: "restore the printed headlines" on the 58 titled from an extraction file; 54 here, 4 held (title-plan .json, "held").
 *  - Documents: "drop the suffix ... Author, publication and date are fields." The "(Author, Publication, Date)" suffix goes; a
 *    byline or a book title the suffix held and no field did moves into sourceLine first.
 * Slugs do not change, so no address moves. Every old title is in inventory/review/title-plan-2026-10-07.json, so this reverses.
 * Plan: scripts/import/build_title_plan_2026_10_07.py. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/retitle_2026_10_07.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $es = Craft::$app->getEntries(); $el = Craft::$app->getElements(); $n = 0;
$P = json_decode((string)file_get_contents(\Craft::getAlias('@root') . '/inventory/review/title-plan-2026-10-07.json'), true);
$f = $fs->getFieldByHandle('catalogueCaption'); echo 'field catalogueCaption: ' . ($f ? 'exists' : 'create') . PHP_EOL;
if ($APPLY && !$f) { $f = new \craft\fields\PlainText(['handle' => 'catalogueCaption', 'name' => 'Catalogue entry',
    'instructions' => 'Leon Worden\'s catalogue entry for the photograph, as the legacy page\'s title tag carries it: code, subject, caption. Every word kept; the title is the headline the page prints.', 'multiline' => false]);
  if (!$fs->saveField($f)) { throw new \RuntimeException(json_encode($f->getFirstErrors())); } $n++; }
$t = $es->getEntryTypeByHandle('photograph'); $layout = $t->getFieldLayout();
$has = in_array('catalogueCaption', array_map(fn($x) => $x->handle, $layout->getCustomFields()), true);
echo 'photograph layout: ' . ($has ? 'has it' : 'add at the top') . PHP_EOL;
if ($APPLY && $f && !$has) { $tab = $layout->getTabs()[0]; $els = $tab->getElements(); $i = 0;
  foreach ($els as $k => $x) { if ($x instanceof \craft\fieldlayoutelements\entries\EntryTitleField) { $i = $k + 1; } }
  array_splice($els, $i, 0, [new \craft\fieldlayoutelements\CustomField($f)]); $tab->setElements($els); $layout->setTabs($layout->getTabs()); $t->setFieldLayout($layout);
  if (!$es->saveEntryType($t)) { throw new \RuntimeException(json_encode($t->getFirstErrors())); } $n++; $has = true; }
$c = ['photo title' => 0, 'photo caption' => 0, 'photo same' => 0, 'article' => 0, 'document' => 0, 'refused' => 0];
$save = function (Entry $e) use ($el, &$n) { if (!$el->saveElement($e)) { throw new \RuntimeException("#{$e->id} " . json_encode($e->getFirstErrors())); } $n++; };
foreach ($P['photographs'] as $r) {
  $e = Entry::find()->id($r['id'])->section('photographs')->status(null)->one(); if (!$e) { $c['refused']++; echo "#{$r['id']} REFUSED: not a photograph" . PHP_EOL; continue; }
  if ($e->title !== $r['old'] && $e->title !== $r['new']) { $c['refused']++; echo "#{$r['id']} REFUSED: title is now \"{$e->title}\"" . PHP_EOL; continue; }
  $dirty = false;
  if ($e->title !== $r['new']) { $c['photo title']++; $e->title = $r['new']; $dirty = true; } else { $c['photo same']++; }
  $cur = $has ? trim((string)$e->getFieldValue('catalogueCaption')) : '';
  if ($r['caption'] !== '' && $cur !== $r['caption']) { if ($cur !== '') { echo "#{$r['id']} caption already set differently, left" . PHP_EOL; } else { $c['photo caption']++; if ($has) { $e->setFieldValue('catalogueCaption', $r['caption']); } $dirty = true; } }
  if ($APPLY && $dirty) { $save($e); }
}
foreach ($P['articles'] as $r) {
  $e = Entry::find()->id($r['id'])->section('articles')->status(null)->one();
  if (!$e || ($e->title !== $r['old'] && $e->title !== $r['new'])) { $c['refused']++; echo "#{$r['id']} REFUSED: " . ($e ? "title is now \"{$e->title}\"" : 'not found') . PHP_EOL; continue; }
  if ($e->title === $r['new']) { continue; }
  echo "article #{$r['id']}: \"{$r['old']}\" -> \"{$r['new']}\"" . PHP_EOL; $c['article']++;
  if ($APPLY) { $e->title = $r['new']; $save($e); }
}
foreach ($P['documents'] as $r) {
  $e = Entry::find()->id($r['id'])->section('documents')->status(null)->one(); if (!$e) { $c['refused']++; echo "#{$r['id']} REFUSED: not found" . PHP_EOL; continue; }
  $new = $r['title'] ?? trim(preg_replace('~\s*\((?:[^()]|\([^()]*\))*\)\s*$~u', '', $e->title));
  $src = trim((string)$e->getFieldValue('sourceLine')); $newSrc = $src;
  if ($r['sourceLine']) { [$how, $txt] = $r['sourceLine'];
    if ($how === 'prefix' && !str_contains($src, trim($txt, ' .'))) { $newSrc = $txt . $src; }
    if ($how === 'set-if-empty' && $src === '') { $newSrc = $txt; } }
  if ($new === $e->title && $newSrc === $src) { continue; }
  echo "document #{$r['id']}: \"{$e->title}\" -> \"$new\"" . ($newSrc !== $src ? " | sourceLine \"$newSrc\"" : '') . PHP_EOL; $c['document']++;
  if ($APPLY) { $e->title = $new; $e->setFieldValue('sourceLine', $newSrc); $save($e); }
}
echo json_encode($c) . PHP_EOL;
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('retitle_2026_10_07.php', $n, 'verified', 'titles as printed: photographs (catalogue entry kept), 54 articles, document suffixes'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
