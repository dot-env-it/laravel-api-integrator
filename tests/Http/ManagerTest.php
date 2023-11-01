<?php

declare(strict_types=1);

use Illuminate\Http\Client\PendingRequest;
use DotEnvIt\ApiIntegrator\DataObjects\Integration;
use DotEnvIt\ApiIntegrator\Exceptions\UnregisteredIntegrationException;
use DotEnvIt\ApiIntegrator\Http\Integrator;

it('can call the static make method', function (): void {
    expect(
        Integrator::make(
            config: __DIR__ . '/../Stubs/integrations.yaml',
        ),
    )->toBeInstanceOf(Integrator::class);
});

it('can create the integrations from the configuration', function (): void {
    $integrator = Integrator::make(
        config: __DIR__ . '/../Stubs/integrations.yaml',
    );

    expect(
        $integrator->integrations['github'],
    )->toBeInstanceOf(Integration::class);
});

it('can create a pending request', function (): void {
    $integrator = Integrator::make(
        config: __DIR__ . '/../Stubs/integrations.yaml',
    );

    expect(
        $integrator->for(
            key: 'github',
        ),
    )->toBeInstanceOf(PendingRequest::class);
});

it('will throw an exception if the integration is not registered', function (): void {
    $integrator = Integrator::make(
        config: __DIR__ . '/../Stubs/integrations.yaml',
    );

    $integrator->for(
        key: 'test',
    );
})->throws(UnregisteredIntegrationException::class);
