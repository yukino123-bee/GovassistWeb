<?php

namespace Database\Seeders;

use App\Models\AssessmentAnswer;
use App\Models\EligibilityAssessment;
use App\Models\EligibilityQuestion;
use App\Models\GovernmentService;
use App\Models\InquiryRequirense;
use App\Models\ServiceCategory;
use App\Models\ServiceRequirement;
use App\Models\ServiceTranslation;
use App\Models\User;
use App\Models\UserChecklist;
use App\Models\UserChecklistItem;
use App\Models\UserInquiry;
use App\Models\UserLanguage;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Users
        $facilitator = User::create([
            'name' => 'Lanny M Cagatin',
            'email' => 'jedcagat@gmail.com',
            'password' => Hash::make('manok123'),
            'role' => 'facilitator',
            'language' => 'en',
            'email_verified_at' => now(),
        ]);

        $citizen = User::create([
            'name' => 'Mark Cagatin',
            'email' => 'cagatinmark26@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'citizen',
            'language' => 'ceb', // Cebuano
            'dob' => '2005-03-11',
            'address' => 'Fatima, San Miguel, Zamboanga del Sur',
            'civil_status' => 'Single',
            'contact_number' => '09563559181',
            'avatar' => null,
            'email_verified_at' => now(),
        ]);

        $john = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role' => 'citizen',
            'language' => 'en',
            'dob' => '1998-05-15',
            'address' => 'Poblacion, San Miguel, Zamboanga del Sur',
            'civil_status' => 'Single',
            'contact_number' => '09123456789',
            'email_verified_at' => now(),
        ]);

        $jane = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'role' => 'citizen',
            'language' => 'en',
            'dob' => '1990-10-22',
            'address' => 'Dumalian, San Miguel, Zamboanga del Sur',
            'civil_status' => 'Married',
            'contact_number' => '09987654321',
            'email_verified_at' => now(),
        ]);

        // Seed User Languages
        UserLanguage::create(['user_id' => $facilitator->id, 'language_code' => 'en', 'is_default' => true]);
        UserLanguage::create(['user_id' => $citizen->id, 'language_code' => 'ceb', 'is_default' => true]);
        UserLanguage::create(['user_id' => $john->id, 'language_code' => 'en', 'is_default' => true]);
        UserLanguage::create(['user_id' => $jane->id, 'language_code' => 'en', 'is_default' => true]);

        // 2. Create Service Categories
        $catEdu = ServiceCategory::create([
            'category_name' => 'Educational Assistance',
            'description' => 'Scholarships, tuition support, and allowances.',
        ]);
        $catMed = ServiceCategory::create([
            'category_name' => 'Medical Assistance',
            'description' => 'Support for hospitalization, medicines, and surgical procedures.',
        ]);
        $catBurial = ServiceCategory::create([
            'category_name' => 'Burial Assistance',
            'description' => 'Financial aid for casket, funeral services, and burial costs.',
        ]);
        $catTrans = ServiceCategory::create([
            'category_name' => 'Transportation Assistance',
            'description' => 'Referrals and fare support for emergency displacement/travel.',
        ]);
        $catEmp = ServiceCategory::create([
            'category_name' => 'Employment Assistance',
            'description' => 'Skills training, livelihood support, and job matching.',
        ]);

        // 3. Create Government Services
        $edu = GovernmentService::create([
            'category_id' => $catEdu->id,
            'service_name' => 'Educational Assistance Program',
            'description' => 'Provides financial aid, scholarships, tuition support, and educational subsidies for underprivileged students.',
            'procedure' => "1. Submit required documents to the SSFO office.\n2. Complete the Eligibility Assessment.\n3. Wait for validation and approval of application.\n4. Claim financial assistance during payout scheduling.",
            'icon' => 'academic-cap',
        ]);

        $med = GovernmentService::create([
            'category_id' => $catMed->id,
            'service_name' => 'Medical Assistance Program',
            'description' => 'Provides financial assistance to eligible individuals to help cover medical expenses, including hospital bills, medicine, and treatments.',
            'procedure' => "1. Submit Hospital Bill or Medical Certificate.\n2. Undergo assessment by facilitator.\n3. Approved requests will receive guarantee letters or financial payouts.",
            'icon' => 'heart',
        ]);

        $burial = GovernmentService::create([
            'category_id' => $catBurial->id,
            'service_name' => 'Burial Assistance Program',
            'description' => 'Provides financial assistance to the family or authorized representative of a deceased individual to cover funeral and burial costs.',
            'procedure' => "1. Present Death Certificate and Funeral Contract.\n2. Fill out social case study report.\n3. Receive financial assistance for burial expenses.",
            'icon' => 'shield-exclamation',
        ]);

        $trans = GovernmentService::create([
            'category_id' => $catTrans->id,
            'service_name' => 'Transportation Assistance Program',
            'description' => 'Provides financial assistance to eligible individuals needing emergency travel support for medical, employment, or emergency displacement.',
            'procedure' => "1. Present travel referral or endorsement.\n2. Submit indigency certification.\n3. Receive travel allowance or tickets.",
            'icon' => 'truck',
        ]);

        $emp = GovernmentService::create([
            'category_id' => $catEmp->id,
            'service_name' => 'Employment Assistance',
            'description' => 'Provides assistance to job seekers, including livelihood support, skill training, and referral programs.',
            'procedure' => "1. Register in the employment database.\n2. Attend skills training workshops.\n3. Get matched with local government or private job placement offers.",
            'icon' => 'briefcase',
        ]);

        // 4. Create Service Translations (English, Cebuano, Filipino)
        // Educational
        ServiceTranslation::create([
            'service_id' => $edu->id,
            'language_code' => 'en',
            'service_name' => 'Educational Assistance Program',
            'description' => 'Provides financial aid, scholarships, tuition support, and educational subsidies for underprivileged students.',
            'procedure' => "1. Submit required documents to the SSFO office.\n2. Complete the Eligibility Assessment.\n3. Wait for validation and approval of application.\n4. Claim financial assistance during payout scheduling.",
        ]);
        ServiceTranslation::create([
            'service_id' => $edu->id,
            'language_code' => 'ceb',
            'service_name' => 'Tabang sa Edukasyon',
            'description' => 'Naghatag og pinansyal nga tabang, mga scholarship, suporta sa matrikula, ug mga subsidyo sa edukasyon alang sa mga nanginahanglan nga estudyante.',
            'procedure' => "1. Isumite ang gikinahanglan nga mga dokumento sa opisina sa SSFO.\n2. Kompletoha ang Eligibility Assessment.\n3. Paghulat sa pag-validate ug pag-apruba sa aplikasyon.\n4. I-claim ang pinansyal nga tabang sa panahon sa gieskedyul nga payout.",
        ]);
        ServiceTranslation::create([
            'service_id' => $edu->id,
            'language_code' => 'fil',
            'service_name' => 'Tulong sa Edukasyon',
            'description' => 'Nagbibigay ng pinansyal na tulong, iskolarsip, suporta sa matrikula, at edukasyonal na subsidyo para sa mga kapus-palad na mag-aaral.',
            'procedure' => "1. Isumite ang mga kinakailangang dokumento sa opisina ng SSFO.\n2. Kumpletuhin ang Eligibility Assessment.\n3. Maghintay para sa pagpapatunay at pag-apruba ng aplikasyon.\n4. Kunin ang tulong pinansyal sa nakatakdang oras.",
        ]);

        // Medical
        ServiceTranslation::create([
            'service_id' => $med->id,
            'language_code' => 'en',
            'service_name' => 'Medical Assistance Program',
            'description' => 'Provides financial assistance to eligible individuals to help cover medical expenses, including hospital bills, medicine, and treatments.',
            'procedure' => "1. Submit Hospital Bill or Medical Certificate.\n2. Undergo assessment by facilitator.\n3. Approved requests will receive guarantee letters or financial payouts.",
        ]);
        ServiceTranslation::create([
            'service_id' => $med->id,
            'language_code' => 'ceb',
            'service_name' => 'Tabang sa Medikal',
            'description' => 'Naghatag og pinansyal nga tabang sa mga kwalipikadong indibidwal aron matabangan ang pagtabon sa mga gasto sa medikal, lakip ang mga bayranan sa ospital, tambal, ug mga pagtambal.',
            'procedure' => "1. Isumite ang Hospital Bill o Medical Certificate.\n2. Moagi sa assessment sa facilitator.\n3. Ang giaprobahan nga mga hangyo makadawat og garantiya nga mga sulat o pinansyal nga payout.",
        ]);
        ServiceTranslation::create([
            'service_id' => $med->id,
            'language_code' => 'fil',
            'service_name' => 'Tulong Medikal',
            'description' => 'Nagbibigay ng pinansyal na tulong sa mga kwalipikadong indibidwal upang matugunan ang mga gastusing medikal, kabilang ang bayad sa ospital, gamot, at paggamot.',
            'procedure' => "1. Isumite ang Hospital Bill o Medical Certificate.\n2. Sumailalim sa pagsusuri ng facilitator.\n3. Ang mga naaprubahang aplikante ay makakatanggap ng guarantee letter o tulong pinansyal.",
        ]);

        // Burial
        ServiceTranslation::create([
            'service_id' => $burial->id,
            'language_code' => 'en',
            'service_name' => 'Burial Assistance Program',
            'description' => 'Provides financial assistance to the family or authorized representative of a deceased individual to cover funeral and burial costs.',
            'procedure' => "1. Present Death Certificate and Funeral Contract.\n2. Fill out social case study report.\n3. Receive financial assistance for burial expenses.",
        ]);
        ServiceTranslation::create([
            'service_id' => $burial->id,
            'language_code' => 'ceb',
            'service_name' => 'Tabang sa Pagpalubong',
            'description' => 'Naghatag og pinansyal nga tabang sa pamilya o awtorisado nga representante sa namatay nga indibidwal aron matabonan ang gasto sa punerarya ug pagpalubong.',
            'procedure' => "1. Ipakita ang Death Certificate ug Funeral Contract.\n2. Sulati ang social case study report.\n3. Makadawat og pinansyal nga tabang alang sa gasto sa pagpalubong.",
        ]);
        ServiceTranslation::create([
            'service_id' => $burial->id,
            'language_code' => 'fil',
            'service_name' => 'Tulong sa Libing',
            'description' => 'Nagbibigay ng tulong pinansyal sa pamilya ng namatay upang makatulong sa mga gastusin sa libing at punerarya.',
            'procedure' => "1. Ipakita ang Death Certificate at Funeral Contract.\n2. Sagutan ang social case study report.\n3. Tanggapin ang tulong pinansyal para sa libing.",
        ]);

        // Transportation
        ServiceTranslation::create([
            'service_id' => $trans->id,
            'language_code' => 'en',
            'service_name' => 'Transportation Assistance Program',
            'description' => 'Provides financial assistance to eligible individuals needing emergency travel support for medical, employment, or emergency displacement.',
            'procedure' => "1. Present travel referral or endorsement.\n2. Submit indigency certification.\n3. Receive travel allowance or tickets.",
        ]);
        ServiceTranslation::create([
            'service_id' => $trans->id,
            'language_code' => 'ceb',
            'service_name' => 'Tabang sa Transportasyon',
            'description' => 'Naghatag og pinansyal nga tabang sa mga kwalipikadong indibidwal nga nanginahanglan og dinalian nga suporta sa pagbiyahe alang sa medikal, trabaho, o emerhensya nga pagbakwit.',
            'procedure' => "1. Ipakita ang travel referral o endorsement.\n2. Isumite ang indigency certification.\n3. Makadawat og travel allowance o mga tiket.",
        ]);
        ServiceTranslation::create([
            'service_id' => $trans->id,
            'language_code' => 'fil',
            'service_name' => 'Tulong sa Transportasyon',
            'description' => 'Nagbibigay ng tulong pinansyal para sa emergency na pamasahe o transportasyon para sa mga layuning medikal, trabaho, o iba pang kagipitan.',
            'procedure' => "1. Ipakita ang travel referral o endorsement.\n2. Isumite ang indigency certification.\n3. Tanggapin ang travel allowance o ticket.",
        ]);

        // Employment
        ServiceTranslation::create([
            'service_id' => $emp->id,
            'language_code' => 'en',
            'service_name' => 'Employment Assistance',
            'description' => 'Provides assistance to job seekers, including livelihood support, skill training, and referral programs.',
            'procedure' => "1. Register in the employment database.\n2. Attend skills training workshops.\n3. Get matched with local government or private job placement offers.",
        ]);
        ServiceTranslation::create([
            'service_id' => $emp->id,
            'language_code' => 'ceb',
            'service_name' => 'Tabang sa Trabaho',
            'description' => 'Naghatag og tabang sa mga nangita og trabaho, lakip ang suporta sa panginabuhian, pagbansay sa kahanas, ug mga programa sa referral.',
            'procedure' => "1. Pagrehistro sa database sa trabaho.\n2. Pagtambong sa mga workshop sa pagbansay sa kahanas.\n3. I-match sa mga tanyag sa gobyerno o pribadong trabaho.",
        ]);
        ServiceTranslation::create([
            'service_id' => $emp->id,
            'language_code' => 'fil',
            'service_name' => 'Tulong sa Trabaho at Kabuhayan',
            'description' => 'Nagbibigay ng tulong sa mga naghahanap ng trabaho, kabilang ang suporta sa pangkabuhayan, pagsasanay, at mga programang referral.',
            'procedure' => "1. Magparehistro sa database ng trabaho.\n2. Dumalo sa pagsasanay sa kasanayan.\n3. I-ugnay sa mga alok ng lokal na pamahalaan o pribadong kumpanya.",
        ]);

        // 5. Create Service Requirements (JSON requirement text translations)
        // Educational
        ServiceRequirement::create([
            'service_id' => $edu->id,
            'requirement_text' => [
                'en' => 'Certificate of Enrollment',
                'ceb' => 'Sertipiko sa Pagpa-enrol',
                'fil' => 'Sertipiko ng Pagpapatala',
            ],
            'is_required' => true,
            'display_order' => 1,
        ]);
        ServiceRequirement::create([
            'service_id' => $edu->id,
            'requirement_text' => [
                'en' => 'Certificate of Registration',
                'ceb' => 'Sertipiko sa Rehistrasyon',
                'fil' => 'Sertipiko ng Rehistrasyon',
            ],
            'is_required' => true,
            'display_order' => 2,
        ]);
        ServiceRequirement::create([
            'service_id' => $edu->id,
            'requirement_text' => [
                'en' => 'Barangay Indigency',
                'ceb' => 'Barangay Indigency',
                'fil' => 'Barangay Indigency',
            ],
            'is_required' => true,
            'display_order' => 3,
        ]);
        ServiceRequirement::create([
            'service_id' => $edu->id,
            'requirement_text' => [
                'en' => 'School ID',
                'ceb' => 'School ID',
                'fil' => 'School ID ng Mag-aaral',
            ],
            'is_required' => true,
            'display_order' => 4,
        ]);
        ServiceRequirement::create([
            'service_id' => $edu->id,
            'requirement_text' => [
                'en' => '1 Valid ID (National / Postal ID)',
                'ceb' => '1 Valid ID (National / Postal ID)',
                'fil' => '1 Valid ID (National / Postal ID)',
            ],
            'is_required' => true,
            'display_order' => 5,
        ]);
        ServiceRequirement::create([
            'service_id' => $edu->id,
            'requirement_text' => [
                'en' => 'Grade (2nd Sem / Incoming 1st Year)',
                'ceb' => 'Grade (2nd Sem / Incoming 1st Year)',
                'fil' => 'Grade (2nd Sem / Incoming 1st Year)',
            ],
            'is_required' => true,
            'display_order' => 6,
        ]);

        // Medical
        ServiceRequirement::create([
            'service_id' => $med->id,
            'requirement_text' => [
                'en' => 'Medical Certificate',
                'ceb' => 'Sertipiko sa Medikal',
                'fil' => 'Sertipiko Medikal',
            ],
            'is_required' => true,
            'display_order' => 1,
        ]);
        ServiceRequirement::create([
            'service_id' => $med->id,
            'requirement_text' => [
                'en' => 'Barangay Indigency',
                'ceb' => 'Barangay Indigency',
                'fil' => 'Barangay Indigency',
            ],
            'is_required' => true,
            'display_order' => 2,
        ]);
        ServiceRequirement::create([
            'service_id' => $med->id,
            'requirement_text' => [
                'en' => '1 Valid ID (of the Applicant)',
                'ceb' => '1 Valid ID (sa Aplikante)',
                'fil' => '1 Valid ID (ng Aplikante)',
            ],
            'is_required' => true,
            'display_order' => 3,
        ]);
        ServiceRequirement::create([
            'service_id' => $med->id,
            'requirement_text' => [
                'en' => '1 Valid ID (of the Patient)',
                'ceb' => '1 Valid ID (sa Pasyente)',
                'fil' => '1 Valid ID (ng Pasyente)',
            ],
            'is_required' => true,
            'display_order' => 4,
        ]);
        ServiceRequirement::create([
            'service_id' => $med->id,
            'requirement_text' => [
                'en' => 'Hospital Bill',
                'ceb' => 'Bayranan sa Ospital',
                'fil' => 'Kabayaran sa Ospital',
            ],
            'is_required' => true,
            'display_order' => 5,
        ]);
        ServiceRequirement::create([
            'service_id' => $med->id,
            'requirement_text' => [
                'en' => 'Authorization of Patient',
                'ceb' => 'Autorisasyon sa Pasyente',
                'fil' => 'Pahintulot ng Pasyente',
            ],
            'is_required' => true,
            'display_order' => 6,
        ]);
        ServiceRequirement::create([
            'service_id' => $med->id,
            'requirement_text' => [
                'en' => 'Letter Request of the Provincial Governor',
                'ceb' => 'Sulat Hangyo sa Gobernador sa Probinsya',
                'fil' => 'Liham Kahilingan sa Gobernador ng Lalawigan',
            ],
            'is_required' => true,
            'display_order' => 7,
        ]);
        ServiceRequirement::create([
            'service_id' => $med->id,
            'requirement_text' => [
                'en' => 'Social Case Study Form (MSWDO)',
                'ceb' => 'Social Case Study Form (MSWDO)',
                'fil' => 'Social Case Study Form (MSWDO)',
            ],
            'is_required' => true,
            'display_order' => 8,
        ]);

        // Burial
        ServiceRequirement::create([
            'service_id' => $burial->id,
            'requirement_text' => [
                'en' => 'Death Certificate',
                'ceb' => 'Death Certificate',
                'fil' => 'Sertipiko ng Kamatayan',
            ],
            'is_required' => true,
            'display_order' => 1,
        ]);
        ServiceRequirement::create([
            'service_id' => $burial->id,
            'requirement_text' => [
                'en' => 'Barangay Indigency',
                'ceb' => 'Barangay Indigency',
                'fil' => 'Barangay Indigency',
            ],
            'is_required' => true,
            'display_order' => 2,
        ]);
        ServiceRequirement::create([
            'service_id' => $burial->id,
            'requirement_text' => [
                'en' => '1 Valid ID (of the Applicant)',
                'ceb' => '1 Valid ID (sa Aplikante)',
                'fil' => '1 Valid ID (ng Aplikante)',
            ],
            'is_required' => true,
            'display_order' => 3,
        ]);
        ServiceRequirement::create([
            'service_id' => $burial->id,
            'requirement_text' => [
                'en' => 'Letter Request of the Provincial Governor',
                'ceb' => 'Sulat Hangyo sa Gobernador sa Probinsya',
                'fil' => 'Liham Kahilingan sa Gobernador ng Lalawigan',
            ],
            'is_required' => true,
            'display_order' => 4,
        ]);
        ServiceRequirement::create([
            'service_id' => $burial->id,
            'requirement_text' => [
                'en' => 'Social Case Study Form (MSWDO)',
                'ceb' => 'Social Case Study Form (MSWDO)',
                'fil' => 'Social Case Study Form (MSWDO)',
            ],
            'is_required' => true,
            'display_order' => 5,
        ]);

        // Transportation
        ServiceRequirement::create([
            'service_id' => $trans->id,
            'requirement_text' => [
                'en' => 'Driver\'s Information',
                'ceb' => 'Impormasyon sa Driver',
                'fil' => 'Impormasyon ng Driver',
            ],
            'is_required' => true,
            'display_order' => 1,
        ]);

        // Employment
        ServiceRequirement::create([
            'service_id' => $emp->id,
            'requirement_text' => [
                'en' => 'PDS (Personal Data Sheet)',
                'ceb' => 'PDS (Personal Data Sheet)',
                'fil' => 'PDS (Personal Data Sheet)',
            ],
            'is_required' => true,
            'display_order' => 1,
        ]);
        ServiceRequirement::create([
            'service_id' => $emp->id,
            'requirement_text' => [
                'en' => 'Resume',
                'ceb' => 'Resume',
                'fil' => 'Resume',
            ],
            'is_required' => true,
            'display_order' => 2,
        ]);
        ServiceRequirement::create([
            'service_id' => $emp->id,
            'requirement_text' => [
                'en' => 'Recommendation',
                'ceb' => 'Rekomendasyon',
                'fil' => 'Rekomendasyon',
            ],
            'is_required' => true,
            'display_order' => 3,
        ]);
        ServiceRequirement::create([
            'service_id' => $emp->id,
            'requirement_text' => [
                'en' => 'Endorsement',
                'ceb' => 'Endorsement',
                'fil' => 'Endorsement',
            ],
            'is_required' => true,
            'display_order' => 4,
        ]);

        // 6. Create Eligibility Questions
        // Educational
        EligibilityQuestion::create([
            'service_id' => $edu->id,
            'question_text_en' => 'What is your family\'s monthly household income?',
            'question_text_ceb' => 'Pila ang binuwan nga kita sa inyong panimalay?',
            'question_text_fil' => 'Magkano ang buwanang kita ng inyong pamilya?',
            'type' => 'number',
            'expected_value' => '15000',
            'operator' => '<=',
        ]);
        EligibilityQuestion::create([
            'service_id' => $edu->id,
            'question_text_en' => 'Are you currently enrolled in an accredited school or college?',
            'question_text_ceb' => 'Kasamtangan ba ikaw nga naka-enrol sa usa ka akreditadong eskwelahan o kolehiyo?',
            'question_text_fil' => 'Kasalukuyan ka bang nag-aaral sa isang kinikilalang paaralan?',
            'type' => 'boolean',
            'expected_value' => 'true',
            'operator' => '==',
        ]);
        EligibilityQuestion::create([
            'service_id' => $edu->id,
            'question_text_en' => 'Do you have any failing grades from the previous semester?',
            'question_text_ceb' => 'Aduna ba kay mga hagbong nga grado gikan sa miaging semester?',
            'question_text_fil' => 'Mayroon ka bang mga bagsak na grado mula sa nakaraang semestre?',
            'type' => 'boolean',
            'expected_value' => 'false',
            'operator' => '==',
        ]);
        EligibilityQuestion::create([
            'service_id' => $edu->id,
            'question_text_en' => 'Are you a resident of this municipality for at least 6 months?',
            'question_text_ceb' => 'Residente ba ikaw niini nga lungsod sulod sa labing menos 6 ka bulan?',
            'question_text_fil' => 'Ikaw ba ay residente ng munisipalidad na ito ng hindi bababa sa 6 na buwan?',
            'type' => 'boolean',
            'expected_value' => 'true',
            'operator' => '==',
        ]);
        EligibilityQuestion::create([
            'service_id' => $edu->id,
            'question_text_en' => 'Are you currently a recipient of any other government scholarship or financial assistance?',
            'question_text_ceb' => 'Nakadawat ba ikaw karon og bisan unsang laing scholarship o pinansyal nga tabang gikan sa gobyerno?',
            'question_text_fil' => 'Kasalukuyan ka bang tumatanggap ng anumang ibang scholarship o tulong pinansyal mula sa gobyerno?',
            'type' => 'boolean',
            'expected_value' => 'false',
            'operator' => '==',
        ]);
        EligibilityQuestion::create([
            'service_id' => $edu->id,
            'question_text_en' => 'Are you a Filipino citizen?',
            'question_text_ceb' => 'Usa ba ikaw ka lungsoranon sa Pilipinas?',
            'question_text_fil' => 'Ikaw ba ay isang mamamayang Pilipino?',
            'type' => 'boolean',
            'expected_value' => 'true',
            'operator' => '==',
        ]);
        EligibilityQuestion::create([
            'service_id' => $edu->id,
            'question_text_en' => 'Are you a registered voter or a child of a registered voter in this municipality?',
            'question_text_ceb' => 'Rehistrado ka ba nga botante o anak sa usa ka rehistradong botante niini nga lungsod?',
            'question_text_fil' => 'Ikaw ba ay rehistradong botante o anak ng isang rehistradong botante sa munisipalidad na ito?',
            'type' => 'boolean',
            'expected_value' => 'true',
            'operator' => '==',
        ]);
        EligibilityQuestion::create([
            'service_id' => $edu->id,
            'question_text_en' => 'Do you possess a Certificate of Good Moral Character from your current/last school?',
            'question_text_ceb' => 'Aduna ba kay Certificate of Good Moral Character gikan sa imong kasamtangan/katapusan nga eskwelahan?',
            'question_text_fil' => 'Mayroon ka bang Sertipiko ng Mabuting Asal mula sa iyong kasalukuyan/huling paaralan?',
            'type' => 'boolean',
            'expected_value' => 'true',
            'operator' => '==',
        ]);
        EligibilityQuestion::create([
            'service_id' => $edu->id,
            'question_text_en' => 'Are you currently employed in a full-time capacity?',
            'question_text_ceb' => 'Aduna ba kay full-time nga trabaho karon?',
            'question_text_fil' => 'Kasalukuyan ka bang may full-time na trabaho?',
            'type' => 'boolean',
            'expected_value' => 'false',
            'operator' => '==',
        ]);
        EligibilityQuestion::create([
            'service_id' => $edu->id,
            'question_text_en' => 'Do you have any parents currently working as regular government employees?',
            'question_text_ceb' => 'Aduna ba kay ginikanan nga nagtrabaho isip regular nga empleyado sa gobyerno?',
            'question_text_fil' => 'Mayroon ka bang mga magulang na kasalukuyang nagtatrabaho bilang regular na empleyado ng gobyerno?',
            'type' => 'boolean',
            'expected_value' => 'false',
            'operator' => '==',
        ]);
        EligibilityQuestion::create([
            'service_id' => $edu->id,
            'question_text_en' => 'Have you ever been convicted of any crime or offense?',
            'question_text_ceb' => 'Nakonbikto na ba ikaw sa bisan unsang krimen o kalapasan?',
            'question_text_fil' => 'Nahatulan ka na ba sa anumang krimen o pagkakasala?',
            'type' => 'boolean',
            'expected_value' => 'false',
            'operator' => '==',
        ]);
        EligibilityQuestion::create([
            'service_id' => $edu->id,
            'question_text_en' => 'Are you willing to render at least 20 hours of community service per semester?',
            'question_text_ceb' => 'Andam ka ba nga mohatag og labing menos 20 ka oras nga serbisyo sa komunidad matag semester?',
            'question_text_fil' => 'Handa ka bang magbigay ng hindi bababa sa 20 oras ng serbisyo sa komunidad bawat semestre?',
            'type' => 'boolean',
            'expected_value' => 'true',
            'operator' => '==',
        ]);
        EligibilityQuestion::create([
            'service_id' => $edu->id,
            'question_text_en' => 'Is your general weighted average (GWA) from the previous semester 85% or higher?',
            'question_text_ceb' => 'Ang imong general weighted average (GWA) ba gikan sa miaging semester kay 85% o pataas?',
            'question_text_fil' => 'Ang iyong pangkalahatang average (GWA) ba mula sa nakaraang semestre ay 85% o mas mataas?',
            'type' => 'boolean',
            'expected_value' => 'true',
            'operator' => '==',
        ]);
        EligibilityQuestion::create([
            'service_id' => $edu->id,
            'question_text_en' => 'Are you transferring from a school outside this municipality?',
            'question_text_ceb' => 'Nagbalhin ba ikaw gikan sa usa ka eskwelahan sa gawas niini nga lungsod?',
            'question_text_fil' => 'Lumilipat ka ba mula sa isang paaralan sa labas ng munisipalidad na ito?',
            'type' => 'boolean',
            'expected_value' => 'false',
            'operator' => '==',
        ]);
        EligibilityQuestion::create([
            'service_id' => $edu->id,
            'question_text_en' => 'Can you provide a valid Certificate of Indigency from your Barangay?',
            'question_text_ceb' => 'Makahatag ba ikaw og balido nga Certificate of Indigency gikan sa imong Barangay?',
            'question_text_fil' => 'Maaari ka bang magbigay ng balidong Sertipiko ng Kahirapan mula sa inyong Barangay?',
            'type' => 'boolean',
            'expected_value' => 'true',
            'operator' => '==',
        ]);

        $this->call(EligibilityQuestionsSeeder::class);

        // 7. Seed History for Mark Cagatin
        $assessment = EligibilityAssessment::create([
            'user_id' => $citizen->id,
            'service_id' => $emp->id,
            'status' => 'eligible',
            'created_at' => Carbon::now()->subDays(4),
        ]);

        AssessmentAnswer::create([
            'assessment_id' => $assessment->id,
            'question' => 'Are you at least 18 years of age?',
            'answer' => '21',
        ]);
        AssessmentAnswer::create([
            'assessment_id' => $assessment->id,
            'question' => 'Are you currently unemployed and actively looking for work?',
            'answer' => 'true',
        ]);

        // Checklist application for Mark Cagatin
        $checklist = UserChecklist::create([
            'user_id' => $citizen->id,
            'service_id' => $emp->id,
            'status' => 'pending',
            'created_at' => Carbon::now()->subDays(2),
        ]);

        // Seed checklist items
        $reqs = ServiceRequirement::where('service_id', $emp->id)->get();
        foreach ($reqs as $req) {
            UserChecklistItem::create([
                'checklist_id' => $checklist->id,
                'requirement_id' => $req->id,
                'is_submitted' => false,
                'file_path' => null,
                'status' => 'pending',
            ]);
        }

        // 8. Seed Inquiries & Responses
        $inquiry = UserInquiry::create([
            'user_id' => $citizen->id,
            'service_id' => $emp->id,
            'inquiry_text' => 'Hello, I uploaded my resume but I would like to ask if there are any specific local trainings happening this month for San Miguel residents?',
            'status' => 'in_progress',
            'created_at' => Carbon::now()->subDays(1),
        ]);

        InquiryRequirense::create([
            'inquiry_id' => $inquiry->id,
            'requireent_text' => 'Hi Mark! Yes, we have an upcoming computer literacy workshop on July 20th. I have updated your application status to in-progress. Please make sure to upload your PSA Birth Certificate so we can verify your slot.',
            'responded_by' => $facilitator->id,
            'created_at' => Carbon::now()->subHours(6),
        ]);

        // Legacy data seeder disabled as per user request
        // $this->call(LegacyDataSeeder::class);
    }
}
