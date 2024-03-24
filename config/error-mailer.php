<?php

return [
    'email' => [
        'recipient' => 'elmeliani@inpt.ac.ma',
        'subject' => 'An error was occured - ' . env('APP_NAME'),
    ],

    'disabledOn' => [
        // 'local',
    ],

    'cacheCooldown' => 10, // in minutes
];
