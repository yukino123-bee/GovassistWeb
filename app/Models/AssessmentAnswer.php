<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentAnswer extends Model
{
    use HasFactory;

    protected $fillable = ['assessment_id', 'question', 'answer', 'is_eligible', 'is_required'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['is_eligible' => 'boolean', 'is_required' => 'boolean'];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(EligibilityAssessment::class, 'assessment_id');
    }
}
