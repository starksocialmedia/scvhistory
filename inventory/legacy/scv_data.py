"""Locate the private derived-data directory (starksocialmedia/scvhistory-data).

Obituary data does not live in this repo. Scripts resolve it as:
  1. $SCV_DATA_DIR, if set (relative paths are resolved against the repo root);
  2. <repo root>/../scvhistory-data, if that directory exists (both repos cloned side by side);
  3. <repo root>/inventory/private/ (gitignored fallback).
Obituary files sit in the "obituaries/" subdirectory of that directory.
Never commit obituary data to this repo.
"""
from __future__ import annotations

import os
from pathlib import Path

REPO_ROOT = Path(__file__).resolve().parents[2]


def data_dir() -> Path:
    env = os.environ.get("SCV_DATA_DIR")
    if env:
        p = Path(env).expanduser()
        return p if p.is_absolute() else (REPO_ROOT / p).resolve()
    sibling = (REPO_ROOT.parent / "scvhistory-data").resolve()
    if sibling.is_dir():
        return sibling
    return REPO_ROOT / "inventory" / "private"


def obituaries_dir() -> Path:
    return data_dir() / "obituaries"
