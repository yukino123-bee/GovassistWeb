<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['service_id', 'question_text_en', 'question_text_ceb', 'question_text_fil', 'type', 'expected_value', 'operator'])]
class EligibilityQuestion extends Model
{
    use HasFactory;

    public function service(): BelongsTo
    {
        return $this->belongsTo(GovernmentService::class, 'service_id');
    }

    public function getQuestionTextAttribute(): string
    {
        $lang = app()->getLocale();
        $field = 'question_text_'.$lang;

        return $this->$field ?? $this->question_text_en;
    }
}
