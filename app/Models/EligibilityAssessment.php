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

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class, 'assessment_id');
    }
}
