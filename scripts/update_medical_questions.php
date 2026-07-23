<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\EligibilityQuestion;
use App\Models\GovernmentService;
use Illuminate\Contracts\Console\Kernel;

$service = GovernmentService::where('service_name', 'Medical Assistance')->first();

if (! $service) {
    echo "Service not found\n";
    exit;
}

$newQuestions = [
    'Where is the patient currently admitted or receiving treatment?',
    'When did the medical condition or illness start?',
    'Why are you requesting medical assistance at this time?',
];

$questionsToReplace = EligibilityQuestion::where('service_id', $service->id)->limit(3)->get();
$scriptPath = base_path('scripts/translate.py');

foreach ($questionsToReplace as $index => $q) {
    $qText = $newQuestions[$index];
    echo "Translating: $qText\n";

    $command = 'python3 '.escapeshellarg($scriptPath).' --text '.escapeshellarg($qText);
    $output = shell_exec($command);

    $translations = ['en' => $qText, 'ceb' => '', 'fil' => '', 'sub' => ''];
    if ($output) {
        $result = json_decode($output, true);
        if ($result && ! isset($result['error'])) {
            $translations = $result;
        }
    }

    $q->update([
        'question_text_en' => $translations['en'],
        'question_text_ceb' => $translations['ceb'],
        'question_text_fil' => $translations['fil'],
        'question_text_sub' => $translations['sub'],
        'type' => 'text',
        'expected_value' => 'N/A',
        'operator' => '==',
    ]);
}

echo "Done updating questions!\n";
