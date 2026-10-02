/**
 * READ ONLY. Source labels pasted into a record's text (2 October 2026).
 *
 * A chatbot answer pasted into a body brings its citation chips with it as
 * bare words between sentences: "...38th District. Wikipedia. He served",
 * "...Santa Clarita Valley Water Agency. LinkedIn Unifying". Wilk's record had
 * them; the sweep that followed looked only at living persons whose
 * bodyAuthorship said "wordpress-import-unsourced", and missed two records
 * whose sections have no bodyAuthorship field at all (Santa Clarita Valley
 * Water, an organization, and the Newhall Pass interchange, a place). This
 * checks the shape itself, in every body of every section, whatever its
 * authorship field says.
 *
 * Legacy text is left out: Leon Worden's pages are not rewritten, and their
 * datelines ("The Signal | Saturday, April 9, 2005") would read as labels.
 * Returns ['ok' => bool, 'fails' => [...]] for check_render.php.
 * Run alone: ddev craft exec "eval(file_get_contents('scripts/import/check_pasted_labels.php'))"
 */

ini_set('memory_limit', '2048M');
$LBL = 'Wikipedia|Yourscvwater|LinkedIn|Ballotpedia|Britannica|Find a Grave|FamilySearch|Calisphere|HMdb|KHTS|SCVNews|signalscv|Santa Clarita Valley Signal|Hometown Station|Patch|ANCA[^.]{0,25}|Facebook|Instagram|YouTube|IMDb|Ancestry|Newspapers\.com|ca\.gov|\.com|\.org';
$fails = [];
foreach (\craft\elements\Entry::find()->status(null)->each(200) as $e) {
    $l = $e->getFieldLayout();
    if (!$l->getFieldByHandle('body')) { continue; }
    if ($l->getFieldByHandle('legacyUrl') && trim((string)$e->legacyUrl) !== '') { continue; }
    $t = preg_replace('~\s+~u', ' ', html_entity_decode(strip_tags((string)$e->body)));
    if (preg_match_all('~(?<=[.!?"”])\s+(' . $LBL . ')(?:\s*[.—–-]\s*|\s+(?=[A-Z]))|\s(' . $LBL . '|CA)\s*$|\b\d{4}\s+(' . $LBL . ')\s+[a-z]~u', $t, $m)) {
        $labels = array_count_values(array_map('trim', array_filter(array_merge($m[1], $m[2], $m[3]))));
        $fails[] = "PASTED LABELS  {$e->section->handle} #{$e->id} {$e->title}: " . json_encode($labels);
    }
}
echo ($fails ? count($fails) . ' records with pasted source labels' . PHP_EOL . implode(PHP_EOL, $fails) : 'no pasted source labels in any non-legacy body') . PHP_EOL;
return ['ok' => !$fails, 'fails' => $fails];
