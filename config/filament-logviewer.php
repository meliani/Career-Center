<?php

// config for Rabol/FilamentLogviewer
return [
    'navigation_group' => 'System',
    'model_class' => App\Models\LogFile::class,
    'policy_class' => App\Policies\LogFilePolicy::class,
];
