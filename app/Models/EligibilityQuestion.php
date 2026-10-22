<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['service_id', 'question_text_en', 'question_text_ceb', 'question_text_fil', 'type', 'expected_value', 'operator', 'is_required'])]
class EligibilityQuestion extends Model
{
    use HasFactory;

    protected $attributes = [
        'is_required' => true,
    ];

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

    public function getPassingRuleLabelAttribute(): string
    {
        if ($this->type === 'boolean') {
            return $this->expected_value === 'true' ? 'Answers Yes' : 'Answers No';
        }

        if ($this->type === 'number') {
            $words = [
                '<=' => 'No more than',
                '>=' => 'At least',
                '<' => 'Less than',
                '>' => 'More than',
                '=' => 'Exactly',
                '==' => 'Exactly',
            ];

            return ($words[$this->operator] ?? $this->operator).' '.$this->expected_value;
        }

        $accepted = $this->acceptedTextAnswers();

        return $accepted === [] ? 'Not checked' : 'Mentions '.implode(' / ', $accepted);
    }

    /**
     * Keywords a written answer may contain to pass; empty means the answer is not checked.
     *
     * @return array<int, string>
     */
    public function acceptedTextAnswers(): array
    {
        return array_values(array_filter(array_map(
            fn (string $keyword) => mb_strtolower(trim($keyword)),
            explode(',', (string) $this->expected_value),
        ), fn (string $keyword) => $keyword !== ''));
    }

    public function textAnswerPasses(?string $answer): bool
    {
        $accepted = $this->acceptedTextAnswers();

        if ($accepted === []) {
            return true;
        }

        $normalizedAnswer = mb_strtolower(trim((string) $answer));

        foreach ($accepted as $keyword) {
            if (str_contains($normalizedAnswer, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
        ];
    }
}
