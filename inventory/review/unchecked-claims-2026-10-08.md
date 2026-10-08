# What is still unchecked (8 October 2026)

For Nathan: "how many claims in the archive rest on a record describing a file rather than the file." Read only. The census is scripts/import/unchecked_claims_2026_10_08.php; its output, with the statement of what it read, is in storage/runtime/photo-import/unchecked-claims.txt, and the asset ids for each class are in unchecked-claims.json.

What it read, before any number: every stored asset file (hashed); the drive manifests, which are a record of the masters made from Reggie; whether each named master is on Reggie. It also read the output of the 6 October credential scan, which is a record of what that scan opened, not the files. The Craft fields it read describe each asset's file and master. The census did not open the masters for credentials and did not compare them with their web copies: those are the gaps it counts.

The archive holds 6,958 assets. 6,909 of them sit in at least one class below. The classes overlap.

| Class | Assets | On a record | What rests on a record | What would check it |
|---|---|---|---|---|
| Checksum with no original kept | 18 | | The recorded checksum is of a download that was not kept (16 outside downloads, 2 Wayback captures: lw2724a and lw2724b, whose recorded paths are not on Reggie) | Nothing held; the download again, if it can be found |
| Content credentials never read | 2,697 | 2,679 | 2,541 assets made since 4 October were never scanned (2,496 of them have a master on Reggie that can be read now). 156 were scanned only in Craft's re-saved copy, which carries no metadata: 85 whose WordPress original was never fetched, 71 with no original known | scan_content_credentials.py over the 2,496 masters; fetch the 85 from the WordPress build |
| Web copy never compared with its master | 4,201 | 3,919 | The checksum is the master's, but the picture shown is a web copy made from it. That the copy came from that master is the import's record. Only 76 stored files are the master byte for byte | A pixel comparison of each web copy with its master, downscaled |
| No checksum at all | 2,569 | 790 | What the file is rests on its source sentence alone. 2,456 name a master path on Reggie with no checksum (made 18 to 25 September; 1,761 of them are on no record). 78 are WordPress files, 27 have no provenance kind, 7 are commissioned and 1 is from outside | For the 2,456: hash the master and compare it with the web copy, not only copy the manifest's line |
| Picture given to a record by name | 2,450 | 2,450 | Attached on 7 and 8 October by a code in the file name or by the record's folder. Only the 15 held records' pictures were looked at | A contact sheet per record, read by eye, or the page that published it |
| "Made from" another file | 9 | 9 | enhancedFrom. The three Firefly enlargements (López, Wicks, Kellar) carry a credential that names an ingredient, but nobody has compared that ingredient's hash with the original's. Five crops and the Newhall Elementary mark rest on the records of the scripts that made them | Compare the credential's ingredient hash with the original; crop the original again and compare |
| Titles not read against the scan | 57 | 57 | 37 ephemera titles (batches 3 and 4) and 20 photographs left as they are (7 not on Reggie, 4 on multi-piece pages, 9 with no headline found) | The scans, by eye, twenty at a time |

## Already fixed today because a file was read

These came up by going to the source:

- the twelve swapped checksums;
- the five Firefly portraits whose records never said so;
- the folder fault (2,367 files).

Every class above is the same shape: something a record says about a file. Each can be checked except the 18 with no original kept.
