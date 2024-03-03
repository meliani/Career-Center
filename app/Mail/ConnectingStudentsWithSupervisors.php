<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class ConnectingStudentsWithSupervisors extends Mailable implements ShouldQueue
    // Notification
{
    use Queueable;
    use SerializesModels;

    public $project;

    public $emailSubject;

    public $emailBody;

    public function __construct(Project $project)
    {
        // dd($project);
        $this->project = $project;
        $this->emailSubject = 'Encadrant pour votre stage de Projet de Fin d\'Études';
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
            markdown: 'emails.connecting_student_with_supervisors',
            with: [
                'user' => $this->project,
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
