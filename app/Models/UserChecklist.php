<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserChecklist extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'service_id', 'status', 'remarks', 'application_type'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(GovernmentService::class, 'service_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(UserChecklistItem::class, 'checklist_id');
    }

    /**
     * Required requirements that apply to this application (renewals only need a subset).
     *
     * @return Collection<int, ServiceRequirement>
     */
    public function requiredRequirements(): Collection
    {
        $requirements = $this->service->requirements->where('is_required', true);

        $isEmploymentRenewal = $this->application_type === 'renewal'
            && str_contains(strtolower($this->service->service_name), 'employment');

        if (! $isEmploymentRenewal) {
            return $requirements->values();
        }

        return $requirements->filter(function (ServiceRequirement $requirement): bool {
            $name = strtolower((string) $requirement->name_en);

            return str_contains($name, 'pds')
                || str_contains($name, 'recommendation')
                || str_contains($name, 'letter request')
                || str_contains($name, 'endorsement');
        })->values();
    }
}
