<?php

namespace Mbindi\Telebridge\Providers;

use Illuminate\Support\ServiceProvider;
use Mbindi\Telebridge\Console\InstallTeleBridge;
use Mbindi\Telebridge\Console\SetWebhookCommand;

class TeleBridgeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/telebridge.php', 'telebridge'
        );

        $this->app->bind('telebridge', function ($app) {
            // This is where the main TeleBridge class would be instantiated.
            // For now, we can return a simple object or a placeholder.
            return new \Mbindi\Telebridge\Services\TelegramClient();
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallTeleBridge::class,
                SetWebhookCommand::class,
            ]);
        }

        $this->loadRoutesFrom(__DIR__.'/../../src/routes/telebridge.php');

        $this->publishes([
            __DIR__.'/../../config/telebridge.php' => config_path('telebridge.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        $this->publishes([
            __DIR__.'/../../database/migrations/' => database_path('migrations')
        ], 'migrations');
    }
}
