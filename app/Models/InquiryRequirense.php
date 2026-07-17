<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InquiryRequirense extends Model
{
    use HasFactory;

    protected $table = 'inquiry_requirenses';

    protected $fillable = ['inquiry_id', 'requireent_text', 'responded_by'];

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(UserInquiry::class, 'inquiry_id');
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function getResponseTextAttribute()
    {
        return $this->requireent_text;
    }
}
