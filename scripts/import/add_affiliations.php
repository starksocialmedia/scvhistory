/**
 * affiliation: one record per person per position at a body that is not an
 * elected office (inventory/review/relationship-model-2026-10-03.md, step 2,
 * approved by Nathan on 3 October 2026).
 *
 * Built now (Nathan, 4 October 2026) because the Hart district's page showed
 * H. Clyde Smyth in a box labelled SUPERINTENDENT as if he held the post: he
 * retired in 1992 and died in 2012. The box was filled from whoever the archive
 * holds with the role, since nothing said when anybody served. A superintendent
 * is employed by the board, not elected to it, so the record is not an office
 * holding: it is an affiliation, with a kind, a title as printed, years, how it
 * ended, and evidence on each date, the discipline the holdings brought to
 * office.
 *
 *   person   who
 *   body     the organization
 *   kind     employed by, member of, founder, owner, board member of a
 *            nonprofit, volunteer
 *   title    as the source prints it ("Superintendent")
 *   years    termStart / termStartEdtf, termEnd / termEndEdtf, reused from
 *            the holdings, so dates are written the same way everywhere
 *   ended    serving, retired, resigned, left, died, dismissed, unknown. A
 *            page calls someone the present holder only when this says
 *            serving and the source is dated.
 *
 * It will replace personOrganizations, orgAssociatedPersons and orgFoundedBy,
 * once the 23 disagreements between them are reported to Nathan and each link
 * is re-read into a kind (step 2 of the model). This script only creates the
 * section; it moves nothing.
 *
 * NO URLS, like the holdings: an affiliation is shown on the body's page and
 * the person's.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_affiliations.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database and to project config' . PHP_EOL; }

$SECTION = 'affiliations';
$SECTION_NAME = 'Affiliations';
$TYPE = 'affiliation';
$TYPE_NAME = 'Affiliation';

/* handle => [class, label, settings]. Relations name their source section. */
$NEW_FIELDS = [
    'affiliationPerson' => ['entries', 'Person', ['sources' => 'persons',       'maxRelations' => 1, 'required' => true]],
    'affiliationBody'   => ['entries', 'Body',   ['sources' => 'organizations', 'maxRelations' => 1, 'required' => true]],
    'affiliationKind'   => ['dropdown', 'Kind', ['options' => [
        'employed' => 'Employed by', 'member' => 'Member of', 'founder' => 'Founder', 'owner' => 'Owner',
        'nonprofit-board' => 'Board member of a nonprofit', 'volunteer' => 'Volunteer']]],
    'affiliationTitle'  => ['plain', 'Title, as printed', []],
    'affiliationEnded'  => ['dropdown', 'How it ended', ['options' => [
        'serving' => 'Still serving', 'retired' => 'Retired', 'resigned' => 'Resigned', 'left' => 'Left',
        'died' => 'Died in the post', 'dismissed' => 'Dismissed', 'unknown' => 'Unknown']]],
];

/* Reused as they are: dates and evidence written the way the holdings write them. */
$REUSED = ['termStart', 'termStartEdtf', 'termEnd', 'termEndEdtf', 'startEvidence', 'endEvidence', 'footnotes', 'footnotesOn', 'editorNotes', 'recordProvenance', 'recordDates'];

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
$role = \craft\elements\Entry::find()->section('roles')->status(null)->title('School Superintendent')->one();
$sup = $role ? \craft\elements\Entry::find()->section('persons')->status(null)->relatedTo(['targetElement' => $role, 'field' => 'roles'])->all() : [];
echo PHP_EOL . 'people with the role School Superintendent, none dated: ' . implode(', ', array_map(fn($p) => $p->title . ' #' . $p->id, $sup)) . PHP_EOL;

if ($missingSource) {
    throw new \RuntimeException('add_affiliations: these sections do not exist: ' . implode(', ', array_unique($missingSource)));
}

if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 76) . PHP_EOL;
    echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}

/* ------------------------------------------------------------- creating

   Order matters, and getting it wrong is what failed on 24 September: Craft 5
   validates a section against its entry types, so saving the section first
   returns "Entry Types cannot be blank". The fields come first because the
   layout needs them, then the entry type carrying that layout, then the section
   with the type already attached. Every failure throws: this script printed
   FAILED and exited 0, and the && chain took that for success. */
$created = 0;

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
        if (!$fs->saveField($f)) {
            throw new \RuntimeException('add_affiliations: field ' . $handle . ' refused: ' . json_encode($f->getErrors()));
        }
        $created++;
        $f = $fs->getFieldByHandle($handle);
    }
    $fieldsForLayout[] = $f;
}
foreach ($REUSED as $h) { if ($f = $fs->getFieldByHandle($h)) { $fieldsForLayout[] = $f; } }
echo 'fields ready: ' . count($fieldsForLayout) . PHP_EOL;

$type = $svc->getEntryTypeByHandle($TYPE);
if (!$type) {
    $type = new \craft\models\EntryType([
        'name' => $TYPE_NAME,
        'handle' => $TYPE,
        'hasTitleField' => false,
        /* Null-safe on purpose: a holding saved before its relations are set
           would otherwise fail on a missing title rather than saying what is
           missing. */
        'titleFormat' => '{affiliationPerson.one().title ?? \'Unknown person\'}'
            . ', {affiliationTitle ?: \'affiliated\'}'
            . ', {affiliationBody.one().title ?? \'unknown body\'}',
    ]);
    $layout = new \craft\models\FieldLayout(['type' => \craft\elements\Entry::class]);
    $tab = new \craft\models\FieldLayoutTab(['name' => 'The affiliation', 'layout' => $layout]);
    $tab->setElements(array_map(fn($f) => new \craft\fieldlayoutelements\CustomField($f), $fieldsForLayout));
    $layout->setTabs([$tab]);
    $type->setFieldLayout($layout);
    if (!$svc->saveEntryType($type)) {
        throw new \RuntimeException('add_affiliations: entry type refused: ' . json_encode($type->getErrors()));
    }
    $created++;
    $type = $svc->getEntryTypeByHandle($TYPE);
    echo 'created the entry type' . PHP_EOL;
}

if (!$section) {
    $section = new \craft\models\Section([
        'name' => $SECTION_NAME,
        'handle' => $SECTION,
        'type' => \craft\models\Section::TYPE_CHANNEL,
        'enableVersioning' => true,
        'siteSettings' => array_map(fn($site) => new \craft\models\Section_SiteSettings([
            'siteId' => $site->id, 'enabledByDefault' => true, 'hasUrls' => false,
        ]), Craft::$app->getSites()->getAllSites()),
    ]);
    $section->setEntryTypes([$type]);
    if (!$svc->saveSection($section)) {
        throw new \RuntimeException('add_affiliations: section refused: ' . json_encode($section->getErrors()));
    }
    $created++;
    echo 'created the section with its entry type attached' . PHP_EOL;
} else {
    $have = array_map(fn($t) => $t->handle, $section->getEntryTypes());
    if (!in_array($TYPE, $have, true)) {
        $section->setEntryTypes(array_merge($section->getEntryTypes(), [$type]));
        if (!$svc->saveSection($section)) {
            throw new \RuntimeException('add_affiliations: attaching the type refused: ' . json_encode($section->getErrors()));
        }
        echo 'attached the entry type to the existing section' . PHP_EOL;
    }
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
if (!$ok) { throw new \RuntimeException('add_affiliations read-back failed'); }

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_affiliations.php', $created, 'verified: section, type, ' . count($present) . ' fields, URLs off',
    'affiliations as records: kind, title, years, how it ended, evidence per date');
