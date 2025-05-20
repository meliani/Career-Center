<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('display.display_plannings', true);
        $this->migrator->add('display.display_project_reviewers', true);
    }
};
