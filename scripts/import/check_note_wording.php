/**
 * READ ONLY. Notes and footnotes are public (Nathan, 3 October 2026): a reader
 * who knows nothing of how the archive was built should not meet its
 * machinery. Fails on any editor note or footnote, other than Leon Worden's,
 * or asset provenance sentence (source),
 * that names the import, WordPress, migration, the mirror, file paths,
 * checksums, scripts, to-dos or the people doing the work, or that narrates
 * the research itself ("read as a search summary", "searched 2 October and not
 * found", "has not been read"; Nathan, 3 October 2026): a note says what the
 * source holds.
 *
 * The earlier scripts that wrote such wording (reworded by
 * reword_public_notes.php) match notes by text, so a re-run of one would add
 * its old note back beside the new; this check is what catches that.
 * A source named as a person ("per Nathan Imhoff, 2 October 2026") is an
 * attribution, not process, and is not matched; whether to keep it is
 * Nathan's call (two term ends rest on it).
 * URLs are set aside before matching ("wp-content/.../migration/" is a City
 * address, not our process).
 * Returns ['ok' => bool, 'fails' => [...]] for check_render.php.
 * Run alone: ddev craft exec "eval(file_get_contents('scripts/import/check_note_wording.php'))"
 */

if ((int)ini_get('memory_limit') !== -1 && (int)ini_get('memory_limit') < 2048) { ini_set('memory_limit', '2048M'); }
$BAD = '~\b(WordPress|the import|on import|imported from|migrated|migration|legacy mirror|in the mirror|inventory/|SHA-?(1|256)|checksums?|manifest|dry run|the script|scripts? (that|which)|next to try|to try next|with Nathan|Nathan\'s|Claude(?! Parrish)|image tag|commented out|read so far|search summary|page was blocked|ha(s|ve) not been (read|checked)|could not be read|[Ss]earched \d|sources searched|lists searched|release search)\b~i';
$fails = [];
foreach (\craft\elements\Entry::find()->status(null)->each(200) as $e) {
    $l = $e->getFieldLayout();
    foreach (['editorNotes', 'footnotes'] as $fld) {
        if (!$l->getFieldByHandle($fld)) { continue; }
        foreach ($e->getFieldValue($fld) ?? [] as $i => $r) {
            if (!is_array($r) || str_starts_with((string)($r['source'] ?? ''), 'legacy')) { continue; }
            $t = preg_replace('~https?://\S+|\S+\.(?:com|org|gov|net)/\S*~', ' ', ($r['heading'] ?? '') . ' ' . ($r['note'] ?? ''));
            if (preg_match($BAD, $t, $m)) { $fails[] = "NOTE WORDING  {$e->section->handle} #{$e->id} {$e->title} ($fld $i): \"{$m[0]}\""; }
        }
    }
}
/* Provenance sentences on /media are public too (3 October 2026): checksums belong in sourceChecksum. */
foreach (\craft\elements\Asset::find()->each(500) as $a) {
    $l = $a->getFieldLayout(); if (!$l || !$l->getFieldByHandle('source')) { continue; }
    $t = preg_replace('~https?://\S+~', ' ', (string)$a->getFieldValue('source'));
    if (preg_match('~\b(SHA-?(1|256)|re-encoded|legacy mirror|manifest|on import|per Nathan)\b~i', $t, $m)) { $fails[] = "PROVENANCE WORDING  asset #{$a->id} {$a->filename}: \"{$m[0]}\""; }
}
echo ($fails ? count($fails) . ' public notes name the archive\'s own machinery' . PHP_EOL . implode(PHP_EOL, $fails) : 'no public note names the archive\'s own machinery') . PHP_EOL;
return ['ok' => !$fails, 'fails' => $fails];
