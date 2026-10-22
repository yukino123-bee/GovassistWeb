<?php

namespace App\Models;

use App\Mail\VerifyOtpEmail;
use App\Services\SmsService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

#[Fillable(['name', 'first_name', 'middle_name', 'last_name', 'email', 'password', 'role', 'language', 'avatar', 'dob', 'sex', 'address', 'barangay', 'street_address', 'civil_status', 'contact_number', 'valid_id_path', 'email_verified_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    public const SEXES = ['Male', 'Female'];

    public const CIVIL_STATUSES = ['Single', 'Married', 'Widowed', 'Divorced', 'Live-in'];

    public function sendEmailVerificationNotification()
    {
        $otpCode = (string) random_int(100000, 999999);
        Cache::put('verification_otp_'.$this->id, $otpCode, now()->addMinutes(15));

        if (! empty($this->email)) {
            try {
                Mail::to($this->email)->send(new VerifyOtpEmail($otpCode));
            } catch (Throwable $e) {
                Log::error('Email OTP dispatch error to '.$this->email.': '.$e->getMessage());
            }
        }

        if (! empty($this->contact_number)) {
            try {
                app(SmsService::class)->sendOtp($this, $otpCode);
            } catch (Throwable $e) {
                Log::error('SMS OTP dispatch error to '.$this->contact_number.': '.$e->getMessage());
            }
        }
    }

    /**
     * Normalize a Philippine mobile number to the 09XXXXXXXXX format, or null if it is not one.
     */
    public static function normalizeContactNumber(?string $number): ?string
    {
        $digits = preg_replace('/[\s\-()]/', '', (string) $number);

        if (preg_match('/^(?:\+?63|0)(9\d{9})$/', $digits, $matches)) {
            return '0'.$matches[1];
        }

        return null;
    }

    public function isFacilitator(): bool
    {
        return in_array($this->role, ['facilitator', 'admin', 'administrator']);
    }

    public function isResident(): bool
    {
        return in_array($this->role, ['resident', 'citizen']);
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
        return ! empty($this->name) && $this->missingProfileFields() === [];
    }

    public const REQUIRED_PROFILE_FIELD_COUNT = 5;

    /**
     * Profile fields a resident still has to fill in, keyed by the field's translation key.
     *
     * @return array<string, string>
     */
    public function missingProfileFields(): array
    {
        $required = [
            'dob' => 'date_of_birth',
            'civil_status' => 'civil_status',
            'address' => 'address',
            'contact_number' => 'contact_info',
            'valid_id_path' => 'upload_id',
        ];

        return collect($required)
            ->filter(fn (string $label, string $field): bool => empty($this->{$field}))
            ->all();
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
