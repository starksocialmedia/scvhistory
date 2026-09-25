# The LW features, cleaned before they land

Generated 19 September 2026 by running `clean_legacy_bodies.php` with
`$FROM_INVENTORY = 'lw-features'`, which reads bodies from the inventory file
into unsaved `photograph` entries and reports what it would do. Nothing was
written; there is nothing in Craft to write to yet, which is the point.

The pages go through the same matchers and the same head and tail walks a real
run uses, so this is a prediction made with the rules rather than with a copy.

## The answer

**All 1,661 would arrive dirty. None is clean.**

| | |
|---|---|
| bodies read | 1,661 |
| bodies the cleaner would change | **1,661** |
| bodies already clean | 0 |
| characters it would remove | 1,170,682 |
| mean per record | 704 |
| records where the walk stopped at something it did not recognise | 345 |

## What it would remove

| pattern | hits |
|---|---|
| breadcrumb | 1,699 |
| gallery-captions | 1,427 |
| block-title | 71 |
| block-publication | 58 |
| bracket-nav | 45 |
| byline | 42 |
| dateline | 7 |
| copyright | 1 |

So the answer to "would they arrive dirty" is not a matter of degree. Every one
of the 1,544 importable pages carries a breadcrumb and most carry the caption
list under the thumbnail rail as well.

## What changed to make this possible

`clean_legacy_bodies.php` covered `articles`, `warMemorials` and `obituaries`.
The LW features import as **photographs**, a section it did not touch, so before
this change they would have arrived with all of the above and stayed that way.
It now covers `photographs` and `documents` too.

The matchers moved to `scripts/import/_legacy_chrome_matchers.php` so the dry
run and the real run share them.

## The 345 the walk stops at

These are bodies where the head or tail walk met a line it could not classify
and stopped, leaving everything from there inward. That is the design: a line
that is not certain is never removed. They are the residue a person would look
at after the pass, and they are listed in full in the run output.

## Order of operations

1. Run `clean_legacy_bodies.php` on the existing sections first, which is 22
   records including the three in the review queue.
2. Import the LW features with `import_lw_features.php`.
3. Run `clean_legacy_bodies.php` again, which then covers the photographs.

Cleaning before the import is not possible: the cleaner works on records, and
the dry run above is a prediction, not a transformation of the inventory file.
