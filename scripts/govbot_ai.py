import sys
import json
import argparse
import warnings
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

# Suppress warnings from scikit-learn
warnings.filterwarnings("ignore")

# Knowledge Base of Intents
# Each intent has examples of how a user might ask about it
KNOWLEDGE_BASE = {
    "educational": {
        "examples": [
            "I need financial help for school",
            "How can I get a scholarship?",
            "My son needs tuition fee assistance",
            "What are the requirements for educational subsidy?",
            "Can you help me pay for college enrollment?",
            "school funding needed",
            "edukasyon subsidy allowance eskwelahan matrikula scholarship study"
        ],
        "response_en": "The **Educational Assistance** program provides financial aid and scholarships for students. The requirements are: School ID, Certificate of Enrollment, and Barangay Certificate of Indigency.",
        "response_ceb": "Ang **Tabang sa Edukasyon** naghatag og pinansyal nga tabang ug scholarship alang sa mga estudyante. Ang mga gikinahanglan mao ang: School ID, Sertipiko sa Pagpa-enrol, ug Sertipiko sa Kakabus sa Barangay."
    },
    "medical": {
        "examples": [
            "I am sick and need money for hospital",
            "My mother needs maintenance medicine",
            "How to apply for health assistance?",
            "I have a huge hospital bill",
            "Where can I ask for medical help?",
            "prescription drugs health hospital confinement",
            "medikal ospital hospital tambal medicine ill sick"
        ],
        "response_en": "The **Medical Assistance** program helps cover hospital bills and medicine. The requirements are: Medical Certificate, Hospital Bill/Quotation, and Barangay Certificate of Indigency.",
        "response_ceb": "Ang **Tabang sa Medikal** naglakip sa pagtabon sa mga gasto sa ospital ug tambal. Gikinahanglan ang: Sertipiko sa Medikal, Hospital Bill/Quotation, ug Sertipiko sa Kakabus sa Barangay."
    },
    "burial": {
        "examples": [
            "My father passed away, we need help with the funeral",
            "Is there assistance for burial expenses?",
            "A relative died and we can't afford the coffin",
            "Death in the family need help",
            "financial aid for deceased person",
            "burial funeral deceased death",
            "palubong patay namatay lubong"
        ],
        "response_en": "The **Burial Assistance** program covers funeral and burial costs of a deceased relative. The requirements are: Registered Death Certificate, Funeral Contract, and Barangay Certificate of Indigency.",
        "response_ceb": "Ang **Tabang sa Pagpalubong** nagtabon sa gasto sa punerarya ug pagpalubong sa namatay nga paryente. Gikinahanglan ang: Rehistradong Death Certificate, Kontrata sa Punerarya, ug Sertipiko sa Kakabus sa Barangay."
    },
    "transportation": {
        "examples": [
            "I need a ticket to go back to my province",
            "Can you help me with travel fare?",
            "Balik probinsya program",
            "I don't have money for transport",
            "bus ticket assistance fare",
            "transport biyahe travel transportasyon ticket fare"
        ],
        "response_en": "The **Transportation Assistance** program provides travel support for medical emergencies or job placement. The requirements are: Referral Letter/Endorsement and a Valid ID.",
        "response_ceb": "Ang **Tabang sa Transportasyon** naghatag og suporta sa pagbiyahe alang sa medikal nga emerhensya o trabaho. Gikinahanglan ang: Sulat sa Referral/Endorsement ug Balido nga ID."
    },
    "employment": {
        "examples": [
            "I am looking for a job",
            "Is there any livelihood program?",
            "I want to learn new skills to get hired",
            "I need work",
            "unemployed needs assistance",
            "employment job livelihood skill work hire",
            "trabaho"
        ],
        "response_en": "The **Employment Assistance** program provides livelihood support and skills training. The requirements are: PSA Birth Certificate and Resume.",
        "response_ceb": "Ang **Tabang sa Trabaho** naghatag og suporta sa panginabuhian ug pagbansay sa kahanas. Gikinahanglan ang: PSA Birth Certificate ug Resume."
    },
    "requirements": {
        "examples": [
            "What documents do I need to bring?",
            "What are the general requirements?",
            "Do I need an ID?",
            "checklist of files to prepare",
            "kinahanglan document dokumento checklist"
        ],
        "response_en": "The required documents depend on the selected program. Generally, you need a **Barangay Certificate of Indigency** and a **Valid ID**.",
        "response_ceb": "Ang mga gikinahanglang dokumento nagdepende sa serbisyo. Sa kinatibuk-an, magkinahanglan ka og **Sertipiko sa Kakabus (Indigency)** ug **Balido nga ID** sa pamilya."
    },
    "apply": {
        "examples": [
            "How do I submit an application?",
            "What is the process to apply?",
            "I want to apply now",
            "how to apply unsaon pag-apply proseso procedure submit"
        ],
        "response_en": "To apply, please follow these steps:\n1. Go to the **Eligibility** tab.\n2. Start the **Eligibility Assessment** for the desired service.\n3. If you qualify, generate the **Requirements Checklist**.\n4. Upload the required documents and click **Submit Application**.",
        "response_ceb": "Aron mo-apply, sunda kini:\n1. Adto sa tab nga **Kwalipikasyon**.\n2. Sugdi ang **Pagsusi sa Kwalipikasyon** alang sa napili nga serbisyo.\n3. Kung kwalipikado ka, ipatungha ang **Checklist sa mga Kinahanglanon**.\n4. I-upload ang mga dokumento ug i-click ang **Isumite ang Aplikasyon**."
    },
    "about_system": {
        "examples": [
            "What is this system about?",
            "What is GovAssist?",
            "How does this system help?",
            "What can I do here?",
            "about the system details purpose",
            "unsa ni nga sistema",
            "para unsa ni"
        ],
        "response_en": "The system is an online portal for government social services. It helps citizens easily apply for assistance programs, check eligibility, upload documents, and ask inquiries online.",
        "response_ceb": "Ang sistema usa ka online portal alang sa mga serbisyo sosyal sa gobyerno. Nagtabang kini sa mga lungsoranon nga dali makapangayo og tabang, magsusi kung kwalipikado, mag-upload og mga dokumento, ug mangutana online."
    },
    "programs_offered": {
        "examples": [
            "What programs are offered?",
            "List of services",
            "What assistance can I get?",
            "What do you offer?",
            "programs services assistance list",
            "unsa nga mga programa",
            "unsa ang mga tabang"
        ],
        "response_en": "We offer the following programs:\n1. Educational Assistance\n2. Medical Assistance\n3. Burial Assistance\n4. Transportation Assistance\n5. Employment and Livelihood Assistance.",
        "response_ceb": "Kini ang among mga programa:\n1. Tabang sa Edukasyon\n2. Tabang sa Medikal\n3. Tabang sa Pagpalubong\n4. Tabang sa Transportasyon\n5. Tabang sa Trabaho ug Panginabuhian."
    },
    "office_info": {
        "examples": [
            "Where is your office located?",
            "Who is the current head?",
            "Office address",
            "Where can I find you?",
            "location office head contact where",
            "asa ang opisina",
            "kinsa ang head"
        ],
        "response_en": "Our main office is located at **City Hall, Ground Floor, Social Services Office**. The current head of the office is **Lanny M Cagatin**.",
        "response_ceb": "Ang among opisina nahimutang sa **City Hall, Ground Floor, Social Services Office**. Ang kasamtangang pangulo sa opisina mao si **Lanny M Cagatin**."
    },
    "get_requirements": {
        "examples": [
            "Where can I get the requirements?",
            "How to get barangay clearance?",
            "Where to ask for indigency?",
            "where to get obtain requirements certificate",
            "asa mukuha ug requirements",
            "asa mangayo"
        ],
        "response_en": "Most requirements like the **Certificate of Indigency** or **Clearance** can be obtained from your local **Barangay Hall**. Medical certificates can be requested from the **City Health Office** or your attending physician.",
        "response_ceb": "Ang kasagarang mga kinahanglanon sama sa **Certificate of Indigency** makuha sa inyong **Barangay Hall**. Ang mga medical certificate mahimong pangayoon sa **City Health Office** o sa inyong doktor."
    },
    "online_assistance": {
        "examples": [
            "Where can I ask the admin?",
            "I need online assistance",
            "How to contact admin?",
            "Where to chat?",
            "online assistance ask admin contact help message",
            "unsaon pag chat sa admin",
            "mangutana sa admin"
        ],
        "response_en": "For online assistance from an administrator, you can submit an **Inquiry** by navigating to the **Inquiries** tab on your dashboard. An admin or facilitator will respond to your message there.",
        "response_ceb": "Alang sa online nga tabang gikan sa admin, mahimo ka magpadala og **Inquiry** o pangutana pinaagi sa pag-adto sa **Inquiries** nga tab sa imong dashboard. Motubag ang admin o facilitator sa imong mensahe didto."
    }
}

def analyze_intent(user_message, language="en"):
    corpus = []
    labels = []
    
    # Flatten the knowledge base into a corpus of sentences and their corresponding intent labels
    for intent, data in KNOWLEDGE_BASE.items():
        for example in data["examples"]:
            corpus.append(example)
            labels.append(intent)
            
    # Add the user's message to the end of the corpus
    corpus.append(user_message)
    
    # Vectorize the text using TF-IDF
    vectorizer = TfidfVectorizer(stop_words='english')
    try:
        tfidf_matrix = vectorizer.fit_transform(corpus)
    except Exception as e:
        return {"error": str(e), "confidence": 0}
        
    # The last vector is the user's message
    user_vector = tfidf_matrix[-1]
    
    # Calculate cosine similarity between the user message and all examples
    similarities = cosine_similarity(user_vector, tfidf_matrix[:-1]).flatten()
    
    # Find the best matching example
    best_match_idx = similarities.argmax()
    best_score = similarities[best_match_idx]
    
    # If the score is too low, we don't have a good answer
    if best_score < 0.15:
        fallback_en = "I'm sorry, I didn't quite understand your query. You can ask about our programs: **Educational, Medical, Burial, Transportation, or Employment** assistance, and their required documents."
        fallback_ceb = "Pasayloa, wala ko kasabot sa imong pangutana. Mahimo ka mangutana bahin sa mga programa sa: **Edukasyon, Medikal, Pagpalubong, Transportasyon, o Trabaho**, ug ang ilang mga kinahanglanon."
        
        return {
            "intent": "unknown",
            "confidence": float(best_score),
            "response": fallback_ceb if language == "ceb" else fallback_en
        }
        
    # Get the winning intent
    best_intent = labels[best_match_idx]
    
    # Get the corresponding response
    response_key = f"response_{language}"
    response = KNOWLEDGE_BASE[best_intent].get(response_key, KNOWLEDGE_BASE[best_intent]["response_en"])
    
    return {
        "intent": best_intent,
        "confidence": float(best_score),
        "response": response
    }

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="GovBot AI Analyzer")
    parser.add_argument("--message", type=str, required=True, help="The user's message")
    parser.add_argument("--lang", type=str, default="en", help="Language code (en or ceb)")
    
    args = parser.parse_args()
    
    result = analyze_intent(args.message, args.lang)
    print(json.dumps(result))
