<?php

declare(strict_types=1);

namespace DotEnvIt\ApiIntegrator\Providers;

use Illuminate\Support\ServiceProvider;
use DotEnvIt\ApiIntegrator\Http\Integrator;

final class PackageServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/api-integrator.php' => \config_path('api-integrator.php'),
        ], 'api-integrator-config');
    }

    public function register(): void
    {
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
