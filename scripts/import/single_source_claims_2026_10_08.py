# READ ONLY. Every claim on a person record that rests on a single source (Nathan, 8 October 2026, item 11: "After the
# Perkins trust rule and the del Valle dates I want to know how much of the archive stands on one leg").
#
# Reads inventory/review/overnight-2026-10-08/_person-claims-dump.json (written by dump_person_claims_2026_10_08.php)
# and writes single-source-claims-2026-10-08.json beside it. The report (.md) is written from that JSON.
#
# What a claim is, here:
#   body       a citation span in a person's published body: the text from the previous marker group (or the paragraph's
#              start) to a marker group such as [1][3]. The writer's own unit of citation.
#   field      birthDate, deathDate, birthplace, burialPlace: located in the body's spans (year plus a born/died word, or
#              the place's first word), else in a footnote, else no citation located.
#   kin        each spouseOf, childOf, siblingOf link: spans naming the relative.
#   calendar   each recordDates row ticked Confirmed on a person (these publish to On This Day).
#   term       each officeHolding, affiliation and education record pointing at a person: all its footnotes.
#   candidacy  each candidacy record: its footnotes.
# Independence (docs/PROFILES.md, the Perkins reliability rule, Nathan 3 and 4 October 2026):
#   Perkins and Reynolds are one source. A Leon Worden column cited beside either is the same account unless its
#   footnote names another source. CEDA is a compilation of the County's returns, so CEDA and the County's statement are
#   one source. The same URL (the live page and its Wayback copy) is one source. A note that names no document (a term
#   length from statute, "the seat was next filled at...") is reasoning, not a witness, and is not counted.
# What this cannot see: whether two documents the archive counts as independent copy each other (a City biography and a
# campaign biography written by the same person; two newspapers carrying one wire story). It counts citations, not what
# the cited sources say, and does not open any source.
import json, os, re, sys, collections

HERE = os.path.dirname(os.path.abspath(__file__))
ROOT = os.path.dirname(os.path.dirname(HERE))
sys.path.insert(0, HERE)
from _reads import reads

DUMP = os.path.join(ROOT, 'inventory/review/overnight-2026-10-08/_person-claims-dump.json')
OUT = os.path.join(ROOT, 'inventory/review/overnight-2026-10-08/single-source-claims-2026-10-08.json')
reads([
    ('file', 'the dump of person, term, affiliation, education and candidacy records', DUMP),
    ('record', 'footnote texts in those records', 'the sources they cite', 'not read: the census counts citations and their independence by the archive\'s own rules, not what the sources say'),
])

d = json.load(open(DUMP))

# Source families. Order matters: the first pattern that matches names the family.
FAMILIES = [
    (r'Perkins,? "|Perkins\'s? (\d{4}|transcri|notes?)|quoted in A\.? ?B\.? Perkins|perkins-[a-z0-9-]+\.htm|signal/perkins/|Perkins \(19\d\d\)|by A\.? ?B\.? Perkins|Perkins, (record|Story|19\d\d)', 'Perkins/Reynolds (one source, PROFILES.md)'),
    (r'Jerry Reynolds|Reynolds, History of the Santa Clarita|signal/reynolds/', 'Perkins/Reynolds (one source, PROFILES.md)'),
    (r'California Elections Data Archive|\bCEDA\b', 'County returns (CEDA or the County\'s statement)'),
    (r'Registrar-Recorder|Statement of Votes Cast|County of Los Angeles.{0,40}(returns|canvass|results|election)|Board of Supervisors.{0,60}(canvass|declar)', 'County returns (CEDA or the County\'s statement)'),
    (r'council ledger|Santa Clarita City Council \d{4}', 'Leon Worden\'s City Council ledger'),
    (r'Leon Worden\'s roster of the Hart board|hartschoolboardmembers|^The roster (heads|prints|lists|stops)|^The roster gives(?!.*council ledger)', 'Hart board roster (Leon Worden)'),
    (r'Secretary of State, Statements? of Vote|Secretary of State, (special|primary|general) election|sos\.ca\.gov', 'California Secretary of State, Statement of Vote'),
    (r'Secretary of the Senate, Record of|Record of State Senators|Record of Members of the Assembly', 'Secretary of the Senate, Record of Members'),
    (r'Biographical Directory of the United States Congress|bioguide', 'Biographical Directory of the U.S. Congress'),
    (r'wikipedia\.org|\bWikipedia\b', 'Wikipedia'),
    (r'Find a Grave|findagrave', 'Find a Grave'),
    (r'Pen Pictures From the Garden', 'Pen Pictures from the Garden of the World (1888)'),
    (r'Carl Boyer.{0,40}Formation', 'Carl Boyer, Santa Clarita: The Formation'),
    (r'ecpp\.ucr\.edu|Early California Population Project', None),  # one per record number, keyed below
    (r'City of Santa Clarita, (his|her) biography|santaclarita\.gov/city-council/', 'City of Santa Clarita biographies (the City\'s own account of a member)'),
    (r'Resolution No\. ?[\d-]+|declaring the results', 'City Council resolutions declaring results'),
    (r'\bSB 634\b|Senate Bill 634', 'SB 634 (2017)'),
    (r'Leon Worden\'s note on (his |the )?City Council portrait', 'Leon Worden\'s notes on the City Council portraits'),
]
# Reasoning that names no document: the archive's own inference, a district line, a term rule.
REASON = re.compile(r"^(The term|The seat|The district moved|Under the \d{4}|He |She |They |His term|Her term|The agency ended|The resolution gives|From \d{4}|The term's end|No declaring document|The next board|The district's directors took)", re.I)
RULE = re.compile(r'^(California )?(Education|Government|Elections|Water) Code|holds office "for a term|^The agency\'s directors take office|^The new board took office|^The first council\'s terms were staggered|^The City gives each mayoralty', re.I)

def norm_url(u):
    u = re.sub(r'^https?://(web\.archive\.org/web/\d+[a-z_]*/)?', '', u.strip().rstrip('.,;:)"”'))
    u = re.sub(r'^https?://', '', u); u = re.sub(r'^www\.', '', u)
    return u.split('#')[0].rstrip('/').lower()

def families_of(note):
    """The source families a footnote names. Empty: the note names no document (reasoning, a rule)."""
    n = note or ''
    if not n.strip():
        return set(), 'empty'
    if RULE.search(n) and not re.search(r'https?://|CEDA|Statement of Vote|roster', n):
        return set(), 'rule'
    fam = set()
    for pat, name in FAMILIES:
        if re.search(pat, n):
            if name is None:
                for m in re.findall(r'records/\w+/(\d+)|record (\d+)', n):
                    fam.add('ECPP record ' + (m[0] or m[1]))
            else:
                fam.add(name)
    # A note already named by its family is one source, whatever URLs it gives for it.
    urls = [] if fam else [norm_url(u) for u in re.findall(r'https?://\S+', n)]
    for u in urls:
        if any(k in u for k in ('reynolds', 'perkins', 'wikipedia', 'findagrave', 'bioguide', 'ecpp')):
            continue
        fam.add('URL ' + u)
    for num in re.findall(r'#(\d{3,6})', n):
        if not fam:
            fam.add('archive record #' + num)
    if fam:
        return fam, 'source'
    # No pattern, no URL: a named work ("Title," Publisher) or an archive record still counts as a source.
    m = re.search(r'"([^"]{6,120}),?"', n)
    if m or re.search(r'Archive record|LW\d{3,4}|The Signal|Los Angeles Times|Daily News|Canyon Call|Newhall Signal|Mighty 1220|SCVTV|SCVNews|KHTS|Hometown|\bminutes\b|\bpatent\b|\(([A-Z]{2}\d{4})\)', n):
        key = (m.group(1) if m else n[:80]).strip().lower()
        return {'work ' + key}, 'source'
    if REASON.search(n):
        return set(), 'note'
    # A citation that opens with its author or publisher: "Census Bureau, 2020 Census ...", "NCES Common Core of Data, ...".
    if re.match(r"^[A-Z][\w.'&-]*( [A-Z][\w.'&-]*| of| and| the){0,8},", n) and re.search(r'\b1[6-9]\d\d\b|\b20\d\d\b', n):
        return {'work ' + ','.join(n.split(',')[:2]).strip().lower()[:80]}, 'source'
    return set(), 'note'

WORDEN = re.compile(r'Leon Worden|\bLW\d{3,4}\b|lw\d{3,4}\.htm', re.I)
PR = 'Perkins/Reynolds (one source, PROFILES.md)'
# A Worden footnote that says what the column drew on names another source (PROFILES.md, 4 October 2026).
NAMES_OTHER = re.compile(r'drawing on|draws on|citing|quoting|quotes|based on|letter|register|minutes|census|deed|certificate|interview', re.I)

def merge_families(notes):
    """Families across a claim's footnotes, with the independence rules applied."""
    fams, per = set(), []
    for n in notes:
        f, kind = families_of(n)
        per.append((n, f, kind))
        fams |= f
    if PR in fams:
        # A Worden column beside Perkins/Reynolds counts only where its footnote names something else.
        drop = set()
        for n, f, kind in per:
            if WORDEN.search(n or '') and PR not in f and not NAMES_OTHER.search(n or ''):
                others = [x for x in f if not (x.startswith('URL scvhistory.com') or x.startswith('work '))]
                if not others:
                    drop |= f
        fams -= drop
        if drop:
            fams.add(PR)
    return fams, per

def text(s):
    s = re.sub(r'<[^>]+>', ' ', s or '')
    return re.sub(r'[ \t]+', ' ', s)

def spans(body):
    """Citation spans: (text, [footnote numbers]) and the uncited tails."""
    out, tails = [], []
    for para in re.split(r'\n\s*\n|</p>', body or ''):
        para = text(para).strip()
        if not para:
            continue
        pos = 0
        for m in re.finditer(r'((?:\[\d+\])+)', para):
            seg = para[pos:m.start()].strip()
            nums = re.findall(r'\[(\d+)\]', m.group(1))
            if seg:
                out.append((seg, nums))
            elif out:
                out[-1] = (out[-1][0], sorted(set(out[-1][1]) | set(nums), key=int))
            pos = m.end()
        tail = para[pos:].strip()
        if len(tail) > 3:
            tails.append(tail)
    return out, tails

claims = []
def add(person, kind, what, value, notes, extra=None):
    fams, per = merge_families([n for n in notes if n])
    c = {'personId': person['id'], 'person': person['title'], 'kind': kind, 'claim': what, 'value': value,
         'sources': sorted(fams), 'sourceCount': len(fams),
         'footnotes': [{'note': n[:400], 'counted': sorted(f), 'kind': k} for n, f, k in per]}
    if extra:
        c.update(extra)
    claims.append(c)
    return c

persons = {p['id']: p for p in d['persons']}
elections = {e['id']: e for e in d.get('elections', [])}
PUB = ('legacy-leon', 'editorial-2026')
uncited_tails, unfootnoted_bodies = [], []
for p in d['persons']:
    fn = {str(f.get('number')): f.get('note') for f in (p.get('footnotes') or []) if f.get('note')}
    body = p.get('body') or ''
    sp, tails = spans(body) if body else ([], [])
    auth = p.get('bodyAuthorship') or ''
    published = bool(body) and auth in PUB
    if body and not sp:
        unfootnoted_bodies.append({'personId': p['id'], 'person': p['title'], 'bodyAuthorship': auth or 'unset', 'published': published, 'words': len(text(body).split())})
    if published:
        for seg, nums in sp:
            add(p, 'body', seg, None, [fn.get(n) for n in nums], {'markers': nums, 'missingFootnotes': [n for n in nums if n not in fn]})
        for t in tails:
            if sp:
                uncited_tails.append({'personId': p['id'], 'person': p['title'], 'text': t[:300]})
    # Fields.
    def find_spans(pred):
        return [s for s in sp if pred(s[0])]
    for field, words in (('birthDate', r'\bborn\b|\bbirth\b|baptiz'), ('deathDate', r'\bdied\b|\bdeath\b|\bburied\b|killed')):
        v = p.get(field)
        if not v:
            continue
        ev = p.get('birthEvidence' if field == 'birthDate' else 'deathEvidence') or ''
        years = re.findall(r'\b(1[6-9]\d\d|20\d\d)\b', v)
        # Prefer spans holding the whole date as printed, either order ("April 28, 1997" or "28 April 1997").
        exact = []
        m = re.match(r'^(\w+) (\d{1,2}), (\d{4})$', v.strip())
        if m:
            exact = [v.strip(), f'{m.group(2)} {m.group(1)} {m.group(3)}']
        hits = find_spans(lambda s: any(x in s for x in exact)) if (published and exact) else []
        if not hits:
            hits = find_spans(lambda s: years and any(y in s for y in years) and re.search(words, s, re.I)) if published else []
        how = 'body span'
        notes = []
        for s, nums in hits:
            notes += [fn.get(n) for n in nums]
        if not hits:
            notes = [n for n in fn.values() if years and any(y in n for y in years) and re.search(words, n, re.I)]
            how = 'footnote naming the date' if notes else 'no citation located'
        c = add(p, 'field', field, v, notes, {'evidence': ev, 'located': how})
    for field in ('birthplace', 'burialPlace'):
        v = (p.get(field) or '').strip()
        if not v or v.lower() in ('unknown', 'not known'):
            continue
        key = re.split(r'[,(]', v)[0].strip()
        key = re.sub(r'^(the|near|probably)\s+', '', key, flags=re.I)
        hits = find_spans(lambda s: key and key.lower() in s.lower()) if published else []
        notes = []
        for s, nums in hits:
            notes += [fn.get(n) for n in nums]
        how = 'body span'
        if not hits:
            notes = [n for n in fn.values() if key and key.lower() in n.lower()]
            how = 'footnote naming the place' if notes else 'no citation located'
        add(p, 'field', field, v, notes, {'evidence': p.get('burialEvidence', '') if field == 'burialPlace' else '', 'located': how})
    # Kinship.
    for rel in ('spouseOf', 'childOf', 'siblingOf'):
        for r in (p.get(rel) or []):
            t = r['title']
            parts = [x for x in re.sub(r'\b(Jr|Sr|III|II)\.?', '', t).split() if len(x) > 2]
            first, last = (parts[0], parts[-1]) if parts else ('', '')
            hits = find_spans(lambda s: t in s or (first in s and last in s) or (first and re.search(r'\b' + re.escape(first) + r'\b', s) and re.search(r'wife|husband|son|daughter|father|mother|brother|sister|married|widow', s, re.I))) if published else []
            notes = []
            for s, nums in hits:
                notes += [fn.get(n) for n in nums]
            add(p, 'kin', rel, t, notes, {'relativeId': r['id'], 'located': 'body span' if hits else 'no citation located'})
    # Confirmed calendar rows.
    for r in (p.get('recordDates') or []):
        if not r.get('confirmed') or r.get('printed') is None:
            continue
        pr = r['printed']
        variants = [pr]
        iso = r.get('iso') if isinstance(r.get('iso'), str) else ''
        # Craft stores the day at local midnight shifted to UTC, so the stored date can be the day before.
        mm = re.match(r'(\d{4})-(\d\d)-(\d\d)', iso or '')
        months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
        mp = re.search(r'(%s)[a-z]*\.? (\d{1,2}),? (\d{4})' % '|'.join(x[:3] for x in months), pr)
        if mp:
            mon = [x for x in months if x.startswith(mp.group(1))][0]
            variants += [f'{mon} {int(mp.group(2))}, {mp.group(3)}', f'{int(mp.group(2))} {mon} {mp.group(3)}']
        hits = find_spans(lambda s: any(v in s for v in variants)) if published else []
        if not hits and published and mm:
            word = r'\bborn\b|\bbirth' if re.search(r'born|birth', r.get('label') or '', re.I) else (r'\bdie|\bdeath|buried' if re.search(r'die|death', r.get('label') or '', re.I) else None)
            if word:
                hits = find_spans(lambda s: mm.group(1) in s and re.search(word, s, re.I))
        notes = []
        for s, nums in hits:
            notes += [fn.get(n) for n in nums]
        add(p, 'calendar', r.get('label') or '', pr, notes, {'located': 'body span' if hits else 'no citation located'})

def person_of(e, key):
    v = e.get(key) or []
    return v[0] if v else None

for sec, key, label in (('officeHoldings', 'holdingPerson', 'term'), ('affiliations', 'affiliationPerson', 'affiliation'), ('educations', 'educationPerson', 'education'), ('candidacies', 'candidacyPerson', 'candidacy')):
    for e in d[sec]:
        pr = person_of(e, key)
        if not pr:
            continue
        pseudo = {'id': pr['id'], 'title': pr['title']}
        notes = [f.get('note') for f in (e.get('footnotes') or []) if f.get('note')]
        if sec == 'candidacies':
            # The result's source may sit on the election record rather than the candidacy.
            for el in (e.get('candidacyElection') or []):
                notes += [f.get('note') for f in (elections.get(el['id'], {}).get('footnotes') or []) if f.get('note')]
        ev = e.get('startEvidence') or e.get('outcomeEvidence') or e.get('educationEvidence') or ''
        val = ' to '.join(x for x in (e.get('termStart'), e.get('termEnd')) if x) or e.get('educationYears') or e.get('outcome') or ''
        extra = {'recordId': e['id'], 'recordSection': sec, 'evidence': ev, 'endEvidence': e.get('endEvidence', ''), 'howEnded': e.get('howEnded', ''), 'footnotesOn': len(e.get('footnotesOn') or [])}
        add(pseudo, label, e['title'], val, notes, extra)

# Totals.
tot = collections.Counter(); one = collections.Counter(); zero = collections.Counter()
for c in claims:
    tot[c['kind']] += 1
    if c['sourceCount'] == 1:
        one[c['kind']] += 1
    elif c['sourceCount'] == 0:
        zero[c['kind']] += 1
famOne = collections.Counter(c['sources'][0] for c in claims if c['sourceCount'] == 1)
res = {'refTitles': d.get('refTitles', {}), '_built': '2026-10-08', '_script': 'scripts/import/single_source_claims_2026_10_08.py',
       'totals': {k: {'claims': tot[k], 'oneSource': one[k], 'noSource': zero[k]} for k in tot},
       'oneSourceByFamily': famOne.most_common(),
       'unfootnotedBodies': unfootnoted_bodies, 'uncitedTails': uncited_tails, 'claims': claims}
json.dump(res, open(OUT, 'w'), indent=1, ensure_ascii=False)
print('claims by kind: total / one source / no source counted')
for k in tot:
    print(f'  {k:12} {tot[k]:5} {one[k]:5} {zero[k]:5}')
print(f'  {"all":12} {sum(tot.values()):5} {sum(one.values()):5} {sum(zero.values()):5}')
print('single-source claims by the source they rest on (top 25):')
for f, n in famOne.most_common(25):
    print(f'  {n:5}  {f}')
print(f'bodies with no footnote markers: {len(unfootnoted_bodies)}; uncited tails in footnoted bodies: {len(uncited_tails)}')
print('wrote', OUT)
