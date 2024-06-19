<?php

namespace App\Console\Commands;

use App\Models\EntrepriseContacts;
use DateTime;
use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;

class ProcessBounceEmails extends Command
{
    protected $signature = 'email:process-bounces --preview';

    protected $description = 'Fetch and process bounce emails';

    public function __construct()
    {
        parent::__construct();

        $this->addOption('preview', null, null, 'Preview bounce emails without processing them');
    }

    public function handle()
    {
        $client = Client::account('default');
        $client->connect();

        $folder = $client->getFolder('INBOX'); // Adjust if your bounces are in a different folder

        // $messages = $folder->query()->unseen()->get();

        // Calculate the start of the previous day
        $yesterday = now()->subDays(10)->startOfDay();

        // Adjust the query to fetch emails since the start of the previous day
        $messages = $folder->query()->since($yesterday->format('d M Y'))->get();

        if ($messages->isEmpty()) {
            $this->info('No bounce emails found from the last day.');

            return;
        }

        foreach ($messages as $message) {
            $subject = $message->getSubject();
            $body = $message->getTextBody();
            $headers = $message->getHeaders();
            $email = $this->getBouncedEmail($message);

            if ($this->isBounce($subject, $body, $headers)) {
                $bounceData = $this->parseBounce($body, $headers);
                if ($bounceData) {
                    if ($this->option('preview')) {
                        $this->previewBounce($message);
                    } else {
                        $this->line("Processing bounce for {$email} ({$bounceData['reason']})");
                        $this->updateEmailStatus($email, $bounceData['reason']);
                    }
                }
            }

            // Mark message as seen
            // $message->setFlag('Seen');
        }

        $client->disconnect();
    }

    private function isBounce($subject, $body, $headers)
    {
        // Detect common bounce indicators
        $patterns = [
            '/delivery[^\n]*failed/i',
            '/user[^\n]*unknown/i',
            '/mailbox[^\n]*full/i',
            '/recipient[^\n]*not[^\n]*found/i',
            '/user[^\n]*does[^\n]*not[^\n]*exist/i',
            '/account[^\n]*disabled/i',
            '/not[^\n]*deliverable/i',
            '/permanent[^\n]*error/i',
            '/undelivered[^\n]*mail/i',
            '/returned[^\n]*to[^\n]*sender/i',
            '/user[^\n]*not[^\n]*known/i',
            '/unrouteable[^\n]*address/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $subject) || preg_match($pattern, $body) || preg_match($pattern, $headers)) {
                return true;
            }
        }

        return false;
    }

    private function parseBounce($body, $headers)
    {
        // Extract the email address and reason from the email body or headers
        preg_match('/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\b/i', $body, $matches);
        $email = $matches[0] ?? null;

        // Determine the bounce reason from common patterns
        $reason = 'unknown';
        $patterns = [
            'user not exists' => '/user[^\n]*does[^\n]*not[^\n]*exist/i',
            'mailbox full' => '/mailbox[^\n]*full/i',
            'delivery failed' => '/delivery[^\n]*failed/i',
            'account disabled' => '/account[^\n]*disabled/i',
            'permanent error' => '/permanent[^\n]*error/i',
            'unrouteable address' => '/unrouteable[^\n]*address/i',
            'domain not found' => '/domain[^\n]*not[^\n]*found/i',
            'spam detected' => '/spam[^\n]*detected/i',
            'message too large' => '/message[^\n]*too[^\n]*large/i',
            'attachment rejected' => '/attachment[^\n]*rejected/i',
            'policy violation' => '/policy[^\n]*violation/i',
            'virus detected' => '/virus[^\n]*detected/i',
            'blacklisted' => '/blacklisted/i',
            'quota exceeded' => '/quota[^\n]*exceeded/i',
            'no such user' => '/no[^\n]*such[^\n]*user/i',
            'over quota' => '/over[^\n]*quota/i',
            'rejected by policy' => '/rejected[^\n]*by[^\n]*policy/i',
            'server busy' => '/server[^\n]*busy/i',
            'service unavailable' => '/service[^\n]*unavailable/i',
            'timeout' => '/timeout/i',
            // Add the new pattern for "invalid recipients"
            'invalid recipients' => '/invalid[^\n]*recipients/i',
        ];

        foreach ($patterns as $key => $pattern) {
            if (preg_match($pattern, $body) || preg_match($pattern, $headers)) {
                $reason = $key;

                break;
            }
        }

        return $email ? ['email' => $email, 'reason' => $reason] : null;
    }

    private function updateEmailStatus($bouncedEmail, $reason)
    {
        $contact = EntrepriseContacts::where('email', $bouncedEmail)->first();

        if ($contact) {
            $contact->number_of_bounces++;
            $contact->is_account_disabled = true;
            $contact->bounce_reason = $reason;
            $contact->save();
        }

    }

    private function previewBounce($message)
    {
        $date = new DateTime($message->getDate());
        $from = $message->getFrom()[0] ? $message->getFrom()[0]->mail : 'Unknown sender';
        // $this->error("Subject: {$message->getSubject()} | From: {$from} | Date: " . $date->format('Y-m-d H:i:s'));
        $this->error("{$this->getBouncedEmail($message)}From: {$from} | Date: " . $date->format('Y-m-d H:i:s'));
    }

    private function getBouncedEmail($message)
    {
        // Extract the email address and reason from the email body or headers
        // look for the email address in the body
        preg_match('/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\b/i', $message->getTextBody(), $matches);
        $email = $matches[0] ?? null;

        // look for the email address in the headers
        if (! $email) {
            preg_match('/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\b/i', $message->getHeaders(), $matches);
            $email = $matches[0] ?? null;
        }

        return $email;
    }
}
