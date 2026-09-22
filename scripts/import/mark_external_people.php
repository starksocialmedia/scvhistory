/**
 * Applies the locality policy: a person record requires a Santa Clarita Valley
 * connection the articles document.
 *
 * Lived, worked, owned, built, founded, filmed, buried or acted here. A
 * national figure written about by a local columnist is a subject, not a
 * resident. Q. David Bowers has no more claim on a record here than he would in
 * a Vermont town's archive that happened to run his column.
 *
 * TWO POPULATIONS, AND THE CHEAPER ONE IS MOST OF IT
 *
 * Nine of the ten Nathan named are not records. They are approved decisions
 * waiting in batch three, which the gate is holding on two containment pairs.
 * Marking them External in the decisions file costs nothing and creates
 * nothing. Creating them first and deleting them afterwards would take two
 * applies, leave nine ids in the relations and the review file, and end in the
 * same place.
 *
 * Only Ward Connerly exists, plus Bill Clinton and Theodore Roosevelt from the
 * earlier brief. Those three are removals: drop the relations, record the
 * Wikidata id in the canon as external, and list them for deletion in the
 * control panel. They are never deleted here, for the same reason the ranchos
 * were not: the id is in the decisions file and the relations, and removing it
 * is the one step that cannot be undone.
 *
 * WHY THE COLLECTION IS NOT THE TEST
 *
 * Coins-only catches the eight numismatists and nothing else. Ward Connerly's
 * five articles are all in Worden's column, which is local; they are about
 * Proposition 209, which is not. The subject decides, and a subject is a
 * judgement, so this script proposes from the evidence and a person rules.
 *
 * ALSO, BY NATHAN'S INSTRUCTION, ONE MERGE
 *
 * Kit Carson #15976 folds into Christopher Houston Carson #315, with Kit Carson
 * kept as an alias. Two records for one man, and the fuller name is the title
 * by the name policy while the name every article actually uses is the alias,
 * which is what an alias is for.
 *
 * This rides along here rather than in its own script because it is the same
 * operation the strip performs in reverse: the strip moves relations off a
 * record and drops them, the merge moves them onto another record and keeps
 * them. Doing both in one pass means one read-back proves both.
 *
 * Dry run by default.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/mark_external_people.php'))"
 */

$APPLY = false;

/* Nathan's ruling, 21 September. */
$EXTERNAL = [
    'Q. David Bowers' => 'Q7263116', 'Kenneth Bressett' => 'Q6389291',
    'Farran Zerbe' => 'Q5437084', 'Russell Rulau' => '', 'Bill Fivaz' => '',
    'Abe Kosoff' => '', 'Max Mehl' => 'Q1913185', 'Aubrey Bebee' => '',
    'Ward Connerly' => 'Q7968160', 'Ronald Reagan' => 'Q9960',
    'Bill Clinton' => 'Q1124', 'Theodore Roosevelt' => 'Q33866',
];
/* Kept, and why, so the list is a record of the decision rather than a diff. */
$KEPT = [
    'John Wayne' => 'filmed at Melody Ranch',
    'Tom Mix' => 'filmed here',
    'Charles Crocker' => 'the Southern Pacific through the valley',
    'William Mulholland' => 'the St Francis Dam collapse',
    'Kit Carson' => 'came through with Fremont',
    'Christopher Houston Carson' => 'came through with Fremont',
];

/* from, into, alias to keep. Both sides must already exist; a merge into a
   record that is not there is a rename with a deletion attached. */
$MERGES = [
    ['from' => 15976, 'into' => 315, 'alias' => 'Kit Carson'],
];

/* THE TRAP THIS CLOSES
 *
 * The decisions file holds an approved "Kit Carson", and it is harmless only
 * because #15976 exists and the gate treats it as already held. Delete #15976
 * after the merge and that row stops being a collision and becomes a create:
 * batch three would build a fresh Kit Carson and quietly undo the merge, with
 * nothing in either report saying so. The decision is repointed at the target
 * here, in the same pass that performs the merge, because that is the only
 * moment both facts are in view. */

$FILE = \Craft::getAlias('@webroot') . '/review/records-decided.json';
$doc = json_decode(file_get_contents($FILE), true) ?: [];
$rows = $doc['decisions'] ?? [];
$norm = fn(string $v) => trim(mb_strtolower(preg_replace('~[^a-z0-9 ]~i', ' ', $v)));

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

$ARTICLE_FIELDS = ['subjectPerson', 'writtenBy', 'editedBy', 'relatedPersons'];
$inFile = []; $asRecord = []; $absent = [];

foreach ($EXTERNAL as $name => $qid) {
    $rec = \craft\elements\Entry::find()->section('persons')->title($name)->status(null)->one();
    $idx = null;
    foreach ($rows as $i => $r) {
        if (($r['type'] ?? '') === 'pair') { continue; }
        if ($norm((string)($r['name'] ?? '')) === $norm($name)) { $idx = $i; break; }
    }
    if ($rec) {
        $arts = [];
        foreach ($ARTICLE_FIELDS as $fh) {
            foreach (\craft\elements\Entry::find()->section('articles')
                         ->relatedTo(['targetElement' => $rec, 'field' => $fh])->status(null)->limit(null)->all() as $a) {
                $arts[$a->id] = $a;
            }
        }
        $colls = [];
        foreach ($arts as $a) { $c = $a->partOfCollection->one(); $s = $c ? $c->slug : '(none)'; $colls[$s] = ($colls[$s] ?? 0) + 1; }
        $asRecord[] = ['name' => $name, 'qid' => $qid, 'entry' => $rec, 'articles' => count($arts),
                       'colls' => $colls, 'idx' => $idx];
    } elseif ($idx !== null) {
        $inFile[] = ['name' => $name, 'qid' => $qid, 'idx' => $idx,
                     'action' => $rows[$idx]['action'] ?? '?',
                     'articles' => count($rows[$idx]['articles'] ?? [])];
    } else {
        $absent[] = $name;
    }
}

echo 'DECISIONS TO MARK EXTERNAL, so nothing is ever created (' . count($inFile) . '):' . PHP_EOL;
printf("   %-24s %-10s %-6s %s\n", 'NAME', 'WAS', 'ARTS', 'WIKIDATA');
foreach ($inFile as $x) {
    printf("   %-24s %-10s %-6d %s\n", $x['name'], $x['action'], $x['articles'], $x['qid'] ?: '(none yet)');
}

echo PHP_EOL . 'RECORDS THAT EXIST, to strip and list for deletion (' . count($asRecord) . '):' . PHP_EOL;
foreach ($asRecord as $x) {
    printf("   %-24s #%-6d %-4d articles   %s\n", $x['name'], $x['entry']->id, $x['articles'],
        implode(', ', array_map(fn($k, $v) => $k . ' ' . $v, array_keys($x['colls']), $x['colls'])));
}
if ($absent) { echo PHP_EOL . 'neither a record nor a decision: ' . implode(', ', $absent) . PHP_EOL; }

echo PHP_EOL . 'KEPT, with the connection that earns the record:' . PHP_EOL;
$mergedAway = array_map(fn($m) => $m['from'], $MERGES);
foreach ($KEPT as $n => $why) {
    $r = \craft\elements\Entry::find()->section('persons')->title($n)->status(null)->one();
    $note = $r && in_array($r->id, $mergedAway, true) ? ' (merged away below; the man is kept)' : '';
    printf("   %-28s %-14s %s%s\n", $n, $r ? '#' . $r->id : 'pending', $why, $note);
}

$merges = [];
foreach ($MERGES as $m) {
    $from = \craft\elements\Entry::find()->id($m['from'])->status(null)->one();
    $into = \craft\elements\Entry::find()->id($m['into'])->status(null)->one();
    if (!$from || !$into) {
        echo PHP_EOL . 'MERGE SKIPPED: #' . $m['from'] . ' -> #' . $m['into']
           . ' (' . (!$from ? 'source' : 'target') . ' does not exist)' . PHP_EOL;
        continue;
    }
    $moving = [];
    foreach ($ARTICLE_FIELDS as $fh) {
        foreach (\craft\elements\Entry::find()->section('articles')
                     ->relatedTo(['targetElement' => $from, 'field' => $fh])->status(null)->limit(null)->all() as $a) {
            $moving[$fh][] = $a;
        }
    }
    $n = array_sum(array_map('count', $moving));
    $have = array_values(array_filter(array_map('trim',
        preg_split('~[\r\n]+~', (string)$into->getFieldValue('personAliases')))));
    $merges[] = ['from' => $from, 'into' => $into, 'moving' => $moving, 'n' => $n,
                 'alias' => $m['alias'], 'aliasHeld' => in_array($m['alias'], $have, true),
                 'aliases' => array_values(array_unique(array_merge($have, [$m['alias']])))];
    echo PHP_EOL . 'MERGE' . PHP_EOL;
    printf("   #%-6d %-32s -> #%-6d %s\n", $from->id, $from->title, $into->id, $into->title);
    foreach ($moving as $fh => $as) { printf("      %-16s %d relations move\n", $fh, count($as)); }
    printf("      %-16s %s\n", 'aliases after', implode(' | ', $merges[count($merges) - 1]['aliases']));
    printf("      %-16s #%d, after the read-back\n", 'delete in the CP', $from->id);
    $pend = 0;
    foreach ($rows as $r) {
        if (($r['type'] ?? '') === 'pair') { continue; }
        if ($norm((string)($r['name'] ?? '')) === $norm((string)$from->title)
            && ($r['action'] ?? '') === 'approved') { $pend++; }
    }
    if ($pend) {
        printf("      %-16s %d approved decision repointed to the merge, or batch three rebuilds it\n",
            'decisions file', $pend);
    }
}

$relToDrop = 0;
foreach ($asRecord as $x) { $relToDrop += $x['articles']; }
echo PHP_EOL . str_repeat('-', 78) . PHP_EOL;
echo 'decisions to change: ' . count($inFile) . '   records to strip: ' . count($asRecord)
   . '   relations to drop: ' . $relToDrop . PHP_EOL;
echo 'canon entries to add as external: ' . count(array_filter($EXTERNAL)) . PHP_EOL;

if (!$APPLY) { echo PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

/* the decisions file */
foreach ($inFile as $x) {
    $rows[$x['idx']]['action'] = 'external';
    $rows[$x['idx']]['wikidataId'] = $x['qid'];
    unset($rows[$x['idx']]['articles'], $rows[$x['idx']]['aliases']);
    $rows[$x['idx']]['settledBy'] = 'Nathan, ' . date('Y-m-d') . ': the locality policy; a national figure';
}
$doc['decisions'] = array_values($rows);
$tmp = $FILE . '.tmp';
file_put_contents($tmp, json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n", LOCK_EX);
rename($tmp, $FILE);
@chmod($FILE, 0644);
echo 'decisions marked external: ' . count($inFile) . PHP_EOL;

/* the decision that would rebuild what the merge just folded away */
$repointed = 0;
foreach ($merges as $mg) {
    foreach ($rows as $i => $r) {
        if (($r['type'] ?? '') === 'pair') { continue; }
        if ($norm((string)($r['name'] ?? '')) !== $norm((string)$mg['from']->title)) { continue; }
        if (($r['action'] ?? '') === 'merged' && (int)($r['into'] ?? 0) === $mg['into']->id) { continue; }
        $rows[$i]['action'] = 'merged';
        $rows[$i]['into'] = $mg['into']->id;
        $rows[$i]['intoName'] = $mg['into']->title;
        unset($rows[$i]['intoKey'], $rows[$i]['articles']);
        $rows[$i]['settledBy'] = 'Nathan, ' . date('Y-m-d') . ': merged into #' . $mg['into']->id
            . '; the decision would otherwise recreate the record the merge removed';
        $repointed++;
    }
}
if ($repointed) {
    $doc['decisions'] = array_values($rows);
    $tmp2 = $FILE . '.tmp';
    file_put_contents($tmp2, json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n", LOCK_EX);
    rename($tmp2, $FILE);
    @chmod($FILE, 0644);
    echo 'merge decisions repointed: ' . $repointed . PHP_EOL;
}

/* the canon */
$CANON = \Craft::getAlias('@root') . '/inventory/legacy/name-canon.json';
$canon = json_decode(file_get_contents($CANON), true);
$added = 0;
foreach ($EXTERNAL as $name => $qid) {
    $exists = false;
    foreach ($canon['canon'] as $c) { if ($c['canonical'] === $name) { $exists = true; } }
    if ($exists) { continue; }
    $canon['canon'][] = [
        'canonical' => $name, 'type' => 'person', 'external' => true,
        'wikidataId' => $qid, 'aliases' => [],
        'note' => 'External. A national figure a local columnist wrote about, with no Santa '
                . 'Clarita Valley connection the articles document. The prose linker points the '
                . 'name at Wikidata; the archive holds no record.',
    ];
    $added++;
}
file_put_contents($CANON, json_encode($canon, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
echo 'canon entries added: ' . $added . PHP_EOL;

/* the existing records: relations off, record left for a person to delete */
$stripped = 0; $dropped = 0;
foreach ($asRecord as $x) {
    foreach ($ARTICLE_FIELDS as $fh) {
        foreach (\craft\elements\Entry::find()->section('articles')
                     ->relatedTo(['targetElement' => $x['entry'], 'field' => $fh])->status(null)->limit(null)->all() as $a) {
            $ids = array_values(array_filter(array_map(fn($r) => $r->id, $a->{$fh}->all()),
                fn($i) => $i !== $x['entry']->id));
            $a->setFieldValue($fh, $ids);
            if (\Craft::$app->elements->saveElement($a)) { $dropped++; }
        }
    }
    $stripped++;
}

$moved = 0; $aliasOk = 0;
foreach ($merges as $mg) {
    foreach ($mg['moving'] as $fh => $arts) {
        foreach ($arts as $a) {
            /* Repoint, not reassign: an article can already carry the target,
               and writing the target in twice is a duplicate relation that the
               control panel shows as two identical rows. */
            $ids = array_map(fn($r) => $r->id, $a->{$fh}->all());
            $ids = array_values(array_unique(array_map(
                fn($i) => $i === $mg['from']->id ? $mg['into']->id : $i, $ids)));
            $a->setFieldValue($fh, $ids);
            if (\Craft::$app->elements->saveElement($a)) { $moved++; }
        }
    }
    $mg['into']->setFieldValue('personAliases', implode("\n", $mg['aliases']));
    if (\Craft::$app->elements->saveElement($mg['into'])) {
        $back = \craft\elements\Entry::find()->id($mg['into']->id)->status(null)->one();
        if (str_contains((string)$back->getFieldValue('personAliases'), $mg['alias'])) { $aliasOk++; }
    }
}

$stranded = 0;
foreach ($merges as $mg) {
    foreach ($ARTICLE_FIELDS as $fh) {
        $stranded += (int)\craft\elements\Entry::find()->section('articles')
            ->relatedTo(['targetElement' => $mg['from'], 'field' => $fh])->status(null)->count();
    }
}

$left = 0;
foreach ($asRecord as $x) {
    foreach ($ARTICLE_FIELDS as $fh) {
        $left += \craft\elements\Entry::find()->section('articles')
            ->relatedTo(['targetElement' => $x['entry'], 'field' => $fh])->status(null)->count();
    }
}
echo PHP_EOL . 'READ-BACK' . PHP_EOL;
printf("   %-24s %-16s %s\n", 'records stripped', $stripped . ' of ' . count($asRecord), $stripped === count($asRecord) ? 'pass' : 'FAIL');
printf("   %-24s %-16s %s\n", 'relations remaining', (string)$left, $left === 0 ? 'pass' : 'FAIL');
$wantMove = array_sum(array_column($merges, 'n'));
printf("   %-24s %-16s %s\n", 'merge relations moved', $moved . ' of ' . $wantMove, $moved === $wantMove ? 'pass' : 'FAIL');
printf("   %-24s %-16s %s\n", 'merge aliases kept', $aliasOk . ' of ' . count($merges), $aliasOk === count($merges) ? 'pass' : 'FAIL');
printf("   %-24s %-16s %s\n", 'merge sources stranded', (string)$stranded, $stranded === 0 ? 'pass' : 'FAIL');
echo PHP_EOL . 'FOR DELETION IN THE CONTROL PANEL:' . PHP_EOL;
foreach ($asRecord as $x) { printf("   #%-6d %-28s external\n", $x['entry']->id, $x['name']); }
foreach ($merges as $mg) { printf("   #%-6d %-28s merged into #%d\n", $mg['from']->id, $mg['from']->title, $mg['into']->id); }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('mark_external_people.php', count($inFile) + $stripped,
    ($left === 0 && $moved === $wantMove && $stranded === 0 ? 'verified: ' : 'FAILED: ')
        . $stripped . ' stripped, ' . $left . ' relations left, ' . $moved . ' of ' . $wantMove . ' merged',
    count($inFile) . ' decisions marked external, ' . $added . ' canon entries');
