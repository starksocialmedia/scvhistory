/**
 * The five school boards' elections, 1995 to 2024, from CEDA; and the district
 * records their seats are elected from, with SCV Water's three divisions
 * (Nathan, 29 September 2026: "build the body field on the election record and
 * the district records for trustee areas and water divisions, then start with
 * CEDA since it covers all five districts 1995 to 2024 with winners marked").
 *
 * Reads inventory/elections/ceda-scv.json (parse_ceda.py): 214 candidates in 62
 * contests. In every contest CEDA's winners are the top of the count; in the six
 * 1995 contests, whose file has no "vote for" column, the seats are the number
 * CEDA marks elected, and the note says so.
 *
 *   PLACES     25 trustee areas, five for each district, each evidenced by a
 *              CEDA contest or the County's lists of cancelled elections
 *              (Castaic's are lettered A to E); and SCV Water Divisions 1 to 3
 *              (the agency's own page: "nine directly elected directors, from
 *              three electoral divisions", read 29 September 2026). A district
 *              is a place record with districtKind and districtNumber, tied to
 *              its body by placeOrganizations.
 *   ELECTIONS  one per contest: body, date, trustee area or at large, full or
 *              short term. Seats are CEDA's "vote for". Evidence roster, cited
 *              as a compilation, never certified. No turnout: CEDA has none.
 *   CANDIDACIES the name as CEDA prints it, votes, elected or not, the area.
 *   PEOPLE     linked on the full first name and surname, as for the council.
 *              A person record for anyone who stood more than once across the
 *              council and the boards, public facts only. Groups that are
 *              council-only belong to council_ceda.php, which runs first.
 *
 * Not here: the seats filled without a vote (the cancelled contests, whose
 * appointees the County does not name), and office holdings, which need them.
 * SCV Water's elections are not in CEDA; they come from the County's returns.
 *
 * Needs add_election_body.php and council_ceda.php first. Idempotent (elections
 * by slug, candidacies by election and name, places by title). Dry run by
 * default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_ceda_school_boards.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements(); $fs = Craft::$app->getFields();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$refused = [];
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$ceda = json_decode(file_get_contents("$root/inventory/elections/ceda-scv.json"), true);
$cman = json_decode(file_get_contents("$root/inventory/elections/ceda-manifest.json"), true);
$county = json_decode(file_get_contents("$root/inventory/elections/county/manifest.json"), true);
$schemaReady = (bool)$fs->getFieldByHandle('electionBody');
$cedaDoc = Entry::find()->section('documents')->status(null)->sourcePath($cman['source'])->one();
$countyDoc = Entry::find()->section('documents')->status(null)->sourcePath($county['files']['cancelled-elections-november-2024.pdf']['url'])->one();
if (!$schemaReady || !$cedaDoc) { echo 'NEEDS add_election_body.php and council_ceda.php first (the plan below is still checked)' . PHP_EOL; }
$CEDA_CITE = fn(int $y) => "California Elections Data Archive (CEDA), Center for California Studies and Institute for Social Research, California State University, Sacramento, with the Secretary of State: the $y candidates file (CEDA{$y}Data.xls" . ($y >= 2011 ? 'x' : '') . '), a compilation of the counties\' returns, not a certified return';

/* ------------------------------------------------ the bodies */
$BODIES = [
    'william-s-hart-union-high-school-district' => ['hart-district', ['1', '2', '3', '4', '5']],
    'saugus-union-school-district' => ['saugus-union', ['1', '2', '3', '4', '5']],
    'newhall-school-district' => ['newhall-school-district', ['1', '2', '3', '4', '5']],
    'sulphur-springs-union-school-district' => ['sulphur-springs', ['1', '2', '3', '4', '5']],
    'castaic-union-school-district' => ['castaic-union', ['A', 'B', 'C', 'D', 'E']],
];
$org = [];
foreach (array_keys($BODIES) as $slug) { $o = Entry::find()->section('organizations')->slug($slug)->one(); if (!$o) { $refused[] = "no organization $slug"; } else { $org[$slug] = $o; } }
$water = Entry::find()->section('organizations')->id(402)->one();
if (!$water || $water->title !== 'Santa Clarita Valley Water') { $refused[] = '#402 is not Santa Clarita Valley Water'; }

/* Where each area is evidenced: CEDA contests, and the County's cancelled lists. */
$CANCELLED = [   /* read from inventory/elections/county, 29 September 2026 */
    '2020' => ['castaic-union-school-district' => ['A', 'C'], 'newhall-school-district' => ['4', '5'], 'sulphur-springs-union-school-district' => ['1', '2', '3']],
    '2022' => ['castaic-union-school-district' => ['B', 'D'], 'newhall-school-district' => ['3'], 'sulphur-springs-union-school-district' => ['3', '4', '5']],
    '2024' => ['castaic-union-school-district' => ['C', 'D'], 'newhall-school-district' => ['4'], 'saugus-union-school-district' => ['4', '1'], 'sulphur-springs-union-school-district' => ['1', '2']],
];
$school = array_values(array_filter($ceda['rows'], fn($r) => isset($BODIES[$r['body']])));
$seen = [];
foreach ($school as $r) { if ($r['area'] !== '') { $seen[$r['body']][$r['area']]['CEDA'][$r['year']] = true; } }
foreach ($CANCELLED as $y => $bs) { foreach ($bs as $b => $as) { foreach ($as as $a) { $seen[$b][$a]['cancelled'][$y] = true; } } }
echo 'DISTRICTS' . PHP_EOL;
$placePlan = [];
foreach ($BODIES as $slug => [$short, $areas]) {
    foreach ($areas as $a) {
        $ev = $seen[$slug][$a] ?? [];
        if (!$ev) { $refused[] = "$slug area $a is evidenced nowhere"; continue; }
        $t = ($org[$slug]->title ?? $slug) . ", Trustee Area $a";
        $why = trim((isset($ev['CEDA']) ? 'contested in ' . implode(', ', array_keys($ev['CEDA'])) . ' (CEDA)' : '') . (isset($ev['CEDA'], $ev['cancelled']) ? '; ' : '') . (isset($ev['cancelled']) ? 'uncontested in ' . implode(', ', array_keys($ev['cancelled'])) . ', filled by appointment in lieu of election (the County\'s lists of cancelled elections)' : ''));
        $have = Entry::find()->section('places')->status(null)->title($t)->one();
        echo '   ' . ($have ? "#{$have->id} " : 'create ') . str_pad($t, 64) . $why . PHP_EOL;
        $placePlan[$t] = [$slug, 'trustee-area', $a, $why, $have?->id];
    }
}
for ($n = 1; $n <= 3; $n++) {
    $t = "Santa Clarita Valley Water, Division $n";
    $have = Entry::find()->section('places')->status(null)->title($t)->one();
    echo '   ' . ($have ? "#{$have->id} " : 'create ') . $t . PHP_EOL;
    $placePlan[$t] = ['scv-water', 'water-division', (string)$n, 'one of the agency\'s three electoral divisions, each electing three of its nine directors (Santa Clarita Valley Water Agency, "Board of Directors", yourscvwater.com/governance/board-directors, read 29 September 2026)', $have?->id];
}

/* ------------------------------------------------ the contests */
$contests = [];
foreach ($school as $r) { $contests[implode('|', [$r['body'], $r['date'], $r['area'], strtolower($r['term'])])][] = $r; }
ksort($contests);
$printed = fn($d) => date('F j, Y', strtotime($d));
echo PHP_EOL . 'ELECTIONS (' . count($contests) . ')' . PHP_EOL;
$plan = [];
foreach ($contests as $key => $rs) {
    [$body, $date, $area, $term] = explode('|', $key);
    usort($rs, fn($a, $b) => ($b['votes'] ?? 0) <=> ($a['votes'] ?? 0));
    $nEl = count(array_filter($rs, fn($r) => $r['elected']));
    $seats = $rs[0]['seats'] ?? $nEl;
    if ($seats !== $nEl) { $refused[] = "$key: vote for $seats, $nEl elected"; }
    if (array_filter(array_slice($rs, 0, $nEl), fn($r) => !$r['elected'])) { $refused[] = "$key: CEDA's winners are not the top of the count"; }
    $slug = \craft\helpers\ElementHelper::normalizeSlug($BODIES[$body][0] . ' board election ' . $printed($date) . ($area !== '' ? ' trustee area ' . $area : '') . ($term === 'short' ? ' short term' : ''));
    $have = Entry::find()->section('elections')->status(null)->slug($slug)->one();
    $plan[$key] = [$body, $date, $area, $term, $rs, $seats, $slug, $have];
    echo '   ' . ($have ? "#{$have->id} " : 'create ') . str_pad($slug, 66) . str_pad(count($rs) . ' cand.', 9) . "$seats seat" . ($seats === 1 ? ' ' : 's') . ($rs[0]['seats'] === null ? ' (from CEDA\'s elected)' : '') . PHP_EOL;
}

/* ------------------------------------------------ people */
$norm = fn($s) => strtolower(trim(preg_replace('~[^A-Za-z -]~', '', \craft\helpers\StringHelper::toAscii((string)$s))));
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
$CONFIRMED = ['mike lyons' => 'michael lyons'];
$canon = fn(string $k) => $CONFIRMED[$k] ?? $k;
$pIndex = [];
foreach (Entry::find()->section('persons')->status(null)->all() as $p) {
    $names = [$p->title, (string)$p->fullName]; foreach (preg_split('~\n~', (string)$p->personAliases) as $a) { if (trim($a)) { $names[] = trim($a); } }
    foreach ($names as $n) { foreach ($keysOf($n) as $k) { $pIndex[$canon($k)][$p->id] = $p->title; } }
}
/* Every name that has stood: the boards' from CEDA, the council's as printed, 2014's from CEDA. */
$stood = [];   /* [name, 'school'|'council', ref] */
foreach ($plan as $key => [, , , , $rs]) { foreach ($rs as $r) { $stood[] = [trim($r['first'] . ' ' . $r['last']), 'school', "$key|" . $r['last'] . '|' . $r['first']]; } }
$c14 = array_values(array_filter($ceda['rows'], fn($r) => $r['body'] === 'city-of-santa-clarita' && $r['year'] === 2014));
foreach (Entry::find()->section('candidacies')->status(null)->all() as $c) {
    $e = $c->candidacyElection->status(null)->one(); if (!$e) { continue; }
    if ($schemaReady && $e->electionBody->ids() && !in_array(394, $e->electionBody->ids())) { continue; }   /* a board candidacy from an earlier run */
    $n = (string)$c->nameAsPrinted;
    if ($e->electionDateEdtf === '2014-04-08') { $r = array_values(array_filter($c14, fn($r) => (int)$r['votes'] === (int)$c->votes)); $n = $r ? trim($r[0]['first'] . ' ' . $r[0]['last']) : $n; }
    $stood[] = [$n, 'council', $c->id, (bool)$c->candidacyPerson->status(null)->ids()];
}
$groups = []; $keyGroup = [];
foreach ($stood as $i => $s) {
    $ks = array_map($canon, $keysOf($s[0])); if (!$ks) { continue; }
    $into = null; foreach ($ks as $k) { if (isset($keyGroup[$k])) { $into = $keyGroup[$k]; break; } }
    $into ??= $ks[0]; $groups[$into][] = $i; foreach ($ks as $k) { $keyGroup[$k] ??= $into; }
}
$NICK = ['ken' => 'kenneth', 'mike' => 'michael', 'bob' => 'robert', 'ed' => 'edmund', 'bill' => 'william', 'jim' => 'james', 'tim' => 'timothy', 'dan' => 'daniel', 'chuck' => 'charles', 'jeff' => 'jeffrey', 'doug' => 'douglas', 'tom' => 'thomas', 'rick' => 'richard', 'steve' => 'steven', 'joe' => 'joseph', 'pat' => 'patricia', 'sue' => 'susan', 'kathy' => 'katherine'];
$held = []; $heldKeys = [];
foreach ($groups as $k => $is) { [$f, $l] = explode(' ', $k, 2); if (isset($NICK[$f], $groups[$NICK[$f] . ' ' . $l]) && array_filter(array_merge($is, $groups[$NICK[$f] . ' ' . $l]), fn($i) => $stood[$i][1] === 'school')) { $held[] = ucwords($k) . ' and ' . ucwords($NICK[$f] . ' ' . $l); $heldKeys[$k] = true; } }
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
/* A suffix on some printings and not others may be a father and a son: held,
   never joined (the Smyths were joined once on a weaker rule). */
foreach ($groups as $k => $is) { if (!array_filter($is, fn($i) => $stood[$i][1] === 'school')) { continue; } $sx = array_unique(array_map(fn($i) => $suffixOf($stood[$i][0]), $is)); if (count($sx) > 1) { $held[] = implode(' / ', array_unique(array_map(fn($i) => $stood[$i][0], $is))) . ' (a suffix on some printings only)'; $heldKeys[$k] = true; } }
$personFor = []; $create = []; $crossLinks = []; $pending = [];
foreach ($groups as $k => $is) {
    $hasSchool = (bool)array_filter($is, fn($i) => $stood[$i][1] === 'school');
    if (!$hasSchool || isset($heldKeys[$k])) { continue; }
    $ex = $pIndex[$k] ?? [];
    if (count($ex) > 1) { $refused[] = "$k matches " . count($ex) . ' people'; continue; }
    if ($ex) { $pid = array_key_first($ex); }
    elseif (count(array_filter($is, fn($i) => $stood[$i][1] === 'council')) >= 2) {
        if ($cedaDoc) { $refused[] = "$k: stood for the council more than once and has no record"; continue; }
        $pending[$k] = true; $pid = "pending:$k";   /* council_ceda.php creates it; at apply time it exists */
    }
    elseif (count($is) >= 2) {
        $printings = array_values(array_unique(array_map(fn($i) => $stood[$i][0], $is)));
        $title = $titleOf($printings);
        $pid = "new:$k"; $create[$k] = ['title' => $title, 'aliases' => array_values(array_diff($printings, [$title])), 'is' => $is];
    } else { continue; }
    foreach ($is as $i) {
        if ($stood[$i][1] === 'school') { $personFor[$stood[$i][2]] = $pid; }
        elseif (empty($stood[$i][3]) && !isset($pending[$k])) { $crossLinks[$stood[$i][2]] = $pid; }
    }
}
$label = fn($pid) => is_string($pid) ? (str_starts_with($pid, 'pending:') ? ucwords(substr($pid, 8)) . ' (from council_ceda.php)' : $create[substr($pid, 4)]['title'] . ' (new)') : "#$pid " . $get($pid)->title;
echo PHP_EOL . 'PEOPLE: ' . count($create) . ' to create, stood more than once' . PHP_EOL;
foreach ($create as $k => $c) {
    $where = array_map(fn($i) => $stood[$i][1] === 'council' ? 'council' : explode('|', $stood[$i][2])[0], $c['is']);
    $years = array_map(fn($i) => $stood[$i][1] === 'council' ? substr($get($stood[$i][2])->candidacyElection->status(null)->one()->electionDateEdtf, 0, 4) : substr(explode('|', $stood[$i][2])[1], 0, 4), $c['is']);
    echo '   ' . str_pad($c['title'], 24) . implode(', ', array_map(fn($w, $y) => "$y " . str_replace(['-union-school-district', '-school-district', 'william-s-', '-union-high'], '', $w), $where, $years)) . ($c['aliases'] ? '; also printed ' . implode('; ', array_map(fn($a) => "\"$a\"", array_filter($c['aliases'], fn($a) => str_contains($a, ' ')))) : '') . PHP_EOL;
}
$toExisting = array_filter($personFor, fn($p) => is_int($p) || str_starts_with((string)$p, 'pending:'));
echo 'Board candidacies linked to people already in the archive (' . count($toExisting) . '): ' . implode('; ', array_unique(array_map(fn($p) => $label($p), $toExisting))) . PHP_EOL;
echo 'Council candidacies linked across to a board candidate (' . count($crossLinks) . '): ' . implode('; ', array_map(fn($cid, $p) => $get($cid)->nameAsPrinted . ' -> ' . $label($p), array_keys($crossLinks), $crossLinks)) . PHP_EOL;
echo 'HELD, one person under two first names?: ' . ($held ? implode('; ', $held) : 'none') . PHP_EOL;
echo 'Board candidacies with no person record (stood once): ' . (count($school) - count($personFor)) . PHP_EOL;

echo PHP_EOL . 'SUMMARY: ' . count(array_filter($placePlan, fn($p) => !$p[4])) . ' places, ' . count(array_filter($plan, fn($p) => !$p[7])) . ' elections, ' . count($school) . ' candidacies, ' . count($create) . ' people.' . PHP_EOL;
echo 'REFUSED: ' . ($refused ? implode(' | ', $refused) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($refused) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }
if (!$schemaReady || !$cedaDoc) { echo 'REFUSING: run add_election_body.php and council_ceda.php first' . PHP_EOL; return; }

/* ------------------------------------------------ apply, in one transaction */
$tx = Craft::$app->getDb()->beginTransaction();
try {
$placeSec = $svc->getSectionByHandle('places'); $placeType = $svc->getEntryTypeByHandle('place');
$placeId = [];
foreach ($placePlan as $t => [$slug, $kind, $num, $why, $have]) {
    if (!$have) {
        $body = $slug === 'scv-water' ? $water : $org[$slug];
        $p = new Entry(); $p->sectionId = $placeSec->id; $p->setTypeId($placeType->id); $p->title = $t;
        $p->setFieldValues(['districtKind' => $kind, 'districtNumber' => $num, 'placeOrganizations' => [$body->id],
            'body' => ($kind === 'water-division' ? "Division $num of the Santa Clarita Valley Water Agency: $why." : "Trustee Area $num of the {$body->title}, one of the five areas its board members have been elected from since the district left at-large elections. It was $why."),
            'recordProvenance' => 'import_ceda_school_boards.php, 29 September 2026']);
        if (!$elements->saveElement($p)) { throw new \RuntimeException("$t: " . json_encode($p->getFirstErrors())); }
        $have = $p->id;
    }
    $placeId[$slug . '|' . $num] = $have;
}
$pSec = $svc->getSectionByHandle('persons'); $pType = $svc->getEntryTypeByHandle('person');
$newIds = [];
foreach ($create as $k => $c) {
    $p = new Entry(); $p->sectionId = $pSec->id; $p->setTypeId($pType->id); $p->title = $c['title'];
    $p->setFieldValues(['fullName' => $c['title'], 'personAliases' => implode("\n", array_filter($c['aliases'], fn($a) => str_contains($a, ' '))),
        'recordProvenance' => 'import_ceda_school_boards.php, 29 September 2026: stood more than once for a school board or the council; public facts only']);
    if (!$elements->saveElement($p)) { throw new \RuntimeException("person {$c['title']}: " . json_encode($p->getFirstErrors())); }
    $newIds["new:$k"] = $p->id;
}
$pidOf = fn($p) => is_string($p) ? $newIds[$p] : $p;
$elSec = $svc->getSectionByHandle('elections'); $elType = $svc->getEntryTypeByHandle('Election');
$caSec = $svc->getSectionByHandle('candidacies'); $caType = $svc->getEntryTypeByHandle('Candidacy');
foreach ($plan as $key => [$body, $date, $area, $term, $rs, $seats, $slug, $have]) {
    $y = (int)substr($date, 0, 4);
    $el = $have ?: new Entry();
    if (!$have) { $el->sectionId = $elSec->id; $el->setTypeId($elType->id); $el->slug = $slug; }
    $notes = ["Candidates, votes and winners: CEDA, rows " . implode(', ', array_map(fn($r) => $r['row'], $rs)) . '. ' . $CEDA_CITE($y) . '. No declaring document is held; the winners are marked on CEDA\'s record, which agrees with the count, and are upgraded to certified when the board\'s declaration is obtained.'];
    if ($rs[0]['seats'] === null) { $notes[] = "Seats: $seats. The 1995 file has no \"vote for\" column; the number is the count CEDA marks elected."; }
    if ($term === 'short') { $notes[] = 'A short term: the remainder of a term left vacant, filled on the same ballot as the full terms.'; }
    $el->setFieldValues(['electionDate' => $printed($date), 'electionDateEdtf' => $date, 'electionKind' => 'general', 'electionBody' => [$org[$body]->id],
        'electionDistrict' => $area !== '' ? [$placeId[$body . '|' . $area]] : [], 'seatsUp' => $seats, 'seatsUpEvidence' => 'roster',
        'sourceDocuments' => [$cedaDoc->id], 'footnotes' => $fn($notes), 'recordProvenance' => 'import_ceda_school_boards.php, 29 September 2026']);
    if (!$elements->saveElement($el)) { throw new \RuntimeException("election $slug: " . json_encode($el->getFirstErrors())); }
    foreach ($rs as $r) {
        $name = trim($r['first'] . ' ' . $r['last']);
        $ca = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $el, 'field' => 'candidacyElection'])->nameAsPrinted($name)->one() ?? new Entry();
        if (!$ca->id) { $ca->sectionId = $caSec->id; $ca->setTypeId($caType->id); }
        $pid = $personFor["$key|{$r['last']}|{$r['first']}"] ?? null;
        $ca->setFieldValues(['candidacyElection' => [$el->id], 'candidacyPerson' => $pid ? [$pidOf($pid)] : [], 'nameAsPrinted' => $name, 'votesAsPrinted' => (string)$r['votes'], 'votes' => $r['votes'],
            'outcome' => $r['elected'] ? 'elected' : 'not-elected', 'outcomeEvidence' => 'roster', 'candidacyDistrict' => $area !== '' ? [$placeId[$body . '|' . $area]] : [],
            'footnotes' => $fn(['CEDA ' . $y . ', row ' . $r['row'] . ($r['designation'] ? '; ballot designation "' . $r['designation'] . '"' : '') . ($r['incumbent'] ? '; the incumbent' : '') . '.']),
            'recordProvenance' => 'import_ceda_school_boards.php, 29 September 2026']);
        if (!$elements->saveElement($ca)) { throw new \RuntimeException("candidacy $name: " . json_encode($ca->getFirstErrors())); }
    }
}
foreach ($crossLinks as $cid => $p) { $c = $get($cid); $c->setFieldValue('candidacyPerson', [$pidOf($p)]); if (!$elements->saveElement($c)) { throw new \RuntimeException("cross link #$cid"); } }
$tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }

/* Read back. */
$short = [];
$nE = 0; $nC = 0;
foreach ($plan as [$body, , , , $rs, , $slug]) {
    $el = Entry::find()->section('elections')->status(null)->slug($slug)->one();
    if (!$el) { $short[] = "no $slug"; continue; } $nE++;
    $cs = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $el, 'field' => 'candidacyElection'])->all();
    $nC += count($cs);
    if (count($cs) !== count($rs)) { $short[] = "$slug: " . count($cs) . ' of ' . count($rs) . ' candidacies'; }
    if (array_sum(array_map(fn($c) => (int)$c->votes, $cs)) !== array_sum(array_map(fn($r) => (int)$r['votes'], $rs))) { $short[] = "$slug: votes do not sum"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : "OK: $nE elections, $nC candidacies, votes summed per contest") . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('import_ceda_school_boards.php', $nE + $nC + count($placePlan) + count($create), $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'five school boards 1995-2024 from CEDA; 25 trustee areas; SCV Water divisions');
if ($short) { throw new \RuntimeException('import_ceda_school_boards: ' . implode('; ', $short)); }
