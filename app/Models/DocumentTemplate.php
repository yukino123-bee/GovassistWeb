<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    protected $fillable = [
        'service_id',
        'requirement_id',
        'file_path',
        'name_en',
        'name_ceb',
        'description_en',
        'description_ceb',
    ];

    public function service()
    {
        return $this->belongsTo(GovernmentService::class);
    }

    public function requirement()
    {
        return $this->belongsTo(ServiceRequirement::class);
    }
}
