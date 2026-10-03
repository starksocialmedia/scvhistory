/**
 * READ ONLY. A claim deleted as impossible must not come back (Nathan,
 * 3 October 2026: "so nobody restores it later"). Fails if any sentence in
 * scripts/import/removed-claims.json is found in any record's body, author bio
 * or caption.
 * Returns ['ok' => bool, 'fails' => [...]] for check_render.php.
 * Run alone: ddev craft exec "eval(file_get_contents('scripts/import/check_removed_claims.php'))"
 */

ini_set('memory_limit', '2048M');
$C = json_decode((string)file_get_contents(\Craft::getAlias('@root') . '/scripts/import/removed-claims.json'), true)['claims'] ?? [];
$fails = [];
if ($C) {
    $norm = fn($s) => preg_replace('~\s+~u', ' ', str_replace("\u{00A0}", ' ', html_entity_decode(strip_tags((string)$s), ENT_QUOTES)));
    foreach (\craft\elements\Entry::find()->status(null)->each(200) as $e) {
        $l = $e->getFieldLayout(); $t = '';
        foreach (['body', 'authorBio', 'photoCaptionExt', 'wmNarrative'] as $f) { if ($l->getFieldByHandle($f)) { $t .= ' ' . $e->getFieldValue($f); } }
        $t = $norm($t);
        foreach ($C as $c) { if (str_contains($t, $norm($c['claim']))) { $fails[] = "REMOVED CLAIM BACK  {$e->section->handle} #{$e->id} {$e->title}: \"" . mb_substr($c['claim'], 0, 80) . '"'; } }
    }
}
echo ($fails ? implode(PHP_EOL, $fails) : 'no removed claim has come back (' . count($C) . ' listed)') . PHP_EOL;
return ['ok' => !$fails, 'fails' => $fails];
