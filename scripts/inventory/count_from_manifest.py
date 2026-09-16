"""Count files by extension and path prefix from the copied manifest."""

from __future__ import annotations

import json
import sys
from collections import Counter
from pathlib import Path

from common import RAW_DIR, ensure_raw, parse_manifest_path


def main() -> int:
    ensure_raw()
    src = RAW_DIR / "scvhistory-manifest-2026-08-20.sha256"
    if not src.is_file():
        print(f"STOP: copied manifest missing at {src}", file=sys.stderr)
        return 2

    ext = Counter()
    top = Counter()
    second = Counter()
    html_bucket = Counter()
    n = 0
    with src.open("r", errors="replace") as f:
        for line in f:
            path = parse_manifest_path(line)
            if path is None:
                continue
            n += 1
            parts = path.split("/")
            top[parts[0]] += 1
            if len(parts) >= 2:
                second[f"{parts[0]}/{parts[1]}"] += 1
            name = parts[-1]
            if "." in name:
                e = name.rsplit(".", 1)[-1].lower()
            else:
                e = "(noext)"
            ext[e] += 1
            low = path.lower()
            if low.endswith(".htm") or low.endswith(".html"):
                if path.startswith("scvhistory/files/"):
                    html_bucket["scvhistory/files"] += 1
                elif path.startswith("scvhistory/") and path.count("/") == 1:
                    html_bucket["scvhistory root"] += 1
                elif path.startswith("scvhistory/signal/"):
                    html_bucket["scvhistory/signal"] += 1
                else:
                    html_bucket[parts[0]] += 1

    out = {
        "source": str(src),
        "file_count": n,
        "by_extension": dict(ext.most_common()),
        "by_top": dict(top.most_common()),
        "by_second": dict(second.most_common(80)),
        "html_by_bucket": dict(html_bucket.most_common()),
    }
    dest = RAW_DIR / "extension-counts.json"
    dest.write_text(json.dumps(out, indent=2) + "\n")
    print(f"files={n} extensions={len(ext)} wrote {dest}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
