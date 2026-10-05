/**
 * The Santa Clarita Community College District's elections (Nathan, 5 October 2026: "The college district's 36 contests from
 * 1967 to 2024 should become election records. That is the earliest elected body in the valley we would hold"). From the
 * "elections" list in inventory/review/public-bodies-college-2026-10-05.json, each with its sources.
 * One election record per contest (a numbered seat or a trustee area is a contest, as Hart's areas are), with a candidacy for
 * each candidate. Not made here: the four contests cancelled for want of candidates (2013 Seats 1, 3 and 5; 2016 Area 3;
 * 2022), which the archive records as appointed terms on the trustee, as for the sole-candidate terms of 4 October; and 1973,
 * whose return was not found. A candidate is linked to a person record only where the identity is settled (Scott Wilk, #335).
 * Saved disabled with the district until Nathan has read it. Idempotent: matched by slug. Dry run by default. $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_coc_elections_2026_10_05.php'))"
 */
use craft\elements\Entry;
use craft\helpers\StringHelper;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $es = Craft::$app->getEntries();
$D = json_decode(file_get_contents("$root/inventory/review/public-bodies-college-2026-10-05.json"), true)[0]['elections'];
$body = Entry::find()->section('organizations')->title('Santa Clarita Community College District')->status(null)->one();
if (!$body) { echo "REFUSED: no district record\n"; return; }
$PERSON = ['Scott T. Wilk' => 335];
$plan = []; $skip = [];
foreach ($D as $x) {
  if (str_starts_with($x['kind'], 'cancelled') || str_contains($x['kind'], 'not found') || !($x['candidates'] ?? [])) { $skip[] = ($x['dateText'] ?? $x['date']) . ', ' . ($x['contest'] ?? '') . " ({$x['kind']})"; continue; }
  $slug = 'scccd-board-election-' . StringHelper::toKebabCase(($x['date'] ?? '') . ' ' . ($x['contest'] ?? ''));
  $plan[] = [$slug, $x];
}
foreach ($plan as [$slug, $x]) { $have = Entry::find()->section('elections')->slug($slug)->status(null)->one(); echo ($have ? "exists #{$have->id}" : 'create') . ": {$x['dateText']}, {$x['contest']}: " . count($x['candidates']) . " candidates\n"; }
echo count($plan) . ' to make; not made: ' . count($skip) . "\n"; foreach ($skip as $s) { echo "   skip $s\n"; }
if (!$APPLY) { return; }
$secE = $es->getSectionByHandle('elections'); $secC = $es->getSectionByHandle('candidacies'); $n = 0;
$fn = fn(array $notes) => array_map(fn($i, $t) => ['number' => (string)($i + 1), 'note' => $t, 'source' => 'editorial-2026'], array_keys($notes), $notes);
foreach ($plan as [$slug, $x]) {
  $e = Entry::find()->section('elections')->slug($slug)->status(null)->one();
  if (!$e) { $e = new Entry(); $e->sectionId = $secE->id; $e->setTypeId($secE->getEntryTypes()[0]->id); $e->slug = $slug; $e->enabled = false; }
  $edtf = preg_match('~^\d{4}(-\d{2}(-\d{2})?)?$~', $x['date']) ? $x['date'] : '';
  $kind = str_starts_with($x['kind'], 'special') || str_starts_with($x['kind'], 'formation') ? 'special' : 'general';
  $ed = [['heading' => 'The contest', 'note' => trim(($x['contest'] ?? '') . (($x['system'] ?? '') ? '; ' . $x['system'] : '') . ($x['kind'] !== 'regular' ? '; ' . $x['kind'] : '') . '.'), 'position' => 'top']];
  if (is_string($x['votes'] ?? null)) { $ed[] = ['heading' => 'The count', 'note' => 'No return for this contest is held; the result rests on the accounts cited.', 'position' => 'bottom']; }
  $e->setFieldValues(['electionDate' => $x['dateText'], 'electionDateEdtf' => $edtf, 'electionKind' => $kind, 'seatsUp' => (int)($x['seats'] ?? 1), 'seatsUpEvidence' => 'contemporary',
    'electionBody' => [$body->id], 'footnotes' => $fn($x['sources'] ?? []), 'editorNotes' => $ed, 'recordProvenance' => 'create_coc_elections_2026_10_05.php, 5 October 2026']);
  if (!$el->saveElement($e)) { throw new \RuntimeException("$slug " . json_encode($e->getFirstErrors())); } $n++;
  foreach ($x['candidates'] as $c) {
    if (Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $e, 'field' => 'candidacyElection'])->nameAsPrinted($c['name'])->exists()) { continue; }
    $k = new Entry(); $k->sectionId = $secC->id; $k->setTypeId($secC->getEntryTypes()[0]->id); $k->enabled = false;
    $notes = array_values(array_filter([($c['source'] ?? '') ?: (($x['sources'] ?? [])[0] ?? ''), $c['note'] ?? '']));
    $k->setFieldValues(['candidacyElection' => [$e->id], 'candidacyPerson' => isset($PERSON[$c['name']]) ? [$PERSON[$c['name']]] : [], 'nameAsPrinted' => $c['name'],
      'votesAsPrinted' => isset($c['votes']) && $c['votes'] !== null ? number_format($c['votes']) : '', 'votes' => $c['votes'] ?? null,
      'outcome' => !empty($c['elected']) ? 'elected' : 'not-elected', 'outcomeEvidence' => 'contemporary', 'footnotes' => $fn($notes),
      'recordProvenance' => 'create_coc_elections_2026_10_05.php, 5 October 2026']);
    if (!$el->saveElement($k)) { throw new \RuntimeException("candidacy {$c['name']} " . json_encode($k->getFirstErrors())); } $n++;
  }
}
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('create_coc_elections_2026_10_05.php', $n, 'verified', 'Santa Clarita Community College District: ' . count($plan) . ' elections and their candidacies, disabled with the district');
echo "done: $n\n";
