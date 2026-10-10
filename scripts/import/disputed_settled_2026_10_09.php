/**
 * The disputed-but-stated items our own notes already settle (Nathan's brief of 9 October 2026, item 12; the list is
 * inventory/review/overnight-2026-10-08/disputed-stated-as-fact-2026-10-08.md, section (a); the reasoning for each of the
 * twelve is inventory/review/disputed-settled-2026-10-09.md).
 *
 * One of the twelve is settled by a documented ruling: A2, the "prefer Perkins" rule. Nathan approved narrowing it on
 * 4 October 2026 (inventory/review/a-b-perkins-sources.md, K9: "The rule now trusts his verbatim transcriptions only";
 * K12: a figure that is his summary of a document is not preferred until the document is seen). Two texts still state the
 * withdrawn rule as the archive's present policy:
 *   #333 Arthur Buckingham Perkins, body (editorial-2026, written by build_perkins_profile.php; not Leon's prose)
 *   #1434 Rancho San Francisco (1957), editor note "Reading this document" (written by build_antonio_del_valle_profile.php)
 * This rewords the one clause in each so it states the narrowed rule. Nothing else in either text changes. Leon's prose is
 * not touched. The two builders do not revert it: build_perkins_profile.php refuses a body edited since the WordPress
 * import, and build_antonio_del_valle_profile.php writes the document note only when it creates the document.
 *
 * It also searches every entry's stored content for the two old phrasings and lists any other place they appear, without
 * changing it.
 *
 * Idempotent: a record whose text already reads the new way is reported and not saved. Dry run by default; $APPLY = true
 * writes. Nathan reads the dry run before any apply.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/disputed_settled_2026_10_09.php'))"
 */
use craft\elements\Entry;

$APPLY = false;

$root = \Craft::getAlias('@root');
$PROFILES = "$root/docs/PROFILES.md";
$DOSSIER = "$root/inventory/review/a-b-perkins-sources.md";
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['file', 'the Perkins dossier, for K9 and K12 (the rule as narrowed and approved on 4 October 2026)', $DOSSIER],
  ['file', 'docs/PROFILES.md, the Perkins reliability rule', $PROFILES],
  ['record', 'Craft field body of #333 and editorNotes of #1434', 'the archive\'s Perkins rule as K9 states it', 'read'],
  ['record', 'every entry\'s stored content (elements_sites.content), searched for the old phrasings', 'the same rule', 'read'],
]);
/* The APPLY warning follows reads(): check_census_reads fails a script that prints before it. */
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

/* The rule, from the files themselves: refuse if K9 or K12 does not say what this script relies on. */
$dossier = file_get_contents($DOSSIER);
$profiles = file_get_contents($PROFILES);
$k9 = str_contains($dossier, 'The rule now trusts his verbatim transcriptions only') && str_contains($dossier, 'limit \'prefer Perkins\' to facts he transcribes') && str_contains($dossier, '**Applied 2026-10-04** (approved by Nathan; Reynolds Section 2.1');
$k12 = str_contains($dossier, 'neither figure is preferred until the deed is seen');
echo 'K9 in the dossier (verbatim transcriptions only; approved by Nathan, applied 4 October): ' . ($k9 ? 'yes' : 'NO') . PHP_EOL;
echo 'K12 in the dossier (a summary of a document is not preferred): ' . ($k12 ? 'yes' : 'NO') . PHP_EOL;
echo 'PROFILES.md still words the rule "Accept Perkins where he quotes or cites a document": ' . (str_contains($profiles, 'Accept Perkins where he quotes or cites a document') ? 'yes (wider than K9; for Nathan, not changed here)' : 'no') . PHP_EOL;
if (!$k9 || !$k12) { throw new \RuntimeException('the dossier does not hold K9 and K12 as this script expects; stopped'); }

$PLAN = [
  [
    'id' => 333, 'title' => 'Arthur Buckingham Perkins', 'field' => 'body',
    'old' => 'and listed his references, so where a later retelling departs from him on a figure, the archive prefers Perkins until an original is seen. His figures still need checking:',
    'new' => 'and listed his references, and where he transcribes a document word for word the archive accepts his text. His summaries and figures, like any later retelling, still need checking against an original:',
  ],
  [
    'id' => 1434, 'title' => 'Rancho San Francisco: A Study of a California Land Grant (1957)', 'field' => 'editorNotes', 'heading' => 'Reading this document',
    'old' => 'and cited them, and this archive prefers his figures to later retellings until an original is seen. They still need checking.',
    'new' => 'and cited them. Where he transcribes a document word for word this archive accepts his text; his summaries and figures, like later retellings, need checking against an original.',
  ],
];

$els = Craft::$app->getElements(); $bad = []; $todo = []; $n = 0;
echo str_repeat('=', 78) . PHP_EOL . ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
foreach ($PLAN as $p) {
  $e = Entry::find()->id($p['id'])->status(null)->one();
  if (!$e || $e->title !== $p['title']) { $bad[] = "#{$p['id']} not found or retitled"; continue; }
  $has = []; foreach ($e->getFieldLayout()->getCustomFields() as $f) { $has[$f->handle] = true; }
  if (!isset($has[$p['field']])) { $bad[] = "#{$p['id']} has no {$p['field']} field"; continue; }
  if ($p['field'] === 'body') {
    if (isset($has['bodyAuthorship']) && (string)$e->getFieldValue('bodyAuthorship') !== 'editorial-2026') { $bad[] = "#{$p['id']} body is not editorial-2026: not ours to reword"; continue; }
    $cur = (string)$e->getFieldValue('body');
    $cOld = substr_count($cur, $p['old']); $cNew = substr_count($cur, $p['new']);
    echo PHP_EOL . "#{$p['id']} {$p['title']}, body (editorial-2026)" . PHP_EOL;
    if ($cNew === 1 && $cOld === 0) { echo '  already reads the new way; nothing to do' . PHP_EOL; continue; }
    if ($cOld !== 1) { $bad[] = "#{$p['id']} body: the old clause found $cOld times, expected 1"; continue; }
    echo '  before: ...' . $p['old'] . '...' . PHP_EOL . '  after:  ...' . $p['new'] . '...' . PHP_EOL;
    $todo[] = [$e, 'body', str_replace($p['old'], $p['new'], $cur)];
  } else {
    $rows = array_values(array_filter((array)$e->getFieldValue('editorNotes'), fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== ''));
    $hit = array_keys(array_filter($rows, fn($r) => ($r['heading'] ?? '') === $p['heading']));
    echo PHP_EOL . "#{$p['id']} {$p['title']}, editor note \"{$p['heading']}\"" . PHP_EOL;
    if (count($hit) !== 1) { $bad[] = "#{$p['id']}: " . count($hit) . " notes headed {$p['heading']}, expected 1"; continue; }
    $i = $hit[0]; $note = (string)$rows[$i]['note'];
    if (str_contains($note, $p['new']) && !str_contains($note, $p['old'])) { echo '  already reads the new way; nothing to do' . PHP_EOL; continue; }
    if (substr_count($note, $p['old']) !== 1) { $bad[] = "#{$p['id']}: the old clause not found once in the note"; continue; }
    $newNote = str_replace($p['old'], $p['new'], $note);
    echo '  before: ' . $note . PHP_EOL . '  after:  ' . $newNote . PHP_EOL;
    $out = array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'note' => (string)($r['note'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom')], $rows);
    $out[$i]['note'] = $newNote;
    $todo[] = [$e, 'editorNotes', $out];
  }
}

/* Anywhere else the old phrasings stand: reported, not changed. */
$others = (new \craft\db\Query())->select(['es.elementId'])->from(['es' => '{{%elements_sites}}'])
  ->innerJoin(['e' => '{{%elements}}'], '[[e.id]] = [[es.elementId]]')
  ->where(['e.dateDeleted' => null, 'e.revisionId' => null, 'e.draftId' => null])
  ->andWhere(['or', ['like', 'es.content', 'prefers Perkins'], ['like', 'es.content', 'prefers his figures'], ['like', 'es.content', 'prefer Perkins']])
  ->column();
$others = array_values(array_diff(array_map('intval', $others), array_column($PLAN, 'id')));
echo PHP_EOL . 'other live entries holding "prefer(s) Perkins" or "prefers his figures": ' . ($others ? implode(', ', array_map(fn($i) => "#$i", $others)) . ' (listed for Nathan, not changed)' : 'none') . PHP_EOL;

echo PHP_EOL . 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
echo 'to change: ' . count($todo) . ' of ' . count($PLAN) . ' records' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply. A second run after an apply changes nothing.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
  foreach ($todo as [$e, $h, $v]) {
    $e->setFieldValue($h, $v);
    if (!$els->saveElement($e)) { throw new \RuntimeException("#{$e->id}: " . json_encode($e->getFirstErrors())); }
    $n++;
  }
  $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }

$short = [];
foreach ($PLAN as $p) {
  $e = Entry::find()->id($p['id'])->status(null)->one();
  $text = $p['field'] === 'body' ? (string)$e->getFieldValue('body')
    : implode("\n", array_map(fn($r) => (string)($r['note'] ?? ''), array_filter((array)$e->getFieldValue('editorNotes'), 'is_array')));
  if (!str_contains($text, $p['new']) || str_contains($text, $p['old'])) { $short[] = "#{$p['id']}"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK') . "; saved $n" . PHP_EOL;
if ($n) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('disputed_settled_2026_10_09.php', $n, $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'the prefer-Perkins clause on #333 and #1434 reworded to the rule as narrowed on 4 October (K9)'); }
if ($short) { throw new \RuntimeException('disputed_settled_2026_10_09: read-back short ' . implode('; ', $short)); }
