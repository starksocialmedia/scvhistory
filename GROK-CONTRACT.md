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
        { "href_raw": "", "anchor_text": "", "is_internal": true }
      ],
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
