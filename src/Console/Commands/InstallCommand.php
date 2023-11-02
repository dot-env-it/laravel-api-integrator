<?php

declare(strict_types=1);

namespace DotEnvIt\ApiIntegrator\Console\Commands;

use Illuminate\Console\Command;

final class InstallCommand extends Command
{
    protected $signature = 'api-integrator:install';

    protected $description = 'Initialize the API Integrator and publishes integrations.yaml file';

    public function handle(): void
    {
        $this->info('Initializing API Integrator...');

        $this->call('vendor:publish', [
            '--provider' => 'DotEnvIt\ApiIntegrator\Providers\ApiIntegratorServiceProvider',
            '--tag'      => 'api-integrator-config',
        ]);

        $this->info('API Integrator installed successfully.');
    }
}
