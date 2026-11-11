<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EligibilityAssessment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'service_id', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(GovernmentService::class, 'service_id');
    }

    /**
     * Percentage of scored answers the facilitator flagged eligible.
     * Only required questions are scored; if there are none, every answer is.
     */
    public function scorePercentage(): int
    {
        $answers = $this->answers;
        $scored = $answers->where('is_required', true);
        $scored = $scored->isEmpty() ? $answers : $scored;

        if ($scored->isEmpty()) {
            return 0;
        }

        return (int) round($scored->where('is_eligible', true)->count() / $scored->count() * 100);
    }

    /** Whether the live score still meets the program's minimum (used before letting a resident in). */
    public function meetsMinimum(): bool
    {
        return $this->scorePercentage() >= ($this->service?->minimum_percentage ?? 0);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class, 'assessment_id');
    }
}
