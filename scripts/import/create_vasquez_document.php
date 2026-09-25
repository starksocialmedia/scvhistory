/**
 * Makes the 1874 Vasquez sketch a document record, and takes it out of the
 * person's body.
 *
 * Person #285's body is not a biography anybody here wrote. It is a published
 * item of 1874: "TIBURCIO VASQUEZ! A Brief Sketch of the Notorious Bandit",
 * entered for copyright that year by V. Wolfenstein of Los Angeles, datelined
 * Los Angeles, May 19th 1874 — five days after Vasquez was captured at Greek
 * George's. The archive holds it as JE4001, a 1200 dpi scan from a copy print.
 *
 * Two things were lost when it became a person's body: the copyright line and
 * the headline, both of which are on the legacy page and neither of which is
 * in the record. A document record carries them, and carries a publisher and a
 * date, which a person's body cannot.
 *
 * SOURCE. The text comes from inventory/legacy/vasquez-je4001.json, fetched
 * from the legacy page with its URL and date recorded, not retyped and not
 * summarised. Verbatim, as the extraction contract requires.
 *
 * WHAT HAPPENS TO #285's BODY. It is emptied, and bodyAuthorship with it. The
 * text has not been deleted: it lives in the document, which is a record with
 * a publisher, a date and its own page, and the document points at the person
 * through subjectPerson. A person's body is for what the archive says about
 * the person, and until an editorial profile is written the honest content is
 * nothing at all. The birth-date correction stays on the person, because it is
 * a fact about the man rather than about the document; the note about the
 * original's paragraphing moves to the document, because it describes it.
 *
 * Needs add_document_publishing_fields.php applied first.
 *
 * Idempotent: a document with this legacyKey is found and left alone.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_vasquez_document.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$PERSON = 285;
$SRC = \Craft::getAlias('@root') . '/inventory/legacy/vasquez-je4001.json';

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

if (!is_file($SRC)) { echo 'source extract missing: ' . $SRC . PHP_EOL; return; }
$src = json_decode((string)file_get_contents($SRC), true);
$meta = $src['meta'];

$svc = Craft::$app->getEntries();
$section = $svc->getSectionByHandle('documents');
$type = $svc->getEntryTypeByHandle('document');
if (!$section || !$type) { echo 'documents section or document type missing' . PHP_EOL; return; }
$layout = $type->getFieldLayout();
$need = ['originallyPublishedTitle', 'originalPublishDate', 'sourceLine', 'subjectPerson'];
$missing = array_values(array_filter($need, fn($h) => !$layout->getFieldByHandle($h)));
if ($missing) {
    echo 'the document layout does not carry: ' . implode(', ', $missing) . PHP_EOL;
    echo 'run add_document_publishing_fields.php first.' . PHP_EOL;
    if ($APPLY) { return; }
}

$person = \craft\elements\Entry::find()->id($PERSON)->section('persons')->status(null)->one();
if (!$person) { echo 'person #' . $PERSON . ' not found' . PHP_EOL; return; }

/* The document's body is the text and nothing else.
 *
 * It used to be composed as headline + copyright line + text, which put both
 * in the body while the fields held them too, and then put the headline in
 * twice: once joined here, and once again inside body_lines, which start above
 * the centred lines the page printed. fix_document_body_duplication.php cleaned
 * that up on the Vasquez record; this is the fault it was cleaning up.
 *
 * The headline belongs in originallyPublishedTitle and the credit in
 * sourceLine, and templates/documents/_entry.twig prints them above the text in
 * the order the legacy page set them: credit, rule, headline, rule, text. So
 * the display matter is dropped from the front of body_lines here, and every
 * line dropped must equal what the fields are being given. */
$displayLines = [];
$normLine = fn(string $x): string => trim(preg_replace('~\s+~', ' ', $x));
$headNorm = $normLine((string)$src['headline']);
$displayLines[$headNorm] = true;
if (str_contains($headNorm, '!')) {
    [$firstPart, $restPart] = explode('!', $headNorm, 2);
    $displayLines[$normLine($firstPart . '!')] = true;
    $displayLines[$normLine($restPart)] = true;
}
$displayLines[$normLine((string)$src['copyright_line'])] = true;

$textLines = $src['body_lines'];
while ($textLines && isset($displayLines[$normLine((string)$textLines[0])])) {
    array_shift($textLines);
}
$droppedFromBody = count($src['body_lines']) - count($textLines);
$body = implode("\n\n", $textLines);

$fields = [
    'title' => 'A Brief Sketch of the Notorious Bandit',
    'slug' => 'a-brief-sketch-of-the-notorious-bandit-1874',
    'originallyPublishedTitle' => $src['headline'],
    'originalPublishDate' => 'May 19, 1874',
    'originalPublishDateEdtf' => '1874-05-19',
    'sourceLine' => $src['copyright_line'],
    'legacyKey' => $meta['legacy_key'],
    'legacyUrl' => '/scvhistory/' . $meta['legacy_key'] . '.htm',
    'sourcePath' => $meta['source_url'],
    'webmasterNoteBottom' => implode("\n\n", array_filter($src['webmaster_notes'], fn($n) => !str_starts_with($n, '*'))),
    'culturalSensitivityNote' => '',
    'recordProvenance' => 'text from ' . $meta['source_url'] . ', fetched ' . $meta['fetched']
        . '; moved out of person #' . $PERSON . "'s body, where it had arrived without its copyright line or headline",
    'body' => $body,   /* the text only: the headline and credit are fields */
    'recordDates' => [
        ['col1' => 'May 19th, 1874', 'col2' => '1874-05-19', 'col3' => 'day',  'col4' => 'dateline on the sketch', 'col5' => true],
        ['col1' => '1874',           'col2' => '1874-01-01', 'col3' => 'year', 'col4' => 'copyright entered by V. Wolfenstein, Los Angeles', 'col5' => true],
    ],
    'subjectPerson' => [$PERSON],
];

$existing = \craft\elements\Entry::find()->section('documents')->status(null)->legacyKey($meta['legacy_key'])->one();
echo 'source          ' . $meta['source_url'] . ', fetched ' . $meta['fetched'] . PHP_EOL;
echo 'existing record ' . ($existing ? '#' . $existing->id . ' ' . $existing->title . ' (nothing to create)' : 'none') . PHP_EOL;
echo PHP_EOL . 'WOULD CREATE, documents/document:' . PHP_EOL;
foreach ($fields as $h => $v) {
    if ($h === 'body') { continue; }
    $show = is_array($v) ? json_encode($v) : (string)$v;
    echo '   ' . str_pad($h, 26) . mb_substr(preg_replace('~\s+~', ' ', $show), 0, 96) . PHP_EOL;
}
echo '   ' . str_pad('body', 26) . mb_strlen($body) . ' characters, ' . count($textLines) . ' paragraphs'
   . ($droppedFromBody ? ' (' . $droppedFromBody . ' display line(s) left to the fields)' : '') . PHP_EOL;
echo PHP_EOL . '   first: ' . mb_substr($body, 0, 150) . PHP_EOL;
echo '   last:  ' . mb_substr($body, -90) . PHP_EOL;

$asset = \craft\elements\Asset::find()->filename($meta['legacy_key'] . '.jpg')->one();
echo PHP_EOL . 'the scan ' . $meta['legacy_key'] . '.jpg: ' . ($asset ? 'held, asset #' . $asset->id : 'NOT in the volume. /gif/je4001.jpg on the legacy site; bring it in with import_mirror_images.php when Reggie is mounted') . PHP_EOL;

/* What the person loses and keeps. */
$pBody = trim((string)$person->getFieldValue('body'));
$pNote = trim((string)$person->getFieldValue('personWebmasterNoteBottom'));
$keepNote = implode("\n\n", array_filter(preg_split('~\n\s*\n~', $pNote), fn($n) => str_starts_with(trim($n), '*')));
echo PHP_EOL . 'PERSON #' . $PERSON . ' ' . $person->title . PHP_EOL;
echo '   body                      ' . mb_strlen($pBody) . ' characters -> (empty), the text is the document' . PHP_EOL;
echo '   bodyAuthorship            ' . ((string)$person->getFieldValue('bodyAuthorship') ?: '(empty)') . ' -> (empty), there is no body to label' . PHP_EOL;
echo '   personWebmasterNoteBottom ' . mb_strlen($pNote) . ' characters -> ' . mb_strlen($keepNote) . ', keeping the birth-date correction' . PHP_EOL;
echo '   the document points back through subjectPerson' . PHP_EOL;

if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}
if ($existing) { echo 'the document already exists; leaving everything alone.' . PHP_EOL; return; }
if ($missing) { echo 'the document layout is not ready.' . PHP_EOL; return; }

$doc = new \craft\elements\Entry();
$doc->sectionId = $section->id;
$doc->setTypeId($type->id);
$doc->title = $fields['title'];
$doc->slug = $fields['slug'];
$values = $fields; unset($values['title'], $values['slug']);
$doc->setFieldValues($values);
if (!Craft::$app->getElements()->saveElement($doc)) {
    echo 'FAILED to create the document: ' . json_encode($doc->getErrors()) . PHP_EOL;
    throw new \RuntimeException('create_vasquez_document: the document was not created');
}
echo PHP_EOL . 'created document #' . $doc->id . PHP_EOL;

$person->setFieldValue('body', '');
$person->setFieldValue('bodyAuthorship', '');
$person->setFieldValue('personWebmasterNoteBottom', $keepNote);
if (!Craft::$app->getElements()->saveElement($person)) {
    echo 'FAILED to update the person: ' . json_encode($person->getErrors()) . PHP_EOL;
    throw new \RuntimeException('create_vasquez_document: the document exists but the person still holds the text');
}

/* Read back both records. */
$d = \craft\elements\Entry::find()->id($doc->id)->status(null)->one();
$p = \craft\elements\Entry::find()->id($PERSON)->status(null)->one();
$checks = [
    'document body length matches' => mb_strlen(trim((string)$d->getFieldValue('body'))) === mb_strlen($body),
    'document carries the copyright line' => str_contains((string)$d->getFieldValue('sourceLine'), 'WOLFENSTEIN'),
    'document dated 1874-05-19' => (string)$d->getFieldValue('originalPublishDateEdtf') === '1874-05-19',
    'document points at the person' => in_array($PERSON, $d->subjectPerson->status(null)->ids(), true),
    'person body is empty' => trim((string)$p->getFieldValue('body')) === '',
    'person kept the birth-date note' => str_contains((string)$p->getFieldValue('personWebmasterNoteBottom'), 'April 7'),
];
$bad = array_keys(array_filter($checks, fn($v) => !$v));
echo PHP_EOL . 'READ-BACK ' . ($bad ? 'FAIL' : 'OK') . PHP_EOL;
foreach ($checks as $what => $v) { echo '   ' . ($v ? 'ok   ' : 'FAIL ') . $what . PHP_EOL; }

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('create_vasquez_document.php', 2, ($bad ? 'SHORT: ' . implode('; ', $bad) : 'verified: document and person both read back'),
    'document #' . $doc->id . ' from JE4001, 1874, V. Wolfenstein; person #' . $PERSON . ' body emptied');
if ($bad) { throw new \RuntimeException('create_vasquez_document: read-back failed'); }
