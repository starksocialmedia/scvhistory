#!/usr/bin/env python3
"""Wave 2: SCV War Memorial casualty crawl → inventory/legacy/warmemorial.json"""
from __future__ import annotations

import json
import re
import time
import urllib.parse
from collections import defaultdict
from html import unescape
from pathlib import Path

from bs4 import BeautifulSoup, Comment, Tag

import crawl_reynolds as cr

UA = cr.UA
CRAWLED = "2026-09-17"
INDEX_URL = "https://scvhistory.com/warmemorial/home.htm"
OUT_PATH = Path("/workspace/scvhistory/inventory/legacy/warmemorial.json")
CACHE_DIR = Path("/tmp/warmemorial_pages")
SLEEP = 1.0

CRAFT_36 = [
    "korea_albertthomas", "korea_donaldmorissett", "korea_gilbertmontenegro",
    "korea_henryacuna", "korea_raymondkelly", "korea_robertwhisler",
    "terror_brianprosser", "terror_colelarsen", "terror_deantodd",
    "terror_dennissellen", "terror_iangelig", "terror_jakesuter",
    "terror_johnconant", "terror_josefloresmejia", "terror_richardslocum",
    "terror_robertwilson", "terror_rudyacosta", "terror_stephencolley",
    "ww2_albertmoore", "ww2_archibaldbeall", "ww2_augustrubel",
    "ww2_edwardcontreras", "ww2_ekenaston", "ww2_eugenedarr",
    "ww2_frankwhitmore", "ww2_garrywingfield", "ww2_jackharland",
    "ww2_jamesredman", "ww2_jimbartlett", "ww2_johnnycordova",
    "ww2_johnward", "ww2_leoncherry", "ww2_ozalsmart", "ww2_robertcone",
    "ww2_robertfose", "ww2_tomross",
]

SEED_INDEXES = [
    INDEX_URL,
    "https://scvhistory.com/scvhistory/ww1casualties-index.htm",
    "https://scvhistory.com/warmemorial/ww2casualties-index.htm",
    "https://scvhistory.com/warmemorial/koreacasualties-index.htm",
    "https://scvhistory.com/scvhistory/vietnamcasualties-index.htm",
    "https://scvhistory.com/warmemorial/terrorcasualties-index.htm",
]

CHROME_ANCHORS = set(cr.CHROME_ANCHORS) | {
    "WAR MEMORIAL HOME", "> WAR MEMORIAL HOME", "INDEX",
    "WORLD WAR I", "WORLD WAR II", "KOREAN WAR", "VIETNAM WAR",
    "WAR ON TERROR (AFGHANISTAN-IRAQ)", "> WORLD WAR I", "> WORLD WAR II",
    "> KOREAN WAR", "> VIETNAM WAR", "> WAR ON TERROR (AFGHANISTAN-IRAQ)",
    "ERRATA", "SCV'S WAR ON TERROR CASUALTIES", "SCV'S WORLD WAR II CASUALTIES",
    "SCV'S KOREAN WAR CASUALTIES", "SCV'S VIETNAM WAR CASUALTIES",
    "SCV'S WORLD WAR I CASUALTIES",
}

SKIP_IMG_SUBSTR = cr.SKIP_IMG_SUBSTR + [
    "scvwarmemoriallogo", "warmemorial_monument",
]

# Extra external places common on casualty pages
EXTRA_EXTERNAL = [
    "Afghanistan", "Iraq", "South Korea", "North Korea", "Korea", "Vietnam",
    "South Vietnam", "North Vietnam", "Germany", "Italy", "Japan", "Philippines",
    "Guadalcanal", "Pacific Theater", "European Theater", "Arlington",
    "Arlington National Cemetery", "Honolulu", "Honolulu Memorial",
    "Glendale", "Forest Lawn", "Camp Kearny", "San Diego", "San Diego County",
    "Virginia", "Washington", "Washington State", "Nevada", "Tonopah",
    "Ft. Campbell", "Fort Campbell", "Camp Lewis", "Camp Pendleton",
    "Kandahar", "Epinonville", "France", "England", "Minnesota",
    "Maricopa", "Frazier Park",  # Frazier Park is in COMMUNITY_SET — won't go external
    "Hawaii", "Okinawa", "Iwo Jima", "Normandy", "Belgium", "Netherlands",
    "Kuwait", "Saudi Arabia", "Pakistan", "Syria", "Somalia",
    "Los Angeles County", "Orange County", "Riverside", "San Bernardino",
    "Bakersfield", "Maricopa High School", "Hart High School",
]

# Extend reynolds lists locally for this crawl
LOCAL_PLACES = set(cr.LOCAL_PLACES) | {
    "Wm. S. Hart High", "Hart High", "Hart High School", "Westfield Valencia Town Center",
    "Valencia Town Center", "Soledad Township",
}
# Soledad Township is community — remove from local if in COMMUNITY
for c in cr.COMMUNITY_SET:
    LOCAL_PLACES.discard(c)

EXTERNAL_SCAN = list(dict.fromkeys(cr.EXTERNAL_SCAN + EXTRA_EXTERNAL))
# Remove communities from external scan
EXTERNAL_SCAN = [e for e in EXTERNAL_SCAN if e not in cr.COMMUNITY_SET]

CASUALTY_NAME_RE = re.compile(
    r"(?:^|/)((?:ww1_|ww2_|korea_|terror_|vietnam)[a-z0-9_\-]+)\.html?$",
    re.I,
)
INDEX_NAME_RE = re.compile(r"casualties-index\.html?$", re.I)
SKIP_PATH_RE = re.compile(
    r"(home\.htm|search|disqus|key\.htm|bibliography|publications|"
    r"scvhistory\.htm|warmemorial_errata|googlesearch|#|"
    r"\.(gif|jpe?g|png|css|js)(\?|$))",
    re.I,
)

KNOWN_LABELS = [
    "Home of Record", "Date of Birth", "Birthplace", "High School", "Family",
    "Service", "Service Branch", "Grade at Loss", "Rank", "ID No", "ID Number",
    "Specialty", "Specialty (MOS)", "Length of Service", "Unit",
    "Start of Tour", "Start Tour", "Start Service", "Based", "Base",
    "Supporting", "Supporting Operation", "Incident Date", "Casualty Date",
    "Age at Loss", "Location", "Remains", "Interment", "Awards", "Narrative",
    "Notes", "Casualty Type", "Casualty Reason", "Casualty Detail",
    "Vietnam Memorial Wall", "Conflict", "Branch", "Further reading",
]


def log(msg: str) -> None:
    print(msg, flush=True)


def fetch_decode(url: str) -> tuple[str, str]:
    final, enc, raw = cr.fetch(url)
    html = cr.decode_html(raw, enc)
    return final, html


def is_casualty_url(url: str) -> bool:
    path = urllib.parse.urlparse(url).path
    if SKIP_PATH_RE.search(path):
        return False
    if INDEX_NAME_RE.search(path):
        return False
    return bool(CASUALTY_NAME_RE.search(path))


def is_index_url(url: str) -> bool:
    path = urllib.parse.urlparse(url).path.lower()
    if path.endswith("/warmemorial/home.htm") or path.endswith("/warmemorial/"):
        return True
    return bool(INDEX_NAME_RE.search(path))


def harvest_links(base_url: str, html: str) -> tuple[set[str], set[str]]:
    """Return (casualty_urls, index_urls) absolute."""
    soup = BeautifulSoup(html, "lxml")
    casualties: set[str] = set()
    indexes: set[str] = set()
    for tag in soup.find_all(["a", "area"], href=True):
        href = tag["href"].strip()
        if not href or href.startswith("javascript:") or href.startswith("mailto:"):
            continue
        absu = urllib.parse.urljoin(base_url, href)
        absu = absu.split("#")[0]
        if not cr.allowed_url(absu):
            continue
        host = urllib.parse.urlparse(absu).netloc.lower()
        if "scvhistory.com" not in host and "santaclaritawarmemorial" not in host:
            continue
        if is_casualty_url(absu):
            casualties.add(absu)
        elif is_index_url(absu):
            indexes.add(absu)
    return casualties, indexes


def extract_main_column_html(content_html: str) -> str:
    """Prefer the wide left content TD; fall back to full content."""
    soup = BeautifulSoup(content_html, "lxml")
    # Remove scripts/styles/comments early
    for tag in soup.find_all(["script", "style", "noscript"]):
        tag.decompose()
    for c in soup.find_all(string=lambda x: isinstance(x, Comment)):
        c.extract()

    # Find left content td: width ~800 or style containing width:800
    best = None
    best_score = 0
    for td in soup.find_all("td"):
        style = (td.get("style") or "") + " " + str(td.get("width") or "")
        text_len = len(td.get_text(" ", strip=True))
        score = 0
        if re.search(r"width\s*[:=]\s*['\"]?800", style, re.I):
            score += 100
        if "padding:0px 0px 20px 20px" in style or "padding:0px 0px 20px" in style:
            score += 50
        if text_len > 200:
            score += min(text_len // 50, 40)
        # Sidebar is ~120px
        if re.search(r"width\s*[:=]\s*['\"]?120", style, re.I):
            score -= 80
        if score > best_score:
            best_score = score
            best = td

    if best is not None and best_score >= 50:
        return best.decode_contents() if hasattr(best, "decode_contents") else str(best)

    # Fallback: remove sidebar-looking tds
    for td in list(soup.find_all("td")):
        style = (td.get("style") or "") + " " + str(td.get("width") or "")
        if re.search(r"width\s*[:=]\s*['\"]?120", style, re.I):
            td.decompose()
    body = soup.find("body") or soup
    return body.decode_contents() if hasattr(body, "decode_contents") else str(body)


def strip_chrome_from_soup(root: Tag) -> None:
    for img in list(root.find_all("img")):
        src = img.get("src") or ""
        if any(s in src for s in SKIP_IMG_SUBSTR):
            parent = img.parent
            if parent and parent.name == "a" and parent.find("img") is img:
                parent.decompose()
            else:
                img.decompose()
    for a in list(root.find_all("a")):
        t = " ".join(a.get_text(" ", strip=True).split()).upper()
        if t in CHROME_ANCHORS or t.startswith("> "):
            # Keep if it's a photo credit / content link with image
            if a.find("img"):
                continue
            parent = a.parent
            a.decompose()
            if parent and parent.name in ("font", "span", "p", "div") and not parent.get_text(strip=True):
                parent.decompose()
    for el in list(root.find_all(["span", "div", "p", "font"])):
        t = el.get_text(" ", strip=True)
        tu = t.upper()
        if len(t) > 400:
            continue
        if "RIGHTS RESERVED" in tu or t.startswith("SCVHistory.com is another service"):
            el.decompose()
    for el in list(root.find_all(id=re.compile(r"disqus", re.I))):
        el.decompose()


def html_to_br_text(html_frag: str) -> str:
    """Convert br/p to newlines then get text — preserves label lines."""
    frag = re.sub(r"<br\s*/?>", "\n", html_frag, flags=re.I)
    frag = re.sub(r"</p\s*>", "\n", frag, flags=re.I)
    frag = re.sub(r"</div\s*>", "\n", frag, flags=re.I)
    frag = re.sub(r"<hr\s*/?>", "\n", frag, flags=re.I)
    soup = BeautifulSoup(frag, "lxml")
    for tag in soup.find_all(["script", "style", "noscript"]):
        tag.decompose()
    text = soup.get_text("\n", strip=False)
    text = text.replace("\xa0", " ").replace("\r\n", "\n").replace("\r", "\n")
    # collapse excessive blank lines but keep single blanks
    lines = [ln.rstrip() for ln in text.split("\n")]
    out = []
    blank = 0
    for ln in lines:
        if not ln.strip():
            blank += 1
            if blank <= 1:
                out.append("")
        else:
            blank = 0
            out.append(ln.strip())
    return "\n".join(out).strip()


def parse_service_record(main_html: str) -> dict[str, str]:
    """Parse label:value pairs from main content. No empty values. No invention."""
    text = html_to_br_text(main_html)
    labels_sorted = sorted(KNOWN_LABELS, key=len, reverse=True)
    label_alt = r"[A-Z][A-Za-z0-9 /()'%.+-]*(?:\s*\([^)]+\))?"
    # Use [ \t]* not \s* after colon — \s* would eat blank lines and glue the next label into rest.
    pattern = re.compile(
        r"(?m)^(?P<label>" + "|".join(re.escape(l) for l in labels_sorted) + r"|" + label_alt + r")[ \t]*:[ \t]*(?P<rest>.*)$"
    )

    accept_prefix = set(l.lower() for l in KNOWN_LABELS) | {
        "birthplace", "family", "interment", "notes", "grade at loss",
        "casualty type", "casualty reason", "casualty detail",
        "vietnam memorial wall", "start service", "start tour", "based",
        "supporting", "specialty (mos)", "further reading",
    }

    matches = list(pattern.finditer(text))
    filtered = []
    for m in matches:
        lab = m.group("label").strip()
        lab_l = lab.lower()
        if lab_l in accept_prefix:
            filtered.append(m)
            continue
        words = lab.split()
        if 1 <= len(words) <= 5 and lab[0].isupper() and not lab.endswith("."):
            if lab_l.split()[0] in {
                "the", "he", "she", "at", "in", "on", "of", "to", "for", "and",
                "his", "her", "they", "this", "that", "with", "from", "ssg", "cpl",
                "pvt", "sgt", "spc", "lt", "pfc", "click", "see", "name",
            }:
                continue
            filtered.append(m)

    def is_label_line(ln: str) -> bool:
        return bool(pattern.match(ln.strip()))

    record: dict[str, str] = {}
    for i, m in enumerate(filtered):
        key = m.group("label").strip()
        key_l = key.lower()
        end = filtered[i + 1].start() if i + 1 < len(filtered) else len(text)
        first_rest = m.group("rest").strip()
        following = text[m.end():end]
        parts: list[str] = []
        if first_rest:
            parts.append(first_rest)

        multiline_keys = {"narrative", "notes", "awards", "remains", "further reading"}
        lines = [ln.strip() for ln in following.split("\n")]

        if key_l in multiline_keys:
            for ln in lines:
                if not ln:
                    continue
                if ln.upper() in CHROME_ANCHORS:
                    continue
                if ln.startswith("SCV's ") and "CASUALTIES" in ln.upper():
                    break
                if key_l == "remains" and re.match(
                    r"^(CPL|SSG|SGT|PVT|PFC|SPC|LT|CPT|1LT|2LT|MAJ|COL|GEN|LCDR|PO[123]|SN)\b",
                    ln,
                ):
                    break
                if key_l == "remains" and ln.lower().startswith("further reading"):
                    break
                if is_label_line(ln):
                    break
                parts.append(ln)
        else:
            # If value was empty on the label line (common when </b> splits label/value),
            # take the next non-empty line as the value.
            if not first_rest:
                for ln in lines:
                    if not ln:
                        continue
                    if is_label_line(ln):
                        break
                    if ln.upper() in CHROME_ANCHORS:
                        break
                    parts.append(ln)
                    break
            # Allow a parenthetical continuation only
            else:
                for ln in lines:
                    if not ln:
                        continue
                    if ln.startswith("(") and ln.endswith(")"):
                        parts.append(ln)
                    break

        value = " ".join(parts)
        value = re.sub(r"\s+", " ", value).strip()
        if key_l in ("narrative", "notes", "awards"):
            value = re.split(
                r"\bSCV's (?:WAR ON TERROR|WORLD WAR|KOREAN WAR|VIETNAM WAR)",
                value,
            )[0].strip()
        # Vietnam Memorial Wall: keep panel line only, drop photo caption leakage
        if key_l == "vietnam memorial wall":
            value = re.split(r"\bName on the Traveling\b", value)[0].strip()
        if not value:
            continue
        if key in record:
            if value not in record[key]:
                record[key] = record[key] + "; " + value
        else:
            record[key] = value
    return record



def extract_header_fields(content_html: str, full_html: str) -> tuple[str, str, str]:
    """title (name), byline (branch), date_raw from header classes / title tag."""
    soup = BeautifulSoup(content_html, "lxml")
    title = ""
    alth = soup.find(class_=re.compile(r"altheadline", re.I))
    if alth:
        title = " ".join(alth.get_text(" ", strip=True).split())
    byline = ""
    byl = soup.find(class_=re.compile(r"^byline$", re.I)) or soup.find(class_="byline")
    if byl:
        byline = " ".join(byl.get_text(" ", strip=True).split())
    date_raw = ""
    dl = soup.find(class_=re.compile(r"dateline", re.I))
    if dl:
        date_raw = " ".join(dl.get_text(" ", strip=True).split())
        date_raw = date_raw.lstrip("|").strip()

    if not title:
        tsoup = BeautifulSoup(full_html, "lxml")
        if tsoup.title and tsoup.title.string:
            t = tsoup.title.string.strip()
            t = re.sub(r"^SCVHistory\.com\s*\|\s*", "", t)
            t = re.sub(r"^SCV\s+(?:World War I|World War II|Korean War|Vietnam War|War on Terror)\s+Casualty\s*\|\s*", "", t, flags=re.I)
            # Keep name portion before comma branch if present
            title = t.strip()
    return title, byline, date_raw


def build_body_text(main_html: str, title: str, byline: str, date_raw: str) -> str:
    """Verbatim main content text including narrative; no sidebar/nav chrome."""
    soup = BeautifulSoup(main_html, "lxml")
    strip_chrome_from_soup(soup)
    # Also remove nested sidebar tables that say CASUALTIES INDEX
    for table in list(soup.find_all("table")):
        t = table.get_text(" ", strip=True).upper()
        if "CASUALTIES" in t and ("INDEX" in t or "SCV'S" in t) and len(t) < 800:
            # might be the brown header only — if mostly names of other casualties, drop
            if "HOME OF RECORD" not in t and "NARRATIVE" not in t:
                table.decompose()
    text = html_to_br_text(str(soup))
    # Prepend header if not already present
    header_bits = []
    if title and title not in text[:200]:
        header_bits.append(title)
    if byline and byline not in text[:300]:
        header_bits.append(byline)
    if date_raw and date_raw not in text[:400]:
        header_bits.append("| " + date_raw if not date_raw.startswith("|") else date_raw)
    if header_bits:
        text = "\n".join(header_bits) + "\n\n" + text
    # Strip leading conflict nav leftovers
    text = re.sub(
        r"^(?:>\s*)?(?:WAR MEMORIAL HOME|WORLD WAR I|WORLD WAR II|KOREAN WAR|VIETNAM WAR|WAR ON TERROR[^\n]*)\n+",
        "",
        text,
        flags=re.M,
    )
    while True:
        new = re.sub(
            r"^(?:>\s*)?(?:WAR MEMORIAL HOME|WORLD WAR I|WORLD WAR II|KOREAN WAR|VIETNAM WAR|WAR ON TERROR[^\n]*)\n+",
            "",
            text,
            flags=re.M,
        )
        if new == text:
            break
        text = new
    # Truncate at sidebar leakage
    text = re.split(
        r"\nSCV's (?:WAR ON TERROR|WORLD WAR II|WORLD WAR I|KOREAN WAR|VIETNAM WAR)",
        text,
    )[0].strip()
    return text


def extract_images_local(root: Tag) -> list[dict]:
    images = []
    pos = 0
    for img in root.find_all("img"):
        src = img.get("src")
        if src is None:
            continue
        src_raw = src
        if any(s in src_raw for s in SKIP_IMG_SUBSTR):
            continue
        # skip tiny sidebar thumbs that are other casualties (width 120 in sidebar already removed)
        pos += 1
        alt = img.get("alt") or ""
        caption = ""
        credit = ""
        links_to = ""
        parent = img.parent
        if parent and parent.name == "a" and parent.get("href"):
            links_to = parent.get("href") or ""
        container = img.find_parent(["td", "figure", "div", "p"]) or parent
        if container:
            cap = container.find(class_=re.compile(r"caption", re.I))
            if cap:
                caption = " ".join(cap.get_text(" ", strip=True).split())
            # bodysansbold caption near image
            boldcap = container.find(class_=re.compile(r"bodysansbold", re.I))
            if boldcap and not caption:
                caption = " ".join(boldcap.get_text(" ", strip=True).split())
        if not caption and parent:
            nxt = parent.find_next_sibling(["font", "p", "div", "i", "em", "br"])
            # look a bit further
            node = parent
            for _ in range(3):
                node = node.find_next_sibling() if node else None
                if not node:
                    break
                if getattr(node, "name", None) in ("font", "p", "div", "span"):
                    ct = " ".join(node.get_text(" ", strip=True).split())
                    if ct and len(ct) < 250 and "NEXT" not in ct.upper() and "CASUALTIES" not in ct.upper():
                        caption = ct
                        break
        images.append({
            "src_raw": src_raw,
            "alt": alt,
            "caption": caption,
            "credit_raw": credit,
            "position_in_body": pos,
            "links_to": links_to,
        })
    return images


def extract_places_ext(text: str):
    """Local places + external with extended EXTERNAL_SCAN; Surrey not a place."""
    local: dict[str, int] = defaultdict(int)
    needs_review: list[dict] = []

    for loc in sorted(LOCAL_PLACES, key=len, reverse=True):
        n = cr.count_wb(text, loc)
        if n:
            local[loc] += n

    for short, longers in cr.SHORT_FORMS.items():
        if short not in local:
            continue
        stand = cr.standalone_short_count(text, short, longers)
        if stand <= 0:
            local.pop(short, None)
        else:
            local[short] = stand
            needs_review.append({
                "reason": "possible_place_variant",
                "detail": (
                    f"'{short}' appears {stand} time(s) standing alone; "
                    f"possible variant of {', '.join(longers)}"
                ),
            })

    local.pop("Surrey", None)
    for c in list(local):
        if c in cr.COMMUNITY_SET:
            local.pop(c, None)

    external: dict[str, int] = defaultdict(int)
    for ext in sorted(EXTERNAL_SCAN, key=len, reverse=True):
        if ext in cr.COMMUNITY_SET or ext in LOCAL_PLACES:
            continue
        n = cr.count_wb(text, ext)
        if n <= 0:
            continue
        if ext == "San Fernando":
            for longer in [
                "San Fernando Pass", "San Fernando Road", "San Fernando road",
                "San Fernando Valley", "Mission San Fernando",
            ]:
                n -= cr.count_wb(text, longer)
            n = max(0, n)
        if ext == "San Francisco":
            n -= cr.count_wb(text, "Rancho San Francisco")
            n = max(0, n)
        if ext == "Korea":
            n -= cr.count_wb(text, "South Korea")
            n -= cr.count_wb(text, "North Korea")
            n -= cr.count_wb(text, "Korean War")
            n = max(0, n)
        if ext == "Vietnam":
            n -= cr.count_wb(text, "South Vietnam")
            n -= cr.count_wb(text, "North Vietnam")
            n -= cr.count_wb(text, "Vietnam War")
            n -= cr.count_wb(text, "Vietnam Memorial")
            n = max(0, n)
        if n:
            external[ext] += n

    for loc in list(external):
        if loc in local or loc in cr.COMMUNITY_SET or loc in LOCAL_PLACES:
            if loc in LOCAL_PLACES or loc in cr.COMMUNITY_SET:
                external.pop(loc, None)

    return dict(local), dict(external), needs_review


LABEL_LINE_RE = re.compile(
    r"(?m)^(?:" + "|".join(re.escape(l) for l in KNOWN_LABELS) + r")[ \t]*:[ \t]*.*$"
)


def body_for_entities(body_text: str, service_record: dict) -> str:
    """Strip service-record label lines so labels are not mistaken for people/places."""
    text = LABEL_LINE_RE.sub("", body_text)
    # Also strip bare label-only lines that already had values moved
    text = re.sub(
        r"(?m)^(?:" + "|".join(re.escape(l) for l in KNOWN_LABELS) + r")[ \t]*:[ \t]*$",
        "",
        text,
    )
    # Prefer narrative/notes as primary entity source when present
    chunks = []
    for k in ("Narrative", "Notes", "Awards"):
        if service_record.get(k):
            chunks.append(service_record[k])
    # Keep non-label residual prose (WW1 long form after remains)
    residual = text
    # Drop short header/byline leftovers
    residual = re.sub(r"(?m)^(U\.S\.\s+(?:Army|Navy|Air Force|Marine Corps|Coast Guard).*)$", "", residual)
    residual = re.sub(r"(?m)^\|\s*.*$", "", residual)
    if chunks:
        return "\n\n".join(chunks) + "\n\n" + residual
    return residual



def process_casualty(url: str, html: str, final_url: str, position: int) -> dict:
    legacy_path, legacy_key = cr.legacy_from_url(final_url)
    content_html = cr.extract_between_markers(html)
    main_html = extract_main_column_html(content_html)
    title, byline_raw, date_raw = extract_header_fields(content_html, html)
    service_record = parse_service_record(main_html)
    body_text = build_body_text(main_html, title, byline_raw, date_raw)

    soup = BeautifulSoup(main_html, "lxml")
    strip_chrome_from_soup(soup)
    body_html = soup.decode_contents() if hasattr(soup, "decode_contents") else str(soup)
    images = extract_images_local(soup)
    links_out = cr.extract_links(soup)
    # filter chrome links
    links_out = [
        L for L in links_out
        if L["anchor_text"].upper() not in CHROME_ANCHORS
        and not L["anchor_text"].upper().startswith("> ")
    ]
    # Main-column extraction drops the related-reading sidebar — restore it.
    related = cr.extract_related_reading_links(html, final_url)
    if not related:
        related = cr.extract_related_reading_links(content_html, final_url)
    links_out = cr.merge_links_out(links_out, related, final_url)
    editor_notes = cr.extract_editor_notes(soup, body_text)
    fine_print_raw = cr.extract_fine_print(content_html)
    dates_mentioned = cr.extract_dates(body_text)
    ent_text = body_for_entities(body_text, service_record)
    # Ensure page subject counted as a person
    if title:
        # strip trailing period from WW1 style titles
        subj = title.rstrip(".").strip()
        if subj and subj not in ent_text:
            ent_text = subj + "\n" + ent_text
    communities_mentioned = cr.extract_communities(body_text)  # communities from full body OK
    people = cr.extract_people(ent_text)
    # Drop people that are obviously service labels / medals / unit fragments
    drop_people = []
    for name in people:
        low = name.lower()
        if any(
            tok in low
            for tok in (
                "casualty", "incident", "memorial wall", "length of", "home of",
                "date of", "age at", "high school", "good conduct", "service medal",
                "purple heart", "bronze star", "achievement medal", "commendation",
            )
        ):
            drop_people.append(name)
            continue
        parts = name.split()
        if parts and parts[-1] in {
            "Date", "Record", "Loss", "Type", "Reason", "Detail", "Tour", "Service",
            "Wall", "Division", "Battalion", "Company", "Group", "Medals", "Medal",
            "Cemetery", "Memorial", "Remains", "Narrative", "Forces", "Beret",
            "Non", "Province", "Regiment", "Archives", "Code", "Immaterial",
            "Education", "Occupation", "Gender", "Race", "Status", "Disposition",
            "Gulf", "Interred", "Soil", "Missing", "Box", "Certification",
            "Chaplain", "Recipient", "Colonel", "Personnel", "Refer", "Number",
            "Request", "Ribbon", "Scholarship", "Badge", "Corps", "Trainer",
            "Fuselage", "Aircraft", "Registration", "Conference", "Bros",
            "Chairman", "Board", "Call", "Accident", "Life", "Dairy", "Kids",
            "Dingtoes", "Wedding", "Marker", "Histories", "Carthage", "Said",
            "Birthday", "Courier", "News", "Returned", "Officers", "Pocket",
            "Airport", "Vibrator", "Valiant", "Rocks",
        }:
            drop_people.append(name)
            continue
        if name in {"Air Force", "Green Beret", "Special Forces", "United States", "Died Non"}:
            drop_people.append(name)
    for name in drop_people:
        people.pop(name, None)
    # Force-include page subject if it looks like a person name
    if title:
        subj = re.sub(r'\s+', ' ', title).strip().rstrip('.')
        # drop nickname quotes for storage of a clean form but keep printable title form
        subj_clean = re.sub(r'\s*"[^"]+"\s*', ' ', subj).strip()
        subj_clean = re.sub(r'\s+', ' ', subj_clean)
        if subj_clean and len(subj_clean.split()) >= 2:
            people[subj_clean] = people.get(subj_clean, 0) + max(1, people.get(subj_clean, 0) or 1)
            if subj_clean not in people or people[subj_clean] < 1:
                people[subj_clean] = 1
            people[subj_clean] = max(people.get(subj_clean, 0), 1)
    # Extra surname tails that are not people on these pages
    for name in list(people):
        parts = name.split()
        if parts and parts[-1] in {
            "Afghan", "Unit", "Shock", "Heights", "Citation", "Citations",
            "Device", "NCO", "Noncrew", "Outright", "Recovered", "Unrecovered",
            "Freedom", "Theater", "Province", "County", "Park", "Lawn",
        }:
            people.pop(name, None)
        elif name in {
            "Taliban Afghan", "Surgical Shock", "Boyle Heights", "Korean Presidential Unit",
            "Died Non", "Infantry Regiment", "National Archives", "Persian Gulf",
            "Marine Corps", "Combat Infantryman Badge", "Air Corps", "Draft Board",
            "Draft Call", "Daily News", "Oxnard Daily Courier", "Warrant Officers",
            "Branch Code", "Branch Immaterial", "Civil Life Education", "Civil Occupation",
            "Dead Gender", "Male Race", "Marital Status", "Oklahoma Disposition",
            "Dead Interred", "Foreign Soil", "Korea Missing", "Post Office Box",
            "America Certification", "Catholic Chaplain", "Dear Recipient",
            "Lt. Colonel", "Official Military Personnel", "Reply Refer", "Request Number",
            "Overseas Service Ribbon", "Selective Service Registration", "Basic Trainer",
            "Training Aircraft Fuselage", "Grave Marker", "Oral Histories", "Roman Carthage",
            "Sidi Bou Said", "Double Wedding", "Billiwhack Dairy Can", "Carey Kids",
            "Desk Johnny Dingtoes", "Casablanca Conference", "Valpredo Bros",
            "Gelig Memorial Scholarship", "Cemetery James", "Protestant Cemetery Name",
        }:
            people.pop(name, None)
        # Drop 2-token form-field pairs where both tokens look like labels
        elif len(name.split()) == 2:
            a, b = name.split()
            if a in {
                "Branch", "Civil", "Dead", "Male", "Marital", "Foreign", "Post",
                "Office", "Official", "Military", "Reply", "Request", "Overseas",
                "Selective", "Basic", "Training", "Combat", "Infantry", "National",
                "Persian", "Marine", "Air", "Draft", "Daily", "Oxnard", "Warrant",
                "America", "Catholic", "Dear", "Grave", "Oral", "Roman", "Double",
                "Korea", "Virginia", "Oklahoma",
            } and b[0].isupper():
                # only drop if surname also looks non-personal
                if b in {
                    "Code", "Immaterial", "Education", "Occupation", "Gender", "Race",
                    "Status", "Soil", "Box", "Personnel", "Refer", "Number", "Ribbon",
                    "Registration", "Trainer", "Fuselage", "Badge", "Regiment",
                    "Archives", "Gulf", "Corps", "Board", "Call", "News", "Courier",
                    "Officers", "Certification", "Chaplain", "Recipient", "Marker",
                    "Histories", "Carthage", "Wedding", "Missing", "Disposition",
                    "Interred", "Colonel",
                }:
                    people.pop(name, None)
    # Drop shorter name forms that are token-prefixes of longer names on this page
    names = list(people)
    for short in names:
        sp = short.split()
        for longer in names:
            if short == longer:
                continue
            lp = longer.split()
            if len(lp) > len(sp) and lp[:len(sp)] == sp:
                people.pop(short, None)
                break
    local, external, short_nr = extract_places_ext(body_text)
    # Hart High short-form fold: if Hart High School or Wm. S. Hart High present, fold Hart High
    if "Hart High" in local and ("Hart High School" in local or "Wm. S. Hart High" in local):
        # keep standalone count logic already in extract_places_ext; OK
        pass
    orgs = cr.extract_orgs(ent_text)

    people_mentions = cr.mention_list(people)
    for ment in people_mentions:
        for link in links_out:
            if link["anchor_text"].strip() == ment["name_raw"] and link["is_internal"]:
                ment["linked_to"] = link["href_raw"]
                break
    places_mentions = cr.mention_list(local)
    orgs_mentions = cr.mention_list(orgs)
    external_mentions = cr.mention_list(external)

    needs_review = list(short_nr)
    last_groups: dict[str, list[str]] = defaultdict(list)
    for n in people:
        parts = n.replace(".", "").split()
        if parts:
            last_groups[parts[-1]].append(n)
    for last, group in last_groups.items():
        uniq = sorted(set(group))
        if len(uniq) >= 2 and last[0].isupper() and len(last) > 2:
            needs_review.append({
                "reason": "possible_same_person",
                "detail": f"Multiple name forms with surname {last}: " + "; ".join(uniq),
            })

    # Prefer canonical source_url as requested target when same path
    source_url = final_url
    if urllib.parse.urlparse(final_url).path.rstrip("/") == urllib.parse.urlparse(url).path.rstrip("/"):
        source_url = url

    page = {
        "source_url": source_url,
        "legacy_path": legacy_path,
        "legacy_key": legacy_key,
        "title": title or "",
        "subtitle": "",
        "byline_raw": byline_raw or "",
        "date_raw": date_raw or "",
        "series_position": position,
        "body_text": body_text,
        "body_html": body_html,
        "editor_notes": editor_notes,
        "images": images,
        "links_out": links_out,
        "dates_mentioned": dates_mentioned,
        "people_mentioned": people_mentions,
        "places_mentioned": places_mentions,
        "orgs_mentioned": orgs_mentions,
        "communities_mentioned": communities_mentioned,
        "external_places_mentioned": external_mentions,
        "fine_print_raw": fine_print_raw or "",
        "needs_review": needs_review,
        "service_record": service_record,
    }
    for k, v in list(page.items()):
        if v is None:
            if k == "service_record":
                page[k] = {}
            elif k in (
                "editor_notes", "images", "links_out", "dates_mentioned",
                "people_mentioned", "places_mentioned", "orgs_mentioned",
                "communities_mentioned", "external_places_mentioned", "needs_review",
            ):
                page[k] = []
            else:
                page[k] = ""
    return page


def main() -> None:
    CACHE_DIR.mkdir(parents=True, exist_ok=True)
    OUT_PATH.parent.mkdir(parents=True, exist_ok=True)

    notes: list[str] = []
    failures: list[str] = []
    casualty_urls: set[str] = set()
    index_queue = list(SEED_INDEXES)
    seen_indexes: set[str] = set()

    # robots courtesy
    try:
        time.sleep(SLEEP)
        cr.fetch("https://scvhistory.com/robots.txt")
        notes.append("robots.txt fetched; warmemorial/scvhistory casualty paths allowed.")
    except Exception as e:
        notes.append(f"robots.txt fetch note: {e}")

    while index_queue:
        idx_url = index_queue.pop(0)
        # normalize
        key = idx_url.split("#")[0].rstrip("/")
        if key in seen_indexes:
            continue
        seen_indexes.add(key)
        log(f"INDEX {idx_url}")
        time.sleep(SLEEP)
        try:
            final, html = fetch_decode(idx_url)
            safe = re.sub(r"[^\w.-]+", "_", urllib.parse.urlparse(final).path.strip("/"))
            (CACHE_DIR / f"index_{safe}").write_text(html, encoding="utf-8", errors="replace")
            cas, idxs = harvest_links(final, html)
            casualty_urls |= cas
            for i in idxs:
                ik = i.split("#")[0].rstrip("/")
                if ik not in seen_indexes:
                    index_queue.append(i)
            notes.append(f"Index {final}: +{len(cas)} casualty links (running unique={len(casualty_urls)})")
            log(f"  -> {len(cas)} casualties, {len(idxs)} index links; unique casualties={len(casualty_urls)}")
        except Exception as e:
            failures.append(f"index {idx_url}: {e}")
            log(f"  FAIL {e}")

    # Sort stably: conflict then name
    def sort_key(u: str) -> tuple:
        path = urllib.parse.urlparse(u).path.lower()
        bn = path.rsplit("/", 1)[-1]
        order = {"ww1_": 0, "ww2_": 1, "korea_": 2, "vietnam": 3, "terror_": 4}
        o = 9
        for pref, val in order.items():
            if bn.startswith(pref):
                o = val
                break
        return (o, bn)

    urls = sorted(casualty_urls, key=sort_key)
    log(f"Total casualty URLs to crawl: {len(urls)}")

    pages: list[dict] = []
    for i, url in enumerate(urls, 1):
        log(f"[{i}/{len(urls)}] {url}")
        time.sleep(SLEEP)
        try:
            final, html = fetch_decode(url)
            safe = re.sub(r"[^\w.-]+", "_", urllib.parse.urlparse(final).path.strip("/"))
            (CACHE_DIR / safe).write_bytes(html.encode("utf-8", errors="replace"))
            # Also keep latin-1 original? store as utf-8 replaced is fine for cache
            # Re-fetch raw for accurate cache — actually decode already happened;
            # re-write using original bytes via fetch again is wasteful. Store html.
            page = process_casualty(url, html, final, i)
            pages.append(page)
            log(
                f"  OK {page['legacy_key']} title={page['title']!r} "
                f"sr_keys={list(page['service_record'].keys())[:6]}..."
            )
        except Exception as e:
            failures.append(f"{url}: {e}")
            log(f"  FAIL {e}")

    crawled_keys = [p["legacy_key"] for p in pages]
    craft_set = set(CRAFT_36)
    matched = sorted(k for k in crawled_keys if k in craft_set)
    missing = sorted(craft_set - set(crawled_keys))

    entity_index = cr.build_entity_index(pages)

    meta = {
        "section": "warmemorial",
        "crawled": CRAWLED,
        "index_url": INDEX_URL,
        "page_count": len(pages),
        "craft_expected_count": 36,
        "craft_matched_count": len(matched),
        "craft_missing_from_crawl": missing,
        "craft_matched_keys": matched,
        "crawler_notes": (
            "Wave 2 War Memorial casualty crawl. Indexes crawled from home + WW1/WW2/Korea/"
            "Vietnam/Terror casualty index pages (indexes harvested for links only, not as articles). "
            "UA SCVHistory-Legacy-Extract/1.0; 1.0s between requests; robots.txt; meta-refresh; "
            "latin-1/charset decode. service_record = printed label→value only (no invented/empty). "
            "Entity rules match Perkins Wave 0 / Reynolds. "
            + " ".join(notes)
            + (f" Failures: {failures}" if failures else " No fetch failures.")
        ),
        "person_rule": cr.PERSON_RULE,
        "place_rule": cr.PLACE_RULE,
        "failures": failures,
        "indexes_crawled": sorted(seen_indexes),
    }

    out = {
        "meta": meta,
        "pages": pages,
        "entity_index": entity_index,
    }

    # null scrub
    def scrub(obj):
        if isinstance(obj, dict):
            return {k: scrub(v) for k, v in obj.items() if v is not None}
        if isinstance(obj, list):
            return [scrub(x) for x in obj]
        return obj

    out = scrub(out)

    with OUT_PATH.open("w", encoding="utf-8") as f:
        json.dump(out, f, indent=2, ensure_ascii=False)
        f.write("\n")

    byte_size = OUT_PATH.stat().st_size
    log(f"Wrote {OUT_PATH} ({byte_size} bytes)")
    log(f"page_count={len(pages)} craft_matched={len(matched)} missing={missing}")
    log(f"entity people={len(entity_index['people'])} places={len(entity_index['places'])} "
        f"orgs={len(entity_index['organizations'])} external={len(entity_index['external_places'])} "
        f"communities={len(entity_index['community_mentions'])}")
    if pages:
        log(f"sample service_record keys ({pages[0]['legacy_key']}): {list(pages[0]['service_record'].keys())}")


if __name__ == "__main__":
    main()
