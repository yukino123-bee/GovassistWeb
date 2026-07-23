<?php

namespace Database\Seeders;

use App\Models\EligibilityQuestion;
use App\Models\GovernmentService;
use Illuminate\Database\Seeder;

class EligibilityQuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $med = GovernmentService::where('service_name', 'like', 'Medical Assistance%')->first();
        $burial = GovernmentService::where('service_name', 'like', 'Burial Assistance%')->first();
        $trans = GovernmentService::where('service_name', 'like', 'Transportation%')->first();
        $emp = GovernmentService::where('service_name', 'like', 'Employment%')->first();

        if (! $med || ! $burial || ! $trans || ! $emp) {
            throw new \Exception('One or more required Government Services are missing in the database.');
        }

        // Delete existing questions for these services to allow clean re-seeding
        EligibilityQuestion::whereIn('service_id', [$med->id, $burial->id, $trans->id, $emp->id])->delete();

        // --- MEDICAL ASSISTANCE ---
        $medicalQuestions = [
            [
                'question_text_en' => 'Is there an active medical case, prescription, or hospital admission for the patient?',
                'question_text_ceb' => 'Aduna ba\'y aktibo nga kaso sa medikal, reseta, o pagkasulod sa ospital ang pasyente?',
                'question_text_fil' => 'Mayroon bang aktibong kasong medikal, reseta, o pagkaka-admit sa ospital ang pasyente?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Does the patient reside in this municipality?',
                'question_text_ceb' => 'Nagpuyo ba ang pasyente niini nga lungsod?',
                'question_text_fil' => 'Ang pasyente ba ay naninirahan sa munisipalidad na ito?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is there a valid medical certificate or clinical abstract issued within the last 3 months?',
                'question_text_ceb' => 'Aduna ba\'y balido nga medical certificate o clinical abstract nga gi-isyu sulod sa miaging 3 ka bulan?',
                'question_text_fil' => 'Mayroon bang balidong medical certificate o clinical abstract na ibinigay sa nakalipas na 3 buwan?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is the patient a Filipino resident?',
                'question_text_ceb' => 'Usa ba ka lungsoranon sa Pilipinas ang pasyente?',
                'question_text_fil' => 'Ang pasyente ba ay isang mamamayang Pilipino?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Does the family\'s monthly household income fall below 15,000 PHP?',
                'question_text_ceb' => 'Ang binuwan nga kita ba sa panimalay sa pamilya ubos sa 15,000 PHP?',
                'question_text_fil' => 'Ang buwanang kita ba ng pamilya ay mababa sa 15,000 PHP?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Can a valid Barangay Certificate of Indigency be provided for the patient\'s family?',
                'question_text_ceb' => 'Makahatag ba og balido nga Barangay Certificate of Indigency alang sa pamilya sa pasyente?',
                'question_text_fil' => 'Makakapagbigay ba ng balidong Barangay Certificate of Indigency para sa pamilya ng pasyente?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is there a doctor\'s prescription or hospital bill showing the need for financial aid?',
                'question_text_ceb' => 'Aduna ba\'y reseta sa doktor o bayranan sa ospital nga nagpakita sa panginahanglan og pinansyal nga tabang?',
                'question_text_fil' => 'Mayroon bang reseta ng doktor o hospital bill na nagpapakita ng pangangailangan para sa tulong pinansyal?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is the patient or the family representative a registered voter of this municipality?',
                'question_text_ceb' => 'Ang pasyente ba o ang representante sa pamilya usa ka rehistradong botante niini nga lungsod?',
                'question_text_fil' => 'Ang pasyente ba o ang kinatawan ng pamilya ay rehistradong botante sa munisipalidad na ito?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is the required treatment or medicine unavailable at the local government health center?',
                'question_text_ceb' => 'Ang gikinahanglan ba nga pagtambal o tambal dili magamit sa health center sa lokal nga kagamhanan?',
                'question_text_fil' => 'Ang kailangang gamot o paggamot ba ay hindi magagamit sa local government health center?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is there a valid government-issued ID of the patient or their immediate family representative?',
                'question_text_ceb' => 'Aduna ba\'y balido nga ID nga gi-isyu sa gobyerno ang pasyente o ang ilang representante sa pamilya?',
                'question_text_fil' => 'Mayroon bang balidong ID na ibinigay ng gobyerno ang pasyente o ang kanilang kinatawan ng pamilya?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Has the patient already received full coverage or full reimbursement from other sources for this case?',
                'question_text_ceb' => 'Nakadawat na ba ang pasyente og tibuok nga bayad gikan sa ubang mga tinubdan alang niini?',
                'question_text_fil' => 'Nakatanggap na ba ang pasyente ng buong kabayaran mula sa ibang mga mapagkukunan para dito?',
                'type' => 'boolean',
                'expected_value' => 'false',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is the treatment plan or laboratory request signed by a licensed physician?',
                'question_text_ceb' => 'Ang plano ba sa pagtambal o hangyo sa laboratoryo gipirmahan sa usa ka lisensyadong doktor?',
                'question_text_fil' => 'Ang plano ng paggamot o kahilingan sa laboratoryo ba ay nilagdaan ng isang lisensyadong doktor?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is the patient\'s family case assessment certified by a municipal social worker (MSWD)?',
                'question_text_ceb' => 'Ang case assessment ba sa pamilya sa pasyente sertipikado sa usa ka municipal social worker (MSWD)?',
                'question_text_fil' => 'Ang case assessment ba ng pamilya ng pasyente ay sertipikado ng isang municipal social worker (MSWD)?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is the patient currently hospitalized or undergoing active outpatient therapy?',
                'question_text_ceb' => 'Ang pasyente ba kasamtangang na-ospital o nagpaubos sa aktibong outpatient therapy?',
                'question_text_fil' => 'Ang pasyente ba ay kasalukuyang nakaratay sa ospital o sumasailalim sa aktibong outpatient therapy?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is there a formal referral letter from the Barangay Health Worker (BHW) or Barangay Captain?',
                'question_text_ceb' => 'Aduna ba\'y pormal nga sulat sa referral gikan sa Barangay Health Worker (BHW) o Kapitan sa Barangay?',
                'question_text_fil' => 'Mayroon bang pormal na referral letter mula sa Barangay Health Worker (BHW) o Kapitan ng Barangay?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
        ];

        foreach ($medicalQuestions as $q) {
            $q['service_id'] = $med->id;
            EligibilityQuestion::create($q);
        }

        // --- BURIAL ASSISTANCE ---
        $burialQuestions = [
            [
                'question_text_en' => 'Is there a registered death certificate of the deceased person?',
                'question_text_ceb' => 'Aduna ba\'y rehistradong death certificate sa namatay nga tawo?',
                'question_text_fil' => 'Mayroon bang nakarehistrong death certificate ng namatay na tao?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Was the deceased person a resident of this municipality?',
                'question_text_ceb' => 'Ang namatay ba nga tawo residente niini nga lungsod?',
                'question_text_fil' => 'Ang namatay na tao ba ay residente ng munisipalidad na ito?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is the family of the deceased listed as indigent or low-income in the barangay?',
                'question_text_ceb' => 'Ang pamilya ba sa namatay nalista isip kabus o ubos og kita sa barangay?',
                'question_text_fil' => 'Ang pamilya ba ng namatay ay nakalista bilang mahirap o may mababang kita sa barangay?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is there an official funeral contract or estimate invoice for the burial services?',
                'question_text_ceb' => 'Aduna ba\'y opisyal nga funeral contract o banabana nga invoice alang sa mga serbisyo sa paglubong?',
                'question_text_fil' => 'Mayroon bang opisyal na funeral contract o estimate invoice para sa mga serbisyo sa paglilibing?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is the representative applying for assistance an immediate family member of the deceased?',
                'question_text_ceb' => 'Ang representante ba nga nangayo og tabang usa ka suod nga sakop sa pamilya sa namatay?',
                'question_text_fil' => 'Ang kinatawan ba na humihingi ng tulong ay isang malapit na miyembro ng pamilya ng namatay?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Does the deceased\'s family have a monthly household income below 15,000 PHP?',
                'question_text_ceb' => 'Ang pamilya ba sa namatay adunay binuwan nga kita sa panimalay nga ubos sa 15,000 PHP?',
                'question_text_fil' => 'Ang pamilya ba ng namatay ay may buwanang kita ng pamilya na mababa sa 15,000 PHP?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is the deceased a Filipino resident?',
                'question_text_ceb' => 'Usa ba ka lungsoranon sa Pilipinas ang namatay?',
                'question_text_fil' => 'Ang namatay ba ay isang mamamayang Pilipino?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is there a valid government-issued ID of the immediate family representative?',
                'question_text_ceb' => 'Aduna ba\'y balido nga ID nga gi-isyu sa gobyerno ang suod nga representante sa pamilya?',
                'question_text_fil' => 'Mayroon bang balidong ID na ibinigay ng gobyerno ang malapit na kinatawan ng pamilya?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Has the family already received full burial coverage or assistance from private insurance for this death?',
                'question_text_ceb' => 'Nakadawat na ba ang pamilya og tibuok nga burial coverage o tabang gikan sa pribadong seguro alang niini nga kamatayon?',
                'question_text_fil' => 'Nakatanggap na ba ang pamilya ng buong burial coverage o tulong mula sa pribadong seguro para sa kamatayang ito?',
                'type' => 'boolean',
                'expected_value' => 'false',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is the deceased\'s body located within this municipality or being brought here for burial?',
                'question_text_ceb' => 'Ang patay nga lawas ba sa namatay anaa niini nga lungsod o gidala dinhi alang sa paglubong?',
                'question_text_fil' => 'Ang bangkay ba ng namatay ay nasa munisipalidad na ito o dinadala dito para sa paglilibing?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is there a Barangay Certificate of Residency for the deceased person?',
                'question_text_ceb' => 'Aduna ba\'y Barangay Certificate of Residency alang sa namatay nga tawo?',
                'question_text_fil' => 'Mayroon bang Barangay Certificate of Residency para sa namatay na tao?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is the representative of the deceased\'s family a registered voter in this municipality?',
                'question_text_ceb' => 'Ang representante ba sa pamilya sa namatay usa ka rehistradong botante niini nga lungsod?',
                'question_text_fil' => 'Ang kinatawan ba ng pamilya ng namatay ay isang rehistradong botante sa munisipalidad na ito?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is the death certified as not being due to any pending criminal investigation?',
                'question_text_ceb' => 'Ang kamatayon ba napamatud-an nga dili tungod sa bisan unsang nagpadayon nga imbestigasyon sa kriminal?',
                'question_text_fil' => 'Ang kamatayon ba ay napatunayang hindi dahil sa anumang nakabinbing kriminal na imbestigasyon?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is the application filed within 30 days from the date of death?',
                'question_text_ceb' => 'Ang aplikasyon ba gisumite sulod sa 30 ka adlaw gikan sa petsa sa kamatayon?',
                'question_text_fil' => 'Ang aplikasyon ba ay isinumite sa loob ng 30 araw mula sa petsa ng kamatayan?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is the burial permit issued by the local municipal health officer available?',
                'question_text_ceb' => 'Ang burial permit ba nga gi-isyu sa lokal nga municipal health officer magamit?',
                'question_text_fil' => 'Ang burial permit ba na ibinigay ng local municipal health officer ay magagamit?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
        ];

        foreach ($burialQuestions as $q) {
            $q['service_id'] = $burial->id;
            EligibilityQuestion::create($q);
        }

        // --- TRANSPORTATION ASSISTANCE ---
        $transQuestions = [
            [
                'question_text_en' => 'Are you currently stranded or in need of emergency transportation assistance?',
                'question_text_ceb' => 'Kasamtangan ba ikaw nga na-stranded o nanginahanglan og emergency nga tabang sa transportasyon?',
                'question_text_fil' => 'Kasalukuyan ka bang stranded o nangangailangan ng emergency na tulong sa transportasyon?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is the travel destination within the country?',
                'question_text_ceb' => 'Ang destinasyon ba sa biyahe sulod sa nasod?',
                'question_text_fil' => 'Ang destinasyon ba ng paglalakbay ay nasa loob ng bansa?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Do you have a valid referral letter, medical appointment, or court summon requiring this travel?',
                'question_text_ceb' => 'Aduna ba kay balido nga referral letter, appointment sa medikal, o court summon nga nagkinahanglan niini nga biyahe?',
                'question_text_fil' => 'Mayroon ka bang balidong referral letter, appointment sa medikal, o patawag ng korte na nangangailangan ng paglalakbay na ito?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Are you a registered resident of this municipality?',
                'question_text_ceb' => 'Rehistrado ka ba nga residente niini nga lungsod?',
                'question_text_fil' => 'Ikaw ba ay isang nakarehistrong residente ng munisipalidad na ito?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Do you have a valid government-issued ID?',
                'question_text_ceb' => 'Aduna ba kay balido nga ID nga gi-isyu sa gobyerno?',
                'question_text_fil' => 'Mayroon ka bang balidong ID na ibinigay ng gobyerno?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is your family\'s monthly household income below 15,000 PHP?',
                'question_text_ceb' => 'Ang binuwan nga kita ba sa inyong panimalay ubos sa 15,000 PHP?',
                'question_text_fil' => 'Ang buwanang kita ba ng inyong pamilya ay mababa sa 15,000 PHP?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Can you provide a Barangay Certificate of Indigency?',
                'question_text_ceb' => 'Makahatag ba ikaw og Barangay Certificate of Indigency?',
                'question_text_fil' => 'Maaari ka bang magbigay ng Barangay Certificate of Indigency?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Have you received transportation assistance from this office within the last 6 months?',
                'question_text_ceb' => 'Nakadawat ka ba og tabang sa transportasyon gikan niini nga opisina sulod sa miaging 6 ka bulan?',
                'question_text_fil' => 'Nakatanggap ka ba ng tulong sa transportasyon mula sa opisinang ito sa nakalipas na 6 na buwan?',
                'type' => 'boolean',
                'expected_value' => 'false',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is the primary purpose of travel for leisure or vacation?',
                'question_text_ceb' => 'Ang nag-unang katuyoan ba sa pagbiyahe alang sa kalingawan o bakasyon?',
                'question_text_fil' => 'Ang pangunahing layunin ba ng paglalakbay ay para sa libangan o bakasyon?',
                'type' => 'boolean',
                'expected_value' => 'false',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Do you have a booking quote, ticket price estimate, or travel itinerary?',
                'question_text_ceb' => 'Aduna ba kay booking quote, banabana sa presyo sa tiket, o itinerary sa biyahe?',
                'question_text_fil' => 'Mayroon ka bang booking quote, pagtatantya ng presyo ng tiket, o itinerary ng paglalakbay?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Are you traveling for employment-related relocation or job search?',
                'question_text_ceb' => 'Nagbiyahe ka ba alang sa pagbalhin nga may kalabutan sa trabaho o pagpangita og trabaho?',
                'question_text_fil' => 'Naglalakbay ka ba para sa paglipat na may kaugnayan sa trabaho o paghahanap ng trabaho?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is the beneficiary a minor traveling without an adult companion or DSWD travel clearance?',
                'question_text_ceb' => 'Ang benepisyaryo ba usa ka menor de edad nga nagbiyahe nga walay kauban nga hamtong o clearance sa pagbiyahe sa DSWD?',
                'question_text_fil' => 'Ang benepisyaryo ba ay isang menor de edad na naglalakbay nang walang kasamang matanda o DSWD travel clearance?',
                'type' => 'boolean',
                'expected_value' => 'false',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is the travel required due to a natural disaster or family emergency?',
                'question_text_ceb' => 'Ang pagbiyahe ba gikinahanglan tungod sa natural nga kalamidad o dinalian nga emergency sa pamilya?',
                'question_text_fil' => 'Ang paglalakbay ba ay kinakailangan dahil sa isang likas na kalamidad o emergency sa pamilya?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Are you currently receiving regular travel subsidies from other private sponsors or agencies?',
                'question_text_ceb' => 'Kasamtangan ka ba nga nakadawat og regular nga tabang sa pagbiyahe gikan sa ubang pribadong sponsor o ahensya?',
                'question_text_fil' => 'Kasalukuyan ka bang nakakatanggap ng regular na tulong sa paglalakbay mula sa ibang pribadong sponsor o ahensya?',
                'type' => 'boolean',
                'expected_value' => 'false',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is there a valid police report or certificate if you are a victim of theft or loss of travel funds?',
                'question_text_ceb' => 'Aduna ba\'y balido nga report sa pulisya o sertipiko kung biktima ka sa pangawat o pagkawala sa pundo sa pagbiyahe?',
                'question_text_fil' => 'Mayroon bang balidong police report o sertipiko kung ikaw ay biktima ng pagnanakaw o pagkawala ng pondo sa paglalakbay?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
        ];

        foreach ($transQuestions as $q) {
            $q['service_id'] = $trans->id;
            EligibilityQuestion::create($q);
        }

        // --- EMPLOYMENT ASSISTANCE ---
        $employmentQuestions = [
            [
                'question_text_en' => 'Are you at least 18 years of age?',
                'question_text_ceb' => 'Aduna ba kay 18 anyos o pataas ang edad?',
                'question_text_fil' => 'Ikaw ba ay may edad na 18 pataas?',
                'type' => 'number',
                'expected_value' => '18',
                'operator' => '>=',
            ],
            [
                'question_text_en' => 'Are you currently unemployed and actively looking for work?',
                'question_text_ceb' => 'Kasamtangan ba ka nga walay trabaho ug aktibong nangita og trabaho?',
                'question_text_fil' => 'Kasalukuyan ka bang walang trabaho at aktibong naghahanap ng trabaho?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Are you a registered resident of this municipality?',
                'question_text_ceb' => 'Rehistrado ka ba nga residente niini nga lungsod?',
                'question_text_fil' => 'Ikaw ba ay isang nakarehistrong residente ng munisipalidad na ito?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Do you have a valid government-issued ID?',
                'question_text_ceb' => 'Aduna ba kay balido nga ID nga gi-isyu sa gobyerno?',
                'question_text_fil' => 'Mayroon ka bang balidong ID na ibinigay ng gobyerno?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Is your family\'s monthly household income below 15,000 PHP?',
                'question_text_ceb' => 'Ang binuwan nga kita ba sa inyong panimalay ubos sa 15,000 PHP?',
                'question_text_fil' => 'Ang buwanang kita ba ng inyong pamilya ay mababa sa 15,000 PHP?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Can you provide a Barangay Certificate of Indigency?',
                'question_text_ceb' => 'Makahatag ba ikaw og Barangay Certificate of Indigency?',
                'question_text_fil' => 'Maaari ka bang magbigay ng Barangay Certificate of Indigency?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Are you currently enrolled as a full-time student?',
                'question_text_ceb' => 'Kasamtangan ka ba nga enrolled isip full-time nga estudyante?',
                'question_text_fil' => 'Kasalukuyan ka bang nakatala bilang isang full-time na mag-aaral?',
                'type' => 'boolean',
                'expected_value' => 'false',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Do you have a Barangay Clearance or Police Clearance without any active criminal records?',
                'question_text_ceb' => 'Aduna ba kay Barangay Clearance o Police Clearance nga walay aktibo nga rekord sa krimen?',
                'question_text_fil' => 'Mayroon ka bang Barangay Clearance o Police Clearance na walang aktibong rekord ng krimen?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Are you willing to undergo skills training or seminars conducted by the local government?',
                'question_text_ceb' => 'Andam ka ba nga mopaubos sa pagbansay sa mga kahanas o mga seminar nga gipahigayon sa lokal nga kagamhanan?',
                'question_text_fil' => 'Handa ka bang sumailalim sa pagsasanay sa kasanayan o mga seminar na isinasagawa ng lokal na pamahalaan?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Have you registered in the Public Employment Service Office (PESO) database?',
                'question_text_ceb' => 'Narehistro ka ba sa database sa Public Employment Service Office (PESO)?',
                'question_text_fil' => 'Nakarehistro ka ba sa database ng Public Employment Service Office (PESO)?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Are you currently receiving any regular pension or retirement benefits?',
                'question_text_ceb' => 'Kasamtangan ka ba nga nakadawat og regular nga pension o mga benepisyo sa pagretiro?',
                'question_text_fil' => 'Kasalukuyan ka bang nakakatanggap ng anumang regular na pensyon o mga benepisyo sa pagreretiro?',
                'type' => 'boolean',
                'expected_value' => 'false',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Do you have a copy of your Resume or Curriculum Vitae (CV)?',
                'question_text_ceb' => 'Aduna ba kay kopya sa imong Resume o Curriculum Vitae (CV)?',
                'question_text_fil' => 'Mayroon ka bang kopya ng iyong Resume o Curriculum Vitae (CV)?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Are you blacklisted or banned from working in any local government projects?',
                'question_text_ceb' => 'Nalakip ba ka sa blacklist o gidid-an sa pagtrabaho sa bisan unsang mga proyekto sa lokal nga kagamhanan?',
                'question_text_fil' => 'Naka-blacklist ka ba o pinagbawalang magtrabaho sa anumang mga proyekto ng lokal na pamahalaan?',
                'type' => 'boolean',
                'expected_value' => 'false',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Can you work on flexible hours or shifts as required by potential employers?',
                'question_text_ceb' => 'Makatrabaho ba ikaw sa flexible nga mga oras o mga shift sumala sa gikinahanglan sa mga potensyal nga amo?',
                'question_text_fil' => 'Maaari ka bang magtrabaho sa flexible na oras o shift ayon sa kinakailangan ng mga potensyal na employer?',
                'type' => 'boolean',
                'expected_value' => 'true',
                'operator' => '==',
            ],
            [
                'question_text_en' => 'Are you a recipient of any other active full-time livelihood grants from national government agencies?',
                'question_text_ceb' => 'Nakadawat ka ba og bisan unsang uban nga aktibo nga full-time livelihood grant gikan sa nasyonal nga mga ahensya sa gobyerno?',
                'question_text_fil' => 'Nakatanggap ka ba ng anumang iba pang aktibong full-time livelihood grant mula sa mga pambansang ahensya ng pamahalaan?',
                'type' => 'boolean',
                'expected_value' => 'false',
                'operator' => '==',
            ],
        ];

        foreach ($employmentQuestions as $q) {
            $q['service_id'] = $emp->id;
            EligibilityQuestion::create($q);
        }
    }
}
