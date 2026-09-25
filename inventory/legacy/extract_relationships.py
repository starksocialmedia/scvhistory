#!/usr/bin/env python3
"""Extract stated family relationships from obituary prose. Research-only; never invent."""
from __future__ import annotations

import json
import re
from collections import Counter
from pathlib import Path
from datetime import date

import sys as _sys
_sys.path.insert(0, str(Path(__file__).resolve().parent))
from scv_data import obituaries_dir  # noqa: E402

# Obituary data lives in the private repo starksocialmedia/scvhistory-data (see scv_data.py).
INV = obituaries_dir()
OBITS = INV / "obituaries.json"
REPORT = INV / "obituaries_relationships_report.json"

REL_TERMS = [
    "great-great-great-grandchildren", "great-great-great-grandchild",
    "great-great-great-grandsons", "great-great-great-grandson",
    "great-great-great-granddaughters", "great-great-great-granddaughter",
    "great-great-grandchildren", "great-great-grandchild",
    "great-great-grandsons", "great-great-grandson",
    "great-great-granddaughters", "great-great-granddaughter",
    "great-grandchildren", "great-grandchild",
    "great grandchildren", "great grandchild",
    "great-grandsons", "great-grandson", "great grandsons", "great grandson",
    "great-granddaughters", "great-granddaughter",
    "great granddaughters", "great granddaughter",
    "greatgrandchildren",
    "grandchildren", "grandchild", "grand-children", "grand children",
    "grandkids", "grand kids", "great-grandkids", "great grandkids",
    "grandsons", "grandson", "granddaughters", "granddaughter",
    "sons-in-law", "son-in-law", "son in-law", "sons in-law",
    "daughters-in-law", "daughter-in-law", "daughter in-law", "daughters in-law",
    "mothers-in-law", "mother-in-law", "fathers-in-law", "father-in-law",
    "brothers-in-law", "brother-in-law", "sisters-in-law", "sister-in-law",
    "stepchildren", "stepchild", "step children", "step-children",
    "stepsons", "stepson", "stepdaughters", "stepdaughter",
    "stepmothers", "stepmother", "stepfathers", "stepfather",
    "ex-husbands", "ex-husband", "ex-wives", "ex-wife",
    "former husbands", "former husband", "former wives", "former wife",
    "fiancées", "fiancée", "fiancees", "fiancee",
    "fiancés", "fiancé", "fiances", "fiance",
    "parents", "parent", "mothers", "mother", "moms", "mom",
    "fathers", "father", "dads", "dad",
    "wives", "wife", "husbands", "husband", "spouses", "spouse",
    "partners", "partner",
    "children", "child", "sons", "son", "daughters", "daughter",
    "brothers", "brother", "sisters", "sister", "siblings", "sibling",
    "uncles", "uncle", "aunts", "aunt",
    "nephews", "nephew", "nieces", "niece", "cousins", "cousin",
    "grandparents", "grandparent",
    "grandfathers", "grandfather", "grandmothers", "grandmother",
]

REL_ALT = "|".join(re.escape(t) for t in sorted(set(REL_TERMS), key=len, reverse=True))

NUMBER = (
    r"(?:\d+|one|two|three|four|five|six|seven|eight|nine|ten|"
    r"eleven|twelve|thirteen|fourteen|fifteen|sixteen|seventeen|"
    r"eighteen|nineteen|twenty|several|many|numerous|a few)"
)

ROLE_HEAD = re.compile(
    rf"""(?ix)
    ^
    (?:(?:his|her|their|the|my|our)\s+)?
    (?:(?:loving|beloved|devoted|dear|late|surviving|close)\s+)*
    (?:{NUMBER}\s+)?
    (?P<rel>{REL_ALT})
    (?:\s+of\s+(?:{NUMBER}|\d+)\s+years?)?
    \b
    """,
)

# Mid-list role starts: ", his wife" / "and her son" / "their mom"
ROLE_BOUNDARY = re.compile(
    rf"""(?ix)
    (?:
        (?:^|;)\s*
      | (?<=,)\s+
      | \s+and\s+
      | \s+by\s+
    )
    (?:(?:his|her|their|the|my|our)\s+)?
    (?:(?:loving|beloved|devoted|dear|late|surviving|close)\s+)*
    (?:{NUMBER}\s+)?
    (?:{REL_ALT})
    (?:\s+of\s+(?:{NUMBER}|\d+)\s+years?)?
    \b
    """,
)

KIN_OF_RE = re.compile(
    rf"""(?ix)
    \b(?:was\s+|is\s+)?
    (?:the\s+)?
    (?:beloved\s+|only\s+|oldest\s+|eldest\s+|youngest\s+|
       first\s+of\s+\w+\s+|first\s+|second\s+|third\s+)?
    (?P<rel>sons?|daughters?|child|children|brothers?|sisters?|siblings?|
            husbands?|wives?|spouse|fathers?|mothers?|parents?|
            grandsons?|granddaughters?|grandchild(?:ren)?|
            stepfathers?|stepmothers?|stepsons?|stepdaughters?)
    \s+of\s+
    (?:the\s+late\s+)?
    (?P<people>.{{3,100}}?)
    (?=
        \s*[.;] |
        \s*,\s*(?:he|she|they|who|whom|passed|died|has|was|born) |
        \s+(?:he|she|they|who|whom|passed|died|has\s+died|was\s+born|born|grew|lived|remained) |
        \s+\d{{1,2}},?\s+\d{{4}} |
        \s+(?:January|February|March|April|May|June|July|August|September|October|November|December) |
        $
    )
    """,
)

SURVIVED_RE = re.compile(
    r"(?i)\b(?:is|was|are|were)?\s*(?:also\s+)?survived\s+by\b"
)
# Avoid "those survived by him"
SURVIVED_FALSE = re.compile(r"(?i)\bthose\s+survived\s+by\b")
PRECEDED_RE = re.compile(
    r"(?i)\b(?:is|was|are|were|has\s+been)?\s*(?:also\s+)?"
    r"(?:preceded\s+in\s+(?:death|passing)\s+by|predeceased\s+(?:in\s+death\s+)?by)\b"
)

# Any survival wording ends a preceded-in-death list, including "survived her
# children" (no "by"), "survives", "leaves", "survivors".
SURVIVAL_CUT = re.compile(
    r"(?i)\b(?:is|are|was|were)?\s*(?:also\s+)?(?:survived|survives|survivors?|leaves|left\s+behind)\b"
)

PLACE_NAMES = {
    "los angeles", "new york", "valencia", "newhall", "saugus", "castaic",
    "acton", "canyon country", "santa clarita", "anaheim", "pasadena",
    "hollywood", "camarillo", "santa barbara", "long beach", "fresno",
    "placerville", "petaluma", "hollywood", "wyo", "wyo.", "california",
    "hawaii", "michigan", "montana", "ohio", "pennsylvania", "arizona",
    "canada", "encino", "van nuys", "san fernando", "palmdale", "lancaster",
    "hollywood", "bigfork", "surfside", "hollywood", "hollywood",
}

DESC_PREFIX = re.compile(
    r"""(?ix)^
    (?:
        (?:Broadway\s+actor|silent\s+(?:western\s+)?film\s+star|
           actor|actress|engineer|homemaker|chemical\s+engineer|
           (?:a\s+)?(?:well[- ]known|prominent|local|longtime)\s+\w+
        )
        (?:\s+and\s+(?:silent\s+)?(?:western\s+)?(?:film\s+)?star)?
        (?:\s+of\s+the\s+same\s+name)?
        \s+
    )+
    """,
)

JUNK_PERSON = re.compile(
    r"""(?ix)
    ^(?:
        in\s+\d{4} | after\s+\d+ | from\s+a\s+previous |
        of\s+[A-Z] | now\s+in | during\s+the |
        and\s+(?:his|her|their)\s+ | along\s+with |
        many\s+beloved | numerous\s+dear | extended\s+family |
        family$ | friends?$ | both\s+brothers |
        grand\s*kids | great-?grand |
        the\s+love\s+of | best\s+friend |
        a\s+funeral | funeral\s+service | donations
    )
    """,
)

REL_ONLY = re.compile(rf"(?i)^(?:{REL_ALT})$")

REVERSE_CLAUSE = re.compile(
    rf"""(?ix)
    ^(?P<name>[A-Z][^,]{{1,60}}?)
    (?:,\s*(?P<place>[A-Z][^,]{{1,40}}))?
    ,\s*(?P<rel>{REL_ALT})
    \s*$
    """,
)


def derive_subject(page: dict) -> str:
    title = (page.get("title") or "").strip()
    t = re.sub(
        r"^(?:SCVHistory\.com\s+\S+\s*\|\s*)?(?:Tataviam\s+Culture\s*\|\s*)?"
        r"(?:Film-Arts\s*\|\s*)?(?:Rancho\s+Camulos\s*\|\s*)?"
        r"(?:Obits?|Obituaries?|Obituary)\s*(?:\||:)\s*",
        "",
        title,
        flags=re.I,
    ).strip()
    if not t:
        t = title
    if ":" in t:
        left = t.split(":", 1)[0].strip()
        if len(left) >= 3:
            t = left
    if "," in t:
        left = t.split(",", 1)[0].strip()
        if len(left) >= 3:
            t = left
    t = re.sub(r"\s*\(\d{4}\s*[-–—]\s*\d{4}\.?\)\s*$", "", t).strip()
    t = re.sub(r"\s+\d{4}\s*[-–—]\s*\d{4}\.?\s*$", "", t).strip()
    t = t.rstrip(".").strip()
    # Strip leftover Obituary prefix if any
    t = re.sub(r"^(?:Obits?|Obituaries?|Obituary)\s*\|\s*", "", t, flags=re.I).strip()
    t = strip_headline(t)
    if not t and page.get("people_mentioned"):
        t = page["people_mentioned"][0].get("name_raw") or ""
    return t


# Headline wording around the name in news-style obituary titles
# ("Lifelong Newhall Resident X Dies at 104", "Cancer Claims Life of ... Descendant X",
# "X Obituary & Death Certificate"). Generic role/verb words only; no names.
HEADLINE_ROLE = re.compile(
    r"(?i)^.*\b(?:founder|resident|developer|creator|descendant|pioneer|producer|"
    r"actor|actress|star|executive|cartoonist|rancher|leader|native)\s+(?=[A-Z])"
)
HEADLINE_TAIL = re.compile(r"\s+(?:Dies|Died|Dead|Killed|Passes(?:\s+Away)?|Obituary)\b.*$")


def strip_headline(t: str) -> str:
    orig = t
    # leftover section prefix ("Rancho Camulos | Name ...")
    if "|" in t:
        t = t.rsplit("|", 1)[1].strip()
    t = HEADLINE_TAIL.sub("", t).strip()
    m = HEADLINE_ROLE.match(t)
    if m:
        rest = t[m.end():].strip()
        # keep only if at least two name-like tokens remain
        if len(re.findall(r"[A-Z][\w.'\-]*", rest)) >= 2:
            t = rest
    return t if len(t) >= 3 else orig


def normalize_ws(s: str) -> str:
    s = s.replace("\xa0", " ").replace("\u00a0", " ").replace("�", " ")
    s = re.sub(r"[ \t]+", " ", s)
    s = re.sub(r"\n+", " ", s)
    return s.strip()


def split_sentences(text: str) -> list[str]:
    text = normalize_ws(text)
    protected = text
    placeholders: dict[str, str] = {}
    n = 0

    def protect(m: re.Match) -> str:
        nonlocal n
        ph = f"__P{n}__"
        n += 1
        placeholders[ph] = m.group(0)
        return ph

    # Protect common abbrevs; single-letter initials only when not roman-numeral-ish
    # before a capital pronoun start of sentence (handled later by bleed trim)
    protected = re.sub(
        r"\b(?:Mrs|Mr|Ms|Miss|Dr|Jr|Sr|St|Gen|Col|Capt|Lt|Rev|Fr|Gov|Sgt|"
        r"Blvd|Ave|Rd|Co|Inc|Corp|Ltd|Dept|No|vs|etc|"
        r"Jan|Feb|Mar|Apr|Jun|Jul|Aug|Sep|Sept|Oct|Nov|Dec|"
        r"U\.S|U\.S\.A|D\.C|a\.m|p\.m|[A-Z])\.",
        protect,
        protected,
    )
    parts = re.split(r"(?<=[.!?])\s+(?=[A-Z\"'“])", protected)
    out = []
    for p in parts:
        for ph, orig in placeholders.items():
            p = p.replace(ph, orig)
        p = p.strip()
        if p:
            out.append(p)
    return out


def strip_place_suffix(name: str) -> str:
    name = re.sub(
        r"""(?ix)
        ,?\s+of\s+
        (?:
            [A-Z][A-Za-z.'\-]+(?:\s+[A-Z][A-Za-z.'\-]+){0,3}
            (?:,\s*(?:[A-Z]{2}\.?|Calif\.?|California|Ariz\.?|Arizona|
                       Wyo\.?|Wyoming|HI|AB|MB|PA|OH|NY|MT|AR|
                       [A-Z][a-z]+))?
        )
        \s*$
        """,
        "",
        name,
    ).strip()
    # "Cody, Wyo." leftover when "of" already consumed oddly
    name = re.sub(
        r"(?i),?\s+(?:Wyo\.?|Calif\.?|Ariz\.?|[A-Z]{2})\s*$",
        "",
        name,
    ).strip()
    return name


def clean_person_name(name: str) -> str:
    name = normalize_ws(name)
    name = name.strip(" ;,.")
    name = re.sub(r"^(?:and|or|&|also|by|as\s+well\s+as)\s+", "", name, flags=re.I)
    name = re.sub(r"^(?:his|her|their|my|our)\s+", "", name, flags=re.I)
    # Cut trailing sentence bleed: "Name V. He was..."
    name = re.split(r"(?<=\.)\s+(?=He|She|They|It|We|His|Her)\b", name)[0].strip()
    name = re.split(
        r"""(?ix)
        ,\s*in\s+(?:\d{4}|January|February|March|April|May|June|July|
                    August|September|October|November|December) |
        ,\s*after\s+ |
        ,\s*from\s+a\s+previous |
        ,\s*who\b |
        ,\s*whom\b |
        ,\s*he\b |
        ,\s*she\b |
        \s+in\s+\d{4}\b |
        \s+after\s+\d+ |
        \s+from\s+a\s+(?:previous|former)
        """,
        name,
    )[0].strip()
    name = strip_place_suffix(name)
    name = re.sub(r",?\s*\d{1,3}\s*(?:years?\s+old)?\s*$", "", name).strip()
    # "best friend Name"
    name = re.sub(r"(?i)^(?:best\s+friend|dear\s+friend|friend)\s+", "", name).strip()
    name = re.sub(r"(?i)^the\s+love\s+of\s+(?:her|his|their)\s+life,?\s*", "", name).strip()
    # Date scraps
    name = re.sub(
        r"(?i)\s+(?:January|February|March|April|May|June|July|August|"
        r"September|October|November|December)\b.*$",
        "",
        name,
    ).strip()
    name = re.sub(r"\s+\d{1,2},?\s+\d{4}\b.*$", "", name).strip()
    name = name.strip(" ;,.")
    name = re.sub(r"\s+", " ", name)
    return name


def is_good_person(person: str) -> bool:
    if not person or len(person) < 2 or len(person) > 80:
        return False
    if REL_ONLY.match(person):
        return False
    if JUNK_PERSON.match(person):
        return False
    if person.lower().rstrip(".") in PLACE_NAMES:
        return False
    if re.fullmatch(r"[A-Z]{2}", person):
        return False
    if re.fullmatch(r"[A-Z]{2}\s+Canada", person, re.I):
        return False
    if not re.match(r"^(?:[A-Z\"'“]|Mrs\.|Mr\.|Ms\.|Miss|Dr\.)", person):
        return False
    if re.fullmatch(rf"(?i){NUMBER}(?:\s+\w+)?", person):
        return False
    if person.lower() in {
        "california", "the same name", "his own", "six", "family",
        "friends", "pennsylvania", "he", "she",
    }:
        return False
    if re.match(rf"(?i)^(?:{REL_ALT})\s+[A-Z]", person):
        return False
    # Reject if looks like boilerplate sentence
    if re.search(r"(?i)\b(funeral|service will|donations|cemetery|marriage|pioneer|police officers|where he|where she)\b", person):
        return False
    if re.search(r"(?i)\bwas\b", person) and len(person) > 30:
        return False
    if person.endswith(")"):
        return False
    return True


def split_name_list(names: str) -> list[str]:
    names = normalize_ws(names)
    if not names:
        return []
    people: list[str] = []
    chunks = re.split(r"\s*;\s*", names)
    for chunk in chunks:
        chunk = chunk.strip()
        if not chunk:
            continue
        parts: list[str] = []
        buf = ""
        depth = 0
        i = 0
        while i < len(chunk):
            c = chunk[i]
            if c == "(":
                depth += 1
                buf += c
                i += 1
                continue
            if c == ")":
                depth = max(0, depth - 1)
                buf += c
                i += 1
                continue
            if depth == 0:
                if c == ",":
                    parts.append(buf.strip())
                    buf = ""
                    i += 1
                    if i < len(chunk) and chunk[i] == " ":
                        i += 1
                    continue
                m = re.match(r"^(?:\s+and\s+|\s+&\s+|\s+or\s+)", chunk[i:], re.I)
                if m and buf.strip():
                    rest = chunk[i + m.end() :]
                    if re.match(r"(?i)^(?:his|her|their)?\s*(?:wife|husband)\b", rest):
                        parts.append(buf.strip())
                        buf = ""
                        break
                    parts.append(buf.strip())
                    buf = ""
                    i += m.end()
                    continue
            buf += c
            i += 1
        if buf.strip():
            parts.append(buf.strip())
        for p in parts:
            p = clean_person_name(p)
            if is_good_person(p):
                people.append(p)
    return people


def parse_role_clause(clause: str) -> list[tuple[str, str]]:
    clause = normalize_ws(clause).strip(" ;,")
    if not clause:
        return []
    clause = re.sub(r"^(?:(?:and|by|or)\s+)+", "", clause, flags=re.I).strip()
    clause = re.sub(
        r"(?i)^(?:the\s+apple\s+of\s+(?:his|her|their)\s+eye|the\s+light\s+of\s+(?:his|her|their)\s+life)\s*,\s*",
        "",
        clause,
    ).strip()

    # Reverse: "First Surname, Place, wife"
    rev = REVERSE_CLAUSE.match(clause)
    if rev:
        person = clean_person_name(rev.group("name"))
        rel = rev.group("rel")
        if is_good_person(person):
            return [(rel, person)]

    dual = re.match(
        r"""(?ix)
        ^(?P<n1>[A-Z][^&,]{1,50}?)\s*(?:&|and)\s*(?P<n2>[A-Z][^,]{1,50}?)\s*,\s*
        (?:his|her|their)\s+(?P<r1>mother|father|parents?)\s+and\s+(?P<r2>mother|father|parents?)
        \s*$
        """,
        clause,
    )
    if dual:
        out = []
        for n, r in ((dual.group("n1"), dual.group("r1")), (dual.group("n2"), dual.group("r2"))):
            person = clean_person_name(n)
            if is_good_person(person):
                out.append((r, person))
        if out:
            return out

    m = ROLE_HEAD.match(clause)
    if not m:
        return []

    rel = m.group("rel")
    after = clause[m.end() :].strip()
    after = re.sub(r"^[\s:,\-–—]+", "", after)
    after = re.sub(r"^(?:and|or)\s+", "", after, flags=re.I)
    after = re.sub(r"(?i)^and\s+best\s+friend\s+", "", after)
    # Do not bleed into the next sentence (keep middle initials like "W. Asher")
    after = re.split(
        r"(?<=\.)\s+(?=(?:Mr|Mrs|Ms|Miss|Dr)\.?\s+[A-Z]|(?:He|She|They|His|Her|Their|The)\s)",
        after,
    )[0].strip()

    if not after or re.fullmatch(rf"(?i){NUMBER}(?:\s+\w+)?", after):
        return []

    return [(rel, n) for n in split_name_list(after)]


def split_by_role_boundaries(chunk: str) -> list[str]:
    """Split a semicolon-free chunk on mid-list role markers."""
    chunk = chunk.strip()
    if not chunk:
        return []
    # If reverse pattern matches whole chunk, keep intact
    if REVERSE_CLAUSE.match(chunk.strip(" ;,")):
        return [chunk]

    matches = list(ROLE_BOUNDARY.finditer(chunk))
    if len(matches) <= 1:
        return [chunk]

    starts = [m.start() for m in matches]
    # If first match isn't at 0, keep preamble only when it itself has a role/reverse
    clauses = []
    if starts[0] != 0:
        preamble = chunk[: starts[0]].strip(" ;,")
        preamble_n = re.sub(r"^(?:and|by|or)\s+", "", preamble, flags=re.I).strip()
        if preamble_n and (ROLE_HEAD.match(preamble_n) or REVERSE_CLAUSE.match(preamble_n)):
            clauses.append(preamble_n)
        indices = starts
    else:
        indices = starts

    for i, st in enumerate(indices):
        en = indices[i + 1] if i + 1 < len(indices) else len(chunk)
        part = chunk[st:en].strip()
        part = re.sub(r"^(?:,\s*|\s*and\s+|\s*by\s+|and\s+|by\s+)", "", part, flags=re.I)
        if part:
            clauses.append(part)
    return clauses or [chunk]


def find_clause_spans(tail: str) -> list[str]:
    """Split survival tail into role clauses. Prefer semicolons, then role boundaries."""
    tail = normalize_ws(tail).rstrip(".")
    tail = re.sub(r"(?i)\salong\s+with\s+", "; ", tail)

    # Semicolon-first: preserves "Name, Place, role" reverse lists
    if ";" in tail:
        rough = [c.strip() for c in re.split(r"\s*;\s*", tail) if c.strip()]
        mended: list[str] = []
        i = 0
        while i < len(rough):
            cur = rough[i]
            cur_name = re.sub(r"^(?:and\s+)", "", cur, flags=re.I).strip()
            if (
                i + 1 < len(rough)
                and re.match(r"^[A-Z][\w'.\-]+(?:\s+[A-Z][\w'.\-]+){0,4}$", cur_name)
                and REVERSE_CLAUSE.match(rough[i + 1].strip(" ;,"))
                and "," in rough[i + 1]
            ):
                mended.append(f"{cur_name}, {rough[i + 1].strip()}")
                i += 2
                continue
            mended.append(cur)
            i += 1
        clauses: list[str] = []
        for chunk in mended:
            chunk_n = re.sub(r"^(?:and\s+)", "", chunk, flags=re.I).strip()
            if REVERSE_CLAUSE.match(chunk_n.strip(" ;,")):
                clauses.append(chunk_n)
            else:
                clauses.extend(split_by_role_boundaries(chunk))
        return clauses

    return split_by_role_boundaries(tail)


def extract_from_survival_sentence(sentence: str, survival: str) -> list[dict]:
    if survival == "survived_by":
        if SURVIVED_FALSE.search(sentence):
            return []
        m = SURVIVED_RE.search(sentence)
    else:
        m = PRECEDED_RE.search(sentence)
    if not m:
        return []
    tail = sentence[m.end() :]
    # A sentence may carry both markers ("preceded in death by X and survived by Y").
    # Stop this tail where the other survival marker begins so each person keeps
    # the survival status the text states for them.
    other = PRECEDED_RE if survival == "survived_by" else SURVIVAL_CUT
    om = other.search(tail)
    if om:
        tail = tail[: om.start()]
        tail = re.sub(r"(?i)[\s,;]*(?:\band\b|\bshe\b|\bhe\b|\bis\b|\bwas\b)?[\s,;]*$", "", tail)
    tail = tail.strip().rstrip(".")
    out = []
    for cl in find_clause_spans(tail):
        for rel, person in parse_role_clause(cl):
            out.append(
                {
                    "person_raw": person,
                    "relationship_raw": rel,
                    "survival_raw": survival,
                    "sentence_raw": sentence.strip(),
                }
            )
    return out


def extract_kin_of(sentence: str) -> list[dict]:
    out = []
    if SURVIVED_RE.search(sentence) or PRECEDED_RE.search(sentence):
        return out
    # Skip header-ish lines that mash title + dates
    if re.search(r"(?i)\bOBITUARIES?\b", sentence) and re.search(
        r"\d{4}", sentence
    ):
        # still allow if clear prose "mother of Mrs. X, passed"
        if not re.search(r"(?i)\bpassed\b|\bdied\b|\bwas born\b", sentence):
            return out

    for m in KIN_OF_RE.finditer(sentence):
        rel = m.group("rel")
        # Skip ALL-CAPS / title-case organization headers ("Wives of ... Officers")
        if rel[0].isupper() and rel.lower() != rel and not sentence.strip().lower().startswith(
            ("he ", "she ", "they ", "was ", "is ", "the ", "a ", "an ", "on ", "in ")
        ):
            # allow normal prose "Mother of X, passed" which we handle via passed/died check above
            if "Wives of" in sentence or "Police Officers" in sentence:
                continue
        people_span = m.group("people").strip()
        people_span = re.split(
            r"(?i)\s+(?:who|whom|passed|died|has died|was born|born in|grew|lived|remained|,?\s*he\b|,?\s*she\b)",
            people_span,
        )[0].strip()
        people_span = people_span.rstrip(",;.")
        stripped = DESC_PREFIX.sub("", people_span).strip()
        if stripped and re.match(r"^[A-Z]", stripped):
            people_span = stripped
        if re.match(r"(?i)^(?:six|five|four|three|two|one)\b", people_span):
            continue
        if not re.match(r"^(?:the\s+late\s+)?[A-Z\"']", people_span):
            continue
        people_span = re.sub(
            r"(?i)\s+(?:January|February|March|April|May|June|July|August|"
            r"September|October|November|December)\b.*$",
            "",
            people_span,
        ).strip()
        people_span = re.sub(r"\s+\d{1,2},?\s+\d{4}\b.*$", "", people_span).strip()
        people_span = re.split(r",\s+(?:a|an|the|who|which|whom)\b", people_span, maxsplit=1)[0].strip()

        if re.search(r"\band\b", people_span, re.I):
            parts = re.split(r"\s+and\s+", people_span, flags=re.I)
            if len(parts) == 2 and all(
                re.match(r"^(?:the\s+late\s+)?[A-Z]", p.strip()) for p in parts
            ):
                people = [clean_person_name(p) for p in parts]
            else:
                people = [clean_person_name(people_span)]
        else:
            people = [clean_person_name(people_span)]

        for person in people:
            if person in {"Mrs", "Mr", "Ms", "Miss", "Dr"}:
                continue
            if not is_good_person(person):
                continue
            out.append(
                {
                    "person_raw": person,
                    "relationship_raw": rel,
                    "survival_raw": "stated",
                    "sentence_raw": sentence.strip(),
                }
            )
    return out


# ---------------------------------------------------------------- retention
# Principle: do not build a structured graph of living people. A relationship is
# kept only when the text or the date establishes the named person is deceased.
RETENTION_YEARS = 72


def cutoff_year(today: date | None = None) -> int:
    """Obituaries printed in this year or earlier qualify as pre_cutoff. Rolls forward yearly."""
    return (today or date.today()).year - RETENTION_YEARS


def printed_year(page: dict) -> int | None:
    """Latest four-digit year in date_raw (for a life-dates range this is the death year)."""
    ys = [int(y) for y in re.findall(r"\b(1[5-9]\d\d|20\d\d)\b", page.get("date_raw") or "")]
    return max(ys) if ys else None


DECEASED_AFTER = re.compile(
    r"""(?ix)^\s*(?:
        \(\s*(?:deceased|dec\.?|d\.\s*\d{4})\s*\) |
        ,?\s*(?:both\s+|all\s+|now\s+)?deceased\b |
        ,?\s*who\s+(?:had\s+)?(?:died|passed\s+away|passed|was\s+killed|predeceased)\b |
        ,?\s*(?:who\s+)?preceded\s+(?:him|her|them)\s+in\s+death
    )"""
)
# "the late X" / "her late husband, X": "late" directly before the name, or
# before a single kinship word. "the late A and B" marks A only.
DECEASED_BEFORE = re.compile(
    r"(?i)\blate\s+(?:(?:husband|wife|spouse|son|daughter|father|mother|brother|sister|"
    r"parents?|grandfather|grandmother|uncle|aunt)\s*,?\s*)?$"
)


def stated_deceased(rel: dict) -> bool:
    person = rel["person_raw"]
    # "X, both deceased" / "X (deceased)" only when it closes the name; a
    # "deceased" inside the name can belong to someone else ("First (Spouse,
    # deceased 2003) Surname").
    if re.search(r"(?i)(?:,\s*(?:both\s+|all\s+)?deceased|\(\s*deceased\s*\))$", person):
        return True
    if re.search(r"(?i)\bdeceased\b", person):
        return False
    sent = rel["sentence_raw"]
    start = 0
    while True:
        i = sent.find(person, start)
        if i < 0:
            return False
        before = sent[max(0, i - 40):i]
        after = sent[i + len(person):i + len(person) + 60]
        if DECEASED_AFTER.search(after):
            return True
        if DECEASED_BEFORE.search(before):
            return True
        start = i + 1


def retention_basis(rel: dict, year: int | None, cutoff: int) -> str:
    if rel["survival_raw"] == "preceded_in_death":
        return "preceded_in_death"
    if stated_deceased(rel):
        return "stated_deceased"
    if year is not None and year <= cutoff:
        return "pre_cutoff"
    return ""


def era(year: int | None) -> str:
    if year is None:
        return "undated"
    if year <= 1954:
        return "<=1954"
    if year <= 1980:
        return "1955-1980"
    if year <= 2000:
        return "1981-2000"
    if year <= 2010:
        return "2001-2010"
    return "2011+"


def extract_page_relationships(page: dict) -> list[dict]:
    subject = derive_subject(page)
    text = page.get("body_text") or ""
    sentences = split_sentences(text)
    seen: set[tuple] = set()
    results = []

    def add(rel: dict) -> None:
        key = (
            rel["person_raw"].lower(),
            rel["relationship_raw"].lower(),
            rel["survival_raw"],
            rel["sentence_raw"][:100],
        )
        if key in seen:
            return
        seen.add(key)
        results.append(
            {
                "person_raw": rel["person_raw"],
                "relationship_raw": rel["relationship_raw"],
                "subject_raw": subject,
                "sentence_raw": rel["sentence_raw"],
                "survival_raw": rel.get("survival_raw") or "",
            }
        )

    for sent in sentences:
        if SURVIVED_RE.search(sent) and not SURVIVED_FALSE.search(sent):
            for rel in extract_from_survival_sentence(sent, "survived_by"):
                add(rel)
        if PRECEDED_RE.search(sent):
            for rel in extract_from_survival_sentence(sent, "preceded_in_death"):
                add(rel)
        for rel in extract_kin_of(sent):
            add(rel)

    return results


def main() -> None:
    with open(OBITS, encoding="utf-8") as f:
        data = json.load(f)

    pages = data["pages"]
    assert len(pages) == 595, f"expected 595 pages, got {len(pages)}"
    cutoff = cutoff_year()

    extracted = 0
    kept = 0
    basis_counts: Counter[str] = Counter()
    rel_counts: Counter[str] = Counter()
    kept_era: Counter[str] = Counter()
    dropped_era: Counter[str] = Counter()
    pages_era: Counter[str] = Counter()
    pages_with = 0
    paren_dropped = 0

    for page in pages:
        year = printed_year(page)
        e = era(year)
        pages_era[e] += 1
        out = []
        for r in extract_page_relationships(page):
            extracted += 1
            basis = retention_basis(r, year, cutoff)
            # A parenthetical inside a name usually carries a spouse or partner
            # ("Name (Spouse) Surname") whose death the text does not establish.
            if basis and "(" in r["person_raw"] and not stated_deceased(r):
                paren_dropped += 1
                basis = ""
            if not basis:
                dropped_era[e] += 1
                continue
            r["retention_basis"] = basis
            out.append(r)
            basis_counts[basis] += 1
            rel_counts[r["relationship_raw"].lower()] += 1
            kept_era[e] += 1
        page["relationships"] = out
        kept += len(out)
        if out:
            pages_with += 1

    with open(OBITS, "w", encoding="utf-8") as f:
        json.dump(data, f, ensure_ascii=False, indent=2)
        f.write("\n")

    eras = ["<=1954", "1955-1980", "1981-2000", "2001-2010", "2011+", "undated"]
    report = {
        "page_count": len(pages),
        "retention_rule": (
            "Do not build a structured graph of living people. A relationship is kept only if "
            "survival_raw is preceded_in_death, or the obituary text states the named person is "
            "deceased, or the obituary's printed year (latest year in date_raw) is at or before the "
            f"cutoff year (current year minus {RETENTION_YEARS}). Undated obituaries qualify only by the "
            "first two. A name carrying a parenthetical (usually a living spouse, e.g. 'Name (Spouse) "
            "Surname') is not stored unless the text states that person is deceased. Everything else "
            "is not stored in the structured array."
        ),
        "cutoff_year": cutoff,
        "retention_years": RETENTION_YEARS,
        "relationships_extracted_before_retention": extracted,
        "relationships_kept": kept,
        "relationships_dropped": extracted - kept,
        "pages_with_relationships": pages_with,
        "dropped_parenthetical_name_count": paren_dropped,
        "kept_by_retention_basis": dict(basis_counts),
        "kept_by_relationship_raw": [
            {"relationship_raw": k, "count": v}
            for k, v in sorted(rel_counts.items(), key=lambda x: (-x[1], x[0]))
        ],
        "pages_by_printed_era": {k: pages_era.get(k, 0) for k in eras},
        "kept_by_printed_era": {k: kept_era.get(k, 0) for k in eras},
        "dropped_by_printed_era": {k: dropped_era.get(k, 0) for k in eras},
        "notes": (
            "Aggregate counts only; no names or quoted sentences. Stated relationships only; no identity "
            "resolution or merging. Existing page fields left intact."
        ),
    }
    with open(REPORT, "w", encoding="utf-8") as f:
        json.dump(report, f, ensure_ascii=False, indent=2)
        f.write("\n")

    print(f"cutoff year: {cutoff}")
    print(f"extracted: {extracted}  kept: {kept}  dropped: {extracted - kept}")
    print("kept by basis:", dict(basis_counts))
    print("kept by era:", {k: kept_era.get(k, 0) for k in eras})
    print("dropped by era:", {k: dropped_era.get(k, 0) for k in eras})
    print("top relationship_raw (kept):", report["kept_by_relationship_raw"][:20])


if __name__ == "__main__":
    main()
