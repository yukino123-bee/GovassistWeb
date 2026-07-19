<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceRequirement extends Model
{
    use HasFactory;

    protected $fillable = ['service_id', 'requirement_text', 'is_required', 'display_order'];

    protected $casts = [
        'requirement_text' => 'array',
        'is_required' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(GovernmentService::class, 'service_id');
    }

    public function getTextAttribute()
    {
        $lang = app()->getLocale();
        if (is_array($this->requirement_text)) {
            return $this->requirement_text[$lang] ?? $this->requirement_text['en'] ?? '';
        }

        return $this->requirement_text;
    }

    public function getNameEnAttribute(): string
    {
        return is_array($this->requirement_text) ? ($this->requirement_text['en'] ?? '') : '';
    }

    public function getNameCebAttribute(): string
    {
        return is_array($this->requirement_text) ? ($this->requirement_text['ceb'] ?? '') : '';
    }

    public function getNameFilAttribute(): string
    {
        return is_array($this->requirement_text) ? ($this->requirement_text['fil'] ?? '') : '';
    }

    public function template(): HasOne
    {
        return $this->hasOne(DocumentTemplate::class, 'requirement_id');
    }
}
