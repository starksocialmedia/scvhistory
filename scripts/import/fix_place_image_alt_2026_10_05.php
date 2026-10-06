/**
 * Silent-faults audit finding 5 (inventory/review/silent-faults-audit-2026-10-05.md). attach_place_images.php:51 gave
 * every Wikimedia Commons place image the alt text "Public domain. Wikimedia Commons.": a rights line where a
 * description belongs, read aloud to a screen-reader user in place of the picture. The rights line itself was
 * written nowhere else: license, source, sourceUrl, provenanceKind and photoCredit are all empty on the six.
 *
 * WHAT IS WRITTEN, per asset:
 *   alt             the asset's own title (the caption attach_place_images.php gave it), first sentence, closing
 *                   period dropped. Where that is only the place's name ("Lake Hughes."), "Photograph of" is put in
 *                   front; both of those images (Lake Hughes, Rancho Camulos) were looked at and are photographs.
 *                   Nothing else is added.
 *   license         public-domain, only where empty: attach_place_images.php records each file as public domain,
 *                   checked by hand.
 *   provenanceKind  outside, only where empty.
 *   sourceUrl       the Commons file page of the file named in attach_place_images.php's $picks, only where empty.
 *   source          "Wikimedia Commons, File:<name>. Public domain. The stored copy was requested from Commons at
 *                   1,600 pixels wide.", only where empty (the script fetched ?width=1600; the URL is in sourceUrl).
 *   photoCredit     "Wikimedia Commons. Public domain.", only where empty: the line that sat in alt, moved to the
 *                   credit field.
 * The Commons file names are read from attach_place_images.php itself, not retyped.
 *
 * Refuses: an em dash, a value over its field's limit, a licence or provenance option the field does not hold, an
 * asset whose file is not in $picks. Idempotent: matches on the bad alt text; a second run finds nothing.
 * Writes inventory/review/place-image-alt-dry-run-2026-10-05.md.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_place_image_alt_2026_10_05.php'))"
 */
use craft\elements\Asset;
use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;

$root = \Craft::getAlias('@root');
$BAD_ALT = 'Public domain. Wikimedia Commons.';
$CREDIT = 'Wikimedia Commons. Public domain.';

/* The Commons file per slug, read from the script that fetched them. */
$src = file_get_contents("$root/scripts/import/attach_place_images.php");
preg_match_all("~^\s*'([a-z0-9-]+)' => \['((?:[^'\\\\]|\\\\.)*)', '((?:[^'\\\\]|\\\\.)*)'\],~m", $src, $m, PREG_SET_ORDER);
$PICKS = []; foreach ($m as $r) { $PICKS[$r[1]] = stripslashes($r[2]); }
if (count($PICKS) !== 6) { throw new \RuntimeException('expected 6 picks in attach_place_images.php, read ' . count($PICKS)); }

$fs = \Craft::$app->getFields();
$opts = fn($h) => array_map(fn($o) => $o['value'], $fs->getFieldByHandle($h)->options);
if (!in_array('public-domain', $opts('license'), true)) { throw new \RuntimeException('license has no public-domain option'); }
if (!in_array('outside', $opts('provenanceKind'), true)) { throw new \RuntimeException('provenanceKind has no outside option'); }
$has = function ($el, string $h): bool { foreach ($el->getFieldLayout()?->getCustomFields() ?? [] as $f) { if ($f->handle === $h) { return true; } } return false; };
$readS = function ($el, string $h) use ($has): string {
  if (!$has($el, $h)) { return ''; }
  try { $v = $el->getFieldValue($h); } catch (\Throwable $t) { return ''; }
  if ($v instanceof \craft\fields\data\SingleOptionFieldData) { return (string)$v->value; }
  if ($v instanceof \craft\fields\data\LinkData) { return (string)$v->getValue(); }
  return trim((string)$v);
};

/* Every asset with the licence line as alt, not only the six the audit named. */
$ids = []; foreach (Asset::find()->limit(null)->all() as $x) { if ((string)$x->alt === $BAD_ALT) { $ids[] = (int)$x->id; } }
sort($ids);

$plan = []; $bad = [];
foreach ($ids as $id) {
  $a = Asset::find()->id($id)->one();
  if (!$a || (string)$a->alt !== $BAD_ALT) { continue; }
  $slug = pathinfo($a->filename, PATHINFO_FILENAME);
  $file = $PICKS[$slug] ?? null;
  if ($file === null) { $bad[] = "#$id {$a->filename}: not in attach_place_images.php"; continue; }
  $place = Entry::find()->section('places')->status(null)->relatedTo(['targetElement' => $a, 'field' => 'featuredImage'])->one();
  $first = preg_split('~(?<=\.)\s+~u', trim((string)$a->title))[0];
  $desc = rtrim($first, ' .');
  $bare = $place ? mb_strtolower($desc) === mb_strtolower(rtrim($place->title, ' .')) : (mb_strtolower($desc) === mb_strtolower(str_replace('-', ' ', $slug)));
  $alt = $bare ? "Photograph of $desc" : $desc;
  $url = 'https://commons.wikimedia.org/wiki/File:' . rawurlencode(str_replace(' ', '_', $file));
  $want = [
    'license' => 'public-domain',
    'provenanceKind' => 'outside',
    'sourceUrl' => $url,
    'source' => "Wikimedia Commons, File:$file. Public domain. The stored copy was requested from Commons at 1,600 pixels wide.",
    'photoCredit' => $CREDIT,
  ];
  $set = []; $kept = [];
  foreach ($want as $h => $v) {
    if (!$has($a, $h)) { $bad[] = "#$id: no field $h"; continue 2; }
    $cur = $readS($a, $h);
    if ($cur === '') { $set[$h] = $v; } else { $kept[$h] = $cur; }
  }
  foreach (array_merge(['alt' => $alt], $set) as $h => $v) {
    if (preg_match('~[\x{2013}\x{2014}]~u', $v)) { $bad[] = "#$id: em or en dash in $h"; continue 2; }
    $f = $h === 'alt' ? null : $fs->getFieldByHandle($h);
    if ($f instanceof \craft\fields\PlainText && $f->charLimit && mb_strlen($v) > $f->charLimit) { $bad[] = "#$id: $h is " . mb_strlen($v) . " characters, limit {$f->charLimit}"; continue 2; }
  }
  $plan[] = compact('a', 'place', 'alt', 'set', 'kept', 'file');
}

foreach ($plan as $p) {
  echo "#{$p['a']->id} {$p['a']->filename}  " . ($p['place'] ? "place #{$p['place']->id} {$p['place']->title}" : 'no place uses it') . PHP_EOL;
  echo "   alt: " . json_encode($BAD_ALT) . ' -> ' . json_encode($p['alt']) . PHP_EOL;
  foreach ($p['set'] as $h => $v) { echo "   $h: (empty) -> " . json_encode($v) . PHP_EOL; }
  foreach ($p['kept'] as $h => $v) { echo "   $h: kept " . json_encode($v) . PHP_EOL; }
}
foreach ($bad as $b) { echo "REFUSED $b" . PHP_EOL; }

$o = [];
$o[] = '# Place image alt text, dry run, 5 October 2026';
$o[] = '';
$o[] = 'Generated by `scripts/import/fix_place_image_alt_2026_10_05.php` (' . ($APPLY ? 'APPLIED' : 'DRY RUN, nothing written') . '). Silent-faults audit finding 5.';
$o[] = '';
$o[] = 'Alt text becomes the asset\'s own title (the caption `attach_place_images.php` gave it), first sentence, closing period dropped; "Photograph of" is put in front only where the title is just the place name. The rights line moves to the rights and credit fields, which were all empty. Only empty fields are filled.';
$o[] = '';
$o[] = 'Assets: ' . count($plan) . ' to change, ' . count($bad) . ' refused.';
$o[] = '';
$o[] = '| Asset | Used by | Current alt | New alt |';
$o[] = '|---|---|---|---|';
foreach ($plan as $p) { $o[] = "| #{$p['a']->id} `{$p['a']->filename}` | " . ($p['place'] ? "#{$p['place']->id} {$p['place']->title}" : 'no place (featuredImage of nothing)') . " | $BAD_ALT | {$p['alt']} |"; }
$o[] = '';
$o[] = '## Rights and credit fields';
$o[] = '';
foreach ($plan as $p) {
  $o[] = "### #{$p['a']->id} `{$p['a']->filename}` (Commons: `{$p['file']}`)";
  $o[] = '';
  foreach ($p['set'] as $h => $v) { $o[] = "- $h: (empty) -> $v"; }
  foreach ($p['kept'] as $h => $v) { $o[] = "- $h: kept, already \"$v\""; }
  $o[] = '';
}
if ($bad) { $o[] = '## Refused'; $o[] = ''; foreach ($bad as $b) { $o[] = "- $b"; } $o[] = ''; }
$o[] = '## Notes';
$o[] = '';
$o[] = '- The place page shows only the image and its alt text; the licence and credit appear on the image\'s /media page.';
$o[] = '- Public domain rests on the hand check recorded in `attach_place_images.php`; this script does not re-verify it against Commons.';
$o[] = '- `attach_place_images.php:51` and the same shape at `find_place_images.php:82` now write a description as alt and the licence to license, source and photoCredit (dated comments in both).';
$o[] = '';
$o[] = 'Idempotent: the script matches on the bad alt text, so a second run finds nothing.';
file_put_contents("$root/inventory/review/place-image-alt-dry-run-2026-10-05.md", implode("\n", $o) . "\n");
echo 'wrote inventory/review/place-image-alt-dry-run-2026-10-05.md' . PHP_EOL;

if (!$APPLY || $bad) { echo ($bad ? 'refusals above: nothing written' : 'nothing written') . PHP_EOL; return; }
$el = \Craft::$app->getElements(); $n = 0; $short = [];
foreach ($plan as $p) {
  $a = $p['a']; $vals = $p['set'];
  if (isset($vals['sourceUrl'])) { $vals['sourceUrl'] = ['type' => 'url', 'value' => $vals['sourceUrl']]; }
  $a->alt = $p['alt']; $a->setFieldValues($vals);
  if (!$el->saveElement($a)) { throw new \RuntimeException("#{$a->id} " . json_encode($a->getFirstErrors())); }
  $n++;
  $r = Asset::find()->id($a->id)->one();
  if ((string)$r->alt !== $p['alt']) { $short[] = "#{$a->id} alt"; }
  foreach ($p['set'] as $h => $v) { if ($readS($r, $h) !== $v) { $short[] = "#{$a->id} $h"; } }
}
if ($short) { echo 'READ-BACK FAILED: ' . implode(', ', $short) . PHP_EOL; return; }
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('fix_place_image_alt_2026_10_05.php', $n, "verified $n of $n", 'place image alt text from title; rights line to license, source, credit');
echo "done: $n" . PHP_EOL;
