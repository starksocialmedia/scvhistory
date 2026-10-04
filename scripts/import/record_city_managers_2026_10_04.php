/**
 * The City's city managers (inventory/review/city-managers-2026-10-04.md; Nathan, 4 October 2026: "yes to records for Pulskamp
 * and Striplin, Bien named in the text. Striplin to the sidebar, the history under History").
 *  - Ken Pulskamp and Ken Striplin: person records, public life only (no birth dates, though the legacy pages print them).
 *  - Affiliations with the City, title "City Manager": George Caravalho (#16396), May 1988 to May 2002, left for Riverside;
 *    Pulskamp, May 2002 (interim, permanent later that year) to 30 November 2012, retired; Striplin, 1 December 2012, serving.
 *  - Fred Bien, the interim manager of 1987 and 1988, named in the City's text with his sources; no record (nothing is known
 *    of him beyond the post).
 * The page shows the present manager in the sidebar and the past ones under History (organizations/_entry.twig, EXEC).
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/record_city_managers_2026_10_04.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $svc = Craft::$app->getEntries();
$S = [
  'sc9020' => 'City of Santa Clarita biography of George Caravalho, as carried on SCVHistory.com, photograph SC9020, /scvhistory/sc9020.htm: "City Manager, City of Santa Clarita May 1988 to May 2002; City Manager, City of Riverside 2002 to 2005"; he followed the interim manager, Fred Bien.',
  'sig2020' => 'The Signal, January 2020, "George Caravalho, Santa Clarita\'s first city manager, dead at 81," read in the Wayback Machine\'s capture, https://web.archive.org/web/20201128200728/https://signalscv.com/2020/01/george-caravalho-santa-claritas-first-city-manager-dead-at-81/: hired a year after the City\'s founding as its first permanent city manager.',
  'wb2002' => 'City of Santa Clarita, City Manager page, last updated May 6, 2002, as captured by the Wayback Machine on June 7, 2002, https://web.archive.org/web/20020607094825/http://www.santa-clarita.com:80/cityhall/citymanager.htm: "Interim City Manager Kenneth R. Pulskamp"; City of Santa Clarita, FY 2002-03 budget, "Message from the Interim City Manager," July 1, 2002, https://santaclarita.gov/wp-content/uploads/sites/42/migration/coverlett.pdf. He is "City Manager" in the City Manager Office page captured February 6, 2003, https://web.archive.org/web/20030206213820/http://santa-clarita.com:80/cityhall/cmo/; the date of his permanent appointment is not yet established.',
  'puls2012' => 'City of Santa Clarita release as carried by SCVNews, October 22, 2012, https://scvnews.com/pulskamp-leaving-early-to-run-city-of-burbank/: "Ken Pulskamp\'s last day as Santa Clarita\'s City Manager is Nov. 30"; Carol Rock, "City Manager Ken Pulskamp Announces Retirement," SCVNews, August 27, 2012, https://scvnews.com/city-manager-ken-pulskamp-announces-retirement/.',
  'strip2012' => 'Leon Worden, "Ken Striplin Named Santa Clarita City Manager," SCVNews, August 30, 2012, https://scvnews.com/ken-striplin-named-santa-clarita-city-manager/: chosen by the council 4 to 1 in closed session; Leon Worden, "Earlier Start for Santa Clarita\'s New City Manager," SCVNews, November 8, 2012, https://scvnews.com/earlier-start-for-santa-claritas-new-city-manager/: the contract amended to begin on December 1, 2012.',
  'strip2026' => 'City of Santa Clarita, City Manager, https://santaclarita.gov/city-manager/, read 4 October 2026: Ken Striplin is the City Manager.',
  'bien' => 'Jerry Reynolds, History of the Santa Clarita Valley, chapter 70, "Birth of a City," with Leon Worden\'s note, as carried on SCVHistory.com, /scvhistory/signal/reynolds/part70.html (archive article #2165): Fred Bien began work the day after the cityhood vote of November 3, 1987; City Council minutes, January 14, 1988, as carried on SCVHistory.com, /scvhistory/cc011488minutes.pdf: "City Manager/City Clerk, Fred Bien."',
];
$fn = fn(array $keys) => array_map(fn($i, $k) => ['number' => (string)($i + 1), 'note' => $S[$k], 'source' => 'editorial-2026'], array_keys($keys), $keys);
$City = Entry::find()->id(394)->one(); $Car = Entry::find()->id(16396)->status(null)->one();
$bad = []; if ($City?->title !== 'The City of Santa Clarita' || $Car?->title !== 'George Caravalho') { $bad[] = 'City or Caravalho not as expected (' . $Car?->title . ')'; }
$P = ['Ken Pulskamp' => "Kenneth R. Pulskamp", 'Ken Striplin' => "Kenneth W. Striplin"];
foreach ($P as $n => $a) { echo "$n: " . (Entry::find()->section('persons')->status(null)->title($n)->exists() ? 'exists' : 'create') . PHP_EOL; }
$BIEN = "The City's first city manager was an interim, Fred Bien, hired by the council-elect, who began work the day after the cityhood vote and acted as City Clerk as well. George Caravalho, the first permanent city manager, followed him in 1988.";
echo 'City text: ' . (str_contains((string)$City->body, 'Fred Bien') ? 'has Bien' : 'add the Bien paragraph') . PHP_EOL . 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$n = 0; $pSec = $svc->getSectionByHandle('persons'); $pType = $svc->getEntryTypeByHandle('person'); $people = ['George Caravalho' => $Car];
foreach ($P as $name => $alias) { $p = Entry::find()->section('persons')->status(null)->title($name)->one();
    if (!$p) { $p = new Entry(); $p->sectionId = $pSec->id; $p->setTypeId($pType->id); $p->setFieldValues(['fullName' => $name, 'personAliases' => $alias, 'occupation' => 'City manager', 'recordProvenance' => 'record_city_managers_2026_10_04.php, 4 October 2026: City Manager of Santa Clarita']);
        if (!$el->saveElement($p)) { throw new \RuntimeException("$name " . json_encode($p->getFirstErrors())); } $n++; }
    $people[$name] = $p; }
$aSec = $svc->getSectionByHandle('affiliations'); $aType = $svc->getEntryTypeByHandle('affiliation');
$A = [['George Caravalho', 'May 1988', '1988-05', 'May 2002', '2002-05', 'left', ['sc9020', 'sig2020']],
      ['Ken Pulskamp', 'May 2002', '2002-05', 'November 30, 2012', '2012-11-30', 'retired', ['wb2002', 'puls2012']],
      ['Ken Striplin', 'December 1, 2012', '2012-12-01', '', '', 'serving', ['strip2012', 'strip2026']]];
foreach ($A as [$who, $s, $se, $e, $ee, $how, $keys]) { $p = $people[$who];
    if (Entry::find()->section('affiliations')->status(null)->relatedTo(['and', ['targetElement' => $p, 'field' => 'affiliationPerson'], ['targetElement' => 394, 'field' => 'affiliationBody']])->affiliationTitle('City Manager')->exists()) { continue; }
    $a = new Entry(); $a->sectionId = $aSec->id; $a->setTypeId($aType->id);
    $a->setFieldValues(array_filter(['affiliationPerson' => [$p->id], 'affiliationBody' => [394], 'affiliationKind' => 'employed', 'affiliationTitle' => 'City Manager', 'termStart' => $s, 'termStartEdtf' => $se, 'termEnd' => $e, 'termEndEdtf' => $ee,
        'affiliationEnded' => $how, 'startEvidence' => 'contemporary', 'endEvidence' => $e ? 'contemporary' : null, 'footnotes' => $fn($keys), 'recordProvenance' => 'record_city_managers_2026_10_04.php, 4 October 2026']));
    if (!$el->saveElement($a)) { throw new \RuntimeException("$who " . json_encode($a->getFirstErrors())); } $n++; }
if (!str_contains((string)$City->body, 'Fred Bien')) { $rows = $City->footnotes; $k = count($rows) + 1; $rows[] = ['number' => (string)$k, 'note' => $S['bien'], 'source' => 'editorial-2026'];
    $City->setFieldValues(['body' => rtrim((string)$City->body) . "\n\n" . $BIEN . "[$k]", 'footnotes' => $rows]); if (!$el->saveElement($City)) { throw new \RuntimeException('City'); } $n++; }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('record_city_managers_2026_10_04.php', $n, 'verified', 'City managers: Pulskamp and Striplin records; Caravalho, Pulskamp, Striplin as City Manager; Bien in the City\'s text');
echo "done: $n writes" . PHP_EOL;
