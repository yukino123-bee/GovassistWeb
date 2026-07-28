<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\EligibilityQuestion;
use Illuminate\Contracts\Console\Kernel;

// Truncate existing questions to replace them entirely
EligibilityQuestion::truncate();

$questions = [
    // Service 1: Educational Assistance
    1 => [
        ['text' => 'Is the student currently enrolled in an accredited school or college?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => "Is the student's family monthly household income below PHP 15,000?", 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the student a resident of the municipality/province for at least 6 months?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the student currently receiving any other government scholarship or educational assistance?', 'type' => 'boolean', 'expected' => 'false', 'operator' => '=='],
        ['text' => 'Is the student of good moral character as certified by the school?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Does the student have a passing grade average from the previous semester/year?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the student enrolled in at least a minimum required number of units?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => "Is the student's family classified as indigent by the barangay?", 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the student an active member of any community youth organization?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the student a dependent of a solo parent or a person with disability?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the student willing to render community service hours if required?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Has the student submitted all required academic records?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the student enrolled in a priority course or degree program?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the student free from any disciplinary action from the school?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the student a first-time applicant for this specific assistance program?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
    ],
    // Service 2: Medical Assistance
    2 => [
        ['text' => 'Is the patient currently admitted to a public hospital or certified healthcare facility?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => "Is the patient's family monthly household income below PHP 15,000?", 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the patient a certified resident of the municipality/province?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Does the patient have a valid medical certificate outlining the illness or condition?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => "Is the patient's illness considered a severe or chronic condition requiring extensive treatment?", 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => "Is the patient's family classified as indigent by the local barangay?", 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Has the patient exhausted all available PhilHealth benefits for this admission?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the patient currently receiving identical medical assistance from other government agencies?', 'type' => 'boolean', 'expected' => 'false', 'operator' => '=='],
        ['text' => 'Does the patient require specialized medication not available in the local health center?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the patient a senior citizen, a person with disability, or a minor?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the patient scheduled for an urgent surgical procedure?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Has the patient secured a social case study report from the MSWDO?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the patient a member of an indigenous cultural community?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Does the patient require continuous dialysis, chemotherapy, or physical therapy?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the patient unable to work due to the current medical condition?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
    ],
    // Service 3: Burial Assistance
    3 => [
        ['text' => 'Was the deceased a registered resident of the municipality/province at the time of death?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => "Is the deceased's family monthly household income below PHP 15,000?", 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Does the family have a certified true copy of the death certificate?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the family classified as indigent by the local barangay?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Did the death occur within the last 30 days prior to this application?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the applicant a direct family member or legal representative of the deceased?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Has the family secured a funeral contract from a registered mortuary service?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Was the deceased a senior citizen or a person with disability?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Has the family received burial assistance from other government agencies for this death?', 'type' => 'boolean', 'expected' => 'false', 'operator' => '=='],
        ['text' => 'Is the cause of death related to an accident or a sudden tragic event?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Was the deceased the primary breadwinner of the family?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the family unable to cover the basic funeral and burial costs?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Does the family have a social case study report from the MSWDO?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the applicant requesting assistance specifically for casket or interment services?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Has the family complied with all local health regulations for the burial?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
    ],
    // Service 4: Transportation Assistance
    4 => [
        ['text' => 'Is the passenger a registered resident of the municipality/province?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => "Is the passenger's family monthly household income below PHP 15,000?", 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the passenger traveling for an urgent medical referral or emergency?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the passenger classified as indigent by the local barangay?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the passenger a stranded individual or victim of a calamity seeking to return home?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Does the passenger have a valid referral letter from a hospital, employer, or agency?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the passenger traveling to seek employment opportunities outside the province?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Has the passenger exhausted personal funds for travel expenses?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the passenger a senior citizen, a person with disability, or a pregnant woman?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the passenger requesting assistance for a one-way trip only?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Does the passenger have valid identification and necessary travel permits?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the passenger traveling with essential medical equipment or a necessary escort?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Has the passenger received transportation assistance from the local government in the last 6 months?', 'type' => 'boolean', 'expected' => 'false', 'operator' => '=='],
        ['text' => "Is the passenger's destination within the approved inter-provincial or regional routes?", 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Does the passenger have a social case study report justifying the need for travel assistance?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
    ],
    // Service 5: Employment Assistance
    5 => [
        ['text' => 'Is the job seeker a registered resident of the municipality/province?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the job seeker currently unemployed and actively looking for work?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the job seeker at least 18 years of age and legally allowed to work?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Does the job seeker have a certificate of indigency from the local barangay?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Has the job seeker registered with the local Public Employment Service Office (PESO)?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the job seeker a graduate of a vocational training or skills development program?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the job seeker a displaced worker due to retrenchment or company closure?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the job seeker applying for a livelihood starter kit or capital assistance?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Has the job seeker received employment or livelihood assistance in the past year?', 'type' => 'boolean', 'expected' => 'false', 'operator' => '=='],
        ['text' => 'Is the job seeker a person with disability or a single parent seeking employment?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the job seeker willing to undergo additional skills training if required?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Does the job seeker have a valid NBI or Police Clearance for employment purposes?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the job seeker applying for a position that matches their current qualifications?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Is the job seeker an out-of-school youth seeking entry-level employment?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
        ['text' => 'Has the job seeker secured a social case study report from the MSWDO?', 'type' => 'boolean', 'expected' => 'true', 'operator' => '=='],
    ],
];

$optionalQuestionTexts = [
    'Is the student an active member of any community youth organization?',
    'Is the student a dependent of a solo parent or a person with disability?',
    'Is the student willing to render community service hours if required?',
    'Is the student enrolled in a priority course or degree program?',
    'Is the student a first-time applicant for this specific assistance program?',
    'Is the patient currently admitted to a public hospital or certified healthcare facility?',
    "Is the patient's illness considered a severe or chronic condition requiring extensive treatment?",
    'Has the patient exhausted all available PhilHealth benefits for this admission?',
    'Does the patient require specialized medication not available in the local health center?',
    'Is the patient a senior citizen, a person with disability, or a minor?',
    'Is the patient scheduled for an urgent surgical procedure?',
    'Is the patient a member of an indigenous cultural community?',
    'Does the patient require continuous dialysis, chemotherapy, or physical therapy?',
    'Is the patient unable to work due to the current medical condition?',
    'Was the deceased a senior citizen or a person with disability?',
    'Is the cause of death related to an accident or a sudden tragic event?',
    'Was the deceased the primary breadwinner of the family?',
    'Is the applicant requesting assistance specifically for casket or interment services?',
    'Has the family complied with all local health regulations for the burial?',
    'Is the passenger traveling for an urgent medical referral or emergency?',
    'Is the passenger a stranded individual or victim of a calamity seeking to return home?',
    'Is the passenger traveling to seek employment opportunities outside the province?',
    'Is the passenger a senior citizen, a person with disability, or a pregnant woman?',
    'Is the passenger requesting assistance for a one-way trip only?',
    'Is the passenger traveling with essential medical equipment or a necessary escort?',
    'Is the job seeker a graduate of a vocational training or skills development program?',
    'Is the job seeker a displaced worker due to retrenchment or company closure?',
    'Is the job seeker applying for a livelihood starter kit or capital assistance?',
    'Is the job seeker a person with disability or a single parent seeking employment?',
    'Is the job seeker willing to undergo additional skills training if required?',
    'Is the job seeker applying for a position that matches their current qualifications?',
    'Is the job seeker an out-of-school youth seeking entry-level employment?',
    'Has the job seeker secured a social case study report from the MSWDO?',
];

foreach ($questions as $serviceId => $serviceQuestions) {
    foreach ($serviceQuestions as $q) {
        EligibilityQuestion::create([
            'service_id' => $serviceId,
            'question_text_en' => $q['text'],
            'question_text_ceb' => $q['text'],
            'question_text_fil' => $q['text'],
            'type' => $q['type'],
            'expected_value' => $q['expected'],
            'operator' => $q['operator'],
            'is_required' => ! in_array($q['text'], $optionalQuestionTexts, true),
        ]);
    }
}

echo "Successfully inserted 15 eligibility questions for all 5 services.\n";
