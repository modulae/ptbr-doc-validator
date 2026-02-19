<?php

namespace Modulae\PTBRDocValidator;

use Illuminate\Support\Facades\Validator;
use Modulae\PTBRDocValidator\ValueObjects\Cnpj;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PTBRDocValidatorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('ptbr-doc-validator')
            ->hasTranslations();
    }

    public function bootingPackage(): void
    {
        Validator::extend('cnpj', fn ($attribute, $value): bool => Cnpj::isValidValue((string) $value));
    }
}
