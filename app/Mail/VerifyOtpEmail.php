<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyOtpEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otpCode;

    public string $subjectLine;

    public string $introText;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $otpCode,
        string $subjectLine = 'Verify Your Email Address',
        string $introText = 'Thank you for registering. Please use the following One-Time Password (OTP) to verify your email address.',
    ) {
        $this->otpCode = $otpCode;
        $this->subjectLine = $subjectLine;
        $this->introText = $introText;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.verify-otp',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
