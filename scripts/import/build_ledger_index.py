#!/usr/bin/env python3
"""
Builds the legacy half of the migration ledger: one row per legacy page.

Read only. It touches no database and downloads nothing. It reads the two
sitemap crawls, every page extraction, every image inventory and the drive
manifest, and writes one condensed file:

    web/review/ledger-index.json

That file is the static half of the ledger. It changes only when a crawl, an
extraction or a drive manifest changes, so it is built rather than computed at
request time; a 96 MB manifest cannot be read on every page load. Everything
that can go stale between builds, which is to say everything about Craft, is
computed at request time in templates/admin-ledger/data.twig instead.

The page records the mtime of every source it read. /admin-ledger compares
those against the files on disk and says so when one has moved on, so a stale
index announces itself rather than quietly misreporting.

Three things are worth knowing about the numbers it produces.

The sitemap crawl stopped at 5,000 pages, its own MAX_PAGES. The legacy site is
larger than that. This ledger covers what has been crawled, not the whole site,
and the page says so at the top rather than implying the archive is nearly done.

An image src is resolved against the page it sat on, exactly as
import_legacy_images.php resolves it, so both agree on what file a page means.

A thumbnail missing from the drive is not the same as a picture that is gone.
The mirror on Reggie holds al1890.jpg but not al1890t.jpg, and the import
already prefers the larger file, so a src whose full-size sibling is present is
reported apart from one that is nowhere at all.

Run: python3 scripts/import/build_ledger_index.py
"""

import json
import os
import re
import sys
import glob
import time
from urllib.parse import urlsplit, unquote

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
LEGACY = os.path.join(ROOT, 'inventory', 'legacy')
OUT = os.path.join(ROOT, 'web', 'review', 'ledger-index.json')

SITEMAPS = ['sitemap', 'sitemap-2']
EXTRACTIONS = ['perkins', 'reynolds', 'reynolds-full', 'warmemorial', 'worden', 'loose-pages']
IMAGE_INVENTORIES = ['perkins-images', 'reynolds-images', 'warmemorial-images']

IMAGE_EXT = {'jpg', 'jpeg', 'gif', 'png', 'webp', 'tif', 'tiff', 'bmp'}
CANON_HOST = 'scvhistory.com'

# Page furniture, not pictures. The same rules import_legacy_images.php uses to
# decide what not to download, repeated here so the two agree: a ledger that
# counts the army seal as a missing picture sends somebody looking for a file
# that was deliberately left behind. The threshold is applied across every
# inventory at once rather than one at a time, which catches slightly more.
CHROME_PAGE_THRESHOLD = 5
CHROME_FILENAMES = {
    'armylogo', 'navylogo', 'marinelogo', 'marineslogo', 'airforcelogo',
    'coastguardlogo', 'nationalguardlogo', 'merchantmarinelogo',
}
CHROME_SHAPE = re.compile(r'^[a-z][a-z0-9-]*logo\.(png|gif|jpe?g)$', re.I)


# --------------------------------------------------------------- normalising

def norm_path(value):
    """A legacy page identity: the root-relative path, lowercased, no query.

    Craft stores '/scvhistory/dispatch1501reynolds.htm'. The sitemaps store the
    same shape. Nothing is stripped beyond the host: Chapter 15's legacyUrl was
    wrong precisely because it had lost the /scvhistory/ segment, and a
    normaliser that forgave that would have hidden it.
    """
    if not value:
        return ''
    v = str(value).strip()
    if not v:
        return ''
    if re.match(r'^https?://', v, re.I) or v.startswith('//'):
        v = urlsplit(v if not v.startswith('//') else 'https:' + v).path
    v = v.split('#')[0].split('?')[0]
    v = unquote(v)
    if not v.startswith('/'):
        v = '/' + v
    v = re.sub(r'/{2,}', '/', v)
    return v.lower()


MASTHEAD = re.compile(r'^[A-Za-z0-9][A-Za-z0-9.\-]*\.(?:com|net|org)\s*\|?\s*', re.I)


def clean_title(raw):
    """Drops the masthead a legacy <title> opens with.

    2,107 of these titles begin "SCVHistory.com | ", and a few begin with one
    of the sister sites: DManzer.com, RandyWicks.com, OldTownNewhall.com,
    SaugusSpeedway.net, SantaClaritaWarMemorial.com.

    Only the domain and the bar after it are dropped. A photo page writes its
    accession code between the two, as in "SCVHistory.com AU3501 | Bouquet
    Canyon", and that code is how the picture is ordered from the archive, so
    it stays.
    """
    t = (raw or '').strip()
    return MASTHEAD.sub('', t, count=1).strip()


def absolutise(src, base):
    """Resolves a possibly relative image src against the page it appeared on.

    Same rules as import_legacy_images.php. If the two disagreed, the ledger
    would report a file as missing that the importer had already fetched.
    """
    src = (src or '').strip()
    if not src:
        return None
    if src.startswith('//'):
        return 'https:' + src
    if re.match(r'^https?://', src, re.I):
        return src
    b = urlsplit(base or '')
    if not b.netloc:
        return None
    if src.startswith('/'):
        return 'https://%s%s' % (b.netloc, src)
    out = []
    for seg in (os.path.dirname(b.path).rstrip('/') + '/' + src).split('/'):
        if seg in ('', '.'):
            continue
        if seg == '..':
            if out:
                out.pop()
            continue
        out.append(seg)
    return 'https://%s/%s' % (b.netloc, '/'.join(out))


def ext_of(url):
    return os.path.splitext(urlsplit(url).path)[1].lstrip('.').lower()


def drive_key(url):
    """The path a mirror of the site would store this image at."""
    return unquote(urlsplit(url).path).lstrip('/').lower()


def siblings(path):
    """The same picture at another size. The legacy site names a thumbnail by
    appending t to the stem and a full-size by appending _large."""
    d, b = os.path.split(path)
    stem, ext = os.path.splitext(b)
    out = []
    if stem.endswith('t'):
        out += [stem[:-1], stem[:-1] + '_large']
    if stem.endswith('_large'):
        out.append(stem[:-6])
    out.append(stem + '_large')
    return [os.path.join(d, s + ext) for s in out if s]


# ------------------------------------------------------------------ the read

def load(name):
    p = os.path.join(LEGACY, name + '.json')
    if not os.path.exists(p):
        return None, None
    with open(p, encoding='utf-8') as fh:
        return json.load(fh), p


def pages_in(doc):
    """Every extraction keeps its pages in one or more top-level lists, under
    different names: pages, series_pages, related_pages. Walk whichever exist
    rather than naming them, so a new list in a future crawl is not dropped."""
    for key, value in doc.items():
        if not isinstance(value, list):
            continue
        for item in value:
            if isinstance(item, dict) and ('legacy_path' in item or 'source_url' in item):
                yield key, item


def main():
    sources = {}
    rows = {}

    def touch(path, **fields):
        r = rows.setdefault(path, {
            'p': path, 't': '', 'k': '', 's': '', 'src': [], 'x': '',
            'w': None, 'ic': None, 'img': [],
        })
        for k, v in fields.items():
            if v in (None, '', []):
                continue
            if k == 'src':
                if v not in r['src']:
                    r['src'].append(v)
            elif not r.get(k):
                r[k] = v
        return r

    # 1. the crawls. They carry title, kind, section and word count for every
    #    page, which is the only description most rows will ever have.
    for name in SITEMAPS:
        doc, path = load(name)
        if doc is None:
            print('missing: %s.json' % name, file=sys.stderr)
            continue
        sources[name + '.json'] = os.path.getmtime(path)
        for pg in doc.get('pages', []):
            p = norm_path(pg.get('path') or pg.get('url'))
            if not p:
                continue
            touch(p,
                  t=clean_title(pg.get('title')),
                  k=pg.get('kind') or '',
                  s=pg.get('section') or '',
                  src=name,
                  w=pg.get('body_word_count'),
                  ic=pg.get('image_count'))
        meta = doc.get('meta', {})
        sources.setdefault('_crawls', {})
        sources['_crawls'][name] = {
            'crawled': meta.get('crawled', ''),
            'page_count': meta.get('page_count', 0),
            'truncated': bool(meta.get('truncated')),
            'stop_reason': meta.get('stop_reason', ''),
        }

    # 2. the extractions. A page that has been extracted is a page whose body,
    #    images and entities are already in hand, which is a different state
    #    from one the crawler merely saw.
    for name in EXTRACTIONS:
        doc, path = load(name)
        if doc is None:
            if name != 'loose-pages':
                print('missing: %s.json' % name, file=sys.stderr)
            continue
        sources[name + '.json'] = os.path.getmtime(path)
        for _list, pg in pages_in(doc):
            p = norm_path(pg.get('legacy_path') or pg.get('source_url'))
            if not p:
                continue
            r = touch(p, t=clean_title(pg.get('title')), src=name, k='article')
            r['x'] = name
            if pg.get('needs_review'):
                r['nr'] = len(pg['needs_review'])

    # 3. the pictures. Sources come from the image inventories and from the
    #    images array each extracted page carries; the two overlap and are
    #    deduplicated on the absolute url.
    images = {}

    def note_image(src_raw, source_url, links_to=''):
        url = absolutise(src_raw, source_url)
        if not url or ext_of(url) not in IMAGE_EXT:
            return
        page = norm_path(source_url)
        rec = images.setdefault(url, {'u': url, 'b': os.path.basename(urlsplit(url).path).lower(),
                                      'pages': [], 'd': None, 'alt': '', 'c': ''})
        if page and page not in rec['pages']:
            rec['pages'].append(page)
        link = absolutise(links_to, source_url) if links_to else None
        if link and ext_of(link) in IMAGE_EXT and link != url:
            note_image(links_to, source_url)

    for name in IMAGE_INVENTORIES:
        doc, path = load(name)
        if doc is None:
            print('missing: %s.json' % name, file=sys.stderr)
            continue
        sources[name + '.json'] = os.path.getmtime(path)
        for r in doc.get('images', []):
            note_image(r.get('src_raw'), r.get('source_url'), r.get('links_to') or '')

    for name in EXTRACTIONS:
        doc, _path = load(name)
        if doc is None:
            continue
        for _list, pg in pages_in(doc):
            for r in pg.get('images') or []:
                note_image(r.get('src_raw'), pg.get('source_url'), r.get('links_to') or '')

    # 4. the drive. A 96 MB manifest of the mirror on Reggie, read once here so
    #    it never has to be read on a page load. Absent is reported as unknown,
    #    never as missing: the drive not being plugged in is not a lost picture.
    manifest = sorted(glob.glob(os.path.join(ROOT, 'inventory', 'raw', 'scvhistory-manifest-*.sha256')))
    drive = {'file': '', 'entries': 0, 'read': False}
    on_drive = set()
    if manifest:
        mf = manifest[-1]
        drive['file'] = os.path.basename(mf)
        drive['read'] = True
        sources[os.path.join('inventory/raw', os.path.basename(mf))] = os.path.getmtime(mf)
        with open(mf, encoding='utf-8', errors='replace') as fh:
            for line in fh:
                sp = line.find('  ')
                if sp < 0:
                    continue
                p = line[sp + 2:].strip()
                if p.startswith('./'):
                    p = p[2:]
                if p:
                    on_drive.add(p.lower())
        drive['entries'] = len(on_drive)
    else:
        print('no drive manifest under inventory/raw; the drive column will read unknown', file=sys.stderr)

    # Which of these are furniture rather than pictures.
    for rec in images.values():
        stem = os.path.splitext(rec['b'])[0]
        if len(rec['pages']) > CHROME_PAGE_THRESHOLD:
            rec['c'] = 'on %d pages' % len(rec['pages'])
        elif stem in CHROME_FILENAMES:
            rec['c'] = 'service seal'
        elif CHROME_SHAPE.match(rec['b']):
            rec['c'] = 'a logo'

    for rec in images.values():
        if not drive['read']:
            rec['d'] = 'unknown'
            continue
        key = drive_key(rec['u'])
        if key in on_drive:
            rec['d'] = 'on drive'
        else:
            sib = [s for s in siblings(key) if s in on_drive]
            if sib:
                rec['d'] = 'other size on drive'
                rec['alt'] = sib[0]
            else:
                rec['d'] = 'nowhere'

    # Hang each picture off the pages that show it, so a row can be read alone.
    for rec in images.values():
        for p in rec['pages']:
            if p in rows:
                row = {'b': rec['b'], 'd': rec['d'], 'u': rec['u']}
                if rec.get('c'):
                    row['c'] = rec['c']
                rows[p]['img'].append(row)

    # 5. tidy. Drop the empty keys rather than shipping 5,791 nulls.
    out_rows = []
    for p in sorted(rows):
        r = rows[p]
        r['u'] = 'https://%s%s' % (CANON_HOST, p)
        out_rows.append({k: v for k, v in r.items() if v not in (None, '', [])})

    crawls = sources.pop('_crawls', {})
    payload = {
        'meta': {
            'built': time.strftime('%Y-%m-%dT%H:%M:%S%z'),
            'built_by': 'scripts/import/build_ledger_index.py',
            'page_count': len(out_rows),
            'image_count': len(images),
            'crawls': crawls,
            'drive': drive,
            'sources': {k: round(v) for k, v in sorted(sources.items())},
            'note': 'Static half of the migration ledger. Craft state is computed at '
                    'request time in templates/admin-ledger/data.twig and is not in here.',
        },
        'pages': out_rows,
        'images': sorted(images.values(), key=lambda r: r['u']),
    }

    os.makedirs(os.path.dirname(OUT), exist_ok=True)
    with open(OUT, 'w', encoding='utf-8') as fh:
        json.dump(payload, fh, separators=(',', ':'))

    # ------------------------------------------------------------- the report
    by_source = {}
    for r in out_rows:
        by_source[' + '.join(r.get('src', ['?']))] = by_source.get(' + '.join(r.get('src', ['?'])), 0) + 1
    by_kind = {}
    for r in out_rows:
        by_kind[r.get('k') or '(none)'] = by_kind.get(r.get('k') or '(none)', 0) + 1
    by_drive = {}
    for rec in images.values():
        if rec.get('c'):
            continue
        by_drive[rec['d']] = by_drive.get(rec['d'], 0) + 1
    chrome = [r for r in images.values() if r.get('c')]

    print('wrote %s' % os.path.relpath(OUT, ROOT))
    print('%d legacy pages, %d distinct images' % (len(out_rows), len(images)))
    print()
    print('pages by kind')
    for k, n in sorted(by_kind.items(), key=lambda kv: -kv[1]):
        print('  %-14s %d' % (k, n))
    print()
    print('pages by source')
    for k, n in sorted(by_source.items(), key=lambda kv: -kv[1])[:12]:
        print('  %-34s %d' % (k, n))
    extracted = sum(1 for r in out_rows if r.get('x'))
    print()
    print('extracted: %d of %d pages have a body in an inventory file' % (extracted, len(out_rows)))
    print()
    print('page furniture set aside: %d (service seals, logos, navigation rails)' % len(chrome))
    print()
    print('content pictures against the drive')
    for k, n in sorted(by_drive.items(), key=lambda kv: -kv[1]):
        print('  %-22s %d' % (k, n))
    gone = [r['u'] for r in images.values() if r['d'] == 'nowhere' and not r.get('c')]
    if gone:
        print()
        print('referenced by a page and on no drive (%d):' % len(gone))
        for u in sorted(gone):
            print('  ' + u)
    for name, c in crawls.items():
        if c.get('truncated'):
            print()
            print('NOTE: %s stopped at %d pages (%s). The legacy site is larger than this ledger.'
                  % (name, c.get('page_count', 0), c.get('stop_reason', '')))


main()
