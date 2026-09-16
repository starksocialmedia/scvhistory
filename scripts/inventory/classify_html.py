"""Classify editorial HTML page types. Reads Jordy; writes to inventory/raw."""

from __future__ import annotations

import csv
import json
import os
import re
import sys
from collections import Counter, defaultdict
from html.parser import HTMLParser
from pathlib import Path

from common import (
    SITE_ROOT,
    RAW_DIR,
    drive_ok,
    ensure_raw,
    parse_manifest_path,
    require_drive,
)

ITEM_ID_RE = re.compile(
    r"\b([A-Z]{1,6}\d{2,5}[A-Za-z]{0,2})\b"
)
TITLE_OBJECT_RE = re.compile(
    r"SCVHistory\.com\s+([A-Z]{1,6}\d{2,5}[A-Za-z]{0,2})\s*\|",
    re.I,
)
FOOTER_ID_RE = re.compile(
    r"([A-Z]{1,6}\d{2,5}[A-Za-z]{0,2})\s*:\s*.{8,}",
)
CREDIT_HINT_RE = re.compile(
    r"(collection of|courtesy of|photo by|photograph by|dpi jpeg|from photocopy)",
    re.I,
)
BYLINE_RE = re.compile(
    r"(by\s+[A-Z][a-z]+ [A-Z][a-z]+|written by|Leon Worden)",
    re.I,
)


class TitleParser(HTMLParser):
    def __init__(self) -> None:
        super().__init__(convert_charrefs=True)
        self._in_title = False
        self.title_parts: list[str] = []
        self.n_links = 0
        self.n_imgs = 0
        self.text_len = 0
        self.captions = 0
        self.footer_hits: list[str] = []
        self.img_ids: list[str] = []

    def handle_starttag(self, tag: str, attrs: list[tuple[str, str | None]]) -> None:
        ad = {k: (v or "") for k, v in attrs}
        if tag == "title":
            self._in_title = True
        elif tag == "a" and "href" in ad:
            self.n_links += 1
        elif tag == "img":
            self.n_imgs += 1
            src = ad.get("src", "")
            m = ITEM_ID_RE.search(Path(src).stem.upper().replace("T", "", 1) if False else Path(src).name)
            name = Path(src).name
            m = ITEM_ID_RE.search(name.upper())
            if m:
                self.img_ids.append(m.group(1))
        cls = ad.get("class", "")
        if "caption" in cls.lower():
            self.captions += 1

    def handle_endtag(self, tag: str) -> None:
        if tag == "title":
            self._in_title = False

    def handle_data(self, data: str) -> None:
        text = data.strip()
        if not text:
            return
        if self._in_title:
            self.title_parts.append(text)
        self.text_len += len(text)
        if FOOTER_ID_RE.match(text) or CREDIT_HINT_RE.search(text):
            if len(text) < 400:
                self.footer_hits.append(text)


def classify_path(rel: str, title: str, n_links: int, n_imgs: int, text_len: int) -> str:
    low = rel.lower()
    t = title.strip()

    if low in ("index.htm", "index.html"):
        return "home"
    if low == "obits.htm" or low.endswith("/obits.htm"):
        return "obituary_index"
    if low.startswith("warmemorial/"):
        name = Path(low).name
        if name in ("home.htm",) or "index" in name or "casualties" in name:
            return "war_memorial_index"
        return "war_memorial_profile"
    if low.startswith("mentryville/"):
        return "minisite_mentryville"
    if low.startswith("oldtownnewhall/"):
        return "minisite_oldtownnewhall"
    if low.startswith("pico/"):
        return "minisite_pico"
    if low.startswith("include/") or low.startswith("icons/"):
        return "chrome"
    if low.startswith("orig/"):
        return "orig_copy"
    if low.startswith("scvhistory/signal/"):
        return "newspaper_signal"
    if low.startswith("scvhistory/files/"):
        if t.lower().startswith("index of"):
            return "apache_listing"
        if "yearbook" in low:
            return "yearbook_package"
        return "files_other_html"
    if "yearbook" in low:
        return "yearbook_landing"

    if TITLE_OBJECT_RE.search(t):
        return "object_page"

    if re.search(r"history in pictures\s+[-–—]\s+", t, re.I):
        return "topic_or_place_index"

    if "obit" in low or re.search(r"obituar", t, re.I):
        return "obituary"

    if n_links >= 25 and text_len < 4000 and n_imgs <= 8:
        return "index_or_list"

    if n_imgs >= 1 and text_len > 800:
        return "article"

    if n_links >= 15:
        return "index_or_list"

    return "unclassified"


def html_paths_from_manifest(src: Path) -> list[str]:
    paths: list[str] = []
    with src.open("r", errors="replace") as f:
        for line in f:
            path = parse_manifest_path(line)
            if path is None:
                continue
            low = path.lower()
            if low.endswith(".htm") or low.endswith(".html"):
                paths.append(path)
    return paths


def is_editorial(path: str) -> bool:
    low = path.lower()
    if low.startswith("scvhistory/files/"):
        return False
    if low.startswith("include/") or low.startswith("icons/"):
        return False
    return True


def main() -> int:
    require_drive()
    ensure_raw()
    manifest = RAW_DIR / "scvhistory-manifest-2026-08-20.sha256"
    if not manifest.is_file():
        print(f"STOP: copied manifest missing at {manifest}", file=sys.stderr)
        return 2

    all_html = html_paths_from_manifest(manifest)
    editorial = [p for p in all_html if is_editorial(p)]
    files_html = [p for p in all_html if p.lower().startswith("scvhistory/files/")]
    print(f"html total={len(all_html)} editorial={len(editorial)} files_html={len(files_html)}")

    csv_path = RAW_DIR / "page-types.csv"
    meta_samples: dict[str, list[dict]] = defaultdict(list)
    counts: Counter[str] = Counter()
    item_ids = 0
    credit_pages = 0
    byline_pages = 0
    caption_pages = 0

    files_listing = 0
    files_yearbook = 0
    for p in files_html:
        if "yearbook" in p.lower():
            files_yearbook += 1
        else:
            files_listing += 1
    counts["apache_listing"] = files_listing
    counts["yearbook_package_html"] = files_yearbook

    processed = 0
    with csv_path.open("w", newline="") as out:
        w = csv.writer(out)
        w.writerow(
            [
                "path",
                "type",
                "title",
                "item_id",
                "category",
                "n_links",
                "n_imgs",
                "text_len",
                "has_credit",
                "has_byline",
                "captions",
            ]
        )
        for rel in editorial:
            if processed % 200 == 0 and not drive_ok():
                print(f"STOP: drive gone after {processed} pages", file=sys.stderr)
                return 3
            abs_path = SITE_ROOT / rel
            title = ""
            n_links = n_imgs = text_len = captions = 0
            footer_hits: list[str] = []
            try:
                raw = abs_path.read_bytes()
            except OSError:
                w.writerow([rel, "unreadable", "", "", "", 0, 0, 0, "", "", 0])
                counts["unreadable"] += 1
                processed += 1
                continue
            text = raw.decode("windows-1252", errors="replace")
            parser = TitleParser()
            try:
                parser.feed(text)
            except Exception:
                pass
            title = " ".join(parser.title_parts).strip()
            n_links, n_imgs, text_len = parser.n_links, parser.n_imgs, parser.text_len
            captions = parser.captions
            footer_hits = parser.footer_hits

            ptype = classify_path(rel, title, n_links, n_imgs, text_len)
            counts[ptype] += 1

            item_id = ""
            category = ""
            m = TITLE_OBJECT_RE.search(title)
            if m:
                item_id = m.group(1).upper()
                parts = [p.strip() for p in title.split("|")]
                if len(parts) >= 2:
                    category = parts[1]
                item_ids += 1
            has_credit = bool(any(CREDIT_HINT_RE.search(x) for x in footer_hits) or CREDIT_HINT_RE.search(text[-2500:]))
            has_byline = bool(BYLINE_RE.search(title) or BYLINE_RE.search(text[:4000]))
            if has_credit:
                credit_pages += 1
            if has_byline:
                byline_pages += 1
            if captions:
                caption_pages += 1

            w.writerow(
                [
                    rel,
                    ptype,
                    title[:240],
                    item_id,
                    category[:80],
                    n_links,
                    n_imgs,
                    text_len,
                    "yes" if has_credit else "",
                    "yes" if has_byline else "",
                    captions,
                ]
            )
            if len(meta_samples[ptype]) < 8:
                meta_samples[ptype].append(
                    {
                        "path": rel,
                        "title": title[:240],
                        "item_id": item_id,
                        "category": category[:80],
                        "footer": footer_hits[:3],
                    }
                )
            processed += 1
            if processed % 500 == 0:
                print(f"classified {processed}/{len(editorial)}")

    summary = {
        "editorial_classified": processed,
        "files_html_not_parsed": len(files_html),
        "counts": dict(counts.most_common()),
        "object_pages_with_item_id": item_ids,
        "pages_with_credit_hint": credit_pages,
        "pages_with_byline_hint": byline_pages,
        "pages_with_caption_class": caption_pages,
        "examples": {k: v[:3] for k, v in meta_samples.items()},
    }
    dest = RAW_DIR / "page-types-summary.json"
    dest.write_text(json.dumps(summary, indent=2) + "\n")
    print(f"wrote {csv_path} and {dest}")
    print("counts:", json.dumps(dict(counts.most_common()), indent=2))
    return 0


if __name__ == "__main__":
    sys.exit(main())
