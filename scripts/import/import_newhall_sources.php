/**
 * The Henry Mayo Newhall sources: nine legacy pages as eight records, seven
 * documents and one photograph, with their scans brought in from Reggie.
 *
 * THE SET (Nathan, 29 September 2026). Grok's read of the two hub pages found
 * six sources dated within his lifetime; the inventory added al1873, lw3756 and
 * al1870. hs6501a and hs6501b are two views of one receipt, so they are one
 * record with two images. That is why nine pages make eight records.
 *
 *   hs6501a+b  documents    receipt, S.F. & S.J. Railroad, 23 December 1865
 *   lw3583     documents    railroad pass signed by Newhall, undated, 1860s:
 *                           valid until 1 January 1870, which is its expiry,
 *                           not its date (the inventory said 1870 and was wrong)
 *   al1870     documents    S.F. & S.J. Railroad document. lw3583's page
 *                           questions whether the Newhall signature on it is
 *                           genuine, so it is never cited flat: it carries a
 *                           footnote quoting that doubt, with footnotesOn
 *                           pointing at the lw3583 record, and it is HELD until
 *                           the lines quoted are chosen in $CHALLENGE.
 *   lw2182     documents    billhead, H.M. Newhall & Co., 15 April 1870. Signed
 *                           by G. Palache, not by Newhall. The page's essay
 *                           says he reached San Francisco in 1850; that is
 *                           Leon Worden's later account, framing, not the
 *                           billhead, and the hub he also wrote says 1849.
 *   al1873     documents    billhead, H.M. Newhall & Co., 1873
 *   lw3756     documents    advertisement, H.M. Newhall & Co., 1867-1874
 *   rn7301     photographs  his five sons, 1873. The subject is the sons, not
 *                           him: photoPeople names whichever of them has a
 *                           record, and he is not added.
 *   al1882     documents    obituary, California Spirit of the Times, 18 March
 *                           1882. Five days after his death: contemporary, not
 *                           within his lifetime. It gives his birth as May 23,
 *                           1825; the site's own footnote calls May 13 the
 *                           commonly listed date. The disagreement belongs to the
 *                           profile; here the obituary's date is recorded as the
 *                           obituary states it, only if the extract finds it.
 *   sfexaminer18820321hmn documents  reading of the will, S.F. Examiner, 1882.
 *                           Its page embeds sfexaminer18740708hmn.jpg, reported
 *                           by the extractor as a lead; nothing here dates
 *                           anything 1874.
 *
 * SOURCE. inventory/legacy/newhall-sources.json, written on the host by
 * extract_newhall_sources.py, with each page's sha256.
 *
 * THE BODY IS HELD UNTIL THE CUT IS READ. The Mentry import could put the
 * transcription in the body and the webmaster's words in webmasterNoteTop
 * because each page had been read and cut by hand. These pages have not been
 * read on the mirror yet. So a record is planned in full from what is known,
 * and HELD, created by nobody, until its entry in $SPLITS names the line ranges
 * of the extract: which lines are the source (body), which are the webmaster
 * about it (webmasterNoteTop), which are credits and scan lines
 * (webmasterNoteBottom), and which line is the masthead (sourceLine). A page
 * that never transcribes its document, only writes about it, gets an empty body,
 * as the Mentry death certificate did.
 *
 * DATES. A date is set only when the extract finds it printed on the page: the
 * ISO form in $RECORDS must be among the dates the extractor parsed, or the
 * date is dropped and reported. recordDates has no decade precision, so lw3583
 * gets originalPublishDateEdtf 186X and no recordDates row.
 *
 * SUBJECTS. subjectPerson is Henry Mayo Newhall #283 for the pass, the
 * al1870 document, the obituary and the will. The receipt, the billheads and
 * the advertisement are his firm's and his railroad's paper; neither firm has
 * an organization record, so they carry him as subject too, which is reported
 * as a decision for Nathan. The receipt's railroad and the two newspapers have
 * no organization records either: publishedBy stays empty and they are named
 * in sourceLine only. #16312 "Henry M. Newhall" is a second person record for
 * the same name and is reported, not touched.
 *
 * No evidence level is set: import_mentry_sources.php set none, and
 * set_evidence_levels.php rates records after the fact.
 *
 * Idempotent: an entry with the legacyKey or an asset with the filename is
 * found and left alone. Needs the Reggie drive: _reggie.php stops the run with
 * a clear message when it is not connected.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_newhall_sources.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

$SRC      = \Craft::getAlias('@root') . '/inventory/legacy/newhall-sources.json';
$MIRROR   = (require \Craft::getAlias('@root') . '/scripts/import/_reggie.php')('scvhistory.com');   /* stops here, clearly, when the drive is out */
$NEWHALL  = 283;            /* person: Henry Mayo Newhall */
$ALSO_NAMED = 16312;        /* person: "Henry M. Newhall", reported only */
$FOLDER_PATH = 'legacy/';
$SONS = ['Henry Gregory Newhall', 'William Mayo Newhall', 'Edwin White Newhall', 'Walter Scott Newhall', 'George Almer Newhall'];

/* Filled in after reading the extract: line ranges [from, to) into each item's
   `lines`. 'body' => [] means the page does not transcribe the document.
   A key absent here is HELD. */
$SPLITS = [
    /* 'lw2182' => ['sourceLine' => null, 'body' => [], 'top' => [[3, 20]], 'bottom' => [[20, 22]]], */
];
/* Indexes into findings.lw3583_al1870 to quote in al1870's footnote. Empty holds al1870. */
$CHALLENGE = [];

$RECORDS = [
    'hs6501a' => ['section' => 'documents', 'images' => ['hs6501a', 'hs6501b'], 'also' => ['hs6501b'],
                  'date' => ['1865-12-23', 'day'], 'subject' => true, 'firmPaper' => true],
    'lw3583'  => ['section' => 'documents', 'images' => ['lw3583'], 'edtf' => '186X', 'printed' => 'n.d. (1860s)',
                  'mustSee' => '1870', 'subject' => true],
    'al1870'  => ['section' => 'documents', 'images' => ['al1870'], 'subject' => true, 'challenged' => true],
    'lw2182'  => ['section' => 'documents', 'images' => ['lw2182'], 'date' => ['1870-04-15', 'day'], 'subject' => true, 'firmPaper' => true],
    'al1873'  => ['section' => 'documents', 'images' => ['al1873'], 'date' => ['1873', 'year'], 'subject' => true, 'firmPaper' => true],
    'lw3756'  => ['section' => 'documents', 'images' => ['lw3756'], 'edtf' => '1867/1874', 'printed' => '1867-1874',
                  'mustSee' => '1867', 'subject' => true, 'firmPaper' => true],
    'rn7301'  => ['section' => 'photographs', 'images' => ['rn7301'], 'date' => ['1873', 'year']],
    'al1882'  => ['section' => 'documents', 'images' => ['al1882'], 'date' => ['1882-03-18', 'day'], 'subject' => true,
                  'extraDate' => ['1825-05-23', 'day', 'date of birth, as the obituary states it; the site\'s own footnote calls May 13, 1825 the commonly listed date']],
    'sfexaminer18820321hmn' => ['section' => 'documents', 'images' => ['sfexaminer18820321hmn'], 'date' => ['1882-03-21', 'day'], 'subject' => true],
];

if (!is_file($SRC)) { echo 'source extract missing: ' . $SRC . PHP_EOL . 'Run on the host first: python3 scripts/import/extract_newhall_sources.py' . PHP_EOL; return; }
$src = json_decode((string)file_get_contents($SRC), true);
$items = [];
foreach ($src['items'] as $it) { $items[$it['key']] = $it; }
$findings = $src['findings'] ?? [];

$elements = Craft::$app->getElements();
$svc = Craft::$app->getEntries();
$newhall = \craft\elements\Entry::find()->id($NEWHALL)->section('persons')->status(null)->one();
if (!$newhall || $newhall->title !== 'Henry Mayo Newhall') { echo 'REFUSING: #' . $NEWHALL . ' is not the persons record Henry Mayo Newhall' . PHP_EOL; return; }
$volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia');
$folder = $volume ? Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => $FOLDER_PATH]) : null;
if (!$folder) { echo 'asset folder archiveMedia/' . $FOLDER_PATH . ' not found' . PHP_EOL; return; }
$sons = [];
foreach ($SONS as $name) {
    $s = \craft\elements\Entry::find()->section('persons')->status(null)->title($name)->one();
    if ($s) { $sons[$name] = $s->id; }
}

/* --------------------------------------------------------------- helpers */

$slug = fn(string $s): string => \craft\helpers\StringHelper::toKebabCase(\craft\helpers\StringHelper::toAscii(trim($s, ". \t")));
$para = fn(array $lines): string => implode("\n\n", array_map('trim', $lines));
$legacy = fn(string $page): array => ['legacyUrl' => '/scvhistory/' . $page . '.htm', 'sourcePath' => 'https://scvhistory.com/scvhistory/' . $page . '.htm'];
$dateRow = fn(string $printed, string $iso, string $gran, string $label): array => [
    'printed' => $printed, 'iso' => substr(str_pad($iso, 10, '-01', STR_PAD_RIGHT), 0, 10) . ' 00:00:00', 'granularity' => $gran, 'label' => $label, 'confirmed' => false,
];
$take = function (array $lines, array $ranges): array {
    $out = [];
    foreach ($ranges as [$a, $b]) { $out = array_merge($out, array_slice($lines, $a, $b - $a)); }
    return $out;
};
/* The page title, less "SCVHistory.com CODE | Section | ". */
$titleOf = fn(string $t): string => rtrim(trim(preg_replace('~^.*\|\s*~', '', $t)), '.');
$readCredit = function (string $raw): array {
    $out = ['creditDpi' => '', 'creditProcess' => '', 'creditKind' => ''];
    $rest = trim(preg_replace('~^[A-Za-z_]+\d+[a-z]?\s*:\s*~', '', $raw), ' .');
    if (preg_match('~(\d{2,6})\s*dpi\b~i', $rest, $m)) { $out['creditDpi'] = $m[1]; }
    if (preg_match('~dpi\s+(\w+)\s+from\s+(.+)$~i', $rest, $m)) { $out['creditProcess'] = $m[1]; $out['creditKind'] = $m[2]; }
    return $out;
};

/* ------------------------------------------------------------------ plan */

$plan = []; $notes = [];
foreach ($RECORDS as $key => $cfg) {
    if (!isset($items[$key])) { $notes[] = "$key: not in the extract"; continue; }
    $it = $items[$key];
    $held = [];
    $printedDates = array_column($it['dates'], 'iso');
    $f = $legacy($key) + ['legacyKey' => $key];
    $rows = [];

    /* The date, only if the page prints it. */
    $edtf = ''; $printed = '';
    if (isset($cfg['date'])) {
        [$iso, $gran] = $cfg['date'];
        $hit = array_values(array_filter($it['dates'], fn($d) => $d['iso'] === $iso));
        if ($hit) {
            $edtf = $iso; $printed = $hit[0]['printed'];
            $rows[] = $dateRow($printed, $iso, $gran, 'as the legacy page prints it; not yet checked against the scan');
        } else { $notes[] = "$key: $iso is not printed on the page, so no date is set (dates found: " . implode(', ', array_unique($printedDates)) . ')'; }
    }
    if (isset($cfg['edtf'])) {
        if (in_array($cfg['mustSee'], array_map(fn($d) => substr($d, 0, 4), $printedDates), true)) { $edtf = $cfg['edtf']; $printed = $cfg['printed']; }
        else { $notes[] = "$key: the page does not print {$cfg['mustSee']}, so {$cfg['edtf']} is not set"; }
    }
    if (isset($cfg['extraDate'])) {
        [$iso, $gran, $label] = $cfg['extraDate'];
        $hit = array_values(array_filter($it['dates'], fn($d) => $d['iso'] === $iso));
        if ($hit) { $rows[] = $dateRow($hit[0]['printed'], $iso, $gran, $label); }
        else { $notes[] = "$key: $iso is not printed on the page; the birth-date row is not added"; }
    }

    /* The cut. */
    $split = $SPLITS[$key] ?? null;
    if ($split === null) { $held[] = 'text and framing not yet cut: add it to $SPLITS after reading the extract'; }
    $body = $split ? $para($take($it['lines'], $split['body'])) : '';
    $top = $split ? $para($take($it['lines'], $split['top'] ?? [])) : '';
    $bottom = $split ? $take($it['lines'], $split['bottom'] ?? []) : [];
    foreach ($it['scan_lines'] as $s) { if (!in_array($s, $bottom, true)) { $bottom[] = $s; } }
    $masthead = ($split && isset($split['sourceLine'])) ? ($it['lines'][$split['sourceLine']] ?? '') : '';

    $images = [];
    foreach ($cfg['images'] as $code) {
        $im = null;
        foreach ($items[$code]['images'] ?? [] as $x) { if (stripos(basename($x['file']), $code) === 0) { $im = $x; break; } }
        if (!$im) { $notes[] = "$key: no image for $code in the extract"; continue; }
        $images[] = ['code' => strtoupper($code), 'rel' => $im['master'] ?? $im['file']];
    }

    if ($cfg['section'] === 'photographs') {
        $scan = $it['scan_lines'][0] ?? '';
        $f += [
            'body' => $body,
            'photoSourceCode' => 'RN7301',
            'creditRaw' => $scan,
            'photoDate' => $printed, 'photoDateEdtf' => $edtf,
            'photoPeople' => array_values($sons),
            'recordDates' => $rows,
        ] + ($scan ? $readCredit($scan) : []);
        if (count($sons) < count($SONS)) { $notes[] = "$key: no person record for " . implode(', ', array_diff($SONS, array_keys($sons))) . '; photoPeople names only ' . (count($sons) ? implode(', ', array_keys($sons)) : 'nobody'); }
        $type = 'photograph';
    } else {
        $f += [
            'body' => $body,
            'sourceLine' => $masthead,
            'originalPublishDate' => $printed,
            'originalPublishDateEdtf' => $edtf,
            'webmasterNoteTop' => $top,
            'webmasterNoteBottom' => $para($bottom),
            'subjectPerson' => !empty($cfg['subject']) ? [$NEWHALL] : [],
            'recordDates' => $rows,
        ];
        $type = 'document';
    }
    if (!empty($cfg['firmPaper'])) { $notes[] = "$key: his firm's or railroad's paper; subject set to Newhall himself for want of an organization record (a decision for Nathan)"; }
    if (!empty($cfg['challenged'])) {
        $quote = array_values(array_intersect_key($findings['lw3583_al1870'] ?? [], array_flip($CHALLENGE)));
        if (!$quote) { $held[] = 'the lw3583 doubt about the signature is not yet quoted: set $CHALLENGE'; }
        $f['footnotes'] = $quote ? [['number' => '1', 'note' => 'The legacy page for another Newhall railroad document, lw3583, questions whether the Newhall signature on this one is genuine: "' . implode(' ', $quote) . '"', 'source' => 'editorial-2026']] : [];
        $f['footnotesOn'] = 'lw3583';   /* resolved to the record at apply time */
    }
    foreach ($cfg['also'] ?? [] as $b) { $notes[] = "$key: /scvhistory/$b.htm is the second view of this record; its legacy URL needs a redirect in config/redirects.php (not added here)"; }

    $title = $titleOf($it['title']);
    $plan[] = ['key' => $key, 'section' => $cfg['section'], 'type' => $type, 'title' => $title,
               'slug' => $slug($title) . (preg_match('~^\d{4}~', $edtf, $y) && !str_contains($title, $y[0]) ? '-' . $y[0] : ''),
               'fields' => $f, 'images' => $images, 'held' => $held];
}

/* ---------------------------------------------------------------- report */

foreach ($plan as &$p) {
    $type = $svc->getEntryTypeByHandle($p['type']);
    $layout = array_map(fn($x) => $x->handle, $type->getFieldLayout()->getCustomFields());
    $p['sectionId'] = $svc->getSectionByHandle($p['section'])->id; $p['typeId'] = $type->id;
    $p['absent'] = array_values(array_diff(array_keys($p['fields']), $layout));
    $p['existing'] = \craft\elements\Entry::find()->section($p['section'])->status(null)->legacyKey($p['key'])->one();
    foreach ($p['images'] as &$im) {
        $im['asset'] = \craft\elements\Asset::find()->volumeId($volume->id)->filename(basename($im['rel']))->one();
        $im['onMirror'] = is_file($MIRROR . $im['rel']);
    }
    unset($im);
}
unset($p);

foreach ($plan as $p) {
    echo PHP_EOL . ($p['existing'] ? 'EXISTS #' . $p['existing']->id . ' ' : ($p['held'] ? 'HELD      ' : 'NEW       ')) . $p['section'] . '  ' . $p['title'] . '   [' . $p['key'] . ']' . PHP_EOL;
    foreach ($p['held'] as $h) { echo '   HELD: ' . $h . PHP_EOL; }
    echo '   ' . str_pad('slug', 26) . $p['slug'] . PHP_EOL;
    foreach ($p['fields'] as $h => $v) {
        if ($v === '' || $v === []) { continue; }
        if (in_array($h, ['body', 'webmasterNoteTop', 'webmasterNoteBottom'], true)) { echo '   ' . str_pad($h, 26) . mb_strlen($v) . ' chars: ' . mb_substr(preg_replace('~\s+~', ' ', $v), 0, 70) . '...' . PHP_EOL; continue; }
        $show = is_array($v) ? json_encode($v, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : (string)$v;
        echo '   ' . str_pad($h, 26) . mb_substr(preg_replace('~\s+~', ' ', $show), 0, 110) . PHP_EOL;
    }
    foreach ($p['images'] as $i => $im) {
        echo '   ' . str_pad($i === 0 ? 'featuredImage' : 'recordImages', 26) . $im['rel']
            . ($im['asset'] ? '   held, asset #' . $im['asset']->id : ($im['onMirror'] ? '   to import from the mirror' : '   NOT ON THE MIRROR')) . PHP_EOL;
    }
    if ($p['absent']) { echo '   NOT ON THE LAYOUT, will be dropped: ' . implode(', ', $p['absent']) . PHP_EOL; }
}

echo PHP_EOL . str_repeat('-', 78) . PHP_EOL . 'NOTES' . PHP_EOL;
foreach ($notes as $n) { echo '   ' . $n . PHP_EOL; }
$also = \craft\elements\Entry::find()->id($ALSO_NAMED)->status(null)->one();
if ($also) { echo '   #' . $ALSO_NAMED . ' "' . $also->title . '" is a second persons record under his name; not touched here' . PHP_EOL; }
echo PHP_EOL . 'FINDINGS FROM THE EXTRACT, quoted whole' . PHP_EOL;
foreach (['al1882_arrival' => 'al1882 on his coming to California', 'al1882_birth' => 'al1882 on his birth', 'lw2182_arrival' => 'lw2182 essay (Leon Worden) on his arrival', 'hub_arrival' => 'the hub (Leon Worden) on his arrival', 'lw3583_al1870' => 'lw3583 on the al1870 signature, by index for $CHALLENGE'] as $k => $label) {
    echo '   -- ' . $label . PHP_EOL;
    foreach ($findings[$k] ?? [] as $i => $l) { echo '      [' . $i . '] ' . $l . PHP_EOL; }
    if (empty($findings[$k])) { echo '      (nothing found)' . PHP_EOL; }
}
echo '   -- the 1874 lead: ' . json_encode($findings['lead_1874'] ?? null, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

$ready = array_filter($plan, fn($p) => !$p['existing'] && !$p['held']);
$heldN = count(array_filter($plan, fn($p) => !$p['existing'] && $p['held']));
$missing = array_filter($plan, fn($p) => !$p['held'] && array_filter($p['images'], fn($im) => !$im['asset'] && !$im['onMirror']));
echo PHP_EOL . 'records ready to create: ' . count($ready) . ', held: ' . $heldN . ', already held: ' . count(array_filter($plan, fn($p) => $p['existing'])) . ' of ' . count($plan) . PHP_EOL;
echo 'scans missing from the mirror on ready records: ' . count($missing) . '. Nothing was invented for them.' . PHP_EOL;

if (!$APPLY) { echo PHP_EOL . str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($missing) { echo 'refusing: a scan is not on the mirror' . PHP_EOL; return; }
if (!$ready) { echo 'nothing ready; a second run is a no-op' . PHP_EOL; return; }

/* ----------------------------------------------------------------- apply */

$tmpDir = sys_get_temp_dir() . '/newhall-import';
@mkdir($tmpDir, 0775, true);
$made = []; $failed = [];
foreach ($plan as $p) { if ($p['existing']) { $made[$p['key']] = $p['existing']->id; } }
foreach ($ready as $p) {
    $ids = [];
    foreach ($p['images'] as $im) {
        if ($im['asset']) { $ids[] = $im['asset']->id; continue; }
        $fn = basename($im['rel']);
        $tmp = $tmpDir . '/' . $fn;
        if (!@copy($MIRROR . $im['rel'], $tmp)) { $failed[] = $fn . ': could not copy from the mirror'; continue 2; }
        $a = new \craft\elements\Asset();
        $a->tempFilePath = $tmp;
        $a->setFilename($fn);
        $a->newFolderId = $folder->id;
        $a->setVolumeId($volume->id);
        $a->setScenario(\craft\elements\Asset::SCENARIO_CREATE);
        $a->avoidFilenameConflicts = false;
        if (!$elements->saveElement($a)) { $failed[] = $fn . ': ' . json_encode($a->getErrors()); continue 2; }
        $fresh = \craft\elements\Asset::find()->id($a->id)->one();
        $fresh->setFieldValue('photoSourceCode', $im['code']);
        $fresh->setFieldValue('legacySourcePath', $im['rel']);
        $fresh->setFieldValue('provenanceKind', 'legacy-mirror');
        if (!$elements->saveElement($fresh)) { $failed[] = $fn . ': provenance ' . json_encode($fresh->getFirstErrors()); }
        $ids[] = $a->id;
        @unlink($tmp);
        echo 'imported ' . $fn . ' -> asset #' . $a->id . PHP_EOL;
    }
    $values = array_diff_key($p['fields'], array_flip($p['absent']));
    if (($values['footnotesOn'] ?? null) === 'lw3583') {
        if (!isset($made['lw3583'])) { $failed[] = $p['key'] . ': lw3583 has no record, so the challenge cannot point at it'; continue; }
        $values['footnotesOn'] = [$made['lw3583']];
    }
    $values = array_filter($values, fn($v) => $v !== '' && $v !== []);
    if ($ids) { $values['featuredImage'] = [$ids[0]]; }
    if (count($ids) > 1) { $values['recordImages'] = $ids; }
    $e = new \craft\elements\Entry();
    $e->sectionId = $p['sectionId'];
    $e->setTypeId($p['typeId']);
    $e->title = $p['title'];
    $e->slug = $p['slug'];
    $e->setFieldValues($values);
    if (!$elements->saveElement($e)) { $failed[] = $p['key'] . ': ' . json_encode($e->getErrors()); continue; }
    $made[$p['key']] = $e->id;
    echo 'created #' . $e->id . '  ' . $p['section'] . '  ' . $p['title'] . PHP_EOL;
}

/* ------------------------------------------------------------- read back */

$short = [];
foreach ($ready as $p) {
    $id = $made[$p['key']] ?? null;
    $e = $id ? \craft\elements\Entry::find()->id($id)->status(null)->one() : null;
    if (!$e) { $short[] = $p['key'] . ': no record'; continue; }
    foreach (array_diff_key($p['fields'], array_flip($p['absent'])) as $h => $v) {
        if ($v === '' || $v === []) { continue; }
        $got = $e->getFieldValue($h);
        if ($h === 'footnotesOn') {
            if ($got->status(null)->ids() != [$made['lw3583'] ?? -1]) { $short[] = $p['key'] . ': footnotesOn does not point at the lw3583 record'; }
        } elseif ($got instanceof \craft\elements\db\ElementQuery) {
            if (array_map('intval', $got->status(null)->ids()) != $v) { $short[] = $p['key'] . ': ' . $h . ' reads ' . json_encode($got->status(null)->ids()); }
        } elseif (in_array($h, ['recordDates', 'footnotes'], true)) {
            if (count((array)$got) !== count($v)) { $short[] = $p['key'] . ": $h has " . count((array)$got) . ' rows, not ' . count($v); }
        } elseif (trim((string)$got) !== trim((string)$v)) {
            $short[] = $p['key'] . ': ' . $h . ' reads back ' . mb_strlen(trim((string)$got)) . ' chars, not ' . mb_strlen(trim((string)$v));
        }
    }
    $imgs = $p['images'];
    $fi = $e->featuredImage->one();
    if ($imgs && !$fi) { $short[] = $p['key'] . ': no featuredImage'; }
    if ($fi && trim((string)$fi->getFieldValue('legacySourcePath')) !== $imgs[0]['rel']) { $short[] = $p['key'] . ': asset legacySourcePath reads "' . $fi->getFieldValue('legacySourcePath') . '"'; }
    if (count($imgs) > 1 && $e->recordImages->count() != count($imgs)) { $short[] = $p['key'] . ': recordImages holds ' . $e->recordImages->count() . ' of ' . count($imgs); }
}
$bad = array_merge($failed, $short);
echo PHP_EOL . 'READ-BACK ' . ($bad ? 'SHORT' : 'OK') . ': ' . (count($ready) - count($short)) . ' of ' . count($ready) . PHP_EOL;
foreach ($bad as $b) { echo '   FAIL ' . $b . PHP_EOL; }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('import_newhall_sources.php', count($ready), $bad ? 'SHORT: ' . implode('; ', $bad) : 'verified: ' . count($ready) . ' records and their scans read back',
    'Henry Mayo Newhall lifetime and 1882 sources; ' . $heldN . ' held');
if ($bad) { throw new \RuntimeException('import_newhall_sources: read-back failed'); }
