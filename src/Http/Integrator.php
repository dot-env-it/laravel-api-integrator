<?php

declare(strict_types=1);

namespace DotEnvIt\ApiIntegrator\Http;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Yaml\Yaml;
use DotEnvIt\ApiIntegrator\DataObjects\Integration;
use DotEnvIt\ApiIntegrator\Enums\AuthType;
use DotEnvIt\ApiIntegrator\Exceptions\UnregisteredIntegrationException;

final class Integrator
{
    /**
     * @param array<string,Integration> $integrations
     */
    public function __construct(
        public array $integrations,
    ) {}

    /**
     * @param string $key
     * @return PendingRequest
     * @throw UnregisteredIntegrationException
     */
    public function for(string $key): PendingRequest
    {
        if (! \array_key_exists($key, $this->integrations)) {
            throw new UnregisteredIntegrationException(
                message: "Cannot call [$key], this integration has yet to be registered.",
            );
        }

        $request = Http::baseUrl(
            url: $this->integrations[$key]->url,
        );

        if (null !== $this->integrations[$key]->auth) {
            match ($this->integrations[$key]->auth->type) {
                AuthType::Bearer => $request->withToken(
                    token: $this->integrations[$key]->auth->value,
                ),
                AuthType::Header => $request->withHeader(
                    name: $this->integrations[$key]->auth->name ?? 'Authorization',
                    value: $this->integrations[$key]->auth->value,
                ),
            };
        }

        return $request;
    }

    public static function make(string $config): Integrator
    {
        $yaml = Yaml::parse(
            input: \file_get_contents($config),
            flags: Yaml::PARSE_CUSTOM_TAGS,
        );

        $integrations = [];

        foreach ($yaml['integrations'] as $name => $integration) {
            $integrations[$name] = Integration::fromArray(
                data: $integration,
            );
        }

        return new Integrator(
            integrations: $integrations,
        );
    }
}
