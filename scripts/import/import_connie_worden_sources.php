/**
 * Connie Worden's sources, on the Mentry pattern (Nathan, 3 October 2026: "Yes
 * to the roughly 12 imports for Connie Worden, on the Mentry pattern. Her papers
 * being the source of nearly all the city-formation material is the finding.
 * She is a source of this archive, not only a subject in it").
 *
 * SOURCE. inventory/legacy/connie-worden-sources.json: 18 items from 17 mirror
 * pages, each page and file sha256-checked against the Reggie manifest, bodies
 * verbatim, every claim an exact phrase of its body. Flipbook pages (the 2003
 * minutes, her 2003 proposal, the City's 2007 book) were transcribed from the
 * page images, not the OCR; agenda page 414 is OCR only and says so.
 *
 * WHAT THIS DOES. Creates 14 documents, each with its body (the source's own
 * words), its notes (commentary to webmasterNoteTop, credits and apparatus to
 * webmasterNoteBottom), its date, its legacy page, and Connie Worden (#16418) as
 * subject. Her 1999 typescript (cw9901.pdf, manifest-matched, copied to
 * storage/cw-scans/ because the container does not see the drive) is attached as
 * a document file.
 *
 * HELD, for Nathan:
 *   gt8702_sg010487, Laurel Suomisto's "Cityhood Backers: Who Are They?" (The
 *     Signal, January 4, 1987): verbatim, it gives the ages, home communities and
 *     businesses of committee members who may be living. Whether a verbatim 1987
 *     story keeps them is a rule for Nathan, not a script.
 *   The three photographs credited to her files (#5701 LW8501, #4255 LW2612,
 *     #4861 LW3060): they are not pictures of her, so photoPeople would be wrong.
 *     They wait for fromCollectionOf (step 4 of the relationship model).
 *
 * Idempotent: a document with the legacyKey is left alone.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_connie_worden_sources.php'))"
 */

use craft\elements\Entry;
use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$src = json_decode(file_get_contents("$root/inventory/legacy/connie-worden-sources.json"), true);
$svc = Craft::$app->getEntries(); $el = Craft::$app->getElements();
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', (string)$s));
$para = fn($v) => is_array($v) ? implode("\n\n", array_map('trim', $v)) : trim((string)$v);
$CW = 16418; $HOLD = ['gt8702_sg010487'];
$PUB = ['sg110185_lutz' => 376, 'gt8702_sg011187' => 376, 'sc1708' => 394, 'sc19872007_p67' => 394, 'scvhs_minutes_20030519' => 15493];
$SCAN = ['cw9901' => "$root/storage/cw-scans/cw9901.pdf"];
$bad = [];
$cw = Entry::find()->id($CW)->status(null)->one();
if (!$cw || !str_contains($cw->title, 'Worden')) { $bad[] = '#16418 is not Connie Worden'; }
foreach ($PUB as $k => $id) { if (!Entry::find()->id($id)->section('organizations')->status(null)->exists()) { $bad[] = "publisher #$id missing"; } }
$type = $svc->getEntryTypeByHandle('document'); $sec = $svc->getSectionByHandle('documents');
$handles = array_map(fn($f) => $f->handle, $type->getFieldLayout()->getCustomFields());
$dateRow = fn(string $printed, string $iso, string $gran, string $label): array => ['printed' => $printed, 'iso' => $iso . ' 00:00:00', 'granularity' => $gran, 'label' => $label, 'confirmed' => false];

$plan = [];
foreach ($src['items'] as $it) {
    if ($it['kind'] !== 'document') { continue; }
    $k = $it['key'];
    $body = $para($it['body']);
    foreach ((array)$it['claims'] as $c) { if (!str_contains($ws($body), $ws($c))) { $bad[] = "$k: claim not in body: " . mb_substr($c, 0, 50); } }
    $page = $src['pages'][$it['page']] ?? null;
    if (!$page || empty($page['manifest_matched'])) { $bad[] = "$k: page not manifest-matched"; }
    $bottom = (array)($it['webmasterNoteBottom'] ?? []);
    if (!empty($it['ocr_status'])) { $bottom[] = 'Transcription: ' . (is_array($it['ocr_status']) ? implode(' ', $it['ocr_status']) : $it['ocr_status']); }
    if (!empty($it['excerpt'])) { $bottom[] = 'An excerpt: ' . (is_string($it['excerpt']) ? $it['excerpt'] : 'the part of the page about her.'); }
    if (!empty($it['conflict'])) { $bottom[] = 'As printed: ' . (is_string($it['conflict']) ? $it['conflict'] : json_encode($it['conflict'])); }
    $d = $it['date'] ?? []; $edtf = (string)($d['edtf'] ?? '');
    $gran = preg_match('~^\d{4}-\d{2}-\d{2}$~', $edtf) ? 'day' : (preg_match('~^\d{4}$~', $edtf) ? 'year' : '');
    $f = [
        'body' => $body,
        'sourceLine' => (string)($it['masthead'] ?? ($it['byline'] ?? '')),
        'originallyPublishedTitle' => (string)($it['originally_published_title'] ?? ''),
        'originalPublishDate' => (string)($d['printed'] ?? ''),
        'originalPublishDateEdtf' => $edtf,
        'webmasterNoteTop' => $para($it['webmasterNoteTop'] ?? []),
        'webmasterNoteBottom' => $para($bottom),
        'subjectPerson' => [$CW],
        'publishedBy' => isset($PUB[$k]) ? [$PUB[$k]] : [],
        'legacyKey' => $k,
        'legacyUrl' => $page['path'] ?? '',
        'sourcePath' => 'https://scvhistory.com' . ($page['path'] ?? ''),
        'recordDates' => $gran ? [$dateRow((string)$d['printed'], $gran === 'year' ? "$edtf-01-01" : $edtf, $gran, ($d['from'] ?? 'as printed') . '; not yet checked against the original')] : [],
    ];
    $absent = array_values(array_diff(array_keys($f), $handles));
    $existing = Entry::find()->section('documents')->status(null)->legacyKey($k)->one();
    $scan = $SCAN[$k] ?? null;
    if ($scan && (!is_file($scan) || hash_file('sha256', $scan) !== $it['scan']['sha256'])) { $bad[] = "$k: the scan copy is missing or changed"; }
    $plan[] = ['key' => $k, 'title' => rtrim($it['title'], '.'), 'fields' => $f, 'absent' => $absent, 'existing' => $existing, 'hold' => in_array($k, $HOLD), 'scan' => $scan];
}
foreach ($plan as $p) {
    printf("%-6s %-26s %s%s\n", $p['hold'] ? 'HOLD' : ($p['existing'] ? 'have' : 'create'), $p['key'], mb_substr($p['title'], 0, 70), $p['scan'] ? '  + typescript scan' : '');
    if ($p['absent']) { echo '       not on the layout: ' . implode(', ', $p['absent']) . PHP_EOL; }
}
$todo = array_filter($plan, fn($p) => !$p['hold'] && !$p['existing']);
echo count($todo) . ' documents to create, ' . count(array_filter($plan, fn($p) => $p['hold'])) . ' held' . PHP_EOL;
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => 'legacy/']);
$made = 0; $short = [];
foreach ($todo as $p) {
    $vals = array_filter(array_diff_key($p['fields'], array_flip($p['absent'])), fn($v) => $v !== '' && $v !== []);
    if ($p['scan']) {
        $fn = 'cw9901.pdf'; $a = Asset::find()->volumeId($volume->id)->filename($fn)->one();
        if (!$a) {
            $tmp = sys_get_temp_dir() . '/' . $fn; copy($p['scan'], $tmp);
            $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($fn); $a->newFolderId = $folder->id; $a->setVolumeId($volume->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
            if (!$el->saveElement($a)) { $short[] = "$fn: " . json_encode($a->getFirstErrors()); continue; }
            $fresh = Asset::find()->id($a->id)->one(); $fresh->title = 'A Brief History of the Push for Self-Government in Santa Clarita, typescript, 1999';
            $ah = array_map(fn($f) => $f->handle, $fresh->getFieldLayout()->getCustomFields());
            $fresh->setFieldValues(array_intersect_key(['legacySourcePath' => '/scvhistory/files/cw9901/cw9901.pdf', 'photoSourceCode' => 'CW9901', 'sourceChecksum' => 'sha256:' . hash_file('sha256', $p['scan'])], array_flip($ah)));
            if (!$el->saveElement($fresh)) { $short[] = "$fn: " . json_encode($fresh->getFirstErrors()); }
        }
        $vals['documentFiles'] = [$a->id];
    }
    $e = new Entry(); $e->sectionId = $sec->id; $e->setTypeId($type->id); $e->title = $p['title'];
    $e->setFieldValues($vals);
    if (!$el->saveElement($e)) { $short[] = "{$p['key']}: " . json_encode($e->getFirstErrors()); continue; }
    $r = Entry::find()->id($e->id)->status(null)->one();
    if ($ws($r->body) !== $ws($p['fields']['body']) || $r->subjectPerson->one()?->id !== $CW) { $short[] = "{$p['key']}: read-back"; continue; }
    $made++; echo "created #{$e->id} {$p['title']}" . PHP_EOL;
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : "OK: $made documents") . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('import_connie_worden_sources.php', $made, $short ? 'SHORT' : 'verified', 'Connie Worden: 14 source documents and her 1999 typescript; Suomisto 1987 held; three collection photographs wait for fromCollectionOf');
if ($short) { throw new \RuntimeException('import_connie_worden_sources: ' . implode('; ', $short)); }
