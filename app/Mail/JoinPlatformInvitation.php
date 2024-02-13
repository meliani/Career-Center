<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JoinPlatformInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $emailSubject;

    public $emailBody;

    public function __construct($user)
    {
        $this->user = $user;
        $this->emailSubject = 'Invitation automatique à rejoindre INPT Entreprises';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.join_platform_invitation',
            with: [
                'user' => $this->user,
            ],
        );
    }
    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
