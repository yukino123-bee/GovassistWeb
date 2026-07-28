<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommonQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['service_id', 'question_text', 'answer_text'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(GovernmentService::class, 'service_id');
    }

    public static function createDefaultsForService(GovernmentService $service): void
    {
        $serviceAttributes = $service->getAttributes();
        $questions = [
            [
                'question_text' => "What is {$service->service_name}?",
                'answer_text' => $serviceAttributes['description'],
            ],
            [
                'question_text' => "Who is eligible for {$service->service_name}?",
                'answer_text' => "Complete the eligibility assessment for {$service->service_name} to confirm whether you qualify.",
            ],
            [
                'question_text' => "How do I apply for {$service->service_name}?",
                'answer_text' => $serviceAttributes['procedure'] ?: 'Complete the eligibility assessment, upload every required document, and submit your application.',
            ],
            [
                'question_text' => "What documents are required for {$service->service_name}?",
                'answer_text' => "Open the {$service->service_name} Requirements Checklist to view and upload every required document.",
            ],
            [
                'question_text' => "How do I check my {$service->service_name} application status?",
                'answer_text' => 'Open your Profile to view the application status and any facilitator remarks.',
            ],
        ];

        foreach ($questions as $question) {
            $service->commonQuestions()->create($question);
        }
    }
}
