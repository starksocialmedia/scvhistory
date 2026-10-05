/**
 * Sending a photograph to the archive: the submissions section (Nathan, 4 October 2026: "The photo submission form,
 * approved as proposed: hidden field, timing check, rate limit, no Turnstile"; inventory/review/photo-submission-proposal-2026-10-04.md).
 *
 * One entry per submission, in a channel with no URLs whose entries are saved disabled, so nothing in it is public.
 * The files are not assets: they sit in storage/submissions/<entry uid>/, outside the web root, and become archive
 * media only when the archivist accepts one on /admin-submissions (modules/submissions).
 *
 *   submissionRecord       the record it is for
 *   submissionKind         photograph (the only kind for now)
 *   submissionStatus       new, accepted, declined, spam
 *   submissionWho          who is in it, and which is the subject
 *   submissionWhen         when it was taken, as the sender gives it
 *   submissionTakenBy      who took it, if known
 *   submissionHolder       who holds the original print or file now
 *   submissionSenderName   the sender's name
 *   submissionCredit       the credit the sender asks for ("" = no credit)
 *   submissionEmail        the sender's address; never published, cleared a year after the decision
 *   submissionPermission   the two boxes as ticked, with the time
 *   submissionFiles        JSON: each file's stored name, original name, SHA-256 of the bytes sent, size, pixels
 *   submissionDecision     what was decided, when, and why (a decline's one-line reason)
 *
 * Writes project config (fields, an entry type, a section). Safe to run twice. Dry run by default; set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_submissions_section.php'))"
 */
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $es = Craft::$app->getEntries();
$TEXT = [
  'submissionWho' => ['Submission: who is in it', true], 'submissionWhen' => ['Submission: when taken', false],
  'submissionTakenBy' => ['Submission: taken by', false], 'submissionHolder' => ['Submission: original held by', false],
  'submissionSenderName' => ['Submission: sender', false], 'submissionCredit' => ['Submission: credit asked for', false],
  'submissionEmail' => ['Submission: sender email (private)', false], 'submissionPermission' => ['Submission: permission given', true],
  'submissionFiles' => ['Submission: files', true], 'submissionDecision' => ['Submission: decision', true],
];
$n = 0;
foreach ($TEXT as $h => [$name, $multi]) {
  if ($fs->getFieldByHandle($h)) { echo "$h exists\n"; continue; }
  echo "$h create\n"; if (!$APPLY) { continue; }
  $f = new \craft\fields\PlainText(['name' => $name, 'handle' => $h, 'multiline' => $multi, 'searchable' => false]);
  if (!$fs->saveField($f)) { throw new \RuntimeException("$h " . json_encode($f->getFirstErrors())); } $n++;
}
$DROP = [
  'submissionKind' => ['Submission: kind', [['label' => 'Photograph', 'value' => 'photograph', 'default' => true]]],
  'submissionStatus' => ['Submission: status', [['label' => 'New', 'value' => 'new', 'default' => true], ['label' => 'Accepted', 'value' => 'accepted', 'default' => false], ['label' => 'Declined', 'value' => 'declined', 'default' => false], ['label' => 'Spam', 'value' => 'spam', 'default' => false]]],
];
foreach ($DROP as $h => [$name, $opts]) {
  if ($fs->getFieldByHandle($h)) { echo "$h exists\n"; continue; }
  echo "$h create\n"; if (!$APPLY) { continue; }
  $f = new \craft\fields\Dropdown(['name' => $name, 'handle' => $h, 'options' => $opts]);
  if (!$fs->saveField($f)) { throw new \RuntimeException("$h " . json_encode($f->getFirstErrors())); } $n++;
}
if (!$fs->getFieldByHandle('submissionRecord')) {
  echo "submissionRecord create\n";
  if ($APPLY) { $f = new \craft\fields\Entries(['name' => 'Submission: for record', 'handle' => 'submissionRecord', 'maxRelations' => 1, 'sources' => '*']);
    if (!$fs->saveField($f)) { throw new \RuntimeException('submissionRecord ' . json_encode($f->getFirstErrors())); } $n++; }
} else { echo "submissionRecord exists\n"; }
$ORDER = ['submissionStatus', 'submissionRecord', 'submissionKind', 'submissionWho', 'submissionWhen', 'submissionTakenBy', 'submissionHolder', 'submissionSenderName', 'submissionCredit', 'submissionEmail', 'submissionPermission', 'submissionFiles', 'submissionDecision'];
$et = $es->getEntryTypeByHandle('submission');
if ($et) { echo "entry type exists\n"; } else {
  echo "entry type create\n";
  if ($APPLY) {
    $et = new \craft\models\EntryType(['name' => 'Submission', 'handle' => 'submission', 'hasTitleField' => true]);
    $layout = new \craft\models\FieldLayout(['type' => \craft\elements\Entry::class]);
    $tab = new \craft\models\FieldLayoutTab(['layout' => $layout, 'name' => 'Submission', 'sortOrder' => 1]);
    $els = [new \craft\fieldlayoutelements\entries\EntryTitleField()];
    foreach ($ORDER as $h) { $els[] = new \craft\fieldlayoutelements\CustomField($fs->getFieldByHandle($h)); }
    $tab->setElements($els); $layout->setTabs([$tab]); $et->setFieldLayout($layout);
    if (!$es->saveEntryType($et)) { throw new \RuntimeException('entry type ' . json_encode($et->getErrors())); } $n++;
  }
}
if ($es->getSectionByHandle('submissions')) { echo "section exists\n"; } else {
  echo "section create (channel, no URLs, disabled by default)\n";
  if ($APPLY) {
    $s = new \craft\models\Section(['name' => 'Submissions', 'handle' => 'submissions', 'type' => \craft\models\Section::TYPE_CHANNEL, 'enableVersioning' => false]);
    $ss = []; foreach (Craft::$app->getSites()->getAllSites() as $site) { $ss[$site->id] = new \craft\models\Section_SiteSettings(['siteId' => $site->id, 'enabledByDefault' => false, 'hasUrls' => false]); }
    $s->setSiteSettings($ss); $s->setEntryTypes([$et]);
    if (!$es->saveSection($s)) { throw new \RuntimeException('section ' . json_encode($s->getErrors())); } $n++;
  }
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('add_submissions_section.php', $n, 'verified', 'submissions section: 13 fields, entry type, channel with no URLs'); }
echo ($APPLY ? "done: $n writes" : 'nothing written') . PHP_EOL;
