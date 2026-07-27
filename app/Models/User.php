<?php

namespace App\Models;

use App\Mail\VerifyOtpEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

#[Fillable(['name', 'first_name', 'middle_name', 'last_name', 'email', 'password', 'role', 'language', 'avatar', 'dob', 'address', 'civil_status', 'contact_number', 'valid_id_path', 'email_verified_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    public function sendEmailVerificationNotification()
    {
        $otpCode = (string) random_int(100000, 999999);
        Cache::put('verification_otp_'.$this->id, $otpCode, now()->addMinutes(15));

        Mail::to($this->email)->send(new VerifyOtpEmail($otpCode));
    }

    public function isFacilitator(): bool
    {
        return $this->role === 'facilitator';
    }

    public function isResident(): bool
    {
        return $this->role === 'resident';
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(EligibilityAssessment::class, 'user_id');
    }

    /**
     * Check if the resident's profile is fully completed.
     */
    public function isProfileComplete(): bool
    {
        return ! empty($this->name)
            && ! empty($this->dob)
            && ! empty($this->address)
            && ! empty($this->civil_status)
            && ! empty($this->contact_number)
            && ! empty($this->valid_id_path);
    }

    /**
     * Check if the resident is eligible for a given government service.
     */
    public function isEligibleFor(int $serviceId): bool
    {
        return $this->assessments()
            ->where('service_id', $serviceId)
            ->where('status', 'eligible')
            ->exists();
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
        if ($this->first_name || $this->last_name) {
            return trim($this->first_name.' '.($this->middle_name ? $this->middle_name.' ' : '').$this->last_name);
        }

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
