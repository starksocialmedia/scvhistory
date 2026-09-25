#!/usr/bin/env python3
"""Apply the living-people rule to structured name fields of obituaries.json.

Principle (GROK-CONTRACT.md, "Living people"): do not build a structured graph of
living people. On a post-cutoff obituary (latest year in date_raw after the cutoff, or
undated) structured fields may carry a person's name only if the person is the
obituary subject or the text establishes the person is deceased, using the storage
rule of `relationships` (extract_relationships.py): preceded_in_death, stated_deceased,
pre_cutoff. Removed names remain only in body_text/body_html prose.

Run after extract_relationships.py. What it does, post-cutoff pages only:
- people_mentioned: keep the subject and names passing the rule; others removed.
  Kept entries carry retention_basis.
- relationships: stated_deceased entries are re-checked with the stricter
  attachment test below; sentence_raw is trimmed to the preceded-in-death clause or
  the clause stating the death (verbatim substring of body_text); set to null when
  that clause holds a parenthetical name ("Name (Spouse) Surname").
- orgs_mentioned: entries whose name never occurs on a single line of body_text
  (glued across a line break, often a person's name + an org) are dropped;
  entity_index.organizations is rebuilt from orgs_mentioned.
- dates_mentioned: a context snippet is kept only when every capitalized
  name-like token in it is accounted for (subject, kept names, known places/orgs,
  common words); otherwise context is set to null and the date value is kept.
- needs_review possible_same_person groupings kept only when every grouped name is
  still indexed on that page.
All pages: entity_index.people rebuilt from people_mentioned.

Subject matching ignores degrees (M.D., Ph.D.) but not generational suffixes: a name
whose Jr./Sr./II/III/IV/2nd/3rd differs from the subject's (including missing) is not
the subject unless every occurrence of it in body_text carries the subject's suffix.
Otherwise it goes through the normal rule.

stated_deceased attachment: "the late X" and "X (deceased)" / "X, deceased" attach to
X. "X, who died/passed away ..." counts only when a following date differs from the
subject's death (year, or month when no year), or, with no date, when X directly
follows a kinship word ("his wife, X, who died"). Otherwise the phrase may describe the
subject; the name is not kept and is listed in the review file.

Idempotent. No personal names are hardcoded. The committed report is aggregate-only;
an optional review file (names + URLs, for a human, never committed) is written with
--review-out PATH.
"""
from __future__ import annotations

import argparse
import json
import re
import sys
from collections import Counter
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))
from extract_relationships import (  # noqa: E402
    DECEASED_BEFORE,
    PRECEDED_RE,
    REL_ALT,
    SURVIVAL_CUT,
    cutoff_year,
    derive_subject,
    normalize_ws,
    printed_year,
    split_sentences,
)

from scv_data import obituaries_dir  # noqa: E402

# Obituary data lives in the private repo starksocialmedia/scvhistory-data (see scv_data.py).
INV = obituaries_dir()
OBITS = INV / "obituaries.json"
REPORT = INV / "obituaries_living_rule_report.json"

GEN_SUFFIXES = {"jr", "sr", "ii", "iii", "iv", "2nd", "3rd", "4th"}
DEGREES = {"md", "m.d", "phd", "ph.d", "esq", "dds", "ret"}
HONORIFICS = {"mr", "mrs", "ms", "miss", "dr", "rev", "fr", "capt", "col", "lt", "gen", "sgt"}
SURNAME_GROUP = re.compile(r"^Multiple name forms with surname [^:]+:\s*(?P<names>.+)$")
MONTHS = ["january", "february", "march", "april", "may", "june", "july", "august",
          "september", "october", "november", "december"]
MONTH_RE = re.compile(r"(?i)\b(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sept?|Oct|Nov|Dec)[a-z]*\.?")
YEAR_RE = re.compile(r"\b(1[5-9]\d\d|20\d\d)\b")
SUFFIX_AFTER = re.compile(r"^,?\s*(Jr|Sr|II|III|IV|2nd|3rd|4th)\b\.?", re.I)

# Deceased markers after a name, split by kind.
DEC_AFTER_MARK = re.compile(
    r"""(?ix)^\s*(?:
        \(\s*(?:deceased|dec\.?|d\.\s*\d{4})\s*\) |
        ,?\s*(?:both\s+|all\s+|now\s+)?deceased\b |
        ,?\s*(?:who\s+)?preceded\s+(?:him|her|them)\s+in\s+death
    )"""
)
DEC_AFTER_WHO = re.compile(
    r"(?i)^\s*,?\s*who\s+(?:had\s+)?(?:died|passed\s+away|passed|was\s+killed|predeceased)\b"
)
KIN_BEFORE = re.compile(
    rf"(?i)\b(?:{REL_ALT})(?:\s+of\s+(?:\d+|\w+)\s+years?)?\s*,?\s*$"
)
DATE_AFTER = re.compile(r"^[^.;]{0,40}")


# ------------------------------------------------------------------ subject match
def _tokens(s: str) -> list[str]:
    s = normalize_ws(s)
    s = re.sub(r"\([^)]*\)", " ", s)
    s = re.sub(r"[\"'“”‘’`]+", " ", s)
    toks = [t.strip(".,;:").lower() for t in s.split()]
    return [t for t in toks if t and t not in HONORIFICS]


def _split_suffix(toks: list[str]) -> tuple[list[str], set[str]]:
    core, suf = [], set()
    for t in toks:
        k = t.replace(".", "")
        if k in GEN_SUFFIXES:
            suf.add(k)
        elif k in {d.replace(".", "") for d in DEGREES}:
            continue
        else:
            core.append(t)
    return core, suf


def _tok_eq(a: str, b: str) -> bool:
    return a == b or (len(a) == 1 and b.startswith(a)) or (len(b) == 1 and a.startswith(b))


def _core_match(n_core: list[str], s_core: list[str]) -> bool:
    if len(n_core) < 2 or not s_core:
        return False
    if _tok_eq(n_core[0], s_core[0]) and all(t in s_core[1:] for t in n_core[1:]):
        return True
    for i in range(len(s_core) - len(n_core) + 1):
        if n_core == s_core[i:i + len(n_core)]:
            return True
    return False


def subject_match(name: str, subject: str, body: str) -> str:
    """'subject', 'suffix_evidence' (every occurrence carries the subject's suffix),
    'suffix_collision' (core matches, suffix differs) or ''."""
    n_core, n_suf = _split_suffix(_tokens(name))
    s_core, s_suf = _split_suffix(_tokens(subject))
    if not _core_match(n_core, s_core):
        return ""
    if n_suf == s_suf:
        return "subject"
    if s_suf and not n_suf:
        occ = [m.end() for m in re.finditer(r"(?<!\w)" + re.escape(name) + r"(?!\w)", body)]
        if occ and all((m := SUFFIX_AFTER.match(body[e:])) and m.group(1).lower() in s_suf for e in occ):
            return "suffix_evidence"
    return "suffix_collision"


# ------------------------------------------------------------------ death tests
def subject_death(page: dict) -> tuple[int | None, int | None]:
    raw = page.get("date_raw") or ""
    ys = [int(y) for y in YEAR_RE.findall(raw)]
    year = max(ys) if ys else None
    month = None
    if year is not None:
        # month of the last dated expression ending in that year
        for m in re.finditer(r"(?i)\b([A-Z][a-z]+)\.?\s+\d{1,2},?\s+" + str(year), raw):
            mm = MONTH_RE.match(m.group(1))
            if mm:
                month = _month_num(mm.group(0))
    return year, month


def _month_num(s: str) -> int | None:
    s = s.lower().rstrip(".")[:3]
    for i, m in enumerate(MONTHS):
        if m.startswith(s):
            return i + 1
    return None


def _occ(name: str, sent: str) -> list[int]:
    return [m.start() for m in re.finditer(r"(?<![\w])" + re.escape(name) + r"(?![\w])", sent)]


def death_attach(name: str, sent: str, death: tuple[int | None, int | None]):
    """Return (verdict, span) with verdict in {'yes','subject','ambiguous','no'} and span
    the (start, end) in sent of the clause stating the death when verdict == 'yes'."""
    best = "no"
    for i in _occ(name, sent):
        before = sent[max(0, i - 40):i]
        j = i + len(name)
        after = sent[j:j + 80]
        mb = DECEASED_BEFORE.search(before)
        if mb:
            return "yes", (max(0, i - 40) + mb.start(), j)
        mm = DEC_AFTER_MARK.match(after)
        if mm:
            return "yes", (i, j + mm.end())
        mw = DEC_AFTER_WHO.match(after)
        if not mw:
            continue
        tail = DATE_AFTER.match(after[mw.end():]).group(0)
        ys = [int(y) for y in YEAR_RE.findall(tail)]
        mo = MONTH_RE.search(tail)
        mon = _month_num(mo.group(0)) if mo else None
        end = j + mw.end()
        dm = re.match(r"(?i)^[^.;,]*?\b(?:1[5-9]\d\d|20\d\d)\b", after[mw.end():])
        if dm:
            end = j + mw.end() + dm.end()
        if ys:
            if death[0] is not None and ys[0] == death[0]:
                best = "subject" if best == "no" else best
                continue
            return "yes", (i, end)
        if mon is not None:
            if death[1] is not None and mon == death[1]:
                best = "subject" if best == "no" else best
                continue
            return "yes", (i, end)
        if KIN_BEFORE.search(before):
            return "yes", (i, end)
        best = "ambiguous"
    return best, None


def preceded_span(sent: str) -> tuple[int, int] | None:
    m = PRECEDED_RE.search(sent)
    if not m:
        return None
    start = m.end()
    cut = SURVIVAL_CUT.search(sent[start:])
    return start, start + (cut.start() if cut else len(sent) - start)


def _in_parens(s: str, pos: int) -> bool:
    depth = 0
    for c in s[:pos]:
        depth += c == "("
        depth -= c == ")" and depth > 0
    return depth > 0


def name_basis(name, sentences, death, amb_log):
    """preceded_in_death / stated_deceased / '' for a name on a post-cutoff page."""
    verdicts = []
    for sent in sentences:
        if not _occ(name, sent):
            continue
        span = preceded_span(sent)
        if span:
            tail = sent[span[0]:span[1]]
            for pos in _occ(name, sent):
                if span[0] <= pos < span[1] and not _in_parens(tail, pos - span[0]):
                    return "preceded_in_death"
        if "(" not in name:
            verdicts.append(death_attach(name, sent, death)[0])
    if "yes" in verdicts:
        return "stated_deceased"
    if "ambiguous" in verdicts:
        amb_log.append("ambiguous")
    elif "subject" in verdicts:
        amb_log.append("subject")
    return ""


# ------------------------------------------------------------------ verbatim mapping
def verbatim(fragment: str, body: str) -> str | None:
    """Find fragment (whitespace-normalized) in body; return the verbatim body substring."""
    frag = normalize_ws(fragment).strip()
    if not frag:
        return None
    if frag in body:
        return frag
    parts = [re.escape(p) for p in frag.split(" ")]
    pat = r"[\s\xa0\ufffd]+".join(parts)
    m = re.search(pat, body)
    return m.group(0) if m else None


def _strip_clause(s: str) -> str:
    prev = None
    s = s.strip()
    while s != prev:
        prev = s
        s = re.sub(r"(?i)[\s,;:]*(?:\band\b|\bshe\b|\bhe\b|\bis\b|\bwas\b|\balso\b)?[\s,;:]*$", "", s)
        s = s.strip().rstrip(",;:").strip()
    return s


def trim_sentence(rel: dict, death) -> str | None:
    sent = normalize_ws(rel["sentence_raw"])
    if rel.get("retention_basis") == "preceded_in_death":
        m = PRECEDED_RE.search(sent)
        if not m:
            return None
        span = preceded_span(sent)
        start = m.start() + (len(m.group(0)) - len(m.group(0).lstrip()))
        return _strip_clause(sent[start:span[1]])
    if rel.get("retention_basis") == "stated_deceased":
        person = rel["person_raw"]
        if re.search(r"(?i)(?:,\s*(?:both\s+|all\s+)?deceased|\(\s*deceased\s*\))$", person):
            i = sent.find(person)
            if i < 0:
                return None
            span = (i, i + len(person))
        else:
            v, span = death_attach(person, sent, death)
            if v != "yes":
                return None
        start, end = span
        # extend back to the kinship word of this relationship within the clause
        rel_word = rel.get("relationship_raw") or ""
        window_start = max(0, start - 80)
        window = sent[window_start:start]
        if rel_word:
            ms = list(re.finditer(r"(?i)\b" + re.escape(rel_word) + r"\b", window))
            if ms and not re.search(r"[;.]", window[ms[-1].start():]):
                start = window_start + ms[-1].start()
        return _strip_clause(sent[start:end])
    return None  # pre_cutoff relationships live on pre-cutoff pages, which are not touched


PAREN_NAME = re.compile(r"\((?!(?i:\s*(?:deceased|dec\.?|d\.\s*\d{4})\s*\)))[^)]*[A-Z][^)]*\)")


def org_on_one_line(name: str, body: str) -> bool:
    words = [re.escape(w) for w in name.split()]
    if not words:
        return False
    pat = r"(?i)(?<!\w)" + r"[ \t\xa0\ufffd]+".join(words) + r"(?!\w)"
    return re.search(pat, body) is not None


# ------------------------------------------------------------------ dates context
STOPWORDS = set("""
a an the and or but of in on at to for from by with as is was were be been he she it they we i you
his her its their our my your him them this that these those there here then when where while after
before during since until upon into onto over under about between among through throughout also both
all each every some any many most more other another such only just not no yes so if because though
although however later early late born died passed married survived preceded moved grew lived worked
served retired graduated attended became joined she'd he'd following funeral services service memorial
mass rosary visitation viewing burial interment inurnment celebration life cemetery mortuary chapel
church catholic lutheran methodist baptist presbyterian episcopal christian jewish temple synagogue
parish mission high school elementary junior college university academy institute state community
army navy air force marine marines corps coast guard national reserve war world ii korean vietnam
civil wwii ww2 veteran veterans memorial park home funeral hospital medical center valley county city
street avenue road blvd drive lane way highway canyon ranch lake river mountain hills beach
american american's californian north south east west northern southern eastern western new old saint st
mt ft mr mrs ms dr jr sr rev fr father pastor god lord jesus christ christmas easter thanksgiving
january february march april may june july august september october november december jan feb mar apr
jun jul aug sep sept oct nov dec monday tuesday wednesday thursday friday saturday sunday a.m p.m am pm
obituary obituaries obit resident residents native longtime family friends donations in lieu flowers
please information online www com org net inc co company corporation department club society association
foundation united states u.s usa us los angeles santa clarita county california calif ca
""".split())


def context_ok(ctx: str, allowed_phrases: list[str], allowed_tokens: set[str]) -> bool:
    s = normalize_ws(ctx)
    for ph in sorted(set(allowed_phrases), key=len, reverse=True):
        if ph and len(ph) > 2:
            s = re.sub(r"(?i)(?<!\w)" + re.escape(ph) + r"(?!\w)", " ", s)
    for tok in re.findall(r"[A-Za-z][\w'’.\-]*", s):
        t = tok.strip(".'’-").lower()
        if not tok[0].isupper() or not t:
            continue
        if t in STOPWORDS or t in allowed_tokens:
            continue
        if len(t) == 1:  # initials
            continue
        return False
    return True


# ------------------------------------------------------------------ counting
def snapshot(data: dict, cutoff: int) -> dict:
    total = post = 0
    names_all, names_post = set(), set()
    pages_post_with = psp = psp_post = 0
    basis = Counter()
    ctx_total = ctx_null = rels = 0
    for p in data["pages"]:
        y = printed_year(p)
        is_post = y is None or y > cutoff
        pm = p.get("people_mentioned") or []
        total += len(pm)
        names_all.update(n["name_raw"] for n in pm)
        rels += len(p.get("relationships") or [])
        if is_post:
            post += len(pm)
            names_post.update(n["name_raw"] for n in pm)
            pages_post_with += bool(pm)
            for n in pm:
                basis[n.get("retention_basis", "(none)")] += 1
            for d in p.get("dates_mentioned") or []:
                ctx_total += 1
                ctx_null += d.get("context") is None
        k = sum(1 for r in p.get("needs_review") or [] if r.get("reason") == "possible_same_person")
        psp += k
        psp_post += k if is_post else 0
    return {
        "people_mentioned_entries_total": total,
        "people_mentioned_entries_post_cutoff": post,
        "people_mentioned_entries_pre_cutoff": total - post,
        "unique_names_all_pages": len(names_all),
        "unique_names_post_cutoff_pages": len(names_post),
        "post_cutoff_pages_with_people_mentioned": pages_post_with,
        "entity_index_people": len(data["entity_index"]["people"]),
        "needs_review_possible_same_person": psp,
        "needs_review_possible_same_person_post_cutoff": psp_post,
        "post_cutoff_entries_by_retention_basis": dict(basis),
        "relationships": rels,
        "relationships_sentence_raw_null": sum(1 for p in data["pages"] for r in p.get("relationships") or [] if r.get("sentence_raw") is None),
        "orgs_mentioned_entries": sum(len(p.get("orgs_mentioned") or []) for p in data["pages"]),
        "entity_index_organizations": len(data["entity_index"].get("organizations", [])),
        "post_cutoff_dates_mentioned": ctx_total,
        "post_cutoff_dates_mentioned_context_kept": ctx_total - ctx_null,
        "post_cutoff_dates_mentioned_context_null": ctx_null,
    }


def rebuild_people_index(data: dict, cutoff: int) -> list[dict]:
    old = {e["name_raw"]: e for e in data["entity_index"]["people"]}
    agg: dict[str, dict] = {}
    for p in data["pages"]:
        y = printed_year(p)
        is_post = y is None or y > cutoff
        for n in p.get("people_mentioned") or []:
            e = agg.setdefault(n["name_raw"], {"count": 0, "pages": set(), "basis": set()})
            e["count"] += n.get("count", 0)
            e["pages"].add(p["source_url"])
            e["basis"].add(n.get("retention_basis") or ("pre_cutoff" if not is_post else ""))
    out = []
    for name, e in agg.items():
        prev = old.get(name, {})
        out.append({
            "name_raw": name,
            "name_variants": prev.get("name_variants", []),
            "mention_count": e["count"],
            "pages": sorted(e["pages"]),
            "has_legacy_page": prev.get("has_legacy_page", False),
            "legacy_page_url": prev.get("legacy_page_url", ""),
            "retention_basis": sorted(b for b in e["basis"] if b),
        })
    out.sort(key=lambda x: (-x["mention_count"], x["name_raw"]))
    return out


def rebuild_org_index(data: dict) -> list[dict]:
    old = {e["name_raw"]: e for e in data["entity_index"].get("organizations", [])}
    agg: dict[str, dict] = {}
    for p in data["pages"]:
        for o in p.get("orgs_mentioned") or []:
            e = agg.setdefault(o["name_raw"], {"count": 0, "pages": set()})
            e["count"] += o.get("count", 0)
            e["pages"].add(p["source_url"])
    out = []
    for name, e in agg.items():
        prev = old.get(name, {})
        out.append({
            "name_raw": name,
            "name_variants": prev.get("name_variants", []),
            "mention_count": e["count"],
            "pages": sorted(e["pages"]),
            "has_legacy_page": prev.get("has_legacy_page", False),
            "legacy_page_url": prev.get("legacy_page_url", ""),
        })
    out.sort(key=lambda x: (-x["mention_count"], x["name_raw"].lower()))
    return out


def main() -> None:
    ap = argparse.ArgumentParser()
    ap.add_argument("--review-out", help="write names+URLs needing human judgment here (never commit)")
    args = ap.parse_args()

    raw_in = OBITS.read_text(encoding="utf-8")
    data = json.loads(raw_in)
    pages = data["pages"]
    assert len(pages) == 595, f"expected 595 pages, got {len(pages)}"
    cutoff = cutoff_year()
    before = snapshot(data, cutoff)

    # global non-person vocabulary: known place/community names from the index
    ei = data["entity_index"]
    place_phrases = [e.get("name_raw") or e.get("longer_name") or "" for k in
                     ("places", "external_places", "community_mentions") for e in ei.get(k, [])]
    place_phrases += [e.get("longer_name", "") for e in ei.get("community_inferred", [])]

    c = Counter()
    review = {"suffix_collision": [], "stated_deceased_ambiguous": [], "stated_deceased_refers_to_subject": [],
              "relationship_stated_dropped": []}

    for p in pages:
        y = printed_year(p)
        if not (y is None or y > cutoff):
            continue
        body = p.get("body_text") or ""
        subject = derive_subject(p)
        sentences = split_sentences(body)
        death = subject_death(p)

        # people_mentioned
        kept = []
        for n in p.get("people_mentioned") or []:
            name = n["name_raw"]
            sm = subject_match(name, subject, body)
            b = ""
            if sm in ("subject", "suffix_evidence"):
                b = "subject"
                c["subject_via_suffix_evidence"] += sm == "suffix_evidence"
            else:
                log: list[str] = []
                b = name_basis(name, sentences, death, log)
                if sm == "suffix_collision":
                    c["suffix_collision_entries"] += 1
                    c["suffix_collision_kept_by_rule"] += bool(b)
                    if not b:
                        review["suffix_collision"].append({"source_url": p["source_url"], "name": name, "subject": subject})
                if not b and log:
                    key = "stated_deceased_ambiguous" if log[0] == "ambiguous" else "stated_deceased_refers_to_subject"
                    c[key] += 1
                    review[key].append({"source_url": p["source_url"], "name": name})
            if not b:
                c["people_removed"] += 1
                continue
            if n.get("retention_basis") != b:
                n = {**n, "retention_basis": b}
            kept.append(n)
        p["people_mentioned"] = kept

        # relationships: re-check stated_deceased attachment, trim sentence_raw
        rels = []
        for r in p.get("relationships") or []:
            if r.get("retention_basis") == "stated_deceased" and r.get("sentence_raw") is not None:
                person = r["person_raw"]
                marked = re.search(r"(?i)(?:,\s*(?:both\s+|all\s+)?deceased|\(\s*deceased\s*\))$", person)
                if not marked and death_attach(person, normalize_ws(r["sentence_raw"]), death)[0] != "yes":
                    c["relationships_stated_dropped"] += 1
                    review["relationship_stated_dropped"].append({"source_url": p["source_url"], "name": person})
                    continue
            t = trim_sentence(r, death) if r.get("sentence_raw") is not None else None
            if t is not None:
                v = verbatim(t, body)
                if v is None:
                    c["sentence_trim_unmapped"] += 1
                    r = {**r, "sentence_raw": ""}
                elif v != r["sentence_raw"]:
                    c["sentence_raw_trimmed"] += 1
                    r = {**r, "sentence_raw": v}
            # A parenthetical holding a name inside the kept clause usually names a
            # (possibly living) spouse: "Name (Spouse) Surname". Keep the relationship,
            # null the sentence.
            if r.get("sentence_raw") and PAREN_NAME.search(r["sentence_raw"]):
                r = {**r, "sentence_raw": None}
                c["sentence_raw_nulled_parenthetical"] += 1
            rels.append(r)
        p["relationships"] = rels

        # orgs_mentioned: an org name that never occurs on one line of body_text was
        # glued across a line break (typically "<Name>\n<Org>" header lines) and can
        # carry a person's name. Drop it.
        orgs = []
        for o in p.get("orgs_mentioned") or []:
            if org_on_one_line(o["name_raw"], body):
                orgs.append(o)
            else:
                c["orgs_dropped_not_on_one_line"] += 1
        p["orgs_mentioned"] = orgs

        # dates_mentioned contexts
        # Only multi-word kept names mask a context: a bare first name ("Henry") from a
        # preceded-in-death list could otherwise vouch for a different person sharing
        # it plus the subject's surname.
        allowed_phrases = [x for x in [n["name_raw"] for n in kept] + [r["person_raw"] for r in rels]
                           if len(x.split()) >= 2] + [subject]
        allowed_phrases += place_phrases
        allowed_phrases += [e.get("name_raw", "") for k in ("places_mentioned", "external_places_mentioned")
                            for e in p.get(k) or []]
        allowed_phrases += list(p.get("communities_mentioned") or [])
        allowed_tokens = set(_tokens(subject)) | set(_tokens(p.get("title") or "")) & set(_tokens(subject))
        for m in re.finditer(r"[\"'“‘(]([A-Z][\w\-]+)[\"'”’)]", subject):
            allowed_tokens.add(m.group(1).lower())
        for d in p.get("dates_mentioned") or []:
            ctx = d.get("context")
            if ctx is None:
                continue
            if not context_ok(ctx, allowed_phrases, allowed_tokens):
                d["context"] = None
                c["dates_context_nulled"] += 1

        # needs_review surname groupings
        kept_names = {n["name_raw"] for n in kept}
        nr = []
        for r in p.get("needs_review") or []:
            if r.get("reason") == "possible_same_person":
                m = SURNAME_GROUP.match(r.get("detail", ""))
                names = [x.strip() for x in m.group("names").split(";")] if m else []
                if not (names and all(x in kept_names for x in names)):
                    c["possible_same_person_removed"] += 1
                    continue
            nr.append(r)
        p["needs_review"] = nr

    ei["people"] = rebuild_people_index(data, cutoff)
    ei["organizations"] = rebuild_org_index(data)
    after = snapshot(data, cutoff)
    raw_out = json.dumps(data, ensure_ascii=False, indent=2) + "\n"

    if args.review_out:
        Path(args.review_out).write_text(json.dumps(review, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")

    if raw_out == raw_in and REPORT.exists():
        print("no changes (already applied); report left unchanged")
        return
    OBITS.write_text(raw_out, encoding="utf-8")
    report = {
        "page_count": len(pages),
        "cutoff_year": cutoff,
        "rule": (
            "On obituaries printed after the cutoff year (latest year in date_raw) or undated: a name stays in "
            "people_mentioned / entity_index.people only if it is the obituary subject or the text establishes "
            "the person is deceased (preceded_in_death, stated_deceased). A name whose generational suffix "
            "(Jr., Sr., II, III, IV) differs from the subject's, including a missing one, is not the subject "
            "unless every occurrence in body_text carries the subject's suffix. stated_deceased requires the "
            "death phrase to attach to the named person, not the subject. relationships[].sentence_raw is "
            "trimmed to the preceded-in-death clause or the clause stating the death, and set to null when that clause "
            "holds a parenthetical name. orgs_mentioned entries that never occur on one line of body_text (glued "
            "across a line break, often a person's name plus an org) are dropped. dates_mentioned[].context "
            "is set to null unless every name-like token in it is the subject, a kept name, a known place, or a "
            "common word. possible_same_person groupings are kept only when every grouped name is still "
            "indexed. Obituaries at or before the cutoff are unchanged. Removed names remain only in "
            "body_text/body_html."
        ),
        "before": before,
        "after": after,
        "changes": dict(c),
        "notes": "Aggregate counts only; no names.",
    }
    REPORT.write_text(json.dumps(report, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
    print(json.dumps({"before": before, "after": after, "changes": dict(c)}, indent=1))


if __name__ == "__main__":
    main()
