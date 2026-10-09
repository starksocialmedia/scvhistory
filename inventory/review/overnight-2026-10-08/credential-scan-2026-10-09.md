# The credential scan, items 1 to 3 (overnight, 8 to 9 October 2026)

Claude, for Nathan. Scripts:
- `scripts/import/credential_targets_2026_10_08.php` (the list);
- `scripts/import/scan_credentials_2026_10_08.py` (opens the files);
- `scripts/import/credential_findings_2026_10_08.php` (sets the findings against the records).

Full output is in storage/runtime/overnight/ (cred-scan.json, cred-findings.json).

## What was read, before any number

- **The file itself, for 2,612 of 2,645 assets:**
  - 2,508 masters on Reggie, at each asset's legacySourcePath;
  - 104 masters held in the repo, each found by hashing every image and PDF under inventory/ and storage/ and matching the recorded checksum (SHA-256 or SHA-1).
  - Each was opened with exiftool (the Mac's 10.31, run by the container's perl) and searched byte by byte for C2PA, JUMBF and generator names.
  - Every stored copy in web/uploads was opened too. Craft re-saves an upload, so a clean stored copy proves nothing on its own.
- **A record, not the file, for 33:**
  - 6 masters named on Reggie but not there:
    - lw2724a and lw2724b are Wayback captures held in storage/runtime;
    - lw2875.pdf and the two lw3786 PDFs are named on Reggie and absent.
  - 4 SVG marks whose masters the scan did not hash, because SVG was not in its list. Found afterwards in inventory/incoming/done by checksum and read whole (SVG is text): no credential and no generator named.
  - 23 portraits with no checksum recorded (21 from the WordPress build, 1 outside, 1 with no kind). Only their stored copies were read, so for these 23 a clean result is not evidence.
- **Craft fields:** contentCredentials, enhancementMethod and enhancedFrom, read to compare with what the files say.

exiftool 10.31 predates C2PA. A manifest embedded in the file is found by its bytes (`c2pa.claim`, `urn:c2pa`); a manifest held elsewhere is found through XMP dcterms:provenance, fetched, and its chain of steps read.

## The groups

| Group | Assets | Master read | Record only |
|---|---:|---:|---:|
| 1. Made after 4 October, master on Reggie (the 2,496) | 2,496 | 2,490 | 6 (named, not on Reggie) |
| 2. Person portraits (the 99 with no edit recorded are 100 tonight with Boston's) | 123 | 100 | 23 (no checksum, so stored copy only) |
| 3. Added since 4 October (Craft's dateCreated; your 2,541 was a different count) | 2,598 | 2,588 | 10 |

The groups overlap, so the distinct total is 2,645.

## Findings

**49 assets carry a marker. On a record, the credential and the record disagree on two.** Both are cases of the thing that keeps happening, and both are written up in ERRORLOG.

### 1. Bill Cooper #26946: a generated image set as his portrait today. Taken off under the no-generated-image rule.

- **The file:** "bill-cooper-campaign-2026.png", downloaded on 8 October from his campaign site's CDN as "the photograph beside his biography". It was one of the 19 set back as unedited originals.
- **Its embedded C2PA manifest:**
  - `c2pa.created` on 2026-06-12, by softwareAgent "gpt-image" version 2.0;
  - digital source type trainedAlgorithmicMedia;
  - signed by OpenAI, then converted and watermarked;
  - no ingredient, so no photograph anywhere in the chain.
- **What it is:** a generated image of a real person, not an edit.
- **How it got through:** it was set on the strength of where it was published, without its file being read.
- **What was done:**
  - Taken off his record under docs/DATA-MODEL.md ("no generated image of a real person anywhere in the archive"), by `cooper_generated_off_2026_10_09.php`, dry run then apply.
  - Snapshot before-cooper-generated-off-2026-10-09.
  - The asset stays, with its credential recorded.
- **What's left:** he has no portrait now. His campaign's image cannot be one. His SCV Water board photograph, if one exists, has not been looked for.
- **Still exposed:** the asset still has a /media page (see the branch-to-staging report).

### 2. Cave Johnson Couts #323: text-to-image steps in a portrait restored as an enhanced pair. Not touched; yours to decide.

- **The asset:** #27387, cave-johnson-couts-us-army-edited.png, one of the 17 restored on 8 October.
- **Its manifest chain, read from the file:**
  - `2026-10-01 08:20 opened (text_to_image) > edited (text_to_image)`.
- **What its record says:**
  - contentCredentials: "edited (Firefly Image 5)";
  - enhancementMethod: "Cropped and cleaned, edges filled (Nathan Imhoff's description of his edits; one Firefly edit in the credential)".
- **What went wrong:** the seven held for text-prompt steps were sorted by the record's summary of the credential, not by the chain. Couts went into the 17 when the chain puts him with the seven.
- **Not touched:** his portrait is unchanged. Whether he joins the seven is your call: "Do not touch the seven text-prompt portraits" covers the decision, and this is the same question.

### 3. Everything else with a marker matches its record, or is on no record

| Class | Assets | Note |
|---|---:|---|
| Edited, generative steps, record says so, on a record | 23 | The 17 restored pairs (Couts apart), Wicks's and Kellar's enlargements, Frémont, and the four marks (Newhall Elementary, Newhall School District, State Senate seal, Canyon High). All under rules already set; the marks wait on your ruling. |
| Text-to-image in the chain, record says so, on no record | 9 | The seven held portraits (#31451, #31445, #31429, #31425, #31402, #31393, #31389) and two more, #31414 (Pete Knight's edit, replaced by his photograph) and #31400 (rn3004-enhanced). Off every record. |
| Edited, record says so, on no record | 10 | Seven of the 4 October Firefly edits the 19 originals replaced (Steve Knight, Sharon and George Runner, Messina, Jensen, Moore, Wilson), Tom Mix's writing-removed copy #31410, and the Castaic Lake and Newhall County water marks. |
| Edited, record says nothing, on no record | 3 | Audra Strickland #31465, BJ Atkins #31443, Jerry Gladbach #31417: creative upsampler, 6 October. Off every record; their records do not mention the credential. Not changed. |
| Text-to-image, record says nothing, on no record | 2 | Rasmussen #29122 and Walters #29118, taken off on 8 October. Their asset records still do not say so. Not changed. |

## The portraits, answered

- **Of the 100 portraits with no edit recorded, the file was read for 77:**
  - 41 masters held in the repo;
  - 36 on Reggie.
- **For the other 23, only the stored copy was read.**
- **One carries a credential its record does not mention:** Bill Cooper's, above, and it was generated rather than edited.
- **None of the other 99 carries a credential or a generator marker in its file.**
- **What this does not prove:** that they are unedited. An edit made without content credentials, or with metadata stripped, leaves nothing for this scan to find. Comparing each with its earliest published copy is the next step (on TODO, "not tonight").

## Byte hits not counted as findings

227 assets have a 4- or 5-byte string somewhere in their bytes:
- "jumb" in 150;
- "c2pa" in 81;
- "dalle" in 45.

None of them holds a C2PA claim (`c2pa.claim`, `urn:c2pa`), an XMP provenance link, an IPTC digital source type or a named generator. These are chance matches inside compressed image and PDF data. They are listed in cred-scan.json.

## The scan itself

- **The first run stalled for 40 minutes** on a large master: the byte search was one regular expression over the whole file. It was killed and rewritten as a fixed-string search in 64 MB chunks.
- **The 4 SVG masters were missed** because the hash pass did not list SVG. They were read by hand afterwards (above).
