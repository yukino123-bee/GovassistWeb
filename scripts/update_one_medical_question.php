<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\EligibilityQuestion;

$oldQuestionText = "Why are you requesting medical assistance at this time?";
$newQuestionText = "What is the reason why the patient got hospitalized?";

$q = EligibilityQuestion::where('question_text_en', $oldQuestionText)->first();

if (!$q) {
    echo "Question not found\n";
    exit;
}

$scriptPath = base_path('scripts/translate.py');

echo "Translating: $newQuestionText\n";

$command = 'python3 ' . escapeshellarg($scriptPath) . ' --text ' . escapeshellarg($newQuestionText);
$output = shell_exec($command);

$translations = ['en' => $newQuestionText, 'ceb' => '', 'fil' => '', 'sub' => ''];
if ($output) {
    $result = json_decode($output, true);
    if ($result && !isset($result['error'])) {
        $translations = $result;
    }
}

$q->update([
    'question_text_en' => $translations['en'],
    'question_text_ceb' => $translations['ceb'],
    'question_text_fil' => $translations['fil'],
    'question_text_sub' => $translations['sub'],
]);

echo "Done updating question!\n";
