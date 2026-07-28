import sys
import json
import os
import re
import subprocess
import tempfile
import unicodedata
import zipfile
import xml.etree.ElementTree as ET
from difflib import SequenceMatcher

def extract_text_from_pdf(pdf_path):
    try:
        import pypdf
        reader = pypdf.PdfReader(pdf_path)
        text = ""
        for page in reader.pages:
            t = page.extract_text()
            if t:
                text += t + " "
        if text.strip():
            return text.strip()
    except Exception:
        pass

    try:
        import fitz
        import pytesseract
        from PIL import Image
        from io import BytesIO

        text = ""
        with fitz.open(pdf_path) as document:
            for page in document:
                pixmap = page.get_pixmap(matrix=fitz.Matrix(2, 2))
                image = Image.open(BytesIO(pixmap.tobytes("png")))
                text += pytesseract.image_to_string(image) + " "

        return text.strip()
    except Exception:
        pass

    try:
        import pytesseract
        from PIL import Image

        with tempfile.TemporaryDirectory(prefix="govassist-pdf-ocr-") as temporary_directory:
            output_prefix = os.path.join(temporary_directory, "page")
            subprocess.run(
                ["pdftoppm", "-png", "-r", "200", pdf_path, output_prefix],
                check=True,
                capture_output=True,
                timeout=90,
            )

            text = ""
            page_paths = sorted(
                os.path.join(temporary_directory, filename)
                for filename in os.listdir(temporary_directory)
                if filename.endswith(".png")
            )

            for page_path in page_paths:
                with Image.open(page_path) as image:
                    text += pytesseract.image_to_string(image) + " "

            return text.strip()
    except Exception:
        return ""

def extract_text_from_docx(docx_path):
    try:
        with zipfile.ZipFile(docx_path) as docx:
            xml_content = docx.read('word/document.xml')
            root = ET.fromstring(xml_content)
            
            # Namespace for Word document elements
            ns = {'w': 'http://schemas.openxmlformats.org/wordprocessingml/2006/main'}
            
            # Extract all text elements (<w:t>)
            texts = []
            for elem in root.findall('.//w:t', ns):
                if elem.text:
                    texts.append(elem.text)
                    
            return " ".join(texts)
    except Exception:
        return ""

def extract_text_from_doc(doc_path):
    try:
        result = subprocess.run(
            ["antiword", doc_path],
            check=True,
            capture_output=True,
            text=True,
            timeout=30,
        )

        return result.stdout.strip()
    except Exception:
        return ""

def extract_text_from_image(image_path):
    try:
        from paddleocr import PaddleOCR
        ocr = PaddleOCR(use_angle_cls=True, lang='en', show_log=False)
        result = ocr.ocr(image_path, cls=True)
        if result:
            text_lines = []
            for res in result:
                if res:
                    for line in res:
                        if line and len(line) > 1 and line[1]:
                            text_lines.append(line[1][0])
            if text_lines:
                return " ".join(text_lines)
    except Exception:
        pass

    try:
        import pytesseract
        from PIL import Image
        img = Image.open(image_path)

        text = ""
        # Try 0, 90, 180, 270 degrees to catch all orientations
        for angle in [0, 90, 180, 270]:
            rotated_img = img.rotate(angle, expand=True)
            text += pytesseract.image_to_string(rotated_img) + " "

        return text
    except Exception:
        return ""

def clean_text(text):
    if not text:
        return ""

    normalized = unicodedata.normalize("NFKD", text).lower()
    normalized = "".join(character for character in normalized if not unicodedata.combining(character))
    normalized = re.sub(r"[^a-z0-9]+", " ", normalized)

    return " ".join(normalized.split())

def word_matches(keyword_word, document_words):
    if keyword_word in document_words:
        return True

    if len(keyword_word) < 4:
        return False

    return any(
        SequenceMatcher(
            None,
            keyword_word.replace("rn", "m").replace("1", "l"),
            document_word.replace("rn", "m").replace("1", "l"),
        ).ratio() >= 0.80
        for document_word in document_words
        if abs(len(keyword_word) - len(document_word)) <= 2
    )

def search_keywords(text, keywords_str):
    if not text or not keywords_str:
        return False
        
    cleaned_text = clean_text(text)
    document_words = set(cleaned_text.split())
    keywords = list(dict.fromkeys(
        clean_text(keyword)
        for keyword in keywords_str.split(',')
        if clean_text(keyword)
    ))

    if not keywords:
        return False

    for kw in keywords:
        if f" {kw} " in f" {cleaned_text} ":
            continue

        keyword_words = kw.split()
        important_words = [word for word in keyword_words if len(word) > 3] or keyword_words
        if not all(
            word_matches(word, document_words)
            for word in important_words
        ):
            return False

    return True

def extract_document_text(path):
    extension = os.path.splitext(path)[1].lower()

    if extension == '.pdf':
        return extract_text_from_pdf(path), "pdf_text"
    if extension == '.docx':
        return extract_text_from_docx(path), "docx_text"
    if extension == '.doc':
        return extract_text_from_doc(path), "doc_text"
    if extension in ['.png', '.jpg', '.jpeg']:
        return extract_text_from_image(path), "ocr_image"

    return "", "unsupported"

def compare_images(image_path1, image_path2, keywords=None):
    if not os.path.exists(image_path1):
        return {"match": False, "score": 0.0, "method": "missing_document", "note": "The uploaded document could not be read."}

    if not os.path.exists(image_path2):
        return {"match": False, "score": 0.0, "method": "missing_template", "note": "The administrator template could not be read."}

    extracted_text, method = extract_document_text(image_path1)
    keywords_match = search_keywords(extracted_text, keywords)

    if extracted_text:
        return {
            "match": keywords_match,
            "score": 1.0 if keywords_match else 0.0,
            "method": f"{method}_keywords",
            "text_snippet": extracted_text[:150],
            "note": "Document verified successfully." if keywords_match else "The document is missing one or more required verification keywords.",
        }

    return {
        "match": False,
        "score": 0.0,
        "method": method,
        "note": "OCR could not read the document text. Please upload a clearer copy.",
    }

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print(json.dumps({"error": "Missing image paths", "match": False, "score": 0}))
        sys.exit(1)
        
    path1 = sys.argv[1]
    path2 = sys.argv[2]
    keywords = sys.argv[3] if len(sys.argv) > 3 else None
    
    result = compare_images(path1, path2, keywords)
    print(json.dumps(result))
