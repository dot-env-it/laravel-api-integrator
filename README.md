# Laravel API Integrator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dot-env-it/laravel-api-integrator.svg?style=flat-square)](https://packagist.org/packages/dot-env-it/laravel-api-integrator)
[![Total Downloads](https://img.shields.io/packagist/dt/dot-env-it/laravel-api-integrator.svg?style=flat-square)](https://packagist.org/packages/dot-env-it/laravel-api-integrator)
![GitHub Actions](https://github.com/dot-env-it/laravel-api-integrator/actions/workflows/laravel.yml/badge.svg)

Package to simplify third-party api integrations. Make API calls like they are part of your code with this package. No need to remember base url or path of any API. Just call it like `Integration::for('api-provider')->getSomethingCool()->json();` 

## Become a sponsor

[Click to Sponsor](https://github.com/sponsors/Jagdish-J-P)

Your support allows me to keep this package free, up-to-date and maintainable. Alternatively, you can **[spread the word!](http://twitter.com/share?text=I+am+using+this+cool+PHP+package&url=https://github.com/dot-env-it/laravel-api-integrator&hashtags=PHP,Laravel)**

## Installation
```bash
composer require dot-env-it/laravel-api-integrator
```

Run `install` command to publish config file and yml file

```bash
php artisan api-integrator:install
```
This command will create `api-integrator.yaml` file at root of project and `api-integrator.php` file at `config` folder

Sample `api-integrator.yaml` file
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
#### You can pass environment variables to yaml file using `!env` tag

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
## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email jagdish.j.ptl@gmail.com instead of using the issue tracker.

## Credits

- [Jagdish-J-P](https://github.com/jagdish-j-p)
- [dot-env-it](https://github.com/dot-env-it)
- [Just Steve King](https://github.com/JustSteveKing)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
