---
name: scv-import-script
description: How to write an import, migration or backfill script for the SCVHistory Craft build. Use whenever adding a file to scripts/import/, or changing Craft data from the command line.
---

# Import scripts in this repo

Every script in `scripts/import/` is an eval-style fragment. No opening `<?php`
tag. Run with:

```
ddev craft exec "eval(file_get_contents('scripts/import/NAME.php'))"
```

Paths inside the container are relative to the repo root, or use
`\Craft::getAlias('@root')`. A path outside the repo is not visible to DDEV.

## The shape

```php
/**
 * One paragraph on what this does and why.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/NAME.php'))"
 */

$APPLY = false;
```

Then: load and validate input, plan, print the plan, write only under `$APPLY`,
print a summary. Nathan reads the dry run before applying. He runs the script,
not you.

## Rules that are not negotiable

**`$APPLY` is false in the committed file. Always.** A script is run by flipping
the flag locally; the flip is never committed. A second flag such as
`$OVERWRITE` may exist, also false.

Immediately under the flag, every script carries this line, so an accidental
write shows up in the first line of output rather than being discovered after
the fact:

```php
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
```

**Before running any import script, check the flag. Do not assume it is false.**
A `true` left in a committed file has already caused one unintended write: a run
meant as a preview wrote to 8 records before anyone had read the report. Check
with `grep -n '^\$APPLY' scripts/import/NAME.php`, and when a script you did not
write reports `APPLYING`, stop and say so rather than carrying on.

**Report the whole plan before writing.** One line per record showing what would
change, then a summary with counts by category. A number with no list behind it
is not a report.

**Idempotent.** A second run must be a no-op. Match on a stable key, skip what is
already done, and skip the save entirely when there is nothing to set, so
`dateUpdated` does not move. Say so in the closing line of the report.

**Guard every custom field read against the element's own field layout.**

```php
$has = [];
foreach ($el->getFieldLayout()->getCustomFields() as $f) { $has[$f->handle] = true; }
if (isset($has['recordDates'])) { $v = $el->getFieldValue('recordDates'); }
```

`isset($el->someHandle)` and `$el->someHandle is defined` both lie: they return
true for a field the entry type does not have, then Craft throws
`Calling unknown method` when the value is read. This has broken the site four
times. Wrap the read in try/catch as well.

**Never overwrite a non-empty field** unless a flag explicitly says to. Fill gaps
only. An empty incoming value never overwrites anything, because under
GROK-CONTRACT.md an empty string means "not found", not "blank".

**Never invent a value.** No path guessed from a slug, no date inferred, no
linked-data URI made up. If the evidence is not there, report the record and
move on. "Nothing was invented for them" belongs in the summary.

**Report anything unmatched.** Communities with no matching term, labels with no
field, pages with no record, URLs that 404. List them with their keys so Nathan
can act. Never drop a row silently.

## Traps that have cost time here

- Craft 5 stores relation targets in both the `relations` table and the
  `elements_sites` content JSON. Raw SQL updates look right and do nothing. Save
  through the element API.
- A table field's date column stores `Y-m-d 00:00:00` and reads back one day
  earlier in `America/Los_Angeles`. `build_calendar_index.php` converts to UTC to
  compensate. Match the existing convention rather than "fixing" it.
- `legacy_key` is not unique across inventories: `part06` exists in both the
  Perkins and the Reynolds files. Match on `legacyUrl` derived from the source
  URL.
- An absolute legacy URL on another host cannot become a root-relative path.
  Strip the host only for scvhistory.com and www.scvhistory.com.
- Verify every field handle against `config/project/` before using it.

## Field creation

Creating a field or adding one to a layout follows `add_collection_parts.php`:
check `getFieldByHandle` first, create only when missing, check the layout for
the handle before appending a `CustomField` to `$tabs[0]`, and save the entry
type. Still `$APPLY`-gated.

## Verify before handing over

Test writes with a temporary fixture record, read it back, then hard-delete it
with `deleteElement($el, true)` and confirm it is gone. Do not test by applying
to real records. If you do apply something, snapshot first and restore, and say
so in the report.

## When you are done

Add a dated entry to `CHANGELOG.md`: agent, date, done, decisions, blockers,
next. Commit only the files you changed; never `git add -A`. Other agents commit
to this repo at the same time.
