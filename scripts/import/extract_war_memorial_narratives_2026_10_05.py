#!/usr/bin/env python3
"""
Read only. Recovers the full narrative of the eight war memorial records that
parse_war_memorials.php cut to their first source line (silent-faults audit,
5 October 2026, finding 3), from each record's legacy page in the Reggie mirror.

Why on the host: the DDEV container sees the mirror only when Reggie was
connected when DDEV started (.ddev/drive-mount.sh). This script reads the mirror
directly; restore_war_memorial_narratives_2026_10_05.php reads its output,
re-extracts the same narrative from the record's stored legacyHtml by the same
rule, and refuses any record where the two disagree.

The rule (the same as the fixed parser): the narrative is the HTML after
"<b>Narrative:</b>" up to the first block end the legacy template prints after
it (<div style="height:20px">, </div>, <hr>, the next bold "Label:", or the end
of the content). Paragraphs are the legacy <p> breaks. Inside a paragraph the
page's source line breaks are HTML whitespace, not breaks in the text, so they
collapse to one space, as a browser shows them. Tags are dropped (link text
kept); entities are decoded to the characters they print (&mdash; to an em dash,
&#34; to a straight quote, &acirc; to a circumflex a). Nothing else is changed.

Word-for-word check: the restored text's words must appear contiguously, in
order, in the word stream of the whole mirror page as a browser would show it.

Writes: inventory/review/war-memorial-narratives-2026-10-05.json
Run (MacBook): python3 scripts/import/extract_war_memorial_narratives_2026_10_05.py
"""
import hashlib, html, json, os, re, sys

MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'
OUT = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..',
                   'inventory', 'review', 'war-memorial-narratives-2026-10-05.json')

# id -> sourcePath as the record carries it (checked against Craft by the PHP).
RECORDS = {
    570: 'scvhistory.com/warmemorial/ww2_johnward.htm',
    552: 'scvhistory.com/warmemorial/korea_henryacuna.htm',
    546: 'scvhistory.com/warmemorial/terror_brianprosser.htm',
    516: 'scvhistory.com/warmemorial/ww2_edwardcontreras.htm',
    566: 'scvhistory.com/warmemorial/ww2_jimbartlett.htm',
    564: 'scvhistory.com/warmemorial/ww2_jamesredman.htm',
    532: 'scvhistory.com/warmemorial/terror_josefloresmejia.htm',
    514: 'scvhistory.com/warmemorial/ww2_ekenaston.htm',
}

END = re.compile(r'<div\s+style\s*=\s*"height:\s*20px|</div>|<hr\b|<b>\s*[A-Z][A-Za-z ]{1,30}:\s*</b>|<!--\s*XWP-END', re.I)


def text_of(fragment):
    fragment = re.sub(r'<(script|style)\b[^>]*>.*?</\1>', ' ', fragment, flags=re.I | re.S)
    fragment = re.sub(r'<[^>]+>', ' ', fragment)
    fragment = html.unescape(fragment).replace('\xa0', ' ')
    return re.sub(r'\s+', ' ', fragment).strip()


def narrative(page):
    m = re.search(r'<b>\s*Narrative:\s*</b>', page, re.I)
    if not m:
        return None, None
    rest = page[m.end():]
    e = END.search(rest)
    region = rest[:e.start()] if e else rest
    paras = [text_of(p) for p in re.split(r'<p\b[^>]*>|</p>', region, flags=re.I)]
    return [p for p in paras if p], region


def words(s):
    return s.split()


def contiguous(needle, hay):
    n = len(needle)
    for i in range(len(hay) - n + 1):
        if hay[i] == needle[0] and hay[i:i + n] == needle:
            return i
    return -1


out, bad = [], 0
for rid, sp in RECORDS.items():
    path = os.path.join(MIRROR, sp.split('/', 1)[1])
    raw = open(path, 'rb').read()
    page = raw.decode('cp1252', errors='strict')
    paras, region = narrative(page)
    if paras is None:
        print(f'#{rid} {sp}: REFUSED, no Narrative label on the page'); bad += 1; continue
    full = '\n\n'.join(paras)
    pos = contiguous(words(full), words(text_of(page)))
    ok = pos >= 0 and full[-1] in '.!?"”)'
    if not ok:
        bad += 1
    out.append({
        'id': rid, 'sourcePath': sp, 'mirrorFile': path,
        'mirrorSha256': hashlib.sha256(raw).hexdigest(),
        'narrative': full, 'paragraphs': len(paras), 'words': len(words(full)),
        'inOrderInMirrorPage': pos >= 0, 'endsWithClosingPunctuation': full[-1] in '.!?"”)',
        'regionHtml': region,
    })
    print(f'#{rid} {sp}: {len(paras)} paragraph(s), {len(words(full))} words, '
          f'in order in the mirror page: {"yes" if pos >= 0 else "NO"}')

json.dump(out, open(OUT, 'w', encoding='utf-8'), ensure_ascii=False, indent=1)
print(f'written: {os.path.normpath(OUT)}  ({len(out)} records, {bad} failing a check)')
sys.exit(1 if bad else 0)
