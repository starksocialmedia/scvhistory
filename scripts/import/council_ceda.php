/**
 * The council elections read against CEDA, the state's compilation of local
 * results (Nathan, 29 September 2026: "accept it ... cite it as a compilation
 * rather than a certified return, and lift 2020 to 2024 from derived to roster
 * on that basis"; turn on the 2014 links, "full first names from CEDA satisfy
 * the rule"; person records for the repeat candidates).
 *
 * Reads inventory/elections/ceda-scv.json (parse_ceda.py). CEDA holds the
 * council for 2004 to 2024; Santa Clarita's April elections of 1996 to 2002 are
 * not in it.
 *
 *   THE CHECK   Every council election CEDA holds is compared, candidate by
 *               candidate, on votes, on the number of seats (CEDA's "vote for")
 *               and on who is elected. Any difference refuses the whole run.
 *   2020-2024   outcomes and seats from derived to roster, and the office
 *               holdings those elections began; the notes say the roster is
 *               CEDA, a compilation, and that the declaring resolution is
 *               still wanted. 2004-2018 keep roster and gain CEDA as a second
 *               compilation beside the council ledger.
 *   2014        each surname-only candidacy is matched to CEDA's 2014 row by
 *               its vote count and surname; the full name is CEDA's, the note
 *               on the candidacy says so, and it links on that name under the
 *               usual rule. "Harper" is CEDA's "Berta Gonzalez-Harper" (928).
 *   PEOPLE      a person record for every name that stood more than once,
 *               public facts only: the name, the printings, the candidacies.
 *               Mike Lyons and Michael D. Lyons are one (Nathan). Names that
 *               may be one person under two first names (Ken and Kenneth
 *               Dean) are listed, not joined.
 *   THE BODY    electionBody on all 19 council elections is the City (#394);
 *               November 2024 is Council District 1. Council Districts 1 and
 *               3 become place records: 1 from the 2024 statement of votes, 3
 *               from the County's list of cancelled elections, which says it
 *               was filled by appointment in lieu of election.
 *   DOCUMENTS   two records: CEDA (no file attached: statewide spreadsheets,
 *               45 MB, kept in inventory/raw/ceda with their checksums), and
 *               the County's three final lists of cancelled elections, 2020,
 *               2022, 2024, with the PDFs (inventory/elections/county).
 *
 * Needs add_election_body.php first. Idempotent. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/council_ceda.php'))"
 */

use craft\elements\Entry;
use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements(); $fs = Craft::$app->getFields();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$CITY = 394; $refused = [];
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$ceda = json_decode(file_get_contents("$root/inventory/elections/ceda-scv.json"), true);
$cman = json_decode(file_get_contents("$root/inventory/elections/ceda-manifest.json"), true);
$county = json_decode(file_get_contents("$root/inventory/elections/county/manifest.json"), true);
if (!$ceda || !$cman || !$county) { echo 'REFUSING: run parse_ceda.py; the county manifest is missing' . PHP_EOL; return; }
foreach ($county['files'] as $f => $m) { if (hash_file('sha256', "$root/inventory/elections/county/$f") !== $m['sha256']) { echo "REFUSING: $f differs from its manifest" . PHP_EOL; return; } }
$schemaReady = (bool)$fs->getFieldByHandle('electionBody') && in_array('council-district', array_map(fn($o) => $o['value'], $fs->getFieldByHandle('districtKind')->options), true);
if (!$schemaReady) { echo 'NEEDS add_election_body.php first (the plan below is still checked)' . PHP_EOL; }
$CEDA_CITE = fn(int $y) => "California Elections Data Archive (CEDA), Center for California Studies and Institute for Social Research, California State University, Sacramento, with the Secretary of State: the $y candidates file (" . "CEDA{$y}Data.xls" . ($y >= 2011 ? 'x' : '') . '), a compilation of the counties\' returns, not a certified return';

/* ------------------------------------------------ the check */
$rows = array_values(array_filter($ceda['rows'], fn($r) => $r['body'] === 'city-of-santa-clarita'));
$byDate = []; foreach ($rows as $r) { $byDate[$r['date']][] = $r; }
$els = Entry::find()->section('elections')->status(null)->orderBy('electionDateEdtf asc')->all();
$cands = []; foreach ($els as $e) { $c = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $e, 'field' => 'candidacyElection'])->all(); usort($c, fn($a, $b) => ($b->votes ?? 0) <=> ($a->votes ?? 0)); $cands[$e->id] = $c; }
$norm = fn($s) => strtolower(trim(preg_replace('~[^A-Za-z -]~', '', \craft\helpers\StringHelper::toAscii((string)$s))));
$sur = fn($name) => $norm(preg_replace('~.*\s~', '', trim(preg_replace(['~,?\s*\b(Jr|Sr|II|III|IV)\b\.?~', '~\([^)]*\)~'], '', (string)$name))));
$match = []; /* candidacy id => ceda row */ $misspelt = [];
echo 'CEDA AGAINST THE ARCHIVE, council' . PHP_EOL;
foreach ($els as $e) {
    $cr = $byDate[$e->electionDateEdtf] ?? null;
    if (!$cr) { echo "   {$e->electionDateEdtf}  not in CEDA" . PHP_EOL; continue; }
    $bad = [];
    if ((int)$cr[0]['seats'] !== (int)$e->seatsUp) { $bad[] = "seats: CEDA {$cr[0]['seats']}, archive {$e->seatsUp}"; }
    if (count($cr) !== count($cands[$e->id])) { $bad[] = 'candidates: CEDA ' . count($cr) . ', archive ' . count($cands[$e->id]); }
    foreach ($cands[$e->id] as $c) {
        /* Same vote, and the same surname or one CEDA misspells by a letter or two
           ("Doydston", "Wieczoek"): the misspelling is reported, never adopted. */
        $near = fn($a, $b) => str_contains($a, $b) || str_contains($b, $a) || levenshtein($a, $b) <= 2;
        $hit = array_values(array_filter($cr, fn($r) => (int)$r['votes'] === (int)$c->votes && $near($norm($r['last']), $sur($c->nameAsPrinted))));
        if (count($hit) === 1 && !str_contains($norm($hit[0]['last']), $sur($c->nameAsPrinted)) && !str_contains($sur($c->nameAsPrinted), $norm($hit[0]['last']))) { $misspelt[] = "{$e->electionDateEdtf} CEDA prints \"{$hit[0]['last']}\" for {$c->nameAsPrinted} ({$c->votes} votes)"; }
        if (count($hit) !== 1) { $bad[] = "{$c->nameAsPrinted} {$c->votes}: " . (count($hit) ? 'ambiguous' : 'no CEDA row with that surname and vote'); continue; }
        $match[$c->id] = $hit[0];
        if ($hit[0]['elected'] !== ((string)$c->outcome->value === 'elected')) { $bad[] = "{$c->nameAsPrinted}: CEDA " . ($hit[0]['elected'] ? 'elected' : 'not elected') . ', archive ' . $c->outcome->value; }
    }
    if ($bad) { $refused[] = $e->electionDateEdtf . ': ' . implode('; ', $bad); }
    echo "   {$e->electionDateEdtf}  " . ($bad ? 'DIFFERS: ' . implode('; ', $bad) : count($cr) . ' candidates, votes, seats and winners agree') . PHP_EOL;
}

echo 'CEDA misspellings, same votes: ' . ($misspelt ? implode('; ', $misspelt) : 'none') . PHP_EOL;

/* ------------------------------------------------ the evidence */
$lift = []; $addCeda = [];
foreach ($els as $e) {
    if (!isset($byDate[$e->electionDateEdtf])) { continue; }
    $y = (int)substr($e->electionDateEdtf, 0, 4);
    $won = array_filter($cands[$e->id], fn($c) => (string)$c->outcome->value === 'elected');
    $ev = (string)(reset($won)?->outcomeEvidence->value ?? '');
    if ($ev === 'derived') { $lift[] = $e; } elseif ($ev === 'roster') { $addCeda[] = $e; }
}
echo PHP_EOL . 'LIFTED from derived to roster, on CEDA: ' . implode(', ', array_map(fn($e) => $e->electionDateEdtf, $lift)) . PHP_EOL;
echo 'CEDA ADDED beside the council ledger: ' . implode(', ', array_map(fn($e) => $e->electionDateEdtf, $addCeda)) . PHP_EOL;
$liftIds = array_map(fn($e) => $e->id, $lift);
$holdLift = [];
foreach (Entry::find()->section('officeHoldings')->status(null)->all() as $h) {
    if ((string)$h->startEvidence->value !== 'derived') { continue; }
    $y = substr((string)$h->termStartEdtf, 0, 4);
    $e = array_values(array_filter($lift, fn($e) => substr($e->electionDateEdtf, 0, 4) === $y));
    if ($e) { $holdLift[] = [$h, $e[0]]; echo "   holding #{$h->id} " . $h->holdingPerson->status(null)->one()?->title . " from {$h->termStart}: start derived -> roster" . PHP_EOL; }
}

/* ------------------------------------------------ names */
$keysOf = function (string $name) use ($norm): array {
    $name = preg_replace('~\.(?=[A-Za-z]{2})~', '. ', $name);
    $name = preg_replace('~,?\s*\b(Jr|Sr|II|III|IV)\b\.?~', '', $name);
    $nicks = []; if (preg_match_all('~[“"(]([^”")]+)[”")]~u', $name, $m)) { $nicks = $m[1]; }
    $plain = preg_replace(['~[“"][^”"]+[”"]~u', '~\([^)]*\)~'], ' ', $name);
    $t = array_values(array_filter(preg_split('~\s+~', trim($plain)), fn($w) => $w !== ''));
    if (count($t) < 2) { return []; }
    $last = $norm(end($t));
    $firsts = array_values(array_filter(array_slice($t, 0, -1), fn($w) => !preg_match('~^[A-Za-z]\.?$~', $w)));
    $keys = [];
    if ($firsts) { $keys[] = $norm($firsts[0]) . ' ' . $last; }
    foreach ($nicks as $n) { $keys[] = $norm($n) . ' ' . $last; }
    return array_unique($keys);
};
/* The name each candidacy stands under: as printed, or for 2014 CEDA's. */
$fullName = [];
$c2014 = [];
foreach ($els as $e) { foreach ($cands[$e->id] as $c) {
    if ($e->electionDateEdtf === '2014-04-08') {
        $r = $match[$c->id] ?? null;
        if (!$r) { $refused[] = "2014 {$c->nameAsPrinted}: no CEDA row"; continue; }
        $fullName[$c->id] = trim($r['first'] . ' ' . $r['last']); $c2014[$c->id] = $r;
    } else { $fullName[$c->id] = (string)$c->nameAsPrinted; }
} }
$CONFIRMED = ['mike lyons' => 'michael lyons'];   /* Nathan, 29 September 2026 */
$canon = fn(string $k) => $CONFIRMED[$k] ?? $k;
$persons = []; $pIndex = [];
foreach (Entry::find()->section('persons')->status(null)->all() as $p) {
    $names = [$p->title, (string)$p->fullName]; foreach (preg_split('~\n~', (string)$p->personAliases) as $a) { if (trim($a)) { $names[] = trim($a); } }
    foreach ($names as $n) { foreach ($keysOf($n) as $k) { $pIndex[$canon($k)][$p->id] = $p; } }
}
/* Candidacies that share any key are one name: "Edmund (Ed) G. Stevens" carries
   "ed stevens" as well as "edmund stevens", and joins "Ed Stevens". */
$groups = []; $keyGroup = [];
foreach ($fullName as $cid => $n) {
    $ks = array_map($canon, $keysOf($n)); if (!$ks) { continue; }
    $into = null; foreach ($ks as $k) { if (isset($keyGroup[$k])) { $into = $keyGroup[$k]; break; } }
    $into ??= $ks[0];
    $groups[$into][] = $cid; foreach ($ks as $k) { $keyGroup[$k] ??= $into; }
}
$allC = []; foreach ($cands as $cs) { foreach ($cs as $c) { $allC[$c->id] = $c; } }
/* One person under two first names? Listed for Nathan, never joined, and the
   shorter form waits: no record for it until he says. */
$NICK = ['ken' => 'kenneth', 'mike' => 'michael', 'bob' => 'robert', 'ed' => 'edmund', 'bill' => 'william', 'jim' => 'james', 'tim' => 'timothy', 'dan' => 'daniel', 'chuck' => 'charles', 'jeff' => 'jeffrey', 'doug' => 'douglas'];
$held = []; $heldKeys = [];
foreach ($groups as $k => $cids) { [$f, $l] = explode(' ', $k, 2); if (isset($NICK[$f]) && isset($groups[$NICK[$f] . ' ' . $l])) { $held[] = ucwords($k) . ' (' . count($cids) . ') and ' . ucwords($NICK[$f] . ' ' . $l) . ' (' . count($groups[$NICK[$f] . ' ' . $l]) . ')'; $heldKeys[$k] = true; } }
$titleOf = function (array $printings): string {
    /* The shortest printing, without initials, nicknames or a suffix: "Paul de la
       Cerda" keeps its particles, "M. Teresa Todd" is Teresa Todd, and CEDA's
       "Michael. Hogan" loses the stray point. Every printing stays an alias. */
    usort($printings, fn($a, $b) => strlen($a) <=> strlen($b));
    $t = preg_replace(['~[“"(][^”")]+[”")]~u', '~,?\s*\b(Jr|Sr|II|III|IV)\b\.?~', '~(?<=\w)\.(?=\s)~'], [' ', '', ''], $printings[0]);
    $w = array_values(array_filter(preg_split('~\s+~', trim($t)), fn($x) => $x !== '' && !preg_match('~^[A-Za-z]\.?$~', $x)));
    return implode(' ', $w);
};
$suffixOf = fn($n) => preg_match('~\b(Jr|Sr|II|III|IV)\b~', $n, $m) ? $m[1] : '';
foreach ($groups as $k => $cids) { $sx = array_unique(array_map(fn($cid) => $suffixOf($fullName[$cid]), $cids)); if (count($sx) > 1 && $k !== 'carl boyer') { $held[] = implode(' / ', array_unique(array_map(fn($cid) => $fullName[$cid], $cids))) . ' (a suffix on some printings only)'; $heldKeys[$k] = true; } }
$newPeople = []; $links = [];
foreach ($groups as $k => $cids) {
    if (isset($heldKeys[$k])) { continue; }
    $existing = $pIndex[$k] ?? [];
    if (count($existing) > 1) { $refused[] = "$k matches " . count($existing) . ' people'; continue; }
    $pid = $existing ? array_key_first($existing) : null;
    if (!$pid && count($cids) >= 2) {
        $printings = array_values(array_unique(array_map(fn($cid) => $fullName[$cid], $cids)));
        usort($printings, fn($a, $b) => strlen($a) <=> strlen($b));
        /* The title is the first name and surname, the middle names and initials
           left to the printings, which are all kept as aliases. */
        $title = $k === 'michael lyons' ? 'Mike Lyons' : $titleOf($printings);
        $newPeople[$k] = ['title' => $title, 'aliases' => array_values(array_diff(array_unique(array_merge($printings, array_map(fn($cid) => (string)$allC[$cid]->nameAsPrinted, $cids))), [$title], array_filter(array_merge($printings, array_map(fn($cid) => (string)$allC[$cid]->nameAsPrinted, $cids)), fn($a) => !str_contains(trim($a), ' ')))), 'cids' => $cids];   /* a surname alone is not an alias */
    }
    foreach ($cids as $cid) {
        if ($allC[$cid]->candidacyPerson->status(null)->ids()) { continue; }
        if ($pid || isset($newPeople[$k])) { $links[$cid] = $pid ?: "new:$k"; }
    }
}
echo PHP_EOL . 'PEOPLE, stood more than once (' . count($newPeople) . ')' . PHP_EOL;
foreach ($newPeople as $k => $p) {
    $years = array_map(fn($cid) => substr($allC[$cid]->candidacyElection->status(null)->one()->electionDateEdtf, 0, 4), $p['cids']); sort($years);
    echo '   create ' . str_pad($p['title'], 24) . implode(', ', $years) . ($p['aliases'] ? '; also printed ' . implode('; ', array_map(fn($a) => "\"$a\"", $p['aliases'])) : '') . PHP_EOL;
}
echo 'HELD, one person under two first names?: ' . ($held ? implode('; ', $held) : 'none') . PHP_EOL;
echo PHP_EOL . 'CANDIDACIES LINKED (' . count($links) . ')' . PHP_EOL;
foreach ($links as $cid => $to) {
    $c = $allC[$cid]; $e = $c->candidacyElection->status(null)->one();
    echo '   ' . $e->electionDateEdtf . ' ' . str_pad($c->nameAsPrinted . ($e->electionDateEdtf === '2014-04-08' ? " (CEDA: {$fullName[$cid]})" : ''), 40) . '-> ' . (is_int($to) ? "#$to " . $get($to)->title : $newPeople[substr($to, 4)]['title'] . ' (new)') . PHP_EOL;
}
$notLinked = array_filter($allC, fn($c) => !$c->candidacyPerson->status(null)->ids() && !isset($links[$c->id]));
echo 'Stood once, no record (' . count($notLinked) . '): stays a name on the election page.' . PHP_EOL;

/* ------------------------------------------------ districts and documents */
$DISTRICTS = [
    1 => 'Its first election was on 5 November 2024 (County of Los Angeles, Statement of Votes Cast, November 5, 2024, Council District 1).',
    3 => 'Its seat was first filled in 2024 without an election: the County\'s final list of cancelled elections for 5 November 2024 lists "Santa Clarita (Council District 3)" under appointment in lieu of election due to insufficiency of candidates.',
];
$distPlan = [];
foreach ($DISTRICTS as $n => $body) {
    $t = "Santa Clarita City Council, District $n";
    $have = Entry::find()->section('places')->status(null)->title($t)->one();
    echo ($have ? "#{$have->id} exists: " : 'create place ') . $t . PHP_EOL;
    if (!$have) { $distPlan[$n] = [$t, $body]; }
}
$cedaDoc = Entry::find()->section('documents')->status(null)->sourcePath($cman['source'])->one();
$countyDoc = Entry::find()->section('documents')->status(null)->sourcePath($county['files']['cancelled-elections-november-2024.pdf']['url'])->one();
echo ($cedaDoc ? "#{$cedaDoc->id} exists: " : 'create document ') . 'California Elections Data Archive (CEDA), candidate files, 1995 to 2024' . PHP_EOL;
echo ($countyDoc ? "#{$countyDoc->id} exists: " : 'create document ') . 'Final lists of cancelled elections, November 2020, 2022 and 2024 (3 PDFs)' . PHP_EOL;
$noBody = array_filter($els, fn($e) => !$schemaReady || !$e->getFieldValue('electionBody')->ids());
echo 'electionBody -> The City of Santa Clarita (#394) on ' . count($noBody) . ' council elections; November 5, 2024 -> Council District 1' . PHP_EOL;

echo PHP_EOL . 'SUMMARY: ' . count($lift) . ' elections lifted to roster, ' . count($holdLift) . ' holdings lifted, ' . count($addCeda) . ' elections gain CEDA, '
    . count($newPeople) . ' people, ' . count($links) . ' candidacies linked, ' . count($distPlan) . ' districts, ' . (int)!$cedaDoc + (int)!$countyDoc . ' documents.' . PHP_EOL;
echo 'REFUSED: ' . ($refused ? implode(' | ', $refused) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($refused) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }
if (!$schemaReady) { echo 'REFUSING: run add_election_body.php first' . PHP_EOL; return; }

/* ------------------------------------------------ apply, in one transaction */
$tx = Craft::$app->getDb()->beginTransaction();
try {
$docType = $svc->getEntryTypeByHandle('document'); $docSec = $svc->getSectionByHandle('documents');
if (!$cedaDoc) {
    $d = new Entry(); $d->sectionId = $docSec->id; $d->setTypeId($docType->id); $d->title = 'California Elections Data Archive (CEDA): candidate files, 1995 to 2024';
    $d->setFieldValues(['sourcePath' => $cman['source'], 'sourceLine' => 'Center for California Studies and Institute for Social Research, California State University, Sacramento, with the California Secretary of State',
        'originalPublishDate' => '1995 to 2024', 'originalPublishDateEdtf' => '1995/2024',
        'body' => 'CEDA compiles every county\'s returns for city, school district and community college elections into one file a year, with each candidate\'s votes and whether they were elected. It is a compilation, not a certified return: this archive cites it as a roster, and an outcome it supports is upgraded to certified only by the body\'s own declaring resolution.',
        'editorNotes' => [['heading' => 'Where the files came from', 'position' => 'bottom', 'note' => 'The yearly candidate files, unmodified, as kept in the repository github.com/justindbk/ceda (Justin de Benedictis-Kessner and Rachel Bernhard), commit ' . substr($cman['commit'], 0, 7) . ', because the Sacramento State portal could not be downloaded from. That repository\'s corrections are a separate script and are not applied here; none concerns the Santa Clarita Valley. The checksums are in inventory/elections/ceda-manifest.json.']],
        'recordProvenance' => 'council_ceda.php, 29 September 2026: 30 files fetched and checksummed 29 September 2026']);
    if (!$elements->saveElement($d)) { throw new \RuntimeException('CEDA document: ' . json_encode($d->getFirstErrors())); }
    $cedaDoc = $d;
}
if (!$countyDoc) {
    $volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $assets = Craft::$app->getAssets();
    $folder = $assets->findFolder(['volumeId' => $volume->id, 'path' => 'elections/']);
    $ids = [];
    foreach ($county['files'] as $f => $m) {
        $tmp = sys_get_temp_dir() . '/' . $f; copy("$root/inventory/elections/county/$f", $tmp);
        $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($f); $a->newFolderId = $folder->id; $a->setVolumeId($volume->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = true;
        if (!$elements->saveElement($a)) { throw new \RuntimeException("$f: " . json_encode($a->getFirstErrors())); }
        $ids[] = $a->id;
    }
    $d = new Entry(); $d->sectionId = $docSec->id; $d->setTypeId($docType->id); $d->title = 'Final lists of cancelled elections: November 2020, 2022 and 2024';
    $d->setFieldValues(['sourcePath' => $county['files']['cancelled-elections-november-2024.pdf']['url'], 'sourceLine' => $county['by'],
        'originalPublishDate' => '2020 to 2024', 'originalPublishDateEdtf' => '2020/2024', 'documentFiles' => $ids,
        'body' => 'For each general election, the County lists the contests it did not hold: "appointment in lieu of election due to insufficiency of candidates". Where no more candidates filed than there were seats, no vote was taken and the body appointed the candidates, or filled the seat. These lists are the only record here of those seats.',
        'recordProvenance' => 'council_ceda.php, 29 September 2026: ' . implode('; ', array_map(fn($f, $m) => "$f from {$m['url']}, SHA-256 {$m['sha256']}", array_keys($county['files']), $county['files']))]);
    if (!$elements->saveElement($d)) { throw new \RuntimeException('county document: ' . json_encode($d->getFirstErrors())); }
    $countyDoc = $d;
}
$placeSec = $svc->getSectionByHandle('places'); $placeType = $svc->getEntryTypeByHandle('place');
$distIds = [];
foreach ($DISTRICTS as $n => $body) {
    $t = "Santa Clarita City Council, District $n";
    $p = Entry::find()->section('places')->status(null)->title($t)->one();
    if (!$p) {
        $p = new Entry(); $p->sectionId = $placeSec->id; $p->setTypeId($placeType->id); $p->title = $t;
        $p->setFieldValues(['districtKind' => 'council-district', 'districtNumber' => (string)$n, 'placeOrganizations' => [$CITY],
            'body' => "One of the five districts the Santa Clarita City Council has been elected from since 2024, when the City moved from at-large elections. $body",
            'recordProvenance' => 'council_ceda.php, 29 September 2026']);
        if (!$elements->saveElement($p)) { throw new \RuntimeException("$t: " . json_encode($p->getFirstErrors())); }
    }
    $distIds[$n] = $p->id;
}
$short = [];
$pSec = $svc->getSectionByHandle('persons'); $pType = $svc->getEntryTypeByHandle('person');
$newIds = [];
foreach ($newPeople as $k => $np) {
    $p = Entry::find()->section('persons')->status(null)->title($np['title'])->one();
    if (!$p) {
        $p = new Entry(); $p->sectionId = $pSec->id; $p->setTypeId($pType->id); $p->title = $np['title'];
        $p->setFieldValues(['fullName' => $np['title'], 'personAliases' => implode("\n", $np['aliases']),
            'recordProvenance' => 'council_ceda.php, 29 September 2026: stood for the Santa Clarita City Council more than once; public facts only']);
        if (!$elements->saveElement($p)) { throw new \RuntimeException("person {$np['title']}: " . json_encode($p->getFirstErrors())); }
    }
    $newIds["new:$k"] = $p->id;
}
foreach ($links as $cid => $to) {
    $c = $get($cid); $c->setFieldValue('candidacyPerson', [is_int($to) ? $to : $newIds[$to]]);
    if (isset($c2014[$cid])) {
        $r = $c2014[$cid];
        $c->setFieldValue('footnotes', $fn(['The City\'s 2014 results print surnames only. The full name, ' . $fullName[$cid] . ', is from CEDA\'s 2014 file (row ' . $r['row'] . '), which gives the same ' . number_format($r['votes']) . ' votes. ' . $CEDA_CITE(2014) . '.']));
    }
    if (!$elements->saveElement($c)) { $short[] = "link #$cid"; }
}
/* 2014 candidacies not linked still carry their CEDA name in a note. */
foreach ($c2014 as $cid => $r) { if (!isset($links[$cid])) { $c = $get($cid); if (!$c->footnotes) {
    $c->setFieldValue('footnotes', $fn(['The City\'s 2014 results print surnames only. The full name, ' . $fullName[$cid] . ', is from CEDA\'s 2014 file (row ' . $r['row'] . '), which gives the same ' . number_format($r['votes']) . ' votes. ' . $CEDA_CITE(2014) . '.']));
    if (!$elements->saveElement($c)) { $short[] = "note #$cid"; } } } }

foreach ($els as $e) {
    $e = $get($e->id);
    $vals = ['electionBody' => [$CITY]];
    if ($e->electionDateEdtf === '2024-11-05') { $vals['electionDistrict'] = [$distIds[1]]; }
    $y = (int)substr($e->electionDateEdtf, 0, 4);
    if (isset($byDate[$e->electionDateEdtf])) {
        $n = (int)$e->seatsUp;
        $cedaNote = 'CEDA agrees: it gives the same ' . count($cands[$e->id]) . ' candidates and votes, ' . $n . ' to be elected, and marks the same ' . ($n === 1 ? 'winner' : $n . ' winners') . '. ' . $CEDA_CITE($y) . '.';
        $notes = array_values(array_filter(array_map(fn($r) => (string)($r['note'] ?? ''), $e->footnotes ?? []), fn($t) => $t !== '' && !str_starts_with($t, 'CEDA agrees')));
        if (in_array($e->id, $liftIds, true)) {
            $notes = array_values(array_filter($notes, fn($t) => !str_starts_with($t, 'Winners:') && !str_starts_with($t, 'Seats:')));
            $notes[] = 'Winners: the first ' . $n . ' of this count. No declaring resolution is held, and the council ledger in the archive ends in 2020; the winners rest on the count and on CEDA, the state\'s compilation of local results, which marks the same ' . ($n === 1 ? 'candidate' : 'candidates') . ' elected. They are upgraded to certified when the declaring resolution is obtained.';
            if ((string)$e->seatsUpEvidence->value === 'derived') { $vals['seatsUpEvidence'] = 'roster'; }
            foreach ($cands[$e->id] as $c) { $c = $get($c->id); if ((string)$c->outcomeEvidence->value === 'derived') { $c->setFieldValue('outcomeEvidence', 'roster'); if (!$elements->saveElement($c)) { $short[] = "outcome #{$c->id}"; } } }
        }
        $notes[] = $cedaNote;
        $vals['footnotes'] = $fn($notes);
        $vals['sourceDocuments'] = array_values(array_unique(array_merge($e->sourceDocuments->status(null)->ids(), [$cedaDoc->id])));
    }
    $e->setFieldValues($vals);
    if (!$elements->saveElement($e)) { $short[] = 'election ' . $e->electionDateEdtf . ': ' . json_encode($e->getFirstErrors()); }
}
foreach ($holdLift as [$h, $e]) {
    $h = $get($h->id); $y = (int)substr($e->electionDateEdtf, 0, 4);
    $notes = array_values(array_filter(array_map(fn($r) => (string)($r['note'] ?? ''), $h->footnotes ?? []), fn($t) => $t !== '' && !str_starts_with($t, 'No declaring document is held')));
    $notes[] = 'No declaring document is held: the outcome rests on the count and on CEDA, the state\'s compilation of local results, which marks the same winner. ' . $CEDA_CITE($y) . '.';
    $h->setFieldValues(['startEvidence' => 'roster', 'footnotes' => $fn($notes)]);
    if (!$elements->saveElement($h)) { $short[] = "holding #{$h->id}"; }
}
if ($short) { throw new \RuntimeException('writes refused: ' . implode('; ', $short)); }
$tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }

/* Read back. */
$short = [];
$left = Entry::find()->section('candidacies')->status(null)->outcomeEvidence('derived')->count();
if ((int)$left) { $short[] = "$left candidacies still derived"; }
if ((int)Entry::find()->section('officeHoldings')->status(null)->startEvidence('derived')->count()) { $short[] = 'holdings still derived'; }
foreach ($els as $e) { if (!$get($e->id)->electionBody->ids()) { $short[] = 'no body on ' . $e->electionDateEdtf; } }
foreach ($links as $cid => $to) { if (!$get($cid)->candidacyPerson->status(null)->ids()) { $short[] = "unlinked #$cid"; } }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('council_ceda.php', count($lift) + count($holdLift) + count($newPeople) + count($links), $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'council read against CEDA; 2020-2024 to roster; 2014 names; repeat candidates; council districts 1 and 3');
if ($short) { throw new \RuntimeException('council_ceda: ' . implode('; ', $short)); }
