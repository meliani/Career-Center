<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SecondYearCampaign extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $name;

    public $recipientType;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $recipientType)
    {
        $this->name = $name;
        $this->recipientType = $recipientType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Demande de stages techniques pour les élèves ingénieurs de l'INPT",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        if ($this->recipientType === 'Alumni') {
            return new Content(
                markdown: 'emails.second_year_campaign_alumni',
                with: [
                    'name' => $this->name,
                ],
            );
        }

        return new Content(
            markdown: 'emails.second_year_campaign_supervisors',
            with: [
                'name' => $this->name,
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
