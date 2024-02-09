<?php

// config for Rabol/FilamentLogviewer
return [
    'navigation_group' => 'System',
    'model_class' => \Rabol\FilamentLogviewer\Models\LogFile::class,
    'policy_class' => \App\Policies\LogFilePolicy::class,
];
