/**
 * The valley's water board elections, 2016 to 2024, from the County's certified
 * returns (Nathan, 30 September 2026: "yes to water 2016 to 2024 now"). The
 * earlier years wait for one pass through the County's scanned volumes, with
 * the school boards before 1995.
 *
 * Reads inventory/elections/county-svc-water.json (parse_county_svc.py): 14
 * contests. Castaic Lake Water Agency, November 2016: at large and Divisions 1
 * to 3. Santa Clarita Valley Water, 2020 and 2022: Divisions 1 to 3; 2024:
 * Divisions 1 and 2, and Division 3 twice, a full term and a two-year term.
 *
 *   THE BODIES  SCV Water is #402. Castaic Lake Water Agency becomes its own
 *               record: its 2016 election cannot belong to an agency formed in
 *               2018. "Castaic Lake Water Agency" leaves #402's aliases for it.
 *               SCV Water was "created January 1, 2018 by Senate Bill 634 ...
 *               which merged three water agencies" (the agency's Who We Are):
 *               Castaic Lake is dissolved 1 January 2018, succeeded by #402,
 *               and #402 is preceded by it.
 *   DISTRICTS   Castaic Lake Water Agency Divisions 1 to 3, place records like
 *               SCV Water's.
 *   FIGURES     votes, registration and ballots cast, summed from the County's
 *               precinct statements; where the Official Election Returns
 *               exist (2016, 2020, 2022) the votes are the returns'. Seats are
 *               the returns' "VOTE FOR", or for 2024 the statement's own figure.
 *   VENTURA     seven contests were "(SHARED W/VENTURA CO)": part of the
 *               division votes in Ventura County, whose figures are not here.
 *               The page says so, and where the last winning margin is under
 *               one per cent of the ballots cast it says the Ventura share could
 *               have decided it (2020, Division 3: 111 votes).
 *   WINNERS     the first N of the count. "roster" where SCV Water's own list of
 *               directors (read 30 September 2026) names the winner in that
 *               division with the term that election began; otherwise
 *               "derived". Names match on the full first name, so "BILL COOPER"
 *               is not confirmed by "William Cooper": that pair is held for
 *               Nathan, like the other first-name pairs.
 *   PEOPLE      linked on the full first name and surname; a record for anyone
 *               who has now stood more than once across the council, the
 *               boards and the water boards. Printed in capitals by the County,
 *               so a new person's title takes another body's printing where
 *               there is one, and is title-cased otherwise.
 *   DOCUMENTS   one per election: the statement's contest spreadsheets, and the
 *               returns where they exist; and SCV Water's list of directors.
 *
 * Idempotent (elections by slug, candidacies by election and name, places,
 * documents and the organization by title or source). Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_water_boards.php'))"
 */

use craft\elements\Entry;
use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$refused = [];
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$data = json_decode(file_get_contents("$root/inventory/elections/county-svc-water.json"), true);
$man = json_decode(file_get_contents("$root/inventory/elections/county/svc-manifest.json"), true);
$dir = json_decode(file_get_contents("$root/inventory/elections/scv-water-directors-2026-09-30.json"), true);
if (!$data || !$man || !$dir) { echo 'REFUSING: run parse_county_svc.py; the directors list is missing' . PHP_EOL; return; }
foreach ($man['contests'] as $f => $m) { if (hash_file('sha256', "$root/inventory/elections/county/svc/$f") !== $m['sha256']) { echo "REFUSING: $f differs from the manifest" . PHP_EOL; return; } }
foreach ($man['returns'] as $f => $m) { if (hash_file('sha256', "$root/inventory/elections/county/$f") !== $m['sha256']) { echo "REFUSING: $f differs from the manifest" . PHP_EOL; return; } }

$scv = $get(402);
if (!$scv || $scv->title !== 'Santa Clarita Valley Water') { echo 'REFUSING: #402 is not Santa Clarita Valley Water' . PHP_EOL; return; }
$clwa = Entry::find()->section('organizations')->status(null)->title('Castaic Lake Water Agency')->one();
$scvAliases = (string)$scv->orgAliases;
$aliasOff = preg_match('~(,\s*)?Castaic Lake Water Agency~', $scvAliases);
echo 'BODIES' . PHP_EOL . '   ' . ($clwa ? "#{$clwa->id} exists: " : 'create organization ') . 'Castaic Lake Water Agency (government)' . ($aliasOff ? '; "Castaic Lake Water Agency" leaves the aliases of #402' : '') . PHP_EOL;
$divPlan = [];
foreach (['castaic-lake-water-agency' => 'Castaic Lake Water Agency', 'santa-clarita-valley-water' => 'Santa Clarita Valley Water'] as $b => $bt) {
    for ($n = 1; $n <= 3; $n++) {
        $t = "$bt, Division $n"; $have = Entry::find()->section('places')->status(null)->title($t)->one();
        if ($b === 'santa-clarita-valley-water' && !$have) { $refused[] = "no place $t (import_ceda_school_boards.php makes it)"; }
        if (!$have) { echo "   create place $t" . PHP_EOL; }
        $divPlan["$b|$n"] = [$t, $have?->id];
    }
}

/* ------------------------------------------------ names */
$norm = fn($s) => strtolower(trim(preg_replace('~[^A-Za-z -]~', '', \craft\helpers\StringHelper::toAscii((string)$s))));
$keysOf = function (string $name) use ($norm): array {
    $name = preg_replace(['~\.(?=[A-Za-z]{2})~', '~,?\s*\b(Jr|Sr|II|III|IV|P\.E)\b\.?~i'], ['. ', ''], $name);
    $nicks = []; if (preg_match_all('~[“"(]([^”")]+)[”")]~u', $name, $m)) { $nicks = $m[1]; }
    $plain = preg_replace(['~[“"][^”"]+[”"]~u', '~\([^)]*\)~'], ' ', $name);
    $t = array_values(array_filter(preg_split('~\s+~', trim($plain)), fn($w) => $w !== ''));
    if (count($t) < 2) { return []; }
    $last = $norm(end($t));
    $firsts = array_values(array_filter(array_slice($t, 0, -1), fn($w) => !preg_match('~^[A-Za-z]\.?$~', $w)));
    $keys = [];
    if ($firsts) { $keys[] = $norm(rtrim($firsts[0], '.')) . ' ' . $last; }
    foreach ($nicks as $n) { $keys[] = $norm($n) . ' ' . $last; }
    return array_unique($keys);
};
$tc = fn($s) => preg_replace_callback("~\\b(Mc|O')([a-z])~", fn($m) => $m[1] . strtoupper($m[2]), ucwords(strtolower($s), " -'"));

/* ------------------------------------------------ the contests */
$printed = fn($d) => date('F j, Y', strtotime($d));
$short = ['castaic-lake-water-agency' => 'clwa', 'santa-clarita-valley-water' => 'scv-water'];
echo PHP_EOL . 'ELECTIONS (' . count($data['contests']) . ')' . PHP_EOL;
$plan = [];
foreach ($data['contests'] as $c) {
    $y = (int)substr($c['date'], 0, 4);
    $slug = \craft\helpers\ElementHelper::normalizeSlug($short[$c['body']] . ' board election ' . $printed($c['date']) . ' ' . ($c['division'] ? 'division ' . $c['division'] : 'at large') . ($c['term'] === 'two-year' ? ' two year term' : ''));
    $have = Entry::find()->section('elections')->status(null)->slug($slug)->one();
    $win = array_slice($c['candidates'], 0, $c['seats']); $next = $c['candidates'][$c['seats']] ?? null;
    /* SCV Water's own list confirms a winner when it names them in that division with the term this election began. */
    $expires = 'January ' . ($y + ($c['term'] === 'two-year' ? 3 : 5));
    $confirm = [];
    foreach ($win as $w) {
        $hit = $c['body'] === 'santa-clarita-valley-water' ? array_values(array_filter($dir['directors'], fn($d) => $d['division'] === $c['division'] && $d['expires'] === $expires && array_intersect($keysOf($d['name']), $keysOf($tc($w['name']))))) : [];
        $confirm[$w['name']] = $hit ? $hit[0]['name'] : null;
    }
    $ev = count(array_filter($confirm)) === count($win) ? 'roster' : 'derived';
    $margin = $next ? $win[count($win) - 1]['votes'] - $next['votes'] : null;
    $close = $c['sharedWithVentura'] && $margin !== null && $c['ballots'] && $margin / $c['ballots'] < 0.01;
    $plan[] = [$c, $slug, $have, $ev, $confirm, $margin, $close];
    echo '   ' . ($have ? "#{$have->id} " : 'create ') . str_pad($slug, 58) . str_pad("{$c['seats']} seat" . ($c['seats'] > 1 ? 's' : ''), 8) . str_pad($ev, 9) . implode(', ', array_map(fn($w) => $w['name'] . ($confirm[$w['name']] ? ' (list: ' . $confirm[$w['name']] . ')' : ''), $win))
        . ($c['sharedWithVentura'] ? ' | Ventura' . ($close ? ", CLOSE: $margin votes" : '') : '') . PHP_EOL;
}

/* ------------------------------------------------ people */
$CONFIRMED = ['mike lyons' => 'michael lyons'];
$canon = fn(string $k) => $CONFIRMED[$k] ?? $k;
$pIndex = [];
foreach (Entry::find()->section('persons')->status(null)->all() as $p) {
    $names = [$p->title, (string)$p->fullName]; foreach (preg_split('~\n~', (string)$p->personAliases) as $a) { if (trim($a)) { $names[] = trim($a); } }
    foreach ($names as $n) { foreach ($keysOf($n) as $k) { $pIndex[$canon($k)][$p->id] = $p->title; } }
}
$stood = [];   /* [name, 'water'|'other', ref, linked?] */
foreach ($plan as $i => [$c]) { foreach ($c['candidates'] as $x) { $stood[] = [$x['name'], 'water', "$i|{$x['name']}", false]; } }
foreach (Entry::find()->section('candidacies')->status(null)->all() as $cd) {
    $e = $cd->candidacyElection->status(null)->one(); if (!$e) { continue; }
    if (($e->electionBody->ids()[0] ?? null) === 402 || str_contains((string)$e->recordProvenance, 'import_water_boards')) { continue; }
    $n = (string)$cd->nameAsPrinted;
    foreach (array_map(fn($r) => (string)($r['note'] ?? ''), $cd->footnotes ?? []) as $note) { if (preg_match('~The full name, ([^,]+), is from CEDA~', $note, $m)) { $n = $m[1]; } }
    $stood[] = [$n, 'other', $cd->id, (bool)$cd->candidacyPerson->status(null)->ids()];
}
$groups = []; $keyGroup = [];
foreach ($stood as $i => $s) {
    $ks = array_map($canon, $keysOf($s[0])); if (!$ks) { continue; }
    $into = null; foreach ($ks as $k) { if (isset($keyGroup[$k])) { $into = $keyGroup[$k]; break; } }
    $into ??= $ks[0]; $groups[$into][] = $i; foreach ($ks as $k) { $keyGroup[$k] ??= $into; }
}
$NICK = ['ken' => 'kenneth', 'mike' => 'michael', 'bob' => 'robert', 'ed' => 'edward', 'bill' => 'william', 'jim' => 'james', 'tim' => 'timothy', 'dan' => 'daniel', 'chuck' => 'charles', 'jeff' => 'jeffrey', 'doug' => 'douglas', 'tom' => 'thomas', 'rick' => 'richard', 'steve' => 'steven', 'joe' => 'joseph', 'kathy' => 'katherine'];
$held = []; $heldKeys = [];
foreach ($groups as $k => $is) { [$f, $l] = explode(' ', $k, 2); if (isset($NICK[$f], $groups[$NICK[$f] . ' ' . $l]) && array_filter(array_merge($is, $groups[$NICK[$f] . ' ' . $l]), fn($i) => $stood[$i][1] === 'water')) { $held[] = ucwords($k) . ' and ' . ucwords($NICK[$f] . ' ' . $l); $heldKeys[$k] = $heldKeys[$NICK[$f] . ' ' . $l] = true; } }
$personFor = []; $create = []; $crossLinks = [];
foreach ($groups as $k => $is) {
    if (!array_filter($is, fn($i) => $stood[$i][1] === 'water') || isset($heldKeys[$k])) { continue; }
    $ex = $pIndex[$k] ?? [];
    if (count($ex) > 1) { $refused[] = "$k matches " . count($ex) . ' people'; continue; }
    if ($ex) { $pid = array_key_first($ex); }
    elseif (count($is) >= 2) {
        $mixed = array_values(array_filter(array_map(fn($i) => $stood[$i][0], $is), fn($n) => $n !== strtoupper($n)));
        $base = $mixed ? $mixed : array_map(fn($i) => $tc($stood[$i][0]), $is);
        usort($base, fn($a, $b) => strlen($a) <=> strlen($b));
        $w = array_values(array_filter(preg_split('~\s+~', trim(preg_replace('~,?\s*\b(Jr|Sr|II|III|IV)\b\.?~', '', $base[0]))), fn($x) => !preg_match('~^[A-Za-z]\.?$~', $x)));
        $title = implode(' ', $w);
        $pid = "new:$k"; $create[$k] = ['title' => $title, 'aliases' => array_values(array_unique(array_filter(array_map(fn($i) => $tc($stood[$i][0]), $is), fn($a) => $a !== $title && str_contains($a, ' ')))), 'is' => $is];
    } else { continue; }
    foreach ($is as $i) { if ($stood[$i][1] === 'water') { $personFor[$stood[$i][2]] = $pid; } elseif (!$stood[$i][3]) { $crossLinks[$stood[$i][2]] = $pid; } }
}
$label = fn($p) => is_string($p) ? $create[substr($p, 4)]['title'] . ' (new)' : "#$p " . $get($p)->title;
echo PHP_EOL . 'PEOPLE: ' . count($create) . ' to create' . PHP_EOL;
foreach ($create as $c) { echo '   ' . str_pad($c['title'], 22) . implode('; ', array_map(fn($i) => $stood[$i][1] === 'water' ? substr($data['contests'][(int)explode('|', $stood[$i][2])[0]]['date'], 0, 4) . ' water' : substr($get($stood[$i][2])->candidacyElection->one()->electionDateEdtf, 0, 4) . ' ' . (($b = $get($stood[$i][2])->candidacyElection->one()->electionBody->one()) && $b->id !== 394 ? $b->title : 'council'), $c['is'])) . PHP_EOL; }
echo 'Water candidacies linked to people already in the archive: ' . implode('; ', array_unique(array_map($label, array_filter($personFor, 'is_int')))) . PHP_EOL;
echo 'Other candidacies linked across: ' . ($crossLinks ? implode('; ', array_map(fn($cid, $p) => $get($cid)->nameAsPrinted . ' -> ' . $label($p), array_keys($crossLinks), $crossLinks)) : 'none') . PHP_EOL;
echo 'HELD, one person under two first names?: ' . ($held ? implode('; ', $held) : 'none') . PHP_EOL;
$docTitles = ['2016-11-08' => 'Statement of Votes Cast and Official Election Returns, General Election, November 8, 2016: Castaic Lake Water Agency',
    '2020-11-03' => 'Statement of Votes Cast and Official Election Returns, General Election, November 3, 2020: Santa Clarita Valley Water Agency',
    '2022-11-08' => 'Statement of Votes Cast and Official Election Returns, General Election, November 8, 2022: Santa Clarita Valley Water Agency',
    '2024-11-05' => 'Statement of Votes Cast, General Election, November 5, 2024: Santa Clarita Valley Water Agency'];
$DIR_TITLE = 'Santa Clarita Valley Water Agency: Board of Directors (web page, read 30 September 2026)';
foreach (array_merge(array_values($docTitles), [$DIR_TITLE]) as $t) { $d = Entry::find()->section('documents')->status(null)->title($t)->one(); echo ($d ? "#{$d->id} exists: " : 'create document ') . $t . PHP_EOL; }

echo PHP_EOL . 'SUMMARY: ' . count(array_filter($plan, fn($p) => !$p[2])) . ' elections, ' . array_sum(array_map(fn($p) => count($p[0]['candidates']), $plan)) . ' candidacies, ' . count($create) . ' people, ' . count(array_filter($divPlan, fn($d) => !$d[1])) . ' places, ' . ($clwa ? 0 : 1) . ' organization.' . PHP_EOL;
echo 'REFUSED: ' . ($refused ? implode(' | ', $refused) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($refused) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

/* ------------------------------------------------ apply, in one transaction */
$tx = Craft::$app->getDb()->beginTransaction();
try {
    $orgSec = $svc->getSectionByHandle('organizations');
    if (!$clwa) {
        $clwa = new Entry(); $clwa->sectionId = $orgSec->id; $clwa->setTypeId($orgSec->getEntryTypes()[0]->id); $clwa->title = 'Castaic Lake Water Agency';
        $clwa->setFieldValues(['orgType' => 'government', 'orgAliases' => 'CLWA', 'dateDissolved' => 'January 1, 2018', 'dateDissolvedEdtf' => '2018-01-01', 'succeededBy' => [402],
            'body' => 'The Santa Clarita Valley\'s wholesale water agency, governed by an elected board: a director at large and directors for three divisions. On 1 January 2018 it was merged into the Santa Clarita Valley Water Agency, created by Senate Bill 634.[1]',
            'footnotes' => $fn(['Santa Clarita Valley Water Agency, "Who We Are", yourscvwater.com/who-we-are, read 30 September 2026: "created January 1, 2018 by Senate Bill 634, an act of the State Legislature, which merged three water agencies in the Santa Clarita Valley."']),
            'recordProvenance' => 'import_water_boards.php, 30 September 2026']);
        if (!$elements->saveElement($clwa)) { throw new \RuntimeException('CLWA: ' . json_encode($clwa->getFirstErrors())); }
    }
    /* #402: the alias goes to the new record, which it now follows. */
    $s = $get(402); $pre = $s->precededBy->status(null)->ids();
    if ($aliasOff || !in_array($clwa->id, $pre)) {
        if ($aliasOff) { $s->setFieldValue('orgAliases', trim(preg_replace('~(,\s*)?Castaic Lake Water Agency~', '', (string)$s->orgAliases), ', ')); }
        $s->setFieldValue('precededBy', array_values(array_unique(array_merge($pre, [$clwa->id]))));
        if (!$elements->saveElement($s)) { throw new \RuntimeException('#402: ' . json_encode($s->getFirstErrors())); }
    }
    $bodyId = ['castaic-lake-water-agency' => $clwa->id, 'santa-clarita-valley-water' => 402];
    $placeSec = $svc->getSectionByHandle('places'); $placeType = $svc->getEntryTypeByHandle('place');
    foreach ($divPlan as $k => [$t, $id]) {
        if ($id) { continue; }
        [$b, $n] = explode('|', $k);
        $p = new Entry(); $p->sectionId = $placeSec->id; $p->setTypeId($placeType->id); $p->title = $t;
        $p->setFieldValues(['districtKind' => 'water-division', 'districtNumber' => $n, 'placeOrganizations' => [$bodyId[$b]],
            'body' => "Division $n of the Castaic Lake Water Agency, which elected a director for each of its three divisions as well as one at large, until the agency was merged into the Santa Clarita Valley Water Agency in 2018.",
            'recordProvenance' => 'import_water_boards.php, 30 September 2026']);
        if (!$elements->saveElement($p)) { throw new \RuntimeException("$t: " . json_encode($p->getFirstErrors())); }
        $divPlan[$k][1] = $p->id;
    }
    /* Documents. */
    $docType = $svc->getEntryTypeByHandle('document'); $docSec = $svc->getSectionByHandle('documents');
    $volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $assets = Craft::$app->getAssets();
    $folder = $assets->findFolder(['volumeId' => $volume->id, 'path' => 'elections/']);
    $upload = function (string $path) use ($elements, $folder, $volume) {
        $tmp = sys_get_temp_dir() . '/' . basename($path); copy($path, $tmp);
        $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename(basename($path)); $a->newFolderId = $folder->id; $a->setVolumeId($volume->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = true;
        if (!$elements->saveElement($a)) { throw new \RuntimeException(basename($path) . ': ' . json_encode($a->getFirstErrors())); }
        return $a->id;
    };
    $docFor = [];
    foreach ($docTitles as $date => $t) {
        $d = Entry::find()->section('documents')->status(null)->title($t)->one();
        if (!$d) {
            $eid = array_key_first(array_filter($man['zips'], fn($z) => $z['date'] === $date));
            $files = array_keys(array_filter($man['contests'], fn($m) => $m['zip'] === $eid));
            $ret = array_keys(array_filter($man['returns'], fn($m, $f) => str_contains($f, $eid), ARRAY_FILTER_USE_BOTH));
            $ids = array_merge(array_map(fn($f) => $upload("$root/inventory/elections/county/svc/$f"), $files), array_map(fn($f) => $upload("$root/inventory/elections/county/$f"), $ret));
            $d = new Entry(); $d->sectionId = $docSec->id; $d->setTypeId($docType->id); $d->title = $t;
            $d->setFieldValues(['sourcePath' => $man['zips'][$eid]['url'], 'sourceLine' => 'County of Los Angeles, Registrar-Recorder/County Clerk', 'originalPublishDate' => $printed($date), 'originalPublishDateEdtf' => $date, 'documentFiles' => $ids,
                'body' => 'The County\'s certified count, precinct by precinct: one spreadsheet for each water board contest, from the statement of votes cast for the whole election' . ($ret ? ', and the Official Election Returns, which give each contest\'s totals and the number to be elected' : '') . '.',
                'recordProvenance' => 'import_water_boards.php, 30 September 2026: ' . $man['zips'][$eid]['url'] . ', SHA-256 ' . $man['zips'][$eid]['sha256'] . ($ret ? '; ' . implode('; ', array_map(fn($f) => $man['returns'][$f]['url'], $ret)) : '')]);
            if (!$elements->saveElement($d)) { throw new \RuntimeException("$t: " . json_encode($d->getFirstErrors())); }
        }
        $docFor[$date] = $d->id;
    }
    $dirDoc = Entry::find()->section('documents')->status(null)->title($DIR_TITLE)->one();
    if (!$dirDoc) {
        $dirDoc = new Entry(); $dirDoc->sectionId = $docSec->id; $dirDoc->setTypeId($docType->id); $dirDoc->title = $DIR_TITLE;
        $dirDoc->setFieldValues(['sourcePath' => 'https://www.yourscvwater.com/governance/board-directors', 'sourceLine' => 'Santa Clarita Valley Water Agency', 'originalPublishDate' => 'read 30 September 2026', 'originalPublishDateEdtf' => '2026-09-30',
            'body' => 'The agency\'s own list of its directors, each with a division, the month elected or appointed, and the month the term expires. It describes the board as "nine directly elected directors, from three electoral divisions". Read on 30 September 2026 it named ten, one with a term shown as expired in January 2025. The archive keeps the names, divisions and terms (inventory/elections/scv-water-directors-2026-09-30.json), not the biographies.',
            'recordProvenance' => 'import_water_boards.php, 30 September 2026: page SHA-256 ' . $dir['htmlSha256']]);
        if (!$elements->saveElement($dirDoc)) { throw new \RuntimeException('directors document: ' . json_encode($dirDoc->getFirstErrors())); }
    }
    /* People. */
    $pSec = $svc->getSectionByHandle('persons'); $pType = $svc->getEntryTypeByHandle('person');
    $newIds = [];
    foreach ($create as $k => $c) {
        $p = new Entry(); $p->sectionId = $pSec->id; $p->setTypeId($pType->id); $p->title = $c['title'];
        $p->setFieldValues(['fullName' => $c['title'], 'personAliases' => implode("\n", $c['aliases']),
            'recordProvenance' => 'import_water_boards.php, 30 September 2026: stood more than once for the council, a school board or a water board; public facts only']);
        if (!$elements->saveElement($p)) { throw new \RuntimeException("person {$c['title']}: " . json_encode($p->getFirstErrors())); }
        $newIds["new:$k"] = $p->id;
    }
    $pidOf = fn($p) => is_string($p) ? $newIds[$p] : $p;
    /* Elections and candidacies. */
    $elSec = $svc->getSectionByHandle('elections'); $elType = $svc->getEntryTypeByHandle('Election');
    $caSec = $svc->getSectionByHandle('candidacies'); $caType = $svc->getEntryTypeByHandle('Candidacy');
    foreach ($plan as $i => [$c, $slug, $have, $ev, $confirm, $margin, $close]) {
        $el = $have ?: new Entry();
        if (!$have) { $el->sectionId = $elSec->id; $el->setTypeId($elType->id); $el->slug = $slug; }
        $dist = $c['division'] ? [$divPlan[$c['body'] . '|' . $c['division']][1]] : [];
        $notes = ['Votes, registration and ballots cast: County of Los Angeles, statement of votes cast by precinct (' . $c['file'] . '), summed over its ' . $c['precincts'] . ' precincts' . ($c['votesFrom'] === 'returns' ? '; the candidates\' totals are the Official Election Returns\'' : '') . '.'];
        $notes[] = 'Seats: ' . $c['seats'] . ', from ' . ($c['seatsFrom'] === 'the spreadsheet, row 2 (unlabelled)' ? 'the statement\'s own figure for the contest (the returns for 2024 are not yet published); SCV Water\'s list of directors agrees' : ($c['seatsFrom'] === 'the spreadsheet, "VOTE FOR"' ? 'the statement ("VOTE FOR")' : 'the Official Election Returns, ' . $c['seatsFrom'] . ' ("VOTE FOR")')) . '.';
        if ($c['term'] === 'two-year') { $notes[] = 'A two-year term: the remainder of a term, filled on the same ballot as the division\'s full term.'; }
        if ($diffs = array_filter($c['candidates'], fn($x) => isset($x['spreadsheetVotes']))) { $notes[] = 'The County\'s public precinct statement differs from the certified returns by a vote or two: ' . implode('; ', array_map(fn($x) => $tc($x['name']) . ' ' . number_format($x['spreadsheetVotes']) . ' against ' . number_format($x['votes']), $diffs)) . '. The returns are used.'; }
        if ($c['sharedWithVentura']) { $notes[] = 'Part of this ' . ($c['division'] ? 'division' : 'agency') . ' votes in Ventura County: the returns mark the contest "(SHARED W/VENTURA CO)". Every figure here, registration, ballots and votes, is Los Angeles County\'s share; Ventura County\'s is not held.' . ($close ? ' The last winning margin here, ' . number_format($margin) . ' votes, is small enough that the Ventura share could have decided it.' : ''); }
        $notes[] = 'Winners: the first ' . $c['seats'] . ' of the count. ' . ($ev === 'roster' ? 'SCV Water\'s own list of its directors names ' . ($c['seats'] > 1 ? 'each of them' : 'the winner') . ' in this division with the term this election began (' . implode('; ', array_filter($confirm)) . '). ' : 'No declaring document is held, so the outcome is read from the count. ') . 'Upgraded to certified when the board\'s declaration is obtained.';
        $el->setFieldValues(['electionDate' => $printed($c['date']), 'electionDateEdtf' => $c['date'], 'electionKind' => 'general', 'electionBody' => [$bodyId[$c['body']]], 'electionDistrict' => $dist,
            'consolidatedWith' => 'County of Los Angeles general election', 'registeredVoters' => $c['registered'], 'ballotsCast' => $c['ballots'], 'seatsUp' => $c['seats'],
            'seatsUpEvidence' => $c['seatsFrom'] === 'the spreadsheet, row 2 (unlabelled)' ? 'roster' : 'certified', 'sourceDocuments' => array_merge([$docFor[$c['date']]], $ev === 'roster' ? [$dirDoc->id] : []),
            'footnotes' => $fn($notes), 'recordProvenance' => 'import_water_boards.php, 30 September 2026']);
        if (!$elements->saveElement($el)) { throw new \RuntimeException("election $slug: " . json_encode($el->getFirstErrors())); }
        foreach ($c['candidates'] as $r => $x) {
            $ca = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $el, 'field' => 'candidacyElection'])->nameAsPrinted($x['name'])->one() ?? new Entry();
            if (!$ca->id) { $ca->sectionId = $caSec->id; $ca->setTypeId($caType->id); }
            $pid = $personFor["$i|{$x['name']}"] ?? null;
            $ca->setFieldValues(['candidacyElection' => [$el->id], 'candidacyPerson' => $pid ? [$pidOf($pid)] : [], 'nameAsPrinted' => $x['name'], 'votesAsPrinted' => (string)$x['votes'], 'votes' => $x['votes'],
                'outcome' => $r < $c['seats'] ? 'elected' : 'not-elected', 'outcomeEvidence' => $ev, 'candidacyDistrict' => $dist, 'recordProvenance' => 'import_water_boards.php, 30 September 2026']);
            if (!$elements->saveElement($ca)) { throw new \RuntimeException("candidacy {$x['name']}: " . json_encode($ca->getFirstErrors())); }
        }
    }
    foreach ($crossLinks as $cid => $p) { $cd = $get($cid); $cd->setFieldValue('candidacyPerson', [$pidOf($p)]); if (!$elements->saveElement($cd)) { throw new \RuntimeException("cross link #$cid"); } }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }

$short = []; $nC = 0;
foreach ($plan as [$c, $slug]) {
    $el = Entry::find()->section('elections')->status(null)->slug($slug)->one();
    if (!$el) { $short[] = "no $slug"; continue; }
    $cs = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $el, 'field' => 'candidacyElection'])->all(); $nC += count($cs);
    if (array_sum(array_map(fn($x) => (int)$x->votes, $cs)) !== array_sum(array_column($c['candidates'], 'votes'))) { $short[] = "$slug: votes do not sum"; }
    if ((int)$el->registeredVoters !== $c['registered']) { $short[] = "$slug: registration"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: ' . count($plan) . " elections, $nC candidacies, votes and registration read back") . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('import_water_boards.php', count($plan) + $nC, $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'CLWA 2016 and SCV Water 2020-2024 from the County\'s returns');
if ($short) { throw new \RuntimeException('import_water_boards: ' . implode('; ', $short)); }
