<?php

namespace App\Mail;

use App\Models\UserChecklist;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationApprovedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public UserChecklist $checklist;

    /**
     * Create a new message instance.
     */
    public function __construct(UserChecklist $checklist)
    {
        $this->checklist = $checklist;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Application Approved - GovAssist',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.application-approved',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
