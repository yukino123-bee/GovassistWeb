import sys
import json
import argparse
import warnings
from deep_translator import GoogleTranslator

warnings.filterwarnings("ignore")

def translate_text(text):
    try:
        # Translate to English (en)
        en_trans = GoogleTranslator(source='auto', target='en').translate(text)
        
        # Translate to Cebuano (ceb)
        ceb_trans = GoogleTranslator(source='auto', target='ceb').translate(text)
        
        # Translate to Filipino/Tagalog (tl)
        fil_trans = GoogleTranslator(source='auto', target='tl').translate(text)
        
        # For Subanen, fallback to Cebuano since standard APIs don't support it
        sub_trans = ceb_trans

        return {
            "en": en_trans,
            "ceb": ceb_trans,
            "fil": fil_trans,
            "sub": sub_trans
        }
    except Exception as e:
        return {"error": str(e)}

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="GovAssist Translator")
    parser.add_argument("--text", type=str, required=True, help="Text to translate")
    
    args = parser.parse_args()
    
    result = translate_text(args.text)
    print(json.dumps(result))
