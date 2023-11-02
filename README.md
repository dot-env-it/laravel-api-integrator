# Laravel API Integrator

## Installation
```bash
composer require dot-env-it/laravel-api-integrator
```

Run `install` command to publish config file and yml file

```bash
php artisan api-integrator:install
```
This command will create `api-integrator.yml` file at root of project and `api-integrator.php` file at `config` folder

Sample `api-integrator.yml` file
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
#### You can pass environment variables to yml file using `!env` tag

## USAGE
 
```php
use DotEnvIt\ApiIntegrator\Facades\Integration;

//api url https://api.github.com/foo
Integration::for('github')->get('foo')->json();

//api url https://api.example.com/foo
Integration::for('example')->get('foo')->json();
```
This package also provides a magic method for each http method
```php
use DotEnvIt\ApiIntegrator\Facades\Integration;

//api url https://api.example.com/foo
Integration::for('example')->getFoo()->json();

//api url https://api.example.com/foo/1
Integration::for('example')->getFoo_id(['id' => 1])->json();

//api url https://api.example.com/foo/1/bar/2
Integration::for('example')->getFoo_foo_id_bar_bar_id(['foo_id' => 1, 'bar_id' => 2])->json();

//api url https://api.example.com/foo/1?foo=bar&bar=baz
Integration::for('example')->getFoo_id(['id' => 1, 'foo' => 'bar', 'bar' => 'baz'])->json();

//POST api url https://api.example.com/foo/1/bar/2/baz
Integration::for('example')->postFoo_foo_id_bar_bar_id_baz(['foo_id' => 1, 'bar_id' => 2])->json();
```
