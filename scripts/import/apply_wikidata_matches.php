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
 * Idempotent. A second run finds every field filled and writes nothing.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/apply_wikidata_matches.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

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
$conflicts = []; $missingField = []; $missingRecord = [];

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

echo str_repeat('=', 76) . PHP_EOL;
echo 'matches in the file:        ' . count($data['matches'] ?? []) . PHP_EOL;
echo 'records that would change:  ' . $recordsTouched . PHP_EOL;
echo 'fields that would be set:   ' . $wouldSet . PHP_EOL;
echo 'fields already correct:     ' . $alreadyDone . PHP_EOL;

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
