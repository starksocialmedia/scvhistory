"""Walk Jordy in batches and record bytes by extension. Resumable. Read-only."""

from __future__ import annotations

import json
import os
import stat
import sys
from collections import Counter
from pathlib import Path

from common import SITE_ROOT, RAW_DIR, drive_ok, ensure_raw, require_drive


def load_done(path: Path) -> set[str]:
    done: set[str] = set()
    if not path.is_file():
        return done
    with path.open("r", errors="replace") as f:
        for line in f:
            line = line.strip()
            if not line:
                continue
            try:
                row = json.loads(line)
            except json.JSONDecodeError:
                continue
            batch = row.get("batch")
            if batch:
                done.add(batch)
    return done


def list_batches() -> list[tuple[str, Path]]:
    batches: list[tuple[str, Path]] = []
    batches.append(("root-files", SITE_ROOT))
    for name in sorted(os.listdir(SITE_ROOT)):
        p = SITE_ROOT / name
        if not p.is_dir():
            continue
        if name == "scvhistory":
            continue
        batches.append((name, p))
    scv = SITE_ROOT / "scvhistory"
    if scv.is_dir():
        batches.append(("scvhistory-root", scv))
        for name in sorted(os.listdir(scv)):
            p = scv / name
            if not p.is_dir():
                continue
            if name == "files":
                continue
            batches.append((f"scvhistory/{name}", p))
        files = scv / "files"
        if files.is_dir():
            for name in sorted(os.listdir(files)):
                p = files / name
                if p.is_dir():
                    batches.append((f"files/{name}", p))
            batches.append(("files-loose", files))
    return batches


def walk_batch(batch_id: str, root: Path) -> dict:
    files = 0
    bytes_by_ext: Counter[str] = Counter()
    errors = 0
    only_immediate_files = batch_id in ("root-files", "scvhistory-root", "files-loose")

    def consider(dirpath: Path, names: list[str], immediate: bool) -> None:
        nonlocal files, errors
        for name in names:
            if name in (".", ".."):
                continue
            p = dirpath / name
            try:
                st = p.lstat()
            except OSError:
                errors += 1
                continue
            if stat.S_ISDIR(st.st_mode):
                continue
            if not stat.S_ISREG(st.st_mode):
                continue
            if batch_id == "root-files" and dirpath != SITE_ROOT:
                continue
            if batch_id == "scvhistory-root":
                rel = p.relative_to(root)
                if rel.parts and rel.parts[0] in ("files",):
                    continue
                if len(rel.parts) > 1:
                    continue
            if batch_id == "files-loose":
                if dirpath != root:
                    continue
            files += 1
            if "." in name:
                ext = name.rsplit(".", 1)[-1].lower()
            else:
                ext = "(noext)"
            bytes_by_ext[ext] += st.st_size

    if only_immediate_files:
        try:
            names = os.listdir(root)
        except OSError:
            return {
                "batch": batch_id,
                "files": 0,
                "errors": 1,
                "bytes_by_ext": {},
                "skipped": True,
            }
        consider(root, names, True)
    else:
        for dirpath, dirnames, filenames in os.walk(root, followlinks=False):
            if not drive_ok():
                raise RuntimeError("dismount")
            consider(Path(dirpath), filenames, False)

    return {
        "batch": batch_id,
        "files": files,
        "errors": errors,
        "bytes_by_ext": dict(bytes_by_ext),
    }


def append_row(path: Path, row: dict) -> None:
    with path.open("a") as f:
        f.write(json.dumps(row, separators=(",", ":")) + "\n")
        f.flush()
        os.fsync(f.fileno())


def main() -> int:
    require_drive()
    ensure_raw()
    out = RAW_DIR / "sizes.jsonl"
    done = load_done(out)
    batches = list_batches()
    remaining = [b for b in batches if b[0] not in done]
    print(f"batches total={len(batches)} done={len(done)} remaining={len(remaining)}")
    for i, (batch_id, root) in enumerate(remaining, 1):
        if not drive_ok():
            print(f"STOP: drive gone before batch {batch_id}", file=sys.stderr)
            return 3
        try:
            row = walk_batch(batch_id, root)
        except RuntimeError as e:
            if str(e) == "dismount":
                print(f"STOP: drive gone during batch {batch_id}", file=sys.stderr)
                return 3
            raise
        append_row(out, row)
        if i % 25 == 0 or i == len(remaining):
            print(
                f"{i}/{len(remaining)} {batch_id} files={row['files']} "
                f"errors={row.get('errors', 0)}"
            )
    print(f"wrote {out}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
