# The single-source count, fixed (10 October 2026)

Claude, for Nathan (overnight brief, item 6).

## What was read

- **The 8 October dump** of person, term, affiliation, education and candidacy records (inventory/review/overnight-2026-10-08/_person-claims-dump.json). It is the dump the 8 October census used, so any change below comes from the method, not from tonight's added sources.
- **Two scripts:** the 8 October census (scripts/import/single_source_claims_2026_10_08.py) and tonight's fixed version (single_source_claims_2026_10_10.py).
- **The hand count of the 50:** inventory/review/single-source-second-sources-2026-10-09.md.

## Why the audit undercounted

`families_of()` read a whole footnote as one citation. Once a known family (Perkins, Reynolds, CEDA and so on) matched anywhere in the note, it stopped looking:
- no URL in the note was counted (`urls = [] if fam`);
- no named work was looked for.

So a footnote that cites Perkins and then quotes Bell from the Internet Archive counted as Perkins alone. The first-named source was read as the only source. That is the pattern again: the census described a footnote by its first citation and then treated the description as the footnote.

## The fix

- A footnote that cites Perkins or Reynolds is split into its citations. A split falls at a sentence or semicolon break outside quotation marks, or after a quotation that closes a sentence. It never falls after an initial ("A.B. Perkins") or an abbreviation ("ed. Worden"), and only where the next clause opens like a citation: a name or title and a comma, a quotation, a URL, or a labelled value such as "1882: Pollack, 2012".
- Each citation's families are taken separately.
- A source known only through another is not a witness of its own: "quoted in Perkins", "known here only from a summary", "the legacy page cites".
- An archive record that is itself a Perkins or Reynolds text (his articles; Reynolds's numbered chapters) is that family, not a second source.

**Why Perkins and Reynolds notes only.** Splitting every footnote was tried, and it overcounted: a roster sentence was read as the Hart roster, and a quoted remark as a work. Single-source claims fell from 926 to 829, but with artifacts I could not check overnight. Other notes keep the 8 October reading, so the general census may still undercount second sources by up to about 97 claims. Those need reading, not a better regex.

The rules were set by checking every change against the 50 read by hand. Two early versions overcounted ("quoted in Perkins" counted as a source, and a Reynolds chapter's record number counted as a second witness), and each was corrected before these numbers.

## The count

| | 8 October | Fixed |
|---|---:|---:|
| Claims resting on Perkins or Reynolds alone | 50 | 41 |
| All single-source claims | 926 | 917 |

Nine left the list. Each footnote already named a second source:
- **Banning 11 and 15:** Bell, *Reminiscences of a Ranger*, read on Archive.org.
- **Mentry 17:** the Warren Times Mirror (#20096) and the Los Angeles Times (#20107).
- **Mentry 18:** the Los Angeles Times (#20107).
- **Lyon 8:** the typed copy of the 1869 agreement.
- **Lyon 9:** Pollack 2012 and Addi Lyon's obituary.
- **Lyon 6:** Pollack 2012. The 9 October report noted it was in the footnote but did not count it among its ten.
- **Jenkins 2, and his birthplace field (5):** Kreider 1952.

One other claim gained a source without being on the list: Banning's road, which now counts Bell.

**Two of the hand count's ten the counter cannot see:**
- **Del Valle 43:** the burial that settles it is cited in the sentence before, so the sentence's reasoning rests on another sentence's note.
- **Jenkins's 1910 census row:** it is named in a clause ("The 1910 census gives his age as 74, record #4363") that does not open like a citation.

So the hand count's 10 becomes 11 with Lyon 6. The counter finds 9 of the 11.

**Tonight's added sources do not show in this count.** Re-running the census on a fresh dump would count them: the 19 added by second_sources_2026_10_10.php and Jenkins's five. That re-run was not done tonight, because the dump script writes over the 8 October file.
