<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Student;

class StudentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $emailSubject;
    public $emailBody;

    public function __construct(Student $student, string $emailSubject, string $emailBody)
    {
        // dd($emailSubject);
        $this->student = $student;
        $this->emailSubject = $emailSubject;
        $this->emailBody = $emailBody;
    }

    public function build()
    {
        // dd($this->emailSubject);
        return $this->markdown('emails.student_info')
            ->with('student', $this->student);
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
            markdown: 'emails.student_info',
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