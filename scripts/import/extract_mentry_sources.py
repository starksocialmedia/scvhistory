"""
The Mentry sources, extracted verbatim from the Reggie mirror.

Reads the seven pages ch1070 links to and does not yet have a record for, plus
ch1070 itself for its scan line, and writes inventory/legacy/mentry-sources.json
for scripts/import/import_mentry_sources.php.

VERBATIM. Nothing is retyped, corrected or summarised. Whitespace is closed up,
because the HTML wraps lines where the page does not, and that is the only
change. A [sic] is the page's own. Leon's typos stay: "fiancier", "Los Anglees",
"Nehall", "sill resides", "wehre".

TRANSCRIPTION AND INTERPRETATION ARE KEPT APART. Each item carries `text`, what
the source itself says, and `framing`, what the webmaster or a contributor says
about it. The import writes the first to the body and the second to the
webmaster note, so a reader can always tell which words are the 1899 reporter's
and which are 2014's.

Every page also carries the same boilerplate biography of Mentry, which is
ch1070's own text repeated as page furniture. It is recorded once, under ch1070,
and dropped from the others: it describes the man, not the item.

Run on the host, where Reggie is mounted:
    python3 scripts/import/extract_mentry_sources.py
"""
import hashlib
import html
import json
import os
import re
import sys
from datetime import date

MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'
OUT = os.path.join(os.path.dirname(__file__), '..', '..', 'inventory', 'legacy', 'mentry-sources.json')

BIO_START = 'Born Charles Alexander Menetrier (maybe)'
BIO_END = 'Further reading: The Story of Mentryville.'
SIDEBAR = "CHARLES ALEXANDER 'ALEC' MENTRY"


def page_lines(key):
    path = f'{MIRROR}/scvhistory/{key}.htm'
    raw = open(path, 'rb').read()
    text = raw.decode('latin-1')
    title = html.unescape(re.search(r'(?is)<title>(.*?)</title>', text).group(1)).strip()
    body = text[text.lower().find('<body'):]
    body = re.sub(r'(?is)<(script|style).*?</\1>', '', body)
    body = re.sub(r'(?s)<!--.*?-->', '', body)
    body = re.sub(r'\s+', ' ', body)
    body = re.sub(r'(?i)<br\s*/?>|</p>|<p[^>]*>|</div>|</tr>|</h\d>|<h\d[^>]*>|</td>', '\n', body)
    body = html.unescape(re.sub(r'<[^>]+>', '', body))
    lines = [re.sub(r'\s+', ' ', l).strip() for l in body.split('\n')]
    lines = [l for l in lines if l]
    end = lines.index(SIDEBAR) if SIDEBAR in lines else len(lines)
    lines = lines[:end]
    return {
        'path': path.replace(MIRROR, ''),
        'sha256': hashlib.sha256(raw).hexdigest(),
        'title': title,
        'lines': lines,
    }


def cut_bio(lines):
    """Drop the repeated ch1070 biography, returning (lines without it, bio)."""
    if BIO_START not in ' '.join(lines):
        return lines, []
    s = next(i for i, l in enumerate(lines) if l.startswith(BIO_START))
    e = next(i for i, l in enumerate(lines) if l.startswith(BIO_END))
    return lines[:s] + lines[e + 1:], lines[s:e + 1]


def between(lines, start, stop=None):
    s = next(i for i, l in enumerate(lines) if l.startswith(start))
    e = len(lines) if stop is None else next(i for i, l in enumerate(lines) if i > s and l.startswith(stop))
    return lines[s:e]


def image(rel):
    """The file and its master, as the mirror holds them."""
    base, ext = os.path.splitext(rel)
    out = {'file': rel}
    for suffix in ('_large', '_orig'):
        if os.path.exists(f'{MIRROR}{base}{suffix}{ext}'):
            out['master'] = f'{base}{suffix}{ext}'
    return out


def expect(cond, what):
    if not cond:
        sys.exit(f'EXTRACTION FAILED: {what}')


items = []
pages = {}

# ch1070: the hub. Its bio is the one place the boilerplate is recorded.
p = page_lines('ch1070')
rest, bio = cut_bio(p['lines'])
pages['ch1070'] = {**{k: p[k] for k in ('path', 'sha256', 'title')},
                   'bio': bio, 'scan_line': next(l for l in rest if l.startswith('CH1070:'))}
expect(len(bio) >= 6, 'ch1070 bio')

# The two photographs.
for key, code in (('ch1030a', 'CH1030a'), ('ch1040', 'CH1040')):
    p = page_lines(key)
    rest, bio = cut_bio(p['lines'])
    expect(bio, f'{key}: the shared bio was not found, so the cut is unsafe')
    head = rest[1]  # after the breadcrumb line
    scan = next(l for l in rest if l.startswith(code + ':'))
    caption = [l for l in rest[2:] if l not in (scan,) and not l.startswith('Click')]
    # The line under the headline is a section kicker, not a caption.
    kicker = caption.pop(0) if caption and caption[0] in ('Pico Canyon | Mentryville', 'Pico Canyon Oil Pioneer') else ''
    pages[key] = {k: p[k] for k in ('path', 'sha256', 'title')}
    items.append({
        'kind': 'photograph', 'key': key, 'code': code, 'page': key,
        'headline': head, 'kicker': kicker, 'caption': caption, 'scan_line': scan,
        'image': image(f'/gif/{key}.jpg'),
    })

# sw_petermentre: one page, four printed items, each with its own clipping.
p = page_lines('sw_petermentre')
L = p['lines']
pages['sw_petermentre'] = {k: p[k] for k in ('path', 'sha256', 'title')}
framing = between(L, "Webmaster's note.", 'Click to enlarge.')
parker = next(l for l in L if l.startswith('Historian Lauren Parker adds'))
clips = [
    ('sw_herald111186', 'A Missing Man.', None, 'Skeleton in the Mountains'),
    ('sw_herald031799', 'Skeleton in the Mountains', 'Remains of a Man Dead Twelve Years Are Found', "Mentre's Bones Found."),
    ('sw_lat031799', "Mentre's Bones Found.", 'Mysterious Disappearance of Twelve Years Ago Explained.', 'Historian Lauren Parker'),
    ('lp_warrenpatimesmirror020431', 'First California Well.', None, None),
]
for code, head, sub, stop in clips:
    block = between(L, head, stop)
    block = [l for l in block if l != 'Click to enlarge.']
    expect(block[0] == head, f'{code}: headline')
    i = 1
    if sub:
        expect(block[1] == sub, f'{code}: subhead')
        i = 2
    masthead = block[i]
    expect(' | ' in masthead, f'{code}: masthead "{masthead}"')
    items.append({
        'kind': 'document', 'key': code, 'page': 'sw_petermentre',
        'headline': head, 'subheadline': sub or '', 'masthead': masthead,
        'text': block[i + 1:],
        'framing': (framing + [p['lines'][p['lines'].index("Webmaster's note.") - 2],
                               p['lines'][p['lines'].index("Webmaster's note.") - 1]]) if code == 'sw_herald111186'
                   else ([parker] if code == 'lp_warrenpatimesmirror020431' else []),
        'image': image(f'/gif/{code}.jpg'),
    })

# penpictures_mentry: the 1889 biography.
p = page_lines('penpictures_mentry')
L = p['lines']
pages['penpictures_mentry'] = {k: p[k] for k in ('path', 'sha256', 'title')}
s = L.index('C.A. (Charles Alexander) Mentry.')
note = next(l for l in L if l.startswith("Webmaster's note."))
items.append({
    'kind': 'document', 'key': 'penpictures_mentry', 'page': 'penpictures_mentry',
    'headline': L[s], 'masthead': ' '.join(L[s + 1:s + 4]),
    'text': L[s + 4:L.index(note)], 'framing': [note],
    'image': image('/gif/penpictureslacounty.jpg'),
})

# as0001: the certificate. The page is an essay about it; there is no
# transcription of the certificate itself, so text is empty and the essay is
# framing. What the essay quotes from the certificate is listed separately.
p = page_lines('as0001')
rest, bio = cut_bio(p['lines'])
pages['as0001'] = {k: p[k] for k in ('path', 'sha256', 'title')}
essay = between(rest, 'For years and years', 'AS0001:')
essay = [l for l in essay if l != '-->']
items.append({
    'kind': 'document', 'key': 'as0001', 'page': 'as0001',
    'headline': rest[1], 'masthead': '', 'text': [], 'framing': essay,
    'scan_line': next(l for l in rest if l.startswith('AS0001:')),
    'certificate_as_quoted': [
        'died Oct. 4, 1900', 'birth date of March 27, 1847',
        '52 years, 6 months and 8 days old at time of death',
        'California Hospital, 1414 S. Hope St.', 'typhoid fever', 'chronic nephritis as a contributing factor',
        'attending physician, John R. Haynes of 929 S. Main St., Los Angeles',
        'Born in France to parents who were also born in France', 'white, male, and married',
        'occupation "speculator"', 'contracting it 35 days earlier', '("1 mo.")',
    ],
    'image': image('/gif/as0001.jpg'),
})

# scofield: the eulogy.
p = page_lines('scofield')
L = p['lines']
pages['scofield'] = {k: p[k] for k in ('path', 'sha256', 'title')}
s = L.index('Ode to the Man Behind Mentryville')
close = next(l for l in L if l.startswith('This eulogy comes to us from Carol Lagasse'))
items.append({
    'kind': 'document', 'key': 'scofield', 'page': 'scofield',
    'headline': L[s], 'byline': L[s + 1], 'masthead': L[s + 2],
    'standfirst': L[s + 3],
    'text': L[s + 4:L.index(close)], 'framing': [L[s + 3], close],
    'image': None,
})

# lp_lat031754: Arthur's death.
p = page_lines('lp_lat031754')
L = p['lines']
pages['lp_lat031754'] = {k: p[k] for k in ('path', 'sha256', 'title')}
s = L.index('Death of Arthur Charles Mentry, Son of Oilman Alex Mentry.')
credit = next(l for l in L if l.startswith('News story courtesy of'))
items.append({
    'kind': 'document', 'key': 'lp_lat031754', 'page': 'lp_lat031754',
    'headline': L[s], 'masthead': L[s + 1],
    'text': [l for l in L[s + 2:L.index(credit)] if l != 'Click to enlarge.'], 'framing': [credit],
    'image': image('/gif/lp_lat031754.jpg'),
})

for it in items:
    if it.get('image'):
        f = MIRROR + (it['image'].get('master') or it['image']['file'])
        expect(os.path.exists(f), f'{it["key"]}: image {f} missing')
    if it['kind'] == 'document' and it['key'] != 'as0001':
        expect(it['text'], f'{it["key"]}: no text')

out = {
    'meta': {
        'source': 'Reggie mirror, ' + MIRROR,
        'extracted': date.today().isoformat(),
        'extracted_by': 'scripts/import/extract_mentry_sources.py, Claude Code',
        'note': 'Verbatim, whitespace closed up. text is the source; framing is the webmaster or a contributor.',
    },
    'pages': pages,
    'items': items,
}
os.makedirs(os.path.dirname(OUT), exist_ok=True)
with open(OUT, 'w') as fh:
    json.dump(out, fh, indent=1, ensure_ascii=False)
print(f'{len(items)} items from {len(pages)} pages -> {os.path.relpath(OUT)}')
for it in items:
    print(f"  {it['kind']:10} {it['key']:30} text {len(it.get('text') or it.get('caption') or []):2}  "
          f"framing {len(it.get('framing') or []):2}  image {(it.get('image') or {}).get('master') or (it.get('image') or {}).get('file')}")
