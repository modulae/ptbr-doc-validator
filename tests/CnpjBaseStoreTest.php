<?php

use Modulae\PTBRDocValidator\ValueObjects\Cnpj;

it('formats base number stripping mask and padding to 8 chars', function () {
    expect(Cnpj::formatBaseNumber('09165609'))->toBe('09165609')
        ->and(Cnpj::formatBaseNumber('9165609'))->toBe('09165609')
        ->and(Cnpj::formatBaseNumber('12.ABC.345'))->toBe('12ABC345');
});

it('formats store number preserving alphanumeric chars and padding to 4 chars', function () {
    expect(Cnpj::formatStoreNumber('0001'))->toBe('0001')
        ->and(Cnpj::formatStoreNumber('1'))->toBe('0001')
        ->and(Cnpj::formatStoreNumber('AB01'))->toBe('AB01');
});

it('generates a CNPJ preserving alphanumeric store segment — regression for formatStoreNumber stripping letters', function () {
    $cnpj = Cnpj::generateFromBaseStore('12345678', 'A001');

    expect(substr($cnpj->raw(), 8, 4))->toBe('A001');
});

it('calculates the first check digit for a numeric CNPJ base+store', function () {
    expect(Cnpj::calculateDigit('091656090001'))->toBe(9);
});

it('calculates the second check digit for a numeric CNPJ base+store+first', function () {
    expect(Cnpj::calculateDigit('0916560900019'))->toBe(5);
});

it('generates a numeric CNPJ from base and store numbers', function () {
    $cnpj = Cnpj::generateFromBaseStore('09165609', '0001');

    expect($cnpj->raw())->toBe('09165609000195')
        ->and($cnpj->isValid())->toBeTrue();
});

it('generates a numeric CNPJ padding short base and store inputs', function () {
    $cnpj = Cnpj::generateFromBaseStore('9165609', '1');

    expect($cnpj->raw())->toBe('09165609000195')
        ->and($cnpj->isValid())->toBeTrue();
});

it('generates an alphanumeric CNPJ from base and store numbers', function () {
    $cnpj = Cnpj::generateFromBaseStore('12ABC345', '0001');

    expect($cnpj->isValid())->toBeTrue()
        ->and(substr($cnpj->raw(), 0, 8))->toBe('12ABC345')
        ->and(substr($cnpj->raw(), 8, 4))->toBe('0001');
});

it('splits a numeric CNPJ into base and store', function () {
    $cnpj = new Cnpj('09165609000195');

    expect($cnpj->splitToBaseStore())->toBe(['09165609', '0001']);
});

it('splits an alphanumeric CNPJ into base and store', function () {
    $cnpj = Cnpj::generateFromBaseStore('12ABC345', '0001');
    [$base, $store] = $cnpj->splitToBaseStore();

    expect($base)->toBe('12ABC345')
        ->and($store)->toBe('0001');
});

it('round-trips generateFromBaseStore and splitToBaseStore', function () {
    $original = Cnpj::generateFromBaseStore('09165609', '0001');
    [$base, $store] = $original->splitToBaseStore();
    $rebuilt = Cnpj::generateFromBaseStore($base, $store);

    expect($rebuilt->raw())->toBe($original->raw());
});
