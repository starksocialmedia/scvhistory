# On This Day, 2 October 2026

## Why it is empty

On This Day reads `templates/_data/calendar.json`. That file is built by `build_calendar_index.php` from one source only: rows in the `recordDates` table field that a person has ticked Confirmed. Nobody has ticked any, so the calendar has no days and the page is empty on every date. The page has never had content, and the 3 September "bug" was a cached copy of an empty page.

## What confirming a date involves

1. `propose_record_dates.php` reads every record's body and finds dates in the prose by pattern ("March 16, 1959", "16 March 1959", "March 1959", "c. 1900", "in 1907"). It writes each as an unconfirmed row: the date as printed, an ISO date, a precision (day, month, year or circa), and the sentence around it as the label.
2. `export_unconfirmed_dates.php` writes the queue to `web/review/dates.json` for the review screen at `web/review/dates.html`.
3. A person reads each row there and ticks Confirmed when the date is right and is something that happened on that day.
4. `apply_confirmed_dates.php` writes the ticks back, and `build_calendar_index.php` rebuilds the calendar.

The screen and the scripts were built on 17 September. Step 3 has never been done.

## What the queue holds

The 432 was the count on 18 September. Imports since then (Reynolds, the LW features, the profiles) have added rows, and the queue is now **2,101 rows, 2 of them confirmed**.

| Precision | Rows | Can go on a calendar day |
|---|---|---|
| day | 590 | yes |
| month | 98 | no, there is no day |
| year | 1,372 | no |
| circa | 41 | no |

Only the 590 day rows matter for On This Day. The other 1,511 can never appear there, whatever is decided about them. By section: 1,488 rows are in articles, 255 in war memorials, 218 in persons, and the rest are scattered.

The 590 day rows, sorted:

| Kind | Rows | Proposal |
|---|---|---|
| The same date the record already holds in a structured field (birth, death, event, founding, publication) | 43 | **Confirm automatically.** The record already asserts the date. |
| Newspaper datelines and cutlines ("The Newhall Signal \| Thursday, July 19, 1945") | 43 | **Reject automatically.** These record when a paper ran a story, not when something happened. |
| Fragments of lists ("Cityhood Application 12/17/1985 Boundary Map 1/2/1986") | 5 | **Reject automatically.** |
| Births in prose | 106 | Decision, quick. Living people are excluded from the calendar whatever is decided. |
| Deaths in prose | 60 | Decision, quick. |
| Other events in prose | 324 | Decision: is it an event, and is the date its date? |

That leaves **490 decisions**. At about five seconds a row on the review screen, that is under an hour.

## The bigger fix: the calendar should not depend on the queue alone

The archive already holds exact dates in structured fields that were set from sources when the records were built. None of them reach On This Day, because the builder reads only `recordDates`.

| Source | Dated items | Calendar days covered |
|---|---|---|
| Events, elections, deaths, foundings and dissolutions, office terms, photographs, and births of historical people | 445 | 157 of 366 |
| ...plus war memorial deaths, once the 36 loosely written dates ("4-27-1919") are read as dates | 481 | 175 |
| ...plus every prose day row, if all 490 decisions were yes (the ceiling) | | 283 |
| Article publication dates (575), as a separate "published on this day" line | 1,033 | 333 |

## Proposal

1. **Teach the builder to read the structured fields** listed above as well as confirmed rows, for historical people only (the `historical.twig` test). The page then shows something on about half the days of the year. This needs no review: each date is one the record already asserts.
2. **Read the 36 war memorial death dates** into `deathDateEdtf`. Each date is read as it is written, and anything ambiguous is listed for you.
3. **Run the automatic confirmations and rejections**: confirm 43, reject 48.
4. **Put the 490 remaining rows on the review screen in order of need**: first the rows that would fill a day with nothing, then the rest.
5. **A decision for you:** should article publication dates appear, as a second line such as "From the archive: published on this day"? They raise coverage from about 175 days to 333. They are dates of writing, not of events, so they should never be mixed into the main list.

Rows for living people's births are never put on the calendar.
