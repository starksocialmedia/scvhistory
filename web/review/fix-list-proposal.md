# The fix list: why a section rather than a field

The ask was `editorNote` on every record. A field is the wrong shape, and the
fourth reason below is the one that decides it.

1. **It cannot hold a note about a record that does not exist.** "There should be
   a record for the Ruiz-Perea census", "this legacy page was never imported",
   "the articles index sorts wrong". Those are the commonest things to notice
   while reading and none of them has a record to hang off. 5,606 of the 5,791
   legacy pages have no record at all, so the majority of what there is to say
   is about something a field cannot reach.
2. **It holds one note.** The second thing you notice overwrites the first, or is
   appended to it and the two then share a single done flag.
3. **A done flag needs a second field**, and marking something done means editing
   the record the note is about, which is the thing you were trying to avoid.
4. **A field is edited in the control panel.** Five seconds while reading means
   writing from the page you are on. A field cannot be reached from there without
   leaving it.

## The shape

One entry per note, in a `fixes` channel with no URLs.

| field | |
|---|---|
| `fixNote` | what is wrong, in whatever words. Required. |
| `fixStatus` | open, done, not a problem |
| `fixRecord` | the record it is about, if there is one. Optional. |
| `fixLegacyUrl` | the legacy page it is about, if there is no record. Optional. |
| `fixSeenOn` | the URL you were reading. Filled in for you. |

No title field: the title is generated from the note, so there is nothing to
fill in but the note.

`fixRecord` and `fixLegacyUrl` are both optional and a note may have neither. A
note about nothing in particular is still a note.

## The five seconds

A **Note a fix** button sits in the bottom right of every page, for a logged-in
admin and nobody else. It opens a textarea. Typing a line and pressing Save
posts to Craft's own `entries/save-entry` action and returns you to the page you
were reading. The record you were looking at is attached automatically where
there is one, along with its legacy URL and the page you were on.

No module, no endpoint of our own, no JavaScript beyond opening the panel.

## What this does not solve

**Notes made on production are lost.** Runbook §3: content moves one way, and a
content refresh is a whole-database import from local that overwrites
everything. A note written while browsing staging is content.

Three ways out, and the choice is yours:

- **Take notes locally.** Simplest, and wrong: browsing staging is exactly when
  you notice things.
- **Export the fixes before a refresh.** A small script dumps the section to JSON
  and reads it back afterwards. The section is tiny, so this is cheap, but it is
  a step somebody has to remember.
- **Make fixes the one thing that flows up.** Honest, and it makes the rule
  "content does not move" have an exception, which is how rules stop being
  followed.

The second is what I would do, and the script is fifteen minutes' work once you
say so. Until then the fix list is reliable locally and lossy on staging, and
that should be written on the page rather than discovered.

## What is not tested

The capture form has not been exercised end to end, because that needs the
section to exist and creating it is a project config change that is yours to
run. On first use, check: the note saves, the redirect returns you to the page
you were on, and the record is attached. If the save fails it will be the
permissions on the front-end action, which is the usual cause.
