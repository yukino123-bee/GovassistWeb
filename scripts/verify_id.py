import sys
import json
import os

def clean_text(text):
    if not text:
        return ""
    return " ".join(text.lower().split())

def verify_name_in_image(image_path, expected_name):
    try:
        import pytesseract
        from PIL import Image
        import cv2
        
        # Read with cv2, convert to grayscale, resize if too large
        img = cv2.imread(image_path)
        if img is None:
            return {"match": False, "error": "Could not read image"}
            
        # Resize to max 1500px width/height to avoid timeouts
        h, w = img.shape[:2]
        if max(h, w) > 1500:
            scale = 1500 / max(h, w)
            img = cv2.resize(img, (int(w * scale), int(h * scale)))
            
        gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
        
        # Preprocessing: thresholding to remove background noise (helpful for IDs)
        # Apply adaptive threshold
        gray = cv2.adaptiveThreshold(gray, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C, cv2.THRESH_BINARY, 11, 2)
        
        pil_img = Image.fromarray(gray)
        
        text = ""
        for angle in [0, 90, 180, 270]:
            rotated = pil_img.rotate(angle, expand=True)
            text += pytesseract.image_to_string(rotated) + " "
            
        cleaned_text = clean_text(text)
        name_clean = clean_text(expected_name)
        
        # If full name in text
        if name_clean in cleaned_text:
            return {"match": True, "method": "full_phrase"}
            
        # Check individual words
        words = name_clean.split()
        important_words = [w for w in words if len(w) > 2] # 3 chars or more like "jed"
        
        if not important_words:
            # If name is super short, just check if it's anywhere in text
            if name_clean in cleaned_text:
                return {"match": True, "method": "short_name"}
            return {"match": False, "error": "Name not found"}
            
        # If at least ONE important word is found, we accept it to be lenient with OCR on IDs
        found_words = [w for w in important_words if w in cleaned_text]
        if len(found_words) > 0:
            return {"match": True, "method": "partial_word", "found": found_words}
            
        return {"match": False, "error": "Name not found in ID text"}
        
    except Exception as e:
        return {"match": False, "error": str(e)}

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print(json.dumps({"match": False, "error": "Missing arguments"}))
        sys.exit(1)
        
    image_path = sys.argv[1]
    name = sys.argv[2]
    
    result = verify_name_in_image(image_path, name)
    print(json.dumps(result))
