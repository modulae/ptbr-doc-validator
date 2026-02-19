<?php

use Illuminate\Database\Eloquent\Model;
use Modulae\PTBRDocValidator\ValueObjects\Cnpj;

class TestModel extends Model
{
    protected $casts = [
        'cnpj' => Cnpj::class,
    ];
}

it('can cast to Cnpj value object', function () {
    $model = new TestModel;
    $model->cnpj = 'T6.JSP.XPS/0001-11';

    expect($model->cnpj)->toBeInstanceOf(Cnpj::class)
        ->and($model->cnpj->raw())->toBe('T6JSPXPS000111');
});

it('can cast null/empty to null', function () {
    $model = new TestModel;
    $model->cnpj = null;
    expect($model->getAttributes()['cnpj'] ?? null)->toBeNull();

    $model->cnpj = '';
    expect($model->getAttributes()['cnpj'] ?? null)->toBeNull();
});

it('sets normalized value in attributes', function () {
    $model = new TestModel;
    $model->cnpj = 'T6.JSP.XPS/0001-11';

    expect($model->getAttributes()['cnpj'])->toBe('T6JSPXPS000111');
});
