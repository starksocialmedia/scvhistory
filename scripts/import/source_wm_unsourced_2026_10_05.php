/**
 * War memorial sourcing, the records still short of the archive's rule (Nathan, 5 October 2026: "Records still unsourced:
 * Ball, Ross, Kenaston, Cone, Rubel, and the eight War on Terror records; and Wilson, Todd, Conant and Colley via the VA
 * Gravesite Locator").
 * Of the thirteen, seven were met by source_wm_remaining_2026_10_04.php (applied 4 October 2026, 21:29): Ball, Cone, Rubel,
 * Sellen, Gelig, Suter, Acosta. This script takes the other six. Research: inventory/review/war-memorial-unsourced-dry-run-2026-10-05.md,
 * with every page relied on saved in inventory/news/war-memorial-2026-10-05/ (manifest.json).
 *   #514 Kenaston: the VA Gravesite Locator lists him (Los Angeles National Cemetery). Two footnotes; the "Not yet sourced"
 *        note becomes a "Sources, 2026" note: half the rule. The locator was approved for Wilson, Todd, Conant and Colley;
 *        it was used for Kenaston because the query is the same kind (a burial in a VA national cemetery), as the 4 October
 *        review proposed. Nathan to confirm.
 *   #524 Colley: Valencia High School's 2003 yearbook pictures Stephen Colley among the class of 2003 seniors. One footnote
 *        added after the existing three; the "Sources, 2026" note says the rule is met.
 *   #528 Wilson, #542 Todd, #534 Conant, #580 Ross: nothing new found. Each "Sources, 2026" note is rewritten to record what
 *        was searched, on the 4 October pattern ("A note asserting no source exists records what was searched").
 * Only footnotes and editor notes are written. factSources, the fact fields and the legacy text are not touched: where a
 * source differs from the record, the note says so (Kenaston's age; Wilson's two dates), and any change waits on Nathan.
 * Exact text: a "Sources, 2026" note is replaced only if it reads exactly as below ($old); a footnote is added only if no
 * footnote already has its text. Idempotent: a record already carrying the new note and footnotes is skipped.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/source_wm_unsourced_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$H = 'Sources, 2026';
$VA = 'https://gravelocator.cem.va.gov/ngl/result';
$R = [
 514 => ['title' => 'Lawrence E. Kenaston',
  'old' => 'Not yet sourced. He does not appear in the Navy\'s 1946 State Summary of War Casualties for California (National Archives NAID 305189), which covers the Marine Corps, dead and missing, by next of kin\'s state.',
  'new' => 'This record meets half of the archive\'s rule for a memorial record. The Department of Veterans Affairs\' burial record confirms his service as a corporal in the Marine Corps, his dates, born February 7, 1912, and died January 11, 1945, and his grave at Los Angeles National Cemetery (note 1); it does not say how he died. His tie to the valley rests on the original page on SCVHistory.com (note 2). The burial record\'s dates make him 32 when he died; the record above gives 34. He is not in the Navy\'s 1946 casualty list for California (National Archives NAID 305189), which by its own notice leaves out deaths in the United States and suicides.',
  'footnotes' => [
   'U.S. Department of Veterans Affairs, Nationwide Gravesite Locator: "KENASTON, LAWRENCE EDWARD, CPL US MARINE CORPS, Date of Birth: 02/07/1912, Date of Death: 01/11/1945, Buried At: SECTION 174 ROW B SITE 10, LOS ANGELES NATIONAL CEMETERY, 950 SOUTH SEPULVEDA BOULEVARD LOS ANGELES, CA 90049." ' . $VA . ' (POST: last name Kenaston, death year 1945; result 1 of 1; read 5 October 2026)',
   'SCVHistory.com, Santa Clarita Valley War Memorial, page for him (/warmemorial/ww2_ekenaston.htm).',
  ]],
 524 => ['title' => 'Stephen Edward Colley',
  'old' => 'This record meets half of the archive\'s rule for a memorial record. The Department of Veterans Affairs\' burial record confirms his service in the Iraq war period, his dates and his grave (note 1), and NPR\'s account confirms the circumstances of his death (note 2); his tie to the valley rests on the original page on SCVHistory.com.',
  'new' => 'This record meets the archive\'s rule for a memorial record. The Department of Veterans Affairs\' burial record confirms his service in the Iraq war period, his dates and his grave (note 1), and NPR\'s account confirms the circumstances of his death (note 2). Valencia High School\'s 2003 yearbook pictures Stephen Colley among the seniors of the class of 2003 (note 4), the school and class the original page on SCVHistory.com gives him; the yearbook does not mention his service, so the match rests on his name, school and class.',
  'footnotes' => [
   'Valencia High School, Voyager (2003 yearbook), page 39, "2003 Seniors": a senior portrait captioned "Stephen Colley"; the index reads "Colley, Stephen 28, 39, 248." SCVHistory.com, /scvhistory/vhs2003yearbook.htm (Valencia High School\'s file copy), page image /scvhistory/files/vhs2003yearbook/page043.jpg (read from the SCVHistory.com mirror, 5 October 2026).',
  ],
  'keepFootnotes' => 3],
 528 => ['title' => 'Robert Michael Wilson',
  'old' => 'Not yet sourced. He died outside a theater of war, so he is in neither the National Archives\' war casualty file nor a Defense Department casualty release.',
  'new' => 'Not yet sourced. He died outside a theater of war, so he is in neither the National Archives\' war casualty file nor a Defense Department casualty release. Searched 4 and 5 October 2026 and not found: the Department of Veterans Affairs\' Nationwide Gravesite Locator, under Wilson, Robert, by the middle name Michael, by birth in 1983 and in April 1983, and by death in 2002, August 2002 and September 2002; the web; the pages of SCVHistory.com; and the 2001 yearbooks of Valencia High School and Saugus High School. The original page on SCVHistory.com gives two dates of death, August 13, 2002 in its text and August 31, 2002 in its fields; no source has settled which is right.',
  'footnotes' => []],
 542 => ['title' => 'Dean Glenn Todd Jr',
  'old' => 'This record meets half of the archive\'s rule for a memorial record. Stars and Stripes records his death in service and gives his hometown as Canyon Country (note 1); no federal record of his death is in the archive.',
  'new' => 'This record meets half of the archive\'s rule for a memorial record. Stars and Stripes records his death in service and gives his hometown as Canyon Country (note 1); no federal record of his death is in the archive. Searched 4 and 5 October 2026 and not found: the Department of Veterans Affairs\' Nationwide Gravesite Locator, under Todd, Todd-Eckard and Eckard, with the first name Dean, by birth in 1982 and 1983, and by death in 2004, August 2004 and September 2004; and the web.',
  'footnotes' => []],
 534 => ['title' => 'John Michael Conant',
  'old' => 'This record does not yet meet the archive\'s rule for a memorial record. His grave marker, photographed for SCVHistory.com, confirms his rank and dates (note 1); no federal record or newspaper account of his death is in the archive.',
  'new' => 'This record does not yet meet the archive\'s rule for a memorial record. His grave marker, photographed for SCVHistory.com, confirms his rank and dates (note 1); no federal record or newspaper account of his death is in the archive. Searched 4 and 5 October 2026 and not found: the Department of Veterans Affairs\' Nationwide Gravesite Locator, under Conant with the first name John, by birth in 1971, and by death in 2008 and April 2008, and its listing for the National Memorial Cemetery of the Pacific (Punchbowl), which holds four Conants, none of them him; and the web.',
  'footnotes' => []],
 580 => ['title' => 'Thomas Milton Ross Jr.',
  'old' => 'This record meets half of the archive\'s rule for a memorial record. The American Battle Monuments Commission lists a Thomas M. Ross, Gunner\'s Mate First Class, U.S. Navy, from California, missing in action and named on the Walls of the Missing at Manila (note 1); the match rests on his name, rating and state, since the Commission gives no hometown. His tie to the valley rests on the original page on SCVHistory.com.',
  'new' => 'This record meets half of the archive\'s rule for a memorial record. The American Battle Monuments Commission lists a Thomas M. Ross, Gunner\'s Mate First Class, U.S. Navy, from California, missing in action and named on the Walls of the Missing at Manila (note 1); the match rests on his name, rating and state, since the Commission gives no hometown. His tie to the valley rests on the original page on SCVHistory.com. He is not in the Navy\'s 1946 casualty list for California (National Archives NAID 305189), among either the dead or the 70 missing (read 4 and 5 October 2026).',
  'footnotes' => []],
];
$bad = []; $plan = [];
foreach ($R as $id => $r) {
  $e = Entry::find()->id($id)->status(null)->one();
  if (!$e || $e->title !== $r['title']) { $bad[] = "#$id is not {$r['title']}"; continue; }
  $fn = array_values(array_filter($e->footnotes ?? [], fn($x) => is_array($x) && trim((string)($x['note'] ?? '')) !== ''));
  $have = array_map(fn($x) => (string)$x['note'], $fn);
  $add = array_values(array_filter($r['footnotes'], fn($t) => !in_array($t, $have, true)));
  if (isset($r['keepFootnotes']) && $add && count($fn) !== $r['keepFootnotes']) { $bad[] = "#$id has " . count($fn) . " footnotes, expected {$r['keepFootnotes']}"; continue; }
  if (!isset($r['keepFootnotes']) && $r['footnotes'] && $fn && $add) { $bad[] = "#$id already has footnotes; expected none"; continue; }
  $src = array_values(array_filter($e->editorNotes ?? [], fn($x) => ($x['heading'] ?? '') === $H));
  if (count($src) !== 1) { $bad[] = "#$id has " . count($src) . " \"$H\" notes, expected 1"; continue; }
  $cur = trim((string)$src[0]['note']);
  if ($cur === $r['new'] && !$add) { echo "#$id {$r['title']}: already done" . PHP_EOL; continue; }
  if ($cur !== $r['old'] && $cur !== $r['new']) { $bad[] = "#$id: the \"$H\" note is not the text this script expects"; continue; }
  foreach ($r['footnotes'] as $i => $t) { if (!str_contains($r['new'], 'note ' . (($r['keepFootnotes'] ?? 0) + $i + 1)) && $i === 0) { $bad[] = "#$id: the note does not cite its new footnote"; } }
  $plan[$id] = [$r, $add, count($fn)];
  echo "#$id {$r['title']}: " . ($cur === $r['old'] ? 'note replaced' : 'note already new') . ', ' . count($add) . ' footnote(s) added after ' . count($fn) . PHP_EOL;
  echo '   OLD: ' . $cur . PHP_EOL . '   NEW: ' . $r['new'] . PHP_EOL;
  foreach ($add as $i => $t) { echo '   FN' . (count($fn) + $i + 1) . ': ' . $t . PHP_EOL; }
}
if (preg_match('~\x{2014}~u', json_encode($R, JSON_UNESCAPED_UNICODE))) { $bad[] = 'an em dash in the text'; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$n = 0; $tx = Craft::$app->getDb()->beginTransaction();
try {
  foreach ($plan as $id => [$r, $add, $had]) {
    $e = Entry::find()->id($id)->status(null)->one();
    $ed = [];
    foreach ($e->editorNotes ?? [] as $x) {
      $h = (string)($x['heading'] ?? ''); $t = trim((string)($x['note'] ?? '')); if ($t === '') { continue; }
      if ($h === $H) { $t = $r['new']; }
      $ed[] = ['heading' => $h, 'position' => (string)($x['position'] ?? 'bottom') ?: 'bottom', 'note' => $t];
    }
    $fn = [];
    foreach ($e->footnotes ?? [] as $x) { if (trim((string)($x['note'] ?? '')) === '') { continue; } $fn[] = ['number' => (string)$x['number'], 'note' => (string)$x['note'], 'source' => (string)($x['source'] ?? '')]; }
    foreach ($add as $t) { $fn[] = ['number' => (string)(count($fn) + 1), 'note' => $t, 'source' => 'editorial-2026']; }
    $e->setFieldValues(['footnotes' => $fn, 'editorNotes' => $ed]);
    if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); } $n++;
  }
  $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK: ' . $t->getMessage() . PHP_EOL; throw $t; }
foreach ($plan as $id => [$r, $add, $had]) {
  $b = Entry::find()->id($id)->status(null)->one();
  $s = array_values(array_filter($b->editorNotes ?? [], fn($x) => ($x['heading'] ?? '') === $H));
  if (count($s) !== 1 || trim((string)$s[0]['note']) !== $r['new'] || count(iterator_to_array($b->footnotes ?? [])) !== $had + count($add)) { throw new \RuntimeException("#$id not read back"); }
}
$root = \Craft::getAlias('@root');
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('source_wm_unsourced_2026_10_05.php', $n, 'verified', 'war memorial: Kenaston half-sourced from the VA locator; Colley meets the rule (Valencia High 2003 yearbook); Wilson, Todd, Conant and Ross notes record what was searched');
echo "done: $n records" . PHP_EOL;
