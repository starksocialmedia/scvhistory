---
name: scv-review-screen
description: The export, review, apply pattern used for adjudicating data in the SCVHistory build. Use when a task needs a human decision on many records, such as confirming dates, merging entities or approving relations.
---

# Export, review, apply

When a task needs Nathan's judgement on many records, do not build a confirmation
interface inside Craft and do not guess. Build three pieces. The working examples
are the dates trio and the entities trio:

- `scripts/import/export_unconfirmed_dates.php` and `export_entity_candidates.php`
- `web/review/dates.html` and `web/review/entities.html`
- `scripts/import/apply_confirmed_dates.php` and `apply_entity_merges.php`

Read the matching trio before writing a new one and follow its shape.

## 1. The export script

Read only. It never writes to the database, and the header comment says so.

```php
/**
 * Exports X to web/review/NAME.json for the review screen. Read only.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/export_NAME.php'))"
 */
```

Walk the sections, guard every field read against the element's own field layout,
and emit a flat array of rows. Each row carries what the screen needs to show and
what the apply script needs to find the record again: `entryId`, a row index
where the target is a table row, `section`, `slug`, `title`, `url`, and the
values under review. Write to `\Craft::getAlias('@webroot') . '/review/NAME.json'`.

The HTML is committed, the data is not. `.gitignore` names the data files one
by one, so add each new export and download filename to it; a new one is not
covered by a wildcard.

## 2. The review screen

One standalone HTML file in `web/review/`. No build step, no framework, no
network beyond fetching its own JSON. Served at
`https://scvhistory.ddev.site/review/NAME.html`.

- `fetch('NAME.json')` on load
- decisions held in `localStorage` under one key, saved on every change, so a
  closed tab loses nothing
- keyboard driven, and the shortcuts are printed on the page: C confirm,
  D delete, S skip, Tab next
- a Download button that serialises the decisions to a Blob and saves
  `confirmed.json` or `merged.json`
- show the record's title and a link to its page, so a judgement call can be
  checked against the record

Nothing is written back from the browser. The download is the handoff.

## 3. The apply script

Reads the downloaded file out of `web/review/`, not the export.

```php
$file = \Craft::getAlias('@webroot') . '/review/confirmed.json';
if (!file_exists($file)) { echo 'ERROR: not found. Download it from the review screen first.' . PHP_EOL; return; }
```

Dry run by default with `$APPLY`. Group the decisions by entry so each record is
saved once. Re-read the live row before writing, because the export may be stale.
Save through the element API: Craft 5 keeps relation targets in both the
`relations` table and the content JSON, so raw SQL looks right and does nothing.
Report every decision that no longer matches the data rather than forcing it.

## What the three together guarantee

The database is never written from a browser. Every write goes through a script
that can be dry run and read first. The screen holds no state that matters, so it
can be reloaded or rebuilt at any time. And the export can be re-run after an
apply to see what is left.

Say in the report how many rows were exported, how many decisions came back, and
how many applied cleanly.
