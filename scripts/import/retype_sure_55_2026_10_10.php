/**
 * The retype of the 55 the type audit found sure to be articles (inventory/review/overnight-2026-10-08/type-audit-2026-10-08.md:
 * 19 photographs holding a page scan, 12 born-digital or newsletter pieces on photograph pages, 24 documents), under Nathan's
 * rule of 7 October: "a scanned newspaper page is an article that survives as an image. The scan is how we hold it, not what
 * it is. Type follows the thing."
 *
 * DRY RUN ONLY. For each record it prints: section and type from and to; every field with a value and whether the article
 * type carries it, maps it to its own field, or has no home for it; what would be filled (heldAs, sourceLine, the publish
 * date, originallyPublishedTitle, writtenBy with its basis, publishedBy), only into empty fields; the title against the
 * printed headline; the scan files and the role each would take (facsimile); the links that point at the record; the old
 * address and the new one. Then the approval batches with their counts. Report in
 * inventory/review/retype-sure-55-dry-run-2026-10-10.md.
 *
 * How a move is done follows merge_and_move_2026_10_07.php (#2689 and #28295, Nathan's "yes to the move"): same id, so
 * every link survives; old address to config/redirects.php. Where that script added the photograph-only fields to the
 * article type ("Photograph details"), this plan maps the photograph relations onto the article's own relation fields,
 * as the 7 October retype read proposed (photoPlaces to depictsPlace and the rest), and documentFiles onto
 * recordDocuments, which the article page prints.
 *
 * Not touched, and not in any batch (the brief's list): the 22 enhanced portrait pairs, the Hart and COC drafts, the
 * fallen officers, the date decisions, the disaster comparison, Boston's profile, #2913, #5377. The script checks that no
 * record or scan file in the plan is one of those.
 *
 * $APPLY stays false. The apply half is not written: it is written after Nathan approves batches, and tested on a fixture
 * first. Setting $APPLY = true stops the script.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/retype_sure_55_2026_10_10.php'))"
 */
use craft\elements\Entry;
use craft\elements\Asset;
$APPLY = false;
$root = \Craft::getAlias('@root'); $db = Craft::$app->db; $fs = Craft::$app->getFields(); $es = Craft::$app->getEntries();
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['file', 'the type audit, for the 55 and what each scan prints', "$root/inventory/review/overnight-2026-10-08/type-audit-2026-10-08.md"],
  ['file', 'the redirect map, for addresses already redirected', "$root/config/redirects.php"],
  ['record', 'Craft entries: the 55, their field values, the links that point at them, the person and organization records named in bylines', 'the legacy pages and the scans', 'not read: the type audit opened every scan and page for these 55 (OCR and by eye, 8 October); this dry run carries what it found, quoted in the plan below'],
  ['record', 'Craft asset fields assetRole and filename on the scans these records hold', 'the scan files', 'not read: the role is about what a file is a copy of, which the type audit read from the files; nothing here depends on the bytes'],
]);
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; throw new \RuntimeException('dry run only: the apply half is written after Nathan approves batches'); }
echo 'DRY RUN (nothing is written)' . PHP_EOL . PHP_EOL;

/* The plan. kind: what the source is. held: heldAs. sl: sourceLine as printed (byline, publication | date). opd: date as
 * printed; edtf: only a plain year, year-month or day (a season or a two-month issue is left empty, not invented). opt:
 * originallyPublishedTitle, the printed head (null: keep the record's title as it is printed; false: no head printed).
 * w: writtenBy person id (only a printed byline naming someone with a record). pub: publishedBy ids (only a publisher
 * printed on the held page or the born-digital page itself, with a record). fax: the scans are copies of the record
 * (assetRole facsimile). where: where publication and date were read. */
$LEON = 279; $SCVH = 378; $HERALD = 390; $SIGNAL = 376;
$P = [
  /* Leon Worden's born-digital and newsletter pieces: the legacy page is the thing. */
  2691 => ['web', 'web', 'By Leon Worden. SCVHistory.com | Wednesday, October 6, 2004.', 'Wednesday, October 6, 2004', '2004-10-06', null, $LEON, [$SCVH], false, 'page'],
  2721 => ['web', 'web', 'By Leon Worden. Heritage Junction Dispatch | January-February 2020.', 'January-February 2020', '', null, $LEON, [], false, 'page'],
  4441 => ['web', 'web', 'By Leon Worden. | January 3, 2015.', 'January 3, 2015', '2015-01-03', null, $LEON, [], false, 'page'],
  4689 => ['web', 'web', 'By Leon Worden. Principal research and documentation by Tricia Lemon Putnam. SCVHistory.com, March 2017.', 'March 2017', '2017-03', null, $LEON, [$SCVH], false, 'page'],
  4931 => ['web', 'web', 'By Leon Worden. SCVHistory.com | Sunday, September 17, 2017.', 'Sunday, September 17, 2017', '2017-09-17', null, $LEON, [$SCVH], false, 'page'],
  5071 => ['web', 'web', 'By Leon Worden. | Wednesday, April 18, 2018.', 'Wednesday, April 18, 2018', '2018-04-18', null, $LEON, [], false, 'page'],
  5235 => ['web', 'web', 'By Leon Worden. Photos by Stan Walker and Leon Worden, Video by Jessica Boyer. SCVHistory.com | Thursday, August 30, 2018.', 'Thursday, August 30, 2018', '2018-08-30', null, $LEON, [$SCVH], false, 'page'],
  5399 => ['web', 'web', 'By Leon Worden. SCVHistory.com | June 2, 2019.', 'June 2, 2019', '2019-06-02', null, $LEON, [$SCVH], false, 'page'],
  5589 => ['web', 'web', 'By Leon Worden. Discovery 1950 | SCVHistory.com, May 3, 2020.', 'May 3, 2020', '2020-05-03', null, $LEON, [$SCVH], false, 'page'],
  5605 => ['web', 'web', 'By Leon Worden. SCVHistory.com | October 13, 2020.', 'October 13, 2020', '2020-10-13', null, $LEON, [$SCVH], false, 'page'],
  5767 => ['web', 'web', 'By Leon Worden. | February 1997. (c)1997 SCVHistory.com', 'February 1997', '1997-02', null, $LEON, [$SCVH], false, 'page'],
  2689 => ['web', 'web', null, null, null, null, null, [], false, 'page'],
  /* Magazine and newsletter pages, the publication and date printed on the pages Craft holds. */
  4783 => ['magazine', 'magazine-pages', 'By Carl W. Breihan. Golden West | 1967.', '1967', '1967', null, null, [], true, 'scan'],
  4919 => ['magazine', 'magazine-pages', 'By Bob Simmons. California Journal | April 1976.', 'April 1976', '1976-04', null, null, [], true, 'scan'],
  4967 => ['magazine', 'magazine-pages', 'Santa Clarita Valley Magazine | Winter 1987-88.', 'Winter 1987-88', '', null, null, [], true, 'scan'],
  5073 => ['magazine', 'magazine-pages', 'By Bruce Kelly. Pacific News, No. 249 | April 1984.', 'April 1984', '1984-04', null, null, [], true, 'scan'],
  5087 => ['magazine', 'magazine-pages', 'By J.K. Parrish. Illustrated by Al Martin Napoletano. Old West | Fall 1969.', 'Fall 1969', '', null, null, [], true, 'scan'],
  5137 => ['magazine', 'magazine-pages', 'By Larry Warren. Stock Car Racing | July 1981.', 'July 1981', '1981-07', null, null, [], true, 'scan'],
  5145 => ['magazine', 'magazine-pages', 'Text and photography by Randy Keller. Pacific Rail News, Issue 336 | November 1991.', 'November 1991', '1991-11', null, null, [], true, 'scan'],
  5205 => ['magazine', 'magazine-pages', 'By Arnold Marquis. Frontier Times | June-July 1967.', 'June-July 1967', '', null, null, [], true, 'scan'],
  5233 => ['magazine', 'magazine-pages', 'By Waddell F. Smith. True West | July-August 1966.', 'July-August 1966', '', null, null, [], true, 'scan'],
  5361 => ['magazine', 'magazine-pages', 'By Frank J. Taylor. The Saturday Evening Post | October 13, 1951.', 'October 13, 1951', '1951-10-13', null, null, [], true, 'scan'],
  5383 => ['magazine', 'magazine-pages', 'By the Staff of Dirt Bike. Dirt Bike | June 1973.', 'June 1973', '1973-06', null, null, [], true, 'scan'],
  5385 => ['magazine', 'magazine-pages', 'By Andy Sperandeo. Model Railroader | August 1994.', 'August 1994', '1994-08', null, null, [], true, 'scan'],
  5425 => ['magazine', 'magazine-pages', 'Dune Buggy | November 1970.', 'November 1970', '1970-11', null, null, [], true, 'scan'],
  /* What identifies the piece is not on the scan Craft holds: Leon's header only, or on Reggie only. */
  3049 => ['header', 'magazine-pages', null, null, null, null, null, [], true, 'header: The Land of Sunshine, December 1900'],
  4269 => ['header', 'magazine-pages', null, null, null, null, null, [], true, 'header: Compass, Pacific Telephone, Vol. 3 No. 11, August 8, 1966'],
  4691 => ['header', 'magazine-pages', 'By Michael Frost.', null, null, null, null, [], true, 'header: Pageant, May 1957 (the byline is printed)'],
  4909 => ['header', 'magazine-pages', null, null, null, null, null, [], true, 'header: TV Guide, 1957'],
  5245 => ['header', 'magazine-pages', 'Car Life | November 1964.', 'November 1964', '1964-11', null, null, [], true, 'byline from the header only: "By Bill Libby"; publication and date printed'],
  2963 => ['header', 'clipping', 'New-York Observer | Saturday, October 1, 1842.', 'Saturday, October 1, 1842', '1842-10-01', null, null, [], true, 'Reggie only: Craft holds the masthead strip (lw2181a); the dateline and the item are lw2181b, c, d'],
  /* Newspaper items, clipping held in Craft; publication and date already in sourceLine (from Leon's header). */
  20087 => ['clipping', 'clipping', null, null, null, null, null, [], true, 'header'],
  20090 => ['clipping', 'clipping', null, null, null, null, null, [], true, 'header'],
  20093 => ['clipping', 'clipping', null, null, null, null, null, [], true, 'header'],
  20096 => ['clipping', 'clipping', null, null, null, null, null, [], true, 'header'],
  20107 => ['clipping', 'clipping', null, null, null, null, null, [], true, 'header'],
  /* Newspaper items that print a letter (whose writer is an open question, TODO 6 October). */
  26983 => ['letter', 'clipping', null, null, null, null, null, [], true, 'header'],
  28057 => ['letter', 'clipping', null, null, null, 'Death of a Monster Bear.', null, [$HERALD], false, 'scan (on Reggie, tlp_laherald072875pg3; none in Craft): masthead "Los Angeles Herald. WEDNESDAY, JULY 28, 1875."'],
  /* Cityhood clippings (gt8702, vannuysvalleynews052775): the clipping is on Reggie, none in Craft. */
  28291 => ['cityhood', 'clipping', null, null, null, null, null, [], false, 'header'],
  28293 => ['cityhood', 'clipping', null, null, null, false, null, [], false, 'scan: folio "Sunday, January 11, 1987"; paper not named'],
  28310 => ['cityhood', 'clipping', null, null, null, null, null, [$SIGNAL], false, 'scan: folio "The Newhall Signal ... Sunday, January 4, 1987"'],
  /* Transcriptions on Leon's pages, no scan held: the Saugus High School shooting coverage. */
  31308 => ['saugus', 'transcription-only', null, null, null, null, null, [], false, 'page'],
  31310 => ['saugus', 'transcription-only', null, null, null, null, null, [], false, 'page'],
  31314 => ['saugus', 'transcription-only', null, null, null, null, null, [], false, 'page'],
  31316 => ['saugus', 'transcription-only', null, null, null, null, null, [], false, 'page'],
  31318 => ['saugus', 'transcription-only', null, null, null, null, null, [], false, 'page'],
  31320 => ['saugus', 'transcription-only', null, null, null, null, null, [], false, 'page'],
  31322 => ['saugus', 'transcription-only', null, null, null, null, null, [], false, 'page'],
  31324 => ['saugus', 'transcription-only', null, null, null, null, null, [], false, 'page'],
  31326 => ['saugus', 'transcription-only', null, null, null, null, null, [], false, 'page'],
  31328 => ['saugus', 'transcription-only', null, null, null, null, null, [], false, 'page'],
  31330 => ['saugus', 'transcription-only', null, null, null, null, null, [], false, 'page'],
  31332 => ['saugus', 'transcription-only', null, null, null, null, null, [], false, 'page'],
  /* Other transcriptions. */
  28305 => ['other', 'transcription-only', null, null, null, null, null, [], false, 'page'],
  31412 => ['other', 'transcription-only', null, null, null, null, null, [], false, 'page'],
];
/* The brief's do-not-touch list, checked rather than assumed. */
$NEVER = [2913, 5377];
$MAP = ['photoPeople' => 'subjectPerson', 'photoPlaces' => 'depictsPlace', 'photoOrganizations' => 'subjectOrganization',
  'photoEvents' => 'articleEvents', 'photoArticles' => 'relatedArticles', 'photoGroups' => 'subjectGroup', 'documentFiles' => 'recordDocuments'];

$artType = $es->getEntryTypeByHandle('article'); $artSec = $es->getSectionByHandle('articles');
$artHave = []; foreach ($artType->getFieldLayout()->getCustomFields() as $f) { $artHave[$f->handle] = $f; }
$uriFormat = $artSec->getSiteSettings()[array_key_first($artSec->getSiteSettings())]->uriFormat;
$redirectsSrc = (string)file_get_contents("$root/config/redirects.php");
$audit = (string)file_get_contents("$root/inventory/review/overnight-2026-10-08/type-audit-2026-10-08.md");
$notInAudit = array_values(array_filter(array_keys($P), fn($i) => !str_contains($audit, "#$i ") && !str_contains($audit, "#$i,") && !str_contains($audit, "#$i (")));
echo 'plan records not named in the type audit: ' . ($notInAudit ? '#' . implode(', #', $notInAudit) : 'none') . PHP_EOL . PHP_EOL;
$empty = function ($v) { if ($v === null || $v === '' || $v === [] || $v === false) { return true; }
  if (is_array($v)) { foreach ($v as $r) { if (is_array($r) ? array_filter($r, fn($x) => $x !== null && $x !== '' && $x !== false) : ($r !== null && $r !== '')) { return false; } } return true; } return false; };
$allows = function ($field, $el) { $src = $field->sources ?? '*'; if ($src === '*' || $src === null) { return true; }
  if ($el instanceof Entry) { return in_array('section:' . $el->section->uid, (array)$src, true); }
  if ($el instanceof Asset) { return in_array('volume:' . $el->getVolume()->uid, (array)$src, true); }
  return true; };
$short = fn($v) => mb_substr(str_replace("\n", ' / ', is_scalar($v) ? (string)$v : json_encode($v, JSON_UNESCAPED_UNICODE)), 0, 110);
$C = ['records' => 0, 'missing' => 0, 'already' => 0, 'move' => 0, 'carried' => 0, 'mapped' => 0, 'nohome' => 0, 'fills' => 0, 'keptExisting' => 0,
  'writtenBy' => 0, 'titleChange' => 0, 'leadPromoted' => 0, 'scanAssets' => 0, 'facsimile' => 0, 'faxShared' => 0, 'inbound' => 0, 'inboundBlocked' => 0,
  'disabled' => 0, 'leadCategory' => 0, 'navLines' => 0, 'redirects' => 0, 'slugClash' => 0, 'mapBlocked' => 0, 'charLimit' => 0];
$byKind = []; $flags = [];
$flag = function ($id, $s) use (&$flags) { $flags[$id][] = $s; };

foreach ($P as $id => [$kind, $held, $sl, $opd, $edtf, $opt, $w, $pub, $fax, $where]) {
  $C['records']++; $byKind[$kind][] = $id;
  if (in_array($id, $NEVER, true)) { echo "#$id is on the do-not-touch list; refused" . PHP_EOL; $flag($id, 'on the do-not-touch list'); continue; }
  $e = Entry::find()->id($id)->status(null)->one();
  if (!$e) { echo "#$id MISSING" . PHP_EOL; $C['missing']++; $flag($id, 'missing'); continue; }
  $from = $e->section->handle . '/' . $e->type->handle;
  echo "#$id [$kind] \"{$e->title}\"" . ($e->enabled ? '' : ' (disabled, stays disabled)') . PHP_EOL;
  if (!$e->enabled) { $C['disabled']++; }
  $has = []; foreach ($e->getFieldLayout()->getCustomFields() as $f) { $has[$f->handle] = $f; }
  if ($e->section->handle === 'articles') {
    $C['already']++; $h = isset($has['heldAs']) ? (string)($e->getFieldValue('heldAs')->value ?? '') : '';
    echo "   already an article (moved 7 October); heldAs " . ($h === '' ? "empty (would be \"$held\"; the fourteen-day audit's TYPE-1)" : "\"$h\"") . '; not in a batch' . PHP_EOL . PHP_EOL;
    $flag($id, 'already an article'); continue;
  }
  $C['move']++;
  echo "   $from -> articles/article" . PHP_EOL;
  /* Fields: carried by handle, mapped, or no home. */
  $vals = array_filter($e->getSerializedFieldValues(), fn($v) => !$empty($v));
  $carried = []; $mapped = []; $nohome = [];
  foreach ($vals as $h => $v) {
    if (isset($artHave[$h])) { $carried[] = $h; continue; }
    if (isset($MAP[$h]) && isset($artHave[$MAP[$h]])) {
      $to = $MAP[$h]; $tf = $artHave[$to]; $bad = [];
      foreach ((array)$v as $tid) { $t = Craft::$app->getElements()->getElementById((int)$tid); if (!$t || !$allows($tf, $t)) { $bad[] = $tid; } }
      $cur = isset($has[$to]) ? (array)$v : [];
      $mapped[] = "$h -> $to (" . count((array)$v) . ')' . ($bad ? ' BLOCKED for ' . implode(',', $bad) : '');
      if ($bad) { $C['mapBlocked']++; $flag($id, "$h -> $to blocked for " . implode(',', $bad)); }
      continue;
    }
    $dup = ''; foreach ($vals as $h2 => $v2) { if ($h2 !== $h && isset($artHave[$h2]) && is_string($v) && is_string($v2) && (str_contains($v2, trim($v, '> ')) )) { $dup = " (its text is already in $h2, which is carried)"; break; } }
    $nohome[] = "$h = \"" . $short($v) . '"' . $dup;
  }
  $C['carried'] += count($carried); $C['mapped'] += count($mapped); $C['nohome'] += count($nohome);
  echo '   carried as is (' . count($carried) . '): ' . implode(', ', $carried) . PHP_EOL;
  if ($mapped) { echo '   mapped: ' . implode('; ', $mapped) . PHP_EOL; }
  if ($nohome) { echo '   NO HOME on the article type: ' . implode('; ', $nohome) . PHP_EOL; $flag($id, 'a field with no home: ' . implode('; ', $nohome)); }
  /* Fills, only into empty fields. */
  $cur = fn($h) => isset($has[$h]) ? $e->getSerializedFieldValues([$h])[$h] ?? null : null;
  $fills = [];
  $want = ['heldAs' => $held];
  if ($sl !== null) { $want['sourceLine'] = $sl; }
  if ($opd !== null) { $want['originalPublishDate'] = $opd; }
  if ($edtf) { $want['originalPublishDateEdtf'] = $edtf; }
  if ($opt === null && in_array($kind, ['web', 'magazine', 'header'], true)) { $want['originallyPublishedTitle'] = $e->title; }
  elseif (is_string($opt)) { $want['originallyPublishedTitle'] = $opt; }
  foreach ($want as $h => $v) {
    $now = $cur($h);
    if ($h === 'heldAs') { $now = isset($has['heldAs']) ? (string)($e->getFieldValue('heldAs')->value ?? '') : ''; }
    if ($empty($now)) { $fills[] = "$h = \"$v\""; $C['fills']++;
      $f = $artHave[$h] ?? null; if ($f instanceof \craft\fields\PlainText && $f->charLimit && mb_strlen($v) > $f->charLimit) { $C['charLimit']++; $flag($id, "$h over its limit"); echo "   REFUSED $h: " . mb_strlen($v) . " > {$f->charLimit}" . PHP_EOL; } }
    elseif (trim((string)(is_scalar($now) ? $now : json_encode($now))) !== trim($v)) { $C['keptExisting']++; echo "   kept existing $h \"" . $short($now) . "\" (the plan read \"$v\")" . PHP_EOL;
      if ($h === 'originallyPublishedTitle') { $flag($id, "originallyPublishedTitle holds \"" . $short($now) . "\", not the printed head \"$v\""); } }
  }
  if ($pub) { $have = isset($has['publishedBy']) ? $e->getFieldValue('publishedBy')->status(null)->ids() : [];
    $add = array_values(array_diff($pub, $have)); if ($add) { $names = array_map(fn($x) => Entry::find()->id($x)->status(null)->one()?->title ?? "#$x", $add); $fills[] = 'publishedBy + ' . implode(', ', $names); $C['fills']++; } }
  if ($w) { $p = Entry::find()->id($w)->section('persons')->status(null)->one();
    $haveW = isset($has['writtenBy']) ? $e->getFieldValue('writtenBy')->status(null)->ids() : [];
    $body = (string)($cur('body') ?? '');
    $printed = (bool)preg_match('~By Leon Worden~i', $body);
    if (!$p) { echo "   writtenBy: person #$w not found; not set" . PHP_EOL; $flag($id, 'writer record missing'); }
    elseif (in_array($w, $haveW)) { echo "   writtenBy {$p->title} already set" . PHP_EOL; }
    else { $fills[] = "writtenBy {$p->title} (#$w), authorshipBasis printed-byline, note \"Printed byline \\\"By Leon Worden\\\". Read from the original page.\""; $C['fills']++; $C['writtenBy']++;
      echo '   the byline in the body Craft holds: ' . ($printed ? 'yes' : 'NOT FOUND (the type audit read it from the page on Reggie)') . PHP_EOL; } }
  echo '   fills (empty fields only): ' . ($fills ? implode('; ', $fills) : 'none') . PHP_EOL;
  echo "   publication and date read from: $where" . PHP_EOL;
  /* Title. */
  $printedHead = is_string($opt) ? rtrim($opt, '.') : null;
  if ($opt === false) { echo "   title: NO HEADLINE PRINTED; \"{$e->title}\" is a description" . PHP_EOL; $C['titleChange']++; $flag($id, 'no headline printed; title is a description with a suffix'); }
  else { $oPT = (string)($cur('originallyPublishedTitle') ?? '');
    $head = $printedHead ?? ($oPT !== '' ? rtrim(preg_replace('/\s+/', ' ', $oPT), '.') : null);
    $norm = fn($x) => strtolower(preg_replace('/[^a-z0-9]+/i', '', (string)$x));
    if ($head !== null && !str_starts_with($norm($head), $norm($e->title)) && !str_starts_with($norm($e->title), $norm($head))) { echo "   title: \"{$e->title}\" against the printed head \"$head\": CHECK" . PHP_EOL; $flag($id, "title against printed head \"$head\""); $C['titleChange']++; }
    else { echo '   title: unchanged (the printed head)' . PHP_EOL; } }
  /* The article page promotes a first paragraph to the lead. */
  $body = (string)($cur('body') ?? ''); $paras = array_values(array_filter(preg_split('/\n\n/', $body), fn($p) => trim($p) !== ''));
  if (count($paras) > 1 && !preg_match('/\[mfn|\[\s*\d{1,3}\s*\](?!\d)/i', $paras[0]) && !preg_match('/^\[[a-z]+\]/', trim($paras[0]))) {
    $lead = trim(strip_tags($paras[0])); $C['leadPromoted']++;
    if (str_starts_with($lead, '>')) { $C['leadCategory']++; echo "   LEAD on the article page would be the legacy category line \"" . $short($lead) . '" (a template question for every moved photograph; not a flag on the record)' . PHP_EOL; }
    elseif ($e->section->handle === 'photographs') { echo '   lead on the article page: "' . $short($lead) . '"' . PHP_EOL; }
  }
  if ($e->section->handle === 'photographs') {
    $NAV = ['click image to enlarge', 'click to enlarge', 'click each image to enlarge', 'click images to enlarge', 'click map to enlarge', 'download archival scan',
      'download archival scans', 'click image for more', 'click for more'];
    $navHit = [];
    foreach (preg_split('/\n/', $body) as $line) { $parts = array_map(fn($x) => strtolower(trim($x, " .|:\t")), explode('|', $line));
      if ($parts && count(array_filter($parts, fn($x) => in_array($x, $NAV, true))) && count(array_filter($parts, fn($x) => $x !== '' && !in_array($x, $NAV, true))) === 0) { $navHit[] = trim($line); } }
    if ($navHit) { $C['navLines']++; echo '   NAVIGATION the photograph page drops and the article page would print: "' . implode('", "', array_unique($navHit)) . '"' . PHP_EOL; }
  }
  /* The scans. */
  $assets = [];
  foreach (['featuredImage', 'recordImages', 'recordDocuments', 'documentFiles'] as $h) { if (!isset($has[$h])) { continue; }
    foreach ($e->getFieldValue($h)->all() as $a) { $assets[$a->id] = [$h, $a]; } }
  if (!$assets) { echo '   scans in Craft: none' . ($fax ? ' (FLAG: planned as facsimile)' : '') . PHP_EOL; }
  foreach ($assets as $aid => [$h, $a]) {
    $C['scanAssets']++;
    $role = ''; foreach ($a->getFieldLayout()->getCustomFields() as $f) { if ($f->handle === 'assetRole') { $role = (string)($a->getFieldValue('assetRole')->value ?? ''); } }
    $others = $db->createCommand("select r.sourceId from {{%relations}} r join {{%elements}} e on e.id=r.sourceId where r.targetId=$aid and r.sourceId<>$id and e.revisionId is null and e.draftId is null and e.dateDeleted is null")->queryColumn();
    $otherSec = array_map(function ($s) { $x = Craft::$app->getElements()->getElementById((int)$s); return $x instanceof Entry ? "#$s " . $x->section->handle : "#$s"; }, array_unique($others));
    $to = $fax ? ($role === '' ? ($others ? 'facsimile? SHARED, left' : 'facsimile') : "left ($role)") : 'left';
    if ($fax && $role === '' && !$others) { $C['facsimile']++; }
    if ($fax && $others) { $C['faxShared']++; $flag($id, "scan $aid shared with " . implode(', ', $otherSec)); }
    echo "   scan $h #$aid {$a->filename} role " . ($role ?: 'none') . " -> $to" . ($otherSec ? ' (also on ' . implode(', ', $otherSec) . ')' : '') . PHP_EOL;
  }
  /* Links that point here. Same id, so each survives if its field accepts articles. */
  $in = $db->createCommand("select r.sourceId, f.id fid, f.handle from {{%relations}} r join {{%fields}} f on f.id=r.fieldId join {{%elements}} e on e.id=r.sourceId where r.targetId=$id and e.revisionId is null and e.draftId is null and e.dateDeleted is null")->queryAll();
  $inb = [];
  foreach ($in as $r) { $C['inbound']++; $f = $fs->getFieldById((int)$r['fid']); $ok = $allows($f, (function () use ($artSec) { $x = new Entry(); $x->sectionId = $artSec->id; return $x; })());
    $s = Craft::$app->getElements()->getElementById((int)$r['sourceId']); $label = $s instanceof Entry ? "#{$s->id} {$s->section->handle} \"" . mb_substr($s->title, 0, 40) . '"' : "#{$r['sourceId']}";
    if (!$ok) { $C['inboundBlocked']++; $flag($id, "inbound {$r['handle']} from {$r['sourceId']} does not accept articles"); }
    if (in_array($r['handle'], ['derivedImageLinks'], true)) { continue; }
    $inb[] = "{$r['handle']} from $label" . ($ok ? '' : ' DOES NOT ACCEPT ARTICLES'); }
  $nd = count(array_filter($in, fn($r) => $r['handle'] === 'derivedImageLinks'));
  echo '   links in: ' . ($inb ? implode('; ', $inb) : 'none') . ($nd ? "; and $nd derivedImageLinks" : '') . ' (same id, kept)' . PHP_EOL;
  /* Address. */
  $clash = Entry::find()->section('articles')->slug($e->slug)->status(null)->ids();
  if ($clash) { $C['slugClash']++; $flag($id, 'slug clash with ' . implode(',', $clash)); }
  $new = str_replace('{slug}', $e->slug, $uriFormat);
  $C['redirects']++; echo "   address: {$e->uri} -> $new" . ($clash ? ' SLUG CLASH with #' . implode(',', $clash) : '') . (str_contains($redirectsSrc, "'{$e->uri}'") ? ' (already in redirects.php)' : '') . PHP_EOL . PHP_EOL;
}

/* Approval batches. A record with a flag goes to its kind's "with notes" batch; the rest are mechanical. */
$BATCH = ['web' => 'WEB', 'magazine' => 'MAGAZINE', 'header' => 'HEADER', 'clipping' => 'CLIPPING', 'letter' => 'LETTERS', 'cityhood' => 'CITYHOOD', 'saugus' => 'SAUGUS', 'other' => 'TRANSCRIPTS'];
echo 'BATCHES' . PHP_EOL;
foreach ($byKind as $k => $ids) {
  $out = array_values(array_filter($ids, fn($i) => isset($flags[$i]) && in_array('already an article', $flags[$i], true)));
  $ids = array_values(array_diff($ids, $out));
  $mech = array_values(array_filter($ids, fn($i) => !isset($flags[$i]))); $notes = array_values(array_diff($ids, $mech));
  echo "  {$BATCH[$k]}: " . count($ids) . ' (' . count($mech) . ' with nothing flagged: ' . ($mech ? '#' . implode(', #', $mech) : 'none') . '; ' . count($notes) . ' flagged: ' . ($notes ? '#' . implode(', #', $notes) : 'none') . ')' . ($out ? '; out of the batch: #' . implode(', #', $out) : '') . PHP_EOL;
}
echo PHP_EOL . 'FLAGS' . PHP_EOL; foreach ($flags as $i => $fl) { echo "  #$i: " . implode(' | ', array_unique($fl)) . PHP_EOL; }
echo PHP_EOL . 'COUNTS' . PHP_EOL; foreach ($C as $k => $v) { echo "  $k: $v" . PHP_EOL; }
echo 'nothing written (dry run; a second run prints the same plan)' . PHP_EOL;
