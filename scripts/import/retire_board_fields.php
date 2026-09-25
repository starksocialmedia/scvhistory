/**
 * Retires the board fields officeHolding replaces.
 *
 * The organization type carries four fields from the WordPress era that were
 * meant to record who sat on a body: hasSubBoards, subBoards, boardMembers and
 * termNotes. None of them holds a single value, across 38 organizations, and
 * every one of them is a worse way to say what officeHolding says properly:
 *
 *   boardMembers  a relation to persons with no dates, so it cannot say when
 *                 anybody served, which is the whole question.
 *   termNotes     one free-text field for every term of every member, which is
 *                 a paragraph where a record belongs.
 *   subBoards     the inverse of parentOrganization, already expressible, and
 *                 a second place for the same fact to be wrong in.
 *   hasSubBoards  a lightswitch guarding subBoards.
 *
 * Two ways to say one thing is how data goes wrong, so these go rather than sit
 * as an alternative nobody chose. Verified empty before anything is removed: if
 * any record has acquired a value since, the script stops and says which.
 *
 * parentOrganization stays. It is used, it is right, and it is the nesting.
 *
 * Checked before anything is removed: no record holds a value, AND no template,
 * module or script still names the handle. The first check alone let this delete
 * termNotes, subBoards and boardMembers while organizations/_entry.twig was still
 * reading them, and every organization page answered 500 until the templates
 * caught up.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/retire_board_fields.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this removes fields from the layout and deletes them' . PHP_EOL; }

$RETIRE = ['hasSubBoards', 'subBoards', 'boardMembers', 'termNotes'];
$TYPE = 'organization';

$fs = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

/* Nothing is deleted on trust: every record is read first. */
$inUse = [];
foreach ($RETIRE as $h) {
    $f = $fs->getFieldByHandle($h);
    if (!$f) { printf("   %-16s already gone\n", $h); continue; }
    $used = 0;
    foreach (\craft\elements\Entry::find()->section('organizations')->status(null)->limit(null)->each() as $o) {
        if (!$o->getFieldLayout()?->getFieldByHandle($h)) { continue; }
        $v = $o->getFieldValue($h);
        if (is_object($v) && method_exists($v, 'count')) { $v = $v->status(null)->count(); }
        if (is_array($v)) { $v = count($v); }
        if (is_bool($v)) { $v = $v ? 1 : 0; }
        if (is_string($v)) { $v = trim($v) === '' ? 0 : 1; }
        if ((int)$v > 0) { $used++; }
    }
    printf("   %-16s %s, %d record%s carrying a value\n", $h, get_class($f), $used, $used === 1 ? '' : 's');
    if ($used > 0) { $inUse[] = $h . ' (' . $used . ')'; }
}

/* Records were checked and templates were not, which is how /organizations/
   acton-hotel started answering 500: every organization page read termNotes, and
   a field that holds no values is still a field the site calls by name. A
   handle is used by whatever names it, not only by what stores something in it. */
$referenced = [];
$roots = ['templates', 'modules', 'scripts/import', 'config'];
/* Named literally: __FILE__ inside a script run through `craft exec` is Craft's
   ExecController, not this file, so basename(__FILE__) excluded nothing and the
   check counted its own $RETIRE list as four references to itself. */
$self = 'retire_board_fields.php';
foreach ($RETIRE as $h) {
    foreach ($roots as $root) {
        $dir = \Craft::getAlias('@root') . '/' . $root;
        if (!is_dir($dir)) { continue; }
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS));
        foreach ($it as $file) {
            if (!$file->isFile()) { continue; }
            if (!preg_match('~\.(twig|php|js|json|yaml|yml)$~', $file->getFilename())) { continue; }
            $rel = str_replace(\Craft::getAlias('@root') . '/', '', $file->getPathname());
            /* This script names all four by design, and so does the log. */
            if (str_ends_with($rel, $self) || str_contains($rel, 'APPLIED.log')) { continue; }
            /* Project config is the schema itself: it is rewritten by the apply. */
            if (str_starts_with($rel, 'config/project/')) { continue; }
            $body = (string)file_get_contents($file->getPathname());
            /* Comments are not usage. A template that explains why a field went
               away names it on purpose, and counting that as a reference makes
               the check cry wolf at its own documentation. */
            $ext = $file->getExtension();
            if ($ext === 'twig') {
                $body = preg_replace('~\{#.*?#\}~s', ' ', $body);
            } elseif (in_array($ext, ['php', 'js'], true)) {
                $body = preg_replace(['~/\*.*?\*/~s', '~//[^\n]*~', '~(?<![:\w])#[^\n]*~'], ' ', $body);
            }
            if (preg_match('~(?<![A-Za-z0-9_])' . preg_quote($h, '~') . '(?![A-Za-z0-9_])~', $body)) {
                foreach (preg_split('~\R~', $body) as $i => $line) {
                    if (preg_match('~(?<![A-Za-z0-9_])' . preg_quote($h, '~') . '(?![A-Za-z0-9_])~', $line)) {
                        $referenced[$h][] = $rel . ':' . ($i + 1) . '  ' . trim(mb_substr($line, 0, 90));
                    }
                }
            }
        }
    }
}
echo PHP_EOL . 'still named in the code:' . PHP_EOL;
foreach ($RETIRE as $h) {
    $hits = $referenced[$h] ?? [];
    printf("   %-16s %d reference%s\n", $h, count($hits), count($hits) === 1 ? '' : 's');
    foreach (array_slice($hits, 0, 6) as $hit) { echo '      ' . $hit . PHP_EOL; }
}

$type = $svc->getEntryTypeByHandle($TYPE);
$onLayout = array_values(array_intersect($RETIRE, array_map(fn($c) => $c->handle, $type->getFieldLayout()->getCustomFields())));
echo PHP_EOL . 'on the organization layout: ' . ($onLayout ? implode(', ', $onLayout) : 'none') . PHP_EOL;
echo 'parentOrganization: kept, ' . \craft\elements\Entry::find()->section('organizations')->status(null)->parentOrganization(':notempty:')->count() . ' record(s) use it' . PHP_EOL;

if ($inUse) {
    throw new \RuntimeException('retire_board_fields: ' . implode('; ', $inUse)
        . ' now hold values. Move them into officeHolding first.');
}
if ($referenced) {
    echo PHP_EOL . 'STOPPING: these handles are still named in the code. Deleting a field the'
       . ' templates call is how every organization page came to answer 500 on 25 September.' . PHP_EOL;
    throw new \RuntimeException('retire_board_fields: still referenced: '
        . implode(', ', array_map(fn($h) => $h . ' (' . count($referenced[$h]) . ')', array_keys($referenced))));
}
if (!$svc->getEntryTypeByHandle('officeHolding')) {
    echo PHP_EOL . 'officeHolding does not exist yet. Run add_office_holding.php first, so nothing is removed'
       . ' before its replacement is there.' . PHP_EOL;
    if ($APPLY) { return; }
}

if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 74) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}

/* Off the layout first, then deleted. */
$layout = $type->getFieldLayout();
$tabs = $layout->getTabs();
foreach ($tabs as $tab) {
    $els = array_values(array_filter($tab->getElements(), fn($el) =>
        !($el instanceof \craft\fieldlayoutelements\CustomField && in_array($el->getField()->handle, $RETIRE, true))));
    $tab->setElements($els);
}
$layout->setTabs($tabs);
$type->setFieldLayout($layout);
if (!$svc->saveEntryType($type)) { echo 'FAILED layout: ' . json_encode($type->getErrors()) . PHP_EOL; return; }

$removed = 0;
foreach ($RETIRE as $h) {
    $f = $fs->getFieldByHandle($h);
    if ($f && $fs->deleteField($f)) { $removed++; }
}

Craft::$app->getFields()->refreshFields();
$t2 = $svc->getEntryTypeByHandle($TYPE);
$still = array_values(array_intersect($RETIRE, array_map(fn($c) => $c->handle, $t2->getFieldLayout()->getCustomFields())));
$fieldsLeft = array_values(array_filter($RETIRE, fn($h) => (bool)Craft::$app->getFields()->getFieldByHandle($h)));
$parentKept = (bool)Craft::$app->getFields()->getFieldByHandle('parentOrganization');
$ok = !$still && !$fieldsLeft && $parentKept;
echo PHP_EOL . 'READ-BACK ' . ($ok ? 'OK' : 'FAIL') . PHP_EOL;
echo '   removed ' . $removed . ' field(s); still on the layout: ' . ($still ? implode(', ', $still) : 'none') . PHP_EOL;
echo '   parentOrganization kept: ' . ($parentKept ? 'yes' : 'NO') . PHP_EOL;
if (!$ok) { throw new \RuntimeException('retire_board_fields read-back failed'); }

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('retire_board_fields.php', $removed, 'verified: gone from the layout and deleted, parentOrganization kept',
    'superseded by officeHolding; all four were empty across 38 organizations');
