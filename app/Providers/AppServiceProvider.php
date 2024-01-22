<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Illuminate\Support\Facades\App;
use Illuminate\Routing\UrlGenerator;
use Filament\Tables\Columns\TextColumn;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Connection;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(UrlGenerator $url): void
    {
        if (App::environment('production')) {
            $url->formatScheme('https');
        }
        Model::unguard();
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar', 'en', 'fr']); // also accepts a closure
        });
        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch
                ->visible(fn (): bool => auth()->user()?->hasAnyRole([
                    'Administrator',
                    'SuperAdministrator',
                ]));
        });

        TextColumn::configureUsing(function (TextColumn $column): void {
            $column
                ->toggleable()
                ->searchable();
        });
        /* Add Enum support to DBAL */
        Type::addType('enum', StringType::class);

        // $platform = Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform();
        $platform = Schema::getConnection()->getDoctrineConnection()->getDatabasePlatform();
        // $connection = $this->app->make('db')->connection();
        // $platform = $connection->getDoctrineConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');


        /* Jobs / Queue configuration */
        RateLimiter::for('default', function (object $job) {
            return $job->user->vipCustomer()
                ? Limit::none()
                : Limit::perMinute(3)->by($job->user->id);
        });
        /* end of Jobs/Queues Config  */
    }
}
