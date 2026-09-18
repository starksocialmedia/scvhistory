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
what production deploys. Regenerate after applying entity merges or relation
decisions, since both change which records an article may link to.
