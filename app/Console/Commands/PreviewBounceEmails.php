<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;

class PreviewBounceEmails extends Command
{
    protected $signature = 'email:preview-bounces';

    protected $description = 'Preview bounce emails without processing them';

    public function handle()
    {
        $this->info('Fetching bounce emails from the last day...');

        $client = Client::account('default');
        $client->connect();

        $folder = $client->getFolder('INBOX'); // Adjust if your bounces are in a different folder

        // Calculate the start of the previous day
        $yesterday = now()->subDay()->startOfDay();

        // Adjust the query to fetch emails since the start of the previous day
        $messages = $folder->query()->since($yesterday->format('d M Y'))->get();

        if ($messages->isEmpty()) {
            $this->info('No bounce emails found from the last day.');

            return;
        }

        foreach ($messages as $message) {
            $date = new DateTime($message->getDate());
            $from = $message->getFrom()[0] ? $message->getFrom()[0]->mail : 'Unknown sender';
            $this->line("Subject: {$message->getSubject()} | From: {$from} | Date: " . $date->format('Y-m-d H:i:s'));
        }
    }
}
