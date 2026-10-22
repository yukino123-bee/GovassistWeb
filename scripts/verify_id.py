#!/usr/bin/env python3
"""
Checks that the name on an uploaded valid ID matches the resident's name.

Only rejects when the ID was clearly readable and neither the first nor the
last name appears on it. Anything unclear (blurry photo, no OCR engine on the
server) is accepted so genuine residents are never blocked.

Usage: verify_id.py <image> <first_name> [middle_name] [last_name]
"""
import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from document_review import (  # noqa: E402
    MIN_CONFIDENT_OCR,
    MIN_READABLE_WORDS,
    clean_text,
    extract_any,
    word_matches,
)


def name_present(name, document_words):
    parts = [p for p in clean_text(name).split() if p]

    return bool(parts) and any(word_matches(p, document_words) or p in document_words for p in parts)


def verify_id(path, first_name, middle_name="", last_name=""):
    if not os.path.exists(path):
        return {"match": True, "method": "missing_file", "note": "File could not be read, accepted."}

    extraction = extract_any(path)
    words = extraction["words"]
    confidence = extraction["confidence"]

    readable = words >= MIN_READABLE_WORDS and (confidence is None or confidence >= MIN_CONFIDENT_OCR)
    if not readable:
        return {
            "match": True,
            "method": extraction["engine"] or "unreadable",
            "readable": False,
            "issues": extraction["issues"],
            "note": "The ID could not be read automatically, so it was accepted for staff review.",
        }

    document_words = set(clean_text(extraction["text"]).split())
    first_ok = name_present(first_name, document_words) if clean_text(first_name) else True
    last_ok = name_present(last_name, document_words) if clean_text(last_name) else True
    middle_ok = name_present(middle_name, document_words) if clean_text(middle_name) else True

    # A single missing part can be an OCR slip or a formatting difference; both missing is a real mismatch.
    match = first_ok or last_ok
    note = "Name found on the ID." if first_ok and last_ok else "Only part of the name was found on the ID."
    if not match:
        note = "Neither the first nor the last name was found on the ID."

    return {
        "match": match,
        "method": extraction["engine"],
        "readable": True,
        "name_parts": {"first": first_ok, "middle": middle_ok, "last": last_ok},
        "note": note,
    }


if __name__ == "__main__":
    arguments = sys.argv[1:]
    if not arguments:
        print(json.dumps({"match": True, "note": "Missing arguments, accepting"}))
        sys.exit(0)

    first_name = arguments[1] if len(arguments) > 1 else ""
    middle_name = arguments[2] if len(arguments) > 2 else ""
    last_name = arguments[3] if len(arguments) > 3 else ""

    print(json.dumps(verify_id(arguments[0], first_name, middle_name, last_name)))
