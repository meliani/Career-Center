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

        // Preliminary checks (example: checking network connectivity)
        if (! $this->isNetworkAvailable()) {
            $this->error('Network is not available. Please check your internet connection.');

            return;
        }

        try {
            $client = Client::account('default');
            $client->connect();
            $this->info('Successfully connected to the IMAP server.');
        } catch (\Exception $e) {
            // Enhanced error handling
            $this->error('Failed to connect to the IMAP server. Error: ' . $e->getMessage());

            // Additional suggestions based on common issues
            $this->suggestCommonSolutions();
        }
    }

    /**
     * Example method to check network availability
     */
    private function isNetworkAvailable(): bool
    {
        // This is a simplistic check. Consider using more robust methods.
        $connected = @fsockopen(env('IMAP_HOST'), env('IMAP_PORT'));
        if ($connected) {
            fclose($connected);

            return true;
        }

        return false;
    }

    /**
     * Suggest common solutions for connection issues
     */
    private function suggestCommonSolutions()
    {
        $this->info('Please check the following:');
        $this->info('1. The IMAP server address and port are correct.');
        $this->info('2. Your firewall or antivirus is not blocking the connection.');
        $this->info('3. The credentials and authentication method are correct.');
        // Add more suggestions as needed
    }
}
