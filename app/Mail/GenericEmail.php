<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GenericEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $sender;

    public $emailSubject;

    public $emailBody;

    public function __construct($sender, string $emailSubject, string $emailBody)
    {
        // dd($emailSubject);
        $this->sender = $sender;
        $this->emailSubject = $emailSubject;
        $this->emailBody = $emailBody;
    }

    // public function build()
    // {
    //     // dd($this->emailSubject);
    //     return $this->markdown('emails.generic_email')
    //         ->with('sender', $this->sender);
    // }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
            from: $this->sender->email,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.generic_email',
            with: [
                'sender' => $this->sender,
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
