/**
 * Adrian W. Adams #28667: the profile, his relations and his portrait (Nathan, 6 October 2026: "this is Judge Adrian W.
 * Adams, so the photographs are his ... Three connections to records we hold: the Newhall Municipal Court ..., the
 * Newhall Incident event, and the hospital. Relate all three"). Reads inventory/review/adrian-adams-draft-2026-10-06.json,
 * made by scripts/import/draft_adams_profile_2026_10_06.py, where every quotation is checked against its page on the mirror.
 *
 * WHAT IT WRITES
 *  - #28667 Adrian Adams: body and footnotes (18), bodyAuthorship editorial-2026; fullName "Adrian W. Adams" (only if it is
 *    still "Adrian Adams"); occupation if empty; aliases "Adrian W. Adams", "Judge Adrian Adams" added; role Judge #18425
 *    added to roles; researchLeads if empty (the Hart question, among others); recordProvenance appended.
 *  - A new office holding: Judge #18425, Newhall Municipal Court #29882, 27 January 1970 to 1991, appointed, ended "left"
 *    (he retired), evidence contemporary/retrospective. Skipped if he already holds one at the court from 1970.
 *  - A new affiliation: Henry Mayo Newhall Memorial Hospital #380, nonprofit-board, "Chairman of the board", from 1970.
 *    Skipped if he already has an affiliation with #380.
 *  - #380 orgAssociatedPersons: #28667 appended. #31376 The Newhall Incident eventPersons: #28667 appended.
 *  - #28751 (his Hart holding, 1957 to 1963): note 3 added, the Hart programs and the 1962 yearbook. Dates, evidence and
 *    notes 1 and 2 stand. The identity of the trustee with the judge stays a lead (researchLeads on #28667).
 *  - His portrait: inventory/incoming/hm7301_large.jpg (copied unchanged from the mirror's gif/hm7301_large.jpg) imported to
 *    archiveMedia/legacy/ with its provenance and set as featuredImage. Refused if he has a portrait by then, if the file is
 *    not the file taken (sha256, pixel size), or if hm7301.jpg / hm7301_large.jpg is in Craft without this checksum.
 *  - photoPeople: #28667 appended on any photograph record of the eleven on AA5001 that Craft holds by then (found by
 *    photoSourceCode, legacyKey or legacyUrl), except LW2170b, which shows his nameplate and gavels, not him. On 6 October
 *    none was held, so this does nothing until they are imported.
 * Fills an empty body only; refuses a body that differs. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Prints inventory/review/adrian-adams-dry-run-2026-10-06.md.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_adams_profile_2026_10_06.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $svc = Craft::$app->getEntries(); $fields = Craft::$app->getFields();
$D = json_decode((string)file_get_contents("$root/inventory/review/adrian-adams-draft-2026-10-06.json"), true);
if (!$D) { throw new \RuntimeException('adrian-adams-draft-2026-10-06.json missing or unreadable'); }
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$P = 28667; $JUDGE = 18425; $COURT = 29882; $HOSP = 380; $EVENT = 31376; $HART = 28751;
$bad = []; $out = [];
if ($D['quoteCheck'] !== 'every quotation found in its saved source') { $bad[] = 'the draft failed its quotation check'; }
foreach ([$P => 'Adrian Adams', $JUDGE => 'Judge', $COURT => 'Newhall Municipal Court', $HOSP => 'Henry Mayo Newhall Memorial Hospital', $EVENT => 'The Newhall Incident'] as $id => $t) {
    if ($get($id)?->title !== $t) { $bad[] = "#$id is not $t (found: " . ($get($id)?->title ?? 'nothing') . ')'; }
}
$lay = fn($e) => array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
$isEmpty = function ($e, $h) { $v = $e->getFieldValue($h);
    if ($v instanceof \craft\elements\db\ElementQuery) { return !$v->status(null)->exists(); }
    if (is_object($v) && property_exists($v, 'value')) { return (string)$v->value === ''; }
    return trim((string)$v) === ''; };
$live = fn($rows) => array_values(array_filter((array)$rows, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
$rows = fn(array $notes) => array_map(fn($n, $i) => ['number' => (string)($i + 1), 'note' => is_array($n) ? $n['note'] : $n, 'source' => 'editorial-2026'], $notes, array_keys($notes));
$ids = fn($e, $h) => $e->getFieldValue($h)->status(null)->ids();
$opt = function ($h, $v) use ($fields, &$bad) { $f = $fields->getFieldByHandle($h);
    if ($f instanceof \craft\fields\Dropdown && !in_array($v, array_column($f->options, 'value'), true)) { $bad[] = "$h has no option '$v'"; } };
$PROV = 'build_adams_profile_2026_10_06.php, 6 October 2026';

/* 1. the person */
$p = $get($P); $set = [];
if ($p) {
    $pl = $lay($p);
    foreach (['body', 'footnotes', 'bodyAuthorship', 'occupation', 'fullName', 'personAliases', 'roles', 'researchLeads', 'featuredImage', 'recordProvenance'] as $h) { if (!in_array($h, $pl, true)) { $bad[] = "$h not on the person layout"; } }
    $hartOk = $get($HART); if (!$hartOk || $hartOk->holdingPerson->status(null)->one()?->id !== $P) { $bad[] = "#$HART is not Adrian Adams's Hart holding"; }
    $cur = trim((string)$p->body);
    if ($cur !== '' && $cur !== trim($D['body'])) { $bad[] = "#$P has a different body already"; }
    if ($cur !== trim($D['body'])) { $set['body'] = $D['body']; $set['footnotes'] = $rows($D['footnotes']); }
    if ($live($p->footnotes) && $cur === '') { $bad[] = "#$P has footnotes but no body; look before replacing them"; }
    foreach (['bodyAuthorship', 'occupation', 'researchLeads'] as $h) { if ($isEmpty($p, $h)) { $set[$h] = $D['fields'][$h]; } elseif ($h === 'researchLeads' && trim((string)$p->researchLeads) !== trim($D['fields']['researchLeads'])) { $bad[] = "#$P has other researchLeads; merge by hand"; } }
    if ((string)$p->fullName === 'Adrian Adams') { $set['fullName'] = $D['fields']['fullName']; }
    $aliases = array_values(array_filter(array_map('trim', explode("\n", (string)$p->personAliases))));
    $addA = array_values(array_diff($D['fields']['aliasesAdd'], $aliases)); if ($addA) { $set['personAliases'] = implode("\n", array_merge($aliases, $addA)); }
    $roles = $ids($p, 'roles'); if (!in_array($JUDGE, $roles)) { $set['roles'] = array_values(array_merge($roles, [$JUDGE])); }
    /* recordProvenance holds 255 characters: the newest entry first, the oldest cut. */
    if ($set) { $set['recordProvenance'] = mb_substr(trim("$PROV: profile (Judge Adrian W. Adams), aliases, role Judge; " . (string)$p->recordProvenance, '; '), 0, 255); }
}

/* 2. the court holding */
$hs = $svc->getSectionByHandle('officeHoldings'); $ht = $svc->getEntryTypeByHandle('officeHolding');
$C = $D['courtHolding'];
$courtHave = array_filter(Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $P, 'field' => 'holdingPerson'], ['targetElement' => $COURT, 'field' => 'holdingBody']])->all(), fn($h) => str_starts_with((string)$h->termStartEdtf, '1970'));
$courtV = ['holdingPerson' => [$P], 'holdingOffice' => [$JUDGE], 'holdingBody' => [$COURT], 'termStart' => $C['termStart'], 'termStartEdtf' => $C['termStartEdtf'],
    'termEnd' => $C['termEnd'], 'termEndEdtf' => $C['termEndEdtf'], 'selectionMethod' => $C['selectionMethod'], 'howEnded' => $C['howEnded'],
    'startEvidence' => $C['startEvidence'], 'endEvidence' => $C['endEvidence'], 'footnotes' => $rows($C['footnotes']), 'recordProvenance' => mb_substr("$PROV: Judge of the Newhall Municipal Court, from AA7001 (The Signal, 28 January 1970), AA5001 and his own article of 2003", 0, 255)];
foreach (['selectionMethod', 'howEnded', 'startEvidence', 'endEvidence'] as $h) { $opt($h, $courtV[$h]); }
if ($hs && $ht) { $t = new Entry(); $t->sectionId = $hs->id; $t->setTypeId($ht->id); $tl = $lay($t);
    foreach (array_keys($courtV) as $h) { if (!in_array($h, $tl, true)) { $bad[] = "$h not on the officeHolding layout"; } } } else { $bad[] = 'officeHoldings section or type missing'; }

/* 3. the hospital affiliation */
$as = $svc->getSectionByHandle('affiliations'); $at = $svc->getEntryTypeByHandle('affiliation');
$A = $D['hospitalAffiliation'];
$affHave = Entry::find()->section('affiliations')->status(null)->relatedTo(['and', ['targetElement' => $P, 'field' => 'affiliationPerson'], ['targetElement' => $HOSP, 'field' => 'affiliationBody']])->all();
$affV = array_filter(['affiliationPerson' => [$P], 'affiliationBody' => [$HOSP], 'affiliationKind' => $A['affiliationKind'], 'affiliationTitle' => $A['affiliationTitle'],
    'affiliationEnded' => $A['affiliationEnded'], 'termStart' => $A['termStart'], 'termStartEdtf' => $A['termStartEdtf'], 'termEnd' => $A['termEnd'], 'termEndEdtf' => $A['termEndEdtf'],
    'startEvidence' => $A['startEvidence'], 'footnotes' => $rows($A['footnotes']), 'recordProvenance' => mb_substr("$PROV: founding chairman of the hospital board, from the Van Nuys News of 9 February 1971 and the hospital pages on SCVHistory.com", 0, 255)], fn($v) => $v !== '' && $v !== null);
foreach (['affiliationKind', 'affiliationEnded', 'startEvidence'] as $h) { $opt($h, $affV[$h]); }
if ($as && $at) { $t = new Entry(); $t->sectionId = $as->id; $t->setTypeId($at->id); $tl = $lay($t);
    foreach (array_keys($affV) as $h) { if (!in_array($h, $tl, true)) { $bad[] = "$h not on the affiliation layout"; } } } else { $bad[] = 'affiliations section or type missing'; }

/* 4. the hospital's associated people, the event's people */
$hosp = $get($HOSP); $ev = $get($EVENT);
$hospIds = $hosp ? $ids($hosp, 'orgAssociatedPersons') : []; $evIds = $ev ? $ids($ev, 'eventPersons') : [];

/* 5. the Hart holding's note 3 */
$hart = $get($HART); $hartSet = null;
if ($hart) { $hr = $live($hart->footnotes);
    if (!in_array($D['hartHolding']['note3'], array_column($hr, 'note'), true)) {
        if (count($hr) !== 2) { $bad[] = "#$HART has " . count($hr) . ' notes, expected 2'; }
        $hartSet = array_merge(array_map(fn($r) => ['number' => (string)$r['number'], 'note' => $r['note'], 'source' => $r['source'] ?? ''], $hr), [['number' => (string)(count($hr) + 1), 'note' => $D['hartHolding']['note3'], 'source' => 'editorial-2026']]);
    }
    if ((string)$hart->termStartEdtf !== '1957' || (string)$hart->termEndEdtf !== '1963') { $bad[] = "#$HART is not 1957 to 1963"; }
}

/* 6. the portrait */
$PT = $D['portrait']; $path = "$root/inventory/incoming/{$PT['file']}"; $pa = null; $portrait = '';
/* A failed run of 6 October left a re-encoded hm7301_large.jpg in legacy/ with no asset; the portrait takes its own name. */
$PT['assetFilename'] = 'adrian-w-adams-hm7301_large.jpg';
$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia');
if (!$vol) { $bad[] = 'archiveMedia volume missing'; }
elseif (!is_file($path)) { $bad[] = "inventory/incoming/{$PT['file']} is missing"; }
else {
    $sha = hash_file('sha256', $path);
    if ($sha !== $PT['sha256']) { $bad[] = "{$PT['file']} is not the file taken (sha256 $sha)"; }
    [$w, $h] = getimagesize($path); if ($w !== $PT['width'] || $h !== $PT['height']) { $bad[] = "{$PT['file']} is $w x $h, expected {$PT['width']} x {$PT['height']}"; }
    $pa = Asset::find()->sourceChecksum('sha256:' . $sha)->one();
    if (!$pa) { foreach (Asset::find()->filename(['hm7301.jpg', 'hm7301_large.jpg'])->all() as $tw) { $bad[] = "{$tw->filename} is already in Craft as #{$tw->id} without this file's checksum: link it or say why not"; } }
    $vh = array_map(fn($f) => $f->handle, $vol->getFieldLayout()->getCustomFields());
    foreach ($PT['fields'] as $hh => $x) { if (!in_array($hh, $vh, true)) { $bad[] = "$hh is not an archiveMedia field"; continue; }
        $f = $fields->getFieldByHandle($hh);
        if ($f instanceof \craft\fields\PlainText && $f->charLimit && mb_strlen((string)$x) > $f->charLimit) { $bad[] = "$hh over its limit"; }
        if ($f instanceof \craft\fields\Dropdown && !in_array($x, array_column($f->options, 'value'), true)) { $bad[] = "$hh \"$x\" is not an option"; } }
    $fi = $p ? $ids($p, 'featuredImage') : [];
    if ($fi && (!$pa || !in_array($pa->id, $fi))) { $bad[] = "#$P already has a portrait (#" . implode(',', $fi) . '); refused, never overwritten'; }
    $portrait = ($pa ? "#{$pa->id} already imported" : "import as legacy/{$PT['assetFilename']}") . " ($w x $h, " . number_format($PT['bytes']) . ' bytes); ' . ($fi && $pa && in_array($pa->id, $fi) ? 'already the portrait' : 'set as featuredImage');
}

/* 7. the photographs */
$photoPlan = [];
foreach ($D['photographs'] as $ph) {
    $code = $ph['code']; $lc = strtolower($code);
    $rec = Entry::find()->section('photographs')->status(null)->photoSourceCode($code)->one()
        ?? Entry::find()->section('photographs')->status(null)->legacyKey($lc)->one()
        ?? Entry::find()->section('photographs')->status(null)->legacyUrl("*/$lc.htm")->one();
    $have = $rec ? $ids($rec, 'photoPeople') : [];
    $photoPlan[] = ['ph' => $ph, 'rec' => $rec, 'add' => $rec && $ph['photoPeople'] && !in_array($P, $have)];
}
if (preg_match('~\x{2014}~u', $D['body'] . json_encode($D['footnotes']) . json_encode($C) . json_encode($A) . $D['hartHolding']['note3'] . $D['fields']['researchLeads'])) { $bad[] = 'an em dash in our own text'; }

/* the report */
$show = fn($v) => is_array($v) ? (isset($v[0]['note']) ? count($v) . ' notes' : implode(', ', array_map(fn($x) => '#' . $x . ' ' . ($get($x)?->title ?? '?'), $v))) : (mb_strlen((string)$v) > 160 ? mb_substr($v, 0, 160) . '... (' . mb_strlen($v) . ' chars)' : $v);
$out[] = "# Adrian W. Adams #28667: the profile, dry run (6 October 2026)\n\n" . ($APPLY ? 'APPLY.' : 'Nothing is written by a dry run.') . " The prose, every note, the Hart verdict and the photographs are in inventory/review/adrian-adams-draft-2026-10-06.md; every quotation there was checked against its page on the mirror.\n";
$out[] = "## #28667 Adrian Adams\n\n" . ($set ? implode("\n", array_map(fn($h, $v) => "- $h: " . $show($v), array_keys($set), $set)) : '- nothing to change') . "\n- title: not touched (\"Adrian Adams\", the name as the roster prints it)\n";
$out[] = "## The court\n\n" . ($courtHave ? '- HAVE #' . implode(', #', array_map(fn($h) => $h->id, $courtHave)) : "- create office holding: Judge #$JUDGE, Newhall Municipal Court #$COURT, {$C['termStart']} to {$C['termEnd']}, {$C['selectionMethod']}, ended {$C['howEnded']} (retired), evidence {$C['startEvidence']}/{$C['endEvidence']}, " . count($C['footnotes']) . ' notes') . "\n";
$out[] = "## The hospital\n\n" . ($affHave ? '- HAVE affiliation #' . implode(', #', array_map(fn($a) => $a->id, $affHave)) : "- create affiliation: #$HOSP, {$A['affiliationKind']}, \"{$A['affiliationTitle']}\", from {$A['termStart']} ({$A['startEvidence']}), end not recorded, " . count($A['footnotes']) . ' notes')
    . "\n- #$HOSP orgAssociatedPersons: " . (in_array($P, $hospIds) ? 'already holds #28667' : 'append #28667 (now: ' . ($hospIds ? '#' . implode(', #', $hospIds) : 'empty') . ')') . "\n";
$out[] = "## The Newhall Incident\n\n- #$EVENT eventPersons: " . (in_array($P, $evIds) ? 'already holds #28667' : 'append #28667 (now: ' . ($evIds ? '#' . implode(', #', $evIds) : 'empty') . ')') . ". Source: AA5001, \"the arraignment of the two suspects\"; the profile notes that only one survived.\n";
$out[] = "## The Hart holding #$HART\n\n" . ($hartSet ? '- add note 3: ' . $D['hartHolding']['note3'] : '- note 3 already there') . "\n- dates (1957 to 1963), evidence (roster/roster) and notes 1 and 2 stand. The identity stays a lead: see researchLeads.\n";
$out[] = "## The portrait\n\n- inventory/incoming/{$PT['file']} (mirror {$PT['mirrorPath']}, sha256 {$PT['sha256']}): $portrait\n- caption \"{$PT['fields']['photoCaptionExt']}\", date as printed {$PT['fields']['dateAsPrinted']}, credit \"{$PT['fields']['photoCredit']}\", licence unknown\n";
$out[] = "## The photographs on AA5001 (photoPeople)\n\n" . implode("\n", array_map(fn($x) => "- {$x['ph']['code']} ({$x['ph']['dateAsPrinted']}): " . ($x['rec'] ? "#{$x['rec']->id} {$x['rec']->title}; " . ($x['add'] ? 'append #28667 to photoPeople' : ($x['ph']['photoPeople'] ? 'already related' : 'not related: objects, he is not in it')) : 'not held in Craft; nothing to relate'), $photoPlan)) . "\n";
$out[] = "## Body as it will read\n\n" . $D['body'] . "\n";
$out[] = "## Notes\n\n" . implode("\n", array_map(fn($f) => "{$f['number']}. {$f['note']}", $D['footnotes'])) . "\n";
$out[] = 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . "\n";
file_put_contents("$root/inventory/review/adrian-adams-dry-run-2026-10-06.md", implode("\n", $out));
$nPh = count(array_filter($photoPlan, fn($x) => $x['add']));
echo "#$P: " . count($set) . ' fields; court holding: ' . ($courtHave ? 'have' : 'create') . '; hospital affiliation: ' . ($affHave ? 'have' : 'create')
    . '; #380 people: ' . (in_array($P, $hospIds) ? 'have' : 'append') . '; #31376 people: ' . (in_array($P, $evIds) ? 'have' : 'append') . '; #28751 note 3: ' . ($hartSet ? 'add' : 'have')
    . "; portrait: $portrait; photoPeople: $nPh of " . count($photoPlan) . ' (held: ' . count(array_filter($photoPlan, fn($x) => $x['rec'])) . ')' . PHP_EOL
    . 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL . 'printed to inventory/review/adrian-adams-dry-run-2026-10-06.md' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first; nothing was written' . PHP_EOL; return; }

$folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'legacy/']);
if (!$folder) { throw new \RuntimeException('archiveMedia legacy/ folder not found'); }
$tx = Craft::$app->getDb()->beginTransaction(); $writes = 0;
try {
    if (!$pa) {
        $tmp = sys_get_temp_dir() . '/' . $PT['assetFilename']; copy($path, $tmp);
        $pa = new Asset(); $pa->tempFilePath = $tmp; $pa->setFilename($PT['assetFilename']); $pa->newFolderId = $folder->id; $pa->setVolumeId($vol->id);
        $pa->setScenario(Asset::SCENARIO_CREATE); $pa->avoidFilenameConflicts = false;
        $pa->title = $PT['assetTitle']; $pa->alt = $PT['alt']; $pa->setFieldValues($PT['fields']);
        if (!$el->saveElement($pa)) { throw new \RuntimeException('portrait: ' . json_encode($pa->getFirstErrors())); } $writes++;
    }
    $e = $get($P);
    if (!in_array($pa->id, $ids($e, 'featuredImage'))) { $set['featuredImage'] = [$pa->id]; }
    if ($set) { $e->setFieldValues($set); if (!$el->saveElement($e)) { throw new \RuntimeException("#$P: " . json_encode($e->getFirstErrors())); } $writes++; }
    if (!$courtHave) { $h = new Entry(); $h->sectionId = $hs->id; $h->setTypeId($ht->id); $h->setFieldValues($courtV);
        if (!$el->saveElement($h)) { throw new \RuntimeException('court holding: ' . json_encode($h->getFirstErrors())); } $writes++; }
    if (!$affHave) { $a = new Entry(); $a->sectionId = $as->id; $a->setTypeId($at->id); $a->setFieldValues($affV);
        if (!$el->saveElement($a)) { throw new \RuntimeException('affiliation: ' . json_encode($a->getFirstErrors())); } $writes++; }
    foreach ([[$HOSP, 'orgAssociatedPersons'], [$EVENT, 'eventPersons']] as [$id, $h]) { $x = $get($id); $cur = $ids($x, $h);
        if (!in_array($P, $cur)) { $x->setFieldValue($h, array_values(array_merge($cur, [$P]))); if (!$el->saveElement($x)) { throw new \RuntimeException("#$id: " . json_encode($x->getFirstErrors())); } $writes++; } }
    if ($hartSet) { $x = $get($HART); $x->setFieldValue('footnotes', $hartSet); if (!$el->saveElement($x)) { throw new \RuntimeException("#$HART: " . json_encode($x->getFirstErrors())); } $writes++; }
    foreach ($photoPlan as $pp) { if (!$pp['add']) { continue; } $x = $get($pp['rec']->id); $cur = $ids($x, 'photoPeople');
        $x->setFieldValue('photoPeople', array_values(array_merge($cur, [$P]))); if (!$el->saveElement($x)) { throw new \RuntimeException("#{$x->id}: " . json_encode($x->getFirstErrors())); } $writes++; }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written to the database (a file copied into the volume may remain): ' . $t->getMessage() . PHP_EOL; throw $t; }
$r = $get($P);
$ok = trim((string)$r->body) === trim($D['body']) && count($live($r->footnotes)) === count($D['footnotes'])
    && Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $P, 'field' => 'holdingPerson'], ['targetElement' => $COURT, 'field' => 'holdingBody']])->exists()
    && Entry::find()->section('affiliations')->status(null)->relatedTo(['and', ['targetElement' => $P, 'field' => 'affiliationPerson'], ['targetElement' => $HOSP, 'field' => 'affiliationBody']])->exists()
    && in_array($P, $ids($get($HOSP), 'orgAssociatedPersons')) && in_array($P, $ids($get($EVENT), 'eventPersons'))
    && in_array($pa->id, $ids($r, 'featuredImage')) && (string)$pa->sourceChecksum === 'sha256:' . $PT['sha256'];
echo 'READ-BACK ' . ($ok ? "OK: $writes writes; " . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('build_adams_profile_2026_10_06.php', $writes, $ok ? 'verified' : 'SHORT', 'Adrian W. Adams: editorial profile; Judge, Newhall Municipal Court 1970-1991; Henry Mayo board chairman; Newhall Incident; Hart note 3; HM7301 portrait');
if (!$ok) { throw new \RuntimeException('build_adams_profile: read-back failed'); }
