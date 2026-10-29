<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class SmsService
{
    protected bool $enabled;

    protected string $driver;

    protected ?string $apiKey;

    protected string $senderName;

    protected int $maxAttempts;

    protected int $decayMinutes;

    protected array $twilioConfig;

    public function __construct()
    {
        $this->enabled = (bool) config('services.sms.enabled', true);
        $this->driver = (string) config('services.sms.driver', 'log');
        $this->apiKey = config('services.sms.api_key');
        $this->senderName = (string) config('services.sms.sender_name', 'GovAssist');
        $this->maxAttempts = (int) config('services.sms.rate_limit_max_attempts', 3);
        $this->decayMinutes = (int) config('services.sms.rate_limit_decay_minutes', 10);
        $this->twilioConfig = (array) config('services.sms.twilio', []);
    }

    /**
     * Send an SMS to a phone number (protected by Rate Limiting).
     */
    public function send(string $phoneNumber, string $message, bool $bypassRateLimit = false): bool
    {
        if (! $this->enabled) {
            Log::info("SMS disabled. Suppressed message to {$phoneNumber}: {$message}");

            return true;
        }

        $formattedNumber = $this->formatPhoneNumber($phoneNumber);
        if (! $formattedNumber) {
            Log::warning("Invalid phone number format for SMS: {$phoneNumber}");

            return false;
        }

        $rateLimitKey = 'sms_limit:'.md5($formattedNumber);

        if (! $bypassRateLimit && RateLimiter::tooManyAttempts($rateLimitKey, $this->maxAttempts)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            Log::warning("SMS Rate Limit Exceeded for {$formattedNumber}. Suppressed dispatch for {$seconds} seconds to protect API token usage.");

            return false;
        }

        try {
            $sent = match ($this->driver) {
                'unisms' => $this->sendViaUniSms($formattedNumber, $message),
                'semaphore' => $this->sendViaSemaphore($formattedNumber, $message),
                'twilio' => $this->sendViaTwilio($formattedNumber, $message),
                default => $this->sendViaLog($formattedNumber, $message),
            };

            if ($sent && ! $bypassRateLimit) {
                RateLimiter::hit($rateLimitKey, $this->decayMinutes * 60);
            }

            return $sent;
        } catch (\Exception $e) {
            Log::error("SMS dispatch error ({$this->driver}) to {$formattedNumber}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Get remaining allowed SMS attempts for a phone number.
     */
    public function remainingAttempts(string $phoneNumber): int
    {
        $formattedNumber = $this->formatPhoneNumber($phoneNumber);
        if (! $formattedNumber) {
            return 0;
        }

        $rateLimitKey = 'sms_limit:'.md5($formattedNumber);

        return RateLimiter::remaining($rateLimitKey, $this->maxAttempts);
    }

    /**
     * Clear rate limit counter for a specific phone number.
     */
    public function clearRateLimit(string $phoneNumber): void
    {
        $formattedNumber = $this->formatPhoneNumber($phoneNumber);
        if ($formattedNumber) {
            $rateLimitKey = 'sms_limit:'.md5($formattedNumber);
            RateLimiter::clear($rateLimitKey);
        }
    }

    /**
     * Send OTP Verification code via SMS.
     */
    public function sendOtp(User|string $recipient, string $otpCode): bool
    {
        $phone = $this->resolvePhoneNumber($recipient);
        if (! $phone) {
            return false;
        }

        $message = "SSFO GovAssist: Your verification OTP code is {$otpCode}. Valid for 15 minutes. Do not share this code.";

        return $this->send($phone, $message);
    }

    /**
     * Send Application Status Update via SMS.
     */
    public function sendApplicationUpdate(User $user, string $serviceName, string $status, ?string $remarks = null): bool
    {
        $phone = $this->resolvePhoneNumber($user);
        if (! $phone) {
            return false;
        }

        $formattedStatus = strtoupper($status);
        $message = "SSFO GovAssist: Your application for {$serviceName} has been {$formattedStatus}.";

        if (! empty($remarks)) {
            $message .= " Remarks: {$remarks}";
        }

        return $this->send($phone, $message);
    }

    /**
     * Send Inquiry Reply alert via SMS.
     */
    public function sendInquiryReply(User|string $recipient, string $replyMessage): bool
    {
        $phone = $this->resolvePhoneNumber($recipient);
        if (! $phone) {
            return false;
        }

        $preview = Str::limit(strip_tags($replyMessage), 60);
        $message = "SSFO GovAssist: A facilitator replied to your inquiry: \"{$preview}\". Log in to view full thread.";

        return $this->send($phone, $message);
    }

    /**
     * Send Reassessment Request Update via SMS.
     */
    public function sendReassessmentUpdate(User $user, string $serviceName, string $status): bool
    {
        $phone = $this->resolvePhoneNumber($user);
        if (! $phone) {
            return false;
        }

        $formattedStatus = strtoupper($status);
        $message = "SSFO GovAssist: Your eligibility reassessment request for {$serviceName} has been {$formattedStatus}. Log in to view details.";

        return $this->send($phone, $message);
    }

    /**
     * Send the facilitator's eligibility decision via SMS.
     */
    public function sendAssessmentDecision(User $user, string $serviceName, string $decision): bool
    {
        $phone = $this->resolvePhoneNumber($user);
        if (! $phone) {
            return false;
        }

        $outcome = $decision === 'eligible' ? 'ELIGIBLE' : 'NOT ELIGIBLE';
        $message = "SSFO GovAssist: A facilitator reviewed your eligibility assessment for {$serviceName}: {$outcome}. Log in to view details.";

        return $this->send($phone, $message);
    }

    protected function sendViaLog(string $phoneNumber, string $message): bool
    {
        Log::info("SMS [LOG DRIVER] To: {$phoneNumber} | Message: {$message}");

        return true;
    }

    protected function sendViaUniSms(string $phoneNumber, string $message): bool
    {
        if (empty($this->apiKey)) {
            Log::warning('UniSMS API key is missing. Falling back to log driver.');

            return $this->sendViaLog($phoneNumber, $message);
        }

        $payload = [
            'recipient' => $phoneNumber,
            'content' => $message,
        ];

        if (! empty($this->senderName)) {
            $payload['sender_id'] = $this->senderName;
        }

        $response = Http::withBasicAuth($this->apiKey, '')
            ->acceptJson()
            ->post('https://unismsapi.com/api/sms', $payload);

        if ($response->successful()) {
            Log::info("UniSMS sent successfully to {$phoneNumber}");

            return true;
        }

        Log::error("UniSMS failed ({$response->status()}): ".$response->body());

        return false;
    }

    protected function sendViaSemaphore(string $phoneNumber, string $message): bool
    {
        if (empty($this->apiKey)) {
            Log::warning('Semaphore API key is missing. Falling back to log driver.');

            return $this->sendViaLog($phoneNumber, $message);
        }

        $response = Http::post('https://api.semaphore.co/api/v4/messages', [
            'apikey' => $this->apiKey,
            'number' => $phoneNumber,
            'message' => $message,
            'sendername' => $this->senderName,
        ]);

        if ($response->successful()) {
            Log::info("Semaphore SMS sent successfully to {$phoneNumber}");

            return true;
        }

        Log::error("Semaphore SMS failed ({$response->status()}): ".$response->body());

        return false;
    }

    protected function sendViaTwilio(string $phoneNumber, string $message): bool
    {
        $sid = $this->twilioConfig['sid'] ?? null;
        $token = $this->twilioConfig['auth_token'] ?? null;
        $from = $this->twilioConfig['from'] ?? null;

        if (empty($sid) || empty($token) || empty($from)) {
            Log::warning('Twilio credentials incomplete. Falling back to log driver.');

            return $this->sendViaLog($phoneNumber, $message);
        }

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";
        $response = Http::asForm()
            ->withBasicAuth($sid, $token)
            ->post($url, [
                'From' => $from,
                'To' => $phoneNumber,
                'Body' => $message,
            ]);

        if ($response->successful()) {
            Log::info("Twilio SMS sent successfully to {$phoneNumber}");

            return true;
        }

        Log::error("Twilio SMS failed ({$response->status()}): ".$response->body());

        return false;
    }

    protected function resolvePhoneNumber(User|string|null $recipient): ?string
    {
        if ($recipient instanceof User) {
            return $recipient->contact_number;
        }

        if (is_string($recipient)) {
            return $recipient;
        }

        return null;
    }

    protected function formatPhoneNumber(string $phone): ?string
    {
        $cleaned = preg_replace('/[^0-9+]/', '', trim($phone));

        if (empty($cleaned)) {
            return null;
        }

        // Format 09xxxxxxxxx -> +639xxxxxxxxx for Philippine mobile numbers
        if (preg_match('/^09[0-9]{9}$/', $cleaned)) {
            return '+63'.substr($cleaned, 1);
        }

        // Format 639xxxxxxxxx -> +639xxxxxxxxx
        if (preg_match('/^639[0-9]{9}$/', $cleaned)) {
            return '+'.$cleaned;
        }

        // Already formatted with country code
        if (preg_match('/^\+[1-9][0-9]{7,14}$/', $cleaned)) {
            return $cleaned;
        }

        return $cleaned;
    }
}
