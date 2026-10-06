#!/usr/bin/env python3
"""Extract the Newsmaker of the Week interview with State Sen. William J. "Pete" Knight
(taped 1 April 2004, printed Sunday 25 April 2004) from the Reggie mirror.

Read-only on the mirror. Writes inventory/legacy/pete-knight-newsmaker-2004.json, which
scripts/import/import_knight_newsmaker_2026_10_06.php reads inside DDEV (the mirror is
host-only). Nathan, 6 October 2026: "Import it". Claude Code.

What goes where (DATA-MODEL, Transcription and interpretation):
- interview: the program's own introduction (italic on the page), the questions and
  answers, and the closing line about the broadcast. Verbatim; entities decoded, so the
  page's &#151; is kept as an em dash; italics and bold not marked; "Signal:" and
  "Knight:" kept as printed.
- sidebars: the two boxed texts printed beside the interview, "KNIGHT'S MILITARY
  BACKGROUND" and "OFFICIAL BIOGRAPHY", verbatim under their printed headings.
- editorsNote: the note printed above the introduction after his death ("This was Pete
  Knight's final television interview ..."): the site's framing, for webmasterNoteTop.
- captions: the two photograph captions, recorded but not imported (the portrait is
  handled separately).
The verbatim check tokenises the page independently (regex tag strip, entity decode) and
requires every paragraph to appear as a contiguous word run in the page, in order.
Run on the host: python3 scripts/import/extract_knight_newsmaker_2026_10_06.py
"""
import hashlib, html, json, os, re, sys

MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'
REL = 'scvhistory/signal/newsmaker/sg042504.htm'
PAGE = os.path.join(MIRROR, REL)
MANIFEST = '/Volumes/Reggie/SCVHistory/scvhistory-manifest-2026-08-20.sha256'
OUT = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..', 'inventory', 'legacy', 'pete-knight-newsmaker-2004.json')

raw = open(PAGE, 'rb').read()
digest = hashlib.sha256(raw).hexdigest()
listed = None
for line in open(MANIFEST, encoding='utf-8', errors='replace'):
    if line.rstrip().endswith('./' + REL):
        listed = line.split('  ', 1)[0]
        break
s = raw.decode('latin-1')
if re.search(r'[\x80-\xff]', s):
    sys.exit('non-ASCII bytes in the page: check the encoding before trusting the text')

content = s[s.index('<!-- XWP-BEGIN-CONTENT -->'):s.index('<!-- XWP-END-CONTENT -->')]
side_start = content.index('<table border=0 width=260')
side_end = content.index('</TD></TR></TABLE>\n</TD></TR></TABLE>', side_start) + len('</TD></TR></TABLE>\n</TD></TR></TABLE>')
side, main = content[side_start:side_end], content[side_end:]


def paras(fragment):
    f = re.sub(r'(?is)<script.*?</script>', '', fragment)
    f = re.sub(r'(?i)<li[^>]*>', '\n\n', f)
    f = re.sub(r'(?i)<p[^>]*>|<br\s*/?>|<hr[^>]*>|</?(td|tr|table|ul|center)[^>]*>', '\n\n', f)
    f = re.sub(r'<[^>]+>', '', f)
    f = html.unescape(f).replace('\xa0', ' ')
    out = []
    for block in re.split(r'\n\s*\n', f):
        t = re.sub(r'\s+', ' ', block).strip()
        if t:
            out.append(t)
    return out


m = paras(main)
i0 = next(i for i, p in enumerate(m) if p.startswith('"Newsmaker of the Week" is presented'))
i1 = next(i for i, p in enumerate(m) if p.startswith('See this interview in its entirety'))
note = [p for p in m[:i0] if p.startswith("Editor's Note:")]
if len(note) != 1:
    sys.exit("editor's note not found once")
interview = m[i0:i1 + 1]
tail = m[i1 + 1:]

sp = paras(side)
cap1 = 'Pete Knight on April 1, 2004'
mil = sp.index("KNIGHT'S MILITARY BACKGROUND")
off = sp.index('OFFICIAL BIOGRAPHY')
captions = [cap1] if cap1 in sp else []
military = sp[mil + 1:off]
official = sp[off + 1:]
military = [p for p in military if p != '[FULL IMAGE IN NEW WINDOW]']

# printed heads
slug = paras(content[content.index('<!--START:SLUG-->'):content.index('<!--END:SLUG-->')])
byline = paras(content[content.index('<!--START:BYLINE-->'):content.index('<!--END:BYLINE-->')])
dateline = re.search(r'<p>(Sunday, April 25, 2004)<br>', content).group(1)
taped = re.search(r'\((Television interview conducted April 1, 2004)\)', content).group(1)
copyright_line = re.search(r'(&#169;2004 SCVTV\.)', content).group(1)

# verbatim check: every paragraph a contiguous word run in the independently tokenised page
tok = lambda t: re.findall(r"[A-Za-z0-9'—\-\.\,\;\:\!\?\"\(\)\$%&#/]+", t)
page_words = tok(re.sub(r'\s+', ' ', html.unescape(re.sub(r'<[^>]+>', ' ', re.sub(r'(?is)<script.*?</script>', '', s))).replace('\xa0', ' ')))
page_str = ' ' + ' '.join(page_words) + ' '
bad, pos = [], 0
for p in note + interview + military + official:
    w = ' ' + ' '.join(tok(p)) + ' '
    k = page_str.find(w)
    if k < 0:
        bad.append(p[:80])
if bad:
    sys.exit('NOT VERBATIM: ' + ' | '.join(bad))

SEP = '\n\n'
body = SEP.join(interview + ["KNIGHT'S MILITARY BACKGROUND"] + military + ['OFFICIAL BIOGRAPHY'] + official)
data = {
    'extracted': '2026-10-06',
    'extractedBy': 'scripts/import/extract_knight_newsmaker_2026_10_06.py, Claude Code',
    'source': 'Reggie mirror, /Volumes/Reggie/SCVHistory/scvhistory.com (read-only)',
    'page': {'path': REL, 'legacyUrl': '/' + REL, 'sha256': digest, 'manifestSha256': listed,
             'manifestCheck': 'match' if listed == digest else ('absent' if listed is None else 'MISMATCH'),
             'title': html.unescape(re.search(r'(?is)<title>(.*?)</title>', s).group(1).strip())},
    'conventions': 'Paragraphs separated by a blank line, no HTML, entities decoded (the page\'s &#151; kept as an em dash), italics and bold not marked. The body is the interview (introduction, questions and answers, closing line) followed by the two sidebars under their printed headings.',
    'slug': slug, 'byline': byline, 'dateline': dateline, 'taped': taped, 'copyright': html.unescape(copyright_line),
    'editorsNote': note[0], 'captions': captions + ['Pete Knight, 1965 (image alt text; the enlarged image link only)'],
    'videoLink': 'http://www.scvtv.com/html/sg042504-nm.html',
    'afterInterview': tail,
    'interviewParagraphs': len(interview), 'militaryParagraphs': len(military), 'officialParagraphs': len(official),
    'body': body,
    'verbatimCheck': 'every paragraph found as a contiguous word run in the page',
}
os.makedirs(os.path.dirname(OUT), exist_ok=True)
json.dump(data, open(OUT, 'w', encoding='utf-8'), ensure_ascii=False, indent=1)
print(f"sha256 {digest} manifest {data['page']['manifestCheck']}; interview {len(interview)} paragraphs, military {len(military)}, official {len(official)}; {len(body.split())} words; written {os.path.relpath(OUT)}")
