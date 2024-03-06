<?php

namespace App\Models;

use jdavidbakr\MailTracker\Concerns\IsSentEmailUrlClickedModel;
use jdavidbakr\MailTracker\Contracts\SentEmailUrlClickedModel;
use jdavidbakr\MailTracker\Model\SentEmailUrlClicked as SentEmailUrlClickedParent;

class SentEmailUrlClicked extends SentEmailUrlClickedParent implements SentEmailUrlClickedModel
{
    use IsSentEmailUrlClickedModel;

    protected $table = 'sent_emails_url_clicked';

    protected $fillable = [
        'sent_email_id',
        'url',
        'hash',
        'clicks',
    ];
}
