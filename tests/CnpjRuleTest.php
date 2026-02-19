<?php

it('validator works', function (string $value, bool $expected) {
    expect(\Modulae\PTBRDocValidator\Rules\CnpjRule::isValid($value))->toBe($expected);
})->with([
    'Alphanumeric CNPJ with dots and dashes 1' => ['T6.JSP.XPS/0001-11', true],
    'Alphanumeric CNPJ with dots and dashes 2' => ['T6.JSP.XPS/J84K-69', true],
    'Alphanumeric CNPJ with dots and dashes 3' => ['D2.M97.AA0/0001-63', true],
    'Alphanumeric CNPJ with dots and dashes 4' => ['W9.7VY.JMY/0001-81', true],
    'Alphanumeric CNPJ clean 1' => ['E5SGVHX9000190', true],
    'Alphanumeric CNPJ clean 2' => ['E5SGVHX960LR53', true],
    'Alphanumeric CNPJ clean 3' => ['12ABC34501DE35', true],
    'Numeric CNPJ with dots and dashes 1' => ['32.332.643/0001-29', true],
    'Numeric CNPJ with dots and dashes 2' => ['32.332.643/3060-40', true],
    'Numeric CNPJ clean 1' => ['38725833000192', true],
    'Numeric CNPJ clean 2' => ['38725833223060', true],
    'Invalid CNPJ' => ['12.345.678/0001-00', false],
    'Zeroed CNPJ' => ['00.000.000/0000-00', false],
    'CNPJ with invalid characters' => ['32.332.643/0001-9!', false],
]);

it('can use the validator through the validator facade', function (string $value, bool $expected) {
    $validator = \Illuminate\Support\Facades\Validator::make(
        ['cnpj' => $value],
        ['cnpj' => 'cnpj']
    );

    expect($validator->passes())->toBe($expected);
})->with([
    'Alphanumeric CNPJ' => ['T6.JSP.XPS/0001-11', true],
    'Invalid CNPJ' => ['12.345.678/0001-00', false],
]);

it('can use the Cnpj value object', function () {
    $cnpj = new \Modulae\PTBRDocValidator\ValueObjects\Cnpj('T6.JSP.XPS/0001-11');

    expect($cnpj->isValid())->toBeTrue()
        ->and($cnpj->formatted())->toBe('T6.JSP.XPS/0001-11')
        ->and($cnpj->raw())->toBe('T6JSPXPS000111')
        ->and((string) $cnpj)->toBe('T6JSPXPS000111');
});

it('can create a Cnpj value object from another Cnpj object', function () {
    $cnpj1 = new \Modulae\PTBRDocValidator\ValueObjects\Cnpj('T6.JSP.XPS/0001-11');
    $cnpj2 = new \Modulae\PTBRDocValidator\ValueObjects\Cnpj($cnpj1);

    expect($cnpj2->raw())->toBe($cnpj1->raw());
});

it('can create a Cnpj value object using the static new method', function () {
    $cnpj = \Modulae\PTBRDocValidator\ValueObjects\Cnpj::new('T6.JSP.XPS/0001-11');

    expect($cnpj)->toBeInstanceOf(\Modulae\PTBRDocValidator\ValueObjects\Cnpj::class)
        ->and($cnpj->raw())->toBe('T6JSPXPS000111');
});

it('can normalize a cnpj value using the static method', function () {
    expect(\Modulae\PTBRDocValidator\ValueObjects\Cnpj::normalize('T6.JSP.XPS/0001-11'))->toBe('T6JSPXPS000111');
});

it('can validate a cnpj through the CnpjRule::validate method', function () {
    $rule = new \Modulae\PTBRDocValidator\Rules\CnpjRule;
    $failCalled = false;
    $fail = function ($message) use (&$failCalled) {
        $failCalled = true;
        expect($message)->toBe('ptbr-doc-validator::validation.cnpj');
    };

    $rule->validate('cnpj', 'invalid-cnpj', $fail);
    expect($failCalled)->toBeTrue();

    $failCalled = false;
    $rule->validate('cnpj', 'T6.JSP.XPS/0001-11', $fail);
    expect($failCalled)->toBeFalse();
});

it('translates the error message correctly', function () {
    $rule = new \Modulae\PTBRDocValidator\Rules\CnpjRule;
    $failCalled = false;
    $message = '';
    $fail = function ($msg) use (&$failCalled, &$message) {
        $failCalled = true;
        $message = __($msg, ['attribute' => 'cnpj']);
    };

    app()->setLocale('en');
    $rule->validate('cnpj', 'invalid-cnpj', $fail);
    expect($message)->toBe('The cnpj is not a valid CNPJ.');

    $failCalled = false;
    app()->setLocale('pt_BR');
    $rule->validate('cnpj', 'invalid-cnpj', $fail);
    expect($message)->toBe('O campo cnpj nao e um CNPJ valido.');
});
