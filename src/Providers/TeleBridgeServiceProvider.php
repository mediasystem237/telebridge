<?php

namespace Mbindi\Telebridge\Providers;

use Illuminate\Support\ServiceProvider;
use Mbindi\Telebridge\Console\InstallTeleBridge;
use Mbindi\Telebridge\Console\SetWebhookCommand;
use Mbindi\Telebridge\Console\TestTelegramConnection;
use Mbindi\Telebridge\Console\PollingCommand;
use Mbindi\Telebridge\Console\SetupCommandsCommand;
use Mbindi\Telebridge\TeleBridgeManager;
use Mbindi\Telebridge\Notifications\TelegramChannel;

class TeleBridgeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/telebridge.php', 'telebridge'
        );

        // Singleton du TelegramClient
        $this->app->singleton(\Mbindi\Telebridge\Services\TelegramClient::class, function ($app) {
            return new \Mbindi\Telebridge\Services\TelegramClient();
        });

        // Singleton du Manager (pour Facade)
        $this->app->singleton('telebridge', function ($app) {
            return new TeleBridgeManager(
                $app->make(\Mbindi\Telebridge\Services\TelegramClient::class)
            );
        });

        // Enregistrer le canal de notification
        $this->app->make('Illuminate\Notifications\ChannelManager')
            ->extend('telegram', function ($app) {
                return new TelegramChannel(
                    $app->make(\Mbindi\Telebridge\Services\TelegramClient::class)
                );
            });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallTeleBridge::class,
                SetWebhookCommand::class,
                TestTelegramConnection::class,
                PollingCommand::class,
                SetupCommandsCommand::class,
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
