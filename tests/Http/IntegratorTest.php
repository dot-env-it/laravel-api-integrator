<?php

declare(strict_types=1);

use DotEnvIt\ApiIntegrator\DataObjects\Integration;
use DotEnvIt\ApiIntegrator\Exceptions\UnregisteredIntegrationException;
use DotEnvIt\ApiIntegrator\Http\Integrator;
use Illuminate\Http\Client\Request;
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
        getFakeRequests(),
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

it('can set custom token', function (): void {
    $integrator = Integrator::make(
        __DIR__ . '/../Stubs/integrations.yaml',
    );

    Http::fake(
        getFakeRequests(),
    );

    expect(
        $request = $integrator->for('example')->withToken('fake')->getUsers(),
    )->toBeInstanceOf(Response::class)
        ->and(
            $request->json('data.users.0.name'),
        )->toBe('John Doe');

    Http::assertSent(fn (Request $request) => $request->hasHeader('Authorization', 'Bearer fake') && ! $request->hasHeader('Authorization', 'Bearer test'));


});

it('can set custom header', function (): void {
    $integrator = Integrator::make(
        __DIR__ . '/../Stubs/integrations.yaml',
    );

    Http::fake(
        getFakeRequests(),
    );

    expect(
        $request = $integrator->for('example')
            ->withHeader('X-CUSTOM-HEADER', 'fake')
            ->withHeader('X-CUSTOM-HEADER-2', 'fake2')
            ->getUsers(),
    )->toBeInstanceOf(Response::class)
        ->and(
            $request->json('data.users.0.name'),
        )->toBe('John Doe');

    Http::assertSent(fn (Request $request) => $request->hasHeader('X-CUSTOM-HEADER', 'fake'));
    Http::assertSent(fn (Request $request) => $request->hasHeader('X-CUSTOM-HEADER-2', 'fake2'));

});

it('can set custom headers', function (): void {
    $integrator = Integrator::make(
        __DIR__ . '/../Stubs/integrations.yaml',
    );

    Http::fake(
        getFakeRequests(),
    );

    expect(
        $request = $integrator->for('example')->withHeaders([
            'X-CUSTOM-HEADER'   => 'fake',
            'X-CUSTOM-HEADER-2' => 'fake2',
        ])->getUsers(),
    )->toBeInstanceOf(Response::class)
        ->and(
            $request->json('data.users.0.name'),
        )->toBe('John Doe');

    Http::assertSent(fn (Request $request) => $request->hasHeader('X-CUSTOM-HEADER', 'fake'));
    Http::assertSent(fn (Request $request) => $request->hasHeader('X-CUSTOM-HEADER-2', 'fake2'));

});


function getFakeRequests(): array
{
    return [
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
    ];
}
