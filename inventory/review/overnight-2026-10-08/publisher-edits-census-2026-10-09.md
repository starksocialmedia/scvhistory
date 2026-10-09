# Publisher-edited images: the shape (9 October 2026)

Claude, for Nathan:
> "how many of the archive's images are in that position, publisher-edited rather than ours, as far as the credentials can tell."

Scripts:
- `scripts/import/publisher_edits_targets_2026_10_09.php` (the list);
- `scripts/import/publisher_edits_census_2026_10_09.py` (reads the files).

Output: storage/runtime/overnight/pub-census.json. Nothing written.

## What was read, before any number

- **The file itself, for 6,759 of 6,886 image assets:**
  - 6,632 masters on Reggie;
  - 127 masters held in the repo, matched by checksum.
  - Each file's metadata was read with exiftool: the software that wrote it, the XMP history's agents and actions, the camera, any IPTC digital source type, any provenance link. Its bytes were searched for an embedded C2PA claim.
- **The stored copy only, for 127:** no master found (58 from the WordPress build, 28 on Reggie, 41 others). Craft re-saves uploads, so a stored copy with no metadata says nothing either way.

## The answer

**As far as the credentials can tell: none.** No image in the archive carries a content credential (C2PA) or an IPTC digital source type from its publisher. Six files carry one:
- Bill Cooper's campaign image: generated, not edited, and now off his record.
- Five Firefly files from our own work, all on no record (Strickland, Atkins, Gladbach, Rasmussen, Walters). Their records don't say so, which is why the census first sorted them here.

Credentials barely exist in what publishers put out before 2024, so this was to be expected. Credentials will not be how publisher edits show up.

**Metadata says more, but less than it seems.** A file whose metadata names an editing program was saved by that program. That says nothing about what was changed: a crop, levels, a resave, or a composite all look the same.

| Origin | Images | Editing program named | Camera only | Master read, no metadata | Stored copy only |
|---|---:|---:|---:|---:|---:|
| The original site (legacy-mirror) | 6,662 | 2,945 | 1,209 | 2,445 | 28 |
| The old WordPress build | 77 | 16 | 1 | 0 | 58 |
| Outside (press, official, Commons, campaigns) | 115 | 17 | 5 | 42 | 3 |
| Commissioned / none | 32 | 0 | 0 | 0 | 19 |

The rest are our own recorded edits (56), files naming software that isn't an editor (36), and the six credentials above.

**What the programs are:**
- Photoshop on 2,952 files;
- Photoshop Elements 15;
- Lightroom 5;
- Camera Raw 2;
- Picasa, Corel and GIMP 1 each.

The XMP histories say "saved" (2,625), "created" (842), "converted" (590), "derived" (427). Those are save, convert and derive steps, not a record of what was changed.

## What the shape means

1. **The original site's own images are the large group: 2,945 masters saved through Photoshop.**
   - The publisher here is SCVHistory.com itself: Leon's scanning and preparation of prints, clippings and documents.
   - 920 of the 2,995 editor-named files across the archive also carry camera metadata, so they began as camera files and were then processed.
   - This is the site we are migrating. Whether "publisher-edited" covers Leon's own preparation, or only outside publishers, is your call. The new rule's wording ("edited by its publisher before it reached us") reads as covering both.
2. **Modern outside photographs are few, and nearly all are Photoshop or Lightroom files.**
   - 115 outside images in all; 17 name an editor. They are:
     - the official portraits (Steve Knight's House portrait, George Runner's Board of Equalization portrait);
     - the Hart district board portraits;
     - Commons files of legislators (McClintock, McCarthy, Garcia, Stern, Thomas);
     - two campaign images (Ahuja, Griese-Schlickart);
     - four school and water marks;
     - Gladbach's ACWA portrait.
   - That is ordinary professional processing. Nothing in the metadata says composite.
   - 42 outside masters carry no metadata at all, and the City's council portraits are among them.
3. **The case you named, a composite, is invisible to all of this.**
   - Gibbs's and Ayala's files carry no metadata. The composite shows only in the pictures: two sitters on the same backdrop.
   - Finding the rest of that kind means looking, set by set: the same background behind different sitters, retouched skin, a replaced sky.
   - The groups worth looking at first are the bodies' own official portrait sets:
     - the City council;
     - the Hart, COC and water boards;
     - the Assembly and Senate member photographs.

## Suggested next step (not done)

1. Record on each outside asset what its metadata says: the program and the camera, as `rightsNote` or `source` text under the new rule. That is mechanical, and it discloses only what the file itself says.
2. Look at the official portrait sets by eye for composites, and disclose what is seen.

Both wait on your word. The first is about 115 assets; the legacy 2,945 wait on your call in point 1 above.
