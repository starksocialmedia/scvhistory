# Legacy extraction contract

Grok reads scvhistory.com and produces JSON. Grok does not write to the database,
does not edit templates, and does not decide what anything is. Claude Code reads
these files and imports. Nathan adjudicates anything flagged.

## Absolute rules

1. **Never invent.** A fact you cannot find is an empty string. No inferred dates,
   no reconstructed names, no guessed coordinates, no summarizing. If a page is
   ambiguous, record what it says and flag it.
2. **Verbatim text.** Body text is copied exactly, including typos, bracketed
   editorial notes, and odd spacing. Do not clean, modernize, or reflow. Cleanup
   is a separate reviewed pass.
3. **Provenance on every record.** `source_url` and `crawled` (ISO date) are
   required on every object. A fact without a source does not ship.
4. **No judgment calls.** You report counts and whether a legacy page exists.
   The promotion rules below decide what becomes a record. Anything that does not
   fit the rules goes in `needs_review` with a reason.
5. **Do not download images.** Record the `src` attribute exactly as written in the
   HTML, relative path and all. The image files come from the drive and are matched
   on that path.
6. **Dates are raw.** Record the date text exactly as printed and where it appeared.
   Do not normalize, reformat, or interpret. "c. 1839", "the following spring" and
   "March 9, 1842" are all recorded as written.
7. **One file per section**, committed to `inventory/legacy/`. Never a branch.

8. **`date_raw` is the printed dateline.** Take it from the masthead or byline block
   at the head of the article, exactly as printed (for example a Signal pipe-date
   line). If a page carries two dates in that block, record both in `date_raw` as
   printed rather than choosing one. Do not leave `date_raw` empty when the dateline
   is present in that head block even if the same text also appears in `body_text`.
9. **`editor_notes` record Leon/webmaster notes as set-off blocks.** These are
   typically italic or bracketed blocks (including `Webmaster's note` / `Webmaster's
   Notes` and inline `[sic: …]` corrections). Set `position` to `top`, `bottom`, or
   `inline` by where the block sits relative to the article prose. Capture notes that
   appear inside the body as well as above or below it; leave the note text in
   `body_text` unchanged when it occurs there.

## Wave 0 scope

The Perkins series, "Story of Our Valley", published in The Signal 1954 to 1962.
Start from the series index on scvhistory.com. Nothing else in this wave.

Output: `inventory/legacy/perkins.json`

## Schema

```json
{
  "meta": {
    "section": "perkins",
    "crawled": "2026-09-18",
    "index_url": "https://scvhistory.com/scvhistory/...",
    "page_count": 0,
    "crawler_notes": ""
  },
  "pages": [
    {
      "source_url": "https://scvhistory.com/scvhistory/sv0101.htm",
      "legacy_path": "/scvhistory/sv0101.htm",
      "legacy_key": "sv0101",
      "title": "",
      "subtitle": "",
      "byline_raw": "",
      "date_raw": "",
      "series_position": 1,
      "body_text": "",
      "body_html": "",
      "editor_notes": [
        { "position": "top", "text": "" }
      ],
      "images": [
        {
          "src_raw": "../gif/mugs/example.jpg",
          "alt": "",
          "caption": "",
          "credit_raw": "",
          "position_in_body": 3,
          "links_to": ""
        }
      ],
      "links_out": [
        { "href_raw": "", "anchor_text": "", "is_internal": true, "role": "related_reading" }
      ],
      "related_block_header_raw": "",
      "dates_mentioned": [
        { "text_raw": "March 9, 1842", "context": "sentence it appeared in" }
      ],
      "people_mentioned": [
        { "name_raw": "Henry Mayo Newhall", "count": 2, "linked_to": "" }
      ],
      "places_mentioned": [
        { "name_raw": "Beale's Cut", "count": 1, "linked_to": "" }
      ],
      "orgs_mentioned": [
        { "name_raw": "Southern Pacific Railroad", "count": 3, "linked_to": "" }
      ],
      "communities_mentioned": ["Newhall", "Saugus"],
      "fine_print_raw": "",
      "footnotes": [
        {
          "number": "1",
          "text": "",
          "resolution": "same_page|shared_notes_page|orphan",
          "notes_page_url": ""
        }
      ],
      "footnotes_notes_page_url": "",
      "needs_review": [
        { "reason": "", "detail": "" }
      ]
    }
  ],
  "entity_index": {
    "people": [
      {
        "name_raw": "",
        "name_variants": [],
        "mention_count": 0,
        "pages": [],
        "has_legacy_page": false,
        "legacy_page_url": ""
      }
    ],
    "places": [],
    "organizations": []
  }
}
```

Empty string for missing text. Empty array for missing lists. `false` for unknown
booleans only where the schema says boolean. Never `null`, never "unknown", never
a placeholder.

## Promotion rules

These decide what becomes a record. Apply them mechanically; do not interpret.

- Has its own page on scvhistory.com → gets a record. Always. Leon already decided.
- No page, named on three or more distinct pages → gets a record.
- Named on one or two pages → stays a tag.
- Named only inside a list, caption, roster, or index page → tag only.

Report the counts in `entity_index`. Do not create records. Do not mark anything
as promoted. Claude Code applies the rules at import.

## Name variants

When the same person appears as "H.M. Newhall", "Henry Mayo Newhall" and "Newhall",
record each spelling in `name_variants` under one entry and count them together only
when the page makes the identity explicit. When it does not, record them separately
and add a `needs_review` entry saying they may be the same person. Do not merge on
your own judgment.

## Communities

The community list is closed. Use only these names, exactly as spelled:

Acton, Agua Dulce, Bouquet Canyon, Camulos, Canyon Country, Castaic,
Castaic Junction, Fair Oaks Ranch, Fillmore, Frazier Park, Haskell Canyon,
Hasley Canyon, Lake Hughes, Lebec, Mentryville, Mint Canyon, Mojave Desert,
Newhall, Pico Canyon, Piru, Placerita Canyon, Potrero Canyon, Ravenna,
San Francisquito Canyon, Sand Canyon, Santa Clarita, Saugus, Saugus-Valencia,
Soledad Canyon, Soledad Township, Stevenson Ranch, Tejon, Towsley Canyon,
Val Verde, Valencia

A place name that is not on this list is a place, not a community. Do not add to
this list. If a page names an area that seems like a community and is not listed,
put it in `needs_review`.

A place or organization's name never implies its community. Saugus Cafe is in
Valencia; it is named for the Saugus rail station, not its location. Newhall Land
and Farming was headquartered in Valencia. Castaic Lake is in Castaic but Castaic
Junction is a separate community, and several canyons carry names that belong to
other communities entirely.

Never infer `community_mentions` from a name. Record a community only where the
text places the subject there (as a place the prose locates something in or talks
about as a community). Where the text does not, leave `communities_mentioned`
empty rather than guessing. A community word that appears only as part of a longer
proper name (Saugus Cafe, Newhall Land, Newhall Pass, Castaic Creek, Piru Creek,
Tejon Ranch, Hyatt Valencia, and the like) is not a community mention. Prefer the
longest closed-list community match; do not also record a shorter community that
is only a prefix of a longer one on the list (e.g. do not add Castaic solely
because the text says Castaic Junction).
When a closed-list community word appears only as part of a longer place or
organization name, do not put it in `communities_mentioned`. Record it instead
in `community_inferred` with: the short community name, the longer name it was
drawn from, a count, and the pages. A human confirms any that are correct by
coincidence. Do not drop these hits.


## Crawl conduct

One request at a time, one second apart, with a descriptive User-Agent naming the
project and a contact address. Respect robots.txt. This is Leon's live site and it
must not be strained.

## Definition of done for wave 0

- `inventory/legacy/perkins.json` validates against the schema above
- every page in the series index appears in `pages`
- `entity_index` counts reconcile with the per-page mention arrays
- a short report: page count, entity counts, how many entities each promotion rule
  would select, and every distinct reason appearing in `needs_review`

## Footnotes (`footnotes`, `footnotes_notes_page_url`)

Recoverable footnote markers and note text. Do not invent note text. Keep
`body_text` markers exactly as printed (do not strip superscripts).

Resolutions:

- `same_page` — note text recovered from a NOTES / Notes / `#notes` block on the
  same page (e.g. Perkins RSF; lw2045 / lw2045b / lw2104). Omit
  `notes_page_url` on the footnote object or leave it empty. Set page-level
  `footnotes_notes_page_url` to `""`.
- `shared_notes_page` — chapter markers link to a sibling `notes.html#N`
  (Reynolds and Perkins series). Attach only notes the chapter references.
  Set `notes_page_url` on each footnote and page-level
  `footnotes_notes_page_url` to the absolute `https://` notes page URL.
- `orphan` — a `<sup>[n]</sup>` (or similar) marker with no recoverable note
  text on the page and no `notes.html` link. Record
  `{ "number": "n", "text": "", "resolution": "orphan" }`. Never invent text.

```json
"footnotes": [
  { "number": "1", "text": "", "resolution": "same_page|shared_notes_page|orphan", "notes_page_url": "" }
],
"footnotes_notes_page_url": ""
```

## Related-reading sidebars (`links_out`)

Many legacy pages carry a narrow right-hand column headed with `inversecaption` / `thumbcaption` (Leon's related-reading apparatus: diaries, companion stories, index galleries). Those links belong in `links_out` even when they sit outside the main prose column. Extractors that narrow to the main column must still merge them. Tag merged sidebar links with `"role": "related_reading"` and, when present, record the sidebar heading in `related_block_header_raw`. Do not treat site chrome (NEXT/PREVIOUS/PHOTO CREDITS/BIBLIOGRAPHY) as related reading.

## Living people

**Do not build a structured graph of living people.** Every date cutoff is only a
proxy for this. Where a date doesn't settle it, apply the principle: if the text
doesn't establish the named person is deceased, the relationship is not structured.
It stays only as prose in `body_text`. The deceased subject's own facts are
unaffected.

## Where obituary data lives

**Obituary data lives in the private repo `starksocialmedia/scvhistory-data`; never
commit obituary data here.** That covers `obituaries.json` and the three
`obituaries_*_report.json` files, now at `obituaries/` in the data repo, and any
checkpoint, log or excerpt of them. `.gitignore` blocks `inventory/legacy/obituaries*`
and `inventory/private/`. The scripts stay in this repo and hold no personal data:
`crawl_obituaries.py`, `inventory/legacy/extract_relationships.py`,
`apply_living_rule.py`, `extract_funeral.py`. They locate the data through
`inventory/legacy/scv_data.py`: `$SCV_DATA_DIR` if set (relative paths resolve
against the repo root), else `../scvhistory-data` next to the repo root, else the
gitignored `inventory/private/`. Files are read and written in `<data dir>/obituaries/`.
`crawl_obituaries.py` does not commit; run the three scripts after a crawl, then
commit in the data repo. `extract_footnotes.py` does not process obituaries (its
report is committed here). Where this contract says `obituaries.json` or an
`obituaries_*_report.json`, it means the file in the data repo.

## Obituary relationships (`relationships`, obituaries.json)

Stated family relationships only, extracted from each obituary's own `body_text` by
`inventory/legacy/extract_relationships.py`. No identity resolution, no merging, no
linking to other pages or records.

Storage rule. A relationship is stored only if one of these holds, recorded in
`retention_basis`:

- `preceded_in_death`: the text lists the person in a "preceded in death/passing by"
  or "predeceased by" clause.
- `stated_deceased`: the obituary text itself states the named person is deceased
  ("the late X", "her late husband, X", "X (deceased)", "X, both deceased",
  "X, who died in 1990").
- `pre_cutoff`: the obituary's printed year (the latest four-digit year in
  `date_raw`; for a life-dates range this is the death year) is 72 or more years
  before the current year, i.e. 1954 or earlier in 2026. The cutoff rolls forward
  each year.

Undated obituaries qualify only as `preceded_in_death` or `stated_deceased`. A name
carrying a parenthetical ("Name (Spouse) Surname") is not stored unless the text
states that person is deceased, because the parenthetical usually names a living
spouse. Everything else is not stored: no sidecar file, and no names in reports.
`obituaries_relationships_report.json` is aggregate-only and records the cutoff year
used.

```json
"relationships": [
  { "person_raw": "", "relationship_raw": "", "subject_raw": "", "sentence_raw": "",
    "survival_raw": "preceded_in_death|stated|survived_by|", "retention_basis": "preceded_in_death|stated_deceased|pre_cutoff" }
]
```

- `subject_raw` is the obituary's subject, taken from the page title (with an
  `Obituary |`-style prefix, trailing life dates, and news-headline wording such as
  "Lifelong Newhall Resident ... Dies at 104" or "... Obituary & Death Certificate"
  removed). Some titles still leave extra words in it.
- `person_raw` is the other named person, as printed.
- `relationship_raw` is the kinship word as printed, including plural and case
  (`daughters`, `Son`). It is not normalized.
- `sentence_raw` is the sentence the relationship came from.

Direction depends on `survival_raw`:

- `preceded_in_death` / `survived_by` ("He was preceded in death by his wife, Mary"):
  `person_raw` is the subject's `relationship_raw` (Mary is the subject's wife).
- `stated` ("the son of the late X"): the phrase is "`relationship_raw` of
  `person_raw`", so the **subject** is the person's `relationship_raw` (the subject is
  X's son).

Known limitations: in lists like "daughter Sue and her husband Bob", the nested spouse
is recorded against the subject. Some `person_raw` values are fragments. A human checks
entries before any import treats them as facts.

## Obituary name index and other name-bearing fields

The living-people rule also governs every structured field that can carry a name on
an obituary, applied by `inventory/legacy/apply_living_rule.py` after
`extract_relationships.py` (counts in `obituaries_living_rule_report.json`,
aggregate-only). On an obituary printed after the cutoff year, or undated:

- `people_mentioned`: a name stays only if it is the obituary subject (matched to the
  title-derived subject) or passes the relationship storage rule (`preceded_in_death`,
  `stated_deceased`); the kept entry carries `retention_basis` (`subject` or the
  basis). A name whose generational suffix (Jr., Sr., II, III, IV) differs from the
  subject's, including a missing one, is not the subject unless every occurrence in
  `body_text` carries the subject's suffix; otherwise it goes through the normal rule.
- `stated_deceased` requires the death phrase to attach to the named person: "the
  late X" and "X (deceased)" do; "X, who died/passed away" does only when a following
  date differs from the subject's death, or X directly follows a kinship word.
- `entity_index.people` is rebuilt from the filtered `people_mentioned` (each entry
  lists its `retention_basis` values, `pre_cutoff` for older pages).
- `needs_review` surname groupings (`possible_same_person`) are kept only when every
  grouped name is still indexed on that page.
- `relationships[].sentence_raw` is trimmed to the preceded-in-death clause or the
  clause stating the death, so survivors listed in the same sentence are not carried.
  It stays a verbatim substring of `body_text`. When that clause contains a
  parenthetical name ("Name (Spouse) Surname"), `sentence_raw` is `null`; the
  relationship itself is kept.
- `orgs_mentioned` entries whose name never occurs on a single line of `body_text`
  are dropped: they were glued across a line break, usually a person's name from a
  header line plus an employer or school. `entity_index.organizations` is rebuilt from
  `orgs_mentioned`.
- `dates_mentioned[].context` is set to `null` (the date in `text_raw` is kept) unless
  every capitalized name-like word in it is the subject, a kept name, a known place,
  or a common word.

Other names remain only in `body_text`/`body_html`.

`editor_notes` is Leon's printed text, like `body_text`, and is left as is. If Craft
ever renders `editor_notes` on its own, it carries the same exposure as `body_text`
and gets the same display rule. Obituaries printed at or before
the cutoff are not changed by this step. Cases needing human judgment (suffix
collisions, ambiguous death phrases) are written, with names, to a review file outside
the repo via `--review-out`; never commit it.

## Obituary funeral fields (`funeral_location_raw`, `burial_location_raw`, `funeral_home_raw`)

Verbatim spans from the obituary's own `body_text`; empty string when not stated.
Written by `inventory/legacy/extract_funeral.py`, counts in
`obituaries_funeral_report.json`.

- `funeral_location_raw`: the place in "(funeral|memorial|graveside) services / Mass /
  celebration of life will be (was) held ... at X".
- `burial_location_raw`: the place in "interment / inurnment / entombment / burial
  (will be|followed) at|in Y" or "was buried / laid to rest at|in Y". A reception
  "following the burial at" a place is not a burial location.
- `funeral_home_raw`: the name in "arrangements by / under the direction of Z" or "Z is
  in charge of arrangements", and only when Z is a funeral establishment (Mortuary,
  Funeral Home, Chapel, Memorial Park, and the like).

The span ends at a semicolon, parenthesis, sentence end, line break, or a following
date/time/day clause. It continues past a comma only while the next comma segment is a
short run of capitalized place words (optionally a ZIP), so "Quintin, Pangasinan,
Philippines" and "Eternal Valley Memorial Park, Newhall, CA" stay whole; a segment that
starts with a day, month, number, or title, or leads into an officiant, ends the span. When a page states more than
one distinct value for a field, the field is left empty and the page is listed in the
report. A mortuary name printed alone under the obituary heading is not used: the text
does not state what it did.
