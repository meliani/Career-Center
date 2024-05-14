<?php

use App\Models\Student;
use Illuminate\Support\Facades\App;

return [
    'datetime_format' => 'd/m/Y H:i:s',
    'date_format' => 'd/m/Y',

    // 'activity_resource' => \Z3d0X\FilamentLogger\Resources\ActivityResource::class,
    'activity_resource' => \App\Filament\Administration\Resources\ActivityResource::class,

    'resources' => [
        'enabled' => true,
        'log_name' => 'Resource',
        'logger' => \Z3d0X\FilamentLogger\Loggers\ResourceLogger::class,
        'color' => 'success',
        'exclude' => [
            // \App\Filament\Administration\Resources\UserResource::class,
        ],
    ],

    'access' => [
        'enabled' => true,
        'logger' => \Z3d0X\FilamentLogger\Loggers\AccessLogger::class,
        'color' => 'success',
        'log_name' => 'Access',
    ],

    'notifications' => [
        'enabled' => true,
        'logger' => \Z3d0X\FilamentLogger\Loggers\NotificationLogger::class,
        'color' => null,
        'log_name' => 'Notification',
    ],

    'models' => [
        'enabled' => true,
        'log_name' => 'Model',
        'color' => 'warning',
        'logger' => \Z3d0X\FilamentLogger\Loggers\ModelLogger::class,
        'register' => [
            // App\Models\User::class,
            Student::class,
        ],
    ],

    'custom' => [
        // [
        //     'log_name' => 'Custom',
        //     'color' => 'primary',
        // ]
    ],
];
