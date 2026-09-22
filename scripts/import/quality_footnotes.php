/**
 * Quality pass 4 of 5: unconverted footnotes.
 *
 * Two jobs, both exact or not done.
 *
 * NOTES STILL IN THE BODY. A piece whose notes sit at the foot of its body
 * under a "Notes" heading gets them moved into footnotes rows, and the heading
 * and the block leave the body. Only when the block is the end of the body,
 * starts at 1, runs without a gap, and holds nothing but numbered notes. The
 * Source column is set per record, below, because who wrote the notes is a
 * fact about the record and not something to infer from the text.
 *
 * MARKERS WHOSE NOTES ARE ON A SHARED PAGE. survey_footnotes.php found twelve
 * pieces whose notes are on the series' notes page. This sets footnotesOn to
 * that page, but only where the page proves it: the page has a section headed
 * with the piece's chapter number ("2. Rancho San Francisco" for "2. Rancho San
 * Francisco", "1. The Time of Trees" for "1. Early Inhabitants"), and every
 * marker in the piece is a note number in that section. Measured on 22
 * September:
 *
 *   Perkins, Editor's Notes #1432   sectioned by chapter, numbering continuous
 *                                   across sections. Chapters 1, 2, 3, 4 and 6
 *                                   prove out. History of Pico Canyon Oil
 *                                   Production and History of Downtown Newhall
 *                                   are not numbered chapters and their marker
 *                                   numbers belong to other sections.
 *   Reynolds, Notes #2173           one unsectioned run, 1 to 8, while the
 *                                   chapters' markers restart at 1 (chapter 70
 *                                   has [1][2][3], chapter 55 has [1][2]). No
 *                                   chapter can be proved against it.
 *
 * Everything not proved is listed for a person.
 *
 * Dry run by default, with before and after counts from the quality report.
 * Run:   ddev craft exec "eval(file_get_contents('scripts/import/quality_footnotes.php'))"
 * Apply: ddev craft exec '$QUALITY_APPLY = true; eval(file_get_contents("scripts/import/quality_footnotes.php"));'
 */

$APPLY = false;
if (!empty($QUALITY_APPLY)) { $APPLY = true; echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$run = require \Craft::getAlias('@root') . '/scripts/import/_quality_pass.php';

/* Who wrote the notes held in a body, per record. Perkins' 1957 land-grant
   study carries his own citations. The Reynolds Castaic chapter's notes are the
   editor's corrections ("Reynolds said 1916; however ..."). */
$BLOCK_SOURCE = [1434 => 'author', 2175 => 'editor'];

$MARKER  = '/(?<![A-Za-z0-9])\[\s*(\d{1,3})\s*\](?!\d)/';
$HEADING = '/^\s*(notes?|footnotes?|end\s?notes?|references?|sources?\s+and\s+notes)\s*:?\s*$/i';
$NOTES_SLUG = '/(^|-)(notes|footnotes|endnotes|editors-notes|editor-notes)($|-)/i';

/* A notes page's sections: chapter number => note numbers under it. A section
   heading is "N. Title" on one line; a note is "N." on its own line or "N. text". */
$sectionsOf = function (string $body): array {
    $L = array_values(array_filter(array_map('trim', explode("\n", $body)), 'strlen'));
    $out = []; $cur = null;
    foreach ($L as $i => $l) {
        /* A heading has a title with no full stop in it, and the next line
           opens a note or another heading. A note's text has full stops. */
        if (preg_match('~^(\d{1,3})\.\s+(\p{Lu}[^.]{2,60})$~u', $l, $m) && preg_match('~^\d{1,3}\.~', $L[$i + 1] ?? '')) {
            $cur = (int)$m[1]; $out[$cur] = []; continue;
        }
        if ($cur !== null && preg_match('~^(\d{1,3})\.(\s|$)~', $l, $m)) { $out[$cur][] = (int)$m[1]; }
    }
    return $out;
};

/* Notes pages per collection, found the survey's way: a member whose slug says notes. */
$notesPage = [];
foreach (\craft\elements\Entry::find()->section('collections')->status(null)->limit(null)->all() as $c) {
    foreach (\craft\elements\Entry::find()->relatedTo(['targetElement' => $c, 'field' => 'partOfCollection'])->status(null)->limit(null)->all() as $x) {
        if (preg_match($NOTES_SLUG, $x->slug)) { $notesPage[(int)$c->id] = ['id' => (int)$x->id, 'title' => $x->title, 'sections' => $sectionsOf((string)$x->body)]; }
    }
}
foreach ($notesPage as $cid => $np) {
    echo 'notes page for collection #' . $cid . ': #' . $np['id'] . ' ' . $np['title'] . ', '
       . ($np['sections'] ? count($np['sections']) . ' chapter sections (' . implode(', ', array_keys($np['sections'])) . ')' : 'no chapter sections') . PHP_EOL;
}

$run([
    'script' => 'quality_footnotes.php',
    'label' => 'Unconverted footnotes',
    'apply' => $APPLY,
    'classes' => ['footnotes'],
    'propose' => function (\craft\elements\Entry $e, int $cid, array &$listed) use ($BLOCK_SOURCE, $MARKER, $HEADING, $NOTES_SLUG, $notesPage): ?array {
        if (preg_match($NOTES_SLUG, $e->slug)) { return null; }
        $layout = $e->getFieldLayout();
        if (!$layout?->getFieldByHandle('footnotes')) { return null; }
        $body = (string)$e->getFieldValue('body');
        $rows = (array)$e->getFieldValue('footnotes');
        $on = $e->footnotesOn->status(null)->ids();
        preg_match_all($MARKER, $body, $mm);
        $markers = array_map('intval', $mm[1]);

        /* 1. a notes block at the foot of the body */
        $L = explode("\n", $body);
        $h = null;
        foreach ($L as $i => $l) { if (preg_match($HEADING, $l)) { $h = $i; } }
        if ($h !== null) {
            if ($rows) { $listed[] = 'notes heading in the body and footnotes rows already held, kept'; return null; }
            if (!isset($BLOCK_SOURCE[$e->id])) { $listed[] = 'notes block in the body, but no Source decided for this record, kept'; return null; }
            $notes = []; $cur = null; $stray = [];
            foreach (array_slice($L, $h + 1) as $l) {
                $t = trim($l);
                if ($t === '') { continue; }
                if (preg_match('~^(\d{1,3})\.\s*(.*)$~u', $t, $m)) { $cur = (int)$m[1]; $notes[] = [$cur, $m[2]]; continue; }
                if ($cur === null) { $stray[] = $t; continue; }
                $notes[count($notes) - 1][1] = trim($notes[count($notes) - 1][1] . ' ' . $t);
            }
            $nums = array_column($notes, 0);
            if ($stray) { $listed[] = 'text between the notes heading and note 1 ("' . mb_substr($stray[0], 0, 50) . '"), kept'; return null; }
            if (!$notes || $nums !== range(1, count($nums))) { $listed[] = 'notes block is not numbered 1 to N without a gap, kept'; return null; }
            if ($h < count($L) * 0.5) { $listed[] = 'notes heading is not in the second half of the body, kept'; return null; }
            $new = [];
            foreach ($notes as [$n, $txt]) { $new[] = ['col1' => (string)$n, 'col2' => $txt, 'col3' => $BLOCK_SOURCE[$e->id]]; }
            $kept = rtrim(implode("\n", array_slice($L, 0, $h)));
            $missing = array_diff(array_unique($markers), $nums);
            if ($missing) { $listed[] = 'markers with no note in the block: ' . implode(', ', $missing) . ' (converted anyway; the block is what the source holds)'; }
            return ['set' => ['body' => $kept, 'footnotes' => $new],
                    'show' => ['- ' . trim($L[$h]) . ' and ' . count($notes) . ' notes below it',
                               '+ footnotes rows 1-' . count($notes) . ', source ' . $BLOCK_SOURCE[$e->id],
                               '  first: ' . mb_substr($notes[0][1], 0, 80),
                               '  last:  ' . mb_substr(end($notes)[1], 0, 80)]];
        }

        /* 2. markers whose notes are on the series notes page */
        if (!$markers || $rows || $on) { return null; }
        $np = $notesPage[$cid] ?? null;
        if (!$np) { $listed[] = count($markers) . ' markers, no notes on the record and no notes page in the collection: orphan'; return null; }
        if (!preg_match('~^(\d{1,3})\.\s~', $e->title, $tm) || !isset($np['sections'][(int)$tm[1]])) {
            $listed[] = count($markers) . ' markers; ' . $np['title'] . ' #' . $np['id'] . ' has no section for this piece, not linked';
            return null;
        }
        $sec = $np['sections'][(int)$tm[1]];
        $outside = array_diff(array_unique($markers), $sec);
        if ($outside) {
            $listed[] = 'markers ' . implode(', ', $outside) . ' are not notes in section ' . $tm[1] . ' of #' . $np['id'] . ', not linked';
            return null;
        }
        return ['set' => ['footnotesOn' => [$np['id']]],
                'show' => ['+ footnotesOn -> #' . $np['id'] . ' ' . $np['title'] . ', section ' . $tm[1]
                           . ' holds notes ' . min($sec) . '-' . max($sec) . ', markers ' . implode(',', array_unique($markers))]];
    },
]);
