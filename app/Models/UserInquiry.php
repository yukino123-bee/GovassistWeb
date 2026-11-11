<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'user_id',
        'service_id',
        'guest_name',
        'guest_email',
        'inquiry_text',
        'status',
        'awaiting_reply_since',
        'resident_seen_reply_id',
    ];

    protected $appends = ['expires_at'];

    protected function casts(): array
    {
        return [
            'awaiting_reply_since' => 'datetime',
            'resident_seen_reply_id' => 'integer',
        ];
    }

    /** Replies written by facilitators or other staff (not the resident, not automatic system messages). */
    public function staffResponses(): HasMany
    {
        return $this->responses()
            ->where('is_system', false)
            ->whereHas('responder', fn (Builder $responder) => $responder->where('role', '!=', 'resident'));
    }

    /** A staff reply the resident has not opened the Inquiry page to see yet. */
    public function hasUnreadStaffReply(): bool
    {
        return $this->staffResponses()
            ->when($this->resident_seen_reply_id, fn (Builder $replies, $seenId) => $replies->where('id', '>', $seenId))
            ->exists();
    }

    /** Called when the resident opens the Inquiry page: everything staff have written so far counts as seen. */
    public function markSeenByResident(): void
    {
        $latestStaffReplyId = $this->staffResponses()->max('id');

        // toBase keeps updated_at, so inactivity pruning is unchanged
        if ($latestStaffReplyId) {
            self::whereKey($this->id)->toBase()->update(['resident_seen_reply_id' => $latestStaffReplyId]);
        }
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

    /**
     * Conversations where staff replied and the resident stayed silent past the timeout.
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('awaiting_reply_since', '<=', now()->subHours(config('inquiries.reply_timeout_hours')));
    }

    /**
     * Conversations with no activity (thread update or new message) for the inactivity window.
     */
    public function scopeInactive(Builder $query): Builder
    {
        $cutoff = now()->subHours(config('inquiries.inactive_after_hours'));

        return $query->where('updated_at', '<=', $cutoff)
            ->whereDoesntHave('responses', fn (Builder $responses) => $responses->where('created_at', '>', $cutoff));
    }

    /**
     * Conversations the prune job deletes: expired replies or inactive threads.
     */
    public function scopeStale(Builder $query): Builder
    {
        return $query->where(fn (Builder $stale) => $stale->expired()->orWhere(fn (Builder $inactive) => $inactive->inactive()));
    }

    public function getExpiresAtAttribute(): ?string
    {
        return $this->awaiting_reply_since
            ?->copy()
            ->addHours(config('inquiries.reply_timeout_hours'))
            ->toIso8601String();
    }
}
