#!/usr/bin/env python3
"""Apply the living-people storage rule to the name indexes of the legacy inventory files.

Same principle as apply_living_rule.py (GROK-CONTRACT.md, "Living people"): do not build a
structured index of living people. Fields changed: pages[].people_mentioned,
entity_index.people, pages[].needs_review possible_same_person groupings, and
pages[].dates_mentioned[].context (masked). body_text / body_html / titles / links_out /
the date values themselves are NOT changed.

A page is post-cutoff when the latest four-digit year in date_raw is after the cutoff
(current year minus 72) or date_raw has no year (conservative). Pre-cutoff pages are left
unchanged. On a post-cutoff page a name stays structured only with a retention basis:

  preceded_in_death  inside a "preceded/predeceased (in death) by" clause
  stated_deceased    the text states the person is dead. Obituary wording (shared with
                     apply_living_rule.py): "the late X", "X (deceased)", "X, who died ...
                     <year>". Widened wording (inventory pages only): "X died / passed away /
                     was killed", "death / funeral / murder / grave of X", "X's death",
                     "(d. 1920)" after the name, a life-date range after the name
                     "X (1850-1920)". A range ending after the cutoff must span >= 20 years
                     (so an office term "(1995-2003)" is not read as life dates).
  subject            X is the page's own subject (>= 2 name tokens matching the last "|"
                     segment of the title, trailing dates removed) AND the title carries a
                     life-date range or the text states the subject's death (same wording,
                     applied to X, the title subject or X's surname).
  casualty_subject   the page is a war-memorial casualty page (a "<Name> <service> | d. <date>" /
                     KIA / MIA header with a casualty record, or a parsed service_record with
                     casualty fields such as "Casualty Date" / "Age at Loss") and X is that page's
                     casualty (the title subject, incl. nickname/partial/rank forms of it).
  public_figure      X is in public_figure_verified.json: names whose legacy page link or
                     Wikidata match was checked by hand as the same historical or public
                     person (or deceased per their own linked legacy obituary). Unverified
                     has_legacy_page flags are NOT used.

public_figure_verified.json and living_rule_overrides.json hold names and live in the private
repo starksocialmedia/scvhistory-data (inventory/), located through scv_data.py
($SCV_DATA_DIR, else ../scvhistory-data, else inventory/private/). The script stops with an
error if either is missing. The overrides file lists curated per-value fixes for built fields
(orgs_mentioned, entity_index.organizations, title_topic, orphan_note, community_inferred)
that carry a removed person name where no name-free rule tells persons from non-persons.

entity_index.people entries keep only pages where the name is kept (pre-cutoff pages
always) and get retention_basis; entries left with no pages are removed. possible_same_person
groupings on post-cutoff pages are kept only when every grouped name is still on the page.
needs_review notes on kept index entries that name a removed variant ("paired_name: X",
"longer_form: X") are dropped.
dates_mentioned contexts on post-cutoff pages are set to null unless every name-like token
is accounted for (kept multi-word names, known places/communities/orgs, common words), as
in apply_living_rule.py; the date values stay.

Idempotent. No personal names in this script. The report is aggregate-only; --review-out
writes names + URLs for a human (never commit it).
"""
from __future__ import annotations

import argparse
import copy
import json
import re
import sys
from collections import Counter
from pathlib import Path

HERE = Path(__file__).resolve().parent
sys.path.insert(0, str(HERE))
from extract_relationships import cutoff_year, normalize_ws, printed_year, split_sentences  # noqa: E402
from apply_living_rule import (  # noqa: E402
    DECEASED_BEFORE, SURNAME_GROUP, YEAR_RE, _occ, _split_suffix, _tokens,
    context_ok, name_basis, org_on_one_line, subject_match,
)
from scv_data import data_dir  # noqa: E402

FILES = ["lw-features", "lw-film", "lw-remainder", "lw-disaster", "coins", "worden",
         "oldtownnewhall", "media", "reynolds", "reynolds-full", "perkins", "warmemorial",
         "loose-pages", "mentryville"]
# Both files hold names and live in the private data repo (see scv_data.py), never here.
VERIFIED_REL = Path("inventory") / "public_figure_verified.json"
OVERRIDES_REL = Path("inventory") / "living_rule_overrides.json"
APPROVED = ("preceded_in_death", "stated_deceased", "stated_deceased_wide", "subject_deceased",
            "casualty_subject", "public_figure")
VARIANTS = {
    "obituary_wording_only": ("preceded_in_death", "stated_deceased"),
    "approved_without_casualty_subject": tuple(b for b in APPROVED if b != "casualty_subject"),
    "approved": APPROVED,
}
WRITE_VARIANT = "approved"
ORDER = ["preceded_in_death", "stated_deceased", "stated_deceased_wide", "subject_deceased",
         "casualty_subject", "public_figure"]
DATA_LABEL = {"stated_deceased_wide": "stated_deceased", "subject_deceased": "subject"}
CURRENT_YEAR = cutoff_year() + 72

LIFE_AFTER = re.compile(
    r"^\s*,?\s*\(\s*(?:b\.\s*|born\s+)?(?:c\.\s*|ca\.\s*)?(1[5-9]\d\d)\s*[-–—]+\s*"
    r"(?:c\.\s*|ca\.\s*)?(1[5-9]\d\d|20\d\d)\s*\)")
D_AFTER = re.compile(r"(?i)^\s*,?\s*\(\s*(?:d\.|died)\s*(?:c\.\s*)?(1[5-9]\d\d|20\d\d)\s*\)")
DIED_AFTER = re.compile(
    r"(?i)^\s*(?:,\s*(?:age\s+)?\d{1,3}\s*,)?\s*(?:died|passed\s+away|was\s+killed|perished|"
    r"was\s+murdered|was\s+hanged|was\s+executed)\b")
POSS_DEATH_AFTER = re.compile(r"(?i)^['’]s\s+(?:death|funeral|murder|grave|burial|killing|execution|hanging)\b")
DEATH_OF_BEFORE = re.compile(
    r"(?i)\b(?:death|funeral|murder|burial|grave|killing|execution|hanging)\s+of\s+(?:the\s+)?$")
TITLE_LIFE = re.compile(r"\(\s*(?:c\.\s*)?(1[5-9]\d\d)\s*[-–—]+\s*(?:c\.\s*)?(1[5-9]\d\d|20\d\d)\s*\)")
TITLE_TAIL_DATES = re.compile(
    r"(?:[\s,]*(?:\((?:[^)]*\d{4}[^)]*)\)|\d{1,2}[-/]\d{1,2}[-/]\d{2,4}|(?:c\.\s*)?\d{4}(?:\s*[-–—]\s*\d{2,4})?s?))+[\s.]*$")
CASUALTY_HEAD = re.compile(
    r"(?i)^.{0,120}?\b(?:U\.S\.\s+(?:Army|Navy|Marine\s+Corps|Air\s+Force|Coast\s+Guard)|Army|Navy|Marines?)?"
    r"[^|]{0,40}\|\s*(?:d\.\s+\w+\.?\s+\d{1,2},\s+\d{4}|MIA\b|KIA\b)")
CASUALTY_FIELD = re.compile(r"(?i)^\s*(?:casualty (?:date|reason|type|detail)|age at \(?loss\)?|official date of death|grade at loss)\s*$")
CASUALTY_RECORD = re.compile(r"(?i)\b(?:Home of Record|Casualty Date|Age at Loss)\b")


def life_range_ok(a: int, b: int, cutoff: int) -> bool:
    return a <= b <= CURRENT_YEAR and (b <= cutoff or b - a >= 20)


def page_lists(data: dict) -> list[dict]:
    if "pages" in data:
        return data["pages"]
    return (data.get("series_pages") or []) + (data.get("related_pages") or [])


def page_subject(page: dict):
    """(subject string, (birth, death) from a life-date range in the title or None)."""
    t = normalize_ws(page.get("title") or "")
    segs = [s.strip() for s in t.split("|") if s.strip()]
    t = segs[-1] if segs else ""
    m = TITLE_LIFE.search(t)
    t = TITLE_TAIL_DATES.sub("", t).strip().rstrip(".,:;").strip()
    return t, ((int(m.group(1)), int(m.group(2))) if m else None)


def is_subject(name: str, subject: str, body: str) -> bool:
    if len(_split_suffix(_tokens(subject))[0]) < 2:
        return False
    return subject_match(name, subject, body) in ("subject", "suffix_evidence")


def extra_deceased(name: str, sent: str, cutoff: int) -> bool:
    """Widened stated-deceased wording attached to name in sent."""
    for i in _occ(name, sent):
        before = sent[max(0, i - 40):i]
        after = sent[i + len(name):i + len(name) + 80]
        m = LIFE_AFTER.match(after)
        if m and life_range_ok(int(m.group(1)), int(m.group(2)), cutoff):
            return True
        if D_AFTER.match(after) or DIED_AFTER.match(after) or POSS_DEATH_AFTER.match(after):
            return True
        if DEATH_OF_BEFORE.search(before) or DECEASED_BEFORE.search(before):
            return True
    return False


def _sents_with(s: str, sentences: list[str]) -> list[str]:
    return [x for x in sentences if s in x]


def _deceased_any(name: str, sents: list[str], cutoff: int) -> bool:
    return bool(name_basis(name, sents, (None, None), [])) or any(extra_deceased(name, s, cutoff) for s in sents)


TITLE_WORDS = {"mr", "dr", "rev", "fr", "father", "pfc", "pvt", "sgt", "ssg", "sfc", "cpl", "spc", "sp4", "lcpl",
               "lt", "2lt", "1lt", "capt", "maj", "col", "gen", "adm", "ens", "sheriff", "former", "mayor", "gov",
               "governor", "sen", "senator", "rep", "judge", "the", "actor", "actress", "director", "chief"}


def surname_sentences(name: str, sur: str, sentences: list[str]) -> list[str]:
    """Sentences with occurrences of the bare surname that can only mean this person:
    an occurrence right after a different capitalized word (another given name, "Leo X")
    is masked out; rank/title words ("PFC X", "Sheriff X") are allowed."""
    own = {w.strip(".,").lower() for w in name.split()}
    out = []
    for s in sentences:
        if sur not in s:
            continue
        parts, last = [], 0
        for m in re.finditer(r"(?<![\w])" + re.escape(sur) + r"(?![\w])", s):
            prev = re.findall(r"([A-Za-z][\w.'’\-]*)\s*[\"“]?\s*$", s[:m.start()])
            pw = prev[0].strip(".").lower() if prev else ""
            # "First Middle Surname": a middle name/initial right after this person's own first name is fine
            prev2 = re.findall(r"([A-Za-z][\w.'’\-]*)\s+[A-Za-z][\w.'’\-]*\s*[\"“]?\s*$", s[:m.start()])
            p2 = prev2[0].strip(".").lower() if prev2 else ""
            if prev and prev[0][0].isupper() and pw not in own and pw not in TITLE_WORDS and p2 not in own:
                parts.append(s[last:m.start()] + "\u2063" * len(sur))
                last = m.end()
        parts.append(s[last:])
        out.append("".join(parts))
    return out


def is_casualty_page(page: dict) -> bool:
    body = normalize_ws(page.get("body_text") or "")
    if CASUALTY_HEAD.match(body[:300]) and CASUALTY_RECORD.search(body[:3000]):
        return True
    # war memorial pages parsed into a service record with casualty fields
    sr = page.get("service_record")
    return isinstance(sr, dict) and any(CASUALTY_FIELD.match(k or "") for k in sr)


def name_bases(name: str, ctx: dict, verified: set[str], cutoff: int) -> set[str]:
    out = set()
    sents = _sents_with(name, ctx["sentences"])
    b = name_basis(name, sents, (None, None), [])
    if b:
        out.add(b)
    elif any(extra_deceased(name, s, cutoff) for s in sents):
        out.add("stated_deceased_wide")
    if is_subject(name, ctx["subject"], ctx["body"]):
        life = ctx["title_life"]
        core = [w for w in normalize_ws(name).split() if w.strip(".,").lower() not in {"jr", "sr", "ii", "iii", "iv"}]
        sur = core[-1].strip(".,") if len(core) >= 2 else ""
        # the bare surname is not used for "Mrs./Miss/Ms." names (a wife shares the surname)
        use_sur = len(sur) >= 3 and sur[0].isupper() and not re.match(r"(?i)^(?:mrs|miss|ms)\b", name)
        if (life and life_range_ok(life[0], life[1], cutoff)) or any(
                _deceased_any(pr, _sents_with(pr, ctx["sentences"]), cutoff) for pr in (name, ctx["subject"]) if pr) or (
                use_sur and _deceased_any(sur, surname_sentences(name, sur, ctx["sentences"]), cutoff)):
            out.add("subject_deceased")
        if ctx["casualty"]:
            out.add("casualty_subject")
    if name.lower() in verified:
        out.add("public_figure")
    return out


def pick(bases: set[str], allowed) -> str:
    for b in ORDER:
        if b in bases and b in allowed:
            return b
    return ""


def is_post(page: dict, cutoff: int) -> bool:
    y = printed_year(page)
    return y is None or y > cutoff


def snapshot(data: dict, cutoff: int) -> dict:
    pages = page_lists(data)
    total = post = psp = psp_post = 0
    names_all, names_post = set(), set()
    basis = Counter()
    ctx_total = ctx_null = 0
    for p in pages:
        pm = p.get("people_mentioned") or []
        po = is_post(p, cutoff)
        total += len(pm)
        names_all.update(n["name_raw"] for n in pm)
        if po:
            post += len(pm)
            names_post.update(n["name_raw"] for n in pm)
            for n in pm:
                basis[n.get("retention_basis", "(none)")] += 1
            for d in p.get("dates_mentioned") or []:
                ctx_total += 1
                ctx_null += d.get("context") is None
        k = sum(1 for r in p.get("needs_review") or [] if r.get("reason") == "possible_same_person")
        psp += k
        psp_post += k if po else 0
    return {
        "pages": len(pages), "post_cutoff_pages": sum(is_post(p, cutoff) for p in pages),
        "people_mentioned_total": total, "people_mentioned_post_cutoff": post,
        "unique_names_all": len(names_all), "unique_names_post_cutoff": len(names_post),
        "entity_index_people": len(data["entity_index"]["people"]),
        "possible_same_person": psp, "possible_same_person_post_cutoff": psp_post,
        "post_cutoff_kept_by_retention_basis": dict(basis),
        "post_cutoff_dates_mentioned": ctx_total,
        "post_cutoff_dates_context_kept": ctx_total - ctx_null,
        "post_cutoff_dates_context_null": ctx_null,
    }


def place_phrases(data: dict) -> list[str]:
    ei = data.get("entity_index") or {}
    out = []
    for k in ("places", "external_places", "community_mentions", "organizations"):
        for e in ei.get(k) or []:
            if isinstance(e, dict):
                out.append(e.get("name_raw") or e.get("longer_name") or "")
    return [x for x in out if x]


def transform(data: dict, cutoff: int, allowed, bases_cache: dict, c: Counter,
              review: list | None, mask_dates: bool, overrides: list[dict] | None = None) -> None:
    pages = page_lists(data)
    kept_pairs: set[tuple[str, str]] = set()
    pair_basis: dict[tuple[str, str], str] = {}
    post_urls = set()
    global_places = place_phrases(data) if mask_dates else []
    if overrides:  # a value an override removes must not vouch for a date context
        gone = {it.get("value") for it in overrides if it.get("action") in ("drop", "replace")}
        global_places = [x for x in global_places if x not in gone]
    org_changes: list = []
    for p in pages:
        url = p["source_url"]
        if not is_post(p, cutoff):
            continue
        post_urls.add(url)
        kept = []
        for n in p.get("people_mentioned") or []:
            name = n["name_raw"]
            b = pick(bases_cache.get((url, name), set()), allowed)
            if not b:
                c["people_removed"] += 1
                continue
            c[f"kept_{b}"] += 1
            label = DATA_LABEL.get(b, b)
            if n.get("retention_basis") != label:
                n = {**n, "retention_basis": label}
            kept.append(n)
            kept_pairs.add((url, name))
            pair_basis[(url, name)] = label
            if review is not None:
                review.append({"source_url": url, "name": name, "basis": b})
        p["people_mentioned"] = kept
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
        if overrides:
            apply_page_overrides(p, overrides, c, org_changes)
        if mask_dates:
            body = p.get("body_text") or ""
            allowed_phrases = [x for x in kept_names if len(x.split()) >= 2] + global_places
            allowed_phrases += [e.get("name_raw", "") for k in ("places_mentioned", "external_places_mentioned")
                                for e in p.get(k) or [] if isinstance(e, dict)]
            allowed_phrases += [o.get("name_raw", "") for o in p.get("orgs_mentioned") or []
                                if isinstance(o, dict) and org_on_one_line(o.get("name_raw", ""), body)]
            allowed_phrases += [x for x in p.get("communities_mentioned") or [] if isinstance(x, str)]
            for d in p.get("dates_mentioned") or []:
                ctx = d.get("context")
                if ctx is None:
                    continue
                low = ctx.lower()
                if context_ok(ctx, [a for a in allowed_phrases if a and a.lower() in low], set()):
                    c["dates_context_kept"] += 1
                else:
                    d["context"] = None
                    c["dates_context_nulled"] += 1
    counts = Counter()
    for p in pages:
        for n in p.get("people_mentioned") or []:
            counts[(p["source_url"], n["name_raw"])] += n.get("count", 0)
    out = []
    for e in data["entity_index"]["people"]:
        name = e["name_raw"]
        old_pages = e.get("pages") or []
        pg = [u for u in old_pages if u not in post_urls or (u, name) in kept_pairs]
        if not pg:
            c["entity_people_removed"] += 1
            continue
        e = dict(e)
        if len(pg) != len(old_pages):
            c["entity_people_pages_trimmed"] += 1
            e["pages"] = pg
            e["mention_count"] = sum(counts[(u, name)] for u in pg) or e.get("mention_count", 0)
        e["retention_basis"] = sorted({pair_basis[(u, name)] for u in pg if (u, name) in pair_basis}
                                      | ({"pre_cutoff"} if any(u not in post_urls for u in pg) else set()))
        out.append(e)
    # needs_review notes on kept index entries must not name a removed variant
    kept_index = {e["name_raw"] for e in out}
    for e in out:
        nr = e.get("needs_review")
        if not nr:
            continue
        nr2 = []
        for r in nr:
            det = r.get("detail", "") if isinstance(r, dict) else ""
            key, _, other = det.partition(":")
            if key in VARIANT_NOTE_KEYS and other.strip() and other.strip() not in kept_index:
                c["entity_variant_notes_removed"] += 1
                continue
            nr2.append(r)
        if len(nr2) != len(nr):
            e["needs_review"] = nr2
    data["entity_index"]["people"] = out
    if overrides:
        apply_index_overrides(data, overrides, org_changes, c)


VARIANT_NOTE_KEYS = {"paired_name", "longer_form"}


def _private_file(rel: Path) -> Path:
    path = data_dir() / rel
    if not path.is_file():
        sys.exit(f"ERROR: {path} not found. It lives in the private repo starksocialmedia/scvhistory-data "
                 f"({rel}). Clone that repo next to this one (../scvhistory-data) or set SCV_DATA_DIR.")
    return path


def load_verified() -> set[str]:
    doc = json.loads(_private_file(VERIFIED_REL).read_text(encoding="utf-8"))
    return {x["name_raw"].lower() for x in doc.get("names") or []}


def load_overrides() -> list[dict]:
    doc = json.loads(_private_file(OVERRIDES_REL).read_text(encoding="utf-8"))
    return list(doc.get("items") or [])


def apply_page_overrides(p: dict, items: list[dict], c: Counter, org_changes: list) -> None:
    """Curated built-field fixes (private overrides file) on one post-cutoff page."""
    url = p["source_url"]
    for it in items:
        if it.get("source_url") and it["source_url"] != url:
            continue
        fld, act = it["field"], it["action"]
        if fld == "orgs_mentioned":
            orgs = p.get("orgs_mentioned") or []
            out = []
            for o in orgs:
                if isinstance(o, dict) and o.get("name_raw") == it["value"]:
                    c[f"override_{fld}_{act}"] += 1
                    if act == "drop":
                        org_changes.append((url, it["value"], None, o.get("count", 1)))
                        continue
                    new = it["new"]
                    org_changes.append((url, it["value"], new, o.get("count", 1)))
                    same = next((x for x in out + orgs if isinstance(x, dict) and x.get("name_raw") == new), None)
                    if same is not None:
                        same["count"] = same.get("count", 0) + o.get("count", 1)
                        continue
                    o = {**o, "name_raw": new}
                out.append(o)
            if len(out) != len(orgs) or any(a is not b for a, b in zip(out, orgs)):
                p["orgs_mentioned"] = out
        elif fld == "community_inferred":
            ci = p.get("community_inferred") or []
            out = [e for e in ci if not (isinstance(e, dict) and e.get("longer_name") == it["value"])]
            if len(out) != len(ci):
                c[f"override_{fld}_{act}"] += len(ci) - len(out)
                p["community_inferred"] = out
        elif fld in ("title_topic", "orphan_note"):
            v = p.get(fld)
            if not isinstance(v, str):
                continue
            # a name the rule keeps on this page stays in the derived field
            if it.get("name") and any(it["name"].lower() in (n.get("name_raw") or "").lower()
                                      for n in p.get("people_mentioned") or []):
                continue
            if act == "replace" and v == it["value"]:
                p[fld] = it["new"]
                c[f"override_{fld}_{act}"] += 1
            elif act == "replace_substring" and it["find"] in v:
                p[fld] = v.replace(it["find"], it["new"])
                c[f"override_{fld}_{act}"] += 1


def apply_index_overrides(data: dict, items: list[dict], org_changes: list, c: Counter) -> None:
    ei = data.get("entity_index") or {}
    orgs = ei.get("organizations")
    if isinstance(orgs, list):
        for url, old, new, cnt in org_changes:
            e_old = next((e for e in orgs if e.get("name_raw") == old), None)
            if e_old is None:
                continue
            e_old["pages"] = [u for u in e_old.get("pages") or [] if u != url]
            e_old["mention_count"] = max(0, e_old.get("mention_count", 0) - cnt)
            if new:
                e_new = next((e for e in orgs if e.get("name_raw") == new), None)
                if e_new is None:
                    e_new = {**e_old, "name_raw": new, "name_variants": [], "mention_count": 0, "pages": [],
                             "has_legacy_page": False, "legacy_page_url": ""}
                    e_new.pop("needs_review", None)
                    orgs.append(e_new)
                if url not in (e_new.get("pages") or []):
                    e_new["pages"] = (e_new.get("pages") or []) + [url]
                e_new["mention_count"] = e_new.get("mention_count", 0) + cnt
        before = len(orgs)
        orgs[:] = [e for e in orgs if e.get("pages")]
        c["override_org_index_entries_emptied"] += before - len(orgs)
    for it in items:
        fld = it["field"]
        if not fld.startswith("entity_index.") or it["action"] != "drop":
            continue
        lst = ei.get(fld.split(".", 1)[1])
        if isinstance(lst, list):
            n = len(lst)
            lst[:] = [e for e in lst if (e.get("name_raw") or e.get("longer_name")) != it["value"]]
            c[f"override_{fld}_drop"] += n - len(lst)
    ci = ei.get("community_inferred")
    if isinstance(ci, list):
        drop = {it["value"] for it in items if it["field"] == "community_inferred"}
        n = len(ci)
        ci[:] = [e for e in ci if e.get("longer_name") not in drop]
        c["override_entity_index.community_inferred_drop"] += n - len(ci)


def dump(data: dict, raw_in: str) -> str:
    indent = 2 if raw_in.startswith("{\n") else None
    return json.dumps(data, ensure_ascii=False, indent=indent) + "\n"


def main() -> None:
    ap = argparse.ArgumentParser()
    ap.add_argument("--dir", default=str(HERE), help="directory holding the inventory files")
    ap.add_argument("--files", nargs="*", default=FILES)
    ap.add_argument("--dry-run", action="store_true", help="report only, write no inventory file")
    ap.add_argument("--report", default=None, help="default <dir>/inventory_living_rule_report.json")
    ap.add_argument("--review-out", help="names + URLs + basis of kept entries (never commit)")
    args = ap.parse_args()
    d_dir = Path(args.dir)
    report_path = Path(args.report) if args.report else d_dir / "inventory_living_rule_report.json"

    cutoff = cutoff_year()
    verified = load_verified()
    overrides_all = load_overrides()
    report = {"cutoff_year": cutoff, "written_variant": None if args.dry_run else WRITE_VARIANT,
              "public_figure_verified_names": len(verified),
              "override_items": len(overrides_all),
              "variants": {k: list(v) for k, v in VARIANTS.items()}, "files": {},
              "notes": "Aggregate counts only; no names. Undated pages count as post-cutoff. "
                       "Retention bases in the data: stated_deceased covers obituary and widened wording; "
                       "subject requires a stated death; casualty_subject is the casualty of a war memorial "
                       "casualty page. Built-field overrides come from the private data repo."}
    review = [] if args.review_out else None
    any_changed = False
    for f in args.files:
        path = d_dir / f"{f}.json"
        if not path.exists():
            continue
        raw_in = path.read_text(encoding="utf-8")
        data = json.loads(raw_in)
        before = snapshot(data, cutoff)
        bases_cache: dict = {}
        for p in page_lists(data):
            if not is_post(p, cutoff):
                continue
            body = p.get("body_text") or ""
            subj, life = page_subject(p)
            ctx = {"body": body, "sentences": split_sentences(body), "subject": subj,
                   "title_life": life, "casualty": is_casualty_page(p)}
            for n in p.get("people_mentioned") or []:
                key = (p["source_url"], n["name_raw"])
                if key not in bases_cache:
                    bases_cache[key] = name_bases(n["name_raw"], ctx, verified, cutoff)
        fr = {"before": before, "variants": {}}
        written = None
        for vname, allowed in VARIANTS.items():
            d2 = copy.deepcopy(data)
            c = Counter()
            is_w = vname == WRITE_VARIANT
            transform(d2, cutoff, allowed, bases_cache, c, review if (is_w and review is not None) else None,
                      mask_dates=is_w, overrides=[o for o in overrides_all if o.get("file") == f] if is_w else None)
            fr["variants"][vname] = {"after": snapshot(d2, cutoff), "changes": dict(c)}
            if is_w:
                written = d2
        raw_out = dump(written, raw_in)
        fr["file_changed"] = raw_out != raw_in
        any_changed |= fr["file_changed"]
        if not args.dry_run and fr["file_changed"]:
            path.write_text(raw_out, encoding="utf-8")
        report["files"][f] = fr
        a = fr["variants"][WRITE_VARIANT]["after"]
        print(f"{f}: pm {before['people_mentioned_total']}->{a['people_mentioned_total']} "
              f"(post {before['people_mentioned_post_cutoff']}->{a['people_mentioned_post_cutoff']}) "
              f"ei {before['entity_index_people']}->{a['entity_index_people']}", flush=True)
    if report_path.exists() and not any_changed and not args.dry_run:
        print("no inventory changes (already applied); report left unchanged")
    else:
        report_path.write_text(json.dumps(report, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
    if review is not None:
        Path(args.review_out).write_text(json.dumps(review, ensure_ascii=False, indent=2) + "\n")


if __name__ == "__main__":
    main()
