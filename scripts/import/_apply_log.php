<?php
/**
 * The shared apply log. Returns a callable; require it and call it.
 *
 *   $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
 *   $applyLog('fix_place_gnis_ids.php', 2, 'verified 2 of 2');
 *
 * WHY THIS EXISTS
 *
 * On 21 September two place records changed their GNIS ids and nothing in the
 * project could say what had written them. The values were right and the
 * provenance string named the script, but the question "who ran this, and when"
 * took a revision-table query, a transcript search and two wrong guesses, and
 * was settled in the end only because a person remembered pasting a block whose
 * output had scrolled past.
 *
 * An apply changes the database and then leaves no trace of itself outside the
 * data it wrote. This is that trace. One line, appended, committed, so the next
 * time the question comes up it is a file read rather than an investigation.
 *
 * Committed on purpose. The log is small, it is append-only, and its value is
 * entirely in being there later on somebody else's machine.
 *
 * Tab separated: an aligned column is easier to read until the day a script
 * name grows and every historical line is misaligned against it.
 */

return function (string $script, int $rows, string $readback = '', string $note = ''): void {
    $path = \Craft::getAlias('@root') . '/scripts/import/APPLIED.log';

    $line = implode("\t", [
        (new DateTime())->format('Y-m-d H:i:s T'),
        $script,
        gethostname() ?: '?',
        $rows . ' rows',
        $readback !== '' ? $readback : '-',
        $note !== '' ? $note : '-',
    ]);

    /* Created with a header the first time, so the file explains its own
       columns to whoever opens it without this script in front of them. */
    if (!file_exists($path)) {
        $header = "# Applies that wrote to the database. Appended by scripts/import/_apply_log.php.\n"
                . "# when\tscript\thost\trows\tread-back\tnote\n";
        file_put_contents($path, $header, LOCK_EX);
    }

    file_put_contents($path, $line . "\n", FILE_APPEND | LOCK_EX);
    echo 'logged to scripts/import/APPLIED.log' . PHP_EOL;
};
