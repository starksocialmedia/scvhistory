/**
 * Ygnacio del Valle #293: the withheld body is Leon Worden's (Nathan,
 * 3 October 2026: "Yes, relabel Ygnacio del Valle's body as Leon's text with
 * LW2052 cited. At 96% it is his, and attributing it is the honest fix").
 *
 * 52 of its 57 sentences are verbatim on LW2052 (SCVHistory.com,
 * /scvhistory/lw2052.htm; Reggie mirror, manifest-matched; a copy in
 * inventory/legacy/fetched). The other five are his too, copy-edited by
 * someone else ("awarded awarded", "Acutally", "at Los Angeles", a dropped
 * "and", added commas). The archive presents legacy text as its author wrote
 * it, so the body is replaced with his paragraphs from LW2052, verbatim, and
 * the offices list he gives; the body is then labelled
 * legacy-leon and an "About this text" note names him and the page. The
 * existing correction note (the Assembly of 1852, not the first Legislature)
 * stays: it corrects his text without rewriting it.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/relabel_ygnacio_del_valle.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', str_replace("\u{00A0}", ' ', html_entity_decode(strip_tags((string)$s), ENT_QUOTES))));
$e = Entry::find()->id(293)->status(null)->one();
$lw = $ws(@file_get_contents("$root/inventory/legacy/fetched/lw2052.txt"));
/* Leon's prose, verbatim: the paragraphs of LW2052 from "Ygnacio Ramón" to the offices list. */
$lines = array_values(array_filter(array_map('trim', explode("\n", (string)@file_get_contents("$root/inventory/legacy/fetched/lw2052.txt"))), fn($l) => $l !== ''));
$i0 = null; $i1 = null;
foreach ($lines as $i => $l) { if ($i0 === null && str_starts_with($l, 'Ygnacio Ramón de Jesus del Valle')) { $i0 = $i; } if ($i0 !== null && $i1 === null && $l === 'City of Los Angeles') { $i1 = $i; } }
$PROSE = ($i0 !== null && $i1 !== null) ? array_slice($lines, $i0, $i1 - $i0) : [];
$NOTE = 'This text is Leon Worden\'s, from his page on Ygnacio del Valle, LW2052, on SCVHistory.com (/scvhistory/lw2052.htm).';
$bad = [];
if (!$e || $e->title !== 'Ygnacio del Valle') { $bad[] = '#293 is not Ygnacio del Valle'; }
$body = (string)$e->body;
$at = strpos($body, '<strong>') !== false ? strrpos(substr($body, 0, strpos($body, '<strong>')), "\n\n") : false;
$list = $at !== false ? ltrim(substr($body, $at)) : '';
if (count($PROSE) < 8) { $bad[] = 'LW2052 prose not found (' . count($PROSE) . ' paragraphs)'; }
if ($list === '' || !str_starts_with(strip_tags($list), 'City of Los Angeles')) { $bad[] = 'the offices list was not found in the body'; }
foreach (array_filter(array_map('trim', explode("\n", strip_tags($list)))) as $l) { if (!str_contains($lw, $ws($l))) { $bad[] = 'list line not on LW2052: ' . mb_substr($l, 0, 60); } }
$new = implode("\n\n", $PROSE) . "\n\n" . $list;
$n = $new === $body ? 0 : 1;
$sents = array_filter(array_map('trim', preg_split('~(?<=[.!?])\s+|\n+~u', strip_tags($new))), fn($s) => str_word_count($s) >= 5);
$off = array_filter($sents, fn($s) => !str_contains($lw, $ws($s)));
if ($off) { $bad[] = count($off) . ' sentences not on LW2052: ' . mb_substr(reset($off), 0, 80); }
$done = $n === 0 && ($e->bodyAuthorship->value ?? '') === 'legacy-leon' && in_array($NOTE, array_column($e->editorNotes ?? [], 'note'), true);
echo $done ? "already done\n" : "#293: body -> Leon's " . count($PROSE) . " paragraphs from LW2052, verbatim, and the offices list as he gives it (" . count($sents) . " sentences, all on LW2052); bodyAuthorship wordpress-import-unsourced -> legacy-leon; About this text note\n";
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
if ($done) { return; }
$rows = array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
if (!in_array($NOTE, array_column($rows, 'note'), true)) { array_unshift($rows, ['heading' => 'About this text', 'position' => 'bottom', 'note' => $NOTE]); }
$e->setFieldValues(['body' => $new, 'bodyAuthorship' => 'legacy-leon', 'editorNotes' => $rows]);
$ok = Craft::$app->getElements()->saveElement($e);
$r = Entry::find()->id(293)->status(null)->one();
$ok = $ok && ($r->bodyAuthorship->value ?? '') === 'legacy-leon' && (string)$r->body === $new;
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('relabel_ygnacio_del_valle.php', 1, $ok ? 'verified' : 'SHORT', 'Ygnacio del Valle: withheld body is Leon Worden\'s LW2052; five copy-edits undone; legacy-leon');
if (!$ok) { throw new \RuntimeException('relabel_ygnacio_del_valle: read-back failed'); }
