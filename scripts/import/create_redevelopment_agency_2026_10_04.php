/**
 * The Redevelopment Agency of the City of Santa Clarita, and the end of the Newhall Redevelopment Committee (Nathan,
 * 4 October 2026: "Yes to the Redevelopment Agency record. It is a real body, legally distinct from the City, it ran
 * Newhall's redevelopment, and the Newhall Redevelopment Committee advised it"; "Find its dissolution date from the
 * state's 2011-12 wind-up of redevelopment agencies, which should also settle the committee's end").
 * From inventory/review/aadusd-redevelopment-2026-10-04.md, sources in inventory/news/aadusd-redevelopment-2026-10-04/.
 *  1. The Agency: founded 1989 (the City's own booklet, 2012, a retrospective account); dissolved as of 1 February 2012
 *     under AB X1 26, in the City's own words; parent the City (#394); no Successor Agency record (the City acting in
 *     that capacity, said in the body). Leon Worden's column of 11 February 2000 (#12302) gains it as a subject.
 *  2. The Committee (#16290): dateDissolved 1 March 2012, from the SCVHistory chronology, the only dated source; the
 *     agency's end on 1 February is the reason it gives, a month earlier. Its footnotes are rewritten with handle keys only
 *     (ERRORLOG, 4 October: rows read back carry column keys too).
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_redevelopment_agency_2026_10_04.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $svc = Craft::$app->getEntries(); $T = 'Redevelopment Agency of the City of Santa Clarita';
$CITY = 'City of Santa Clarita, agenda report';
$NOTES = [
  'City of Santa Clarita, "The Redevelopment Story of the City of Santa Clarita" (2012), as carried on SCVHistory.com, /scvhistory/sc_newhallredevelopment19972012.htm: "the Santa Clarita City Council formed the Redevelopment Agency (Agency) in 1989 and the Newhall Redevelopment Project Area (Project Area) in 1997"; "When the Agency was originally formed, the City loaned funding to start the operation, until tax increment started to generate. Then in 2008, the Agency issued bonds."',
  'Leon Worden, "What\'s up with redevelopment funds?", February 11, 2000, as carried on SCVHistory.com, /scvhistory/signal/worden/old/lw021100.htm.',
  'SCVHistory.com, chronology, /scvhistory/timeline.htm: "July 8: Santa Clarita City Council adopts initial Newhall Redevelopment Plan" (1997).',
  'Christine Bitter, "City Council fills 3 seats with 5 people," The Signal, June 8, 2002, as carried on SCVHistory.com, /oldtownnewhall/news/sg062802.htm.',
  "$CITY to the Redevelopment Agency Board, August 23, 2011, https://santaclarita.gov/home/showpublisheddocument/5532/635835750691630000: \"On June 29, 2011, as part of adopting the State of California Fiscal Year 2011-12 budget, the Governor signed two trailer bills: AB X1 26 (\u{201C}Dissolution Act\u{201D}) and AB X1 27.\"",
  "$CITY, January 24, 2012, https://santaclarita.gov/home/showpublisheddocument/5903/635835750691630000: in December 2011 the Supreme Court, in California Redevelopment Association v. Matosantos (which the report calls \"California Redevelopment Agency v. Matosantos\"), \"upheld ABX1 26 but struck down ABX1 27\"; \"Under ABX1 26, Redevelopment Agencies are dissolved as of February 1, 2012, and replaced with Successor Agencies\"; \"The City automatically becomes the Successor Agency, unless otherwise determined by the City Council.\"",
  "$CITY to the City Council acting as Successor Agency, April 24, 2012, https://santaclarita.gov/home/showpublisheddocument/6092/635835750691630000: \"On February 28, 2012, the Successor Agency adopted the ROPS\" for February 1 to June 30, 2012.",
  'City of Santa Clarita, Housing Successor, https://santaclarita.gov/community-development/redevelopment/housing-successor/, read 4 October 2026; Housing Successor Annual Report, fiscal year 2014-15, https://santaclarita.gov/home/showpublisheddocument/10049/636057511431100000.'];
$BODY = "The Redevelopment Agency of the City of Santa Clarita was the City's redevelopment agency, a public body separate in law from the City, with its own budget, debts and property. Its board was the City Council: as Leon Worden wrote in 2000, \"the redevelopment agency board and the City Council are the same five people.\"[2] The Council formed the agency in 1989.[1]\n\nAn earlier plan, for about \$1.1 billion, was in Worden's words \"killed in the courts because it violated state law.\" The agency had borrowed from the City to pay for it, and from 1993 to 1999 it spent about \$2.4 million.[2] In 1997 the Council formed the Newhall Redevelopment Project Area, adopting its initial plan on July 8, and the old downtown of Newhall became the agency's work.[1][3] The City lent the agency its first funds until tax increment began to come in; in 2008 the agency issued its own bonds for the work on Main Street.[1] The Newhall Redevelopment Committee, a citizens' committee formed in 1996, advised it.[4]\n\nOn June 29, 2011, the Governor signed AB X1 26, the Dissolution Act, as part of the state budget.[5] In December 2011 the California Supreme Court upheld it, and under it every redevelopment agency in the state was dissolved as of February 1, 2012. The City became the Successor Agency, to wind down the agency's affairs; the City Council, acting as Successor Agency, adopted its first schedule of payments on February 28, 2012.[6][7] The City is also the Housing Successor to the former agency.[8]";
$CBODY = "\n\nThe committee was formally dissolved on March 1, 2012, after the state abolished redevelopment agencies; the agency it advised had been dissolved on February 1.[2][3]";
$CNOTES = [
  'SCVHistory.com, chronology, /scvhistory/timeline.htm: "March 1: Newhall Redevelopment Committee formally dissolved, after state outlaws redevelopment agencies" (2012). The only dated source found; the City action that dissolved it has not been found (searched 4 October 2026: the archive\'s mirror of SCVHistory.com and the City\'s 2011 and 2012 redevelopment agenda reports; the City\'s online agenda archive refused automated reads).',
  "$CITY, January 24, 2012, https://santaclarita.gov/home/showpublisheddocument/5903/635835750691630000: \"Under ABX1 26, Redevelopment Agencies are dissolved as of February 1, 2012.\""];
$fn = fn(array $notes) => array_map(fn($i, $t) => ['number' => (string)($i + 1), 'note' => $t, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$a = Entry::find()->section('organizations')->status(null)->title($T)->one(); $c = Entry::find()->id(16290)->status(null)->one(); $w = Entry::find()->id(12302)->status(null)->one();
$bad = []; if (!$c || $c->title !== 'Newhall Redevelopment Committee') { $bad[] = '#16290 is not the committee'; } if (!$w || !str_contains((string)$w->body, 'same five people')) { $bad[] = '#12302 is not Worden\'s column'; }
echo "$T: " . ($a ? "#{$a->id} exists" : 'create') . "; committee #16290 dissolved now: " . ($c?->dateDissolvedEdtf ?: 'none') . PHP_EOL . 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$n = 0; $os = $svc->getSectionByHandle('organizations');
if (!$a) { $a = new Entry(); $a->sectionId = $os->id; $a->setTypeId($os->getEntryTypes()[0]->id); $a->title = $T; }
if (!str_contains((string)$a->body, 'Dissolution Act')) {
  $h = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields());
  $v = ['orgType' => 'government', 'orgLevel' => 'valley', 'hasParentOrg' => true, 'parentOrganization' => [394], 'orgAliases' => 'Santa Clarita Redevelopment Agency',
    'dateFounded' => '1989', 'dateFoundedEdtf' => '1989', 'foundedEvidence' => 'retrospective', 'dateDissolved' => 'February 1, 2012', 'dateDissolvedEdtf' => '2012-02-01', 'neighborhood' => [199],
    'body' => $BODY, 'footnotes' => $fn($NOTES), 'recordProvenance' => 'create_redevelopment_agency_2026_10_04.php, 4 October 2026: the body the Newhall Redevelopment Committee advised'];
  $a->setFieldValues(array_intersect_key($v, array_flip($h))); if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); } $n++;
}
if (!str_contains((string)$c->body, 'formally dissolved')) {
  $old = array_map(fn($r) => (string)$r['note'], iterator_to_array($c->footnotes)); if (count($old) !== 1) { throw new \RuntimeException('committee footnotes not as expected: ' . count($old)); }
  $c->setFieldValues(['body' => rtrim((string)$c->body) . $CBODY, 'footnotes' => $fn(array_merge($old, $CNOTES)), 'dateDissolved' => 'March 1, 2012', 'dateDissolvedEdtf' => '2012-03-01']);
  if (!$el->saveElement($c)) { throw new \RuntimeException(json_encode($c->getFirstErrors())); } $n++;
}
$ids = $w->subjectOrganization->ids(); if (!in_array($a->id, $ids)) { $w->setFieldValue('subjectOrganization', array_merge($ids, [$a->id])); if (!$el->saveElement($w)) { throw new \RuntimeException('#12302'); } $n++; }
$a = Entry::find()->id($a->id)->status(null)->one(); $c = Entry::find()->id(16290)->status(null)->one();
if ($a->dateDissolvedEdtf !== '2012-02-01' || count($a->footnotes) !== count($NOTES) || $c->dateDissolvedEdtf !== '2012-03-01' || count($c->footnotes) !== 3) { throw new \RuntimeException('not read back'); }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('create_redevelopment_agency_2026_10_04.php', $n, 'verified', 'Redevelopment Agency of the City of Santa Clarita (1989 to 1 February 2012); Newhall Redevelopment Committee dissolved 1 March 2012');
echo "done: $n writes (#{$a->id} {$a->uri})" . PHP_EOL;
