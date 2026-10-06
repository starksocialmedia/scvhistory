/**
 * The Stan Walker credit on two Mentry documents (silent-faults audit, 5 October 2026, finding 8).
 *
 * import_mentry_sources.php:153 wrote the constant "News reports courtesy of Stan Walker" into webmasterNoteBottom for
 * every sw_ document with no framing of its own: #20090 "Skeleton in the Mountains" (sw_herald031799, Los Angeles Herald,
 * March 17, 1899) and #20093 "Mentre's Bones Found" (sw_lat031799, Los Angeles Times, March 17, 1899). The audit found
 * the credit printed only once in the extract, on #20087, and asked whether the page prints it for these two.
 *
 * What the page prints: all four documents come from one legacy page, /scvhistory/sw_petermentre.htm. Its header, under
 * the page headline "The Mysterious Disappearance and Death of Alec Mentry's Father.", prints as its byline
 * `<font class="byline">News reports courtesy of Stan Walker</font>` and then "SCVHistory.com | March 1, 2014". It is the
 * page's only credit. It names "news reports", plural, and stands above the 1886 Herald item and both 1899 items; the
 * 1931 Warren Times Mirror item (#20096) is introduced separately as Lauren Parker's addition. extract_mentry_sources.py
 * filed the page byline under the first item's framing only, which is why the extract shows it once. So the credit is
 * the page's own, verbatim, and it covers #20090 and #20093: the constant happened to be right. This script changes
 * nothing where the note is exactly the line the page prints, and says so. Where a note held anything else it would be
 * replaced by the printed line (or emptied, if the page printed none).
 *
 * The credit is read from inventory/legacy/mentry-sources.json (written from the Reggie mirror with the page's sha256),
 * and checked against the mirror page itself when the container can see it. The Jordy originals were not mounted on
 * 5 October 2026 and were not checked.
 *
 * Idempotent: a record whose note already reads as the page prints is not saved. Writes no text of its own (the
 * em-dash refusal is applied to the credit anyway).
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_mentry_stan_walker_2026_10_05.php'))"
 */
use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$root = \Craft::getAlias('@root');
$TARGETS = [20090 => 'sw_herald031799', 20093 => 'sw_lat031799'];
$src = json_decode((string)file_get_contents("$root/inventory/legacy/mentry-sources.json"), true);
if (!$src) { throw new \RuntimeException('inventory/legacy/mentry-sources.json missing or unreadable'); }

/* The credit each page prints, from the extract. */
$pageCredit = [];
foreach ($src['items'] as $it) {
    foreach ($it['framing'] ?? [] as $l) { if (preg_match('~^News (reports|story) courtesy\b~', $l)) { $pageCredit[$it['page']][] = $l; } }
}
$itemPage = []; foreach ($src['items'] as $it) { $itemPage[$it['key']] = $it['page']; }

$MIRROR = null; $mirrorNote = '';
try { $MIRROR = (require "$root/scripts/import/_reggie.php")('scvhistory.com'); }
catch (\Throwable $t) { $mirrorNote = 'The mirror is not visible inside the container, so the page was not re-read here; the credit is the extract\'s, '
    . 'which records the page\'s sha256. Checked on the host on 5 October 2026: the mirror page\'s sha256 matches the extract\'s, and its only credit is line 167, '
    . '`<font class="byline">News reports courtesy of Stan Walker</font>`.'; }

echo "# Mentry documents, the Stan Walker credit: dry run, 5 October 2026\n\n";
echo "Script: `scripts/import/fix_mentry_stan_walker_2026_10_05.php`. Source: the silent-faults audit, finding 8.\n\n";
if ($mirrorNote) { echo "$mirrorNote\n\n"; }
echo "Jordy (the originals) was not mounted, so the originals were not checked.\n\n";

$plan = []; $left = []; $refused = [];
foreach ($TARGETS as $id => $key) {
    $e = Entry::find()->id($id)->section('documents')->status(null)->one();
    if (!$e) { $refused[] = "#$id: no documents entry"; continue; }
    $has = []; foreach ($e->getFieldLayout()->getCustomFields() as $f) { $has[$f->handle] = true; }
    if (!isset($has['webmasterNoteBottom'], $has['legacyKey'])) { $refused[] = "#$id: no webmasterNoteBottom or legacyKey field"; continue; }
    if ((string)$e->getFieldValue('legacyKey') !== $key) { $refused[] = "#$id: legacyKey is not $key"; continue; }
    $page = $itemPage[$key] ?? null;
    if (!$page) { $refused[] = "#$id: $key is not in the extract"; continue; }
    $credits = array_values(array_unique($pageCredit[$page] ?? []));
    if (count($credits) > 1) { $refused[] = "#$id: the page prints more than one credit: " . implode(' / ', $credits); continue; }
    $credit = $credits[0] ?? '';
    if ($credit !== '' && preg_match('~\x{2014}~u', $credit)) { $refused[] = "#$id: em dash in the credit"; continue; }

    $pageLine = 'not re-read (see above)';
    if ($MIRROR !== null) {
        $path = $MIRROR . ($src['pages'][$page]['path'] ?? '');
        $raw = (string)@file_get_contents($path);
        if ($raw === '' || hash('sha256', $raw) !== ($src['pages'][$page]['sha256'] ?? '')) { $refused[] = "#$id: mirror page $path missing or changed since the extract"; continue; }
        $printed = $credit !== '' && str_contains($raw, '<font class="byline">' . $credit . '</font>');
        if ($credit !== '' && !$printed) { $refused[] = "#$id: the page does not print \"$credit\" as its byline"; continue; }
        $pageLine = $printed ? 'yes, as the page byline' : 'no credit printed';
    }

    $cur = (string)$e->getFieldValue('webmasterNoteBottom');
    echo "## #$id {$e->title} ($key, page {$src['pages'][$page]['path']})\n\n";
    echo "- webmasterNoteBottom now: " . json_encode($cur, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
    echo "- The page prints: " . ($credit !== '' ? json_encode($credit, JSON_UNESCAPED_UNICODE) : '(no credit)') . "\n";
    echo "- Checked in the mirror page here: $pageLine\n";
    if ($cur === $credit) {
        echo "- Decision: LEAVE. The note is the credit the page prints, verbatim. Nothing to change.\n\n";
        $left[] = "#$id";
        continue;
    }
    echo "- Decision: REPLACE with " . ($credit !== '' ? json_encode($credit, JSON_UNESCAPED_UNICODE) : 'nothing (the page prints no credit)') . "\n\n";
    $plan[$id] = [$e, $credit];
}

$n = 0;
if ($APPLY) {
    foreach ($plan as $id => [$e, $credit]) {
        $e->setFieldValue('webmasterNoteBottom', $credit);
        if (!\Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); }
        if ((string)Entry::find()->id($id)->status(null)->one()->getFieldValue('webmasterNoteBottom') !== $credit) { throw new \RuntimeException("#$id did not read back"); }
        $n++;
    }
    if ($n) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('fix_mentry_stan_walker_2026_10_05.php', $n, "read back $n", 'Mentry credit line set to what the page prints'); }
}

echo "## Summary\n\n";
echo "- Left as they are (the page prints the credit): " . (count($left) ? implode(', ', $left) : 'none') . "\n";
echo "- To change: " . (count($plan) ? implode(', ', array_map(fn($i) => "#$i", array_keys($plan))) : 'none') . "\n";
echo "- Refused: " . count($refused) . "\n";
foreach ($refused as $r) { echo "  - $r\n"; }
echo "- " . ($APPLY ? "Written: $n." : 'Nothing written.') . " A second run changes nothing.\n";
