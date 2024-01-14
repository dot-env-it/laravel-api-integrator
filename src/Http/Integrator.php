<?php

declare(strict_types=1);

namespace DotEnvIt\ApiIntegrator\Http;

use DotEnvIt\ApiIntegrator\DataObjects\Integration;
use DotEnvIt\ApiIntegrator\Enums\AuthType;
use DotEnvIt\ApiIntegrator\Exceptions\InvalidActionException;
use DotEnvIt\ApiIntegrator\Exceptions\UnregisteredIntegrationException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

use function array_key_exists;
use function file_get_contents;

final class Integrator
{
    /**
     * @param array<string,Integration> $integrations
     */
    public function __construct(
        public array          $integrations,
        public PendingRequest $request = new PendingRequest(),
    ) {
    }

    public function __call(string $name, array $arguments): Response
    {
        empty($arguments) && $arguments = [[]];

        if (method_exists(PendingRequest::class, $name)) {
            return $this->request->{$name}(...$arguments);
        }

        $actions = $this->getActionInfo($name, $arguments[0]);

        if (
            empty($actions['method'])
            || empty($actions['path'])
            || ! method_exists($this->request, $actions['method'])
        ) {
            throw new InvalidActionException('Invalid action');
        }

        return $this->request->{$actions['method']}(
            $actions['path'],
            $arguments[0],
        );
    }

    /**
     * @param string $action
     * @param array|null $arguments
     * @return array{method: string, path: string}
     */
    private function getActionInfo(string $action, ?array $arguments = null): array
    {
        $arguments ??= [];

        $actions = explode('_', Str::snake($action));

        return [
            'method' => array_shift($actions),
            'path'   => $this->transformPath(
                path: Str::slug(
                    implode('-', $actions)
                ),
                arguments: $arguments,
            ),
        ];
    }

    /**
     * @param string $path
     * @param array|null $arguments
     * @return string
     */
    private function transformPath(string $path, ?array $arguments = null): string
    {
        foreach ($arguments as $key => $argument) {
            if (is_array($argument)) {
                continue;
            }

            $key = Str::of($key)->snake()->slug();

            $path = trim(str_replace(
                search: "-{$key}-",
                replace: "/{$argument}/",
                subject: $path . '-',
            ), '-');
        }

        return $path;
    }

    /**
     * @param string $key
     * @return Integrator
     * @throw UnregisteredIntegrationException
     */
    public function for(string $key): self
    {
        if ( ! array_key_exists($key, $this->integrations)) {
            throw new UnregisteredIntegrationException(
                message: "Cannot call [{$key}], this integration has yet to be registered.",
            );
        }

        $this->request = Http::baseUrl(
            url: $this->integrations[$key]->url,
        );

        if ( ! app()->isProduction()) {
            $this->request->withOptions(['verify' => false]);
        }

        if (null !== $this->integrations[$key]->auth) {
            match ($this->integrations[$key]->auth->type) {
                AuthType::Bearer => $this->request->withToken(
                    token: $this->integrations[$key]->auth->value,
                ),
                AuthType::Header => $this->request->withHeader(
                    name: $this->integrations[$key]->auth->name ?? 'Authorization',
                    value: $this->integrations[$key]->auth->value,
                ),
            };
        }

        return $this;
    }

    public function withToken(string $token): self
    {
        $this->request->withToken(
            token: $token,
        );

        return $this;
    }

    public function asForm(): self
    {
        $this->request->asForm();

        return $this;
    }

    public function asJson(): self
    {
        $this->request->asJson();

        return $this;
    }

    public function asMultipart(): self
    {
        $this->request->asMultipart();

        return $this;
    }

    public function asOctetStream(): self
    {
        $this->request->asOctetStream();

        return $this;
    }

    public function asXml(): self
    {
        $this->request->asXml();

        return $this;
    }

    public function attach(string|array $name, string $contents = '', string|null $filename = null, array $headers = []): self
    {
        $this->request->attach(
            name: $name,
            contents: $contents,
            filename: $filename,
            headers: $headers,
        );

        return $this;
    }

    public function withOptions(array $options): self
    {
        $this->request->withOptions(
            options: $options,
        );

        return $this;
    }

    public function withCookies(array $cookies, string $domain): self
    {
        $this->request->withCookies(
            cookies: $cookies,
            domain: $domain,
        );

        return $this;
    }

    public function withCookie(string $name, string $value): self
    {
        $this->request->withCookie(
            name: $name,
            value: $value,
        );

        return $this;
    }

    public function withHeader(string $name, string $value): self
    {
        $this->request->withHeader(
            name: $name,
            value: $value,
        );

        return $this;
    }

    public function withHeaders(array $headers): self
    {
        $this->request->withHeaders(
            headers: $headers,
        );

        return $this;
    }

    public static function make(string $config): Integrator
    {
        $yaml = Yaml::parse(
            input: file_get_contents($config),
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
