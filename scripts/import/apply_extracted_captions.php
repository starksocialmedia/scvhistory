/**
 * Puts the captions the crawl already extracted onto the assets they describe.
 *
 * 562 of the 568 assets in Craft are titled from their own filename, because
 * the importer had nothing else to name them with. The caption text was never
 * lost: it sits in the *-images.json inventories, one record per image
 * reference, and has simply never been written back. This writes it back.
 *
 * Matching is by filename, lowercased. That is the only key the two sides
 * share: the inventories record src_raw exactly as the legacy page wrote it,
 * and the importer named each asset after the file it fetched. There are no
 * filename collisions among the 568.
 *
 * The same file is referenced from several pages, so a filename can carry more
 * than one caption. Where the variants agree after cleaning, one is written.
 * Where they differ the longest surviving text wins, on the reasoning that the
 * legacy site shortened captions on index pages and wrote them out in full on
 * the page that owned the image. Every conflict is listed in the report with
 * all its variants, so a wrong pick is visible rather than silent.
 *
 * Navigation is stripped. "Click image to enlarge" is not a caption, it is the
 * instruction that used to sit under a thumbnail, and 681 of the 5,220 caption
 * records are nothing else. The rule is the one already accepted for trailing
 * [ BACK ]: a segment that reads as navigation is removed, and a caption that
 * was nothing but navigation leaves the asset with no caption rather than a
 * false one. Removal is by whole segment and never mid-sentence.
 *
 * What is written, and nothing else is touched:
 *   photoCaptionExt  the cleaned caption. The field exists for this and is
 *                    empty on all 568.
 *   title            only where the current title is the filename, which is to
 *                    say where it carries no information. A title someone set
 *                    by hand is left alone.
 *   alt              only where empty. The caption is the best alt text we
 *                    have, and 560 of the 568 have none at all.
 *   photoCredit      only where empty and the inventory recorded a credit.
 *
 * Re-running is safe and is expected. Only 113 of the 568 assets we hold have
 * a caption in the inventories, because most of the archive's images have not
 * been fetched yet. The inventories carry captions for about 2,160 distinct
 * files. Run this again after each image import and the rest will land.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/apply_extracted_captions.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$root   = \Craft::getAlias('@root');
$REPORT = \Craft::getAlias('@webroot') . '/review/captions.md';

/* ---------------------------------------------------------------- cleaning */

/* A segment is navigation when it opens with one of these. Opening, not
   containing: "the sign says click here" is a caption about a sign. */
$NAV_OPENER = '/^\s*[\[\("\x{201C}\']*\s*(click|download|enlarge|mouse\s*over|scroll\s+down)\b/iu';

/* A bracketed control, as the legacy pages wrote their size links:
   [ FULL VIEW ] [ CLOSEUP ] [ Jumbo Size ] */
$NAV_BRACKET = '/^\s*\[[^\]]*\]\s*$/u';

/* The same controls written without brackets, as a bar segment of their own:
   "... 1944. | Full View | Archival Scan". Only a whole segment, and only these
   words, so a caption that says "a full view of the valley" is untouched. */
$NAV_PHRASE = '/^\s*(full\s+view|close\s?up|ultra\s+close\s?up|jumbo(\s+size)?|'
            . 'archival\s+scans?|extra\s+large|larger(\s+image)?|original(\s+image)?|'
            . 'supersize|full\s+size|hi-?res(olution)?)\s*[.:]?\s*$/iu';

$clean = function (string $raw) use ($NAV_OPENER, $NAV_BRACKET, $NAV_PHRASE): string {
    $raw = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $raw = preg_replace('/\s+/u', ' ', $raw);

    $kept = [];
    /* The pipe was the legacy separator between a caption and its controls, so
       it is a hard boundary. Inside a pipe segment, a sentence or clause end is
       a soft one. Splitting keeps the terminator with the text before it. */
    foreach (preg_split('/\s*\|\s*/u', (string)$raw) as $bar) {
        if (trim($bar) === '') { continue; }
        if (preg_match($NAV_BRACKET, $bar)) { continue; }
        if (preg_match($NAV_PHRASE, $bar)) { continue; }

        $parts = preg_split('/(?<=[.;!?])\s+/u', $bar);
        $keptHere = [];
        foreach ($parts as $p) {
            if (trim($p) === '') { continue; }
            if (preg_match($NAV_OPENER, $p)) { continue; }
            if (preg_match($NAV_BRACKET, $p)) { continue; }
            /* A trailing run of bracketed controls on an otherwise good
               sentence: keep the sentence, drop the controls. */
            $p = preg_replace('/(\s*\[[^\]]*\]\s*)+$/u', '', $p);
            /* And a navigation clause hung on the end with a semicolon or a
               comma, which the sentence split does not always separate. */
            $p = preg_replace('/[;,]\s*click\b[^.;]*[.]?\s*$/iu', '', $p);
            if (trim($p) === '') { continue; }
            $keptHere[] = trim($p);
        }
        if ($keptHere) { $kept[] = implode(' ', $keptHere); }
    }

    $out = trim(implode(' | ', $kept));
    /* What the removal left behind: an opened bracket with nothing in it, a
       colon introducing text that is gone, a dangling separator. */
    $out = preg_replace('/\(\s*\)|\[\s*\]/u', '', $out);
    $out = trim($out, " \t\n\r\0\x0B|-–—");
    $out = preg_replace('/\s{2,}/u', ' ', $out);
    $out = rtrim($out, " :;,");
    return trim($out);
};

$norm = function (string $s): string {
    return preg_replace('/[^a-z0-9]/', '', mb_strtolower($s, 'UTF-8'));
};

/* ------------------------------------------------------------- inventories */

$files = glob($root . '/inventory/legacy/*-images.json');
sort($files);
if (!$files) { echo 'no *-images.json inventories found' . PHP_EOL; return; }

$byFile   = [];   /* filename => [cleaned caption => [count, raw sample, keys]] */
$creditBy = [];   /* filename => credit */
$refs = 0; $rawCaptions = 0; $navOnly = 0;

foreach ($files as $f) {
    $data = json_decode(file_get_contents($f), true);
    foreach (($data['images'] ?? []) as $r) {
        $refs++;
        $src = (string)($r['src_raw'] ?? '');
        if ($src === '') { continue; }
        $fn = mb_strtolower(basename(parse_url($src, PHP_URL_PATH) ?: $src), 'UTF-8');
        if ($fn === '') { continue; }

        $cr = trim((string)($r['credit_raw'] ?? ''));
        if ($cr !== '' && !isset($creditBy[$fn])) { $creditBy[$fn] = $cr; }

        $raw = trim((string)($r['caption'] ?? ''));
        if ($raw === '') { continue; }
        $rawCaptions++;

        $c = $clean($raw);
        if ($c === '') { $navOnly++; continue; }

        if (!isset($byFile[$fn][$c])) {
            $byFile[$fn][$c] = ['n' => 0, 'raw' => $raw, 'keys' => []];
        }
        $byFile[$fn][$c]['n']++;
        $k = (string)($r['legacy_key'] ?? '');
        if ($k !== '' && count($byFile[$fn][$c]['keys']) < 4) { $byFile[$fn][$c]['keys'][$k] = true; }
    }
}

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo 'inventories read: ' . count($files) . PHP_EOL;
echo 'image references: ' . $refs . PHP_EOL;
echo 'carrying a caption: ' . $rawCaptions . PHP_EOL;
echo 'caption was navigation only: ' . $navOnly . PHP_EOL;
echo 'distinct files with a real caption: ' . count($byFile) . PHP_EOL;
echo str_repeat('=', 72) . PHP_EOL;

/* ------------------------------------------------------------------ assets */

$assets = \craft\elements\Asset::find()->limit(null)->all();
$elements = Craft::$app->getElements();

$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

$conflicts = []; $applied = []; $navOnlyAssets = []; $noCaption = 0;
$setCaption = 0; $setTitle = 0; $setAlt = 0; $setCredit = 0; $saved = 0; $failed = [];

foreach ($assets as $a) {
    $fn = mb_strtolower($a->filename, 'UTF-8');
    if (!isset($byFile[$fn])) { $noCaption++; continue; }

    $variants = $byFile[$fn];
    /* Longest wins. Ties go to the one seen most often, then alphabetically, so
       a second run picks the same one. */
    $keys = array_keys($variants);

    /* The longest is usually the fullest, because the legacy site shortened a
       caption on an index page and wrote it out on the page that owned the
       image. But the extractor sometimes took a paragraph of body text sitting
       under a picture, and a paragraph always beats a caption on length. So a
       variant over 200 characters is set aside when a shorter one exists; it is
       still used when it is all there is, since some captions really are long. */
    $short = array_filter($keys, fn($k) => mb_strlen($k, 'UTF-8') <= 200);
    $pool  = $short ?: $keys;

    usort($pool, function ($x, $y) use ($variants) {
        $d = mb_strlen($y, 'UTF-8') <=> mb_strlen($x, 'UTF-8');
        if ($d !== 0) { return $d; }
        $d = $variants[$y]['n'] <=> $variants[$x]['n'];
        if ($d !== 0) { return $d; }
        return strcmp($x, $y);
    });
    $caption = $pool[0];
    $keys = array_merge([$caption], array_values(array_diff($keys, [$caption])));

    if (count($keys) > 1) {
        $conflicts[] = ['file' => $fn, 'id' => $a->id, 'chosen' => $caption, 'others' => array_slice($keys, 1)];
    }

    /* A caption that is only the legacy image code again is no better than the
       title we already have: "LW3733b: Click to enlarge." A caption that merely
       happens to match the filename is a different thing, because the archive
       names a portrait after its sitter, and "Pedro Fages" on pedrofages.jpg is
       a correct caption rather than a filename showing through. The code is
       told apart by having no space and carrying a digit. */
    $stem = pathinfo($a->filename, PATHINFO_FILENAME);
    $looksLikeCode = !str_contains(trim($caption), ' ') && preg_match('/[0-9]/', $caption);
    if ($looksLikeCode && $norm($caption) === $norm($stem)) {
        $navOnlyAssets[] = $fn . '  (caption was the filename: ' . $caption . ')';
        continue;
    }

    $changes = [];

    if ($hasField($a, 'photoCaptionExt') && trim((string)$a->getFieldValue('photoCaptionExt')) === '') {
        $a->setFieldValue('photoCaptionExt', $caption);
        $changes[] = 'caption';
        $setCaption++;
    }

    /* The title only where it carries nothing. A title that differs from the
       filename was set by a person and is not ours to overwrite. */
    if ($norm((string)$a->title) === $norm($stem)) {
        $t = $caption;
        if (mb_strlen($t, 'UTF-8') > 120) {
            $cut = preg_split('/(?<=[.!?])\s+/u', $t)[0];
            $t = mb_strlen($cut, 'UTF-8') <= 120 ? $cut : (mb_substr($t, 0, 117, 'UTF-8') . '...');
        }
        $t = rtrim($t, ' .');
        if ($t !== '') { $a->title = $t; $changes[] = 'title'; $setTitle++; }
    }

    if (trim((string)$a->alt) === '') { $a->alt = $caption; $changes[] = 'alt'; $setAlt++; }

    if (isset($creditBy[$fn]) && $hasField($a, 'photoCredit')
        && trim((string)$a->getFieldValue('photoCredit')) === '') {
        $a->setFieldValue('photoCredit', $creditBy[$fn]);
        $changes[] = 'credit';
        $setCredit++;
    }

    if (!$changes) { continue; }

    $applied[] = ['file' => $fn, 'id' => $a->id, 'caption' => $caption,
                  'title' => $a->title, 'changed' => implode(', ', $changes)];

    if ($APPLY) {
        if ($elements->saveElement($a)) { $saved++; }
        else { $failed[] = $fn . ': ' . json_encode($a->getErrors()); }
    }
}

echo 'assets in Craft: ' . count($assets) . PHP_EOL;
echo 'with a caption in the inventories: ' . (count($assets) - $noCaption) . PHP_EOL;
echo 'caption was navigation or the filename: ' . count($navOnlyAssets) . PHP_EOL;
echo 'assets to change: ' . count($applied) . PHP_EOL;
echo '  photoCaptionExt set: ' . $setCaption . PHP_EOL;
echo '  title set: ' . $setTitle . PHP_EOL;
echo '  alt set: ' . $setAlt . PHP_EOL;
echo '  photoCredit set: ' . $setCredit . PHP_EOL;
echo 'variant conflicts resolved by length: ' . count($conflicts) . PHP_EOL;
if ($APPLY) { echo 'saved: ' . $saved . PHP_EOL; }
foreach ($failed as $f) { echo '  FAILED ' . $f . PHP_EOL; }

/* ------------------------------------------------------------------ report */

$out = [];
$out[] = '# Extracted captions applied to assets';
$out[] = '';
$out[] = 'Generated by scripts/import/apply_extracted_captions.php on ' . date('Y-m-d H:i');
$out[] = ($APPLY ? 'Mode: APPLIED' : 'Mode: DRY RUN, nothing written') . '.';
$out[] = '';
$out[] = '## The shape of it';
$out[] = '';
$out[] = '| | |';
$out[] = '|---|---:|';
$out[] = '| image references in the inventories | ' . $refs . ' |';
$out[] = '| references carrying caption text | ' . $rawCaptions . ' |';
$out[] = '| of those, navigation only | ' . $navOnly . ' |';
$out[] = '| distinct files with a real caption | ' . count($byFile) . ' |';
$out[] = '| assets in Craft | ' . count($assets) . ' |';
$out[] = '| assets matched to a caption | ' . (count($assets) - $noCaption) . ' |';
$out[] = '| assets changed by this run | ' . count($applied) . ' |';
$out[] = '';
$out[] = 'The gap between the distinct files with a caption and the assets matched';
$out[] = 'to one is the archive that has not been fetched yet. Re-run this after';
$out[] = 'each image import.';
$out[] = '';
$out[] = '## Conflicting variants (' . count($conflicts) . ')';
$out[] = '';
$out[] = 'The longest under 200 characters was taken. If another is right, say';
$out[] = 'which and it will be corrected by hand.';
$out[] = '';
foreach ($conflicts as $c) {
    $out[] = '### ' . $c['file'] . ' (asset ' . $c['id'] . ')';
    $out[] = '';
    $out[] = '- TAKEN: ' . $c['chosen'];
    foreach ($c['others'] as $o) { $out[] = '- also: ' . $o; }
    $out[] = '';
}
$out[] = '## Left with no caption (' . count($navOnlyAssets) . ')';
$out[] = '';
foreach ($navOnlyAssets as $n) { $out[] = '- ' . $n; }
$out[] = '';
$out[] = '## Every change (' . count($applied) . ')';
$out[] = '';
$out[] = '| asset | file | fields | caption |';
$out[] = '|---:|---|---|---|';
foreach ($applied as $a) {
    $out[] = '| ' . $a['id'] . ' | ' . $a['file'] . ' | ' . $a['changed'] . ' | '
           . str_replace('|', '\\|', $a['caption']) . ' |';
}
$out[] = '';

@mkdir(dirname($REPORT), 0775, true);
file_put_contents($REPORT, implode("\n", $out) . "\n");
echo PHP_EOL . 'report: ' . $REPORT . PHP_EOL;
