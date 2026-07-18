<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\EligibilityQuestion;
use App\Models\GovernmentService;

$service = GovernmentService::where("service_name", "Medical Assistance")->first();

if (!$service) {
    echo "Service not found\n";
    exit;
}

$updates = [
    23 => [
        'en' => "Does the patient have a valid hospital bill or promissory note?",
        'ceb' => "Aduna bay balidong bayronon sa ospital o promissory note ang pasyente?",
        'fil' => "Mayroon bang balidong bill sa ospital o promissory note ang pasyente?",
    ],
    25 => [
        'en' => "Is the assistance for dialysis treatment?",
        'ceb' => "Para ba sa dialysis treatment ang tabang?",
        'fil' => "Para ba sa dialysis treatment ang tulong?",
    ],
    26 => [
        'en' => "Is the assistance for chemotherapy sessions?",
        'ceb' => "Para ba sa chemotherapy session ang tabang?",
        'fil' => "Para ba sa chemotherapy session ang tulong?",
    ],
    27 => [
        'en' => "Is the assistance for maintenance medicines?",
        'ceb' => "Para ba sa maintenance nga tambal ang tabang?",
        'fil' => "Para ba sa maintenance na gamot ang tulong?",
    ],
    28 => [
        'en' => "Does the patient belong to the indigent sector based on MSWDO assessment?",
        'ceb' => "Nahisakop ba ang pasyente sa indigent sector base sa MSWDO assessment?",
        'fil' => "Kabilang ba ang pasyente sa indigent sector base sa MSWDO assessment?",
    ],
    29 => [
        'en' => "Has the patient received medical assistance from us in the last 3 months?",
        'ceb' => "Nakadawat ba og medikal nga tabang ang pasyente gikan namo sa miaging 3 ka bulan?",
        'fil' => "Nakatanggap ba ng medikal na tulong ang pasyente mula sa amin sa nakalipas na 3 buwan?",
    ],
    30 => [
        'en' => "Is the patient a senior citizen or a Person with Disability (PWD)?",
        'ceb' => "Ang pasyente ba usa ka senior citizen o Person with Disability (PWD)?",
        'fil' => "Ang pasyente ba ay isang senior citizen o Person with Disability (PWD)?",
    ],
    31 => [
        'en' => "Does the patient have an active PhilHealth membership?",
        'ceb' => "Aduna bay aktibong PhilHealth membership ang pasyente?",
        'fil' => "May aktibo bang PhilHealth membership ang pasyente?",
    ],
    33 => [
        'en' => "Does the claimant/representative have a valid government-issued ID?",
        'ceb' => "Aduna bay balidong government-issued ID ang claimant/representante?",
        'fil' => "Mayroon bang balidong government-issued ID ang claimant/kinatawan?",
    ],
    34 => [
        'en' => "Is the patient's monthly household income less than Php 12,000?",
        'ceb' => "Ubos ba sa Php 12,000 ang binuwan nga kita sa pamilya sa pasyente?",
        'fil' => "Mababa ba sa Php 12,000 ang buwanang kita ng pamilya ng pasyente?",
    ]
];

foreach ($updates as $id => $trans) {
    $q = EligibilityQuestion::find($id);
    if ($q) {
        $q->update([
            'question_text_en' => $trans['en'],
            'question_text_ceb' => $trans['ceb'],
            'question_text_fil' => $trans['fil'],
            'question_text_sub' => $trans['ceb'], // fallback to ceb
        ]);
        echo "Updated ID $id\n";
    }
}

// Add the new text question
EligibilityQuestion::create([
    'service_id' => $service->id,
    'question_text_en' => "What is your relationship to the patient? (e.g. self, spouse, child, representative)",
    'question_text_ceb' => "Unsa ang imong relasyon sa pasyente? (pananglitan: kaugalingon, asawa/bana, anak, representante)",
    'question_text_fil' => "Ano ang iyong relasyon sa pasyente? (halimbawa: sarili, asawa, anak, kinatawan)",
    'question_text_sub' => "Unsa ang imong relasyon sa pasyente? (pananglitan: kaugalingon, asawa/bana, anak, representante)",
    'type' => 'text',
    'expected_value' => 'N/A',
    'operator' => '=='
]);

echo "Added new relationship question\n";
