/**
 * The City's three standing commissions and the commissioners with a place in the archive, from the
 * City's own commission pages (Nathan, 4 October 2026: "Create the three commissions as organizations
 * under the City, alongside the Planning Commission which already exists. Record the members with their
 * terms, as affiliations ... On whether each commissioner gets a person record: you decide case by case
 * against the significance rule"; Patti Rasmussen: "Lead with the valley ... Living person: public life
 * only").
 *
 * The pages, read 4 October 2026, saved in inventory/news/city-commissions-2026-10-04/ (html, text). Only
 * what a reader sees is taken: the Planning Commission page also carries Patsy Ayala and Denise Lite as
 * vice-chairpersons in blocks hidden from every device, which are left out.
 *
 *  1. Organizations: the Parks, Recreation and Community Services Commission and the Arts Commission,
 *     under the City (#394), beside the Planning Commission (#15939).
 *  2. Each commission's members, offices and terms as the City lists them, dated, as a note on its
 *     record; members without a record are named there and nowhere else.
 *  3. Person records for five, each with a public-life profile from the City's biography (no family):
 *       Patti Rasmussen #2591 (exists): The Signal's education reporter, the Historical Society's board.
 *       Susan Shapiro: managed SCVTV from 1993 to 2007 and built its media centre in Newhall.
 *       Tim Burkhart: more than twenty years on the Planning Commission; a council candidate in 2024
 *         (his candidacy #22406 linked to the new record).
 *       Lisa Eichman: on the Planning Commission since October 2010.
 *       Jeri Seratti: KHTS, the valley's radio station, which the archive cites throughout; she directed
 *         its emergency broadcast after the Northridge earthquake.
 *     Not given records, for Nathan to decide: Nathan Keith, Di Thompson, Michael Millar. The rest are
 *     named on their commission's record only.
 *  4. Affiliations: each of the five's commission seat, with office and term; Rasmussen's posts at The
 *     Signal (#376) and on the Historical Society's board (#15493). Rasmussen and Seratti linked to the
 *     Northridge Earthquake (#875).
 * The Historical Society years Nathan gave (2003 to 2016) are not on the City's page and are not recorded.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/record_city_commissions_2026_10_04.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $svc = Craft::$app->getEntries(); $el = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one(); $person = fn($t) => Entry::find()->section('persons')->status(null)->title($t)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$bad = []; $D = "$root/inventory/news/city-commissions-2026-10-04";
$PG = ['planning' => ['planning-commission', 'Planning Commission'], 'parks' => ['parks-recreation-and-community-services', 'Parks, Recreation and Community Services'], 'arts' => ['arts-commission', 'Arts Commission']];
$T = []; foreach ($PG as $k => [$slug, $t]) { if (!is_file("$D/$slug.txt")) { $bad[] = "$slug missing"; continue; } $T[$k] = preg_replace('~\s+~u', ' ', file_get_contents("$D/$slug.txt")); }
$cite = function (string $k, string $quote) use (&$bad, $T, $PG): string {
    if (!str_contains($T[$k] ?? '', $quote)) { $bad[] = "$k does not read: $quote"; }
    return 'City of Santa Clarita, "' . $PG[$k][1] . '," https://santaclarita.gov/commission-information/' . $PG[$k][0] . '/, read 4 October 2026: "' . $quote . '"';
};
$src = fn($k) => 'City of Santa Clarita, "' . $PG[$k][1] . '," https://santaclarita.gov/commission-information/' . $PG[$k][0] . '/, read 4 October 2026';

/* Rosters as the City lists them: [name, office, term expires] */
$R = [
    'planning' => [['Nathan Keith', 'Chairperson', 'December 2028'], ['Daniel Faina', 'Vice-Chairperson', 'December 2026'], ['Tim Burkhart', 'Commissioner', 'December 2028'], ['Pamela Verner', 'Commissioner', 'December 2026'], ['Lisa Eichman', 'Commissioner', 'December 2026']],
    'parks' => [['Di Thompson', 'Chair', 'December 2026'], ['Hugo Cherre', 'Vice Chair', 'December 2028'], ['Skye Ostrom', 'Commissioner', 'December 2026'], ['Peggy Stabile', 'Commissioner', 'December 2026'], ['Dennis Sugasawara', 'Commissioner', 'December 2028']],
    'arts' => [['Susan Shapiro', 'Chair', 'December 2026'], ['Tracey Thompson', 'Vice-Chair', '2028'], ['Dr. Michael Millar', 'Commissioner', 'December 2026'], ['Patti Rasmussen', 'Commissioner', 'December 2026'], ['Jeri Seratti', 'Commissioner', '2028']],
];
foreach ($R as $k => $rows) { foreach ($rows as [$n, $o, $t]) { $cite($k, "$n, $o Term Expires: $t"); } }
$ORG = ['planning' => $get(15939), 'parks' => Entry::find()->section('organizations')->status(null)->title('Parks, Recreation and Community Services Commission')->one(), 'arts' => Entry::find()->section('organizations')->status(null)->title('Arts Commission')->one()];
if ($ORG['planning']?->title !== 'Planning Commission') { $bad[] = '#15939 is not the Planning Commission'; }
foreach (['parks', 'arts'] as $k) { echo ($ORG[$k] ? "#{$ORG[$k]->id} {$ORG[$k]->title} exists" : 'create ' . ($k === 'parks' ? 'Parks, Recreation and Community Services Commission' : 'Arts Commission') . ', under the City') . PHP_EOL; }
$MEET = ['planning' => 'first and third Tuesday', 'parks' => 'first Thursday', 'arts' => 'second Thursday'];
$rosterNote = fn($k) => 'Members as the City lists them on 4 October 2026: ' . implode('; ', array_map(fn($r) => $r[0] . ', ' . strtolower($r[1]) . ', term to ' . $r[2], $R[$k])) . '. (' . $src($k) . '.)';

/* Profiles. */
$P = [];
$P['Patti Rasmussen'] = ['id' => 2591, 'org' => 'arts', 'office' => 'Commissioner', 'term' => 'December 2026', 'aliases' => '', 'occ' => 'Journalist',
    'body' => '<p>Patti Rasmussen, a freelance journalist who has lived in Newhall since the mid-1970s, was the education reporter for The Signal.[4] She wrote "Open Book," a column on the valley\'s schools: the archive holds 24 of her pieces, from May to November 1997, as they were carried on the Old Town Newhall site, on school boards, graduation, adult education, the PTA and the theatre programs of the valley\'s high schools.[1][2]</p><p>She was president of the Peachland PTA and chairman of the Newhall Site Council, and co-founded and led the Theatre Arts for Children Foundation, which raised money to restore the auditorium of the historic Newhall Elementary School; in May 1996 Leon Worden called her a fellow Signal columnist and the foundation\'s head.[4][3] After the Northridge earthquake of 1994, the City says, her backyard served as a theatre while Hart High School repaired its auditorium.[4]</p><p>She served on the board of the Santa Clarita Valley Historical Society as its educational outreach chair, was an original member of the Arts Alliance, and has been president of the boards of the Santa Clarita Shakespeare Festival and the SCV Senior Foundation. She sits on the City\'s Arts Commission, her term running to December 2026.[4]</p>',
    'notes' => null, 'cite' => [$cite('arts', 'Patti Rasmussen is a freelance journalist who has resided in Newhall since the mid-70s.'), $cite('arts', 'Patti worked as the Education Reporter for The Signal newspaper and has served on various school committees as president of the Peachland PTA and chairman of the Newhall Site Council.'), $cite('arts', 'She was the co-founder/president of Theatre Arts for Children Foundation that raised funds for the restoration of the historic Newhall Elementary School auditorium.'), $cite('arts', 'She also served on the board of the SCV Historical Society as the Educational Outreach Chair and was an original member of the Arts Alliance.'), $cite('arts', 'The Rasmussen backyard was also used as theater space after the 1994 Northridge earthquake while Hart High repaired its auditorium.'), $cite('arts', 'Most recently Patti served as President of the Board of Directors of the Santa Clarita Shakespeare Festival and was the President of the SCV Senior Foundation.')]];
$P['Susan Shapiro'] = ['id' => null, 'org' => 'arts', 'office' => 'Chair', 'term' => 'December 2026', 'aliases' => '', 'occ' => 'Television producer',
    'body' => '<p>Susan Shapiro came to Santa Clarita in 1993 to manage the local public television channel, SCVTV, and was the cable company\'s community television manager here until 2007. She designed and equipped the SCVTV Media Center in Newhall, and worked with the video production instructors of the Hart district and College of the Canyons to make the channel a classroom for their students.[1]</p><p>She sat on the City\'s Newhall Redevelopment Committee from 2002 to 2009 and its Citizens Public Library Advisory Committee in 2010 and 2011, and on the Hart district\'s ROP Advisory Committee from 2000. She chairs the City\'s Arts Commission, her term running to December 2026.[1]</p>',
    'cite' => [$cite('arts', 'Susan came to Santa Clarita in 1993 to manage the local public television channel, SCVTV. Susan designed and equipped the SCVTV Media Center in Newhall') . '; "As the cable company\'s Community Television Manager in Santa Clarita from 1993 to 2007"; "Newhall Redevelopment Committee (2002-09), City of Santa Clarita Citizens Public Library Advisory Committee (2010-11) and William S. Hart Union High School District ROP Advisory Committee (2000-present)."']];
$cite('arts', "As the cable company’s Community Television Manager in Santa Clarita from 1993 to 2007"); $cite('arts', 'Newhall Redevelopment Committee (2002-09), City of Santa Clarita Citizens Public Library Advisory Committee (2010-11) and William S. Hart Union High School District ROP Advisory Committee (2000-present)');
$P['Tim Burkhart'] = ['id' => null, 'org' => 'planning', 'office' => 'Commissioner', 'term' => 'December 2028', 'aliases' => '', 'occ' => 'Planning commissioner',
    'body' => '<p>Tim Burkhart, a graduate of William S. Hart High School who attended College of the Canyons and took bachelor\'s and master\'s degrees at California State University, Northridge, has served on the City\'s Planning Commission for more than twenty years; his present term runs to December 2028.[1] He retired as corporate vice president of maintenance and construction for Six Flags.[1] In November 2024 he stood for the City Council in District 1 and came third of three, with 4,108 votes.[2]</p>',
    'cite' => [$cite('planning', 'Tim graduated from William S. Hart High School, attended College of the Canyons, and earned both Bachelor’s and Master’s degrees from California State University, Northridge. He has dedicated over 20 years to ser') . ' The page lists him as a commissioner; its biography of him calls him the commission\'s chairperson.', 'Archive records: "City Council election, District 1, November 5, 2024," and his candidacy in it.']];
$cite('planning', 'As the retired Corporate Vice President of Maintenance and Construction for Six Flags');
$P['Lisa Eichman'] = ['id' => null, 'org' => 'planning', 'office' => 'Commissioner', 'term' => 'December 2026', 'aliases' => '', 'occ' => 'Planning commissioner',
    'body' => '<p>Lisa Eichman has been a member of the City\'s Planning Commission since October 2010; her present term runs to December 2026. She has lived in Valencia since 1986, and is a founding partner of Eichman &amp; Eichman, Tax and Accounting, and an owner of Gymnastics Unlimited.[1]</p>',
    'cite' => [$cite('planning', 'Lisa Eichman has been a dedicated member of the Planning Commission since October 2010') . '; "A resident of Valencia since 1986"; "She is a co-owner and founding partner of Eichman & Eichman, Tax and Accounting, and also owns Gymnastics Unlimited."']];
$cite('planning', 'A resident of Valencia since 1986'); $cite('planning', 'She is a co-owner and founding partner of Eichman & Eichman, Tax and Accounting, and also owns Gymnastics Unlimited');
$P['Jeri Seratti'] = ['id' => null, 'org' => 'arts', 'office' => 'Commissioner', 'term' => '2028', 'aliases' => 'Jeri Seratti Goldman', 'occ' => 'Radio station owner and manager',
    'body' => '<p>Jeri Seratti, also known as Jeri Seratti Goldman, has co-owned and run the valley\'s radio station since 2003, when AM 1220 was bought back from Clear Channel and put on the air as KHTS, "Santa Clarita\'s Hometown Station"; she had managed the same station, then KBET, from 1995 until it was sold in 1998.[1] After the Northridge earthquake of 1994 the station was the valley\'s line of information through the months of recovery, and she directed its full-time emergency broadcast.[1] She sits on the City\'s Arts Commission, her term running to 2028.[1]</p>',
    'cite' => [$cite('arts', 'In 1995, Jeri left Westwood One to take over the daily management at KBET') . '; "She remained KBET’s General Manager until the station was sold in 1998 to Clear Channel Communications"; "In 2003, the Goldman’s re-purchased AM-1220 from Clear Channel, turning on KHTS AM-1220, as Santa Clarita’s “Hometown Station” on October 24, 2003"; "In 1994, the radio station served as the information conduit during the Northridge Earthquake and the many months of recovery. Jeri guided the full-time emergency broadcast."']];
$cite('arts', 'She remained KBET’s General Manager until the station was sold in 1998 to Clear Channel Communications'); $cite('arts', 'In 1994, the radio station served as the information conduit during the Northridge Earthquake and the many months of recovery. Jeri guided the full-time emergency broadcast.');
foreach ($P as $n => $x) { $p = $x['id'] ? $get($x['id']) : $person($n); echo "$n: " . ($p ? "#{$p->id} exists" : 'create') . ', profile ' . str_word_count(strip_tags($x['body'])) . ' words, ' . $x['office'] . ' of the ' . ($x['org'] === 'planning' ? 'Planning' : ($x['org'] === 'parks' ? 'Parks' : 'Arts')) . ' Commission to ' . $x['term'] . PHP_EOL; }
$R2591 = $get(2591); if ($R2591?->title !== 'Patti Rasmussen' || count($R2591->footnotes ?? []) !== 3) { $bad[] = '#2591 is not as read'; }
$SIG = $get(376); $SHS = $get(15493); $NQ = $get(875); $BC = $get(22406);
if ($SIG?->title !== 'The Santa Clarita Valley Signal' || $SHS?->title !== 'Santa Clarita Valley Historical Society' || $NQ?->title !== 'Northridge Earthquake' || !str_contains((string)$BC?->title, 'Tim Burkhart')) { $bad[] = 'The Signal, the Society, the earthquake or Burkhart\'s candidacy is not where expected'; }
echo 'Rasmussen: The Signal (education reporter), the Historical Society (board, educational outreach chair), the Northridge Earthquake; Seratti: the Northridge Earthquake; Burkhart\'s candidacy #22406 linked' . PHP_EOL;
echo 'NOT GIVEN RECORDS, for Nathan: Nathan Keith, Di Thompson, Michael Millar' . PHP_EOL;
foreach ($P as $x) { if (preg_match('~\x{2014}|husband|wife|son|daughter|children~u', strip_tags($x['body']))) { $bad[] = 'family or an em dash in a profile'; } }
echo 'REFUSED: ' . ($bad ? implode(' | ', array_unique($bad)) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }

$PROV = 'record_city_commissions_2026_10_04.php, 4 October 2026: from the City\'s commission pages';
$n = 0; $tx = Craft::$app->getDb()->beginTransaction();
try {
    $os = $svc->getSectionByHandle('organizations');
    foreach (['parks' => 'Parks, Recreation and Community Services Commission', 'arts' => 'Arts Commission'] as $k => $title) {
        if ($ORG[$k]) { continue; }
        $o = new Entry(); $o->sectionId = $os->id; $o->setTypeId($os->getEntryTypes()[0]->id); $o->title = $title;
        $o->setFieldValues(['parentOrganization' => [394], 'orgType' => 'government', 'recordProvenance' => $PROV]);
        if (!$el->saveElement($o)) { throw new \RuntimeException("$title: " . json_encode($o->getFirstErrors())); } $ORG[$k] = $o; $n++;
    }
    foreach ($ORG as $k => $o) {
        $rows = array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], array_filter($o->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '' && ($r['heading'] ?? '') !== 'Members')));
        $rows[] = ['heading' => 'Members', 'position' => 'top', 'note' => $rosterNote($k) . ' It meets on the ' . $MEET[$k] . ' of the month.'];
        $o->setFieldValue('editorNotes', $rows); if (!$el->saveElement($o)) { throw new \RuntimeException($o->title . ': ' . json_encode($o->getFirstErrors())); } $n++;
    }
    $pSec = $svc->getSectionByHandle('persons'); $pType = $svc->getEntryTypeByHandle('person');
    $aSec = $svc->getSectionByHandle('affiliations'); $aType = $svc->getEntryTypeByHandle('affiliation');
    $mkA = function ($pid, $bid, $kind, $title, $ended, $notes, $te = null) use ($aSec, $aType, $el, $PROV, $fn, &$n) {
        if (Entry::find()->section('affiliations')->status(null)->relatedTo(['and', ['targetElement' => $pid, 'field' => 'affiliationPerson'], ['targetElement' => $bid, 'field' => 'affiliationBody']])->affiliationTitle($title)->exists()) { return; }
        $a = new Entry(); $a->sectionId = $aSec->id; $a->setTypeId($aType->id);
        $v = ['affiliationPerson' => [$pid], 'affiliationBody' => [$bid], 'affiliationKind' => $kind, 'affiliationTitle' => $title, 'affiliationEnded' => $ended, 'footnotes' => $fn($notes), 'recordProvenance' => $PROV];
        if ($te) { $v['termEnd'] = $te; }
        $a->setFieldValues($v); if (!$el->saveElement($a)) { throw new \RuntimeException('affiliation: ' . json_encode($a->getFirstErrors())); } $n++;
    };
    foreach ($P as $name => $x) {
        $p = $x['id'] ? $get($x['id']) : $person($name);
        if (!$p) { $p = new Entry(); $p->sectionId = $pSec->id; $p->setTypeId($pType->id); $p->setFieldValues(['fullName' => $name, 'recordProvenance' => $PROV]); if (!$el->saveElement($p)) { throw new \RuntimeException("$name: " . json_encode($p->getFirstErrors())); } $n++; }
        $notes = $x['id'] === 2591 ? array_merge(array_map(fn($r) => (string)$r['note'], $p->footnotes ?? []), [implode(' ', $x['cite'])]) : $x['cite'];
        $v = ['body' => $x['body'], 'footnotes' => $fn($notes), 'bodyAuthorship' => 'editorial-2026', 'occupation' => $x['occ']];
        if ($x['aliases']) { $v['personAliases'] = $x['aliases']; }
        if ($name === 'Patti Rasmussen' || $name === 'Jeri Seratti') { $v['personEvents'] = array_values(array_unique(array_merge($p->personEvents->ids(), [875]))); }
        if ($x['id'] === 2591 && str_contains((string)$p->body, 'Leon Worden called her a fellow Signal columnist and the foundation')) { unset($v['body'], $v['footnotes']); }
        $p->setFieldValues($v); if (!$el->saveElement($p)) { throw new \RuntimeException("$name: " . json_encode($p->getFirstErrors())); } $n++;
        $mkA($p->id, $ORG[$x['org']]->id, 'member', $x['office'], 'serving', [$src($x['org']) . ': "' . $name . ', ' . $x['office'] . ' Term Expires: ' . $x['term'] . '."'], $x['term']);
        if ($name === 'Patti Rasmussen') {
            $mkA($p->id, 376, 'employed', 'Education Reporter', 'unknown', [$x['cite'][1] . ' The page gives no years.']);
            $mkA($p->id, 15493, 'nonprofit-board', 'Board member, Educational Outreach Chair', 'unknown', [$x['cite'][3] . ' The page gives no years.']);
        }
        if ($name === 'Tim Burkhart' && !$BC->candidacyPerson->exists()) { $BC->setFieldValue('candidacyPerson', [$p->id]); if (!$el->saveElement($BC)) { throw new \RuntimeException('candidacy: ' . json_encode($BC->getFirstErrors())); } $n++; }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$ok = $person('Jeri Seratti') && $person('Susan Shapiro') && str_contains((string)$get(2591)->body, 'Historical Society') && Entry::find()->section('organizations')->status(null)->title('Arts Commission')->exists();
echo 'READ-BACK ' . ($ok ? "OK: $n writes" : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('record_city_commissions_2026_10_04.php', $n, $ok ? 'verified' : 'SHORT', 'Parks and Arts commissions created; rosters of three commissions dated; Rasmussen, Shapiro, Burkhart, Eichman, Seratti profiles and seats');
if (!$ok) { throw new \RuntimeException('record_city_commissions: read-back short'); }
