import cv2
import sys
import json
import os
from skimage.metrics import structural_similarity as ssim

def compare_images(image_path1, image_path2):
    if not os.path.exists(image_path1):
        return {"error": f"File not found: {image_path1}", "match": False, "score": 0}
    if not os.path.exists(image_path2):
        return {"error": f"File not found: {image_path2}", "match": False, "score": 0}

    try:
        # Load the images in grayscale
        img1 = cv2.imread(image_path1, cv2.IMREAD_GRAYSCALE)
        img2 = cv2.imread(image_path2, cv2.IMREAD_GRAYSCALE)

        if img1 is None or img2 is None:
            return {"error": "Could not read one or both images.", "match": False, "score": 0}

        # Resize img2 to match img1 dimensions to compute SSIM
        img2 = cv2.resize(img2, (img1.shape[1], img1.shape[0]))

        # Compute SSIM
        score, _ = ssim(img1, img2, full=True)

        # Consider it a match if SSIM is above a threshold (e.g., 0.85)
        # 0.85 is relatively high for structural similarity
        threshold = 0.85
        is_match = score > threshold

        return {
            "match": bool(is_match),
            "score": float(score)
        }
    except Exception as e:
        return {"error": str(e), "match": False, "score": 0}

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print(json.dumps({"error": "Missing image paths", "match": False, "score": 0}))
        sys.exit(1)
        
    path1 = sys.argv[1]
    path2 = sys.argv[2]
    
    result = compare_images(path1, path2)
    print(json.dumps(result))
