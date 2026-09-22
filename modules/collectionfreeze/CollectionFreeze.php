<?php
/**
 * Refuses console writes to anything in a frozen collection.
 *
 * A collection is frozen when its collectionFrozen lightswitch is on. From then
 * on it belongs to the control panel: the editorial fixes are made there by
 * hand, and a re-run of any import or rebuild script would overwrite them
 * without saying so. This is what stops that re-run.
 *
 * WHY HERE AND NOT IN EACH SCRIPT
 *
 * There are about ninety write scripts under scripts/import. A guard copied
 * into each one is a guard the ninety-first forgets. Every one of them writes
 * through Craft's element service inside `ddev craft exec`, which is a console
 * request, so one event handler covers the lot, including scripts not yet
 * written. The one raw-SQL writer, fix_asset_provenance.php, deletes asset
 * relations only and does not touch entries.
 *
 * WHAT COUNTS AS IN A FROZEN COLLECTION
 *
 *   - the collection entry itself, when its lightswitch is on
 *   - any entry whose saved partOfCollection points at a frozen collection
 *   - any entry a frozen collection lists in articlesInCollection
 *   - any entry about to be saved with partOfCollection pointing at one, so a
 *     script can't add a new piece to a frozen collection either
 * Drafts and revisions count through their canonical entry.
 *
 * WHAT IS LET THROUGH
 *
 *   - web requests, which is the control panel. That is where the edits go.
 *   - resaves Craft queues for itself after a schema change, when they run
 *     inside a queue job. Those don't come from a script and change no content.
 *     `craft resave/entries` run by hand is not a queue job and is refused.
 *
 * A refused save returns false with an error on the element, and a line goes to
 * stderr. The script carries on with the entries it is still allowed to write,
 * and a summary of everything refused prints when the process exits. A
 * script's read-back will also come up short, which is meant to happen.
 *
 * Inert until the collectionFrozen field exists.
 */

namespace modules\collectionfreeze;

use Craft;
use craft\base\Element;
use craft\elements\Entry;
use craft\events\ModelEvent;
use yii\base\Event;
use yii\base\Module;
use yii\queue\Queue;

class CollectionFreeze extends Module
{
    /** @var int[]|null frozen collection ids */
    private ?array $_frozen = null;
    /** @var array<int,int>|null member entry id => its frozen collection id */
    private ?array $_members = null;
    private bool $_inQueueJob = false;
    /** @var array<int,string> */
    private array $_refused = [];

    public function init(): void
    {
        parent::init();

        if (!Craft::$app->getRequest()->getIsConsoleRequest()) {
            return;
        }

        Event::on(Queue::class, Queue::EVENT_BEFORE_EXEC, function () { $this->_inQueueJob = true; });
        Event::on(Queue::class, Queue::EVENT_AFTER_EXEC, function () { $this->_inQueueJob = false; });
        Event::on(Queue::class, Queue::EVENT_AFTER_ERROR, function () { $this->_inQueueJob = false; });

        Event::on(Entry::class, Element::EVENT_BEFORE_SAVE, function (ModelEvent $e) {
            $this->_check($e, 'save');
        });
        Event::on(Entry::class, Element::EVENT_BEFORE_DELETE, function (ModelEvent $e) {
            $this->_check($e, 'delete');
        });

        register_shutdown_function(function () {
            if (!$this->_refused) { return; }
            $out = PHP_EOL . str_repeat('!', 74) . PHP_EOL
                 . 'FROZEN: refused ' . count($this->_refused) . ' write(s) to frozen collections' . PHP_EOL;
            foreach (array_slice($this->_refused, 0, 40, true) as $line) { $out .= '   ' . $line . PHP_EOL; }
            if (count($this->_refused) > 40) { $out .= '   ... and ' . (count($this->_refused) - 40) . ' more' . PHP_EOL; }
            $out .= str_repeat('!', 74) . PHP_EOL;
            echo $out;
        });
    }

    private function _check(ModelEvent $e, string $verb): void
    {
        /** @var Entry $entry */
        $entry = $e->sender;
        if ($this->_inQueueJob && $entry->resaving) { return; }

        $this->_load();
        if (!$this->_frozen) { return; }

        $id = $entry->getCanonicalId();
        $why = null;

        if ($id && in_array($id, $this->_frozen, true)) {
            $why = 'is a frozen collection';
        } elseif ($id && isset($this->_members[$id])) {
            $why = 'is in frozen collection #' . $this->_members[$id];
        } elseif ($entry->getFieldLayout()?->getFieldByHandle('partOfCollection')
                  && $entry->isFieldDirty('partOfCollection')) {
            $pending = $entry->getFieldValue('partOfCollection');
            $ids = is_object($pending) && method_exists($pending, 'ids')
                ? $pending->status(null)->ids()
                : (array)$pending;
            $hit = array_values(array_intersect(array_map('intval', $ids), $this->_frozen));
            if ($hit) { $why = 'would be added to frozen collection #' . $hit[0]; }
        }

        if ($why === null) { return; }

        $e->isValid = false;
        $msg = 'refused ' . $verb . ' of #' . ($id ?: 'new') . ' "' . $entry->title . '": ' . $why
             . '. Unfreeze it in the control panel to run a script against it.';
        $entry->addError('collectionFrozen', $msg);
        $this->_refused[] = $msg;
        fwrite(STDERR, 'FROZEN ' . $msg . PHP_EOL);
    }

    /** Read once per process. The lightswitch is only changed from the CP, never mid-script. */
    private function _load(): void
    {
        if ($this->_frozen !== null) { return; }
        $this->_frozen = [];
        $this->_members = [];

        $fields = Craft::$app->getFields();
        if (!$fields->getFieldByHandle('collectionFrozen')) { return; }

        $this->_frozen = array_map('intval', Entry::find()
            ->section('collections')->status(null)->limit(null)
            ->collectionFrozen(true)->ids());
        if (!$this->_frozen) { return; }

        $db = Craft::$app->getDb();
        $partOf = $fields->getFieldByHandle('partOfCollection');
        $listed = $fields->getFieldByHandle('articlesInCollection');

        if ($partOf) {
            $rows = (new \craft\db\Query())->select(['sourceId', 'targetId'])->from('{{%relations}}')
                ->where(['fieldId' => $partOf->id, 'targetId' => $this->_frozen])->all($db);
            foreach ($rows as $r) { $this->_members[(int)$r['sourceId']] = (int)$r['targetId']; }
        }
        if ($listed) {
            $rows = (new \craft\db\Query())->select(['sourceId', 'targetId'])->from('{{%relations}}')
                ->where(['fieldId' => $listed->id, 'sourceId' => $this->_frozen])->all($db);
            foreach ($rows as $r) { $this->_members[(int)$r['targetId']] = (int)$r['sourceId']; }
        }
    }
}
