# Checksum audit (8 October 2026)

Read-only, Claude, for Nathan ("does the stored checksum match the master, or the file itself? Tell me how many are self-referential"). Script: scripts/import/checksum_audit_2026_10_08.py (with checksum_audit_dump_2026_10_08.php); per-asset results in storage/runtime/photo-import/checksum-audit.json.

## What it read

- **The recorded checksum** (`sourceChecksum`) of every asset that has one: 4,387 of 6,956, from Craft.
- **The master each names, from sources Craft never wrote:** for a legacy path, the drive manifests, made from Reggie (inventory/raw/scvhistory-manifest-2026-08-20.sha256 and the addendum on Reggie). For a file from outside, any original kept outside Craft (inventory/incoming, inventory/sole-copies, inventory/elections, storage/masters), hashed here.
- **The stored file itself,** hashed here from the volume. Craft re-saves an image on upload, so a stored image is never byte-identical to what was supplied; a stored PDF is.

**Self-referential** means the recorded checksum is the hash of the file it is supposed to vouch for, with no independent master behind it. Comparing it with the file can then only pass.

## Can it fail

Yes. Run on the ten portraits replaced in place on 6 October, as they stood before tonight's restore, it flags all ten. Each names a legacy master on Reggie (`legacySourcePath`) but records the supplied Firefly file's hash: "matches neither the master named nor the file". It flags the two such records still standing (below).

## Result

| | Assets |
|---|---|
| Matches its master on Reggie (drive manifest) | 4,275 |
| of which the stored file is byte-identical to the master (PDFs and other files copied as they are; confirmed by the manifest, not by the file) | 76 |
| Matches a supplied file kept in inventory/incoming/done | 92 |
| Matches nothing held: the original download was not kept | 18 |
| Matches neither the master it names nor the file | 2 |
| Self-referential in the strict sense (equals the stored file, no independent master) | 0 |

**The self-referential shape, as it happened on 6 October**, is a field that names a master while holding the hash of a substituted file. Twelve records had it: the ten replaced in place, and two not restored, which are below. The eight restored tonight now record their master's checksum from the manifest.

1. **Two still have it: Randy Wicks (#31252, randywicks1995_karzinphoto_large.jpg) and Bob Kellar (#27852, sc1310.jpg).** These are the two plain enlargements kept tonight. Their `legacySourcePath` names the legacy master on Reggie, but `sourceChecksum` is the Firefly enlargement's own hash. An integrity check on either compares the enlargement with itself.
2. **48 of the 92 supplied files were downloaded from firefly.adobe.com.** Each such asset's checksum is the Firefly output's own, the earliest copy the archive holds of an edited file. 42 were already off their records. **Five were still on records, and their asset records never said Firefly:**
   - Patti Rasmussen (#29122) and Brian Walters (#29118): both text_to_image.
   - Audra Strickland (#31465), BJ Atkins (#31443) and Jerry Gladbach (#31417): enlargements with no original held.

   All five were taken off tonight (pull_undisclosed_firefly_2026_10_08.php). Chico López (#31474) stays: a plain enlargement whose original is held and linked.
3. **The other 44 supplied files** are downloads with their URL on the file (Wikimedia, the Assembly, campaign and agency sites, a Google Drive share), crops the archive made, and marks.
4. **The 18 that match nothing held** cannot be checked, but they are not self-referential:
   - 16 outside downloads (marks, Commons and Flickr photographs, an official portrait) whose downloaded original was not kept.
   - #4435's two Internet Archive captures (lw2724a and b), whose checksum is of the capture as fetched; Craft re-saved the file.
