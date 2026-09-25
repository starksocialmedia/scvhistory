#!/usr/bin/env python3
"""Crawl SCVHistory obituaries into <SCV_DATA_DIR>/obituaries/obituaries.json.

Targets from scvhistory-map.json: kind==obituary OR filename starts with
obituary_ / obituary-. Batches of 100; output and checkpoint are written after each.

Obituary data lives in the private repo starksocialmedia/scvhistory-data, never in
this repo. The data directory is resolved by inventory/legacy/scv_data.py:
$SCV_DATA_DIR, else ../scvhistory-data next to the repo root, else the gitignored
inventory/private/. This script does not commit anything. After a crawl, run
inventory/legacy/extract_relationships.py -> apply_living_rule.py -> extract_funeral.py
(the raw crawl output is unfiltered), then commit in the data repo.
"""
from __future__ import annotations

import json
import re
import sys
import time
import urllib.request
from collections import defaultdict
from datetime import date
from pathlib import Path

import crawl_reynolds as cr
import crawl_wave6_common as w6

UA = cr.UA
SLEEP = 1.0
BATCH_SIZE = 100
CRAWLED = date.today().isoformat()
REPO = Path(__file__).resolve().parent
sys.path.insert(0, str(REPO / "inventory" / "legacy"))
from scv_data import obituaries_dir  # noqa: E402

MAP_PATH = REPO / "inventory" / "legacy" / "scvhistory-map.json"
DATA = obituaries_dir()
OUT_PATH = DATA / "obituaries.json"
CKPT_PATH = DATA / "obituaries_checkpoint.json"
LOG_PATH = DATA / "obituaries_stdout.log"


def log(msg: str) -> None:
    line = msg.rstrip() + "\n"
    print(line, end="", flush=True)
    with LOG_PATH.open("a", encoding="utf-8") as f:
        f.write(line)


def is_obituary_target(page: dict) -> bool:
    kind = (page.get("kind") or "").strip()
    path = page.get("path") or ""
    fn = path.rsplit("/", 1)[-1].lower()
    if kind == "obituary":
        return True
    if fn.startswith("obituary_") or fn.startswith("obituary-"):
        return True
    return False


def obituary_urls() -> list[dict]:
    data = json.loads(MAP_PATH.read_text(encoding="utf-8"))
    out: list[dict] = []
    seen: set[str] = set()
    kind_obit = 0
    filename_extra = 0
    for p in data.get("pages") or []:
        if not is_obituary_target(p):
            continue
        path = p.get("path") or ""
        if not path or path in seen:
            continue
        seen.add(path)
        if (p.get("kind") or "") == "obituary":
            kind_obit += 1
        else:
            filename_extra += 1
        url = p.get("url") or ("https://scvhistory.com" + path)
        out.append(
            {
                "url": url,
                "path": path,
                "title_hint": p.get("title") or "",
                "map_kind": p.get("kind") or "",
            }
        )
    out.sort(key=lambda x: x["path"])
    return out, kind_obit, filename_extra


def fetch(url: str) -> tuple[str | None, str | None]:
    req = urllib.request.Request(
        url,
        headers={"User-Agent": UA, "Accept": "text/html,application/xhtml+xml,*/*;q=0.8"},
    )
    try:
        with urllib.request.urlopen(req, timeout=60) as resp:
            final = resp.geturl()
            raw = resp.read()
            ctype = resp.headers.get("Content-Type", "") or ""
            charset = "utf-8"
            m = re.search(r"charset=([\w\-]+)", ctype, re.I)
            if m:
                charset = m.group(1).strip()
            try:
                text = raw.decode(charset, errors="replace")
            except LookupError:
                text = raw.decode("utf-8", errors="replace")
            return final, text
    except Exception as e:
        return None, f"{type(e).__name__}: {e}"


def extract_community_inferred(text: str) -> list[dict]:
    """Closed-list community words that appear only inside longer place/org names.

    Mirrors extract_communities skip rules; records short community + longer_name
    + count for human confirmation (GROK-CONTRACT community_inferred).
    """
    if not text:
        return []
    name_tail = re.compile(
        r"^\s*(?:"
        r"Cafe|Caf[eé]|Hotel|Saloon|Ranch|Company|Land|Farming|Station|Depot|"
        r"Speedway|School|High|Elementary|College|Church|Bank|Mill|Store|"
        r"Lake|Dam|Creek|River|Wash|Pass|Road|Ave|Avenue|Blvd|Boulevard|"
        r"Street|St\.?|Hwy|Highway|Freeway|Park|Plaza|Mall|Center|Centre|"
        r"Market|Hardware|Hospital|Library|Museum|Cemetery|Airport|"
        r"&|and"
        r")\b",
        re.I,
    )
    # Bare "and"/"&" after a community is prose, not an embedded place/org name.
    name_tail_embed = re.compile(
        r"^\s*(?:"
        r"Cafe|Caf[eé]|Hotel|Saloon|Ranch|Company|Land|Farming|Station|Depot|"
        r"Speedway|School|High|Elementary|College|Church|Bank|Mill|Store|"
        r"Lake|Dam|Creek|River|Wash|Pass|Road|Ave|Avenue|Blvd|Boulevard|"
        r"Street|St\.?|Hwy|Highway|Freeway|Park|Plaza|Mall|Center|Centre|"
        r"Market|Hardware|Hospital|Library|Museum|Cemetery|Airport"
        r")\b",
        re.I,
    )
    name_head = re.compile(
        r"(?:"
        r"Cafe|Caf[eé]|Hotel|Saloon|Ranch|Company|Land|Farming|Station|Depot|"
        r"Speedway|School|Hyatt|Fort|Mount|Mt\.?|Lake|Rancho|Plaza|Mall|"
        r"Museum|Library|Hospital|Airport|Ranch"
        r")\s+$",
        re.I,
    )
    cap_cont = re.compile(
        r"(?i)^(Land|Farming|Cafe|Hotel|Lake|Junction|Canyon|Station|"
        r"Speedway|School|Ranch|Creek|Pass|Park|Ave|Avenue|Road)$"
    )
    # Longer closed-list communities — do not infer shorter prefix of these
    longer_communities = sorted(cr.COMMUNITIES, key=len, reverse=True)

    counts: dict[tuple[str, str], int] = defaultdict(int)
    for name in longer_communities:
        pat = re.compile(
            r"(?<![A-Za-z0-9])" + re.escape(name) + r"(?![A-Za-z0-9])",
            re.I,
        )
        for m in pat.finditer(text):
            after = text[m.end() : m.end() + 64]
            before = text[max(0, m.start() - 48) : m.start()]
            is_embedded = False
            longer = ""

            # Head word before (e.g. Rancho Camulos, Fort Tejon)
            hm = name_head.search(before)
            if hm:
                is_embedded = True
                head = hm.group(0).strip()
                longer = f"{head} {text[m.start():m.end()]}".strip()
                # absorb one more capitalized token after if present
                m2 = re.match(r"^\s+([A-Z][A-Za-z0-9'\-]+)", after)
                if m2 and cap_cont.match(m2.group(1)):
                    longer = f"{longer} {m2.group(1)}"
            elif name_tail_embed.match(after):
                is_embedded = True
                # community + following proper-name tokens
                terminal = {
                    "cafe", "café", "hotel", "saloon", "station", "depot",
                    "speedway", "school", "high", "elementary", "college",
                    "church", "bank", "mill", "store", "lake", "dam", "creek",
                    "river", "wash", "pass", "road", "ave", "avenue", "blvd",
                    "boulevard", "street", "st", "st.", "hwy", "highway",
                    "freeway", "park", "plaza", "mall", "center", "centre",
                    "market", "hardware", "hospital", "library", "museum",
                    "cemetery", "airport", "ranch", "company", "co", "co.",
                    "inc", "inc.",
                }
                continue_ok = {"land", "farming", "and", "&", "company", "co", "co.", "inc", "inc."}
                rest = after
                parts = [text[m.start() : m.end()]]
                pos = 0
                for _ in range(8):
                    m2 = re.match(r"^(\s+)([A-Za-z0-9&'\.\-]+)", rest[pos:])
                    if not m2:
                        break
                    tok = m2.group(2)
                    tok_l = tok.lower().rstrip(".,;")
                    if tok_l in {
                        "the", "a", "an", "in", "on", "at", "to", "for", "from",
                        "with", "by", "or", "of", "was", "were", "is", "are",
                    }:
                        break
                    parts.append(tok.rstrip(".,;"))
                    pos += len(m2.group(0))
                    if tok.endswith(".") or tok.endswith(",") or tok.endswith(";"):
                        break
                    if tok_l in terminal and tok_l not in {"land", "farming"}:
                        break
                    if not (
                        tok[:1].isupper()
                        or tok_l in continue_ok
                        or name_tail.match(tok)
                        or cap_cont.match(tok)
                    ):
                        parts.pop()
                        break
                longer = " ".join(parts).strip(" .,;")
            else:
                m2 = re.match(r"^\s+([A-Z][A-Za-z0-9'\-]+)", after)
                if m2 and cap_cont.match(m2.group(1)):
                    # Check if community+continuation is itself a longer list entry
                    candidate = f"{name} {m2.group(1)}"
                    if any(
                        c.lower() == candidate.lower()
                        for c in cr.COMMUNITIES
                        if len(c) > len(name)
                    ):
                        continue  # longer community match handles this
                    is_embedded = True
                    longer = candidate

            if not is_embedded or not longer:
                continue
            # Skip if this match is wholly inside a longer closed-list community
            # that was already the intended community mention
            skip_prefix = False
            for longer_c in longer_communities:
                if longer_c.lower() == name.lower():
                    continue
                if longer_c.lower().startswith(name.lower()) and len(longer_c) > len(name):
                    # If the text at this position is the longer community, skip
                    span = text[m.start() : m.start() + len(longer_c)]
                    if span.lower() == longer_c.lower():
                        skip_prefix = True
                        break
            if skip_prefix:
                continue
            counts[(name, longer)] += 1

    out = [
        {"community": c, "longer_name": ln, "count": n}
        for (c, ln), n in counts.items()
    ]
    out.sort(key=lambda x: (-x["count"], x["community"].lower(), x["longer_name"].lower()))
    return out


def aggregate_community_inferred(pages: list[dict]) -> list[dict]:
    by_key: dict[tuple[str, str], dict] = {}
    for page in pages:
        url = page.get("source_url") or ""
        for m in page.get("community_inferred") or []:
            key = (m.get("community") or "", m.get("longer_name") or "")
            if not key[0]:
                continue
            ent = by_key.setdefault(
                key,
                {
                    "community": key[0],
                    "longer_name": key[1],
                    "mention_count": 0,
                    "pages": [],
                    "needs_review": [
                        {
                            "reason": "community_inferred_from_name",
                            "detail": (
                                "Community word appeared as part of a longer "
                                "place/org name; confirm before promoting."
                            ),
                        }
                    ],
                },
            )
            ent["mention_count"] += m.get("count") or 0
            if url and url not in ent["pages"]:
                ent["pages"].append(url)
    return sorted(
        by_key.values(),
        key=lambda e: (-e["mention_count"], e["community"].lower(), e["longer_name"].lower()),
    )


def load_state() -> dict:
    if CKPT_PATH.exists():
        return json.loads(CKPT_PATH.read_text(encoding="utf-8"))
    if OUT_PATH.exists():
        doc = json.loads(OUT_PATH.read_text(encoding="utf-8"))
        done = {p.get("source_url") for p in doc.get("pages") or []}
        return {
            "done_urls": sorted(done),
            "failures": doc.get("meta", {}).get("failures") or [],
            "batch_num": 0,
        }
    return {"done_urls": [], "failures": [], "batch_num": 0}


def save_ckpt(state: dict) -> None:
    CKPT_PATH.write_text(json.dumps(state, ensure_ascii=False, indent=2) + "\n")


def write_output(
    pages: list[dict],
    failures: list,
    notes: str,
    target_n: int,
    done_n: int,
    kind_obit: int,
    filename_extra: int,
    batch_num: int,
) -> None:
    ei = w6.build_entity_index_with_unclassified(pages)
    ei["community_inferred"] = aggregate_community_inferred(pages)
    doc = {
        "meta": {
            "section": "obituaries",
            "source": "scvhistory-map.json",
            "crawled": CRAWLED,
            "index_url": "https://scvhistory.com/scvhistory/obits.htm",
            "page_count": len(pages),
            "target_count": target_n,
            "complete": done_n >= target_n and len(pages) >= target_n - len(failures),
            "batch_num": batch_num,
            "batch_size": BATCH_SIZE,
            "map_kind_obituary_count": kind_obit,
            "filename_prefix_extra_count": filename_extra,
            "deduped_target_count": target_n,
            "crawler_notes": notes,
            "person_rule": getattr(w6, "PERSON_RULE", "") or getattr(cr, "PERSON_RULE", ""),
            "place_rule": getattr(cr, "PLACE_RULE", ""),
            "related_reading_merge": True,
            "failures": failures,
        },
        "pages": pages,
        "entity_index": ei,
    }
    OUT_PATH.write_text(json.dumps(doc, ensure_ascii=False) + "\n")


def process_obituary(url: str, series_position: int, title_hint: str, html: str, final: str) -> dict:
    page = w6.process_content_page(url, series_position, title_hint, html, final)
    # Explicit related-reading merge on full HTML (contract sidebar rule)
    related = cr.extract_related_reading_links(html, final)
    page["links_out"] = cr.merge_links_out(page.get("links_out") or [], related, final)
    # Reaffirm communities after body cleanup; add community_inferred
    body = page.get("body_text") or ""
    page["communities_mentioned"] = cr.extract_communities(body)
    page["community_inferred"] = extract_community_inferred(body)
    # Ensure related_block_header_raw present
    if "related_block_header_raw" not in page:
        page["related_block_header_raw"] = ""
    return page


def main() -> None:
    DATA.mkdir(parents=True, exist_ok=True)
    targets, kind_obit, filename_extra = obituary_urls()
    target_n = len(targets)
    state = load_state()
    done = set(state.get("done_urls") or [])
    failures = list(state.get("failures") or [])
    batch_num = int(state.get("batch_num") or 0)

    pages: list[dict] = []
    by_url: dict[str, dict] = {}
    if OUT_PATH.exists():
        doc = json.loads(OUT_PATH.read_text(encoding="utf-8"))
        pages = list(doc.get("pages") or [])
        by_url = {p["source_url"]: p for p in pages if p.get("source_url")}

    remaining = [t for t in targets if t["url"] not in done]
    log(
        f"START targets={target_n} (kind_obituary={kind_obit} "
        f"filename_prefix_extra={filename_extra}; user expected ~607; "
        f"map kind=obituary ~595) done={len(done)} remaining={len(remaining)} "
        f"batch_size={BATCH_SIZE}"
    )

    i = 0
    while i < len(remaining):
        batch_num += 1
        batch = remaining[i : i + BATCH_SIZE]
        batch_pages: list[dict] = []
        log(
            f"BATCH {batch_num} begin size={len(batch)} "
            f"overall={len(done)+1}-{len(done)+len(batch)}/{target_n}"
        )

        for spec in batch:
            url = spec["url"]
            final, html_or_err = fetch(url)
            time.sleep(SLEEP)
            if final is None:
                failures.append({"url": url, "error": html_or_err})
                log(f"  FAIL {url} {html_or_err}")
                done.add(url)
                continue
            try:
                page = process_obituary(
                    url,
                    len(pages) + len(batch_pages) + 1,
                    spec.get("title_hint") or "",
                    html_or_err,
                    final,
                )
                batch_pages.append(page)
                by_url[page["source_url"]] = page
                # Also mark original map URL done (may differ if redirect)
                done.add(url)
                done.add(page["source_url"])
                log(
                    f"  OK {page.get('legacy_key')} "
                    f"imgs={len(page.get('images') or [])} "
                    f"words={len((page.get('body_text') or '').split())} "
                    f"people={len(page.get('people_mentioned') or [])} "
                    f"inferred={len(page.get('community_inferred') or [])}"
                )
            except Exception as e:
                failures.append({"url": url, "error": f"{type(e).__name__}: {e}"})
                done.add(url)
                log(f"  FAIL process {url} {type(e).__name__}: {e}")

        pages = [by_url[t["url"]] for t in targets if t["url"] in by_url]
        # Also include redirected pages keyed by final URL if map URL missing
        for t in targets:
            if t["url"] not in by_url:
                # try matching by legacy_path
                for u, p in by_url.items():
                    if p.get("legacy_path") == t["path"]:
                        by_url[t["url"]] = p
                        break
        pages = [by_url[t["url"]] for t in targets if t["url"] in by_url]

        notes = (
            f"Obituaries from scvhistory-map.json. Deduped target count={target_n} "
            f"(kind=obituary={kind_obit}, filename-prefix extras={filename_extra}; "
            f"user expected ~607). Obituaries name family/places/employers/dates so "
            f"entity_index is intentionally dense. Related-reading sidebar merge applied "
            f"via extract_related_reading_links + merge_links_out on full HTML. "
            f"community_inferred records closed-list community words inside longer "
            f"place/org names for human confirm. "
            f"Progress {len(pages)}/{target_n}; failures {len(failures)}; "
            f"batch_num={batch_num}."
        )
        write_output(
            pages, failures, notes, target_n, len(done), kind_obit, filename_extra, batch_num
        )
        save_ckpt(
            {
                "done_urls": sorted(done),
                "failures": failures,
                "batch_num": batch_num,
                "page_count": len(pages),
                "target_count": target_n,
            }
        )
        log(
            f"BATCH {batch_num} SAVED {OUT_PATH} pages_in_batch={len(batch_pages)} "
            f"total_pages={len(pages)}/{target_n} failures={len(failures)}"
        )
        i += BATCH_SIZE

    log(f"DONE page_count={len(pages)}/{target_n} failures={len(failures)}")


if __name__ == "__main__":
    main()
