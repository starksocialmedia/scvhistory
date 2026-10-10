/**
 * Two obituaries John Boston wrote for The Signal, from the original site on Reggie, as records in the obituaries section
 * (Nathan, 9 October 2026: "Boston: yes to Haskell and Harris as articles, both carry his byline."). He said "articles"; the
 * archive's type for an obituary is the obituaries section (DATA-ORGANIZATION.md: an obituary is its own entry type, and
 * "an obituary credits the newspaper that ran it"), which is why they go there and not to articles.
 *
 *  - /scvhistory/obituary_frederickbaileyhaskell.htm: the Signal piece headed "Watching a Lifetime of Change.", printed
 *    "By John Boston, Editor." over "The Signal | Tuesday, January 25, 2005.". Leon Worden's page heading above it
 *    ("Frederick Bailey Haskell" / "Scion of Pioneering Saugus Family." / life dates) and his audio box on the 1988 oral
 *    history interview are page furniture, not the piece; they are listed, not imported.
 *  - /scvhistory/sg010906.htm: "Educator George Harris Dies at 95", printed "By John Boston and Kristopher Daams | Signal
 *    Staff Writers | Monday, January 9, 2006", credit "(c)2006 THE SIGNAL • USED BY PERMISSION • RIGHTS RESERVED.".
 *
 * Each page is read from Reggie (/mnt/reggie inside the web container) and the byline is checked on the bytes; a page whose
 * byline is not the one expected is refused. Body: the printed paragraphs, verbatim (entities decoded, the page's declared
 * charset honoured, typos and "[sic]" kept), separated by blank lines. Title: the printed headline. writtenBy John Boston
 * #2576 on both, basis printed-byline. Kristopher Daams has no person record and none is made; Haskell and Harris have none
 * either, so obitSubject stays empty. obitPublishedIn: The Santa Clarita Valley Signal #376 (organizations). Pictures and
 * sidebar boxes are other records' and are listed, not imported. Matches the field use of the existing obituaries (#28051,
 * #28053): publicationDetails as writer, paper, date, credit; obitDatePublished and obitDateOfDeath as printed dates.
 * Idempotent: an obituary is found by legacyUrl; a record that exists is reported and only empty fields would be filled.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/boston_obituaries_2026_10_09.php'))"
 */
use craft\elements\{Entry, Asset};

$APPLY = false;

$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $fields = Craft::$app->getFields(); $n = 0; $bad = [];
$R = '/mnt/reggie/scvhistory.com/scvhistory';
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['file', 'the Haskell obituary page on Reggie', "$R/obituary_frederickbaileyhaskell.htm"],
  ['file', 'the George Harris obituary page on Reggie', "$R/sg010906.htm"],
  ['record', 'Craft entries matched by legacyUrl, and persons/organizations by title', 'the two pages', 'read'],
  ['record', 'Craft asset filename (sg010906-harris*, sg20050125haskell*)', 'the pictures on the pages', 'not read: the pictures are not imported here; the script only asks whether Craft already holds them by name'],
]);
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$hasF = function ($e): array { $h = []; foreach ($e->getFieldLayout()->getCustomFields() as $f) { $h[$f->handle] = true; } return $h; };
$txt = function (string $html): string {
  $t = preg_replace('~<br\s*/?>~i', ' ', $html); $t = strip_tags($t);
  $t = html_entity_decode($t, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  return trim(preg_replace('~\s+~u', ' ', $t));
};
$paras = function (string $html) use ($txt): array {
  return array_values(array_filter(array_map($txt, preg_split('~<p\b[^>]*>~i', $html)), fn($p) => $p !== ''));
};

/* ---------- Haskell ---------- */
$hRaw = file_get_contents("$R/obituary_frederickbaileyhaskell.htm");
$H = ['file' => 'obituary_frederickbaileyhaskell.htm', 'sha' => hash('sha256', $hRaw)];
if (!preg_match('~<div class="byline">\s*<p[^>]*>\s*(By John Boston, Editor\.)\s*</div>~', $hRaw, $m)) { $bad[] = 'Haskell: byline "By John Boston, Editor." not on the page'; }
$H['byline'] = $m[1] ?? '';
if (!preg_match('~<div class="dateline">\s*<p[^>]*>\s*(The Signal \| Tuesday, January 25, 2005\.)\s*</div>~', $hRaw, $m)) { $bad[] = 'Haskell: dateline not on the page'; }
$H['dateline'] = $m[1] ?? '';
preg_match('~<div class="altheadline">\s*<p[^>]*>\s*(.*?)\s*</div>\s*<div class="subhed2">\s*<p[^>]*>\s*(.*?)\s*</div>\s*<div class="byline">~s', $hRaw, $m);
$H['headline'] = rtrim($txt($m[1] ?? ''), '.'); $H['deck'] = $txt($m[2] ?? '');
$start = strpos($hRaw, $H['dateline']); $start = strpos($hRaw, '</div>', $start) + 6; $end = strpos($hRaw, '<p><hr><p>', $start);
$H['paragraphs'] = ($start && $end) ? $paras(substr($hRaw, $start, $end - $start)) : [];
preg_match('~<div class="altheadline"><h1>(.*?)</h1></div>\s*<div class="subhed2"><h2>(.*?)</h2></div>\s*<div class="byline">(.*?)</div>~s', $hRaw, $m);
$H['pageHeading'] = $txt($m[1] ?? '') . ' / ' . $txt($m[2] ?? '') . ' / ' . $txt($m[3] ?? '');
preg_match('~<div class="bodysansbold"[^>]*>(.*?)</div>~s', $hRaw, $m); $H['audioNote'] = $txt($m[1] ?? '');
$H += ['legacyKey' => 'obituary_frederickbaileyhaskell', 'published' => 'January 25, 2005', 'death' => 'January 22, 2005',
  'pub' => 'John Boston, Editor, The Signal, Tuesday, January 25, 2005',
  'note' => 'Printed byline "By John Boston, Editor." over the dateline "The Signal | Tuesday, January 25, 2005." Read from the original page.',
  'notCarried' => [
    'Signal deck under the headline (no subheadline field on the obituary type): "' . '%DECK%' . '"',
    'Leon Worden\'s page heading: "%HEAD%"',
    'Audio box, "Audio Interview, 1988." (three players for scvtv.com/vid/baileyhaskell_oralhistory19880508.mp3, _orig.aiff, _orig.mp3, captions "Hear edited MP3", "Download original .aiff (lossless)", "Hear original MP3 (lossy)") and Leon\'s note under it: "%AUDIO%"',
    'Picture below the text: gif/sg20050125haskell.jpg, enlargement gif/sg20050125haskell_large.jpg, caption "Click image to enlarge." (no credit printed)',
    'Sidebar "JOHN C. HASKELL / HASKELL RANCH": ap2030.htm "Homestead 1894"; ed_laherald070309.htm "John C. Haskell Death 7-2-1909"; sg19570822taylor.htm "Obituary: Bertha Haskell Taylor 1957"; ap2031.htm "Cabin 1963"; self-link "Bailey Haskell Oral History (1988) & Obituary (2005)"',
  ],
  'images' => ['sg20050125haskell*'], 'sidebar' => ['ap2030', 'ed_laherald070309', 'sg19570822taylor', 'ap2031']];
foreach ($H['notCarried'] as $i => $s) { $H['notCarried'][$i] = str_replace(['%DECK%', '%HEAD%', '%AUDIO%'], [$H['deck'], $H['pageHeading'], $H['audioNote']], $s); }

/* ---------- Harris ---------- */
$gRaw = file_get_contents("$R/sg010906.htm");
$G = ['file' => 'sg010906.htm', 'sha' => hash('sha256', $gRaw)];
if (!preg_match('~charset=windows-1252~i', $gRaw)) { $bad[] = 'Harris: declared charset is not windows-1252'; }
$gRaw = mb_convert_encoding($gRaw, 'UTF-8', 'Windows-1252');
if (!preg_match('~<font class="dateline">(By John Boston and Kristopher Daams \| Signal Staff Writers \| Monday, January 9, 2006)</font>~', $gRaw, $m)) { $bad[] = 'Harris: byline not on the page'; }
$G['byline'] = $m[1] ?? '';
preg_match('~<font class="altheadline">(.*?)</font><br>\s*<font class="subhed2">(.*?)</font>~s', $gRaw, $m);
$G['headline'] = $txt($m[1] ?? ''); $G['deck'] = $txt($m[2] ?? '');
preg_match('~<div class="bodyserif">(.*?)</div>~s', $gRaw, $m);
$G['paragraphs'] = $paras(preg_replace('~<img\b[^>]*>~i', '', $m[1] ?? ''));
preg_match('~<font class="credit">(.*?)</font>~s', $gRaw, $m); $G['credit'] = $txt($m[1] ?? '');
$G += ['legacyKey' => 'sg010906', 'published' => 'January 9, 2006', 'death' => 'January 8, 2006',
  'pub' => 'John Boston and Kristopher Daams, Signal Staff Writers, The Signal, Monday, January 9, 2006. ' . $G['credit'],
  'note' => 'Printed byline "' . $G['byline'] . '" under the headline. The byline is shared with Kristopher Daams, who has no person record here; only John Boston is linked. The obituaries index credits it "by John Boston and Kristopher Daams, 1-9-2006". Read from the original page.',
  'notCarried' => [
    'Signal deck under the headline (no subheadline field on the obituary type): "' . $G['deck'] . '"',
    'Picture in the text, right: gif/sg010906-harris.jpg, alt "George Harris", no caption or credit printed',
  ],
  'images' => ['sg010906-harris*'], 'sidebar' => []];

/* ---------- Plan ---------- */
$boston = Entry::find()->id(2576)->section('persons')->status(null)->one();
$signal = Entry::find()->id(376)->section('organizations')->status(null)->one();
if ($boston?->title !== 'John Boston') { $bad[] = '#2576 is not John Boston'; }
if ($signal?->title !== 'The Santa Clarita Valley Signal') { $bad[] = '#376 is not The Santa Clarita Valley Signal'; }
$sec = Craft::$app->getEntries()->getSectionByHandle('obituaries'); $type = Craft::$app->getEntries()->getEntryTypeByHandle('obituary');
$typeHas = []; foreach ($type->getFieldLayout()->getCustomFields() as $f) { $typeHas[$f->handle] = $f; }
$opts = array_column($fields->getFieldByHandle('authorshipBasis')->options, 'value');
if (!in_array('printed-byline', $opts, true)) { $bad[] = 'authorshipBasis has no printed-byline option'; }
$persons = [];
foreach (['Haskell', 'Harris', 'Daams'] as $s) { foreach (Entry::find()->section('persons')->status(null)->title("*$s*")->all() as $p) { $persons[$s][] = "#{$p->id} {$p->title}"; } }

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
foreach ([$H, $G] as $o) {
  $url = "/scvhistory/{$o['file']}";
  $V = ['body' => implode("\n\n", $o['paragraphs']), 'writtenBy' => [2576], 'authorshipBasis' => 'printed-byline', 'authorshipBasisNote' => $o['note'],
    'publicationDetails' => $o['pub'], 'obitDatePublished' => $o['published'], 'obitDateOfDeath' => $o['death'], 'obitPublishedIn' => [376],
    'obitLegacyUrl' => $url, 'legacyUrl' => $url, 'legacyKey' => $o['legacyKey'], 'sourcePath' => "https://scvhistory.com$url"];
  foreach ($V as $h => $v) {
    if (!isset($typeHas[$h])) { $bad[] = "{$o['file']}: $h is not on the obituary layout"; continue; }
    $f = $typeHas[$h];
    if ($f instanceof \craft\fields\PlainText && $f->charLimit && mb_strlen($v) > $f->charLimit) { $bad[] = "{$o['file']} $h: " . mb_strlen($v) . " > {$f->charLimit}"; }
  }
  foreach (['authorshipBasisNote', 'publicationDetails'] as $h) { if (preg_match('~\x{2014}~u', $V[$h])) { $bad[] = "{$o['file']} $h: em dash"; } }
  if (count($o['paragraphs']) < 10) { $bad[] = "{$o['file']}: only " . count($o['paragraphs']) . ' paragraphs parsed'; }
  $ex = Entry::find()->section('obituaries')->status(null)->legacyUrl($url)->one();
  $ex = $ex ?: Entry::find()->status(null)->legacyUrl($url)->one();
  if ($ex && $ex->section->handle !== 'obituaries') { $bad[] = "$url is already #{$ex->id} in {$ex->section->handle}"; }
  $set = $V;
  if ($ex) { $exHas = $hasF($ex); foreach ($V as $h => $v) { if (!isset($exHas[$h])) { continue; } $cur = $ex->getFieldValue($h); $cur = $cur instanceof \craft\elements\db\ElementQuery ? $cur->status(null)->ids() : (string)$cur; if ($cur !== '' && $cur !== []) { unset($set[$h]); } } }
  echo PHP_EOL . ($ex ? "#{$ex->id} EXISTS, " . ($set ? 'would fill empty: ' . implode(', ', array_keys($set)) : 'nothing to set') : 'WOULD CREATE') . " obituary \"{$o['headline']}\"" . PHP_EOL;
  echo "  page: $url (sha256 " . substr($o['sha'], 0, 16) . '...)' . PHP_EOL;
  echo "  byline on the page: \"{$o['byline']}\"" . (isset($o['dateline']) ? " / dateline \"{$o['dateline']}\"" : '') . PHP_EOL;
  foreach ($V as $h => $v) { if ($h !== 'body') { echo "  $h: " . (is_array($v) ? json_encode($v) . ($h === 'writtenBy' ? ' John Boston' : ' The Santa Clarita Valley Signal') : $v) . PHP_EOL; } }
  echo '  obitSubject: empty (no person record for the subject; none created)' . PHP_EOL;
  echo '  body: ' . count($o['paragraphs']) . ' paragraphs, ' . mb_strlen($V['body']) . ' characters' . PHP_EOL;
  foreach ($o['paragraphs'] as $i => $p) { echo '    [' . ($i + 1) . "] $p" . PHP_EOL; }
  echo '  not imported (other records, or no field):' . PHP_EOL;
  foreach ($o['notCarried'] as $s) { echo "    - $s" . PHP_EOL; }
  foreach ($o['images'] as $img) { $a = Asset::find()->filename($img)->all(); echo "    - Craft assets named $img: " . ($a ? implode(', ', array_map(fn($x) => "#{$x->id} {$x->filename}", $a)) : 'none') . PHP_EOL; }
  foreach ($o['sidebar'] as $k) { $s = Entry::find()->status(null)->legacyUrl("/scvhistory/$k.htm")->one(); echo "    - sidebar $k.htm in Craft: " . ($s ? "#{$s->id} {$s->title} [{$s->section->handle}]" : 'none') . PHP_EOL; }
  if (!$APPLY || $bad) { continue; }
  if (!$ex) { $ex = new Entry(); $ex->sectionId = $sec->id; $ex->setTypeId($type->id); $ex->title = $o['headline']; }
  if (!$set) { continue; }
  $ex->setFieldValues($set);
  if (!$el->saveElement($ex)) { throw new \RuntimeException("{$o['file']}: " . json_encode($ex->getFirstErrors())); } $n++;
}
echo PHP_EOL . 'Person records: ' . json_encode($persons ?: 'none for Haskell, Harris or Daams') . '; none created, nothing invented.' . PHP_EOL;
echo 'Refused: ' . ($bad ? PHP_EOL . '  - ' . implode(PHP_EOL . '  - ', $bad) : 'none') . PHP_EOL;
if ($APPLY && !$bad && $n) {
  $short = [];
  foreach ([$H, $G] as $o) { $r = Entry::find()->section('obituaries')->status(null)->legacyUrl("/scvhistory/{$o['file']}")->one(); if (!$r || $r->getFieldValue('writtenBy')->status(null)->ids() !== [2576] || trim((string)$r->getFieldValue('body')) === '') { $short[] = $o['file']; } }
  $applyLog = require "$root/scripts/import/_apply_log.php";
  $applyLog('boston_obituaries_2026_10_09.php', $n, $short ? 'SHORT' : 'verified', 'Haskell and George Harris obituaries by John Boston, The Signal, in obituaries');
  if ($short) { throw new \RuntimeException('read-back short: ' . implode(', ', $short)); }
}
echo ($APPLY && !$bad ? "done: $n saved" : 'Nothing was written.') . ' A second run after an apply is a no-op.' . PHP_EOL;
