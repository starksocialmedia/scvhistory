# The Reynolds "duplicates", the asset store, and lw2184 on the mirror

2026-09-20. Nothing applied.

## The 21 files are not duplicates

Every one has a different hash. Looking at them says why: each is a **per-chapter
title card** — the same drawing of Reynolds over the same map, with that
chapter's number and title lettered into it.

`history-santa-clarita-chapter-1-jerry-reynolds.jpg` reads "Chapter 1 / A Valley
Takes Shape". Chapter 3 reads "Man Arrives". They are 1200×675, a 16:9 card,
and they differ in the lettering.

**Merging them would destroy 21 distinct pieces of artwork** and leave 21
chapters sharing one card. The script does not do it, and prints a line telling
whoever runs it not to delete them.

The two files that genuinely are portraits:

| file | size | dimensions | what it is |
|---|---:|---|---|
| `persons/jerry-reynolds.jpg` | 327,308 | 720×1066 | the portrait photograph. Canonical |
| `legacy/reynolds_jerry.jpg` | 11,845 | 150×200 | the legacy site's thumbnail of it |

Different files, not duplicates of each other: one is a 150px thumbnail.

## The whole asset store, hashed

570 files.

| | |
|---|---:|
| **byte-identical groups** | **2** |
| extra copies that could be deleted | 2 |
| same name, different content | 3 |
| **files imported more than once under chapter-specific names** | **0** |

The two byte-identical pairs are the Craft-rename collisions already known:

```
legacy/perkins_ab.jpg  =  legacy/perkins_ab_2026-09-18-071146_nitl.jpg
legacy/jj2003a.jpg     =  legacy/jj2003a_2026-09-18-071338_lure.jpg
```

The three same-name groups are format or version pairs, not import duplicates:
`henry-clay-wiley-portrait` as both .jpg and .png, `edwin-bryant` the same,
`henry-mayo-newhall` and `henry-mayo-newhall-2`.

**The pattern the brief describes does not occur anywhere in the store**, because
it was never a duplication pattern.

## What the script does instead

**1. Provenance on the canonical portrait.** `persons/jerry-reynolds.jpg` is
byte-identical to `/mnt/reggie/scvhistory.com/gif/lw2184.jpg`, md5
`f5434e986278c9d45d7e5f4979065d83`. It **is** lw2184, unmodified from the mirror.
It gets `photoSourceCode` = `lw2184` and `legacySourcePath` = `/gif/lw2184.jpg`,
so the provenance survives the file being replaced by an enhanced version.

`legacySourcePath` does not exist on the asset layout — there is
`photoSourceCode` but no field for the path. **That is a schema change** and the
script adds it, with instructions saying it records what the archive originally
took and must not change when the stored file does.

**2. The two real duplicates.** Relations are repointed from the renamed copy to
the original, and the copy is listed for deletion by hand. Not deleted here: a
script that removes files is a different risk from one that moves references,
and only the second is reversible.

| copy | live relations moved | on revisions, left alone |
|---|---:|---:|
| `perkins_ab_…_nitl.jpg` #1757 | **2** | 10 |
| `jj2003a_…_lure.jpg` #1846 | **1** | 8 |

**Revisions keep their rows.** Craft stores a relation row on every revision it
made, and those rows record what the entry looked like at the time. Repointing
them would rewrite history to say the duplicate was never there, which is the
opposite of what an archive should do. My first pass would have moved all 21;
restricted to canonical elements it moves 3.

Read-back verification: provenance is read back off a freshly loaded asset and
compared, and the copies are re-queried for live relations. Any mismatch stops
the script before anything is deleted.

## lw2184 on the mirror: it is there

`/mnt/reggie/scvhistory.com/gif/lw2184.jpg`, 327,308 bytes, readable, and
byte-identical to the asset we hold.

The bind in `.ddev/docker-compose.drive.yaml` is

```
/Volumes/Reggie/SCVHistory:/mnt/reggie:ro
```

so the host path is exactly the one in your message,
`/Volumes/Reggie/SCVHistory/scvhistory.com/gif/lw2184.jpg`, and the file exists
at it. **My own shell cannot read `/Volumes/Reggie` either** — `ls` returns
nothing while Docker reads the same path fine. That is macOS withholding
removable-volume access from the terminal, not a missing file. Granting the
terminal Full Disk Access would settle it, or just read through the container as
I have been.

### The mirror layout matches what the plate resolver expects

| directory | entries |
|---|---:|
| `gif` | 22,043 |
| `orig` | 2 |
| `icons` | 9 |

`gif` is the archive's picture directory and everything else is negligible.

The resolver takes `photoSourceCode`, lowercases it, and looks for
`<code>.jpg`. On the mirror that is `gif/<code>.jpg`, flat, no subdirectories:
`gif/lw2184.jpg`, `gif/lw2100a.jpg`, `gif/lw2100at.jpg`. 8,462 files in `gif`
begin `lw`. The thumbnail convention is a trailing `t` on the same stem, so
`lw2100a.jpg` and `lw2100at.jpg` sit side by side. There is **no** `lw2184t.jpg`,
which is consistent with the portrait never having had a thumbnail.

So the resolver's assumption holds: one flat directory, stem plus extension. It
needs no path logic beyond `gif/`.
