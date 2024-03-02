<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('NotificationSettings.in_app', false);
        $this->migrator->add('NotificationSettings.by_email', false);
        $this->migrator->add('NotificationSettings.by_sms', false);

    }
};
