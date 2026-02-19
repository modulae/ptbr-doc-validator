# Brazilian document validators for Laravel.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/modulae/ptbr-doc-validator.svg?style=flat-square)](https://packagist.org/packages/modulae/ptbr-doc-validator)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/modulae/ptbr-doc-validator/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/modulae/ptbr-doc-validator/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/modulae/ptbr-doc-validator/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/modulae/ptbr-doc-validator/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/modulae/ptbr-doc-validator.svg?style=flat-square)](https://packagist.org/packages/modulae/ptbr-doc-validator)

A lightweight Laravel package that provides validators and utilities for common Brazilian documents. The first supported document is CNPJ, including support for alphanumeric variants that are to be introduced in June 2026. It includes:

- A `cnpj` validation rule registered with Laravel's `Validator` facade.
- A `Cnpj` value object with helpers to normalize, format, cast in Eloquent models, and validate values.
- A `cnpjToString` helper to normalize strings and arrays of CNPJ values.
- Localized validation error messages (EN and PT-BR).

## Installation

Install via Composer:

```bash
composer require modulae/ptbr-doc-validator
```

Publish translations (optional, if you want to customize messages):

```bash
php artisan vendor:publish --tag="ptbr-doc-validator-translations"
```

## Usage

### 1) Using the built-in `cnpj` validation rule

```php
use Illuminate\Support\Facades\Validator;

$validator = Validator::make(
    ['cnpj' => 'T6.JSP.XPS/0001-11'],
    ['cnpj' => 'cnpj']
);

$validator->passes(); // true/false
```

### 2) Using the `Cnpj` value object

```php
use Modulae\PTBRDocValidator\ValueObjects\Cnpj;

$cnpj = new Cnpj('T6.JSP.XPS/0001-11');
$cnpj->isValid();     // bool
$cnpj->raw();         // "T6JSPXPS000111"
$cnpj->formatted();   // "T6.JSP.XPS/0001-11"
(string) $cnpj;       // "T6JSPXPS000111"
```

Eloquent cast example:

```php
class Company extends Model
{
    protected $casts = [
        'cnpj' => \Modulae\PTBRDocValidator\ValueObjects\Cnpj::class,
    ];
}
```

### 3) Using the `cnpjToString` helper

```php
use function Modulae\PTBRDocValidator\cnpjToString;

cnpjToString('T6.JSP.XPS/0001-11');
// => "T6JSPXPS000111"

cnpjToString(['32.332.643/0001-29', 'T6.JSP.XPS/0001-11']);
// => ['32332643000129', 'T6JSPXPS000111']
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
