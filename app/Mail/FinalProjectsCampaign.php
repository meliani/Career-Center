<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FinalProjectsCampaign extends Mailable
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
            subject: "Stages PFE pour les élèves ingénieurs de l'INPT",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        if ($this->recipientType === 'Alumni') {
            return new Content(
                markdown: 'emails.internship-campaigns.final_projects_campaign_alumni',
                with: [
                    'name' => $this->name,
                ],
            );
        }

        return new Content(
            markdown: 'emails.internship-campaigns.final_projects_campaign_supervisors',
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
