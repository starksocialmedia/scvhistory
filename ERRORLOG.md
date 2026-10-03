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
| 2026-10-03 | Mirror searches from the agent shell | `grep` in the agent's shell is a function wrapping `ugrep -I`, which skips the mirror's ISO-8859-1 pages as binary, silently. "Connie Worden" found 50 files with it and 207 with the system grep; "Christy Smith" 4 against 6; "Bennett-Arcan" 4 against 8. Earlier mirror sweeps run with plain `grep` may have missed pages. | Search the mirror with `LC_ALL=C /usr/bin/grep -rlia`, or Python reading as latin-1. |
| 2026-10-03 | Mirror search for Bennett-Arcan | `timeout 600 grep ... 2>/dev/null` returned nothing: macOS has no `timeout`, and the redirect hid "command not found", so the search looked empty. The party was nearly retired on it. | No `timeout` on macOS; never send stderr to /dev/null on a search whose emptiness decides anything. |
| 2026-10-03 | build_trunkey_profile.php, build_smith_profile.php apply | `recordProvenance` holds at most 255 characters; the appended note overflowed and the save failed. Both transactions rolled back; nothing was half-written. | Shorter notes; profile scripts now refuse in the dry run if the provenance would pass 255. |
| 2026-10-03 | Portrait imports, Smith's footnotes | Copied from the older Ahuja pattern: a checksum and received file name in the public source sentence, and repository paths in five footnotes. check_render's wording checks failed. | fix_portrait_provenance_2026_10_03.php and fix_smith_note_paths.php; checksums in sourceChecksum. Copy from the newest script of a kind, not the first. |

## Patterns to Avoid
- Never use `php craft eval`
- Never use `Craft::$app->sections` in Craft 5
- Never edit config files via CLI append/cat
- Never assume a file exists unless confirmed
- Never set `Entry->title` when `hasTitleField` is false and `titleFormat` is null; Craft will store an empty title
- Never search the mirror with the shell's `grep`: it skips latin-1 pages. Use `LC_ALL=C /usr/bin/grep -a`
- Never hide stderr on a command whose empty result decides something
