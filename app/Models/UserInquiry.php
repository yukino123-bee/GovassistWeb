<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'guest_name',
        'guest_email',
        'inquiry_text',
        'status',
        'is_bot',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(GovernmentService::class, 'service_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(InquiryRequirense::class, 'inquiry_id');
    }
}
