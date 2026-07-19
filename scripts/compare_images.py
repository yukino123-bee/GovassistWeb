import sys
import json
import os
import zipfile
import xml.etree.ElementTree as ET

def extract_text_from_pdf(pdf_path):
    try:
        import pypdf
        reader = pypdf.PdfReader(pdf_path)
        text = ""
        for page in reader.pages:
            t = page.extract_text()
            if t:
                text += t + " "
        return text.strip()
    except Exception as e:
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
    except Exception as e:
        return ""

def extract_text_from_image(image_path):
    try:
        import pytesseract
        from PIL import Image
        img = Image.open(image_path)
        return pytesseract.image_to_string(img)
    except ImportError:
        return ""
    except Exception as e:
        return ""

def clean_text(text):
    if not text:
        return ""
    return " ".join(text.lower().split())

def search_keywords(text, keywords_str):
    if not text or not keywords_str:
        return False
        
    cleaned_text = clean_text(text)
    
    # Split keywords by commas and trim spaces
    keywords = [k.strip().lower() for k in keywords_str.split(',') if k.strip()]
    
    for kw in keywords:
        if not kw:
            continue
        # Check if the full phrase is in the text
        if kw in cleaned_text:
            return True
            
        # Also check individual important words of the phrase (length > 3)
        words = [w.strip(".,;:?!()[]{}") for w in kw.split()]
        important_words = [w for w in words if len(w) > 3]
        if important_words and all(w in cleaned_text for w in important_words):
            return True
            
    return False

def compare_images(image_path1, image_path2, keywords=None):
    if not os.path.exists(image_path1):
        return {"error": f"File not found: {image_path1}", "match": False, "score": 0}
        
    ext1 = os.path.splitext(image_path1)[1].lower()
    
    # 1. Try extracting text based on file format
    extracted_text = ""
    method = "unknown"
    
    if ext1 == '.pdf':
        extracted_text = extract_text_from_pdf(image_path1)
        method = "pdf_text"
    elif ext1 in ['.docx', '.doc']:
        extracted_text = extract_text_from_docx(image_path1)
        method = "docx_text"
    elif ext1 in ['.png', '.jpg', '.jpeg']:
        extracted_text = extract_text_from_image(image_path1)
        method = "ocr_image"
        
    # 2. Check if keyword match is successful
    if extracted_text and keywords:
        matched = search_keywords(extracted_text, keywords)
        if matched:
            return {
                "match": True,
                "score": 1.0,
                "method": f"{method}_keyword",
                "text_snippet": extracted_text[:150]
            }
            
    # 3. Fallback: If no text was extracted or keyword matching failed,
    # and both are images, perform standard visual SSIM comparison
    if ext1 in ['.png', '.jpg', '.jpeg']:
        if not os.path.exists(image_path2):
            return {"error": f"File not found: {image_path2}", "match": False, "score": 0}
            
        try:
            import cv2
            from skimage.metrics import structural_similarity as ssim
            
            img1 = cv2.imread(image_path1, cv2.IMREAD_GRAYSCALE)
            img2 = cv2.imread(image_path2, cv2.IMREAD_GRAYSCALE)

            if img1 is None or img2 is None:
                return {"error": "Could not read one or both images.", "match": False, "score": 0}

            # Resize template to match user document dimensions to compute SSIM
            img2 = cv2.resize(img2, (img1.shape[1], img1.shape[0]))

            score, _ = ssim(img1, img2, full=True)
            threshold = 0.85
            is_match = score > threshold

            return {
                "match": bool(is_match),
                "score": float(score),
                "method": "visual_ssim"
            }
        except Exception as e:
            return {"error": str(e), "match": False, "score": 0}
            
    # Default return when no matches are found
    return {
        "match": False,
        "score": 0.0,
        "method": method,
        "error": "No keywords matched and file format did not support image comparison."
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
