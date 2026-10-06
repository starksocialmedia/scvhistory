/**
 * Tom Frew III and Tom Frew IV, father and son (Nathan, 5 October 2026: "apply the title changes and the corrected notes ... Each
 * record should say the other exists and how they differ, as the two Lópezes do"). From inventory/review/tom-frew-2026-10-05.md.
 * The old notes said "no source joins them"; three do: Ruth Newhall's account of the family (Old Town Newhall Gazette, December
 * 1996, frew1296), the Newhall telephone directory of January 1958, which lists "Frew TM IV" and "Frew Thos M Jr" at different
 * addresses (hb5801), and Leon Worden's caption AP0724 ("One was Tom III, aka Thomas Frew Jr."). So the notes are corrected,
 * not merely clarified.
 * - #18783 "Tom Frew" becomes "Tom Frew IV" (the title is made from fullName, so that is the name set; the M rests on the 1958
 *   directory's "Frew TM IV"), born Thanksgiving Day, 1928, as the family account
 *   gives it; its link from "71. Requiem" (#2167, which is about Tom Frew II) is removed; he is named in photograph #2743 (LW2043).
 * - #28647 "Thomas Frew Jr." becomes "Thomas M. Frew Jr.", with the alias "Tom Frew III", the name that keeps the two apart.
 * Slugs (and so the addresses) are unchanged. Tom Frew II, the grandfather, has no record; that is Nathan's to decide.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/tom_frew_generations_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $n = 0;
$IV = Entry::find()->id(18783)->status(null)->one(); $III = Entry::find()->id(28647)->status(null)->one(); $req = Entry::find()->id(2167)->status(null)->one(); $ph = Entry::find()->id(2743)->status(null)->one();
if (!in_array($IV?->title, ['Tom Frew', 'Tom Frew IV', 'Thomas M. Frew IV'], true) || !in_array($III?->title, ['Thomas Frew Jr.', 'Thomas M. Frew Jr.'], true) || !$req || !$ph) { throw new \RuntimeException('records not as expected'); }
$NIV = 'This is Tom Frew IV, born on Thanksgiving Day, 1928, the grandson of Tom Frew II, who bought the Spruce Street blacksmith shop in 1900, and the son of Tom Frew III. He closed the shop in 1970, served as president of the Santa Clarita Valley Historical Society in the late 1990s, and left the valley in 2003 (Ruth Newhall, "The Accidental Blacksmiths Of Old Newhall," Old Town Newhall Gazette, December 1996, as carried on SCVHistory.com, /scvhistory/frew1296.htm; HS9019, /scvhistory/hs9019.htm). His father, called Thomas M. Frew Jr. in his lifetime, has his own record, Thomas M. Frew Jr.; the Newhall telephone directory of January 1958 lists the two separately, at different addresses ("Frew TM IV"; "Frew Thos M Jr", /scvhistory/hb5801.htm). Not to be confused with him.';
$NIII = 'This is Tom Frew III, called Thomas M. Frew Jr. in his lifetime, the son of Tom Frew II, who bought the Spruce Street blacksmith shop in 1900, and the father of Tom Frew IV (Ruth Newhall, "The Accidental Blacksmiths Of Old Newhall," Old Town Newhall Gazette, December 1996, as carried on SCVHistory.com, /scvhistory/frew1296.htm; Leon Worden, caption to AP0724, /scvhistory/ap0724.htm: "One was Tom III, aka Thomas Frew Jr."). His son, president of the Santa Clarita Valley Historical Society in the 1990s, has his own record, Tom Frew IV; the Newhall telephone directory of January 1958 lists the two separately (/scvhistory/hb5801.htm). Not to be confused with him.';
$notes = function ($e, $new) { $rows = array_values(array_filter(array_map(fn($r) => ['heading' => (string)$r['heading'], 'note' => (string)$r['note'], 'position' => (string)$r['position'] ?: 'bottom'], iterator_to_array($e->editorNotes ?? [])), fn($r) => $r['note'] !== '' && $r['heading'] !== 'Two men named Thomas Frew'));
  array_unshift($rows, ['heading' => 'Two men named Thomas Frew', 'note' => $new, 'position' => 'bottom']); return $rows; };
$hasNote = fn($e, $new) => in_array($new, array_map(fn($r) => (string)$r['note'], iterator_to_array($e->editorNotes ?? [])), true);
/* Tom Frew IV */
/* A person's title is made from fullName, so the name is set there. */
$v = []; if ((string)$IV->fullName !== 'Tom Frew IV') { $v['fullName'] = 'Tom Frew IV'; }
if ((string)$IV->birthDateEdtf !== '1928-11') { $v['birthDate'] = 'Thanksgiving Day, 1928'; $v['birthDateEdtf'] = '1928-11'; $v['birthEvidence'] = 'retrospective'; }
if (!$hasNote($IV, $NIV)) { $v['editorNotes'] = $notes($IV, $NIV); }
echo "#18783 -> Tom Frew IV: " . ($v ? implode(', ', array_map(fn($k) => $k === '_t' ? 'title' : $k, array_keys($v))) : 'done already') . PHP_EOL;
if ($APPLY && $v) { unset($v['_t']); $IV->setFieldValues($v); if (!$el->saveElement($IV)) { throw new \RuntimeException(json_encode($IV->getFirstErrors())); } $n++; }
/* Thomas M. Frew Jr. */
$v = []; if ((string)$III->fullName !== 'Thomas M. Frew Jr.') { $v['fullName'] = 'Thomas M. Frew Jr.'; }
if (!str_contains((string)$III->personAliases, 'Tom Frew III')) { $v['personAliases'] = trim(trim((string)$III->personAliases) . "\nTom Frew III"); }
if (!$hasNote($III, $NIII)) { $v['editorNotes'] = $notes($III, $NIII); }
echo "#28647 -> Thomas M. Frew Jr.: " . ($v ? implode(', ', array_map(fn($k) => $k === '_t' ? 'title' : $k, array_keys($v))) : 'done already') . PHP_EOL;
if ($APPLY && $v) { unset($v['_t']); $III->setFieldValues($v); if (!$el->saveElement($III)) { throw new \RuntimeException(json_encode($III->getFirstErrors())); } $n++; }
/* "71. Requiem" is about Tom Frew II */
$sp = $req->subjectPerson->status(null)->ids(); echo '#2167 subjectPerson: ' . (in_array(18783, $sp, true) ? 'remove Tom Frew IV' : 'clear already') . PHP_EOL;
if ($APPLY && in_array(18783, $sp, true)) { $req->setFieldValue('subjectPerson', array_values(array_diff($sp, [18783]))); if (!$el->saveElement($req)) { throw new \RuntimeException('#2167'); } $n++; }
/* LW2043 names him */
$pp = $ph->photoPeople->status(null)->ids(); echo '#2743 photoPeople: ' . (in_array(18783, $pp, true) ? 'linked already' : 'add Tom Frew IV ("SCVHS directors Pat Saletore and Tom Frew IV")') . PHP_EOL;
if ($APPLY && !in_array(18783, $pp, true)) { $ph->setFieldValue('photoPeople', array_merge($pp, [18783])); if (!$el->saveElement($ph)) { throw new \RuntimeException('#2743'); } $n++; }
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('tom_frew_generations_2026_10_05.php', $n, 'verified', 'Tom Frew IV and Thomas M. Frew Jr., father and son'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
