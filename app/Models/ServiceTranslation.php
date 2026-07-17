<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['service_id', 'language_code', 'service_name', 'description', 'procedure'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(GovernmentService::class, 'service_id');
    }
}
