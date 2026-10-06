#!/usr/bin/env python3
"""Adrian W. Adams #28667: the profile draft and his relations (Nathan, 6 October 2026: "this is Judge Adrian W.
Adams, so the photographs are his ... Relate all three [the court, the Newhall Incident, the hospital] ... whether this
is the Adrian Adams on the Hart board 1957 to 1963 ... Settle it or leave it as a lead").

Writes inventory/review/adrian-adams-draft-2026-10-06.json and .md. Every quotation in a note is checked against the
page it cites on the Reggie mirror (read as bytes, utf-8 else latin-1, never the shell's grep); the script reports any
quotation not found. No em dashes in our own text. Reads nothing from the database.
Run: python3 scripts/import/draft_adams_profile_2026_10_06.py
"""
import json, re, html, hashlib, pathlib

ROOT = pathlib.Path(__file__).resolve().parents[2]
M = pathlib.Path('/Volumes/Reggie/SCVHistory/scvhistory.com/scvhistory')
GIF = pathlib.Path('/Volumes/Reggie/SCVHistory/scvhistory.com/gif')
PERSON = 28667


def text(p):
    b = (M / p).read_bytes()
    try:
        t = b.decode('utf-8')
    except UnicodeDecodeError:
        t = b.decode('latin-1')
    t = re.sub(r'(?is)<(script|style).*?</\1>', ' ', t)
    return html.unescape(re.sub(r'<[^>]+>', ' ', t))


def norm(s):
    s = s.replace('’', "'").replace('‘', "'").replace('“', '"').replace('”', '"').replace('\xa0', ' ')
    return re.sub(r'\s+', ' ', s).strip().lower()


SRC = {
    'AA5001': 'aa5001.htm', 'AA7001': 'aa7001.htm', 'AA7002': 'aa7002.htm', 'SG2003': 'sg041203.htm',
    'HB57': 'files/hb5701/files/basic-html/page7.html', 'HB58': 'files/hb5801/files/basic-html/page6.html',
    'LW6902': 'files/lw6902/files/basic-html/page10.html', 'CHAMBER': 'scvchamber-presidents.htm', 'HS0880': 'hs0880.htm',
    'ROSTER': 'hartschoolboardmembers.htm', 'P1958': 'files/hart1958graduation/files/basic-html/page8.html',
    'P1960': 'files/gf6001/gf6001_ocr.htm', 'P1961': 'files/tr6101/tr6101_ocr.htm',
    'Y1962a': 'files/hart1962yearbook/files/basic-html/page8.html', 'Y1962b': 'files/hart1962yearbook/files/basic-html/page9.html',
    'JD8301': 'jd8301.htm', 'AL1977G': 'al1977g.htm', 'PREHIST': 'hmnmhprehistory.htm', 'VNN71': 'hmnmh_vnn020971.htm',
    'HM7203': 'hm7203.htm', 'AA7402': 'aa7402.htm', 'FULLER': 'obituary_johnstevensfuller.htm', 'HM8301': 'hm8301.htm',
    'AA7401': 'aa7401.htm', 'CO7401': 'co7401.htm', 'MWOTY': 'mwoty.htm', 'LW2170A': 'lw2170a.htm', 'LW2170B': 'lw2170b.htm',
    'HM7301': 'hm7301.htm', 'AA5002': 'aa5002.htm', 'AA5003': 'aa5003.htm',
}
T = {k: norm(text(v)) for k, v in SRC.items()}
# SCVTV's Legacy page, read on the live site 6 October 2026 (saved in the session scratchpad; only its title is quoted)
SCVTV_TITLE = 'Judge Adrian Adams (2004): Tales of the Newhall Court; Newhall Incident; more'
T['SCVTV'] = norm(SCVTV_TITLE)

# key, note, the sources its quotations are checked against
NOTES = [
    ('AA5001', 'SCVHistory.com, "Judge Adrian W. Adams," AA5001, /scvhistory/aa5001.htm; the same note is printed on AA5002, AA5003, AA7001, AA7002, HM7301, LW2170a and LW2170b, and on SCVTV\'s Legacy episode of 2004, "Judge Adrian Adams (2004): Tales of the Newhall Court; Newhall Incident; more" (scvtv.com, uploaded August 26, 2013, read October 6, 2026): "Adams opened a law practice on San Fernando Road in Newhall on May 1, 1953, and partnered with attorney James Lowder"; "Adrian W. Adams was appointed judge of the Newhall Municipal Court in 1970 by Gov. Ronald Reagan. For the first time, the Newhall Judicial District had two judges. (C.M. MacDougall, already seated, was the other.)"; "By the time Adams retired from the bench in 1991, a third seat had been added to the court"; "One career highlight as a judge was the arraignment of the two suspects in the 1970 Newhall Incident, which came just months after his appointment to the bench"; "one of his biggest contributions was to chair the board that created Henry Mayo Newhall Memorial Hospital."', ['AA5001', 'SCVTV']),
    ('AA7001', 'Caption to "Judge Adrian W. Adams," AA7001, quoting The Signal of January 28, 1970, as carried on SCVHistory.com, /scvhistory/aa7001.htm: "Adrian W. Adams was appointed judge of the Newhall Municipal Court on Jan. 27, 1970, by Gov. Ronald Reagan"; "Newhall became a two-judge town yesterday with the appointment of Adrian Adams to the Newhall Municipal Court bench"; "The former FBI agent and current Newhall Elementary district board of education member said he was pleased."', ['AA7001']),
    ('SG2003', 'Adrian Adams, "Tales of the Newhall Court," The Signal, April 12, 2003, as carried on SCVHistory.com, /scvhistory/sg041203.htm. The editor\'s note: "May 1 is the 50th anniversary of the day former Municipal Court Judge Adrian Adams opened his attorney practice in Newhall. His appointment to the bench by Gov. Ronald Reagan in 1970 marked the first time the Santa Clarita Valley had two judges. By the time he retired in 1991 a third seat had been added." Adams: "As the writer has been retired from the court since 1991"; "What we do have are the dockets of the court proceedings going back to the 1850s."', ['SG2003']),
    ('DIR', 'Telephone directories as carried on SCVHistory.com: Pacific Telephone, January 1957, HB5701, page 7 (/scvhistory/files/hb5701/), and January 1958, HB5801, page 6 (/scvhistory/files/hb5801/): "Adams Adrian W atty 22508WMarket," with a second listing for "Adams Adrian W" at "22547WDecoroDrSaugus"; Local City Directory, 1969, LW6902, page 10, photograph #5685 in this archive (/scvhistory/lw6902.htm): "Adams Adrian W Attorney 22508 Market." No other Adrian Adams is listed in the three.', ['HB57', 'HB58', 'LW6902']),
    ('CHAMBER', 'SCVHistory.com, "Santa Clarita Valley Chamber of Commerce Presidents," /scvhistory/scvchamber-presidents.htm: "1956 Adrian Adams Attorney at Law."', ['CHAMBER']),
    ('HS0880', 'The Newhall Signal and Saugus Enterprise, August 30, 1956, "Alarm, Opposition Flame at Huge Railroad Cyn. Trash Dump," as carried on SCVHistory.com with HS0880, /scvhistory/hs0880.htm: "The Newhall Chamber of Commerce held an emergency meeting Tuesday afternoon in the office of C of C President Adrian Adams."', ['HS0880']),
    ('ROSTER', 'Leon Worden, William S. Hart Union High School District Governing Board Members, SCVHistory.com, /scvhistory/hartschoolboardmembers.htm: "1957-1958 Adrian Adams"; "1959-1961 Dr. W.D. Ross Adrian Adams"; "1962 Dr. W.D. Ross Edith Palmer Adrian Adams"; the board of 1963 does not list him.', ['ROSTER']),
    ('PROGRAMS', 'Hart High School commencement programs as carried on SCVHistory.com: 1958 (/scvhistory/files/hart1958graduation/, page 8), 1960 (GF6001, /scvhistory/files/gf6001/, page 8) and 1961 (TR6101, /scvhistory/files/tr6101/), each listing "Mr. Adrian W. Adams" first among the trustees; the Hart yearbook of 1962 (/scvhistory/files/hart1962yearbook/), page 9: "Mr. Adrian W. Adams, President," and page 8, a message to the students, "What is there left?", signed "Adrian Adams" as "President of Board."', ['P1958', 'P1960', 'P1961', 'Y1962a', 'Y1962b']),
    ('JD8301', 'Paul Dworin, "Judge Byram Formally Installed," The Newhall Signal and Saugus Enterprise, February 4, 1983, as carried on SCVHistory.com with JD8301, /scvhistory/jd8301.htm: "brief remarks were made by Byram\'s colleague, Newhall Municipal Court Judge Adrian Adams. Reminiscing about his own induction as judge in the old courthouse (which was located on Market Street just east of San Fernando Road)," and "on a day like this (rainy) we\'d have to have buckets on the floor." The page\'s webmaster\'s note: "There were only two court seats, and they weren\'t often vacant. The other seat was held by Judge Adrian Adams, who had been appointed by Gov. Ronald Reagan in 1970 when the second seat was added."', ['JD8301']),
    ('AL1977', 'California Highway Patrol, Information Bulletin, July 1, 1970, AL1977g, as carried on SCVHistory.com, /scvhistory/al1977g.htm, of the gunman in the house: "they found he had committed suicide"; and "THE SURVIVING SUSPECT HAS BEEN HELD TO ANSWER IN THE SUPERIOR COURT FOR THE COUNTY OF LOS ANGELES ON FOUR COUNTS OF MURDER AND ONE COUNT OF ROBBERY." The names, Jack Twinning and Bobby Davis, and the morning of April 6 are in this archive\'s record of the Newhall Incident (#31376) and its sources.', ['AL1977G']),
    ('PREHIST', 'Leon Worden, "Volunteers Pave the Way to Henry Mayo Hospital," 2012, as carried on SCVHistory.com, /scvhistory/hmnmhprehistory.htm: "In 1970 the committee reorganized as the Henry Mayo Newhall Memorial Hospital board and placed Newhall Municipal Court Judge Adrian Adams at the helm."', ['PREHIST']),
    ('VNN71', '"Newhall Hospital Group Chooses Executive Board," Van Nuys News, February 9, 1971, as carried on SCVHistory.com, /scvhistory/hmnmh_vnn020971.htm: "Named as officers for the hospital which will be erected in 1973 are Municipal Judge Adrian Adams, chairman"; "Judge Adams appointed Fuller as chairman of the investment committee."', ['VNN71']),
    ('HM7203', 'Caption to HM7203, "Henry Mayo Hospital Groundbreaking," from a hospital newsletter, as carried on SCVHistory.com, /scvhistory/hm7203.htm: "September 1972: Jane Newhall, great-granddaughter to hospital namesake Henry Mayo Newhall"; "Shown with her is Judge Adrian Adams, chairman of the Henry Mayo Newhall Memorial Hospital board."', ['HM7203']),
    ('AA7402', 'Caption to AA7402, "Henry Mayo Hospital Board Thanks Ex-Newhall Land President Tom Lowe, 1974," SCVHistory.com, /scvhistory/aa7402.htm: "Judge Adrian Adams (left), president of the nonprofit board that built Henry Mayo Newhall Memorial Hospital, presents Thomas L. Lowe"; "This is 1974; the hospital opened in 1975"; the inscription is signed "Board of Trustees Henry Mayo Newhall Memorial Hospital June 1974."', ['AA7402']),
    ('FULLER', 'Leon Worden, "John Stevens Fuller," SCVNews.com, January 6, 2014, as carried on SCVHistory.com, /scvhistory/obituary_johnstevensfuller.htm, quoting Fuller: "The Lutheran Hospital Society, The Newhall Land and Farming Co. and (Municipal Court Judge) Adrian Adams brought together a group of about 25 people to be the board, and that\'s how the hospital got started."', ['FULLER']),
    ('HM8301', 'Caption to HM8301, "Groundbreaking for Expansion, Early 1980s," SCVHistory.com, /scvhistory/hm8301.htm: "Adrian Adams, Newhall Municipal Court judge and founding chairman of the hospital board."', ['HM8301']),
    ('COC', 'Caption to AA7401, "Gov. Reagan Greets COC Trustees Boyer, Adams, Claffey, Fortine at IRC Dedication, 4-22-1974," SCVHistory.com, /scvhistory/aa7401.htm: "Trustee Adrian Adams (appointed by Reagan to a Municipal Court judgeship in 1970)"; "During the dedication ceremony, Adams delivered a tribute to Bonelli, an original board member." Caption to CO7401, the same ceremony, /scvhistory/co7401.htm: "Between Rockwell and Reagan is Municipal Court Judge Adrian Adams," "whom Reagan had appointed to the bench four years earlier. At this time, Adams was chairing the effort to fund the construction of the nonprofit Henry Mayo Newhall Memorial Hospital." The district\'s list of its boards from 1967 (Santa Clarita Community College District, Board of Trustees, "History," https://www.canyons.edu/administration/board/history.php, read October 6, 2026) does not name him.', ['AA7401', 'CO7401']),
    ('MWOTY', 'SCVHistory.com, "SCV Man & Woman of the Year," /scvhistory/mwoty.htm, the honour begun by the Newhall-Saugus Chamber of Commerce as the "Outstanding Citizen of the Year": "1980 Judge Adrian Adams."', ['MWOTY']),
]

num = {k: i + 1 for i, (k, _, _) in enumerate(NOTES)}
R = lambda *ks: ''.join(f'[{num[k]}]' for k in ks)

BODY = '\n\n'.join([
    f'Adrian W. Adams was a Newhall lawyer who became a judge of the Newhall Municipal Court and chaired the board that built Henry Mayo Newhall Memorial Hospital. He opened his law practice on San Fernando Road in Newhall on May 1, 1953, and practised with the attorney James Lowder.{R("AA5001", "SG2003")} The telephone directories of 1957 and 1958 list him as an attorney on West Market Street, and the city directory of 1969 at the same address.{R("DIR")} He was president of the Newhall Chamber of Commerce in 1956, and that August the chamber met in his office to oppose a trash dump planned for Railroad Canyon.{R("CHAMBER", "HS0880")}',
    f'Leon Worden\'s roster of the William S. Hart Union High School District board lists an Adrian Adams on the board from 1957 to 1962.{R("ROSTER")} The district\'s commencement programs of 1958, 1960 and 1961 name "Mr. Adrian W. Adams" first among its trustees, and the yearbook of 1962 names him president of the board, over a message to the students.{R("PROGRAMS")} No source found says in words that the trustee was the Newhall lawyer; the directories of those years list one Adrian W. Adams in the valley, and he is the attorney.{R("DIR")}',
    f'On January 27, 1970, Governor Ronald Reagan appointed him to the Newhall Municipal Court. "Newhall became a two-judge town yesterday," The Signal reported: C.M. MacDougall, already on the bench, had been the district\'s only judge. The Signal described Adams then as a former FBI agent and a member of the board of the Newhall elementary school district.{R("AA7001", "AA5001")} In 1983, at the installation of Judge H. Keith Byram, he remembered his own swearing-in at the old courthouse on Market Street, where on a rainy day "we\'d have to have buckets on the floor."{R("JD8301")} He retired from the bench in 1991, by which time the court had a third seat.{R("AA5001", "SG2003")}',
    f'SCVHistory.com\'s note on him counts among the highlights of his years as a judge the arraignment of the suspects in the Newhall Incident, the killing of four Highway Patrol officers in April 1970, "just months after his appointment to the bench."{R("AA5001")} The note speaks of two suspects. Only one survived to be charged: the gunman who held a householder hostage killed himself on the morning of April 6, and the Highway Patrol reported that "the surviving suspect" had been held to answer in the Superior Court.{R("AL1977")} The note does not say when the arraignment was held.',
    f'In 1970 the citizens\' committee formed to build a hospital for the valley became the board of Henry Mayo Newhall Memorial Hospital, with Adams at its head; in February 1971 the Van Nuys News reported the board\'s officers, "Municipal Judge Adrian Adams, chairman."{R("PREHIST", "VNN71")} He stood with Jane Newhall at the groundbreaking in September 1972, and in June 1974 he presented Thomas L. Lowe, formerly of Newhall Land, with the board\'s thanks; the hospital opened in 1975.{R("HM7203", "AA7402")} John Fuller, the board\'s first treasurer, remembered that the Lutheran Hospital Society, Newhall Land "and (Municipal Court Judge) Adrian Adams brought together a group of about 25 people to be the board."{R("FULLER")} Captions call him its chairman, its president and its "founding chairman."{R("HM7203", "AA7402", "HM8301")}',
    f'In April 1974 he spoke at the dedication of College of the Canyons\' first permanent building, a tribute to Dr. William G. Bonelli. One caption calls him a trustee of the college; another, of the same ceremony, calls him a Municipal Court judge, and the district\'s own list of its boards does not include him.{R("COC")} He was the valley\'s Man of the Year for 1980.{R("MWOTY")} In April 2003, near the fiftieth anniversary of his practice, The Signal published his history of the Newhall court, drawn from its dockets.{R("SG2003")}',
])

bad = []
for k, note, srcs in NOTES:
    for q in re.findall(r'"([^"]{6,})"', note):
        # a quotation that ends in a comma or full stop inside the quotes keeps it only where the source has it
        cand = [q, q.rstrip('.,;')]
        if not any(any(norm(c) in T[s] for s in srcs) for c in cand):
            bad.append(f'note {num[k]} ({k}): not found in {"/".join(srcs)}: "{q[:90]}"')
for q in re.findall(r'"([^"]{6,})"', BODY):
    if not any(norm(q.rstrip('.,')) in t for t in T.values()):
        bad.append(f'body: not found in any source: "{q[:90]}"')

# the court, the hospital, the event, the Hart note, the portrait, the photographs
COURT_HOLDING = {
    'holdingOffice': 18425, 'officeTitle': 'Judge', 'holdingBody': 29882, 'bodyTitle': 'Newhall Municipal Court',
    'termStart': 'January 27, 1970', 'termStartEdtf': '1970-01-27', 'termEnd': '1991', 'termEndEdtf': '1991',
    'selectionMethod': 'appointed', 'howEnded': 'left', 'startEvidence': 'contemporary', 'endEvidence': 'retrospective',
    'footnotes': [
        NOTES[num['AA7001'] - 1][1],
        NOTES[num['AA5001'] - 1][1],
        NOTES[num['SG2003'] - 1][1],
        'He retired: "retired from the bench in 1991" (AA5001) and "retired from the court since 1991" (his own words, 2003). The archive\'s vocabulary has no "retired" for an office holding, so howEnded is "left". Whether he stood for election to the seat after his appointment is not searched; see researchLeads on his record.',
        NOTES[num['JD8301'] - 1][1],
    ],
}
HOSPITAL_AFF = {
    'affiliationBody': 380, 'bodyTitle': 'Henry Mayo Newhall Memorial Hospital', 'affiliationKind': 'nonprofit-board',
    'affiliationTitle': 'Chairman of the board', 'affiliationEnded': 'unknown',
    'termStart': '1970', 'termStartEdtf': '1970', 'termEnd': '', 'termEndEdtf': '', 'startEvidence': 'retrospective',
    'footnotes': [NOTES[num[k] - 1][1] for k in ('PREHIST', 'VNN71', 'HM7203', 'AA7402', 'HM8301', 'FULLER')] + [
        'When he left the chair is not recorded. He is "chairman" in February 1971 and September 1972, "president" in June 1974, and "founding chairman" in a caption of the early 1980s that does not say whether he still held the post.'],
}
HART_NOTE3 = NOTES[num['PROGRAMS'] - 1][1] + ' These are contemporary printings of the board for 1958 to 1962; the identification of this trustee with the Newhall attorney and later judge rests on the name with its initial and the directories of 1957 and 1958 (see his record).'

M_PAGE = lambda c: M / f'{c}.htm'
PHOTOS = [
    # code, date as printed in the AA5001 sidebar, the page's own caption (as printed), credit as printed, who is shown, photoPeople?
    ('AA5001', '1950s', 'In this photo, Adams is seen behind the desk at his Newhall law office in the 1950s.', 'AA5001: 9600 dpi jpeg from original print', 'Adams alone', True),
    ('AA5002', '1950s', 'In this photo, Adams is seen at his Newhall law office in the 1950s.', 'AA5002: 9600 dpi jpeg from original print', 'Adams alone', True),
    ('AA5003', '1950s', 'In this photo, Adams is seen at his Newhall law office in the 1950s.', 'AA5003: 9600 dpi jpeg from original print', 'Adams alone', True),
    ('AA7001', '1970', 'Adrian W. Adams was appointed judge of the Newhall Municipal Court on Jan. 27, 1970, by Gov. Ronald Reagan. This photo ran Jan. 28, 1970, in The Signal', 'AA7001: 9600 dpi jpeg from newsprint', 'Adams alone, in robes', True),
    ('AA7002', 'With C.M. MacDougall 1970', 'Newhall Municipal Court Judges Adrian Adams (left) and C.M. MacDougall.', 'AA7002: 19200 dpi jpeg from smaller jpeg, collection of Adrian Adams. Online image only.', 'Adams and MacDougall', True),
    ('HM7301', '~1973', '(no caption of its own: the page is headed "Judge Adrian W. Adams" and carries the general note)', 'HM7301: 9600 dpi jpeg from printed image', 'Adams alone, head and shoulders', True),
    ('AA7401', 'COC-IRC Dedication 1974', 'California Gov. Ronald Reagan greets members of the Santa Clarita Community College District (COC) Board of Trustees at the dedication of College of the Canyons\' first permanent building', '(none printed beyond the page)', 'Reagan with Boyer, Adams, Dyer, Claffey, Fortine, Rheinschmidt', True),
    ('AA7402', 'HMNMH with Tom Lowe 1974', 'Judge Adrian Adams (left), president of the nonprofit board that built Henry Mayo Newhall Memorial Hospital, presents Thomas L. Lowe', '(none printed beyond the page)', 'Adams and Lowe', True),
    ('HM8301', 'Hospital Expansion 1980s', 'Groundbreaking for an expansion (we\'re still trying to figure out exactly what and when) at Henry Mayo Newhall Memorial Hospital in Valencia, probably early 1980s.', '(none printed beyond the page)', 'Watson, Mysko, Adams, McMahon, Fuller', True),
    ('LW2170a', '2003', 'Retired Judge Adrian Adams at home in the Happy Valley section of Newhall.', 'LW2170a: 9600 dpi jpeg from digital photograph by Leon Worden', 'Adams alone', True),
    ('LW2170b', '2003', 'Retired Judge Adrian Adams\' official nameplate and commemorative gavels are reminders of his service on the bench and as the first president of the Henry Mayo Newhall Memorial Hospital board.', 'LW2170b: 9600 dpi jpeg from digital photograph by Leon Worden', 'his nameplate and gavels; he is not in it', False),
]
photos = []
for code, d, cap, cred, who, pp in PHOTOS:
    page = M_PAGE(code.lower())
    t = norm(text(f'{code.lower()}.htm'))
    title = re.search(r'<title>(.*?)</title>', page.read_bytes().decode('latin-1'), re.S | re.I).group(1).strip()
    if not cap.startswith('(') and norm(cap.rstrip('.')) not in t:
        bad.append(f'photo {code}: caption not found on its page')
    files = []
    for f in (f'{code.lower()}.jpg', f'{code.lower()}_large.jpg'):
        p = GIF / f
        if p.exists():
            files.append({'file': f'gif/{f}', 'bytes': p.stat().st_size, 'sha256': hashlib.sha256(p.read_bytes()).hexdigest()})
    photos.append({'code': code, 'page': f'/scvhistory/{code.lower()}.htm', 'pageTitle': title, 'dateAsPrinted': d,
                   'captionAsPrinted': cap, 'creditAsPrinted': cred, 'shows': who, 'photoPeople': pp, 'files': files})

pf = ROOT / 'inventory/incoming/hm7301_large.jpg'
PORTRAIT = {
    'file': 'hm7301_large.jpg', 'mirrorPath': 'gif/hm7301_large.jpg', 'sourcePage': '/scvhistory/hm7301.htm',
    'sha256': hashlib.sha256(pf.read_bytes()).hexdigest(), 'bytes': pf.stat().st_size, 'width': 1600, 'height': 1879,
    'assetFilename': 'hm7301_large.jpg', 'folder': 'legacy/', 'assetTitle': 'Adrian W. Adams, portrait', 'alt': 'Portrait of Adrian W. Adams',
    'fields': {
        'provenanceKind': 'legacy-mirror', 'acquiredDate': '2026-10-06', 'license': 'unknown',
        'rightsNote': 'No permission to republish is established.', 'legacySourcePath': 'gif/hm7301_large.jpg',
        'sourceChecksum': 'sha256:' + hashlib.sha256(pf.read_bytes()).hexdigest(),
        'source': 'SCVHistory.com, gif/hm7301_large.jpg, as published on the original site: the enlargement linked from the photograph on /scvhistory/hm7301.htm, "Judge Adrian W. Adams." Copied from the legacy mirror on 6 October 2026.',
        'photoCaptionExt': 'Judge Adrian W. Adams.', 'photoSourceCode': 'HM7301', 'dateAsPrinted': '~1973', 'photoCredit': 'HM7301: 9600 dpi jpeg from printed image',
    },
    'why': 'The clearest likeness the page links: a studio head-and-shoulders portrait, 1600 by 1879 on the mirror. AA5001, the page\'s own photograph (800 by 631, no enlargement on the mirror), shows him at his desk in the 1950s; it is the better picture of the lawyer but the smaller face.',
}

RESEARCH_LEADS = '\n\n'.join([
    'Hart board, 1957 to 1963 (holding #28751). The trustee is "Mr. Adrian W. Adams" in the Hart commencement programs of 1958, 1960 and 1961 and president of the board in the 1962 yearbook; the 1957 and 1958 telephone directories list one Adrian W. Adams in the valley, the attorney at 22508 W. Market Street, Newhall (with a house on Decoro Drive, Saugus). No source found says in words that the trustee was the lawyer who became the judge. What would settle it: the full Signal story of January 28, 1970 on his appointment (AA7001 carries only its opening; it names his school-board service as Newhall Elementary), his obituary, a Hart board minute or a Signal election notice naming the trustee as an attorney. Searched 6 October 2026: the mirror text index (storage/runtime/pc/index.jsonl, every page and flipbook page) for "adrian", then each hit read; the archive\'s records; the web (no obituary found).',
    'Newhall elementary school board. The Signal of January 28, 1970 calls him a "current Newhall Elementary district board of education member." His term on the Newhall School District board (#21590) is not recorded; no holding is made.',
    'The Newhall Incident arraignment. AA5001 says he arraigned "the two suspects"; one died on April 6, 1970. The date of the arraignment and the court record are not found.',
    'Elections to the bench. Appointed in 1970; whether and when he stood for election to the seat afterwards is not searched (the County\'s returns for 1970, 1976, 1982, 1988).',
    'College of the Canyons. AA7401 calls him "Trustee Adrian Adams" at the dedication of April 22, 1974; CO7401 calls him a Municipal Court judge and the district\'s board history does not list him. No holding is made.',
    'Not imported: the eleven photographs on AA5001 and the four others that name him (CO7401, HM7203, the JD8301 gallery of 1983, with jd8301e "Judge Adrian Adams" and jd8301g "Judges Adrian Adams and Keith Byram"); his article "Tales of the Newhall Court" (The Signal, April 12, 2003, /scvhistory/sg041203.htm); SCVTV\'s Legacy interview of 2004; the Van Nuys News of February 9, 1971; the AA2001 and AA2101 court papers, "collection of Judge Adrian Adams."',
    'Birth and death are not found in the archive or on the web (searched 6 October 2026).',
])

allown = BODY + json.dumps([n for _, n, _ in NOTES]) + json.dumps(COURT_HOLDING) + json.dumps(HOSPITAL_AFF) + HART_NOTE3 + RESEARCH_LEADS + json.dumps(PORTRAIT)
if '—' in allown:
    bad.append('an em dash in our own text')

D = {
    'drafted': '2026-10-06', 'draftedBy': 'scripts/import/draft_adams_profile_2026_10_06.py, Claude Code',
    'person': {'id': PERSON, 'title': 'Adrian Adams'},
    'body': BODY,
    'footnotes': [{'number': str(num[k]), 'key': k, 'note': n} for k, n, _ in NOTES],
    'fields': {'bodyAuthorship': 'editorial-2026', 'occupation': 'Attorney; Newhall Municipal Court judge', 'fullName': 'Adrian W. Adams',
               'aliasesAdd': ['Adrian W. Adams', 'Judge Adrian Adams'], 'rolesAdd': [18425], 'researchLeads': RESEARCH_LEADS},
    'courtHolding': COURT_HOLDING,
    'hospitalAffiliation': HOSPITAL_AFF,
    'hospitalAssociatedPersonsAdd': 380,
    'eventPersonsAdd': 31376,
    'hartHolding': {'id': 28751, 'note3': HART_NOTE3},
    'portrait': PORTRAIT,
    'photographs': photos,
    'quoteCheck': bad or 'every quotation found in its saved source',
}
(ROOT / 'inventory/review/adrian-adams-draft-2026-10-06.json').write_text(json.dumps(D, indent=1, ensure_ascii=False))

md = ['# Adrian W. Adams #28667: profile draft (6 October 2026)\n',
      'For Nathan. Drafted by Claude Code from the Reggie mirror. Quotation check: ' + (('FAILED: ' + '; '.join(bad)) if bad else 'every quotation found in its saved source') + '.\n',
      '## What the record is now\n',
      '- #28667 "Adrian Adams", made by create_hart_early_members.php on 3 October 2026 for the Hart roster: role School Board Member, era Postwar Boom, no body, no portrait, no dates. One holding, #28751, Hart board 1957 to 1963 (roster).',
      '- The portrait census of 5 October held AA5001, HM7301 and AA7001 as "medium": "likely the same man, but not stated." Nathan, 6 October: this is Judge Adrian W. Adams.',
      '- Newhall Municipal Court #29882 names him in its body and cites AA7001 and his 2003 article, but has no relation to him. Henry Mayo #380 names him in its body; orgAssociatedPersons holds Henry Mayo Newhall and Scott Wilk only. The Newhall Incident #31376 has no eventPersons.',
      '- The role Judge #18425 exists and has no office holdings yet; this would be its first.', '',
      '## The Hart board: a lead, strongly supported\n',
      'The trustee of 1957 to 1962 is printed as "Mr. Adrian W. Adams" (the Hart commencement programs of 1958, 1960, 1961) and as president of the board in the 1962 yearbook. The January 1957 and January 1958 telephone directories list only one Adrian W. Adams in the valley: an attorney at 22508 W. Market Street, Newhall, with a residence on Decoro Drive in Saugus. In 1956 the chamber\'s list of presidents gives "Adrian Adams Attorney at Law". The judge is "Adrian W. Adams", in practice in Newhall from 1953. And in January 1970 The Signal calls him a sitting member of a school board (Newhall Elementary). Everything points one way, but no source says in words that the Hart trustee was the attorney who became the judge, so under "No connection the sources do not make" it stays a lead: the holding stays on his record, the profile states the inference as an inference, and researchLeads says what would settle it (the full Signal story of 28 January 1970, an obituary, a Hart minute or election notice naming the trustee as an attorney). Proposed: a third note on #28751 citing the programs and yearbook.\n',
      '## Body\n', BODY, '',
      '## Notes\n'] + [f'{num[k]}. {n}' for k, n, _ in NOTES] + [
      '', '## Relations\n',
      f'- **The court**: a new office holding, Judge #18425 at Newhall Municipal Court #29882, {COURT_HOLDING["termStart"]} to {COURT_HOLDING["termEnd"]}, appointed, ended "left" (he retired; the vocabulary has no "retired"), evidence contemporary/retrospective, {len(COURT_HOLDING["footnotes"])} notes. Role Judge added to his roles.',
      f'- **The hospital**: a new affiliation, nonprofit-board, "Chairman of the board", from 1970 (retrospective), end not recorded, {len(HOSPITAL_AFF["footnotes"])} notes; and #28667 added to #380\'s orgAssociatedPersons (the org page\'s ASSOCIATED PEOPLE box; the affiliation shows on his page).',
      '- **The Newhall Incident**: #28667 added to #31376 eventPersons, on AA5001\'s words. The note\'s "two suspects" is wrong on its face (one gunman was dead by morning); the profile says so and does not repeat it.',
      '- **The law practice**: body text only. James Lowder has no record (he appears as 1968 chamber president and a 1969 chamber ex-officio director); not made.',
      '- **The Hart holding #28751**: note 3 added (below).', '',
      '### #28751 note 3\n', HART_NOTE3, '',
      '## Portrait\n',
      f'`inventory/incoming/hm7301_large.jpg`, copied unchanged from the mirror `gif/hm7301_large.jpg` (1600 x 1879, {PORTRAIT["bytes"]:,} bytes, sha256 {PORTRAIT["sha256"]}). {PORTRAIT["why"]} Not in Craft by filename, stem or checksum. Imported into archiveMedia/legacy/ and set as featuredImage (refused if he has one by then).', '',
      '## The photographs on AA5001\n',
      'None is in Craft: no photograph record by legacyKey, legacyUrl or photoSourceCode, no asset by filename or stem, no sourceChecksum matching any mirror file (checked 6 October 2026 against every entry\'s content and all 4,477 assets). photoPeople therefore has nowhere to go yet; the loader checks again and relates any that have been imported since.\n',
      '| Code | Date as printed | Caption as printed | Credit | Shows | photoPeople | Mirror files |', '|---|---|---|---|---|---|---|'] + [
      f"| {p['code']} | {p['dateAsPrinted']} | {p['captionAsPrinted']} | {p['creditAsPrinted']} | {p['shows']} | {'yes' if p['photoPeople'] else 'no: objects, he is not in it'} | {', '.join(f['file'] for f in p['files'])} |" for p in photos] + [
      '', 'The sidebar also links SCVTV\'s Legacy interview of 2004 (scvtv.com/?p=6045) and his article "Judge Adams\' Tales of the Newhall Court" (sg041203.htm, The Signal, April 12, 2003). Neither is in Craft as a record of its own.', '',
      'Elsewhere on the mirror, naming him and not linked from AA5001: CO7401 (the 1974 dedication, another frame), HM7203 (the 1972 groundbreaking with Jane Newhall), the JD8301 gallery of 1983 (jd8301e "Judge Adrian Adams", jd8301g "Judges Adrian Adams and Keith Byram"). And three court papers credited "collection of Judge Adrian Adams": AA2001 (Tom Mix fined, 1920), AA2101a and b (1921).', '',
      '## Research leads (to his record)\n', RESEARCH_LEADS, '',
      '## Not used\n',
      '- LW2170a\'s "at home in the Happy Valley section of Newhall" (2003): private.',
      '- The directories\' house addresses: cited for identity only, not in the body.',
      '- "Former FBI agent": in the body, from The Signal of 1970, a contemporary source; no other source gives his FBI years.',
    ]
(ROOT / 'inventory/review/adrian-adams-draft-2026-10-06.md').write_text('\n'.join(md) + '\n')
print('quote check:', 'OK' if not bad else 'FAILED')
for b in bad:
    print('  ', b)
