#!/usr/bin/env python3
"""Extract footnotes into legacy inventory page objects."""
from __future__ import annotations

import html as html_lib
import json
import re
from collections import Counter
from datetime import date
from pathlib import Path
from urllib.parse import urljoin, urlparse

INV = Path(__file__).resolve().parent
HTML_CACHE = Path("/tmp/scv_html")

SHARED_NOTES_URLS = {
    "perkins": "https://scvhistory.com/scvhistory/signal/perkins/notes.html",
    "reynolds": "https://scvhistory.com/scvhistory/signal/reynolds/notes.html",
}

TARGET_FILES = [
    "perkins.json",
    "reynolds.json",
    "reynolds-full.json",
    "lw-features.json",
    "lw-film.json",
    "lw-disaster.json",
    "lw-remainder.json",
    "media.json",
    # obituaries.json is not here: it lives in the private repo starksocialmedia/scvhistory-data
    # and must not feed this report (which is committed to this repo). Missing files are skipped.
    "warmemorial.json",
    "loose-pages.json",
    "mentryville.json",
    "oldtownnewhall.json",
    "coins.json",
    "lw-map.json",
    "scvhistory-map.json",
    "scvhistory-map_files_documents.json",
]


def strip_tags(s: str) -> str:
    s = re.sub(r"(?is)<br\s*/?>", "\n", s)
    s = re.sub(r"(?is)</p>", "\n", s)
    # Complete tags and truncated tags cut at section boundaries
    s = re.sub(r"(?is)<[^>]*>?", "", s)
    s = html_lib.unescape(s)
    s = s.replace("\xa0", " ")
    s = re.sub(r"[ \t]+\n", "\n", s)
    s = re.sub(r"\n{3,}", "\n\n", s)
    s = re.sub(r"[ \t]{2,}", " ", s)
    s = re.sub(r"\s+$", "", s)
    return s.strip()


def parse_shared_notes(html: str) -> dict[str, str]:
    notes: dict[str, str] = {}
    parts = re.split(r'(?i)<a\s+name=["\'](\d+)["\']\s*>', html)
    for i in range(1, len(parts), 2):
        num = parts[i]
        content = parts[i + 1] if i + 1 < len(parts) else ""
        text = re.sub(r"(?is)\[\s*<a[^>]*>\s*BACK\s*</a>\s*\]", "", content)
        text = re.sub(r"(?is)<a[^>]*>\s*BACK\s*</a>", "", text)
        text = strip_tags(text)
        text = re.sub(rf"^\s*{re.escape(num)}\.\s*", "", text)
        text = re.sub(r"\s+", " ", text).strip()
        text = re.split(r"(?i)\s*©\d{4}", text)[0].strip()
        if text:
            notes[num] = text
    return notes


def find_notes_section_html(body_html: str) -> str | None:
    if not body_html:
        return None
    patterns = [
        r'(?is)<a[^>]*\sname=["\']notes["\'][^>]*>',
        r'(?is)<[^>]+\sid=["\']notes["\'][^>]*>',
        r'(?is)<font[^>]*class=["\']subhed2["\'][^>]*>\s*NOTES\s*</font>',
        r'(?is)<font[^>]*class=["\']subhed2["\'][^>]*>[^<]*Notes\.?\s*</font>',
        r'(?is)<font[^>]*>[^<]*Notes\.?\s*</font>',
        r'(?is)<font[^>]*>\s*NOTES\s*</font>',
        r'(?is)<b>\s*Notes\.?\s*</b>',
        r'(?is)<font[^>]*>\s*Notes\.?\s*</font>',
        r'(?is)<p[^>]*>\s*Notes\.?\s*</p>',
        r"(?is)>\s*Webmaster's Notes\.?\s*<",
        r'(?is)>\s*Notes\.?\s*<',
        r'(?is)>\s*NOTES\s*<',
    ]
    # Prefer the earliest heading in document order (Notes. before Webmaster's Notes.)
    candidates = []
    for pat in patterns:
        for m in re.finditer(pat, body_html):
            ctx = body_html[max(0, m.start() - 5) : m.end() + 5]
            group_l = m.group().lower()
            if (
                re.search(r"(?i)notes that|notes from", ctx)
                and "Notes." not in ctx
                and "NOTES" not in ctx
                and 'name="' not in group_l
                and "id=" not in group_l
            ):
                continue
            candidates.append(m.start())
    if not candidates:
        return None
    start = min(candidates)
    rest = body_html[start:]
    end_m = re.search(
        r"(?is)(?:"
        r'<div class="smalltext"|'
        r'<hr[^>]*>\s*<font class="smalltext"|'
        r"</td>\s*<td[^>]*(?:width=[\"']120[\"']|style=[\"'][^\"']*width:\s*120)|"
        r'<span class="inversecaption"|'
        r'<div class="inversecaption"|'
        r"PHOTO CREDITS|BIBLIOGRAPHY|"
        r"©\d{4},\s*SANTA CLARITA|"
        r"</blockquote>\s*</div>\s*<p></p><hr"
        r")",
        rest[80:] if len(rest) > 80 else "",
    )
    if end_m:
        return rest[: 80 + end_m.start()]
    return rest


def parse_same_page_notes(section_html: str) -> dict[str, str]:
    notes: dict[str, str] = {}
    if not section_html:
        return notes

    raw = section_html

    for m in re.finditer(
        r"(?is)\[\s*([0-9]+(?:\s*,\s*[0-9]+)*)\s*\]\s*(.*?)(?=(?:\[\s*[0-9]+|\Z))",
        raw,
    ):
        nums = [n.strip() for n in m.group(1).split(",")]
        text = re.sub(r"\s+", " ", strip_tags(m.group(2))).strip()
        if not text or len(text) < 2 or text.lower().startswith("notes"):
            continue
        for n in nums:
            notes[n] = text

    work = re.sub(
        r"(?is)^.*?(?:NOTES|Notes\.?|Webmaster's Notes\.?)\s*",
        "",
        raw,
        count=1,
    )

    starts = list(
        re.finditer(r"(?is)(?:^|>|\n)\s*(?:<b>)?(\d{1,3})\.(?:</b>)?\s+", work)
    )
    for i, m in enumerate(starts):
        num = m.group(1)
        end = starts[i + 1].start() if i + 1 < len(starts) else len(work)
        chunk = work[m.end() : end]
        text = re.sub(r"\s+", " ", strip_tags(chunk)).strip()
        text = re.split(r"(?i)Webmaster'?s Notes", text)[0].strip()
        if text and (num not in notes or len(text) > len(notes[num])):
            notes[num] = text

    # Letter notes a. / [a] — prefer Webmaster's Notes subsection when present
    letter_src = work
    wm = re.search(r"(?is)Webmaster'?s Notes\.?", work)
    if wm:
        letter_src = work[wm.end() :]
    for m in re.finditer(
        r"(?is)(?:^|>|\n)\s*(?:\[)?([a-z])(?:\])?\.\s+(.*?)(?="
        r"(?:(?:^|>|\n)\s*(?:\[)?(?:[a-z]|\d+)(?:\])?\.\s+)|\Z)",
        letter_src,
    ):
        key = m.group(1).lower()
        text = re.sub(r"\s+", " ", strip_tags(m.group(2))).strip()
        if text and len(text) >= 8 and key not in notes:
            notes[key] = text

    for m in re.finditer(
        r"(?is)\[\s*([0-9]+|[A-Za-z])\s*\]\s+([^\[\n<]{3,}.*?)(?="
        r"(?:\[\s*(?:[0-9]+|[A-Za-z])\s*\])|\Z)",
        work,
    ):
        key = m.group(1)
        if key.isalpha():
            key = key.lower()
        text = re.sub(r"\s+", " ", strip_tags(m.group(2))).strip()
        if text and key not in notes:
            notes[key] = text

    return notes


def extract_markers(body_html: str) -> list[dict]:
    if not body_html:
        return []
    found: list[dict] = []
    seen: set[str] = set()

    def add(number: str, href: str | None, kind: str):
        number = number.strip()
        if not number or number in {"*", "†", "‡"}:
            return
        parts = [p.strip() for p in re.split(r"\s*,\s*", number) if p.strip()]
        for part in parts:
            if not re.fullmatch(r"[0-9]+|[A-Za-z]", part):
                continue
            key = part.lower() if part.isalpha() else part
            if key in seen:
                continue
            seen.add(key)
            found.append(
                {
                    "number": key if part.isalpha() else part,
                    "href": href,
                    "kind": kind,
                }
            )

    for m in re.finditer(
        r'(?is)<sup[^>]*>\s*\[?\s*<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>\s*([^<]+?)\s*</a>\s*\]?\s*</sup>',
        body_html,
    ):
        href, label = m.group(1).strip(), m.group(2).strip()
        if re.search(r"notes\.html", href, re.I):
            add(label, href, "shared")
        elif re.search(r"#notes\b", href, re.I):
            add(label, href, "hash_notes")
        else:
            add(label, href, "plain")

    for m in re.finditer(r"(?is)<sup[^>]*>\s*\[([^<\]]+)\]\s*</sup>", body_html):
        label = m.group(1).strip()
        if re.search(r"<a\s", label, re.I):
            continue
        add(label, None, "plain")

    return found


def absolute_notes_url(page_url: str, href: str) -> str:
    abs_url = urljoin(page_url, href)
    parsed = urlparse(abs_url)
    return f"https://scvhistory.com{parsed.path}"


def load_shared_maps() -> dict[str, dict[str, str]]:
    maps = {}
    for key, url in SHARED_NOTES_URLS.items():
        fname = "perkins_notes.html" if key == "perkins" else "reynolds_notes.html"
        path = HTML_CACHE / fname
        maps[url] = parse_shared_notes(path.read_text(errors="replace"))
        print(f"Parsed {url}: {len(maps[url])} notes")
    return maps


def classify_and_build(page: dict, shared_maps: dict[str, dict[str, str]]):
    bh = page.get("body_html") or ""
    url = page.get("source_url") or ""
    markers = extract_markers(bh)
    section = find_notes_section_html(bh)
    same_notes = parse_same_page_notes(section) if section else {}

    if not markers and not same_notes:
        return [], None

    shared_page_url = None
    for m in markers:
        if m["kind"] == "shared" and m["href"]:
            shared_page_url = absolute_notes_url(url, m["href"].split("#")[0])
            break

    shared_lookup = shared_maps.get(shared_page_url or "", {})
    footnotes: list[dict] = []
    seen: set[str] = set()

    # Pattern B first when chapter links to notes.html
    for m in markers:
        if m["kind"] != "shared" or not shared_page_url:
            continue
        num = m["number"]
        if num in seen:
            continue
        seen.add(num)
        text = shared_lookup.get(num, "")
        entry = {"number": num, "text": "", "resolution": "orphan"}
        if text:
            entry["text"] = text
            entry["resolution"] = "shared_notes_page"
            entry["notes_page_url"] = shared_page_url
        footnotes.append(entry)

    # Pattern A: all recoverable on-page notes (when not a shared-notes chapter)
    if same_notes and not shared_page_url:

        def sort_key(k: str):
            return (0, int(k)) if k.isdigit() else (1, k)

        for num in sorted(same_notes.keys(), key=sort_key):
            if num in seen:
                continue
            seen.add(num)
            footnotes.append(
                {"number": num, "text": same_notes[num], "resolution": "same_page"}
            )

    # Pattern C: markers without recoverable text
    for m in markers:
        if m["kind"] == "shared":
            continue
        num = m["number"]
        if num in seen:
            continue
        if (not shared_page_url) and num in same_notes and same_notes[num]:
            seen.add(num)
            footnotes.append(
                {"number": num, "text": same_notes[num], "resolution": "same_page"}
            )
            continue
        seen.add(num)
        footnotes.append({"number": num, "text": "", "resolution": "orphan"})

    return footnotes, shared_page_url


def iter_page_buckets(data: dict):
    for key in ("pages", "series_pages", "related_pages"):
        if key in data and isinstance(data[key], list):
            yield key, data[key]


def process_file(path: Path, shared_maps: dict) -> dict:
    data = json.loads(path.read_text())
    updated_pages = 0
    stats = Counter()
    page_reports = []

    for bucket, pages in iter_page_buckets(data):
        for page in pages:
            if not isinstance(page, dict):
                continue
            footnotes, notes_url = classify_and_build(page, shared_maps)
            page.pop("footnotes", None)
            page.pop("footnotes_notes_page_url", None)
            if not footnotes:
                continue
            page["footnotes"] = footnotes
            page["footnotes_notes_page_url"] = notes_url or ""
            updated_pages += 1
            for fn in footnotes:
                stats[fn["resolution"]] += 1
            page_reports.append(
                {
                    "file": path.name,
                    "bucket": bucket,
                    "legacy_key": page.get("legacy_key"),
                    "source_url": page.get("source_url"),
                    "count": len(footnotes),
                    "resolutions": dict(Counter(f["resolution"] for f in footnotes)),
                    "footnotes_notes_page_url": page.get("footnotes_notes_page_url", ""),
                }
            )

    if updated_pages:
        path.write_text(json.dumps(data, ensure_ascii=False, indent=2) + "\n")
    return {
        "file": path.name,
        "pages_with_footnotes": updated_pages,
        "by_resolution": dict(stats),
        "total_entries": sum(stats.values()),
        "pages": page_reports,
    }


def main():
    shared_maps = load_shared_maps()
    all_file_stats = []
    all_pages = []

    for fname in TARGET_FILES:
        path = INV / fname
        if not path.exists():
            continue
        result = process_file(path, shared_maps)
        if result["pages_with_footnotes"]:
            print(
                f"{fname}: pages={result['pages_with_footnotes']} "
                f"entries={result['total_entries']} {result['by_resolution']}"
            )
            all_file_stats.append(result)
            all_pages.extend(result["pages"])

    rsf = next((p for p in all_pages if p.get("legacy_key") == "perkins-rsf-1957"), None)
    totals = Counter()
    for r in all_file_stats:
        for k, v in r["by_resolution"].items():
            totals[k] += v

    samples = []
    for key in ("perkins-rsf-1957", "part05", "lw2024"):
        hit = next((p for p in all_pages if p.get("legacy_key") == key), None)
        if not hit:
            continue
        data = json.loads((INV / hit["file"]).read_text())
        page = None
        for _, pages in iter_page_buckets(data):
            for p in pages:
                if p.get("legacy_key") == key:
                    page = p
                    break
            if page:
                break
        samples.append(
            {
                "legacy_key": key,
                "source_url": hit["source_url"],
                "footnotes_count": hit["count"],
                "resolutions": hit["resolutions"],
                "footnotes_notes_page_url": hit["footnotes_notes_page_url"],
                "footnotes_sample": (page or {}).get("footnotes", [])[:3],
            }
        )

    notes_urls_used = sorted(
        {p["footnotes_notes_page_url"] for p in all_pages if p.get("footnotes_notes_page_url")}
    )

    report = {
        "crawled": date.today().isoformat(),
        "source": "live_site_fallback",
        "reggie_preferred": True,
        "reggie_available": False,
        "pages_with_footnotes_nonempty": len(all_pages),
        "counts_by_resolution": dict(totals),
        "total_footnote_entries": sum(totals.values()),
        "perkins_rsf_note_count": rsf["count"] if rsf else 0,
        "notes_html_urls_used": notes_urls_used,
        "files_updated": [r["file"] for r in all_file_stats],
        "per_file": [
            {
                "file": r["file"],
                "pages_with_footnotes": r["pages_with_footnotes"],
                "total_entries": r["total_entries"],
                "by_resolution": r["by_resolution"],
            }
            for r in all_file_stats
        ],
        "sample_pages": samples,
        "pages": all_pages,
    }
    out = INV / "footnotes_extraction_report.json"
    out.write_text(json.dumps(report, ensure_ascii=False, indent=2) + "\n")
    print("Wrote", out)
    print(
        "TOTALS",
        dict(totals),
        "pages",
        len(all_pages),
        "RSF",
        report["perkins_rsf_note_count"],
    )


if __name__ == "__main__":
    main()
