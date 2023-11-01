<?php

declare(strict_types=1);

namespace DotEnvIt\ApiIntegrator\DataObjects;

final readonly class Integration
{
    public function __construct(
        public string $url,
        public null|Auth $auth,
    ) {}

    /**
     * @param array{url:string,auth:null|array{type:string,value:string,name:null|string}} $data
     * @return Integration
     */
    public static function fromArray(array $data): Integration
    {
        return new Integration(
            url: $data['url'],
            auth: $data['auth'] ?
                Auth::fromArray(
                    data: $data['auth'],
                ) : null,
        );
    }
}
