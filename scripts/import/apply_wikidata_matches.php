/**
 * Writes the confirmed Wikidata reconciliation into the authority fields.
 *
 * inventory/legacy/wikidata-matches.json is Grok's pass over the export: 30
 * matches it will stand behind, 18 near-misses it will not, and 14 it could
 * find nothing for. Only the confirmed matches are written here. The near-
 * misses are printed at the end with what corroborated and what failed, because
 * "probably the same Antonio del Valle" is a judgement for Nathan to make one
 * at a time, not a thing to apply in bulk.
 *
 * Per record it sets, only where the field is empty:
 *   wikidataId            from the Q-number
 *   gnisId                from a GNIS sitelink, on places
 *   viafId                from a VIAF sitelink, the bare identifier
 *   personWikipediaUrl    from a Wikipedia sitelink
 *   personGraveUrl        from a Find A Grave sitelink
 *
 * A non-empty field is never overwritten. Where a record already holds a value
 * that differs from the match, the difference is reported and the record is
 * left alone, so a hand-checked identifier always beats a reconciled one.
 *
 * Two further things it does, both reported in the dry run rather than silent:
 *
 *   $FORCE_GRAVE, off by default, replaces personGraveUrl on eight records where
 *   the stored URL resolves to a different person entirely. Find A Grave goes by
 *   the memorial number and ignores the slug, so
 *   /memorial/1819/christopher-houston-carson serves Harry Chapin's grave. Both
 *   ends of all eight were opened in a browser and are recorded in $GRAVE_FIX,
 *   so the correction can be read without opening anything. It touches those
 *   eight rows only, and only while the stored URL is still the one checked.
 *
 *   Two values that are broken rather than disputed are corrected: John C.
 *   Frémont's birthDate, which holds his own name, and Juan Bandini's
 *   burialPlace, which is missing its leading E. Each is written only while the
 *   field still holds exactly the broken value.
 *
 * Three burial places where the record and Find A Grave name different
 * cemeteries are reported as needing research and never written. Some of those
 * may be reinterments, which is a question of fact, not a broken link.
 *
 * Idempotent. A second run finds every field filled and writes nothing.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/apply_wikidata_matches.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

/* The one case where a value already in the record loses. Eight personGraveUrl
   values point at a different person entirely: Find A Grave resolves by the
   memorial number and ignores the slug, so a URL reading
   /memorial/1819/christopher-houston-carson serves Harry Chapin's grave. Every
   one of the eight was opened in a browser and both ends recorded below, so the
   correction can be audited without opening anything.

   Off by default, and it only ever touches the eight rows in $GRAVE_FIX: it is
   not a general "trust Wikidata" switch. */
$FORCE_GRAVE = false;
if ($FORCE_GRAVE) { echo 'FORCE_GRAVE IS ON, eight personGraveUrl values will be replaced' . PHP_EOL; }

/* id => [what the stored URL actually serves, what the replacement serves].
   Read from findagrave.com on 2026-09-18 in a browser session, because curl is
   answered with a Cloudflare challenge. */
$GRAVE_FIX = [
    315 => ['Christopher Houston Carson',
            'https://www.findagrave.com/memorial/1819/christopher-houston-carson',
            'Harry Chapin, 1942 to 1981, Huntington Rural Cemetery, New York',
            'https://www.findagrave.com/memorial/177',
            'Kit Carson, 24 Dec 1809 to 23 May 1868, Kit Carson Memorial Cemetery, Taos'],
    323 => ['Cave Johnson Couts',
            'https://www.findagrave.com/memorial/9372/cave-johnson-couts',
            'Davison Alexander Dalziel, 1852 to 1928, Highgate Cemetery East, London',
            'https://www.findagrave.com/memorial/6156421',
            'Cave Johnson Couts, 11 Nov 1821 to 10 Jun 1874, Calvary Cemetery, San Diego'],
    327 => ['Edward Fitzgerald Beale',
            'https://www.findagrave.com/memorial/5144/edward-fitzgerald-beale',
            'Simon Bolivar Buckner Sr., 1823 to 1914, Frankfort Cemetery, Kentucky',
            'https://www.findagrave.com/memorial/11558486',
            'Edward Fitzgerald "Ned" Beale, 4 Feb 1822 to 22 Apr 1893, Chester Rural Cemetery'],
    313 => ['Edwin Bryant',
            'https://www.findagrave.com/memorial/8906/edwin-bryant',
            'James Francis Edward Stuart, 1688 to 1766, Saint Peter\'s Basilica',
            'https://www.findagrave.com/memorial/30145642',
            'Edwin Bryant, 21 Aug 1805 to 16 Dec 1869, Spring Grove Cemetery, Cincinnati'],
    319 => ['James Wilson Marshall',
            'https://www.findagrave.com/memorial/1144/james-wilson-marshall',
            'Gen. George S. Patton, 1885 to 1945, Luxembourg American Cemetery',
            'https://www.findagrave.com/memorial/6649',
            'James Wilson Marshall, 8 Oct 1810 to 10 Aug 1885, James Marshall Monument, Coloma'],
    307 => ["John C. Fr\u{00E9}mont",
            'https://www.findagrave.com/memorial/834/john-charles-fremont',
            'Dick Powell, 1904 to 1963, Forest Lawn Memorial Park, Glendale',
            'https://www.findagrave.com/memorial/2615',
            'John Charles Fremont, 21 Jan 1813 to 13 Jul 1890, Rockland Cemetery, Sparkill'],
    325 => ['Juan Bandini',
            'https://www.findagrave.com/memorial/9366/juan-bandini',
            'Mary Scott Hogarth, 1819 to 1837, Kensal Green Cemetery, London',
            'https://www.findagrave.com/memorial/11163474',
            'Juan Lorenzo Bruno Bandini, 4 Oct 1800 to 5 Nov 1859, Calvary Cemetery, East Los Angeles'],
    321 => ['William Lewis Manly',
            'https://www.findagrave.com/memorial/1979/william-lewis-manly',
            'Queen Anne, 1665 to 1714, Westminster Abbey',
            'https://www.findagrave.com/memorial/18522962',
            'William Lewis Manly, 6 Apr 1820 to 5 Feb 1903, Woodbridge Masonic Cemetery'],
];

/* Two values that are plainly wrong rather than merely disputed. Each is only
   written where the field still holds exactly the broken value, so this cannot
   overwrite a correction made by hand in the meantime. */
$FIELD_FIX = [
    [307, 'birthDate', "John C. Fr\u{00E9}mont", 'January 21, 1813',
        'the birthDate field holds the person\'s own name'],
    [325, 'burialPlace', 'l Campo Santo Cemetery, San Diego, California',
        'El Campo Santo Cemetery, San Diego, California',
        'the leading E is missing'],
];

/* Burial places where the record and Find A Grave disagree on the cemetery
   rather than on the link. Questions of fact, and some may be reinterments.
   Reported so they are not forgotten; never written. */
$NEEDS_RESEARCH = [
    ['Edward Fitzgerald Beale', 'Rock Creek Cemetery, Washington, D.C.', 'Chester Rural Cemetery, Chester'],
    ['Edwin Bryant', 'Cave Hill Cemetery, Louisville, Kentucky', 'Spring Grove Cemetery, Cincinnati'],
    ['William Lewis Manly', 'Oak Hill Cemetery, San Jose, California', 'Woodbridge Masonic Cemetery, Woodbridge'],
];

$root = \Craft::getAlias('@root');
$path = $root . '/inventory/legacy/wikidata-matches.json';

if (!file_exists($path)) { echo 'not found: ' . $path . PHP_EOL; return; }
$data = json_decode(file_get_contents($path), true);
if (!is_array($data)) { echo 'could not parse ' . $path . PHP_EOL; return; }

$elements = Craft::$app->getElements();

$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

/* A VIAF sitelink arrives as a URL; the field wants the bare identifier. */
$viafId = function (string $url): string {
    return preg_match('~viaf\.org/viaf/(\d+)~', $url, $m) ? $m[1] : '';
};

/* Which sitelink feeds which handle, per kind. A place has no grave. */
$MAP = [
    'person' => [
        'wikipedia'    => 'personWikipediaUrl',
        'find_a_grave' => 'personGraveUrl',
    ],
    'place' => [
        'wikipedia' => 'placeWikipediaUrl',
    ],
];

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo 'source: inventory/legacy/wikidata-matches.json, generated '
    . ($data['meta']['generated'] ?? 'unknown') . PHP_EOL;
echo str_repeat('=', 76) . PHP_EOL;

$wouldSet = 0; $recordsTouched = 0; $alreadyDone = 0;
$conflicts = []; $missingField = []; $missingRecord = []; $graveFixes = [];

foreach (($data['matches'] ?? []) as $m) {
    $id   = (int)($m['id'] ?? 0);
    $kind = (string)($m['kind'] ?? '');
    $qid  = trim((string)($m['qid'] ?? ''));
    $sl   = $m['sitelinks'] ?? [];

    $e = $id ? \craft\elements\Entry::find()->id($id)->status(null)->one() : null;
    if (!$e) { $missingRecord[] = ($m['title'] ?? '?') . ' (#' . $id . ')'; continue; }

    /* What this match offers, handle => value. */
    $offers = [];
    if ($qid !== '') { $offers['wikidataId'] = $qid; }
    if (!empty($sl['gnis']))  { $offers['gnisId'] = trim((string)$sl['gnis']); }
    if (!empty($sl['viaf']))  {
        $v = $viafId((string)$sl['viaf']);
        if ($v !== '') { $offers['viafId'] = $v; }
    }
    foreach (($MAP[$kind] ?? []) as $key => $handle) {
        if (!empty($sl[$key])) { $offers[$handle] = trim((string)$sl[$key]); }
    }

    $set = []; $kept = []; $absent = [];
    foreach ($offers as $handle => $value) {
        if (!$hasField($e, $handle)) { $absent[] = $handle; $missingField[$handle] = true; continue; }
        $current = '';
        try { $current = trim((string)$e->getFieldValue($handle)); } catch (\Throwable $ex) { $current = ''; }
        if ($current === '') { $set[$handle] = $value; continue; }
        if ($current === $value) { $alreadyDone++; continue; }

        /* The eight verified cases, where the value already in the record is the
           wrong man's grave. Only these rows, only this handle, and only while
           the stored URL is still the one that was checked. */
        if ($handle === 'personGraveUrl' && isset($GRAVE_FIX[$e->id])
            && $current === $GRAVE_FIX[$e->id][1]) {
            $graveFixes[] = [$e, $GRAVE_FIX[$e->id]];
            if ($FORCE_GRAVE) { $set[$handle] = $GRAVE_FIX[$e->id][3]; }
            continue;
        }
        $kept[] = $handle;
        /* A Find A Grave URL carries the memorial number before the slug. Two
           URLs with the same number are the same grave written two ways and do
           not matter; two different numbers mean one of them points at the
           wrong person, which does. */
        $gid = function (string $u): string {
            return preg_match('~findagrave\.com/memorial/(\d+)~', $u, $mm) ? $mm[1] : '';
        };
        $severity = 'differs';
        if ($handle === 'personGraveUrl' || $handle === 'mpFindAGraveUrl' || $handle === 'obitGraveUrl') {
            $a1 = $gid($current); $b1 = $gid($value);
            $severity = ($a1 !== '' && $a1 === $b1) ? 'same grave, different spelling of the URL'
                : 'DIFFERENT MEMORIAL, one of these is the wrong grave';
        }
        $conflicts[] = str_pad($severity, 52) . $e->title . '  ' . $handle
            . PHP_EOL . '      ours:  ' . $current . PHP_EOL . '      match: ' . $value;
    }

    if (!$set && !$kept && !$absent) { continue; }

    echo str_pad($e->section->handle, 8) . str_pad($e->title, 32)
        . $qid . '  ' . ($m['confidence'] ?? '') . PHP_EOL;
    echo '   ' . ($m['wikidata_label'] ?? '') . ' — ' . ($m['wikidata_description'] ?? '') . PHP_EOL;
    foreach (($m['corroborating_facts'] ?? []) as $f) {
        echo '   on ' . str_pad((string)($f['note'] ?? $f['property'] ?? ''), 18)
            . 'wikidata "' . ($f['wikidata_value'] ?? '') . '"  ours "' . ($f['our_value'] ?? '') . '"' . PHP_EOL;
    }
    foreach ($set as $handle => $value) { echo '   SET  ' . str_pad($handle, 20) . $value . PHP_EOL; }
    foreach ($kept as $handle) { echo '   KEEP ' . str_pad($handle, 20) . 'already has a different value, left alone' . PHP_EOL; }
    foreach ($absent as $handle) { echo '   SKIP ' . str_pad($handle, 20) . 'no such field on this entry type' . PHP_EOL; }

    if ($set) {
        $wouldSet += count($set);
        $recordsTouched++;
        if ($APPLY) {
            foreach ($set as $handle => $value) {
                try { $e->setFieldValue($handle, $value); }
                catch (\Throwable $ex) { echo '   set ' . $handle . ' failed: ' . $ex->getMessage() . PHP_EOL; }
            }
            echo '   ' . ($elements->saveElement($e) ? 'saved' : 'SAVE FAILED: ' . json_encode($e->getErrors())) . PHP_EOL;
        }
    }
    echo str_repeat('-', 76) . PHP_EOL;
}

/* ------------------------------------------------- the two plainly wrong values */

echo str_repeat('=', 76) . PHP_EOL;
echo 'FIELD CORRECTIONS, not from Wikidata. Two values that are broken rather than' . PHP_EOL;
echo 'disputed, written only while the field still holds exactly the broken value.' . PHP_EOL;
echo str_repeat('=', 76) . PHP_EOL;

$fieldFixed = 0;
foreach ($FIELD_FIX as [$id, $handle, $wasValue, $nowValue, $why]) {
    $e = \craft\elements\Entry::find()->id($id)->status(null)->one();
    if (!$e) { echo '#' . $id . ' not found' . PHP_EOL; continue; }
    if (!$hasField($e, $handle)) { echo $e->title . ': no ' . $handle . ' field' . PHP_EOL; continue; }

    $current = '';
    try { $current = trim((string)$e->getFieldValue($handle)); } catch (\Throwable $ex) {}

    echo $e->title . '  ' . $handle . PHP_EOL;
    echo '   why:  ' . $why . PHP_EOL;
    if ($current === $nowValue) { echo '   already corrected, nothing to do' . PHP_EOL . PHP_EOL; continue; }
    if ($current !== $wasValue) {
        echo '   SKIP: holds "' . $current . '", which is neither the broken value nor the fix.' . PHP_EOL;
        echo '         Someone has changed it; decide this one by hand.' . PHP_EOL . PHP_EOL;
        continue;
    }
    echo '   was:  "' . $current . '"' . PHP_EOL;
    echo '   now:  "' . $nowValue . '"' . PHP_EOL;
    $fieldFixed++;
    if ($APPLY) {
        try { $e->setFieldValue($handle, $nowValue); }
        catch (\Throwable $ex) { echo '   set failed: ' . $ex->getMessage() . PHP_EOL; }
        echo '   ' . ($elements->saveElement($e) ? 'saved' : 'SAVE FAILED: ' . json_encode($e->getErrors())) . PHP_EOL;
    }
    echo PHP_EOL;
}

echo str_repeat('=', 76) . PHP_EOL;
echo 'matches in the file:        ' . count($data['matches'] ?? []) . PHP_EOL;
echo 'records that would change:  ' . $recordsTouched . PHP_EOL;
echo 'fields that would be set:   ' . $wouldSet . PHP_EOL;
echo 'fields already correct:     ' . $alreadyDone . PHP_EOL;
echo 'broken values corrected:    ' . $fieldFixed . PHP_EOL;

if ($graveFixes) {
    echo PHP_EOL . str_repeat('=', 76) . PHP_EOL;
    echo 'GRAVE LINKS POINTING AT THE WRONG PERSON, ' . count($graveFixes) . '.' . PHP_EOL;
    echo 'Find A Grave resolves by the memorial number and ignores the slug, so a URL' . PHP_EOL;
    echo 'reading the right name can serve anyone. Both ends were opened and recorded.' . PHP_EOL;
    echo ($FORCE_GRAVE ? 'FORCE_GRAVE is on: these are being replaced.'
        : 'FORCE_GRAVE is off: nothing below is written. Set it to true to replace them.') . PHP_EOL;
    echo str_repeat('=', 76) . PHP_EOL;
    foreach ($graveFixes as [$e, $fix]) {
        echo PHP_EOL . $e->title . '  (#' . $e->id . ')' . PHP_EOL;
        echo '   stored: ' . $fix[1] . PHP_EOL;
        echo '           serves ' . $fix[2] . PHP_EOL;
        echo '   fix:    ' . $fix[3] . PHP_EOL;
        echo '           serves ' . $fix[4] . PHP_EOL;
    }
}

if ($NEEDS_RESEARCH) {
    echo PHP_EOL . str_repeat('=', 76) . PHP_EOL;
    echo 'BURIAL PLACES NEEDING RESEARCH, ' . count($NEEDS_RESEARCH) . '. Never written by this script.' . PHP_EOL;
    echo 'The link is not the problem here; the two sources name different cemeteries.' . PHP_EOL;
    echo 'Some of these may be reinterments rather than errors.' . PHP_EOL;
    echo str_repeat('=', 76) . PHP_EOL;
    foreach ($NEEDS_RESEARCH as [$who, $ours, $theirs]) {
        echo PHP_EOL . $who . PHP_EOL;
        echo '   ours:        ' . $ours . PHP_EOL;
        echo '   find a grave: ' . $theirs . PHP_EOL;
    }
}

if ($conflicts) {
    echo PHP_EOL . 'fields left alone because they already hold something different.' . PHP_EOL;
    echo 'A value already in the record always wins; this is a list to read, not a problem' . PHP_EOL;
    echo 'the script should solve.' . PHP_EOL;
    sort($conflicts);
    foreach ($conflicts as $c) { echo '  ' . $c . PHP_EOL; }
}
if ($missingField) {
    echo PHP_EOL . 'handles the match offered that no entry type carries yet:' . PHP_EOL;
    foreach (array_keys($missingField) as $h) {
        echo '  ' . $h . ($h === 'gnisId' ? '  run add_gnis_field.php first' : '') . PHP_EOL;
    }
}
if ($missingRecord) {
    echo PHP_EOL . 'matches whose record is gone:' . PHP_EOL;
    foreach ($missingRecord as $r) { echo '  ' . $r . PHP_EOL; }
}

/* --------------------------------------------------- near misses, for reading */

$near = $data['near_misses'] ?? [];
if ($near) {
    echo PHP_EOL . str_repeat('=', 76) . PHP_EOL;
    echo 'NEAR MISSES, ' . count($near) . '. Nothing below is written by this script.' . PHP_EOL;
    echo 'Each is one judgement. Decide them individually and add the identifier by hand,' . PHP_EOL;
    echo 'or add it to the matches list and re-run.' . PHP_EOL;
    echo str_repeat('=', 76) . PHP_EOL;
    foreach ($near as $n) {
        echo PHP_EOL . str_pad((string)($n['kind'] ?? ''), 8) . ($n['title'] ?? '') . '  (#' . ($n['id'] ?? '') . ')' . PHP_EOL;
        foreach (($n['candidates'] ?? []) as $c) {
            echo '   ' . ($c['candidate_qid'] ?? '') . '  ' . ($c['candidate_label'] ?? '')
                . ' — ' . ($c['wikidata_description'] ?? '') . PHP_EOL;
            foreach (($c['corroborating_facts'] ?? []) as $f) {
                echo '      for: ' . str_pad((string)($f['note'] ?? ''), 18)
                    . 'wikidata "' . ($f['wikidata_value'] ?? '') . '"  ours "' . ($f['our_value'] ?? '') . '"' . PHP_EOL;
            }
            if (!empty($c['failed'])) { echo '      against: ' . $c['failed'] . PHP_EOL; }
        }
        if (!empty($n['failed'])) { echo '   against: ' . $n['failed'] . PHP_EOL; }
    }
}

$un = $data['unmatched'] ?? [];
if ($un) {
    echo PHP_EOL . 'no candidate at all, ' . count($un) . ':' . PHP_EOL;
    foreach ($un as $u) { echo '  ' . str_pad((string)($u['title'] ?? ''), 34) . ($u['failed'] ?? $u['status'] ?? '') . PHP_EOL; }
}

echo PHP_EOL . ($APPLY ? 'applied.' : 'nothing written.') . PHP_EOL;
