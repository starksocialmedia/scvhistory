/**
 * The same thing held twice, merged, and #28295 moved to articles (Nathan, 7 October 2026: "merge all three. Perkins 1957,
 * lw2304a, the two shared images. Keep the lower id, carry every field and link across." "Type: yes to the move including
 * #28295").
 *  1. Perkins 1957: keep article #1434, merge document #27374 (one text; #27374 printed the 109 notes at the end, #1434 holds
 *     them as footnotes).
 *  2. lw2304a: keep article #865, merge photograph #3179 (one map, one caption; #3179's body adds the page's navigation).
 *  3. "The two shared images": #1444 (Perkins, 1961) and #2689 share two images but are different pieces. The duplicate is
 *     #2689 (photograph, /scvhistory/lw030597.htm) and article #12558 (/scvhistory/signal/worden/old/lw030597.htm): one piece,
 *     Leon Worden's "Story of Sulphur Springs School", on two pages. Keep #2689, moved to articles (it is an article), merge #12558.
 *  4. #28295, Karina Lutz's "City Backers Join Prison Furor" (The Signal, November 1, 1985): document to article, in its
 *     collection beside the other seven (sg110185).
 * Carrying: every field with a value on the merged record comes across. A relation is the union; a table gains the rows it lacks;
 * a text field the kept record leaves empty is filled; where both hold different text the kept record's stays and the other is
 * printed below for the record (the merged record goes to the trash, so it is restorable, and nothing it held is lost). A field the
 * kept record's type lacks is added to that type's layout first ("Photograph details" on the article type: the photograph-only
 * fields, the third gap of the type census). Links pointing at the merged record move to the kept one. A second legacy address
 * is kept in recordProvenance for the cutover redirect map. Moved and merged records' addresses go in config/redirects.php.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/merge_and_move_2026_10_07.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $es = Craft::$app->getEntries(); $db = Craft::$app->db; $n = 0; $redirects = [];
$handles = fn(Entry $e) => array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
$empty = function ($v) { if ($v === null || $v === '' || $v === []) { return true; } if (is_array($v)) { foreach ($v as $r) { if (is_array($r) ? array_filter($r, fn($x) => $x !== null && $x !== '' && $x !== false) : ($r !== null && $r !== '')) { return false; } } return true; } return false; };
$ensure = function ($typeHandle, array $need, string $tabName = 'Photograph details') use ($es, $APPLY, &$n) {
  $t = $es->getEntryTypeByHandle($typeHandle); $layout = $t->getFieldLayout(); $have = array_map(fn($f) => $f->handle, $layout->getCustomFields());
  $miss = array_values(array_diff($need, $have)); if (!$miss) { return; }
  echo "   type $typeHandle gains ($tabName): " . implode(', ', $miss) . PHP_EOL; if (!$APPLY) { return; }
  $tabs = $layout->getTabs(); $tab = null; foreach ($tabs as $x) { if ($x->name === $tabName) { $tab = $x; } }
  if (!$tab) { $tab = new \craft\models\FieldLayoutTab(['name' => $tabName, 'layout' => $layout]); $tabs[] = $tab; }
  $els = $tab->getElements(); foreach ($miss as $h) { $els[] = new \craft\fieldlayoutelements\CustomField(Craft::$app->getFields()->getFieldByHandle($h)); }
  $tab->setElements($els); $layout->setTabs($tabs); $t->setFieldLayout($layout);
  if (!$es->saveEntryType($t)) { throw new \RuntimeException(json_encode($t->getFirstErrors())); } $n++;
};
$move = function (Entry $e, string $section) use ($es, $ensure, $empty, $APPLY, &$n, &$redirects) {
  if ($e->section->handle === $section) { return $e; }
  $sec = $es->getSectionByHandle($section); $type = $sec->getEntryTypes()[0]; $vals = array_filter($e->getSerializedFieldValues(), fn($v) => !$empty($v));
  echo "   move #{$e->id} {$e->section->handle} -> $section, carrying " . count($vals) . ' fields' . PHP_EOL;
  $ensure($type->handle, array_keys($vals)); $oldUri = $e->uri;
  if (!$APPLY) { return $e; }
  $e->sectionId = $sec->id; $e->setTypeId($type->id); $e->setFieldValues($vals);
  if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException("move #{$e->id} " . json_encode($e->getFirstErrors())); } $n++;
  $e = Entry::find()->id($e->id)->status(null)->one(); if ($oldUri && $oldUri !== $e->uri) { $redirects[$oldUri] = $e->uri; } return $e;
};
$merge = function (Entry $keep, Entry $drop) use ($el, $db, $ensure, $empty, $handles, $APPLY, &$n, &$redirects) {
  $kv = $keep->getSerializedFieldValues(); $dv = array_filter($drop->getSerializedFieldValues(), fn($v) => !$empty($v));
  $ensure($keep->type->handle, array_keys($dv));
  $fields = Craft::$app->getFields(); $set = []; $kept = [];
  foreach ($dv as $h => $v) {
    $f = $fields->getFieldByHandle($h); $k = $kv[$h] ?? null;
    if ($f instanceof \craft\fields\BaseRelationField) { $u = array_values(array_unique(array_merge((array)$k, (array)$v))); if ($u != (array)$k) { $set[$h] = $u; } continue; }
    if ($f instanceof \craft\fields\Table) { $rows = $empty($k) ? [] : array_values((array)$k); $sig = array_map(fn($r) => json_encode(array_values(array_filter((array)$r, 'is_string'))), $rows); $add = 0;
      foreach ((array)$v as $r) { $s = json_encode(array_values(array_filter((array)$r, 'is_string'))); if (!in_array($s, $sig, true) && !$empty([$r])) { $rows[] = $r; $add++; } } if ($add) { $set[$h] = $rows; } continue; }
    if ($empty($k)) { if (is_string($v) && mb_strlen(trim($v)) > 40 && str_contains((string)($kv['body'] ?? ''), mb_substr(trim($v), 0, 120))) { echo "     $h already in #{$keep->id}'s body; not repeated" . PHP_EOL; continue; } $set[$h] = $v; continue; }
    if (trim(is_scalar($v) ? (string)$v : json_encode($v)) !== trim(is_scalar($k) ? (string)$k : json_encode($k))) { $kept[$h] = $v; }
  }
  $extra = [];
  foreach (['legacyUrl', 'legacyKey', 'sourcePath'] as $h) { if (isset($kept[$h])) { $extra[] = "$h {$kept[$h]}"; unset($kept[$h]); } }
  if ($extra) { if (!in_array('recordProvenance', $handles($keep), true)) { $ensure($keep->type->handle, ['recordProvenance'], 'Record'); }
    $prov = in_array('recordProvenance', $handles($keep), true) ? trim((string)($keep->getFieldValue('recordProvenance') ?? '')) : ''; $note = "Merged #{$drop->id} 7 Oct 2026; also: " . implode('; ', $extra);
    $set['recordProvenance'] = mb_substr($prov ? "$prov | $note" : $note, 0, 255); }
  echo "   merge #{$drop->id} into #{$keep->id}: sets " . (implode(', ', array_keys($set)) ?: 'nothing') . PHP_EOL;
  foreach ($kept as $h => $v) { echo "     kept #{$keep->id}'s $h; #{$drop->id} held: " . mb_substr(str_replace("\n", ' / ', is_scalar($v) ? (string)$v : json_encode($v, JSON_UNESCAPED_UNICODE)), 0, 160) . PHP_EOL; }
  $in = $db->createCommand("select r.sourceId, f.handle from {{%relations}} r join {{%fields}} f on f.id=r.fieldId join {{%elements}} e on e.id=r.sourceId where r.targetId={$drop->id} and e.revisionId is null and e.draftId is null and e.dateDeleted is null and r.sourceId<>{$keep->id}")->queryAll();
  foreach ($in as $r) { echo "     link #{$r['sourceId']} {$r['handle']} -> #{$keep->id}" . PHP_EOL; }
  if (!$APPLY) { return; }
  $keep->setFieldValues($set); if (!$el->saveElement($keep)) { throw new \RuntimeException("#{$keep->id} " . json_encode($keep->getFirstErrors())); } $n++;
  foreach ($in as $r) { $s = Entry::find()->id($r['sourceId'])->status(null)->one(); $ids = $s->getFieldValue($r['handle'])->status(null)->ids();
    $s->setFieldValue($r['handle'], array_values(array_unique(array_map(fn($i) => $i == $drop->id ? $keep->id : $i, $ids)))); if (!$el->saveElement($s)) { throw new \RuntimeException("#{$s->id}"); } $n++; }
  $redirects[$drop->uri] = $keep->uri;
  if (!$el->deleteElement($drop)) { throw new \RuntimeException("retire #{$drop->id}"); } $n++;
};
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
foreach ([[1434, 27374, null], [865, 3179, null], [2689, 12558, 'articles']] as [$k, $d, $to]) {
  $keep = $get($k); $drop = $get($d); echo "#$k <- #$d" . PHP_EOL;
  if (!$drop) { echo '   done already' . PHP_EOL; continue; }
  if ($to) { $keep = $move($keep, $to); }
  $merge($keep, $drop);
}
echo '#28295 -> articles' . PHP_EOL; $m = $get(28295); $move($m, 'articles');
foreach ($redirects as $a => $b) { echo "redirect $a -> $b" . PHP_EOL; }
if ($APPLY && $redirects) {
  $cfg = \Craft::getAlias('@root') . '/config/redirects.php'; $s = file_get_contents($cfg); $add = "    /* Merged and moved, 7 October 2026 (merge_and_move_2026_10_07.php). */\n";
  foreach ($redirects as $a => $b) { if (!str_contains($s, "'$a'")) { $add .= "    '$a' => '$b',\n"; } }
  $s = preg_replace('~(\$moved = \[\n)~', '$1' . str_replace('$', '\$', $add), $s, 1); file_put_contents($cfg, $s);
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('merge_and_move_2026_10_07.php', $n, 'verified', 'three merges (keep lower id) and #28295 to articles'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
