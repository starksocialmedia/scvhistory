/**
 * The Mentry sources: the pages ch1070 links to that have no record yet, as
 * two photographs and eight documents, with their scans brought in from Reggie.
 *
 * Of the eight pages ch1070 links, only lw2585 is held (photograph #4187).
 * These are the other seven. One of them, sw_petermentre, is four printed items
 * on one page, each with its own clipping and its own date and paper, so it
 * becomes four documents rather than one record with four dates. That is why
 * seven pages make ten records.
 *
 * SOURCE. inventory/legacy/mentry-sources.json, written by
 * extract_mentry_sources.py from the Reggie mirror with each page's sha256.
 * Verbatim; see that script for what "verbatim" allows.
 *
 * TRANSCRIPTION IN THE BODY, INTERPRETATION IN THE NOTE. A document's body is
 * what the source says and nothing else. What the webmaster or a contributor
 * says about it goes to webmasterNoteTop, and credits and editing notes to
 * webmasterNoteBottom. So the death certificate record has an EMPTY body: the
 * legacy page never transcribed the certificate, it wrote an essay about it,
 * and the essay is 2014 talking, not 1900. The facts the essay quotes from the
 * certificate go into recordDates labelled as quoted, unconfirmed, until
 * someone reads the scan.
 *
 * HEADLINES. originallyPublishedTitle holds a headline only where the original
 * printed it. The 1886 and 1899 clippings print theirs. "Ode to the Man Behind
 * Mentryville" is SCVHistory's 2013 headline for Scofield's eulogy, and the
 * 1954 headline is the webmaster's, so both of those stay empty.
 *
 * SUBJECTS. subjectPerson says who a document is about, not who it names. Alex
 * Mentry #18648 is the subject of the biography, the certificate, the eulogy
 * and the 1931 tablet item. The 1886 and 1899 items are about his father, and
 * the 1954 item about his son; neither has a record, so those four carry no
 * subject until they do. They reach Mentry through the Mentryville collection.
 *
 * SCANS. Read from the mirror, not the live site (DRIVE.md). Where the mirror
 * holds a _large master it is imported instead of the 800px copy:
 * as0001_large.jpg (2400 x 3899) and penpictureslacounty_large.jpg (2400 x 3196).
 * Nothing else here has one. ch1070's own portrait is already asset #2324, and
 * no master of it exists anywhere on the drive; that belongs to the profile.
 * The scan lines say "9600 dpi jpeg from smaller jpeg": these are enlargements
 * of small files, not scans of originals, and the record says so in creditRaw.
 *
 * Idempotent: an entry with the legacyKey or an asset with the filename is
 * found and left alone.
 *
 * Needs the Reggie mount in the container: /mnt/reggie. After a remount of the
 * drive, `ddev restart` first.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_mentry_sources.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$SRC      = \Craft::getAlias('@root') . '/inventory/legacy/mentry-sources.json';
$MIRROR   = '/mnt/reggie/scvhistory.com';
$MENTRY   = 18648;          /* person: Alex Mentry */
$HERALD   = 390;            /* organization: Los Angeles Herald */
$COMMUNITY_SLUG = 'mentryville';
$FOLDER_PATH = 'legacy/';   /* where the mirror import puts legacy images */

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

if (!is_file($SRC)) { echo 'source extract missing: ' . $SRC . PHP_EOL; return; }
$src = json_decode((string)file_get_contents($SRC), true);
if (!is_dir($MIRROR)) {
    echo 'the mirror is not mounted at ' . $MIRROR . '. Run: ddev restart' . PHP_EOL;
    if ($APPLY) { return; }
}

$elements = Craft::$app->getElements();
$svc = Craft::$app->getEntries();
$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

$mentry = \craft\elements\Entry::find()->id($MENTRY)->section('persons')->status(null)->one();
$herald = \craft\elements\Entry::find()->id($HERALD)->section('organizations')->status(null)->one();
$community = \craft\elements\Category::find()->group('neighborhood')->slug($COMMUNITY_SLUG)->one();
if (!$mentry || !$herald || !$community) {
    echo 'missing: ' . (!$mentry ? 'person #' . $MENTRY . ' ' : '') . (!$herald ? 'org #' . $HERALD . ' ' : '')
        . (!$community ? 'community ' . $COMMUNITY_SLUG : '') . PHP_EOL;
    return;
}
$volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia');
$folder = $volume ? Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => $FOLDER_PATH]) : null;
if (!$folder) { echo 'asset folder archiveMedia/' . $FOLDER_PATH . ' not found' . PHP_EOL; return; }

/* ------------------------------------------------------------- the records */

$slug = fn(string $s): string => \craft\helpers\StringHelper::toKebabCase(
    \craft\helpers\StringHelper::toAscii(trim($s, ". \t")));
$para = fn(array $lines): string => implode("\n\n", array_map('trim', $lines));
$legacy = fn(string $page): array => [
    'legacyUrl' => '/scvhistory/' . $page . '.htm',
    'sourcePath' => 'https://scvhistory.com/scvhistory/' . $page . '.htm',
];
/* iso is a date column: stored as a whole date at midnight, the way
   import_ruiz_census.php writes it, whatever the precision. granularity says
   how much of it is meant. */
$dateRow = fn(string $printed, string $iso, string $gran, string $label): array => [
    'printed' => $printed, 'iso' => substr($iso, 0, 10) . ' 00:00:00', 'granularity' => $gran,
    'label' => $label, 'confirmed' => false,
];
/* The scan line, read the way import_lw_features.php reads it. */
$readCredit = function (string $raw): array {
    $out = ['creditDpi' => '', 'creditProcess' => '', 'creditKind' => ''];
    $rest = trim(preg_replace('~^[A-Za-z_]+\d+[a-z]?\s*:\s*~', '', $raw), ' .');
    if (preg_match('~(\d{2,6})\s*dpi\b~i', $rest, $m)) { $out['creditDpi'] = $m[1]; }
    if (preg_match('~dpi\s+(\w+)\s+from\s+(.+)$~i', $rest, $m)) { $out['creditProcess'] = $m[1]; $out['creditKind'] = $m[2]; }
    return $out;
};

$plan = [];
foreach ($src['items'] as $it) {
    $key = $it['key'];
    $img = $it['image'] ?? null;
    $imgRel = $img ? ($img['master'] ?? $img['file']) : null;

    if ($it['kind'] === 'photograph') {
        $f = array_merge([
            'body' => $para($it['caption']),
            'photoSourceCode' => $it['code'],
            'creditRaw' => $it['scan_line'],
            'photoPeople' => [$MENTRY],
            'neighborhood' => [$community->id],
            'legacyKey' => $key,
        ], $legacy($it['page']), $readCredit($it['scan_line']));
        if ($key === 'ch1040') {
            $f['photoDate'] = '1893';
            $f['photoDateEdtf'] = '1893';
            $f['recordDates'] = [$dateRow('1893', '1893-01-01', 'year', 'hand-written on the print, as the legacy caption reports it')];
        }
        $plan[] = ['key' => $key, 'section' => 'photographs', 'type' => 'photograph',
                   'title' => rtrim($it['headline'], '.'),
                   'slug' => $slug($it['headline']) . (isset($f['photoDateEdtf']) ? '-' . $f['photoDateEdtf'] : ''),
                   'fields' => $f, 'image' => $imgRel, 'code' => $it['code']];
        continue;
    }

    /* Documents. The masthead is "Paper | Date." for the clippings. */
    $paper = ''; $printedDate = ''; $edtf = ''; $gran = '';
    if (str_contains($it['masthead'] ?? '', ' | ')) {
        [$paper, $printedDate] = array_map('trim', explode(' | ', $it['masthead'], 2));
        $d = \DateTime::createFromFormat('!F j, Y', preg_replace('~^\w+day,\s*~', '', rtrim($printedDate, '.')));
        if ($d) { $edtf = $d->format('Y-m-d'); $gran = 'day'; }
    }
    $top = []; $bottom = [];
    foreach ($it['framing'] ?? [] as $l) {
        if (preg_match('~^(News reports courtesy|News story courtesy|SCVHistory\.com \||Webmaster\'s note\. Text has been|This eulogy comes to us)~', $l)) { $bottom[] = $l; }
        elseif ($l === "Webmaster's note.") { continue; }
        else { $top[] = $l; }
    }
    if (str_starts_with($key, 'sw_') && !$bottom) { $bottom[] = 'News reports courtesy of Stan Walker'; }
    if (!empty($it['scan_line'])) { $bottom[] = $it['scan_line']; }

    $f = array_merge([
        'body' => $para($it['text']),
        'sourceLine' => $it['masthead'] ?? '',
        'originallyPublishedTitle' => '',
        'originalPublishDate' => $printedDate ? rtrim($printedDate, '.') : '',
        'originalPublishDateEdtf' => $edtf,
        'webmasterNoteTop' => $para($top),
        'webmasterNoteBottom' => $para($bottom),
        'neighborhood' => [$community->id],
        'legacyKey' => $key,
        'recordDates' => $edtf ? [$dateRow(rtrim($printedDate, '.'), $edtf . 'T00:00:00', $gran, 'masthead date, ' . $paper . '; not yet checked against the clipping')] : [],
        'subjectPerson' => [],
        'publishedBy' => [],
    ], $legacy($it['page']));
    $title = rtrim($it['headline'], '.');

    switch ($key) {
        case 'sw_herald111186':
        case 'sw_herald031799':
            $f['publishedBy'] = [$HERALD];
            $f['originallyPublishedTitle'] = $it['headline'] . ($it['subheadline'] ? ' ' . $it['subheadline'] : '');
            break;
        case 'sw_lat031799':
            $f['originallyPublishedTitle'] = $it['headline'] . ' ' . $it['subheadline'];
            break;
        case 'lp_warrenpatimesmirror020431':
            $f['originallyPublishedTitle'] = $it['headline'];
            $f['subjectPerson'] = [$MENTRY];
            break;
        case 'penpictures_mentry':
            $title = 'C.A. Mentry, in Pen Pictures From the Garden of the World';
            $f['originallyPublishedTitle'] = $it['headline'];
            $f['originalPublishDate'] = '1889';
            $f['originalPublishDateEdtf'] = '1889';
            $f['recordDates'] = [$dateRow('1889', '1889-01-01', 'year', 'imprint, The Lewis Publishing Co., Chicago')];
            $f['subjectPerson'] = [$MENTRY];
            break;
        case 'as0001':
            $f['subjectPerson'] = [$MENTRY];
            $f['recordDates'] = [
                $dateRow('Oct. 4, 1900', '1900-10-04T00:00:00', 'day', 'date of death, as the certificate states it, quoted on the legacy page; not yet read off the scan'),
                $dateRow('March 27, 1847', '1847-03-27T00:00:00', 'day', 'date of birth, as the certificate states it, quoted on the legacy page; the same certificate gives an age that computes to 1848'),
            ];
            break;
        case 'scofield':
            $title = "Demetrius Scofield's Eulogy to Charles Alexander Mentry";
            $f['sourceLine'] = $it['byline'];
            $f['originalPublishDate'] = '';
            $f['originalPublishDateEdtf'] = '1900';
            $f['recordDates'] = [
                $dateRow('1900', '1900-01-01', 'year', 'delivered upon Mentry\'s death, per the legacy standfirst; no day is printed'),
                $dateRow('September 1997', '1997-09-01', 'month', 'republished in the Old Town Newhall Gazette'),
            ];
            $f['subjectPerson'] = [$MENTRY];
            break;
        case 'lp_lat031754':
            $title = 'Death of Arthur Charles Mentry';
            break;
    }
    $yr = substr($f['originalPublishDateEdtf'], 0, 4);
    $plan[] = ['key' => $key, 'section' => 'documents', 'type' => 'document',
               'title' => $title, 'slug' => $slug($title) . ($yr ? '-' . $yr : ''),
               'fields' => $f, 'image' => $imgRel, 'code' => strtoupper($key)];
}

/* ------------------------------------------------------------------ report */

$assetFor = [];
foreach ($plan as &$p) {
    $sec = $svc->getSectionByHandle($p['section']);
    $type = $svc->getEntryTypeByHandle($p['type']);
    $layoutHandles = array_map(fn($x) => $x->handle, $type->getFieldLayout()->getCustomFields());
    $p['sectionId'] = $sec->id; $p['typeId'] = $type->id;
    $p['absent'] = array_values(array_diff(array_keys($p['fields']), $layoutHandles));
    $p['existing'] = \craft\elements\Entry::find()->section($p['section'])->status(null)->legacyKey($p['key'])->one();
    if ($p['image']) {
        $fn = basename($p['image']);
        $p['asset'] = \craft\elements\Asset::find()->volumeId($volume->id)->filename($fn)->one();
        $p['mirrorFile'] = $MIRROR . $p['image'];
        $p['onMirror'] = is_file($p['mirrorFile']);
    }
}
unset($p);

foreach ($plan as $p) {
    echo PHP_EOL . ($p['existing'] ? 'EXISTS #' . $p['existing']->id . ' ' : 'NEW       ') . $p['section'] . '  ' . $p['title']
        . '   [' . $p['key'] . ']' . PHP_EOL;
    echo '   slug                      ' . $p['slug'] . PHP_EOL;
    foreach ($p['fields'] as $h => $v) {
        if ($v === '' || $v === []) { continue; }
        if ($h === 'body' || $h === 'webmasterNoteTop') { echo '   ' . str_pad($h, 26) . mb_strlen($v) . ' chars: ' . mb_substr(preg_replace('~\s+~', ' ', $v), 0, 70) . '...' . PHP_EOL; continue; }
        $show = is_array($v) ? json_encode($v, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : (string)$v;
        echo '   ' . str_pad($h, 26) . mb_substr(preg_replace('~\s+~', ' ', $show), 0, 100) . PHP_EOL;
    }
    if ($p['image']) {
        echo '   ' . str_pad('featuredImage', 26) . $p['image']
            . ($p['asset'] ? '   held, asset #' . $p['asset']->id : ($p['onMirror'] ? '   to import from the mirror' : '   NOT ON THE MIRROR')) . PHP_EOL;
    } else {
        echo '   ' . str_pad('featuredImage', 26) . '(none: the page has no scan)' . PHP_EOL;
    }
    if ($p['absent']) { echo '   NOT ON THE LAYOUT, will be dropped: ' . implode(', ', $p['absent']) . PHP_EOL; }
}

$toMake = count(array_filter($plan, fn($p) => !$p['existing']));
$toImport = count(array_filter($plan, fn($p) => $p['image'] && !$p['asset']));
$missing = array_filter($plan, fn($p) => $p['image'] && !$p['asset'] && !$p['onMirror']);
echo PHP_EOL . str_repeat('-', 78) . PHP_EOL;
echo 'records to create: ' . $toMake . ' of ' . count($plan) . PHP_EOL;
echo 'scans to import from the mirror: ' . $toImport . PHP_EOL;
echo 'scans missing from the mirror: ' . count($missing) . PHP_EOL;
echo 'without a record for: Peter Mentre (1886 and 1899 items), Arthur Charles Mentry (1954), Demetrius G. Scofield (author of the eulogy)' . PHP_EOL;
echo 'without an organization for: Los Angeles Times, Warren Times Mirror, The Lewis Publishing Co.; named in sourceLine only' . PHP_EOL;

if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}
if ($missing) { echo 'refusing: a scan is not on the mirror' . PHP_EOL; return; }

/* ------------------------------------------------------------------- apply */

$tmpDir = sys_get_temp_dir() . '/mentry-import';
@mkdir($tmpDir, 0775, true);
$made = []; $failed = [];
foreach ($plan as $p) {
    $assetId = $p['asset']->id ?? null;
    if ($p['image'] && !$assetId) {
        /* The mirror is read-only: copy first, as import_mirror_images.php does. */
        $fn = basename($p['image']);
        $tmp = $tmpDir . '/' . $fn;
        if (!@copy($p['mirrorFile'], $tmp)) { $failed[] = $fn . ': could not copy from the mirror'; continue; }
        $a = new \craft\elements\Asset();
        $a->tempFilePath = $tmp;
        $a->setFilename($fn);
        $a->newFolderId = $folder->id;
        $a->setVolumeId($volume->id);
        $a->setScenario(\craft\elements\Asset::SCENARIO_CREATE);
        $a->avoidFilenameConflicts = false;
        if (!$elements->saveElement($a)) { $failed[] = $fn . ': ' . json_encode($a->getErrors()); continue; }
        /* Reloaded before provenance: see import_mirror_images.php on why a
           second save of a create-scenario asset is refused. */
        $fresh = \craft\elements\Asset::find()->id($a->id)->one();
        $fresh->setFieldValue('photoSourceCode', $p['code']);
        $fresh->setFieldValue('legacySourcePath', $p['image']);
        if (!$elements->saveElement($fresh)) { $failed[] = $fn . ': provenance ' . json_encode($fresh->getFirstErrors()); }
        $assetId = $a->id;
        @unlink($tmp);
        echo 'imported ' . $fn . ' -> asset #' . $assetId . PHP_EOL;
    }
    if ($p['existing']) { $made[$p['key']] = $p['existing']->id; continue; }

    $e = new \craft\elements\Entry();
    $e->sectionId = $p['sectionId'];
    $e->setTypeId($p['typeId']);
    $e->title = $p['title'];
    $e->slug = $p['slug'];
    $values = array_diff_key($p['fields'], array_flip($p['absent']));
    $values = array_filter($values, fn($v) => $v !== '' && $v !== []);
    if ($assetId) { $values['featuredImage'] = [$assetId]; }
    $e->setFieldValues($values);
    if (!$elements->saveElement($e)) { $failed[] = $p['key'] . ': ' . json_encode($e->getErrors()); continue; }
    $made[$p['key']] = $e->id;
    echo 'created #' . $e->id . '  ' . $p['section'] . '  ' . $p['title'] . PHP_EOL;
}

/* ---------------------------------------------------------------- read back */

$short = [];
foreach ($plan as $p) {
    $id = $made[$p['key']] ?? null;
    $e = $id ? \craft\elements\Entry::find()->id($id)->status(null)->one() : null;
    if (!$e) { $short[] = $p['key'] . ': no record'; continue; }
    if ($p['existing']) { continue; }
    $want = array_diff_key($p['fields'], array_flip($p['absent']));
    foreach ($want as $h => $v) {
        if ($v === '' || $v === []) { continue; }
        $got = $e->getFieldValue($h);
        if ($got instanceof \craft\elements\db\ElementQuery) {
            if ($got->status(null)->ids() != $v) { $short[] = $p['key'] . ': ' . $h . ' reads ' . json_encode($got->status(null)->ids()); }
        } elseif ($h === 'recordDates') {
            if (count((array)$got) !== count($v)) { $short[] = $p['key'] . ': recordDates ' . count((array)$got) . ' rows, not ' . count($v); }
            elseif (trim((string)(((array)$got)[0]['printed'] ?? '')) !== $v[0]['printed']) { $short[] = $p['key'] . ': recordDates printed column reads empty'; }
        } elseif (trim((string)$got) !== trim((string)$v)) {
            $short[] = $p['key'] . ': ' . $h . ' reads back ' . mb_strlen(trim((string)$got)) . ' chars, not ' . mb_strlen(trim((string)$v));
        }
    }
    if ($p['image'] && !$e->featuredImage->one()) { $short[] = $p['key'] . ': no featuredImage'; }
    if ($p['image']) {
        $a = $e->featuredImage->one();
        if ($a && trim((string)$a->getFieldValue('legacySourcePath')) !== $p['image']) { $short[] = $p['key'] . ': asset legacySourcePath reads "' . $a->getFieldValue('legacySourcePath') . '"'; }
    }
}
$bad = array_merge($failed, $short);
echo PHP_EOL . 'READ-BACK ' . ($bad ? 'SHORT' : 'OK') . ': ' . (count($plan) - count($short)) . ' of ' . count($plan) . PHP_EOL;
foreach ($bad as $b) { echo '   FAIL ' . $b . PHP_EOL; }

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('import_mentry_sources.php', count($made), ($bad ? 'SHORT: ' . implode('; ', $bad) : 'verified: ' . count($made) . ' records and their scans read back'),
    '2 photographs, 8 documents from 7 legacy pages; masters as0001_large, penpictureslacounty_large');
if ($bad) { throw new \RuntimeException('import_mentry_sources: read-back failed'); }
