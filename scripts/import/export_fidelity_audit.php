/**
 * Side-by-side fidelity files, for a reviewer with no access to either site.
 *
 * One plain text file per batch of ten articles. For each article it writes the
 * legacy page's extracted body_text and the body as Craft now stores it, one
 * after the other, both verbatim, plus the fields that were set from that page.
 * The legacy URL is at the top of every pair so anything doubtful can be
 * checked by hand later, but nothing in the file depends on reaching it.
 *
 * Read only. It writes text files and touches nothing in Craft.
 *
 * The comparison line under each pair counts rather than judges: how many lines
 * of the legacy text appear nowhere in the imported one, and how many lines the
 * imported one has that the legacy did not. Lines are compared on their words
 * alone, ignoring case, spacing and punctuation, because the import deliberately
 * changes all three. A count is a place to look, not a verdict; the two bodies
 * below it are the evidence.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/export_fidelity_audit.php'))"
 */

$INVENTORY = 'perkins';
$LISTS     = ['series_pages', 'related_pages'];   /* in order */
$LIMIT     = 10;      /* how many articles in total; 0 for all */
$PER_FILE  = 10;      /* articles per file */
$OUT_DIR   = \Craft::getAlias('@webroot') . '/review/fidelity';

/* The fields set from the legacy page, in the order a reviewer would check
   them. Each is [label, how to read it]. */
$FIELDS = ['title', 'subheadline', 'writtenBy', 'originalPublishDate',
           'publishedBy', 'neighborhood'];

$root = \Craft::getAlias('@root');

$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

/** A line reduced to its words, for comparing text the import reflowed. */
$fingerprint = function (string $line): string {
    $s = mb_strtolower(trim($line));
    $s = preg_replace('~\[image:\d+\]~', ' ', $s);
    $s = preg_replace('~</?[a-z][^>]*>~i', ' ', $s);
    $s = preg_replace('~[^\p{L}\p{N}]+~u', ' ', $s);
    return trim(preg_replace('~\s+~', ' ', $s));
};

$readable = function ($value) {
    if ($value === null) { return '(empty)'; }
    if ($value instanceof \craft\elements\db\ElementQuery) {
        $els = $value->all();
        if (!$els) { return '(none)'; }
        return implode('; ', array_map(fn($e) => $e->title . ' #' . $e->id, $els));
    }
    if (is_array($value)) { return $value ? json_encode($value) : '(none)'; }
    $s = trim((string)$value);
    return $s === '' ? '(empty)' : $s;
};

/* ------------------------------------------------------------------ read */

$path = $root . '/inventory/legacy/' . $INVENTORY . '.json';
if (!file_exists($path)) { echo 'not found: ' . $path . PHP_EOL; return; }
$doc = json_decode(file_get_contents($path), true);

$pages = [];
foreach ($LISTS as $list) {
    foreach (($doc[$list] ?? []) as $p) { $p['_list'] = $list; $pages[] = $p; }
}
/* Series order, as the contents page gives it. */
usort($pages, function ($a, $b) use ($LISTS) {
    $la = array_search($a['_list'], $LISTS, true);
    $lb = array_search($b['_list'], $LISTS, true);
    if ($la !== $lb) { return $la <=> $lb; }
    return ((int)($a['series_position'] ?? 0)) <=> ((int)($b['series_position'] ?? 0));
});
if ($LIMIT > 0) { $pages = array_slice($pages, 0, $LIMIT); }

echo 'inventory: ' . $INVENTORY . '.json, ' . count($pages) . ' articles' . PHP_EOL;

if (!is_dir($OUT_DIR)) { mkdir($OUT_DIR, 0775, true); }

/* ----------------------------------------------------------------- write */

$batches = array_chunk($pages, $PER_FILE);
$total = count($pages);
$written = [];
$missing = [];
$n = 0;

foreach ($batches as $bi => $batch) {
    $from = $bi * $PER_FILE + 1;
    $to = $from + count($batch) - 1;
    $name = sprintf('%s-%02d_articles-%03d-%03d.txt', $INVENTORY, $bi + 1, $from, $to);
    $out = [];

    $out[] = str_repeat('=', 80);
    $out[] = 'SCVHISTORY.COM  FIDELITY AUDIT';
    $out[] = $INVENTORY . ', articles ' . $from . ' to ' . $to . ' of ' . $total
        . '   (file ' . ($bi + 1) . ' of ' . count($batches) . ')';
    $out[] = 'Generated ' . (new DateTime())->format('j F Y, g:ia');
    $out[] = str_repeat('=', 80);
    $out[] = '';
    $out[] = 'Each article below appears twice, verbatim:';
    $out[] = '';
    $out[] = '  A. LEGACY    the body as the extraction read it off the legacy page.';
    $out[] = '  B. IMPORTED  the body as Craft stores it now.';
    $out[] = '';
    $out[] = 'B is expected to differ from A. The import strips the legacy page furniture,';
    $out[] = 'the breadcrumbs, the repeated headline, the byline and dateline and the footer';
    $out[] = 'link row, and moves the copyright line into a field of its own. It also inserts';
    $out[] = '[image:N] tokens where pictures sit, and <em> and <a> for emphasis and links.';
    $out[] = 'Prose that appears in A and not in B is the thing to look for.';
    $out[] = '';
    $out[] = 'The legacy URL is given for every article so it can be checked by hand, but';
    $out[] = 'nothing here needs it: both texts are in this file.';
    $out[] = '';

    foreach ($batch as $p) {
        $n++;
        $key = (string)($p['legacy_key'] ?? '');
        $legacyPath = (string)($p['legacy_path'] ?? '');
        $entry = null;
        if ($legacyPath !== '') {
            $entry = \craft\elements\Entry::find()->status(null)->legacyUrl($legacyPath)->one();
        }
        if (!$entry && $key !== '') {
            $entry = \craft\elements\Entry::find()->status(null)->legacyKey($key)->one();
        }

        $legacyBody = (string)($p['body_text'] ?? '');
        $craftBody = ($entry && $hasField($entry, 'body')) ? (string)$entry->body : '';

        $out[] = '';
        $out[] = str_repeat('=', 80);
        $out[] = sprintf('ARTICLE %d of %d', $n, $total);
        $out[] = str_repeat('=', 80);
        $out[] = '';
        $out[] = 'LEGACY URL    ' . (string)($p['source_url'] ?? '(none recorded)');
        $out[] = 'LEGACY PATH   ' . ($legacyPath !== '' ? $legacyPath : '(none recorded)');
        $out[] = 'LEGACY KEY    ' . ($key !== '' ? $key : '(none)');
        $out[] = 'INVENTORY     ' . $INVENTORY . '.json -> ' . $p['_list']
            . ', series_position ' . (string)($p['series_position'] ?? '-');

        if (!$entry) {
            $missing[] = $key . '  ' . $legacyPath;
            $out[] = 'CRAFT RECORD  NONE. This page has not been imported.';
            $out[] = '';
            $out[] = str_repeat('-', 80);
            $out[] = 'A. LEGACY, as extracted    ' . number_format(mb_strlen($legacyBody)) . ' characters';
            $out[] = str_repeat('-', 80);
            $out[] = $legacyBody === '' ? '(the extraction recorded no body text)' : $legacyBody;
            $out[] = '';
            $out[] = str_repeat('-', 80);
            $out[] = 'B. IMPORTED';
            $out[] = str_repeat('-', 80);
            $out[] = '(nothing: there is no record for this page)';
            $out[] = '';
            continue;
        }

        $out[] = 'CRAFT RECORD  #' . $entry->id . '  ' . $entry->section->handle . '/' . $entry->slug;
        $out[] = 'CRAFT URL     ' . ((string)$entry->url ?: '(no public URL)');
        $out[] = '';
        $out[] = str_repeat('-', 80);
        $out[] = 'FIELDS SET FROM THIS PAGE';
        $out[] = str_repeat('-', 80);
        foreach ($FIELDS as $fh) {
            if ($fh === 'title') {
                $out[] = str_pad('title', 22) . $entry->title;
                continue;
            }
            if (!$hasField($entry, $fh)) {
                $out[] = str_pad($fh, 22) . '(no such field on this entry type)';
                continue;
            }
            $out[] = str_pad($fh, 22) . $readable($entry->getFieldValue($fh));
        }
        /* Pictures, counted where they are actually stored. */
        $imgBits = [];
        $imgTotal = 0;
        foreach (['featuredImage', 'recordImages', 'bandImage'] as $af) {
            if (!$hasField($entry, $af)) { continue; }
            $c = (int)$entry->getFieldValue($af)->count();
            if ($c) { $imgBits[] = $af . ' ' . $c; }
            $imgTotal += $c;
        }
        $out[] = str_pad('related images', 22) . $imgTotal
            . ($imgBits ? '   (' . implode(', ', $imgBits) . ')' : '');
        $tokens = preg_match_all('~\[image:\d+\]~', $craftBody);
        $out[] = str_pad('image tokens in body', 22) . $tokens;

        /* The count that says where to look. */
        $aLines = array_values(array_filter(array_map($fingerprint, preg_split('~\R~u', $legacyBody)), fn($l) => $l !== ''));
        $bLines = array_values(array_filter(array_map($fingerprint, preg_split('~\R~u', $craftBody)), fn($l) => $l !== ''));
        $bSet = array_flip($bLines);
        $aSet = array_flip($aLines);
        $onlyA = array_values(array_filter($aLines, fn($l) => !isset($bSet[$l])));
        $onlyB = array_values(array_filter($bLines, fn($l) => !isset($aSet[$l])));

        $out[] = '';
        $out[] = str_repeat('-', 80);
        $out[] = 'AT A GLANCE';
        $out[] = str_repeat('-', 80);
        $out[] = str_pad('legacy body', 22) . number_format(mb_strlen($legacyBody)) . ' characters, '
            . count($aLines) . ' non-empty lines';
        $out[] = str_pad('imported body', 22) . number_format(mb_strlen($craftBody)) . ' characters, '
            . count($bLines) . ' non-empty lines';
        $out[] = str_pad('lines only in A', 22) . count($onlyA)
            . '   (removed by the import, or reflowed)';
        $out[] = str_pad('lines only in B', 22) . count($onlyB)
            . '   (added by the import, or reflowed)';
        if ($onlyA) {
            $out[] = '';
            $out[] = 'IN A AND NOT IN B, first 15, compared on their words alone.';
            $out[] = 'Most of these are page furniture the import removes on purpose: the series';
            $out[] = 'heading, the repeated headline, the breadcrumb, the caption list under the';
            $out[] = 'thumbnail rail. Prose here is a loss. Read them against A and B below.';
            foreach (array_slice($onlyA, 0, 15) as $l) {
                $out[] = '   - ' . mb_substr($l, 0, 96);
            }
            if (count($onlyA) > 15) { $out[] = '   ... and ' . (count($onlyA) - 15) . ' more'; }
        }
        if ($onlyB) {
            $out[] = '';
            $out[] = 'IN B AND NOT IN A, first 15. This is the direction worth reading closely:';
            $out[] = 'the import removes things by design and adds almost nothing, so a sentence';
            $out[] = 'here came from somewhere other than the page above. It may be an editor\'s';
            $out[] = 'addition, or a second legacy page folded into this record, or the extraction';
            $out[] = 'having missed part of the page. All three want checking.';
            foreach (array_slice($onlyB, 0, 15) as $l) {
                $out[] = '   + ' . mb_substr($l, 0, 96);
            }
            if (count($onlyB) > 15) { $out[] = '   ... and ' . (count($onlyB) - 15) . ' more'; }
        }

        $out[] = '';
        $out[] = str_repeat('-', 80);
        $out[] = 'A. LEGACY, as extracted    ' . $INVENTORY . '.json, body_text, verbatim';
        $out[] = str_repeat('-', 80);
        $out[] = $legacyBody === '' ? '(the extraction recorded no body text)' : $legacyBody;
        $out[] = '';
        $out[] = str_repeat('-', 80);
        $out[] = 'B. IMPORTED, as Craft stores it    entry #' . $entry->id . ', body, verbatim';
        $out[] = str_repeat('-', 80);
        $out[] = $craftBody === '' ? '(the body field is empty)' : $craftBody;
        $out[] = '';
    }

    $out[] = '';
    $out[] = str_repeat('=', 80);
    $out[] = 'END OF FILE. Articles ' . $from . ' to ' . $to . ' of ' . $total . '.';
    if ($bi + 1 < count($batches)) {
        $next = sprintf('%s-%02d_articles-%03d-%03d.txt', $INVENTORY, $bi + 2,
            $to + 1, min($to + $PER_FILE, $total));
        $out[] = 'Next: ' . $next;
    } else {
        $out[] = 'This is the last file for ' . $INVENTORY . '.';
    }
    $out[] = str_repeat('=', 80);

    $body = implode("\n", $out) . "\n";
    file_put_contents($OUT_DIR . '/' . $name, $body);
    $written[$name] = strlen($body);
}

echo PHP_EOL . 'wrote to ' . str_replace(\Craft::getAlias('@webroot'), 'web', $OUT_DIR) . '/' . PHP_EOL;
foreach ($written as $name => $bytes) {
    echo '   ' . str_pad($name, 42) . number_format($bytes) . ' bytes' . PHP_EOL;
}
if ($missing) {
    echo PHP_EOL . 'pages with no Craft record, written with an empty B: ' . count($missing) . PHP_EOL;
    foreach ($missing as $m) { echo '   ' . $m . PHP_EOL; }
}
echo PHP_EOL . 'Readable at ' . Craft::$app->getSites()->getPrimarySite()->getBaseUrl()
    . 'review/fidelity/ , or straight off disk. Nothing in Craft was changed.' . PHP_EOL;
