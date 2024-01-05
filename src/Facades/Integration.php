<?php

declare(strict_types=1);

namespace DotEnvIt\ApiIntegrator\Facades;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Facade;
use DotEnvIt\ApiIntegrator\Http\Integrator;

/**
 * @method static Integrator for(string $key)
 */
final class Integration extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Integrator::class;
    }
}
