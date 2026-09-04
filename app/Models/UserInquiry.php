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
        'ticket_number',
        'subject',
        'user_id',
        'service_id',
        'guest_name',
        'guest_email',
        'inquiry_text',
        'status',
    ];

    protected static function booted(): void
    {
        static::created(function (UserInquiry $inquiry): void {
            if (empty($inquiry->ticket_number)) {
                $year = date('Y', strtotime((string) $inquiry->created_at));
                $inquiry->ticket_number = 'TKT-'.$year.'-'.str_pad((string) $inquiry->id, 5, '0', STR_PAD_LEFT);
                $inquiry->saveQuietly();
            }
        });
    }

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

    public function isResolvedOrClosed(): bool
    {
        return in_array($this->status, ['resolved', 'closed'], true);
    }

    public function canResidentReply(): bool
    {
        return ! $this->isResolvedOrClosed();
    }
}
