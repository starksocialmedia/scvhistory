/**
 * education: one record per person per school.
 *
 * Nathan, 29 September 2026: alumni on school records, the same shape as
 * officeHolding. A relation cannot hold "class of 1951" or say whether the
 * person graduated, so the attendance becomes a record joining a person and a
 * school, with its own years, outcome, evidence and footnotes:
 *
 *   educationPerson     who studied: a person, or a war memorial casualty,
 *                       because the first alumni the archive can name are
 *                       the eight casualties whose high school it records
 *   educationSchool     the school, an organization
 *   educationYears      the years as printed, "1947-1951"
 *   educationYearsEdtf  the same in EDTF, "1947/1951", "../1951"
 *   classOf             the graduating class as printed, "1951"
 *   educationOutcome    graduated, attended, or unknown. "Class of" names a
 *                       class, not a diploma, so it is graduated only where a
 *                       source says so.
 *   educationEvidence   the officeHolding scale: certified (the school's own
 *                       record), contemporary (a yearbook, a program, a report
 *                       of the time), retrospective (an obituary or later
 *                       account), roster (an undated list), uncited
 *
 * The school page lists the alumni the archive holds; the person and casualty
 * pages list the schools. schema.org alumniOf is the external term.
 *
 * NO URLS: a record is rendered by the school's page and the person's, as
 * officeHolding is. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_education.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database and to project config' . PHP_EOL; }

$SECTION = 'educations'; $SECTION_NAME = 'Education';
$TYPE = 'education'; $TYPE_NAME = 'Education';
$EVIDENCE = ['certified' => 'Certified: the school\'s own record', 'contemporary' => 'Contemporary: a yearbook, program or report',
    'retrospective' => 'Retrospective account', 'roster' => 'Undated roster', 'uncited' => 'Uncited'];
$NEW_FIELDS = [
    'educationPerson'    => ['entries', 'Person', ['sources' => ['persons', 'warMemorials'], 'maxRelations' => 1]],
    'educationSchool'    => ['entries', 'School', ['sources' => ['organizations'], 'maxRelations' => 1]],
    'educationYears'     => ['plain', 'Years, as printed', []],
    'educationYearsEdtf' => ['plain', 'Years, EDTF', []],
    'classOf'            => ['plain', 'Class of, as printed', []],
    'educationOutcome'   => ['dropdown', 'Outcome', ['options' => ['graduated' => 'Graduated', 'attended' => 'Attended', 'unknown' => 'Not known']]],
    'educationEvidence'  => ['dropdown', 'Evidence', ['options' => $EVIDENCE]],
];
$REUSED = ['footnotes', 'footnotesOn', 'editorNotes', 'recordProvenance'];

$fs = Craft::$app->getFields();
$svc = Craft::$app->getEntries();
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 76) . PHP_EOL;
$section = $svc->getSectionByHandle($SECTION);
echo 'section ' . $SECTION . ': ' . ($section ? 'exists' : 'would create, channel, URLs OFF (rendered by the school and person pages)') . PHP_EOL . PHP_EOL . 'fields:' . PHP_EOL;
foreach ($NEW_FIELDS as $h => [$kind, $label, $opts]) {
    $note = $kind === 'entries' ? '  -> ' . implode(', ', $opts['sources']) : ($kind === 'dropdown' ? '  ' . implode(', ', array_keys($opts['options'])) : '');
    if ($kind === 'entries') { foreach ($opts['sources'] as $src) { if (!$svc->getSectionByHandle($src)) { throw new \RuntimeException("add_education: section $src not found"); } } }
    printf("   %-19s %-9s %s%s\n", $h, $kind, $fs->getFieldByHandle($h) ? 'exists already' : 'would create', $note);
}
echo PHP_EOL . 'reused as-is: ' . implode(', ', array_map(fn($h) => $h . ($fs->getFieldByHandle($h) ? '' : ' (MISSING)'), $REUSED)) . PHP_EOL;
if (!$APPLY) { echo PHP_EOL . str_repeat('=', 76) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL . 'then: import_wm_high_schools.php.' . PHP_EOL; return; }

/* Fields, then the entry type carrying them, then the section with the type
   attached: Craft 5 refuses a section with no entry type (24 September). */
$created = 0; $forLayout = [];
foreach ($NEW_FIELDS as $h => [$kind, $label, $opts]) {
    $f = $fs->getFieldByHandle($h);
    if (!$f) {
        $f = match ($kind) { 'entries' => new \craft\fields\Entries(), 'dropdown' => new \craft\fields\Dropdown(), default => new \craft\fields\PlainText() };
        $f->name = $label; $f->handle = $h;
        if ($kind === 'entries') { $f->sources = array_map(fn($s) => 'section:' . $svc->getSectionByHandle($s)->uid, $opts['sources']); $f->maxRelations = $opts['maxRelations']; }
        if ($kind === 'dropdown') { $f->options = array_map(fn($v, $l) => ['label' => $l, 'value' => $v, 'default' => false], array_keys($opts['options']), array_values($opts['options'])); }
        if (!$fs->saveField($f)) { throw new \RuntimeException("add_education: field $h refused: " . json_encode($f->getErrors())); }
        $created++; $f = $fs->getFieldByHandle($h);
    }
    $forLayout[] = $f;
}
foreach ($REUSED as $h) { if ($f = $fs->getFieldByHandle($h)) { $forLayout[] = $f; } }
$type = $svc->getEntryTypeByHandle($TYPE);
if (!$type) {
    $type = new \craft\models\EntryType(['name' => $TYPE_NAME, 'handle' => $TYPE, 'hasTitleField' => false,
        'titleFormat' => '{educationPerson.one().title ?? \'Unknown person\'} at {educationSchool.one().title ?? \'unknown school\'}']);
    $layout = new \craft\models\FieldLayout(['type' => \craft\elements\Entry::class]);
    $tab = new \craft\models\FieldLayoutTab(['name' => 'Education', 'layout' => $layout]);
    $tab->setElements(array_map(fn($f) => new \craft\fieldlayoutelements\CustomField($f), $forLayout));
    $layout->setTabs([$tab]); $type->setFieldLayout($layout);
    if (!$svc->saveEntryType($type)) { throw new \RuntimeException('add_education: entry type refused: ' . json_encode($type->getErrors())); }
    $created++; $type = $svc->getEntryTypeByHandle($TYPE);
}
if (!$section) {
    $section = new \craft\models\Section(['name' => $SECTION_NAME, 'handle' => $SECTION, 'type' => \craft\models\Section::TYPE_CHANNEL, 'enableVersioning' => true,
        'siteSettings' => array_map(fn($site) => new \craft\models\Section_SiteSettings(['siteId' => $site->id, 'enabledByDefault' => true, 'hasUrls' => false]), Craft::$app->getSites()->getAllSites())]);
    $section->setEntryTypes([$type]);
    if (!$svc->saveSection($section)) { throw new \RuntimeException('add_education: section refused: ' . json_encode($section->getErrors())); }
    $created++;
}
Craft::$app->getFields()->refreshFields();
$t2 = $svc->getEntryTypeByHandle($TYPE); $s2 = $svc->getSectionByHandle($SECTION);
$present = $t2 ? array_map(fn($c) => $c->handle, $t2->getFieldLayout()->getCustomFields()) : [];
$missing = array_values(array_diff(array_merge(array_keys($NEW_FIELDS), $REUSED), $present));
$ok = $s2 && $t2 && !$missing && !array_filter($s2->getSiteSettings(), fn($ss) => $ss->hasUrls);
echo 'READ-BACK ' . ($ok ? 'OK: section, type, ' . count($present) . ' fields, URLs off' : 'FAIL' . ($missing ? ': missing ' . implode(', ', $missing) : '')) . PHP_EOL;
if (!$ok) { throw new \RuntimeException('add_education read-back failed'); }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_education.php', $created, 'verified: section, type, ' . count($present) . ' fields, URLs off', 'education as records: person, school, years, class, outcome, evidence');
