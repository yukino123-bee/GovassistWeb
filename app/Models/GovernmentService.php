<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GovernmentService extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'service_name', 'description', 'procedure', 'icon'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ServiceTranslation::class, 'service_id');
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(ServiceRequirement::class, 'service_id');
    }

    public function eligibilityQuestions(): HasMany
    {
        return $this->hasMany(EligibilityQuestion::class, 'service_id');
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(UserChecklist::class, 'service_id');
    }

    public function getTranslation(?string $lang = null)
    {
        $lang = $lang ?: app()->getLocale();
        $translation = $this->translations()->where('language_code', $lang)->first();

        if ($translation) {
            return $translation;
        }

        return (object) [
            'service_name' => $this->getRawOriginal('service_name'),
            'description' => $this->getRawOriginal('description'),
            'procedure' => $this->getRawOriginal('procedure'),
        ];
    }

    public function getNameAttribute()
    {
        return $this->getTranslation()->service_name;
    }

    public function getDescriptionAttribute()
    {
        return $this->getTranslation()->description;
    }

    public function getProcedureAttribute()
    {
        return $this->getTranslation()->procedure ?? '';
    }

    public function getNameEnAttribute()
    {
        return $this->translations()->where('language_code', 'en')->value('service_name') ?? $this->service_name;
    }

    public function getNameCebAttribute()
    {
        return $this->translations()->where('language_code', 'ceb')->value('service_name') ?? '';
    }

    public function getNameFilAttribute()
    {
        return $this->translations()->where('language_code', 'fil')->value('service_name') ?? '';
    }

    public function getDescriptionEnAttribute()
    {
        return $this->translations()->where('language_code', 'en')->value('description') ?? $this->description;
    }

    public function getDescriptionCebAttribute()
    {
        return $this->translations()->where('language_code', 'ceb')->value('description') ?? '';
    }

    public function getDescriptionFilAttribute()
    {
        return $this->translations()->where('language_code', 'fil')->value('description') ?? '';
    }

    public function getProcedureEnAttribute()
    {
        return $this->translations()->where('language_code', 'en')->value('procedure') ?? $this->procedure;
    }

    public function getProcedureCebAttribute()
    {
        return $this->translations()->where('language_code', 'ceb')->value('procedure') ?? '';
    }

    public function getProcedureFilAttribute()
    {
        return $this->translations()->where('language_code', 'fil')->value('procedure') ?? '';
    }

    public function getNameSubAttribute()
    {
        return $this->translations()->where('language_code', 'sub')->value('service_name') ?? '';
    }

    public function getDescriptionSubAttribute()
    {
        return $this->translations()->where('language_code', 'sub')->value('description') ?? '';
    }

    public function getProcedureSubAttribute()
    {
        return $this->translations()->where('language_code', 'sub')->value('procedure') ?? '';
    }
}
