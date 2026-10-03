<?php
/**
 * The mirror pages cited by the 3 October 2026 re-search scripts
 * (fix_no_source_notes_2026_10_03.php, widen_couts_profile.php,
 * restore_california_battalion.php), read from their byte-for-byte copies in
 * inventory/sources/no-source-fixes-2026-10-03/ and checked against the hashes in
 * its manifest. Where Reggie is mounted, each copy is also checked against the
 * mirror itself. Returns ['text' => fn($key) => string, 'has' => fn($key, $phrase) => bool,
 * 'bad' => [...], 'mirror' => bool].
 *
 *   $SRC = require \Craft::getAlias('@root') . '/scripts/import/_source_texts.php';
 *
 * Text is compared after folding curly quotes, apostrophes and spaces, so a
 * quotation in a script is checked against what the page says, not against
 * how its quotation marks were typed.
 */

$dir = \Craft::getAlias('@root') . '/inventory/sources/no-source-fixes-2026-10-03';
$man = json_decode((string)@file_get_contents("$dir/manifest.json"), true)['files'] ?? [];
$bad = []; $T = []; $mirror = is_dir('/mnt/reggie/scvhistory.com');
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{00A0}"], ["'", "'", '"', '"', ' '], (string)$s)));
if (!$man) { $bad[] = 'the source manifest is missing'; }
foreach ($man as $f => $m) {
    $raw = @file_get_contents("$dir/$f");
    if ($raw === false || hash('sha256', $raw) !== $m['sha256']) { $bad[] = "source copy $f is missing or has changed"; continue; }
    if ($mirror) { $live = @file_get_contents('/mnt/reggie/scvhistory.com' . $m['legacyPath']); if ($live !== false && hash('sha256', $live) !== $m['sha256']) { $bad[] = "the mirror's {$m['legacyPath']} no longer matches the copy"; } }
    $T[preg_replace('~\.[a-z]+$~', '', $f)] = $ws(@file_get_contents("$dir/{$m['text']}"));
}
return [
    'text' => fn(string $k): string => $T[$k] ?? '',
    'has' => fn(string $k, string $phrase): bool => isset($T[$k]) && str_contains($T[$k], $ws($phrase)),
    'ws' => $ws,
    'bad' => $bad,
    'mirror' => $mirror,
];
