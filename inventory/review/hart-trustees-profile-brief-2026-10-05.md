# Drafting brief: Hart trustee profiles, 5 October 2026

For the drafting agents. Draft only: nothing is written to the database. Nathan reads every profile in the dry
run before it is applied (docs/PROFILES.md, "The shape that worked", step 5).

Input: your batch's dossier, inventory/review/hart-trustees-sources-batch-<x>-2026-10-05.json and .md, and the
saved pages beside it. Read docs/PROFILES.md in full and the Kellar profile's script
(scripts/import/build_kellar_profile.php) for the voice, and read three live profiles for the house style:
persons #21944 (Kellar), #15737 (Heidt), #16140 (Darcy), e.g.
ddev craft exec "echo craft\elements\Entry::find()->id(21944)->one()->body;" and its footnotes.

Output: inventory/review/hart-trustees-profiles-draft-batch-<x>-2026-10-05.json, a list, one object per person:
  { "id": 28677, "name": "...", "form": "full" | "short" | "retrospective" | "one-line",
    "body": "paragraphs separated by a blank line, with [n] note markers after the sentence they support",
    "footnotes": ["note 1 text", "note 2 text", ...],
    "notesForNathan": ["anything he must decide: identity doubts, disagreements left open, proposed field changes"] }
and a .md rendering of the same (each profile as a reader would see it, its notes beneath).

Rules:
- Lead with what the person did in the valley. The Hart board service comes from their office holdings and the
  roster; say how the term began and ended only as the sources say.
- Every sentence carries a note. A note cites the source in the archive's style: author, title, publication, date,
  page; "in this archive" with the record's title where the archive holds it; else the URL; mirror pages as
  "as carried on SCVHistory.com, /scvhistory/<page>.htm". Quote the words the sentence rests on.
- Leon Worden's roster is primary but has erred; where it is the only source, the sentence says it is the roster's.
  Where sources disagree, footnote both and state neither.
- Fewer than three lifetime sources: a retrospective profile that says its evidence is later accounts. Roster
  only: one line, e.g. "X sat on the William S. Hart Union High School District board from 1953 to 1957, by Leon
  Worden's roster of the board.[1]"
- Living people: public life only (offices, elections, public roles, the profession they gave as candidates). No
  addresses, family, health. A tie through a relative is not theirs (McKeon's niece is not a fact about Wilson's
  public life). Campaign material is attributed ("her campaign statement says").
- A "probable" identity is not stated. Put it in notesForNathan.
- Reynolds and Perkins: one source between them, attributed. A Worden column counts as independent only where it
  cites something else.
- No em dashes. Plain prose, no praise. No note may name the archive's own process: not "inventory/", "import",
  "migrated", "mirror", "manifest", "SHA", "script", "searched <date>", "could not be read", "Claude", "Nathan's".
  A note that says no source exists says only what is not known; the search goes in notesForNathan.
