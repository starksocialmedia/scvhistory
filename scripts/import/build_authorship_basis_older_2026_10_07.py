"""The authorship basis for the older author links (Nathan, 7 October 2026: "Yes to the same pass on the other 410. An author link
that says nothing about what it rests on is the thing we are fixing"). Every writtenBy link with no basis yet: 409 articles, 10
collections, 6 documents, 1 obituary. Reads storage/runtime/basis410/classified.json, made by
scripts/import/classify_authorship_older_2026_10_07.py from the records (storage/runtime/basis410/dump.php) and the legacy pages on
Reggie; the cases the rules could not settle were read by hand and are set in HAND below with what was read.
Writes inventory/review/authorship-basis-older-2026-10-07.json for authorship_basis_2026_10_07.php.
Run: python3 scripts/import/build_authorship_basis_older_2026_10_07.py"""
import json, re, collections
rows = json.load(open('storage/runtime/basis410/classified.json'))
NOT_CHECKED = ' The original page is not on the copy of the old site held, and was not checked against it.'
HAND = {
  28287: ('derived', 'No byline. The heading on the page names her as the speaker, "City Formation Committee Member Connie Worden-Roberts Remembers", and the text is in her first person.'),
  28285: ('closing-tagline', 'The proposal, in the Historical Society board minutes of May 19, 2003, ends "Respectfully submitted, Connie Worden-Roberts".'),
  12866: ('closing-tagline', 'Signed at the end: "JO ANNE DARCY, Councilwoman, City of Santa Clarita". No byline at the head.'),
  12850: ('closing-tagline', 'Signed at the end: "HOWARD P. \'BUCK\' McKEON, Member of Congress". No byline at the head.'),
  817: ('closing-tagline', 'Signed at the end: "— LEON WORDEN, 1998". No byline at the head.'),
  12530: ('closing-tagline', 'The text held ends "Leon Worden is The Signal\'s special sections editor." No byline at its head.' + NOT_CHECKED),
  12284: ('closing-tagline', 'The text held ends "Leon Worden is a Santa Clarita resident. His commentary appears on Wednesdays." No byline at its head.' + NOT_CHECKED),
  12278: ('closing-tagline', 'The text held ends "Leon Worden is The Signal\'s special sections editor." No byline at its head.' + NOT_CHECKED),
  855: ('derived', 'The text held prints no author. Attributed as chapter 17 of Jerry Reynolds\'s series, whose other chapter pages carry the heading "History of the Santa Clarita Valley by Jerry Reynolds".' + NOT_CHECKED),
  1432: ('derived', 'The page prints no author. The editor\'s notes to SCVHistory.com\'s edition of A.B. Perkins\'s "Story of Our Valley", attributed to Leon Worden as the site\'s editor.'),
  1418: ('derived', 'The page prints no author. The introduction to SCVHistory.com\'s edition of A.B. Perkins\'s "Story of Our Valley", attributed to Leon Worden as the site\'s editor.'),
  865: ('derived', 'The page prints no author. A caption on SCVHistory.com, attributed to Leon Worden as the site\'s editor.'),
  849: ('derived', 'The page prints no author. A caption on SCVHistory.com, attributed to Leon Worden as the site\'s editor.'),
  873: ('series-attribution', 'The collection\'s index page is headed "STORY OF OUR VALLEY BY A.B. PERKINS".'),
  665: ('printed-byline', 'The collection\'s index page is headed "Leon Worden".'),
  669: ('printed-byline', 'The collection\'s index page is headed "DARRYL MANZER" above "Now And Then in the Santa Clarita Valley".'),
  673: ('printed-byline', 'The collection\'s index page is headed "Dr. Sol Taylor, the Lincoln Cent Expert", as recorded in the text held.' + NOT_CHECKED),
  679: ('closing-tagline', 'The collection\'s index page ends "©PATTI RASMUSSEN. PUBLISHED BY PERMISSION OF THE AUTHOR."'),
  681: ('closing-tagline', 'The collection\'s index page ends "©PAULINE HARTE. PUBLISHED BY PERMISSION OF THE AUTHOR." and opens with a line about her.'),
  683: ('closing-tagline', 'The collection\'s index page ends "©RICHARD RIOUX. PUBLISHED BY PERMISSION OF THE AUTHOR."'),
  685: ('closing-tagline', 'The collection\'s index page ends "©TIM WHYTE. PUBLISHED BY PERMISSION OF THE AUTHOR."'),
}
def segment(ev, last):
    parts = [p.strip() for p in ev.split('|')]
    for i, p in enumerate(parts):
        if last.lower() in p.lower() and i + 2 < len(parts) and re.match(r'^[·•]$', parts[i + 1]):
            return p, parts[i + 1] + ' ' + parts[i + 2]
    for i, p in enumerate(parts):
        if last.lower() in p.lower():
            if re.match(r'^[·•]$', parts[i + 1] if i + 1 < len(parts) else '') and i + 2 < len(parts):
                return p, parts[i + 1] + ' ' + parts[i + 2]
            return p, None
    return ev.strip(), None
out, todo = [], []
for r in rows:
    last = r['person'].split()[-1].rstrip('.')
    tail = ' Read from the original page.' if r['src'] == 'page' else ' Read from the text held.' + NOT_CHECKED
    if r['entryId'] in HAND:
        basis, note = HAND[r['entryId']]
    elif r['kind'] == 'printed-byline':
        seg, date = segment(r['ev'], last)
        seg = re.sub(r'\s+', ' ', seg).rstrip(',.')
        if date and not seg.lower().startswith('by') and 'by ' not in seg.lower():
            basis, note = 'printed-byline', f'Printed at the head of the piece as "{seg} {date}": the name and date, without "By".' + tail
        else:
            basis, note = 'printed-byline', f'Printed byline "{seg}".' + tail
    elif r['kind'] == 'series-attribution':
        h = re.sub(r'\s+', ' ', r['ev']).strip()
        if r['section'] == 'collections':
            basis, note = 'series-attribution', f'The series\' index page is headed "{h}".' + tail
        else:
            basis, note = 'series-attribution', f'The series heading on the original page reads "{h}"; the piece itself carries no byline of its own.' + tail
    else:
        todo.append(r['entryId']); continue
    out.append({'entryId': r['entryId'], 'title': r['title'], 'personId': r['personId'], 'person': r['person'], 'section': r['section'], 'legacyUrl': r['path'], 'basis': basis, 'note': note})
json.dump({'built': '2026-10-07', 'by': 'scripts/import/build_authorship_basis_older_2026_10_07.py', 'rows': out, 'unsettled': todo}, open('inventory/review/authorship-basis-older-2026-10-07.json', 'w'), indent=1, ensure_ascii=False)
print(len(out), collections.Counter(o['basis'] for o in out), 'unsettled', todo)
print(collections.Counter((o['person'], o['basis']) for o in out))
