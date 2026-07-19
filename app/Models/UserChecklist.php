<?php

namespace App\Models;

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
}
