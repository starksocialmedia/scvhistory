#!/usr/bin/env python3
"""Extract the Saugus High School shooting (14 November 2019) series from the Reggie mirror.

Read-only on the mirror. Writes inventory/legacy/saugus-high-2019-sources.json.
Step 2 of inventory/review/saugus-high-2019-survey-2026-10-05.md, under Nathan's rulings
of 5 October 2026 (D2, D3, D4, D6, D8). Claude Code.

Bodies are verbatim from each page (UTF-8, all ASCII with HTML entities; entities are
decoded, so &mdash; is kept as an em dash and &shy; as U+00AD). The verbatim check
tokenises each page independently (regex tag strip, entity decode) and requires every
body paragraph to appear as a contiguous word run in the page, in order.
"""
import hashlib, html, json, os, re, subprocess, sys
from html.parser import HTMLParser

MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'
BASE = MIRROR + '/scvhistory/'
MANIFESTS = ['/Volumes/Reggie/SCVHistory/scvhistory-manifest-2026-08-20.sha256',
             '/Volumes/Reggie/SCVHistory/scvhistory-manifest-addendum-2026-10-04.sha256']
OUT = os.path.join(os.path.dirname(__file__), '..', '..', 'inventory', 'legacy', 'saugus-high-2019-sources.json')
PLACEHOLDER = '[contact details withheld]'

# ---------------------------------------------------------------- manifest
manifest = {}
for m in MANIFESTS:
    for line in open(m, encoding='utf-8', errors='replace'):
        parts = line.rstrip('\n').split('  ', 1)
        if len(parts) == 2:
            manifest[parts[1].lstrip('./') if parts[1].startswith('./') else parts[1]] = parts[0]

def sha(path):
    h = hashlib.sha256()
    with open(path, 'rb') as f:
        for chunk in iter(lambda: f.read(1 << 20), b''):
            h.update(chunk)
    return h.hexdigest()

def rel(path):
    return os.path.relpath(path, MIRROR)

def manifest_status(path, digest):
    r = rel(path)
    if r not in manifest:
        return False, 'not listed'
    return True, ('match' if manifest[r] == digest else 'listed, checksum differs')

def dims(path):
    try:
        out = subprocess.run(['sips', '-g', 'pixelWidth', '-g', 'pixelHeight', path],
                             capture_output=True, text=True).stdout
        w = re.search(r'pixelWidth: (\d+)', out); h = re.search(r'pixelHeight: (\d+)', out)
        return (int(w.group(1)) if w else None), (int(h.group(1)) if h else None)
    except Exception:
        return None, None

# ---------------------------------------------------------------- page text
BLOCK = {'p', 'div', 'br', 'tr', 'td', 'table', 'h1', 'h2', 'h3', 'h4', 'li', 'ul', 'ol', 'hr',
         'header', 'nav', 'article', 'section', 'blockquote', 'figure', 'figcaption', 'center'}

class Paras(HTMLParser):
    def __init__(self):
        super().__init__(convert_charrefs=True); self.out = []; self.skip = 0
    def handle_starttag(self, t, a):
        a = dict(a)
        if t in ('script', 'style', 'noscript'): self.skip += 1
        if t in BLOCK: self.out.append('\n\n')
        if t == 'img': self.out.append(f' [IMG {a.get("src")}] ')
        if t == 'video': self.out.append(f' [VIDEO poster={a.get("poster")}] ')
        if t == 'source': self.out.append(f' [SRC {a.get("src")}] ')
        if t == 'iframe': self.out.append(f' [IFRAME {a.get("src")}] ')
    def handle_endtag(self, t):
        if t in ('script', 'style', 'noscript'): self.skip -= 1
        if t in BLOCK: self.out.append('\n\n')
    def handle_data(self, d):
        if not self.skip: self.out.append(d)

RAW = {}
def raw(name):
    if name not in RAW:
        b = open(BASE + name + '.htm', 'rb').read()
        try:
            RAW[name] = b.decode('utf-8')      # every page declares utf-8 and decodes cleanly
        except UnicodeDecodeError:
            RAW[name] = b.decode('latin-1')
    return RAW[name]

PAR = {}
def paras(name):
    if name not in PAR:
        region = raw(name).split('<!-- XWP-BEGIN-CONTENT -->')[1].split('<aside>')[0]
        p = Paras(); p.feed(region)
        t = ''.join(p.out).replace('\xa0', ' ')
        PAR[name] = [x for x in (re.sub(r'[ \t\r\n]+', ' ', y).strip() for y in re.split(r'\n\s*\n', t)) if x]
    return PAR[name]

def source_words(name):
    """Independent tokenisation of the whole page, for the verbatim check."""
    t = raw(name)
    t = re.sub(r'<(script|style)\b.*?</\1>', ' ', t, flags=re.S | re.I)
    t = re.sub(r'</?(?:a|i|b|em|strong|span|font|u)\b[^>]*>', '', t, flags=re.I)  # inline tags join
    t = re.sub(r'<[^>]+>', ' ', t)
    t = html.unescape(t).replace('\xa0', ' ')
    return t.split()

def find_run(words, run, start):
    n = len(run)
    for i in range(start, len(words) - n + 1):
        if words[i:i + n] == run:
            return i
    return -1

def verify(name, paragraphs):
    words = source_words(name); pos = 0; failures = []
    for k, para in enumerate(paragraphs):
        segs = [s.split() for s in para.split(PLACEHOLDER)]
        for s in segs:
            if not s: continue
            i = find_run(words, s, pos)
            if i < 0:
                failures.append(k + 1); break
            pos = i + len(s)
    return failures

# ---------------------------------------------------------------- pages
PAGES = [
    ('sg20191114shs', 1), ('lat20191115shs', 2), ('austindave20191115shs', 3), ('scvtv20191115shs', 4),
    ('lat20191116shs', 5), ('sg20191117shs', 6), ('addisonkoegle20191117', 7),
    ('bryanmuehlberger20191117', 8), ('lat20191118shs', 9), ('sg20191119shs', 10), ('hd20200112', 11),
    ('hd20200406', None),
]

def fine_print(name):
    tail = raw(name).split('<!-- XWP-END-CONTENT -->')[1]
    t = re.sub(r'<(script|style)\b.*?</\1>', ' ', tail, flags=re.S | re.I)
    t = html.unescape(re.sub(r'<[^>]+>', ' ', t)).replace('\xa0', ' ')
    t = re.sub(r'\s+', ' ', t).strip()
    i = t.find('SCVHistory.com is another service')
    return t[i:] if i >= 0 else None

pages_out = []
for name, n in PAGES:
    path = BASE + name + '.htm'; d = sha(path); inm, st = manifest_status(path, d)
    title = html.unescape(re.search(r'<title>(.*?)</title>', raw(name), re.S).group(1)).strip()
    comment = re.search(r'<!--\s*(ALL UNATTRIBUTED CONTENT.*?)\s*-->', raw(name))
    pages_out.append({
        'path': rel(path), 'seriesPage': n, 'sha256': d, 'inManifest': inm, 'manifestCheck': st,
        'title': title, 'legacyUrl': '/scvhistory/' + name + '.htm',
        'finePrint': fine_print(name),
        'copyrightComment': comment.group(1) if comment else None,
        'notes': None if n else 'Not one of the eleven series pages: the later district page that mentions the shooting (survey, "Around the series"). Its piece is one of the 16.',
    })

# ---------------------------------------------------------------- pieces
# Indices refer to paras(page). body: inclusive range; skip: indices inside the range that are
# images, captions or player text, captured separately. caps: (image file, caption idx or None,
# credit text source) where the printed caption line is split at " Photo: ".
def P(**kw): return kw

PIECES = [
    P(key='sg20191114-signal-holt', page='sg20191114shs', publisher='The Signal', kind='news',
      hl=2, by=3, dl=4, body=(7, 49), skip=[12, 13], iso='2019-11-14',
      caps=[('sg20191114shs_video02.jpg', 6, 'video poster; caption printed under the first video'),
            ('sg20191114shs_video01.jpg', 51, 'video poster; caption printed under the second video'),
            ('sg20191114shs06.jpg', 60, None), ('sg20191114shs07.jpg', 62, None)],
      never_caps=['sg20191114shs08.jpg (caption at paragraph position after body paragraph 5; names the shooter)',
                  'sg20191114shs.jpg (aerial credit line, covers shs, 02, 03)',
                  'sg20191114shs04.jpg (caption covers 04 and 05; gives the block and street of the family home)'],
      notes='Page 1 and the series hub. The running report of 14 November: the later afternoon text first, then "From the original story, Thursday morning:" (printed in italics), then the contributors line (italics). Italics are not marked in the body. The 22 paragraphs of the morning story keep their early errors as printed (the suspect "in custody"). The two video captions are recorded against the video posters. Images 02 to 05 print no caption of their own; the D6 "never" images have no caption text copied here.'),
    P(key='lat20191115-gerber', page='lat20191115shs', publisher='Los Angeles Times', kind='news',
      hl=2, deck=3, by=4, dl=5, body=(8, 51), skip=[18, 19], iso='2019-11-15',
      caps=[('lat20191115shs.jpg', 7, None), ('lat20191115shs02.jpg', 19, None)],
      notes='Held on D5 (L.A. Times rights). Ends with the staff contributors line, kept in the body. Gives the girl who died as 16 (see disagreements).'),
    P(key='austindave20191115-video', page='austindave20191115shs', publisher='not printed (video hosted on scvtv.com)', kind='video-introduction',
      hl=2, by=5, dl=5, body=(6, 10), skip=[], iso='2019-11-14/2019-11-15',
      caps=[('austindave20191115shs.jpg', None, 'video poster; no caption printed')],
      notes='The byline and date are printed together, in italics, under the video: "Video & story by Austin Dave, November 14-15, 2019". The player fallback text ("To view this video please enable JavaScript ...") is not part of the piece. The video is not in the mirror.'),
    P(key='scvtv20191115-press-conference', page='scvtv20191115shs', publisher='SCVTV', kind='news',
      hl=2, deck=3, dl=4, body=(6, 10), skip=[], iso='2019-11-15',
      caps=[('scvtv20191115shs_video.jpg', None, 'video poster; no caption printed')],
      notes='Summary of the LASD press conference of 15 November, under the video. No byline printed; the dateline names SCVTV. The deck "Plus: Saugus Grads Set Up Fund." belongs to the page and announces the second piece.'),
    P(key='scvtv20191115-fund-peeples', page='scvtv20191115shs', publisher='SCVTV/SCVNews.com', kind='news',
      hl=11, by=12, dl=13, body=(15, 34), skip=[], iso='2019-11-15', redact=True,
      caps=[('scvtv20191115shsvigil.jpg', None, 'the City vigil graphic; no caption printed')],
      notes='D8 applies: private contact details replaced (see redactions). The piece quotes the GoFundMe page at length, including the organizers\' own "More About the Organizers" text. "Peter­son" in paragraph 2 carries a soft hyphen (&shy;) as printed. Public links kept: the support-services form, the GoFundMe page, and a Signal archive article.'),
    P(key='lat20191116a-shining-light', page='lat20191116shs', publisher='Los Angeles Times', kind='news',
      hl=2, deck=3, by=4, dl=5, body=(8, 41), skip=[20, 21], iso='2019-11-16',
      caps=[('lat20191116shs.jpg', 7, None), ('lat20191116shs03.jpg', 21, None)],
      notes='Held on D5. "Sophoia" in the caption of lat20191116shs03.jpg is as printed. The headline is printed inside quotation marks.'),
    P(key='lat20191116b-victims-identified', page='lat20191116shs', publisher='Los Angeles Times', kind='news',
      hl=42, deck=43, by=44, dl=45, body=(48, 78), skip=[57, 58], iso='2019-11-15',
      caps=[('shs2019-graciemuehlberger_full.jpg', 47, None), ('shs2019-dominicblackwell.jpg', 58, None)],
      notes='Held on D5. Printed second on the page but dated a day earlier than the others. Paragraphs 21 to 23 are three Instagram comments quoted without quotation marks, as printed.'),
    P(key='lat20191116c-firearms-seized', page='lat20191116shs', publisher='Los Angeles Times', kind='news',
      hl=79, deck=80, by=81, dl=82, body=(85, 127), skip=[93, 94, 105, 106], iso='2019-11-16',
      caps=[('lat20191116shs05.jpg', 94, None), ('lat20191116shs06.jpg', 106, None)],
      never_caps=['sg20191114shs08.jpg (the shooter\'s portrait, reused at the head of this piece; caption names him)'],
      notes='Held on D5. Names the shooter and his late father, and describes the weapon, the father\'s arrests and the family\'s domestic history. The archive\'s own text uses none of it (D2).'),
    P(key='lat20191116d-search-for-answers', page='lat20191116shs', publisher='Los Angeles Times', kind='news',
      hl=128, deck=129, by=130, dl=131, body=(134, 156), skip=[], iso='2019-11-16',
      caps=[('lat20191116shs04.jpg', 133, None)],
      notes='Held on D5. Paragraph 22 names the street of the shooter\'s family home (see addressOccurrences). "home , and" in paragraph 3 is as printed.'),
    P(key='lat20191116e-banks-commentary', page='lat20191116shs', publisher='Los Angeles Times', kind='news',
      hl=157, by=158, dl=159, body=(162, 199), skip=[], iso='2019-11-16',
      caps=[('lat20191116shs02.jpg', 161, None)],
      notes='Held on D5. A commentary ("Commentary by Sandy Banks." is the printed byline). The section breaks "* * *" are kept as their own paragraphs.'),
    P(key='sg20191117-vigil-video', page='sg20191117shs', publisher='SCVTV for the City of Santa Clarita', kind='video-introduction',
      hl=2, deck=3, dl=4, body=(7, 9), skip=[8], iso='2019-11-17',
      caps=[('saugusstrongvigil20191117.jpg', None, 'video poster; no caption printed')],
      notes='The page\'s video credit and the credit line under the embedded City photo gallery (files/sc1903/sc1903.htm, 74 photographs; not in the survey\'s count). The vigil video is not in the mirror.'),
    P(key='sg20191117-signal-alvarenga', page='sg20191117shs', publisher='The Signal', kind='news',
      hl=10, by=11, dl=12, body=(13, 43), skip=[15, 16, 21, 22, 27, 28, 34], iso='2019-11-17',
      caps=[('sg20191117shs.jpg', 16, None), ('sg20191117shs02.jpg', 22, None), ('sg20191117shs03.jpg', 28, None),
            ('sg20191117shs04.jpg', None, 'no caption or credit printed')],
      notes='Quotes the Muehlberger letter (paragraph 17) with wording that differs from the letter as printed on page 8 (see disagreements). "My also heart goes out" in paragraph 30 is as printed.'),
    P(key='addisonkoegle20191117-video', page='addisonkoegle20191117', publisher='SCVHistory.com (unattributed introduction)', kind='video-introduction',
      hl=2, deck=3, body=(7, 8), skip=[], iso='2019-11-17', status='excluded (D4: the Koegle family asked the media to respect their privacy)',
      caps=[],
      notes='Excluded on D4. The text is kept here only as the record of what the page held; the video is listed as excluded and nothing from this page is to be imported. The introduction names the shooter. No dateline or byline printed; "Saugus High School Shooting" is the page\'s subhead.'),
    P(key='bryanmuehlberger20191117-letter', page='bryanmuehlberger20191117', publisher='the Muehlberger family (released for publication 19 November 2019)', kind='letter',
      hl=2, dl=3, intro=4, body=(5, 40), skip=[], iso='2019-11-17', released='2019-11-19',
      caps=[('shs-gracieannemuehlberger.jpg', None, None), ('shs-gracieannemuehlberger02.jpg', None, None),
            ('shs-gracieannemuehlberger05.jpg', None, None), ('shs-gracieannemuehlberger04.jpg', None, None),
            ('shs-gracieannemuehlberger03.jpg', None, None), ('shs-gracieannemuehlberger06.jpg', None, None),
            ('shs-gracieannemuehlberger07.jpg', None, None), ('shs-gracieannemuehlberger08.jpg', None, None),
            ('shs-gracieannemuehlberger09.jpg', None, None), ('shs-gracieannemuehlberger10.jpg', None, None)],
      notes='The most important document in the set. Body: the letter as printed, from "As Cindy and I struggle" to the closing hashtags, including the GoFundMe line. No signature line is printed: the letter ends with "#GracieStrong, #DominicStrong, and #SaugusStrong". The authorship and the release statement are printed only in the site\'s italic introduction, kept verbatim in siteIntroduction. No byline element on the page. The ten photographs follow the letter in a two-column grid with no captions, no credits and empty alt text; they are listed in the order printed (01, 02, 05, 04, 03, 06, 07, 08, 09, 10). "pink tutu\'s", "the why\'s", "its Friday", "Sadies Hawkins", "Gracie, will remain", "hear her that laughter" and "Cindy and I" (object) are as printed.'),
    P(key='lat20191118a-thousands-mourn', page='lat20191118shs', publisher='Los Angeles Times', kind='news',
      hl=2, by=3, dl=4, body=(7, 26), skip=[15, 16], iso='2019-11-18',
      caps=[('lat20191118shs.jpg', 6, None), ('lat20191118shs02.jpg', 16, None)],
      notes='Held on D5. "re­mem­ber­ed" in paragraph 12 carries three soft hyphens (&shy;) as printed.'),
    P(key='lat20191118b-new-wave-of-grief', page='lat20191118shs', publisher='Los Angeles Times', kind='news',
      hl=27, deck=28, by=29, dl=30, body=(33, 77), skip=[50, 51], iso='2019-11-18',
      caps=[('lat20191118shs03.jpg', 32, None), ('lat20191118shs04.jpg', 51, None)],
      notes='Held on D5. The note at the memorial and the text messages are printed in italics; italics are not marked in the body. "[heart]" is the paper\'s own bracket. Spaces before commas ("Blackwell , 14", "Thousand Oaks ,") are as printed.'),
    P(key='sg20191119-signal-murga', page='sg20191119shs', publisher='The Signal', kind='news',
      hl=2, by=3, dl=4, body=(7, 14), skip=[], iso='2019-11-19',
      caps=[('shs-miatretta.jpg', 6, None)],
      notes='Carries the Tretta family statement (paragraph 3) and the Koegle family statement with its privacy request (paragraph 7), both verbatim within the news text.'),
    P(key='hd20200112-district-kuhlman', page='hd20200112', publisher='William S. Hart Union High School District', kind='district-release',
      hl=2, by=3, dl=4, body=(7, 57), skip=[], iso='2020-01-12',
      caps=[('mugs/hartschooldistrictlogo.jpg', None, 'district logo; no caption printed')],
      notes='Emailed to district families. Includes the signature lines ("Sincerely" / "Mike Kuhlman" / "Deputy Superintendent" / "William S. Hart Union High School District.") and the consultants\' biographies printed after them. The page links the original PDF (hd20200112.pdf, listed under documentFiles); the link text "Open original .pdf version" is not part of the piece. "in in the U.S.", "WAs an Associate", "Rossiter School" are as printed. The PDF was not compared with the page text.'),
    P(key='hd20200406-district-ferry', page='hd20200406', publisher='William S. Hart Union High School District', kind='district-release',
      hl=3, by=4, dl=5, body=(8, 14), skip=[], iso='2020-04-06',
      caps=[('vinceferry2020.jpg', None, '"Click image to enlarge." is the only text under the photograph')],
      notes='Outside the eleven-page series; one paragraph mentions "the tragic events of November 14, 2019". The printed byline line is the district\'s name.'),
]

def split_caption(text):
    if text is None: return None, None
    t = re.sub(r'\s*Click to enlarge\.\s*$', '', text)
    m = re.search(r'\s(Photo: .*)$', t)
    if m: return t[:m.start()].strip() or None, m.group(1).strip()
    if t.startswith('Photo: '): return None, t
    return t, None

EMAIL = re.compile(r'[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}')
LINKEDIN = re.compile(r'(?:https?://)?(?:www\.)?linkedin\.com/in/[^\s)]+')
PHONE = re.compile(r'\(?\b\d{3}\)?[-. ]\d{3}[-.]\d{4}\b')
URL = re.compile(r'(?:https?://|www\.)\S+|\b[\w-]+\.(?:org|com|net)(?:/\S*)?', re.I)

def redact(paragraphs, key):
    out, recs = [], []
    for i, p in enumerate(paragraphs):
        hits = sorted([(m.start(), m.end(), 'LinkedIn profile address') for m in LINKEDIN.finditer(p)] +
                      [(m.start(), m.end(), None) for m in EMAIL.finditer(p)])
        if not hits: out.append(p); continue
        new, last = '', 0
        for n, (s, e, kind) in enumerate(hits, 1):
            if kind is None:
                dom = p[s:e].split('@')[1].lower()
                kind = 'personal email address (free webmail)' if dom in ('gmail.com', 'yahoo.com', 'hotmail.com', 'aol.com', 'icloud.com') else 'work email address (school district domain)'
            new += p[last:s] + PLACEHOLDER; last = e
            recs.append({'piece': key, 'paragraph': i + 1, 'occurrenceInParagraph': n,
                         'ofInParagraph': len(hits), 'kind': kind, 'replacedWith': PLACEHOLDER})
        out.append(new + p[last:])
    return out, recs

pieces_out, all_redactions, scan = [], [], []
check = []
for pc in PIECES:
    pg = paras(pc['page'])
    a, b = pc['body']
    idx = [i for i in range(a, b + 1) if i not in pc['skip']]
    body_paras = [pg[i] for i in idx]
    assert not any(x.startswith('[IMG') or x.startswith('[VIDEO') or x.startswith('[IFRAME') for x in body_paras), pc['key']
    fails = verify(pc['page'], body_paras)
    redactions = []
    if pc.get('redact'):
        orig = body_paras
        body_paras, redactions = redact(body_paras, pc['key'])
        # after redaction: the verified text and the output differ only at the placeholders
        fails2 = []
        for k, (o, r) in enumerate(zip(orig, body_paras)):
            parts = r.split(PLACEHOLDER)
            pat = '^' + '(.+?)'.join(re.escape(x) for x in parts) + '$'
            m = re.match(pat, o, re.S)
            if not m or any(not (EMAIL.fullmatch(g) or LINKEDIN.fullmatch(g)) for g in m.groups()):
                fails2.append(k + 1)
        if sum(len(x.split(PLACEHOLDER)) - 1 for x in body_paras) != len(redactions):
            fails2.append('placeholder count')
    else:
        fails2 = fails
    check.append({'piece': pc['key'], 'paragraphs': len(body_paras), 'words': sum(len(x.split()) for x in body_paras),
                  'failedParagraphs': fails, 'failedAfterRedaction': fails2})
    # scan for other contact details and URLs (not copied for private ones)
    for i, p in enumerate(body_paras):
        for m in EMAIL.finditer(p):
            scan.append((pc['key'], i + 1, 'email address left in text'))
        for m in PHONE.finditer(p):
            scan.append((pc['key'], i + 1, 'phone number'))
    caps = []
    for img, ci, note in pc['caps']:
        cap, cred = split_caption(pg[ci]) if ci is not None else (None, None)
        c = {'image': img, 'caption': cap, 'credit': cred}
        if ci is not None and pg[ci].rstrip().endswith('Click to enlarge.'):
            c['printedAlso'] = 'Click to enlarge.'
        if note: c['note'] = note
        caps.append(c)
    date_line = pg[pc['dl']] if 'dl' in pc else None
    entry = {
        'key': pc['key'], 'page': '/scvhistory/' + pc['page'] + '.htm', 'publisher': pc['publisher'],
        'byline': pg[pc['by']] if 'by' in pc else None,
        'date': date_line, 'dateIso': pc['iso'],
        'headline': pg[pc['hl']], 'deck': pg[pc['deck']] if 'deck' in pc else None,
        'kind': pc['kind'],
        'body': '\n\n'.join(body_paras),
        'captions': caps, 'redactions': redactions, 'notes': pc['notes'],
    }
    if 'intro' in pc: entry['siteIntroduction'] = pg[pc['intro']]
    if 'released' in pc: entry['releasedIso'] = pc['released']
    if 'status' in pc: entry['status'] = pc['status']
    if pc.get('never_caps'): entry['neverImageCaptionsNotCopied'] = pc['never_caps']
    if pc['key'] == 'austindave20191115-video': entry['date'] = 'November 14-15, 2019 (printed in the byline line)'
    pieces_out.append(entry); all_redactions += redactions

# ---------------------------------------------------------------- images
G = MIRROR + '/gif/'
LAT = 'held for rights'
IMAGES = [
    # file, piece, status, credit override, note
    ('sg20191114shs.jpg', 'sg20191114-signal-holt', 'never', 'KTLA-5 frame of the injured under treatment, blue box over the person (D6).'),
    ('sg20191114shs02.jpg', 'sg20191114-signal-holt', 'never', 'KTLA-5 frame of the injured under treatment (D6).'),
    ('sg20191114shs03.jpg', 'sg20191114-signal-holt', 'never', 'KTLA-5 frame of the injured under treatment (D6).'),
    ('sg20191114shs04.jpg', 'sg20191114-signal-holt', 'never', 'KTLA frame of the search of the family home (D6).'),
    ('sg20191114shs05.jpg', 'sg20191114-signal-holt', 'never', 'KTLA frame of the search of the family home (D6).'),
    ('sg20191114shs08.jpg', 'sg20191114-signal-holt', 'never', 'The shooter\'s school portrait (D2, D6). Also printed at the head of lat20191116c.'),
    ('sg20191114shs06.jpg', 'sg20191114-signal-holt', LAT, 'KTLA frame of the evacuation at Central Park (survey). No credit printed under it.'),
    ('sg20191114shs07.jpg', 'sg20191114-signal-holt', LAT, '#SAUGUSSTRONG banner on a fence; Two-8-Nine Media watermark (survey).'),
    ('sg20191114shs_video02.jpg', 'sg20191114-signal-holt', 'scvtv-city', 'Poster of the first SCVTV video (about 11 a.m.).'),
    ('sg20191114shs_video01.jpg', 'sg20191114-signal-holt', 'scvtv-city', 'Poster of the second SCVTV video (about 4 p.m.).'),
    ('lat20191115shs.jpg', 'lat20191115-gerber', LAT, None),
    ('lat20191115shs02.jpg', 'lat20191115-gerber', LAT, None),
    ('austindave20191115shs.jpg', 'austindave20191115-video', 'scvtv-city', 'Video poster.'),
    ('scvtv20191115shsvigil.jpg', 'scvtv20191115-fund-peeples', 'scvtv-city', 'The City of Santa Clarita vigil announcement graphic (survey).'),
    ('scvtv20191115shs_video.jpg', 'scvtv20191115-press-conference', 'scvtv-city', 'Video poster.'),
    ('lat20191116shs.jpg', 'lat20191116a-shining-light', LAT, None),
    ('lat20191116shs03.jpg', 'lat20191116a-shining-light', LAT, 'Caption names two minors, sisters aged 16 and 12.'),
    ('shs2019-graciemuehlberger_full.jpg', 'lat20191116b-victims-identified', 'family (with the letter)', 'Family photograph, "Courtesy of Muehlberger family". Printed on page 5 in an L.A. Times piece, not with the letter; grouped with the family\'s photographs under D6.'),
    ('shs2019-dominicblackwell.jpg', 'lat20191116b-victims-identified', LAT, 'Dominic Blackwell\'s portrait; no credit printed.'),
    ('lat20191116shs05.jpg', 'lat20191116c-firearms-seized', LAT, None),
    ('lat20191116shs06.jpg', 'lat20191116c-firearms-seized', LAT, None),
    ('lat20191116shs04.jpg', 'lat20191116d-search-for-answers', LAT, None),
    ('lat20191116shs02.jpg', 'lat20191116e-banks-commentary', LAT, None),
    ('saugusstrongvigil20191117.jpg', 'sg20191117-vigil-video', 'scvtv-city', 'Poster of the vigil video.'),
    ('sg20191117shs.jpg', 'sg20191117-signal-alvarenga', 'signal', None),
    ('sg20191117shs02.jpg', 'sg20191117-signal-alvarenga', 'signal', None),
    ('sg20191117shs03.jpg', 'sg20191117-signal-alvarenga', 'signal', None),
    ('sg20191117shs04.jpg', 'sg20191117-signal-alvarenga', LAT, 'The two memorial crosses. No caption or credit of its own; D6: held until its maker is known. Not one of the 16 named in the ruling, so the held count is 17.'),
] + [(f, 'bryanmuehlberger20191117-letter', 'family (with the letter)', 'Released with the letter for publication by the family on 19 November 2019 (site introduction). No caption, credit or alt text printed.')
     for f in ['shs-gracieannemuehlberger.jpg', 'shs-gracieannemuehlberger02.jpg', 'shs-gracieannemuehlberger05.jpg',
               'shs-gracieannemuehlberger04.jpg', 'shs-gracieannemuehlberger03.jpg', 'shs-gracieannemuehlberger06.jpg',
               'shs-gracieannemuehlberger07.jpg', 'shs-gracieannemuehlberger08.jpg', 'shs-gracieannemuehlberger09.jpg',
               'shs-gracieannemuehlberger10.jpg']] + [
    ('lat20191118shs.jpg', 'lat20191118a-thousands-mourn', LAT, None),
    ('lat20191118shs02.jpg', 'lat20191118a-thousands-mourn', LAT, 'Caption names four students.'),
    ('lat20191118shs03.jpg', 'lat20191118b-new-wave-of-grief', LAT, 'Caption names two students.'),
    ('lat20191118shs04.jpg', 'lat20191118b-new-wave-of-grief', LAT, None),
    ('shs-miatretta.jpg', 'sg20191119-signal-murga', LAT, 'Mia Tretta\'s portrait; no credit printed (D4, D6).'),
]
EXTRA = [  # outside the survey's 43
    ('mugs/hartschooldistrictlogo.jpg', 'hd20200112-district-kuhlman', 'import', 'District logo at the head of the district release. Decorative; outside the survey\'s 43. Status is a proposal for Nathan.'),
    ('vinceferry2020.jpg', 'hd20200406-district-ferry', LAT, 'Photograph of Principal Ferry on the district page; no credit printed. Outside the survey\'s 43. Status is a proposal for Nathan (uncredited, so held like the uncredited vigil photograph).'),
    ('vinceferry2020_large.jpg', 'hd20200406-district-ferry', LAT, 'Larger version linked from vinceferry2020.jpg. Outside the survey\'s 43.'),
]

cap_index = {}
for pc in pieces_out:
    for c in pc['captions']:
        cap_index[c['image']] = (c['caption'], c['credit'])

images_out = []
def add_image(f, piece, status, note, path=None, survey=True):
    path = path or (G + f)
    d = sha(path); inm, st = manifest_status(path, d); w, h = dims(path)
    rec = {'file': rel(path), 'sha256': d, 'inManifest': inm, 'manifestCheck': st,
           'width': w, 'height': h, 'piece': piece, 'status': status, 'inSurveyCount': survey}
    if status == 'never':
        rec['credit'] = None; rec['caption'] = None
        rec['captionNotCopied'] = True
    else:
        cap, cred = cap_index.get(f, (None, None))
        rec['credit'] = cred; rec['caption'] = cap
    if note: rec['notes'] = note
    images_out.append(rec)

for f, piece, status, note in IMAGES: add_image(f, piece, status, note)
for f, piece, status, note in EXTRA: add_image(f, piece, status, note, survey=False)

# the City gallery embedded on page 6
SC = BASE + 'files/sc1903/'
gal = list(dict.fromkeys(re.findall(r'(data1/images/[^"]+)"', open(SC + 'sc1903.htm', encoding='utf-8', errors='replace').read())))
for g in gal:
    add_image(os.path.basename(g), 'sg20191117-vigil-video', 'scvtv-city',
              'City of Santa Clarita vigil gallery (files/sc1903/sc1903.htm, iframe on page 6), credited on the page as "Photos: City of Santa Clarita." No caption per photograph (slide titles are file labels). Not in the survey\'s count.',
              path=SC + g, survey=False)
for r in images_out:
    if r['file'].startswith('scvhistory/files/sc1903/'):
        r['credit'] = 'Photos: City of Santa Clarita.'; r['caption'] = None

document_files = []
pdf = BASE + 'hd20200112.pdf'; d = sha(pdf); inm, st = manifest_status(pdf, d)
document_files.append({'file': rel(pdf), 'sha256': d, 'inManifest': inm, 'manifestCheck': st,
                       'bytes': os.path.getsize(pdf), 'piece': 'hd20200112-district-kuhlman', 'status': 'import',
                       'notes': 'The district\'s original PDF, linked from the page as "Open original .pdf version".'})

# ---------------------------------------------------------------- videos
videos = [
    {'url': 'https://scvtv.com/vid/Saugusshooting_20191114.mp4', 'page': '/scvhistory/sg20191114shs.htm', 'title': 'November 14, 2019, approximately 11 a.m.', 'poster': 'gif/sg20191114shs_video02.jpg', 'status': 'not in the mirror; no record until SCVTV supplies the file and a transcript (D11)'},
    {'url': 'https://scvtv.com/vid/saugusshooting_secondupdate_11142019.mp4', 'page': '/scvhistory/sg20191114shs.htm', 'title': 'Above: November 14, 2019, approximately 4 p.m.', 'poster': 'gif/sg20191114shs_video01.jpg', 'status': 'not in the mirror; no record until SCVTV supplies the file and a transcript (D11)'},
    {'url': 'https://scvtv.com/vid/shsshootingaustin_20191115.mp4', 'page': '/scvhistory/austindave20191115shs.htm', 'title': 'We Stand Together, #SaugusStrong', 'poster': 'gif/austindave20191115shs.jpg', 'status': 'not in the mirror; no record until SCVTV supplies the file and a transcript (D11)'},
    {'url': 'https://scvtv.com/vid/saugusshooting_pressconference20191115.mp4', 'page': '/scvhistory/scvtv20191115shs.htm', 'title': 'Press Conference at SCV Sheriff Station.', 'poster': 'gif/scvtv20191115shs_video.jpg', 'status': 'not in the mirror; no record until SCVTV supplies the file and a transcript (D11)'},
    {'url': 'https://scvtv.com/vid/saugusvigil_20191118.mp4', 'page': '/scvhistory/sg20191117shs.htm', 'title': '#SaugusStrong Vigil.', 'poster': 'gif/saugusstrongvigil20191117.jpg', 'status': 'not in the mirror; no record until SCVTV supplies the file and a transcript (D11)'},
    {'url': 'https://scvtv.com/vid/addisonkoegle20191117.mp4', 'page': '/scvhistory/addisonkoegle20191117.htm', 'title': 'Video Message from Addison Koegle', 'poster': 'https://scvtv.com/vid/image/addisonkoegle20191117.jpg (on scvtv.com, not in the mirror)', 'status': 'excluded (D4: Koegle family privacy request); not in the mirror'},
    {'url': 'https://scvtv.com/vid/addisonkoegle20191117_full.mp4', 'page': '/scvhistory/addisonkoegle20191117.htm', 'title': 'Download highest quality video', 'poster': None, 'status': 'excluded (D4: Koegle family privacy request); not in the mirror'},
]

# ---------------------------------------------------------------- disagreements
SENT = re.compile(r'(?<!Capt\.)(?<!Dr\.)(?<!Mr\.)(?<!Mrs\.)(?<!Gov\.)(?<!St\.)(?<!Nov\.)(?<!Dec\.)(?<!Oct\.)(?<=[.!?"])\s+(?=[A-Z"])')

def quote(key, needle):
    pc = next(p for p in pieces_out if p['key'] == key)
    hay = '\n'.join([pc['headline'] or '', pc['deck'] or '', pc['body']])
    for para in hay.split('\n'):
        if needle in para:
            # the sentence holding the needle
            for s in SENT.split(para):
                if needle in s: return s
            return para
    raise SystemExit(f'quote not found: {key}: {needle}')

def Q(key, needle): return {'piece': key, 'quote': quote(key, needle)}

def QP(key, needle):
    pc = next(p for p in pieces_out if p['key'] == key)
    return {'piece': key, 'quote': next(x for x in pc['body'].split('\n\n') if needle in x), 'scope': 'whole paragraph'}

timeline_line = 'November 14: Gunman, age 16, slays 2 fellow Saugus High School students, wounds 4 others before turning gun on himself [link].'
tl = open(BASE + 'timeline.htm', 'rb').read().decode('latin-1')
assert 'Gunman, age 16, slays 2 fellow Saugus High School students, wounds 4 others' in tl

disagreements = [
    {'topic': 'The count of the wounded',
     'summary': 'Written before the shooter died on 15 November, the first reports count him among the wounded (four wounded besides the two dead, or six gunshot victims). Later reports give three wounded besides the shooter; the Signal of 19 November counts him among the dead.',
     'quotes': [Q('sg20191114-signal-holt', 'four others, all students'),
                {'piece': 'page title, /scvhistory/sg20191114shs.htm', 'quote': 'Breaking News: 2 Students Killed, 4 Wounded (11/14/2019).'},
                Q('sg20191114-signal-holt', 'found six gunshot victims'),
                Q('sg20191114-signal-holt', 'shoot and wound five people'),
                {'piece': 'timeline.htm, 2019 (Leon Worden\'s chronology; not one of the pieces)', 'quote': timeline_line},
                Q('lat20191115-gerber', 'Three others wounded'),
                Q('lat20191116a-shining-light', 'He wounded five students'),
                Q('lat20191116c-firearms-seized', 'wounding three others'),
                Q('sg20191119-signal-murga', 'three teenagers dead and three others wounded')]},
    {'topic': 'Gracie Muehlberger\'s age',
     'summary': 'The first reports of 14 and 15 November give 16; the coroner\'s identification, the vigil coverage and her parents\' letter give 15 (born 10 October 2004).',
     'quotes': [Q('sg20191114-signal-holt', 'a 16-year-old female student'),
                Q('sg20191114-signal-holt', 'The 16-year-old girl died at 9:23 a.m.'),
                Q('lat20191115-gerber', 'A 16-year-old girl and a 14-year-old boy died'),
                Q('lat20191116b-victims-identified', 'Gracie Anne Muehlberger, 15, died'),
                Q('bryanmuehlberger20191117-letter', 'born on October 10th, 2004'),
                Q('bryanmuehlberger20191117-letter', 'barely over 15')]},
    {'topic': 'The letter as quoted at the vigil and as printed',
     'summary': 'The Signal\'s quotation of the Muehlberger letter differs in wording from the letter printed on page 8 (released by the family on 19 November). The letter as printed is the family\'s text.',
     'quotes': [QP('sg20191117-signal-alvarenga', 'She was only barely 15'),
                Q('bryanmuehlberger20191117-letter', 'She was only barely over 15'),
                Q('bryanmuehlberger20191117-letter', 'too short of time'),
                Q('bryanmuehlberger20191117-letter', 'Gracie, will remain alive')]},
    {'topic': 'Where the wounded were taken',
     'summary': 'The L.A. Times of 15 November says all six were taken to Henry Mayo; the Signal reports two students airlifted to Providence Holy Cross, where the two wounded girls were treated.',
     'quotes': [Q('lat20191115-gerber', 'all transported to the Henry Mayo hospital emergency room'),
                Q('lat20191115-gerber', 'Three other students also were taken to Henry Mayo'),
                Q('sg20191114-signal-holt', 'Providence Holy Cross confirmed two others were airlifted'),
                Q('sg20191119-signal-murga', 'was airlifted Thursday morning to Providence Holy Cross Medical Center')]},
    {'topic': 'Whether the shooter was taken into custody',
     'summary': 'The morning story reports a manhunt ending in custody; the afternoon report and the Sheriff\'s Department say he was found among the wounded in the quad.',
     'quotes': [Q('sg20191114-signal-holt', 'confirmed the suspect was in custody'),
                Q('sg20191114-signal-holt', 'was later identified as one of the victims who was found in the quad')]},
    {'topic': 'The injured student\'s message at the vigil',
     'summary': 'The Signal and the site call it a video message; the L.A. Times calls it an audio message, and the two papers quote it differently. Recorded for the record only: the student and her family asked for privacy (D4).',
     'quotes': [Q('sg20191119-signal-murga', 'I wanted to let everyone know'),
                Q('lat20191118a-thousands-mourn', 'addressed the crowd through an audio message'),
                Q('lat20191118a-thousands-mourn', 'I\'m doing well and I\'m home with my family')]},
    {'topic': 'The principal\'s words at the vigil',
     'summary': 'The two papers quote the same line differently.',
     'quotes': [Q('sg20191117-signal-alvarenga', 'Strength is the ability to welcome our tears'),
                Q('lat20191118a-thousands-mourn', 'is the ability to welcome our tears in the midst of our pain')]},
    {'topic': 'Names of the hospitals and the principal',
     'summary': 'Variants, not contradictions: "Henry Mayo Newhall Hospital" and "Henry Mayo Newhall Memorial Hospital"; "Providence Holy Cross Hospital" and "Providence Holy Cross Medical Center"; "Vince Ferry" and "Vincent Ferry".',
     'quotes': [Q('sg20191114-signal-holt', 'Providence Holy Cross Hospital in Mission Hills'),
                Q('lat20191115-gerber', 'Henry Mayo Newhall Memorial Hospital'),
                Q('lat20191118a-thousands-mourn', 'Principal Vincent Ferry'),
                Q('sg20191117-signal-alvarenga', 'Saugus Principal Vince Ferry')]},
]

# ---------------------------------------------------------------- address occurrences (not copied)
# generic: a numbered block, or a capitalised name followed by a street suffix (the street itself is not written here)
ADDR = re.compile(r'\b\d+ block of\b|\b(?:[A-Z][a-z]+ ){1,3}(?:Drive|Street|Avenue|Road|Lane|Court|Circle|Boulevard|Parkway|Way)\b')
address_occurrences = []
for pc in pieces_out:
    for i, p in enumerate(pc['body'].split('\n\n')):
        if ADDR.search(p):
            sents = SENT.split(p)
            sn = next(k for k, s in enumerate(sents, 1) if ADDR.search(s))
            address_occurrences.append({'piece': pc['key'], 'where': f'body, paragraph {i + 1}, sentence {sn} (last sentence of the paragraph)' if sn == len(sents) else f'body, paragraph {i + 1}, sentence {sn}',
                                        'what': 'the street name of the family home (no house number)', 'redacted': False})
# captions of images (all pages, including the never images whose captions are not copied)
for name, _ in PAGES:
    for i, p in enumerate(paras(name)):
        if ADDR.search(p) and not any(p in pc['body'] for pc in pieces_out):
            address_occurrences.append({'piece': 'sg20191114-signal-holt' if name == 'sg20191114shs' else name,
                                        'where': 'caption printed under sg20191114shs04.jpg (a D6 "never" image; covers 04 and 05), the caption\'s only sentence' if name == 'sg20191114shs' else f'page text block {i}',
                                        'what': 'the hundred-block and street name of the family home', 'redacted': False,
                                        'note': 'Not copied into this extract: the caption belongs to a never image.'})

out = {
    'extracted': '2026-10-05',
    'extractedBy': 'scripts/import/extract_saugus_high_2019_sources.py, Claude Code',
    'source': 'Reggie mirror, ' + MIRROR + ' (read-only); manifests ' + ', '.join(os.path.basename(m) for m in MANIFESTS),
    'rulings': 'Nathan, 5 October 2026: D2 (the shooter named only inside verbatim source text), D3 (Gracie Muehlberger and Dominic Blackwell named), D4 (the wounded named only where sources name them; the Koegle video excluded), D6 (six images never imported; captions not copied), D8 (private contact details replaced with "[contact details withheld]").',
    'conventions': 'Bodies are verbatim, paragraphs separated by a blank line, no HTML, entities decoded (em dashes and soft hyphens kept as printed; italics not marked). Headlines, decks, bylines and datelines are as printed, each on its own field. "Click to enlarge." after a caption is site UI and recorded as printedAlso. Paragraph numbers in redactions and addressOccurrences count body paragraphs from 1.',
    'pages': pages_out,
    'pieces': pieces_out,
    'images': images_out,
    'documentFiles': document_files,
    'videos': videos,
    'disagreements': disagreements,
    'redactions': all_redactions,
    'addressOccurrences': address_occurrences,
    'otherContactScan': {'patterns': 'email addresses, LinkedIn addresses, phone numbers, in all 19 bodies, captions, headlines and bylines',
                         'leftInText': [{'piece': k, 'paragraph': n, 'what': w} for k, n, w in scan]},
    'verbatimCheck': check,
}
with open(OUT, 'w', encoding='utf-8') as f:
    json.dump(out, f, ensure_ascii=False, indent=1)
print('pieces', len(pieces_out), 'images', len(images_out), 'redactions', len(all_redactions))
for c in check:
    print(c['piece'], c['paragraphs'], c['words'], c['failedParagraphs'], c['failedAfterRedaction'])
print('address', address_occurrences)
print('scan', scan)
