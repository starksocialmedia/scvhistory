# Why "saved: 5" saved nothing

2026-09-20. `add_footnote_source_column.php` reported `saved: 5` on every run and
`rows already carrying a source: 0` on the next. The rows were never written.

## The cause

A Craft Table field stores its rows **keyed by column** — `col1`, `col2`, `col3`.
When you read a row back off an element it comes with **both** keys for the same
cell:

```json
{"col1":"1","col2":"the note text","col3":null,
 "number":"1","note":"the note text","source":null}
```

The script read that row, set `$r['source'] = 'editor'` — the **handle** — and
saved. Craft's normalisation prefers the **column** key, and `col3` was still
`null`, so the value was discarded at serialisation. `saveElement()` returned
true because the save genuinely succeeded; it just saved a null.

## Proved, without writing anything

`serializeValue()` gives the exact JSON Craft would put in the content column.
Run on `chapter-5-tribal-relics`:

| | |
|---|---|
| **stored now** | `[{"col1":"1","col3":null},{"col1":"2","col3":null},{"col1":"3","col3":null}]` |
| **what the old script produces** | `[{"col1":"1","col3":null},{"col1":"2","col3":null},{"col1":"3","col3":null}]` |
| **what the fixed script produces** | `[{"col1":"1","col3":"editor"},{"col1":"2","col3":"editor"},{"col1":"3","col3":"editor"}]` |

The database was not touched to get this.

## The fix

The script now looks the column key up from the field definition rather than
assuming it, reduces every row to column keys before saving, and reads either
key when testing whether a value is already there. It reports `the source column
is stored as col3` so the mapping is visible in the output.

**The rule, stated once:** a Table row keyed by handle alone is safe, because
there is no column key to lose to. A row that carries both is dangerous when the
column key is empty. Rows read off an element always carry both. So anything
that reads rows, changes them and writes them back must write column keys.

`import_ruiz_census.php` builds its rows from scratch, keyed by handle only, and
was never at risk. It is checked anyway.

## Read-back verification, everywhere

Every script that writes now reads its own writes back from a freshly loaded
element, counts them, and stops with `THE WRITE DID NOT PERSIST` and a per-record
reason when the count does not match. Nine scripts:

| script | what it verifies |
|---|---|
| `add_footnote_source_column.php` | every row carries a source |
| `apply_extracted_captions.php` | every asset carries a caption |
| `convert_mfn_footnotes.php` | rows written, and the shortcode gone from the body |
| `convert_note_footnotes.php` | rows written, and the note field cleared |
| `reimport_photograph_bodies.php` | the stored body matches what was written |
| `link_images_to_records.php` | every planned relation is on its record |
| `attach_perkins_collection.php` | partOfCollection set, and the reading order holds |
| `import_ruiz_census.php` | row count and filled map numbers |
| `merge_rudy.php` | **every moved field reads back before the other record is deleted** |

`merge_rudy.php` is the one that mattered most: it moved fields to one record and
then deleted the other. A save that reported success and stored nothing would
have destroyed the only copy. It now refuses to delete if the read-back fails.

## What this does not change

Nothing has been applied. The templates read an empty source as `editor`, which
is what the backfill writes, so chapter 5 renders Editor's Notes correctly either
way. The backfill only fills the Source cell in the control panel.
