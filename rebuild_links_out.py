#!/usr/bin/env python3
"""Rebuild links_out by merging related-reading sidebars from Reggie HTML."""
from __future__ import annotations

import json
import re
import sys
import urllib.parse
import urllib.request
from html.parser import HTMLParser
from pathlib import Path

REGGIE = Path("/Volumes/Reggie/SCVHistory/scvhistory.com")
LEGACY = Path(sys.argv[1] if len(sys.argv) > 1 else "/workspace/scvhistory/inventory/legacy")
UA = "SCVHistory-Legacy-Extract/1.0 (+rebuild_links_out)"

CHROME_ANCHORS = {
    "NEXT", "PREVIOUS", "CONTENTS", "SEARCH", "RETURN TO TOP", "RETURN TO MAIN INDEX",
    "PHOTO CREDITS", "BIBLIOGRAPHY", "BOOKS FOR SALE", "HOME", "TOP", "INDEX",
    "COMMENTS POWERED BY DISQUS.",
}
CHROME_HREF = re.compile(
    r"(?i)(key\.htm|bibliography\.htm|publications\.htm|googlesearch|disqus\.com)"
)

FILES = [
    "reynolds.json", "reynolds-full.json", "perkins.json", "worden.json",
    "oldtownnewhall.json", "warmemorial.json", "mentryville.json", "media.json",
    "coins.json", "loose-pages.json", "lw-features.json", "lw-film.json",
    "lw-disaster.json", "lw-remainder.json",
]


def is_internal(href: str) -> bool:
    if href.startswith("#") or href.startswith("mailto:"):
        return True
    if href.startswith("/") or not re.match(r"^[a-zA-Z][a-zA-Z0-9+.-]*:", href):
        return True
    host = urllib.parse.urlparse(urllib.parse.urljoin("https://scvhistory.com/", href)).netloc
    return "scvhistory.com" in host or host == ""


def norm_path(href: str, base: str) -> str:
    absu = urllib.parse.urljoin(base or "https://scvhistory.com/", href or "")
    path = urllib.parse.unquote(urllib.parse.urlparse(absu).path or "").rstrip("/")
    path = re.sub(r"/index\.html?$", "", path, flags=re.I)
    return path.lower()


def url_to_reggie(url: str) -> Path | None:
    path = urllib.parse.unquote(urllib.parse.urlparse(url).path or "").lstrip("/")
    cand = REGGIE / path
    if cand.is_file():
        return cand
    for alt in (cand.with_suffix(".html"), cand.with_suffix(".htm")):
        if alt.is_file():
            return alt
    if cand.is_dir():
        for name in ("index.html", "index.htm"):
            if (cand / name).is_file():
                return cand / name
    return None


class RelatedParser(HTMLParser):
    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.in_td = 0
        self.depth_flags: list[bool] = []
        self.td_links: list[dict] = []
        self.in_a = False
        self.a_href = None
        self.a_text: list[str] = []
        self.related: list[dict] = []
        self.header = None
        self.in_inverse = False
        self.inv_text: list[str] = []

    def handle_starttag(self, tag, attrs):
        ad = dict(attrs)
        if tag == "td":
            self.in_td += 1
            self.depth_flags.append(False)
            self.td_links = []
        cls = ad.get("class", "")
        if isinstance(cls, list):
            cls = " ".join(cls)
        if tag in ("span", "font", "div", "p", "a") and re.search(
            r"inversecaption|thumbcaption", cls or "", re.I
        ):
            if self.in_td and self.depth_flags:
                self.depth_flags[-1] = True
            if "inversecaption" in (cls or "").lower():
                self.in_inverse = True
                self.inv_text = []
        if tag == "a" and "href" in ad:
            self.in_a = True
            self.a_href = ad.get("href")
            self.a_text = []

    def handle_endtag(self, tag):
        if tag == "a" and self.in_a:
            href = self.a_href or ""
            text = " ".join("".join(self.a_text).split())
            self.in_a = False
            if self.in_td and self.depth_flags and self.depth_flags[-1]:
                if href and not href.startswith("javascript:") and not href.startswith("mailto:"):
                    if text.upper() not in CHROME_ANCHORS and not CHROME_HREF.search(href):
                        if not (href.startswith("#") and not text):
                            self.td_links.append(
                                {
                                    "href_raw": href,
                                    "anchor_text": text,
                                    "is_internal": is_internal(href),
                                    "role": "related_reading",
                                }
                            )
            self.a_href = None
            self.a_text = []
        if self.in_inverse and tag in ("span", "font", "div", "p"):
            ht = " ".join("".join(self.inv_text).split())
            if ht and not self.header:
                self.header = ht
            self.in_inverse = False
            self.inv_text = []
        if tag == "td" and self.in_td:
            flagged = self.depth_flags.pop() if self.depth_flags else False
            if flagged:
                self.related.extend(self.td_links)
            self.in_td -= 1
            self.td_links = []

    def handle_data(self, data):
        if self.in_a:
            self.a_text.append(data)
        if self.in_inverse:
            self.inv_text.append(data)


def extract_related(html: str, base_url: str) -> tuple[list[dict], str | None]:
    if not html or not re.search(r"inversecaption|thumbcaption", html, re.I):
        return [], None
    p = RelatedParser()
    try:
        p.feed(html)
    except Exception:
        return [], None
    seen: set[tuple[str, str]] = set()
    out: list[dict] = []
    for L in p.related:
        key = (norm_path(L["href_raw"], base_url), L["anchor_text"])
        if key in seen:
            continue
        seen.add(key)
        out.append(L)
    return out, p.header


def merge_links(existing: list[dict], related: list[dict], base: str) -> tuple[list[dict], int]:
    out = [dict(L) for L in (existing or [])]
    have = {norm_path(L.get("href_raw") or "", base) for L in out}
    added = 0
    for L in related or []:
        n = norm_path(L.get("href_raw") or "", base)
        if not n:
            continue
        if n in have:
            for e in out:
                if norm_path(e.get("href_raw") or "", base) == n and "role" not in e:
                    e["role"] = "related_reading"
                    break
            continue
        have.add(n)
        out.append(dict(L))
        added += 1
    return out, added


def page_lists(data: dict) -> list[tuple[str, list]]:
    out = []
    for key in ("pages", "series_pages", "related_pages"):
        if isinstance(data.get(key), list) and data[key]:
            out.append((key, data[key]))
    return out


def main() -> None:
    report = {
        "pages_with_related_block": 0,
        "pages_gained_links": 0,
        "links_added": 0,
        "no_html": 0,
        "files": [],
    }
    for fname in FILES:
        path = LEGACY / fname
        if not path.exists():
            continue
        data = json.loads(path.read_text(encoding="utf-8"))
        file_block = file_gained = file_added = file_nohtml = 0
        for _key, group in page_lists(data):
            for page in group:
                url = page.get("source_url") or ""
                rp = url_to_reggie(url) if REGGIE.exists() else None
                html = None
                if rp:
                    try:
                        html = rp.read_text(encoding="utf-8", errors="replace")
                    except Exception:
                        html = None
                if html is None and url:
                    try:
                        req = urllib.request.Request(url, headers={"User-Agent": UA})
                        with urllib.request.urlopen(req, timeout=40) as r:
                            html = r.read().decode("utf-8", "replace")
                    except Exception:
                        html = None
                if not html:
                    file_nohtml += 1
                    continue
                related, header = extract_related(html, url)
                if not related:
                    continue
                file_block += 1
                new_links, added = merge_links(page.get("links_out") or [], related, url)
                page["links_out"] = new_links
                if header:
                    page["related_block_header_raw"] = header
                if added:
                    file_gained += 1
                    file_added += added
        path.write_text(json.dumps(data, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
        report["files"].append(
            {
                "file": fname,
                "pages_with_related_block": file_block,
                "pages_gained_links": file_gained,
                "links_added": file_added,
                "no_html": file_nohtml,
            }
        )
        report["pages_with_related_block"] += file_block
        report["pages_gained_links"] += file_gained
        report["links_added"] += file_added
        report["no_html"] += file_nohtml
        print(
            f"{fname}: block={file_block} gained_pages={file_gained} "
            f"links_added={file_added} no_html={file_nohtml}",
            flush=True,
        )

    (LEGACY / "links_out_rebuild_report.json").write_text(
        json.dumps(report, indent=2) + "\n", encoding="utf-8"
    )
    print(
        "TOTALS",
        {
            k: report[k]
            for k in (
                "pages_with_related_block",
                "pages_gained_links",
                "links_added",
                "no_html",
            )
        },
    )


if __name__ == "__main__":
    main()
