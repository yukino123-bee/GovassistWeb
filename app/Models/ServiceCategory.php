<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = ['category_name', 'description'];

    public function governmentServices(): HasMany
    {
        return $this->hasMany(GovernmentService::class, 'category_id');
    }
}
