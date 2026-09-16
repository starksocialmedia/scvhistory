"""Merge sizes.jsonl into bytes-by-extension totals."""

from __future__ import annotations

import json
import sys
from collections import Counter
from pathlib import Path

from common import RAW_DIR, ensure_raw


def main() -> int:
    ensure_raw()
    src = RAW_DIR / "sizes.jsonl"
    if not src.is_file():
        print(f"STOP: {src} missing", file=sys.stderr)
        return 2
    files = 0
    errors = 0
    bytes_by_ext: Counter[str] = Counter()
    batches = 0
    with src.open("r", errors="replace") as f:
        for line in f:
            line = line.strip()
            if not line:
                continue
            row = json.loads(line)
            batches += 1
            files += int(row.get("files") or 0)
            errors += int(row.get("errors") or 0)
            for ext, n in (row.get("bytes_by_ext") or {}).items():
                bytes_by_ext[ext] += int(n)
    total_bytes = sum(bytes_by_ext.values())
    out = {
        "batches": batches,
        "files": files,
        "errors": errors,
        "total_bytes": total_bytes,
        "total_gib": round(total_bytes / (1024**3), 2),
        "by_extension_bytes": dict(bytes_by_ext.most_common()),
    }
    dest = RAW_DIR / "sizes-summary.json"
    dest.write_text(json.dumps(out, indent=2) + "\n")
    print(
        f"batches={batches} files={files} errors={errors} "
        f"bytes={total_bytes} gib={out['total_gib']} wrote {dest}"
    )
    return 0


if __name__ == "__main__":
    sys.exit(main())
