"""
An index of the paragraph markup in the crawled source HTML, one row per page.

The question behind it is whether a paragraph of 900 words in our database was
900 words on the legacy page or was six paragraphs whose breaks we lost. Craft
does not hold the answer: legacyHtml is empty on every record that carries a
long paragraph. The crawl does hold it, in body_html, but those files run to
55 MB and are not something a report should open on every run.

So this reduces each page to the few numbers the question needs and writes them
to inventory/legacy/html-index.json, keyed by legacy_key:

    p       <p> tags in the source
    br      <br> tags
    blocks  the number of text blocks the HTML actually separates, counting a
            <p>, a <br><br> pair and a block-level tag as a separator. This is
            the figure to set against the paragraph count in our stored body,
            because the legacy pages broke text with <br><br> at least as often
            as with <p>.
    words   words in the source text, so a body that lost half its text is
            visible as well as one that lost its breaks.

Read only. Writes one file and touches nothing else.
Run: python3 scripts/import/build_legacy_html_index.py
"""

import json
import glob
import os
import re

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
OUT = os.path.join(ROOT, 'inventory', 'legacy', 'html-index.json')

# The lists differ between inventories: perkins splits its pages into a series
# and the related pages around it, the rest keep one list called pages.
LISTS = ('pages', 'series_pages', 'related_pages')

BLOCK = re.compile(r'<(?:p|div|tr|li|h[1-6]|blockquote|center|table)\b', re.I)
BRBR = re.compile(r'(?:<br\b[^>]*>\s*){2,}', re.I)
TAG = re.compile(r'<[^>]+>')


def stats(html: str) -> dict:
    if not html:
        return {'p': 0, 'br': 0, 'blocks': 0, 'words': 0}

    # Every separator the legacy pages used, counted once each. A <br><br> run
    # is one break however many tags are in it.
    p = len(re.findall(r'<p\b', html, re.I))
    br = len(re.findall(r'<br\b', html, re.I))
    blocks = len(BLOCK.findall(html)) + len(BRBR.findall(html))

    text = TAG.sub(' ', html)
    text = re.sub(r'&[a-z]+;|&#\d+;', ' ', text)
    words = len([w for w in text.split() if w.strip()])

    return {'p': p, 'br': br, 'blocks': blocks, 'words': words}


def main() -> None:
    index = {}
    files = sorted(glob.glob(os.path.join(ROOT, 'inventory', 'legacy', '*.json')))
    for path in files:
        name = os.path.basename(path)
        if name in ('html-index.json',) or name.endswith('-images.json'):
            continue
        try:
            with open(path) as fh:
                data = json.load(fh)
        except (ValueError, OSError):
            continue
        if not isinstance(data, dict):
            continue

        found = 0
        for key in LISTS:
            rows = data.get(key)
            if not isinstance(rows, list):
                continue
            for row in rows:
                if not isinstance(row, dict):
                    continue
                lk = (row.get('legacy_key') or '').strip().lower()
                if not lk:
                    continue
                html = row.get('body_html') or ''
                # A key seen twice keeps the richer source rather than the last
                # one read, because an index page and the page itself can share
                # a key and the index page carries almost no markup.
                s = stats(html)
                if lk not in index or s['words'] > index[lk]['words']:
                    s['from'] = name
                    index[lk] = s
                found += 1
        if found:
            print('%-34s %5d pages' % (name, found))

    with open(OUT, 'w') as fh:
        json.dump(index, fh)
    print('\n%d keys written to %s' % (len(index), OUT))


if __name__ == '__main__':
    main()
