<?php

namespace App\Jobs;

use App\Mail\SecondYearCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMassMail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $delay;

    protected $email;

    protected $name;

    protected $recipientCategory;

    public $queue;

    public function __construct($email, $name, $recipientCategory)
    {
        $this->email = $email;
        $this->name = $name;
        $this->recipientCategory = $recipientCategory;
        $this->delay = now()->addSeconds(3);
        $this->queue = 'emails';

    }

    public function onQueue($queue)
    {
        // hard code this job to the posts queue
        $this->queue = 'emails';

        return $this;
    }

    public function handle(): void
    {
        Mail::to($this->email)->send(new SecondYearCampaign($this->name, $this->recipientCategory));
    }
}
