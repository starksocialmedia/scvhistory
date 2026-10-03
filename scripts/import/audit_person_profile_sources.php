/**
 * READ ONLY. What the archive already holds for each person whose page shows
 * no biography (Nathan, 2 October 2026: "Tell me how many could be written
 * from sources already in the archive, with no new research").
 *
 * For every live person whose prose does not publish (bodyAuthorship not
 * legacy-leon or editorial-2026, or no body at all):
 *   links      entries pointing at the record, by section (bylines, subjects,
 *              photographs, obituaries, office and candidacy records)
 *   mentions   texts in the archive naming them (title, fullName or an alias),
 *              and the words in the sentences that name them: the raw material
 *              a profile would be written from
 *   copied     for withheld WordPress text: how many of its sentences share an
 *              eight-word run with a text already in the archive, and which
 *              text. A sentence found there can be cited instead of rewritten.
 * The corpus is every non-person body that publishes (articles, documents,
 * obituaries, collections, photographs with captions, places, organizations,
 * events, groups, war memorials) plus person bodies that publish.
 * Writes inventory/review/person-profile-sources-2026-10-02.md and a JSON copy.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/audit_person_profile_sources.php'))"
 */

use craft\elements\Entry;

ini_set('memory_limit', '3072M');
$norm = fn($s) => trim(preg_replace('~\s+~u', ' ', html_entity_decode(strip_tags(str_replace(['</p>', '<br>', '<br/>', '<br />'], ' ', (string)$s)), ENT_QUOTES)));
$fieldsOf = function ($e) { $h = []; foreach ($e->getFieldLayout()->getCustomFields() as $f) { $h[$f->handle] = true; } return $h; };
$val = function ($e, $h, $k) { if (!isset($h[$k])) { return ''; } try { $v = $e->getFieldValue($k); return is_object($v) && property_exists($v, 'value') ? (string)$v->value : (string)$v; } catch (\Throwable $t) { return ''; } };
$PUB = ['legacy-leon', 'editorial-2026'];

/* The people. */
$targets = [];
foreach (Entry::find()->section('persons')->each(100) as $p) {
    $h = $fieldsOf($p);
    $auth = $val($p, $h, 'bodyAuthorship');
    $body = $norm($val($p, $h, 'body') ?: $val($p, $h, 'authorBio'));
    if ($body !== '' && in_array($auth, $PUB, true)) { continue; }
    $names = [$p->title];
    if ($fn = trim($val($p, $h, 'fullName'))) { $names[] = $fn; }
    foreach (preg_split('~[;\n|]~', $val($p, $h, 'personAliases')) as $a) { if (mb_strlen(trim($a)) > 5) { $names[] = trim($a); } }
    /* "Jo Anne Darcy" also appears as "JoAnne Darcy"; "Carl Boyer III" as "Carl Boyer". Strip suffixes and parentheses. */
    foreach ($names as $n) { $s = trim(preg_replace(['~\([^)]*\)~', '~,?\s+(Jr\.?|Sr\.?|III|II|IV)$~', '~\s+[A-Z]\.\s+~'], ['', '', ' '], $n)); if ($s !== $n && mb_strlen($s) > 5) { $names[] = $s; } }
    $names = array_values(array_unique(array_filter(array_map('trim', $names))));
    $targets[$p->id] = ['id' => $p->id, 'title' => $p->title, 'slug' => $p->slug, 'auth' => $body === '' ? 'empty' : ($auth ?: 'unclassified'), 'words' => $body === '' ? 0 : str_word_count($body), 'body' => $body, 'names' => $names, 'links' => [], 'inbound' => 0, 'mentions' => [], 'sentWords' => 0, 'copied' => 0, 'sentences' => 0, 'copiedFrom' => [],
        'dates' => array_filter(['born' => $val($p, $h, 'birthDate'), 'died' => $val($p, $h, 'deathDate'), 'occupation' => $val($p, $h, 'occupation')])];
}
echo count($targets) . " people with no published biography\n";

/* Inbound links by section. */
foreach ($targets as $id => &$t) {
    foreach (Entry::find()->status(null)->relatedTo(['targetElement' => $id])->all() as $r) {
        if ($r->section->handle === 'persons') { continue; }
        $t['links'][$r->section->handle] = ($t['links'][$r->section->handle] ?? 0) + 1; $t['inbound']++;
    }
    /* Bylines separately: a byline says they wrote, not who they were. */
    $t['bylines'] = Entry::find()->section(['articles', 'collections'])->relatedTo(['targetElement' => $id, 'field' => ['writtenBy', 'editedBy']])->count();
}
unset($t);

/* Shingles of the withheld text, eight words, to find copies. */
$sh = function ($s) { $w = preg_split('~\W+~u', mb_strtolower($s), -1, PREG_SPLIT_NO_EMPTY); $o = []; for ($i = 0; $i + 8 <= count($w); $i++) { $o[] = implode(' ', array_slice($w, $i, 8)); } return $o; };
$want = [];
foreach ($targets as $id => &$t) {
    if ($t['body'] === '') { continue; }
    $t['sent'] = array_values(array_filter(preg_split('~(?<=[.!?])\s+(?=[A-Z"])~u', $t['body']), fn($s) => str_word_count($s) >= 8));
    $t['sentences'] = count($t['sent']);
    foreach ($t['sent'] as $si => $s) { foreach ($sh($s) as $g) { $want[$g][] = [$id, $si]; } }
}
unset($t);
$hit = [];

/* The corpus. */
$pats = []; foreach ($targets as $id => $t) { foreach ($t['names'] as $n) { $pats[$id][] = '~\b' . preg_quote($n, '~') . '\b~iu'; } }
$scanned = 0;
foreach (['articles', 'documents', 'obituaries', 'collections', 'photographs', 'places', 'organizations', 'events', 'groups', 'warMemorials', 'persons'] as $sec) {
    foreach (Entry::find()->section($sec)->each(200) as $e) {
        $h = $fieldsOf($e);
        if ($sec === 'persons' && !in_array($val($e, $h, 'bodyAuthorship'), $PUB, true)) { continue; }
        $text = $norm($val($e, $h, 'body') . ' ' . $val($e, $h, 'wmNarrative') . ' ' . $val($e, $h, 'photoCaptionExt'));
        if (str_word_count($text) < 5) { continue; }
        $scanned++;
        $label = "$sec #{$e->id} {$e->title}";
        foreach ($pats as $id => $ps) {
            if ($e->id === $id) { continue; }
            $n = 0; foreach ($ps as $re) { $n = max($n, preg_match_all($re, $text)); }
            if (!$n) { continue; }
            $sw = 0; foreach (preg_split('~(?<=[.!?])\s+~u', $text) as $s) { foreach ($ps as $re) { if (preg_match($re, $s)) { $sw += str_word_count($s); break; } } }
            $targets[$id]['mentions'][] = [$label, $n, $sw, $sec, $e->url];
            $targets[$id]['sentWords'] += $sw;
        }
        if ($want) { foreach ($sh($text) as $g) { if (isset($want[$g])) { foreach ($want[$g] as [$id, $si]) { if ($e->id !== $id) { $hit[$id][$si][$label] = true; } } } } }
    }
}
foreach ($hit as $id => $bySent) { $targets[$id]['copied'] = count($bySent); $from = []; foreach ($bySent as $labels) { foreach ($labels as $l => $_) { $from[$l] = ($from[$l] ?? 0) + 1; } } arsort($from); $targets[$id]['copiedFrom'] = $from; }
echo "scanned $scanned texts\n";

/* Tiers, by what a writer would have in hand with no new research. */
foreach ($targets as $id => &$t) {
    $subst = array_filter($t['mentions'], fn($m) => $m[1] >= 3 || $m[2] >= 120);
    $t['substantial'] = count($subst);
    $t['texts'] = count($t['mentions']);
    $office = ($t['links']['officeHoldings'] ?? 0) + ($t['links']['candidacies'] ?? 0);
    $t['office'] = $office;
    if ($t['substantial'] >= 2 || ($t['substantial'] >= 1 && $t['texts'] >= 4) || ($t['copied'] && $t['copied'] >= $t['sentences'] / 2)) { $t['tier'] = 'A'; }
    elseif ($t['texts'] >= 2 || $t['substantial'] >= 1 || $office >= 1 || $t['copied']) { $t['tier'] = 'B'; }
    else { $t['tier'] = 'C'; }
    usort($t['mentions'], fn($a, $b) => $b[2] <=> $a[2]);
    unset($t['body'], $t['sent']);
}
unset($t);
uasort($targets, fn($a, $b) => [$a['tier'], -$a['inbound']] <=> [$b['tier'], -$b['inbound']]);

$md = ['# What the archive already holds for each person with no biography, 2 October 2026', '', 'Generated by scripts/import/audit_person_profile_sources.php (read only).', '',
    'Tier A: at least two texts in the archive say something substantial about them (named three times, or 120 words of sentences naming them), or one such text and four that name them, or the withheld text is at least half found in a text already here. A short sourced profile can be written from these with no new research.',
    'Tier B: something to cite (two texts naming them, an office or candidacy record, or part of the withheld text found here), but only enough for a few sentences.',
    'Tier C: nothing in the archive beyond the record itself.', ''];
$c = ['A' => 0, 'B' => 0, 'C' => 0]; $byAuth = [];
foreach ($targets as $t) { $c[$t['tier']]++; $byAuth[$t['auth']][$t['tier']] = ($byAuth[$t['auth']][$t['tier']] ?? 0) + 1; }
$md[] = '| | A | B | C |'; $md[] = '|---|---|---|---|';
foreach ($byAuth as $a => $r) { $md[] = "| $a | " . ($r['A'] ?? 0) . ' | ' . ($r['B'] ?? 0) . ' | ' . ($r['C'] ?? 0) . ' |'; }
$md[] = "| all | {$c['A']} | {$c['B']} | {$c['C']} |"; $md[] = '';
foreach ($targets as $t) {
    $md[] = "## {$t['tier']} · {$t['title']} (#{$t['id']})"; $md[] = '';
    $md[] = "- body: {$t['auth']}" . ($t['words'] ? ", {$t['words']} words, {$t['sentences']} sentences, {$t['copied']} found in the archive" : '');
    if ($t['copiedFrom']) { $md[] = '- withheld text found in: ' . implode('; ', array_map(fn($l, $n) => "$l ($n)", array_keys(array_slice($t['copiedFrom'], 0, 4)), array_slice($t['copiedFrom'], 0, 4))); }
    $md[] = "- links in: {$t['inbound']} (" . implode(', ', array_map(fn($k, $v) => "$k $v", array_keys($t['links']), $t['links'])) . "); bylines {$t['bylines']}; office and candidacy records {$t['office']}";
    if ($t['dates']) { $md[] = '- on the record: ' . implode('; ', array_map(fn($k, $v) => "$k $v", array_keys($t['dates']), $t['dates'])); }
    $md[] = "- texts naming them: {$t['texts']}, substantial {$t['substantial']}, {$t['sentWords']} words of sentences naming them";
    foreach (array_slice($t['mentions'], 0, 5) as $m) { $md[] = "  - {$m[0]}: named {$m[1]}x, {$m[2]} words"; }
    $md[] = '';
}
file_put_contents(\Craft::getAlias('@root') . '/inventory/review/person-profile-sources-2026-10-02.md', implode(PHP_EOL, $md) . PHP_EOL);
file_put_contents(\Craft::getAlias('@root') . '/inventory/review/person-profile-sources-2026-10-02.json', json_encode(array_values($targets), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo json_encode($c) . PHP_EOL . json_encode($byAuth) . PHP_EOL . 'wrote inventory/review/person-profile-sources-2026-10-02.md' . PHP_EOL;
