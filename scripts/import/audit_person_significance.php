/**
 * Every person record against the corrected rule (Nathan, 1 October 2026): a
 * person record requires significance to SCV history, not appearance in a
 * result. Standing for office, even often, is persistence, not significance.
 *
 * Read only. For each person: what points at it (articles, photographs,
 * documents, obituaries, war memorial, places, organizations, groups, events,
 * other people), what it holds (office holdings, candidacies won and lost, a
 * published profile body, an image, dates, outbound relations), and a proposed
 * group:
 *   KEEP    held office in the archive's records, or appears in an article,
 *           photograph, document, obituary or war memorial record, or has a
 *           published profile body, or is otherwise documented (a place, an
 *           organization or another person points at it).
 *   REMOVE  nothing but candidacies, none won.
 *   ASK     the rest: won an election but holds no office record (a body with
 *           thin data), or lost every race but carries something of its own (an
 *           unpublished body, dates, an image, outbound relations).
 *
 * Writes inventory/review/person-significance.md and .json.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/audit_person_significance.php'))"
 */

use craft\elements\Entry;

$root = \Craft::getAlias('@root');
$rows = [];
$persons = Entry::find()->section('persons')->status(null)->orderBy('title')->all();
foreach ($persons as $p) {
    $inb = (new \craft\db\Query())->select(['s.handle AS sec', 'f.handle AS fld', 'r.sourceId'])->from(['r' => '{{%relations}}'])
        ->innerJoin(['el' => '{{%elements}}'], 'el.id = r.sourceId')->innerJoin(['e' => '{{%entries}}'], 'e.id = r.sourceId')
        ->innerJoin(['s' => '{{%sections}}'], 's.id = e.sectionId')->innerJoin(['f' => '{{%fields}}'], 'f.id = r.fieldId')
        ->where(['r.targetId' => $p->id, 'el.revisionId' => null, 'el.draftId' => null, 'el.dateDeleted' => null])->all();
    $by = []; foreach ($inb as $r) { $by[$r['sec']][$r['sourceId']] = $r['fld']; }
    $cands = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $p, 'field' => 'candidacyPerson'])->all();
    $won = array_filter($cands, fn($c) => (string)$c->outcome->value === 'elected');
    $h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
    $auth = in_array('bodyAuthorship', $h) ? (string)$p->bodyAuthorship->value : '';
    $body = trim(strip_tags((string)$p->body));
    $out = (int)(new \craft\db\Query())->from(['r' => '{{%relations}}'])->innerJoin(['f' => '{{%fields}}'], 'f.id = r.fieldId')
        ->where(['r.sourceId' => $p->id])->andWhere(['not in', 'f.handle', ['roles']])->count();
    $hold = count($by['officeHoldings'] ?? []);
    $docd = array_sum(array_map(fn($s) => count($by[$s] ?? []), ['articles', 'photographs', 'documents', 'obituaries', 'warMemorials']));
    $other = array_sum(array_map(fn($s) => count($by[$s] ?? []), ['places', 'organizations', 'groups', 'events', 'persons', 'collections']));
    $published = $body !== '' && in_array($auth, ['legacy-leon', 'editorial-2026'], true);
    $img = in_array('featuredImage', $h) && $p->featuredImage->one();
    $dates = trim((string)($p->birthDate ?? '') . (string)($p->deathDate ?? '')) !== '';
    if ($hold || $docd || $published || $other) { $g = 'KEEP'; }
    elseif ($cands && !$won && !$body && !$img && !$out && !$dates) { $g = 'REMOVE'; }
    else { $g = 'ASK'; }
    $why = [];
    if ($hold) { $why[] = "$hold office holding" . ($hold > 1 ? 's' : ''); }
    foreach (['articles', 'photographs', 'documents', 'obituaries', 'warMemorials', 'places', 'organizations', 'groups', 'events', 'persons', 'collections'] as $s) { if (!empty($by[$s])) { $why[] = count($by[$s]) . ' ' . $s; } }
    if ($published) { $why[] = 'published profile (' . $auth . ')'; } elseif ($body !== '') { $why[] = 'unpublished body (' . ($auth ?: 'no authorship') . ')'; }
    if ($img) { $why[] = 'image'; } if ($dates) { $why[] = 'dates'; } if ($out) { $why[] = "$out outbound relations"; }
    if ($cands) { $why[] = count($cands) . ' candidac' . (count($cands) > 1 ? 'ies' : 'y') . ', ' . count($won) . ' won'; }
    $bodies = array_values(array_unique(array_map(fn($c) => ($b = $c->candidacyElection->one()?->electionBody->one()) && $b->id != 394 ? $b->title : 'City Council', $cands)));
    $rows[] = ['id' => $p->id, 'title' => $p->title, 'group' => $g, 'holds' => $why ?: ['nothing'], 'bodies' => $bodies, 'provenance' => (string)$p->recordProvenance];
}
$count = array_count_values(array_column($rows, 'group'));
$md = ["# Person records against the significance rule", '', 'Read only, ' . date('j F Y') . '. ' . count($rows) . ' person records: ' . implode(', ', array_map(fn($g) => "$g " . ($count[$g] ?? 0), ['KEEP', 'REMOVE', 'ASK'])) . '. Rule: a person record requires significance to SCV history, not appearance in a result (Nathan, 1 October 2026).', ''];
foreach (['REMOVE', 'ASK', 'KEEP'] as $g) {
    $md[] = "## $g (" . ($count[$g] ?? 0) . ')'; $md[] = '';
    foreach (array_filter($rows, fn($r) => $r['group'] === $g) as $r) { $md[] = "- #{$r['id']} {$r['title']}: " . implode('; ', $r['holds']) . ($r['bodies'] ? ' [' . implode(', ', $r['bodies']) . ']' : ''); }
    $md[] = '';
}
file_put_contents("$root/inventory/review/person-significance.md", implode("\n", $md));
file_put_contents("$root/inventory/review/person-significance.json", json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo implode(', ', array_map(fn($g) => "$g " . ($count[$g] ?? 0), ['KEEP', 'REMOVE', 'ASK'])) . ' of ' . count($rows) . PHP_EOL;
