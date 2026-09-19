# The archive drive

Reggie is the source of truth for SCVHistory's files. 2 TB, HFS+, 658 GB of
mirrored material, verified against the previous copy on 2026-09-19.

Mounted at /Volumes/Reggie/SCVHistory on the machine running the import.

## What is on it

A mirror of scvhistory.com as it stood when the copy was taken: the HTML pages,
the images under gif/, and the document scans. 734,883 files.

## The rule

Read images from the drive, not from the live site. Leon's server is thirty
years old and running on someone else's hosting, and the mirror is the archival
copy. Fetch from scvhistory.com only where a file is not on the drive, and
report it when that happens so the gap is visible.

A file that exists on the drive and not in Craft is work still to do. A file
referenced by a page and absent from both is a loss, and belongs in the ledger
rather than being quietly skipped.

## What is not on it

scvleon.com, which hosts the illustrations for Leon's earlier columns under
/signal/worden/old/. 15 known images so far. That is a second body of material
and needs its own mirror before those pages import.

## Provenance

The drive is a copy of a copy: Jordy to Scratch to Reggie, each verified with
rsync in dry-run mode reporting no differences. Jordy holds the original pull.
