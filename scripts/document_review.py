#!/usr/bin/env python3
"""
GovAssist document review engine.

Reads a resident's uploaded requirement (image, PDF, DOCX, DOC), checks it
against the keywords / blank template an administrator configured, and returns
one of three outcomes as JSON:

  verified  - everything expected was found, safe to auto-approve
  review    - could not be decided automatically, a facilitator must look
  mismatch  - the document was readable and is clearly the wrong one

Usage:
  document_review.py review <document> [template] [keywords]
  document_review.py text <document>
"""
import json
import os
import re
import shutil
import subprocess
import sys
import tempfile
import unicodedata
import zipfile
import xml.etree.ElementTree as ET

try:
    from rapidfuzz import fuzz

    def similarity(left, right):
        return fuzz.ratio(left, right) / 100.0
except Exception:  # pragma: no cover - rapidfuzz is optional
    from difflib import SequenceMatcher

    def similarity(left, right):
        return SequenceMatcher(None, left, right).ratio()

OCR_LANGUAGE = os.environ.get("GOVASSIST_OCR_LANG", "eng")
MAX_PDF_PAGES = 3
MIN_READABLE_WORDS = 12
MIN_CONFIDENT_OCR = 55.0
LOW_WORD_FUZZY = 0.8


# --------------------------------------------------------------------------
# Text helpers
# --------------------------------------------------------------------------

def clean_text(text):
    if not text:
        return ""

    normalized = unicodedata.normalize("NFKD", text).lower()
    normalized = "".join(c for c in normalized if not unicodedata.combining(c))
    normalized = re.sub(r"[^a-z0-9]+", " ", normalized)

    return " ".join(normalized.split())


def ocr_fold(word):
    """Collapse the characters OCR engines most often confuse."""
    return word.replace("rn", "m").replace("1", "l").replace("0", "o")


def word_matches(keyword_word, document_words):
    if keyword_word in document_words:
        return True

    if len(keyword_word) < 4:
        return False

    folded = ocr_fold(keyword_word)

    return any(
        similarity(folded, ocr_fold(word)) >= LOW_WORD_FUZZY
        for word in document_words
        if abs(len(keyword_word) - len(word)) <= 2
    )


def split_keywords(raw):
    seen = []
    for part in re.split(r"[,;\n]+", raw or ""):
        cleaned = clean_text(part)
        if cleaned and cleaned not in seen:
            seen.append(cleaned)

    return seen


def keyword_found(keyword, cleaned_text, document_words):
    if f" {keyword} " in f" {cleaned_text} ":
        return True

    words = keyword.split()
    important = [w for w in words if len(w) > 3] or words

    return all(word_matches(w, document_words) for w in important)


def search_keywords(text, keywords_str):
    """True only when every configured keyword is present in the text."""
    keywords = split_keywords(keywords_str)
    if not text or not keywords:
        return False

    cleaned = clean_text(text)
    words = set(cleaned.split())

    return all(keyword_found(k, cleaned, words) for k in keywords)


def template_overlap(template_text, document_text):
    """Share of the blank form's printed labels that also appear on the document."""
    template_words = {w for w in clean_text(template_text).split() if len(w) >= 4}
    if len(template_words) < 8:
        return None

    document_words = set(clean_text(document_text).split())
    found = sum(1 for w in template_words if word_matches(w, document_words))

    return found / len(template_words)


# --------------------------------------------------------------------------
# Extraction: native text formats
# --------------------------------------------------------------------------

def extract_docx(path):
    try:
        with zipfile.ZipFile(path) as docx:
            root = ET.fromstring(docx.read("word/document.xml"))
        ns = "{http://schemas.openxmlformats.org/wordprocessingml/2006/main}"

        return " ".join(el.text for el in root.iter(ns + "t") if el.text)
    except Exception:
        return ""


def extract_doc(path):
    if not shutil.which("antiword"):
        return ""
    try:
        result = subprocess.run(["antiword", path], capture_output=True, text=True, timeout=30)

        return result.stdout.strip() if result.returncode == 0 else ""
    except Exception:
        return ""


def extract_pdf(path):
    """Embedded PDF text first, OCR of rasterised pages when it is a scan."""
    text = ""
    if shutil.which("pdftotext"):
        try:
            result = subprocess.run(
                ["pdftotext", "-layout", "-enc", "UTF-8", "-l", "10", path, "-"],
                capture_output=True, text=True, timeout=60,
            )
            text = result.stdout if result.returncode == 0 else ""
        except Exception:
            text = ""

    if len(re.sub(r"[^A-Za-z0-9]", "", text)) >= 40:
        return {"text": text, "confidence": 100.0, "words": len(text.split()), "engine": "pdf_text", "issues": []}

    if not shutil.which("pdftoppm"):
        return {"text": text, "confidence": None, "words": len(text.split()), "engine": None, "issues": []}

    pages = []
    with tempfile.TemporaryDirectory(prefix="govassist-pdf-") as directory:
        try:
            subprocess.run(
                ["pdftoppm", "-png", "-r", "200", "-f", "1", "-l", str(MAX_PDF_PAGES), path, os.path.join(directory, "page")],
                check=True, capture_output=True, timeout=90,
            )
        except Exception:
            return {"text": text, "confidence": None, "words": 0, "engine": None, "issues": []}

        for name in sorted(os.listdir(directory)):
            if name.endswith(".png"):
                pages.append(extract_image(os.path.join(directory, name)))

    return merge_results(pages)


def merge_results(results):
    results = [r for r in results if r]
    if not results:
        return {"text": "", "confidence": None, "words": 0, "engine": None, "issues": []}

    words = sum(r["words"] for r in results)
    weighted = [(r["confidence"], r["words"]) for r in results if r["confidence"] is not None]
    confidence = (
        sum(c * w for c, w in weighted) / max(sum(w for _, w in weighted), 1) if weighted else None
    )
    issues = []
    for result in results:
        for issue in result["issues"]:
            if issue not in issues:
                issues.append(issue)

    return {
        "text": "\n".join(r["text"] for r in results),
        "confidence": confidence,
        "words": words,
        "engine": next((r["engine"] for r in results if r["engine"]), None),
        "issues": issues,
    }


# --------------------------------------------------------------------------
# Extraction: images (OpenCV preparation + Tesseract)
# --------------------------------------------------------------------------

_tesseract_languages = None


def tesseract_language():
    global _tesseract_languages
    if _tesseract_languages is None:
        try:
            output = subprocess.run(["tesseract", "--list-langs"], capture_output=True, text=True, timeout=15)
            _tesseract_languages = set(output.stdout.split()[1:]) | set(output.stderr.split())
        except Exception:
            _tesseract_languages = set()

    wanted = [lang for lang in OCR_LANGUAGE.split("+") if lang in _tesseract_languages]

    return "+".join(wanted) if wanted else "eng"


def run_tesseract(image_path):
    """Return (text, mean_confidence, word_count) or None when the engine fails."""
    try:
        result = subprocess.run(
            ["tesseract", image_path, "stdout", "-l", tesseract_language(), "--psm", "6", "tsv"],
            capture_output=True, text=True, timeout=90,
        )
    except Exception:
        return None
    if result.returncode != 0:
        return None

    words, confidences = [], []
    for line in result.stdout.splitlines()[1:]:
        cells = line.split("\t")
        if len(cells) < 12 or not cells[11].strip():
            continue
        try:
            confidence = float(cells[10])
        except ValueError:
            continue
        if confidence < 0:
            continue
        words.append(cells[11].strip())
        confidences.append(confidence)

    if not words:
        return "", 0.0, 0

    return " ".join(words), sum(confidences) / len(confidences), len(words)


def load_gray(path):
    import cv2
    import numpy as np
    from PIL import Image, ImageOps

    with Image.open(path) as image:
        image = ImageOps.exif_transpose(image).convert("L")
        return np.array(image), cv2


def estimate_skew(gray, cv2, np):
    """Projection-profile skew search between -8 and +8 degrees."""
    height, width = gray.shape
    scale = 700.0 / max(height, width)
    small = cv2.resize(gray, (int(width * scale), int(height * scale))) if scale < 1 else gray
    binary = cv2.threshold(cv2.bitwise_not(small), 0, 255, cv2.THRESH_BINARY | cv2.THRESH_OTSU)[1]
    centre = (binary.shape[1] / 2, binary.shape[0] / 2)

    def score(angle):
        matrix = cv2.getRotationMatrix2D(centre, angle, 1.0)
        rotated = cv2.warpAffine(binary, matrix, (binary.shape[1], binary.shape[0]), flags=cv2.INTER_NEAREST)

        return float(np.var(rotated.sum(axis=1)))

    coarse = max(range(-8, 9, 2), key=score)
    fine = max([coarse + step / 2 for step in range(-3, 4)], key=score)

    return fine


def prepare_variants(gray, cv2, np):
    height, width = gray.shape
    longest = max(height, width)
    if longest < 1600:
        factor = 2000.0 / longest
        gray = cv2.resize(gray, None, fx=factor, fy=factor, interpolation=cv2.INTER_CUBIC)
    elif longest > 3600:
        factor = 3000.0 / longest
        gray = cv2.resize(gray, None, fx=factor, fy=factor, interpolation=cv2.INTER_AREA)

    angle = estimate_skew(gray, cv2, np)
    if abs(angle) >= 0.5:
        matrix = cv2.getRotationMatrix2D((gray.shape[1] / 2, gray.shape[0] / 2), angle, 1.0)
        gray = cv2.warpAffine(gray, matrix, (gray.shape[1], gray.shape[0]), flags=cv2.INTER_CUBIC, borderValue=255)

    enhanced = cv2.createCLAHE(clipLimit=2.0, tileGridSize=(8, 8)).apply(gray)
    binary = cv2.adaptiveThreshold(
        cv2.medianBlur(enhanced, 3), 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C, cv2.THRESH_BINARY, 31, 15
    )

    return [enhanced, binary]


def assess_quality(gray, cv2, np):
    issues = []
    height, width = gray.shape
    if min(height, width) < 600:
        issues.append("The image is very small. Please upload a higher resolution photo or scan.")

    scale = 1200.0 / max(height, width)
    sample = cv2.resize(gray, None, fx=scale, fy=scale) if scale < 1 else gray
    if cv2.Laplacian(sample, cv2.CV_64F).var() < 60:
        issues.append("The image looks blurry. Please hold the camera steady and retake the photo.")
    if float(np.mean(sample)) < 70:
        issues.append("The image is too dark. Please retake it in better light.")

    return issues


def score_ocr(result):
    return result[1] * min(result[2], 60)


def extract_image(path):
    empty = {"text": "", "confidence": None, "words": 0, "engine": None, "issues": []}

    try:
        gray, cv2 = load_gray(path)
        import numpy as np
    except Exception:
        return empty

    issues = assess_quality(gray, cv2, np)
    if not shutil.which("tesseract"):
        return {**empty, "issues": issues}

    best = None
    with tempfile.TemporaryDirectory(prefix="govassist-ocr-") as directory:
        for rotation in (0, 1, 2, 3):
            oriented = np.rot90(gray, rotation).copy() if rotation else gray
            for index, variant in enumerate(prepare_variants(oriented, cv2, np)):
                file_path = os.path.join(directory, f"r{rotation}v{index}.png")
                cv2.imwrite(file_path, variant)
                result = run_tesseract(file_path)
                if result and (best is None or score_ocr(result) > score_ocr(best)):
                    best = result
            if best and best[1] >= MIN_CONFIDENT_OCR and best[2] >= MIN_READABLE_WORDS:
                break

    if best is None:
        return {**empty, "issues": issues}

    return {"text": best[0], "confidence": best[1], "words": best[2], "engine": "tesseract", "issues": issues}


def extract_any(path):
    extension = os.path.splitext(path)[1].lower()
    plain = lambda text, engine: {  # noqa: E731
        "text": text, "confidence": 100.0 if text.strip() else None,
        "words": len(text.split()), "engine": engine if text.strip() else None, "issues": [],
    }

    if extension == ".pdf":
        return extract_pdf(path)
    if extension == ".docx":
        return plain(extract_docx(path), "docx_text")
    if extension == ".doc":
        return plain(extract_doc(path), "doc_text")
    if extension in (".png", ".jpg", ".jpeg", ".webp", ".bmp", ".tif", ".tiff"):
        return extract_image(path)

    return {"text": "", "confidence": None, "words": 0, "engine": None, "issues": []}


# --------------------------------------------------------------------------
# Decision
# --------------------------------------------------------------------------

def outcome(status, message, **extra):
    base = {
        "status": status,
        "match": status == "verified",
        "score": 0.0,
        "method": None,
        "confidence": None,
        "found": [],
        "missing": [],
        "issues": [],
        "note": message,
        "message": message,
    }
    base.update(extra)

    return base


def review(document_path, template_path=None, keywords=None):
    if not os.path.exists(document_path):
        return outcome("review", "The uploaded document could not be read. A facilitator will check it.", method="missing_document")

    extraction = extract_any(document_path)
    text, words, confidence = extraction["text"], extraction["words"], extraction["confidence"]
    method = extraction["engine"]
    issues = list(extraction["issues"])
    wanted = split_keywords(keywords)

    if words < 3:
        if method is None and not shutil.which("tesseract") and os.path.splitext(document_path)[1].lower() not in (".docx", ".doc"):
            message = "Automatic reading is not available on this server, so a facilitator will check the document."
        else:
            message = "We could not read any text on the document. A facilitator will check it; a clearer copy helps."
        return outcome("review", message, method=method or "unreadable", issues=issues, missing=wanted)

    cleaned = clean_text(text)
    document_words = set(cleaned.split())
    found = [k for k in wanted if keyword_found(k, cleaned, document_words)]
    missing = [k for k in wanted if k not in found]

    template_score = None
    if template_path and os.path.exists(template_path):
        template_text = extract_any(template_path)["text"]
        template_score = template_overlap(template_text, text)

    coverage = len(found) / len(wanted) if wanted else None
    readable = words >= MIN_READABLE_WORDS and (confidence is None or confidence >= MIN_CONFIDENT_OCR)
    score = coverage if coverage is not None else (template_score or 0.0)
    details = {
        "method": method, "confidence": confidence, "found": found, "missing": missing,
        "issues": issues, "score": round(score, 3), "template_score": template_score,
        "text_snippet": text[:200],
    }

    if wanted:
        if not missing:
            return outcome("verified", "Document verified successfully.", **details)
        if coverage >= 0.5 or not readable:
            return outcome(
                "review",
                "Part of the document could not be matched automatically. A facilitator will check it.",
                **details,
            )

        return outcome(
            "mismatch",
            "This does not look like the right document. We could not find: " + ", ".join(missing) + ".",
            **details,
        )

    if template_score is not None:
        if template_score >= 0.6:
            return outcome("verified", "Document matches the template.", **details)
        if template_score >= 0.3 or not readable:
            return outcome("review", "The document only partly matches the template. A facilitator will check it.", **details)

        return outcome("mismatch", "This does not look like the requested form. Please upload the correct document.", **details)

    return outcome("review", "No checking rules are configured for this document, so a facilitator will check it.", **details)


# --------------------------------------------------------------------------
# Command line
# --------------------------------------------------------------------------

if __name__ == "__main__":
    arguments = sys.argv[1:]

    if len(arguments) >= 2 and arguments[0] == "text":
        print(json.dumps(extract_any(arguments[1])))
    elif len(arguments) >= 2 and arguments[0] == "review":
        document = arguments[1]
        template = arguments[2] if len(arguments) > 2 and arguments[2] else None
        keywords = arguments[3] if len(arguments) > 3 else None
        print(json.dumps(review(document, template, keywords)))
    else:
        print(json.dumps(outcome("review", "Missing arguments.", method="usage")))
        sys.exit(1)
