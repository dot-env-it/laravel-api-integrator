# Laravel API Integrator


```yaml
integrations:
  github:
    url: 'https://api.github.com/'
    auth:
      type: Bearer
      value: !env 'GITHUB_TOKEN'
      name: 'Authorization'

  example:
    url: 'https://api.example.com'
    auth:
      type: Header
      token: !env 'EXAMPLE_TOKEN'
      name: 'X-API-KEY'
```

```php
use DotEnvIt\ApiIntegrator\Facades\Integration;

Integration::for('github')->get('something')->json();
```

```php
Http::baseUrl('https://api.github.com')->withToken(
    token: '1234-1234-1234-1234',
)->get('something')->json();
```
