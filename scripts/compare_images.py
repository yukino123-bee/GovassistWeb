import sys
import json
import os
import re
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
    return " ".join(text.lower().split())

def search_keywords(text, keywords_str):
    if not text or not keywords_str:
        return False
        
    cleaned_text = clean_text(text)
    keywords = [k.strip().lower() for k in keywords_str.split(',') if k.strip()]

    if not keywords:
        return False

    for kw in keywords:
        if kw in cleaned_text:
            continue
            
        words = [w.strip(".,;:?!()[]{}") for w in kw.split()]
        important_words = [w for w in words if len(w) > 3]
        if not important_words or not all(w in cleaned_text for w in important_words):
            return False

    return True

def extract_document_text(path):
    extension = os.path.splitext(path)[1].lower()

    if extension == '.pdf':
        return extract_text_from_pdf(path), "pdf_text"
    if extension in ['.docx', '.doc']:
        return extract_text_from_docx(path), "docx_text"
    if extension in ['.png', '.jpg', '.jpeg']:
        return extract_text_from_image(path), "ocr_image"

    return "", "unsupported"

def text_similarity(document_text, template_text):
    document_words = set(re.findall(r"[a-z0-9]{3,}", clean_text(document_text)))
    template_words = set(re.findall(r"[a-z0-9]{3,}", clean_text(template_text)))

    if not document_words or not template_words:
        return 0.0

    template_coverage = len(document_words & template_words) / len(template_words)
    sequence_score = SequenceMatcher(
        None,
        clean_text(template_text),
        clean_text(document_text),
    ).ratio()

    return max(template_coverage, sequence_score)

def compare_images(image_path1, image_path2, keywords=None):
    if not os.path.exists(image_path1):
        return {"match": False, "score": 0.0, "method": "missing_document", "note": "The uploaded document could not be read."}

    if not os.path.exists(image_path2):
        return {"match": False, "score": 0.0, "method": "missing_template", "note": "The administrator template could not be read."}

    ext1 = os.path.splitext(image_path1)[1].lower()
    extracted_text, method = extract_document_text(image_path1)
    template_text, _ = extract_document_text(image_path2)
    keywords_match = search_keywords(extracted_text, keywords)

    if extracted_text and template_text:
        score = text_similarity(extracted_text, template_text)
        matched = keywords_match and score >= 0.55
        return {
            "match": matched,
            "score": score,
            "method": f"{method}_template_and_keywords",
            "text_snippet": extracted_text[:150],
            "note": "Document verified successfully." if matched else "The document does not match the configured template or all required keywords.",
        }

    if ext1 in ['.png', '.jpg', '.jpeg']:
        try:
            import cv2
            from skimage.metrics import structural_similarity as ssim

            img1 = cv2.imread(image_path1, cv2.IMREAD_GRAYSCALE)
            img2 = cv2.imread(image_path2, cv2.IMREAD_GRAYSCALE)

            if img1 is not None and img2 is not None:
                img2 = cv2.resize(img2, (img1.shape[1], img1.shape[0]))
                score, _ = ssim(img1, img2, full=True)
                matched = keywords_match and score >= 0.55

                return {
                    "match": bool(matched),
                    "score": float(score),
                    "method": "visual_ssim_and_keywords",
                    "note": "Document verified successfully." if matched else "The document does not match the configured template or all required keywords.",
                }
        except Exception:
            pass

    return {
        "match": False,
        "score": 0.0,
        "method": method,
        "note": "OCR could not confirm that this document matches the configured template and all required keywords. Please upload a clear, correct document.",
    }

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print(json.dumps({"error": "Missing image paths", "match": False, "score": 0}))
        sys.exit(1)
        
    path1 = sys.argv[1]
    path2 = sys.argv[2]
    keywords = sys.argv[3] if len(sys.argv) > 3 else None
    
    result = compare_images(path1, path2, keywords)
    with open("/tmp/compare_images.log", "w") as f:
        f.write(json.dumps(result))
    print(json.dumps(result))
