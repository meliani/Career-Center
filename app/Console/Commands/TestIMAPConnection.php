<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;

class TestIMAPConnection extends Command
{
    protected $signature = 'email:test-imap-connection';

    protected $description = 'Test IMAP connection settings';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Attempting to connect to IMAP server...');

        try {
            $client = Client::account('default');
            $client->connect();
            $this->info('Successfully connected to the IMAP server.');
        } catch (\Exception $e) {
            $this->error('Failed to connect to the IMAP server. Error: ' . $e->getMessage());
        }
    }
}
