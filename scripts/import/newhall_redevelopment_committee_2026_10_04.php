/**
 * The Newhall Redevelopment Committee, a City committee (Nathan, 4 October 2026: "Move it there from Valley government,
 * into the City's card with the others"), from Christine Bitter's Signal article of 8 June 2002, as carried on the legacy
 * site (/oldtownnewhall/news/sg062802.htm, read in the mirror).
 *  1. The committee nested under the City (#394), founded 1996, with what the article says of it.
 *  2. Affiliations: Leon Worden, its chairman in June 2002; Susan Shapiro, appointed an alternate in June 2002, then
 *     community programming manager at AT&T Broadband's public access studio in Newhall. The other appointees (Amparo
 *     Cevallos, Arthur Sohikian, Robert J. Spierer, John Grannis) are named in the text only.
 *  3. The 2002 mayoralty, which the City's pages for sitting members do not give: Frank Ferry, Mayor, and Cameron Smyth,
 *     Mayor Pro Tem, as the article names them in June 2002.
 *  4. Bob Kellar: the article spells him "Keller"; the variant is added to his aliases, the quotation left as printed.
 * When the committee ended is not yet established; nothing is recorded for it.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/newhall_redevelopment_committee_2026_10_04.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $svc = Craft::$app->getEntries();
$SIG = 'Christine Bitter, "City Council fills 3 seats with 5 people," The Signal, June 8, 2002, as carried on SCVHistory.com under the title "Council Makes Redevelopment Committee Appointments," /oldtownnewhall/news/sg062802.htm.';
$C = Entry::find()->id(16290)->status(null)->one(); $W = Entry::find()->id(279)->status(null)->one(); $S = Entry::find()->id(29100)->status(null)->one(); $K = Entry::find()->id(21944)->status(null)->one();
$F = Entry::find()->id(23083)->status(null)->one(); $SM = Entry::find()->id(16380)->status(null)->one();
$bad = []; foreach ([[$C, 'Newhall Redevelopment Committee'], [$W, 'Leon Worden'], [$S, 'Susan Shapiro'], [$K, 'Bob Kellar'], [$F, 'Frank Ferry'], [$SM, 'Cameron Smyth']] as [$e, $t]) { if ($e?->title !== $t) { $bad[] = "not $t"; } }
$BODY = "The Newhall Redevelopment Committee was a committee of the City of Santa Clarita, established in 1996 to advise the Santa Clarita Redevelopment Agency, which was the City Council sitting as the agency, and to promote the revitalization of Newhall. By 2002 it counted among its work a redevelopment plan for Newhall, the Newhall Street Fair, architectural guidelines and a storefront improvement program. Its members were appointed by the City Council, without term limits, and it met in public on the first Monday of each month at City Hall.[1]\n\nIn June 2002, with Leon Worden as its chairman, the council filled three open seats from ten applicants, appointing Amparo Cevallos, Arthur Sohikian and Robert J. Spierer, and added two alternates, John Grannis and Susan Shapiro, who would not vote until they were given a seat.[1]";
echo "committee #16290: under the City; body; founded 1996" . PHP_EOL . 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$n = 0; $fn = fn(array $x) => array_map(fn($i, $t) => ['number' => (string)($i + 1), 'note' => $t, 'source' => 'editorial-2026'], array_keys($x), $x);
if (!str_contains((string)$C->body, 'established in 1996')) {
    $C->setFieldValues(['hasParentOrg' => true, 'parentOrganization' => [394], 'body' => $BODY, 'footnotes' => $fn([$SIG]), 'dateFounded' => '1996', 'dateFoundedEdtf' => '1996', 'foundedEvidence' => 'contemporary',
        'recordProvenance' => trim((string)$C->recordProvenance . '; newhall_redevelopment_committee_2026_10_04.php, 4 October 2026: under the City')]);
    if (!$el->saveElement($C)) { throw new \RuntimeException(json_encode($C->getFirstErrors())); } $n++; }
$aSec = $svc->getSectionByHandle('affiliations'); $aType = $svc->getEntryTypeByHandle('affiliation');
foreach ([[$W, 'Chairman', 'June 2002', '2002-06', 'member', 'As chairman in June 2002, when the council made its appointments, he said: "It\'s exciting to see so many good people volunteer to help improve our Old Town, and I know we\'ll benefit from the diversity of ideas and backgrounds."'],
          [$S, 'Alternate member', 'June 2002', '2002-06', 'member', 'Appointed an alternate in June 2002, non-voting until given a seat; the article describes her as community programming manager at AT&T Broadband\'s public access television studio in Newhall.']] as [$p, $title, $s, $se, $kind, $note]) {
    if (Entry::find()->section('affiliations')->status(null)->relatedTo(['and', ['targetElement' => $p, 'field' => 'affiliationPerson'], ['targetElement' => $C, 'field' => 'affiliationBody']])->exists()) { continue; }
    $a = new Entry(); $a->sectionId = $aSec->id; $a->setTypeId($aType->id);
    $a->setFieldValues(['affiliationPerson' => [$p->id], 'affiliationBody' => [$C->id], 'affiliationKind' => $kind, 'affiliationTitle' => $title, 'termStart' => $s, 'termStartEdtf' => $se, 'affiliationEnded' => 'unknown', 'startEvidence' => 'contemporary',
        'footnotes' => $fn([$SIG . ' ' . $note]), 'recordProvenance' => 'newhall_redevelopment_committee_2026_10_04.php, 4 October 2026']);
    if (!$el->saveElement($a)) { throw new \RuntimeException($p->title . json_encode($a->getFirstErrors())); } $n++;
}
$hs = $svc->getSectionByHandle('officeHoldings'); $ht = $svc->getEntryTypeByHandle('officeHolding');
foreach ([[$F, 18431, 'Mayor'], [$SM, 29044, 'Mayor Pro Tem']] as [$p, $role, $t]) {
    if (Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $p, 'field' => 'holdingPerson'], ['targetElement' => $role, 'field' => 'holdingOffice']])->termStartEdtf('2002')->exists()) { continue; }
    $h = new Entry(); $h->sectionId = $hs->id; $h->setTypeId($ht->id);
    $h->setFieldValues(['holdingPerson' => [$p->id], 'holdingOffice' => [$role], 'holdingBody' => [394], 'selectionMethod' => 'rotated', 'termStart' => '2002', 'termStartEdtf' => '2002', 'howEnded' => 'expired', 'startEvidence' => 'contemporary',
        'footnotes' => $fn([$SIG . " The article names him $t in June 2002."]), 'recordProvenance' => 'newhall_redevelopment_committee_2026_10_04.php, 4 October 2026: the 2002 mayoralty from The Signal']);
    if (!$el->saveElement($h)) { throw new \RuntimeException($t . json_encode($h->getFirstErrors())); } $n++;
}
$al = (string)$K->personAliases; if (!str_contains($al, 'Bob Keller')) { $K->setFieldValue('personAliases', trim($al . "\nBob Keller")); if (!$el->saveElement($K)) { throw new \RuntimeException('Kellar'); } $n++; }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('newhall_redevelopment_committee_2026_10_04.php', $n, 'verified', 'Newhall Redevelopment Committee under the City, from The Signal of 8 June 2002; Worden chairman, Shapiro alternate; Ferry Mayor and Smyth Mayor Pro Tem, 2002; Keller variant');
echo "done: $n writes" . PHP_EOL;
