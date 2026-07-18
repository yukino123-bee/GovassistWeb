<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\GovernmentService;
use App\Models\EligibilityQuestion;

$programs = [
    'Medical Assistance' => [
        "Do you have a medical prescription from a licensed doctor?",
        "Is the patient currently admitted to a hospital?",
        "Have you secured a medical certificate or clinical abstract?",
        "Do you have a valid hospital bill or promissory note?",
        "Is the patient a registered resident of the city/municipality?",
        "Are you applying for dialysis treatment assistance?",
        "Are you applying for chemotherapy session assistance?",
        "Are you requesting for maintenance medicines?",
        "Do you belong to the indigent sector based on MSWDO assessment?",
        "Have you received medical assistance from us in the last 3 months?",
        "Are you a senior citizen or a Person with Disability (PWD)?",
        "Do you have an active PhilHealth membership?",
        "Is the requested assistance for a surgical operation?",
        "Do you have a valid government-issued ID?",
        "Is your monthly household income less than Php 12,000?"
    ],
    'Burial Assistance' => [
        "Is the deceased an immediate family member (parent, spouse, child)?",
        "Do you have the original registered Death Certificate?",
        "Did the death occur within the last 30 days?",
        "Do you have a funeral service contract or statement of account?",
        "Was the deceased a registered resident of this municipality?",
        "Do you have a Barangay Certificate of Indigency?",
        "Are you the person who directly paid for the funeral expenses?",
        "Have you secured a permit to transfer the cadaver (if applicable)?",
        "Are you requesting assistance for embalming or casket costs?",
        "Are you requesting assistance for cemetery lot or niche rental?",
        "Are you an active member of any local burial association or cooperative?",
        "Do you have a valid government-issued ID of the claimant?",
        "Is your monthly household income less than Php 10,000?",
        "Have you received any burial assistance from DSWD for this deceased?",
        "Are you willing to sign a waiver of non-duplication of claims?"
    ],
    'Transportation' => [
        "Are you stranded and need to return to your home province?",
        "Do you have a referral letter from MSWDO or local officials?",
        "Is your travel due to a medical emergency or hospital referral?",
        "Are you traveling to seek employment outside the municipality?",
        "Have you secured a valid Police Clearance or NBI Clearance?",
        "Do you have a valid government-issued ID?",
        "Is your intended destination within the Philippines?",
        "Are you a victim of a recent calamity or disaster?",
        "Are you a rescued victim of human trafficking or abuse?",
        "Have you received transportation assistance from us in the past 6 months?",
        "Are you traveling alone?",
        "Do you have a Barangay Certificate of Indigency?",
        "Are you a senior citizen or a Person with Disability (PWD)?",
        "Are you willing to undergo an interview with our social worker?",
        "Is your monthly household income less than Php 8,000?"
    ],
    'Employment' => [
        "Are you currently unemployed or underemployed?",
        "Are you between the ages of 18 and 60?",
        "Do you have a valid Resume or Biodata?",
        "Have you secured a Barangay Clearance for employment purposes?",
        "Do you have a valid Police Clearance or NBI Clearance?",
        "Have you completed at least high school or Alternative Learning System (ALS)?",
        "Are you applying for a starter kit or capital assistance?",
        "Are you a solo parent seeking livelihood support?",
        "Are you a returning Overseas Filipino Worker (OFW)?",
        "Are you willing to attend a mandatory livelihood training seminar?",
        "Do you have a business plan or project proposal (for capital assistance)?",
        "Have you received livelihood assistance from us in the past 12 months?",
        "Are you a member of a registered cooperative or workers' association?",
        "Do you have a valid government-issued ID?",
        "Is your monthly household income less than Php 15,000?"
    ]
];

$scriptPath = base_path('scripts/translate.py');

foreach ($programs as $serviceName => $questions) {
    echo "Processing $serviceName...\n";
    $service = GovernmentService::where('service_name', $serviceName)->first();
    
    if (!$service) {
        echo "Service $serviceName not found. Skipping.\n";
        continue;
    }

    // Delete existing questions for this service if any (to avoid duplicates since they want 15 total)
    EligibilityQuestion::where('service_id', $service->id)->delete();

    foreach ($questions as $qText) {
        echo "  Translating: $qText\n";
        
        $command = 'python3 ' . escapeshellarg($scriptPath) . ' --text ' . escapeshellarg($qText);
        $output = shell_exec($command);
        
        $translations = ['en' => $qText, 'ceb' => '', 'fil' => '', 'sub' => ''];
        if ($output) {
            $result = json_decode($output, true);
            if ($result && !isset($result['error'])) {
                $translations = $result;
            }
        }

        // Generate some random logic type for demo purposes, mostly boolean
        $type = 'boolean';
        $operator = '==';
        $expected = 'true';

        // If income question
        if (str_contains(strtolower($qText), 'income')) {
            $type = 'boolean';
            $operator = '==';
            $expected = 'true';
        }

        EligibilityQuestion::create([
            'service_id' => $service->id,
            'question_text_en' => $translations['en'],
            'question_text_ceb' => $translations['ceb'],
            'question_text_fil' => $translations['fil'],
            'question_text_sub' => $translations['sub'],
            'type' => $type,
            'expected_value' => $expected,
            'operator' => $operator,
        ]);
    }
}

echo "Done seeding new questions!\n";
