<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'language', 'avatar', 'dob', 'address', 'civil_status', 'contact_number', 'valid_id_path'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public function isFacilitator(): bool
    {
        return $this->role === 'facilitator';
    }

    public function isCitizen(): bool
    {
        return $this->role === 'citizen';
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(EligibilityAssessment::class, 'user_id');
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(UserChecklist::class, 'user_id');
    }

    public function languages(): HasMany
    {
        return $this->hasMany(UserLanguage::class, 'user_id');
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(UserInquiry::class, 'user_id');
    }

    // ERD Column Aliases / Accessors
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    public function setFullNameAttribute(string $value): void
    {
        $this->attributes['name'] = $value;
    }

    public function getPhoneNumberAttribute(): ?string
    {
        return $this->contact_number;
    }

    public function setPhoneNumberAttribute(?string $value): void
    {
        $this->attributes['contact_number'] = $value;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'date',
        ];
    }
}
