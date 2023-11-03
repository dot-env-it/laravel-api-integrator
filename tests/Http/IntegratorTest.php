<?php

declare(strict_types=1);

use Illuminate\Http\Client\PendingRequest;
use DotEnvIt\ApiIntegrator\DataObjects\Integration;
use DotEnvIt\ApiIntegrator\Exceptions\UnregisteredIntegrationException;
use DotEnvIt\ApiIntegrator\Http\Integrator;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

it('can call the static make method', function (): void {
    expect(
        Integrator::make(
            __DIR__ . '/../Stubs/integrations.yaml',
        ),
    )->toBeInstanceOf(Integrator::class);
});

it('can create the integrations from the configuration', function (): void {
    $integrator = Integrator::make(
        __DIR__ . '/../Stubs/integrations.yaml',
    );

    expect(
        $integrator->integrations['github'],
    )->toBeInstanceOf(Integration::class);
});

it('can create a pending request', function (): void {
    $integrator = Integrator::make(
        __DIR__ . '/../Stubs/integrations.yaml',
    );

    expect(
        $integrator->for(
            'github',
        ),
    )->toBeInstanceOf(Integrator::class);
});

it('will throw an exception if the integration is not registered', function (): void {
    $integrator = Integrator::make(
        config: __DIR__ . '/../Stubs/integrations.yaml',
    );

    $integrator->for(
        key: 'test',
    );
})->throws(UnregisteredIntegrationException::class);

it('can install the package', function (): void {
    $this->artisan('api-integrator:install')
        ->expectsOutput('Initializing API Integrator...')
        ->expectsOutput('API Integrator installed successfully.')
        ->assertExitCode(0);

    expect(
        Integrator::make(
            config: config('api-integrator.definition'),
        ),
    )->toBeInstanceOf(Integrator::class);
});

it('can call the magic methods', function (): void {
    $integrator = Integrator::make(
        __DIR__ . '/../Stubs/integrations.yaml',
    );

    Http::fake(
        [
            'api.example.com/users' => Http::response(
                [
                    'data' => [
                        'users' => [
                            [
                                'id'   => 1,
                                'name' => 'John Doe',
                            ],
                        ],
                    ],
                ],
            ),
            'api.example.com/users/1/posts/2/*' => Http::response(
                [
                    'data' => [
                        'posts' => [
                            [
                                'id'   => 2,
                                'name' => 'post 2',
                            ],
                        ],
                    ],
                ],
            ),
        ],
    );

    expect(
        $request = $integrator->for('example')->getUsers(),
    )->toBeInstanceOf(Response::class)
        ->and(
            $request->json('data.users.0.name'),
        )->toBe('John Doe')
        ->and(
            $request = $integrator->for('example')->getUsers_userId_posts_postId([
                'userId' => 1,
                'postId' => 2,
            ])
        )->toBeInstanceOf(Response::class)
        ->and(
            $request->json('data.posts.0.name'),
        )->toBe('post 2');

});
