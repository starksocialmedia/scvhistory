"""Shared helpers for Jordy inventory scripts. Never write to the drive."""

from __future__ import annotations

import os
import sys
from pathlib import Path

DRIVE_ROOT = Path("/Volumes/Jordy/SCVHistory")
SITE_ROOT = DRIVE_ROOT / "scvhistory.com"
MANIFEST_SRC = DRIVE_ROOT / "scvhistory-manifest-2026-08-20.sha256"

REPO_ROOT = Path(__file__).resolve().parents[2]
INVENTORY_DIR = REPO_ROOT / "inventory"
RAW_DIR = INVENTORY_DIR / "raw"


def drive_ok() -> bool:
    return DRIVE_ROOT.is_dir() and SITE_ROOT.is_dir()


def require_drive() -> None:
    if drive_ok():
        return
    print(
        "STOP: /Volumes/Jordy/SCVHistory/scvhistory.com is not mounted. "
        "No files were written to the drive.",
        file=sys.stderr,
    )
    sys.exit(3)


def ensure_raw() -> Path:
    RAW_DIR.mkdir(parents=True, exist_ok=True)
    return RAW_DIR


def parse_manifest_path(line: str) -> str | None:
    line = line.rstrip("\n")
    if "  " not in line:
        return None
    path = line.split("  ", 1)[1]
    if path.startswith("./"):
        path = path[2:]
    return path or None
