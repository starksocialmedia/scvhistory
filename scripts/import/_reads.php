<?php
/**
 * What a script read, stated before it reports any number. Returns a callable; require it and call it first.
 *
 *   $reads = require \Craft::getAlias('@root') . '/scripts/import/_reads.php';
 *   $reads([
 *     ['file', 'every stored asset file, hashed', '@webroot/uploads/archive-media'],
 *     ['record', 'Craft field sourceChecksum', 'the master on Reggie', 'read'],
 *     ['record', 'Craft fields width, height', 'the stored file', 'not read: the census counts pictures, not sizes'],
 *   ]);
 *
 * Nathan, 8 October 2026: "Before a census reports a number it states what it read, and reading a Craft field about a
 * file is not reading the file. Where a census could read the file itself and does not, it says so in its own output."
 * Four counts that week were wrong for the same reason: a tab title, the index link text, a folder's contents and the
 * asset records were each read in place of the thing they described (ERRORLOG, "a census that counts a derived copy").
 *
 * Each entry is one of:
 *   ['file', what, path]                    the thing itself, opened by this script. The path is checked here: if it is
 *                                           not there, the line says NOT READ, whatever the script meant to do.
 *   ['record', what, describes, 'read']     a description of a file (a Craft field, a manifest, an index, a title),
 *                                           and the file it describes was also read by this script.
 *   ['record', what, describes, 'not read: why']
 *                                           the file it describes could be read and was not; the why is required.
 *   ['record', what, describes, 'cannot be read: why']
 *                                           the file it describes is not held, or not reachable.
 * check_census_reads.php (run by check_render) fails a script that prints before it calls this, a script that reads a
 * Craft field about a file without listing it here, and one that declares a file read with no call in it that reads one.
 */
return function (array $entries): void {
    $out = ['READ, before any number:'];
    $bad = [];
    foreach ($entries as $e) {
        [$kind, $what] = $e;
        if ($kind === 'file') {
            $p = \Craft::getAlias((string)($e[2] ?? ''), false) ?: (string)($e[2] ?? '');
            $out[] = (is_file($p) || is_dir($p)) ? "  the file itself: $what" : "  NOT READ: $what ($p is not there)";
        } elseif ($kind === 'record') {
            $state = (string)($e[3] ?? '');
            $about = (string)($e[2] ?? '');
            if ($state === 'read') { $out[] = "  a record about a file: $what, describing $about; the file was read too"; }
            elseif (str_starts_with($state, 'not read: ') && strlen($state) > 12) { $out[] = "  A RECORD, NOT THE FILE: $what describes $about; the file could be read and was not (" . substr($state, 10) . ')'; }
            elseif (str_starts_with($state, 'cannot be read: ') && strlen($state) > 18) { $out[] = "  A RECORD, NOT THE FILE: $what describes $about; the file cannot be read here (" . substr($state, 16) . ')'; }
            else { $bad[] = "$what: say whether $about was read, and if not, why"; }
        } else { $bad[] = "$what: kind must be file or record"; }
    }
    if ($bad) { throw new \RuntimeException('reads(): ' . implode('; ', $bad)); }
    echo implode(PHP_EOL, $out) . PHP_EOL;
};
