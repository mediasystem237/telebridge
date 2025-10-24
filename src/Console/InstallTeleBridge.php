<?php

namespace Mbindi\Telebridge\Console;

use Illuminate\Console\Command;

class InstallTeleBridge extends Command
{
    protected $signature = 'telebridge:install';
    protected $description = 'Install the TeleBridge package by publishing configuration and running migrations.';

    public function handle()
    {
        $this->info('Publishing TeleBridge Configuration...');
        $this->call('vendor:publish', [
            '--provider' => 'Mbindi\Telebridge\Providers\TeleBridgeServiceProvider',
            '--tag' => 'config'
        ]);

        // Uncomment the following lines when you have migrations
        $this->info('Running TeleBridge Migrations...');
        $this->call('migrate');

        $this->info('TeleBridge installed successfully.');
    }
}
