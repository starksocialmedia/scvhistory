/**
 * Silent-faults audit finding 4 (inventory/review/silent-faults-audit-2026-10-05.md). Thirteen images carry
 * "Biography by Friends of Hart Park" as their caption (photoCaptionExt), title and alt text, so thirteen
 * photograph pages print a byline as the caption of a tobacco card, a mural, a park entrance.
 *
 * WHY THE EXTRACTOR TOOK THAT LINE. The crawl's image extractor (the extract_images shape kept in
 * crawl_reynolds.py) looks for the caption inside the image's nearest enclosing <td> or <div>. On these
 * pages that <td> holds the whole page: the image, Leon's text, and, at the foot, the Friends of Hart Park
 * biography of Hart, headed <div class="subhed2">William S. Hart</div><div class="caption">Biography by
 * Friends of Hart Park</div>. That div is the only element on the page with the class "caption", so the
 * extractor took it as the image's caption, and the credit line under the biography ("Hart biography
 * (c) Friends of Hart Park, Used by permission.") as the image's credit. apply_extracted_captions.php then
 * wrote it as caption, title and alt, because nothing rejected a byline.
 *
 * WHAT THE PAGES GIVE. inventory/review/legacy-evidence-2026-10-05.json (extract_legacy_evidence_2026_10_05.py,
 * read from the mirror) records, for each page, the image's own block: from the image to the next image, rule,
 * body text or heading. None of the 13 has a caption element there. What sits under each image is "Click
 * image to enlarge" and then the page's body text, which the photograph record already prints as its body.
 * So the caption is cleared, not replaced: the page gives none, and repeating the body's first paragraph
 * under the plate would be a caption the legacy page never had.
 *
 * WHAT IS WRITTEN, per asset:
 *   photoCaptionExt  cleared.
 *   alt              the photograph record's own title, its closing period dropped. Nothing else.
 *   title            the same. The asset was titled from its filename before the faulty caption landed;
 *                    the record title says more and is not invented.
 * Not touched: photoCredit. It carries the biography's credit, not the image's, on these 13 and on 22 more
 * assets (35 in all). That is a separate decision for Nathan and is listed in the report.
 *
 * The photograph record is the one whose photoSourceCode or legacyKey equals the image code; lw3445c, one of
 * three plates on page lw3445, falls back to lw3445, and the report says so.
 *
 * Refuses: an em dash in a written value, a value over the field's limit, an asset whose record or page
 * evidence is missing. Idempotent: matches on the byline, so a second run finds nothing and saves nothing.
 * Writes inventory/review/hart-park-captions-dry-run-2026-10-05.md.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_hart_park_captions_2026_10_05.php'))"
 */
use craft\elements\Asset;
use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;

$root = \Craft::getAlias('@root');
$BYLINE = 'Biography by Friends of Hart Park';
$EXPECTED = [13046, 13083, 13086, 13090, 13104, 13120, 13271, 13302, 13306, 13405, 13535, 13733, 14847];
$evPath = "$root/inventory/review/legacy-evidence-2026-10-05.json";
if (!file_exists($evPath)) { throw new \RuntimeException('run scripts/import/extract_legacy_evidence_2026_10_05.py first'); }
$EV = json_decode(file_get_contents($evPath), true)['hart'] ?? [];

$has = function ($el, string $h): bool { foreach ($el->getFieldLayout()?->getCustomFields() ?? [] as $f) { if ($f->handle === $h) { return true; } } return false; };
$read = function ($el, string $h) use ($has): string { if (!$has($el, $h)) { return ''; } try { return trim((string)$el->getFieldValue($h)); } catch (\Throwable $t) { return ''; } };

/* Every asset carrying the byline in caption, title or alt, not only the thirteen the audit named. */
$ids = Asset::find()->photoCaptionExt($BYLINE)->ids();
foreach (Asset::find()->title($BYLINE)->ids() as $i) { $ids[] = $i; }
$ids = array_values(array_unique(array_map('intval', $ids))); sort($ids);
$extra = array_diff($ids, $EXPECTED); $gone = array_diff($EXPECTED, $ids);

$plan = []; $bad = [];
foreach ($ids as $id) {
  $a = Asset::find()->id($id)->one();
  $code = strtolower($read($a, 'photoSourceCode') ?: pathinfo($a->filename, PATHINFO_FILENAME));
  $rec = null; $via = $code;
  foreach ([$code, preg_replace('~(?<=\d)[a-z]$~', '', $code)] as $k) {
    $rec = Entry::find()->section('photographs')->status(null)->photoSourceCode([$k, strtoupper($k)])->one()
        ?? Entry::find()->section('photographs')->status(null)->legacyKey($k)->one();
    if ($rec) { $via = $k; break; }
  }
  $ev = $EV[$code] ?? null;
  if (!$rec) { $bad[] = "#$id $code: no photograph record"; continue; }
  if (!$ev || isset($ev['error'])) { $bad[] = "#$id $code: no page evidence" . ($ev['error'] ?? ''); continue; }
  $desc = rtrim(trim($rec->title), " .");
  $newCap = $ev['captionUnderImage'] ?? '';
  foreach (['alt' => $desc, 'title' => $desc, 'caption' => $newCap] as $k => $v) {
    if (preg_match('~[\x{2013}\x{2014}]~u', $v)) { $bad[] = "#$id $code: em or en dash in new $k"; continue 2; }
  }
  $f = \Craft::$app->getFields()->getFieldByHandle('photoCaptionExt');
  if ($f->charLimit && mb_strlen($newCap) > $f->charLimit) { $bad[] = "#$id $code: caption over {$f->charLimit}"; continue; }
  if (mb_strlen($desc) > 255) { $bad[] = "#$id $code: title over 255"; continue; }
  $plan[] = ['a' => $a, 'code' => $code, 'rec' => $rec, 'via' => $via, 'ev' => $ev,
    'cur' => ['caption' => $read($a, 'photoCaptionExt'), 'title' => (string)$a->title, 'alt' => (string)$a->alt, 'credit' => $read($a, 'photoCredit')],
    'new' => ['caption' => $newCap, 'title' => $desc, 'alt' => $desc]];
}

foreach ($plan as $p) {
  echo "#{$p['a']->id} {$p['a']->filename}  photograph #{$p['rec']->id} \"{$p['rec']->title}\"" . ($p['via'] !== $p['code'] ? " (via {$p['via']})" : '') . PHP_EOL;
  echo "   caption: " . json_encode($p['cur']['caption']) . " -> " . json_encode($p['new']['caption']) . PHP_EOL;
  echo "   title:   " . json_encode($p['cur']['title']) . " -> " . json_encode($p['new']['title']) . PHP_EOL;
  echo "   alt:     " . json_encode($p['cur']['alt']) . " -> " . json_encode($p['new']['alt']) . PHP_EOL;
}
foreach ($bad as $b) { echo "REFUSED $b" . PHP_EOL; }
if ($extra) { echo 'NOTE: beyond the audit\'s thirteen: #' . implode(', #', $extra) . PHP_EOL; }
if ($gone) { echo 'NOTE: already clear: #' . implode(', #', $gone) . PHP_EOL; }

/* Assets whose credit is the biography's, not the image's: reported only. */
$credits = Asset::find()->photoCredit('*Friends of Hart Park*')->ids();

/* ------------------------------------------------------------------ report */
$o = [];
$o[] = '# Hart Park byline captions, dry run, 5 October 2026';
$o[] = '';
$o[] = 'Generated by `scripts/import/fix_hart_park_captions_2026_10_05.php` (' . ($APPLY ? 'APPLIED' : 'DRY RUN, nothing written') . '). Silent-faults audit finding 4.';
$o[] = '';
$o[] = '## Why the extractor took the line';
$o[] = '';
$o[] = 'The crawl looked for a caption anywhere inside the image\'s enclosing `<td>`. On these pages that cell holds the whole page, and the only element with the class "caption" is the subtitle of the Friends of Hart Park biography of Hart at the foot of the page: `<div class="caption">Biography by Friends of Hart Park</div>` (line given per image below). The credit under that biography became the image\'s credit the same way. `apply_extracted_captions.php` wrote the line as caption, title and alt because it rejected navigation but not bylines. It now rejects bylines and text credits (dated comment in the script).';
$o[] = '';
$o[] = '## What the pages give';
$o[] = '';
$o[] = 'None of the 13 pages has a caption element in the image\'s own block (the image, then "Click image to enlarge", up to the next image, rule, heading or body text). The text under each image is the page\'s body, which the photograph record already prints. So the caption is cleared, and alt and title become the photograph record\'s own title. The first line of body text is shown for reference only; it is not written.';
$o[] = '';
$o[] = "Assets: " . count($plan) . " to change, " . count($bad) . " refused." . ($extra ? ' Beyond the audit\'s list: #' . implode(', #', $extra) . '.' : '') . ($gone ? ' Already clear: #' . implode(', #', $gone) . '.' : '');
$o[] = '';
foreach ($plan as $p) {
  $ev = $p['ev'];
  $o[] = "### Asset #{$p['a']->id} `{$p['a']->filename}`, photograph #{$p['rec']->id}";
  $o[] = '';
  $o[] = "- Photograph record: #{$p['rec']->id} \"{$p['rec']->title}\"" . ($p['via'] !== $p['code'] ? " (matched on `{$p['via']}`: `{$p['code']}` is one of the plates on that page)" : '');
  $o[] = "- Legacy page: `{$ev['page']}`, \"{$ev['title']}\"";
  $o[] = "- Image at line {$ev['imageLine']}; the byline the crawl took at line {$ev['bylineLine']}" . (!empty($ev['bylineAfterAnotherImage']) ? ' (after another image)' : '');
  $o[] = "- Caption element in the image's own block: " . ($ev['captionUnderImage'] !== '' ? "line {$ev['captionLine']}: \"{$ev['captionUnderImage']}\"" : 'none');
  $o[] = "- First body line under the image (reference, not written): " . ($ev['firstParagraphUnderImage'] !== '' ? "line {$ev['firstParagraphLine']}: \"" . mb_substr($ev['firstParagraphUnderImage'], 0, 160) . (mb_strlen($ev['firstParagraphUnderImage']) > 160 ? '...' : '') . '"' : 'not extracted');
  $o[] = '';
  $o[] = '| Field | Current | New |';
  $o[] = '|---|---|---|';
  foreach (['caption' => 'photoCaptionExt', 'title' => 'title', 'alt' => 'alt'] as $k => $label) {
    $o[] = "| $label | " . ($p['cur'][$k] === '' ? '(empty)' : $p['cur'][$k]) . ' | ' . ($p['new'][$k] === '' ? '(empty)' : $p['new'][$k]) . ' |';
  }
  $o[] = '';
}
if ($bad) { $o[] = '## Refused'; $o[] = ''; foreach ($bad as $b) { $o[] = "- $b"; } $o[] = ''; }
$o[] = '## For Nathan: the biography credit on ' . count($credits) . ' assets';
$o[] = '';
$o[] = 'photoCredit reads "' . ($credits ? $read(Asset::find()->id($credits[0])->one(), 'photoCredit') : '') . '" on ' . count($credits) . ' assets, the 13 above among them. It credits the biography text on the page, not the image. This script does not touch it. Asset ids: #' . implode(', #', $credits) . '.';
$o[] = '';
$o[] = 'Idempotent: the script matches on the byline, so a second run finds nothing.';
file_put_contents("$root/inventory/review/hart-park-captions-dry-run-2026-10-05.md", implode("\n", $o) . "\n");
echo 'wrote inventory/review/hart-park-captions-dry-run-2026-10-05.md' . PHP_EOL;

if (!$APPLY || $bad) { echo ($bad ? 'refusals above: nothing written' : 'nothing written') . PHP_EOL; return; }
$el = \Craft::$app->getElements(); $n = 0; $short = [];
foreach ($plan as $p) {
  $a = $p['a'];
  $a->setFieldValue('photoCaptionExt', $p['new']['caption'] === '' ? null : $p['new']['caption']);
  $a->title = $p['new']['title']; $a->alt = $p['new']['alt'];
  if (!$el->saveElement($a)) { throw new \RuntimeException("#{$a->id} " . json_encode($a->getFirstErrors())); }
  $n++;
  $r = Asset::find()->id($a->id)->one();
  if (trim((string)$r->getFieldValue('photoCaptionExt')) !== $p['new']['caption'] || $r->title !== $p['new']['title'] || $r->alt !== $p['new']['alt']) { $short[] = "#{$a->id}"; }
}
if ($short) { echo 'READ-BACK FAILED: ' . implode(', ', $short) . PHP_EOL; return; }
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('fix_hart_park_captions_2026_10_05.php', $n, "verified $n of $n", 'Hart Park byline cleared from caption, title, alt');
echo "done: $n" . PHP_EOL;
