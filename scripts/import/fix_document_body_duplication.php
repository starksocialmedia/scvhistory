/**
 * Takes the headline and the credit line out of a document's body, where the
 * fields already hold them.
 *
 * The Vasquez document's body opens with three things that are also fields:
 *
 *   TIBURCIO VASQUEZ! A Brief Sketch of the Notorious Bandit.   originallyPublishedTitle
 *   Entered according to act of Congress, in the year 1874, by
 *   V. WOLFENSTEIN, of Los Angeles ...                          sourceLine
 *
 * They are in the body because create_vasquez_document.php put them there,
 * composing the body as headline + copyright + text. That was a guess about
 * where they belonged, and the legacy page settles it: the copyright line sits
 * in a blockquote, then a rule, then TIBURCIO VASQUEZ! centred in class
 * "subhed2", then a rule, then the sub-headline centred, then a rule, and only
 * then the text in class "bodyserif". Both are display matter printed above the
 * text, not the text — so they belong in the fields, which is where they also
 * are, and templates/documents/_entry.twig now renders them in that order.
 *
 * The body keeps what the sketch says, opening "Vasquez is now busily engaged"
 * and closing on its dateline, "Los Angeles May 19th, 1874."
 *
 * NOTHING IS MATCHED BY EYE. The lines to remove are taken from
 * inventory/legacy/vasquez-je4001.json, the copy fetched from the legacy page
 * with its URL and date recorded, and each one must equal the field it
 * duplicates and appear at the head of the body. Anything that does not match
 * exactly is listed and the record is left alone.
 *
 * Idempotent: a body that no longer opens with them is already done.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_document_body_duplication.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$SRC = \Craft::getAlias('@root') . '/inventory/legacy/vasquez-je4001.json';

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

if (!is_file($SRC)) { echo 'source extract missing: ' . $SRC . PHP_EOL; return; }
$src = json_decode((string)file_get_contents($SRC), true);
$norm = fn(string $s): string => trim(preg_replace('~\s+~', ' ', $s));

$plan = []; $listed = [];
foreach (\craft\elements\Entry::find()->section('documents')->status(null)->limit(null)->each() as $doc) {
    $body = trim((string)$doc->getFieldValue('body'));
    $headline = trim((string)$doc->getFieldValue('originallyPublishedTitle'));
    $credit = trim((string)$doc->getFieldValue('sourceLine'));
    if ($body === '') { continue; }

    /* Only the record this extract describes. */
    if (trim((string)$doc->getFieldValue('legacyKey')) !== (string)($src['meta']['legacy_key'] ?? '')) {
        $listed[] = '#' . $doc->id . ' ' . $doc->title . ': no source extract on file, left alone';
        continue;
    }

    /* The display matter, every form of it that is actually in the body. The
       headline is there twice: once joined, as create_vasquez_document.php
       composed it, and once as the two centred lines the page printed, because
       the extract's body lines start above them. Each form is checked against
       the fetched page, so nothing is removed on the strength of a pattern. */
    $srcHead = $norm((string)($src['headline'] ?? ''));
    $display = [];
    if ($srcHead !== '') {
        $display[$srcHead] = 'headline, equal to originallyPublishedTitle and to the fetched page';
        /* "TIBURCIO VASQUEZ!" and "A Brief Sketch of the Notorious Bandit." */
        if (str_contains($srcHead, '!')) {
            [$first, $rest] = explode('!', $srcHead, 2);
            foreach ([$norm($first . '!'), $norm($rest)] as $part) {
                if ($part !== '' && $part !== $srcHead) {
                    $display[$part] = 'headline as the page set it, one centred line of two';
                }
            }
        }
    }
    $srcCredit = $norm((string)($src['copyright_line'] ?? ''));
    if ($srcCredit !== '') { $display[$srcCredit] = 'credit line, equal to sourceLine and to the fetched page'; }

    /* Sanity: the fields must still hold what is being removed, or this is
       deleting text the record does not keep anywhere. */
    if ($headline === '' || $norm($headline) !== $srcHead) {
        $listed[] = '#' . $doc->id . ': originallyPublishedTitle does not match the fetched headline, left alone';
        continue;
    }
    if ($credit === '' || $norm($credit) !== $srcCredit) {
        $listed[] = '#' . $doc->id . ': sourceLine does not match the fetched credit line, left alone';
        continue;
    }

    $lines = preg_split('~\R~', $body);
    $drop = []; $why = [];
    foreach ($lines as $i => $line) {
        $n = $norm($line);
        if ($n === '') { continue; }
        if (isset($display[$n])) {
            $drop[$i] = true;
            if (!in_array($display[$n], $why, true)) { $why[] = $display[$n]; }
            continue;
        }
        /* The first line that is not display matter is where the text starts,
           and nothing below it is touched. */
        break;
    }

    if (!$drop) { $listed[] = '#' . $doc->id . ' ' . $doc->title . ': body already starts with the text'; continue; }

    $kept = $lines;
    foreach (array_keys($drop) as $i) { unset($kept[$i]); }
    $new = trim(preg_replace('~\A\s*\n~', '', implode("\n", $kept)));

    $plan[] = ['e' => $doc, 'old' => $body, 'new' => $new, 'why' => $why];
}

foreach ($plan as $p) {
    $doc = $p['e'];
    echo PHP_EOL . '#' . $doc->id . '  ' . $doc->title . PHP_EOL;
    foreach ($p['why'] as $w) { echo '   remove: ' . $w . PHP_EOL; }
    echo '   body ' . mb_strlen($p['old']) . ' -> ' . mb_strlen($p['new']) . ' characters' . PHP_EOL;
    echo '   was:  ' . json_encode(mb_substr($p['old'], 0, 110)) . PHP_EOL;
    echo '   now:  ' . json_encode(mb_substr($p['new'], 0, 110)) . PHP_EOL;
    echo '   ends: ' . json_encode(mb_substr($p['new'], -40)) . PHP_EOL;
    echo '   the fields keep them:' . PHP_EOL;
    echo '      originallyPublishedTitle  ' . (string)$doc->getFieldValue('originallyPublishedTitle') . PHP_EOL;
    echo '      sourceLine                ' . mb_substr((string)$doc->getFieldValue('sourceLine'), 0, 80) . '…' . PHP_EOL;
}
foreach ($listed as $l) { echo PHP_EOL . 'LEFT  ' . $l . PHP_EOL; }

echo PHP_EOL . 'documents to change: ' . count($plan) . PHP_EOL;

if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}

$saved = 0; $failed = [];
foreach ($plan as $p) {
    $e = $p['e'];
    $e->setFieldValue('body', $p['new']);
    if (Craft::$app->getElements()->saveElement($e)) { $saved++; }
    else { $failed[] = '#' . $e->id . ': ' . json_encode($e->getFirstErrors()); }
}
echo 'saved ' . $saved . ' of ' . count($plan) . PHP_EOL;
foreach ($failed as $f) { echo 'FAILED ' . $f . PHP_EOL; }

$ok = 0; $short = [];
foreach ($plan as $p) {
    $f = \craft\elements\Entry::find()->id($p['e']->id)->status(null)->one();
    $back = trim((string)$f->getFieldValue('body'));
    $headline = trim((string)$f->getFieldValue('originallyPublishedTitle'));
    $credit = trim((string)$f->getFieldValue('sourceLine'));
    if ($back !== trim($p['new'])) { $short[] = '#' . $f->id . ': the body is not what was planned'; continue; }
    if ($headline === '' || $credit === '') { $short[] = '#' . $f->id . ': a field lost its value'; continue; }
    if (!str_contains($back, 'Los Angeles May 19th, 1874')) { $short[] = '#' . $f->id . ': the dateline is gone'; continue; }
    $ok++;
}
$readback = ($short ? 'SHORT ' : 'verified ') . $ok . ' of ' . count($plan)
          . '; headline and credit still in their fields, dateline still in the body';
echo 'READ-BACK ' . $readback . PHP_EOL;
foreach ($short as $m) { echo '  ' . $m . PHP_EOL; }

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_document_body_duplication.php', $saved, $readback,
    'headline and credit line removed from the body; they render from the fields');

if ($short || $failed) { throw new \RuntimeException('fix_document_body_duplication: the write did not land as planned.'); }
