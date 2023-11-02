<?php

declare(strict_types=1);

namespace DotEnvIt\ApiIntegrator\Providers;

use DotEnvIt\ApiIntegrator\Console\Commands\InstallCommand;
use Illuminate\Support\ServiceProvider;
use DotEnvIt\ApiIntegrator\Http\Integrator;

use function base_path;
use function config_path;

final class ApiIntegratorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/api-integrator.php'     => config_path('api-integrator.php'),
            __DIR__ . '/../../tests/Stubs/integrations.yaml' => base_path('integrations.yaml'),
        ], 'api-integrator-config');

        if($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/api-integrator.php',
            'api-integrator',
        );

        /** @var array{definition:string} $config */
        $config = config('api-integrator');

        $this->app->bind(
            abstract: Integrator::class,
            concrete: fn () => Integrator::make(
                config: $config['definition'],
            )
        );
    }
}
