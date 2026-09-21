<?php
/**
 * Stores review decisions server side, one write per click.
 *
 * KEYED BY NAME AND TYPE, NEVER BY QUEUE KEY
 *
 * The queue key is derived from the name and moves whenever the name canon
 * folds something or a regeneration changes a grouping. A decision filed under
 * it becomes unreachable the next time the queue is built, which is how a
 * "same thing" marked on Monday is gone on Tuesday. The name a person saw, and
 * the type they chose, are what they actually decided, so that is the key.
 *
 * NOTHING HERE DELETES
 *
 * save upserts one decision. clear removes ONE entry by key and nothing else.
 * There is no action that empties the file, because there is no reason for a
 * program to empty it, and every reason for it not to be able to.
 *
 * Writes are atomic: the file is written beside itself and renamed, under a
 * lock, so a click that lands mid-write cannot truncate somebody's afternoon.
 */

namespace modules\reviewstore\controllers;

use Craft;
use craft\web\Controller;
use yii\web\Response;

class DecisionsController extends Controller
{
    protected array|int|bool $allowAnonymous = true;

    private function path(string $set = ''): string
    {
        $name = $set === 'pairs' ? 'records-pairs-decided.json' : 'records-decided.json';
        return Craft::getAlias('@webroot') . '/review/' . $name;
    }

    private static function keyFor(string $name, string $type): string
    {
        $n = mb_strtolower(trim($name));
        $n = preg_replace('~[^a-z0-9 ]~u', ' ', $n);
        $n = trim(preg_replace('~\s+~', ' ', $n));
        return $n . '|' . mb_strtolower(trim($type));
    }

    private function read(string $set = ''): array
    {
        $p = $this->path($set);
        if (!file_exists($p)) {
            return ['generated' => null, 'decisions' => []];
        }
        $d = json_decode(file_get_contents($p), true);
        return is_array($d) ? $d : ['generated' => null, 'decisions' => []];
    }

    private function write(array $doc, string $set = ''): bool
    {
        $p = $this->path($set);
        $tmp = $p . '.tmp';
        $doc['generated'] = (new \DateTime())->format('c');
        $json = json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($json === false) { return false; }
        if (file_put_contents($tmp, $json . "\n", LOCK_EX) === false) { return false; }
        return rename($tmp, $p);
    }

    /** GET: everything on file, for the screen to apply on load. */
    public function actionAll(): Response
    {
        $set = (string)$this->request->getParam('set', '');
        $doc = $this->read($set);
        $out = [];
        foreach (($doc['decisions'] ?? []) as $x) {
            $k = $x['storeKey'] ?? self::keyFor((string)($x['name'] ?? ''), (string)($x['type'] ?? ''));
            $out[$k] = $x;
        }
        return $this->asJson(['ok' => true, 'count' => count($out), 'decisions' => $out]);
    }

    /** POST: upsert one decision. */
    public function actionSave(): Response
    {
        $this->requirePostRequest();
        $set = (string)$this->request->getBodyParam('set', '');
        $dec = $this->request->getBodyParam('decision');
        if (!is_array($dec) || trim((string)($dec['name'] ?? '')) === '') {
            return $this->asJson(['ok' => false, 'error' => 'a decision needs a name']);
        }

        $key = self::keyFor((string)$dec['name'], (string)($dec['type'] ?? ''));
        $dec['storeKey'] = $key;
        $dec['decidedAt'] = (new \DateTime())->format('c');

        $doc = $this->read($set);
        $rows = $doc['decisions'] ?? [];
        $found = false;
        foreach ($rows as $i => $x) {
            $xk = $x['storeKey'] ?? self::keyFor((string)($x['name'] ?? ''), (string)($x['type'] ?? ''));
            if ($xk === $key) { $rows[$i] = $dec; $found = true; break; }
        }
        if (!$found) { $rows[] = $dec; }
        $doc['decisions'] = array_values($rows);

        if (!$this->write($doc, $set)) {
            return $this->asJson(['ok' => false, 'error' => 'could not write the decisions file']);
        }
        return $this->asJson(['ok' => true, 'key' => $key, 'total' => count($rows), 'added' => !$found]);
    }

    /** POST: remove ONE decision, when a reviewer un-clicks it. */
    public function actionClear(): Response
    {
        $this->requirePostRequest();
        $set = (string)$this->request->getBodyParam('set', '');
        $name = (string)$this->request->getBodyParam('name', '');
        $type = (string)$this->request->getBodyParam('type', '');
        if (trim($name) === '') {
            return $this->asJson(['ok' => false, 'error' => 'a name is required']);
        }
        $key = self::keyFor($name, $type);

        $doc = $this->read($set);
        $before = count($doc['decisions'] ?? []);
        $doc['decisions'] = array_values(array_filter($doc['decisions'] ?? [],
            fn($x) => ($x['storeKey'] ?? self::keyFor((string)($x['name'] ?? ''), (string)($x['type'] ?? ''))) !== $key));

        if (!$this->write($doc, $set)) {
            return $this->asJson(['ok' => false, 'error' => 'could not write the decisions file']);
        }
        return $this->asJson(['ok' => true, 'removed' => $before - count($doc['decisions'])]);
    }
}
