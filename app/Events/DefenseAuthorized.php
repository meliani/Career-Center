<?php

namespace App\Events;

use App\Models\Project;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DefenseAuthorized
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $project;

    public $emails;

    public function __construct(Project $project, array $emails)
    {
        $this->project = $project;
        $this->emails = $emails;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('projects'),
        ];
    }
}
