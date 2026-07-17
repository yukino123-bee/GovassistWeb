<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentAnswer extends Model
{
    use HasFactory;

    protected $fillable = ['assessment_id', 'question', 'answer'];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(EligibilityAssessment::class, 'assessment_id');
    }
}
