#!/usr/bin/env python3
"""Fail when a docs SVG is not well-formed UTF-8 XML, has no viewBox, or has path data a browser
would stop drawing at (an unknown command or a command with the wrong number of arguments).

Scans docs/public. Extra directories can be passed as arguments so the built
site can be checked with the same rules.
"""

from __future__ import annotations

import re
import sys
import xml.etree.ElementTree as ET
from pathlib import Path

REPO = Path(__file__).resolve().parents[2]
DEFAULT_ROOTS = (REPO / "docs" / "public",)


def svg_files(roots: list[Path]) -> list[Path]:
    files: list[Path] = []
    for root in roots:
        if not root.exists():
            continue
        if root.is_file() and root.suffix.lower() == ".svg":
            files.append(root)
            continue
        files.extend(sorted(path for path in root.rglob("*") if path.suffix.lower() == ".svg"))
    return files


def check(path: Path) -> list[str]:
    errors: list[str] = []
    data = path.read_bytes()
    try:
        text = data.decode("utf-8")
    except UnicodeDecodeError as exc:
        return [f"{path}: not valid UTF-8 ({exc})"]

    if "\ufffd" in text:
        errors.append(f"{path}: contains U+FFFD, which browsers will not treat as a currency sign")

    try:
        root = ET.fromstring(text)
    except ET.ParseError as exc:
        errors.append(f"{path}: not well-formed XML ({exc})")
        return errors

    if root.get("viewBox") is None:
        errors.append(f"{path}: the <svg> element has no viewBox")
    for element in root.iter():
        if element.tag.endswith("}path") or element.tag == "path":
            problem = path_data_error(element.get("d", ""))
            if problem:
                errors.append(f"{path}: broken path data ({problem}): {element.get('d', '')[:60]}")

    return errors


ARGS = {"M": 2, "L": 2, "H": 1, "V": 1, "C": 6, "S": 4, "Q": 4, "T": 2, "A": 7, "Z": 0}
TOKEN = re.compile(r"[MmLlHhVvCcSsQqTtAaZz]|[-+]?(?:\d+\.?\d*|\.\d+)(?:[eE][-+]?\d+)?|[\s,]+|.")


def path_data_error(d: str) -> str | None:
    """Returns why the path data is broken, or None. Arc flags written without separators are not supported."""
    command = None
    numbers = 0
    for token in TOKEN.findall(d):
        if token.strip(" \t\r\n,") == "":
            continue
        if token.upper() in ARGS:
            if command is not None and (ARGS[command] == 0 and numbers or ARGS[command] and (numbers == 0 or numbers % ARGS[command])):
                return f"{command} has {numbers} numbers"
            if command is None and token.upper() != "M":
                return "does not start with M"
            command, numbers = token.upper(), 0
            continue
        if not re.fullmatch(r"[-+]?(?:\d+\.?\d*|\.\d+)(?:[eE][-+]?\d+)?", token):
            return f"unexpected {token!r}"
        if command is None:
            return "number before the first command"
        numbers += 1
    if command is None:
        return "empty"
    if ARGS[command] and (numbers == 0 or numbers % ARGS[command]):
        return f"{command} has {numbers} numbers"
    return None


def main(argv: list[str]) -> int:
    roots = [Path(arg) for arg in argv[1:]] or list(DEFAULT_ROOTS)
    files = svg_files(roots)
    if not files:
        print("No SVG files found in: " + ", ".join(str(root) for root in roots), file=sys.stderr)
        return 1

    failed = False
    for path in files:
        errors = check(path)
        if errors:
            failed = True
            for error in errors:
                print(error, file=sys.stderr)
        else:
            print(f"ok {path}")

    return 1 if failed else 0


if __name__ == "__main__":
    sys.exit(main(sys.argv))
