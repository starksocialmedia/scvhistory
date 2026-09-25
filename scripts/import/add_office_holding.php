/**
 * officeHolding: one record per person per term of office.
 *
 * A term is a fact about three separate things, and the archive can currently
 * express only two of them badly. The roles section names offices (City Council
 * Member, Mayor, State Senator). The organizations section names bodies and
 * nests them (Planning Commission under The City of Santa Clarita). Nothing
 * holds the term itself, because a Craft relation carries no data: relating
 * Laurene Weste to City Council Member cannot say 1998 to 2022.
 *
 * So the term becomes a record, joining:
 *
 *   person   who served
 *   office   the role, not the body: "Member of the California State Assembly"
 *   body     the organization
 *   district the constituency, where the office has one. A place, because a
 *            district has boundaries and a lifespan and is not a body.
 *
 * WHY THE OFFICE AND THE DISTRICT ARE SEPARATE. The valley's Assembly district
 * was the 38th and is now the 40th; its congressional seat went from the 25th
 * to the 27th. If the office were "the 38th District seat", every redistricting
 * would invent a new office and a continuous career would fragment across
 * unrelated records. The office is the seat in the chamber; the district is who
 * it answers to; a career reads as one line with a changing number, which is
 * what happened.
 *
 * EVIDENCE SITS ON EACH DATE, NOT ON THE TERM. A term often has a firm start
 * and a vague end: an election result fixes the day somebody took office, while
 * the leaving is recorded years later in a retrospective paragraph. One
 * evidence level for the whole term would average those into a single claim
 * that is wrong at one end. So startEvidence and endEvidence are separate, and
 * both are rendered: the timeline draws a certified end square and an
 * uncertain one tapered, so the drawing cannot imply precision the source does
 * not have. The levels, strongest first:
 *
 *   certified     an election result, a certificate, a minuted oath or
 *                 resignation. The body's own record of the event.
 *   contemporary  a report written at the time, which the 106 council articles
 *                 in this archive mostly are.
 *   retrospective a later account: an obituary, an anniversary piece, a
 *                 profile written decades after.
 *   roster        a list of members with no date attached, so the year is the
 *                 roster's and the day is nobody's.
 *   uncited       nothing yet. A holding with an uncited date does not publish.
 *
 * Every level except uncited needs a footnotes row naming the source; the page
 * refuses to render a term whose dates cite nothing, the same rule the
 * editorial profiles use.
 *
 * NO URLS. A holding is rendered by the body's page and by the person's. Seven
 * hundred thin pages of their own would be worse than none, so the section is
 * created with URLs off.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_office_holding.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database and to project config' . PHP_EOL; }

$SECTION = 'officeHoldings';
$SECTION_NAME = 'Office Holdings';
$TYPE = 'officeHolding';
$TYPE_NAME = 'Office Holding';

/* handle => [class, label, settings]. Relations name their source section. */
$NEW_FIELDS = [
    'holdingPerson'   => ['entries', 'Person',   ['sources' => 'persons',       'maxRelations' => 1, 'required' => true]],
    'holdingOffice'   => ['entries', 'Office',   ['sources' => 'roles',         'maxRelations' => 1, 'required' => true]],
    'holdingBody'     => ['entries', 'Body',     ['sources' => 'organizations', 'maxRelations' => 1, 'required' => true]],
    'holdingDistrict' => ['entries', 'District', ['sources' => 'places',        'maxRelations' => 1]],
    'termStart'       => ['plain',   'Term start, as printed', []],
    'termStartEdtf'   => ['plain',   'Term start, EDTF', []],
    'termEnd'         => ['plain',   'Term end, as printed', []],
    'termEndEdtf'     => ['plain',   'Term end, EDTF', []],
    'seatLabel'       => ['plain',   'Seat or area', []],
    'selectionMethod' => ['dropdown', 'How the office was taken', ['options' => [
        'elected' => 'Elected', 'appointed' => 'Appointed', 'rotated' => 'Rotated by the body',
        'exofficio' => 'Ex officio', 'succeeded' => 'Succeeded mid-term']]],
    'howEnded'        => ['dropdown', 'How the term ended', ['options' => [
        'expired' => 'Term expired', 'reelected' => 'Re-elected', 'resigned' => 'Resigned',
        'died' => 'Died in office', 'recalled' => 'Recalled', 'termed-out' => 'Termed out',
        'left' => 'Left for another office', 'serving' => 'Still serving', 'unknown' => 'Unknown']]],
    'startEvidence'   => ['dropdown', 'Evidence for the start', ['options' => [
        'certified' => 'Certified: the body\'s own record', 'contemporary' => 'Contemporary report',
        'retrospective' => 'Retrospective account', 'roster' => 'Undated roster', 'uncited' => 'Uncited']]],
    'endEvidence'     => ['dropdown', 'Evidence for the end', ['options' => [
        'certified' => 'Certified: the body\'s own record', 'contemporary' => 'Contemporary report',
        'retrospective' => 'Retrospective account', 'roster' => 'Undated roster', 'uncited' => 'Uncited']]],
];

/* Reused as they are, so a holding cites the way everything else does. */
$REUSED = ['footnotes', 'footnotesOn', 'editorNotes', 'recordProvenance', 'recordDates'];

$fs = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 76) . PHP_EOL;

/* ----------------------------------------------------------- the section */
$section = $svc->getSectionByHandle($SECTION);
echo 'section ' . $SECTION . ': ' . ($section ? 'exists' : 'would create, channel, URLs OFF (rendered by the body and person pages)') . PHP_EOL;

/* ------------------------------------------------------------ the fields */
echo PHP_EOL . 'fields:' . PHP_EOL;
$missingSource = [];
foreach ($NEW_FIELDS as $handle => [$kind, $label, $opts]) {
    $have = $fs->getFieldByHandle($handle);
    $note = '';
    if ($kind === 'entries') {
        $src = $svc->getSectionByHandle($opts['sources']);
        if (!$src) { $missingSource[] = $opts['sources']; $note = '  SECTION ' . $opts['sources'] . ' NOT FOUND'; }
        else { $note = '  -> ' . $opts['sources']; }
    }
    if ($kind === 'dropdown') { $note = '  ' . implode(', ', array_keys($opts['options'])); }
    printf("   %-18s %-9s %s%s\n", $handle, $kind, $have ? 'exists already' : 'would create', $note);
}
echo PHP_EOL . 'reused as-is: ' . implode(', ', array_map(fn($h) => $h . ($fs->getFieldByHandle($h) ? '' : ' (MISSING)'), $REUSED)) . PHP_EOL;

/* ------------------------------------------------ what it would describe */
echo PHP_EOL . 'what the archive could express on day one:' . PHP_EOL;
$council = \craft\elements\Entry::find()->section('roles')->status(null)->title('City Council Member')->one();
$holders = $council ? \craft\elements\Entry::find()->section('persons')->status(null)->relatedTo(['targetElement' => $council, 'field' => 'roles'])->all() : [];
echo '   City Council Member is on ' . count($holders) . ' person records: '
   . implode(', ', array_map(fn($p) => $p->title . ' #' . $p->id, $holders)) . PHP_EOL;
echo '   none of them carries a term date, because there has been nowhere to put one' . PHP_EOL;
$bodies = \craft\elements\Entry::find()->section('organizations')->status(null)->orgType('government')->count();
echo '   organizations typed government: ' . $bodies . ' (the City and its Planning Commission)' . PHP_EOL;
$arts = 0;
foreach (\craft\elements\Entry::find()->section('articles')->status(null)->limit(null)->each() as $a) {
    if (stripos((string)$a->body, 'City Council') !== false) { $arts++; }
}
echo '   articles mentioning the City Council, the sourcing base: ' . $arts . PHP_EOL;

if ($missingSource) {
    echo PHP_EOL . 'STOPPING: these sections do not exist: ' . implode(', ', array_unique($missingSource)) . PHP_EOL;
    return;
}

if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 76) . PHP_EOL;
    echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    echo 'then: the missing body records, the district places, and the council roster.' . PHP_EOL;
    return;
}

/* ------------------------------------------------------------- creating */
$created = 0;
if (!$section) {
    $section = new \craft\models\Section([
        'name' => $SECTION_NAME, 'handle' => $SECTION, 'type' => \craft\models\Section::TYPE_CHANNEL,
        'enableVersioning' => true,
        'siteSettings' => array_map(fn($site) => new \craft\models\Section_SiteSettings([
            'siteId' => $site->id, 'enabledByDefault' => true, 'hasUrls' => false,
        ]), Craft::$app->getSites()->getAllSites()),
    ]);
    if (!$svc->saveSection($section)) { echo 'FAILED section: ' . json_encode($section->getErrors()) . PHP_EOL; return; }
    $created++;
    echo 'created the section' . PHP_EOL;
}

$fieldsForLayout = [];
foreach ($NEW_FIELDS as $handle => [$kind, $label, $opts]) {
    $f = $fs->getFieldByHandle($handle);
    if (!$f) {
        $f = match ($kind) {
            'entries' => new \craft\fields\Entries(),
            'dropdown' => new \craft\fields\Dropdown(),
            default => new \craft\fields\PlainText(),
        };
        $f->name = $label;
        $f->handle = $handle;
        if ($kind === 'entries') {
            $src = $svc->getSectionByHandle($opts['sources']);
            $f->sources = ['section:' . $src->uid];
            $f->maxRelations = $opts['maxRelations'] ?? null;
        }
        if ($kind === 'dropdown') {
            $f->options = array_map(fn($v, $l) => ['label' => $l, 'value' => $v, 'default' => false],
                array_keys($opts['options']), array_values($opts['options']));
        }
        if (!$fs->saveField($f)) { echo 'FAILED field ' . $handle . ': ' . json_encode($f->getErrors()) . PHP_EOL; return; }
        $created++;
        $f = $fs->getFieldByHandle($handle);
    }
    $fieldsForLayout[] = $f;
}
foreach ($REUSED as $h) { if ($f = $fs->getFieldByHandle($h)) { $fieldsForLayout[] = $f; } }

$type = $svc->getEntryTypeByHandle($TYPE);
if (!$type) {
    $type = new \craft\models\EntryType(['name' => $TYPE_NAME, 'handle' => $TYPE, 'hasTitleField' => false,
        'titleFormat' => '{holdingPerson.one().title} — {holdingOffice.one().title}, {holdingBody.one().title}']);
    $layout = new \craft\models\FieldLayout(['type' => \craft\elements\Entry::class]);
    $tab = new \craft\models\FieldLayoutTab(['name' => 'The term', 'layout' => $layout]);
    $tab->setElements(array_map(fn($f) => new \craft\fieldlayoutelements\CustomField($f), $fieldsForLayout));
    $layout->setTabs([$tab]);
    $type->setFieldLayout($layout);
    if (!$svc->saveEntryType($type)) { echo 'FAILED entry type: ' . json_encode($type->getErrors()) . PHP_EOL; return; }
    $created++;
    echo 'created the entry type' . PHP_EOL;
    $section->setEntryTypes([$type]);
    if (!$svc->saveSection($section)) { echo 'FAILED attaching the type: ' . json_encode($section->getErrors()) . PHP_EOL; return; }
}

/* ------------------------------------------------------------ read back */
Craft::$app->getFields()->refreshFields();
$s2 = $svc->getSectionByHandle($SECTION);
$t2 = $svc->getEntryTypeByHandle($TYPE);
$present = $t2 ? array_map(fn($c) => $c->handle, $t2->getFieldLayout()->getCustomFields()) : [];
$want = array_merge(array_keys($NEW_FIELDS), $REUSED);
$missing = array_values(array_diff($want, $present));
$urlsOff = $s2 && !array_filter($s2->getSiteSettings(), fn($ss) => $ss->hasUrls);
$ok = $s2 && $t2 && !$missing && $urlsOff;
echo PHP_EOL . 'READ-BACK ' . ($ok ? 'OK' : 'FAIL') . PHP_EOL;
echo '   section ' . ($s2 ? 'present' : 'MISSING') . ', URLs ' . ($urlsOff ? 'off' : 'ON — should be off') . PHP_EOL;
echo '   entry type ' . ($t2 ? 'present' : 'MISSING') . ', fields on the layout ' . count($present) . PHP_EOL;
if ($missing) { echo '   missing: ' . implode(', ', $missing) . PHP_EOL; }
if (!$ok) { throw new \RuntimeException('add_office_holding read-back failed'); }

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_office_holding.php', $created, 'verified: section, type, ' . count($present) . ' fields, URLs off',
    'terms as records; evidence per date, not per term');
