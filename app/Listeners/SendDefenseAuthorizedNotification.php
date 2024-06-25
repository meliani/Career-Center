<?php

namespace App\Listeners;

use App\Events\DefenseAuthorized;
use App\Notifications\DefenseAuthorizedNotification;
use Illuminate\Support\Facades\Notification;

class SendDefenseAuthorizedNotification
{
    // public $emails;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        // $this->emails = $emails;
    }

    /**
     * Handle the event.
     */
    public function handle(DefenseAuthorized $event)
    {
        // $administrators = \App\Models\User::administrators();
        // $programUsers = \App\Models\User::where('assigned_program', $event->project->internship_agreement->student->program->value)->get();

        // $notifiables = $administrators->merge($programUsers);
        Notification::route('mail', $event->emails)
        ->notify(new DefenseAuthorizedNotification($event->project));
        // Notification::send($event->emails, new DefenseAuthorizedNotification($event->project));
    }
}
