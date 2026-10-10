# Claude, for Nathan

Disputed in our notes, stated as fact: which of the twelve our own notes already settle. 9 October 2026, your brief item 12. Nothing was written to Craft.

The list is section (a) of inventory/review/overnight-2026-10-08/disputed-stated-as-fact-2026-10-08.md: 12 items on 13 records. For each I asked one question only: does an editor note, a factSources row, a footnote, or a ruling of yours written down in CHANGELOG, TODO or docs already say which side is right? A dossier's "Evidence favors" line counts only where you approved it. Nothing here was settled by new research or by my judgment.

Read for this: TODO.md, HANDOFF.md, CHANGELOG.md, ERRORLOG.md, docs/PROFILES.md, docs/DATA-MODEL.md (sourcing, factSources, editor notes), the overnight list and its JSON, the Perkins, del Valle, Henry Mayo Newhall and term-endings dossiers, the Placerita draft, and the records themselves (#333, #1434, #309, #18834, #293, #16290, #28455, #932, #631) through read-only probes in DDEV.

## Settled by our notes: one (A2)

**A2. The "prefer Perkins" rule.** Settled by your ruling of 4 October: the Perkins dossier's correction K9 ("The rule now trusts his verbatim transcriptions only ... limit 'prefer Perkins' to facts he transcribes. Applied 2026-10-04 (approved by Nathan)") and K12 (a figure that is his summary of a document: "neither figure is preferred until the deed is seen"). Two texts still state the rule you withdrew:

- #333 Arthur Buckingham Perkins, body. This is the archive's own profile (bodyAuthorship editorial-2026, build_perkins_profile.php), not Leon's prose.
- #1434 Rancho San Francisco (1957), the editor note "Reading this document" (build_antonio_del_valle_profile.php).

The script `scripts/import/disputed_settled_2026_10_09.php` ($APPLY = false) rewords the one clause in each and touches nothing else. A sweep of every live entry's stored content found the old phrasing nowhere else. Neither builder would put the old text back: build_perkins_profile.php refuses a body edited since the WordPress import, and the del Valle builder writes the document note only when it creates the document.

Its dry run, as it printed:

```
READ, before any number:
  the file itself: the Perkins dossier, for K9 and K12 (the rule as narrowed and approved on 4 October 2026)
  the file itself: docs/PROFILES.md, the Perkins reliability rule
  a record about a file: Craft field body of #333 and editorNotes of #1434, describing the archive's Perkins rule as K9 states it; the file was read too
  a record about a file: every entry's stored content (elements_sites.content), searched for the old phrasings, describing the same rule; the file was read too
K9 in the dossier (verbatim transcriptions only; approved by Nathan, applied 4 October): yes
K12 in the dossier (a summary of a document is not preferred): yes
PROFILES.md still words the rule "Accept Perkins where he quotes or cites a document": yes (wider than K9; for Nathan, not changed here)
DRY RUN

#333 Arthur Buckingham Perkins, body (editorial-2026)
  before: ...and listed his references, so where a later retelling departs from him on a figure, the archive prefers Perkins until an original is seen. His figures still need checking:...
  after:  ...and listed his references, and where he transcribes a document word for word the archive accepts his text. His summaries and figures, like any later retelling, still need checking against an original:...

#1434 Rancho San Francisco: A Study of a California Land Grant (1957), editor note "Reading this document"
  before: Perkins worked from the land-case files, deeds, probate records and the Spanish archives, and cited them, and this archive prefers his figures to later retellings until an original is seen. They still need checking. In this text he dates ...
  after:  Perkins worked from the land-case files, deeds, probate records and the Spanish archives, and cited them. Where he transcribes a document word for word this archive accepts his text; his summaries and figures, like later retellings, need checking against an original. In this text he dates ...

other live entries holding "prefer(s) Perkins" or "prefers his figures": none
REFUSED: none
to change: 2 of 2 records
nothing was written. Set $APPLY = true to apply. A second run after an apply changes nothing.
```

`check_census_reads.php`: "37 scripts made since the rule, each says what it read before any number."

**One thing for you inside A2.** docs/PROFILES.md, "A.B. Perkins as a source", still says "Accept Perkins where he quotes or cites a document", which was written on 3 October, before K9. K12 is exactly a case where he cites a deed and is not preferred. So PROFILES.md is wider than the rule you approved the next day. I have not changed it: the wording of a rule is yours. The script's new sentences follow K9.

## Excluded, as the brief says

- **A4** Northridge deaths and cost, **A5** the Ruiz family dead, **A6** Powerhouse Fire homes: all three are figures for the disasters comparison section (TODO: "The comparison section is next to design; no figure row is published without its scope"). Not touched, nothing proposed.
- None of the others is a Hart or College of the Canyons trustee draft, a fallen officer, or one of the 490 date decisions. A11 McGrath is a Newhall School District holding, so it is not excluded. A1's row is a confirmed recordDates row, not one of the 490 unconfirmed ones, but it is not settled anyway (below).

## Not settled by our notes: these need you (eight)

**A1. Henry Mayo Newhall's birthday, 13 or 23 May 1825 (#283).** The dossier's own verdict is "Evidence favors: Unresolved" (henry-mayo-newhall-sources.md, C1), and no ruling of yours since names a day. The record states 13 May in birthDate and a confirmed recordDates row, so On This Day prints it. Needs your word: hedge it (birthEvidence, an editor note that the 1882 obituaries give 23 May, the recordDates row unconfirmed), or leave it. Leon's body (Ruth Newhall, 1992) stays as printed either way.

**A3. Rancho Camulos (#631), five points.** The body is the archive's own (fill_place_stub_bodies.php, from Reynolds), so it could be changed, but the dossier settles none of the five as stated: the adobe date is "NEEDS_VERIFICATION" (C11), the partition figures "cannot all be right" (C7, no figure chosen), children "12 born, with 5 or 6 surviving" against the body's eleven (C13, not settled), the 1905 Calvary re-interment NEEDS_VERIFICATION (C14), and Jackson's visit, 1882 or 1883, NEEDS_VERIFICATION (C20). The Reynolds rule in PROFILES.md says how to write an uncorroborated figure ("give it as his, or leave it out"), but which side is right is not decided. The body would need rewriting as attributed prose, which is a profile edit for you to read, not a correction.

**A7. Beale's Cut, 90 feet (#932, namingNote).** The note is cited and its quotation checked against La Puerta (set_namesakes.php reads lapuerta2023.txt). The dossier (Beale C11) and the Perkins triage (P27) say no survey supports 90 feet, and K19 confirms an 1874 paper's "one hundred feet deep". No note says which is right. The body's form, attributing "a ninety-foot slash" to Reynolds, is the model if you want the namingNote attributed to Leon in words rather than by citation.

**A8. Stearns: the dream and the oak "enter the story in 1930" (#309, editorial body).** The Placerita draft (item 4) recommends a sentence; the 6 October apply made the event but no ruling on item 4 is recorded in CHANGELOG or TODO. Your word on the draft's sentence settles it.

**A9. Francisco Lopez: "two thousand miners" and the petition "in the National Archives" (#18834, editorial body).** Same: the Placerita draft (item 5) recommends attributing the miners to Reynolds and giving both places for the petition. No ruling recorded.

**A10. Newhall Redevelopment Committee, "without term limits" (#16290).** TODO.md:159 lists it as waiting on you (2002 against Ellis 2005's four-year terms). No ruling.

**A11. McGrath's 2009 term (#28455, Newhall School District).** term-endings-2026-10-04.md: "the holding may need removing rather than redating. Nathan's call." No ruling found in CHANGELOG or TODO. Two smaller points ride with it: footnote 2 ("did not stand at the next election ... 2013") is a term-end note for a term that did not run, and the resignation itself is read from a notice that one would be submitted (below, Read from a description).

**A12. Ygnacio del Valle's birthplace and burial (#293, fields).** The dossier does not contradict the record: it says "Jalisco" and "Compostela" can both be right, and recommends "Nueva Galicia (now Jalisco/Nayarit)" as NEEDS_VERIFICATION (C1); for burial, "Camulos ... is best supported", with the 1905 move to Calvary unverified (C14). No approval of yours is recorded for C1 or C14 (the Perkins dossier's K-corrections touch other del Valle points, C3, C4, C8 and C15). So nothing settles a change. If you want the Antonio pattern (#291's Birthplace editor note), it is a one-line note; your call.

## Read from a description

One new instance, beyond the five listed overnight:

- **#28455 McGrath, howEnded "resigned", termEnd 8 December 2009.** Footnote 4 is the district's release of 4 November 2009, which says he "will submit a resignation effective December 8th". That is a notice of a resignation to come, not a record that it was made; no board minute or later notice is cited. The holding states the resignation as done, on the strength of the announcement. Footnote 3 also puts the start of the term on the first Friday in December (4 December 2009), so if the resignation took effect on 8 December he held the seat for four days, which bears on your A11 call. Listed, not fixed.

## Not done

The PROFILES.md wording (above) is not changed. No CHANGELOG entry, no commit. The script's $APPLY is false.
