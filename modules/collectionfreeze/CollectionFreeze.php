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
 *   - clearing the switch. Unfreezing is allowed from anywhere, including a
 *     console script, because a freeze nobody can undo is a trap rather than a
 *     control. Only the unfreeze on its own: a save that clears the switch and
 *     changes something else at the same time is refused and says why.
 *   - web requests, which is the control panel. That is where the edits go.
 *     Note what decides this: Yii reads getIsConsoleRequest() as
 *     PHP_SAPI === 'cli', so under php-fpm this module registers no handlers at
 *     all and a control panel save never reaches the check below.
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
    /** @var int[] collections unfrozen in this process, for the closing line */
    private array $_unfroze = [];

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

        /* The frozen list is read once and then kept, because a script does not
           freeze anything mid-run. Anything that could make it stale drops it:
           a collection saved (its switch may have moved) or a piece whose
           partOfCollection changed (it may have joined a frozen collection).
           Without this, freezing inside a running process was not seen, and a
           member of the collection just frozen could still be written. */
        Event::on(Entry::class, Element::EVENT_AFTER_SAVE, function (ModelEvent $e) {
            /** @var Entry $entry */
            $entry = $e->sender;
            if ($entry->getIsRevision() || $entry->getIsDraft()) { return; }
            if ($entry->getSection()?->handle === 'collections'
                || in_array('partOfCollection', $entry->getDirtyFields(), true)) {
                $this->_frozen = null;
                $this->_members = null;
            }
        });

        register_shutdown_function(function () {
            if ($this->_unfroze) {
                echo 'FROZEN: unfroze collection(s) #' . implode(', #', array_unique($this->_unfroze))
                   . '. Scripts can write to them again.' . PHP_EOL;
            }
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

        /* Revisions and drafts are copies of a save this guard has already
           judged, so they are never judged again. Craft makes a revision by
           duplicating the entry, and refusing that duplicate does not stop
           anything: it throws InvalidElementException out of the middle of a
           save that was allowed. That is what made the freeze one-way, because
           the unfreeze itself died on its own revision. */
        if ($entry->getIsRevision() || $entry->getIsDraft()) { return; }

        $this->_load();
        if (!$this->_frozen) { return; }

        /* Unfreezing is always allowed, or the switch is a one-way door.
           Only the unfreeze itself: a save that clears the switch and changes
           something else in the same breath is still refused, and says so. */
        if ($verb === 'save' && $this->_clearsFrozen($entry)) {
            $other = $this->_otherChanges($entry);
            if (!$other) {
                $this->_unfroze[] = (int)$entry->getCanonicalId();
                return;
            }
            $e->isValid = false;
            $msg = 'refused save of #' . $entry->getCanonicalId() . ' "' . $entry->title
                 . '": unfreezing is allowed, but not together with other changes ('
                 . implode(', ', $other) . '). Unfreeze on its own first.';
            $entry->addError('collectionFrozen', $msg);
            $this->_refused[] = $msg;
            fwrite(STDERR, 'FROZEN ' . $msg . PHP_EOL);
            return;
        }

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
             . '. Unfreeze it first: in the control panel, or with'
             . ' ddev craft exec \'$FREEZE_APPLY = true; $FREEZE_SLUG = "<slug>"; $FREEZE_ON = false;'
             . ' eval(file_get_contents("scripts/import/set_collection_frozen.php"));\'';
        $entry->addError('collectionFrozen', $msg);
        $this->_refused[] = $msg;
        fwrite(STDERR, 'FROZEN ' . $msg . PHP_EOL);
    }

    /**
     * Is this save clearing collectionFrozen on a collection that is frozen?
     */
    private function _clearsFrozen(Entry $entry): bool
    {
        $id = $entry->getCanonicalId();
        if (!$id || !in_array($id, $this->_frozen, true)) { return false; }
        if (!$entry->getFieldLayout()?->getFieldByHandle('collectionFrozen')) { return false; }
        return !$entry->getFieldValue('collectionFrozen');
    }

    /**
     * What else this save changes, beside the switch. Empty means the unfreeze
     * is the whole of it.
     *
     * @return string[]
     */
    private function _otherChanges(Entry $entry): array
    {
        $fields = array_values(array_diff($entry->getDirtyFields(), ['collectionFrozen']));
        $attrs = array_values(array_diff($entry->getDirtyAttributes(), ['dateUpdated']));
        return array_merge($fields, $attrs);
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
