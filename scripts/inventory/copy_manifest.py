"""Copy the Jordy sha256 manifest onto the internal disk. Read-only on Jordy."""

from __future__ import annotations

import shutil
import sys

from common import MANIFEST_SRC, RAW_DIR, ensure_raw, require_drive


def main() -> int:
    require_drive()
    ensure_raw()
    dest = RAW_DIR / "scvhistory-manifest-2026-08-20.sha256"
    if not MANIFEST_SRC.is_file():
        print(f"STOP: manifest missing at {MANIFEST_SRC}", file=sys.stderr)
        return 3
    shutil.copy2(MANIFEST_SRC, dest)
    print(f"copied {MANIFEST_SRC.stat().st_size} bytes to {dest}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
