/**
 * John Michael McGrath's 2009 Newhall School District holding (#28455): howEnded said "resigned", and the one source it cites
 * prints only that he would (Nathan's overnight brief of 10 October 2026, item 8: "Fix it"). The district's release of
 * 4 November 2009 (footnote 4, saved as inventory/news/walters-2026-10-03/scvtv_nsd110409.txt): "He will submit a resignation
 * effective December 8th, creating the open seat." inventory/review/how-ended-vs-sources-2026-10-10.md found no source that
 * prints the resignation made.
 * - howEnded resigned -> unknown, the only value that does not state what no source prints.
 * - A footnote says what the release announced and that no source read prints it done.
 * - termEnd stays 8 December 2009, the effective date the release announced; the footnote says so. Whether the holding should
 *   stand at all (disputed point A11, inventory/review/disputed-settled-2026-10-09.md) is Nathan's and is not touched.
 * Idempotent. Dry run by default; $APPLY = true writes.
 */
use craft\elements\Entry;
$APPLY = false;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$SRC = "$root/inventory/news/walters-2026-10-03/scvtv_nsd110409.txt";
if (!is_file($SRC)) { foreach (glob("$root/inventory/*/walters-2026-10-03/scvtv_nsd110409.*") ?: [] as $g) { $SRC = $g; break; } }
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['file', 'the district release of 4 November 2009, as saved', $SRC],
  ['record', 'Craft fields howEnded, termEnd and footnotes on #28455', 'the release', 'read'],
]);
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$said = 'He will submit a resignation effective December 8th, creating the open seat.';
$txt = preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string)@file_get_contents($SRC)), ENT_QUOTES | ENT_HTML5));
if (!str_contains($txt, $said)) { throw new \RuntimeException("the saved release does not print \"$said\""); }
$h = Entry::find()->id(28455)->status(null)->one();
if (!$h || !str_starts_with($h->title, 'John Michael McGrath')) { throw new \RuntimeException('#28455 not as expected'); }
$NOTE = 'How the term ended: the district announced on 4 November 2009 that he "will submit a resignation effective December 8th, creating the open seat" (note 4). No source read prints that he resigned, so the archive does not say so; the end date is the effective date the district announced.';
$rows = array_map(fn($r) => ['number' => (string)$r['number'], 'note' => (string)$r['note'], 'source' => (string)($r['source'] ?? '')], iterator_to_array($h->footnotes));
$has = (bool)array_filter($rows, fn($r) => $r['note'] === $NOTE);
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo "#28455: howEnded \"{$h->howEnded}\" -> \"unknown\"; termEnd \"{$h->termEnd}\" kept; footnote " . ($has ? 'there already' : 'added: ' . $NOTE) . PHP_EOL;
if (!$APPLY) { echo 'nothing written' . PHP_EOL; return; }
if ((string)$h->howEnded === 'unknown' && $has) { echo 'done already' . PHP_EOL; return; }
if (!$has) { $rows[] = ['number' => (string)(count($rows) + 1), 'note' => $NOTE, 'source' => 'editorial-2026']; }
$h->setFieldValue('footnotes', $rows); $h->setFieldValue('howEnded', 'unknown');
if (!$el->saveElement($h)) { throw new \RuntimeException(json_encode($h->getFirstErrors())); } $n++;
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('mcgrath_how_ended_2026_10_10.php', $n, 'verified', 'McGrath #28455: howEnded resigned -> unknown; the announcement footnoted');
echo "done: $n" . PHP_EOL;
