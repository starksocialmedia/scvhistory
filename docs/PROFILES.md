# Editorial profiles

How a person record gets a 2026 editorial body (`bodyAuthorship: editorial-2026`):
the rule before writing, the shape that worked, and what it costs. Written after
the Charles Alexander Mentry pilot (#18648, applied 28 September 2026).

## Before writing: count the source set

Nathan, 28 September 2026. Before any profile is written, count:

1. **Is there a hub page?** A legacy page that gathers the person's sources, the
   way ch1070 gathered Mentry's eight.
2. **How many sources from the person's lifetime?** Contemporary: written while
   the events were current (an obituary, a certificate, a newspaper item, a
   biography published in their life).
3. **How many are already in Craft?** The rest are imported first, as documents
   or photographs, before a word of the profile is written.

**Fewer than three sources from the person's lifetime means a retrospective
profile.** That is fine, as long as the page says so: the body names its
evidence as later accounts, and the evidence fields say `retrospective`.

## The shape that worked

1. **Extract** the sources verbatim from the Reggie mirror into
   `inventory/legacy/<name>-sources.json`, with each page's sha256
   (`extract_mentry_sources.py`).
2. **Import** them as documents and photographs (`import_mentry_sources.php`).
   The body is the source's own text; commentary goes to `webmasterNoteTop`,
   credits and editing notes to `webmasterNoteBottom` (DATA-MODEL: Transcription
   and interpretation). A page that wrote about a source without transcribing it
   gets an empty body.
3. **Make the people and events the profile will cite**, each with its own
   evidence and footnotes (`create_pico_1860s_records.php`).
4. **Write the profile** (`update_mentry_profile.php`): every claim footnoted to a
   record; conflicts kept, not resolved (EDTF sets and qualifiers, a source-fault
   note); evidence per claim (DATA-MODEL: Evidence rates the claim, not the
   document). A disputed claim is written as a disagreement among its sources.
5. **Dry run, read, apply.** Nathan reads the prose in the dry run before the
   apply.

## What the pilot proved

- Evidence per claim holds up. Two birth years, two arrival years, three
  death years for Lyon, and two birth years for Jenkins are all kept visible,
  not resolved.
- The transcription and interpretation split works across ten documents,
  including a certificate with an empty body.
- A disputed claim ("first oil well in ...") is expressible as prose with
  footnotes. It needed no schema.
- Research can overturn an approved framing: Perkins was neither the narrowest
  nor the only hedged source. Check the framing against the sources before
  writing it.

## What it cost

Mentry: eight scripts (six content, two schema), thirteen new records (ten
sources, two people, one event), one research pass by an agent of about six and
a half minutes. The hub page did the gathering: five of its eight links were
sources from the lifetimes of Mentry or his family. Without a hub, finding the
sources is the larger part of the work.

## The queue

| Record | Body now | Hub | Lifetime sources | In Craft | Expect |
|---|---|---|---|---|---|
| Henry Mayo Newhall #283 | Leon's own, 2,869 chars | ap1335, ap1335a | strong: 1882 obituary, 1882 will, 1865-1873 billheads and receipts, railroad pass | 3 of 25 titled pages | full profile |
| Edward Fitzgerald Beale #327 | unsourced, hidden | lw2204, lw2205 | thin locally; Bonsal 1912 is book-length | 4 of 12 | decide scope first: the valley or the life |
| Ygnacio del Valle #293 | unsourced, hidden | lw2052 | weak; Pen Pictures 1889 is posthumous | 2 of 7 | retrospective profile |
