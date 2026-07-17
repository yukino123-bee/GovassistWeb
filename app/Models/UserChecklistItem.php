<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserChecklistItem extends Model
{
    use HasFactory;

    protected $fillable = ['checklist_id', 'requirement_id', 'is_submitted', 'file_path', 'submitted_at', 'status'];

    protected $casts = [
        'is_submitted' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(UserChecklist::class, 'checklist_id');
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(ServiceRequirement::class, 'requirement_id');
    }
}
