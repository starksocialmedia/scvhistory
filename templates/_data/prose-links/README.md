Link layers for article prose.

One file per article, named by entry id, written by
scripts/import/link_mentions_in_prose.php and read by _partials/prose.twig
through Twig's source(). Each file holds the character offsets of the mentions
to link, never the prose itself.

The bodies are not touched by any of this. Delete every .json in here and the
site loses its inline links and nothing else; re-run the script and they come
back. A span whose text is no longer at its offset is skipped at render, so a
stale layer goes quiet rather than cutting through a sentence.

These files are generated, but they are committed, because the templates are
what production deploys and because the spans encode reviewed relation
decisions. Regenerate after applying entity merges or relation decisions, since
both change which records an article may link to.

Each file opens with a header naming the script, its version, the date that
layer was last written, and a fingerprint of the canon it was built from:

    "generated_by": "scripts/import/link_mentions_in_prose.php",
    "version": "1.0",
    "generated": "2026-09-18T08:40:11+00:00",
    "canon": "3f9a1c4e8b20@2026-09-18",

A layer whose version or canon fingerprint does not match the current ones is
stale and is rewritten on the next run, even where its spans have not moved.
