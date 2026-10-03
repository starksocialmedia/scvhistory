/**
 * READ ONLY. Does each page show every field its record holds?
 *
 * The rule (DEPLOY-RUNBOOK section 9, Nathan, 2 October 2026): a script that
 * writes a field a page must display is not done until the rendered page has
 * been checked for that field, not the record. Three times in one session a
 * value applied, read back and never rendered: person bodies (September), and
 * editor notes on persons, organizations and places, which only articles,
 * documents and photographs rendered (2 October). check_rendered_bodies.php
 * covers bodies; this covers every other field.
 *
 * HOW. Every field is classified in scripts/import/field-display.json:
 *   page      shown on the record's page: checked here
 *   schema    emitted only in the page's JSON-LD or meta, on purpose
 *   internal  bookkeeping, never shown, on purpose (the reason is recorded)
 *   gap       should be shown and is not yet: reported, not failed, until built
 * For each section, it picks the fewest records that between them hold a
 * value in every "page" field the section has, fetches each page once, and
 * looks for each value in the rendered text:
 *   text and dropdowns   the value (or the option's label), or for long text
 *                        a run of its first eight words
 *   tables               the first text cell of each row (up to three rows)
 *   relations            the related record's title (the first one)
 *   assets, switches     not checked (images are covered by the image checks)
 * A field's "mode" in the registry changes what is looked for: href (a link
 * target, found in the HTML rather than the text), number (printed with
 * thousands separators), list (the first item of a ; or , list), body (left
 * to check_rendered_bodies.php).
 * A value the page does not show is MISSING and fails. A field that holds data
 * and is not in the registry fails as UNCLASSIFIED, so a new field cannot be
 * added without saying where it shows. A field with a known reason a value
 * may not show has an "unless" note in the registry, and is reported, not
 * failed.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/check_rendered_fields.php'))"
 */

use craft\elements\Entry;

ini_set('memory_limit', '2048M');
$REG = json_decode((string)file_get_contents(\Craft::getAlias('@root') . '/scripts/import/field-display.json'), true) ?: [];
$fieldsOf = $REG['fields'] ?? [];
$skipTypes = ['Assets', 'Lightswitch', 'Matrix'];
$plain = fn(string $html) => strtolower(preg_replace('~\s+~u', ' ', html_entity_decode(strip_tags(preg_replace('~<(script|style)\b.*?</\1>~is', ' ', preg_replace('~<(sup|a class="nr")[^>]*>.*?</(sup|a)>~is', ' ', $html))), ENT_QUOTES | ENT_HTML5)));
$norm = fn(string $s) => strtolower(trim(preg_replace('~\s+~u', ' ', html_entity_decode(strip_tags(preg_replace('~\[\d+\]|\[/?lines\]~', ' ', $s)), ENT_QUOTES | ENT_HTML5))));
$probe = function (string $s) use ($norm): string {
    $s = $norm($s);
    if (mb_strlen($s) <= 70) { return $s; }
    return preg_match('~(?:[\p{L}\p{N}\'’.,-]+ ){7}[\p{L}\p{N}\'’-]+~u', $s, $m) ? $m[0] : mb_substr($s, 0, 60);
};
/* What to look for, per field value; [] when there is nothing to check. */
$needles = function ($f, $v) use ($probe, $skipTypes): array {
    $t = (new \ReflectionClass($f))->getShortName();
    if (in_array($t, $skipTypes, true) || $v === null) { return []; }
    if ($v instanceof \craft\elements\db\ElementQuery) { $el = $v->status(null)->one(); return $el && trim((string)$el->title) !== '' ? [$probe((string)$el->title)] : []; }
    if ($v instanceof \craft\fields\data\SingleOptionFieldData) { return (string)$v->value === '' ? [] : [$probe((string)($v->label ?: $v->value))]; }
    if (is_array($v)) {
        $out = [];
        foreach (array_slice(array_values(array_filter($v, 'is_array')), 0, 3) as $row) {
            foreach ($row as $k => $cell) { if (!preg_match('~^col\d+$~', (string)$k) && is_string($cell) && trim(strip_tags($cell)) !== '' && !preg_match('~^(top|bottom|inline|editorial-\d+|day|month|year|circa)$~', trim($cell))) { $out[] = $probe($cell); break; } }
        }
        return $out;
    }
    if (is_object($v) && !method_exists($v, '__toString')) { return []; }
    $s = trim((string)$v);
    return $s === '' ? [] : [$probe($s)];
};
$fails = []; $gaps = []; $unclassified = []; $excused = []; $pages = 0; $checks = 0;
foreach (Craft::$app->getEntries()->getAllSections() as $sec) {
    /* Which records hold which page fields; then the fewest records that cover them all. */
    $holds = []; $want = [];
    foreach (Entry::find()->section($sec->handle)->each(200) as $e) {
        if (!$e->url) { continue; }
        foreach ($e->getFieldLayout()->getCustomFields() as $f) {
            try { $v = $e->getFieldValue($f->handle); } catch (\Throwable $t) { continue; }
            if (!$needles($f, $v)) { continue; }
            $cls = $fieldsOf[$f->handle]['show'] ?? null;
            if ($cls === null) { $unclassified[$f->handle][$sec->handle] = true; continue; }
            if ($cls === 'gap') { $gaps[$f->handle][$sec->handle] = ($gaps[$f->handle][$sec->handle] ?? 0) + 1; continue; }
            if ($cls !== 'page') { continue; }
            if (in_array($sec->handle, $fieldsOf[$f->handle]['notOn'] ?? [], true)) { continue; }
            $holds[$e->id][] = $f->handle; $want[$f->handle] = true;
        }
    }
    $chosen = []; $left = $want;
    while ($left) {
        $best = null; $bestN = 0;
        foreach ($holds as $id => $hs) { $n = count(array_intersect_key(array_flip($hs), $left)); if ($n > $bestN) { $best = $id; $bestN = $n; } }
        if (!$best) { break; }
        $chosen[] = $best; foreach ($holds[$best] as $h) { unset($left[$h]); } unset($holds[$best]);
    }
    foreach ($chosen as $id) {
        $e = Entry::find()->id($id)->one();
        $ch = curl_init($e->url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_TIMEOUT => 40, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => 0]);
        $html = (string)curl_exec($ch); $st = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE); curl_close($ch); $pages++;
        if ($st !== 200) { $fails[] = "NO PAGE  {$sec->handle} #$id {$e->title}  status $st"; continue; }
        $text = $plain($html);
        foreach ($e->getFieldLayout()->getCustomFields() as $f) {
            if (($fieldsOf[$f->handle]['show'] ?? null) !== 'page' || in_array($sec->handle, $fieldsOf[$f->handle]['notOn'] ?? [], true)) { continue; }
            try { $v = $e->getFieldValue($f->handle); } catch (\Throwable $t) { continue; }
            $mode = $fieldsOf[$f->handle]['mode'] ?? 'text';
            if ($mode === 'body') { continue; }
            $ns = $needles($f, $v);
            if ($mode === 'href') { $u = trim((string)$v); $path = parse_url($u, PHP_URL_PATH) ?: $u; $ns = [str_contains($html, $u) || str_contains($html, htmlspecialchars($u)) || ($path !== '/' && str_contains($html, $path)) ? '' : $u]; }
            if ($mode === 'number' && is_numeric(trim((string)$v))) { $ns = [number_format((float)trim((string)$v))]; }
            if ($mode === 'evidence') { $W = ['certified' => "the body's own record", 'contemporary' => 'reported at the time', 'retrospective' => 'recalled later', 'roster' => 'from an undated roster', 'derived' => 'read from the count', 'uncited' => 'not yet sourced']; $ns = [isset($W[(string)($v->value ?? '')]) ? strtolower($W[(string)$v->value]) : '']; }
            if ($mode === 'list') { $ns = [$probe(trim(preg_split('~[;,]~', (string)$v)[0]))]; }
            /* On a sourced record (war memorials), a field's value may show in its sourced form, as the fact row that says the same thing
               ("Hart High School, class of 1967" for "Hart High School (class of 1967)"). The same test as the template: the opening words of
               one in the other. */
            $nz = fn($x) => trim(preg_replace('~\s+~', ' ', str_replace(['(', ')', ',', '.', ';'], '', mb_strtolower(strip_tags((string)$x)))));
            $factVals = $e->getFieldLayout()->getFieldByHandle('factSources') ? array_map(fn($r) => $nz($r['value'] ?? ''), array_filter($e->factSources ?? [], 'is_array')) : [];
            foreach ($ns as $n) {
                $checks++;
                if ($n === '' || str_contains($text, $n)) { continue; }
                if ($factVals && is_scalar($v) || (is_object($v) && method_exists($v, '__toString'))) { $fv = $nz((string)$v); $hit = false; foreach ($factVals as $xv) { if ($xv !== '' && (str_contains($xv, mb_substr($fv, 0, 16)) || str_contains($fv, mb_substr($xv, 0, 16))) && str_contains($nz($text), mb_substr($xv, 0, 16))) { $hit = true; break; } } if ($hit) { continue; } }
                $line = "{$sec->handle}.{$f->handle}  #$id {$e->title}  \"" . mb_substr($n, 0, 60) . '"';
                if (!empty($fieldsOf[$f->handle]['unless'])) { $excused[] = $line . '  (' . $fieldsOf[$f->handle]['unless'] . ')'; } else { $fails[] = 'MISSING  ' . $line; }
                break;
            }
        }
    }
}
/* Assets have their own page, /media/<id>. The fields marked "on": "media" are looked for there, on the fewest assets that hold them all. */
$mediaFields = array_keys(array_filter($fieldsOf, fn($v) => ($v['on'] ?? '') === 'media' && ($v['show'] ?? '') === 'page'));
$left = array_flip($mediaFields); $base = rtrim(\craft\helpers\UrlHelper::siteUrl(), '/');
foreach (\craft\elements\Asset::find()->each(200) as $a) {
    if (!$left) { break; }
    $lay = $a->getFieldLayout(); if (!$lay) { continue; }
    /* A body's current mark has no /media page: its address goes to the record, where its source and date show (3 October 2026). */
    if ($lay->getFieldByHandle('assetRole') && (string)($a->getFieldValue('assetRole')->value ?? '') === 'current-mark') { continue; }
    $has = [];
    foreach (array_keys($left) as $h) { if (!$lay->getFieldByHandle($h)) { continue; } try { $v = $a->getFieldValue($h); } catch (\Throwable $t) { continue; } if ($v instanceof \craft\fields\data\SingleOptionFieldData ? (string)$v->value !== '' : ($v instanceof \DateTimeInterface || trim((string)$v) !== '')) { $has[$h] = $v; } }
    if (!$has) { continue; }
    $ch = curl_init("$base/media/{$a->id}"); curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 40, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => 0]);
    $html = (string)curl_exec($ch); curl_close($ch); $pages++; $text = $plain($html);
    foreach ($has as $h => $v) {
        $mode = $fieldsOf[$h]['mode'] ?? 'text'; $checks++;
        if ($v instanceof \craft\fields\data\SingleOptionFieldData) { $n = $probe((string)$v->label); }
        elseif ($v instanceof \DateTimeInterface) { $n = strtolower($v->format('F j, Y')); }
        elseif ($mode === 'date') { $n = strtolower(date('F j, Y', strtotime((string)$v))); }
        elseif ($mode === 'href') { $n = str_contains($html, trim((string)$v)) ? '' : trim((string)$v); }
        elseif ($mode === 'first-line') { $n = $probe(explode(',', strtok((string)$v, "\n"))[0]); }
        else { $n = $probe((string)$v); }
        if ($n !== '' && !str_contains($text, $n)) { $fails[] = "MISSING  media.$h  asset #{$a->id} {$a->filename}  \"" . mb_substr($n, 0, 60) . '"'; }
        unset($left[$h]);
    }
}

foreach ($unclassified as $h => $secs) { $fails[] = "UNCLASSIFIED  $h holds data on " . implode(', ', array_keys($secs)) . ': add it to scripts/import/field-display.json'; }
echo "$pages pages fetched, $checks values looked for" . PHP_EOL;
if ($gaps) { echo 'GAPS (known, should show, not built yet): ' . implode('; ', array_map(fn($h, $s) => "$h on " . implode(', ', array_map(fn($k, $n) => "$k ($n)", array_keys($s), $s)), array_keys($gaps), $gaps)) . PHP_EOL; }
if ($excused) { echo 'NOT SHOWN, WITH A REASON (' . count($excused) . '):' . PHP_EOL . '  ' . implode(PHP_EOL . '  ', $excused) . PHP_EOL; }
echo ($fails ? count($fails) . ' FAILURES' . PHP_EOL . implode(PHP_EOL, $fails) : 'every page field that holds data shows on its page') . PHP_EOL;
return ['ok' => !$fails, 'fails' => $fails, 'gaps' => $gaps];
