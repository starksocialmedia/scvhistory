# What this week cost and what it bought (4 to 10 October 2026)

Claude, for Nathan. Written while it is fresh, as you asked.

## What was read

- ERRORLOG.md (Open Errors, 3 to 10 October)
- CHANGELOG.md entries from 6 to 9 October
- inventory/review/LONG-RUN-2026-10-09.md, text-to-image-step-2026-10-09.md, archive-images-2026-10-09.md
- Tonight's reports, listed in DECISIONS-2026-10-10.md

The figure of 1,431 images imported is your brief's. It was not recounted for this.

## What it cost

**1. One pattern, now seven times.** The archive took a point from a description of a source and stated it as if it came from the source. The descriptions were of four kinds:
- **A label:** the credential scanner's summary, sorted on as though it were the manifest.
- **A caption:** "Death Cert 10-19-1916" stood for a certificate the archive said it did not hold. The certificate was on Reggie all along.
- **A notice:** McGrath's "will submit a resignation", written down as a resignation.
- **A snippet:** Pecsi's resignation, taken tonight from a search result whose article was never opened (the seventh).

Tonight's other candidates are the same shape:
- a citation sitting in a headline field (#28057);
- a family manuscript known only through del Castillo's description, with his own correction on the same page left out (Camulos, 1,800 acres);
- a label, "legacy-leon", read as Leon's authorship of #283.

**2. The portrait operation removed real photographs and put them back.**
- On 8 October a rule about Firefly edits took 24 enhanced portraits off. It overrode the 6 October enhanced-pair rule without anyone saying so.
- 19 came off because "no original held" was read as "no original exists". All 19 had a real photograph reachable the whole time.
- In putting real photographs back, a generated image went on: Bill Cooper's campaign picture, an OpenAI file with its credential embedded, was set as an unedited photograph. Nobody opened the file.
- Couts came off in the morning and went back in the evening, once his chain was read.

**3. A parser bug drove a week of decisions.**
- The credential scanner labelled each Firefly step from whichever byte pattern it met first. CBOR does not fix the order of a map's keys.
- The same Image 5 step was therefore labelled "text_to_image" on some files and "Firefly Image 5" on others.
- Built on that label, one after another:
  - the seven held as "text-prompt" portraits;
  - the seventeen restored;
  - the overnight Couts finding;
  - your Couts ruling.
- Read field by field, all 22 chains have the same step, and every one opens a real original.

**4. Checks that read records instead of files.**
- The check built to catch a generated image passed while Cooper's was live, because it tested what his record said about the file.
- The single-source census counted a footnote's first-named source as its only one, so it overstated the claims resting on Perkins or Reynolds (item 6, tonight).

**5. Rules changed four times in two days:** the enhanced-pair rule, the Firefly rule, the publisher-edit rule and your enhancement rule. Each change undid part of the one before, and the 8 October change did so unflagged. Tonight's audit of everything changed in 48 hours measures what no longer complies (changed-48h-vs-rules-2026-10-10.md).

**6. My own slips.**
- **Hour-long check runs lost.** An unescaped apostrophe in a check script cost one hour-long render check.
- **A database search that never ran.** Tonight I searched with an error-hiding redirect against a table name without its prefix. The search returned nothing because it failed, not because nothing was there; I caught it and re-ran it.
- **A misdated rule in public text.** I copied the wrong date for a rule into 17 public notes without checking the rule's own record.

## What it bought

**Images**
- **Checks that open the file.**
  - `_generated_scan.php` reads each asset's master by checksum, its stored copy and any manifest.
  - `check_generated_files.php` fails the render check on:
    - a generated file on a record;
    - an unpaired generative edit;
    - a withheld file that is reachable.
- **Every generated image found by opening the files, and off the records.**
  - The scan of all 6,978 assets found one generated file: Cooper's. It is on no record.
  - 35 generative files on no record answer 404 at /media and stay out of the staging rsync.
- **The manifests read as data:** `manifest_steps_2026_10_09.py` parses each key as a CBOR value, and nothing it writes can be sorted on later.
- **The enhancement rule, in your words,** in DATA-MODEL, with the Firefly rule reconciled to it.
- **The 22 pairs:** the chain, the pictures side by side, and tonight the claim each one makes (the-22-pairs-by-claim-2026-10-10.md), so you can rule on a few kinds rather than 22 cases.
- **1,431 images imported** (your figure).

**Sources**
- **Second sources read in the source**, not taken from the report:
  - 19 claims on ten records tonight, each quotation checked against its text by the script before it was written;
  - Jenkins's five, from his death certificate and obituary, now imported.
- **The census's undercount fixed:** for Perkins and Reynolds footnotes it now counts every source a footnote names.

## What we would do differently

1. **A summary field is never evidence.** Nothing a script writes about a source (a label, a count, a class) is read later as if it were the source. Where a summary must exist, the step that uses it re-reads the source. The manifest reader and the generated-file scan now work this way. Tonight's second-sources script checks every quotation against its text before it writes it.
2. **"Not held" or "none" needs a search behind it.** Say what was searched and where. Three of the seven instances were absences: no original, no certificate, no parent. None had been looked for in the place the thing was.
3. **An announcement is not an event.** "Will submit", "plans to", "effective" are recorded as what they are until a source prints the act. Olsen and Love have the same fault as McGrath (how-ended-vs-sources-2026-10-10.md).
4. **A new rule comes with a list of what it undoes,** written beside it before anything is applied. The 8 October collision would have been one line.
5. **Open the file before a picture goes on a person.** The Cooper case is the whole argument.
6. **Checks fail loudly.** No error-hiding redirect on a query whose silence would be read as a result.
7. **Reports quote sources, not each other.**
   - Yesterday's report gave Camulos "three independent sources". Tonight's reading found two, one resting on a manuscript nobody here has seen.
   - It also quoted Bell from a copy where the line was split across a page. That quote held up when re-read; the next one might not.

The lesson is worth more than the fixes. Every costly mistake this week was a place where something said about a source was easier to reach than the source. The work got faster each time it reached for the easier thing, and slower overall.
