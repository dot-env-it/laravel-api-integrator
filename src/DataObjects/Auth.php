<?php

declare(strict_types=1);

namespace DotEnvIt\ApiIntegrator\DataObjects;

use Symfony\Component\Yaml\Tag\TaggedValue;
use DotEnvIt\ApiIntegrator\Enums\AuthType;

use function env;

final class Auth
{
    public function __construct(
        public AuthType $type,
        public string $value,
        public null|string $name = null,
    ) {
    }

    /**
     * @param array{type:string,value:TaggedValue,name:null|string} $data
     * @return Auth
     */
    public static function fromArray(array $data): Auth
    {
        info('Auth::fromArray', ['data' => $data, 'env' => env($data['value']->getValue()), 'getenv' => config('api-integrator.token')]);

        return new Auth(
            type: AuthType::from(
                value: $data['type'],
            ),
            value: config('api-integrator.token') ?? '',
            name: $data['name'] ?? '',
        );
    }
}
