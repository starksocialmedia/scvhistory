# SCVHistory.com — Error Log

## Open Errors

| Date | Context | Error | Status |
|---|---|---|---|
| 2026-09-16 | local DDEV Places/Collections import | 52 entries (IDs 583-685) saved with empty titles. `import_places_and_series.php` set `Entry->title` but Place and Collection have `hasTitleField: false` and `titleFormat: null`, so Craft discarded the title. | Open. Do not import more of those types until the title field is on. Plan in TODO.md Waiting on Nathan. |

## Resolved Errors

| Date | Context | Error | Resolution |
|---|---|---|---|
| 2026-04-14 | DDEV/Cloudways | `php craft eval` unknown command | Use `php craft exec` |
| 2026-04-14 | DDEV field lookup | `craft\console\Application::sections` unknown property | Use `Craft::$app->entries` in Craft 5 |
| 2025 | WordPress | functions.php corruption | Never edit config via CLI append/cat |

## Patterns to Avoid
- Never use `php craft eval`
- Never use `Craft::$app->sections` in Craft 5
- Never edit config files via CLI append/cat
- Never assume a file exists unless confirmed
- Never set `Entry->title` when `hasTitleField` is false and `titleFormat` is null; Craft will store an empty title
